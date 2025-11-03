<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePromotionRequest;
use App\Models\Promotion;
use Illuminate\Http\Request;
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
        $store = auth()->user()->stores()->firstOrFail();

        $query = $store->promotions();

        // Filter by estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Filter by date range
        if ($request->filled('fecha_desde')) {
            $query->where('fecha_desde', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->where('fecha_hasta', '<=', $request->fecha_hasta);
        }

        // Search in text
        if ($request->filled('search')) {
            $query->where('texto_promocion', 'like', "%{$request->search}%");
        }

        // Order by creation date (newest first)
        $query->orderBy('created_at', 'desc');

        $promotions = $query->paginate(15);

        // Statistics
        $stats = [
            'total' => $store->promotions()->count(),
            'pendiente' => $store->promotions()->where('estado', 'pendiente')->count(),
            'aprobada' => $store->promotions()->where('estado', 'aprobada')->count(),
            'denegada' => $store->promotions()->where('estado', 'denegada')->count(),
            'active' => $store->promotions()
                ->where('estado', 'aprobada')
                ->where('fecha_desde', '<=', now())
                ->where('fecha_hasta', '>=', now())
                ->count(),
        ];

        return view('store.promotions.index', compact('promotions', 'store', 'stats'));
    }

    /**
     * Show the form for creating a new promotion.
     */
    public function create()
    {
        $store = auth()->user()->stores()->firstOrFail();
        
        $categories = ['Inicial', 'Medium', 'Premium'];
        $maxDuration = config('shopping.promotion.max_duration_days', 90);

        return view('store.promotions.create', compact('store', 'categories', 'maxDuration'));
    }

    /**
     * Store a newly created promotion in storage.
     * Creates promotion with estado='pendiente' pending admin approval.
     */
    public function store(StorePromotionRequest $request)
    {
        try {
            $store = auth()->user()->stores()->firstOrFail();

            $promotion = Promotion::create(array_merge(
                $request->validated(),
                [
                    'store_id' => $store->id,
                    'estado' => 'pendiente' // Requires admin approval
                ]
            ));

            Log::info('Promotion created by store owner', [
                'promotion_id' => $promotion->id,
                'promotion_code' => $promotion->codigo_promocion,
                'store_id' => $store->id,
                'owner_id' => auth()->id()
            ]);

            return redirect()
                ->route('store.promotions.show', $promotion)
                ->with('success', 'Promoción creada exitosamente. Está pendiente de aprobación por el administrador.');
        } catch (\Exception $e) {
            Log::error('Failed to create promotion', [
                'error' => $e->getMessage(),
                'owner_id' => auth()->id()
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
        $store = auth()->user()->stores()->firstOrFail();
        
        if ($promotion->store_id !== $store->id) {
            abort(403, 'No tiene permiso para ver esta promoción.');
        }

        $promotion->load('usages.client');

        // Get usage statistics
        $usageStats = [
            'total' => $promotion->usages()->count(),
            'enviada' => $promotion->usages()->where('estado', 'enviada')->count(),
            'aceptada' => $promotion->usages()->where('estado', 'aceptada')->count(),
            'rechazada' => $promotion->usages()->where('estado', 'rechazada')->count(),
        ];

        return view('store.promotions.show', compact('promotion', 'usageStats'));
    }

    /**
     * Remove the specified promotion from storage.
     * Uses soft delete to preserve historical data.
     * Note: Per business rules, promotions cannot be edited to prevent false advertising.
     */
    public function destroy(Promotion $promotion)
    {
        // Ensure owner can only delete their own promotions
        $store = auth()->user()->stores()->firstOrFail();
        
        if ($promotion->store_id !== $store->id) {
            abort(403, 'No tiene permiso para eliminar esta promoción.');
        }

        // Don't allow deletion of promotions with accepted usage requests
        $acceptedUsages = $promotion->usages()->where('estado', 'aceptada')->count();
        
        if ($acceptedUsages > 0) {
            return redirect()
                ->back()
                ->with('error', "No se puede eliminar la promoción. Tiene {$acceptedUsages} solicitud(es) aceptada(s).");
        }

        try {
            $promotionText = $promotion->texto_promocion;
            $promotion->delete(); // Soft delete

            Log::info('Promotion deleted by store owner', [
                'promotion_id' => $promotion->id,
                'promotion_code' => $promotion->codigo_promocion,
                'store_id' => $store->id,
                'owner_id' => auth()->id()
            ]);

            return redirect()
                ->route('store.promotions.index')
                ->with('success', "Promoción '{$promotionText}' eliminada exitosamente.");
        } catch (\Exception $e) {
            Log::error('Failed to delete promotion', [
                'promotion_id' => $promotion->id,
                'error' => $e->getMessage(),
                'owner_id' => auth()->id()
            ]);

            return redirect()
                ->back()
                ->with('error', 'Error al eliminar la promoción. Por favor intente nuevamente.');
        }
    }
}
