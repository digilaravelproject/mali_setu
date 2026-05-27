@extends('layouts.app')

@section('title', 'Total Jobs — Mali Setu')

@section('content')
@php
    $activeTab = request()->has('applications_page') ? 'applications' : 'browse';
@endphp

<div class="row justify-content-center text-start">
    <div class="col-xl-12 col-12">
        
        <!-- Header Banner -->
        <div class="welcome-banner mb-4 text-start shadow-sm border border-white border-opacity-10" style="background: linear-gradient(135deg, #ff4757 0%, #ff7a59 100%);">
            <span class="badge bg-white bg-opacity-20 text-black mb-3 px-3 py-1.5 rounded-pill fw-bold text-uppercase small"><i class="fa-solid fa-briefcase me-1 text-warning"></i> Careers Hub</span>
            <h1 class="fw-extrabold text-white mb-2 fs-2">Total Jobs Center</h1>
            <p class="opacity-90 mb-0 font-medium small" style="line-height:1.6;">Browse active career opportunities published by verified local businesses or track your submitted job applications.</p>
        </div>

        <!-- Tab Toggle buttons -->
        <div class="d-flex gap-2 mb-4">
            <a href="{{ route('dashboard.jobs.applied') }}" class="btn {{ $activeTab === 'browse' ? 'btn-primary' : 'btn-outline-primary' }} rounded-pill px-4 fw-bold shadow-sm">
                <i class="fa-solid fa-magnifying-glass me-1.5"></i> Browse Jobs Directory
            </a>
            <a href="{{ route('dashboard.jobs.applied', ['applications_page' => 1]) }}" class="btn {{ $activeTab === 'applications' ? 'btn-primary' : 'btn-outline-primary' }} rounded-pill px-4 fw-bold shadow-sm">
                <i class="fa-solid fa-file-signature me-1.5"></i> My Applications
            </a>
        </div>

        <!-- Main Card Panel -->
        <div class="glass-card bg-white p-4 border shadow-sm rounded-4">
            
            @if($activeTab === 'browse')
                <!-- TAB 1: BROWSE JOBS DIRECTORY -->
                <h5 class="fw-bold mb-4" style="color: var(--primary);"><i class="fa-solid fa-magnifying-glass me-1"></i> Active Employment Opportunities</h5>
                
                <div class="d-flex flex-column gap-4">
                    @forelse($allJobs as $job)
                        <div class="card border rounded-4 p-4 text-start bg-light bg-opacity-50 hover-shadow transition">
                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                                <div class="text-start flex-grow-1" style="max-width: 80%;">
                                    <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                        <h5 class="fw-bold text-dark mb-0">{{ $job->title }}</h5>
                                        <span class="badge bg-primary bg-opacity-10 text-primary fw-bold text-uppercase small" style="font-size:0.7rem;">{{ $job->category ?? 'General' }}</span>
                                    </div>
                                    <div class="mb-3 text-muted small">
                                        <span class="me-3"><i class="fa-solid fa-store me-1 text-primary"></i> <strong>{{ $job->business->business_name ?? 'N/A' }}</strong></span>
                                        <span class="me-3"><i class="fa-solid fa-location-dot me-1 text-primary"></i> {{ $job->location ?? 'N/A' }}</span>
                                        <span><i class="fa-solid fa-clock me-1 text-primary"></i> Deadline: {{ $job->application_deadline ? $job->application_deadline->format('M d, Y') : 'N/A' }}</span>
                                    </div>
                                    <p class="text-secondary small mb-3" style="line-height: 1.6;">{{ $job->description }}</p>
                                    
                                    <div class="d-flex flex-wrap gap-2 align-items-center">
                                        <span class="badge bg-white text-secondary border px-3 py-2 rounded-pill small">{{ ucfirst(str_replace('-', ' ', $job->employment_type)) }}</span>
                                        <span class="badge bg-white text-secondary border px-3 py-2 rounded-pill small">{{ ucfirst(str_replace('_', ' ', $job->experience_level)) }}</span>
                                        <span class="text-primary small fw-bold ms-2">Salary Budget: <strong class="text-dark">₹{{ $job->salary_range ?? 'Not specified' }}</strong></span>
                                    </div>

                                    @if(!empty($job->skills_required))
                                        <div class="mt-3">
                                            <span class="text-secondary small fw-bold me-2">Required Skills:</span>
                                            @foreach(is_array($job->skills_required) ? $job->skills_required : json_decode($job->skills_required, true) as $skill)
                                                <span class="badge bg-light text-dark border me-1 small px-2 py-1 rounded">{{ $skill }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                <div class="text-end">
                                    @if(Auth::check() && $job->business && Auth::id() === $job->business->user_id)
                                        <span class="badge bg-secondary py-2 px-3 rounded-pill fw-bold text-uppercase" style="font-size:0.75rem;"><i class="fa-solid fa-user-tie me-1"></i> My Posting</span>
                                    @elseif($job->hasUserApplied(Auth::id()))
                                        <button class="btn btn-secondary btn-sm rounded-pill py-2 px-3 fw-bold shadow-sm" disabled><i class="fa-solid fa-circle-check me-1"></i> Applied</button>
                                    @else
                                        <button type="button" class="btn btn-primary btn-sm rounded-pill py-2 px-4 fw-bold shadow-sm" onclick="openApplyJobModal({{ $job->id }}, '{{ addslashes($job->title) }}')">Apply Now</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5 bg-light rounded-3 border">
                            <i class="fa-solid fa-briefcase text-muted fs-1 mb-3" style="font-size: 3rem;"></i>
                            <h6 class="fw-bold mb-1">No Active Openings</h6>
                            <p class="small mb-0 text-muted">There are currently no active job openings available in the directory.</p>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination for Browse -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $allJobs->appends(['jobs_page' => $allJobs->currentPage()])->links() }}
                </div>

            @else
                <!-- TAB 2: MY APPLICATIONS HISTORY -->
                <h5 class="fw-bold mb-4" style="color: var(--primary);"><i class="fa-solid fa-file-signature me-1"></i> Application History</h5>
                
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr class="text-secondary small fw-bold" style="border-bottom: 2px solid #f1f3f5;">
                                <th class="py-3">Job Details</th>
                                <th class="py-3">Location</th>
                                <th class="py-3">Date Applied</th>
                                <th class="py-3">Status</th>
                                <th class="py-3 text-end">Action / Feedback</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($applications as $app)
                                <tr style="border-bottom: 1px solid #f8f9fa;">
                                    <td class="py-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; min-width: 48px;">
                                                <i class="fa-solid fa-briefcase fs-5"></i>
                                            </div>
                                            <div>
                                                <h6 class="fw-bold text-dark mb-0.5">{{ $app->jobPosting->title ?? 'N/A' }}</h6>
                                                <span class="text-muted small"><i class="fa-solid fa-store me-1 text-primary"></i> {{ $app->jobPosting->business->business_name ?? 'N/A' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <span class="text-secondary small"><i class="fa-solid fa-location-dot me-1 text-primary"></i> {{ $app->jobPosting->location ?? 'N/A' }}</span>
                                    </td>
                                    <td class="py-3">
                                        <span class="text-secondary small"><i class="fa-solid fa-calendar me-1 text-primary"></i> {{ $app->applied_at ? $app->applied_at->format('M d, Y') : 'N/A' }}</span>
                                    </td>
                                    <td class="py-3">
                                        @php
                                            $badgeColor = match($app->status) {
                                                'pending' => 'warning',
                                                'reviewed' => 'info',
                                                'accepted' => 'success',
                                                'rejected' => 'danger',
                                                default => 'secondary'
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $badgeColor }} bg-opacity-10 text-{{ $badgeColor }} py-2 px-3 rounded-pill fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                            {{ $app->status }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-end">
                                        @if($app->employer_notes)
                                            <button class="btn btn-outline-primary btn-sm rounded-3 fw-bold" onclick="showFeedback('{{ addslashes($app->jobPosting->title ?? 'N/A') }}', '{{ addslashes($app->employer_notes) }}')">
                                                <i class="fa-solid fa-comment-dots"></i> Feedback
                                            </button>
                                        @else
                                            <span class="text-muted small italic">No feedback yet</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-secondary">
                                        <i class="fa-solid fa-file-invoice text-muted fs-1 mb-3" style="font-size: 3rem;"></i>
                                        <h6 class="fw-bold mb-1">No Applications Yet</h6>
                                        <p class="small mb-0 text-muted">You haven't submitted any job applications yet.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination for Applications -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $applications->appends(['applications_page' => $applications->currentPage()])->links() }}
                </div>
            @endif

        </div>
        
    </div>
</div>

<!-- Apply Job Modal Overlay -->
<div class="modal fade" id="applyJobModal" tabindex="-1" aria-labelledby="applyJobModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg p-3">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold mb-0" id="applyJobModalLabel">Apply for Job</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('dashboard.business.jobs.apply') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="job_posting_id" id="apply_job_posting_id">
                
                <div class="modal-body py-3">
                    <div class="mb-3 text-start">
                        <label class="form-label">Job Title</label>
                        <input type="text" id="apply_job_title" class="form-control bg-light" readonly style="border: 1.5px solid #e2e8f0; pointer-events: none;">
                    </div>
                    
                    <div class="mb-3 text-start">
                        <label class="form-label">Cover Letter <span class="text-danger">*</span></label>
                        <textarea name="cover_letter" class="form-control" rows="4" placeholder="Briefly describe why you are a good fit for this role..." required></textarea>
                    </div>
                    
                    <div class="mb-3 text-start">
                        <label class="form-label">Upload Resume <span class="text-secondary small">(Optional, PDF/DOCX/JPG/PNG up to 5MB)</span></label>
                        <input type="file" name="resume" class="form-control">
                    </div>
                    
                    <div class="mb-3 text-start">
                        <label class="form-label">Additional Info <span class="text-secondary small">(Optional)</span></label>
                        <textarea name="additional_info" class="form-control" rows="2" placeholder="Any additional links or notes..."></textarea>
                    </div>
                </div>
                
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4" style="background-color: var(--primary) !important; border-color: var(--primary) !important;">Submit Application</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openApplyJobModal(jobId, jobTitle) {
        document.getElementById('apply_job_posting_id').value = jobId;
        document.getElementById('apply_job_title').value = jobTitle;
        const modal = new bootstrap.Modal(document.getElementById('applyJobModal'));
        modal.show();
    }

    function showFeedback(jobTitle, feedback) {
        Swal.fire({
            title: `Feedback: ${jobTitle}`,
            text: feedback,
            icon: 'info',
            confirmButtonColor: '#ff4757',
            background: 'rgba(255, 255, 255, 0.95)',
            backdrop: `rgba(255, 71, 87, 0.2)`
        });
    }
</script>
@endsection
