@extends('layouts.front-end.app')

@section('title', translate('my_Order_List'))
@push('css_or_js')
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/social-icon.css') }}">
    <style>
        @media (max-width: 991px) {
            .customer-profile-wishlist {
                margin-top: 20px;
            }

            .d-lg-flex {
                display: block !important;
            }
        }
    </style>
@endpush
@section('content')

    <div class="container py-2 py-md-4 p-0 p-md-2 user-profile-container px-5px">
        <div class="row">
            @include('web-views.partials._profile-aside')
            <section class="col-lg-9 __customer-profile customer-profile-wishlist px-0">
                <!-- <div class="card __card d-lg-flex web-direction customer-profile-orders"> -->
                <div class="card __card customer-profile-orders shadow-sm rounded">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between gap-2">
                            <div>
                                <div class="d-flex align-items-center gap-2 text-capitalize">
                                    <h4 class="text-capitalize mb-0 mobile-fs-14 fs-18 font-bold">{{ translate('order') }}
                                        #{{ $donateOrders['trans_id'] ?? '' }} </h4>
                                    <span
                                        class="status-badge rounded-pill __badge badge-soft-badge-soft-success fs-12 font-semibold text-capitalize">
                                        Completed
                                    </span>
                                </div>
                                <div class="date fs-12 font-semibold text-secondary-50 text-body mb-3 mt-2">
                                    {{ date('d M, Y h:i A', strtotime($donateOrders['created_at'])) }}
                                </div>
                            </div>
                            <button class="profile-aside-btn btn btn--primary px-2 rounded px-2 py-1 d-lg-none">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15"
                                    fill="none">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M7 9.81219C7 9.41419 6.842 9.03269 6.5605 8.75169C6.2795 8.47019 5.898 8.31219 5.5 8.31219C4.507 8.31219 2.993 8.31219 2 8.31219C1.602 8.31219 1.2205 8.47019 0.939499 8.75169C0.657999 9.03269 0.5 9.41419 0.5 9.81219V13.3122C0.5 13.7102 0.657999 14.0917 0.939499 14.3727C1.2205 14.6542 1.602 14.8122 2 14.8122H5.5C5.898 14.8122 6.2795 14.6542 6.5605 14.3727C6.842 14.0917 7 13.7102 7 13.3122V9.81219ZM14.5 9.81219C14.5 9.41419 14.342 9.03269 14.0605 8.75169C13.7795 8.47019 13.398 8.31219 13 8.31219C12.007 8.31219 10.493 8.31219 9.5 8.31219C9.102 8.31219 8.7205 8.47019 8.4395 8.75169C8.158 9.03269 8 9.41419 8 9.81219V13.3122C8 13.7102 8.158 14.0917 8.4395 14.3727C8.7205 14.6542 9.102 14.8122 9.5 14.8122H13C13.398 14.8122 13.7795 14.6542 14.0605 14.3727C14.342 14.0917 14.5 13.7102 14.5 13.3122V9.81219ZM12.3105 7.20869L14.3965 5.12269C14.982 4.53719 14.982 3.58719 14.3965 3.00169L12.3105 0.915687C11.725 0.330188 10.775 0.330188 10.1895 0.915687L8.1035 3.00169C7.518 3.58719 7.518 4.53719 8.1035 5.12269L10.1895 7.20869C10.775 7.79419 11.725 7.79419 12.3105 7.20869ZM7 2.31219C7 1.91419 6.842 1.53269 6.5605 1.25169C6.2795 0.970186 5.898 0.812187 5.5 0.812187C4.507 0.812187 2.993 0.812187 2 0.812187C1.602 0.812187 1.2205 0.970186 0.939499 1.25169C0.657999 1.53269 0.5 1.91419 0.5 2.31219V5.81219C0.5 6.21019 0.657999 6.59169 0.939499 6.87269C1.2205 7.15419 1.602 7.31219 2 7.31219H5.5C5.898 7.31219 6.2795 7.15419 6.5605 6.87269C6.842 6.59169 7 6.21019 7 5.81219V2.31219Z"
                                        fill="white" />
                                </svg>
                            </button>
                        </div>

                        <div class="bg-white border-lg rounded mobile-full">
                            <div class="p-lg-3 p-0">
                                <div class="card border-sm">
                                    <div class="p-lg-3">
                                        <div>
                                            <a href="{{ route('donate-create-pdf-invoice', [$donateOrders['id']]) }}"
                                                title="Download Donate invoice"
                                                class="btn btn--primary btn-sm float-end my-2">
                                                <i class="tio-download-to"></i>invoice
                                            </a>
                                        </div>
                                        <div class="border-lg rounded payment mb-lg-3 table-responsive">
                                            <table class="table table-borderless mb-0">
                                                <thead>
                                                    <tr class="order_table_tr">
                                                        <td class="order_table_td">
                                                            <div class="">
                                                                <div
                                                                    class="_1 py-2 d-flex justify-content-between align-items-center">
                                                                    <h6 class="fs-13 font-bold text-capitalize">
                                                                        {{ translate('payment_info') }}
                                                                    </h6>
                                                                </div>
                                                                <div class="fs-12">
                                                                    <span
                                                                        class="text-muted text-capitalize">{{ translate('payment_status') }}</span>:
                                                                    <span
                                                                        class="text-success text-capitalize">{{ translate('paid') }}</span>
                                                                    <?php
                                                                $getSubscription = (\App\Models\DonationSubscription::where('subscription_id', ($donateOrders['subscription_id'] ?? ""))->first());
                                                                if (($getSubscription['status'] ?? "") == 'created' || ($getSubscription['status'] ?? "") == 'active' || ($getSubscription['status'] ?? "") == 'cancelled') {
                                                                    $status = $getSubscription['status'] ?? "";
                                                                    $colorClass = match ($status) {
                                                                        'created'  => 'text-warning',
                                                                        'active'   => 'text-success',
                                                                        'cancelled' => 'text-danger',
                                                                        default    => 'text-secondary',
                                                                    }; ?>
                                                                    <br>
                                                                    <span
                                                                        class="text-muted text-capitalize">{{ translate('Auto Pay') }}</span>:
                                                                    <span
                                                                        class="{{ $colorClass }} text-capitalize">{{ ucwords($getSubscription['status']) }}</span>
                                                                    <?php if (($getSubscription['status'] ?? "") == 'active') { ?>
                                                                    &nbsp;&nbsp; <a class="btn btn-sm btn-warning py-1 my-3"
                                                                        onclick="cancelSubscription(`{{ $donateOrders['subscription_id'] ?? '' }}`)">Cancel
                                                                        Subscription</a>
                                                                    <?php } ?>
                                                                    <?php } ?>
                                                                </div>
                                                                <div class="mt-2 fs-12">
                                                                    <span
                                                                        class="text-muted text-capitalize">{{ translate('payment_method') }}</span>
                                                                    :<span class="text-primary text-capitalize">
                                                                        @if ($donateOrders['transaction_id'] == 'wallet')
                                                                            {{ translate('Wallet') }}
                                                                        @else
                                                                            {{ translate('online') }}
                                                                        @endif
                                                                    </span>
                                                                </div>
                                                                <div class="mt-2 fs-12">
                                                                    <small class="fs-13 font-bold text-capitalize">Trust
                                                                        Name</small>
                                                                    :
                                                                    <span>{{ $donateOrders['getTrust']['name'] ?? 'Mahakal.com' }}
                                                                    </span>
                                                                    <br>
                                                                    @if ($donateOrders['ads_id'])
                                                                        <small class="fs-13 font-bold text-capitalize">Ads
                                                                            Name</small>
                                                                        :
                                                                        <span>{{ $donateOrders['adsTrust']['name'] ?? '' }}</span><br>
                                                                    @endif

                                                                </div>
                                                            </div>
                                                            <!--  -->

                                                        </td>
                                                        <td class="order_table_td">
                                                            <div class="">
                                                                <div class="py-2">
                                                                    <h6 class="fs-13 font-bold text-capitalize">
                                                                        {{ translate('User_info') }}:
                                                                    </h6>
                                                                </div>
                                                                <div class="">
                                                                    <span class="text-capitalize fs-12">
                                                                        <span class="text-capitalize">
                                                                            <span
                                                                                class="min-w-60px">{{ translate('name') }}</span>
                                                                            :
                                                                            &nbsp;{{ $donateOrders['users']['name'] ?? '' }}
                                                                        </span>
                                                                        <br>
                                                                        <span class="text-capitalize">
                                                                            <span
                                                                                class="min-w-60px">{{ translate('phone') }}</span>
                                                                            :
                                                                            &nbsp;{{ $donateOrders['users']['phone'] ?? '' }},
                                                                        </span>
                                                                        <br>
                                                                        <span style="text-transform: lowercase;">
                                                                            <span
                                                                                class="min-w-60px">{{ translate('Email') }}</span>:
                                                                            &nbsp;<span>{{ $donateOrders['users']['email'] ?? '' }}</span>,
                                                                        </span>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                        <?php
                                        $order_information = json_decode($donateOrders['information'] ?? '[]', true);
                                        $getadsnew = json_decode($donateOrders['adsTrust']['set_json'] ?? '[]', true);
                                        $newArray = [];
                                        $product_amount = 0;
                                        if ($order_information && !empty($getadsnew) && count($order_information) > 0) {
                                            $adsData = $getadsnew['en'] ?? ($getadsnew['in'] ?? []);
                                            $adsById = [];
                                            foreach ($adsData as $adsItem) {
                                                $adsById[$adsItem['id']] = $adsItem;
                                            }
                                            foreach ($order_information as $inlist) {
                                                $id = $inlist['id'] ?? null;
                                                if (!empty($id)) {
                                                    $newItem = ['id' => $id];
                                                    if (isset($adsById[$id])) {
                                                        $adsItem = $adsById[$id];
                                                        $newItem['name'] = $adsItem['set_title'] ?? '';
                                                        $product_amount += (float) $inlist['fullamount'] ?? 0;
                                                    } else {
                                                        $newItem['name'] = $inlist['title'] ?? '';
                                                    }
                                                    $newItem['title'] = $inlist['subtitle'] ?? '';
                                                    $newItem['amount'] = $inlist['amount'] ?? '';
                                                    $newItem['qty'] = $inlist['qty'] ?? '';
                                                    $newItem['fullamount'] = $inlist['fullamount'] ?? '';
                                                    $newArray[] = $newItem;
                                                }
                                            }
                                        }
                                        $indkey = 1;
                                        ?>
                                        <div class="border-lg rounded payment mb-lg-3 table-responsive">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>S.No</th>
                                                        <th>Description</th>
                                                        <th>Rate</th>
                                                        <th>Tax</th>
                                                        <th>Tax Amt</th>
                                                        <th>Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                    @if ($product_amount > 0)
                                                        <tr>
                                                            <td><?php echo $indkey++; ?></td>
                                                            <td>
                                                                @if ($donateOrders['type'] == 'donate_trust')
                                                                    {{ $donateOrders['getTrust']['trust_name'] ?? '' }}
                                                                @else
                                                                    {{ $donateOrders['adsTrust']['name'] ?? '' }}
                                                                @endif
                                                            </td>
                                                            <td>{{ webCurrencyConverter(amount: ($donateOrders['amount'] ?? 0) - $product_amount) }}
                                                            </td>
                                                            
                                                            <td>Not Applicable</td>
                                                            <td>{{ webCurrencyConverter(amount: 0) }}</td>
                                                            <td>{{ webCurrencyConverter(amount: ($donateOrders['amount'] ?? 0) - $product_amount) }}
                                                            </td>
                                                        </tr>
                                                        @foreach ($newArray as $ky => $vl)
                                                            <tr>
                                                                <td><?php echo $indkey++; ?></td>
                                                                <td>
                                                                    <span>{{ $vl['name'] }}</span><br>
                                                                    <span>{{ $vl['title'] }}</span><br>
                                                                    <span>{{ $vl['amount'] }} *
                                                                        {{ $vl['qty'] }}</span><br>
                                                                </td>
                                                                <td>{{ webCurrencyConverter(amount: $vl['fullamount'] ?? 0) }}
                                                                </td>
                                                                <td>Not Applicable</td>
                                                                <td>{{ webCurrencyConverter(amount: 0) }}</td>
                                                                <td>{{ webCurrencyConverter(amount: $vl['fullamount'] ?? 0) }}
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @endif
                                                </tbody>

                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="row d-flex justify-content-end mt-2">
                                    <div class="col-md-8 col-lg-5">
                                        <div class="bg-white border-sm rounded">
                                            <div class="card-body ">
                                                <table class="calculation-table table table-borderless mb-0">
                                                    <tbody class="totals">
                                                        <tr>
                                                            <td>
                                                                <div class="text-start">
                                                                    <span
                                                                        class="font-semibold">{{ translate('item') }}</span>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="text-end">
                                                                    <span
                                                                        class="font-semibold">{{ translate('Price') }}</span>
                                                                </div>
                                                            </td>
                                                        </tr>

                                                        <tr class="border-top">
                                                            <td>
                                                                <div class="text-start">
                                                                    <span
                                                                        class="product-qty">{{ translate('subtotal') }}</span>
                                                                </div>
                                                            </td>
                                                            <td>

                                                                <div class="text-end">
                                                                    <span class="fs-15 font-semibold">
                                                                        {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: (float) $donateOrders['amount'] ?? 0), currencyCode: getCurrencyCode()) }}
                                                                    </span>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr class="border-top">
                                                            <td>
                                                                <div class="text-start">
                                                                    <span class="font-weight-bold">
                                                                        <strong>{{ translate('Paid_Amount') }}</strong>
                                                                    </span>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="text-end">
                                                                    <span class="font-weight-bold amount">
                                                                        {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: (float) $donateOrders['amount'] ?? 0), currencyCode: getCurrencyCode()) }}
                                                                    </span>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>



            </section>
        </div>

    </div>


@endsection

@push('script')
    <script>
        function cancelSubscription(subscriptionId) {
            fetch("{{ url('api/v1/donate/cancel-subscription') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json"
                    },
                    body: JSON.stringify({
                        id: subscriptionId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    console.log("Response:", data);
                    if (data.status === 1) {
                        toastr.success("Subscription cancelled successfully!");
                        window.location.reload(true);
                    } else {
                        toastr.error("Failed: " + data.message);
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                });
        }
    </script>
@endpush
