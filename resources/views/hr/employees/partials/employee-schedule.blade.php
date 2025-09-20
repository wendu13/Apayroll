<!-- resources/views/employees/schedule.blade.php -->
<div class="tab-pane fade" id="schedule" role="tabpanel">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 id="schedule-title">Employee Schedule</h5>
            <div>
                <button id="schedule-add-btn" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addScheduleModal">
                    + Add Schedule
                </button>

            </div>
        </div>

        <div class="card-body">
            <!-- RECORD MODE -->
            <div id="schedule-record">
                @if($employee->schedules->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Weeks</th>
                                    <th>Date Selected</th>
                                    <th>Cutoff</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                                <tbody>
                                    @foreach($employee->scheduleFiles as $file)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $file->weeks }} Week(s)</td>
                                            <td>{{ \Carbon\Carbon::parse($file->created_at)->format('F d, Y') }}</td>
                                            <td>{{ $file->schedule?->cutoff_half ?? 'No schedule' }} - {{ $file->schedule?->year ?? '' }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-info view-schedule-btn" data-group="{{ $file->id }}">View</button>
                                                <a href="{{ route('employees.schedules.download', [$employee->id, $file->id]) }}" class="btn btn-sm btn-success" target="_blank">Download</a>
                                                <form method="POST" action="{{ route('employees.schedules.destroy', [$employee, $file]) }}" style="display:inline;" onsubmit="return confirm('Are you sure?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">Remove</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">No schedule records yet.</p>
                @endif
            </div>

            <!-- VIEW MODE -->
            <div id="schedule-view" style="display:none;">
                <div class="row">
                    <div class="col-md-4">
                        <p><strong>Department:</strong> <span id="view-department"></span></p>
                        <p><strong>Employee ID:</strong> <span id="view-employee-id"></span></p>
                        <p><strong>Full Name:</strong> <span id="view-fullname"></span></p>
                        <p><strong>Weeks:</strong> <span id="view-weeks"></span></p>
                        <p><strong>Schedule:</strong> <span id="view-schedule"></span></p>
                        <p><strong>Time In:</strong> <span id="view-time-in"></span></p>
                        <p><strong>Time Out:</strong> <span id="view-time-out"></span></p>
                        
                        <!-- Legend -->
                        <div id="view-legend" class="mt-3">
                            <span class="badge bg-primary">Working Day (Blue)</span> 
                            <span class="badge bg-danger">Rest Day (Red)</span>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div id="view-calendars-container"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ADD SCHEDULE MODAL -->
<div class="modal fade" id="addScheduleModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form action="{{ route('employees.schedules.store', $employee->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Schedule</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body row">
                    <!-- Left: Options -->
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Weeks</label>
                            <select name="weeks" id="weeks" class="form-control" required>
                                <option value="1">1 Week</option>
                                <option value="2">2 Weeks</option>
                                <option value="3">3 Weeks</option>
                                <option value="4">4 Weeks</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Time In</label>
                            <input type="time" name="time_in" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Time Out</label>
                            <input type="time" name="time_out" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mark Days As:</label>
                            <div class="btn-group w-100">
                                <button type="button" id="btn-working" class="btn btn-outline-primary active">Working Day</button>
                                <button type="button" id="btn-rest" class="btn btn-outline-danger">Rest Day</button>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Calendar -->
                    <div class="col-md-9">
                        <div id="mini-calendar"></div>
                        <input type="hidden" name="days_json" id="days-json">
                        <small class="text-muted">
                            Select up to <span id="max-days-label">7</span> days (based on weeks).
                        </small>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Save Schedule</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<!-- FullCalendar -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.9/index.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.9/index.global.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // ---- ADD SCHEDULE CALENDAR ----
        const calendarEl   = document.getElementById('mini-calendar');
        const weeksSelect  = document.getElementById('weeks');
        const maxDaysLabel = document.getElementById('max-days-label');
        let activeType = 'working';
        @php
            $takenDates = $employee->scheduleFiles
                ->flatMap(fn($f) => $f->days_json ? json_decode($f->days_json, true) : [])
                ->pluck('date')
                ->values();
        @endphp

        const takenDates = @json($takenDates);

        // Toggle Working/Rest buttons
        document.getElementById('btn-working').addEventListener('click', function(){
            activeType = 'working';
            this.classList.add('active');
            document.getElementById('btn-rest').classList.remove('active');
        });
        document.getElementById('btn-rest').addEventListener('click', function(){
            activeType = 'restday';
            this.classList.add('active');
            document.getElementById('btn-working').classList.remove('active');
        });

        function getMaxDays() {
            const weeks = parseInt(weeksSelect.value || '1', 10);
            const max = weeks * 7;
            maxDaysLabel.textContent = max;
            return max;
        }

        // Helper function to apply styling
        function applyScheduleStyles() {
            calendarEl.querySelectorAll('.fc-day.working').forEach(el => {
                el.style.backgroundColor = '#0D47A1';
                el.style.color = '#084298';
            });
            calendarEl.querySelectorAll('.fc-day.restday').forEach(el => {
                el.style.backgroundColor = '#B71C1C';
                el.style.color = '#721c24';
            });
        }

        if (calendarEl) {
            // Ensure container has proper dimensions before creating calendar
            calendarEl.style.width = '100%';
            calendarEl.style.minHeight = '700px';
            calendarEl.style.visibility = 'visible';
            calendarEl.style.display = 'block';

            // Determine initial date from existing schedules
            let initialDate = new Date();
            if(takenDates.length > 0){
                initialDate = new Date(takenDates[0]);
            }

            // Use setTimeout to ensure DOM is fully ready
            setTimeout(() => {
                const calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    initialDate: initialDate,
                    selectable: true,
                    height: 'auto',
                    expandRows: true,
                    dayMaxEvents: false,
                    headerToolbar: { left: 'prev,next today', center: 'title', right: '' },
                    validRange: { start: new Date() },
                    viewDidMount: function() {
                        // Force re-render after view is mounted
                        setTimeout(() => {
                            calendar.updateSize();
                            applyScheduleStyles();
                        }, 50);
                    },
                    datesSet: function() {
                        // Apply styles when dates change
                        setTimeout(() => {
                            applyScheduleStyles();
                        }, 50);
                    },
                    dayCellDidMount: function(arg) {
                        const dateStr = arg.date.toISOString().slice(0,10);
                        if (takenDates.includes(dateStr)) {
                            arg.el.classList.add('taken');
                            arg.el.style.pointerEvents = 'none';
                            arg.el.style.opacity = '0.5';
                            arg.el.title = 'Already scheduled';
                        }
                    },
                    dateClick: function(info) {
                        const cell = info.dayEl.closest('.fc-daygrid-day');
                        if (!cell) return;

                        // Check if today or past date
                        const today = new Date();
                        today.setHours(0, 0, 0, 0);
                        const clickedDate = new Date(info.date);
                        if (clickedDate <= today) {
                            alert('Cannot select today or past dates');
                            return;
                        }

                        if (cell.classList.contains('working') || cell.classList.contains('restday')) {
                            cell.classList.remove('working','restday'); 
                            cell.style.backgroundColor=''; 
                            cell.style.color='';
                            updateDaysJson(); 
                            return;
                        }
                        if (takenDates.includes(info.dateStr)) {
                            alert('This date is already scheduled');
                            return;
                        }

                        const maxDays = getMaxDays();
                        const selectedCount = calendarEl.querySelectorAll('.fc-daygrid-day.working,.fc-daygrid-day.restday').length;
                        if (selectedCount >= maxDays) {
                            alert('Maximum days reached for selected weeks');
                            return;
                        }

                        if(activeType==='working'){ 
                            cell.classList.add('working'); 
                            cell.style.backgroundColor='#0D47A1';
                            cell.style.color='#084298';
                        } else { 
                            cell.classList.add('restday'); 
                            cell.style.backgroundColor='#B71C1C';
                            cell.style.color='#721c24';
                        }
                        updateDaysJson();
                    }
                });
                
                calendar.render();
                
                // Multiple attempts to ensure proper rendering
                setTimeout(() => {
                    calendar.updateSize();
                }, 100);
                
                setTimeout(() => {
                    calendar.updateSize();
                    applyScheduleStyles();
                }, 200);

                function updateDaysJson(){
                    const days=[];
                    calendarEl.querySelectorAll('.fc-daygrid-day').forEach(dayEl=>{
                        const date = dayEl.getAttribute('data-date');
                        if(!date) return;
                        if(dayEl.classList.contains('working')) days.push({date,type:'regular'});
                        else if(dayEl.classList.contains('restday')) days.push({date,type:'restday'});
                    });
                    document.getElementById('days-json').value=JSON.stringify(days);
                }
                
            }, 100); // Delay initial calendar creation
            
            getMaxDays();
            weeksSelect.addEventListener('change', getMaxDays);
        }

        // ---- VIEW SCHEDULE ----
        const recordDiv = document.getElementById('schedule-record');
        const viewDiv = document.getElementById('schedule-view');
        const addBtn   = document.getElementById('schedule-add-btn');

        document.addEventListener('click', function(e){
            if(e.target && e.target.matches('.view-schedule-btn')){
                const groupId = e.target.dataset.group;
                const employeeId = '{{ $employee->id }}';

                fetch(`/employees/${employeeId}/schedules/view/${groupId}`)
                    .then(res => res.json())
                    .then(data => {
                        document.getElementById('view-department').textContent = data.department;
                        document.getElementById('view-employee-id').textContent = data.employee_id;
                        document.getElementById('view-fullname').textContent = data.full_name;
                        document.getElementById('view-weeks').textContent = data.weeks;
                        document.getElementById('view-schedule').textContent = `${data.schedule_half} - ${data.schedule_year}`;
                        document.getElementById('view-time-in').textContent = data.time_in;
                        document.getElementById('view-time-out').textContent = data.time_out;

                        renderSeparateCalendars(data.months);

                        // Show/Hide divs and toggle Add button
                        recordDiv.style.display = 'none';
                        viewDiv.style.display = 'block';
                        addBtn.textContent = 'Close';
                        addBtn.classList.remove('btn-primary');
                        addBtn.classList.add('btn-secondary');
                        addBtn.removeAttribute('data-bs-toggle');
                        addBtn.removeAttribute('data-bs-target');

                        addBtn.onclick = () => {
                            viewDiv.style.display = 'none';
                            recordDiv.style.display = 'block';
                            addBtn.textContent = '+ Add Schedule';
                            addBtn.classList.remove('btn-secondary');
                            addBtn.classList.add('btn-primary');
                            addBtn.setAttribute('data-bs-toggle','modal');
                            addBtn.setAttribute('data-bs-target','#addScheduleModal');
                            addBtn.onclick = null;
                        }
                    });
            }
        });

        function renderSeparateCalendars(months)
        {
            const container = document.getElementById('view-calendars-container');
            container.innerHTML = '';

            const monthKeys = Object.keys(months);
            
            if (monthKeys.length === 1) {
                // Single calendar - center it
                const monthContainer = document.createElement('div');
                monthContainer.className = 'd-flex justify-content-center';
                monthContainer.innerHTML = `
                    <div class="single-calendar-container">
                        <div id="view-calendar-0" class="single-calendar"></div>
                    </div>
                `;
                container.appendChild(monthContainer);

                const calendarEl = monthContainer.querySelector('#view-calendar-0');
                renderSingleMonthCalendar(calendarEl, months[monthKeys[0]].dates, 0, true);
            } else {
                // Multiple calendars - side by side
                const rowContainer = document.createElement('div');
                rowContainer.className = 'd-flex flex-wrap justify-content-start';
                container.appendChild(rowContainer);

                monthKeys.forEach((monthKey, index) => {
                    const monthData = months[monthKey];
                    
                    const monthContainer = document.createElement('div');
                    monthContainer.className = 'month-calendar-small me-3 mb-3';
                    monthContainer.innerHTML = `
                        <div id="view-calendar-${index}" class="multi-calendar"></div>
                    `;
                    rowContainer.appendChild(monthContainer);

                    const calendarEl = monthContainer.querySelector(`#view-calendar-${index}`);
                    renderSingleMonthCalendar(calendarEl, monthData.dates, index, false);
                });
            }
        }

        function renderSingleMonthCalendar(calendarEl, dates, index, isSingle)
        {
            calendarEl.style.width = '100%';
            calendarEl.style.display = 'block';

            if (isSingle) {
                calendarEl.style.height = '400px';
            } else {
                calendarEl.style.height = '300px';
            }

            let firstDate = new Date(dates[0].date);

            if (calendarEl.fcCalendar) {
                calendarEl.fcCalendar.destroy();
            }

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                initialDate: firstDate,
                height: isSingle ? 400 : 300,
                aspectRatio: isSingle ? 1.35 : 1,
                headerToolbar: { left: '', center: 'title', right: '' },
                datesSet: function() {
                    // Apply colors to each day
                    setTimeout(() => {
                        calendarEl.querySelectorAll('.fc-day').forEach(dayEl => {
                            const dayISO = dayEl.getAttribute('data-date');
                            const found = dates.find(d => d.date === dayISO);
                            if(found){
                                dayEl.style.backgroundColor = found.type === 'regular' ? '#0D47A1' : '#B71C1C';
                                dayEl.style.color = '#FFFFFF';
                            } else {
                                dayEl.style.backgroundColor = '#FFFFFF';
                                dayEl.style.color = '#000000';
                            }
                        });
                    }, 50);
                },
                dayCellDidMount: function(arg){
                    const found = dates.find(d => d.date === arg.date.toISOString().slice(0,10));
                    if(found){
                        arg.el.style.backgroundColor = found.type === 'regular' ? '#0D47A1' : '#B71C1C';
                        arg.el.style.color = '#FFFFFF';
                    } else {
                        arg.el.style.backgroundColor = '#FFFFFF';
                        arg.el.style.color = '#000000';
                    }
                }
            });

            calendar.render();
            calendarEl.fcCalendar = calendar;

            // Ensure proper sizing after render
            setTimeout(() => {
                calendar.updateSize();
            }, 100);
        }

    });
