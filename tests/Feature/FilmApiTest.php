<?php

namespace Tests\Feature;

use App\Models\Film;
use App\Models\Language;
use App\Models\Category;
use App\Models\Inventory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilmApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_films_list_via_api()
    {
        // Create test data
        $language = Language::factory()->create(['name' => 'English']);
        $category = Category::factory()->create(['name' => 'Action']);
        
        $film1 = Film::factory()->create([
            'title' => 'Test Movie 1',
            'language_id' => $language->language_id,
            'category_id' => $category->category_id,
            'rental_rate' => 2.99,
            'rating' => 'PG',
        ]);
        
        $film2 = Film::factory()->create([
            'title' => 'Test Movie 2',
            'language_id' => $language->language_id,
            'category_id' => $category->category_id,
            'rental_rate' => 3.99,
            'rating' => 'PG-13',
        ]);

        // Test API endpoint with correct route
        $response = $this->getJson('/api/v1/films');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',  // API uses 'id' not 'film_id'
                            'title',
                            'description',
                            'release_year',
                            'rental_rate',
                            'rating',
                            'language',
                            'category',
                        ]
                    ],
                    'links',
                    'meta'
                ]);

        $data = $response->json('data');
        $this->assertCount(2, $data);
        
        // Verify film data
        $this->assertEquals('Test Movie 1', $data[0]['title']);
        $this->assertEquals('Test Movie 2', $data[1]['title']);
        $this->assertEquals('English', $data[0]['language']['name']);
        $this->assertEquals('Action', $data[0]['category']['name']);
    }

    public function test_can_search_films_via_api()
    {
        // Create test data
        $language = Language::factory()->create(['name' => 'Spanish']);
        $category = Category::factory()->create(['name' => 'Comedy']);
        
        $film1 = Film::factory()->create([
            'title' => 'Action Hero Adventure',
            'description' => 'An exciting action movie',
            'language_id' => $language->language_id,
            'category_id' => $category->category_id,
            'rental_rate' => 4.99,
        ]);
        
        $film2 = Film::factory()->create([
            'title' => 'Comedy Show',
            'description' => 'A funny comedy',
            'language_id' => $language->language_id,
            'category_id' => $category->category_id,
            'rental_rate' => 3.99,
        ]);

        // Test search functionality by title
        $response = $this->getJson('/api/v1/films?search=action');

        $response->assertStatus(200);
        
        $films = $response->json('data');
        $this->assertCount(1, $films);
        $this->assertEquals('Action Hero Adventure', $films[0]['title']);
        
        // Test search functionality by description
        $response = $this->getJson('/api/v1/films?search=funny');
        
        $response->assertStatus(200);
        $films = $response->json('data');
        $this->assertCount(1, $films);
        $this->assertEquals('Comedy Show', $films[0]['title']);
    }

    public function test_can_filter_films_by_availability_via_api()
    {
        // Create test data
        $language = Language::factory()->create(['name' => 'French']);
        $category = Category::factory()->create(['name' => 'Drama']);
        
        $film1 = Film::factory()->create([
            'title' => 'Available Movie',
            'language_id' => $language->language_id,
            'category_id' => $category->category_id,
            'rental_rate' => 2.99,
        ]);
        
        $film2 = Film::factory()->create([
            'title' => 'Unavailable Movie',
            'language_id' => $language->language_id,
            'category_id' => $category->category_id,
            'rental_rate' => 4.99,
        ]);

        // Add available inventory to film1 only
        Inventory::factory()->create([
            'film_id' => $film1->film_id,
            'store_id' => 1,
            'condition' => 'available',
        ]);

        // Add damaged inventory to film2 (not available for rent)
        Inventory::factory()->create([
            'film_id' => $film2->film_id,
            'store_id' => 1,
            'condition' => 'damaged',
        ]);

        // Test availability filter - should return only films with available inventory
        $response = $this->getJson('/api/v1/films?availability=available');

        $response->assertStatus(200);
        
        $films = $response->json('data');
        $this->assertCount(1, $films);
        $this->assertEquals('Available Movie', $films[0]['title']);
        
        // Test unavailable filter
        $response = $this->getJson('/api/v1/films?availability=unavailable');
        
        $response->assertStatus(200);
        $films = $response->json('data');
        $this->assertCount(1, $films);
        $this->assertEquals('Unavailable Movie', $films[0]['title']);
    }

    public function test_can_get_single_film_via_api()
    {
        // Create test data
        $language = Language::factory()->create(['name' => 'Spanish']);
        $category = Category::factory()->create(['name' => 'Comedy']);
        
        $film = Film::factory()->create([
            'title' => 'Test Movie',
            'description' => 'A test movie description',
            'release_year' => 2023,
            'language_id' => $language->language_id,
            'category_id' => $category->category_id,
            'rental_duration' => 5,
            'rental_rate' => 2.99,
            'length' => 120,
            'replacement_cost' => 19.99,
            'rating' => 'PG',
        ]);

        // Add inventory to test counts
        Inventory::factory()->count(3)->create([
            'film_id' => $film->film_id,
            'store_id' => 1,
            'condition' => 'available',
        ]);

        // Test single film endpoint with correct API path
        $response = $this->getJson("/api/v1/films/{$film->film_id}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        'id',  // API uses 'id' not 'film_id'
                        'title',
                        'description',
                        'release_year',
                        'rental_duration',
                        'rental_rate',
                        'length',
                        'replacement_cost',
                        'rating',
                        'language',
                        'category',
                    ]
                ]);

        $filmData = $response->json('data');
        $this->assertEquals('Test Movie', $filmData['title']);
        $this->assertEquals('A test movie description', $filmData['description']);
        $this->assertEquals(2023, $filmData['release_year']);
        $this->assertEquals(5, $filmData['rental_duration']);
        $this->assertEquals(2.99, $filmData['rental_rate']);
        $this->assertEquals(120, $filmData['length']);
        $this->assertEquals(19.99, $filmData['replacement_cost']);
        $this->assertEquals('PG', $filmData['rating']);
        $this->assertEquals('Spanish', $filmData['language']['name']);
        $this->assertEquals('Comedy', $filmData['category']['name']);
        // Note: FilmResource doesn't include inventory counts in basic response
    }

    public function test_api_returns_404_for_nonexistent_film()
    {
        $response = $this->getJson('/api/v1/films/999999');

        $response->assertStatus(404);
        // Laravel returns a standard "No query results" message for model not found
        $response->assertJsonFragment([
            'message' => 'No query results for model [App\\Models\\Film] 999999'
        ]);
    }

    public function test_can_get_inventory_list_via_api()
    {
        // Create test data
        $language = Language::factory()->create(['name' => 'German']);
        $category = Category::factory()->create(['name' => 'Horror']);
        $film = Film::factory()->create([
            'title' => 'Inventory Test Film',
            'language_id' => $language->language_id,
            'category_id' => $category->category_id,
            'rental_rate' => 3.99,
            'rating' => 'R',
        ]);

        // Create multiple inventory items with different conditions
        $availableInventory = Inventory::factory()->create([
            'film_id' => $film->film_id,
            'store_id' => 1,
            'condition' => 'available',
        ]);

        $damagedInventory = Inventory::factory()->create([
            'film_id' => $film->film_id,
            'store_id' => 1,
            'condition' => 'damaged',
        ]);

        // Test inventory API endpoint with correct path
        $response = $this->getJson('/api/v1/inventory');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',  // InventoryResource uses 'id' not 'inventory_id'
                            'film_id',
                            'store_id',
                            'condition',
                            'is_available',
                            'film',
                        ]
                    ]
                ]);

        $inventoryData = $response->json('data');
        $this->assertCount(2, $inventoryData);
        
        // Verify film relationship is included
        $this->assertEquals('Inventory Test Film', $inventoryData[0]['film']['title']);
        
        // Test available filter
        $response = $this->getJson('/api/v1/inventory?condition=available');
        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
    }
}