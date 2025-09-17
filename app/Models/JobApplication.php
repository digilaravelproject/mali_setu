<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class JobApplication extends Model
{
    protected $fillable = [
        'user_id',
        'job_posting_id',
        'cover_letter',
        'resume_url',
        'additional_info',
        'status',
        'employer_notes',
        'applied_at',
        'reviewed_at'
    ];

    protected $casts = [
        'applied_at' => 'datetime',
        'reviewed_at' => 'datetime'
    ];

    /**
     * Get the user who applied for the job
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the job posting this application is for
     */
    public function jobPosting(): BelongsTo
    {
        return $this->belongsTo(JobPosting::class);
    }

    /**
     * Scope for pending applications
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for reviewed applications
     */
    public function scopeReviewed($query)
    {
        return $query->where('status', 'reviewed');
    }

    /**
     * Scope for accepted applications
     */
    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    /**
     * Scope for rejected applications
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Mark application as reviewed
     */
    public function markAsReviewed($notes = null)
    {
        $this->update([
            'status' => 'reviewed',
            'reviewed_at' => now(),
            'employer_notes' => $notes
        ]);
    }

    /**
     * Accept the application
     */
    public function accept($notes = null)
    {
        $this->update([
            'status' => 'accepted',
            'reviewed_at' => now(),
            'employer_notes' => $notes
        ]);
    }

    /**
     * Reject the application
     */
    public function reject($notes = null)
    {
        $this->update([
            'status' => 'rejected',
            'reviewed_at' => now(),
            'employer_notes' => $notes
        ]);
    }

    /**
     * Check if application is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if application is reviewed
     */
    public function isReviewed(): bool
    {
        return $this->status === 'reviewed';
    }

    /**
     * Check if application is accepted
     */
    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

    /**
     * Check if application is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Get status badge color for UI
     */
    public function getStatusBadgeColor(): string
    {
        return match($this->status) {
            'pending' => 'warning',
            'reviewed' => 'info',
            'accepted' => 'success',
            'rejected' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Get formatted applied date
     */
    public function getFormattedAppliedDateAttribute(): string
    {
        return $this->applied_at->format('M d, Y');
    }

    /**
     * Get formatted reviewed date
     */
    public function getFormattedReviewedDateAttribute(): ?string
    {
        return $this->reviewed_at?->format('M d, Y');
    }
}