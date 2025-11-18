<?php

namespace Tests\Unit;

use App\Models\Promotion;
use App\Models\Store;
use App\Models\User;
use App\Services\PromotionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PromotionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected PromotionService $promotionService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->promotionService = app(PromotionService::class);
    }

    /**
     * Test promotion eligibility - valid scenario
     */
    public function test_client_is_eligible_for_valid_promotion()
    {
        $client = User::factory()->medium()->create();
        
        $promotion = Promotion::factory()->create([
            'store_id' => Store::factory()->create()->id,
            'estado' => 'aprobada',
            'fecha_desde' => now()->subDay(),
            'fecha_hasta' => now()->addMonth(),
            'categoria_minima' => 'Inicial', // Medium can access Inicial
            'dias_semana' => [1, 1, 1, 1, 1, 1, 1], // All days valid
        ]);

        $result = $this->promotionService->checkEligibility($promotion, $client);

        $this->assertTrue($result['eligible']);
    }

    /**
     * Test promotion eligibility - wrong category
     */
    public function test_inicial_client_cannot_access_premium_promotion()
    {
        $client = User::factory()->inicial()->create();
        
        $promotion = Promotion::factory()->create([
            'store_id' => Store::factory()->create()->id,
            'estado' => 'aprobada',
            'fecha_desde' => now()->subDay(),
            'fecha_hasta' => now()->addMonth(),
            'categoria_minima' => 'Premium', // Inicial cannot access
            'dias_semana' => [1, 1, 1, 1, 1, 1, 1],
        ]);

        $result = $this->promotionService->checkEligibility($promotion, $client);

        $this->assertFalse(\['eligible']);
    }

    /**
     * Test promotion eligibility - expired promotion
     */
    public function test_client_cannot_use_expired_promotion()
    {
        $client = User::factory()->medium()->create();
        
        $promotion = Promotion::factory()->create([
            'store_id' => Store::factory()->create()->id,
            'estado' => 'aprobada',
            'fecha_desde' => now()->subMonth(),
            'fecha_hasta' => now()->subDay(), // Expired yesterday
            'categoria_minima' => 'Inicial',
            'dias_semana' => [1, 1, 1, 1, 1, 1, 1],
        ]);

        $result = $this->promotionService->checkEligibility($promotion, $client);

        $this->assertFalse(\['eligible']);
    }

    /**
     * Test promotion eligibility - future promotion
     */
    public function test_client_cannot_use_future_promotion()
    {
        $client = User::factory()->medium()->create();
        
        $promotion = Promotion::factory()->create([
            'store_id' => Store::factory()->create()->id,
            'estado' => 'aprobada',
            'fecha_desde' => now()->addWeek(), // Starts next week
            'fecha_hasta' => now()->addMonth(),
            'categoria_minima' => 'Inicial',
            'dias_semana' => [1, 1, 1, 1, 1, 1, 1],
        ]);

        $result = $this->promotionService->checkEligibility($promotion, $client);

        $this->assertFalse(\['eligible']);
    }

    /**
     * Test promotion eligibility - invalid day of week
     */
    public function test_client_cannot_use_promotion_on_invalid_day()
    {
        $client = User::factory()->medium()->create();
        
        // Create promotion valid only on Mondays (index 0)
        $promotion = Promotion::factory()->create([
            'store_id' => Store::factory()->create()->id,
            'estado' => 'aprobada',
            'fecha_desde' => now()->subDay(),
            'fecha_hasta' => now()->addMonth(),
            'categoria_minima' => 'Inicial',
            'dias_semana' => [1, 0, 0, 0, 0, 0, 0], // Only Monday
        ]);

        // Get today's day of week (0 = Monday, 6 = Sunday in Laravel)
        $today = now()->dayOfWeekIso - 1; // Convert to 0-6 range

        if ($today != 0) { // If not Monday
            $result = $this->promotionService->checkEligibility($promotion, $client);
            $this->assertFalse(\['eligible']);
        } else {
            // If today is Monday, it should be eligible
            $result = $this->promotionService->checkEligibility($promotion, $client);
            $this->assertTrue(\['eligible']);
        }
    }

    /**
     * Test promotion eligibility - already used
     */
    public function test_client_cannot_use_same_promotion_twice()
    {
        $client = User::factory()->medium()->create();
        
        $promotion = Promotion::factory()->create([
            'store_id' => Store::factory()->create()->id,
            'estado' => 'aprobada',
            'fecha_desde' => now()->subDay(),
            'fecha_hasta' => now()->addMonth(),
            'categoria_minima' => 'Inicial',
            'dias_semana' => [1, 1, 1, 1, 1, 1, 1],
        ]);

        // Create existing usage
        $client->promotionUsages()->create([
            'promotion_id' => $promotion->id,
            'fecha_uso' => now(),
            'estado' => 'aceptada',
        ]);

        $result = $this->promotionService->checkEligibility($promotion, $client);

        $this->assertFalse(\['eligible']);
    }

    /**
     * Test promotion eligibility - pending usage (not yet accepted)
     */
    public function test_client_with_pending_usage_is_not_eligible()
    {
        $client = User::factory()->medium()->create();
        
        $promotion = Promotion::factory()->create([
            'store_id' => Store::factory()->create()->id,
            'estado' => 'aprobada',
            'fecha_desde' => now()->subDay(),
            'fecha_hasta' => now()->addMonth(),
            'categoria_minima' => 'Inicial',
            'dias_semana' => [1, 1, 1, 1, 1, 1, 1],
        ]);

        // Create pending usage
        $client->promotionUsages()->create([
            'promotion_id' => $promotion->id,
            'fecha_uso' => now(),
            'estado' => 'enviada', // Still pending
        ]);

        $result = $this->promotionService->checkEligibility($promotion, $client);

        $this->assertFalse(\['eligible']);
    }

    /**
     * Test promotion eligibility - denied promotion
     */
    public function test_client_cannot_use_denied_promotion()
    {
        $client = User::factory()->medium()->create();
        
        $promotion = Promotion::factory()->create([
            'store_id' => Store::factory()->create()->id,
            'estado' => 'denegada', // Denied by admin
            'fecha_desde' => now()->subDay(),
            'fecha_hasta' => now()->addMonth(),
            'categoria_minima' => 'Inicial',
            'dias_semana' => [1, 1, 1, 1, 1, 1, 1],
        ]);

        $result = $this->promotionService->checkEligibility($promotion, $client);

        $this->assertFalse(\['eligible']);
    }

    /**
     * Test promotion eligibility - pending approval
     */
    public function test_client_cannot_use_pending_promotion()
    {
        $client = User::factory()->medium()->create();
        
        $promotion = Promotion::factory()->create([
            'store_id' => Store::factory()->create()->id,
            'estado' => 'pendiente', // Awaiting admin approval
            'fecha_desde' => now()->subDay(),
            'fecha_hasta' => now()->addMonth(),
            'categoria_minima' => 'Inicial',
            'dias_semana' => [1, 1, 1, 1, 1, 1, 1],
        ]);

        $result = $this->promotionService->checkEligibility($promotion, $client);

        $this->assertFalse(\['eligible']);
    }

    /**
     * Test category hierarchy - Premium can access all
     */
    public function test_premium_client_can_access_all_categories()
    {
        $client = User::factory()->premium()->create();
        
        // Test Inicial promotion
        $inicialPromo = Promotion::factory()->create([
            'store_id' => Store::factory()->create()->id,
            'estado' => 'aprobada',
            'fecha_desde' => now()->subDay(),
            'fecha_hasta' => now()->addMonth(),
            'categoria_minima' => 'Inicial',
            'dias_semana' => [1, 1, 1, 1, 1, 1, 1],
        ]);

        // Test Medium promotion
        $mediumPromo = Promotion::factory()->create([
            'store_id' => Store::factory()->create()->id,
            'estado' => 'aprobada',
            'fecha_desde' => now()->subDay(),
            'fecha_hasta' => now()->addMonth(),
            'categoria_minima' => 'Medium',
            'dias_semana' => [1, 1, 1, 1, 1, 1, 1],
        ]);

        // Test Premium promotion
        $premiumPromo = Promotion::factory()->create([
            'store_id' => Store::factory()->create()->id,
            'estado' => 'aprobada',
            'fecha_desde' => now()->subDay(),
            'fecha_hasta' => now()->addMonth(),
            'categoria_minima' => 'Premium',
            'dias_semana' => [1, 1, 1, 1, 1, 1, 1],
        ]);

        $this->assertTrue($this->promotionService->isClientEligible($client, $inicialPromo));
        $this->assertTrue($this->promotionService->isClientEligible($client, $mediumPromo));
        $this->assertTrue($this->promotionService->isClientEligible($client, $premiumPromo));
    }

    /**
     * Test category hierarchy - Medium can access Inicial and Medium
     */
    public function test_medium_client_can_access_inicial_and_medium()
    {
        $client = User::factory()->medium()->create();
        
        $inicialPromo = Promotion::factory()->create([
            'store_id' => Store::factory()->create()->id,
            'estado' => 'aprobada',
            'fecha_desde' => now()->subDay(),
            'fecha_hasta' => now()->addMonth(),
            'categoria_minima' => 'Inicial',
            'dias_semana' => [1, 1, 1, 1, 1, 1, 1],
        ]);

        $mediumPromo = Promotion::factory()->create([
            'store_id' => Store::factory()->create()->id,
            'estado' => 'aprobada',
            'fecha_desde' => now()->subDay(),
            'fecha_hasta' => now()->addMonth(),
            'categoria_minima' => 'Medium',
            'dias_semana' => [1, 1, 1, 1, 1, 1, 1],
        ]);

        $premiumPromo = Promotion::factory()->create([
            'store_id' => Store::factory()->create()->id,
            'estado' => 'aprobada',
            'fecha_desde' => now()->subDay(),
            'fecha_hasta' => now()->addMonth(),
            'categoria_minima' => 'Premium',
            'dias_semana' => [1, 1, 1, 1, 1, 1, 1],
        ]);

        $this->assertTrue($this->promotionService->isClientEligible($client, $inicialPromo));
        $this->assertTrue($this->promotionService->isClientEligible($client, $mediumPromo));
        $this->assertFalse($this->promotionService->isClientEligible($client, $premiumPromo));
    }
}
