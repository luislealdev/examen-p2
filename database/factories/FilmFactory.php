<?php

namespace Database\Factories;

use App\Models\Language;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Film>
 */
class FilmFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->words(3, true),
            'description' => fake()->paragraph(),
            'release_year' => fake()->numberBetween(1950, 2024),
            'language_id' => Language::factory(),
            'rental_duration' => fake()->numberBetween(3, 7),
            'rental_rate' => fake()->randomFloat(2, 0.99, 9.99),
            'length' => fake()->numberBetween(60, 180),
            'replacement_cost' => fake()->randomFloat(2, 9.99, 29.99),
            'rating' => fake()->randomElement(['G', 'PG', 'PG-13', 'R', 'NC-17']),
            'category_id' => Category::factory(),
        ];
    }
}