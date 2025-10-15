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
     */
    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'address_id', 'address_id');
    }
}
