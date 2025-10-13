<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SimpleRentalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar que existan datos base
        $customersCount = DB::table('customers')->count();
        $inventoryCount = DB::table('inventory')->count();
        $staffCount = DB::table('staff')->count();

        if ($customersCount === 0 || $inventoryCount === 0 || $staffCount === 0) {
            $this->command->error('No hay suficientes datos base para crear rentas.');
            $this->command->info("Customers: $customersCount, Inventory: $inventoryCount, Staff: $staffCount");
            return;
        }

        // Limpiar rentas existentes
        DB::table('rentals')->truncate();

        // Obtener algunos IDs para crear rentas
        $customerIds = DB::table('customers')->pluck('customer_id')->toArray();
        $inventoryIds = DB::table('inventory')->pluck('inventory_id')->toArray();
        $staffIds = DB::table('staff')->pluck('staff_id')->toArray();

        $rentals = [];
        $statuses = ['active', 'returned', 'overdue'];

        // Crear 50 rentas de prueba
        for ($i = 0; $i < 50; $i++) {
            $rentalDate = Carbon::now()->subDays(rand(1, 90));
            $status = $statuses[array_rand($statuses)];
            $returnDate = null;

            if ($status === 'returned') {
                $returnDate = $rentalDate->copy()->addDays(rand(1, 7));
            }

            $rentals[] = [
                'inventory_id' => $inventoryIds[array_rand($inventoryIds)],
                'customer_id' => $customerIds[array_rand($customerIds)],
                'staff_id' => $staffIds[array_rand($staffIds)],
                'rental_date' => $rentalDate->format('Y-m-d H:i:s'),
                'return_date' => $returnDate?->format('Y-m-d H:i:s'),
                'rental_amount' => rand(199, 799) / 100, // $1.99 a $7.99
                'status' => $status,
                'notes' => rand(1, 10) > 7 ? 'Renta de prueba generada automáticamente' : null,
                'last_update' => $rentalDate->format('Y-m-d H:i:s'),
            ];
        }

        DB::table('rentals')->insert($rentals);
        
        $this->command->info('Se crearon ' . count($rentals) . ' rentas de prueba exitosamente.');
    }
}