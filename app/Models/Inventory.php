<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Inventory extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'inventory';

    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'inventory_id';

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'film_id',
        'store_id',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'last_update' => 'datetime',
        'inventory_id' => 'integer',
        'film_id' => 'integer',
        'store_id' => 'integer',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function ($inventory) {
            $inventory->last_update = now();
        });

        static::updating(function ($inventory) {
            $inventory->last_update = now();
        });
    }

    // ===== RELATIONSHIPS =====

    /**
     * Get the film that owns the inventory item.
     */
    public function film(): BelongsTo
    {
        return $this->belongsTo(Film::class, 'film_id', 'film_id');
    }

    /**
     * Get the store that owns the inventory item.
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'store_id', 'store_id');
    }

    // ===== SCOPES =====

    /**
     * Scope a query to search by film title or store information.
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->whereHas('film', function (Builder $filmQuery) use ($search) {
            $filmQuery->where('title', 'like', "%{$search}%")
                     ->orWhere('description', 'like', "%{$search}%");
        })->orWhereHas('store', function (Builder $storeQuery) use ($search) {
            $storeQuery->where('store_id', 'like', "%{$search}%");
        });
    }

    /**
     * Scope a query to filter by film.
     */
    public function scopeByFilm(Builder $query, int $filmId): Builder
    {
        return $query->where('film_id', $filmId);
    }

    /**
     * Scope a query to filter by store.
     */
    public function scopeByStore(Builder $query, int $storeId): Builder
    {
        return $query->where('store_id', $storeId);
    }

    /**
     * Scope a query to filter by film rating.
     */
    public function scopeByFilmRating(Builder $query, string $rating): Builder
    {
        return $query->whereHas('film', function (Builder $filmQuery) use ($rating) {
            $filmQuery->where('rating', $rating);
        });
    }

    /**
     * Scope a query to filter by film category.
     */
    public function scopeByFilmCategory(Builder $query, int $categoryId): Builder
    {
        return $query->whereHas('film.categories', function (Builder $categoryQuery) use ($categoryId) {
            $categoryQuery->where('category_id', $categoryId);
        });
    }

    /**
     * Scope a query to filter by film language.
     */
    public function scopeByFilmLanguage(Builder $query, int $languageId): Builder
    {
        return $query->whereHas('film', function (Builder $filmQuery) use ($languageId) {
            $filmQuery->where('language_id', $languageId);
        });
    }

    /**
     * Scope a query to get recent inventory items.
     */
    public function scopeRecent(Builder $query, int $days = 30): Builder
    {
        return $query->where('last_update', '>=', now()->subDays($days));
    }

    /**
     * Scope a query to order alphabetically by film title.
     */
    public function scopeAlphabetical(Builder $query): Builder
    {
        return $query->join('film', 'inventory.film_id', '=', 'film.film_id')
                    ->orderBy('film.title', 'asc')
                    ->select('inventory.*');
    }

    /**
     * Scope a query to order by newest first.
     */
    public function scopeNewest(Builder $query): Builder
    {
        return $query->orderBy('last_update', 'desc');
    }

    /**
     * Scope a query to order by oldest first.
     */
    public function scopeOldest(Builder $query): Builder
    {
        return $query->orderBy('last_update', 'asc');
    }

    /**
     * Scope a query to get inventory items with high rental rates.
     */
    public function scopeHighValue(Builder $query, float $minRate = 4.00): Builder
    {
        return $query->whereHas('film', function (Builder $filmQuery) use ($minRate) {
            $filmQuery->where('rental_rate', '>=', $minRate);
        });
    }

    /**
     * Scope a query to get available inventory (not currently rented).
     * Note: This would need rental table implementation for full functionality.
     */
    public function scopeAvailable(Builder $query): Builder
    {
        // For now, we'll just return all inventory
        // This would be enhanced when rental table is implemented
        return $query;
    }

    // ===== ACCESSORS =====

    /**
     * Get the formatted last update date.
     */
    public function getLastUpdateFormatAttribute(): string
    {
        return $this->last_update ? $this->last_update->format('M d, Y \a\t g:i A') : 'N/A';
    }

    /**
     * Get the human-readable time since last update.
     */
    public function getLastUpdateHumanAttribute(): string
    {
        return $this->last_update ? $this->last_update->diffForHumans() : 'N/A';
    }

    /**
     * Get the film title safely.
     */
    public function getFilmTitleAttribute(): string
    {
        return $this->film->title ?? 'Unknown Film';
    }

    /**
     * Get the store location safely.
     */
    public function getStoreLocationAttribute(): string
    {
        return $this->store ? "Store #{$this->store->store_id}" : 'Unknown Store';
    }

    /**
     * Get the rental rate from the film.
     */
    public function getRentalRateAttribute(): float
    {
        return $this->film->rental_rate ?? 0.00;
    }

    /**
     * Get the film rating with color class.
     */
    public function getFilmRatingAttribute(): string
    {
        return $this->film->rating ?? 'N/A';
    }

    /**
     * Get the status of the inventory item.
     */
    public function getStatusAttribute(): string
    {
        // This would be enhanced with rental table integration
        return 'Available';
    }

    /**
     * Get the status color class for display.
     */
    public function getStatusColorAttribute(): string
    {
        // This would be enhanced with rental table integration
        return 'success'; // Available = green
    }

    // ===== STATIC METHODS =====

    /**
     * Get inventory statistics.
     */
    public static function getStatistics(): array
    {
        return [
            'total_items' => self::count(),
            'by_store' => self::selectRaw('store_id, COUNT(*) as count')
                            ->groupBy('store_id')
                            ->with('store')
                            ->get()
                            ->mapWithKeys(fn($item) => [
                                $item->store->store_id ?? 'Unknown' => $item->count
                            ]),
            'by_rating' => self::join('film', 'inventory.film_id', '=', 'film.film_id')
                             ->selectRaw('film.rating, COUNT(*) as count')
                             ->groupBy('film.rating')
                             ->get()
                             ->mapWithKeys(fn($item) => [$item->rating => $item->count]),
            'recent_additions' => self::recent(7)->count(),
            'avg_rental_rate' => self::join('film', 'inventory.film_id', '=', 'film.film_id')
                                   ->avg('film.rental_rate'),
            'high_value_items' => self::highValue()->count(),
        ];
    }

    /**
     * Get available copy count for a specific film.
     */
    public static function getAvailableCount(int $filmId): int
    {
        return self::where('film_id', $filmId)->available()->count();
    }

    /**
     * Get inventory summary by store.
     */
    public static function getStoreInventorySummary(): array
    {
        return self::selectRaw('store_id, COUNT(*) as total_items, 
                               COUNT(DISTINCT film_id) as unique_films')
                  ->groupBy('store_id')
                  ->with('store')
                  ->get()
                  ->mapWithKeys(fn($item) => [
                      $item->store_id => [
                          'store' => $item->store,
                          'total_items' => $item->total_items,
                          'unique_films' => $item->unique_films
                      ]
                  ])
                  ->toArray();
    }
}
