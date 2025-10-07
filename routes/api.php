<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MovieController;
use Illuminate\Http\Request;
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

// Test route
Route::get('test', function () {
    return response()->json([
        'success' => true,
        'message' => 'API is working!',
        'timestamp' => now()
    ]);
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