@extends('layouts.front-end.app')

@section('title', translate('All_vendor_Page'))

@push('css_or_js')
    <meta property="og:image" content="{{dynamicStorage(path: 'storage/app/public/company')}}/{{$web_config['web_logo']->value}}"/>
    <meta property="og:title" content="Brands of {{$web_config['name']->value}} "/>
    <meta property="og:url" content="{{env('APP_URL')}}">
    <meta property="og:description" content="{{ substr(strip_tags(str_replace('&nbsp;', ' ', $web_config['about']->value)),0,160) }}">
    <meta property="twitter:card" content="{{dynamicStorage(path: 'storage/app/public/company')}}/{{$web_config['web_logo']->value}}"/>
    <meta property="twitter:title" content="Brands of {{$web_config['name']->value}}"/>
    <meta property="twitter:url" content="{{env('APP_URL')}}">
    <meta property="twitter:description" content="{{ substr(strip_tags(str_replace('&nbsp;', ' ', $web_config['about']->value)),0,160) }}">
@endpush

@section('content')

    <div class="container mb-md-4 {{Session::get('direction') === "rtl" ? 'rtl' : ''}} __inline-65">
        <div class="bg-primary-light rounded-10 my-4 p-3 p-sm-4" data-bg-img="{{ theme_asset(path: 'public/assets/front-end/img/media/bg.png') }}">
            <div class="row g-2 align-items-center">
                <div class="col-lg-8 col-md-6">
                    <div class="d-flex flex-column gap-1 text-primary">
                        <h4 class="mb-0 text-start fw-bold text-primary text-uppercase">{{ translate('all_Vendors') }}</h4>
                        <p class="fs-14 fw-semibold mb-0">{{translate('Find_your_desired_vendor_and_book_your_favourite_tour')}}</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <form action="{{ route('tour.all-vendor') }}">
                        <div class="input-group">
                            <input type="text" class="form-control rounded-10" value="{{request('vendor_name')}}"  placeholder="{{translate('Search_Vendor')}}" name="vendor_name">
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
                @if(count($vendors) > 0)
                    <div class="row mx-n2 __min-h-200px">
                        @foreach ($vendors as $vendor)
                        @php
                            $tourCount = App\Models\TourVisits::where('created_id',$vendor['id'])->where('status', 1)->count();
                        @endphp
                            <div class="col-lg-3 col-md-6 col-sm-12 px-2 pb-4 text-center">
                                <a href="{{route('tour.vendor-tour',$vendor['tour_id'])}}" class="others-store-card text-capitalize">
                                    <div class="overflow-hidden other-store-banner">
                                        {{-- @if($vendor['id'] != 0) --}}
                                            <img class="w-100 h-100 object-cover" alt="" src="{{ getValidImage(path: 'storage/app/public/tour_and_travels/doc/'.$vendor['banner'], type: 'shop-banner') }}">
                                        {{-- @else
                                            <img class="w-100 h-100 object-cover" alt="" src="{{ getValidImage(path: 'storage/app/public/shop/'.$vendor['banner'], type: 'shop-banner') }}">
                                        @endif --}}
                                    </div>
                                    <div class="name-area">
                                        <div class="position-relative">
                                            <div class="overflow-hidden other-store-logo rounded-full">
                                                {{-- @if($vendor['id'] != 0) --}}
                                                    <img class="rounded-full" alt="{{ translate('store') }}"
                                                         src="{{ getValidImage(path: 'storage/app/public/tour_and_travels/doc/'.$vendor['tour_image'], type: 'shop') }}">
                                                {{-- @else
                                                <img class="rounded-full" alt="{{ translate('store') }}"
                                                     src="{{ getValidImage(path: 'storage/app/public/company/'.$vendor['image'], type: 'shop') }}">
                                                @endif --}}
                                            </div>

                                        </div>
                                        <div class="info pt-2">
                                            <h5 class="text-start pt-3">{{ $vendor['company_name'] }}</h5>
                                            <div class="d-flex align-items-center">
                                                <small class="fw-bold pt-0 mt-0">{{ $vendor['state'] .' '. $vendor['city'] }}</small>
                                            </div>
                                            <div class="d-flex align-items-center pt-1">
                                                <h6 class="web-text-primary">{{ number_format($vendor['avg_rating'], 1) }}</h6>
                                                <i class="tio-star text-star mx-1"></i>
                                                <small>{{ translate('rating') }}</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="info-area">
                                        <div class="info-item">
                                            <h6 class="web-text-primary">{{$vendor['review_count'] < 1000 ? $vendor['review_count'] : number_format($vendor['review_count']/1000 , 1).'K'}}</h6>
                                            <span>{{ translate('reviews') }}</span>
                                        </div>
                                        <div class="info-item">
                                            <h6 class="web-text-primary">{{$tourCount < 1000 ? $tourCount : number_format($tourCount/1000 , 1).'K'}}</h6>
                                            <span>{{ translate('tours') }}</span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>

                    {{-- <div class="row mx-n2">
                        <div class="col-md-12">
                            <div class="text-center">
                                {{ $vendors->links() }}
                            </div>
                        </div>
                    </div> --}}
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
