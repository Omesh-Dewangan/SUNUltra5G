@extends('layouts.app')

@section('title', 'Forgot Password - Ultra5G')

@section('styles')
<style>
    .forgot-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 70vh;
        padding: 20px;
    }

    .forgot-card {
        width: 100%;
        max-width: 400px;
        background: var(--glass-bg);
        backdrop-filter: blur(15px);
        border: 1px solid var(--glass-border);
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        animation: fadeIn 0.8s ease-out;
    }

    .forgot-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .forgot-header h2 {
        font-size: 24px;
        color: #1e3c72;
        margin-bottom: 10px;
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
        box-sizing: border-box;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
    }
</style>
@endsection

@section('content')
<div class="forgot-container">
    <div class="forgot-card">
        <div class="forgot-header">
            <img src="{{ asset('assets/images/logo.svg') }}" alt="SUNUltra 5G" style="height: 50px; margin-bottom: 15px; width: auto;">
            <h2>Reset Password</h2>
            <p style="color: var(--secondary-color); font-size: 14px;">Enter your email to receive a password reset link.</p>
        </div>

        <div id="status-msg" style="display: none; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;"></div>

        <form id="forgot-form">
            @csrf
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="your@email.com" required>
            </div>

            <button type="submit" id="submit-btn" class="btn" style="width: 100%;">Send Reset Link</button>
        </form>

        <div style="text-align: center; margin-top: 25px;">
            <a href="{{ route('login') }}" style="color: var(--primary-color); text-decoration: none; font-size: 14px; font-weight: 600;">
                <i class="fas fa-arrow-left"></i> Back to Login
            </a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#forgot-form').on('submit', function(e) {
            e.preventDefault();
            const btn = $('#submit-btn');
            const msg = $('#status-msg');
            
            btn.text('Processing...').prop('disabled', true);
            msg.hide();

            // Simulate sending reset link
            setTimeout(() => {
                msg.text('A password reset link has been sent to your email address.')
                   .css({'background': '#d1fae5', 'color': '#065f46', 'display': 'block'});
                btn.text('Link Sent').prop('disabled', false);
            }, 1500);
        });
    });
</script>
@endsection
