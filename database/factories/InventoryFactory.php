<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Inventory>
 */
class InventoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'store_id' => Store::factory(),
            'product_id' => Product::factory(),
            'quantity' => $this->faker->numberBetween(0, 100),
            'reorder_level' => 10,
            'reorder_quantity' => 50,
            'cost_price' => $this->faker->randomFloat(2, 1, 50),
            'selling_price' => $this->faker->randomFloat(2, 5, 100),
            'expiry_date' => $this->faker->dateTimeBetween('now', '+1 year'),
        ];
    }
}
