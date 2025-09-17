@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 800px;">
    <div class="card shadow">
        <div class="card-body" style="padding:20px;">

            <!-- Header Info -->
            <div class="row mb-4">
                <div class="col-6">
                    <h6>Employee ID: <strong>{{ $employee->id }}</strong></h6>
                    <h6>Department: <strong>{{ $employee->department->name ?? 'N/A' }}</strong></h6>
                    <h6>Name: <strong>{{ $employee->fullname }}</strong></h6>
                </div>
                <div class="col-6 text-end">
                    <h6>Cutoff Period</h6>
                    <p>{{ $cutoff->first_half_start }} - {{ $cutoff->second_half_end }} {{ $cutoff->month }}/{{ $cutoff->year }}</p>
                </div>
            </div>

            <!-- Details + Calendar -->
            <div class="row">
                <!-- Left Side -->
                <div class="col-4">
                    <p><strong>Time In:</strong> {{ $schedules->first()->start_time ?? '-' }}</p>
                    <p><strong>Time Out:</strong> {{ $schedules->first()->end_time ?? '-' }}</p>
                    <p><span class="badge bg-primary">Regular = Blue</span></p>
                    <p><span class="badge bg-danger">Restday = Red</span></p>
                </div>

                <!-- Right Side Calendar -->
                <div class="col-8">
                    <div id="print-calendar"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.9/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('print-calendar');

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        height: 400,
        headerToolbar: { left: '', center: 'title', right: '' },
        selectable: false,
        events: [
            @foreach($schedules as $sched)
                {
                    title: "{{ $sched->type === 'regular' ? 'Work' : 'Rest' }}",
                    start: "{{ $sched->date }}",
                    allDay: true,
                    color: "{{ $sched->type === 'regular' ? '#0d6efd' : '#dc3545' }}"
                },
            @endforeach
        ]
    });

    calendar.render();
});
</script>

<style>
@media print {
    body {
        margin: 0;
        padding: 0;
    }
    .card {
        width: 210mm;   /* A4 width */
        height: 74mm;   /* 1/4 of 297mm */
        page-break-after: always;
    }
}
</style>
@endpush
