<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Subcategory> */
class SubcategoryFactory extends Factory
{
    protected $model = Subcategory::class;

    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'name' => ucfirst($this->faker->unique()->words(2, true)),
            'description' => $this->faker->sentence(10),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
