<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePromotionStatusRequest;
use App\Models\Promotion;
use App\Services\PromotionService;
use Illuminate\Http\Request;

/**
 * Admin controller for approving or denying store promotions.
 * Implements the promotion approval workflow required by business rules.
 */
class PromotionApprovalController extends Controller
{
    public function __construct(
        private PromotionService $promotionService
    ) {
    }

    /**
     * Display a listing of promotions pending approval.
     * Supports filtering by store, date range, and search query.
     */
    public function index(Request $request)
    {
    // Las aprobaciones se gestionan desde el panel principal.
    return redirect()->route('admin.dashboard', ['section' => 'aprobar-promociones']);
    }

    /**
     * Display the specified promotion for review.
     */
    public function show(Promotion $promotion)
    {
        // Only show pending promotions
        if ($promotion->status !== 'pendiente') {
            return redirect()
                ->route('admin.dashboard', ['section' => 'aprobar-promociones'])
                ->with('info', 'Esta promoción ya fue procesada.');
        }

        return redirect()
            ->route('admin.dashboard', ['section' => 'aprobar-promociones'])
            ->with('info', 'Usá el panel de administrador para revisar promociones pendientes.');
    }

    /**
     * Approve a promotion.
     */
    public function approve(UpdatePromotionStatusRequest $request, Promotion $promotion)
    {
        if ($promotion->status !== 'pendiente') {
            return redirect()
                ->route('admin.dashboard', ['section' => 'aprobar-promociones'])
                ->with('error', 'Esta promoción ya fue procesada anteriormente.');
        }

        $success = $this->promotionService->approvePromotion($promotion);

        if ($success) {
            return redirect()
                ->route('admin.dashboard', ['section' => 'aprobar-promociones'])
                ->with('success', "Promoción '{$promotion->description}' aprobada exitosamente. Se envió notificación al local.");
        }

        return redirect()
            ->back()
            ->with('error', 'Error al aprobar la promoción. Por favor intente nuevamente.');
    }

    /**
     * Deny a promotion with optional reason.
     */
    public function deny(UpdatePromotionStatusRequest $request, Promotion $promotion)
    {
        if ($promotion->status !== 'pendiente') {
            return redirect()
                ->route('admin.dashboard', ['section' => 'aprobar-promociones'])
                ->with('error', 'Esta promoción ya fue procesada anteriormente.');
        }

    $reason = $request->validated()['reason'] ?? null;

        $success = $this->promotionService->denyPromotion($promotion, $reason);

        if ($success) {
            return redirect()
                ->route('admin.dashboard', ['section' => 'aprobar-promociones'])
                ->with('success', "Promoción '{$promotion->description}' denegada. Se envió notificación al local con el motivo.");
        }

        return redirect()
            ->back()
            ->with('error', 'Error al denegar la promoción. Por favor intente nuevamente.');
    }

    /**
     * Display all promotions (approved, denied, pending) for admin review.
     */
    public function all(Request $request)
    {
        return redirect()->route('admin.dashboard', ['section' => 'aprobar-promociones']);
    }
}
