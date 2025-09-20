<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\EmployeeSchedule;
use PDF;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('n'));
        $editMode = $request->get('edit', false); // true kapag mag-eedit

        $settings = Schedule::where('year', $year)->first();

        return view('hr.schedule.index', [
            'year' => $year,
            'month' => $month,
            'settings' => $settings,
            'editMode' => $editMode
        ]);
    }

    public function save(Request $request)
    {
        $validated = $request->validate([
            'first_half_start' => 'nullable|integer',
            'first_half_end' => 'nullable|string',
            'second_half_start' => 'nullable|integer',
            'second_half_end' => 'nullable|string',
            'regular_start' => 'nullable',
            'regular_end' => 'nullable',
            'night_start' => 'nullable',
            'night_end' => 'nullable',
            'year' => 'required|integer'
        ]);
    
        $schedule = Schedule::firstOrNew(['year' => $validated['year']]);
        $schedule->fill($validated);
        $schedule->save();
    
        return redirect()->route('schedule.index', [
            'year' => $validated['year']
        ])->with('success', 'Schedule updated successfully.');
    }

    public function storeEmployeeSchedule(Request $request, $employeeId)
    {
        $validated = $request->validate([
            'weeks' => 'required|integer|min:1|max:4',
            'days_json' => 'required|string',
            'time_in' => 'required',
            'time_out' => 'required',
        ]);

        $schedule = Schedule::latest()->first();

        $days = json_decode($validated['days_json'], true);

        foreach ($days as $day) {
            EmployeeSchedule::create([
                'employee_id' => $employeeId,
                'schedule_id' => $schedule->id,
                'date' => $day['date'],
                'start_time' => $validated['time_in'],
                'end_time' => $validated['time_out'],
                'type' => $day['type'], // working o restday, depende sa pinili
            ]);
        }

        return back()->with('success', 'Schedule generated successfully.');
    }

    public function view($employeeId, $scheduleId)
    {
        $employee = \App\Models\Employee::with('department')->findOrFail($employeeId);
        $schedule = Schedule::findOrFail($scheduleId);
    
        // kunin lahat ng schedules sa schedule
        $schedules = EmployeeSchedule::where('employee_id', $employeeId)
            ->where('schedule_id', $scheduleId)
            ->orderBy('date')
            ->get();
    
        return view('schedules.file', compact('employee','schedule','schedules'));
    }

    public function print($id)
    {
        $schedule = Schedule::findOrFail($id);

        // render print view
        return view('schedules.print', compact('schedule'));
    }

    public function download($id)
    {
        $schedule = Schedule::findOrFail($id);

        $pdf = \PDF::loadView('schedules.print', compact('schedule'));
        return $pdf->download("schedule-{$schedule->id}.pdf");
    }
    
    
}