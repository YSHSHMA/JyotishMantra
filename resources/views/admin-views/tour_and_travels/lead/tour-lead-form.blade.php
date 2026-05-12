@php
use App\Utils\Helpers;
@endphp
@extends('layouts.back-end.app')

@section('title', translate('Tour_leads'))
@push('css_or_js')
<link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet"
    href="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/css/intlTelInput.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta3/css/bootstrap-select.min.css">
<script src="https://maps.googleapis.com/maps/api/js?key={{ $googleMapsApiKey }}&libraries=places&callback=initAutocomplete"></script>
<style>
    .gj-datepicker-bootstrap [role=right-icon] button .gj-icon {
        top: 14px;
        right: 5px;
    }

    .bg-label-primary {
        background-color: #007bff;
        color: #fff;
    }

    .bg-label-primary:hover {
        background-color: #0056b3;
    }

    .bg-label-danger {
        background-color: #dc3545;
        color: #fff;
    }

    .bg-label-danger:hover {
        background-color: #c82333;
    }

    .bg-label-success {
        background-color: #28a745;
        color: #fff;
    }

    .bg-label-success:hover {
        background-color: #218838;
    }

    .bg-label-info {
        background-color: #17a2b8;
        color: #fff;
    }

    .bg-label-info:hover {
        background-color: #117a8b;
    }

    .bg-label-warning {
        background-color: #ffc107;
        color: #212529;
    }

    .bg-label-warning:hover {
        background-color: #e0a800;
    }

    .dropdown-menufollow {
        background-color: #fff;
        border: 1px solid #ccc;
        border-radius: 0.375rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
        padding: 1rem;
        width: 225px;
        margin-right: 13rem;
        text-align: center;
        display: flex;
        gap: 0.5rem;
        position: absolute;
    }

    .d-flex {
        display: flex;
    }

    .justify-content-center {
        justify-content: center;
    }

    .gap-2 {
        gap: 0.5rem;
    }

    .myactionbtn {
        width: 1.625rem !important;
        height: 1.625rem !important;
    }

    .ticket-box {
        background-color: #F7F7F7;
        display: flex;
        border-radius: 0.25rem;
    }

    [theme="dark"] .ticket-box {
        background-color: #323232;
    }

    .ticket-box p {
        color: #999;
        font-size: 0.75rem;
    }

    .ticket-border {
        -webkit-border-start: 2px dashed #fff;
        border-inline-start: 2px dashed #fff;
        position: relative;
    }

    [theme="dark"] .ticket-border {
        -webkit-border-start: 2px dashed var(--bs-white);
        border-inline-start: 2px dashed var(--bs-white);
    }

    .ticket-border::after,
    .ticket-border::before {
        --size: 1rem;
        inline-size: var(--size);
        block-size: var(--size);
        inset-inline-start: -1px;
        inset-block-start: calc(var(--size) / -2);
        transform: translateX(-50%);
        background-color: #fff;
        content: "";
        position: absolute;
        border-radius: var(--size);
    }

    [theme="dark"] .ticket-border::after,
    [theme="dark"] .ticket-border::before {
        background-color: var(--bs-white);
    }

    .ticket-border::before {
        inset-block-start: auto;
        inset-block-end: calc(var(--size) / -2);
    }

    .ticket-amount {
        font-weight: 700;
        font-size: 1.3rem;
        white-space: nowrap;
        -webkit-margin-after: 0.3rem;
        margin-block-end: 0.3rem;
    }

    .ticket-start {
        padding: 1.5rem;
        text-align: center;
    }

    .ticket-start img {
        margin-bottom: 0.5rem;
    }

    .ticket-end {
        padding: 2rem 1rem;
        text-align: center;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        flex-grow: 1;
        gap: 0.5rem;
    }

    .ticket-end button {
        outline: 1px dashed var(--bs-primary);
        color: var(--bs-primary);
        border: none;
        font-weight: 700;
        background-color: rgba(var(--bs-primary-rgb), 0.1);
        padding: 0.5rem 1rem;
        border-radius: 0.25rem;
        -webkit-margin-after: 0.5rem;
        margin-block-end: 0.5rem;
    }

    .ticket-end button:focus {
        outline: 1px dashed var(--bs-primary) !important;
    }

    .ticket-end h6 {
        font-size: 0.85rem;
    }
