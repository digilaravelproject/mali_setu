@extends('layouts.app')

@section('title', 'Blog Portal — Mali Setu')

@section('content')
<div class="container py-4">
    <!-- Header Area -->
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h1 class="fw-extrabold text-primary mb-1">
                <i class="fa-solid fa-blog me-2"></i>Community Blog Portal
            </h1>
            <p class="text-muted mb-0">Share your stories, knowledge, and updates with the community.</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0 d-flex gap-2 justify-content-md-end justify-content-start flex-wrap">
            @if($user->blog_access)
                <button class="btn btn-outline-primary px-4 py-2 rounded-pill shadow-sm fw-bold" id="my-publications-btn" type="button">
                    <i class="fa-solid fa-book-open me-2"></i>My Publications ({{ count($myBlogs) }})
                </button>
                <a href="{{ route('blogs.create') }}" class="btn btn-primary px-4 py-2 rounded-pill shadow-sm fw-bold">
                    <i class="fa-solid fa-pen-nib me-2"></i>Write a Blog
                </a>
            @else
                <button class="btn btn-outline-secondary px-4 py-2 rounded-pill shadow-sm" disabled title="Contact Admin to get blog access">
                    <i class="fa-solid fa-lock me-2"></i>Write a Blog
                </button>
            @endif
        </div>
    </div>

    <!-- Search & Filter Area -->
    <div class="card border-0 shadow-sm mb-4" style="background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(10px); border-radius: 16px;">
        <div class="card-body p-4">
            <form action="{{ route('blogs.index') }}" method="GET" class="row g-3">
                <div class="col-lg-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Search blog title or content..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-lg-4">
                    <select name="tag" class="form-select">
                        <option value="">Filter by Tag (All)</option>
                        @php
                            // Fetch all active tags from blogs
                            $allTags = \App\Models\Blog::where('is_active', true)->pluck('tags');
                            $uniqueTags = [];
                            foreach ($allTags as $tagsArr) {
                                if (is_array($tagsArr)) {
                                    foreach ($tagsArr as $t) {
                                        $uniqueTags[] = trim($t);
                                    }
                                }
                            }
                            $uniqueTags = array_unique(array_filter($uniqueTags));
                            asort($uniqueTags);
                        @endphp
                        @foreach($uniqueTags as $t)
                            <option value="{{ $t }}" {{ request('tag') == $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 d-grid">
                    <button type="submit" class="btn btn-primary rounded-pill">Filter</button>
                </div>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px;">
            <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px;">
            <i class="fa-solid fa-circle-exclamation me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Navigation Tabs -->
    <ul class="nav nav-pills mb-4 gap-2" id="blogTabs" role="tablist">
        @if(!$activeCategories->isEmpty())
            @foreach($activeCategories as $index => $cat)
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $index === 0 ? 'active' : '' }} rounded-pill px-4 fw-semibold" id="tab-{{ Str::slug($cat->name) }}" data-bs-toggle="pill" data-bs-target="#pane-{{ Str::slug($cat->name) }}" type="button" role="tab" aria-controls="pane-{{ Str::slug($cat->name) }}" aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                        <i class="fa-solid fa-folder-open me-2"></i>{{ $cat->name }}
                    </button>
                </li>
            @endforeach
        @else
            <li class="nav-item" role="presentation">
                <button class="nav-link active rounded-pill px-4" id="explore-tab" data-bs-toggle="pill" data-bs-target="#explore-pane" type="button" role="tab" aria-controls="explore-pane" aria-selected="true">
                    <i class="fa-solid fa-compass me-2"></i>Explore Articles
                </button>
            </li>
        @endif
        @if($user->blog_access)
            <li class="nav-item d-none" role="presentation">
                <button class="nav-link" id="my-blogs-tab" data-bs-toggle="pill" data-bs-target="#my-blogs-pane" type="button" role="tab" aria-controls="my-blogs-pane" aria-selected="false"></button>
            </li>
        @endif
    </ul>

    <!-- Tab Contents -->
    <div class="tab-content" id="blogTabContent">
        @if(!$activeCategories->isEmpty())
            @foreach($activeCategories as $index => $cat)
                @php
                    $catBlogs = $blogs->filter(function($b) use ($cat) {
                        return $b->blog_type == $cat->id || (is_string($b->blog_type) && trim($b->blog_type) === $cat->name);
                    });
                @endphp
                <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="pane-{{ Str::slug($cat->name) }}" role="tabpanel" aria-labelledby="tab-{{ Str::slug($cat->name) }}" tabindex="0">
                    @if($catBlogs->isEmpty())
                        <div class="text-center py-5">
                            <img src="https://illustrations.popsy.co/pink/creative-writing.svg" alt="No blogs" style="max-height: 200px;" class="mb-4">
                            <h3 class="text-muted">No Articles Found</h3>
                            <p class="text-muted">Be the first to publish a stories or community update!</p>
                        </div>
                    @else
                        <div class="row g-4">
                            @foreach($catBlogs as $blog)
                                <div class="col-6 col-md-6 col-lg-4">
                                    <div class="card h-100 border-0 shadow-sm blog-card position-relative overflow-hidden" style="border-radius: 20px; transition: transform 0.3s ease, box-shadow 0.3s ease;">
                                        <!-- Media Section -->
                                        @if($blog->media_path)
                                            @php
                                                $mediaList = is_array($blog->media_path) ? $blog->media_path : json_decode($blog->media_path, true);
                                            @endphp
                                            <div class="blog-media-container" style="height: 200px; overflow: hidden; position: relative;">
                                                @if(is_array($mediaList) && count($mediaList) > 1)
                                                    <div id="cardCarousel-{{ $blog->id }}" class="carousel slide carousel-fade h-100 w-100" data-bs-ride="carousel" data-bs-interval="3000">
                                                        <div class="carousel-indicators" style="bottom: 10px; margin-bottom: 0; z-index: 15;">
                                                            @foreach($mediaList as $mIdx => $mPath)
                                                                <button type="button" data-bs-target="#cardCarousel-{{ $blog->id }}" data-bs-slide-to="{{ $mIdx }}" class="{{ $mIdx === 0 ? 'active' : '' }}" aria-current="{{ $mIdx === 0 ? 'true' : 'false' }}" style="width: 8px; height: 8px; border-radius: 50%; margin: 0 4px; background-color: #fff; border: 1px solid rgba(0,0,0,0.25);"></button>
                                                            @endforeach
                                                        </div>
                                                        <div class="carousel-inner h-100">
                                                            @foreach($mediaList as $idx => $mPath)
                                                                @php
                                                                    $ext = strtolower(pathinfo($mPath, PATHINFO_EXTENSION));
                                                                    $isVid = in_array($ext, ['mp4', 'mov', 'avi', 'webm', 'ogg']);
                                                                @endphp
                                                                <div class="carousel-item h-100 {{ $idx === 0 ? 'active' : '' }}">
                                                                    @if($isVid)
                                                                        <video src="{{ asset('storage/' . $mPath) }}" muted loop playsinline autoplay class="w-100 h-100 object-fit-cover"></video>
                                                                    @else
                                                                        <img src="{{ asset('storage/' . $mPath) }}" alt="Media slide" class="w-100 h-100 object-fit-cover">
                                                                    @endif
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @else
                                                    @php
                                                        $singlePath = is_array($mediaList) ? ($mediaList[0] ?? null) : $blog->media_path;
                                                    @endphp
                                                    @if($singlePath)
                                                        @php
                                                            $ext = strtolower(pathinfo($singlePath, PATHINFO_EXTENSION));
                                                            $isVid = in_array($ext, ['mp4', 'mov', 'avi', 'webm', 'ogg']);
                                                        @endphp
                                                        @if($isVid)
                                                            <video src="{{ asset('storage/' . $singlePath) }}" muted playsinline autoplay loop class="w-100 h-100 object-fit-cover"></video>
                                                            <span class="badge bg-dark bg-opacity-75 position-absolute top-3 right-3 m-3"><i class="fa-solid fa-video me-1"></i>Video</span>
                                                        @else
                                                            <img src="{{ asset('storage/' . $singlePath) }}" alt="{{ $blog->title }}" class="w-100 h-100 object-fit-cover">
                                                        @endif
                                                    @else
                                                        <div class="bg-light d-flex align-items-center justify-content-center text-muted h-100">
                                                            <i class="fa-solid fa-newspaper fa-3x opacity-25"></i>
                                                        </div>
                                                    @endif
                                                @endif
                                            </div>
                                        @else
                                            <div class="blog-media-container bg-light d-flex align-items-center justify-content-center text-muted" style="height: 200px;">
                                                <i class="fa-solid fa-newspaper fa-3x opacity-25"></i>
                                            </div>
                                        @endif

                                        <!-- Body -->
                                        <div class="card-body d-flex flex-column p-4">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="small text-muted"><i class="fa-regular fa-clock me-1"></i>{{ $blog->created_at->format('M d, Y') }}</span>
                                                <div class="d-flex align-items-center gap-1">
                                                    @php
                                                        $userLiked = $blog->likedBy($user->id);
                                                    @endphp
                                                    <button class="btn btn-sm btn-like border-0 bg-transparent p-0 d-flex align-items-center text-muted" data-id="{{ $blog->id }}" style="transition: transform 0.2s ease;">
                                                        <i class="fa-{{ $userLiked ? 'solid text-danger' : 'regular' }} fa-heart fa-lg me-1"></i>
                                                        <span class="likes-count fw-bold" style="font-size:0.9rem;">{{ $blog->likes_count }}</span>
                                                    </button>
                                                </div>
                                            </div>

                                            <h5 class="fw-bold text-dark mb-2 text-truncate-2" style="line-height: 1.4; height: 2.8rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                                <a href="{{ route('blogs.show', $blog->id) }}" class="text-decoration-none text-dark hover-primary">{{ $blog->title }}</a>
                                            </h5>
                                            
                                            <p class="text-muted small mb-3 text-truncate-3" style="height: 3.6rem; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                                                {{ strip_tags($blog->description) }}
                                            </p>

                                            <div class="mt-auto pt-3 border-top d-flex align-items-center justify-content-between">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-circle-sm bg-primary text-white d-flex align-items-center justify-content-center me-2 fw-bold" style="width:32px; height:32px; border-radius:50%; font-size:0.85rem;">
                                                        {{ strtoupper(substr($blog->user->name ?? 'U', 0, 1)) }}
                                                    </div>
                                                    <span class="small fw-semibold text-secondary">{{ $blog->user->name ?? 'Anonymous' }}</span>
                                                </div>
                                                <a href="{{ route('blogs.show', $blog->id) }}" class="btn btn-link text-primary p-0 small fw-bold text-decoration-none">Read More <i class="fa-solid fa-arrow-right ms-1"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        @else
            <div class="tab-pane fade show active" id="explore-pane" role="tabpanel" aria-labelledby="explore-tab" tabindex="0">
                <div class="text-center py-5">
                    <img src="https://illustrations.popsy.co/pink/creative-writing.svg" alt="No blogs" style="max-height: 200px;" class="mb-4">
                    <h3 class="text-muted">No Articles Found</h3>
                    <p class="text-muted">Be the first to publish a story or community update!</p>
                </div>
            </div>
        @endif

        @if($user->blog_access)
            <!-- My Publications Tab Pane -->
            <div class="tab-pane fade" id="my-blogs-pane" role="tabpanel" aria-labelledby="my-blogs-tab" tabindex="0">
                @if($myBlogs->isEmpty())
                    <div class="text-center py-5">
                        <img src="https://illustrations.popsy.co/pink/remote-work.svg" alt="No blogs" style="max-height: 200px;" class="mb-4">
                        <h3 class="text-muted">You haven't written any blogs yet</h3>
                        <p class="text-muted">Tap on the 'Write a Blog' button to publish your first article.</p>
                    </div>
                @else
                    <div class="row g-4">
                        @foreach($myBlogs as $blog)
                            <div class="col-6 col-md-6 col-lg-4">
                                <div class="card h-100 border-0 shadow-sm position-relative overflow-hidden" style="border-radius: 20px;">
                                    <!-- Media Section -->
                                    @if($blog->media_path)
                                        @php
                                            $mediaList = is_array($blog->media_path) ? $blog->media_path : json_decode($blog->media_path, true);
                                        @endphp
                                        <div style="height: 180px; overflow: hidden; position: relative;">
                                            @if(is_array($mediaList) && count($mediaList) > 1)
                                                <div id="pubCarousel-{{ $blog->id }}" class="carousel slide carousel-fade h-100 w-100" data-bs-ride="carousel" data-bs-interval="3000">
                                                    <div class="carousel-inner h-100">
                                                        @foreach($mediaList as $idx => $mPath)
                                                            @php
                                                                $ext = strtolower(pathinfo($mPath, PATHINFO_EXTENSION));
                                                                $isVid = in_array($ext, ['mp4', 'mov', 'avi', 'webm', 'ogg']);
                                                            @endphp
                                                            <div class="carousel-item h-100 {{ $idx === 0 ? 'active' : '' }}">
                                                                @if($isVid)
                                                                    <video src="{{ asset('storage/' . $mPath) }}" muted loop playsinline autoplay class="w-100 h-100 object-fit-cover"></video>
                                                                @else
                                                                    <img src="{{ asset('storage/' . $mPath) }}" alt="Media slide" class="w-100 h-100 object-fit-cover">
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @else
                                                @php
                                                    $singlePath = is_array($mediaList) ? ($mediaList[0] ?? null) : $blog->media_path;
                                                @endphp
                                                @if($singlePath)
                                                    @php
                                                        $ext = strtolower(pathinfo($singlePath, PATHINFO_EXTENSION));
                                                        $isVid = in_array($ext, ['mp4', 'mov', 'avi', 'webm', 'ogg']);
                                                    @endphp
                                                    @if($isVid)
                                                        <video src="{{ asset('storage/' . $singlePath) }}" muted playsinline autoplay loop class="w-100 h-100 object-fit-cover"></video>
                                                        <span class="badge bg-dark bg-opacity-75 position-absolute top-3 right-3 m-3"><i class="fa-solid fa-video me-1"></i>Video</span>
                                                    @else
                                                        <img src="{{ asset('storage/' . $singlePath) }}" alt="{{ $blog->title }}" class="w-100 h-100 object-fit-cover">
                                                    @endif
                                                @else
                                                    <div class="bg-light d-flex align-items-center justify-content-center text-muted h-100">
                                                        <i class="fa-solid fa-newspaper fa-3x opacity-25"></i>
                                                    </div>
                                                @endif
                                            @endif
                                            <span class="badge {{ $blog->is_active ? 'bg-success' : 'bg-secondary' }} position-absolute top-0 left-0 m-3 shadow" style="z-index: 10;">
                                                {{ $blog->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </div>
                                    @else
                                        <div class="bg-light d-flex align-items-center justify-content-center text-muted position-relative" style="height: 180px;">
                                            <i class="fa-solid fa-newspaper fa-3x opacity-25"></i>
                                            <span class="badge {{ $blog->is_active ? 'bg-success' : 'bg-secondary' }} position-absolute top-0 left-0 m-3 shadow">
                                                {{ $blog->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </div>
                                    @endif

                                    <!-- Body -->
                                    <div class="card-body d-flex flex-column p-4">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="small text-muted"><i class="fa-regular fa-clock me-1"></i>{{ $blog->created_at->format('M d, Y') }}</span>
                                            <span class="small text-danger fw-bold"><i class="fa-solid fa-heart me-1"></i>{{ $blog->likes_count }} Likes</span>
                                        </div>

                                        <h5 class="fw-bold text-dark mb-2 text-truncate-2" style="height: 2.8rem; overflow: hidden;">
                                            <a href="{{ route('blogs.show', $blog->id) }}" class="text-decoration-none text-dark hover-primary">{{ $blog->title }}</a>
                                        </h5>

                                        <div class="mt-auto pt-3 border-top d-flex gap-2">
                                            <a href="{{ route('blogs.show', $blog->id) }}" class="btn btn-sm btn-outline-primary flex-grow-1 rounded-pill"><i class="fa-solid fa-eye"></i></a>
                                            <a href="{{ route('blogs.edit', $blog->id) }}" class="btn btn-sm btn-outline-secondary flex-grow-1 rounded-pill"><i class="fa-solid fa-pen"></i></a>
                                            <form action="{{ route('blogs.destroy', $blog->id) }}" method="POST" class="flex-grow-1 m-0">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger w-100 rounded-pill"><i class="fa-solid fa-trash"></i></button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>

<style>
    #blogTabs {
        display: flex !important;
        flex-wrap: nowrap !important;
        overflow-x: auto !important;
        justify-content: start !important;
        padding: 8px 16px 16px 16px !important;
        -webkit-overflow-scrolling: touch;
        max-width: 100%;
        scrollbar-width: thin;
        scrollbar-color: var(--primary) rgba(0, 0, 0, 0.05);
    }
    #blogTabs::-webkit-scrollbar {
        height: 6px !important;
        display: block !important;
    }
    #blogTabs::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.05) !important;
        border-radius: 10px !important;
    }
    #blogTabs::-webkit-scrollbar-thumb {
        background: var(--primary) !important;
        border-radius: 10px !important;
    }
    #blogTabs::-webkit-scrollbar-thumb:hover {
        background: var(--primary-dark, #ff2a3b) !important;
    }
    #blogTabs .nav-item {
        flex-shrink: 0 !important;
        white-space: nowrap !important;
    }
    .nav-pills .nav-link {
        border: 2px solid var(--primary) !important;
        color: var(--primary) !important;
        background: transparent !important;
        transition: all 0.3s ease;
        font-weight: 700;
    }
    .nav-pills .nav-link.active {
        background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%) !important;
        color: white !important;
        border-color: transparent !important;
        box-shadow: 0 4px 15px rgba(255, 71, 87, 0.3) !important;
    }
    .nav-pills .nav-link:hover:not(.active) {
        background: rgba(255, 71, 87, 0.05) !important;
        color: var(--primary) !important;
    }
    .blog-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 35px rgba(255, 71, 87, 0.1) !important;
    }
    .hover-primary:hover {
        color: var(--primary) !important;
    }
    .btn-like:active {
        transform: scale(1.2);
    }

    /* Mobile Responsive Overrides for grid-6 matching */
    @media (max-width: 576px) {
        .blog-media-container {
            height: 120px !important;
        }
        .blog-card .card-body {
            padding: 12px !important;
        }
        .blog-card h5 {
            font-size: 0.82rem !important;
            height: 2.2rem !important;
            -webkit-line-clamp: 2 !important;
            line-height: 1.3 !important;
            margin-bottom: 4px !important;
        }
        .blog-card p.text-muted {
            font-size: 0.72rem !important;
            height: 2rem !important;
            -webkit-line-clamp: 2 !important;
            margin-bottom: 8px !important;
        }
        .blog-card .avatar-circle-sm {
            width: 24px !important;
            height: 24px !important;
            font-size: 0.7rem !important;
        }
        .blog-card .small {
            font-size: 0.68rem !important;
        }
        .blog-card .btn-link {
            font-size: 0.68rem !important;
        }
        .blog-card .border-top {
            padding-top: 8px !important;
            margin-top: 8px !important;
        }
        .likes-count {
            font-size: 0.75rem !important;
        }
        /* Publications specific height fix */
        .tab-pane #my-blogs-pane div[style*="height: 180px"] {
            height: 120px !important;
        }
    }
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Like button AJAX handler
    document.querySelectorAll('.btn-like').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const btn = this;
            const blogId = btn.getAttribute('data-id');
            
            // Check if already processing a request for this button
            if (btn.isProcessing) return;
            
            const icon = btn.querySelector('i');
            const countSpan = btn.querySelector('.likes-count');
            
            // Optimistic UI updates
            const isCurrentlyLiked = icon.classList.contains('fa-solid');
            let currentCount = parseInt(countSpan.textContent) || 0;
            
            if (isCurrentlyLiked) {
                // Optimistically Unlike
                icon.classList.remove('fa-solid', 'text-danger');
                icon.classList.add('fa-regular');
                countSpan.textContent = Math.max(0, currentCount - 1);
            } else {
                // Optimistically Like
                icon.classList.remove('fa-regular');
                icon.classList.add('fa-solid', 'text-danger');
                // Micro-animation trigger
                btn.style.transform = 'scale(1.25)';
                setTimeout(() => { btn.style.transform = ''; }, 200);
                countSpan.textContent = currentCount + 1;
            }
            
            btn.isProcessing = true;
            
            fetch(`/dashboard/blogs/${blogId}/like`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Sync count and final state from server
                    countSpan.textContent = data.likes_count;
                    if (data.liked) {
                        icon.classList.remove('fa-regular');
                        icon.classList.add('fa-solid', 'text-danger');
                    } else {
                        icon.classList.remove('fa-solid', 'text-danger');
                        icon.classList.add('fa-regular');
                    }
                } else {
                    // Revert optimistic changes on failure
                    if (isCurrentlyLiked) {
                        icon.classList.remove('fa-regular');
                        icon.classList.add('fa-solid', 'text-danger');
                        countSpan.textContent = currentCount;
                    } else {
                        icon.classList.remove('fa-solid', 'text-danger');
                        icon.classList.add('fa-regular');
                        countSpan.textContent = currentCount;
                    }
                }
                btn.isProcessing = false;
            })
            .catch(err => {
                console.error(err);
                // Revert optimistic changes on error
                if (isCurrentlyLiked) {
                    icon.classList.remove('fa-regular');
                    icon.classList.add('fa-solid', 'text-danger');
                    countSpan.textContent = currentCount;
                } else {
                    icon.classList.remove('fa-solid', 'text-danger');
                    icon.classList.add('fa-regular');
                    countSpan.textContent = currentCount;
                }
                btn.isProcessing = false;
            });
        });
    });

    // Relocated Publications button click handler to switch Bootstrap tabs
    const myPubBtn = document.getElementById('my-publications-btn');
    const myBlogsTab = document.getElementById('my-blogs-tab');
    if (myPubBtn && myBlogsTab) {
        myPubBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Deactivate all active category tabs/links
            document.querySelectorAll('#blogTabs .nav-link').forEach(link => {
                link.classList.remove('active');
                link.setAttribute('aria-selected', 'false');
            });
            
            // Explicitly hide all other tab panes to clear the screen
            document.querySelectorAll('#blogTabContent .tab-pane').forEach(pane => {
                pane.classList.remove('show', 'active');
            });
            
            // Show only the user's publications pane
            const myBlogsPane = document.getElementById('my-blogs-pane');
            if (myBlogsPane) {
                myBlogsPane.classList.add('show', 'active');
            }
            
            // Smoothly scroll down to the content pane
            document.getElementById('blogTabContent').scrollIntoView({ behavior: 'smooth' });
        });
    }

    // Deactivate and hide publications pane when any category tab is clicked
    document.querySelectorAll('#blogTabs .nav-link').forEach(link => {
        link.addEventListener('click', function() {
            const myBlogsPane = document.getElementById('my-blogs-pane');
            if (myBlogsPane) {
                myBlogsPane.classList.remove('show', 'active');
            }
        });
    });

    // Mouse drag scrolling & dynamic centering for horizontal category tabs
    const tabEl = document.getElementById('blogTabs');
    if (tabEl) {
        let isDown = false;
        let startX;
        let scrollLeft;

        tabEl.addEventListener('mousedown', (e) => {
            isDown = true;
            startX = e.pageX - tabEl.offsetLeft;
            scrollLeft = tabEl.scrollLeft;
            tabEl.style.cursor = 'grabbing';
        });
        tabEl.addEventListener('mouseleave', () => {
            isDown = false;
            tabEl.style.cursor = 'grab';
        });
        tabEl.addEventListener('mouseup', () => {
            isDown = false;
            tabEl.style.cursor = 'grab';
        });
        tabEl.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - tabEl.offsetLeft;
            const walk = (x - startX) * 1.5; // multiplier
            tabEl.scrollLeft = scrollLeft - walk;
        });
        tabEl.style.cursor = 'grab';

        // Dynamically center tabs if they fit within the viewport, otherwise align left
        const adjustTabsAlignment = () => {
            if (tabEl.scrollWidth > tabEl.clientWidth) {
                tabEl.style.setProperty('justify-content', 'flex-start', 'important');
            } else {
                tabEl.style.setProperty('justify-content', 'center', 'important');
            }
        };

        // Run alignment checks on load and window resize
        adjustTabsAlignment();
        window.addEventListener('resize', adjustTabsAlignment);
    }
});
</script>
@endsection
