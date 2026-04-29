@extends('layouts.app')

@section('title', 'Login - Ultra5G')

@section('styles')
<style>
    .login-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 70vh;
        padding: 20px;
    }

    .login-card {
        width: 100%;
        max-width: 400px;
        background: var(--glass-bg);
        backdrop-filter: blur(15px);
        border: 1px solid var(--glass-border);
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        transform: translateY(0);
        transition: transform 0.3s ease;
    }

    .login-card:hover {
        transform: translateY(-5px);
    }

    .login-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .login-header h2 {
        font-size: 24px;
        color: #1e3c72;
    }

    .form-group a:hover {
        text-decoration: underline !important;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        font-size: 14px;
    }

    .form-control {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 16px;
        transition: all 0.3s ease;
        box-sizing: border-box;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
    }

    .login-btn {
        width: 100%;
        margin-top: 10px;
    }

    .error-msg {
        background-color: #fff2f2;
        color: #d32f2f;
        padding: 10px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 14px;
        display: none;
        border-left: 4px solid #d32f2f;
    }

    /* Responsive adjustments */
    @media (max-width: 480px) {
        .login-card {
            padding: 25px;
        }
        
        .login-header h2 {
            font-size: 20px;
        }
    }
</style>
@endsection

@section('content')
<div class="login-container">
    <div class="login-card">
        <div class="login-header">
            <div style="display: flex; justify-content: center; margin-bottom: 20px;">
                <img src="{{ asset('assets/images/logo.svg') }}" alt="SUNUltra 5G" style="height: 60px; width: auto; max-width: 100%;">
            </div>
            <h2>Sign In</h2>
            <p style="color: var(--secondary-color); font-size: 14px;">Enter your credentials to access your account</p>
        </div>

        <div id="error-alert" class="error-msg"></div>

        <form id="login-form">
            @csrf
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="admin@example.com" required>
            </div>

            <div class="form-group">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <label for="password" style="margin: 0;">Password</label>
                    <a href="{{ route('password.request') }}" style="font-size: 13px; color: var(--primary-color); text-decoration: none; font-weight: 500; border: none; outline: none;">Forgot Password?</a>
                </div>
                <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" id="submit-btn" class="btn login-btn">Login Now</button>
        </form>

        <div style="text-align: center; margin-top: 20px; font-size: 13px; color: var(--secondary-color);">
            <p>Demo Credentials: <strong>admin@example.com</strong> / <strong>password123</strong></p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#login-form').on('submit', function(e) {
            e.preventDefault();
            
            const form = $(this);
            const btn = $('#submit-btn');
            const errorAlert = $('#error-alert');
            
            btn.text('Authenticating...').prop('disabled', true);
            errorAlert.fadeOut();

            $.ajax({
                url: '/login',
                type: 'POST',
                data: form.serialize(),
                success: function(response) {
                    btn.text('Success! Redirecting...').css('background-color', '#28a745');
                    setTimeout(() => {
                        window.location.href = response.data.redirect;
                    }, 1000);
                },
                error: function(xhr) {
                    btn.text('Login Now').prop('disabled', false);
                    let message = 'An error occurred. Please try again.';
                    
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    
                    errorAlert.text(message).fadeIn();
                }
            });
        });
    });
</script>
@endsection
