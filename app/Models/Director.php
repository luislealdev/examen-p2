<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Director extends Model
{
    protected $primaryKey = 'director_id';

    protected $fillable = [
        'first_name',
        'last_name',
        'biography',
        'birth_date',
        'birth_place',
        'imdb_id',
        'photo_url'
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    protected $appends = [
        'full_name'
    ];

    /**
     * Accessor para el nombre completo
     */
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Relación con películas
     */
    public function films(): BelongsToMany
    {
        return $this->belongsToMany(Film::class, 'film_directors', 'director_id', 'film_id')
                    ->withPivot(['role'])
                    ->withTimestamps();
    }

    /**
     * Scope para buscar por nombre
     */
    public function scopeSearchByName($query, $name)
    {
        return $query->where(function ($q) use ($name) {
            $q->where('first_name', 'like', "%{$name}%")
              ->orWhere('last_name', 'like', "%{$name}%")
              ->orWhereRaw("CONCAT(first_name, ' ', last_name) like ?", ["%{$name}%"]);
        });
    }

    /**
     * Scope para buscar por IMDb ID
     */
    public function scopeByImdbId($query, $imdbId)
    {
        return $query->where('imdb_id', $imdbId);
    }
}
