<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\JobApplication;
use App\Models\JobPosting;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class JobManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = JobPosting::with('business')->withCount('applications');

        $query->when($request->filled('search'), function ($query) use ($request) {
            $term = $request->string('search')->trim();
            $query->where(function ($query) use ($term) {
                $query->where('title', 'like', "%{$term}%")
                    ->orWhere('location', 'like', "%{$term}%")
                    ->orWhereHas('business', fn ($business) => $business->where('business_name', 'like', "%{$term}%"));
            });
        })->when($request->filled('category'), fn ($query) => $query->where('category', $request->category))
            ->when($request->filled('location'), fn ($query) => $query->where('location', 'like', '%'.$request->location.'%'))
            ->when($request->filled('employment_type'), fn ($query) => $query->where('employment_type', $request->employment_type))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->status));

        return view('admin.jobs.index', [
            'jobs' => $query->latest()->paginate(15)->withQueryString(),
            'categories' => JobPosting::whereNotNull('category')->distinct()->orderBy('category')->pluck('category'),
            'locations' => JobPosting::whereNotNull('location')->distinct()->orderBy('location')->pluck('location'),
        ]);
    }

    public function create()
    {
        return view('admin.jobs.form', ['job' => new JobPosting, 'businesses' => $this->businesses()]);
    }

    public function store(Request $request)
    {
        JobPosting::create($this->validated($request));

        return redirect()->route('admin.jobs.index')->with('success', 'Job created successfully.');
    }

    public function edit(JobPosting $job)
    {
        return view('admin.jobs.form', ['job' => $job, 'businesses' => $this->businesses()]);
    }

    public function update(Request $request, JobPosting $job)
    {
        $job->update($this->validated($request));

        return redirect()->route('admin.jobs.index')->with('success', 'Job updated successfully.');
    }

    public function toggle(JobPosting $job)
    {
        $job->update(['is_active' => ! $job->is_active]);

        return back()->with('success', 'Job visibility updated.');
    }

    public function destroy(JobPosting $job)
    {
        $job->delete();

        return back()->with('success', 'Job deleted successfully.');
    }

    public function applications(Request $request)
    {
        $query = JobApplication::with(['user', 'jobPosting.business']);
        $query->when($request->filled('job_id'), fn ($query) => $query->where('job_posting_id', $request->job_id))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->status))
            ->when($request->filled('search'), function ($query) use ($request) {
                $term = $request->string('search')->trim();
                $query->whereHas('user', fn ($user) => $user->where('name', 'like', "%{$term}%")->orWhere('email', 'like', "%{$term}%"));
            });

        return view('admin.jobs.applications', [
            'applications' => $query->latest('applied_at')->paginate(15)->withQueryString(),
            'jobs' => JobPosting::orderBy('title')->get(['id', 'title']),
            'counts' => [
                'total' => JobApplication::count(),
                'pending' => JobApplication::where('status', 'pending')->count(),
                'reviewed' => JobApplication::where('status', 'reviewed')->count(),
                'accepted' => JobApplication::where('status', 'accepted')->count(),
            ],
        ]);
    }

    public function updateApplicationStatus(Request $request, JobApplication $application)
    {
        $data = $request->validate(['status' => ['required', Rule::in(['pending', 'reviewed', 'accepted', 'rejected'])]]);
        $application->update($data + ['reviewed_at' => $data['status'] === 'pending' ? null : now()]);

        return back()->with('success', 'Application status updated.');
    }

    public function destroyApplication(JobApplication $application)
    {
        $application->delete();

        return back()->with('success', 'Application deleted.');
    }

    private function businesses()
    {
        return Business::orderBy('business_name')->get(['id', 'business_name']);
    }

    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'business_id' => ['required', 'exists:businesses,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'requirements' => ['required', 'string'],
            'salary_range' => ['nullable', 'string', 'max:100'],
            'job_type' => ['required', 'string', 'max:50'],
            'location' => ['required', 'string', 'max:255'],
            'experience_level' => ['nullable', Rule::in(['entry', 'junior', 'mid', 'senior', 'executive'])],
            'employment_type' => ['nullable', Rule::in(['full-time', 'part-time', 'contract', 'freelance', 'internship'])],
            'category' => ['nullable', 'string', 'max:100'],
            'skills_required_text' => ['nullable', 'string'],
            'benefits_text' => ['nullable', 'string'],
            'application_deadline' => ['nullable', 'date'],
            'expires_at' => ['required', 'date'],
            'status' => ['required', Rule::in(['pending', 'approved', 'rejected'])],
            'is_active' => ['required', 'boolean'],
        ]);

        $validated['skills_required'] = $this->commaSeparated($validated['skills_required_text'] ?? null);
        $validated['benefits'] = $this->commaSeparated($validated['benefits_text'] ?? null);
        unset($validated['skills_required_text'], $validated['benefits_text']);

        return $validated;
    }

    private function commaSeparated(?string $value): ?array
    {
        if (! $value) {
            return null;
        }

        $items = array_values(array_filter(array_map('trim', explode(',', $value))));

        return $items ?: null;
    }
}
