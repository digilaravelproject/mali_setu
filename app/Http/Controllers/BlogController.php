<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\BlogLike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{
    /**
     * Display a listing of blogs.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Blog::with('user')->withCount('likes');

        // Apply Search Filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Apply Tag Filter
        if ($request->filled('tag')) {
            $query->whereJsonContains('tags', trim($request->tag));
        }

        // Get public active blogs
        $blogs = $query->where('is_active', true)
            ->latest()
            ->paginate(9);

        // Get user's own blogs (if they have blog creation access)
        $myBlogs = [];
        if ($user->blog_access) {
            $myBlogs = Blog::where('user_id', $user->id)
                ->withCount('likes')
                ->latest()
                ->get();
        }

        return view('blog.index', compact('blogs', 'myBlogs', 'user'));
    }

    /**
     * Show the form for creating a new blog.
     */
    public function create()
    {
        $user = Auth::user();
        if (!$user->blog_access) {
            return redirect()->route('blogs.index')->with('error', 'You do not have access to write blogs. Please contact an administrator.');
        }

        return view('blog.create');
    }

    /**
     * Store a newly created blog in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->blog_access) {
            return redirect()->route('blogs.index')->with('error', 'Unauthorized action.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'blog_type' => 'required|string|max:255',
            'description' => 'required|string|max:10000',
            'tags' => 'required|string',
            'media' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,mov,avi,webm|max:10240', // Max 10MB
        ], [
            'title.required' => 'Please enter blog title',
            'blog_type.required' => 'Please select a blog type',
            'description.required' => 'Please enter blog description',
            'tags.required' => 'Please add at least one tag',
        ]);

        try {
            $tags = [];
            if ($request->filled('tags')) {
                $tags = array_filter(array_map('trim', explode(',', $request->tags)));
            }

            $mediaPath = null;
            $mediaType = null;

            if ($request->hasFile('media')) {
                $file = $request->file('media');
                $mediaPath = $file->store('blogs/media', 'public');
                $mime = $file->getMimeType();
                $mediaType = str_starts_with($mime, 'video/') ? 'video' : 'image';
            }

            Blog::create([
                'user_id' => $user->id,
                'title' => $request->title,
                'blog_type' => $request->blog_type,
                'description' => $request->description,
                'tags' => array_values($tags),
                'media_path' => $mediaPath,
                'media_type' => $mediaType,
                'is_active' => true,
            ]);

            return redirect()->route('blogs.index')->with('success', 'Blog article published successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to publish blog: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified blog details.
     */
    public function show($id)
    {
        $blog = Blog::with('user')->withCount('likes')->findOrFail($id);
        $user = Auth::user();

        // Get related blogs by matching tags
        $related = Blog::where('id', '!=', $blog->id)
            ->where('is_active', true)
            ->when($blog->tags, function ($query) use ($blog) {
                $query->where(function ($q) use ($blog) {
                    foreach ($blog->tags as $tag) {
                        $q->orWhereJsonContains('tags', $tag);
                    }
                });
            })
            ->latest()
            ->take(5)
            ->get();

        // Fallback to recent blogs if no matching tags
        if ($related->isEmpty()) {
            $related = Blog::where('id', '!=', $blog->id)
                ->where('is_active', true)
                ->latest()
                ->take(5)
                ->get();
        }

        $isLiked = false;
        if ($user) {
            $isLiked = BlogLike::where('blog_id', $blog->id)
                ->where('user_id', $user->id)
                ->exists();
        }

        return view('blog.show', compact('blog', 'related', 'isLiked'));
    }

    /**
     * Show the form for editing the specified blog.
     */
    public function edit($id)
    {
        $blog = Blog::findOrFail($id);
        $user = Auth::user();

        if ($blog->user_id !== $user->id) {
            return redirect()->route('blogs.index')->with('error', 'You are not authorized to edit this blog.');
        }

        return view('blog.edit', compact('blog'));
    }

    /**
     * Update the specified blog in storage.
     */
    public function update(Request $request, $id)
    {
        $blog = Blog::findOrFail($id);
        $user = Auth::user();

        if ($blog->user_id !== $user->id) {
            return redirect()->route('blogs.index')->with('error', 'Unauthorized action.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'blog_type' => 'required|string|max:255',
            'description' => 'required|string|max:10000',
            'tags' => 'required|string',
            'media' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,mov,avi,webm|max:10240',
        ], [
            'title.required' => 'Please enter blog title',
            'blog_type.required' => 'Please select a blog type',
            'description.required' => 'Please enter blog description',
            'tags.required' => 'Please add at least one tag',
        ]);

        try {
            $tags = [];
            if ($request->filled('tags')) {
                $tags = array_filter(array_map('trim', explode(',', $request->tags)));
            }

            if ($request->hasFile('media')) {
                // Delete old media file
                if ($blog->media_path && Storage::disk('public')->exists($blog->media_path)) {
                    Storage::disk('public')->delete($blog->media_path);
                }

                $file = $request->file('media');
                $blog->media_path = $file->store('blogs/media', 'public');
                $mime = $file->getMimeType();
                $blog->media_type = str_starts_with($mime, 'video/') ? 'video' : 'image';
            }

            $blog->title = $request->title;
            $blog->blog_type = $request->blog_type;
            $blog->description = $request->description;
            $blog->tags = array_values($tags);
            $blog->save();

            return redirect()->route('blogs.index')->with('success', 'Blog article updated successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update blog: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified blog from storage.
     */
    public function destroy($id)
    {
        $blog = Blog::findOrFail($id);
        $user = Auth::user();

        if ($blog->user_id !== $user->id) {
            return redirect()->route('blogs.index')->with('error', 'Unauthorized action.');
        }

        try {
            if ($blog->media_path && Storage::disk('public')->exists($blog->media_path)) {
                Storage::disk('public')->delete($blog->media_path);
            }
            $blog->delete();

            return redirect()->route('blogs.index')->with('success', 'Blog article deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('blogs.index')->with('error', 'Failed to delete blog: ' . $e->getMessage());
        }
    }

    /**
     * Like/unlike a blog via AJAX or Web request.
     */
    public function like($id)
    {
        $user = Auth::user();
        $blog = Blog::findOrFail($id);

        $like = BlogLike::where('blog_id', $blog->id)
            ->where('user_id', $user->id)
            ->first();

        if ($like) {
            $like->delete();
            $liked = false;
        } else {
            BlogLike::create([
                'blog_id' => $blog->id,
                'user_id' => $user->id,
            ]);
            $liked = true;
        }

        $likesCount = $blog->likes()->count();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'liked' => $liked,
                'likes_count' => $likesCount
            ]);
        }

        return back();
    }

    /**
     * Display the specified blog details for the public (including guest users).
     */
    public function showPublic($id)
    {
        $blog = Blog::with('user')->findOrFail($id);
        
        // Increase the views count dynamically on details view!
        $blog->increment('views_count');
        
        // Refresh model to get updated views_count and include likes count
        $blog = Blog::with('user')->withCount('likes')->findOrFail($id);

        // Get related blogs by matching tags (or recent blogs fallback)
        $related = Blog::where('id', '!=', $blog->id)
            ->where('is_active', true)
            ->when($blog->tags, function ($query) use ($blog) {
                $query->where(function ($q) use ($blog) {
                    foreach ($blog->tags as $tag) {
                        $q->orWhereJsonContains('tags', $tag);
                    }
                });
            })
            ->latest()
            ->take(5)
            ->get();

        if ($related->isEmpty()) {
            $related = Blog::where('id', '!=', $blog->id)
                ->where('is_active', true)
                ->latest()
                ->take(5)
                ->get();
        }

        $isLiked = false;
        $user = Auth::user();
        if ($user) {
            $isLiked = BlogLike::where('blog_id', $blog->id)
                ->where('user_id', $user->id)
                ->exists();
        } else {
            $isLiked = BlogLike::where('blog_id', $blog->id)
                ->where('session_id', session()->getId())
                ->exists();
        }

        return view('blog.public_show', compact('blog', 'related', 'isLiked'));
    }

    /**
     * Public like/unlike a blog via AJAX or Web request for both guests and authenticated users.
     */
    public function likePublic($id)
    {
        $blog = Blog::findOrFail($id);
        $user = Auth::user();

        if ($user) {
            $like = BlogLike::where('blog_id', $blog->id)
                ->where('user_id', $user->id)
                ->first();

            if ($like) {
                $like->delete();
                $liked = false;
            } else {
                BlogLike::create([
                    'blog_id' => $blog->id,
                    'user_id' => $user->id,
                ]);
                $liked = true;
            }
        } else {
            $sessionId = session()->getId();
            $like = BlogLike::where('blog_id', $blog->id)
                ->where('session_id', $sessionId)
                ->first();

            if ($like) {
                $like->delete();
                $liked = false;
            } else {
                BlogLike::create([
                    'blog_id' => $blog->id,
                    'session_id' => $sessionId,
                ]);
                $liked = true;
            }
        }

        $likesCount = $blog->likes()->count();

        return response()->json([
            'success' => true,
            'liked' => $liked,
            'likes_count' => $likesCount
        ]);
    }
}
