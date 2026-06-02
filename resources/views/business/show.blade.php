@extends('layouts.app')

@section('content')
<div class="row g-4 justify-content-center text-start">
    <div class="col-xl-12 col-12">
        
        <!-- Header Actions -->
        <div class="d-flex align-items-center justify-content-between mb-4 mt-4">
            <a href="{{ route('dashboard.business.browse') }}" class="btn btn-light btn-sm rounded-3">
                <i class="fa-solid fa-arrow-left"></i> Back to Directory
            </a>
            @if(Auth::id() === $business->user_id)
                <a href="{{ route('dashboard.business.index') }}" class="btn btn-teal btn-sm rounded-3">
                    <i class="fa-solid fa-sliders me-1"></i> Edit Business
                </a>
            @endif
        </div>

        <!-- Cover Photos Showcase -->
        <div class="glass-card overflow-hidden p-0 border shadow-sm mb-4 bg-white">
            @php
                $photos = $business->photo ? explode(',', $business->photo) : [];
            @endphp
            @if(count($photos) > 0)
                <div id="businessPhotosCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner" style="height: 380px;">
                        @foreach($photos as $index => $photo)
                            @if(trim($photo))
                                <div class="carousel-item {{ $index === 0 ? 'active' : '' }} h-100">
                                    <img src="{{ asset('storage/' . trim($photo)) }}" class="d-block w-100 h-100" style="object-fit: cover;">
                                </div>
                            @endif
                        @endforeach
                    </div>
                    @if(count($photos) > 1)
                        <button class="carousel-control-prev" type="button" data-bs-target="#businessPhotosCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#businessPhotosCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    @endif
                </div>
            @else
                <div class="bg-teal bg-opacity-10 text-teal d-flex flex-column align-items-center justify-content-center" style="height: 240px;">
                    <i class="fa-solid fa-store fs-1 mb-2"></i>
                    <h5 class="fw-bold mb-0">Showroom Photos Pending</h5>
                </div>
            @endif

            <!-- Business Meta Banner -->
            <div class="p-4 border-top">
                <div class="row align-items-start g-3">
                    <div class="col text-start">
                        <div class="d-flex align-items-center gap-2 flex-wrap mb-2">
                            <span class="badge bg-teal bg-opacity-10 text-teal py-1.5 px-3 rounded-pill fw-semibold">{{ $business->category->name ?? 'Agriculture' }}</span>
                            <span class="badge bg-light text-secondary py-1.5 px-3 rounded-pill border">{{ $business->business_type }}</span>
                            @if($business->verification_status === 'approved')
                                <span class="badge bg-success py-1.5 px-3 rounded-pill text-white"><i class="fa-solid fa-circle-check me-1"></i> Verified Partner</span>
                            @endif
                        </div>
                        <h2 class="fw-bold text-dark mb-2">{{ $business->business_name }}</h2>
                        <div class="d-flex flex-wrap gap-3 align-items-center text-secondary small">
                            <span><i class="fa-solid fa-location-dot me-1 text-primary"></i> {{ $business->address }}, {{ $business->city }}, {{ $business->state }} - {{ $business->pincode }}</span>
                            @if(isset($business->distance))
                                <span class="badge bg-light text-secondary border small"><i class="fa-solid fa-route text-danger me-1"></i> {{ $business->distance }} km away</span>
                            @endif
                            @if($business->opening_time)
                                <span><i class="fa-solid fa-clock me-1 text-primary"></i> Timings: {{ $business->opening_time }} - {{ $business->closing_time }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-auto text-md-end text-start">
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-warning fs-4"><i class="fa-solid fa-star"></i></span>
                            <h3 class="fw-bold text-dark mb-0 font-monospace">{{ number_format($avgRating, 1) }}</h3>
                            <span class="text-muted small">({{ $business->reviews->count() }} Reviews)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Left Side: Profile Details & Catalogs -->
            <div class="col-lg-8 col-12">
                
                <!-- About Description -->
                <div class="glass-card p-4 border shadow-sm mb-4 bg-white">
                    <h5 class="fw-bold text-primary mb-3"><i class="fa-solid fa-circle-info me-1"></i> About Enterprise</h5>
                    <p class="text-secondary small mb-0" style="line-height: 1.6; white-space: pre-line;">{{ $business->description }}</p>
                </div>

                <!-- Tabwise Showcase (Products, Services, Jobs) -->
                <div class="glass-card p-4 border shadow-sm mb-4 bg-white">
                    <ul class="nav nav-pills mb-4 bg-light p-1 rounded-3 nav-fill" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-bold small rounded-3 py-2" id="prod-showcase-tab" data-bs-toggle="tab" data-bs-target="#prod-showcase" type="button" role="tab"><i class="fa-solid fa-box me-1"></i> Products ({{ $business->products->count() }})</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold small rounded-3 py-2" id="serv-showcase-tab" data-bs-toggle="tab" data-bs-target="#serv-showcase" type="button" role="tab"><i class="fa-solid fa-server me-1"></i> Services ({{ $business->services->count() }})</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold small rounded-3 py-2" id="jobs-showcase-tab" data-bs-toggle="tab" data-bs-target="#jobs-showcase" type="button" role="tab"><i class="fa-solid fa-briefcase me-1"></i> Jobs ({{ $business->jobPostings->count() }})</button>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <!-- Products Catalog -->
                        <div class="tab-pane fade show active" id="prod-showcase" role="tabpanel">
                            <div class="row g-3">
                                @forelse($business->products as $p)
                                    <div class="col-md-6 col-12">
                                        <div class="card h-100 border rounded-4 p-3 bg-light bg-opacity-50">
                                            <div class="d-flex gap-3 align-items-start">
                                                @if($p->image_path)
                                                    <img src="{{ asset('storage/' . $p->image_path) }}" class="rounded shadow-sm border" style="width: 80px; height: 80px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light border text-secondary rounded d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;"><i class="fa-solid fa-box fs-3"></i></div>
                                                @endif
                                                <div class="text-start">
                                                    <h6 class="fw-bold text-dark mb-1">{{ $p->name }}</h6>
                                                    <span class="badge bg-teal bg-opacity-10 text-teal mb-2">₹{{ number_format($p->cost ?? 0, 2) }}</span>
                                                    <p class="text-secondary small mb-0" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">{{ $p->description }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12 text-center py-4 bg-light rounded-3 border">
                                        <p class="text-secondary small mb-0">No premium products cataloged under this showroom.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <!-- Services Catalog -->
                        <div class="tab-pane fade" id="serv-showcase" role="tabpanel">
                            <div class="row g-3">
                                @forelse($business->services as $s)
                                    <div class="col-md-6 col-12">
                                        <div class="card h-100 border rounded-4 p-3 bg-light bg-opacity-50">
                                            <div class="d-flex gap-3 align-items-start">
                                                @if($s->image_path)
                                                    <img src="{{ asset('storage/' . $s->image_path) }}" class="rounded shadow-sm border" style="width: 80px; height: 80px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light border text-secondary rounded d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;"><i class="fa-solid fa-server fs-3"></i></div>
                                                @endif
                                                <div class="text-start">
                                                    <h6 class="fw-bold text-dark mb-1">{{ $s->name }}</h6>
                                                    <span class="badge bg-teal bg-opacity-10 text-teal mb-2">₹{{ number_format($s->cost ?? 0, 2) }}</span>
                                                    <p class="text-secondary small mb-0" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">{{ $s->description }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12 text-center py-4 bg-light rounded-3 border">
                                        <p class="text-secondary small mb-0">No active services offered under this showroom.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <!-- Job Postings -->
                        <div class="tab-pane fade" id="jobs-showcase" role="tabpanel">
                            <div class="d-flex flex-column gap-3">
                                @forelse($business->jobPostings as $j)
                                    <div class="card border rounded-4 p-4 text-start bg-light bg-opacity-50">
                                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                            <div class="text-start">
                                                <h6 class="fw-bold text-dark mb-1">{{ $j->title }}</h6>
                                                <div class="d-flex flex-wrap gap-2 mb-3">
                                                    <span class="badge bg-light text-secondary border small">{{ ucfirst($j->employment_type) }}</span>
                                                    <span class="badge bg-light text-secondary border small">{{ ucfirst($j->experience_level) }}</span>
                                                    <span class="badge bg-light text-secondary border small"><i class="fa-solid fa-location-dot me-1"></i> {{ $j->location }}</span>
                                                </div>
                                                <p class="text-secondary small mb-3">{{ $j->description }}</p>
                                                <div class="small fw-semibold text-secondary">Salary Budget: <span class="text-primary">₹{{ $j->salary_range ?? 'Unspecified' }}</span></div>
                                            </div>
                                            <div>
                                                @if(Auth::check() && Auth::id() !== $business->user_id)
                                                    @if($j->hasUserApplied(Auth::id()))
                                                        <button class="btn btn-secondary btn-sm rounded-3 py-2 px-3 fw-bold" disabled><i class="fa-solid fa-circle-check me-1"></i> Applied</button>
                                                    @else
                                                        <button type="button" class="btn btn-primary btn-sm rounded-3 py-2 px-3 fw-bold" onclick="openApplyJobModal({{ $j->id }}, '{{ addslashes($j->title) }}')">Apply Now</button>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-4 bg-light rounded-3 border">
                                        <p class="text-secondary small mb-0">No active employment listings posted currently.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reviews and Customer Ratings Section -->
                <div class="glass-card p-4 border shadow-sm bg-white">
                    <h5 class="fw-bold text-primary mb-4"><i class="fa-solid fa-comments me-1"></i> Customer Reviews</h5>
                    
                    @if(Auth::check() && Auth::id() !== $business->user_id)
                        <!-- Write a review form -->
                        <div class="card border rounded-4 p-4 mb-4 bg-light bg-opacity-50">
                            <h6 class="fw-bold text-dark mb-3">Submit Your Rating & Feedback</h6>
                            
                            <form action="{{ route('dashboard.business.reviews.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="business_id" value="{{ $business->id }}">
                                
                                <div class="mb-3 text-start">
                                    <label class="form-label fw-semibold text-secondary small">Rating Indicator *</label>
                                    <select name="rating" class="form-select w-auto" required>
                                        <option value="5">⭐⭐⭐⭐⭐ (5 - Excellent)</option>
                                        <option value="4">⭐⭐⭐⭐ (4 - Good)</option>
                                        <option value="3">⭐⭐⭐ (3 - Average)</option>
                                        <option value="2">⭐⭐ (2 - Fair)</option>
                                        <option value="1">⭐ (1 - Poor)</option>
                                    </select>
                                </div>

                                <div class="mb-3 text-start">
                                    <label class="form-label fw-semibold text-secondary small">Review Comments</label>
                                    <textarea name="review_text" class="form-control" rows="3" placeholder="Describe your experience with this vendor..." required></textarea>
                                </div>

                                <button type="submit" class="btn btn-teal btn-sm rounded-3 py-2 px-4 fw-bold">Submit Review</button>
                            </form>
                        </div>
                    @endif

                    <!-- Reviews List -->
                    <div class="d-flex flex-column gap-3 mt-4">
                        @forelse($business->reviews as $r)
                            <div class="border-bottom pb-3 mb-2 text-start">
                                <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap">
                                    <div>
                                        <strong class="text-dark small">{{ $r->user->name ?? 'Anonymous Guest' }}</strong>
                                        <div class="text-warning small mt-0.5">
                                            @for($i=1; $i<=5; $i++)
                                                <i class="fa-solid fa-star {{ $i <= $r->rating ? 'text-warning' : 'text-muted opacity-50' }}"></i>
                                            @endfor
                                        </div>
                                    </div>
                                    <span class="text-muted small">{{ $r->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-secondary small mb-0 italic">"{{ $r->review_text }}"</p>
                            </div>
                        @empty
                            <div class="text-center py-4 bg-light rounded-3 border">
                                <p class="text-secondary small mb-0">No ratings or feedbacks cataloged yet. Be the first to submit!</p>
                            </div>
                        @endforelse
                    </div>

                </div>

            </div>

            <!-- Right Side: Contact info & Maps placeholder -->
            <div class="col-lg-4 col-12">
                <!-- Action Cards -->
                <div class="glass-card p-4 border shadow-sm mb-4 bg-white">
                    <h5 class="fw-bold text-teal mb-3"><i class="fa-solid fa-paper-plane me-1"></i> Contact Enterprise</h5>
                    
                    <div class="d-flex flex-column gap-3">
                        @if($business->contact_phone)
                            <a href="#" class="btn btn-outline-teal w-100 py-2.5 rounded-3 fw-bold text-start" data-bs-toggle="modal" data-bs-target="#phoneCallModal"><i class="fa-solid fa-phone me-2"></i> Call Vendor</a>
                        @endif
                        @if($business->contact_email)
                            <a href="mailto:{{ $business->contact_email }}?subject=Inquiry about {{ rawurlencode($business->business_name) }}&body=Hi, I found your business {{ rawurlencode($business->business_name) }} on Mali Setu: {{ rawurlencode(url()->current()) }}" class="btn btn-outline-teal w-100 py-2.5 rounded-3 fw-bold text-start"><i class="fa-solid fa-envelope me-2"></i> Email Vendor</a>
                        @endif
                        @if($business->contact_phone)
                            @php
                                $cleanPhone = preg_replace('/[^0-9]/', '', $business->contact_phone);
                                if (strlen($cleanPhone) == 10) {
                                    $cleanPhone = '91' . $cleanPhone;
                                }
                            @endphp
                            <a href="https://api.whatsapp.com/send?phone={{ $cleanPhone }}&text=Hello, I found your business {{ rawurlencode($business->business_name) }} on Mali Setu: {{ rawurlencode(url()->current()) }}" target="_blank" class="btn btn-outline-success w-100 py-2.5 rounded-3 fw-bold text-start" style="border-color: #25D366 !important; color: #25D366 !important; transition: all 0.2s;"><i class="fa-brands fa-whatsapp me-2" style="font-size: 1.1rem;"></i> WhatsApp Vendor</a>
                        @endif
                        @if($business->website)
                            <a href="{{ $business->website }}" target="_blank" class="btn btn-outline-secondary w-100 py-2.5 rounded-3 fw-bold text-start"><i class="fa-solid fa-globe me-2"></i> Visit Website</a>
                        @endif
                    </div>
                </div>

                <!-- Location / Address Card -->
                <div class="glass-card p-4 border shadow-sm bg-white">
                    <h5 class="fw-bold text-teal mb-3"><i class="fa-solid fa-location-dot me-1"></i> Business Location</h5>
                    <p class="text-secondary small mb-4">{{ $business->address }}, {{ $business->city }}, {{ $business->state }} - {{ $business->pincode }}</p>
                    
                    <!-- Static premium placeholder map image -->
                    <div class="rounded-4 overflow-hidden shadow-sm border" style="height: 200px; background: #e0f2f1; position: relative;">
                        <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center text-teal">
                            <i class="fa-solid fa-map-location-dot fs-1 mb-2"></i>
                            <span class="small fw-semibold">Interactive Map Portal Synced</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Apply Job Modal -->
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
                    <button type="submit" class="btn btn-primary px-4">Submit Application</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Phone Contact Call Modal -->
@if($business->contact_phone)
<div class="modal fade" id="phoneCallModal" tabindex="-1" aria-labelledby="phoneCallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg p-3">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold mb-0" id="phoneCallModalLabel">Contact Vendor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="mb-3 d-inline-flex align-items-center justify-content-center bg-teal bg-opacity-10 text-teal rounded-circle" style="width: 72px; height: 72px;">
                    <i class="fa-solid fa-phone-volume fs-1 text-primary"></i>
                </div>
                <h6 class="text-secondary small mb-1">Business Phone Number</h6>
                <h3 class="fw-bold text-dark mb-4">{{ $business->contact_phone }}</h3>
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-light px-4 rounded-3 fw-bold" data-bs-dismiss="modal">Close</button>
                    <a href="tel:{{ $business->contact_phone }}" class="btn btn-primary px-4 rounded-3 fw-bold"><i class="fa-solid fa-phone me-1"></i> Call Now</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@section('scripts')
<script>
    function openApplyJobModal(jobId, jobTitle) {
        document.getElementById('apply_job_posting_id').value = jobId;
        document.getElementById('apply_job_title').value = jobTitle;
        const modal = new bootstrap.Modal(document.getElementById('applyJobModal'));
        modal.show();
    }
</script>
@endsection
@endsection
