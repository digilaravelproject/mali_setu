@extends('admin.layouts.app')

@section('title', 'Edit Cast')

@section('content')
<div class="content-area">
    <div class="row mb-4">
        <div class="col-md-12">
            <a href="{{ route('admin.casts.index') }}" class="btn btn-secondary mb-3">
                <i class="fas fa-arrow-left me-1"></i> Back to Casts
            </a>
            <h2 class="mb-4">
                <i class="fas fa-edit me-2"></i>
                Edit Cast: {{ $cast->name }}
            </h2>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Cast Information</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.casts.update', $cast->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Cast Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $cast->name) }}" 
                                   placeholder="Enter cast name" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="5" 
                                      placeholder="Enter cast description...">{{ old('description', $cast->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" 
                                       name="is_active" value="1" {{ old('is_active', $cast->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Active
                                </label>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Update Cast
                            </button>
                            <a href="{{ route('admin.casts.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
