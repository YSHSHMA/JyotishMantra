@extends('layouts.back-end.app-tour')
@section('title', translate('Tour_details'))

@push('css_or_js')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
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
            <!-- {{-- <div class="col-lg-4">
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
                                    $new_withdrowal = \App\Models\WithdrawalAmountHistory::where('vendor_id', auth('tour')->user()->relation_id)
                                        ->where('ex_id', $getData['id'])
                                        ->where('type', 'tour')
                                        ->first();
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
            </div> --}} -->
            <div class="col-lg-12">
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
                                    <a href="{{ route('tour-vendor.tour_visits.view', [$getData['Tour']['id']]) }}">
                                        {{ $getData['Tour']['tour_name'] }}
                                    </a>
                                    <br>
                                    <span class="h6">{{ translate('booking_date') }} : {{ date('d M Y, h:s:A', strtotime($getData['created_at'])) }}</span><br>
                                    <span class="h6">Pickup Date :{{ date('d M,Y', strtotime($getData['pickup_date'])) }}</span><br>
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
                                <img src="http://localhost:8000/assets/back-end/img/vendor-information.png"
                                    alt=""> Customer information
                            </h4>
                        </div>
                        @if ($getData['cab_assign'] != 0)
                        <div class="media flex-wrap gap-3">
                            <div class="">
                                <img class="avatar rounded-circle avatar-70"
                                    src="{{ getValidImage(path: 'storage/app/public/profile/' . ($getData['userData']['image'] ?? ''), type: 'backend-product') }}"
                                    alt="Image">
                            </div>
                            <div class="media-body d-flex flex-column gap-1">
                                <span
                                    class="title-color"><strong>{{ $getData['userData']['name'] }}</strong></span>
                                <span
                                    class="title-color break-all"><strong>{{ $getData['userData']['phone'] }}</strong></span>
                                @if ($getData['userData']['phone'] != $getData['userData']['email'])
                                <span class="title-color break-all">{{ $getData['userData']['email'] }}</span>
                                @endif
                            </div>
                        </div>
                        @else
                        <style>
                            .blinking {
                                color: gray;
                                animation: blink 2s infinite;
                            }

                            @keyframes blink {
                                0% {
                                    opacity: 0.6;
                                }

                                50% {
                                    opacity: 0;
                                }

                                100% {
                                    opacity: 1;
                                }
                            }
                        </style>
                        <div class="media flex-wrap gap-3 blinking">
                            <h4>Customer information will be displayed once the booking is accepted</h4>
                        </div>
                        @endif
                        <!-- </div> -->
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
                                                $tourPackages = \App\Models\TourCab::where('id', ($val['id'] ?? ''))->first();
                                                $images = getValidImage(path: 'storage/app/public/tour_and_travels/cab/' . ($tourPackages['image'] ?? ''), type: 'backend-product');
                                                $assign_cabs_use_allPackages = ($val['id'] ?? '');
                                                $assign_cabs_use_qtys = ($val['qty'] ?? 0);
                                            } elseif ($val['type'] == 'other' || $val['type'] == 'hotel' || $val['type'] == 'foods' || $val['type'] == 'food' || \Illuminate\Support\Str::startsWith($val['type'], 'other')) {
                                                $tourPackages = \App\Models\TourPackage::where('id', ($val['id'] ?? ''))->first();
                                                $images = getValidImage(path: 'storage/app/public/tour_and_travels/package/' . ($tourPackages['image'] ?? ''), type: 'backend-product');
                                            } else {
                                                $images = getValidImage(path: 'storage/app/public/tour_and_travels/package/', type: 'backend-product');
                                                $tourPackages = [];
                                            }
                                            ?>
                                            <td>
                                                @if ($val['type'] == 'ex_distance')
                                                <span class="fs-15 font-semibold">Ex Distance</span>
                                                @elseif($val['type'] == 'route')
                                                <span class="fs-15 font-semibold">Route</span>
                                                @elseif($val['type'] == 'per_head')
                                                <span class="fs-15 font-semibold">Per Head</span>
                                                @elseif($val['type'] == 'transport')
                                                <span class="fs-15 font-semibold">Ex Transport</span>
                                                @elseif($val['type'] == 'tax' || $val['type'] == 'cgst' || $val['type'] == 'sgst')
                                                <span class="fs-15 font-semibold">{{ $val['title']}}</span>
                                                @else
                                                <div class="media align-items-center">
                                                    <img class="d-block rounded"
                                                        src="{{ $images }}"
                                                        alt="{{ translate('image_Description') }}"
                                                        style="width: 80px;height: 72px;">
                                                    <div class="ml-1">
                                                        <small class="title-color"
                                                            data-title="{{ $tourPackages['name'] ?? '' }}"
                                                            role="tooltip" data-toggle="tooltip">
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
                                                <span class="fs-15 font-semibold">{{ $val['qty'] }}</span>
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
                                                @if ($val['type'] != 'ex_distance' && $getData['use_date'] != 0)
                                                <br>
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
            <?php $qty_cities = (($getData['Tour']['is_person_use'] == 1) ? 20 : 1);
            $perPeopleQty = 0;
            if ($getData['Tour']['is_person_use'] == 1) {
                $package_bookings = json_decode($getData['booking_package'], true);
                if (is_array($package_bookings)) {
                    foreach ($package_bookings as $item) {
                        if (isset($item['type']) && $item['type'] === 'per_head') {
                            $perPeopleQty = isset($item['qty']) ? (int)$item['qty'] : 1;
                            break;
                        }
                    }
                }
            }
            ?>
            <div class="col-sm-6">
                <div class="card h-100 refund-details-card--2">
                    <div class="card-body">
                        <h4 class="mb-3 text-capitalize">{{ translate('traveller_info') }}</h4>
                        <div class="key-val-list d-flex flex-column gap-2 min-width--60px">
                            <div class="key-val-list-item">
                                <form action="{{ route('tour-vendor.order.assign-accept') }}" method="post">
                                    @csrf
                                    <div class="row">
                                        <div class='col-md-12'>
                                            <label class="text-capitalize d-none">{{ translate('select_traveller_company') }}</label>
                                            <select class="form-control d-none">
                                                <?php $cabs_ids = 0; ?>
                                                <option value="" selected disabled>Select traveller Company
                                                </option>
                                                @if (!empty($company_list))
                                                @foreach ($company_list as $va)
                                                <?php $cabs_ids = $va['id']; ?>
                                                <option value="{{ $va['id'] }}" {{ $va['id'] == ($getData['company']['id'] ?? '') ? 'selected' : '' }}> {{ $va['company_name'] }} </option>
                                                @endforeach
                                                @endif
                                            </select>
                                            <input type="hidden" name='cab_id' value='{{ $cabs_ids }}'>
                                            <input type="hidden" name='id' value='{{ $getData["id"] }}'>
                                        </div>
                                        @if (\App\Models\TourOrder::where('id', $getData['id'])->where('cab_assign', 0)->exists())
                                        <div class="col-md-12 mt-2 text-center">
                                            @if($cabs_ids && ($getData['refund_status'] == 0 || $getData['refund_status'] == 3))
                                            <button type="submit" class="btn btn-success w-50">Accept</button>
                                            @elseif($cabs_ids && ($getData['refund_status'] == 1))

                                            @else
                                            <span class="text-danger font-weight-bolder">Vendor Profile Incomplete</span>
                                            @endif
                                        </div>
                                        @endif
                                        <div class="col-md-12 mt-1">
                                            <hr>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            @if ($getData['company'])
                            <div class="key-val-list-item d-flex gap-3">
                                <span class="text-capitalize">{{ translate('company_name') }}</span>:
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
                            <div>
                                <a class="btn btn-primary btn-sm"
                                    onclick="relatedOrderView(`{{ $getData['tour_id'] }}`,`{{ $getData['id'] }}`)">Related
                                    Order View </a>
                            </div>
                            <div class="row">
                                <div class="col-md-12 mt-2">
                                    <hr>
                                </div>
                                <?php if ($getData['Tour']['is_person_use'] == 1) { ?>

                                    <div class="col-md-12 my-2">
                                        <span>Total Person:{{ $perPeopleQty }}</span>&nbsp;&nbsp;|&nbsp;&nbsp;<span class="remaining-seats">Remining Person:0 </span>&nbsp;&nbsp;|&nbsp;&nbsp;<span class="extra-seats text-danger font-weight-bolder" style="font-size: 17px;">Ex. Person:0 </span>
                                        <!-- satish -->
                                    </div>
                                    <div class="col-md-12">
                                        <hr>
                                    </div>
                                <?php } ?>
                                <?php
                                $bookingPackage = json_decode($getData['booking_package'], true);
                                if (is_array($bookingPackage) && $getData['Tour']['tour_type'] == 'cities_tour') {
                                    foreach ($bookingPackage as $item) {
                                        if (isset($item['type']) && $item['type'] === 'cab' && isset($item['qty'])) {
                                            $qty_cities = $item['qty'];
                                            break;
                                        }
                                    }
                                }
                                ?>

                                <div class="col-md-6">
                                    <?php
                                    $travellerCabIds = json_decode($getData['traveller_cab_id'], true) ?? [];
                                    ?>
                                    <select class="form-control  select2" data-max="{{ $qty_cities }}" onchange="{{ (($getData['Tour']['is_person_use'] == 1)?'perHeadCabassign(this)':'') }}" data-type="cab"
                                        {{ $getData['Tour']['tour_type'] == 'cities_tour' ? 'multiple' : '' }} {{ (($getData['Tour']['is_person_use'] == 1)?'multiple':'') }}>

                                        @if ($cabDetails)
                                        @foreach ($cabDetails as $val)
                                        @if ($assign_cabs == $val['cab_id'] || ($getData['Tour']['is_person_use'] == 1))
                                        <?php
                                        $getcheckQty = \App\Models\TourOrder::whereRaw('JSON_CONTAINS(traveller_cab_id, ?)', [json_encode((string) $val['id'])])
                                            ->where('tour_id', $getData['tour_id'])
                                            ->where('pickup_status', 0)
                                            ->where('pickup_date', [$getData['pickup_date']])
                                            ->select('booking_package', 'tour_id')
                                            ->with(['Tour'])
                                            ->get()
                                            ->map(function ($tourVisit) {
                                                $packages = json_decode($tourVisit->booking_package, true);
                                                $cabPackage = collect($packages)->firstWhere('type', 'cab');
                                                if (!$cabPackage) {
                                                    $cabPackage = collect($packages)->firstWhere('type', 'per_head');
                                                }
                                                return $cabPackage ? (int) $cabPackage['qty'] : 0; // Ensure qty is integer
                                            })
                                            ->sum();
                                        ?>
                                        <option value="{{ $val['id'] }}"
                                            @if (in_array($val['id'], (array) $travellerCabIds)) selected @endif data-seats="{{ $val['Cabs']['seats']??'' }}">
                                            {{ $val['Cabs']['name'] }} - {{ $val['reg_number'] }} {{(($getData['Tour']['is_person_use'] == 1)? "(".$val['Cabs']['seats']."seat)":'')}}
                                            {{ $getData['Tour']['use_date'] == 1 || $getData['Tour']['use_date'] == 4 ? '( Total assign:' . $getcheckQty . ' ,Available:' . ((int) ($val['Cabs']['seats'] ?? 0) - (int) $getcheckQty) . ' )' : '' }}
                                        </option>
                                        @endif
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <?php
                                    $travellerdriverIds = json_decode($getData['traveller_driver_id'], true) ?? [];
                                    ?>
                                    <select class="form-control select3" data-max="{{ $qty_cities }}" onchange="{{ (($getData['Tour']['is_person_use'] == 1)?'perHeadCabassign(this)':'') }}" data-type="driver"
                                        {{ $getData['Tour']['tour_type'] == 'cities_tour' ? 'multiple' : '' }} {{ (($getData['Tour']['is_person_use'] == 1)?'multiple':'') }}>
                                        @if ($travellerDetails)
                                        @foreach ($travellerDetails as $vval)
                                        <option value="{{ $vval['id'] }}"
                                            @if (in_array($vval['id'], (array) $travellerdriverIds)) selected @endif>
                                            {{ $vval['name'] }} - ({{ $vval['phone'] }})
                                        </option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-12 mt-2 mb-2">
                                    @if ($getData['drop_status'] == 0)
                                    <form method="post" action="{{ route('tour-vendor.order.cab-driver-assign') }}">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $getData['id'] }}">
                                        <input type="hidden" name="days" value="{{ (($getData['Tour']['number_of_day'] == '0.5') ? 1 : $getData['Tour']['number_of_day']) }}">
                                        <input type="hidden" name="traveller_cab_id" value="{{ $getData['traveller_cab_id'] }}" class="option_cab_id">
                                        <input type="hidden" name="traveller_driver_id" value="{{ $getData['traveller_driver_id'] }}" class="option_driver_id">
                                        <button type="submit" class="form-control btn btn-success assign_status" {{ (($getData['Tour']['is_person_use'] == 1)?'disabled="disabled"':'') }}>Assigned</button>
                                    </form>
                                    @endif
                                </div>
                            </div>
                            @endif
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
                                <?php
                                $p_checkIndex = 1;
                                ?>
                                @if (!empty($getData['Tour']['cab_list_price']) && json_decode($getData['Tour']['cab_list_price'], true) && ($getData['Tour']['is_person_use'] == 0))
                                @foreach (json_decode($getData['Tour']['cab_list_price'], true) as $p_info)
                                @if ($assign_cabs_use_allPackages == $p_info['cab_id'] && $p_checkIndex == 1)
                                <tr>
                                    <td>
                                        <?php $tourPackages = \App\Models\TourCab::where('id', $p_info['cab_id'])->first();
                                        $p_checkIndex++;
                                        ?>
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
                                        {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: ((float) $p_info['price'] ?? 0) * $assign_cabs_use_qtys ?? 1), currencyCode: getCurrencyCode()) }}
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "Select an Cabs",
            allowClear: true,
            maximumSelectionLength: "{{ ($getData['Tour']['tour_type'] == 'cities_tour' || $getData['Tour']['is_person_use'] == 1) ? $qty_cities : 2 }}"

        });
        $('.select3').select2({
            placeholder: "Select an Driver",
            allowClear: true,
            maximumSelectionLength: "{{ ($getData['Tour']['tour_type'] == 'cities_tour' || $getData['Tour']['is_person_use'] == 1) ? $qty_cities : 2 }}"
        });
    });

    $('.select2').on('change', function() {
        let selectedValues = $(this).val();

        if (selectedValues) {
            let formattedValues = Array.isArray(selectedValues) ? selectedValues.map(value => String(value)) : [
                String(selectedValues)
            ];
            $('.option_cab_id').val(JSON.stringify(formattedValues));
        } else {
            $('.option_cab_id').val($(this).val());
        }
    });
    $('.select3').on('change', function() {
        let selectedValues = $(this).val();
        if (selectedValues) {
            let formattedValues = Array.isArray(selectedValues) ? selectedValues.map(value => String(value)) : [
                String(selectedValues)
            ];
            $('.option_driver_id').val(JSON.stringify(formattedValues));
        } else {
            $('.option_driver_id').val($(this).val());
        }
    });
