<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ScheduleFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'schedule_id',
        'time_in',
        'time_out',
        'weeks',
        'days_json',
    ];

    protected $casts = [
        'days_json' => 'array', // Auto JSON conversion
        'time_in' => 'datetime:H:i',
        'time_out' => 'datetime:H:i',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class, 'schedule_id');
    }

    public function employeeSchedules()
    {
        return $this->hasMany(EmployeeSchedule::class, 'schedule_file_id');
    }

    // Helper: formatted days
    public function getFormattedDaysAttribute()
    {
        if (!$this->days_json) return [];

        return collect($this->days_json)->map(function ($day) {
            return [
                'date' => $day['date'],
                'type' => $day['type'],
                'formatted_date' => Carbon::parse($day['date'])->format('M d, Y'),
                'day_name' => Carbon::parse($day['date'])->format('l'),
            ];
        });
    }
}
