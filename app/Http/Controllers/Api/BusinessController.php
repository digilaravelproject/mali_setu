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
            
            // 👇 NEW FIELDS
            'country' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'taluka' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:500',
            'city' => 'required|string|max:100',
            'pincode' => 'required|digits:6',

            'website' => 'nullable|url',
            'photos' => 'nullable|array',
            'photos.*' => 'string',
            'opening_time' => 'nullable|date_format:H:i',
            'closing_time' => 'nullable|date_format:H:i',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);
        
        if ($validator->fails()) {
            $errors = $validator->errors();
        
            return response()->json([
                'success' => false,
                'message' => $errors->first(), // 👈 get first error message
                'errors' => $errors
            ], 422);
        }

        if ($validator->fails()) {
            $errors = $validator->errors();
        
            return response()->json([
                'success' => false,
                'message' => $errors->first(), // 👈 get first error message
                'errors' => $errors
            ], 422);
        }

        // Handle photo uploads
        // $photoPaths = [];
        // if ($request->hasFile('photos')) {
        //     foreach ($request->file('photos') as $photo) {
        //         $photoPaths[] = $photo->store('business/photos', 'public');
        //     }
        // }
        
        $photoPaths = [];

        if ($request->photos) {
            foreach ($request->photos as $base64Image) {
        
                if (str_contains($base64Image, 'base64,')) {
                    $base64Image = explode('base64,', $base64Image)[1];
                }
        
                $imageData = base64_decode($base64Image);
        
                $fileName = 'business/photos/' . uniqid() . '.jpg';
        
                Storage::disk('public')->put($fileName, $imageData);
        
                $photoPaths[] = $fileName;
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
            
            // 👇 NEW FIELDS
            'country' => $request->country,
            'state' => $request->state,
            'district' => $request->district,
            'taluka' => $request->taluka,
            'address' => $request->address,
            'city' => $request->city,
            'pincode' => $request->pincode,
    
            'website' => $request->website,
            'opening_time' => $request->opening_time,
            'closing_time' => $request->closing_time,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'verification_status' => 'pending',
            'subscription_status' => 'trial',
        ]);

        // Store photo paths if any
        if (!empty($photoPaths)) {
            $impPhoto = implode(', ', $photoPaths);
            // echo'<pre>';print_r($photoPaths);die;
            $business->update([
                'photo' => $impPhoto
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
    public function index_oldd(Request $request)
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
     * Get all businesses with pagination and distance filter
     */
    public function index_5KM(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric|min:-90|max:90',
            'longitude' => 'required|numeric|min:-180|max:180',
            'radius' => 'nullable|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $userLatitude = $request->latitude;
        $userLongitude = $request->longitude;
        $radiusKm = $request->input('radius', 5);

        $distanceSql = "(
            6371 * acos(
                cos(radians(?)) *
                cos(radians(latitude)) *
                cos(radians(longitude) - radians(?)) +
                sin(radians(?)) *
                sin(radians(latitude))
            )
        )";

        $query = Business::with([
                'user',
                'category',
                'products',
                'services'
            ])
            ->selectRaw("businesses.*, {$distanceSql} AS distance", [
                $userLatitude,
                $userLongitude,
                $userLatitude
            ])
            ->where('verification_status', 'approved')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude');

        /*
        |--------------------------------------------------------------------------
        | Apply Filters
        |--------------------------------------------------------------------------
        */

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('business_type')) {
            $query->where('business_type', $request->business_type);
        }

        if ($request->has('search')) {
            $query->where('business_name', 'like', '%' . $request->search . '%');
        }

        /*
        |--------------------------------------------------------------------------
        | Radius Filter
        |--------------------------------------------------------------------------
        */

        $query->havingRaw('distance <= ?', [$radiusKm])
            ->orderBy('distance', 'asc');

        $businesses = $query->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $businesses
        ]);
    }
    
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'latitude'  => 'required|numeric|min:-90|max:90',
            'longitude' => 'required|numeric|min:-180|max:180',
            'radius'    => 'nullable|numeric|min:0|max:100',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors()
            ], 422);
        }
    
        $userLatitude  = $request->latitude;
        $userLongitude = $request->longitude;
    
        /*
        |--------------------------------------------------------------------------
        | Distance Formula (Haversine)
        |--------------------------------------------------------------------------
        */
    
        $distanceSql = "(
            6371 * acos(
                cos(radians(?)) *
                cos(radians(latitude)) *
                cos(radians(longitude) - radians(?)) +
                sin(radians(?)) *
                sin(radians(latitude))
            )
        )";
    
        /*
        |--------------------------------------------------------------------------
        | Base Query
        |--------------------------------------------------------------------------
        */
    
        $query = Business::with([
                'user',
                'category',
                'products',
                'services'
            ])
            ->selectRaw("
                businesses.*,
                {$distanceSql} AS KMfromuser
            ", [
                $userLatitude,
                $userLongitude,
                $userLatitude
            ])
            ->where('verification_status', 'approved')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude');
    
        /*
        |--------------------------------------------------------------------------
        | Optional Filters
        |--------------------------------------------------------------------------
        */
    
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
    
        if ($request->filled('business_type')) {
            $query->where('business_type', $request->business_type);
        }
    
        if ($request->filled('search')) {
            $query->where('business_name', 'like', '%' . $request->search . '%');
        }
    
        /*
        |--------------------------------------------------------------------------
        | Optional Radius Filter (only if provided)
        |--------------------------------------------------------------------------
        */
    
        if ($request->filled('radius')) {
            $query->havingRaw('KMfromuser <= ?', [$request->radius]);
        }
    
        /*
        |--------------------------------------------------------------------------
        | Sort by Distance (Nearest → Farthest)
        |--------------------------------------------------------------------------
        */
    
        $query->orderBy('KMfromuser', 'asc');
    
        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */
    
        $businesses = $query->paginate(10);
    
        /*
        |--------------------------------------------------------------------------
        | Return Response
        |--------------------------------------------------------------------------
        */
    
        return response()->json([
            'success' => true,
            'data'    => $businesses
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
    
    public function showOnCategory_old($cat_id)
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

    public function showOnCategory_5KM(Request $request, $cat_id)
    {
        try {
            // Validate inputs
            $validator = Validator::make(
                array_merge($request->all(), ['cat_id' => $cat_id]),
                [
                    'cat_id' => 'required|numeric',
                    'latitude' => 'required|numeric|min:-90|max:90',
                    'longitude' => 'required|numeric|min:-180|max:180',
                    'radius' => 'nullable|numeric|min:0|max:100',
                ]
            );

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $userLatitude = $request->input('latitude');
            $userLongitude = $request->input('longitude');

            // Default radius = 5 KM
            $radiusKm = $request->input('radius', 5);

            // Haversine formula
            $distanceSql = "(
                6371 * acos(
                    cos(radians(?)) *
                    cos(radians(latitude)) *
                    cos(radians(longitude) - radians(?)) +
                    sin(radians(?)) *
                    sin(radians(latitude))
                )
            )";

            $businesses = Business::with([
                'user',
                'category',
                'products',
                'services',
                'reviews.user'
            ])
            ->selectRaw("businesses.*, {$distanceSql} AS distance", [
                $userLatitude,
                $userLongitude,
                $userLatitude
            ])
            ->where('category_id', $cat_id)
            ->where('verification_status', 'approved')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')

            // Filter within radius
            ->havingRaw('distance <= ?', [$radiusKm])

            ->orderBy('distance', 'asc')

            ->get();

            if ($businesses->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No businesses found within ' . $radiusKm . ' km for this category'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Businesses fetched successfully',
                'data' => [
                    'businesses' => $businesses,
                    'count' => $businesses->count(),
                    'radius_km' => $radiusKm
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
    
    public function showOnCategory(Request $request, $cat_id)
    {
        try {
            // Validate inputs
            $validator = Validator::make(
                array_merge($request->all(), ['cat_id' => $cat_id]),
                [
                    'cat_id' => 'required|numeric',
                    'latitude' => 'required|numeric|min:-90|max:90',
                    'longitude' => 'required|numeric|min:-180|max:180',
                ]
            );
    
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
    
            $userLatitude = $request->input('latitude');
            $userLongitude = $request->input('longitude');
    
            // Haversine formula
            $distanceSql = "(
                6371 * acos(
                    cos(radians(?)) *
                    cos(radians(latitude)) *
                    cos(radians(longitude) - radians(?)) +
                    sin(radians(?)) *
                    sin(radians(latitude))
                )
            )";
    
            $businesses = Business::with([
                    'user',
                    'category',
                    'products',
                    'services',
                    'reviews.user'
                ])
                ->selectRaw("businesses.*, {$distanceSql} AS KMfromuser", [
                    $userLatitude,
                    $userLongitude,
                    $userLatitude
                ])
                ->where('category_id', $cat_id)
                ->where('verification_status', 'approved')
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
    
                // Sort nearest to farthest
                ->orderBy('KMfromuser', 'asc')
    
                ->get();
    
            if ($businesses->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No businesses found for this category'
                ], 404);
            }
    
            // Optional: Round KM value
            $businesses->transform(function ($business) {
                $business->KMfromuser = round($business->KMfromuser, 2);
                return $business;
            });
    
            return response()->json([
                'success' => true,
                'message' => 'Businesses fetched successfully',
                'data' => [
                    'businesses' => $businesses,
                    'count' => $businesses->count()
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
    public function update_old(Request $request, $id)
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
            'photos' => 'nullable|array',
            'photos.*' => 'string',
            'opening_time' => 'sometimes|date_format:H:i',
            'closing_time' => 'sometimes|date_format:H:i',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
        
            return response()->json([
                'success' => false,
                'message' => $errors->first(), // 👈 get first error message
                'errors' => $errors
            ], 422);
        }
        
        $photoPaths = [];

        // Get existing photos
        $existingPhotos = $business->photo ? explode(',', $business->photo) : [];
        
        if ($request->photos) {
        
            foreach ($request->photos as $base64Image) {
        
                if (str_contains($base64Image, 'base64,')) {
                    $base64Image = explode('base64,', $base64Image)[1];
                }
        
                $imageData = base64_decode($base64Image);
        
                $fileName = 'business/photos/' . uniqid() . '.jpg';
        
                Storage::disk('public')->put($fileName, $imageData);
        
                $photoPaths[] = $fileName;
            }
        
            // OPTIONAL: delete old photos
            foreach ($existingPhotos as $oldPhoto) {
                if (Storage::disk('public')->exists(trim($oldPhoto))) {
                    Storage::disk('public')->delete(trim($oldPhoto));
                }
            }
        
            // Merge or replace (choose one)
            $finalPhotos = $photoPaths; // replace
            // $finalPhotos = array_merge($existingPhotos, $photoPaths); // append instead
        
            $business->update([
                'photo' => implode(',', $finalPhotos)
            ]);
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

            // NEW ADDRESS FIELDS
            'country' => 'sometimes|string|max:100',
            'state' => 'sometimes|string|max:100',
            'district' => 'sometimes|string|max:100',
            'taluka' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:500',
            'city' => 'sometimes|string|max:100',
            'pincode' => 'sometimes|digits:6',

            'website' => 'nullable|url',

            'photos' => 'nullable|array',
            'photos.*' => 'string',

            'opening_time' => 'sometimes|date_format:H:i',
            'closing_time' => 'sometimes|date_format:H:i',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            return response()->json([
                'success' => false,
                'message' => $errors->first(),
                'errors' => $errors
            ], 422);
        }

        $photoPaths = [];

        // Existing photos
        $existingPhotos = $business->photo
            ? explode(',', $business->photo)
            : [];

        if ($request->photos) {

            foreach ($request->photos as $base64Image) {

                if (str_contains($base64Image, 'base64,')) {
                    $base64Image = explode('base64,', $base64Image)[1];
                }

                $imageData = base64_decode($base64Image);

                $fileName = 'business/photos/' . uniqid() . '.jpg';

                Storage::disk('public')->put($fileName, $imageData);

                $photoPaths[] = $fileName;
            }

            // Delete old photos (optional)
            foreach ($existingPhotos as $oldPhoto) {
                if (Storage::disk('public')->exists(trim($oldPhoto))) {
                    Storage::disk('public')->delete(trim($oldPhoto));
                }
            }

            $business->update([
                'photo' => implode(',', $photoPaths)
            ]);
        }

        // Update all fields
        $business->update($request->only([
            'business_name',
            'business_type',
            'category_id',
            'description',
            'contact_phone',
            'contact_email',

            'country',
            'state',
            'district',
            'taluka',
            'address',
            'city',
            'pincode',

            'website',
            'opening_time',
            'closing_time',
            'latitude',
            'longitude'
        ]));

        // Notification
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
            'data' => [
                'business' => $business->fresh()
            ]
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
        
            return response()->json([
                'success' => false,
                'message' => $errors->first(), // 👈 get first error message
                'errors' => $errors
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
        
            return response()->json([
                'success' => false,
                'message' => $errors->first(), // 👈 get first error message
                'errors' => $errors
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