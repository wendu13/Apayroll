<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeSchedule extends Model
{
    use HasFactory;

    protected $table = 'employee_schedule'; // <-- eto yung nasa DB mo

    protected $fillable = [
        'employee_id',
        'schedule_id',
        'schedule_file_id',
        'date',
        'start_time',
        'end_time',
        'type',
    ];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function scheduleFile()
    {
        return $this->belongsTo(ScheduleFile::class);
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class, 'schedule_id');
    }
}

