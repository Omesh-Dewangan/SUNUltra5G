@extends('layouts.dashboard')

@section('title', 'Category Master')

@section('content')
<div class="content-header">
    <div class="w-100">
        <span class="breadcrumb-item">Master Data / Inventory</span>
        <div class="d-flex align-items-center">
            <a href="{{ route('dashboard') }}" class="text-muted back-btn-minimal me-2" title="Back to Dashboard">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="page-title">Category Master</h1>
        </div>
        <p class="page-subtitle ms-md-4 ps-md-2">Group your products logically into categories.</p>
    </div>
    <div class="col-12 col-md-6 text-md-end">
        <button onclick="$('#import-category-modal').modal('show')" class="btn btn-outline-primary px-4 fw-bold me-2">
            <i class="fas fa-file-import me-2"></i>Import CSV
        </button>
        <button onclick="$('#add-category-form')[0].reset(); $('#add-category-modal').modal('show')" class="btn btn-primary px-4 fw-bold">
            <i class="fas fa-plus me-2"></i>Add Category
        </button>
    </div>
</div>

<div class="data-card border-0 shadow-sm">
    <div class="card-header bg-white py-3 border-bottom-0 d-flex justify-content-between align-items-center">
        <h3 class="h6 mb-0 text-muted fw-bold">Categories List</h3>
        <div class="input-group" style="max-width: 300px;">
            <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-search"></i></span>
            <input type="text" class="form-control border-start-0 bg-light live-search-input" data-table="categories-table" placeholder="Search categories...">
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="categories-table">
            <thead class="bg-light">
                <tr class="text-muted small text-uppercase">
                    <th class="ps-4 py-3 border-0" style="width: 80px;">#</th>
                    <th class="py-3 border-0">Category Name</th>
                    <th class="py-3 border-0">Slug</th>
                    <th class="py-3 border-0">Description</th>
                    <th class="pe-4 py-3 border-0 text-end">Actions</th>
                </tr>
            </thead>
            <tbody class="small">
                @foreach($categories as $category)
                <tr>
                    <td class="ps-4 py-3 text-muted">#{{ $loop->iteration }}</td>
                    <td class="py-3 fw-bold text-dark">{{ $category->name }}</td>
                    <td class="py-3 text-muted">{{ $category->slug }}</td>
                    <td class="py-3 text-muted">{{ $category->description ?? '-' }}</td>
                    <td class="pe-4 py-3 text-end">
                        <div class="d-flex justify-content-end gap-1">
                            <button onclick="editCategory({{ $category->id }}, '{{ addslashes($category->name) }}', '{{ addslashes($category->description) }}')" class="btn btn-sm btn-primary-light" title="Edit">
                                <i class="fas fa-pen"></i>
                            </button>
                            <button onclick="deleteCategory({{ $category->id }})" class="btn btn-sm btn-danger-light" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Add Category Modal -->
<div class="modal fade" id="add-category-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold">Add New Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="add-category-form">
                @csrf
                <div class="modal-body p-4">
                    <div class="form-group mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Category Name</label>
                        <input type="text" name="name" class="form-control px-3 py-2" placeholder="e.g., LED Lights" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label small fw-bold text-muted text-uppercase">Description (Optional)</label>
                        <textarea name="description" class="form-control px-3 py-2" rows="3" placeholder="Brief details..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top-0 p-4 pt-0">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="save-cat-btn" class="btn btn-primary px-4 fw-bold">Save Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="edit-category-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold">Edit Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="edit-category-form">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="edit_category_id">
                <div class="modal-body p-4">
                    <div class="form-group mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Category Name</label>
                        <input type="text" name="name" id="edit_category_name" class="form-control px-3 py-2" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label small fw-bold text-muted text-uppercase">Description (Optional)</label>
                        <textarea name="description" id="edit_category_desc" class="form-control px-3 py-2" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top-0 p-4 pt-0">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="update-category-btn" class="btn btn-primary px-4 fw-bold">Update Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Import Category Modal -->
<div class="modal fade" id="import-category-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold">Import Categories from CSV</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="import-category-form" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-info small border-0 shadow-none mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold"><i class="fas fa-info-circle me-1"></i> CSV Format:</span>
                            <a href="{{ route('master.categories.sample') }}" class="btn btn-xs btn-primary py-0 px-2 fw-bold" style="font-size: 10px;">
                                <i class="fas fa-download me-1"></i> Download Sample
                            </a>
                        </div>
                        Name, Description<br>
                        <span class="text-muted opacity-75">Note: The first row will be skipped (header).</span>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Select CSV File</label>
                        <input type="file" name="csv_file" class="form-control px-3 py-2" accept=".csv" required>
                    </div>
                </div>
                <div class="modal-footer border-top-0 p-4 pt-0">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="import-cat-btn" class="btn btn-primary px-4 fw-bold">Upload & Import</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('#add-category-form').on('submit', function(e) {
        e.preventDefault();
        const btn = $('#save-cat-btn');
        const oldText = btn.text();
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Saving...');

        $.ajax({
            url: "{{ route('master.categories.store') }}",
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Saved!',
                    text: response.message,
                    showConfirmButton: false,
                    timer: 1500
                });
                setTimeout(() => location.reload(), 1500);
            },
            error: function(xhr) {
                const msg = xhr.responseJSON?.message || 'Error saving category.';
                Swal.fire({ icon: 'error', title: 'Oops...', text: msg });
                btn.prop('disabled', false).text(oldText);
            }
        });
    });

    $('#edit-category-form').on('submit', function(e) {
        e.preventDefault();
        const btn = $('#update-category-btn');
        const oldText = btn.text();
        const id = $('#edit_category_id').val();
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Updating...');

        $.ajax({
            url: `/master/categories/${id}`,
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Updated!',
                    text: response.message,
                    showConfirmButton: false,
                    timer: 1500
                });
                setTimeout(() => location.reload(), 1500);
            },
            error: function(xhr) {
                const msg = xhr.responseJSON?.message || 'Error updating category.';
                Swal.fire({ icon: 'error', title: 'Oops...', text: msg });
                btn.prop('disabled', false).text(oldText);
            }
        });
    });

    $('#import-category-form').on('submit', function(e) {
        e.preventDefault();
        const btn = $('#import-cat-btn');
        const oldText = btn.text();
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Processing...');

        const formData = new FormData(this);

        $.ajax({
            url: "{{ route('master.categories.import') }}",
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Imported!',
                    text: response.message,
                    showConfirmButton: true
                }).then(() => location.reload());
            },
            error: function(xhr) {
                const msg = xhr.responseJSON?.message || 'Error importing categories.';
                Swal.fire({ icon: 'error', title: 'Import Failed', text: msg });
                btn.prop('disabled', false).text(oldText);
            }
        });
    });
});

function editCategory(id, name, desc) {
    $('#edit_category_id').val(id);
    $('#edit_category_name').val(name);
    $('#edit_category_desc').val(desc);
    $('#edit-category-modal').modal('show');
}

function deleteCategory(id) {
    Swal.fire({
        title: 'Delete Category?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/master/categories/${id}`,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    _method: 'DELETE'
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 1500
                    });
                    setTimeout(() => location.reload(), 1500);
                },
                error: function(xhr) {
                    const msg = xhr.responseJSON?.message || 'Error deleting category.';
                    Swal.fire({ icon: 'error', title: 'Oops...', text: msg });
                }
            });
        }
    });
}
</script>
@endsection
