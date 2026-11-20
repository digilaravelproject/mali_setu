@extends('admin.layouts.app')
@section('title','States')
@section('page-title','Location - States')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
    <div><h3 class="fw-bold mb-1">States</h3><p class="text-muted mb-0">Read-only state master synchronized from LGD data.</p></div>
    <form method="POST" action="{{ route('admin.locations.sync') }}">@csrf<input type="hidden" name="level" value="states"><button class="btn btn-primary"><i class="fas fa-rotate me-1"></i> Sync States</button></form>
</div>
@if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
<div class="card mb-4"><div class="card-body"><form method="GET" class="row g-2 align-items-end"><div class="col-md-8"><label class="form-label">Search state</label><input name="search" value="{{ request('search') }}" class="form-control" placeholder="State name"></div><div class="col-md-2"><label class="form-label">Status</label><select name="status" class="form-select"><option value="">All</option><option value="active" @selected(request('status')==='active')>Active</option><option value="inactive" @selected(request('status')==='inactive')>Inactive</option></select></div><div class="col-md-2 d-flex gap-2"><button class="btn btn-primary flex-grow-1">Filter</button><a href="{{ route('admin.locations.states.index') }}" class="btn btn-outline-secondary"><i class="fas fa-times"></i></a></div></form></div></div>
<div class="card"><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead><tr><th>#</th><th>State key</th><th>State</th><th>Districts</th><th>Status</th><th>Last synced</th></tr></thead><tbody>
@forelse($states as $state)<tr><td>{{ $states->firstItem()+$loop->index }}</td><td><code>{{ $state->external_code }}</code></td><td class="fw-semibold">{{ $state->name }}</td><td><span class="badge text-bg-light border">{{ $state->districts_count }}</span></td><td><span class="badge {{ $state->is_active?'text-bg-success':'text-bg-secondary' }}">{{ $state->is_active?'Active':'Inactive' }}</span></td><td>{{ $state->synced_at?->format('d M Y, h:i A') ?? 'Legacy data' }}</td></tr>
@empty<tr><td colspan="6" class="text-center text-muted py-5">No states found. Click Sync States to import them.</td></tr>@endforelse
</tbody></table></div></div><div class="mt-3">{{ $states->links() }}</div>
@endsection
