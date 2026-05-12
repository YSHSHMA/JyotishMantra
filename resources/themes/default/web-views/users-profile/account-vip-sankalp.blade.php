@extends('layouts.front-end.app')
@section('title', translate('order_Track'))
@push('css_or_js')
    <link rel="stylesheet"
        href="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/css/intlTelInput.css') }}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endpush
@section('content')
    <div class="container pb-5 mb-2 mb-md-4 mt-3 rtl __inline-47 text-align-direction">
        <div class="row g-3">
            @include('web-views.partials._profile-aside')
            <section class="col-lg-9">
                @include('web-views.users-profile.vipanushthan-details.vip-order-partial')
                <div class="card border-0">
                    <div class="card-body">
                        <div class="d-flex flex-column gap-2 mb-3">
                            <h5 class="font-bold m-0 fs-16">Sankalp Details Update</h5>
                            <p class="m-0">Ensure your Sankalp details are accurate and up to date. Make the necessary
                                edits and confirm the updates.</p>
                        </div>

                        <div class="text-end d-none d-lg-block">
                            @if ($order['is_edited'] == '0')
                                <button class="btn btn-danger px-2 py-1" type="button" id="editButton">
                                    <i class="tio tio-edit"></i>
                                </button>
                            @else
                                <button class="btn btn-primary px-2 py-1" type="button" id="editButton">
                                    Show Details
                                </button>
                            @endif
                        </div>
                        @if ($order)
                            <form class="needs-validation" id="sankalp_check" action="{{ route('VIPsanklpUpdate', $order['order_id']) }}" method="post">
                                @csrf
                                <input type="hidden" name="orer_id" value="{{ $order['order_id'] }}">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label class="form-label font-semibold">{{ translate('phone_number') }}
                                                <small class="text-primary">(
                                                    *{{ translate('country_code_is_must_like_for_IND') }}
                                                    91)</small></label>
                                            <input class="form-control text-align-direction phone-input-with-country-picker"
                                                type="tel"
                                                value="{{ isset($order['customers']['phone']) ? $order['leads']['person_phone'] : '' }}"
                                                required readonly inputmode="numeric" maxlength="10" minlength="10">
                                            <input type="hidden" class="country-picker-phone-number w-50" name="newphone"
                                                readonly>
                                        </div>
                                    </div>

                                </div>
                                <hr class="my-2">
                                <h2 class="mt-4 mb-3  text-lg-left mobile-fs-20 fs-18 font-bold">
                                    {{ translate('Name_of_member_participating_in_VIP_Pooja') }}</h2>
                                <span>{{ translate('Panditji_will_take_these_names_along_with_gotra_during_the_VIP_puja') }}</span>
                               
                                <div class="row">
                                    @php
                                        $members = json_decode($order['members'], true);
                                        $membersList = is_array($members) ? implode(', ', $members) : '';
                                    @endphp
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label class="form-label font-semibold">Member Name</label>
                                            <input class="form-control text-align-direction" type="text" name="members[]"
                                                value="{{ $membersList }}" placeholder="Member Name"
                                                {{ $order['is_edited'] == '1' ? 'readonly' : '' }} required>
                                        </div>
                                    </div>
                                </div>
                                <hr class="my-2">
                                <h2 class="mt-4 mb-3  text-lg-left mobile-fs-20 fs-18 font-bold">
                                    {{ translate('Fill_participans_gotra') }}</h2>
                                <span>{{ translate('Gotra_will_be_recited_during_the_VIP_puja') }} </span>
                                <div class="row mt-2">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label class="form-label font-semibold">{{ translate('gotra') }}</label>
                                            <input class="form-control" type="text" name="gotra" id="GotraId"
                                                placeholder="{{ translate('gotra') }}"
                                                value="{{ isset($order['gotra']) && $order['gotra'] != 'on' ? $order['gotra'] : 'Kashyap' }}"
                                                data-gotra="{{ isset($order['gotra']) && $order['gotra'] != 'on' ? $order['gotra'] : '' }}"
                                                {{ $order['is_edited'] == '1' ? 'readonly' : '' }}>
                                        </div>
                                    </div>
                                </div>

                                @if ($order['is_edited'] == '0')
                                    <div class="col-sm-12">
                                        <div class="rtl">
                                            <label class="custom-control custom-checkbox m-0 d-flex">
                                                <input type="checkbox" class="custom-control-input" id="gotraCheck"
                                                    {{ !isset($order['gotra']) || $order['gotra'] == 'on' ? 'checked' : '' }}>
                                                <span
                                                    class="custom-control-label">{{ translate('I_do_not_know_my_gotra') }}</span>
                                            </label>
                                        </div>
                                    </div>
                                @endif
                                <hr class="my-2">
                                <div class="row">
                                    @php
                                        $isPrashad = $order->is_prashad ?? 0; 
                                    @endphp
                                    <div class="col-md-9">
                                        <h2 class="mt-4 mb-3  text-lg-left mobile-fs-20 fs-18 font-bold">
                                            {{ translate('Do_you_want_to_get_puja_prasad') }}?</h2>
                                        <span>{{ translate('Prasad_of_workship_will_be_sent_within_8_10_days_after_completion_of_puja') }}</span>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="mb-0 d-flex">
                                            <button type="button"
                                                class="font-semibold border mt-4 text-black rounded bg-transparent yes-btn"
                                                style="width: 70px; height:30px;"
                                                {{ $order['is_edited'] == '1' ? 'disabled' : '' }}>
                                                <span>{{ translate('Yes') }}</span>
                                            </button>
                                            <button type="button"
                                                class="font-semibold border mt-4 ml-2 rounded no-btn bg-warning text-white"
                                                {{ $order['is_edited'] == '1' ? 'disabled' : '' }}
                                                style="width: 70px; height:30px;">
                                                <span>{{ translate('No') }}</span>
                                            </button>
                                        </div>
                                    </div>

                                </div>

                                <input type="hidden" id="is_prashad" name="is_prashad" value="{{ $isPrashad }}">
                                <div class="row hideable-div mt-3">
                                    <input type="hidden" name="product_id" value="853" id="product-id">
                                    <input type="hidden" name="booking_date" value="{{ $order->booking_date }}"
                                        id="booking-date">
                                    <input type="hidden" name="type" value="pooja" id="type">
                                    <input type="hidden" name="payment_type" value="P" id="payment-type">
                                    <input type="hidden" name="warehouse_id" value="61202" id="seller-id">
                                    <input type="hidden" name="seller_id" value="14" id="seller-id">
                                    <input type="hidden" name="service_id" value="{{ $order['vippoojas']['id'] }}"
                                        id="service-id">
                                    <input type="hidden" name="user_id" value="{{ $order['customers']['id'] }}"
                                        id="iser-id">



                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <input class="form-control text-align-direction" type="text"
                                                name="house_no" id="house_no"
                                                placeholder="{{ translate('house_no_building_name(Compulsory)') }}"
                                                value="{{ $order['house_no'] }}"
                                                {{ $order['is_edited'] == '1' ? 'readonly' : '' }}>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <input class="form-control text-align-direction" type="text"
                                                name="landmark" placeholder="{{ translate('landmark(Compulsory)') }}"
                                                id="landmark" value="{{ $order['landmark'] }}"
                                                {{ $order['is_edited'] == '1' ? 'readonly' : '' }}>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <input class="form-control text-align-direction areaValidation" type="text"
                                                name="area" id="google-search" placeholder="Your Address"
                                                value="{{ $order['area'] }}"
                                                {{ $order['is_edited'] == '1' ? 'readonly' : '' }}>
                                            <input class="form-control" type="hidden" name="latitude" id="latitude"
                                                placeholder="latitude" value="{{ $order['latitude'] }}"
                                                {{ $order['is_edited'] == '1' ? 'readonly' : '' }}>
                                            <input class="form-control" type="hidden" name="longitude" id="longitude"
                                                placeholder="longitude" value="{{ $order['longitude'] }}"
                                                {{ $order['is_edited'] == '1' ? 'readonly' : '' }}>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <input class="form-control" type="text" name="state" id="state"
                                                placeholder="{{ translate('State(Compulsory)') }}" inputmode="text"
                                                value="{{ $order['state'] }}"
                                                {{ $order['is_edited'] == '1' ? 'readonly' : '' }}>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <input class="form-control" type="text" name="city" id="city"
                                                placeholder="{{ translate('City(Compulsory)') }}" inputmode="text"
                                                value="{{ $order['city'] }}"
                                                {{ $order['is_edited'] == '1' ? 'readonly' : '' }}>

                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <input class="form-control" type="text" name="pincode" id="pincode"
                                                placeholder="{{ translate('Pincode(Compulsory)') }}" inputmode="number"
                                                value="{{ $order['pincode'] }}" inputmode="number"
                                                {{ $order['is_edited'] == '1' ? 'readonly' : '' }}>
                                        </div>
                                    </div>
                                </div>
                                @if ($order['is_edited'] == '0')
                                    <div class="web-direction">
                                        <div class="mx-auto mt-4 __max-w-356">
                                            <button class="w-100 btn btn--primary" id=""
                                                type="submit">{{ translate('Update_Details') }}
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </form>
                        @endif
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection
@push('script')
    <script src="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/js/intlTelInput.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/country-picker-init.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/sankalp.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places&callback=initAutocomplete"
        async></script>
    <script>
        // --search place using google map -------------------------------------------------------------

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

                    // Validate that state and city contain only letters
                    if ((field === '#state' || field === '#city') && !/^[A-Za-z\s]+$/.test(
                            value)) {
                        $(field).css('border', '1px solid red');
                        toastr.error("Please fill in all required fields for Prasad delivery.");
                        return false;
                        isValid = false;
                    }
                });
                if (!isValid) {
                    event.preventDefault();
                    toastr.error("Please fill in all required fields for Prasad delivery.");
                    return false;
                }
            }
        });
    </script>
@endpush
