@extends('layouts.app')

@section('styles')
<style>
.chat-wrapper { display: flex; flex-direction: column; height: calc(100vh - 180px); min-height: 500px; }
.chat-header { background: rgba(255,255,255,0.9); border-radius: 16px 16px 0 0; border: 1px solid rgba(255,71,87,0.08); padding: 14px 20px; backdrop-filter: blur(10px); }
.chat-body { flex: 1; overflow-y: auto; background: linear-gradient(160deg, #fff5f6 0%, rgba(255,71,87,0.02) 100%); padding: 20px; border-left: 1px solid rgba(255,71,87,0.08); border-right: 1px solid rgba(255,71,87,0.08); }
.chat-footer { background: rgba(255,255,255,0.95); border-radius: 0 0 16px 16px; border: 1px solid rgba(255,71,87,0.08); border-top: none; padding: 14px 20px; }
.msg-bubble { padding: 10px 16px; border-radius: 18px; font-size: 0.9rem; line-height: 1.5; word-break: break-word; }
.msg-mine { background: var(--primary); color: #fff; border-bottom-right-radius: 4px; margin-left: auto; }
.msg-theirs { background: #fff; color: #2d3748; border-bottom-left-radius: 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
.msg-time { font-size: 0.68rem; opacity: 0.6; margin-top: 3px; }
.avatar-sm { width: 36px; height: 36px; border-radius: 10px; object-fit: cover; }
.typing-indicator { display: none; font-size: 0.8rem; color: #adb5bd; padding: 4px 8px; }
</style>
@endsection

@section('content')
<div class="py-4">
    <div class="d-flex align-items-center gap-3 mb-3">
        <a href="{{ route('matrimony.conversations') }}" class="btn btn-light btn-sm rounded-3"><i class="fa-solid fa-arrow-left"></i></a>
        <h4 class="fw-bold mb-0">Chat</h4>
    </div>

    <div class="chat-wrapper">
        {{-- Header --}}
        <div class="chat-header d-flex align-items-center gap-3">
            @php
                $op = $otherUser->matrimonyProfile ?? null;
                $photos = $op->personal_details['photos'] ?? [];
            @endphp
            @if(!empty($photos[0]))
                <img src="{{ asset('storage/' . $photos[0]) }}" class="avatar-sm">
            @else
                <img src="{{ asset('default-avatar.png') }}" class="avatar-sm">
            @endif
            <div>
                <h6 class="fw-bold mb-0">{{ $otherUser->name }}</h6>
                @if($op)
                    <small class="text-muted">{{ $op->age ?? '' }}y • {{ $op->location_details['city'] ?? '' }}</small>
                @endif
            </div>
            @if($op)
                <a href="{{ route('matrimony.show', $op->id) }}" class="btn btn-outline-primary btn-sm rounded-3 ms-auto px-3">View Profile</a>
            @endif
        </div>

        {{-- Messages --}}
        <div class="chat-body" id="chatBody">
            @foreach($messages as $msg)
            @php $mine = $msg->sender_id === $user->id; @endphp
            <div class="d-flex {{ $mine ? 'justify-content-end' : 'justify-content-start' }} mb-3" data-msg-id="{{ $msg->id }}">
                @if(!$mine)
                    @if(!empty($photos[0]))
                        <img src="{{ asset('storage/' . $photos[0]) }}" class="avatar-sm me-2 mt-auto">
                    @else
                        <img src="{{ asset('default-avatar.png') }}" class="avatar-sm me-2 mt-auto">
                    @endif
                @endif
                <div>
                    <div class="msg-bubble {{ $mine ? 'msg-mine' : 'msg-theirs' }}">{{ $msg->message_text }}</div>
                    <div class="msg-time {{ $mine ? 'text-end' : '' }}">{{ $msg->created_at->format('h:i A') }}</div>
                </div>
            </div>
            @endforeach
            <div id="msgEnd"></div>
        </div>

        {{-- Input --}}
        <div class="chat-footer">
            <div class="d-flex gap-3 align-items-center">
                <input type="text" id="msgInput" class="form-control rounded-3 border-0 shadow-sm" placeholder="Type a message..." style="background:#f8f9fa;" maxlength="1000" autocomplete="off">
                <button class="btn btn-primary rounded-3 px-4 fw-bold" id="sendBtn" onclick="sendMessage()">
                    <i class="fa-solid fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
const conversationId = {{ $conversation->id }};
const userId = {{ $user->id }};
const sendUrl = "{{ route('matrimony.chat.send') }}";
const fetchUrl = "{{ route('matrimony.chat.fetch', $conversation->id) }}";
const csrfToken = "{{ csrf_token() }}";
let lastMsgId = {{ $messages->last()?->id ?? 0 }};

function scrollToBottom() {
    document.getElementById('msgEnd').scrollIntoView({ behavior: 'smooth' });
}

function renderMessage(msg) {
    const mine = msg.sender_id == userId;
    const time = new Date(msg.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    const existing = document.querySelector(`[data-msg-id="${msg.id}"]`);
    if (existing) return;

    const div = document.createElement('div');
    div.className = `d-flex ${mine ? 'justify-content-end' : 'justify-content-start'} mb-3`;
    div.dataset.msgId = msg.id;
    div.innerHTML = `
        <div>
            <div class="msg-bubble ${mine ? 'msg-mine' : 'msg-theirs'}">${msg.message_text}</div>
            <div class="msg-time ${mine ? 'text-end' : ''}">${time}</div>
        </div>`;
    document.getElementById('chatBody').insertBefore(div, document.getElementById('msgEnd'));
}

function sendMessage() {
    const input = document.getElementById('msgInput');
    const text = input.value.trim();
    if (!text) return;

    input.disabled = true;
    document.getElementById('sendBtn').disabled = true;

    fetch(sendUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ conversation_id: conversationId, message_text: text })
    }).then(r => r.json()).then(data => {
        if (data.success) {
            renderMessage(data.message);
            lastMsgId = data.message.id;
            scrollToBottom();
        }
    }).finally(() => {
        input.value = '';
        input.disabled = false;
        document.getElementById('sendBtn').disabled = false;
        input.focus();
    });
}

document.getElementById('msgInput').addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); }
});

// Polling every 5 seconds
setInterval(() => {
    fetch(`${fetchUrl}?after_id=${lastMsgId}`)
        .then(r => r.json()).then(data => {
            if (data.success && data.messages.length > 0) {
                data.messages.forEach(msg => {
                    renderMessage(msg);
                    lastMsgId = Math.max(lastMsgId, msg.id);
                });
                scrollToBottom();
            }
        });
}, 5000);

scrollToBottom();
</script>
@endsection
