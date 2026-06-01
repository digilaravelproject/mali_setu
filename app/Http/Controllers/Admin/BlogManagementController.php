<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BlogManagementController extends Controller
{
    /**
     * Display a listing of blogs.
     */
    public function index(Request $request)
    {
        $query = Blog::with(['category', 'user']);

        if ($request->has('search') && $request->search !== '') {
            $query->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        if ($request->has('status') && $request->status !== '') {
            $query->where('is_active', $request->status);
        }

        $blogs = $query->latest()->paginate(20);

        $stats = [
            'total' => Blog::count(),
            'active' => Blog::where('is_active', true)->count(),
            'inactive' => Blog::where('is_active', false)->count(),
        ];

        return view('admin.blogs.index', compact('blogs', 'stats'));
    }

    /**
     * Display the blog access user listing.
     */
    public function accessUsers(Request $request)
    {
        $query = 
            \App\Models\User::query();

        if ($request->has('search') && $request->search !== '') {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->latest()->paginate(20);

        return view('admin.blogs.access', compact('users'));
    }

    /**
     * Toggle blog creation access for a user.
     */
    public function toggleUserAccess($id)
    {
        try {
            $user = \App\Models\User::findOrFail($id);
            $user->blog_access = !$user->blog_access;
            $user->save();

            return redirect()->back()->with('success', 'Blog access updated for ' . $user->name);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update blog access: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new blog.
     */
    public function create()
    {
        $categories = \App\Models\BlogCategory::active()->get();
        return view('admin.blogs.create', compact('categories'));
    }

    /**
     * Store a newly created blog in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'blog_type' => 'required|exists:blog_categories,id',
                'description' => 'nullable|string|max:5000',
                'tags' => 'nullable|string',
                'media' => 'nullable|array',
                'media.*' => 'file|mimes:jpg,jpeg,png,gif,mp4,mov,avi,webm|max:10240',
                'is_active' => 'boolean',
            ]);

            $tags = null;
            if ($request->filled('tags')) {
                $tags = array_filter(array_map('trim', explode(',', $request->tags)));
            }

            $mediaPaths = [];
            $mediaType = 'image';

            if ($request->hasFile('media')) {
                foreach ($request->file('media') as $file) {
                    $path = $file->store('blogs/media', 'public');
                    $mediaPaths[] = $path;
                    $mime = $file->getMimeType();
                    if (str_starts_with($mime, 'video/')) {
                        $mediaType = 'video';
                    }
                }
            }

            Blog::create([
                'user_id' => auth()->id(),
                'title' => $request->title,
                'blog_type' => $request->blog_type,
                'description' => $request->description,
                'tags' => $tags,
                'media_path' => $mediaPaths,
                'media_type' => $mediaType,
                'is_active' => $request->has('is_active') ? $request->is_active : true,
            ]);

            return redirect()->route('admin.blogs.index')->with('success', 'Blog created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create blog: ' . $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        $blog = Blog::with(['comments' => function($q) {
            $q->whereNull('parent_id')->with(['user', 'replies.user'])->latest();
        }])->findOrFail($id);
        
        $categories = \App\Models\BlogCategory::active()->get();

        return view('admin.blogs.edit', compact('blog', 'categories'));
    }

    /**
     * Update the specified blog in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $blog = Blog::findOrFail($id);

            $request->validate([
                'title' => 'required|string|max:255',
                'blog_type' => 'required|exists:blog_categories,id',
                'description' => 'nullable|string|max:5000',
                'tags' => 'nullable|string',
                'media' => 'nullable|array',
                'media.*' => 'file|mimes:jpg,jpeg,png,gif,mp4,mov,avi,webm|max:10240',
                'is_active' => 'boolean',
            ]);

            $tags = null;
            if ($request->filled('tags')) {
                $tags = array_filter(array_map('trim', explode(',', $request->tags)));
            }

            if ($request->hasFile('media')) {
                // Remove old media files if exists
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
                foreach ($request->file('media') as $file) {
                    $path = $file->store('blogs/media', 'public');
                    $mediaPaths[] = $path;
                    $mime = $file->getMimeType();
                    if (str_starts_with($mime, 'video/')) {
                        $mediaType = 'video';
                    }
                }
                $blog->media_path = $mediaPaths;
                $blog->media_type = $mediaType;
            }

            $blog->title = $request->title;
            $blog->blog_type = $request->blog_type;
            $blog->description = $request->description;
            $blog->tags = $tags;
            $blog->is_active = $request->has('is_active') ? $request->is_active : false;
            $blog->save();

            return redirect()->route('admin.blogs.index')->with('success', 'Blog updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update blog: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified blog from storage.
     */
    public function destroy($id)
    {
        try {
            $blog = Blog::findOrFail($id);
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

            return redirect()->route('admin.blogs.index')->with('success', 'Blog deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete blog: ' . $e->getMessage());
        }
    }

    /**
     * Toggle blog active status.
     */
    public function toggleStatus($id)
    {
        try {
            $blog = Blog::findOrFail($id);
            $blog->is_active = !$blog->is_active;
            $blog->save();

            return redirect()->back()->with('success', 'Blog status updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update status: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified comment from a blog (Admin action).
     */
    public function deleteComment($id)
    {
        try {
            $comment = \App\Models\BlogComment::findOrFail($id);
            $comment->delete();

            return redirect()->back()->with('success', 'Comment deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete comment: ' . $e->getMessage());
        }
    }
}
