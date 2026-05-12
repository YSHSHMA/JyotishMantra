@extends('layouts.front-end.app')
@section('title', $chadhavaDetails['name'])
@php
@endphp
@push('css_or_js')
    <meta name="description" content="{{ $chadhavaDetails->slug }}">
    <meta name="keywords"
        content="@foreach (explode(' ', $chadhavaDetails['name']) as $keyword) {{ $keyword . ' , ' }} @endforeach">
    @if ($chadhavaDetails['meta_image'] != null)
        <meta property="og:image"
            content="{{ dynamicStorage(path: 'storage/app/public/product/meta') }}/{{ $chadhavaDetails->meta_image }}" />
        <meta property="twitter:card"
            content="{{ dynamicStorage(path: 'storage/app/public/product/meta') }}/{{ $chadhavaDetails->meta_image }}" />
    @else
        <meta property="og:image"
            content="{{ dynamicStorage(path: 'storage/app/public/product/thumbnail') }}/{{ $chadhavaDetails->thumbnail }}" />
        <meta property="twitter:card"
            content="{{ dynamicStorage(path: 'storage/app/public/product/thumbnail/') }}/{{ $chadhavaDetails->thumbnail }}" />
    @endif
    @if ($chadhavaDetails['meta_title'] != null)
        <meta property="og:title" content="{{ $chadhavaDetails->meta_title }}" />
        <meta property="twitter:title" content="{{ $chadhavaDetails->meta_title }}" />
    @else
        <meta property="og:title" content="{{ $chadhavaDetails->name }}" />
        <meta property="twitter:title" content="{{ $chadhavaDetails->name }}" />
    @endif
    <meta property="og:url" content="{{ route('product', [$chadhavaDetails->slug]) }}">
    @if ($chadhavaDetails['meta_description'] != null)
        <meta property="twitter:description" content="{!! Str::limit($chadhavaDetails['meta_description'], 55) !!}">
        <meta property="og:description" content="{!! Str::limit($chadhavaDetails['meta_description'], 55) !!}">
    @else
        <meta property="og:description"
            content="@foreach (explode(' ', $chadhavaDetails['name']) as $keyword) {{ $keyword . ' , ' }} @endforeach">
        <meta property="twitter:description"
            content="@foreach (explode(' ', $chadhavaDetails['name']) as $keyword) {{ $keyword . ' , ' }} @endforeach">
    @endif
    <meta property="twitter:url" content="{{ route('product', [$chadhavaDetails->slug]) }}">
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/poojafilter/poojadetails.css') }}">
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/product-details.css') }}" />
    <link rel="stylesheet"
        href="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/css/intlTelInput.css') }}">


    <style>
        .nextbtn {
            display: flex;
            align-items:flex-start;
            flex-direction: column;
        }
        .fixedbtn{
            position: fixed;
            z-index: 1;
            bottom: 0;
            top: auto;
            left: 20%;
            right: 20%;
        }
        .nextbtn p, .nextbtn span {
            margin-right: 10px;
        }

        .symbolright i{
            color: #fff!important;
        }
        .otp-input-fields {
            margin: auto;
            max-width: 400px;
            width: auto;
            display: flex;
            justify-content: center;
            gap: 20px;
            padding: 10px;
        }

        .otp-input-fields input {
            height: 50px;
            width: 50px;
            background-color: transparent;
            border-radius: 4px;
            border: 1px solid #2f8f1f;
            text-align: center;
            outline: none;
            font-size: 18px;
            /* Firefox */
        }

        .otp-input-fields input::-webkit-outer-spin-button,
        .otp-input-fields input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .otp-input-fields input[type=number] {
            -moz-appearance: textfield;
        }

        .otp-input-fields input:focus {
            border-width: 2px;
            border-color: #287a1a;
            font-size: 20px;
        }

        .product-preview-item {
            height: 60% !important;
        }
    </style>
@endpush

