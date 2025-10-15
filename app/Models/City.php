<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'city';

    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'city_id';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'city',
        'country_id',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'last_update' => 'datetime',
        'country_id' => 'integer',
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
     * Get the country that owns the city.
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id', 'country_id');
    }

    /**
     * Get the addresses for the city.
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class, 'city_id', 'city_id');
    }
}
