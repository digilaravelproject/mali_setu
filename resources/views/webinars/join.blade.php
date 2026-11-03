@extends('layouts.app')
@section('title', ($isHost ? 'Host ' : 'Watch ').$webinar->title)

@section('styles')
<style>
    .live-stage { min-height: 70vh; background: #111827; border-radius: 1rem; position: relative; overflow: hidden; }
    .video-grid { min-height: 70vh; display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 10px; padding: 10px; }
    .video-tile { min-height: 320px; background: #020617; border-radius: .8rem; overflow: hidden; position: relative; }
    .video-label { position: absolute; left: 12px; bottom: 12px; z-index: 2; color: #fff; background: rgba(0,0,0,.62); border-radius: 999px; padding: 4px 10px; font-size: .8rem; }
    .waiting-state { position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #cbd5e1; text-align: center; padding: 2rem; }
    .live-controls { position: sticky; bottom: 16px; z-index: 5; }
</style>
@endsection

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <span class="badge bg-danger"><i class="fas fa-circle me-1"></i>{{ $isHost ? 'HOST' : 'LIVE' }}</span>
            <h4 class="d-inline ms-2">{{ $webinar->title }}</h4>
            <span id="connectionStatus" class="badge bg-secondary ms-2">Connecting…</span>
        </div>
        <a id="leaveButton" href="{{ $isHost ? route('admin.webinars.index') : route('webinars.show', $webinar) }}" class="btn btn-outline-secondary">
            <i class="fas fa-right-from-bracket me-1"></i>Leave
        </a>
    </div>

    <div class="live-stage">
        <div id="videoGrid" class="video-grid"></div>
        <div id="waitingState" class="waiting-state">
            <i class="fas fa-video fa-3x mb-3"></i>
            <h5>{{ $isHost ? 'Preparing your camera and microphone…' : 'Waiting for the host to go live…' }}</h5>
            <p class="mb-0">Keep this page open. The video will start automatically.</p>
        </div>
    </div>

    @if($isHost)
        <div class="text-center mt-3 live-controls">
            <button id="micButton" type="button" class="btn btn-light rounded-pill px-4" disabled><i class="fas fa-microphone me-1"></i>Mute</button>
            <button id="cameraButton" type="button" class="btn btn-light rounded-pill px-4" disabled><i class="fas fa-video me-1"></i>Camera off</button>
        </div>
    @endif

    <div id="agoraError" class="alert alert-warning mt-3 d-none" role="alert"></div>
</div>
@endsection

@section('scripts')
<script src="https://download.agora.io/sdk/release/AgoraRTC_N-4.24.8.js"></script>
<script>
(() => {
    const isHost = @json($isHost);
    const tokenUrl = @json($tokenUrl);
    const redirectUrl = @json($isHost ? route('admin.webinars.index') : route('webinars.show', $webinar));
    const grid = document.getElementById('videoGrid');
    const waiting = document.getElementById('waitingState');
    const status = document.getElementById('connectionStatus');
    const errorBox = document.getElementById('agoraError');
    let client = null;
    let localTracks = [];
    let credentials = null;
    let leaving = false;

    function showError(message) {
        errorBox.textContent = message;
        errorBox.classList.remove('d-none');
        status.textContent = 'Connection failed';
        status.className = 'badge bg-danger ms-2';
    }

    function setConnected() {
        status.textContent = isHost ? 'You are live' : 'Connected';
        status.className = 'badge bg-success ms-2';
    }

    function createTile(id, label) {
        let tile = document.getElementById(id);
        if (tile) return tile;
        tile = document.createElement('div');
        tile.id = id;
        tile.className = 'video-tile';
        const name = document.createElement('span');
        name.className = 'video-label';
        name.textContent = label;
        tile.appendChild(name);
        grid.appendChild(tile);
        waiting.classList.add('d-none');
        return tile;
    }

    async function fetchCredentials() {
        const response = await fetch(tokenUrl, {
            credentials: 'same-origin',
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            cache: 'no-store'
        });
        const body = await response.json().catch(() => ({}));
        if (!response.ok) throw new Error(body.message || 'Unable to authorize this live session.');
        return body;
    }

    async function renewToken() {
        credentials = await fetchCredentials();
        await client.renewToken(credentials.token);
    }

    async function connect() {
        if (!window.AgoraRTC) throw new Error('The Agora video SDK could not be loaded. Check your internet connection.');
        credentials = await fetchCredentials();
        client = AgoraRTC.createClient({ mode: 'live', codec: 'vp8' });
        await client.setClientRole(isHost ? 'host' : 'audience');

        client.on('user-published', async (user, mediaType) => {
            await client.subscribe(user, mediaType);
            if (mediaType === 'video') user.videoTrack.play(createTile(`remote-${user.uid}`, 'Host'));
            if (mediaType === 'audio') user.audioTrack.play();
        });
        client.on('user-unpublished', (user, mediaType) => {
            if (mediaType === 'video') document.getElementById(`remote-${user.uid}`)?.remove();
            if (!isHost && !grid.querySelector('.video-tile')) waiting.classList.remove('d-none');
        });
        client.on('user-left', user => {
            document.getElementById(`remote-${user.uid}`)?.remove();
            if (!isHost) waiting.classList.remove('d-none');
        });
        client.on('token-privilege-will-expire', () => renewToken().catch(error => showError(error.message)));
        client.on('connection-state-change', current => {
            if (current === 'CONNECTED') setConnected();
            if (current === 'RECONNECTING') {
                status.textContent = 'Reconnecting…';
                status.className = 'badge bg-warning text-dark ms-2';
            }
        });

        await client.join(credentials.app_id, credentials.channel, credentials.token, credentials.uid);

        if (isHost) {
            localTracks = await AgoraRTC.createMicrophoneAndCameraTracks();
            localTracks[1].play(createTile('local-host', 'You (Host)'));
            await client.publish(localTracks);
            document.getElementById('micButton').disabled = false;
            document.getElementById('cameraButton').disabled = false;
        }
        setConnected();
    }

    async function leave() {
        if (leaving) return;
        leaving = true;
        localTracks.forEach(track => { track.stop(); track.close(); });
        localTracks = [];
        if (client) await client.leave().catch(() => {});
    }

    document.getElementById('leaveButton').addEventListener('click', async event => {
        event.preventDefault();
        await leave();
        window.location.href = redirectUrl;
    });

    if (isHost) {
        document.getElementById('micButton').addEventListener('click', async function () {
            const muted = !localTracks[0].muted;
            await localTracks[0].setMuted(muted);
            this.innerHTML = muted ? '<i class="fas fa-microphone me-1"></i>Unmute' : '<i class="fas fa-microphone me-1"></i>Mute';
        });
        document.getElementById('cameraButton').addEventListener('click', async function () {
            const muted = !localTracks[1].muted;
            await localTracks[1].setMuted(muted);
            this.innerHTML = muted ? '<i class="fas fa-video me-1"></i>Camera on' : '<i class="fas fa-video me-1"></i>Camera off';
        });
    }

    window.addEventListener('pagehide', leave);
    connect().catch(error => {
        console.error('Agora connection error', error);
        showError(error.message || 'Unable to connect to the live webinar.');
    });
})();
</script>
@endsection
