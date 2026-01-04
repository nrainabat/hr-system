@extends('layouts.app')
@section('title', 'Leave Calendar')
@section('content')
<div class="container py-4">
    <h4 class="fw-bold mb-3" style="color: #123456;">Leave Calendar</h4>
    <div class="card shadow-sm border-0 p-3">
        <div id='calendar'></div>
    </div>
</div>

{{-- FullCalendar CDN --}}
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,listWeek'
            },
            events: '{{ route("admin.leave.calendar.events") }}', // Fetch events from our API
            eventColor: '#123456'
        });
        calendar.render();
    });
</script>
@endsection