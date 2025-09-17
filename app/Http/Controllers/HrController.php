<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HrController extends Controller
{
    public function dashboard()
    {
        return view('hr.dashboard');
    }

    public function requests()
    {
        // dito yung approve/reject request logic
    }
}
