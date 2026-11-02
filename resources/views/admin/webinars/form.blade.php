@extends('admin.layouts.app')
@section('title',$webinar->exists?'Edit Webinar':'Create Webinar')
@section('page-title',$webinar->exists?'Edit Webinar':'Create Webinar / Video Conference')
@section('content')
<div class="text-muted small mb-3">Home <span class="mx-2">›</span> Broadcast / Webinars <span class="mx-2">›</span> {{ $webinar->exists?'Edit':'Create' }} Webinar</div>
<form method="POST" enctype="multipart/form-data" action="{{ $webinar->exists?route('admin.webinars.update',$webinar):route('admin.webinars.store') }}">@csrf @if($webinar->exists)@method('PUT')@endif
<div class="row g-4"><div class="col-lg-7"><div class="card border-0 shadow-sm"><div class="card-body p-4">
 <div class="mb-3"><label class="form-label fw-semibold">Title *</label><input required name="title" class="form-control" value="{{ old('title',$webinar->title) }}">@error('title')<small class="text-danger">{{ $message }}</small>@enderror</div>
 <div class="mb-3"><label class="form-label fw-semibold">Description *</label><textarea required name="description" rows="5" class="form-control">{{ old('description',$webinar->description) }}</textarea></div>
 <div class="mb-3"><label class="form-label fw-semibold">Speaker</label><input name="speaker_name" class="form-control" value="{{ old('speaker_name',$webinar->speaker_name) }}"></div>
 <div class="mb-3"><label class="form-label fw-semibold">Agenda</label><textarea name="agenda" rows="4" class="form-control">{{ old('agenda',$webinar->agenda) }}</textarea></div>
 <div class="row"><div class="col-md-7 mb-3"><label class="form-label fw-semibold">Date & Time *</label><input type="datetime-local" required name="starts_at" class="form-control" value="{{ old('starts_at',$webinar->starts_at?->format('Y-m-d\TH:i')) }}"></div><div class="col-md-5 mb-3"><label class="form-label fw-semibold">Duration (minutes) *</label><input type="number" min="15" max="480" required name="duration_minutes" class="form-control" value="{{ old('duration_minutes',$webinar->duration_minutes?:60) }}"></div></div>
</div></div></div><div class="col-lg-5"><div class="card border-0 shadow-sm"><div class="card-body p-4">
 <div class="mb-3"><label class="form-label fw-semibold">Banner Image</label><input type="file" name="banner" accept="image/*" class="form-control">@if($webinar->banner_url)<img src="{{ $webinar->banner_url }}" class="img-fluid rounded mt-2">@endif<div class="form-text">Recommended 1280 × 720</div></div>
 <div class="mb-3"><label class="form-label fw-semibold">Target Audience</label><select name="audience" class="form-select"><option value="all" @selected(old('audience',$webinar->audience)==='all')>All registered & approved users</option><option value="subscribers" @selected(old('audience',$webinar->audience)==='subscribers')>Subscribers only</option></select></div>
 <div class="mb-4"><label class="form-label fw-semibold">Status</label><select name="status" class="form-select"><option value="published" @selected(old('status',$webinar->status)==='published')>Publish Now</option><option value="draft" @selected(old('status',$webinar->status)==='draft')>Draft</option><option value="cancelled" @selected(old('status',$webinar->status)==='cancelled')>Cancelled</option></select></div>
 @if($webinar->exists)<div class="alert alert-light border small"><strong>Agora channel:</strong><br>{{ $webinar->agora_channel }}<br><a href="{{ $webinar->agora_link }}" target="_blank">{{ $webinar->agora_link }}</a></div>@endif
 <div class="d-flex justify-content-end gap-2"><a href="{{ route('admin.webinars.index') }}" class="btn btn-outline-secondary">Cancel</a><button class="btn btn-primary px-4">{{ $webinar->exists?'Update':'Create Webinar' }}</button></div>
</div></div></div></div></form>
@endsection
