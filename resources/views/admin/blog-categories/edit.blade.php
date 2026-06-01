@extends('admin.layouts.app')

@section('title', 'Edit Blog Category')

@section('content')
<div class="content-area">
    <!-- Back Link -->
    <div class="mb-4">
        <a href="{{ route('admin.blog-categories.index') }}" class="text-primary text-decoration-none fw-bold small">
            <i class="fas fa-arrow-left me-1"></i> Back to Listing
        </a>
    </div>

    <!-- Form Card -->
    <div class="card border-0 shadow-sm col-lg-8 mx-auto" style="border-radius:16px;">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-tags text-primary me-2"></i>Edit Blog Category</h5>
        </div>
        <div class="card-body p-4">
            
            @if($errors->any())
                <div class="alert alert-danger border-0 rounded-3">
                    <ul class="mb-0 small">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.blog-categories.update', $category->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Name Field -->
                <div class="mb-3">
                    <label for="name" class="form-label fw-bold text-secondary">Category Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="Enter category name" value="{{ old('name', $category->name) }}" required>
                    <div class="form-text small text-muted">Must be a unique name to identify the category.</div>
                </div>

                <!-- Description Field -->
                <div class="mb-4">
                    <label for="description" class="form-label fw-bold text-secondary">Description</label>
                    <textarea name="description" id="description" rows="4" class="form-control" placeholder="Provide a brief description...">{{ old('description', $category->description) }}</textarea>
                </div>

                <!-- Action Buttons -->
                <div class="row g-2">
                    <div class="col-6">
                        <button type="submit" class="btn btn-primary rounded-pill py-2.5 fw-bold w-100">
                            <i class="fas fa-save me-1"></i> Save Changes
                        </button>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('admin.blog-categories.index') }}" class="btn btn-outline-secondary rounded-pill py-2.5 w-100">
                            Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
