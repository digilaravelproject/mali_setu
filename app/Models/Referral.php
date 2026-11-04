<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    protected $fillable = ['referrer_user_id', 'referred_user_id', 'referral_code_used', 'commission_points', 'status'];
    protected $casts = ['commission_points' => 'integer'];

    public function referrer() { return $this->belongsTo(User::class, 'referrer_user_id'); }
    public function referredUser() { return $this->belongsTo(User::class, 'referred_user_id'); }
}
