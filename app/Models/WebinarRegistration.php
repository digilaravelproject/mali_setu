<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebinarRegistration extends Model
{
    protected $fillable = ['webinar_id', 'user_id', 'transaction_id', 'paid_at'];
    protected $casts = ['paid_at' => 'datetime'];
    public function webinar() { return $this->belongsTo(Webinar::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function transaction() { return $this->belongsTo(Transaction::class); }
}
