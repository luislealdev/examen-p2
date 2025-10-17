<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\InventoryResource;
use App\Http\Resources\FilmResource;
use App\Models\Inventory;
use App\Models\Store;
use App\Models\Film;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class InventoryController extends Controller
{
    /**
     * Display a listing of inventory with filtering and pagination
     */
    public function index(Request $request): JsonResponse
    {
        $query = Inventory::with(['film.language', 'film.category', 'store.address.city.country']);

        // Filter by store
        if ($request->filled('store_id')) {
            $query->where('store_id', $request->store_id);
        }

        // Filter by film
        if ($request->filled('film_id')) {
            $query->where('film_id', $request->film_id);
        }

        // Filter by condition
        if ($request->filled('condition')) {
            $validConditions = ['available', 'damaged', 'lost'];
            if (in_array($request->condition, $validConditions)) {
                $query->where('condition', $request->condition);
            }
        }

        // Filter by availability
        if ($request->filled('available_only') && $request->available_only) {
            $query->where('condition', 'available')
                  ->whereDoesntHave('rentals', function($q) {
                      $q->whereNull('return_date');
                  });
        }

        // Filter by currently rented
        if ($request->filled('rented_only') && $request->rented_only) {
            $query->whereHas('rentals', function($q) {
                $q->whereNull('return_date');
            });
        }

        // Search by film title
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('film', function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%');
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'inventory_id');
        $sortDirection = $request->get('sort_direction', 'asc');
        
        if (in_array($sortBy, ['inventory_id', 'condition', 'last_update'])) {
            $query->orderBy($sortBy, $sortDirection);
        } elseif ($sortBy === 'film_title') {
            $query->join('film', 'inventory.film_id', '=', 'film.film_id')
                  ->orderBy('film.title', $sortDirection)
                  ->select('inventory.*');
        } elseif ($sortBy === 'store') {
            $query->orderBy('store_id', $sortDirection);
        }

        // Pagination
        $perPage = min($request->get('per_page', 20), 100);
        $inventory = $query->paginate($perPage);

        // Calculate stats
        $stats = [
            'total_items' => $inventory->total(),
            'available_items' => Inventory::where('condition', 'available')
                                        ->whereDoesntHave('rentals', function($q) {
                                            $q->whereNull('return_date');
                                        })->count(),
            'rented_items' => Inventory::whereHas('rentals', function($q) {
                                $q->whereNull('return_date');
                            })->count(),
            'damaged_items' => Inventory::where('condition', 'damaged')->count(),
            'lost_items' => Inventory::where('condition', 'lost')->count(),
        ];

        return response()->json([
            'data' => InventoryResource::collection($inventory),
            'stats' => $stats,
            'meta' => [
                'current_page' => $inventory->currentPage(),
                'last_page' => $inventory->lastPage(),
                'per_page' => $inventory->perPage(),
                'total' => $inventory->total(),
                'from' => $inventory->firstItem(),
                'to' => $inventory->lastItem(),
            ],
            'links' => [
                'first' => $inventory->url(1),
                'last' => $inventory->url($inventory->lastPage()),
                'prev' => $inventory->previousPageUrl(),
                'next' => $inventory->nextPageUrl(),
            ],
        ]);
    }

    /**
     * Display the specified inventory item
     */
    public function show(Inventory $inventory): JsonResponse
    {
        $inventory->load(['film.language', 'film.category', 'store.address.city.country']);
        
        return response()->json([
            'data' => new InventoryResource($inventory),
        ]);
    }

    /**
     * Get inventory for a specific store
     */
    public function byStore(Request $request, Store $store): JsonResponse
    {
        $request->merge(['store_id' => $store->store_id]);
        return $this->index($request);
    }

    /**
     * Get inventory availability stats by store
     */
    public function availabilityByStore(): JsonResponse
    {
        $stores = Store::with(['address.city.country'])
                      ->withCount([
                          'inventory',
                          'inventory as available_count' => function($query) {
                              $query->where('condition', 'available')
                                    ->whereDoesntHave('rentals', function($q) {
                                        $q->whereNull('return_date');
                                    });
                          },
                          'inventory as rented_count' => function($query) {
                              $query->whereHas('rentals', function($q) {
                                  $q->whereNull('return_date');
                              });
                          },
                          'inventory as damaged_count' => function($query) {
                              $query->where('condition', 'damaged');
                          },
                          'inventory as lost_count' => function($query) {
                              $query->where('condition', 'lost');
                          }
                      ])
                      ->get();

        return response()->json([
            'data' => $stores->map(function($store) {
                return [
                    'store_id' => $store->store_id,
                    'address' => $store->address ? [
                        'address' => $store->address->address,
                        'city' => $store->address->city->city ?? null,
                        'country' => $store->address->city->country->country ?? null,
                    ] : null,
                    'inventory_stats' => [
                        'total' => $store->inventory_count,
                        'available' => $store->available_count,
                        'rented' => $store->rented_count,
                        'damaged' => $store->damaged_count,
                        'lost' => $store->lost_count,
                        'availability_rate' => $store->inventory_count > 0 
                            ? round(($store->available_count / $store->inventory_count) * 100, 2) 
                            : 0,
                    ],
                ];
            }),
        ]);
    }

    /**
     * Get most popular films by rental frequency
     */
    public function popularFilms(Request $request): JsonResponse
    {
        $days = $request->get('days', 30);
        $limit = min($request->get('limit', 20), 50);

        $popularFilms = Film::with(['language', 'category'])
                           ->withCount([
                               'inventory as rental_count' => function($query) use ($days) {
                                   $query->whereHas('rentals', function($q) use ($days) {
                                       $q->where('rental_date', '>=', now()->subDays($days));
                                   });
                               }
                           ])
                           ->having('rental_count', '>', 0)
                           ->orderBy('rental_count', 'desc')
                           ->limit($limit)
                           ->get();

        return response()->json([
            'data' => $popularFilms->map(function($film, $index) {
                return [
                    'rank' => $index + 1,
                    'film' => new FilmResource($film),
                    'rental_count' => $film->rental_count,
                ];
            }),
            'meta' => [
                'period_days' => $days,
                'total_films' => $popularFilms->count(),
            ],
        ]);
    }

    /**
     * Get inventory movements/history for tracking
     */
    public function movements(Request $request): JsonResponse
    {
        $query = \App\Models\InventoryMovement::with(['inventory.film', 'inventory.store'])
                                             ->orderBy('created_at', 'desc');

        // Filter by inventory item
        if ($request->filled('inventory_id')) {
            $query->where('inventory_id', $request->inventory_id);
        }

        // Filter by movement type
        if ($request->filled('movement_type')) {
            $validTypes = ['return', 'damage', 'loss', 'repair'];
            if (in_array($request->movement_type, $validTypes)) {
                $query->where('movement_type', $request->movement_type);
            }
        }

        // Filter by date range
        if ($request->filled('from_date')) {
            $query->where('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->where('created_at', '<=', $request->to_date);
        }

        $movements = $query->paginate($request->get('per_page', 20));

        return response()->json([
            'data' => $movements->map(function($movement) {
                return [
                    'id' => $movement->id,
                    'inventory_id' => $movement->inventory_id,
                    'movement_type' => $movement->movement_type,
                    'previous_condition' => $movement->previous_condition,
                    'new_condition' => $movement->new_condition,
                    'notes' => $movement->notes,
                    'created_at' => $movement->created_at,
                    'inventory' => $movement->inventory ? [
                        'inventory_id' => $movement->inventory->inventory_id,
                        'film_title' => $movement->inventory->film->title ?? null,
                        'store_id' => $movement->inventory->store_id,
                    ] : null,
                ];
            }),
            'meta' => [
                'current_page' => $movements->currentPage(),
                'last_page' => $movements->lastPage(),
                'per_page' => $movements->perPage(),
                'total' => $movements->total(),
            ],
        ]);
    }
}
