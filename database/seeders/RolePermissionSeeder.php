<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /**
         * Create roles
         */
        $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'api']);
        $userRole = Role::create(['name' => 'user', 'guard_name' => 'api']);

        /**
         * Create permissions
         */
        $editPermission = Permission::create(['name' => 'edit_tasks', 'guard_name' => 'api']);
        $viewPermission = Permission::create(['name' => 'view_tasks', 'guard_name' => 'api']);

        /**
         * Assign permissions to roles
         */
        $adminRole->givePermissionTo($editPermission, $viewPermission);
        $userRole->givePermissionTo($viewPermission);
    }
}
