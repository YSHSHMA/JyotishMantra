@extends('layouts.front-end.app')
@section('title', $chadhavaDetails['meta_title'])
@php
    use App\Utils\Helpers;
    use function App\Utils\getNextPoojaDay;
    use function App\Utils\getNextChadhavaDay;
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
        a.section-link.active {
            color: #ffffff !important;
            background: var(--base) !important;
            font-weight: bold;

        }

        a.section-link {
            border-radius: 100px !important;
            padding: 9px 17px;
            /* font-size: 13px; */
            text-decoration: none;
        }

        @media only screen and (max-width: 600px) {
            a.section-link {
                padding: 5px 8px;
                font-size: 10px;
            }

            .w-70 {
                width: 100% !important;
            }

            .font-10 {
                font-size: 14px;
                font-weight: 700;
            }

            .circle-img-container {
                width: 23px !important;
            }
        }

        .w-70 {
            width: 70%;
        }

        .nextbtn {
            display: flex;
            align-items: flex-start;
            flex-direction: column;
        }

        .fixedbtn {
            position: fixed;
            z-index: 1;
            bottom: 0;
            top: auto;
            left: 20%;
            right: 20%;
        }

        .nextbtn p,
        .nextbtn span {
            margin-right: 10px;
        }

        .symbolright i {
            color: #fff !important;
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
            aspect-ratio: unset;
        }

        .pooja-calendar {
            font-size: 16px;
            margin-bottom: 5px;
            font-weight: 800;
            margin-left: 8px;
        }

        .pooja-venue {
            font-size: 16px;
            margin-bottom: 5px;
            font-weight: 800;
            margin-left: 8px;

        }

        .product-preview-item {
            /* height: 60% !important; */
            aspect-ratio: unset;
        }

        .section-content {
            padding-bottom: 25px;
        }

        .partial-pooja {
            background: white;
            box-shadow: 0px 3px 6px rgb(0 0 0 / 29%);
            border-radius: 5px;
            border-top: 2px solid #fe9802;
        }

        .button-sticky {
            border-radius: 5px 5px 0 0;
            border: 1px solid rgba(20, 85, 172, 0.05);
            box-shadow: 0 -7px 30px 0 rgba(0, 113, 220, 0.1);
            position: sticky;
            bottom: 0;
            left: 0;
            z-index: 99;
            transition: all 150ms ease-in-out;
        }
        .review-content {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            font-size: 14px;
            height: 154px;
            text-align: center;
        }

        .owl-dots {
            top: 25px;
            position: relative !important;
        }
        @media (max-width: 768px) {
            .otp-input-fields input {
                height: 40px;
                width: 40px;
            }

            .otp-input-fields {
                gap: 9px;
            }
        }

        .product-name {
            font-size: 14px;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 180px;
            display: block;
        }
    </style>
@endpush

