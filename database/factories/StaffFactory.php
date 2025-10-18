<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Staff>
 */
class StaffFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName,
            'last_name' => fake()->lastName,
            'address_id' => Address::factory(),
            'picture' => null,
            'email' => fake()->unique()->safeEmail,
            'store_id' => 1, // Using a default store ID to avoid circular dependency
            'active' => true,
            'username' => fake()->unique()->userName,
            'password' => bcrypt('password'),
        ];
    }
}