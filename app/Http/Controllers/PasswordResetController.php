<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PasswordResetController extends Controller
{
    /**
     * Mostrar formulario para solicitar restablecimiento de contraseña
     */
    public function showRequestForm(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Enviar enlace de restablecimiento de contraseña
     */
    public function sendResetLinkEmail(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Debe ser un correo electrónico válido.',
            'email.exists' => 'No encontramos una cuenta con este correo electrónico.',
        ]);

        // Buscar el usuario
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'No encontramos una cuenta con este correo electrónico.']);
        }

        // Generar token único
        $token = Str::random(64);

        // Eliminar tokens previos para este email
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        // Insertar nuevo token
        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => Hash::make($token),
            'created_at' => Carbon::now()
        ]);

        // Enviar email
        try {
            Mail::send('emails.password-reset', [
                'token' => $token,
                'email' => $request->email,
                'user' => $user,
                'url' => route('password.reset', ['token' => $token, 'email' => $request->email])
            ], function ($message) use ($request) {
                $message->to($request->email);
                $message->subject('Restablecer Contraseña - ' . config('app.name'));
            });

            return back()->with('status', 'Hemos enviado por correo electrónico su enlace de restablecimiento de contraseña.');
        } catch (\Exception $e) {
            // Log del error
            Log::error('Error enviando email de recuperación: ' . $e->getMessage());
            
            return back()->withErrors(['email' => 'Hubo un problema enviando el correo. Inténtelo de nuevo.']);
        }
    }

    /**
     * Mostrar formulario de restablecimiento de contraseña
     */
    public function showResetForm(Request $request, $token): View
    {
        $email = $request->query('email');
        
        // Verificar que el token existe y es válido
        $tokenRecord = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (!$tokenRecord || !Hash::check($token, $tokenRecord->token)) {
            abort(404, 'Token de restablecimiento inválido');
        }

        // Verificar que el token no haya expirado (24 horas)
        $createdAt = Carbon::parse($tokenRecord->created_at);
        if ($createdAt->addHours(24)->isPast()) {
            // Eliminar token expirado
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            abort(404, 'El enlace de restablecimiento ha expirado');
        }

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $email
        ]);
    }

    /**
     * Restablecer la contraseña
     */
    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:8|confirmed',
        ], [
            'token.required' => 'Token requerido.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Debe ser un correo electrónico válido.',
            'email.exists' => 'No encontramos una cuenta con este correo electrónico.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
        ]);

        // Verificar token
        $tokenRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$tokenRecord || !Hash::check($request->token, $tokenRecord->token)) {
            return back()->withErrors(['token' => 'Token de restablecimiento inválido']);
        }

        // Verificar que el token no haya expirado (24 horas)
        $createdAt = Carbon::parse($tokenRecord->created_at);
        if ($createdAt->addHours(24)->isPast()) {
            // Eliminar token expirado
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withErrors(['token' => 'El enlace de restablecimiento ha expirado']);
        }

        // Actualizar contraseña del usuario
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Eliminar el token usado
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('status', 'Su contraseña ha sido restablecida correctamente. Puede iniciar sesión con su nueva contraseña.');
    }
}
