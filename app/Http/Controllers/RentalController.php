<?php

namespace App\Http\Controllers;

use App\Models\Rental;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RentalController extends Controller
{
    public function index()
    {
        $rentals = Rental::with(['inventory.film', 'customer'])
            ->where('customer_id', Auth::id())
            ->orderBy('rental_date', 'desc')
            ->paginate(10);

        return view('rentals.index', compact('rentals'));
    }

    public function store(Request $request, Film $film)
    {
        // Buscar inventario disponible
        $inventory = Inventory::where('film_id', $film->id)
            ->whereDoesntHave('rentals', function($query) {
                $query->whereNull('return_date');
            })
            ->first();

        if (!$inventory) {
            return back()->with('error', 'Lo sentimos, esta película no está disponible para alquiler en este momento.');
        }

        // Crear el alquiler
        $rental = new Rental();
        $rental->rental_date = now();
        $rental->inventory_id = $inventory->id;
        $rental->customer_id = Auth::id();
        $rental->staff_id = 1; // TODO: Asignar un empleado automáticamente
        $rental->save();

        return redirect()->route('rentals.index')
            ->with('success', 'Película alquilada correctamente. ¡Disfrútala!');
    }
}