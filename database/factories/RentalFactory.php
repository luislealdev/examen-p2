<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Inventory;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Rental>
 */
class RentalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'rental_date' => fake()->dateTimeBetween('-1 month', 'now'),
            'inventory_id' => 1, // Using default inventory ID
            'customer_id' => 1, // Using default customer ID
            'return_date' => fake()->optional(0.7)->dateTimeBetween('now', '+1 week'),
            'staff_id' => 1, // Using default staff ID
        ];
    }
    
    /**
     * Create an active rental (not yet returned).
     */
    public function active()
    {
        return $this->state(function (array $attributes) {
            return [
                'return_date' => null,
            ];
        });
    }
    
    /**
     * Create a returned rental.
     */
    public function returned()
    {
        return $this->state(function (array $attributes) {
            return [
                'return_date' => fake()->dateTimeBetween($attributes['rental_date'], 'now'),
            ];
        });
    }
}