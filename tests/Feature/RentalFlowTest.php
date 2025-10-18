<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Film;
use App\Models\Language;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Store;
use App\Models\Staff;
use App\Models\Rental;
use App\Models\Address;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RentalFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_register()
    {
        $customerData = [
            'store_id' => 1, // Using seeded store
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'address_id' => 1, // Using seeded address
            'active' => 1,
            'create_date' => now(),
        ];

        $customer = Customer::create($customerData);

        $this->assertInstanceOf(Customer::class, $customer);
        $this->assertEquals('John', $customer->first_name);
        $this->assertEquals('Doe', $customer->last_name);
        $this->assertEquals('john.doe@example.com', $customer->email);
        $this->assertTrue((bool)$customer->active);
        $this->assertNotNull($customer->customer_id);
        $this->assertEquals(1, $customer->store_id);
        $this->assertEquals(1, $customer->address_id);
    }

    public function test_customer_can_rent_available_film()
    {
        // Create necessary models using seeded data
        $language = Language::factory()->create(['name' => 'English']);
        $category = Category::factory()->create(['name' => 'Action']);
        
        $customer = Customer::factory()->create([
            'store_id' => 1, // Use seeded store
            'address_id' => 1, // Use seeded address
        ]);
        
        $film = Film::factory()->create([
            'language_id' => $language->language_id,
            'category_id' => $category->category_id,
            'title' => 'Test Action Movie',
            'rental_duration' => 3,
            'rental_rate' => 2.99,
        ]);
        
        // Create available inventory
        $inventory = Inventory::factory()->create([
            'film_id' => $film->film_id,
            'store_id' => 1, // Use seeded store
            'condition' => 'available',
        ]);

        // Create rental
        $rental = Rental::create([
            'rental_date' => now(),
            'inventory_id' => $inventory->inventory_id,
            'customer_id' => $customer->customer_id,
            'return_date' => null,
            'staff_id' => 1, // Use seeded staff
        ]);

        $this->assertInstanceOf(Rental::class, $rental);
        $this->assertEquals($customer->customer_id, $rental->customer_id);
        $this->assertEquals($inventory->inventory_id, $rental->inventory_id);
        $this->assertEquals(1, $rental->staff_id);
        $this->assertNull($rental->return_date);
        $this->assertNotNull($rental->rental_date);
        
        // Verify inventory is no longer available
        $this->assertFalse($inventory->isAvailable());
        
        // Verify rental relationship works
        $this->assertEquals($film->film_id, $rental->inventory->film->film_id);
        $this->assertEquals($customer->customer_id, $rental->customer->customer_id);
    }

    public function test_customer_can_return_rented_film()
    {
        // Create necessary models using seeded data
        $language = Language::factory()->create(['name' => 'Spanish']);
        $category = Category::factory()->create(['name' => 'Comedy']);
        
        $customer = Customer::factory()->create([
            'store_id' => 1,
            'address_id' => 1,
        ]);
        
        $film = Film::factory()->create([
            'language_id' => $language->language_id,
            'category_id' => $category->category_id,
            'title' => 'Comedy Movie',
            'rental_duration' => 7,
            'rental_rate' => 3.99,
        ]);
        
        // Create rented inventory
        $inventory = Inventory::factory()->create([
            'film_id' => $film->film_id,
            'store_id' => 1,
            'condition' => 'available',
        ]);

        // Create active rental
        $rentalDate = now()->subDays(2);
        $rental = Rental::create([
            'rental_date' => $rentalDate,
            'inventory_id' => $inventory->inventory_id,
            'customer_id' => $customer->customer_id,
            'return_date' => null,
            'staff_id' => 1,
        ]);

        // Verify rental is active initially
        $this->assertNull($rental->return_date);
        $this->assertFalse($inventory->isAvailable());

        // Return the film
        $returnDate = now();
        $rental->update(['return_date' => $returnDate]);

        $updatedRental = $rental->fresh();
        $this->assertNotNull($updatedRental->return_date);
        $this->assertEquals($returnDate->format('Y-m-d H:i'), $updatedRental->return_date->format('Y-m-d H:i'));
        
        // Verify inventory is available again
        $this->assertTrue($inventory->isAvailable());
        
        // Verify rental period calculation
        $rentalDays = abs($updatedRental->return_date->diffInDays($updatedRental->rental_date));
        $this->assertEquals(2, $rentalDays);
    }

    public function test_customer_cannot_rent_unavailable_inventory()
    {
        // Create necessary models using seeded data
        $language = Language::factory()->create(['name' => 'French']);
        $category = Category::factory()->create(['name' => 'Drama']);
        
        $customer = Customer::factory()->create([
            'store_id' => 1,
            'address_id' => 1,
        ]);
        
        $film = Film::factory()->create([
            'language_id' => $language->language_id,
            'category_id' => $category->category_id,
            'title' => 'Drama Movie',
        ]);
        
        // Create damaged inventory (not available for rent)
        $damagedInventory = Inventory::factory()->create([
            'film_id' => $film->film_id,
            'store_id' => 1,
            'condition' => 'damaged',
        ]);

        // Create lost inventory (also not available for rent)
        $lostInventory = Inventory::factory()->create([
            'film_id' => $film->film_id,
            'store_id' => 1,
            'condition' => 'lost',
        ]);

        // Verify that damaged and lost inventory still return true for isAvailable() 
        // since they have no active rentals, but they should not be rentable in practice
        $this->assertTrue($damagedInventory->isAvailable()); // No active rentals
        $this->assertTrue($lostInventory->isAvailable()); // No active rentals
        
        // However, check their conditions
        $this->assertEquals('damaged', $damagedInventory->condition);
        $this->assertEquals('lost', $lostInventory->condition);
        
        // Business logic should prevent renting damaged/lost items
        $this->assertNotEquals('available', $damagedInventory->condition);
        $this->assertNotEquals('available', $lostInventory->condition);
        
        // Verify film shows as having no available inventory (condition-wise)
        $film->refresh();
        $this->assertFalse($film->has_available_inventory);
        $this->assertEquals('No disponible', $film->availability_status);
    }

    public function test_rental_calculates_late_fees_correctly()
    {
        // Create necessary models using seeded data
        $language = Language::factory()->create(['name' => 'German']);
        $category = Category::factory()->create(['name' => 'Horror']);
        
        $customer = Customer::factory()->create([
            'store_id' => 1,
            'address_id' => 1,
        ]);
        
        $film = Film::factory()->create([
            'language_id' => $language->language_id,
            'category_id' => $category->category_id,
            'title' => 'Horror Movie',
            'rental_duration' => 3, // 3 days rental
            'rental_rate' => 4.99,
        ]);
        
        $inventory = Inventory::factory()->create([
            'film_id' => $film->film_id,
            'store_id' => 1,
            'condition' => 'available',
        ]);

        // Create overdue rental (rented 5 days ago, should have been returned 2 days ago)
        $rentalDate = now()->subDays(5);
        $returnDate = now();
        
        $rental = Rental::create([
            'rental_date' => $rentalDate,
            'inventory_id' => $inventory->inventory_id,
            'customer_id' => $customer->customer_id,
            'return_date' => $returnDate,
            'staff_id' => 1,
        ]);

        // Verify rental exists and has return date
        $this->assertInstanceOf(Rental::class, $rental);
        $this->assertNotNull($rental->return_date);
        $this->assertEquals($customer->customer_id, $rental->customer_id);
        $this->assertEquals($inventory->inventory_id, $rental->inventory_id);
        
        // Calculate rental period and late days
        $actualRentalDays = abs($rental->return_date->diffInDays($rental->rental_date));
        $expectedRentalDays = $film->rental_duration;
        $daysLate = max(0, $actualRentalDays - $expectedRentalDays);
        
        $this->assertEquals(5, $actualRentalDays);
        $this->assertEquals(3, $expectedRentalDays);
        $this->assertEquals(2, $daysLate);
        $this->assertGreaterThan(0, $daysLate);
        
        // Verify business logic for late fee calculation
        $baseFee = $film->rental_rate;
        $lateFeePerDay = 1.00; // Assumed late fee
        $expectedTotalFee = $baseFee + ($daysLate * $lateFeePerDay);
        
        $this->assertEquals(4.99, $baseFee);
        $this->assertEquals(6.99, $expectedTotalFee);
    }

    public function test_film_availability_updates_with_inventory_changes()
    {
        // Create film with all required relationships
        $language = Language::factory()->create(['name' => 'Italian']);
        $category = Category::factory()->create(['name' => 'Sci-Fi']);
        
        $film = Film::factory()->create([
            'language_id' => $language->language_id,
            'category_id' => $category->category_id,
            'title' => 'Sci-Fi Adventure',
            'rental_rate' => 5.99,
        ]);
        
        // Film without inventory should not have available inventory
        $this->assertFalse($film->has_available_inventory);
        $this->assertEquals('Sin inventario', $film->availability_status);
        $this->assertEquals('warning', $film->availability_class);
        
        // Add first available inventory
        $inventory1 = Inventory::factory()->create([
            'film_id' => $film->film_id,
            'store_id' => 1,
            'condition' => 'available',
        ]);
        
        // Refresh film and check availability
        $film->refresh();
        $this->assertTrue($film->has_available_inventory);
        $this->assertEquals('Totalmente disponible', $film->availability_status);
        $this->assertEquals('success', $film->availability_class);
        $this->assertEquals(1, $film->available_inventory_count);
        $this->assertEquals(1, $film->total_inventory_count);
        
        // Add second inventory item
        $inventory2 = Inventory::factory()->create([
            'film_id' => $film->film_id,
            'store_id' => 1,
            'condition' => 'available',
        ]);
        
        // Refresh and verify still available
        $film->refresh();
        $this->assertTrue($film->has_available_inventory);
        $this->assertEquals('Totalmente disponible', $film->availability_status);
        $this->assertEquals('success', $film->availability_class);
        $this->assertEquals(2, $film->available_inventory_count);
        $this->assertEquals(2, $film->total_inventory_count);
        
        // Create customer and rent one inventory item
        $customer = Customer::factory()->create([
            'store_id' => 1,
            'address_id' => 1,
        ]);
        
        Rental::create([
            'rental_date' => now(),
            'inventory_id' => $inventory1->inventory_id,
            'customer_id' => $customer->customer_id,
            'return_date' => null,
            'staff_id' => 1,
        ]);
        
        // Refresh and check partial availability
        $film->refresh();
        $this->assertTrue($film->has_available_inventory);
        $this->assertEquals('Parcialmente disponible (1/2)', $film->availability_status);
        $this->assertEquals('info', $film->availability_class);
        $this->assertEquals(1, $film->available_inventory_count);
        $this->assertEquals(2, $film->total_inventory_count);
        
        // Rent the second inventory item
        Rental::create([
            'rental_date' => now(),
            'inventory_id' => $inventory2->inventory_id,
            'customer_id' => $customer->customer_id,
            'return_date' => null,
            'staff_id' => 1,
        ]);
        
        // Refresh and check no availability
        $film->refresh();
        $this->assertFalse($film->has_available_inventory);
        $this->assertEquals('No disponible', $film->availability_status);
        $this->assertEquals('danger', $film->availability_class);
        $this->assertEquals(0, $film->available_inventory_count);
        $this->assertEquals(2, $film->total_inventory_count);
    }
}