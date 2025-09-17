<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VolunteerProfile extends Model
{
    protected $fillable = [
        'user_id',
        'skills',
        'experience',
        'availability',
        'location',
        'bio',
        'interests',
        'status',
    ];

    protected $casts = [
        'interests' => 'array',
    ];

    /**
     * Get the user that owns the volunteer profile
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
