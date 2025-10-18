<?php

namespace App\Http\Controllers;

use App\Models\Rental;
use App\Models\Inventory;
use App\Models\Film;
use App\Models\Customer;
use App\Models\InventoryMovement;
use App\Services\InventoryManagementService;
use App\Services\BusinessActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
class RentalController extends Controller
{
    /**
     * Display all active rentals with return management
     */
    public function index(Request $request): View
    {
        $query = Rental::with(['inventory.film.category', 'customer', 'staff.store'])
                      ->orderBy('rental_date', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->whereNull('return_date');
            } elseif ($request->status === 'returned') {
                $query->whereNotNull('return_date');
            }
        } else {
            // Por defecto mostrar solo activas
            $query->whereNull('return_date');
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('customer', function($sq) use ($search) {
                    $sq->where('first_name', 'like', '%' . $search . '%')
                       ->orWhere('last_name', 'like', '%' . $search . '%')
                       ->orWhere('email', 'like', '%' . $search . '%');
                })
                ->orWhereHas('inventory.film', function($sq) use ($search) {
                    $sq->where('title', 'like', '%' . $search . '%');
                })
                ->orWhere('rental_id', 'like', '%' . $search . '%');
            });
        }

        // Filter by overdue (más de 7 días)
        if ($request->filled('overdue') && $request->overdue) {
            $query->where('rental_date', '<', now()->subDays(7))
                  ->whereNull('return_date');
        }

        // Filter by customer
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        $rentals = $query->paginate(20);

        // Stats for dashboard
        $stats = [
            'active' => Rental::whereNull('return_date')->count(),
            'overdue' => Rental::whereNull('return_date')
                              ->where('rental_date', '<', now()->subDays(7))
                              ->count(),
            'today' => Rental::whereDate('rental_date', today())->count(),
            'this_week' => Rental::whereBetween('rental_date', [now()->startOfWeek(), now()->endOfWeek()])->count(),
        ];

        return view('rentals.index', compact('rentals', 'stats'));
    }

    /**
     * Process a rental return directly from rentals view
     */
    public function processReturn(Request $request, Rental $rental): RedirectResponse
    {
        $request->validate([
            'condition' => 'required|in:available,damaged,lost',
            'notes' => 'nullable|string|max:1000',
            'process_payment' => 'boolean',
            'payment_amount' => 'nullable|numeric|min:0',
            'payment_type' => 'nullable|in:rental,late_fee,damage,other',
        ]);

        if ($rental->isReturned()) {
            return redirect()->back()
                           ->with('error', 'Esta renta ya ha sido devuelta.');
        }

        // Calculate payment details
        $rental->load('film');
        $paymentDetails = $this->calculatePaymentForReturn($rental, $request->condition);

        // Process inventory return
        $inventoryService = app(InventoryManagementService::class);
        $result = $inventoryService->processReturn(
            $rental->rental_id,
            $request->condition,
            $request->notes
        );

        if ($result['success']) {
            // Process payment if requested
            if ($request->process_payment && $paymentDetails['total_amount'] > 0) {
                $this->processReturnPayment($rental, $paymentDetails, $request);
            }

            $message = $result['message'];
            if ($paymentDetails['total_amount'] > 0 && !$request->process_payment) {
                $message .= ' Cantidad pendiente de pago: $' . number_format($paymentDetails['total_amount'], 2);
            }

            return redirect()->back()
                           ->with('success', $message);
        } else {
            return redirect()->back()
                           ->with('error', $result['message'])
                           ->withInput();
        }
    }

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

        // Verify customer is not blocked due to overdue rentals
        $customer = Customer::find($request->customer_id);
        if ($customer->shouldBeBlocked()) {
            $overdueRentals = $customer->overdueRentals;
            $totalLateFees = $customer->getTotalLateFees();
            
            return redirect()->back()->with('error', 
                "Cliente bloqueado: {$customer->full_name} tiene " . 
                count($overdueRentals) . " película(s) en retraso con cargos de \${$totalLateFees}. " .
                "Debe devolver las películas pendientes antes de rentar nuevas películas."
            );
        }

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

        // Find available inventory for this film in the employee's store (in good condition and not rented)
        $inventory = Inventory::where('film_id', $film->film_id)
            ->where('store_id', $employeeStoreId)
            ->where('condition', 'available') // Only allow rentals of items in good condition
            ->whereDoesntHave('rentals', function ($query) {
                $query->whereNull('return_date');
            })
            ->first();

        if (!$inventory) {
            // Check if there are copies but they're damaged or lost
            $damagedOrLostCount = Inventory::where('film_id', $film->film_id)
                ->where('store_id', $employeeStoreId)
                ->whereIn('condition', ['damaged', 'lost'])
                ->count();
            
            $rentedCount = Inventory::where('film_id', $film->film_id)
                ->where('store_id', $employeeStoreId)
                ->where('condition', 'available')
                ->whereHas('rentals', function ($query) {
                    $query->whereNull('return_date');
                })
                ->count();

            $errorMessage = 'No hay copias disponibles de esta película en tu tienda (Tienda #' . $employeeStoreId . ').';
            
            if ($damagedOrLostCount > 0 || $rentedCount > 0) {
                $errorMessage .= ' ';
                if ($rentedCount > 0) {
                    $errorMessage .= $rentedCount . ' copia(s) están rentadas. ';
                }
                if ($damagedOrLostCount > 0) {
                    $errorMessage .= $damagedOrLostCount . ' copia(s) están dañadas o perdidas.';
                }
            }
            
            return redirect()->back()->with('error', $errorMessage);
        }

        // Create the rental
        $rental = Rental::create([
            'rental_date' => now(),
            'inventory_id' => $inventory->inventory_id,
            'customer_id' => $request->customer_id,
            'staff_id' => $staff->staff_id,
        ]);

        // Log the rental movement
        InventoryMovement::create([
            'inventory_id' => $inventory->inventory_id,
            'rental_id' => $rental->rental_id,
            'movement_type' => 'rental',
            'condition_from' => 'available',
            'condition_to' => 'available', // Still available but rented
            'user_id' => Auth::id(),
            'staff_id' => $staff->staff_id,
            'customer_id' => $request->customer_id,
            'notes' => 'Película rentada desde la tienda #' . $employeeStoreId,
            'metadata' => [
                'store_id' => $employeeStoreId,
                'film_title' => $film->title,
                'rental_date' => now()->toISOString(),
            ],
        ]);

        // Log business activity
        BusinessActivityLogger::logRental('create', $rental->rental_id, $request->customer_id, 
            $film->film_id, $staff->staff_id, [
            'inventory_id' => $inventory->inventory_id,
            'film_title' => $film->title,
            'store_id' => $employeeStoreId,
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

    /**
     * Search customers for rental form autocomplete
     */
    public function searchCustomers(Request $request)
    {
        $query = $request->get('q');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $customers = Customer::where(function ($q) use ($query) {
            $q->where('first_name', 'LIKE', '%' . $query . '%')
              ->orWhere('last_name', 'LIKE', '%' . $query . '%')
              ->orWhere('email', 'LIKE', '%' . $query . '%')
              ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ['%' . $query . '%']);
        })
        ->with('rentals')
        ->select('customer_id', 'first_name', 'last_name', 'email')
        ->orderBy('last_name')
        ->limit(10)
        ->get();

        return response()->json($customers->map(function ($customer) {
            $isBlocked = $customer->shouldBeBlocked();
            $blockedText = $isBlocked ? ' 🚫 BLOQUEADO' : '';
            
            return [
                'id' => $customer->customer_id,
                'text' => $customer->first_name . ' ' . $customer->last_name . $blockedText,
                'email' => $customer->email,
                'full_text' => $customer->first_name . ' ' . $customer->last_name . ' (' . $customer->email . ')' . $blockedText,
                'is_blocked' => $isBlocked
            ];
        }));
    }

    /**
     * Calculate payment details for a rental return
     */
    private function calculatePaymentForReturn(Rental $rental, string $condition): array
    {
        $rental->load('film');
        $dueDate = $rental->rental_date->addDays($rental->film->rental_duration);
        $returnDate = now();
        
        $details = [
            'rental_fee' => (float) $rental->film->rental_rate,
            'late_fee' => 0,
            'damage_fee' => 0,
            'total_amount' => 0,
            'is_overdue' => $returnDate->isAfter($dueDate),
            'days_late' => 0
        ];

        // Calculate late fee if overdue
        if ($details['is_overdue']) {
            $details['days_late'] = $dueDate->diffInDays($returnDate);
            $details['late_fee'] = $details['days_late'] * 1.50; // $1.50 per day late
        }

        // Calculate damage fee
        if ($condition === 'damaged') {
            $details['damage_fee'] = (float) $rental->film->replacement_cost * 0.10; // 10% of replacement cost
        } elseif ($condition === 'lost') {
            $details['damage_fee'] = (float) $rental->film->replacement_cost; // Full replacement cost
        }

        $details['total_amount'] = $details['rental_fee'] + $details['late_fee'] + $details['damage_fee'];

        return $details;
    }

    /**
     * Process payment for a return
     */
    private function processReturnPayment(Rental $rental, array $paymentDetails, Request $request): void
    {
        $staff = \App\Models\Staff::where('email', Auth::user()->email)->first();
        
        // Create payments for each type
        if ($paymentDetails['rental_fee'] > 0) {
            \App\Models\Payment::create([
                'customer_id' => $rental->customer_id,
                'staff_id' => $staff ? $staff->staff_id : 1,
                'rental_id' => $rental->rental_id,
                'amount' => $paymentDetails['rental_fee'],
                'payment_date' => now(),
                'payment_type' => 'rental',
                'notes' => 'Pago de renta al momento de devolución'
            ]);
        }

        if ($paymentDetails['late_fee'] > 0) {
            \App\Models\Payment::create([
                'customer_id' => $rental->customer_id,
                'staff_id' => $staff ? $staff->staff_id : 1,
                'rental_id' => $rental->rental_id,
                'amount' => $paymentDetails['late_fee'],
                'payment_date' => now(),
                'payment_type' => 'late_fee',
                'notes' => "Multa por {$paymentDetails['days_late']} día(s) de retraso"
            ]);
        }

        if ($paymentDetails['damage_fee'] > 0) {
            $damageType = $request->condition === 'lost' ? 'pérdida' : 'daño';
            \App\Models\Payment::create([
                'customer_id' => $rental->customer_id,
                'staff_id' => $staff ? $staff->staff_id : 1,
                'rental_id' => $rental->rental_id,
                'amount' => $paymentDetails['damage_fee'],
                'payment_date' => now(),
                'payment_type' => 'damage',
                'notes' => "Cargo por {$damageType} de película"
            ]);
        }
    }
}
