<?php

namespace App\Console\Commands;

use App\Mail\StoreOwnerRegistrationNotification;
use App\Mail\PromotionCreatedNotification;
use App\Models\User;
use App\Models\Promotion;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmailNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test-notifications {type? : Type of notification (store-owner|promotion|all)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email notifications for admin';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $type = $this->argument('type') ?? 'all';
        $admin = User::where('user_type', 'administrador')->first();

        if (!$admin) {
            $this->error('❌ Administrator user not found.');
            return 1;
        }

        $this->info("🧪 Testing Email Notifications for Admin: {$admin->email}");

        // Test Store Owner Registration Notification
        if ($type === 'store-owner' || $type === 'all') {
            $this->line("\n📧 Sending Store Owner Registration Notification...");
            try {
                $storeOwner = User::where('user_type', 'dueño de local')->first();
                if ($storeOwner) {
                    Mail::to($admin->email)->send(
                        new StoreOwnerRegistrationNotification($storeOwner)
                    );
                    $this->info("✅ Store Owner notification sent successfully!");
                } else {
                    $this->warn("⚠️  No store owner found to test with");
                }
            } catch (\Exception $e) {
                $this->error("❌ Error: {$e->getMessage()}");
                return 1;
            }
        }

        // Test Promotion Created Notification
        if ($type === 'promotion' || $type === 'all') {
            $this->line("\n📧 Sending Promotion Created Notification...");
            try {
                $promotion = Promotion::with('store')->first();
                if ($promotion) {
                    Mail::to($admin->email)->send(
                        new PromotionCreatedNotification($promotion)
                    );
                    $this->info("✅ Promotion notification sent successfully!");
                } else {
                    $this->warn("⚠️  No promotion found to test with");
                }
            } catch (\Exception $e) {
                $this->error("❌ Error: {$e->getMessage()}");
                return 1;
            }
        }

        $this->line("\n✅ All tests completed!");
        return 0;
    }
}
