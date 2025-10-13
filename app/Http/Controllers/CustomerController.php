<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Customer::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by active status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'inactive') {
                $query->inactive();
            }
        }

        // Sort functionality
        $sortBy = $request->get('sort', 'last_update');
        $sortDirection = $request->get('direction', 'desc');
        
        if (in_array($sortBy, ['first_name', 'last_name', 'email', 'active', 'create_date', 'last_update'])) {
            $query->orderBy($sortBy, $sortDirection);
        }

        // Handle AJAX requests for Select2
        if ($request->filled('format') && $request->format === 'select2') {
            $customers = $query->limit(20)->get();
            return response()->json($customers);
        }

        // Pagination with request parameters preserved
        $customers = $query->paginate(15)->withQueryString();

        return view('customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $stores = Store::all();
        return view('customers.create', compact('stores'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'store_id' => 'required|integer|exists:stores,store_id',
            'first_name' => 'required|string|max:45',
            'last_name' => 'required|string|max:45',
            'email' => 'nullable|email|max:50|unique:customers,email',
            'address_id' => 'required|integer|min:1',
            'active' => 'boolean',
        ]);

        $validated['active'] = $request->boolean('active', true);

        Customer::create($validated);

        return redirect()->route('customers.index')
            ->with('success', 'Customer created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer): View
    {
        $customer->load('store'); // Eager load the store relationship
        return view('customers.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer): View
    {
        $stores = Store::all();
        return view('customers.edit', compact('customer', 'stores'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $validated = $request->validate([
            'store_id' => 'required|integer|exists:stores,store_id',
            'first_name' => 'required|string|max:45',
            'last_name' => 'required|string|max:45',
            'email' => 'nullable|email|max:50|unique:customers,email,' . $customer->customer_id . ',customer_id',
            'address_id' => 'required|integer|min:1',
            'active' => 'boolean',
        ]);

        $validated['active'] = $request->boolean('active', true);

        $customer->update($validated);

        return redirect()->route('customers.index')
            ->with('success', 'Customer updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer): RedirectResponse
    {
        // Instead of deleting, we mark as inactive (soft delete approach)
        $customer->update(['active' => false]);

        return redirect()->route('customers.index')
            ->with('success', 'Customer deactivated successfully!');
    }
}
