<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(DepartmentsAndPartsSeeder::class);

        $this->call(RoleAndPermissionsSeeder::class);

        $this->createUserWithRole('superadmin@syathiby.id', 'superadmin');
        $this->createUserWithRole('admin@syathiby.id', 'admin');
        $this->createUserWithRole('hr@syathiby.id', 'hr');
        $this->createUserWithRole('manager@syathiby.id', 'manager');
        $this->createUserWithRole('employee@syathiby.id', 'employee');

        \App\Models\User::factory(3)->create();
    }

    private function createUserWithRole(string $email, string $roleName)
    {
        $user = \App\Models\User::create([
            'name' => ucfirst($roleName),
            'email' => $email,
            'password' => bcrypt('bismillahi'),
            'status' => 'active'
        ]);

        $role = Role::where('name', $roleName)->first();
        if ($role) {
            $user->assignRole($role);
        }
    }
}