@extends('layouts.back-end.app-trustees')

<?php

use App\Utils\Helpers;

if (auth('trust')->check()) {
    $relationEmployees = auth('trust')->user()->relation_id;
    $roleTabs = 1;
    $purohits_id = 0;
    $employees_id = 0;
} elseif (auth('trust_employee')->check() && (optional(\App\Models\VendorRoles::find(auth('trust_employee')->user()->emp_role_id))->name === 'Sub Pandit')) {
    $relationEmployees = auth('trust_employee')->user()->relation_id;
    $roleTabs = 0;
    $purohits_id = auth('trust_employee')->user()->purohit_id;
    $employees_id = auth('trust_employee')->user()->id;
} elseif (auth('purohit')->check()) {
    $roleTabs = 0;
    $purohits_id = auth('purohit')->user()->id;
    $employees_id = 0;
    $relationEmployees = (\App\Models\Purohit::with(['temple'])->where('id', auth('purohit')->user()->id)->first()['temple']['trust_id'] ?? 0);
} elseif (auth('trust_employee')->check()) {
    $relationEmployees = auth('trust_employee')->user()->relation_id;
    $roleTabs = 1;
    $purohits_id = auth('trust_employee')->user()->purohit_id;
    $employees_id = 0;
}
?>
@section('title', translate('purohit_Order_list'))
@push('css_or_js')
<style>
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

    .print-button-show {
        position: fixed;
        bottom: 12px;
        right: 18px;
        /* background-color: white;
        box-shadow: 2px 3px 14px;
        padding: 9px;
        border-radius: 12px; */
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
            {{ translate('purohit_Order_list') }}
        </h2>
    </div>
    <div class="row g-3" id="order_stats">
        <div class="col-lg-12">
            <div class="row g-2 my-2">
                <div class="col-md-3">
                    <div class="card card-body h-100 justify-content-center">
                        <div class="d-flex gap-2 justify-content-between align-items-center">
                            <div class="d-flex flex-column align-items-start">
                                <h3 class="mb-1 fz-24 text-success total-earning-order">00.00</h3>
                                <div class="text-capitalize mb-0 text-success">Total Earning</div>
                            </div>
                            <div>
                                <img width="40" class="mb-2 rotate-icon" src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/rupay.png') }}" alt="">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <a class="text-decoration-none">
                        <div class="card card-body h-100 justify-content-center">
                            <div class="d-flex gap-2 justify-content-between align-items-center">
                                <div class="d-flex flex-column align-items-start">
                                    <h3 class="mb-1 fz-24 text-warning total-cash-earning-order">00.00</h3>
                                    <div class="text-capitalize mb-0 text-warning">Cash Earning</div>
                                </div>
                                <div>
                                    <img width="40" class="mb-2 rotate-icon" src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/rupay.png') }}" alt="">
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
                                    <h3 class="mb-1 fz-24 text-warning total-online-earning-order">00.00 </h3>
                                    <div class="text-capitalize mb-0 text-warning">Online Earning</div>
                                </div>
                                <div>
                                    <img width="40" class="mb-2 rotate-icon" src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/rupay.png') }}" alt="">
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
                                    <h3 class="mb-1 fz-24 text-warning total-pending-earning-order">00.00</h3>
                                    <div class="text-capitalize mb-0 text-warning">Pending Amount</div>
                                </div>
                                <div>
                                    <img width="40" class="mb-2 rotate-icon" src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/rupay.png') }}" alt="">
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-12"></div>
                <div class="col-md-3">
                    <a class="text-decoration-none">
                        <div class="card card-body h-100 justify-content-center">
                            <div class="d-flex gap-2 justify-content-between align-items-center">
                                <div class="d-flex flex-column align-items-start">
                                    <h3 class="mb-1 fz-24 text-info total-order-number">0</h3>
                                    <div class="text-capitalize mb-0">TOTAL ORDER</div>
                                </div>
                                <div>
                                    <img width="40" class="mb-2" src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/order.png') }}" alt="">
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
                                    <h3 class="mb-1 fz-24 text-success total-completed-order-number">0</h3>
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
                                    <h3 class="mb-1 fz-24 text-primary  total-pending-order-number">0</h3>
                                    <div class="text-capitalize mb-0">PENDING ORDER</div>
                                </div>
                                <div>
                                    <img width="40" class="mb-2" src="{{ dynamicAsset(path: 'public/assets/back-end/img/pooja/panding.png') }}" alt="">
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
                                    <h3 class="mb-1 fz-24 text-danger  total-rejected-order-number">0</h3>
                                    <div class="text-capitalize mb-0">REJECTED ORDER</div>
                                </div>
                                <div>
                                    <img src="https://cdn-icons-png.flaticon.com/512/1828/1828665.png" alt="Rejected Icon" width="40">
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="card mb-4 no-print">
        <div class="card-header">
            <h5 class="mb-0">{{ translate('Find_Order_Receipts') }}</h5>
        </div>
        <div class="card-body p-0 px-2">
            <div class="row g-3 py-2">
                <div class="col-md-4">
                    <select class="form-control purohit_id {{ (($roleTabs == 0)?'d-none':'')}}" onchange="purohitchange()">
                        <option value="">{{ translate('Select Purohit') }}</option>
                        @if(isset($purohits) && count($purohits) > 0)
                        @foreach ($purohits as $purohit)
                        <option value="{{ $purohit->id }}" {{ (($purohits_id == $purohit->id)?'selected':'')}}>{{ $purohit->name }}</option>
                        @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-md-4">
                    <?php $getEmployeeData = \App\Models\VendorEmployees::where('type', 'trust')->where('purohit_id', $purohits_id)->get(); ?>
                    @if (auth('trust_employee')->check() && (optional(\App\Models\VendorRoles::find(auth('trust_employee')->user()->emp_role_id))->name === 'Sub Pandit'))
                    <input type="hidden" class="emp_id" value="{{ auth('trust_employee')->user()->id }}">
                    @else
                    <select class="form-control emp_id d-none">
                        <option value="">{{ translate('Select Sub Pandit') }}</option>
                        @foreach ($getEmployeeData as $emps)
                        <option value="{{ $emps->id }}" {{ (($employees_id == $emps->id)?'selected':'')}}>{{ $emps->name }}</option>
                        @endforeach
                    </select>

                    @endif
                </div>
            </div>
            <div class="row g-2 flex-grow-1 my-2">
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <select class="form-control payment_status">
                        <option value="">Select Payment Status</option>
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <select class="form-control payment_mode">
                        <option value="">Select Payment Method</option>
                        <option value="online">Online</option>
                        <option value="cash">Cash</option>
                        <option value="free">Free</option>
                    </select>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-3 d-none datebetweenOrderList">
                    <div class="input-group input-group-custom input-group-merge">
                        <input type="datetime-local" class="form-control start_date" value="{{ date('Y-m-d\TH:i') }}">
                        <input type="datetime-local" class="form-control end_date">
                    </div>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-3 text-center">
                    <span class="TodayOrderList btn btn-success">Today /Custom date</span>
                    <span class="TodayOrderListdnone btn btn-outline-success d-none">Today Order</span>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <select class="print_status form-control">
                        <option value="">Select Print Out Slip</option>
                        <option value="1">Already Print</option>
                        <option value="0">Print Now</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table id="orderlistpayment" class="table table-striped table-bordered table-hover">
                    <thead class="thead-light  text-capitalize">
                        <tr>
                            <th>{{ translate('SL') }}
                                @if(auth('purohit')->check() || (auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', auth('trust_employee')->user()->emp_role_id)->first()['name'] ?? "") == 'Sub Pandit')))
                                @else
                                <input type="checkbox" class="row-checkbox-all">
                                @endif
                            </th>
                            <th>{{ translate('action') }}</th>
                            <th>{{ translate('order_id') }}</th>
                            <th>{{ translate('temple_name') }}</th>
                            <th>{{ translate('Customer Name') }}</th>
                            <th>{{ translate('payment_mode') }}</th>
                            <th>{{ translate('platform') }}</th>
                            <th>{{ translate('Pandit_Amount') }}</th>
                            <th>{{ translate('gst') }}</th>
                            <th>{{ translate('platform_fee') }}</th>
                            <th>{{ translate('receipt_fee') }}</th>
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
    @if(isset($OrderList) && count($OrderList) > 0)
    @foreach ($OrderList as $item)
    <div class="modal fade" id="leadDetailsModal{{ $item->id }}" tabindex="-1" aria-labelledby="leadDetailsModalLabel{{ $item->id }}" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="leadDetailsModalLabel{{ $item->id }}">Lead Details - Order #{{ $item->order_id }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div>
                            <strong>Payment Mode:</strong> {{ $item->payment_mode }}
                        </div>
                        <div>
                            <strong>Payment Status:</strong>
                            @if ($item->payment_status == 0)
                            <span class="badge badge-warning">Pending</span>
                            @elseif($item->payment_status == 1)
                            <span class="badge badge-success">Confirmed</span>
                            @elseif($item->payment_status == 2)
                            <span class="badge badge-danger">Cancelled</span>
                            @else
                            <span class="badge badge-secondary">Unknown</span>
                            @endif
                        </div>
                        <div>
                            <strong>Amount:</strong>
                            <span style="font-size: 1.3rem; font-weight: bold;">
                                {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $item->total_amount ?? 0), currencyCode: getCurrencyCode()) }}</span>
                        </div>
                    </div>
                    <hr>
                    <h6>Services Booked:</h6>
                    <?php
                    $pujaDetails = $item->details->filter(function ($detail) {
                        return $detail->type === 'puja';
                    });
                    $first_customerGet = '';
                    ?>
                    <div class="row">
                        @foreach ($pujaDetails as $detail)
                        <div class="col-md-4 mb-3">
                            <div class="ticket-card h-100 border p-3 rounded shadow-sm">
                                <h6 class="mb-1">{{ ucfirst($detail->type ?? '-') }} - {{ $detail->package->varient_name ?? '-' }}
                                </h6>
                                <p class="mb-1">
                                    {{ !empty($detail->booking_date) ? \Carbon\Carbon::parse($detail->booking_date)->format('d M Y') : '-' }}
                                </p>
                                <p class="mb-1">
                                    {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $detail->final_amount ?? 0), currencyCode: getCurrencyCode()) }}
                                </p>
                                <p class="mb-1">{{ $detail->type_order_id }}</p>
                                <p class="mb-1">
                                    {{ !empty($detail->timeslot) ? \Carbon\Carbon::parse($detail->timeslot->start_time)->format('h:i A') : '-' }}
                                    -
                                    {{ !empty($detail->timeslot) ? \Carbon\Carbon::parse($detail->timeslot->end_time)->format('h:i A') : '-' }}
                                </p>
                                @php
                                $members = json_decode($detail->customers ?? '[]', true);
                                @endphp

                                @if (!empty($members))
                                <p class="mb-1">Yajman Information:</p>
                                <ul class="list-group">
                                    @foreach ($members as $member)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        {{ $member['name'] ?? ($member['mobile'] ?? 'N/A') }}
                                        @if (!empty($member['aadhar']))
                                        <span class="badge badge-secondary">Aadhaar:
                                            {{ $member['aadhar'] }}</span>
                                        @endif
                                    </li>
                                    @endforeach
                                </ul>
                                @else
                                <p class="mb-0">No members added.</p>
                                @endif
                                <p class="mt-2"><strong>Purohit / Pandit:</strong>
                                    {{ $detail->purohit->name ?? '-' }}
                                </p>
                            </div>
                        </div>
                        <?php if (empty($first_customerGet)) {
                            $first_customerGet = (json_decode($detail['customers'] ?? "[]", true)[0]['name'] ?? "");
                        }
                        ?>
                        @endforeach
                    </div>
                    <hr>
                    <p><strong>Temple Location:</strong>
                        {{ $item->temple->name ?? '-' }},
                        {{ $item->temple->cities->city ?? '' }},
                        {{ ucwords(strtolower($item->temple->states->name ?? '')) }},
                        {{ $item->temple->country->name ?? '' }}
                    </p>
                    <p><strong>Yajman Name:</strong> {{ ($item->user->name ?? ($first_customerGet)) }}
                        ({{ $item->total_people_count }} persons)</p>
                </div>
            </div>
        </div>
    </div>
    @endforeach
    @endif
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
    <!--Details -->
