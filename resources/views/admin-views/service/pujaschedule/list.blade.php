@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')

@section('title', 'puja_schedule_list')
@push('css_or_js')
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 d-flex gap-2">
                <img width="20" src="{{ asset('public/assets/back-end/img/festival.png') }}" alt="">
                {{ translate('puja_schedule_list') }}
            </h2>
        </div>

        <div class="card p-4">
            <div class="row mb-3">
                <form method="GET" id="filterForm" class="row mb-3">
                    <div class="col-md-4">
                        <select name="category" class="form-control" onchange="this.form.submit()">
                            <option value="">All Categories</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <select name="weekday" class="form-control" onchange="this.form.submit()">
                            <option value="">All Weekdays</option>
                            @foreach (['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $day)
                                <option value="{{ $day }}" {{ request('weekday') == $day ? 'selected' : '' }}>
                                    {{ $day }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
            <table id="table" class="table table-bordered">
                @php
                    \Carbon\Carbon::setLocale('hi'); // Set Hindi for translatedFormat()
                @endphp
                <thead>
                    <tr>
                        <th>SL</th>
                        <th>Puja Name</th>
                        <th>Day Name</th>
                        <th>Week Day</th>
                        <th>Month Dates</th>
                        @if (Helpers::modules_permission_check('Pooja Schedule', 'Pooja Schedule', 'update-time') || Helpers::modules_permission_check('Pooja Schedule', 'Pooja Schedule', 'update-weekdays'))
                        <th>Action</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pujaschedule as $index => $pujaList)
                        @php
                            $first = $pujaList->first();
                            $service = $first->service ?? null;
                            $serviceName = $service->name ?? 'N/A';
                            $serviceVenue = $service->pooja_venue ?? 'N/A';
                            $servicetime = $service->pooja_time ?? 'N/A';
                            $categoryName = $service->category->name ?? 'Unknown';
                            $bookingDates = $pujaList->pluck('booking_date')->unique();
                        @endphp

                        @foreach ($bookingDates as $date)
                            @php
                                $carbonDate = \Carbon\Carbon::parse($date);
                                $weekdayHindi = $carbonDate->translatedFormat('l'); // Hindi
                                $weekdayEnglish = $carbonDate->locale('en')->isoFormat('dddd'); // English
                            @endphp

                            <tr>
                                @if ($loop->first)
                                    <td rowspan="{{ $bookingDates->count() }}">{{ $loop->parent->iteration }}</td>
                                    <td rowspan="{{ $bookingDates->count() }}">
                                        {{ $serviceName }}<br>
                                        {{ $serviceVenue }}<br>
                                        Service Timer Off: <strong>{{ $servicetime }}</strong><br>

                                        @php
                                        
                                            $weekDays = is_string($service->week_days)
                                                ? json_decode($service->week_days, true)
                                                : $service->week_days;

                                            // Define color classes for each day
                                            $dayColors = [
                                                'sunday' => 'badge-soft-danger',
                                                'monday' => 'badge-soft-primary',
                                                'tuesday' => 'badge-soft-info',
                                                'wednesday' => 'badge-soft-warning',
                                                'thursday' => 'badge-soft-secondary',
                                                'friday' => 'badge-soft-success',
                                                'saturday' => 'badge-soft-dark',
                                            ];
                                        @endphp

                                        @if (!empty($weekDays) && is_array($weekDays))
                                            @foreach ($weekDays as $day)
                                                @php
                                                    $dayLower = strtolower($day);
                                                    $badgeClass = $dayColors[$dayLower] ?? 'badge-soft-secondary';
                                                @endphp
                                                <span class="badge {{ $badgeClass }}">{{ ucfirst($dayLower) }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-muted">No Days</span>
                                        @endif

                                    </td>
                                    <td rowspan="{{ $bookingDates->count() }}">{{ ucfirst($categoryName) }}</td>
                                @endif

                                <td>{{ $weekdayHindi }} ({{ $weekdayEnglish }})</td>
                                <td>{{ $carbonDate->format('d M, Y') }}</td>
                                @if (Helpers::modules_permission_check('Pooja Schedule', 'Pooja Schedule', 'update-time') || Helpers::modules_permission_check('Pooja Schedule', 'Pooja Schedule', 'update-weekdays'))
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        @if (Helpers::modules_permission_check('Pooja Schedule', 'Pooja Schedule', 'update-time'))
                                        <button type="button" class="btn btn-sm btn-outline-warning time-to-change"
                                            data-toggle="modal" data-target="#ServiceTimeEditModal"
                                            data-id="{{ $service->id }}" data-servicename="{{ $service->name }}"
                                            data-oldtime="{{ \Carbon\Carbon::parse($service->pooja_time)->format('H:i') }}"
                                            title="Update Time">
                                            <i class="tio-time"></i>
                                        </button>
                                        @endif

                                        @if (Helpers::modules_permission_check('Pooja Schedule', 'Pooja Schedule', 'update-weekdays'))
                                        <button type="button" class="btn btn-sm btn-outline-success week-to-change"
                                            data-toggle="modal" data-target="#ServiceWeekEditModal"
                                            data-id="{{ $service->id }}" data-servicename="{{ $service->name }}"
                                            data-weekdays='@json($service->week_days ?? [])' title="Update Weekdays">
                                            <i class="tio-calendar"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                                @endif
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>

            </table>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="ServiceTimeEditModal" tabindex="-1" role="dialog" aria-labelledby="editTimeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="updateTimeForm" method="POST" action="{{ route('admin.pujaschedule.update-pooja-time') }}">
                @csrf
                @method('POST') <!-- Use PUT if needed -->

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Update Pooja Time</h5>
                        <p class="text-muted mb-0" style="font-size: 0.9rem;">⏰ कृपया समय को <strong>24 घंटे के
                                फ़ॉर्मेट</strong> में दर्ज करें (जैसे 14:30)</p>

                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body">
                        <input type="hidden" id="service_id" name="service_id">
                        <div class="form-group">
                            <label>Pooja Name:</label>
                            <input type="text" id="service_name" name="name" class="form-control" readonly>
                        </div>

                        <div class="form-group">
                            <label>Old Time:</label>
                            <input type="text" class="form-control" id="old_time" readonly>
                        </div>

                        <div class="form-group">
                            <label>New Time:</label>
                            <input type="time" class="form-control" name="new_time" required>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Update Time</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    {{-- Week chanage --}}
    <div class="modal fade" id="ServiceWeekEditModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form method="POST" action="{{ route('admin.pujaschedule.update-pooja-week') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Update Pooja Week Days</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body">
                        <input type="hidden" name="service_id" id="week_service_id">
                        <input type="text" readonly class="form-control mb-2" id="week_service_name">

                        <div class="form-group">
                            <label>Select Days:</label><br>
                            @foreach (['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'] as $day)
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input weekday-checkbox" type="checkbox"
                                        id="weekday_{{ $day }}" name="week_days[]"
                                        value="{{ $day }}">
                                    <label class="form-check-label"
                                        for="weekday_{{ $day }}">{{ ucfirst($day) }}</label>
                                </div>
                            @endforeach


                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>



@endsection

@push('script')
    <script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        let table = $('#table').DataTable({
            pageLength: 10
        });

        // Change the time
    </script>
    <script>
        $('#categoryFilter, #weekdayFilter').on('change', function() {
            let catVal = $('#categoryFilter').val().toLowerCase();
            let weekVal = $('#weekdayFilter').val().toLowerCase();

            table.rows().every(function() {
                let data = this.data();
                let matchCategory = catVal === '' || data[2].toLowerCase().includes(catVal);
                let matchWeekday = weekVal === '' || data[3].toLowerCase().includes(weekVal);
                if (matchCategory && matchWeekday) {
                    $(this.node()).show();
                } else {
                    $(this.node()).hide();
                }
            });
        });
    </script>
    <script>
        $(document).on('click', '.time-to-change', function() {
            const serviceId = $(this).data('id');
            const serviceName = $(this).data('servicename');
            const oldTime = $(this).data('oldtime');

            console.log("Service ID:", serviceId);
            console.log("Service Name:", serviceName);
            console.log("Old Time:", oldTime);

            $('#service_id').val(serviceId);
            $('#old_time').val(oldTime);
            $('#service_name').val(serviceName);

            $('input[name="new_time"]').val('');
        });
    </script>
    <script>
        $(document).on('click', '.week-to-change', function() {
            const serviceId = $(this).data('id');
            const serviceName = $(this).data('servicename');
            let selectedDays = $(this).data('weekdays');

            if (!Array.isArray(selectedDays)) {
                try {
                    selectedDays = JSON.parse(selectedDays);
                } catch (e) {
                    selectedDays = [];
                }
            }

            console.log("Selected Days:", selectedDays);
            $('#week_service_id').val(serviceId);
            $('#week_service_name').val(serviceName);
            $('.weekday-checkbox').prop('checked', false);

            setTimeout(() => {
                if (Array.isArray(selectedDays)) {
                    selectedDays.forEach(day => {
                        $('#weekday_' + day.toLowerCase()).prop('checked', true);
                    });
                }
            }, 100);


            $('#ServiceWeekEditModal').modal('show');
        });
    </script>
@endpush