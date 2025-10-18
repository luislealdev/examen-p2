<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Store;
use App\Services\BusinessActivityLogger;
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
        $query = Customer::with(['store.address', 'address.city.country']);

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
        $countries = \App\Models\Country::orderBy('country')->get();
        $isAdmin = auth()->user()->isAdmin();
        $employeeStore = null;
        
        // Si es empleado, obtener su tienda asignada
        if (!$isAdmin) {
            $staff = \App\Models\Staff::where('email', auth()->user()->email)
                ->orWhere('username', auth()->user()->email)
                ->first();
            
            if ($staff && $staff->store) {
                $employeeStore = $staff->store->load(['address.city.country', 'manager']);
            }
        }
        
        return view('customers.create', compact('countries', 'isAdmin', 'employeeStore'));
    }

    /**
     * Search stores for autocomplete
     */
    public function searchStores(Request $request)
    {
        // Validar y sanitizar entrada
        $request->validate([
            'q' => 'required|string|min:1|max:100|regex:/^[a-zA-Z0-9\s\-_.,]+$/'
        ]);
        
        $query = trim($request->get('q'));
        
        // Escapar caracteres especiales para LIKE
        $query = str_replace(['%', '_'], ['\%', '\_'], $query);

        \Log::info('Searching stores', ['query' => $query]);

        $stores = Store::with(['address.city.country', 'manager'])
            ->where(function($q) use ($query) {
                $q->where('store_id', 'LIKE', '%' . $query . '%')
                  ->orWhereHas('address.city', function ($subQ) use ($query) {
                      $subQ->where('city', 'LIKE', '%' . $query . '%');
                  })
                  ->orWhereHas('address.city.country', function ($subQ) use ($query) {
                      $subQ->where('country', 'LIKE', '%' . $query . '%');
                  });
            })
            ->orderBy('store_id')
            ->limit(10)
            ->get();

        \Log::info('Stores found', ['count' => $stores->count()]);

        $result = $stores->map(function ($store) {
            $cityName = optional(optional($store->address)->city)->city ?? 'Ciudad desconocida';
            $countryName = optional(optional(optional($store->address)->city)->country)->country ?? 'País desconocido';
            $managerName = $store->manager ? $store->manager->full_name : 'Sin gerente';
            $address = optional($store->address)->address ?? 'Sin dirección';
            
            return [
                'id' => $store->store_id,
                'text' => "Tienda {$store->store_id} - {$cityName}, {$countryName}",
                'manager' => $managerName,
                'address' => $address,
                'full_text' => "Tienda {$store->store_id} - {$cityName}, {$countryName} (Gerente: {$managerName})"
            ];
        });

        return response()->json($result);
    }

    /**
     * Get cities by country for dynamic dropdown
     */
    public function getCitiesByCountry(Request $request)
    {
        $countryId = $request->get('country_id');
        
        if (!$countryId) {
            return response()->json(['error' => 'country_id is required'], 400);
        }
        
        $cities = \App\Models\City::where('country_id', $countryId)
            ->orderBy('city')
            ->get(['city_id', 'city']);

        \Log::info('Cities loaded for country', [
            'country_id' => $countryId,
            'cities_count' => $cities->count()
        ]);

        return response()->json($cities);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $isAdmin = auth()->user()->isAdmin();
        
        // Reglas de validación base
        $validationRules = [
            'first_name' => 'required|string|max:45',
            'last_name' => 'required|string|max:45',
            'email' => 'nullable|email|max:50|unique:customers,email',
            'active' => 'boolean',
            // Campos de dirección
            'address_line1' => 'required|string|max:50',
            'address_line2' => 'nullable|string|max:50',
            'district' => 'required|string|max:20',
            'postal_code' => 'required|string|max:10',
            'phone' => 'nullable|string|max:20',
            'city_id' => 'required|integer|exists:city,city_id',
        ];
        
        // Solo administradores pueden seleccionar tienda
        if ($isAdmin) {
            $validationRules['store_id'] = 'required|integer|exists:stores,store_id';
        }
        
        $validated = $request->validate($validationRules);
        $validated['active'] = $request->boolean('active', true);

        // Determinar la tienda
        if ($isAdmin) {
            $storeId = $validated['store_id'];
        } else {
            // Para empleados, usar su tienda asignada
            $staff = \App\Models\Staff::where('email', auth()->user()->email)
                ->orWhere('username', auth()->user()->email)
                ->first();
                
            if (!$staff || !$staff->store_id) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'No se encontró la tienda asignada al empleado.');
            }
            
            $storeId = $staff->store_id;
        }

        // Crear la dirección primero
        $address = \App\Models\Address::create([
            'address' => $validated['address_line1'],
            'address2' => $validated['address_line2'],
            'district' => $validated['district'],
            'postal_code' => $validated['postal_code'],
            'phone' => $validated['phone'],
            'city_id' => $validated['city_id'],
            'last_update' => now(),
        ]);

        // Crear el cliente con la dirección creada
        $customer = Customer::create([
            'store_id' => $storeId,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'address_id' => $address->address_id,
            'active' => $validated['active'],
        ]);

        // Log de actividad de negocio
        BusinessActivityLogger::logCustomer('create', $customer->customer_id, [
            'customer_name' => $customer->full_name,
            'email' => $customer->email,
            'store_id' => $storeId,
            'active' => $validated['active'],
            'created_by_admin' => $isAdmin,
        ]);

        $storeText = $isAdmin ? 'con la tienda seleccionada' : "asignado a tu tienda (Tienda #{$storeId})";
        
        return redirect()->route('customers.index')
            ->with('success', "Cliente creado exitosamente {$storeText}!");
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer): View
    {
        // Log de visualización del cliente
        BusinessActivityLogger::logCustomer('view', $customer->customer_id, [
            'customer_name' => $customer->full_name,
            'viewed_by_user_id' => auth()->id(),
        ]);

        // Cargar relaciones básicas del cliente
        $customer->load([
            'store.address.city.country',
            'store.manager', 
            'address.city.country'
        ]);

        // Cargar rentals activos con información detallada
        $activeRentals = $customer->rentals()
            ->with(['inventory.film.category', 'inventory.store', 'staff'])
            ->whereNull('return_date')
            ->orderBy('rental_date', 'desc')
            ->get()
            ->map(function($rental) {
                // Calcular días de renta y retraso
                $rentalDays = now()->diffInDays($rental->rental_date);
                $dueDate = $rental->rental_date->addDays(7); // 7 días período de renta
                $rental->rental_days = $rentalDays;
                $rental->due_date = $dueDate;
                $rental->is_overdue = now()->isAfter($dueDate);
                $rental->days_overdue = $rental->is_overdue ? now()->diffInDays($dueDate) : 0;
                $rental->late_fee = $rental->days_overdue * 1.50; // $1.50 por día de retraso
                return $rental;
            });

        // Cargar historial de rentals devueltos (últimos 20)
        $rentalHistory = $customer->rentals()
            ->with(['inventory.film.category', 'inventory.store', 'staff'])
            ->whereNotNull('return_date')
            ->orderBy('return_date', 'desc')
            ->take(20)
            ->get()
            ->map(function($rental) {
                // Calcular días que tuvo la película
                $rental->rental_days = $rental->rental_date->diffInDays($rental->return_date);
                $dueDate = $rental->rental_date->addDays(7);
                $rental->due_date = $dueDate;
                $rental->was_late = $rental->return_date->isAfter($dueDate);
                $rental->days_late = $rental->was_late ? $dueDate->diffInDays($rental->return_date) : 0;
                $rental->late_fee_paid = $rental->days_late * 1.50;
                return $rental;
            });

        // Calcular estadísticas del cliente
        $stats = [
            'total_rentals' => $customer->rentals()->count(),
            'active_rentals' => $activeRentals->count(),
            'overdue_rentals' => $activeRentals->where('is_overdue', true)->count(),
            'total_late_fees' => $activeRentals->sum('late_fee'),
            'is_blocked' => $customer->shouldBeBlocked(),
            'lifetime_late_fees' => $rentalHistory->sum('late_fee_paid') + $activeRentals->sum('late_fee')
        ];

        return view('customers.show', compact('customer', 'activeRentals', 'rentalHistory', 'stats'));
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

    /**
     * Display blocked customers management interface
     */
    public function blocked(Request $request): View
    {
        // Solo admins pueden acceder a esta funcionalidad
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Solo administradores pueden gestionar bloqueos de clientes.');
        }

        // Obtener todos los clientes con rentas activas
        $customers = Customer::with(['rentals' => function($query) {
            $query->whereNull('return_date')->with('inventory.film');
        }])
        ->whereHas('rentals', function($query) {
            $query->whereNull('return_date');
        })
        ->get()
        ->filter(function($customer) {
            return $customer->shouldBeBlocked();
        })
        ->map(function($customer) {
            $overdueRentals = $customer->overdueRentals;
            return [
                'customer' => $customer,
                'overdue_count' => count($overdueRentals),
                'total_late_fees' => $customer->getTotalLateFees(),
                'overdue_rentals' => $overdueRentals
            ];
        })
        ->sortByDesc('total_late_fees');

        return view('customers.blocked', compact('customers'));
    }

    /**
     * Force unblock a customer (admin only)
     */
    public function forceUnblock(Customer $customer, Request $request): RedirectResponse
    {
        // Solo admins pueden forzar desbloqueos
        if (!auth()->user()->isAdmin()) {
            return redirect()->back()->with('error', 'Solo administradores pueden desbloquear clientes.');
        }

        $request->validate([
            'reason' => 'required|string|max:255'
        ]);

        // Marcar todas las rentas vencidas como devueltas con override administrativo
        $overdueRentals = $customer->rentals()->whereNull('return_date')->get()->filter(function($rental) {
            $dueDate = $rental->rental_date->addDays(7);
            return now()->isAfter($dueDate);
        });

        foreach ($overdueRentals as $rental) {
            $rental->update([
                'return_date' => now()
            ]);
        }

        // Log de la acción administrativa (simplificado)
        \Log::info("Cliente desbloqueado por admin", [
            'customer_id' => $customer->customer_id,
            'customer_name' => $customer->full_name,
            'admin_user' => auth()->user()->email,
            'reason' => $request->reason,
            'rentals_returned' => count($overdueRentals),
            'timestamp' => now()
        ]);

        return redirect()->back()->with('success', 
            "Cliente {$customer->full_name} desbloqueado exitosamente. " . 
            count($overdueRentals) . " rentas fueron marcadas como devueltas por override administrativo."
        );
    }

    /**
     * Extend rental period for a customer (admin only)
     */
    public function extendRental(Customer $customer, Request $request): RedirectResponse
    {
        // Solo admins pueden extender rentas
        if (!auth()->user()->isAdmin()) {
            return redirect()->back()->with('error', 'Solo administradores pueden extender rentas.');
        }

        $request->validate([
            'rental_id' => 'required|exists:rental,rental_id',
            'extension_days' => 'required|integer|min:1|max:30',
            'reason' => 'required|string|max:255'
        ]);

        $rental = \App\Models\Rental::find($request->rental_id);
        
        if ($rental->customer_id !== $customer->customer_id) {
            return redirect()->back()->with('error', 'La renta no pertenece a este cliente.');
        }

        if ($rental->return_date) {
            return redirect()->back()->with('error', 'Esta renta ya ha sido devuelta.');
        }

        // Por simplicidad, ajustamos la fecha de renta para simular extensión
        $rental->update([
            'rental_date' => $rental->rental_date->addDays($request->extension_days)
        ]);

        // Log de la extensión
        \Log::info("Renta extendida por admin", [
            'rental_id' => $rental->rental_id,
            'customer_name' => $customer->full_name,
            'film_title' => $rental->inventory->film->title,
            'extension_days' => $request->extension_days,
            'admin_user' => auth()->user()->email,
            'reason' => $request->reason,
            'timestamp' => now()
        ]);

        return redirect()->back()->with('success', 
            "Renta de '{$rental->inventory->film->title}' extendida por {$request->extension_days} días."
        );
    }
}
