@extends('admin.layouts.app')
@section('title', 'Webinar Recording')
@section('page-title', 'Webinar Recording')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-1">{{ $webinar->title }}</h4>
        <div class="text-muted small">Recorded {{ $webinar->recorded_at?->timezone(config('app.webinar_timezone'))->format('d M Y, h:i A') ?? 'after the webinar' }} IST</div>
    </div>
    <a href="{{ route('admin.webinars.index', ['tab' => 'completed']) }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body p-3">
        <video class="w-100 rounded bg-dark" style="max-height:75vh" controls preload="metadata">
            <source src="{{ $webinar->recording_url }}" type="video/webm">
            Your browser does not support webinar playback.
        </video>
    </div>
</div>
@endsection