</style>
@endpush
@section('content')
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 d-flex gap-2">
            {{ translate('Tour_Lead_form') }}
            <span class="badge badge-soft-dark radius-50 fz-14"></span>
        </h2>
    </div>
    <form class="product-form text-start" action="{{ route('admin.tour-lead.tour-lead-save') }}" method="POST" enctype="multipart/form-data" id="services_form">
        @csrf
        <div class="card">
            <div class="card-body">
                <div class="">
                    <div class="card-header pt-0">
                        <div class="d-flex gap-2">
                            <i class="tio-company"></i>
                            <h4 class="mb-0">{{ translate('User_infomation') }}</h4>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-md-4">
                            <label class="title-color" for="platform">{{ translate('platform') }} </label>
                            <select name="platform" id="platform " class="form-control" required="">
                                <option value="">{{ translate('select_platform') }}</option>
                                <option value="app">App</option>
                                <option value="web">Website</option>
                                <option value="admin">Admin Side</option>
                                <option value="instagram">Instagram</option>
                                <option value="facebook">Facebook</option>
                                <option value="ads">Ads</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <div class="col-md-12" id="phone-div">
                                <div class="form-group">
                                    <label
                                        class="form-label font-semibold">{{ translate('phone_number') }}
                                        <small class="text-primary">(
                                            *{{ translate('country_code_is_must_like_for_IND') }} 91
                                            )</small>
                                    </label>
                                    <input
                                        class="form-control text-align-direction phone-input-with-country-picker"
                                        type="tel"
                                        value=""
                                        name="person_phone" id="person-number"
                                        placeholder="{{ translate('enter_phone_number') }}" required
                                        oninput="this.value=this.value.slice(0,10)">
                                    <input type="hidden" class="country-picker-phone-number w-50" name="person_phone" readonly>

                                    <p id="number-validation" class="text-danger" style="display: none">Enter Your Valid Mobile Number</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="title-color" for="user_name">{{ translate('user_Name') }} </label>
                                <input type="text" required name="user_name" class="form-control" id="person-name" placeholder="{{ translate('user_name') }}">
                            </div>
                        </div>

                    </div>
                    <div class="card-header pt-0">
                        <div class="d-flex gap-2">
                            <i class="tio-briefcase"></i>
                            <h4 class="mb-0">{{ translate('Tour_booking_and_Enquiry') }}</h4>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="title-color" for="tour_id">{{ translate('select_tour') }} </label>
                                <select type="text" name="tour_id" class="form-control selectpicker" data-live-search="true">
                                    @if($tourData)
                                    @foreach($tourData as $va)
                                    <option value="{{ $va['id'] }}">{{ $va['tour_name'] }} ({{ (($va['is_person_use'] == 1)?"Per Head":"Cab")}}) {{ $va['number_of_day'] . 'D/' . $va['number_of_night'] . 'N' }}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <button type="button" class="btn btn-sm btn-success" onclick="$('#AllitineraryPlace').modal('show')">Itinerary</button>
                            <button type="button" class="btn btn-sm btn-success" onclick="$('#AllincludedPlace').modal('show')">Included</button>
                            <button type="button" class="btn btn-sm btn-success" onclick="$('#AllexincludedPlace').modal('show')">Excluded</button>
                        </div>
                        <div class="col-md-12">
                            <div class="row row-new-address-date">

                            </div>
                            <div class="row-new-appends-caplist">

                            </div>
                            <div class="row row-new-appends">

                            </div>
                        </div>
                        <input type="hidden" name="booking_package" class="booking_array_package">
                        <input type="hidden" name="amount" class="booking_total_amount">
                        <input type="hidden" name="part_payment" class="part_payment_class" value="full">
                        <input type="hidden" name="whatsapp_msg" class="whatsapp_msg1_send" value="0">
                        <input type="hidden" name="custom_amount_payment" class="amount_payment_class" value="0">
                        <input type="hidden" name="coupon_id" class="coupon_id" value="0">
                        <input type="hidden" name="coupon_amount" class="coupon_amount" value="0">
                        <input type="hidden" name="itinerary_pdf_send" class="itinerary_pdf_send_class" value="0">
                        <input type="hidden" class="is_person_use_tour">
                        <input type="hidden" class="use_date_tour">
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-end gap-3 mt-3 mx-1">
            @if (Helpers::modules_permission_check('Tour', 'Tour Lead', 'add'))
            <button type="button" onclick="pay_summary()" class="btn btn--primary px-4">{{ translate('submit') }}</button>
            @endif
        </div>
    </form>
</div>


<div class="modal fade" id="AllitineraryPlace" tabindex="-1">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    Itinerary Place
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row itinerary_bodys">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary waves-effect waves-light" data-dismiss="modal">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="AllincludedPlace" tabindex="-1">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    Inclusion
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="inclusion_bodys">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary waves-effect waves-light" data-dismiss="modal">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="AllexincludedPlace" tabindex="-1">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    Exclusion
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="exclusion_bodys">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary waves-effect waves-light" data-dismiss="modal">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="paymentSummaryModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Payment Summary</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered" id="paymentSummaryTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Qty</th>
                            <th>Main Price</th>
                            <th>GST %</th>
                            <th>GST Amount</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                <div class="justify-content-between my-3">
                    <span>
                        <form class="needs-validation" action="javascript:" method="post" novalidate id="coupon-code-events-ajax">
                            <div class="d-flex form-control rounded-pill ps-3 p-1">
                                <img width="24" src="{{ theme_asset(path: 'public/assets/front-end/img/icons/coupon.svg') }}" alt="" onclick="couponList()">
                                <input type="hidden" name="user_id" value="" class="get_customer_id">
                                <input type="hidden" name="amount" value="" class="coupan_amount_min">
                                <input class="input_code border-0 px-2 text-dark bg-transparent outline-0 w-100" type="text" name="coupon_code" placeholder="{{ translate('coupon_code') }}" onclick="return (($('.input_code').val() == '')?couponList():'')">
                                <button
                                    class="btn btn--primary rounded-pill text-uppercase py-1 fs-12 coupan_apply_text"
                                    type="button" id="events-coupon-code">
                                    {{ translate('apply') }}
                                </button>
                            </div>
                            <div class="invalid-feedback">{{ translate('please_provide_coupon_code') }}</div>
                        </form>
                        <span id="route-coupon-events" data-url="{{ url('api/v1/tour/tour-coupon-apply') }}"></span>
                    </span>
                </div>
                <div class="justify-content-between my-2">
                    <span>
                        <input type="radio" name="payments" value='full' checked>Full Amount&nbsp;
                        <input type="radio" name="payments" value='part'>Part Amount&nbsp;
                        <input type="radio" name="payments" value='custom'>Custom Amount&nbsp;
                        <input type="text" class="form-control w-50 amount_payment_display d-none" oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                    </span>
                </div>
                <div class="justify-content-between mt-2">
                    <span>
                        <input type="radio" name="whatsapp_msg_send" value='0' checked>Not Send Message&nbsp;
                        <input type="radio" name="whatsapp_msg_send" value='1'>Send Message&nbsp;
                        <br>
                        <br>
                        <input type="checkbox" onclick="$('.itinerary_pdf_send_class').val($(this).is(':checked') ? 1 : 0)"> send Itinerary PDF
                    </span>
                    <h6 class="text-right">Discount Amount <span class="Coupon_apply_discount">0.00</span></h6>
                    <h5 class="text-right">Final Amount: <span id="finalAmount">0</span></h5>
                </div>
                <div class="justify-content-between mt-2">
                    <span>
                    </span>
                </div>
                <button onclick="Pay_nowUse()" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="coupon-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Coupons</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body row g-3" id="modal-body">
            </div>
        </div>
    </div>
