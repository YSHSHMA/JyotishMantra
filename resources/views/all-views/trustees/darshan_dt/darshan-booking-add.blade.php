@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app-trustees')

@section('title', 'Darshan Booking')
@push('css_or_js')
<link rel="stylesheet"
    href="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/css/intlTelInput.css') }}">
<link href="{{ dynamicAsset(path: 'public/assets/select2/css/select2.min.css') }}" rel="stylesheet">
<style>
    .bg-warning {
        background: rgb(255, 202, 67) !important;
    }

    .receipt {
        width: 300px;
        padding: 10px;
        border: 1px dashed #000;
        font-family: monospace;
        font-size: 14px;
        background: #fff;
        margin: auto;
    }

    .receipt-title {
        text-align: center;
        margin: 5px 0;
    }

    hr {
        border: none;
        border-top: 1px dashed #000;
        margin: 5px 0;
    }

    @media print {
        @page {
            size: 80mm auto;
            margin: 5mm;
        }

        body {
            margin: 0;
            padding: 0;
        }

        body * {
            visibility: hidden;
        }

        #print-receipt,
        #print-receipt * {
            visibility: visible;
        }

        img {
            max-width: 100%;
        }

        #print-receipt {
            position: absolute;
            left: 5mm;
            top: 10mm;
            width: 70mm;
            margin: 0;
            padding: 0;
        }

        #modal-print-receipt,
        #modal-print-receipt * {
            visibility: visible;
            /* receipt visible */
        }

        #modal-print-receipt {
            position: absolute;
            left: 0;
            top: 5mm;
            width: 60mm;
            margin: 0;
            padding: 0;
        }
    }
</style>
@endpush
@section('content')
@php
if (auth('trust')->check()) {
$relationEmployees = auth('trust')->user()->relation_id;
} elseif (auth('trust_employee')->check()) {
$relationEmployees = auth('trust_employee')->user()->relation_id;
} elseif (auth('purohit')->check()) {
$relationEmployees = (\App\Models\Purohit::with(['temple'])->where('id',auth('purohit')->user()->id)->first()['temple']['trust_id']??0);
}
@endphp
<div class="content container-fluid">

    <div class="mb-3 d-flex justify-content-between align-items-center">
        <h2 class="h1 mb-0 d-flex gap-2 align-items-center">
            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/') }}" alt="" style="height:40px;">
            Darshan List
        </h2>

        <div class="d-flex gap-2">
            <button id="refreshOrdersBtn" class="btn btn-warining"> Refresh Orders </button>
            <a href="{{ route('trustees-vendor.darshan-booking.get.purohittransaction') }}" class="btn btn-success">
                <i class="tio-rupee"></i> Purohit Transactions
            </a>
            <a href="{{ route('trustees-vendor.darshan-booking.get.bookingList') }}" class="btn btn-primary">
                <i class="tio-list"></i> Booking List
            </a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 mb-2">
            <div class="card">
                {{-- <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Booking List</h4>
                        <small class="text-muted">Temple, Package, Purohit & User Information</small>
                    </div> --}}
                {{-- <div class="card-body" style="height: 400px; overflow-y: auto;"> --}}
                <table class="table table-bordered" id="tableList">
                    <thead>
                        <tr>
                            <th>Sn</th>
                            <th>Order Id</th>
                            <th>Purohit Name</th>
                            <th>User Name</th>
                            <th>TransactionId</th>
                            <th>Amount</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="ordersBody">
                    </tbody>
                </table>

                <!-- Spinner -->
                <div id="loading" class="text-center my-3" style="display:none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>

                <!-- No more orders message -->
                <div id="noMoreOrders" class="text-center text-muted my-3" style="display:none;">
                    No more orders available.
                </div>
                {{-- </div> --}}
            </div>

        </div>
    </div>

    <div class="row">
        <div class="col-md-12 mb-2">
            <div class="card">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Darshan Booking Form</h4>
                    <small class="text-muted">Temple, Package, Purohit & User Information</small>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label><strong>Select Date:</strong></label>
                            <div id="date-buttons" class="d-flex gap-2 mt-2"></div>
                            <input type="hidden" name="puja_date" id="puja-date" class="pickDate" />
                        </div>
                    </div>
                    <div class='row'>
                        <div class="col-md-3 form-group">
                            <label for="">Yajman Name</label>
                            <input type="text" name="user_name" id="customer_name" class="form-control"
                                placeholder="Enter User Full Name">
                        </div>
                        <div class="col-md-3 form-group">
                            <label for="">Yajman Address (Optional)</label>
                            <input type="text" name="user_address" id="customer_address" class="form-control"
                                placeholder="Enter User Address">
                        </div>
                        <div class="col-md-3 form-group">
                            <label for="">Phone Number</label>
                            <input class="form-control text-align-direction phone-input-with-country-picker"
                                type="tel" value="" name="person_phone" id="person-number"
                                placeholder="Enter User Phone Number" required
                                oninput="this.value=this.value.replace(/\D/g,'').slice(0,10)">
                            <small class="form-text text-danger">
                                Note: Please enter your WhatsApp number.
                            </small>

                        </div>
                        <div class="col-md-3 form-group">
                            <label for="">Aadhar Number (Optional)</label>
                            <input class="form-control text-align-direction" type="text" value=""
                                placeholder="Enter User Aadhar Number" id="aadhar" required
                                oninput="this.value=this.value.replace(/\D/g,'').slice(0,12)">
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="">Temple List</label>
                            <select name="temple_id" id="temple_id" class="form-control" required>
                                <option value="" disabled selected>Select Temple Name</option>
                                @if (isset($templeList) && $templeList)
                                @foreach ($templeList as $val)
                                <option value="{{ $val['id'] }}" data-plan='@json(json_decode($val[' vip_plans'] ?? '[]' , true))'>
                                    {{ $val['name'] }}
                                </option>
                                @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="">Package List</label>
                            <select name="package_id" id="package_id" class="form-control" required>
                                <option value="" disabled selected>Select Package</option>
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="">Purohit Choose</label>
                            <select name="purohit_id" id="purohit_id" class="form-control" required>
                                <option value="" disabled selected>Select Purohit Ji</option>
                            </select>
                        </div>

                        <div class="col-md-4 form-group">
                            <label for="">Number Of Yajman</label>
                            <div class="d-flex">
                                <input class="form-control text-align-direction setmax-limit-devotees" type="text"
                                    placeholder="Enter Devotees Number" required
                                    oninput="this.value=this.value.replace(/\D/g,'').slice(0,2)">
                                <button type="button" class="btn btn-outline-success"
                                    id="checkDevotees">Devotees</button>
                            </div>
                        </div>
                        <!-- Receipt Price Field (default hidden) -->
                        <div class="col-md-4 form-group" id="receipt-price-wrapper">
                            <label for="receipt-price-input">Receipt Price</label>
                            <input class="form-control text-align-direction" type="number" value=""
                                placeholder="Receipt Price" id="receipt-price-input" readonly>
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="">Paymant Mode Select</label>
                            <select id="payment_mode" class="form-control">
                                <option value="cash" selected>Cash</option>
                                <option value="online">Online</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group online-qr-code-show"></div>
                            <div class="col-md-6 form-group">
                                <input type="text" id="urlShow" class="d-none form-control" readonly>
                            </div>
                            <div class="col-md-6 form-group d-none" id="paymentDetailsSuccess">
                                <div class="card border-success shadow-sm">
                                    <div class="card-header bg-success text-white text-center">
                                        Payment Success Details
                                    </div>
                                    <div class="card-body">
                                        <p><strong>Transaction ID:</strong> <span id="paymentId"></span></p>
                                        <p><strong>Amount:</strong> <span id="paymentAmount"></span></p>
                                        {{-- <p><strong>Order ID:</strong> <span id="OrderId"></span></p> --}}
                                    </div>
                                </div>
                            </div>


                        </div>
                        <div class="col-md-6">
                            <div class="form-group float-end">
                                <button type="button" onclick="CreateBooking()"
                                    class="btn btn-primary d-block create-prints">Create Order</button>
                            </div>
                        </div>
                        <!-- Divider -->
                        <div class="col-12">
                            <hr style="border-top: 2px solid #ccc; margin: 20px 0;">
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="orderId"><strong>Order ID</strong></label>
                                <input type="text" id="orderId" class="form-control order-id-show"
                                    placeholder="Enter Order Id">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="orderId"><strong>Select Receipt Type to Print:</strong></label>
                            <div class="form-group d-flex gap-2">
                                <a onclick="PintingOrders('full');$('.online-qr-code-show').html('')"
                                    class="btn btn-outline-primary w-50 fs-6" id="print-button1"> Print Full (For
                                    Customer)</a>
                                <a onclick="PintingOrders('pandit');$('.online-qr-code-show').html('')"
                                    class="btn btn-outline-success w-50 fs-6" id="print-button2">Print Pandit (For
                                    Purohit)</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <button
        onclick="window.print();$('.create-prints').prop('disabled', false);$('.create-prints').text('Create Order')"
        class="d-none btn btn-primary print_Receipt">Print Receipt</button>
    <div class="receipt d-none" id="print-receipt">
        <!-- Top Logo -->
    </div>


