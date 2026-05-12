<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ 'Invoice' }}</title>
    <meta http-equiv="Content-Type" content="text/html;" />
    <meta charset="UTF-8">
    <link rel="stylesheet" href="{{ dynamicAsset(path: 'public/assets/back-end/css/invoice.css') }}">
    <style>
        body {
            font-family: "Segoe UI Emoji", "Noto Color Emoji", "Apple Color Emoji", sans-serif;
        }
    </style>
</head>

<body>
    <div class="invoice-box">
        <table class="table-no-border" style="width: 100%; margin-bottom: 20px;">
            <tr>
                <td style="text-align: left; vertical-align: middle;">
                    <img height="40" src="{{ dynamicStorage(path: "storage/app/public/company/".getWebConfig(name: 'company_web_logo')) }}" alt="">
                </td>
                <td style="text-align: right; vertical-align: middle;">
                    <h4 style="margin: 0;"><strong>INVOICE</strong></h4>
                </td>
            </tr>
        </table>

        <table class="table-no-border" style="margin-bottom: 20px;">
            <tr>
                <td style="width: 50%; text-align: left;">
                    <strong>From:</strong><br>
                    <strong>Mahakal AstroTech Pvt Ltd</strong><br>
                    {{ getWebConfig(name: 'shop_address') ?? 'N/A' }}<br>
                    GSTIN: {{ getWebConfig(name: 'company_gst') ?? 'N/A' }}<br>
                    PAN: {{ getWebConfig(name: 'company_pan') ?? 'N/A' }}
                </td>
                <td style="width: 50%; text-align: left;">
                    Order No: {{ $tourOrders['order_id'] }}<br>
                    Date: {{ date('d-m-Y h:i:s a', strtotime($tourOrders['created_at'])) }}<br>
                    <strong>To:</strong><br>
                    <strong>{{ $tourOrders['userdata']['f_name'] ?? '' }} {{ $tourOrders['userdata']['l_name'] ?? '' }}</strong><br>
                    <strong>Phone:</strong> {{ $tourOrders['userdata']['phone'] ?? '' }}<br>
                    <strong>Email:</strong> {{ $tourOrders['userdata']['email'] ?? '' }}
                </td>
            </tr>
        </table>

        <?php $ex_distance = 0;
        if (!empty($tourOrders['booking_package'])) {
            $decodedPackages = json_decode($tourOrders['booking_package'], true);
            if (is_array($decodedPackages)) {
                foreach ($decodedPackages as $val) {
                    if (isset($val['id'], $val['type'], $val['price']) && $val['id'] == 0 && $val['type'] == 'ex_distance' && $val['price'] > 0 && ($tourOrders['Tour']['is_person_use'] ?? 0) == 0) {
                        $ex_distance = $val['price'];
                        break;
                    }
                }
            }
        }
        ?>
        <table class="table-bordered">
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Description</th>
                    <th>Qty</th>
                    <th>Rate</th>
                    <th>Tax</th>
                    <th>Tax Amt</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $p_checkIndex = 0;
                $tax_summary = [];
                $tax_summary_amounts = 0;
                if (!empty($tourOrders['booking_package'])) {
                    $decodedPackages = json_decode($tourOrders['booking_package'], true);
                    if (is_array($decodedPackages)) {
                        $indexs = 1;
                        foreach ($decodedPackages as $val) {
                            if ((((float)$val['price'] ?? 0) >= 0) || $val['type'] == "route") {
                                if ($val['type'] == 'cab') {
                                    if ($tourOrders['Tour']['use_date'] != 0) {
                                        $p_checkIndex = ($val['qty']);
                                    }
                                    $tourPackages = \App\Models\TourCab::where('id', ($val['id'] ?? ''))->first();
                                    $images = getValidImage(path: 'storage/app/public/tour_and_travels/cab/' . ($tourPackages['image'] ?? ''), type: 'backend-product');
                                    $tax_summary_amounts += $val['tax_price'] ?? 0;
                                } elseif ($val['type'] == 'other' || $val['type'] == 'food' || $val['type'] == 'foods' || $val['type'] == 'hotel' || \Illuminate\Support\Str::startsWith($val['type'], 'other')) {
                                    if ($tourOrders['Tour']['use_date'] != 0 && $tourOrders['Tour']['is_person_use'] == 0) {
                                        continue;
                                    }
                                    $tourPackages = \App\Models\TourPackage::where('id', ($val['id'] ?? ''))->first();
                                    $images = getValidImage(path: 'storage/app/public/tour_and_travels/package/' . ($tourPackages['image'] ?? ''), type: 'backend-product');
                                } elseif ($val['type'] == 'per_head' || $val['type'] == 'transport') {
                                    $tourPackages = [];
                                    $images = getValidImage(path: 'storage/app/public/tour_and_travels/package/', type: 'backend-product');
                                    $tax_summary_amounts += $val['tax_price'] ?? 0;
                                } elseif ($val['type'] == 'tax' || $val['type'] == 'cgst' || $val['type'] == 'sgst') {
                                    $tax_summary_amounts += $val['tax_price'] ?? 0;
                                    continue;
                                } elseif ($val['type'] == 'route') {
                                    continue;
                                } else {
                                    $tourPackages = [];
                                    $images = getValidImage(path: 'storage/app/public/tour_and_travels/package/', type: 'backend-product');
                                }
                ?>
                                <tr>
                                    <td><?php echo $indexs++; ?></td>
                                    <td>
                                        <div class="media align-items-center gap-5">
                                            @if ($val['type'] == 'ex_distance')
                                            <small class=" w-50 font-semibold text-center">Ex Distance</small>
                                            @elseif($val['type'] == 'route')
                                            <small class=" w-50 font-semibold text-center">Route</small>
                                            @elseif($val['type'] == 'per_head')
                                            <small class=" w-50 font-semibold text-center">Per Head</small>
                                            @elseif($val['type'] == 'transport')
                                            <small class=" w-50 font-semibold text-center">Ex Transport</small>
                                            @else
                                            <!-- {{-- <img class="d-block get-view-by-onclick rounded" src="{{ $images }}"  alt="{{ translate('image_Description') }}" style="width: 80px;height: 72px;">--}} -->
                                            <div class="ml-1">
                                                <small class="title-color"
                                                    data-title="{{ $tourPackages['name'] ?? '' }}"
                                                    role="tooltip" data-toggle="tooltip">
                                                    {{ $tourPackages['name'] ?? '' }} <br>
                                                    @if (!empty($val['seats'] ?? '') && $val['type'] == 'cab')
                                                    {{ $val['seats'] ?? '' }}
                                                    {{ $val['type'] == 'cab' ? 'seats' : 'people' }}
                                                    @endif
                                                </small>
                                            </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="media align-items-center gap-5">
                                            @if ($val['type'] == 'ex_distance')
                                            <small class="fs-15 font-semibold">Km: {{ $val['qty'] }}</small>
                                            @elseif($val['type'] == 'route' || $val['type'] == 'transport')
                                            <small class="fs-15 font-semibold"></small>
                                            @else
                                            <small class="fs-15 font-semibold">
                                                @if ($val['type'] == 'cab')
                                                @if (($tourOrders['Tour']['tour_type'] ?? '') == 'cities_tour')
                                                cabs :
                                                @else
                                                people :
                                                @endif
                                                @else
                                                people :
                                                @endif
                                                {{ $val['qty'] }}
                                            </small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="">
                                            <?php if ($val['type'] == 'cab' || $val['type'] == 'per_head' || ($val['type'] == 'transport')) { ?>
                                                <span class="fs-15 font-semibold">
                                                    @if(($tourOrders['Tour']['is_person_use'] ?? 0) == 1)
                                                    {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: ((float) $val['price'] ?? 0)), currencyCode: getCurrencyCode()) }}
                                                    @else
                                                    {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: ((float) $val['price'] ?? 0) - $ex_distance), currencyCode: getCurrencyCode()) }}
                                                    @endif
                                                </span>
                                                <?php } else {
                                                if ($tourOrders['use_date'] == 0 && $val['type'] != 'route') { ?>
                                                    <span class="fs-15 font-semibold">
                                                        {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: (float) $val['pprice'] ?? 0), currencyCode: getCurrencyCode()) }}
                                                    </span>
                                                <?php } elseif ($val['type'] == 'ex_distance') { ?>
                                                    <span class="fs-15 font-semibold">
                                                        {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: (float) ($val['pprice'] ?? 0) ), currencyCode: getCurrencyCode()) }}
                                                    </span>
                                                <?php  } elseif ($tourOrders['Tour']['is_person_use'] == '1' && ($val['type'] == 'hotel' || $val['type'] == 'foods')) { ?>
                                                    <span class="fs-15 font-semibold ">
                                                        {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: (float) $val['pprice'] ?? 0), currencyCode: getCurrencyCode()) }}
                                                    </span>
                                                <?php  } elseif ($val['type'] == 'route') { ?>
                                                    <span class="fs-15 font-semibold">
                                                        {{ ucwords(str_replace('_', ' ', $val['price'] ?? '')) }}
                                                    </span>
                                                <?php   } else { ?>
                                                    <span
                                                        class="fs-15 text-success">{{ translate('included in The Price') }}</span>
                                            <?php }
                                            } ?>
                                        </div>
                                    </td>
                                    <td>{{ ($val['gst'] ?? ($val['tax']??"") ) }}%</td>
                                    <td>{{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: (float)($val['tax_price']??0)), currencyCode: getCurrencyCode()) }}</td>
                                    <td>
                                        @if ($val['type'] == 'ex_distance')
                                        {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: (float)($val['price']??0) + ($val['tax_price']??0) ), currencyCode: getCurrencyCode()) }}
                                        @else
                                        {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: (float)($val['total_price']??0)), currencyCode: getCurrencyCode()) }}
                                        @endif
                                    </td>
                                </tr>
                <?php }
                        }
                    }
                } ?>
            </tbody>
        </table>

        <table class="summary-final-table">
            <tr>
                <td class="summary-left">
                    <strong>Note:</strong> This is a system-generated invoice and does not require a physical signature.
                </td>
                <td class="summary-right">
                    <table class="summary-table-inner">
                        <tr>
                            <td>Sub Total</td>
                            <td>
                                {{-- setCurrencySymbol(amount: ((($tourOrders['amount'] ?? 0) +($tourOrders['coupon_amount'] ?? 0)) - ($tourOrders['gst_amount'] ?? 0) - $ex_distance), currencyCode: getCurrencyCode()) --}}
                                @if($tourOrders['part_payment'] == 'part')
                                {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: ((float) $tourOrders['amount'] ?? 0) + ((float) $tourOrders['amount'] ?? 0) + ((float) $tourOrders['coupon_amount'] ?? 0) - ($tourOrders['gst_amount']??0)), currencyCode: getCurrencyCode()) }}
                                @elseif($tourOrders['part_payment'] == 'custom')
                                {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: (((float) $tourOrders['order_amount'] ?? 0)) + ((float) $tourOrders['coupon_amount'] ?? 0) - ($tourOrders['gst_amount']??0)), currencyCode: getCurrencyCode()) }}
                                @else
                                {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: ((float) $tourOrders['amount'] ?? 0) + ((float) $tourOrders['coupon_amount'] ?? 0) - ($tourOrders['gst_amount']??0)), currencyCode: getCurrencyCode()) }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>Total Tax</td>
                            <td>{{ setCurrencySymbol(amount: ($tourOrders['gst_amount'] ?? 0), currencyCode: getCurrencyCode()) }}</td>
                        </tr>
                        <tr>
                            <td>Discount Amount</td>
                            <td>-{{ setCurrencySymbol(amount: ($tourOrders['coupon_amount'] ?? 0), currencyCode: getCurrencyCode()) }}</td>
                        </tr>
                        <tr>
                            <td>Total Amount</td>
                            <td><strong>{{ setCurrencySymbol(amount: ($tourOrders['order_amount']??0), currencyCode: getCurrencyCode()) }}</strong></td>
                        </tr>
                        <tr>
                            <td>Paid Amount</td>
                            <td><strong>{{ setCurrencySymbol(amount: ($tourOrders['amount']??0), currencyCode: getCurrencyCode()) }}</strong></td>
                        </tr>
                        @if($tourOrders['part_payment'] == 'part' || $tourOrders['part_payment'] == 'custom')
                        <tr class="border-top">
                            <td>Remaining Pay</td>
                            <td><strong>
                                    @if($tourOrders['part_payment'] == 'part')
                                    {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: (float) $tourOrders['amount'] ?? 0), currencyCode: getCurrencyCode()) }}
                                    @else
                                    {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: (float)($tourOrders['order_amount'] ?? 0) - ($tourOrders['amount'] ?? 0)), currencyCode: getCurrencyCode()) }}
                                    @endif
                                    <strong>
                            </td>
                        </tr>
                        @endif                        
                    </table>
                </td>
            </tr>
        </table>
        <div class="footer-note">
            <strong></strong>
        </div>
        <br>
        <div class="content-position-y">
            <div class="row">
                @if (!empty($tourOrders['Tour']['tour_type'] ?? ''))
                @php
                $getSpecial_tour = \App\Models\TourRefundPolicy::where('status',1)->where('type', $tourOrders['Tour']['tour_type'])->orderBy('day', 'desc')->get();
                @endphp
                @if (!empty($getSpecial_tour))
                @php $data_check = ''; @endphp
                @foreach ($getSpecial_tour as $val)
                @php
                $pickupDate = strtotime($tourOrders['pickup_date'].' '.$tourOrders['pickup_time'] .' -' .$val['day'] .' hours');
                $createdAt = strtotime($tourOrders['created_at']);
                @endphp

                @if ($pickupDate > $createdAt)
                @php
                $data_check = 'access';
                break;
                @endphp
                @endif
                @endforeach
                @if ($data_check == 'access')
                <table class="table">
                    <thead>
                        <tr>
                            <td colspan="3" class="text-center"
                                style="padding: 5px; background-color: gainsboro;">
                                {{ ucwords('cancellation policy') }}
                            </td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($getSpecial_tour as $val)
                        @php
                        $pickupDate = strtotime($tourOrders['pickup_date'].' '.$tourOrders['pickup_time'].' -' .$val['day'] .' hours');
                        $createdAt = strtotime($tourOrders['created_at']);
                        @endphp
                        @if ($pickupDate > $createdAt)
                        <tr>
                            <td>
                                {!! preg_replace('/\{\{\s*\$date\s*\}\}/','<strong>' . date('d-m-Y h:i A', strtotime($tourOrders['pickup_date'].' '.$tourOrders['pickup_time']. ' -' . $val['day'] . ' hours')) . '</strong>',$val['message']) !!}
                            </td>
                            <td>{{ $val['percentage'] }}%</td>
                            <td>
                                <?php
                                $total_amounts = 0;
                                $total_amounts = (float) $tourOrders['amount'] ?? 0;
                                $total_amounts = ($total_amounts * ((float) ($val['percentage'] ?? 0))) / 100;
                                ?>
                                {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $total_amounts), currencyCode: getCurrencyCode()) }}
                            </td>
                        </tr>
                        @endif
                        @endforeach
                    </tbody>
                </table>
                @endif
                @endif
                @endif
            </div>
        </div>
        <div class="row">
            <section>
                <table>
                    <tr>
                        <th class="content-position-y bg-light py-4">
                            <div class="d-flex justify-content-center gap-2">
                                <div class="mb-2">
                                    <img height="10" src="{{ theme_asset(path: 'public/assets/front-end/img/icons/telephone.png') }}"
                                        alt="">
                                    {{ ucwords('phone')}}
                                    : {{ getWebConfig(name: 'company_phone') }}
                                </div>
                                <div class="mb-2">
                                    <img height="10" src="{{ theme_asset(path: 'public/assets/front-end/img/icons/email.png') }}" alt="">
                                    {{ ucwords('email')}}
                                    : {{ getWebConfig(name: 'company_email') }}
                                </div>
                            </div>
                            <div class="mb-2">
                                <img height="10" src="{{ theme_asset(path: 'public/assets/front-end/img/icons/web.png') }}" alt="">
                                {{ ucwords('website')}}
                                : {{url('/')}}
                            </div>
                            <div>
                                {{ ucwords('all copy right reserved © '.date('Y').' ')}} {{($companyName??"") }}
                            </div>
                        </th>
                    </tr>
                </table>
            </section>
        </div>
    </div>
</body>

</html>