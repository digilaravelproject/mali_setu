@extends('admin.layouts.app')

@section('title', 'Edit Business')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Business</h1>
        <div class="d-sm-flex">
            <a href="{{ route('admin.businesses.show', $business->id) }}" class="btn btn-secondary btn-sm shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Business
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Business Information</h6>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.businesses.update', $business->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="business_name" class="form-label">Business Name</label>
                                    <input type="text" class="form-control" id="business_name" name="business_name" value="{{ old('business_name', $business->business_name) }}" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="business_type" class="form-label">Business Type</label>
                                    <select class="form-control" id="business_type" name="business_type" required>
                                        <option value="" disabled {{ old('business_type', $business->business_type) ? '' : 'selected' }}>Select type</option>
                                        @foreach($businessTypes as $type)
                                            <option value="{{ $type }}" {{ old('business_type', $business->business_type) === $type ? 'selected' : '' }}>{{ $type }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="category_id" class="form-label">Category</label>
                                    <select class="form-control" id="category_id" name="category_id" required>
                                        <option value="" disabled {{ old('category_id', $business->category_id) ? '' : 'selected' }}>Select category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id', $business->category_id) == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="contact_phone" class="form-label">Phone</label>
                                    <input type="text" class="form-control" id="contact_phone" name="contact_phone" value="{{ old('contact_phone', $business->contact_phone) }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="contact_email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="contact_email" name="contact_email" value="{{ old('contact_email', $business->contact_email) }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="website" class="form-label">Website</label>
                                    <input type="text" class="form-control" id="website" name="website" value="{{ old('website', $business->website) }}">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4">{{ old('description', $business->description) }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
