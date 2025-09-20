<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;
    
    protected $table = 'schedule'; // specify exact table name

    protected $fillable = [
        'year',
        'cutoff_half',
        'first_half_start',
        'first_half_end',
        'second_half_start',
        'second_half_end',
        'regular_start',
        'regular_end',
        'night_start',
        'night_end',
    ];

    public function employeeSchedules()
    {
        return $this->hasMany(EmployeeSchedule::class, 'schedule_id');
    }
}
