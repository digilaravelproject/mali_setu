@extends('layouts.app')

@section('content')
<style>
.convo-card { border-radius: 16px; background: rgba(255,255,255,0.8); border: 1px solid rgba(173,20,87,0.08); padding: 14px 18px; transition: all 0.3s ease; cursor: pointer; text-decoration: none; color: inherit; display: block; }
.convo-card:hover { transform: translateX(4px); border-color: var(--primary); color: inherit; }
.convo-avatar { width: 52px; height: 52px; border-radius: 14px; object-fit: cover; }
.convo-avatar-placeholder { width: 52px; height: 52px; border-radius: 14px; background: linear-gradient(135deg,#fce4ec,#f8bbd0); display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: 1.3rem; }
.unread-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--primary); }
</style>

<div class="py-4">
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('matrimony.index') }}" class="btn btn-light btn-sm rounded-3"><i class="fa-solid fa-arrow-left"></i></a>
        <div>
            <h4 class="fw-bold mb-0">Messages</h4>
            <p class="text-secondary small mb-0">Chat with your connected matrimony matches.</p>
        </div>
    </div>

    @if($conversations->count() === 0)
        <div class="glass-card text-center py-5">
            <div style="font-size:3rem;color:var(--primary);opacity:0.3;" class="mb-3"><i class="fa-solid fa-comments"></i></div>
            <h5 class="fw-bold">No Conversations Yet</h5>
            <p class="text-secondary">Start chatting after your connection requests are accepted.</p>
            <a href="{{ route('matrimony.requests') }}" class="btn btn-outline-primary rounded-3 px-4">View Requests</a>
        </div>
    @else
    <div class="glass-card">
        <h5 class="fw-bold text-primary mb-4"><i class="fa-solid fa-comments me-2"></i> Active Conversations</h5>
        <div class="d-flex flex-column gap-3">
            @foreach($conversations as $convo)
            @php
                $otherUser = $convo->user1_id === $user->id ? $convo->user2 : $convo->user1;
                $otherProfile = $otherUser?->matrimonyProfile;
                $lastMsg = $convo->latestMessage;
                $photos = $otherProfile->personal_details['photos'] ?? [];
            @endphp
            <a href="{{ route('matrimony.chat', $convo->id) }}" class="convo-card">
                <div class="d-flex align-items-center gap-3">
                    @if(!empty($photos[0]))
                        <img src="{{ asset('storage/' . $photos[0]) }}" class="convo-avatar">
                    @else
                        <img src="{{ asset('default-avatar.png') }}" class="convo-avatar">
                    @endif
                    <div class="flex-grow-1 min-width-0">
                        <div class="d-flex align-items-center justify-content-between">
                            <h6 class="fw-bold mb-0 text-truncate">{{ $otherUser->name ?? 'Unknown' }}</h6>
                            <small class="text-muted ms-2 text-nowrap">{{ $convo->last_message_at ? $convo->last_message_at->diffForHumans() : '' }}</small>
                        </div>
                        <p class="text-secondary small mb-0 text-truncate">
                            {{ $lastMsg ? ($lastMsg->sender_id == $user->id ? 'You: ' : '') . $lastMsg->message_text : 'Say hello! 👋' }}
                        </p>
                    </div>
                    <i class="fa-solid fa-chevron-right text-muted small ms-2"></i>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
