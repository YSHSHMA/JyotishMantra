@extends('layouts.front-end.app')
@section('title', translate('order_Track'))
@push('css_or_js')
    <link rel="stylesheet" href="{{ theme_asset('public/assets/front-end/plugin/intl-tel-input/css/intlTelInput.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .star-rating {
            color: orangered;
        }

        .rating__card {
            box-shadow: 0px 15px 50px -18px rgba(0, 0, 0, 0.3);
            box-sizing: border-box;
            padding: 30px;
            font-family: sans-serif;
            color: #545454;
            font-weight: 100;
            border-radius: 10px;
            background-size: cover;
            background: $df-bg-gray;
            width: 100%;
        }

        .rating__card__quote {
            padding: 30px 0;
            margin: 0;
            box-sizing: border-box;
            line-height: 1.2;
            font-size: 20px;
            line-height: 1.54;
        }

        .rating__card__stars {
            margin: auto;
            display: block;
            text-align: center;
        }

        .rating__card__stars__name {
            font-size: 16px;
            margin: 0 0 0 5px;
        }

        .rating__card__bottomText {
            margin: 30px auto 15px;
            text-align: center;
            font-size: 14px;
        }
    </style>
@endpush
@section('content')
    <div class="container pb-5 mb-2 mb-md-4 mt-3 rtl __inline-47 text-align-direction">
        <div class="row g-3">
            @include('web-views.partials._profile-aside')
            <section class="col-lg-9">
                @include('web-views.users-profile.chadhava-details.chadhava-order-partial')
                <div class="card border-0">
                    <div class="card-body">

                        @if ($existingReview)
                            <!-- Display existing review comment and rating -->
                            <section class="rating__card">
                                <blockquote class="rating__card__quote">“{{ $existingReview->comment }}”</blockquote>
                                <div class="rating__card__stars">
                                    @for ($i = 1; $i <= 5; $i++)
                                        @if ($i <= $existingReview->rating)
                                            <i class="fas fa-star star-rating"></i>
                                        @else
                                            <i class="far fa-star star-rating"></i>
                                        @endif
                                    @endfor
                                    <br>
                                    <span class="rating__card__stars__name">{{ $order['customer']['name'] }}</span>
                                </div>
                                <p class="rating__card__bottomText">
                                    {{ date('h:i A, d M Y', strtotime($existingReview['created_at'])) }}</p>
                            </section>
                        @else
                            <div class="px-4 lg:px-0 md:bg-[#FAFAFA]">
                                <div class="w-full top-12 md:static">
                                    <div class="md:max-w-screen-xl mx-auto flex flex-row items-center">
                                        <h4 class="inline m-0 font-bold text-BLACK-90">{{ translate('your') }}
                                            {{ $order['chadhava']['name'] }}
                                            {{ translate('Pooja_has_been_successfully_completed') }}</h4>
                                    </div>
                                </div>
                                <div class="max-w-screen-xl mx-auto md:mt-3">
                                    <span
                                        class="font-normal mt-3">{{ translate('Please_share_your_review_and_rating_to_help_other_devotees') }}</span>
                                </div>
                            </div>
                            <!-- Display review submission form -->
                            <form action="{{ route('submit_chadhava_review', $order['order_id']) }}" method="post"
                                enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="order_id" value="{{ $order->order_id }}">
                                <input type="hidden" name="user_id" value="{{ $order->customer_id }}">
                                <input type="hidden" name="service_id" value="{{ $order->service_id }}">
                                <input type="hidden" name="service_type" value="{{ $order->type }}">
                                <input type="hidden" name="astro_id"
                                    value="{{ isset($order['pandit']['id']) ? $order['pandit']['id'] : '' }}">

                                <div class="modal-body">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">{{ translate('Give_Your_Rating_&_Feedback') }}
                                            ⭐⭐⭐⭐⭐</label>
                                        <select class="form-control" name="rating">
                                            <option value="1">{{ translate('1') }}</option>
                                            <option value="2">{{ translate('2') }}</option>
                                            <option value="3">{{ translate('3') }}</option>
                                            <option value="4">{{ translate('4') }}</option>
                                            <option value="5">{{ translate('5') }}</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="exampleInputEmail1">{{ translate('comment') }}</label>
                                        <textarea class="form-control" name="comment" placeholder="Write your comments here"></textarea>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <a href="{{ url()->previous() }}"
                                        class="btn btn-secondary">{{ translate('back') }}</a>
                                    <button type="submit" class="btn btn-primary">{{ translate('submit') }}</button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection
@push('script')
    <script src="{{ theme_asset('public/assets/front-end/plugin/intl-tel-input/js/intlTelInput.js') }}"></script>
    <script src="{{ theme_asset('public/assets/front-end/js/country-picker-init.js') }}"></script>
@endpush
