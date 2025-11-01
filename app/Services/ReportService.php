<?php

namespace App\Services;

use App\Models\Promotion;
use App\Models\PromotionUsage;
use App\Models\Store;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Service class for generating reports and statistics.
 * Provides admin and store owner reporting functionality.
 */
class ReportService
{
    /**
     * Generate promotion usage statistics by store (admin report).
     *
     * @param array $filters ['date_from' => string, 'date_to' => string, 'store_id' => int]
     * @return array
     */
    public function getPromotionUsageByStore(array $filters = []): array
    {
        $query = Store::query()->with('promotions.usages');

        // Filter by specific store if provided
        if (!empty($filters['store_id'])) {
            $query->where('id', $filters['store_id']);
        }

        $stores = $query->get();
        $report = [];

        foreach ($stores as $store) {
            $usages = PromotionUsage::query()
                ->whereHas('promotion', function ($q) use ($store) {
                    $q->where('store_id', $store->id);
                });

            // Apply date filters
            if (!empty($filters['date_from'])) {
                $usages->where('fecha_uso', '>=', $filters['date_from']);
            }
            if (!empty($filters['date_to'])) {
                $usages->where('fecha_uso', '<=', $filters['date_to']);
            }

            $usagesData = $usages->get();

            $report[] = [
                'store_id' => $store->id,
                'store_codigo' => $store->codigo,
                'store_name' => $store->nombre,
                'store_rubro' => $store->rubro,
                'total_promotions' => $store->promotions->count(),
                'approved_promotions' => $store->promotions->where('estado', 'aprobada')->count(),
                'total_requests' => $usagesData->count(),
                'pending_requests' => $usagesData->where('estado', 'enviada')->count(),
                'accepted_requests' => $usagesData->where('estado', 'aceptada')->count(),
                'rejected_requests' => $usagesData->where('estado', 'rechazada')->count(),
                'unique_clients' => $usagesData->pluck('client_id')->unique()->count(),
            ];
        }

        return $report;
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
