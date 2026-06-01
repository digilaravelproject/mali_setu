@extends('admin.layouts.app')

@section('title', 'Blog Category Management')

@section('content')
<div class="content-area">
    <!-- Header Card -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold text-dark"><i class="fas fa-tags text-primary me-2"></i>Blog Categories</h4>
            <p class="text-secondary small mb-0">Manage classifications and groups for all bloggers in the portal</p>
        </div>
        <a href="{{ route('admin.blog-categories.create') }}" class="btn btn-primary rounded-pill px-4 py-2fw-bold shadow-sm">
            <i class="fas fa-plus me-1"></i> Add Category
        </a>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-body p-3">
            <form action="{{ route('admin.blog-categories.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-9 col-lg-10">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-secondary"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Search categories by name or description..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3 col-lg-2">
                    <button type="submit" class="btn btn-dark w-100 rounded-3">Search</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Category Listing Table -->
    <div class="card border-0 shadow-sm" style="border-radius:16px; overflow:hidden;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light py-3">
                        <tr class="text-secondary small fw-bold">
                            <th class="ps-4" style="width: 80px;">ID</th>
                            <th>Category Name</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Created Date</th>
                            <th class="pe-4 text-end" style="width: 220px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            <tr>
                                <td class="ps-4 fw-bold text-secondary">#{{ $category->id }}</td>
                                <td>
                                    <span class="fw-bold text-dark fs-6">{{ $category->name }}</span>
                                </td>
                                <td>
                                    <span class="text-secondary small">{{ $category->description ? Str::limit($category->description, 80) : '—' }}</span>
                                </td>
                                <td>
                                    @if($category->is_active)
                                        <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-3 py-2">Active</span>
                                    @else
                                        <span class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle px-3 py-2">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-secondary small"><i class="far fa-calendar-alt me-1"></i>{{ $category->created_at->format('M d, Y') }}</span>
                                </td>
                                <td class="pe-4 text-end">
                                    <div class="d-inline-flex gap-2">
                                        <!-- View Detail Button -->
                                        <a href="{{ route('admin.blog-categories.show', $category->id) }}" class="btn btn-sm btn-outline-primary rounded-circle" title="View Details" style="width:36px; height:36px; display:inline-flex; align-items:center; justify-content:center;">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        <!-- Edit Category Button -->
                                        <a href="{{ route('admin.blog-categories.edit', $category->id) }}" class="btn btn-sm btn-outline-warning rounded-circle" title="Edit Category" style="width:36px; height:36px; display:inline-flex; align-items:center; justify-content:center;">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <!-- Toggle Active/Inactive Status -->
                                        <form action="{{ route('admin.blog-categories.toggle-status', $category->id) }}" method="POST" class="d-inline status-toggle-form">
                                            @csrf
                                            @if($category->is_active)
                                                <button type="submit" class="btn btn-sm btn-outline-warning rounded-circle" title="Deactivate Category" style="width:36px; height:36px; display:inline-flex; align-items:center; justify-content:center;" onclick="return confirm('Are you sure you want to deactivate \'{{ $category->name }}\'?')">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            @else
                                                <button type="submit" class="btn btn-sm btn-outline-success rounded-circle" title="Activate Category" style="width:36px; height:36px; display:inline-flex; align-items:center; justify-content:center;" onclick="return confirm('Are you sure you want to activate \'{{ $category->name }}\'?')">
                                                    <i class="fas fa-check-circle"></i>
                                                </button>
                                            @endif
                                        </form>

                                        <!-- Delete Button -->
                                        <form action="{{ route('admin.blog-categories.destroy', $category->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle" title="Delete Category" style="width:36px; height:36px; display:inline-flex; align-items:center; justify-content:center;" onclick="return confirm('WARNING: Deleting this category will unlink all bloggers assigned to it. Are you sure you want to delete \'{{ $category->name }}\'?')">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-secondary">
                                    <i class="fas fa-tags fa-3x mb-3 text-light"></i>
                                    <p class="mb-0 fs-6">No blog categories found.</p>
                                    <p class="small text-muted">Try updating your filters or create a new blog category.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($categories->hasPages())
                <div class="card-footer bg-white border-0 py-3">
                    <div class="d-flex justify-content-center">
                        {{ $categories->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
