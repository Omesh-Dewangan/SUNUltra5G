<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Exception;

class ProfileService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function updateProfile(int $userId, array $data)
    {
        return DB::transaction(function () use ($userId, $data) {
            try {
                $updateData = [
                    'name' => $data['name'],
                    'email' => $data['email'],
                ];

                if (!empty($data['password'])) {
                    $updateData['password'] = Hash::make($data['password']);
                }

                $user = $this->userRepository->update($userId, $updateData);

                Log::info('User Profile Updated', ['user_id' => $userId]);

                return $user;
            } catch (Exception $e) {
                Log::error('Profile Update Failed', ['exception' => $e, 'user_id' => $userId]);
                throw $e;
            }
        });
    }
}
