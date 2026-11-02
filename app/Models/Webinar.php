<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Webinar extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by', 'title', 'category', 'description', 'banner_path', 'speaker_name', 'agenda',
        'starts_at', 'duration_minutes', 'agora_channel', 'agora_link', 'audience',
        'require_registration', 'allow_qa', 'allow_recording', 'access_type', 'plan_ids', 'status',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'require_registration' => 'boolean',
        'allow_qa' => 'boolean',
        'allow_recording' => 'boolean',
        'plan_ids' => 'array',
    ];
    protected $appends = ['banner_url', 'ends_at', 'webinar_status'];

    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function registrations() { return $this->hasMany(WebinarRegistration::class); }
    public function registeredUsers() { return $this->belongsToMany(User::class, 'webinar_registrations')->withTimestamps(); }
    public function comments() { return $this->hasMany(WebinarComment::class); }

    public function getBannerUrlAttribute(): ?string
    {
        return $this->banner_path ? Storage::disk('public')->url($this->banner_path) : null;
    }

    public function getEndsAtAttribute()
    {
        return $this->starts_at?->copy()->addMinutes($this->duration_minutes);
    }

    public function getWebinarStatusAttribute(): string
    {
        if ($this->status === 'cancelled') return 'cancelled';
        if (now()->lt($this->starts_at)) return 'upcoming';
        if (now()->lte($this->ends_at)) return 'live';
        return 'completed';
    }

    public function userCanAccess(User $user): bool
    {
        if ($user->user_type === 'admin') return true;
        if ($user->caste_verification_status !== 'approved') return false;

        $hasBusinessSubscription = $user->business
            && $user->business->subscription_status === 'active'
            && (! $user->business->subscription_expires_at || now()->lt($user->business->subscription_expires_at));
        $hasMatrimonySubscription = $user->matrimonyProfile?->profile_expires_at
            && now()->lt($user->matrimonyProfile->profile_expires_at);

        if ($this->audience === 'subscribers' && ! $hasBusinessSubscription && ! $hasMatrimonySubscription) {
            return false;
        }
        if ($this->access_type !== 'specific_plans') return true;

        $selectedPlans = collect($this->plan_ids ?? []);
        return Transaction::where('user_id', $user->id)->where('status', 'completed')
            ->whereIn('purpose', ['business_registration', 'matrimony_profile'])->get()
            ->contains(function ($transaction) use ($selectedPlans, $hasBusinessSubscription, $hasMatrimonySubscription) {
                $prefix = $transaction->purpose === 'business_registration' ? 'business:' : 'matrimony:';
                $active = $transaction->purpose === 'business_registration' ? $hasBusinessSubscription : $hasMatrimonySubscription;
                return $active && $selectedPlans->contains($prefix.($transaction->metadata['plan_id'] ?? ''));
            });
    }
}
