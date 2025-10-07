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
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
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
// Route::resource('stores', StoreController::class);
// Route::resource('inventories', InventoryController::class);
// Route::resource('staff', StaffController::class);
// Route::resource('customers', CustomerController::class);
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
