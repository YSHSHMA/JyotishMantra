<!DOCTYPE html>
<html lang="hi">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1, maximum-scale=1.0, minimum-scale=1.0">
    <title>Mahakal • Booking Form</title>
    <!-- Styles -->
    <link rel="stylesheet" media="screen"
    href="{{ theme_asset(path: 'public/assets/front-end/vendor/simplebar/dist/simplebar.min.css') }}">
    <link rel="stylesheet" media="screen"
        href="{{ theme_asset(path: 'public/assets/front-end/vendor/tiny-slider/dist/tiny-slider.css') }}">
    <link rel="stylesheet" media="screen"
        href="{{ theme_asset(path: 'public/assets/front-end/vendor/drift-zoom/dist/drift-basic.min.css') }}">
    <link rel="stylesheet" media="screen"
        href="{{ theme_asset(path: 'public/assets/front-end/vendor/lightgallery.js/dist/css/lightgallery.min.css') }}">
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/roboto-font.css') }}">
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/poojafilter/bootstrapnew.min.css') }}">
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/home.css') }}">
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/poojafilter/layout.css') }}">
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/animationbutton.css') }}">
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/back-end/vendor/icon-set/style.css') }}">
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/css/lightbox.css') }}">
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/master.css') }}" />
    
    <link rel="stylesheet" media="screen" href="{{ theme_asset(path: 'public/assets/front-end/css/theme.css') }}">
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/custom.css') }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" media="screen" href="{{ theme_asset(path: 'public/assets/front-end/css/slick.css') }}">
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/back-end/css/toastr.css') }}" />
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/responsive1.css') }}" />
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/owl.carousel.min.css') }}">

    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/style.css?v=2') }}">

    <style>
      :root {
          --base: {{ $web_config['primary_color'] }};
          --base-2: {{ $web_config['secondary_color'] }};
          --web-primary: {{ $web_config['primary_color'] }};
          --web-primary-10: {{ $web_config['primary_color'] }}10;
          --web-primary-20: {{ $web_config['primary_color'] }}20;
          --web-primary-40: {{ $web_config['primary_color'] }}40;
          --web-secondary: {{ $web_config['secondary_color'] }};
          --web-direction: {{ Session::get('direction') }};
          --text-align-direction: {{ Session::get('direction') === 'rtl' ? 'right' : 'left' }};
          --text-align-direction-alt: {{ Session::get('direction') === 'rtl' ? 'left' : 'right' }};
      }

      .dropdown-menu {
          margin-{{ Session::get('direction') === 'rtl' ? 'right' : 'left' }}: -8px !important;
      }

      @media (max-width: 767px) {
          .navbar-expand-md .dropdown-menu>.dropdown>.dropdown-toggle {
              padding-{{ Session::get('direction') === 'rtl' ? 'left' : 'right' }}: 1.95rem;
          }

          .mobi-sty {
              display: contents;
          }
      }
      .modal-quick-view{
        z-index: 10000;
      }
  </style>

    <style>
        /* HEADER BASE DESIGN */
        .main-header {
            width: 100%;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 9999;
            background: #ffffff;
            padding: 10px 12px;
            border-bottom: 1px solid #e5e5e5;
        }

        /* FLEX WRAPPER */
        .header-wrapper {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        /* LEFT SIDE: GURU TITLE */
        .guru-title {
            display: flex;
            flex-direction: column;
            margin: 0;
        }

        .guru-title .small-text {
            font-size: 12px;
            color: #444;
            margin-bottom: -3px;
        }

        .guru-title h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
            color: #000;
        }

        /* RIGHT SIDE: LOGO */
        .powered-logo {
            text-align: right;
        }

        .powered-logo .powered-text {
            font-size: 11px;
            color: #666;
            display: block;
            margin-bottom: -2px;
        }

        .site-logo {
            height: 28px;
            width: auto;
            object-fit: contain;
        }

        /* PAGE CONTENT ADJUSTMENT */
        .page-wrapper {
            padding-top: 85px !important;
        }

        /* MOBILE DESIGN */
        @media (max-width: 576px) {
            .guru-title h3 {
                font-size: 16px;
            }

            .site-logo {
                height: 24px;
            }
        }

        /* ---------------- FOOTER ---------------- */
        .name-puja {
            font-size: 20px !important;
        }

        /* Body ko pura height cover karne ke liye */
        html,
        body {
            height: 100%;
            margin: 0;
        }

        /* Page ka main wrapper */
        .page-wrapper {
            min-height: 100%;
            display: flex;
            flex-direction: column;
        }

        /* Content ko expand karne ke liye */
        .content {
            flex: 1;
        }

        /* Footer styling */
        /* footer {
            background: #000;
            color: #fff;
            padding: 20px 10px;
            text-align: center;
        }

        footer a {
            color: #ffdd55;
            font-weight: 600;
            text-decoration: none;
        } */

        /* Instagram-style Pandit Header */
        .pandit-profile-box {
            display: flex;
            align-items: center;
            background: #fff;
            padding: 18px;
            border-radius: 18px;
            margin-bottom: 25px;
            gap: 20px;
            border: 1px solid #eee;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .pandit-dp {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #ff8800;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.12);
        }

        .pandit-profile-right {
            flex: 1;
        }

        .profile-top-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .pandit-name {
            font-size: 24px;
            font-weight: 700;
            color: #222;
        }

        .follow-btn {
            background: #ff5722;
            color: #fff;
            border: none;
            padding: 6px 16px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            font-size: 14px;
        }

        .profile-stats {
            margin: 6px 0;
            font-size: 15px;
            color: #444;
            display: flex;
            gap: 10px;
        }

        .short-bio {
            font-size: 14px;
            color: #555;
            margin-bottom: 10px;
        }

        .temple-info {
            font-size: 14px;
            color: #333;
            line-height: 20px;
        }

        /* Mobile View */
        @media(max-width: 768px) {
            .pandit-profile-box {
                flex-direction: row;
                text-align: center;
                padding: 8px 8px;
            }

            .profile-top-row {
                flex-direction: column;
                gap: 10px;
            }

            .profile-stats {
                justify-content: center;
            }
        }

        /* Verified Badge */
        .verified-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #ff5722;
            color: #fff;
            font-size: 12px;
            font-weight: bold;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            margin-left: 6px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        }

        /* Cards styling */
        .card {
            border-radius: 12px;
            transition: transform .2s ease-in-out;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .puja-image {
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }

        .two-lines-only {
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
        }

        .venue {
            font-size: 14px;
            max-width: 180px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Floating Panel button */
        .floating-view-btn {
            position: fixed;
            bottom: 80px;
            right: 15px;
            width: 50px;
            height: 50px;
            background: #0d6efd;
            border-radius: 50%;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .floating-toggle-panel {
            position: fixed;
            bottom: 140px;
            right: 15px;
            width: 150px;
            background: #fff;
            border-radius: 12px;
            padding: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.25);
            z-index: 9999;
            display: none;
        }

        /* Floating button */
        .view-toggle-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 55px;
            height: 55px;
            background: #000;
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            z-index: 9999;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.25);
        }

        /* List View Styles */
        .list-view .service-card {
            flex-direction: row !important;
            display: flex;
        }

        .list-view .service-card .card {
            display: flex;
            flex-direction: row;
            height: auto;
        }

        .list-view .service-card img {
            width: 140px !important;
            height: 140px !important;
            object-fit: cover;
        }

        .list-view .card-body {
            padding-left: 15px !important;
        }

        /* Hide floating button on desktop */
        @media(min-width: 768px) {
            .view-toggle-btn {
                display: none;
            }
        }

        .pooja-badge {
            top: 1rem !important;
            background-color: #FF7722 !important;
        }

        .for-discount-value {
            background: #FF7722;
            position: absolute;
            top: 8px;
            inset-inline-start: 8px;
            z-index: 3;
            border-radius: 4px !important;
            white-space: nowrap;
        }

        .blink {
            text-decoration: blink;
            -webkit-animation-name: blinker;
            -webkit-animation-duration: 0.6s;
            -webkit-animation-iteration-count: infinite;
            -webkit-animation-timing-function:
                ease-in-out;
            -webkit-animation-direction: alternate;
        }

        /* Tabs */
        /* Tabs Main Wrapper */
        .service-tabs-wrapper {
            background: #fff;
            z-index: 999;
        }

        /* Desktop Sticky */
        .service-tabs-wrapper.sticky {
            position: fixed;
            /* top: 64px;   header height */
            left: 0;
            right: 0;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            border-top: 1px solid #ff6600;
        }

        /* Tabs Row */
        .service-tabs {
            display: flex;
            justify-content: space-around;
            padding: 1px 0;
            border-bottom: 1px solid #eee;
            background: #fff;
        }

        /* Each Tab */
        .tab-item {
            text-align: center;
            padding: 10px 0;
            font-weight: 600;
            font-size: 14px;
            flex: 1;
            cursor: pointer;
            color: #666;
        }

        .tab-item i {
            display: block;
            font-size: 20px;
            margin-bottom: 3px;
        }

        /* Active Tab */
        .tab-item.active {
            color: #000;
            border-bottom: 3px solid #ff6600;
        }

        /* ---------- Mobile Footer Mode ---------- */
        @media(max-width: 768px) {
            .service-tabs-wrapper {
                position: fixed !important;
                left: 0;
                right: 0;
                bottom: 0 !important;
                top: auto !important;
                background: #fff;
                border-top: 1px solid #eee;
                box-shadow: 0 -3px 10px rgba(0, 0, 0, 0.1);
                z-index: 9999;
            }

            .service-tabs {
                padding: 1px 0;
            }

            .tab-item {
                font-size: 12px;
                padding: 5px 0;
            }

            .tab-item i {
                font-size: 18px;
                margin-bottom: 0;
            }
        }

        @media(min-width: 769px) {
            .service-tabs-wrapper {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                background: #fff;
                border-top: 1px solid #eee;
                padding: 1px 0;
                box-shadow: 0 -3px 12px rgba(0, 0, 0, 0.15);
                z-index: 9999;
            }

            /* Ensure footer visible */
            body {
                padding-bottom: 80px;
                /* Tab bar ke liye jagah */
            }
        }

        .tab-item .icon {
            display: flex;
            justify-content: center;
            margin-bottom: 3px;
        }

        .tab-item svg {
            height: 22px;
            width: 22px;
        }

        .tab-item.active svg {
            stroke: #ff6600;
        }

        .tab-item span {
            font-size: 12px;
            font-weight: 600;
            display: block;
        }

        .product-horizontal-card {
            width: 100%;
            /* display: flex; */
            gap: 10px;
            background: #fff;
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 6px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.06);
        }

        .phc-image-box {
            /* width: 35%; */
            min-width: 120px;
        }

        .phc-image-box img {
            width: 100%;
            height: 120px;
            object-fit: cover;
            border-radius: 10px;
        }

        .phc-content-box {
            /* width: 65%; */
            display: flex;
            flex-direction: column;
        }

        .phc-title {
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 10px;
            line-height: 1.3;
        }

        .phc-desc {
            font-size: 13px;
            color: #777;
            margin-bottom: 6px;
        }

        .phc-rating {
            font-size: 16px;
            color: #ff9800;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .phc-rating .count {
            color: #333;
            font-size: 12px;
            margin-left: 4px;
        }

        .phc-price-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 8px;
        }

        /* Price */
        .phc-new-price {
            font-size: 18px;
            font-weight: 700;
            color: #ff6600;
        }

        .phc-old-price {
            text-decoration: line-through;
            font-size: 13px;
            color: #999;
        }

        /* Circular Cart Button */
        .phc-cart-btn {
            width: 38px;
            height: 38px;
            min-width: 38px;
            min-height: 38px;
            background: #ff6600;
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-size: 16px;
            transition: 0.2s ease-in-out;
        }

        .phc-cart-btn:hover {
            background: #e85600;
        }

        /* iNSTAGRAM */
        .insta-profile-box {
            border-radius: 16px;
            margin-bottom: 25px;
            /* gap: 20px; */
            width: 100%;
            /* max-width: 480px; */
            background: #fff;
            border: 1px solid #eee;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 20px;
            font-family: Arial, sans-serif;
            color: #000;
        }

        .profile-top {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .profile-pic {
            border: 3px solid #ff5722;
            position: relative;
            width: 90px;
            border-radius: 50px;
            height: 90px;
            bottom: 24px;
        }

        .profile-pic img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }

        .add-circle {
            position: absolute;
            right: 0;
            bottom: 0;
            width: 26px;
            height: 26px;
            background: #ff6412;
            color: #fff;
            font-size: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #ff5722;
        }

        .profile-name {
            margin: 0;
            font-size: 20px;
            font-weight: 600;
        }

        .stats-row {
            display: flex;
            margin-top: 10px;
            gap: 20px;
        }

        .stat-box {
            text-align: center;
        }

        .stat-number {
            display: block;
            font-size: 18px;
            font-weight: 700;
        }

        .stat-label {
            font-size: 13px;
            color: #555;
        }

        .bio {
            margin-top: 15px;
            line-height: 22px;
            font-size: 15px;
        }

        .profile-link {
            display: inline-block;
            margin-top: 10px;
            color: #00376b;
            text-decoration: none;
            font-weight: 600;
        }

        /* Produt bar */
        /* PRODUCT TOP BAR */
        .product-bar {
            display: none;
            /* initially hidden */
            position: sticky;
            top: 58px;
            /* right below tab wrapper */
            z-index: 999;
            background: #fff;
            padding: 10px 12px;
            border-bottom: 1px solid #eee;
        }

        .product-bar-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .prod-title {
            font-size: 18px;
            font-weight: 600;
        }

        .search-box {
            position: relative;
            flex: 1;
            display:block;
        }

        .search-box input {
            width: 100%;
            padding: 8px 12px 8px 36px;
            border-radius: 8px;
            border: 1px solid #ddd;
            font-size: 14px;
        }

        .search-box i {
            position: absolute;
            top: 50%;
            left: 12px;
            transform: translateY(-50%);
            color: #888;
            font-size: 14px;
        }

        .cart-btn {
            position: relative;
            background: #ff6600;
            color: #fff;
            border: none;
            padding: 8px 10px;
            border-radius: 10px;
            font-size: 18px;
        }

        .cart-btn i {
            font-size: 18px;
        }

        .cart-btn .badge {
            position: absolute;
            top: -6px;
            right: -6px;
            background: red;
            color: #fff;
            font-size: 11px;
            padding: 3px 5px;
            border-radius: 50%;
        }

        .view-btn {
            background: #fff;
            border: 1px solid #ddd;
            padding: 8px 12px;
            border-radius: 10px;
            margin-left: 8px;
            font-size: 18px;
            cursor: pointer;
            transition: .25s;
        }

        .view-btn.active {
            border-color: #ff6600;
            color: #ff6600;
            background: #fff7f0;
        }
        /* default sizes – override as needed */
        .chat-icon-svg, .call-icon-svg {
        display: inline-block;
        vertical-align: middle;
        transition: transform .18s ease, filter .18s ease;
        }

        /* change color via stroke */
        .chat-icon-svg path, .call-icon-svg path { stroke: #FF6F00; }

        /* hover effect */
        .chat-call-svg-wrapper:hover .chat-icon-svg { transform: translateY(-3px) scale(1.03); }
        .chat-call-svg-wrapper:hover .call-icon-svg { transform: translateY(-3px) scale(1.03); }

        /* make icons bigger on small screens if needed */
        @media (max-width: 576px) {
        .chat-icon-svg, .call-icon-svg { width: 30px; height: 30px; }
        }
        .service-card {
            margin-bottom: 15px; /* space between rows */
        }
        /* Hide these on screens smaller than 576px (mobile) */
      /* MOBILE VIEW – BUT NOT WHEN ITEM IS COL-12 */
    @media (max-width: 575.98px) {

    /* .col-12 list view → media query skip */
    .service-item:not(.col-12) .pooja-heading,
    .service-item:not(.col-12) .card-text {
        display: none !important;
    }

    .service-item:not(.col-12) .name-puja {
        font-size: 16px !important;
    }

    .service-item:not(.col-12) .ratings{
        font-size: 10px;
        display: inline !important;
    }

    .service-item:not(.col-12) .puja-image{
        height: 120px !important;
    }
    .bar-left{                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             
       display: none !important;
    }
    }


    </style>
</head>

<body>
    @php
        // Selected language from dropdown
        $langs = str_replace('_', '-', app()->getLocale()) == 'in' ? 'hi' : str_replace('_', '-', app()->getLocale());
        $lang = $data['code'] ?? getDefaultLanguage();
        // Alphabets
        $englishLetters = range('A', 'Z');
        $hindiLetters = [
            'अ',
            'आ',
            'इ',
            'ई',
            'उ',
            'ऊ',
            'ऋ',
            'ए',
            'ऐ',
            'ओ',
            'औ',
            'अं',
            'अः',
            'क',
            'ख',
            'ग',
            'घ',
            'ङ',
            'च',
            'छ',
            'ज',
            'झ',
            'ञ',
            'ट',
            'ठ',
            'ड',
            'ढ',
            'ण',
            'त',
            'थ',
            'द',
            'ध',
            'न',
            'प',
            'फ',
            'ब',
            'भ',
            'म',
            'य',
            'र',
            'ल',
            'व',
            'श',
            'ष',
            'स',
            'ह',
            'क्ष',
            'त्र',
            'ज्ञ',
        ];
        // Final alphabet based on current language
        $alphabet = $lang == 'in' ? $hindiLetters : $englishLetters;
        $ecommerceLogo = getWebConfig('company_web_logo');
        $realName = $realName = Str::slug($guruji->name, '-');
    @endphp
    <!-- Header -->
    <header class="main-header">
        <div class="header-wrapper">
            <div class="guru-title">
                <span class="small-text">Sacred Rituals by</span>
                <h3>{{ $guruji->name }}</h3>
            </div>
            <div class="powered-logo">
                <span class="powered-text">Powered by</span>
                <a href="{{ route('guruji.individual', ['name' => Str::slug($guruji->name)]) }}">
                    <img src="{{ getValidImage('storage/app/public/company/' . $ecommerceLogo, type: 'backend-logo') }}"
                        alt="Mahakal.com" class="site-logo">
                </a>
            </div>
        </div>
    </header>
    <div class="page-wrapper">
        <div class="container">
            <div class="insta-profile-box">
                <!-- Profile + Stats Row -->
                <div class="profile-top">
                    <div class="left-section">
                        <div class="profile-pic">
                            <img src="{{ $guruji->image }}" alt="profile">
                            <span class="add-circle">✓</span>
                        </div>
                    </div>
                    <div class="right-section">
                        <h2 class="profile-name">{{ $guruji->name }}</h2>
                        <div class="stats-row">
                            <div class="stat-box">
                                <span class="stat-number">{{ $guruji->experience }}+ Years</span>
                                <span class="stat-label">Experience</span>
                            </div>
                            <div class="stat-box">
                                <span class="stat-number">{{ $finalPujaCount }}+</span>
                                <span class="stat-label">Devotees</span>
                            </div>
                            <div class="stat-box">
                                <span class="stat-number">636</span>
                                <span class="stat-label">followers</span>
                            </div>
                        </div>
                        <!-- FOLLOW BUTTON -->
                        <button class="follow-btn" id="followBtn">Follow</button>
                    </div>
                </div>
                <!-- Bio -->
                <div class="bio">
                    {{ $guruji->bio }}
                </div>
            </div>
            <div class="service-tabs-wrapper">
                @include('web-views.guruji.partial.tabs')
            </div>

            <div class="row">
                <section class="col-lg-12 mt-4 pb-4" id="serviceListitem">
                    <div id="serviceTopBar" class="product-bar">
                        <div class="product-bar-inner">
                            <div class="bar-left">
                                <h4>Services</h4>
                            </div>
                            <div class="bar-center">
                                <div class="search-box">
                                    <i class="fa fa-search"></i>
                                    <input type="text" id="serviceSearch" placeholder="Search services...">
                                </div>
                            </div>
                            <div class="bar-right">
                            <button class="view-btn gridBtn">
                                <i class="fa fa-th-large"></i>
                            </button>

                            </div>
                        </div>
                    </div>
                    <div class="row g-3 mt-3">
                        <!-- Puja Start -->
                            @foreach ($services as $service)
                                @include('web-views.guruji.partial.puja-card', ['service' => $service, 'realName' => $realName])
                            @endforeach
                        <!-- Puja End -->
                    </div>

                </section>
                <!-- Consulltancy Start -->
                <section class="col-lg-12 mt-4 pb-4" id="consellingListitem">
                    <div id="consellingTopBar" class="product-bar" style="display:none;">
                        <div class="product-bar-inner">
                            <div class="bar-left">
                                <h4>Consulltancy</h4>
                            </div>
                            <div class="bar-center">
                                <div class="search-box">
                                    <i class="fa fa-search"></i>
                                    <input type="text" id="consulltancySearch" placeholder="Search Consulltancy...">
                                </div>
                            </div>
                            <div class="bar-right">
                                <button class="view-btn gridBtn">
                                    <i class="fa fa-th-large"></i>
                                </button>

                            </div>
                        </div>
                    </div>
                    <div class="row g-3 mt-3">
                       <!-- conselling Start -->
                        @if($conselling->count() > 0)
                            @foreach ($conselling->take(20) as $service)
                                @include('web-views.guruji.partial.conselling-card', [
                                    'conselling' => $service, 
                                    'realName' => $realName
                                ])
                            @endforeach
                        @else
                            {{-- NO EVENTS FOUND --}}
                            <div class="col-12">
                                <div class="text-center p-5 border rounded-3 bg-light">
                                    <img src="{{ getValidImage('storage/app/public/company/' . $ecommerceLogo, type: 'backend-logo') }}" class="mb-3">
                                    <h4>Upcoming conselling</h4>
                                    <p class="text-muted">Currently there are coming soon conselling.</p>
                                    <a href="{{ route('guruji.individual', [$realName]) }}" class="btn btn-primary mt-2">
                                        + Add New conselling
                                    </a>  
                                </div>
                            </div>
                        @endif
                        <!-- conselling End -->

                    </div>
                </section>
                <!-- Consulltancy End -->
                <!-- Chat / Call Start -->
                <section class="col-lg-12 mt-4 pb-4" id="chatcallListitem">
                    <div id="chatcallTopBar" class="product-bar" style="display:none;">
                        <div class="product-bar-inner">
                            <div class="bar-left">
                                <h4>Live Chat & Call</h4>
                            </div>
                        </div>
                    </div>
                    <div class="row g-3 mt-3">
                     {{-- NO EVENTS FOUND --}}
                        <div class="col-12">
                            <div class="text-center p-5 border rounded-3 bg-light">
                            <img src="{{ getValidImage('storage/app/public/company/' . $ecommerceLogo, type: 'backend-logo') }}"
                               class="mb-3">
                            <h4>Live Chat & Call with Astrologer</h4>
                            <p class="text-muted">Connect instantly for guidance & solutions</p>
                            <a href="https://mahakal.com/download" class="btn btn-primary mt-2"> Live Chat / Call the Astrologer
                            </a>  
                            </div>
                        </div>
                    </div>
                </section>
                <!-- Chat / Call End -->
                <!-- Event Start -->
                <section class="col-lg-12 mt-4 pb-4" id="eventListitem">
                    <div id="eventTopBar" class="product-bar" style="display:none;">
                        <div class="product-bar-inner">
                            <div class="bar-left">
                                <h4>Events</h4>
                            </div>
                            <div class="bar-center">
                                <div class="search-box">
                                    <i class="fa fa-search"></i>
                                    <input type="text" id="eventSearch" placeholder="Search events...">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row g-3 mt-3">
                     {{-- NO EVENTS FOUND --}}
                        <div class="col-12">
                            <div class="text-center p-5 border rounded-3 bg-light">
                            <img src="{{ getValidImage('storage/app/public/company/' . $ecommerceLogo, type: 'backend-logo') }}"
                               class="mb-3">
                            <h4>upcoming events.</h4>
                            <p class="text-muted">Currently there are Comming Soon Events.</p>
                            <a href="{{ route('guruji.individual',[$realName]) }}" class="btn btn-primary mt-2"> + Add New Event
                            </a>  
                            </div>
                        </div>
                    </div>
                </section>
                <!-- Event End -->
                <!-- Products Start -->
                <section class="col-lg-12 mt-4 pb-4" id="productListitem">
                    <div id="productTopBar" class="product-bar" style="display:none;">
                        <div class="product-bar-inner">
                            <div class="bar-left">
                                <h4>Products</h4>
                            </div>
                            <div class="bar-center">
                                <div class="search-box">
                                    <i class="fa fa-search"></i>
                                    <input type="text" id="productSearch" placeholder="Search products...">
                                </div>
                            </div>
                            <div class="bar-right">
                                <div class="navbar-sticky bg-light mobile-head">
                                    <div class="navbar navbar-expand-md navbar-light">
                                        <div class="navbar-toolbar d-flex flex-shrink-0 align-items-center">
                                            <div id="cart_items">
                                                @include('layouts.front-end.partials._cart')
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mt-3">
                        @if ($products->count() > 0)

                            <div id="productListWrapper" class="row ms-1">
                                @foreach ($products as $product)
                                    @include('web-views.guruji.partial.product-card', ['product' => $product])
                                @endforeach
                            </div>

                        @else
                            <div class="col-12">
                                <div class="text-center p-5 border rounded-3 bg-light">
                                    <img src="{{ theme_asset(path: 'public/assets/front-end/img/media/product.svg') }}" class="img-fluid" alt="">
                                    <h6 class="text-muted">{{ translate('no_product_found') }}</h6>
                                </div>
                            </div>
                        @endif
                    </div>

                </section>
                <!-- Products End -->
            </div>
        </div>

        <div class="row">
         <div class="col-12 loading-parent">
             <div id="loading" class="d--none">
                 <div class="text-center">
                     <img width="200" alt=""
                         src="{{ getValidImage(path: 'storage/app/public/company/' . getWebConfig(name: 'loader_gif'), type: 'source', source: theme_asset(path: 'public/assets/front-end/img/loader.gif')) }}">
                 </div>
             </div>
         </div>
     </div>

        @include('layouts.front-end.partials._quick-view-modal')

        <span id="route-quick-view" data-url="{{ route('quick-view') }}"></span>
        <span id="route-cart-add" data-url="{{ route('cart.add') }}"></span>
         <span id="route-cart-remove" data-url="{{ route('cart.remove') }}"></span>
         <span id="route-cart-variant-price" data-url="{{ route('cart.variant_price') }}"></span>
         <span id="route-cart-nav-cart" data-url="{{ route('cart.nav-cart') }}"></span>
         <span id="route-cart-order-again" data-url="{{ route('cart.order-again') }}"></span>
         <span id="route-cart-updateQuantity" data-url="{{ route('cart.updateQuantity') }}"></span>
         <span id="route-cart-updateQuantity-guest" data-url="{{ route('cart.updateQuantity.guest') }}"></span>
         <span id="route-cart-nav-cart" data-url="{{ route('cart.nav-cart') }}"></span>
         <span id="route-checkout-details" data-url="{{ route('checkout-details') }}"></span>
        <span id="route-checkout-payment" data-url="{{ route('checkout-payment') }}"></span>

         <meta name="_token" content="{{ csrf_token() }}">

        
        <!-- FOOTER -->
        <!-- <footer>
            © {{ date('Y') }} <a href="https://mahakal.com">Mahakal.com</a> • Bhagwan Mahakal ki Seva mein
            Samarpit
        </footer> -->
    </div>
    <script src="{{ theme_asset(path: 'public/assets/front-end/vendor/jquery/dist/jquery-2.2.4.min.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/product-view.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/vendor/bootstrap/dist/js/bootstrap.bundle.min.js') }}">
    </script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/owl.carousel.min.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/back-end/js/toastr.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/custom.js') }}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const heading = document.querySelector(".name-heading");
            const dp = document.querySelector(".pandit-dp");

            if (heading && dp) {
                dp.style.height = heading.offsetHeight + "px";
            }
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const tabWrapper = document.querySelector(".service-tabs-wrapper");
            const tabs = document.querySelectorAll(".tab-item");
            const sections = ["#serviceListitem", "#eventListitem", "#productListitem","#consellingListitem","#chatcallListitem"];
            /* Sticky Tabs */
            let stickyOffset = tabWrapper.offsetTop;
            window.addEventListener("scroll", function() {
                if (window.pageYOffset > stickyOffset) {
                    tabWrapper.classList.add("sticky");
                } else {
                    tabWrapper.classList.remove("sticky");
                }
            });
            /* ALL TOP BARS */
            const productBar = document.getElementById("productTopBar");
            const serviceBar = document.getElementById("serviceTopBar");
            const consellingBar = document.getElementById("consellingTopBar");
            const chatcallBar = document.getElementById("chatcallTopBar");
            const eventBar = document.getElementById("eventTopBar");
            /* Tab Change Function */
            function changeTab(tab) {
                tabs.forEach(t => t.classList.remove("active"));
                tab.classList.add("active");
                // Hide all sections
                sections.forEach(sec => document.querySelector(sec).style.display = "none");
                let target = tab.dataset.target;
                document.querySelector(target).style.display = "block";
                // SHOW ONLY ACTIVE TAB'S TOPBAR — HIDE OTHERS
                productBar.style.display = "none";
                serviceBar.style.display = "none";
                eventBar.style.display = "none";
                consellingBar.style.display = "none";
                chatcallBar.style.display = "none";
                if (target === "#productListitem") productBar.style.display = "flex";
                if (target === "#serviceListitem") serviceBar.style.display = "flex";
                if (target === "#consellingListitem") consellingBar.style.display = "flex";
                if (target === "#chatcallListitem") chatcallBar.style.display = "flex";
                if (target === "#eventListitem") eventBar.style.display = "flex";
                // Smooth Scroll
                window.scrollTo({
                    top: tabWrapper.offsetTop - 80,
                    behavior: "smooth"
                });
            }
            // CLICK Event
            tabs.forEach(tab => {
                tab.addEventListener("click", () => changeTab(tab));
            });
            // SWIPE support
            let touchStartX = 0;
            let touchEndX = 0;
            const contentArea = document.querySelector(".row");
            contentArea.addEventListener("touchstart", (e) => {
                touchStartX = e.changedTouches[0].screenX;
            });
            contentArea.addEventListener("touchend", (e) => {
                touchEndX = e.changedTouches[0].screenX;
                let diff = touchStartX - touchEndX;
                if (Math.abs(diff) < 50) return;
                let activeIndex = [...tabs].findIndex(t => t.classList.contains("active"));
                if (diff > 50 && activeIndex < tabs.length - 1) {
                    changeTab(tabs[activeIndex + 1]);
                }
                if (diff < -50 && activeIndex > 0) {
                    changeTab(tabs[activeIndex - 1]);
                }
            });
        });
    </script>
    <script>
        document.getElementById("followBtn").addEventListener("click", function() {
            if (this.innerText === "Follow") {
                this.innerText = "Following";
                this.style.background = "#efefef";
                this.style.color = "#000";
            } else {
                this.innerText = "Follow";
                this.style.background = "#0095f6";
                this.style.color = "#fff";
            }
        });
        //product
        document.addEventListener("DOMContentLoaded", function() {
            const productTabId = "productListitem";
            const productBar = document.getElementById("productTopBar");

            function toggleProductBar() {
                const activeTabContent = document.querySelector(".tab-pane.active");

                if (activeTabContent && activeTabContent.id === productTabId) {
                    productBar.style.display = "flex"; // show bar
                } else {
                    productBar.style.display = "none"; // hide bar
                }
            }
            // When tab changed
            document.querySelectorAll('[data-bs-toggle="tab"]').forEach(tab => {
                tab.addEventListener("shown.bs.tab", toggleProductBar);
            });
            // Initially check on page load
            toggleProductBar();

        });
    </script>
    <script>
      document.addEventListener("DOMContentLoaded", function() {
        // Utility function to normalize text (lowercase English, leave Hindi)
        function normalizeText(text) {
            return text ? text.toLowerCase().trim() : "";
        }

        // Product Search
        const productSearch = document.getElementById("productSearch");
        const products = document.querySelectorAll("#productListWrapper .product-item");
        productSearch?.addEventListener("input", function() {
            const value = normalizeText(this.value);
            products.forEach(item => {
                const name = normalizeText(item.querySelector(".product-name")?.innerText);
                item.style.display = name.includes(value) ? "block" : "none";
            });
        });

        // Counselling Search
        const counsellingSearch = document.getElementById("consulltancySearch");
        const counsellingItems = document.querySelectorAll("#consellingListitem .conselling-item");
        counsellingSearch?.addEventListener("input", function() {
            const value = normalizeText(this.value);
            counsellingItems.forEach(item => {
                const name = normalizeText(item.querySelector(".counselling-name")?.innerText);
                const price = normalizeText(item.querySelector(".price")?.innerText);
                item.style.display = name.includes(value) ? "block" : "none";
                item.style.display = price.includes(value) ? "block" : "none";
            });
        });

        // Service Search
        const serviceSearch = document.getElementById("serviceSearch");
        const serviceItems = document.querySelectorAll("#serviceListitem .service-item");
        serviceSearch?.addEventListener("input", function() {
            const value = normalizeText(this.value);
            serviceItems.forEach(item => {
                const name = normalizeText(item.querySelector(".name-puja")?.innerText);
                const category = normalizeText(item.querySelector(".category-name")?.innerText);
                const venue = normalizeText(item.querySelector(".pooja-venue")?.innerText);
                item.style.display = name.includes(value) ? "block" : "none";
                item.style.display = category.includes(value) ? "block" : "none";
                item.style.display = venue.includes(value) ? "block" : "none";
            });
        });

        });

    </script>
    <script>
        document.addEventListener('keydown', function(e) {
            if (
                (e.ctrlKey && (e.key === '+' || e.key === '-' || e.key === '=')) ||
                e.key === 'Meta'
            ) {
                e.preventDefault();
            }
        });
        window.addEventListener('wheel', function(e) {
            if (e.ctrlKey) {
                e.preventDefault();
            }
        }, {
            passive: false
        });
        let lastTouch = 0;
        document.addEventListener('touchend', function(e) {
            let now = new Date().getTime();
            if (now - lastTouch <= 300) {
                e.preventDefault();
            }
            lastTouch = now;
        }, false);
        
    </script>
    <script>
        document.querySelectorAll(".gridBtn").forEach(btn => {
            btn.addEventListener("click", function () {

            const items = document.querySelectorAll(".gride-service");

                items.forEach(item => {
                    if (item.classList.contains("col-12")) {

                        // List → Grid
                        item.classList.remove("col-12");
                        item.classList.add("col-xl-3","col-lg-4","col-md-4","col-sm-6","col-6");

                    } else {

                    // Grid → List
                    item.classList.remove("col-xl-3","col-lg-4","col-md-4","col-sm-6","col-6");
                    item.classList.add("col-12");
                    }
                });
            });
        });
</script>

</body>

</html>
