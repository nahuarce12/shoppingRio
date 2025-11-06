<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\NewsService;
use App\Services\PromotionUsageService;

/**
 * Client dashboard controller.
 * Displays available promotions, usage history, news, and account information.
 */
class DashboardController extends Controller
{
    public function __construct(
        private PromotionUsageService $promotionUsageService,
        private NewsService $newsService
    ) {
    }

    /**
     * Display the client dashboard.
     */
    public function index()
    {
        $client = auth()->user();

        // Client category information
        $categoryInfo = config('shopping.client_categories.' . $client->categoria_cliente, []);

        // Usage statistics
        $usageStats = [
            'total' => $client->promotionUsages()->count(),
            'enviada' => $client->promotionUsages()->where('estado', 'enviada')->count(),
            'aceptada' => $client->promotionUsages()->where('estado', 'aceptada')->count(),
        ];

        // Recent usage history
        $recentUsages = $client->promotionUsages()
            ->with(['promotion.store'])
            ->orderBy('fecha_uso', 'desc')
            ->limit(5)
            ->get();

        // Active news for this client category
        $news = $this->newsService->getActiveNewsForUser($client);

        // Available promotions count
        $availablePromotionsCount = \App\Models\Promotion::approved()
            ->active()
            ->validToday()
            ->forCategory($client->categoria_cliente)
            ->whereDoesntHave('usages', function ($q) use ($client) {
                $q->where('client_id', $client->id);
            })
            ->count();

        return view('dashboard.client.index', compact(
            'client',
            'categoryInfo',
            'usageStats',
            'recentUsages',
            'news',
            'availablePromotionsCount'
        ));
    }
}
