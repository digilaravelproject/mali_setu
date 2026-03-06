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
        $query = Blog::query();

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
     * Show the form for creating a new blog.
     */
    public function create()
    {
        return view('admin.blogs.create');
    }

    /**
     * Store a newly created blog in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string|max:5000',
                'tags' => 'nullable|string',
                'media' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,mov,avi,webm',
                'is_active' => 'boolean',
            ]);

            $tags = null;
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
                'user_id' => auth()->id(),
                'title' => $request->title,
                'description' => $request->description,
                'tags' => $tags,
                'media_path' => $mediaPath,
                'media_type' => $mediaType,
                'is_active' => $request->has('is_active') ? $request->is_active : true,
            ]);

            return redirect()->route('admin.blogs.index')->with('success', 'Blog created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create blog: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show the form for editing the specified blog.
     */
    public function edit($id)
    {
        $blog = Blog::findOrFail($id);
        return view('admin.blogs.edit', compact('blog'));
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
                'description' => 'nullable|string|max:5000',
                'tags' => 'nullable|string',
                'media' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,mov,avi,webm',
                'is_active' => 'boolean',
            ]);

            $tags = null;
            if ($request->filled('tags')) {
                $tags = array_filter(array_map('trim', explode(',', $request->tags)));
            }

            if ($request->hasFile('media')) {
                // Remove old media if exists
                if ($blog->media_path && Storage::disk('public')->exists($blog->media_path)) {
                    Storage::disk('public')->delete($blog->media_path);
                }

                $file = $request->file('media');
                $blog->media_path = $file->store('blogs/media', 'public');
                $mime = $file->getMimeType();
                $blog->media_type = str_starts_with($mime, 'video/') ? 'video' : 'image';
            }

            $blog->title = $request->title;
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
            if ($blog->media_path && Storage::disk('public')->exists($blog->media_path)) {
                Storage::disk('public')->delete($blog->media_path);
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
}
