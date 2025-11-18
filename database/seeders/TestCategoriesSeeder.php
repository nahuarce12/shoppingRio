<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Store;
use App\Models\Promotion;
use App\Models\PromotionUsage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class TestCategoriesSeeder extends Seeder
{
    /**
     * Seed specific test cases for category upgrade logic.
     * 
     * This seeder creates edge case scenarios to test the CategoryUpgradeService:
     * - Clients with exactly threshold counts
     * - Clients at edge of 6-month window
     * - Clients with mixed usage patterns
     */
    public function run(): void
    {
        $this->command->info('🧪 Creating test cases for category upgrade logic...');

        // Ensure we have at least one store and multiple promotions
        $store = Store::first();
        if (!$store) {
            $owner = User::factory()->storeOwner()->create([
                'name' => 'Test Owner',
                'email' => 'testowner@shoppingrio.com',
            ]);
            $store = Store::factory()->forOwner($owner)->create();
        }

        // Get or create multiple approved promotions for testing (we need at least 25 for test case 8)
        $promotions = Promotion::where('status', 'aprobada')->get();
        if ($promotions->count() < 25) {
            for ($i = $promotions->count(); $i < 25; $i++) {
                $promotions->push(
                    Promotion::factory()
                        ->forStore($store)
                        ->approved()
                        ->inicial()
                        ->create()
                );
            }
        }

        // ========================================
        // Test Case 1: Client at exact threshold for Initial->Medium
        // ========================================
        $this->command->info('  📌 Test Case 1: Client at Initial->Medium threshold (5 usages)');
        
        $testClient1 = User::factory()->inicial()->create([
            'name' => 'Test Threshold Initial',
            'email' => 'test.threshold.initial@example.com',
            'password' => Hash::make('password'),
        ]);

        // Create exactly 5 accepted usages within last 6 months
        for ($i = 0; $i < 5; $i++) {
            PromotionUsage::factory()
                ->forClient($testClient1)
                ->forPromotion($promotions[$i])
                ->accepted()
                ->onDate(Carbon::now()->subMonths(3)->addDays($i))
                ->create();
        }

        // ========================================
        // Test Case 2: Client at exact threshold for Medium->Premium
        // ========================================
        $this->command->info('  📌 Test Case 2: Client at Medium->Premium threshold (15 usages)');
        
        $testClient2 = User::factory()->medium()->create([
            'name' => 'Test Threshold Medium',
            'email' => 'test.threshold.medium@example.com',
            'password' => Hash::make('password'),
        ]);

        // Create exactly 15 accepted usages within last 6 months
        for ($i = 0; $i < 15; $i++) {
            PromotionUsage::factory()
                ->forClient($testClient2)
                ->forPromotion($promotions[$i])
                ->accepted()
                ->onDate(Carbon::now()->subMonths(4)->addDays($i * 5))
                ->create();
        }

        // ========================================
        // Test Case 3: Client just below threshold (4 usages)
        // ========================================
        $this->command->info('  📌 Test Case 3: Client just below Initial->Medium threshold (4 usages)');
        
        $testClient3 = User::factory()->inicial()->create([
            'name' => 'Test Below Threshold',
            'email' => 'test.below.threshold@example.com',
            'password' => Hash::make('password'),
        ]);

        // Create 4 accepted usages (1 short of threshold)
        for ($i = 0; $i < 4; $i++) {
            PromotionUsage::factory()
                ->forClient($testClient3)
                ->forPromotion($promotions[$i])
                ->accepted()
                ->onDate(Carbon::now()->subMonths(2)->addDays($i))
                ->create();
        }

        // ========================================
        // Test Case 4: Client at edge of 6-month window
        // ========================================
        $this->command->info('  📌 Test Case 4: Client with usages at 6-month boundary');
        
        $testClient4 = User::factory()->inicial()->create([
            'name' => 'Test Window Edge',
            'email' => 'test.window.edge@example.com',
            'password' => Hash::make('password'),
        ]);

        // 3 usages within 6 months
        for ($i = 0; $i < 3; $i++) {
            PromotionUsage::factory()
                ->forClient($testClient4)
                ->forPromotion($promotions[$i])
                ->accepted()
                ->onDate(Carbon::now()->subMonths(5)->addDays($i * 10))
                ->create();
        }

        // 5 usages outside 6-month window (should not count)
        for ($i = 3; $i < 8; $i++) {
            PromotionUsage::factory()
                ->forClient($testClient4)
                ->forPromotion($promotions[$i])
                ->accepted()
                ->onDate(Carbon::now()->subMonths(7)->addDays($i))
                ->create();
        }

        // ========================================
        // Test Case 5: Client with rejected usages (should not count)
        // ========================================
        $this->command->info('  📌 Test Case 5: Client with mixed accepted/rejected usages');
        
        $testClient5 = User::factory()->inicial()->create([
            'name' => 'Test Rejected Usages',
            'email' => 'test.rejected.usages@example.com',
            'password' => Hash::make('password'),
        ]);

        // 3 accepted usages
        for ($i = 0; $i < 3; $i++) {
            PromotionUsage::factory()
                ->forClient($testClient5)
                ->forPromotion($promotions[$i])
                ->accepted()
                ->onDate(Carbon::now()->subMonths(2)->addDays($i))
                ->create();
        }

        // 5 rejected usages (should not count towards upgrade)
        for ($i = 3; $i < 8; $i++) {
            PromotionUsage::factory()
                ->forClient($testClient5)
                ->forPromotion($promotions[$i])
                ->rejected()
                ->onDate(Carbon::now()->subMonths(2)->addDays($i + 10))
                ->create();
        }

        // ========================================
        // Test Case 6: Client with pending usages (should not count)
        // ========================================
        $this->command->info('  📌 Test Case 6: Client with pending (enviada) usages');
        
        $testClient6 = User::factory()->inicial()->create([
            'name' => 'Test Pending Usages',
            'email' => 'test.pending.usages@example.com',
            'password' => Hash::make('password'),
        ]);

        // 2 accepted usages
        for ($i = 0; $i < 2; $i++) {
            PromotionUsage::factory()
                ->forClient($testClient6)
                ->forPromotion($promotions[$i])
                ->accepted()
                ->onDate(Carbon::now()->subMonths(1)->addDays($i))
                ->create();
        }

        // 4 pending usages (should not count towards upgrade)
        for ($i = 2; $i < 6; $i++) {
            PromotionUsage::factory()
                ->forClient($testClient6)
                ->forPromotion($promotions[$i])
                ->sent()
                ->onDate(Carbon::now()->subDays($i + 1))
                ->create();
        }

        // ========================================
        // Test Case 7: Client who should upgrade (over threshold)
        // ========================================
        $this->command->info('  📌 Test Case 7: Client ready for Initial->Medium upgrade (7 usages)');
        
        $testClient7 = User::factory()->inicial()->create([
            'name' => 'Test Ready Upgrade',
            'email' => 'test.ready.upgrade@example.com',
            'password' => Hash::make('password'),
        ]);

        // Create 7 accepted usages (above threshold of 5)
        for ($i = 0; $i < 7; $i++) {
            PromotionUsage::factory()
                ->forClient($testClient7)
                ->forPromotion($promotions[$i])
                ->accepted()
                ->onDate(Carbon::now()->subMonths(3)->addDays($i * 7))
                ->create();
        }

        // ========================================
        // Test Case 8: Premium client (should stay Premium)
        // ========================================
        $this->command->info('  📌 Test Case 8: Premium client with many usages (stays Premium)');
        
        $testClient8 = User::factory()->premium()->create([
            'name' => 'Test Stay Premium',
            'email' => 'test.stay.premium@example.com',
            'password' => Hash::make('password'),
        ]);

        // Create 25 accepted usages (way above all thresholds)
        for ($i = 0; $i < 25; $i++) {
            PromotionUsage::factory()
                ->forClient($testClient8)
                ->forPromotion($promotions[$i])
                ->accepted()
                ->onDate(Carbon::now()->subMonths(5)->addDays($i * 5))
                ->create();
        }

        // ========================================
        // Summary
        // ========================================
        $this->command->newLine();
        $this->command->info('✅ Test cases created successfully!');
        $this->command->newLine();
        $this->command->table(
            ['Test Case', 'Email', 'Current Category', 'Expected Outcome'],
            [
                ['1', 'test.threshold.initial@example.com', 'Inicial', 'Upgrade to Medium (5 usages)'],
                ['2', 'test.threshold.medium@example.com', 'Medium', 'Upgrade to Premium (15 usages)'],
                ['3', 'test.below.threshold@example.com', 'Inicial', 'Stay Inicial (4 < 5)'],
                ['4', 'test.window.edge@example.com', 'Inicial', 'Stay Inicial (3 in window, 5 outside)'],
                ['5', 'test.rejected.usages@example.com', 'Inicial', 'Stay Inicial (only 3 accepted)'],
                ['6', 'test.pending.usages@example.com', 'Inicial', 'Stay Inicial (only 2 accepted)'],
                ['7', 'test.ready.upgrade@example.com', 'Inicial', 'Upgrade to Medium (7 > 5)'],
                ['8', 'test.stay.premium@example.com', 'Premium', 'Stay Premium (max category)'],
            ]
        );

        $this->command->newLine();
        $this->command->info('💡 To test category upgrade logic, run:');
        $this->command->info('   php artisan categories:evaluate');
        $this->command->newLine();
        $this->command->info('📧 All test users have password: password');
    }
}
