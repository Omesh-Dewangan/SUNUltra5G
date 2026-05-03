@extends('layouts.dashboard')

@section('title', 'Product Master')

@section('content')
<style>
    @media (max-width: 768px) {
        .content-header { flex-direction: column; align-items: flex-start !important; gap: 15px; }
        .content-header .col-md-6 { width: 100%; text-align: left !important; }
        .content-header .d-flex { width: 100%; justify-content: flex-start !important; }
        .content-header .btn { flex: 1; font-size: 11px; padding: 6px 10px !important; }
        .page-title { font-size: 18px !important; }
        .page-subtitle { font-size: 11px !important; margin-left: 0 !important; padding-left: 0 !important; }
        .card-header { flex-direction: column; align-items: flex-start !important; gap: 12px; }
        .card-header .input-group { max-width: 100% !important; }
    }
</style>
<div class="row align-items-center mb-0 page-title-row">
    <div class="col-12 col-md-6">
        <span class="breadcrumb-item">Master Data / Catalog</span>
        <div class="d-flex align-items-center">
            <a href="{{ route('dashboard') }}" class="text-muted back-btn-minimal me-2" title="Back to Dashboard">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="page-title" style="font-size: 22px;">Product Master</h1>
        </div>
        <p class="page-subtitle ms-md-4 ps-md-2 mb-0">Manage your product catalog and specifications.</p>
    </div>
    <div class="col-12 col-md-6 text-md-end mt-2 mt-md-0">
        <div class="d-flex flex-wrap justify-content-md-end gap-2">
            <button onclick="$('#import-product-modal').modal('show')" class="btn btn-outline-primary px-3 py-2 fw-bold">
                <i class="fas fa-file-import me-2"></i>Import CSV
            </button>
            <a href="{{ route('master.products.export') }}" class="btn btn-success px-3 py-2 fw-bold">
                <i class="fas fa-file-csv me-2"></i>Export CSV
            </a>
            <button onclick="$('#add-product-form')[0].reset(); $('#specs-container').empty(); $('.add-select2').val(null).trigger('change'); $('#add-product-modal').modal('show')" class="btn btn-primary px-3 py-2 fw-bold">
                <i class="fas fa-plus me-2"></i>Add Product
            </button>
        </div>
    </div>
</div>

<div class="data-card border-0 shadow-sm">
    <div class="card-header bg-white py-3 border-bottom-0 d-flex justify-content-between align-items-center">
        <h3 class="h6 mb-0 text-muted fw-bold">Product Catalog</h3>
        <div class="input-group" style="max-width: 300px;">
            <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-search"></i></span>
            <input type="text" class="form-control border-start-0 bg-light live-search-input" data-table="products-table" placeholder="Search products, SKU, category...">
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="products-table">
            <thead class="bg-light">
                <tr class="text-muted small text-uppercase">
                    <th class="ps-4 py-3 border-0">SKU Code</th>
                    <th class="py-3 border-0">Name & Category</th>
                    <th class="py-3 border-0 text-center">Unit</th>
                    <th class="py-3 border-0 text-center">Wattage</th>
                    <th class="py-3 border-0">Specifications</th>
                    <th class="pe-4 py-3 border-0 text-end">Actions</th>
                </tr>
            </thead>
            <tbody class="small">
                @foreach($products as $product)
                <tr>
                    <td class="ps-4 py-3 fw-bold text-primary">{{ $product->code }}</td>
                    <td class="py-3">
                        <div class="fw-bold text-dark">{{ $product->name }}</div>
                        <div class="extra-small text-muted">{{ $product->category->name ?? '-' }}</div>
                    </td>
                    <td class="py-3 text-center text-muted">{{ $product->unit }}</td>
                    <td class="py-3 text-center fw-bold text-dark">{{ $product->wattage ?? '-' }}</td>
                    <td class="py-3">
                        @if($product->specifications)
                            <div class="d-flex flex-wrap gap-1">
                                @foreach($product->specifications as $key => $val)
                                    <span class="badge text-bg-light border small text-dark fw-normal" style="font-size: 10px;">
                                        <span class="text-muted">{{ $key }}:</span> {{ $val }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td class="pe-4 py-3 text-end">
                        <div class="d-flex justify-content-end gap-1">
                            <a href="{{ route('master.products.stock', encrypt($product->id)) }}" class="btn btn-sm btn-success-light" title="Manage Stock">
                                <i class="fas fa-boxes"></i>
                            </a>
                            <button onclick='editProduct(@json($product), "{{ encrypt($product->id) }}")' class="btn btn-sm btn-primary-light" title="Edit">
                                <i class="fas fa-pen"></i>
                            </button>
                            <button onclick="deleteProduct('{{ encrypt($product->id) }}')" class="btn btn-sm btn-danger-light" title="Delete">
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

<!-- Add Product Modal -->
<div class="modal fade" id="add-product-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="add-product-form">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label font-bold small text-muted">Product Code (SKU)</label>
                                <input type="text" name="code" class="form-control" placeholder="e.g., SUHML120" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label font-bold small text-muted">Product Name</label>
                                <input type="text" name="name" class="form-control" placeholder="e.g., LED High Mast 120W" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label font-bold small text-muted">Category</label>
                                <select name="category_id" class="form-select select2 add-select2" required style="width: 100%;">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label font-bold small text-muted">Unit of Measurement</label>
                                <select name="unit" class="form-select select2 add-select2" required style="width: 100%;">
                                    <option value="">Select Unit</option>
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->name }}">{{ $unit->name }}</option>
                                    @endforeach
                                    <option value="Piece">Piece (Default)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label font-bold small text-muted">Wattage (Optional)</label>
                                <input type="text" name="wattage" class="form-control" placeholder="e.g., 120W">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label font-bold small text-muted">Selling Price (₹)</label>
                                <input type="number" name="selling_price" step="0.01" class="form-control" placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label font-bold small text-muted">Low Stock Alert</label>
                                <input type="number" name="low_stock_threshold" class="form-control" value="10" required>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <label class="font-bold small text-muted mb-0">Custom Specifications</label>
                            <button type="button" id="add-spec-btn" class="btn btn-success-light btn-sm">
                                <i class="fas fa-plus"></i> Add Row
                            </button>
                        </div>
                        <div id="specs-container">
                            <!-- Spec rows will be added here -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="save-product-btn" class="btn btn-primary">Save Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Product Modal -->
