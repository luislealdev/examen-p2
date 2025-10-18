<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FilmResource;
use App\Http\Resources\InventoryResource;
use App\Models\Film;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FilmController extends Controller
{
    /**
     * Display a listing of films with filtering, search and pagination
     */
    public function index(Request $request): JsonResponse
    {
        $query = Film::with(['language', 'originalLanguage', 'category']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%')
                  ->orWhere('actors', 'like', '%' . $search . '%')
                  ->orWhere('director', 'like', '%' . $search . '%');
            });
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by language
        if ($request->filled('language_id')) {
            $query->where('language_id', $request->language_id);
        }

        // Filter by rating
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        // Filter by release year
        if ($request->filled('year')) {
            $query->where('release_year', $request->year);
        }

        // Filter by decade
        if ($request->filled('decade')) {
            $decade = intval($request->decade);
            $query->whereBetween('release_year', [$decade, $decade + 9]);
        }

        // Filter by availability
        if ($request->filled('availability')) {
            if ($request->availability === 'available') {
                $query->whereHas('inventory', function($q) {
                    $q->available();
                });
            } elseif ($request->availability === 'unavailable') {
                $query->whereDoesntHave('inventory', function($q) {
                    $q->available();
                });
            }
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'title');
        $sortDirection = $request->get('sort_direction', 'asc');
        
        if (in_array($sortBy, ['title', 'release_year', 'rental_rate', 'length', 'imdb_rating'])) {
            $query->orderBy($sortBy, $sortDirection);
        }

        // Pagination
        $perPage = min($request->get('per_page', 20), 100); // Máximo 100 por página
        $films = $query->paginate($perPage);

        return response()->json([
            'data' => FilmResource::collection($films),
            'meta' => [
                'current_page' => $films->currentPage(),
                'last_page' => $films->lastPage(),
                'per_page' => $films->perPage(),
                'total' => $films->total(),
                'from' => $films->firstItem(),
                'to' => $films->lastItem(),
            ],
            'links' => [
                'first' => $films->url(1),
                'last' => $films->url($films->lastPage()),
                'prev' => $films->previousPageUrl(),
                'next' => $films->nextPageUrl(),
            ],
        ]);
    }

    /**
     * Display the specified film
     */
    public function show(Film $film): JsonResponse
    {
        $film->load(['language', 'originalLanguage', 'category']);
        
        return response()->json([
            'data' => new FilmResource($film),
        ]);
    }

    /**
     * Get film inventory across all stores
     */
    public function inventory(Film $film): JsonResponse
    {
        $inventories = Inventory::with(['store.address.city.country'])
                               ->where('film_id', $film->film_id)
                               ->get();

        $stats = [
            'total_copies' => $inventories->count(),
            'available_copies' => $inventories->where('condition', 'available')->filter(fn($inv) => $inv->isAvailable())->count(),
            'rented_copies' => $inventories->filter(fn($inv) => !$inv->isAvailable())->count(),
            'damaged_copies' => $inventories->where('condition', 'damaged')->count(),
            'lost_copies' => $inventories->where('condition', 'lost')->count(),
            'by_store' => $inventories->groupBy('store_id')->map(function($storeInventories) {
                return [
                    'total' => $storeInventories->count(),
                    'available' => $storeInventories->where('condition', 'available')->filter(fn($inv) => $inv->isAvailable())->count(),
                    'rented' => $storeInventories->filter(fn($inv) => !$inv->isAvailable())->count(),
                    'damaged' => $storeInventories->where('condition', 'damaged')->count(),
                    'lost' => $storeInventories->where('condition', 'lost')->count(),
                ];
            }),
        ];

        return response()->json([
            'data' => InventoryResource::collection($inventories),
            'stats' => $stats,
            'film' => new FilmResource($film),
        ]);
    }

    /**
     * Get films by category
     */
    public function byCategory(Request $request, int $categoryId): JsonResponse
    {
        $request->merge(['category_id' => $categoryId]);
        return $this->index($request);
    }

    /**
     * Get films by language
     */
    public function byLanguage(Request $request, int $languageId): JsonResponse
    {
        $request->merge(['language_id' => $languageId]);
        return $this->index($request);
    }

    /**
     * Get films by rating
     */
    public function byRating(Request $request, string $rating): JsonResponse
    {
        $request->merge(['rating' => $rating]);
        return $this->index($request);
    }

    /**
     * Get recently added films
     */
    public function recent(Request $request): JsonResponse
    {
        $days = $request->get('days', 30);
        
        $films = Film::with(['language', 'originalLanguage', 'category'])
                    ->where('last_update', '>=', now()->subDays($days))
                    ->orderBy('last_update', 'desc')
                    ->paginate($request->get('per_page', 20));

        return response()->json([
            'data' => FilmResource::collection($films),
            'meta' => [
                'period_days' => $days,
                'current_page' => $films->currentPage(),
                'last_page' => $films->lastPage(),
                'per_page' => $films->perPage(),
                'total' => $films->total(),
            ],
        ]);
    }
}
