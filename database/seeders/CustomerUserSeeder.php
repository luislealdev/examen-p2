<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Support\Facades\Hash;

class CustomerUserSeeder extends Seeder
{
    public function run(): void
    {
        // Get existing customers
        $customers = Customer::where('active', true)->get();

        foreach ($customers as $customer) {
            // Check if user already exists
            $existingUser = User::where('email', $customer->email)->first();
            
            if (!$existingUser) {
                User::create([
                    'name' => $customer->first_name . ' ' . $customer->last_name,
                    'email' => $customer->email,
                    'password' => Hash::make('Cliente123!'), // Default password for all customers
                    'role' => 'customer'
                ]);
            }
        }

        $this->command->info('Customer users created with password: Cliente123!');
        $this->command->info('Available customer emails:');
        
        foreach ($customers as $customer) {
            $this->command->info('- ' . $customer->email);
        }
    }
}