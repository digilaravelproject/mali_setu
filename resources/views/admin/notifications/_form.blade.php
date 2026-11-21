@csrf
@if($action->exists)
    @method('PUT')
@endif

<div class="row">
    <div class="col-lg-7">
        <div class="form-group">
            <label for="title_template">Notification title</label>
            <input class="form-control @error('title_template') is-invalid @enderror" id="title_template" name="title_template" maxlength="255" value="{{ old('title_template', $action->title_template) }}" required>
            @error('title_template')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <label for="body_template">Notification message</label>
            <textarea class="form-control @error('body_template') is-invalid @enderror" id="body_template" name="body_template" rows="4" maxlength="2000" required>{{ old('body_template', $action->body_template) }}</textarea>
            @error('body_template')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <label for="action_url">Mobile API endpoint / deep link</label>
            @php($selectedActionUrl = old('action_url', $action->action_url))
            <select class="form-select @error('action_url') is-invalid @enderror" id="action_url" name="action_url">
                <option value="">No in-app action</option>
                @if($selectedActionUrl && !collect($apiEndpoints)->pluck('uri')->contains($selectedActionUrl))
                    <option value="{{ $selectedActionUrl }}" selected>{{ $selectedActionUrl }} (saved custom deep link)</option>
                @endif
                @foreach($apiEndpoints as $endpoint)
                    <option value="{{ $endpoint['uri'] }}" @selected($selectedActionUrl === $endpoint['uri'])>{{ $endpoint['event'] }} — {{ $endpoint['methods'] }} — {{ $endpoint['uri'] }}</option>
                @endforeach
            </select>
            <small class="form-text text-muted">Each option shows the functionality/event name, HTTP method and mobile API endpoint. Parameters such as <code>{id}</code> may use template variables.</small>
            @error('action_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <label for="external_url">External URL</label>
            <input type="url" class="form-control @error('external_url') is-invalid @enderror" id="external_url" name="external_url" value="{{ old('external_url', $action->external_url) }}" placeholder="https://www.youtube.com/watch?v=...">
            <small class="form-text text-muted">Optional website or social link, such as YouTube or Facebook. It is sent separately as <code>external_url</code>.</small>
            @error('external_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <label for="notificationImage">Notification image <span class="text-muted">(optional)</span></label>
            <input type="file" class="form-control @error('image') is-invalid @enderror" id="notificationImage" name="image" accept="image/jpeg,image/png,image/gif,image/webp">
            <small class="form-text text-muted">JPG, PNG, GIF or WebP, up to 5 MB. The push is sent without an image when none is selected.</small>
            @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <input type="hidden" name="remove_image" id="removeNotificationImage" value="0">
            <div id="notificationImagePreview" class="notification-image-preview mt-3 {{ $action->image_path ? '' : 'd-none' }}">
                <img id="notificationImagePreviewImg" src="{{ $action->image_url ?? '' }}" alt="Notification image preview">
                <button type="button" id="removeNotificationImageButton" class="btn btn-danger btn-sm rounded-circle" aria-label="Remove notification image" title="Remove image"><i class="fas fa-times"></i></button>
            </div>
        </div>
        <div class="form-group">
            <label for="available_variables_text">Template variables</label>
            <input class="form-control" id="available_variables_text" name="available_variables_text" value="{{ old('available_variables_text', implode(', ', $action->available_variables ?? [])) }}" placeholder="user_name, amount, status">
            <small class="form-text text-muted">Comma separated. Use in content as <code>@{{user_name}}</code>. Missing values render as blank.</small>
        </div>
        <div class="form-check form-switch mb-3">
            <input type="checkbox" class="form-check-input" role="switch" id="is_enabled" name="is_enabled" value="1" {{ old('is_enabled', $action->exists ? $action->is_enabled : true) ? 'checked' : '' }}>
            <label class="form-check-label fw-semibold" for="is_enabled">Enable this notification action</label>
        </div>
        <div class="form-check form-switch mb-4">
            <input type="checkbox" class="form-check-input" role="switch" id="push_enabled" name="push_enabled" value="1" {{ old('push_enabled', $action->push_enabled) ? 'checked' : '' }}>
            <label class="form-check-label fw-semibold" for="push_enabled">Send Firebase push when this action occurs</label>
        </div>
        <button class="btn btn-primary"><i class="fas fa-save"></i> Save action</button>
        <a href="{{ route('admin.notifications.index') }}" class="btn btn-secondary">Cancel</a>
    </div>

    <div class="col-lg-5 mt-4 mt-lg-0">
        <div class="card shadow notification-phone-card">
            <div class="card-header bg-white"><i class="fas fa-mobile-screen-button me-2"></i><strong>Mobile preview</strong></div>
            <div class="card-body notification-phone">
                <div class="phone-status"><strong>9:41</strong><span><i class="fas fa-signal"></i>&nbsp; <i class="fas fa-wifi"></i>&nbsp; <i class="fas fa-battery-full"></i></span></div>
                <div class="phone-date">Monday, 27 October</div>
                <div class="notification-preview">
                    <div class="d-flex align-items-center mb-2">
                        <img src="{{ asset('landing_page_logo.jpeg') }}" class="notification-logo" alt="Mali Setu logo">
                        <strong class="ms-2">Mali Setu</strong>
                        <small class="text-muted ms-auto">now</small>
                    </div>
                    <div id="previewTitle" class="font-weight-bold text-break"></div>
                    <div id="previewBody" class="small text-muted text-break"></div>
                </div>
                <small class="preview-note">Preview only — appearance varies by Android/iOS and user settings.</small>
            </div>
        </div>
        <div class="alert alert-info mt-3">
            Variables are replaced using event data. In this preview, variables are shown as sample values.
        </div>
    </div>
</div>

@once
@push('styles')
<style>
    .notification-phone-card { border: 0; border-radius: 18px; overflow: hidden; }
    .notification-phone { min-height: 430px; padding: 1.2rem; color: #fff; background: radial-gradient(circle at top right, rgba(170,18,98,.45), transparent 45%), linear-gradient(150deg,#273044,#101624); }
    .phone-status { display: flex; justify-content: space-between; font-size: .78rem; opacity: .9; }
    .phone-date { margin: 2.1rem 0 1rem; text-align: center; font-size: .85rem; color: rgba(255,255,255,.7); }
    .notification-preview { color: #172033; padding: 1rem; border-radius: 16px; border: 1px solid rgba(255,255,255,.65); background: rgba(255,255,255,.95); box-shadow: 0 14px 34px rgba(0,0,0,.24); }
    .notification-logo { width: 38px; height: 38px; border-radius: 10px; object-fit: cover; background: #fff; }
    .notification-image-preview { position: relative; width: min(100%, 360px); }
    .notification-image-preview img { display: block; width: 100%; max-height: 210px; border-radius: 12px; border: 1px solid #e4e7ec; object-fit: cover; }
    .notification-image-preview button { position: absolute; top: .5rem; right: .5rem; width: 30px; height: 30px; padding: 0; box-shadow: 0 2px 8px rgba(0,0,0,.3); }
    #previewTitle { font-weight: 800; font-size: .95rem; margin-bottom: .2rem; }
    #previewBody { line-height: 1.4; }
    .preview-note { display: block; margin-top: 1rem; text-align: center; color: rgba(255,255,255,.64); }
    .form-check-input:checked { background-color: #84144f; border-color: #84144f; }
</style>
@endpush
@endonce

@push('scripts')
<script>
(() => {
    const title = document.getElementById('title_template');
    const body = document.getElementById('body_template');
    const imageInput = document.getElementById('notificationImage');
    const imagePreview = document.getElementById('notificationImagePreview');
    const imagePreviewImg = document.getElementById('notificationImagePreviewImg');
    const removeImage = document.getElementById('removeNotificationImage');
    let previewObjectUrl = null;
    const render = value => (value || '').replace(/\{\{\s*([\w.-]+)\s*\}\}/g, (_, key) => ({
        user_name: 'Darshan', amount: '499', status: 'approved', sender_name: 'Amit',
        requester_name: 'Priya', applicant_name: 'Rahul', volunteer_name: 'Neha', purpose: 'subscription'
    })[key] || `[${key}]`);
    const refresh = () => {
        document.getElementById('previewTitle').textContent = render(title.value) || 'Notification title';
        document.getElementById('previewBody').textContent = render(body.value) || 'Your notification message appears here.';
    };
    title.addEventListener('input', refresh);
    body.addEventListener('input', refresh);
    imageInput.addEventListener('change', () => {
        if (!imageInput.files.length) return;
        if (previewObjectUrl) URL.revokeObjectURL(previewObjectUrl);
        previewObjectUrl = URL.createObjectURL(imageInput.files[0]);
        imagePreviewImg.src = previewObjectUrl;
        imagePreview.classList.remove('d-none');
        removeImage.value = '0';
    });
    document.getElementById('removeNotificationImageButton').addEventListener('click', () => {
        if (previewObjectUrl) URL.revokeObjectURL(previewObjectUrl);
        previewObjectUrl = null;
        imageInput.value = '';
        imagePreviewImg.removeAttribute('src');
        imagePreview.classList.add('d-none');
        removeImage.value = '1';
    });
    refresh();
})();
</script>
@endpush
