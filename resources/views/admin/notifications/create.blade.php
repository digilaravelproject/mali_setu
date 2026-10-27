@extends('admin.layouts.app')
@section('title', 'Create Notification Action')
@section('page-title', 'Create Notification Action')
@section('content')
<div class="container-fluid">
    <div class="card shadow">
        <div class="card-header"><strong>New notification action</strong></div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.notifications.store') }}">
                @include('admin.notifications._form')
            </form>
        </div>
    </div>
</div>
@endsection
