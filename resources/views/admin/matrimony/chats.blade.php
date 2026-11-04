@extends('admin.layouts.app')
@section('title','Matrimony Chats')
@section('page-title','Matrimony - Chats')
@push('styles')
<style>
 .chat-avatar{width:44px;height:44px;border-radius:50%;object-fit:cover;background:#eef2f6}
 .chat-members{display:flex;align-items:center;min-width:260px}.chat-members .chat-avatar+ .chat-avatar{margin-left:-12px;border:3px solid #fff}
 .chat-stat{border:1px solid #e6e9ef;border-radius:12px;padding:1rem;background:#fff}.chat-stat strong{font-size:1.35rem;color:#84144f}
</style>
@endpush
@section('content')
<div class="row g-3 mb-3">
 @foreach([['total_conversations','Conversations','fa-comments'],['active_today','Active Today','fa-bolt'],['total_messages','Messages','fa-message'],['messages_today','Messages Today','fa-calendar-day']] as [$key,$label,$icon])
 <div class="col-6 col-xl-3"><div class="chat-stat d-flex align-items-center gap-3"><i class="fas {{ $icon }} text-muted fs-4"></i><div><strong>{{ number_format($stats[$key]) }}</strong><div class="small text-muted">{{ $label }}</div></div></div></div>
 @endforeach
</div>
<div class="card"><div class="card-body border-bottom">
 <form class="d-flex gap-2"><input class="form-control" name="search" value="{{ request('search') }}" placeholder="Search member name or phone"><button class="btn btn-primary"><i class="fas fa-search me-1"></i>Search</button>@if(request('search'))<a class="btn btn-light border" href="{{ route('admin.matrimony.chats') }}">Reset</a>@endif</form>
</div><div class="table-responsive"><table class="table table-hover align-middle mb-0">
 <thead><tr><th>#</th><th>Members</th><th>Last Message</th><th>Messages</th><th>Last Activity</th></tr></thead>
 <tbody>
 @forelse($conversations as $conversation)
 @php
  $firstProfile=$conversation->user1?->matrimonyProfile;$secondProfile=$conversation->user2?->matrimonyProfile;
  $firstPhoto=$firstProfile?->profile_photo_url ?: asset('default-avatar.png');$secondPhoto=$secondProfile?->profile_photo_url ?: asset('default-avatar.png');
  $last=$conversation->latestMessage;
 @endphp
 <tr>
  <td>{{ $conversations->firstItem()+$loop->index }}</td>
  <td><div class="chat-members"><img class="chat-avatar" src="{{ $firstPhoto }}" alt=""><img class="chat-avatar" src="{{ $secondPhoto }}" alt=""><div class="ms-2"><strong>{{ $firstProfile?->candidate_name ?: ($conversation->user1?->name ?? 'Deleted user') }}</strong><span class="text-muted mx-1">&amp;</span><strong>{{ $secondProfile?->candidate_name ?: ($conversation->user2?->name ?? 'Deleted user') }}</strong></div></div></td>
  <td><div class="text-truncate" style="max-width:380px">{{ $last?->message_text ?: ($last?->message ?: 'No messages') }}</div><div class="small text-muted">{{ $last?->sender?->name }}</div></td>
  <td><span class="badge text-bg-light border">{{ $conversation->messages_count }}</span></td>
  <td class="text-nowrap">{{ $conversation->last_message_at?->format('d M Y, h:i A') ?: '-' }}</td>
 </tr>
 @empty<tr><td colspan="5" class="text-center text-muted py-5">No conversations found.</td></tr>@endforelse
 </tbody>
</table></div></div><div class="mt-3">{{ $conversations->links() }}</div>
@endsection
