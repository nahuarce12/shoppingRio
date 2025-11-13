<?php

namespace Database\Factories;

use App\Models\News;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\News>
 */
class NewsFactory extends Factory
{
    protected $model = News::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $newsTemplates = [
            'Nuevas marcas internacionales llegan al shopping este mes',
            'Gran apertura de temporada con descuentos exclusivos',
            'Renovación completa del patio de comidas - Nuevas opciones gastronómicas',
            'Evento especial: Fashion Week en el shopping - No te lo pierdas',
            'Sorteo mensual entre todos los clientes - Premios increíbles',
            'Horario extendido los fines de semana - Comprá hasta las 22hs',
            'Nuevos estacionamientos disponibles con tarifa promocional',
            'Show en vivo este fin de semana - Entrada gratuita',
            'Programa de beneficios exclusivos para clientes frecuentes',
            'Cyber Monday en el shopping - Descuentos digitales',
            'Inauguración de nueva sala de cine premium',
            'Zona de juegos infantiles renovada - Diversión para toda la familia',
            'Alianza con bancos - 12 cuotas sin interés en locales adheridos',
            'App móvil del shopping ya disponible - Descargala gratis',
            'Mercado orgánico todos los sábados en la plaza central',
        ];

        $fechaDesde = fake()->dateTimeBetween('-1 month', '+1 week');
        $fechaHasta = fake()->dateTimeBetween($fechaDesde, '+2 months');

        return [
            'description' => fake()->randomElement($newsTemplates),
            'start_date' => $fechaDesde,
            'end_date' => $fechaHasta,
            'target_category' => fake()->randomElement(['Inicial', 'Medium', 'Premium']),
            'created_by' => 1, // Default to admin user ID 1, can be overridden
        ];
    }

    /**
     * Create news for a specific client category.
     */
    public function forCategory(string $category): static
    {
        return $this->state(fn (array $attributes) => [
            'target_category' => $category,
        ]);
    }

    /**
     * Create news for Initial category clients.
     */
    public function inicial(): static
    {
        return $this->forCategory('Inicial');
    }

    /**
     * Create news for Medium category clients.
     */
    public function medium(): static
    {
        return $this->forCategory('Medium');
    }

    /**
     * Create news for Premium category clients.
     */
    public function premium(): static
    {
        return $this->forCategory('Premium');
    }

    /**
     * Create active news (currently valid).
     */
    public function active(): static
    {
        $fechaDesde = fake()->dateTimeBetween('-1 week', 'now');
        $fechaHasta = fake()->dateTimeBetween('+1 week', '+2 months');

        return $this->state(fn (array $attributes) => [
            'start_date' => $fechaDesde,
            'end_date' => $fechaHasta,
        ]);
    }

    /**
     * Create expired news.
     */
    public function expired(): static
    {
        $fechaDesde = fake()->dateTimeBetween('-3 months', '-2 months');
        $fechaHasta = fake()->dateTimeBetween($fechaDesde, '-1 week');

        return $this->state(fn (array $attributes) => [
            'start_date' => $fechaDesde,
            'end_date' => $fechaHasta,
        ]);
    }

    /**
     * Create upcoming news (starts in the future).
     */
    public function upcoming(): static
    {
        $fechaDesde = fake()->dateTimeBetween('+1 week', '+1 month');
        $fechaHasta = fake()->dateTimeBetween($fechaDesde, '+2 months');

        return $this->state(fn (array $attributes) => [
            'start_date' => $fechaDesde,
            'end_date' => $fechaHasta,
        ]);
    }

    /**
     * Create long-duration news (3-6 months).
     */
    public function longDuration(): static
    {
        $fechaDesde = fake()->dateTimeBetween('-1 month', 'now');
        $fechaHasta = fake()->dateTimeBetween('+3 months', '+6 months');

        return $this->state(fn (array $attributes) => [
            'start_date' => $fechaDesde,
            'end_date' => $fechaHasta,
        ]);
    }
}
