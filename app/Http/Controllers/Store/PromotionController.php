<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePromotionRequest;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Store owner controller for managing their own promotions.
 * Handles creation and deletion of promotions (no editing per business rules).
 */
class PromotionController extends Controller
{
    /**
     * Display a listing of the store's promotions.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $store = $user?->store;

        if (!$store) {
            return view('dashboard.store.no-store');
        }

        $query = $store->promotions();

        // Filter by estado
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->where('start_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('end_date', '<=', $request->end_date);
        }

        // Search in text
        if ($request->filled('search')) {
            $query->where('description', 'like', "%{$request->search}%");
        }

        $query->withCount(['usages as accepted_usages_count' => function ($usageQuery) {
            $usageQuery->where('status', 'aceptada');
        }])->orderBy('created_at', 'desc');

        $promotions = $query->paginate(15);

        // Statistics
        $stats = [
            'total' => $store->promotions()->count(),
            'pendiente' => $store->promotions()->where('status', 'pendiente')->count(),
            'aprobada' => $store->promotions()->where('status', 'aprobada')->count(),
            'denegada' => $store->promotions()->where('status', 'denegada')->count(),
            'active' => $store->promotions()
                ->where('status', 'aprobada')
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->count(),
        ];

    return view('dashboard.store.promotions-index', compact('promotions', 'store', 'stats'));
    }

    /**
     * Show the form for creating a new promotion.
     */
    public function create()
    {
        $user = Auth::user();
        $store = $user?->store;

        if (!$store) {
            return view('dashboard.store.no-store');
        }
        
        $categories = ['Inicial', 'Medium', 'Premium'];
        $maxDuration = config('shopping.promotion.max_duration_days', 90);

    return view('dashboard.store.promotions-create', compact('store', 'categories', 'maxDuration'));
    }

    /**
     * Store a newly created promotion in storage.
     * Creates promotion with estado='pendiente' pending admin approval.
     */
    public function store(StorePromotionRequest $request)
    {
        try {
            $user = Auth::user();
            $store = $user?->store;

            if (!$store) {
                return view('dashboard.store.no-store');
            }

            $data = array_merge(
                $request->validated(),
                [
                    'store_id' => $store->id,
                    'status' => 'pendiente' // Requires admin approval
                ]
            );

            // Handle image upload
            if ($request->hasFile('imagen')) {
                $imagePath = $request->file('imagen')->store('promotions/images', 'public');
                $data['imagen'] = $imagePath;
            }

            $promotion = Promotion::create($data);

            Log::info('Promotion created by store owner', [
                'promotion_id' => $promotion->id,
                'promotion_code' => $promotion->code,
                'store_id' => $store->id,
                'owner_id' => Auth::id()
            ]);

            return redirect()
                ->route('store.dashboard', ['section' => 'mis-promociones'])
                ->with('success', 'Promoción creada exitosamente. Está pendiente de aprobación por el administrador.');
        } catch (\Exception $e) {
            Log::error('Failed to create promotion', [
                'error' => $e->getMessage(),
                'owner_id' => Auth::id()
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al crear la promoción. Por favor intente nuevamente.');
        }
    }

    /**
     * Display the specified promotion.
     */
    public function show(Promotion $promotion)
    {
        // Ensure owner can only see their own promotions
        $user = Auth::user();
        $store = $user?->store;

        if (!$store) {
            return view('dashboard.store.no-store');
        }
        
        if ($promotion->store_id !== $store->id) {
            abort(403, 'No tiene permiso para ver esta promoción.');
        }

        $promotion->load('usages.client');

        $usageCollection = $promotion->usages;

        // Get usage statistics
        $usageStats = [
            'total' => $usageCollection->count(),
            'pending' => $usageCollection->where('status', 'enviada')->count(),
            'accepted' => $usageCollection->where('status', 'aceptada')->count(),
            'rejected' => $usageCollection->where('status', 'rechazada')->count(),
        ];

        return view('dashboard.store.promotion-show', compact('promotion', 'usageStats', 'store'));
    }

    /**
     * Remove the specified promotion from storage.
     * Uses soft delete to preserve historical data.
     * Note: Per business rules, promotions cannot be edited to prevent false advertising.
     */
    public function destroy(Promotion $promotion)
    {
        // Ensure owner can only delete their own promotions
        $user = Auth::user();
        $store = $user?->store;

        if (!$store) {
            return view('dashboard.store.no-store');
        }
        
        if ($promotion->store_id !== $store->id) {
            abort(403, 'No tiene permiso para eliminar esta promoción.');
        }

        // Don't allow deletion of promotions with accepted usage requests
        $acceptedUsages = $promotion->usages()->where('status', 'aceptada')->count();
        
        if ($acceptedUsages > 0) {
            return redirect()
                ->back()
                ->with('error', "No se puede eliminar la promoción. Tiene {$acceptedUsages} solicitud(es) aceptada(s).");
        }

        try {
            $promotionText = $promotion->description;
            $promotion->delete(); // Soft delete

            Log::info('Promotion deleted by store owner', [
                'promotion_id' => $promotion->id,
                'promotion_code' => $promotion->code,
                'store_id' => $store->id,
                'owner_id' => Auth::id()
            ]);

            return redirect()
                ->route('store.dashboard', ['section' => 'mis-promociones'])
                ->with('success', "Promoción '{$promotionText}' eliminada exitosamente.");
        } catch (\Exception $e) {
            Log::error('Failed to delete promotion', [
                'promotion_id' => $promotion->id,
                'error' => $e->getMessage(),
                'owner_id' => Auth::id()
            ]);

            return redirect()
                ->back()
                ->with('error', 'Error al eliminar la promoción. Por favor intente nuevamente.');
        }
    }
}