</script>

<style>
/* MAIN ADD SCHEDULE CALENDAR - Full size no scroll */
#mini-calendar { 
    width: 100% !important;
    height: 100px !important;
    display: block !important;
}

#mini-calendar .fc { 
    width: 100% !important; 
    height: 100px !important;
}

#mini-calendar .fc-view-harness { 
    height: 100px !important; 
}

#mini-calendar .fc-daygrid-day-frame { 
    padding: 4px !important; 
    min-height: 30px !important;
}

/* VIEW MODE - Single calendar (centered) */
.single-calendar-container {
    width: 500px;
    margin: 0 auto;
}

.single-calendar { 
    width: 100% !important;
    height: 400px !important;
    display: block !important;
}

.single-calendar .fc { 
    width: 100% !important; 
    height: 400px !important;
}

.single-calendar .fc-view-harness { 
    height: 350px !important; 
}

.single-calendar .fc-daygrid-day-frame { 
    padding: 6px !important; 
    min-height: 45px !important;
}

.single-calendar .fc-daygrid-day-number {
    font-size: 14px !important;
}

.single-calendar .fc-col-header-cell {
    padding: 8px !important;
    font-size: 13px !important;
}

.single-calendar .fc-toolbar-title {
    font-size: 18px !important;
}

/* VIEW MODE - Multiple calendars side by side */
.multi-calendar { 
    width: 100% !important;
    height: 300px !important;
    display: block !important;
}

