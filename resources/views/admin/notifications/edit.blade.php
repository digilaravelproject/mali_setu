@extends('admin.layouts.app')
@section('title', 'Edit Notification Action')
@section('page-title', 'Edit Notification Action')
@section('content')
<div class="container-fluid">
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <div class="card shadow">
        <div class="card-header"><strong>{{ $action->name }}</strong></div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.notifications.update', $action) }}">
                @include('admin.notifications._form')
            </form>
        </div>
    </div>
</div>
@endsection
