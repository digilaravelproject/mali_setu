@extends('admin.layouts.app')
@section('title','Chat Conversation')
@section('page-title','Matrimony - Chat Conversation')
@push('styles')
<style>
 .conversation-shell{max-width:1050px;margin:auto}.conversation-header{position:sticky;top:0;z-index:2}.person-avatar{width:54px;height:54px;border-radius:50%;object-fit:cover;background:#eef2f6}.message-list{background:#f4f6f9;min-height:450px;padding:1.25rem}.message-row{display:flex;margin-bottom:1rem}.message-row.right{justify-content:flex-end}.message-bubble{max-width:min(72%,680px);padding:.75rem 1rem;border-radius:16px 16px 16px 4px;background:#fff;box-shadow:0 1px 3px rgba(0,0,0,.08)}.message-row.right .message-bubble{background:#f7ddea;border-radius:16px 16px 4px 16px}.message-meta{font-size:.7rem;color:#64748b;margin-top:.4rem}.message-delete{opacity:.65}.message-bubble:hover .message-delete{opacity:1}
</style>
@endpush
@section('content')
@php
 $firstProfile=$conversation->user1?->matrimonyProfile;$secondProfile=$conversation->user2?->matrimonyProfile;
 $firstName=$firstProfile?->candidate_name ?: ($conversation->user1?->name ?? 'Deleted user');$secondName=$secondProfile?->candidate_name ?: ($conversation->user2?->name ?? 'Deleted user');
 $firstPhoto=$firstProfile?->profile_photo_url ?: asset('default-avatar.png');$secondPhoto=$secondProfile?->profile_photo_url ?: asset('default-avatar.png');
@endphp
<div class="conversation-shell">
 <div class="d-flex justify-content-between align-items-center mb-3"><a href="{{ route('admin.matrimony.chats') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Back to chats</a><span class="badge text-bg-light border">{{ $messages->total() }} messages</span></div>
 <div class="card overflow-hidden">
  <div class="card-header conversation-header"><div class="row align-items-center g-3"><div class="col"><div class="d-flex align-items-center gap-2"><img src="{{ $firstPhoto }}" class="person-avatar" alt=""><div><strong>{{ $firstName }}</strong><div class="small text-muted">{{ $conversation->user1?->phone }}</div></div></div></div><div class="col-auto text-muted"><i class="fas fa-comments fs-4"></i></div><div class="col"><div class="d-flex align-items-center justify-content-end gap-2"><div class="text-end"><strong>{{ $secondName }}</strong><div class="small text-muted">{{ $conversation->user2?->phone }}</div></div><img src="{{ $secondPhoto }}" class="person-avatar" alt=""></div></div></div></div>
  <div class="message-list">
   @forelse($messages as $message)
    @php($fromFirst=$message->sender_id===$conversation->user1_id)
    <div class="message-row {{ $fromFirst ? '' : 'right' }}"><div class="message-bubble">
     <div class="d-flex justify-content-between gap-3 align-items-start"><strong class="small">{{ $message->sender?->name ?? 'Deleted user' }}</strong><form method="POST" action="{{ route('admin.matrimony.chats.messages.destroy',[$conversation,$message]) }}" onsubmit="return confirm('Delete this message?')">@csrf @method('DELETE')<button class="btn btn-link text-danger p-0 message-delete" title="Delete message"><i class="fas fa-trash"></i></button></form></div>
     @if($message->message_type==='image' && $message->attachment_path)<img src="{{ asset('storage/'.$message->attachment_path) }}" class="img-fluid rounded mt-2" style="max-height:320px" alt="Chat image">@elseif($message->attachment_path)<a href="{{ asset('storage/'.$message->attachment_path) }}" target="_blank" class="d-block mt-2"><i class="fas fa-paperclip me-1"></i>Open attachment</a>@endif
     @if($message->message_text || $message->message)<div class="mt-1" style="white-space:pre-wrap">{{ $message->message_text ?: $message->message }}</div>@endif
     <div class="message-meta">{{ $message->created_at?->format('d M Y, h:i A') }} @if($message->is_read)<i class="fas fa-check-double ms-1" title="Read"></i>@endif</div>
    </div></div>
   @empty<div class="text-center text-muted py-5">No messages in this conversation.</div>@endforelse
  </div>
 </div>
 <div class="mt-3">{{ $messages->links() }}</div>
</div>
@endsection
