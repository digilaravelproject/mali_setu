<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Taluka extends Model
{
    protected $fillable = ['district_id', 'name', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function localities(): HasMany
    {
        return $this->hasMany(Locality::class);
    }
}
