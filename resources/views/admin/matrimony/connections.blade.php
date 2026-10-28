@extends('admin.layouts.app')

@section('title', 'Matrimony Request Analysis')
@section('page-title', 'Matrimony - Request Analysis')

@section('content')
<div class="container-fluid">
    <div class="row g-3 mb-4">
        @foreach(['total' => 'Total Requests', 'pending' => 'Pending', 'accepted' => 'Accepted', 'rejected' => 'Rejected'] as $key => $label)
            <div class="col-6 col-xl-3"><div class="card shadow-sm border-0"><div class="card-body"><div class="text-muted small">{{ $label }}</div><div class="h3 mb-0">{{ $stats[$key] }}</div></div></div></div>
        @endforeach
    </div>
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <form class="row g-2">
                <div class="col-md-6"><input name="search" value="{{ request('search') }}" class="form-control" placeholder="Search sender or receiver"></div>
                <div class="col-md-3"><select name="status" class="form-select"><option value="">All statuses</option>@foreach(['pending','accepted','rejected'] as $status)<option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>@endforeach</select></div>
                <div class="col-md-3"><button class="btn btn-primary w-100"><i class="fas fa-search me-1"></i> Search</button></div>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>#</th><th>Sender</th><th>Receiver</th><th>Sent On</th><th>Status</th><th>Responded On</th><th>Action</th></tr></thead>
                <tbody>
                @forelse($connections as $connection)
                    <tr>
                        <td>{{ $connections->firstItem() + $loop->index }}</td>
                        <td><strong>{{ $connection->sender?->name ?? 'Deleted user' }}</strong><div class="small text-muted">{{ $connection->sender?->phone }}</div></td>
                        <td><strong>{{ $connection->receiver?->name ?? 'Deleted user' }}</strong><div class="small text-muted">{{ $connection->receiver?->phone }}</div></td>
                        <td>{{ $connection->created_at?->format('d M Y, h:i A') }}</td>
                        <td><span class="badge bg-{{ ['accepted'=>'success','rejected'=>'danger','pending'=>'warning'][$connection->status] ?? 'secondary' }}">{{ ucfirst($connection->status) }}</span></td>
                        <td>{{ $connection->responded_at?->format('d M Y, h:i A') ?? '-' }}</td>
                        <td><button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#request{{ $connection->id }}" title="View"><i class="fas fa-eye"></i></button></td>
                    </tr>
                    <div class="modal fade" id="request{{ $connection->id }}" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Request Details</h5><button class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><p><strong>Sender:</strong> {{ $connection->sender?->name ?? '-' }}</p><p><strong>Receiver:</strong> {{ $connection->receiver?->name ?? '-' }}</p><p><strong>Message:</strong> {{ $connection->message ?: '-' }}</p><p><strong>Response:</strong> {{ $connection->response_message ?: '-' }}</p><p><strong>Status:</strong> {{ ucfirst($connection->status) }}</p></div></div></div></div>
                @empty
                    <tr><td colspan="7" class="text-center py-5 text-muted">No matrimony requests found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($connections->hasPages())<div class="card-footer bg-white">{{ $connections->links('pagination::bootstrap-5') }}</div>@endif
    </div>
</div>
@endsection
