<?php

namespace Database\Factories;

use App\Models\PromotionUsage;
use App\Models\Promotion;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PromotionUsage>
 */
class PromotionUsageFactory extends Factory
{
    protected $model = PromotionUsage::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => null, // Will be set by seeder or state method
            'promotion_id' => null, // Will be set by seeder or state method
            'usage_date' => fake()->dateTimeBetween('-6 months', 'now'),
            'status' => fake()->randomElement(['aceptada', 'aceptada', 'aceptada', 'enviada', 'rechazada']), // Weighted towards accepted
        ];
    }

    /**
     * Assign a client to the usage.
     */
    public function forClient(User $client): static
    {
        return $this->state(fn (array $attributes) => [
            'client_id' => $client->id,
        ]);
    }

    /**
     * Assign a promotion to the usage.
     */
    public function forPromotion(Promotion $promotion): static
    {
        return $this->state(fn (array $attributes) => [
            'promotion_id' => $promotion->id,
        ]);
    }

    /**
     * Create a sent/pending usage request.
     */
    public function sent(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'enviada',
            'usage_date' => now(),
        ]);
    }

    /**
     * Create an accepted usage.
     */
    public function accepted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'aceptada',
        ]);
    }

    /**
     * Create a rejected usage.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rechazada',
        ]);
    }

    /**
     * Create a usage from within the last 6 months (for category evaluation).
     */
    public function recentSixMonths(): static
    {
        return $this->state(fn (array $attributes) => [
            'usage_date' => fake()->dateTimeBetween('-6 months', 'now'),
        ]);
    }

    /**
     * Create a usage from more than 6 months ago (outside evaluation window).
     */
    public function oldUsage(): static
    {
        return $this->state(fn (array $attributes) => [
            'usage_date' => fake()->dateTimeBetween('-2 years', '-7 months'),
        ]);
    }

    /**
     * Create a usage on a specific date.
     */
    public function onDate(\DateTime|string $date): static
    {
        return $this->state(fn (array $attributes) => [
            'usage_date' => $date,
        ]);
    }
}
