<?php

namespace App\Services;

use App\Models\News;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Service class for news management and filtering.
 * Handles active news retrieval, expiration checking, and admin CRUD operations.
 */
class NewsService
{
    /**
     * Get active news filtered by client category.
     * Uses category hierarchy (Premium sees all, Medium sees Medium+Inicial, etc.).
     *
     * @param User|null $user Authenticated user (null for unregistered users)
     * @return Collection
     */
    public function getActiveNewsForUser(?User $user): Collection
    {
        if ($user && $user->isClient()) {
            // Filter by client category
            return News::query()
                ->with('creator')
                ->active()
                ->forCategory($user->client_category)
                ->orderBy('start_date', 'desc')
                ->get();
        }

        // For unregistered users, show only 'Inicial' category news
        return News::query()
            ->with('creator')
            ->active()
            ->where('target_category', 'Inicial')
            ->orderBy('start_date', 'desc')
            ->get();
    }

    /**
     * Get all news with optional filters (for admin views).
     *
     * @param array $filters ['categoria' => string, 'expired' => bool, 'active' => bool]
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getFilteredNews(array $filters = [])
    {
        $query = News::query()->with('creator');

        // Filter by category
        if (!empty($filters['categoria'])) {
            $query->where('target_category', $filters['categoria']);
        }

        // Filter by active/expired status
        if (isset($filters['active']) && $filters['active'] === true) {
            $query->active();
        } elseif (isset($filters['expired']) && $filters['expired'] === true) {
            $query->expired();
        }

        return $query->orderBy('start_date', 'desc');
    }

    /**
     * Create new news announcement (admin only).
     *
     * @param array $data ['description', 'start_date', 'end_date', 'target_category', 'created_by']
     * @return array ['success' => bool, 'news' => News|null, 'message' => string]
     */
    public function createNews(array $data): array
    {
        try {
            DB::beginTransaction();

            // Validate date range
            if (Carbon::parse($data['end_date'])->lt(Carbon::parse($data['start_date']))) {
                return [
                    'success' => false,
                    'news' => null,
                    'message' => 'End date must be after start date.'
                ];
            }

            $news = News::create([
                'description' => $data['description'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'target_category' => $data['target_category'],
                'created_by' => $data['created_by']
            ]);

            Log::info("News created: ID {$news->id}", [
                'news_id' => $news->id,
                'code' => $news->code,
                'categoria' => $news->target_category,
                'created_by' => $data['created_by']
            ]);

            DB::commit();

            return [
                'success' => true,
                'news' => $news,
                'message' => 'News created successfully.'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create news: ' . $e->getMessage());
            
            return [
                'success' => false,
                'news' => null,
                'message' => 'Failed to create news. Please try again.'
            ];
        }
    }

    /**
     * Update existing news (admin only).
     *
     * @param News $news
     * @param array $data ['description', 'start_date', 'end_date', 'target_category']
     * @return array ['success' => bool, 'message' => string]
     */
    public function updateNews(News $news, array $data): array
    {
        try {
            DB::beginTransaction();

            // Validate date range if dates are being updated
            if (isset($data['start_date']) && isset($data['end_date'])) {
                if (Carbon::parse($data['end_date'])->lt(Carbon::parse($data['start_date']))) {
                    return [
                        'success' => false,
                        'message' => 'End date must be after start date.'
                    ];
                }
            }

            $news->fill($data);
            $news->save();

            Log::info("News updated: ID {$news->id}", [
                'news_id' => $news->id,
                'code' => $news->code
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => 'News updated successfully.'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update news: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Failed to update news. Please try again.'
            ];
        }
    }

    /**
     * Delete news (admin only).
     *
     * @param News $news
     * @return array ['success' => bool, 'message' => string]
     */
    public function deleteNews(News $news): array
    {
        try {
            $newsId = $news->id;
            $codigo = $news->code;

            $news->delete();

            Log::info("News deleted: ID {$newsId}, codigo {$codigo}");

            return [
                'success' => true,
                'message' => 'News deleted successfully.'
            ];
        } catch (\Exception $e) {
            Log::error('Failed to delete news: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Failed to delete news. Please try again.'
            ];
        }
    }

    /**
     * Get count of expired news.
     *
     * @return int
     */
    public function getExpiredNewsCount(): int
    {
        return News::expired()->count();
    }

    /**
     * Delete all expired news.
     * Used by scheduled cleanup job.
     *
     * @return int Number of deleted news
     */
    public function deleteExpiredNews(): int
    {
        try {
            $expiredNews = News::expired()->get();
            $count = $expiredNews->count();

            if ($count > 0) {
                News::expired()->delete();
                
                Log::info("Expired news cleanup: {$count} news deleted", [
                    'deleted_count' => $count,
                    'deleted_at' => Carbon::now()
                ]);
            }

            return $count;
        } catch (\Exception $e) {
            Log::error('Failed to delete expired news: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get news statistics.
     *
     * @return array
     */
    public function getNewsStats(): array
    {
        $totalNews = News::count();
        $activeNews = News::active()->count();
        $expiredNews = News::expired()->count();

        $byCategory = News::query()
            ->selectRaw('categoria_destino, COUNT(*) as count')
            ->groupBy('target_category')
            ->pluck('count', 'target_category')
            ->toArray();

        return [
            'total' => $totalNews,
            'active' => $activeNews,
            'expired' => $expiredNews,
            'by_category' => $byCategory
        ];
    }

    /**
     * Check if news will expire within N days.
     *
     * @param News $news
     * @param int $days Number of days to check ahead
     * @return bool
     */
    public function willExpireSoon(News $news, int $days = 7): bool
    {
        $expirationDate = Carbon::parse($news->end_date);
        $checkDate = Carbon::now()->addDays($days);

        return $expirationDate->lte($checkDate) && !$news->isExpired();
    }

    /**
     * Get news that will expire soon.
     *
     * @param int $days Number of days to look ahead (default 7)
     * @return Collection
     */
    public function getExpiringSoon(int $days = 7): Collection
    {
        $checkDate = Carbon::now()->addDays($days);

        return News::query()
            ->active()
            ->where('end_date', '<=', $checkDate)
            ->orderBy('end_date', 'asc')
            ->get();
    }

    /**
     * Extend news expiration date.
     *
     * @param News $news
     * @param int $days Number of days to extend
     * @return array ['success' => bool, 'message' => string]
     */
    public function extendExpiration(News $news, int $days): array
    {
        try {
            $oldDate = $news->end_date;
            $newDate = Carbon::parse($oldDate)->addDays($days);

            $news->end_date = $newDate;
            $news->save();

            Log::info("News expiration extended: ID {$news->id}", [
                'news_id' => $news->id,
                'old_date' => $oldDate,
                'new_date' => $newDate,
                'days_added' => $days
            ]);

            return [
                'success' => true,
                'message' => "News expiration extended by {$days} days."
            ];
        } catch (\Exception $e) {
            Log::error('Failed to extend news expiration: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Failed to extend expiration. Please try again.'
            ];
        }
    }
}
