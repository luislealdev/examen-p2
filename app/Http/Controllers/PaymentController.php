<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Customer;
use App\Models\Rental;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PaymentController extends Controller
{
    /**
     * Display customer's rental history (for authenticated clients)
     */
    public function rentals(Request $request): View
    {
        $user = Auth::user();
        
        // Find customer associated with this user
        $customer = Customer::where('email', $user->email)->first();
        
        if (!$customer) {
            abort(404, 'No se encontró información del cliente.');
        }

        $query = Rental::with(['film', 'customer', 'staff', 'inventory.store'])
            ->where('customer_id', $customer->customer_id);

        // Apply filters
        if ($request->filled('date_from')) {
            $query->whereDate('rental_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('rental_date', '<=', $request->date_to);
        }

        if ($request->filled('status')) {
            if ($request->status === 'returned') {
                $query->whereNotNull('return_date');
            } elseif ($request->status === 'pending') {
                $query->whereNull('return_date');
            } elseif ($request->status === 'overdue') {
                $query->join('inventory', 'rental.inventory_id', '=', 'inventory.inventory_id')
                      ->join('film', 'inventory.film_id', '=', 'film.film_id')
                      ->whereNull('rental.return_date')
                      ->whereRaw("date(rental.rental_date, '+' || film.rental_duration || ' days') < date('now')")
                      ->select('rental.*'); // Ensure we only select rental columns
            }
        }

        if ($request->filled('store_id')) {
            $query->whereHas('inventory', function($q) use ($request) {
                $q->where('store_id', $request->store_id);
            });
        }

        $rentals = $query->orderBy('rental_date', 'desc')->paginate(15);
        
        // Calculate statistics
        $totalRentals = Rental::where('customer_id', $customer->customer_id)->count();
        $pendingRentals = Rental::where('customer_id', $customer->customer_id)
            ->whereNull('return_date')->count();
        $returnedRentals = Rental::where('customer_id', $customer->customer_id)
            ->whereNotNull('return_date')->count();
        $overdueRentals = Rental::join('inventory', 'rental.inventory_id', '=', 'inventory.inventory_id')
            ->join('film', 'inventory.film_id', '=', 'film.film_id')
            ->where('rental.customer_id', $customer->customer_id)
            ->whereNull('rental.return_date')
            ->whereRaw("date(rental.rental_date, '+' || film.rental_duration || ' days') < date('now')")
            ->count();

        return view('payments.rentals', compact('rentals', 'customer', 'totalRentals', 'pendingRentals', 'returnedRentals', 'overdueRentals'));
    }

    /**
     * Display customer's own payments (for authenticated clients)
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        
        // Find customer associated with this user
        $customer = Customer::where('email', $user->email)->first();
        
        if (!$customer) {
            abort(404, 'No se encontró información del cliente.');
        }

        $query = Payment::with(['customer', 'staff', 'rental.film'])
            ->where('customer_id', $customer->customer_id);

        // Apply filters
        if ($request->filled('date_from')) {
            $query->whereDate('payment_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('payment_date', '<=', $request->date_to);
        }

        if ($request->filled('amount_min')) {
            $query->where('amount', '>=', $request->amount_min);
        }

        if ($request->filled('amount_max')) {
            $query->where('amount', '<=', $request->amount_max);
        }

        $payments = $query->orderBy('payment_date', 'desc')->paginate(15);
        
        // Calculate totals
        $totalAmount = $query->sum('amount');
        $totalPayments = $query->count();

        return view('payments.index', compact('payments', 'customer', 'totalAmount', 'totalPayments'));
    }

    /**
     * Display customer's pending charges (for authenticated clients)
     */
    public function pending(Request $request): View
    {
        $user = Auth::user();
        
        // Find customer associated with this user
        $customer = Customer::where('email', $user->email)->first();
        
        if (!$customer) {
            abort(404, 'No se encontró información del cliente.');
        }

        // Get pending rentals (not returned yet)
        $query = Rental::with(['film', 'customer', 'staff', 'inventory.store'])
            ->where('customer_id', $customer->customer_id)
            ->whereNull('return_date');

        // Apply filters
        if ($request->filled('overdue_only') && $request->overdue_only == '1') {
            $query->join('inventory', 'rental.inventory_id', '=', 'inventory.inventory_id')
                  ->join('film', 'inventory.film_id', '=', 'film.film_id')
                  ->whereRaw("date(rental.rental_date, '+' || film.rental_duration || ' days') < date('now')")
                  ->select('rental.*'); // Ensure we only select rental columns
        }

        if ($request->filled('store_id')) {
            $query->whereHas('inventory', function($q) use ($request) {
                $q->where('store_id', $request->store_id);
            });
        }

        $pendingRentals = $query->orderBy('rental_date', 'desc')->paginate(15);
        
        // Calculate totals and late fees
        $totalPendingCount = $query->count();
        $overdueCount = Rental::join('inventory', 'rental.inventory_id', '=', 'inventory.inventory_id')
            ->join('film', 'inventory.film_id', '=', 'film.film_id')
            ->where('rental.customer_id', $customer->customer_id)
            ->whereNull('rental.return_date')
            ->whereRaw("date(rental.rental_date, '+' || film.rental_duration || ' days') < date('now')")
            ->count();

        return view('payments.pending', compact('pendingRentals', 'customer', 'totalPendingCount', 'overdueCount'));
    }

    /**
     * Display specific customer's payments (for employees/admins)
     */
    public function clientPayments(Request $request, Customer $customer): View
    {
        $query = Payment::with(['customer', 'staff', 'rental.film'])
            ->where('customer_id', $customer->customer_id);

        // Apply filters
        if ($request->filled('date_from')) {
            $query->whereDate('payment_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('payment_date', '<=', $request->date_to);
        }

        if ($request->filled('amount_min')) {
            $query->where('amount', '>=', $request->amount_min);
        }

        if ($request->filled('amount_max')) {
            $query->where('amount', '<=', $request->amount_max);
        }

        if ($request->filled('staff_id')) {
            $query->where('staff_id', $request->staff_id);
        }

        $payments = $query->orderBy('payment_date', 'desc')->paginate(15);
        
        // Calculate totals
        $totalAmount = $query->sum('amount');
        $totalPayments = $query->count();

        // Get staff for filter
        $staff = \App\Models\Staff::select('staff_id', 'first_name', 'last_name')->get();

        return view('payments.client-payments', compact('payments', 'customer', 'totalAmount', 'totalPayments', 'staff'));
    }

    /**
     * Display specific customer's pending charges (for employees/admins)
     */
    public function clientPending(Request $request, Customer $customer): View
    {
        // Get pending rentals (not returned yet)
        $query = Rental::with(['film', 'customer', 'staff', 'inventory.store'])
            ->where('customer_id', $customer->customer_id)
            ->whereNull('return_date');

        // Apply filters
        if ($request->filled('overdue_only') && $request->overdue_only == '1') {
            $query->join('inventory', 'rental.inventory_id', '=', 'inventory.inventory_id')
                  ->join('film', 'inventory.film_id', '=', 'film.film_id')
                  ->whereRaw("date(rental.rental_date, '+' || film.rental_duration || ' days') < date('now')")
                  ->select('rental.*'); // Ensure we only select rental columns
        }

        if ($request->filled('store_id')) {
            $query->whereHas('inventory', function($q) use ($request) {
                $q->where('store_id', $request->store_id);
            });
        }

        if ($request->filled('staff_id')) {
            $query->where('staff_id', $request->staff_id);
        }

        $pendingRentals = $query->orderBy('rental_date', 'desc')->paginate(15);
        
        // Calculate totals and late fees
        $totalPendingCount = $query->count();
        $overdueCount = Rental::join('inventory', 'rental.inventory_id', '=', 'inventory.inventory_id')
            ->join('film', 'inventory.film_id', '=', 'film.film_id')
            ->where('rental.customer_id', $customer->customer_id)
            ->whereNull('rental.return_date')
            ->whereRaw("date(rental.rental_date, '+' || film.rental_duration || ' days') < date('now')")
            ->count();

        // Get staff and stores for filters
        $staff = \App\Models\Staff::select('staff_id', 'first_name', 'last_name')->get();
        $stores = \App\Models\Store::select('store_id', 'address_id')->with('address')->get();

        return view('payments.client-pending', compact('pendingRentals', 'customer', 'totalPendingCount', 'overdueCount', 'staff', 'stores'));
    }

    /**
     * Process a manual payment (for employees/admins)
     */
    public function processPayment(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:999.99',
            'rental_id' => 'nullable|exists:rental,rental_id',
            'payment_type' => 'required|in:rental,late_fee,damage,other',
            'notes' => 'nullable|string|max:500'
        ]);

        $payment = Payment::create([
            'customer_id' => $customer->customer_id,
            'staff_id' => Auth::user()->isStaff() ? 
                \App\Models\Staff::where('email', Auth::user()->email)->first()?->staff_id : 1,
            'rental_id' => $validated['rental_id'],
            'amount' => $validated['amount'],
            'payment_date' => now(),
            'payment_type' => $validated['payment_type'],
            'notes' => $validated['notes']
        ]);

        return redirect()->back()->with('success', 'Pago procesado correctamente por $' . number_format((float)$payment->amount, 2));
    }
}