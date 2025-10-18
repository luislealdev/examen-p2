<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Payment;
use App\Models\Rental;
use App\Models\Customer;
use App\Models\Staff;
use App\Models\Film;
use Carbon\Carbon;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener algunas rentas existentes para crear pagos
        $rentals = Rental::limit(50)->get();
        $customers = Customer::limit(10)->get();
        $staff = Staff::limit(3)->get();

        if ($rentals->isEmpty() || $customers->isEmpty() || $staff->isEmpty()) {
            $this->command->info('No hay datos suficientes para crear pagos. Se necesitan rentas, clientes y staff.');
            return;
        }

        // Crear pagos para las rentas existentes
        foreach ($rentals as $rental) {
            // Obtener film para calcular precio de renta
            $inventory = $rental->inventory;
            if ($inventory && $inventory->film) {
                $film = $inventory->film;
                $amount = $film->rental_rate ?? 2.99;
                
                // Agregar fees por rentas tardías ocasionalmente
                if (rand(1, 5) == 1) { // 20% de probabilidad
                    $amount += rand(1, 5); // Late fee de $1-5
                }

                Payment::create([
                    'customer_id' => $rental->customer_id,
                    'staff_id' => $rental->staff_id,
                    'rental_id' => $rental->rental_id,
                    'amount' => $amount,
                    'payment_date' => $rental->rental_date,
                    'last_update' => now(),
                ]);
            }
        }

        // Crear algunos pagos adicionales aleatorios para mejor estadística
        for ($i = 0; $i < 100; $i++) {
            $customer = $customers->random();
            $staffMember = $staff->random();
            
            // Crear pagos con fechas variadas en el último año
            $paymentDate = Carbon::now()->subDays(rand(1, 365));
            
            Payment::create([
                'customer_id' => $customer->customer_id,
                'staff_id' => $staffMember->staff_id,
                'rental_id' => null, // Algunos pagos sin rental específico (fees, etc.)
                'amount' => rand(150, 799) / 100, // $1.50 - $7.99
                'payment_date' => $paymentDate,
                'last_update' => $paymentDate,
            ]);
        }

        $this->command->info('Pagos creados exitosamente.');
    }
}
