<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $cities = [
            // México
            ['city' => 'Ciudad de México', 'country_id' => 1],
            ['city' => 'Guadalajara', 'country_id' => 1],
            ['city' => 'Monterrey', 'country_id' => 1],
            
            // Estados Unidos
            ['city' => 'New York', 'country_id' => 2],
            ['city' => 'Los Angeles', 'country_id' => 2],
            ['city' => 'Chicago', 'country_id' => 2],
            
            // Canadá
            ['city' => 'Toronto', 'country_id' => 3],
            ['city' => 'Vancouver', 'country_id' => 3],
            ['city' => 'Montreal', 'country_id' => 3],
            
            // España
            ['city' => 'Madrid', 'country_id' => 4],
            ['city' => 'Barcelona', 'country_id' => 4],
            ['city' => 'Valencia', 'country_id' => 4],
            
            // Argentina
            ['city' => 'Buenos Aires', 'country_id' => 5],
            ['city' => 'Córdoba', 'country_id' => 5],
            ['city' => 'Rosario', 'country_id' => 5],
        ];

        foreach ($cities as $city) {
            City::create($city);
        }
    }
}