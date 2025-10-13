<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Store;
use App\Models\Customer;
use App\Models\Staff;
use App\Models\Film;
use App\Models\Language;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Rental;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RentalTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create languages
        $english = Language::firstOrCreate(['name' => 'English']);
        $spanish = Language::firstOrCreate(['name' => 'Spanish']);

        // Create categories
        $action = Category::firstOrCreate(['name' => 'Action']);
        $comedy = Category::firstOrCreate(['name' => 'Comedy']);
        $drama = Category::firstOrCreate(['name' => 'Drama']);

        // Create stores
        $store1 = Store::create([
            'manager_staff_id' => 1, // We'll create staff later
            'address_id' => 1
        ]);

        $store2 = Store::create([
            'manager_staff_id' => 2,
            'address_id' => 2
        ]);

        // Create staff
        $staff1 = Staff::create([
            'first_name' => 'John',
            'last_name' => 'Manager',
            'email' => 'john.manager@sakila.com',
            'store_id' => $store1->store_id,
            'active' => true,
            'username' => 'john.manager',
            'password' => bcrypt('password')
        ]);

        $staff2 = Staff::create([
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane.smith@sakila.com',
            'store_id' => $store2->store_id,
            'active' => true,
            'username' => 'jane.smith',
            'password' => bcrypt('password')
        ]);

        // Update stores with correct manager IDs
        $store1->update(['manager_staff_id' => $staff1->staff_id]);
        $store2->update(['manager_staff_id' => $staff2->staff_id]);

        // Create customers
        $customers = [
            [
                'first_name' => 'Alice',
                'last_name' => 'Johnson',
                'email' => 'alice.johnson@email.com',
                'store_id' => $store1->store_id,
                'active' => true
            ],
            [
                'first_name' => 'Bob',
                'last_name' => 'Williams',
                'email' => 'bob.williams@email.com',
                'store_id' => $store1->store_id,
                'active' => true
            ],
            [
                'first_name' => 'Carol',
                'last_name' => 'Davis',
                'email' => 'carol.davis@email.com',
                'store_id' => $store2->store_id,
                'active' => true
            ],
            [
                'first_name' => 'David',
                'last_name' => 'Miller',
                'email' => 'david.miller@email.com',
                'store_id' => $store2->store_id,
                'active' => false // Inactive customer for testing
            ]
        ];

        foreach ($customers as $customerData) {
            Customer::create($customerData);
        }

        // Create films
        $films = [
            [
                'title' => 'The Action Hero',
                'description' => 'An exciting action movie with lots of adventure.',
                'release_year' => 2023,
                'language_id' => $english->language_id,
                'rental_duration' => 3,
                'rental_rate' => 4.99,
                'length' => 120,
                'replacement_cost' => 19.99,
                'rating' => 'PG-13'
            ],
            [
                'title' => 'Comedy Gold',
                'description' => 'A hilarious comedy that will make you laugh.',
                'release_year' => 2022,
                'language_id' => $english->language_id,
                'rental_duration' => 5,
                'rental_rate' => 3.99,
                'length' => 95,
                'replacement_cost' => 15.99,
                'rating' => 'PG'
            ],
            [
                'title' => 'Drama Masterpiece',
                'description' => 'A touching drama about human relationships.',
                'release_year' => 2023,
                'language_id' => $english->language_id,
                'rental_duration' => 7,
                'rental_rate' => 5.99,
                'length' => 140,
                'replacement_cost' => 24.99,
                'rating' => 'R'
            ],
            [
                'title' => 'Aventura Española',
                'description' => 'Una película de aventuras en español.',
                'release_year' => 2023,
                'language_id' => $spanish->language_id,
                'rental_duration' => 4,
                'rental_rate' => 4.99,
                'length' => 110,
                'replacement_cost' => 18.99,
                'rating' => 'PG-13'
            ]
        ];

        foreach ($films as $filmData) {
            Film::create($filmData);
        }

        // Create inventory
        $allFilms = Film::all();
        $allStores = Store::all();

        foreach ($allFilms as $film) {
            foreach ($allStores as $store) {
                // Create 2-4 copies of each film in each store
                $copies = rand(2, 4);
                for ($i = 0; $i < $copies; $i++) {
                    Inventory::create([
                        'film_id' => $film->film_id,
                        'store_id' => $store->store_id
                    ]);
                }
            }
        }

        // Create some sample rentals
        $allCustomers = Customer::where('active', true)->get();
        $allInventory = Inventory::with('film')->get();
        $allStaff = Staff::all();

        // Create active rentals
        foreach ($allCustomers->take(3) as $customer) {
            $inventory = $allInventory->where('store_id', $customer->store_id)->random();
            
            Rental::create([
                'inventory_id' => $inventory->inventory_id,
                'customer_id' => $customer->customer_id,
                'staff_id' => $allStaff->where('store_id', $customer->store_id)->first()->staff_id,
                'rental_date' => Carbon::now()->subDays(rand(1, 5)),
                'due_date' => Carbon::now()->addDays(rand(1, 3)),
                'rental_amount' => $inventory->film->rental_rate,
                'status' => 'active'
            ]);
        }

        // Create overdue rentals
        foreach ($allCustomers->take(2) as $customer) {
            $inventory = $allInventory->where('store_id', $customer->store_id)->random();
            
            $rental = Rental::create([
                'inventory_id' => $inventory->inventory_id,
                'customer_id' => $customer->customer_id,
                'staff_id' => $allStaff->where('store_id', $customer->store_id)->first()->staff_id,
                'rental_date' => Carbon::now()->subDays(rand(8, 15)),
                'due_date' => Carbon::now()->subDays(rand(2, 7)),
                'rental_amount' => $inventory->film->rental_rate,
                'status' => 'active'
            ]);

            // Apply late fee for overdue rental
            $rental->applyLateFee();
        }

        // Create returned rentals
        foreach ($allCustomers as $customer) {
            $inventory = $allInventory->where('store_id', $customer->store_id)->random();
            
            $rentalDate = Carbon::now()->subDays(rand(15, 30));
            $dueDate = $rentalDate->copy()->addDays(rand(3, 7));
            $returnDate = $dueDate->copy()->addDays(rand(-2, 3)); // Some returned late, some early
            
            $rental = Rental::create([
                'inventory_id' => $inventory->inventory_id,
                'customer_id' => $customer->customer_id,
                'staff_id' => $allStaff->where('store_id', $customer->store_id)->first()->staff_id,
                'rental_date' => $rentalDate,
                'due_date' => $dueDate,
                'return_date' => $returnDate,
                'rental_amount' => $inventory->film->rental_rate,
                'status' => 'returned'
            ]);

            // Apply late fee if returned late
            if ($returnDate->isAfter($dueDate)) {
                $rental->applyLateFee();
            }
        }

        $this->command->info('Rental test data created successfully!');
        $this->command->info('Created:');
        $this->command->info('- ' . Language::count() . ' languages');
        $this->command->info('- ' . Category::count() . ' categories');
        $this->command->info('- ' . Store::count() . ' stores');
        $this->command->info('- ' . Staff::count() . ' staff members');
        $this->command->info('- ' . Customer::count() . ' customers');
        $this->command->info('- ' . Film::count() . ' films');
        $this->command->info('- ' . Inventory::count() . ' inventory items');
        $this->command->info('- ' . Rental::count() . ' rentals');
        $this->command->info('- ' . Rental::where('status', 'active')->count() . ' active rentals');
        $this->command->info('- ' . Rental::whereRaw('status = "active" AND due_date < datetime("now")')->count() . ' overdue rentals');
    }
}
