@extends('layouts.front-end.app')
@section('title', translate("Welcome to Mahakal.com, World's Largest Devotional Platform"))
@push('css_or_js')
    <meta property="og:image"
        content="{{ dynamicStorage(path: 'storage/app/public/company') }}/{{ $web_config['web_logo'] }}" />
    <meta property="og:title" content="Products of {{ $web_config['name'] }} " />
    <meta property="og:url" content="{{ env('APP_URL') }}">
    <meta property="og:description"
        content="{{ substr(strip_tags(str_replace('&nbsp;', ' ', $web_config['about']->value)), 0, 160) }}">

    <meta property="twitter:card"
        content="{{ dynamicStorage(path: 'storage/app/public/company') }}/{{ $web_config['web_logo'] }}" />
    <meta property="twitter:title" content="Products of {{ $web_config['name'] }}" />
    <meta property="twitter:url" content="{{ env('APP_URL') }}">
    <meta property="twitter:description"
        content="{{ substr(strip_tags(str_replace('&nbsp;', ' ', $web_config['about']->value)), 0, 160) }}">
    <style>
        .for-count-value {
                {
                    {
                    Session: :get('direction')==="rtl" ? 'left': 'right'
                }
            }

            : 0.6875 rem;
            ;
        }

        .for-count-value {

                {
                    {
                    Session: :get('direction')==="rtl" ? 'left': 'right'
                }
            }

            : 0.6875 rem;
        }

        .for-brand-hover:hover {
            color: var(--web-primary);
        }

        .for-hover-label:hover {
            color: var(--web-primary) !important;
        }

        .page-item.active .page-link {
            background-color: var(--web-primary) !important;
        }

        .for-sorting {
            padding- {
                    {
                    Session: :get('direction')==="rtl" ? 'left': 'right'
                }
            }

            : 9px;
        }

        .sidepanel {
                {
                    {
                    Session: :get('direction')==="rtl" ? 'right': 'left'
                }
            }

            : 0;
        }

        .sidepanel .closebtn {
            {{ Session::get('direction') === 'rtl' ? 'left' : 'right' }}: 25 px;
        }

        @media (max-width: 360px) {
            .for-sorting-mobile {
                margin- {
                        {
                        Session: :get('direction')==="rtl" ? 'left': 'right'
                    }
                }

                : 0% !important;
            }

            .for-mobile {

                margin- {
                        {
                        Session: :get('direction')==="rtl" ? 'right': 'left'
                    }
                }

                : 10% !important;
            }

        }

        /* @media (max-width: 500px) {
                                                    .for-mobile {
                                                        margin- {
                                                                {
                                                                Session: :get('direction')==="rtl" ? 'right': 'left'
                                                            }
                                                        }

                                                        : 27%;
                                                    }
                                                } */
    </style>
@endpush

