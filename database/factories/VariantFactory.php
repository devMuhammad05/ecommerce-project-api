<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Variant>
 */
final class VariantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'sku' => fake()->unique()->numerify('SKU-#####'),
            'quantity' => fake()->numberBetween(0, 50),
            'price' => fake()->numberBetween(10000, 1000000), // Prices in cents
            'compare_at_price' => fake()->optional()->numberBetween(10000, 1000000),
            'cost_price' => fake()->numberBetween(5000, 500000),

            'weight' => fake()->randomFloat(2, 0.1, 5),
            'height' => fake()->randomFloat(2, 0.1, 10),
            'width' => fake()->randomFloat(2, 0.1, 10),
            'depth' => fake()->randomFloat(2, 0.1, 10),
        ];
    }
}