@section('content')
    <div class="w-full h-full sticky top-0 z-20 d-none d-md-block" style="top: 84px;">
        <div class="bg-bar w-full">
            <div class="d-flex overflow-x-scroll w-full scrollbar-hide max-w-screen-xl mx-auto"
                id="breadcrum-container-outer">
                <div class="d-flex flex-row items-center bg-bar h-14 px-4 md:px-0" id="breadcrum-container">
                    @include('web-views.chadhava.partials.statusbar')
                </div>
            </div>
        </div>
    </div>
    <div class="__inline-23">
        <div class="container mt-4 rtl text-align-direction">
            <div class="row {{ Session::get('direction') === 'rtl' ? '__dir-rtl' : '' }}">
                <div class="col-lg-12 col-12">
                    <div class="row">
                        <div class="col-lg-6 col-md-4 col-12">
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
                        <div class="col-lg-6 col-md-8 mt-md-0 mt-sm-3 web-direction">
                            <div class="details __h-100">
                                <span class="text-12 font-bold  line-clamp-2 text-ellipsis mb-0"
                                    style="color:#fe9802;">{{ strtoupper($chadhavaDetails->pooja_heading) }}
                                </span><br>

                                <div class="w-bar h-bar bg-gradient mt-2"></div>
                                <span class="mb-2 __inline-24">{{ $chadhavaDetails->name }}</span>
                                <p>{{ $chadhavaDetails->short_details }}</p>


                                <?php
                                
                                $ChadhavanextDate = '';
                                $ChadhavaWeek = json_decode($chadhavaDetails->chadhava_week);
                                $nextChadhavaDay = getNextChadhavaDay($ChadhavaWeek);
                                if ($nextChadhavaDay) {
                                    $ChadhavanextDate = $nextChadhavaDay->format('Y-m-d H:i:s');
                                }
                                $startDate = $chadhavaDetails->start_date;
                                $endDate = $chadhavaDetails->end_date;
                                $currentDate = time();
                                $formattedDates = [];
                                $ChadhavaearliestDate = '';
                                if ($startDate && $endDate && $startDate <= $endDate) {
                                    $currentDateIter = $startDate->copy();
                                    while ($currentDateIter <= $endDate) {
                                        $formattedDates[] = $currentDateIter->format('Y-m-d');
                                
                                        $currentDateIter->addDay();
                                    }
                                
                                    foreach ($formattedDates as $date) {
                                        if (strtotime($date) > $currentDate) {
                                            $ChadhavaearliestDate = date('d M, l', strtotime($date));
                                            break;
                                        }
                                    }
                                }
                                ?>
                                <div class="d-flex">
                                    <img src="{{ theme_asset(path: 'public\assets\front-end\img\track-order\temple.png') }}"
                                        alt="" style="width:24px;height:24px;">
                                    <p class="pooja-venue one-lines-only">{{ $chadhavaDetails->chadhava_venue }}</p>
                                </div>
                               
                                    <div class="d-flex">
                                        <img src="{{ theme_asset(path: 'public\assets\front-end\img\track-order\date.png') }}"
                                            alt="" style="width:24px;height:24px;">
                                        <p class="pooja-calendar">
                                            {{ date('d', strtotime($nextDate)) }},
                                            {{ translate(date('F', strtotime($nextDate))) }} ,
                                            {{ translate(date('l', strtotime($nextDate))) }}

                                        </p>
                                    </div>
                              

                               <!-- Profile Icon -->
                                 <div class="flex flex-col">
                                    <div class="flex flex-wrap justify-center items-center">
                                        <div class="w-70 d-flex justify-content-between">
                                           <div class="tray mb-3 ml-3 mr-0">
                                                @php
                                                    $uniqueUsers = range(0, 13);
                                                    shuffle($uniqueUsers);
                                                    $selectedUsers = array_slice($uniqueUsers, 0, 6);
                                                @endphp
                                                @foreach ($selectedUsers as $random_user)
                                                    <div class="relative circle-img-container">
                                                        <div class="bg-cover bg-center flex-shrink-0 cursor-pointer border border-4 border-white rounded-full circle-img"
                                                            style="background-image:url('{{ theme_asset(path: 'public/assets/user_list/user' . $random_user . '.jpg') }}')">
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <!-- Ratings Display -->
                                            @foreach ($chadhavaGet as $service)
                                                @php
                                                    $avgRating = (!empty($service->review_avg_rating) && $service->review_avg_rating > 0) ? $service->review_avg_rating : 5.0;
                                                    $fullStars = floor($avgRating);
                                                    $halfStar = $avgRating - $fullStars >= 0.5 ? 1 : 0;
                                                @endphp

                                                <div class="font-10">
                                                    <p
                                                        class="text-sm mt-4 font-medium border-b border-dashed border-primary font-weight-bold">
                                                        <i class="fas fa-star text-primary"></i>
                                                        {{ number_format($avgRating, 1) }}/5 (1K+
                                                        ratings)
                                                    </p>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <!-- Count number of People -->
                                <div class="mt-[1px] mb-3 md:mb-0 flex flex-col">
                                    <div class="flex flex-row mt-2 flex-nowrap break-normal leading-normal">
                                        <div class="flex">
                                            <div class=""><span
                                                    class=" inline-flex break-normal">{{ translate('Till_now') }}</span><span
                                                    class=" font-bold text-#F18912 ml-1 break-normal">10000 +<span
                                                        class=" ml-1 mr-1 inline-flex break-normal">{{ translate('Devotees') }}</span></span><span
                                                    class="text-">{{ translate('have_participated_in_Chadava_conducted_by_mahakal.com_Chadava_Seva') }}
                                                </span></div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Button -->
                                {{-- Product List Show --}}
                                <div class="flex flex-col">
                                    <div class="new_arrival_product">
                                        <div class="carousel-wrap">
                                            <div class="owl-carousel owl-theme chadhava-product">
                                                @php
                                                    $productIds = json_decode($chadhavaDetails->product_id, true);
                                                    $selected_product_array = [];
                                                    $ChadhavaProduct =
                                                        is_array($productIds) && !empty($productIds)
                                                            ? \App\Models\Product::whereIn('id', $productIds)->where('status', 1)->get()
                                                            : collect();
                                                @endphp
                                                @if (!empty($ChadhavaProduct))
                                                    @foreach ($ChadhavaProduct as $chadhava)
                                                        @include(
                                                            'web-views.chadhava.partials._chadhava_product',
                                                            ['chadhava' => $chadhava]
                                                        )
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- Product List Show --}}
                                <div id="" role="button">
                                    @php
                                        if (auth('customer')->check()) {
                                            $customer = App\Models\User::where('id', auth('customer')->id())->first();
                                        }
                                    @endphp
                                    @if (auth('customer')->check())
                                        <form class="needs-validation_" id="chadhavastore"
                                            action="{{ route('chadhava.lead.store') }}" method="post">
                                            @csrf
                                            <input type="hidden" name="service_id"  value="{{ $chadhavaDetails['id'] }}">
                                            <input type="hidden" name="product_id"  value="{{ $chadhavaDetails->product_id }}">
                                            @if ($chadhavaDetails->chadhava_type == 0)
                                                <input type="hidden" name="booking_date" id="poojaBook"   value="{{ date('Y-m-d', strtotime($ChadhavanextDate)) }}" placeholder="Pooja Weekly">
                                            @else
                                                <input type="hidden" name="booking_date" id="poojaBook"  value="{{ date('Y-m-d', strtotime($ChadhavaearliestDate)) }}" placeholder="Events">
                                            @endif
                                            <input class="form-control text-align-direction" type="hidden"
                                                value="{{ $customer['phone'] }}" name="person_phone" id="person-number"
                                                placeholder="{{ translate('enter_phone_number') }}" inputmode="number"
                                                maxlength="10" minlength="10"
                                                {{ isset($customer['phone']) ? 'readonly' : '' }} input-mode="number">

                                            <input class="form-control text-align-direction"
                                                value="{{ $customer['f_name'] }} {{ $customer['l_name'] }}"
                                                type="hidden" name="person_name" id="person-name"
                                                placeholder="{{ translate('Ex') }}: {{ translate('your_full_name') }}!"
                                                inputmode="name" {{ isset($customer['f_name']) ? 'readonly' : '' }}
                                                input-mode="text">
                                            <button type="submit" name="QueryForm"   class="btn btn--primary btn-block btn-shadow font-weight-bold d-none d-sm-block">{{ translate('Chadhava_Book_Now') }}</button>
                                        </form>
                                    @else
                                        <a href="javascript:void(0);" id="ChadhavaProduct-btn"
                                            class="btn btn--primary btn-block btn-shadow font-weight-bold d-none d-sm-block">{{ translate('Chadhava_Book_Now') }}</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>



        <div class="container-fluid" style="padding-left: 0 !important;padding-right:0 !important;">
            <div class="row">
                <div class="col-12">
                    <div class="navbar_section1 section-links w-100 mt-3 border-top border-bottom py-2 mb-4"
                        style="overflow: auto;">
                        <div class="d-flex justify-content-around">
                            <a class="section-link ml-2 active" href="#about_pooja">{{ translate('about_chadhava') }}</a>
                            <a class="section-link" href="#faqs"> {{ translate('faqs') }}</a>
                            <a class="section-link" href="#reviews">{{ translate('reviews') }}</a>


                        </div>
                    </div>
                    <div class="px-4 pb-3 mb-3 __rounded-10 pt-3">
                        <div class="content-sections px-lg-3">
                            <!-- Inclusion Section -->
                            <div class="section-content active" id="about_pooja">
                                <div class="row mt-2 p-3 partial-pooja">
                                    <!-- About Me -->
                                    <div class="ck-rendered-content">
                                        @include('web-views.chadhava.partials.aboutMe')
                                    </div>
                                </div>
                            </div>
                            <div class="section-content" id="faqs">
                                <div class="row mt-2 p-3 partial-pooja">
                                    @foreach ($Faqs as $faq)
                                        <div class="col-12">
                                            @include('web-views.chadhava.partials.faq', ['faq' => $faq])
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="section-content" id="reviews">
                                <div class="row mt-2 p-2 partial-pooja pb-4">
                                    <!-- Review -->
                                    <div class="col-12">
                                        @foreach ($chadhavaGet as $service)
                                            @include('web-views.chadhava.partials.review', [
                                                'servicesGet' => $service,
                                            ])
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="modal fade rtl text-align-direction" id="participateChadhavaModal" tabindex="-1" role="dialog"
        aria-labelledby="participateChadhavaModal" aria-hidden="true" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog" role="document">
            <div class="modal-content" style="border-top: 2px solid var(--base) !important;/">
                <div class="modal-header">
                    <div class="flex justify-center items-center my-3">
                        <span class="text-18 font-bold ml-2">{{ translate('Fill_your_details_for_Chadhava') }}</span>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <hr class="bg-[#E6E4EB] w-full">
                <div class="modal-body flex justify-content-center">
                    <div id="recaptcha-container"></div>
                    <div class="w-full mt-1 px-2">
                        <span
                            class="block text-[16px] font-bold text-gray-900 dark:text-white">{{ translate('enter_Your_Whatsapp_Mobile_Number') }}</span>
                        <span
                            class="text-[12px] font-normal text-[#707070]">{{ translate('Your_chadhava   _booking_updates_will_be_sent_on_below_WhatsApp_number') }}</span>
                        <div class="w-full mr-9 px-0 pt-3">
                            <form class="needs-validation_" id="lead-store-form"
                                action="{{ route('chadhava.lead.store') }}" method="post">
                                @csrf
                                @php
                                    if (auth('customer')->check()) {
                                        $customer = App\Models\User::where('id', auth('customer')->id())->first();
                                    }
                                @endphp
                                <input type="hidden" name="service_id" value="{{ $chadhavaDetails['id'] }}">
                                <input type="hidden" name="product_id" value="{{ $chadhavaDetails->product_id }}">
                                <input type="hidden" name="verify_otp" id="verifyOTP" value="0">
                                @if ($chadhavaDetails->chadhava_type == 0)
                                    <input type="hidden" name="booking_date" id="poojaBook"
                                        value="{{ date('Y-m-d', strtotime($ChadhavanextDate)) }}"
                                        placeholder="Pooja Weekly">
                                @else
                                    <input type="hidden" name="booking_date" id="poojaBook"
                                        value="{{ date('Y-m-d', strtotime($ChadhavaearliestDate)) }}"
                                        placeholder="Events">
                                @endif
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
                                                name="person_phone" id="person-number"inputmode="number"
                                                required maxlength="10" minlength="10"
                                                placeholder="{{ translate('phone_number') }}" required
                                                {{ isset($customer['phone']) ? 'readonly' : '' }}>

                                            <input type="hidden" class="country-picker-phone-number w-50"
                                                name="person_phone" readonly>

                                            <p id="number-validation" class="text-danger" style="display: none">Enter
                                                Your Valid Mobile Number</p>
                                        </div>
                                    </div>
                                    <div class="col-md-12" id="name-div">
                                        <div class="form-group">
                                            <label class="form-label font-semibold">{{ translate('your_name') }}</label>
                                            <input class="form-control text-align-direction"
                                                value="{{ !empty($customer['f_name']) ? $customer['f_name'] : '' }}{{ !empty($customer['l_name']) ? ' ' . $customer['l_name'] : '' }}"
                                                type="text" name="person_name" id="person-name"
                                                placeholder="{{ translate('Ex') }}: {{ translate('your_name') }}!"
                                                required {{ isset($customer['f_name']) ? 'readonly' : '' }}>
                                            <p id="name-validation" class="text-danger" style="display: none">Enter
                                                Your Name</p>
                                        </div>
                                    </div>
                                    <div class="col-md-12" id="otp-input-div" style="display: none;">
                                        <div class="form-group text-center">
                                            <label class="form-label font-semibold ">{{ translate('enter_OTP') }}</label>
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
                                    {{-- with mobile number register start --}}
                                    {{-- <div class="mx-auto mt-1" id="withoutOTPButton" style="display: none;">
                                            <button type="submit"
                                                class="btn btn--primary btn-block btn-shadow mt-1 font-weight-bold"
                                                style="width:445px">
                                                {{ translate('Book Chadhava') }}</button>
                                        </div> --}}
                                    {{-- with mobile number register end --}}
                                    <div class="mx-auto mt-1" id="send-otp-btn-div">
                                        <button type="button"
                                            class="btn btn--primary btn-block btn-shadow mt-1 font-weight-bold"
                                            id="send-otp-btn"> {{ translate('book_chadhava') }}
                                        </button>
                                         <button type="button" class="d-none btn btn--primary btn-block btn-shadow mt-1 font-weight-bold" id="withoutOTP"> {{ translate('book_chadhava') }}  </button>
                                        <p class="text-center mt-2" style="font-size: 10px;">
                                            {{ translate('By_tapping_the_"Book Pooja"_button_below_,_you_will_send_an_OTP_to_ your_mobile_number') }}
                                        </p>
                                    </div>

                                    <div class="mx-auto mt-1 __max-w-356" id="verify-otp-btn-div"
                                        style="display: none ;width:445px">
                                        <div class="d-flex">
                                            <button type="button"
                                                class="btn btn--primary btn-block btn-shadow mt-1 font-weight-bold me-2"
                                                id="otp-back-btn">
                                                {{ translate('back') }} </button>
                                            <button type="submit"
                                                class="btn btn--primary btn-block btn-shadow mt-1 font-weight-bold"
                                                id="verify-otp-btn" disabled>
                                                {{ translate('verify_OTP') }} </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-center mt-3" id="resend-div" style="display: none;">
                                    <p id="resend-otp-timer-text" style="display: none">
                                        {{ translate('Resend_OTP_in') }} <span id="resend-otp-timer"></span>
                                    </p>
                                    <p id="resend-otp-btn-text" style="display: none">
                                        {{ translate('Did_not_get_the_code?') }}? <a href="javascript:0"
                                            id="resend-otp-btn" style="color: blue;">{{ translate('Resend_OTP') }}</a>
                                    </p>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="button-sticky bg-white d-sm-none">
        <div class="d-flex flex-column gap-1 py-2">
            <div class="d-flex gap-3 justify-content-center" role="button">
                @if (auth('customer')->check())
                    <button type="submit" name="QueryForm"
                        class="btn btn--primary string-limit text-white h-full flex flex-row justify-center items-center"
                        onclick="chadhavaSubmit(this)">{{ translate('Chadhava_Book_Now') }}</button>
                @else
                    <a href="javascript:void(0);" id="ChadhavaProduct-btn1"
                        class="btn btn--primary string-limit text-white h-full flex flex-row justify-center items-center"
                        data-toggle="tab" role="tab">
                        <span class="font-bold">{{ translate('Chadhava_Book_Now') }}</span>
                    </a>
                @endif
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
    <script>
        $(document).ready(function() {
            const $stickyElement = $('.button-sticky');
            const $offsetElement = $('.partial-pooja');

            $(window).on('scroll', function() {
                const elementOffset = $offsetElement.offset().top;
                const scrollTop = $(window).scrollTop();

                if (scrollTop >= elementOffset) {
                    $stickyElement.addClass('stick');
                } else {
                    $stickyElement.removeClass('stick');
                }
            });
        });
    </script>
    <!-- Otp Send -->
    <script>
        const firebaseConfig = {
            apiKey: "{{ env('FIREBASE_APIKEY') }}",
            authDomain: "{{ env('FIREBASE_AUTHDOMAIN') }}",
            projectId: "{{ env('FIREBASE_PRODJECTID') }}",
            storageBucket: "{{ env('FIREBASE_STROAGEBUCKET') }}",
            messagingSenderId: "{{ env('FIREBASE_MESSAGINGSENDERID') }}",
            appId: "{{ env('FIREBASE_APPID') }}",
            measurementId: "{{ env('FIREBASE_MEASUREMENTID') }}"
        };
        firebase.initializeApp(firebaseConfig);
    </script>

    <script>
        $('#ChadhavaProduct-btn').click(function(e) {
            e.preventDefault();
            $('#participateChadhavaModal').modal('show');
        });
        $('#ChadhavaProduct-btn1').click(function(e) {
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
                $('#send-otp-btn').text('Please Wait ...');
                $('#send-otp-btn').prop('disabled', true);
                toastr.success('please wait...');
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
                        $('#slot-div').css('display', 'none');
                        $('#otp-input-div').css('display', 'block');
                        $('#verify-otp-btn-div').css('display', 'block');
                        otpTimer();
                        confirmationResult = confirmation;
                        toastr.success('otp sent successfully');
                        $('#send-otp-btn').prop('disabled', true);
                        $('#resend-div').show();
                    })
                    .catch(function(error) {
                        console.error('OTP sending error:', error);
                        toastr.error('Failed to send OTP. Please try again.');
                        $('#send-otp-btn').text('Book Pooja');
                        $('#send-otp-btn').prop('disabled', false);
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
            toastr.success('Please Wait....');
            var name = $('#person-name').val();
            var number = $('#person-number').val();
            var otp = $('#otp1').val() + $('#otp2').val() + $('#otp3').val() + $('#otp4').val() + $('#otp5').val() +
                $('#otp6').val();
            if (confirmationResult) {

                confirmationResult.confirm(otp).then(function(result) {
                        $(this).text('Please Wait ...');
                        $(this).prop('disabled', true);
                        toastr.success('Please Wait...');
                        $('#participateChadhavaModal').modal('hide');
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
            $('#slot-div').css('display', 'block');
            $('#otp-input-div').css('display', 'none');
            $('#verify-otp-btn-div').css('display', 'none');
            $('#send-otp-btn').prop('disabled', false);
            $('#send-otp-btn').text('Book Pooja');
            $('#resend-div').hide();

        });
    </script>

    <!-- OTP SECTION -->
    <script type="text/javascript">
        var otp_inputs = document.querySelectorAll(".otp__digit");
        const verifyOtpBtn = document.getElementById('verify-otp-btn');

        function checkOtpFields() {
            let allFilled = true;
            otp_inputs.forEach(field => {
                if (field.value === '') {
                    allFilled = false;
                }
            });
            verifyOtpBtn.disabled = !allFilled;
        }
        otp_inputs.forEach(field => {
            field.addEventListener('input', checkOtpFields);
        });
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
           // Without OTP LOGIN
        $('#withoutOTP').click(function(e) {
            e.preventDefault();
            $('#lead-store-form').submit();
        });
        // Without OTP LOGIN
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
                        $('#verifyOTP').val(1);
                    } else {
                        $(this).text('Please Wait ...');
                        $(this).prop('disabled', true);
                        $('#send-otp-btn').addClass('d-none');
                        $('#withoutOTP').removeClass('d-none');
                    }
                }
            });
        });
    </script>
    <script>
        function addChadhavaProduct(that) {
            var productid = $(that).data('productid');
            var ChadhavaPrice = $('#chdhavaPrice' + productid).val();
            var price = $(that).data('price');
            var name = $(that).data('name');
            var qtymin = $(that).data('qtymin');
            var event = $(that).data('event');
            var chadhavaid = $(that).data('chadhavaid');
            var currentCount = parseInt($('#chadhavaproductCount' + productid).text()) || 0;
            currentCount++;
            $('#chadhavaproductCount').text(currentCount);
            $.ajax({
                url: "{{ url('chadhava/add-chadhava-product') }}",
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
        //  Update the Code the Chadhava
        function updateProductCount() {
            $.ajax({
                type: 'GET',
                url: '/get-chadhava-products',
                success: function(response) {
                    console.log(response);
                    $('#product-count').html(response.count);
                }
            });
        }
        //   Update Qty
        function QuantityUpdate(cartId, quantity, updateid, pprice) {
            var inputBox = $('#cart_quantity_web' + cartId);
            console.log(inputBox.val());

            if (quantity == -1) {
                if (inputBox.val() == 1) {
                    deleteQuantity(updateid, cartId, pprice);
                }
                if (inputBox.val() == 2) {
                    $('#DeleteIcon' + cartId).addClass('tio-delete text-danger');
                    $('#DeleteIcon' + cartId).removeClass('tio-remove');
                    $('#get-view-by-onclick' + cartId).append();
                    toastr.warning('Quantity not Applicable.');
                    var newQuantity = parseInt(inputBox.val()) + quantity;
                    inputBox.val(newQuantity);
                    ProductQuantity(cartId, quantity, updateid, pprice, newQuantity);
                } else {
                    var newQuantity = parseInt(inputBox.val()) + quantity;
                    inputBox.val(newQuantity);
                    ProductQuantity(cartId, quantity, updateid, pprice, newQuantity);
                }
            } else {
                $('#DeleteIcon' + cartId).removeClass('tio-delete text-danger');
                $('#DeleteIcon' + cartId).addClass('tio-remove');
                var newQuantity = parseInt(inputBox.val()) + quantity;
                inputBox.val(newQuantity);
                ProductQuantity(cartId, quantity, updateid, pprice, newQuantity);
            }

            //
        }

        function ProductQuantity(cartId, quantity, updateid, pprice, newQuantity) {
            $.ajax({
                url: "{{ route('updateCartQuantity') }}",
                method: 'POST',
                data: {
                    updateid: updateid,
                    price: pprice,
                    cartId: cartId,
                    quantity: newQuantity,
                    _token: '{{ csrf_token() }}'
                },
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    toastr.success(response.message);
                    $('.totalProduct' + cartId).text(response.data.final_price.final_price + '.00');
                    $('#productCountFinal' + cartId).val(response.data.final_price);
                    $('#mainProductPriceInput').val(parseInt(poojaprice) + parseInt(response.data
                        .total_amount));
                    var localPrice = parseInt(response.data.total_amount) + parseInt(poojaprice) + '.00';
                    $('#mainProductPrice').text(localPrice) + '.00';
                    $('#mainProductPriceInput').text(localPrice) + '.00';
                    location.reload();
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        }
        // Delete Quantity
        function deleteQuantity(updateid, cartId, pprice) {
            $.ajax({
                url: "{{ route('deleteQuantity') }}",
                method: 'POST',
                data: {
                    pprice: pprice,
                    updateid: updateid,
                    _token: '{{ csrf_token() }}'
                },
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    toastr.success(response.message);
                    location.reload();

                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        }
    </script>
    {{-- Click thie miunx and plus --}}
    <script>
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
                        input.closest('.quantity-box').querySelector(
                            '.btn-number[data-type="minus"]').setAttribute('disabled', true);
                    } else {
                        input.closest('.quantity-box').querySelector(
                            '.btn-number[data-type="minus"]').removeAttribute('disabled');
                    }


                    if (input.value >= maxValue) {
                        input.closest('.quantity-box').querySelector(
                            '.btn-number[data-type="plus"]').setAttribute('disabled', true);

                    } else {
                        input.closest('.quantity-box').querySelector(
                            '.btn-number[data-type="plus"]').removeAttribute('disabled');
                    }
                    // QuantityUpdate(input.getAttribute('data-cart-id'), input.value);
                });
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            function getNavbarOffset() {
                return $('.navbar_section1').outerHeight() || 0;
            }

            $('.section-link').on('click', function(e) {
                e.preventDefault();

                const targetId = $(this).attr('href');
                $('html, body').animate({
                    scrollTop: $(targetId).offset().top - getNavbarOffset() - 20
                }, 200);
            });

            $(window).on('scroll resize', function() {
                const scrollTop = $(window).scrollTop() + getNavbarOffset() + 100;

                $(".navbar_section1").css({
                    'position': 'sticky',
                    'top': window.innerWidth <= 768 ? '0' : '8.7rem',
                    'left': '0',
                    'right': '0',
                    'background-color': '#fff',
                    'z-index': '1000',
                    'box-shadow': scrollTop > 900 ? '0 2px 10px rgba(0, 0, 0, 0.1)' : 'none',
                    'overflow': 'auto',
                });

                $('.section-content').each(function() {
                    const sectionTop = $(this).offset().top;
                    const sectionBottom = sectionTop + $(this).outerHeight();
                    const sectionId = $(this).attr('id');
                    const navLink = $(`.section-link[href="#${sectionId}"]`);

                    if (scrollTop >= sectionTop && scrollTop < sectionBottom) {
                        $('.section-link').removeClass('active');
                        navLink.addClass('active');
                    }
                });
            });

            $(window).trigger('scroll');
        });
    </script>
    <script>
        function chadhavaSubmit(button) {
            $('#chadhavastore').submit();

        }
    </script>
@endpush
