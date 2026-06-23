@extends('admin.layouts.app')

@section('title', 'Category Management')

@push('styles')
<style>
    .category-row {
        cursor: grab;
    }
    .category-row:active {
        cursor: grabbing;
    }
    .ghost-class {
        background-color: #f1f5f9 !important;
        opacity: 0.8;
        border: 2px dashed #cbd5e1;
    }
</style>
@endpush

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
                        <table class="table table-bordered table-striped align-middle">
                            <thead>
                                <tr>
                                    <th style="width: 80px;">ID</th>
                                    <th style="width: 100px;">Photo</th>
                                    <th>Category Name</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="sortable-categories">
                                @forelse($categories as $category)
                                <tr data-id="{{ $category->id }}" class="category-row">
                                    <td>{{ $category->id }}</td>
                                    <td>
                                        @if($category->photo)
                                            <img src="{{ asset('storage/' . $category->photo) }}" alt="{{ $category->name }}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                                        @else
                                            <span class="badge bg-secondary">No Photo</span>
                                        @endif
                                    </td>
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

                                            <!-- Edit Category Link -->
                                            <a href="{{ route('admin.category.edit', $category->id) }}" style="margin-left: 15px;" class="btn btn-sm btn-warning" title="Edit Category">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <!-- Move Category Button -->
                                            <button type="button" style="margin-left: 15px;" class="btn btn-sm btn-info text-white btn-move-category" data-id="{{ $category->id }}" data-name="{{ $category->name }}" title="Move Category Across Pages">
                                                <i class="fas fa-arrows-alt-v"></i> Move
                                            </button>

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
                    <div class="d-flex justify-content-center mt-3">
                        {{ $categories->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Move Category Modal -->
<div class="modal fade" id="moveCategoryModal" tabindex="-1" aria-labelledby="moveCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.category.moveToPage') }}" method="POST">
            @csrf
            <input type="hidden" name="category_id" id="move_category_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="moveCategoryModalLabel">Move Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Move category <strong id="move_category_name" class="text-danger"></strong> to another page:</p>
                    
                    <div class="mb-3">
                        <label for="target_page" class="form-label">Target Page</label>
                        <input type="number" class="form-control" name="target_page" id="target_page" min="1" max="{{ $categories->lastPage() }}" value="{{ $categories->currentPage() }}" required>
                        <div class="form-text">Enter page number (1 to {{ $categories->lastPage() }}).</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Page Position</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="position" id="position_top" value="top" checked>
                            <label class="form-check-label" for="position_top">
                                Top of page
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="position" id="position_bottom" value="bottom">
                            <label class="form-check-label" for="position_bottom">
                                Bottom of page
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save & Move</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<!-- SortableJS -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
$(document).ready(function() {
    var el = document.getElementById('sortable-categories');
    if (el) {
        new Sortable(el, {
            animation: 150,
            ghostClass: 'ghost-class',
            delay: 300,
            delayOnTouchOnly: false,
            onEnd: function (evt) {
                var order = [];
                $('#sortable-categories tr').each(function(index, element) {
                    var id = $(element).data('id');
                    if (id) {
                        order.push(id);
                    }
                });
                
                $.ajax({
                    url: "{{ route('admin.category.reorder') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        order: order,
                        page: "{{ $categories->currentPage() }}",
                        per_page: "{{ $categories->perPage() }}"
                    },
                    success: function(response) {
                        if (response.success) {
                            console.log('Order updated successfully');
                        } else {
                            alert('Failed to update category order');
                        }
                    },
                    error: function() {
                        alert('Error updating category order');
                    }
                });
            }
        });
    }

    // Modal show triggers
    $('.btn-move-category').on('click', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        
        $('#move_category_id').val(id);
        $('#move_category_name').text(name);
        
        var myModal = new bootstrap.Modal(document.getElementById('moveCategoryModal'));
        myModal.show();
    });
});
</script>
@endpush
