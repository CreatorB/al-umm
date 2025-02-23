<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Schedule;

class UpdateScheduleTitles extends Command
{
    protected $signature = 'schedules:update-titles';
    protected $description = 'Update schedule titles based on time ranges';

    public function handle()
    {
        $this->info('Updating schedule titles...');

        Schedule::chunk(100, function ($schedules) {
            foreach ($schedules as $schedule) {
                // Contoh logika untuk membuat title yang lebih deskriptif
                $title = $this->generateTitle($schedule);
                $schedule->update(['title' => $title]);
            }
        });

        $this->info('Schedule titles updated successfully!');
    }

    private function generateTitle($schedule)
    {
        // Contoh logika untuk membuat title
        if ($schedule->monday_start) {
            return sprintf(
                'Schedule %d (%s-%s)', 
                $schedule->id,
                substr($schedule->monday_start, 0, 5),
                substr($schedule->monday_end, 0, 5)
            );
        }
        
        return "Schedule {$schedule->id}";
    }
}