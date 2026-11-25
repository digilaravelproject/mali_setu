@extends('layouts.app')

@section('content')
<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div><h2 class="fw-bold mb-1">Business Enquiries</h2><p class="text-secondary mb-0">Enquiries you sent and received.</p></div>
        <a href="{{ route('dashboard.business.browse') }}" class="btn btn-outline-primary">Browse Businesses</a>
    </div>
    <div class="glass-card bg-white border shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light"><tr><th>Item</th><th>Business</th><th>From</th><th>Price</th><th>Status</th><th>Date</th><th></th></tr></thead>
                <tbody>
                @forelse($enquiries as $enquiry)
                    <tr>
                        <td><strong>{{ $enquiry->enquirable?->name ?? 'Deleted item' }}</strong><div class="small text-muted">{{ class_basename($enquiry->enquirable_type) }}</div></td>
                        <td>{{ $enquiry->business->business_name }}</td>
                        <td>{{ $enquiry->user->name }}</td>
                        <td>{{ $enquiry->price !== null ? '₹'.number_format($enquiry->price, 2) : '—' }}</td>
                        <td><span class="badge bg-{{ $enquiry->status === 'new' ? 'primary' : ($enquiry->status === 'closed' ? 'secondary' : 'success') }}">{{ ucfirst($enquiry->status) }}</span></td>
                        <td>{{ $enquiry->created_at->format('d M Y, h:i A') }}</td>
                        <td><a href="{{ route('dashboard.business.enquiries.show', $enquiry) }}" class="btn btn-sm btn-outline-primary">View</a></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-5">No enquiries yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $enquiries->links() }}</div>
</div>
@endsection
