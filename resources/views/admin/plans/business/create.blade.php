@extends('admin.layouts.app')

@section('title', 'Add Business Plan')
@section('page-title', 'Add Business Plan')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Create Business Plan</h3>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.plans.business.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Company Type</label>
                            <input type="text" name="company_type" class="form-control" value="{{ old('company_type') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Duration (years)</label>
                            <input type="number" name="duration_years" class="form-control" value="{{ old('duration_years', 1) }}" min="1" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Price (₹)</label>
                            <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price') }}" required>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="hidden" name="active" value="0">
                            <input type="checkbox" name="active" value="1" class="form-check-input" id="active" {{ old('active', 1) ? 'checked' : '' }}>
                            <label class="form-check-label" for="active">Active</label>
                        </div>
                        <button class="btn btn-primary">Create</button>
                        <a href="{{ route('admin.plans.business.index') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
