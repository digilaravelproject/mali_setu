<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Business extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'business_name',
        'business_type',
        'category_id',
        'description',
        'contact_phone',
        'contact_email',
        // 👇 add these
        'country',
        'state',
        'district',
        'village',
        'taluka',
        'address',
        'city',
        'pincode',
        'website',
        'verification_status',
        'subscription_status',
        'subscription_expires_at',
        'job_posting_limit',
        'verified_at',
        'verified_by',
        'rejection_reason',
        'status',
        'opening_time',
        'closing_time',
        'photo',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'subscription_expires_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    /**
     * Get the user that owns the business
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the business category
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(BusinessCategory::class);
    }

    /**
     * Get the products for the business
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get the services for the business
     */
    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    /**
     * Get the locations for the business
     */
    public function locations(): HasMany
    {
        return $this->hasMany(BusinessLocation::class);
    }

    /**
     * Get the reviews for the business
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(BusinessReview::class);
    }

    /**
     * Get the job postings for the business
     */
    public function jobPostings(): HasMany
    {
        return $this->hasMany(JobPosting::class);
    }

    /**
     * Get business registration transactions for this business owner.
     */
    public function businessRegistrationTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'user_id', 'user_id')
            ->where('purpose', 'business_registration');
    }

    /** Payment audit rows for this user's business registration. */
    public function businessRegistrationPayments(): HasMany
    {
        return $this->hasMany(Payment::class, 'user_id', 'user_id')
            ->where('payment_type', 'business_registration')
            ->latest();
    }

    /**
     * Get a simple payment status for this business.
     */
    public function getBusinessPaymentStatusAttribute(): string
    {
        $transactions = $this->relationLoaded('businessRegistrationTransactions')
            ? $this->businessRegistrationTransactions
            : $this->businessRegistrationTransactions()->get();
        $payments = $this->relationLoaded('businessRegistrationPayments')
            ? $this->businessRegistrationPayments
            : $this->businessRegistrationPayments()->get();

        if ($transactions->contains('status', 'completed') || $payments->contains('status', 'completed')) {
            return 'paid';
        }

        if ($transactions->contains('status', 'pending') || $payments->contains('status', 'pending')) {
            return 'pending';
        }

        return 'unpaid';
    }

    /**
     * Determine whether business payment is pending.
     */
    public function getPaymentPendingAttribute(): bool
    {
        return $this->businessPaymentStatus !== 'paid';
    }

    /**
     * Set the photo attribute (stores as a JSON array string to comply with the CHECK constraint)
     */
    public function setPhotoAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['photo'] = json_encode($value);
        } elseif (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $this->attributes['photo'] = $value;
            } else {
                $paths = array_filter(array_map('trim', explode(',', $value)));
                $this->attributes['photo'] = json_encode(array_values($paths));
            }
        } else {
            $this->attributes['photo'] = null;
        }
    }

    /**
     * Get the photo attribute (decodes the JSON array and formats as comma-separated string)
     */
    public function getPhotoAttribute($value)
    {
        if (empty($value)) {
            return null;
        }
        $decoded = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return implode(', ', $decoded);
        }

        return $value;
    }

    /**
     * Scope for approved businesses
     */
    public function scopeApproved($query)
    {
        return $query->where('verification_status', 'approved');
    }

    /**
     * Scope for active subscriptions
     */
    public function scopeActiveSubscription($query)
    {
        return $query->where('subscription_status', 'active')
            ->where('subscription_expires_at', '>', now());
    }
}
