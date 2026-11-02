@extends('admin.layouts.app')
@section('title','Manage Webinars')
@section('page-title','Manage Webinars')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
 <div class="text-muted small">Home <span class="mx-2">›</span> Broadcast / Webinars</div>
 <a class="btn btn-primary" href="{{ route('admin.webinars.create') }}"><i class="fas fa-plus me-2"></i>Create Webinar</a>
</div>
<div class="card border-0 shadow-sm"><div class="card-body">
 <div class="nav nav-tabs mb-3">
 @foreach(['all'=>'All','upcoming'=>'Upcoming','live'=>'Live','completed'=>'Completed'] as $key=>$label)
  <a class="nav-link {{ request('tab','all')===$key?'active':'' }}" href="{{ route('admin.webinars.index',['tab'=>$key]) }}">{{ $label }} ({{ $counts[$key] }})</a>
 @endforeach
 </div>
 <form class="d-flex gap-2 mb-3"><input type="hidden" name="tab" value="{{ request('tab','all') }}"><input class="form-control" name="search" value="{{ request('search') }}" placeholder="Search webinars..."><button class="btn btn-outline-dark"><i class="fas fa-filter me-1"></i>Filter</button></form>
 <div class="table-responsive"><table class="table table-hover align-middle"><thead><tr><th>#</th><th>Title</th><th>Date & Time</th><th>Status</th><th>Registrations</th><th>Agora Link</th><th class="text-end">Action</th></tr></thead><tbody>
 @forelse($webinars as $webinar)<tr><td>{{ $webinars->firstItem()+$loop->index }}</td><td><div class="d-flex align-items-center gap-2">@if($webinar->banner_url)<img src="{{ $webinar->banner_url }}" style="width:64px;height:42px;object-fit:cover;border-radius:7px">@endif<div><strong>{{ $webinar->title }}</strong><div class="small text-muted">{{ $webinar->speaker_name }}</div></div></div></td><td>{{ $webinar->starts_at->format('d M Y') }}<div class="small text-muted">{{ $webinar->starts_at->format('h:i A') }}</div></td><td><span class="badge bg-{{ $webinar->webinar_status==='live'?'success':($webinar->webinar_status==='upcoming'?'primary':'secondary') }}">{{ ucfirst($webinar->webinar_status) }}</span></td><td>{{ $webinar->registrations_count }}</td><td><a href="{{ $webinar->agora_link }}" target="_blank">Join link</a></td><td class="text-end"><a class="btn btn-sm btn-info text-white" href="{{ route('admin.webinars.edit',$webinar) }}"><i class="fas fa-edit"></i></a> <form class="d-inline" method="POST" action="{{ route('admin.webinars.destroy',$webinar) }}" onsubmit="return confirm('Delete this webinar?')">@csrf @method('DELETE')<button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button></form></td></tr>
 @empty<tr><td colspan="7" class="text-center py-5 text-muted">No webinars found.</td></tr>@endforelse
 </tbody></table></div>{{ $webinars->links() }}
</div></div>
@endsection
