<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Services\PromotionUsageService;
use App\Services\ReportService;

/**
 * Store owner dashboard controller.
 * Displays statistics and quick access to store management functions.
 */
class DashboardController extends Controller
{
    public function __construct(
        private PromotionUsageService $promotionUsageService,
        private ReportService $reportService
    ) {
    }

    /**
     * Display the store owner dashboard.
     */
    public function index()
    {
        $store = auth()->user()->stores()->firstOrFail();

        // Promotion statistics
        $promotionStats = [
            'total' => $store->promotions()->count(),
            'pendiente' => $store->promotions()->where('estado', 'pendiente')->count(),
            'aprobada' => $store->promotions()->where('estado', 'aprobada')->count(),
            'active' => $store->promotions()
                ->where('estado', 'aprobada')
                ->where('fecha_desde', '<=', now())
                ->where('fecha_hasta', '>=', now())
                ->count(),
        ];

        // Usage request statistics
        $usageStats = $this->promotionUsageService->getStoreUsageStats($store->id);

        // Recent pending requests
        $pendingRequests = $this->promotionUsageService
            ->getPendingRequestsForStore($store->id)
            ->take(5);

        // Recent promotions
        $recentPromotions = $store->promotions()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('store.dashboard', compact(
            'store',
            'promotionStats',
            'usageStats',
            'pendingRequests',
            'recentPromotions'
        ));
    }
}
