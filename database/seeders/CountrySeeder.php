<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            ['country' => 'México'],
            ['country' => 'Estados Unidos'],
            ['country' => 'Canadá'],
            ['country' => 'España'],
            ['country' => 'Argentina'],
            // Agrega más países según necesites
        ];

        foreach ($countries as $country) {
            Country::create($country);
        }
    }
}