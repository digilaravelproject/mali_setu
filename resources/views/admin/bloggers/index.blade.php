@extends('admin.layouts.app')

@section('title', 'Blogger Management')

@section('content')
<div class="content-area">
    <!-- Header -->
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <h2 class="mb-1">
                <i class="fas fa-users-cog me-2"></i>
                Blogger Management
            </h2>
            <p class="text-muted mb-0">Create, monitor, and manage community bloggers and their credentials.</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.bloggers.create') }}" class="btn btn-primary px-4 rounded-pill">
                <i class="fas fa-plus me-1"></i> Add New Blogger
            </a>
        </div>
    </div>

    <!-- Blogger Table Card -->
    <div class="card border-0 shadow-sm" style="border-radius:16px;">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold text-dark">All Registered Bloggers</h5>
        </div>
        <div class="card-body p-4">
            <!-- Search -->
            <form method="GET" action="{{ route('admin.bloggers.index') }}" class="mb-4">
                <div class="row g-2">
                    <div class="col-md-10">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                            <input type="text" name="search" class="form-control border-start-0" 
                                   placeholder="Search by name, email or phone number..." 
                                   value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100 rounded-pill">
                            Filter
                        </button>
                    </div>
                </div>
            </form>

            @if($bloggers->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Blogger</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th>Registered</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bloggers as $blogger)
                            <tr id="blogger-row-{{ $blogger->id }}">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar bg-primary text-white d-flex align-items-center justify-content-center fw-bold me-3" style="width:40px; height:40px; border-radius:50%;">
                                            {{ strtoupper(substr($blogger->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold text-dark">{{ $blogger->name }}</h6>
                                            <span class="small text-muted">{{ $blogger->email }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $blogger->phone ?? 'N/A' }}</td>
                                <td>
                                    <button class="badge border-0 status-badge bg-{{ $blogger->status === 'active' ? 'success' : 'secondary' }}" 
                                            onclick="toggleBloggerStatus(this, {{ $blogger->id }})"
                                            title="Click to toggle active/suspended status">
                                        {{ ucfirst($blogger->status) }}
                                    </button>
                                </td>
                                <td>{{ $blogger->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('admin.bloggers.show', $blogger->id) }}" 
                                           class="btn btn-outline-primary" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.bloggers.edit', $blogger->id) }}" 
                                           class="btn btn-outline-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" 
                                              action="{{ route('admin.bloggers.destroy', $blogger->id) }}"
                                              style="display: inline;"
                                              onsubmit="return confirm('Are you sure you want to delete this blogger? All their published blogs will also be deleted.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $bloggers->links() }}
                </div>
            @else
                <div class="alert alert-info border-0 rounded-4 p-4 text-center">
                    <div class="mb-3 text-secondary opacity-50"><i class="fas fa-users-cog fs-1"></i></div>
                    <h5>No Bloggers Found</h5>
                    <p class="text-secondary small mb-3">There are no bloggers matching your query or registered yet.</p>
                    <a href="{{ route('admin.bloggers.create') }}" class="btn btn-primary btn-sm px-4 rounded-pill">Create First Blogger</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleBloggerStatus(badge, bloggerId) {
        if (badge.disabled) return;
        badge.disabled = true;

        fetch(`/admin/bloggers/${bloggerId}/toggle-status`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.status === 'active') {
                    badge.className = 'badge border-0 status-badge bg-success';
                    badge.textContent = 'Active';
                } else {
                    badge.className = 'badge border-0 status-badge bg-secondary';
                    badge.textContent = 'Suspended';
                }
            } else {
                alert('Failed to update status: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error toggling blogger status:', error);
            alert('Failed to update status due to a connection error.');
        })
        .finally(() => {
            badge.disabled = false;
        });
    }
</script>
@endpush
