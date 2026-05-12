@extends('layouts.front-end.app')
@section('title', translate('order_Track'))
@push('css_or_js')
    <link rel="stylesheet" href="{{ theme_asset('public/assets/front-end/plugin/intl-tel-input/css/intlTelInput.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
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
            font-size: 2rem;
            line-height: 1.54;
        }

        .rating__card__stars {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
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

        .star-rating {
            display: flex;
            gap: 5px;
            font-size: 30px;
            cursor: pointer;
        }

        .star-rating i {
            color: #fe9802;
            transition: color 0.2s;
        }

        .star-rating i.filled {
            color: #fe9802;
        }
    </style>
    </style>
@endpush
@section('content')
    <div class="container pb-5 mb-2 mb-md-4 mt-3 rtl __inline-47 text-align-direction">
        <div class="row g-3">
            @include('web-views.partials._profile-aside')
            <section class="col-lg-9">
                @include('web-views.users-profile.service-details.service-order-partial')
                <div class="card border-0">
                    <div class="card-body">
                        @if ($existingReview)
                            <!-- Display existing review comment and rating -->
                            <section class="rating__card text-center">
                                <blockquote class="rating__card__quote">“ {{ $existingReview->comment }} ”</blockquote>
                                <div class="rating__card__stars d-flex star-rating">
                                    @for ($i = 1; $i <= 5; $i++)
                                        @if ($i <= $existingReview->rating)
                                            <i class="fas fa-star"></i>
                                        @else
                                            <i class="far fa-star"></i>
                                        @endif
                                    @endfor
                                    <div class="w-100"></div>
                                    <span class="rating__card__stars__name">{{ $order['customer']['name'] }}</span>
                                </div>
                                <p class="rating__card__bottomText">
                                    {{ date('d M Y,h:i A, ', strtotime($existingReview['updated_at'])) }}
                                </p>
                            </section>
                        @else
                            <div class="px-4 lg:px-0 md:bg-[#FAFAFA]">
                                <div class="w-full top-12 md:static">
                                    <div class="md:max-w-screen-xl mx-auto flex flex-row items-center">
                                        <h4 class="inline m-0 font-bold text-BLACK-90">{{ translate('your') }}
                                            {{ $order['services']['name'] }}
                                            {{ translate('Puja_has_been_successfully_completed') }}
                                        </h4>
                                    </div>
                                </div>
                                <div class="max-w-screen-xl mx-auto md:mt-3">
                                    <span
                                        class="font-normal mt-3">{{ translate('Please_share_your_review_and_rating_to_help_other_devotees') }}</span>
                                </div>
                            </div>
                            <!-- Display review submission form -->
                            <form action="{{ route('submit_service_review', $order['order_id']) }}" method="post"
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
                                        <label>{{ translate('Give_Your_Rating_&_Feedback') }}</label>
                                        <div class="star-rating" id="starRating">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i class="far fa-star" data-index="{{ $i }}"></i>
                                            @endfor
                                        </div>
                                        <input type="hidden" name="rating" id="ratingInput" value="0">
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">{{ translate('comment') }}</label>
                                        <textarea class="form-control" name="comment" placeholder="Write your comments here"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="youtubeLink">
                                            {{ translate('YouTube Video Link (Optional)') }}
                                        </label>
                                        <div class="input-group">
                                            <input type="url" class="form-control" name="youtube_link" id="youtubeLink"
                                                placeholder="Paste your YouTube video link here">
                                            <div class="input-group-append">
                                                <a href="https://www.youtube.com" target="_blank" class="btn btn-danger"
                                                    title="Open YouTube">
                                                    <i class="fas fa-youtube"></i>
                                                </a>
                                            </div>
                                        </div>

                                        <small class="form-text text-muted">Share a YouTube link if you want to include a
                                            video with your review.</small>
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const stars = document.querySelectorAll('#starRating i');
            const ratingInput = document.getElementById('ratingInput');

            let currentRating = 0;

            stars.forEach(star => {
                star.addEventListener('click', function() {
                    const index = parseInt(this.getAttribute('data-index'));

                    if (index === currentRating) {
                        currentRating = 0;
                    } else {
                        currentRating = index;
                    }

                    ratingInput.value = currentRating;

                    stars.forEach((s, i) => {
                        if (i < currentRating) {
                            s.classList.remove('far');
                            s.classList.add('fas', 'filled');
                        } else {
                            s.classList.remove('fas', 'filled');
                            s.classList.add('far');
                        }
                    });
                });
            });
        });
    </script>
@endpush
