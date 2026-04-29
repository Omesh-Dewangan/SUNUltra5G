@extends('layouts.dashboard')

@section('title', 'Account Settings - SUNUltra 5G')

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <div style="margin-bottom: 24px;">
        <h1 style="font-size: 24px; font-weight: 700; color: var(--text-dark);">Account Settings</h1>
        <p style="color: var(--text-muted); margin-top: 4px;">Update your personal information and security settings.</p>
    </div>

    <div class="data-card">
        <form id="profile-form">
            @csrf
            <div class="grid-2-col" style="margin-bottom: 20px;">
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <label style="font-size: 14px; font-weight: 600; color: var(--text-dark);">Full Name</label>
                    <input type="text" name="name" value="{{ Auth::user()->name }}" required 
                        style="padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px;">
                </div>
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <label style="font-size: 14px; font-weight: 600; color: var(--text-dark);">Email Address</label>
                    <input type="email" name="email" value="{{ Auth::user()->email }}" required 
                        style="padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px;">
                </div>
            </div>

            <div style="border-top: 1px solid #f1f5f9; padding-top: 20px; margin-top: 20px;">
                <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 15px;">Change Password</h3>
                <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 15px;">Leave blank if you don't want to change your password.</p>
                
                <div class="grid-2-col">
                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        <label style="font-size: 14px; font-weight: 600; color: var(--text-dark);">New Password</label>
                        <input type="password" name="password" 
                            style="padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px;">
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        <label style="font-size: 14px; font-weight: 600; color: var(--text-dark);">Confirm Password</label>
                        <input type="password" name="password_confirmation" 
                            style="padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px;">
                    </div>
                </div>
            </div>

            <div style="margin-top: 30px; display: flex; justify-content: flex-end; gap: 12px;">
                <button type="submit" id="save-btn" class="btn" style="background: var(--primary-blue); color: white; padding: 10px 24px; border-radius: 8px; border: none; cursor: pointer; font-weight: 600;">
                    Save Changes
                </button>
            </div>
        </form>
    </div>

    <div id="alert-box" style="display: none; margin-top: 20px; padding: 15px; border-radius: 8px; font-size: 14px;"></div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#profile-form').on('submit', function(e) {
            e.preventDefault();
            
            const btn = $('#save-btn');
            const alertBox = $('#alert-box');
            
            btn.prop('disabled', true).text('Saving...');
            alertBox.hide().removeClass('success error');

            $.ajax({
                url: "{{ route('profile.update') }}",
                method: "POST",
                data: $(this).serialize(),
                success: function(response) {
                    alertBox.text(response.message)
                        .css({'background': '#d1fae5', 'color': '#065f46', 'display': 'block'});
                    
                    // Update header names dynamically
                    $('#header-user-name').text(response.user.name);
                    $('#header-user-initial').text(response.user.initial);
                    $('#profile-dropdown-name').text(response.user.name);
                    $('#profile-dropdown-email').text(response.user.email);
                    
                    btn.prop('disabled', false).text('Save Changes');
                },
                error: function(xhr) {
                    const errors = xhr.responseJSON.errors;
                    let errorMsg = 'Update failed. ';
                    if (errors) {
                        errorMsg = Object.values(errors)[0][0];
                    }
                    alertBox.text(errorMsg)
                        .css({'background': '#fee2e2', 'color': '#b91c1c', 'display': 'block'});
                    
                    btn.prop('disabled', false).text('Save Changes');
                }
            });
        });
    });
</script>
@endsection
