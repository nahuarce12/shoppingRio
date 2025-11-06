<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\PromotionUsage;
use App\Services\PromotionUsageService;
use App\Services\ReportService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

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
        $user = Auth::user();
        $store = $user?->stores()->with(['promotions.usages'])->first();

        if (!$store) {
            return view('dashboard.store.no-store');
        }

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

        $usageSummary = [
            'total' => $usageStats['total_requests'] ?? 0,
            'pending' => $usageStats['pending'] ?? 0,
            'accepted' => $usageStats['accepted'] ?? 0,
            'rejected' => $usageStats['rejected'] ?? 0,
            'unique_clients' => $usageStats['unique_clients'] ?? 0,
        ];

        // Recent pending requests
        $pendingRequests = $this->promotionUsageService
            ->getPendingRequestsForStore($store->id)
            ->take(5);

        $recentUsageHistory = PromotionUsage::query()
            ->with(['promotion' => function ($query) {
                $query->withTrashed();
            }, 'client'])
            ->whereHas('promotion', function ($query) use ($store) {
                $query->where('store_id', $store->id);
            })
            ->orderByDesc('fecha_uso')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        // Recent promotions
        $recentPromotions = $store->promotions()
            ->withCount(['usages as accepted_usages_count' => function ($query) {
                $query->where('estado', 'aceptada');
            }])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $reportWindow = Carbon::now()->subMonths(3)->startOfDay();
        $ownerReport = collect($this->reportService->getStoreOwnerReport($user, [
            'date_from' => $reportWindow->toDateString(),
        ]))->firstWhere('store.id', $store->id);

        $section = request()->query('section');

        return view('dashboard.store.index', compact(
            'store',
            'promotionStats',
            'usageSummary',
            'pendingRequests',
            'recentPromotions',
            'ownerReport',
            'section',
            'recentUsageHistory'
        ));
    }
}
