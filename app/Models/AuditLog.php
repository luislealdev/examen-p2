<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'resource',
        'method',
        'url',
        'ip_address',
        'user_agent',
        'request_data',
        'response_code',
    ];

    protected $casts = [
        'request_data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación con el usuario
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scopes para filtrar logs
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    public function scopeByResource($query, $resource)
    {
        return $query->where('resource', $resource);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeLastDays($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Método estático para registrar un log fácilmente
     */
    public static function logAccess($action, $resource = null, $requestData = null, $responseCode = 200)
    {
        $request = request();
        
        return self::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'resource' => $resource,
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'request_data' => $requestData,
            'response_code' => $responseCode,
        ]);
    }

    /**
     * Atributo para mostrar el nombre del usuario o "Usuario eliminado"
     */
    public function getUserNameAttribute()
    {
        return $this->user ? $this->user->name : 'Usuario eliminado';
    }

    /**
     * Atributo para mostrar el email del usuario o "N/A"
     */
    public function getUserEmailAttribute()
    {
        return $this->user ? $this->user->email : 'N/A';
    }

    /**
     * Atributo para mostrar una descripción amigable de la acción
     */
    public function getActionDescriptionAttribute()
    {
        $descriptions = [
            'login' => 'Inicio de sesión',
            'logout' => 'Cierre de sesión',
            'access_page' => 'Acceso a página',
            'create' => 'Crear registro',
            'update' => 'Actualizar registro',
            'delete' => 'Eliminar registro',
            'view' => 'Ver registro',
        ];

        return $descriptions[$this->action] ?? ucfirst($this->action);
    }
}

