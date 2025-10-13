<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Staff;
use App\Models\Store;
use App\Models\Address;
use Illuminate\Support\Facades\Hash;

class StaffSeeder extends Seeder
{
    public function run(): void
    {
        // Crear dirección para los empleados
        $address = Address::create([
            'address' => 'Calle Principal 123',
            'district' => 'Centro',
            'city' => 'Ciudad Ejemplo',
            'postal_code' => '12345',
            'phone' => '555-0123',
        ]);

        // Crear tienda si no existe
        $store = Store::firstOrCreate(
            ['store_id' => 1],
            [
                'manager_staff_id' => 1,
                'address_id' => $address->address_id,
            ]
        );

        // Crear empleados de prueba
        $employees = [
            [
                'first_name' => 'Juan',
                'last_name' => 'Pérez',
                'email' => 'juan.perez@sakila.com',
                'username' => 'juanperez',
                'password' => 'Empleado123!'
            ],
            [
                'first_name' => 'María',
                'last_name' => 'García',
                'email' => 'maria.garcia@sakila.com',
                'username' => 'mariagarcia',
                'password' => 'Empleado123!'
            ],
            [
                'first_name' => 'Admin',
                'last_name' => 'Sistema',
                'email' => 'admin@sakila.com',
                'username' => 'admin',
                'password' => 'Admin123!'
            ]
        ];

        foreach ($employees as $employee) {
            // Crear el usuario en la tabla users
            $user = \App\Models\User::create([
                'name' => $employee['first_name'] . ' ' . $employee['last_name'],
                'email' => $employee['email'],
                'password' => Hash::make($employee['password']),
                'role' => 'employee'
            ]);

            // Crear el staff
            Staff::create([
                'first_name' => $employee['first_name'],
                'last_name' => $employee['last_name'],
                'address_id' => $address->address_id,
                'email' => $employee['email'],
                'store_id' => $store->store_id,
                'active' => true,
                'username' => $employee['username'],
                'password' => Hash::make($employee['password'])
            ]);
        }
    }
}