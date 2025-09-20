document.addEventListener('DOMContentLoaded', function () {
    // ---- ADD SCHEDULE CALENDAR ----
    const calendarEl   = document.getElementById('mini-calendar');
    const weeksSelect  = document.getElementById('weeks');
    const maxDaysLabel = document.getElementById('max-days-label');
    let activeType = 'working';
    
    const takenDates = window.employeeData?.takenDates || [];

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
        calendarEl.style.minHeight = '380px';
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
                height: 380,
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
                
                    // Mark taken dates (existing behavior)
                    if (takenDates.includes(dateStr)) {
                        arg.el.classList.add('taken');
                        arg.el.style.pointerEvents = 'none';
                        arg.el.style.opacity = '0.5';
                        arg.el.title = 'Already scheduled';
                    }
                
                    // Get the date number element
                    const dayNumberEl = arg.el.querySelector('.fc-daygrid-day-number');
                    if (!dayNumberEl) return;
                
                    // Make number bold
                    dayNumberEl.style.fontWeight = 'bold';
                
                    // Apply color based on highlight
                    if (arg.el.classList.contains('working') || arg.el.classList.contains('restday')) {
                        dayNumberEl.style.color = '#FFFFFF'; // white for highlighted dates
                    } else if (arg.el.classList.contains('taken')) {
                        dayNumberEl.style.color = '#666666'; // optional: grey for taken dates
                    } else {
                        dayNumberEl.style.color = '#000000'; // black for normal dates
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
            const employeeId = window.employeeData?.id || '';

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
            calendarEl.style.height = 'auto';
        } else {
            calendarEl.style.height = 'auto';
        }

        let firstDate = new Date(dates[0].date);

        if (calendarEl.fcCalendar) {
            calendarEl.fcCalendar.destroy();
        }

        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            initialDate: firstDate,
            height: 'auto',          // 🔹 full height auto
            contentHeight: 'auto',   // 🔹 content adjusts to weeks
            expandRows: true,        // 🔹 ensure rows fill properly
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