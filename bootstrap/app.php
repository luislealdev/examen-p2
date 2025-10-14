<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\UpdateLastLogin;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Register Passport middlewares
        $middleware->alias([
            'scope' => \Laravel\Passport\Http\Middleware\CheckTokenForAnyScope::class,
            'scopes' => \App\Http\Middleware\CheckScopes::class,
            'role' => \App\Http\Middleware\CheckRole::class, 
        ]);
        
        // Agregar middleware para actualizar último login
        $middleware->web(append: [
            UpdateLastLogin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();