@extends('admin.layouts.app')
@section('title', 'Notification Payload Format')
@section('page-title', 'Notification Payload Format')

@push('styles')
<style>
    .payload-page .payload-card { border: 0; border-radius: 16px; overflow: hidden; }
    .payload-page .payload-editor { min-height: 360px; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; font-size: .9rem; line-height: 1.55; tab-size: 2; }
    .payload-page .variable-chip { display: inline-block; margin: 0 .3rem .4rem 0; padding: .3rem .55rem; border-radius: 7px; color: #84144f; background: #fff1f7; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; font-size: .78rem; }
    .payload-page .example-box { margin: 0; padding: 1rem; border-radius: 10px; color: #dbeafe; background: #172033; font-size: .8rem; white-space: pre-wrap; }
</style>
@endpush

@section('content')
<div class="container-fluid payload-page">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert"><i class="fas fa-circle-check me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
    @endif

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="h4 fw-bold mb-1">{{ $action->name }}</h2>
            <p class="text-muted mb-0">Configure the Firebase data payload for <code>{{ $action->key }}</code>.</p>
        </div>
        <a href="{{ route('admin.notifications.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Back to notifications</a>
    </div>

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="card shadow-sm payload-card">
                <div class="card-header bg-white p-4">
                    <strong class="d-block">Payload JSON</strong>
                    <small class="text-muted">Enter a JSON object. These keys are added to the push data and override event data with the same names.</small>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.notifications.payload.update', $action) }}">
                        @csrf
                        @method('PUT')
                        <textarea id="payload_format" name="payload_format" class="form-control payload-editor @error('payload_format') is-invalid @enderror" spellcheck="false" placeholder='{"type":"business","id":"@{{business_id}}"}'>{{ old('payload_format', $action->payload_template ? json_encode($action->payload_template, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : '') }}</textarea>
                        @error('payload_format')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <small class="d-block text-muted mt-2">Leave the editor empty and save to restore the default generated payload.</small>
                        <div class="d-flex flex-wrap gap-2 mt-4">
                            <button class="btn btn-primary px-4"><i class="fas fa-floppy-disk me-2"></i>Save payload format</button>
                            <button type="button" id="formatJson" class="btn btn-outline-secondary"><i class="fas fa-align-left me-2"></i>Format JSON</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card shadow-sm payload-card mb-4">
                <div class="card-header bg-white p-3"><strong>Available variables</strong></div>
                <div class="card-body p-3">
                    @foreach(array_unique(array_merge($action->available_variables ?? [], ['user_name', 'user_email', 'notification_id', 'action_key', 'action_url', 'notification_title', 'notification_message'])) as $variable)
                        <span class="variable-chip">&#123;&#123; {{ $variable }} &#125;&#125;</span>
                    @endforeach
                    <small class="d-block text-muted mt-2">Variables are replaced separately for each recipient when the push is sent.</small>
                </div>
            </div>
            <div class="card shadow-sm payload-card">
                <div class="card-header bg-white p-3"><strong>Example</strong></div>
                <div class="card-body p-3">
                    <pre class="example-box">{
  "type": "{{ $action->key }}",
  "action": "details",
  "id": "@{{business_id}}",
  "screen": "business_details"
}</pre>
                    <small class="d-block text-muted mt-3">Firebase requires data values to be strings; arrays and objects are automatically JSON-encoded during delivery.</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(() => {
    const editor = document.getElementById('payload_format');
    document.getElementById('formatJson').addEventListener('click', () => {
        try {
            editor.value = JSON.stringify(JSON.parse(editor.value), null, 2);
            editor.classList.remove('is-invalid');
        } catch (error) {
            editor.classList.add('is-invalid');
            editor.focus();
        }
    });
})();
</script>
@endpush
