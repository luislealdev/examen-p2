<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class Staff extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'staff';

    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'staff_id';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'address_id',
        'picture',
        'email',
        'store_id',
        'active',
        'username',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     */
    protected $hidden = [
        'password',
        'picture', // Hide binary data by default
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'active' => 'boolean',
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
     * Automatically hash the password when set.
     */
    public function setPasswordAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['password'] = Hash::make($value);
        }
    }

    /**
     * Scope a query to only include active staff.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }

    /**
     * Scope a query to only include inactive staff.
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('active', false);
    }

    /**
     * Get the full name attribute.
     */
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Get the home store of this staff member.
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'store_id', 'store_id');
    }

    /**
     * Get the stores this staff member manages.
     */
    public function managedStores(): HasMany
    {
        return $this->hasMany(Store::class, 'manager_staff_id', 'staff_id');
    }

    /**
     * Get the address of this staff member.
     */
    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'address_id', 'address_id');
    }

    /**
     * Get the corresponding user for authentication.
     */
    public function user()
    {
        return User::where('email', $this->email ?: $this->username . '@sakila.local')->first();
    }

    /**
     * Get the role from the corresponding user.
     */
    public function getRoleAttribute()
    {
        $user = $this->user();
        return $user ? $user->role : 'employee';
    }

    /**
     * Check if this staff member is a manager.
     */
    public function getIsManagerAttribute(): bool
    {
        return $this->managedStores()->count() > 0;
    }

    /**
     * Get initials for display.
     */
    public function getInitialsAttribute(): string
    {
        return strtoupper(substr($this->first_name, 0, 1) . substr($this->last_name, 0, 1));
    }

    /**
     * Get picture as base64 data URL for display.
     */
    public function getPictureUrlAttribute(): ?string
    {
        if (!$this->picture) {
            return null;
        }
        
        return 'data:image/jpeg;base64,' . base64_encode($this->picture);
    }

    /**
     * Check if staff has a picture.
     */
    public function getHasPictureAttribute(): bool
    {
        return !empty($this->picture);
    }
}
