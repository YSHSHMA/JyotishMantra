@extends('layouts.front-end.app')

@section('title', translate('my_Address'))

@push('css_or_js')
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/vendor/nouislider/distribute/nouislider.min.css')}}"/>
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/bootstrap-select.min.css') }}">
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/address.css') }}">
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/css/intlTelInput.css') }}">
@endpush

@section('content')
<div class="container py-4 rtl __account-address text-align-direction">

    <div class="row g-3">
        @include('web-views.partials._profile-aside')
        <section class="col-lg-9 col-md-8">

            <div class="card">
                <div class="card-body">
                    <h5 class="font-bold m-0 fs-16">{{translate('Update_Addresses')}}</h5>
                    <form action="{{route('address-update')}}" method="post">
                        @csrf
                        <div class="row pb-1">
                            <div class="col-md-6">
                                <input type="hidden" name="id" value="{{$shippingAddress->id}}">
                                <ul class="donate-now d-flex gap-2">
                                    <li class="address_type_li">
                                        <input type="radio" class="address_type" id="a25" name="addressAs" value="permanent"  {{ $shippingAddress->address_type == 'permanent' ? 'checked' : ''}} />
                                        <label for="a25" class="component">{{translate('permanent')}}</label>
                                    </li>
                                    <li class="address_type_li">
                                        <input type="radio" class="address_type" id="a50" name="addressAs" value="home" {{ $shippingAddress->address_type == 'home' ? 'checked' : ''}} />
                                        <label for="a50" class="component">{{translate('home')}}</label>
                                    </li>
                                    <li class="address_type_li">
                                        <input type="radio" class="address_type" id="a75" name="addressAs" value="office" {{ $shippingAddress->address_type == 'office' ? 'checked' : ''}}/>
                                        <label for="a75" class="component">{{translate('office')}}</label>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <input type="hidden" id="is_billing" value="{{$shippingAddress->is_billing}}">
                                <ul class="donate-now d-flex gap-2">
                                    <li class="address_type_bl">
                                        <input type="radio" class="bill_type" id="b25" name="is_billing" value="0"  {{ $shippingAddress->is_billing == '0' ? 'checked' : ''}} />
                                        <label for="b25" class="component">{{translate('shipping')}}</label>
                                    </li>
                                    <li class="address_type_bl">
                                        <input type="radio" class="bill_type" id="b50" name="is_billing" value="1" {{ $shippingAddress->is_billing == '1' ? 'checked' : ''}} />
                                        <label for="b50" class="component">{{translate('billing')}}</label>
                                    </li>

                                </ul>
                            </div>
                        </div>
                        <label for="name">
                            {{translate('address')}}
                            <span class="text-danger">*</span>
                        </label>
                        <div class="input-group mb-3 p-0 col-12">
                            <input type="text" id="search-place" name="address" value="{{$shippingAddress->address}}" class="form-control" placeholder="Search for your location" aria-label="Place" aria-describedby="basic-addon1">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-location-arrow" style="align-content: center; font-size: 22px;"></i></span>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="person_name">{{translate('contact_person_name')}}</label>
                                <input class="form-control" type="text" id="person_name"
                                    name="name"
                                    value="{{$shippingAddress->contact_person_name}}"
                                    required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="own_phone">{{translate('phone')}}</label>
                                <input class="form-control phone-input-with-country-picker" type="text" id="own_phone" value="{{$shippingAddress->phone}}" required="required">
                                <input type="hidden" class="country-picker-phone-number w-50" name="phone" value="{{ $shippingAddress->phone }}" readonly>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="city">{{translate('country')}}</label>
                                <input class="form-control" type="text" id="country" name="country" value="{{$shippingAddress->country}}" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="city">{{translate('state')}}</label>
                                <input class="form-control" type="text" id="state" name="state" value="{{$shippingAddress->state}}" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="city">{{translate('city')}}</label>

                                <input class="form-control" type="text" id="city" name="city" value="{{$shippingAddress->city}}" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="zip_code">{{translate('zip_code')}}</label>
                                <input class="form-control" type="text" id="zip" name="zip" value="{{$shippingAddress->zip}}" required>
                            </div>
                        </div>
                        <input class="form-control" type="hidden" id="latitude" name="latitude" value="{{$shippingAddress->latitude}}">
                        <input class="form-control" type="hidden" id="longitude" name="longitude" value="{{$shippingAddress->longitude}}">

                        <div class="modal-footer">
                            <a href="{{ route('account-address') }}" class="closeB btn btn-secondary fs-14 font-semi-bold py-2 px-4">{{translate('close')}}</a>
                            <button type="submit" class="btn btn--primary fs-14 font-semi-bold py-2 px-4">{{translate('update')}}  </button>
                        </div>
                    </form>
                </div>
            </div>

        </section>
    </div>
</div>
<span id="system-country-restrict-status" data-value="{{ $country_restrict_status }}"></span>
@endsection

@push('script')
<script src="{{ theme_asset(path: 'public/assets/front-end/js/bootstrap-select.min.js') }}"></script>
<script src="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/js/intlTelInput.js') }}"></script>
<script src="{{ theme_asset(path: 'public/assets/front-end/js/country-picker-init.js') }}"></script>
<script src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_MAPS_API_KEY')}}&libraries=places&callback=initAutocomplete" async></script>

{{-- search place using google map --}}
<script>
    let autocomplete;

    function initAutocomplete() {
        const input = document.getElementById("search-place");
        const options = {
            componentRestrictions: { country: "IN" }
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
        let country = '';
        let state = '';
        let city = '';
        let postalCode = '';

        addressComponents.forEach(component => {
            const componentType = component.types[0];

            switch (componentType) {
                case 'country':
                    country = component.long_name;
                    break;
                case 'administrative_area_level_1': // State
                    state = component.long_name;
                    break;
                case 'locality': // City
                    city = component.long_name;
                    break;
                case 'postal_code':
                    postalCode = component.long_name;
                    break;
            }
        });

        $('#country').val(country);
        $('#state').val(state);
        $('#city').val(city);
        $('#zip').val(postalCode);
        $('#latitude').val(latitude);
        $('#longitude').val(longitude);
    }
</script>

@endpush
