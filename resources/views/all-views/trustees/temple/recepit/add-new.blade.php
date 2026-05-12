@extends('layouts.back-end.app-trustees')

@php
use App\Utils\Helpers;

switch ($mode ?? 'all') {
case 'cash':
$pageTitle = translate('Cash_Receipts');
break;
case 'online':
$pageTitle = translate('Online_Receipts');
break;
default:
$pageTitle = translate('Temple_Order_List');
break;
}
@endphp
@php
if (auth('trust')->check()) {
$relationEmployees = auth('trust')->user()->relation_id;
$roleTabs = 1;
} elseif (auth('trust_employee')->check()) {
$relationEmployees = auth('trust_employee')->user()->relation_id;
$roleTabs = 0;
} elseif (auth('purohit')->check()) {
$roleTabs=1;
$relationEmployees = (\App\Models\Purohit::with(['temple'])->where('id',auth('purohit')->user()->id)->first()['temple']['trust_id']??0);
}
@endphp
@section('title', $pageTitle)

@push('css_or_js')
{{-- THERMAL PRINT STYLES --}}
<style>
    .emp_suggestion_lists li.active {
        background-color: #007bff;
        color: #fff;
    }
    .receipt {
        width: 80mm;
        margin: 0 auto;
        border-bottom: 1px dashed #000;
        padding: 10px 10px;
        background-color: #fff;
        /* Added white background */
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        /* Optional: thoda shadow for better contrast */
    }

    .receipt h6 {
        text-align: center;
        margin: 0;
        font-size: 14px;
        text-transform: uppercase;
    }

    .divider {
        border-top: 1px dashed #000;
        margin: 4px 0;
    }

    .info p {
        margin: 0 0 4px 0;
        display: flex;
        justify-content: space-between;
    }

    .qr svg {
        width: 80px;
        height: 80px;
    }

    .center {
        text-align: center;
        font-weight: bold;
        margin-top: 5px;
    }

    button.btn:focus {
        border: 3px solid #5b9fe7a1;
    }
