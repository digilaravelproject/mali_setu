<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
     * Public URL used by social sharing and link-preview crawlers.
     */
    public function getPublicUrlAttribute(): string
    {
        return route('blogs.public.show', $this->id);
    }

    /**
     * First uploaded image suitable for a social link preview.
     */
    public function getShareImageUrlAttribute(): ?string
    {
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        foreach ((array) $this->media_path as $path) {
            if (!is_string($path) || !in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), $imageExtensions, true)) {
                continue;
            }

            if (Str::startsWith($path, ['http://', 'https://'])) {
                return $path;
            }

            if (!Storage::disk('public')->exists($path)) {
                continue;
            }

            return asset('storage/' . ltrim($path, '/'));
        }

        return null;
    }

    /**
     * Get all comments and replies for the blog.
     */
    public function allComments(): HasMany
    {
        return $this->hasMany(BlogComment::class);
    }
}
