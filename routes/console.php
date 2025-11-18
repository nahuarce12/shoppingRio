<?php

use App\Jobs\CleanupExpiredNewsJob;
use App\Jobs\EvaluateClientCategoriesJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
 * Register scheduled tasks for ShoppingRio application.
 * 
 * - EvaluateClientCategoriesJob: Runs monthly to evaluate client category upgrades
 * - CleanupExpiredNewsJob: Runs daily at midnight to cleanup expired news
 */

// Evaluate client categories monthly (1st day of each month at 2 AM)
if (config('shopping.scheduled_jobs.category_evaluation.enabled', true)) {
    Schedule::job(new EvaluateClientCategoriesJob())
        ->monthly()
        ->at('02:00')
        ->name('evaluate-client-categories')
        ->withoutOverlapping()
        ->onOneServer();
}

// Cleanup expired news daily at midnight
if (config('shopping.scheduled_jobs.news_cleanup.enabled', true)) {
    Schedule::job(new CleanupExpiredNewsJob())
        ->daily()
        ->at('00:00')
        ->name('cleanup-expired-news')
        ->withoutOverlapping()
        ->onOneServer();
}
