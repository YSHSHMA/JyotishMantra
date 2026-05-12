@extends('layouts.front-end.app')

@section('title', translate('shipping_Address'))

@push('css_or_js')
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/bootstrap-select.min.css') }}">
    <link rel="stylesheet"
        href="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/css/intlTelInput.css') }}">

    <style>
        /* Single color box with blinking effect */
        #same-day-delivery-error {
            display: inline-block;
            background-color: #ffdddd;   /* light red background */
            color: #b30000;              /* dark red text */
            border: 1px solid #ffaaaa;
            padding: 8px 14px;
            border-radius: 6px;
            font-weight: 500;
            animation: blink 1.5s infinite ease-in-out;
        }

        /* Soft blink animation */
        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.6; }
        }
    </style>
@endpush

@section('content')
    @php($billingInputByCustomer = getWebConfig(name: 'billing_input_by_customer'))
    <div class="container py-4 rtl __inline-56 px-0 px-md-3 text-align-direction">
        <div class="row mx-max-md-0">
            <div class="col-md-12 mb-3">
                <h3 class="font-weight-bold text-center text-lg-left">{{ translate('checkout') }}</h3>
            </div>
            <section class="col-lg-8 px-max-md-0">
                <div class="checkout_details">
                    <div class="px-3 px-md-3">
                        @include('web-views.partials._checkout-steps', ['step' => 2])
                    </div>
                    @php($defaultLocation = getWebConfig(name: 'default_location'))

                    @if ($physical_product_view)
                        <input type="hidden" id="physical_product" name="physical_product"
                            value="{{ $physical_product_view ? 'yes' : 'no' }}">
                        <div class="px-3 px-md-0">
                            <h4 class="pb-2 mt-4 fs-18 text-capitalize">{{ translate('shipping_address') }}</h4>
                        </div>

                        @php($shippingAddresses = \App\Models\ShippingAddress::where(['customer_id' => auth('customer')->id(), 'is_billing' => 0, 'is_guest' => 0])->get())
                        <form method="post" class="card __card" id="address-form">
                            <div class="card-body p-0">
                                <ul class="list-group">
                                    <li class="list-group-item add-another-address">
                                        @if ($shippingAddresses->count() > 0)
                                            <div class="d-flex align-items-center justify-content-end gap-3">
                                                <div class="dropdown">
                                                    <button class="form-control dropdown-toggle text-capitalize"
                                                        type="button" id="dropdownMenuButton" data-toggle="dropdown"
                                                        aria-haspopup="true" aria-expanded="false">
                                                        {{ translate('saved_address') }}
                                                    </button>

                                                    <div class="dropdown-menu dropdown-menu-right saved-address-dropdown scroll-bar-saved-address"
                                                        aria-labelledby="dropdownMenuButton">
                                                        @foreach ($shippingAddresses as $key => $address)
                                                            <div class="dropdown-item select_shipping_address {{ $key == 0 ? 'active' : '' }}"
                                                                id="shippingAddress{{ $key }}">
                                                                <input type="hidden"
                                                                    class="selected_shippingAddress{{ $key }}"
                                                                    value="{{ $address }}">
                                                                <input type="hidden" name="shipping_method_id"
                                                                    value="{{ $address['id'] }}">
                                                                <div class="media gap-2">
                                                                    <div class="">
                                                                        <i class="tio-briefcase"></i>
                                                                    </div>
                                                                    <div class="media-body">
                                                                        <div class="mb-1 text-capitalize">
                                                                            {{ $address->address_type }}</div>
                                                                        <div
                                                                            class="text-muted fs-12 text-capitalize text-wrap">
                                                                            {{ $address->address }}</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        <div id="accordion">
                                            <div class="">
                                                <div class="mt-3">
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <div class="form-group">
                                                                <label>{{ translate('contact_person_name') }}
                                                                    <span class="text-danger">*</span>
                                                                </label>
                                                                <input type="text" class="form-control"
                                                                    name="contact_person_name"
                                                                    {{ $shippingAddresses->count() == 0 ? 'required' : '' }}
                                                                    id="name">
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="form-group">
                                                                <label>{{ translate('phone') }}
                                                                    <span class="text-danger">*</span>
                                                                </label>
                                                                <input type="text"
                                                                    class="form-control phone-input-with-country-picker"
                                                                    name="phone" id="phone"
                                                                    {{ $shippingAddresses->count() == 0 ? 'required' : '' }}>
                                                                <input type="hidden" id="shipping_phone_view"
                                                                    class="country-picker-phone-number w-50" name="phone"
                                                                    readonly>
                                                            </div>
                                                        </div>
                                                        @if (!auth('customer')->check())
                                                            <div class="col-sm-12">
                                                                <div class="form-group">
                                                                    <label for="exampleInputEmail1">
                                                                        {{ translate('email') }}
                                                                        <span class="text-danger">*</span>
                                                                    </label>
                                                                    <input type="email" class="form-control"
                                                                        name="email" id="email"
                                                                        {{ $shippingAddresses->count() == 0 ? 'required' : '' }}>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label>{{ translate('address_type') }}</label>
                                                                <select class="form-control" name="address_type"
                                                                    id="address_type">
                                                                    <option value="permanent">{{ translate('permanent') }}
                                                                    </option>
                                                                    <option value="home">{{ translate('home') }}</option>
                                                                    <option value="others">{{ translate('others') }}
                                                                    </option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <label for="name">
                                                            {{ translate('address') }}
                                                            <span class="text-danger">*</span>
                                                        </label>
                                                        <div class="input-group mb-3 p-0 col-12">
                                                            <input type="text" id="address" name="address"
                                                                class="form-control" placeholder="Search for your location"
                                                                aria-label="Place" aria-describedby="basic-addon1"
                                                                {{ $shippingAddresses->count() == 0 ? 'required' : '' }}>
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text" id="basic-addon1"><i
                                                                        class="fa fa-location-arrow"
                                                                        style="align-content: center; font-size: 22px;"></i></span>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 mb-3">
                                                            <small id="same-day-delivery-error" class=""></small>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="form-group">
                                                                <label>{{ translate('country') }}
                                                                    <span class="text-danger">*</span></label>
                                                                <input class="form-control" type="text" id="country"
                                                                    name="country"
                                                                    {{ $shippingAddresses->count() == 0 ? 'required' : '' }}>
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="form-group">
                                                                <label>{{ translate('state') }}
                                                                    <span class="text-danger">*</span></label>
                                                                <input class="form-control" type="text" id="state"
                                                                    name="state"
                                                                    {{ $shippingAddresses->count() == 0 ? 'required' : '' }}>
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="form-group">
                                                                <label>{{ translate('city') }}<span
                                                                        class="text-danger">*</span></label>
                                                                <input type="text" class="form-control" name="city"
                                                                    id="city"
                                                                    {{ $shippingAddresses->count() == 0 ? 'required' : '' }}>
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="form-group">
                                                                <label>{{ translate('zip_code') }}
                                                                    <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control" name="zip"
                                                                    id="zip"
                                                                    {{ $shippingAddresses->count() == 0 ? 'required' : '' }}>
                                                            </div>
                                                        </div>
                                                    </div>


                                                    <div class="d-flex gap-3 align-items-center">
                                                        <label class="form-check-label d-flex gap-2 align-items-center"
                                                            id="save_address_label">
                                                            <input type="hidden" name="shipping_method_id"
                                                                id="shipping_method_id" value="0">
                                                            @if (auth('customer')->check())
                                                                <input type="checkbox" name="save_address"
                                                                    id="save_address">
                                                                {{ translate('save_this_Address') }}
                                                            @endif
                                                        </label>
                                                    </div>

                                                    <input type="hidden" id="latitude" name="latitude"
                                                        class="form-control d-inline"
                                                        placeholder="{{ translate('ex') }} : -94.22213"
                                                        value="{{ $defaultLocation ? $defaultLocation['lat'] : 0 }}">
                                                    <input type="hidden" name="longitude" class="form-control"
                                                        placeholder="{{ translate('ex') }} : 103.344322" id="longitude"
                                                        value="{{ $defaultLocation ? $defaultLocation['lng'] : 0 }}">

                                                    <button type="submit" class="btn btn--primary d--none"
                                                        id="address_submit"></button>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </form>
                    @endif

                    @if ($billingInputByCustomer)
                        <div>
                            <div
                                class="billing-methods_label d-flex flex-wrap justify-content-between gap-2 mt-4 pb-3 px-3 px-md-0">
                                <h4 class="mb-0 fs-18 text-capitalize">{{ translate('billing_address') }}</h4>

                                @php($billingAddresses = \App\Models\ShippingAddress::where(['customer_id' => auth('customer')->id(), 'is_billing' => 1, 'is_guest' => '0'])->get())
                                @if ($physical_product_view)
                                    <div class="form-check d-flex gap-3 align-items-center">
                                        <input type="checkbox" id="same_as_shipping_address"
                                            name="same_as_shipping_address"
                                            class="form-check-input action-hide-billing-address"
                                            {{ $billingInputByCustomer == 0 ? '' : 'checked' }}>
                                        <label class="form-check-label" for="same_as_shipping_address">
                                            {{ translate('same_as_shipping_address') }}
                                        </label>
                                    </div>
                                @endif
                            </div>

                            @if (!$physical_product_view)
                                <div
                                    class="rounded px-3 py-3 fs-15 text-base font-weight-medium custom-light-primary-color mb-3 d-flex align-items-center gap-2">
                                    <img src="{{ theme_asset('public/assets/front-end/img/icons/info-light.svg') }}"
                                        alt="">
                                    <span>{{ translate('if_you_fill_up_this_section_this_address_will_use_in_future._if_need_to_send_to_you') }}</span>
                                </div>
                            @endif

                            <form method="post" class="card __card" id="billing-address-form">
                                <div id="hide_billing_address" class="">
                                    <ul class="list-group">

                                        <li class="list-group-item action-billing-address-hide">
                                            @if ($billingAddresses->count() > 0)
                                                <div class="d-flex align-items-center justify-content-end gap-3">

                                                    <div class="dropdown">
                                                        <button class="form-control dropdown-toggle text-capitalize"
                                                            type="button" id="dropdownMenuButton" data-toggle="dropdown"
                                                            aria-haspopup="true" aria-expanded="false">
                                                            {{ translate('saved_address') }}
                                                        </button>

                                                        <div class="dropdown-menu dropdown-menu-right saved-address-dropdown scroll-bar-saved-address"
                                                            aria-labelledby="dropdownMenuButton">
                                                            @foreach ($billingAddresses as $key => $address)
                                                                <div class="dropdown-item select_billing_address {{ $key == 0 ? 'active' : '' }}"
                                                                    id="billingAddress{{ $key }}">
                                                                    <input type="hidden"
                                                                        class="selected_billingAddress{{ $key }}"
                                                                        value="{{ $address }}">
                                                                    <input type="hidden" name="billing_method_id"
                                                                        value="{{ $address['id'] }}">
                                                                    <div class="media gap-2">
                                                                        <div class="">
                                                                            <i class="tio-briefcase"></i>
                                                                        </div>
                                                                        <div class="media-body">
                                                                            <div class="mb-1 text-capitalize">
                                                                                {{ $address->address_type }}</div>
                                                                            <div
                                                                                class="text-muted fs-12 text-capitalize text-wrap">
                                                                                {{ $address->address }}</div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            <div id="accordion">
                                                <div class="">
                                                    <div class="">
                                                        <div class="row">

                                                            <div class="col-sm-6">
                                                                <div class="form-group">
                                                                    <label>{{ translate('contact_person_name') }}<span
                                                                            class="text-danger">*</span></label>
                                                                    <input type="text" class="form-control"
                                                                        name="billing_contact_person_name"
                                                                        id="billing_contact_person_name"
                                                                        {{ $billingAddresses->count() == 0 ? 'required' : '' }}>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <div class="form-group">
                                                                    <label>{{ translate('phone') }}
                                                                        <span class="text-danger">*</span>
                                                                    </label>
                                                                    <input type="text"
                                                                        class="form-control phone-input-with-country-picker-2"
                                                                        id="billing_phone"
                                                                        {{ $billingAddresses->count() == 0 ? 'required' : '' }}>
                                                                    <input type="hidden"
                                                                        class="country-picker-phone-number-2 w-50"
                                                                        name="billing_phone" readonly>
                                                                </div>
                                                            </div>
                                                            @if (!auth('customer')->check())
                                                                <div class="col-sm-12">
                                                                    <div class="form-group">
                                                                        <label
                                                                            for="exampleInputEmail1">{{ translate('email') }}
                                                                            <span class="text-danger">*</span></label>
                                                                        <input type="text" class="form-control"
                                                                            name="billing_contact_email"
                                                                            id="billing_contact_email" id
                                                                            {{ $billingAddresses->count() == 0 ? 'required' : '' }}>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                            <label for="name">
                                                                {{ translate('Address') }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <div class="input-group mb-3 p-0 col-12">
                                                                <input type="text" id="billing_address"
                                                                    name="billing_address" class="form-control"
                                                                    placeholder="Search for your location"
                                                                    aria-label="Place" aria-describedby="basic-addon1"
                                                                    {{ $billingAddresses->count() == 0 ? 'required' : '' }}>
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text" id="basic-addon1"><i
                                                                            class="fa fa-location-arrow"
                                                                            style="align-content: center; font-size: 22px;"></i></span>
                                                                </div>
                                                            </div>
                                                            <div class="col-12">
                                                                <div class="form-group">
                                                                    <label>{{ translate('address_type') }}</label>
                                                                    <select class="form-control"
                                                                        name="billing_address_type"
                                                                        id="billing_address_type">
                                                                        <option value="permanent">
                                                                            {{ translate('permanent') }}</option>
                                                                        <option value="home">{{ translate('home') }}
                                                                        </option>
                                                                        <option value="others">{{ translate('others') }}
                                                                        </option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="form-group">
                                                                    <label>{{ translate('country') }}<span
                                                                            class="text-danger">*</span></label>
                                                                    <input type="text" class="form-control"
                                                                        id="billing_country" name="billing_country"
                                                                        {{ $billingAddresses->count() == 0 ? 'required' : '' }}>
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="form-group">
                                                                    <label>{{ translate('state') }}<span
                                                                            class="text-danger">*</span></label>
                                                                    <input type="text" class="form-control"
                                                                        id="billing_state" name="billing_state"
                                                                        {{ $billingAddresses->count() == 0 ? 'required' : '' }}>
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="form-group">
                                                                    <label
                                                                        for="exampleInputEmail1">{{ translate('city') }}<span
                                                                            class="text-danger">*</span></label>
                                                                    <input type="text" class="form-control"
                                                                        id="billing_city" name="billing_city"
                                                                        {{ $billingAddresses->count() == 0 ? 'required' : '' }}>
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="form-group">
                                                                    <label>{{ translate('zip_code') }}
                                                                        <span class="text-danger">*</span></label>
                                                                    <input type="text" class="form-control"
                                                                        id="billing_zip" name="billing_zip"
                                                                        {{ $billingAddresses->count() == 0 ? 'required' : '' }}>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <input type="hidden" name="billing_method_id"
                                                            id="billing_method_id" value="0">
                                                        @if (auth('customer')->check())
                                                            <div class=" d-flex gap-3 align-items-center">
                                                                <label
                                                                    class="form-check-label d-flex gap-2 align-items-center"
                                                                    id="save-billing-address-label">
                                                                    <input type="checkbox" name="save_address_billing"
                                                                        id="save_address_billing">
                                                                    {{ translate('save_this_Address') }}
                                                                </label>
                                                            </div>
                                                        @endif

                                                        <input type="hidden" id="billing_latitude"
                                                            name="billing_latitude" class="form-control d-inline">
                                                        <input type="hidden" name="billing_longitude"
                                                            class="form-control" id="billing_longitude">

                                                        <button type="submit" class="btn btn--primary d--none"
                                                            id="address_submit"></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            </section>
            @include('web-views.partials._order-summary')
        </div>
    </div>

    <span id="message-update-this-address" data-text="{{ translate('Update_this_Address') }}"></span>
    <span id="route-customer-choose-shipping-address-other"
        data-url="{{ route('customer.choose-shipping-address-other') }}"></span>
    <span id="default-latitude-address"
        data-value="{{ $defaultLocation ? $defaultLocation['lat'] : '-33.8688' }}"></span>
    <span id="default-longitude-address"
        data-value="{{ $defaultLocation ? $defaultLocation['lng'] : '151.2195' }}"></span>
    <span id="route-action-checkout-function" data-route="checkout-details"></span>
    <span id="system-country-restrict-status" data-value="{{ $country_restrict_status }}"></span>
@endsection

@push('script')
    <script src="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/js/intlTelInput.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/country-picker-init.js') }}"></script>
    <script>
        "use strict";
        // const deliveryRestrictedCountries = @json($countriesName);
        // function deliveryRestrictedCountriesCheck(countryOrCode, elementSelector, inputElement) {
        //     const foundIndex = deliveryRestrictedCountries.findIndex(country => country.toLowerCase() === countryOrCode.toLowerCase());
        //     if (foundIndex !== -1) {
        //         $(elementSelector).removeClass('map-area-alert-danger');
        //         $(inputElement).parent().find('.map-address-alert').removeClass('opacity-100').addClass('opacity-0')
        //     } else {
        //         $(elementSelector).addClass('map-area-alert-danger');
        //         $(inputElement).val('')
        //         $(inputElement).parent().find('.map-address-alert').removeClass('opacity-0').addClass('opacity-100')
        //     }
        // }
        initializePhoneInput(".phone-input-with-country-picker-2", ".country-picker-phone-number-2");
    </script>

    <script src="{{ theme_asset(path: 'public/assets/front-end/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/shipping.js') }}"></script>
    {{-- <script src="https://maps.googleapis.com/maps/api/js?key={{getWebConfig(name: 'map_api_key')}}&callback=mapsShopping&libraries=places&v=3.49" defer></script> --}}
    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places&callback=initAutocomplete"
        async></script>

    {{-- search place using google map for shipping and billing address --}}
    <script>
        let shippingautocomplete;
        let billingautocomplete;

        function initAutocomplete() {
            const shippinginput = document.getElementById("address");
            const billinginput = document.getElementById("billing_address");
            shippingautocomplete = new google.maps.places.Autocomplete(shippinginput);
            shippingautocomplete.addListener("place_changed", onPlaceChangeShipping)
            billingautocomplete = new google.maps.places.Autocomplete(billinginput);
            billingautocomplete.addListener("place_changed", onPlaceChangeBilling)
        }

        function onPlaceChangeShipping() {
            const shippingplace = shippingautocomplete.getPlace();
            const shippingaddressComponents = shippingplace.address_components;

            let shippinglatitude = shippingplace.geometry.location.lat();
            let shippinglongitude = shippingplace.geometry.location.lng();
            let shippingcountry = '';
            let shippingstate = '';
            let shippingcity = '';
            let shippingpostalCode = '';

            shippingaddressComponents.forEach(component => {
                const shippingcomponentType = component.types[0];

                switch (shippingcomponentType) {
                    case 'country':
                        shippingcountry = component.long_name;
                        break;
                    case 'administrative_area_level_1': // State
                        shippingstate = component.long_name;
                        break;
                    case 'locality': // City
                        shippingcity = component.long_name;
                        break;
                    case 'postal_code':
                        shippingpostalCode = component.long_name;
                        break;
                }
            });

            $('#country').val(shippingcountry);
            $('#state').val(shippingstate);
            $('#city').val(shippingcity).trigger("change");
            $('#zip').val(shippingpostalCode);
            $('#latitude').val(shippinglatitude);
            $('#longitude').val(shippinglongitude);
        }

        function onPlaceChangeBilling() {
            const billingplace = billingautocomplete.getPlace();
            const billingaddressComponents = billingplace.address_components;

            let billinglatitude = billingplace.geometry.location.lat();
            let billinglongitude = billingplace.geometry.location.lng();
            let billingcountry = '';
            let billingstate = '';
            let billingcity = '';
            let billingpostalCode = '';

            billingaddressComponents.forEach(component => {
                const billingcomponentType = component.types[0];

                switch (billingcomponentType) {
                    case 'country':
                        billingcountry = component.long_name;
                        break;
                    case 'administrative_area_level_1': // State
                        billingstate = component.long_name;
                        break;
                    case 'locality': // City
                        billingcity = component.long_name;
                        break;
                    case 'postal_code':
                        billingpostalCode = component.long_name;
                        break;
                }
            });

            $('#billing_country').val(billingcountry);
            $('#billing_state').val(billingstate);
            $('#billing_city').val(billingcity);
            $('#billing_zip').val(billingpostalCode);
            $('#billing_latitude').val(billinglatitude);
            $('#billing_longitude').val(billinglongitude);
        }
    </script>

    {{-- same day delivery check --}}
    <script>
        $(document).ready(function() {
            function checkSameDayDelivery() { 
                let locationData = JSON.parse(localStorage.getItem("location"));
                let inputCity = $("#city").val()?.trim().toLowerCase();
                let message = "";

                $("#same-day-delivery-error").text('');

                if (locationData && inputCity) {
                    let storedCity = locationData.city.trim().toLowerCase();

                    if (storedCity === inputCity) {
                        message = "Same day delivery is available.";
                    } else {
                        message = "Regular day delivery is available.";
                    }
                } else {
                    message = "Please enter a city to check delivery availability.";
                }

                $("#same-day-delivery-error").text(message);
            }

            // Run on page load
            checkSameDayDelivery();


            // Run on input change
            $("#city").on("input change", function() {
                checkSameDayDelivery();
            });
        });
    </script>

    <script>
        $(document).ready(function () {
            
        
        var country = $('#country').val();
        var phoneno = $('#phone').val();
        var hiddenPhoneno = $('#shipping_phone_view').val();
        console.log(phoneno);
        console.log(hiddenPhoneno);
        var allCountries = [ [ "Afghanistan", "af", "93" ], [ "Albania", "al", "355" ], [ "Algeria", "dz", "213" ], [ "American Samoa", "as", "1", 5, [ "684" ] ], [ "Andorra", "ad", "376" ], [ "Angola", "ao", "244" ], [ "Anguilla", "ai", "1", 6, [ "264" ] ], [ "Antigua & Barbuda", "ag", "1", 7, [ "268" ] ], [ "Argentina", "ar", "54" ], [ "Armenia", "am", "374" ], [ "Aruba", "aw", "297" ], [ "Ascension Island", "ac", "247" ], [ "Australia", "au", "61", 0 ], [ "Austria", "at", "43" ], [ "Azerbaijan", "az", "994" ], [ "Bahamas", "bs", "1", 8, [ "242" ] ], [ "Bahrain", "bh", "973" ], [ "Bangladesh", "bd", "880" ], [ "Barbados", "bb", "1", 9, [ "246" ] ], [ "Belarus", "by", "375" ], [ "Belgium", "be", "32" ], [ "Belize", "bz", "501" ], [ "Benin", "bj", "229" ], [ "Bermuda", "bm", "1", 10, [ "441" ] ], [ "Bhutan", "bt", "975" ], [ "Bolivia", "bo", "591" ], [ "Bosnia & Herzegovina", "ba", "387" ], [ "Botswana", "bw", "267" ], [ "Brazil", "br", "55" ], [ "British Indian Ocean Territory", "io", "246" ], [ "British Virgin Islands", "vg", "1", 11, [ "284" ] ], [ "Brunei", "bn", "673" ], [ "Bulgaria", "bg", "359" ], [ "Burkina Faso", "bf", "226" ], [ "Burundi", "bi", "257" ], [ "Cambodia", "kh", "855" ], [ "Cameroon", "cm", "237" ], [ "Canada", "ca", "1", 1, [ "204", "226", "236", "249", "250", "263", "289", "306", "343", "354", "365", "367", "368", "382", "387", "403", "416", "418", "428", "431", "437", "438", "450", "584", "468", "474", "506", "514", "519", "548", "579", "581", "584", "587", "604", "613", "639", "647", "672", "683", "705", "709", "742", "753", "778", "780", "782", "807", "819", "825", "867", "873", "902", "905" ] ], [ "Cape Verde", "cv", "238" ], [ "Caribbean Netherlands", "bq", "599", 1, [ "3", "4", "7" ] ], [ "Cayman Islands", "ky", "1", 12, [ "345" ] ], [ "Central African Republic", "cf", "236" ], [ "Chad", "td", "235" ], [ "Chile", "cl", "56" ], [ "China", "cn", "86" ], [ "Christmas Island", "cx", "61", 2, [ "89164" ] ], [ "Cocos (Keeling) Islands", "cc", "61", 1, [ "89162" ] ], [ "Colombia", "co", "57" ], [ "Comoros", "km", "269" ], [ "Congo - Brazzaville", "cg", "242" ], [ "Congo - Kinshasa", "cd", "243" ], [ "Cook Islands", "ck", "682" ], [ "Costa Rica", "cr", "506" ], [ "Côte d’Ivoire", "ci", "225" ], [ "Croatia", "hr", "385" ], [ "Cuba", "cu", "53" ], [ "Curaçao", "cw", "599", 0 ], [ "Cyprus", "cy", "357" ], [ "Czech Republic", "cz", "420" ], [ "Denmark", "dk", "45" ], [ "Djibouti", "dj", "253" ], [ "Dominica", "dm", "1", 13, [ "767" ] ], [ "Dominican Republic", "do", "1", 2, [ "809", "829", "849" ] ], [ "Ecuador", "ec", "593" ], [ "Egypt", "eg", "20" ], [ "El Salvador", "sv", "503" ], [ "Equatorial Guinea", "gq", "240" ], [ "Eritrea", "er", "291" ], [ "Estonia", "ee", "372" ], [ "Eswatini", "sz", "268" ], [ "Ethiopia", "et", "251" ], [ "Falkland Islands", "fk", "500" ], [ "Faroe Islands", "fo", "298" ], [ "Fiji", "fj", "679" ], [ "Finland", "fi", "358", 0 ], [ "France", "fr", "33" ], [ "French Guiana", "gf", "594" ], [ "French Polynesia", "pf", "689" ], [ "Gabon", "ga", "241" ], [ "Gambia", "gm", "220" ], [ "Georgia", "ge", "995" ], [ "Germany", "de", "49" ], [ "Ghana", "gh", "233" ], [ "Gibraltar", "gi", "350" ], [ "Greece", "gr", "30" ], [ "Greenland", "gl", "299" ], [ "Grenada", "gd", "1", 14, [ "473" ] ], [ "Guadeloupe", "gp", "590", 0 ], [ "Guam", "gu", "1", 15, [ "671" ] ], [ "Guatemala", "gt", "502" ], [ "Guernsey", "gg", "44", 1, [ "1481", "7781", "7839", "7911" ] ], [ "Guinea", "gn", "224" ], [ "Guinea-Bissau", "gw", "245" ], [ "Guyana", "gy", "592" ], [ "Haiti", "ht", "509" ], [ "Honduras", "hn", "504" ], [ "Hong Kong", "hk", "852" ], [ "Hungary", "hu", "36" ], [ "Iceland", "is", "354" ], [ "India", "in", "91" ], [ "Indonesia", "id", "62" ], [ "Iran", "ir", "98" ], [ "Iraq", "iq", "964" ], [ "Ireland", "ie", "353" ], [ "Isle of Man", "im", "44", 2, [ "1624", "74576", "7524", "7924", "7624" ] ], [ "Israel", "il", "972" ], [ "Italy", "it", "39", 0 ], [ "Jamaica", "jm", "1", 4, [ "876", "658" ] ], [ "Japan", "jp", "81" ], [ "Jersey", "je", "44", 3, [ "1534", "7509", "7700", "7797", "7829", "7937" ] ], [ "Jordan", "jo", "962" ], [ "Kazakhstan", "kz", "7", 1, [ "33", "7" ] ], [ "Kenya", "ke", "254" ], [ "Kiribati", "ki", "686" ], [ "Kosovo", "xk", "383" ], [ "Kuwait", "kw", "965" ], [ "Kyrgyzstan", "kg", "996" ], [ "Laos", "la", "856" ], [ "Latvia", "lv", "371" ], [ "Lebanon", "lb", "961" ], [ "Lesotho", "ls", "266" ], [ "Liberia", "lr", "231" ], [ "Libya", "ly", "218" ], [ "Liechtenstein", "li", "423" ], [ "Lithuania", "lt", "370" ], [ "Luxembourg", "lu", "352" ], [ "Macau", "mo", "853" ], [ "Madagascar", "mg", "261" ], [ "Malawi", "mw", "265" ], [ "Malaysia", "my", "60" ], [ "Maldives", "mv", "960" ], [ "Mali", "ml", "223" ], [ "Malta", "mt", "356" ], [ "Marshall Islands", "mh", "692" ], [ "Martinique", "mq", "596" ], [ "Mauritania", "mr", "222" ], [ "Mauritius", "mu", "230" ], [ "Mayotte", "yt", "262", 1, [ "269", "639" ] ], [ "Mexico", "mx", "52" ], [ "Micronesia", "fm", "691" ], [ "Moldova", "md", "373" ], [ "Monaco", "mc", "377" ], [ "Mongolia", "mn", "976" ], [ "Montenegro", "me", "382" ], [ "Montserrat", "ms", "1", 16, [ "664" ] ], [ "Morocco", "ma", "212", 0 ], [ "Mozambique", "mz", "258" ], [ "Myanmar (Burma)", "mm", "95" ], [ "Namibia", "na", "264" ], [ "Nauru", "nr", "674" ], [ "Nepal", "np", "977" ], [ "Netherlands", "nl", "31" ], [ "New Caledonia", "nc", "687" ], [ "New Zealand", "nz", "64" ], [ "Nicaragua", "ni", "505" ], [ "Niger", "ne", "227" ], [ "Nigeria", "ng", "234" ], [ "Niue", "nu", "683" ], [ "Norfolk Island", "nf", "672" ], [ "North Korea", "kp", "850" ], [ "North Macedonia", "mk", "389" ], [ "Northern Mariana Islands", "mp", "1", 17, [ "670" ] ], [ "Norway", "no", "47", 0 ], [ "Oman", "om", "968" ], [ "Pakistan", "pk", "92" ], [ "Palau", "pw", "680" ], [ "Palestine", "ps", "970" ], [ "Panama", "pa", "507" ], [ "Papua New Guinea", "pg", "675" ], [ "Paraguay", "py", "595" ], [ "Peru", "pe", "51" ], [ "Philippines", "ph", "63" ], [ "Poland", "pl", "48" ], [ "Portugal", "pt", "351" ], [ "Puerto Rico", "pr", "1", 3, [ "787", "939" ] ], [ "Qatar", "qa", "974" ], [ "Réunion", "re", "262", 0 ], [ "Romania", "ro", "40" ], [ "Russia", "ru", "7", 0 ], [ "Rwanda", "rw", "250" ], [ "Samoa", "ws", "685" ], [ "San Marino", "sm", "378" ], [ "São Tomé & Príncipe", "st", "239" ], [ "Saudi Arabia", "sa", "966" ], [ "Senegal", "sn", "221" ], [ "Serbia", "rs", "381" ], [ "Seychelles", "sc", "248" ], [ "Sierra Leone", "sl", "232" ], [ "Singapore", "sg", "65" ], [ "Sint Maarten", "sx", "1", 21, [ "721" ] ], [ "Slovakia", "sk", "421" ], [ "Slovenia", "si", "386" ], [ "Solomon Islands", "sb", "677" ], [ "Somalia", "so", "252" ], [ "South Africa", "za", "27" ], [ "South Korea", "kr", "82" ], [ "South Sudan", "ss", "211" ], [ "Spain", "es", "34" ], [ "Sri Lanka", "lk", "94" ], [ "St Barthélemy", "bl", "590", 1 ], [ "St Helena", "sh", "290" ], [ "St Kitts & Nevis", "kn", "1", 18, [ "869" ] ], [ "St Lucia", "lc", "1", 19, [ "758" ] ], [ "St Martin", "mf", "590", 2 ], [ "St Pierre & Miquelon", "pm", "508" ], [ "St Vincent & Grenadines", "vc", "1", 20, [ "784" ] ], [ "Sudan", "sd", "249" ], [ "Suriname", "sr", "597" ], [ "Svalbard & Jan Mayen", "sj", "47", 1, [ "79" ] ], [ "Sweden", "se", "46" ], [ "Switzerland", "ch", "41" ], [ "Syria", "sy", "963" ], [ "Taiwan", "tw", "886" ], [ "Tajikistan", "tj", "992" ], [ "Tanzania", "tz", "255" ], [ "Thailand", "th", "66" ], [ "Timor-Leste", "tl", "670" ], [ "Togo", "tg", "228" ], [ "Tokelau", "tk", "690" ], [ "Tonga", "to", "676" ], [ "Trinidad & Tobago", "tt", "1", 22, [ "868" ] ], [ "Tunisia", "tn", "216" ], [ "Turkey", "tr", "90" ], [ "Turkmenistan", "tm", "993" ], [ "Turks & Caicos Islands", "tc", "1", 23, [ "649" ] ], [ "Tuvalu", "tv", "688" ], [ "Uganda", "ug", "256" ], [ "Ukraine", "ua", "380" ], [ "United Arab Emirates", "ae", "971" ], [ "United Kingdom", "gb", "44", 0 ], [ "United States", "us", "1", 0 ], [ "Uruguay", "uy", "598" ], [ "US Virgin Islands", "vi", "1", 24, [ "340" ] ], [ "Uzbekistan", "uz", "998" ], [ "Vanuatu", "vu", "678" ], [ "Vatican City", "va", "39", 1, [ "06698" ] ], [ "Venezuela", "ve", "58" ], [ "Vietnam", "vn", "84" ], [ "Wallis & Futuna", "wf", "681" ], [ "Western Sahara", "eh", "212", 1, [ "5288", "5289" ] ], [ "Yemen", "ye", "967" ], [ "Zambia", "zm", "260" ], [ "Zimbabwe", "zw", "263" ], [ "Åland Islands", "ax", "358", 1, [ "18" ] ] ];
        
        for (var i = 0; i < allCountries.length; i++) {
            var c = allCountries[i];
            allCountries[i] = {
                name: c[0],
                iso2: c[1],
                dialCode: c[2]
            };
        }

        // Find selected country data
        var selectedCountry = allCountries.find(function (c) {
            return c.name.toLowerCase() === country.toLowerCase();
        });

        if (selectedCountry) {
            var dialCode = selectedCountry.dialCode;

            // Remove dial code from phone normally
            var cleanedPhone = phoneno.replace(new RegExp("^\\+?" + dialCode), '');
            
            // For hidden phone, remove only "91" (India) if it’s at the start
            var cleanedHiddenPhone = hiddenPhoneno.replace(/^(\+?91)/, '');

            // Update input fields
            $('#phone').val(cleanedPhone);
            $('#shipping_phone_view').val('+'+cleanedHiddenPhone);
        }
    });
    </script>
@endpush
