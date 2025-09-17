<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DeductionSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $deductions = [
            'late_absences',
            'loans_advances',
            'sss_contribution',
            'phic_contribution',
            'hdmf_contribution',
            'sss_loan',
            'hdmf_loan',
            'tax',
        ];
    
        foreach ($deductions as $deduction) {
            \App\Models\DeductionSetting::firstOrCreate(
                ['deduction_type' => $deduction],
                ['settings' => null]
            );
        }
    }
    
}
