@extends('layouts.dashboard')

@section('title', 'Unit Master')

@section('content')
<style>
    @media (max-width: 768px) {
        .page-title-row { flex-direction: column; align-items: flex-start !important; gap: 15px; }
        .page-title-row .col-md-6 { width: 100%; text-align: left !important; }
        .page-title-row .btn { width: 100%; margin-bottom: 10px; margin-right: 0 !important; }
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
            <h1 class="page-title" style="font-size: 22px;">Unit Master</h1>
        </div>
        <p class="page-subtitle ms-md-4 ps-md-2 mb-0">Manage units of measurement for products.</p>
    </div>
    <div class="col-12 col-md-6 text-md-end mt-2 mt-md-0">
        <div class="d-flex flex-wrap justify-content-md-end gap-2">
            <button onclick="$('#import-unit-modal').modal('show')" class="btn btn-outline-primary px-3 py-2 fw-bold">
                <i class="fas fa-file-import me-2"></i>Import CSV
            </button>
            <button onclick="$('#add-unit-form')[0].reset(); $('#add-unit-modal').modal('show')" class="btn btn-primary px-3 py-2 fw-bold">
                <i class="fas fa-plus me-2"></i>Add Unit
            </button>
        </div>
    </div>
</div>

<div class="data-card border-0 shadow-sm">
    <div class="card-header bg-white py-3 border-bottom-0 d-flex justify-content-between align-items-center">
        <h3 class="h6 mb-0 text-muted fw-bold">Units List</h3>
        <div class="input-group" style="max-width: 300px;">
            <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-search"></i></span>
            <input type="text" class="form-control border-start-0 bg-light live-search-input" data-table="units-table" placeholder="Search units...">
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="units-table">
            <thead class="bg-light">
                <tr class="text-muted small text-uppercase">
                    <th class="ps-4 py-3 border-0" style="width: 80px;">#</th>
                    <th class="py-3 border-0">Unit Name</th>
                    <th class="py-3 border-0">Short Name</th>
                    <th class="pe-4 py-3 border-0 text-end">Actions</th>
                </tr>
            </thead>
            <tbody class="small">
                @foreach($units as $unit)
                <tr>
                    <td class="ps-4 py-3 text-muted">#{{ $loop->iteration }}</td>
                    <td class="py-3 fw-bold text-dark">{{ $unit->name }}</td>
                    <td class="py-3">
                        <span class="badge text-bg-light border text-primary fw-bold px-2 py-1">
                            {{ $unit->short_name }}
                        </span>
                    </td>
                    <td class="pe-4 py-3 text-end">
                        <div class="d-flex justify-content-end gap-1">
                            <button onclick="editUnit('{{ encrypt($unit->id) }}', '{{ addslashes($unit->name) }}', '{{ addslashes($unit->short_name) }}')" class="btn btn-sm btn-primary-light" title="Edit">
                                <i class="fas fa-pen"></i>
                            </button>
                            <button onclick="deleteUnit('{{ encrypt($unit->id) }}')" class="btn btn-sm btn-danger-light" title="Delete">
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

<!-- Add Unit Modal -->
<div class="modal fade" id="add-unit-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold">Add New Unit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="add-unit-form">
                @csrf
                <div class="modal-body p-4">
                    <div class="form-group mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Unit Name</label>
                        <input type="text" name="name" class="form-control px-3 py-2" placeholder="e.g., Piece, Box, Coil" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label small fw-bold text-muted text-uppercase">Short Name</label>
                        <input type="text" name="short_name" class="form-control px-3 py-2" placeholder="e.g., PCS, BOX, COIL" required style="text-transform: uppercase;">
                    </div>
                </div>
                <div class="modal-footer border-top-0 p-4 pt-0">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="save-unit-btn" class="btn btn-primary px-4 fw-bold">Save Unit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Unit Modal -->
<div class="modal fade" id="edit-unit-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold">Edit Unit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="edit-unit-form">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="edit_unit_id">
                <div class="modal-body p-4">
                    <div class="form-group mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Unit Name</label>
                        <input type="text" name="name" id="edit_unit_name" class="form-control px-3 py-2" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label small fw-bold text-muted text-uppercase">Short Name</label>
                        <input type="text" name="short_name" id="edit_unit_short_name" class="form-control px-3 py-2" required style="text-transform: uppercase;">
                    </div>
                </div>
                <div class="modal-footer border-top-0 p-4 pt-0">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="update-unit-btn" class="btn btn-primary px-4 fw-bold">Update Unit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Import Unit Modal -->
<div class="modal fade" id="import-unit-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold">Import Units from CSV</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="import-unit-form" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-info small border-0 shadow-none mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold"><i class="fas fa-info-circle me-1"></i> CSV Format:</span>
                            <a href="{{ route('master.units.sample') }}" class="btn btn-xs btn-primary py-0 px-2 fw-bold" style="font-size: 10px;">
                                <i class="fas fa-download me-1"></i> Download Sample
                            </a>
                        </div>
                        Name, Short Name<br>
                        <span class="text-muted opacity-75">Example: Piece, PCS</span>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Select CSV File</label>
                        <input type="file" name="csv_file" class="form-control px-3 py-2" accept=".csv" required>
                    </div>
                </div>
                <div class="modal-footer border-top-0 p-4 pt-0">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="import-unit-btn" class="btn btn-primary px-4 fw-bold">Upload & Import</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('#add-unit-form').on('submit', function(e) {
        e.preventDefault();
        const btn = $('#save-unit-btn');
        const oldText = btn.text();
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Saving...');

        $.ajax({
            url: "{{ route('master.units.store') }}",
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
                const msg = xhr.responseJSON?.message || 'Error saving unit.';
                Swal.fire({ icon: 'error', title: 'Oops...', text: msg });
                btn.prop('disabled', false).text(oldText);
            }
        });
    });

    $('#edit-unit-form').on('submit', function(e) {
        e.preventDefault();
        const btn = $('#update-unit-btn');
        const oldText = btn.text();
        const id = $('#edit_unit_id').val();
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Updating...');

        $.ajax({
            url: `/master/units/${id}`,
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
                const msg = xhr.responseJSON?.message || 'Error updating unit.';
                Swal.fire({ icon: 'error', title: 'Oops...', text: msg });
                btn.prop('disabled', false).text(oldText);
            }
        });
    });

    $('#import-unit-form').on('submit', function(e) {
        e.preventDefault();
        const btn = $('#import-unit-btn');
        const oldText = btn.text();
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Processing...');

        const formData = new FormData(this);

        $.ajax({
            url: "{{ route('master.units.import') }}",
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
                const msg = xhr.responseJSON?.message || 'Error importing units.';
                Swal.fire({ icon: 'error', title: 'Import Failed', text: msg });
                btn.prop('disabled', false).text(oldText);
            }
        });
    });
});

function editUnit(id, name, shortName) {
    $('#edit_unit_id').val(id);
    $('#edit_unit_name').val(name);
    $('#edit_unit_short_name').val(shortName);
    $('#edit-unit-modal').modal('show');
}

function deleteUnit(id) {
    Swal.fire({
        title: 'Delete Unit?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/master/units/${id}`,
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
                    const msg = xhr.responseJSON?.message || 'Error deleting unit.';
                    Swal.fire({ icon: 'error', title: 'Oops...', text: msg });
                }
            });
        }
    });
}
</script>
@endsection
