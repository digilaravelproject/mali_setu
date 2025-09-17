<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VolunteerApplication extends Model
{
    protected $fillable = [
        'volunteer_profile_id',
        'volunteer_opportunity_id',
        'message',
        'status',
        'applied_at',
        'responded_at',
        'admin_notes',
    ];

    protected $casts = [
        'applied_at' => 'datetime',
        'responded_at' => 'datetime',
    ];

    /**
     * Get the volunteer profile that owns this application
     */
    public function volunteerProfile(): BelongsTo
    {
        return $this->belongsTo(VolunteerProfile::class);
    }

    /**
     * Get the volunteer opportunity this application is for
     */
    public function volunteerOpportunity(): BelongsTo
    {
        return $this->belongsTo(VolunteerOpportunity::class);
    }

    /**
     * Get the user who made this application
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'volunteer_profile_id', 'id')
            ->through('volunteerProfile');
    }

    /**
     * Check if application is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if application is approved
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if application is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Approve the application
     */
    public function approve(string $adminNotes = null): void
    {
        $this->update([
            'status' => 'approved',
            'responded_at' => now(),
            'admin_notes' => $adminNotes,
        ]);
        
        // Increment volunteers registered count
        $this->volunteerOpportunity->incrementVolunteersRegistered();
    }

    /**
     * Reject the application
     */
    public function reject(string $adminNotes = null): void
    {
        $this->update([
            'status' => 'rejected',
            'responded_at' => now(),
            'admin_notes' => $adminNotes,
        ]);
    }

    /**
     * Withdraw the application
     */
    public function withdraw(): void
    {
        $wasApproved = $this->isApproved();
        
        $this->update([
            'status' => 'withdrawn',
            'responded_at' => now(),
        ]);
        
        // If was approved, decrement volunteers registered count
        if ($wasApproved) {
            $this->volunteerOpportunity->decrementVolunteersRegistered();
        }
    }
}
