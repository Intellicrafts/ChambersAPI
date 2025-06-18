<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lawyer extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'email',
        'phone_number',
        'password_hash',
        'active',
        'is_verified',
        'license_number',
        'bar_association',
        'specialization',
        'years_of_experience',
        'bio',
        'profile_picture_url',
        'consultation_fee',
        'deleted'
    ];

    protected $hidden = [
        'password_hash',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
            'is_verified' => 'boolean',
            'deleted' => 'boolean',
            'years_of_experience' => 'integer',
            'consultation_fee' => 'decimal:2',
        ];
    }

    // No deleted_at column

    /**
     * Get lawyer's appointments
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Get lawyer's reviews
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get lawyer's availability slots
     */
    public function availabilitySlots(): HasMany
    {
        return $this->hasMany(AvailabilitySlot::class);
    }

    /**
     * Get lawyer's categories
     */
    public function categories(): HasMany
    {
        return $this->hasMany(LawyerCategory::class);
    }

    /**
     * Scope for active lawyers
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope for verified lawyers
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope for specialization
     */
    public function scopeBySpecialization($query, $specialization)
    {
        return $query->where('specialization', $specialization);
    }

    /**
     * Get average rating
     */
    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    /**
     * Get total reviews count
     */
    public function getTotalReviewsAttribute()
    {
        return $this->reviews()->count();
    }

    /**
     * Get profile picture URL
     */
    public function getProfilePictureAttribute(): string
    {
        return $this->profile_picture_url 
            ? asset('storage/lawyers/' . $this->profile_picture_url)
            : asset('images/default-lawyer.png');
    }

    /**
     * Get available slots for today
     */
    public function getTodayAvailableSlots()
    {
        return $this->availabilitySlots()
            ->where('start_time', '>=', now()->startOfDay())
            ->where('start_time', '<=', now()->endOfDay())
            ->where('is_booked', false)
            ->orderBy('start_time')
            ->get();
    }
}