@extends('layouts.front-end.app')
@section('title', 'user detail')
@push('css_or_js')
    <link rel="stylesheet"
        href="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/css/intlTelInput.css') }}">
    <link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />

    <style>
        .gj-datepicker-bootstrap [role=right-icon] button .gj-icon {
            top: 14px;
            right: 5px;
        }

        .gj-timepicker-bootstrap [role=right-icon] button .gj-icon {
            top: 14px;
            right: 5px;
        }

        .city-list {
            position: absolute;
            z-index: 99;
            text-align: left;
            width: 97%;
            overflow-x: hidden;
            height: 170px;
        }
    </style>
@endpush
@section('content')
    <div class="w-full h-full sticky md:top-[68px] top-0 z-20">
        <div class="bg-bar w-full">
            <div class="d-flex overflow-x-scroll w-full scrollbar-hide max-w-screen-xl mx-auto"
                id="breadcrum-container-outer">
                <div class="d-flex flex-row items-center bg-bar h-14 px-4 md:px-0" id="breadcrum-container">
                    @include('web-views.counselling.partials.statusbar')
                </div>
            </div>
        </div>
    </div>
    <div class="container mt-3 rtl px-0 px-md-3 text-align-direction" id="cart-summary">
        <h3 class="mt-4 mb-3 text-center text-lg-left mobile-fs-20 fs-18 font-bold">
            {{-- <span aria-hidden="true"><i class="fa fa-arrow-left"></i></span>  --}}
            {{ translate('Enter_your_details_for_counselling') }}
        </h3>
        <div class="row">
            <div class="col-md-8">
                <div class="login-card">
                    <div class="mx-auto __max-w-760">
                        <h2 class="mt-4 mb-3 text-center text-lg-left mobile-fs-20 fs-18 font-bold">
                            {{ translate('Your_WhatsApp_Number') }}
                        </h2>
                        <span>{{ translate('Your_detailed_consultancy_report_will_be_delivered_as_a_PDF_on_WhatsApp_to_the_number_below') }}.</span>
                        <form action="{{ route('counselling.user.store') }}" method="post">
                            @csrf
                            <input type="hidden" name="order_id" value="{{ $orderDetail['order_id'] }}">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label font-semibold">{{ translate('phone_number') }}
                                            <small class="text-primary">( *
                                                {{ translate('country_code_is_must_like_for_IND') }} 91 )</small></label>
                                        <input class="form-control text-align-direction phone-input-with-country-picker"
                                            type="tel" value="{{ $orderDetail['customers']['phone'] }}" readonly>
                                        <input type="hidden" class="country-picker-phone-number w-50" name="person_phone"
                                            readonly>
                                    </div>
                                </div>
                            </div>
                            <hr class="my-2">
                            <h2 class="mt-4 mb-3 text-center text-lg-left mobile-fs-20 fs-18 font-bold">
                                {{ translate('Enter_Your_Details') }}
                            </h2>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="form-label font-semibold">{{ translate('name') }}</label>
                                        <input class="form-control text-align-direction" type="text" name="name"
                                            value="{{ $orderDetail['customers']['f_name'] }} {{ $orderDetail['customers']['l_name'] }}"
                                            required>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="form-label font-semibold">{{ translate('gender') }}</label>
                                        <select name="gender" id="" class="form-control">
                                            <option value="male">Male</option>
                                            <option value="female">Female</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="form-label font-semibold">{{ translate('DOB') }}</label>
                                        <input class="form-control text-align-direction" type="text" name="dob"
                                            id="datepicker" placeholder="{{ translate('Enter_your_date_of_birth') }}"
                                            autocomplete="off" required>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="form-label font-semibold">{{ translate('Birth_Time') }}</label>
                                        <input class="form-control text-align-direction" type="text" name="time"
                                            id="timepicker" placeholder="{{ translate('Enter_your_birth_time') }}"
                                            onclick="$timepicker.open()" autocomplete="off" required>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="form-label font-semibold">{{ translate('Country') }}</label>
                                        <select name="country" id="country" onchange="countrychange()"
                                            class="form-control">
                                            @foreach ($country as $countryName)
                                                <option value="{{ $countryName->name }}"
                                                    {{ $countryName->name == 'India' ? 'selected' : '' }}>
                                                    {{ $countryName->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="form-label font-semibold">{{ translate('city') }}</label>
                                        <input class="form-control" type="text" id="places" value=""
                                            name="places" required="" autocomplete="off"
                                            placeholder="{{ translate('Select_City') }}">
                                        <div class="city-list d-none">
                                            <ul id="citylist" class="list-group">
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="web-direction">
                                <div class="mx-auto mt-4 __max-w-356">
                                    <button class="w-100 btn btn--primary" id=""
                                        type="submit">{{ translate('Proceed') }}
                                    </button>
                                </div>
                            </div>
                        </form>
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
    {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script> --}}
    <script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>

    <script>
        // datepicker
        var today, datepicker;
        today = new Date(new Date().getFullYear(), new Date().getMonth(), new Date().getDate());
        $('#datepicker').datepicker({
            uiLibrary: 'bootstrap4',
            format: 'dd/mm/yyyy',
            modal: true,
            footer: true,
            maxDate: today
        });

        // time picker
        $('#timepicker').timepicker({
            uiLibrary: 'bootstrap4',
            modal: true,
            footer: true
        });

        var $timepicker = $('#timepicker').timepicker({
            uiLibrary: 'bootstrap4',
            modal: true,
            footer: true
        });
    </script>

    <script>
        // city load
        $("#places").keyup(function() {
            var length = $('#places').val().length;
            $('#citylist').html("");
            if (length > 1) {
                let countryName = $("#country").val();
                let cityName = $("#places").val();
                let city = "";
                
                var data = {
                    country: countryName,
                    name: cityName,
                }
                
                $.ajax({
                    type: "post",
                    url: "https://geo.vedicrishi.in/places/",
                    data: JSON.stringify(data),
                    dataType: "json",
                    headers: {
                        "Content-Type": 'application/json'
                    },
                    success: function(response) {
                        $('.city-list').removeClass('d-none');
                        $.each(response, function(key, value) {
                            city +=
                                `<li class="list-group-item" style="cursor: pointer;" onclick="citydata('${value.place}')">${value.place}</li>`;
                        });
                        $('#citylist').append(city);
                    }
                });
            }
        });

        // lat lon and place
        function citydata(place) {
            $('#places').val(place);
            $('#citylist').html("");
            $('.city-list').addClass('d-none');
        }

        // country change
        function countrychange() {
            $("#places").val("");
            $('#citylist').html("");
        }
    </script>
@endpush
