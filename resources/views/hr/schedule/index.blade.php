@extends('layouts.hr')

@section('page-title', 'Cutoff Schedule')

@section('hr-content')
<div class="container mt-3">
    <form method="POST" action="{{ route('schedule.save') }}">
        @csrf
        <input type="hidden" name="year" value="{{ $year }}">

        <div class="row">
            {{-- LEFT: Calendar --}}
            <div class="col-md-7">
                <h5 class="mb-2">Cutoff Calendar</h5>
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <a href="{{ route('schedule.index', ['year' => $month == 1 ? $year - 1 : $year, 'month' => $month == 1 ? 12 : $month - 1]) }}" class="btn btn-light btn-sm">&laquo; Prev</a>
                    <strong>{{ \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y') }}</strong>
                    <a href="{{ route('schedule.index', ['year' => $month == 12 ? $year + 1 : $year, 'month' => $month == 12 ? 1 : $month + 1]) }}" class="btn btn-light btn-sm">Next &raquo;</a>
                </div>
                @php
                    $daysInMonth = \Carbon\Carbon::createFromDate($year, $month, 1)->daysInMonth;
                    $firstDayOfMonth = \Carbon\Carbon::createFromDate($year, $month, 1)->dayOfWeek;
                    $day = 1;
                @endphp
                <table class="table table-bordered table-sm text-center align-middle mb-0" id="calendar-table">
                    <thead class="table-light">
                        <tr>
                            <th>Sun</th><th>Mon</th><th>Tue</th><th>Wed</th>
                            <th>Thu</th><th>Fri</th><th>Sat</th>
                        </tr>
                    </thead>
                    <tbody>
                    @for ($row = 0; $row < 6; $row++)
                        <tr>
                            @for ($col = 0; $col < 7; $col++)
                                @if ($row == 0 && $col < $firstDayOfMonth)
                                    <td></td>
                                @elseif ($day > $daysInMonth)
                                    <td></td>
                                @else
                                    <td data-day="{{ $day }}">{{ $day }}</td>
                                    @php $day++; @endphp
                                @endif
                            @endfor
                        </tr>
                    @endfor
                    </tbody>
                </table>
            </div>

            {{-- RIGHT: Cutoff & Work Times --}}
            {{-- Determine mode --}}
            @php
                $isNew = !$settings; // true if first time
                $editing = $editMode || $isNew;
            @endphp

            <div class="col-md-5">
                <h5 class="mb-2">Cutoff Settings</h5>

                @if($editing)
                    {{-- Editable form --}}
                    <div class="row mb-2">
                        <div class="col">
                            <label class="small">1st Start</label>
                            <select id="first_half_start" name="first_half_start" class="form-select form-select-sm">
                                <option value="">--</option>
                                @for($i=1; $i<=31; $i++)
                                    <option value="{{ $i }}" {{ ($settings->first_half_start ?? '') == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col">
                            <label class="small">1st End</label>
                            <select id="first_half_end" name="first_half_end" class="form-select form-select-sm">
                                <option value="">--</option>
                                @for($i=1; $i<=31; $i++)
                                    <option value="{{ $i }}" {{ ($settings->first_half_end ?? '') == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                                <option value="end" {{ ($settings->first_half_end ?? '') == 'end' ? 'selected' : '' }}>End</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col">
                            <label class="small">2nd Start</label>
                            <select id="second_half_start" name="second_half_start" class="form-select form-select-sm">
                                <option value="">--</option>
                                @for($i=1; $i<=31; $i++)
                                    <option value="{{ $i }}" {{ ($settings->second_half_start ?? '') == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col">
                            <label class="small">2nd End</label>
                            <select id="second_half_end" name="second_half_end" class="form-select form-select-sm">
                                <option value="">--</option>
                                @for($i=1; $i<=31; $i++)
                                    <option value="{{ $i }}" {{ ($settings->second_half_end ?? '') == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                                <option value="end" {{ ($settings->second_half_end ?? '') == 'end' ? 'selected' : '' }}>End</option>
                            </select>
                        </div>
                    </div>

                    <h5 class="mt-3">Work Times</h5>
                    @php
                        $timeOptions = [];
                        for($h=0; $h<24; $h++){
                            for($m=0; $m<60; $m+=30){
                                $timeOptions[] = sprintf('%02d:%02d', $h, $m);
                            }
                        }
                    @endphp

                    <div class="mb-1">
                    {{-- Regular Start --}}
                        <label class="small">Regular Start</label>
                        <select name="regular_start" class="form-select form-select-sm">
                            <option value="">--</option>
                            @foreach($timeOptions as $time)
                                <option value="{{ $time }}" 
                                    {{ (old('regular_start') ?? ($settings->regular_start ?? '')) == $time ? 'selected' : '' }}>
                                    {{ $time }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Regular End --}}
                    <div class="mb-1">
                        <label class="small">Regular End</label>
                        <select name="regular_end" class="form-select form-select-sm">
                            <option value="">--</option>
                            @foreach($timeOptions as $time)
                                <option value="{{ $time }}" 
                                    {{ (old('regular_end') ?? ($settings->regular_end ?? '')) == $time ? 'selected' : '' }}>
                                    {{ $time }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Night Start --}}
                    <div class="mb-1">
                        <label class="small">Night Start</label>
                        <select name="night_start" class="form-select form-select-sm">
                            <option value="">--</option>
                            @foreach($timeOptions as $time)
                                <option value="{{ $time }}" 
                                    {{ (old('night_start') ?? ($settings->night_start ?? '')) == $time ? 'selected' : '' }}>
                                    {{ $time }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Night End --}}
                    <div class="mb-2">
                        <label class="small">Night End</label>
                        <select name="night_end" class="form-select form-select-sm">
                            <option value="">--</option>
                            @foreach($timeOptions as $time)
                                <option value="{{ $time }}" 
                                    {{ (old('night_end') ?? ($settings->night_end ?? '')) == $time ? 'selected' : '' }}>
                                    {{ $time }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- BUTTONS --}}
                    <div class="d-flex gap-2 mt-3">
                        <button type="submit" class="btn btn-primary btn-sm px-3">Save</button>
                        @if(!$isNew)
                        <button type="button" class="btn btn-secondary btn-sm px-3" 
                            onclick="window.location.href='{{ route('schedule.index', ['year' => $year, 'month' => $month]) }}'">
                            Cancel
                        </button>
                        @endif
                    </div>
                @else
                    {{-- View Mode --}}
                    <div class="mb-2 small" 
                        data-fs="{{ $settings->first_half_start }}" 
                        data-fe="{{ $settings->first_half_end }}" 
                        data-ss="{{ $settings->second_half_start }}" 
                        data-se="{{ $settings->second_half_end }}">
                        <strong>Cutoff:</strong><br>
                        1st: {{ $settings->first_half_start }} - {{ $settings->first_half_end }}<br>
                        2nd: {{ $settings->second_half_start }} - {{ $settings->second_half_end }}<br><br>
                        <strong>Work Times:</strong><br>
                        Regular: {{ $settings->regular_start }} - {{ $settings->regular_end }}<br>
                        Night: {{ $settings->night_start }} - {{ $settings->night_end }}
                    </div>
                    <a href="{{ route('schedule.index', ['year' => $year, 'month' => $month, 'edit' => 1]) }}" class="btn btn-primary btn-sm">Edit</a>
                @endif
            </div>
        </div>
    </form>
</div>

{{-- Calendar Highlight Script --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const updateHighlight = (fs, fe, ss, se) => {
        document.querySelectorAll('#calendar-table td[data-day]').forEach(td => {
            const day = parseInt(td.dataset.day);
            td.style.backgroundColor = '';
            td.style.color = '';

            if (fs && fe && day >= fs && day <= fe) {
                td.style.backgroundColor = '#1e3a8a';
                td.style.color = 'white';
            }
            if (ss && se && day >= ss && day <= se) {
                td.style.backgroundColor = '#3b82f6';
                td.style.color = 'white';
            }
        });
    };

    // Edit Mode
    if (document.getElementById('first_half_start')) {
        const getVal = id => {
            const el = document.getElementById(id);
            if (!el) return null;
            const val = el.value;
            return val === 'end' ? 31 : (parseInt(val) || null);
        };

        ['first_half_start','first_half_end','second_half_start','second_half_end'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.addEventListener('change', () => {
                updateHighlight(getVal('first_half_start'), getVal('first_half_end'), getVal('second_half_start'), getVal('second_half_end'));
            });
        });

        updateHighlight(getVal('first_half_start'), getVal('first_half_end'), getVal('second_half_start'), getVal('second_half_end'));
    } 
    // View Mode
    else {
        const viewBox = document.querySelector('[data-fs]');
        if (viewBox) {
            const fs = parseInt(viewBox.dataset.fs) || null;
            const fe = viewBox.dataset.fe === 'end' ? 31 : (parseInt(viewBox.dataset.fe) || null);
            const ss = parseInt(viewBox.dataset.ss) || null;
            const se = viewBox.dataset.se === 'end' ? 31 : (parseInt(viewBox.dataset.se) || null);
            updateHighlight(fs, fe, ss, se);
        }
    }

    // Calendar highlight – huwag alisin kahit view mode
    function renderCalendar(selectedDates, viewMode = false) {
        // ... existing render logic ...
        selectedDates.forEach(date => {
            const cell = document.querySelector(`[data-date="${date}"]`);
            if (cell) {
                cell.classList.add('highlight');
            }
        });
    }

    let editMode = false;
    let originalValues = {};

    function enableEditMode() {
        editMode = true;

        // Save original values
        document.querySelectorAll('[data-field]').forEach(input => {
            originalValues[input.name] = input.value;
            input.removeAttribute('readonly');
        });

        document.getElementById('editBtn').style.display = 'none';
        document.getElementById('saveBtn').style.display = 'inline-block';
        document.getElementById('cancelBtn').style.display = 'inline-block';
    }

    function cancelEditMode() {
        editMode = false;

        // Restore original values
        document.querySelectorAll('[data-field]').forEach(input => {
            input.value = originalValues[input.name];
            input.setAttribute('readonly', true);
        });

        document.getElementById('editBtn').style.display = 'inline-block';
        document.getElementById('saveBtn').style.display = 'none';
        document.getElementById('cancelBtn').style.display = 'none';
    }
    
});
</script>

@endsection
