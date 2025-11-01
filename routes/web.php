<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LocalController;
use App\Http\Controllers\PromocionController;
use App\Http\Controllers\NovedadController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Store\StoreDashboardController;
use App\Http\Controllers\Client\ClientDashboardController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
| Accessible by all users (registered, unregistered)
*/

// Home
Route::get('/', [HomeController::class, 'index'])->name('home.index');

// Locales (Stores)
Route::prefix('locales')->name('pages.locales')->group(function () {
    Route::get('/', [LocalController::class, 'index'])->name('');
    Route::get('/{id}', [LocalController::class, 'show'])->name('.show');
});

// Promociones (Promotions)
Route::prefix('promociones')->name('pages.promociones')->group(function () {
    Route::get('/', [PromocionController::class, 'index'])->name('');
    Route::get('/{id}', [PromocionController::class, 'show'])->name('.show');
});

// Novedades (News)
Route::get('/novedades', [NovedadController::class, 'index'])->name('pages.novedades');

// Static Pages
Route::get('/quienes-somos', [PageController::class, 'about'])->name('pages.about');
Route::get('/contacto', [PageController::class, 'contact'])->name('pages.contact');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
| Login, Register, Logout - handled by Laravel Breeze or custom auth
*/

// Note: Laravel Breeze auth routes will be loaded via require __DIR__.'/auth.php';
// Aliases for consistency with mockup naming convention
Route::get('/login', function () {
    return redirect()->route('login');
})->name('auth.login');

Route::get('/register', function () {
    return redirect()->route('register');
})->name('auth.register');

/*
|--------------------------------------------------------------------------
| Administrator Routes
|--------------------------------------------------------------------------
| Protected by auth + role middleware
*/

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Store Owner Approval Workflow
    Route::prefix('store-owners')->name('store-owners.')->group(function () {
        Route::get('/pending', [\App\Http\Controllers\Admin\StoreOwnerApprovalController::class, 'index'])->name('pending');
        Route::post('/{user}/approve', [\App\Http\Controllers\Admin\StoreOwnerApprovalController::class, 'approve'])->name('approve');
        Route::post('/{user}/reject', [\App\Http\Controllers\Admin\StoreOwnerApprovalController::class, 'reject'])->name('reject');
    });
    
    // Future: CRUD routes for stores, promotions approval, news management, reports
});

/*
|--------------------------------------------------------------------------
| Store Owner Routes
|--------------------------------------------------------------------------
| Protected by auth + role middleware + store ownership verification
*/

Route::middleware(['auth', 'verified'])->prefix('local')->name('store.')->group(function () {
    Route::get('/dashboard', [StoreDashboardController::class, 'index'])->name('dashboard');
    // Future: CRUD routes for own promotions, discount request handling, reports
});

/*
|--------------------------------------------------------------------------
| Client Routes
|--------------------------------------------------------------------------
| Protected by auth + verified middleware
*/

Route::middleware(['auth', 'verified'])->prefix('cliente')->name('client.')->group(function () {
    Route::get('/mi-cuenta', [ClientDashboardController::class, 'index'])->name('dashboard');
    // Future: view/request promotions, usage history, profile management
});