</div>
<div class="print-button-show d-none"><input type="radio" name="service" value="puja" class="d-none" checked><a class="btn btn-primary" onclick="multiOrderPrints()"><i class="tio tio-print"></i> Print Now</a></div>


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
                    <select name="" class="form-control pandit-select-option" onclick="purohitUpdate(this)">
                        @if($purohits)
                        @foreach($purohits as $pval)
                        <option value="{{ $pval['id'] }}">{{ $pval['name'] }}</option>
                        @endforeach
                        @endif
                    </select>
                </div>
                <div class="form-group mb-0 text-center">
                    <label class="purohit-name-show h3"></label>
                </div>
                <div class="">
                    <input type="hidden" class="form-control purohit_ids">
                    <input type="hidden" class="form-control order_ids">
                    <input type="hidden" class="form-control purohitstatus" value="0">
                    <input type="hidden" class="form-control purohitEmpNames">
                    <input type="text" autocomplete='off' class="form-control purohit_employee_name_show" placeholder="Search employee...">
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
    })->count() }}">
</div>
<input type="hidden" class="order-count-show-old" value="{{ $newtotalOrders }}">
@endsection
@push('script')
<script src="https://cdn.jsdelivr.net/npm/qrcodejs/qrcode.min.js"></script>
<script>
    setInterval(() => {
        $('#newcountGet').load(location.href + ' #newcountGet > *', function() {
            tablerset();
        });
    }, 7000);

    let isAmountRequestRunning = false;
    setInterval(async () => {
        if (isAmountRequestRunning) return;
        try {
            isAmountRequestRunning = true;
            const res = await getAmount();
            if (res.success) {
                $('.total-earning-order').text(res.data.total_amount);
                $('.total-cash-earning-order').text(res.data.cash_amount);
                $('.total-online-earning-order').text(res.data.online_amount);
                $('.total-pending-earning-order').text(res.data.pending_amount);

                $('.total-order-number').text(res.data.total_order);
                $('.total-completed-order-number').text(res.data.complete_order);
                $('.total-pending-order-number').text(res.data.pending_order);
                $('.total-rejected-order-number').text(res.data.cancelled_order);
            } else {
                toastr.error('Failed to confirm payment.');
            }
        } catch (error) {
            console.error('Amount fetch failed:', error);
        } finally {
            isAmountRequestRunning = false;
        }
    }, 2000);


    function tablerset() {
        let newnum = parseInt($('.order-count-show').val()) || 0;
        let oldnum = parseInt($('.order-count-show-old').val()) || 0;
        if (newnum > oldnum) {
            $('#orderlistpayment').DataTable().draw();
            $('.order-count-show-old').val(newnum);
        }
    }
