<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\CategoryUpgradeService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job to evaluate and upgrade client categories based on promotion usage.
 * Runs periodically (default: every 6 months) to check all clients.
 * Uses CategoryUpgradeService for business logic and sends notification emails.
 */
class EvaluateClientCategoriesJob implements ShouldQueue
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
    public int $timeout = 300; // 5 minutes

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        // No parameters needed - processes all clients
    }

    /**
     * Execute the job.
     * Iterates all clients and evaluates each for category upgrade.
     * Logs summary statistics after completion.
     */
    public function handle(CategoryUpgradeService $categoryUpgradeService): void
    {
        Log::info('Starting client category evaluation job');

        $startTime = now();
        $stats = [
            'total_clients' => 0,
            'evaluated' => 0,
            'upgraded' => 0,
            'errors' => 0,
            'upgrades_by_category' => [
                'Inicial -> Medium' => 0,
                'Inicial -> Premium' => 0,
                'Medium -> Premium' => 0,
            ]
        ];

        try {
            // Get all clients (tipo_usuario = 'cliente')
            $clients = User::where('tipo_usuario', 'cliente')
                ->whereNotNull('email_verified_at') // Only verified clients
                ->get();

            $stats['total_clients'] = $clients->count();

            foreach ($clients as $client) {
                try {
                    $stats['evaluated']++;

                    // Store old category for upgrade tracking
                    $oldCategory = $client->categoria_cliente;

                    // Evaluate client for potential upgrade
                    $result = $categoryUpgradeService->evaluateClient($client);

                    // Track upgrades
                    if ($result['upgraded']) {
                        $stats['upgraded']++;
                        
                        // Track upgrade type
                        $upgradeKey = "{$result['old_category']} -> {$result['new_category']}";
                        if (isset($stats['upgrades_by_category'][$upgradeKey])) {
                            $stats['upgrades_by_category'][$upgradeKey]++;
                        }

                        Log::info("Client upgraded successfully", [
                            'user_id' => $client->id,
                            'email' => $client->email,
                            'old_category' => $result['old_category'],
                            'new_category' => $result['new_category']
                        ]);
                    }
                } catch (\Exception $e) {
                    $stats['errors']++;
                    Log::error("Failed to evaluate client category", [
                        'user_id' => $client->id,
                        'email' => $client->email,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            $duration = now()->diffInSeconds($startTime);

            Log::info('Client category evaluation job completed', [
                'duration_seconds' => $duration,
                'stats' => $stats
            ]);
        } catch (\Exception $e) {
            Log::error('Client category evaluation job failed: ' . $e->getMessage(), [
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
        Log::error('EvaluateClientCategoriesJob failed permanently', [
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);

        // TODO: Send alert email to admin about job failure
        // Mail::to(config('mail.admin_email'))->send(new JobFailedMail('EvaluateClientCategoriesJob', $exception));
    }
}
