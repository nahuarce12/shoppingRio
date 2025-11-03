<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePromotionStatusRequest;
use App\Models\Promotion;
use App\Services\PromotionService;
use Illuminate\Http\Request;

/**
 * Admin controller for approving or denying store promotions.
 * Implements the promotion approval workflow required by business rules.
 */
class PromotionApprovalController extends Controller
{
    public function __construct(
        private PromotionService $promotionService
    ) {
    }

    /**
     * Display a listing of promotions pending approval.
     * Supports filtering by store, date range, and search query.
     */
    public function index(Request $request)
    {
        $query = Promotion::with(['store', 'store.owner'])
            ->where('estado', 'pendiente');

        // Filter by store
        if ($request->filled('store_id')) {
            $query->where('store_id', $request->store_id);
        }

        // Filter by date range
        if ($request->filled('fecha_desde')) {
            $query->where('fecha_desde', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->where('fecha_hasta', '<=', $request->fecha_hasta);
        }

        // Search in promotion text or store name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('texto_promocion', 'like', "%{$search}%")
                  ->orWhereHas('store', function ($sq) use ($search) {
                      $sq->where('nombre', 'like', "%{$search}%");
                  });
            });
        }

        // Order by creation date (oldest first for FIFO processing)
        $query->orderBy('created_at');

        $promotions = $query->paginate(20);

        // Get stores for filter dropdown
        $stores = \App\Models\Store::orderBy('nombre')->get();

        // Statistics
        $stats = [
            'pending' => Promotion::where('estado', 'pendiente')->count(),
            'approved_today' => Promotion::where('estado', 'aprobada')
                ->whereDate('updated_at', today())
                ->count(),
            'denied_today' => Promotion::where('estado', 'denegada')
                ->whereDate('updated_at', today())
                ->count(),
        ];

        return view('admin.promotions.approval.index', compact('promotions', 'stores', 'stats'));
    }

    /**
     * Display the specified promotion for review.
     */
    public function show(Promotion $promotion)
    {
        // Only show pending promotions
        if ($promotion->estado !== 'pendiente') {
            return redirect()
                ->route('admin.promotions.approval.index')
                ->with('info', 'Esta promoción ya fue procesada.');
        }

        $promotion->load(['store', 'store.owner']);

        // Get other promotions from same store for context
        $storePromotions = Promotion::where('store_id', $promotion->store_id)
            ->where('id', '!=', $promotion->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.promotions.approval.show', compact('promotion', 'storePromotions'));
    }

    /**
     * Approve a promotion.
     */
    public function approve(UpdatePromotionStatusRequest $request, Promotion $promotion)
    {
        if ($promotion->estado !== 'pendiente') {
            return redirect()
                ->route('admin.promotions.approval.index')
                ->with('error', 'Esta promoción ya fue procesada anteriormente.');
        }

        $success = $this->promotionService->approvePromotion($promotion);

        if ($success) {
            return redirect()
                ->route('admin.promotions.approval.index')
                ->with('success', "Promoción '{$promotion->texto_promocion}' aprobada exitosamente. Se envió notificación al local.");
        }

        return redirect()
            ->back()
            ->with('error', 'Error al aprobar la promoción. Por favor intente nuevamente.');
    }

    /**
     * Deny a promotion with optional reason.
     */
    public function deny(UpdatePromotionStatusRequest $request, Promotion $promotion)
    {
        if ($promotion->estado !== 'pendiente') {
            return redirect()
                ->route('admin.promotions.approval.index')
                ->with('error', 'Esta promoción ya fue procesada anteriormente.');
        }

        $reason = $request->validated()['reason'] ?? null;

        $success = $this->promotionService->denyPromotion($promotion, $reason);

        if ($success) {
            return redirect()
                ->route('admin.promotions.approval.index')
                ->with('success', "Promoción denegada. Se envió notificación al local con el motivo.");
        }

        return redirect()
            ->back()
            ->with('error', 'Error al denegar la promoción. Por favor intente nuevamente.');
    }

    /**
     * Display all promotions (approved, denied, pending) for admin review.
     */
    public function all(Request $request)
    {
        $query = Promotion::with(['store', 'store.owner']);

        // Filter by estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Filter by store
        if ($request->filled('store_id')) {
            $query->where('store_id', $request->store_id);
        }

        // Filter by date range
        if ($request->filled('fecha_desde')) {
            $query->where('fecha_desde', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->where('fecha_hasta', '<=', $request->fecha_hasta);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('texto_promocion', 'like', "%{$search}%")
                  ->orWhereHas('store', function ($sq) use ($search) {
                      $sq->where('nombre', 'like', "%{$search}%");
                  });
            });
        }

        $query->orderBy('created_at', 'desc');

        $promotions = $query->paginate(20);

        $stores = \App\Models\Store::orderBy('nombre')->get();

        return view('admin.promotions.all', compact('promotions', 'stores'));
    }
}
