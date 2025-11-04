# Preparation Script for Manual E2E Testing
# ShoppingRio - Phase 10 Testing
# Date: November 3, 2025

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  ShoppingRio - E2E Testing Setup" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# 1. Check if XAMPP services are running
Write-Host "[1/6] Checking XAMPP services..." -ForegroundColor Yellow
$apacheRunning = Get-Process -Name "httpd" -ErrorAction SilentlyContinue
$mysqlRunning = Get-Process -Name "mysqld" -ErrorAction SilentlyContinue

if ($apacheRunning) {
    Write-Host "  OK Apache is running" -ForegroundColor Green
} else {
    Write-Host "  ERROR Apache is NOT running" -ForegroundColor Red
    Write-Host "    Please start Apache from XAMPP Control Panel" -ForegroundColor Yellow
    exit 1
}

if ($mysqlRunning) {
    Write-Host "  OK MySQL is running" -ForegroundColor Green
} else {
    Write-Host "  ERROR MySQL is NOT running" -ForegroundColor Red
    Write-Host "    Please start MySQL from XAMPP Control Panel" -ForegroundColor Yellow
    exit 1
}

Write-Host ""

# 2. Check .env configuration
Write-Host "[2/6] Checking .env configuration..." -ForegroundColor Yellow
if (Test-Path ".env") {
    Write-Host "  OK .env file exists" -ForegroundColor Green
    
    # Check database configuration
    $envContent = Get-Content ".env" -Raw
    if ($envContent -match "DB_DATABASE=") {
        Write-Host "  OK Database configuration found" -ForegroundColor Green
    } else {
        Write-Host "  ERROR Database configuration missing" -ForegroundColor Red
        exit 1
    }
    
    # Check mail configuration
    if ($envContent -match "MAIL_MAILER=") {
        Write-Host "  OK Mail configuration found" -ForegroundColor Green
    } else {
        Write-Host "  WARNING Mail configuration missing" -ForegroundColor Yellow
    }
} else {
    Write-Host "  ERROR .env file not found" -ForegroundColor Red
    Write-Host "    Please copy .env.example to .env and configure it" -ForegroundColor Yellow
    exit 1
}

Write-Host ""

# 3. Clear caches
Write-Host "[3/6] Clearing application caches..." -ForegroundColor Yellow
php artisan config:clear | Out-Null
php artisan cache:clear 2>&1 | Out-Null
php artisan view:clear | Out-Null
php artisan route:clear | Out-Null
Write-Host "  OK Caches cleared successfully" -ForegroundColor Green

Write-Host ""

# 4. Run migrations with fresh seeding
Write-Host "[4/6] Setting up database with fresh data..." -ForegroundColor Yellow
Write-Host "    This will DROP all tables and recreate them with seed data" -ForegroundColor Yellow
$confirm = Read-Host "    Continue? (yes/no)"

if ($confirm -eq "yes") {
    php artisan migrate:fresh --seed
    Write-Host "  OK Database setup complete" -ForegroundColor Green
} else {
    Write-Host "  SKIPPED Database setup skipped" -ForegroundColor Yellow
}

Write-Host ""

# 5. Cache routes
Write-Host "[5/6] Caching routes for performance..." -ForegroundColor Yellow
php artisan route:cache | Out-Null
Write-Host "  OK Routes cached successfully" -ForegroundColor Green

Write-Host ""

# 6. Display seed data credentials
Write-Host "[6/6] Test Credentials (from DatabaseSeeder):" -ForegroundColor Yellow
Write-Host ""
Write-Host "  ADMINISTRATOR:" -ForegroundColor Cyan
Write-Host "    Email: admin@shoppingrio.com" -ForegroundColor White
Write-Host "    Password: Admin123!" -ForegroundColor White
Write-Host ""
Write-Host "  STORE OWNERS (5 approved):" -ForegroundColor Cyan
Write-Host "    Email: owner1@tiendamoda.com (Tienda de Moda)" -ForegroundColor White
Write-Host "    Email: owner2@techstore.com (Tech Store)" -ForegroundColor White
Write-Host "    Email: owner3@cafedelcentro.com (Cafe del Centro)" -ForegroundColor White
Write-Host "    Email: owner4@deportesmax.com (Deportes Max)" -ForegroundColor White
Write-Host "    Email: owner5@hogarydeco.com (Hogar y Deco)" -ForegroundColor White
Write-Host "    Password: password (for all)" -ForegroundColor White
Write-Host ""
Write-Host "  CLIENTS:" -ForegroundColor Cyan
Write-Host "    Inicial (10): client1-10@example.com" -ForegroundColor White
Write-Host "    Medium (10): client11-20@example.com" -ForegroundColor White
Write-Host "    Premium (10): client21-30@example.com" -ForegroundColor White
Write-Host "    Password: password (for all)" -ForegroundColor White
Write-Host ""

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Setup Complete! Ready for Testing" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Access the application at:" -ForegroundColor Yellow
Write-Host "  http://localhost/shoppingRio/public" -ForegroundColor White
Write-Host ""
Write-Host "To start the queue worker (for emails):" -ForegroundColor Yellow
Write-Host "  php artisan queue:work" -ForegroundColor White
Write-Host ""
Write-Host "Testing checklist: TESTING-CHECKLIST.md" -ForegroundColor Yellow
Write-Host ""
