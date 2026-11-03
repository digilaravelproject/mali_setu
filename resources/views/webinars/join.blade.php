@extends($isHost ? 'admin.layouts.app' : 'layouts.app')
@section('title', ($isHost ? 'Host ' : 'Join ').$webinar->title)
@section('page-title', 'Live Webinar')

@section('styles')
    @include('webinars.partials.live-styles')
@endsection
@push('styles')
    @include('webinars.partials.live-styles')
@endpush

@section('content')
<div class="meet-shell">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <a id="backButton" href="{{ $isHost ? route('admin.webinars.index') : route('webinars.show', $webinar) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back
        </a>
        <div class="d-flex align-items-center gap-2">
            <span class="live-dot"><span></span> LIVE</span>
            <span id="connectionStatus" class="meet-status">Connecting…</span>
        </div>
    </div>

    <div class="meet-heading mb-3">
        <div><h4 class="mb-1">{{ $webinar->title }}</h4><small>{{ $isHost ? 'Host controls enabled' : 'Live webinar meeting' }}</small></div>
        <button id="chatToggleTop" class="btn btn-dark rounded-pill" type="button"><i class="fas fa-message me-2"></i>Chat <span id="chatBadge" class="badge bg-danger d-none">0</span></button>
    </div>

    <div class="meet-workspace">
        <main class="meet-stage-wrap">
            <div id="videoGrid" class="meet-grid"></div>
            <div id="waitingState" class="meet-waiting">
                <div class="waiting-avatar"><i class="fas fa-video"></i></div>
                <h5>{{ $isHost ? 'Preparing your camera and microphone…' : 'Connecting to the meeting…' }}</h5>
                <p>{{ $isHost ? 'Allow browser access when prompted.' : 'Other participants will appear automatically.' }}</p>
            </div>
        </main>

        <aside id="chatPanel" class="meet-chat d-none">
            <div class="chat-header"><strong>In-call messages</strong><button id="chatClose" type="button" class="btn-close"></button></div>
            <div id="chatMessages" class="chat-messages"><div class="chat-empty">No messages yet</div></div>
            @if($webinar->allow_qa)
            <form id="chatForm" class="chat-form">
                <textarea id="chatInput" rows="2" maxlength="1000" placeholder="Send a message" required></textarea>
                <button type="submit" aria-label="Send message"><i class="fas fa-paper-plane"></i></button>
            </form>
            @else
            <div class="p-3 text-muted small border-top">Chat is disabled for this webinar.</div>
            @endif
        </aside>
    </div>

    <div class="meet-bottom-bar">
        <div class="meeting-info d-none d-lg-block"><strong>{{ now()->format('h:i A') }}</strong><span></span>{{ Str::limit($webinar->title, 28) }}</div>
        <div class="meet-controls">
            <button id="micButton" class="control-button" type="button" disabled title="Microphone"><i class="fas fa-microphone"></i></button>
            <button id="cameraButton" class="control-button" type="button" disabled title="Camera"><i class="fas fa-video"></i></button>
            <button id="screenButton" class="control-button" type="button" disabled title="Present screen"><i class="fas fa-desktop"></i></button>
            <button id="leaveButton" class="control-button hangup" type="button" title="Leave meeting"><i class="fas fa-phone-slash"></i></button>
        </div>
        <div class="meet-actions">
            <button id="participantButton" class="action-button" type="button" title="Participants"><i class="fas fa-users"></i><span id="participantCount">1</span></button>
            <button id="chatButton" class="action-button" type="button" title="Chat"><i class="fas fa-message"></i></button>
        </div>
    </div>

    <div id="meetingToast" class="meeting-toast d-none"></div>
    <div id="agoraError" class="alert alert-warning mt-3 d-none" role="alert"></div>
</div>
@endsection

@section('scripts')
    @include('webinars.partials.live-script')
@endsection
@push('scripts')
    @include('webinars.partials.live-script')
@endpush
