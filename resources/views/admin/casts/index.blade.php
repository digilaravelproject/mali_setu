@extends('admin.layouts.app')

@section('title', 'Manage Casts')

@section('content')
<div class="content-area">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="mb-4">
                <i class="fas fa-sitemap me-2"></i>
                Casts Management
            </h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.casts.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Add New Cast
            </a>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex align-items-start">
                        <div class="stats-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <i class="fas fa-layer-group"></i>
                        </div>
                        <div class="ms-3">
                            <p class="stats-label">Total Casts</p>
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
                        <div class="stats-icon" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="ms-3">
                            <p class="stats-label">Active</p>
                            <p class="stats-number">{{ $stats['active'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex align-items-start">
                        <div class="stats-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                            <i class="fas fa-times-circle"></i>
                        </div>
                        <div class="ms-3">
                            <p class="stats-label">Inactive</p>
                            <p class="stats-number">{{ $stats['inactive'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Casts Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">All Casts</h5>
        </div>
        <div class="card-body">
            <!-- Search and Filter -->
            <form method="GET" class="mb-4">
                <div class="row">
                    <div class="col-md-6">
                        <input type="text" name="search" class="form-control" 
                               placeholder="Search by name or description..." 
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-4">
                        <select name="status" class="form-control">
                            <option value="">All Status</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i> Search
                        </button>
                    </div>
                </div>
            </form>

            @if($casts->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Cast Name</th>
                                <th>Description</th>
                                <th>Sub-Casts</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($casts as $cast)
                            <tr>
                                <td><strong>{{ $cast->name }}</strong></td>
                                <td>
                                    <small>{{ Str::limit($cast->description, 45) ?? 'N/A' }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $cast->sub_casts_count }}</span>
                                </td>
                                <td>
                                    @if($cast->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>{{ $cast->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('admin.casts.subcasts.index', $cast->id) }}" 
                                           class="btn btn-info" title="Manage Sub-Casts">
                                            <i class="fas fa-list"></i> Sub-Casts
                                        </a>
                                        <a href="{{ route('admin.casts.edit', $cast->id) }}" 
                                           class="btn btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" 
                                              action="{{ route('admin.casts.toggle-status', $cast->id) }}"
                                              style="display: inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-{{ $cast->is_active ? 'warning' : 'success' }}" 
                                                    title="{{ $cast->is_active ? 'Deactivate' : 'Activate' }}">
                                                <i class="fas fa-{{ $cast->is_active ? 'ban' : 'check' }}"></i>
                                            </button>
                                        </form>
                                        <form method="POST" 
                                              action="{{ route('admin.casts.destroy', $cast->id) }}"
                                              style="display: inline;"
                                              onsubmit="return confirm('Are you sure you want to delete this cast? All sub-casts will also be deleted.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" title="Delete">
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

                {{ $casts->links('pagination::bootstrap-4') }}
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    No casts found. <a href="{{ route('admin.casts.create') }}" class="alert-link">Create one now</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
