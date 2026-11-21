<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VolunteerReferralProfile extends Model
{
    protected $fillable = ['user_id', 'commission_points', 'commission_mode', 'role_commission_points', 'district_id', 'taluka_id', 'locality_id', 'is_active'];

    protected $casts = ['commission_points' => 'integer', 'role_commission_points' => 'array', 'is_active' => 'boolean'];

    public function commissionForRole(?string $role): int
    {
        if (($this->commission_mode ?? 'default') !== 'role_wise') {
            return (int) $this->commission_points;
        }

        $points = $this->role_commission_points ?? [];

        return (int) ($points[$role] ?? $this->commission_points);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function taluka()
    {
        return $this->belongsTo(Taluka::class);
    }

    public function locality()
    {
        return $this->belongsTo(Locality::class);
    }
}
