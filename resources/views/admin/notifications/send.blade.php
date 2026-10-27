@extends('admin.layouts.app')
@section('title', 'Send Push Notification')
@section('page-title', 'Send Push Notification')
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center"><div class="col-lg-8">
        <div class="card shadow">
            <div class="card-header"><strong><i class="fas fa-paper-plane"></i> Send a configured notification</strong></div>
            <div class="card-body">
                @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
                <form method="POST" action="{{ route('admin.notifications.send') }}" onsubmit="return confirm('Send this notification now?')">
                    @csrf
                    <div class="form-group">
                        <label>Notification action</label>
                        <select class="form-control" name="notification_action_id" required>
                            <option value="">Select an action</option>
                            @foreach($actions as $action)
                                <option value="{{ $action->id }}" {{ (string) old('notification_action_id', $selectedAction?->id) === (string) $action->id ? 'selected' : '' }}>{{ $action->name }}{{ $action->push_enabled ? '' : ' (push disabled)' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Audience</label>
                        <select class="form-control" id="audience" name="audience" required>
                            <option value="user" {{ old('audience') === 'user' ? 'selected' : '' }}>One user</option>
                            <option value="all" {{ old('audience') === 'all' ? 'selected' : '' }}>All users with a device token ({{ $users->count() }})</option>
                        </select>
                    </div>
                    <div class="form-group" id="userGroup">
                        <label>User</label>
                        <select class="form-control" name="user_id">
                            <option value="">Select a user</option>
                            @foreach($users as $user)<option value="{{ $user->id }}" {{ (string) old('user_id') === (string) $user->id ? 'selected' : '' }}>{{ $user->name }} — {{ $user->email ?: $user->phone }}</option>@endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Optional event data (JSON)</label>
                        <textarea class="form-control" name="data" rows="5" placeholder='{"amount":"499","status":"approved"}'>{{ old('data') }}</textarea>
                        <small class="form-text text-muted">Values replace template variables and are delivered to the mobile app.</small>
                    </div>
                    <div class="alert alert-warning"><strong>Note:</strong> The action must have Push enabled to reach devices. An in-app notification is also created.</div>
                    <button class="btn btn-success"><i class="fas fa-paper-plane"></i> Send now</button>
                    <a href="{{ route('admin.notifications.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div></div>
</div>
@endsection
@push('scripts')
<script>
(() => {
    const audience = document.getElementById('audience');
    const group = document.getElementById('userGroup');
    const refresh = () => group.style.display = audience.value === 'user' ? '' : 'none';
    audience.addEventListener('change', refresh); refresh();
})();
</script>
@endpush
