<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BusinessCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    /**
     * Get all businesse categorys with pagination
     */
    public function index(Request $request)
    {
        $query = BusinessCategory::where('is_active', '1');

        $businessesCat = $query->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $businessesCat
        ]);
    }

    /**
     * Register a new business
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Handle photo upload
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('category/photos', 'public');
        }

        $businessCategory = BusinessCategory::create([
            'name' => $request->business_name,
            'description' => $request->description,
            'photo' => $photoPath,  // save single path directly
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Business Category registered successfully',
            'data' => ['business_category' => $businessCategory]
        ], 201);
    }

    /**
     * Get single business details
     */
    public function show($id)
    {
        $businessCat = BusinessCategory::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => ['business_category' => $businessCat]
        ]);
    }

    /**
     * Update business
     */
    public function update(Request $request, $id)
    {
        $businessCategory = BusinessCategory::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Handle photo upload
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('category/photos', 'public');
        }

        $data = $request->only(['business_name', 'description']);

        // Add photos if any
        if (!empty($photoPath)) {
            $data['photo'] = $photoPath;
        }

        $businessCategory->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Business category updated successfully',
            'data' => ['business_category' => $businessCategory]
        ]);
    }

    /**
     * Delete business
     */
    public function destroy(Request $request, $id)
    {
        $businessCategory = BusinessCategory::findOrFail($id);

        $businessCategory->delete();

        return response()->json([
            'success' => true,
            'message' => 'Business category deleted successfully'
        ]);
    }
}