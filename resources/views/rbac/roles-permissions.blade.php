@extends('layouts.dashboard')

@section('title', 'Roles & Permissions Management')

@section('content')
<div class="row align-items-center mb-4 g-3">
    <div class="col-12 col-md-6">
        <h1 class="h3 fw-bold text-dark mb-1">Roles & Permissions</h1>
        <p class="text-muted mb-0">Manage security roles and their access levels.</p>
    </div>
    <div class="col-12 col-md-6 text-md-end">
        <button onclick="$('#add-role-form')[0].reset(); $('#add-role-modal').modal('show')" class="btn btn-primary px-4 fw-bold">
            <i class="fas fa-plus me-2"></i>Add New Role
        </button>
    </div>
</div>

<div class="row g-4">
    <!-- Roles List -->
    <div class="col-12 col-lg-4">
        <div class="data-card border-0 shadow-sm">
            <h3 class="h6 fw-bold text-dark mb-4 text-uppercase letter-spacing-1">Select Role</h3>
            <div class="d-flex flex-column gap-2" id="role-list-container">
                @foreach($roles as $role)
                <div class="role-item-wrapper d-flex align-items-center gap-2">
                    <button class="role-selector-btn flex-grow-1 text-start px-3 py-3 rounded-3 border {{ $loop->first ? 'active' : '' }}" 
                        data-role-id="{{ $role->id }}"
                        data-role-name="{{ $role->name }}"
                        data-role-slug="{{ $role->slug }}"
                        data-role-desc="{{ $role->description }}"
                        data-permissions="{{ json_encode($role->permissions->pluck('id')) }}">
                        <div class="fw-bold small mb-0">{{ $role->name }}</div>
                        <div class="extra-small opacity-75">{{ $role->slug }}</div>
                    </button>
                    <div class="action-btns d-flex flex-column gap-1">
                        <button onclick="editRole('{{ encrypt($role->id) }}', '{{ addslashes($role->name) }}', '{{ addslashes($role->slug) }}', '{{ addslashes($role->description) }}')" class="btn btn-sm btn-light p-1 border-0" title="Edit Role">
                            <i class="fas fa-pen text-primary" style="font-size: 10px;"></i>
                        </button>
                        <button onclick="deleteRole('{{ encrypt($role->id) }}', '{{ $role->slug }}')" class="btn btn-sm btn-light p-1 border-0" title="Delete Role">
                            <i class="fas fa-trash text-danger" style="font-size: 10px;"></i>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Permissions Grid -->
    <div class="col-12 col-lg-8">
        <div class="data-card border-0 shadow-sm">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="h6 fw-bold text-dark mb-1 text-uppercase letter-spacing-1">Permissions for: <span id="current-role-name" class="text-primary">{{ $roles[0]->name ?? '' }}</span></h3>
                    <p class="extra-small text-muted mb-0">Select access levels for this role.</p>
                </div>
                <button id="save-permissions-btn" class="btn btn-primary px-4 fw-bold">
                    <i class="fas fa-save me-2"></i>Save Permissions
                </button>
            </div>

            <div class="row g-3">
                @foreach($permissions as $permission)
                <div class="col-12 col-md-6 col-xl-4">
                    <label class="permission-card d-flex align-items-center gap-3 p-3 rounded-4 border h-100 cursor-pointer transition-all hover-shadow">
                        <input type="checkbox" class="permission-checkbox form-check-input flex-shrink-0 m-0" value="{{ $permission->id }}">
                        <div class="min-w-0">
                            <div class="fw-bold small text-dark text-truncate">{{ $permission->name }}</div>
                            <div class="extra-small text-muted">{{ $permission->slug }}</div>
                        </div>
                    </label>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Add Role Modal -->
<div class="modal fade" id="add-role-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold">Create New Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="add-role-form">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Role Name</label>
                        <input type="text" name="name" class="form-control px-3 py-2" placeholder="e.g., Inventory Manager" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Slug (Unique ID)</label>
                        <input type="text" name="slug" class="form-control px-3 py-2" placeholder="e.g., inventory_manager" required>
                    </div>
                    <div>
                        <label class="form-label small fw-bold text-muted text-uppercase">Description</label>
                        <textarea name="description" class="form-control px-3 py-2" rows="2" placeholder="What can this role do?"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top-0 p-4 pt-0">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="save-role-btn" class="btn btn-primary px-4 fw-bold">Create Role</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Role Modal -->
