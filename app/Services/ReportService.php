<?php

namespace App\Services;

use App\Models\Promotion;
use App\Models\PromotionUsage;
use App\Models\Store;
use App\Models\User;
use App\Models\News;
use Carbon\Carbon;

/**
 * Service class for generating reports and statistics.
 * Provides admin and store owner reporting functionality.
 */
class ReportService
{
    /**
     * Provide summary metrics for admin reports overview.
     */
    public function getSystemSummary(): array
    {
        return $this->getAdminDashboardStats();
    }

    public function getPromotionUsageReport(?string $startDate = null, ?string $endDate = null, ?int $storeId = null, ?string $estado = null): array
    {
        $usageQuery = PromotionUsage::with(['promotion.store']);

        if ($startDate) {
            $start = Carbon::parse($startDate)->startOfDay()->toDateString();
            $usageQuery->whereDate('fecha_uso', '>=', $start);
        }

        if ($endDate) {
            $end = Carbon::parse($endDate)->endOfDay()->toDateString();
            $usageQuery->whereDate('fecha_uso', '<=', $end);
        }

        if ($storeId) {
            $usageQuery->whereHas('promotion', function ($query) use ($storeId) {
                $query->where('store_id', $storeId);
            });
        }

        if ($estado) {
            $usageQuery->where('estado', $estado);
        }

        $usages = $usageQuery->get();

        $totalRequests = $usages->count();
        $accepted = $usages->where('estado', 'aceptada')->count();
        $rejected = $usages->where('estado', 'rechazada')->count();
        $pending = $usages->where('estado', 'enviada')->count();

        $byStore = $usages
            ->filter(fn ($usage) => $usage->promotion && $usage->promotion->store)
            ->groupBy(fn ($usage) => $usage->promotion->store->id)
            ->map(function ($group) {
                $store = $group->first()->promotion->store;
                $total = $group->count();
                $accepted = $group->where('estado', 'aceptada')->count();
                $rejected = $group->where('estado', 'rechazada')->count();
                $pending = $group->where('estado', 'enviada')->count();

                return (object) [
                    'store_id' => $store->id,
                    'store_codigo' => $store->codigo,
                    'store_name' => $store->nombre,
                    'store_rubro' => $store->rubro,
                    'total_requests' => $total,
                    'accepted_count' => $accepted,
                    'rejected_count' => $rejected,
                    'pending_count' => $pending,
                    'acceptance_rate' => $total > 0 ? round(($accepted / $total) * 100, 2) : 0,
                    'unique_clients' => $group->pluck('client_id')->filter()->unique()->count(),
                ];
            })
            ->values();

        $byPromotion = $usages
            ->filter(fn ($usage) => $usage->promotion && $usage->promotion->store)
            ->groupBy('promotion_id')
            ->map(function ($group) {
                $promotion = $group->first()->promotion;
                $store = $promotion->store;
                $total = $group->count();
                $accepted = $group->where('estado', 'aceptada')->count();
                $rejected = $group->where('estado', 'rechazada')->count();
                $pending = $group->where('estado', 'enviada')->count();

                return (object) [
                    'promotion_id' => $promotion->id,
                    'codigo_promocion' => $promotion->codigo,
                    'texto_promocion' => $promotion->texto,
                    'categoria_minima' => $promotion->categoria_minima,
                    'store' => $store,
                    'total_requests' => $total,
                    'accepted_count' => $accepted,
                    'rejected_count' => $rejected,
                    'pending_count' => $pending,
                    'acceptance_rate' => $total > 0 ? round(($accepted / $total) * 100, 2) : 0,
                ];
            })
            ->values();

        return [
            'summary' => [
                'total_requests' => $totalRequests,
                'accepted' => $accepted,
                'rejected' => $rejected,
                'pending' => $pending,
                'acceptance_rate' => $totalRequests > 0 ? round(($accepted / $totalRequests) * 100, 2) : 0,
            ],
            'by_store' => $byStore->all(),
            'by_promotion' => $byPromotion->all(),
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'store_id' => $storeId,
                'estado' => $estado,
            ],
        ];
    }

    public function getPromotionUsageByStore(array $filters = []): array
    {
        $report = $this->getPromotionUsageReport(
            $filters['date_from'] ?? null,
            $filters['date_to'] ?? null,
            $filters['store_id'] ?? null,
            $filters['estado'] ?? null
        );

        return $report['by_store'];
    }

    public function getStorePerformanceReport(int $periodMonths = 3): array
    {
        $startDate = Carbon::now()->subMonths($periodMonths)->startOfMonth();

        $stores = Store::with(['promotions' => function ($query) use ($startDate) {
            $query->with(['usages' => function ($usageQuery) use ($startDate) {
                $usageQuery->whereDate('fecha_uso', '>=', $startDate);
            }]);
        }])->get();

        return $stores->map(function ($store) use ($startDate) {
            $promotions = $store->promotions;
            $usages = $promotions->flatMap(fn ($promotion) => $promotion->usages);

            $totalPromotions = $promotions->count();
            $approvedPromotions = $promotions->where('estado', 'aprobada')->count();
            $pendingPromotions = $promotions->where('estado', 'pendiente')->count();
            $deniedPromotions = $promotions->where('estado', 'denegada')->count();

            $totalRequests = $usages->count();
            $accepted = $usages->where('estado', 'aceptada')->count();
            $rejected = $usages->where('estado', 'rechazada')->count();
            $pending = $usages->where('estado', 'enviada')->count();

            $activePromotions = $promotions->filter(fn ($promotion) => $promotion->isActive())->count();

            return (object) [
                'store_id' => $store->id,
                'store_codigo' => $store->codigo,
                'store_name' => $store->nombre,
                'store_rubro' => $store->rubro,
                'total_promotions' => $totalPromotions,
                'approved_promotions' => $approvedPromotions,
                'pending_promotions' => $pendingPromotions,
                'denied_promotions' => $deniedPromotions,
                'active_promotions' => $activePromotions,
                'total_requests' => $totalRequests,
                'accepted_requests' => $accepted,
                'rejected_requests' => $rejected,
                'pending_requests' => $pending,
                'acceptance_rate' => $totalRequests > 0 ? round(($accepted / $totalRequests) * 100, 2) : 0,
                'start_date' => $startDate->toDateString(),
            ];
        })->all();
    }

    public function getMostPopularPromotions(int $limit = 10, int $periodMonths = 1): array
    {
        $startDate = Carbon::now()->subMonths($periodMonths)->startOfMonth();

        $usages = PromotionUsage::with(['promotion.store'])
            ->where('estado', 'aceptada')
            ->whereDate('fecha_uso', '>=', $startDate)
            ->get();

        $popular = $usages
            ->filter(fn ($usage) => $usage->promotion && $usage->promotion->store)
            ->groupBy('promotion_id')
            ->map(function ($group) {
                $promotion = $group->first()->promotion;
                $store = $promotion->store;
                $accepted = $group->where('estado', 'aceptada')->count();
                $total = $group->count();

                return (object) [
                    'promotion_id' => $promotion->id,
                    'codigo_promocion' => $promotion->codigo,
                    'texto_promocion' => $promotion->texto,
                    'store' => $store,
                    'accepted_count' => $accepted,
                    'total_requests' => $total,
                    'acceptance_rate' => $total > 0 ? round(($accepted / $total) * 100, 2) : 0,
                ];
            })
            ->sortByDesc('accepted_count')
            ->take($limit)
            ->values();

    return $popular->all();
    }

    public function getClientActivityReport(int $periodMonths = 3): array
    {
        $startDate = Carbon::now()->subMonths($periodMonths)->startOfMonth();

        $usages = PromotionUsage::with('client')
            ->whereDate('fecha_uso', '>=', $startDate)
            ->get();

        $totalRequests = $usages->count();
        $accepted = $usages->where('estado', 'aceptada')->count();
        $rejected = $usages->where('estado', 'rechazada')->count();
        $pending = $usages->where('estado', 'enviada')->count();

        $topClients = $usages
            ->filter(fn ($usage) => $usage->client)
            ->groupBy('client_id')
            ->map(function ($group) {
                $client = $group->first()->client;
                $total = $group->count();
                $accepted = $group->where('estado', 'aceptada')->count();
                $rejected = $group->where('estado', 'rechazada')->count();
                $pending = $group->where('estado', 'enviada')->count();

                return (object) [
                    'client_id' => $client->id,
                    'client_name' => $client->name,
                    'client_email' => $client->email,
                    'categoria_cliente' => $client->categoria_cliente,
                    'total_requests' => $total,
                    'accepted_count' => $accepted,
                    'rejected_count' => $rejected,
                    'pending_count' => $pending,
                    'acceptance_rate' => $total > 0 ? round(($accepted / $total) * 100, 2) : 0,
                ];
            })
            ->sortByDesc('accepted_count')
            ->values();

        $categoryBreakdown = $usages
            ->filter(fn ($usage) => $usage->client)
            ->groupBy(fn ($usage) => $usage->client->categoria_cliente)
            ->map(fn ($group) => $group->count())
            ->all();

        $monthlyTrend = [];
        for ($i = $periodMonths - 1; $i >= 0; $i--) {
            $monthStart = Carbon::now()->subMonths($i)->startOfMonth();
            $monthEnd = Carbon::now()->subMonths($i)->endOfMonth();

            $monthlyUsages = $usages->filter(function ($usage) use ($monthStart, $monthEnd) {
                return $usage->fecha_uso >= $monthStart && $usage->fecha_uso <= $monthEnd;
            });

            $monthlyTrend[] = [
                'month' => $monthStart->format('Y-m'),
                'total_requests' => $monthlyUsages->count(),
                'accepted' => $monthlyUsages->where('estado', 'aceptada')->count(),
                'rejected' => $monthlyUsages->where('estado', 'rechazada')->count(),
                'pending' => $monthlyUsages->where('estado', 'enviada')->count(),
            ];
        }

        return [
            'summary' => [
                'total_requests' => $totalRequests,
                'accepted' => $accepted,
                'rejected' => $rejected,
                'pending' => $pending,
                'acceptance_rate' => $totalRequests > 0 ? round(($accepted / $totalRequests) * 100, 2) : 0,
            ],
            'top_clients' => $topClients->all(),
            'category_breakdown' => $categoryBreakdown,
            'monthly_trend' => $monthlyTrend,
        ];
    }

    public function getPendingApprovalsCount(): array
    {
        return [
            'pending_promotions' => Promotion::pending()->count(),
            'pending_store_owners' => User::pending()->count(),
            'expired_news' => News::expired()->count(),
        ];
    }

    /**
     * Generate client category distribution report (admin report).
     *
     * @return array
     */
    public function getClientCategoryDistribution(): array
    {
        $clients = User::clients()->get();
        
        $distribution = [
            'Inicial' => 0,
            'Medium' => 0,
            'Premium' => 0
        ];

        foreach ($clients as $client) {
            if (isset($distribution[$client->categoria_cliente])) {
                $distribution[$client->categoria_cliente]++;
            }
        }

        $total = $clients->count();
        
        return [
            'total_clients' => $total,
            'distribution' => $distribution,
            'percentages' => [
                'Inicial' => $total > 0 ? round(($distribution['Inicial'] / $total) * 100, 2) : 0,
                'Medium' => $total > 0 ? round(($distribution['Medium'] / $total) * 100, 2) : 0,
                'Premium' => $total > 0 ? round(($distribution['Premium'] / $total) * 100, 2) : 0,
            ]
        ];
    }

    /**
     * Generate store owner report for their own store(s).
     *
     * @param User $storeOwner
     * @param array $filters ['date_from' => string, 'date_to' => string]
     * @return array
     */
    public function getStoreOwnerReport(User $storeOwner, array $filters = []): array
    {
        if (!$storeOwner->isStoreOwner()) {
            return [];
        }

        $stores = $storeOwner->stores;
        $report = [];

        foreach ($stores as $store) {
            $promotions = $store->promotions;
            
            $usagesQuery = PromotionUsage::query()
                ->whereHas('promotion', function ($q) use ($store) {
                    $q->where('store_id', $store->id);
                });

            // Apply date filters
            if (!empty($filters['date_from'])) {
                $usagesQuery->where('fecha_uso', '>=', $filters['date_from']);
            }
            if (!empty($filters['date_to'])) {
                $usagesQuery->where('fecha_uso', '<=', $filters['date_to']);
            }

            $usages = $usagesQuery->get();

            $report[] = [
                'store' => [
                    'id' => $store->id,
                    'codigo' => $store->codigo,
                    'nombre' => $store->nombre,
                    'rubro' => $store->rubro
                ],
                'promotions' => [
                    'total' => $promotions->count(),
                    'pending' => $promotions->where('estado', 'pendiente')->count(),
                    'approved' => $promotions->where('estado', 'aprobada')->count(),
                    'denied' => $promotions->where('estado', 'denegada')->count(),
                ],
                'usage_requests' => [
                    'total' => $usages->count(),
                    'pending' => $usages->where('estado', 'enviada')->count(),
                    'accepted' => $usages->where('estado', 'aceptada')->count(),
                    'rejected' => $usages->where('estado', 'rechazada')->count(),
                ],
                'clients' => [
                    'unique_count' => $usages->pluck('client_id')->unique()->count(),
                    'by_category' => $this->getClientsByCategoryForStore($usages)
                ]
            ];
        }

        return $report;
    }

    /**
     * Get clients grouped by category for a store's usages.
     *
     * @param \Illuminate\Database\Eloquent\Collection $usages
     * @return array
     */
    private function getClientsByCategoryForStore($usages): array
    {
        $clientIds = $usages->pluck('client_id')->unique();
        $clients = User::whereIn('id', $clientIds)->get();

        $byCategory = [
            'Inicial' => 0,
            'Medium' => 0,
            'Premium' => 0
        ];

        foreach ($clients as $client) {
            if (isset($byCategory[$client->categoria_cliente])) {
                $byCategory[$client->categoria_cliente]++;
            }
        }

        return $byCategory;
    }

    /**
     * Get detailed usage report for a specific promotion.
     *
     * @param Promotion $promotion
     * @return array
     */
    public function getPromotionDetailedReport(Promotion $promotion): array
    {
        $usages = $promotion->usages()->with('client')->get();

        $clientList = [];
        foreach ($usages as $usage) {
            $clientList[] = [
                'client_id' => $usage->client_id,
                'client_name' => $usage->client->name,
                'client_email' => $usage->client->email,
                'client_category' => $usage->client->categoria_cliente,
                'fecha_uso' => $usage->fecha_uso->format('Y-m-d'),
                'estado' => $usage->estado
            ];
        }

        return [
            'promotion' => [
                'id' => $promotion->id,
                'codigo' => $promotion->codigo,
                'texto' => $promotion->texto,
                'fecha_desde' => $promotion->fecha_desde->format('Y-m-d'),
                'fecha_hasta' => $promotion->fecha_hasta->format('Y-m-d'),
                'categoria_minima' => $promotion->categoria_minima,
                'estado' => $promotion->estado
            ],
            'store' => [
                'id' => $promotion->store->id,
                'codigo' => $promotion->store->codigo,
                'nombre' => $promotion->store->nombre
            ],
            'statistics' => [
                'total_requests' => $usages->count(),
                'pending' => $usages->where('estado', 'enviada')->count(),
                'accepted' => $usages->where('estado', 'aceptada')->count(),
                'rejected' => $usages->where('estado', 'rechazada')->count(),
            ],
            'clients' => $clientList
        ];
    }

    /**
     * Get admin dashboard statistics summary.
     *
     * @return array
     */
    public function getAdminDashboardStats(): array
    {
        return [
            'stores' => [
                'total' => Store::count(),
                'by_rubro' => Store::selectRaw('rubro, COUNT(*) as count')
                    ->groupBy('rubro')
                    ->pluck('count', 'rubro')
                    ->toArray()
            ],
            'store_owners' => [
                'total' => User::storeOwners()->count(),
                'approved' => User::storeOwners()->approved()->count(),
                'pending' => User::storeOwners()->pending()->count()
            ],
            'promotions' => [
                'total' => Promotion::count(),
                'pending' => Promotion::pending()->count(),
                'approved' => Promotion::approved()->count(),
                'denied' => Promotion::denied()->count(),
                'active' => Promotion::active()->count()
            ],
            'clients' => [
                'total' => User::clients()->count(),
                'by_category' => [
                    'Inicial' => User::clients()->byCategory('Inicial')->count(),
                    'Medium' => User::clients()->byCategory('Medium')->count(),
                    'Premium' => User::clients()->byCategory('Premium')->count(),
                ]
            ],
            'usage_requests' => [
                'total' => PromotionUsage::count(),
                'pending' => PromotionUsage::pending()->count(),
                'accepted' => PromotionUsage::accepted()->count(),
                'rejected' => PromotionUsage::rejected()->count(),
                'this_month' => PromotionUsage::where('fecha_uso', '>=', Carbon::now()->startOfMonth())->count()
            ]
        ];
    }

    /**
     * Get category upgrade trends over time.
     *
     * @param int $months Number of months to analyze (default 6)
     * @return array
     */
    public function getCategoryUpgradeTrends(int $months = 6): array
    {
        // This is a simplified version - in production, you'd track category changes in a separate audit table
        $clients = User::clients()->get();
        $usageService = new PromotionUsageService();

        $trends = [];
        for ($i = 0; $i < $months; $i++) {
            $monthStart = Carbon::now()->subMonths($i)->startOfMonth();
            $monthEnd = Carbon::now()->subMonths($i)->endOfMonth();

            $monthData = [
                'month' => $monthStart->format('Y-m'),
                'usage_requests' => PromotionUsage::whereBetween('fecha_uso', [$monthStart, $monthEnd])->count(),
                'accepted_requests' => PromotionUsage::whereBetween('fecha_uso', [$monthStart, $monthEnd])
                    ->where('estado', 'aceptada')
                    ->count()
            ];

            $trends[] = $monthData;
        }

        return array_reverse($trends);
    }

    /**
     * Export report data to array format suitable for Excel/PDF generation.
     *
     * @param string $reportType 'usage_by_store', 'category_distribution', 'store_owner'
     * @param array $params Report-specific parameters
     * @return array
     */
    public function exportReport(string $reportType, array $params = []): array
    {
        switch ($reportType) {
            case 'usage_by_store':
                return $this->getPromotionUsageByStore($params);
                
            case 'category_distribution':
                return $this->getClientCategoryDistribution();
                
            case 'store_owner':
                if (empty($params['store_owner'])) {
                    return [];
                }
                return $this->getStoreOwnerReport($params['store_owner'], $params);
                
            default:
                return [];
        }
    }
}
