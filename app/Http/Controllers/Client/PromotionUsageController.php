<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\PromotionUsageRequest;
use App\Models\Promotion;
use App\Services\PromotionUsageService;
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
    public function request(PromotionUsageRequest $request, Promotion $promotion)
    {
        $result = $this->promotionUsageService->createUsageRequest(
            auth()->user(),
            $promotion
        );

        if ($result['success']) {
            return redirect()
                ->route('client.usage-history')
                ->with('success', 'Solicitud enviada exitosamente. Espere la aprobación del local.');
        }

        return redirect()
            ->back()
            ->with('error', $result['message']);
    }

    /**
     * Display client's usage history.
     */
    public function history(Request $request)
    {
        $filters = $request->only(['estado', 'fecha_desde', 'fecha_hasta']);

        $usageHistory = $this->promotionUsageService->getClientUsageHistory(
            auth()->id(),
            $filters
        );

        // Statistics
        $stats = [
            'total' => auth()->user()->promotionUsages()->count(),
            'enviada' => auth()->user()->promotionUsages()->where('estado', 'enviada')->count(),
            'aceptada' => auth()->user()->promotionUsages()->where('estado', 'aceptada')->count(),
            'rechazada' => auth()->user()->promotionUsages()->where('estado', 'rechazada')->count(),
        ];

        return view('client.usage-history', compact('usageHistory', 'stats'));
    }
}
