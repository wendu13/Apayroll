<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\EmployeeSchedule;
use App\Models\Schedule;
use App\Models\ScheduleFile;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Intervention\Image\Facades\Image;
use Imagick;

class EmployeeScheduleController extends Controller
{
    public function store(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'weeks'     => 'required|integer|min:1|max:4',
            'time_in'   => 'required|date_format:H:i',
            'time_out'  => 'required|date_format:H:i',
            'days_json' => 'nullable|string',
        ]);
    
        $cutoff = Schedule::first();
        if (!$cutoff) {
            return back()->with('error', 'No schedule found. Please set up schedules first.');
        }
    
        $daysData = $validated['days_json'] ? json_decode($validated['days_json'], true) : [];
    
        if (empty($daysData)) {
            return back()->with('error', 'No days selected.');
        }
    
        // Save parent schedule file
        $scheduleFile = ScheduleFile::create([
            'employee_id' => $employee->id,
            'schedule_id' => $cutoff->id, // ✅ FIXED
            'time_in' => $validated['time_in'],
            'time_out' => $validated['time_out'],
            'weeks' => $validated['weeks'],
            'days_json' => $validated['days_json'] ?? null,
        ]);
    
        // Save individual employee schedules
        $successCount = 0;
        foreach ($daysData as $day) {
            EmployeeSchedule::create([
                'employee_id' => $employee->id,
                'schedule_id' => $cutoff->id,
                'schedule_file_id' => $scheduleFile->id,
                'date' => $day['date'],
                'start_time' => $validated['time_in'],
                'end_time' => $validated['time_out'],
                'type' => in_array($day['type'], ['work', 'rest']) ? $day['type'] : 'work',
                'remarks' => $day['remarks'] ?? null,
            ]);
            $successCount++;
        }
    
        return back()->with('success', "Schedule created! {$successCount} day(s) saved for {$validated['weeks']} week(s).");
    }

    // View schedule group
    public function viewSchedule(Employee $employee, ScheduleFile $file)
    {
        $daysData = json_decode($file->days_json, true) ?: [];
        
        // Group dates by month
        $datesByMonth = [];
        foreach ($daysData as $day) {
            $date = Carbon::parse($day['date']);
            $monthKey = $date->format('Y-m');
            if (!isset($datesByMonth[$monthKey])) {
                $datesByMonth[$monthKey] = [
                    'month_name' => $date->format('F Y'),
                    'dates' => []
                ];
            }
            $datesByMonth[$monthKey]['dates'][] = $day;
        }
        
        $data = [
            'department'     => $employee->department,
            'employee_id'    => $employee->employee_number,
            'full_name'      => $employee->last_name . ', ' . $employee->first_name . ' ' . $employee->middle_name,
            'weeks'          => $file->weeks,
            'schedule_half'  => $file->schedule?->cutoff_half ?? 'No schedule',
            'schedule_year'  => $file->schedule?->year ?? '',
            'time_in'        => \Carbon\Carbon::parse($file->time_in)->format('h:i A'),
            'time_out'       => \Carbon\Carbon::parse($file->time_out)->format('h:i A'),
            'dates'          => $daysData,
            'months'         => $datesByMonth,
        ];

        return response()->json($data);
    }

    // Download JPEG
    public function download(Employee $employee, $id)
    {
        $file = ScheduleFile::where('employee_id', $employee->id)->findOrFail($id);
        $days = json_decode($file->days_json, true);

        // Group days by month
        $daysByMonth = [];
        foreach ($days as $day) {
            $date = Carbon::parse($day['date']);
            $monthKey = $date->format('Y-m');
            if (!isset($daysByMonth[$monthKey])) {
                $daysByMonth[$monthKey] = [
                    'month_name' => $date->format('F Y'),
                    'first_date' => $date,
                    'days' => []
                ];
            }
            $daysByMonth[$monthKey]['days'][$day['date']] = $day['type'];
        }

        // Calculate image height based on number of months
        $monthCount = count($daysByMonth);
        $baseHeight = 1200;
        $calendarHeight = 800; // Height per calendar
        $totalHeight = $baseHeight + ($calendarHeight * $monthCount);

        $width = 1200;
        $img = Image::canvas($width, $totalHeight, '#ffffff');
        $fontPath = public_path('fonts/arial.ttf');

        $startX = 100;
        $y = 90;

        // ===== HEADER =====
        $img->text("HR Department – Employee Schedule", $width/2, $y, function($font) use ($fontPath) {
            if(file_exists($fontPath)) $font->file($fontPath);
            $font->size(48);
            $font->color('#000000');
            $font->align('center');
        });
        $y += 100;

        $timeIn = date('h:i A', strtotime($file->time_in));
        $timeOut = date('h:i A', strtotime($file->time_out));

        $headerInfo = [
            "Department: {$employee->department}",
            "Employee ID: {$employee->employee_number}",
            "Name: {$employee->last_name}, {$employee->first_name} {$employee->middle_name}",
            "Weeks: {$file->weeks}",
            "Time In: {$timeIn}",
            "Time Out: {$timeOut}",
        ];

        foreach ($headerInfo as $line) {
            $img->text($line, $startX, $y, function($font) use ($fontPath) {
                if(file_exists($fontPath)) $font->file($fontPath);
                $font->size(38);
                $font->color('#000000');
                $font->align('left');
            });
            $y += 60;
        }

        // Legend
        $y += 20;
        $img->text("Legend:", $startX, $y, function($font) use ($fontPath) {
            if(file_exists($fontPath)) $font->file($fontPath);
            $font->size(36);
            $font->color('#000000');
            $font->align('left');
        });
        $y += 50;

        $img->text("Work Days", $startX, $y, function($font) use ($fontPath) {
            if(file_exists($fontPath)) $font->file($fontPath);
            $font->size(34);
            $font->color('#0D47A1'); // dark blue
            $font->align('left');
        });

        $img->text("Rest Days", $startX + 200, $y, function($font) use ($fontPath) {
            if(file_exists($fontPath)) $font->file($fontPath);
            $font->size(34);
            $font->color('#B71C1C'); // dark red
            $font->align('left');
        });

        $y += 80;

        // ===== RENDER EACH MONTH'S CALENDAR =====
        foreach ($daysByMonth as $monthData) {
            $y = $this->renderMonthCalendar($img, $monthData, $y, $width, $fontPath);
            $y += 50; // Space between calendars
        }

        $filename = $employee->last_name . '_' . $file->created_at->format('Y-m-d') . '.jpg';

        return $img->response('jpg')->withHeaders([
            'Content-Disposition'=>'attachment; filename="'.$filename.'"'
        ]);
    }

    private function renderMonthCalendar($img, $monthData, $startY, $width, $fontPath)
    {
        $y = $startY;
        
        // Month title
        $img->text($monthData['month_name'], $width/2, $y, function($font) use ($fontPath) {
            if(file_exists($fontPath)) $font->file($fontPath);
            $font->size(44);
            $font->color('#000000');
            $font->align('center');
        });
        $y += 60;

        $calendarWidth = 1000;
        $cellWidth = $calendarWidth / 7;
        $cellHeight = 80;
        $gridStartX = ($width - $calendarWidth) / 2;

        // Week headers
        $weekdays = ['Su','Mo','Tu','We','Th','Fr','Sa'];
        for ($i = 0; $i < 7; $i++) {
            $x = $gridStartX + ($i * $cellWidth);
            $img->rectangle($x, $y, $x + $cellWidth, $y + 40, function($draw) {
                $draw->background('#f8f9fa');
                $draw->border(2, '#333333');
            });
            $textX = $x + ($cellWidth / 2);
            $img->text($weekdays[$i], $textX, $y + 20, function($font) use ($fontPath) {
                if(file_exists($fontPath)) $font->file($fontPath);
                $font->size(32);
                $font->color('#000000');
                $font->align('center');
                $font->valign('middle');
            });
        }
        $y += 40;

        // Calendar grid
        $firstDate = $monthData['first_date'];
        $calendarStart = $firstDate->copy()->startOfMonth()->startOfWeek(Carbon::SUNDAY);
        $calendarEnd = $firstDate->copy()->endOfMonth()->endOfWeek(Carbon::SATURDAY);

        $currentDate = $calendarStart->copy();
        $row = 0;

        while ($currentDate <= $calendarEnd) {
            for ($col = 0; $col < 7; $col++) {
                $x = $gridStartX + ($col * $cellWidth);
                $cellY = $y + ($row * $cellHeight);

                $dateStr = $currentDate->format('Y-m-d');
                $dayNumber = $currentDate->format('j');
                $isCurrentMonth = $currentDate->month === $firstDate->month;

                if (!$isCurrentMonth) {
                    $bgColor = '#f8f9fa';
                    $textColor = '#adb5bd';
                } else {
                    $scheduleType = $monthData['days'][$dateStr] ?? null;
                    if ($scheduleType === 'regular') {
                        $bgColor = '#0D47A1';
                        $textColor = '#FFFFFF';
                    } elseif ($scheduleType === 'restday') {
                        $bgColor = '#B71C1C';
                        $textColor = '#FFFFFF';
                    } else {
                        $bgColor = '#FFFFFF';
                        $textColor = '#000000';
                    }
                }

                $img->rectangle($x, $cellY, $x + $cellWidth, $cellY + $cellHeight, function($draw) use ($bgColor) {
                    $draw->background($bgColor);
                    $draw->border(2, '#333333');
                });

                $textX = $x + ($cellWidth / 2);
                $textY = $cellY + ($cellHeight / 2);

                $img->text($dayNumber, $textX, $textY, function($font) use ($fontPath, $textColor) {
                    if(file_exists($fontPath)) $font->file($fontPath);
                    $font->size(30);
                    $font->color($textColor);
                    $font->align('center');
                    $font->valign('middle');
                });

                $currentDate->addDay();
                
                if ($currentDate > $calendarEnd) break;
            }
            $row++;
        }

        return $y + ($row * $cellHeight);
    }

    public function destroy(Employee $employee, ScheduleFile $schedule)
    {
        // Delete related employee_schedule records first
        EmployeeSchedule::where('schedule_file_id', $schedule->id)->delete();
        
        // Delete the schedule file
        $schedule->delete();
        
        return back()->with('success', 'Schedule removed successfully!');
    }
}