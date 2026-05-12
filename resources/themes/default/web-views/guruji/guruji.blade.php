@extends('layouts.front-end.app')

@php
    use Carbon\Carbon;
    use App\Utils\Helpers;
@endphp
@push('css_or_js')

<link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/home.css') }}" />
@endpush


@section('content')
<div class="container mb-md-4 {{Session::get('direction') === "rtl" ? 'rtl' : ''}} __inline-65">
    <div class="bg-primary-light rounded-10 my-4 p-3 p-sm-4" data-bg-img="{{ theme_asset(path: 'public/assets/front-end/img/media/bg.png') }}">
        <div class="row g-2 align-items-center">
            <div class="col-lg-8 col-md-6">
                <div class="d-flex flex-column gap-1 text-primary">
                    <h4 class="mb-0 text-start fw-bold text-primary text-uppercase">{{ translate('all_Gurujis') }}</h4>
                    <p class="fs-14 fw-semibold mb-0">{{ translate('Find_your_Guruji_and_book_your_pooja') }}
                    </p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <form action="{{ route('tour.all-vendor') }}">
                    <div class="input-group">
                        <input type="text" class="form-control rounded-10" value="{{request('vendor_name')}}"  placeholder="{{ translate('Search_Vendor') }}" name="vendor_name">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary rounded-10" type="submit">{{translate('search')}}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="row">
        <section class="col-lg-12">
            @if(count($GurujiList) > 0)
                <div class="row mx-n2 __min-h-200px">
                    @foreach ($GurujiList as $guruji)
                        <div class="col-12 col-md-3 mb-4">
                            <a href="{{ route('guruji.guruji_personal_pooja', ['name' => Str::slug($guruji['name'])]) }}"
                            class="others-store-card text-capitalize d-block">

                                <div class="overflow-hidden other-store-banner" style="height:150px;">
                                    <img class="w-100 h-100"
                                        src="{{ getValidImage(path: 'storage/app/public/astrologers/'.$guruji->image, type: 'shop-banner') }}"
                                        alt="">
                                </div>

                                <div class="name-area mt-2">
                                    <div class="overflow-hidden other-store-logo rounded-full" style="width:60px; height:60px;">
                                        <img class="rounded-full w-100 h-100"
                                            src="{{ getValidImage(path: 'storage/app/public/astrologers/'.$guruji->image, type: 'shop') }}"
                                            alt="{{ translate('store') }}">
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
                                        <h6 class="web-text-primary">0</h6>
                                        <span>{{ translate('products') }}</span>
                                    </div>
                                </div>

                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
            @else
                <div class="mb-5 text-center text-muted">
                    <div class="d-flex justify-content-center my-2">
                        <img alt="" src="{{ theme_asset(path: 'public/assets/front-end/img/media/seller.svg') }}">
                    </div>
                    <h4 class="text-muted">{{ translate('tour_vendor_not_available') }}</h4>
                    <p>{{ translate('Sorry_no_data_found_related_to_your_search') }}</p>
                </div>
            @endif
        </section>
    </div>
</div>

@endsection

@push('script')
   
@endpush
