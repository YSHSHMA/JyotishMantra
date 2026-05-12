<div class="col-xl-3 col-lg-4 col-md-4 col-sm-6 col-6 conselling-item gride-service" data-type="puja">
    <div class="card h-100 shadow-sm">
        <!-- Image -->
        <a href="{{ route('guruji.book-conselling', [$realName, $conselling['slug']]) }}">
            <img class="card-img-top puja-image"
                style="height:180px; object-fit:cover;"
                src="{{ !empty($conselling->thumbnail)
                    ? getValidImage(path: 'storage/app/public/pooja/thumbnail/' . $conselling['thumbnail'])
                    : getValidImage(path: 'storage/app/public/company/' . getWebConfig(name: 'loader_gif')) }}">
        </a>

        <div class="card-body d-flex flex-column">
            <p class="pooja-name two-lines-only name-puja counselling-name">{{ $conselling['name'] }}</p>
            <!-- Price -->
            <div class="d-flex align-items-center mb-1">
                <!-- Orange SVG Rupee Icon -->
                <i class="fa fa-rupee-sign" style="font-size:20px; color:#FF6F00;"></i>

                <p class="pooja-venue venue ms-2 price" style="margin:0;">
               
                @if(!empty($service->counsellingPackage) && !is_null($service->counsellingPackage->price))
                    <span style="color:#FF6F00; font-weight:700; font-size:20px; margin-left:4px;">
                        {{ number_format($service->counsellingPackage->price, 2) }} /-
                    </span>
                @else
                    <span style="color:#FF6F00; font-weight:700; font-size:20px; margin-left:4px;">
                        {{ number_format($service->counselling_selling_price ?? 0, 2) }} /-
                    </span>
                @endif

                    
                </p>
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

            <a href="{{ route('guruji.book-conselling', [$realName, $service['slug']]) }}"
                class="animated-button mt-3 w-100 text-center">
                <span class="text-wrapper">
                    <span class="text-slide">{{ translate('Book_now') }}</span>
                    <span class="text-slide">{{ translate('Book_now!') }}</span>
                </span>
                <span class="icon">
                    <img src="{{ theme_asset(path: 'public/assets/front-end/img/track-order/arrow-white-icon.svg') }}">
                </span>
            </a>
        </div>
    </div>
</div>
