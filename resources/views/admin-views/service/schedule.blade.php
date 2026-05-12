@extends('layouts.back-end.app')

@section('title', translate('Pooja_schedule'))

@push('css_or_js')
    <link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.10.1/main.min.css" />


    <style>
        #calendar {
            max-width: 1200px;
            margin: 40px auto;
        }

        .gj-timepicker-bootstrap [role=right-icon] button .gj-icon {
            top: 14px;
            right: 5px;
        }

        .gj-datepicker-bootstrap [role=right-icon] button .gj-icon {
            top: 14px;
            right: 5px;
        }

        .fc-daygrid-day-clicked {
            background-color: #90EE90 !important;
        }

        .fc-event {
            background-color: #90EE90 !important;
            border-color: #90EE90 !important;
            color: white !important;
            font-size: :18px !important;
        }
        .fc-event div div{
            text-align: center;
            white-space: break-spaces;
        }
    </style>
@endpush
@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 d-flex gap-10">
                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/brand-setup.png') }}" alt="">
                {{ translate('Pooja_schedule') }}
            </h2>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body text-start">
                        <div id="calendar"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal for Date and Time Picker -->
    <div class="modal fade" id="dateTimeModal" tabindex="-1" aria-labelledby="dateTimeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="dateTimeModalLabel">Select Date and Time</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                @php
                    // dd($event);
                @endphp
                <div class="modal-body">
                    <form id="dateTimeForm" method="POST" action="{{ route('admin.service.eventUpdate', [$event->id]) }}"
                        autocomplete="off">
                        @csrf
                        <input type="hidden" class="form-control" name="id" value="{{ $event->id }}">
                        <div class="mb-3">
                            <label for="startDate" class="form-label">Selected Date</label>
                            <input type="text" class="form-control" name="schedule[]" id="selectedDate" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="onlytime" class="form-label">Time</label>
                            <input type="text" class="form-control" name="schedule_time[]" id="onlytime"
                                onclick="$timepicker.open()" autocomplete="off" placeholder="Event Time" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Event</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
    <script>
        var today, datepicker;
        var today = new Date(new Date().getFullYear(), new Date().getMonth(), new Date().getDate());
        $('#datepicker').datepicker({
            uiLibrary: 'bootstrap4',
            format: 'dd/mm/yyyy',
            modal: true,
            footer: true,
            maxDate: false
        });

        $('#onlytime').timepicker({
            uiLibrary: 'bootstrap4',
            modal: true,
            footer: true
        });

        var $timepicker = $('#onlytime').timepicker({
            uiLibrary: 'bootstrap4',
            modal: true,
            footer: true
        });
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.10.1/main.min.js" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.10.1/locales-all.min.js" type="text/javascript">
    </script>
    <script src="https://unpkg.com/@fullcalendar/core@5.10.1/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.14/index.global.min.js"></script>
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var selectedDate = [];
            var onlytime = [];
            var disabledDates = [];
            var events = {!! $schedulesJson !!};
            var disabledDates = [];
            events.forEach(function(event) {
                var date = event.start.split('T')[0];
                disabledDates.push(date);
            });
            var calendar = new FullCalendar.Calendar(calendarEl, {
                timeZone: 'Asia/Kolkata',
                editable: false,
                droppable: false,
                multiMonthMaxColumns: 2,
                dayMaxEventRows: true,
                month: 'long',
                day: 'numeric',
                year: 'numeric',
                initialView: 'dayGridMonth',
                validRange: function(nowDate) {
                    return {
                        start: nowDate
                    };
                },
                events: {!! $schedulesJson !!},
                dateClick: function(info) {
                    if (disabledDates.includes(info.dateStr)) {
                        return;
                    }
                    if (!Array.isArray(selectedDate)) {
                        selectedDate = [];
                    }
                    var dateIndex = selectedDate.indexOf(info.dateStr);
                    if (dateIndex > -1) {
                        selectedDate.splice(dateIndex, 1);
                        info.dayEl.style.backgroundColor = '';
                    } else {
                        selectedDate.push(info.dateStr);
                        info.dayEl.style.backgroundColor = '#90EE90';
                    }
                    document.getElementById('selectedDate').value = selectedDate;
                    var dateTimeModal = new bootstrap.Modal(document.getElementById('dateTimeModal'));
                    dateTimeModal.show();
                },
                eventClick: function(info) {
                    var dateStr = info.event.startStr.split('T')[0];
                    var timeStr = info.event.startStr.split('T')[1];
                    if (!onlytime[dateStr]) {
                        onlytime[dateStr] = [];
                    }
                    if (onlytime[dateStr].includes(timeStr)) {
                        onlytime[dateStr] = onlytime[dateStr].filter(time => time !== timeStr);
                        info.el.style.backgroundColor = '';
                    } else {
                        onlytime[dateStr].push(timeStr);
                        info.el.style.backgroundColor = '#90EE90';
                    }
                    document.getElementById('onlytime').value = onlytime;

                },
                selectable: true,
                selectAllow: function(selectInfo) {
                    return !disabledDates.includes(selectInfo.startStr);
                },
                eventContent: function(arg) {
                 
                    var deleteIcon = document.createElement('span');
                    deleteIcon.innerHTML = '&#10006;';
                    deleteIcon.style.cursor = 'pointer';
                    deleteIcon.style.color = 'red';
                    deleteIcon.style.marginLeft = '5px';
                    var contentContainer = document.createElement('div');
                    var titleContainer = document.createElement('div');
                    var idContainer = document.createElement('div');
                    titleContainer.textContent = arg.event.title; 
                    idContainer.textContent ={{ $event->id }};
                    contentContainer.appendChild(titleContainer);
                    contentContainer.appendChild(idContainer);
                    contentContainer.appendChild(deleteIcon);

                    // Handle delete icon click
                    deleteIcon.onclick = function() {
                        var eventId = {{ $event->id }};
                        var dateAndTime = arg.event.startStr; 
                        var scheduleDate = dateAndTime.split('T')[0]; 
                        var scheduleTime = dateAndTime.split('T')[1].substring(0, 5);
                        $.ajax({
                            url: "{{ url('admin/service/schedule-delete') }}",
                            method: 'POST',
                            data: {
                                id: eventId, 
                                schedule: scheduleDate, 
                                schedule_time: scheduleTime, 
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                arg.event.remove();
                                toastr.success('Event Delete successfully');
                            },
                            error: function(error) {
                                toastr.error('Error deleting event. Please try again.');
                            }
                        });
                    };

                    return {
                        domNodes: [contentContainer]
                    };
                }
            });
            calendar.render();
        });
    </script>
@endpush