</style>
@endpush
@section('content')
<div class="content container-fluid">
    {{-- HEADER --}}
    <div class="mb-3 d-flex justify-content-between align-items-center no-print">
        <h2 class="h1 mb-0 d-flex gap-2 align-items-center">
            <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/rashi.png') }}" alt="">
            {{ $pageTitle }}
        </h2>
    </div>
    <?php
    $lastSegment = last(request()->segments());
    if (auth('purohit')->check() || (auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', auth('trust_employee')->user()->emp_role_id)->first()['name'] ?? "") == 'Sub Pandit'))) {
        $getquerys = \App\Models\TempleOrderMaster::where('status', 1)
            ->whereHas('details', function ($q1) {
                $q1->when((auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', auth('trust_employee')->user()->emp_role_id)->first()['name'] ?? "") == 'Sub Pandit')), function ($q) {
                    $q->where('emp_id', auth('trust_employee')->user()->id)->where('purohit_id', auth('trust_employee')->user()->purohit_id);
                    $q->where('type', 'puja');
                })->when((auth('purohit')->check()), function ($q) {
                    $q->where('purohit_id', auth('purohit')->user()->id);
                    $q->where('type', 'puja');
                });
            });
        $getquerys->where('payment_status', 1);
        $totalPayment = (clone $getquerys)->withSum(['details as total_base_price' => function ($q) {
            $q->where('type', 'puja');
        }], 'base_price')->get()->sum('total_base_price');
        $totalPaymentCash = (clone $getquerys)->where('payment_mode', 'cash')->withSum(['details as total_base_price' => function ($q) {
            $q->where('type', 'puja');
        }], 'base_price')->get()->sum('total_base_price');
        $totalPaymentOnline = (clone $getquerys)->where('payment_mode', 'online')->withSum(['details as total_base_price' => function ($q) {
            $q->where('type', 'puja');
        }], 'base_price')->get()->sum('total_base_price');
    } else {
        $totalPayment = \App\Models\TempleOrderMaster::where('status', 1)
            ->where('payment_status', 1)
            ->withSum(['details as total_base_price' => function ($q) {
                $q->where('type', 'puja');
            }], 'receipt_fee')->get()->sum('total_base_price');
        $totalPaymentCash = \App\Models\TempleOrderMaster::where('payment_mode', 'cash')
            ->where('status', 1)
            ->where('payment_status', 1)
            ->withSum(['details as total_base_price' => function ($q) {
                $q->where('type', 'puja');
            }], 'receipt_fee')->get()->sum('total_base_price');
        $totalPaymentOnline = \App\Models\TempleOrderMaster::where('payment_mode', 'online')
            ->where('status', 1)
            ->where('payment_status', 1)
            ->withSum(['details as total_base_price' => function ($q) {
                $q->where('type', 'puja');
            }], 'receipt_fee')->get()->sum('total_base_price');
    }

    ?>
    <div class="row g-3" id="order_stats">
        <div class="col-lg-12">
            <div class="row g-2 my-2">
                @if ($lastSegment == 'recepit')
                <div class="col-md-4">
                    <div class="card card-body h-100 justify-content-center">
                        <div class="d-flex gap-2 justify-content-between align-items-center">
                            <div class="d-flex flex-column align-items-start">
                                <h3 class="mb-1 fz-24 text-success">
                                    {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $totalPayment), currencyCode: getCurrencyCode()) }}
                                </h3>
                                <div class="text-capitalize mb-0 text-success">Total Earning</div>
                            </div>
                            <div>
                                <img width="40" class="mb-2 rotate-icon"
                                    src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/rupay.png') }}"
                                    alt="">
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                @if ($lastSegment == 'cashrecepit' || $lastSegment == 'recepit')
                <div class="col-md-4">
                    <a class="text-decoration-none">
                        <div class="card card-body h-100 justify-content-center">
                            <div class="d-flex gap-2 justify-content-between align-items-center">
                                <div class="d-flex flex-column align-items-start">
                                    <h3 class="mb-1 fz-24 text-warning">
                                        {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $totalPaymentCash), currencyCode: getCurrencyCode()) }}
                                    </h3>
                                    <div class="text-capitalize mb-0 text-warning">Cash Earning</div>
                                </div>
                                <div>
                                    <img width="40" class="mb-2 rotate-icon"
                                        src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/rupay.png') }}"
                                        alt="">
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                @endif
                @if ($lastSegment == 'onlinerecepit' || $lastSegment == 'recepit')
                <div class="col-md-4">
                    <a class="text-decoration-none">
                        <div class="card card-body h-100 justify-content-center">
                            <div class="d-flex gap-2 justify-content-between align-items-center">
                                <div class="d-flex flex-column align-items-start">
                                    <h3 class="mb-1 fz-24 text-warning">
                                        {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $totalPaymentOnline), currencyCode: getCurrencyCode()) }}
                                    </h3>
                                    <div class="text-capitalize mb-0 text-warning">Online Earning</div>
                                </div>
                                <div>
                                    <img width="40" class="mb-2 rotate-icon"
                                        src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/rupay.png') }}"
                                        alt="">
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                @endif
                <div class="col-12"></div>
                <?php
                $paymentCounts = \App\Models\TempleOrderMaster::selectRaw('payment_status, COUNT(*) as total_count')
                    ->whereHas('details', function ($q1) {
                        $q1->when((auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', auth('trust_employee')->user()->emp_role_id)->first()['name'] ?? "") == 'Sub Pandit')), function ($q) {
                            $q->where('emp_id', auth('trust_employee')->user()->id)->where('purohit_id', auth('trust_employee')->user()->purohit_id);
                            $q->where('type', 'puja');
                        })->when((auth('purohit')->check()), function ($q) {
                            $q->where('purohit_id', auth('purohit')->user()->id);
                            $q->where('type', 'puja');
                        });
                    })
                    ->when($lastSegment == 'onlinerecepit', function ($q) {
                        $q->where('payment_mode', 'online');
                    })
                    ->when($lastSegment == 'cashrecepit', function ($q) {
                        $q->where('payment_mode', 'cash');
                    })
                    ->groupBy('payment_status')
                    ->pluck('total_count', 'payment_status');
                ?>
                <div class="col-md-3">
                    <a class="text-decoration-none">
                        <div class="card card-body h-100 justify-content-center">
                            <div class="d-flex gap-2 justify-content-between align-items-center">
                                <div class="d-flex flex-column align-items-start">
                                    <h3 class="mb-1 fz-24 text-info">
                                        {{ ($paymentCounts[0] ?? 0) + ($paymentCounts[1] ?? 0) }}
                                    </h3>
                                    <div class="text-capitalize mb-0">TOTAL ORDER</div>

                                </div>
                                <div>
                                    <img width="40" class="mb-2"
                                        src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/order.png') }}"
                                        alt="">
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a class="text-decoration-none">
                        <div class="card card-body h-100 justify-content-center">
                            <div class="d-flex gap-2 justify-content-between align-items-center">
                                <div class="d-flex flex-column align-items-start">
                                    <h3 class="mb-1 fz-24 text-success">
                                        {{ $paymentCounts[1] ?? 0 }}
                                    </h3>
                                    <div class="text-capitalize mb-0">COMPLETED ORDER</div>
                                </div>
                                <div>
                                    <img width="40" class="mb-2"
                                        src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/ordercom.png') }}"
                                        alt="">
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a class="text-decoration-none">
                        <div class="card card-body h-100 justify-content-center">
                            <div class="d-flex gap-2 justify-content-between align-items-center">
                                <div class="d-flex flex-column align-items-start">
                                    <h3 class="mb-1 fz-24 text-primary">
                                        {{ $paymentCounts[0] ?? 0 }}
                                    </h3>
                                    <div class="text-capitalize mb-0">PENDING ORDER</div>
                                </div>
                                <div>
                                    <img width="40" class="mb-2"
                                        src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/panding.png') }}"
                                        alt="">
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a class="text-decoration-none">
                        <div class="card card-body h-100 justify-content-center">
                            <div class="d-flex gap-2 justify-content-between align-items-center">
                                <div class="d-flex flex-column align-items-start">
                                    <h3 class="mb-1 fz-24 text-danger">
                                        {{ $paymentCounts[2] ?? 0 }}
                                    </h3>
                                    <div class="text-capitalize mb-0">REJECTED ORDER</div>
                                </div>
                                <div>
                                    <img src="https://cdn-icons-png.flaticon.com/512/1828/1828665.png"
                                        alt="Rejected Icon" width="40">
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="card mb-4 no-print mx-2">
        <div class="card-header">
            <h5 class="mb-0">{{ translate('Find_Order_Receipts') }}</h5>
        </div>
        <div class="card-body p-0">
            <div class="row g-2 flex-grow-1 my-3">
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <?php $getTemples = \App\Models\Temple::where('trust_id', $relationEmployees)->get(); ?>
                    <select class="form-control temple_name">
                        <option value="">Select Temple</option>
                        @if ($getTemples)
                        @foreach ($getTemples as $planT)
                        <option value=" {{ $planT['id'] }}"> {{ $planT['name'] }}</option>
                        @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <select class="form-control payment_status">
                        <option value="">Select Payment Status</option>
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-3 {{ (($lastSegment == 'recepit')?'':"d-none")}}">
                    <select class="form-control payment_mode">
                        <option value="">Select Payment Method</option>
                        <option value="online" {{ (($lastSegment == 'onlinerecepit')?'selected':"")}}>Online</option>
                        <option value="cash" {{ (($lastSegment == 'cashrecepit')?'selected':"")}}>Cash</option>
                        <option value="free">Free</option>
                    </select>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-3 d-none datebetweenOrderList">
                    <div class="input-group input-group-custom input-group-merge">
                        <input type="datetime-local" class="form-control start_date" value="{{ date('Y-m-d\TH:i') }}">
                        <input type="datetime-local" class="form-control end_date">
                    </div>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <span class="TodayOrderList btn btn-success">Today /Custom date</span>
                    <span class="TodayOrderListdnone btn btn-outline-success d-none">Today Order</span>
                </div>

            </div>

            <div class="table-responsive">
                <table id="orderlistpayment" class="table table-striped table-bordered table-hover">
                    <thead class="thead-light  text-capitalize">
                        <tr>
                            <th>{{ translate('SL') }}</th>
                            <th>{{ translate('action') }}</th>
                            <th>{{ translate('order_id') }}</th>
                            <th>{{ translate('temple_name') }}</th>
                            <th>{{ translate('service_name') }}</th>
                            <th>{{ translate('Customer Name') }}</th>
                            <th>{{ translate('payment_mode') }}</th>
                            <th>{{ translate('platform') }}</th>
                            <th>{{ translate('pandit_amount') }}</th>
                            <th>{{ translate('trust_amount') }}</th>
                            <th>{{ translate('gst') }}</th>
                            <th>{{ translate('platform_fee') }}</th>
                            <th>{{ translate('amount') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @if(auth('purohit')->check() || (auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', auth('trust_employee')->user()->emp_role_id)->first()['name'] ?? "") == 'Sub Pandit')))
    @else
    {{-- SEARCH SECTION --}}
    <div class="card mb-4 no-print">
        <div class="card-header">
            <h5 class="mb-0">{{ translate('Find_Order_Receipts') }}</h5>
        </div>
        <div class="card-body">
            <form id="orderSearchForm" class="row g-2">
                <div class="col-md-4">
                    <input type="text" id="orderIdInput" name="order_id" class="form-control"
                        placeholder="{{ translate('Enter_Order_ID') }}" required>
                    <small>{{ translate('Ex:MCOM1000') }}</small>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 get_receipt_info">
                        {{ translate('Get_Details') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    {{-- PRINT BUTTON (HIDDEN IN PRINT) --}}
    <div class="d-flex gap-3 justify-content-center align-items-center no-print mt-3">
        <div class="text-center no-print mt-3">
            <button class="btn btn-outline-primary" id="printall_ReceiptBtn">
                <i class="tio tio-print"></i> {{ translate('All_in_one_Receipt') }}
            </button>
        </div>
        <div class="text-center no-print mt-3">
            <button class="btn btn-outline-primary" id="printBtn">
                <i class="tio tio-print"></i> {{ translate('Print_All_Receipts') }}
            </button>
        </div>
        <div class="text-center no-print mt-3">
            <button class="btn btn-outline-primary d-none" id="print_Btn2">
                <i class="tio tio-print"></i> {{ translate('Print_Puja_Receipts') }}
            </button>
        </div>
    </div>
    {{-- RECEIPTS AREA --}}
    <div class="d-flex gap-3 justify-content-center no-print mt-3">
        <div id="all_recipt_order_Details_Section" class="pt-2" style="display:none;">
            <div id="all_recept_thermal_Receipts"></div>
        </div>
        <div id="orderDetailsSection" class="pt-2" style="display:none;">
            <div id="thermalReceipts"></div>
        </div>
        <div id="order_Details_Section" class="pt-2" style="display:none;">
            <div id="thermal_Receipts2"></div>
        </div>
    </div>
    @endif
    <!--Details -->
    <div class="modal fade" id="leadDetailsModal" tabindex="-1" aria-labelledby="leadDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="leadDetailsModalLabel"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body add-new-order-details">

                </div>
            </div>
        </div>
    </div>

    <!-- Payment Confirm -->
    <div class="modal fade" id="cashConfirmModal" tabindex="-1" role="dialog"
        aria-labelledby="cashConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title" id="cashConfirmModalLabel">{{ translate('Cash Payment Confirmation') }}
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <p class="mb-3">{{ translate('Have you received this payment in cash?') }}</p>
                    <input type="hidden" id="confirmOrderId">
                    <button type="button" class="btn btn-success"
                        id="confirmCashBtn">{{ translate('Yes, payment has been received, it is confirmed') }}</button>
                    <button type="button" class="btn btn-secondary"
                        data-dismiss="modal">{{ translate('No') }}</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Payment Confirm -->
    <!-- Purohit Confirm -->
    <div class="modal fade" id="puhrohitModal" tabindex="-1" role="dialog" aria-labelledby="puhrohitModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title" id="puhrohitModalLabel">{{ translate('Purohit Confirmation') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <p class="mb-3">{{ translate('Select the Purohit who received the payment') }}</p>

                    <input type="hidden" id="confirmPurohitOrderId">

                    <!-- Dropdown for Purohit list -->
                    <div class="form-group">
                        <select id="purohitSelect" class="form-control">
                            <option value="">{{ translate('Select Purohit') }}</option>
                            @if($purohits)
                            @foreach ($purohits as $purohit)
                            <option value="{{ $purohit->id }}">{{ $purohit->name }}</option>
                            @endforeach
                            @endif
                        </select>
                    </div>

                    <button type="button" class="btn btn-success"
                        id="confirmPurohitBtn">{{ translate('Assign purohit') }}</button>
                    <button type="button" class="btn btn-secondary"
                        data-dismiss="modal">{{ translate('Cancel') }}</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Purohit Confirm -->
    <!--Details -->
</div>

<div class="modal fade" id="purohit-modal-show" tabindex="-1" role="dialog" aria-labelledby="detailsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Assign Ticket</h3>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="">Change Purohit</label>
                    @if(auth('trust')->check() || (auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', auth('trust_employee')->user()->emp_role_id)->first()['name'] ?? "") != 'Sub Pandit')))
                    <select name="" class="form-control pandit-select-option" onchange="purohitUpdate(this)">
                        @if($purohits)
                        @foreach($purohits as $pval)
                        <option value="{{ $pval['id'] }}">{{ $pval['name'] }}</option>
                        @endforeach
                        @endif
                    </select>
                    @endif
                </div>
                <div class="form-group mb-0 text-center">
                    <label class="purohit-name-show h3"></label>
                </div>
                <div class="">
                    <input type="hidden" class="form-control purohit_ids">
                    <input type="hidden" class="form-control order_ids">
                    <input type="hidden" class="form-control purohitstatus" value="0">
                    <input type="hidden" class="form-control" id="purohitEmpNames">
                    <input type="text" autocomplete='off' class="form-control" id="purohit_employee_name_show" placeholder="Search employee...">
                    <ul class="list-group emp_suggestion_lists" style="display:none; position:absolute; z-index:1000; width:70%;"> </ul>
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                <button class="btn btn-warning text-white cash-counter-slip" onclick="paymentCollect('0')">All Amount</button>
                <button class="btn btn-info cash-counter-slip" onclick="paymentCollect('1')">Slip Amount</button>
                <button class="btn btn-success online-counter-slip" onclick="paymentCollect('2')">Slip Print</button>
                <button class="btn btn-danger" data-dismiss="modal">Skip Now</button>
            </div>
        </div>
    </div>
</div>


<?php $newtotalOrders = \App\Models\TempleOrderMaster::with(['temple', 'user', 'details.package'])
    ->where('status', 1)
    ->where('trust_id', $relationEmployees)->whereHas('details', function ($q1) {
        $q1->when((auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', (auth('trust_employee')->user()->emp_role_id ?? ''))->first()['name'] ?? '') == 'Sub Pandit')), function ($q) {
            $q->where('emp_id', auth('trust_employee')->user()->id)->where('purohit_id', auth('trust_employee')->user()->purohit_id);
            $q->where('type', 'puja');
        })->when((auth('purohit')->check()), function ($q) {
            $q->where('purohit_id', auth('purohit')->user()->id);
            $q->where('type', 'puja');
        });
    })->when(($mode === 'cash'), function ($query) {
        $query->where('payment_mode', 'cash');
    })->when(($mode === 'online'), function ($query) {
        $query->where('payment_mode', 'online');
    })->count() ?>
<div id="newcountGet">
    <input type="hidden" class="order-count-show" value="{{ \App\Models\TempleOrderMaster::with(['temple', 'user', 'details.package'])
    ->where('status', 1)
    ->where('trust_id', $relationEmployees)->whereHas('details', function ($q1) {
        $q1->when((auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', (auth('trust_employee')->user()->emp_role_id ?? ''))->first()['name'] ?? '') == 'Sub Pandit')), function ($q) {
            $q->where('emp_id', auth('trust_employee')->user()->id)->where('purohit_id', auth('trust_employee')->user()->purohit_id);
            $q->where('type', 'puja');
        })->when((auth('purohit')->check()), function ($q) {
            $q->where('purohit_id', auth('purohit')->user()->id);
            $q->where('type', 'puja');
        });
    })->when(($mode === 'cash'), function ($query) {
        $query->where('payment_mode', 'cash');
    })->when(($mode === 'online'), function ($query) {
        $query->where('payment_mode', 'online');
    })->count() }}">
</div>
<input type="hidden" class="order-count-show-old" value="{{ $newtotalOrders }}">
@endsection


@push('script')
<script src="https://cdn.jsdelivr.net/npm/qrcodejs/qrcode.min.js"></script>

<script>
    document.getElementById('orderSearchForm').addEventListener('submit', function(e) {
        e.preventDefault();
        let orderId = document.getElementById('orderIdInput').value.trim();
        if (!orderId) return;
        $("#loading").removeClass('d--none');
        fetch(`{{ route('trustees-vendor.recepit-management.get-order-details') }}?order_id=${orderId}`)
            .then(res => res.json())
            .then(data => {
                $("#loading").addClass('d--none');
                if (data.success) {
                    document.getElementById('all_recipt_order_Details_Section').style.display = 'block';
                    document.getElementById('all_recept_thermal_Receipts').innerHTML = data.html3;
                    document.getElementById('orderDetailsSection').style.display = 'block';
                    document.getElementById('thermalReceipts').innerHTML = data.html;
                    // document.getElementById('order_Details_Section').style.display = 'block';
                    document.getElementById('thermal_Receipts2').innerHTML = data.html2;


                    // Generate QR for each receipt
                    document.querySelectorAll('.qr-code').forEach(qr => {
                        let text = qr.dataset.text;
                        qr.innerHTML = '';
                        new QRCode(qr, {
                            text: text,
                            width: 70,
                            height: 70
                        });
                    });
                    if ($('.get_receipt_info').data('type') == 'other') {
                        $('#printall_ReceiptBtn').click();
                        printStatusUpdate();
                    }
                } else {
                    toastr.error("{{ translate('Order_not_found!') }}");
                    document.getElementById('all_recipt_order_Details_Section').style.display = 'none';
                    document.getElementById('orderDetailsSection').style.display = 'none';
                    // document.getElementById('order_Details_Section').style.display = 'none';

                }
            })
            .catch(error => {
                toastr.error("{{ translate('Something_went_wrong!') }}");
                $("#loading").addClass('d--none');
                console.error('Fetch error:', error);
            });
    });
</script>
<script>
    document.getElementById('printBtn').addEventListener('click', function() {
        let receipts = document.querySelectorAll('#thermalReceipts');
        if (!receipts.length) {
            toastr.error("No receipt found to print!");
            return;
        }
        let printWindow = window.open('', '', 'height=600,width=400');
        printWindow.document.write('<html><head><title>Receipt Print</title>');
        printWindow.document.write(`
        <style>
            body { font-family: monospace; font-size: 12px; margin: 10px; }
            #thermalReceipts { page-break-after: always; border-bottom: 1px dashed #000; padding-bottom: 10px; margin-bottom: 10px; }
            h6 { text-align: center; margin: 5px 0; font-size: 14px; text-transform: uppercase; }
            .info p { margin: 2px 0; }
            .qr-code { text-align: center; margin-top: 5px; }
        </style>
    `);

        printWindow.document.write('</head><body>');
        receipts.forEach(function(receipt) {
            printWindow.document.write(receipt.outerHTML);
        });
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        printWindow.onload = function() {
            printWindow.focus();
            printWindow.print();
            printWindow.close();
        };
    });

    document.getElementById('print_Btn2').addEventListener('click', function() {
        let receipts = document.querySelectorAll('#thermal_Receipts2');
        if (!receipts.length) {
            toastr.error("No receipt found to print!");
            return;
        }
        let printWindow = window.open('', '', 'height=600,width=400');
        printWindow.document.write('<html><head><title>Receipt Print</title>');
        printWindow.document.write(`
        <style>
            body { font-family: monospace; font-size: 12px; margin: 10px; }
            #thermal_Receipts2 { page-break-after: always; border-bottom: 1px dashed #000; padding-bottom: 10px; margin-bottom: 10px; }
            h6 { text-align: center; margin: 5px 0; font-size: 14px; text-transform: uppercase; }
            .info p { margin: 2px 0; }
            .qr-code { text-align: center; margin-top: 5px; }
        </style>
    `);

        printWindow.document.write('</head><body>');
        receipts.forEach(function(receipt) {
            printWindow.document.write(receipt.outerHTML);
        });
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        printWindow.onload = function() {
            printWindow.focus();
            printWindow.print();
            printWindow.close();
        };
    });


    document.getElementById('printall_ReceiptBtn').addEventListener('click', function() {
        let receipts = document.querySelectorAll('#all_recept_thermal_Receipts');
        if (!receipts.length) {
            toastr.error("No receipt found to print!");
            return;
        }
        let printWindow = window.open('', '', 'height=600,width=400');
        printWindow.document.write('<html><head><title>Receipt Print</title>');
        printWindow.document.write(`
        <style>
            body { font-family: monospace; font-size: 12px; margin: 10px; }
            #all_recept_thermal_Receipts { page-break-after: always; border-bottom: 1px dashed #000; padding-bottom: 10px; margin-bottom: 10px; }
            h6 { text-align: center; margin: 5px 0; font-size: 14px; text-transform: uppercase; }
            .info p { margin: 2px 0; }
            .qr-code { text-align: center; margin-top: 5px; }
        </style>
    `);

        printWindow.document.write('</head><body>');
        receipts.forEach(function(receipt) {
            printWindow.document.write(receipt.outerHTML);
        });
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        printWindow.onload = function() {
            printWindow.focus();
            printWindow.print();
            printWindow.close();
        };
    });
</script>

<script>
    $(document).on('click', '[data-target="#cashConfirmModal"]', function() {
        let orderId = $(this).data('id');
        $('#confirmOrderId').val(orderId);
    });

    $('#confirmCashBtn').click(function() {
        let orderId = $('#confirmOrderId').val();
        $.ajax({
            url: "{{ route('trustees-vendor.recepit-management.cash.confirm') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                order_id: orderId
            },
            success: function(res) {
                if (res.success) {
                    console.log(res);
                    toastr.success('Cash payment confirmed successfully!');
                    $('#cashConfirmModal').modal('hide');
                    reloadDataTable();
                    $('#orderIdInput').val(orderId);
                    // $('.get_receipt_info').removeData('type').removeAttr('data-type').attr('data-type','other');;
                    $('.get_receipt_info').click();
                } else {
                    toastr.error('Failed to confirm payment.');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                toastr.error('An error occurred while processing your request.');
            }
        });
    });
</script>
<script>
    $(document).on('click', '[data-target="#puhrohitModal"]', function() {
        let orderId = $(this).data('id');
        let purohit = $(this).data('purohit');
        $('#purohitSelect').val(purohit);
        $('#confirmPurohitOrderId').val(orderId);
    });

    // function printNow(that) {
    //     $("#orderIdInput").val($(that).data('id'));
    //     $('.get_receipt_info').click();
    //     document.getElementById('orderSearchForm').scrollIntoView({
    //         behavior: 'smooth',
    //         block: 'start'
    //     });
    // }
    function printNow(that) {
        $("#orderIdInput").val($(that).data('id'));
        $(".purohit_ids").val($(that).data('purohit'));
        $('.order_ids').val($(that).data('id'));
        let stats = $(that).data('employee');
        let statsemploye = $(that).data('employee_status');
        if (stats == 0 && $(that).data('purohit') != 0) {
            if ($(that).data('platform') == 'cash') {
                $('.cash-counter-slip').removeClass('d-none');
                $('.online-counter-slip').addClass('d-none');
            } else {
                $('.cash-counter-slip').addClass('d-none');
                $('.online-counter-slip').removeClass('d-none');
            }
            $('.purohitstatus').val(0);
            $('#purohitEmpNames').val('');
            $('#purohit_employee_name_show').val('');
            $('.pandit-select-option').val($(that).data('purohit'));
            $('#purohit-modal-show').modal('show');
            $('.purohit-name-show').text($(that).data('purohit_name'));
            $('.get_receipt_info').removeData('type').removeAttr('data-type').attr('data-type', 'puja');
            initializeEmployeeSearch();
        } else if (stats != 0 && $(that).data('purohit') != 0 && statsemploye == 1) {
            $.ajax({
                url: "{{url('api/v1/purohit-all-employee-list')}}",
                type: "GET",
                data: {
                    search: '',
                    purohit: $(that).data('purohit'),
                },
                success: function(res) {
                    let list = '';
                    if (res.status && res.data.length > 0) {
                        let Emmploye = res.data.find(item => item.id == stats);
                        if (Emmploye) {
                            $('.purohitstatus').val(1);
                            $('#purohitEmpNames').val(Emmploye.name);
                            $('#purohit_employee_name_show').val(Emmploye.full_name);
                            $('.emp_suggestion_lists').text('');
                        }
                    }
                }
            });
            if ($(that).data('platform') == 'cash') {
                $('.cash-counter-slip').removeClass('d-none');
                $('.online-counter-slip').addClass('d-none');
            } else {
                $('.cash-counter-slip').addClass('d-none');
                $('.online-counter-slip').removeClass('d-none');
            }
            $('.purohitstatus').val(0);
            $('#purohitEmpNames').val('');
            $('#purohit_employee_name_show').val('');
            $('.pandit-select-option').val($(that).data('purohit'));
            $('#purohit-modal-show').modal('show');
            $('.purohit-name-show').text($(that).data('purohit_name'));
            $('.get_receipt_info').removeData('type').removeAttr('data-type').attr('data-type', 'puja');
            initializeEmployeeSearch();
        } else {
            $('.get_receipt_info').removeData('type').removeAttr('data-type').attr('data-type', 'other');
        }
        $('.get_receipt_info').click();
        document.getElementById('orderSearchForm').scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });

    }

    $('#confirmPurohitBtn').click(function() {
        let orderId = $('#confirmPurohitOrderId').val();
        let purohitId = $('#purohitSelect').val();

        if (!purohitId) {
            toastr.warning('Please select a Purohit.');
            return;
        }
        if (!confirm('Are you sure you want to change the Purohit?')) {
            // User clicked Cancel, do nothing
            return;
        }

        $.ajax({
            url: "{{ route('trustees-vendor.recepit-management.purohit.confirm') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                order_id: orderId,
                purohit_id: purohitId
            },
            success: function(res) {
                if (res.success) {
                    toastr.success('Purohit assigned and payment confirmed successfully!');
                    $('#puhrohitModal').modal('hide');
                    location.reload();
                } else {
                    toastr.error('Failed to confirm payment.');
                }
            }
        });
    });
</script>
<script>
    $(document).ready(function() {
        initDataTable({
            tableId: '#orderlistpayment',
            ajaxUrl: "{{ route('trustees-vendor.recepit-management.order-list-booking-receipt-filter') }}",
            exportTitle: "Trust Puja Orders",
            pageLength: 25,
            notshowfooter: 1,
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'order_id',
                    name: 'order_id'
                },
                {
                    data: 'temple_name',
                    name: 'temple_name',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'service_name',
                    name: 'service_name',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'yajman_name',
                    name: 'yajman_name',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'payment_mode',
                    name: 'payment_mode',
                },
                {
                    data: 'platform',
                    name: 'platform',
                },
                {
                    data: 'pandit_amount',
                    name: 'pandit_amount',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'trust_amount',
                    name: 'trust_amount',
                    orderable: false,
                    searchable: false
                }, {
                    data: 'gst',
                    name: 'gst',
                    orderable: false,
                    searchable: false
                }, {
                    data: 'platform_fee',
                    name: 'platform_fee',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'amount',
                    name: 'amount',
                    orderable: false,
                    searchable: false
                },
            ],
            extraOptions: {
                serverSide: true,
                createdRow: function(row, data, dataIndex) {
                    $(row).addClass('row-order-id-' + data.order_ids);
                    $(row).addClass('get-order-recodes');
                    $(row).attr('row-order-id', data.order_ids);
                    $(row).attr('row-order-status', data.order_status);
                    // if (data.order_status === 'pending') {
                    //     updateOrderArray(data.order_ids);
                    // }
                },
                initComplete: function() {
                    console.log('DataTable initialized successfully');
                },
                ajax: {
                    data: function(d) {
                        d.searchValue = $('#datatableSearch_').val();
                        d.start_date = $('.start_date').val();
                        d.end_date = $('.end_date').val();
                        d.payment_mode = $('.payment_mode').val();
                        d.payment_status = $('.payment_status').val();
                        d.temple_name = $('.temple_name').val();
                        d.puja_name = $('.puja_name').val();
                    }
                }
            }
        });
    });

    $('.payment_mode, .start_date, .end_date, .payment_status, .temple_name').on('change', function() {
        $('#orderlistpayment').DataTable().draw();
    });
    $('#orderlistpayment').on('draw.dt', function() {
        updateOrderArray();
    });

    $(document).ready(function() {
        $('.TodayOrderList').click(function() {
            $('.TodayOrderListdnone').removeClass('d-none');
            $('.datebetweenOrderList').removeClass('d-none');
            $('.TodayOrderList').addClass('d-none');
            $('.start_date').val("");
            $('#orderlistpayment').DataTable().draw();
        });
        $('.TodayOrderListdnone').click(function() {
            $('.datebetweenOrderList').addClass('d-none');
            $('.TodayOrderList').removeClass('d-none');
            $('.TodayOrderListdnone').addClass('d-none');
            $('.start_date').val("{{ date('Y-m-d\TH:i') }}");
            $('#orderlistpayment').DataTable().draw();
        });
    });
    $(document).on('click', '.view-details', function() {
        let htmlContent = $(this).data('html');

        $('#detailsModalBody').html(htmlContent);
        $('#detailsModal').modal('show');
    });
</script>
<script>
    // $(document).ready(function() {
    //     let activeInput = null;
    //     $(document).on('keyup click focus', '#purohit_employee_name_show', function() {
    //         let keyword = $(this).val();
    //         activeInput = $(this);
    //         let suggestionBox = $(this).next('.emp_suggestion_lists');
    //         // if (keyword.length < 2) {
    //         //     suggestionBox.hide();
    //         //     return;
    //         // }
    //         $.ajax({
    //             url: "{{url('api/v1/purohit-all-employee-list')}}",
    //             type: "GET",
    //             data: {
    //                 search: keyword,
    //                 purohit: $('.purohit_ids').val(),
    //             },
    //             success: function(res) {
    //                 let list = '';
    //                 if (res.status && res.data.length > 0) {
    //                     $.each(res.data, function(i, item) {
    //                         list += `
    //                       <li class="list-group-item" data-name="${item.name}" data-id="${item.id}">
    //                           ${item.full_name}
    //                       </li>`;
    //                     });
    //                     suggestionBox.html(list).show();
    //                 } else {
    //                     suggestionBox.hide();
    //                 }
    //             }
    //         });
    //     });
    //     $(document).on('click ', '.emp_suggestion_lists li', function(e) {
    //         e.preventDefault();
    //         e.stopPropagation();
    //         let selectedName = $(this).text().trim();
    //         let selectedName2 = $(this).data('name').trim();
    //         let suggestionBox = $(this).closest('.emp_suggestion_lists');
    //         let inputBox = suggestionBox.prev('#purohit_employee_name_show');
    //         inputBox.val(selectedName);
    //         $('#purohitEmpNames').val(selectedName2);
    //         $('.purohitstatus').val(1);
    //         suggestionBox.hide();
    //     });
    //     $(document).on('click', function(e) {
    //         if (!$(e.target).closest('#purohit_employee_name_show, .emp_suggestion_lists').length) {
    //             $('.emp_suggestion_lists').hide();
    //         }
    //     });
    // });
  function initializeEmployeeSearch() {
    const purohitEmpNamesInput = document.getElementById('purohit_employee_name_show');
    const purohit_Emp_Names = document.getElementById('purohitEmpNames');
    const suggestionBox = document.querySelector('.emp_suggestion_lists');

    if (!purohitEmpNamesInput) return;

    let debounceTimer;
    let currentIndex = -1;
    let isSelected = false; // ✅ NEW

    // CLICK / FOCUS
    ['click', 'focus'].forEach(event => {
        purohitEmpNamesInput.addEventListener(event, function (e) {
            if (isSelected) return; // ❌ do not re-search after selection

            clearTimeout(debounceTimer);
            const keyword = e.target.value.trim();
            debounceTimer = setTimeout(() => {
                searchEmployees(keyword);
            }, 200);
        });
    });

    // INPUT
    purohitEmpNamesInput.addEventListener('input', function (e) {
        isSelected = false; // reset when user types again
        clearTimeout(debounceTimer);
        const keyword = e.target.value.trim();

        debounceTimer = setTimeout(() => {
            searchEmployees(keyword);
        }, 300);
    });

    // CLICK ON LIST ITEM
    suggestionBox.addEventListener('click', function (e) {
        if (e.target.tagName === 'LI' && e.target.dataset.name) {
            selectItem(e.target);
        }
    });

    // KEYBOARD NAVIGATION
    purohitEmpNamesInput.addEventListener('keydown', function (e) {
        const items = suggestionBox.querySelectorAll('li');
        if (!items.length || suggestionBox.style.display === 'none') return;

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            currentIndex = (currentIndex + 1) % items.length;
            updateActive(items);
        }

        if (e.key === 'ArrowUp') {
            e.preventDefault();
            currentIndex = (currentIndex - 1 + items.length) % items.length;
            updateActive(items);
        }

        if (e.key === 'Tab') { // ✅ FIXED TAB
            if (currentIndex >= 0) {
                e.preventDefault();
                selectItem(items[currentIndex]);
            }
        }
    });

    // CLICK OUTSIDE (DO NOT CLEAR VALUE)
    document.addEventListener('click', function (e) {
        if (
            !e.target.closest('#purohit_employee_name_show') &&
            !e.target.closest('.emp_suggestion_lists')
        ) {
            suggestionBox.style.display = 'none';
        }
    });
    function updateActive(items) {
        items.forEach(item => item.classList.remove('active'));
        if (currentIndex >= 0) {
            items[currentIndex].classList.add('active');
            items[currentIndex].scrollIntoView({ block: 'nearest' });
        }
    }
    function selectItem(item) {
        purohitEmpNamesInput.value = item.textContent.trim();
        purohit_Emp_Names.value = item.dataset.name || '';
        $('.purohitstatus').val(1);

        isSelected = true; // ✅ prevent re-search
        currentIndex = -1;
        suggestionBox.style.display = 'none';
    }
}


function searchEmployees(keyword) {
    const purohitId = $('.purohit_ids').val();
    const suggestionBox = document.querySelector('.emp_suggestion_lists');
    let urls = "{{url('api/v1/purohit-all-employee-list')}}";
    fetch(`${urls}?search=${encodeURIComponent(keyword)}&purohit=${purohitId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(res => {
            if (res.status && res.data && res.data.length > 0) {
                let listHTML = '';
                res.data.forEach(item => {
                    listHTML += `
                        <li class="list-group-item" data-id="${item.id}" data-name="${item.name}" style="cursor: pointer;">
                            ${item.full_name}
                        </li>
                    `;
                });
                suggestionBox.innerHTML = listHTML;
                suggestionBox.style.display = 'block';
            } else {
                suggestionBox.innerHTML = '<li class="list-group-item text-muted">No employees found</li>';
                suggestionBox.style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Error fetching employees:', error);
            suggestionBox.innerHTML = '<li class="list-group-item text-danger">Error loading employees</li>';
            suggestionBox.style.display = 'block';
        });
}


    function paymentCollect(num) {
        let status = $('.purohitstatus').val();
        if (status == 0) {
            toastr.error('Please Select Valid Assign Tickit Pandit Id');
            return false;
        }
        $.ajax({
            url: "{{url('api/v1/collect-paymant-order-update')}}",
            type: "post",
            data: {
                purohit: $('.purohit_ids').val(),
                order_id: $('.order_ids').val(),
                ex_id: $('#purohitEmpNames').val(),
                num: num
            },
            success: function(res) {
                $('#orderlistpayment').DataTable().draw();
                $('#purohit-modal-show').modal('hide');
                $('.purohit-name-show').text('');
                $('#print_Btn2').click();
                printStatusUpdate();
            }
        });
    }

    function purohitUpdate(that) {
        $('.purohit-name-show').text($(that).find('option:selected').text());
        $('.purohit_ids').val($(that).val());
        $('.purohitstatus').val(0);
        $('#purohitEmpNames').val('');
        $('#purohit_employee_name_show').val('');
    }
</script>
<script>
    function reloadDataTable() {
        $('#orderlistpayment').DataTable().draw();
    }
    $(document).ready(function() {
        reloadDataTable();
        $('#payment-status, #purohit-id, #booking-status').on('change', function() {
            $(this).closest('form').submit();
        });
    });
</script>
<script>
    // setInterval(() => {
    //     $('#newcountGet').load(location.href + ' #newcountGet > *', function() {
    //         tablerset();
    //     });
    //     $('#order_stats').load(location.href + ' #order_stats > *');
    // }, 7000);

    setInterval(() => {
        $.get(location.href, function(response) {
            const html = $('<div>').html(response);
            $('#newcountGet').html(html.find('#newcountGet').html());
            $('#order_stats').html(html.find('#order_stats').html());

            tablerset();
        });
    }, 7000);

    function tablerset() {
        let newnum = parseInt($('.order-count-show').val()) || 0;
        let oldnum = parseInt($('.order-count-show-old').val()) || 0;
        if (newnum > oldnum) {
            $('#orderlistpayment').DataTable().draw();
            $('.order-count-show-old').val(newnum);
        }
    }


    $(document).on('click', '.show-order-details-now', function() {
        $.ajax({
            url: "{{ route('trustees-vendor.recepit-management.order-details-modal-data') }}",
            type: "POST",
            data: {
                _token: $('meta[name="_token"]').attr('content'),
                order_id: $(this).data('orderid'),
            },
            success: function(res) {
                $('#leadDetailsModal').modal('show');
                $("#leadDetailsModalLabel").text(`Lead Details - Order #${$(this).data('orderid')}`);
                $('.add-new-order-details').html(res.html);
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                toastr.error('An error occurred while processing your request.');
            }
        });
    });
