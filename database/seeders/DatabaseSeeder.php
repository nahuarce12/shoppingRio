<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Store;
use App\Models\Promotion;
use App\Models\News;
use App\Models\PromotionUsage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Truncate tables to ensure clean seeding
        User::truncate();
        Store::truncate();
        Promotion::truncate();
        News::truncate();
        PromotionUsage::truncate();
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('🚀 Starting database seeding...');

        // ========================================
        // 1. Create Administrator
        // ========================================
        $this->command->info('📋 Creating administrator...');
        
        $admin = User::factory()->admin()->create([
            'name' => 'Administrator',
            'email' => 'admin@shoppingrio.com',
            'password' => Hash::make('Admin123!'),
        ]);

        $this->command->info("✅ Admin created: {$admin->email}");

        // ========================================
        // 2. Create Stores FIRST (before owners)
        // ========================================
        $this->command->info('📋 Creating stores...');
        
        $stores = Store::factory()->count(20)->create();

        $this->command->info("✅ Created {$stores->count()} stores");

        // ========================================
        // 3. Create Store Owners and assign them to stores
        // ========================================
        $this->command->info('📋 Creating store owners...');
        
        // 3 approved store owners - assign to first 3 stores
        $approvedOwners = collect();
        foreach ($stores->take(3) as $index => $store) {
            $owner = User::factory()
                ->storeOwner()
                ->create(['store_id' => $store->id]);
            $approvedOwners->push($owner);
        }

        // 2 pending store owners (awaiting admin approval) - assign to next 2 stores
        $pendingOwners = collect();
        foreach ($stores->slice(3, 2) as $store) {
            $owner = User::factory()
                ->pendingStoreOwner()
                ->create(['store_id' => $store->id]);
            $pendingOwners->push($owner);
        }

        $this->command->info("✅ Created 3 approved and 2 pending store owners");

        // ========================================
        // 4. Create Promotions
        // ========================================
        $this->command->info('📋 Creating promotions...');
        
        $promotions = collect();
        
        foreach ($stores as $store) {
            // Each store has 2-3 promotions
            $promoCount = fake()->numberBetween(2, 3);
            
            $storePromotions = Promotion::factory()
                ->count($promoCount)
                ->forStore($store)
                ->create();
            
            $promotions = $promotions->merge($storePromotions);
        }

        // Set promotion states: 30 approved, 10 pending, 10 denied
        $allPromotions = $promotions->shuffle();
        
        $allPromotions->take(30)->each(fn($promo) => $promo->update(['estado' => 'aprobada']));
        $allPromotions->slice(30, 10)->each(fn($promo) => $promo->update(['estado' => 'pendiente']));
        $allPromotions->slice(40, 10)->each(fn($promo) => $promo->update(['estado' => 'denegada']));

        $this->command->info("✅ Created {$promotions->count()} promotions (30 approved, 10 pending, 10 denied)");

        // ========================================
        // 5. Create Clients
        // ========================================
        $this->command->info('📋 Creating clients...');
        
        // 10 clients per category (30 total)
        $inicialClients = User::factory()->count(10)->inicial()->create();
        $mediumClients = User::factory()->count(10)->medium()->create();
        $premiumClients = User::factory()->count(10)->premium()->create();

        $allClients = $inicialClients->merge($mediumClients)->merge($premiumClients);

        $this->command->info("✅ Created 30 clients (10 Initial, 10 Medium, 10 Premium)");

        // ========================================
        // 6. Create News
        // ========================================
        $this->command->info('📋 Creating news...');
        
        // 15 news items distributed across categories
        $newsItems = collect();
        
        // 5 for each category
        $newsItems = $newsItems->merge(News::factory()->count(5)->inicial()->active()->create());
        $newsItems = $newsItems->merge(News::factory()->count(5)->medium()->active()->create());
        $newsItems = $newsItems->merge(News::factory()->count(5)->premium()->active()->create());

        // Add some expired news
        News::factory()->count(3)->expired()->create();

        $this->command->info("✅ Created 15 active news items (5 per category) + 3 expired");

        // ========================================
        // 7. Create Promotion Usages
        // ========================================
        $this->command->info('📋 Creating promotion usages...');
        
        $approvedPromotions = Promotion::where('estado', 'aprobada')->get();
        $usageCount = 0;

        // Create realistic usage patterns
        foreach ($allClients as $client) {
            // Each client has used 1-5 promotions
            $usagesToCreate = fake()->numberBetween(1, 5);
            
            // Get eligible promotions for this client's category
            $eligiblePromotions = $approvedPromotions->filter(function ($promo) use ($client) {
                $categoryHierarchy = ['Inicial' => 0, 'Medium' => 1, 'Premium' => 2];
                return $categoryHierarchy[$promo->categoria_minima] <= $categoryHierarchy[$client->categoria_cliente];
            });

            if ($eligiblePromotions->isEmpty()) {
                continue;
            }

            // Random selection of promotions (no duplicates per client - single use rule)
            $selectedPromotions = $eligiblePromotions->random(min($usagesToCreate, $eligiblePromotions->count()));

            foreach ($selectedPromotions as $promotion) {
                $estado = fake()->randomElement(['aceptada', 'aceptada', 'aceptada', 'enviada', 'rechazada']);
                
                $usage = PromotionUsage::factory()
                    ->forClient($client)
                    ->forPromotion($promotion)
                    ->recentSixMonths();

                if ($estado === 'rechazada') {
                    $usage = $usage->rejected();
                } elseif ($estado === 'aceptada') {
                    $usage = $usage->accepted();
                } else {
                    $usage = $usage->sent();
                }

                $usage->create();
                $usageCount++;
            }
        }

        $this->command->info("✅ Created {$usageCount} promotion usage records");

        // ========================================
        // Summary
        // ========================================
        $this->command->newLine();
        $this->command->info('🎉 Database seeding completed successfully!');
        $this->command->newLine();
        $this->command->table(
            ['Entity', 'Count'],
            [
                ['Administrators', 1],
                ['Store Owners (Approved)', 3],
                ['Store Owners (Pending)', 2],
                ['Stores', $stores->count()],
                ['Promotions (Approved)', 30],
                ['Promotions (Pending)', 10],
                ['Promotions (Denied)', 10],
                ['Clients (Initial)', 10],
                ['Clients (Medium)', 10],
                ['Clients (Premium)', 10],
                ['News (Active)', 15],
                ['News (Expired)', 3],
                ['Promotion Usages', $usageCount],
            ]
        );

        $this->command->newLine();
        $this->command->info('📧 Default credentials:');
        $this->command->info('   Admin: admin@shoppingrio.com / Admin123!');
        $this->command->info('   All other users: [their email] / password');
    }
}

