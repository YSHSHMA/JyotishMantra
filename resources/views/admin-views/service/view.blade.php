@extends('layouts.back-end.app')

@section('title', translate('pooja_Preview'))
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
                    {{ translate('View_Pooja_Details') }}
                </h2>
            </div>
        </div>
        
        <div class="card card-top-bg-element">
            <div class="card-body">
                <div>
                    <div class="media flex-nowrap flex-column flex-sm-row gap-3 flex-grow-1">
                        <div class="d-flex flex-column align-items-center __min-w-165px">
                            <a class="aspect-1 float-left overflow-hidden"
                               href="{{ getValidImage(path: 'storage/app/public/pooja/thumbnail/'. $service['thumbnail'],type: 'backend-product') }}"
                               data-lightbox="product-gallery-{{ $service['id'] }}">
                                <img class="avatar avatar-170 rounded-0"
                                     src="{{ getValidImage(path: 'storage/app/public/pooja/thumbnail/'. $service['thumbnail'],type: 'backend-product') }}"
                                     alt="">
                            </a>
                            @if ($serviceActive)
                                <a href="{{ route('service', $service['slug']) }}"
                                   class="btn btn-outline--primary mr-1 mt-2" target="_blank">
                                    <i class="tio-globe"></i>
                                    {{ translate('view_live') }}
                                </a>
                            @endif
                        </div>
                        @if($service->images && file_exists(base_path('storage/app/public/pooja/thumbnail/'.$service->images)))
                            <a href="{{ dynamicAsset(path: 'storage/app/public/pooja/thumbnail/'.$service->images) }}"
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

                                    @if ($service->product_type == 'pooja' > 0)
                                      
                                   
                                        @foreach (json_decode($service->images) as $imageKey => $photo)
                                            @if($imageKey < 3 || count(json_decode($service->images, true)) < 5)
                                                <a class="aspect-1 float-left overflow-hidden"
                                                   href="{{ getValidImage(path: 'storage/app/public/pooja/'.$photo, type: 'backend-product') }}"
                                                   data-lightbox="product-gallery-{{ $service['id'] }}">
                                                    <img width="50"  class="img-fit max-50" alt=""
                                                         src="{{ getValidImage(path: 'storage/app/public/pooja/'.$photo, type: 'backend-product') }}">
                                                </a>
                                            @elseif($imageKey == 3)
                                                <a class="aspect-1 float-left overflow-hidden d-block border rounded-lg position-relative"
                                                   href="{{ getValidImage(path: 'storage/app/public/pooja/'.$photo, type: 'backend-product') }}"
                                                   data-lightbox="product-gallery-{{ $service['id'] }}">
                                                    <img width="50"  class="img-fit max-50" alt=""
                                                         src="{{ getValidImage(path: 'storage/app/public/pooja/'.$photo, type: 'backend-product') }}">
                                                    <div class="extra-images">
                                                        <span class="extra-image-count">
                                                            +{{ (count(json_decode($service->images, true)) - $imageKey) + 1 }}
                                                        </span>
                                                    </div>
                                                </a>
                                            @else
                                                <a class="aspect-1 float-left overflow-hidden d-none"
                                                   href="{{ getValidImage(path: 'storage/app/public/pooja/'.$photo, type: 'backend-product') }}"
                                                   data-lightbox="product-gallery-{{ $service['id'] }}">
                                                    <img width="50"  class="img-fit max-50" alt=""
                                                         src="{{ getValidImage(path: 'storage/app/public/pooja/'.$photo, type: 'backend-product') }}">
                                                </a>
                                            @endif
                                        @endforeach
                                    @endif

                                </div>
                                <div class="d-flex gap-3 flex-nowrap lh-1 badge badge--primary-light justify-content-sm-end height-30px align-items-center">
                                    <span class="text-dark">
                                        {{ count($service->orderDetails) }} {{ translate('orders') }}
                                       
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
                                        if (count($service['translations'])) {
                                            $translate = [];
                                            foreach ($service['translations'] as $translation) {
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
                                            <h2 class="mb-2 pb-1 text-gulf-blue">{{ $translate[$language]['name']??$service['name']}}</h2>
                                            <a class="btn btn-outline--primary btn-sm square-btn mx-2 w-auto h-25"
                                               title="{{ translate('edit') }}"
                                               href="{{ route('admin.service.update', [$service['id']]) }}">
                                                <i class="tio-edit"></i>
                                            </a>
                                        </div>
                                        <div class="">
                                            <label class="text-gulf-blue font-weight-bold">{{ translate('description').' : ' }}</label>
                                            <div class="rich-editor-html-content">
                                                {!! $translate[$language]['description'] ?? $service['details'] !!}
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
            @if(!empty($service['pooja_venue']) >0)
            <div class="col-md-6">
                <div class="card border-0">
                    <div class="card-body p-0">
                        <div class="table-responsive datatable-custom">
                            <table
                                class="table table-borderless table-nowrap table-align-middle card-table w-100 text-start">
                                <thead class="thead-color  bg-warning thead-50 text-capitalize">
                                <tr>
                                    <th class="text-center">{{ translate('pooja_venue') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                   
                                        <tr>
                                            <td class="text-center">
                                                <span class="py-1">{{$service['pooja_venue']}}</span>
                                            </td>
                                        </tr>
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            <div class="col-md-6">
                <div class="card border-0">
                    <div class="card-body p-0">
                        <div class="table-responsive datatable-custom">
                            <table class="table table-borderless table-nowrap table-align-middle card-table w-100 text-start">
                                <thead class="thead-color thead-50 text-capitalize">
                                <tr>
                                    <th class="text-center">{{ translate('Category') }}</th>
                                    <th class="text-center">{{ translate('pooja_time') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                        <tr>
                                            <td class="text-center">
                                                <span class="py-1">{{ @UcFirst($service['category']['name']) }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="py-1">{{ date('h:i A', strtotime($service['pooja_time'])) }}</span>
                                            </td>
                                        </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
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
                                        @foreach(json_decode($service['packages_id']) as $key => $pac)
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
                                        @foreach(json_decode($service['product_id']) as $key => $pro)                                  
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
            @if($service['pooja_type'] == 0)
            @if(!empty($service['week_days']) && count(json_decode($service['week_days'])) > 0)
            <div class="col-md-4">
                <div class="card border-0">
                    <div class="card-body p-0">
                        <div class="table-responsive datatable-custom">
                            <table
                                class="table table-borderless table-nowrap table-align-middle card-table w-100 text-start">
                                <thead class="thead-color thead-50 text-capitalize">
                                <tr>
                                    <th class="text-center">{{ translate('week_days') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach(json_decode($service['week_days']) as $key=>$value)
                                        <tr>
                                            <td class="text-center">
                                                <span class="py-1">{{ ucwords($value) }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @else
          
            <div class="col-md-4">
                <div class="card border-0">
                    <div class="card-body p-0">
                        <div class="table-responsive datatable-custom">
                            <table
                                class="table table-borderless table-nowrap table-align-middle card-table w-100 text-start">
                                <thead class="thead-color thead-50 text-capitalize">
                                <tr>
                                    <th class="text-center" colspan="2">{{ translate('Schedule_pooja_Praform') }}</th>
                                  
                                </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th class="text-center thead-color">Date</th>
                                        <th class="text-center thead-color">Pooja time</th>
                                    </tr>
                                    @foreach(json_decode($service['schedule']) as $value)
                                    <tr>
                                        <td>
                                            <span class="py-1">{{ date('d F Y',strtotime($value->schedule)) }}</span>
                                        </td>
                                        <td>
                                            <span class="py-1"> {{ $value->schedule_time }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                                
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-header bg--primary--light">
                        <h5 class="card-title text-capitalize">{{translate('product_SEO_&_meta_data')}}</h5>
                    </div>
                    <div class="card-body">
                        <div>
                            <h6 class="mb-3 text-capitalize">
                                {{$service['meta_title'] ?? translate('meta_title_not_found').' '.'!'}}
                            </h6>
                        </div>
                        <p class="text-capitalize">
                            {{$service['meta_description'] ?? translate('meta_description_not_found').' '.'!'}}
                        </p>
                        @if($service['meta_image'])
                            <div class="d-flex flex-wrap gap-2">
                                <a class="aspect-1 float-left overflow-hidden"
                                   href="{{ getValidImage(path: 'storage/app/public/product/meta/'.$service['meta_image'],type: 'backend-basic') }}"
                                   data-lightbox="meta-thumbnail">
                                    <img class="max-width-100px"
                                         src="{{ getValidImage(path: 'storage/app/public/product/meta/'.$service['meta_image'],type: 'backend-basic') }}" alt="{{translate('meta_image')}}">
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-header bg--primary--light">
                        <h5 class="card-title text-capitalize">{{translate('product_video')}}</h5>
                    </div>
                    <div class="card-body">
                        <div>
                            <h6 class="mb-3 text-capitalize">
                                {{$service['video_provider'].' '.translate('video_link')}}
                            </h6>
                        </div>
                        @if($service['video_url'] )
                            <a href="{{$service['video_url']}}" target="_blank" class="text-primary">
                                {{$service['video_url']}}
                            </a>
                        @else
                            <span>{{ translate('no_data_to_show').' '.'!'}}</span>
                        @endif
                    </div>
                </div>
            </div>
            @if ($service->denied_note && $service['request_status'] == 2)
                <div class="col-md-12">
                    <div class="card h-100">
                        <div class="card-header bg--primary--light">
                            <h5 class="card-title text-capitalize">{{translate('reject_reason')}}</h5>
                        </div>
                        <div class="card-body">
                            <div>
                                {{ $service->denied_note}}
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
       
    </div>

    <div class="modal fade" id="publishNoteModal" tabindex="-1" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ translate('rejected_note') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="form-group" action="{{ route('admin.service.deny', ['id'=>$service['id']]) }}"
                      method="post" id="product-status-denied">
                    @csrf
                    <div class="modal-body">
                        <textarea class="form-control text-area-max-min" name="denied_note" rows="3"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ translate('close') }}
                        </button>
                        <button type="button" class="btn btn--primary form-submit"
                                data-redirect-route="{{route('admin.service.list',['seller','status' => $service['request_status']])}}"
                                data-form-id="product-status-denied">{{ translate('submit') }}</button>
                    </div>
                </form>
            </div>
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
