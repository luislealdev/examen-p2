<?php

namespace Database\Seeders;

use App\Models\Store;
use App\Models\Address;
use App\Models\City;
use App\Models\Staff;
use Illuminate\Database\Seeder;

class StoreSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener un empleado para ser gerente
        $staff = Staff::first();
        
        if (!$staff) {
            throw new \Exception('No hay empleados registrados. Por favor, ejecuta el StaffSeeder primero.');
        }

        // Crear tiendas
        $addresses = Address::all();
        
        // Asignar cada dirección a una tienda
        foreach ($addresses as $address) {
            Store::create([
                'manager_staff_id' => $staff->staff_id,
                'address_id' => $address->address_id,
            ]);
        }
    }
}