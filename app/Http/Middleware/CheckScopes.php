<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Passport\Exceptions\MissingScopeException;
use Symfony\Component\HttpFoundation\Response;

class CheckScopes
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$scopes): Response
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated.'
            ], 401);
        }

        $token = $user->token();
        
        if (!$token) {
            return response()->json([
                'message' => 'Invalid token.'
            ], 401);
        }

        foreach ($scopes as $scope) {
            if (!$token->can($scope)) {
                return response()->json([
                    'message' => 'Insufficient scope. Required: ' . $scope
                ], 403);
            }
        }

        return $next($request);
    }
}
