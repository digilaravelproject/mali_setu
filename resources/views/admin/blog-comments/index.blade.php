@extends('admin.layouts.app')

@section('title', 'Manage Comments')

@section('content')
<div class="content-area">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="mb-4">
                <i class="fas fa-comments me-2 text-danger"></i>
                Blog Comments Management
            </h2>
            <p class="text-secondary mb-0">View all comments and replies across all published blogs, and moderate discussion.</p>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex align-items-start">
                        <div class="stats-icon" style="background: linear-gradient(135deg, #ff4757 0%, #ff2a3b 100%);">
                            <i class="fas fa-comments"></i>
                        </div>
                        <div class="ms-3">
                            <p class="stats-label">Total Comments</p>
                            <p class="stats-number">{{ $stats['total'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex align-items-start">
                        <div class="stats-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                            <i class="fas fa-comment-dots"></i>
                        </div>
                        <div class="ms-3">
                            <p class="stats-label">Top Level</p>
                            <p class="stats-number">{{ $stats['top_level'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex align-items-start">
                        <div class="stats-icon" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                            <i class="fas fa-reply-all"></i>
                        </div>
                        <div class="ms-3">
                            <p class="stats-label">Replies</p>
                            <p class="stats-number">{{ $stats['replies'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Comments Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">All Comments</h5>
        </div>
        <div class="card-body">
            <!-- Search and Filter -->
            <form method="GET" class="mb-4">
                <div class="row">
                    <div class="col-md-10">
                        <input type="text" name="search" class="form-control" 
                               placeholder="Search comments by content, commenter's name, or blog title..." 
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i> Search
                        </button>
                    </div>
                </div>
            </form>

            @if($comments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th style="width: 35%;">Comment</th>
                                <th>Author</th>
                                <th>Blog Title</th>
                                <th>Type</th>
                                <th>Created At</th>
                                <th style="width: 10%;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($comments as $comment)
                            <tr>
                                <td>
                                    <div class="text-wrap small text-secondary" style="max-height: 80px; overflow-y: auto;">
                                        <strong>{{ $comment->comment }}</strong>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar bg-light text-primary d-flex align-items-center justify-content-center fw-bold me-2" style="width: 30px; height: 30px; border-radius: 50%; font-size: 0.8rem;">
                                            {{ strtoupper(substr($comment->user->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <div>
                                            <span class="fw-semibold text-dark">{{ $comment->user->name ?? 'Anonymous' }}</span>
                                            <br>
                                            <small class="text-muted" style="font-size: 0.75rem;">{{ $comment->user->email ?? 'N/A' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($comment->blog)
                                        <a href="{{ route('blogs.public.show', $comment->blog->id) }}" target="_blank" class="text-decoration-none text-primary small fw-semibold">
                                            {{ Str::limit($comment->blog->title, 40) }}
                                        </a>
                                    @else
                                        <span class="text-muted small">Deleted Blog</span>
                                    @endif
                                </td>
                                <td>
                                    @if(is_null($comment->parent_id))
                                        <span class="badge bg-primary">Comment</span>
                                    @else
                                        <span class="badge bg-info">Reply</span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">{{ $comment->created_at->format('M d, Y \a\t h:i A') }}</small>
                                </td>
                                <td>
                                    <form method="POST" 
                                          action="{{ route('admin.blog-comments.destroy', $comment->id) }}"
                                          style="display: inline;"
                                          onsubmit="return confirm('Are you sure you want to delete this comment? (Deleting a parent comment will also delete all of its replies.)');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 36px; height: 36px;" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{ $comments->appends(request()->query())->links('pagination::bootstrap-5') }}
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    No comments found matching your filters.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
