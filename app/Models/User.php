<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'username',
        'staff_id',
        'store_id',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Check if user is an employee
     */
    public function isEmployee(): bool
    {
        return $this->role === 'employee' || $this->role === 'admin';
    }

    /**
     * Check if user is a customer
     */
    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    /**
     * Check if user is an admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Get the staff record associated with the user
     */
    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id', 'staff_id');
    }

    /**
     * Get the store associated with the user
     */
    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id', 'store_id');
    }

    /**
     * Scope a query to filter by user's store
     * Admins can see everything, employees only their store
     */
    public function scopeForUserStore($query)
    {
        if ($this->isAdmin()) {
            return $query; // Admin ve todo
        }
        
        return $query->where('store_id', $this->store_id);
    }
}
