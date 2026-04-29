<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Services\ProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Exception;

class ProfileController extends Controller
{
    protected $profileService;

    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    public function index()
    {
        return view('profile');
    }

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        try {
            $user = $this->profileService->updateProfile(Auth::id(), $request->validated());

            return response()->json([
                'status' => true,
                'message' => 'Profile updated successfully!',
                'data' => [
                    'user' => [
                        'name' => $user->name,
                        'email' => $user->email,
                        'initial' => strtoupper(substr($user->name, 0, 1))
                    ]
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to update profile. Please try again.',
                'data' => []
            ], 500);
        }
    }
}
