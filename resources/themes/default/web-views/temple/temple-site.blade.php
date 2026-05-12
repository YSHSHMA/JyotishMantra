@extends('layouts.temple-front-end.app1')
@php
    $ecommerceLogo = getWebConfig('company_web_logo');
    $verify = $temple->aadhaar_verify_status ?? 0;
    $images = !empty($temple['galleries2']['images']) ? json_decode($temple['galleries2']['images'], true) : [];
    $businessMode = getWebConfig(name: 'business_mode');
@endphp
@section('title', 'temple website')

@push('css_or_js')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endpush
@section('content')
    <!-- start slider section -->
    <section class="creative-fullpage--slider">
        <div class="banner-horizental">
            <div class="swiper swiper-container-h">
                <div class="swiper-wrapper">
                    @foreach ($images as $image)
                        <div class="swiper-slide">
                            <div class="slider-inner" data-swiper-parallax="100">
                                <img src="{{ getValidImage('storage/app/public/temple/gallery/' . $image, type: 'product') }}"
                                    alt="full_screen-image">
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="swiper-button-wrapper creative-button--wrapper">
                    <div class="swiper-button-next" tabindex="0" role="button" aria-label="Next slide">
                    </div>
                    <div class="swiper-button-prev" tabindex="0" role="button" aria-label="Previous slide">
                    </div>
                </div>
                <div class="slider-pagination-area">
                    <h5 class="slide-range one">1</h5>
                    <div class="swiper-pagination swiper-pagination-progressbar swiper-pagination-horizontal"><span
                            class="swiper-pagination-progressbar-fill"
                            style="transform: translate3d(0px, 0px, 0px) scaleX(0.666667) scaleY(1); transition-duration: 1500ms;"></span>
                    </div>
                    <h5 class="slide-range three">{{ count($images) }}</h5>
                </div>
            </div>
        </div>
    </section>
    <a href="" class="jay-mangal" target="_blank">
        {{ $temple->name }}
    </a>
    <!-- end slider section -->

    <!-- start address section -->
    <section class="address-section text-center">
        <div class="container">
            <div class="address-detail">
                <span class="rounded-pill text-bg-warning address-bedge"><i class="fa-solid fa-star"></i>
                    Birthplace of Planet Mars</span>
                <h1 class="m-0 mandir-name">{{ $temple->name }}</h1>
                <p class="fs-5">{{ $temple->cities->city }}, {{ $temple->states->name }}</p>
                <p class="para">{!! $temple->short_description !!}</p>
                <a href="" class="address-btn-bg"><i class="fa-solid fa-phone"></i> &nbsp;Contact Us</a>
                <small class="text-muted d-block fs-8 mt-4"><i
                        class="fa-solid fa-location-dot color-orange"></i>&nbsp;&nbsp;Ankpat
                    Marg,
                    Mangalnath Mandir, Agar Rd, Ujjain, Madhya
                    Pradesh
                    456006</small>
            </div>
            <div class="fid-area">
                <div class="row">
                    <div class="col-md-4">
                        <div class="fid">
                            <h2>1000+</h2>
                            <small>Years Old</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="fid border-left-right">
                            <h2>50K+</h2>
                            <small>Devotees/Year</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="fid">
                            <h2>12+</h2>
                            <small>Sacred Poojas</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- end address section -->

    <!-- start about us section -->
    <section class="about-us" id="about">
        <div class="container">
            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="image-mosaic">
                        <div class="mosaic-item tall">
                            <img src="{{ theme_asset(path: 'public/assets/temple-front-end/assets/images/home-images/ancient-shivling-with-milk-abhishek-red-sindoor-hi.jpg') }}"
                                alt="">
                        </div>

                        <div class="mosaic-item">
                            <img src="{{ theme_asset(path: 'public/assets/temple-front-end/assets/images/home-images/hindu-priest-pandit-performing-puja-havan-fire-rit.jpg') }}"
                                alt="">
                        </div>

                        <div class="mosaic-item">
                            <img src="{{ theme_asset(path: 'public/assets/temple-front-end/assets/images/home-images/mangalnath-temple-entrance-ancient-architecture-wi.jpg') }}"
                                alt="">
                        </div>

                        <div class="mosaic-item full overlay-card">
                            <img src="{{ theme_asset(path: 'public/assets/temple-front-end/assets/images/home-images/shipra-river-bank-temple-ghat-ujjain-sunrise-spiri.jpg') }}"
                                alt="">
                            <div class="since-badge">
                                <span>Since</span>
                                <strong>500 AD</strong>
                                <small>Ancient Sacred Temple</small>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="about-us-details">
                        <small class="text-uppercase fw-semibold color-orange2 mb-3 d-block">About The Temple
                        </small>
                        <h2>Welcome to the Historic <span>{{ $temple->name }}</span></h2>
                        <p>{!! $temple->details !!}</p>
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="about-us-list">
                                    <li><i class="fa-regular fa-circle-check fa-lg color-orange2"></i> Birthplace of
                                        Planet Mars
                                        (Mangal)
                                    </li>
                                    <li><i class="fa-regular fa-circle-check fa-lg color-orange2"></i> Located in Sacred
                                        Ujjain City</li>
                                    <li><i class="fa-regular fa-circle-check fa-lg color-orange2"></i> Mentioned in
                                        Matsya Purana</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="about-us-list">
                                    <li><i class="fa-regular fa-circle-check fa-lg color-orange2"></i> Located on Prime
                                        Meridian (Karka Line)</li>
                                    <li><i class="fa-regular fa-circle-check fa-lg color-orange2"></i> Clear view of
                                        Mars
                                        planet</li>
                                    <li><i class="fa-regular fa-circle-check fa-lg color-orange2"></i> Powerful Mangal
                                        Dosh Nivaran</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- end about us section -->

    <!-- start history section-->
    <section class="history" id="history">
        <div class="container">
            <div class="history-header-title">
                <span class="rounded-pill text-bg-warning history-bedge"><i class="fa-regular fa-sun"></i>
                    Temple History & Story</span>
                <h1>The Divine Legend of <span class="my-bg-gradient">Mangalnath Temple</span></h1>
                <p>मंगलनाथ मंदिर का पवित्र इतिहास और पौराणिक कथा</p>
            </div>

            <div class="row justify-content-center g-4 align-items-stretch">
                <!-- Left Card -->
                <div class="col-md-7 col-12">
                    <div class="legend-card light-card h-100">
                        <div class="card-header d-flex align-items-center gap-3">
                            <div class="icon-box">
                                <i class="fa-solid fa-book-open fa-xl"></i>
                            </div>
                            <div class="history-card-title">
                                <h4 class="mb-0">Origin of Mangal (Mars)</h4>
                                <small class="color-orange2">मंगल ग्रह की उत्पत्ति</small>
                            </div>
                        </div>

                        <p>
                            According to the sacred <strong class="maroon-color">Matsya Purana</strong>, the
                            Mangalanth Temple is revered as the
                            <span class="highlight">birthplace of Planet Mars (Mangal)</span>.
                            The Puranas proclaim that Mars is the son of Mother Earth (Prithvi),
                            making this the most sacred site for Mars-related worship in the universe.
                        </p>

                        <p>
                            Hindu mythology tells a fascinating tale: when Lord Shiva was
                            battling the powerful demon <strong class="maroon-color">Andhakasura</strong>, a drop of
                            his divine perspiration fell upon the Earth at this very location.
                            From this sacred drop arose the Shivling from which
                            <span class="highlight">Mangal Grah</span> (Planet Mars) was born,
                            hence the temple is dedicated to Lord Shiva as
                            <strong class="maroon-color">Mangalanath</strong> – the Lord of Mars.
                        </p>
                    </div>
                </div>

                <!-- Right Card -->
                <div class="col-md-5 col-12">
                    <div class="legend-card orange-card h-100">
                        <div class="card-header d-flex align-items-center gap-3">
                            <div class="icon-box white-icon">
                                ✨
                            </div>
                            <h4 class="mb-0 text-white">The Sacred Legend</h4>
                        </div>

                        <p>
                            According to <strong>Mahant Rajendra Bharti</strong>, the chief priest
                            of Mangalanth Temple, the name
                            <strong>"Ugra"</strong> refers to the natural name of planet Mars in
                            Sanskrit scriptures.
                        </p>

                        <p>
                            The temple is associated with <strong>Maharaja Vikramaditya</strong>,
                            the legendary king of Ujjain. Ujjain is known as
                            <span class="highlight-white">"Nabhigradesh"</span>
                            (Navel of the Earth), making it the most auspicious place for
                            <strong>Mangal Dosh Nivaran</strong>.
                        </p>
                    </div>
                </div>

                <!-- Left Card -->
                <div class="col-md-7 col-12">
                    <div class="legend-card light-card h-100">
                        <div class="card-header d-flex align-items-center gap-3">
                            <div class="icon-box">
                                <i class="fa-solid fa-star fa-xl"></i>
                            </div>
                            <div class="history-card-title">
                                <h4 class="mb-0">Astronomical Significance
                                </h4>
                                <small class="color-orange2">खगोलीय महत्व</small>
                            </div>
                        </div>

                        <p>
                            The Mangalnath Temple holds remarkable <strong class="maroon-color">astronomical
                                significance</strong>It is situated exactly on the
                            <span class="highlight">Tropic of Cancer (Karka Rekha)</span>,
                            making Ujjain the geographical center of the ancient world according to Hindu cosmology.
                        </p>

                        <p>
                            In ancient times, this temple served as an <strong class="maroon-color">observatory
                            </strong>for tracking celestial bodies. The rays from Mars planet are believed to fall
                            directly on this sacred site, making it the most powerful location for viewing and
                            worshipping the Red Planet.
                        </p>
                    </div>
                </div>

                <!-- Right Card -->
                <div class="col-md-5 col-12">
                    <div class="legend-card light-card h-100">
                        <div class="card-header d-flex align-items-center gap-3">
                            <div class="icon-box green-icon">
                                <i class="fa-solid fa-location-dot fa-xl"></i>
                            </div>
                            <div class="history-card-title">
                                <h4 class="mb-0">Sacred Location
                                </h4>
                                <small class="color-green">पवित्र स्थान

                                </small>
                            </div>
                        </div>

                        <p>
                            The temple is beautifully situated on the banks of the holy <strong class="maroon-color">
                                Kshipra (Shipra)
                                River</strong>, considered one of the most sacred rivers in Hindu tradition.</p>
                        <p>The temple
                            comes alive especially on
                            <strong class="color-green">Tuesdays (Mangalwar),</strong> when thousands of devotees gather
                            for special pujs
                            and rituals.
                        </p>


                    </div>
                </div>

                <!-- vedic-quote-card -->
                <div class="col-md-8 col-12">
                    <div class="vedic-quote-card">
                        <div class="om-badge">ॐ</div>

                        <h2 class="sanskrit-text">
                            “भूमोस्स्य जन्मस्थानं च भूमिपुत्रः स उच्यते”
                        </h2>

                        <p class="translation">
                            “This is the birthplace of Bhauma (Mars), who is called the son of Earth”
                        </p>

                        <span class="source">— Matsya Purana</span>
                    </div>
                </div>



            </div>
        </div>



    </section>
    <!-- end history section -->

    <!-- start pooja section -->
    <section class="pooja" id="poojas">
        <div class="container">
            <div class="pooja-header-title">
                <span class="small-line">Our Sacred Services</span>
                <h1>Powerful Dosha Nivaran <span class="my-bg-gradient">Pujas</span></h1>
                <p>Book authentic Vedic pujas performed by experienced Pandit Ji with full rituals and vidhi as per
                    ancient scriptures at the sacred Mangalnath Mandir.</p>
            </div>
            <ul class="nav nav-pills justify-content-center pooja-tabs mb-4" id="poojaTab" role="tablist">
                @foreach ($templeServices as $index => $service)
                    @php
                        $tabId = 'service-' . $service['id'];
                    @endphp
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $index === 0 ? 'active' : '' }}" id="{{ $tabId }}-tab"
                            data-bs-toggle="pill" data-bs-target="#{{ $tabId }}" type="button" role="tab"
                            aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                            {{ ucfirst(strtolower($service['name'])) }}
                        </button>
                    </li>
                @endforeach
            </ul>


            <div class="tab-content" id="poojaTabContent">
                <!-- POOJA TAB -->
                @foreach ($templeServices as $index => $service)
                    <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="service-{{ $service['id'] }}"
                        role="tabpanel">
                        <div class="row g-4">
                            @if (!empty($packages[$service['id']]))
                                @foreach ($packages[$service['id']] as $package)
                                    <div class="col-12 col-sm-6 col-lg-3">
                                    <a href="#" class=""
                                                        data-serviceid="{{ $package->id }}"
                                                        data-servicetype="{{ $service['name'] }}"
                                                        onclick="proceedBooking(this)">
                                        <div class="puja-card gradient-border">
                                            <div class="puja-img divine-card">
                                                <img src="{{ getValidImage('storage/app/public/temple/package/' . $package->image, type: 'product') }}"
                                                    alt="">
                                                <span class="popular-badge">
                                                    {{ ucfirst(strtolower($service['name'])) }}</span>
                                                <div class="halo-light"></div>
                                                <div class="bottom-overlay"></div>
                                                <div class="puja-title">
                                                    <h5>{{ $package->varient_name }}</h5>
                                                    <span>{{ $package->varient_name }}</span>
                                                </div>
                                            </div>
                                            <div class="puja-body">
                                                <p class="text-clamp-2">
                                                    {!! $package->description !!}
                                                </p>
                                                <div class="text-center mt-3">
                                                    <a href="#" class="header-btn"  data-serviceid="{{ $package->id }}"
                                                        data-servicetype="{{ $service['name'] }}"
                                                        onclick="proceedBooking(this)">
                                                    <span class="text-center">₹{{ number_format(
                                                                $package->base_price 
                                                                + $package->platform_fee_percentage 
                                                                + $package->receipt_fee_percentage, 
                                                                2
                                                                ) }}
                                                            Book Pooja </span><span>→</span>
                                                    </a>

                                                </div>

                                            </div>
                                        </div>
                                        </a>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

        </div>



    </section>
    <!-- end pooja section -->

    <!-- live darshan section -->
    <section class="live-darshan" id="livedarshan">
        <div class="container">
            <div class="live-darshan-title">
                <span class="live-darshan-small-line">Live Darshan
                </span>
                <h1>Experience Divine<span class="my-orange-color"> Live Darshan</span></h1>
                <p>घर बैठे करें मंगलनाथ मंदिर के लाइव दर्शन</p>
            </div>
            <div class="row g-4">
                <!-- LEFT: VIDEO -->
                <div class="col-lg-8">
                    <div class="video-card">
                        <iframe src="https://www.youtube.com/embed/mxHwNjvOqok?si=pqDbVowy2VuuT7SY"
                            title="YouTube video player" frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                    </div>
                    <div class="video-content">
                        <div class="video-text">
                            <h5>Mangalanath Temple Live Stream</h5>
                            <p>Watch live aarti, puja ceremonies, and darshan from Mangalnath Temple, Ujjain</p>
                        </div>

                        <a href="#" class="btn btn-warning sub-btn btn-sm">
                            <i class="fa-solid fa-play"></i> &nbsp;Subscribe Channel
                        </a>
                    </div>
                </div>
                <!-- RIGHT: INFO CARDS -->
                <div class="col-lg-4">
                    <!-- Aarti Timings -->
                    <div class="info-card mb-3">
                        <h6 class="card-title"><i class="fa-regular fa-clock"></i> Aarti Timings</h6>
                        <ul>
                            <li><span>Morning Aarti</span><b>5:00 AM</b></li>
                            <li><span>Bhog Aarti</span><b>11:00 AM</b></li>
                            <li><span>Evening Aarti</span><b>7:00 PM</b></li>
                            <li><span>Shayan Aarti</span><b>10:30 PM</b></li>
                        </ul>
                    </div>

                    <!-- Special Days -->
                    <div class="info-card mb-3">
                        <h6 class="card-title"><i class="fa-regular fa-bell"></i> Special Days</h6>
                        <p><b>Tuesday (मंगलवार):</b> Special Mangal Puja & Abhishek</p>
                        <p><b>Mangal Chaturthi:</b> Grand celebrations with special rituals</p>
                        <p><b>Shravan Month:</b> Holy month with special darshan</p>
                    </div>

                    <!-- CTA -->
                    <div class="youtube-card text-center">
                        <i class="fa-solid fa-video text-white fa-2xl mb-5"></i>
                        <h6 class="text-white fw-bold">Watch on YouTube</h6>
                        <p>Subscribe for daily darshan videos and live streams</p>
                        <a href="#" class="btn btn-danger ytb w-100">Visit Channel</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- end live darshan section -->

    <!-- start ujjain destail section -->
    <section class="pooja">
        <div class="container">
            <div class="pooja-header-title">
                <span class="small-line">Holy City of Ujjain
                </span>
                <h1>Discover <span class="my-bg-gradient">पवित्र उज्जैन नगरी</span></h1>
                <p>Ujjain, one of the seven sacred cities (Sapta Puri) of Hinduism, is located on the banks of the holy
                    Kshipra River in Madhya Pradesh. Known as Avantika in ancient times, this city has been a major
                    center of learning, spirituality, and astronomy for over 3,000 years. It served as the capital of
                    King Vikramaditya and is the site of the famous Kumbh Mela held every 12 years.</p>
            </div>
            <div class="fid-area mb-5 mw-100">
                <div class="row">
                    <div class="col-md-3">
                        <div class="fid">
                            <h2>3000+</h2>
                            <small>Years of History</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="fid">
                            <h2>84</h2>
                            <small>Ancient Temples</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="fid">
                            <h2>1</h2>
                            <small>of 12 Jyotirlingas</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="fid">
                            <h2>51</h2>
                            <small>Shakti Peethas (1 here)</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="major-temple mb-5">
                <h4 class="text-center">Major Temples in Ujjain</h4>
                <div class="row g-4">
                    <!-- Card 1 -->
                    <div class="col-12 col-sm-6 col-lg-6">
                        <!-- Card 1 -->
                        <div class="temple-card">
                            <div class="image-box">
                                <span class="badge">Jyotirlinga</span>
                                <img src="{{ theme_asset(path: 'public/assets/temple-front-end/assets/images/home-images/mahakaleshwar-jyotirlinga-temple-ujjain-ancient-hi.jpg') }}"
                                    alt="">
                            </div>

                            <div class="card-body">
                                <div class="temple-head-title">
                                    <div class="t-title">
                                        <h3>श्री महाकालेश्वर मंदिर</h3>
                                        <span>Shri Mahakaleshwar Temple</span>
                                    </div>
                                    <span class="temple-tag"><i class="fa-solid fa-star"></i> Swayambhu
                                        Jyotirlinga</span>
                                </div>
                                <p class="temple-para">
                                    One of the twelve Jyotirlingas, this self-manifested lingam is dedicated to Lord
                                    Shiva. It is the most prominent temple of Ujjain and attracts millions of devotees
                                    annually for the famous Bhasma Aarti.
                                </p>

                                <div class="temple-footer">
                                    <a href="#">More Details →</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2 -->
                    <div class="col-12 col-sm-6 col-lg-6">
                        <!-- Card 1 -->
                        <div class="temple-card">
                            <div class="image-box">
                                <span class="badge">Shiva Temple</span>
                                <img src="{{ theme_asset(path: 'public/assets/temple-front-end/assets/images/home-images/kal-bhairav-temple-ujjain-hindu-ancient-shiva.jpg') }}"
                                    alt="">
                            </div>

                            <div class="card-body">
                                <div class="temple-head-title">
                                    <div class="t-title">
                                        <h3>काल भैरव मंदिर</h3>
                                        <span>Kaal Bhairav Temple</span>
                                    </div>
                                    <span class="temple-tag"><i class="fa-solid fa-star"></i> Guardian of Ujjain</span>
                                </div>
                                <p class="temple-para">
                                    Dedicated to Kaal Bhairav, a fierce manifestation of Lord Shiva. It is believed that
                                    visiting this temple before Mahakal darshan is essential. The deity is famously
                                    offered liquor as prasad.
                                </p>

                                <div class="temple-footer">
                                    <a href="#">More Details →</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 3 -->
                    <div class="col-12 col-sm-6 col-lg-6">
                        <!-- Card 1 -->
                        <div class="temple-card">
                            <div class="image-box">
                                <span class="badge">Shakti Peeth</span>
                                <img src="{{ theme_asset(path: 'public/assets/temple-front-end/assets/images/home-images/harsiddhi-shakti-peeth-temple-ujjain-devi-goddess.jpg') }}"
                                    alt="">
                            </div>

                            <div class="card-body">
                                <div class="temple-head-title">
                                    <div class="t-title">
                                        <h3>हरसिद्धि मंदिर</h3>
                                        <span>Harsiddhi Temple</span>
                                    </div>
                                    <span class="temple-tag"><i class="fa-solid fa-star"></i> 51 Shakti Peethas</span>
                                </div>
                                <p class="temple-para">
                                    One of the 51 Shakti Peethas dedicated to Goddess Annapurna. A beautiful example of
                                    Maratha architecture with two majestic pillars adorned with lamps that illuminate
                                    during Navratri.
                                </p>

                                <div class="temple-footer">
                                    <a href="#">More Details →</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 4 -->
                    <div class="col-12 col-sm-6 col-lg-6">
                        <!-- Card 1 -->
                        <div class="temple-card">
                            <div class="image-box">
                                <span class="badge">Ganesh Temple</span>
                                <img src="{{ theme_asset(path: 'public/assets/temple-front-end/assets/images/home-images/chintaman-ganesh-temple-ujjain-lord-ganesha-hindu.jpg') }}"
                                    alt="">
                            </div>

                            <div class="card-body">
                                <div class="temple-head-title">
                                    <div class="t-title">
                                        <h3>चिंतामन गणेश मंदिर</h3>
                                        <span>Chintaman Ganesh Temple</span>
                                    </div>
                                    <span class="temple-tag"><i class="fa-solid fa-star"></i> Self-Manifested
                                        Idol</span>
                                </div>
                                <p class="temple-para">
                                    An ancient temple where the idol of Lord Ganesha is believed to have self-manifested
                                    from the ground. 'Chintaman' means the one who removes all worries and anxieties
                                    from life.
                                </p>

                                <div class="temple-footer">
                                    <a href="#">More Details →</a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            <div class="other-sacred-places">
                <h4 class="text-center">Other Sacred Places</h4>
                <div class="row g-4">
                    <!-- Card 1 -->
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="other-sacred-places-card">
                            <div class="card-body">
                                <i class="fa-solid fa-location-dot other-sacred-places-card-icon"></i>
                                <div class="other-sacred-places-head-title">
                                    <div class="ospt-title">
                                        <h3>बड़े गणेश मंदिर</h3>
                                        <span>Bade Ganeshji Ka Mandir</span>
                                    </div>
                                </div>
                                <p class="other-sacred-places-para">
                                    Located near Mahakaleshwar Temple, famous for its huge idol of Lord Ganesha.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2 -->
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="other-sacred-places-card">
                            <div class="card-body">
                                <i class="fa-solid fa-location-dot other-sacred-places-card-icon"></i>
                                <div class="other-sacred-places-head-title">
                                    <div class="ospt-title">
                                        <h3>सांदीपनि आश्रम</h3>
                                        <span>Sandipani Ashram</span>
                                    </div>
                                </div>
                                <p class="other-sacred-places-para">
                                    The ashram of Guru Sandipani where Lord Krishna and Balarama received their
                                    education.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Card 3 -->
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="other-sacred-places-card">
                            <div class="card-body">
                                <i class="fa-solid fa-location-dot other-sacred-places-card-icon"></i>
                                <div class="other-sacred-places-head-title">
                                    <div class="ospt-title">
                                        <h3>राम घाट</h3>
                                        <span>Ram Ghat</span>
                                    </div>
                                </div>
                                <p class="other-sacred-places-para">
                                    Sacred ghat on Kshipra River where Lord Rama bathed during exile. Hosts the grand
                                    Kumbh Mela.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Card 4 -->
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="other-sacred-places-card">
                            <div class="card-body">
                                <i class="fa-solid fa-location-dot other-sacred-places-card-icon"></i>
                                <div class="other-sacred-places-head-title">
                                    <div class="ospt-title">
                                        <h3>सिद्ध वट</h3>
                                        <span>Siddh Vat</span>
                                    </div>
                                </div>
                                <p class="other-sacred-places-para">
                                    Ancient sacred Banyan tree on Shipra river banks, considered highly auspicious.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Card 5 -->
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="other-sacred-places-card">
                            <div class="card-body">
                                <i class="fa-solid fa-location-dot other-sacred-places-card-icon"></i>
                                <div class="other-sacred-places-head-title">
                                    <div class="ospt-title">
                                        <h3>गढ़कालिका मंदिर</h3>
                                        <span>Gadkalika Temple</span>
                                    </div>
                                </div>
                                <p class="other-sacred-places-para">
                                    Ancient temple dedicated to Goddess Kali, associated with the great poet Kalidasa.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Card 6 -->
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="other-sacred-places-card">
                            <div class="card-body">
                                <i class="fa-solid fa-location-dot other-sacred-places-card-icon"></i>
                                <div class="other-sacred-places-head-title">
                                    <div class="ospt-title">
                                        <h3>इस्कॉन मंदिर</h3>
                                        <span>ISKCON Temple</span>
                                    </div>
                                </div>
                                <p class="other-sacred-places-para">
                                    Modern temple dedicated to Lord Krishna with beautiful architecture and peaceful
                                    ambiance.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Card 7 -->
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="other-sacred-places-card">
                            <div class="card-body">
                                <i class="fa-solid fa-location-dot other-sacred-places-card-icon"></i>
                                <div class="other-sacred-places-head-title">
                                    <div class="ospt-title">
                                        <h3>गोमती कुंड</h3>
                                        <span>Gomti Kund</span>
                                    </div>
                                </div>
                                <p class="other-sacred-places-para">
                                    Sacred water tank in Sandipani Ashram believed to contain waters from all holy
                                    places.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Card 8 -->
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="other-sacred-places-card">
                            <div class="card-body">
                                <i class="fa-solid fa-location-dot other-sacred-places-card-icon"></i>
                                <div class="other-sacred-places-head-title">
                                    <div class="ospt-title">
                                        <h3>चौबीस खंबा मंदिर</h3>
                                        <span>Chaubis Khamba Temple</span>
                                    </div>
                                </div>
                                <p class="other-sacred-places-para">
                                    Ancient temple with 24 pillars dedicated to Chhoti Mata and Badi Mata.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="featured-card row g-4 align-items-center">

                <!-- Left Content -->
                <div class="col-lg-7 text-white mt-0">
                    <span class="featured-label">FEATURED TEMPLE</span>

                    <h2 class="mt-2 mb-3 fw-semibold">
                        मंगलनाथ मंदिर <span class="fw-bold myfont-family">(Mangalnath Temple)</span>
                    </h2>

                    <p class="mb-4">
                        The Mangalnath Temple is unique as it is the only temple in the world
                        dedicated to the planet Mars (Mangal). According to the Matsya Purana,
                        this is the birthplace of Mars. Located on the banks of Kshipra River,
                        it is considered the most powerful place for performing Mangal Dosh
                        Nivaran Puja and Bhat Puja. Thousands of devotees visit daily.
                    </p>

                    <a href="#" class="btn btn-light btn-book d-inline-flex align-items-center gap-2">
                        Book Your Pooja <span>↗</span>
                    </a>
                </div>

                <!-- Right Image -->
                <div class="col-lg-5">
                    <div class="featured-img position-relative">
                        <img src="{{ theme_asset(path: 'public/assets/temple-front-end/assets/images/home-images/mangal-nath-mandir-1-1.webp') }}"
                            class="img-fluid w-100" alt="Temple">

                        <span class="img-badge">Birthplace of Mars</span>
                    </div>
                </div>

            </div>
        </div>



    </section>
    <!-- end pooja section -->

    <!-- start why choose-us section -->
    <section class="why-choose-us">
        <div class="container">
            <div class="why-choose-us-title">
                <span class="why-choose-us-small-line">Why Choose Us</span>
                <h1>Why Devotees Trust Our Service</h1>
                <p>Experience divine blessings with authentic rituals performed by experienced priests at the sacred
                    Mangalnath Temple</p>
            </div>
            <div class="row g-4">
                <!-- Card 1 -->
                <div class="col-12 col-sm-6 col-lg-4">
                    <div class="why-choose-us-card">
                        <div class="card-body">
                            <i class="fa-solid fa-shield why-choose-us-card-para-card-icon"></i>
                            <div class="why-choose-us-card-head-title">
                                <div class="ospt-title">
                                    <h3>100% Authentic Vedic Pujas</h3>
                                </div>
                            </div>
                            <p class="why-choose-us-card-para">
                                All rituals performed strictly as per Shastras with complete Vidhi by trained Vedic
                                priests


                            </p>
                        </div>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="col-12 col-sm-6 col-lg-4">
                    <div class="why-choose-us-card">
                        <div class="card-body">
                            <i class="fa-solid fa-award why-choose-us-card-para-card-icon"></i>
                            <div class="why-choose-us-card-head-title">
                                <div class="ospt-title">
                                    <h3>Certified Pandit Ji</h3>
                                </div>
                            </div>
                            <p class="why-choose-us-card-para">
                                Experienced and verified Pandit Ji from the temple with deep knowledge of Vedic
                                scriptures
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="col-12 col-sm-6 col-lg-4">
                    <div class="why-choose-us-card">
                        <div class="card-body">
                            <i class="fa-solid fa-users why-choose-us-card-para-card-icon"></i>
                            <div class="why-choose-us-card-head-title">
                                <div class="ospt-title">
                                    <h3>Custom Rituals as per Kundli</h3>
                                </div>
                            </div>
                            <p class="why-choose-us-card-para">
                                Personalized Sankalp and rituals based on your Janm Kundli for maximum benefits
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Card 4 -->
                <div class="col-12 col-sm-6 col-lg-4">
                    <div class="why-choose-us-card">
                        <div class="card-body">
                            <i class="fa-solid fa-video why-choose-us-card-para-card-icon"></i>
                            <div class="why-choose-us-card-head-title">
                                <div class="ospt-title">
                                    <h3>Live Puja Streaming</h3>
                                </div>
                            </div>
                            <p class="why-choose-us-card-para">
                                Virtual puja options available for NRIs and devotees who cannot visit in person
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Card 5 -->
                <div class="col-12 col-sm-6 col-lg-4">
                    <div class="why-choose-us-card">
                        <div class="card-body">
                            <i class="fa-regular fa-clock why-choose-us-card-para-card-icon"></i>
                            <div class="why-choose-us-card-head-title">
                                <div class="ospt-title">
                                    <h3>Auspicious Muhurat Selection</h3>
                                </div>
                            </div>
                            <p class="why-choose-us-card-para">
                                Puja performed on the most auspicious timing based on Panchang and planetary positions
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Card 6 -->
                <div class="col-12 col-sm-6 col-lg-4">
                    <div class="why-choose-us-card">
                        <div class="card-body">
                            <i class="fa-regular fa-circle-check why-choose-us-card-para-card-icon"></i>
                            <div class="why-choose-us-card-head-title">
                                <div class="ospt-title">
                                    <h3>Complete Puja Samagri</h3>
                                </div>
                            </div>
                            <p class="why-choose-us-card-para">
                                All required items for the puja included - no hidden costs or additional charges
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- end why choose us section -->

    <!-- start process section -->
    <section class="process-section py-5">
        <div class="container text-center">

            <span class="process-subtitle">SIMPLE PROCESS</span>
            <h2 class="process-title">
                Your Puja, Just <span class="my-bg-gradient">3 Steps </span> Away
            </h2>

            <div class="row position-relative mt-5 align-items-start process-wrapper">

                <!-- Line -->
                <div class="process-line d-none d-md-block"></div>

                <!-- Step 1 -->
                <div class="col-md-4 process-item">
                    <div class="process-circle">01</div>
                    <h5>Choose Your Puja Type</h5>
                    <p>
                        Select from a wide range of Dosha Nivaran and
                        Shanti Pujas based on your horoscope needs
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="col-md-4 process-item">
                    <div class="process-circle">02</div>
                    <h5>Provide Basic Details</h5>
                    <p>
                        Fill a short form with your name, birth details
                        (for Sankalp), and preferred puja date
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="col-md-4 process-item">
                    <div class="process-circle">03</div>
                    <h5>Get Confirmation & Join</h5>
                    <p>
                        We handle the rest – you receive updates, video
                        link for live streaming & divine blessings
                    </p>
                </div>

            </div>
        </div>
    </section>
    <!-- end why choose us section -->

    <!-- start temple gallery -->
    <section class="temple-gallery">
        <div class="temple-gallery-header-title">
            <span class="small-line">Temple Gallery</span>
            <h1>Divine Glimpses of <span class="my-bg-gradient">Mangalnath Mandir</span></h1>
            <p>Browse through our gallery to witness the divine pujs and rituals performed at Mangalnath Temple

            </p>
        </div>
        <div class="container py-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="gallery-item h-100">
                        <a href="images/home-images/gallery1.webp" data-lightbox="mandir-gallery"
                            data-title="Mangalnath Mandir Puja Ujjain">
                            <img src="{{ theme_asset(path: 'public/assets/temple-front-end/assets/images/home-images/gallery1.webp') }}"
                                class="img-fluid">
                            <div class="gallery-overlay">
                                <i class="fa-solid fa-magnifying-glass-plus"></i>
                                <span class="gallery-title">Mangalnath Mandir Puja Ujjain</span>
                            </div>
                        </a>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="row g-3">

                        <div class="col-6">
                            <div class="gallery-item h-100">
                                <a href="images/home-images/gallery2.webp" data-lightbox="mandir-gallery"
                                    data-title="Devotees performing Puja">
                                    <img src="{{ theme_asset(path: 'public/assets/temple-front-end/assets/images/home-images/gallery2.webp') }}"
                                        class="img-fluid">
                                    <div class="gallery-overlay">
                                        <i class="fa-solid fa-magnifying-glass-plus"></i>
                                        <span class="gallery-title">Devotees performing Puja</span>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="gallery-item h-100">
                                <a href="images/home-images/gallery2.webp" data-lightbox="mandir-gallery"
                                    data-title="Sacred Puja Items">
                                    <img src="{{ theme_asset(path: 'public/assets/temple-front-end/assets/images/home-images/gallery3.webp') }}"
                                        class="img-fluid">
                                    <div class="gallery-overlay">
                                        <i class="fa-solid fa-magnifying-glass-plus"></i>
                                        <span class="gallery-title">Sacred Puja Items</span>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="gallery-item h-100">
                                <a href="images/home-images/gallery4.webp" data-lightbox="mandir-gallery"
                                    data-title="Mangalnath Temple Entrance">
                                    <img src="{{ theme_asset(path: 'public/assets/temple-front-end/assets/images/home-images/gallery4.webp') }}"
                                        class="img-fluid">
                                    <div class="gallery-overlay">
                                        <i class="fa-solid fa-magnifying-glass-plus"></i>
                                        <span class="gallery-title">Mangalnath Temple Entrance</span>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="gallery-item h-100">
                                <a href="images/home-images/gallery3.webp" data-lightbox="mandir-gallery"
                                    data-title="Sacred Puja Items">
                                    <img src="{{ theme_asset(path: 'public/assets/temple-front-end/assets/images/home-images/gallery3.webp') }}"
                                        class="img-fluid">
                                    <div class="gallery-overlay">
                                        <i class="fa-solid fa-magnifying-glass-plus"></i>
                                        <span class="gallery-title">Sacred Puja Items</span>
                                    </div>
                                </a>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>
    <!-- end temple gallery -->

    <!-- start get-in-touch section -->
    <section class="get-in-touch" id="contact">
        <div class="container">
            <div class="row align-items-center g-5">

                <!-- LEFT CONTENT -->
                <div class="col-lg-6">
                    <div class="get-in-touch-details">
                        <p class="section-title">GET IN TOUCH</p>
                        <h1 class="main-heading">
                            Book Your <span class="my-bg-gradient">Sacred Puja</span><br>Today
                        </h1>

                        <p class="pera">
                            Contact us now for instant puja booking at Mangalnath Temple Ujjain. Our experienced Pandit
                            Ji will guide you through the process and perform authentic Vedic rituals.
                        </p>
                    </div>


                    <ul class="contact-list mt-4 p-0">
                        <li>
                            <div class="contact-icon"><i class="fa-solid fa-phone"></i></div>
                            <div class="contact-text">
                                <strong>Phone / WhatsApp</strong><br>
                                <a href="tel:+9196858 85131">+91 96858 85131</a>
                            </div>
                        </li>
                        <li>
                            <div class="contact-icon"><i class="fa-solid fa-envelope"></i></div>
                            <div class="contact-text">
                                <strong>Email</strong><br>
                                <a href="mailto:info@mangalnathtemple.in">info@mangalnathtemple.in</a><br>
                                <a href="mailto:admin@mangalnathtemple.in.in">admin@mangalnathtemple.in</a>
                            </div>
                        </li>
                        <li>
                            <div class="contact-icon"><i class="fa-solid fa-location-dot"></i></div>
                            <div class="contact-text">
                                <strong>Address</strong><br>
                                <p>
                                    Angark Marga, Mangalnath Mandir,<br>
                                    Ujjain, Madhya Pradesh 456006
                                </p>
                            </div>
                        </li>
                        <li>
                            <div class="contact-icon"><i class="fa-regular fa-clock"></i></div>
                            <div class="contact-text">
                                <strong>Temple Timings
                                </strong><br>
                                <p class="mb-0">
                                    Morning: 5:00 AM - 12:00 PM<br>
                                    Evening: 4:00 PM - 9:00 PM
                                </p>
                            </div>
                        </li>
                    </ul>

                    <div class="info-box">
                        <h6>Best Time to Visit</h6>
                        <ul class="mb-0 small">
                            <li><b>Tuesday (Mangalwar)</b> – Most auspicious for Mangal Dosh Puja</li>
                            <li><b>Simhastha Kumbh Mela</b> - For special rituals and blessings</li>
                            <li><b>Shravan Month</b> – Holy month dedicated to Lord Shiva</li>
                        </ul>
                    </div>
                </div>

                <!-- RIGHT FORM -->
                <div class="col-lg-6 margin-top-135 margin-top-30">
                    <div class="form-card">
                        <h5>Book Your Puja</h5>
                        <p>Fill the form below and we'll get back to you within 24 hours</p>

                        <form>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="f-label">First
                                        Name</label>
                                    <input type="text" class="form-control" placeholder="First Name">
                                </div>
                                <div class="col-md-6">
                                    <label class="f-label">Last
                                        Name</label>
                                    <input type="text" class="form-control" placeholder="Last Name">
                                </div>
                                <div class="col-12">
                                    <label class="f-label">Email</label>
                                    <input type="email" class="form-control" placeholder="Email Address">
                                </div>
                                <div class="col-12">
                                    <label class="f-label">Phone
                                        Number</label>
                                    <input type="text" class="form-control" placeholder="Phone Number">
                                </div>
                                <div class="col-12">
                                    <label class="f-label">Select
                                        Puja</label>
                                    <select class="form-select">
                                        <option selected>Choose Puja Type</option>
                                        <option>Mangal Dosh Puja</option>
                                        <option>Rudrabhishek</option>
                                        <option>Navgraha Puja</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="f-label">Message
                                        (Optional)</label>
                                    <textarea class="form-control" rows="3"
                                        placeholder="Any specific requirements or birth details for Sankalp..."></textarea>
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-submit sub-btn-req w-100">
                                        Submit Booking Request
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </section>
    <!-- end get-in-touch section -->

    <div class="modal fade" id="poojaBookingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content pooja-modal">
    
                <!-- Modal Header -->
                <div class="modal-header border-0">
                    <h5 class="modal-title">
                        <span class="">ॐ</span> Book Pooja
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
    
                <!-- Modal Body -->
                <div class="modal-body">
                    <form action="{{ route('mandirservice.booking') }}" method="POST">
                        @csrf
                        <input type="hidden" name="service_id" id="service-id">
                        <input type="hidden" name="service_type" id="service-type">
                        <!-- Mobile Number -->
                        <div class="mb-3">
                            <label class="form-label">Mobile Number</label>
                            <input type="tel" class="form-control" placeholder="Enter 10 digit mobile number"
                                name="mobile" id="mobile" maxlength="10" required>
                        </div>
    
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" placeholder="Enter your name" name="name"
                                id="name" required>
                        </div>
    
                        <!-- Aadhaar Card -->
                        <div class="mb-3" id="aadharDiv">
                            <label class="form-label">Aadhaar Card Number({{ $temple->aadhaar_verify_status==1?'*':'Optional' }})</label>
                            <input type="text" class="form-control" placeholder="Enter 12 digit Aadhaar number"
                                name="aadhar" id="aadhar" maxlength="12"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,12)"
                                {{ $verify == 1 ? 'required' : '' }}>
                        </div>
    
                        <div class="mb-3 d-none" id="otpDiv">
                            <label class="form-label">Verify OTP</label>
                            <input type="text" class="form-control" placeholder="Enter OTP" id="otp">
                        </div>
    
                        <!-- Submit -->
                        <button type="button" class="btn modal-btn-book w-100 mt-2 {{ $verify == 0 ? 'd-none' : '' }}"
                            id="sendOtpBtn">
                            Send OTP
                        </button>
    
                        <button type="button" class="btn modal-btn-book w-100 mt-2 d-none" id="verifyOtpBtn">
                            Verify
                        </button>
    
                        <button type="submit" class="btn modal-btn-book w-100 mt-2 {{ $verify == 0 ? '' : 'd-none' }}"
                            id="proceedBtn">
                            Proceed Booking
                        </button>
    
                    </form>
                </div>
    
            </div>
        </div>
    </div>

    <input type="hidden" id="aadhar-request-id" inputmode="numeric">

