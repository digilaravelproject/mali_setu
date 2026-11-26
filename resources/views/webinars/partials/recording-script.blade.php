<script>
(() => {
    if (window.__maliSetuRecordingStarted) return;
    window.__maliSetuRecordingStarted = true;

    const button = document.getElementById('recordButton');
    if (!button || !navigator.mediaDevices?.getDisplayMedia || !window.MediaRecorder) {
        if (button) button.disabled = true;
        return;
    }

    const uploadUrl = @json($recordingUploadUrl);
    const csrf = @json(csrf_token());
    let recorder = null;
    let captureStream = null;
    let recordingId = null;
    let chunkIndex = 0;
    let uploadQueue = Promise.resolve();
    let stopping = false;

    const uuid = () => crypto.randomUUID
        ? crypto.randomUUID()
        : 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, c => {
            const r = Math.random() * 16 | 0;
            return (c === 'x' ? r : (r & 0x3 | 0x8)).toString(16);
        });

    const toast = message => {
        const box = document.getElementById('meetingToast');
        if (!box) return;
        box.textContent = message;
        box.classList.remove('d-none');
        clearTimeout(toast.timer);
        toast.timer = setTimeout(() => box.classList.add('d-none'), 5000);
    };

    const setButtonState = active => {
        button.classList.toggle('off', active);
        button.title = active ? 'Stop and save recording' : 'Start recording';
        button.innerHTML = active
            ? '<i class="fas fa-stop"></i>'
            : '<i class="fas fa-circle"></i>';
    };

    const uploadChunk = (blob, final) => {
        if (!blob?.size && !final) return Promise.resolve();
        const form = new FormData();
        form.append('recording_id', recordingId);
        form.append('chunk_index', String(chunkIndex++));
        form.append('is_final', final ? '1' : '0');
        form.append('chunk', blob, `chunk-${chunkIndex}.webm`);

        return fetch(uploadUrl, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {'Accept': 'application/json', 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest'},
            body: form,
        }).then(async response => {
            const body = await response.json().catch(() => ({}));
            if (!response.ok) throw new Error(body.message || 'A recording chunk could not be saved.');
            return body;
        });
    };

    const stopRecording = () => {
        if (!recorder || recorder.state === 'inactive' || stopping) return;
        stopping = true;
        button.disabled = true;
        recorder.stop();
    };

    const startRecording = async () => {
        try {
            captureStream = await navigator.mediaDevices.getDisplayMedia({
                video: {frameRate: 30},
                audio: true,
                preferCurrentTab: true,
            });
            const mimeType = [
                'video/webm;codecs=vp9,opus',
                'video/webm;codecs=vp8,opus',
                'video/webm',
            ].find(type => MediaRecorder.isTypeSupported(type));

            recordingId = uuid();
            chunkIndex = 0;
            stopping = false;
            recorder = new MediaRecorder(captureStream, mimeType ? {mimeType} : undefined);
            recorder.addEventListener('dataavailable', event => {
                if (!event.data?.size) return;
                const isFinal = recorder.state === 'inactive';
                uploadQueue = uploadQueue.then(() => uploadChunk(event.data, isFinal));
            });
            recorder.addEventListener('stop', async () => {
                try {
                    await uploadQueue;
                    toast('Webinar recording saved successfully.');
                } catch (error) {
                    toast(error.message || 'Recording upload failed. Please try again.');
                } finally {
                    captureStream?.getTracks().forEach(track => track.stop());
                    captureStream = null;
                    recorder = null;
                    stopping = false;
                    button.disabled = false;
                    setButtonState(false);
                }
            });
            captureStream.getVideoTracks()[0]?.addEventListener('ended', stopRecording, {once: true});
            recorder.start(10000);
            setButtonState(true);
            toast('Recording started. Share this browser tab and enable tab audio for the complete webinar.');
        } catch (error) {
            captureStream?.getTracks().forEach(track => track.stop());
            captureStream = null;
            toast(error.name === 'NotAllowedError' ? 'Recording permission was cancelled.' : (error.message || 'Unable to start recording.'));
        }
    };

    button.addEventListener('click', () => recorder?.state === 'recording' ? stopRecording() : startRecording());
    window.addEventListener('beforeunload', event => {
        if (recorder?.state !== 'recording') return;
        event.preventDefault();
        event.returnValue = 'Stop and save the webinar recording before leaving.';
    });
})();
</script>
