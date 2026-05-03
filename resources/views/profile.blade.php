@extends('layouts.dashboard')

@section('title', 'Account Settings - SUNUltra 5G')

@section('content')
<style>
    .password-wrapper {
        position: relative;
        width: 100%;
        max-width: 400px;
    }
    .toggle-password {
        position: absolute;
        right: 12px;
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
    @media (max-width: 768px) {
        .grid-2-col { grid-template-columns: 1fr !important; gap: 15px !important; }
        .data-card { padding: 20px !important; }
        .profile-header h1 { font-size: 20px !important; }
        .profile-header p { font-size: 12px !important; }
    }
</style>
<div style="max-width: 800px; margin: 0 auto;">
    <div style="margin-bottom: 24px;" class="profile-header">
        <div class="d-flex align-items-center">
            <a href="{{ route('dashboard') }}" class="text-muted back-btn-minimal me-2">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 style="font-size: 24px; font-weight: 700; color: var(--text-dark); margin:0;">Account Settings</h1>
                <p style="color: var(--text-muted); margin-top: 4px; margin-bottom:0;">Update your personal information and security settings.</p>
            </div>
        </div>
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
                <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 5px;">Change Password</h3>
                <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 15px;">You must verify your current password before setting a new one.</p>
                
                <div style="display: flex; flex-direction: column; gap: 8px; margin-bottom: 16px;">
                    <label style="font-size: 14px; font-weight: 600; color: var(--text-dark);">Current Password <span style="color:#ef4444">*</span></label>
                <div class="password-wrapper">
                    <input type="password" name="current_password" id="current_password" placeholder="Enter your current password"
                        style="padding: 10px; padding-right: 40px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; width: 100%;">
                    <i class="fas fa-eye toggle-password"></i>
                </div>
                    <span id="current-pwd-error" style="color:#ef4444; font-size:12px; display:none;"></span>
                </div>

                <div class="grid-2-col">
                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        <label style="font-size: 14px; font-weight: 600; color: var(--text-dark);">New Password</label>
                        <div class="password-wrapper">
                            <input type="password" name="password" id="new_password"
                                style="padding: 10px; padding-right: 40px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; width: 100%;">
                            <i class="fas fa-eye toggle-password"></i>
                        </div>
                        <span id="new-pwd-error" style="color:#ef4444; font-size:12px; display:none;"></span>
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        <label style="font-size: 14px; font-weight: 600; color: var(--text-dark);">Confirm Password</label>
                        <div class="password-wrapper">
                            <input type="password" name="password_confirmation" id="confirm_password"
                                style="padding: 10px; padding-right: 40px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; width: 100%;">
                            <i class="fas fa-eye toggle-password"></i>
                        </div>
                        <span id="confirm-pwd-error" style="color:#ef4444; font-size:12px; display:none;"></span>
                    </div>
                </div>

                <div style="margin-top: 12px; padding: 10px 14px; background: #fef3c7; border-left: 4px solid #f59e0b; border-radius: 6px; font-size: 13px; color: #92400e;">
                    <i class="fas fa-info-circle me-1"></i>
                    <strong>Forgot your current password?</strong> Please contact your System Administrator to reset it.
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
        // Toggle Password Visibility
        $('.toggle-password').on('click', function() {
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

        // Real-time Password Matching Validation
        $('input[name="password"], input[name="password_confirmation"], #current_password').on('keyup', function() {
            const current = $('#current_password').val();
            const password = $('input[name="password"]').val();
            const confirm = $('input[name="password_confirmation"]').val();
            const saveBtn = $('#save-btn');
            
            // Clear all inline errors first
            $('#current-pwd-error, #new-pwd-error, #confirm-pwd-error').hide().text('');
            $('#current_password, #new_password, #confirm_password').css('border-color', '#e2e8f0');

            if (current.length > 0 || password.length > 0 || confirm.length > 0) {
                let hasError = false;

                // Current Password Check
                if (current.length === 0) {
                    $('#current-pwd-error').text('Required for password change.').show();
                    $('#current_password').css('border-color', '#ef4444');
                    hasError = true;
                }
                
                // New Password Check
                if (password.length === 0) {
                    $('#new-pwd-error').text('Please enter a new password.').show();
                    $('#new_password').css('border-color', '#ef4444');
                    hasError = true;
                } else if (password.length < 8) {
                    $('#new-pwd-error').text('Must be at least 8 characters.').show();
                    $('#new_password').css('border-color', '#ef4444');
                    hasError = true;
                }

                // Confirm Password Check
                if (confirm.length === 0) {
                    $('#confirm-pwd-error').text('Please confirm your new password.').show();
                    $('#confirm_password').css('border-color', '#ef4444');
                    hasError = true;
                } else if (password !== confirm) {
                    $('#confirm-pwd-error').text('Passwords do not match!').show();
                    $('#confirm_password').css('border-color', '#ef4444');
                    hasError = true;
                }

                saveBtn.prop('disabled', hasError);
            } else {
                saveBtn.prop('disabled', false);
            }
        });

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
                    $('#header-user-name').text(response.data.user.name);
                    $('#header-user-initial').text(response.data.user.initial);
                    $('#profile-dropdown-name').text(response.data.user.name);
                    $('#profile-dropdown-email').text(response.data.user.email);

                    // Clear password fields on success
                    $('#current_password').val('');
                    $('input[name="password"]').val('');
                    $('input[name="password_confirmation"]').val('');
                    $('#current-pwd-error').hide();
                    
                    btn.prop('disabled', false).text('Save Changes');
                },
                error: function(xhr) {
                    const errors = xhr.responseJSON?.errors;
                    let errorMsg = 'Update failed. Please try again.';
                    if (errors) {
                        // Show current_password error inline
                        if (errors.current_password) {
                            $('#current-pwd-error').text(errors.current_password[0]).show();
                            $('#current_password').css('border-color', '#ef4444');
                        } else {
                            $('#current-pwd-error').hide();
                            $('#current_password').css('border-color', '#e2e8f0');
                        }
                        // Show first error in alert box
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
