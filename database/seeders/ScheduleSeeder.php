<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Schedule;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // General schedule (default)
        Schedule::create([
            'year' => now()->year,
            'first_half_start' => 1,
            'first_half_end'   => 15,
            'second_half_start' => 16,
            'second_half_end'   => 30,
            'regular_start' => '08:00:00',
            'regular_end'   => '17:00:00',
            'night_start'   => '22:00:00',
            'night_end'     => '06:00:00',
        ]);
    }
}
