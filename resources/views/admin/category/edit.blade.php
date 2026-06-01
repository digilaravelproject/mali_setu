@extends('admin.layouts.app')

@section('title', 'Category Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">

            <!-- Edit Category Form -->
            <div class="card mb-4">
                <div class="card-body">
                    <h4 class="mb-4">Edit Category</h4>
                    <form action="{{ route('admin.category.update', $category->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Category Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Category Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   placeholder="Enter category name"
                                   value="{{ old('name', $category->name) }}">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" id="description" rows="3" 
                                      class="form-control @error('description') is-invalid @enderror" 
                                      placeholder="Enter category description">{{ old('description', $category->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Photo -->
                        <div class="mb-3">
                            <label for="photo" class="form-label">Photo</label>
                            @if($category->photo)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $category->photo) }}" alt="Current Photo" style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px; border: 1px solid #ddd;">
                                    <div class="form-text small text-muted">Current Category Photo</div>
                                </div>
                            @endif
                            <input type="file" name="photo" id="photo" 
                                   class="form-control @error('photo') is-invalid @enderror" 
                                   accept="image/*">
                            <div class="form-text small text-muted">Upload a new photo to replace the current one (optional)</div>
                            @error('photo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('admin.category.index') }}" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-success" style="margin-left: 10px;">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
