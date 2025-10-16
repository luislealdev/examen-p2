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
            'store_id' => 'required|exists:stores,store_id',
        ]);

        // Find available inventory for this film in the specified store
        $inventory = Inventory::where('film_id', $film->film_id)
            ->where('store_id', $request->store_id)
            ->whereDoesntHave('rentals', function ($query) {
                $query->whereNull('return_date');
            })
            ->first();

        if (!$inventory) {
            return redirect()->back()->with('error', 'No hay copias disponibles de esta película en la tienda seleccionada.');
        }

        // Find the staff record for the current user
        $staff = \App\Models\Staff::where('email', Auth::user()->email)
            ->orWhere('username', Auth::user()->email)
            ->first();
            
        if (!$staff) {
            return redirect()->back()->with('error', 'No se encontró el registro de personal asociado.');
        }

        // Create the rental
        Rental::create([
            'rental_date' => now(),
            'inventory_id' => $inventory->inventory_id,
            'customer_id' => $request->customer_id,
            'staff_id' => $staff->staff_id,
        ]);

        return redirect()->back()->with('success', 'Película rentada exitosamente.');
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
        $storeId = $request->get('store_id');
        
        $query = Inventory::where('film_id', $film->film_id);
        
        if ($storeId) {
            $query->where('store_id', $storeId);
        }
        
        $inventories = $query->with(['store', 'rentals' => function ($query) {
            $query->whereNull('return_date');
        }])->get();

        $availability = $inventories->map(function ($inventory) {
            return [
                'inventory_id' => $inventory->inventory_id,
                'store_id' => $inventory->store_id,
                'store_name' => $inventory->store->manager_staff_id ?? 'Tienda ' . $inventory->store_id,
                'is_available' => $inventory->isAvailable(),
                'current_rental' => $inventory->currentRental(),
            ];
        });

        return response()->json($availability);
    }
}
