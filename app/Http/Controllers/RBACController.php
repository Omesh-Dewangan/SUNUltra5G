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
     * Create New User from RBAC (AJAX)
     */
    public function storeUser(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role_id' => 'nullable|exists:roles,id'
        ]);

        try {
            // Since there is no UserRepository->create implemented yet in this context,
            // we will use the Eloquent Model directly for creating the user.
            $user = \App\Models\User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            ]);

            if ($request->filled('role_id')) {
                $this->rbacService->assignUserRole($user->id, $request->role_id);
            }

            return response()->json([
                'status' => true,
                'message' => 'User created successfully!'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to create user: ' . $e->getMessage()
            ], 500);
        }
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

    /**
     * Create New Role (AJAX)
     */
    public function storeRole(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name',
            'slug' => 'required|string|unique:roles,slug',
            'description' => 'nullable|string'
        ]);

        try {
            $this->roleRepository->create($request->only(['name', 'slug', 'description']));
            return response()->json(['status' => true, 'message' => 'Role created successfully!']);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => 'Failed to create role.'], 500);
        }
    }

    /**
     * Update Role (AJAX)
     */
    public function updateRole(Request $request, $id): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name,'.$id,
            'slug' => 'required|string|unique:roles,slug,'.$id,
            'description' => 'nullable|string'
        ]);

        try {
            $this->roleRepository->update($id, $request->only(['name', 'slug', 'description']));
            return response()->json(['status' => true, 'message' => 'Role updated successfully!']);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => 'Failed to update role.'], 500);
        }
    }

    /**
     * Delete Role (AJAX)
     */
    public function destroyRole($id): JsonResponse
    {
        try {
            // Prevent deleting critical roles
            $role = $this->roleRepository->findById($id);
            if ($role && in_array($role->slug, ['super_admin', 'admin'])) {
                return response()->json(['status' => false, 'message' => 'Cannot delete critical system roles.'], 403);
            }

            $this->roleRepository->delete($id);
            return response()->json(['status' => true, 'message' => 'Role deleted successfully!']);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => 'Failed to delete role.'], 500);
        }
    }
}
