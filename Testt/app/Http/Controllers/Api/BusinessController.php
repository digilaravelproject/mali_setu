<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Product;
use App\Models\Service;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class BusinessController extends Controller
{
    /**
     * @var \App\Services\NotificationService
     */
    protected $notifications;

    public function __construct(NotificationService $notifications)
    {
        $this->notifications = $notifications;
    }
    /**
     * Register a new business
     */
    public function register(Request $request)
    {
        $user = $request->user();
        
        // if (Business::where('user_id', $request->user()->id)->exists()) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'You already have a business profile.',
        //         'errors'  => ['business' => ['A user can create only one business.']]
        //     ], 409);
        // }
        
        $validator = Validator::make($request->all(), [
            'business_name' => 'required|string|max:255',
            'business_type' => 'required',
            'category_id' => 'required|integer|exists:business_categories,id',
            'description' => 'required|string',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email',
            'website' => 'nullable|url',
            'photos' => 'nullable|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            'opening_time' => 'nullable|date_format:H:i',
            'closing_time' => 'nullable|date_format:H:i',
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
            'opening_time' => $request->opening_time,
            'closing_time' => $request->closing_time,
            'verification_status' => 'pending',
            'subscription_status' => 'trial',
        ]);

        // Store photo paths if any
        if (!empty($photoPaths)) {
            $business->update([
                'photos' => $photoPaths   // directly store array
            ]);
        }
        
        if (empty($user->user_type) || $user->user_type != 'business') {
            $user->update([
                'user_type' => 'business'
            ]);
        }

        // Email: business registered
        $this->notifications->createNotification(
            $user->id,
            Notification::TYPE_BUSINESS_VERIFIED,
            'Business registered',
            'Your business "' . $business->business_name . '" has been registered and is pending verification.',
            ['business_id' => $business->id],
            '/business/manage',
            Notification::PRIORITY_MEDIUM,
            $business,
            ['in_app', 'email']
        );

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
    public function show_old($id)
    {
        $business = Business::with([
            'user','category','products','services','reviews.user'
        ])->find($id);
        
        if (!$business) {
            return response()->json(['message' => 'Business not found'], 404);
        }
        
        // $business = Business::with(['user', 'category', 'products', 'services', 'reviews.user'])
        //     ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => ['business' => $business]
        ]);
    }

    public function show($id)
    {
        try {
            // Validate ID (optional but recommended)
            if (!is_numeric($id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid business ID',
                    'errors'  => ['id' => ['Business ID must be numeric']]
                ], 422);
            }
    
            // Fetch business with relationships
            $business = Business::with([
                'user',
                'category',
                'products',
                'services',
                'reviews.user'
            ])->find($id);
    
            // Business not found
            if (!$business) {
                return response()->json([
                    'success' => false,
                    'message' => 'Business not found'
                ], 404);
            }
    
            // Success response
            return response()->json([
                'success' => true,
                'message' => 'Business fetched successfully',
                'data' => [
                    'business' => $business
                ]
            ], 200);
    
        } catch (\Throwable $e) {
    
            // Log error for debugging
            \Log::error('Business show API error', [
                'business_id' => $id,
                'error' => $e->getMessage()
            ]);
    
            // Server error response
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again later.'
            ], 500);
        }
    }

    /**
     * Search businesses by name or description
     */
    public function searchBusiness(Request $request)
    {
        try {
            // Validate search string
            $validator = Validator::make($request->all(), [
                'search' => 'required|string|min:1|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $searchQuery = $request->input('search');

            // Search businesses by name or description
            $businesses = Business::with([
                'user',
                'category',
                'products',
                'services',
                'reviews.user'
            ])
            ->where('verification_status', 'approved')
            ->where(function ($query) use ($searchQuery) {
                $query->where('business_name', 'like', '%' . $searchQuery . '%')
                      ->orWhere('description', 'like', '%' . $searchQuery . '%');
            })
            ->get();

            // No businesses found
            if ($businesses->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No businesses found matching your search'
                ], 404);
            }

            // Success response
            return response()->json([
                'success' => true,
                'message' => 'Businesses found successfully',
                'data' => [
                    'businesses' => $businesses,
                    'count' => $businesses->count()
                ]
            ], 200);

        } catch (\Throwable $e) {

            // Log error for debugging
            \Log::error('Business search API error', [
                'search_query' => $request->input('search'),
                'error' => $e->getMessage()
            ]);

            // Server error response
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again later.'
            ], 500);
        }
    }
    
    public function showOnCategory($cat_id)
    {
        try {
            // Validate category ID
            if (!is_numeric($cat_id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid category ID',
                ], 422);
            }
    
            // Fetch businesses by category_id
            $businesses = Business::with([
                'user',
                'category',
                'products',
                'services',
                'reviews.user'
            ])
            ->where('category_id', $cat_id)
            ->get();
    
            // No business found
            if ($businesses->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No businesses found for this category'
                ], 404);
            }
    
            // Success response
            return response()->json([
                'success' => true,
                'message' => 'Businesses fetched successfully',
                'data' => [
                    'businesses' => $businesses
                ]
            ], 200);
    
        } catch (\Throwable $e) {
    
            \Log::error('Business category API error', [
                'category_id' => $cat_id,
                'error' => $e->getMessage()
            ]);
    
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again later.'
            ], 500);
        }
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
            'business_type' => 'sometimes',
            'category_id' => 'sometimes|integer|exists:business_categories,id',
            'description' => 'sometimes|string',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email',
            'website' => 'nullable|url',
            'opening_time' => 'sometimes|date_format:H:i',
            'closing_time' => 'sometimes|date_format:H:i',
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
            , 'opening_time', 'closing_time'
        ]));

        // Email: business updated
        $this->notifications->createNotification(
            $request->user()->id,
            Notification::TYPE_PROFILE_UPDATE,
            'Business updated',
            'Your business "' . $business->business_name . '" details have been updated.',
            ['business_id' => $business->id],
            '/business/manage',
            Notification::PRIORITY_LOW,
            $business,
            ['in_app', 'email']
        );

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
            'cost' => 'nullable|numeric|min:0',
            // 'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
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

        // Email: product added
        $this->notifications->createNotification(
            $request->user()->id,
            Notification::TYPE_BUSINESS_VERIFIED,
            'New product added',
            'A new product "' . $product->name . '" has been added to your business "' . $business->business_name . '".',
            [
                'business_id' => $business->id,
                'product_id' => $product->id,
            ],
            '/business/manage',
            Notification::PRIORITY_LOW,
            $product,
            ['in_app', 'email']
        );

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
            'cost' => 'nullable|numeric|min:0',
            // 'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
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

        // Email: service added
        $this->notifications->createNotification(
            $request->user()->id,
            Notification::TYPE_BUSINESS_VERIFIED,
            'New service added',
            'A new service "' . $service->name . '" has been added to your business "' . $business->business_name . '".',
            [
                'business_id' => $business->id,
                'service_id' => $service->id,
            ],
            '/business/manage',
            Notification::PRIORITY_LOW,
            $service,
            ['in_app', 'email']
        );

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