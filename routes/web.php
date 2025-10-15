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
use App\Http\Controllers\OmdbController;
use App\Http\Controllers\WebAuthController;
use Illuminate\Support\Facades\Route;

// Redirección principal
Route::get('/', function () {
    return redirect()->route('films.index');
});

// === RUTAS DE AUTENTICACIÓN ===
Route::middleware('guest')->group(function () {
    Route::get('/login', [WebAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [WebAuthController::class, 'login'])->name('auth.login');
    Route::get('/register', [WebAuthController::class, 'showRegister'])->name('auth.register');
    Route::post('/register', [WebAuthController::class, 'register']);
});

Route::post('/logout', [WebAuthController::class, 'logout'])
    ->middleware('auth')
    ->name('auth.logout');

// === RUTAS PÚBLICAS (TODOS LOS USUARIOS) ===
// Películas - solo visualización pública
Route::get('films', [FilmController::class, 'index'])->name('films.index');
Route::get('films/{film}', [FilmController::class, 'show'])->name('films.show');
Route::get('films-category/{category}', [FilmController::class, 'byCategory'])->name('films.by-category');
Route::get('films-language/{language}', [FilmController::class, 'byLanguage'])->name('films.by-language');
Route::get('films-rating/{rating}', [FilmController::class, 'byRating'])->name('films.by-rating');
Route::get('films-decade/{decade}', [FilmController::class, 'byDecade'])->name('films.by-decade');
Route::get('films-recent', [FilmController::class, 'recent'])->name('films.recent');

// === RUTAS PARA EMPLEADOS Y ADMINISTRADORES ===
Route::middleware(['auth', 'role:employee,admin'])->group(function () {
    
    // Gestión de películas (crear, editar, eliminar)
    Route::post('films', [FilmController::class, 'store'])->name('films.store');
    Route::get('films/create', [FilmController::class, 'create'])->name('films.create');
    Route::get('films/{film}/edit', [FilmController::class, 'edit'])->name('films.edit');
    Route::put('films/{film}', [FilmController::class, 'update'])->name('films.update');
    Route::delete('films/{film}', [FilmController::class, 'destroy'])->name('films.destroy');
    Route::get('films-statistics', [FilmController::class, 'statistics'])->name('films.statistics');

    // OMDB API routes
    Route::prefix('omdb')->name('omdb.')->group(function () {
        Route::get('search', [OmdbController::class, 'search'])->name('search');
        Route::post('search-movies', [OmdbController::class, 'searchMovies'])->name('search-movies');
        Route::post('movie-details', [OmdbController::class, 'getMovieDetails'])->name('movie-details');
        Route::post('preview-import', [OmdbController::class, 'previewImport'])->name('preview-import');
        Route::post('import-movie', [OmdbController::class, 'importMovie'])->name('import-movie');
        Route::get('check-config', [OmdbController::class, 'checkConfiguration'])->name('check-config');
    });

    // Gestión de inventarios
    Route::resource('inventories', InventoryController::class);
    Route::get('inventories-film/{film}', [InventoryController::class, 'byFilm'])->name('inventories.by-film');
    Route::get('inventories-store/{store}', [InventoryController::class, 'byStore'])->name('inventories.by-store');
    Route::get('inventories-recent', [InventoryController::class, 'recent'])->name('inventories.recent');
    Route::get('inventories-high-value', [InventoryController::class, 'highValue'])->name('inventories.high-value');
    Route::get('inventories-statistics', [InventoryController::class, 'statistics'])->name('inventories.statistics');
    Route::get('inventories-bulk-create', [InventoryController::class, 'bulkCreate'])->name('inventories.bulk-create');
    Route::post('inventories-bulk-store', [InventoryController::class, 'bulkStore'])->name('inventories.bulk-store');

    // Gestión de categorías
    Route::resource('categories', CategoryController::class);
    Route::get('categories-alphabetical', [CategoryController::class, 'alphabetical'])->name('categories.alphabetical');
    Route::get('categories-popular', [CategoryController::class, 'popular'])->name('categories.popular');

    // Gestión de idiomas
    Route::resource('languages', LanguageController::class);
    Route::get('languages-alphabetical', [LanguageController::class, 'alphabetical'])->name('languages.alphabetical');

    // Gestión de tiendas
    Route::resource('stores', StoreController::class);

    // Gestión de clientes
    Route::resource('customers', CustomerController::class);
});

// === RUTAS SOLO PARA ADMINISTRADORES ===
Route::middleware(['auth', 'role:admin'])->group(function () {
    
    // Panel de administración
    Route::get('admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // Gestión de usuarios
    Route::get('admin/users', [AdminController::class, 'users'])->name('admin.users');
    Route::get('admin/users/create', [AdminController::class, 'createUser'])->name('admin.users.create');
    Route::post('admin/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
    Route::get('admin/users/{user}/edit', [AdminController::class, 'editUser'])->name('admin.users.edit');
    Route::put('admin/users/{user}', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::delete('admin/users/{user}', [AdminController::class, 'deleteUser'])->name('admin.users.destroy');
    
    // Gestión de personal (solo administradores)
    Route::resource('staff', StaffController::class);
    Route::get('staff/{staff}/picture', [StaffController::class, 'picture'])->name('staff.picture');
});
