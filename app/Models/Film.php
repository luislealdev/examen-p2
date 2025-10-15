<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Film extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'film';

    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'film_id';

    /**
     * Indicates if the model should be timestamped.
     * We handle timestamps manually with last_update field.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'title',
        'description',
        'release_year',
        'language_id',
        'original_language_id',
        'rental_duration',
        'rental_rate',
        'length',
        'replacement_cost',
        'rating',
        'special_features',
        'category_id',
        // Nuevos campos OMDB
        'imdb_id',
        'poster_url',
        'imdb_rating',
        'imdb_votes',
        'metascore',
        'plot',
        'awards',
        'box_office',
        'production',
        'website',
        'writers',
        'countries',
        'imported_from_omdb',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'release_year' => 'integer',
        'rental_duration' => 'integer',
        'rental_rate' => 'decimal:2',
        'length' => 'integer',
        'replacement_cost' => 'decimal:2',
        'special_features' => 'array',
        'last_update' => 'datetime',
        // Nuevos campos OMDB
        'imdb_rating' => 'decimal:1',
        'imdb_votes' => 'integer',
        'metascore' => 'integer',
        'writers' => 'array',
        'countries' => 'array',
        'imported_from_omdb' => 'boolean',
    ];

    /**
     * Available film ratings.
     */
    public const RATINGS = ['G', 'PG', 'PG-13', 'R', 'NC-17'];

    /**
     * Available special features.
     */
    public const SPECIAL_FEATURES = [
        'Trailers',
        'Commentaries',
        'Deleted Scenes',
        'Behind the Scenes'
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
     * Relationships
     */

    /**
     * Get the language of the film.
     */
    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'language_id', 'language_id');
    }

    /**
     * Get the original language of the film.
     */
    public function originalLanguage(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'original_language_id', 'language_id');
    }

    /**
     * Get the category for the film.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    /**
     * Get the actors of the film.
     */
    public function actors(): BelongsToMany
    {
        return $this->belongsToMany(Actor::class, 'film_actors', 'film_id', 'actor_id')
                    ->withPivot(['character_name', 'order'])
                    ->withTimestamps()
                    ->orderBy('film_actors.order');
    }

    /**
     * Get the directors of the film.
     */
    public function directors(): BelongsToMany
    {
        return $this->belongsToMany(Director::class, 'film_directors', 'film_id', 'director_id')
                    ->withPivot(['role'])
                    ->withTimestamps();
    }

    /**
     * Query Scopes
     */

    /**
     * Scope a query to search for films by title or description.
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'LIKE', "%{$search}%")
              ->orWhere('description', 'LIKE', "%{$search}%");
        });
    }

    /**
     * Scope a query to filter by rating.
     */
    public function scopeByRating(Builder $query, string $rating): Builder
    {
        return $query->where('rating', $rating);
    }

    /**
     * Scope a query to filter by release year.
     */
    public function scopeByYear(Builder $query, int $year): Builder
    {
        return $query->where('release_year', $year);
    }

    /**
     * Scope a query to filter by language.
     */
    public function scopeByLanguage(Builder $query, int $languageId): Builder
    {
        return $query->where('language_id', $languageId);
    }

    /**
     * Scope a query to filter by category.
     */
    public function scopeByCategory(Builder $query, int $categoryId): Builder
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope a query to filter by rental rate range.
     */
    public function scopeByRentalRate(Builder $query, ?float $min = null, ?float $max = null): Builder
    {
        if ($min !== null) {
            $query->where('rental_rate', '>=', $min);
        }
        if ($max !== null) {
            $query->where('rental_rate', '<=', $max);
        }
        return $query;
    }

    /**
     * Scope a query to filter by length range.
     */
    public function scopeByLength(Builder $query, ?int $min = null, ?int $max = null): Builder
    {
        if ($min !== null) {
            $query->where('length', '>=', $min);
        }
        if ($max !== null) {
            $query->where('length', '<=', $max);
        }
        return $query;
    }

    /**
     * Scope a query to get recent films.
     */
    public function scopeRecent(Builder $query, int $years = 10): Builder
    {
        return $query->where('release_year', '>=', now()->year - $years);
    }

    /**
     * Scope a query to get films with special features.
     */
    public function scopeWithSpecialFeatures(Builder $query): Builder
    {
        return $query->whereNotNull('special_features');
    }

    /**
     * Scope a query to order by newest release year.
     */
    public function scopeNewest(Builder $query): Builder
    {
        return $query->orderBy('release_year', 'desc');
    }

    /**
     * Scope a query to order by title alphabetically.
     */
    public function scopeAlphabetical(Builder $query): Builder
    {
        return $query->orderBy('title', 'asc');
    }

    /**
     * Accessors & Mutators
     */

    /**
     * Get the formatted title.
     */
    public function getFormattedTitleAttribute(): string
    {
        return Str::title($this->title);
    }

    /**
     * Get the film's slug.
     */
    public function getSlugAttribute(): string
    {
        return Str::slug($this->title);
    }

    /**
     * Get the duration in hours and minutes format.
     */
    public function getDurationFormatAttribute(): string
    {
        if (!$this->length) {
            return 'N/A';
        }
        
        $hours = intval($this->length / 60);
        $minutes = $this->length % 60;
        
        if ($hours > 0) {
            return "{$hours}h {$minutes}m";
        }
        
        return "{$minutes}m";
    }

    /**
     * Get the rating badge color.
     */
    public function getRatingColorAttribute(): string
    {
        return match($this->rating) {
            'G' => 'success',
            'PG' => 'info',
            'PG-13' => 'warning',
            'R' => 'danger',
            'NC-17' => 'dark',
            default => 'secondary'
        };
    }

    /**
     * Check if film is recent (released in last 10 years).
     */
    public function getIsRecentAttribute(): bool
    {
        return $this->release_year && $this->release_year >= (now()->year - 10);
    }

    /**
     * Get the film's decade.
     */
    public function getDecadeAttribute(): string
    {
        if (!$this->release_year) {
            return 'Unknown';
        }
        
        $decade = floor($this->release_year / 10) * 10;
        return $decade . 's';
    }

    /**
     * Get special features as formatted string.
     */
    public function getSpecialFeaturesFormattedAttribute(): string
    {
        if (!$this->special_features || empty($this->special_features)) {
            return 'None';
        }
        
        return implode(', ', $this->special_features);
    }

    /**
     * Get short description (truncated).
     */
    public function getShortDescriptionAttribute(): string
    {
        if (!$this->description) {
            return 'No description available.';
        }
        
        return Str::limit($this->description, 100);
    }

    /**
     * Check if film has special features.
     */
    public function getHasSpecialFeaturesAttribute(): bool
    {
        return !empty($this->special_features);
    }

    /**
     * Get the rental period formatted.
     */
    public function getRentalPeriodAttribute(): string
    {
        $days = $this->rental_duration;
        return $days === 1 ? '1 day' : "{$days} days";
    }

    /**
     * Get film age category.
     */
    public function getAgeCategoryAttribute(): string
    {
        if (!$this->release_year) {
            return 'Unknown';
        }
        
        $age = now()->year - $this->release_year;
        
        if ($age < 5) return 'New Release';
        if ($age < 15) return 'Recent';
        if ($age < 30) return 'Classic';
        
        return 'Vintage';
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'film_id';
    }

    /**
     * Convert model to string representation.
     */
    public function __toString(): string
    {
        return $this->title;
    }
}
