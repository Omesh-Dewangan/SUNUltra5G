<?php

namespace App\Traits;

use App\Models\Role;
use App\Models\Permission;

trait HasPermissions
{
    /**
     * Relationship with Roles
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole(string $roleSlug)
    {
        return $this->roles->contains('slug', $roleSlug);
    }

    /**
     * Check if user has a specific permission
     */
    public function hasPermission(string $permissionSlug)
    {
        return $this->roles->flatMap->permissions->contains('slug', $permissionSlug);
    }

    /**
     * Assign a role to user
     */
    public function assignRole(string $roleSlug)
    {
        $role = Role::where('slug', $roleSlug)->first();
        if ($role) {
            $this->roles()->syncWithoutDetaching([$role->id]);
        }
        return $this;
    }
}
