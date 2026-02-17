@extends('admin.layouts.app')

@section('content')
<div class="container py-4">
    <h2>View Hero</h2>

    <div class="card mt-3">
        <div class="card-body">
            <h4 class="card-title mb-3">{{ $hero->title }}</h4>
            <img src="{{ asset('storage/'.$hero->image_path) }}" alt="Hero" class="img-fluid" style="max-height:400px">
            <div class="mt-3 text-muted">Created: {{ $hero->created_at->format('d M Y H:i') }}</div>
        </div>
    </div>

    <div class="mt-3 d-flex gap-2">
        <a class="btn btn-primary" href="{{ route('heroes.edit', $hero) }}">Edit</a>
        <form action="{{ route('heroes.destroy', $hero) }}" method="POST" onsubmit="return confirm('Delete this hero?');">
            @csrf @method('DELETE')
            <button class="btn btn-danger" type="submit">Delete</button>
        </form>
        <a class="btn btn-secondary" href="{{ route('heroes.index') }}">Back</a>
    </div>
</div>
@endsection
