@extends('layouts.front-end.app')
@push('css_or_js')
<link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/home.css') }}" />
<link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/poojafilter/layout.css') }}">
<link rel="stylesheet"  href="{{ theme_asset(path: 'public/assets/front-end/css/animationbutton.css') }}">
<style>
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
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }
    .venue{
        font-size:14px;
        max-width: 180px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .name-puja{
        font-size:20px !important;
    }
    
    .alphabet-filter-mobile {
        display: flex;
        flex-direction: column;  
        gap: 4px;
        background: #fff;
        padding: 6px 4px;
        border-radius: 10px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        position: fixed;
        right: 0px;
        top: 70px;            
        z-index: 9999;
        width: 30px;
        height: calc(100vh - 80px);
        overflow-y: auto;
    }

    .alphabet-filter-mobile button,
    .alphabet-filter-mobile div {
        width: 14px;
        height: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;         
        font-weight: 600;
        cursor: pointer;
    }
    @media (min-width: 769px) {
        .alphabet-filter-mobile {
            display: none !important;
        }
    }
    .alphabet-filter-mobile.hide {
        opacity: 0;
        visibility: hidden;
    }
    .service-list-view .service-card {
        width: 100%;
    }

    /* Mobile Grid View (2 columns) */
    .service-grid-view {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
        width: 100%;
        box-sizing: border-box;
        margin: 0 !important;
        padding: 0 !important;
    }

    /* Prevent items from cutting on right side */
    .service-grid-view > * {
        min-width: 0 !important;
    }

    /* Force each card to fit grid cell */
    .service-grid-view .service-card {
        width: 100% !important;
    }

    /* Card height auto for responsive layout */
    .service-grid-view .service-card .card {
        height: auto;
    }

    /* Hide toggle buttons on desktop */
    @media (min-width: 768px) {
        #gridBtn,
        #listBtn {
            display: none !important;
        }
    }

    /* -------------------------------
   EXTRA GRID VIEW CUSTOM DESIGN
--------------------------------*/

/* Image smaller in grid */
.service-grid-view .puja-image {
    height: 130px !important;
    object-fit: cover;
    border-radius: 8px;
}

/* Hide short benefits text */
.service-grid-view .card-text {
    display: none !important;
}

/* Hide devotees + rating */
.service-grid-view .d-flex.justify-content-between {
    display: none !important;
}

/* Smaller headings */
.service-grid-view .pooja-heading {
    font-size: 12px !important;
    margin-bottom: 4px;
}

.service-grid-view .pooja-name {
    font-size: 13px !important;
    margin-bottom: 2px;
}

/* Venue text small */
.service-grid-view .pooja-venue {
    font-size: 11px !important;
}

/* Venue icon smaller */
.service-grid-view img[style*="temple.png"] {
    width: 16px !important;
    height: 16px !important;
}

/* Button compact */
.service-grid-view .animated-button {
    padding: 6px !important;
    font-size: 12px !important;
    border-radius: 6px;
}

