<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        Permission::create(['name' => 'create_users']);
        Permission::create(['name' => 'update_users']);
        Permission::create(['name' => 'view_users']);
        Permission::create(['name' => 'delete_users']);
 
        // create roles and assign created permissions

        // this can be done as separate statements
        $role = Role::create(['name' => 'superadmin']);
        $role->givePermissionTo(Permission::all());

        $role = Role::create(['name' => 'admin']);
        $role->givePermissionTo(['create_users', 'update_users', 'view_users']);

        $role = Role::create(['name' => 'hr']);
        $role->givePermissionTo(['create_users', 'update_users', 'view_users']);

        $role = Role::create(['name' => 'manager']);
        $role->givePermissionTo(['view_users']);

        // or may be done by chaining
        $role = Role::create(['name' => 'employee'])
            ->givePermissionTo(['update_users', 'view_users']);
    }
}
