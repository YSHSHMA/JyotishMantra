@extends('layouts.front-end.app')
@section('title', $ChadhavaDetail['chadhava_name'])
@push('css_or_js')
    <link rel="stylesheet"
        href="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/css/intlTelInput.css') }}">
    <style>
        .vertical {
            height: 10% position: absolute border-left: 2px solid black
        }

        .pooja-calendar {
            font-size: 16px;
            margin-bottom: 5px;
            font-weight: 800;
            margin-left: 8px;
        }

        .pooja-venue {
            font-size: 16px;
            margin-bottom: 5px;
            font-weight: 800;
            margin-left: 8px;

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
                    @include('web-views.chadhava.partials.statusbar')
                </div>
            </div>
        </div>
    </div>
    <div class="container rtl mb-3" id="cart-summary">
        <h2 class="mt-4 mb-3  text-lg-left mobile-fs-20 fs-18 font-bold">
            <span class="text font-bold px-3">{{ translate('Enter_details_for_your_chadhava') }}</span>
        </h2>
        <hr class="my-2">
        <div class="row pb-3">
            <div class="col-md-7">
                <div class="login-card">
                    <div class="mx-auto __max-w-760">
                        <h2 class="mt-4 mb-3  text-lg-left mobile-fs-20 fs-18 font-bold">
                            {{ translate('Your_WhatsApp_Number') }}</h2>
                        <span>{{ translate('Your_Puja_booking_updates_like_pooja_Photos_Videos_and_other_details_will_be_sent_on_WhatsApp_on_below_number') }}</span>
                        <form class="needs-validation" id="chadhava_check" action="{{ route('chadhava.user.store') }}"
                            method="post">
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
                                                <span>{{ translate('I_have_a_different_number_for_calling') }}</span>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-12" id="newPhoneAdd" style="display:none">
                                    <div class="form-group">
                                        <label
                                            class="form-label font-semibold">{{ translate('Enter_new_your_Calling_Number') }}</label>
                                        <input class="form-control text-align-direction" type="number" maxlength="10"
                                            minlength="10" name="newPhone"
                                            placeholder="{{ translate('enter__new_phone_number') }}" autocomplete="off"
                                            inputmode="number">
                                    </div>
                                </div>
                            </div>
                            <hr class="my-2">
                            <h2 class="mt-4 mb-3  text-lg-left mobile-fs-20 fs-18 font-bold">
                                {{ translate('Name_of_member_participating_in_Chadhava') }}</h2>
                            <span>{{ translate('Panditji_will_take_these_names_along_with_gotra_during_the_chadhava') }}</span>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label font-semibold">{{ translate('Member_name') }}</label>
                                        <input class="form-control text-align-direction" type="text" name="members"
                                            placeholder="{{ translate('Member_name') }}" required>
                                    </div>
                                </div>
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
                                            placeholder="{{ translate('Gotra') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="rtl">
                                    <label class="custom-control custom-checkbox m-0 d-flex">
                                        <input type="checkbox" class="custom-control-input" id="gotraCheck">
                                        <span
                                            class="custom-control-label"><span>{{ translate('I_do_not_know_my_gotra') }}</span></span>
                                    </label>
                                </div>
                            </div>
                            <hr class="my-2">
                            <div class="row mt-2">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label font-semibold">{{ translate('Chadava_Reason') }}</label>
                                        <textarea class="form-control text-align-direction" type="text" name="reason"
                                            placeholder="{{ translate('Chadava_Reason') }}"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="web-direction">
                                <div class="mx-auto mt-4 __max-w-356">
                                    <button class="w-100 btn btn--primary" id=""
                                        type="submit">{{ translate('Proceed_to_book') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-5 pt-2">
                <div class="flash_deal_product rtl cursor-pointer mb-2 ">
                    <div class="d-flex p-3">
                        <div class="d-flex align-items-center justify-content-center p-3">
                            <div class="flash-deals-background-image image-default-bg-color">
                                    <img src="{{ dynamicStorage(path: 'storage/app/public/chadhava/thumbnail/' . $orderDetails['chadhava']['thumbnail']) }}"
                                        class="__img-125px" alt="{{ translate('chadhava') }}">
                                </div>
                        </div>
                        <div class="flash_deal_product_details pl-3 pr-3 pr-1 d-flex align-items-center">

                            <div>
                                <div>
                                    <span class="text-12 font-bold  line-clamp-2 text-ellipsis mb-0"
                                        style="color:#fe9802;">{{ strtoupper($ChadhavaDetail->pooja_heading) }}
                                    </span><br>
                                    <h1 class="flash-product-title"
                                        style="font-size:15px;font-weight: 600;line-height:20px;margin-bottom:8px;">
                                        {{ $ChadhavaDetail['chadhava_name'] }}
                                    </h1>
                                </div>

                                <div class="d-flex">
                                    <img src="{{ theme_asset(path: 'public\assets\front-end\img\track-order\temple.png') }}"
                                        alt="" style="width:24px;height:24px;">
                                    <p class="pooja-venue one-lines-only">
                                        {{ $ChadhavaDetail['chadhava']['chadhava_venue'] }}</p>
                                </div>
                                <div class="d-flex">
                                    <img src="{{ theme_asset(path: 'public\assets\front-end\img\track-order\date.png') }}"
                                        alt="" style="width:24px;height:24px;">
                                    <p class="pooja-venue one-lines-only">
                                        {{ date('d', strtotime($orderDetails->booking_date)) }},
                                        {{ translate(date('F', strtotime($orderDetails->booking_date))) }} ,
                                        {{ translate(date('l', strtotime($orderDetails->booking_date))) }}
                                    <p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
@push('script')
    </script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/js/intlTelInput.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/country-picker-init.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                $(this).css("background-color", "#00FF00");
                $("button.no-btn").css("background-color", "orange");
            });
            $("button.no-btn").click(function() {
                $('#is_prashad').val(0);
                $(".hideable-div").hide();
                $(this).css("background-color", "#FF0000");
                $("button.yes-btn").css("background-color", "orange");
            });

        });
    </script>
    <script>
        $(document).ready(function() {
            if ($('input[name="is_prashad"]:checked').val() === 'yes' && $('input[name="newnumber"]:checked')
                .length > 0) {
                $('#chadhava_check').validate({
                    rules: {
                        members: {
                            required: true
                        },
                        gotra: {
                            required: true
                        }
                    },
                    messages: {
                        members: {
                            required: "Please enter your Family Member"
                        },
                        gotra: {
                            required: "Please enter your Gotra"
                        }
                    },
                });
            }
        });
    </script>
@endpush
