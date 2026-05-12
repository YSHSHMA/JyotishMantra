@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')

@section('title', translate('Cities_review'))
@push('css_or_js')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="{{dynamicAsset(path: 'public/assets/select2/css/select2.min.css') }}" rel="stylesheet">
@endpush
@section('content')
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 d-flex gap-2">
            <img width="15" src="{{ dynamicAsset(path: 'public/assets/back-end/img/rashi.png') }}" alt="">
            {{ translate('Cities_review') }}
        </h2>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="px-3 py-4">
                    <!-- Search bar -->
                    <div class="row align-items-center">
                        <div class="col-sm-4 col-md-6 col-lg-4 mb-2 mb-sm-0">
                            <form action="{{ url()->current() }}" method="GET">
                                <div class="input-group input-group-custom input-group-merge">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="tio-search"></i>
                                        </div>
                                    </div>
                                    <input id="datatableSearch_" type="search" name="searchValue" class="form-control" placeholder="{{ translate('search_by_Name') }}" aria-label="Search orders" value="{{ request('searchValue') }}" required>
                                    <button type="submit" class="btn btn--primary">{{ translate('search') }}</button>
                                </div>
                            </form>
                        </div>
                        
                    </div>
                </div>
                <div class="text-start">
                    <div class="table-responsive">
                        <table id="datatable" class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                            <thead class="thead-light thead-50 text-capitalize">
                                <tr>
                                    <th>{{ translate('SL') }}</th>
                                    <th class="text-center">{{ translate('cities_name') }} </th>
                                    <th class="text-center">{{ translate('user_name') }} </th>
                                    <th class="text-center">{{ translate('image') }} </th>
                                    <th class="text-center">{{ translate('comment') }}</th>
                                    <th class="text-center">{{ translate('Create_date') }}</th>
                                    @if (Helpers::modules_permission_check('Temple', 'City', 'review-status') || Helpers::modules_permission_check('Temple', 'City', 'review-delete'))
                                    <th class="text-center">{{ translate('action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Loop through video subcategories -->
                                @foreach($getData as $key => $videosubcategory)
                                <tr>
                                    <td>{{$getData->firstItem()+$key}}</td>
                                    <td class="text-center">{{ ($videosubcategory['cities']['city'] ?? '') }}</td>
                                    <td class="text-center">{{ ($videosubcategory['userData']['name'] ?? '') }}</td>
                                    <td class="text-center">  
                                            @if($videosubcategory['image'])
                                        <img  alt="" class="h-auto aspect-1 bg-white onerror-add-class-d-none" src="{{ getValidImage(path: 'storage/app/public/cities/review/' . $videosubcategory['image'], type: 'backend-product') }}" style="width: 100px;" />
                                    @endif
                                    </td>
                                    <td class="text-center">{{ ($videosubcategory['comment']) }} </td>
                                    <td class="text-center">{{ date('d M Y',strtotime($videosubcategory['created_at'])) }}       </td>    
                                    @if (Helpers::modules_permission_check('Temple', 'City', 'review-status') || Helpers::modules_permission_check('Temple', 'City', 'review-delete'))
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">  
                                            @if (Helpers::modules_permission_check('Temple', 'City', 'review-status'))
                                        <form action="{{route('admin.cities.review-status-update') }}" method="post" id="videosubcategory-status{{$videosubcategory['id']}}-form">
                                            @csrf
                                            <input type="hidden" name="id" value="{{$videosubcategory['id']}}">
                                            <label class="switcher mx-auto">
                                                <input type="checkbox" class="switcher_input toggle-switch-message" name="status" id="videosubcategory-status{{ $videosubcategory['id'] }}" value="1" {{ $videosubcategory['status'] == 1 ? 'checked' : '' }} data-modal-id="toggle-status-modal" data-toggle-id="videosubcategory-status{{ $videosubcategory['id'] }}" data-on-image="videosubcategory-status-on.png" data-off-image="videosubcategory-status-off.png" data-on-title="{{ translate('Want_to_Turn_ON').' Review '. translate('status') }}" data-off-title="{{ translate('Want_to_Turn_OFF').' Review '.translate('status') }}" data-on-message="<p>{{ translate('if_enabled_this_Review_will_be_available_on_the_website_and_customer_app') }}</p>" data-off-message="<p>{{ translate('if_disabled_this_Review_will_be_hidden_from_the_website_and_customer_app') }}</p>">
                                                <span class="switcher_control"></span>
                                            </label>
                                        </form>
                                        @endif
                                        
                                        @if (Helpers::modules_permission_check('Temple', 'City', 'review-delete'))
                                            <a class="delete-data btn btn-outline-danger btn-sm square-btn" data-id="cities_visit-{{ $videosubcategory['id'] }}">
                                                <i class="tio-delete"></i>
                                            </a>
                                            <form action="{{ route('admin.cities.review-delete',$videosubcategory['id'] )}}" method="post" id="cities_visit-{{ $videosubcategory['id'] }}">
                                                @csrf 
                                                <input type="hidden" name="_method" value="delete">
                                                <input type="hidden" name="id" value="{{ $videosubcategory['id'] }}">
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Pagination for video subcategory list -->
                <div class="table-responsive mt-4">
                    <div class="d-flex justify-content-lg-end">
                        {{ $getData->links() }}
                    </div>
                </div>
                <!-- Message for no data to show -->
                @if(count($getData) == 0)
                <div class="text-center p-4">
                    <img class="mb-3 w-160" src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}" alt="{{ translate('image') }}">
                    <p class="mb-0">{{ translate('no_data_to_show') }}</p>
                </div>
                @endif
            </div>
        </div>

    </div>
</div>

@endsection