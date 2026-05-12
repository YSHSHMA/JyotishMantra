@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')

@section('title', translate('astrologer'))

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
                                                        {{ \App\Models\PanditTransectionPooja::where('type', $card['type'])->count() }}
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
                                            <th>ID</th>
                                            <th>Service Order ID</th>
                                            <th>Type</th>
                                            <th>Service Name</th>
                                            <th>Pandit Name</th>
                                            <th>Booking Date</th>
                                            <th>Pandit Amount</th>
                                            <th>Commission</th>
                                            <th>Tax</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($pandit as $key => $value)
                                            <tr>
                                                <td>{{ $value->id }}</td>
                                                <td>{{ $value->service_order_id }}</td>
                                                <td>{{ $value->type }}</td>

                                                <td>
                                                    @if ($value->type === 'pooja')
                                                        {{ $value->service->name ?? '-' }}
                                                    @elseif($value->type === 'vip')
                                                        {{ $value->vipPooja->name ?? '-' }}
                                                    @elseif($value->type === 'anushthan')
                                                        {{ $value->vipPooja->name ?? '-' }}
                                                    @elseif($value->type === 'chadhava')
                                                        {{ $value->chadhava->name ?? '-' }}
                                                    @elseif($value->type === 'offlinepooja')
                                                        {{ $value->offlinepoojaOrder->offlinepooja->name ?? '-' }}
                                                    @elseif($value->type === 'counselling')
                                                        {{ $value->service->name ?? '-' }}
                                                    @endif
                                                </td>

                                                <td>{{ $value->pandit->name ?? '-' }}</td>
                                                <td>{{ $value->booking_date }}</td>
                                                <td>₹{{ $value->pandit_amount }}</td>
                                                <td>{{ $value->admin_commission }}%</td>
                                                <td>{{ $value->govt_tax }}%</td>
                                                <td>{{ $value->status == 1 ? 'Active' : 'Inactive' }}</td>
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
@endsection

@push('script')
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>
    {{-- detail modal --}}
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
    </script>
@endpush