@csrf
@if($action->exists)
    @method('PUT')
@endif

<div class="row">
    <div class="col-lg-7">
        <div class="form-group">
            <label for="name">Action name</label>
            <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $action->name) }}" required>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <label for="key">Action key</label>
            <input class="form-control @error('key') is-invalid @enderror" id="key" name="key" value="{{ old('key', $action->key) }}" pattern="[a-z0-9_]+" {{ $action->exists ? 'readonly' : '' }} required>
            <small class="form-text text-muted">Lowercase letters, numbers and underscores. App code can trigger this key.</small>
            @error('key')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <label for="description">Admin description</label>
            <textarea class="form-control" id="description" name="description" rows="2">{{ old('description', $action->description) }}</textarea>
        </div>
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
            <label for="action_url">Mobile deep link / action URL</label>
            <input class="form-control" id="action_url" name="action_url" value="{{ old('action_url', $action->action_url) }}" placeholder="/dashboard/matrimony">
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
    refresh();
})();
</script>
@endpush
