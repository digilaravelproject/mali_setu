<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'name',
        'description',
        'cost',
        'image_path',
        'status',
    ];

    protected $casts = [
        'cost' => 'decimal:2',
    ];

    /**
     * Get the business that owns the product.
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Scope a query to only include active products.
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
}