<?php

namespace App\Listeners;

use App\Events\PromotionCreated;
use App\Mail\PromotionCreatedNotification;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotifyAdminOfPromotionCreation implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PromotionCreated $event): void
    {
        // Get admin email from config (priority) or database
        $adminEmail = config('shopping.admin_email');
        
        if (!$adminEmail) {
            $admin = User::where('user_type', 'administrador')->first();
            $adminEmail = $admin?->email;
        }

        if (!$adminEmail) {
            Log::error('Admin email not configured for promotion creation notification');
            return;
        }

        try {
            // Send notification to admin
            Mail::to($adminEmail)->send(
                new PromotionCreatedNotification($event->promotion)
            );

            Log::info('Promotion creation notification sent to admin', [
                'admin_email' => $adminEmail,
                'promotion_id' => $event->promotion->id,
                'store_id' => $event->promotion->store_id,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send promotion creation notification', [
                'error' => $e->getMessage(),
                'promotion_id' => $event->promotion->id,
            ]);
        }
    }
}
