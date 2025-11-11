<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\User;
use App\Models\Promotion;
use App\Models\PromotionUsage;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Administrator dashboard controller.
 * Displays system-wide statistics and quick access to management functions.
 */
class DashboardController extends Controller
{
    /**
     * Display the administrator dashboard.
     */
    public function index(Request $request)
    {
        $activeSection = $request->query('section');

        // System statistics
        $stats = [
            'stores_active' => Store::count(),
            'clients_total' => User::where('tipo_usuario', 'cliente')->count(),
            'promotions_pending' => Promotion::where('estado', 'pendiente')->count(),
            'owners_pending' => User::where('tipo_usuario', 'dueño de local')
                ->whereNull('approved_at')
                ->count(),
        ];

        // Client category distribution
        $categoryDistribution = [
            'Inicial' => User::where('tipo_usuario', 'cliente')
                ->where('categoria_cliente', 'Inicial')
                ->count(),
            'Medium' => User::where('tipo_usuario', 'cliente')
                ->where('categoria_cliente', 'Medium')
                ->count(),
            'Premium' => User::where('tipo_usuario', 'cliente')
                ->where('categoria_cliente', 'Premium')
                ->count(),
        ];

        // Total usage statistics
        $usageStats = [
            'total' => PromotionUsage::where('estado', 'aceptada')->count(),
            'this_month' => PromotionUsage::where('estado', 'aceptada')
                ->whereMonth('fecha_uso', now()->month)
                ->whereYear('fecha_uso', now()->year)
                ->count(),
        ];

        // Top stores by usage
        $topStores = Store::query()
            ->select('stores.*', DB::raw('COUNT(promotion_usage.id) as usage_count'))
            ->leftJoin('promotions', 'stores.id', '=', 'promotions.store_id')
            ->leftJoin('promotion_usage', function ($join) {
                $join->on('promotions.id', '=', 'promotion_usage.promotion_id')
                    ->where('promotion_usage.estado', 'aceptada');
            })
            ->groupBy(
                'stores.id',
                'stores.codigo',
                'stores.nombre',
                'stores.ubicacion',
                'stores.rubro',
                'stores.logo',
                'stores.created_at',
                'stores.updated_at',
                'stores.deleted_at'
            )
            ->orderByDesc('usage_count')
            ->limit(5)
            ->get();

        // Stores overview for management table
        $stores = Store::with('owner')
            ->withTrashed()
            ->orderByDesc('created_at')
            ->limit(15)
            ->get();

        // Pending store owner accounts awaiting approval
        $pendingOwners = User::storeOwners()
            ->whereNull('approved_at')
            ->orderByDesc('created_at')
            ->limit(15)
            ->get();

        // Promotions waiting for admin approval
        $pendingPromotions = Promotion::with(['store.owner'])
            ->pending()
            ->orderByDesc('created_at')
            ->limit(15)
            ->get();

        // Latest news (vigent or recent)
        $latestNews = News::with('creator')
            ->orderByDesc('created_at')
            ->limit(15)
            ->get();

        // All promotions for reports
        $promotions = Promotion::all();

        return view('dashboard.admin.index', compact(
            'stats',
            'categoryDistribution',
            'usageStats',
            'topStores',
            'stores',
            'pendingOwners',
            'pendingPromotions',
            'latestNews',
            'promotions',
            'activeSection'
        ));
    }
}
