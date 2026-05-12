@extends('layouts.back-end.app')

@section('title', translate('Best Time To Visit'))
@push('css_or_js')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="{{dynamicAsset(path: 'public/assets/select2/css/select2.min.css') }}" rel="stylesheet">
@endpush
@section('content')


<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 d-flex gap-2">
            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/videosubcategory.png') }}" alt="">
            {{ translate('Best_Time_To_Visite') }}
        </h2>
    </div>
    <div class="row">
        <!-- Form for adding new video subcategory -->
        <div class="col-md-12 mb-3">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.citie_visit.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <!-- Language tabs -->
                        <ul class="nav nav-tabs w-fit-content mb-4">
                            @foreach($language as $lang)
                            <li class="nav-item text-capitalize">
                                <a class="nav-link form-system-language-tab cursor-pointer {{$lang == $defaultLanguage? 'active':''}}" id="{{$lang}}-link">
                                    {{ getLanguageName($lang).'('.strtoupper($lang).')' }}
                                </a>
                            </li>
                            @endforeach
                        </ul>
                        <div class="col-12">

                            @foreach($language as $lang)
                            <div class="row {{$lang != $defaultLanguage ? 'd-none':''}} form-system-language-form" id="{{$lang}}-form">
                                <div class='col-md-4 form-group'>
                                    <label class="title-color" for="name">{{ translate('month_name') }}<span class="text-danger">*</span>
                                        ({{ strtoupper($lang) }})</label>
                                        <input type="hidden" name='citie_id' value="{{ $id }}">
                                    <input type="text" name="month_name[]" class="form-control" autocomplete="off"  placeholder="{{ translate('month_name') }}" required="{{ $lang == $defaultLanguage? 'required':''}}">
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="title-color" for="name">{{ translate('weather') }}<span class="text-danger">*</span>({{ strtoupper($lang) }})</label>
                                    <input type="text" name="weather[]" class="form-control" autocomplete="off"  placeholder="{{ translate('Weather') }}" required="{{ $lang == $defaultLanguage? 'required':''}}">

                                </div>
                                <div class="col-md-4 form-group">

                                    <label class="title-color" for="name">{{ translate('sight') }}<span class="text-danger">*</span>
                                        ({{ strtoupper($lang) }})</label>
                                    <input type="text" name="sight[]" class="form-control" placeholder="{{ translate('sight') }}" autocomplete="off"  required="{{$lang == $defaultLanguage? 'required':''}}">
                                    <input type="hidden" name="lang[]" value="{{$lang}}" id="lang">
                                </div>
                                <div class="col-md-6 form-group">

                                    <label class="title-color" for="name">{{ translate('season') }}<span class="text-danger">*</span>
                                        ({{ strtoupper($lang) }})</label>
                                    <input type="text" name="season[]" class="form-control" placeholder="{{ translate('season') }}" autocomplete="off"  required="{{$lang == $defaultLanguage? 'required':''}}">
                                </div>
                                <div class="col-md-6 form-group">

                                    <label class="title-color" for="name">{{ translate('Crowd') }}<span class="text-danger">*</span>
                                        ({{ strtoupper($lang) }})</label>
                                    <input type="text" name="crowd[]" class="form-control" placeholder="{{ translate('crowd') }}" autocomplete="off"  required="{{$lang == $defaultLanguage? 'required':''}}">
                                </div>
              

                            </div>
                            @endforeach
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <div class="form-group">
                                                <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                                                    <div>
                                                        <label for="name" class="title-color text-capitalize font-weight-bold mb-0">{{ translate('image') }}</label>
                                                        <span class="badge badge-soft-info">{{ THEME_RATIO[theme_root_path()]['Product Image'] }}</span>
                                                        <span class="input-label-secondary cursor-pointer" data-toggle="tooltip" title="{{ translate('add_Weather_image') }} JPG, PNG or JPEG {{ translate('format_within') }} 2MB">
                                                            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">
                                                        </span>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="custom_upload_input">
                                                        <input type="file" name="image" class="custom-upload-input-file action-upload-color-image" id="" data-imgpreview="pre_gst_img_viewer" accept=".jpg, .webp, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                                        <span class="delete_file_input btn btn-outline-danger btn-sm square-btn d--none">
                                                            <i class="tio-delete"></i>
                                                        </span>
                                                        <div class="img_area_with_preview position-absolute z-index-2">
                                                            <img id="pre_gst_img_viewer" class="h-auto aspect-1 bg-white d-none" src="dummy" alt="">
                                                        </div>
                                                        <div class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                                            <div class="d-flex flex-column justify-content-center align-items-center">
                                                                <img alt="" class="w-75" src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}">
                                                                <h3 class="text-muted">{{ translate('Upload_Image') }}</h3>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <p class="text-muted mt-2">
                                                        {{ translate('image_format') }} : {{ 'Jpg, png, jpeg, webp,' }}
                                                        <br>
                                                        {{ translate('image_size') }} : {{ translate('max') }} {{ '2 MB' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                        </div>
                        <!-- Buttons for form actions -->
                        <div class="d-flex flex-wrap gap-2 justify-content-end">
                            <button type="reset" class="btn btn-secondary">{{ translate('reset') }}</button>
                            <button type="submit" class="btn btn--primary">{{ translate('submit') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

      
        <div class="col-md-12">
            <div class="card">
                <div class="px-3 py-4">
                    <!-- Search bar -->
                    <div class="row align-items-center">
                        <div class="col-sm-8 col-md-6 col-lg-8 mb-4">
                            </div>
                            <div class="col-sm-4 col-md-6 col-lg-4 mb-8">
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
                <!-- Table displaying video subcategories -->
                <div class="text-start">
                    <div class="table-responsive">
                        <table id="datatable" class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                            <thead class="thead-light thead-50 text-capitalize">
                                <tr>
                                    <th>{{ translate('SL') }}</th>
                                    <th class="text-center">{{ translate('Month_name') }} </th>
                                    <th class="text-center">{{ translate('Weather') }} </th>
                                    <th class="text-center">{{ translate('Sight') }}</th>
                                    <th class="text-center">{{ translate('Season') }}</th>
                                    <th class="text-center">{{ translate('Crowd') }}</th>
                                    <th class="text-center">{{ translate('Create_date') }}</th>
                                    <th class="text-center">{{ translate('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Loop through video subcategories -->
                                @foreach($list as $key => $value)
                                <tr>
                                    <td>{{ $key +1 }}</td>
                                    <td class="text-center">{{ translate($value['month_name']) }}</td>
                                    <td class="text-center">{{ translate($value['weather']) }}       </td>
                                    <td class="text-center">{{ translate($value['sight']) }}       </td>
                                    <td class="text-center">{{ translate($value['season']) }}       </td>
                                    <td class="text-center">{{ translate($value['crowd']) }}       </td>
                                    <td class="text-center">{{ date('d M Y',strtotime($value['created_at'])) }}       </td>    
                                    
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <a class="btn btn-outline-info btn-sm square-btn" title="{{ translate('edit') }}" href="{{route('admin.citie_visit.update',[$value['id']])}}">
                                                <i class="tio-edit"></i>
                                            </a>
                                            <a class="delete-data btn btn-outline-danger btn-sm square-btn" data-id="cities_visit-{{ $value['id'] }}">
                                                <i class="tio-delete"></i>
                                            </a>
                                            <form action="{{ route('admin.citie_visit.delete',$value['id'] )}}" method="post" id="cities_visit-{{ $value['id'] }}">
                                                @csrf 
                                                <input type="hidden" name="_method" value="delete">
                                                <input type="hidden" name="id" value="{{ $value['id'] }}">
                                            </form>
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
                        {{-- $list->links() --}}
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
<!-- Hidden HTML element for delete route -->
<span id="route-admin-videosubcategory-delete" data-url="{{ route('admin.videosubcategory.delete') }}"></span>
<!-- Toast message for video deleted -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 5">
    <div id="video-deleted-message" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-body">
            {{ translate('Video deleted') }}
        </div>
    </div>
</div>



@endsection
@push('script')
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/admin/product-add-update.js') }}"></script>
@endpush