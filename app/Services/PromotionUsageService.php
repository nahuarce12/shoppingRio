<?php

namespace App\Services;

use App\Mail\PromotionUsageAcceptedMail;
use App\Mail\PromotionUsageRejectedMail;
use App\Mail\PromotionUsageRequestMail;
use App\Models\Promotion;
use App\Models\PromotionUsage;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Service class for promotion usage request management.
 * Handles creating usage requests, accepting/rejecting, and statistics.
 */
class PromotionUsageService
{
    /**
     * Create a promotion usage request for a client.
     * Validates eligibility before creating the request.
     *
     * @param Promotion $promotion
     * @param User $client
     * @return array ['success' => bool, 'message' => string, 'usage' => PromotionUsage|null]
     */
    public function createUsageRequest(Promotion $promotion, User $client): array
    {
        // Check eligibility first
        $promotionService = new PromotionService();
        $eligibility = $promotionService->checkEligibility($promotion, $client);

        if (!$eligibility['eligible']) {
            return [
                'success' => false,
                'message' => $eligibility['reason'],
                'usage' => null
            ];
        }

        try {
            DB::beginTransaction();

            // Create usage request with 'enviada' status
            $usage = PromotionUsage::create([
                'client_id' => $client->id,
                'promotion_id' => $promotion->id,
                'fecha_uso' => Carbon::today(),
                'estado' => 'enviada'
            ]);

            // Send notification email to store owner
            Mail::to($promotion->store->owner->nombreUsuario)
                ->send(new PromotionUsageRequestMail($usage));

            DB::commit();

            return [
                'success' => true,
                'message' => 'Usage request sent successfully. Awaiting store owner approval.',
                'usage' => $usage
            ];
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            
            // Check if it's a duplicate entry error (unique constraint violation)
            if ($e->getCode() == 23000) {
                return [
                    'success' => false,
                    'message' => 'You have already requested this promotion.',
                    'usage' => null
                ];
            }

            Log::error('Failed to create usage request: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to create usage request. Please try again.',
                'usage' => null
            ];
        }
    }

    /**
     * Accept a usage request (store owner action).
     * Updates estado to 'aceptada' and sends notification to client.
     *
     * @param PromotionUsage $usage
     * @return bool
     */
    public function acceptUsageRequest(PromotionUsage $usage): bool
    {
        if ($usage->estado !== 'enviada') {
            return false;
        }

        try {
            DB::beginTransaction();

            $usage->estado = 'aceptada';
            $usage->save();

            // Send acceptance email to client
            Mail::to($usage->client->nombreUsuario)
                ->send(new PromotionUsageAcceptedMail($usage));

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to accept usage request: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Reject a usage request (store owner action).
     * Updates estado to 'rechazada' and sends notification to client.
     *
     * @param PromotionUsage $usage
     * @param string|null $reason Optional reason for rejection
     * @return bool
     */
    public function rejectUsageRequest(PromotionUsage $usage, ?string $reason = null): bool
    {
        if ($usage->estado !== 'enviada') {
            return false;
        }

        try {
            DB::beginTransaction();

            $usage->estado = 'rechazada';
            $usage->save();

            // Send rejection email to client with reason
            Mail::to($usage->client->nombreUsuario)
                ->send(new PromotionUsageRejectedMail($usage, $reason));

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to reject usage request: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get pending usage requests for a specific store.
     *
     * @param int $storeId
     * @return Collection
     */
    public function getPendingRequestsForStore(int $storeId): Collection
    {
        return PromotionUsage::query()
            ->with(['client', 'promotion'])
            ->whereHas('promotion', function ($query) use ($storeId) {
                $query->where('store_id', $storeId);
            })
            ->pending()
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Get usage history for a specific client.
     *
     * @param User $client
     * @param array $filters ['estado' => string, 'date_from' => string, 'date_to' => string]
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getClientUsageHistory(User $client, array $filters = [])
    {
        $query = PromotionUsage::query()
            ->with(['promotion.store'])
            ->where('client_id', $client->id);

        // Filter by status
        if (!empty($filters['estado'])) {
            $query->where('estado', $filters['estado']);
        }

        // Filter by date range
        if (!empty($filters['date_from'])) {
            $query->where('fecha_uso', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->where('fecha_uso', '<=', $filters['date_to']);
        }

        return $query->orderBy('fecha_uso', 'desc');
    }

    /**
     * Get usage statistics for a specific promotion.
     *
     * @param Promotion $promotion
     * @return array
     */
    public function getPromotionUsageStats(Promotion $promotion): array
    {
        $usages = $promotion->usages;

        $stats = [
            'total_requests' => $usages->count(),
            'pending' => $usages->where('estado', 'enviada')->count(),
            'accepted' => $usages->where('estado', 'aceptada')->count(),
            'rejected' => $usages->where('estado', 'rechazada')->count(),
            'acceptance_rate' => 0,
            'unique_clients' => $usages->pluck('client_id')->unique()->count(),
        ];

        // Calculate acceptance rate
        $totalProcessed = $stats['accepted'] + $stats['rejected'];
        if ($totalProcessed > 0) {
            $stats['acceptance_rate'] = round(($stats['accepted'] / $totalProcessed) * 100, 2);
        }

        return $stats;
    }

    /**
     * Get usage statistics for a specific store.
     *
     * @param int $storeId
     * @return array
     */
    public function getStoreUsageStats(int $storeId): array
    {
        $usages = PromotionUsage::query()
            ->whereHas('promotion', function ($query) use ($storeId) {
                $query->where('store_id', $storeId);
            })
            ->get();

        $stats = [
            'total_requests' => $usages->count(),
            'pending' => $usages->where('estado', 'enviada')->count(),
            'accepted' => $usages->where('estado', 'aceptada')->count(),
            'rejected' => $usages->where('estado', 'rechazada')->count(),
            'unique_clients' => $usages->pluck('client_id')->unique()->count(),
        ];

        return $stats;
    }

    /**
     * Get accepted usage count for a client in the last N months.
     * Used for category upgrade evaluation.
     *
     * @param User $client
     * @param int $months Number of months to look back (default 6)
     * @return int
     */
    public function getAcceptedUsageCount(User $client, int $months = 6): int
    {
        return PromotionUsage::query()
            ->where('client_id', $client->id)
            ->where('estado', 'aceptada')
            ->where('fecha_uso', '>=', Carbon::now()->subMonths($months))
            ->count();
    }

    /**
     * Check if a client has any pending requests.
     *
     * @param User $client
     * @return bool
     */
    public function hasPendingRequests(User $client): bool
    {
        return PromotionUsage::query()
            ->where('client_id', $client->id)
            ->where('estado', 'enviada')
            ->exists();
    }

    /**
     * Get all usage requests with optional filters (for admin reports).
     *
     * @param array $filters ['store_id' => int, 'estado' => string, 'date_from' => string, 'date_to' => string]
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getFilteredUsageRequests(array $filters = [])
    {
        $query = PromotionUsage::query()
            ->with(['client', 'promotion.store']);

        // Filter by store
        if (!empty($filters['store_id'])) {
            $query->whereHas('promotion', function ($q) use ($filters) {
                $q->where('store_id', $filters['store_id']);
            });
        }

        // Filter by status
        if (!empty($filters['estado'])) {
            $query->where('estado', $filters['estado']);
        }

        // Filter by date range
        if (!empty($filters['date_from'])) {
            $query->where('fecha_uso', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->where('fecha_uso', '<=', $filters['date_to']);
        }

        return $query->orderBy('fecha_uso', 'desc');
    }
}
