<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use Illuminate\Http\Request;

class BlogCategoryManagementController extends Controller
{
    /**
     * Display a listing of the blog categories.
     */
    public function index(Request $request)
    {
        $query = BlogCategory::query();

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }

        $categories = $query->latest()->paginate(15);

        return view('admin.blog-categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new blog category.
     */
    public function create()
    {
        return view('admin.blog-categories.create');
    }

    /**
     * Store a newly created blog category in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:blog_categories,name',
            'description' => 'nullable|string|max:1000',
        ]);

        try {
            BlogCategory::create([
                'name' => $request->name,
                'description' => $request->description,
                'is_active' => true,
            ]);

            return redirect()->route('admin.blog-categories.index')
                ->with('success', 'Blog category created successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to create category: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified blog category and its associated bloggers.
     */
    public function show($id)
    {
        $category = BlogCategory::findOrFail($id);
        
        // Fetch bloggers associated with this category
        $bloggers = $category->bloggers()->latest()->paginate(10);

        return view('admin.blog-categories.show', compact('category', 'bloggers'));
    }

    /**
     * Show the form for editing the specified blog category.
     */
    public function edit($id)
    {
        $category = BlogCategory::findOrFail($id);
        return view('admin.blog-categories.edit', compact('category'));
    }

    /**
     * Update the specified blog category in storage.
     */
    public function update(Request $request, $id)
    {
        $category = BlogCategory::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:blog_categories,name,' . $category->id,
            'description' => 'nullable|string|max:1000',
        ]);

        try {
            $category->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            return redirect()->route('admin.blog-categories.index')
                ->with('success', 'Blog category updated successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update category: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Toggle the active/inactive status of the blog category.
     */
    public function toggleStatus(Request $request, $id)
    {
        try {
            $category = BlogCategory::findOrFail($id);
            $newStatus = !$category->is_active;
            
            $category->update([
                'is_active' => $newStatus
            ]);

            $statusText = $newStatus ? 'activated' : 'deactivated';

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'is_active' => $newStatus,
                    'message' => "Category '{$category->name}' {$statusText} successfully!"
                ]);
            }

            return redirect()->back()->with('success', "Category '{$category->name}' {$statusText} successfully!");
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update category status: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to update category status.');
        }
    }

    /**
     * Remove the specified blog category from storage.
     */
    public function destroy(Request $request, $id)
    {
        try {
            $category = BlogCategory::findOrFail($id);
            
            // Nullify reference on associated users first
            $category->bloggers()->update(['blog_category_id' => null]);
            
            $category->delete();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Category '{$category->name}' deleted successfully!"
                ]);
            }

            return redirect()->route('admin.blog-categories.index')
                ->with('success', "Category '{$category->name}' deleted successfully!");
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete category: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to delete category.');
        }
    }
}
