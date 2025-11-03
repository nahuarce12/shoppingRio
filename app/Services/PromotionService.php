<?php

namespace App\Services;

use App\Mail\PromotionApprovedMail;
use App\Mail\PromotionDeniedMail;
use App\Models\Promotion;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Service class for promotion-related business logic.
 * Handles eligibility checking, filtering, and approval workflows.
 */
class PromotionService
{
    /**
     * Check if a promotion is eligible for a specific client.
     * Validates: approved status, date range, day of week, category access, single-use rule.
     *
     * @param Promotion $promotion
     * @param User $client
     * @return array ['eligible' => bool, 'reason' => string|null]
     */
    public function checkEligibility(Promotion $promotion, User $client): array
    {
        // Check if promotion is approved
        if ($promotion->estado !== 'aprobada') {
            return [
                'eligible' => false,
                'reason' => 'Promotion is not approved by admin yet.'
            ];
        }

        // Check if promotion is within date range
        $today = Carbon::today();
        if ($today->lt($promotion->fecha_desde) || $today->gt($promotion->fecha_hasta)) {
            return [
                'eligible' => false,
                'reason' => 'Promotion is not within valid date range.'
            ];
        }

        // Check if today is a valid day of week for this promotion
        $dayOfWeek = $today->dayOfWeek; // 0 = Sunday, 1 = Monday, ..., 6 = Saturday
        // Convert to project convention: 0 = Monday to 6 = Sunday
        $projectDayIndex = ($dayOfWeek === 0) ? 6 : $dayOfWeek - 1;
        
        if (!isset($promotion->dias_semana[$projectDayIndex]) || !$promotion->dias_semana[$projectDayIndex]) {
            return [
                'eligible' => false,
                'reason' => 'Promotion is not valid for today.'
            ];
        }

        // Check if client has access based on category hierarchy
        if (!$client->canAccessCategory($promotion->categoria_minima)) {
            return [
                'eligible' => false,
                'reason' => 'Your client category does not have access to this promotion.'
            ];
        }

        // Check if client has already used this promotion (single-use rule)
        $hasUsed = $promotion->hasBeenUsedBy($client->id);
        if ($hasUsed) {
            return [
                'eligible' => false,
                'reason' => 'You have already used this promotion.'
            ];
        }

        return [
            'eligible' => true,
            'reason' => null
        ];
    }

    /**
     * Get all available promotions for a specific client.
     * Filters by approval status, date range, day of week, and category access.
     *
     * @param User $client
     * @param array $filters Optional filters: ['store_id' => int, 'search' => string]
     * @return Collection
     */
    public function getAvailablePromotions(User $client, array $filters = []): Collection
    {
        $query = Promotion::query()
            ->with('store')
            ->approved()
            ->active()
            ->validToday()
            ->forCategory($client->categoria_cliente);

        // Apply store filter if provided
        if (!empty($filters['store_id'])) {
            $query->where('store_id', $filters['store_id']);
        }

        // Apply search filter if provided (search in promotion text or store name)
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('texto', 'like', "%{$search}%")
                  ->orWhereHas('store', function ($sq) use ($search) {
                      $sq->where('nombre', 'like', "%{$search}%");
                  });
            });
        }

        // Exclude promotions already used by this client
        $query->whereDoesntHave('usages', function ($q) use ($client) {
            $q->where('client_id', $client->id);
        });

        return $query->orderBy('fecha_hasta', 'asc')->get();
    }

    /**
     * Get all promotions with optional filters (for admin/store owner views).
     *
     * @param array $filters ['estado' => string, 'store_id' => int, 'date_from' => string, 'date_to' => string]
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getFilteredPromotions(array $filters = [])
    {
        $query = Promotion::query()->with('store');

        // Filter by status
        if (!empty($filters['estado'])) {
            $query->where('estado', $filters['estado']);
        }

        // Filter by store
        if (!empty($filters['store_id'])) {
            $query->where('store_id', $filters['store_id']);
        }

        // Filter by date range
        if (!empty($filters['date_from'])) {
            $query->where('fecha_desde', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->where('fecha_hasta', '<=', $filters['date_to']);
        }

        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Approve a promotion (admin only).
     * Updates estado to 'aprobada' and sends notification email to store owner.
     *
     * @param Promotion $promotion
     * @return bool
     */
    public function approvePromotion(Promotion $promotion): bool
    {
        if ($promotion->estado !== 'pendiente') {
            return false;
        }

        try {
            DB::beginTransaction();

            $promotion->estado = 'aprobada';
            $promotion->save();

            // Send approval email to store owner
            Mail::to($promotion->store->owner->nombreUsuario)
                ->send(new PromotionApprovedMail($promotion));

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to approve promotion: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Deny a promotion (admin only).
     * Updates estado to 'denegada' and sends notification email to store owner.
     *
     * @param Promotion $promotion
     * @param string|null $reason Optional reason for denial
     * @return bool
     */
    public function denyPromotion(Promotion $promotion, ?string $reason = null): bool
    {
        if ($promotion->estado !== 'pendiente') {
            return false;
        }

        try {
            DB::beginTransaction();

            $promotion->estado = 'denegada';
            $promotion->save();

            // Send denial email to store owner with reason
            Mail::to($promotion->store->owner->nombreUsuario)
                ->send(new PromotionDeniedMail($promotion, $reason));

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to deny promotion: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get statistics for a specific promotion.
     * Returns usage counts by status.
     *
     * @param Promotion $promotion
     * @return array ['total' => int, 'pending' => int, 'accepted' => int, 'rejected' => int]
     */
    public function getPromotionStats(Promotion $promotion): array
    {
        $usages = $promotion->usages;

        return [
            'total' => $usages->count(),
            'pending' => $usages->where('estado', 'enviada')->count(),
            'accepted' => $usages->where('estado', 'aceptada')->count(),
            'rejected' => $usages->where('estado', 'rechazada')->count(),
        ];
    }

    /**
     * Get all promotions viewable by unregistered users (all approved promotions).
     *
     * @param array $filters ['store_id' => int, 'categoria' => string, 'search' => string]
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getPublicPromotions(array $filters = [])
    {
        $query = Promotion::query()
            ->with('store')
            ->approved();

        // Filter by store
        if (!empty($filters['store_id'])) {
            $query->where('store_id', $filters['store_id']);
        }

        // Filter by category
        if (!empty($filters['categoria'])) {
            $query->forCategory($filters['categoria']);
        }

        // Search in promotion text or store name
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('texto', 'like', "%{$search}%")
                  ->orWhereHas('store', function ($sq) use ($search) {
                      $sq->where('nombre', 'like', "%{$search}%");
                  });
            });
        }

        return $query->orderBy('fecha_hasta', 'asc');
    }

    /**
     * Validate dias_semana array structure.
     * Ensures array has exactly 7 boolean values.
     *
     * @param array $diasSemana
     * @return bool
     */
    public function validateDiasSemana(array $diasSemana): bool
    {
        if (count($diasSemana) !== 7) {
            return false;
        }

        foreach ($diasSemana as $value) {
            if (!is_bool($value)) {
                return false;
            }
        }

        return true;
    }
}
