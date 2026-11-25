<script crossorigin src="https://unpkg.com/@daily-co/daily-js"></script>
<script>
(() => {
    if (window.__maliSetuDailyStarted) return;
    window.__maliSetuDailyStarted = true;
    const options = {
        isHost: @json($isHost), tokenUrl: @json($tokenUrl), commentsUrl: @json($commentsUrl),
        controlsUrl: @json($controlsUrl ?? null), csrf: @json(csrf_token()),
        chatEnabled: @json((bool) $webinar->allow_qa),
        redirectUrl: @json($isHost ? route('admin.webinars.index') : route('webinars.show', $webinar)),
    };
    const grid = document.getElementById('videoGrid');
    const waiting = document.getElementById('waitingState');
    const status = document.getElementById('connectionStatus');
    const errorBox = document.getElementById('agoraError');
    const toastBox = document.getElementById('meetingToast');
    const chatPanel = document.getElementById('chatPanel');
    const chatMessages = document.getElementById('chatMessages');
    let call, credentials, leaving = false, audioMuted = false, videoMuted = false, sharing = false;
    let lastCommentId = 0, lastControlId = null, chatTimer, controlTimer, unread = 0;

    const requestJson = async (url, init = {}) => {
        const response = await fetch(url, {credentials:'same-origin',cache:'no-store',...init,headers:{Accept:'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':options.csrf,...(init.headers||{})}});
        const body = await response.json().catch(() => ({}));
        if (!response.ok) throw new Error(body.message || 'Request failed.');
        return body;
    };
    const showError = message => { errorBox.textContent=message; errorBox.classList.remove('d-none'); status.textContent='Connection failed'; };
    const showToast = message => { toastBox.textContent=message; toastBox.classList.remove('d-none'); clearTimeout(showToast.timer); showToast.timer=setTimeout(()=>toastBox.classList.add('d-none'),3500); };
    const participantCount = () => document.getElementById('participantCount').textContent = call ? Object.keys(call.participants()).length : 1;
    const updateButtons = () => {
        const mic=document.getElementById('micButton'), cam=document.getElementById('cameraButton'), screen=document.getElementById('screenButton');
        mic.disabled=cam.disabled=screen.disabled=false; mic.classList.toggle('off',audioMuted); cam.classList.toggle('off',videoMuted); screen.classList.toggle('off',sharing);
        mic.innerHTML=`<i class="fas fa-${audioMuted?'microphone-slash':'microphone'}"></i>`; cam.innerHTML=`<i class="fas fa-video${videoMuted?'-slash':''}"></i>`;
    };
    async function connect() {
        if (!window.DailyIframe) throw new Error('Daily.co video SDK could not be loaded.');
        credentials=await requestJson(options.tokenUrl);
        grid.style.display='block';
        call=window.DailyIframe.createFrame(grid,{showLeaveButton:false,showFullscreenButton:true,iframeStyle:{width:'100%',height:'100%',border:'0',borderRadius:'14px'}});
        call.on('joined-meeting',()=>{waiting.classList.add('d-none');status.textContent=options.isHost?'You are live':'Connected';status.style.color='#188038';updateButtons();participantCount();startPolling();});
        call.on('participant-joined',participantCount).on('participant-left',participantCount).on('participant-updated',participantCount);
        call.on('left-meeting',()=>{if(!leaving) exit();});
        call.on('error',event=>showError(event?.errorMsg||'Daily.co meeting error.'));
        await call.join({url:credentials.room_url,token:credentials.token,userName:credentials.display_name});
    }
    async function toggleAudio(force){audioMuted=force??!audioMuted;await call.setLocalAudio(!audioMuted);updateButtons()}
    async function toggleVideo(force){videoMuted=force??!videoMuted;await call.setLocalVideo(!videoMuted);updateButtons()}
    async function toggleScreen(){if(sharing){await call.stopScreenShare();sharing=false}else{await call.startScreenShare();sharing=true}updateButtons()}
    function renderComments(comments){if(!comments?.length)return;chatMessages.querySelector('.chat-empty')?.remove();comments.forEach(item=>{if(document.getElementById(`chat-${item.id}`))return;const row=document.createElement('div');row.id=`chat-${item.id}`;row.className='chat-item';row.innerHTML=`<div class="chat-meta"><strong></strong>${item.time}</div><div class="chat-text"></div>`;row.querySelector('strong').textContent=item.mine?'You':item.name;row.querySelector('.chat-text').textContent=item.message;chatMessages.appendChild(row);lastCommentId=Math.max(lastCommentId,item.id);if(chatPanel.classList.contains('d-none')&&!item.mine)unread++});chatMessages.scrollTop=chatMessages.scrollHeight;updateChatBadge()}
    async function pollComments(){if(!options.chatEnabled||leaving)return;try{renderComments(await requestJson(`${options.commentsUrl}?after=${lastCommentId}`))}catch(_){}}
    async function pollControls(){if(!options.controlsUrl||leaving)return;try{const command=await requestJson(`${options.controlsUrl}?seen=${encodeURIComponent(lastControlId||'')}`);if(!command?.id)return;lastControlId=command.id;if(command.action==='mute_audio'){await toggleAudio(true);showToast('The host muted your microphone.')}if(command.action==='disable_video'){await toggleVideo(true);showToast('The host turned off your camera.')}if(command.action==='ask_unmute'&&confirm('The host asked you to unmute. Unmute now?'))await toggleAudio(false);if(command.action==='ask_video'&&confirm('The host asked you to turn on your camera. Turn it on now?'))await toggleVideo(false);if(command.action==='remove'){alert('The host removed you from this webinar.');await exit()}}catch(_){}}
    function startPolling(){pollComments();chatTimer=setInterval(pollComments,2000);if(options.controlsUrl){pollControls();controlTimer=setInterval(pollControls,2000)}}
    function toggleChat(open){chatPanel.classList.toggle('d-none',open===undefined?!chatPanel.classList.contains('d-none'):!open);if(!chatPanel.classList.contains('d-none')){unread=0;updateChatBadge()}}
    function updateChatBadge(){const badge=document.getElementById('chatBadge');badge.textContent=unread;badge.classList.toggle('d-none',!unread)}
    document.getElementById('micButton').onclick=()=>toggleAudio().catch(e=>showError(e.message));document.getElementById('cameraButton').onclick=()=>toggleVideo().catch(e=>showError(e.message));document.getElementById('screenButton').onclick=()=>toggleScreen().catch(e=>showError(e.message));
    ['chatButton','chatToggleTop'].forEach(id=>document.getElementById(id).onclick=()=>toggleChat());document.getElementById('chatClose').onclick=()=>toggleChat(false);
    document.getElementById('chatForm')?.addEventListener('submit',async event=>{event.preventDefault();const input=document.getElementById('chatInput'),message=input.value.trim();if(!message)return;input.disabled=true;try{renderComments(await requestJson(`${options.commentsUrl}?after=${lastCommentId}`,{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({comment:message})}));input.value=''}catch(e){showError(e.message)}finally{input.disabled=false;input.focus()}});
    async function leave(){if(leaving)return;leaving=true;clearInterval(chatTimer);clearInterval(controlTimer);if(call){await call.leave().catch(()=>{});call.destroy()}}
    async function exit(){await leave();window.location.href=options.redirectUrl}document.getElementById('leaveButton').onclick=exit;document.getElementById('backButton').onclick=e=>{e.preventDefault();exit()};window.addEventListener('pagehide',leave);
    connect().catch(error=>showError(error.message||'Unable to join the Daily.co webinar.'));
})();
</script>
