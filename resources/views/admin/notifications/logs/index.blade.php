@extends('admin.layouts.app')
@section('title', 'Notification Logs')
@section('page-title', 'Notification Logs')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div><h3 class="fw-bold mb-1">Notification Logs</h3><div class="text-muted">Push delivery attempts and in-app notification records.</div></div>
    <a href="{{ route('admin.notifications.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Push Notifications</a>
</div>
<div class="card mb-3"><div class="card-body">
    <form class="row g-2 align-items-end">
        <div class="col-md-4"><label class="form-label">Search</label><input name="search" value="{{ request('search') }}" class="form-control" placeholder="Title, message or user"></div>
        <div class="col-md-3"><label class="form-label">Push status</label><select name="status" class="form-select"><option value="">All statuses</option><option value="sent" @selected(request('status') === 'sent')>Sent</option><option value="failed" @selected(request('status') === 'failed')>Failed / unsent</option><option value="not_attempted" @selected(request('status') === 'not_attempted')>Not attempted</option></select></div>
        <div class="col-md-3"><label class="form-label">Type</label><select name="type" class="form-select"><option value="">All types</option>@foreach($types as $type)<option value="{{ $type }}" @selected(request('type') === $type)>{{ str($type)->replace('_', ' ')->title() }}</option>@endforeach</select></div>
        <div class="col-md-2 d-flex gap-2"><button class="btn btn-primary flex-fill"><i class="fas fa-search"></i></button><a href="{{ route('admin.notifications.logs.index') }}" class="btn btn-light border">Reset</a></div>
    </form>
</div></div>
<div class="card"><div class="table-responsive"><table class="table table-hover align-middle mb-0">
    <thead><tr><th>#</th><th>Created</th><th>User</th><th>Type</th><th>Title</th><th>Push status</th><th>Read</th><th class="text-end">Action</th></tr></thead>
    <tbody>@forelse($notifications as $notification)<tr>
        <td>{{ $notifications->firstItem() + $loop->index }}</td>
        <td>{{ $notification->created_at?->format('d M Y, h:i A') }}</td>
        <td><strong>{{ $notification->user?->name ?? 'Deleted user' }}</strong><div class="small text-muted">{{ $notification->user?->email }}</div></td>
        <td>{{ str($notification->type)->replace('_', ' ')->title() }}</td><td>{{ $notification->title }}</td>
        <td>@if($notification->push_sent)<span class="badge text-bg-success">Sent</span>@elseif($notification->push_attempted_at)<span class="badge text-bg-danger">Failed / unsent</span>@else<span class="badge text-bg-secondary">Not attempted</span>@endif</td>
        <td><span class="badge {{ $notification->is_read ? 'text-bg-info' : 'text-bg-light border' }}">{{ $notification->is_read ? 'Read' : 'Unread' }}</span></td>
        <td class="text-end"><a href="{{ route('admin.notifications.logs.show', $notification) }}" class="btn btn-sm btn-outline-primary" title="View"><i class="fas fa-eye"></i></a></td>
    </tr>@empty<tr><td colspan="8" class="text-center text-muted py-5">No notification logs found.</td></tr>@endforelse</tbody>
</table></div></div><div class="mt-3">{{ $notifications->links() }}</div>
@endsection
