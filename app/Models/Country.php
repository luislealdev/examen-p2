<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'country';

    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'country_id';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'country',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
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
     * Get the cities for the country.
     */
    public function cities(): HasMany
    {
        return $this->hasMany(City::class, 'country_id', 'country_id');
    }
}