</div>

@endsection

@push('script')
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>
<script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>
<script src="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/js/intlTelInput.js') }}"></script>
<script src="{{ theme_asset(path: 'public/assets/front-end/js/country-picker-init.js') }}"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta3/js/bootstrap-select.min.js"></script>
<script>
    const TourArrayBooking = [];
    let ex_transport_priceArray = [];

    function pay_summary() {
        let summaryHtml = "";
        let grandTotal = 0;

        TourArrayBooking.forEach((item, i) => {
            summaryHtml += `
            <tr>
                <td>${i + 1}</td>
                <td>${item.title}</td>
                <td>${item.qty}</td>
                <td>${(isNaN(parseFloat(item.price)) ? 0 : parseFloat(item.price)).toFixed(2)}</td>
                <td>${item.gst}%</td>
                <td>${parseFloat(item.tax_price || 0).toFixed(2)}</td>
                <td>${parseFloat(item.total_price || 0).toFixed(2)}</td>
            </tr>
        `;

            grandTotal += parseFloat(item.total_price || 0);
        });
        $("#paymentSummaryTable tbody").html(summaryHtml);
        $("#finalAmount").text(Number(grandTotal || 0).toFixed(2));
        $('.coupan_amount_min').val(Number(grandTotal || 0));
        $('.booking_total_amount').val(Number(grandTotal || 0).toFixed(2));

        // show modal
        $("#paymentSummaryModal").modal("show");
    }
</script>
<script>
    $(document).ready(function() {
        $('input[name="payments"]').on('click', function() {
            let value = $(this).val();
            $('.part_payment_class').val(value);
            if (value === 'custom') {
                $('.amount_payment_display').removeClass('d-none').val('');
                $('.amount_payment_display').addClass('d-inline');
            } else {
                $('.amount_payment_display').addClass('d-none').val('');
                $('.amount_payment_display').removeClass('d-inline');
                $('.amount_payment_class').val(''); // clear custom value
            }
        });
        $('input[name="whatsapp_msg_send"]').on('click', function() {
            let value = $(this).val();
            $('.whatsapp_msg1_send').val(value);
        });

        $('.amount_payment_display').on('keyup', function() {
            let bookingAmount = parseFloat($('.booking_total_amount').val()) || 0;
            let enteredAmount = parseFloat($(this).val()) || 0;

            if (enteredAmount > bookingAmount) {
                $(this).val(bookingAmount); // limit to max
                $('.amount_payment_class').val(bookingAmount);
            } else {
                $('.amount_payment_class').val(enteredAmount);
            }
        });
    });
