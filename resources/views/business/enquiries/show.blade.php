@extends('layouts.app')

@section('content')
<div class="py-4">
    <a href="{{ route('dashboard.business.enquiries.index') }}" class="btn btn-light btn-sm mb-4"><i class="fa-solid fa-arrow-left me-1"></i> All Enquiries</a>
    <div class="glass-card bg-white border shadow-sm p-4 p-lg-5">
        <div class="d-flex justify-content-between flex-wrap gap-3 mb-4">
            <div><span class="badge bg-primary mb-2">{{ class_basename($enquiry->enquirable_type) }}</span><h2 class="fw-bold mb-1">{{ $enquiry->enquirable?->name ?? 'Deleted catalog item' }}</h2><div class="text-secondary">{{ $enquiry->business->business_name }}</div></div>
            <div class="text-end"><div class="small text-muted">Enquiry price</div><div class="fs-3 fw-bold text-primary">{{ $enquiry->price !== null ? '₹'.number_format($enquiry->price, 2) : 'Not specified' }}</div></div>
        </div>
        <div class="row g-4">
            <div class="col-lg-7"><h6 class="fw-bold">Requirement / description</h6><div class="bg-light rounded-3 p-3" style="white-space:pre-line">{{ $enquiry->description }}</div></div>
            <div class="col-lg-5"><h6 class="fw-bold">Enquiring user</h6><div class="border rounded-3 p-3"><strong>{{ $enquiry->user->name }}</strong><div class="small mt-2"><a href="mailto:{{ $enquiry->user->email }}">{{ $enquiry->user->email }}</a></div><div class="small">{{ $enquiry->user->phone }}</div></div></div>
        </div>
        <div class="small text-muted mt-4">Sent {{ $enquiry->created_at->format('d M Y, h:i A') }} · Status: {{ ucfirst($enquiry->status) }}</div>
    </div>
</div>
@endsection
