<?php

namespace App\Providers;

use App\Events\StoreOwnerRegistered;
use App\Events\PromotionCreated;
use App\Listeners\NotifyAdminOfStoreOwnerRegistration;
use App\Listeners\NotifyAdminOfPromotionCreation;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        StoreOwnerRegistered::class => [
            NotifyAdminOfStoreOwnerRegistration::class,
        ],
        PromotionCreated::class => [
            NotifyAdminOfPromotionCreation::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }
}
