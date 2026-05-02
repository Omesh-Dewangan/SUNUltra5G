@extends('layouts.dashboard')

@section('title', 'User Role Management')

@section('content')
<div class="content-header bg-white p-4 rounded-4 shadow-sm border-0 mb-4">
    <div class="w-100">
        <span class="breadcrumb-item">Security / Access Control</span>
        <div class="d-flex align-items-center">
            <a href="{{ route('dashboard') }}" class="text-muted back-btn-minimal me-2" title="Back to Dashboard">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="page-title">User Role Management</h1>
        </div>
        <p class="page-subtitle ms-md-4 ps-md-2">Configure system users, permissions and security levels.</p>
    </div>
</div>

<style>
    .password-wrapper {
        position: relative;
    }
    .toggle-password {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #94a3b8;
        z-index: 10;
        transition: color 0.2s;
    }
    .toggle-password:hover {
        color: var(--primary-blue);
    }
</style>
<div class="data-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
        <h3 class="h6 fw-bold text-muted mb-0">Active System Users</h3>
        
        <div style="display: flex; gap: 10px; align-items: center;">
            <div class="input-group" style="width: 250px;">
                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-search"></i></span>
                <input type="text" class="form-control border-start-0 bg-light live-search-input" data-table="users-table" placeholder="Search users...">
            </div>
            
            <button class="btn btn-primary fw-bold px-3" data-bs-toggle="modal" data-bs-target="#create-user-modal">
                <i class="fas fa-user-plus me-2"></i>Create User
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table style="width: 100%; border-collapse: collapse; text-align: left;" id="users-table">
            <thead>
                <tr style="border-bottom: 2px solid var(--border-color);">
                    <th style="padding: 12px; font-size: 14px; color: var(--text-muted);">User</th>
                    <th style="padding: 12px; font-size: 14px; color: var(--text-muted);">Email</th>
                    <th style="padding: 12px; font-size: 14px; color: var(--text-muted);">Current Role</th>
                    <th style="padding: 12px; font-size: 14px; color: var(--text-muted);">Assign Role</th>
                    <th style="padding: 12px; font-size: 14px; color: var(--text-muted); text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr style="border-bottom: 1px solid var(--border-color);">
                    <td style="padding: 12px;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 32px; height: 32px; border-radius: 8px; background: var(--primary-blue); color: white; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600;">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <span style="font-weight: 600; font-size: 14px;">{{ $user->name }}</span>
                        </div>
                    </td>
                    <td style="padding: 12px; font-size: 14px; color: var(--text-muted);">{{ $user->email }}</td>
                    <td style="padding: 12px;">
                        @foreach($user->roles as $role)
                            <span style="background: rgba(34, 197, 94, 0.1); color: #22c55e; padding: 4px 8px; border-radius: 6px; font-size: 12px; font-weight: 600;">
                                {{ $role->name }}
                            </span>
                        @endforeach
                    </td>
                    <td style="padding: 12px;">
                        <select class="role-assign-select" data-user-id="{{ $user->id }}" 
                            style="padding: 6px 12px; border-radius: 6px; border: 1px solid var(--border-color); background: var(--input-bg); color: var(--text-dark); font-size: 13px;">
                            <option value="">Select Role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ $user->roles->contains('id', $role->id) ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td style="padding: 12px; text-align: right;">
                        <div class="d-flex justify-content-end gap-2">
                            <button onclick="editUser({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ addslashes($user->email) }}')" class="btn btn-sm btn-light text-primary border" title="Edit User">
                                <i class="fas fa-pen"></i>
                            </button>
                            <button onclick="deleteUser({{ $user->id }})" class="btn btn-sm btn-light text-danger border" title="Delete User">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div style="margin-top: 20px;">
        {{ $users->links() }}
    </div>
</div>

<div id="status-toast" style="position: fixed; bottom: 20px; right: 20px; padding: 12px 24px; border-radius: 8px; color: white; font-weight: 600; display: none; z-index: 9999;"></div>

