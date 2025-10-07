<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Store extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'stores';

    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'store_id';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'manager_staff_id',
        'address_id',
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
     * Get the manager staff that manages this store.
     * TODO: Uncomment when Staff model is created
     */
    // public function manager(): BelongsTo
    // {
    //     return $this->belongsTo(Staff::class, 'manager_staff_id', 'staff_id');
    // }

    /**
     * Get the address of this store.
     * TODO: Uncomment when Address model is created
     */
    // public function address(): BelongsTo
    // {
    //     return $this->belongsTo(Address::class, 'address_id', 'address_id');
    // }
}
