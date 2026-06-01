@extends('admin.layouts.app')

@section('title', 'View Blog Category')

@section('content')
<div class="content-area">
    <!-- Back Link -->
    <div class="mb-4">
        <a href="{{ route('admin.blog-categories.index') }}" class="text-primary text-decoration-none fw-bold small">
            <i class="fas fa-arrow-left me-1"></i> Back to Listing
        </a>
    </div>

    <div class="row g-4">
        <!-- Category Profile Card -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm text-center p-4" style="border-radius: 16px;">
                <div class="mx-auto bg-primary-subtle text-primary rounded-circle mb-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 2.5rem;">
                    <i class="fas fa-tags"></i>
                </div>
                
                <h4 class="fw-bold mb-1 text-dark">{{ $category->name }}</h4>
                <div class="mb-3">
                    @if($category->is_active)
                        <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-3 py-1.5">Active Category</span>
                    @else
                        <span class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle px-3 py-1.5">Inactive Category</span>
                    @endif
                </div>

                <hr class="text-light my-3">

                <div class="text-start">
                    <h6 class="fw-bold text-secondary small mb-1">Description:</h6>
                    <p class="text-dark small mb-3">{{ $category->description ?? 'No description provided.' }}</p>
                    
                    <h6 class="fw-bold text-secondary small mb-1">Total Bloggers:</h6>
                    <p class="text-dark fw-bold small mb-3"><i class="fas fa-users-cog text-primary me-1"></i>{{ $bloggers->total() }} bloggers</p>

                    <h6 class="fw-bold text-secondary small mb-1">Created At:</h6>
                    <p class="text-dark small mb-0"><i class="far fa-calendar-alt text-primary me-1"></i>{{ $category->created_at->format('M d, Y H:i A') }}</p>
                </div>
            </div>
        </div>

        <!-- Associated Bloggers List -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold text-dark mb-0"><i class="fas fa-users-cog me-2 text-primary"></i>Bloggers in this Category</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr class="text-secondary small fw-bold">
                                    <th class="ps-4">Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Status</th>
                                    <th class="pe-4 text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bloggers as $blogger)
                                    <tr>
                                        <td class="ps-4">
                                            <span class="fw-bold text-dark">{{ $blogger->name }}</span>
                                        </td>
                                        <td>
                                            <span class="text-secondary small">{{ $blogger->email }}</span>
                                        </td>
                                        <td>
                                            <span class="text-secondary small">{{ $blogger->phone }}</span>
                                        </td>
                                        <td>
                                            @if($blogger->status === 'active')
                                                <span class="badge bg-success-subtle text-success border border-success-subtle">Active</span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Suspended</span>
                                            @endif
                                        </td>
                                        <td class="pe-4 text-end">
                                            <a href="{{ route('admin.bloggers.show', $blogger->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold">
                                                View Account
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-secondary">
                                            <i class="fas fa-user-slash fa-3x mb-3 text-light"></i>
                                            <p class="mb-0 fs-6">No bloggers registered under this category.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($bloggers->hasPages())
                        <div class="card-footer bg-white border-0 py-3">
                            <div class="d-flex justify-content-center">
                                {{ $bloggers->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
