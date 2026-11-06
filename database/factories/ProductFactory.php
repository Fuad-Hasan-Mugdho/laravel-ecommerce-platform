<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Product> */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $newPrice = $this->faker->randomFloat(2, 100, 5000);

        return [
            'subcategory_id' => Subcategory::factory(),
            // Keep category_id consistent with the subcategory's parent.
            'category_id' => fn (array $attributes) => Subcategory::find($attributes['subcategory_id'])->category_id,
            'name' => ucfirst($this->faker->unique()->words(3, true)),
            'description' => $this->faker->paragraph(3),
            'image' => null,
            'old_price' => $this->faker->boolean(60) ? round($newPrice * $this->faker->randomFloat(2, 1.1, 1.6), 2) : null,
            'new_price' => $newPrice,
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
