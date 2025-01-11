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

        $this->call(PermitSeeder::class);
    }

    private function createUserWithRole(string $email, string $roleName)
    {
        $user = \App\Models\User::create([
            'name' => ucfirst($roleName),
            'email' => $email,
            'password' => bcrypt('bismillahi'),
            'status' => 'active',
            'working_days' => '27',
            'working_time_start' => '08:00',
            'working_time_end' => '16:00',
            'nip' => '1345121',
            'jumlah_cuti' => '3',
        ]);

        $role = Role::where('name', $roleName)->first();
        if ($role) {
            $user->assignRole($role);
        }
    }
}