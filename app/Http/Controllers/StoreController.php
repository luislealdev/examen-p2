<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\Staff;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $stores = Store::with(['manager', 'address'])->latest('last_update')->paginate(10);
        return view('store.index', compact('stores'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $staff = Staff::where('active', 1)->get();
        $addresses = Address::orderBy('city')->orderBy('address')->get();
        return view('store.create', compact('staff', 'addresses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // Basic validation
        $validated = $request->validate([
            'manager_staff_id' => 'required|exists:staff,staff_id',
            'address_id' => 'nullable|exists:addresses,address_id',
        ]);

        // Additional validation for new address fields if creating new address
        if ($request->filled(['new_address'])) {
            $newAddressValidated = $request->validate([
                'new_address' => 'required|string|max:100',
                'new_district' => 'nullable|string|max:50',
                'new_postal_code' => 'nullable|string|max:20',
                'new_phone' => 'nullable|string|max:20',
            ]);
        }

        DB::beginTransaction();
        try {
            // Determine address ID
            if ($request->filled('new_address')) {
                // Create new address
                $address = Address::create([
                    'address' => $newAddressValidated['new_address'],
                    'district' => $newAddressValidated['new_district'] ?? null,
                    'postal_code' => $newAddressValidated['new_postal_code'] ?? null,
                    'phone' => $newAddressValidated['new_phone'] ?? null,
                ]);
                $addressId = $address->address_id;
                \Log::info('New address created in store creation', ['address_id' => $addressId]);
            } elseif ($request->filled('address_id')) {
                // Use existing address
                $addressId = $validated['address_id'];
            } else {
                throw new \Exception('Debe seleccionar una dirección existente o crear una nueva');
            }

            // Create store
            Store::create([
                'manager_staff_id' => $validated['manager_staff_id'],
                'address_id' => $addressId,
            ]);

            DB::commit();
            return redirect()->route('stores.index')
                ->with('success', 'Tienda creada exitosamente!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Error al crear la tienda: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Store $store): View
    {
        $store->load(['manager', 'address', 'inventories.film']);
        return view('store.show', compact('store'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Store $store): View
    {
        $store->load(['manager', 'address']);
        $staff = Staff::where('active', 1)->get();
        $addresses = Address::orderBy('city')->orderBy('address')->get();
        return view('store.edit', compact('store', 'staff', 'addresses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Store $store): RedirectResponse
    {
        \Log::info('Store update attempt', [
            'store_id' => $store->store_id,
            'request_data' => $request->all()
        ]);

        try {
            // Basic validation
            $validated = $request->validate([
                'manager_staff_id' => 'required|exists:staff,staff_id',
                'address_id' => 'nullable|exists:addresses,address_id',
            ]);

            DB::beginTransaction();
            $addressId = $store->address_id; // Default to current address

            // Check if creating new address
            if ($request->filled('new_address')) {
                $newAddressValidated = $request->validate([
                    'new_address' => 'required|string|max:100',
                    'new_district' => 'nullable|string|max:50',
                    'new_postal_code' => 'nullable|string|max:20',
                    'new_phone' => 'nullable|string|max:20',
                ]);

                // Create new address
                $newAddress = Address::create([
                    'address' => $newAddressValidated['new_address'],
                    'district' => $newAddressValidated['new_district'],
                    'postal_code' => $newAddressValidated['new_postal_code'],
                    'phone' => $newAddressValidated['new_phone'],
                ]);
                
                $addressId = $newAddress->address_id;
                \Log::info('New address created', ['address_id' => $addressId]);
                
            } elseif ($request->filled('edit_address_line')) {
                $addressValidated = $request->validate([
                    'edit_address_line' => 'required|string|max:100',
                    'edit_district' => 'nullable|string|max:50',
                    'edit_postal_code' => 'nullable|string|max:20',
                    'edit_phone' => 'nullable|string|max:20',
                ]);

                // Update current address
                $store->address->update([
                    'address' => $addressValidated['edit_address_line'],
                    'district' => $addressValidated['edit_district'],
                    'postal_code' => $addressValidated['edit_postal_code'],
                    'phone' => $addressValidated['edit_phone'],
                ]);
                
                $addressId = $store->address_id; // Keep current address ID
                \Log::info('Address updated', ['address_id' => $addressId]);
                
            } elseif ($request->filled('address_id')) {
                // Use selected existing address
                $addressId = $validated['address_id'];
            }

            // Update store (manager and address)
            $store->update([
                'manager_staff_id' => $validated['manager_staff_id'],
                'address_id' => $addressId,
            ]);

            DB::commit();
            \Log::info('Store updated successfully', ['store_id' => $store->store_id]);
            
            return redirect()->route('stores.index')
                ->with('success', 'Tienda actualizada exitosamente!');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            \Log::warning('Validation error in store update', ['errors' => $e->errors()]);
            return back()->withErrors($e->errors())->withInput();
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Store update error: ' . $e->getMessage(), [
                'store_id' => $store->store_id,
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withInput()->withErrors(['error' => 'Error al actualizar la tienda: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Store $store): RedirectResponse
    {
        $store->delete();

        return redirect()->route('stores.index')
            ->with('success', 'Store deleted successfully!');
    }
}
