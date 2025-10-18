<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Customer extends Model
{
    use HasFactory;

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
     */
    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'address_id', 'address_id');
    }

    /**
     * Get all rentals for this customer.
     */
    public function rentals(): HasMany
    {
        return $this->hasMany(Rental::class, 'customer_id', 'customer_id');
    }

    /**
     * Get active rentals (not returned yet).
     */
    public function activeRentals(): HasMany
    {
        return $this->rentals()->whereNull('return_date');
    }

    /**
     * Get returned rentals.
     */
    public function returnedRentals(): HasMany
    {
        return $this->rentals()->whereNotNull('return_date');
    }

    /**
     * Check if customer has overdue rentals.
     */
    public function hasOverdueRentals(): bool
    {
        return $this->activeRentals()
            ->where('rental_date', '<', now()->subDays(7)) // 7 days rental period
            ->exists();
    }

    /**
     * Get overdue rentals with calculated late fees.
     */
    public function getOverdueRentalsAttribute()
    {
        return $this->activeRentals()
            ->with(['inventory.film'])
            ->where('rental_date', '<', now()->subDays(7))
            ->get()
            ->map(function($rental) {
                $rental->days_overdue = now()->diffInDays($rental->rental_date->addDays(7));
                $rental->late_fee = $rental->days_overdue * 1.50; // $1.50 per day late fee
                return $rental;
            });
    }

    /**
     * Calculate total late fees for this customer.
     */
    public function getTotalLateFees(): float
    {
        return $this->overdue_rentals->sum('late_fee');
    }

    /**
     * Check if customer should be blocked (has overdue rentals).
     */
    public function shouldBeBlocked(): bool
    {
        return $this->hasOverdueRentals();
    }
}
