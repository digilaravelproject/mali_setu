<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessCategory;
use Illuminate\Http\Request;

class CategoryManagementController extends Controller
{
    /**
     * Display all businesses
     */
    public function index(Request $request)
    {
        $categories = BusinessCategory::orderBy('sort_order', 'asc')->orderBy('id', 'asc')->paginate(20);
        
        return view('admin.category.index', compact('categories'));
    }

    /**
     * Add category form
     */
    public function addcategory()
    {
        return view('admin.category.add');
    }

    /**
     * Create new category
     */
    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:business_categories',
            'description' => 'required|string|max:500',
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        
        // Handle photo upload
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('category/photos', 'public');
        }

        $businessCategory = BusinessCategory::create([
            'name' => $request->name,
            'description' => $request->description,
            'photo' => $photoPath,  // save single path directly
        ]);
        
        return redirect()->back()->with('success', 'Category created successfully!');
    }

    /**
     * Activate Category
     */
    public function active(Request $request, $categoryId)
    {
        try {
            $category = BusinessCategory::findOrFail($categoryId);
            
            $category->update([
                'is_active' => 1
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Category activated successfully!'
                ]);
            }
            
            return redirect()->back()->with('success', 'Category activated successfully!');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to category activated: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to category activated.');
        }
    }

    /**
     * Inactivate Category
     */
    public function inactive(Request $request, $categoryId)
    {
        try {
            $category = BusinessCategory::findOrFail($categoryId);
            
            $category->update([
                'is_active' => 0
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Category inactivated successfully!'
                ]);
            }
            
            return redirect()->back()->with('success', 'Category inactivated successfully!');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to category inactivated: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to category inactivated.');
        }
    }
    
    /**
     * Delete business
     */
    public function destroy(Request $request, $id)
    {
        try {
            $category = BusinessCategory::findOrFail($id);
            $category->delete();
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Category deleted successfully!'
                ]);
            }
            
            return redirect()->back()->with('success', 'Category deleted successfully!');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete Category: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to delete Category.');
        }
    }

    /**
     * Show edit category form
     */
    public function edit($id)
    {
        $category = BusinessCategory::findOrFail($id);
        return view('admin.category.edit', compact('category'));
    }

    /**
     * Update category
     */
    public function update(Request $request, $id)
    {
        $category = BusinessCategory::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:business_categories,name,' . $id,
            'description' => 'required|string|max:500',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'description' => $request->description,
        ];

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if it exists
            if ($category->photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($category->photo)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($category->photo);
            }
            $data['photo'] = $request->file('photo')->store('category/photos', 'public');
        }

        $category->update($data);

        return redirect()->route('admin.category.index')->with('success', 'Category updated successfully!');
    }

    /**
     * Reorder categories via AJAX
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'exists:business_categories,id',
            'page' => 'required|integer|min:1',
            'per_page' => 'required|integer|min:1',
        ]);

        $order = $request->order;
        $page = $request->page;
        $perPage = $request->per_page;

        $startPosition = ($page - 1) * $perPage;

        foreach ($order as $index => $id) {
            BusinessCategory::where('id', $id)->update([
                'sort_order' => $startPosition + $index + 1
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Category order updated successfully!'
        ]);
    }

    /**
     * Move category to specific page and position
     */
    public function moveToPage(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:business_categories,id',
            'target_page' => 'required|integer|min:1',
            'position' => 'required|in:top,bottom',
        ]);

        $categoryId = $request->category_id;
        $targetPage = $request->target_page;
        $position = $request->position;
        $perPage = 20;

        // Get all categories ordered by sort_order asc, id asc
        $allCategories = BusinessCategory::orderBy('sort_order', 'asc')->orderBy('id', 'asc')->get();
        
        $totalCategories = $allCategories->count();
        $totalPages = ceil($totalCategories / $perPage);

        if ($targetPage > $totalPages) {
            $targetPage = $totalPages;
        }

        // Find the index of the category to move
        $targetItem = null;
        $filtered = [];
        foreach ($allCategories as $cat) {
            if ($cat->id == $categoryId) {
                $targetItem = $cat;
            } else {
                $filtered[] = $cat;
            }
        }

        if (!$targetItem) {
            return redirect()->back()->with('error', 'Category not found.');
        }

        // Calculate insertion index
        if ($position === 'top') {
            $insertIndex = ($targetPage - 1) * $perPage;
        } else {
            $insertIndex = ($targetPage * $perPage) - 1;
        }

        if ($insertIndex < 0) {
            $insertIndex = 0;
        }
        if ($insertIndex > count($filtered)) {
            $insertIndex = count($filtered);
        }

        array_splice($filtered, $insertIndex, 0, [$targetItem]);

        // Save new sequential order
        foreach ($filtered as $index => $cat) {
            $cat->sort_order = $index + 1;
            $cat->save();
        }

        return redirect()->route('admin.category.index', ['page' => $targetPage])
            ->with('success', 'Category "' . $targetItem->name . '" moved to page ' . $targetPage . ' (' . $position . ') successfully!');
    }
}