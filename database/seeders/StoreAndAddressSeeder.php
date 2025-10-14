<?php

namespace Database\Seeders;

use App\Models\Store;
use App\Models\Address;
use App\Models\City;
use App\Models\Staff;
use Illuminate\Database\Seeder;

class StoreAndAddressSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener ciudades existentes
        $cities = City::all();
        
        // Obtener empleados existentes
        $staffMembers = Staff::all();
        
        if ($staffMembers->isEmpty()) {
            throw new \Exception('No hay empleados registrados. Por favor, ejecuta el StaffSeeder primero.');
        }

        // Crear tiendas con sus direcciones
        $storeData = [
            [
                'city' => 'Ciudad de México',
                'address' => 'Av. Insurgentes Sur 1234',
                'district' => 'Coyoacán',
                'postal_code' => '04510',
                'phone' => '5555123456',
            ],
            [
                'city' => 'Guadalajara',
                'address' => 'Av. Vallarta 3000',
                'district' => 'Zapopan',
                'postal_code' => '45040',
                'phone' => '3336789012',
            ],
            [
                'city' => 'Monterrey',
                'address' => 'Av. Gonzalitos 500',
                'district' => 'San Nicolás',
                'postal_code' => '66450',
                'phone' => '8181234567',
            ],
        ];

        foreach ($storeData as $data) {
            // Encontrar la ciudad
            $city = $cities->firstWhere('city', $data['city']);
            
            if (!$city) {
                continue;
            }

            // Crear la dirección
            $address = Address::create([
                'address' => $data['address'],
                'district' => $data['district'],
                'city_id' => $city->city_id,
                'postal_code' => $data['postal_code'],
                'phone' => $data['phone'],
            ]);

            // Crear la tienda y obtener su instancia
            $store = Store::create([
                'manager_staff_id' => $staffMembers->random()->staff_id,
                'address_id' => $address->address_id,
            ]);

            // Actualizar la tienda del empleado que es manager
            Staff::where('staff_id', $store->manager_staff_id)
                ->update(['store_id' => $store->store_id]);
        }

        // Asignar empleados restantes a tiendas aleatoriamente
        $stores = Store::all();
        Staff::whereNull('store_id')->get()
            ->each(function ($staff) use ($stores) {
                $staff->update(['store_id' => $stores->random()->store_id]);
            });
    }
}