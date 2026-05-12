@if($GurujiList->isNotEmpty())
    @foreach($GurujiList as $guruji)
        <div class="col-lg-3 col-md-6 col-sm-12 px-2 pb-4">
            <a href="{{ route('guruji.individual', ['name' => Str::slug($guruji['name'])]) }}"
                class="others-store-card text-capitalize d-block">

                <div class="overflow-hidden other-store-banner" style="height:150px;">
                    <img class="w-100 h-100"
                        src="{{ getValidImage(path: 'storage/app/public/astrologers/banner/' . $guruji->banner, type: 'shop-banner') }}"
                        alt="{{ translate('Astrologer Banner') }}">
                </div>
                <div class="name-area mt-2">
                    <div class="overflow-hidden other-store-logo rounded-full" style="width:60px; height:60px;">
                        <img class="rounded-full w-100 h-100"  src="{{ $guruji->image }}"
                            alt="{{ translate('Astrologer Profile') }}">
                    </div>

                    <div class="info pt-2">
                        <h5 class="m-0">{{ $guruji->name }}</h5>
                        <div class="d-flex align-items-center">
                            <h6 class="web-text-primary mb-0">5.5</h6>
                            <i class="tio-star text-star mx-1"></i>
                            <small>{{ translate('rating') }}</small>
                        </div>
                    </div>
                </div>

                <div class="info-area mt-2 d-flex justify-content-between">
                    <div class="info-item">
                        <h6 class="web-text-primary">0</h6>
                        <span>{{ translate('reviews') }}</span>
                    </div>
                    <div class="info-item">
                        <h6 class="web-text-primary">{{ $guruji->service_count }}</h6>
                        <span>{{ translate('Services') }}</span>
                    </div>
                </div>

            </a>
        </div>
    @endforeach
@endif