</div>
{{-- User Information --}}
<div class="modal fade" id="AadharDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    User Infomation
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success waves-effect waves-light" onclick="saveDevoteesArray()"
                    data-dismiss="modal">
                    save
                </button>
                <button type="button" class="btn btn-secondary waves-effect waves-light" data-dismiss="modal">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
{{-- Pandit Assing --}}
<!-- Assign Pandit Modal -->
<div class="modal fade" id="assignPanditModal" tabindex="-1">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignPanditLabel">Assign Pandit</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-3"> Here you can assign or change the Purohit for this Darshan order.
                    Please make sure to save the changes after updating.
                </p>
                <input type="hidden" id="assign_order_id" name="id">
                <div class="mb-3">
                    <label for="purohit-select" class="form-label">Select Pandit</label>
                    <select class="form-control" id="purohit-select">
                        <option value="">-- Select Pandit --</option>
                        @foreach ($purohits as $purohit)
                        <option value="{{ $purohit->id }}" data-temple-id="{{ $purohit->temple_id }}">
                            {{ $purohit->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button class="btn btn-primary" onclick="savePandit()">Save</button>
            </div>
        </div>
    </div>
</div>
{{-- Print Model --}}
<div class="modal fade" id="assignPrintModal" tabindex="-1">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Print Receipt</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="assign_id" name="id">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><strong>Order ID</strong></label>
                            <input type="text" id="orderId" class="form-control order-id-show"
                                placeholder="Enter Order Id" readonly>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label><strong>Select Receipt Type to Print:</strong></label>
                        <div class="form-group d-flex gap-2">
                            <!-- Modal Print Buttons -->
                            <a onclick="PintingOrders('full','modal');$('.online-qr-code-show').html('')"
                                class="btn btn-outline-primary w-50 fs-6" id="print-button1"> Print Full (For
                                Customer)</a>
                            <a onclick="PintingOrders('pandit','modal');$('.online-qr-code-show').html('')"
                                class="btn btn-outline-success w-50 fs-6" id="print-button2">Print Pandit (For
                                Purohit)</a>

                            <!-- Modal Receipt -->
                        </div>
                    </div>
                </div>
                <button
                    onclick="window.print();$('.create-prints').prop('disabled', false);$('.create-prints').text('Create Order')"
                    class="d-none btn btn-primary print_Receipt">Print Receipt</button>
                <div class="receipt d-none" id="modal-print-receipt">
                    <!-- Top Logo -->
                </div>

            </div>

        </div>
    </div>
</div>
{{-- Oder For QR DETAILS --}}
<!-- Bootstrap Modal -->
<div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="orderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <!-- Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="orderModalLabel">Order Details</h5>
                <p class="mb-0 text-muted small">
                    If the customer has come to the counter, please collect the cash payment and mark the order as
                    complete.
                </p>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <!-- Body -->
            <div class="modal-body" id="orderDetails">
                <!-- Dynamic details will come here -->
            </div>

            <!-- Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button id="confirmPaymentBtn" type="button" class="btn btn-success">Confirm Payment</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="imagePreviewModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content bg-transparent border-0 shadow-none">
            <div class="modal-body text-center position-relative">
                <img id="previewImage" src="" class="img-fluid rounded" alt="Preview" />
                <button type="button" class="close position-absolute" style="top: -10px; right: -10px;" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="bg-white rounded-circle px-2">&times;</span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
@push('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script src="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/js/intlTelInput.js') }}"></script>
<script src="{{ theme_asset(path: 'public/assets/front-end/js/country-picker-init.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const getPurohitUrl = "{{ url('trustees-vendor/darshan-booking/get-purohits') }}";
    const getFetchOrder = "{{ url('trustees-vendor/darshan-booking/get-ordersBookFetch') }}";
    const getOrderHide = "{{ url('trustees-vendor/darshan-booking/get-ordersHide/:order_id') }}";
