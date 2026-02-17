@extends('admin.layouts.app')

@section('title', 'Donation Causes')
@section('page-title', 'Donation Causes Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Donation Causes</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.donations.causes.create') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-plus"></i> Add New Cause
                        </a>
                        <a href="{{ route('admin.donations.index') }}" class="btn btn-info btn-sm">
                            <i class="fas fa-list"></i> View Donations
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4>{{ $stats['total'] ?? 0 }}</h4>
                                            <p class="mb-0">Total Causes</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-list fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4>{{ $stats['active'] ?? 0 }}</h4>
                                            <p class="mb-0">Active</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-check-circle fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4>₹{{ number_format($stats['total_raised'] ?? 0, 2) }}</h4>
                                            <p class="mb-0">Total Raised</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-rupee-sign fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4>₹{{ number_format($stats['total_target'] ?? 0, 2) }}</h4>
                                            <p class="mb-0">Target Amount</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-bullseye fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Filters -->
                    <form method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" placeholder="Search by title or organization..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <select name="status" class="form-select">
                                    <option value="">All Status</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="category" class="form-select">
                                    <option value="">All Categories</option>
                                    <option value="medical" {{ request('category') == 'medical' ? 'selected' : '' }}>Medical</option>
                                    <option value="education" {{ request('category') == 'education' ? 'selected' : '' }}>Education</option>
                                    <option value="disaster" {{ request('category') == 'disaster' ? 'selected' : '' }}>Disaster Relief</option>
                                    <option value="social" {{ request('category') == 'social' ? 'selected' : '' }}>Social</option>
                                    <option value="environment" {{ request('category') == 'environment' ? 'selected' : '' }}>Environment</option>
                                    <option value="other" {{ request('category') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                            </div>
                        </div>
                    </form>
                    
                    <!-- Causes Table -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Organization</th>
                                    <th>Category</th>
                                    <th>Target Amount</th>
                                    <th>Raised Amount</th>
                                    <th>Progress</th>
                                    <th>Start Date</th>
                                    <th>Phone</th>
                                    <th>Urgency</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($causes->count() > 0)
                                    @foreach($causes as $cause)
                                        <tr>
                                            <td>#{{ $cause->id }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($cause->image_url)
                                                        <img src="{{ $cause->image_url }}" alt="{{ $cause->title }}" style="width: 40px; height: 40px; border-radius: 4px; margin-right: 10px;">
                                                    @endif
                                                    <span>{{ $cause->title }}</span>
                                                </div>
                                            </td>
                                            <td>{{ $cause->organization }}</td>
                                            <td>
                                                <span class="badge bg-secondary">{{ ucfirst($cause->category) }}</span>
                                            </td>
                                            <td>₹{{ number_format($cause->target_amount, 2) }}</td>
                                            <td>₹{{ number_format($cause->raised_amount, 2) }}</td>
                                            <td>
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar {{ $cause->raised_amount >= $cause->target_amount ? 'bg-success' : 'bg-info' }}" 
                                                         role="progressbar" 
                                                         style="width: {{ min(($cause->raised_amount / $cause->target_amount) * 100, 100) }}%"
                                                         aria-valuenow="{{ $cause->raised_amount }}" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="{{ $cause->target_amount }}">
                                                        {{ number_format(($cause->raised_amount / $cause->target_amount) * 100, 1) }}%
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $cause->start_date?->format('d M Y') ?? 'N/A' }}</td>
                                            <td>
                                                @php
                                                    $contactInfo = is_string($cause->contact_info) ? json_decode($cause->contact_info, true) : $cause->contact_info;
                                                    $phone = $contactInfo['phone'] ?? 'N/A';
                                                @endphp
                                                {{ $phone }}
                                            </td>
                                            <td>
                                                @if($cause->urgency === 'critical')
                                                    <span class="badge bg-danger">Critical</span>
                                                @elseif($cause->urgency === 'high')
                                                    <span class="badge bg-warning">High</span>
                                                @elseif($cause->urgency === 'medium')
                                                    <span class="badge bg-info">Medium</span>
                                                @else
                                                    <span class="badge bg-success">Low</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($cause->status === 'active')
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.donations.causes.edit', $cause->id) }}" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <form action="{{ route('admin.donations.causes.destroy', $cause->id) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="12" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-2x mb-2"></i>
                                            <p>No causes found</p>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $causes->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
