@php 
use App\Utils\Helpers;
@endphp
@extends('layouts.back-end.app')

@section('title', translate('banner'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="d-flex justify-content-between align-items-center gap-3 mb-3">
            <h2 class="h1 mb-1 text-capitalize d-flex align-items-center gap-2">
                <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/banner.png') }}" alt="">
                {{ translate('banner_Setup') }}
                <small>
                    <strong class="text--primary"> ({{str_replace("_", " ", theme_root_path()) }})</strong>
                </small>
            </h2>
            <div class="btn-group">
                <div class="ripple-animation" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none" class="svg replaced-svg">
                        <path d="M9.00033 9.83268C9.23644 9.83268 9.43449 9.75268 9.59449 9.59268C9.75449 9.43268 9.83421 9.2349 9.83366 8.99935V5.64518C9.83366 5.40907 9.75366 5.21463 9.59366 5.06185C9.43366 4.90907 9.23588 4.83268 9.00033 4.83268C8.76421 4.83268 8.56616 4.91268 8.40616 5.07268C8.24616 5.23268 8.16644 5.43046 8.16699 5.66602V9.02018C8.16699 9.25629 8.24699 9.45074 8.40699 9.60352C8.56699 9.75629 8.76477 9.83268 9.00033 9.83268ZM9.00033 13.166C9.23644 13.166 9.43449 13.086 9.59449 12.926C9.75449 12.766 9.83421 12.5682 9.83366 12.3327C9.83366 12.0966 9.75366 11.8985 9.59366 11.7385C9.43366 11.5785 9.23588 11.4988 9.00033 11.4993C8.76421 11.4993 8.56616 11.5793 8.40616 11.7393C8.24616 11.8993 8.16644 12.0971 8.16699 12.3327C8.16699 12.5688 8.24699 12.7668 8.40699 12.9268C8.56699 13.0868 8.76477 13.1666 9.00033 13.166ZM9.00033 17.3327C7.84755 17.3327 6.76421 17.1138 5.75033 16.676C4.73644 16.2382 3.85449 15.6446 3.10449 14.8952C2.35449 14.1452 1.76088 13.2632 1.32366 12.2493C0.886437 11.2355 0.667548 10.1521 0.666992 8.99935C0.666992 7.84657 0.885881 6.76324 1.32366 5.74935C1.76144 4.73546 2.35505 3.85352 3.10449 3.10352C3.85449 2.35352 4.73644 1.7599 5.75033 1.32268C6.76421 0.88546 7.84755 0.666571 9.00033 0.666016C10.1531 0.666016 11.2364 0.884905 12.2503 1.32268C13.2642 1.76046 14.1462 2.35407 14.8962 3.10352C15.6462 3.85352 16.24 4.73546 16.6778 5.74935C17.1156 6.76324 17.3342 7.84657 17.3337 8.99935C17.3337 10.1521 17.1148 11.2355 16.677 12.2493C16.2392 13.2632 15.6456 14.1452 14.8962 14.8952C14.1462 15.6452 13.2642 16.2391 12.2503 16.6768C11.2364 17.1146 10.1531 17.3332 9.00033 17.3327ZM9.00033 15.666C10.8475 15.666 12.4206 15.0168 13.7195 13.7185C15.0184 12.4202 15.6675 10.8471 15.667 8.99935C15.667 7.15213 15.0178 5.57907 13.7195 4.28018C12.4212 2.98129 10.8481 2.33213 9.00033 2.33268C7.1531 2.33268 5.58005 2.98185 4.28116 4.28018C2.98227 5.57852 2.3331 7.15157 2.33366 8.99935C2.33366 10.8466 2.98283 12.4196 4.28116 13.7185C5.57949 15.0174 7.15255 15.6666 9.00033 15.666Z" fill="currentColor"></path>
                    </svg>
                </div>

                <div class="dropdown-menu dropdown-menu-right bg-aliceblue border border-color-primary-light p-4 dropdown-w-lg-30">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/note.png') }}" alt="">
                        <h5 class="text-primary mb-0">{{ translate('note') }}</h5>
                    </div>
                    <p class="title-color font-weight-medium mb-0">{{ translate('currently_you_are_managing_banners_for') }} {{ucwords(str_replace("_", " ", theme_root_path())) }}.{{ translate('these_saved_data_is_only_applicable_only_for_') }}{{ucwords(str_replace("_", " ", theme_root_path())) }}.{{ translate('if_you_change_theme_from_theme_setup_these_banners_will_not_be_shown_in_changed_theme._You_have_upload_all_the_banners_over_again _according_to_the_new_theme_ratio_and_sizes._If_you_switch_back_to_') }}{{ucwords(str_replace("_", " ", theme_root_path())) }}{{ translate('_again_,_you_will_see_the_saved_data.') }}</p>
                </div>
            </div>
        </div>

        <div class="row pb-4 d--none text-start" id="main-banner">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 text-capitalize">{{ translate('banner_form') }}</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.banner.store') }}" method="post" enctype="multipart/form-data"
                              class="banner_form">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <input type="hidden" id="id" name="id">

                                    <div class="form-group" id="startendDate">
                                        <div class="row">
                                            <div class="col-md-6 col-lg-6">
                                                <div class="form-group">
                                                    <label class="title-color">{{ translate('start_date') }}</label>
                                                    <input class="form-control text-align-direction" type="date" name="start_date"
                                                        id="StartDateSelected" placeholder="Start Date">
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-lg-6">
                                                <div class="form-group">
                                                    <label class="title-color">{{ translate('end_date') }}</label>
                                                    <input class="form-control text-align-direction" type="date" name="end_date"
                                                        id="EndDateSelected" placeholder="End Date">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="name" class="title-color text-capitalize">
                                            {{ translate('banner_type') }}
                                        </label>
                                        <select class="js-example-responsive form-control w-100" name="banner_type" required id="banner_type_select">
                                            @foreach($bannerTypes as $key => $banner)
                                                <option value="{{ $key }}">{{ $banner }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="name" class="title-color text-capitalize">{{ translate('banner_URL') }}</label>
                                        <input type="url" name="url" class="form-control" id="url"  placeholder="{{ translate('Enter_url') }}">
                                    </div>

                                    <div class="form-group">
                                        <label for="resource_id"
                                               class="title-color text-capitalize">{{ translate('resource_type') }}</label>
                                        <select class="js-example-responsive form-control w-100 action-display-data"
                                                name="resource_type" id="resource_type" required>
                                            <option value="product">{{ translate('product') }}</option>
                                            <option value="category">{{ translate('category') }}</option>
                                            <option value="shop">{{ translate('shop') }}</option>
                                            <option value="brand">{{ translate('brand') }}</option>
                                            <option value="mahakal">{{ translate('mahakal') }}</option>
                                            <option value="mahakalapp">{{ translate('mahakal_app') }}</option>
                                            <option value="appsection">{{ translate('app_section') }}</option>
                                            <option value="astrology">{{ translate('astrology') }}</option>
                                            <option value="auspicious_occasion">{{ translate('auspicious_occasion') }}</option>
                                            <option value="chat">{{ translate('chat') }}</option>
                                            <option value="events">{{ translate('events') }}</option>
                                        </select>

                                    </div>

                                    <div class="form-group mb-0" id="resource-product">
                                        <label for="product_id"
                                               class="title-color text-capitalize">{{ translate('product') }}</label>
                                        <select class="js-example-responsive form-control w-100"
                                                name="product_id">
                                            @foreach($products as $product)
                                                <option value="{{ $product['id'] }}">{{ $product['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group mb-0 d--none" id="resource-category">
                                        <label for="name"
                                               class="title-color text-capitalize">{{ translate('category') }}</label>
                                        <select class="js-example-responsive form-control w-100"
                                                name="category_id">
                                            @foreach($categories as $category)
                                                <option value="{{ $category['id'] }}">{{ $category['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group mb-0 d--none" id="resource-shop">
                                        <label for="shop_id" class="title-color">{{ translate('shop') }}</label>
                                        <select class="w-100 js-example-responsive form-control" name="shop_id">
                                            @foreach($shops as $shop)
                                                <option value="{{ $shop['id'] }}">{{ $shop['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group mb-0 d--none" id="resource-brand">
                                        <label for="brand_id"
                                               class="title-color text-capitalize">{{ translate('brand') }}</label>
                                        <select class="js-example-responsive form-control w-100"
                                                name="brand_id">
                                            @foreach($brands as $brand)
                                                <option value="{{ $brand['id'] }}">{{ $brand['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group mb-0" id="resource-mahakalapp">
                                        <label for="mahakalapp_id" class="title-color text-capitalize">
                                            {{ translate('mahakal_app') }}
                                        </label>
                                        <select class="form-control w-100" name="mahakalapp_id" id="mahakalapp_id">
                                            <option value="">{{ translate('Select Service') }}</option>
                                            @foreach($services as $service)
                                                <option value="{{ $service->id }}">{{ $service->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group mb-0 d-none" id="sub-service-container">
                                        <label for="sub_service_id" class="title-color text-capitalize">
                                            {{ translate('sub_service') }}
                                        </label>
                                        <select class="form-control w-100" name="sub_service_id" id="sub_service_id">
                                            <option value="">{{ translate('Select') }}</option>
                                        </select>
                                    </div> 
                                    <div class="form-group mb-0 d--none" id="resource-appsection">
                                        <label for="appsection_id" class="title-color text-capitalize">
                                            {{ translate('app_section') }}
                                        </label>
                                        <select class="js-example-responsive form-control w-100" name="appsection_id" id="appsection">
                                            <option value="">Select Section</option>
                                            @foreach($appsections as $appsection)
                                                <option value="{{ $appsection['id'] }}">{{ $appsection['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>


                                    <div class="form-group mb-0" style="display: none;" id="imageTypeContainer">
                                        <label for="imageType">Image Type</label>
                                        <select id="imageType" class="form-control w-100" name="image_type">
                                            <!-- Options will be dynamically populated -->
                                        </select>
                                    </div>

                                    <!-- For Theme Fashion - New input Field - Start -->
                                    @if(theme_root_path() == 'theme_fashion')
                                    <div class="form-group mt-4 input-field-for-main-banner">
                                        <label for="button_text" class="title-color text-capitalize">{{ translate('Button_Text') }}</label>
                                        <input type="text" name="button_text" class="form-control" id="button_text" placeholder="{{ translate('Enter_button_text') }}">
                                    </div>
                                    <div class="form-group mt-4 mb-0 input-field-for-main-banner">
                                        <label for="background_color" class="title-color text-capitalize">{{ translate('background_color') }}</label>
                                        <input type="color" name="background_color" class="form-control form-control_color w-100" id="background_color" value="#fee440">
                                    </div>
                                    @endif
                                </div>
                                <div class="col-md-6 d-flex flex-column justify-content-center">
                                    <div>
                                        <div class="mx-auto text-center">
                                            <div class="uploadDnD">
                                                <div class="form-group inputDnD input_image" data-title="{{ 'Drag and drop file or Browse file' }}">
                                                    <input type="file" name="image" class="form-control-file text--primary font-weight-bold" onchange="readUrl(this)" accept=".jpg, .png, .jpeg, .gif, .bmp, .webp |image/*">
                                                </div>
                                            </div>
                                        </div>
                                        <label for="name" class="title-color text-capitalize">
                                            {{ translate('banner_image') }}
                                        </label>
                                        <span class="ml-1 text-info" id="brand_image_text"></span>
                                        <span class="title-color" id="theme_ratio">( {{ translate('ratio') }} 4:1  )</span>
                                        <p>{{ translate('banner_Image_ratio_is_not_same_for_all_sections_in_website') }}. {{ translate('please_review_the_ratio_before_upload') }}</p>
                                        <!-- For Theme Fashion - New input Field - Start -->
                                        @if(theme_root_path() == 'theme_fashion')
                                        <div class="form-group mt-4 input-field-for-main-banner">
                                            <label for="title" class="title-color text-capitalize">{{ translate('Title') }}</label>
                                            <input type="text" name="title" class="form-control" id="title" placeholder="{{ translate('Enter_banner_title') }}">
                                        </div>
                                        <div class="form-group mb-0 input-field-for-main-banner">
                                            <label for="sub_title" class="title-color text-capitalize">{{ translate('Sub_Title') }}</label>
                                            <input type="text" name="sub_title" class="form-control" id="sub_title" placeholder="{{ translate('Enter_banner_sub_title') }}">
                                        </div>
                                        @endif
                                        <!-- For Theme Fashion - New input Field - End -->

                                    </div>
                                </div>
                                <input type="hidden" name="pooja_id" id="pooja_id">
                                <div class="col-12 d-flex justify-content-end flex-wrap gap-10">
                                    <button class="btn btn-secondary cancel px-4" type="reset">{{ translate('reset') }}</button>
                                    <button id="add" type="submit"
                                            class="btn btn--primary px-4">{{ translate('save') }}</button>
                                    <button id="update"
                                       class="btn btn--primary d--none text-white">{{ translate('update') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row" id="banner-table">
            <div class="col-md-12">
                <div class="card">
                    <div class="px-3 py-4">
                        <div class="row align-items-center">
                            <div class="col-md-4 col-lg-6 mb-2 mb-md-0">
                                <h5 class="mb-0 text-capitalize d-flex gap-2">
                                    {{ translate('banner_table') }}
                                    <span
                                        class="badge badge-soft-dark radius-50 fz-12">{{ $banners->total() }}</span>
                                </h5>
                            </div>
                            <div class="col-md-8 col-lg-6">
                                <div class="row gy-2 gx-2 align-items-center text-left">
                                    <div class="col-sm-12 col-md-9">
                                        <form action="{{ url()->current() }}" method="GET">
                                            <div class="row gy-2 gx-2 align-items-center text-left">
                                                <div class="col-sm-12 col-md-9">
                                                    <select class="form-control __form-control" name="searchValue" id="date_type">
                                                        <option value="">{{ translate('all') }}</option>
                                                        @foreach($bannerTypes as $key => $banner)
                                                            <option value="{{ $key }}" {{ request('searchValue') == $key ? 'selected':'' }}>{{ $banner }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-sm-12 col-md-3">
                                                    <button type="submit" class="btn btn--primary px-4 w-100 text-nowrap">
                                                        {{ translate('filter') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="col-sm-12 col-md-3">
                                        @if (Helpers::modules_permission_check('Banner Setup', 'Banner Setup', 'add'))
                                        <div id="banner-btn">
                                            <button id="main-banner-add" class="btn btn--primary text-nowrap text-capitalize">
                                                <i class="tio-add"></i>
                                                {{ translate('add_banner') }}
                                            </button>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="columnSearchDatatable"
                               class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                            <thead class="thead-light thead-50 text-capitalize">
                            <tr>
                                <th class="pl-xl-5">{{ translate('SL') }}</th>
                                <th>{{ translate('image') }}</th>
                                <th>{{ translate('banner_type') }}</th>
                                <th>{{ translate('start_date') }}</th>
                                <th>{{ translate('end_date') }}</th>
                                @if (Helpers::modules_permission_check('Banner Setup', 'Banner Setup', 'published'))
                                <th>{{ translate('published') }}</th>
                                @endif
                                <th class="text-center">{{ translate('action') }}</th>
                            </tr>
                            </thead>
                            @foreach($banners as $key=>$banner)
                                <tbody>
                                <tr id="data-{{ $banner->id}}">
                                    <td class="pl-xl-5">{{ $banners->firstItem()+$key}}</td>
                                    <td>
                                        <img class="ratio-4:1" width="80" alt=""
                                             src="{{ getValidImage(path: 'storage/app/public/banner/'.$banner['photo'] , type: 'backend-banner') }}">
                                    </td>
                                    <td>{{ translate(str_replace('_',' ',$banner->banner_type)) }}</td>
                                    <td>{{ translate(str_replace('_',' ',$banner->start_date)) }}</td>
                                    <td>{{ translate(str_replace('_',' ',$banner->end_date)) }}</td>
                                    @if (Helpers::modules_permission_check('Banner Setup', 'Banner Setup', 'published'))
                                    <td>
                                        <form action="{{ route('admin.banner.status') }}" method="post" id="banner-status{{ $banner['id'] }}-form">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $banner['id'] }}">
                                            <label class="switcher">
                                                <input type="checkbox" class="switcher_input toggle-switch-message" name="status"
                                                    id="banner-status{{ $banner['id'] }}" value="1" {{ $banner['published'] == 1 ? 'checked' : '' }}
                                                    data-modal-id="toggle-status-modal"
                                                    data-toggle-id="banner-status{{ $banner['id'] }}"
                                                    data-on-image="banner-status-on.png"
                                                    data-off-image="banner-status-off.png"
                                                    data-on-title="{{ translate('Want_to_Turn_ON').' '.translate(str_replace('_',' ',$banner->banner_type)).' '.translate('status') }}"
                                                    data-off-title="{{ translate('Want_to_Turn_OFF').' '.translate(str_replace('_',' ',$banner->banner_type)).' '.translate('status') }}"
                                                    data-on-message="<p>{{ translate('if_enabled_this_banner_will_be_available_on_the_website_and_customer_app') }}</p>"
                                                    data-off-message="<p>{{ translate('if_disabled_this_banner_will_be_hidden_from_the_website_and_customer_app') }}</p>">
                                                <span class="switcher_control"></span>
                                            </label>
                                        </form>
                                    </td>
                                    @endif
                                    <td>
                                        <div class="d-flex gap-10 justify-content-center">
                                            @if (Helpers::modules_permission_check('Banner Setup', 'Banner Setup', 'edit'))
                                            <a class="btn btn-outline--primary btn-sm cursor-pointer edit"
                                               title="{{ translate('edit') }}"
                                               href="{{ route('admin.banner.update',[$banner['id']]) }}">
                                                <i class="tio-edit"></i>
                                            </a>
                                            @endif
                                            @if (Helpers::modules_permission_check('Banner Setup', 'Banner Setup', 'delete'))
                                            <a class="btn btn-outline-danger btn-sm cursor-pointer banner-delete-button"
                                               title="{{ translate('delete') }}"
                                               id="{{ $banner['id'] }}">
                                                <i class="tio-delete"></i>
                                            </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            @endforeach
                        </table>
                    </div>

                    <div class="table-responsive mt-4">
                        <div class="px-4 d-flex justify-content-lg-end">
                            {{ $banners->links() }}
                        </div>
                    </div>

                    @if(count($banners)==0)
                        <div class="text-center p-4">
                            <img class="mb-3 w-160"
                                 src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}"
                                 alt="Image Description">
                            <p class="mb-0">{{ translate('no_data_to_show') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <span id="route-admin-banner-store" data-url="{{ route('admin.banner.store') }}"></span>
    <span id="route-admin-banner-delete" data-url="{{ route('admin.banner.delete') }}"></span>
@endsection

@push('script')
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/banner.js') }}"></script>

    <script>
        "use strict";

        $(document).ready(function () {

            getThemeWiseRatio();

            $('#banner_type_select').on('change', function() {
            let inputValue = $(this).val().toString();
            if (inputValue === 'Mahakal App Banner' || inputValue === 'Astrology Banner' || inputValue === 'Auspicious Occasion Banner' || inputValue === 'Chat Banner' || inputValue === 'Events Banner' || inputValue === 'E Commerece App Banner') {
                $('#theme_ratio').text('16:9').show();  
                $('#brand_image_text').hide(); 
            } else {
                getThemeWiseRatio();
            }
        });

            $('#imageType').on('change', function() {
                const selectedAppSection = $('#appsection').find('option:selected').text().trim().toLowerCase();
                if (selectedAppSection === 'shop' || selectedAppSection === 'auspicious occasion consultation') {
                    getThemeWiseRatio();
                }
            });

            $('#resource_type').on('change', function() {
                const selectedResourceType = $(this).val();
                if (selectedResourceType === 'appsection') {
                    $('#imageTypeContainer').show();
                } else {
                    $('#imageTypeContainer').hide();
                    $('#additional-select').remove(); 
                    $('#additional-input').remove();
                }
            });

            // Event handler for app section change
            $('#appsection').on('change', function() {
                const selectedText = $(this).find('option:selected').text().trim().toLowerCase();
                const imageTypeOptions = {
                    'panchang': '<option value="left">Left</option>',
                    'vedic astrology': '<option value="right">Right</option>',
                    'shop': `
                        <option value="center">Center</option>
                        <option value="right-top">Right-Top</option>
                        <option value="right-bottom">Right-Bottom</option>
                    `,
                    'astrology consultation': '<option value="right">Right</option>',
                    'auspicious occasion consultation': `
                        <option value="left">Left</option>
                        <option value="right-top">Right-Top</option>
                        <option value="right-bottom">Right-Bottom</option>
                    `,
                    'default': ''  
                };
                $('#imageType').html(imageTypeOptions[selectedText] || imageTypeOptions['default']);

                if (selectedText === 'shop') {
                    const additionalSelectField = `
                        <br><div class="form-group mb-0" id="additional-select">
                          <select id="additional-select-options" class="form-control w-100" name="app_section_resource_type">
                            <option value="">Select an option</option>
                            <option value="category">Category</option>
                            <option value="product">Product</option>
                          </select>
                        </div>
                    `;
                    $('#imageTypeContainer').append(additionalSelectField);
                } else {
                    $('#additional-select').remove();
                    $('#additional-input').remove(); 
                }
                
                getThemeWiseRatio();
            });

            $(document).on('change', '#additional-select-options', function () {
                const selectedOption = $(this).val();
                $('#additional-input').remove(); 

                if (selectedOption) {
                    let inputField = '';

                    // Add input field based on selected option
                    if (selectedOption === 'category') {
                        inputField = `
                            <div class="form-group mt-2" id="additional-input">
                              <label for="category-input">Select Category</label>
                              <select id="category-input" class="form-control" name="app_section_resource_id">
                                ${generateCategoryOptions()}
                              </select>
                            </div>
                        `;
                    } else if (selectedOption === 'product') {
                        inputField = `
                            <div class="form-group mt-2" id="additional-input">
                              <label for="product-input">Select Product</label>
                              <select id="product-input" class="form-control" name="app_section_resource_id">
                                ${generateProductOptions()}
                              </select>
                            </div>
                        `;
                    }

                    $('#additional-select').after(inputField);
                }
            });

            function generateCategoryOptions() {
                const categories = @json($categories);
                let options = '';
                categories.forEach(category => {
                    options += `<option value="${category.id}">${category.name}</option>`;
                });
                return options;
            }

            function generateProductOptions() {
                const products = @json($products);
                let options = '';
                products.forEach(product => {
                    options += `<option value="${product.id}">${product.name}</option>`;
                });
                return options;
            }

            $('#resource_type').trigger('change');
        });

        function getThemeWiseRatio() {
            let banner_type = $('#banner_type_select').val();
            let theme = '{{ theme_root_path() }}';
            let theme_ratio = {!! json_encode(THEME_RATIO) !!};
            
            let get_ratio;
            const selectedAppSection = $('#appsection').find('option:selected').text().trim().toLowerCase();
            const selectedImageType = $('#imageType').val();

            if (banner_type === 'Mahakal App Banner' || banner_type === 'Astrology Banner' || banner_type === 'Auspicious Occasion Banner' || banner_type === 'Chat Banner' || banner_type === 'Events Banner' || banner_type === 'E Commerece App Banner') {
                get_ratio = '16:9'; 
                $('#theme_ratio').show();
                $('#theme_ratio').text(get_ratio);
                $('#brand_image_text').hide(); 
            } else if (selectedAppSection === 'panchang' && selectedImageType === 'left') {
                get_ratio = theme_ratio[theme]['panchang'];
                $('#theme_ratio').show();
                $('#theme_ratio').hide();
                $('#brand_image_text').text(theme_ratio[theme]['Brand Image']).show();
            } else if (selectedAppSection === 'vedic astrology' && selectedImageType === 'right') {
                get_ratio = theme_ratio[theme]['vedic astrology'];
                $('#theme_ratio').show();
                $('#theme_ratio').hide();
                $('#brand_image_text').text(theme_ratio[theme]['Brand Image']).show();
            }  else if (selectedAppSection === 'astrology consultation' && selectedImageType === 'right') {
                get_ratio = theme_ratio[theme]['astrology consultation'];
                $('#theme_ratio').show();
                $('#theme_ratio').hide();
                $('#brand_image_text').text(theme_ratio[theme]['Brand Image']).show();
            } else if (selectedAppSection === 'shop' && selectedImageType === 'center') {
                get_ratio = theme_ratio[theme]['shop'];
                $('#theme_ratio').show();
                $('#theme_ratio').hide();
                $('#brand_image_text').text(theme_ratio[theme]['Brand Image']).show();
            } else if (selectedAppSection === 'shop' && selectedImageType === 'right-top') {
                get_ratio = theme_ratio[theme]['shop'];
                $('#theme_ratio').show();
                $('#theme_ratio').show();
                $('#brand_image_text').hide();
            } else if (selectedAppSection === 'auspicious occasion consultation' && selectedImageType === 'left') {
                get_ratio = theme_ratio[theme]['auspicious occasion consultation'];
                $('#theme_ratio').show();
                $('#theme_ratio').hide();
                $('#brand_image_text').text(theme_ratio[theme]['Brand Image']).show();
            } else if (selectedAppSection === 'auspicious occasion consultation' && selectedImageType === 'right-top') {
                get_ratio = theme_ratio[theme]['auspicious occasion consultation'];
                $('#theme_ratio').show();
                $('#theme_ratio').show();
                $('#brand_image_text').hide();
            } else {
                get_ratio = theme_ratio[theme][banner_type] || '4:1';
                $('#theme_ratio').show();
                $('#brand_image_text').hide();
                $('#theme_ratio').text(get_ratio);
            }
        }
    </script>

    <script>
        $(document).ready(function() {
            $('#resource_type').change(function() {
                var selectedResourceType = $(this).val();
                if (selectedResourceType === 'appsection') {
                    $('#imageTypeContainer').show();
                } else {
                    $('#imageTypeContainer').hide();
                }
            });

            // Trigger the change event on page load to set the initial state
            $('#resource_type').trigger('change');
        });

    </script>

    <script>
        let subServices       = @json($subServices);
        let vipPooja    = @json($vipPooja);
        let anushthan = @json($anushthan);
        let chadhava          = @json($chadhava);
        let tour          = @json($tour);
        let event          = @json($event);
        let darshan          = @json($darshan);
        let offlinePooja          = @json($offlinePooja);
        let donation          = @json($donation);

        document.getElementById('mahakalapp_id').addEventListener('change', function () {
            let serviceId = this.value;
            let subServiceContainer = document.getElementById('sub-service-container');
            let subServiceSelect = document.getElementById('sub_service_id');

            subServiceSelect.innerHTML = '<option value="">{{ translate("Select") }}</option>';

            if (serviceId && subServices[serviceId]) {
                subServices[serviceId].forEach(function (sub) {
                    let opt = document.createElement('option');
                    opt.value = sub.id;
                    opt.textContent = sub.name;
                    subServiceSelect.appendChild(opt);
                });
                subServiceContainer.classList.remove('d-none');
            } 
            else if (serviceId == 50) {
                vipPooja.forEach(function (pooja) {
                    let opt = document.createElement('option');
                    opt.value = pooja.id;
                    opt.textContent = pooja.name;
                    subServiceSelect.appendChild(opt);
                });
                subServiceContainer.classList.remove('d-none');
            } 
            else if (serviceId == 51) {
                anushthan.forEach(function (pooja) {
                    let opt = document.createElement('option');
                    opt.value = pooja.id;
                    opt.textContent = pooja.name;
                    subServiceSelect.appendChild(opt);
                });
                subServiceContainer.classList.remove('d-none');
            } 
            else if (serviceId == 52) {
                chadhava.forEach(function (chadhava) {
                    let opt = document.createElement('option');
                    opt.value = chadhava.id;
                    opt.textContent = chadhava.name;
                    subServiceSelect.appendChild(opt);
                });
                subServiceContainer.classList.remove('d-none');
            } 
            else if (serviceId == 272) {
                tour.forEach(function (tour) {
                    let opt = document.createElement('option');
                    opt.value = tour.id;
                    opt.textContent = tour.tour_name;
                    subServiceSelect.appendChild(opt);
                });
                subServiceContainer.classList.remove('d-none');
            } 
            else if (serviceId == 273) {
                event.forEach(function (event) {
                    let opt = document.createElement('option');
                    opt.value = event.id;
                    opt.textContent = event.event_name;
                    subServiceSelect.appendChild(opt);
                });
                subServiceContainer.classList.remove('d-none');
            }
            else if (serviceId == 274) {
                donation.forEach(function (donation) {
                    let opt = document.createElement('option');
                    opt.value = donation.id;
                    opt.textContent = donation.name;
                    subServiceSelect.appendChild(opt);
                });
                subServiceContainer.classList.remove('d-none');
            }
            else if (serviceId == 275) {
                darshan.forEach(function (darshan) {
                    let opt = document.createElement('option');
                    opt.value = darshan.id;
                    opt.textContent = darshan.name;
                    subServiceSelect.appendChild(opt);
                });
                subServiceContainer.classList.remove('d-none');
            } 
            else if (serviceId == 276) {
                offlinePooja.forEach(function (offlinepooja) {
                    let opt = document.createElement('option');
                    opt.value = offlinepooja.id;
                    opt.textContent = offlinepooja.name;
                    subServiceSelect.appendChild(opt);
                });
                subServiceContainer.classList.remove('d-none');
            } 
            else {
                subServiceContainer.classList.add('d-none');
            }
        });

        document.getElementById('sub_service_id').addEventListener('change', function () {
            let selectedId = this.value;
            document.getElementById('pooja_id').value = selectedId;
        });
    </script>

@endpush
