<?php

namespace App\Http\Controllers;

use App\Models\Rental;
use App\Models\Customer;
use App\Models\Film;
use App\Models\Inventory;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RentalController extends Controller
{
    /**
     * Display a listing of rentals
     */
    public function index(Request $request)
    {
        $query = Rental::with(['customer', 'inventory.film', 'staff', 'store']);
        
        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('store_id')) {
            $query->whereHas('inventory', function($q) use ($request) {
                $q->where('store_id', $request->store_id);
            });
        }
        
        if ($request->filled('customer_search')) {
            $search = $request->customer_search;
            $query->whereHas('customer', function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('film_search')) {
            $search = $request->film_search;
            $query->whereHas('inventory.film', function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%");
            });
        }

        $rentals = $query->orderBy('rental_date', 'desc')->paginate(20);
        $stores = Store::all();
        
        return view('rentals.index', compact('rentals', 'stores'));
    }

    /**
     * Show the form for creating a new rental
     */
    public function create(Request $request)
    {
        $stores = Store::all();
        $selectedStore = $request->store_id ? Store::find($request->store_id) : null;
        
        // Get available inventory for the selected store
        $availableInventory = [];
        if ($selectedStore) {
            $availableInventory = Inventory::with('film')
                ->where('store_id', $selectedStore->store_id)
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                          ->from('rentals')
                          ->whereColumn('rentals.inventory_id', 'inventory.inventory_id')
                          ->where('rentals.status', 'active');
                })
                ->get()
                ->groupBy('film_id')
                ->map(function ($items) {
                    $film = $items->first()->film;
                    return [
                        'film' => $film,
                        'available_count' => $items->count(),
                        'inventory_ids' => $items->pluck('inventory_id')->toArray()
                    ];
                });
        }

        return view('rentals.create', compact('stores', 'selectedStore', 'availableInventory'));
    }

    /**
     * Store a newly created rental
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,customer_id',
            'film_id' => 'required|exists:film,film_id',
            'store_id' => 'required|exists:stores,store_id',
            'rental_amount' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Check if customer can rent
            $customer = Customer::findOrFail($request->customer_id);
            if (!$customer->canRent()) {
                return back()->withErrors(['customer_id' => 'Customer has outstanding fees and cannot rent until paid.']);
            }

            // Find available inventory
            $inventory = Inventory::where('film_id', $request->film_id)
                ->where('store_id', $request->store_id)
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                          ->from('rentals')
                          ->whereColumn('rentals.inventory_id', 'inventory.inventory_id')
                          ->where('rentals.status', 'active');
                })
                ->first();

            if (!$inventory) {
                return back()->withErrors(['film_id' => 'No copies available for this film at the selected store.']);
            }

            // Get film rental duration
            $film = Film::findOrFail($request->film_id);
            $rentalDuration = $film->rental_duration ?? 3;
            
            // Create rental
            $rental = Rental::create([
                'inventory_id' => $inventory->inventory_id,
                'customer_id' => $request->customer_id,
                'staff_id' => 1, // TODO: Use actual authenticated staff
                'rental_date' => now(),
                'due_date' => now()->addDays($rentalDuration),
                'rental_amount' => $request->rental_amount,
                'status' => 'active',
                'notes' => $request->notes
            ]);

            DB::commit();

            return redirect()->route('rentals.show', $rental)
                ->with('success', 'Rental created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error creating rental: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified rental
     */
    public function show(Rental $rental)
    {
        $rental->load(['customer', 'inventory.film', 'staff', 'store']);
        
        // Update late fee if applicable
        if ($rental->is_overdue && !$rental->late_fee_applied) {
            $rental->applyLateFee();
            $rental->refresh();
        }
        
        return view('rentals.show', compact('rental'));
    }

    /**
     * Show the form for processing a return
     */
    public function returnForm(Rental $rental)
    {
        if ($rental->status !== 'active') {
            return redirect()->route('rentals.show', $rental)
                ->with('error', 'This rental cannot be returned.');
        }
        
        $rental->load(['customer', 'inventory.film', 'store']);
        
        // Calculate current late fee
        $currentLateFee = $rental->getCurrentLateFee();
        
        return view('rentals.return', compact('rental', 'currentLateFee'));
    }

    /**
     * Process a rental return
     */
    public function processReturn(Request $request, Rental $rental)
    {
        $request->validate([
            'return_condition' => 'required|string',
            'additional_fees' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000'
        ]);

        if ($rental->status !== 'active') {
            return redirect()->route('rentals.show', $rental)
                ->with('error', 'This rental cannot be returned.');
        }

        try {
            DB::beginTransaction();

            // Calculate and apply late fees
            if ($rental->is_overdue) {
                $rental->applyLateFee();
            }

            // Add any additional fees
            $additionalFees = $request->additional_fees ?? 0;
            if ($additionalFees > 0) {
                $rental->late_fee += $additionalFees;
            }

            // Update rental status
            $rental->update([
                'return_date' => now(),
                'status' => 'returned',
                'notes' => $rental->notes . "\n\nReturn Notes: " . $request->notes . "\nCondition: " . $request->return_condition
            ]);

            DB::commit();

            return redirect()->route('rentals.show', $rental)
                ->with('success', 'Rental returned successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error processing return: ' . $e->getMessage()]);
        }
    }

    /**
     * Get customer rental history and status
     */
    public function getCustomerInfo(Request $request)
    {
        $customer = Customer::with(['activeRentals.inventory.film', 'overdueRentals'])
            ->find($request->customer_id);

        if (!$customer) {
            return response()->json(['error' => 'Customer not found'], 404);
        }

        return response()->json([
            'customer' => $customer,
            'canRent' => $customer->canRent(),
            'totalOutstandingFees' => $customer->getTotalOutstandingFees(),
            'activeRentalsCount' => $customer->activeRentals()->count(),
            'overdueRentalsCount' => $customer->overdueRentals()->count()
        ]);
    }

    /**
     * Check film availability
     */
    public function checkAvailability(Request $request)
    {
        $filmId = $request->film_id;
        $storeId = $request->store_id;

        $availableCount = Inventory::where('film_id', $filmId)
            ->where('store_id', $storeId)
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                      ->from('rentals')
                      ->whereColumn('rentals.inventory_id', 'inventory.inventory_id')
                      ->where('rentals.status', 'active');
            })
            ->count();

        $film = Film::find($filmId);

        return response()->json([
            'available_count' => $availableCount,
            'film' => $film,
            'rental_rate' => $film ? $film->rental_rate : 0
        ]);
    }

    /**
     * Get overdue rentals report
     */
    public function overdueReport()
    {
        $overdueRentals = Rental::with(['customer', 'inventory.film', 'store'])
            ->where('status', 'active')
            ->where('due_date', '<', now())
            ->orderBy('due_date')
            ->get();

        // Update late fees for all overdue rentals
        foreach ($overdueRentals as $rental) {
            if (!$rental->late_fee_applied) {
                $rental->applyLateFee();
            }
        }

        return view('rentals.overdue', compact('overdueRentals'));
    }

    /**
     * Update rental status to overdue (scheduled job)
     */
    public function updateOverdueStatus()
    {
        $overdueRentals = Rental::where('status', 'active')
            ->where('due_date', '<', now())
            ->get();

        foreach ($overdueRentals as $rental) {
            $rental->update(['status' => 'overdue']);
            $rental->applyLateFee();
        }

        return response()->json(['updated' => $overdueRentals->count()]);
    }
}
