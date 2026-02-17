<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VolunteerOpportunity extends Model
{
    protected $fillable = [
        'title',
        'description',
        'organization',
        'location',
        'required_skills',
        'start_date',
        'end_date',
        'volunteers_needed',
        'volunteers_registered',
        'status',
        'contact_person',
        'contact_email',
        'contact_phone',
        'requirements',
        'time_commitment',
        'admin_notes',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'required_skills' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'volunteers_needed' => 'integer',
        'volunteers_registered' => 'integer',
        'reviewed_at' => 'datetime',
    ];

    /**
     * Get all applications for this opportunity
     */
    public function applications(): HasMany
    {
        return $this->hasMany(VolunteerApplication::class);
    }

    /**
     * Get approved applications for this opportunity
     */
    public function approvedApplications(): HasMany
    {
        return $this->hasMany(VolunteerApplication::class)->where('status', 'approved');
    }

    /**
     * Check if opportunity is full
     */
    public function isFull(): bool
    {
        return $this->volunteers_registered >= $this->volunteers_needed;
    }

    /**
     * Check if opportunity is active and accepting applications
     */
    public function isAcceptingApplications(): bool
    {
        return $this->status === 'active' && !$this->isFull() && $this->start_date > now();
    }

    /**
     * Increment volunteers registered count
     */
    public function incrementVolunteersRegistered(): void
    {
        $this->increment('volunteers_registered');
    }

    /**
     * Decrement volunteers registered count
     */
    public function decrementVolunteersRegistered(): void
    {
        $this->decrement('volunteers_registered');
    }

    /**
     * Get the admin who reviewed this opportunity
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
