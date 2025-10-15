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

// --- RUTAS PÚBLICAS Y DE AUTENTICACIÓN ---
Route::middleware('guest')->group(function () {
    // Login/Registro de Clientes
    Route::get('/login', [WebAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [WebAuthController::class, 'login'])->name('auth.login');
    Route::get('/register', [WebAuthController::class, 'showRegister'])->name('auth.register');
    Route::post('/register', [WebAuthController::class, 'register'])->name('auth.register.post');

    // Login de Empleados
    Route::get('/staff/login', [WebAuthController::class, 'showStaffLogin'])->name('auth.staff.login');
    Route::post('/staff/login', [WebAuthController::class, 'staffLogin'])->name('auth.staff.login.post');

    // Recuperación de contraseña (si aplica)
    // ... tus rutas de password reset ...
});

Route::post('/logout', [WebAuthController::class, 'logout'])
    ->middleware('auth')
    ->name('auth.logout');

// Rutas públicas para visualizar información
Route::resource('films', FilmController::class)->only(['index', 'show']);
Route::get('films-category/{category}', [FilmController::class, 'byCategory'])->name('films.by-category');
Route::get('films-language/{language}', [FilmController::class, 'byLanguage'])->name('films.by-language');
Route::get('films-rating/{rating}', [FilmController::class, 'byRating'])->name('films.by-rating');
Route::get('films-decade/{decade}', [FilmController::class, 'byDecade'])->name('films.by-decade');
Route::get('films-recent', [FilmController::class, 'recent'])->name('films.recent');


// --- RUTAS COMPARTIDAS PARA USUARIOS AUTENTICADOS ---
Route::middleware(['auth'])->group(function () {
    // Rutas accesibles por todos los usuarios autenticados
    Route::get('rentals', [RentalController::class, 'index'])->name('rentals.index');
    Route::post('films/{film}/rent', [RentalController::class, 'rentFilm'])->name('rentals.rent-film');
    Route::post('inventory/{inventory}/rent', [App\Http\Controllers\Client\StoreController::class, 'rentMovie'])->name('stores.rent');
    
    // Rutas de pagos y cargos
    Route::get('payments', [App\Http\Controllers\Client\PaymentController::class, 'index'])->name('payments.index');
    
    // Rutas de perfil
    Route::get('profile/edit', [WebAuthController::class, 'editProfile'])->name('profile.edit');
    Route::put('profile/update', [WebAuthController::class, 'updateProfile'])->name('profile.update');
    
    // Rutas específicas para clientes
    Route::post('films/{film}/rent', [RentalController::class, 'rentFilm'])->name('rentals.rent-film');
    
    // Rutas de devolución (disponibles para clientes y empleados)
    Route::get('rentals/{rental}/return', [RentalController::class, 'returnForm'])->name('rentals.return-form');
    Route::post('rentals/{rental}/return', [RentalController::class, 'processReturn'])->name('rentals.process-return');
});


// --- RUTAS PARA EMPLEADOS (Y ADMINS) ---
Route::middleware(['auth', 'role:employee'])->group(function () {
    // DASHBOARD
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/rental-statistics', [AdminController::class, 'rentalStatistics'])->name('admin.rental-statistics');

    // RECURSOS CRUD
    Route::resource('films', FilmController::class)->except(['index', 'show']);
    Route::resource('languages', LanguageController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('inventories', InventoryController::class);
    Route::resource('stores', StoreController::class);
    Route::resource('customers', CustomerController::class);
    Route::resource('staff', StaffController::class);
    Route::resource('rentals', RentalController::class)->except(['index', 'store']); // index y store ya están en cliente

    // RUTAS ESPECIALES
    Route::get('staff/{staff}/picture', [StaffController::class, 'picture'])->name('staff.picture');
    Route::get('languages-alphabetical', [LanguageController::class, 'alphabetical'])->name('languages.alphabetical');
    Route::get('categories-alphabetical', [CategoryController::class, 'alphabetical'])->name('categories.alphabetical');
    Route::get('categories-popular', [CategoryController::class, 'popular'])->name('categories.popular');
    Route::get('films-statistics', [FilmController::class, 'statistics'])->name('films.statistics');

    // Rutas de Inventario
    Route::get('inventories-film/{film}', [InventoryController::class, 'byFilm'])->name('inventories.by-film');
    Route::get('inventories-store/{store}', [InventoryController::class, 'byStore'])->name('inventories.by-store');
    Route::get('inventories-recent', [InventoryController::class, 'recent'])->name('inventories.recent');
    Route::get('inventories-high-value', [InventoryController::class, 'highValue'])->name('inventories.high-value');
    Route::get('inventories-statistics', [InventoryController::class, 'statistics'])->name('inventories.statistics');
    Route::get('inventories-bulk-create', [InventoryController::class, 'bulkCreate'])->name('inventories.bulk-create');
    Route::post('inventories-bulk-store', [InventoryController::class, 'bulkStore'])->name('inventories.bulk-store');

    // Rutas de Alquileres
    Route::get('rentals/{rental}/return', [RentalController::class, 'returnForm'])->name('rentals.return-form');
    Route::post('rentals/{rental}/return', [RentalController::class, 'processReturn'])->name('rentals.process-return');
    Route::get('rentals-overdue', [RentalController::class, 'overdueReport'])->name('rentals.overdue');

    // Rutas de OMDB
    Route::prefix('omdb')->name('omdb.')->group(function () {
        Route::get('search', [OmdbController::class, 'search'])->name('search');
        Route::post('search-movies', [OmdbController::class, 'searchMovies'])->name('search-movies');
        Route::post('movie-details', [OmdbController::class, 'getMovieDetails'])->name('movie-details');
        Route::post('preview-import', [OmdbController::class, 'previewImport'])->name('preview-import');
        Route::post('import-movie', [OmdbController::class, 'importMovie'])->name('import-movie');
        Route::get('check-config', [OmdbController::class, 'checkConfiguration'])->name('check-config');
    });

    // Rutas de Reportes Admin
    Route::prefix('admin/reports')->name('admin.reports.')->group(function () {
        Route::get('revenue', [AdminController::class, 'getRevenueReport'])->name('revenue');
        Route::get('top-customers', [AdminController::class, 'getTopCustomers'])->name('top-customers');
        Route::get('export/csv', [AdminController::class, 'exportToCSV'])->name('export.csv');
        Route::get('export/pdf', [AdminController::class, 'exportToPDF'])->name('export.pdf');
    });

    // Rutas AJAX
    Route::post('ajax/customer-info', [RentalController::class, 'getCustomerInfo'])->name('ajax.customer-info');
    Route::post('ajax/film-availability', [RentalController::class, 'checkAvailability'])->name('ajax.film-availability');
    Route::post('ajax/update-overdue', [RentalController::class, 'updateOverdueStatus'])->name('ajax.update-overdue');
});