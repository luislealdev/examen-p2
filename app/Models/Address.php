<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $table = 'addresses';
    protected $primaryKey = 'address_id';
    public $timestamps = false;

    protected $fillable = [
        'address', 'address2', 'district', 'city', 'postal_code', 'phone'
    ];
}
