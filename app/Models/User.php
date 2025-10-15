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
    ];

    /**
     * User roles constants
     */
    public const ROLE_CLIENT = 'client';
    public const ROLE_EMPLOYEE = 'employee';
    public const ROLE_ADMIN = 'admin';

    public const ROLES = [
        self::ROLE_CLIENT,
        self::ROLE_EMPLOYEE,
        self::ROLE_ADMIN,
    ];

    /**
     * Check if user is an admin
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Check if user is an employee
     */
    public function isEmployee(): bool
    {
        return $this->role === self::ROLE_EMPLOYEE;
    }

    /**
     * Check if user is a client
     */
    public function isClient(): bool
    {
        return $this->role === self::ROLE_CLIENT;
    }

    /**
     * Check if user has admin or employee privileges
     */
    public function isStaff(): bool
    {
        return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_EMPLOYEE]);
    }

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
            'password' => 'hashed',
        ];
    }
}
