<?php

namespace App\Listeners;

use App\Events\StoreOwnerRegistered;
use App\Mail\StoreOwnerRegistrationNotification;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotifyAdminOfStoreOwnerRegistration implements ShouldQueue
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
    public function handle(StoreOwnerRegistered $event): void
    {
        // Find the admin user
        $admin = User::where('user_type', 'administrador')->first();

        if (!$admin) {
            Log::error('Admin user not found for store owner registration notification');
            return;
        }

        try {
            // Send notification to admin
            Mail::to($admin->email)->send(
                new StoreOwnerRegistrationNotification($event->user)
            );

            Log::info('Store owner registration notification sent to admin', [
                'admin_email' => $admin->email,
                'store_owner_id' => $event->user->id,
                'store_owner_email' => $event->user->email,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send store owner registration notification', [
                'error' => $e->getMessage(),
                'store_owner_id' => $event->user->id,
            ]);
        }
    }
}
