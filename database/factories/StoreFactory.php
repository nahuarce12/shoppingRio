<?php

namespace Database\Factories;

use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Store>
 */
class StoreFactory extends Factory
{
    protected $model = Store::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $rubros = [
            'indumentaria',
            'calzado',
            'perfumeria',
            'joyeria',
            'optica',
            'comida',
            'tecnologia',
            'libreria',
            'deportes',
            'jugueteria',
            'hogar',
            'electrodomesticos',
        ];

        $ubicaciones = [
            'Planta Baja - Local 101',
            'Planta Baja - Local 102',
            'Planta Baja - Local 103',
            'Primer Piso - Local 201',
            'Primer Piso - Local 202',
            'Primer Piso - Local 203',
            'Segundo Piso - Local 301',
            'Segundo Piso - Local 302',
            'Segundo Piso - Local 303',
            'Tercer Piso - Local 401',
            'Tercer Piso - Local 402',
            'Patio de Comidas - Local PC1',
            'Patio de Comidas - Local PC2',
        ];

        return [
            'nombre' => fake()->company() . ' ' . fake()->randomElement(['Store', 'Shop', 'Boutique', 'Gallery', 'Center']),
            'ubicacion' => fake()->randomElement($ubicaciones),
            'rubro' => fake()->randomElement($rubros),
            'owner_id' => null, // Will be set by seeder or state method
        ];
    }

    /**
     * Assign a store owner to the store.
     */
    public function forOwner(User $owner): static
    {
        return $this->state(fn (array $attributes) => [
            'owner_id' => $owner->id,
        ]);
    }

    /**
     * Create a store with a specific rubro.
     */
    public function rubro(string $rubro): static
    {
        return $this->state(fn (array $attributes) => [
            'rubro' => $rubro,
        ]);
    }

    /**
     * Create an indumentaria store.
     */
    public function indumentaria(): static
    {
        return $this->state(fn (array $attributes) => [
            'rubro' => 'indumentaria',
            'nombre' => fake()->randomElement([
                'Fashion Store',
                'Elegance Boutique',
                'Style Gallery',
                'Trendy Fashion',
                'Urban Style',
            ]),
        ]);
    }

    /**
     * Create a comida store.
     */
    public function comida(): static
    {
        return $this->state(fn (array $attributes) => [
            'rubro' => 'comida',
            'ubicacion' => fake()->randomElement([
                'Patio de Comidas - Local PC1',
                'Patio de Comidas - Local PC2',
                'Patio de Comidas - Local PC3',
                'Patio de Comidas - Local PC4',
            ]),
            'nombre' => fake()->randomElement([
                'Burger House',
                'Pizza Express',
                'Sushi Bar',
                'Café Delight',
                'Pasta & More',
            ]),
        ]);
    }

    /**
     * Create a tecnologia store.
     */
    public function tecnologia(): static
    {
        return $this->state(fn (array $attributes) => [
            'rubro' => 'tecnologia',
            'nombre' => fake()->randomElement([
                'TechWorld',
                'Digital Store',
                'Gadget Center',
                'Smart Tech',
                'Electronics Plus',
            ]),
        ]);
    }
}
