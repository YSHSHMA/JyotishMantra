@extends('layouts.front-end.app')

@section('title', translate('user_Detail'))

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

    <div class="container pb-5 mb-2 mb-md-4 mt-3 rtl __inline-47 text-align-direction">
        <div class="row g-3">
            @include('web-views.partials._profile-aside')
            <section class="col-lg-9">
                @include('web-views.users-profile.service-details.counselling-order-partial')
                <div class="card border-0">
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-12 mb-3">
                                <h5 class="font-bold m-0 fs-16">User Detail Update</h5>
                            </div>
                            {{-- <div class="text-end col-2">
                                <button class="btn btn-primary px-2 py-1" type="button"
                                    id="edit-button">{{ !empty($order['counselling_user'])?($order['counselling_user']['is_update'] == 0 ? 'Edit Detail' : 'Show Detail'):'Add Detail' }}</button>
                            </div> --}}
                            <div id="user-update">
                                <form action="{{route('account-counselling-order-user-update')}}" method="post">
                                    @csrf
                                    <input type="hidden" name="type" value="{{ !empty($order['counselling_user'])?'update':'add'}}">
                                    <input type="hidden" name="order_id" value="{{ $order['order_id'] }}">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label class="form-label font-semibold">{{ translate('phone_number') }}
                                                    <small class="text-primary">( *
                                                        {{ translate('country_code_is_must_like_for_IND') }} 91
                                                        )</small></label>
                                                <input
                                                    class="form-control text-align-direction phone-input-with-country-picker"
                                                    type="tel" value="{{ $order['customers']['phone'] }}" readonly>
                                                <input type="hidden" class="country-picker-phone-number w-50"
                                                    name="person_phone" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <hr class="my-2">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label class="form-label font-semibold">Name</label>
                                                <input class="form-control text-align-direction" type="text"
                                                    name="name" value="{{ !empty($order['counselling_user'])?$order['counselling_user']['name']:'' }}" required {{!empty($order['counselling_user'])?($order['counselling_user']['is_update']==1?'disabled':''):''}}>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label class="form-label font-semibold">Gender</label>
                                                <select name="gender" id="" class="form-control" {{!empty($order['counselling_user'])?($order['counselling_user']['is_update']==1?'disabled':''):''}}>
                                                    <option value="male" {{ !empty($order['counselling_user'])?($order['counselling_user']['gender'] == 'male' ? 'selected' : ''):'' }}>Male
                                                    </option>
                                                    <option value="female" {{ !empty($order['counselling_user'])?($order['counselling_user']['gender'] == 'female' ? 'selected' : ''):'' }}>
                                                        Female</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label class="form-label font-semibold">DOB</label>
                                                <input class="form-control text-align-direction" type="text"
                                                    name="dob" value="{{ !empty($order['counselling_user'])?$order['counselling_user']['dob']:'' }}" id="{{!empty($order['counselling_user'])&&$order['counselling_user']['is_update']==1?'':'datepicker'}}"
                                                    placeholder="Enter your date of birth" autocomplete="off" required {{!empty($order['counselling_user'])?($order['counselling_user']['is_update']==1?'disabled':''):''}}>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label class="form-label font-semibold">Birth Time</label>
                                                <input class="form-control text-align-direction" type="text"
                                                    name="time" value="{{ !empty($order['counselling_user'])?$order['counselling_user']['time']:'' }}" id="{{!empty($order['counselling_user'])&&$order['counselling_user']['is_update']==1?'':'timepicker'}}"
                                                    placeholder="Enter your birth time" onclick="$timepicker.open()"
                                                    autocomplete="off" required {{!empty($order['counselling_user'])?($order['counselling_user']['is_update']==1?'disabled':''):''}}>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label class="form-label font-semibold">Country</label>
                                                <select name="country" id="country" onchange="countrychange()"
                                                    class="form-control" {{!empty($order['counselling_user'])?($order['counselling_user']['is_update']==1?'disabled':''):''}}>
                                                    @foreach ($country as $countryName)
                                                        <option value="{{ $countryName->name }}"
                                                            {{ !empty($order['counselling_user'])?($countryName->name == $order['counselling_user']['country'] ? 'selected' : ''):'' }}>
                                                            {{ $countryName->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label class="form-label font-semibold">City</label>
                                                <input class="form-control" type="text" id="places"
                                                    value="{{ !empty($order['counselling_user'])?$order['counselling_user']['city']:'' }}" name="places" required=""
                                                    autocomplete="off" placeholder="Select City" {{!empty($order['counselling_user'])?($order['counselling_user']['is_update']==1?'disabled':''):''}}>
                                                <div class="city-list d-none">
                                                    <ul id="citylist" class="list-group">
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- @if (!empty($order['counselling_user'])?$order['counselling_user']['is_update'] == 0:) --}}
                                        <div class="web-direction">
                                            <div class="mx-auto mt-4 __max-w-356">
                                                <button class="w-100 btn btn--primary" id=""
                                                    type="submit" style="{{!empty($order['counselling_user'])?($order['counselling_user']['is_update'] == 0?'display: block':'display: none'):'display: block'}}">{{ translate(!empty($order['counselling_user'])?($order['counselling_user']['is_update'] == 0?'Update':''):'Add') }}
                                                </button>
                                            </div>
                                        </div>
                                    {{-- @endif --}}
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <span id="message-ratingContent" data-poor="{{ translate('poor') }}" data-average="{{ translate('average') }}"
        data-good="{{ translate('good') }}" data-good-message="{{ translate('the_delivery_service_is_good') }}"
        data-good2="{{ translate('very_Good') }}"
        data-good2-message="{{ translate('this_delivery_service_is_very_good_I_am_highly_impressed') }}"
        data-excellent="{{ translate('excellent') }}"
        data-excellent-message="{{ translate('best_delivery_service_highly_recommended') }}"></span>
@endsection
@push('script')
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/spartan-multi-image-picker.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/account-order-details.js') }}"></script>
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

    {{-- edit button toggle --}}
    <script>
        // $('#edit-button').click(function (e) { 
        //     e.preventDefault();
            
        //     $('#user-update').toggle();
        // });
    </script>
@endpush