@section('content')

    {{-- main page --}}

    <div class="container-fluid py-3" dir="{{ Session::get('direction') }}">
        <div class="search-page-header d-flex justify-content-between align-items-center">
            <h5 class="font-semibold mb-1">{{ translate('same_day_delivery_sellers') }}</h5>
            <p class="font-semibold mb-1">{{ translate('total_sellers: ') . $sellers->count() }}</p>
        </div>
    </div>

    <div class="container-fluid">

        <div class="row">
            <section class="col-lg-12">
                @if(count($sellers) > 0)
                    <div class="row mx-n2 __min-h-200px">
                        @foreach ($sellers as $seller)
                            @php($current_date = date('Y-m-d'))
                            @php($start_date = date('Y-m-d', strtotime($seller['vacation_start_date'])))
                            @php($end_date = date('Y-m-d', strtotime($seller['vacation_end_date'])))

                            <div class="col-lg-3 col-md-6 col-sm-12 px-2 pb-4 text-center">
                                <a href="{{route('shopView',['id'=>$seller['shop']['slug']])}}" class="others-store-card text-capitalize">
                                    <div class="overflow-hidden other-store-banner">
                                        @if($seller['id'] != 0)
                                            <img class="w-100 h-100 object-cover" alt="" src="{{ getValidImage(path: 'storage/app/public/shop/banner/'.$seller['shop']['banner'], type: 'shop-banner') }}">
                                        @else
                                            <img class="w-100 h-100 object-cover" alt="" src="{{ getValidImage(path: 'storage/app/public/shop/'.$seller['shop']['banner'], type: 'shop-banner') }}">
                                        @endif
                                    </div>
                                    <div class="name-area">
                                        <div class="position-relative">
                                            <div class="overflow-hidden other-store-logo rounded-full">
                                                @if($seller['id'] != 0)
                                                    <img class="rounded-full" alt="{{ translate('store') }}"
                                                         src="{{ getValidImage(path: 'storage/app/public/shop/'.$seller['shop']['image'], type: 'shop') }}">
                                                @else
                                                <img class="rounded-full" alt="{{ translate('store') }}"
                                                     src="{{ getValidImage(path: 'storage/app/public/company/'.$seller['shop']['image'], type: 'shop') }}">
                                                @endif
                                            </div>

                                            @if($seller['temporary_close'] || ($seller['vacation_status'] && ($current_date >= $seller['vacation_start_date']) && ($current_date <= $seller['vacation_end_date'])))
                                                <span class="temporary-closed position-absolute text-center rounded-full p-2">
                                                    <span>{{translate('closed_now')}}</span>
                                                </span>
                                            @endif
                                        </div>
                                        <div class="info pt-2">
                                            <h5 class="text-start">{{ $seller['name'] }}</h5>
                                            <div class="d-flex align-items-center">
                                                <h6 class="web-text-primary">{{number_format($seller['average_rating'],1)}}</h6>
                                                <i class="tio-star text-star mx-1"></i>
                                                <small>{{ translate('rating') }}</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="info-area">
                                        <div class="info-item">
                                            <h6 class="web-text-primary">{{$seller['review_count'] < 1000 ? $seller['review_count'] : number_format($seller['review_count']/1000 , 1).'K'}}</h6>
                                            <span>{{ translate('reviews') }}</span>
                                        </div>
                                        <div class="info-item">
                                            <h6 class="web-text-primary">{{$seller['product_count'] < 1000 ? $seller['product_count'] : number_format($seller['product_count']/1000 , 1).'K'}}</h6>
                                            <span>{{ translate('products') }}</span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>

                @else
                    <div class="mb-5 text-center text-muted">
                        <div class="d-flex justify-content-center my-2">
                            <img alt="" src="{{ theme_asset(path: 'public/assets/front-end/img/media/seller.svg') }}">
                        </div>
                        <h4 class="text-muted">{{ translate('vendor_not_available') }}</h4>
                        <p>{{ translate('Sorry_no_data_found_related_to_your_search') }}</p>
                    </div>
                @endif
            </section>
        </div>

    </div>
@endsection

