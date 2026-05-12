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
        <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/owl.theme.default.min.css') }}">
<link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/rSliders/responsiveslides.css') }}">
<link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/rSliders/demo.css') }}">
<!--poojafilter-css-->
<link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/poojafilter/layout.css') }}">
<link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/animationbutton.css') }}">
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


    {{-- location modal --}}
    <div class="modal fade" id="locationModal" tabindex="-1" role="dialog" aria-labelledby="locationModalLabel"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="locationModalLabel">Enable Location</h5>
                </div>
                <div class="modal-body">
                    <p>We need your location to show products near you.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" id="turnOnLocationBtn" class="btn btn-primary">
                        <span id="btnText">Turn On Location</span>
                        <span id="btnSpinner" class="spinner-border spinner-border-sm d-none" role="status"
                            aria-hidden="true"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- chanage address modal --}}
    <div class="modal fade" id="changeAddressModal" tabindex="-1" role="dialog" aria-labelledby="changeAddressModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Change Your Address</h5>
                </div>
                <div class="modal-body">
                    <div class="position-relative">
                        <input type="text" class="form-control" id="google-search" placeholder="Search your address" />

                        <!-- Space below input for search icon / suggestions -->
                        <div id="search-space" class="border mt-2 p-3 text-center">
                            <i id="search-icon" class="fa fa-search fa-2x text-muted"></i>
                            <ul id="autocomplete-list" class="list-group d-none mt-2"
                                style=" max-height: 200px; overflow-y: auto;"></ul>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    {{-- main page --}}

    {{-- <div class="container-fluid py-3" dir="{{ Session::get('direction') }}">
        <div
            class="search-page-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
            <div class="mb-2 mb-md-0">
                <h5 class="font-semibold mb-1">{{ translate('same_day_delivery_products') }}</h5>
            </div>
            <div class="text-md-right">
                <h5 class="font-semibold mb-1">{{ translate('current_Address: ') }} <span id="current-address"></span></h5>
                <button class="btn btn-sm btn--primary" data-toggle="modal" data-target="#changeAddressModal">
                    {{ translate('change_address') }}
                </button>

            </div>
        </div>

    </div> --}}

    <div class="__inline-61 mt-2">
        <section class="slider-section">
            <div class="container-fluid">
                <div class="row">
                    <div class="callbacks_container">
                        <ul class="rslides" id="slider4">
                            @if (count($main_banner) > 0)
                            @foreach ($main_banner as $banner)
                            <li>
                                <a class="d-block" href="{{ $banner['url'] }}">
                                    <img
    
                                        src="{{ getValidImage(path: 'storage/app/public/banner/'.$banner['photo'], type: 'banner') }}"
                                        />
                                </a>
                            </li>
                            @endforeach
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        {{-- featured products --}}
        <div class="" id="featured-products">
        </div>

        {{-- sellers --}}
        <div class="" id="sellers">
        </div>

        {{-- latest products --}}
        <div class="" id="latest-products">
        </div>  

        {{-- category --}}
        <div class="" id="categories">
        </div>

        {{-- category product--}}
        <div class="" id="category-product">
        </div>
        
        {{-- <div class="container-fluid pb-5 mb-2 mb-md-4 rtl __inline-35" dir="{{ Session::get('direction') }}">
            <div class="row">
                <section class="col-12">
                    <div class="row w-100" id="latest-products">
                        <div class="d-flex justify-content-center w-100">
                            <div class="spinner-border" role="status">
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div> --}}
    </div>
@endsection

