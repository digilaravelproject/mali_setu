@extends('admin.layouts.app')
@section('title','Manage Webinars')
@section('page-title','Manage Webinars')
@push('styles')
<style>
 .webinar-tabs { display:flex; flex-wrap:nowrap; gap:0; overflow-x:auto; border-bottom:1px solid #e4e8ef; }
 .webinar-tabs .nav-item { margin:0; flex:0 0 auto; }
 .webinar-tabs .nav-link { display:block !important; padding:.85rem 1.25rem; color:#4d5565 !important; background:transparent !important; border:0; border-bottom:3px solid transparent; border-radius:0; transform:none !important; white-space:nowrap; font-weight:600; }
 .webinar-tabs .nav-link:hover { color:#9b0056 !important; background:#fff6fa !important; transform:none !important; }
 .webinar-tabs .nav-link.active { color:#9b0056 !important; background:#fff !important; border-bottom-color:#9b0056; transform:none !important; }
</style>
@endpush
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
 <div class="text-muted small">Home <span class="mx-2">›</span> Broadcast / Webinars</div>
 <a class="btn btn-primary" href="{{ route('admin.webinars.create') }}"><i class="fas fa-plus me-2"></i>Create Webinar</a>
</div>
<div class="card border-0 shadow-sm"><div class="card-body">
 <ul class="nav webinar-tabs mb-3">
 @foreach(['all'=>'All','upcoming'=>'Upcoming','live'=>'Live','completed'=>'Completed'] as $key=>$label)
  <li class="nav-item"><a class="nav-link {{ request('tab','all')===$key?'active':'' }}" href="{{ route('admin.webinars.index',['tab'=>$key]) }}">{{ $label }} ({{ $counts[$key] }})</a></li>
 @endforeach
 </ul>
 <form class="d-flex gap-2 mb-3"><input type="hidden" name="tab" value="{{ request('tab','all') }}"><input class="form-control" name="search" value="{{ request('search') }}" placeholder="Search webinars..."><button class="btn btn-outline-dark"><i class="fas fa-filter me-1"></i>Filter</button></form>
 <div class="table-responsive"><table class="table table-hover align-middle"><thead><tr><th>#</th><th>Title</th><th>Category</th><th>Date & Time</th><th>Status</th><th>Registrations</th><th class="text-end">Action</th></tr></thead><tbody>
 @forelse($webinars as $webinar)<tr><td>{{ $webinars->firstItem()+$loop->index }}</td><td><div class="d-flex align-items-center gap-2">@if($webinar->banner_url)<img src="{{ $webinar->banner_url }}" style="width:64px;height:42px;object-fit:cover;border-radius:7px">@endif<div><strong>{{ $webinar->title }}</strong><div class="small text-muted">{{ $webinar->speaker_name }}</div></div></div></td><td>@foreach($webinar->selected_categories as $category)<span class="badge rounded-pill me-1 {{ $category==='business'?'text-bg-danger':($category==='matrimony'?'text-bg-warning':'text-bg-primary') }}">{{ ucfirst($category) }}</span>@endforeach</td><td>{{ $webinar->starts_at->timezone(config('app.webinar_timezone'))->format('d M Y') }}<div class="small text-muted">{{ $webinar->starts_at->timezone(config('app.webinar_timezone'))->format('h:i A') }} IST</div></td><td><span class="badge bg-{{ $webinar->webinar_status==='live'?'success':($webinar->webinar_status==='upcoming'?'primary':'secondary') }}">{{ ucfirst($webinar->webinar_status) }}</span></td><td>{{ $webinar->registrations_count }}</td><td class="text-end text-nowrap">@if(auth()->user()->hasAdminPermission('webinars.edit')) @if($webinar->adminCanGoLive())<a class="btn btn-sm btn-success" href="{{ route('admin.webinars.go-live',$webinar) }}" title="Go live as host"><i class="fas fa-broadcast-tower me-1"></i>Go Live</a>@elseif($webinar->status === 'published' && now()->lt($webinar->starts_at->copy()->subMinutes(10)))<button class="btn btn-sm btn-outline-success" disabled title="Available 10 minutes before the scheduled time"><i class="fas fa-clock me-1"></i>Go Live</button>@endif <a class="btn btn-sm btn-info text-white" href="{{ route('admin.webinars.edit',$webinar) }}"><i class="fas fa-edit"></i></a>@endif <form class="d-inline" method="POST" action="{{ route('admin.webinars.destroy',$webinar) }}" onsubmit="return confirm('Delete this webinar?')">@csrf @method('DELETE')<button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button></form></td></tr>
 @empty<tr><td colspan="7" class="text-center py-5 text-muted">No webinars found.</td></tr>@endforelse
 </tbody></table></div>{{ $webinars->links() }}
</div></div>
@endsection
