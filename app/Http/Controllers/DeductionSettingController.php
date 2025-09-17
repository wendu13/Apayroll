<?php

namespace App\Http\Controllers;

use App\Models\DeductionSetting;
use Illuminate\Http\Request;
use App\Models\SSSBracket;
use App\Models\TaxBracket;

class DeductionSettingController extends Controller
{
    public function index()
    {
        // Late & Absences
        $lateAbsence = DeductionSetting::where('deduction_type', 'late_absences')->first();
        if (!$lateAbsence) {
            $lateAbsence = new DeductionSetting();
            $lateAbsence->deduction_type = 'late_absences';
            $lateAbsence->settings = json_encode(['days' => 0]);
            $lateAbsence->save();
        }
        $lateAbsence->settings = json_decode($lateAbsence->settings, true);

        // Loan and Advances
        $loanAdvances = DeductionSetting::where('deduction_type', 'loan_advances')->first();
        if (!$loanAdvances) {
            $loanAdvances = new DeductionSetting();
            $loanAdvances->deduction_type = 'loan_advances';
            $loanAdvances->settings = json_encode([]);
            $loanAdvances->save();
        }
        $loanAdvances->settings = json_decode($loanAdvances->settings, true);
    
        // SSS - separate table
        $sssBrackets = SSSBracket::orderBy('to')->get();
    
        // PHIC
        $phicSetting = DeductionSetting::where('deduction_type', 'phic')->first();

        if (!$phicSetting) {
            $phicSetting = new DeductionSetting();
            $phicSetting->deduction_type = 'phic';
            $phicSetting->settings = json_encode([
                'rate' => 0,
                'min_salary' => 0,
                'max_salary' => 0,
                'employer_share' => 0,
                'employee_share' => 0,
            ]);
            $phicSetting->save();
        }

        // decode JSON to array for easy access in blade
        $phicSetting->settings = json_decode($phicSetting->settings, true);
    
        // HDMF
        $hdmfSetting = DeductionSetting::where('deduction_type', 'hdmf')->first();
        if (!$hdmfSetting) {
            $hdmfSetting = new DeductionSetting();
            $hdmfSetting->deduction_type = 'hdmf';
            $hdmfSetting->settings = json_encode([
                'employee' => 0,
                'employer' => 0,
            ]);
            $hdmfSetting->save();
        }
        $hdmfSetting->settings = json_decode($hdmfSetting->settings, true);

        // SSS Loan
        $sssLoanSetting = DeductionSetting::where('deduction_type', 'sss_loan')->first();
        if (!$sssLoanSetting) {
            $sssLoanSetting = new DeductionSetting();
            $sssLoanSetting->deduction_type = 'sss_loan';
            $sssLoanSetting->settings = json_encode([]);
            $sssLoanSetting->save();
        }
        $sssLoanSetting->settings = json_decode($sssLoanSetting->settings, true);

        // HDMF Loan
        $hdmfLoanSetting = DeductionSetting::where('deduction_type', 'hdmf_loan')->first();
        if (!$hdmfLoanSetting) {
            $hdmfLoanSetting = new DeductionSetting();
            $hdmfLoanSetting->deduction_type = 'hdmf_loan';
            $hdmfLoanSetting->settings = json_encode([]);
            $hdmfLoanSetting->save();
        }
        $hdmfLoanSetting->settings = json_decode($hdmfLoanSetting->settings, true);

        // Tax - separate table (no deduction_settings entry)
        $taxBrackets = TaxBracket::orderBy('from')->get();

        return view('hr.deductions.index', compact(
            'lateAbsence',
            'loanAdvances',
            'sssBrackets',
            'phicSetting',
            'hdmfSetting',
            'sssLoanSetting',
            'hdmfLoanSetting',
            'taxBrackets'
        ));
    }

    // update method remains the same (no tax handling)
    public function update(Request $request, DeductionSetting $deduction)
    {
        if ($deduction->deduction_type === 'late_absences') {
            $request->validate([
                'days' => 'required|numeric|min:0'
            ]);
    
            $deduction->settings = json_encode([
                'days' => $request->days
            ]);
    
            $deduction->save();
            $message = 'Late & Absences updated.';
    
        } elseif ($deduction->deduction_type === 'phic') {
            $request->validate([
                'rate' => 'required|numeric|min:0|max:100',
                'min_salary' => 'required|numeric|min:0',
                'max_salary' => 'required|numeric|min:0|gte:min_salary',
                'employer_share' => 'required|numeric|min:0|max:100',
                'employee_share' => 'required|numeric|min:0|max:100',
            ]);
        
            // optional: check na employer + employee = 100%
            if (($request->employer_share + $request->employee_share) !== 100) {
                return back()->withErrors([
                    'employer_share' => 'Employer + Employee share must equal 100%',
                    'employee_share' => 'Employer + Employee share must equal 100%',
                ]);
            }
        
            $deduction->settings = json_encode([
                'rate' => $request->rate,
                'min_salary' => $request->min_salary,
                'max_salary' => $request->max_salary,
                'employer_share' => $request->employer_share,
                'employee_share' => $request->employee_share,
            ]);
        
            $deduction->save();
            $message = 'PHIC Contribution updated.';     
    
        } elseif ($deduction->deduction_type === 'hdmf') {
            $request->validate([
                'employee' => 'required|numeric|min:0',
                'employer' => 'required|numeric|min:0',
            ]);
        
            $deduction->settings = json_encode([
                'employee' => $request->employee,
                'employer' => $request->employer,
            ]);
        
            $deduction->save();
            $message = 'HDMF Contribution updated.';
        }        
    
        return redirect()->route('deductions.index')->with('success', $message);
    }
}