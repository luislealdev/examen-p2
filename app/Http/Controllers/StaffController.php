<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class StaffController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        // Obtener todos los staff de la tabla staff
        $staffFromTable = Staff::with('store')
            ->get()
            ->map(function ($staff) {
                // Buscar el usuario correspondiente para obtener su rol
                $user = User::where('email', $staff->email)->first();
                $userRole = $user ? $user->role : 'employee'; // Default employee si no encuentra user
                
                return [
                    'id' => $staff->staff_id,
                    'source' => 'staff_table',
                    'first_name' => $staff->first_name,
                    'last_name' => $staff->last_name,
                    'full_name' => $staff->first_name . ' ' . $staff->last_name,
                    'email' => $staff->email,
                    'username' => $staff->username,
                    'active' => $staff->active,
                    'store_id' => $staff->store_id,
                    'store_name' => $staff->store ? 'Tienda #' . $staff->store->store_id : 'N/A',
                    'role' => $userRole,
                    'last_update' => $staff->last_update,
                    'created_at' => $staff->last_update,
                ];
            });

        // Obtener empleados y admins de la tabla users que NO estén en staff
        $existingStaffEmails = $staffFromTable->pluck('email')->toArray();
        
        $staffFromUsers = User::whereIn('role', [User::ROLE_EMPLOYEE, User::ROLE_ADMIN])
            ->whereNotIn('email', $existingStaffEmails)
            ->get()
            ->map(function ($user) {
                $nameParts = explode(' ', trim($user->name), 2);
                $firstName = $nameParts[0];
                $lastName = isset($nameParts[1]) ? $nameParts[1] : '';
                
                return [
                    'id' => 'user_' . $user->id,
                    'source' => 'users_table',
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'full_name' => $user->name,
                    'email' => $user->email,
                    'username' => explode('@', $user->email)[0],
                    'active' => true, // Los usuarios siempre están activos
                    'store_id' => null,
                    'store_name' => 'Sin asignar',
                    'role' => $user->role,
                    'last_update' => $user->updated_at,
                    'created_at' => $user->created_at,
                ];
            });

        // Combinar ambas colecciones
        $allStaff = $staffFromTable->concat($staffFromUsers);

        // Aplicar filtros de búsqueda
        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $allStaff = $allStaff->filter(function ($staff) use ($search) {
                return stripos($staff['first_name'], $search) !== false ||
                       stripos($staff['last_name'], $search) !== false ||
                       stripos($staff['full_name'], $search) !== false ||
                       stripos($staff['email'], $search) !== false ||
                       stripos($staff['username'], $search) !== false;
            });
        }

        // Filtro por estado
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $allStaff = $allStaff->where('active', true);
            } elseif ($request->status === 'inactive') {
                $allStaff = $allStaff->where('active', false);
            }
        }

        // Filtro por tienda
        if ($request->filled('store_id')) {
            $allStaff = $allStaff->where('store_id', $request->store_id);
        }

        // Ordenamiento
        $sortBy = $request->get('sort', 'last_update');
        $sortDirection = $request->get('direction', 'desc');
        
        if (in_array($sortBy, ['first_name', 'last_name', 'email', 'username', 'active', 'last_update', 'store_id'])) {
            $allStaff = $allStaff->sortBy($sortBy, SORT_REGULAR, $sortDirection === 'desc');
        }

        // Paginación manual
        $perPage = 15;
        $currentPage = request()->get('page', 1);
        $total = $allStaff->count();
        $items = $allStaff->forPage($currentPage, $perPage)->values();
        
        $staff = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $currentPage,
            [
                'path' => request()->url(),
                'pageName' => 'page',
            ]
        );
        
        $staff->withQueryString();

        // Get stores for filter dropdown
        $stores = Store::orderBy('store_id')->get();

        return view('staff.index', compact('staff', 'stores'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $stores = Store::all();
        return view('staff.create', compact('stores'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:45',
            'last_name' => 'required|string|max:45',
            'address_id' => 'required|integer|min:1',
            'picture' => 'nullable|image|max:2048', // 2MB max for image
            'email' => 'nullable|email|max:50',
            'store_id' => 'required|integer|exists:stores,store_id',
            'active' => 'boolean',
            'username' => 'required|string|max:16|unique:staff,username',
            'password' => 'required|string|min:6|max:255',
            'role' => 'required|in:employee,admin',
        ]);

        $validated['active'] = $request->boolean('active', true);

        DB::transaction(function () use ($validated) {
            // 1. Crear usuario en la tabla users para autenticación
            $user = User::create([
                'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                'email' => $validated['email'] ?: $validated['username'] . '@sakila.local',
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
            ]);

            // 2. Crear staff en la tabla staff
            $staffData = $validated;
            // Handle picture upload
            if (request()->hasFile('picture')) {
                $staffData['picture'] = file_get_contents(request()->file('picture')->getRealPath());
            }
            
            Staff::create($staffData);
        });

        return redirect()->route('staff.index')
            ->with('success', 'Personal creado exitosamente con acceso al sistema!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Staff $staff): View
    {
        $staff->load([
            'store.address.city.country',
            'store.manager',
            'managedStores.address.city.country'
        ]);
        
        return view('staff.show', compact('staff'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Staff $staff): View
    {
        $stores = Store::all();
        return view('staff.edit', compact('staff', 'stores'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Staff $staff): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:45',
            'last_name' => 'required|string|max:45',
            'address_id' => 'required|integer|min:1',
            'picture' => 'nullable|image|max:2048',
            'email' => 'nullable|email|max:50',
            'store_id' => 'required|integer|exists:stores,store_id',
            'active' => 'boolean',
            'username' => [
                'required',
                'string',
                'max:16',
                Rule::unique('staff', 'username')->ignore($staff->staff_id, 'staff_id')
            ],
            'password' => 'nullable|string|min:6|max:255',
            'role' => 'required|in:employee,admin',
        ]);

        $validated['active'] = $request->boolean('active', true);

        DB::transaction(function () use ($validated, $staff) {
            // Update staff record
            $staffData = $validated;
            if (request()->hasFile('picture')) {
                $staffData['picture'] = file_get_contents(request()->file('picture')->getRealPath());
            }
            
            // Only update password if provided
            if (empty($staffData['password'])) {
                unset($staffData['password']);
            }

            $staff->update($staffData);

            // Update corresponding user record
            $user = User::where('email', $staff->email ?: $staff->username . '@sakila.local')->first();
            if ($user) {
                $userData = [
                    'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                    'email' => $validated['email'] ?: $validated['username'] . '@sakila.local',
                    'role' => $validated['role'],
                ];
                
                if (!empty($validated['password'])) {
                    $userData['password'] = Hash::make($validated['password']);
                }
                
                $user->update($userData);
            }
        });

        return redirect()->route('staff.index')
            ->with('success', 'Personal actualizado exitosamente!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Staff $staff): RedirectResponse
    {
        DB::transaction(function () use ($staff) {
            // Delete corresponding user record
            $user = User::where('email', $staff->email ?: $staff->username . '@sakila.local')->first();
            if ($user) {
                // Prevent deletion of the last admin
                if ($user->role === 'admin' && User::where('role', 'admin')->count() <= 1) {
                    throw new \Exception('No se puede eliminar el último administrador.');
                }
                $user->delete();
            }
            
            // Instead of deleting, we mark as inactive (soft delete approach)
            $staff->update(['active' => false]);
        });

        return redirect()->route('staff.index')
            ->with('success', 'Personal desactivado exitosamente!');
    }

    /**
     * Display staff member's picture.
     */
    public function picture(Staff $staff)
    {
        if (!$staff->picture) {
            abort(404);
        }

        return response($staff->picture)
            ->header('Content-Type', 'image/jpeg');
    }

    /**
     * Sincronizar empleados de la tabla users que no están en staff
     */
    public function syncFromUsers(): RedirectResponse
    {
        // Obtener empleados y admins que no están en staff
        $existingStaffEmails = Staff::pluck('email')->toArray();
        
        $usersToSync = User::whereIn('role', [User::ROLE_EMPLOYEE, User::ROLE_ADMIN])
            ->whereNotIn('email', $existingStaffEmails)
            ->get();

        if ($usersToSync->isEmpty()) {
            return redirect()->route('staff.index')
                ->with('info', 'No hay empleados por sincronizar.');
        }

        $synced = 0;
        $defaultStore = Store::first();
        $defaultAddress = \DB::table('address')->first();

        foreach ($usersToSync as $user) {
            $nameParts = explode(' ', trim($user->name), 2);
            $firstName = $nameParts[0];
            $lastName = isset($nameParts[1]) ? $nameParts[1] : '';

            Staff::create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'address_id' => $defaultAddress->address_id,
                'email' => $user->email,
                'store_id' => $defaultStore->store_id,
                'active' => true,
                'username' => explode('@', $user->email)[0],
                'password' => '$2y$10$dummy', // Password dummy, usarán el de users
            ]);

            $synced++;
        }

        return redirect()->route('staff.index')
            ->with('success', "Se sincronizaron {$synced} empleados exitosamente.");
    }
}
