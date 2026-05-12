@php
    use App\Utils\Helpers;
    use function App\Utils\displayStarRating;
@endphp
<div class="portfolio {{ $poojaD->service->category->slug }}" data-cat="{{ $poojaD->service->category->slug }}">
    <div class="portfolio-wrapper">
        <div class="card">

            <!-- Category Badge -->
            <span class="for-discount-value pooja-badge p-1 pl-2 pr-2 font-bold fs-13">
                <span class="direction-ltr blink d-block">{{ $poojaD->service->category->name }}</span>
            </span>

            <!-- Image -->
            <a href="{{ route('epooja', $poojaD->service->slug) }}">
                <img src="{{ !empty($poojaD->service->thumbnail)
                    ? getValidImage('storage/app/public/pooja/thumbnail/' . $poojaD->service->thumbnail)
                    : getValidImage(
                        'storage/app/public/company/' . getWebConfig('loader_gif'),
                        'source',
                        theme_asset('public/assets/front-end/img/kashi-vishwanath-temple.jpg'),
                    ) }}"
                    class="card-img-top puja-image" alt="...">
            </a>

            <div class="card-body newpadding">
                <p class="pooja-heading underborder two-lines-only">
                    {{ strtoupper($poojaD->service->pooja_heading) }}</p>
                <span class="puja-title d-none">{{ $poojaD->service->getRawOriginal('pooja_heading') }}</span>
                <div class="w-bar h-bar bg-gradient mt-2"></div>
                <p class="pooja-name two-lines-only">{{ $poojaD->service->name }}</p>
                <span class="puja-title d-none">{{ $poojaD->service->getRawOriginal('name') }}</span>
                <p class="card-text mt-2 pb-2 two-lines-only">{{ $poojaD->service->short_benifits }}</p>
                <span class="puja-title d-none">{{ $poojaD->service->getRawOriginal('short_benifits') }}</span>

                <!-- Venue -->
                <div class="d-flex">
                    <img src="{{ theme_asset('public/assets/front-end/img/track-order/temple.png') }}" alt=""
                        style="width:24px;height:24px;">
                    <p class="pooja-venue one-lines-only">{{ $poojaD->service->pooja_venue }}</p>
                    <span class="puja-title d-none">{{ $poojaD->service->getRawOriginal('pooja_venue') }}</span>
                </div>

                <!-- Date (next_date from model) -->
                <div class="d-flex">
                    <img src="{{ theme_asset('public/assets/front-end/img/track-order/date.png') }}" alt=""
                        style="width:24px;height:24px;">
                    <p class="pooja-calendar">
                        @if ($poojaD->booking_date)
                            {{ date('d,M,l',strtotime($poojaD->booking_date)) }}
                        @endif
                    </p>
                </div>

                <!-- Devotees + Rating -->
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <img src="{{ theme_asset('public/assets/front-end/img/track-order/users.gif') }}"
                            alt="Users" class="colored-icon" style="width: 24px; height: 24px; margin-right: 5px;">
                        <span class="pooja-calendar">{{ 10000 + $poojaD->pooja_order_review_count }}+
                            Devotees</span>
                    </div>

                    <div class="d-flex align-items-center">
                        {!! displayStarRating($poojaD->review_avg_rating ?? 0) !!}
                        <span class="ml-2">({{ number_format($poojaD->review_avg_rating ?? 5, 1) }}/5)</span>
                    </div>
                </div>

                <!-- CTA Button -->
                <a href="{{ route('epooja', $poojaD->service->slug) }}" class="animated-button mt-2">
                    <span class="text-wrapper">
                        <span class="text-slide">{{ translate('GO_PARTICIPATE') }}</span>
                        <span class="text-slide">{{ translate('Limited_slots!') }}</span>
                    </span>
                    <span class="icon">
                        <img src="{{ theme_asset('public/assets/front-end/img/track-order/arrow-white-icon.svg') }}"
                            alt="arrow" />
                    </span>
                </a>
            </div>
        </div>
    </div>
</div>