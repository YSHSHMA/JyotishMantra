<div class="col-xl-3 col-lg-4 col-md-4 col-sm-6 col-6 service-card service-item gride-service" data-type="puja">
    <div class="card h-100 shadow-sm">
        <!-- Category Badge -->
        <span class="for-discount-value pooja-badge p-1 pl-2 pr-2 font-bold fs-13">
            <span class="direction-ltr blink d-block category-name">
                {{ $service->category->name ?? 'No Category' }}
            </span>
        </span>

        <!-- Image -->
        <a href="{{ route('guruji.book-puja', [$realName, $service->slug]) }}">
        @php
            $astrologerImg = !empty($service->thumbnail) ? 'storage/app/public/astrologers/service-thumbnail/'.$service->thumbnail : null;
            $poojaImg = !empty($service->thumbnail)  ? 'storage/app/public/pooja/thumbnail/'.$service->thumbnail  : null;
            $defaultImg = 'storage/app/public/company/' . getWebConfig(name: 'loader_gif');
        @endphp

        <img class="card-img-top puja-image"
            style="height:180px; object-fit:cover;"
            src="{{ $astrologerImg && file_exists(base_path($astrologerImg)) ? getValidImage(path: $astrologerImg) : ($poojaImg && file_exists(base_path($poojaImg)) ? getValidImage(path: $poojaImg): getValidImage(path: $defaultImg))
            }}">
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
            <div class="d-flex justify-content-between align-items-center mt-auto ratings" style="font-size:12px;">
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
                    <img src="{{ theme_asset(path: 'public/assets/front-end/img/track-order/arrow-white-icon.svg') }}">
                </span>
            </a>
        </div>
    </div>
</div>
 