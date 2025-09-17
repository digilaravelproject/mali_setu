<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CasteCertificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'file_path',
        'admin_notes',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    /**
     * Get the user that owns the caste certificate.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin who verified the certificate.
     */
    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Scope a query to only include pending certificates.
     */
    public function scopePending($query)
    {
        return $query->whereHas('user', function($q) {
            $q->where('caste_verification_status', 'pending');
        });
    }

    /**
     * Scope a query to only include approved certificates.
     */
    public function scopeApproved($query)
    {
        return $query->whereHas('user', function($q) {
            $q->where('caste_verification_status', 'approved');
        });
    }

    /**
     * Scope a query to only include rejected certificates.
     */
    public function scopeRejected($query)
    {
        return $query->whereHas('user', function($q) {
            $q->where('caste_verification_status', 'rejected');
        });
    }
}
