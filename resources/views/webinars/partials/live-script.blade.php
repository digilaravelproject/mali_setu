<script src="https://download.agora.io/sdk/release/AgoraRTC_N-4.24.8.js"></script>
<script>
(() => {
    const options = {
        isHost: @json($isHost),
        tokenUrl: @json($tokenUrl),
        commentsUrl: @json($commentsUrl),
        controlsUrl: @json($controlsUrl ?? null),
        moderationUrl: @json($moderationUrl ?? null),
        participantUrl: @json($participantUrl ?? null),
        redirectUrl: @json($isHost ? route('admin.webinars.index') : route('webinars.show', $webinar)),
        csrf: @json(csrf_token()),
        chatEnabled: @json((bool) $webinar->allow_qa),
    };
    const grid = document.getElementById('videoGrid');
    const waiting = document.getElementById('waitingState');
    const status = document.getElementById('connectionStatus');
    const errorBox = document.getElementById('agoraError');
    const toastBox = document.getElementById('meetingToast');
    const chatPanel = document.getElementById('chatPanel');
    const chatMessages = document.getElementById('chatMessages');
    let client, credentials, audioTrack, cameraTrack, screenTrack;
    let leaving = false, audioMuted = false, videoMuted = false, sharingScreen = false;
    let lastCommentId = 0, lastControlId = null, unread = 0;
    let chatTimer, controlTimer;

    const requestJson = async (url, init = {}) => {
        const response = await fetch(url, {
            credentials: 'same-origin', cache: 'no-store', ...init,
            headers: { 'Accept':'application/json', 'X-Requested-With':'XMLHttpRequest', 'X-CSRF-TOKEN':options.csrf, ...(init.headers || {}) }
        });
        const body = response.status === 204 ? null : await response.json().catch(() => ({}));
        if (!response.ok) throw new Error(body?.message || 'Request failed. Please try again.');
        return body;
    };
    const showToast = message => {
        toastBox.textContent = message; toastBox.classList.remove('d-none');
        clearTimeout(showToast.timer); showToast.timer = setTimeout(() => toastBox.classList.add('d-none'), 3500);
    };
    const showError = message => {
        errorBox.textContent = message; errorBox.classList.remove('d-none');
        status.textContent = 'Connection failed'; status.style.color = '#d93025';
    };
    const setConnected = () => { status.textContent = options.isHost ? 'You are live' : 'Connected'; status.style.color = '#188038'; };
    const memberId = uid => String(uid).startsWith('user-') ? Number(String(uid).slice(5)) : null;
    const displayName = uid => String(uid).startsWith('host-') ? 'Host' : `Member ${memberId(uid) || uid}`;
    const updateParticipantCount = () => document.getElementById('participantCount').textContent = (client?.remoteUsers.length || 0) + 1;

    async function resolveName(uid, label) {
        const id = memberId(uid);
        if (!options.isHost || !id || !options.participantUrl) return;
        try {
            const person = await requestJson(options.participantUrl.replace('__USER__', id));
            label.textContent = person.name;
        } catch (_) {}
    }

    function tileFor(uid, local = false) {
        const tileId = local ? 'local-participant' : `remote-${uid}`;
        let tile = document.getElementById(tileId);
        if (tile) return tile;
        tile = document.createElement('div'); tile.id = tileId; tile.className = 'meet-tile';
        const player = document.createElement('div'); player.className = 'meet-player'; tile.appendChild(player);
        const label = document.createElement('span'); label.className = 'meet-name'; label.textContent = local ? `${credentials.display_name} (You)` : displayName(uid); tile.appendChild(label);
        const mic = document.createElement('span'); mic.className = 'meet-mic-state'; mic.innerHTML = '<i class="fas fa-microphone"></i>'; tile.appendChild(mic);
        if (options.isHost && !local && memberId(uid)) tile.appendChild(moderationMenu(memberId(uid)));
        grid.appendChild(tile); waiting.classList.add('d-none'); updateParticipantCount(); resolveName(uid, label);
        return tile;
    }

    function moderationMenu(userId) {
        const wrap = document.createElement('div'); wrap.className = 'dropdown participant-menu';
        wrap.innerHTML = `<button class="btn btn-sm btn-dark rounded-circle" data-bs-toggle="dropdown" aria-label="Participant controls"><i class="fas fa-ellipsis-vertical"></i></button>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><button class="dropdown-item" data-control="mute_audio"><i class="fas fa-microphone-slash me-2"></i>Mute participant</button></li>
              <li><button class="dropdown-item" data-control="disable_video"><i class="fas fa-video-slash me-2"></i>Turn off camera</button></li>
              <li><button class="dropdown-item" data-control="ask_unmute"><i class="fas fa-microphone me-2"></i>Ask to unmute</button></li>
              <li><button class="dropdown-item" data-control="ask_video"><i class="fas fa-video me-2"></i>Ask to turn on camera</button></li>
              <li><hr class="dropdown-divider"></li><li><button class="dropdown-item text-danger" data-control="remove"><i class="fas fa-user-xmark me-2"></i>Remove participant</button></li>
            </ul>`;
        wrap.querySelectorAll('[data-control]').forEach(button => button.addEventListener('click', async () => {
            try {
                await requestJson(options.moderationUrl, { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({user_id:userId, action:button.dataset.control}) });
                showToast('Participant control sent.');
            } catch (error) { showError(error.message); }
        }));
        return wrap;
    }

    async function renewToken() {
        credentials = await requestJson(options.tokenUrl);
        await client.renewToken(credentials.token);
    }

    async function connect() {
        if (!window.AgoraRTC) throw new Error('Agora video SDK could not be loaded.');
        credentials = await requestJson(options.tokenUrl);
        client = AgoraRTC.createClient({mode:'live', codec:'vp8'});
        await client.setClientRole('host');
        client.on('user-published', async (user, mediaType) => {
            await client.subscribe(user, mediaType);
            const tile = tileFor(user.uid);
            if (mediaType === 'video') user.videoTrack.play(tile.querySelector('.meet-player'));
            if (mediaType === 'audio') { user.audioTrack.play(); tile.querySelector('.meet-mic-state').innerHTML = '<i class="fas fa-microphone"></i>'; }
            updateParticipantCount();
        });
        client.on('user-unpublished', (user, mediaType) => {
            const tile = document.getElementById(`remote-${user.uid}`);
            if (mediaType === 'audio' && tile) tile.querySelector('.meet-mic-state').innerHTML = '<i class="fas fa-microphone-slash"></i>';
            if (mediaType === 'video' && tile) tile.querySelector('.meet-player').innerHTML = '';
        });
        client.on('user-left', user => { document.getElementById(`remote-${user.uid}`)?.remove(); updateParticipantCount(); });
        client.on('token-privilege-will-expire', () => renewToken().catch(error => showError(error.message)));
        client.on('connection-state-change', state => {
            if (state === 'CONNECTED') setConnected();
            if (state === 'RECONNECTING') status.textContent = 'Reconnecting…';
        });
        await client.join(credentials.app_id, credentials.channel, credentials.token, credentials.uid);
        const localTile = tileFor(credentials.uid, true);
        try { audioTrack = await AgoraRTC.createMicrophoneAudioTrack(); } catch (_) { showToast('Microphone is unavailable. You can still watch the meeting.'); }
        try { cameraTrack = await AgoraRTC.createCameraVideoTrack(); cameraTrack.play(localTile.querySelector('.meet-player')); } catch (_) { showToast('Camera is unavailable. You can still use audio.'); }
        const publishable = [audioTrack, cameraTrack].filter(Boolean); if (publishable.length) await client.publish(publishable);
        document.getElementById('micButton').disabled = !audioTrack; document.getElementById('cameraButton').disabled = !cameraTrack; document.getElementById('screenButton').disabled = false;
        setConnected(); updateParticipantCount(); startPolling();
    }

    async function toggleAudio(force) {
        if (!audioTrack) return; const next = force ?? !audioMuted; await audioTrack.setMuted(next); audioMuted = next;
        const button = document.getElementById('micButton'); button.classList.toggle('off', next); button.innerHTML = `<i class="fas fa-${next?'microphone-slash':'microphone'}"></i>`;
        tileFor(credentials.uid, true).querySelector('.meet-mic-state').innerHTML = `<i class="fas fa-${next?'microphone-slash':'microphone'}"></i>`;
    }
    async function toggleVideo(force) {
        if (!cameraTrack) return; const next = force ?? !videoMuted; await cameraTrack.setMuted(next); videoMuted = next;
        const button = document.getElementById('cameraButton'); button.classList.toggle('off', next); button.innerHTML = `<i class="fas fa-video${next?'-slash':''}"></i>`;
    }
    async function toggleScreen() {
        if (sharingScreen) return stopScreenShare();
        screenTrack = await AgoraRTC.createScreenVideoTrack({}, 'disable');
        screenTrack.on('track-ended', stopScreenShare); if(cameraTrack) await client.unpublish(cameraTrack); await client.publish(screenTrack);
        screenTrack.play(tileFor(credentials.uid, true).querySelector('.meet-player')); sharingScreen = true; document.getElementById('screenButton').classList.add('off'); showToast('You are presenting your screen.');
    }
    async function stopScreenShare() {
        if (!sharingScreen) return; await client.unpublish(screenTrack).catch(()=>{}); screenTrack.stop(); screenTrack.close(); screenTrack = null;
        if(cameraTrack){await client.publish(cameraTrack);cameraTrack.play(tileFor(credentials.uid, true).querySelector('.meet-player'))} sharingScreen = false; document.getElementById('screenButton').classList.remove('off');
    }

    function renderComments(comments) {
        if (!comments?.length) return;
        chatMessages.querySelector('.chat-empty')?.remove();
        comments.forEach(item => {
            if (document.getElementById(`chat-${item.id}`)) return;
            const row = document.createElement('div'); row.id = `chat-${item.id}`; row.className = 'chat-item';
            const meta = document.createElement('div'); meta.className = 'chat-meta'; const strong = document.createElement('strong'); strong.textContent = item.mine ? 'You' : item.name; meta.append(strong, document.createTextNode(item.time));
            const text = document.createElement('div'); text.className = 'chat-text'; text.textContent = item.message; row.append(meta,text); chatMessages.appendChild(row); lastCommentId = Math.max(lastCommentId,item.id);
            if (chatPanel.classList.contains('d-none') && !item.mine) unread++;
        });
        chatMessages.scrollTop = chatMessages.scrollHeight; updateChatBadge();
    }
    async function pollComments() { if (!options.chatEnabled || leaving) return; try { renderComments(await requestJson(`${options.commentsUrl}?after=${lastCommentId}`)); } catch (_) {} }
    async function pollControls() {
        if (!options.controlsUrl || leaving) return;
        try {
            const command = await requestJson(`${options.controlsUrl}?seen=${encodeURIComponent(lastControlId || '')}`); if (!command?.id) return; lastControlId = command.id;
            if (command.action === 'mute_audio') { await toggleAudio(true); showToast('The host muted your microphone.'); }
            if (command.action === 'disable_video') { await toggleVideo(true); showToast('The host turned off your camera.'); }
            if (command.action === 'ask_unmute' && confirm('The host asked you to unmute. Unmute now?')) await toggleAudio(false);
            if (command.action === 'ask_video' && confirm('The host asked you to turn on your camera. Turn it on now?')) await toggleVideo(false);
            if (command.action === 'remove') { alert('The host removed you from this webinar.'); await exit(); }
        } catch (_) {}
    }
    function startPolling(){ pollComments(); chatTimer=setInterval(pollComments,2000); if(options.controlsUrl){pollControls();controlTimer=setInterval(pollControls,2000)} }
    function toggleChat(open) { chatPanel.classList.toggle('d-none', open === undefined ? !chatPanel.classList.contains('d-none') : !open); if(!chatPanel.classList.contains('d-none')){unread=0;updateChatBadge();chatMessages.scrollTop=chatMessages.scrollHeight} }
    function updateChatBadge(){const badge=document.getElementById('chatBadge');badge.textContent=unread;badge.classList.toggle('d-none',!unread)}

    document.getElementById('micButton').onclick=()=>toggleAudio().catch(e=>showError(e.message)); document.getElementById('cameraButton').onclick=()=>toggleVideo().catch(e=>showError(e.message)); document.getElementById('screenButton').onclick=()=>toggleScreen().catch(e=>showError(e.message));
    ['chatButton','chatToggleTop'].forEach(id=>document.getElementById(id).onclick=()=>toggleChat()); document.getElementById('chatClose').onclick=()=>toggleChat(false);
    document.getElementById('chatForm')?.addEventListener('submit', async event => { event.preventDefault(); const input=document.getElementById('chatInput'); const message=input.value.trim(); if(!message)return; input.disabled=true; try{renderComments(await requestJson(`${options.commentsUrl}?after=${lastCommentId}`,{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({comment:message})}));input.value=''}catch(e){showError(e.message)}finally{input.disabled=false;input.focus()} });

    async function leave(){if(leaving)return;leaving=true;clearInterval(chatTimer);clearInterval(controlTimer);if(sharingScreen)await stopScreenShare();[audioTrack,cameraTrack].filter(Boolean).forEach(track=>{track.stop();track.close()});if(client)await client.leave().catch(()=>{})}
    async function exit(){await leave();window.location.href=options.redirectUrl} document.getElementById('leaveButton').onclick=exit; document.getElementById('backButton').onclick=event=>{event.preventDefault();exit()}; window.addEventListener('pagehide',leave);
    connect().catch(error=>{console.error('Agora meeting error',error);showError(error.message||'Unable to join the webinar.')});
})();
</script>
