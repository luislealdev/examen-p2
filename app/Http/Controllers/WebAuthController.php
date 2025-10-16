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
            
            // Registrar evento de login en auditoría
            \DB::table('audit_logs')->insert([
                'user_id' => $user->id,
                'action' => 'login',
                'resource' => null,
                'method' => 'POST',
                'url' => $request->fullUrl(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'request_data' => json_encode(['email' => $user->email]),
                'response_code' => 200,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Redirect based on user role
            switch ($user->role) {
                case User::ROLE_ADMIN:
                    return redirect()->intended(route('admin.dashboard'))
                        ->with('success', "¡Bienvenido de vuelta, {$user->name}!");
                case User::ROLE_EMPLOYEE:
                    return redirect()->intended(route('films.index'))
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
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users',
                'unique:customers,email'  // También validar que el email no exista en customers
            ],
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Crear el usuario
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => User::ROLE_CLIENT, // Por defecto, todos los registros son clientes
        ]);

        // Separar el nombre en first_name y last_name
        $nameParts = explode(' ', trim($request->name), 2);
        $firstName = $nameParts[0];
        $lastName = isset($nameParts[1]) ? $nameParts[1] : '';

        // Obtener datos por defecto
        $defaultStore = \App\Models\Store::first();
        if (!$defaultStore) {
            throw new \Exception('No hay tiendas disponibles en el sistema');
        }

        $defaultAddress = \DB::table('address')->first();
        if (!$defaultAddress) {
            throw new \Exception('No hay direcciones disponibles en el sistema');
        }

        // Crear registros según el rol
        if ($user->role === User::ROLE_CLIENT) {
            // Crear el customer asociado
            \App\Models\Customer::create([
                'store_id' => $defaultStore->store_id,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $request->email,
                'address_id' => $defaultAddress->address_id,
                'active' => true,
            ]);
        } elseif (in_array($user->role, [User::ROLE_EMPLOYEE, User::ROLE_ADMIN])) {
            // Crear el staff asociado
            \App\Models\Staff::create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'address_id' => $defaultAddress->address_id,
                'email' => $request->email,
                'store_id' => $defaultStore->store_id,
                'active' => true,
                'username' => explode('@', $request->email)[0], // Usar parte del email como username
                'password' => Hash::make($request->password),
            ]);
        }

        Auth::login($user);

        return redirect()->route('films.index')
            ->with('success', '¡Cuenta creada exitosamente! Bienvenido a Sakila Movies.');
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        
        // Registrar evento de logout en auditoría
        if ($user) {
            \DB::table('audit_logs')->insert([
                'user_id' => $user->id,
                'action' => 'logout',
                'resource' => null,
                'method' => 'POST',
                'url' => $request->fullUrl(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'request_data' => json_encode(['email' => $user->email]),
                'response_code' => 200,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
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
