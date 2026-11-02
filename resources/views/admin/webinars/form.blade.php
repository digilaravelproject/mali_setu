@extends('admin.layouts.app')
@section('title', $webinar->exists ? 'Edit Webinar' : 'Create Webinar')
@section('page-title', $webinar->exists ? 'Edit Webinar / Video Conference' : 'Create Webinar / Video Conference')

@push('styles')
<style>
    .webinar-form-card { border: 1px solid #edf0f5; border-radius: 12px; background: #fff; box-shadow: 0 3px 14px rgba(25,33,61,.05); }
    .webinar-form-card .form-label { color: #252b3a; font-size: .88rem; font-weight: 600; margin-bottom: 6px; }
    .webinar-form-card .form-control { min-height: 42px; border-color: #dfe3eb; font-size: .9rem; }
    .webinar-form-card textarea.form-control { min-height: auto; }
    .category-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 10px; }
    .category-option { position: relative; cursor: pointer; }
    .category-option input { position: absolute; opacity: 0; pointer-events: none; }
    .category-option span { height: 68px; border: 1px solid #e3e6ee; border-radius: 7px; background: #fafbfe; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 5px; color: #252b3a; transition: .18s ease; }
    .category-option i { font-size: 1.2rem; }
    .category-option.business i { color: #a00058; } .category-option.matrimony i { color: #f03e82; } .category-option.other i { color: #8d3fd1; }
    .category-option input:checked + span { border-color: #b00062; background: #fff5fa; box-shadow: inset 0 0 0 1px #b00062; }
    .category-option input:checked + span::after { content: '\f058'; font-family: 'Font Awesome 6 Free'; font-weight: 900; position: absolute; right: 8px; top: 7px; color: #b00062; font-size: .8rem; }
    .banner-preview { height: 168px; border-radius: 8px; overflow: hidden; background: linear-gradient(135deg,#8a004d,#d21374); position: relative; display:flex; align-items:center; justify-content:center; color:#fff; }
    .banner-preview img { width: 100%; height: 100%; object-fit: cover; }
    .banner-placeholder { text-align:center; } .banner-placeholder i { font-size:2.3rem; margin-bottom:8px; }
    .compact-check { display:flex; gap:9px; align-items:center; font-size:.88rem; color:#303647; margin-bottom:9px; cursor:pointer; }
    .compact-check input { width:16px; height:16px; accent-color:#ae005f; }
    .status-option { display:inline-flex; align-items:center; gap:7px; margin-right:22px; font-size:.88rem; cursor:pointer; }
    .status-option input { width:16px; height:16px; accent-color:#ae005f; }
    .btn-webinar { background:#a9005d; border-color:#a9005d; color:#fff; }
    .btn-webinar:hover { background:#8b004d; border-color:#8b004d; color:#fff; }
    @media(max-width:767px){ .category-grid{grid-template-columns:1fr;} }
</style>
@endpush

@section('content')
@php
    $selectedCategory = old('category', $webinar->category ?: 'business');
    $selectedAudience = old('audience', $webinar->audience ?: 'all');
    $selectedAccess = old('access_type', $webinar->access_type ?: 'free');
    $selectedPlans = old('plan_ids', $webinar->plan_ids ?: []);
    $selectedStatus = old('status', $webinar->status ?: 'published');
@endphp
<div class="text-muted small mb-3">
    <a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Home</a>
    <span class="mx-2">›</span><a href="{{ route('admin.webinars.index') }}" class="text-decoration-none">Broadcast / Webinars</a>
    <span class="mx-2">›</span>{{ $webinar->exists ? 'Edit Webinar' : 'Create Webinar' }}
</div>

<form method="POST" enctype="multipart/form-data" action="{{ $webinar->exists ? route('admin.webinars.update',$webinar) : route('admin.webinars.store') }}">
    @csrf
    @if($webinar->exists) @method('PUT') @endif
    <div class="row g-3 align-items-start">
        <div class="col-xl-7">
            <div class="webinar-form-card p-3">
                <div class="mb-3">
                    <label class="form-label">Title <span class="text-danger">*</span></label>
                    <input required name="title" class="form-control" value="{{ old('title',$webinar->title) }}" placeholder="Business Growth Strategies for Entrepreneurs">
                    @error('title')<small class="text-danger">{{ $message }}</small>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Category / Section <span class="text-danger">*</span></label>
                    <div class="category-grid">
                        <label class="category-option business"><input type="radio" name="category" value="business" @checked($selectedCategory==='business')><span><i class="fas fa-store"></i>Business</span></label>
                        <label class="category-option matrimony"><input type="radio" name="category" value="matrimony" @checked($selectedCategory==='matrimony')><span><i class="fas fa-heart"></i>Matrimony</span></label>
                        <label class="category-option other"><input type="radio" name="category" value="other" @checked($selectedCategory==='other')><span><i class="fas fa-shapes"></i>Other</span></label>
                    </div>
                    @error('category')<small class="text-danger">{{ $message }}</small>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Description <span class="text-danger">*</span></label>
                    <textarea required name="description" rows="4" class="form-control" placeholder="Join our expert session on business growth, marketing and networking...">{{ old('description',$webinar->description) }}</textarea>
                    @error('description')<small class="text-danger">{{ $message }}</small>@enderror
                </div>

                <div class="row g-2">
                    <div class="col-md-5 mb-3"><label class="form-label">Date <span class="text-danger">*</span></label><input type="date" required name="webinar_date" class="form-control" value="{{ old('webinar_date',$webinar->starts_at?->format('Y-m-d')) }}">@error('webinar_date')<small class="text-danger">{{ $message }}</small>@enderror</div>
                    <div class="col-md-4 mb-3"><label class="form-label">Time <span class="text-danger">*</span></label><input type="time" required name="webinar_time" class="form-control" value="{{ old('webinar_time',$webinar->starts_at?->format('H:i')) }}">@error('webinar_time')<small class="text-danger">{{ $message }}</small>@enderror</div>
                    <div class="col-md-3 mb-3"><label class="form-label">Duration (Minutes) <span class="text-danger">*</span></label><input type="number" min="15" max="480" required name="duration_minutes" class="form-control" value="{{ old('duration_minutes',$webinar->duration_minutes ?: 60) }}"></div>
                </div>

                {{-- Platform is intentionally omitted because Agora is used automatically. --}}
                <div class="mb-1">
                    <label class="form-label d-block">Meeting Settings <span class="text-danger">*</span></label>
                    <label class="compact-check"><input type="checkbox" name="require_registration" value="1" @checked(old('require_registration',$webinar->exists ? $webinar->require_registration : true))> Require Registration</label>
                    <label class="compact-check"><input type="checkbox" name="allow_qa" value="1" @checked(old('allow_qa',$webinar->exists ? $webinar->allow_qa : true))> Allow Q&amp;A</label>
                    <label class="compact-check mb-0"><input type="checkbox" name="allow_recording" value="1" @checked(old('allow_recording',$webinar->allow_recording))> Allow Record Session</label>
                </div>
            </div>
        </div>

        <div class="col-xl-5">
            <div class="webinar-form-card p-3">
                <div class="mb-3">
                    <label class="form-label">Banner Image</label>
                    <div class="banner-preview" id="bannerPreview">@if($webinar->banner_url)<img src="{{ $webinar->banner_url }}" alt="Webinar banner">@else<div class="banner-placeholder"><i class="fas fa-video"></i><div class="fw-bold">Webinar Banner</div></div>@endif</div>
                    <div class="d-flex align-items-center justify-content-between mt-2"><label class="btn btn-sm btn-outline-primary mb-0" for="bannerInput"><i class="fas fa-upload me-1"></i> Change Image</label><span class="small text-muted">Recommended size: 1280 × 720</span></div>
                    <input type="file" name="banner" id="bannerInput" accept="image/*" class="d-none">
                    @error('banner')<small class="text-danger d-block mt-1">{{ $message }}</small>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label d-block">Target Audience</label>
                    <label class="compact-check"><input type="checkbox" class="exclusive-choice" data-group="audience" name="audience" value="all" @checked($selectedAudience==='all')> All Registered &amp; Approved Users</label>
                    <label class="compact-check mb-0"><input type="checkbox" class="exclusive-choice" data-group="audience" name="audience" value="subscribers" @checked($selectedAudience==='subscribers')> Only Subscribers</label>
                </div>

                <div class="mb-3">
                    <label class="form-label d-block">Subscription Plan Access</label>
                    <label class="compact-check"><input type="checkbox" class="exclusive-choice" data-group="access" name="access_type" value="free" @checked($selectedAccess==='free')> Free for All</label>
                    <label class="compact-check"><input type="checkbox" class="exclusive-choice" data-group="access" name="access_type" value="specific_plans" @checked($selectedAccess==='specific_plans')> Specific Plans Only</label>
                    <select name="plan_ids[]" id="planSelect" class="form-select" multiple size="3" @disabled($selectedAccess!=='specific_plans')>
                        @foreach($businessPlans as $plan)<option value="business:{{ $plan->id }}" @selected(in_array('business:'.$plan->id,$selectedPlans))>Business — {{ $plan->company_type }} ({{ $plan->duration_years }} year)</option>@endforeach
                        @foreach($matrimonyPlans as $plan)<option value="matrimony:{{ $plan->id }}" @selected(in_array('matrimony:'.$plan->id,$selectedPlans))>Matrimony — {{ $plan->plan_name ?: $plan->duration_years.' year' }}</option>@endforeach
                    </select>
                    <div class="form-text">Hold Ctrl to select multiple plans.</div>
                    @error('plan_ids')<small class="text-danger">{{ $message }}</small>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label d-block">Status</label>
                    <label class="status-option"><input type="radio" name="status" value="published" @checked($selectedStatus==='published')> Publish Now</label>
                    <label class="status-option"><input type="radio" name="status" value="draft" @checked($selectedStatus==='draft')> Schedule for Later</label>
                </div>

                @if($webinar->exists)<div class="alert alert-light border py-2 px-3 small mb-3"><strong>Agora channel:</strong> {{ $webinar->agora_channel }}</div>@endif
                <div class="d-flex justify-content-end gap-2 pt-1"><a href="{{ route('admin.webinars.index') }}" class="btn btn-outline-secondary px-4">Cancel</a><button class="btn btn-webinar px-4">{{ $webinar->exists ? 'Update Webinar' : 'Create Webinar' }}</button></div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.getElementById('bannerInput')?.addEventListener('change', function () {
    const file = this.files?.[0]; if (!file) return; const reader = new FileReader();
    reader.onload = event => document.getElementById('bannerPreview').innerHTML = `<img src="${event.target.result}" alt="Webinar banner preview">`;
    reader.readAsDataURL(file);
});
document.querySelectorAll('.exclusive-choice').forEach(input => input.addEventListener('change', function () {
    const group = document.querySelectorAll(`.exclusive-choice[data-group="${this.dataset.group}"]`);
    if (this.checked) group.forEach(item => { if (item !== this) item.checked = false; });
    else if (![...group].some(item => item.checked)) this.checked = true;
}));
document.querySelectorAll('input[name="access_type"]').forEach(input => input.addEventListener('change', function () {
    if (!this.checked) return;
    const select = document.getElementById('planSelect'); select.disabled = this.value !== 'specific_plans';
    if (select.disabled) Array.from(select.options).forEach(option => option.selected = false);
}));
</script>
@endpush
