<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class WebAuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    /**
     * Show registration form
     */
    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.register');
    }

    /**
     * Handle login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            
            // Redirect based on user role
            switch ($user->role) {
                case User::ROLE_ADMIN:
                case User::ROLE_EMPLOYEE:
                    return redirect()->intended(route('admin.dashboard'))
                        ->with('success', "¡Bienvenido de vuelta, {$user->name}!");
                case User::ROLE_CLIENT:
                    return redirect()->intended(route('films.index'))
                        ->with('success', "¡Bienvenido, {$user->name}!");
                default:
                    return redirect()->route('films.index')
                        ->with('success', "¡Bienvenido, {$user->name}!");
            }
        }

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    /**
     * Handle registration
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', Rule::in([User::ROLE_CLIENT])], // Solo clientes pueden registrarse
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => User::ROLE_CLIENT, // Por defecto, todos los registros son clientes
        ]);

        Auth::login($user);

        return redirect()->route('films.index')
            ->with('success', '¡Cuenta creada exitosamente! Bienvenido a Sakila Movies.');
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('films.index')
            ->with('success', '¡Has cerrado sesión exitosamente!');
    }

    /**
     * Dashboard redirect based on role
     */
    public function dashboard()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        switch ($user->role) {
            case User::ROLE_ADMIN:
            case User::ROLE_EMPLOYEE:
                return redirect()->route('admin.dashboard');
            case User::ROLE_CLIENT:
                return redirect()->route('films.index');
            default:
                return redirect()->route('films.index');
        }
    }
}
