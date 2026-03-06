@extends('admin.layouts.app')

@section('title', 'Create Blog')

@section('content')
<div class="content-area">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="mb-4">
                <i class="fas fa-plus me-2"></i>
                Create Blog
            </h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.blogs.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Blogs
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.blogs.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="5">{{ old('description') }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tags (comma separated)</label>
                    <input type="text" name="tags" class="form-control" value="{{ old('tags') }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Media (image/video)</label>
                    <input type="file" name="media" class="form-control" accept="image/*,video/*">
                </div>

                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">
                        Active
                    </label>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Create Blog
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
