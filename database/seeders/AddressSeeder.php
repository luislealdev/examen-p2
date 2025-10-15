<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Country;
use App\Models\City;
use App\Models\Address;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create countries
        $countries = [
            ['country' => 'España'],
            ['country' => 'Francia'],
            ['country' => 'Estados Unidos'],
            ['country' => 'Reino Unido'],
        ];

        foreach ($countries as $countryData) {
            $country = Country::create([
                'country' => $countryData['country'],
                'last_update' => now(),
            ]);

            // Create cities for each country
            $cities = $this->getCitiesForCountry($countryData['country']);
            
            foreach ($cities as $cityName) {
                $city = City::create([
                    'city' => $cityName,
                    'country_id' => $country->country_id,
                    'last_update' => now(),
                ]);

                // Create a sample address for each city
                Address::create([
                    'address' => 'Calle Principal ' . rand(1, 100),
                    'address2' => rand(0, 1) ? 'Piso ' . rand(1, 5) : null,
                    'district' => 'Centro',
                    'city_id' => $city->city_id,
                    'postal_code' => str_pad(rand(10000, 99999), 5, '0', STR_PAD_LEFT),
                    'phone' => '+34-' . rand(600000000, 699999999),
                    'location' => rand(-90, 90) . ',' . rand(-180, 180),
                    'last_update' => now(),
                ]);
            }
        }
    }

    /**
     * Get cities for a given country.
     */
    private function getCitiesForCountry(string $country): array
    {
        return match ($country) {
            'España' => ['Madrid', 'Barcelona', 'Valencia', 'Sevilla'],
            'Francia' => ['París', 'Lyon', 'Marsella', 'Toulouse'],
            'Estados Unidos' => ['Nueva York', 'Los Ángeles', 'Chicago', 'Houston'],
            'Reino Unido' => ['Londres', 'Manchester', 'Birmingham', 'Liverpool'],
            default => ['Ciudad Principal'],
        };
    }
}
