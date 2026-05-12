@extends('layouts.back-end.app')

@section('title', translate('Tour_details'))

@push('css_or_js')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
@endpush
@section('content')
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
            <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/refund_transaction.png') }}"
                alt="">
            {{ translate('Tour_details') }}
        </h2>
    </div>
    <div class="refund-details-card--2 p-4">
        <div class="row gy-2">
            <div class="col-lg-4">
                <div class="card h-100 refund-details-card">
                    <div class="card-body">
                        <h4 class="mb-3">{{ translate('Payment_summary') }}</h4>
                        <ul class="dm-info p-0 m-0">
                            <li class="align-items-center">
                                <span class="left">{{ translate('transaction_id') }} </span> <span>:</span> <span
                                    class="right">{{ $getData['transaction_id'] }}</span>
                            </li>
                            <li class="align-items-center">
                                <span class="left text-capitalize">{{ translate('booking_date') }}</span>
                                <span>:</span>
                                <span class="right">{{ date('d M Y, h:s:A', strtotime($getData['created_at'])) }}</span>
                            </li>
                            <li class="align-items-center">
                                <span class="left">{{ translate('payment_method') }} </span> <span>:</span> <span
                                    class="right">{{ str_replace('_', ' ', $getData['payment_method']) }}</span>
                            </li>
                        </ul>
                        @if ($getData['refund_status'] != 0)
                        <h4 class="mb-1 mt-3">{{ translate('Refund_summary') }}</h4>
                        <ul class="dm-info p-0 m-0">
                            <li class="align-items-center">
                                <span class="left">{{ translate('refund_id') }} </span> <span>:</span> <span
                                    class="right">{{ $getData['refound_id'] }}</span>
                            </li>
                            <li class="align-items-center">
                                <span class="left text-capitalize">{{ translate('refund_date') }}</span>
                                <span>:</span>
                                <span class="right">
                                    @if ($getData['refund_date'])
                                    {{ date('d M Y, h:s:A', strtotime($getData['refund_date'])) }}
                                    @endif
                                </span>
                            </li>
                            <li class="align-items-center">
                                <span class="left">{{ translate('refund_status') }} </span> <span>:</span>
                                <span class="right">
                                    @if ($getData['refund_status'] == 1)
                                    {{ translate('success') }}
                                    @elseif($getData['refund_status'] == 2)
                                    {{ translate('progressed') }}
                                    @elseif($getData['refund_status'] == 3)
                                    {{ translate('failed') }}
                                    @else
                                    {{ translate('pending') }}
                                    @endif

                                </span>
                            </li>
                        </ul>
                        @endif


                        @if (($getData['advance_withdrawal_amount'] ?? 0) != 0)
                        <h4 class="mb-1 mt-3">{{ translate('advance_withdrawal_amount') }}</h4>
                        <ul class="dm-info p-0 m-0">
                            <li class="align-items-center">
                                <span class="left">{{ translate('amount') }} </span> <span>:</span> <span
                                    class="right">{{ $getData['advance_withdrawal_amount'] }}</span>
                            </li>

                            <li class="align-items-center">
                                <span class="left">{{ translate('status') }} </span> <span>:</span>
                                <span class="right">
                                    <?php
                                    $new_withdrowal = \App\Models\WithdrawalAmountHistory::where('vendor_id', $getData['cab_assign'])->where('ex_id', $getData['id'])->where('type', 'tour')->first();
                                    ?>
                                    @if ($new_withdrowal['status'] == 1)
                                    {{ translate('success') }}
                                    @elseif($new_withdrowal['status'] == 2)
                                    {{ translate('failed') }}
                                    @else
                                    {{ translate('pending') }}
                                    @endif

                                </span>
                            </li>
                        </ul>
                        @endif

                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card h-100 refund-details-card">
                    <div class="card-body">
                        <div class="gap-3 mb-4 d-flex justify-content-between flex-wrap align-items-center">
                            <h4 class="">{{ translate('Tour_details') }}</h4>

                        </div>
                        <div class="refund-details">
                            <div class="img">
                                <div class="onerror-image border rounded">
                                    <img src="{{ getValidImage(path: 'storage/app/public/tour_and_travels/tour_visit/' . ($getData['Tour']['tour_image'] ?? ''), type: 'backend-product') }}"
                                        alt="">
                                </div>
                            </div>
                            <div class="--content flex-grow-1">
                                <h4>
                                    <a href="{{ route('admin.tour_visits.overview', [$getData['Tour']['id']]) }}">
                                        {{ $getData['Tour']['tour_name'] }}
                                    </a>
                                    <br>
                                    <span class="h6">Pickup Date :
                                        {{ date('d M,Y', strtotime($getData['pickup_date'])) }}</span><br>
                                    <span class="h6">Pickup Time : {{ $getData['pickup_time'] }}</span><br>
                                    <span class="h6">Pickup Location : {{ $getData['pickup_address'] }}</span>
                                </h4>


                            </div>
                            <ul class="dm-info p-0 m-0 w-l-115">

                                <li>
                                    <span class="left">{{ translate('total_price') }} </span>
                                    <span>:</span>
                                    <span class="right">
                                        <strong>
                                            {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $getData['amount'] + $getData['coupon_amount']), currencyCode: getCurrencyCode()) }}
                                        </strong>
                                    </span>
                                </li>
                                <li>
                                    <span class="left">{{ translate('coupon_discount') }} </span>
                                    <span>:</span>
                                    <span class="right">
                                        <strong>
                                            {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $getData['coupon_amount'] ?? 0), currencyCode: getCurrencyCode()) }}
                                        </strong>
                                    </span>
                                </li>

                                <li>
                                    <span class="left">{{ translate('total_tax') }} </span>
                                    <span>:</span>
                                    <span class="right">
                                        <strong>
                                            {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $getData['gst_amount'] ?? 0), currencyCode: getCurrencyCode()) }}
                                        </strong>
                                    </span>
                                </li>
                                <li>
                                    <span class="left">{{ translate('admin_commission') }} </span>
                                    <span>:</span>
                                    <span class="right">
                                        <strong>
                                            {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $getData['admin_commission'] ?? 0), currencyCode: getCurrencyCode()) }}
                                        </strong>
                                    </span>
                                </li>
                                <li>
                                    <span class="left">{{ translate('subtotal') }} </span>
                                    <span>:</span>
                                    <span class="right">
                                        <strong>
                                            {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $getData['final_amount'] ?? 0), currencyCode: getCurrencyCode()) }}
                                        </strong>
                                    </span>
                                </li>
                                @if ($getData['refund_status'] != 0)
                                <li>
                                    <span class="left">{{ translate('refundable_amount') }} </span>
                                    <span>:</span>
                                    <span class="right">
                                        <strong>
                                            {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $getData['refund_amount'] ?? 0), currencyCode: getCurrencyCode()) }}
                                        </strong>
                                    </span>
                                </li>
                                @endif

                            </ul>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex gap-2 align-items-center justify-content-between mb-4">
                            <h4 class="d-flex gap-2">
                                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/vendor-information.png') }}"
                                    alt=""> Customer information
                            </h4>
                        </div>
                        <div class="media flex-wrap gap-3">
                            <div class="">
                                <img class="avatar rounded-circle avatar-70"
                                    src="{{ getValidImage(path: 'storage/app/public/profile/' . $getData['userData']['image'], type: 'backend-product') }}"
                                    alt="Image">
                            </div>
                            <div class="media-body d-flex flex-column gap-1">
                                <span class="title-color"><strong>{{ $getData['userData']['name'] }}</strong></span>
                                <span
                                    class="title-color break-all"><strong>{{ $getData['userData']['phone'] }}</strong></span>
                                @if ($getData['userData']['phone'] != $getData['userData']['email'])
                                <span class="title-color break-all">{{ $getData['userData']['email'] }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6">
                <div class="card h-100 refund-details-card--2">
                    <div class="card-body">
                        <h4 class="mb-3 text-capitalize">{{ translate('Booking_info') }}</h4>
                        <div class="row">
                            <div class="col-12 table-responsive datatable-custom">
                                <table
                                    class="table table-hover text-center table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                                    <thead class="thead-light thead-50 text-capitalize">
                                        <tr>
                                            <th>{{ translate('packages') }}</th>
                                            <th>{{ translate('QTY') }}</th>
                                            <th>{{ translate('price') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php($ex_charges = 0)
                                        @php($total_off_price = 0)
                                        @php($assign_cabs = 0)
                                        @php($assign_cabs_use_allPackages = 0)
                                        @php($assign_cabs_use_qtys = 0)
                                        @if (!empty($getData['booking_package']) && json_decode($getData['booking_package'], true))
                                        @foreach (json_decode($getData['booking_package'], true) as $val)
                                        @if ($val['type'] == 'ex_distance')
                                        @php($ex_charges = $val['price'] ?? 0)
                                        @elseif($val['type'] == 'cab')
                                        @php($assign_cabs = $val['id'] ?? 0)
                                        @endif
                                        @endforeach
                                        @endif

                                        @if (!empty($getData['booking_package']) && json_decode($getData['booking_package'], true))
                                        @foreach (json_decode($getData['booking_package'], true) as $val)
                                        @if (
                                        $getData['use_date'] == 0 ||
                                        (($val['type'] == 'cab' || $val['type'] == 'per_head' || $val['type'] == 'tax' || $val['type'] == 'cgst' || $val['type'] == 'sgst') && $getData['use_date'] == 1) ||
                                        ($val['type'] != 'ex_distance' && $getData['use_date'] == 2) ||
                                        ($val['type'] != 'ex_distance' && $getData['use_date'] == 3) ||
                                        ($val['type'] != 'ex_distance' && $getData['use_date'] == 4))
                                        <tr>
                                            <?php
                                            if ($val['type'] == 'cab') {
                                                $tourPackages = \App\Models\TourCab::where('id', $val['id'])->first();
                                                $images = getValidImage(path: 'storage/app/public/tour_and_travels/cab/' . $tourPackages['image'], type: 'backend-product');
                                                $assign_cabs_use_allPackages = $val['id'];
                                                $assign_cabs_use_qtys = $val['qty'];
                                            } elseif ($val['type'] == 'other' || $val['type'] == 'foods' || $val['type'] == 'food' || $val['type'] == 'hotel' || \Illuminate\Support\Str::startsWith($val['type'], 'other')) {
                                                $tourPackages = \App\Models\TourPackage::where('id', $val['id'])->first();
                                                $images = getValidImage(path: 'storage/app/public/tour_and_travels/package/' . $tourPackages['image'], type: 'backend-product');
                                            } else {
                                                $tourPackages = [];
                                                $images = getValidImage(path: 'storage/app/public/tour_and_travels/package/', type: 'backend-product');
                                            }
                                            ?>
                                            <td>
                                                @if ($val['type'] == 'ex_distance')
                                                <span class="fs-15 font-semibold">Ex Distance</span>
                                                @elseif($val['type'] == 'route')
                                                <span class="fs-15 font-semibold">Route</span>
                                                @elseif($val['type'] == 'transport')
                                                <span class="fs-15 font-semibold">Ex Transport</span>
                                                @elseif($val['type'] == 'per_head')
                                                <span class="fs-15 font-semibold">Per Head</span>
                                                @elseif($val['type'] == 'tax' || $val['type'] == 'cgst' || $val['type'] == 'sgst')
                                                <span class="fs-15 font-semibold">{{ strtoupper($val['type']) }}</span>
                                                @else
                                                <div class="media align-items-center">
                                                    <img class="d-block get-view-by-onclick rounded"
                                                        src="{{ $images }}"
                                                        alt="{{ translate('image_Description') }}"
                                                        style="width: 80px;height: 72px;">
                                                    <div class="ml-1">
                                                        <small class="title-color" data-title="{{ $tourPackages['name'] ?? '' }}" role="tooltip" data-toggle="tooltip">
                                                            {{ $tourPackages['name'] ?? '' }} <br>
                                                            @if (!empty($val['seats'] ?? ''))
                                                            {{ $val['seats'] ?? '' }}
                                                            {{ $val['type'] == 'cab' ? 'seats' : 'people' }}
                                                            @endif
                                                        </small>
                                                    </div>
                                                </div>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($val['type'] == 'ex_distance')
                                                <span class="fs-15 font-semibold">{{ $val['qty'] }} Km</span>
                                                @elseif($val['type'] == 'route')
                                                <span class="fs-15 font-semibold"></span>
                                                @elseif($val['type'] == 'per_head' || $val['type'] == 'transport')
                                                <span class="fs-15 font-semibold">{{ $val['qty'] }} </span>
                                                @elseif($val['type'] == 'tax')
                                                <span class="fs-15 font-semibold"></span>
                                                @else
                                                <span class="fs-15 font-semibold">
                                                    @if ($val['type'] == 'cab')
                                                    @if ($getData['Tour']['tour_type'] == 'cities_tour')
                                                    cabs :
                                                    @else
                                                    people :
                                                    @endif
                                                    @endif
                                                    {{ $val['qty'] }}
                                                </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($val['type'] == 'cab')
                                                <span
                                                    class="fs-15 font-semibold">{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: ($val['price'] ?? 0) - ($ex_charges ?? 0)), currencyCode: getCurrencyCode()) }}</span>
                                                <br>
                                                @if ($val['type'] != 'ex_distance' && $getData['use_date'] != 0)
                                                <a class="btn btn-sm btn-success"
                                                    onclick="$('.modelopen_packages').modal('show')">View
                                                    Plan</a>
                                                @endif
                                                @php($total_off_price += ($val['price'] ?? 0) - ($ex_charges ?? 0))
                                                @elseif($val['type'] == 'route')
                                                <span
                                                    class="fs-15 font-semibold">{{ ucwords(str_replace('_', ' ', $val['price'])) }}</span>
                                                @else
                                                @php($total_off_price += $val['price'] ?? 0)
                                                <span
                                                    class="fs-15 font-semibold">{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $val['price']), currencyCode: getCurrencyCode()) }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endif
                                        @endforeach
                                        @endif
                                    </tbody>
                                    <tfoot>
                                        <th></th>
                                        <th></th>
                                        <th>{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $total_off_price ?? 0), currencyCode: getCurrencyCode()) }}
                                        </th>
                                    </tfoot>
                                </table>
                                @if (empty($getData['booking_package']))
                                <div class="text-center p-4">
                                    <img class="mb-3 w-160"
                                        src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}"
                                        alt="{{ translate('image_description') }}">
                                    <p class="mb-0">{{ translate('no_data_to_show') }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6">
                <div class="card h-100 refund-details-card--2">
                    <div class="card-body">
                        <h4 class="mb-3 text-capitalize">{{ translate('cab_info') }}</h4>
                        <div class="key-val-list d-flex flex-column gap-2 min-width--60px">
                            <div class="key-val-list-item">
                                <form action="{{ route('admin.tour-visits-booking.assigned-cab') }}" method="post">
                                    @csrf
                                    <div class="row">
                                        <div class='col-md-12'>
                                            <label class="text-capitalize">{{ translate('select_Traveller') }}</label>
                                            <input type="hidden" name='id' value='{{ $getData['id'] }}'>
                                            <select name="cab_id" class="form-control">
                                                <option value="" selected disabled>Select Cab Company</option>
                                                @if (!empty($company_list))
                                                @foreach ($company_list as $va)
                                                <option value="{{ $va['id'] }}"
                                                    {{ ($getData['company']['id'] ?? 0) == $va['id'] ? 'selected' : '' }}>
                                                    {{ $va['company_name'] }}
                                                </option>
                                                @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <div class="col-md-12 mt-2">
                                            <button type="submit" class="btn btn-success float-end">Assign</button>
                                        </div>
                                        <div class="col-md-12 mt-1">
                                            <hr>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            @if ($getData['company'])
                            <div class="key-val-list-item d-flex gap-3">
                                <span class="text-capitalize">{{ translate('traveller_name') }}</span>:
                                <span>{{ $getData['company']['company_name'] }}</span>
                            </div>
                            <div class="key-val-list-item d-flex gap-3">
                                <span class="text-capitalize">{{ translate('email_address') }}</span>:
                                <span>
                                    <a class="text-dark"
                                        href="mailto:{{ $getData['company']['email'] }}">{{ $getData['company']['email'] }}
                                    </a>
                                </span>
                            </div>
                            <div class="key-val-list-item d-flex gap-3">
                                <span class="text-capitalize">{{ translate('phone_number') }} </span>:
                                <span>
                                    <a class="text-dark"
                                        href="tel:{{ $getData['company']['phone_no'] }}">{{ $getData['company']['phone_no'] }}
                                    </a>
                                </span>
                            </div>
                            @endif

                        </div>
                        <div>
                            <a class="btn btn-primary btn-sm"
                                onclick="relatedOrderView(`{{ $getData['tour_id'] }}`,`{{ $getData['id'] }}`)">Related Order View </a>
                        </div>
                        <div>
                            <hr>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                @if (
                                $getData['traveller_cab_id'] != 0 &&
                                json_decode($getData['traveller_cab_id']) &&
                                \App\Models\TourCabManage::whereIn('id', json_decode($getData['traveller_cab_id']))->exists())
                                <?php $getCarList = \App\Models\TourCabManage::whereIn('id', json_decode($getData['traveller_cab_id']))
                                    ->with(['Cabs'])
                                    ->get(); ?>
                                @if ($getCarList)
                                @foreach ($getCarList as $cab_details)
                                <span class="font-weight-bolder">Transport
                                    Name</span>&nbsp;:&nbsp;<span>{{ $cab_details['Cabs']['name'] }} ({{ $cab_details['Cabs']['seats'] }}seats)</span><br>
                                <span class="font-weight-bolder">Car
                                    Reg.</span>&nbsp;:&nbsp;<span>{{ $cab_details['reg_number'] }}</span><br>
                                <span class="font-weight-bolder">Car
                                    Model</span>&nbsp;:&nbsp;<span>{{ $cab_details['model_number'] }}</span><br>
                                <hr>
                                @endforeach
                                @endif
                                @endif
                            </div>
                            <div class="col-md-6">
                                @if (
                                $getData['traveller_driver_id'] != 0 &&
                                json_decode($getData['traveller_driver_id']) &&
                                \App\Models\TourDriverManage::whereIn('id', json_decode($getData['traveller_driver_id']))->exists())
                                <?php $getDrivers = \App\Models\TourDriverManage::whereIn('id', json_decode($getData['traveller_driver_id']))->get(); ?>
                                @if ($getDrivers)
                                @foreach ($getDrivers as $driver_details)
                                <span class="font-weight-bolder">Driver
                                    Name</span>&nbsp;:&nbsp;<span>{{ $driver_details['name'] }}</span><br>
                                <span class="font-weight-bolder">Driver
                                    phone</span>&nbsp;:&nbsp;<span>{{ $driver_details['phone'] }}</span><br>
                                <span class="font-weight-bolder">Driver
                                    Email</span>&nbsp;:&nbsp;<span>{{ $driver_details['email'] }}</span><br>
                                <span class="font-weight-bolder">Driver
                                    Year</span>&nbsp;:&nbsp;<span>{{ $driver_details['year_ex'] }}</span><br>
                                <span class="font-weight-bolder">Driver
                                    License</span>&nbsp;:&nbsp;<span>{{ $driver_details['license_number'] }}</span><br>
                                <hr>
                                @endforeach
                                @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Days</th>
                                <th>Title</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (!empty($getData['Tour'] ?? '') && count($getData['Tour']['TourPlane']) > 0)
                            <?php $pq = 1; ?>
                            @foreach ($getData['Tour']['TourPlane'] as $vals)
                            <tr>
                                <td>{{ $pq }}</td>
                                <td>{{ $vals['name'] }}</td>
                                <td>{{ $vals['time'] }}</td>
                                <td>{!! $vals['description'] !!}</td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal modelopen_packages" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $getData['Tour']['tour_name'] ?? '' }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <table class="table">
                            <thead>
                                <tr>
                                    <td>Name</td>
                                    <td>No. Of Person</td>
                                    <td>price</td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $k_index = 1; ?>
                                @if (!empty($getData['Tour']['cab_list_price']) && json_decode($getData['Tour']['cab_list_price'], true) && ($getData['Tour']['is_person_use'] == 0))
                                @foreach (json_decode($getData['Tour']['cab_list_price'], true) as $p_info)
                                @if ($assign_cabs_use_allPackages == $p_info['cab_id'] && $k_index == 1)
                                <?php $k_index++; ?>
                                <tr>
                                    <td>
                                        <?php $tourPackages = \App\Models\TourCab::where('id', $p_info['cab_id'])->first(); ?>
                                        <div class="col-3 text-left">
                                            <img src="{{ getValidImage(path: 'storage/app/public/tour_and_travels/cab/' . ($tourPackages['image'] ?? ''), type: 'backend-product') }}"
                                                class="img-fluid img-thumbnail">
                                        </div>
                                        <span class="font-weight-bold">
                                            {{ $tourPackages['name'] ?? '' }}
                                        </span>

                                    </td>
                                    <td>{{ $assign_cabs_use_qtys }}</td>
                                    <td>
                                        @if(1 > $p_info['price'] && $p_info['type'] != 'route')
                                        <span>Included</span>
                                        @else
                                        {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: ((float) $p_info['price'] ?? 0) * $assign_cabs_use_qtys ?? 1), currencyCode: getCurrencyCode()) }}
                                        @endif
                                    </td>
                                </tr>
                                @endif
                                @endforeach
                                @endif
                                @if (!empty($getData['Tour']['package_list_price']) && json_decode($getData['Tour']['package_list_price'], true))
                                @foreach (json_decode($getData['Tour']['package_list_price'], true) as $p_info)
                                <tr>
                                    <td>
                                        <?php $tourPackages = \App\Models\TourPackage::where('id', $p_info['package_id'] ?? '')->first(); ?>
                                        <div class="col-3 text-left">
                                            <img src="{{ getValidImage(path: 'storage/app/public/tour_and_travels/package/' . ($tourPackages['image'] ?? ''), type: 'backend-product') }}"
                                                class="img-fluid img-thumbnail">
                                        </div>
                                        <span class="font-weight-bold">
                                            {{ $tourPackages['name'] ?? '' }}
                                        </span>
                                    </td>
                                    <td>{{ $assign_cabs_use_qtys }}</td>
                                    <td>
                                        {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: ((float) $p_info['pprice'] ?? 0) * $assign_cabs_use_qtys ?? 1), currencyCode: getCurrencyCode()) }}
                                    </td>
                                </tr>
                                @endforeach
                                @endif
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade update-date-model" tabindex="-1" aria-labelledby="dateTimeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content modal-xl">
            <div class="modal-header">
                <h5 class="modal-title">{{ $getData['Tour']['tour_name'] }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 text-center datatable_view">
                        <table id="tourOrdersTable" class="display">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Time</th>
                                    <th>Cabs Info</th>
                                    <th>Driver Info</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@push('script')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script>
    $('#tourOrdersTable').DataTable();

    function relatedOrderView(tour_id, order_id) {
        $.ajax({
            url: "{{ route('tour.related-order-view') }}",
            data: {
                type: "admin",
                user_id: "",
                tour_id,
                order_id,
                _token: '{{ csrf_token() }}'
            },
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            dataType: "json",
            type: "post",
            success: function(data) {
                $(".update-date-model").modal('show');
                $(".datatable_view").html(data.data);
                $('#tourOrdersTable2').DataTable();
            }
        });
    }
</script>
@endpush