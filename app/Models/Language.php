<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Language extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'language';

    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'language_id';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'code',
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
     * Scope a query to search by language name.
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where('name', 'like', "%{$search}%");
    }

    /**
     * Scope a query to order by name alphabetically.
     */
    public function scopeAlphabetical(Builder $query): Builder
    {
        return $query->orderBy('name');
    }

    /**
     * Get the language name in title case.
     */
    public function getFormattedNameAttribute(): string
    {
        return ucwords(strtolower($this->name));
    }

    /**
     * Get the first letter of the language name for grouping.
     */
    public function getFirstLetterAttribute(): string
    {
        return strtoupper(substr($this->name, 0, 1));
    }

    /**
     * Get films that use this language.
     */
    public function films()
    {
        return $this->hasMany(Film::class, 'language_id', 'language_id');
    }

    /**
     * Get films that use this language as original language.
     * TODO: Uncomment when Film model is created
     */
    // public function originalLanguageFilms(): HasMany
    // {
    //     return $this->hasMany(Film::class, 'original_language_id', 'language_id');
    // }

    /**
     * Get count of films using this language.
     * This is a placeholder until Film model is created.
     */
    public function getFilmsCountAttribute(): int
    {
        // TODO: Replace with actual count when Film model exists
        // return $this->films()->count();
        return 0;
    }

    /**
     * Check if this language is used by any films.
     */
    public function getIsUsedAttribute(): bool
    {
        // TODO: Replace with actual check when Film model exists
        // return $this->films()->exists();
        return false;
    }
}
