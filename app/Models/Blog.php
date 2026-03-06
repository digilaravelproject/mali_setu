<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Blog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'tags',
        'media_path',
        'media_type',
        'is_active',
    ];

    protected $casts = [
        'tags' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the author of the blog.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get likes for the blog.
     */
    public function likes(): HasMany
    {
        return $this->hasMany(BlogLike::class);
    }

    /**
     * Check if a given user has liked this blog.
     */
    public function likedBy(int $userId): bool
    {
        return $this->likes()->where('user_id', $userId)->exists();
    }
}
