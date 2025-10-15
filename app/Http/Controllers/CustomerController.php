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
    public function index(Request $request): View
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

        // Pagination with request parameters preserved
        $customers = $query->paginate(15)->withQueryString();

        return view('customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $stores = Store::with('address.city.country', 'manager')->get();
        $addresses = \App\Models\Address::with('city.country')->get();
        
        return view('customers.create', compact('stores', 'addresses'));
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
        $customer->load([
            'store.address.city.country',
            'store.manager', 
            'address.city.country'
        ]);
        return view('customers.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer): View
    {
        $stores = Store::with('address.city.country', 'manager')->get();
        $addresses = \App\Models\Address::with('city.country')->get();
        $countries = \App\Models\Country::all();
        $cities = \App\Models\City::all();
        
        return view('customers.edit', compact('customer', 'stores', 'addresses', 'countries', 'cities'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $validationRules = [
            'store_id' => 'required|integer|exists:stores,store_id',
            'first_name' => 'required|string|max:45',
            'last_name' => 'required|string|max:45',
            'email' => 'nullable|email|max:50|unique:customers,email,' . $customer->customer_id . ',customer_id',
            'active' => 'boolean',
            'address_option' => 'required|in:edit_current,select_existing,create_new',
        ];

        // Validación condicional según la opción de dirección
        if ($request->address_option === 'select_existing') {
            $validationRules['address_id'] = 'required|integer|exists:address,address_id';
        } else {
            $validationRules = array_merge($validationRules, [
                'address_line1' => 'required|string|max:50',
                'address_line2' => 'nullable|string|max:50',
                'district' => 'required|string|max:20',
                'postal_code' => 'required|string|max:10',
                'phone' => 'nullable|string|max:20',
                'country_id' => 'required|integer|exists:country,country_id',
                'city_id' => 'required|integer|exists:city,city_id',
            ]);
        }

        $validated = $request->validate($validationRules);
        $validated['active'] = $request->boolean('active', true);

        // Manejar la dirección según la opción seleccionada
        if ($request->address_option === 'select_existing') {
            // Usar dirección existente
            $customer->update([
                'store_id' => $validated['store_id'],
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'address_id' => $validated['address_id'],
                'active' => $validated['active'],
            ]);
        } else {
            // Editar dirección actual o crear nueva
            $addressData = [
                'address' => $validated['address_line1'],
                'address2' => $validated['address_line2'],
                'district' => $validated['district'],
                'postal_code' => $validated['postal_code'],
                'phone' => $validated['phone'],
                'city_id' => $validated['city_id'],
                'last_update' => now(),
            ];

            if ($request->address_option === 'edit_current' && $customer->address) {
                // Actualizar dirección existente
                $customer->address->update($addressData);
                $addressId = $customer->address_id;
            } else {
                // Crear nueva dirección
                $newAddress = \App\Models\Address::create($addressData);
                $addressId = $newAddress->address_id;
            }

            // Actualizar customer
            $customer->update([
                'store_id' => $validated['store_id'],
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'address_id' => $addressId,
                'active' => $validated['active'],
            ]);
        }

        return redirect()->route('customers.show', $customer->customer_id)
            ->with('success', 'Cliente actualizado exitosamente!');
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
