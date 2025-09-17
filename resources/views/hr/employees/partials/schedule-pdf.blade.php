<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Schedule PDF</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; margin:0; padding:0; }
        .container { width: 25%; padding: 10px; /* 1/4 A4 */ }
        .header p { margin:2px 0; }
        table { border-collapse: collapse; width: 100%; font-size: 8px; }
        th, td { border: 1px solid #ccc; text-align: center; padding: 2px; }
        .highlight { background-color: #cfe2ff; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <p><strong>Department:</strong> {{ $employee->department }}</p>
            <p><strong>Employee ID:</strong> {{ $employee->employee_number }}</p>
            <p><strong>Full Name:</strong> {{ $employee->full_name }}</p>
            <p><strong>Weeks:</strong> {{ $file->weeks }}</p>
            <p><strong>Cutoff:</strong> {{ $file->cutoff?->label ?? 'No cutoff' }} - {{ $file->cutoff?->year ?? '' }}</p>
            <p><strong>Time In:</strong> {{ \Carbon\Carbon::parse($file->time_in)->format('h:i A') }}</p>
            <p><strong>Time Out:</strong> {{ \Carbon\Carbon::parse($file->time_out)->format('h:i A') }}</p>
            <p><strong>Legend:</strong> <span style="background:#cfe2ff;padding:2px 5px;">Blue</span> - Working days, 
                <span style="background:#f8d7da;padding:2px 5px;">Red</span> - Restday
            </p>
        </div>

        @php
            $dates = collect($days);
            $dateObjs = $dates->map(fn($d) => [
                'date' => \Carbon\Carbon::parse($d['date']),
                'type' => $d['type']
            ])->sortBy('date')->values();

            $first = $dateObjs->first()['date'] ?? now();
            $monthName = $first->format('F');
            $year = $first->year;

            $start = \Carbon\Carbon::create($year, $first->month, 1);
            $end = $start->copy()->endOfMonth();
            $dayRows = [];
            $week = [];
            for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
                $dayKey = $d->format('Y-m-d');
                $found = $dateObjs->firstWhere('date', $d);
                $week[$d->dayOfWeek] = [
                    'day' => $d->day,
                    'type' => $found['type'] ?? null
                ];
                if ($d->dayOfWeek == 6) {
                    $dayRows[] = $week;
                    $week = [];
                }
            }
            if (!empty($week)) $dayRows[] = $week;
        @endphp

        <h4 style="text-align:center;">{{ $monthName }} {{ $year }}</h4>

        <table>
            <thead>
                <tr>
                    <th>Sun</th><th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dayRows as $week)
                    <tr>
                        @for($i=0; $i<7; $i++)
                            @php
                                $dayInfo = $week[$i] ?? null;
                                $bgColor = $dayInfo['type'] ?? null;
                                if($bgColor) {
                                    $bgColor = $bgColor === 'regular' ? '#cfe2ff' : ($bgColor === 'restday' ? '#f8d7da' : '');
                                }
                            @endphp
                            <td style="background-color: {{ $bgColor }}">{{ $dayInfo['day'] ?? '' }}</td>
                        @endfor
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
