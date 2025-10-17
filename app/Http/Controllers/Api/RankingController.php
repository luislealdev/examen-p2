<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FilmResource;
use App\Models\Film;
use App\Models\Rental;
use App\Models\Customer;
use App\Models\Store;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class RankingController extends Controller
{
    /**
     * Get most popular films by rental count
     */
    public function popularFilms(Request $request): JsonResponse
    {
        $days = $request->get('days', 30);
        $limit = min($request->get('limit', 20), 50);

        $popularFilms = Film::with(['language', 'category'])
                           ->select('film.*')
                           ->join('inventory', 'film.film_id', '=', 'inventory.film_id')
                           ->join('rental', 'inventory.inventory_id', '=', 'rental.inventory_id')
                           ->where('rental.rental_date', '>=', now()->subDays($days))
                           ->groupBy('film.film_id')
                           ->orderBy(DB::raw('COUNT(rental.rental_id)'), 'desc')
                           ->limit($limit)
                           ->get()
                           ->map(function($film, $index) use ($days) {
                               $rentalCount = Rental::join('inventory', 'rental.inventory_id', '=', 'inventory.inventory_id')
                                                   ->where('inventory.film_id', $film->film_id)
                                                   ->where('rental.rental_date', '>=', now()->subDays($days))
                                                   ->count();
                               
                               return [
                                   'rank' => $index + 1,
                                   'film' => new FilmResource($film),
                                   'rental_count' => $rentalCount,
                                   'rental_rate_per_day' => round($rentalCount / $days, 2),
                               ];
                           });

        return response()->json([
            'data' => $popularFilms,
            'meta' => [
                'period_days' => $days,
                'total_films' => $popularFilms->count(),
                'generated_at' => now()->toISOString(),
            ],
        ]);
    }

    /**
     * Get highest rated films by IMDB rating
     */
    public function topRatedFilms(Request $request): JsonResponse
    {
        $limit = min($request->get('limit', 20), 50);
        $minRating = $request->get('min_rating', 7.0);

        $topRatedFilms = Film::with(['language', 'category'])
                            ->whereNotNull('imdb_rating')
                            ->where('imdb_rating', '>=', $minRating)
                            ->orderBy('imdb_rating', 'desc')
                            ->orderBy('title', 'asc')
                            ->limit($limit)
                            ->get()
                            ->map(function($film, $index) {
                                return [
                                    'rank' => $index + 1,
                                    'film' => new FilmResource($film),
                                    'imdb_rating' => $film->imdb_rating,
                                ];
                            });

        return response()->json([
            'data' => $topRatedFilms,
            'meta' => [
                'minimum_rating' => $minRating,
                'total_films' => $topRatedFilms->count(),
                'generated_at' => now()->toISOString(),
            ],
        ]);
    }

    /**
     * Get films by revenue (rental rate × rental count)
     */
    public function topRevenueFilms(Request $request): JsonResponse
    {
        $days = $request->get('days', 30);
        $limit = min($request->get('limit', 20), 50);

        $revenueFilms = Film::with(['language', 'category'])
                           ->select('film.*')
                           ->join('inventory', 'film.film_id', '=', 'inventory.film_id')
                           ->join('rental', 'inventory.inventory_id', '=', 'rental.inventory_id')
                           ->where('rental.rental_date', '>=', now()->subDays($days))
                           ->groupBy('film.film_id')
                           ->orderBy(DB::raw('COUNT(rental.rental_id) * film.rental_rate'), 'desc')
                           ->limit($limit)
                           ->get()
                           ->map(function($film, $index) use ($days) {
                               $rentalCount = Rental::join('inventory', 'rental.inventory_id', '=', 'inventory.inventory_id')
                                                   ->where('inventory.film_id', $film->film_id)
                                                   ->where('rental.rental_date', '>=', now()->subDays($days))
                                                   ->count();
                               
                               $revenue = $rentalCount * $film->rental_rate;
                               
                               return [
                                   'rank' => $index + 1,
                                   'film' => new FilmResource($film),
                                   'rental_count' => $rentalCount,
                                   'rental_rate' => $film->rental_rate,
                                   'total_revenue' => $revenue,
                               ];
                           });

        return response()->json([
            'data' => $revenueFilms,
            'meta' => [
                'period_days' => $days,
                'total_films' => $revenueFilms->count(),
                'total_revenue' => $revenueFilms->sum('total_revenue'),
                'generated_at' => now()->toISOString(),
            ],
        ]);
    }

    /**
     * Get category rankings by popularity
     */
    public function popularCategories(Request $request): JsonResponse
    {
        $days = $request->get('days', 30);

        $categoryStats = Category::select('category.*')
                                ->join('film', 'category.category_id', '=', 'film.category_id')
                                ->join('inventory', 'film.film_id', '=', 'inventory.film_id')
                                ->join('rental', 'inventory.inventory_id', '=', 'rental.inventory_id')
                                ->where('rental.rental_date', '>=', now()->subDays($days))
                                ->groupBy('category.category_id')
                                ->orderBy(DB::raw('COUNT(rental.rental_id)'), 'desc')
                                ->get()
                                ->map(function($category, $index) use ($days) {
                                    $rentalCount = Rental::join('inventory', 'rental.inventory_id', '=', 'inventory.inventory_id')
                                                        ->join('film', 'inventory.film_id', '=', 'film.film_id')
                                                        ->where('film.category_id', $category->category_id)
                                                        ->where('rental.rental_date', '>=', now()->subDays($days))
                                                        ->count();
                                    
                                    $filmCount = Film::where('category_id', $category->category_id)->count();
                                    
                                    return [
                                        'rank' => $index + 1,
                                        'category_id' => $category->category_id,
                                        'name' => $category->name,
                                        'rental_count' => $rentalCount,
                                        'film_count' => $filmCount,
                                        'avg_rentals_per_film' => $filmCount > 0 ? round($rentalCount / $filmCount, 2) : 0,
                                    ];
                                });

        return response()->json([
            'data' => $categoryStats,
            'meta' => [
                'period_days' => $days,
                'total_categories' => $categoryStats->count(),
                'generated_at' => now()->toISOString(),
            ],
        ]);
    }

    /**
     * Get customer rankings by rental activity
     */
    public function topCustomers(Request $request): JsonResponse
    {
        $days = $request->get('days', 30);
        $limit = min($request->get('limit', 20), 50);

        $topCustomers = Customer::select('customer.*')
                               ->join('rental', 'customer.customer_id', '=', 'rental.customer_id')
                               ->where('rental.rental_date', '>=', now()->subDays($days))
                               ->groupBy('customer.customer_id')
                               ->orderBy(DB::raw('COUNT(rental.rental_id)'), 'desc')
                               ->limit($limit)
                               ->get()
                               ->map(function($customer, $index) use ($days) {
                                   $rentalCount = Rental::where('customer_id', $customer->customer_id)
                                                       ->where('rental_date', '>=', now()->subDays($days))
                                                       ->count();
                                   
                                   $totalSpent = Rental::join('inventory', 'rental.inventory_id', '=', 'inventory.inventory_id')
                                                      ->join('film', 'inventory.film_id', '=', 'film.film_id')
                                                      ->where('rental.customer_id', $customer->customer_id)
                                                      ->where('rental.rental_date', '>=', now()->subDays($days))
                                                      ->sum('film.rental_rate');
                                   
                                   return [
                                       'rank' => $index + 1,
                                       'customer_id' => $customer->customer_id,
                                       'name' => $customer->first_name . ' ' . $customer->last_name,
                                       'email' => $customer->email,
                                       'rental_count' => $rentalCount,
                                       'total_spent' => $totalSpent,
                                       'avg_spent_per_rental' => $rentalCount > 0 ? round($totalSpent / $rentalCount, 2) : 0,
                                   ];
                               });

        return response()->json([
            'data' => $topCustomers,
            'meta' => [
                'period_days' => $days,
                'total_customers' => $topCustomers->count(),
                'generated_at' => now()->toISOString(),
            ],
        ]);
    }

    /**
     * Get store performance rankings
     */
    public function storePerformance(Request $request): JsonResponse
    {
        $days = $request->get('days', 30);

        $storeStats = Store::with(['address.city.country'])
                          ->select('store.*')
                          ->join('inventory', 'store.store_id', '=', 'inventory.store_id')
                          ->join('rental', 'inventory.inventory_id', '=', 'rental.inventory_id')
                          ->where('rental.rental_date', '>=', now()->subDays($days))
                          ->groupBy('store.store_id')
                          ->orderBy(DB::raw('COUNT(rental.rental_id)'), 'desc')
                          ->get()
                          ->map(function($store, $index) use ($days) {
                              $rentalCount = Rental::join('inventory', 'rental.inventory_id', '=', 'inventory.inventory_id')
                                                  ->where('inventory.store_id', $store->store_id)
                                                  ->where('rental.rental_date', '>=', now()->subDays($days))
                                                  ->count();
                              
                              $revenue = Rental::join('inventory', 'rental.inventory_id', '=', 'inventory.inventory_id')
                                              ->join('film', 'inventory.film_id', '=', 'film.film_id')
                                              ->where('inventory.store_id', $store->store_id)
                                              ->where('rental.rental_date', '>=', now()->subDays($days))
                                              ->sum('film.rental_rate');
                              
                              $totalInventory = $store->inventory()->count();
                              
                              return [
                                  'rank' => $index + 1,
                                  'store_id' => $store->store_id,
                                  'address' => $store->address ? [
                                      'address' => $store->address->address,
                                      'city' => $store->address->city->city ?? null,
                                      'country' => $store->address->city->country->country ?? null,
                                  ] : null,
                                  'rental_count' => $rentalCount,
                                  'total_revenue' => $revenue,
                                  'total_inventory' => $totalInventory,
                                  'utilization_rate' => $totalInventory > 0 ? round(($rentalCount / $totalInventory) * 100, 2) : 0,
                              ];
                          });

        return response()->json([
            'data' => $storeStats,
            'meta' => [
                'period_days' => $days,
                'total_stores' => $storeStats->count(),
                'total_rentals' => $storeStats->sum('rental_count'),
                'total_revenue' => $storeStats->sum('total_revenue'),
                'generated_at' => now()->toISOString(),
            ],
        ]);
    }

    /**
     * Get comprehensive rental statistics
     */
    public function overallStats(Request $request): JsonResponse
    {
        $days = $request->get('days', 30);

        $stats = [
            'period' => [
                'days' => $days,
                'start_date' => now()->subDays($days)->toDateString(),
                'end_date' => now()->toDateString(),
            ],
            'rentals' => [
                'total_rentals' => Rental::where('rental_date', '>=', now()->subDays($days))->count(),
                'total_returns' => Rental::where('rental_date', '>=', now()->subDays($days))
                                        ->whereNotNull('return_date')->count(),
                'pending_returns' => Rental::where('rental_date', '>=', now()->subDays($days))
                                          ->whereNull('return_date')->count(),
                'overdue_rentals' => Rental::where('rental_date', '<', now()->subDays(7))
                                          ->whereNull('return_date')->count(),
            ],
            'revenue' => [
                'total_revenue' => Rental::join('inventory', 'rental.inventory_id', '=', 'inventory.inventory_id')
                                        ->join('film', 'inventory.film_id', '=', 'film.film_id')
                                        ->where('rental.rental_date', '>=', now()->subDays($days))
                                        ->sum('film.rental_rate'),
                'avg_revenue_per_day' => 0, // Se calculará después
            ],
            'inventory' => [
                'total_films' => Film::count(),
                'total_copies' => \App\Models\Inventory::count(),
                'available_copies' => \App\Models\Inventory::where('condition', 'available')
                                                          ->whereDoesntHave('rentals', function($q) {
                                                              $q->whereNull('return_date');
                                                          })->count(),
                'damaged_copies' => \App\Models\Inventory::where('condition', 'damaged')->count(),
                'lost_copies' => \App\Models\Inventory::where('condition', 'lost')->count(),
            ],
            'customers' => [
                'total_customers' => Customer::count(),
                'active_customers' => Customer::whereHas('rentals', function($q) use ($days) {
                                               $q->where('rental_date', '>=', now()->subDays($days));
                                           })->count(),
            ],
        ];

        // Calculate average revenue per day
        $stats['revenue']['avg_revenue_per_day'] = $days > 0 
            ? round($stats['revenue']['total_revenue'] / $days, 2) 
            : 0;

        return response()->json([
            'data' => $stats,
            'generated_at' => now()->toISOString(),
        ]);
    }
}
