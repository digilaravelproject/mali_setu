<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'name',
        'description',
        'cost',
        'image_path',
        'image_paths',
        'status',
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'image_paths' => 'array',
    ];

    /**
     * Get the business that owns the service.
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Scope a query to only include active services.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Get the formatted cost attribute.
     */
    public function getFormattedCostAttribute()
    {
        return '₹' . number_format($this->cost, 2);
    }

    public function getImagesAttribute(): array
    {
        $paths = $this->image_paths ?: array_filter([$this->image_path]);

        return collect($paths)->map(fn ($path) => [
            'path' => $path,
            'url' => asset('storage/'.$path),
        ])->values()->all();
    }

    protected $appends = ['images'];
}
