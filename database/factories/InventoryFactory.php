<?php

namespace Database\Factories;

use App\Models\Film;
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
            'film_id' => 1, // Use default film ID
            'store_id' => 1, // Use default store ID
            'condition' => fake()->randomElement(['available', 'damaged', 'lost']),
        ];
    }
}