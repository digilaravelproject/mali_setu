@extends('admin.layouts.app')

@section('title', 'Edit Blog')

@section('content')
<div class="content-area">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="mb-4">
                <i class="fas fa-edit me-2"></i>
                Edit Blog
            </h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.blogs.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Blogs
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.blogs.update', $blog->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title', $blog->title) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Blog Type</label>
                    <select name="blog_type" class="form-select" required>
                        <option value="" disabled>Select Blog Category</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ (int)old('blog_type', $blog->blog_type) === $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="5">{{ old('description', $blog->description) }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tags (comma separated)</label>
                    <input type="text" name="tags" class="form-control" value="{{ old('tags', is_array($blog->tags) ? implode(', ', $blog->tags) : $blog->tags) }}">
                </div>

                @if($blog->media_path)
                    @php
                        $mediaList = is_array($blog->media_path) ? $blog->media_path : json_decode($blog->media_path, true);
                    @endphp
                    @if(is_array($mediaList) && count($mediaList) > 0)
                        <div class="mb-3">
                            <label class="form-label">Existing Media</label>
                            <div class="row g-2">
                                @foreach($mediaList as $mPath)
                                    @php
                                        $ext = strtolower(pathinfo($mPath, PATHINFO_EXTENSION));
                                        $isVid = in_array($ext, ['mp4', 'mov', 'avi', 'webm', 'ogg']);
                                    @endphp
                                    <div class="col-md-3">
                                        @if($isVid)
                                            <video src="{{ asset('storage/' . $mPath) }}" class="img-fluid rounded border" style="max-height: 120px; width: 100%; object-fit: cover;" controls></video>
                                        @else
                                            <img src="{{ asset('storage/' . $mPath) }}" alt="Blog Media" class="img-fluid rounded border" style="max-height: 120px; width: 100%; object-fit: cover;">
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endif

                <div class="mb-3">
                    <label class="form-label">Replace Media (optional - select one or more)</label>
                    <input type="file" name="media[]" class="form-control" accept="image/*,video/*" multiple>
                </div>

                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $blog->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">
                        Active
                    </label>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Update Blog
                </button>
            </form>
        </div>
    </div>

    <!-- Blog Comments Management Card -->
    <div class="card mt-4">
        <div class="card-header bg-light d-flex align-items-center justify-content-between py-3">
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-comments me-2 text-danger"></i>Manage Comments (<span id="total-comment-count">{{ $blog->allComments()->count() }}</span>)</h5>
            <span class="text-secondary small">Scrollable discussion log</span>
        </div>
        <div class="card-body p-4">
            <div id="comments-scroll-container" style="max-height: 480px; overflow-y: auto; padding-right: 8px;">
                @forelse($blog->comments as $comment)
                    <div class="comment-wrapper mb-4" id="comment-{{ $comment->id }}">
                        <!-- Parent Comment Card -->
                        <div class="d-flex align-items-start p-3 rounded mb-2 border-start border-3 border-secondary" style="background: #f8f9fa;">
                            <div class="avatar bg-secondary text-white d-flex align-items-center justify-content-center fw-bold me-3 shadow-sm" style="width: 38px; height: 38px; border-radius: 50%; flex-shrink: 0; font-size: 0.9rem;">
                                {{ strtoupper(substr($comment->user->name ?? 'U', 0, 1)) }}
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.95rem;">{{ $comment->user->name ?? 'Anonymous' }}</h6>
                                    <span class="text-muted small" style="font-size: 0.75rem;">{{ $comment->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-secondary small mb-2" style="white-space: pre-line;">{{ $comment->comment }}</p>
                                <div class="text-end">
                                    <form method="POST" action="{{ route('admin.blogs.comments.destroy', $comment->id) }}" onsubmit="return confirm('Are you sure you want to delete this comment? (Deleting a parent comment will also delete all of its replies.)')" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger px-2.5 py-1 rounded fw-bold" style="font-size: 0.75rem;">
                                            <i class="fas fa-trash me-1"></i>Delete Comment
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Replies List (Nested) -->
                        <div class="replies-wrapper ms-5 border-start border-2 border-light ps-3 d-flex flex-column gap-3">
                            @foreach($comment->replies as $reply)
                                <div class="d-flex align-items-start p-3 rounded reply-card" id="comment-{{ $reply->id }}" style="background: rgba(255, 71, 87, 0.02); border: 1px solid rgba(255, 71, 87, 0.04);">
                                    <div class="avatar bg-primary text-white d-flex align-items-center justify-content-center fw-bold me-3 shadow-sm" style="width: 32px; height: 32px; border-radius: 50%; flex-shrink: 0; font-size: 0.8rem;">
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
                                        <p class="text-secondary small mb-2" style="white-space: pre-line;">{{ $reply->comment }}</p>
                                        <div class="text-end">
                                            <form method="POST" action="{{ route('admin.blogs.comments.destroy', $reply->id) }}" onsubmit="return confirm('Are you sure you want to delete this reply?')" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger px-2.5 py-1 rounded fw-bold" style="font-size: 0.75rem;">
                                                    <i class="fas fa-trash me-1"></i>Delete Reply
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <i class="fas fa-comments fa-3x opacity-20 mb-3 text-secondary"></i>
                        <p class="text-muted small mb-0">No comments on this blog yet.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<style>
    #comments-scroll-container::-webkit-scrollbar {
        width: 6px;
    }
    #comments-scroll-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    #comments-scroll-container::-webkit-scrollbar-thumb {
        background: var(--primary-color, #ff4757);
        border-radius: 10px;
    }
    #comments-scroll-container::-webkit-scrollbar-thumb:hover {
        background: #8b0000;
    }
</style>
@endsection
