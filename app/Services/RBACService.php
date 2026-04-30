<?php

namespace App\Services;

use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class RBACService
{
    protected $roleRepository;
    protected $userRepository;

    public function __construct(RoleRepository $roleRepository, UserRepository $userRepository)
    {
        $this->roleRepository = $roleRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Assign a role to a user
     */
    public function assignUserRole(int $userId, int $roleId)
    {
        return DB::transaction(function () use ($userId, $roleId) {
            try {
                $user = $this->userRepository->findById($userId);
                if (!$user) throw new Exception("User not found.");

                $user->roles()->sync([$roleId]);

                Log::info('User Role Assigned', ['user_id' => $userId, 'role_id' => $roleId]);
                return true;
            } catch (Exception $e) {
                Log::error('Role Assignment Failed', ['exception' => $e]);
                throw $e;
            }
        });
    }

    /**
     * Sync permissions to a role
     */
    public function syncRolePermissions(int $roleId, array $permissionIds)
    {
        return DB::transaction(function () use ($roleId, $permissionIds) {
            try {
                $role = $this->roleRepository->findById($roleId);
                if (!$role) throw new Exception("Role not found.");

                $role->permissions()->sync($permissionIds);

                Log::info('Role Permissions Synced', ['role_id' => $roleId, 'permissions' => $permissionIds]);
                return true;
            } catch (Exception $e) {
                Log::error('Permission Sync Failed', ['exception' => $e]);
                throw $e;
            }
        });
    }
}
