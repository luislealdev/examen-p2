<?php

namespace Tests\Unit;

use App\Models\Film;
use App\Models\Language;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Store;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilmTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_a_film_with_required_attributes()
    {
        $language = Language::factory()->create(['name' => 'English']);
        $category = Category::factory()->create(['name' => 'Action']);
        
        $film = Film::create([
            'title' => 'Test Movie',
            'description' => 'A test movie description',
            'release_year' => 2023,
            'language_id' => $language->language_id,
            'rental_duration' => 3,
            'rental_rate' => 4.99,
            'replacement_cost' => 19.99,
            'rating' => 'PG',
            'category_id' => $category->category_id,
        ]);

        $this->assertEquals('Test Movie', $film->title);
        $this->assertEquals(4.99, $film->rental_rate);
        $this->assertEquals('PG', $film->rating);
    }

    public function test_belongs_to_a_language()
    {
        $language = Language::factory()->create(['name' => 'English']);
        $film = Film::factory()->create(['language_id' => $language->language_id]);
        
        $this->assertInstanceOf(Language::class, $film->language);
        $this->assertEquals('English', $film->language->name);
    }

    public function test_belongs_to_a_category()
    {
        $category = Category::factory()->create(['name' => 'Action']);
        $film = Film::factory()->create(['category_id' => $category->category_id]);
        
        $this->assertInstanceOf(Category::class, $film->category);
        $this->assertEquals('Action', $film->category->name);
    }

    public function test_can_have_inventory_items()
    {
        $film = Film::factory()->create();
        $store = Store::factory()->create();
        
        Inventory::factory()->create([
            'film_id' => $film->film_id,
            'store_id' => $store->store_id,
            'condition' => 'available',
        ]);
        
        $this->assertCount(1, $film->inventory);
        $this->assertInstanceOf(Inventory::class, $film->inventory->first());
    }

    public function test_can_check_if_has_available_inventory()
    {
        $film = Film::factory()->create();
        $store = Store::factory()->create();
        
        // Film without inventory should not have available inventory
        $this->assertFalse($film->has_available_inventory);
        
        // Create available inventory (no active rentals)
        Inventory::factory()->create([
            'film_id' => $film->film_id,
            'store_id' => $store->store_id,
            'condition' => 'available',
        ]);
        
        // Refresh the film to load the inventory
        $film->refresh();
        $this->assertTrue($film->has_available_inventory);
    }

    public function test_can_get_availability_status_correctly()
    {
        $film = Film::factory()->create();
        $store = Store::factory()->create();
        
        // Film with no inventory should be 'Sin inventario'
        $this->assertEquals('Sin inventario', $film->availability_status);
        
        // Add inventory
        Inventory::factory()->create([
            'film_id' => $film->film_id,
            'store_id' => $store->store_id,
            'condition' => 'available',
        ]);
        
        // Refresh the film to load the inventory
        $film->refresh();
        $this->assertEquals('Totalmente disponible', $film->availability_status);
    }

    public function test_can_get_availability_class_for_ui()
    {
        $film = Film::factory()->create();
        $store = Store::factory()->create();
        
        // Film with no inventory should have 'warning' class
        $this->assertEquals('warning', $film->availability_class);
        
        // Add inventory
        Inventory::factory()->create([
            'film_id' => $film->film_id,
            'store_id' => $store->store_id,
            'condition' => 'available',
        ]);
        
        // Refresh the film to load the inventory
        $film->refresh();
        $this->assertEquals('success', $film->availability_class);
    }

    public function test_can_scope_films_with_available_inventory()
    {
        $film1 = Film::factory()->create();
        $film2 = Film::factory()->create();
        $store = Store::factory()->create();
        
        // Only film1 has inventory
        Inventory::factory()->create([
            'film_id' => $film1->film_id,
            'store_id' => $store->store_id,
            'condition' => 'available',
        ]);
        
        $availableFilms = Film::withAvailableInventory()->get();
        
        $this->assertCount(1, $availableFilms);
        $this->assertEquals($film1->film_id, $availableFilms->first()->film_id);
    }

    public function test_can_get_age_category_correctly()
    {
        $currentYear = now()->year;
        
        $newRelease = Film::factory()->create(['release_year' => $currentYear]);
        $recentFilm = Film::factory()->create(['release_year' => $currentYear - 8]);
        $classicFilm = Film::factory()->create(['release_year' => $currentYear - 20]);
        $vintageFilm = Film::factory()->create(['release_year' => $currentYear - 35]);
        
        $this->assertEquals('New Release', $newRelease->age_category);
        $this->assertEquals('Recent', $recentFilm->age_category);
        $this->assertEquals('Classic', $classicFilm->age_category);
        $this->assertEquals('Vintage', $vintageFilm->age_category);
    }

    public function test_can_search_films_by_title_and_description()
    {
        $film1 = Film::factory()->create([
            'title' => 'Action Hero',
            'description' => 'A thrilling adventure movie'
        ]);
        $film2 = Film::factory()->create([
            'title' => 'Romantic Comedy',
            'description' => 'A funny love story'
        ]);
        
        $searchResults = Film::search('action')->get();
        
        $this->assertCount(1, $searchResults);
        $this->assertEquals($film1->film_id, $searchResults->first()->film_id);
        
        $searchResults = Film::search('love')->get();
        
        $this->assertCount(1, $searchResults);
        $this->assertEquals($film2->film_id, $searchResults->first()->film_id);
    }
}