<?php

namespace App\Http\Controllers;

use App\Models\VolunteerProfile;
use App\Models\VolunteerOpportunity;
use App\Models\VolunteerApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class VolunteerController extends Controller
{
    /**
     * Volunteer profile dashboard and user applications list.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $volunteer = $user->volunteer;
        
        $applications = [];
        $matchedOpportunities = [];

        if ($volunteer) {
            $applications = VolunteerApplication::where('volunteer_profile_id', $volunteer->id)
                ->with('volunteerOpportunity')
                ->latest()
                ->get();

            // Match by skills/location or fetch active ones
            $matchQuery = VolunteerOpportunity::where('status', 'active')
                ->where('start_date', '>', now())
                ->whereRaw('volunteers_registered < volunteers_needed');

            // Exclude already applied
            $appliedIds = $applications->pluck('volunteer_opportunity_id')->toArray();
            if (!empty($appliedIds)) {
                $matchQuery->whereNotIn('id', $appliedIds);
            }

            if ($volunteer->location) {
                $matchQuery->where('location', 'like', '%' . $volunteer->location . '%');
            }

            $matchedOpportunities = $matchQuery->latest()->take(3)->get();
        }

        return view('volunteer.index', compact('volunteer', 'applications', 'matchedOpportunities'));
    }

    /**
     * Register or update a volunteer profile.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'bio'          => 'nullable|string|max:1000',
            'experience'   => 'nullable|string|max:1000',
            'skills'       => 'required|array|min:1',
            'skills.*'     => 'string|max:50',
            'availability' => 'required|string|max:255',
            'location'     => 'required|string|max:255',
            'interests'    => 'required|array|min:1',
            'interests.*'  => 'string|max:50',
        ], [
            'skills.required' => 'Please select or add at least one skill.',
            'interests.required' => 'Please select at least one field of interest.',
        ]);

        $skillsArr = array_filter(array_map('trim', $request->skills));
        $skillsCsv = implode(',', $skillsArr);

        $interestsArr = array_filter(array_map('trim', $request->interests));

        $data = [
            'bio'          => $request->bio,
            'experience'   => $request->experience,
            'skills'       => mb_substr($skillsCsv, 0, 255),
            'availability' => $request->availability,
            'location'     => $request->location,
            'interests'    => $interestsArr,
        ];

        $volunteer = $user->volunteer;

        if ($volunteer) {
            $volunteer->update($data);
            $message = 'Your volunteer profile has been updated successfully!';
        } else {
            // Default new web volunteer to active so they can participate immediately
            $data['user_id'] = $user->id;
            $data['status'] = 'active';
            VolunteerProfile::create($data);
            $message = 'Congratulations! You are now registered as a volunteer.';
        }

        return redirect()->route('volunteers.index')->with('success', $message);
    }

    /**
     * Browse active volunteer opportunities with robust filters.
     */
    public function browse(Request $request)
    {
        $query = VolunteerOpportunity::where('status', 'active')
            ->where('start_date', '>', now());

        // Text query matching title, organization, or requirements
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('organization', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Location Filter
        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        // Skills Filter
        if ($request->filled('skill')) {
            $skill = $request->skill;
            $query->where(function ($q) use ($skill) {
                $q->whereJsonContains('required_skills', $skill);
            });
        }

        $opportunities = $query->latest('start_date')->paginate(12)->withQueryString();

        return view('volunteer.browse', compact('opportunities'));
    }

    /**
     * Show detailed volunteer opportunity card.
     */
    public function show($id)
    {
        $opportunity = VolunteerOpportunity::findOrFail($id);
        $user = Auth::user();
        $volunteer = $user->volunteer;
        
        $hasApplied = false;
        $application = null;

        if ($volunteer) {
            $application = VolunteerApplication::where('volunteer_profile_id', $volunteer->id)
                ->where('volunteer_opportunity_id', $id)
                ->first();
            $hasApplied = !is_null($application);
        }

        return view('volunteer.show', compact('opportunity', 'volunteer', 'hasApplied', 'application'));
    }

    /**
     * Submit application to volunteer for an opportunity.
     */
    public function apply(Request $request)
    {
        $request->validate([
            'volunteer_opportunity_id' => 'required|exists:volunteer_opportunities,id',
            'message' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();
        $volunteer = $user->volunteer;

        if (!$volunteer) {
            return redirect()->route('volunteers.index')
                ->with('error', 'You must register a volunteer profile before applying for opportunities.');
        }

        $opportunity = VolunteerOpportunity::findOrFail($request->volunteer_opportunity_id);

        if (!$opportunity->isAcceptingApplications()) {
            return back()->withErrors(['message' => 'This opportunity is no longer accepting applications or is full.']);
        }

        // Check duplicate applications
        $existing = VolunteerApplication::where('volunteer_profile_id', $volunteer->id)
            ->where('volunteer_opportunity_id', $opportunity->id)
            ->first();

        if ($existing) {
            return back()->withErrors(['message' => 'You have already applied for this volunteer opportunity.']);
        }

        VolunteerApplication::create([
            'volunteer_profile_id' => $volunteer->id,
            'volunteer_opportunity_id' => $opportunity->id,
            'message' => $request->message,
            'status' => 'pending',
            'applied_at' => now(),
        ]);

        return redirect()->route('volunteers.index')
            ->with('success', 'Your application to help has been submitted! The coordinator will review it shortly.');
    }

    /**
     * Withdraw a volunteer application.
     */
    public function withdraw($id)
    {
        $user = Auth::user();
        $volunteer = $user->volunteer;

        if (!$volunteer) {
            return redirect()->route('volunteers.index')->with('error', 'Volunteer profile not found.');
        }

        $application = VolunteerApplication::where('id', $id)
            ->where('volunteer_profile_id', $volunteer->id)
            ->firstOrFail();

        if ($application->status === 'withdrawn') {
            return back()->with('error', 'This application is already withdrawn.');
        }

        $application->withdraw();

        return redirect()->route('volunteers.index')
            ->with('success', 'You have successfully withdrawn your application.');
    }
}
