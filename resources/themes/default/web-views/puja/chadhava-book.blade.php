<!DOCTYPE html>
<html lang="hi">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Mahakal • Booking Form</title>

    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/roboto-font.css') }}">
    <link href="{{ theme_asset(path: 'public/assets/front-end/css/poojafilter/bootstrapnew.min.css') }}"
        rel="stylesheet" />
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/owl.theme.default.min.css') }}">
    <link
        rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/css/intlTelInput.css') }}">
    <link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />


    <style>
        .gj-datepicker-bootstrap [role=right-icon] button .gj-icon {
            top: 14px;
            right: 5px;
        }

        .gj-timepicker-bootstrap [role=right-icon] button .gj-icon {
            top: 14px;
            right: 5px;
        }

        header.fixed-top {
            height: 80px;
            /* actual header height */
        }

        /* Desktop default */
        .site-logo {
            max-height: 70px;
        }

        /* Mobile view → logo बड़ा */
        @media (max-width: 768px) {
            .site-logo {
                max-height: 100px;
            }
        }

        .card-hero {
            border: 0;
            overflow: hidden;
            border-radius: 1.25rem;
            background: #f3f4f6;
            color: #fff;
            padding: 1rem;
        }

        /* Slider */
        .owl-carousel .item img {
            border-radius: 1rem;
            max-height: 250px;
            /* object-fit: cover; */
        }

        .owl-theme .owl-dots .owl-dot span {
            background: transparent !important;
            border: 2px solid #FF9800;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
            margin: 5px;
            transition: all 0.3s ease;
        }

        /* Active dot */
        .owl-theme .owl-dots .owl-dot.active span {
            background: #FF9800 !important;
            border-color: #FF9800;
        }

        /* Sticky Footer */
        .sticky-bar {
            position: sticky;
            bottom: 0;
            z-index: 951;
            background-color: #eee;
            border-top: 2px solid #FF9800;
            color: #000;
        }

        /* Pooja name  */
        .pooja-calendar {
            font-size: 16px;
            margin-bottom: 5px;
            font-weight: 800;
            margin-left: 8px;
            color: #000000 !important;
        }

        .pooja-venue {
            font-size: 16px;
            margin-bottom: 5px;
            font-weight: 800;
            margin-left: 8px;
            color: #000000 !important;

        }

        .leading-normal {
            color: #000000 !important;
        }

        /* Count  */
        .countdown {
            display: flex;
            justify-content: center;
            gap: 25px;
            flex-wrap: wrap;
            margin-top: 10px;
        }

        .time-box {
            background: #111;
            color: #FF6F00;
            text-align: center;
            padding: 8px 12px;
            /* height कम करने के लिए padding घटाई */
            border-radius: 10px;
            min-width: 60px;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.25);
        }

        .time-box span {
            display: block;
            font-size: 1.6rem;
            /* पहले 2rem था → अब छोटा */
            font-weight: bold;
            line-height: 1.2;
            /* ज्यादा height ना ले */
        }

        .time-box small {
            display: block;
            margin-top: 2px;
            /* पहले 4px था */
            font-size: 0.8rem;
            color: #fff;
        }

        /* Tablet */
        @media (max-width: 992px) {
            .time-box {
                min-width: 55px;
                padding: 7px 10px;
            }

            .time-box span {
                font-size: 1.4rem;
            }

            .time-box small {
                font-size: 0.75rem;
            }
        }

        /* Mobile */
        @media (max-width: 600px) {
            .countdown {
                gap: 8px;
            }

            .time-box {
                min-width: 50px;
                padding: 6px 8px;
            }

            .time-box span {
                font-size: 1.2rem;
            }

            .time-box small {
                font-size: 0.7rem;
            }
        }

        /* Container holding all profile circles */
        .tray {
            display: flex;
            align-items: center;
            gap: 2px;
            /* spacing between circles */
        }

        /* Individual circle container */
        .circle-img-container {
            position: relative;
            width: 45px;
            height: 45px;
        }

        /* The circle image itself */
        .circle-img {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            /* makes it circular */
            background-size: cover;
            /* image covers circle */
            background-position: center;
            background-repeat: no-repeat;
            border: 3px solid #fff;
            /* white border around */
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
            /* soft shadow */
            cursor: pointer;
            transition: transform 0.2s ease-in-out;
        }

        /* Hover effect */
        .circle-img:hover {
            transform: scale(1.1);
            border-color: #FF6F00;
            /* saffron border on hover */
        }

        .fa-star {
            color: #FF6F00;
        }

        .btn-primary {
            background-color: #FF6F00;
            border-color: #FF6F00;
        }

        .btn-primary:hover {
            background-color: #FF6F00;
            border-color: #FF6F00;
        }

        .font-10 {
            font-size: 14px;
            color: #000;
            font-weight: 700;
        }

        .carousel-img {
            width: 100%;
            height: auto;
            /* default for mobile */
            max-height: 500px;
            /* limit on big screens */
            object-fit: contain;
            /* full image visible */
            border-radius: 8px;
        }

        /* For laptops/desktops */
        @media (min-width: 992px) {
            .carousel-img {
                height: 500px;
                /* fix height */
                object-fit: cover;
                /* fills box without stretching */
            }
        }

        /* For tablets */
        @media (min-width: 768px) and (max-width: 991px) {
            .carousel-img {
                height: 400px;
            }
        }

        /* For small mobile */
        @media (max-width: 767px) {
            .carousel-img {
                height: auto;
                max-height: 250px;
                object-fit: contain;
            }
        }

        #footerInfo {
            color: #000
        }

        /* Product List Css */
        .product-slide {
            position: fixed;
            bottom: 100px;
            /* hidden initially */
            left: 0;
            width: 100%;
            background: #1c1c1c;
            color: #fff;
            padding: 1rem;
            box-shadow: 0 -5px 20px rgba(0, 0, 0, .4);
            transition: bottom 0.3s ease-in-out;
            z-index: 9999;
        }

        .product-slide.show {
            bottom: 0;
            /* slide up */
        }

        .slide-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .slide-img {
            height: 50px;
            width: 50px;
            object-fit: contain;
        }

        .slide-name {
            font-size: 1rem;
        }

        .slide-price {
            font-size: 0.9rem;
            color: #FF6F00;
        }



        .product-card:hover {
            transform: translateY(-3px);
        }

        .slide-img {
            flex-shrink: 0;
        }

        .owl-carousel .item {
            padding: 5px;
        }

        #productList {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            /* cards ke beech gap */
        }

        #productList .product-card {
            flex: 0 0 calc(50% - 10px);
            /* Mobile: 2 cards per row */
            max-width: calc(50% - 10px);
            background: #fff;
            border-radius: 8px;
            padding: 8px;
            text-align: center;
        }

        @media (min-width: 768px) {
            #productList .product-card {
                flex: 0 0 calc(25% - 10px);
                /* Tablet/Desktop: 4 per row */
                max-width: calc(25% - 10px);
            }
        }



        .product-card {
            width: 100% !important;
            max-width: 100% !important;
        }


        .product-card .product-name {
            font-size: 14px;
            font-weight: 500;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 130px;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
        }

        .product-card .product-name.expanded {
            white-space: normal;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 180px;
        }

        @media (min-width: 768px) {
            .product-card {
                max-width: 350px;
            }
        }

        .text-warning {
            color: #FF6F00 !important;
        }

        .quantity-counter .btn {
            width: 32px;
            height: 32px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .quantity-counter .quantity {
            min-width: 24px;
            text-align: center;
            font-weight: 600;
        }

        /* Scrollbar for Chrome, Edge, Safari */
        #product::-webkit-scrollbar {
            width: 8px;
        }

        #product::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        #product::-webkit-scrollbar-thumb {
            background: orange;
            border-radius: 4px;
        }

        #product::-webkit-scrollbar-thumb:hover {
            background: darkorange;
        }

        #product {
            scrollbar-width: thin;
            scrollbar-color: orange #f1f1f1;
        }
    </style>
