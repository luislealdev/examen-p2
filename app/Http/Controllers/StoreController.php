<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $stores = Store::with(['manager', 'address.city.country'])
                      ->latest('last_update')
                      ->paginate(10);
        return view('store.index', compact('stores'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $staff = \App\Models\Staff::where('active', true)->get();
        $addresses = \App\Models\Address::with('city.country')->get();
        $countries = \App\Models\Country::all();
        
        return view('store.create', compact('staff', 'addresses', 'countries'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validationRules = [
            'manager_staff_id' => 'required|integer|exists:staff,staff_id',
            'address_option' => 'required|in:select_existing,create_new',
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

        // Manejar la dirección según la opción seleccionada
        if ($request->address_option === 'select_existing') {
            $addressId = $validated['address_id'];
        } else {
            // Crear nueva dirección
            $addressData = [
                'address' => $validated['address_line1'],
                'address2' => $validated['address_line2'],
                'district' => $validated['district'],
                'postal_code' => $validated['postal_code'],
                'phone' => $validated['phone'],
                'city_id' => $validated['city_id'],
                'last_update' => now(),
            ];
            
            $newAddress = \App\Models\Address::create($addressData);
            $addressId = $newAddress->address_id;
        }

        Store::create([
            'manager_staff_id' => $validated['manager_staff_id'],
            'address_id' => $addressId,
            'last_update' => now(),
        ]);

        return redirect()->route('stores.index')
            ->with('success', 'Tienda creada exitosamente!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Store $store): View
    {
        $store->load([
            'manager.address.city.country',
            'address.city.country'
        ]);
        return view('store.show', compact('store'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Store $store): View
    {
        $staff = \App\Models\Staff::where('active', true)->get();
        $addresses = \App\Models\Address::with('city.country')->get();
        $countries = \App\Models\Country::all();
        
        return view('store.edit', compact('store', 'staff', 'addresses', 'countries'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Store $store): RedirectResponse
    {
        $validationRules = [
            'manager_staff_id' => 'required|integer|exists:staff,staff_id',
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

        // Manejar la dirección según la opción seleccionada
        if ($request->address_option === 'select_existing') {
            // Usar dirección existente
            $store->update([
                'manager_staff_id' => $validated['manager_staff_id'],
                'address_id' => $validated['address_id'],
                'last_update' => now(),
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

            if ($request->address_option === 'edit_current' && $store->address) {
                // Actualizar dirección existente
                $store->address->update($addressData);
                $addressId = $store->address_id;
            } else {
                // Crear nueva dirección
                $newAddress = \App\Models\Address::create($addressData);
                $addressId = $newAddress->address_id;
            }

            // Actualizar store
            $store->update([
                'manager_staff_id' => $validated['manager_staff_id'],
                'address_id' => $addressId,
                'last_update' => now(),
            ]);
        }

        return redirect()->route('stores.show', $store->store_id)
            ->with('success', 'Tienda actualizada exitosamente!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Store $store): RedirectResponse
    {
        $store->delete();

        return redirect()->route('stores.index')
            ->with('success', 'Tienda eliminada exitosamente!');
    }
}
