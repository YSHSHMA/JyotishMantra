@extends('layouts.back-end.app')

@section('title', 'puja_devotee_list')
@push('css_or_js')
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@section('content')
    @php
        $orderCards = [
            [
                'type' => 'pooja',
                'color' => 'text-success',
                'label' => 'Puja Order',
                'route' => route('admin.pooja.orders.list', 'all'),
                'image' => asset('public/assets/back-end/img/pooja/ordercom.png'),
            ],
            [
                'type' => 'vip',
                'color' => 'text-success',
                'label' => 'VIP Order',
                'route' => route('admin.vippooja.order.list', 'all'),
                'image' => asset('public/assets/back-end/img/pooja/ordercom.png'),
            ],
            [
                'type' => 'anushthan',
                'color' => 'text-primary',
                'label' => 'Anushthan Order',
                'route' => route('admin.anushthan.order.list', 'all'),
                'image' => asset('public/assets/back-end/img/pooja/ordercom.png'),
            ],
            [
                'type' => 'chadhava',
                'color' => 'text-danger',
                'label' => 'Chadhava Order',
                'route' => route('admin.chadhava.order.list', 'all'),
                'image' => asset('public/assets/back-end/img/pooja/ordercom.png'),
            ],
        ];
    @endphp


    <div class="content container-fluid">
        <div class="card mb-3 remove-card-shadow">
            <div class="card-body">
                <div class="mb-3">
                    <h2 class="h1 mb-0 d-flex gap-2">
                        <img width="20" src="{{ asset('public/assets/back-end/img/festival.png') }}" alt="">
                        {{ translate('puja devotee list') }}
                    </h2>
                </div>
                <div class="row g-2" id="order_stats">
                    <div class="col-lg-12">

                        <div class="row g-2">
                            @foreach ($orderCards as $card)
                                <div class="col-md-3">
                                    <a href="{{ $card['route'] }}" class="text-decoration-none" target="_blank">
                                        <div class="card card-body h-100 justify-content-center">
                                            <div class="d-flex gap-2 justify-content-between align-items-center">
                                                <div class="d-flex flex-column align-items-start">
                                                    <h3 class="mb-1 fz-24 {{ $card['color'] }}">
                                                        {{ \App\Models\Devotee::where('type', $card['type'])->where('status', 1)->count() }}
                                                    </h3>
                                                    <div class="text-capitalize mb-0">{{ $card['label'] }}</div>
                                                </div>
                                                <div>
                                                    <img width="40" class="mb-2" src="{{ $card['image'] }}"
                                                        alt="{{ $card['type'] }}">
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>  

                    </div>

                </div>
            </div>
        </div>
        <div class="row mt-20">
            <div class="col-md-12">
                <div class="card mb-3">
                    <div class="px-3 py-4">
                        <div class="row g-2 flex-grow-1">
                            <div class="col-sm-12 col-md-12 col-lg-12">
                                <form method="GET" action="{{ url()->current() }}" id="filterForm" class="row mb-3">
                                    {{-- 🔹 Service Type --}}
                                    <div class="col-md-3">
                                        <label for="type" class="font-weight-bold">Service Type</label>
                                        <select name="type" id="type" class="form-control"
                                            onchange="this.form.submit()">
                                            <option value="">All Types</option>
                                            <option value="pooja" {{ request('type') == 'pooja' ? 'selected' : '' }}>Pooja
                                            </option>
                                            <option value="vip" {{ request('type') == 'vip' ? 'selected' : '' }}>VIP
                                            </option>
                                            <option value="anushthan"
                                                {{ request('type') == 'anushthan' ? 'selected' : '' }}>Anushthan</option>
                                            <option value="chadhava" {{ request('type') == 'chadhava' ? 'selected' : '' }}>
                                                Chadhava</option>
                                        </select>
                                    </div>

                                    {{-- 🔹 Service Name --}}
                                    <div class="col-md-3">
                                        <label for="service_id" class="font-weight-bold">Service Name</label>
                                        @php
                                            $selectedType = request('type');
                                            if ($selectedType === 'pooja') {
                                                $uniqueServices = $devotee
                                                    ->pluck('serviceOrder.services')
                                                    ->filter()
                                                    ->unique('id');
                                            } elseif (in_array($selectedType, ['vip', 'anushthan'])) {
                                                $uniqueServices = $devotee
                                                    ->pluck('serviceOrder.vippoojas')
                                                    ->filter()
                                                    ->unique('id');
                                            } elseif ($selectedType === 'chadhava') {
                                                $uniqueServices = $devotee
                                                    ->pluck('chadhavaOrder.chadhava')
                                                    ->filter()
                                                    ->unique('id');
                                            } else {
                                                $uniqueServices = collect();
                                            }
                                        @endphp
                                        <select name="service_id" id="service_id" class="form-control"
                                            onchange="this.form.submit()">
                                            <option value="">All Services</option>
                                            @foreach ($uniqueServices as $service)
                                                <option value="{{ $service->id }}"
                                                    {{ request('service_id') == $service->id ? 'selected' : '' }}>
                                                    {{ Str::limit($service->name, 40) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- 🔹 Booking Date --}}
                                    <div class="col-md-3">
                                        <label for="booking_date" class="font-weight-bold">Booking Date</label>
                                        <input type="date" name="booking_date" id="booking_date" class="form-control"
                                            value="{{ request('booking_date') }}" onchange="this.form.submit()">
                                    </div>

                                    {{-- 🔹 Clear Filters --}}
                                    <div class="col-md-3 d-flex align-items-end">
                                        <a href="{{ url()->current() }}" class="btn btn-secondary w-100">
                                            Clear All Filters
                                        </a>
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mb-3">
                    <div class="px-3 py-4">
                        <div class="row g-2 flex-grow-1">
                            <div class="col-md-12">
                                <table id="table" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th>Puja Type</th>
                                            <th>Puja Name,order,Amount</th>
                                            <th>Customer Name & Phone</th>
                                            <th>Members Name & Gotra</th>
                                            <th>Prasaad</th>
                                            <th>Address</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($devotee as $d)
                                            @php
                                                $type = $d->type;
                                                $serviceName = '-';

                                                if ($d->type === 'pooja') {
                                                    $serviceName = $d->serviceOrder->services->name ?? '-';
                                                } elseif (in_array($d->type, ['vip', 'anushthan'])) {
                                                    $serviceName = $d->serviceOrder->vippoojas->name ?? '-';
                                                } elseif ($d->type === 'chadhava') {
                                                    $serviceName = $d->chadhavaOrder->chadhava->name ?? '-';
                                                }
                                                $members = json_decode($d->members, true);
                                                $gotra = $d->gotra ?? '-';
                                            @endphp
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $d->type }}</td>
                                                <td>
                                                    {{ $serviceName }},<br>
                                                    {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $d->serviceOrder->pay_amount - $d->serviceOrder->coupon_amount), currencyCode: getCurrencyCode()) }}
                                                    ,<br>
                                                    {{ $d->service_order_id ?? '-' }},<br>
                                                    {{ $d->serviceOrder && $d->serviceOrder->booking_date ? date('d, M, Y', strtotime($d->serviceOrder->booking_date)) : '-' }}
                                                </td>
                                                <td>
                                                    <strong>Name:</strong> {{ $d->name ?? '-' }}<br>
                                                    <strong>Phone:</strong> {{ $d->phone ?? '-' }}
                                                </td>
                                                <td>
                                                    @if (is_array($members) && count($members))
                                                        <ul class="mb-0 pl-3" type="square">
                                                            @foreach ($members as $i => $member)
                                                                <li>{{ $i + 1 }}. {{ $member }} (Gotra:
                                                                    {{ $gotra }})</li>
                                                            @endforeach
                                                        </ul>
                                                    @else
                                                        <p>No members available.</p>
                                                    @endif
                                                </td>
                                                <!-- Prasad Delivery Badge -->
                                                <td>
                                                    @if ($d->is_prashad == 1)
                                                        <span class="badge bg-success">Prasad Delivered</span>
                                                    @else
                                                        <span class="badge bg-danger">Not Delivered</span>
                                                    @endif
                                                </td>


                                                <td>
                                                    {{ $d->house_no ?? '' }}
                                                    @if (!empty($d->area))
                                                        , {{ $d->area }}
                                                    @endif
                                                    @if (!empty($d->landmark))
                                                        , {{ $d->landmark }}
                                                    @endif
                                                    @if (!empty($d->address_city))
                                                        , {{ $d->address_city }}
                                                    @endif
                                                    @if (!empty($d->address_state))
                                                        , {{ $d->address_state }}
                                                    @endif
                                                    @if (!empty($d->address_pincode))
                                                        - {{ $d->address_pincode }}
                                                    @endif
                                                    @if (!empty($d->latitude) && !empty($d->longitude))
                                                        ({{ $d->latitude }}, {{ $d->longitude }})
                                                    @endif
                                                </td>
                                                <!-- Status Field -->
                                                <td>
                                                    @if ($d->status == 1)
                                                        <span class="badge bg-success">Complete</span>
                                                    @else
                                                        <span class="badge bg-warning text-white">Pending</span>
                                                    @endif
                                                </td>

                                            </tr>
                                        @endforeach


                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
        let table = $('#table').DataTable({
            pageLength: 20,
            scrollY: '500px',
            scrollCollapse: true,
            paging: true,
            fixedHeader: true,
            fixedFooter: true,
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, "All"]
            ],
        });
        $('#type').on('change', function() {
            $('#table').DataTable().ajax.reload();
        });
        $('#service_id').on('change', function() {
            $('#table').DataTable().ajax.reload();
        });
        $(document).ready(function() {
            $('.ckeditor').ckeditor();
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

        $('#filterMonth, #filterDay').on('change', function() {
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