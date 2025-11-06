<?php

namespace Database\Factories;

use App\Models\Promotion;
use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Promotion>
 */
class PromotionFactory extends Factory
{
    protected $model = Promotion::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $promoTexts = [
            '20% de descuento en toda la tienda',
            '2x1 en productos seleccionados',
            '50% de descuento en segunda unidad',
            '3 cuotas sin interés con tarjetas de crédito',
            '15% de descuento pagando en efectivo',
            '30% off en toda la colección de invierno',
            '25% de descuento en compras superiores a $10000',
            'Llevá 3 y pagá 2 en artículos seleccionados',
            '40% de descuento en liquidación final',
            '10% de descuento adicional para clientes premium',
            'Envío gratis en compras superiores a $5000',
            '35% off en productos de temporada',
            'Descuento especial del 20% los martes',
            '2x1 en el segundo producto de igual o menor valor',
            '25% de descuento presentando esta promoción',
        ];

        // Generate date range - mix of active, upcoming, and expiring promotions
        // 60% active (started in past, ends in future), 30% upcoming, 10% recent
        $random = fake()->numberBetween(1, 100);
        
        if ($random <= 60) {
            // Active promotions: started 1-30 days ago, ends 1-60 days from now
            $fechaDesde = fake()->dateTimeBetween('-30 days', 'now');
            $fechaHasta = fake()->dateTimeBetween('now', '+60 days');
        } elseif ($random <= 90) {
            // Upcoming promotions: start in future
            $fechaDesde = fake()->dateTimeBetween('+1 day', '+30 days');
            $fechaHasta = fake()->dateTimeBetween($fechaDesde, '+90 days');
        } else {
            // Recently expired: ended in last 7 days
            $fechaHasta = fake()->dateTimeBetween('-7 days', '-1 day');
            $fechaDesde = fake()->dateTimeBetween('-60 days', $fechaHasta);
        }

        // Generate random days of week (at least 1 day active)
        $diasSemana = [];
        $numDays = fake()->numberBetween(1, 7);
        $selectedDays = fake()->randomElements(range(0, 6), $numDays);
        
        for ($i = 0; $i <= 6; $i++) {
            $diasSemana[] = in_array($i, $selectedDays) ? 1 : 0;
        }

        return [
            'texto' => fake()->randomElement($promoTexts),
            'fecha_desde' => $fechaDesde,
            'fecha_hasta' => $fechaHasta,
            'categoria_minima' => fake()->randomElement(['Inicial', 'Medium', 'Premium']),
            'dias_semana' => $diasSemana,
            'estado' => 'aprobada',
            'store_id' => null, // Will be set by seeder or state method
        ];
    }

    /**
     * Assign a store to the promotion.
     */
    public function forStore(Store $store): static
    {
        return $this->state(fn (array $attributes) => [
            'store_id' => $store->id,
        ]);
    }

    /**
     * Create a pending promotion (awaiting admin approval).
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'pendiente',
        ]);
    }

    /**
     * Create an approved promotion.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'aprobada',
        ]);
    }

    /**
     * Create a denied promotion.
     */
    public function denied(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'denegada',
        ]);
    }

    /**
     * Create a promotion for a specific client category.
     */
    public function forCategory(string $category): static
    {
        return $this->state(fn (array $attributes) => [
            'categoria_minima' => $category,
        ]);
    }

    /**
     * Create a promotion for Initial category clients.
     */
    public function inicial(): static
    {
        return $this->forCategory('Inicial');
    }

    /**
     * Create a promotion for Medium category clients.
     */
    public function medium(): static
    {
        return $this->forCategory('Medium');
    }

    /**
     * Create a promotion for Premium category clients.
     */
    public function premium(): static
    {
        return $this->forCategory('Premium');
    }

    /**
     * Create a promotion valid all week.
     */
    public function allWeek(): static
    {
        return $this->state(fn (array $attributes) => [
            'dias_semana' => [1, 1, 1, 1, 1, 1, 1],
        ]);
    }

    /**
     * Create a promotion valid only on weekends.
     */
    public function weekendsOnly(): static
    {
        return $this->state(fn (array $attributes) => [
            'dias_semana' => [0, 0, 0, 0, 0, 1, 1], // Saturday and Sunday
        ]);
    }

    /**
     * Create a promotion valid only on weekdays.
     */
    public function weekdaysOnly(): static
    {
        return $this->state(fn (array $attributes) => [
            'dias_semana' => [1, 1, 1, 1, 1, 0, 0], // Monday to Friday
        ]);
    }

    /**
     * Create an expired promotion.
     */
    public function expired(): static
    {
        $fechaDesde = fake()->dateTimeBetween('-3 months', '-2 months');
        $fechaHasta = fake()->dateTimeBetween($fechaDesde, '-1 week');

        return $this->state(fn (array $attributes) => [
            'fecha_desde_promo' => $fechaDesde,
            'fecha_hasta_promo' => $fechaHasta,
        ]);
    }

    /**
     * Create a promotion starting in the future.
     */
    public function upcoming(): static
    {
        $fechaDesde = fake()->dateTimeBetween('+1 week', '+1 month');
        $fechaHasta = fake()->dateTimeBetween($fechaDesde, '+3 months');

        return $this->state(fn (array $attributes) => [
            'fecha_desde_promo' => $fechaDesde,
            'fecha_hasta_promo' => $fechaHasta,
        ]);
    }
}
