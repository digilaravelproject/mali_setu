<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubCast extends Model
{
    protected $table = 'sub_casts';

    protected $fillable = [
        'cast_id',
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the cast that owns the sub-cast
     */
    public function cast(): BelongsTo
    {
        return $this->belongsTo(Cast::class);
    }
}
