@extends('layouts.app')

@section('title', $blog->title . ' — Mali Setu')

@section('content')
<div class="container py-4">
    <!-- Back to Portal Link -->
    <div class="mb-4">
        <a href="{{ route('blogs.index') }}" class="text-primary text-decoration-none fw-bold small">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Blog Portal
        </a>
    </div>

    <div class="row g-4">
        <!-- Main Article Column -->
        <div class="col-lg-8">
            <article class="card border-0 shadow-sm overflow-hidden" style="border-radius: 24px; background: #fff;">
                <!-- Article Header Image/Video -->
                @if($blog->media_path)
                    <div class="article-media-wrapper position-relative" style="max-height: 480px; overflow: hidden; background: #000;">
                        @if($blog->media_type === 'video')
                            <video src="{{ asset('storage/' . $blog->media_path) }}" controls class="w-100 h-100 object-fit-contain" style="max-height:480px;"></video>
                        @else
                            <img src="{{ asset('storage/' . $blog->media_path) }}" alt="{{ $blog->title }}" class="w-100 h-100 object-fit-cover" style="max-height:480px; object-position: center;">
                        @endif
                    </div>
                @endif

                <!-- Article Body -->
                <div class="card-body p-4 p-md-5">
                    <!-- Category/Tags -->
                    <div class="mb-3 d-flex flex-wrap gap-2">
                        @if($blog->tags)
                            @foreach($blog->tags as $tag)
                                <a href="{{ route('blogs.index', ['tag' => $tag]) }}" class="badge bg-primary bg-opacity-10 text-primary text-decoration-none px-3 py-2 rounded-pill small">
                                    #{{ $tag }}
                                </a>
                            @endforeach
                        @endif
                    </div>

                    <!-- Title -->
                    <h1 class="fw-black text-dark mb-4" style="font-size: 2.2rem; line-height: 1.3; font-weight: 800;">
                        {{ $blog->title }}
                    </h1>

                    <!-- Author & Metadata Card -->
                    <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded-4 mb-4 flex-wrap gap-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar bg-primary text-white d-flex align-items-center justify-content-center fw-bold me-3 shadow-sm" style="width:48px; height:48px; border-radius:50%; font-size:1.1rem;">
                                {{ strtoupper(substr($blog->user->name ?? 'U', 0, 1)) }}
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold text-dark">{{ $blog->user->name ?? 'Anonymous' }}</h6>
                                <span class="small text-muted"><i class="fa-regular fa-clock me-1"></i>Published {{ $blog->created_at->format('M d, Y \a\t h:i A') }}</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <!-- AJAX Like -->
                            <button class="btn btn-like-large px-4 py-2 rounded-pill border-0 d-flex align-items-center gap-2 shadow-sm" data-id="{{ $blog->id }}" style="background: rgba(220, 53, 69, 0.1); color: #dc3545; transition: all 0.2s ease;">
                                <i class="fa-{{ $isLiked ? 'solid' : 'regular' }} fa-heart fa-lg"></i>
                                <span class="likes-count fw-bold">{{ $blog->likes_count }}</span>
                            </button>
                        </div>
                    </div>

                    <!-- Article Content Description -->
                    <div class="article-content text-secondary" style="font-size: 1.1rem; line-height: 1.8;">
                        {!! nl2br(e($blog->description)) !!}
                    </div>
                </div>
            </article>
        </div>

        <!-- Sidebar / Related Articles -->
        <div class="col-lg-4">
            <!-- Share Card -->
            <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 20px; background: #fff;">
                <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-share-nodes me-2 text-primary"></i>Share Article</h5>
                <p class="text-muted small">Spread the word about this community story.</p>
                <div class="d-flex gap-2">
                    <button onclick="copyArticleUrl()" class="btn btn-light rounded-circle shadow-sm" style="width: 44px; height: 44px;" title="Copy Link"><i class="fa-solid fa-link"></i></button>
                    <a href="https://api.whatsapp.com/send?text={{ urlencode($blog->title . ' - ' . url()->current()) }}" target="_blank" class="btn btn-light rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center" style="width: 44px; height: 44px; color:#25D366;" title="Share on WhatsApp"><i class="fa-brands fa-whatsapp fa-lg"></i></a>
                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($blog->title) }}" target="_blank" class="btn btn-light rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center" style="width: 44px; height: 44px; color:#1DA1F2;" title="Share on X"><i class="fa-brands fa-x-twitter fa-lg"></i></a>
                </div>
            </div>

            <!-- Related Articles Card -->
            <div class="card border-0 shadow-sm p-4" style="border-radius: 20px; background: #fff;">
                <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-fire me-2 text-primary"></i>Keep Reading</h5>
                <hr class="mt-0 mb-3 opacity-10">

                <div class="d-flex flex-column gap-3">
                    @foreach($related as $rel)
                        <div class="d-flex align-items-center gap-3 py-2 border-bottom border-light last-border-0">
                            @if($rel->media_path)
                                <div style="width: 70px; height: 70px; border-radius: 12px; overflow: hidden; flex-shrink: 0;">
                                    @if($rel->media_type === 'video')
                                        <video src="{{ asset('storage/' . $rel->media_path) }}" muted class="w-100 h-100 object-fit-cover"></video>
                                    @else
                                        <img src="{{ asset('storage/' . $rel->media_path) }}" alt="{{ $rel->title }}" class="w-100 h-100 object-fit-cover">
                                    @endif
                                </div>
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center text-muted" style="width: 70px; height: 70px; border-radius: 12px; flex-shrink: 0;">
                                    <i class="fa-solid fa-newspaper opacity-20"></i>
                                </div>
                            @endif
                            <div class="overflow-hidden">
                                <h6 class="fw-bold text-dark mb-1 text-truncate" style="font-size: 0.95rem;">
                                    <a href="{{ route('blogs.show', $rel->id) }}" class="text-decoration-none text-dark hover-primary">{{ $rel->title }}</a>
                                </h6>
                                <span class="small text-muted d-block" style="font-size: 0.8rem;">{{ $rel->created_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .btn-like-large:hover {
        transform: scale(1.05);
        background: #dc3545 !important;
        color: #fff !important;
    }
    .hover-primary:hover {
        color: var(--primary) !important;
    }
    .last-border-0:last-child {
        border-bottom: 0 !important;
    }
</style>
@endsection

@section('scripts')
<script>
function copyArticleUrl() {
    navigator.clipboard.writeText(window.location.href);
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: 'Article link copied to clipboard!',
        showConfirmButton: false,
        timer: 3000,
        background: 'rgba(255, 255, 255, 0.95)',
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const likeBtn = document.querySelector('.btn-like-large');
    if (likeBtn) {
        likeBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const btn = this;
            const blogId = btn.getAttribute('data-id');
            
            if (btn.isProcessing) return;
            
            const icon = btn.querySelector('i');
            const countSpan = btn.querySelector('.likes-count');
            
            // Optimistic UI updates
            const isCurrentlyLiked = icon.classList.contains('fa-solid');
            let currentCount = parseInt(countSpan.textContent) || 0;
            
            if (isCurrentlyLiked) {
                // Optimistically Unlike
                icon.classList.remove('fa-solid');
                icon.classList.add('fa-regular');
                btn.style.background = 'rgba(220, 53, 69, 0.1)';
                btn.style.color = '#dc3545';
                countSpan.textContent = Math.max(0, currentCount - 1);
            } else {
                // Optimistically Like
                icon.classList.remove('fa-regular');
                icon.classList.add('fa-solid');
                btn.style.background = '#dc3545';
                btn.style.color = '#fff';
                // Trigger micro-animation scale
                btn.style.transform = 'scale(1.1)';
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
                    countSpan.textContent = data.likes_count;
                    if (data.liked) {
                        icon.classList.remove('fa-regular');
                        icon.classList.add('fa-solid');
                        btn.style.background = '#dc3545';
                        btn.style.color = '#fff';
                    } else {
                        icon.classList.remove('fa-solid');
                        icon.classList.add('fa-regular');
                        btn.style.background = 'rgba(220, 53, 69, 0.1)';
                        btn.style.color = '#dc3545';
                    }
                } else {
                    // Revert optimistic changes
                    if (isCurrentlyLiked) {
                        icon.classList.remove('fa-regular');
                        icon.classList.add('fa-solid');
                        btn.style.background = '#dc3545';
                        btn.style.color = '#fff';
                        countSpan.textContent = currentCount;
                    } else {
                        icon.classList.remove('fa-solid');
                        icon.classList.add('fa-regular');
                        btn.style.background = 'rgba(220, 53, 69, 0.1)';
                        btn.style.color = '#dc3545';
                        countSpan.textContent = currentCount;
                    }
                }
                btn.isProcessing = false;
            })
            .catch(err => {
                console.error(err);
                // Revert optimistic changes
                if (isCurrentlyLiked) {
                    icon.classList.remove('fa-regular');
                    icon.classList.add('fa-solid');
                    btn.style.background = '#dc3545';
                    btn.style.color = '#fff';
                    countSpan.textContent = currentCount;
                } else {
                    icon.classList.remove('fa-solid');
                    icon.classList.add('fa-regular');
                    btn.style.background = 'rgba(220, 53, 69, 0.1)';
                    btn.style.color = '#dc3545';
                    countSpan.textContent = currentCount;
                }
                btn.isProcessing = false;
            });
        });
    }
});
</script>
@endsection