@push('script')
    <script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/gh/ethereumjs/browser-builds/dist/ethereumjs-tx/ethereumjs-tx-1.3.3.min.js">
    </script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/home.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/responsiveslides.min.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/jquery.mixitup.min.js') }}"></script>
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
                fetchLocationProducts(location.city);

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
                        fetchLocationProducts(city);

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

        // Fetch products
        function fetchLocationProducts(city) {
            selectedCity = city;

            $('#latest-products').html(
                '<div class="d-flex justify-content-center w-100 m-3"><div class="spinner-border"></div></div>'
            );

            $.ajax({
                url: "{{ route('same-day-delivery.index') }}",
                type: "GET",
                data: {
                    city: city
                },
                success: function(response) {
                    if (response.status) {
                        $('#categories').html(response.category_view);
                        $('#category-product').html(response.category_product_view);
                        $('#featured-products').html(response.featured_view);
                        $('#sellers').html(response.seller_view);
                        $('#latest-products').html(response.latest_view);
                        initSellerCarousel();
                        initFeaturedCarousel();
                    } else {
                        $('#categories').html(`<h6 class="text-center py-5">No category found</h6>`);
                        $('#featured-products').html(
                            `<h6 class="text-center py-5">No featured products found</h6>`);
                        $('#sellers').html(`<h6 class="text-center py-5">No sellers found</h6>`);
                        $('#latest-products').html(`<h6 class="text-center py-5">No products found</h6>`);
                    }
                }
            });
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
                        fetchLocationProducts(city);

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

    {{-- view all click --}}
    <script>        
        function viewAll(type){
            if(type == 'seller'){
                let url = "{{ url('/same-day-delivery/sellers') }}";
                window.location.href = url + "?city=" 
                + selectedCity;
            } else{
                let url = "{{ url('/same-day-delivery/products') }}";
                window.location.href = url + "?city=" 
                + selectedCity 
                + "&data_from=" + type;
            }
        }
    </script>

    {{-- own carousel --}}
    <script>
        function initSellerCarousel() {
    
            const $carousel = $('.seller-store-slider');
    
            if (!$carousel.length) {
                console.warn('Seller carousel not found');
                return;
            }
    
            // Destroy previous instance (VERY IMPORTANT for AJAX reload)
            if ($carousel.hasClass('owl-loaded')) {
                $carousel.trigger('destroy.owl.carousel');
                $carousel.removeClass('owl-loaded');
                $carousel.find('.owl-stage-outer').children().unwrap();
            }
    
            // Initialize again
            $carousel.owlCarousel({
                loop: true,
                margin: 10,
                nav: true,
                dots: false,
                autoplay: true,
                navText: [
                    "<i class='czi-arrow-left'></i>",
                    "<i class='czi-arrow-right'></i>"
                ],
                responsive: {
                    0: { items: 1 },
                    576: { items: 2 },
                    768: { items: 3 },
                    992: { items: 4 },
                    1200: { items: 4 }
                }
            });
        }

        function initFeaturedCarousel() {
    
            const $carousel = $('.featured-store-slider');
    
            if (!$carousel.length) {
                console.warn('Featured carousel not found');
                return;
            }
    
            // Destroy previous instance (VERY IMPORTANT for AJAX reload)
            if ($carousel.hasClass('owl-loaded')) {
                $carousel.trigger('destroy.owl.carousel');
                $carousel.removeClass('owl-loaded');
                $carousel.find('.owl-stage-outer').children().unwrap();
            }
    
            // Initialize again
            $carousel.owlCarousel({
                loop: true,
                margin: 10,
                nav: true,
                dots: false,
                autoplay: true,
                navText: [
                    "<i class='czi-arrow-left'></i>",
                    "<i class='czi-arrow-right'></i>"
                ],
                responsive: {
                    0: { items: 1 },
                    576: { items: 2 },
                    768: { items: 4 },
                    992: { items: 6 },
                    1200: { items: 6 }
                }
            });
        }
    </script>

<script type="text/javascript">
    // Slideshow 4
    $("#slider4").responsiveSlides({
        auto: true,
        pager: false,
        nav: true,
        speed: 500,
        namespace: "callbacks",
        before: function() {
            $('.events').append("<li>before event fired.</li>");
        },
        after: function() {
            $('.events').append("<li>after event fired.</li>");
        }
    });
</script>
    
@endpush
