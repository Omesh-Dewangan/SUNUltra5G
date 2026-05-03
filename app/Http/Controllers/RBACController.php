<?php

namespace App\Http\Controllers;

use App\Repositories\RoleRepository;
use App\Repositories\PermissionRepository;
use App\Repositories\UserRepository;
use App\Services\RBACService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;
use App\Traits\LogsActivity;

class RBACController extends Controller
{
    use LogsActivity;

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

            $this->logActivity('CREATE_USER', 'User', $user->id, ['email' => $user->email]);

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
     * Update User (AJAX)
     */
    public function updateUser(Request $request, string $encryptedId): JsonResponse
    {
        $id = decrypt($encryptedId);
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8',
        ]);

        try {
            $user = \App\Models\User::findOrFail($id);
            $user->name = $request->name;
            $user->email = $request->email;

            if ($request->filled('password')) {
                $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
            }
            $user->save();

            $this->logActivity('UPDATE_USER', 'User', $id, ['email' => $user->email]);

            return response()->json([
                'status' => true,
                'message' => 'User details updated successfully!'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to update user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete User (AJAX)
     */
    public function destroyUser(string $encryptedId): JsonResponse
    {
        try {
            $id = decrypt($encryptedId);
            $user = \App\Models\User::findOrFail($id);

            if ($user->id === auth()->id()) {
                return response()->json(['status' => false, 'message' => 'Cannot delete your own active account.'], 403);
            }
            if ($user->hasRole('super_admin')) {
                return response()->json(['status' => false, 'message' => 'Cannot delete a super admin.'], 403);
            }

            $this->logActivity('DELETE_USER', 'User', $id, ['email' => $user->email]);
            $user->delete();
            return response()->json(['status' => true, 'message' => 'User deleted successfully!']);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => 'Failed to delete user: ' . $e->getMessage()], 500);
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
            $this->logActivity('ASSIGN_ROLE', 'User', $request->user_id, ['role_id' => $request->role_id]);

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
            $this->logActivity('SYNC_PERMISSIONS', 'Role', $request->role_id, ['count' => count($request->permissions ?? [])]);

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
            $role = $this->roleRepository->create($request->only(['name', 'slug', 'description']));
            $this->logActivity('CREATE_ROLE', 'Role', $role->id, ['name' => $role->name]);

            return response()->json(['status' => true, 'message' => 'Role created successfully!']);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => 'Failed to create role.'], 500);
        }
    }

    /**
     * Update Role (AJAX)
     */
    public function updateRole(Request $request, string $encryptedId): JsonResponse
    {
        $id = decrypt($encryptedId);
        $request->validate([
            'name' => 'required|string|unique:roles,name,' . $id,
            'slug' => 'required|string|unique:roles,slug,' . $id,
            'description' => 'nullable|string'
        ]);

        try {
            $this->roleRepository->update($id, $request->only(['name', 'slug', 'description']));
            $this->logActivity('UPDATE_ROLE', 'Role', $id, ['name' => $request->name]);

            return response()->json(['status' => true, 'message' => 'Role updated successfully!']);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => 'Failed to update role.'], 500);
        }
    }

    /**
     * Delete Role (AJAX)
     */
    public function destroyRole(string $encryptedId): JsonResponse
    {
        try {
            $id = decrypt($encryptedId);
            // Prevent deleting critical roles
            $role = $this->roleRepository->findById($id);
            if (!$role) {
                return response()->json(['status' => false, 'message' => 'Role not found.'], 404);
            }

            if (in_array($role->slug, ['super_admin', 'admin'])) {
                return response()->json(['status' => false, 'message' => 'Cannot delete critical system roles.'], 403);
            }

            // Capture full role data for reversion/reference in logs
            $logData = [
                'name' => $role->name,
                'slug' => $role->slug,
                'description' => $role->description,
                'permissions' => $role->permissions->pluck('name')->toArray(),
                'permission_ids' => $role->permissions->pluck('id')->toArray(),
                'deleted_at' => now()->toDateTimeString()
            ];

            $this->logActivity('DELETE_ROLE', 'Role', $id, $logData);

            $this->roleRepository->delete($id);
            return response()->json(['status' => true, 'message' => 'Role deleted successfully! Details backed up in activity logs.']);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => 'Failed to delete role: ' . $e->getMessage()], 500);
        }
    }

    /**
     * View Activity Logs (AJAX/Page)
     */
    public function systemLogs()
    {
        $logs = \App\Models\ActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('rbac.logs', compact('logs'));
    }



    /**
     * Restore a deleted Resource (Role, Category, Unit, Product) from Log (AJAX)
     */
    public function restoreResource(string $encryptedLogId): JsonResponse
    {
        try {
            $logId = decrypt($encryptedLogId);
            $log = \App\Models\ActivityLog::findOrFail($logId);
            $action = $log->action;
            $data = $log->details;

            if (!str_starts_with($action, 'DELETE_')) {
                return response()->json(['status' => false, 'message' => 'This action cannot be restored.'], 400);
            }

            $message = "";
            switch ($action) {
                case 'DELETE_ROLE':
                    if (\App\Models\Role::where('slug', $data['slug'])->exists()) {
                        return response()->json(['status' => false, 'message' => 'A role with this slug already exists.'], 400);
                    }
                    $resource = \App\Models\Role::create([
                        'name' => $data['name'],
                        'slug' => $data['slug'],
                        'description' => $data['description'] ?? ''
                    ]);
                    if (!empty($data['permission_ids'])) {
                        $resource->permissions()->sync($data['permission_ids']);
                    }
                    $message = "Role '{$resource->name}' restored!";
                    break;

                case 'DELETE_CATEGORY':
                    // Check if slug still exists to prevent duplicates
                    if (isset($data['slug']) && \App\Models\Category::where('slug', $data['slug'])->exists()) {
                        return response()->json(['status' => false, 'message' => 'A category with this slug already exists.'], 400);
                    }
                    $resource = \App\Models\Category::create([
                        'name' => $data['name'],
                        'slug' => $data['slug'] ?? \Illuminate\Support\Str::slug($data['name']),
                        'description' => $data['description'] ?? null
                    ]);
                    $message = "Category '{$resource->name}' restored!";
                    break;

                case 'DELETE_UNIT':
                    $resource = \App\Models\Unit::create([
                        'name' => $data['name'],
                        'short_name' => $data['short_name'] ?? null
                    ]);
                    $message = "Unit '{$resource->name}' restored!";
                    break;

                case 'DELETE_PRODUCT':
                    if (\App\Models\Inventory::where('code', $data['code'])->exists()) {
                        return response()->json(['status' => false, 'message' => 'A product with this code already exists.'], 400);
                    }
                    $resource = \App\Models\Inventory::create($data);
                    $message = "Product '{$resource->name}' restored!";
                    break;

                default:
                    return response()->json(['status' => false, 'message' => 'Restoration logic not implemented for this resource.'], 400);
            }

            $this->logActivity('RESTORE_RESOURCE', $log->model, $resource->id, ['original_log_id' => $logId, 'action' => $action]);

            return response()->json([
                'status' => true,
                'message' => $message . " Details recovered from logs."
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => 'Restoration failed: ' . $e->getMessage()], 500);
        }
    }
}
