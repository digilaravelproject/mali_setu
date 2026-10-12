<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MatrimonyProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'gender',
        'date_of_birth',
        'time_of_birth',
        'age',
        'height',
        'weight',
        'complexion',
        'physical_status',
        'personal_details',
        'family_details',
        'education_details',
        'professional_details',
        'lifestyle_details',
        'location_details',
        'religious_details',      // new JSON column
        'partner_preferences',
        'privacy_settings',
        'approval_status',
        'approved_at',
        'approved_by',
        'rejection_reason',
        'status',
        'profile_expires_at',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'personal_details' => 'array',
        'family_details' => 'array',
        'education_details' => 'array',
        'professional_details' => 'array',
        'lifestyle_details' => 'array',
        'location_details' => 'array',
        'religious_details' => 'array',
        'partner_preferences' => 'array',
        'privacy_settings' => 'array',
        'profile_expires_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    /**
     * Get the user that owns the matrimony profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the connection requests sent to this profile.
     */
    public function receivedConnectionRequests(): HasMany
    {
        return $this->hasMany(ConnectionRequest::class, 'receiver_id', 'user_id');
    }

    /**
     * Get the connection requests sent by this profile.
     */
    public function sentConnectionRequests(): HasMany
    {
        return $this->hasMany(ConnectionRequest::class, 'sender_id', 'user_id');
    }

    /**
     * Get matrimony profile transactions
     */
    public function matrimonyTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'user_id', 'user_id')
            ->where('purpose', 'matrimony_profile')
            ->latest();
    }

    /**
     * Get the candidate's name stored in the matrimony profile.
     */
    public function getCandidateNameAttribute(): string
    {
        $personalDetails = $this->personal_details ?? [];
        $nameParts = array_filter([
            $personalDetails['first_name'] ?? null,
            $personalDetails['middle_name'] ?? null,
            $personalDetails['last_name'] ?? null,
        ], fn ($part) => filled($part));

        return trim(implode(' ', $nameParts))
            ?: trim((string) ($personalDetails['full_name'] ?? $personalDetails['name'] ?? ''));
    }

    /**
     * Search admin listings by candidate details or the owning user account.
     */
    public function scopeAdminSearch($query, string $search)
    {
        $terms = preg_split('/\s+/', trim($search), -1, PREG_SPLIT_NO_EMPTY) ?: [];

        foreach ($terms as $term) {
            $query->where(function ($termQuery) use ($term) {
                $like = '%'.$term.'%';

                $termQuery->where('personal_details->first_name', 'like', $like)
                    ->orWhere('personal_details->middle_name', 'like', $like)
                    ->orWhere('personal_details->last_name', 'like', $like)
                    ->orWhere('personal_details->full_name', 'like', $like)
                    ->orWhere('personal_details->name', 'like', $like)
                    ->orWhereHas('user', function ($userQuery) use ($like) {
                        $userQuery->where('name', 'like', $like)
                            ->orWhere('email', 'like', $like);
                    });
            });
        }

        return $query;
    }

    /**
     * Scope a query to only include approved profiles.
     */
    public function scopeApproved($query)
    {
        return $query->where('approval_status', 'approved');
    }

    /**
     * Scope a query to only include premium profiles.
     */
    public function scopePremium($query)
    {
        return $query->where('is_premium', true);
    }

    /**
     * Scope a query to filter by age range.
     */
    public function scopeAgeRange($query, $minAge, $maxAge)
    {
        return $query->whereBetween('age', [$minAge, $maxAge]);
    }

    /**
     * Scope a query to filter by religion.
     */
    public function scopeReligion($query, $religion)
    {
        return $query->where('religion', $religion);
    }

    /**
     * Scope a query to filter by caste.
     */
    public function scopeCaste($query, $caste)
    {
        return $query->where('caste', $caste);
    }
}
