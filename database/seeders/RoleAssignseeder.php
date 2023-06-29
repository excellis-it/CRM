<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAssignseeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adminRole = Role::create(['name' => 'ADMIN']);
        $managerRole = Role::create(['name' => 'MANAGER']);
        $teamLeadRole = Role::create(['name' => 'TEAMLEAD']);

        $permission = Permission::create(['name' => 'Role list']);
        $permission = Permission::create(['name' => 'Role edit']);
        $permission = Permission::create(['name' => 'Role update']);
        $permission = Permission::create(['name' => 'Role create']);
        $permission = Permission::create(['name' => 'Role delete']);

        $permission = Permission::create(['name' => 'User list']);
        $permission = Permission::create(['name' => 'User edit']);
        $permission = Permission::create(['name' => 'User create']);
        $permission = Permission::create(['name' => 'User delete']);

        $permission = Permission::create(['name' => 'Permission list']);
        $permission = Permission::create(['name' => 'Permission list-by-role']);
        $permission = Permission::create(['name' => 'Permission edit']);
        $permission = Permission::create(['name' => 'Permission update']);
        $permission = Permission::create(['name' => 'Permission create']);
        $permission = Permission::create(['name' => 'Permission delete']);
        $permission = Permission::create(['name' => 'Permission assign']);

        $permission = Permission::create(['name' => 'Project list']);
        $permission = Permission::create(['name' => 'Project edit']);
        $permission = Permission::create(['name' => 'Project update']);
        $permission = Permission::create(['name' => 'Project create']);
        $permission = Permission::create(['name' => 'Project delete']);
        $permission = Permission::create(['name' => 'Project assign']);
        
        $adminRole->givePermissionTo(Permission::all());

        $managerRole->givePermissionTo([
            'User list',
            'Project list',
        ]);
    }
}