</script>
<script>
    function printStatusUpdate() {
        let orderid = $('#orderIdInput').val();
        let type = $('.get_receipt_info').data('type');
        $.ajax({
            url: "{{ route('trustees-vendor.recepit-management.print-status-update') }}",
            type: "GET",
            data: {
                type: type,
                orderid: orderid
            },
            success: function(res) {}
        });
    }
</script>
<script>
    const OrderArrays = [];

    function updateOrderArray() {
        OrderArrays.length = 0;
        $('#orderlistpayment tbody tr').each(function() {
            if ($(this).attr('row-order-status') === 'pending') {
                OrderArrays.push($(this).attr('row-order-id'));
            }
        });
    }

    setInterval(() => {
        checkOrderStatus();
    }, 4000);

    function checkOrderStatus() {
        $.ajax({
            url: "{{ route('trustees-vendor.order-management.multi-order-status-check') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                order_id: JSON.stringify(OrderArrays),
            },
            success: function(res) {
                if (res.status == 1) {
                    const index = OrderArrays.indexOf(res.data);
                    if (index !== -1) {
                        OrderArrays.splice(index, 1);
                    }
                    const $row = $('#orderlistpayment tbody').find('tr[row-order-id="' + (res.data) + '"]');
                    if ($row.length) {
                        $row.attr('row-order-status', 'confirmed');
                        $row.find('.order-status-text')
                            .removeClass('badge-danger')
                            .addClass('badge-success')
                            .text('Confirmed');
                            const $container = $row.find('.order-append-child');
                        if ($container.find('.print-button-append').length === 0) {
                            $container.append(res.printbutton);
                        }
                    }                   
                } else {

                }
            }
        });
    }
</script>
@endpush