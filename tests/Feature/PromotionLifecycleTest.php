<?php

namespace Tests\Feature;

use App\Models\Promotion;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PromotionLifecycleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(); // Run seeders for test data
    }

    /**
     * Test complete promotion lifecycle: create, approve, request, accept
     *
     * @return void
     */
    public function test_promotion_lifecycle_flow()
    {
        // 1. Store owner creates a promotion
        $store = Store::first();
        $owner = $store->owner;

        $this->actingAs($owner);

        $promotionData = [
            'texto' => '50% de descuento en segunda unidad',
            'fecha_desde' => now()->addDay()->format('Y-m-d'),
            'fecha_hasta' => now()->addDays(30)->format('Y-m-d'),
            'dias_semana' => [1, 1, 1, 1, 1, 1, 1], // All days
            'categoria_minima' => 'Inicial',
            'store_id' => $store->id,
        ];

        $response = $this->post(route('store.promotions.store'), $promotionData);

        $response->assertRedirect();
        $this->assertDatabaseHas('promotions', [
            'texto' => '50% de descuento en segunda unidad',
            'estado' => 'pendiente',
            'store_id' => $store->id,
        ]);

        $promotion = Promotion::where('texto', '50% de descuento en segunda unidad')->first();
        $this->assertEquals('pendiente', $promotion->estado);

        // 2. Admin approves the promotion
        $admin = User::where('tipo_usuario', 'administrador')->first();
        $this->actingAs($admin);

        $response = $this->post(route('admin.promotions.approve', $promotion->id));
        $response->assertRedirect();

        $promotion->refresh();
        $this->assertEquals('aprobada', $promotion->estado);

        // 3. Client requests to use the promotion
        $client = User::where('tipo_usuario', 'cliente')->first();
        $this->actingAs($client);

        $response = $this->post(route('client.promotions.request', $promotion->id));
        $response->assertRedirect();

        $this->assertDatabaseHas('promotion_usage', [
            'client_id' => $client->id,
            'promotion_id' => $promotion->id,
            'estado' => 'enviada',
        ]);

        // 4. Store owner accepts the request
        $usage = $promotion->usages()->where('client_id', $client->id)->first();
        $this->actingAs($owner);

        $response = $this->post(route('store.usage-requests.accept', $usage->id));
        $response->assertRedirect();

        $usage->refresh();
        $this->assertEquals('aceptada', $usage->estado);
    }

    /**
     * Test promotion denial flow
     *
     * @return void
     */
    public function test_admin_can_deny_promotion()
    {
        $promotion = Promotion::where('estado', 'pendiente')->first();
        $admin = User::where('tipo_usuario', 'administrador')->first();

        $this->actingAs($admin);

        $response = $this->post(route('admin.promotions.deny', $promotion->id), [
            'reason' => 'Does not meet shopping policy',
        ]);

        $response->assertRedirect();

        $promotion->refresh();
        $this->assertEquals('denegada', $promotion->estado);
    }

    /**
     * Test client cannot request same promotion twice
     *
     * @return void
     */
    public function test_client_cannot_request_same_promotion_twice()
    {
        $client = User::where('tipo_usuario', 'cliente')->first();
        $promotion = Promotion::approved()->active()->first();

        $this->actingAs($client);

        // First request - should succeed
        $response1 = $this->post(route('client.promotions.request', $promotion->id));
        $response1->assertRedirect();

        // Second request - should fail
        $response2 = $this->post(route('client.promotions.request', $promotion->id));
        $response2->assertStatus(422); // Validation error or forbidden

        // Verify only one usage record exists
        $usageCount = $promotion->usages()->where('client_id', $client->id)->count();
        $this->assertEquals(1, $usageCount);
    }

    /**
     * Test promotion visibility based on category
     *
     * @return void
     */
    public function test_promotion_visibility_based_on_client_category()
    {
        $inicialClient = User::where('tipo_usuario', 'cliente')
            ->where('categoria_cliente', 'Inicial')
            ->first();

        $premiumClient = User::where('tipo_usuario', 'cliente')
            ->where('categoria_cliente', 'Premium')
            ->first();

        $premiumPromotion = Promotion::approved()
            ->active()
            ->where('categoria_minima', 'Premium')
            ->first();

        // Inicial client should NOT see Premium promotion
        $this->actingAs($inicialClient);
        $response = $this->get(route('client.promotions.show', $premiumPromotion->id));
        $response->assertStatus(403); // Forbidden

        // Premium client SHOULD see Premium promotion
        $this->actingAs($premiumClient);
        $response = $this->get(route('client.promotions.show', $premiumPromotion->id));
        $response->assertStatus(200);
    }

    /**
     * Test promotion cannot be requested on invalid day
     *
     * @return void
     */
    public function test_promotion_cannot_be_requested_on_invalid_day()
    {
        $client = User::where('tipo_usuario', 'cliente')->first();

        // Create a promotion valid only on Mondays (day 0)
        $promotion = Promotion::factory()->create([
            'store_id' => Store::first()->id,
            'dias_semana' => [1, 0, 0, 0, 0, 0, 0], // Only Monday
            'estado' => 'aprobada',
            'fecha_desde' => now()->subDay(),
            'fecha_hasta' => now()->addMonth(),
        ]);

        $this->actingAs($client);

        // If today is not Monday, request should fail
        $today = now()->dayOfWeek; // 0 = Sunday, 6 = Saturday
        if ($today != 1) { // Not Monday
            $response = $this->post(route('client.promotions.request', $promotion->id));
            $response->assertStatus(422); // Validation error
        }
    }

    /**
     * Test expired promotions cannot be requested
     *
     * @return void
     */
    public function test_expired_promotions_cannot_be_requested()
    {
        $client = User::where('tipo_usuario', 'cliente')->first();

        $expiredPromotion = Promotion::factory()->create([
            'store_id' => Store::first()->id,
            'estado' => 'aprobada',
            'fecha_desde' => now()->subDays(30),
            'fecha_hasta' => now()->subDay(), // Expired yesterday
        ]);

        $this->actingAs($client);

        $response = $this->post(route('client.promotions.request', $expiredPromotion->id));
        $response->assertStatus(422); // Validation error
    }

    /**
     * Test store owner can only manage their own promotions
     *
     * @return void
     */
    public function test_store_owner_cannot_edit_other_store_promotions()
    {
        $owner1 = User::where('tipo_usuario', 'dueño de local')->first();
        $owner2 = User::where('tipo_usuario', 'dueño de local')->skip(1)->first();

        $promotion = Promotion::where('store_id', $owner2->store->id)->first();

        $this->actingAs($owner1);

        $response = $this->delete(route('store.promotions.destroy', $promotion->id));
        $response->assertStatus(403); // Forbidden
    }
}
