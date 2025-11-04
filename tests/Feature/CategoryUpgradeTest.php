<?php

namespace Tests\Feature;

use App\Jobs\EvaluateClientCategoriesJob;
use App\Mail\CategoryUpgradeNotificationMail;
use App\Models\Promotion;
use App\Models\PromotionUsage;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CategoryUpgradeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test client upgrades from Inicial to Medium
     *
     * @return void
     */
    public function test_client_upgrades_to_medium_after_5_accepted_usages()
    {
        Mail::fake();

        $client = User::factory()->inicial()->create();
        $this->assertEquals('Inicial', $client->categoria_cliente);

        $store = Store::factory()->create();
        $promotions = Promotion::factory()->count(5)->create([
            'store_id' => $store->id,
            'estado' => 'aprobada',
            'fecha_desde' => now()->subMonth(),
            'fecha_hasta' => now()->addMonth(),
        ]);

        // Create 5 accepted usages within last 6 months
        foreach ($promotions as $promotion) {
            PromotionUsage::factory()->create([
                'client_id' => $client->id,
                'promotion_id' => $promotion->id,
                'estado' => 'aceptada',
                'fecha_uso' => now()->subDays(rand(1, 30)),
            ]);
        }

        // Run category evaluation job
        $categoryService = app(\App\Services\CategoryUpgradeService::class);
        $job = new EvaluateClientCategoriesJob();
        $job->handle($categoryService);

        $client->refresh();
        $this->assertEquals('Medium', $client->categoria_cliente);

        // Assert upgrade email was sent
        Mail::assertSent(CategoryUpgradeNotificationMail::class, function ($mail) use ($client) {
            return $mail->client->id === $client->id
                && $mail->oldCategory === 'Inicial'
                && $mail->newCategory === 'Medium';
        });
    }

    /**
     * Test client upgrades from Medium to Premium
     *
     * @return void
     */
    public function test_client_upgrades_to_premium_after_15_accepted_usages()
    {
        Mail::fake();

        $client = User::factory()->medium()->create();
        $this->assertEquals('Medium', $client->categoria_cliente);

        $store = Store::factory()->create();
        $promotions = Promotion::factory()->count(15)->create([
            'store_id' => $store->id,
            'estado' => 'aprobada',
            'fecha_desde' => now()->subMonth(),
            'fecha_hasta' => now()->addMonth(),
        ]);

        // Create 15 accepted usages within last 6 months
        foreach ($promotions as $promotion) {
            PromotionUsage::factory()->create([
                'client_id' => $client->id,
                'promotion_id' => $promotion->id,
                'estado' => 'aceptada',
                'fecha_uso' => now()->subDays(rand(1, 150)),
            ]);
        }

        // Run category evaluation job
        $categoryService = app(\App\Services\CategoryUpgradeService::class);
        $job = new EvaluateClientCategoriesJob();
        
        $job->handle($categoryService);

        $client->refresh();
        $this->assertEquals('Premium', $client->categoria_cliente);

        // Assert upgrade email was sent
        Mail::assertSent(CategoryUpgradeNotificationMail::class, function ($mail) use ($client) {
            return $mail->client->id === $client->id
                && $mail->oldCategory === 'Medium'
                && $mail->newCategory === 'Premium';
        });
    }

    /**
     * Test client does NOT upgrade if below threshold
     *
     * @return void
     */
    public function test_client_does_not_upgrade_below_threshold()
    {
        Mail::fake();

        $client = User::factory()->inicial()->create();

        $store = Store::factory()->create();
        $promotions = Promotion::factory()->count(4)->create([ // Only 4 (need 5)
            'store_id' => $store->id,
            'estado' => 'aprobada',
            'fecha_desde' => now()->subMonth(),
            'fecha_hasta' => now()->addMonth(),
        ]);

        // Create 4 accepted usages (1 below threshold)
        foreach ($promotions as $promotion) {
            PromotionUsage::factory()->create([
                'client_id' => $client->id,
                'promotion_id' => $promotion->id,
                'estado' => 'aceptada',
                'fecha_uso' => now()->subDays(rand(1, 30)),
            ]);
        }

        // Run category evaluation job
        $categoryService = app(\App\Services\CategoryUpgradeService::class);
        $job = new EvaluateClientCategoriesJob();
        
        $job->handle($categoryService);

        $client->refresh();
        $this->assertEquals('Inicial', $client->categoria_cliente); // Still Inicial

        // Assert NO upgrade email was sent
        Mail::assertNotSent(CategoryUpgradeNotificationMail::class);
    }

    /**
     * Test only usages within last 6 months count
     *
     * @return void
     */
    public function test_only_recent_usages_count_for_upgrade()
    {
        Mail::fake();

        $client = User::factory()->inicial()->create();

        $store = Store::factory()->create();
        $promotions = Promotion::factory()->count(8)->create([
            'store_id' => $store->id,
            'estado' => 'aprobada',
            'fecha_desde' => now()->subYear(),
            'fecha_hasta' => now()->addMonth(),
        ]);

        // Create 3 recent usages (within 6 months)
        for ($i = 0; $i < 3; $i++) {
            PromotionUsage::factory()->create([
                'client_id' => $client->id,
                'promotion_id' => $promotions[$i]->id,
                'estado' => 'aceptada',
                'fecha_uso' => now()->subDays(rand(1, 150)), // Within 6 months
            ]);
        }

        // Create 5 old usages (outside 6 months)
        for ($i = 3; $i < 8; $i++) {
            PromotionUsage::factory()->create([
                'client_id' => $client->id,
                'promotion_id' => $promotions[$i]->id,
                'estado' => 'aceptada',
                'fecha_uso' => now()->subDays(200), // Outside 6 months
            ]);
        }

        // Run category evaluation job
        $categoryService = app(\App\Services\CategoryUpgradeService::class);
        $job = new EvaluateClientCategoriesJob();
        
        $job->handle($categoryService);

        $client->refresh();
        $this->assertEquals('Inicial', $client->categoria_cliente); // Only 3 recent, need 5

        Mail::assertNotSent(CategoryUpgradeNotificationMail::class);
    }

    /**
     * Test rejected usages do NOT count for upgrade
     *
     * @return void
     */
    public function test_rejected_usages_do_not_count()
    {
        Mail::fake();

        $client = User::factory()->inicial()->create();

        $store = Store::factory()->create();
        $promotions = Promotion::factory()->count(8)->create([
            'store_id' => $store->id,
            'estado' => 'aprobada',
            'fecha_desde' => now()->subMonth(),
            'fecha_hasta' => now()->addMonth(),
        ]);

        // Create 3 accepted usages
        for ($i = 0; $i < 3; $i++) {
            PromotionUsage::factory()->create([
                'client_id' => $client->id,
                'promotion_id' => $promotions[$i]->id,
                'estado' => 'aceptada',
                'fecha_uso' => now()->subDays(rand(1, 30)),
            ]);
        }

        // Create 5 rejected usages (should not count)
        for ($i = 3; $i < 8; $i++) {
            PromotionUsage::factory()->create([
                'client_id' => $client->id,
                'promotion_id' => $promotions[$i]->id,
                'estado' => 'rechazada',
                'fecha_uso' => now()->subDays(rand(1, 30)),
            ]);
        }

        // Run category evaluation job
        $categoryService = app(\App\Services\CategoryUpgradeService::class);
        $job = new EvaluateClientCategoriesJob();
        
        $job->handle($categoryService);

        $client->refresh();
        $this->assertEquals('Inicial', $client->categoria_cliente); // Only 3 accepted

        Mail::assertNotSent(CategoryUpgradeNotificationMail::class);
    }

    /**
     * Test pending usages do NOT count for upgrade
     *
     * @return void
     */
    public function test_pending_usages_do_not_count()
    {
        Mail::fake();

        $client = User::factory()->inicial()->create();

        $store = Store::factory()->create();
        $promotions = Promotion::factory()->count(7)->create([
            'store_id' => $store->id,
            'estado' => 'aprobada',
            'fecha_desde' => now()->subMonth(),
            'fecha_hasta' => now()->addMonth(),
        ]);

        // Create 2 accepted usages
        for ($i = 0; $i < 2; $i++) {
            PromotionUsage::factory()->create([
                'client_id' => $client->id,
                'promotion_id' => $promotions[$i]->id,
                'estado' => 'aceptada',
                'fecha_uso' => now()->subDays(rand(1, 30)),
            ]);
        }

        // Create 5 pending usages (should not count)
        for ($i = 2; $i < 7; $i++) {
            PromotionUsage::factory()->create([
                'client_id' => $client->id,
                'promotion_id' => $promotions[$i]->id,
                'estado' => 'enviada',
                'fecha_uso' => now()->subDays(rand(1, 30)),
            ]);
        }

        // Run category evaluation job
        $categoryService = app(\App\Services\CategoryUpgradeService::class);
        $job = new EvaluateClientCategoriesJob();
        
        $job->handle($categoryService);

        $client->refresh();
        $this->assertEquals('Inicial', $client->categoria_cliente); // Only 2 accepted

        Mail::assertNotSent(CategoryUpgradeNotificationMail::class);
    }

    /**
     * Test Premium clients stay Premium
     *
     * @return void
     */
    public function test_premium_clients_stay_premium()
    {
        $client = User::factory()->premium()->create();

        $store = Store::factory()->create();
        $promotions = Promotion::factory()->count(25)->create([
            'store_id' => $store->id,
            'estado' => 'aprobada',
            'fecha_desde' => now()->subMonth(),
            'fecha_hasta' => now()->addMonth(),
        ]);

        // Create 25 accepted usages
        foreach ($promotions as $promotion) {
            PromotionUsage::factory()->create([
                'client_id' => $client->id,
                'promotion_id' => $promotion->id,
                'estado' => 'aceptada',
                'fecha_uso' => now()->subDays(rand(1, 150)),
            ]);
        }

        // Run category evaluation job
        $categoryService = app(\App\Services\CategoryUpgradeService::class);
        $job = new EvaluateClientCategoriesJob();
        
        $job->handle($categoryService);

        $client->refresh();
        $this->assertEquals('Premium', $client->categoria_cliente); // Still Premium
    }
}
