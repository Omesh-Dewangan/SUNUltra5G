<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

class AuthService
{
    public function login(array $credentials)
    {
        try {
            if (Auth::attempt($credentials)) {
                Log::info('User Logged In', ['email' => $credentials['email']]);
                return true;
            }
            return false;
        } catch (Exception $e) {
            Log::error('Login Operation Failed', ['exception' => $e, 'email' => $credentials['email']]);
            throw $e;
        }
    }

    public function logout($request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        Log::info('User Logged Out');
    }
}
