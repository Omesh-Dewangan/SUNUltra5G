@extends('layouts.dashboard')

@section('title', 'User Role Management')

@section('content')
<div class="data-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 class="card-title" style="margin-bottom: 0;">User Role Management</h2>
    </div>

    <div class="table-responsive">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
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
});
</script>
@endsection
