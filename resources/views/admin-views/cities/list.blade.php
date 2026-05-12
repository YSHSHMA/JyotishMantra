@extends('layouts.back-end.app')

@section('title', translate('Cities List'))
@push('css_or_js')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="{{dynamicAsset(path: 'public/assets/select2/css/select2.min.css') }}" rel="stylesheet">
@endpush
@section('content')
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 d-flex gap-2">
            <img width="15" src="{{ dynamicAsset(path: 'public/assets/back-end/img/rashi.png') }}" alt="">
            {{ translate('Cities List') }}
        </h2>
    </div>
    <div class="row">

        <!-- Section for displaying video subcategory list -->
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
                        <div class="col-lg-8 mt-3 mt-lg-0 d-flex flex-wrap gap-3 justify-content-lg-end">
                            <a class="btn btn--primary" href="{{ route('admin.cities.view')}}"><i class="tio-add"></i>{{ translate('Add new Item')}}</a>
                        </div>
                    </div>
                </div>
                <!-- Table displaying video subcategories -->
                <div class="text-start">
                    <div class="table-responsive">
                        <table id="datatable" class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                            <thead class="thead-light thead-50 text-capitalize">
                                <tr>
                                    <th>{{ translate('SL') }}</th>
                                    <th>{{ translate('cities_name') }} </th>
                                    <th>{{ translate('short_description') }} </th>
                                    <th>{{ translate('Famous_For') }}</th>
                                    <th>{{ translate('Create_date') }}</th>
                                    <th>{{ translate('action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($list as $key => $videosubcategory)
                                <tr>
                                    <td>{{$list->firstItem()+$key}}</td>
                                    <td>{{ Str::limit(($videosubcategory['city'] ?? ''),20) }}</td>
                                    <td>{{ Str::limit(($videosubcategory['short_desc']),20) }}</td>
                                    <td>{{ Str::limit(($videosubcategory['famous_for']),20) }}       </td>
                                    <td>{{ date('d M Y',strtotime($videosubcategory['created_at'])) }} </td>    
                                    <!-- <td>
                                        <form action="{{route('admin.videosubcategory.status-update') }}" method="post" id="videosubcategory-status{{$videosubcategory['id']}}-form">
                                            @csrf
                                            <input type="hidden" name="id" value="{{$videosubcategory['id']}}">
                                            <label class="switcher mx-auto">
                                                <input type="checkbox" class="switcher_input toggle-switch-message" name="status" id="videosubcategory-status{{ $videosubcategory['id'] }}" value="1" {{ $videosubcategory['status'] == 1 ? 'checked' : '' }} data-modal-id="toggle-status-modal" data-toggle-id="videosubcategory-status{{ $videosubcategory['id'] }}" data-on-image="videosubcategory-status-on.png" data-off-image="videosubcategory-status-off.png" data-on-title="{{ translate('Want_to_Turn_ON').' '.$videosubcategory['defaultname'].' '. translate('status') }}" data-off-title="{{ translate('Want_to_Turn_OFF').' '.$videosubcategory['defaultname'].' '.translate('status') }}" data-on-message="<p>{{ translate('if_enabled_this_video_will_be_available_on_the_website_and_customer_app') }}</p>" data-off-message="<p>{{ translate('if_disabled_this_video_will_be_hidden_from_the_website_and_customer_app') }}</p>">
                                                <span class="switcher_control"></span>
                                            </label>
                                        </form>
                                    </td> -->
                                    <td>

                                        <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('admin.cities.gallery',[$videosubcategory['id']] ) }}" class="btn btn-outline-info btn-sm square-btn" title="{{ translate('cities_gallery')}}">
                                                <i class="tio-image nav-icon"></i>
                                            </a>
                                            <a href="{{ route('admin.citie_visit.list',[$videosubcategory['id']] ) }}" class="btn btn-outline-info btn-sm square-btn" title="{{ translate('cities_visits')}}">
                                                <i class="tio-star nav-icon"></i>
                                            </a>
                                            
                                             <a class="btn btn-outline-info btn-sm square-btn" title="{{ translate('edit') }}" href="{{route('admin.cities.update',[$videosubcategory['id']])}}">
                                                <i class="tio-edit"></i>
                                            </a>
                                           <!-- <a class="videosubcategory-delete-button btn btn-outline-danger btn-sm square-btn" id="{{ $videosubcategory['id'] }}">
                                                <i class="tio-delete"></i>
                                            </a> -->
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Pagination for video subcategory list -->
                <div class="table-responsive mt-4">
                    <div class="d-flex justify-content-lg-end">
                        {{ $list->links() }}
                    </div>
                </div>
                <!-- Message for no data to show -->
                @if(count($list) == 0)
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