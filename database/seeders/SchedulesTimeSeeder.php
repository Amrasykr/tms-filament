<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SchedulesTimeSeeder extends Seeder
{
    public function run(): void
    {
        $times = [
            ['08:00:00', '09:20:00'],
            ['10:00:00', '11:20:00'],
            ['12:30:00', '13:50:00'],
        ];

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        $now = Carbon::now();

        foreach ($days as $day) {
            foreach ($times as [$start, $end]) {
                DB::table('schedules_times')->insert([
                    'day' => $day,
                    'start_time' => $start,
                    'end_time' => $end,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }
}
