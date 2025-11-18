<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\PromotionUsageRequest;
use App\Models\Promotion;
use App\Services\PromotionUsageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Client controller for requesting promotions and tracking usage history.
 * Implements the promotion usage request flow.
 */
class PromotionUsageController extends Controller
{
    public function __construct(
        private PromotionUsageService $promotionUsageService
    ) {
    }

    /**
     * Request to use a promotion (send request to store owner).
     */
    public function request(PromotionUsageRequest $request): RedirectResponse
    {
        $promotionId = $request->validated()['promotion_id'];

        $promotion = Promotion::with('store.owner')->findOrFail($promotionId);

        $result = $this->promotionUsageService->createUsageRequest(
            $promotion,
            $request->user()
        );

        if ($result['success']) {
            return redirect()
                ->route('promociones.show', $promotion)
                ->with('success', 'Solicitud enviada exitosamente. Esperá la aprobación del local.');
        }

        return redirect()
            ->back()
            ->with('error', $result['message']);
    }

    /**
     * Display client's usage history.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['status', 'start_date', 'end_date']);

        $client = $request->user();

        $usageHistory = $this->promotionUsageService->getClientUsageHistory(
            $client,
            $filters
        )->paginate(10);

        // Statistics
        $stats = [
            'total' => $client->promotionUsages()->count(),
            'enviada' => $client->promotionUsages()->where('status', 'enviada')->count(),
            'aceptada' => $client->promotionUsages()->where('status', 'aceptada')->count(),
            'rechazada' => $client->promotionUsages()->where('status', 'rechazada')->count(),
        ];

        return view('client.usage-history', compact('usageHistory', 'stats'));
    }
}
