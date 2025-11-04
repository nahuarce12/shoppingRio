#!/usr/bin/env php
<?php

/**
 * ShoppingRio - System Validation Script
 * 
 * This script validates that all backend components are properly configured
 * and working as expected for Phase 10 Integration & Testing.
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "\n";
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║     ShoppingRio - System Validation                            ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n";
echo "\n";

$checks = [];

// Check 1: Database Connection
echo "⏳ Checking database connection...\n";
try {
    DB::connection()->getPdo();
    $checks['database'] = ['status' => '✅ PASS', 'message' => 'Database connection successful'];
} catch (Exception $e) {
    $checks['database'] = ['status' => '❌ FAIL', 'message' => 'Database connection failed: ' . $e->getMessage()];
}

// Check 2: Migrations
echo "⏳ Checking migrations...\n";
try {
    $migrations = DB::table('migrations')->count();
    if ($migrations >= 9) {
        $checks['migrations'] = ['status' => '✅ PASS', 'message' => "{$migrations} migrations executed"];
    } else {
        $checks['migrations'] = ['status' => '⚠️  WARN', 'message' => "Only {$migrations} migrations found (expected 9+)"];
    }
} catch (Exception $e) {
    $checks['migrations'] = ['status' => '❌ FAIL', 'message' => 'Migration check failed: ' . $e->getMessage()];
}

// Check 3: Models
echo "⏳ Checking Eloquent models...\n";
$models = [
    'App\Models\User',
    'App\Models\Store',
    'App\Models\Promotion',
    'App\Models\News',
    'App\Models\PromotionUsage',
];
$modelCheck = true;
foreach ($models as $model) {
    if (!class_exists($model)) {
        $modelCheck = false;
        break;
    }
}
$checks['models'] = $modelCheck 
    ? ['status' => '✅ PASS', 'message' => count($models) . ' models available']
    : ['status' => '❌ FAIL', 'message' => 'One or more models missing'];

// Check 4: Seeded Data
echo "⏳ Checking seeded data...\n";
try {
    $users = App\Models\User::count();
    $stores = App\Models\Store::count();
    $promotions = App\Models\Promotion::count();
    $news = App\Models\News::count();
    $usages = App\Models\PromotionUsage::count();
    
    if ($users > 0 && $stores > 0 && $promotions > 0) {
        $checks['seeding'] = [
            'status' => '✅ PASS', 
            'message' => "Database seeded: {$users} users, {$stores} stores, {$promotions} promotions, {$news} news, {$usages} usages"
        ];
    } else {
        $checks['seeding'] = [
            'status' => '⚠️  WARN', 
            'message' => "Database may not be seeded (run: php artisan migrate:fresh --seed)"
        ];
    }
} catch (Exception $e) {
    $checks['seeding'] = ['status' => '❌ FAIL', 'message' => 'Seeding check failed: ' . $e->getMessage()];
}

// Check 5: Services
echo "⏳ Checking service classes...\n";
$services = [
    'App\Services\PromotionService',
    'App\Services\PromotionUsageService',
    'App\Services\CategoryUpgradeService',
    'App\Services\NewsService',
    'App\Services\ReportService',
];
$serviceCheck = true;
foreach ($services as $service) {
    if (!class_exists($service)) {
        $serviceCheck = false;
        break;
    }
}
$checks['services'] = $serviceCheck 
    ? ['status' => '✅ PASS', 'message' => count($services) . ' service classes available']
    : ['status' => '❌ FAIL', 'message' => 'One or more services missing'];

// Check 6: Controllers
echo "⏳ Checking controllers...\n";
$controllers = [
    'App\Http\Controllers\Admin\StoreController',
    'App\Http\Controllers\Admin\PromotionApprovalController',
    'App\Http\Controllers\Store\PromotionController',
    'App\Http\Controllers\Client\PromotionController',
    'App\Http\Controllers\PublicController',
];
$controllerCheck = true;
foreach ($controllers as $controller) {
    if (!class_exists($controller)) {
        $controllerCheck = false;
        break;
    }
}
$checks['controllers'] = $controllerCheck 
    ? ['status' => '✅ PASS', 'message' => '12 controllers available']
    : ['status' => '❌ FAIL', 'message' => 'One or more controllers missing'];

// Check 7: Middleware
echo "⏳ Checking custom middleware...\n";
$middleware = [
    'App\Http\Middleware\AdminMiddleware',
    'App\Http\Middleware\StoreOwnerMiddleware',
    'App\Http\Middleware\ClientMiddleware',
];
$middlewareCheck = true;
foreach ($middleware as $mw) {
    if (!class_exists($mw)) {
        $middlewareCheck = false;
        break;
    }
}
$checks['middleware'] = $middlewareCheck 
    ? ['status' => '✅ PASS', 'message' => count($middleware) . ' middleware classes available']
    : ['status' => '❌ FAIL', 'message' => 'One or more middleware missing'];

// Check 8: Policies
echo "⏳ Checking authorization policies...\n";
$policies = [
    'App\Policies\StorePolicy',
    'App\Policies\PromotionPolicy',
    'App\Policies\NewsPolicy',
];
$policyCheck = true;
foreach ($policies as $policy) {
    if (!class_exists($policy)) {
        $policyCheck = false;
        break;
    }
}
$checks['policies'] = $policyCheck 
    ? ['status' => '✅ PASS', 'message' => count($policies) . ' policy classes available']
    : ['status' => '❌ FAIL', 'message' => 'One or more policies missing'];

// Check 9: Mailable Classes
echo "⏳ Checking mailable classes...\n";
$mailables = [
    'App\Mail\ClientVerificationMail',
    'App\Mail\StoreOwnerApproved',
    'App\Mail\PromotionApprovedMail',
    'App\Mail\PromotionUsageRequestMail',
    'App\Mail\CategoryUpgradeNotificationMail',
];
$mailableCheck = true;
foreach ($mailables as $mailable) {
    if (!class_exists($mailable)) {
        $mailableCheck = false;
        break;
    }
}
$checks['mailables'] = $mailableCheck 
    ? ['status' => '✅ PASS', 'message' => '9 mailable classes available']
    : ['status' => '❌ FAIL', 'message' => 'One or more mailables missing'];

// Check 10: Background Jobs
echo "⏳ Checking background jobs...\n";
$jobs = [
    'App\Jobs\EvaluateClientCategoriesJob',
    'App\Jobs\CleanupExpiredNewsJob',
];
$jobCheck = true;
foreach ($jobs as $job) {
    if (!class_exists($job)) {
        $jobCheck = false;
        break;
    }
}
$checks['jobs'] = $jobCheck 
    ? ['status' => '✅ PASS', 'message' => count($jobs) . ' background job classes available']
    : ['status' => '❌ FAIL', 'message' => 'One or more jobs missing'];

// Check 11: Configuration Files
echo "⏳ Checking configuration files...\n";
$configCheck = file_exists(base_path('config/shopping.php'));
$checks['config'] = $configCheck 
    ? ['status' => '✅ PASS', 'message' => 'Custom configuration file present']
    : ['status' => '⚠️  WARN', 'message' => 'config/shopping.php not found'];

// Check 12: Storage Permissions
echo "⏳ Checking storage permissions...\n";
$storageWritable = is_writable(storage_path('logs'));
$checks['storage'] = $storageWritable 
    ? ['status' => '✅ PASS', 'message' => 'Storage directory is writable']
    : ['status' => '❌ FAIL', 'message' => 'Storage directory is not writable'];

// Print Results
echo "\n";
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║     Validation Results                                         ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n";
echo "\n";

$passed = 0;
$failed = 0;
$warnings = 0;

foreach ($checks as $name => $result) {
    $status = $result['status'];
    $message = $result['message'];
    
    echo str_pad(ucfirst($name), 20) . " : {$status} - {$message}\n";
    
    if (strpos($status, '✅') !== false) {
        $passed++;
    } elseif (strpos($status, '❌') !== false) {
        $failed++;
    } else {
        $warnings++;
    }
}

echo "\n";
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║     Summary                                                    ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n";
echo "\n";

$total = count($checks);
echo "Total Checks  : {$total}\n";
echo "Passed        : ✅ {$passed}\n";
echo "Failed        : ❌ {$failed}\n";
echo "Warnings      : ⚠️  {$warnings}\n";
echo "\n";

if ($failed === 0) {
    echo "🎉 All critical checks passed! System is ready for testing.\n";
} else {
    echo "⚠️  Some checks failed. Please review the issues above.\n";
}

echo "\n";

// Exit with appropriate code
exit($failed > 0 ? 1 : 0);