</script>
<script>
    let table = $('#tableList').DataTable({
        pageLength: 5,
        scrollY: '500px',
        scrollCollapse: true,
        paging: true,
        fixedHeader: true,
        fixedFooter: true,
        lengthMenu: [
            [5, 10, 25, -1],
            [5, 10, 25, "All"]
        ],
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        let templeSelect = document.getElementById("temple_id");
        let packageSelect = document.getElementById("package_id");
        let purohitSelect = document.getElementById("purohit_id");

        templeSelect.addEventListener("change", function() {
            let selectedOption = templeSelect.options[templeSelect.selectedIndex];
            let plans = selectedOption.dataset.plan ? JSON.parse(selectedOption.dataset.plan) : [];
            let templeId = templeSelect.value;
            const receiptWrapper = document.getElementById("receipt-price-wrapper");
            const receiptInput = document.getElementById("receipt-price-input");


            // Clear existing packages
            packageSelect.innerHTML = '<option value="" disabled selected>Select Package Now</option>';
            // Reset purohit dropdown
            purohitSelect.innerHTML =
                '<option value="" disabled selected>Select Purohit Ji Now</option>';
            // Append packages
            if (Array.isArray(plans)) {
                plans.forEach(plan => {
                    let opt = document.createElement("option");
                    opt.value = plan?.id ?? "";
                    opt.dataset.price = plan?.package?.[0]?.price ?? 0;
                    opt.dataset.limit = plan?.package?.[0]?.limit ?? "";
                    opt.dataset.receipt_price = plan?.package?.[0]?.receipt_price ?? 0;
                    opt.dataset.platform_fee = plan?.package?.[0]?.platform_fee ?? 0;
                    opt.dataset.platform_gst = plan?.package?.[0]?.platform_gst ?? 0;
                    opt.dataset.platform_base_price = plan?.package?.[0]?.platform_base_price ?? 0;
                    opt.textContent = (plan?.name ?? "Unnamed Plan") + " (" + (plan?.package?.[
                        0
                    ]?.name ?? "") + " Rs." + (plan?.package?.[0]?.price ?? 0) + ")";
                    packageSelect.appendChild(opt);
                });
            }
            packageSelect.addEventListener("change", function() {
                let selectedOption = this.options[this.selectedIndex];
                let receiptPrice = selectedOption.dataset.receipt_price;
                receiptInput.value = receiptPrice;
            });
            // Fetch Purohits via AJAX
            if (templeId) {
                fetch(`${getPurohitUrl}/${templeId}`)
                    .then(response => response.json())
                    .then(data => {
                        purohitSelect.innerHTML =
                            '<option value="" disabled selected>Select Purohit Ji Now</option>';
                        data.forEach(purohit => {
                            let opt = document.createElement("option");
                            opt.value = purohit.id;
                            opt.textContent = purohit.name;
                            purohitSelect.appendChild(opt);
                        });
                    })
                    .catch(error => console.error("Error fetching Purohits:", error));
            }
        });
    });
    $('#package_id').on('change', function() {
        let packageLimit = $(this).find(':selected').data('limit');
        let input = $('.setmax-limit-devotees');
        if (packageLimit == 0) {
            packageLimit = 100;
        }
        input.attr('max', packageLimit);
        if (parseInt(input.val()) > packageLimit) {
            input.val(packageLimit);
        }
    });
    $('.setmax-limit-devotees').on('input', function() {
        let max = parseInt($(this).attr('max')) || Infinity;
        let val = parseInt($(this).val()) || 0;
        if (val > max) {
            $(this).val(max);
            toastr.error(max + " Devotees Darshan");
        }
        AddDevoteesMember();
    });
    let devoteesArray = [];

    function AddDevoteesMember() {
        let persons = parseInt($('.setmax-limit-devotees').val()) || 0;
        let htmls = '';
        for (let i = 1; i <= persons; i++) {
            let oldData = devoteesArray[i - 1] || {
                name: (i == 1) ? $('#customer_name').val() || `devotees${i}` : `devotees${i}`,
                address: (i == 1) ? $('#customer_address').val() || `devotees${i}` : `devotees${i}`,
                phone: (i == 1) ? ($('.iti__selected-flag').text() + $('#person-number').val() || "0000000000") : "0000000000",
                aadhar: (i == 1) ? ($('#aadhar').val() || "000000000000") : "000000000000"
            };

            htmls += `
                    <div class="row">
                        <div class="col-md-3 form-group">
                            <label for="">User Name</label>
                            <input type="text" name="user_name[]" 
                                id="customer_name_${i}" 
                                class="form-control" 
                                placeholder="Enter User Full Name" 
                                value="${oldData.name}">
                        </div>
                        <div class="col-md-3 form-group">
                            <label for="">User Name</label>
                            <input type="text" name="user_address[]" 
                                id="customer_address${i}" 
                                class="form-control" 
                                placeholder="Enter User Address" 
                                value="${oldData.address}">
                        </div>
                        <div class="col-md-3 form-group">
                            <label for="">Phone Number</label>
                            <input class="form-control text-align-direction phone-input-with-country-picker" 
                                type="tel" 
                                name="person_phone[]" 
                                id="person-number_${i}" 
                                placeholder="Enter User Phone Number" 
                                value="${oldData.phone}"
                                required 
                                oninput="this.value=this.value.replace(/\\D/g,'').slice(0,15)">
                        </div>
                        <div class="col-md-3 form-group">
                            <label for="">Aadhar Number</label>
                            <input class="form-control text-align-direction" 
                                type="text" 
                                name="aadhar_number[]" 
                                id="aadhar_${i}" 
                                placeholder="Enter User Aadhar Number" 
                                value="${oldData.aadhar}"
                                required 
                                oninput="this.value=this.value.replace(/\\D/g,'').slice(0,12)">
                        </div>
                    </div>
                `;
            devoteesArray[i - 1] = oldData;
        }

        $('#AadharDetailsModal .modal-body').html(htmls);
    }
    $('#checkDevotees').click(function() {
        $('#AadharDetailsModal').modal('show');
    })

    function saveDevoteesArray() {
        devoteesArray = [];
        $('#AadharDetailsModal .modal-body .row').each(function(i, row) {
            devoteesArray.push({
                name: $(row).find('[name="user_name[]"]').val(),
                address: $(row).find('[name="user_address[]"]').val(),
                phone: $(row).find('[name="person_phone[]"]').val(),
                aadhar: $(row).find('[name="aadhar_number[]"]').val()
            });
        });
    }

    function CreateBooking() {
        let temple_id = $('#temple_id').val();
        let package_id = $('#package_id').val();
        let purohit_id = $('#purohit_id').val();
        let date = $('.pickDate').val();
        let receipt_price_id = $('#receipt_price').val();
        let platform_base_price_id = $('#platform_base_price').val();
        let platform_gst_id = $('#platform_gst').val();
        let payment_mode = $('#payment_mode').val();
        let user_name = $('#customer_name').val().trim();
        let user_address = $('#customer_address').val().trim();
        let person_phone = $('#person-number').val().trim();
        let member = $('.setmax-limit-devotees').val().trim();
        if (!temple_id) {
            toastr.error('Please select a Temple Name');
            return;
        }
        if (!package_id) {
            toastr.error('Please select a Package Name');
            return;
        }
        if (!user_name) {
            toastr.error('Please Enter User Name');
            return;
        }
        if (!user_address) {
            toastr.error('Please Enter User Name');
            return;
        }
        if (!person_phone || person_phone.length !== 10 || !/^\d+$/.test(person_phone)) {
            toastr.error('Please enter a valid 10-digit phone number');
            return;
        }
        if (1 > member) {
            toastr.error('Please Enter Member Number');
            return;
        }
        if (!payment_mode) {
            toastr.error('Please select a Payment Mode');
            return;
        }
        Swal.fire({
            html: `
                <h3 style="text-align:center; margin-top:15px; margin-bottom:10px; font-size:16px; font-weight:bold;">
                    Confirm the Details & Amount
                    <h4>${$('#temple_id option:selected').text()}</h4>
                </h3>
                <table style="font-size:14px; margin-top:10px; border-collapse: collapse; width:100%;">
                  <tr>
                        <td style="padding:4px 8px;"><strong>Puja Date :</strong></td>
                        <td style="padding:4px 8px;">${$('#puja-date').val()}</td>
                    </tr>
                  
                    <tr>
                        <td style="padding:4px 8px;"><strong>Total Member :</strong></td>
                        <td style="padding:4px 8px;">${member} Members</td>
                    </tr>
                    <tr>
                        <td style="padding:4px 8px;"><strong>Per Price Yajman :</strong></td>
                        <td style="padding:4px 8px;">₹ ${$('#package_id option:selected').data('price')} /-</td>
                    </tr>
                    <tr>
                        <td style="padding:4px 8px;"><strong>Recipt Amount :</strong></td>
                        <td style="padding:4px 8px;">₹ ${$('#package_id option:selected').data('receipt_price')} /-</td>
                    </tr>
                    <tr>
                        <td style="padding:4px 8px;"><strong>Platform Fees :</strong></td>
                        <td style="padding:4px 8px;">₹ ${$('#package_id option:selected').data('platform_fee')} /-</td>
                    </tr>
                    <tr>
                        <td style="padding:4px 8px;"><strong>Total Price :</strong></td>
                        <td style="padding:4px 8px;">
                          ₹ ${
                                ((parseInt($('#package_id option:selected').data('price')) || 0) * member) +
                                ((parseInt($('#package_id option:selected').data('receipt_price')) || 0) * member) +
                                ((parseInt($('#package_id option:selected').data('platform_fee')) || 0) * member)
                            } /-

                        </td>
                    </tr>

                </table>
            `,
            showCancelButton: true,
            confirmButtonText: 'Yes, proceed',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $('.create-prints').prop('disabled', true);
                $('.create-prints').text('Please Wait...');
                $('#print-receipt').addClass('d-none');
                $(".print_Receipt").addClass('d-none');
                $.ajax({
                    url: "{{ route('trustees-vendor.darshan-booking.darshan-booking-save') }}",
                    data: {
                        temple_id,
                        package_id,
                        purohit_id,
                        date,
                        receipt_price_id,
                        platform_gst_id,
                        platform_base_price_id,
                        person_phone: $('.iti__selected-flag').text() + person_phone,
                        user_name: $('#customer_name').val(),
                        user_address: $('#customer_address').val(),
                        payment_mode,
                        userList: JSON.stringify(devoteesArray),
                        _token: '{{ csrf_token() }}',
                    },
                    dataType: "json",
                    type: 'POST',
                    success: function(response) {
                        $('.online-qr-code-show').html('');
                        if (response.success == 1 && response.status == 1) {
                            $('.create-prints').text('Print');
                            $('.order-id-show').val(response.data['order_id']);
                            // PintingOrders();
                        } else if (response.success == 1 && response.status == 2) {
                            $('.online-qr-code-show').html(response.data);
                            $('#urlShow').val(response.paymentID);
                            $('#urlShow').removeClass('d-none');
                        } else {
                            toastr.error(response.message, '', {
                                positionClass: 'toast-top-right'
                            });
                            $('.create-prints').prop('disabled', false);
                            $('.create-prints').text('Create Order');
                        }
                    },
                    error: function(xhr, status, error) {
                        let message = 'Something went wrong. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        } else if (xhr.responseText) {
                            message = xhr.responseText;
                        }
                        toastr.error(message);
                        $('.create-prints').prop('disabled', false);
                        $('.create-prints').text('Create Order');
                    }
                });
            }
        });
    }
