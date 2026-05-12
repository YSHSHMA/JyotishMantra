@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')

@section('title', 'puja_records_list')
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
                {{ translate('Puja Records List') }}
            </h2>
        </div>

        <div class="card p-4">
            <table id="table" class="table table-bordered">
                <thead>
                    <tr>
                        <th>SL</th>
                        <th>Puja Name</th>
                        <th>Day Name</th>
                        <th>Total Month Days</th>
                        <th>Total Days</th>
                        <th>Total Pujas</th>
                        <th>Total Amount</th>
                        @if (Helpers::modules_permission_check('Pooja Records', 'Pooja Records', 'dates'))
                        <th>Date-wise Details</th>
                        @endif
                        @if (Helpers::modules_permission_check('Pooja Records', 'Pooja Records', 'export'))
                        <th>Action</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($recordpuja as $index => $row)
                        <tr @if ($loop->first) style="background-color: #e6ffe6;" @endif>
                            <td>
                                {{ $index + 1 }}
                                @if ($loop->first)
                                    <span class="badge badge-success ms-2">Most Performed</span>
                                @endif
                            </td>
                            <td>{{ $row['service_name'] }}</td>
                            <td>
                                @foreach ($row['day_frequency'] as $day => $count)
                                    <div>{{ $day }}</div>
                                @endforeach
                            </td>
                            <td>
                                @foreach ($row['month_frequency'] as $month => $count)
                                    <div>{{ $month }}</div>
                                @endforeach
                            </td>
                            <td>{{ $row['total_days'] }}</td>
                            <td>{{ $row['total_pujas'] }}</td>
                            <td>₹{{ number_format($row['total_amount'], 2) }}</td>
                            @if (Helpers::modules_permission_check('Pooja Records', 'Pooja Records', 'dates'))
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-info view-dates-btn"
                                    data-service="{{ $row['service_name'] }}" data-dates='@json($row['date_list'])'>
                                    <i class="tio-calendar-month"></i> View Dates
                                </button>
                            </td>
                            @endif
                            @if (Helpers::modules_permission_check('Pooja Records', 'Pooja Records', 'export'))
                            <td>
                                <a href="{{ route('admin.pujarecords.export-form', ['service' => $row['service_name']]) }}"
                                    class="btn btn-sm btn-success">
                                    Export
                                </a>
                            </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="dateListModal" tabindex="-1" aria-labelledby="dateListModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="dateListModalLabel">Puja Dates</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="GET" action="{{ route('admin.pujarecords.export-filtered') }}" target="_blank"
                        class="mb-3">
                        <input type="hidden" name="service" id="exportServiceName">
                        <input type="hidden" name="month" id="exportSelectedMonth">
                        <input type="hidden" name="day" id="exportSelectedDay">
                        <button type="submit" class="btn btn-sm btn-success">
                            <i class="tio-download"></i> Export Filtered
                        </button>
                    </form>

                    <div class="row mb-3">
                        <div class="col">
                            <select class="form-select form-select-sm form-control" id="filterMonth">
                                <option value="">All Months</option>
                                @foreach (['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'] as $month)
                                    <option value="{{ $month }}">{{ $month }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            <select class="form-select form-select-sm form-control" id="filterDay">
                                <option value="">All Days</option>
                                @foreach (['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $day)
                                    <option value="{{ $day }}">{{ $day }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>


                    <!-- Calendar placeholder -->
                    <div id="calendarContainer"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        $('#table').DataTable({
            pageLength: 10
        });

        let allDates = [];

        $(document).on('click', '.view-dates-btn', function() {
            const service = $(this).data('service');
            allDates = $(this).data('dates');

            $('#dateListModalLabel').text(`Puja Dates for "${service}"`);

            const currentMonth = new Date().toLocaleString('default', {
                month: 'short'
            });
            $('#filterMonth').val(currentMonth);
            $('#filterDay').val('');

            const filtered = filterDates(allDates, currentMonth, '');
            syncExportInputs(service);
            initFlatpickr(filtered, service);
            $('#dateListModal').modal('show');
        });

        $('#filterMonth, #filterDay').on('change', function () {
            const monthFilter = $('#filterMonth').val();
            const dayFilter = $('#filterDay').val();
            const service = $('#dateListModalLabel').text().split('"')[1];

            const filtered = filterDates(allDates, monthFilter, dayFilter);
            syncExportInputs(service);
            initFlatpickr(filtered, service); // ← This re-initializes calendar with filtered dates
        });

        function filterDates(dateList, month, day) {
            return dateList.filter(date => {
                const d = new Date(date);
                const m = d.toLocaleString('default', {
                    month: 'short'
                });
                const dayName = d.toLocaleString('default', {
                    weekday: 'long'
                });
                return (!month || m === month) && (!day || dayName === day);
            });
        }

        function initFlatpickr(dates, service) {
            const calendarDiv = document.createElement("input");
            calendarDiv.type = "text";
            calendarDiv.id = "flatpickrCalendar";
            document.getElementById("calendarContainer").innerHTML = "";
            document.getElementById("calendarContainer").appendChild(calendarDiv);

            flatpickr("#flatpickrCalendar", {
                dateFormat: "Y-m-d",
                inline: true,
                enable: dates,
                defaultDate: dates,
                onChange: function(selectedDates, dateStr) {
                    if (dateStr) {
                        const url =
                            `{{ url('admin/pujarecords/puja-details') }}/${service}/${encodeURIComponent(dateStr)}`;
                        window.open(url, "_blank");
                    }
                }
            });
        }

        function syncExportInputs(service) {
            $('#exportServiceName').val(service);
            $('#exportSelectedMonth').val($('#filterMonth').val());
            $('#exportSelectedDay').val($('#filterDay').val());
        }
    </script>
@endpush