</script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script>
    $('#tourOrdersTable').DataTable();

    function relatedOrderView(tour_id, order_id) {
        $.ajax({
            url: "{{ route('tour.related-order-view') }}",
            data: {
                type: "vendor",
                user_id: "{{ auth('tour')->user()->relation_id }}",
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

    function perHeadCabassign(that) {
        let selectedValues = $('.select2').val();
        let seatCounts = [];
        let seatCountsTotal = 0;
        if (selectedValues && Array.isArray(selectedValues)) {
            selectedValues.forEach(value => {
                let seat = $('.select2 option[value="' + value + '"]').attr('data-seats');
                seatCountsTotal += Number(seat);
                seatCounts.push({
                    value: value,
                    seats: parseInt(seat) || 0
                });
            });
        }
        var qtys = "{{$perPeopleQty}}";
        $('.remaining-seats').text(`Remining person:${((seatCountsTotal >= qtys)? 0 :(seatCountsTotal - qtys))}`);
        $('.extra-seats').text(`Ex. seats:${(seatCountsTotal - qtys)}`);
        if ($(that).data('type') == 'cab') {
            let cabCount = $('.select2').val()?.length || 0;
            $('.select3').val(null).trigger('change');
            $('.select3').select2('destroy');
            $('.select3').select2({
                placeholder: "Select an Driver",
                allowClear: true,
                maximumSelectionLength: cabCount
            });
        }
        let CabLenght = $('.select2').val()?.length || 0;
        let driverLeght = $('.select3').val()?.length || 0;
        if (CabLenght == driverLeght && (seatCountsTotal >= qtys)) {
            $('.assign_status').attr('disabled', false);
        } else {
            $('.assign_status').attr('disabled', true);

        }
    }
</script>
@endpush