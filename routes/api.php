<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\FilmController;
use App\Http\Controllers\Api\InventoryController;
use App\Http\Controllers\Api\RankingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route for getting cities by country
Route::get('countries/{country}/cities', function ($countryId) {
    $cities = \App\Models\City::where('country_id', $countryId)->get(['city_id', 'city']);
    return response()->json($cities);
});

// Test route
Route::get('test', function () {
    return response()->json([
        'success' => true,
        'message' => 'API is working!',
        'timestamp' => now()
    ]);
});

/*
|--------------------------------------------------------------------------
| Public API Routes (No Authentication Required)
|--------------------------------------------------------------------------
*/

// API version prefix
Route::prefix('v1')->group(function () {
    
    // Films API
    Route::prefix('films')->group(function () {
        Route::get('/', [FilmController::class, 'index']);
        Route::get('/{film}', [FilmController::class, 'show']);
        Route::get('/{film}/inventory', [FilmController::class, 'inventory']);
        
        // Filter routes
        Route::get('/category/{categoryId}', [FilmController::class, 'byCategory']);
        Route::get('/language/{languageId}', [FilmController::class, 'byLanguage']); 
        Route::get('/rating/{rating}', [FilmController::class, 'byRating']);
        Route::get('/recent', [FilmController::class, 'recent']);
    });

    // Inventory API
    Route::prefix('inventory')->group(function () {
        Route::get('/', [InventoryController::class, 'index']);
        Route::get('/{inventory}', [InventoryController::class, 'show']);
        Route::get('/store/{store}', [InventoryController::class, 'byStore']);
        Route::get('/availability/by-store', [InventoryController::class, 'availabilityByStore']);
        Route::get('/popular-films', [InventoryController::class, 'popularFilms']);
        Route::get('/movements', [InventoryController::class, 'movements']);
    });

    // Rankings & Statistics API
    Route::prefix('rankings')->group(function () {
        Route::get('/films/popular', [RankingController::class, 'popularFilms']);
        Route::get('/films/top-rated', [RankingController::class, 'topRatedFilms']);
        Route::get('/films/revenue', [RankingController::class, 'topRevenueFilms']);
        Route::get('/categories/popular', [RankingController::class, 'popularCategories']);
        Route::get('/customers/top', [RankingController::class, 'topCustomers']);
        Route::get('/stores/performance', [RankingController::class, 'storePerformance']);
        Route::get('/stats/overall', [RankingController::class, 'overallStats']);
    });

    // Helper endpoints for metadata
    Route::prefix('metadata')->group(function () {
        Route::get('/categories', function () {
            return response()->json([
                'data' => \App\Models\Category::orderBy('name')->get(['category_id', 'name']),
            ]);
        });
        
        Route::get('/languages', function () {
            return response()->json([
                'data' => \App\Models\Language::orderBy('name')->get(['language_id', 'name']),
            ]);
        });
        
        Route::get('/ratings', function () {
            return response()->json([
                'data' => \App\Models\Film::select('rating')
                                         ->whereNotNull('rating')
                                         ->distinct()
                                         ->orderBy('rating')
                                         ->pluck('rating'),
            ]);
        });

        Route::get('/stores', function () {
            return response()->json([
                'data' => \App\Models\Store::with(['address.city.country'])
                                          ->get()
                                          ->map(function($store) {
                                              return [
                                                  'store_id' => $store->store_id,
                                                  'address' => $store->address ? [
                                                      'address' => $store->address->address,
                                                      'city' => $store->address->city->city ?? null,
                                                      'country' => $store->address->city->country->country ?? null,
                                                  ] : null,
                                              ];
                                          }),
            ]);
        });
    });
});

// Public routes (no authentication required)
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

// OAuth2 routes (handled by Passport)
Route::group(['prefix' => 'oauth'], function () {
    Route::post('token', '\Laravel\Passport\Http\Controllers\AccessTokenController@issueToken');
    Route::post('refresh', [AuthController::class, 'refreshToken']);
});

// Protected routes (authentication required)
Route::middleware('auth:api')->group(function () {

    // User authentication routes
    Route::prefix('auth')->group(function () {
        Route::get('user', [AuthController::class, 'user']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refreshToken']);
    });

    // Movies routes with scope-based access control
    Route::prefix('movies')->group(function () {

        // Read-only access (requires 'read-movies' scope)
        Route::middleware('scopes:read-movies')->group(function () {
            // Route::get('/', [MovieController::class, 'index']);
            // Route::get('/search', [MovieController::class, 'search']);
            // Route::get('/{movie}', [MovieController::class, 'show']);
        });

        // Write access (requires 'write-movies' scope)
        Route::middleware('scopes:write-movies')->group(function () {
            // Route::post('/', [MovieController::class, 'store']);
            // Route::put('/{movie}', [MovieController::class, 'update']);
        });

        // Delete access (requires 'delete-movies' scope)
        Route::middleware('scopes:delete-movies')->group(function () {
            // Route::delete('/{movie}', [MovieController::class, 'destroy']);
        });
    });

    // Admin routes (requires 'admin' scope)
    Route::middleware('scopes:admin')->prefix('admin')->group(function () {
        Route::get('users', function () {
            return response()->json(\App\Models\User::all());
        });

        // Route::get('movies/stats', [MovieController::class, 'stats']);

        // Route::delete('movies/bulk-delete', [MovieController::class, 'bulkDelete']);
    });
});

// Fallback route for API
Route::fallback(function () {
    return response()->json([
        'message' => 'Endpoint not found.'
    ], 404);
});