<?php

use App\Http\Controllers\TaskController;
use App\Http\Controllers\ActorController;
use App\Http\Controllers\FilmController;
use App\Http\Controllers\RentalController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('films.index');
});

// Ruta de login solo para diseño (sin funcionalidad)
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// TODO: Create Controllers and add them here
// Rutas públicas de CRUD (sin middleware de autenticación)
// Route::resource('actors', ActorController::class);
// Route::resource('films', FilmController::class);
// Route::resource('rentals', RentalController::class);

// Nuevas rutas CRUD para Sakila
Route::resource('stores', StoreController::class);
Route::resource('customers', CustomerController::class);
Route::resource('staff', StaffController::class);
// Special route for staff pictures
Route::get('staff/{staff}/picture', [StaffController::class, 'picture'])->name('staff.picture');
Route::resource('languages', LanguageController::class);
// Special route for languages alphabetical view
Route::get('languages-alphabetical', [LanguageController::class, 'alphabetical'])->name('languages.alphabetical');
Route::resource('categories', CategoryController::class);
// Special routes for categories
Route::get('categories-alphabetical', [CategoryController::class, 'alphabetical'])->name('categories.alphabetical');
Route::get('categories-popular', [CategoryController::class, 'popular'])->name('categories.popular');

// Films routes
Route::resource('films', FilmController::class);
// Special routes for films
Route::get('films-category/{category}', [FilmController::class, 'byCategory'])->name('films.by-category');
Route::get('films-language/{language}', [FilmController::class, 'byLanguage'])->name('films.by-language');
Route::get('films-rating/{rating}', [FilmController::class, 'byRating'])->name('films.by-rating');
Route::get('films-decade/{decade}', [FilmController::class, 'byDecade'])->name('films.by-decade');
Route::get('films-recent', [FilmController::class, 'recent'])->name('films.recent');
Route::get('films-statistics', [FilmController::class, 'statistics'])->name('films.statistics');

// Inventory routes
Route::resource('inventories', InventoryController::class);
// Special routes for inventories
Route::get('inventories-film/{film}', [InventoryController::class, 'byFilm'])->name('inventories.by-film');
Route::get('inventories-store/{store}', [InventoryController::class, 'byStore'])->name('inventories.by-store');
Route::get('inventories-recent', [InventoryController::class, 'recent'])->name('inventories.recent');
Route::get('inventories-high-value', [InventoryController::class, 'highValue'])->name('inventories.high-value');
Route::get('inventories-statistics', [InventoryController::class, 'statistics'])->name('inventories.statistics');
Route::get('inventories-bulk-create', [InventoryController::class, 'bulkCreate'])->name('inventories.bulk-create');
Route::post('inventories-bulk-store', [InventoryController::class, 'bulkStore'])->name('inventories.bulk-store');

// Route::resource('inventories', InventoryController::class);
// Route::resource('actors', ActorController::class);
// Route::resource('films', FilmController::class);
// Route::resource('rentals', RentalController::class);

// Rutas protegidas con middleware de roles (para demostración)
// Nota: Estas rutas requerirán autenticación cuando implementes un sistema de login
Route::middleware(['admin'])->group(function () {
    // Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    // Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
    // Route::put('/admin/users/{user}/role', [AdminController::class, 'updateUserRole'])->name('admin.users.updateRole');
});

Route::middleware(['role:admin,moderator'])->group(function () {
    // Route::get('/admin/moderator', [AdminController::class, 'moderatorPanel'])->name('admin.moderator');
});
