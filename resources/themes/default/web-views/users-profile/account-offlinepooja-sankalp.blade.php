@extends('layouts.front-end.app')
@section('title', translate('order_Sankalp'))
@push('css_or_js')
    <link rel="stylesheet"
        href="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/css/intlTelInput.css') }}">
@endpush
@section('content')
    <style>
        .pac-container {
            z-index: 10000 !important;
        }

        .btn-check+.btn {
            min-width: 150px;
            text-align: center;
        }

        .btn-check:checked+.btn {
            background-color: #0d6efd;
            color: #ffffff !important;
            border-color: #0d6efd;
        }
    </style>
    <div class="container pb-5 mb-2 mb-md-4 mt-3 rtl __inline-47 text-align-direction">
        <div class="row g-3">
            @include('web-views.partials._profile-aside')
            <section class="col-lg-9">
                @include('web-views.users-profile.offlinepooja-details.offlinepooja-order-partial')
                <div class="card border-0">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                            <h5 class="font-bold m-0 fs-16">Sankalp Details Updates</h5>
                            <div class="text-end d-none d-lg-block">
                                @if ($order['is_edited'] == '0')
                                    <button class="btn btn-danger px-2 py-1" type="button" id="editButton">Edit
                                        Details</button>
                                @else
                                    <button class="btn btn-primary px-2 py-1" type="button" id="editButton">Show
                                        Details</button>
                                @endif
                            </div>

                        </div>
                        @if ($order)
                            <form class="needs-validation" id="sankalp_check"
                                action="{{ route('offlinepoojasanklpUpdate', $order['order_id']) }}" method="post">
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

                                <div class="row hideable-div mt-3">

                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label class="form-label font-semibold">{{ translate('booking_Date') }}<span
                                                    class="text-danger">*</span></label>
                                            <input class="form-control text-align-direction" type="date"
                                                name="booking_date" value="{{ $order['booking_date'] }}"
                                                {{ $order['is_edited'] == '1' ? 'disabled' : '' }} min="{{ date('Y-m-d', strtotime('+2 days')) }}" required>
                                        </div>
                                    </div>

                                    @if ($order['is_edited'] == '1')
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label
                                                    class="form-label font-semibold">{{ translate('pooja_method - ' . $order['pooja_method']) }}</label>
                                            </div>
                                        </div>
                                        @if ($order['pooja_venue_type'] == 'address')
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <label
                                                        class="form-label font-semibold">{{ translate('venue_address ') }}</label>
                                                    <input class="form-control text-align-direction" type="text"
                                                        value="{{ $order['venue_address'] }}" disabled>
                                                </div>
                                            </div>
                                            @if (!empty($order['landmark']))
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <label
                                                            class="form-label font-semibold">{{ translate('landmark') }}</label>
                                                        <input class="form-control text-align-direction" type="text"
                                                            name="landmark" value="{{ $order['landmark'] }}" disabled>
                                                    </div>
                                                </div>
                                            @endif
                                        @else
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <label
                                                        class="form-label font-semibold">{{ translate('your_temple') }}</label>
                                                    <input class="form-control text-align-direction" type="text"
                                                        value="{{ $order->temple->name }}" disabled>
                                                </div>
                                            </div>
                                        @endif
                                    @else
                                        <div class="row w-100 mt-2">
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <label class="form-label font-semibold">
                                                        {{ translate('pooja_method') }} <span class="text-danger">*</span>
                                                    </label>
                                                    <div class="pt-2 d-flex row gap-1">
                                                        <div
                                                            class="bg-success p-2 rounded align-content-center text-center col-5 offset-1">
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    name="pooja_method" id="online" value="online"
                                                                    checked style="width: 15px; height: 15px;">
                                                                <label class="form-check-label pt-1"
                                                                    for="online">Online</label>
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="bg-success p-2 rounded align-content-center text-center col-5">
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    name="pooja_method" id="offline" value="offline"
                                                                    style="width: 15px; height: 15px;">
                                                                <label class="form-check-label pt-1" for="offline">Offline
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>

                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <label
                                                        class="form-label font-semibold">{{ translate('pooja_venue_type') }}<span
                                                            class="text-danger">*</span></label>
                                                    <select name="pooja_venue_type" id="pooja-venue-type"
                                                        class="form-control">
                                                        <option value="">Choose Venue Type</option>
                                                        <option value="address">Your Address</option>
                                                        <option value="temple">Prefered Temple</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div id="temple-div" class="row w-100 ps-2" style="display: none">
                                                @php $temples = json_decode($order->offlinePooja->temples_id, true); @endphp

                                                @if (!empty($temples))
                                                    <div class="d-flex flex-wrap gap-3">
                                                        @foreach ($temples as $templeId)
                                                            @php
                                                                $temple = App\Models\Temple::select('id', 'name')
                                                                    ->where('id', $templeId)
                                                                    ->first();
                                                            @endphp
                                                            @if ($temple)
                                                                <input type="radio" class="btn-check" name="temple_id"
                                                                    id="temple-{{ $temple->id }}"
                                                                    value="{{ $temple->id }}"
                                                                    {{ $loop->first ? 'checked' : '' }}>
                                                                <label class="btn btn-outline-primary"
                                                                    for="temple-{{ $temple->id }}">
                                                                    {{ $temple->name }}
                                                                </label>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <p>No preferred temple available</p>
                                                @endif
                                            </div>

                                            <div id="user-address-div" class="row w-100" style="display: none">
                                                <div class="col-12 ps-2">
                                                    <div class="form-group">
                                                        <label class="form-label font-semibold">{{ translate('venue_address ') }}<span
                                                                class="text-danger">*</span></label>
                                                        <textarea class="form-control text-align-direction" type="text" name="venue_address" id="google-search2"
                                                            value="" placeholder="your venue address" rows="2"></textarea>
                                                    </div>
                                                </div>
            
                                                <div class="col-sm-6 ps-2">
                                                    <div class="form-group">
                                                        <select name="city" id="city2" class="form-control" required readonly>
                                                            <option value="{{ $order['city'] }}">{{ $order['city'] }}
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
            
            
                                                <div class="col-sm-6 ps-2">
                                                    <div class="form-group">
                                                        <select name="pincode" id="pincode2" class="form-control">
                                                            <option value="">Select Pincode</option>
                                                            @forelse ($cityData as $city)
                                                                <option value="{{ $city['pincode'] }}"
                                                                    data-latitude="{{ $city['latitude'] }}"
                                                                    data-longitude="{{ $city['longitude'] }}">{{ $city['pincode'] }}
                                                                </option>
                                                            @empty
                                                                <option value="">No pincode found</option>
                                                            @endforelse
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12">

                                                    <div class="form-group">
                                                        {{-- <label
                                                            class="form-label font-semibold">{{ translate('venue_address ') }}<span
                                                                class="text-danger">*</span></label>
                                                        <input class="form-control text-align-direction" type="text"
                                                            name="venue_address" id="google-search2"
                                                            placeholder="venue address"> --}}
                                                        <input class="form-control" type="hidden" name="state"
                                                            id="state2" placeholder="State"  value="{{ $state->states->name }}">
                                                        {{-- <input class="form-control" type="hidden" name="city"
                                                            id="city2" placeholder="City Name">
                                                        <input class="form-control" type="hidden" name="pincode"
                                                            id="pincode2" placeholder="Pincode"> --}}
                                                        <input class="form-control" type="hidden" name="latitude"
                                                            id="latitude2" placeholder="latitude">
                                                        <input class="form-control" type="hidden" name="longitude"
                                                            id="longitude2" placeholder="longitude">
                                                    </div>
                                                </div>
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <label
                                                            class="form-label font-semibold">{{ translate('landmark') }}</label>
                                                        <input class="form-control text-align-direction" type="text"
                                                            name="landmark" placeholder="Landmark">
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    @endif
                                </div>
                                @if ($order['is_edited'] == '0')
                                    <div class="web-direction">
                                        <div class="mx-auto mt-4 __max-w-356">
                                            <button class="w-100 btn btn--primary" id="update-btn"
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
    {{-- <script
        src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places&callback=initAutocomplete"
        async></script> --}}
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/spartan-multi-image-picker.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/account-order-details.js') }}"></script>

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

        });
    </script>
    <script>
        $(document).ready(function() {
            $('#editButton').click(function() {
                $('#sankalp_check').toggle();
            });
        });
    </script>

    {{-- pooja venue type div show --}}
    <script>
        $('#pooja-venue-type').change(function(e) {
            e.preventDefault();
            var type = $(this).val();

            if (type == "address") {
                $('#user-address-div').show();
                $('#temple-div').hide();
                $('#google-search2').attr('required', true);
                $('#pincode2').attr('required', true);
                $('#update-btn').attr('disabled', false);
            } else if (type == "temple") {
                $('#user-address-div').hide();
                $('#temple-div').show();
                $('#google-search2').attr('required', false);
                $('#pincode2').attr('required', false);
                $('#update-btn').attr('disabled', false);
            } else {
                $('#user-address-div').hide();
                $('#temple-div').hide();
                $('#google-search2').attr('required', false);
                $('#pincode2').attr('required', false);
                $('#update-btn').attr('disabled', true);
            }

        });
    </script>

    {{-- pincode change --}}
    <script>
        $(document).ready(function() {
            $('#pincode2').on('change', function() {
                let selected = $(this).find(':selected');

                if (selected.val() !== "") {
                    $('#latitude2').val(selected.data('latitude'));
                    $('#longitude2').val(selected.data('longitude'));
                } else {
                    $('#latitude2').val('');
                    $('#longitude2').val('');
                }
            });
        });
    </script>
@endpush
