@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app-trustees')

@section('title', 'Puja Booking')
@push('css_or_js')
<link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/css/intlTelInput.css') }}">
<style>
    .receipt {
        width: 300px;
        /* Similar to POS machine width */
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
            margin: 0;
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

        #print-receipt {
            position: absolute;
            left: 0;
            top: 0;
            width: 80mm;
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
    <div class="mb-3">
        <h2 class="h1 mb-0 d-flex gap-2">
            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/') }}" alt="">Puja Booking
        </h2>
    </div>
    <div class="row">
        <div class="col-md-12 mb-2">
            <div class="card">
                <div class="card-body">
                    <div class='row'>
                        <div class="col-md-6 form-group">
                            <label for="">User Name</label>
                            <input type="text" name="user_name" id="customer_name" class="form-control" placeholder="Enter User Full Name">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="">Phone Number</label>
                            <input class="form-control text-align-direction phone-input-with-country-picker" type="tel" value="" name="person_phone" id="person-number" placeholder="Enter User Phone Number" required oninput="this.value=this.value.slice(0,10)">
                        </div>
                        <div class="col-md-6 form-group ">
                            <?php $gst_tax =  \App\Models\ServiceTax::find(1); ?>
                            <label for="">Puja List</label>
                            <select name="puja_id" id="puja_name_id" class="form-control" required>
                                <option value="">Select Puja Name</option>
                                @if($pujaList)
                                @foreach($pujaList as $val)
                                <?php $gst_amount = (($val['pprice'] * $gst_tax['trust_puja_tax']) / 100); ?>
                                <option value="{{ $val['id'] }}" data-rprice="{{ $val['rprice'] }}" data-pprice="{{ ($val['pprice'] + $gst_amount) }}" data-discount="{{ $val['discount'] }}" data-gst="{{ ($gst_tax['trust_puja_tax']) }}" data-gst_amount="{{ $gst_amount }}">{{ $val['puja_name'] }}</option>
                                @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="">Paymant Mode Select</label>
                            <select id="payment_mode" class="form-control">
                                <option value="online">Online</option>
                                <option value="cash">Cash</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group online-qr-code-show"></div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group float-end">
                                <button type="button" onclick="CreateBooking()" class="btn btn-primary create-prints">Create Order</button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="text" class="form-control order-id-show" placeholder="Enter Order Id">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <a onclick="PintingOrders();$('.online-qr-code-show').html('')" class="btn btn-primary">Print Now</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="receipt d-none" id="print-receipt">
        <!-- Top Logo -->
        <h4 class="receipt-title">PUJA RECEIPT</h4>
        <p><strong>Puja Name:</strong><span class="puja_name_set"></span></p>
        <p><strong>User Name:</strong><span class="user_name_set"></span></p>
        <p><strong>Phone:</strong><span class="phone_number_set"></span></p>
        <p><strong>Date:</strong> {{ date('d M,Y h:i A')}}</p>
        <hr>
        <table width="100%">
            <tr>
                <td>Puja Price</td>
                <td style="text-align: right;"><span class="puja_price_set"></span></td>
            </tr>
            <tr>
                <td>Discount Price</td>
                <td style="text-align: right;"><span class="puja_discount_price_set"></span></td>
            </tr>
            <tr>
                <td>Tax Price</td>
                <td style="text-align: right;"><span class="puja_tax_price_set"></span></td>
            </tr>
            <tr>
                <td>Total Price</td>
                <td style="text-align: right;"><span class="puja_total_price_set"></span></td>
            </tr>
        </table>
        <hr>

        <p style="text-align: center; font-size: 10px;"> <strong>Note:</strong> This is a system-generated invoice and does not require a physical signature.</p>
        <p style="text-align: center; font-size: 12px;" class="m-0"><span style="position: relative;top: 7px;"> Powered by Mahakal.com</span>&nbsp;<img src="{{ dynamicStorage(path: "storage/app/public/company/".getWebConfig(name: 'company_fav_icon')) }}" alt="Logo" style="max-width: 30px;"></p>
    </div>

    <button onclick="window.print();$('.create-prints').prop('disabled', false);$('.create-prints').text('Create Order')" class="d-none btn btn-primary print_Receipt">Print Receipt</button>
</div>

@endsection

@push('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script src="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/js/intlTelInput.js') }}"></script>
<script src="{{ theme_asset(path: 'public/assets/front-end/js/country-picker-init.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function CreateBooking() {
        let puja_id = $('#puja_name_id').val();
        let payment_mode = $('#payment_mode').val();
        let user_name = $('#customer_name').val().trim();
        let person_phone = $('#person-number').val().trim();
        if (!puja_id) {
            toastr.error('Please select a Puja Name');
            return;
        }
        if (!user_name) {
            toastr.error('Please Enter User Name');
            return;
        }
        if (!person_phone || person_phone.length !== 10 || !/^\d+$/.test(person_phone)) {
            toastr.error('Please enter a valid 10-digit phone number');
            return;
        }
        if (!payment_mode) {
            toastr.error('Please select a Payment Mode');
            return;
        }

        ////////////////////////////////////////////////////
        Swal.fire({
            html: `
                <div style="font-size:14px; margin-top:10px;">
                    <p><strong>Puja Price:</strong>${$('#puja_name_id option:selected').data('rprice')}</p>
                    <p><strong>Discount Price:</strong>${$('#puja_name_id option:selected').data('discount')}</p>
                    <p><strong>Total Tax(${$('#puja_name_id option:selected').data('gst')}):</strong>${$('#puja_name_id option:selected').data('gst_amount')}</p>
                    <p><strong>Total Price:</strong> ${$('#puja_name_id option:selected').data('pprice')} </p>
                </div>
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
                    url: "{{ route('trustees-vendor.puja-management.puja-booking-save') }}",
                    data: {
                        puja_id,
                        user_name,
                        person_phone: $('.iti__selected-flag').text() + person_phone,
                        payment_mode,
                        _token: '{{ csrf_token() }}',
                    },
                    dataType: "json",
                    type: 'POST',
                    success: function(response) {
                        $('.online-qr-code-show').html('');
                        console.log(response.data);
                        console.log(response.success);
                        console.log(response.status);
                        if (response.success == 1 && response.status == 1) {
                            $('.create-prints').text('Print');
                            $('.order-id-show').val(response.data['order_id']);
                            PintingOrders();
                        } else if (response.success == 1 && response.status == 2) {
                            $('.online-qr-code-show').html(response.data);
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
                    }
                });
            }
        });
        // ////////////////////////////////////////////////////////////
    }

    function PintingOrders() {
        var orderid = $('.order-id-show').val();
        $('#print-receipt').addClass('d-none');
        $(".print_Receipt").addClass('d-none');
        if (!orderid) {
            toastr.error('Please Enter Order Id');
            return;
        }
        $('.puja_name_set').text('');
        $('.user_name_set').text('');
        $('.phone_number_set').text(0);
        $('.puja_price_set').text(0);
        $('.puja_discount_price_set').text(0);
        $('.puja_tax_price_set').text(0);
        $('.puja_total_price_set').text(0);
        $.ajax({
            url: "{{ route('trustees-vendor.puja-management.puja-booking-order-id') }}",
            data: {
                orderid,
                _token: '{{ csrf_token() }}',
            },
            dataType: "json",
            type: "post",
            success: function(data) {
                if (data.success == 1) {
                    toastr.success(data.message, '', {
                        positionClass: 'toast-top-right'
                    });
                    $('#print-receipt').removeClass('d-none');
                    $(".print_Receipt").removeClass('d-none');
                    var Puja = data.data;
                    $('.puja_name_set').text(Puja.puja_name);
                    $('.user_name_set').text(Puja.user_name);
                    $('.phone_number_set').text(Puja.user_phone);
                    $('.puja_price_set').text((Number(Puja.rprice)).toLocaleString("en-US", {
                        style: "currency",
                        currency: "{{getCurrencyCode()}}"
                    }));
                    $('.puja_discount_price_set').text((Number(Puja.discount)).toLocaleString("en-US", {
                        style: "currency",
                        currency: "{{getCurrencyCode()}}"
                    }));
                    $('.puja_tax_price_set').text((Number(Puja.tax_amount)).toLocaleString("en-US", {
                        style: "currency",
                        currency: "{{getCurrencyCode()}}"
                    }));
                    $('.puja_total_price_set').text((Number(Puja.pprice) + Number(Puja.tax_amount)).toLocaleString("en-US", {
                        style: "currency",
                        currency: "{{getCurrencyCode()}}"
                    }));
                } else {
                    $('#print-receipt').addClass('d-none');
                    $(".print_Receipt").addClass('d-none');
                    toastr.error(data.message, '', {
                        positionClass: 'toast-top-right'
                    });
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
            }
        });
    }
</script>
@endpush