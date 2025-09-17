@extends('admin.layouts.app')

@section('title', 'Category Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Add Category Button -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">Categories</h4>
                <a href="{{ route('admin.category.addcategory') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> Add Category
                </a>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Category Name</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categories as $category)
                                <tr>
                                    <td>{{ $category->id }}</td>
                                    <td>
                                        <strong>{{ $category->name }}</strong>
                                        <br>
                                        <small title="{{ $category->description }}" class="text-muted">{{ Str::limit($category->description, 50) }}</small>
                                    </td>
                                    <td>
                                        @if($category->is_active == 1)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>{{ $category->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            @if($category->is_active == 0)
                                                <!-- Approve Category Form -->
                                                <form action="{{ route('admin.category.active', $category->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to active this category?')">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <!-- Reject Category Form -->
                                                <form action="{{ route('admin.category.inactive', $category->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Are you sure you want to inactive this category?')">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            <!-- Delete Category Form -->
                                            <form action="{{ route('admin.category.destroy', $category->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" style="margin-left: 15px;" class="ml-4 btn btn-sm btn-success" onclick="return confirm('Are you sure you want to delete this category?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">No category found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $categories->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
