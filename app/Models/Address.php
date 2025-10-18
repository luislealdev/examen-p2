<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Address extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'address';

    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'address_id';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'address',
        'address2',
        'district',
        'city_id',
        'postal_code',
        'phone',
        'location',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'last_update' => 'datetime',
        'city_id' => 'integer',
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
     * Get the city that owns the address.
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id', 'city_id');
    }

    /**
     * Get the customers for the address.
     */
    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class, 'address_id', 'address_id');
    }

    /**
     * Get the staff for the address.
     */
    public function staff(): HasMany
    {
        return $this->hasMany(Staff::class, 'address_id', 'address_id');
    }

    /**
     * Get the stores for the address.
     */
    public function stores(): HasMany
    {
        return $this->hasMany(Store::class, 'address_id', 'address_id');
    }

    /**
     * Get the full address as a string.
     */
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address,
            $this->address2,
            $this->district,
            $this->city->city ?? null,
            $this->city->country->country ?? null,
            $this->postal_code,
        ]);

        return implode(', ', $parts);
    }

    /**
     * Parse location coordinates from string format.
     */
    public function getCoordinatesAttribute(): ?array
    {
        if (!$this->location) {
            return null;
        }

        $coords = explode(',', $this->location);
        if (count($coords) === 2) {
            return [
                'lat' => (float) trim($coords[0]),
                'lng' => (float) trim($coords[1]),
            ];
        }

        return null;
    }

    /**
     * Set location coordinates from array.
     */
    public function setCoordinatesAttribute(array $coordinates): void
    {
        if (isset($coordinates['lat']) && isset($coordinates['lng'])) {
            $this->location = $coordinates['lat'] . ',' . $coordinates['lng'];
        }
    }
}
