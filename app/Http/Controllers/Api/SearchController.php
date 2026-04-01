<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\MatrimonyProfile;
use App\Models\JobPosting;
use App\Models\VolunteerOpportunity;
use App\Models\VolunteerProfile;
use App\Models\DonationCause;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    /**
     * 🔎 Global search across all modules
     */
    public function globalSearch(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'query' => 'required|string|min:2|max:255',
            'size' => 'sometimes|integer|min:1|max:100',
            'from' => 'sometimes|integer|min:0',
            'modules' => 'sometimes|array',
            'modules.*' => 'in:businesses,matrimony,jobs,volunteers,donations'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $query = $request->input('query');
            $size = $request->input('size', 20);
            $from = $request->input('from', 0);
            $modules = $request->input('modules', []);

            $results = [];

            if (empty($modules) || in_array('businesses', $modules)) {
                $results['businesses'] = Business::where('business_name', 'like', "%$query%")
                    ->orWhere('description', 'like', "%$query%")
                    ->skip($from)->take($size)->get();
            }

            if (empty($modules) || in_array('matrimony', $modules)) {
                $results['matrimony'] = MatrimonyProfile::where('name', 'like', "%$query%")
                    ->orWhere('caste', 'like', "%$query%")
                    ->orWhere('education', 'like', "%$query%")
                    ->skip($from)->take($size)->get();
            }

            if (empty($modules) || in_array('jobs', $modules)) {
                $results['jobs'] = JobPosting::where('title', 'like', "%$query%")
                    ->orWhere('description', 'like', "%$query%")
                    ->orWhere('skills_required', 'like', "%$query%")
                    ->skip($from)->take($size)->get();
            }

            if (empty($modules) || in_array('volunteers', $modules)) {
                $results['volunteers'] = VolunteerOpportunity::where('title', 'like', "%$query%")
                    ->orWhere('description', 'like', "%$query%")
                    ->orWhere('required_skills', 'like', "%$query%")
                    ->skip($from)->take($size)->get();
            }

            if (empty($modules) || in_array('donations', $modules)) {
                $results['donations'] = DonationCause::where('title', 'like', "%$query%")
                    ->orWhere('description', 'like', "%$query%")
                    ->orWhere('category', 'like', "%$query%")
                    ->skip($from)->take($size)->get();
            }

            return response()->json(['success' => true, 'data' => $results]);

        } catch (\Exception $e) {
            Log::error('Global search failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Search failed'], 500);
        }
    }

    /**
     * 🔎 Search Businesses
     */
    public function searchBusinesses(Request $request)
    {
        $query = Business::query();

        if ($request->filled('query')) {
            $query->where(function($q) use ($request) {
                $q->where('business_name', 'like', "%{$request->input('query')}%")
                  ->orWhere('description', 'like', "%{$request->input('query')}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('location')) {
            $query->where('location', 'like', "%{$request->location}%");
        }

        if ($request->boolean('verified_only')) {
            $query->where('verification_status', 'approved');
        }

        $results = $query->latest()->paginate($request->size ?? 20);

        return response()->json(['success' => true, 'data' => $results]);
    }

    /**
     * 🔎 Search Matrimony Profiles
     */
    public function searchMatrimony_oldd(Request $request)
    {
        $query = MatrimonyProfile::query();
        
        //Basic Details
        if ($request->filled('age_min') || $request->filled('age_max')) {
            $query->whereBetween('age', [$request->age_min ?? 18, $request->age_max ?? 100]);
        }

        if ($request->filled('marital_status')) {
            $query->whereJsonContains('personal_details->marital_status', $request->marital_status);
        }

        if ($request->filled('profile_created_by')) {
            $query->whereJsonContains('personal_details->profile_created_by', $request->profile_created_by);
        }

        if ($request->filled('language')) {
            $query->whereJsonContains('personal_details->language', $request->language);
        }
        
        if ($request->filled('personal_details')) {
            $query->whereJsonContains('personal_details->name', $request->name);
        }

        if ($request->filled('height')) {
            $query->where('height', $request->height);
        }

        if ($request->filled('physical_status')) {
            $query->where('physical_status', $request->physical_status);
        }

        //Profesional details
        if ($request->filled('annual_income')) {
            $query->whereBetween('personal_details->annual_income', [0, $request->annual_income]);
        }

        if ($request->filled('education')) {
            $query->whereJsonContains('education_details->highest_qualification', $request->education);
        }

        if ($request->filled('employment_type')) {
            $query->whereJsonContains('personal_details->employment_type', $request->employment_type);
        }

        //Family Details
        if ($request->filled('family_status')) {
            $query->whereJsonContains('family_details->family_class', $request->family_status);
        }

        if ($request->filled('family_type')) {
            $query->whereJsonContains('personal_details->family_type', $request->family_type);
        }

        if ($request->filled('family_value')) {
            $query->whereJsonContains('family_details->family_value', $request->family_value);
        }

        //Location Details

        if ($request->filled('state')) {
            $query->whereRaw(
                "LOWER(JSON_UNQUOTE(JSON_EXTRACT(location_details, '$.state'))) = ?",
                [strtolower($request->state)]
            );
        }

        if ($request->filled('country')) {
            $query->whereRaw(
                "LOWER(JSON_UNQUOTE(JSON_EXTRACT(location_details, '$.country'))) = ?",
                [strtolower($request->country)]
            );
        }

        if ($request->filled('city')) {
            $query->whereRaw(
                "LOWER(JSON_UNQUOTE(JSON_EXTRACT(location_details, '$.city'))) = ?",
                [strtolower($request->city)]
            );
        }

        if ($request->filled('citizenship')) {
            $query->whereJsonContains('personal_details->citizenship', $request->citizenship);
        }

        //Lifestyle Details
        if ($request->filled('diet')) {
            $query->whereJsonContains('lifestyle_details->diet', $request->diet);
        }

        if ($request->filled('smoking')) {
            $query->whereJsonContains('lifestyle_details->smoking', $request->smoking);
        }

        if ($request->filled('drinking')) {
            $query->whereJsonContains('lifestyle_details->drinking', $request->drinking);
        }

        //Profile type
        // if ($request->filled('photo')) {
        //      $query->where('photo', '!=', '');
        // }

        //Recently created profile
        if ($request->filled('created_at')) {
            switch ($request->created_at) {
                case 'all':
                    // no filter applied
                    break;

                case 'today':
                    $query->whereDate('created_at', today());
                    break;

                case 'last_7_days':
                    $query->where('created_at', '>=', now()->subDays(7));
                    break;

                case 'last_30_days':
                    $query->where('created_at', '>=', now()->subDays(30));
                    break;

                case 'one_week':
                    $query->whereBetween('created_at', [now()->subWeek(), now()]);
                    break;

                case 'one_month':
                    $query->whereBetween('created_at', [now()->subMonth(), now()]);
                    break;
            }
        }
        
        $authUserId = $request->user()->id;

        // foreach (['location', 'caste', 'education', 'occupation', 'gender'] as $field) {
        //     if ($request->filled($field)) {
        //         $query->where($field, $request->$field);
        //     }
        // }
        
        $query->where('user_id', '!=', $authUserId);

        $query->where('approval_status', 'approved');

        $results = $query->latest()->paginate($request->size ?? 20);

        $filtered = $results->getCollection()->filter(function ($usr) use ($authUserId) {
        
            $connection = DB::table('connection_requests')
                ->where(function ($q) use ($authUserId, $usr) {
                    $q->where('sender_id', $authUserId)
                      ->where('receiver_id', $usr->user_id);
                })
                ->orWhere(function ($q) use ($authUserId, $usr) {
                    $q->where('sender_id', $usr->user_id)
                      ->where('receiver_id', $authUserId);
                })
                ->orderBy('id', 'desc')
                ->first();
        
            if ($connection) {
                $usr->connection_status = $connection->status;
        
                // ❌ remove user if removed
                return $connection->status !== 'removed';
            }
        
            $usr->connection_status = 'not_connected';
            return true;
        });
        
        // ✅ reindex + set back to paginator
        $results->setCollection($filtered->values());


        return response()->json(['success' => true, 'data' => $results]);
    }
    
    public function searchMatrimony(Request $request)
    {
        $query = MatrimonyProfile::query();
    
        /*
        |--------------------------------------------------------------------------
        | BASIC DETAILS
        |--------------------------------------------------------------------------
        */
    
        if ($request->filled('age_min') || $request->filled('age_max')) {
    
            $min = $request->age_min ?? 18;
            $max = $request->age_max ?? 100;
    
            $query->whereBetween('age', [$min, $max]);
        }
    
        /*
    NAME SEARCH
    */

    if ($request->filled('name')) {

        $query->whereRaw(
            "LOWER(JSON_UNQUOTE(JSON_EXTRACT(personal_details, '$.name'))) LIKE ?",
            ['%' . strtolower(trim($request->name)) . '%']
        );
    }

    /*
    AGE FILTER
    */

    if ($request->filled('age_min') || $request->filled('age_max')) {

        $query->whereBetween(
            'age',
            [
                $request->age_min ?? 18,
                $request->age_max ?? 100
            ]
        );
    }

    /*
    REQUIRED FILTERS
    */

    $authUserId = $request->user()->id;

    $query->where('user_id', '!=', $authUserId);

    $query->where(
        'approval_status',
        'approved'
    );

    return response()->json([
        'success' => true,
        'data' => $query->latest()->paginate(20)
    ]);
}

    /**
     * 🔎 Search Jobs
     */
    public function searchJobs(Request $request)
    {
        $query = JobPosting::where('status', 'active');

        if ($request->filled('query')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', "%{$request->input('query')}%")
                  ->orWhere('description', 'like', "%{$request->input('query')}%")
                  ->orWhere('skills_required', 'like', "%{$request->input('query')}%");
            });
        }

        foreach (['location', 'category', 'experience_level', 'employment_type'] as $field) {
            if ($request->filled($field)) {
                $query->where($field, $request->$field);
            }
        }

        if ($request->filled('salary_min') || $request->filled('salary_max')) {
            $query->whereBetween('salary_max', [$request->salary_min ?? 0, $request->salary_max ?? 9999999]);
        }

        $results = $query->latest()->paginate($request->size ?? 20);

        return response()->json(['success' => true, 'data' => $results]);
    }

    /**
     * 🔎 Search Volunteers
     */
    public function searchVolunteers(Request $request)
    {
        $query = VolunteerOpportunity::where('status', 'active');

        if ($request->filled('query')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', "%{$request->input('query')}%")
                  ->orWhere('description', 'like', "%{$request->input('query')}%")
                  ->orWhere('required_skills', 'like', "%{$request->input('query')}%");
            });
        }

        if ($request->filled('location')) {
            $query->where('location', 'like', "%{$request->location}%");
        }

        if ($request->filled('skills')) {
            $query->where('required_skills', 'like', "%{$request->skills}%");
        }

        if ($request->filled('start_date') || $request->filled('end_date')) {
            $query->whereBetween('start_date', [$request->start_date ?? now(), $request->end_date ?? now()->addYears(10)]);
        }

        $results = $query->orderBy('start_date')->paginate($request->size ?? 20);

        return response()->json(['success' => true, 'data' => $results]);
    }

    /**
     * 🔎 Search Volunteer Profiles
     */
    public function searchVolunteerProfiles(Request $request)
    {
        $query = VolunteerProfile::with('user')->where('status', 'active');

        if ($request->filled('query')) {
            $search = $request->input('query');
            $query->where(function ($q) use ($search) {
                $q->where('skills', 'like', "%{$search}%")
                    ->orWhere('experience', 'like', "%{$search}%")
                    ->orWhere('availability', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('bio', 'like', "%{$search}%")
                    ->orWhere('interests', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('location')) {
            $query->where('location', 'like', "%{$request->location}%");
        }

        if ($request->filled('experience')) {
            $query->where('experience', 'like', "%{$request->experience}%");
        }

        if ($request->filled('availability')) {
            $query->where('availability', 'like', "%{$request->availability}%");
        }

        $results = $query->latest()->paginate($request->size ?? 20);

        return response()->json(['success' => true, 'data' => $results]);
    }

    /**
     * 🔎 Search Donations
     */
    public function searchDonations(Request $request)
    {
        $query = DonationCause::query();

        if ($request->filled('query')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', "%{$request->input('query')}%")
                  ->orWhere('description', 'like', "%{$request->input('query')}%")
                  ->orWhere('category', 'like', "%{$request->input('query')}%");
            });
        }

        foreach (['category', 'location', 'urgency'] as $field) {
            if ($request->filled($field)) {
                $query->where($field, $request->$field);
            }
        }

        if ($request->boolean('active_only')) {
            $query->where('status', 'active')->where('end_date', '>=', now());
        }

        $results = $query->latest()->paginate($request->size ?? 20);

        return response()->json(['success' => true, 'data' => $results]);
    }

    /**
     * 🔎 Suggestions (basic autocomplete)
     */
    public function getSuggestions(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'query' => 'required|string|min:1|max:100',
            'modules' => 'sometimes|array',
            'modules.*' => 'in:businesses,matrimony,jobs,volunteers,donations',
            'size' => 'sometimes|integer|min:1|max:20'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $query = $request->input('query');
        $size = $request->input('size', 10);
        $modules = $request->input('modules', []);

        $suggestions = [];

        if (empty($modules) || in_array('businesses', $modules)) {
            $suggestions['businesses'] = Business::where('business_name', 'like', "$query%")->limit($size)->pluck('business_name');
        }

        if (empty($modules) || in_array('jobs', $modules)) {
            $suggestions['jobs'] = JobPosting::where('title', 'like', "$query%")->limit($size)->pluck('title');
        }

        if (empty($modules) || in_array('donations', $modules)) {
            $suggestions['donations'] = DonationCause::where('title', 'like', "$query%")->limit($size)->pluck('title');
        }

        return response()->json(['success' => true, 'data' => $suggestions]);
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
                      ->orWhere('description', 'like', '%' . $searchQuery . '%')
                      ->orWhere('country', 'like', '%' . $searchQuery . '%')
                      ->orWhere('state', 'like', '%' . $searchQuery . '%')
                      ->orWhere('district', 'like', '%' . $searchQuery . '%')
                      ->orWhere('taluka', 'like', '%' . $searchQuery . '%')
                      ->orWhere('city', 'like', '%' . $searchQuery . '%')
                      ->orWhere('pincode', 'like', '%' . $searchQuery . '%');
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
}