</script>
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
                    $('.get_receipt_info').removeData('type').removeAttr('data-type').attr('data-type', 'other');;
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
    function printNow(that) {
        $("#orderIdInput").val($(that).data('id'));
        $(".purohit_ids").val($(that).data('purohit'));
        $('.order_ids').val($(that).data('id'));
        let stats = $(that).data('employee');
        let emstatus = $(that).data('employee_status');
        if (stats == 0 && $(that).data('purohit') != 0) {
            if ($(that).data('platform') == 'cash') {
                $('.cash-counter-slip').removeClass('d-none');
                $('.online-counter-slip').addClass('d-none');
            } else {
                $('.cash-counter-slip').addClass('d-none');
                $('.online-counter-slip').removeClass('d-none');
            }
            $('.purohitstatus').val(0);
            $('.purohitEmpNames').val('');
            $('.purohit_employee_name_show').val('');
            $('.pandit-select-option').val($(that).data('purohit'));
            $('#purohit-modal-show').modal('show');
            $('.purohit-name-show').text($(that).data('purohit_name'));
            $('.get_receipt_info').removeData('type').removeAttr('data-type').attr('data-type', 'puja');
        } else if (stats != 0 && $(that).data('purohit') != 0 && emstatus == 1) {
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
                            $('.purohitEmpNames').val(Emmploye.name);
                            $('.purohit_employee_name_show').val(Emmploye.full_name);
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
            $('.purohitEmpNames').val('');
            $('.purohit_employee_name_show').val('');
            $('.pandit-select-option').val($(that).data('purohit'));
            $('#purohit-modal-show').modal('show');
            $('.purohit-name-show').text($(that).data('purohit_name'));
            $('.get_receipt_info').removeData('type').removeAttr('data-type').attr('data-type', 'puja');

        } else {
            $('.get_receipt_info').removeData('type').removeAttr('data-type').attr('data-type', 'other');
        }
        $('.get_receipt_info').click();
        document.getElementById('orderSearchForm').scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });

    }
