<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStoreRequest;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Admin controller for managing stores in the shopping center.
 * Handles CRUD operations for stores (create, read, update, delete).
 */
class StoreController extends Controller
{
    /**
     * Display a listing of stores.
     * Supports filtering by owner, rubro, and search query.
     */
    public function index(Request $request)
    {
        $query = Store::with('owner');

        // Filter by owner
        if ($request->filled('owner_id')) {
            $query->where('owner_id', $request->owner_id);
        }

        // Filter by rubro
        if ($request->filled('rubro')) {
            $query->where('rubro', $request->rubro);
        }

        // Search by name or location
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('ubicacion', 'like', "%{$search}%");
            });
        }

        // Order by store code
    $query->orderBy('codigo');

        $stores = $query->paginate(15);

        // Get approved store owners for filter dropdown
        $owners = User::where('tipo_usuario', 'dueño de local')
            ->whereNotNull('approved_at')
            ->orderBy('name')
            ->get();

        // Get available rubros from config
        $rubros = config('shopping.store_rubros', []);

        return view('admin.stores.index', compact('stores', 'owners', 'rubros'));
    }

    /**
     * Show the form for creating a new store.
     */
    public function create()
    {
        // Get approved store owners (only approved users can own stores)
        $owners = User::where('tipo_usuario', 'dueño de local')
            ->whereNotNull('approved_at')
            ->orderBy('name')
            ->get();

        $rubros = config('shopping.store_rubros', []);

        return view('admin.stores.create', compact('owners', 'rubros'));
    }

    /**
     * Store a newly created store in storage.
     */
    public function store(StoreStoreRequest $request)
    {
        try {
            $store = Store::create($request->validated());

            Log::info('Store created by admin', [
                'store_id' => $store->id,
                'store_code' => $store->codigo,
                'store_name' => $store->nombre,
                'owner_id' => $store->owner_id,
                'admin_id' => auth()->id()
            ]);

            return redirect()
                ->route('admin.stores.show', $store)
                ->with('success', "Local '{$store->nombre}' creado exitosamente con código {$store->codigo}.");
        } catch (\Exception $e) {
            Log::error('Failed to create store', [
                'error' => $e->getMessage(),
                'admin_id' => auth()->id()
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al crear el local. Por favor intente nuevamente.');
        }
    }

    /**
     * Display the specified store.
     */
    public function show(Store $store)
    {
        $store->load(['owner', 'promotions' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }]);

        // Get promotion statistics
        $promotionStats = [
            'total' => $store->promotions()->count(),
            'pendiente' => $store->promotions()->where('estado', 'pendiente')->count(),
            'aprobada' => $store->promotions()->where('estado', 'aprobada')->count(),
            'denegada' => $store->promotions()->where('estado', 'denegada')->count(),
        ];

        return view('admin.stores.show', compact('store', 'promotionStats'));
    }

    /**
     * Show the form for editing the specified store.
     */
    public function edit(Store $store)
    {
        // Get approved store owners
        $owners = User::where('tipo_usuario', 'dueño de local')
            ->whereNotNull('approved_at')
            ->orderBy('name')
            ->get();

        $rubros = config('shopping.store_rubros', []);

        return view('admin.stores.edit', compact('store', 'owners', 'rubros'));
    }

    /**
     * Update the specified store in storage.
     */
    public function update(StoreStoreRequest $request, Store $store)
    {
        try {
            $oldData = $store->toArray();
            
            $store->update($request->validated());

            Log::info('Store updated by admin', [
                'store_id' => $store->id,
                'store_code' => $store->codigo,
                'changes' => array_diff_assoc($request->validated(), $oldData),
                'admin_id' => auth()->id()
            ]);

            return redirect()
                ->route('admin.stores.show', $store)
                ->with('success', "Local '{$store->nombre}' actualizado exitosamente.");
        } catch (\Exception $e) {
            Log::error('Failed to update store', [
                'store_id' => $store->id,
                'error' => $e->getMessage(),
                'admin_id' => auth()->id()
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al actualizar el local. Por favor intente nuevamente.');
        }
    }

    /**
     * Remove the specified store from storage.
     * Uses soft delete to preserve historical data.
     */
    public function destroy(Store $store)
    {
        try {
            $storeName = $store->nombre;
            $storeCode = $store->codigo;

            // Check if store has active promotions
            $activePromotions = $store->promotions()
                ->where('estado', 'aprobada')
                ->where('fecha_hasta', '>=', now())
                ->count();

            if ($activePromotions > 0) {
                return redirect()
                    ->back()
                    ->with('error', "No se puede eliminar el local. Tiene {$activePromotions} promoción(es) activa(s).");
            }

            $store->delete(); // Soft delete

            Log::info('Store deleted by admin', [
                'store_id' => $store->id,
                'store_code' => $storeCode,
                'store_name' => $storeName,
                'admin_id' => auth()->id()
            ]);

            return redirect()
                ->route('admin.stores.index')
                ->with('success', "Local '{$storeName}' (Código: {$storeCode}) eliminado exitosamente.");
        } catch (\Exception $e) {
            Log::error('Failed to delete store', [
                'store_id' => $store->id,
                'error' => $e->getMessage(),
                'admin_id' => auth()->id()
            ]);

            return redirect()
                ->back()
                ->with('error', 'Error al eliminar el local. Por favor intente nuevamente.');
        }
    }
}
