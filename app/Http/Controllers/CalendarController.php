<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Calendar;

class CalendarController extends Controller
{
    public function index()
    {
        $holidays = Calendar::orderBy('date')->get();
    
        $is_locked = Calendar::count() > 0; // 🔒 if there's data, disable further changes
    
        return view('hr.calendar.index', compact('holidays', 'is_locked'));
    }
   

    public function store(Request $request)
    {
        $holidays = $request->input('holidays', []);
    
        if (empty($holidays)) {
            return back()->withErrors(['No holidays to save.']);
        }
    
        $dates = [];
        foreach ($holidays as $index => $holiday) {
            $date = $holiday['date'];
    
            // ✅ Check for duplicate in the submitted holidays
            if (in_array($date, $dates)) {
                return back()->withErrors(["Duplicate date found in submission: $date"]);
            }
    
            $dates[] = $date;
    
            // ✅ Check if this date already exists in DB
            if (Calendar::where('date', $date)->exists()) {
                return back()->withErrors(["Holiday for date $date already exists in the calendar."]);
            }
        }
    
        // ✅ Passed all checks: save holidays
        foreach ($holidays as $holiday) {
            Calendar::create([
                'date' => $holiday['date'],
                'name' => $holiday['name'],
                'type' => $holiday['type'],
                'is_nationwide' => false,
            ]);
        }
    
        return redirect()->route('calendar.index')->with('success', 'Holidays saved successfully.');
    }
    

    public function reset()
    {
        Calendar::truncate(); // delete all
        return redirect()->back()->with('success', 'All holidays have been reset.');
    }
    
    
}
