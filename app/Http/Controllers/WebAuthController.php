<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Customer;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class WebAuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'name' => 'required|string|max:255|min:3',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/[A-Z]/',      // al menos una mayúscula
                'regex:/[0-9]/',      // al menos un número
            ],
            'address' => 'required|string|max:100|min:5',
            'city' => 'nullable|string|max:50',
            'postal_code' => 'nullable|string|max:20',
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9\-\+\s\(\)]+$/'],
        ], [
            'name.required' => 'El nombre es obligatorio',
            'name.min' => 'El nombre debe tener al menos 3 caracteres',
            'email.required' => 'El correo electrónico es obligatorio',
            'email.email' => 'Por favor ingrese un correo electrónico válido',
            'email.unique' => 'Este correo electrónico ya está registrado',
            'password.required' => 'La contraseña es obligatoria',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres',
            'password.regex' => 'La contraseña debe contener al menos una mayúscula y un número',
            'password.confirmed' => 'Las contraseñas no coinciden',
            'address.required' => 'La dirección es obligatoria',
            'address.min' => 'La dirección debe tener al menos 5 caracteres',
            'phone.regex' => 'El número de teléfono solo puede contener números, espacios y los símbolos + - ( )',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // create address
        $address = Address::create([
            'address' => $data['address'],
            'address2' => $data['address2'] ?? null,
            'city' => $data['city'] ?? null,
            'postal_code' => $data['postal_code'] ?? null,
            'phone' => $data['phone'] ?? null,
        ]);

        // create user
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // create customer linked to sakila customers
        $customer = Customer::create([
            'store_id' => 1,
            'first_name' => explode(' ', $data['name'])[0] ?? $data['name'],
            'last_name' => collect(explode(' ', $data['name']))->slice(1)->join(' ') ?: ' ',
            'email' => $data['email'],
            'address_id' => $address->address_id,
            'active' => true,
        ]);

        Auth::login($user);

        return redirect()->route('films.index')->with('success', 'Registro completado.');
    }

    public function editProfile()
    {
        $user = Auth::user();
        $customer = Customer::where('email', $user->email)->first();
        
        if ($customer && $customer->address) {
            $user->address = $customer->address->address;
            $user->city = $customer->address->city;
            $user->phone = $customer->address->phone;
        }

        return view('profile.edit', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|min:3',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'current_password' => 'nullable|required_with:password|string',
            'password' => 'nullable|string|min:8|confirmed|regex:/[A-Z]/|regex:/[0-9]/',
            'address' => 'required|string|max:100|min:5',
            'city' => 'nullable|string|max:50',
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9\-\+\s\(\)]+$/'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Verificar contraseña actual si se quiere cambiar
        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()
                    ->withErrors(['current_password' => 'La contraseña actual no es correcta'])
                    ->withInput();
            }
        }

        // Actualizar usuario
        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        // Actualizar customer y address en Sakila
        $customer = Customer::where('email', $user->email)->first();
        if ($customer) {
            $customer->email = $request->email;
            $customer->first_name = explode(' ', $request->name)[0] ?? $request->name;
            $customer->last_name = collect(explode(' ', $request->name))->slice(1)->join(' ') ?: ' ';
            $customer->save();

            if ($customer->address) {
                $customer->address->address = $request->address;
                $customer->address->city = $request->city;
                $customer->address->phone = $request->phone;
                $customer->address->save();
            }
        }

        return redirect()->route('profile.edit')
            ->with('success', 'Perfil actualizado correctamente.');
    }

    public function showStaffLogin()
    {
        return view('auth.staff-login');
    }

    public function staffLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Verificar si existe un empleado con ese email
        $staff = \App\Models\Staff::where('email', $request->email)->first();
        
        if (!$staff) {
            return back()
                ->withErrors(['email' => 'No se encontró una cuenta de empleado con este correo.'])
                ->withInput();
        }

        // Intentar login
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            // Asignar rol de empleado si no lo tiene
            if (!$user->isEmployee()) {
                $user->role = 'employee';
                $user->save();
            }

            $request->session()->regenerate();
            return redirect()->intended(route('films.index'));
        }

        return back()
            ->withErrors(['email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.'])
            ->withInput();
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended(route('films.index'));
        }

        return back()->withErrors(['email' => 'Credenciales inválidas'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
