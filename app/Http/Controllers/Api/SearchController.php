<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\MatrimonyProfile;
use App\Models\JobPosting;
use App\Models\VolunteerOpportunity;
use App\Models\DonationCause;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

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
                $q->where('business_name', 'like', "%{$request->query}%")
                  ->orWhere('description', 'like', "%{$request->query}%");
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
    public function searchMatrimony(Request $request)
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
        if ($request->filled('country')) {
            $query->whereJsonContains('location_details->country', $request->country);
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
        if ($request->filled('photo')) {
             $query->where('photo', '!=', '');
        }

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

        // foreach (['location', 'caste', 'education', 'occupation', 'gender'] as $field) {
        //     if ($request->filled($field)) {
        //         $query->where($field, $request->$field);
        //     }
        // }

        $query->where('approval_status', 'approved');

        $results = $query->latest()->paginate($request->size ?? 20);

        return response()->json(['success' => true, 'data' => $results]);
    }

    /**
     * 🔎 Search Jobs
     */
    public function searchJobs(Request $request)
    {
        $query = JobPosting::where('status', 'active');

        if ($request->filled('query')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', "%{$request->query}%")
                  ->orWhere('description', 'like', "%{$request->query}%")
                  ->orWhere('skills_required', 'like', "%{$request->query}%");
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
                $q->where('title', 'like', "%{$request->query}%")
                  ->orWhere('description', 'like', "%{$request->query}%")
                  ->orWhere('required_skills', 'like', "%{$request->query}%");
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
     * 🔎 Search Donations
     */
    public function searchDonations(Request $request)
    {
        $query = DonationCause::query();

        if ($request->filled('query')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', "%{$request->query}%")
                  ->orWhere('description', 'like', "%{$request->query}%")
                  ->orWhere('category', 'like', "%{$request->query}%");
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
}
