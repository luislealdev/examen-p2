<?php

namespace App\Http\Controllers;

use App\Models\Rental;
use App\Models\Inventory;
use App\Models\Film;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
class RentalController extends Controller
{
    /**
     * Rent a film (for employees and admins only)
     */
    public function rentFilm(Request $request, Film $film): RedirectResponse
    {
        // Verify user is employee or admin
        if (!Auth::user()->isStaff()) {
            return redirect()->back()->with('error', 'Solo empleados y administradores pueden procesar rentas.');
        }

        $request->validate([
            'customer_id' => 'required|exists:customers,customer_id',
        ]);

        // Find the staff record for the current user
        $staff = \App\Models\Staff::where('email', Auth::user()->email)
            ->orWhere('username', Auth::user()->email)
            ->first();
            
        if (!$staff) {
            return redirect()->back()->with('error', 'No se encontró el registro de personal asociado.');
        }

        // Use the employee's store automatically
        $employeeStoreId = $staff->store_id;
        
        if (!$employeeStoreId) {
            return redirect()->back()->with('error', 'El empleado no tiene una tienda asignada.');
        }

        // Find available inventory for this film in the employee's store
        $inventory = Inventory::where('film_id', $film->film_id)
            ->where('store_id', $employeeStoreId)
            ->whereDoesntHave('rentals', function ($query) {
                $query->whereNull('return_date');
            })
            ->first();

        if (!$inventory) {
            return redirect()->back()->with('error', 'No hay copias disponibles de esta película en tu tienda (Tienda #' . $employeeStoreId . ').');
        }

        // Create the rental
        Rental::create([
            'rental_date' => now(),
            'inventory_id' => $inventory->inventory_id,
            'customer_id' => $request->customer_id,
            'staff_id' => $staff->staff_id,
        ]);

        return redirect()->back()->with('success', 'Película rentada exitosamente desde la Tienda #' . $employeeStoreId . '.');
    }

    /**
     * Return a film
     */
    public function returnFilm(Request $request, Rental $rental): RedirectResponse
    {
        // Verify user is employee or admin
        if (!Auth::user()->isStaff()) {
            return redirect()->back()->with('error', 'Solo empleados y administradores pueden procesar devoluciones.');
        }

        if ($rental->isReturned()) {
            return redirect()->back()->with('error', 'Esta película ya ha sido devuelta.');
        }

        $rental->update([
            'return_date' => now(),
        ]);

        return redirect()->back()->with('success', 'Película devuelta exitosamente.');
    }

    /**
     * Check availability of a film
     */
    public function checkAvailability(Film $film, Request $request)
    {
        // Find the staff record for the current user
        $staff = \App\Models\Staff::where('email', Auth::user()->email)
            ->orWhere('username', Auth::user()->email)
            ->first();
            
        if (!$staff || !$staff->store_id) {
            return response()->json([
                'error' => 'No se encontró la tienda asignada al empleado.'
            ], 400);
        }

        // Only check availability in the employee's store
        $inventories = Inventory::where('film_id', $film->film_id)
            ->where('store_id', $staff->store_id)
            ->with(['store', 'rentals' => function ($query) {
                $query->whereNull('return_date');
            }])
            ->get();

        $availability = $inventories->map(function ($inventory) {
            return [
                'inventory_id' => $inventory->inventory_id,
                'store_id' => $inventory->store_id,
                'store_name' => 'Tienda ' . $inventory->store_id,
                'is_available' => $inventory->isAvailable(),
                'current_rental' => $inventory->currentRental(),
            ];
        });

        $availableCount = $availability->where('is_available', true)->count();
        $totalCount = $availability->count();

        return response()->json([
            'film_title' => $film->title,
            'employee_store' => $staff->store_id,
            'total_copies' => $totalCount,
            'available_copies' => $availableCount,
            'rented_copies' => $totalCount - $availableCount,
            'inventories' => $availability
        ]);
    }
}
