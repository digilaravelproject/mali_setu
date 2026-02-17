<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VolunteerOpportunity;
use App\Models\VolunteerApplication;
use App\Models\VolunteerProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VolunteerController extends Controller
{
    /**
     * Get all volunteer opportunities with filters
     */
    public function getOpportunities(Request $request)
    {
        $query = VolunteerOpportunity::query();

        // Apply filters
        if ($request->has('location') && $request->location) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        if ($request->has('skills') && $request->skills) {
            $skills = is_array($request->skills) ? $request->skills : explode(',', $request->skills);
            $query->where(function ($q) use ($skills) {
                foreach ($skills as $skill) {
                    $q->orWhereJsonContains('required_skills', trim($skill));
                }
            });
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('start_date') && $request->start_date) {
            $query->where('start_date', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->where('end_date', '<=', $request->end_date);
        }

        // // Only show active opportunities that are accepting applications by default
        // if (!$request->has('include_inactive')) {
        //     $query->where('status', 'active')
        //           ->where('start_date', '>', now())
        //           ->whereRaw('volunteers_registered < volunteers_needed');
        // }

        $opportunities = $query->with('applications')
                              ->orderBy('start_date', 'asc')
                              ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $opportunities
        ]);
    }

    /**
     * Get a specific volunteer opportunity
     */
    public function getOpportunity($id)
    {
        $opportunity = VolunteerOpportunity::with(['applications.volunteerProfile'])->find($id);

        if (!$opportunity) {
            return response()->json([
                'success' => false,
                'message' => 'Volunteer opportunity not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $opportunity
        ]);
    }

    /**
     * Create a new volunteer opportunity (for organizations/admins)
     */
    public function createOpportunity(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'organization' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'required_skills' => 'nullable|array',
            'start_date' => 'required|date|after:today',
            'end_date' => 'required|date|after:start_date',
            'volunteers_needed' => 'required|integer|min:1',
            'contact_person' => 'required|string|max:255',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'requirements' => 'nullable|string',
            'time_commitment' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $opportunity = VolunteerOpportunity::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Volunteer opportunity created successfully',
            'data' => $opportunity
        ], 201);
    }

    /**
     * Update a volunteer opportunity
     */
    public function updateOpportunity(Request $request, $id)
    {
        $opportunity = VolunteerOpportunity::find($id);

        if (!$opportunity) {
            return response()->json([
                'success' => false,
                'message' => 'Volunteer opportunity not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'organization' => 'sometimes|string|max:255',
            'location' => 'sometimes|string|max:255',
            'required_skills' => 'sometimes|array',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after:start_date',
            'volunteers_needed' => 'sometimes|integer|min:1',
            'status' => 'sometimes|in:active,inactive,completed,cancelled',
            'contact_person' => 'sometimes|string|max:255',
            'contact_email' => 'sometimes|email|max:255',
            'contact_phone' => 'sometimes|string|max:20',
            'requirements' => 'sometimes|string',
            'time_commitment' => 'sometimes|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $opportunity->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Volunteer opportunity updated successfully',
            'data' => $opportunity
        ]);
    }

    /**
     * Delete a volunteer opportunity
     */
    public function deleteOpportunity($id)
    {
        $opportunity = VolunteerOpportunity::find($id);

        if (!$opportunity) {
            return response()->json([
                'success' => false,
                'message' => 'Volunteer opportunity not found'
            ], 404);
        }

        $opportunity->delete();

        return response()->json([
            'success' => true,
            'message' => 'Volunteer opportunity deleted successfully'
        ]);
    }

    /**
     * Apply for a volunteer opportunity
     */
    public function applyForOpportunity(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'volunteer_opportunity_id' => 'required|exists:volunteer_opportunities,id',
            'message' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();
        
        // Check if user has a volunteer profile
        $volunteerProfile = $user->volunteer;
        if (!$volunteerProfile) {
            return response()->json([
                'success' => false,
                'message' => 'You need to create a volunteer profile first'
            ], 400);
        }

        $opportunity = VolunteerOpportunity::find($request->volunteer_opportunity_id);
        
        // Check if opportunity is accepting applications
        if (!$opportunity->isAcceptingApplications()) {
            return response()->json([
                'success' => false,
                'message' => 'This opportunity is not accepting applications'
            ], 400);
        }

        // Check if user already applied
        $existingApplication = VolunteerApplication::where('volunteer_profile_id', $volunteerProfile->id)
            ->where('volunteer_opportunity_id', $request->volunteer_opportunity_id)
            ->first();

        if ($existingApplication) {
            return response()->json([
                'success' => false,
                'message' => 'You have already applied for this opportunity'
            ], 400);
        }

        $application = VolunteerApplication::create([
            'volunteer_profile_id' => $volunteerProfile->id,
            'volunteer_opportunity_id' => $request->volunteer_opportunity_id,
            'message' => $request->message,
            'applied_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Application submitted successfully',
            'data' => $application->load(['volunteerOpportunity', 'volunteerProfile'])
        ], 201);
    }

    /**
     * Get user's volunteer applications
     */
    public function getMyApplications(Request $request)
    {
        $user = Auth::user();
        $volunteerProfile = $user->volunteer;

        if (!$volunteerProfile) {
            return response()->json([
                'success' => false,
                'message' => 'You need to create a volunteer profile first'
            ], 400);
        }

        $query = VolunteerApplication::where('volunteer_profile_id', $volunteerProfile->id)
            ->with(['volunteerOpportunity']);

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $applications = $query->orderBy('applied_at', 'desc')
                             ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $applications
        ]);
    }

    /**
     * Withdraw an application
     */
    public function withdrawApplication($id)
    {
        $user = Auth::user();
        $volunteerProfile = $user->volunteer;

        if (!$volunteerProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Volunteer profile not found'
            ], 400);
        }

        $application = VolunteerApplication::where('id', $id)
            ->where('volunteer_profile_id', $volunteerProfile->id)
            ->first();

        if (!$application) {
            return response()->json([
                'success' => false,
                'message' => 'Application not found'
            ], 404);
        }

        if ($application->status === 'withdrawn') {
            return response()->json([
                'success' => false,
                'message' => 'Application is already withdrawn'
            ], 400);
        }

        $application->withdraw();

        return response()->json([
            'success' => true,
            'message' => 'Application withdrawn successfully',
            'data' => $application
        ]);
    }

    /**
     * Get matched opportunities for a volunteer based on skills and location
     */
    public function getMatchedOpportunities(Request $request)
    {
        $user = Auth::user();
        $volunteerProfile = $user->volunteer;

        if (!$volunteerProfile) {
            return response()->json([
                'success' => false,
                'message' => 'You need to create a volunteer profile first'
            ], 400);
        }

        $query = VolunteerOpportunity::where('status', 'active')
            ->where('start_date', '>', now())
            ->whereRaw('volunteers_registered < volunteers_needed');

        // Match by location if volunteer has location
        if ($volunteerProfile->location) {
            $query->where('location', 'like', '%' . $volunteerProfile->location . '%');
        }

        // Match by skills if volunteer has skills
        if ($volunteerProfile->skills) {
            $volunteerSkills = is_array($volunteerProfile->skills) 
                ? $volunteerProfile->skills 
                : json_decode($volunteerProfile->skills, true);
            
            if ($volunteerSkills) {
                $query->where(function ($q) use ($volunteerSkills) {
                    foreach ($volunteerSkills as $skill) {
                        $q->orWhereJsonContains('required_skills', $skill);
                    }
                });
            }
        }

        // Exclude opportunities user already applied for
        $appliedOpportunityIds = VolunteerApplication::where('volunteer_profile_id', $volunteerProfile->id)
            ->pluck('volunteer_opportunity_id')
            ->toArray();

        if (!empty($appliedOpportunityIds)) {
            $query->whereNotIn('id', $appliedOpportunityIds);
        }

        $opportunities = $query->orderBy('start_date', 'asc')
                              ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $opportunities
        ]);
    }

    /**
     * Get volunteer profile
     */
    public function getVolunteerProfile(Request $request)
    {
        $user = Auth::user();
        $volunteerProfile = $user->volunteer;

        if (!$volunteerProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Volunteer profile not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $volunteerProfile
        ]);
    }

    /**
     * Create or update volunteer profile
     */

    public function updateVolunteerProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'skills'       => 'nullable|array',     // coming as array from UI
            'experience'   => 'nullable|string',
            'availability' => 'nullable|string',
            'location'     => 'nullable|string|max:255',
            'bio'          => 'nullable|string|max:1000',
            'interests'    => 'nullable|array',     // will be JSON-encoded by cast
            'status'       => 'nullable|in:active,inactive,pending',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors()
            ], 422);
        }
    
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }
    
        $data = $validator->validated();
    
        // Convert skills array -> CSV string because DB column is varchar(255)
        if (array_key_exists('skills', $data)) {
            if (is_array($data['skills'])) {
                // Trim values and implode; keep within 255 if needed
                $csv = implode(',', array_filter(array_map('trim', $data['skills']), fn($v) => $v !== ''));
                // Optional: enforce length
                $data['skills'] = mb_substr($csv, 0, 255);
            } elseif ($data['skills'] === null) {
                $data['skills'] = null;
            }
        }
    
        // interests stays as array; your model casts it to array and will JSON encode to longtext
        // If frontend sometimes sends string, you can normalize:
        if (array_key_exists('interests', $data) && is_string($data['interests'])) {
            $decoded = json_decode($data['interests'], true);
            $data['interests'] = is_array($decoded) ? $decoded : [];
        }
    
        $volunteerProfile = $user->volunteer;
    
        if ($volunteerProfile) {
            $volunteerProfile->update($data);
            $message = 'Volunteer profile updated successfully';
        } else {
            $volunteerProfile = VolunteerProfile::create(array_merge(
                $data,
                ['user_id' => $user->id, 'status' => $data['status'] ?? 'pending']
            ));
            $message = 'Volunteer profile created successfully';
        }
    
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $volunteerProfile->fresh()
        ]);
    }
}
