@extends('layouts.back-end.app')
@section('title', translate('prashadam_information'))
@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet"
        href="{{ dynamicAsset(path: 'public/assets/back-end/plugins/intl-tel-input/css/intlTelInput.css') }}">
    <style>
        .horizontal.timeline {
            display: flex;
            position: relative;
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
            width: 100%;

            &:before {
                content: '';
                display: block;
                position: absolute;

                width: 100%;
                height: .2em;
                background-color: lighten(#000, 95%);
            }

            .line {
                display: block;
                position: absolute;

                width: 50%;
                height: .2em;
                background-color: #8897ec;
            }

            .steps {
                display: flex;
                position: relative;
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
                width: 100%;

                .step {
                    display: block;
                    position: relative;
                    bottom: calc(100% + 1em);
                    padding: .33em;
                    margin: 0 2em;
                    box-sizing: content-box;

                    color: #8897ec;
                    background-color: currentColor;
                    border: .25em solid white;
                    border-radius: 50%;
                    z-index: 500;

                    &:first-child {
                        margin-left: 0;
                    }

                    &:last-child {
                        margin-right: 0;
                        color: #71CB35;
                    }

                    span {
                        position: absolute;

                        top: calc(100% + 1em);
                        left: 50%;
                        transform: translateX(-50%);
                        white-space: nowrap;
                        color: #000;
                        opacity: .4;
                    }

                    &.current {
                        &:before {
                            content: '';
                            display: block;
                            position: absolute;
                            top: 50%;
                            left: 50%;
                            transform: translate(-50%, -50%);

                            padding: 1em;
                            background-color: currentColor;
                            border-radius: 50%;
                            opacity: 0;
                            z-index: -1;

                            animation-name: animation-timeline-current;
                            animation-duration: 2s;
                            animation-iteration-count: infinite;
                            animation-timing-function: ease-out;
                        }

                        span {
                            opacity: .8;
                        }
                    }
                }
            }
        }

        @keyframes animation-timeline-current {
            from {
                transform: translate(-50%, -50%) scale(0);
                opacity: 1;
            }

            to {
                transform: translate(-50%, -50%) scale(1);
                opacity: 0;
            }
        }
    </style>
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="mb-4">
            <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/vippooja.png') }}"
                    alt="">
                {{ translate('prashadam_information') }}
            </h2>
        </div>
        {{-- <div class="col-lg-12">
            <div class="card h-100">
                <div class="card-body text-capitalize d-flex flex-column gap-5">
                    <div class="mb-4">
                        @php

                            $prashadStatus = $prashadinfo->prashad_status;
                        @endphp

                        <div class="horizontal timeline">
                            <div class="steps">
                                <div
                                    class="step {{ $prashadStatus == 0 ? 'current' : ($prashadStatus > 0 ? 'completed' : '') }}">
                                    <span>{{ translate('pending') }}</span>
                                </div>
                                <div
                                    class="step {{ $prashadStatus == 1 ? 'current' : ($prashadStatus > 1 ? 'completed' : '') }}">
                                    <span>{{ translate('confirmed') }}</span>
                                </div>
                                <div
                                    class="step {{ $prashadStatus == 2 ? 'current' : ($prashadStatus > 2 ? 'completed' : '') }}">
                                    <span>{{ translate('packaging') }}</span>
                                </div>
                                <div
                                    class="step {{ $prashadStatus == 3 ? 'current' : ($prashadStatus > 3 ? 'completed' : '') }}">
                                    <span>{{ translate('out_for_delivery') }}</span>
                                </div>
                                <div
                                    class="step {{ $prashadStatus == 4 ? 'current' : ($prashadStatus > 4 ? 'completed' : '') }}">
                                    <span>{{ translate('delivered') }}</span>
                                </div>
                                <div
                                    class="step {{ $prashadStatus == 5 ? 'current' : ($prashadStatus > 5 ? 'completed' : '') }}">
                                    <span>{{ translate('failed_to_deliver') }}</span>
                                </div>
                                <div
                                    class="step {{ $prashadStatus == 6 ? 'current' : ($prashadStatus > 6 ? 'completed' : '') }}">
                                    <span>{{ translate('canceled') }}</span>
                                </div>
                            </div>

                            <div class="line"></div>
                        </div>


                        <div class="line"></div>
                    </div>

                </div>
            </div>
        </div> --}}
        <div class="row">
            <div class="col-lg-6 d-flex flex-column gap-3">
                <div class="card">
                    <div class="card-body text-capitalize d-flex flex-column gap-4">
                        @if ($serviceDetails['type'] == 'pooja')
                            <div class="media align-items-center gap-10">
                                <img class="avatar avatar-60 rounded"
                                    src="{{ getValidImage(path: 'storage/app/public/pooja/thumbnail/' . $serviceDetails['services']['thumbnail'], type: 'backend-product') }}"
                                    alt="{{ translate('image_Description') }}">
                                <div>

                                    <span>{{ $serviceDetails->services->name ?? 'N/A' }}</span><br>
                                    <span>{{ $serviceDetails->services->pooja_venue ?? 'N/A' }}</span>

                                </div>
                            </div>
                        @elseif($serviceDetails->type == 'vip' || $serviceDetails->type == 'anushthan')
                            <div class="media align-items-center gap-10">
                                <img class="avatar avatar-60 rounded"
                                    src="{{ getValidImage(path: 'storage/app/public/pooja/vip/thumbnail/' . $serviceDetails['vippoojas']['thumbnail'], type: 'backend-product') }}"
                                    alt="{{ translate('image_Description') }}">
                                <div>
                                    <h6 class="title-color">
                                        {{ $serviceDetails->vippoojas->name ?? 'N/A' }}
                                    </h6>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
            <div class="col-lg-6 d-flex flex-column gap-3">
                <div class="card">
                    <div class="card-body text-capitalize d-flex flex-column gap-4">

                        <div class="media align-items-center gap-10">
                            <img class="avatar avatar-60 rounded"
                                src="{{ getValidImage(path: 'storage/app/public/product/thumbnail/' . $serviceDetails->thumbnail, type: 'backend-product') }}"
                                alt="{{ translate('image_Description') }}">
                            <div>

                                @if ($serviceDetails->type == 'pooja')
                                    <span>
                                        {{ $serviceDetails->products?->name ?? 'N/A' }}<br>
                                        {{ isset($serviceDetails->products) ? setCurrencySymbol(amount: usdToDefaultCurrency(amount: $serviceDetails->products->unit_price), currencyCode: getCurrencyCode()) : 'N/A' }}
                                    </span>
                                @elseif($serviceDetails->type == 'vip' || $serviceDetails->type == 'anushthan')
                                    <span>
                                        {{ $serviceDetails->products?->name ?? 'N/A' }}<br>
                                        {{ isset($serviceDetails->products) ? setCurrencySymbol(amount: usdToDefaultCurrency(amount: $serviceDetails->products->unit_price), currencyCode: getCurrencyCode()) : 'N/A' }}
                                    </span>
                                @endif


                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
        <div class="row gy-3 pt-3" id="printableArea">
            <div class="col-lg-12">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex flex-wrap flex-md-nowrap gap-10 justify-content-between mb-4">
                            <div class="d-flex flex-column gap-10">
                                <h4 class="text-capitalize">{{ translate('Order_ID') }} #
                                    @if ($serviceDetails->type == 'pooja')
                                        <span>{{ $serviceDetails->services->id ?? 'N/A' }}</span>
                                    @elseif($serviceDetails->type == 'vip' || $serviceDetails->type == 'anushthan')
                                        <span>{{ $serviceDetails->vippoojas->id ?? 'N/A' }}</span>
                                    @endif
                                </h4>
                                <div class="">
                                    <strong>{{ translate('Order_Date') }}:</strong>
                                    <span
                                        class="text-info">{{ date('d F,Y', strtotime($serviceDetails['booking_date'])) }}</span>
                                    <br>
                                    <strong>{{ translate('completed_Date') }}:</strong>
                                    <span
                                        class="text-success">{{ date('d F,Y', strtotime($serviceDetails['order_completed'])) }}</span>
                                </div>
                            </div>
                            <div class="text-sm-right flex-grow-1">

                                <div class="d-flex flex-column gap-2 mt-3">
                                    <div class="order-status d-flex justify-content-sm-end gap-10 text-capitalize">
                                        <span class="title-color">{{ translate('prashad_status') }}: </span>
                                        @if ($serviceDetails['order_status'] == 'confirmed' || $serviceDetails['order_status'] == 'delivered')
                                            <span
                                                class="badge badge-soft-success font-weight-bold radius-50 d-flex align-items-center py-1 px-2">
                                                {{ translate(str_replace('_', ' ', $serviceDetails['order_status'])) }}
                                            </span>
                                        @elseif ($serviceDetails['order_status'] == 'processing')
                                            <span
                                                class="badge badge-danger font-weight-bold radius-50 d-flex align-items-center py-1 px-2">
                                                {{ translate('processing') }}
                                            </span>
                                        @elseif ($serviceDetails['order_status'] == 'in-transit')
                                            <span
                                                class="badge badge-danger font-weight-bold radius-50 d-flex align-items-center py-1 px-2">
                                                {{ translate('in-transit') }}
                                            </span>
                                        @elseif ($serviceDetails['order_status'] == 'out_for_pickup')
                                            <span
                                                class="badge badge-danger font-weight-bold radius-50 d-flex align-items-center py-1 px-2">
                                                {{ translate('out for pickup') }}
                                            </span>
                                        @elseif ($serviceDetails['order_status'] == 'canceled')
                                            <span
                                                class="badge badge-soft-danger font-weight-bold radius-50 d-flex align-items-center py-1 px-2">
                                                {{ translate('canceled') }}
                                            </span>
                                        @else
                                            <span
                                                class="badge badge-soft-danger font-weight-bold radius-50 d-flex align-items-center py-1 px-2">
                                                {{ translate(str_replace('_', ' ', $serviceDetails['order_status'])) }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="order-status d d-flex justify-content-sm-end gap-10 text-capitalize">
                                    <span class="title-color">{{ translate('Total_orders') }}: </span>
                                    <span
                                        class="font-weight-bold radius-50 d-flex align-items-center py-1 px-2">{{ $totalOrder }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive datatable-custom mt-4">
                            <table id="myTable"
                                class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100 text-start">
                                <thead class="thead-light thead-50 text-capitalize">
                                    <tr>
                                        <th>{{ translate('Order_ID') }}</th>
                                        <th>{{ translate('Coustomer_name') }}</th>
                                        <th>{{ translate('Address') }}</th>
                                        <th>{{ translate('Shipway Details') }}</th>
                                        <th>{{ translate('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($prashadinfo as $prashad)
                                        @php
                                            $customerAddress = \App\Models\Service_order::where(
                                                'order_id',
                                                $prashad['order_id'],
                                            )->first();
                                            // dd($);
                                            $shipInfo = \App\Models\Seller::where('id', $prashad['seller_id'])->first();
                                            $shoppincode = \App\Models\Shop::where(
                                                'id',
                                                $prashad['seller_id'],
                                            )->first();
                                        @endphp

                                        <tr>
                                            <td>
                                                <h6 class="title-color"> {{ $prashad['order_id'] }}</h6>
                                            </td>
                                            <td>
                                                <h6 class="title-color">
                                                    {{ $prashad ? $prashad->customers->name : 'N/A' }}
                                                </h6>
                                            </td>
                                            <td>
                                                @if ($customerAddress)
                                                    <span><strong>{{ translate('House No.') }}:</strong></span>
                                                    {{ $customerAddress->house_no }},<br>
                                                    <span><strong>{{ translate('Area.') }}:</strong></span>
                                                    {{ $customerAddress->area }},<br>
                                                    <span><strong>{{ translate('Pincode.') }}:</strong></span>
                                                    {{ $customerAddress->pincode }},<br>
                                                    <span><strong>{{ translate('Landmark') }}:</strong></span>
                                                    {{ $customerAddress->landmark }},<br>
                                                    <span><strong>{{ translate('City') }}:</strong></span>
                                                    {{ $customerAddress->city }},<br>
                                                    <span><strong>{{ translate('State') }}:</strong></span>
                                                    {{ $customerAddress->state }},<br>
                                                @else
                                                    Address not found
                                                @endif
                                            </td>
                                            <td>

                                                @if ($prashad->awb)
                                                    <span><strong>{{ translate('AWB NO.') }}:</strong></span>
                                                    {{ $prashad->awb }},<br>
                                                    <span><strong>{{ translate('carrier_id') }}:</strong></span>
                                                    {{ $prashad->carrier_id }},<br>
                                                    <span><strong>{{ translate('carrier_name') }}:</strong></span>
                                                    {{ $prashad->carrier_name }},<br>
                                                    <span><strong>{{ translate('delivery_charge') }}:</strong></span>
                                                    {{ $prashad->delivery_charge }},<br>
                                                    <span><strong>{{ translate('manifest_ids') }}:</strong></span>
                                                    {{ $prashad->manifest_id }},<br>
                                                    <span><strong>{{ translate('shippingurl') }}:</strong></span>
                                                    <a href="{{ $prashad->shippingurl ?? '' }}" target="_blank"
                                                        class="btn btn-info btn-sm"><i class="tio-download-to"></i></a><br>
                                                @else
                                                    Shipway not found
                                                @endif

                                            </td>
                                            <td>
                                                <div class="">
                                                    @if (empty($prashad->awb))
                                                        <form id="delivery-form"
                                                            action="{{ route('admin.prashad.order.shipwaydelivery', $prashad['order_id']) }}"
                                                            method="post">
                                                            @csrf
                                                            <input type="hidden" name="delivery_partner" value="shipway">
                                                            <input type="hidden" name="fromPincode"
                                                                value="{{ $shoppincode->pincode }}">
                                                            <input type="hidden" name="toPincode"
                                                                value="{{ $customerAddress->pincode }}">
                                                            <input type="hidden" name="paymentType" value="prepaid">
                                                            <input type="hidden" name="order_weight" value="0.30">
                                                            <input type="hidden" name="box_length" value="10">
                                                            <input type="hidden" name="box_breadth" value="10">
                                                            <input type="hidden" name="box_height" value="10">
                                                            <input type="hidden" name="warehouse_id"
                                                                value="{{ $prashad->warehouse_id }}">
                                                            <input type="hidden" name="prashad_id"
                                                                value="{{ $prashad['order_id'] }}">
                                                            <button type="submit"
                                                                class="btn btn-outline-info btn-sm square-btn"
                                                                title="Order Cancel">
                                                                <i class="tio-truck"></i>
                                                            </button>
                                                        </form>
                                                    @else
                                                        <form id="delivery-cancel-form"
                                                            action="{{ route('admin.prashad.order.shipwayCancel', $prashad['order_id']) }}"
                                                            method="POST">
                                                            @csrf
                                                            <input type="hidden" name="delivery_partner"
                                                                value="{{ $prashad->delivery_partner }}">
                                                            <input type="hidden" name="warehouse_id"
                                                                value="{{ $prashad->warehouse_id }}">
                                                            <input type="hidden" name="awb"
                                                                value="{{ $prashad->awb }}">
                                                            <input type="hidden" name="order_id"
                                                                value="{{ $prashad['order_id'] }}">
                                                            <button type="submit" class="btn btn-danger btn-sm"
                                                                title="Shipment Cancel" id="delivery-cancel">
                                                                <i class="tio-truck"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach


                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="table-responsive mt-4">
                        <div class="d-flex justify-content-lg-end">
                            {{ $prashadinfo->links() }}
                        </div>
                    </div>
                    {{-- @if (count($orders) == 0)
                        <div class="text-center p-4">
                            <img class="mb-3 w-160"
                                src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}"
                                alt="">
                            <p class="mb-0">{{ translate('no_data_to_show') }}</p>
                        </div>
                    @endif --}}
                </div>
            </div>


        </div>
    </div>


@endsection

@push('script')
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>
    <script>
        let table = new DataTable('#myTable');
    </script>
    <script>
        function shipwaySubmit() {
            console.log('welcome');
            Swal.fire({
                title: 'Are You Sure To  Delivery Order Shipway.',
                type: 'success',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: getYesWord,
                cancelButtonText: getCancelWord,
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $('#delivery-form').submit();
                }
            });
        }
    </script>
@endpush
