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
        $query = Staff::with('store');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
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

        // Filter by store
        if ($request->filled('store_id')) {
            $query->where('store_id', $request->store_id);
        }

        // Sort functionality
        $sortBy = $request->get('sort', 'last_update');
        $sortDirection = $request->get('direction', 'desc');
        
        if (in_array($sortBy, ['first_name', 'last_name', 'email', 'username', 'active', 'last_update', 'store_id'])) {
            $query->orderBy($sortBy, $sortDirection);
        }

        // Pagination with request parameters preserved
        $staff = $query->paginate(15)->withQueryString();

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
        $staff->load(['store', 'managedStores']);
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
}
