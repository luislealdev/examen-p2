<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rental;
use App\Models\Customer;
use App\Models\Inventory;
use App\Models\Staff;
use Carbon\Carbon;

class RentalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing data
        $customers = Customer::all();
        $inventories = Inventory::with('film')->get();
        $staff = Staff::all();

        if ($customers->isEmpty() || $inventories->isEmpty() || $staff->isEmpty()) {
            $this->command->info('No hay suficientes datos base para crear rentas. Asegúrate de tener customers, inventory y staff.');
            return;
        }

        $statuses = ['active', 'returned', 'overdue'];
        $rentals = [];

        // Crear rentas de los últimos 6 meses
        for ($i = 0; $i < 200; $i++) {
            $inventory = $inventories->random();
            $customer = $customers->random();
            $staffMember = $staff->random();
            
            // Fecha de renta aleatoria en los últimos 6 meses
            $rentalDate = Carbon::now()->subDays(rand(1, 180));
            
            // Determinar estado y fecha de devolución
            $status = $statuses[array_rand($statuses)];
            $returnDate = null;
            
            if ($status === 'returned') {
                // Si está devuelta, generar fecha de devolución
                $returnDate = $rentalDate->copy()->addDays(rand(1, $inventory->film->rental_duration + 3));
            } elseif ($status === 'overdue') {
                // Si está vencida, no tiene fecha de devolución y pasó el plazo
                $dueDate = $rentalDate->copy()->addDays($inventory->film->rental_duration);
                if ($dueDate->isFuture()) {
                    $status = 'active'; // Si no ha vencido, cambiar a activo
                }
            }

            $rentals[] = [
                'inventory_id' => $inventory->inventory_id,
                'customer_id' => $customer->customer_id,
                'staff_id' => $staffMember->staff_id,
                'rental_date' => $rentalDate,
                'return_date' => $returnDate,
                'rental_amount' => $inventory->film->rental_rate,
                'status' => $status,
                'notes' => rand(1, 10) > 8 ? 'Rental procesado automáticamente' : null,
                'last_update' => $rentalDate,
            ];
        }

        // Insertar en lotes para mejor rendimiento
        Rental::insert($rentals);
        
        $this->command->info('Se crearon ' . count($rentals) . ' rentas de prueba.');
    }
}