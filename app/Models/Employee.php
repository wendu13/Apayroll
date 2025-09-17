<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    // Allow these fields to be mass-assigned
    protected $fillable = [
        'department',
        'employee_number',
        'position',
        'last_name',
        'first_name',
        'middle_name',
        'address',
        'zip_code',
        'contact',
        'email',
        'phone',
        'birthplace',
        'birthdate',
        'age',
        'gender',
        'civil_status',
        'religion',
        'nationality',
        'sss',
        'philhealth',
        'pagibig',
        'tin',
        'workday',
        'restday',
        'bank_name',
        'bank_account',
        'basic_rate',
        'rate_type',   // ✅ idagdag ito
        'allowance',
        'other_pay',
        'photo',
    ];


    public function schedules()
    {
        return $this->hasMany(EmployeeSchedule::class);
    }

    public function scheduleFiles()
    {
        return $this->hasMany(ScheduleFile::class, 'employee_id');
    }

    public function getFullNameAttribute()
    {
        $middle = $this->middle_name ? ' ' . $this->middle_name : '';
        return "{$this->last_name}, {$this->first_name}{$middle}";
    }

    // Add deductions relationship
    public function deductions()
    {
        return $this->hasMany(EmployeeDeduction::class);
    }

    // Get active deductions
    public function activeDeductions()
    {
        return $this->hasMany(EmployeeDeduction::class)->where('is_active', true);
    }

    // Get deductions for specific cut off
    public function deductionsForCutOff($cutOff)
    {
        return $this->activeDeductions()->where('cut_off', $cutOff);
    }

    // Get total active deductions amount per cut off
    public function getTotalDeductionAmountForCutOff($cutOff)
    {
        return $this->deductionsForCutOff($cutOff)->sum('per_payment_amount');
    }

}


