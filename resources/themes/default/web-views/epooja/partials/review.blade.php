<div class="tab-pane fade show" id="reviews" role="tabpanel">
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="mx-auto max-w-screen-sm text-center mb-4">
                <h2 class="text-2xl font-bold tracking-tight mt-2">Reviews & Ratings</h2>
                <span class="text-base font-normal">Read what our beloved devotees have to say about Mahakal.com.</span>
            </div>
        </div>
    </div>
    <div class="row">
        @php
            $avgRating = $servicesGet->review_avg_rating ?? 0;
            $fullStars = floor($avgRating);
            $halfStar = $avgRating - $fullStars >= 0.5 ? 1 : 0;
        @endphp

        {{-- <!-- Always Show Overall Rating -->
        <div class="col-lg-4 col-md-4">
            <div class="text-center text-capitalize">
                <p class="text-capitalize">
                    <big>
                        @for ($i = 1; $i <= 5; $i++)
                            @php $delay = $i * 0.1; @endphp
                            @if ($i <= $fullStars)
                                <i class="fas fa-star text-primary star-animate"
                                    style="animation-delay: {{ $delay }}s;"></i>
                            @elseif ($i == $fullStars + 1 && $halfStar)
                                <i class="fas fa-star-half-alt text-primary star-animate"
                                    style="animation-delay: {{ $delay }}s;"></i>
                            @else
                                <i class="far fa-star text-primary star-animate"
                                    style="animation-delay: {{ $delay }}s;"></i>
                            @endif
                        @endfor
                    </big>
                </p>
                <h1 id="rating-number" data-rating="{{ $avgRating }}">{{ number_format($avgRating, 1) }}/5</h1>

            </div>
        </div> --}}
        <!-- Only show Edited Reviews if exists -->
        <div class="col-lg-12 col-md-12">
            @if (!empty($reviewCounts['list']) && count($reviewCounts['list']) > 0)
                <div class="owl-theme owl-carousel review-slider">
                    <!-- 🌟 Video Slide -->
                    @if (!empty($epooja->video_url))
                        @php
                            $videoId = '';
                            if (
                                preg_match(
                                    '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i',
                                    $epooja->video_url,
                                    $matches,
                                )
                            ) {
                                $videoId = $matches[1];
                            }
                        @endphp
                        @if ($videoId)
                            <div class="card product-single-hover shadow-none rtl">
                                <div class="card-body position-relative">
                                    <div class="ratio ratio-16x9">
                                        <iframe width="100%" height="100%"
                                            src="https://www.youtube.com/embed/{{ $videoId }}" frameborder="0"
                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                            allowfullscreen style="border-radius: 10px;"></iframe>
                                    </div>
                                    <div class="d-flex align-items-center mt-2">
                                        <img src="{{ asset('public/images/default.png') }}" alt="User Icon"
                                            class="user-icon"
                                            style="width: 50px; height: 50px; border-radius: 50%; margin-right: 10px;">
                                        <div>
                                            <p class="fw-bold m-0" style="font-size:14px;">Purohit Ji</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                    @foreach (collect($reviewCounts['list'])->sortByDesc('rating') as $poojaReview)
                    @php
                        $youtubeEmbed = '';
                        $videoId = null;
                        if (!empty($poojaReview['youtube_link'])) {
                            preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([^&\/\?]+)/', $poojaReview['youtube_link'], $matches);
                            $videoId = $matches[1] ?? null;
                            if ($videoId) {
                                $youtubeEmbed = 'https://www.youtube.com/embed/' . $videoId;
                            }
                        }
                    @endphp

                    @if ($videoId)
                        {{-- ✅ Show YouTube Video only --}}
                        <div class="card product-single-hover shadow-none rtl">
                            <div class="card-body position-relative">
                                <div class="ratio ratio-16x9">
                                    <iframe width="100%" height="100%" src="{{ $youtubeEmbed }}" frameborder="0"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                        allowfullscreen style="border-radius: 10px;"></iframe>
                                </div>
                                <div class="d-flex align-items-center mt-2">
                                    <img src="{{ asset(empty($poojaReview['userData']['image']) ? 'public/images/default.png' : '/storage/app/public/profile/' . $poojaReview['userData']['image']) }}"
                                        alt="User Icon" class="user-icon"
                                        style="width: 50px; height: 50px; border-radius: 50%; margin-right: 10px;">
                                    <div>
                                        <p class="fw-bold m-0" style="font-size:14px;">
                                            {{ $poojaReview['userData']['name'] ?? 'User Name' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif (!empty(trim($poojaReview['comment'] ?? '')))
                        {{-- ✅ Show comment only if there's no YouTube link --}}
                        <div class="card product-single-hover shadow-none rtl">
                            <div class="card-body position-relative">
                                <div class="single-review-details">
                                    <div class="review-content" id="content-{{ $poojaReview['id'] ?? '0' }}">
                                        {{ $poojaReview['comment'] }}
                                    </div>
                                </div>
                                <div class="d-flex align-items-center mt-2">
                                    <img src="{{ asset(empty($poojaReview['userData']['image']) ? 'public/images/default.png' : '/storage/app/public/profile/' . $poojaReview['userData']['image']) }}"
                                        alt="User Icon" class="user-icon"
                                        style="width: 50px; height: 50px; border-radius: 50%; margin-right: 10px;">
                                    <div>
                                        <p class="fw-bold m-0" style="font-size:14px;">
                                            {{ $poojaReview['userData']['name'] ?? 'User Name' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach

                </div>
            @else
                <div class="text-center text-capitalize">
                    <p class="text-capitalize"><small>{{ translate('No_comment_given_yet') }}!</small></p>
                </div>
            @endif

        </div>
    </div>
</div>