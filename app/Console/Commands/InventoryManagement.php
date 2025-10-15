<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Inventory;
use App\Models\Film;
use App\Models\Store;

class InventoryManagement extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventory:manage 
                            {action : Action to perform (stats, add, list, remove, bulk-add)}
                            {--film= : Film ID for add/remove operations}
                            {--store= : Store ID for operations}
                            {--quantity=1 : Quantity for bulk operations}
                            {--all-stores : Apply to all stores}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage inventory items from command line';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');

        switch ($action) {
            case 'stats':
                $this->showStatistics();
                break;
            case 'add':
                $this->addInventoryItem();
                break;
            case 'list':
                $this->listInventory();
                break;
            case 'remove':
                $this->removeInventoryItem();
                break;
            case 'bulk-add':
                $this->bulkAddItems();
                break;
            default:
                $this->error("Unknown action: {$action}");
                $this->info('Available actions: stats, add, list, remove, bulk-add');
                return 1;
        }

        return 0;
    }

    /**
     * Show inventory statistics.
     */
    private function showStatistics()
    {
        $stats = Inventory::getStatistics();
        
        $this->info('📊 Inventory Statistics');
        $this->line('');
        
        $this->table([
            'Metric',
            'Value'
        ], [
            ['Total Items', number_format($stats['total_items'])],
            ['Recent Additions (7 days)', number_format($stats['recent_additions'])],
            ['High Value Items', number_format($stats['high_value_items'])],
            ['Average Rental Rate', '$' . number_format($stats['avg_rental_rate'], 2)],
        ]);

        // Store distribution
        if (!empty($stats['by_store'])) {
            $this->line('');
            $this->info('🏪 Inventory by Store');
            $storeData = [];
            foreach ($stats['by_store'] as $storeId => $count) {
                $storeData[] = ["Store #{$storeId}", number_format($count)];
            }
            $this->table(['Store', 'Items'], $storeData);
        }

        // Rating distribution
        if (!empty($stats['by_rating'])) {
            $this->line('');
            $this->info('⭐ Inventory by Rating');
            $ratingData = [];
            foreach ($stats['by_rating'] as $rating => $count) {
                $ratingData[] = [$rating, number_format($count)];
            }
            $this->table(['Rating', 'Items'], $ratingData);
        }
    }

    /**
     * Add a single inventory item.
     */
    private function addInventoryItem()
    {
        $filmId = $this->option('film');
        $storeId = $this->option('store');

        if (!$filmId) {
            $films = Film::orderBy('title')->get(['film_id', 'title', 'release_year']);
            $filmChoices = $films->mapWithKeys(function ($film) {
                return [$film->film_id => "{$film->title} ({$film->release_year})"];
            })->toArray();
            
            $filmId = $this->choice('Select a film:', $filmChoices);
        }

        if (!$storeId) {
            $stores = Store::orderBy('store_id')->get(['store_id']);
            $storeChoices = $stores->mapWithKeys(function ($store) {
                return [$store->store_id => "Store #{$store->store_id}"];
            })->toArray();
            
            $storeId = $this->choice('Select a store:', $storeChoices);
        }

        // Validate film and store exist
        $film = Film::find($filmId);
        $store = Store::find($storeId);

        if (!$film) {
            $this->error("Film with ID {$filmId} not found");
            return;
        }

        if (!$store) {
            $this->error("Store with ID {$storeId} not found");
            return;
        }

        // Create inventory item
        $inventory = Inventory::create([
            'film_id' => $filmId,
            'store_id' => $storeId,
        ]);

        $this->info("✅ Added inventory item #{$inventory->inventory_id}");
        $this->line("   Film: {$film->title}");
        $this->line("   Store: #{$store->store_id}");
    }

    /**
     * List inventory items.
     */
    private function listInventory()
    {
        $storeId = $this->option('store');
        $filmId = $this->option('film');

        $query = Inventory::with(['film', 'store']);

        if ($storeId) {
            $query->where('store_id', $storeId);
        }

        if ($filmId) {
            $query->where('film_id', $filmId);
        }

        $inventories = $query->orderBy('last_update', 'desc')->limit(50)->get();

        if ($inventories->isEmpty()) {
            $this->warn('No inventory items found.');
            return;
        }

        $tableData = $inventories->map(function ($inventory) {
            return [
                $inventory->inventory_id,
                $inventory->film->title ?? 'N/A',
                "#{$inventory->store_id}",
                $inventory->film->rating ?? 'N/A',
                '$' . number_format($inventory->film->rental_rate ?? 0, 2),
                $inventory->last_update->format('M j, Y'),
            ];
        })->toArray();

        $this->table([
            'ID',
            'Film',
            'Store',
            'Rating',
            'Rate',
            'Added'
        ], $tableData);

        if ($inventories->count() === 50) {
            $this->info('Showing first 50 items. Use --film or --store options to filter.');
        }
    }

    /**
     * Remove inventory items.
     */
    private function removeInventoryItem()
    {
        $filmId = $this->option('film');
        $storeId = $this->option('store');

        if (!$filmId || !$storeId) {
            $this->error('Both --film and --store options are required for remove action');
            return;
        }

        $items = Inventory::where('film_id', $filmId)
                         ->where('store_id', $storeId)
                         ->get();

        if ($items->isEmpty()) {
            $this->warn("No inventory items found for film {$filmId} in store {$storeId}");
            return;
        }

        $film = Film::find($filmId);
        $store = Store::find($storeId);

        $this->info("Found {$items->count()} item(s) for:");
        $this->line("  Film: " . ($film->title ?? "ID {$filmId}"));
        $this->line("  Store: #{$storeId}");

        if ($this->confirm('Do you want to remove ALL these items?')) {
            $count = $items->count();
            Inventory::where('film_id', $filmId)
                    ->where('store_id', $storeId)
                    ->delete();
            
            $this->info("✅ Removed {$count} inventory item(s)");
        } else {
            $this->info('Operation cancelled');
        }
    }

    /**
     * Bulk add inventory items.
     */
    private function bulkAddItems()
    {
        $filmId = $this->option('film');
        $storeId = $this->option('store');
        $quantity = $this->option('quantity');
        $allStores = $this->option('all-stores');

        if (!$filmId) {
            $this->error('--film option is required for bulk-add action');
            return;
        }

        $film = Film::find($filmId);
        if (!$film) {
            $this->error("Film with ID {$filmId} not found");
            return;
        }

        $stores = collect();
        
        if ($allStores) {
            $stores = Store::all();
        } elseif ($storeId) {
            $store = Store::find($storeId);
            if (!$store) {
                $this->error("Store with ID {$storeId} not found");
                return;
            }
            $stores->push($store);
        } else {
            $this->error('Either --store or --all-stores option is required');
            return;
        }

        $this->info("Bulk adding {$quantity} copies of '{$film->title}' to {$stores->count()} store(s)");
        
        if (!$this->confirm('Continue?')) {
            $this->info('Operation cancelled');
            return;
        }

        $totalAdded = 0;
        $bar = $this->output->createProgressBar($stores->count() * $quantity);

        foreach ($stores as $store) {
            for ($i = 0; $i < $quantity; $i++) {
                Inventory::create([
                    'film_id' => $filmId,
                    'store_id' => $store->store_id,
                ]);
                $totalAdded++;
                $bar->advance();
            }
        }

        $bar->finish();
        $this->line('');
        $this->info("✅ Added {$totalAdded} inventory items successfully!");
    }
}
