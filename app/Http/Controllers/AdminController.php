<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Film;
use App\Models\Store;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    /**
     * Show admin dashboard
     */
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_films' => Film::count(),
            'total_stores' => Store::count(),
            'total_customers' => Customer::count(),
            'recent_users' => User::latest()->take(5)->get(),
            'recent_films' => Film::latest()->take(5)->get(),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    /**
     * Show users management
     */
    public function users()
    {
        $users = User::latest()->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show create user form
     */
    public function createUser()
    {
        return view('admin.users.create');
    }

    /**
     * Store new user (employee or admin)
     */
    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', Rule::in([User::ROLE_EMPLOYEE, User::ROLE_ADMIN])],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        $roleText = $request->role === User::ROLE_ADMIN ? 'administrador' : 'empleado';

        return redirect()->route('admin.users')
            ->with('success', "Usuario {$roleText} '{$user->name}' creado exitosamente.");
    }

    /**
     * Show edit user form
     */
    public function editUser(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update user
     */
    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', Rule::in(User::ROLES)],
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users')
            ->with('success', "Usuario '{$user->name}' actualizado exitosamente.");
    }

    /**
     * Delete user
     */
    public function deleteUser(User $user)
    {
        // Prevent deleting the last admin
        if ($user->isAdmin() && User::where('role', User::ROLE_ADMIN)->count() <= 1) {
            return back()->with('error', 'No puedes eliminar el último administrador del sistema.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.users')
            ->with('success', "Usuario '{$name}' eliminado exitosamente.");
    }
}
