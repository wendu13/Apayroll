<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Models\Hr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Show login page
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Try Admin login
        $admin = Admin::where('email', $request->email)->first();
        if ($admin && Hash::check($request->password, $admin->password)) {
            session(['admin_id' => $admin->id]); // okay to leave this as is if not using guard
            return redirect('/admin/dashboard');
        }

        // Try HR login using guard
        if (Auth::guard('hr')->attempt($request->only('email', 'password'))) {
            $hr = Auth::guard('hr')->user();
            if (!$hr->is_approved) {
                Auth::guard('hr')->logout();
                return back()->withErrors(['email' => 'Your account is pending admin approval.']);
            }
            return redirect('/hr/dashboard');
        }

        return back()->withErrors(['email' => 'Invalid login credentials.']);
    }

    // Logout
    public function logout()
    {
        session()->flush();
        return redirect()->route('login');
    }

    // Show HR Registration Form
    public function showHrRegister()
    {
        return view('auth.register');
    }
    
    public function register(Request $request)
    {
        $request->validate([
            'employee_number' => 'required|unique:hrs',
            'position' => 'required',
            'department' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:hrs',
            'security_question' => 'required',
            'security_answer' => 'required',
            'password' => 'required|confirmed|min:6',
        ]);
    
        Hr::create([
            'employee_number' => $request->employee_number,
            'position' => $request->position,
            'department' => $request->department,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'security_question' => $request->security_question,
            'security_answer' => strtolower($request->security_answer),
            'password' => Hash::make($request->password),
            'is_approved' => false,
        ]);
    
        return redirect()->route('login')->with('success', 'Registration submitted. Wait for admin approval.');
    }
    
    // Show Forgot Password Form
    public function showForgot()
    {
        return view('auth.forgot');
    }

    // Process Reset Password
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:hrs,email',
            'security_question' => 'required',
            'security_answer' => 'required',
            'password' => 'required|confirmed|min:6'
        ]);

        $hr = Hr::where('email', $request->email)->first();

        if (
            $hr->security_question === $request->security_question &&
            strtolower($hr->security_answer) === strtolower($request->security_answer)
        ) {
            $hr->password = Hash::make($request->password);
            $hr->is_approved = false;
            $hr->save();

            return redirect()->route('login')->with('success', 'Password reset successfully.');
        }

        return back()->withErrors(['security_answer' => 'Incorrect security question or answer.']);
    }
}

