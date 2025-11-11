<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\PromotionUsage;
use App\Services\PromotionUsageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Store owner controller for managing promotion usage requests from clients.
 * Handles accepting or rejecting client discount requests.
 */
class PromotionUsageController extends Controller
{
    public function __construct(
        private PromotionUsageService $promotionUsageService
    ) {
    }

    /**
     * Accept a usage request.
     */
    public function accept(PromotionUsage $promotionUsage)
    {
        $user = Auth::user();
        $store = $user?->store;

        if (!$store) {
            abort(403, 'No tiene un local asignado.');
        }

        // Load promotion with store relationship (including soft deleted)
        $promotionUsage->load(['promotion' => function ($query) {
            $query->withTrashed()->with('store');
        }]);

        // Debug logging
        Log::info('Accept Usage Debug', [
            'usage_id' => $promotionUsage->id,
            'promotion_id' => $promotionUsage->promotion_id,
            'has_promotion' => !is_null($promotionUsage->promotion),
            'promotion_exists' => $promotionUsage->promotion ? 'yes' : 'no',
            'store_id_from_user' => $store->id,
            'promotion_store_id' => $promotionUsage->promotion?->store_id ?? 'null'
        ]);

        if (!$promotionUsage->promotion) {
            return redirect()
                ->route('store.dashboard', ['section' => 'solicitudes'])
                ->with('error', 'La promoción asociada ya no está disponible.');
        }

        // Verify the usage request belongs to this store
        if ($promotionUsage->promotion->store_id !== $store->id) {
            abort(403, 'No tiene permiso para procesar esta solicitud.');
        }

        $success = $this->promotionUsageService->acceptUsageRequest($promotionUsage);

        if ($success) {
            return redirect()
                ->route('store.dashboard', ['section' => 'solicitudes'])
                ->with('success', 'Solicitud aceptada. Se notificó al cliente.');
        }

        return redirect()
            ->back()
            ->with('error', 'Error al aceptar la solicitud.');
    }

    /**
     * Reject a usage request with optional reason.
     */
    public function reject(Request $request, PromotionUsage $promotionUsage)
    {
        $user = Auth::user();
        $store = $user?->store;

        if (!$store) {
            abort(403, 'No tiene un local asignado.');
        }

        // Load promotion with store relationship (including soft deleted)
        $promotionUsage->load(['promotion' => function ($query) {
            $query->withTrashed()->with('store');
        }]);

        if (!$promotionUsage->promotion) {
            return redirect()
                ->route('store.dashboard', ['section' => 'solicitudes'])
                ->with('error', 'La promoción asociada ya no está disponible.');
        }

        // Verify the usage request belongs to this store
        if ($promotionUsage->promotion->store_id !== $store->id) {
            abort(403, 'No tiene permiso para procesar esta solicitud.');
        }

        $request->validate([
            'reason' => 'nullable|string|max:500'
        ]);

        $reason = $request->input('reason');

        $success = $this->promotionUsageService->rejectUsageRequest($promotionUsage, $reason);

        if ($success) {
            return redirect()
                ->route('store.dashboard', ['section' => 'solicitudes'])
                ->with('success', 'Solicitud rechazada. Se notificó al cliente.');
        }

        return redirect()
            ->back()
            ->with('error', 'Error al rechazar la solicitud.');
    }
}
