@extends('admin.layouts.app')

@section('content')
<div class="container py-4">
    <h2>Edit Hero</h2>
    <form action="{{ route('heroes.update', $hero) }}" method="POST" enctype="multipart/form-data" class="mt-3">
        @method('PUT')
        @include('admin.heroes._form', ['submit' => 'Update'])
    </form>
</div>
@endsection
