@extends('admin.layouts.app')

@section('title', 'Blogger Details')

@section('content')
<div class="content-area">
    <!-- Back link -->
    <div class="mb-4">
        <a href="{{ route('admin.bloggers.index') }}" class="text-primary text-decoration-none fw-bold small">
            <i class="fas fa-arrow-left me-1"></i> Back to Listing
        </a>
    </div>

    <div class="row g-4">
        <!-- Blogger Profile Sidebar -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm text-center p-4" style="border-radius:16px;">
                <div class="card-body">
                    <div class="avatar bg-primary text-white d-flex align-items-center justify-content-center fw-bold mx-auto mb-3" style="width:90px; height:90px; border-radius:50%; font-size:2.5rem;">
                        {{ strtoupper(substr($blogger->name, 0, 1)) }}
                    </div>
                    
                    <h4 class="fw-bold text-dark mb-1">{{ $blogger->name }}</h4>
                    <span class="badge bg-secondary mb-3">Community Blogger</span>

                    <hr class="opacity-10 my-4">

                    <!-- Blogger Details -->
                    <div class="text-start">
                        <div class="mb-3 d-flex align-items-start">
                            <div class="text-muted me-2"><i class="fas fa-envelope"></i></div>
                            <div>
                                <span class="small text-muted d-block">Email Address</span>
                                <span class="fw-semibold text-dark">{{ $blogger->email }}</span>
                            </div>
                        </div>

                        <div class="mb-3 d-flex align-items-start">
                            <div class="text-muted me-2"><i class="fas fa-tags text-primary"></i></div>
                            <div>
                                <span class="small text-muted d-block">Assigned Blog Category</span>
                                <span class="fw-semibold text-dark">{{ $blogger->blogCategory->name ?? 'N/A' }}</span>
                            </div>
                        </div>

                        <div class="mb-3 d-flex align-items-start">
                            <div class="text-muted me-2"><i class="fas fa-phone"></i></div>
                            <div>
                                <span class="small text-muted d-block">Phone Number</span>
                                <span class="fw-semibold text-dark">{{ $blogger->phone ?? 'N/A' }}</span>
                            </div>
                        </div>

                        <div class="mb-3 d-flex align-items-start">
                            <div class="text-muted me-2"><i class="fas fa-user-lock"></i></div>
                            <div>
                                <span class="small text-muted d-block">Blog Access Privilege</span>
                                <span class="badge bg-success">Granted (1)</span>
                            </div>
                        </div>

                        <div class="mb-3 d-flex align-items-start">
                            <div class="text-muted me-2"><i class="fas fa-toggle-on"></i></div>
                            <div>
                                <span class="small text-muted d-block">Account Status</span>
                                <span class="badge bg-{{ $blogger->status === 'active' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($blogger->status) }}
                                </span>
                            </div>
                        </div>

                        <div class="mb-0 d-flex align-items-start">
                            <div class="text-muted me-2"><i class="fas fa-calendar-alt"></i></div>
                            <div>
                                <span class="small text-muted d-block">Registration Date</span>
                                <span class="fw-semibold text-dark">{{ $blogger->created_at->format('M d, Y \a\t h:i A') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-2 d-grid gap-2">
                        <a href="{{ route('admin.bloggers.edit', $blogger->id) }}" class="btn btn-warning rounded-pill py-2 fw-bold">
                            <i class="fas fa-edit me-1"></i> Edit Blogger
                        </a>
                        <form method="POST" 
                              action="{{ route('admin.bloggers.destroy', $blogger->id) }}"
                              onsubmit="return confirm('Are you sure you want to delete this blogger? All their published blogs will also be deleted.');"
                              class="d-grid m-0">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger rounded-pill py-2">
                                <i class="fas fa-trash me-1"></i> Delete Blogger
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Blogger's Publications List -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" style="border-radius:16px;">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-newspaper me-2 text-primary"></i>Published Articles ({{ count($blogs) }})</h5>
                </div>
                <div class="card-body p-4">
                    @if(count($blogs) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Article</th>
                                        <th>Stats</th>
                                        <th>Status</th>
                                        <th>Published</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($blogs as $blog)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($blog->media_path)
                                                    @php
                                                        $mediaList = is_array($blog->media_path) ? $blog->media_path : json_decode($blog->media_path, true);
                                                        $singlePath = is_array($mediaList) ? ($mediaList[0] ?? null) : $blog->media_path;
                                                    @endphp
                                                    @if($singlePath)
                                                        <img src="{{ asset('storage/' . $singlePath) }}" 
                                                             style="width: 50px; height: 50px; border-radius: 8px; object-fit: cover;" 
                                                             class="me-3" 
                                                             alt="Media">
                                                    @else
                                                        <div class="bg-light d-flex align-items-center justify-content-center text-muted me-3" style="width: 50px; height: 50px; border-radius: 8px;">
                                                            <i class="fas fa-newspaper opacity-40"></i>
                                                        </div>
                                                    @endif
                                                @else
                                                    <div class="bg-light d-flex align-items-center justify-content-center text-muted me-3" style="width: 50px; height: 50px; border-radius: 8px;">
                                                        <i class="fas fa-newspaper opacity-40"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <h6 class="mb-0 fw-bold text-dark">{{ $blog->title }}</h6>
                                                    <span class="small text-muted">{{ $blog->category->name ?? $blog->blog_type }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-danger small fw-bold me-2">
                                                <i class="fa-solid fa-heart me-1"></i>{{ $blog->likes_count }}
                                            </span>
                                            <span class="text-secondary small fw-bold">
                                                <i class="fa-regular fa-eye me-1"></i>{{ $blog->views_count }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($blog->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td>{{ $blog->created_at->format('M d, Y') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-light border-0 text-center py-5">
                            <div class="text-secondary opacity-30 mb-3"><i class="fas fa-book-open fs-1"></i></div>
                            <h6 class="text-secondary fw-semibold">No Publications Yet</h6>
                            <p class="text-secondary small mb-0">This blogger has not written or published any articles yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
