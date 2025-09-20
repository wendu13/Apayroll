<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Calendar;

class CalendarController extends Controller
{
    public function index()
    {
        $holidays = Calendar::orderBy('date')->get();
        $hasHolidays = $holidays->count() > 0;
    
        return view('hr.calendar.index', compact('holidays', 'hasHolidays'));
    }
    
    public function store(Request $request)
    {
        $holidays = $request->input('holidays', []);
    
        // 🧹 Linisin muna lahat
        Calendar::truncate();
    
        // ✅ Insert lahat ng nasa form (kung meron)
        foreach ($holidays as $holiday) {
            if (!empty($holiday['date']) && !empty($holiday['name']) && !empty($holiday['type'])) {
                Calendar::create([
                    'date' => $holiday['date'],
                    'name' => $holiday['name'],
                    'type' => $holiday['type'],
                ]);
            }
        }
    
        return redirect()->back()->with('success', 'Holidays updated successfully.');

    }
    
    public function reset()
    {
        Calendar::truncate();
        return redirect()->back()->with('success', 'All holidays have been reset.');
    }
    
    
}
