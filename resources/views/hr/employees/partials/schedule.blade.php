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
                                            <td>{{ $file->cutoff?->label ?? 'No cutoff' }} - {{ $file->cutoff?->year ?? '' }}</td>
                                            <td>
                                            <button class="btn btn-sm btn-info view-schedule-btn" data-group="{{ $file->id }}">View</button>

                                                <a href="{{ route('employees.schedules.download', [$employee->id, $file->id]) }}"
                                                class="btn btn-sm btn-success" target="_blank">
                                                    Download
                                                </a>
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
                        <p><strong>Cutoff:</strong> <span id="view-cutoff"></span></p>
                        <p><strong>Time In:</strong> <span id="view-time-in"></span></p>
                        <p><strong>Time Out:</strong> <span id="view-time-out"></span></p>
                    </div>
                    <div class="col-md-8">
                        <div id="view-calendar"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ADD SCHEDULE MODAL -->
<div class="modal fade" id="addScheduleModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('employees.schedules.store', $employee->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Schedule</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body row">
                    <!-- Left: Options -->
                    <div class="col-md-4">
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
                    <div class="col-md-8">
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
    const takenDates = @json($employee->schedules->pluck('date')->values());

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
            el.style.backgroundColor = '#cfe2ff';
            el.style.color = '#084298';
        });
        calendarEl.querySelectorAll('.fc-day.restday').forEach(el => {
            el.style.backgroundColor = '#f8d7da';
            el.style.color = '#721c24';
        });
    }

    if (calendarEl) {
        // Ensure container has proper dimensions before creating calendar
        calendarEl.style.width = '100%';
        calendarEl.style.minHeight = '350px';
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

                    if (cell.classList.contains('working') || cell.classList.contains('restday')) {
                        cell.classList.remove('working','restday'); 
                        cell.style.backgroundColor=''; 
                        cell.style.color='';
                        updateDaysJson(); 
                        return;
                    }
                    if (takenDates.includes(info.dateStr)) return;

                    const maxDays = getMaxDays();
                    const selectedCount = calendarEl.querySelectorAll('.fc-daygrid-day.working,.fc-daygrid-day.restday').length;
                    if (selectedCount >= maxDays) return;

                    if(activeType==='working'){ 
                        cell.classList.add('working'); 
                        cell.style.backgroundColor='#cfe2ff';
                        cell.style.color='#084298';
                    } else { 
                        cell.classList.add('restday'); 
                        cell.style.backgroundColor='#f8d7da';
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
                    document.getElementById('view-cutoff').textContent = `${data.cutoff_label} - ${data.cutoff_year}`;
                    document.getElementById('view-time-in').textContent = data.time_in;
                    document.getElementById('view-time-out').textContent = data.time_out;

                    // Add legend dynamically
                    if(!document.getElementById('view-legend')){
                        const legend = document.createElement('div');
                        legend.id = 'view-legend';
                        legend.innerHTML = `<span class="badge bg-primary">Working Day (Blue)</span> 
                                            <span class="badge bg-danger">Rest Day (Red)</span>`;
                        viewDiv.querySelector('.col-md-8').prepend(legend);
                    }

                    renderViewCalendar(data.dates);

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

    function renderViewCalendar(dates){
        const calendarEl = document.getElementById('view-calendar');
        calendarEl.innerHTML = ''; // clear old calendar

        // Ensure container has proper dimensions
        calendarEl.style.width = '100%';
        calendarEl.style.minHeight = '300px';
        calendarEl.style.visibility = 'visible';
        calendarEl.style.display = 'block';

        const events = dates.map(d => ({
            title: '●',
            start: d.date,
            allDay: true,
        }));

        // destroy previous calendar if exists
        if (calendarEl.fcCalendar) {
            calendarEl.fcCalendar.destroy();
        }

        // Set initial date based on first schedule
        let initialDate = new Date();
        if(dates.length > 0){
            initialDate = new Date(dates[0].date);
        }

        setTimeout(() => {
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                initialDate: initialDate,
                height: 200,
                expandRows: true,
                dayMaxEvents: false,
                headerToolbar: { left:'prev,next today', center:'title', right:'' },
                events: events,
                viewDidMount: function() {
                    // Apply colors when view is mounted
                    setTimeout(() => {
                        calendar.updateSize();
                        calendarEl.querySelectorAll('.fc-day').forEach(dayEl => {
                            const dayISO = dayEl.getAttribute('data-date');
                            const found = dates.find(d => d.date === dayISO);
                            if(found){
                                dayEl.style.backgroundColor = found.type === 'regular' ? '#cfe2ff' : '#f8d7da';
                                dayEl.style.color = found.type === 'regular' ? '#084298' : '#721c24';
                            }
                        });
                    }, 50);
                },
                datesSet: function() {
                    // Apply colors with delay for view calendar
                    setTimeout(() => {
                        calendarEl.querySelectorAll('.fc-day').forEach(dayEl => {
                            const dayISO = dayEl.getAttribute('data-date');
                            const found = dates.find(d => d.date === dayISO);
                            if(found){
                                dayEl.style.backgroundColor = found.type === 'regular' ? '#cfe2ff' : '#f8d7da';
                                dayEl.style.color = found.type === 'regular' ? '#084298' : '#721c24';
                            }
                        });
                    }, 50);
                },
                dayCellDidMount: function(arg){
                    const found = dates.find(d => d.date === arg.date.toISOString().slice(0,10));
                    if(found){
                        arg.el.style.backgroundColor = found.type === 'regular' ? '#cfe2ff' : '#f8d7da';
                        arg.el.style.color = found.type === 'regular' ? '#084298' : '#721c24';
                    }
                }
            });

            calendar.render();
            
            // Multiple attempts to ensure proper rendering for view calendar
            setTimeout(() => {
                calendar.updateSize();
            }, 100);
            
            setTimeout(() => {
                calendar.updateSize();
                calendarEl.querySelectorAll('.fc-day').forEach(dayEl => {
                    const dayISO = dayEl.getAttribute('data-date');
                    const found = dates.find(d => d.date === dayISO);
                    if(found){
                        dayEl.style.backgroundColor = found.type === 'regular' ? '#cfe2ff' : '#f8d7da';
                        dayEl.style.color = found.type === 'regular' ? '#084298' : '#721c24';
                    }
                });
            }, 200);
            
            calendarEl.fcCalendar = calendar; // save reference
        }, 100);
    }
});
</script>

<style>
#mini-calendar, #view-calendar { 
    width: 100% !important;
    min-height: 350px !important;
    display: block !important;
}

.fc { 
    width: 100% !important; 
    min-height: 350px !important;
}

.fc-view-harness { 
    min-height: 300px !important; 
}

.fc-daygrid { 
    width: 100% !important; 
}

.fc-scrollgrid {
    border: 1px solid #ddd !important;
}

.fc-daygrid-day-frame { 
    padding: 2px !important; 
    min-height: 40px !important;
}

.fc-day.restday { 
    background-color: #f8d7da !important; 
    color: #721c24 !important; 
}

.fc-day.working { 
    background-color: #cfe2ff !important; 
    color: #084298 !important; 
}

.fc-day:hover { 
    cursor: pointer; 
    opacity: 0.8; 
}

.fc-daygrid-day.taken { 
    background: #eee; 
}

#view-calendar .fc-event { 
    background: #0d6efd; 
    border: none; 
    border-radius: 50%; 
    width: 10px; 
    height: 10px; 
    line-height: 10px; 
    text-align: center; 
    font-size: 12px; 
}
</style>
@endpush