<!-- Create User Modal -->
<div class="modal fade" id="create-user-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 12px;">
            <div class="modal-header bg-light border-bottom-0" style="border-radius: 12px 12px 0 0;">
                <h5 class="modal-title fw-bold text-dark"><i class="fas fa-user-plus me-2 text-primary"></i>Add New User</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="create-user-form">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control px-3 py-2" placeholder="e.g., John Doe" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Email Address <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control px-3 py-2" placeholder="john@example.com" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Password <span class="text-danger">*</span></label>
                        <div class="password-wrapper">
                            <input type="password" name="password" class="form-control px-3 py-2" placeholder="Min 8 characters" required minlength="8">
                            <i class="fas fa-eye toggle-password"></i>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label small fw-bold text-muted text-uppercase">Assign Role</label>
                        <select name="role_id" class="form-select px-3 py-2">
                            <option value="">-- No Role (Assign Later) --</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top-0 p-4 pt-0">
                    <button type="button" class="btn btn-light px-4 fw-bold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="submit-user-btn" class="btn btn-primary px-4 fw-bold shadow-sm">Create User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="edit-user-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 12px;">
            <div class="modal-header bg-light border-bottom-0" style="border-radius: 12px 12px 0 0;">
                <h5 class="modal-title fw-bold text-dark"><i class="fas fa-user-edit me-2 text-primary"></i>Edit User Details</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="edit-user-form">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="edit_user_id">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="edit_user_name" class="form-control px-3 py-2" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Email Address <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="edit_user_email" class="form-control px-3 py-2" required>
                    </div>

                    <div class="mb-0">
                        <label class="form-label small fw-bold text-muted text-uppercase">New Password</label>
                        <div class="password-wrapper">
                            <input type="password" name="password" class="form-control px-3 py-2" placeholder="Leave blank to keep current password" minlength="8">
                            <i class="fas fa-eye toggle-password"></i>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 p-4 pt-0">
                    <button type="button" class="btn btn-light px-4 fw-bold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="update-user-btn" class="btn btn-primary px-4 fw-bold shadow-sm">Update User</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Toggle Password Visibility
    $(document).on('click', '.toggle-password', function() {
        const wrapper = $(this).closest('.password-wrapper');
        const input = wrapper.find('input');
        const icon = $(this);
        
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    $('.role-assign-select').on('change', function() {
        const userId = $(this).data('user-id');
        const roleId = $(this).val();
        
        if (!roleId) return;

        const toast = $('#status-toast');

        $.ajax({
            url: "{{ route('rbac.users.assign') }}",
            method: 'POST',
            data: {
                user_id: userId,
                role_id: roleId,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                toast.text(response.message).css('background', '#22c55e').fadeIn();
                setTimeout(() => toast.fadeOut(), 3000);
                location.reload(); // Reload to reflect changes
            },
            error: function() {
                toast.text('Failed to assign role.').css('background', '#ef4444').fadeIn();
                setTimeout(() => toast.fadeOut(), 3000);
            }
        });
    });

    // Create User Form Submission
    $('#create-user-form').on('submit', function(e) {
        e.preventDefault();
        const btn = $('#submit-user-btn');
        const oldText = btn.html();
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Creating...');
        const toast = $('#status-toast');

        $.ajax({
            url: "{{ route('rbac.users.store') }}",
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                $('#create-user-modal').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'User Created!',
                    text: response.message,
                    showConfirmButton: false,
                    timer: 1500
                });
                setTimeout(() => location.reload(), 1500);
            },
            error: function(xhr) {
                const msg = xhr.responseJSON?.message || 'Error creating user.';
                Swal.fire({
                    icon: 'error',
                    title: 'Failed',
                    text: msg
                });
                btn.prop('disabled', false).html(oldText);
            }
        });
    });

    // Edit User Form Submission
    $('#edit-user-form').on('submit', function(e) {
        e.preventDefault();
        const btn = $('#update-user-btn');
        const oldText = btn.html();
        const id = $('#edit_user_id').val();
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Updating...');

        $.ajax({
            url: `/rbac/users/${id}`,
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                $('#edit-user-modal').modal('hide');
                Swal.fire({ icon: 'success', title: 'Updated!', text: response.message, showConfirmButton: false, timer: 1500 });
                setTimeout(() => location.reload(), 1500);
            },
            error: function(xhr) {
                const msg = xhr.responseJSON?.message || 'Error updating user.';
                Swal.fire({ icon: 'error', title: 'Failed', text: msg });
                btn.prop('disabled', false).html(oldText);
            }
        });
    });
});

function editUser(id, name, email) {
    $('#edit_user_id').val(id);
    $('#edit_user_name').val(name);
    $('#edit_user_email').val(email);
    $('#edit-user-form input[name="password"]').val('');
    $('#edit-user-modal').modal('show');
}

function deleteUser(id) {
    Swal.fire({
        title: 'Delete User?',
        text: "This action cannot be undone!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Yes, delete user!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/rbac/users/${id}`,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    _method: 'DELETE'
                },
                success: function(response) {
                    Swal.fire({ icon: 'success', title: 'Deleted!', text: response.message, showConfirmButton: false, timer: 1500 });
                    setTimeout(() => location.reload(), 1500);
                },
                error: function(xhr) {
                    const msg = xhr.responseJSON?.message || 'Error deleting user.';
                    Swal.fire({ icon: 'error', title: 'Failed', text: msg });
                }
            });
        }
    });
}
</script>
@endsection
