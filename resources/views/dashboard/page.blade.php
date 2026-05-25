@extends('layouts.app')

@section('title', ($page->page_name ?? 'Page Details') . ' — Mali Setu')

@section('content')
<div class="row justify-content-center text-start">
    <div class="col-xl-9 col-12">
        
        <!-- Header Banner -->
        <div class="welcome-banner mb-4">
            <h1 class="fw-extrabold mb-2">{{ $page->page_name ?? 'Information' }}</h1>
            <p class="lead mb-0 text-white-50">Official policy and utility center of Mali Setu</p>
        </div>

        <!-- Main Content Panel -->
        <div class="glass-card bg-white p-5 border shadow-sm rounded-4">
            @if($page)
                <div class="text-secondary" style="line-height: 1.8; font-size: 0.98rem;">
                    {!! $page->clean_description !!}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fa-solid fa-file-circle-xmark text-muted fs-1 mb-3"></i>
                    <h5 class="fw-bold text-dark">Page Not Found</h5>
                    <p class="text-secondary small mb-0">The requested page content has not been published yet.</p>
                </div>
            @endif
        </div>
        
    </div>
</div>
@endsection
