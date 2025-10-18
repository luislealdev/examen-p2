<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create basic country
        DB::table('country')->insertOrIgnore([
            'country_id' => 1,
            'country' => 'United States',
            'last_update' => now(),
        ]);

        // Create basic city
        DB::table('city')->insertOrIgnore([
            'city_id' => 1,
            'city' => 'Test City',
            'country_id' => 1,
            'last_update' => now(),
        ]);

        // Create basic address
        DB::table('address')->insertOrIgnore([
            'address_id' => 1,
            'address' => '123 Test Street',
            'district' => 'Test District',
            'city_id' => 1,
            'postal_code' => '12345',
            'phone' => '555-0123',
            'last_update' => now(),
        ]);

        // Create basic store
        DB::table('stores')->insertOrIgnore([
            'store_id' => 1,
            'manager_staff_id' => 1,
            'address_id' => 1,
            'last_update' => now(),
        ]);

        // Create basic staff
        DB::table('staff')->insertOrIgnore([
            'staff_id' => 1,
            'first_name' => 'Test',
            'last_name' => 'Staff',
            'address_id' => 1,
            'email' => 'test.staff@example.com',
            'store_id' => 1,
            'active' => 1,
            'username' => 'teststaff',
            'password' => bcrypt('password'),
            'last_update' => now(),
        ]);

        // Update store to reference the staff member
        DB::table('stores')
            ->where('store_id', 1)
            ->update(['manager_staff_id' => 1]);
    }
}