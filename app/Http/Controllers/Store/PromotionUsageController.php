<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\PromotionUsage;
use App\Services\PromotionUsageService;
use Illuminate\Http\Request;

/**
 * Store owner controller for managing promotion usage requests from clients.
 * Handles accepting or rejecting client discount requests.
 */
class PromotionUsageController extends Controller
{
    public function __construct(
        private PromotionUsageService $promotionUsageService
    ) {
    }

    /**
     * Display a listing of pending usage requests for the store.
     */
    public function index(Request $request)
    {
        $store = auth()->user()->stores()->firstOrFail();

        $usageRequests = $this->promotionUsageService
            ->getPendingRequestsForStore($store->id);

        return view('store.usage-requests.index', compact('usageRequests', 'store'));
    }

    /**
     * Accept a usage request.
     */
    public function accept(PromotionUsage $usage)
    {
        $store = auth()->user()->stores()->firstOrFail();

        // Verify the usage request belongs to this store
        if ($usage->promotion->store_id !== $store->id) {
            abort(403, 'No tiene permiso para procesar esta solicitud.');
        }

        $success = $this->promotionUsageService->acceptUsageRequest($usage);

        if ($success) {
            return redirect()
                ->route('store.usage-requests.index')
                ->with('success', 'Solicitud aceptada. Se notificó al cliente.');
        }

        return redirect()
            ->back()
            ->with('error', 'Error al aceptar la solicitud.');
    }

    /**
     * Reject a usage request with optional reason.
     */
    public function reject(Request $request, PromotionUsage $usage)
    {
        $store = auth()->user()->stores()->firstOrFail();

        // Verify the usage request belongs to this store
        if ($usage->promotion->store_id !== $store->id) {
            abort(403, 'No tiene permiso para procesar esta solicitud.');
        }

        $request->validate([
            'reason' => 'nullable|string|max:500'
        ]);

        $reason = $request->input('reason');

        $success = $this->promotionUsageService->rejectUsageRequest($usage, $reason);

        if ($success) {
            return redirect()
                ->route('store.usage-requests.index')
                ->with('success', 'Solicitud rechazada. Se notificó al cliente.');
        }

        return redirect()
            ->back()
            ->with('error', 'Error al rechazar la solicitud.');
    }

    /**
     * Display usage history for store promotions.
     */
    public function history(Request $request)
    {
        $store = auth()->user()->stores()->firstOrFail();

        $query = PromotionUsage::whereHas('promotion', function ($q) use ($store) {
            $q->where('store_id', $store->id);
        })->with(['promotion', 'client']);

        // Filter by estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Filter by date range
        if ($request->filled('fecha_desde')) {
            $query->where('fecha_uso', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->where('fecha_uso', '<=', $request->fecha_hasta);
        }

        $query->orderBy('fecha_uso', 'desc');

        $usageHistory = $query->paginate(20);

        return view('store.usage-requests.history', compact('usageHistory', 'store'));
    }
}
