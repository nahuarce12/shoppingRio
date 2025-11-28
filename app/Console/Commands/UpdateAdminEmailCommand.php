<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Validation\Rule;

class UpdateAdminEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:update-email {email? : The new email address for the administrator}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the administrator email address';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Find the administrator user
        $admin = User::where('user_type', 'administrador')->first();

        if (!$admin) {
            $this->error('❌ Administrator user not found in the database.');
            return 1;
        }

        // Get the email from argument or prompt the user
        $newEmail = $this->argument('email') ?? $this->ask('Enter the new administrator email address');

        // Validate the email format
        if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
            $this->error('❌ Invalid email format. Please provide a valid email address.');
            return 1;
        }

        // Check if the email is already in use by another user
        $existingUser = User::where('email', $newEmail)
            ->where('id', '!=', $admin->id)
            ->first();

        if ($existingUser) {
            $this->error("❌ The email '{$newEmail}' is already in use by another user.");
            return 1;
        }

        // Store the old email for display
        $oldEmail = $admin->email;

        // Update the administrator email
        $admin->update(['email' => $newEmail]);

        // Display success message
        $this->info("✅ Administrator email successfully updated!");
        $this->line("   Old email: <fg=red>{$oldEmail}</>");
        $this->line("   New email: <fg=green>{$newEmail}</>");

        return 0;
    }
}
