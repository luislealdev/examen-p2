<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Film;
use App\Models\Store;
use App\Models\Category;
use App\Models\Language;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    /**
     * Get the store ID for the current user based on their role.
     */
    private function getUserStoreId(): ?int
    {
        $user = Auth::user();
        
        // Si es admin, puede ver todo
        if ($user->role === 'admin') {
            return null;
        }
        
        // Si es empleado, obtener su tienda del registro de staff
        if ($user->role === 'employee') {
            $staff = Staff::where('email', $user->email)->first();
            return $staff ? $staff->store_id : null;
        }
        
        return null;
    }

    /**
     * Apply store filter based on user role.
     */
    private function applyStoreFilter($query)
    {
        $userStoreId = $this->getUserStoreId();
        
        if ($userStoreId !== null) {
            $query->byStore($userStoreId);
        }
        
        return $query;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Inventory::with(['film.language', 'film.category', 'store']);

        // Aplicar filtro por tienda del empleado
        $query = $this->applyStoreFilter($query);

        // Search functionality
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by film
        if ($request->filled('film_id')) {
            $query->byFilm($request->film_id);
        }

        // Filter by store (solo si es admin)
        if ($request->filled('store_id') && Auth::user()->role === 'admin') {
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

        // Get filter data (filtrar películas solo de su tienda para empleados)
        $userStoreId = $this->getUserStoreId();
        
        if ($userStoreId !== null) {
            // Empleado: solo películas disponibles en su tienda
            $films = Film::with('language')
                ->whereHas('inventories', function($q) use ($userStoreId) {
                    $q->where('store_id', $userStoreId);
                })
                ->orderBy('title')
                ->get();
        } else {
            // Admin: todas las películas
            $films = Film::with('language')->orderBy('title')->get();
        }
        
        // Stores: solo mostrar selector si es admin
        $stores = Auth::user()->role === 'admin' 
            ? Store::orderBy('store_id')->get() 
            : collect();
            
        $categories = Category::alphabetical()->get();
        $languages = Language::alphabetical()->get();
        $ratings = Film::RATINGS;

        // Get statistics (filtradas por tienda si es empleado)
        $stats = $this->getFilteredStatistics($userStoreId);

        return view('inventories.index', compact(
            'inventories', 'films', 'stores', 'categories', 'languages', 'ratings', 'stats'
        ));
    }

    /**
     * Get statistics filtered by store if needed.
     */
    private function getFilteredStatistics(?int $storeId): array
    {
        $query = Inventory::query();
        
        if ($storeId !== null) {
            $query->where('store_id', $storeId);
        }
        
        return [
            'total_items' => $query->count(),
            'by_store' => $storeId !== null 
                ? [$storeId => $query->count()]
                : Inventory::selectRaw('store_id, COUNT(*) as count')
                    ->groupBy('store_id')
                    ->with('store')
                    ->get()
                    ->mapWithKeys(fn($item) => [
                        $item->store->store_id ?? 'Unknown' => $item->count
                    ]),
            'by_rating' => (clone $query)
                ->join('film', 'inventory.film_id', '=', 'film.film_id')
                ->selectRaw('film.rating, COUNT(*) as count')
                ->groupBy('film.rating')
                ->get()
                ->mapWithKeys(fn($item) => [$item->rating => $item->count]),
            'recent_additions' => (clone $query)->recent(7)->count(),
            'avg_rental_rate' => (clone $query)
                ->join('film', 'inventory.film_id', '=', 'film.film_id')
                ->avg('film.rental_rate'),
            'high_value_items' => (clone $query)->highValue()->count(),
        ];
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $userStoreId = $this->getUserStoreId();
        
        $films = Film::with(['language', 'category'])
                    ->orderBy('title')
                    ->get();
        
        // Si es empleado, solo puede agregar a su tienda
        if ($userStoreId !== null) {
            $stores = Store::with(['address', 'manager'])
                        ->where('store_id', $userStoreId)
                        ->get();
        } else {
            $stores = Store::with(['address', 'manager'])
                        ->orderBy('store_id')
                        ->get();
        }

        return view('inventories.create', compact('films', 'stores'));
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

        // Verificar que el empleado solo puede agregar a su tienda
        $userStoreId = $this->getUserStoreId();
        if ($userStoreId !== null && $validated['store_id'] != $userStoreId) {
            return redirect()->back()
                ->withErrors(['store_id' => 'No puedes agregar inventario a una tienda diferente a la tuya.'])
                ->withInput();
        }

        $inventory = Inventory::create($validated);

        return redirect()->route('inventories.index')
            ->with('success', "Inventory item #{$inventory->inventory_id} created successfully!");
    }

    /**
     * Display the specified resource.
     */
    public function show(Inventory $inventory): View
    {
        // Verificar que el empleado solo pueda ver inventario de su tienda
        $userStoreId = $this->getUserStoreId();
        if ($userStoreId !== null && $inventory->store_id != $userStoreId) {
            abort(403, 'No tienes permiso para ver este inventario.');
        }

        $inventory->load(['film.language', 'film.category', 'store']);
        
        return view('inventories.show', compact('inventory'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Inventory $inventory): View
    {
        // Verificar que el empleado solo pueda editar inventario de su tienda
        $userStoreId = $this->getUserStoreId();
        if ($userStoreId !== null && $inventory->store_id != $userStoreId) {
            abort(403, 'No tienes permiso para editar este inventario.');
        }

        $inventory->load(['film.category', 'film.language', 'store.address', 'store.manager']);
        
        $films = Film::with(['language', 'category'])
                    ->orderBy('title')
                    ->get();
        
        // Si es empleado, solo puede mover a su tienda
        if ($userStoreId !== null) {
            $stores = Store::with(['address', 'manager'])
                        ->where('store_id', $userStoreId)
                        ->get();
        } else {
            $stores = Store::with(['address', 'manager'])
                        ->orderBy('store_id')
                        ->get();
        }

        return view('inventories.edit', compact('inventory', 'films', 'stores'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Inventory $inventory): RedirectResponse
    {
        // Verificar que el empleado solo pueda actualizar inventario de su tienda
        $userStoreId = $this->getUserStoreId();
        if ($userStoreId !== null && $inventory->store_id != $userStoreId) {
            abort(403, 'No tienes permiso para actualizar este inventario.');
        }

        $validated = $request->validate([
            'film_id' => 'required|exists:film,film_id',
            'store_id' => 'required|exists:stores,store_id',
        ]);

        // Verificar que el empleado solo puede mover a su tienda
        if ($userStoreId !== null && $validated['store_id'] != $userStoreId) {
            return redirect()->back()
                ->withErrors(['store_id' => 'No puedes mover inventario a una tienda diferente a la tuya.'])
                ->withInput();
        }

        $inventory->update($validated);

        return redirect()->route('inventories.index')
            ->with('success', "Inventory item #{$inventory->inventory_id} updated successfully!");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Inventory $inventory): RedirectResponse
    {
        // Verificar que el empleado solo pueda eliminar inventario de su tienda
        $userStoreId = $this->getUserStoreId();
        if ($userStoreId !== null && $inventory->store_id != $userStoreId) {
            abort(403, 'No tienes permiso para eliminar este inventario.');
        }

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
        $query = Inventory::with(['film.language', 'film.category', 'store'])
            ->byFilm($film->film_id);
        
        // Aplicar filtro por tienda del empleado
        $query = $this->applyStoreFilter($query);
        
        $inventories = $query->orderBy('store_id')->paginate(20);

        return view('inventories.by-film', compact('inventories', 'film'));
    }

    /**
     * Display inventory items by store.
     */
    public function byStore(Store $store): View
    {
        // Verificar que el empleado solo pueda ver inventario de su tienda
        $userStoreId = $this->getUserStoreId();
        if ($userStoreId !== null && $store->store_id != $userStoreId) {
            abort(403, 'No tienes permiso para ver el inventario de esta tienda.');
        }

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
        
        $query = Inventory::with(['film.language', 'film.category', 'store'])
            ->recent($days);
        
        // Aplicar filtro por tienda del empleado
        $query = $this->applyStoreFilter($query);
        
        $inventories = $query->newest()->paginate(20);

        return view('inventories.recent', compact('inventories', 'days'));
    }

    /**
     * Display high-value inventory items.
     */
    public function highValue(): View
    {
        $query = Inventory::with(['film.language', 'film.category', 'store'])
            ->highValue();
        
        // Aplicar filtro por tienda del empleado
        $query = $this->applyStoreFilter($query);
        
        $inventories = $query->alphabetical()->paginate(20);

        return view('inventories.high-value', compact('inventories'));
    }

    /**
     * Display inventory statistics.
     */
    public function statistics(): View
    {
        $userStoreId = $this->getUserStoreId();
        
        $stats = $this->getFilteredStatistics($userStoreId);
        
        if ($userStoreId !== null) {
            $storeInventory = [
                $userStoreId => [
                    'store' => Store::find($userStoreId),
                    'total_items' => Inventory::where('store_id', $userStoreId)->count(),
                    'unique_films' => Inventory::where('store_id', $userStoreId)
                        ->distinct('film_id')
                        ->count('film_id')
                ]
            ];
        } else {
            $storeInventory = Inventory::getStoreInventorySummary();
        }

        return view('inventories.statistics', compact('stats', 'storeInventory'));
    }

    /**
     * Bulk add inventory items.
     */
    public function bulkCreate(): View
    {
        $userStoreId = $this->getUserStoreId();
        
        $films = Film::with(['language', 'category'])
                    ->orderBy('title')
                    ->get();
        
        // Si es empleado, solo puede agregar a su tienda
        if ($userStoreId !== null) {
            $stores = Store::with(['address', 'manager'])
                        ->where('store_id', $userStoreId)
                        ->get();
        } else {
            $stores = Store::with(['address', 'manager'])
                        ->orderBy('store_id')
                        ->get();
        }

        return view('inventories.bulk-create', compact('films', 'stores'));
    }

    /**
     * Store bulk inventory items.
     */
    public function bulkStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'film_id' => 'required|exists:film,film_id',
            'stores' => 'required|array|min:1',
            'stores.*' => 'exists:stores,store_id',
            'quantity' => 'required|integer|min:1|max:50',
        ]);

        // Verificar que el empleado solo puede agregar a su tienda
        $userStoreId = $this->getUserStoreId();
        if ($userStoreId !== null) {
            foreach ($validated['stores'] as $storeId) {
                if ($storeId != $userStoreId) {
                    return redirect()->back()
                        ->withErrors(['stores' => 'No puedes agregar inventario a tiendas diferentes a la tuya.'])
                        ->withInput();
                }
            }
        }

        $totalAdded = 0;
        
        foreach ($validated['stores'] as $storeId) {
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
}