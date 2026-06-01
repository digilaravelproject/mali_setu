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
                    @php
                        $mediaList = is_array($blog->media_path) ? $blog->media_path : json_decode($blog->media_path, true);
                    @endphp
                    <div class="article-media-wrapper position-relative" style="max-height: 480px; overflow: hidden; background: #000;">
                        @if(is_array($mediaList) && count($mediaList) > 1)
                            <div id="detailCarousel-{{ $blog->id }}" class="carousel slide carousel-fade h-100 w-100" data-bs-ride="carousel" data-bs-interval="3000">
                                <div class="carousel-inner h-100" style="max-height: 480px;">
                                    @foreach($mediaList as $idx => $mPath)
                                        @php
                                            $ext = strtolower(pathinfo($mPath, PATHINFO_EXTENSION));
                                            $isVid = in_array($ext, ['mp4', 'mov', 'avi', 'webm', 'ogg']);
                                        @endphp
                                        <div class="carousel-item h-100 {{ $idx === 0 ? 'active' : '' }}">
                                            @if($isVid)
                                                <video src="{{ asset('storage/' . $mPath) }}" controls muted loop playsinline class="d-block w-100 h-100 object-fit-contain" style="max-height: 480px;"></video>
                                            @else
                                                <img src="{{ asset('storage/' . $mPath) }}" alt="Media slide" class="d-block w-100 h-100 object-fit-cover" style="max-height: 480px; object-position: center;">
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                                <button class="carousel-control-prev" type="button" data-bs-target="#detailCarousel-{{ $blog->id }}" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Previous</span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#detailCarousel-{{ $blog->id }}" data-bs-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Next</span>
                                </button>
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
                                    <video src="{{ asset('storage/' . $singlePath) }}" controls class="w-100 h-100 object-fit-contain" style="max-height:480px;"></video>
                                @else
                                    <img src="{{ asset('storage/' . $singlePath) }}" alt="{{ $blog->title }}" class="w-100 h-100 object-fit-cover" style="max-height:480px; object-position: center;">
                                @endif
                            @endif
                        @endif
                    </div>
                @endif

                <!-- Article Body -->
                <div class="card-body p-4 p-md-5">
                    <!-- Category/Tags -->
                    <div class="mb-3 d-flex flex-wrap gap-2">
                        @if($blog->tags)
                            @foreach($blog->tags as $tag)
                                <a href="{{ route('blogs.index', ['tag' => $tag]) }}" class="badge bg-primary bg-opacity-10 text-primary text-decoration-none px-3 py-2 rounded-pill small" style="color: white !important;">
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

                </div>
            </article>

            <!-- Comments & Discussion Module -->
            <div class="card border-0 shadow-sm mt-4 overflow-hidden" style="border-radius: 24px; background: #fff; border: 1px solid rgba(0,0,0,0.03) !important;">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-2">
                    <h5 class="fw-extrabold text-dark mb-0" style="font-weight: 800; font-size: 1.25rem;">
                        <i class="fa-solid fa-comments me-2 text-primary"></i>Discussion (<span id="total-comment-count">{{ $blog->allComments()->count() }}</span>)
                    </h5>
                </div>
                <div class="card-body p-4">
                    <!-- Write Comment Section -->
                    @auth
                        <div class="d-flex mb-4">
                            <div class="avatar bg-primary text-white d-flex align-items-center justify-content-center fw-bold me-3 shadow-sm" style="width:40px; height:40px; border-radius:50%; flex-shrink: 0; font-size: 0.95rem;">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <div class="flex-grow-1">
                                <form id="main-comment-form">
                                    @csrf
                                    <div class="form-group mb-2">
                                        <textarea id="main-comment-textarea" class="form-control border-0 bg-light p-3" rows="3" placeholder="Write a comment..." style="border-radius: 16px; resize: none; font-size: 0.95rem;" required></textarea>
                                    </div>
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill fw-bold shadow-sm" style="font-size: 0.85rem; transition: transform 0.2s;">
                                            <i class="fa-solid fa-paper-plane me-1"></i>Publish Comment
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="bg-light p-4 rounded-4 text-center mb-4">
                            <p class="text-muted mb-2 small"><i class="fa-solid fa-lock me-2 text-primary"></i>You must be logged in to participate in the discussion.</p>
                            <a href="{{ route('login') }}" class="btn btn-sm btn-primary rounded-pill px-4 fw-bold">Sign In</a>
                        </div>
                    @endauth

                    <hr class="opacity-10 my-4">

                    <!-- Comments List Container -->
                    <div id="comments-container" class="d-flex flex-column gap-4" style="max-height: 480px; overflow-y: auto; padding-right: 8px;">
                        @forelse($blog->comments as $comment)
                            <div class="comment-wrapper" id="comment-{{ $comment->id }}">
                                <!-- Parent Comment Card -->
                                <div class="d-flex align-items-start p-3 rounded-4 comment-card" style="background: #f8f9fa;">
                                    <div class="avatar bg-secondary text-white d-flex align-items-center justify-content-center fw-bold me-3" style="width:38px; height:38px; border-radius:50%; font-size: 0.9rem; flex-shrink:0;">
                                        {{ strtoupper(substr($comment->user->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.95rem;">{{ $comment->user->name ?? 'Anonymous' }}</h6>
                                            <span class="text-muted small comment-time" style="font-size: 0.75rem;">{{ $comment->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="text-secondary mb-2 style-comment-text small" style="white-space: pre-line;">{{ $comment->comment }}</p>
                                        
                                        <!-- Actions Row -->
                                        <div class="d-flex gap-3 align-items-center">
                                            <!-- Reply button (Only for blog owner on other users' comments) -->
                                            @auth
                                                @if($blog->user_id === auth()->id() && $comment->user_id !== auth()->id())
                                                    <button class="btn btn-sm btn-link text-primary p-0 fw-bold text-decoration-none btn-comment-reply" data-id="{{ $comment->id }}" style="font-size: 0.8rem;">
                                                        <i class="fa-solid fa-reply me-1"></i>Reply
                                                    </button>
                                                @endif
                                                <!-- Delete button (If comment author or blog owner) -->
                                                @if($comment->user_id === auth()->id() || $blog->user_id === auth()->id())
                                                    <button class="btn btn-sm btn-link text-danger p-0 fw-bold text-decoration-none btn-comment-delete ms-auto" data-id="{{ $comment->id }}" style="font-size: 0.8rem;">
                                                        <i class="fa-solid fa-trash me-1"></i>Delete
                                                    </button>
                                                @endif
                                            @endauth
                                        </div>

                                        <!-- Reply Form Container -->
                                        <div class="reply-form-container mt-3 d-none" id="reply-form-{{ $comment->id }}">
                                            <form class="inline-reply-form" data-parent-id="{{ $comment->id }}">
                                                @csrf
                                                <div class="d-flex">
                                                    <textarea class="form-control border-0 bg-white p-2.5 small" rows="2" placeholder="Write a reply..." style="border-radius: 12px; resize: none; font-size: 0.85rem; box-shadow: 0 0 10px rgba(0,0,0,0.03);" required></textarea>
                                                    <button type="submit" class="btn btn-primary rounded-pill px-3 py-1.5 ms-2 align-self-end fw-bold" style="font-size: 0.8rem;">Reply</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Replies List (Nested) -->
                                <div class="replies-wrapper ms-5 mt-3 d-flex flex-column gap-3" id="replies-container-{{ $comment->id }}" style="border-left: 2px dashed #e2e8f0; padding-left: 1rem;">
                                    @foreach($comment->replies as $reply)
                                        <div class="d-flex align-items-start p-3 rounded-4 reply-card" id="comment-{{ $reply->id }}" style="background: rgba(255, 71, 87, 0.03); border: 1px solid rgba(255, 71, 87, 0.05);">
                                            <div class="avatar bg-primary text-white d-flex align-items-center justify-content-center fw-bold me-3 shadow-sm" style="width:34px; height:34px; border-radius:50%; font-size: 0.8rem; flex-shrink:0;">
                                                {{ strtoupper(substr($reply->user->name ?? 'U', 0, 1)) }}
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="d-flex align-items-center justify-content-between mb-1">
                                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                                        <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.85rem;">{{ $reply->user->name ?? 'Anonymous' }}</h6>
                                                        @if($reply->user_id === $blog->user_id)
                                                            <span class="badge bg-primary text-white" style="font-size: 0.65rem; border-radius: 4px; padding: 2px 6px;">Author</span>
                                                        @endif
                                                    </div>
                                                    <span class="text-muted small" style="font-size: 0.72rem;">{{ $reply->created_at->diffForHumans() }}</span>
                                                </div>
                                                <p class="text-secondary mb-1 style-comment-text small" style="white-space: pre-line;">{{ $reply->comment }}</p>
                                                
                                                @auth
                                                    @if($reply->user_id === auth()->id() || $blog->user_id === auth()->id())
                                                        <div class="text-end">
                                                            <button class="btn btn-sm btn-link text-danger p-0 fw-bold text-decoration-none btn-comment-delete" data-id="{{ $reply->id }}" style="font-size: 0.75rem;">
                                                                <i class="fa-solid fa-trash me-1"></i>Delete
                                                            </button>
                                                        </div>
                                                    @endif
                                                @endauth
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <div id="no-comments-placeholder" class="text-center py-5">
                                <i class="fa-solid fa-comments fa-3x opacity-20 mb-3 text-secondary"></i>
                                <p class="text-muted small mb-0">No comments yet. Share your thoughts above!</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
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
                                @php
                                    $relMediaList = is_array($rel->media_path) ? $rel->media_path : json_decode($rel->media_path, true);
                                    $relSinglePath = is_array($relMediaList) ? ($relMediaList[0] ?? null) : $rel->media_path;
                                @endphp
                                @if($relSinglePath)
                                    <div style="width: 70px; height: 70px; border-radius: 12px; overflow: hidden; flex-shrink: 0;">
                                        @php
                                            $ext = strtolower(pathinfo($relSinglePath, PATHINFO_EXTENSION));
                                            $isVid = in_array($ext, ['mp4', 'mov', 'avi', 'webm', 'ogg']);
                                        @endphp
                                        @if($isVid)
                                            <video src="{{ asset('storage/' . $relSinglePath) }}" muted class="w-100 h-100 object-fit-cover"></video>
                                        @else
                                            <img src="{{ asset('storage/' . $relSinglePath) }}" alt="{{ $rel->title }}" class="w-100 h-100 object-fit-cover">
                                        @endif
                                    </div>
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center text-muted" style="width: 70px; height: 70px; border-radius: 12px; flex-shrink: 0;">
                                        <i class="fa-solid fa-newspaper opacity-20"></i>
                                    </div>
                                @endif
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
    #comments-container::-webkit-scrollbar {
        width: 6px;
    }
    #comments-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    #comments-container::-webkit-scrollbar-thumb {
        background: #ff4757;
        border-radius: 10px;
    }
    #comments-container::-webkit-scrollbar-thumb:hover {
        background: #ff2a3b;
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

    // ── Blog Comments AJAX Logic ──────────────────────────────────────────

    // Toggle Reply Form
    document.addEventListener('click', function (e) {
        if (e.target && (e.target.classList.contains('btn-comment-reply') || e.target.closest('.btn-comment-reply'))) {
            e.preventDefault();
            const btn = e.target.classList.contains('btn-comment-reply') ? e.target : e.target.closest('.btn-comment-reply');
            const commentId = btn.getAttribute('data-id');
            const formContainer = document.getElementById(`reply-form-${commentId}`);
            if (formContainer) {
                formContainer.classList.toggle('d-none');
                if (!formContainer.classList.contains('d-none')) {
                    formContainer.querySelector('textarea').focus();
                }
            }
        }
    });

    // Submit Main Comment
    const mainCommentForm = document.getElementById('main-comment-form');
    if (mainCommentForm) {
        mainCommentForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const textarea = document.getElementById('main-comment-textarea');
            const commentText = textarea.value.trim();
            const btn = this.querySelector('button[type="submit"]');

            if (!commentText) return;

            btn.disabled = true;
            const originalBtnContent = btn.innerHTML;
            btn.innerHTML = `<i class="fa-solid fa-circle-notch fa-spin me-1"></i>Publishing...`;

            fetch(`/dashboard/blogs/{{ $blog->id }}/comments`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ comment: commentText })
            })
            .then(response => response.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = originalBtnContent;

                if (data.success) {
                    textarea.value = '';
                    // Remove empty placeholder if present
                    const placeholder = document.getElementById('no-comments-placeholder');
                    if (placeholder) {
                        placeholder.remove();
                    }

                    // Render new comment card dynamically
                    const commentHtml = `
                        <div class="comment-wrapper" id="comment-${data.data.id}">
                            <div class="d-flex align-items-start p-3 rounded-4 comment-card" style="background: #f8f9fa;">
                                <div class="avatar bg-secondary text-white d-flex align-items-center justify-content-center fw-bold me-3" style="width:38px; height:38px; border-radius:50%; font-size: 0.9rem; flex-shrink:0;">
                                    ${data.data.user.initial}
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.95rem;">${data.data.user.name}</h6>
                                        <span class="text-muted small comment-time" style="font-size: 0.75rem;">${data.data.created_at}</span>
                                    </div>
                                    <p class="text-secondary mb-2 style-comment-text small" style="white-space: pre-line;">${data.data.comment}</p>
                                    <div class="d-flex gap-3 align-items-center">
                                        <button class="btn btn-sm btn-link text-danger p-0 fw-bold text-decoration-none btn-comment-delete ms-auto" data-id="${data.data.id}" style="font-size: 0.8rem;">
                                            <i class="fa-solid fa-trash me-1"></i>Delete
                                        </button>
                                    </div>
                                    <div class="reply-form-container mt-3 d-none" id="reply-form-${data.data.id}">
                                        <form class="inline-reply-form" data-parent-id="${data.data.id}">
                                            @csrf
                                            <div class="d-flex">
                                                <textarea class="form-control border-0 bg-white p-2.5 small" rows="2" placeholder="Write a reply..." style="border-radius: 12px; resize: none; font-size: 0.85rem; box-shadow: 0 0 10px rgba(0,0,0,0.03);" required></textarea>
                                                <button type="submit" class="btn btn-primary rounded-pill px-3 py-1.5 ms-2 align-self-end fw-bold" style="font-size: 0.8rem;">Reply</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="replies-wrapper ms-5 mt-3 d-flex flex-column gap-3" id="replies-container-${data.data.id}" style="border-left: 2px dashed #e2e8f0; padding-left: 1rem;"></div>
                        </div>
                    `;
                    document.getElementById('comments-container').insertAdjacentHTML('afterbegin', commentHtml);
                    updateCommentCounts(1);

                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: data.message,
                        showConfirmButton: false,
                        timer: 3000,
                        background: 'rgba(255, 255, 255, 0.95)'
                    });
                } else {
                    Swal.fire({ icon: 'error', title: 'Oops...', text: data.message || 'Failed to post comment.' });
                }
            })
            .catch(err => {
                console.error(err);
                btn.disabled = false;
                btn.innerHTML = originalBtnContent;
                Swal.fire({ icon: 'error', title: 'Oops...', text: 'Something went wrong.' });
            });
        });
    }

    // Submit Inline Reply
    document.addEventListener('submit', function (e) {
        if (e.target && e.target.classList.contains('inline-reply-form')) {
            e.preventDefault();
            const form = e.target;
            const parentId = form.getAttribute('data-parent-id');
            const textarea = form.querySelector('textarea');
            const replyText = textarea.value.trim();
            const btn = form.querySelector('button[type="submit"]');

            if (!replyText) return;

            btn.disabled = true;
            const originalBtnContent = btn.innerHTML;
            btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i>`;

            fetch(`/dashboard/blogs/{{ $blog->id }}/comments`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    comment: replyText,
                    parent_id: parentId
                })
            })
            .then(response => response.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = originalBtnContent;

                if (data.success) {
                    textarea.value = '';
                    document.getElementById(`reply-form-${parentId}`).classList.add('d-none');

                    // Render new nested reply card dynamically
                    const replyHtml = `
                        <div class="d-flex align-items-start p-3 rounded-4 reply-card" id="comment-${data.data.id}" style="background: rgba(255, 71, 87, 0.03); border: 1px solid rgba(255, 71, 87, 0.05);">
                            <div class="avatar bg-primary text-white d-flex align-items-center justify-content-center fw-bold me-3 shadow-sm" style="width:34px; height:34px; border-radius:50%; font-size: 0.8rem; flex-shrink:0;">
                                ${data.data.user.initial}
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.85rem;">${data.data.user.name}</h6>
                                        <span class="badge bg-primary text-white" style="font-size: 0.65rem; border-radius: 4px; padding: 2px 6px;">Author</span>
                                    </div>
                                    <span class="text-muted small" style="font-size: 0.72rem;">${data.data.created_at}</span>
                                </div>
                                <p class="text-secondary mb-1 style-comment-text small" style="white-space: pre-line;">${data.data.comment}</p>
                                <div class="text-end">
                                    <button class="btn btn-sm btn-link text-danger p-0 fw-bold text-decoration-none btn-comment-delete" data-id="${data.data.id}" style="font-size: 0.75rem;">
                                        <i class="fa-solid fa-trash me-1"></i>Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                    document.getElementById(`replies-container-${parentId}`).insertAdjacentHTML('beforeend', replyHtml);
                    updateCommentCounts(1);

                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: data.message,
                        showConfirmButton: false,
                        timer: 3000,
                        background: 'rgba(255, 255, 255, 0.95)'
                    });
                } else {
                    Swal.fire({ icon: 'error', title: 'Oops...', text: data.message || 'Failed to post reply.' });
                }
            })
            .catch(err => {
                console.error(err);
                btn.disabled = false;
                btn.innerHTML = originalBtnContent;
                Swal.fire({ icon: 'error', title: 'Oops...', text: 'Something went wrong.' });
            });
        }
    });

    // Delete Comment
    document.addEventListener('click', function (e) {
        if (e.target && (e.target.classList.contains('btn-comment-delete') || e.target.closest('.btn-comment-delete'))) {
            e.preventDefault();
            const btn = e.target.classList.contains('btn-comment-delete') ? e.target : e.target.closest('.btn-comment-delete');
            const commentId = btn.getAttribute('data-id');
            const commentEl = document.getElementById(`comment-${commentId}`);

            if (!commentEl) return;

            Swal.fire({
                title: 'Delete Comment?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ff4757',
                cancelButtonColor: '#718096',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/dashboard/blogs/comments/${commentId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Animate and remove
                            commentEl.style.transition = 'all 0.4s ease';
                            commentEl.style.opacity = '0';
                            commentEl.style.transform = 'translateY(20px)';
                            setTimeout(() => {
                                commentEl.remove();
                                // If no comments left, show placeholder
                                const container = document.getElementById('comments-container');
                                if (container && container.children.length === 0) {
                                    container.innerHTML = `
                                        <div id="no-comments-placeholder" class="text-center py-5">
                                            <i class="fa-solid fa-comments fa-3x opacity-20 mb-3 text-secondary"></i>
                                            <p class="text-muted small mb-0">No comments yet. Share your thoughts above!</p>
                                        </div>
                                    `;
                                }
                            }, 400);

                            updateCommentCounts(-1);

                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: data.message,
                                showConfirmButton: false,
                                timer: 3000,
                                background: 'rgba(255, 255, 255, 0.95)'
                            });
                        } else {
                            Swal.fire({ icon: 'error', title: 'Unauthorized', text: data.message || 'Failed to delete.' });
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        Swal.fire({ icon: 'error', title: 'Error', text: 'Something went wrong.' });
                    });
                }
            });
        }
    });

    // Helper to update badges
    function updateCommentCounts(change) {
        const countBadge = document.getElementById('total-comment-count');
        if (countBadge) {
            const current = parseInt(countBadge.textContent) || 0;
            countBadge.textContent = Math.max(0, current + change);
        }
    }
});
</script>
@endsection
