@extends('admin.layouts.app')

@section('title', 'Donation Details')
@section('page-title', 'Donation Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Donation #{{ $donation->id }}</h3>
                </div>
                
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h5 class="text-muted">Donor Information</h5>
                            <p class="mb-2">
                                <strong>Name:</strong> {{ $donation->user->name ?? 'N/A' }}
                            </p>
                            <p class="mb-2">
                                <strong>Email:</strong> {{ $donation->user->email ?? 'N/A' }}
                            </p>
                            <p class="mb-2">
                                <strong>Phone:</strong> {{ $donation->user->phone ?? 'N/A' }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h5 class="text-muted">Donation Information</h5>
                            <p class="mb-2">
                                <strong>Amount:</strong> <span class="badge bg-success">₹{{ number_format($donation->amount, 2) }}</span>
                            </p>
                            <p class="mb-2">
                                <strong>Status:</strong>
                                @if($donation->status === 'completed')
                                    <span class="badge bg-success">Completed</span>
                                @elseif($donation->status === 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @else
                                    <span class="badge bg-danger">Failed</span>
                                @endif
                            </p>
                            <p class="mb-2">
                                <strong>Date:</strong> {{ $donation->created_at->format('d M Y, H:i') }}
                            </p>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h5 class="text-muted">Cause Information</h5>
                            <p class="mb-2">
                                <strong>Title:</strong> {{ $donation->cause->title ?? 'N/A' }}
                            </p>
                            <p class="mb-2">
                                <strong>Organization:</strong> {{ $donation->cause->organization ?? 'N/A' }}
                            </p>
                            <p class="mb-2">
                                <strong>Category:</strong> {{ $donation->cause->category ?? 'N/A' }}
                            </p>
                            <p class="mb-2">
                                <strong>Start Date:</strong> {{ $donation->cause->start_date?->format('d M Y') ?? 'N/A' }}
                            </p>
                            <p class="mb-2">
                                <strong>End Date:</strong> {{ $donation->cause->end_date?->format('d M Y') ?? 'N/A' }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h5 class="text-muted">Payment Details</h5>
                            <p class="mb-2">
                                <strong>Order ID:</strong> {{ $donation->razorpay_order_id ?? 'N/A' }}
                            </p>
                            <p class="mb-2">
                                <strong>Payment ID:</strong> {{ $donation->razorpay_payment_id ?? 'N/A' }}
                            </p>
                            <p class="mb-2">
                                <strong>Method:</strong> {{ $donation->payment_method ?? 'N/A' }}
                            </p>
                        </div>
                    </div>
                    
                    @if($donation->message)
                        <hr>
                        <h5 class="text-muted">Message</h5>
                        <p>{{ $donation->message }}</p>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Actions</h3>
                </div>
                
                <div class="card-body">
                    <a href="{{ route('admin.donations.index') }}" class="btn btn-secondary btn-block mb-2">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                    
                    <form action="{{ route('admin.donations.destroy', $donation->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-block" onclick="return confirm('Are you sure?')">
                            <i class="fas fa-trash"></i> Delete Donation
                        </button>
                    </form>
                </div>
            </div>
            
            @if($donation->cause)
                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Cause Details</h3>
                    </div>
                    
                    <div class="card-body">
                        @if($donation->cause->image_url)
                            <img src="{{ $donation->cause->image_url }}" class="img-fluid mb-2" alt="Cause Image">
                        @endif
                        
                        <p class="mb-2">
                            <strong>Target Amount:</strong> ₹{{ number_format($donation->cause->target_amount, 2) }}
                        </p>
                        <p class="mb-2">
                            <strong>Raised Amount:</strong> ₹{{ number_format($donation->cause->raised_amount, 2) }}
                        </p>
                        <p class="mb-2">
                            <strong>Start Date:</strong> {{ $donation->cause->start_date?->format('d M Y') ?? 'N/A' }}
                        </p>
                        <p class="mb-2">
                            <strong>End Date:</strong> {{ $donation->cause->end_date?->format('d M Y') ?? 'N/A' }}
                        </p>
                        @php
                            $contactInfo = is_string($donation->cause->contact_info) ? json_decode($donation->cause->contact_info, true) : $donation->cause->contact_info;
                            $phone = $contactInfo['phone'] ?? 'N/A';
                        @endphp
                        <p class="mb-2">
                            <strong>Contact Phone:</strong> {{ $phone }}
                        </p>
                        <p class="mb-2">
                            <strong>Urgency:</strong> 
                            @if($donation->cause->urgency === 'critical')
                                <span class="badge bg-danger">Critical</span>
                            @elseif($donation->cause->urgency === 'high')
                                <span class="badge bg-warning">High</span>
                            @elseif($donation->cause->urgency === 'medium')
                                <span class="badge bg-info">Medium</span>
                            @else
                                <span class="badge bg-success">Low</span>
                            @endif
                        </p>
                        <p>
                            <strong>Status:</strong>
                            @if($donation->cause->status === 'active')
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