@endsection
@push('script')
    <script src="https://cdn.jsdelivr.net/npm/mixitup@3/dist/mixitup.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <script>
        $(document).ready(function() {

            $('#filters .filter').on('click', function() {

                // active tab
                $('#filters .filter').removeClass('active');
                $(this).addClass('active');

                let filterValue = $(this).data('filter');

                // hide all first
                $('.portfolio').addClass('hidden').removeClass('visible');

                if (filterValue === 'all') {
                    $('.portfolio').removeClass('hidden').addClass('visible');
                } else {
                    $(filterValue).removeClass('hidden').addClass('visible');
                }

                // Empty state check
                if ($('.portfolio.visible').length === 0) {
                    $('.portfolio-empty').addClass('visible');
                } else {
                    $('.portfolio-empty').removeClass('visible');
                }

                // scroll
                $('html, body').animate({
                    scrollTop: $('#portfoliolist').offset().top - 100
                }, 300);
            });

        });
    </script>

    <script>
        $(document).on('blur', '#mobile', function() {
            const mobileNumber = $('#mobile').val().trim();

            if (mobileNumber.length !== 10) {
                toastr.error('Please enter a valid 10-digit mobile number!');
                return;
            }

            $.ajax({
                url: "{{ route('mandirservice.customer.check') }}",
                type: "get",
                data: {
                    mobile: mobileNumber
                },
                dataType: "json",
                success: function(data) {
                    if (data.status == 'success') {
                        $('#name').val(data.user.name);
                    } else if (data.status == 'false') {
                        $('#name').val('');
                    } else {
                        toastr.error('Error verify mobile number.');
                    }
                },
                error: function() {
                    toastr.error('Error verify mobile number.');
                }
            });
        });

        $(document).on('click', '#sendOtpBtn', function() {
            const aadhaarNumber = $('#aadhar').val().trim();

            if (aadhaarNumber.length !== 12) {
                toastr.error('Please enter a valid 12-digit Aadhaar number!');
                return;
            }

            $.ajax({
                url: "{{ url('api/v1/darshan/aadhar-send-otp') }}",
                type: "POST",
                data: {
                    aadhaar_number: aadhaarNumber,
                    _token: "{{ csrf_token() }}"
                },
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(data) {
                    if (data.status == 1) {
                        $('#aadhar-request-id').val(data.request_id);
                        $('#aadhar').prop('readonly', true);
                        $('#otpDiv').removeClass('d-none');
                        $('#verifyOtpBtn').removeClass('d-none');
                        $('#sendOtpBtn').addClass('d-none');
                        // $('#proceedBtn').addClass('d-none');
                        toastr.success(
                            'OTP sent successfully to your registered mobile number.');
                    } else if (data.status == 2) {
                        // Aadhaar already verified
                        $('#aadhar-request-id').val('');
                        $('#sendOtpBtn').addClass('d-none');
                        $('#proceedBtn').removeClass('d-none');
                        $('#aadhar').prop('readonly', true);
                        toastr.success('This Aadhaar is already verified.');
                    } else {
                        $('#aadhar-request-id').val('');
                        alert(data.message);
                    }
                },
                error: function() {
                    toastr.success('Error sending OTP. Please try again.');
                }
            });
        });

        // ---- VERIFY OTP ----
        $('#verifyOtpBtn').on('click', function() {
            const aadhaarOtp = $('#otp').val().trim();
            const aadhaarRequestId = $('#aadhar-request-id').val();

            $('#aadhaar-no-error').text('');

            if (aadhaarOtp.length !== 6) {
                $('#aadhaar-no-error').addClass('text-danger').text('Aadhaar OTP must be 6 digits');
                return;
            }

            $.ajax({
                url: "{{ url('api/v1/darshan/aadhar-otp-verify') }}",
                type: "POST",
                data: {
                    otp: aadhaarOtp,
                    request_id: aadhaarRequestId,
                    _token: "{{ csrf_token() }}"
                },
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(data) {
                    if (data.status == 1) {
                        $('#aadhar-request-id').val('');
                        $('#otpDiv').addClass('d-none');
                        $('#verifyOtpBtn').addClass('d-none');
                        $('#proceedBtn').removeClass('d-none');
                        toastr.success('Aadhaar verified successfully.');
                    } else {
                        toastr.error('Unable to verify.');
                    }
                },
                error: function() {
                    toastr.error('Error verifying otp.');
                }
            });
        });

        function proceedBooking(that) {
            var serviceId = $(that).data('serviceid');
            var serviceType = $(that).data('servicetype');

            $('#service-id').val(serviceId);
            $('#service-type').val(serviceType);

            $('#poojaBookingModal').modal('show');
        }
    </script>
@endpush
