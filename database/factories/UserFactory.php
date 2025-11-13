<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'user_type' => 'cliente',
            'client_category' => 'Inicial',
            'approved_at' => now(),
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Create an administrator user.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_type' => 'administrador',
            'client_category' => null,
            'approved_at' => now(),
            'email_verified_at' => now(),
        ]);
    }

    /**
     * Create a store owner user.
     */
    public function storeOwner(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_type' => 'dueño de local',
            'client_category' => null,
            'approved_at' => now(),
            'email_verified_at' => now(),
        ]);
    }

    /**
     * Create a pending store owner (not yet approved).
     */
    public function pendingStoreOwner(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_type' => 'dueño de local',
            'client_category' => null,
            'approved_at' => null,
            'email_verified_at' => now(),
        ]);
    }

    /**
     * Create a client with specific category.
     */
    public function client(string $category = 'Inicial'): static
    {
        return $this->state(fn (array $attributes) => [
            'user_type' => 'cliente',
            'client_category' => $category,
            'approved_at' => now(),
        ]);
    }

    /**
     * Create an Initial category client.
     */
    public function inicial(): static
    {
        return $this->client('Inicial');
    }

    /**
     * Create a Medium category client.
     */
    public function medium(): static
    {
        return $this->client('Medium');
    }

    /**
     * Create a Premium category client.
     */
    public function premium(): static
    {
        return $this->client('Premium');
    }
}
