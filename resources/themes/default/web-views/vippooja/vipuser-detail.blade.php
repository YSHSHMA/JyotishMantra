@extends('layouts.front-end.app')
@section('title', $orderDetails['vippoojas']['name'])
@push('css_or_js')
    <link rel="stylesheet"
        href="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/css/intlTelInput.css') }}">
    <style>
        .vertical {
            height: 10% position: absolute border-left: 2px solid black
        }
       
        .button-sticky {
            border-radius: 5px 5px 0 0;
            border: 1px solid rgba(20, 85, 172, 0.05);
            box-shadow: 0 -7px 30px 0 rgba(0, 113, 220, 0.1);
            position: sticky;
            bottom: 0;
            left: 0;
            z-index: 99;
            transition: all 150ms ease-in-out;
        }
   
    </style>
    <script type="text/javascript">
        function preventBack() {
            window.history.forward();
        }
        setTimeout("preventBack()", 0);
        window.onunload = function() {
            null
        };
    </script>
@endpush
@section('content')
    <div class="w-full h-full sticky md:top-[68px] top-0 z-20">
        <div class="bg-bar w-full">
            <div class="d-flex overflow-x-scroll w-full scrollbar-hide max-w-screen-xl mx-auto"
                id="breadcrum-container-outer">
                <div class="d-flex flex-row items-center bg-bar h-14 px-4 md:px-0" id="breadcrum-container">
                    @include('web-views.vippooja.partials.statusbar')
                </div>
            </div>
        </div>
    </div>
    <div class="container mt-3  px-0 px-md-3 text-align-direction">
        <h3 class="mt-4 mb-3 text-center text-lg-left mobile-fs-20 fs-18 font-bold">
            <a href="{{ url()->previous() }}"><span aria-hidden="true"><i class="fa fa-arrow-left"></i></span>
            </a>
            <span class="text font-bold px-3">Enter details for your puja</span>
        </h3>
        <div class="row">
            <div class="col-md-7">
                <div class="login-card">
                    <div class="mx-auto __max-w-760">
                        <h2 class="mt-4 mb-3 text-center text-lg-left mobile-fs-20 fs-18 font-bold">Your WhatsApp Number
                        </h2>
                        <span>Your Puja booking updates like Puja Photos, Videos and other details will be sent on WhatsApp
                            on below number.</span>
                        <form class="needs-validation modal-form" id="vippooja_check" action="{{ route('vip.user.store') }}"
                            method="post" >
                            @csrf
                            <input type="hidden" name="order_id" value="{{ $orderDetails['order_id'] }}">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label font-semibold">{{ translate('phone_number') }}
                                            <small class="text-primary">( *
                                                {{ translate('country_code_is_must_like_for_IND') }} 91 )</small></label>
                                        @php
                                            $customerPhone = isset($orderDetails['customers']['phone'])
                                                ? $orderDetails['customers']['phone']
                                                : '';
                                            $leadPhone = isset($orderDetails['leads']['person_phone'])
                                                ? $orderDetails['leads']['person_phone']
                                                : '';
                                            $phoneValue = $customerPhone . $leadPhone;
                                        @endphp
                                        <input class="form-control text-align-direction phone-input-with-country-picker"
                                            type="tel"
                                            value="{{ isset($orderDetails['customers']['phone']) ? $orderDetails['leads']['person_phone'] : '' }}"
                                            required readonly>
                                        <input type="hidden" class="country-picker-phone-number w-50" name="person_phone"
                                            readonly>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="rtl">
                                        <label class="custom-control custom-checkbox m-0 d-flex">
                                            <input type="checkbox" class="custom-control-input" name="newnumber"
                                                id="NewNumberAdd" value="0">
                                            <span class="custom-control-label">
                                                <span>I have a different number for calling</span>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-12" id="newPhoneAdd" style="display:none">
                                    <div class="form-group">
                                        <label class="form-label font-semibold">Enter new your Calling Number</label>
                                        <input class="form-control text-align-direction" type="tel" name="newPhone"
                                            placeholder="{{ translate('enter__new_phone_number') }}" autocomplete="off"
                                            inputmode="number">
                                    </div>
                                </div>
                            </div>
                            <hr class="my-2">
                            <h2 class="mt-4 mb-3 text-center text-lg-left mobile-fs-20 fs-18 font-bold">Name of member
                                participating in Puja</h2>
                            <span>Panditji will take these names along with gotra during the puja.</span>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label font-semibold">Member Name</label>
                                        <input class="form-control text-align-direction" type="text" name="members[]"
                                            value="" placeholder="Enter Member Name" required autocomplete="off"
                                            pattern="^[A-Za-z\s]+$" title="Only letters and spaces are allowed">
                                    </div>
                                </div>
                            </div>
                            <hr class="my-2">
                            <h2 class="mt-4 mb-3 text-center text-lg-left mobile-fs-20 fs-18 font-bold">Fill participant’s
                                gotra</h2>
                            <span>Gotra will be recited during the puja.</span>
                            <div class="row mt-2">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label font-semibold">{{ translate('gotra') }}</label>
                                        <input class="form-control" type="text" name="gotra" id="GotraId"
                                            placeholder="{{ translate('gotra') }}" required
                                            pattern="^[A-Za-z\s]+$" title="Only letters and spaces are allowed">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="rtl">
                                    <label class="custom-control custom-checkbox m-0 d-flex">
                                        <input type="checkbox" class="custom-control-input" id="gotraCheck">
                                        <span class="custom-control-label"><span>I do not know my gotra</span></span>
                                    </label>
                                </div>
                            </div>
                            <hr class="my-2">
                            <div class="row">
                                <div class="col-md-10">
                                    <h2 class="mt-4 mb-3 text-center text-lg-left mobile-fs-20 fs-18 font-bold">Do you want
                                        to get puja prasad?</h2>
                                    <span>Prasad of workship will be sent within 8-10 days after completion of puja</span>
                                </div>
                                <div class="col-md-2">
                                    <div class="mb-0 flex">
                                        <button type="button"
                                            class="font-semibold border mt-4 text-black rounded bg-transparent yes-btn">
                                            <span>Yes</span>
                                        </button>
                                        <button type="button"
                                            class="font-semibold border mt-4 ml-2 rounded no-btn bg-warning text-white">
                                            <span>No</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="is_prashad" value="0" id="is_prashad">
                            <div class="row hideable-div mt-3">

                                <input type="hidden" name="product_id" value="853" id="product-id">
                                <input type="hidden" name="booking_date" value="{{ $orderDetails->booking_date }}"
                                    id="booking-date">
                                <input type="hidden" name="type" value="vip" id="type">
                                <input type="hidden" name="payment_type" value="P" id="payment-type">
                                <input type="hidden" name="warehouse_id" value="61202" id="seller-id">
                                <input type="hidden" name="seller_id" value="14" id="seller-id">
                                <input type="hidden" name="service_id" value="{{ $orderDetails['vippoojas']['id'] }}"
                                    id="service-id">
                                <input type="hidden" name="user_id" value="{{ $orderDetails['customers']['id'] }}"
                                    id="iser-id">

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <input class="form-control text-align-direction" type="text" name="house_no"
                                            value="" id="house_no"
                                            placeholder="{{ translate('house_no_building_name(Compulsory)') }}"
                                            autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        {{-- <label class="form-label font-semibold">{{ translate('landmark(Compulsory)') }}</label> --}}
                                        <input class="form-control text-align-direction" type="text" name="landmark"
                                            value="" placeholder="{{ translate('landmark(Compulsory)') }}"
                                            id="landmark" autocomplete="offf">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <input class="form-control text-align-direction areaValidation" type="text"
                                            name="area" id="google-search" value=""
                                            placeholder="{{ translate('Address(Compulsory)') }}" autocomplete="off">

                                        <input class="form-control" type="hidden" name="latitude" id="latitude"
                                            placeholder="latitude">
                                        <input class="form-control" type="hidden" name="longitude" id="longitude"
                                            placeholder="longitude">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <input class="form-control" type="text" name="state" id="state"
                                            placeholder="{{ translate('State(Compulsory)') }}" inputmode="text">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <input class="form-control" type="text" name="city" id="city"
                                            placeholder="{{ translate('City(Compulsory)') }}" inputmode="text">

                                    </div>
                                </div>
                                <div class="col-sm-6">
                                        <div class="form-group">
                                            <input class="form-control"  type="number" name="pincode" id="pincode"
                                                placeholder="{{ translate('Pincode(Compulsory)') }}" inputmode="number">
                                        </div>
                                    </div>

                            </div>
                            <div class="web-direction">
                                <div class="mx-auto mt-4 __max-w-356 d-none d-sm-block">
                                    <button class="w-100 btn btn--primary submit-btn" id=""
                                        type="submit">{{ translate('Proceed_to_book') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="flash_deal_product rtl cursor-pointer mb-2">
                    <div class="d-flex">
                        <div class="d-flex align-items-center justify-content-center p-3">
                            <div class="flash-deals-background-image image-default-bg-color">
                                <img src="{{ dynamicStorage(path: 'storage/app/public/pooja/vip/thumbnail/' . $orderDetails['vippoojas']['thumbnail']) }}"
                                    class="__img-125px" alt="Vippooja Name">

                                </div>
                        </div>
                        <div class="flash_deal_product_details pl-3 pr-3 pr-1 d-flex align-items-center">
                            <div>
                                <div>
                                    <h1 class="flash-product-title"
                                        style="font-size:15px;font-weight: 600;line-height:20px;margin-bottom:8px;">
                                        {{ $poojaDetail->vippooja_name }}
                                    </h1>
                                </div>
                                <div class="widget-meta d-flex flex-wrap gap-8 align-items-center row-gap-0">
                                    <span class="flash-product-price fw-semibold text-dark">
                                        Your Package: <strong>{{ $poojaDetail->package_name }}</strong>
                                    </span>
                                </div>
                                <div class="d-flex flex-wrap gap-8 align-items-center row-gap-0">
                                    <span class="flash-product-price fw-semibold text-dark text-success">
                                        @php
                                            $OrderPrice = \App\Models\Service_order::where(
                                                'order_id',
                                                $orderDetails['order_id'],
                                            )->first();
                                        @endphp
                                        @if ($OrderPrice)
                                            Pay Amount:<strong >{{ webCurrencyConverter(amount: $poojaDetail->final_amount - $poojaDetail->coupon_amount ?? 0) }}
                                            </strong>
                                        @endif
                                    </span>
                                    <span class="flash-product-price fw-semibold text-dark">
                                        Your Slot Book Date:
                                        <strong>{{ date('l, d M Y', strtotime($poojaDetail['booking_date'])) }}</strong>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="button-sticky bg-white d-sm-none">
        <div class="d-flex flex-column gap-1 p-2">
            <div class="d-flex gap-3 justify-content-center" role="button">
                <button class="w-100 btn btn--primary" id="" type="submit" onclick="snakalpSubmit(this)">{{ translate('Proceed_to_book') }}
                </button>
            </div>
        </div>
    </div>
@endsection
@push('script')
    </script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/js/intlTelInput.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/country-picker-init.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places&callback=initAutocomplete"
        async></script>

    <script>
        $(document).ready(function() {
            $("#NewNumberAdd").change(function() {
                if ($(this).is(":checked")) {
                    $("#newPhoneAdd").show();
                    $("#newPhoneAdd input[name='newPhone']").prop("required", true);
                } else {
                    $("#newPhoneAdd").hide();
                    $("#newPhoneAdd input[name='newPhone']").prop("required", false);
                }
            });
            // Check the Gutra 
            $(document).ready(function() {
                $("#gotraCheck").change(function() {
                    if ($(this).is(":checked")) {
                        $("#GotraId").prop("readonly", true).val("Kashyapa");
                    } else {
                        $("#GotraId").prop("readonly", false).val("");
                    }
                });
            });
            // add the condition button YES ANd NO
            $(".hideable-div").hide();
            $("button.yes-btn").click(function() {
                $('#is_prashad').val(1);
                $(".hideable-div").show();
                $(this).removeClass("bg-transparent text-black");
                $(this).addClass("bg-warning text-white");
                $("button.no-btn").removeClass("bg-warning text-white");
                $("button.no-btn").addClass("bg-transparent text-black");

            });
            $("button.no-btn").click(function() {
                $('#is_prashad').val(0);
                $(".hideable-div").hide();
                $(this).removeClass("bg-transparent text-black");
                $(this).addClass("bg-warning text-white");
                $("button.yes-btn").removeClass("bg-warning text-white");
                $("button.yes-btn").addClass("bg-transparent text-black");
            });

        });
    </script>
    <script>
        $(document).ready(function() {
            $('#vippooja_check').submit(function(event) {
                if ($('#is_prashad').val() == '1') {
                    let isValid = true;
                    let requiredFields = ['#state', '#city', '#pincode', '.areaValidation', '#house_no',
                        '#landmark'
                    ];
                    requiredFields.forEach(function(field) {
                    let value = $(field).val().trim();

                        if (value === '') {
                            $(field).css('border', '1px solid red');
                            isValid = false;
                        } else {
                            $(field).css('border', '');
                        }

                        // Validate that state and city contain only letters
                        if ((field === '#state' || field === '#city') && !/^[A-Za-z\s]+$/.test(value)) {
                            $(field).css('border', '1px solid red');
                            alert("State and City should contain only letters.");
                            return false;
                            isValid = false;
                        }
                    });
                    if (!isValid) {
                        event.preventDefault();
                        alert("Please fill in all required fields for Prasad delivery.");
                        return false;
                    }
                }
            });
        });
    </script>
    {{-- search place using google map --}}
    <script>
        let autocomplete;

        function initAutocomplete() {
            const input = document.getElementById("google-search");
            const options = {
                componentRestrictions: {
                    country: "IN"
                }
            }
            autocomplete = new google.maps.places.Autocomplete(input, options);
            autocomplete.addListener("place_changed", onPlaceChange)
        }

        function onPlaceChange() {
            const place = autocomplete.getPlace();
            const addressComponents = place.address_components;

            let latitude = place.geometry.location.lat();
            let longitude = place.geometry.location.lng();
            let address = place.formatted_address;
            let state = '';
            let city = '';
            let postalCode = '';

            addressComponents.forEach(component => {
                const componentType = component.types[0];

                switch (componentType) {
                    case 'administrative_area_level_1':
                        state = component.long_name;
                        break;
                    case 'locality':
                        city = component.long_name;
                        break;
                    case 'postal_code':
                        postalCode = component.long_name;
                        break;
                }
            });

            $('#state').val(state);
            $('#city').val(city);
            $('#pincode').val(postalCode);
            $('#latitude').val(latitude);
            $('#longitude').val(longitude);
        }
    </script>
     <script>
        document.querySelectorAll('.modal-form').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                const submitBtn = form.querySelector('.submit-btn');
                submitBtn.disabled = true;
                submitBtn.innerHTML =
                    'Please wait... <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
            });
        });
    </script>
     <script>
        $(document).ready(function() {
            const $stickyElement = $('.button-sticky');
            const $offsetElement = $('.login-card');

            $(window).on('scroll', function() {
                const elementOffset = $offsetElement.offset().top;
                const scrollTop = $(window).scrollTop();

                if (scrollTop >= elementOffset) {
                    $stickyElement.addClass('stick');
                } else {
                    $stickyElement.removeClass('stick');
                }
            });
        });
    </script>
     <script>
        function snakalpSubmit(button) {
            var form = $('#vippooja_check')[0]; 
            if (form.checkValidity()) {
                $('#vippooja_check').submit();
            } else {
             
                form.reportValidity();
            }
        }
    </script>
@endpush