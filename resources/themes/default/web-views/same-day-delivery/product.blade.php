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
            <h5 class="font-semibold mb-1">{{ translate('same_day_delivery_products') }}</h5>
            <p class="font-semibold mb-1">
                {{ translate('total_products: ') . ($data_from == 'category'
                    ? $products->flatten()->count()
                    : $products->count()) }}
            </p>
            
        </div>

    </div>

    <div class="__inline-61">
        <div class="container-fluid rtl">
            @php($decimalPointSettings = getWebConfig(name: 'decimal_point_settings') ?? 0)
    
            {{-- ================= CATEGORY VIEW ================= --}}
            @if($data_from == 'category')
    
                {{-- Category Tabs --}}
                <div class="row mb-3">
                    <div class="col-md-3 d-flex flex-column gap-2 py-2">
                        @foreach($products as $categoryId => $categoryProducts)
                            <button
                                class="btn btn-outline-primary category-btn"
                                onclick="showCategory({{ $categoryId }})"
                                id="cat-btn-{{ $categoryId }}"
                            >
                                {{ $categoryProducts->first()->category->name }}
                            </button>
                        @endforeach
                    </div>

                    {{-- Category Products --}}
                    <div class="col-md-9">
                    @foreach($products as $categoryId => $categoryProducts)
                        <div class="row g-2 category-products d-none" id="category-{{ $categoryId }}">
                            @foreach ($categoryProducts as $product)
                                <div class="col-xl-2 col-sm-4 col-md-3 col-lg-2 col-6">
                                    @include('web-views.partials._inline-single-product', [
                                        'product' => $product,
                                        'decimal_point_settings' => $decimalPointSettings,
                                    ])
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
                </div>
    
    
            {{-- ================= FEATURED / LATEST VIEW ================= --}}
            @else
    
                <div class="row g-2">
                    @foreach ($products as $product)
                        <div class="col-xl-2 col-sm-4 col-md-3 col-lg-2 col-6">
                            @include('web-views.partials._inline-single-product', [
                                'product' => $product,
                                'decimal_point_settings' => $decimalPointSettings,
                            ])
                        </div>
                    @endforeach
                </div>
    
            @endif
        </div>
    </div>
    
@endsection

@push('script')
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

    <script>
        function showCategory(categoryId) {
            document.querySelectorAll('.category-products').forEach(el => {
                el.classList.add('d-none');
            });
    
            document.querySelectorAll('.category-btn').forEach(btn => {
                btn.classList.remove('btn-primary');
                btn.classList.add('btn-outline-primary');
            });
    
            document.getElementById('category-' + categoryId).classList.remove('d-none');
            document.getElementById('cat-btn-' + categoryId).classList.add('btn-primary');
        }
    
        // Auto-select first category
        document.addEventListener('DOMContentLoaded', function () {
            let firstCategory = document.querySelector('.category-btn');
            if (firstCategory) {
                firstCategory.click();
            }
        });
    </script>
    
@endpush
