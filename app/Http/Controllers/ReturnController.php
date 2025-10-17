<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Models\Rental;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Services\InventoryManagementService;
use App\Services\BusinessActivityLogger;

class ReturnController extends Controller
{
    protected $inventoryService;

    public function __construct(InventoryManagementService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * Show active rentals (pending returns)
     */
    public function index(Request $request): View
    {
        $query = Rental::with(['inventory.film', 'customer', 'staff'])
                      ->whereNull('return_date')
                      ->orderBy('rental_date', 'desc');

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

        // Filter by overdue
        if ($request->filled('overdue') && $request->overdue) {
            $query->where('rental_date', '<', now()->subDays(7)); // Assuming 7 days rental period
        }

        $rentals = $query->paginate(20);

        return view('returns.index', compact('rentals'));
    }

    /**
     * Show return form for a specific rental
     */
    public function show(Rental $rental)
    {
        if ($rental->isReturned()) {
            return redirect()->route('returns.index')
                           ->with('error', 'Esta renta ya ha sido devuelta.');
        }

        $rental->load(['inventory.film', 'customer', 'staff']);
        
        return view('returns.show', compact('rental'));
    }

    /**
     * Process the return
     */
    public function store(Request $request, Rental $rental): RedirectResponse
    {
        $request->validate([
            'condition' => 'required|in:available,damaged,lost',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($rental->isReturned()) {
            return redirect()->route('returns.index')
                           ->with('error', 'Esta renta ya ha sido devuelta.');
        }

        $result = $this->inventoryService->processReturn(
            $rental->rental_id,
            $request->condition,
            $request->notes
        );

        if ($result['success']) {
            return redirect()->route('returns.index')
                           ->with('success', $result['message']);
        } else {
            return redirect()->back()
                           ->with('error', $result['message'])
                           ->withInput();
        }
    }

    /**
     * Show inventory management page
     */
    public function inventory(Request $request): View
    {
        $query = Inventory::with(['film', 'store'])
                         ->orderBy('condition')
                         ->orderBy('inventory_id', 'desc');

        // Filter by condition
        if ($request->filled('condition')) {
            $query->byCondition($request->condition);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('film', function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%');
            });
        }

        // Filter by store
        if ($request->filled('store_id')) {
            $query->where('store_id', $request->store_id);
        }

        $inventories = $query->paginate(20);
        $stats = $this->inventoryService->getInventoryStats();

        return view('returns.inventory', compact('inventories', 'stats'));
    }

    /**
     * Show detailed inventory movements for a specific item
     */
    public function movements(Inventory $inventory): View
    {
        $data = $this->inventoryService->getInventoryMovements($inventory->inventory_id);
        
        return view('returns.movements', $data);
    }

    /**
     * Mark inventory as damaged
     */
    public function markDamaged(Request $request, Inventory $inventory): RedirectResponse
    {
        $request->validate([
            'notes' => 'required|string|max:1000',
        ]);

        $result = $this->inventoryService->markAsDamaged(
            $inventory->inventory_id,
            $request->notes
        );

        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        } else {
            return redirect()->back()->with('error', $result['message']);
        }
    }

    /**
     * Mark inventory as lost
     */
    public function markLost(Request $request, Inventory $inventory): RedirectResponse
    {
        $request->validate([
            'notes' => 'required|string|max:1000',
        ]);

        $result = $this->inventoryService->markAsLost(
            $inventory->inventory_id,
            $request->notes
        );

        if ($result['success']) {
            $message = $result['message'];
            if ($result['was_rented']) {
                $message .= ' Nota: La película estaba rentada cuando se marcó como perdida.';
            }
            return redirect()->back()->with('success', $message);
        } else {
            return redirect()->back()->with('error', $result['message']);
        }
    }

    /**
     * Repair inventory item
     */
    public function repair(Request $request, Inventory $inventory): RedirectResponse
    {
        $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        $result = $this->inventoryService->repairItem(
            $inventory->inventory_id,
            $request->notes
        );

        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        } else {
            return redirect()->back()->with('error', $result['message']);
        }
    }
}
