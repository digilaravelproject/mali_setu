@extends('admin.layouts.app')

@section('title', 'Matrimony Profile Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Matrimony Profile Details</h4>
                    <div>
                        <a href="{{ route('admin.matrimony.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                        @if($profile->approval_status === 'pending')
                            <!-- Approve Form -->
                            <form method="POST" action="{{ route('admin.matrimony.approve', $profile->id) }}" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-success" 
                                        onclick="return confirm('Are you sure you want to approve this matrimony profile?')">
                                    <i class="fas fa-check"></i> Approve
                                </button>
                            </form>
                            
                            <!-- Reject Form -->
                            <form method="POST" action="{{ route('admin.matrimony.reject', $profile->id) }}" style="display: inline;">
                                @csrf
                                <input type="hidden" name="rejection_reason" id="rejection_reason_detail">
                                <button type="submit" class="btn btn-danger" 
                                        onclick="var reason = prompt('Please provide a reason for rejection:'); if (reason && reason.trim() !== '') { document.getElementById('rejection_reason_detail').value = reason.trim(); return true; } else { return false; }">
                                    <i class="fas fa-times"></i> Reject
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Profile Photo -->
                        <div class="col-md-4">
                            <div class="text-center mb-4">
                                @if($profile->profile_photo)
                                    <img src="{{ asset('storage/' . $profile->profile_photo) }}" 
                                         alt="Profile Photo" 
                                         class="img-fluid rounded-circle" 
                                         style="width: 200px; height: 200px; object-fit: cover;">
                                @else
                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width: 200px; height: 200px; margin: 0 auto;">
                                        <i class="fas fa-user fa-5x text-muted"></i>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Status Badge -->
                            <div class="text-center mb-3">
                                @if($profile->status === 'approved')
                                    <span class="badge badge-success badge-lg">Approved</span>
                                @elseif($profile->status === 'pending')
                                    <span class="badge badge-warning badge-lg">Pending Review</span>
                                @elseif($profile->status === 'rejected')
                                    <span class="badge badge-danger badge-lg">Rejected</span>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Profile Information -->
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>Personal Information</h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Full Name:</strong></td>
                                            <td>{{ $profile->user->name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Age:</strong></td>
                                            <td>{{ $profile->age ?? 'Not specified' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Gender:</strong></td>
                                            <td>{{ ucfirst($profile->gender ?? 'Not specified') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Height:</strong></td>
                                            <td>{{ $profile->height ?? 'Not specified' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Weight:</strong></td>
                                            <td>{{ $profile->weight ?? 'Not specified' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Marital Status:</strong></td>
                                            <td>{{ ucfirst($profile->marital_status ?? 'Not specified') }}</td>
                                        </tr>
                                    </table>
                                </div>
                                
                                <div class="col-md-6">
                                    <h5>Contact Information</h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Email:</strong></td>
                                            <td>{{ $profile->user->email }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Phone:</strong></td>
                                            <td>{{ $profile->user->phone }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Location:</strong></td>
                                            <td>
                                                @php
                                                    $locationData = is_string($profile->location_details) ? json_decode($profile->location_details, true) : $profile->location_details;
                                                @endphp
                                                @if($locationData && is_array($locationData))
                                                    {{ $locationData['city'] ?? 'N/A' }}, {{ $locationData['state'] ?? 'N/A' }}
                                                @else
                                                    {{ $profile->location ?? 'Not specified' }}
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Education:</strong></td>
                                            <td>
                                                @php
                                                    $educationData = is_string($profile->education_details) ? json_decode($profile->education_details, true) : $profile->education_details;
                                                @endphp
                                                @if($educationData && is_array($educationData))
                                                    {{ $educationData['highest_qualification'] ?? 'N/A' }}
                                                @else
                                                    {{ $profile->education ?? 'Not specified' }}
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Occupation:</strong></td>
                                            <td>{{ $profile->occupation ?? 'Not specified' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Income:</strong></td>
                                            <td>{{ $profile->income ?? 'Not specified' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- Bio/Description -->
                            @if($profile->bio)
                            <div class="mt-4">
                                <h5>About</h5>
                                <p class="text-muted">{{ $profile->bio }}</p>
                            </div>
                            @endif
                            
                            <!-- Family Information -->
            @if($profile->family_details)
            <div class="mt-4">
                <h5>Family Details</h5>
                @php
                    $familyData = is_string($profile->family_details) ? json_decode($profile->family_details, true) : $profile->family_details;
                @endphp
                @if($familyData && is_array($familyData))
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Father's Name:</strong></td>
                            <td>{{ $familyData['father_name'] ?? 'Not specified' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Mother's Name:</strong></td>
                            <td>{{ $familyData['mother_name'] ?? 'Not specified' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Number of Siblings:</strong></td>
                            <td>{{ $familyData['siblings'] ?? 'Not specified' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Family Type:</strong></td>
                            <td>{{ $familyData['family_type'] ?? 'Not specified' }}</td>
                        </tr>
                    </table>
                @else
                    <p class="text-muted">{{ $profile->family_details }}</p>
                @endif
            </div>
            @endif
                        </div>
                    </div>
                    
                    <!-- Additional Photos -->
                    @if($profile->additional_photos)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Additional Photos</h5>
                            <div class="row">
                                @foreach(json_decode($profile->additional_photos, true) as $photo)
                                <div class="col-md-3 mb-3">
                                    <img src="{{ asset('storage/' . $photo) }}" 
                                         alt="Additional Photo" 
                                         class="img-fluid rounded" 
                                         style="height: 200px; object-fit: cover; width: 100%;">
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Admin Actions -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6>Admin Information</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Created:</strong> {{ $profile->created_at->format('M d, Y H:i') }}</p>
                                            <p><strong>Last Updated:</strong> {{ $profile->updated_at->format('M d, Y H:i') }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            @if($profile->verified_at)
                                                <p><strong>Verified:</strong> {{ $profile->verified_at->format('M d, Y H:i') }}</p>
                                            @endif
                                            @if($profile->rejection_reason)
                                                <p><strong>Rejection Reason:</strong> {{ $profile->rejection_reason }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Rejection Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Matrimony Profile</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="rejection_reason">Reason for Rejection:</label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" 
                                  rows="4" required placeholder="Please provide a reason for rejection..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Profile</button>
                </div>
            </form>
        </div>
    </div>
</div>


@endsection