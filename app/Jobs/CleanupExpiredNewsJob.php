<?php

namespace App\Jobs;

use App\Models\News;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job to cleanup expired news items.
 * Runs daily at midnight to remove or archive news past their fecha_hasta date.
 * Configurable retention period before permanent deletion.
 */
class CleanupExpiredNewsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public int $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public int $timeout = 120; // 2 minutes

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        // No parameters needed - processes all expired news
    }

    /**
     * Execute the job.
     * Deletes news items that expired more than the retention period ago.
     * Retention period configured in config/shopping.php (default: 30 days).
     */
    public function handle(): void
    {
        Log::info('Starting expired news cleanup job');

        $startTime = now();
        $stats = [
            'total_expired' => 0,
            'deleted' => 0,
            'errors' => 0
        ];

        try {
            // Get retention period from config (days after expiration before deletion)
            $retentionDays = config('shopping.scheduled_jobs.news_cleanup.retention_days', 30);
            
            // Calculate cutoff date: news expired more than retention period ago
            $cutoffDate = Carbon::now()->subDays($retentionDays);

            // Find expired news beyond retention period
            $expiredNews = News::where('fecha_hasta', '<', $cutoffDate)->get();

            $stats['total_expired'] = $expiredNews->count();

            if ($stats['total_expired'] === 0) {
                Log::info('No expired news to cleanup');
                return;
            }

            foreach ($expiredNews as $news) {
                try {
                    $newsId = $news->id;
                    $newsTitle = substr($news->texto_novedad, 0, 50); // First 50 chars for logging
                    $expiredDate = $news->fecha_hasta;

                    // Delete the news item
                    $news->delete();
                    $stats['deleted']++;

                    Log::info('Deleted expired news', [
                        'news_id' => $newsId,
                        'title_preview' => $newsTitle,
                        'expired_date' => $expiredDate->toDateString(),
                        'days_since_expiration' => Carbon::now()->diffInDays($expiredDate)
                    ]);
                } catch (\Exception $e) {
                    $stats['errors']++;
                    Log::error('Failed to delete expired news', [
                        'news_id' => $news->id ?? 'unknown',
                        'error' => $e->getMessage()
                    ]);
                }
            }

            $duration = now()->diffInSeconds($startTime);

            Log::info('Expired news cleanup job completed', [
                'duration_seconds' => $duration,
                'cutoff_date' => $cutoffDate->toDateString(),
                'retention_days' => $retentionDays,
                'stats' => $stats
            ]);
        } catch (\Exception $e) {
            Log::error('Expired news cleanup job failed: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            throw $e; // Re-throw to trigger job retry
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('CleanupExpiredNewsJob failed permanently', [
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);

        // TODO: Send alert email to admin about job failure
        // Mail::to(config('mail.admin_email'))->send(new JobFailedMail('CleanupExpiredNewsJob', $exception));
    }
}
