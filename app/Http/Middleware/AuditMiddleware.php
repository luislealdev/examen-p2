<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class AuditMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Solo auditar si el usuario está autenticado
        if (auth()->check()) {
            $this->logRequest($request, $response);
        }

        return $response;
    }

    /**
     * Registrar la petición en los logs de auditoría
     */
    private function logRequest(Request $request, Response $response): void
    {
        try {
            // Determinar la acción basada en la ruta y método
            $action = $this->determineAction($request);
            $resource = $this->determineResource($request);

            // Preparar datos de la petición (solo datos importantes)
            $requestData = $this->prepareRequestData($request);

            // Insertar directamente en la base de datos
            DB::table('audit_logs')->insert([
                'user_id' => auth()->id(),
                'action' => $action,
                'resource' => $resource,
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'request_data' => json_encode($requestData),
                'response_code' => $response->getStatusCode(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            // Silenciar errores de auditoría para no interrumpir la aplicación
            \Log::error('Error en auditoría: ' . $e->getMessage());
        }
    }

    /**
     * Determinar la acción basada en la petición
     */
    private function determineAction(Request $request): string
    {
        $method = $request->method();
        $route = $request->route();
        
        if (!$route) {
            return 'access_page';
        }

        $routeName = $route->getName();
        
        // Mapear nombres de rutas a acciones específicas
        if (str_contains($routeName, 'login')) {
            return 'login';
        }
        
        if (str_contains($routeName, 'logout')) {
            return 'logout';
        }

        // Mapear por método HTTP
        return match($method) {
            'GET' => str_contains($routeName, '.edit') || str_contains($routeName, '.create') ? 'access_form' : 'view',
            'POST' => 'create',
            'PUT', 'PATCH' => 'update',
            'DELETE' => 'delete',
            default => 'access_page'
        };
    }

    /**
     * Determinar el recurso basado en la URL
     */
    private function determineResource(Request $request): ?string
    {
        $path = $request->path();
        
        // Extraer el recurso principal de la URL
        $segments = explode('/', $path);
        
        $resources = [
            'customers', 'inventories', 'films', 'categories', 
            'languages', 'stores', 'staff', 'users', 'admin'
        ];
        
        foreach ($segments as $segment) {
            if (in_array($segment, $resources)) {
                return $segment;
            }
        }
        
        return $segments[0] ?? null;
    }

    /**
     * Preparar datos de la petición para auditoría
     */
    private function prepareRequestData(Request $request): array
    {
        $data = [];
        
        // Solo incluir ciertos campos importantes
        if ($request->isMethod('POST') || $request->isMethod('PUT') || $request->isMethod('PATCH')) {
            $sensitiveFields = ['password', 'password_confirmation', '_token'];
            
            foreach ($request->all() as $key => $value) {
                if (!in_array($key, $sensitiveFields)) {
                    $data[$key] = is_string($value) ? substr($value, 0, 255) : $value;
                }
            }
        }
        
        return $data;
    }
}
