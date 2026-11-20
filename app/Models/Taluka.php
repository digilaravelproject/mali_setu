<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Taluka extends Model
{
    protected $fillable = ['district_id', 'external_code', 'name', 'is_active', 'synced_at'];
    protected $casts = ['is_active' => 'boolean', 'synced_at' => 'datetime'];

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function localities(): HasMany
    {
        return $this->hasMany(Locality::class);
    }
}
