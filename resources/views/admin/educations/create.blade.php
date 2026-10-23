@extends('admin.layouts.app')

@section('title', 'Add Education')

@section('content')
<div class="content-area">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="mb-4"><i class="fas fa-plus me-2"></i> Add Education</h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.educations.index') }}" class="btn btn-secondary">Back to list</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.educations.store') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Highest Qualification</label>
                    <input type="text" name="highest_qualification" class="form-control" value="{{ old('highest_qualification') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">College</label>
                    <input type="text" name="college" class="form-control" value="{{ old('college') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">University</label>
                    <input type="text" name="university" class="form-control" value="{{ old('university') }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Specialization</label>
                    <input type="text" name="specialization" class="form-control" value="{{ old('specialization') }}">
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Passing Year</label>
                        <input type="number" name="passing_year" class="form-control" value="{{ old('passing_year') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Percentage / Grade</label>
                        <input type="text" name="percentage" class="form-control" value="{{ old('percentage') }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description (optional)</label>
                    <textarea name="description" class="form-control" rows="4">{{ old('description') }}</textarea>
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" name="is_active" class="form-check-input" id="isActive" checked>
                    <label class="form-check-label" for="isActive">Active</label>
                </div>

                <button type="submit" class="btn btn-primary">Create</button>
            </form>
        </div>
    </div>
</div>
@endsection
