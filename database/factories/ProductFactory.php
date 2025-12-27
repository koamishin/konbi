<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'sku' => $this->faker->unique()->bothify('SKU-#####'),
            'barcode' => $this->faker->ean13(),
            'description' => $this->faker->sentence(),
            'is_active' => true,
            'requires_age_verification' => false,
            // 'category_id' => Category::factory(), // Optional: can be null
            // 'brand_id' => Brand::factory(), // Optional
        ];
    }
}
