@extends('layouts.app')

@section('title', ($page->page_name ?? 'Page Details') . ' — Mali Setu')

@section('content')
<div class="row justify-content-center text-start">
    <div class="col-xl-12 col-12">
        
        <!-- Header Banner -->
        <div class="welcome-banner mb-4 text-start shadow-sm border border-white border-opacity-10 d-none" style="background: linear-gradient(135deg, #ff4757 0%, #ff7a59 100%);">
            <span class="badge bg-white bg-opacity-20 text-black mb-3 px-3 py-1.5 rounded-pill fw-bold text-uppercase small"><i class="fa-solid fa-file-invoice me-1 text-warning"></i> Policy & Info</span>
            <h1 class="fw-extrabold text-white mb-2 fs-2">{{ $page->page_name ?? 'Information' }}</h1>
            <p class="opacity-90 mb-0 font-medium small" style="line-height:1.6;">Official policy parameters, community guidelines, and service parameters of the Mali Setu Foundation.</p>
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
