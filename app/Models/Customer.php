<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Customer extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'customers';

    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'customer_id';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'store_id',
        'first_name',
        'last_name',
        'email',
        'address_id',
        'active',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'active' => 'boolean',
        'create_date' => 'datetime',
        'last_update' => 'datetime',
    ];

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The name of the "created at" column.
     */
    const CREATED_AT = 'create_date';

    /**
     * The name of the "updated at" column.
     */
    const UPDATED_AT = 'last_update';

    /**
     * Scope a query to only include active customers.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }

    /**
     * Scope a query to only include inactive customers.
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('active', false);
    }

    /**
     * Get the full name attribute.
     */
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Get the store where this customer generally shops.
     * TODO: Uncomment when Store model relationship is needed
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'store_id', 'store_id');
    }

    /**
     * Get the address of this customer.
     * TODO: Uncomment when Address model is created
     */
    // public function address(): BelongsTo
    // {
    //     return $this->belongsTo(Address::class, 'address_id', 'address_id');
    // }

    /**
     * Get all rentals for this customer
     */
    public function rentals()
    {
        return $this->hasMany(Rental::class, 'customer_id', 'customer_id');
    }

    /**
     * Get active rentals for this customer
     */
    public function activeRentals()
    {
        return $this->rentals()->where('status', 'active');
    }

    /**
     * Get overdue rentals for this customer
     */
    public function overdueRentals()
    {
        return $this->rentals()->where('status', 'overdue');
    }

    /**
     * Check if customer has outstanding late fees
     */
    public function hasOutstandingFees(): bool
    {
        return $this->rentals()
            ->where('late_fee_applied', true)
            ->where('late_fee', '>', 0)
            ->where('status', '!=', 'returned')
            ->exists();
    }

    /**
     * Get total outstanding late fees
     */
    public function getTotalOutstandingFees()
    {
        return $this->rentals()
            ->where('late_fee_applied', true)
            ->where('status', '!=', 'returned')
            ->sum('late_fee');
    }

    /**
     * Check if customer can rent (no outstanding fees)
     */
    public function canRent(): bool
    {
        return $this->active && !$this->hasOutstandingFees();
    }
}
