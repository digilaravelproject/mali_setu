<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebinarComment extends Model
{
    protected $fillable = ['webinar_id', 'user_id', 'comment'];
    public function webinar() { return $this->belongsTo(Webinar::class); }
    public function user() { return $this->belongsTo(User::class); }
}
