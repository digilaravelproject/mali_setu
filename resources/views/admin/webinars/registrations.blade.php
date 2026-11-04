@extends('admin.layouts.app')
@section('title','Webinar Registrations')
@section('page-title','Webinar Registrations')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div><h4 class="mb-1">{{ $webinar->title }}</h4><div class="text-muted">{{ number_format($registrations->total()) }} registered user(s)</div></div>
    <a href="{{ route('admin.webinars.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Back to webinars</a>
</div>
<div class="card"><div class="table-responsive"><table class="table table-hover align-middle mb-0">
    <thead><tr><th>#</th><th>User</th><th>Phone</th><th>Registered On</th><th class="text-end">Action</th></tr></thead>
    <tbody>
    @forelse($registrations as $registration)
        @php($user = $registration->user)
        <tr>
            <td>{{ $registrations->firstItem() + $loop->index }}</td>
            <td><div class="d-flex align-items-center gap-2">@if($user?->photo)<img src="{{ asset('storage/'.$user->photo) }}" alt="" class="rounded-circle" style="width:42px;height:42px;object-fit:cover">@else<div class="rounded-circle bg-light d-grid" style="width:42px;height:42px;place-items:center"><i class="fas fa-user text-muted"></i></div>@endif<div><strong>{{ $user?->name ?? 'Deleted user' }}</strong><div class="small text-muted">{{ $user?->email }}</div></div></div></td>
            <td>{{ $user?->phone ?: '-' }}</td>
            <td>{{ $registration->created_at?->timezone(config('app.webinar_timezone'))->format('d M Y, h:i A') }}</td>
            <td class="text-end">@if($user)<a href="{{ route('admin.users.show',$user->id) }}" class="btn btn-sm btn-outline-primary" title="View details"><i class="fas fa-eye"></i></a>@endif</td>
        </tr>
    @empty<tr><td colspan="5" class="text-center text-muted py-5">No users have registered for this webinar.</td></tr>@endforelse
    </tbody>
</table></div></div>
<div class="mt-3">{{ $registrations->links() }}</div>
@endsection
