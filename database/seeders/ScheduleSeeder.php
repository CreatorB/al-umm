<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Schedule;

class ScheduleSeeder extends Seeder
{

    public function run(): void
    {

        Schedule::create([
            'monday_start' => '08:00',
            'monday_end' => '16:00',
            'tuesday_start' => '08:00',
            'tuesday_end' => '16:00',
            'wednesday_start' => '08:00',
            'wednesday_end' => '16:00',
            'thursday_start' => '08:00',
            'thursday_end' => '16:00',
            'friday_start' => '08:00',
            'friday_end' => '16:00',
            'saturday_start' => '08:00',
            'saturday_end' => '13:00',
            'sunday_start' => null, 
            'sunday_end' => null, 
        ]);

        $this->command->info('Jadwal kerja berhasil ditambahkan!');
    }
}