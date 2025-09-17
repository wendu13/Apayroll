@extends('layouts.hr')

{{-- Fixed header title --}}
@section('page-title', 'Calendar Management')

@section('hr-content')
<div class="container">
    <div class="row">
        {{-- LEFT SIDE --}}
        <div class="col-md-7">
            {{-- Subheading (optional, pwede mo burahin kung ayaw mo) --}}
            <h5 class="fw-bold mb-3">Annual Calendar (Holidays)</h5>

            {{-- Controls: Month Navigation --}}
            <div class="d-flex align-items-center gap-2 my-3">
                <button class="btn btn-outline-secondary btn-sm" id="prevMonth">&laquo; Prev</button>
                <button class="btn btn-outline-secondary btn-sm" id="nextMonth">Next &raquo;</button>

                <select class="form-select form-select-sm ms-2" id="monthSelect" style="width: 150px;">
                    @for ($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}">{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                    @endfor
                </select>

                <select class="form-select form-select-sm" id="yearSelect" style="width: 85px;">
                    @for ($y = 2020; $y <= 2030; $y++)
                        <option value="{{ $y }}" {{ $y == 2025 ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>

                <button class="btn btn-sm btn-dark ms-2" id="holidayBtn">Holiday (100%)</button>
                <button class="btn btn-sm btn-info text-white" id="specialBtn">Special (30%)</button>
            </div>

            {{-- Calendar Table --}}
            <table class="table table-bordered calendar-table text-center mx-auto" id="calendarTable" style="width: 100%;">
                <thead>
                    <tr class="table-primary">
                        <th>Sun</th><th>Mon</th><th>Tue</th><th>Wed</th>
                        <th>Thu</th><th>Fri</th><th>Sat</th>
                    </tr>
                </thead>
                <tbody id="calendarBody">
                    {{-- Will be filled by JS --}}
                </tbody>
            </table>

            {{-- Add Holiday Inputs --}}
            <div class="input-group mt-3">
                <input type="text" id="holidayNameInput" class="form-control form-control-sm" placeholder="Holiday Name">
                <button class="btn btn-sm btn-success" id="addHolidayBtn" disabled>Add</button>
            </div>
        </div>

        {{-- RIGHT SIDE --}}
        <div class="col-md-5">
            <form method="POST" action="{{ route('calendar.store') }}">
                <h5 class="fw-bold">Nationwide Holidays</h5>
                @csrf
                <table class="table table-sm table-bordered mt-2">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="holidayList">
                        @foreach ($holidays as $holiday)
                            <tr>
                                @if ($holiday->is_nationwide)
                                    <td><input type="date" class="form-control form-control-sm" value="{{ $holiday->date }}" readonly></td>
                                    <td><input type="text" class="form-control form-control-sm" value="{{ $holiday->name }}" readonly></td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" value="{{ $holiday->type }}" readonly>
                                    </td>
                                @else
                                    <td><input type="date" name="holidays[{{ $loop->index }}][date]" class="form-control form-control-sm" value="{{ $holiday->date }}"></td>
                                    <td><input type="text" name="holidays[{{ $loop->index }}][name]" class="form-control form-control-sm" value="{{ $holiday->name }}"></td>
                                    <td>
                                        <select name="holidays[{{ $loop->index }}][type]" class="form-select form-select-sm">
                                            <option value="Regular" {{ $holiday->type == 'Regular' ? 'selected' : '' }}>Holiday</option>
                                            <option value="Special" {{ $holiday->type == 'Special' ? 'selected' : '' }}>Special</option>
                                        </select>
                                    </td>
                                @endif
                                <td>
                                    @if (!$holiday->is_nationwide)
                                        <button type="button" class="btn btn-sm btn-danger remove-row">Delete</button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                @if (!$is_locked)
                    <button type="submit" class="btn btn-primary btn-sm mt-2 float-end"
                        onclick="return confirm('Once saved, holidays cannot be modified. Proceed?')">Save</button>
                @else
                    <div class="alert alert-warning mt-3">
                        Holidays are already saved. To modify, you must delete all existing holidays.
                    </div>
                @endif
            </form>

            <form action="{{ route('calendar.reset') }}" method="POST" class="mt-2">
                @csrf
                <button type="submit" class="btn btn-outline-danger btn-sm"
                    onclick="return confirm('Are you sure you want to reset the calendar? This cannot be undone.')">
                    Reset Calendar
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let currentDate = new Date();
    let currentMonth = currentDate.getMonth() + 1;
    let currentYear = currentDate.getFullYear();
    let selectedType = null; // "Regular" or "Special"
    let selectedDate = null;

    const monthSelect = document.getElementById('monthSelect');
    const yearSelect = document.getElementById('yearSelect');
    const calendarBody = document.getElementById('calendarBody');
    const holidayNameInput = document.getElementById('holidayNameInput');
    const addHolidayBtn = document.getElementById('addHolidayBtn');
    const holidayBtn = document.getElementById('holidayBtn');
    const specialBtn = document.getElementById('specialBtn');
    const holidayList = document.getElementById('holidayList');

    function generateCalendar(month, year) {
        calendarBody.innerHTML = '';
        const firstDay = new Date(year, month - 1, 1).getDay();
        const daysInMonth = new Date(year, month, 0).getDate();

        let date = 1;
        for (let i = 0; i < 6; i++) {
            const row = document.createElement('tr');
            for (let j = 0; j < 7; j++) {
                const cell = document.createElement('td');
                if (i === 0 && j < firstDay) {
                    cell.innerHTML = '';
                } else if (date > daysInMonth) {
                    break;
                } else {
                    cell.textContent = date;
                    cell.classList.add('calendar-cell');
                    cell.dataset.date = `${year}-${String(month).padStart(2, '0')}-${String(date).padStart(2, '0')}`;
                    cell.style.cursor = 'pointer';
                    cell.addEventListener('click', function () {
                        if (!selectedType) {
                            alert('Please select Holiday or Special first.');
                            return;
                        }

                        document.querySelectorAll('.calendar-cell').forEach(c => {
                            c.classList.remove('selected-holiday', 'selected-special');
                        });

                        this.classList.add(selectedType === 'Regular' ? 'selected-holiday' : 'selected-special');
                        selectedDate = this.dataset.date;
                        addHolidayBtn.disabled = false;
                    });

                    date++;
                }
                row.appendChild(cell);
            }
            calendarBody.appendChild(row);
        }
    }

    function resetButtonStyles() {
        holidayBtn.classList.remove('active');
        specialBtn.classList.remove('active');
    }

    holidayBtn.addEventListener('click', () => {
        selectedType = 'Regular';
        resetButtonStyles();
        holidayBtn.classList.add('active');
    });

    specialBtn.addEventListener('click', () => {
        selectedType = 'Special';
        resetButtonStyles();
        specialBtn.classList.add('active');
    });

    addHolidayBtn.addEventListener('click', () => {
    const name = holidayNameInput.value.trim();

    if (!selectedDate || !selectedType || !name) {
        alert('Complete the fields before adding.');
        return;
    }

    // ✅ Check for duplicates BEFORE adding to the list
    const existingDates = Array.from(holidayList.querySelectorAll('input[type="date"]'))
        .map(input => input.value);
    
    if (existingDates.includes(selectedDate)) {
        alert('This date is already added.');
        return;
    }

    const index = holidayList.querySelectorAll('tr').length;

    const row = document.createElement('tr');
    row.innerHTML = `
        <td><input type="date" name="holidays[${index}][date]" class="form-control form-control-sm" value="${selectedDate}" readonly></td>
        <td><input type="text" name="holidays[${index}][name]" class="form-control form-control-sm" value="${name}"></td>
        <td>
            <select name="holidays[${index}][type]" class="form-select form-select-sm">
                <option value="Regular" ${selectedType === 'Regular' ? 'selected' : ''}>Holiday</option>
                <option value="Special" ${selectedType === 'Special' ? 'selected' : ''}>Special</option>
            </select>
        </td>
        <td><button type="button" class="btn btn-sm btn-danger remove-row">Delete</button></td>
    `;

    holidayList.appendChild(row);

    // Reset form
    holidayNameInput.value = '';
    addHolidayBtn.disabled = true;
    selectedDate = null;
    });

    // Event delegation for delete buttons
    holidayList.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-row')) {
            e.target.closest('tr').remove();
        }
    });

    document.getElementById('prevMonth').addEventListener('click', () => {
        if (currentMonth === 1) {
            currentMonth = 12;
            currentYear--;
        } else {
            currentMonth--;
        }
        monthSelect.value = String(currentMonth);
        yearSelect.value = String(currentYear);
        generateCalendar(currentMonth, currentYear);
    });

    document.getElementById('nextMonth').addEventListener('click', () => {
        if (currentMonth === 12) {
            currentMonth = 1;
            currentYear++;
        } else {
            currentMonth++;
        }
        monthSelect.value = currentMonth;
        yearSelect.value = currentYear;
        generateCalendar(currentMonth, currentYear);
    });

    monthSelect.addEventListener('change', () => {
        currentMonth = parseInt(monthSelect.value);
        generateCalendar(currentMonth, currentYear);
    });

    yearSelect.addEventListener('change', () => {
        currentYear = parseInt(yearSelect.value);
        generateCalendar(currentMonth, currentYear);
    });

    // Initial render
        monthSelect.value = currentMonth;
        yearSelect.value = currentYear;
        generateCalendar(currentMonth, currentYear);
    });


console.log('Calendar script loaded');
</script>

<style>
    .selected-holiday {
        background-color: #032260 !important;
        color: white;
    }

    .selected-special {
        background-color: #add8e6 !important;
    }

    .calendar-table th,
    .calendar-table td {
        font-size: 0.8rem;
        padding: 4px;
        height: 50px;
        vertical-align: middle;
        text-align: center;
    }
</style>
@endpush

@endsection
