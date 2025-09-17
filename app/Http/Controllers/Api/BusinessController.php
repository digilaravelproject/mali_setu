<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class BusinessController extends Controller
{
    /**
     * Register a new business
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_name' => 'required|string|max:255',
            'business_type' => 'required|in:product,service',
            'category_id' => 'required|integer|exists:business_categories,id',
            'description' => 'required|string',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email',
            'website' => 'nullable|url',
            'photos' => 'nullable|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Handle photo uploads
        $photoPaths = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $photoPaths[] = $photo->store('business/photos', 'public');
            }
        }

        $business = Business::create([
            'user_id' => $request->user()->id,
            'business_name' => $request->business_name,
            'business_type' => $request->business_type,
            'category_id' => $request->category_id,
            'description' => $request->description,
            'contact_phone' => $request->contact_phone,
            'contact_email' => $request->contact_email,
            'website' => $request->website,
            'verification_status' => 'pending',
            'subscription_status' => 'trial',
        ]);

        // Store photo paths if any
        if (!empty($photoPaths)) {
            $business->update([
                'photos' => $photoPaths   // directly store array
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Business registered successfully',
            'data' => ['business' => $business]
        ], 201);
    }

    /**
     * Get user's businesses
     */
    public function getUserBusinesses(Request $request)
    {
        $businesses = Business::where('user_id', $request->user()->id)
            ->with(['products', 'services', 'category'])
            ->get();

        return response()->json([
            'success' => true,
            'data' => ['businesses' => $businesses]
        ]);
    }

    /**
     * Get all businesses with pagination
     */
    public function index(Request $request)
    {
        $query = Business::with(['user', 'category', 'products', 'services'])
            ->where('verification_status', 'approved');

        // Apply filters
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('business_type')) {
            $query->where('business_type', $request->business_type);
        }

        if ($request->has('search')) {
            $query->where('business_name', 'like', '%' . $request->search . '%');
        }

        $businesses = $query->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $businesses
        ]);
    }

    /**
     * Get single business details
     */
    public function show($id)
    {
        $business = Business::with(['user', 'category', 'products', 'services', 'reviews.user'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => ['business' => $business]
        ]);
    }

    /**
     * Update business
     */
    public function update(Request $request, $id)
    {
        $business = Business::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'business_name' => 'sometimes|string|max:255',
            'business_type' => 'sometimes|in:product,service',
            'category_id' => 'sometimes|integer|exists:business_categories,id',
            'description' => 'sometimes|string',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email',
            'website' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $business->update($request->only([
            'business_name', 'business_type', 'category_id', 'description',
            'contact_phone', 'contact_email', 'website'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Business updated successfully',
            'data' => ['business' => $business]
        ]);
    }

    /**
     * Add product to business
     */
    public function addProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_id' => 'required|integer|exists:businesses,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'cost' => 'required|numeric|min:0',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verify business ownership
        $business = Business::where('id', $request->business_id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $product = Product::create([
            'business_id' => $request->business_id,
            'name' => $request->name,
            'description' => $request->description,
            'cost' => $request->cost,
            'image_path' => $imagePath,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product added successfully',
            'data' => ['product' => $product]
        ], 201);
    }

    /**
     * Add service to business
     */
    public function addService(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_id' => 'required|integer|exists:businesses,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'cost' => 'required|numeric|min:0',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verify business ownership
        $business = Business::where('id', $request->business_id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('services', 'public');
        }

        $service = Service::create([
            'business_id' => $request->business_id,
            'name' => $request->name,
            'description' => $request->description,
            'cost' => $request->cost,
            'image_path' => $imagePath,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Service added successfully',
            'data' => ['service' => $service]
        ], 201);
    }

    /**
     * Get business products
     */
    public function getProducts($businessId)
    {
        $products = Product::where('business_id', $businessId)
            ->where('status', 'active')
            ->get();

        return response()->json([
            'success' => true,
            'data' => ['products' => $products]
        ]);
    }

    /**
     * Get business services
     */
    public function getServices($businessId)
    {
        $services = Service::where('business_id', $businessId)
            ->where('status', 'active')
            ->get();

        return response()->json([
            'success' => true,
            'data' => ['services' => $services]
        ]);
    }

    /**
     * Delete business
     */
    public function destroy(Request $request, $id)
    {
        $business = Business::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $business->delete();

        return response()->json([
            'success' => true,
            'message' => 'Business deleted successfully'
        ]);
    }
}