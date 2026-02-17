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
        $categories = BusinessCategory::latest()->paginate(20);
        
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
}