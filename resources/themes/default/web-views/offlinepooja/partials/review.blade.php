<div class="tab-pane fade show" id="reviews" role="tabpanel">
    <div class="row">
        @php
            $serviceReviews = $serviceReview ?? 0; 
            $reviewCounts = $reviewCounts ?? [
                'excellent' => 0,
                'good' => 0,
                'average' => 0,
                'below_average' => 0,
                'poor' => 0,
                'averageStar' => 0,
                'list' => [],
            ];

            $sumRatings =
                5 * $reviewCounts['excellent'] +
                4 * $reviewCounts['good'] +
                3 * $reviewCounts['average'] +
                2 * $reviewCounts['below_average'] +
                1 * $reviewCounts['poor'];

            $overallRating = $serviceReviews > 0
                ? number_format($sumRatings / $serviceReviews, 1)
                : 0;

            $overallRating = is_numeric($overallRating) ? $overallRating : 0;
            $fullStars = floor($overallRating);
            $halfStar = round($overallRating - $fullStars);
        @endphp

        <!-- Always Show Overall Rating -->
        <div class="col-lg-4 col-md-4">
            <div class="text-center text-capitalize">
                <p class="text-capitalize">
                    <big>
                        @for ($i = 1; $i <= 5; $i++)
                            @if ($i <= $fullStars)
                                <i class="fas fa-star text-primary"></i>
                            @elseif ($i == $fullStars + 1 && $halfStar)
                                <i class="fas fa-star-half-alt text-primary"></i>
                            @else
                                <i class="far fa-star text-primary"></i>
                            @endif
                        @endfor
                    </big>
                    <h1>({{ $overallRating }})</h1>
                </p>
            </div>
        </div>

        <!-- Only show Edited Reviews if exists -->
        <div class="col-lg-8 col-md-8">
            @if (!empty($reviewCounts['list']) && count($reviewCounts['list']) > 0)
                <div class="owl-theme owl-carousel review-slider">
                    @foreach ($reviewCounts['list'] as $poojaReview)
                        <div class="card product-single-hover shadow-none rtl">
                            <div class="card-body position-relative">
                                <div class="d-flex align-items-center">
                                    <img src="{{ getValidImage(path: 'storage/app/public/profile/' . ($poojaReview['userData']['image'] ?? ''), type: 'product') }}"
                                        alt="User Icon" class="user-icon"
                                        style="width: 50px; height: 50px; border-radius: 50%; margin-right: 10px;">
                                    <div>
                                        <p class="fw-bold m-0">
                                            {{ $poojaReview['userData']['name'] ?? 'user name' }}
                                        </p>
                                        <p class="m-0">
                                            <big class="small">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    @if ($poojaReview['rating'] >= $i)
                                                        <i class="fa fa-star text-warning"></i>
                                                    @elseif ($poojaReview['rating'] >= $i - 0.9)
                                                        <i class="fa fa-star-half-o text-warning"></i>
                                                    @else
                                                        <i class="fa fa-star-o text-muted"></i>
                                                    @endif
                                                @endfor
                                            </big>
                                        </p>
                                    </div>
                                </div>

                                <div class="single-review-details min-height-unset" style="height: 100px; overflow: hidden;">
                                    <div>
                                        <a class="text-capitalize fw-semibold review-comment">
                                            {{ $poojaReview['comment'] ?? '' }}
                                            @php $filePath = 'storage/event/comment/' . ($poojaReview['image'] ?? ''); @endphp
                                            @if ($poojaReview['image'] && file_exists($filePath))
                                                <img alt="{{ translate('product') }}"
                                                    src="{{ getValidImage(path: 'storage/app/public/event/comment/' . $poojaReview['image'], type: 'product') }}"
                                                    class='border border-light'
                                                    style="width:50px">
                                            @endif
                                        </a>
                                    </div>
                                    <a onclick="read(this)" class="read-more-btn">Read More...</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- No edited reviews message -->
                <div class="text-center text-capitalize">
                    <p class="text-capitalize">
                        <small>{{ translate('No_comment_given_yet') }}!</small>
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
