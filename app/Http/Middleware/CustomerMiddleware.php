<?php

namespace App\Http\Middleware;

use Closure;

class CustomerMiddleware
{
    public function handle($request, Closure $next)
    {
        if ($request->user() && !$request->user()->isEmployee()) {
            return $next($request);
        }

        return redirect()->route('films.index')->with('error', 'Acceso no autorizado.');
    }
}