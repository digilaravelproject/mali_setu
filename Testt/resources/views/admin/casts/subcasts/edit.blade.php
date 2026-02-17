@extends('admin.layouts.app')

@section('title', 'Edit Sub-Cast')

@section('content')
<div class="content-area">
    <div class="row mb-4">
        <div class="col-md-12">
            <a href="{{ route('admin.casts.subcasts.index', $cast->id) }}" class="btn btn-secondary mb-3">
                <i class="fas fa-arrow-left me-1"></i> Back to Sub-Casts
            </a>
            <h2 class="mb-4">
                <i class="fas fa-edit me-2"></i>
                Edit Sub-Cast: {{ $subCast->name }}
            </h2>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Sub-Cast Information</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.casts.subcasts.update', [$cast->id, $subCast->id]) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="cast" class="form-label">Cast</label>
                            <input type="text" class="form-control" id="cast" 
                                   value="{{ $cast->name }}" readonly>
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label">Sub-Cast Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $subCast->name) }}" 
                                   placeholder="Enter sub-cast name" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="5" 
                                      placeholder="Enter sub-cast description...">{{ old('description', $subCast->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" 
                                       name="is_active" value="1" {{ old('is_active', $subCast->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Active
                                </label>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Update Sub-Cast
                            </button>
                            <a href="{{ route('admin.casts.subcasts.index', $cast->id) }}" class="btn btn-secondary">
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
