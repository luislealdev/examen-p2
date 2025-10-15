<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\User;
use App\Models\Address;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear dirección para el cliente demo
        $address = Address::create([
            'address' => 'Calle Cliente Demo 456',
            'district' => 'Zona Residencial',
            'city_id' => 1, // Ciudad de México
            'postal_code' => '54321',
            'phone' => '555-9876',
        ]);

        // Obtener el usuario cliente
        $clientUser = User::where('email', 'cliente@test.com')->first();
        
        if ($clientUser) {
            // Crear el perfil de customer
            Customer::create([
                'store_id' => 1, // Tienda 1
                'first_name' => 'Cliente',
                'last_name' => 'Demo',
                'email' => $clientUser->email,
                'address_id' => $address->address_id,
                'active' => true,
            ]);

            echo "Customer creado exitosamente para: {$clientUser->email}\n";
        } else {
            echo "Usuario cliente no encontrado\n";
        }
    }
}