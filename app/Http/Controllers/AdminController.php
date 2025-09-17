<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hr;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    public function showPendingHR()
    {
        $pendingHrs = Hr::where('is_approved', false)->get();
        return view('admin.hr_approvals', compact('pendingHrs'));
    }

    public function approve($id)
    {
        $hr = Hr::findOrFail($id);
        $hr->is_approved = true;
        $hr->save();

        return back()->with('success', 'HR account approved.');
    }

    public function reject($id)
    {
        $hr = Hr::findOrFail($id);
        $hr->delete(); // or mark as rejected if you want to keep the record

        return back()->with('success', 'HR account rejected and deleted.');
    }

    public function approveHr($id)
    {
        $hr = Hr::findOrFail($id);
        $hr->is_approved = true;
        $hr->save();

        return back()->with('success', 'HR account approved.');
    }
}
