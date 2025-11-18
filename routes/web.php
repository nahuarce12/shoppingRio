<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\Admin\StoreController;
use App\Http\Controllers\Admin\PromotionApprovalController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UserApprovalController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Store\PromotionController as StorePromotionController;
use App\Http\Controllers\Store\PromotionUsageController as StorePromotionUsageController;
use App\Http\Controllers\Store\DashboardController as StoreDashboardController;
use App\Http\Controllers\Client\PromotionController as ClientPromotionController;
use App\Http\Controllers\Client\PromotionUsageController as ClientPromotionUsageController;
use App\Http\Controllers\Client\DashboardController as ClientDashboardController;

/*
|--------------------------------------------------------------------------
| Public Routes (Unregistered Users)
|--------------------------------------------------------------------------
| Accessible by all users without authentication
*/

// Home
Route::get('/', [PublicController::class, 'home'])->name('home');

// Stores (Locales)
Route::prefix('locales')->name('locales.')->group(function () {
    Route::get('/', [PublicController::class, 'storesIndex'])->name('index');
    Route::get('/{store}', [PublicController::class, 'storeShow'])->name('show');
});

// Promotions (Promociones)
Route::prefix('promociones')->name('promociones.')->group(function () {
    Route::get('/', [PublicController::class, 'promotionsIndex'])->name('index');
    Route::get('/{promotion}', [PublicController::class, 'promotionShow'])->name('show');
});

// News (Novedades)
Route::prefix('novedades')->name('novedades.')->group(function () {
    Route::get('/', [PublicController::class, 'newsIndex'])->name('index');
});

// Static Pages
Route::get('/quienes-somos', [PublicController::class, 'about'])->name('about');
Route::get('/contacto', [PublicController::class, 'contact'])->name('contact');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
| Handled by Laravel Fortify/Breeze - include auth.php
*/

require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| Administrator Routes
|--------------------------------------------------------------------------
| Protected by auth + admin middleware
*/

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Stores Management
    Route::resource('stores', StoreController::class);
    
    // User Approval (Store Owners)
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/pending', [UserApprovalController::class, 'index'])->name('pending');
        Route::post('/{user}/approve', [UserApprovalController::class, 'approve'])->name('approve');
        Route::post('/{user}/reject', [UserApprovalController::class, 'reject'])->name('reject');
    });
    
    // Promotion Approval
    Route::prefix('promotions')->name('promotions.')->group(function () {
        Route::get('/pending', [PromotionApprovalController::class, 'index'])->name('pending');
        Route::get('/{promotion}', [PromotionApprovalController::class, 'show'])->name('show');
        Route::post('/{promotion}/approve', [PromotionApprovalController::class, 'approve'])->name('approve');
        Route::post('/{promotion}/deny', [PromotionApprovalController::class, 'deny'])->name('deny');
    });
    
    // News Management
    Route::resource('news', NewsController::class);
    Route::get('/news-expired', [NewsController::class, 'expired'])->name('news.expired');
    
    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/promotion-usage', [ReportController::class, 'promotionUsage'])->name('promotion-usage');
        Route::get('/store-performance', [ReportController::class, 'storePerformance'])->name('store-performance');
        Route::get('/client-distribution', [ReportController::class, 'clientDistribution'])->name('client-distribution');
        Route::get('/popular-promotions', [ReportController::class, 'popularPromotions'])->name('popular-promotions');
        Route::get('/client-activity', [ReportController::class, 'clientActivity'])->name('client-activity');
        Route::get('/pending-approvals', [ReportController::class, 'pendingApprovals'])->name('pending-approvals');
        Route::get('/export-csv', [ReportController::class, 'exportCSV'])->name('export-csv');
    });
});

/*
|--------------------------------------------------------------------------
| Store Owner Routes
|--------------------------------------------------------------------------
| Protected by auth + store.owner middleware + store ownership verification
*/

Route::middleware(['auth', 'store.owner'])->prefix('store')->name('store.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [StoreDashboardController::class, 'index'])->name('dashboard');
    
    // Promotions Management (Create, Delete only - no edit per business rules)
    Route::resource('promotions', StorePromotionController::class)
        ->only(['index', 'create', 'store', 'show', 'destroy']);
    
    // Promotion Usage Requests
    Route::prefix('promotion-usages')->name('promotion-usages.')->group(function () {
        Route::post('/{promotionUsage}/accept', [StorePromotionUsageController::class, 'accept'])->name('accept');
        Route::post('/{promotionUsage}/reject', [StorePromotionUsageController::class, 'reject'])->name('reject');
    });
});

/*
|--------------------------------------------------------------------------
| Client Routes
|--------------------------------------------------------------------------
| Protected by auth + verified middleware
*/

Route::middleware(['auth', 'verified'])->prefix('client')->name('client.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [ClientDashboardController::class, 'index'])->name('dashboard');
    
    // Browse Promotions
    Route::prefix('promotions')->name('promotions.')->group(function () {
        Route::get('/', [ClientPromotionController::class, 'index'])->name('index');
        Route::get('/{promotion}', [ClientPromotionController::class, 'show'])->name('show');
    });
    
    // Promotion Usage Requests
    Route::prefix('promotion-usages')->name('promotion-usages.')->group(function () {
        Route::get('/', [ClientPromotionUsageController::class, 'index'])->name('index');
        Route::post('/request', [ClientPromotionUsageController::class, 'request'])->name('request');
    });
});
