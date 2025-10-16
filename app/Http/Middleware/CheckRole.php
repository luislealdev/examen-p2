<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para acceder a esta página.');
        }

        $user = Auth::user();
        
        // Si solo hay un parámetro y contiene comas, dividirlo
        if (count($roles) === 1 && str_contains($roles[0], ',')) {
            $allowedRoles = explode(',', $roles[0]);
        } else {
            $allowedRoles = $roles;
        }

        // DEBUG: Log para ver qué está recibiendo el middleware
        \Log::info('CheckRole Debug', [
            'roles_received' => $roles,
            'user_role' => $user->role,
            'allowed_roles' => $allowedRoles,
            'in_array_result' => in_array($user->role, $allowedRoles),
            'request_path' => $request->path()
        ]);

        if (!in_array($user->role, $allowedRoles)) {
            abort(403, 'No tienes permisos para acceder a esta página.');
        }

        return $next($request);
    }
}
