<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Film;
use App\Models\Store;
use App\Models\Category;
use App\Models\Language;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Inventory::with(['film.language', 'film.category', 'store']);

        // Search functionality
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by film
        if ($request->filled('film_id')) {
            $query->byFilm($request->film_id);
        }

        // Filter by store
        if ($request->filled('store_id')) {
            $query->byStore($request->store_id);
        }

        // Filter by film rating
        if ($request->filled('rating')) {
            $query->byFilmRating($request->rating);
        }

        // Filter by film category
        if ($request->filled('category_id')) {
            $query->byFilmCategory($request->category_id);
        }

        // Filter by film language
        if ($request->filled('language_id')) {
            $query->byFilmLanguage($request->language_id);
        }

        // Filter by recent additions
        if ($request->filled('recent_days')) {
            $query->recent($request->recent_days);
        }

        // Filter by high value items
        if ($request->filled('high_value')) {
            $query->highValue();
        }

        // Sorting
        $sortBy = $request->get('sort', 'alphabetical');
        $sortDirection = $request->get('direction', 'asc');
        
        switch ($sortBy) {
            case 'alphabetical':
                $query->alphabetical();
                break;
            case 'newest':
                $query->newest();
                break;
            case 'oldest':
                $query->oldest();
                break;
            case 'inventory_id':
                $query->orderBy('inventory_id', $sortDirection);
                break;
            case 'store_id':
                $query->orderBy('store_id', $sortDirection);
                break;
            default:
                $query->alphabetical();
        }

        // Pagination with request parameters preserved
        $inventories = $query->paginate(20)->withQueryString();

        // Get filter data
        $films = Film::with('language')->orderBy('title')->get();
        $stores = Store::orderBy('store_id')->get();
        $categories = Category::alphabetical()->get();
        $languages = Language::alphabetical()->get();
        $ratings = Film::RATINGS;

        // Get statistics
        $stats = Inventory::getStatistics();

        return view('inventories.index', compact(
            'inventories', 'films', 'stores', 'categories', 'languages', 'ratings', 'stats'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('inventories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'film_id' => 'required|exists:film,film_id',
            'store_id' => 'required|exists:stores,store_id',
        ]);

        $inventory = Inventory::create($validated);

        return redirect()->route('inventories.index')
            ->with('success', "Inventory item #{$inventory->inventory_id} created successfully!");
    }

    /**
     * Display the specified resource.
     */
    public function show(Inventory $inventory): View
    {
        $inventory->load(['film.language', 'film.category', 'store']);
        
        return view('inventories.show', compact('inventory'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Inventory $inventory): View
    {
        $inventory->load(['film', 'store']);
        $films = Film::with(['language', 'category'])
                    ->orderBy('title')
                    ->get();
        $stores = Store::orderBy('store_id')->get();

        return view('inventories.edit', compact('inventory', 'films', 'stores'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Inventory $inventory): RedirectResponse
    {
        $validated = $request->validate([
            'film_id' => 'required|exists:film,film_id',
            'store_id' => 'required|exists:store,store_id',
        ]);

        $inventory->update($validated);

        return redirect()->route('inventories.index')
            ->with('success', "Inventory item #{$inventory->inventory_id} updated successfully!");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Inventory $inventory): RedirectResponse
    {
        $inventoryId = $inventory->inventory_id;
        
        $inventory->delete();

        return redirect()->route('inventories.index')
            ->with('success', "Inventory item #{$inventoryId} deleted successfully!");
    }

    /**
     * Display inventory items by film.
     */
    public function byFilm(Film $film): View
    {
        $inventories = Inventory::with(['film.language', 'film.category', 'store'])
            ->byFilm($film->film_id)
            ->orderBy('store_id')
            ->paginate(20);

        return view('inventories.by-film', compact('inventories', 'film'));
    }

    /**
     * Display inventory items by store.
     */
    public function byStore(Store $store): View
    {
        $inventories = Inventory::with(['film.language', 'film.category', 'store'])
            ->byStore($store->store_id)
            ->alphabetical()
            ->paginate(20);

        return view('inventories.by-store', compact('inventories', 'store'));
    }

    /**
     * Display recent inventory additions.
     */
    public function recent(Request $request): View
    {
        $days = $request->get('days', 30);
        
        $inventories = Inventory::with(['film.language', 'film.category', 'store'])
            ->recent($days)
            ->newest()
            ->paginate(20);

        return view('inventories.recent', compact('inventories', 'days'));
    }

    /**
     * Display high-value inventory items.
     */
    public function highValue(): View
    {
        $inventories = Inventory::with(['film.language', 'film.category', 'store'])
            ->highValue()
            ->alphabetical()
            ->paginate(20);

        return view('inventories.high-value', compact('inventories'));
    }

    /**
     * Display inventory statistics.
     */
    public function statistics(): View
    {
        $stats = Inventory::getStatistics();
        $storeInventory = Inventory::getStoreInventorySummary();

        return view('inventories.statistics', compact('stats', 'storeInventory'));
    }

    /**
     * Bulk add inventory items.
     */
    public function bulkCreate(): View
    {
        // Cargar tiendas con direcciones y manager para mostrar en checkboxes
        $stores = Store::with(['address', 'manager'])->orderBy('store_id')->get();

        return view('inventories.bulk-create', compact('stores'));
    }

    /**
     * Store bulk inventory items.
     */
    public function bulkStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'film_id' => 'required|exists:film,film_id',
            'store_ids' => 'required|array|min:1',
            'store_ids.*' => 'exists:stores,store_id',
            'quantity' => 'required|integer|min:1|max:50',
        ]);

        $totalAdded = 0;
        
        foreach ($validated['store_ids'] as $storeId) {
            for ($i = 0; $i < $validated['quantity']; $i++) {
                Inventory::create([
                    'film_id' => $validated['film_id'],
                    'store_id' => $storeId,
                ]);
                $totalAdded++;
            }
        }

        return redirect()->route('inventories.index')
            ->with('success', "Successfully added {$totalAdded} inventory items!");
    }

    /**
     * Search films for inventory form autocomplete
     */
    public function searchFilms(Request $request)
    {
        $query = $request->get('q');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $films = Film::with(['category', 'language'])
            ->where(function ($q) use ($query) {
                $q->where('title', 'LIKE', '%' . $query . '%')
                  ->orWhere('description', 'LIKE', '%' . $query . '%');
            })
            ->orderBy('title')
            ->limit(10)
            ->get();

        return response()->json($films->map(function ($film) {
            return [
                'film_id' => $film->film_id,
                'title' => $film->title,
                'release_year' => $film->release_year,
                'category' => $film->category->name ?? 'Sin categoría',
                'language' => $film->language->name ?? 'N/A',
                'rating' => $film->rating,
                'rental_rate' => $film->rental_rate,
                'full_text' => $film->title . ($film->release_year ? ' (' . $film->release_year . ')' : '') . ' - ' . ($film->category->name ?? 'Sin categoría')
            ];
        }));
    }

    /**
     * Search stores for inventory form autocomplete
     */
    public function searchStores(Request $request)
    {
        $query = $request->get('q', '');
        
        $stores = Store::with('address')
            ->where('store_id', 'like', "%{$query}%")
            ->orWhereHas('address', function($q) use ($query) {
                $q->where('address', 'like', "%{$query}%")
                  ->orWhere('district', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get()
            ->map(function($store) {
                return [
                    'store_id' => $store->store_id,
                    'address' => $store->address ? $store->address->address : 'Dirección no disponible',
                    'district' => $store->address ? $store->address->district : '',
                ];
            });
        
        return response()->json($stores);
    }
}