/* Card compact */
.service-grid-view .card {
    padding: 8px !important;
}
/* FULL WIDTH STICKY TOP BAR */
.top-filter-bar {
    position: sticky;
    z-index: 9999;
    background: #ffffff;
    padding: 12px 15px;
    border-bottom: 1px solid #e5e5e5;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

/* Left Title */
.top-filter-bar h4 {
    margin: 0;
    font-size: 18px;
    font-weight: 700;
    color: #000;
}

/* Buttons */
.top-filter-bar .view-toggle button {
    padding: 6px 12px;
    font-size: 12px;
    border-radius: 6px;
}

/* Active Button */
.top-filter-bar .view-toggle button.active {
    background: #0d6efd;
    color: #fff;
}

/*  HIDE GRID/LIST IN DESKTOP (>= 992px) */
@media (min-width: 992px) {
    .top-filter-bar .view-toggle {
        display: none !important;
    }

}
@media (max-width: 767px) {
    .top-filter-bar {
        top: 0 !important;
    }
}

/* 🔹 Desktop (768px and above) → Your existing top:140 */
@media (min-width: 768px) {
    .top-filter-bar {
        display:none;
    }
}


</style>
@endpush

@section('content')
<div class="container mb-md-4 {{Session::get('direction') === "rtl" ? 'rtl' : ''}} __inline-65">
    <div class="bg-primary-light rounded-10 my-4 p-3 p-sm-4" data-bg-img="{{ theme_asset(path: 'public/assets/front-end/img/media/bg.png') }}">
        <div class="row g-2 align-items-center">
            <div class="col-lg-8 col-md-6">
                <div class="d-flex flex-column gap-1 text-primary">
                    <h4 class="mb-0 text-start fw-bold text-primary text-uppercase">{{ translate('Namaskar_yajman') }}</h4>
                    <p class="fs-14 fw-semibold mb-0">{{ translate('here_you_can_find_your_preferred_Guruji_and_book_your_desired_puja.') }}
                    </p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                    <p class="fs-14 fw-semibold mb-0">{{ translate('Temple_name') }} : {{ $guruji->is_pandit_primary_mandir }} 
                        <br>{{ translate('Temple_Address') }} : {{ $guruji->is_pandit_primary_mandir_location}}
                    </p>
            </div>
        </div>
    </div>
    <!-- Mobile Filter Toggle Button -->
    @php
        // Selected language from dropdown
        $lang = $data['code'] ?? getDefaultLanguage();
        // Alphabets
        $englishLetters = range('A','Z');
        $hindiLetters = [
           'अ','आ','इ','ई','उ','ऊ','ऋ','ए','ऐ','ओ','औ',
           'अं','अः',
           'क','ख','ग','घ','ङ',
           'च','छ','ज','झ','ञ',
           'ट','ठ','ड','ढ','ण',
           'त','थ','द','ध','न',
           'प','फ','ब','भ','म',
           'य','र','ल','व',
           'श','ष','स','ह',
           'क्ष','त्र','ज्ञ'
       ];
       // Final alphabet based on current language
       $alphabet = $lang == 'in' ? $hindiLetters : $englishLetters;
       $realName = $realName = Str::slug($guruji->name, '-');
   @endphp
   <div class="alphabet-filter-mobile">
       <button class="btn btn-sm btn-warning alpha-btn" data-letter="all">All</button>
       @foreach ($alphabet as $char)
           <button class="btn btn-sm btn-outline-primary alpha-btn"
               data-letter="{{ $char }}">
               {{ $char }}
           </button>
       @endforeach

   </div>
    <!-- Mobile View Toggle -->
    <div class="top-filter-bar">
        <!-- LEFT TEXT -->
        <h4>{{ translate('Namaskar') }} {{ $guruji->name }}</h4>

        <!-- RIGHT TOGGLE BUTTONS -->
        <div class="view-toggle">
            <button class="btn btn-outline-primary" id="gridBtn">Grid</button>
            <button class="btn btn-outline-primary active" id="listBtn">List</button>
        </div>
    </div>
    <div class="row">
        <section class="col-lg-12" id="serviceListitem">
        <div class="row g-3">
            @foreach ($services as $service)
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12 service-card"  data-name="{{ $service->name }}">
                    <div class="card h-100 shadow-sm">
                        
                        <!-- Category Badge -->
                        <span class="for-discount-value pooja-badge p-1 pl-2 pr-2 font-bold fs-13">
                            <span class="direction-ltr blink d-block">
                                {{ $service->category->name ?? 'No Category' }}
                            </span>
                        </span>

                        <!-- Image -->
                        <a href="{{ route('guruji.book-puja', [$realName, $service->slug]) }}">
                            <img class="card-img-top puja-image"
                                style="height:180px; object-fit:cover;"
                                src="{{ !empty($service->thumbnail)
                                        ? getValidImage(path: 'storage/app/public/pooja/thumbnail/'.$service->thumbnail)
                                        : getValidImage(path: 'storage/app/public/company/' . getWebConfig(name: 'loader_gif')) }}"
                                alt="">
                        </a>

                        <div class="card-body d-flex flex-column">

                            <p class="pooja-heading underborder two-lines-only text-uppercase">
                                {{ strtoupper($service->pooja_heading) }}
                            </p>
                            <p class="pooja-name two-lines-only name-puja">{{ $service->name }}</p>
                            <p class="card-text two-lines-only mb-2">{{ $service->short_benifits }}</p>
                            <!-- Venue -->
                            <div class="d-flex align-items-center mb-1">
                                <img src="{{ theme_asset(path: 'public/assets/front-end/img/track-order/temple.png') }}"
                                    style="width:20px;height:20px;">
                                <p class="pooja-venue venue ms-2">{{ $service->final_venue }}</p>
                            </div>
                            <!-- Devotees & Rating -->
                            <div class="d-flex justify-content-between align-items-center mt-auto" style="font-size:12px;">
                                <div class="d-flex align-items-center">
                                    <img src="{{ theme_asset(path: 'public/assets/front-end/img/track-order/users.gif') }}"
                                        style="width:22px;">
                                    <span class="ms-1">{{ 10000 }}+ Devotees</span>
                                </div>

                                <div>
                                    <i class="fas fa-star"></i> 5/5 (1K +ratings)
                                </div>
                            </div>

                            <a href="{{ route('guruji.book-puja', [$realName, $service->slug]) }}"
                            class="animated-button mt-3 w-100 text-center">
                                <span class="text-wrapper">
                                    <span class="text-slide">{{ translate('GO_PARTICIPATE') }}</span>
                                    <span class="text-slide">{{ translate('Limited_slots!') }}</span>
                                </span>
                                <span class="icon">
                                    <img src="{{ theme_asset(path: 'public/assets/front-end/img/track-order/arrow-white-icon.svg') }}"
                                        alt="arrow">
                                </span>
                            </a>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>
    </section>

    </div>
