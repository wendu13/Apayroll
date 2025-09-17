<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\EmployeeSchedule;
use App\Models\CutoffSchedule;
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

        $cutoff = CutoffSchedule::first();
        if (!$cutoff) {
            return back()->with('error', 'No cutoff schedule found. Please set up cutoff schedules first.');
        }

            // Current implementation
            $daysData = $validated['days_json'] ? json_decode($validated['days_json'], true) : [];

            if (empty($daysData)) {
                return back()->with('error', 'No days selected.');
            }

            // 1. Create schedule file
            $scheduleFile = ScheduleFile::create([
                'employee_id' => $employee->id,
                'cutoff_schedule_id' => $cutoff->id,
                'time_in' => $validated['time_in'],
                'time_out' => $validated['time_out'],
                'weeks' => $validated['weeks'],
                'days_json' => $validated['days_json'] ?? null, // Still raw JSON string
            ]);

            // 2. Process individual schedules
            $successCount = 0;
            foreach ($daysData as $day) {
            EmployeeSchedule::create([
                'employee_id' => $employee->id,
                'cutoff_schedule_id' => $cutoff->id,
                'schedule_file_id' => $scheduleFile->id, // link sa file
                'date' => $day['date'],
                'start_time' => $validated['time_in'],
                'end_time' => $validated['time_out'],
                'type' => $day['type'],
                'remarks' => $day['remarks'] ?? null,
            ]);
            $successCount++;
        }

        return back()->with('success', "Schedule created! {$successCount} day(s) saved for {$validated['weeks']} week(s).");
    }

    // View schedule group
    public function viewSchedule(Employee $employee, ScheduleFile $file)
    {
        $data = [
            'department'   => $employee->department,
            'employee_id'  => $employee->employee_number,
            'full_name'    => $employee->last_name . ', ' . $employee->first_name . ' ' . $employee->middle_name,
            'weeks'        => $file->weeks,
            'cutoff_label' => $file->cutoff?->label ?? 'No cutoff',
            'cutoff_year'  => $file->cutoff?->year ?? '',
            'time_in'      => \Carbon\Carbon::parse($file->time_in)->format('h:i A'),
            'time_out'     => \Carbon\Carbon::parse($file->time_out)->format('h:i A'),
            'dates'        => $file->days_json ?: [], // NEW WAY - no more json_decode needed
        ];

        return response()->json($data);
    }

    // Download JPEG
    public function download(Employee $employee, $id)
    {
        $file = ScheduleFile::where('employee_id', $employee->id)->findOrFail($id);
        $days = json_decode($file->days_json, true);

        $width = 1200;
        $height = 1700;
        $img = Image::canvas($width, $height, '#ffffff');
        $fontPath = public_path('fonts/arial.ttf');

        $startX = 100;
        $y = 90; // Moved header up

        // ===== HEADER =====
        $img->text("HR Department – Employee Schedule", $width/2, $y, function($font) use ($fontPath) {
            if(file_exists($fontPath)) $font->file($fontPath);
            $font->size(48);
            $font->color('#000000');
            $font->align('center');
        });
        $y += 100; // Reduced spacing after header

        // Format time with AM/PM
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

        // Legend with colored text in one line
        $y += 20;
        $img->text("Legend:", $startX, $y, function($font) use ($fontPath) {
            if(file_exists($fontPath)) $font->file($fontPath);
            $font->size(36);
            $font->color('#000000');
            $font->align('left');
        });
        $y += 50;

        // Combined legend text
        $img->text("Work Days", $startX, $y, function($font) use ($fontPath) {
            if(file_exists($fontPath)) $font->file($fontPath);
            $font->size(34);
            $font->color('#084298'); // Blue
            $font->align('left');
        });

        $img->text("Rest Days", $startX + 200, $y, function($font) use ($fontPath) {
            if(file_exists($fontPath)) $font->file($fontPath);
            $font->size(34);
            $font->color('#721c24'); // Red
            $font->align('left');
        });

        $y += 80; // Reduced spacing before calendar

        // ===== CALENDAR =====
        $dates = collect($days)->pluck('date')->toArray();
        $firstDate = !empty($dates) ? Carbon::parse($dates[0]) : Carbon::now();

        $calendarStart = $firstDate->copy()->startOfMonth()->startOfWeek(Carbon::SUNDAY);
        $calendarEnd = $firstDate->copy()->endOfMonth()->endOfWeek(Carbon::SATURDAY);

        $monthLabel = $firstDate->format('F Y');
        $img->text($monthLabel, $width/2, $y, function($font) use ($fontPath) {
            if(file_exists($fontPath)) $font->file($fontPath);
            $font->size(44);
            $font->color('#000000');
            $font->align('center');
        });
        $y += 30;

        // Calendar grid settings
        $calendarWidth = 1100;
        $cellWidth = $calendarWidth / 7;
        $cellHeight = 100;
        $gridStartX = ($width - $calendarWidth) / 2;

        // Draw weekday headers WITH grid lines
        $weekdays = ['Su','Mo','Tu','We','Th','Fr','Sa'];
        for ($i = 0; $i < 7; $i++) {
            $x = $gridStartX + ($i * $cellWidth);

            // Draw header cell background and border
            $img->rectangle($x, $y, $x + $cellWidth, $y + 50, function($draw) {
                $draw->background('#f8f9fa');
                $draw->border(2, '#333333');
            });

            // Draw weekday text centered
            $textX = $x + ($cellWidth / 2);
            $img->text($weekdays[$i], $textX, $y + 25, function($font) use ($fontPath) {
                if(file_exists($fontPath)) $font->file($fontPath);
                $font->size(36);
                $font->color('#000000');
                $font->align('center');
                $font->valign('middle');
            });
        }
        $y += 50;

        // Map scheduled days
        $scheduledDays = [];
        foreach ($days as $d) {
            $scheduledDays[$d['date']] = $d['type'];
        }

        // Draw calendar grid
        $currentDate = $calendarStart->copy();
        $row = 0;

        while ($currentDate <= $calendarEnd) {
            for ($col = 0; $col < 7; $col++) {
                $x = $gridStartX + ($col * $cellWidth);
                $cellY = $y + ($row * $cellHeight);

                $dateStr = $currentDate->format('Y-m-d');
                $dayNumber = $currentDate->format('j');
                $isCurrentMonth = $currentDate->month === $firstDate->month;

                // Determine colors
                if (!$isCurrentMonth) {
                    $bgColor = '#f8f9fa';
                    $textColor = '#adb5bd';
                } else {
                    $scheduleType = $scheduledDays[$dateStr] ?? null;

                    if ($scheduleType === 'regular') {
                        $bgColor = '#cfe2ff';
                        $textColor = '#084298';
                    } elseif ($scheduleType === 'restday') {
                        $bgColor = '#f8d7da';
                        $textColor = '#721c24';
                    } else {
                        $bgColor = '#ffffff';
                        $textColor = '#000000';
                    }
                }

                // Draw cell with grid lines
                $img->rectangle($x, $cellY, $x + $cellWidth, $cellY + $cellHeight, function($draw) use ($bgColor) {
                    $draw->background($bgColor);
                    $draw->border(2, '#333333');
                });

                // Draw day number centered
                $textX = $x + ($cellWidth / 2);
                $textY = $cellY + ($cellHeight / 2);

                $img->text($dayNumber, $textX, $textY, function($font) use ($fontPath, $textColor) {
                    if(file_exists($fontPath)) $font->file($fontPath);
                    $font->size(36);
                    $font->color($textColor);
                    $font->align('center');
                    $font->valign('middle');
                });

                $currentDate->addDay();
            }
            $row++;
        }

        $filename = $employee->last_name . '_' . $file->created_at->format('Y-m-d') . '.jpg';

        return $img->response('jpg')->withHeaders([
            'Content-Disposition'=>'attachment; filename="'.$filename.'"'
        ]);
    }

}
