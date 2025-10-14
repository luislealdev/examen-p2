<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Primero las tablas base
            CountrySeeder::class,
            CitySeeder::class,
            CategorySeeder::class,
            LanguageSeeder::class,
            
            // Primero los empleados, luego las tiendas con sus direcciones
            StaffSeeder::class,
            StoreAndAddressSeeder::class,
        ]);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
