<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear administrador
        User::create([
            'name' => 'Administrador Sistema',
            'email' => 'admin@sakila.com',
            'password' => Hash::make('Admin123!'),
            'role' => 'admin',
        ]);

        // Crear empleado
        User::create([
            'name' => 'Juan Empleado',
            'email' => 'employee@sakila.com',
            'password' => Hash::make('Employee123!'),
            'role' => 'employee',
        ]);

        // Crear cliente
        User::create([
            'name' => 'María Cliente',
            'email' => 'client@sakila.com',
            'password' => Hash::make('Client123!'),
            'role' => 'client',
        ]);

        // Crear algunos usuarios adicionales
        User::create([
            'name' => 'Luis Manager',
            'email' => 'manager@sakila.com',
            'password' => Hash::make('Manager123!'),
            'role' => 'employee',
        ]);

        User::create([
            'name' => 'Ana Admin',
            'email' => 'ana.admin@sakila.com',
            'password' => Hash::make('Admin123!'),
            'role' => 'admin',
        ]);
    }
}