</script>
<script>
    $('.selectpicker').on('changed.bs.select', function(e, clickedIndex, isSelected, previousValue) {
        TourArrayBooking.splice(0, TourArrayBooking.length);
        let selectedValue = $(this).val();
        $('.row-new-appends-caplist').html('');
        $('.row-new-appends').html('');
        $('.row-new-address-date').html('');
        $('.itinerary_bodys').html('');
        $('.inclusion_bodys').html('');
        $('.exclusion_bodys').html('');
        $(".is_person_use_tour").val('');
        $(".use_date_tour").val('');
        $.ajax({
            url: "{{ route('admin.tour-lead.get-tour-info-div') }}",
            type: "post",
            data: {
                tour_id: selectedValue,
                _token: '{{ csrf_token() }}'
            },
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                let result = response.info;
                $('.row-new-appends-caplist').html(result.html_cab_list_price);
                $('.row-new-appends').html(result.package_html_show);
                $('.row-new-address-date').html(result.htmldateTime);
                $('.itinerary_bodys').html(result.itinerary);
                $('.inclusion_bodys').html(result.inclusion);
                $('.exclusion_bodys').html(result.exclusion);
                $(".is_person_use_tour").val(response.data.is_person_use);
                $(".use_date_tour").val(response.data.use_date);
                ex_transport_priceArray = JSON.parse(response.data.ex_transport_price);

                window.$timepicker = $('.pickupopen_time').timepicker({
                    uiLibrary: 'bootstrap4',
                    format: 'hh:MM TT',
                    modal: true,
                    footer: true
                });


                if (result.use_date != 1) {
                    datePicker();
                } else if ($('.hasDatepicker').val() == '' && result.use_date == 1) {
                    datePicker();
                }
                if (result.use_date == 3 && result.is_person_use == 0) {
                    calculateDistance()
                }

                getlocations()

            },
            error: function(xhr) {
                console.error(xhr.responseText);
            }
        });
    });

    function datePicker() {
        var today = new Date();
        var tomorrow = new Date(today);
        tomorrow.setDate(today.getDate());
        $('.hasDatepicker').datepicker({
            uiLibrary: 'bootstrap4',
            format: 'yyyy-mm-dd',
            modal: true,
            footer: true,
            minDate: tomorrow,
            todayHighlight: true
        });
    }

    $('#person-number').blur(function(e) {
        e.preventDefault();
        var code1 = $('.country-picker-phone-number').val();
        var mobile = $(this).val();
        if (mobile.length <= 9) {
            toastr.error("Invalid phone number.");
            $('.one-time-otp-sends').prop('disabled', true);
            return;
        } else {
            $('.one-time-otp-sends').prop('disabled', false);
        }
        $.ajax({
            type: "get",
            url: "{{ url('account-counselling-order-user-name') }}" + "/" + code1,
            success: function(response) {
                if (response.status == 200) {
                    var name = response.user.f_name + ' ' + response.user.l_name;
                    $('#person-name').val(name);
                    $('.get_customer_id').val(response.user.id);
                    $('#person-name').prop('readonly', true);
                    $('#phone-number-valid').val(1);
                    toastr.success('Already Exists User');
                } else {
                    $('#person-name').val('');
                    $('.get_customer_id').val(0);
                    $('#person-name').prop('readonly', false);
                    $('#phone-number-valid').val(0);
                    toastr.success('Enter User Name');
                }
            }
        });
    });


    //per head 



    function updateCabTotal(index, input) {
        let qty = parseInt(input.value) || 0;
        let price = parseFloat($(input).data("price")) || 0;
        let min = parseInt($(input).attr("min")) || 1;
        let max = parseInt($(input).attr("max")) || 9999;
        $('.package_per_head_max').attr('max', qty);
        if (qty < min) qty = min;
        if (qty > max) qty = max;
        $(input).val(qty);
        $(".total_cab_and_perhead_price").text(0);
        $(".cab_qty_input").val(0);
        let existingIndex = TourArrayBooking.findIndex(item => item.type === 'per_head');
        let TourGst = Number("{{ \App\Models\ServiceTax::find(1)['tour_tax'] ?? 1 }}");
        let total = price * qty;
        let gstAmount = (total * TourGst) / 100;
        let grandTotal = total + gstAmount;
        let bookingData = {
            type: "per_head",
            qty: qty,
            id: parseInt($(input).data("id")),
            price: total,
            price2: total,
            man_price: (total / qty),
            pprice: grandTotal,
            gst: TourGst,
            tax_price: gstAmount,
            total_price: grandTotal,
            title: "Per Head"
        };
        if (existingIndex > -1) {
            TourArrayBooking[existingIndex] = bookingData;
        } else {
            TourArrayBooking.push(bookingData);
        }
        $(".total_cab_and_perhead_price" + index).text(total);
        $(".cab_qty_input" + index).val(qty);
        //satish
    }

    function updateCab_Total(index, input) {
        let qty = parseInt(input.value) || 0;
        let price = parseFloat($(input).data("price")) || 0;
        let min = parseInt($(input).attr("min")) || 1;
        let max = parseInt($(input).attr("max")) || 9999;

        if ($(".use_date_tour").val() == 0) {
            let seats = parseInt($(input).data("seats")) || 1;
            $('.package_per_head_max').attr('max', qty * seats);
        } else {
            $('.package_per_head_max').attr('max', qty);
        }
        if ($(".use_date_tour").val() == 1 || $(".use_date_tour").val() == 4) {
            price += parseFloat($(input).data("packageincl")) || 0;
        }

        if (qty < min) qty = min;
        if (qty > max) qty = max;
        $(input).val(qty);
        $(".total_cab_and_perhead_price").text(0);
        $(".cab_qty_input").val(0);
        let existingIndex = TourArrayBooking.findIndex(item => item.type === 'cab');
        let TourGst = Number("{{ \App\Models\ServiceTax::find(1)['tour_tax'] ?? 1 }}");
        let total = 0;
        if ($(".use_date_tour").val() == 2 || $(".use_date_tour").val() == 3) {
            let includepp = parseFloat($(input).data("packageincl")) || 0
            total = price + (includepp * qty);
        } else {
            total = price * qty;
        }
        let gstAmount = (total * TourGst) / 100;
        let grandTotal = total + gstAmount;
        let bookingData = {
            type: "cab",
            qty: qty,
            id: parseInt($(input).data("id")),
            price: total,
            price2: total,
            man_price: (total / qty),
            pprice: grandTotal,
            gst: TourGst,
            tax_price: gstAmount,
            total_price: grandTotal,
            title: "Cab"
        };
        if (existingIndex > -1) {
            TourArrayBooking[existingIndex] = bookingData;
        } else {
            TourArrayBooking.push(bookingData);
        }
        $(".total_cab_and_perhead_price" + index).text(total);
        $(".cab_qty_input" + index).val(qty);
        if ([1, 2, 3, 4, 5].includes(parseInt($(".use_date_tour").val()))) {
            $('.package_per_head_max').trigger('change');
        }
        driverandCabExCharge();
    }

    let roomBooking = [];

    function updatepackageTotal(index, input) {
        let qty = parseInt(input.value) || 0;
        let price = parseFloat($(input).data("price")) || 0;
        let type = $(input).data("type");
        let seats = parseInt($(input).data("seats")) || 1;

        let min = parseInt($(input).attr("min")) || 0;
        let max = parseInt($(input).attr("max")) || 9999;

        // enforce min/max
        if (qty < min) qty = min;
        if (qty > max) qty = max;
        $(input).val(qty);

        let total = 0;
        if (type == "hotel") {
            let existingIndex = roomBooking.findIndex(item => item.type === type);
            let validRooms = qty;
            let persons = qty * seats;
            let totalPersons = roomBooking.reduce((sum, item, idx) => {
                if (idx !== existingIndex) {
                    return sum + item.persons;
                }
                return sum;
            }, 0);
            let totalPersonsqty = roomBooking.reduce((sum, item, idx) => {
                return sum + item.qty;
            }, 0);

            totalPersons += persons;
            if (totalPersons > max) {
                $(input).val(totalPersonsqty);
                toastr.error("You cannot select more than " + max + " persons.");
                return;
            }
            let bookingData = {
                type: type,
                qty: validRooms,
                persons: persons
            };

            if (existingIndex > -1) {
                roomBooking[existingIndex] = bookingData;
            } else {
                roomBooking.push(bookingData);
            }

            total = qty * price;
            $(`.person_total_amounts_${type}${index}`).text(total);
        } else {
            total = price * qty;
            $(`.person_total_amounts_${type}${index}`).text(total);
        }

        let existingIndex = TourArrayBooking.findIndex(item => item.title == $(input).data('hotel_type'));
        let TourGst = Number("{{ \App\Models\ServiceTax::find(1)['tour_tax'] ?? 1 }}");
        let gstAmount = (total * TourGst) / 100;
        let grandTotal = total + gstAmount;
        let bookingData = {
            type: "other",
            qty: qty,
            id: parseInt($(input).data("id")),
            price: total,
            price2: total,
            man_price: (total / qty),
            pprice: grandTotal,
            gst: TourGst,
            tax_price: gstAmount,
            total_price: grandTotal,
            title: $(input).data('hotel_type')
        };
        if (existingIndex > -1) {
            TourArrayBooking[existingIndex] = bookingData;
        } else {
            TourArrayBooking.push(bookingData);
        }
    }
