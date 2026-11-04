<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Locality extends Model
{
    protected $fillable = ['taluka_id', 'name', 'type', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function taluka(): BelongsTo
    {
        return $this->belongsTo(Taluka::class);
    }
}
