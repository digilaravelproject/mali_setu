@extends('admin.layouts.app')

@section('content')
<div class="container py-4">
    <h2>Add Hero</h2>
    <form action="{{ route('heroes.store') }}" method="POST" enctype="multipart/form-data" class="mt-3">
        @include('admin.heroes._form', ['submit' => 'Create'])
    </form>
</div>
@endsection
