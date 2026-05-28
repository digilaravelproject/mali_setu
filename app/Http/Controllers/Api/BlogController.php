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
        $userId = auth()->id();
        
        $query = Blog::with('user')
        ->withCount('likes')
        ->withExists([
            'likes as is_liked' => function ($q) use ($userId) {
                $q->where('user_id', $userId);
            }
        ])
        ->orderBy('created_at', 'desc');

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
        $userId = auth()->id(); // logged-in user
        
        // $blog = Blog::with('user')->withCount('likes')->findOrFail($id);
        $blog = Blog::with('user')
            ->withCount('likes')
            ->withExists([
                'likes as is_liked' => function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                }
            ])
        ->findOrFail($id);


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

        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'blog_type' => 'required|string|max:255',
            'tags' => 'required',
        ];

        if ($request->hasFile('media')) {
            $rules['media'] = 'nullable|array';
            $rules['media.*'] = 'file|mimes:jpg,jpeg,png,gif,mp4,mov,avi,webm|max:10240';
        }

        $validator = Validator::make($request->all(), $rules, [
            'title.required' => 'Please enter blog title',
            'blog_type.required' => 'Please select a blog type',
            'description.required' => 'Please enter blog description',
            'tags.required' => 'Please add at least one tag',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $tags = [];
        if ($request->filled('tags')) {
            if (is_array($request->tags)) {
                $tags = array_filter(array_map('trim', $request->tags));
            } else {
                $tags = array_filter(array_map('trim', explode(',', $request->tags)));
            }
        }

        $mediaPaths = [];
        $mediaType = 'image';

        // 1. Handle actual uploaded files
        if ($request->hasFile('media')) {
            $files = is_array($request->file('media')) ? $request->file('media') : [$request->file('media')];
            foreach ($files as $file) {
                $path = $file->store('blogs/media', 'public');
                $mediaPaths[] = $path;
                $mime = $file->getMimeType();
                if (str_starts_with($mime, 'video/')) {
                    $mediaType = 'video';
                }
            }
        }
        // 2. Fallback to base64 strings (either a single string or an array of strings)
        elseif ($request->filled('media')) {
            $mediaInputs = is_array($request->media) ? $request->media : [$request->media];
            foreach ($mediaInputs as $mediaItem) {
                if (is_string($mediaItem)) {
                    if (str_contains($mediaItem, 'data:') && str_contains($mediaItem, ';base64,')) {
                        [$meta, $encoded] = explode(';base64,', $mediaItem);
                        $mime = str_replace('data:', '', $meta);
                        $extension = explode('/', $mime)[1] ?? 'bin';
                        $decoded = base64_decode($encoded);
                    } else {
                        // Assume base64 without data URI
                        $decoded = base64_decode($mediaItem);
                        $extension = 'jpg';
                        $mime = 'image/jpeg';
                    }

                    if ($decoded !== false) {
                        if (str_starts_with($mime, 'video/')) {
                            $mediaType = 'video';
                        }
                        $fileName = 'blogs/media/' . uniqid() . '.' . $extension;
                        Storage::disk('public')->put($fileName, $decoded);
                        $mediaPaths[] = $fileName;
                    }
                }
            }
        }

        $blog = Blog::create([
            'user_id' => $user->id,
            'title' => $request->title,
            'description' => $request->description,
            'blog_type' => $request->blog_type,
            'tags' => array_values($tags),
            'media_path' => $mediaPaths,
            'media_type' => $mediaType,
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Blog created successfully',
            'data' => $blog,
        ], 201);
    }

    /**
     * Update the specified blog.
     */
    public function update(Request $request, $id)
    {
        $blog = Blog::findOrFail($id);
        $user = $request->user();

        if ($blog->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action. You do not own this blog.',
            ], 403);
        }

        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'blog_type' => 'required|string|max:255',
            'tags' => 'required',
        ];

        if ($request->hasFile('media')) {
            $rules['media'] = 'nullable|array';
            $rules['media.*'] = 'file|mimes:jpg,jpeg,png,gif,mp4,mov,avi,webm|max:10240';
        }

        $validator = Validator::make($request->all(), $rules, [
            'title.required' => 'Please enter blog title',
            'blog_type.required' => 'Please select a blog type',
            'description.required' => 'Please enter blog description',
            'tags.required' => 'Please add at least one tag',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $tags = [];
        if ($request->filled('tags')) {
            if (is_array($request->tags)) {
                $tags = array_filter(array_map('trim', $request->tags));
            } else {
                $tags = array_filter(array_map('trim', explode(',', $request->tags)));
            }
        }

        $mediaPaths = $blog->media_path ?? [];
        $mediaType = $blog->media_type ?? 'image';
        $hasNewMedia = $request->hasFile('media') || $request->filled('media');

        if ($hasNewMedia) {
            // Delete old media files from storage
            if ($blog->media_path) {
                $oldPaths = is_array($blog->media_path) ? $blog->media_path : json_decode($blog->media_path, true);
                if (is_array($oldPaths)) {
                    foreach ($oldPaths as $oldPath) {
                        if (Storage::disk('public')->exists($oldPath)) {
                            Storage::disk('public')->delete($oldPath);
                        }
                    }
                } elseif (is_string($blog->media_path)) {
                    if (Storage::disk('public')->exists($blog->media_path)) {
                        Storage::disk('public')->delete($blog->media_path);
                    }
                }
            }

            $mediaPaths = [];
            $mediaType = 'image';

            // 1. Handle uploaded files
            if ($request->hasFile('media')) {
                $files = is_array($request->file('media')) ? $request->file('media') : [$request->file('media')];
                foreach ($files as $file) {
                    $path = $file->store('blogs/media', 'public');
                    $mediaPaths[] = $path;
                    $mime = $file->getMimeType();
                    if (str_starts_with($mime, 'video/')) {
                        $mediaType = 'video';
                    }
                }
            }
            // 2. Handle base64 strings
            elseif ($request->filled('media')) {
                $mediaInputs = is_array($request->media) ? $request->media : [$request->media];
                foreach ($mediaInputs as $mediaItem) {
                    if (is_string($mediaItem)) {
                        if (str_contains($mediaItem, 'data:') && str_contains($mediaItem, ';base64,')) {
                            [$meta, $encoded] = explode(';base64,', $mediaItem);
                            $mime = str_replace('data:', '', $meta);
                            $extension = explode('/', $mime)[1] ?? 'bin';
                            $decoded = base64_decode($encoded);
                        } else {
                            $decoded = base64_decode($mediaItem);
                            $extension = 'jpg';
                            $mime = 'image/jpeg';
                        }

                        if ($decoded !== false) {
                            if (str_starts_with($mime, 'video/')) {
                                $mediaType = 'video';
                            }
                            $fileName = 'blogs/media/' . uniqid() . '.' . $extension;
                            Storage::disk('public')->put($fileName, $decoded);
                            $mediaPaths[] = $fileName;
                        }
                    }
                }
            }
        }

        $blog->update([
            'title' => $request->title,
            'description' => $request->description,
            'blog_type' => $request->blog_type,
            'tags' => array_values($tags),
            'media_path' => $mediaPaths,
            'media_type' => $mediaType,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Blog updated successfully',
            'data' => $blog,
        ]);
    }

    /**
     * Remove the specified blog.
     */
    public function destroy($id, Request $request)
    {
        $blog = Blog::findOrFail($id);
        $user = $request->user();

        if ($blog->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action. You do not own this blog.',
            ], 403);
        }

        // Delete associated files
        if ($blog->media_path) {
            $oldPaths = is_array($blog->media_path) ? $blog->media_path : json_decode($blog->media_path, true);
            if (is_array($oldPaths)) {
                foreach ($oldPaths as $oldPath) {
                    if (Storage::disk('public')->exists($oldPath)) {
                        Storage::disk('public')->delete($oldPath);
                    }
                }
            } elseif (is_string($blog->media_path)) {
                if (Storage::disk('public')->exists($blog->media_path)) {
                    Storage::disk('public')->delete($blog->media_path);
                }
            }
        }

        $blog->delete();

        return response()->json([
            'success' => true,
            'message' => 'Blog deleted successfully',
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
