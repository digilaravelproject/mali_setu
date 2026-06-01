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
        'blog_type',
        'tags',
        'media_path',
        'media_type',
        'is_active',
    ];

    protected $casts = [
        'tags' => 'array',
        'is_active' => 'boolean',
        'media_path' => 'array',
    ];

    /**
     * Get the author of the blog.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category of the blog.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'blog_type');
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

    /**
     * Get top-level comments for the blog.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(BlogComment::class)->whereNull('parent_id')->latest();
    }

    /**
     * Get all comments and replies for the blog.
     */
    public function allComments(): HasMany
    {
        return $this->hasMany(BlogComment::class);
    }
}
