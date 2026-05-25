@extends('layouts.app')

@section('title', 'Blog Portal — Mali Setu')

@section('content')
<div class="container py-4">
    <!-- Header Area -->
    <div class="row align-items-center mb-4">
        <div class="col-md-8">
            <h1 class="fw-extrabold text-primary mb-1">
                <i class="fa-solid fa-blog me-2"></i>Community Blog Portal
            </h1>
            <p class="text-muted mb-0">Share your stories, knowledge, and updates with the community.</p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            @if($user->blog_access)
                <a href="{{ route('blogs.create') }}" class="btn btn-primary px-4 py-2 rounded-pill shadow-sm">
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
        <li class="nav-item" role="presentation">
            <button class="nav-link active rounded-pill px-4" id="explore-tab" data-bs-toggle="pill" data-bs-target="#explore-pane" type="button" role="tab" aria-controls="explore-pane" aria-selected="true">
                <i class="fa-solid fa-compass me-2"></i>Explore Articles
            </button>
        </li>
        @if($user->blog_access)
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill px-4" id="my-blogs-tab" data-bs-toggle="pill" data-bs-target="#my-blogs-pane" type="button" role="tab" aria-controls="my-blogs-pane" aria-selected="false">
                    <i class="fa-solid fa-book-open me-2"></i>My Publications ({{ count($myBlogs) }})
                </button>
            </li>
        @endif
    </ul>

    <!-- Tab Contents -->
    <div class="tab-content" id="blogTabContent">
        <!-- Explore Tab Pane -->
        <div class="tab-pane fade show active" id="explore-pane" role="tabpanel" aria-labelledby="explore-tab" tabindex="0">
            @if($blogs->isEmpty())
                <div class="text-center py-5">
                    <img src="https://illustrations.popsy.co/pink/creative-writing.svg" alt="No blogs" style="max-height: 200px;" class="mb-4">
                    <h3 class="text-muted">No Articles Found</h3>
                    <p class="text-muted">Be the first to publish a stories or community update!</p>
                </div>
            @else
                <div class="row g-4">
                    @foreach($blogs as $blog)
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 border-0 shadow-sm blog-card position-relative overflow-hidden" style="border-radius: 20px; transition: transform 0.3s ease, box-shadow 0.3s ease;">
                                <!-- Media Section -->
                                @if($blog->media_path)
                                    <div class="blog-media-container" style="height: 200px; overflow: hidden; position: relative;">
                                        @if($blog->media_type === 'video')
                                            <video src="{{ asset('storage/' . $blog->media_path) }}" muted class="w-100 h-100 object-fit-cover"></video>
                                            <span class="badge bg-dark bg-opacity-75 position-absolute top-3 right-3 m-3"><i class="fa-solid fa-video me-1"></i>Video</span>
                                        @else
                                            <img src="{{ asset('storage/' . $blog->media_path) }}" alt="{{ $blog->title }}" class="w-100 h-100 object-fit-cover">
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

                <div class="mt-5 d-flex justify-content-center">
                    {{ $blogs->links() }}
                </div>
            @endif
        </div>

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
                            <div class="col-md-6 col-lg-4">
                                <div class="card h-100 border-0 shadow-sm position-relative overflow-hidden" style="border-radius: 20px;">
                                    <!-- Media Section -->
                                    @if($blog->media_path)
                                        <div style="height: 180px; overflow: hidden; position: relative;">
                                            @if($blog->media_type === 'video')
                                                <video src="{{ asset('storage/' . $blog->media_path) }}" muted class="w-100 h-100 object-fit-cover"></video>
                                                <span class="badge bg-dark bg-opacity-75 position-absolute top-3 right-3 m-3"><i class="fa-solid fa-video me-1"></i>Video</span>
                                            @else
                                                <img src="{{ asset('storage/' . $blog->media_path) }}" alt="{{ $blog->title }}" class="w-100 h-100 object-fit-cover">
                                            @endif
                                            <span class="badge {{ $blog->is_active ? 'bg-success' : 'bg-secondary' }} position-absolute top-0 left-0 m-3 shadow">
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
                                            <a href="{{ route('blogs.show', $blog->id) }}" class="btn btn-sm btn-outline-primary flex-grow-1 rounded-pill"><i class="fa-solid fa-eye me-1"></i>View</a>
                                            <a href="{{ route('blogs.edit', $blog->id) }}" class="btn btn-sm btn-outline-secondary flex-grow-1 rounded-pill"><i class="fa-solid fa-pen me-1"></i>Edit</a>
                                            <form action="{{ route('blogs.destroy', $blog->id) }}" method="POST" class="flex-grow-1 m-0">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger w-100 rounded-pill"><i class="fa-solid fa-trash me-1"></i>Delete</button>
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
    .blog-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 35px rgba(173, 20, 87, 0.1) !important;
    }
    .hover-primary:hover {
        color: var(--primary) !important;
    }
    .btn-like:active {
        transform: scale(1.2);
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
            
            // Disable button briefly to prevent double clicks
            btn.disabled = true;

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
                    const icon = btn.querySelector('i');
                    const countSpan = btn.querySelector('.likes-count');
                    
                    if (data.liked) {
                        icon.classList.remove('fa-regular');
                        icon.classList.add('fa-solid', 'text-danger');
                    } else {
                        icon.classList.remove('fa-solid', 'text-danger');
                        icon.classList.add('fa-regular');
                    }
                    
                    countSpan.textContent = data.likes_count;
                }
                btn.disabled = false;
            })
            .catch(err => {
                console.error(err);
                btn.disabled = false;
            });
        });
    });
});
</script>
@endsection
