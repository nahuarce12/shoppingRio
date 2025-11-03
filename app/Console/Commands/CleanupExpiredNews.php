<?php

namespace App\Console\Commands;

use App\Jobs\CleanupExpiredNewsJob;
use Illuminate\Console\Command;

class CleanupExpiredNews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cleanup-news';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup expired news items based on retention period';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting expired news cleanup...');
        
        try {
            // Dispatch the job synchronously for immediate feedback
            CleanupExpiredNewsJob::dispatchSync();
            
            $this->info('✓ Expired news cleanup completed successfully!');
            $this->line('Check storage/logs/laravel.log for detailed statistics.');
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('✗ Failed to cleanup expired news: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
