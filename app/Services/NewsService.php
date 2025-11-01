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
                ->forCategory($user->categoria_cliente)
                ->orderBy('fecha_desde', 'desc')
                ->get();
        }

        // For unregistered users, show only 'Inicial' category news
        return News::query()
            ->with('creator')
            ->active()
            ->where('categoria_destino', 'Inicial')
            ->orderBy('fecha_desde', 'desc')
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
            $query->where('categoria_destino', $filters['categoria']);
        }

        // Filter by active/expired status
        if (isset($filters['active']) && $filters['active'] === true) {
            $query->active();
        } elseif (isset($filters['expired']) && $filters['expired'] === true) {
            $query->expired();
        }

        return $query->orderBy('fecha_desde', 'desc');
    }

    /**
     * Create new news announcement (admin only).
     *
     * @param array $data ['texto', 'fecha_desde', 'fecha_hasta', 'categoria_destino', 'created_by']
     * @return array ['success' => bool, 'news' => News|null, 'message' => string]
     */
    public function createNews(array $data): array
    {
        try {
            DB::beginTransaction();

            // Validate date range
            if (Carbon::parse($data['fecha_hasta'])->lt(Carbon::parse($data['fecha_desde']))) {
                return [
                    'success' => false,
                    'news' => null,
                    'message' => 'End date must be after start date.'
                ];
            }

            $news = News::create([
                'texto' => $data['texto'],
                'fecha_desde' => $data['fecha_desde'],
                'fecha_hasta' => $data['fecha_hasta'],
                'categoria_destino' => $data['categoria_destino'],
                'created_by' => $data['created_by']
            ]);

            Log::info("News created: ID {$news->id}", [
                'news_id' => $news->id,
                'codigo' => $news->codigo,
                'categoria' => $news->categoria_destino,
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
     * @param array $data ['texto', 'fecha_desde', 'fecha_hasta', 'categoria_destino']
     * @return array ['success' => bool, 'message' => string]
     */
    public function updateNews(News $news, array $data): array
    {
        try {
            DB::beginTransaction();

            // Validate date range if dates are being updated
            if (isset($data['fecha_desde']) && isset($data['fecha_hasta'])) {
                if (Carbon::parse($data['fecha_hasta'])->lt(Carbon::parse($data['fecha_desde']))) {
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
                'codigo' => $news->codigo
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
            $codigo = $news->codigo;

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
            ->groupBy('categoria_destino')
            ->pluck('count', 'categoria_destino')
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
        $expirationDate = Carbon::parse($news->fecha_hasta);
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
            ->where('fecha_hasta', '<=', $checkDate)
            ->orderBy('fecha_hasta', 'asc')
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
            $oldDate = $news->fecha_hasta;
            $newDate = Carbon::parse($oldDate)->addDays($days);

            $news->fecha_hasta = $newDate;
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
