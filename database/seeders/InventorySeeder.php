<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Inventory;
use App\Models\Film;
use App\Models\Store;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all films and stores
        $films = Film::all();
        $stores = Store::all();

        if ($films->isEmpty() || $stores->isEmpty()) {
            $this->command->warn('No films or stores found. Please seed films and stores first.');
            return;
        }

        $this->command->info('Creating inventory items...');

        // Create inventory items for each film in each store
        foreach ($films as $film) {
            foreach ($stores as $store) {
                // Create 1-3 copies of each film in each store
                $copies = rand(1, 3);
                
                for ($i = 0; $i < $copies; $i++) {
                    Inventory::create([
                        'film_id' => $film->film_id,
                        'store_id' => $store->store_id,
                    ]);
                }
            }
        }

        $totalInventory = Inventory::count();
        $this->command->info("Created {$totalInventory} inventory items successfully!");

        // Display summary
        $this->command->table(
            ['Store ID', 'Items', 'Unique Films'],
            $stores->map(function ($store) {
                $items = Inventory::where('store_id', $store->store_id)->count();
                $uniqueFilms = Inventory::where('store_id', $store->store_id)
                                     ->distinct('film_id')
                                     ->count('film_id');
                return [
                    $store->store_id,
                    $items,
                    $uniqueFilms
                ];
            })->toArray()
        );
    }
}