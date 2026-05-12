@extends('layouts.front-end.app')
@section('title', translate('order_Details'))
@push('css_or_js')
    <style>
        .section-card .card-overlay h6 {
            font-size: 16px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .section-card .card-overlay p,
        .section-card .card-overlay span {
            font-size: 14px;
            font-weight: 500;
            line-height: 1.6;
        }

        .section-card .card-overlay strong {
            font-weight: 700;
            color: #fff;
        }

        .section-card {
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        }
    </style>
@endpush
@section('content')
    <div class="container pb-5 mb-2 mb-md-4 mt-3 rtl __inline-47 text-align-direction">
        <div class="row g-3">
            @include('web-views.partials._profile-aside')
            <section class="col-lg-9">
                @include('web-views.users-profile.service-details.service-order-partial')
                <div class="bg-white border-lg rounded mobile-full">
                    <div class="p-lg-3 p-0">
                        <div class="card border-sm">
                            <div class="p-lg-3">
                                <div class="row g-3 mb-4">
                                    {{-- Payment Info --}}
                                    <div class="col-lg-4 col-md-6">
                                        <div class="p-3 border rounded h-100 section-card"
                                            style="background: url('{{ asset('public/assets/front-end/img/backgroundimage.png') }}') center/cover;">
                                            <div class="card-overlay">
                                                <h6 class="fs-13 font-bold mb-3 text-capitalize text-white">
                                                    {{ translate('payment_info') }}
                                                </h6>
                                                <div class="fs-12 mb-2 text-white">
                                                    <span>{{ translate('payment_status') }}:</span>
                                                    @php
                                                        $status = $order['payment_status'];
                                                    @endphp

                                                    @if ($status == 1)
                                                        <span
                                                            class="badge bg-success text-white px-2">{{ translate('paid') }}</span>
                                                    @elseif ($status == 0)
                                                        <span
                                                            class="badge bg-danger text-white px-2">{{ translate('pending') }}</span>
                                                    @elseif ($status == 2)
                                                        <span
                                                            class="badge bg-danger text-white px-2">{{ translate('failed') }}</span>
                                                    @else
                                                        <span
                                                            class="badge bg-info text-dark px-2">{{ translate('failed') }}</span>
                                                    @endif
                                                </div>
                                                <div class="fs-12 text-white">
                                                    <span>{{ translate('payment_method') }}:</span>
                                                    @if ($order['payment_id'] && $order['wallet_translation_id'])
                                                        <span class="">Online/Wallet</span>
                                                    @elseif ($order['payment_id'])
                                                        <span class=""> Online</span>
                                                    @elseif ($order['wallet_translation_id'])
                                                        <span class="">Wallet</span>
                                                    @else
                                                        <span class=""> Online/Wallet</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Bill Address --}}
                                    <div class="col-lg-4 col-md-6">
                                        <div class="p-3 border rounded h-100 section-card"
                                            style="background: url('{{ asset('public/assets/front-end/img/backgroundimage.png') }}') center/cover;">
                                            <div class="card-overlay">
                                                <h6 class="fs-13 font-bold mb-3 text-capitalize text-white">
                                                    {{ translate('bill_address') }}
                                                </h6>
                                                <div class="fs-12 text-white">
                                                    <p class="mb-2"><strong>{{ translate('name') }}:</strong>
                                                        {{ $order['customer']['name'] ?? 'N/A' }}</p>
                                                    <p class="mb-2"><strong>{{ translate('phone') }}:</strong>
                                                        {{ $order['customer']['phone'] ?? 'N/A' }}</p>
                                                    <p class="mb-0"><strong>{{ translate('email') }}:</strong>
                                                        {{ $order['customer']['email'] ?? 'N/A' }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Prasad Details --}}
                                    @if ($order->is_prashad == '1')
                                        <div class="col-lg-4 col-md-6">
                                            <div class="p-3 border rounded h-100 section-card"
                                                style="background: url('{{ asset('public/assets/front-end/img/backgroundimage.png') }}') center/cover;">
                                                <div class="card-overlay">
                                                    <h6 class="fs-13 font-bold mb-3 text-capitalize text-white">
                                                        {{ translate('prasad_details') }}
                                                    </h6>
                                                    <div class="fs-12 text-white">
                                                        <p class="mb-2"><strong>{{ translate('name') }}:</strong>
                                                            {{ $order['customer']['name'] ?? 'N/A' }}</p>
                                                        <p class="mb-2"><strong>{{ translate('phone') }}:</strong>
                                                            {{ $order['customer']['phone'] ?? 'N/A' }}</p>
                                                        <p class="mb-2"><strong>{{ translate('city') }} /
                                                                {{ translate('zip') }}:</strong>
                                                            {{ $order->city ?? 'N/A' }}, {{ $order->pincode ?? '' }}
                                                        </p>
                                                        <p class="mb-0"><strong>{{ translate('address') }}:</strong>
                                                            {{ $order->house_no ?? '' }}, {{ $order->area ?? '' }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="payment mb-3">
                                    <div class="table-responsive d-none d-lg-block">
                                        <table class="table table-hover table-bordered align-middle text-center">
                                            <thead class="thead-light text-capitalize bg-light">
                                                <tr class="fs-13 font-semibold text-secondary">
                                                    <th>{{ translate('puja_name') }}</th>
                                                    <th>{{ translate('price') }}</th>
                                                    <th>{{ translate('Prashad') }}</th>
                                                </tr>
                                            </thead>
                                            @php
                                                if (Str::startsWith($order['order_id'], ['APJ', 'VPJ'])) {
                                                    $poojaData = $order['vippoojas'] ?? [];
                                                    $folder = 'pooja/vip';
                                                    $pujavenue = '';
                                                    $bookingdate = $order->booking_date;
                                                } else {
                                                    $poojaData = $order['services'] ?? [];
                                                    $folder = 'pooja';
                                                    $pujavenue = $order['services']['pooja_venue'];
                                                    $bookingdate = $order->booking_date;
                                                }
                                                $thumbnail = $poojaData['thumbnail'] ?? '';
                                            @endphp
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-2">

                                                            <img class="rounded shadow-sm"
                                                                src="{{ getValidImage(path: 'storage/app/public/' . $folder . '/thumbnail/' . $thumbnail, type: 'backend-product') }}"
                                                                alt="{{ translate('image_Description') }}"
                                                                style="width:50px; height:50px; object-fit:cover;">

                                                            <div>
                                                                <h6 class="title-color mb-0"
                                                                    style="max-width: 180px; white-space: normal; word-wrap: break-word;">
                                                                    {{ $poojaData['name'] ?? 'ServiceName' }}
                                                                    <br>
                                                                    {{ $pujavenue }}
                                                                </h6>
                                                            </div>
                                                        </div>
                                                    </td>

                                                    <td class="fw-bold text-success">
                                                        {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $order['package_price']), currencyCode: getCurrencyCode()) }}
                                                        <br>
                                                        {{ $order['leads']['package_name'] }}
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="badge {{ $order['is_prashad'] == 1 ? 'bg-success' : 'bg-danger' }}">
                                                            {{ $order['is_prashad'] == 1 ? 'Yes' : 'No' }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    {{-- Mobile View (Card Style) --}}
                                    <div class="d-block d-lg-none">
                                        <div class="card shadow-sm mb-3 border-0 rounded-3">
                                            <div class="card-body d-flex flex-column gap-2">
                                                <div class="d-flex align-items-center gap-2">
                                                    <img class="rounded"
                                                        src="{{ getValidImage(path: 'storage/app/public/' . $folder . '/thumbnail/' . $thumbnail, type: 'backend-product') }}"
                                                        alt="{{ translate('image_Description') }}"
                                                        style="width:60px; height:60px; object-fit:cover;">
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-1 fw-bold text-truncate">
                                                            {{ Str::words($poojaData['name'] ?? 'N/A', 20, '...') }}</h6>
                                                        <div class="text-success fw-semibold">
                                                            {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $order['package_price']), currencyCode: getCurrencyCode()) }}
                                                        </div>
                                                        <span
                                                            class="badge {{ $order['is_prashad'] == 1 ? 'bg-success' : 'bg-danger' }}">
                                                            {{ $order['is_prashad'] == 1 ? 'Yes' : 'No' }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @if (count($order['product_leads']) > 0)
                                    <div class="table-responsive d-none d-lg-block">
                                        <table class="table table-hover table-bordered align-middle text-center">
                                            <thead class="thead-light bg-light">
                                                <tr class="fs-13 font-semibold text-secondary">
                                                    <th>{{ translate('Cherity_Details') }}</th>
                                                    <th>{{ translate('qty') }}</th>
                                                    <th>{{ translate('Price') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($order['product_leads'] as $productLeads)
                                                    @php
                                                        $product = \App\Models\Product::find(
                                                            $productLeads['product_id'],
                                                        );
                                                    @endphp
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center gap-2">
                                                                @if (!empty($product))
                                                                    <img class="rounded shadow-sm get-view-by-onclick"
                                                                        data-link="{{ route('product', $product->slug) }}"
                                                                        src="{{ getValidImage(path: 'storage/app/public/product/thumbnail/' . ($product->thumbnail ?? ''), type: 'product') }}"
                                                                        alt="{{ translate('product') }}"
                                                                        style="width: 60px; height: 60px; object-fit: cover; cursor: pointer;">
                                                                @else
                                                                    <img class="rounded shadow-sm"
                                                                        src="{{ asset('public/assets/img/placeholder.png') }}"
                                                                        alt="{{ translate('product') }}"
                                                                        style="width: 60px; height: 60px; object-fit: cover;">
                                                                @endif

                                                                <h6 class="title-color mb-0"
                                                                    style="max-width: 180px; white-space: normal; word-wrap: break-word;">
                                                                    {{ $productLeads['product_name'] }}
                                                                </h6>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span
                                                                class="badge bg-primary fs-14">{{ $productLeads['qty'] }}</span>
                                                        </td>
                                                        <td class="fw-bold text-success">
                                                            {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $productLeads['final_price']), currencyCode: getCurrencyCode()) }}
                                                        </td>
                                                    </tr>
                                                @endforeach


                                            </tbody>
                                        </table>
                                    </div>

                                    {{-- Mobile View as Card Layout --}}
                                    <div class="d-block d-lg-none">
                                        @foreach ($order['product_leads'] as $productLeads)
                                            <div class="card shadow-sm mb-3 border-0 rounded-3">
                                                <div class="card-body d-flex flex-column gap-2">
                                                    <h6 class="fw-bold mb-1 text-truncate">
                                                        {{ Str::words($productLeads['product_name'], 20, '...') }}</h6>
                                                    <div class="d-flex justify-content-between">
                                                        <span class="text-muted">{{ translate('qty') }}:</span>
                                                        <span class="badge bg-primary">{{ $productLeads['qty'] }}</span>
                                                    </div>
                                                    <div class="d-flex justify-content-between">
                                                        <span class="text-muted">{{ translate('Price') }}:</span>
                                                        <span class="fw-bold text-success">
                                                            {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $productLeads['final_price']), currencyCode: getCurrencyCode()) }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                            </div>
                        </div>
                        <div class="row d-flex justify-content-end mt-3">
                            <div class="col-md-8 col-lg-5">
                                <div class="card shadow-sm border-0 rounded-3">
                                    <div class="card-body p-3">
                                        {{-- Desktop Table View --}}
                                        <div class="table-responsive d-none d-lg-block">
                                            <table class="table table-borderless align-middle mb-0">
                                                <tbody class="totals">
                                                    <tr>
                                                        <td class="text-start">
                                                            <span class="fw-semibold">{{ translate('Item') }}</span>
                                                        </td>
                                                        <td class="text-end">
                                                            <span class="fw-semibold">{{ translate('Price') }}</span>
                                                        </td>
                                                        <td class="text-end">
                                                            <span class="fs-15 fw-bold">{{ $order->qty }}</span>
                                                        </td>
                                                    </tr>

                                                    <tr class="border-top">
                                                        <td class="text-start">
                                                            <span class="text-muted">{{ translate('Subtotal') }}</span>
                                                        </td>
                                                        <td colspan="2" class="text-end">
                                                            <span class="fs-15 fw-bold text-success">
                                                                {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $order['package_price']), currencyCode: getCurrencyCode()) }}
                                                            </span>
                                                        </td>
                                                    </tr>

                                                    @if (count($order['product_leads']) > 0)
                                                        <tr>
                                                            <td class="text-start">
                                                                <span
                                                                    class="text-muted">{{ translate('Cherity_Price') }}</span>
                                                            </td>
                                                            <td colspan="2" class="text-end">
                                                                <span class="fs-15 fw-bold">
                                                                    @php
                                                                        $totalSum = 0;
                                                                    @endphp
                                                                    @foreach ($order['product_leads'] as $productLeads)
                                                                        @php
                                                                            $convertedPrice = usdToDefaultCurrency(
                                                                                amount: $productLeads['final_price'],
                                                                            );
                                                                            $totalSum += $convertedPrice;
                                                                        @endphp
                                                                    @endforeach
                                                                    {{ setCurrencySymbol(amount: $totalSum, currencyCode: getCurrencyCode()) }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    @endif

                                                    <tr>
                                                        <td class="text-start">
                                                            <span
                                                                class="text-muted">{{ translate('Amount_Paid_(via_Wallet)') }}</span>
                                                        </td>
                                                        <td colspan="2" class="text-end">
                                                            <span class="fs-15 fw-semibold text-danger">
                                                                - {{ webCurrencyConverter(amount: $order->wallet_amount) }}
                                                            </span>
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td class="text-start">
                                                            <span
                                                                class="text-muted">{{ translate('Coupon_Discount') }}</span><br>
                                                            @if ($order->coupon_code)
                                                                <span
                                                                    class="badge bg-light text-danger">{{ $order->coupon_code }}</span>
                                                            @else
                                                                <span
                                                                    class="text-muted">{{ translate('No Coupon Applied') }}</span>
                                                            @endif
                                                        </td>
                                                        <td colspan="2" class="text-end">
                                                            @if ($order->coupon_amount)
                                                                <span class="fs-15 fw-semibold text-danger">
                                                                    -
                                                                    {{ webCurrencyConverter(amount: $order->coupon_amount) }}
                                                                </span>
                                                            @else
                                                                <span class="fs-15 text-muted">
                                                                    {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: 0.0), currencyCode: getCurrencyCode()) }}
                                                                </span>
                                                            @endif
                                                        </td>
                                                    </tr>

                                                    <tr class="border-top">
                                                        <td class="text-start">
                                                            <strong>{{ translate('Total_Price') }}</strong>
                                                        </td>
                                                        <td colspan="2" class="text-end">
                                                            <span class="fs-16 fw-bold text-dark">
                                                                {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $order->transection_amount), currencyCode: getCurrencyCode()) }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                        {{-- Mobile View as Card Layout --}}
                                        <div class="d-block d-lg-none">
                                            <div class="list-group">
                                                <div class="list-group-item d-flex justify-content-between">
                                                    <span class="text-muted">{{ translate('Item') }}</span>
                                                    <span class="fw-bold">{{ $order->qty }}</span>
                                                </div>
                                                <div class="list-group-item d-flex justify-content-between">
                                                    <span class="text-muted">{{ translate('Subtotal') }}</span>
                                                    <span class="text-success fw-bold">
                                                        {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $order['package_price']), currencyCode: getCurrencyCode()) }}
                                                    </span>
                                                </div>
                                                @if (count($order['product_leads']) > 0)
                                                    <div class="list-group-item d-flex justify-content-between">
                                                        <span class="text-muted">{{ translate('Cherity_Price') }}</span>
                                                        <span class="fw-bold">
                                                            {{ setCurrencySymbol(amount: $totalSum, currencyCode: getCurrencyCode()) }}
                                                        </span>
                                                    </div>
                                                @endif
                                                <div class="list-group-item d-flex justify-content-between">
                                                    <span
                                                        class="text-muted">{{ translate('Amount_Paid_(via_Wallet)') }}</span>
                                                    <span class="text-danger">
                                                        - {{ webCurrencyConverter(amount: $order->wallet_amount) }}
                                                    </span>
                                                </div>
                                                <div class="list-group-item d-flex justify-content-between">
                                                    <span class="text-muted">{{ translate('Coupon_Discount') }}</span>
                                                    @if ($order->coupon_amount)
                                                        <span class="text-danger">
                                                            - {{ webCurrencyConverter(amount: $order->coupon_amount) }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">0</span>
                                                    @endif
                                                </div>
                                                <div
                                                    class="list-group-item d-flex justify-content-between border-top pt-2">
                                                    <strong>{{ translate('Total_Price') }}</strong>
                                                    <strong class="text-dark">
                                                        {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $order->transection_amount), currencyCode: getCurrencyCode()) }}
                                                    </strong>
                                                </div>
                                            </div>
                                        </div>

                                        @if ($order['order_status'] == 'pending')
                                            <button
                                                class="btn btn-soft-danger btn-soft-border w-100 btn-sm text-danger fw-bold text-capitalize mt-3 call-route-alert"
                                                data-route="{{ route('order-cancel', [$order->id]) }}"
                                                data-message="{{ translate('want_to_cancel_this_order?') }}">
                                                {{ translate('cancel_order') }}
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </section>
        </div>
    </div>
    <span id="message-ratingContent" data-poor="{{ translate('poor') }}" data-average="{{ translate('average') }}"
        data-good="{{ translate('good') }}" data-good-message="{{ translate('the_delivery_service_is_good') }}"
        data-good2="{{ translate('very_Good') }}"
        data-good2-message="{{ translate('this_delivery_service_is_very_good_I_am_highly_impressed') }}"
        data-excellent="{{ translate('excellent') }}"
        data-excellent-message="{{ translate('best_delivery_service_highly_recommended') }}"></span>
@endsection
@push('script')
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/spartan-multi-image-picker.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/account-order-details.js') }}"></script>
@endpush