@section('content')
    <div class="__inline-23">
        <div class="container mt-4 rtl text-align-direction">
            <div class="row {{ Session::get('direction') === 'rtl' ? '__dir-rtl' : '' }}">
                <div class="col-lg-9 col-12">
                    <div class="row">
                        <div class="col-lg-5 col-md-5 col-12">
                            <div class="cz-product-gallery">
                                <div class="cz-preview">
                                    <div id="sync1" class="owl-carousel owl-theme product-thumbnail-slider">
                                        @if ($chadhavaDetails->images != null && json_decode($chadhavaDetails->images) > 0)
                                            @foreach (json_decode($chadhavaDetails->images) as $key => $photo)
                                                <div class="product-preview-item d-flex align-items-center justify-content-center {{ $key == 0 ? 'active' : '' }}"
                                                    id="image{{ $key }}">
                                                    <img class="cz-image-zoom img-responsive w-100"
                                                        src="{{ getValidImage(path: 'storage/app/public/chadhava/' . $photo, type: 'product') }}"
                                                        data-zoom="{{ getValidImage(path: 'storage/app/public/chadhavaD/' . $photo, type: 'product') }}"
                                                        alt="{{ translate('product') }}" width="">
                                                </div>
                                            @endforeach

                                        @endif
                                    </div>
                                </div>
                                <div class="d-flex flex-column gap-3">
                                    <button type="button" data-product-id="{{ $chadhavaDetails['id'] }}"
                                        class="btn __text-18px border wishList-pos-btn d-sm-none product-action-add-wishlist">
                                        <i class="fa fa-heart wishlist_icon_{{ $chadhavaDetails['id'] }} web-text-primary"
                                            aria-hidden="true"></i>
                                    </button>
                                    <div class="sharethis-inline-share-buttons share--icons text-align-direction">
                                    </div>
                                </div>
                                <div class="cz">
                                    <div class="table-responsive __max-h-515px" data-simplebar>
                                        <div class="d-flex">
                                            <div id="sync2" class="owl-carousel owl-theme product-thumb-slider">
                                                @if ($chadhavaDetails->images != null && json_decode($chadhavaDetails->images) > 0)
                                                    @foreach (json_decode($chadhavaDetails->images) as $key => $photo)
                                                        <div class="">
                                                            <a class="product-preview-thumb {{ $key == 0 ? 'active' : '' }} d-flex align-items-center justify-content-center"
                                                                id="preview-img{{ $key }}"
                                                                href="#image{{ $key }}">
                                                                <img alt="{{ translate('product') }}"
                                                                    src="{{ getValidImage(path: 'storage/app/public/chadhava/' . $photo, type: 'product') }}">
                                                            </a>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-8 col-4 mt-md-0 mt-sm-3 web-direction">
                            <div class="details __h-100">
                                <span class="mb-2 __inline-24">{{ $chadhavaDetails['chadhava_venue'] }}</span>
                                <div class="d-flex flex-wrap align-items-center mb-2 pro">
                                    <div class="star-rating me-2">
                                        <i class="tio-star-outlined text-warning"></i>
                                        <i class="tio-star-outlined text-warning"></i>
                                        <i class="tio-star-outlined text-warning"></i>
                                        <i class="tio-star-outlined text-warning"></i>
                                        <i class="tio-star-outlined text-warning"></i>
                                    </div>
                                    <span class="d-inline-block  align-middle mt-1 mr-md-2 mr-sm-0 fs-14 text-muted">(0)</span>
                                </div>
                                <div class="row pt-2 specification">   
                                    @if ($chadhavaDetails['details'])
                                    {!! $chadhavaDetails['details'] !!}
                                    @else
                                    <p class="text-center my-5">Detail Not Found</p>
                                    @endif
                                </div>
                                <!-- Profile Icon -->
                                <div class="flex flex-col">
                                    <div class="flex flex-wrap justify-center items-center">
                                        <div class="w-full">
                                            <div class="w-full tray mb-3">
                                                <div class="relative circle-img-container">
                                                    <div class="bg-cover bg-center flex-shrink-0 cursor-pointer border border-4 border-white rounded-full circle-img"
                                                        style="background-image:url('https://raw.githubusercontent.com/go2garret/Bobble-Head-Images/main/dist/img/1.jpg')">
                                                    </div>
                                                </div>
                                                <div class="relative circle-img-container">
                                                    <div class="bg-cover bg-center flex-shrink-0 cursor-pointer border border-4 border-white rounded-full circle-img"
                                                        style="background-image:url('https://raw.githubusercontent.com/go2garret/Bobble-Head-Images/main/dist/img/2.jpg')">
                                                    </div>
                                                </div>
                                                <div class="relative circle-img-container">
                                                    <div class="bg-cover bg-center flex-shrink-0 cursor-pointer border border-4 border-white rounded-full circle-img"
                                                        style="background-image:url('https://raw.githubusercontent.com/go2garret/Bobble-Head-Images/main/dist/img/3.jpg')">
                                                    </div>
                                                </div>
                                                <div class="relative circle-img-container">
                                                    <div class="bg-cover bg-center flex-shrink-0 cursor-pointer border border-4 border-white rounded-full circle-img"
                                                        style="background-image:url('https://raw.githubusercontent.com/go2garret/Bobble-Head-Images/main/dist/img/4.jpg')">
                                                    </div>
                                                </div>
                                                <div class="relative circle-img-container">
                                                    <div class="bg-cover bg-center flex-shrink-0 cursor-pointer border border-4 border-white rounded-full circle-img"
                                                        style="background-image:url('https://raw.githubusercontent.com/go2garret/Bobble-Head-Images/main/dist/img/5.jpg')">
                                                    </div>
                                                </div>
                                                <div class="relative circle-img-container">
                                                    <div class="bg-cover bg-center flex-shrink-0 cursor-pointer border border-4 border-white rounded-full circle-img"
                                                        style="background-image:url('https://raw.githubusercontent.com/go2garret/Bobble-Head-Images/main/dist/img/6.jpg')">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Count number of People -->
                                <div class="mt-[1px] mb-3 md:mb-0 flex flex-col">
                                    <div class="flex flex-row mt-2 flex-nowrap break-normal leading-normal">
                                        <div class="flex">
                                            <div class=""><span class=" inline-flex break-normal">Till
                                                    now</span><span
                                                    class=" font-bold text-#F18912 ml-1 break-normal">{{ \App\Models\Service_order::where('type', 'vip')->count() }}+<span
                                                        class=" ml-1 mr-1 inline-flex break-normal">Customers</span></span><span
                                                    class="text-">have sucessfully counsultancy results conducted
                                                    by<strong> mahakal.com </strong></span></div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Button -->
                                {{-- <div id="" role="button">
                                    <a href="javascript:void(0);" id="participate-btn" class="btn btn--primary btn-block btn-shadow mt-4 font-weight-bold">Chadhava Book Now</a>
                                </div> --}}
                            </div>
                        </div>
                    </div>
                </div>
               
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="pt-3 pb-3">
                        <span class=" __text-16px font-bold text-capitalize">
                           {{ translate('List of all availabel offerings in this temple')}}                           
                        </span>
                    </div>
                    <div>
                        <div class="row">
                            @php
                                $selected_product_array = [];
                                $ChadhavaProduct = \App\Models\Product::where('product_type','pooja')->get();
                            @endphp
                            @if(!empty($ChadhavaProduct))
                                @foreach ($ChadhavaProduct as $productChadhava)
                                    <div class="col-md-4 pb-2">
                                        @if (!in_array($productChadhava->id, $selected_product_array))
                                            @include('web-views.chadhava.partials._chadhava-product-lsit',['productChadhava' => $productChadhava])
                                        @endif
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
                
                
            </div>
            <div class="mt-4 col-12 rtl text-align-direction">
                <div class="row">
                    <div class="col-12">
                        <div class="px-4 pb-3 mb-3 mr-0 mr-md-2 __review-overview __rounded-10 pt-3">
                            <div class="tab-content px-lg-3">
                                <div>
                                    <span class="font-bold pl-1">Frequently Asked Questions</span>
                                 </div>
                                @foreach ($Faqs as $faq)
                                <div class="row pt-2 specification">
                                    <div class="col-12 col-md-12 col-lg-12">
                                        <div class="accordion" id="accordionExample">

                                            <div class="cards">
                                                <div class="card-header" id="heading{{ $faq->id }}">
                                                    <h2 class="mb-0">
                                                        <button class="btn btn-link btn-block  text-left btnClr"
                                                            type="button" data-toggle="collapse"
                                                            data-target="#collapse{{ $faq->id }}"
                                                            aria-expanded="true" aria-controls="collapseOne">
                                                            {{ $faq->question }}
                                                        </button>
                                                    </h2>
                                                </div>
                                                <div id="collapse{{ $faq->id }}" class="collapse"
                                                    aria-labelledby="heading{{ $faq->id }}"
                                                    data-parent="#accordionExample">
                                                    <div class="card-body">
                                                        {!! $faq->detail !!}
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @php 
        // dd(Session::get('chadhava_products'));
        $products = Session::get('chadhava_products', []);
        @endphp
        
        <div class="row fixedbtn">
                <div class="col-md-12 col-lg-12">
                    <div class="pt-3 pb-3">
                        <div class="nextProcessbtn" style="display:{{Session::has('chadhava_products') ? 'block' : 'none'}}">
                            <a class="btn btn--primary text-uppercase d-flex align-items-center" href="#" id="participate-btn"> 
                                <div class="nextbtn">
                                    <div class="countPrice">
                                        <span id="chadhavaproductCount"></span>
                                        <span class="mr-2">offerings</span>
                                        <span id="total-price">₹ 0.00 /-</span>
                                    </div>
                                    <div class="chadavavenue">
                                        <span class="mr-2">{{ $chadhavaDetails['chadhava_venue'] }}</span>
                                    </div>
                                </div>
                                <div class="symbolright justify-content-end" style="margin-left:20rem">
                                    <span class="font-size-sm mr-2">Next</span>
                                    <i class="czi-arrow-right"></i>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
          
            <div class="modal fade rtl text-align-direction" id="participateChadhavaModal" tabindex="-1" role="dialog"
            aria-labelledby="participateChadhavaModal" aria-hidden="true" data-keyboard="false" data-backdrop="static">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="flex justify-center items-center my-3">
                            <span class="text-18 font-bold ml-2">Fill your details for Chadhava</span>
                        </div>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <hr class="bg-[#E6E4EB] w-full">
                    <div class="modal-body flex justify-content-center">
                        <div id="recaptcha-container"></div>
                        <div class="w-full mt-1 px-2">
                            <span class="block text-[16px] font-bold text-gray-900 dark:text-white">Enter Your Whatsapp Mobile Number</span>
                            <span class="text-[12px] font-normal text-[#707070]">Your Chadhava booking updates will be sent on below WhatsApp number.</span>
                            <!-- Model Form -->
                            <div class="w-full mr-9 px-0 pt-3">
                                <form class="needs-validation_" id="lead-store-form"  action="{{ route('chadhava.lead.store') }}" method="post">
                                    @csrf
                                    @php
                                        if (auth('customer')->check()) {
                                            $customer = App\Models\User::where('id', auth('customer')->id())->first();
                                        }
                                    @endphp
                                    <input type="hidden" name="chadhava_id" value="{{ $chadhavaDetails['id'] }}">
                                    <input type="hidden" name="service_price" value="{{ $chadhavaDetails['counselling_selling_price'] }}">
                                    <div class="row">
                                        <div class="col-md-12" id="phone-div">
                                            <div class="form-group">
                                                <label class="form-label font-semibold">{{ translate('phone_number') }}
                                                    <small class="text-primary">( *
                                                        {{ translate('country_code_is_must_like_for_IND') }} 91 )</small>
                                                </label>
                                                <input
                                                    class="form-control text-align-direction phone-input-with-country-picker"
                                                    type="tel"
                                                    value="{{ isset($customer['phone']) ? $customer['phone'] : '' }}"
                                                    name="person_phone" id="person-number"
                                                    placeholder="{{ translate('enter_phone_number') }}" required
                                                    {{ isset($customer['phone']) ? 'readonly' : '' }}>

                                                <input type="hidden" class="country-picker-phone-number w-50"
                                                    name="person_phone" readonly>

                                                <p id="number-validation" class="text-danger" style="display: none">Enter
                                                    Your Valid Mobile Number</p>
                                            </div>
                                        </div>
                                        <div class="col-md-12" id="name-div">
                                            <div class="form-group">
                                                <label
                                                    class="form-label font-semibold">{{ translate('your_name') }}</label>
                                                <input class="form-control text-align-direction"
                                                    value="{{ !empty($customer['f_name']) ? $customer['f_name'] : '' }}{{ !empty($customer['l_name']) ? ' ' . $customer['l_name'] : '' }}"
                                                    type="text" name="person_name" id="person-name"
                                                    placeholder="{{ translate('Ex') }}: {{ translate('your_full_name') }}!"
                                                    required {{ isset($customer['f_name']) ? 'readonly' : '' }}>
                                                <p id="name-validation" class="text-danger" style="display: none">Enter
                                                    Your Name</p>
                                            </div>
                                        </div>
                                        <div class="col-md-12" id="otp-input-div" style="display: none;">
                                            <div class="form-group text-center">
                                                <label
                                                    class="form-label font-semibold ">{{ translate('enter_OTP') }}</label>
                                                <div class="otp-input-fields">
                                                    <input type="number" id="otp1" class="otp__digit otp__field__1"
                                                        inputmode="number">
                                                    <input type="number" id="otp2" class="otp__digit otp__field__2"
                                                        inputmode="number">
                                                    <input type="number" id="otp3" class="otp__digit otp__field__3"
                                                        inputmode="number">
                                                    <input type="number" id="otp4" class="otp__digit otp__field__4"
                                                        inputmode="number">
                                                    <input type="number" id="otp5" class="otp__digit otp__field__5"
                                                        inputmode="number">
                                                    <input type="number" id="otp6" class="otp__digit otp__field__6"
                                                        inputmode="number">
                                                </div>
                                                <p id="otpValidation" class="text-danger"></p>

                                            </div>
                                        </div>
                                        <div class="mx-auto mt-1 __max-w-356" id="send-otp-btn-div">
                                            <button type="button"
                                                class="btn btn--primary btn-block btn-shadow mt-1 font-weight-bold"
                                                id="send-otp-btn"> {{ translate('send_OTP') }} </button>
                                        </div>

                                        <div class="mx-auto mt-1 __max-w-356" id="verify-otp-btn-div"
                                            style="display: none">
                                            <div class="d-flex">
                                                <button type="button"
                                                    class="btn btn--primary btn-block btn-shadow mt-1 font-weight-bold me-2"
                                                    id="otp-back-btn">
                                                    {{ translate('back') }} </button>
                                                <button type="submit"
                                                    class="btn btn--primary btn-block btn-shadow mt-1 font-weight-bold"
                                                    id="verify-otp-btn">
                                                    {{ translate('verify_OTP') }} </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-center mt-3" id="resend-div" style="display: none;">
                                        <p id="resend-otp-timer-text" style="display: none"> Resend Otp in <span
                                                id="resend-otp-timer"></span></p>
                                        <p id="resend-otp-btn-text" style="display: none">Didn't get the code? <a
                                                href="javascript:0" id="resend-otp-btn" style="color: blue;">Resend
                                                Otp</a></p>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/owl.carousel.min.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/home.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/responsiveslides.min.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/jquery.mixitup.min.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/js/intlTelInput.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/country-picker-init.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
    <!-- Firbase CDN -->
    <script src="https://www.gstatic.com/firebasejs/8.6.1/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.6.1/firebase-auth.js"></script>

    <!-- Otp Send -->
    <script>
        const firebaseConfig = {
            apiKey: "AIzaSyBrrMSAtiASPJGKt0aAQqpIYXoFUG4QGv8",
            authDomain: "rizrv-65a76.firebaseapp.com",
            projectId: "rizrv-65a76",
            storageBucket: "rizrv-65a76.appspot.com",
            messagingSenderId: "832249240595",
            appId: "1:832249240595:web:083a86aed6f3f50fa133e6",
            measurementId: "G-GXM9FXY2WM"
        };
        firebase.initializeApp(firebaseConfig);
    </script>

    <script>
        $('#participate-btn').click(function(e) {
            e.preventDefault();
            $('#participateChadhavaModal').modal('show');
        });

        // OTP SEND THE MODEL
        var confirmationResult;
        var appVerifier = "";
        $('#send-otp-btn').click(function(e) {
            e.preventDefault();
            var name = $('#person-name').val();
            var number = $('#person-number').val();
            var phoneNumber = '+91 ' + $('#person-number').val();
            sendotp();
            toastr.success('Please Wait...');
        });


        function sendotp() {
            var name = $('#person-name').val();
            var number = $('#person-number').val();
            if (number == "" || number.length != 10) {
                $('#number-validation').show();
            } else if (name == "") {
                $('#number-validation').hide();
                $('#name-validation').show();
            } else {
                var phoneNumber = '+91 ' + $('#person-number').val();
                if (appVerifier == "") {
                    appVerifier = new firebase.auth.RecaptchaVerifier('recaptcha-container', {
                        size: 'invisible'
                    });
                }

                firebase.auth().signInWithPhoneNumber(phoneNumber, appVerifier).then(function(confirmation) {
                        $('#name-validation').hide();
                        $('#number-validation').hide();
                        $('#send-otp-btn-div').css('display', 'none');
                        $('#phone-div').css('display', 'none');
                        $('#name-div').css('display', 'none');
                        $('#otp-input-div').css('display', 'block');
                        $('#verify-otp-btn-div').css('display', 'block');
                        otpTimer();
                        confirmationResult = confirmation;
                        toastr.success('otp sent successfully');
                        $('#send-otp-btn').prop('disabled', true);
                        $('#resend-div').show();
                    })
                    .catch(function(error) {
                        toastr.success('Failed to send OTP. Please try again.');
                       
                    });
            }
        }

        // otp timer
        function otpTimer() {
            $('#resend-otp-timer-text').css('display', 'block');
            $('#resend-otp-btn-text').css('display', 'none');
            var resendOtpTimer = 30;
            var interval = setInterval(() => {
                resendOtpTimer--;
                $('#resend-otp-timer').text(resendOtpTimer);
                if (resendOtpTimer <= 0) {
                    $('#resend-otp-timer-text').css('display', 'none');
                    $('#resend-otp-btn-text').css('display', 'block');
                    clearInterval(interval);
                }
            }, 1000);
        }

        // resend otp
        $('#resend-otp-btn').click(function(e) {
            e.preventDefault();

            var phoneNumber = '+91 ' + $('#person-number').val();
            if (!appVerifier) {
                appVerifier = new firebase.auth.RecaptchaVerifier('recaptcha-container', {
                    size: 'invisible'
                });
            }
            firebase.auth().signInWithPhoneNumber(phoneNumber, appVerifier).then(function(confirmation) {
                    confirmationResult = confirmation;
                    otpTimer();
                    toastr.success('otp resent successfully');
                })
                .catch(function(error) {
                    alert('Failed to send OTP. Please try again.');
                });
        });

        $('#verify-otp-btn').click(function(e) {
            e.preventDefault();

            var name = $('#person-name').val();
            var number = $('#person-number').val();
            var otp = $('#otp1').val() + $('#otp2').val() + $('#otp3').val() + $('#otp4').val() + $('#otp5').val() +
                $('#otp6').val();
            if (confirmationResult) {
                confirmationResult.confirm(otp).then(function(result) {
                        $(this).text('Please Wait ...');
                        $(this).prop('disabled', true);
                        toastr.success('Please Wait...');
                        $('#lead-store-form').submit();
                    })
                    .catch(function(error) {
                        $('#otpValidation').text('Incorrect Otp');
                        // $('#submit').text('Submit');
                        // $('#submit').prop('disabled', false);
                    });
            }


        });

        $('#otp-back-btn').click(function(e) {
            e.preventDefault();

            $('#send-otp-btn-div').css('display', 'block');
            $('#phone-div').css('display', 'block');
            $('#name-div').css('display', 'block');
            $('#otp-input-div').css('display', 'none');
            $('#verify-otp-btn-div').css('display', 'none');
            $('#send-otp-btn').prop('disabled', false);
            $('#resend-div').hide();
        });
    </script>

    <!-- OTP SECTION -->
    <script type="text/javascript">
        var otp_inputs = document.querySelectorAll(".otp__digit")
        var mykey = "0123456789".split("")
        otp_inputs.forEach((_) => {
            _.addEventListener("keyup", handle_next_input)
        })

        function handle_next_input(event) {
            let current = event.target
            let index = parseInt(current.classList[1].split("__")[2])
            current.value = event.key

            if (event.keyCode == 8 && index > 1) {
                current.previousElementSibling.focus()
            }
            if (index < 6 && mykey.indexOf("" + event.key + "") != -1) {
                var next = current.nextElementSibling;
                next.focus()
            }
            var _finalKey = ""
            for (let {
                    value
                }
                of otp_inputs) {
                _finalKey += value
            }
        }
    </script>
    {{-- mobile no blur --}}
    <script>
        $('#person-number').blur(function(e) {
            e.preventDefault();
            var code = $('.iti__selected-dial-code').text();
            var mobile = $(this).val();
            var no = code + '' + mobile;
            console.log(code);
            console.log(mobile);
            $.ajax({
                type: "get",
                url: "{{ url('account-counselling-order-user-name') }}" + "/" + no,
                success: function(response) {
                    console.log(response);
                    if (response.status == 200) {
                        var name = response.user.f_name + ' ' + response.user.l_name;
                        $('#person-name').val(name);
                    }
                }
            });
        });
    </script>
    <script>
        function addChadhavaProduct(that) {
        var productid = $(that).data('productid');
        var ChadhavaPrice= $('#chdhavaPrice'+productid).val();
        var price = $(that).data('price');
        var name = $(that).data('name');
        var qtymin = $(that).data('qtymin');
        var event = $(that).data('event');
        var chadhavaid = $(that).data('chadhavaid');
        var currentCount = parseInt($('#chadhavaproductCount' + productid).text()) || 0;
        currentCount++;
        $('#chadhavaproductCount').text(currentCount);
        $.ajax({
            url: '{{ url('chadhava/add-chadhava-product') }}', 
            method: 'POST',
            data: {
                productid: productid,
                name: name,
                price: price,
                qtymin: qtymin,
                event: event,
                chadhavaid: chadhavaid,
            },
            headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
            success: function(response) {
                $('#chadhavaproductCount').text(response.count); 
                $('#total-price').text(response.totalPrice);
            },
            error: function(xhr) {
                console.error(xhr.responseText);
            }
        });
            $('.nextProcessbtn').show();
            $('#qtyChadhava-' + productid).show();
            $('#addtoBtn-' + productid).hide();
        }
        function ChadhavaQuntityUpdate(productid, quantity, chadhavaid, pprice) {
            var inputBox = $('#chdhavaInput' + productid);
            console.log(inputBox.val());
            if (quantity == -1) {
                if (inputBox.val() == 1) {
                    $('#qtyChadhava-' + productid).hide();
                    $('#addtoBtn-' + productid).show();
                    DeleteChadhavaProduct(productid, quantity, chadhavaid, pprice);
                }
                    if (inputBox.val() == 1) {
                        $('#DeleteIcon' + productid).addClass('tio-delete text-danger');
                        $('#DeleteIcon' + productid).removeClass('tio-remove');
                        $('#get-view-by-onclick' + productid).append();
                        toastr.warning('Quantity not Applicable.');
                        var newQuantity = parseInt(inputBox.val()) + quantity;
                        inputBox.val(newQuantity);
                        UpdateChadhavaProduct(productid, quantity, chadhavaid, pprice, newQuantity);
                    } else {
                        var newQuantity = parseInt(inputBox.val()) + quantity;
                        inputBox.val(newQuantity);
                        UpdateChadhavaProduct(productid, quantity, chadhavaid, pprice, newQuantity);
                    }
            } else {
                $('#DeleteIcon' + productid).removeClass('tio-delete text-danger');
                $('#DeleteIcon' + productid).addClass('tio-remove');
                var newQuantity = parseInt(inputBox.val()) + quantity;
                inputBox.val(newQuantity);
                UpdateChadhavaProduct(productid, quantity, chadhavaid, pprice, newQuantity);
            }
        }

        function UpdateChadhavaProduct(productid, quantity, chadhavaid, pprice, newQuantity) {
            $.ajax({
                url: '{{ url('chadhava/update-chadhava-product') }}', 
                method: 'POST',
                data: {
                    chadhavaid: chadhavaid,
                    price: pprice,
                    productid: productid,
                    quantity: newQuantity,
                    _token: '{{ csrf_token() }}'
                },
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    toastr.success(response.message);
                    $('#chadhavaproductCount').text(response.count); 
                    $('#total-price').text(response.totalPrice);
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        }
        // Delete Quantity
        function DeleteChadhavaProduct(chadhavaid, productid,pprice) {
            $.ajax({
                url: '{{ url('chadhava/delete-chadhava-product') }}', 
                method: 'POST',
                data: {
                    pprice: pprice,
                    chadhavaid: chadhavaid,
                    _token: '{{ csrf_token() }}'
                },
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    toastr.success(response.message);
                    $('#chadhavaproductCount').text(response.count); 
                    $('#total-price').text(response.totalPrice);                   
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        }
    </script>
    {{-- Click thie miunx and plus --}}
    {{-- <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.btn-number').forEach(function(button) {
                button.addEventListener('click', function(event) {
                    event.preventDefault();
                    const button = event.currentTarget;
                    const input = button.closest('.quantity-box').querySelector('.input-number');
                    const minValue = parseInt(input.getAttribute('data-minimum-order'));
                    const maxValue = parseInt(input.getAttribute('max'));
                    let currentValue = parseInt(input.value);
        
                    if (isNaN(currentValue)) {
                        currentValue = minValue;
                    }
        
                    if (button.getAttribute('data-type') === 'minus') {
                        if (currentValue > minValue) {
                            input.value = currentValue - 1;
                        }
                    } else if (button.getAttribute('data-type') === 'plus') {
                        if (currentValue < maxValue) {
                            input.value = currentValue + 1;
                        }
                    }
        
                  
                    if (input.value <= minValue) {
                        input.closest('.quantity-box').querySelector('.btn-number[data-type="minus"]').setAttribute('disabled', true);
                    } else {
                        input.closest('.quantity-box').querySelector('.btn-number[data-type="minus"]').removeAttribute('disabled');
                    }
        
                 
                    if (input.value >= maxValue) {
                        input.closest('.quantity-box').querySelector('.btn-number[data-type="plus"]').setAttribute('disabled', true);
                    
                    } else {
                        input.closest('.quantity-box').querySelector('.btn-number[data-type="plus"]').removeAttribute('disabled');
                    }
                    // QuantityUpdate(input.getAttribute('data-cart-id'), input.value);
                });
            });
        });
        </script> --}}
@endpush