</div>
@endsection

@push('script')
<script>
document.querySelectorAll('.alpha-btn').forEach(btn => {
    btn.addEventListener('click', function () {

        const letter = this.dataset.letter;
        const cards = document.querySelectorAll('.service-card');

        cards.forEach(card => {
            let name = card.dataset.name.trim();

            if (letter === "all") {
                card.style.display = "block";
            } else if (name.startsWith(letter)) {
                card.style.display = "block";
            } else {
                card.style.display = "none";
            }
        });

    });
});

</script>
<script>
document.addEventListener("scroll", function () {
    const filter = document.querySelector(".alphabet-filter-mobile");
    const footer = document.querySelector("footer, .page-footer, #page-footer"); // footer selector
    if (!filter || !footer) return;
    const footerRect = footer.getBoundingClientRect();
    if (footerRect.top < window.innerHeight - 50) {
        filter.classList.add("hide");
    } else {
        filter.classList.remove("hide");
    }
});
</script>
<script>
    const wrapper = document.querySelector(".row.g-3");  
    const gridBtn = document.getElementById("gridBtn");
    const listBtn = document.getElementById("listBtn");

    // Default view = List
    wrapper.classList.add("service-list-view");

    gridBtn.addEventListener("click", () => {
        wrapper.classList.remove("service-list-view");
        wrapper.classList.add("service-grid-view");

        gridBtn.classList.add("active");
        listBtn.classList.remove("active");
    });

    listBtn.addEventListener("click", () => {
        wrapper.classList.remove("service-grid-view");
        wrapper.classList.add("service-list-view");

        listBtn.classList.add("active");
        gridBtn.classList.remove("active");
    });
</script>


@endpush