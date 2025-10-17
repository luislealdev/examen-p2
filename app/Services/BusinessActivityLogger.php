<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BusinessActivityLogger
{
    /**
     * Registrar actividad de renta
     */
    public static function logRental($action, $rentalId, $customerId, $filmId, $staffId, $details = [])
    {
        self::logActivity([
            'category' => 'rental',
            'action' => $action, // 'create', 'return', 'overdue_notice', 'late_fee'
            'entity_type' => 'rental',
            'entity_id' => $rentalId,
            'user_id' => auth()->id(),
            'staff_id' => $staffId,
            'customer_id' => $customerId,
            'film_id' => $filmId,
            'details' => array_merge([
                'rental_id' => $rentalId,
                'customer_id' => $customerId,
                'film_id' => $filmId,
                'staff_id' => $staffId,
            ], $details),
            'severity' => self::getRentalSeverity($action),
        ]);
    }

    /**
     * Registrar actividad de inventario
     */
    public static function logInventory($action, $inventoryId, $filmId, $storeId, $details = [])
    {
        self::logActivity([
            'category' => 'inventory',
            'action' => $action, // 'add', 'remove', 'transfer', 'update_status'
            'entity_type' => 'inventory',
            'entity_id' => $inventoryId,
            'user_id' => auth()->id(),
            'film_id' => $filmId,
            'store_id' => $storeId,
            'details' => array_merge([
                'inventory_id' => $inventoryId,
                'film_id' => $filmId,
                'store_id' => $storeId,
            ], $details),
            'severity' => self::getInventorySeverity($action),
        ]);
    }

    /**
     * Registrar actividad de acceso/seguridad
     */
    public static function logAccess($action, $details = [])
    {
        self::logActivity([
            'category' => 'access',
            'action' => $action, // 'login', 'logout', 'failed_login', 'password_reset', 'role_change'
            'entity_type' => 'user',
            'entity_id' => auth()->id(),
            'user_id' => auth()->id(),
            'details' => $details,
            'severity' => self::getAccessSeverity($action),
        ]);
    }

    /**
     * Registrar actividad de customer
     */
    public static function logCustomer($action, $customerId, $details = [])
    {
        self::logActivity([
            'category' => 'customer',
            'action' => $action, // 'create', 'update', 'activate', 'deactivate', 'delete'
            'entity_type' => 'customer',
            'entity_id' => $customerId,
            'user_id' => auth()->id(),
            'customer_id' => $customerId,
            'details' => array_merge([
                'customer_id' => $customerId,
            ], $details),
            'severity' => self::getCustomerSeverity($action),
        ]);
    }

    /**
     * Registrar actividad de films
     */
    public static function logFilm($action, $filmId, $details = [])
    {
        self::logActivity([
            'category' => 'film',
            'action' => $action, // 'create', 'update', 'delete', 'view'
            'entity_type' => 'film',
            'entity_id' => $filmId,
            'user_id' => auth()->id(),
            'film_id' => $filmId,
            'details' => array_merge([
                'film_id' => $filmId,
            ], $details),
            'severity' => self::getFilmSeverity($action),
        ]);
    }

    /**
     * Registrar actividad de staff
     */
    public static function logStaff($action, $staffId, $details = [])
    {
        self::logActivity([
            'category' => 'staff',
            'action' => $action, // 'create', 'update', 'activate', 'deactivate', 'role_change'
            'entity_type' => 'staff',
            'entity_id' => $staffId,
            'user_id' => auth()->id(),
            'staff_id' => $staffId,
            'details' => array_merge([
                'staff_id' => $staffId,
            ], $details),
            'severity' => self::getStaffSeverity($action),
        ]);
    }

    /**
     * Método principal para registrar actividad
     */
    private static function logActivity($data)
    {
        try {
            DB::table('business_activity_logs')->insert([
                'category' => $data['category'],
                'action' => $data['action'],
                'entity_type' => $data['entity_type'],
                'entity_id' => $data['entity_id'],
                'user_id' => $data['user_id'],
                'staff_id' => $data['staff_id'] ?? null,
                'customer_id' => $data['customer_id'] ?? null,
                'film_id' => $data['film_id'] ?? null,
                'store_id' => $data['store_id'] ?? null,
                'details' => json_encode($data['details']),
                'severity' => $data['severity'],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error logging business activity', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
        }
    }

    // Métodos para determinar severidad
    private static function getRentalSeverity($action)
    {
        return match($action) {
            'create' => 'info',
            'return' => 'info',
            'overdue_notice' => 'warning',
            'late_fee' => 'warning',
            default => 'info'
        };
    }

    private static function getInventorySeverity($action)
    {
        return match($action) {
            'add', 'update_status' => 'info',
            'remove', 'transfer' => 'warning',
            default => 'info'
        };
    }

    private static function getAccessSeverity($action)
    {
        return match($action) {
            'login', 'logout' => 'info',
            'failed_login' => 'warning',
            'password_reset', 'role_change' => 'high',
            default => 'info'
        };
    }

    private static function getCustomerSeverity($action)
    {
        return match($action) {
            'create', 'update' => 'info',
            'activate', 'deactivate' => 'warning',
            'delete' => 'high',
            default => 'info'
        };
    }

    private static function getFilmSeverity($action)
    {
        return match($action) {
            'view' => 'info',
            'create', 'update' => 'info',
            'delete' => 'high',
            default => 'info'
        };
    }

    private static function getStaffSeverity($action)
    {
        return match($action) {
            'create', 'update' => 'info',
            'activate', 'deactivate', 'role_change' => 'high',
            default => 'info'
        };
    }
}