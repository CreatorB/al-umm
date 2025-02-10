<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\Department;
use App\Models\Part;
use App\Models\Schedule;

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
        $this->call(ScheduleSeeder::class);
        $this->call(RoleAndPermissionsSeeder::class);
        $this->call(PermitSeeder::class);

        $this->createUserWithRole('superadmin@syathiby.id', 'superadmin');
        $this->createUserWithRole('admin@syathiby.id', 'admin');
        $this->createUserWithRole('hr@syathiby.id', 'hr');
        $this->createUserWithRole('manager@syathiby.id', 'manager');
        $this->createUserWithRole('employee@syathiby.id', 'employee');

        User::factory(3)->create();
    }

    private function createUserWithRole(string $email, string $roleName)
    {
        $department = Department::first();
        $part = Part::first();
        $schedule = Schedule::first();


        $user = User::create([
            'name' => ucfirst($roleName),
            'email' => $email,
            'password' => bcrypt('bismillahi'),
            'status' => 'active',
            'schedule_id' => $schedule ? $schedule->id : null,
            'nip' => '1345121',
            'working_days' => '27',
            'jumlah_cuti' => '3',
            'jabatan_id' => $department ? $department->id : null,
            'bagian_id' => $part ? $part->id : null
        ]);

        User::whereNull('schedule_id')->update(['schedule_id' => $schedule->id]);

        $role = Role::where('name', $roleName)->first();
        if ($role) {
            $user->assignRole($role);
        }
    }
}