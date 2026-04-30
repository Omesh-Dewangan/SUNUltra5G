<?php

namespace App\Http\Controllers;

use App\Repositories\RoleRepository;
use App\Repositories\PermissionRepository;
use App\Repositories\UserRepository;
use App\Services\RBACService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;

class RBACController extends Controller
{
    protected $rbacService;
    protected $roleRepository;
    protected $permissionRepository;
    protected $userRepository;

    public function __construct(
        RBACService $rbacService,
        RoleRepository $roleRepository,
        PermissionRepository $permissionRepository,
        UserRepository $userRepository
    ) {
        $this->rbacService = $rbacService;
        $this->roleRepository = $roleRepository;
        $this->permissionRepository = $permissionRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * User Role Management View
     */
    public function userRolesIndex()
    {
        // Note: For production, implement a dedicated UserRepository::getAllWithRoles()
        $users = \App\Models\User::with('roles')->paginate(10);
        $roles = $this->roleRepository->getAll();

        return view('rbac.users-roles', compact('users', 'roles'));
    }

    /**
     * Assign Role to User (AJAX)
     */
    public function assignRole(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_id' => 'required|exists:roles,id',
        ]);

        try {
            $this->rbacService->assignUserRole($request->user_id, $request->role_id);
            return response()->json([
                'status' => true,
                'message' => 'Role assigned successfully!'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to assign role.'
            ], 500);
        }
    }

    /**
     * Role Permission Management View
     */
    public function rolePermissionsIndex()
    {
        $roles = $this->roleRepository->getAll();
        $permissions = $this->permissionRepository->getAll();

        return view('rbac.roles-permissions', compact('roles', 'permissions'));
    }

    /**
     * Sync Permissions to Role (AJAX)
     */
    public function syncPermissions(Request $request): JsonResponse
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        try {
            $this->rbacService->syncRolePermissions($request->role_id, $request->permissions ?? []);
            return response()->json([
                'status' => true,
                'message' => 'Permissions updated successfully!'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to update permissions.'
            ], 500);
        }
    }
}
