@extends('layouts.front-end.app')
@section('title', $sankalpData['services']['name'])
@push('css_or_js')
 <!-- Toastr CSS -->
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
    <link rel="stylesheet"  href="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/css/intlTelInput.css') }}">
    <style>
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
                    @include('web-views.epooja.partials.statusbar')
                </div>
            </div>
        </div>
    </div>
    <div class="__inline-23">
        <div class="container rtl mb-3" id="cart-summary">
            <h2 class="mt-4 mb-3  text-lg-left mobile-fs-20 fs-18 font-bold"><span
                    class="text font-bold px-3">{{ translate('Enter_details_for_your_pooja') }}</span> </h2>
            <hr class="my-2">
            <div class="row pb-3">
                <div class="col-md-5">
                    <div class="flash_deal_product rtl cursor-pointer mb-2">
                        <div class="d-flex p-3">
                            <div class="d-flex align-items-center justify-content-center p-3">
                                <div class="flash-deals-background-image image-default-bg-color">
                                    <img src="{{ dynamicStorage(path: 'storage/app/public/pooja/thumbnail/' . $sankalpData['services']['thumbnail']) }}"
                                        class="__img-125px" alt="{{ translate('pooja') }}">
                                </div>
                            </div>
                            <div class="flash_deal_product_details pl-3 pr-3 pr-1 d-flex align-items-center">
                                <div>
                                    <div>
                                        <h1 class="flash-product-title"
                                            style="font-size:15px;font-weight: 600;line-height:20px;margin-bottom:8px;">
                                            {{ $sankalpData['services']['name'] }}
                                        </h1>
                                    </div>
                                    <div class="d-flex flex-wrap gap-8 align-items-center row-gap-0">
                                        <span class="flash-product-price fw-semibold text-dark">
                                            <img src="{{ theme_asset(path: 'public\assets\front-end\img\track-order\temple.png') }}"
                                                alt="" style="width:20px;height:20px;">
                                            {{ $sankalpData['services']['pooja_venue'] }}
                                        </span>
                                        <span class="flash-product-price fw-semibold text-dark">
                                            <img src="{{ theme_asset(path: 'public\assets\front-end\img\track-order\date.png') }}"
                                                alt="" style="width:20px;height:20px;">
                                            <strong>{{ date('d', strtotime($sankalpData->booking_date)) }},
                                                {{ translate(date('F', strtotime($sankalpData->booking_date))) }} ,
                                                {{ translate(date('l', strtotime($sankalpData->booking_date))) }}</strong>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="login-card">
                        <div class="mx-auto __max-w-760">
                            <h2 class="mt-2 mb-2  text-lg-left mobile-fs-20 fs-18 font-bold">
                                {{ translate('Your_WhatsApp_Number') }}</h2>
                            <span>{{ translate('Your_Puja_booking_updates_like_pooja_Photos_Videos_and_other_details_will_be_sent_on_WhatsApp_on_below_number') }}</span>
                            <form class="needs-validation" id="sankalp_check"
                                action="{{ route('poojaCheckout', $sankalpData['order_id']) }}" method="get">
                                @csrf
                                <input type="hidden" name="order_id" value="{{ $sankalpData['order_id'] }}">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label class="form-label font-semibold">{{ translate('phone_number') }}
                                                <small class="text-primary">( *
                                                    {{ translate('country_code_is_must_like_for_IND') }} 91
                                                    )</small></label>
                                            @php

                                                $customerPhone = isset($sankalpData['customers']['phone'])
                                                    ? $sankalpData['customers']['phone']
                                                    : '';
                                                $leadPhone = isset($sankalpData['leads']['person_phone'])
                                                    ? $sankalpData['leads']['person_phone']
                                                    : '';
                                                $phoneValue = $customerPhone . $leadPhone;
                                            @endphp
                                            <input class="form-control text-align-direction phone-input-with-country-picker"
                                                type="tel"
                                                value="{{ isset($sankalpData['customers']['phone']) ? $sankalpData['leads']['person_phone'] : '' }}"
                                                required readonly>
                                            <input type="hidden" class="country-picker-phone-number w-50"
                                                name="person_phone" readonly>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="rtl">
                                            <label class="custom-control custom-checkbox m-0 d-flex">
                                                <input type="checkbox" class="custom-control-input" name="newnumber"
                                                    id="NewNumberAdd" value="0">
                                                <span class="custom-control-label">
                                                    <span>{{ translate('I_have_a_different_number_for_calling') }}</span>
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-12" id="newPhoneAdd" style="display:none">
                                        <div class="form-group">
                                            <label
                                                class="form-label font-semibold">{{ translate('Enter_new_your_Calling_Number') }}</label>
                                            <input class="form-control text-align-direction" type="tel" name="newPhone"
                                                placeholder="{{ translate('enter__new_phone_number') }}" autocomplete="off"
                                                inputmode="number">
                                        </div>
                                    </div>
                                </div>
                                <hr class="my-2">
                                <h2 class="mt-4 mb-3  text-lg-left mobile-fs-20 fs-18 font-bold">
                                    {{ translate('Name_of_member_participating_in_Pooja') }}</h2>
                                <span>{{ translate('Panditji_will_take_these_names_along_with_gotra_during_the_puja') }}</span>
                                <div class="row">

                                    @for ($person = 1; $person <= $sankalpData['packages']['person']; $person++)
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label class="form-label font-semibold">{{ translate('Family_Member') }}
                                                </label>
                                                <input class="form-control text-align-direction" type="text"
                                                    name="members[]" value=""
                                                    placeholder="{{ translate('Family_Member') }} {{ $person }}"
                                                    required autocomplete="off" pattern="^[A-Za-z\s]+$"
                                                    title="Only letters and spaces are allowed">
                                            </div>
                                        </div>
                                    @endfor
                                </div>
                                <hr class="my-2">
                                <h2 class="mt-4 mb-3  text-lg-left mobile-fs-20 fs-18 font-bold">
                                    {{ translate('Fill_participans_gotra') }}</h2>
                                <span>{{ translate('Gotra_will_be_recited_during_the_puja') }} </span>
                                <div class="row mt-2">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label class="form-label font-semibold">{{ translate('Gotra') }}</label>
                                            <input class="form-control" type="text" name="gotra" id="GotraId"
                                                placeholder="{{ translate('Gotra') }}" pattern="^[A-Za-z\s]+$"
                                                title="Only letters and spaces are allowed" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="rtl">
                                        <label class="custom-control custom-checkbox m-0 d-flex">
                                            <input type="checkbox" class="custom-control-input" id="gotraCheck">
                                            <span
                                                class="custom-control-label">{{ translate('I_do_not_know_my_gotra') }}</span>
                                        </label>
                                    </div>
                                </div>
                                <hr class="my-2">
                                <div class="row">
                                    <div class="col-md-9">
                                        <h2 class="mt-4 mb-3  text-lg-left mobile-fs-20 fs-18 font-bold">
                                            {{ translate('Do_you_want_to_get_puja_prasad') }}?</h2>
                                        <span>{{ translate('Prasad_of_workship_will_be_sent_within_8_10_days_after_completion_of_puja') }}</span>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-0 flex">
                                            <button type="button"
                                                class="font-semibold border mt-4 text-black rounded bg-transparent yes-btn"
                                                style="width: 70px; height:30px;">
                                                <span>{{ translate('Yes') }}</span>
                                            </button>
                                            <button type="button"
                                                class="font-semibold border mt-4 ml-2 rounded no-btn bg-warning text-white"
                                                style="width: 70px; height:30px;">
                                                <span>{{ translate('No') }}</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="is_prashad" value="0" id="is_prashad">
                                <div class="row hideable-div mt-3">
                                    <input type="hidden" name="product_id" value="853" id="product-id">
                                    <input type="hidden" name="booking_date" value="{{ $sankalpData->booking_date }}"
                                        id="booking-date">
                                    <input type="hidden" name="type" value="pooja" id="type">
                                    <input type="hidden" name="payment_type" value="P" id="payment-type">
                                    <input type="hidden" name="warehouse_id" value="61202" id="seller-id">
                                    <input type="hidden" name="seller_id" value="14" id="seller-id">
                                    <input type="hidden" name="service_id" value="{{ $sankalpData['services']['id'] }}"
                                        id="service-id">
                                    <input type="hidden" name="user_id" value="{{ $sankalpData['customers']['id'] }}"
                                        id="iser-id">


                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <input class="form-control text-align-direction" type="text"
                                                name="house_no" id="house_no"
                                                placeholder="{{ translate('house_no_building_name(Compulsory)') }}"
                                                autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            {{-- <label class="form-label font-semibold">{{ translate('landmark(Compulsory)') }}</label> --}}
                                            <input class="form-control text-align-direction" type="text"
                                                name="landmark" placeholder="{{ translate('landmark(Compulsory)') }}"
                                                id="landmark" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <input class="form-control text-align-direction areaValidation" type="text"
                                            name="area" id="google-search" placeholder="Your Address"
                                            autocomplete="off">

                                        <input class="form-control" type="hidden" name="latitude" id="latitude"
                                            placeholder="latitude">
                                        <input class="form-control" type="hidden" name="longitude" id="longitude"
                                            placeholder="longitude">
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
                                        <button class="w-100 btn btn--primary" id=""
                                            type="submit">{{ translate('Proceed_to_book') }}
                                        </button>
                                    </div>
                                </div>
                            </form>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

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

            $('#sankalp_check').submit(function(event) {
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

                        if ((field === '#state' || field === '#city') && !/^[A-Za-z\s]+$/.test(
                                value)) {
                            $(field).css('border', '1px solid red');
                            toastr.error("Please fill in all required fields.");
                            return false;
                            isValid = false;
                        }
                    });
                    if (!isValid) {
                        event.preventDefault();
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
            var form = $('#sankalp_check')[0]; 
            if (form.checkValidity()) {
                $('#sankalp_check').submit();
            } else {
             
                form.reportValidity();
            }
        }
    </script>
@endpush