<div class="modal fade" id="edit-role-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold">Edit Role Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="edit-role-form">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="edit_role_id">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Role Name</label>
                        <input type="text" name="name" id="edit_role_name" class="form-control px-3 py-2" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Slug</label>
                        <input type="text" name="slug" id="edit_role_slug" class="form-control px-3 py-2" required>
                    </div>
                    <div>
                        <label class="form-label small fw-bold text-muted text-uppercase">Description</label>
                        <textarea name="description" id="edit_role_desc" class="form-control px-3 py-2" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top-0 p-4 pt-0">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="update-role-btn" class="btn btn-primary px-4 fw-bold">Update Role</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .role-selector-btn {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        color: var(--text-dark);
        transition: all 0.2s ease;
    }
    .role-selector-btn:hover {
        background: rgba(0,0,0,0.02);
        border-color: #cbd5e1;
    }
    [data-theme="dark"] .role-selector-btn:hover {
        background: rgba(255,255,255,0.05);
    }
    .role-selector-btn.active {
        background: var(--primary-blue) !important;
        color: white !important;
        border-color: var(--primary-blue) !important;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }
    .permission-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        transition: all 0.2s ease;
    }
    .permission-card:hover {
        border-color: var(--primary-blue) !important;
        transform: translateY(-2px);
    }
    [data-theme="dark"] .permission-card:hover {
        background: rgba(255,255,255,0.02) !important;
    }
    .letter-spacing-1 { letter-spacing: 1px; }
    .hover-shadow:hover { box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
    .cursor-pointer { cursor: pointer; }
</style>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    let currentRoleId = $('.role-selector-btn.active').data('role-id');

    // Sync Checkboxes with active role
    function updateCheckboxes() {
        const activeBtn = $(`.role-selector-btn[data-role-id="${currentRoleId}"]`);
        const permissions = activeBtn.data('permissions') || [];
        $('.permission-checkbox').prop('checked', false);
        permissions.forEach(id => {
            $(`.permission-checkbox[value="${id}"]`).prop('checked', true);
        });
        $('#current-role-name').text(activeBtn.data('role-name'));
    }

    updateCheckboxes();

    // Role Selection
    $(document).on('click', '.role-selector-btn', function() {
        $('.role-selector-btn').removeClass('active');
        $(this).addClass('active');
        currentRoleId = $(this).data('role-id');
        updateCheckboxes();
    });

    // Save Permissions
    $('#save-permissions-btn').on('click', function() {
        const selectedPermissions = [];
        $('.permission-checkbox:checked').each(function() {
            selectedPermissions.push($(this).val());
        });

        const btn = $(this);
        const oldHtml = btn.html();
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Saving...');

        $.ajax({
            url: "{{ route('rbac.roles.sync') }}",
            method: 'POST',
            data: {
                role_id: currentRoleId,
                permissions: selectedPermissions,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                Swal.fire({ icon: 'success', title: 'Saved!', text: response.message, timer: 1500, showConfirmButton: false });
                $(`.role-selector-btn[data-role-id="${currentRoleId}"]`).data('permissions', selectedPermissions);
                btn.prop('disabled', false).html(oldHtml);
            },
            error: function() {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to update permissions.' });
                btn.prop('disabled', false).html(oldHtml);
            }
        });
    });

    // Add Role AJAX
    $('#add-role-form').on('submit', function(e) {
        e.preventDefault();
        const btn = $('#save-role-btn');
        const oldText = btn.text();
        btn.prop('disabled', true).text('Creating...');

        $.ajax({
            url: "{{ route('rbac.roles.store') }}",
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                Swal.fire({ icon: 'success', title: 'Success', text: response.message });
                setTimeout(() => location.reload(), 1500);
            },
            error: function(xhr) {
                const msg = xhr.responseJSON?.message || 'Error creating role.';
                Swal.fire({ icon: 'error', title: 'Error', text: msg });
                btn.prop('disabled', false).text(oldText);
            }
        });
    });

    // Update Role AJAX
    $('#edit-role-form').on('submit', function(e) {
        e.preventDefault();
        const id = $('#edit_role_id').val();
        const btn = $('#update-role-btn');
        btn.prop('disabled', true).text('Updating...');

        $.ajax({
            url: `/rbac/roles/${id}`,
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                Swal.fire({ icon: 'success', title: 'Updated', text: response.message });
                setTimeout(() => location.reload(), 1500);
            },
            error: function(xhr) {
                const msg = xhr.responseJSON?.message || 'Error updating role.';
                Swal.fire({ icon: 'error', title: 'Error', text: msg });
                btn.prop('disabled', false).text('Update Role');
            }
        });
    });
});

function editRole(id, name, slug, desc) {
    $('#edit_role_id').val(id);
    $('#edit_role_name').val(name);
    $('#edit_role_slug').val(slug);
    $('#edit_role_desc').val(desc);
    $('#edit-role-modal').modal('show');
}

function deleteRole(id, slug) {
    if (['super_admin', 'admin'].includes(slug)) {
        Swal.fire({ icon: 'warning', title: 'Restricted', text: 'Critical system roles cannot be deleted.' });
        return;
    }

    Swal.fire({
        title: 'Delete Role?',
        text: "This will affect users assigned to this role!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/rbac/roles/${id}`,
                method: 'POST',
                data: { _token: '{{ csrf_token() }}', _method: 'DELETE' },
                success: function(response) {
                    Swal.fire({ icon: 'success', title: 'Deleted', text: response.message });
                    setTimeout(() => location.reload(), 1000);
                },
                error: function(xhr) {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to delete role.' });
                }
            });
        }
    });
}
</script>
@endsection
