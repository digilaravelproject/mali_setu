@extends('admin.layouts.app')
@section('title', 'Push Notifications')
@section('page-title', 'Push Notifications')

@push('styles')
<style>
    .notification-page .card { border: 0; border-radius: 16px; }
    .notification-page .settings-card { height: calc(100% - 1.5rem); overflow: hidden; }
    .notification-page .settings-card .card-header { border: 0; padding: 1.15rem 1.25rem; background: linear-gradient(135deg, #fff, #f8f4f6); }
    .notification-page .section-icon { width: 42px; height: 42px; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; margin-right: .75rem; background: rgba(132, 20, 79, .1); color: #84144f; }
    .notification-page .status-pill { display: inline-flex; align-items: center; gap: .4rem; padding: .38rem .7rem; border-radius: 999px; font-size: .75rem; line-height: 1; font-weight: 800; white-space: nowrap; border: 1px solid transparent; }
    .notification-page .status-pill.is-on { color: #087443; background: #e8f8ef; border-color: #b9ead0; }
    .notification-page .status-pill.is-off { color: #6b7280; background: #f1f3f5; border-color: #dde1e6; }
    .notification-page .status-pill.is-push { color: #2355a5; background: #eaf2ff; border-color: #c5d9fc; }
    .notification-page .actions-panel { border-radius: 18px; overflow: hidden; }
    .notification-page .actions-panel > .card-header { padding: 1.25rem 1.4rem; background: #fff; border-bottom: 1px solid #edf0f3; }
    .notification-action-card { height: 100%; border: 1px solid #e8e9ed !important; border-radius: 14px !important; box-shadow: 0 6px 18px rgba(17, 24, 39, .05); transition: transform .18s ease, box-shadow .18s ease; }
    .notification-action-card:hover { transform: translateY(-2px); box-shadow: 0 12px 26px rgba(17, 24, 39, .09); }
    .notification-action-card .action-icon { width: 46px; height: 46px; flex: 0 0 46px; border-radius: 13px; display: flex; align-items: center; justify-content: center; color: #fff; background: linear-gradient(135deg, #aa1262, #741042); box-shadow: 0 5px 12px rgba(132, 20, 79, .2); }
    .notification-action-card .action-key { display: inline-block; color: #9b1859; background: #fff1f7; border-radius: 6px; padding: .16rem .4rem; font-size: .73rem; overflow-wrap: anywhere; }
    .notification-action-card .message-box { padding: .9rem; border-radius: 10px; background: #f8fafc; border: 1px solid #edf0f3; }
    .notification-action-card .message-title { font-size: .9rem; font-weight: 800; color: #111827; }
    .notification-action-card .message-body { font-size: .84rem; color: #667085; margin-top: .2rem; white-space: pre-line; }
    .notification-action-card .meta-row { font-size: .78rem; color: #667085; }
    .notification-action-card .btn { border-radius: 9px; font-weight: 700; }
    .notification-page .form-check-input:checked { background-color: #84144f; border-color: #84144f; }
    .notification-page .notification-pagination .pagination { margin-bottom: 0; }
    .notification-page .notification-pagination .page-link { color: #84144f; border-radius: 8px; margin: 0 .15rem; }
    .notification-page .notification-pagination .page-item.active .page-link { color: #fff; background: #84144f; border-color: #84144f; }
    @media (max-width: 575.98px) { .notification-page .actions-toolbar { align-items: stretch !important; flex-direction: column; gap: .75rem; } .notification-page .actions-toolbar .btn { width: 100%; } }
</style>
@endpush

@section('content')
<div class="container-fluid notification-page">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert"><i class="fas fa-circle-check me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
    @endif
    @if(session('error'))<div class="alert alert-danger"><i class="fas fa-circle-exclamation me-2"></i>{{ session('error') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div><h2 class="h4 fw-bold mb-1">Push notification centre</h2><p class="text-muted mb-0">Manage Firebase delivery, content and event-level notification rules.</p></div>
        <a href="{{ route('admin.notifications.send-form') }}" class="btn btn-success px-4"><i class="fas fa-paper-plane me-2"></i>Send notification</a>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card shadow-sm settings-card">
                <div class="card-header d-flex align-items-center"><span class="section-icon"><i class="fas fa-fire-flame-curved"></i></span><div><strong class="d-block">Firebase credentials</strong><small class="text-muted">Service-account connection</small></div></div>
                <div class="card-body p-4">
                    @if($firebase['configured'])
                        <div class="d-flex align-items-start gap-3 p-3 rounded-3 mb-3" style="background:#eaf8f0;border:1px solid #bce8cf;"><i class="fas fa-circle-check text-success mt-1"></i><div class="min-w-0"><strong class="d-block text-success">Connected</strong><span class="d-block text-break">{{ $firebase['project_id'] }}</span><small class="text-muted text-break">{{ $firebase['client_email'] }}</small></div></div>
                    @else
                        <div class="alert alert-warning"><i class="fas fa-triangle-exclamation me-2"></i>Firebase credentials are not configured.</div>
                    @endif
                    <form method="POST" action="{{ route('admin.notifications.firebase.update') }}" enctype="multipart/form-data">
                        @csrf
                        <label class="form-label fw-semibold" for="credentials">Replace service-account JSON</label>
                        <input type="file" class="form-control mb-2" id="credentials" name="credentials" accept="application/json,.json" required>
                        <small class="d-block text-muted mb-3"><i class="fas fa-lock me-1"></i>Stored privately. The private key is never displayed.</small>
                        <button class="btn btn-outline-primary"><i class="fas fa-upload me-2"></i>Upload credentials</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm settings-card">
                <div class="card-header d-flex align-items-center"><span class="section-icon"><i class="fas fa-sliders"></i></span><div><strong class="d-block">Delivery settings</strong><small class="text-muted">Master Firebase control</small></div></div>
                <div class="card-body p-4 d-flex flex-column">
                    <form method="POST" action="{{ route('admin.notifications.settings.update') }}" class="d-flex flex-column h-100">
                        @csrf
                        <div class="d-flex justify-content-between align-items-center p-3 rounded-3 mb-3" style="background:#f8fafc;border:1px solid #e8ebef;"><div class="pe-3"><strong class="d-block">Firebase push notifications</strong><small class="text-muted">Turns device delivery on or off for every action.</small></div><div class="form-check form-switch m-0"><input type="checkbox" class="form-check-input" role="switch" id="push_notifications_enabled" name="push_notifications_enabled" value="1" {{ $pushEnabled ? 'checked' : '' }} style="width:3rem;height:1.5rem;"></div></div>
                        <div class="d-flex align-items-center gap-3 p-3 rounded-3 mb-4" style="background:#fff7ec;border:1px solid #f6dfbd;"><span class="section-icon" style="background:#fff0d6;color:#c47400;"><i class="fas fa-mobile-screen"></i></span><div><span class="fs-4 fw-bold d-block">{{ number_format($usersWithTokens) }}</span><small class="text-muted">user(s) with a registered device token</small></div></div>
                        <button class="btn btn-primary mt-auto align-self-start px-4"><i class="fas fa-floppy-disk me-2"></i>Save setting</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4 actions-panel">
        <div class="card-header d-flex justify-content-between align-items-center actions-toolbar"><div><h3 class="h5 fw-bold mb-1">Notification actions</h3><small class="text-muted">{{ $actions->total() }} configurable events with separate notification and push controls.</small></div><a href="{{ route('admin.notifications.create') }}" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Add action</a></div>
        <div class="card-body p-3 p-lg-4">
            <div class="row g-3">
                @forelse($actions as $action)
                    <div class="col-xl-6">
                        <article class="card notification-action-card"><div class="card-body p-3 p-lg-4 d-flex flex-column">
                            <div class="d-flex align-items-start gap-3 mb-3">
                                <span class="action-icon"><i class="fas fa-bell"></i></span>
                                <div class="flex-grow-1 min-w-0"><div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
                                    <div><h4 class="h6 fw-bold mb-1">{{ $action->name }}</h4><code class="action-key">{{ $action->key }}</code></div>
                                    <div class="d-flex flex-wrap gap-2">
                                        <span class="status-pill {{ $action->is_enabled ? 'is-on' : 'is-off' }}"><i class="fas {{ $action->is_enabled ? 'fa-circle-check' : 'fa-circle-xmark' }}"></i>Notification {{ $action->is_enabled ? 'On' : 'Off' }}</span>
                                        <span class="status-pill {{ $action->push_enabled ? 'is-push' : 'is-off' }}"><i class="fas {{ $action->push_enabled ? 'fa-paper-plane' : 'fa-bell-slash' }}"></i>Push {{ $action->push_enabled ? 'On' : 'Off' }}</span>
                                    </div>
                                </div></div>
                            </div>
                            <div class="message-box mb-3"><div class="message-title">{{ $action->title_template }}</div><div class="message-body">{{ $action->body_template }}</div></div>
                            @if($action->action_url)<div class="meta-row text-break mb-2"><i class="fas fa-link me-2 text-primary"></i>{{ $action->action_url }}</div>@endif
                            @if(!empty($action->available_variables))<div class="meta-row mb-3"><i class="fas fa-code me-2 text-primary"></i>@foreach($action->available_variables as $variable)<code class="me-1">{{ '{{'.$variable.'}}' }}</code>@endforeach</div>@endif
                            <div class="d-flex flex-wrap gap-2 mt-auto pt-2 border-top">
                                <a class="btn btn-sm btn-success px-3" href="{{ route('admin.notifications.send-form', $action) }}"><i class="fas fa-paper-plane me-1"></i>Send</a>
                                <a class="btn btn-sm btn-outline-primary px-3" href="{{ route('admin.notifications.edit', $action) }}"><i class="fas fa-pen-to-square me-1"></i>Edit & preview</a>
                                @unless($action->isSystemAction())<form method="POST" action="{{ route('admin.notifications.destroy', $action) }}" class="ms-auto" onsubmit="return confirm('Delete this notification action?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash me-1"></i>Delete</button></form>@endunless
                            </div>
                        </div></article>
                    </div>
                @empty
                    <div class="col-12 text-center text-muted py-5"><i class="far fa-bell-slash fa-2x mb-3"></i><div>No notification actions found.</div></div>
                @endforelse
            </div>
        </div>
        @if($actions->hasPages())
            <div class="card-footer bg-white border-top px-3 px-lg-4 py-3">
                <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
                    <small class="text-muted">Showing <strong>{{ $actions->firstItem() }}</strong>–<strong>{{ $actions->lastItem() }}</strong> of <strong>{{ $actions->total() }}</strong> actions</small>
                    <div class="notification-pagination">{{ $actions->onEachSide(1)->links() }}</div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
