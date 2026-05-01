<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Database\Seeder;

class RBACSeeder extends Seeder
{
    public function run(): void
    {
        // Define Permissions
        $permissions = [
            ['name' => 'View Dashboard', 'slug' => 'view_dashboard'],
            ['name' => 'Manage Inventory', 'slug' => 'manage_inventory'],
            ['name' => 'Manage Orders', 'slug' => 'manage_orders'],
            ['name' => 'Manage Users', 'slug' => 'manage_users'],
            ['name' => 'Manage Dealers', 'slug' => 'manage_dealers'],
            ['name' => 'Manage Masters', 'slug' => 'manage_masters'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(['slug' => $permission['slug']], $permission);
        }

        // Define Roles
        $roles = [
            ['name' => 'Super Admin', 'slug' => 'super_admin', 'description' => 'System owner with full access.'],
            ['name' => 'Admin', 'slug' => 'admin', 'description' => 'Staff with administrative access.'],
            ['name' => 'Dealer', 'slug' => 'dealer', 'description' => 'External dealer/distributor.'],
            ['name' => 'Staff', 'slug' => 'staff', 'description' => 'Regular employee.'],
        ];

        foreach ($roles as $roleData) {
            $role = Role::updateOrCreate(['slug' => $roleData['slug']], $roleData);

            // Assign all permissions to Super Admin
            if ($role->slug === 'super_admin') {
                $role->permissions()->sync(Permission::all());
            }
        }

        // Assign Super Admin role to the first user if exists
        $user = User::first();
        if ($user) {
            $user->assignRole('super_admin');
        }
    }
}
