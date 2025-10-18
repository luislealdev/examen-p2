<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'category';

    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'category_id';

    /**
     * Indicates if the model should be timestamped.
     * We handle timestamps manually with last_update field.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'last_update' => 'datetime',
    ];

    /**
     * Boot the model and set up automatic timestamp updating.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->last_update = now();
        });

        static::updating(function ($model) {
            $model->last_update = now();
        });
    }

    /**
     * Scope a query to search for categories by name.
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where('name', 'LIKE', "%{$search}%");
    }

    /**
     * Scope a query to order categories alphabetically.
     */
    public function scopeAlphabetical(Builder $query): Builder
    {
        return $query->orderBy('name', 'asc');
    }

    /**
     * Scope a query to get categories by first letter.
     */
    public function scopeByFirstLetter(Builder $query, string $letter): Builder
    {
        return $query->where('name', 'LIKE', $letter . '%');
    }

    /**
     * Scope a query to get recently updated categories.
     */
    public function scopeRecentlyUpdated(Builder $query, int $days = 30): Builder
    {
        return $query->where('last_update', '>=', now()->subDays($days));
    }

    /**
     * Get the formatted category name.
     */
    public function getFormattedNameAttribute(): string
    {
        return Str::title($this->name);
    }

    /**
     * Get the first letter of the category name.
     */
    public function getFirstLetterAttribute(): string
    {
        return strtoupper(substr($this->name, 0, 1));
    }

    /**
     * Get the category name in uppercase.
     */
    public function getUpperNameAttribute(): string
    {
        return strtoupper($this->name);
    }

    /**
     * Get the category name in slug format.
     */
    public function getSlugAttribute(): string
    {
        return Str::slug($this->name);
    }

    /**
     * Check if category was updated recently.
     */
    public function getIsRecentAttribute(): bool
    {
        return $this->last_update && $this->last_update->isAfter(now()->subDays(30));
    }

    /**
     * Get short description for display.
     */
    public function getShortDescriptionAttribute(): string
    {
        return "Category: {$this->formatted_name}";
    }

    /**
     * Relationships - Film Category (Many-to-Many when Film model exists)
     * Note: Uncomment when Film model is implemented
     */
    // public function films()
    // {
    //     return $this->belongsToMany(Film::class, 'film_category', 'category_id', 'film_id');
    // }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'category_id';
    }

    /**
     * Convert model to string representation.
     */
    public function __toString(): string
    {
        return $this->name;
    }
}