@push('script')
    <script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/gh/ethereumjs/browser-builds/dist/ethereumjs-tx/ethereumjs-tx-1.3.3.min.js">
    </script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/home.js') }}"></script>
    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places&callback=initAutocompleteCustom"
        async defer></script>

    <script>
        let selectedCity = null;
        let selectedCategory = null;

        $("#current-address").text('');

        $(document).ready(function() {
            let location = JSON.parse(localStorage.getItem("location"));

            if (!location) {
                $("#locationModal").modal("show");
            } else {
                showAddress(location.address);
            }

            $("#turnOnLocationBtn").on("click", function() {
                $("#btnText").addClass("d-none");
                $("#btnSpinner").removeClass("d-none");
                getLocation();
            });
        });

        // Get location from browser
        function getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(showPosition, showError);
            } else {
                toastr.error("Geolocation not supported by this browser.");
                resetBtn();
            }
        }

        function showPosition(position) {
            const lat = position.coords.latitude;
            const long = position.coords.longitude;

            const geocoder = new google.maps.Geocoder();
            const latlng = {
                lat: parseFloat(lat),
                lng: parseFloat(long)
            };

            geocoder.geocode({
                location: latlng
            }, function(results, status) {
                if (status === "OK" && results[0]) {
                    let city = null;
                    let address = results[0].formatted_address;

                    results[0].address_components.forEach(c => {
                        if (c.types.includes("locality")) city = c.long_name;
                    });
                    if (!city) {
                        results[0].address_components.forEach(c => {
                            if (c.types.includes("administrative_area_level_1")) city = c.long_name;
                        });
                    }

                    if (city) {
                        const location = {
                            city: city,
                            lat: lat,
                            long: long,
                            address: address
                        };
                        localStorage.setItem("location", JSON.stringify(location));

                        $("#locationModal").modal("hide");

                        showAddress(address);

                    } else {
                        toastr.error('could not detect location');
                        resetBtn();
                    }
                } else {
                    toastr.error('Geocoder failed');
                    resetBtn();
                }
            });
        }

        // Error handling
        function showError(error) {
            switch (error.code) {
                case error.PERMISSION_DENIED:
                    toastr.error("Please enable location access.");
                    break;
                case error.POSITION_UNAVAILABLE:
                    toastr.error("Location info unavailable.");
                    break;
                case error.TIMEOUT:
                    toastr.error("Request timed out.");
                    break;
                default:
                    toastr.error("An unknown error occurred.");
            }
            resetBtn();
            // localStorage.removeItem("location");
            // $("#current-address").text('');
        }

        // Reset button state
        function resetBtn() {
            $("#btnText").removeClass("d-none");
            $("#btnSpinner").addClass("d-none");
        }

        // Show short address
        function showAddress(address) {
            let words = address.split(' ');
            let shortAddress = words.slice(0, 3).join(' ');
            if (words.length > 3) shortAddress += '...';
            $("#current-address").text(shortAddress);
        }

        // GOOGLE SEARCH FOR CHANGE ADDRESS
        let autocompleteService;
        let placesService;

        function initAutocompleteCustom() {
            const input = document.getElementById('google-search');
            const searchIcon = $("#search-icon");
            const list = $("#autocomplete-list");

            autocompleteService = new google.maps.places.AutocompleteService();
            placesService = new google.maps.places.PlacesService(document.createElement('div'));

            $(input).on('input', function() {
                const query = $(this).val().trim();
                if (query.length === 0) {
                    searchIcon.show();
                    list.addClass("d-none").empty();
                    return;
                }

                searchIcon.hide();

                autocompleteService.getPlacePredictions({
                    input: query
                }, function(predictions, status) {
                    list.empty();
                    if (status !== google.maps.places.PlacesServiceStatus.OK || !predictions) {
                        list.addClass("d-none");
                        return;
                    }

                    predictions.forEach(pred => {
                        const li = $(
                            '<li class="list-group-item list-group-item-action" style="cursor:pointer"></li>'
                        );
                        li.text(pred.description);
                        li.data('place-id', pred.place_id);
                        list.append(li);
                    });

                    list.removeClass("d-none");
                });
            });

            // On select
            $(document).on('click', '#autocomplete-list li', function() {
                const placeId = $(this).data('place-id');

                placesService.getDetails({
                    placeId: placeId
                }, function(place, status) {
                    if (status === google.maps.places.PlacesServiceStatus.OK) {
                        const lat = place.geometry.location.lat();
                        const lng = place.geometry.location.lng();
                        const city = getCityFromPlace(place);
                        const address = place.formatted_address;

                        const locationData = {
                            city,
                            lat,
                            long: lng,
                            address
                        };
                        localStorage.setItem("location", JSON.stringify(locationData));

                        showAddress(address);

                        // Reset UI
                        $('#google-search').val('');
                        $("#autocomplete-list").empty().addClass("d-none");
                        $("#search-icon").show();
                        $('#changeAddressModal').modal('hide');
                    }
                });
            });
        }

        // Helper
        function getCityFromPlace(place) {
            let city = '';
            place.address_components.forEach(c => {
                if (c.types.includes("locality")) city = c.long_name;
            });
            if (!city) {
                place.address_components.forEach(c => {
                    if (c.types.includes("administrative_area_level_1")) city = c.long_name;
                });
            }
            return city;
        }
    </script>
@endpush
