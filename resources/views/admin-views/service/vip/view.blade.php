@extends('layouts.back-end.app')

@section('title', $vippooja['is_anushthan'] == 0 ? 'VIP POOJA | DETAILS' : 'ANUSHTHAN | DETAILS')
@push('css_or_js')
<style>
    .table .thead-color th {
    color: #fff;
    background-color: #073b74;
    border-color: rgb(241 16 16);
}
</style>
@endpush
@section('content')
    <div class="content container-fluid text-start">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-10 mb-3">
            <div class="">
                <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                    <img src="{{ asset('public/assets/back-end/img/inhouse-product-list.png') }}" alt="">
                    @if ($vippooja['is_anushthan'] == 0)
                    <span class="">{{ translate('VIP POOJA || EDIT') }}</span>
                    @elseif ($vippooja['is_anushthan'] == 1)
                        <span class="">{{ translate('ANUSHTHAN || EDIT') }}</span>
                    @endif
                </h2>
            </div>
        </div>
        
        <div class="card card-top-bg-element">
            <div class="card-body">
                <div>
                    <div class="media flex-nowrap flex-column flex-sm-row gap-3 flex-grow-1">
                        <div class="d-flex flex-column align-items-center __min-w-165px">
                            <a class="aspect-1 float-left overflow-hidden"
                               href="{{ getValidImage(path: 'storage/app/public/pooja/vip/thumbnail/'. $vippooja['thumbnail'],type: 'backend-product') }}"
                               data-lightbox="product-gallery-{{ $vippooja['id'] }}">
                                <img class="avatar avatar-170 rounded-0"
                                     src="{{ getValidImage(path: 'storage/app/public/pooja/vip/thumbnail/'. $vippooja['thumbnail'],type: 'backend-product') }}"
                                     alt="">
                            </a>
                            @if ($vipActive)
                                <a href="{{ route('service', $vippooja['slug']) }}"
                                   class="btn btn-outline--primary mr-1 mt-2" target="_blank">
                                    <i class="tio-globe"></i>
                                    {{ translate('view_live') }}
                                </a>
                            @endif
                        </div>
                        @if($vippooja->images && file_exists(base_path('storage/app/public/pooja/vip/thumbnail/'.$vippooja->images)))
                            <a href="{{ dynamicAsset(path: 'storage/app/public/pooja/vip/thumbnail/'.$vippooja->images) }}"
                               class="btn btn-outline--primary mr-1" title="{{translate('Download')}}">
                                <i class="tio-download"></i>
                                {{ translate('download') }}
                            </a>
                        @endif
                        <div class="d-block flex-grow-1 w-max-md-100">
                            @php($languages = getWebConfig(name:'pnc_language'))
                            @php($defaultLanguage = 'en')
                            @php($defaultLanguage = $languages[0])
                            <div class="d-flex flex-wrap justify-content-between align-items-center">
                                <ul class="nav nav-tabs w-fit-content mb-2">
                                    @foreach($languages as $language)
                                        <li class="nav-item text-capitalize">
                                            <a class="nav-link lang-link {{$language == $defaultLanguage? 'active':''}}"
                                            href="javascript:"
                                            id="{{$language}}-link">{{ getLanguageName($language).'('.strtoupper($language).')' }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                               
                            </div>
                            <div class="d-flex flex-wrap align-items-center flex-sm-nowrap justify-content-between gap-3 min-h-50">
                                <div class="d-flex flex-wrap gap-2 align-items-center">

                                    @if ($vippooja->product_type == 'pooja' > 0)
                                      
                                   
                                        @foreach (json_decode($service->images) as $imageKey => $photo)
                                            @if($imageKey < 3 || count(json_decode($vippooja->images, true)) < 5)
                                                <a class="aspect-1 float-left overflow-hidden"
                                                   href="{{ getValidImage(path: 'storage/app/public/pooja/vip/'.$photo, type: 'backend-product') }}"
                                                   data-lightbox="product-gallery-{{ $vippooja['id'] }}">
                                                    <img width="50"  class="img-fit max-50" alt=""
                                                         src="{{ getValidImage(path: 'storage/app/public/pooja/vip/'.$photo, type: 'backend-product') }}">
                                                </a>
                                            @elseif($imageKey == 3)
                                                <a class="aspect-1 float-left overflow-hidden d-block border rounded-lg position-relative"
                                                   href="{{ getValidImage(path: 'storage/app/public/pooja/vip/'.$photo, type: 'backend-product') }}"
                                                   data-lightbox="product-gallery-{{ $vippooja['id'] }}">
                                                    <img width="50"  class="img-fit max-50" alt=""
                                                         src="{{ getValidImage(path: 'storage/app/public/poojavip//'.$photo, type: 'backend-product') }}">
                                                    <div class="extra-images">
                                                        <span class="extra-image-count">
                                                            +{{ (count(json_decode($vippooja->images, true)) - $imageKey) + 1 }}
                                                        </span>
                                                    </div>
                                                </a>
                                            @else
                                                <a class="aspect-1 float-left overflow-hidden d-none"
                                                   href="{{ getValidImage(path: 'storage/app/public/pooja/vip/'.$photo, type: 'backend-product') }}"
                                                   data-lightbox="product-gallery-{{ $vippooja['id'] }}">
                                                    <img width="50"  class="img-fit max-50" alt=""
                                                         src="{{ getValidImage(path: 'storage/app/public/poojavip//'.$photo, type: 'backend-product') }}">
                                                </a>
                                            @endif
                                        @endforeach
                                    @endif

                                </div>
                                <div class="d-flex gap-3 flex-nowrap lh-1 badge badge--primary-light justify-content-sm-end height-30px align-items-center">
                                    <span class="text-dark">
                                        {{ count($vippooja->orderDetails) }} {{ translate('orders') }}
                                       
                                    </span>
                                    <span class="border-left py-2"></span>
                                    <div class="review-hover position-relative cursor-pointer d-flex gap-2 align-items-center">
                                            <i class="tio-star"></i>
                                        {{-- <span>
                                            {{ count($service->rating)>0 ? number_format($service->rating[0]->average, 2, '.', ' '):0 }}
                                        </span> --}}
                                        <div class="review-details-popup">
                                            <h6 class="mb-2">{{ translate('rating') }}</h6>
                                            
                                        </div>
                                    </div>
                                    <span class="border-left py-2"></span>
                                    {{-- <span class="text-dark">
                                        {{ $service->reviews->whereNotNull('comment')->count() }} {{ translate('reviews') }}
                                    </span> --}}
                                </div>
                            </div>
                            <div class="d-block mt-2">
                                @foreach($languages as $language)
                                        <?php
                                        if (count($vippooja['translations'])) {
                                            $translate = [];
                                            foreach ($vippooja['translations'] as $translation) {
                                                if ($translation->locale == $language && $translation->key == "name") {
                                                    $translate[$language]['name'] = $translation->value;
                                                }
                                                if ($translation->locale == $language && $translation->key == "description") {
                                                    $translate[$language]['description'] = $translation->value;
                                                }
                                            }
                                        }
                                        ?>
                                    <div class="{{ $language != 'en'? 'd-none':''}} lang-form" id="{{ $language}}-form">
                                        <div class="d-flex">
                                            <h2 class="mb-2 pb-1 text-gulf-blue">{{ $translate[$language]['name']??$vippooja['name']}}</h2>
                                            <a class="btn btn-outline--primary btn-sm square-btn mx-2 w-auto h-25"
                                               title="{{ translate('edit') }}"
                                               href="{{ route('admin.service.vip.update', [$vippooja['id']]) }}">
                                                <i class="tio-edit"></i>
                                            </a>
                                        </div>
                                        <div class="">
                                            <label class="text-gulf-blue font-weight-bold">{{ translate('description').' : ' }}</label>
                                            <div class="rich-editor-html-content">
                                                {!! $translate[$language]['description'] ?? $vippooja['details'] !!}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <hr>
            </div>
        </div>
        <div class="row g-2 mt-3">
            
           
        </div>
        <div class="row g-2 mt-3">
                <div class="col-md-6">
                    <div class="card border-0">
                        <div class="card-body p-0">
                            <div class="table-responsive datatable-custom">
                                <table class="table table-borderless table-nowrap table-align-middle card-table w-100 text-start">
                                    <thead class="thead-color thead-50 text-capitalize">
                                    <tr>
                                        <th class="text-center">{{ translate('S.no') }}</th>
                                        <th class="text-center">{{ translate('Packages') }}</th>
                                        <th class="text-center">{{ translate('Price') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @foreach(json_decode($vippooja['packages_id']) as $key => $pac)
                                            @include('admin-views.service.partials._packages-list', ['package' => $pac,])
                                        @endforeach
                                        
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div> 
                <div class="col-md-6">
                    <div class="card border-0">
                        <div class="card-body p-0">
                            <div class="table-responsive datatable-custom">
                                <table class="table table-borderless table-nowrap table-align-middle card-table w-100 text-start">
                                    <thead class="thead-color thead-50 text-capitalize">
                                    <tr>
                                        <th class="text-center">{{ translate('S.no') }}</th>
                                        <th class="text-center">{{ translate('Product') }}</th>
                                        <th class="text-center">{{ translate('Price') }}</th>
                                        
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @foreach(json_decode($vippooja['product_id']) as $key => $pro)                                  
                                            @include('admin-views.service.partials._product-list', ['product' => $pro,])                                                 
                                        @endforeach

                                       
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <div class="row g-2 mt-3">
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header bg--primary--light">
                        <h5 class="card-title text-capitalize">{{translate('SEO_&_meta_data')}}</h5>
                    </div>
                    <div class="card-body">
                        <div>
                            <h6 class="mb-3 text-capitalize">
                                {{$vippooja['meta_title'] ?? translate('meta_title_not_found').' '.'!'}}
                            </h6>
                        </div>
                        <p class="text-capitalize">
                            {{$vippooja['meta_description'] ?? translate('meta_description_not_found').' '.'!'}}
                        </p>
                        @if($vippooja['meta_image'])
                            <div class="d-flex flex-wrap gap-2">
                                <a class="aspect-1 float-left overflow-hidden"
                                   href="{{ getValidImage(path: 'storage/app/public/product/meta/'.$vippooja['meta_image'],type: 'backend-basic') }}"
                                   data-lightbox="meta-thumbnail">
                                    <img class="max-width-100px"
                                         src="{{ getValidImage(path: 'storage/app/public/product/meta/'.$vippooja['meta_image'],type: 'backend-basic') }}" alt="{{translate('meta_image')}}">
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header bg--primary--light">
                        <h5 class="card-title text-capitalize">{{translate('video')}}</h5>
                    </div>
                    <div class="card-body">
                        <div>
                            <h6 class="mb-3 text-capitalize">
                                {{$vippooja['video_provider'].' '.translate('video_link')}}
                            </h6>
                        </div>
                        @if($vippooja['video_url'] )
                            <a href="{{$vippooja['video_url']}}" target="_blank" class="text-primary">
                                {{$vippooja['video_url']}}
                            </a>
                        @else
                            <span>{{ translate('no_data_to_show').' '.'!'}}</span>
                        @endif
                    </div>
                </div>
            </div>
            @if ($vippooja->denied_note && $vippooja['request_status'] == 2)
                <div class="col-md-12">
                    <div class="card h-100">
                        <div class="card-header bg--primary--light">
                            <h5 class="card-title text-capitalize">{{translate('reject_reason')}}</h5>
                        </div>
                        <div class="card-body">
                            <div>
                                {{ $vippooja->denied_note}}
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
       
    </div>

@endsection
@push('script')
<script>
    'use strict';
    $(".lang-link").click(function (e) {
        e.preventDefault();
        $('.lang-link').removeClass('active');
        $(".lang-form").addClass('d-none');
        $(this).addClass('active');
        let formId = this.id;
        let lang = formId.split("-")[0];
        $("#" + lang + "-form").removeClass('d-none');
    });

</script>
@endpush