</script>
{{-- Set interval Payment --}}
<script>
    let checkInterval = setInterval(() => {
        let qrDiv = document.querySelector('.online-qr-code-show');
        if (qrDiv) {
            if (qrDiv.innerHTML.trim() === "") {

            } else {
                $.ajax({
                    url: "{{ route('trustees-vendor.darshan-booking.get.payment-check-status') }}",
                    data: {
                        id: $('#urlShow').val(),
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: "json",
                    type: "post",
                    success: function(data) {
                        if (data.is_paid == 1) {
                            toastr.success("Payment Successfully Received!", "Success");
                            $('.online-qr-code-show').html('');
                            $('#urlShow').addClass('d-none');
                            $('#paymentDetailsSuccess').removeClass('d-none');
                            $('#paymentId').text(data.data.transaction_id ?? 'N/A');
                            $('#paymentAmount').text(data.data.payment_amount ?? '0');
                            // $('#OrderId').text(data.data.order_id ?? '');
                            $('.create-prints').prop('disabled', false);
                            $('.create-prints').text('Create Order');
                        } else if (data.is_paid == 2) {
                            toastr.error("Payment Not Successfully Received!", "Error");
                            $('.online-qr-code-show').html('');
                            $('#paymentDetailsSuccess').removeClass('d-none');
                            $('.create-prints').prop('disabled', false);
                            $('.create-prints').text('Create Order');
                        }
                    }

                });
            }
        } else {
            console.log("Div not found in DOM");
        }
    }, 10000);
</script>
{{-- Set interval Payment --}}

<!-- ///////////////////////////////////////////////////////////////////////////////////////////////////// -->
<script>
    function maskPhone(str, startVisible = 0, endVisible = 3) {
        if (!str) return str;
        const start = str.slice(0, startVisible);
        const end = str.slice(-endVisible);
        const masked = '*'.repeat(str.length - (startVisible + endVisible));
        return start + masked + end;
    }

    function PintingOrders(type, source = 'page') {
        var orderid = $('.order-id-show').val();
        if (source === 'modal') {
            orderid = $("#orderId").val(); // id me P capital na ho
            $('#modal-print-receipt').removeClass('d-none');
            $('#print-receipt').addClass('d-none');
        } else {
            orderid = $("#orderId").val();
            $('#print-receipt').removeClass('d-none');
            $('#modal-print-receipt').addClass('d-none');
        }

        $(".print_Receipt").removeClass('d-none');


        if (!orderid) {
            toastr.error('Please Enter Order Id');
            return;
        }

        // reset
        $('.puja_name_set').text('');
        $('.user_name_set').text('');
        $('.user_address_set').text('');
        $('.phone_number_set').text(0);
        $('.puja_price_set').text(0);
        $('.puja_discount_price_set').text(0);
        $('.puja_tax_price_set').text(0);
        $('.puja_total_price_set').text(0);
        if (type === 'full') {
            $('#print-button1').text('Please Wait ...').prop('disabled', true);
            $('#print-button2').text('Print Pandit').prop('disabled', false);
        } else if (type === 'pandit') {
            $('#print-button2').text('Please Wait ...').prop('disabled', true);
            $('#print-button1').text('Print Full').prop('disabled', false);
        }


        $.ajax({
            url: "{{ route('trustees-vendor.darshan-booking.vip-darshan-booking-order-id') }}",
            data: {
                orderid,
                _token: '{{ csrf_token() }}'
            },
            dataType: "json",
            type: "post",
            success: function(data) {
                if (data.success == 1) {
                    toastr.success(data.message, '', {
                        positionClass: 'toast-top-right'
                    });

                    // sab purane receipts clear kar do
                    $('#print-receipt .receipt-body, #modal-print-receipt .receipt-body').remove();

                    var Puja = data.data;

                    if (Puja.members && Puja.members.length > 0) {
                        Puja.members.forEach(function(member, index) {
                            let receiptHtml = `
                                    <div class="receipt-body" style="border:1px dashed #000; padding:10px; margin-bottom:15px; text-align:center; page-break-inside: avoid;">
                                        <h4 class="receipt-title" style="text-align:center; margin:0 0 10px 0; font-size:18px; font-weight:bold; text-decoration:underline;">
                                            ${Puja.title ?? ''}
                                        </h4>
                                        <p style="text-align:center; margin:0 0 10px 0; font-size:15px; font-weight:600;">
                                            ${Puja.temple.name ?? ''}
                                        </p>
                                        <div style="display:flex; justify-content:space-between; font-size:13px; font-weight:600; margin-bottom:8px;">
                                            <div>OrderID: ${Puja.order_id}</div>
                                            <div>Date: ${Puja.date} , ${Puja.time}</div>
                                        </div>
                                        <table style="width:100%; border-collapse: collapse; text-align:left; font-size:14px;">
                                            <tr>
                                                <th><b>Puja Assign</b></th>
                                                <td style="text-align:right;">${Puja.package_name}</td>
                                            </tr>
                                            <tr>
                                                <th style="width:30%; vertical-align:top;"><b>Pandit Name</b></th>
                                                <td style="text-align:right;">
                                                    ${Puja.purohit?.name ?? '<span style="color:red; font-weight:bold;">Not Assigned</span>'}
                                                </td>
                                            </tr>
                                            <tr>
                                                <th style="vertical-align:top;"><b>Yajman Details</b></th>
                                                <td style="text-align:right;">
                                                    <div><b>Name:</b> ${member.name}</div>
                                                    <div><b>Address:</b> ${member.address}</div>
                                                    ${member.phone && !/^0+$/.test(member.phone) ? `<div><b>Phone:</b> ${maskPhone(member.phone,3,3)}</div>` : ''}
                                                    ${member.aadhar && !/^0+$/.test(member.aadhar) ? `<div><b>Aadhar:</b> ${maskPhone(member.aadhar,0,4)}</div>` : ''}
                                                </td>
                                            </tr>
                                        </table>
                                `;

                            // type-based prices
                            if (type === 'full') {
                                receiptHtml += `
                                        <hr>
                                        <table width="100%">                           
                                            <tr><td>Puja Price</td><td style="text-align:right;">${(Number(Puja.price)/Number(Puja.people_qty)).toLocaleString("en-US",{style:"currency",currency:"{{ getCurrencyCode() }}"})}</td></tr>
                                            <tr><td>Receipt Price</td><td style="text-align:right;">${(Number(Puja.receipt_price)/Number(Puja.people_qty)).toLocaleString("en-US",{style:"currency",currency:"{{ getCurrencyCode() }}"})}</td></tr>
                                            <tr><td>Platform Fee</td><td style="text-align:right;">${(Number(Puja.platform_fee)/Number(Puja.people_qty)).toLocaleString("en-US",{style:"currency",currency:"{{ getCurrencyCode() }}"})}</td></tr>
                                            <tr><td>Total Price</td><td style="text-align:right;">${(Number(Puja.final_amount)/Number(Puja.people_qty)).toLocaleString("en-US",{style:"currency",currency:"{{ getCurrencyCode() }}"})}</td></tr>
                                        </table>
                                    `;
                            } else if (type === 'pandit') {
                                receiptHtml += `
                                        <hr>
                                        <table width="100%">                           
                                            <tr><td>Receipt Price</td><td style="text-align:right;">${(Number(Puja.receipt_price)/Number(Puja.people_qty)).toLocaleString("en-US",{style:"currency",currency:"{{ getCurrencyCode() }}"})}</td></tr>
                                        </table>
                                    `;
                            }

                            receiptHtml += `
                                    <p><img src="data:image/png;base64,${member.qrcode}"></p>
                                    <p><strong>Barcode:</strong> ${member.barcode}</p>
                                    <hr>
                                    <p style="font-size:10px;"> <strong>Note:</strong> This is a system-generated invoice and does not require a physical signature.</p>
                                    <p style="font-size:12px;">Powered by Mahakal.com - 100 करोड़ सनातनियों का अपना Spiritual-Tech Platform</p>
                                    </div>
                                `;

                            // 👇 source ke hisaab se append
                            if (source === 'modal') {
                                $('#modal-print-receipt').append(receiptHtml);
                            } else {
                                $('#print-receipt').append(receiptHtml);
                            }
                        });
                    }

                    // 👇 source ke hisaab se show/hide
                    if (source === 'modal') {
                        $('#modal-print-receipt').removeClass('d-none');
                        $('#print-receipt').addClass('d-none');
                    } else {
                        $('#print-receipt').removeClass('d-none');
                        $('#modal-print-receipt').addClass('d-none');
                    }

                    $(".print_Receipt").removeClass('d-none');

                } else {
                    $('#print-receipt, #modal-print-receipt').addClass('d-none');
                    $(".print_Receipt").addClass('d-none');
                    toastr.error(data.message, '', {
                        positionClass: 'toast-top-right'
                    });
                }
            },
            error: function(xhr, status, error) {
                let message = 'Something went wrong. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) message = xhr.responseJSON.message;
                else if (xhr.responseText) message = xhr.responseText;
                toastr.error(message);
            }
        });

    }

    // Function to generate today + next 2 days
    function fillDates() {
        let container = document.getElementById("date-buttons");
        container.innerHTML = "";

        let today = new Date();
        let labels = ["Today", "Tomorrow", "Next Day"];

        for (let i = 0; i <= 2; i++) {
            let date = new Date();
            date.setDate(today.getDate() + i);

            let day = String(date.getDate()).padStart(2, '0');
            let month = String(date.getMonth() + 1).padStart(2, '0');
            let year = date.getFullYear();
            let formatted = `${day}-${month}-${year}`;

            let btn = document.createElement("button");
            btn.type = "button";
            btn.className = "btn btn-outline-success";
            btn.textContent = labels[i];

            btn.addEventListener("click", function() {
                document.querySelectorAll("#date-buttons button")
                    .forEach(b => b.classList.remove("active"));
                btn.classList.add("active");
                document.getElementById("puja-date").value = formatted;
            });
            if (i === 0) {
                btn.classList.add("active");
                document.getElementById("puja-date").value = formatted;
            }

            container.appendChild(btn);
        }
    }
    fillDates();
</script>
<script>
    let offset = 0;
    let allLoaded = false;
    let ordersCache = []; // store all fetched orders

    function loadOrders(limit = 5) {
        if (!allLoaded && ordersCache.length < offset + limit) {
            document.getElementById("loading").style.display = "block";
            fetch(`${getFetchOrder}?offset=${offset}&limit=${limit}`)
                .then(res => res.json())
                .then(data => {
                    document.getElementById("loading").style.display = "none";

                    if (!Array.isArray(data) || data.length === 0) {
                        allLoaded = true;
                        if (ordersCache.length === 0) {
                            document.getElementById("noMoreOrders").style.display = "block";
                        }
                        return;
                    }

                    ordersCache = ordersCache.concat(data);
                    offset += data.length;
                    renderOrders();
                })
                .catch(err => {
                    document.getElementById("loading").style.display = "none";
                    alert("Error loading orders!");
                });
        } else {
            renderOrders();
        }
    }

    function renderOrders() {
        const body = document.querySelector("#ordersBody");
        body.innerHTML = "";
        // show only latest 5
        const visibleOrders = ordersCache.slice(0, 5);

        visibleOrders.forEach(order => {
            let row = `
                    <tr id="order-${order.id}" style="position: relative; padding-left: 40px;">
                            <td style="position: relative; height: 50px; padding-left: 25px;">
                                <!-- Order ID -->
                                <span style="display: block;">${order.id}</span>
                                
                                <!-- Platform (bottom border se chipka hua) -->
                               <span style="position: absolute; bottom: 2px; left: 20px; color: white; font-weight: bold; font-size: 10px;   text-transform: uppercase; padding: 2px 6px; border-radius: 4px;"
                                    class="${
                                        order.platform === 'qr' ? 'bg-warning' :
                                        order.platform === 'app' ? 'bg-primary' :
                                        order.platform === 'web' ? 'bg-info' :
                                        order.platform === 'admin' ? 'bg-success' :
                                        'bg-secondary' // default color
                                    }">
                                    ${order.platform}
                                </span>

                            </td>


                            <td>${order.order_id}</td>
                            <td>${order.purohit && order.purohit.name ? order.purohit.name : 'N/A'} <br> <br>
                            ${order.date || 'N/A'} </td>
                            <!-- Table Row -->
                            <td class="d-flex align-items-center">
                            <img src="${ (order.members && order.members.length > 0 && order.members[0].image) 
                                ? order.members[0].image 
                                : '{{ asset("public/assets/back-end/img/customer-info.png") }}'
                                }"   alt="Avatar"  style="width:28% !important; cursor:pointer;"
                                class="rounded-circle" data-toggle="modal"  data-target="#imagePreviewModal" 
                                onclick="setPreviewImage(this.src)" />
                                <span class="ml-2 font-weight-medium">
                                    ${(order.members && order.members.length > 0 && order.members[0].name) 
                                    ? order.members[0].name    : 'N/A'  }
                                </span>
                            </td>

                            <td style="position: relative; height: 50px; padding-left: 25px;"> <span style="display: block;">${order.transaction_id || 'N/A'}</span>
                                <!-- Platform (bottom border se chipka hua) -->
                            ${order.status == 0 
                                ? `<span style="position: absolute; bottom: 2px; left: 20px; color: white; font-weight: bold; font-size: 10px; text-transform: uppercase; padding: 2px 6px; border-radius: 4px;" 
                                                        class="bg-danger">
                                                        pending for Payment
                                                    </span>` 
                                : ''
                            }
                            </td>
                            <td style="position: relative; height: 50px; padding-left: 25px;"> <span style="display: block;">₹ ${order.final_amount || 0} / -</span>
                                    <!-- Platform (bottom border se chipka hua) -->
                                <span style="position: absolute; bottom: 2px; left: 20px; color: white; font-weight: bold; font-size: 10px; text-transform: uppercase; padding: 2px 6px; border-radius: 4px;"
                                        class="${order.payment_mode === 'Paid' ? 'bg-success' : 'bg-warning'}">
                                        ${order.payment_mode}
                                    </span>
                            </td>
                            <td>
                                <div class="d-flex gap-2" style="max-width: 250px;">
                                    <!-- Done -->
                                    <button   class="btn btn-sm flex-fill ${order.status == 0 ? 'btn-warning' : 'btn-success'}" 
                                        onclick="handleOrder('${order.id}', ${order.status})" 
                                        title="${order.status == 0 ? 'Pending - Confirm Payment' : 'Done'}">
                                        <i class="${order.status == 0 ? 'tio-time' : 'tio-checkmark-circle'}"></i>
                                    </button>

                                    <!-- Assign/Edit Purohit -->
                                    <button class="btn btn-sm flex-fill ${order.purohit_id ? 'btn-primary' : 'btn-success'}"
                                        data-id="${order.id}"  data-purohit-id="${order.purohit_id || ''}"  data-temple-id="${order.temple_id || ''}"  onclick="openPanditModal(this)" title="${order.purohit_id ? 'Edit Purohit' : 'Assign Purohit'}">
                                        <i class="tio-users-switch"></i>
                                    </button>
                                    <!-- Print -->
                                    <button class="btn btn-sm btn-warning flex-fill"
                                            data-id="${order.id}"  
                                            data-orderid="${order.order_id}" 
                                            onclick="openPrintModal(this)" 
                                            title="Print">
                                        <i class="tio-print"></i>
                                    </button>
                                </div>
                            </td>
                    </tr>
                `;
            body.insertAdjacentHTML('beforeend', row);
        });
    }

    function hideOrder(order_id) {
        let url = getOrderHide.replace(':order_id', order_id);
        fetch(url, {
            method: "PATCH",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Content-Type": "application/json"
            }
        }).then(res => res.json()).then(data => {
            if (data.success) {
                ordersCache = ordersCache.filter(order => order.id != order_id);
                renderOrders();
                if (ordersCache.length < 5 && !allLoaded) {
                    loadOrders(5 - ordersCache.length);
                }
            }
        }).catch(err => {
            console.error("Hide error:", err);
            alert("Error hiding order!");
        });
    }

    function handleOrder(order_id, status) {
        if (status === 0) {
            let order = ordersCache.find(o => o.id == order_id);
            if (!order) return;
            $("#orderModal").modal('show');
            document.getElementById("orderDetails").innerHTML = `
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-3">
                            <h6 class="mb-3 text-center fw-bold text-uppercase">Order Invoice</h6>
                            <table class="table table-sm table-bordered mb-0">
                                <tbody>
                                    <tr>
                                        <th style="width: 40%;">Package Name</th>
                                        <td>${order.package_name ?? '-'}</td>
                                    </tr>
                                    <tr>
                                        <th>Order ID</th>
                                        <td>${order.order_id}</td>
                                    </tr>
                                    <tr>
                                        <th>Transaction ID</th>
                                        <td>${order.transaction_id ?? '-'}</td>
                                    </tr>
                                    <tr>
                                        <th>Amount</th>
                                        <td>₹ ${order.final_amount ?? '-'}</td>
                                    </tr>
                                    <tr>
                                        <th>Total Person</th>
                                        <td>${order.people_qty ?? '-'}</td>
                                    </tr>
                                    <tr>
                                        <th>Booking Date & time </th>
                                        <td>${order.date ?? '-'} ${order.time ?? '-'} </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                `;

            document.getElementById("confirmPaymentBtn").onclick = function() {
                confirmPayment(order_id);
            };
            document.getElementById("orderModal").classList.remove("hidden");
        } else if (status === 1) {
            hideOrder(order_id, status);
        }
    }

    function confirmPayment(order_id) {
        let url = getOrderHide.replace(':order_id', order_id);

        fetch(url, {
                method: "PATCH",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    status: 1,
                    payment_mode: "complete",
                    is_hidden: 1
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    closeModal();
                    ordersCache = ordersCache.filter(order => order.id != order_id);
                    renderOrders();
                    if (ordersCache.length < 5 && !allLoaded) {
                        loadOrders(5 - ordersCache.length);
                    }
                }
            })
            .catch(err => {
                console.error("Confirm error:", err);
                alert("Error confirming order!");
            });
    }

    function closeModal() {
        $("#orderModal").modal('hide');
    }

    // Assing the Pandit
    function openPanditModal(button) {
        let orderId = $(button).data("id");
        let currentPurohitId = $(button).data("purohit-id") || null;
        let templeId = $(button).data("temple-id") || null;
        $("#assign_order_id").val(orderId);
        // Filter Pandit dropdown by templeId
        $("#purohit-select option").each(function() {
            let optionTempleId = $(this).data("temple-id"); // you need to add data-temple-id to options
            if (!templeId || optionTempleId == templeId) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
        $("#purohit-select").val(currentPurohitId || '');
        $("#assignPanditModal").modal('show');
    }

    function savePandit() {
        let id = $("#assign_order_id").val();
        let purohitId = $("#purohit-select").val();

        if (!purohitId) {
            alert("Please select a Pandit.");
            return;
        }

        $.ajax({
            url: "{{ route('trustees-vendor.darshan-booking.get.update-purohit') }}",
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                id: id,
                purohit_id: purohitId
            },
            success: function(response) {
                if (response.success) {
                    $(`#purohit-name-${id}`).text(response.purohit_name);
                    let table = $('#tableList').DataTable();
                    table.row($(`#order-${id}`)).invalidate().draw(false);
                    renderOrders();
                    toastr.success("Purhit updated successfully!", "Success");
                    $("#assignPanditModal").modal('hide');
                } else {
                    toastr.errror("Not Assing/Change Purohit Please Try Again!", "Error");
                }
            }
        });
    }

    function openPrintModal(button) {
        let id = $(button).data("id");
        let orderId = $(button).data("orderid");
        $("#assign_id").val(id);
        $(".order-id-show").val(orderId);
        // Show modal
        $("#assignPrintModal").modal('show');
    }

    // initial load
    loadOrders();
</script>
<script>
    function setPreviewImage(src) {
        document.getElementById('previewImage').src = src;
    }
</script>
@endpush