<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table = 'countries';
    protected $primaryKey = 'country_id';
    public $timestamps = false;

    protected $fillable = [
        'country'
    ];

    public function cities()
    {
        return $this->hasMany(City::class, 'country_id', 'country_id');
    }
}