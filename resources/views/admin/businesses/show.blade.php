@extends('admin.layouts.app')

@section('title', 'Business Details')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Business Details</h1>
        <div class="d-sm-flex">
            <a href="{{ route('admin.businesses.index') }}" class="btn btn-secondary btn-sm shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Businesses
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Main details -->
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Business Profile Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Business Name:</strong></td>
                                    <td>{{ $business->business_name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Business Type:</strong></td>
                                    <td><span class="badge bg-info">{{ ucfirst($business->business_type) }}</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Category:</strong></td>
                                    <td>{{ $business->category->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Phone:</strong></td>
                                    <td>{{ $business->contact_phone ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>{{ $business->contact_email ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Website:</strong></td>
                                    <td>
                                        @if($business->website)
                                            <a href="{{ $business->website }}" target="_blank">{{ $business->website }}</a>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Owner:</strong></td>
                                    <td>
                                        <a href="{{ route('admin.users.show', $business->user->id) }}" class="text-decoration-none">
                                            {{ $business->user->name }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Verification Status:</strong></td>
                                    <td>
                                        @if($business->verification_status == 'approved')
                                            <span class="badge bg-success">Approved</span>
                                        @elseif($business->verification_status == 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @else
                                            <span class="badge bg-danger">Rejected</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Business Status:</strong></td>
                                    <td>
                                        @if($business->status == 'active')
                                            <span class="badge bg-success">Active</span>
                                        @elseif($business->status == 'suspended')
                                            <span class="badge bg-warning">Suspended</span>
                                        @else
                                            <span class="badge bg-danger">Banned</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Verified At:</strong></td>
                                    <td>{{ $business->verified_at ? $business->verified_at->format('M d, Y H:i') : 'Not verified' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Created:</strong></td>
                                    <td>{{ $business->created_at->format('M d, Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Updated:</strong></td>
                                    <td>{{ $business->updated_at->format('M d, Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    @if($business->description)
                    <div class="row mt-3 border-top pt-3">
                        <div class="col-12">
                            <h6 class="font-weight-bold text-secondary">Description</h6>
                            <p class="text-muted small">{{ $business->description }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- Location details -->
                    <div class="row mt-3 border-top pt-3">
                        <div class="col-12">
                            <h6 class="font-weight-bold text-secondary"><i class="fas fa-location-dot me-1"></i>Location Details</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td style="width: 25%;"><strong>Country:</strong></td>
                                    <td>{{ $business->country ?? 'India' }}</td>
                                    <td style="width: 25%;"><strong>State:</strong></td>
                                    <td>{{ $business->state ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>District:</strong></td>
                                    <td>{{ $business->district ?? 'N/A' }}</td>
                                    <td><strong>City/Town:</strong></td>
                                    <td>{{ $business->city ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Pincode:</strong></td>
                                    <td>{{ $business->pincode ?? 'N/A' }}</td>
                                    <td><strong>Village:</strong></td>
                                    <td>{{ $business->village ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Taluka:</strong></td>
                                    <td>{{ $business->taluka ?? 'N/A' }}</td>
                                    <td><strong>Coordinates:</strong></td>
                                    <td>
                                        @if($business->latitude || $business->longitude)
                                            Lat: {{ $business->latitude ?? '0' }}, Lng: {{ $business->longitude ?? '0' }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                                @if($business->address)
                                <tr>
                                    <td><strong>Address:</strong></td>
                                    <td colspan="3">{{ $business->address }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    <!-- Timings -->
                    <div class="row mt-3 border-top pt-3">
                        <div class="col-12">
                            <h6 class="font-weight-bold text-secondary"><i class="fas fa-clock me-1"></i>Business Hours</h6>
                            <p class="text-muted small">
                                <strong>Opening Time:</strong> {{ $business->opening_time ? \Carbon\Carbon::parse($business->opening_time)->format('h:i A') : 'N/A' }} <br>
                                <strong>Closing Time:</strong> {{ $business->closing_time ? \Carbon\Carbon::parse($business->closing_time)->format('h:i A') : 'N/A' }}
                            </p>
                        </div>
                    </div>
                    
                    @if($business->rejection_reason)
                    <div class="row mt-3 border-top pt-3">
                        <div class="col-12">
                            <h6 class="font-weight-bold text-danger">Rejection Reason</h6>
                            <p class="text-danger small">{{ $business->rejection_reason }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Products Section -->
            @if($business->products && $business->products->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Products ({{ $business->products->count() }})</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($business->products as $product)
                        <div class="col-md-6 mb-3">
                            <div class="border rounded p-3">
                                <h6 class="font-weight-bold">{{ $product->name }}</h6>
                                <p class="text-muted mb-1">{{ Str::limit($product->description, 100) }}</p>
                                <p class="mb-0"><strong>Price:</strong> ₹{{ number_format($product->price, 2) }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Services Section -->
            @if($business->services && $business->services->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Services ({{ $business->services->count() }})</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($business->services as $service)
                        <div class="col-md-6 mb-3">
                            <div class="border rounded p-3">
                                <h6 class="font-weight-bold">{{ $service->name }}</h6>
                                <p class="text-muted mb-1">{{ Str::limit($service->description, 100) }}</p>
                                <p class="mb-0"><strong>Price:</strong> ₹{{ number_format($service->price, 2) }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
        
        <!-- Action Panel -->
        <div class="col-md-4">
            <!-- Business Photo -->
            @if($business->photo)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Business Photo</h6>
                </div>
                <div class="card-body text-center">
                    <img src="{{ asset('storage/' . $business->photo) }}" alt="Business Photo" class="img-fluid rounded" style="max-height: 250px;">
                </div>
            </div>
            @endif

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Actions</h6>
                </div>
                <div class="card-body">
                    <a href="{{ route('admin.businesses.edit', $business->id) }}" class="btn btn-primary btn-block mb-2 w-100">
                        <i class="fas fa-edit"></i> Edit Business
                    </a>
                    @if($business->verification_status == 'pending')
                        <!-- Approve Business Form -->
                        <form action="{{ route('admin.businesses.approve', $business->id) }}" method="POST" class="d-inline w-100 mb-2">
                            @csrf
                            <button type="submit" class="btn btn-success btn-block w-100 mb-2" onclick="return confirm('Are you sure you want to approve this business?')">
                                <i class="fas fa-check"></i> Approve Business
                            </button>
                        </form>
                        
                        <!-- Reject Business Form -->
                        <form action="{{ route('admin.businesses.reject', $business->id) }}" method="POST" class="d-inline w-100 mb-2">
                            @csrf
                            <input type="hidden" name="rejection_reason" id="rejection_reason_{{ $business->id }}">
                            <button type="submit" class="btn btn-danger btn-block w-100 mb-2" onclick="return handleRejectClick({{ $business->id }})">
                                <i class="fas fa-times"></i> Reject Business
                            </button>
                        </form>
                    @endif
                    
                    @if($business->status == 'active')
                        <!-- Suspend Business Form -->
                        <form action="{{ route('admin.businesses.suspend', $business->id) }}" method="POST" class="d-inline w-100 mb-2">
                            @csrf
                            <button type="submit" class="btn btn-warning btn-block w-100 mb-2" onclick="return confirm('Are you sure you want to suspend this business?')">
                                <i class="fas fa-ban"></i> Suspend Business
                            </button>
                        </form>
                    @else
                        <!-- Activate Business Form -->
                        <form action="{{ route('admin.businesses.activate', $business->id) }}" method="POST" class="d-inline w-100 mb-2">
                            @csrf
                            <button type="submit" class="btn btn-success btn-block w-100 mb-2" onclick="return confirm('Are you sure you want to activate this business?')">
                                <i class="fas fa-check-circle"></i> Activate Business
                            </button>
                        </form>
                    @endif
                    
                    <a href="{{ route('admin.users.show', $business->user->id) }}" class="btn btn-info btn-block mb-2 w-100">
                        <i class="fas fa-user"></i> View Owner
                    </a>
                    
                    <!-- Delete Business Form -->
                    <form action="{{ route('admin.businesses.destroy', $business->id) }}" method="POST" class="d-inline w-100">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-block w-100" onclick="return confirm('Are you sure you want to delete this business? This action cannot be undone.')">
                            <i class="fas fa-trash"></i> Delete Business
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Statistics -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-right">
                                <h4 class="text-primary">{{ $business->products->count() }}</h4>
                                <small class="text-muted">Products</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4 class="text-success">{{ $business->services->count() }}</h4>
                            <small class="text-muted">Services</small>
                        </div>
                    </div>
                    <hr>
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-right">
                                <h4 class="text-info">{{ $business->reviews->count() ?? 0 }}</h4>
                                <small class="text-muted">Reviews</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4 class="text-warning">{{ $business->locations->count() ?? 0 }}</h4>
                            <small class="text-muted">Locations</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function handleRejectClick(businessId) {
    const reason = prompt('Please provide a reason for rejection:');
    if (reason && reason.trim() !== '') {
        document.getElementById('rejection_reason_' + businessId).value = reason.trim();
        return confirm('Are you sure you want to reject this business with reason: "' + reason.trim() + '"?');
    } else {
        alert('Rejection reason is required.');
        return false;
    }
}
</script>
@endsection