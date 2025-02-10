<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Schedule;

class ScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $schedules = [
            [
                'monday_start' => '00:00',
                'monday_end' => '24:00',
                'tuesday_start' => '00:00',
                'tuesday_end' => '24:00',
                'wednesday_start' => '00:00',
                'wednesday_end' => '24:00',
                'thursday_start' => '00:00',
                'thursday_end' => '24:00',
                'friday_start' => '00:00',
                'friday_end' => '24:00',
                'saturday_start' => '00:00',
                'saturday_end' => '24:00',
                'sunday_start' => '00:00',
                'sunday_end' => '24:00',
            ],
            [
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
            ],
            [
                'monday_start' => '08:30',
                'monday_end' => '16:30',
                'tuesday_start' => '08:30',
                'tuesday_end' => '16:30',
                'wednesday_start' => '08:30',
                'wednesday_end' => '16:30',
                'thursday_start' => '08:30',
                'thursday_end' => '16:30',
                'friday_start' => '08:30',
                'friday_end' => '16:30',
                'saturday_start' => '08:00',
                'saturday_end' => '13:00',
                'sunday_start' => null,
                'sunday_end' => null,
            ],
        ];

        foreach ($schedules as $schedule) {
            Schedule::create($schedule);
        }

        $this->command->info('Banyak jadwal kerja berhasil ditambahkan!');
    }
}