<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class JobPosting extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'title',
        'description',
        'requirements',
        'salary_range',
        'job_type',
        'location',
        'experience_level',
        'employment_type',
        'category',
        'skills_required',
        'benefits',
        'application_deadline',
        'is_active',
        'expires_at',
        'status'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
        'application_deadline' => 'datetime',
        'skills_required' => 'array',
        'benefits' => 'array'
    ];

    /**
     * Get the business that owns the job posting
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get all applications for this job posting
     */
    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    /**
     * Get pending applications for this job posting
     */
    public function pendingApplications(): HasMany
    {
        return $this->hasMany(JobApplication::class)->where('status', 'pending');
    }

    /**
     * Get accepted applications for this job posting
     */
    public function acceptedApplications(): HasMany
    {
        return $this->hasMany(JobApplication::class)->where('status', 'accepted');
    }

    /**
     * Scope for active job postings
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where('expires_at', '>', now())
                    ->where('status', 'approved');
    }

    /**
     * Scope for expired job postings
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now());
    }

    /**
     * Scope for pending approval job postings
     */
    public function scopePendingApproval($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for approved job postings
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for rejected job postings
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Scope for filtering by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope for filtering by location
     */
    public function scopeByLocation($query, $location)
    {
        return $query->where('location', 'like', '%' . $location . '%');
    }

    /**
     * Scope for filtering by experience level
     */
    public function scopeByExperienceLevel($query, $level)
    {
        return $query->where('experience_level', $level);
    }

    /**
     * Scope for filtering by employment type
     */
    public function scopeByEmploymentType($query, $type)
    {
        return $query->where('employment_type', $type);
    }

    /**
     * Check if job posting is still accepting applications
     */
    public function isAcceptingApplications(): bool
    {
        return $this->is_active && 
               $this->status === 'approved' &&
               $this->expires_at > now() &&
               (!$this->application_deadline || $this->application_deadline > now());
    }

    /**
     * Get total applications count
     */
    public function getTotalApplicationsAttribute(): int
    {
        return $this->applications()->count();
    }

    /**
     * Get pending applications count
     */
    public function getPendingApplicationsCountAttribute(): int
    {
        return $this->pendingApplications()->count();
    }

    /**
     * Get accepted applications count
     */
    public function getAcceptedApplicationsCountAttribute(): int
    {
        return $this->acceptedApplications()->count();
    }

    /**
     * Check if user has already applied for this job
     */
    public function hasUserApplied($userId): bool
    {
        return $this->applications()->where('user_id', $userId)->exists();
    }

    /**
     * Get formatted salary range
     */
    public function getFormattedSalaryRangeAttribute(): string
    {
        return $this->salary_range ?: 'Not specified';
    }

    /**
     * Get formatted posted date
     */
    public function getFormattedPostedDateAttribute(): string
    {
        return $this->created_at->format('M d, Y');
    }

    /**
     * Get formatted expiry date
     */
    public function getFormattedExpiryDateAttribute(): string
    {
        return $this->expires_at->format('M d, Y');
    }

    /**
     * Get status badge color for UI
     */
    public function getStatusBadgeColor(): string
    {
        return match($this->status) {
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Approve the job posting
     */
    public function approve()
    {
        $this->update(['status' => 'approved']);
    }

    /**
     * Reject the job posting
     */
    public function reject()
    {
        $this->update(['status' => 'rejected', 'is_active' => false]);
    }

    /**
     * Deactivate the job posting
     */
    public function deactivate()
    {
        $this->update(['is_active' => false]);
    }

    /**
     * Activate the job posting
     */
    public function activate()
    {
        if ($this->status === 'approved') {
            $this->update(['is_active' => true]);
        }
    }
}