.multi-calendar .fc { 
    width: 100% !important; 
    height: 300px !important;
}

.multi-calendar .fc-view-harness { 
    height: 250px !important; 
}

.multi-calendar .fc-daygrid-day-frame { 
    padding: 4px !important; 
    min-height: 30px !important;
}

.multi-calendar .fc-daygrid-day-number {
    font-size: 12px !important;
}

.multi-calendar .fc-col-header-cell {
    padding: 4px !important;
    font-size: 11px !important;
}

.multi-calendar .fc-toolbar-title {
    font-size: 14px !important;
}

.multi-calendar .fc-toolbar {
    margin-bottom: 0.2em !important;
}

/* Container for side-by-side calendars */
.month-calendar-small {
    border: 1px solid #dee2e6;
    border-radius: 0.25rem;
    padding: 10px;
    background: #f8f9fa;
    width: 300px;
    flex-shrink: 0;
}

#view-calendars-container {
    max-height: none;
    overflow: visible;
}

/* Remove internal scrollbars from calendars */
.multi-calendar .fc-scroller,
.single-calendar .fc-scroller,
#mini-calendar .fc-scroller {
    overflow: hidden !important;
}

.multi-calendar .fc-daygrid-body,
.single-calendar .fc-daygrid-body,
#mini-calendar .fc-daygrid-body {
    overflow: hidden !important;
}

/* General calendar styling */
.fc-scrollgrid {
    border: 1px solid #ddd !important;
}

.fc-daygrid-day-frame { 
    padding: 2px !important; 
    min-height: 25px !important;
}

.fc-day.restday { 
    background-color: #B71C1C !important; 
    color: #721c24 !important; 
}

.fc-day.working { 
    background-color: #0D47A1 !important; 
    color: #084298 !important; 
}

.fc-day:hover { 
    cursor: pointer; 
    opacity: 0.8; 
}

.fc-daygrid-day.taken { 
    background: #eee; 
}

#view-legend {
    text-align: center;
}

#view-legend .badge {
    margin: 0 5px;
    font-size: 0.8em;
}
</style>
@endpush

