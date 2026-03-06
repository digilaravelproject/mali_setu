<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogLike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BlogController extends Controller
{
    /**
     * List blogs (paginated). Supports optional search and tag filters.
     */
    public function index(Request $request)
    {
        $query = Blog::with('user')->withCount('likes')->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('tags')) {
            $tags = is_array($request->tags) ? $request->tags : explode(',', $request->tags);
            $tags = array_filter(array_map('trim', $tags));

            foreach ($tags as $tag) {
                $query->whereJsonContains('tags', $tag);
            }
        }

        $blogs = $query->paginate($request->get('per_page', 10));

        return response()->json([
            'success' => true,
            'data' => $blogs,
        ]);
    }

    /**
     * Get details for a single blog along with related blogs.
     */
    public function show($id)
    {
        $blog = Blog::with('user')->withCount('likes')->findOrFail($id);

        $related = Blog::where('id', '!=', $blog->id)
            ->when($blog->tags, function ($query) use ($blog) {
                $query->where(function ($q) use ($blog) {
                    foreach ($blog->tags as $tag) {
                        $q->orWhereJsonContains('tags', $tag);
                    }
                });
            })
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'blog' => $blog,
                'related' => $related,
            ],
        ]);
    }

    /**
     * Create a new blog.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'tags' => 'nullable|array',
            'tags.*' => 'string',
            'media' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $tags = $request->tags;
        if (is_string($tags)) {
            $tags = array_filter(array_map('trim', explode(',', $tags)));
        }

        $mediaPath = null;
        $mediaType = null;

        if ($request->filled('media')) {
            $media = $request->media;

            // Parse base64 data URI if provided
            if (str_contains($media, 'data:') && str_contains($media, ';base64,')) {
                [$meta, $encoded] = explode(';base64,', $media);
                $mime = str_replace('data:', '', $meta);
                $extension = explode('/', $mime)[1] ?? 'bin';
                $decoded = base64_decode($encoded);
            } else {
                // Assume base64 without data URI
                $decoded = base64_decode($media);
                $extension = 'jpg';
                $mime = 'image/jpeg';
            }

            if ($decoded === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid media data',
                ], 422);
            }

            if (str_starts_with($mime, 'video/')) {
                $mediaType = 'video';
            } else {
                $mediaType = 'image';
            }

            $fileName = 'blogs/media/' . uniqid() . '.' . $extension;
            Storage::disk('public')->put($fileName, $decoded);
            $mediaPath = $fileName;
        }

        $blog = Blog::create([
            'user_id' => $user->id,
            'title' => $request->title,
            'description' => $request->description,
            'tags' => $tags,
            'media_path' => $mediaPath,
            'media_type' => $mediaType,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Blog created successfully',
            'data' => $blog,
        ]);
    }

    /**
     * Like/unlike a blog.
     */
    public function like($id, Request $request)
    {
        $user = $request->user();

        $blog = Blog::findOrFail($id);

        $existing = BlogLike::where('blog_id', $blog->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $liked = false;
        } else {
            BlogLike::create([
                'blog_id' => $blog->id,
                'user_id' => $user->id,
            ]);
            $liked = true;
        }

        $likesCount = $blog->likes()->count();

        return response()->json([
            'success' => true,
            'data' => [
                'liked' => $liked,
                'likes_count' => $likesCount,
            ],
        ]);
    }

    /**
     * Search blogs.
     */
    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string',
        ]);

        $query = $request['query'];

        $blogs = Blog::with('user')
            ->withCount('likes')
            ->where('title', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 10));

        return response()->json([
            'success' => true,
            'data' => $blogs,
        ]);
    }
}
