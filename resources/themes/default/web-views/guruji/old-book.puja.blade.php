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
    <link href="{{ theme_asset(path: 'public/assets/front-end/css/poojafilter/puja-single.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/owl.theme.default.min.css') }}">
    <link rel="stylesheet"href="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/css/intlTelInput.css') }}">
    <link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
</head>

<body>
    @php
        $ecommerceLogo = getWebConfig('company_web_logo');
    @endphp
    <header class="container-fluid py-3 bg-white shadow-sm fixed-top">
        <div class="row align-items-center justify-content-between">
        
        <!-- LEFT: Guruji Name -->
        <div class="col-6">
            <div class="guru-title">
                <span class="small-text">Sacred Rituals by</span>
                <h3>{{ $gurujiname->name }}</h3>
            </div>
        </div>

        <!-- RIGHT: Logo -->
        <div class="col-6 d-flex justify-content-end">
            <div class="powered-logo text-end">
                <span class="powered-text">Powered by</span>
                <a href="{{ url('/') }}">
                    <img src="{{ getValidImage('storage/app/public/company/' . $ecommerceLogo, type: 'backend-logo') }}"
                        alt="Mahakal.com" class="site-logo">
                </a>
            </div>
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
    <!-- Main Content (header ke neeche se start hoga) -->
    <main class="" style="margin-top:100px;">
        <!-- Image Slider -->
        @php
            if ($puja->product_type == 'pooja') {
                $folder = 'pooja/';
                $bookingdate = $puja->booking_date ?? null;
                $pujavenue = $puja->pooja_venue ?? null;
            }
            $pujaCount = 10000;    
        @endphp
       
            <section class="container mb-2">
                <div class="owl-carousel">
                    @if(!empty($imagePaths))
                        @foreach ($imagePaths as $key => $photo)
                            <div class="item carousel-image {{ $key === 0 ? 'active' : '' }}"
                                id="image{{ $key }}">
                                <img class="w-100 shadow-sm"
                                    src="{{ $photo }}"
                                    alt="Puja Image {{ $key + 1 }}">
                            </div>
                        @endforeach
                    @endif
                </div>
            </section>
            

        <!-- Hero Card -->
        <section class="container mb-3">
            <div class="card-hero">
            <div class="row align-items-center">
                    <!-- Left Side: Details -->
                    <div class="col-md-6 text-start">
                        <div class="flex flex-row mt-2 flex-nowrap leading-normal">
                            <div>
                            <span class="inline-flex">{{ translate('Till_now') }}</span>
                            <span class="font-bold text-dark ml-1">
                                {{ $pujaCount }}+
                                <span class="ml-1 mr-1">{{ translate('Devotees') }}</span>
                            </span>
                            <span style="color:#000;">
                                {{ translate('have_experienced_divine_blessings') }}.
                                {{ \Illuminate\Support\Str::of($gurujiname->name)->replace('-', ' ')->title() }}
                                {{ translate('has_performed_over') }} {{ $pujaCount }}+ {{ translate('Pujas') }}.
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

                    <!-- Right Side: Countdown -->
                    <div class="col-md-6 text-md-end text-start">
                        <!-- Ratings Display -->
                       
                            <div class="font-10">
                                <h3
                                    class="text-sm mt-2 mb-2 font-medium border-b border-dashed border-primary font-weight-bold">
                                    <i class="fas fa-star"></i>
                                    5/5 (1K +ratings)
                                </h3>
                            </div>
                       
                        
                    </div>
                </div>
                @if (!empty($puja) && !empty($puja->pooja_heading))
                    <span class="text-12 font-bold  line-clamp-2 text-ellipsis mb-0"
                        style="color:#fe9802;">{{ strtoupper($puja->pooja_heading) }}
                    </span>
                @endif
                <h3 class="h3 mt-2 text-dark font-bold" id="displayName">{{ $puja->name ?? 'ServiceName' }}</h3>
                <div class="flex flex-col">
                    <div class="flex items-center space-x-1 pt-2">
                        <div class="d-flex">
                            <img src="{{ theme_asset(path: 'public/assets/front-end/img/track-order/temple.png') }}"
                                alt="Puja Venue" style="width:24px;height:24px;">
                            <p class="pooja-venue" style="color:#000;">{{ $puja->final_venue }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Packages -->

        <section class="container mb-2">
            <div class="row g-3 " id="packageList">
       
                @if (!empty($packageShow) && count($packageShow) > 0)
                    <h4 class="mt-4 fw-bold text-start text-md-left text-warning">
                        {{ translate('Select Your Puja Package') }}
                    </h4>
                    <p class="mb-1 text-muted text-start text-md-left">
                        {{ translate('Select_the_best_puja_package_with_clear_pricing_and_services.') }}
                    </p>

                    @foreach ($packageShow as $pac)
                        @php
                            $package = \App\Models\Package::find($pac['package_id'] ?? null);
                            $color = $package->color ?? '#FF6F00';
                            if (!function_exists('darkenColor')) {
                                function darkenColor($hex, $percent = 20)
                                {
                                    $hex = str_replace('#', '', $hex);
                                    $r = hexdec(substr($hex, 0, 2));
                                    $g = hexdec(substr($hex, 2, 2));
                                    $b = hexdec(substr($hex, 4, 2));

                                    $r = max(0, min(255, $r - ($r * $percent) / 100));
                                    $g = max(0, min(255, $g - ($g * $percent) / 100));
                                    $b = max(0, min(255, $b - ($b * $percent) / 100));

                                    return sprintf('#%02x%02x%02x', $r, $g, $b);
                                }
                            }
                            $darkColor = darkenColor($color, 20);
                        @endphp

                        @if ($package)
                            <div class="col-6 col-md-6 col-lg-3">
                                <div class="package {{ $loop->first ? 'selected' : '' }}"
                                    style="--pkg-color: #fd891c;background:{{ $color }} ;"
                                    data-id="{{ $package->id }}" data-name="{{ $package->title }}" data-price="{{ $pac->price ?? 0 }}" data-type="{{ $package->type }}"
                                    data-pandit="{{ $pac->pandit_id }}">

                                    <!-- Person Badge -->
                                    <span class="person-badge" style="background: {{ $color }}">
                                        {{ $package->person }} Person
                                    </span>

                                    <!-- Radio Button Style -->
                                    <span class="select-circle"></span>

                                    <h5 class="mt-4">{{ $package->title }}</h5>
                                    <p class="text-secondary small">{{ $package->short_info ?? '' }}</p>
                                    <div class="package-footer d-flex align-items-center justify-content-between mt-2"
                                        style="background: linear-gradient(to right, #eb181824, {{ $color }}); display: flex; align-items: center; justify-content: space-between; padding: 8px 12px; border: none; width: 100%; border-radius: 8px; cursor: pointer;">
                                        <div class="price" style="color:#fff;">
                                            ₹{{ $pac->price ?? 0 }}/-
                                        </div>
                                        <img src="{{ getValidImage(path: 'storage/app/logo/' . $package->image, type: 'product') ?? asset('default-image.png') }}"
                                            alt="Package" class="package-img">
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                @endif
            </div>
        </section>
        {{-- Product List show --}}
        <section class="container mb-5">
            <div class="row g-2" id="productList">
                @php
                    $productIds = json_decode($puja->product_id, true) ?? [];
                    $products = \App\Models\Product::whereIn('id', $productIds)->where('status', 1)->get()->keyBy('id');
                @endphp

                @if (!empty($productIds) && count($productIds) > 0)
                    <div class="col-12 text-left">
                        <h4 class="mt-4 fw-bold text-start text-md-left text-warning">
                            {{ translate('Offer Your Devotion') }}</h4>
                        <p class="text-muted">
                            {{ translate('Along_with_your_Puja_,_you_may_offer_Chadhava_or_make_a_donation.') }}'
                        </p>
                    </div>
                    <div class="owl-carousel owl-theme" id="productCarousel">
                        @foreach ($productIds as $pid)
                            @php $product = $products[$pid] ?? null; @endphp
                            @if ($product)
                                <div class="item">
                                    <div class="product-card border rounded p-2 h-100 d-flex flex-row align-items-center text-center"
                                        data-id="{{ $product->id }}" data-name="{{ $product->name }}"
                                        data-price="{{ $product->unit_price ?? 0 }}"
                                        data-image="{{ getValidImage(path: 'storage/app/public/product/thumbnail/' . $product->image, type: 'product') }}"
                                        style="box-shadow: 0 2px 6px rgba(0,0,0,0.1); transition: all 0.2s ease-in-out;">

                                        <div class="mb-2"
                                            style="height:120px; display:flex; align-items:center; justify-content:center; width:100%;">
                                            <img src="{{ getValidImage(path: 'storage/app/public/product/thumbnail/' . $product['thumbnail'], type: 'product') }}"
                                                alt="{{ $product->name }}"
                                                style="max-height:100%; max-width:100%; object-fit:contain;">
                                        </div>

                                        <!-- Right: Info + Add -->
                                        <div class="d-flex flex-column">
                                            <h6 class="mb-1 product-name">
                                                {{ $product->name }}
                                            </h6>
                                            <span class="text-warning mb-2">₹{{ $product->unit_price ?? 0 }}/-</span>

                                            <div class="mt-auto">
                                                <button class="btn btn-primary btn-sm btn-add-slide">Add</button>
                                                <!-- Quantity counter (hidden initially) -->
                                                <div
                                                    class="quantity-counter d-none mt-2 d-flex justify-content-center align-items-center">
                                                    <button class="btn btn-sm btn-outline-danger btn-minus">-</button>
                                                    <span class="mx-2 quantity">1</span>
                                                    <button class="btn btn-sm btn-outline-success btn-plus">+</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>

                @endif

            </div>
        </section>             

    </main>

    <!-- Sticky Footer -->
    {{-- <div class="sticky-bar py-3"
        style="background: url('{{ asset('public/assets/front-end/img/bg-footer.jpg') }}') no-repeat center center/cover;"> --}}
    <div class="sticky-bar py-2">
        <div class="container">
            <div class="row align-items-center">

                <!-- Text -->
                <div class="col-12 col-md-8 text-center text-md-start mb-2 mb-md-0" id="footerInfo">
                    {{ translate('Select Your Puja Package') }}
                </div>

                <!-- Button -->
                <div class="col-12 col-md-4 text-center text-md-end">
                    <button class="btn w-100 w-md-auto px-3" id="btnEditInfo"
                        style="background-color: #FF6F00; border-color: #FF6F00;" disabled>
                        {{ translate('Proceed') }}
                    </button>
                </div>
            </div>
        </div>
    </div>



    <!-- Details Modal -->
    <div class="modal fade" id="detailsModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
    aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content" style="border-top: 2px solid var(--base) !important;/">
                <div class="modal-header">
                    <span class="text-18 font-bold mr-2">
                        {{ translate('Fill_your_details_for_Puja') }}</span>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form id="detailsForm" method="POST" novalidate action="{{ route('guruji.gurujipujaLead', $puja->slug) }}">
                    @csrf
                    @php
                        if (auth('customer')->check()) {
                            $customer = App\Models\User::where('id', auth('customer')->id())->first();
                        }
                    @endphp

                    <input type="hidden" name="service_id" value="{{ $forecastServiceId ?? $puja->id }}">
                    
                      
                    <input type="hidden" name="type" id="package_type">
                    <input type="hidden" name="package_id" id="package_id">
                    <input type="hidden" name="package_price" id="package_price">
                    <input type="hidden" name="add_product_id" id="add_product_id">
                    <input type="hidden" name="final_amount" id="total_amount">
                    <input type="hidden" name="pandit_id" id="pandit-id">
                    <div class="modal-body">
                        <span class="block text-16 font-bold text-gray-900 text-dark">
                            {{ translate('Enter Your WhatsApp Mobile Number') }}
                        </span>
                        <span class="text-[12px] font-normal text-[#707070]">
                            {{ translate('Your puja booking updates will be sent on the below WhatsApp number') }}
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
                        <div class="mb-3">
                            <div class="form-group">
                                <label class="form-label font-semibold">{{ translate('Chosse the Date') }}</label>
                                <input class="form-control text-align-direction" type="text" name="booking_date" id="datepicker" placeholder="{{ translate('Enter_your_puja_praforming_date') }}"
                                            autocomplete="off" required>
                            </div>
                        </div>
                        
                    </div>

                    <div class="modal-footer">
                        <button type="submit" id="bookNowBtn"
                            class="btn btn-primary btn-block btn-shadow mt-1 font-weight-bold w-100">
                            {{ translate('Book Now') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Confirm Modal -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true"    data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ translate('Confirm Your Details') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @if ($digital_payment['status'] == 1)
                        @foreach ($payment_gateways_list as $payment_gateway)
                            <form method="post" class="digital_payment pooja-pending-form"
                                id="{{ $payment_gateway->key_name }}_form" action="{{ route('GurujipaymentRequest') }}">
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
                                    <input type="hidden" name="external_redirect_link" value="{{ route('guruji-puja-pending-web-payment') }}">
                                    <label class="d-flex align-items-center gap-2 mb-0 form-check py-2 cursor-pointer">
                                        <input type="radio" id="{{ $payment_gateway->key_name }}"  name="online_payment" class="form-check-input custom-radio"
                                            value="{{ $payment_gateway->key_name }}" hidden>
                                        <img width="30" src="{{ dynamicStorage(path: 'storage/app/public/payment_modules/gateway_image') }}/{{ $payment_gateway->additional_data && json_decode($payment_gateway->additional_data)->gateway_image != null ? json_decode($payment_gateway->additional_data)->gateway_image : '' }}"
                                            alt="" hidden>
                                        <span class="text-capitalize form-check-label" hidden>
                                            @if ($payment_gateway->additional_data && json_decode($payment_gateway->additional_data)->gateway_title != null)
                                                {{ json_decode($payment_gateway->additional_data)->gateway_title }}
                                            @else
                                                {{ str_replace('_', ' ', $payment_gateway->key_name) }}
                                            @endif
                                        </span>
                                    </label>
                                    <input type="hidden" name="order_id" id="pending-order-id" class="orderId" value="">
                                    <input type="hidden" name="leads_id" id="pending-lead-id" class="orderId" value="">
                                </div>
                            </form>
                        @endforeach
                    @endif
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="text-center mb-3"><i class="fas fa-receipt"></i>
                                {{ translate('Booking Receipt') }}</h5>
                            <div class="row">
                                <div class="mb-3 d-flex flex-column gap-1">
                                    <div class="d-flex justify-content-between">
                                        <p class="mb-0"><span id="cService">—</span></p>
                                        <p class="mb-0"><strong>{{ translate('Order ID') }}:</strong>
                                        <span id="cOrderId">—</span></p>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <p class="mb-0"><span id="cVenue">—</span></p>
                                        <p class="mb-0"><strong>{{ translate('Booking Date') }}:</strong>
                                        <span id="cDate">—</span>
                                        </p>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <p class="mb-0"><span id="cPackage">—</span></p>
                                        <p class="mb-0"><span id="cPackagePrice">—</span></p>
                                        <br>
                                        <p class="mb-0"><span id="cGurujiName">—</span></p>
                                    </div>
                                </div>
                                <hr>
                                <h6 class="mb-2"><i class="fas fa-user"></i> {{ translate('Customer Details') }}</h6>
                                <div class="d-flex justify-content-between">
                                    <p class="mb-0"><strong>{{ translate('Name') }}:</strong> 
                                        <span id="cName">—</span></p>
                                    <p class="mb-0"><strong>{{ translate('Mobile') }}:</strong> 
                                        <span id="cMobile">—</span></p>
                                </div>
                            </div>
                            <hr>

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
                                {{ translate('Note Confirmed & Payment Pending') }} </p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">{{ translate('Cancel') }}</button>
                        <button class="btn btn-primary" type="button"
                            id="finalSubmit">{{ translate('Confirm & Submit') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-top: 2px solid var(--base) !important;/">
                <div class="display-6 mb-3">✅</div>
                <h5>पेमेंट सफल!</h5>
                <p>आपकी बुकिंग कन्फर्म हो गई है।</p>
                <button class="btn btn-warning" data-bs-dismiss="modal">ठीक है</button>
            </div>
        </div>
    </div>
    <input type="hidden" id="fullDate" value="{{ date('Y-m-d', strtotime($bookingdate . ' -1 day')) }}">
    <input type="hidden" id="fullTime" value="{{ date('H:i:s', strtotime($puja->pooja_time)) }}">

    <!-- JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/owl.carousel.min.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/js/intlTelInput.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/country-picker-init.js') }}"></script>
    <script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>

    <script>
        
        $(document).ready(function() {
            var today = new Date(new Date().getFullYear(), new Date().getMonth(), new Date().getDate());
            $('#datepicker').datepicker({
                uiLibrary: 'bootstrap4',
                format: 'yyyy-mm-dd',
                modal: true,
                footer: true,
                minDate: today
            });

            $("#productCarousel").owlCarousel({
                loop: false,
                autoplay: false, // autoplay off
                margin: 10,
                nav: false,
                responsive: {
                    0: {
                        items: 2
                    }, // Mobile pe 2 cards
                    576: {
                        items: 2
                    }, // Small tablets pe 2
                    768: {
                        items: 3
                    }, // Medium screens pe 3
                    992: {
                        items: 4
                    }, // Large screen pe 4
                    1200: {
                        items: 5
                    }
                }
            });

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
        let chosen = {
            id: null,
            name: null,
            type: null,
            pandit: null,
            amount: 0
        };
        let selectedProducts = {};

        const detailsModal = new bootstrap.Modal('#detailsModal');
        const confirmModal = new bootstrap.Modal('#confirmModal');
        const successModal = new bootstrap.Modal('#successModal');
        const $bookingDateContainer = $('#booking-date-container');
        const $bookingDateSelect = $('#bookingDateSelect');
        // =========================
        // Update Footer
        // =========================
        function updateFooter() {
            const footer = document.getElementById('footerInfo');
            if (!footer) return;

            let total = parseFloat(chosen.amount) || 0;
            let products = [];
            let productsText = "";

            Object.entries(selectedProducts).forEach(([id, p]) => {
                total += (p.price * p.qty);
                productsText += `${p.name} x${p.qty} (₹${p.price * p.qty}), `;

                products.push({
                    product_id: id,
                    price: p.price,
                    qty: p.qty
                });
            });

            if (productsText.endsWith(", ")) {
                productsText = productsText.slice(0, -2);
            }

            footer.innerHTML = `${chosen.name} 
                <span style="font-size: 18px; font-weight: bold;">(₹${chosen.amount})</span><br>
                ${productsText ? `<strong>Items:</strong> ${productsText}<br>` : ""}
                <strong>Total:</strong> 
                <span style="font-size: 18px; font-weight: bold;">₹${total}</span>
            `;


            // Hidden fields update
            $("#package_type").val(chosen.type);
            $("#package_id").val(chosen.id);
            $("#package_price").val(chosen.amount);
            $("#add_product_id").val(JSON.stringify(products));
            $("#total_amount").val(total);
            $("#pandit-id").val(chosen.pandit);
        }

        // =========================
        // Datepicker
        // =========================
        function datePicker() {
            var today = new Date();
            var tomorrow = new Date(today);
            tomorrow.setDate(today.getDate());

            $('#bookingdate').datepicker({
                uiLibrary: 'bootstrap4',
                format: 'yyyy-mm-dd',
                modal: true,
                footer: true,
                minDate: tomorrow,
                todayHighlight: true
            });
        }

        // =========================
        // Package Selection
        // =========================
        // yeh loop se pehle define karo
        document.querySelectorAll('.package').forEach((pkg, index, packages) => {
            if (index === 0) {
                pkg.classList.add('selected');
                chosen.id = Number(pkg.dataset.id); // ensure number
                chosen.name = pkg.dataset.name;
                chosen.type = pkg.dataset.type;
                chosen.pandit = pkg.dataset.pandit;
                chosen.amount = parseFloat(pkg.dataset.price) || 0;

                let packageType = chosen.type;
                const footer = document.getElementById('footerInfo');
                const btn = document.getElementById('btnEditInfo');

                if (footer) {
                    footer.innerHTML =
                        `Package: ${chosen.name} | Amount:<span style="font-size: 1.5rem; font-weight: bold;">₹${chosen.amount}</span>`;
                }

                if (btn) {
                    const mobileSpan = btn.querySelector('.d-md-none');
                    if (mobileSpan) {
                        mobileSpan.innerHTML =
                            `Package: ${chosen.name} | Amount:<span style="font-size: 1.5rem; font-weight: bold;">₹${chosen.amount}</span>`;
                    }
                    btn.disabled = false;
                }
                $bookingDateSelect.addClass('d-none');
                $bookingDateContainer.empty();
                updateFooter();
            }


            pkg.addEventListener('click', () => {
                packages.forEach(p => p.classList.remove('selected'));
                pkg.classList.add('selected');

                chosen.id = pkg.dataset.id;
                chosen.name = pkg.dataset.name;
                chosen.type = pkg.dataset.type;
                chosen.pandit = pkg.dataset.pandit;
                chosen.amount = parseFloat(pkg.dataset.price) || 0;

                updateFooter();

                // Booking date logic
                
                $bookingDateSelect.addClass('d-none');
                $bookingDateContainer.empty();
                

                // Show carousel
                const carouselWrapper = document.getElementById('productCarousel');
                if (carouselWrapper) {
                    carouselWrapper.classList.remove('d-none');
                    setTimeout(() => carouselWrapper.classList.add('show'), 50);
                }

                // Reset product cards
                selectedProducts = {};
                document.querySelectorAll('.product-card').forEach(card => {
                    const addBtn = card.querySelector('.btn-add-slide');
                    const counter = card.querySelector('.quantity-counter');
                    if (addBtn && counter) {
                        addBtn.classList.remove('d-none');
                        counter.classList.add('d-none');
                        counter.querySelector('.quantity').textContent = "1";
                    }
                });

                $("#add_product_id").val("");
                $("#total_amount").val(chosen.amount);
                $("#package_type").val(chosen.type);
                $("#pandit-id").val(chosen.pandit);
                $('#productCarousel').trigger('to.owl.carousel', [0, 300]);
            });
        });


        // =========================
        // Product Add
        // =========================
        $(document).on('click', '.btn-add-slide', function(e) {
            e.stopPropagation();
            const card = $(this).closest('.product-card');
            const id = card.data('id');
            const name = card.data('name');
            const price = parseFloat(card.data('price')) || 0;

            $(this).addClass('d-none');
            card.find('.quantity-counter').removeClass('d-none');
            card.find('.quantity').text(1);

            selectedProducts[id] = {
                name,
                price,
                qty: 1
            };
            updateFooter();
        });

        // =========================
        // Footer Proceed Btn
        // =========================
        document.getElementById('btnEditInfo').addEventListener('click', () => detailsModal.show());

        // =========================
        // Save Details Form
        // =========================
        const detailsForm = document.getElementById('detailsForm');
        detailsForm.addEventListener('submit', function(e) {
            e.preventDefault();

            let name = document.getElementById('person-name').value.trim();
            let mobile = document.getElementById('person-number').value.trim();
            let packageType = chosen.type;

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
            if (packageType === 'panditpooja') {
                let $bookingDate = $('#bookingDate');
                if ($bookingDate.length > 0) {
                    let bookingsdate = $bookingDate.val().trim();
                    if (!bookingsdate) {
                        alert("Please select a booking date before proceeding.");
                        $('#bookingDate').focus();
                        return false;
                    }
                }
            }
            if (!valid) return;
            $('#bookNowBtn').text('Please Wait ...');
            $('#bookNowBtn').prop('disabled', true);
            let formData = new FormData(detailsForm);
            fetch(detailsForm.action, {
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
                        
                        document.getElementById('cService').textContent = d.service_name || "—";
                        document.getElementById('cVenue').textContent = d.puja_venue || "—";
                        document.getElementById('cName').textContent = d.name;
                        document.getElementById('cMobile').textContent = d.mobile;
                        document.getElementById('cDate').textContent = d.booking_date;
                        document.getElementById('cPackage').textContent = d.package_name;
                        document.getElementById('cPackagePrice').textContent = "₹" + d.package_price;
                        document.getElementById('cGurujiName').textContent = d.guruji_name;
                        document.getElementById('cOrderId').textContent = d.order_id || "—";
                        document.getElementById('cAmount').textContent = "₹" + d.total_amount;

                       
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
                        const finalBtn = document.getElementById('finalSubmit');
                        finalBtn.setAttribute("data-orderid", d.order_id);
                        finalBtn.setAttribute("data-amount", d.total_amount);
                        finalBtn.setAttribute("data-leadid", d.lead_id);

                        $("#pending-order-id").val(d.order_id);
                        $("#pending-lead-id").val(d.lead_id);
                        $("#pending-lead-id").val(d.lead_id);
                        $("#bookNowBtn").prop("disabled", true);
                        confirmModal.show();
                        detailsModal.hide();
                    } else {
                        alert("Error saving booking");
                        $('#bookNowBtn').text('Please Wait ...');
                        $('#bookNowBtn').prop('disabled', false);
                    }
                })
                .catch(err => console.error(err));
        });

        // =========================
        // Final Submit
        // =========================
        document.getElementById('finalSubmit').addEventListener('click', function() {
            const orderId = this.getAttribute("data-orderid");
            const amount = this.getAttribute("data-amount");
            const leadId = this.getAttribute("data-leadid");
            if (!orderId || !amount) return;
            $('#pending-order-id').val(orderId);
            $("#pending-lead-id").val(leadId);
            $('.pooja-pending-form').submit();
        });



        // // //  Disable Right Click
        // document.addEventListener("contextmenu", function(e) {
        //     e.preventDefault();
        // });


        // document.onkeydown = function(e) {
        //     if (e.keyCode == 123) {
        //         return false;
        //     } 
        //     if (e.ctrlKey && e.shiftKey && e.keyCode == 73) {
        //         return false;
        //     } 
        //     if (e.ctrlKey && e.shiftKey && e.keyCode == 74) {
        //         return false;
        //     } 
        //     if (e.ctrlKey && e.keyCode == 85) {
        //         return false;
        //     } 
        // };

        // setInterval(function() {
        //     function detectDevTool() {
        //         const start = performance.now();
        //         debugger;
        //         return performance.now() - start > 100;
        //     }
        //     if (detectDevTool()) {
        //         alert("Developer Tools Disabled!");
        //         window.location.href = "about:blank"; 
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
    <script>
        $(document).ready(function() {

            // Add button click
            $(document).on('click', '.btn-add-slide', function(e) {
                e.stopPropagation();
                const card = $(this).closest('.product-card');
                const id = card.data('id');
                const name = card.data('name');
                const price = parseInt(card.data('price'));

                $(this).addClass('d-none');
                card.find('.quantity-counter').removeClass('d-none');
                card.find('.quantity').text(1);

                selectedProducts[id] = {
                    name,
                    price,
                    qty: 1
                };
                updateFooter();
            });

            // Plus
            $(document).on('click', '.btn-plus', function(e) {
                e.stopPropagation();
                const card = $(this).closest('.product-card');
                const id = card.data('id');
                let qty = parseInt(card.find('.quantity').text());
                qty++;
                card.find('.quantity').text(qty);

                if (selectedProducts[id]) {
                    selectedProducts[id].qty = qty;
                }
                updateFooter();
            });

            // Minus
            $(document).on('click', '.btn-minus', function(e) {
                e.stopPropagation();
                const card = $(this).closest('.product-card');
                const id = card.data('id');
                let qty = parseInt(card.find('.quantity').text());

                if (qty > 1) {
                    qty--;
                    card.find('.quantity').text(qty);
                    selectedProducts[id].qty = qty;
                } else {
                    card.find('.quantity-counter').addClass('d-none');
                    card.find('.btn-add-slide').removeClass('d-none');
                    delete selectedProducts[id];
                }
                updateFooter();
            });

            // Footer update function


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

</body>

</html>