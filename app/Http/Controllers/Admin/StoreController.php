<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStoreRequest;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
        $query = Store::with('owners');

        // Filter by rubro
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Search by name or location
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        // Order by store code
        $query->orderBy('code');

        $stores = $query->paginate(15);

        // Get available rubros from config
        $rubros = config('shopping.store_rubros', []);

        return redirect()->route('admin.dashboard', ['section' => 'locales']);
    }

    /**
     * Show the form for creating a new store.
     */
    public function create()
    {
        // Get approved store owners (only approved users can own stores)
        $owners = User::where('user_type', 'dueño de local')
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
            $data = $request->validated();
            
            // Handle logo upload
            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('stores/logos', 'public');
                $data['logo'] = $logoPath;
            }
            
            $store = Store::create($data);

            Log::info('Store created by admin', [
                'store_id' => $store->id,
                'store_code' => $store->code,
                'store_name' => $store->name,
                'owners_count' => $store->owners()->count(),
                'admin_id' => auth()->id()
            ]);

            return redirect()
                ->route('admin.dashboard', ['section' => 'locales'])
                ->with('success', "Local '{$store->name}' creado exitosamente con código {$store->code}.");
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
        $store->load(['owners', 'promotions' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }]);

        // Get promotion statistics
        $promotionStats = [
            'total' => $store->promotions()->count(),
            'pendiente' => $store->promotions()->where('status', 'pendiente')->count(),
            'aprobada' => $store->promotions()->where('status', 'aprobada')->count(),
            'denegada' => $store->promotions()->where('status', 'denegada')->count(),
        ];

        return view('admin.stores.show', compact('store', 'promotionStats'));
    }

    /**
     * Show the form for editing the specified store.
     */
    public function edit(Store $store)
    {
        // Get approved store owners
        $owners = User::where('user_type', 'dueño de local')
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
            $data = $request->validated();
            
            // Handle logo upload
            if ($request->hasFile('logo')) {
                // Delete old logo if exists
                if ($store->logo) {
                    Storage::disk('public')->delete($store->logo);
                }
                $logoPath = $request->file('logo')->store('stores/logos', 'public');
                $data['logo'] = $logoPath;
            }
            
            $store->update($data);

            Log::info('Store updated by admin', [
                'store_id' => $store->id,
                'store_code' => $store->code,
                'changes' => array_diff_assoc($data, $oldData),
                'admin_id' => auth()->id()
            ]);

            return redirect()
                ->route('admin.dashboard', ['section' => 'locales'])
                ->with('success', "Local '{$store->name}' actualizado exitosamente.");
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
            $storeName = $store->name;
            $storeCode = $store->code;

            // Check if store has active promotions
            $activePromotions = $store->promotions()
                ->where('status', 'aprobada')
                ->where('end_date', '>=', now())
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
                ->route('admin.dashboard', ['section' => 'locales'])
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
