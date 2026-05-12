@extends('layouts.back-end.app-trustees')
@section('title', translate('Create_order'))
@push('css_or_js')
<link rel="stylesheet"
    href="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/css/intlTelInput.css') }}">
<link href="{{ dynamicAsset(path: 'public/assets/select2/css/select2.min.css') }}" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/intlTelInput.min.js"></script>
<!-- Include SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
    .emp_suggestion_lists li.active {
        background-color: #007bff;
        color: #fff;
    }

    .single-line-show {
        display: ruby-text;
    }

    button.btn:focus {
        border: 3px solid #5b9fe7a1;
    }

    .payment-method-flex-add.active {
        position: fixed;
        bottom: 12px;
        right: 18px;
        z-index: 100;
        background-color: white;
        padding: 7px;
        box-shadow: 4px 8px 27px;
        border-radius: 12px;
    }

    /* For focus styles on all payment mode inputs and submit buttons */
    input[name="payment_mode"][class="puja-payment-mode"]:focus,
    input[name="payment_mode"][class="puja-payment-darshan-mode"]:focus,
    input[name="payment_mode"][class="puja-payment-locker-mode"]:focus,
    input[name="payment_mode"][class="puja-payment-bhojan-mode"]:focus,
    .submit-button-class-puja:focus,
    .submit-button-class-darshan:focus,
    .submit-button-class-locker:focus,
    .submit-button-class-bhojan:focus {
        outline: 3px solid #007bff;
        outline-offset: 2px;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    /* Specific styles for submit buttons when focused */
    .submit-button-class-puja:focus,
    .submit-button-class-darshan:focus,
    .submit-button-class-locker:focus,
    .submit-button-class-bhojan:focus {
        position: relative;
        z-index: 1;
    }
</style>
@endpush
@section('content')
@php
use App\Utils\Helpers;
$thisloginstatus = 1;
if (auth('trust')->check()) {
$relationEmployees = auth('trust')->user()->relation_id;
}elseif(auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', auth('trust_employee')->user()->emp_role_id)->first()['name'] ?? "") == 'Sub Pandit')){
$relationEmployees = auth('trust_employee')->user()->relation_id;
$thisloginstatus = 0;
} elseif (auth('trust_employee')->check()) {
$relationEmployees = auth('trust_employee')->user()->relation_id;
} elseif (auth('purohit')->check()) {
$relationEmployees = (\App\Models\Purohit::with(['temple'])->where('id',auth('purohit')->user()->id)->first()['temple']['trust_id']??0);
$thisloginstatus = 0;
}
@endphp
<div class="content container-fluid pt-2">
    <div class="mb-3 d-flex justify-content-between align-items-center">
        <h2 class="h1 mb-0 d-flex gap-2">
            <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/rashi.png') }}" alt="">
            {{ translate('Create_order') }}
        </h2>
        <button type="button" class="btn btn-sm btn-primary" id="toggleTable">
            <i class="tio-exit_fullscreen_1_1">exit_fullscreen_1_1</i>
        </button>
    </div>
    <input type="hidden" class="thisloginstatus" value="{{$thisloginstatus}}">
    <div class="card my-2">
        <div class="card-body" id="tableCardBody">
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
    <div class="card">
        <div class="card-body">
            @php
            $templeCount = $temples->count();
            $defaultTemple = $temples->first();
            @endphp

            {{-- If only one temple --}}
            @if($templeCount === 1)
            <input type="hidden" id="temple_id" name="temple_id" value="{{ $defaultTemple->id }}">
            <div class="mb-3">
                <h5 class="mb-0">Temple: <span class="text-primary">{{ $defaultTemple->name }}</span></h5>
            </div>
            @else
            {{-- If multiple temples --}}
            <div class="row">
                <div class="col-md-4">
                    <label for="temple_id">Select Temple</label>
                    <select name="temple_id" id="temple_id" class="form-control">
                        @foreach($temples as $temple)
                        <option value="{{ $temple->id }}" {{ $loop->first ? 'selected' : '' }}>
                            {{ $temple->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
            @endif

            <!-- Tab Section (Dynamic AJAX Render) -->
            <div id="tabSection" class="mt-4"></div>
        </div>
    </div>
    @if(auth('purohit')->check() || (auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', auth('trust_employee')->user()->emp_role_id)->first()['name'] ?? "") == 'Sub Pandit')))
    @else
    <div class="card">
        <div class="card-body">
            <form id="orderSearchForm" class="row g-2">
                <div class="col-md-4">
                    <input type="text" id="orderIdInput" name="order_id" class="form-control" placeholder="{{ translate('Enter_Order_ID') }}" required>
                    <small>{{ translate('Ex:MCOM1000') }}</small>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 get_receipt_info">
                        {{ translate('Get_Details') }}
                    </button>
                </div>
            </form>
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
    </div>
    @endif
</div>


<!--Details -->
<div class="modal fade" id="leadDetailsModal" tabindex="-1"
    aria-labelledby="leadDetailsModalLabel" aria-hidden="true">
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


<!-- <div class="modal fade" id="purohit-modal-show" tabindex="-1" role="dialog" aria-labelledby="detailsModalLabel" aria-hidden="true">
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
</div> -->

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

<span class="form-url-new-link" data-url="{{ route('trustees-vendor.order-management.store-pooja') }}"></span>
<span class="get-payment-check-status-url" data-url="{{ route('trustees-vendor.darshan-booking.get.payment-check-status') }}"></span>
<span class="cash-paymant-confirm-url" data-url="{{ route('trustees-vendor.recepit-management.cash.confirm') }}"></span>
<span class="get-ordermanagement-timeslots" data-url="{{ route('trustees-vendor.order-management.get-slots') }}"></span>
<span class="create-ticket-url" data-url="{{ route('trustees-vendor.order-management.create-ticket') }}"></span>
<span class="get-ordermanagement-getorderdetails" data-url="{{ route('trustees-vendor.recepit-management.get-order-details') }}"></span>
<span class="get-order-list-booking-receipt-filter" data-url="{{ route('trustees-vendor.recepit-management.order-list-booking-receipt-filter') }}"></span>
<span class="get-user-suggestion-list" data-url="{{url('api/v1/user-suggestion-list')}}"></span>
<span class="get-collect-paymant-order-update" data-url="{{url('api/v1/collect-paymant-order-update')}}"></span>
<span class="get-purohit-all-employee-list" data-url="{{url('api/v1/purohit-all-employee-list')}}"></span>
<span class="modal-order-details-view" data-url="{{ route('trustees-vendor.recepit-management.order-details-modal-data') }}"></span>
@endsection

@push('script')
<script src="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/js/intlTelInput.js') }}"></script>
<script src="{{ theme_asset(path: 'public/assets/front-end/js/country-picker-init.js') }}"></script>
<script src="{{ theme_asset(path: 'public/assets/front-end/js/jquery.mixitup.min.js') }}"></script>

<script src="{{ theme_asset(path: 'public/assets/back-end/js/counter-puja.js') }}"></script>
<script src="{{ theme_asset(path: 'public/assets/back-end/js/counter-locker.js') }}"></script>
<script src="{{ theme_asset(path: 'public/assets/back-end/js/counter-darshan.js') }}"></script>
<script src="{{ theme_asset(path: 'public/assets/back-end/js/counter-bhojan.js') }}"></script>
<script src="{{ theme_asset(path: 'public/assets/back-end/js/counter-vendor-booking.js') }}"></script>
<!-- Include SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@if(auth('purohit')->check() || (auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', auth('trust_employee')->user()->emp_role_id)->first()['name'] ?? "") == 'Sub Pandit')))
@else
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

    function showPurohitAssignmentModal(orderId = null) {
        Swal.fire({
            title: 'Assign Ticket',
            html: `
            <?php if (auth('trust')->check() || (auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', auth('trust_employee')->user()->emp_role_id)->first()['name'] ?? "") != 'Sub Pandit'))) { ?>
            <div class="form-group">
                    <label class="">Purohit List</label>
                    <select name="" class="form-control pandit-select-option" onchange="purohitUpdate(this)">
                        <?php if ($purohits) {
                            foreach ($purohits as $pval) { ?>
                        <option value="{{ $pval['id'] }}">{{ $pval['name'] }}</option>
                        <?php }
                        } ?>
                    </select>
                    </div>
                    <?php } ?>
            <div class="form-group mb-0">
            <label class="purohit-name-show"></label>
            </div>
            <div class="form-group">
                <input type="hidden" id="purohit_ids" class="form-control">
                <input type="hidden" id="order_ids" class="form-control" value="${orderId || ''}">
                <input type="hidden" id="purohitEmpNames" class="form-control">
                <input type="text" id="purohit_employee_name_show" autocomplete='off' class="form-control" placeholder="Search employee...">
                <input type="hidden" id="purohitstatus" class="form-control" value="0">
                <ul class="list-group emp_suggestion_lists" style="display:none; max-height: 200px; overflow-y: auto; margin-top: 5px; position: absolute; z-index: 1000; width: 70%; background: white;"></ul>                
            </div>
            <div class="justify-content-center">
                <button class="btn btn-warning text-white cash-counter-slip" onclick="paymentCollect('0')">All Amount</button>
                <button class="btn btn-info cash-counter-slip" onclick="paymentCollect('1')">Slip Amount</button>
                <button class="btn btn-success online-counter-slip" onclick="paymentCollect('2')">Slip Print</button>
                <button class="btn btn-danger" onclick="Swal.close()">Skip Now</button>
            </div>
        `,
            focusConfirm: false,
            showCancelButton: false,
            showConfirmButton: false,
            didOpen: () => {
                initializeEmployeeSearch();
                window.purohitUpdate = function(that) {
                    $('.purohit-name-show').text($(that).find('option:selected').text());
                    $('#purohit_ids').val($(that).val());
                    $('#purohitstatus').val(0);
                    $('#purohitEmpNames').val('');
                    $('#purohit_employee_name_show').val('');
                };
            },
            preConfirm: () => {
                const purohitIds = document.getElementById('purohit_ids').value;
                const purohitEmpNames = document.getElementById('purohit_employee_name_show').value;
                const purohitStatus = document.getElementById('purohitstatus').value;
                const orderIds = document.getElementById('order_ids').value;

                if (!purohitEmpNames) {
                    Swal.showValidationMessage('Please select an employee');
                    return false;
                }
                return {
                    purohit_ids: purohitIds,
                    purohitEmpNames: purohitEmpNames,
                    purohitstatus: purohitStatus,
                    order_ids: orderIds
                };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                console.log('Saved:', result.value);

            }
        });
    }
</script>

@endif
<script>
    <?php if ($templeCount > 0) { ?>
        loadTempleServices('{{ $defaultTemple->id }}');
    <?php } ?>
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
            success: function(res) {
                $('#orderlistpayment').DataTable().draw();
            }
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
    $(document).on('keydown', function(e) {
        if(e.key){
            $('#paymentDetailsSuccess').removeClass('d-none').addClass('d-none');
        }
    });
</script>
@endpush