</script>
<script>
    function getlocations() {
        let use_dates = $(".use_date_tour").val();
        let pickup_lat = $("#cities_lat_min").val();
        let pickup_long = $("#cities_long_min").val();
        if (use_dates == 0) {
            const inputElement = document.querySelector(".getAddress_google");
            const autocomplete = new google.maps.places.Autocomplete(inputElement, {
                componentRestrictions: {
                    country: "IN"
                }
            });

            const userLat = parseFloat(pickup_lat);
            const userLng = parseFloat(pickup_long);
            const maxDistance = 20000; // 20 km in meters

            const originalPlaceholder = inputElement.placeholder;

            // Listen for input changes (improved)
            $(".getAddress_google").on('input', function() {
                if ($(this).val().length < 2) {
                    // clearFields();
                }
            });


            autocomplete.addListener("place_changed", function() {
                const place = autocomplete.getPlace();

                if (!place.geometry) {
                    clearFields("Address Not Found");
                    return;
                }

                const lat = place.geometry.location.lat();
                const lng = place.geometry.location.lng();
                const distance = getDistanceFromLatLonInMeters(userLat, userLng, lat, lng);

                if (isNaN(distance)) { // Check for invalid coordinates
                    clearFields("Invalid Coordinates");
                    return;
                }
                if (distance > maxDistance) {
                    // Revert to the original placeholder and clear the fields
                    inputElement.placeholder = originalPlaceholder; // Restore placeholder
                    clearFields("Address beyond " + (maxDistance / 1000) + " km radius"); // Clear and provide a reason
                    $(".address_error_message").text(`{{ translate("Pickup will be done only from Hotels, Restaurants, Railway stations, Bus stations within The City")}}.`).fadeIn(400).delay(3000).fadeOut(4000);
                    inputElement.value = ""; // Clear the input field as well
                } else {
                    $(".address_error_message").text('');
                    $(".pickup_address").val(place.formatted_address); // No need to add "(Available)"
                    $(".pickup_lat").val(lat);
                    $(".pickup_long").val(lng);
                    inputElement.value = place.formatted_address; // Set the input field value
                    inputElement.placeholder = originalPlaceholder; // Restore placeholder
                    initMap();

                }
            });

            // ... (getDistanceFromLatLonInMeters and degToRad functions remain the same)

            function clearFields(message = '') {
                $(".pickup_address").val(message);
                $(".pickup_lat").val('');
                $(".pickup_long").val('');
                //  Don't clear the input field immediately on short input, let autocomplete suggest
                if (message !== "Address Not Found" && message !== "Invalid Coordinates") {
                    $(".getAddress_google").val(""); // Clear only if an error message is not being displayed
                }
            }

            function getDistanceFromLatLonInMeters(lat1, lon1, lat2, lon2) {
                const R = 6371000; // Earth's radius in meters
                const dLat = degToRad(lat2 - lat1);
                const dLon = degToRad(lon2 - lon1);
                const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                    Math.cos(degToRad(lat1)) * Math.cos(degToRad(lat2)) *
                    Math.sin(dLon / 2) * Math.sin(dLon / 2);
                const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                return R * c;
            }

            function degToRad(deg) {
                return deg * (Math.PI / 180);
            }
        } else if (use_dates == 2 || use_dates == 3 || use_dates == 4) {

            $(document).ready(function() {
                const inputElement = $('input[type="text"].getAddress_google')[0];

                const centerLatLng = {
                    lat: pickup_lat,
                    lng: pickup_long
                }; // Example: New Delhi, India

                const autocomplete = new google.maps.places.Autocomplete(inputElement, {
                    types: ['establishment'],
                    // componentRestrictions: { country: 'IN' },
                });

                // Set bounds with a 200km radius
                const circle = new google.maps.Circle({
                    center: centerLatLng,
                    radius: 200000, // 200 km in meters
                });

                autocomplete.setBounds(circle.getBounds());

                $(".getAddress_google").on('input', function() {
                    if ($(this).val().length < 2) {
                        $('.pickup_address').val('');
                        $('.pickup_lat').val('');
                        $('.pickup_long').val('');
                    }
                });

                autocomplete.addListener('place_changed', function() {
                    const place = autocomplete.getPlace();
                    if (!place.geometry) {
                        $('.getAddress_google').val('');
                        return;
                    }

                    const lat = place.geometry.location.lat();
                    const lng = place.geometry.location.lng();

                    const distance = getDistanceFromLatLonInKm(centerLatLng.lat, centerLatLng.lng, lat, lng);

                    if (distance > 200) {
                        $('.address_error_message').text('Please select a location within 200km range.').delay(2000).fadeOut(500).fadeIn(200).fadeOut(300);
                        $('.getAddress_google').val('');
                        $('.pickup_address').val('');
                        $('.pickup_lat').val('');
                        $('.pickup_long').val('');
                    } else {
                        $('.pickup_address').val($('.getAddress_google').val());
                        $('.pickup_lat').val(lat);
                        $('.pickup_long').val(lng);
                        calculateDistance();
                        driverandCabExCharge();
                        initMap();
                    }
                });

                function getDistanceFromLatLonInKm(lat1, lon1, lat2, lon2) {
                    const R = 6371; // Radius of Earth in km
                    const dLat = deg2rad(lat2 - lat1);
                    const dLon = deg2rad(lon2 - lon1);
                    const a =
                        Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                        Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) *
                        Math.sin(dLon / 2) * Math.sin(dLon / 2);
                    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                    return R * c; // Distance in km
                }

                function deg2rad(deg) {
                    return deg * (Math.PI / 180);
                }
            });
        } else {

        }

        function initMap() {
            var location = {
                lat: parseFloat(pickup_lat),
                lng: parseFloat(pickup_long)
            };
            var map = new google.maps.Map(document.getElementById("map"), {
                zoom: 8,
                center: location,
            });
            var marker = new google.maps.Marker({
                position: location,
                map: map,
            });
        }
        initMap();

    }

    function driverandCabExCharge() {
        let index_remmm_route = TourArrayBooking.findIndex(item => item.type === "cab");
        if ($(".is_person_use_tour").val() == 0 && $(".use_date_tour").val() == 3 && (TourArrayBooking[index_remmm_route]['id'] ?? '')) {
            $.ajax({
                url: "{{ url('api/v1/tour/tour-get-distance') }}",
                type: "post",
                beforeSend: function() {
                    $('#loading').removeClass('d--none');
                    $('#loading').css('index', 1000);
                },
                data: {
                    _token: $('meta[name="_token"]').attr('content'),
                    tour_id: $('.tour_ids').val(),
                    cab_id: TourArrayBooking[index_remmm_route]['id'],
                    lat: $('.pickup_lat').val(),
                    long: $('.pickup_long').val(),
                    route_way: $('.out_side_div:checked').val()
                },
                success: function(response) {
                    $('#loading').addClass('d--none');
                    let existingItemRoute = TourArrayBooking.findIndex(item => item.type === 'route');
                    let existingRoute = TourArrayBooking.findIndex(item => item.type === "ex_distance");
                    if (existingRoute !== -1) {
                        TourArrayBooking[existingRoute].ExChargeAmount = response.ExChargeAmount;
                        if (TourArrayBooking[existingRoute].price2 > 0) {
                            TourArrayBooking[existingRoute].price = (TourArrayBooking[existingRoute].price2 || 0) + response.ExChargeAmount;
                            TourArrayBooking[existingRoute].total_price = (TourArrayBooking[existingRoute].price || 0) + (TourArrayBooking[existingRoute].tax_price || 0);
                        }
                    }
                    if (TourArrayBooking[existingRoute] && TourArrayBooking[existingRoute].price2 !== undefined) {
                        $('.getAddress_google').data('ex-charge-driver', TourArrayBooking[existingRoute].price2);
                    } else {
                        $('.getAddress_google').data('ex-charge-driver', 0);
                    }
                }
            });
        }
    }

    function Pay_nowUse() {
        const bottomTypes = ["ex_distance", "transport", "route"];

        TourArrayBooking.sort((a, b) => {
            if (bottomTypes.includes(a.type) && !bottomTypes.includes(b.type)) {
                return 1;
            }
            if (!bottomTypes.includes(a.type) && bottomTypes.includes(b.type)) {
                return -1;
            }
            return 0;
        });
        $('.booking_array_package').val(JSON.stringify(TourArrayBooking));
        $("#services_form").submit();
    }


    function calculateDistance() {
        if ($(".is_person_use_tour").val() == 1) {
            let PerHeads = TourArrayBooking.find(item => item.type === 'per_head');
            if (PerHeads) {} else {
                TourArrayBooking.push({
                    type: 'per_head',
                    id: 0,
                    qty: 0,
                    price: 0,
                    price2: 0,
                    hotal_remaining: 0,
                });
            }
        }

        const lat1 = parseFloat($("#cities_lat_min").val());
        const lng1 = parseFloat($("#cities_long_min").val());

        const lat2 = parseFloat($('.pickup_lat').val());
        const lng2 = parseFloat($('.pickup_long').val());

        if (lat2 && lng2) {
            const R = 6371;
            const dLat = (lat2 - lat1) * (Math.PI / 180);
            const dLng = (lng2 - lng1) * (Math.PI / 180);

            const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(lat1 * (Math.PI / 180)) * Math.cos(lat2 * (Math.PI / 180)) *
                Math.sin(dLng / 2) * Math.sin(dLng / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            const distance = (Math.ceil((R * c) * 100) / 100) //Math.ceil(R * c);

            let way_type = $('.out_side_div:checked').val();
            const freeDistance = 20; // Free distance in km
            const perKmCharge = parseFloat($('.out_side_div:checked').data('ex_distance')) || 0;
            let ex_distance = 0;
            let additionalCharge = 0;

            let existingCab = TourArrayBooking.find(item => item.type === 'cab');
            if (distance > freeDistance) {
                ex_distance = distance - freeDistance;
                additionalCharge = ex_distance * perKmCharge;
            } else {
                ex_distance = distance;
            }
            if (existingCab && $(".use_date_tour").val() == 0) {
                additionalCharge = parseFloat(existingCab['qty']) * parseFloat(additionalCharge);
            }

            if (way_type === 'two_way') {
                additionalCharge *= 2;
                ex_distance *= 2;
            }
            let gstexdistance = ((additionalCharge * Number("{{ \App\Models\ServiceTax::find(1)['tour_tax'] ?? 1 }}")) / 100);
            let totalWithGST = additionalCharge + gstexdistance;

            let existingItem = TourArrayBooking.find(item => item.type === 'ex_distance');


            // var getexCharges = $('.getAddress_google').data('ex-charge-driver');
            // if (getexCharges > 0) {
            //     additionalCharge += getexCharges;
            // }


            if (existingItem) {
                existingItem.qty = (Math.ceil(ex_distance * 100) / 100) || 0;
                existingItem.price = (Math.ceil(parseFloat(additionalCharge) * 100) / 100) || 0;
                existingItem.price2 = (Math.ceil(parseFloat(additionalCharge) * 100) / 100) || 0;
                existingItem.tax_price = (Math.ceil(parseFloat(gstexdistance) * 100) / 100) || 0;
                existingItem.total_price = (Math.ceil(parseFloat(totalWithGST) * 100) / 100) || 0;
            } else {
                TourArrayBooking.push({
                    type: 'ex_distance',
                    id: '0',
                    qty: (Math.ceil(ex_distance * 100) / 100) || 0,
                    price: (Math.ceil(parseFloat(additionalCharge) * 100) / 100) || 0,
                    price2: (Math.ceil(parseFloat(additionalCharge) * 100) / 100) || 0,
                    gst: Number("{{ \App\Models\ServiceTax::find(1)['tour_tax'] ?? 1 }}"),
                    title: "Ex Distance",
                    tax_price: gstexdistance,
                    total_price: (Math.ceil(parseFloat(totalWithGST) * 100) / 100) || 0,
                });
            }

            let existingItemRoute = TourArrayBooking.find(item => item.type === 'route');

            let types = $('.extracharges-transport:checked').data('type')
            if (types && $(".is_person_use_tour").val() == 1) {
                way_type = types;
            }
            if (existingItemRoute) {
                existingItemRoute.price = way_type ?? "two_way";
                existingItemRoute.price2 = way_type ?? "two_way";
                existingItemRoute.title = "Route: " + (way_type ?? "two_way");
            } else {
                TourArrayBooking.push({
                    type: 'route',
                    id: 0,
                    qty: 0,
                    title: "Route: " + (way_type ?? "two_way"),
                    price: way_type ?? "two_way",
                    price2: way_type ?? "two_way",
                    gst: 0,
                    tax_price: 0,
                    total_price: 0,
                });
            }
        }
    }

    function transportOption(that = null) {
        if (that != null) {
            let id = $(that).data('id');
            let isChecked = $(`.${id}`).is(':checked');
            $('.extracharges-transport').prop('checked', false);
            if (isChecked) {
                $(`.${id}`).prop('checked', true);
            } else {
                $(`.${id}`).prop('checked', false);
            }
        }
        let PerHeadsUsers = TourArrayBooking.find(item => item.type === 'per_head');
        let gstgroupQty = ex_transport_priceArray;
        let matchedGroup = gstgroupQty.find(item => parseInt(PerHeadsUsers.qty || 0) >= Number(item.min) && parseInt(PerHeadsUsers.qty || 0) <= Number(item.max));
        if (!matchedGroup) {
            gstgroupQty.sort((a, b) => {
                let aDiff = Math.min(Math.abs(PerHeadsUsers.qty - a.min), Math.abs(PerHeadsUsers.qty - a.max));
                let bDiff = Math.min(Math.abs(PerHeadsUsers.qty - b.min), Math.abs(PerHeadsUsers.qty - b.max));
                return aDiff - bDiff;
            });
            matchedGroup = gstgroupQty;
        }
        let types = $('.extracharges-transport:checked').data('type1')

        let pricetrans = types && matchedGroup[types] ? matchedGroup[types] : 0;
        let gstexdistance = 0;
        let totalWithGST = Number(pricetrans);
        if (Number(pricetrans) > 0) {
            gstexdistance = ((parseFloat(pricetrans) * Number("{{ \App\Models\ServiceTax::find(1)['tour_tax'] ?? 1 }}")) / 100);
            totalWithGST = parseFloat(pricetrans) + parseFloat(gstexdistance);
        }
        let transItem = TourArrayBooking.find(item => item.type === 'transport');
        if (transItem) {
            transItem.price = (pricetrans);
            transItem.price2 = (pricetrans);
            transItem.total_price = totalWithGST,
                transItem.title = `Ex Transport`;
            transItem.qty = PerHeadsUsers.qty;
            transItem.gst = Number("{{ \App\Models\ServiceTax::find(1)['tour_tax'] ?? 1 }}");
            transItem.tax_price = gstexdistance;
        } else {
            TourArrayBooking.push({
                type: 'transport',
                id: 0,
                qty: PerHeadsUsers.qty,
                price: (pricetrans),
                price2: (pricetrans),
                gst: Number("{{ \App\Models\ServiceTax::find(1)['tour_tax'] ?? 1 }}"),
                tax_price: gstexdistance,
                total_price: totalWithGST,
                title: `Ex Transport`,
            });
        }

        if ((pricetrans) > 0) {
            $(".extransportPrice").html(`{{ translate('Total Additional Transportation Amount')}} : ${pricetrans.toLocaleString("en-US", { style: "currency", currency: "{{ getCurrencyCode() }}"})}`);
        } else {
            $(".extransportPrice").html('');
        }
    }

    function couponList() {
        let expireDate = "";
        let formattedDate = "";
        let body = "";
        $.ajax({
            type: "post",
            data: {
                _token: "{{ csrf_token() }}",
                type: "tour",
                user_id: $(".get_customer_id").val(),
            },
            url: "{{ route('coupon.coupon-list-type') }}",
            success: function(response) {
                $('#modal-body').html('');
                if (response.status == 200) {
                    if (response.coupons.length > 0) {
                        $.each(response.coupons, function(key, value) {
                            expireDate = new Date(value.expire_date);
                            formattedDate = expireDate.toLocaleString('en-GB', {
                                day: 'numeric',
                                month: 'short',
                                year: 'numeric'
                            }).replace(" ", ", ");

                            body += `<div class="col-lg-6">
                                        <div class="ticket-box">
                                        <div class="ticket-start">
                                            <img width="30" src="{{ asset('public/assets/front-end/img/icons/dollar.png') }}" alt="">
                                            <h2 class="ticket-amount">${((value.discount_type == 'percentage')?'':'₹')}${value.discount} ${((value.discount_type == 'percentage')?'%':'')}</h2>
                                            <p>On All Tours</p>
                                        </div>
                                        <div class="ticket-border"></div>
                                        <div class="ticket-end">
                                            <button class="ticket-welcome-btn couponid click-to-copy-coupon couponid-${value.code}" data-value="${value.code}" onclick="copyToClipboard(this)">${value.code}</button>
                                            <button
                                                class="ticket-welcome-btn couponid-hide d-none couponhideid-${value.code}">Copied</button>
                                            <h6>Valid till ${formattedDate}</h6>
                                            <p class="m-0">Available from minimum purchase ₹${value.min_purchase}</p>
                                        </div>
                                        </div>
                                    </div>`;
                        });
                        $('#modal-body').append(body);
                        $('#coupon-modal').on('hidden.bs.modal', function() {
                            if ($('.modal.show').length) {
                                $('body').addClass('modal-open');
                            }
                        });
                        $('#coupon-modal').modal('show');
                    } else {
                        body = 'Coupons not available';
                        $('#modal-body').css({
                            'display': 'flex',
                            'justify-content': 'center',
                            'padding': '50px 0px',
                            'color': 'red'
                        });
                    }
                } else {
                    toastr.error('Coupon not available');
                }
            }
        });
    }

    function copyToClipboard(button) {
        const value = button.getAttribute("data-value");
        if ($('.input_code').val() == '') {
            $('.input_code').val(value);
            $('#coupon-modal').modal('hide');
        } else {
            navigator.clipboard.writeText(value)
                .then(() => {
                    toastr.success("Copied to clipboard");
                })
                .catch(err => {
                    toast.error("Failed to copy");
                });
        }
    }

    $('#events-coupon-code').on('click', function() {
        apply_coupon();
    });

    function apply_coupon() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            }
        });
        $.ajax({
            type: "POST",
            url: $('#route-coupon-events').data('url'),
            data: $('#coupon-code-events-ajax').serializeArray(),
            success: function(data) {
                let messages = data.message;
                if (data.status == 1) {
                    $(".coupan_apply_text").text("{{ translate('applied') }}");
                    $(".coupon_id").val(data.data['coupon_id']);
                    $(".coupon_amount").val(data.data['coupon_amount']);
                    $(".Coupon_apply_discount").text(`- ${Number(data.data['coupon_amount']).toLocaleString("en-US", { style: "currency", currency: "{{getCurrencyCode()}}"} )}`);
                    $(".booking_total_amount").val(data.data['final_amount']);
                    $('#finalAmount').text(`${Number(data.data['final_amount']).toLocaleString("en-US", { style: "currency", currency: "{{getCurrencyCode()}}"} )}`);
                    toastr.success(messages, {
                        CloseButton: true,
                        ProgressBar: true
                    });

                } else {
                    $(".coupan_apply_text").text("{{ translate('apply') }}");
                    $(".coupon_id").val('');
                    $(".coupon_amount").val(0);
                    $('.input_code').val('');
                    $(".Coupon_apply_discount").text('0.00');
                    $('#finalAmount').text(`${Number($('.coupan_amount_min').val()).toLocaleString("en-US", { style: "currency", currency: "{{getCurrencyCode()}}"} )}`);
                    toastr.error(messages, {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            }
        });
    }
</script>
@endpush