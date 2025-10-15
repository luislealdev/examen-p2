<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rental extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'rental';

    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'rental_id';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'rental_date',
        'inventory_id',
        'customer_id',
        'return_date',
        'staff_id',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'rental_date' => 'datetime',
        'return_date' => 'datetime',
        'last_update' => 'datetime',
    ];

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The name of the "updated at" column.
     */
    const UPDATED_AT = 'last_update';

    /**
     * Get the inventory item that was rented.
     */
    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class, 'inventory_id', 'inventory_id');
    }

    /**
     * Get the customer who made the rental.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    /**
     * Get the staff member who processed the rental.
     */
    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staff_id', 'staff_id');
    }

    /**
     * Check if the rental is currently active (not returned).
     */
    public function isActive(): bool
    {
        return is_null($this->return_date);
    }

    /**
     * Check if the rental has been returned.
     */
    public function isReturned(): bool
    {
        return !is_null($this->return_date);
    }

    /**
     * Get the film through the inventory relationship.
     */
    public function film()
    {
        return $this->inventory->film ?? null;
    }
}
