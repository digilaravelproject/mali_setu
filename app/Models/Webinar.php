<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Webinar extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by', 'title', 'description', 'banner_path', 'speaker_name', 'agenda',
        'starts_at', 'duration_minutes', 'agora_channel', 'agora_link', 'audience', 'status',
    ];

    protected $casts = ['starts_at' => 'datetime'];
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
}
