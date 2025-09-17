<?php

// EmployeeSchedule.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'cutoff_schedule_id',
        'schedule_file_id', // Add this if you're linking to ScheduleFile
        'date',
        'start_time',
        'end_time',
        'type',
        'remarks',
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

    public function cutoff()
    {
        return $this->belongsTo(CutoffSchedule::class, 'cutoff_schedule_id');
    }

    public function scheduleFile()
    {
        return $this->belongsTo(ScheduleFile::class, 'schedule_file_id');
    }
}