</head>

<body>
    @php
    $ecommerceLogo = getWebConfig('company_web_logo');
    @endphp
    <header class="container-fluid py-3 bg-white shadow-sm fixed-top">
        <div class="row align-items-center">
            <!-- Left: Logo -->
            <div class="col-8 d-flex align-items-center">
                <a href="{{ url('/') }}">
                    <img src="{{ getValidImage('storage/app/public/company/' . $ecommerceLogo, type: 'backend-logo') }}"
                        alt="Logo" class="site-logo img-fluid">
                </a>
            </div>

            <!-- Right: Steps -->
            <div class="col-4 d-flex justify-content-end align-items-center">
                <!-- Desktop Steps -->
                <div class="d-none d-md-block text-end">
                    <h1 class="h6 mb-1 text-warning">{{ translate('Online Booking Form') }}</h1>
                    <p class="text-secondary mb-0 small d-flex justify-content-between flex-nowrap align-items-center">
                        <span class="d-flex align-items-center">
                            <i class="fas fa-box me-1"></i> Package Selection
                        </span>
                        <span class="mx-1">→</span>
                        <span class="d-flex align-items-center">
                            <i class="fas fa-user me-1"></i> Details
                        </span>
                        <span class="mx-1">→</span>
                        <span class="d-flex align-items-center">
                            <i class="fas fa-check-circle me-1"></i> Confirmation
                        </span>
                        <span class="mx-1">→</span>
                        <span class="d-flex align-items-center">
                            <i class="fas fa-credit-card me-1"></i> Payment
                        </span>
                    </p>

                </div>

                <!-- Mobile Menu Icon -->
                <button class="btn btn-sm d-md-none ms-2" type="button" data-bs-toggle="collapse"
                    data-bs-target="#mobileSteps">
                    <i class="fas fa-bars fa-lg"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Steps Dropdown -->
        <div class="collapse bg-light p-2 mt-2 d-md-none" id="mobileSteps">
            <h6 class="text-warning mb-1">{{ translate('Online Booking Form') }}</h6>
            <p class="text-secondary small mb-0">
                <i class="fas fa-box"></i> {{ translate('Package Selection') }}
                <span class="mx-1">→</span>
                <i class="fas fa-user"></i> {{ translate('Details') }}
                <span class="mx-1">→</span>
                <i class="fas fa-check-circle"></i> {{ translate('Confirmation') }}
                <span class="mx-1">→</span>
                <i class="fas fa-credit-card"></i> {{ translate('Payment') }}
            </p>
        </div>

    </header>
    <div class="container">
        <!-- Main Content (header ke neeche se start hoga) -->
        <main class="" style="margin-top:100px;">
            <!-- Image Slider -->
            <section class="container mb-2">
                <div class="owl-carousel">
                    @foreach (json_decode($chadhava->images, true) ?? [] as $key => $photo)
                    <div class="item carousel-image ">
                        <img src="{{ getValidImage(
                                path: 'storage/app/public/chadhava/' . (is_array($photo) && isset($photo['image']) ? $photo['image'] : $photo),
                                type: 'product',
                            ) }}"
                            class="w-100 shadow-sm" alt="slide {{ $key + 1 }}">
                    </div>
                    @endforeach
                </div>
            </section>

            <!-- Hero Card -->
            <section class="container mb-3">
                <div class="card-hero">
                    @if (!empty($chadhava) && !empty($chadhava->pooja_heading))
                    <span class="text-12 font-bold  line-clamp-2 text-ellipsis mb-0"
                        style="color:#fe9802;">{{ strtoupper($chadhava->pooja_heading) }}
                    </span>
                    @endif




                    <div class="row align-items-center">
                        <!-- Left Side: Details -->
                        <div class="col-md-6 text-start">
                            <!-- Ratings Display -->
                            @foreach ($chadhavaGet as $service)
                            @php
                            $avgRating =
                            !empty($service->review_avg_rating) && $service->review_avg_rating > 0
                            ? $service->review_avg_rating
                            : 5.0;
                            $fullStars = floor($avgRating);
                            $halfStar = $avgRating - $fullStars >= 0.5 ? 1 : 0;
                            @endphp

                            <div class="font-10">
                                <h3
                                    class="text-sm mt-2 mb-2 font-medium border-b border-dashed border-primary font-weight-bold">
                                    <i class="fas fa-star"></i> {{ number_format($avgRating, 1) }}/5
                                    ({{ $serviceReview }}K+
                                    ratings)
                                </h3>
                            </div>
                            @endforeach

                            <div class="countdown d-flex gap-4 justify-content-md-start justify-content-center text-center fw-bold"
                                style="color:#FF6F00;">
                                <div class="time-box">
                                    <span class="number days">00</span><br>
                                    <small>{{ translate('Days') }}</small>
                                </div>
                                <div class="time-box">
                                    <span class="number hours">00</span><br>
                                    <small>{{ translate('Hours') }}</small>
                                </div>
                                <div class="time-box">
                                    <span class="number minutes">00</span><br>
                                    <small>{{ translate('Mins') }}</small>
                                </div>
                                <div class="time-box">
                                    <span class="number seconds">00</span><br>
                                    <small>{{ translate('Secs') }}</small>
                                </div>
                            </div>
                        </div>

                        <!-- Right Side: Countdown -->
                        <div class="col-md-6 text-start">
                            <div class="flex flex-row mt-2 flex-nowrap leading-normal">
                                <div>
                                    <span class="inline-flex"> {{ translate('Till_now') }}
                                    </span>
                                    <span class="font-bold text-dark ml-1"> 10000 +
                                        <span class="ml-1 mr-1"> {{ translate('Devotees') }} </span>
                                    </span>
                                    <span style="color:#00000;">
                                        {{ translate('have_participated_in_Chadava_conducted_by_mahakal.com_Chadava_Seva') }}
                                    </span>

                                    <div class="tray mb-3 ml-3 mt-2">
                                        @php
                                        $uniqueUsers = range(0, 13);
                                        shuffle($uniqueUsers);
                                        $selectedUsers = array_slice($uniqueUsers, 0, 5);
                                        @endphp
                                        @foreach ($selectedUsers as $random_user)
                                        <div class="relative circle-img-container">
                                            <div class="bg-cover bg-center flex-shrink-0 cursor-pointer border border-4 border-white rounded-full circle-img"
                                                style="background-image:url('{{ theme_asset(path: 'public/assets/user_list/user' . $random_user . '.jpg') }}')">
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <h2 class="h2 mt-2 text-dark font-bold" id="displayName">{{ $chadhava->name ?? 'ServiceName' }}
                    </h2>

                    <div class="flex flex-col">
                        <div class="flex items-center space-x-1 pt-2">
                            <div class="d-flex">
                                <img src="{{ theme_asset(path: 'public/assets/front-end/img/track-order/temple.png') }}"
                                    alt="Puja Venue" style="width:24px;height:24px;">
                                <p class="pooja-venue" style="color:#000;">
                                    {{ $chadhava->chadhava_venue }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center space-x-1 pt-2">
                            <div class="d-flex">
                                <img src="{{ theme_asset(path: 'public/assets/front-end/img/track-order/date.png') }}"
                                    alt="Booking Date" style="width:24px;height:24px;">
                                <p class="pooja-calendar" style="color:#000;">
                                    {{ date('d F, l', strtotime($nextDate)) }}
                                </p>
                            </div>
                        </div>
                    </div>

                </div>
            </section>
            {{-- Product List show --}}
            <section class="container mb-5">
                <div class="row g-3" id="productList">
                    @php
                    $productIds = json_decode($chadhava->product_id, true) ?? [];
                    $products = \App\Models\Product::whereIn('id', $productIds)
                    ->where('status', 1)
                    ->get()
                    ->keyBy('id');
                    @endphp

                    @if (!empty($productIds) && count($productIds) > 0)
                    <!-- Product Scrollable Container -->
                    <div style="border:1px solid #eee; border-radius:8px; overflow:hidden;">
                        <!-- Fixed Heading -->
                        <div
                            style="padding:10px 12px; border-bottom:1px solid #ddd; position:sticky; top:0; z-index:10;">
                            <h5 class="mb-0" style="font-size:16px; font-weight:600;">
                                {{ translate('Offer Your Devotion') }}
                            </h5>
                        </div>
                        <!-- Scrollable Products -->
                        <div id="product-container" style="max-height: 400px; overflow-y: auto; padding:8px;">
                            @foreach ($productIds as $pid)
                            @php $product = $products[$pid] ?? null; @endphp
                            @if ($product)
                            <div class="item col-12 mb-2">
                                <div class="product-card border rounded d-flex align-items-center justify-content-between p-2"
                                    data-id="{{ $product->id }}" data-name="{{ $product->name }}"
                                    data-price="{{ $product->unit_price ?? 0 }}"
                                    data-image="{{ getValidImage(path: 'storage/app/public/product/thumbnail/' . $product->image, type: 'product') ?? asset('default-image.png') }}"
                                    style="box-shadow: 0 2px 6px rgba(0,0,0,0.08); transition: all 0.2s ease-in-out;">
                                    <!-- Left: Image -->
                                    <div class="d-flex align-items-center me-2"
                                        style="width:50px; height:50px;">
                                        <img src="{{ getValidImage(path: 'storage/app/public/product/thumbnail/' . $product['thumbnail'], type: 'product') ?? asset('default-image.png') }}"
                                            alt="{{ $product->name }}" class="rounded"
                                            style="max-height:100%; max-width:100%; object-fit:contain;">
                                    </div>

                                    <!-- Middle: Name + Price -->
                                    <div class="flex-grow-1 text-start">
                                        <h6 class="mb-0 product-name text-truncate"
                                            style="font-size: 14px;">
                                            {{ $product->name }}
                                        </h6>
                                        <small class="text-muted">₹{{ $product->unit_price ?? 0 }}</small>
                                    </div>

                                    <!-- Right: Add button / Counter -->
                                    <div class="ms-2 text-end">
                                        <button
                                            class="btn btn-outline-primary btn-sm btn-add-slide d-none">Add</button>
                                        <!-- Quantity counter (hidden initially) -->
                                        <div class="quantity-counter d-none d-flex align-items-center">
                                            <button
                                                class="btn btn-sm btn-outline-danger btn-minus">-</button>
                                            <span class="mx-2 quantity">1</span>
                                            <button
                                                class="btn btn-sm btn-outline-success btn-plus">+</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                            @endforeach
                        </div>
                    </div>

                    @endif

                </div>
            </section>

        </main>
    </div>
    <!-- Sticky Footer -->
    {{-- <div class="sticky-bar py-3"
        style="background: url('{{ asset('public/assets/front-end/img/bg-footer.jpg') }}') no-repeat center center/cover;"> --}}
    <div class="sticky-bar py-2">
        <div class="container">

            <div class="d-flex justify-content-between align-items-center border-top p-2">
                <!-- Left -->
                <div class="d-flex align-items-center">
                    <i class="fa fa-info-circle me-2" style="cursor:pointer; font-size:18px;" onclick="toggleItemList()"></i>
                    <span id="footerItemCount">Select Your Chadhava Product</span>
                </div>

                <!-- Right -->
                <strong id="footerTotal" class="text-end"></strong>
            </div>

            <!-- Expandable item list -->
            <div id="footerItemList" class="p-2" style="display:none; font-size:14px;"></div>

            <button class="btn px-3 w-100 mt-2"
                id="btnEditInfo" style="background-color:#FF6F00; border-color:#FF6F00;" disabled>
                Proceed
            </button>
        </div>
    </div>

    <div id="fullPageLoader"
        style="position: fixed; top:0; left:0; width:100%; height:100%;
            background: rgba(0,0,0,0.4);
            backdrop-filter: blur(1px);     
            -webkit-backdrop-filter: blur(1px);
            z-index: 999999;
            display:none;
            align-items:center;
            justify-content:center;
            flex-direction: column;">

        <div class="spinner-border text-light" style="width: 4rem; height: 4rem;"></div>
    </div>

    <!-- Details Modal -->
    <div class="modal fade" id="detailsModal" tabindex="-1" aria-hidden="true"
        data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content" style="border-top: 2px solid var(--base) !important;/">
                <div class="modal-header">
                    <span class="text-18 font-bold mr-2">
                        {{ translate('Fill_your_details_for_chadhava') }}</span>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form id="detailsForm" method="POST" novalidate
                    action="{{ route('chadhavaleadStore', $chadhava->slug) }}">
                    @csrf
                    @php
                    if (auth('customer')->check()) {
                    $customer = App\Models\User::where('id', auth('customer')->id())->first();
                    }
                    @endphp

                    <input type="hidden" name="service_id" value="{{ $chadhava->id }}">
                    <input type="hidden" name="booking_date" id="bookingDate"
                        value="{{ date('Y-m-d', strtotime($nextDate)) ?? '' }}" placeholder="Booking Date"
                        class="">
                    <input type="hidden" name="add_product_id" id="add_product_id">
                    <input type="hidden" id="total_amount" name="final_amount">
                    <div class="modal-body">
                        <span class="block text-16 font-bold text-gray-900 text-dark">
                            {{ translate('Enter Your WhatsApp Mobile Number') }}
                        </span>
                        <span class="text-[12px] font-normal text-[#707070]">
                            {{ translate('Your Chahdava booking updates will be sent on the below WhatsApp number') }}
                        </span>

                        <!-- Phone -->
                        <div class="mb-3">
                            <div class="form-group">
                                <label class="form-label font-semibold">
                                    {{ translate('Phone Number') }}
                                    <small class="text-primary">( *
                                        {{ translate('Country code is must like for IND') }} 91 )</small>
                                </label>
                                <input class="form-control phone-input-with-country-picker" type="tel"
                                    value="{{ isset($customer['phone']) ? $customer['phone'] : '' }}"
                                    name="person_phone" id="person-number"
                                    placeholder="{{ translate('Phone Number') }}" inputmode="numeric" required
                                    maxlength="10" minlength="10" {{ isset($customer['phone']) ? 'readonly' : '' }}>

                                <p id="number-validation" class="text-danger d-none">
                                    Enter a valid 10-digit Mobile Number
                                </p>
                            </div>
                        </div>

                        <!-- Name -->
                        <div class="mb-3">
                            <div class="form-group">
                                <label class="form-label font-semibold">{{ translate('Your Name') }}</label>
                                <input class="form-control"
                                    value="{{ !empty($customer['f_name']) ? $customer['f_name'] : '' }}{{ !empty($customer['l_name']) ? $customer['l_name'] : '' }}"
                                    type="text" name="person_name" id="person-name"
                                    placeholder="{{ translate('Ex') }}: {{ translate('Your Name') }}" required
                                    {{ isset($customer['f_name']) ? 'readonly' : '' }}>

                                <p id="name-validation" class="text-danger d-none">
                                    Enter Your Name
                                </p>
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="submit" id="bookNowBtn"
                            class="btn btn-primary btn-block btn-shadow mt-1 font-weight-bold w-100">
                            {{ translate('Book Now') }}
                        </button>
                    </div>
                    <div class="alert alert-warning mt-2" role="alert" style="font-size:14px;">
                        <i class="fas fa-bell"></i>
                        <b>Name</b> & <b>Mobile</b> will be used in the announcement. Please check before booking.
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Confirm Modal -->
    <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ translate('Confirm Your Details') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @if ($digital_payment['status'] == 1)
                    @foreach ($payment_gateways_list as $payment_gateway)
                    <form method="post" class="digital_payment chadhava-pending-form"
                        id="{{ $payment_gateway->key_name }}_form"
                        action="{{ route('chadhavapaymentRequest') }}">
                        @csrf

                        <div class="Details">
                            <input type="hidden" name="payment_method"
                                value="{{ $payment_gateway->key_name }}">
                            <input type="hidden" name="payment_platform" value="web">
                            @if ($payment_gateway->mode == 'live' && isset($payment_gateway->live_values['callback_url']))
                            <input type="hidden" name="callback"
                                value="{{ $payment_gateway->live_values['callback_url'] }}">
                            @elseif($payment_gateway->mode == 'test' && isset($payment_gateway->test_values['callback_url']))
                            <input type="hidden" name="callback"
                                value="{{ $payment_gateway->test_values['callback_url'] }}">
                            @else
                            <input type="hidden" name="callback" value="">
                            @endif
                            <input type="hidden" name="external_redirect_link"
                                value="{{ route('chadhava-pending-web-payment') }}">
                            <label class="d-flex align-items-center gap-2 mb-0 form-check py-2 cursor-pointer">
                                <input type="radio" id="{{ $payment_gateway->key_name }}"
                                    name="online_payment" class="form-check-input custom-radio"
                                    value="{{ $payment_gateway->key_name }}" hidden>
                                <img width="30"
                                    src="{{ dynamicStorage(path: 'storage/app/public/payment_modules/gateway_image') }}/{{ $payment_gateway->additional_data && json_decode($payment_gateway->additional_data)->gateway_image != null ? json_decode($payment_gateway->additional_data)->gateway_image : '' }}"
                                    alt="" hidden>
                                <span class="text-capitalize form-check-label" hidden>
                                    @if ($payment_gateway->additional_data && json_decode($payment_gateway->additional_data)->gateway_title != null)
                                    {{ json_decode($payment_gateway->additional_data)->gateway_title }}
                                    @else
                                    {{ str_replace('_', ' ', $payment_gateway->key_name) }}
                                    @endif
                                </span>
                            </label>
                            <input type="hidden" name="order_id" id="pending-order-id" class="orderId"
                                value="">
                            <input type="hidden" name="leads_id" id="pending-lead-id" class="orderId"
                                value="">

                        </div>

                    </form>
                    @endforeach
                    @endif
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <!-- Header -->
                            <h5 class="text-center mb-3"><i class="fas fa-receipt"></i>
                                {{ translate('Booking Receipt') }}
                            </h5>
                            <div class="row">
                                <!-- Service Details -->
                                <div class="mb-3 d-flex flex-column gap-1">
                                    <div class="d-flex justify-content-between">
                                        <p class="mb-0"><span id="cChadhava">—</span></p>
                                        <p class="mb-0"><strong>{{ translate('Order ID') }}:</strong> <span
                                                id="cOrderId">—</span></p>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <p class="mb-0"><span id="cVenue">—</span></p>
                                        <p class="mb-0"><strong>{{ translate('Booking Date') }}:</strong> <span
                                                id="cDate">—</span>
                                        </p>
                                    </div>
                                </div>

                                <hr>

                                <!-- Customer Details -->
                                <h6 class="mb-2"><i class="fas fa-user"></i> {{ translate('Customer Details') }}
                                </h6>
                                <div class="d-flex justify-content-between">
                                    <p class="mb-0"><strong>{{ translate('Name') }}:</strong> <span
                                            id="cName">—</span></p>
                                    <p class="mb-0"><strong>{{ translate('Mobile') }}:</strong> <span
                                            id="cMobile">—</span></p>
                                </div>
                            </div>

                            <hr>

                            <!-- Product List -->
                            <h6 class="mb-2"><i class="fas fa-shopping-cart"></i>{{ translate(' Your Devotion List') }}</h6>
                            <table class="table table-sm table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ translate('Item') }}</th>
                                        <th style="width:70px;">{{ translate('Qty') }}</th>
                                        <th style="width:90px;">{{ translate('Price') }}</th>
                                    </tr>
                                </thead>
                                <tbody id="cProducts"></tbody>
                            </table>

                            <!-- Amount -->
                            <div class="text-end mt-2">
                                <h6><strong>{{ translate('Total Amount') }}:</strong><span id="cAmount">—</span>
                                </h6>
                            </div>

                            <hr>

                            <!-- Footer Message -->
                            <p class="text-center text-danger fw-bold">
                                {{ translate('Note Confirmed & Payment Pending') }}
                            </p>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">{{ translate('Cancel') }}</button>
                        <button class="btn btn-primary" type="button"
                            id="finalSubmit">{{ translate('Proceed to Payment') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-top: 2px solid var(--base) !important;/">
                <div class="display-6 mb-3"></div>
                <h5>पेमेंट सफल!</h5>
                <p>आपकी बुकिंग कन्फर्म हो गई है।</p>
                <button class="btn btn-warning" data-bs-dismiss="modal">ठीक है</button>
            </div>
        </div>
    </div>
    <input type="hidden" id="fullDate" value="{{ date('Y-m-d', strtotime($nextDate . ' -1 day')) }}">
    <input type="hidden" id="fullTime" value="23:59:59">
    <!-- JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/owl.carousel.min.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/js/intlTelInput.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/country-picker-init.js') }}"></script>
    <script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>

    <script>
        $(document).ready(function() {
            // General Carousel (banners/testimonials etc.)
            $(".owl-carousel").owlCarousel({
                loop: true,
                autoplay: true,
                autoplayTimeout: 3000,
                dots: true,
                nav: false,
                margin: 15,
                responsive: {
                    0: {
                        items: 1 // mobile
                    },
                    600: {
                        items: 1 // tablet portrait
                    },
                    992: {
                        items: 1 // tablet landscape / small laptop
                    },
                    1200: {
                        items: 2 // large desktop
                    }
                }
            });
        });

        // =========================
        // Global Variables
        // =========================
        let selectedProducts = {};
        const detailsModal = new bootstrap.Modal('#detailsModal');
        const confirmModal = new bootstrap.Modal('#confirmModal');


        // =========================
        // Footer Proceed Btn
        // =========================
        document.getElementById('btnEditInfo').addEventListener('click', () => detailsModal.show());
        // =========================
        // Save Details Form
        // =========================
        document.getElementById('detailsForm').addEventListener('submit', function(e) {
            e.preventDefault();

            let name = document.getElementById('person-name').value.trim();
            let mobile = document.getElementById('person-number').value.trim();

            // Validation
            let valid = true;
            if (!/^\d{10}$/.test(mobile)) {
                document.getElementById('number-validation').classList.remove('d-none');
                valid = false;
            } else {
                document.getElementById('number-validation').classList.add('d-none');
            }

            if (name.length < 2) {
                document.getElementById('name-validation').classList.remove('d-none');
                valid = false;
            } else {
                document.getElementById('name-validation').classList.add('d-none');
            }

            if (!valid) return;

            let bookBtn = $('#bookNowBtn');
            bookBtn.text('Please Wait ...').prop('disabled', true);

            let formData = new FormData(this);

            fetch(this.action, {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value
                    },
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        let d = data.data;

                        // Fallback values
                        document.getElementById('cChadhava').textContent = d.chadhava_name || "—";
                        document.getElementById('cVenue').textContent = d.chadhava_venue || "—";
                        document.getElementById('cMobile').textContent = d.person_phone || "—";
                        document.getElementById('cDate').textContent = d.booking_date || "—";
                        document.getElementById('cName').textContent = d.person_name || "—";
                        document.getElementById('cOrderId').textContent = d.order_id || "—";
                        document.getElementById('cAmount').textContent = d.total_amount ? "₹" + d.total_amount :
                            "—";

                        // Products
                        if (Array.isArray(d.products) && d.products.length > 0) {
                            let productRows = "";
                            d.products.forEach(p => {
                                productRows += `
                                    <tr>
                                        <td>${p.name || "—"}</td>
                                        <td>${p.qty || 0}</td>
                                        <td>${p.price ? "₹" + (p.price * (p.qty || 0)).toLocaleString() : "—"}</td>
                                    </tr>`;
                            });
                            document.getElementById("cProducts").innerHTML = productRows;
                        } else {
                            document.getElementById("cProducts").innerHTML = `
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No products added</td>
                                </tr>`;
                        }

                        // Final submit button data
                        const finalBtn = document.getElementById('finalSubmit');
                        finalBtn.dataset.orderid = d.order_id || "";
                        finalBtn.dataset.amount = d.total_amount || 0;
                        finalBtn.dataset.leadid = d.lead_id || "";

                        // Hidden fields
                        $("#pending-order-id").val(d.order_id || "");
                        $("#pending-lead-id").val(d.lead_id || "");

                        // Show modal
                        confirmModal.show();
                        detailsModal.hide();
                    } else {
                        alert("Error saving booking");
                        bookBtn.text('Book Now').prop('disabled', false);
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert("Something went wrong. Please try again.");
                    bookBtn.text('Book Now').prop('disabled', false);
                });
        });


        // =========================
        // Final Submit
        // =========================
        document.getElementById('finalSubmit').addEventListener('click', function() {
            if (!this.dataset.orderid || !this.dataset.amount) return;
            $('#pending-order-id').val(this.dataset.orderid);
            $("#pending-lead-id").val(this.dataset.leadid);
            $('.chadhava-pending-form').submit();
        });
        $('#confirmModal').on('hidden.bs.modal', function() {
            $('#bookNowBtn').text('Book Now');
            $('#bookNowBtn').prop('disabled', false);
        });
        // =========================
        // jQuery Ready
        // =========================


        $(document).ready(function() {
            let selectedProducts = {};
            let defaultsApplied = false; 
            // Apply default selection on first page load only
            if (!defaultsApplied) {
                $(".product-card").each(function() {
                    const card = $(this);
                    const id = card.data('id');
                    const name = card.data('name');
                    const price = parseFloat(card.data('price')) || 0;

                    selectedProducts[id] = {
                        name,
                        price,
                        qty: 1
                    };

                    card.find(".btn-add-slide").addClass("d-none");
                    card.find(".quantity-counter").removeClass("d-none");
                    card.find(".quantity").text("1");
                });

                updateFooter();
                defaultsApplied = true;
            }
            // Add Button (manual click)
            $(document).on("click", ".btn-add-slide", function() {
                const card = $(this).closest(".product-card");
                const id = card.data('id');
                const name = card.data('name');
                const price = parseFloat(card.data('price')) || 0;

                // ONLY add this product
                selectedProducts[id] = {
                    name,
                    price,
                    qty: 1
                };

                card.find(".btn-add-slide").addClass("d-none");
                card.find(".quantity-counter").removeClass("d-none");
                card.find(".quantity").text("1");

                updateFooter();
            });

            // Minus / Plus buttons
            $(document).on("click", ".btn-minus", function() {
                const card = $(this).closest(".product-card");
                const id = card.data('id');

                if (!selectedProducts[id]) return;

                selectedProducts[id].qty--;
                if (selectedProducts[id].qty <= 0) {
                    delete selectedProducts[id];
                    card.find(".btn-add-slide").removeClass("d-none");
                    card.find(".quantity-counter").addClass("d-none");
                    card.find(".quantity").text("0");
                } else {
                    card.find(".quantity").text(selectedProducts[id].qty);
                }

                updateFooter();
            });

            $(document).on("click", ".btn-plus", function() {
                const card = $(this).closest(".product-card");
                const id = card.data('id');

                if (!selectedProducts[id]) return;

                selectedProducts[id].qty++;
                card.find(".quantity").text(selectedProducts[id].qty);

                updateFooter();
            });

            // Cancel / modal hide
            $('#detailsModal').on('hidden.bs.modal', function() {
                // Clear all selections
                selectedProducts = {};

                // Reset product cards UI
                $('.product-card').each(function() {
                    $(this).find(".btn-add-slide").removeClass("d-none");
                    $(this).find(".quantity-counter").addClass("d-none");
                    $(this).find(".quantity").text("0");
                });

                // Reset footer
                $('#footerItemList').empty();
                $('#footerTotal').text('');
                $('#footerItemCount').text('Select Your Chadhava Product');

                // Reset hidden form fields
                $('#add_product_id').val('');
                $('#total_amount').val($('#package_price').val());
                $('#bookNowBtn').html("Book Now").prop("disabled", false);

                defaultsApplied = true;
            });

            // Update footer function
            function updateFooter() {
                let total = 0;
                let totalItems = 0;
                let listHtml = "";
                let products = [];

                Object.entries(selectedProducts).forEach(([id, p]) => {
                    totalItems += p.qty;
                    const itemTotal = p.price * p.qty;
                    total += itemTotal;

                    listHtml += `
                <div class="d-flex justify-content-between border-bottom py-1">
                    <span>${p.name} x${p.qty}</span>
                    <span>₹${itemTotal}</span>
                </div>
            `;

                    products.push({
                        product_id: id,
                        price: p.price,
                        qty: p.qty
                    });
                });

                $("#footerItemList").html(listHtml);
                $("#footerItemCount").text(totalItems > 0 ? `${totalItems} item${totalItems>1?'s':''}` : "Select Your Chadhava Product");
                $("#footerTotal").text(totalItems > 0 ? `₹${total}` : "");
                $("#add_product_id").val(JSON.stringify(products));
                $("#total_amount").val(total);
                $("#btnEditInfo").prop("disabled", totalItems === 0);
                updateConfirmModal();
            }

            function updateConfirmModal() {
                let listHtml = '';
                Object.entries(selectedProducts).forEach(([id, p]) => {
                    const itemTotal = p.price * p.qty;
                    listHtml += `
                <tr>
                    <td>${p.name}</td>
                    <td>${p.qty}</td>
                    <td>₹${itemTotal}</td>
                </tr>
            `;
                });
                $('#cProducts').html(listHtml);
            }
        });

        // Expand/Collapse
        function toggleItemList() {
            const box = document.getElementById("footerItemList");
            box.style.display = (box.style.display === "none") ? "block" : "none";
        }

        // // //  Disable Right Click
        // document.addEventListener("contextmenu", function(e) {
        //     e.preventDefault();
        // });

        // //  Disable F12, Ctrl+Shift+I, Ctrl+U
        // document.onkeydown = function(e) {
        //     if (e.keyCode == 123) {
        //         return false;
        //     } // F12
        //     if (e.ctrlKey && e.shiftKey && e.keyCode == 73) {
        //         return false;
        //     } // Ctrl+Shift+I
        //     if (e.ctrlKey && e.shiftKey && e.keyCode == 74) {
        //         return false;
        //     } // Ctrl+Shift+J
        //     if (e.ctrlKey && e.keyCode == 85) {
        //         return false;
        //     } // Ctrl+U
        // };

        // //  Inspect Detection (Debugger Trick)
        // setInterval(function() {
        //     function detectDevTool() {
        //         const start = performance.now();
        //         debugger;
        //         return performance.now() - start > 100;
        //     }
        //     if (detectDevTool()) {
        //         alert("⚠️ Developer Tools Disabled!");
        //         window.location.href = "about:blank"; // Page blank ho jayega
        //     }
        // }, 1000);
    </script>
    <script>
        $(document).ready(function() {
            var dateGet = $('#fullDate').val();
            var timeGet = $('#fullTime').val();

            const targetTime = new Date(dateGet + 'T' + timeGet).getTime();

            if (isNaN(targetTime)) {
                console.error("Invalid countdown date/time:", dateGet, timeGet);
                return;
            }

            const countdown = setInterval(() => {
                const now = new Date().getTime();
                const diff = targetTime - now;

                if (diff <= 0) {
                    clearInterval(countdown);
                    $(".days, .hours, .minutes, .seconds").text("00");
                    return;
                }

                const totalSeconds = Math.floor(diff / 1000);
                const days = Math.floor(totalSeconds / (60 * 60 * 24));
                const hours = Math.floor((totalSeconds % (60 * 60 * 24)) / (60 * 60));
                const minutes = Math.floor((totalSeconds % (60 * 60)) / 60);
                const seconds = totalSeconds % 60;

                $(".days").text(days.toString().padStart(2, '0'));
                $(".hours").text(hours.toString().padStart(2, '0'));
                $(".minutes").text(minutes.toString().padStart(2, '0'));
                $(".seconds").text(seconds.toString().padStart(2, '0'));
            }, 1000);
        });
    </script>

    {{-- mobile no blur --}}
    <script>
        $('#person-number').blur(function(e) {
            e.preventDefault();
            var code = $('.iti__selected-dial-code').text();
            var mobile = $(this).val();
            var no = code + '' + mobile;

            $.ajax({
                type: "get",
                url: "{{ url('account-service-order-user-name') }}" + "/" + no,
                success: function(response) {
                    if (response.status == 200) {
                        var name = response.user.f_name + ' ' + response.user.l_name;
                        $('#person-name').val(name);
                    } else {
                        $('#send-otp-btn').addClass('d-none');
                        $('#withoutOTP').removeClass('d-none');
                    }
                }
            });
        });
    </script>

    <script>
        $('#detailsModal').on('hidden.bs.modal', function() {
            window.selectedProducts = {};

            // Reset product cards
            $('.product-card').each(function() {
                $(this).find('.btn-add-slide').removeClass('d-none');
                $(this).find('.quantity-counter').addClass('d-none');
                $(this).find('.quantity').text('0');
            });

            $('#add_product_id').val('');
            $('#total_amount').val($('#package_price').val());
            $('#bookNowBtn').html("Book Now").prop("disabled", false);

            $('#selectedProducts,#footerProducts, #footerItemList').empty();
            $('#footerTotal').text('');
            $('#footerItemCount').text('');
            $('#footerItemCount').text('Select Your Chadhava Product');

            isPageLoad = false;
        });
    </script>

    <script>
        $(document).ready(function() {
            $("#bookNowBtn").on("click", function() {
                $("#fullPageLoader")
                    .fadeIn(10)
                    .css("display", "flex");
            });
            $('#confirmModal').on('shown.bs.modal', function() {
                $("#fullPageLoader").fadeOut(10);
            });

        });
    </script>
</body>

</html>