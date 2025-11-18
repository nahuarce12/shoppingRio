<?php

namespace App\Console\Commands;

use App\Jobs\EvaluateClientCategoriesJob;
use Illuminate\Console\Command;

class EvaluateClientCategories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:evaluate-categories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Evaluate all client categories and upgrade based on promotion usage';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting client category evaluation...');
        
        try {
            // Dispatch the job synchronously for immediate feedback
            EvaluateClientCategoriesJob::dispatchSync();
            
            $this->info('✓ Client category evaluation completed successfully!');
            $this->line('Check storage/logs/laravel.log for detailed statistics.');
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('✗ Failed to evaluate client categories: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
