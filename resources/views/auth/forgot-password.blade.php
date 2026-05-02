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
            <div style="display: flex; justify-content: center; margin-bottom: 15px;">
                <img src="{{ asset('assets/images/logo.svg') }}" alt="SUNUltra 5G" style="height: 50px; width: auto; max-width: 100%;">
            </div>
            <h2>Forgot Password?</h2>
            <p style="color: var(--secondary-color); font-size: 14px;">Here's how to recover your account access.</p>
        </div>

        {{-- Step 1: Try changing via Account Settings --}}
        <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 18px; margin-bottom: 16px;">
            <div style="display: flex; align-items: flex-start; gap: 12px;">
                <div style="background: #22c55e; color: white; border-radius: 50%; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 700; flex-shrink: 0;">1</div>
                <div>
                    <p style="font-weight: 700; font-size: 14px; margin: 0 0 4px; color: #15803d;">Remember your password?</p>
                    <p style="font-size: 13px; color: #166534; margin: 0;">Login and go to <strong>Account Settings → Change Password</strong>. You will need your current password to set a new one.</p>
                </div>
            </div>
        </div>

        {{-- Step 2: Contact Admin --}}
        <div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 10px; padding: 18px; margin-bottom: 24px;">
            <div style="display: flex; align-items: flex-start; gap: 12px;">
                <div style="background: #f59e0b; color: white; border-radius: 50%; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 700; flex-shrink: 0;">2</div>
                <div>
                    <p style="font-weight: 700; font-size: 14px; margin: 0 0 4px; color: #92400e;">Forgot your current password?</p>
                    <p style="font-size: 13px; color: #78350f; margin: 0;">Contact your <strong>System Administrator</strong>. They can reset your password directly from the <strong>System Control → User Access</strong> panel.</p>
                </div>
            </div>
        </div>

        <div style="text-align: center;">
            <a href="{{ route('login') }}" style="color: var(--primary-color); text-decoration: none; font-size: 14px; font-weight: 600;">
                <i class="fas fa-arrow-left"></i> Back to Login
            </a>
        </div>
    </div>
</div>
@endsection
