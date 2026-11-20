<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Locality extends Model
{
    protected $fillable = ['taluka_id', 'external_code', 'name', 'type', 'is_active', 'synced_at'];
    protected $casts = ['is_active' => 'boolean', 'synced_at' => 'datetime'];

    public function taluka(): BelongsTo
    {
        return $this->belongsTo(Taluka::class);
    }
}
