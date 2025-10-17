<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class InventoryMovement extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'inventory_id',
        'rental_id',
        'movement_type',
        'condition_from',
        'condition_to',
        'user_id',
        'staff_id',
        'customer_id',
        'notes',
        'metadata',
        'movement_date',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'metadata' => 'array',
        'movement_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Movement types constants
     */
    const MOVEMENT_TYPES = [
        'rental' => 'Renta',
        'return' => 'Devolución',
        'damage' => 'Daño',
        'loss' => 'Pérdida',
        'repair' => 'Reparación',
        'restock' => 'Reposición',
    ];

    /**
     * Condition constants
     */
    const CONDITIONS = [
        'available' => 'Disponible',
        'damaged' => 'Dañada',
        'lost' => 'Perdida',
    ];

    /**
     * Get the inventory item
     */
    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'inventory_id', 'inventory_id');
    }

    /**
     * Get the rental associated with this movement
     */
    public function rental()
    {
        return $this->belongsTo(Rental::class, 'rental_id', 'rental_id');
    }

    /**
     * Get the user who performed the movement
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the staff member who performed the movement
     */
    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id', 'staff_id');
    }

    /**
     * Get the customer involved in the movement
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    /**
     * Scope: Filter by movement type
     */
    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('movement_type', $type);
    }

    /**
     * Scope: Filter by condition
     */
    public function scopeByCondition(Builder $query, string $condition): Builder
    {
        return $query->where('condition_to', $condition);
    }

    /**
     * Scope: Recent movements
     */
    public function scopeRecent(Builder $query, int $days = 30): Builder
    {
        return $query->where('movement_date', '>=', now()->subDays($days));
    }

    /**
     * Scope: Today's movements
     */
    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('movement_date', today());
    }

    /**
     * Get movement type label
     */
    public function getMovementTypeLabel(): string
    {
        return self::MOVEMENT_TYPES[$this->movement_type] ?? ucfirst($this->movement_type);
    }

    /**
     * Get condition labels
     */
    public function getConditionFromLabel(): string
    {
        return $this->condition_from ? (self::CONDITIONS[$this->condition_from] ?? ucfirst($this->condition_from)) : 'N/A';
    }

    public function getConditionToLabel(): string
    {
        return self::CONDITIONS[$this->condition_to] ?? ucfirst($this->condition_to);
    }
}
