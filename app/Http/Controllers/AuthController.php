<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            if ($this->authService->login($request->validated())) {
                $request->session()->regenerate();

                return response()->json([
                    'status' => true,
                    'message' => 'Login successful! Redirecting...',
                    'data' => [
                        'redirect' => route('dashboard')
                    ]
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => 'The provided credentials do not match our records.',
                'data' => []
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred during login. Please try again.',
                'data' => []
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        $this->authService->logout($request);
        return redirect('/');
    }
}
