<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JobPosting;
use App\Models\JobApplication;
use App\Models\Business;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class JobController extends Controller
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
     * Get all active job postings with filters
     */
    public function index(Request $request)
    {
        $query = JobPosting::with(['business', 'business.user'])
            // ->active()
            ->latest();
            
        if ($request->filled('business_id')) {
            $query->where('business_id', $request->business_id);
        }

        // Apply filters
        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        if ($request->filled('location')) {
            $query->byLocation($request->location);
        }

        if ($request->filled('experience_level')) {
            $query->byExperienceLevel($request->experience_level);
        }

        if ($request->filled('employment_type')) {
            $query->byEmploymentType($request->employment_type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('requirements', 'like', "%{$search}%");
            });
        }
        
        // $query->where('status', 'approved');

        $jobs = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $jobs,
            'filters' => [
                'categories' => JobPosting::distinct()->pluck('category')->filter(),
                'locations' => JobPosting::distinct()->pluck('location')->filter(),
                'experience_levels' => ['entry', 'junior', 'mid', 'senior', 'executive'],
                'employment_types' => ['full-time', 'part-time', 'contract', 'freelance', 'internship']
            ]
        ]);
    }

    /**
     * Get a specific job posting
     */
    public function show($id)
    {
        $job = JobPosting::with(['business', 'business.user', 'applications'])
            ->findOrFail($id);

        // Check if current user has applied (if authenticated)
        $hasApplied = false;
        if (Auth::check()) {
            $hasApplied = $job->hasUserApplied(Auth::id());
        }

        return response()->json([
            'success' => true,
            'data' => [
                'job' => $job,
                'has_applied' => $hasApplied,
                'similar_jobs' => JobPosting::with(['business'])
                    ->active()
                    ->where('id', '!=', $id)
                    ->where('category', $job->category)
                    ->limit(5)
                    ->get()
            ]
        ]);
    }

    /**
     * Create a new job posting (Business users only)
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Check if user is a business user with verified business
        if ($user->user_type !== 'business') {
            return response()->json([
                'success' => false,
                'message' => 'Only business users can create job postings'
            ], 403);
        }

        $business = $user->business;
        
        if (!$business || $business->verification_status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Your business must be verified to post jobs'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'requirements' => 'required|string',
            'salary_range' => 'nullable|string|max:100',
            'job_type' => 'required|string|max:50',
            'location' => 'required|string|max:255',
            'experience_level' => 'required|in:entry,junior,mid,senior,executive',
            'employment_type' => 'required|in:full-time,part-time,contract,freelance,internship',
            'category' => 'required|string|max:100',
            'skills_required' => 'nullable|array',
            'benefits' => 'nullable|array',
            'application_deadline' => 'nullable|date|after:today',
            'expires_at' => 'required|date|after:today'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $jobPosting = JobPosting::create([
            'business_id' => $business->id,
            'title' => $request->title,
            'description' => $request->description,
            'requirements' => $request->requirements,
            'salary_range' => $request->salary_range,
            'job_type' => $request->job_type,
            'location' => $request->location,
            'experience_level' => $request->experience_level,
            'employment_type' => $request->employment_type,
            'category' => $request->category,
            'skills_required' => $request->skills_required,
            'benefits' => $request->benefits,
            'application_deadline' => $request->application_deadline,
            'expires_at' => $request->expires_at,
            'is_active' => true,
            'status' => 'pending' // Requires admin approval
        ]);

        // Email: job created
        $this->notifications->createNotification(
            $user->id,
            Notification::TYPE_JOB_APPLICATION_STATUS,
            'Job posting created',
            'Your job "' . $jobPosting->title . '" has been created and is pending admin approval.',
            ['job_id' => $jobPosting->id],
            '/jobs',
            Notification::PRIORITY_MEDIUM,
            $jobPosting,
            ['in_app', 'email']
        );

        return response()->json([
            'success' => true,
            'message' => 'Job posting created successfully and is pending approval',
            'data' => $jobPosting->load('business')
        ], 201);
    }

    /**
     * Update a job posting
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $jobPosting = JobPosting::findOrFail($id);

        // Check if user owns this job posting
        if ($jobPosting->business->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to update this job posting'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'requirements' => 'sometimes|string',
            'salary_range' => 'nullable|string|max:100',
            'job_type' => 'sometimes|string|max:50',
            'location' => 'sometimes|string|max:255',
            'experience_level' => 'sometimes|in:entry,junior,mid,senior,executive',
            'employment_type' => 'sometimes|in:full-time,part-time,contract,freelance,internship',
            'category' => 'sometimes|string|max:100',
            'skills_required' => 'nullable|array',
            'benefits' => 'nullable|array',
            'application_deadline' => 'nullable|date|after:today',
            'expires_at' => 'sometimes|date|after:today'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $jobPosting->update($request->only([
            'title', 'description', 'requirements', 'salary_range', 'job_type',
            'location', 'experience_level', 'employment_type', 'category',
            'skills_required', 'benefits', 'application_deadline', 'expires_at'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Job posting updated successfully',
            'data' => $jobPosting->load('business')
        ]);
    }

    /**
     * Delete a job posting
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $jobPosting = JobPosting::findOrFail($id);

        // Check if user owns this job posting
        if ($jobPosting->business->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to delete this job posting'
            ], 403);
        }

        $jobPosting->delete();

        return response()->json([
            'success' => true,
            'message' => 'Job posting deleted successfully'
        ]);
    }

    /**
     * Get job postings for the authenticated business user
     */
    public function getUserJobs(Request $request)
    {
        $user = Auth::user();
        
        if ($user->user_type !== 'business' || !$user->business) {
            return response()->json([
                'success' => false,
                'message' => 'No business profile found'
            ], 404);
        }

        $jobs = JobPosting::with(['applications'])
            ->where('business_id', $user->business->id)
            ->latest()
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $jobs
        ]);
    }

    /**
     * Apply for a job
     */
    public function apply(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'job_posting_id' => 'required|exists:job_postings,id',
            'cover_letter' => 'required|string|max:2000',
            'resume' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,gif,bmp,webp,svg|max:5120',// 5MB max
            'additional_info' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $jobPosting = JobPosting::findOrFail($request->job_posting_id);

        // Check if job is still accepting applications
        if (!$jobPosting->isAcceptingApplications()) {
            return response()->json([
                'success' => false,
                'message' => 'This job is no longer accepting applications'
            ], 400);
        }

        // Check if user has already applied
        if ($jobPosting->hasUserApplied($user->id)) {
            return response()->json([
                'success' => false,
                'message' => 'You have already applied for this job'
            ], 400);
        }

        // Upload resume
        $resumePath = null;
        if ($request->hasFile('resume')) {
            $resumePath = $request->file('resume')->store('resumes', 'public');
        }

        $application = JobApplication::create([
            'user_id' => $user->id,
            'job_posting_id' => $request->job_posting_id,
            'cover_letter' => $request->cover_letter,
            'resume_url' => $resumePath,
            'additional_info' => $request->additional_info,
            'status' => 'pending',
            'applied_at' => now()
        ]);

        // Email: you applied on a job
        $this->notifications->createNotification(
            $user->id,
            Notification::TYPE_JOB_APPLICATION,
            'Job application submitted',
            'You have applied for "' . $jobPosting->title . '" at ' . optional($jobPosting->business)->business_name . '.',
            [
                'job_id' => $jobPosting->id,
                'application_id' => $application->id,
            ],
            '/jobs/applications',
            Notification::PRIORITY_MEDIUM,
            $application,
            ['in_app', 'email']
        );

        // Email: another user applied on our job (business owner)
        if ($jobPosting->business && $jobPosting->business->user_id) {
            $this->notifications->notifyJobApplication($jobPosting, $user);
        }

        return response()->json([
            'success' => true,
            'message' => 'Application submitted successfully',
            'data' => $application->load(['user', 'jobPosting'])
        ], 201);
    }

    /**
     * Get applications for a specific job (Business owner only)
     */
    public function getJobApplications(Request $request, $jobId)
    {
        $user = Auth::user();
        $jobPosting = JobPosting::findOrFail($jobId);

        // Check if user owns this job posting
        // if ($jobPosting->business->user_id !== $user->id) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Unauthorized to view applications for this job'
        //     ], 403);
        // }

        $applications = JobApplication::with(['user'])
            ->where('job_posting_id', $jobId)
            ->latest('applied_at')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => [
                'job' => $jobPosting,
                'applications' => $applications
            ]
        ]);
    }

    /**
     * Update application status (Business owner only)
     */
    public function updateApplicationStatus(Request $request, $applicationId)
    {
        $user = Auth::user();
        $application = JobApplication::with(['jobPosting.business'])->findOrFail($applicationId);

        // Check if user owns the job posting
        // if ($application->jobPosting->business->user_id !== $user->id) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Unauthorized to update this application'
        //     ], 403);
        // }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:reviewed,accepted,rejected',
            'employer_notes' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $application->update([
            'status' => $request->status,
            'employer_notes' => $request->employer_notes,
            'reviewed_at' => now()
        ]);

        // Email: job application accepted / rejected / reviewed (to applicant)
        $statusMessageMap = [
            'accepted' => 'Congratulations! Your application for "' . $application->jobPosting->title . '" has been accepted.',
            'rejected' => 'Your application for "' . $application->jobPosting->title . '" has been rejected.',
            'reviewed' => 'Your application for "' . $application->jobPosting->title . '" has been reviewed.',
        ];

        $this->notifications->createNotification(
            $application->user_id,
            Notification::TYPE_JOB_APPLICATION_STATUS,
            'Job application ' . $request->status,
            $statusMessageMap[$request->status] ?? 'Your job application status has been updated.',
            [
                'job_id' => $application->jobPosting->id,
                'application_id' => $application->id,
                'status' => $request->status,
            ],
            '/jobs/my-applications',
            Notification::PRIORITY_MEDIUM,
            $application,
            ['in_app', 'email']
        );

        return response()->json([
            'success' => true,
            'message' => 'Application status updated successfully',
            'data' => $application->load(['user', 'jobPosting'])
        ]);
    }

    /**
     * Get user's job applications
     */
    public function getUserApplications(Request $request)
    {
        $user = Auth::user();
        
        $applications = JobApplication::with(['jobPosting.business'])
            ->where('user_id', $user->id)
            ->latest('applied_at')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $applications
        ]);
    }

    /**
     * Get job posting analytics for business owner
     */
    public function getJobAnalytics(Request $request)
    {
        $user = Auth::user();
        
        // if ($user->user_type !== 'business' || !$user->business) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'No business profile found'
        //     ], 404);
        // }

        $businessId = $user->business->id;
        
        $analytics = [
            'total_jobs' => JobPosting::where('business_id', $businessId)->count(),
            'active_jobs' => JobPosting::where('business_id', $businessId)->active()->count(),
            'pending_jobs' => JobPosting::where('business_id', $businessId)->pendingApproval()->count(),
            'total_applications' => JobApplication::whereHas('jobPosting', function($q) use ($businessId) {
                $q->where('business_id', $businessId);
            })->count(),
            'pending_applications' => JobApplication::whereHas('jobPosting', function($q) use ($businessId) {
                $q->where('business_id', $businessId);
            })->where('status', 'pending')->count(),
            'accepted_applications' => JobApplication::whereHas('jobPosting', function($q) use ($businessId) {
                $q->where('business_id', $businessId);
            })->where('status', 'accepted')->count(),
            'recent_applications' => JobApplication::with(['user', 'jobPosting'])
                ->whereHas('jobPosting', function($q) use ($businessId) {
                    $q->where('business_id', $businessId);
                })
                ->latest('applied_at')
                ->limit(5)
                ->get()
        ];

        return response()->json([
            'success' => true,
            'data' => $analytics
        ]);
    }

    /**
     * Toggle job posting active status
     */
    public function toggleStatus($id)
    {
        $user = Auth::user();
        $jobPosting = JobPosting::findOrFail($id);

        // Check if user owns this job posting
        if ($jobPosting->business->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to modify this job posting'
            ], 403);
        }

        if ($jobPosting->is_active) {
            $jobPosting->deactivate();
            $message = 'Job posting deactivated successfully';
        } else {
            $jobPosting->activate();
            $message = 'Job posting activated successfully';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $jobPosting
        ]);
    }
}