<div class="modal fade" id="edit-product-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="edit-product-form">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="edit_product_id">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label font-bold small text-muted">Product Code (SKU)</label>
                                <input type="text" name="code" id="edit_product_code" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label font-bold small text-muted">Product Name</label>
                                <input type="text" name="name" id="edit_product_name" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label font-bold small text-muted">Category</label>
                                <select name="category_id" id="edit_product_category" class="form-select select2 edit-select2" required style="width: 100%;">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label font-bold small text-muted">Unit of Measurement</label>
                                <select name="unit" id="edit_product_unit" class="form-select select2 edit-select2" required style="width: 100%;">
                                    <option value="">Select Unit</option>
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->name }}">{{ $unit->name }}</option>
                                    @endforeach
                                    <option value="Piece">Piece (Default)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label font-bold small text-muted">Wattage (Optional)</label>
                                <input type="text" name="wattage" id="edit_product_wattage" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label font-bold small text-muted">Selling Price (₹)</label>
                                <input type="number" name="selling_price" id="edit_product_price" step="0.01" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label font-bold small text-muted">Low Stock Alert</label>
                                <input type="number" name="low_stock_threshold" id="edit_product_threshold" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <label class="font-bold small text-muted mb-0">Custom Specifications</label>
                            <button type="button" id="edit-add-spec-btn" class="btn btn-success-light btn-sm">
                                <i class="fas fa-plus"></i> Add Row
                            </button>
                        </div>
                        <div id="edit-specs-container"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="update-product-btn" class="btn btn-primary">Update Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Import Product Modal -->
