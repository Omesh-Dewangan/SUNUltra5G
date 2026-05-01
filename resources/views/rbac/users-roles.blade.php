@extends('layouts.dashboard')

@section('title', 'User Role Management')

@section('content')
<div class="data-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
        <h2 class="card-title" style="margin-bottom: 0;">User Role Management</h2>
        
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
                        <input type="password" name="password" class="form-control px-3 py-2" placeholder="Min 8 characters" required minlength="8">
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

@endsection

@section('scripts')
<script>
$(document).ready(function() {
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
});
</script>
@endsection
