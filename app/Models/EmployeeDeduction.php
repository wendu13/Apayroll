<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeDeduction extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'deduction_type',
        'custom_type',
        'amount',
        'term',
        'cut_off',
        'remaining_balance',
        'payments_made',
        'is_active',
        'start_date',
        'notes'
    ];

    protected $casts = [
        'start_date' => 'date',
        'amount' => 'decimal:2',
        'remaining_balance' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    // Calculate per payment amount
    public function getPerPaymentAmountAttribute()
    {
        return $this->term > 0 ? $this->amount / $this->term : 0;
    }

    // Get formatted deduction type
    public function getFormattedDeductionTypeAttribute()
    {
        $types = [
            'company_loan' => 'Company Loan',
            'cash_advance' => 'Cash Advance',
            'sss_loan' => 'SSS Loan',
            'hdmf_loan' => 'HDMF Loan',
            'other' => $this->custom_type ?? 'Other'
        ];

        return $types[$this->deduction_type] ?? 'Unknown';
    }

    // Get formatted cut off
    public function getFormattedCutOffAttribute()
    {
        return $this->cut_off === '1st_half' ? '1st Half' : '2nd Half';
    }

    // Check if deduction is completed
    public function getIsCompletedAttribute()
    {
        return $this->payments_made >= $this->term;
    }

    // Get progress percentage
    public function getProgressPercentageAttribute()
    {
        if ($this->term <= 0) return 0;
        return min(100, ($this->payments_made / $this->term) * 100);
    }

    // Scope for active deductions
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope for specific cut off
    public function scopeCutOff($query, $cutOff)
    {
        return $query->where('cut_off', $cutOff);
    }

    // Process payment (increment payments_made, update remaining_balance)
    public function processPayment()
    {
        if ($this->payments_made < $this->term) {
            $this->payments_made++;
            $this->remaining_balance = max(0, $this->amount - ($this->per_payment_amount * $this->payments_made));
            
            if ($this->payments_made >= $this->term) {
                $this->is_active = false;
                $this->remaining_balance = 0;
            }
            
            $this->save();
        }
    }
}