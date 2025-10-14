<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Jerarquía de roles - un rol superior puede acceder a todo lo de los inferiores
     */
    protected $roleHierarchy = [
        'admin' => 3,      // Nivel más alto
        'employee' => 2,   // Nivel medio
        'customer' => 1,   // Nivel base
    ];

    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        $userRole = $request->user()->role;
        $userLevel = $this->roleHierarchy[$userRole] ?? 0;

        // Verificar si el usuario tiene permiso basado en la jerarquía
        $hasPermission = false;
        
        foreach ($roles as $role) {
            $requiredLevel = $this->roleHierarchy[$role] ?? 999;
            
            // Si el nivel del usuario es igual o superior al requerido, tiene permiso
            if ($userLevel >= $requiredLevel) {
                $hasPermission = true;
                break;
            }
        }

        if (!$hasPermission) {
            abort(403, 'No tienes permiso para acceder a esta página.');
        }

        return $next($request);
    }
}