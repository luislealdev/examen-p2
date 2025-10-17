<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
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
            // Información personal
            'first_name' => 'required|string|max:45',
            'last_name' => 'required|string|max:45',
            'email' => [
                'required',
                'string',
                'email',
                'max:50',
                'unique:users',
                'unique:customers,email'  // También validar que el email no exista en customers
            ],
            'password' => 'required|string|min:8|confirmed',
            
            // Información de dirección
            'address_line1' => 'required|string|max:50',
            'address_line2' => 'nullable|string|max:50',
            'district' => 'required|string|max:20',
            'postal_code' => 'required|string|max:10',
            'phone' => 'nullable|string|max:20',
            'country_id' => 'required|exists:country,country_id',
            'city_id' => 'required|exists:city,city_id',
        ], [
            // Mensajes personalizados
            'first_name.required' => 'El nombre es obligatorio.',
            'last_name.required' => 'El apellido es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'address_line1.required' => 'La dirección principal es obligatoria.',
            'district.required' => 'El distrito/provincia es obligatorio.',
            'postal_code.required' => 'El código postal es obligatorio.',
            'country_id.required' => 'Debe seleccionar un país.',
            'country_id.exists' => 'El país seleccionado no es válido.',
            'city_id.required' => 'Debe seleccionar una ciudad.',
            'city_id.exists' => 'La ciudad seleccionada no es válida.',
        ]);

        // Usar transacción para asegurar consistencia
        \DB::beginTransaction();
        
        try {
            // 1. Crear la dirección primero
            $address = \DB::table('address')->insertGetId([
                'address' => $request->address_line1,
                'address2' => $request->address_line2,
                'district' => $request->district,
                'city_id' => $request->city_id,
                'postal_code' => $request->postal_code,
                'phone' => $request->phone,
                'last_update' => now(),
            ]);

            // 2. Crear el usuario
            $user = User::create([
                'name' => trim($request->first_name . ' ' . $request->last_name),
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => User::ROLE_CLIENT, // Por defecto, todos los registros son clientes
            ]);

            // 3. Obtener tienda por defecto (la primera disponible)
            $defaultStore = \App\Models\Store::first();
            if (!$defaultStore) {
                throw new \Exception('No hay tiendas disponibles en el sistema');
            }

            // 4. Crear el customer asociado
            \App\Models\Customer::create([
                'store_id' => $defaultStore->store_id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'address_id' => $address,
                'active' => true,
                'create_date' => now(),
                'last_update' => now(),
            ]);

            // 5. Registrar evento de registro en auditoría
            \DB::table('audit_logs')->insert([
                'user_id' => $user->id,
                'action' => 'register',
                'resource' => 'user',
                'method' => 'POST',
                'url' => $request->fullUrl(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'request_data' => json_encode([
                    'email' => $user->email,
                    'name' => $user->name,
                    'role' => $user->role,
                ]),
                'response_code' => 201,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            \DB::commit();

            // Login automático después del registro
            Auth::login($user);

            return redirect()->route('films.index')
                ->with('success', "¡Cuenta creada exitosamente! Bienvenido, {$user->name}.");
                
        } catch (\Exception $e) {
            \DB::rollback();
            
            // Log del error
            Log::error('Error en registro de usuario: ' . $e->getMessage(), [
                'email' => $request->email,
                'error' => $e->getTraceAsString()
            ]);
            
            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors(['general' => 'Hubo un error al crear la cuenta. Por favor, inténtelo de nuevo.']);
        }
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
