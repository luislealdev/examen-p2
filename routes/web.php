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
use App\Http\Controllers\PasswordResetController;
use Illuminate\Support\Facades\Route;

// Redirección principal
Route::get('/', function () {
    return redirect()->route('films.index');
});

// === RUTAS DE AUTENTICACIÓN ===
Route::middleware('guest')->group(function () {
    Route::get('/login', [WebAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [WebAuthController::class, 'login'])->name('auth.login');
    Route::get('/register', [WebAuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [WebAuthController::class, 'register'])->name('auth.register');
    
    // Rutas de recuperación de contraseña
    Route::get('/forgot-password', [PasswordResetController::class, 'showRequestForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.update');
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

// Rutas de búsqueda para autocompletado (disponibles para todos)
Route::get('search/films', [InventoryController::class, 'searchFilms'])->name('films.search');
Route::get('search/stores', [InventoryController::class, 'searchStores'])->name('stores.search');

// Ruta para cargar ciudades por país (necesaria para registro público)
Route::get('cities/by-country', [CustomerController::class, 'getCitiesByCountry'])->name('cities.by-country');

// // Ruta temporal de test para debugging
// Route::get('test-country-city', function() {
//     return view('test-country-city');
// })->name('test.country-city');

// === RUTAS PARA EMPLEADOS Y ADMINISTRADORES ===
Route::middleware(['auth', 'role:employee,admin'])->group(function () {
    
    // Gestión de películas (crear, editar, eliminar)
    Route::post('films', [FilmController::class, 'store'])->name('films.store');
    Route::get('films/create', [FilmController::class, 'create'])->name('films.create');
    Route::get('films/{film}/edit', [FilmController::class, 'edit'])->name('films.edit');
    Route::put('films/{film}', [FilmController::class, 'update'])->name('films.update');
    Route::delete('films/{film}', [FilmController::class, 'destroy'])->name('films.destroy');

    // Gestión de rentas (empleados y administradores)
    Route::post('films/{film}/rent', [RentalController::class, 'rentFilm'])->name('rental.rent');
    Route::put('rentals/{rental}/return', [RentalController::class, 'returnFilm'])->name('rental.return');
    Route::get('films/{film}/availability', [RentalController::class, 'checkAvailability'])->name('rental.availability');
    Route::get('customers/search', [RentalController::class, 'searchCustomers'])->name('customers.search');
    
    // Gestión de clientes (empleados y administradores)
    Route::resource('customers', CustomerController::class);
    
    // Búsqueda de tiendas (solo para administradores que pueden seleccionar tienda)
    Route::middleware('role:admin')->group(function () {
        Route::get('stores/search', [CustomerController::class, 'searchStores'])->name('stores.search-for-customers');
    });
    
    // Gestión de bloqueos de clientes (solo administradores)
    Route::middleware('role:admin')->group(function () {
        Route::get('customers-blocked', [CustomerController::class, 'blocked'])->name('customers.blocked');
        Route::post('customers/{customer}/force-unblock', [CustomerController::class, 'forceUnblock'])->name('customers.force-unblock');
        Route::post('customers/{customer}/extend-rental', [CustomerController::class, 'extendRental'])->name('customers.extend-rental');
    });

    // Gestión de inventarios (empleados y administradores)
    Route::resource('inventories', InventoryController::class);
    Route::get('inventories-film/{film}', [InventoryController::class, 'byFilm'])->name('inventories.by-film');
    Route::get('inventories-store/{store}', [InventoryController::class, 'byStore'])->name('inventories.by-store');
    Route::get('inventories-recent', [InventoryController::class, 'recent'])->name('inventories.recent');
    Route::get('inventories-high-value', [InventoryController::class, 'highValue'])->name('inventories.high-value');
    Route::get('inventories-statistics', [InventoryController::class, 'statistics'])->name('inventories.statistics');
    Route::get('inventories-bulk-create', [InventoryController::class, 'bulkCreate'])->name('inventories.bulk-create');
    Route::post('inventories-bulk-store', [InventoryController::class, 'bulkStore'])->name('inventories.bulk-store');

    // OMDB API routes (empleados y administradores)
    Route::prefix('omdb')->name('omdb.')->group(function () {
        Route::get('search', [OmdbController::class, 'search'])->name('search');
        Route::post('search-movies', [OmdbController::class, 'searchMovies'])->name('search-movies');
        Route::post('movie-details', [OmdbController::class, 'getMovieDetails'])->name('movie-details');
        Route::post('preview-import', [OmdbController::class, 'previewImport'])->name('preview-import');
        Route::post('import-movie', [OmdbController::class, 'importMovie'])->name('import-movie');
        Route::get('check-config', [OmdbController::class, 'checkConfiguration'])->name('check-config');
    });
});

// === RUTAS SOLO PARA ADMINISTRADORES ===
Route::middleware(['auth', 'role:admin'])->group(function () {
    
    // Panel de administración
    Route::get('admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // Estadísticas de películas (solo admin)
    Route::get('films-statistics', [FilmController::class, 'statistics'])->name('films.statistics');
    
    // Gestión de categorías (solo admin)
    Route::resource('categories', CategoryController::class);
    Route::get('categories-alphabetical', [CategoryController::class, 'alphabetical'])->name('categories.alphabetical');
    Route::get('categories-popular', [CategoryController::class, 'popular'])->name('categories.popular');

    // Gestión de idiomas (solo admin)
    Route::resource('languages', LanguageController::class);
    Route::get('languages-alphabetical', [LanguageController::class, 'alphabetical'])->name('languages.alphabetical');

    // Gestión de tiendas (solo admin)
    Route::resource('stores', StoreController::class);
    
    // Gestión de usuarios (solo admin)
    Route::get('admin/users', [AdminController::class, 'users'])->name('admin.users');
    Route::get('admin/users/create', [AdminController::class, 'createUser'])->name('admin.users.create');
    Route::post('admin/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
    Route::get('admin/users/{user}/edit', [AdminController::class, 'editUser'])->name('admin.users.edit');
    Route::put('admin/users/{user}', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::delete('admin/users/{user}', [AdminController::class, 'deleteUser'])->name('admin.users.destroy');
    
    // Gestión de personal (solo administradores)
    Route::resource('staff', StaffController::class);
    Route::get('staff/{staff}/picture', [StaffController::class, 'picture'])->name('staff.picture');
    Route::post('staff/sync-from-users', [StaffController::class, 'syncFromUsers'])->name('staff.sync');
    
    // Auditoría (solo administradores)
    Route::prefix('audit')->name('audit.')->group(function () {
        Route::get('/', [App\Http\Controllers\AuditController::class, 'index'])->name('index');
        Route::get('/statistics', [App\Http\Controllers\AuditController::class, 'statistics'])->name('statistics');
        Route::get('/{id}', [App\Http\Controllers\AuditController::class, 'show'])->name('show');
        Route::post('/cleanup', [App\Http\Controllers\AuditController::class, 'cleanup'])->name('cleanup');
    });
    
    // Logs de Actividad de Negocio (solo administradores)
    Route::prefix('business-activity')->name('business-activity.')->group(function () {
        Route::get('/', [App\Http\Controllers\BusinessActivityController::class, 'index'])->name('index');
        Route::get('/dashboard', [App\Http\Controllers\BusinessActivityController::class, 'dashboard'])->name('dashboard');
        Route::get('/{id}', [App\Http\Controllers\BusinessActivityController::class, 'show'])->name('show');
    });
});

// === RUTAS DE DEBUG TEMPORAL ===
Route::get('/debug-auth', function () {
    $user = auth()->user();
    if (!$user) {
        return response()->json([
            'authenticated' => false,
            'message' => 'No user authenticated'
        ]);
    }
    
    return response()->json([
        'authenticated' => true,
        'user_id' => $user->id,
        'email' => $user->email,
        'role' => $user->role,
        'role_type' => gettype($user->role),
        'role_length' => strlen($user->role),
        'role_hex' => bin2hex($user->role),
        'is_admin' => $user->role === 'admin',
        'middleware_test' => [
            'employee_admin' => in_array($user->role, ['employee', 'admin']),
            'admin_only' => in_array($user->role, ['admin'])
        ]
    ]);
});

// Debug específico para middleware employee,admin
Route::get('/debug-middleware-employee-admin', function () {
    $user = auth()->user();
    if (!$user) {
        return response()->json(['error' => 'Not authenticated']);
    }
    
    return response()->json([
        'success' => 'Middleware employee,admin passed successfully!',
        'user' => $user->email,
        'role' => $user->role
    ]);
})->middleware(['auth', 'role:employee,admin']);

// Debug específico para middleware admin-only
Route::get('/debug-middleware-admin-only', function () {
    $user = auth()->user();
    if (!$user) {
        return response()->json(['error' => 'Not authenticated']);
    }
    
    return response()->json([
        'success' => 'Middleware admin-only passed successfully!',
        'user' => $user->email,
        'role' => $user->role
    ]);
})->middleware(['auth', 'role:admin']);
