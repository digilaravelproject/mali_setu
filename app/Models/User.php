<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'user_type',
        'caste_verification_status',
        'status',
        'admin_notes',
        'age',
        'cast_certificate',
        'occupation',
        'reffral_code',
        'address',
        'nearby_location',
        'pincode',
        'road_number',
        'state',
        'city',
        'sector',
        'district',
        'destination',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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
        ];
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole(string $role): bool
    {
        return $this->user_type === $role;
    }

    /**
     * Check if user is verified
     */
    public function isVerified(): bool
    {
        return $this->caste_verification_status === 'approved';
    }

    /**
     * Get the user's caste certificate
     */
    public function casteCertificate()
    {
        return $this->hasOne(CasteCertificate::class);
    }

    /**
     * Get the user's business profile
     */
    public function business()
    {
        return $this->hasOne(Business::class);
    }

    /**
     * Get the user's matrimony profile
     */
    public function matrimonyProfile()
    {
        return $this->hasOne(MatrimonyProfile::class);
    }

    /**
     * Get connection requests sent by this user
     */
    public function sentConnectionRequests()
    {
        return $this->hasMany(ConnectionRequest::class, 'sender_id');
    }

    /**
     * Get connection requests received by this user
     */
    public function receivedConnectionRequests()
    {
        return $this->hasMany(ConnectionRequest::class, 'receiver_id');
    }

    /**
     * Get chat conversations for this user
     */
    public function chatConversations()
    {
        return $this->hasMany(ChatConversation::class, 'user1_id')
            ->orWhere('user2_id', $this->id);
    }

    /**
     * Get the user's volunteer profile/activities
     */
    public function volunteer()
    {
        return $this->hasOne(VolunteerProfile::class);
    }

    /**
     * Get volunteer activities for this user
     */
    public function volunteerActivities()
    {
        return $this->hasMany(VolunteerActivity::class);
    }

    /**
     * Get donations made by this user
     */
    public function donations()
    {
        return $this->hasMany(Donation::class);
    }

    /**
     * Get completed donations made by this user
     */
    public function completedDonations()
    {
        return $this->hasMany(Donation::class)->where('status', 'completed');
    }

    /**
     * Get job applications made by this user
     */
    public function jobApplications()
    {
        return $this->hasMany(JobApplication::class);
    }

    /**
     * Get pending job applications made by this user
     */
    public function pendingJobApplications()
    {
        return $this->hasMany(JobApplication::class)->where('status', 'pending');
    }

    /**
     * Get accepted job applications made by this user
     */
    public function acceptedJobApplications()
    {
        return $this->hasMany(JobApplication::class)->where('status', 'accepted');
    }
}