<div class="modal fade" id="import-product-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold">Import Products from CSV</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="import-product-form" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-info small border-0 shadow-none mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold"><i class="fas fa-info-circle me-1"></i> CSV Columns:</span>
                            <a href="{{ route('master.products.sample') }}" class="btn btn-xs btn-primary py-0 px-2 fw-bold" style="font-size: 10px;">
                                <i class="fas fa-download me-1"></i> Download Sample
                            </a>
                        </div>
                        SKU Code, Name, Category Name, Unit, Wattage, Price, Threshold<br>
                        <span class="text-muted opacity-75">Example: SU-L01, LED Bulb, LED Lights, Piece, 9W, 150, 20</span>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Select CSV File</label>
                        <input type="file" name="csv_file" class="form-control px-3 py-2" accept=".csv" required>
                    </div>
                </div>
                <div class="modal-footer border-top-0 p-4 pt-0">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="import-prod-btn" class="btn btn-primary px-4 fw-bold">Upload & Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="status-toast" style="position: fixed; bottom: 20px; right: 20px; padding: 12px 24px; border-radius: 8px; color: white; font-weight: 600; display: none; z-index: 9999;"></div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Initialize Select2 for Add modal
    if ($.fn.select2) {
        $('.add-select2').select2({
            width: '100%',
            dropdownParent: $('#add-product-modal')
        });
        $('.edit-select2').select2({
            width: '100%',
            dropdownParent: $('#edit-product-modal')
        });
    } else {
        console.error("Select2 is not loaded!");
    }

    // Dynamic Spec Rows - Add modal
    $('#add-spec-btn').on('click', function() {
        const row = `
            <div class="spec-row d-flex gap-2 mb-2">
                <input type="text" name="spec_keys[]" placeholder="Spec Name" class="form-control form-control-sm">
                <input type="text" name="spec_values[]" placeholder="Value" class="form-control form-control-sm">
                <button type="button" class="btn btn-sm btn-danger remove-spec-btn px-2"><i class="fas fa-trash"></i></button>
            </div>
        `;
        $('#specs-container').append(row);
    });

    // Dynamic Spec Rows - Edit modal
    $('#edit-add-spec-btn').on('click', function() {
        const row = `
            <div class="spec-row d-flex gap-2 mb-2">
                <input type="text" name="spec_keys[]" placeholder="Spec Name" class="form-control form-control-sm">
                <input type="text" name="spec_values[]" placeholder="Value" class="form-control form-control-sm">
                <button type="button" class="btn btn-sm btn-danger remove-spec-btn px-2"><i class="fas fa-trash"></i></button>
            </div>
        `;
        $('#edit-specs-container').append(row);
    });

    $(document).on('click', '.remove-spec-btn', function() {
        $(this).closest('.spec-row').remove();
    });

    // Add Product Form Submission
    $('#add-product-form').on('submit', function(e) {
        e.preventDefault();
        const btn = $('#save-product-btn');
        const oldText = btn.text();
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Saving...');

        $.ajax({
            url: "{{ route('master.products.store') }}",
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Product Saved',
                    text: response.message,
                    showConfirmButton: false,
                    timer: 1500
                });
                setTimeout(() => location.reload(), 1500);
            },
            error: function(xhr) {
                const msg = xhr.responseJSON?.message || 'Error saving product.';
                Swal.fire({
                    icon: 'error',
                    title: 'Save Failed',
                    text: msg
                });
                btn.prop('disabled', false).text(oldText);
            }
        });
    });

    // Edit Product Form Submission
    $('#edit-product-form').on('submit', function(e) {
        e.preventDefault();
        const btn = $('#update-product-btn');
        const oldText = btn.text();
        const id = $('#edit_product_id').val();
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Updating...');

        $.ajax({
            url: `/master/products/${id}`,
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
                const msg = xhr.responseJSON?.message || 'Error updating product.';
                Swal.fire({
                    icon: 'error',
                    title: 'Update Failed',
                    text: msg
                });
                btn.prop('disabled', false).text(oldText);
            }
        });
    });

    $('#import-product-form').on('submit', function(e) {
        e.preventDefault();
        const btn = $('#import-prod-btn');
        const oldText = btn.text();
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Processing...');

        const formData = new FormData(this);

        $.ajax({
            url: "{{ route('master.products.import') }}",
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
                const msg = xhr.responseJSON?.message || 'Error importing products.';
                Swal.fire({ icon: 'error', title: 'Import Failed', text: msg });
                btn.prop('disabled', false).text(oldText);
            }
        });
    });
});

function editProduct(product, encryptedId) {
    $('#edit_product_id').val(encryptedId);
    $('#edit_product_code').val(product.code);
    $('#edit_product_name').val(product.name);
    $('#edit_product_wattage').val(product.wattage);
    $('#edit_product_price').val(product.selling_price);
    $('#edit_product_threshold').val(product.low_stock_threshold);

    // Set Select2 values
    $('#edit_product_category').val(product.category_id).trigger('change');
    $('#edit_product_unit').val(product.unit).trigger('change');

    // Populate specs
    const container = $('#edit-specs-container');
    container.empty();
    if (product.specifications) {
        Object.entries(product.specifications).forEach(([key, val]) => {
            container.append(`
                <div class="spec-row d-flex gap-2 mb-2">
                    <input type="text" name="spec_keys[]" value="${key}" class="form-control form-control-sm">
                    <input type="text" name="spec_values[]" value="${val}" class="form-control form-control-sm">
                    <button type="button" class="btn btn-sm btn-danger remove-spec-btn px-2"><i class="fas fa-trash"></i></button>
                </div>
            `);
        });
    }

    $('#edit-product-modal').modal('show');
}

function deleteProduct(id) {
    Swal.fire({
        title: 'Delete Product?',
        text: "This will permanently remove the product from master data.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/master/products/${id}`,
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
                    const msg = xhr.responseJSON?.message || 'Error deleting product.';
                    Swal.fire({
                        icon: 'error',
                        title: 'Delete Failed',
                        text: msg
                    });
                }
            });
        }
    });
}
</script>
@endsection
