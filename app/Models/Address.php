<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $table = 'addresses';
    protected $primaryKey = 'address_id';
    public $timestamps = false;

    protected $fillable = [
        'address', 'address2', 'district', 'city_id', 'postal_code', 'phone'
    ];

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id', 'city_id');
    }
}