</script>

<script>
    $(document).ready(function() {
        initDataTable({
            tableId: '#orderlistpayment',
            ajaxUrl: "{{ route('trustees-vendor.recepit-management.pandit-order-list-receipt-filter') }}",
            exportTitle: "Trust Puja Orders",
            pageLength: 10,
            notshowfooter: 1,
            columns: [{
                    data: 'id',
                    name: 'id',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        let text = data;
                        <?php if (auth('purohit')->check() || (auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', auth('trust_employee')->user()->emp_role_id)->first()['name'] ?? "") == 'Sub Pandit'))) {
                        } else { ?>
                            if (row.checkbox_payment_status == 1 && row.checkbox_printstatus == 0) {
                                text += ` <input type="checkbox" class="row-checkbox" value="${row.checkbox_order_id}" data-employee_status="${row.employee_status}" data-employee_id="${row.employee_id}" data-purohit_id="${row.purohit_id}">`;
                            }
                        <?php } ?>
                        return text;
                    }
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
                    data: 'yajman_name',
                    name: 'yajman_name',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'payment_mode',
                    name: 'payment_mode',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'platform',
                    name: 'platform',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'base_price',
                    name: 'base_price',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'gst',
                    name: 'gst',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'platform_fee',
                    name: 'platform_fee',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'receipt_fee',
                    name: 'receipt_fee',
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
                ajax: {
                    data: function(d) {
                        d.searchValue = $('#datatableSearch_').val();
                        d.start_date = $('.start_date').val();
                        d.end_date = $('.end_date').val();
                        d.payment_mode = $('.payment_mode').val();
                        d.payment_status = $('.payment_status').val();
                        d.temple_name = $('.temple_name').val();
                        d.purohit_id = $('.purohit_id').val();
                        d.print_status = $('.print_status').val();
                        d.emp_id = $('.emp_id').val();
                    }
                }
            }
        });
    });

    $('.payment_mode, .start_date, .end_date, .payment_status, .temple_name,.purohit_id,.print_status,.emp_id').on('change', function() {
        $('#orderlistpayment').DataTable().draw();
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

    function getAmount() {
        let purohitId = $('.purohit_id').val();
        let emp_id = $('.emp_id').val();
        return new Promise((resolve, reject) => {
            $.ajax({
                url: "{{ route('trustees-vendor.recepit-management.purohit-amount-get') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    purohit_id: purohitId,
                    emp_id: emp_id
                },
                success: function(res) {
                    resolve(res);
                },
                error: function(xhr) {
                    reject(xhr);
                }
            });
        });
    }


    let selectedOrders = [];
    let selectedOrdersEmployee = [];
    let selectedOrderspurohit = [];
    $('#orderlistpayment').on('change', '.row-checkbox', function() {
        let orderId = $(this).val();
        if ($(this).is(':checked')) {
            if (!selectedOrders.includes(orderId)) {
                selectedOrders.push(orderId);
                if ($(this).data('employee_status') == 1) {
                    selectedOrdersEmployee.push($(this).data('employee_id'));
                    selectedOrderspurohit.push($(this).data('purohit_id'));
                } else {
                    selectedOrdersEmployee.push(0);
                    selectedOrderspurohit.push(0);
                }
            }
        } else {
            let index = selectedOrders.indexOf(orderId);
            selectedOrders = selectedOrders.filter(id => id != orderId);
            if (index > -1) {
                selectedOrdersEmployee.splice(index, 1);
                selectedOrderspurohit.splice(index, 1);
            }
            $('.row-checkbox-all').prop('checked', false);
        }
        togglePrintButton();
    });
    $('#orderlistpayment').on('change', '.row-checkbox-all', function() {
        let isChecked = $(this).is(':checked');
        selectedOrdersEmployee = [];
        selectedOrderspurohit = [];
        $('.row-checkbox').each(function() {
            $(this).prop('checked', isChecked);

            let orderId = $(this).val();
            if (isChecked) {
                if (!selectedOrders.includes(orderId)) {
                    selectedOrders.push(orderId);
                    if ($(this).data('employee_status') == 1) {
                        selectedOrdersEmployee.push($(this).data('employee_id'));
                        selectedOrderspurohit.push($(this).data('purohit_id'));
                    } else {
                        selectedOrdersEmployee.push(0);
                        selectedOrderspurohit.push(0);
                    }
                }
            } else {
                selectedOrders = [];
                selectedOrdersEmployee = [];
                selectedOrderspurohit = [];
            }
        });
        togglePrintButton();
    });

    // Function to show/hide print button
    function togglePrintButton() {
        if (selectedOrders.length > 0) {
            $('.print-button-show').removeClass('d-none');
        } else {
            $('.print-button-show').addClass('d-none');
        }
    }

    function multiOrderPrints() {
        if (selectedOrders.length < 1) {
            toastr.error('Please select at least one order');
            return false;
        }
        $("#loading").removeClass('d--none');
        fetch(`{{ route('trustees-vendor.recepit-management.get-order-details-puja-slip') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    order_ids: selectedOrders
                })
            })
            .then(res => res.json())
            .then(data => {
                $("#loading").addClass('d--none');
                if (data.success) {
                    document.getElementById('all_recept_thermal_Receipts').innerHTML = data.html2;
                    // document.querySelectorAll('.qr-code').forEach(qr => {
                    //     let text = qr.dataset.text;
                    //     qr.innerHTML = '';
                    //     new QRCode(qr, {
                    //         text: text,
                    //         width: 70,
                    //         height: 70
                    //     });
                    // });

                    // $('#printall_ReceiptBtn').click();

                    $('.order_ids').val(JSON.stringify(selectedOrders));
                    // let stats = $(that).data('employee');
                    // if (stats == 0 && $(that).data('purohit') != 0) {
                    //     if ($(that).data('platform') == 'cash') {
                    $('.cash-counter-slip').removeClass('d-none');
                    $('.online-counter-slip').addClass('d-none');
                    //     } else {
                    //         $('.cash-counter-slip').addClass('d-none');
                    //         $('.online-counter-slip').removeClass('d-none');
                    //     }
                    $('.purohitstatus').val(0);
                    $('.purohitEmpNames').val('');
                    $('.purohit_employee_name_show').val('');
                    $('#purohit-modal-show').modal('show');
                    // }
                    console.log(selectedOrdersEmployee);
                    console.log(selectedOrdersEmployee.includes(0));
                    console.log(selectedOrdersEmployee.every(val => val === selectedOrdersEmployee[0]));
                    console.log(selectedOrderspurohit);
                    if (!selectedOrdersEmployee.includes(0) && selectedOrdersEmployee.every(val => val === selectedOrdersEmployee[0])) {
                        $.ajax({
                            url: "{{url('api/v1/purohit-all-employee-list')}}",
                            type: "GET",
                            data: {
                                search: '',
                                purohit: selectedOrderspurohit[0],
                            },
                            success: function(res) {
                                let list = '';
                                if (res.status && res.data.length > 0) {
                                    let Emmploye = res.data.find(item => item.id == selectedOrdersEmployee[0]);
                                    if (Emmploye) {
                                        $('.purohitstatus').val(1);
                                        $('.purohitEmpNames').val(Emmploye.name);
                                        $('.purohit_employee_name_show').val(Emmploye.full_name);
                                        $('.emp_suggestion_lists').text('');
                                    }
                                }
                            }
                        });
                    }
                } else {
                    toastr.error("{{ translate('Order_not_found!') }}");
                    // document.getElementById('all_recipt_order_Details_Section').style.display = 'none';
                }
            })
            .catch(error => {
                $("#loading").addClass('d--none');
                toastr.error("{{ translate('Something_went_wrong!') }}");
                console.error('Fetch error:', error);
            });
    }

    $(document).ready(function() {
        let activeInput = null;
        $(document).on('keyup click focus', '.purohit_employee_name_show', function() {
            let keyword = $(this).val();
            activeInput = $(this);
            let suggestionBox = $(this).next('.emp_suggestion_lists');
            // if (keyword.length < 2) {
            //     suggestionBox.hide();
            //     return;
            // }
            $.ajax({
                url: "{{url('api/v1/purohit-all-employee-list')}}",
                type: "GET",
                data: {
                    search: keyword,
                    purohit: $('.purohit_ids').val(),
                },
                success: function(res) {
                    let list = '';
                    if (res.status && res.data.length > 0) {
                        $.each(res.data, function(i, item) {
                            list += `
                          <li class="list-group-item" data-name="${item.name}" data-id="${item.id}">
                              ${item.full_name}
                          </li>`;
                        });
                        suggestionBox.html(list).show();
                    } else {
                        suggestionBox.hide();
                    }
                }
            });
        });
        $(document).on('click focus', '.emp_suggestion_lists li', function(e) {
            e.preventDefault();
            e.stopPropagation();
            let selectedName = $(this).text().trim();
            let selectedName2 = $(this).data('name').trim();
            let suggestionBox = $(this).closest('.emp_suggestion_lists');
            let inputBox = suggestionBox.prev('.purohit_employee_name_show');
            inputBox.val(selectedName);
            $('.purohitEmpNames').val(selectedName2);
            $('.purohitstatus').val(1);
            suggestionBox.hide();
        });
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.purohit_employee_name_show, .emp_suggestion_lists').length) {
                $('.emp_suggestion_lists').hide();
            }
        });
    });

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
                ex_id: $('.purohitEmpNames').val(),
                num: num
            },
            success: function(res) {
                let value1 = $('.order_ids').val();
                let orderIds = '';
                try {
                    let parsed = JSON.parse(value1);
                    orderIds = Array.isArray(parsed) ? parsed : [parsed];

                    document.querySelectorAll('.qr-code').forEach(qr => {
                        let text = qr.dataset.text;
                        qr.innerHTML = '';
                        new QRCode(qr, {
                            text: text,
                            width: 70,
                            height: 70
                        });
                    });
                    $('#printall_ReceiptBtn').click();
                    selectedOrders.length = 0;
                } catch (e) {
                    orderIds = value1;
                    $('#print_Btn2').click();
                }
                $('#orderlistpayment').DataTable().draw();
                $('#purohit-modal-show').modal('hide');
                $('.purohit-name-show').text('');
            }
        });
    }

    function purohitUpdate(that) {
        $('.purohit-name-show').text($(that).find('option:selected').text());
        $('.purohit_ids').val($(that).val());
        $('.purohitstatus').val(0);
        $('.purohitEmpNames').val('');
        $('.purohit_employee_name_show').val('');
    }

    function purohitchange() {
        $('.emp_id').empty().append(`<option value="">{{ translate('Select Sub Pandit') }}</option>`);
        $.ajax({
            url: "{{ route('trustees-vendor.recepit-management.purohit-to-get-employee') }}",
            type: "GET",
            data: {
                id: $('.purohit_id').val(),
            },
            success: function(res) {
                if (res.status == 1) {
                    $('.emp_id').removeClass('d-none');
                    res.data.forEach(item => {
                        $('.emp_id').append(
                            `<option value="${item.id}">${item.name}</option>`
                        );
                    })
                } else {
                    $('.emp_id').addClass('d-none');
                }
            }
        });
    }
</script>
@endpush