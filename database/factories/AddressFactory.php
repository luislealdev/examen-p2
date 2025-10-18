<?php

namespace Database\Factories;

use App\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Address>
 */
class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'address' => fake()->streetAddress,
            'address2' => fake()->optional()->secondaryAddress,
            'district' => fake()->state,
            'city_id' => 1, // Using a default city ID
            'postal_code' => fake()->postcode,
            'phone' => fake()->phoneNumber,
        ];
    }
}