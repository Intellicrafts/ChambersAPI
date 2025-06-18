<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'city',
        'state',
        'country',
        'zip_code',
        'active',
        'is_verified',
        'avatar',
        'user_type',
        'deleted'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
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
            'active' => 'boolean',
            'is_verified' => 'boolean',
            'deleted' => 'boolean',
            'user_type' => 'integer',
        ];
    }

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Get user's appointments
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Get user's reviews
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get user's legal queries
     */
    public function legalQueries(): HasMany
    {
        return $this->hasMany(LegalQuery::class);
    }

    /**
     * Scope for active users
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope for verified users
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Get full address
     */
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->country,
            $this->zip_code
        ]);
        
        return implode(', ', $parts);
    }

    // Add full_name accessor (optional)
    public function getFullNameAttribute(): string
    {
        return $this->name;
    }

    /**
     * Get avatar URL
     */
    public function getAvatarUrlAttribute(): string
    {
        return asset('storage/avatars/' . $this->avatar);
    }

    /**
     * User type constants
     */
    const USER_TYPE_CLIENT = 1;
    const USER_TYPE_LAWYER = 2;
    const USER_TYPE_ADMIN = 3;

    /**
     * Check if user is client
     */
    public function isClient(): bool
    {
        return $this->user_type === self::USER_TYPE_CLIENT;
    }

    /**
     * Check if user is lawyer
     */
    public function isLawyer(): bool
    {
        return $this->user_type === self::USER_TYPE_LAWYER;
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->user_type === self::USER_TYPE_ADMIN;
    }


}