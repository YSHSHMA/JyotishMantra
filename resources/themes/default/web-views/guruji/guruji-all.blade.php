@extends('layouts.front-end.app')

@php
    use Carbon\Carbon;
    use App\Utils\Helpers;
@endphp
@push('css_or_js')
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/home.css') }}" />
@endpush

@section('content')
    <div class="container mb-md-4 {{ Session::get('direction') === 'rtl' ? 'rtl' : '' }} __inline-65">
        <div class="bg-primary-light rounded-10 my-4 p-3 p-sm-4"
            data-bg-img="{{ theme_asset(path: 'public/assets/front-end/img/media/bg.png') }}">
            <div class="row g-2 align-items-center">
                <div class="col-lg-8 col-md-6">
                    <div class="d-flex flex-column gap-1 text-primary">
                        <h4 class="mb-0 text-start fw-bold text-primary text-uppercase">{{ translate('all_Gurujis') }}</h4>
                        <p class="fs-14 fw-semibold mb-0">{{ translate('Find_your_Guruji_and_book_your_pooja') }}
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="input-group">
                        <input type="text" class="form-control rounded-10" value="{{ request('name') }}"
                            placeholder="{{ translate('Search_Guru_ji') }}" id="name" name="name">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary rounded-10" type="button"
                                onclick="searchGuruji()">{{ translate('search') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <section class="col-lg-12">
                <div class="row mx-n2 __min-h-200px" id="guruji-list">
                    <div class="d-flex justify-content-center w-100 m-3">
                        <div class="spinner-border" role="status"></div>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection

@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places&callback=initAutocompleteCustom"
        async defer></script>

    <script>
        $(document).ready(function() {

            // 1) On page load – check if location exists in storage
            let storedLocation = JSON.parse(localStorage.getItem("location"));

            if (navigator.geolocation) {
                // Try to access user location
                navigator.geolocation.getCurrentPosition(
                    function() {
                        // Location access allowed
                        handleLocationAvailable(storedLocation);
                    },
                    function() {
                        // Location access denied/off
                        handleLocationDisabled();
                    }
                );
            } else {
                // Geolocation not supported
                handleLocationDisabled();
            }

            function handleLocationAvailable(storedLocation) {

                if (storedLocation && storedLocation.city) {
                    // ✔️ 2.a Use stored city
                    fetchLocationProducts(storedLocation.city, null);
                } else {
                    // ✔️ 2.b Detect location AND store it
                    detectAndStoreLocation();
                }
            }

            function handleLocationDisabled() {
                console.log("Location is OFF or denied by user.");

                // ✔️ 3) Load products with no city
                fetchLocationProducts(null, null);
            }


            function detectAndStoreLocation() {

                navigator.geolocation.getCurrentPosition(function(position) {
                    const lat = position.coords.latitude;
                    const long = position.coords.longitude;

                    const geocoder = new google.maps.Geocoder();
                    const latlng = {
                        lat: lat,
                        lng: long
                    };

                    geocoder.geocode({
                        location: latlng
                    }, function(results, status) {
                        if (status === "OK" && results[0]) {
                            let city = extractCity(results[0].address_components);
                            let address = results[0].formatted_address;

                            if (city) {
                                storeLocation(city, lat, long, address);

                                // Now load products with city
                                fetchLocationProducts(city, null);
                            }
                        }
                    });

                });
            }

            function extractCity(components) {
                let city = null;

                // Priority 1 → locality
                components.forEach(c => {
                    if (c.types.includes("locality")) city = c.long_name;
                });

                // Priority 2 → state (if city is not found)
                if (!city) {
                    components.forEach(c => {
                        if (c.types.includes("administrative_area_level_1")) {
                            city = c.long_name;
                        }
                    });
                }

                return city;
            }

            function storeLocation(city, lat, long, address) {
                const locationData = {
                    city: city,
                    lat: lat,
                    long: long,
                    address: address
                };
                localStorage.setItem("location", JSON.stringify(locationData));
            }

        });
    </script>

    {{-- get guruji data --}}
    <script>
        function fetchLocationProducts(city, name) {
            $('#guruji-list').html(
                '<div class="d-flex justify-content-center w-100 m-3"> <div class="spinner-border" role="status"></div></div>'
            );
            $.ajax({
                url: "{{ route('guruji.guruji-data') }}",
                type: "GET",
                data: {
                    city: null,
                    name: name
                },
                success: function(response) {
                    if (response.status) {
                        $('#guruji-list').html(response.view);
                    } else {
                        $('#guruji-list').html(
                            `<div class="no_product_found d-flex justify-content-center align-items-center w-100 py-5">
                                <div class="text-center">
                                    <img src="{{ theme_asset(path: 'public/assets/front-end/img/media/pandit.png') }}" class="img-fluid" alt="" style="width: 200px;">
                                    <h6 class="text-muted">{{ translate('no_guruji_found') }}</h6>
                                    <a href="{{ route('home') }}" class="btn btn--primary">{{ translate('home') }}</a>
                                </div>
                            </div>`

                        );
                    }
                }
            });
        }
    </script>

    {{-- search --}}
    <script>
        function searchGuruji() {
            const name = $('#name').val().trim();
            if (name.length <= 0) {
                toastr.error('Enter guruji name to search');
                return;
            }

            let location = JSON.parse(localStorage.getItem("location"));
            if (!location) {
                fetchLocationProducts(null, name);
            } else {
                fetchLocationProducts(location.city, name);
            }
        }
    </script>
@endpush
