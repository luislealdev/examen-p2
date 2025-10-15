<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Obtener todas las rentas del cliente con sus pagos y cargos
        $rentals = Rental::with(['inventory.film'])
            ->where('customer_id', $user->id)
            ->orderBy('rental_date', 'desc')
            ->get();

        // Calcular totales
        $totalPagado = $rentals->sum('rental_amount');
        $totalCargosExtra = $rentals->sum('late_fees');
        $totalPendiente = $rentals->whereNull('return_date')->sum('rental_amount');

        return view('client.payments.index', compact(
            'rentals',
            'totalPagado',
            'totalCargosExtra',
            'totalPendiente'
        ));
    }
}