<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CheckAdminExists extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:admin-exists';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if admin user exists (returns exit code 0 if exists, 1 if not)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        try {
            $admin = User::where('user_type', 'administrador')->first();
            
            if ($admin) {
                $this->line("Admin exists: {$admin->email}");
                return 0; // Admin exists
            } else {
                $this->line("No admin found");
                return 1; // Admin doesn't exist
            }
        } catch (\Exception $e) {
            $this->error("Error checking admin: {$e->getMessage()}");
            return 1;
        }
    }
}
