@php 
use App\Utils\Helpers;
@endphp
@extends('layouts.back-end.app')

@section('title', translate('banner'))

@section('content')
    <div class="content container-fluid">

        <div class="d-flex justify-content-between mb-3">
            <div>
                <h2 class="h1 mb-1 text-capitalize d-flex align-items-center gap-2">
                    <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/banner.png') }}" alt="">
                    {{ translate('banner_update_form') }}
                </h2>
            </div>
            <div>
                <a class="btn btn--primary text-white" href="{{ route('admin.banner.list') }}">
                    <i class="tio-chevron-left"></i> {{ translate('back') }}</a>
            </div>
        </div>

        <div class="row text-start">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.banner.update', [$banner['id']]) }}" method="post" enctype="multipart/form-data"
                              class="banner_form">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="hidden" id="id" name="id">
                                    </div>

                                    <div class="form-group" id="startendDate">
                                        <div class="row">
                                            <div class="col-md-6 col-lg-6">
                                                <div class="form-group">
                                                    <label class="title-color">{{ translate('start_date') }}</label>
                                                    <input 
                                                        class="form-control text-align-direction" 
                                                        type="date" 
                                                        name="start_date"
                                                        id="StartDateSelected" 
                                                        placeholder="{{ translate('Start Date') }}"
                                                        value="{{ old('start_date', $banner->start_date ?? '') }}"
                                                    >
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-lg-6">
                                                <div class="form-group">
                                                    <label class="title-color">{{ translate('end_date') }}</label>
                                                    <input 
                                                        class="form-control text-align-direction" 
                                                        type="date" 
                                                        name="end_date"
                                                        id="EndDateSelected" 
                                                        placeholder="{{ translate('End Date') }}"
                                                        value="{{ old('end_date', $banner->end_date ?? '') }}"
                                                    >
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="form-group">
                                        <label for="name" class="title-color text-capitalize">{{ translate('banner_type') }}</label>
                                        <select class="js-example-responsive form-control w-100" name="banner_type" required id="banner_type_select">
                                            @foreach($bannerTypes as $key => $singleBanner)
                                                <option value="{{ $key }}" {{ $banner['banner_type'] == $key ? 'selected':''}}>{{ $singleBanner }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="name" class="title-color text-capitalize">{{ translate('banner_URL') }}</label>
                                        <input type="url" name="url" class="form-control" id="url"  placeholder="{{ translate('enter_url') }}" value="{{$banner['url']}}">
                                    </div>

                                    <div class="form-group">
                                        <label for="resource_id" class="title-color text-capitalize">{{ translate('resource_type') }}</label>
                                        <select class="js-example-responsive form-control w-100 action-display-data" name="resource_type" id="resource_type" required>
                                            <option value="product" {{$banner['resource_type']=='product'?'selected':''}}>{{ translate('product') }}</option>
                                            <option value="category" {{$banner['resource_type']=='category'?'selected':''}}>{{ translate('category') }}</option>
                                            <option value="shop" {{$banner['resource_type']=='shop'?'selected':''}}>{{ translate('shop') }}</option>
                                            <option value="brand" {{$banner['resource_type']=='brand'?'selected':''}}>{{ translate('brand') }}</option>
                                            <option value="mahakal" {{$banner['resource_type']=='mahakal'?'selected':''}}>{{ translate('mahakal') }}</option>
                                            <option value="mahakalapp" {{$banner['resource_type']=='mahakalapp'?'selected':''}}>{{ translate('mahakal_app') }}</option>
                                            <option value="appsection" {{$banner['resource_type']=='appsection'?'selected':''}}>{{ translate('app_section') }}</option>
                                            <option value="astrology" {{$banner['resource_type']=='astrology'?'selected':''}}>{{ translate('astrology') }}</option>
                                            <option value="auspicious_occasion" {{$banner['resource_type']=='auspicious_occasion'?'selected':''}}>{{ translate('auspicious_occasion') }}</option>
                                            <option value="chat" {{$banner['resource_type']=='chat'?'selected':''}}>{{ translate('chat') }}</option>
                                            <option value="events" {{$banner['resource_type']=='events'?'selected':''}}>{{ translate('events') }}</option>
                                        </select>
                                    </div>

                                    <div class="form-group mb-0 {{$banner['resource_type']=='product'?'d--block':'d--none'}}" id="resource-product">
                                        <label for="product_id" class="title-color text-capitalize">{{ translate('product') }}</label>
                                        <select class="js-example-responsive form-control w-100"
                                                name="product_id">
                                            @foreach($products as $product)
                                                <option value="{{$product['id']}}" {{$banner['resource_id']==$product['id']?'selected':''}}>{{$product['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group mb-0 {{ $banner['resource_type']=='category'?'d--block':'d--none' }}" id="resource-category">
                                        <label for="name" class="title-color text-capitalize">{{ translate('category') }}</label>
                                        <select class="js-example-responsive form-control w-100"
                                                name="category_id">
                                            @foreach($categories as $category)
                                                <option value="{{$category['id']}}" {{$banner['resource_id']==$category['id']?'selected':''}}>{{$category['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group mb-0 {{ $banner['resource_type']=='shop'?'d--block':'d--none' }}" id="resource-shop">
                                        <label for="shop_id" class="title-color text-capitalize">{{ translate('shop') }}</label>
                                        <select class="js-example-responsive form-control w-100"
                                                name="shop_id">
                                            @foreach($shops as $shop)
                                                <option value="{{$shop['id']}}" {{$banner['resource_id']==$shop['id']?'selected':''}}>{{$shop['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group mb-0 {{$banner['resource_type']=='brand'?'d--block':'d--none'}}" id="resource-brand">
                                        <label for="brand_id" class="title-color text-capitalize">{{ translate('brand') }}</label>
                                        <select class="js-example-responsive form-control w-100"
                                                name="brand_id">
                                            @foreach($brands as $brand)
                                                <option value="{{$brand['id']}}" {{$banner['resource_id']==$brand['id']?'selected':''}}>{{$brand['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group mb-0 {{ $banner['resource_type']=='mahakalapp'?'d--block':'d--none' }}" id="resource-mahakalapp">
                                        <label for="mahakalapp_id" class="title-color text-capitalize">
                                            {{ translate('mahakal_app') }}
                                        </label>
                                        <select class="form-control w-100" name="mahakalapp_id" id="mahakalapp_id">
                                            <option value="">{{ translate('Select Service') }}</option>
                                            @foreach($services as $service)
                                                <option value="{{ $service->id }}" {{ $banner['resource_id']==$service->id ? 'selected':'' }}>
                                                    {{ $service->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group mb-0 {{ $banner['resource_type']=='mahakalapp'?'':'d-none' }}" id="sub-service-container">
                                        <label for="sub_service_id" class="title-color text-capitalize">
                                            {{ translate('sub_service') }}
                                        </label>
                                        <select class="form-control w-100" name="sub_service_id" id="sub_service_id">
                                            <option value="">{{ translate('Select') }}</option>
                                        </select>
                                    </div>
                                    <div class="form-group mb-0 {{$banner['resource_type']=='appsection'?'d--block':'d--none'}}" id="resource-appsection">
                                        <label for="appsection_id" class="title-color text-capitalize">{{ translate('app_section') }}</label>
                                        <select class="js-example-responsive form-control w-100"
                                                name="appsection_id" id="appsection">
                                            @foreach($appsections as $appsection)
                                                <option value="{{$appsection['id']}}" {{$banner['resource_id']==$appsection['id']?'selected':''}}>{{$appsection['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                   <div class="form-group mb-0" style="display: none;" id="imageTypeContainer">
                                        <label for="imageType">Image Type</label>
                                        <select id="imageType" class="form-control w-100" name="image_type" data-selected-type="{{ $banner['image_type'] }}">
                                            <!-- Options will be dynamically populated -->
                                        </select>
                                    </div>


                                    @if(theme_root_path() == 'theme_fashion')
                                    <div class="form-group mt-4 input-field-for-main-banner {{$banner['banner_type'] !='Main Banner'?'d-none':''}}">
                                        <label for="button_text" class="title-color text-capitalize">{{ translate('Button_Text') }}</label>
                                        <input type="text" name="button_text" class="form-control" id="button_text" placeholder="{{ translate('Enter_button_text') }}" value="{{$banner['button_text']}}">
                                    </div>
                                    <div class="form-group mt-4 mb-0 input-field-for-main-banner {{$banner['banner_type'] !='Main Banner'?'d-none':''}}">
                                        <label for="background_color" class="title-color text-capitalize">{{ translate('background_color') }}</label>
                                        <input type="color" name="background_color" class="form-control form-control_color w-100" id="background_color" value="{{$banner['background_color']}}">
                                    </div>
                                    @endif

                                </div>
                                <div class="col-md-6 d-flex flex-column justify-content-center">
                                    <div>
                                        <div class="mx-auto text-center">
                                            <div class="uploadDnD">
                                                <div class="form-group inputDnD input_image input_image_edit"
                                                     data-bg-img="{{ dynamicStorage(path: 'storage/app/public/banner') }}/{{$banner['photo']}}"
                                                     data-title="{{ file_exists('storage/app/public/banner/'.$banner['photo']) ? '': 'Drag and drop file or Browse file'}}">
                                                    <input type="file" name="image" class="form-control-file text--primary font-weight-bold" onchange="readUrl(this)"  accept=".jpg, .png, .jpeg, .gif, .bmp, .webp |image/*">
                                                </div>
                                            </div>
                                        </div>
                                        <label for="name" class="title-color text-capitalize">
                                            <span class="input-label-secondary cursor-pointer" data-toggle="tooltip" data-placement="right" title="" data-original-title="{{ translate('banner_image_ratio_is_not_same_for_all_sections_in_website').' '.translate('Please_review_the_ratio_before_upload') }}">
                                                <img alt="" width="16" src={{dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }} alt="" class="m-1">
                                            </span>
                                            {{ translate('banner_image') }}
                                        </label>
                                        <span class="ml-1 text-info" id="brand_image_text"></span>
                                        <!-- <span class="title-color" id="theme_ratio">( {{ translate('ratio') }} 4:1  )</span> -->
                                        <span class="ml-1 text-info" id="brand_image_text"></span>
                                        <span class="title-color" id="theme_ratio">( {{ translate('ratio') }} )</span>
                                        <p>{{ translate('banner_Image_ratio_is_not_same_for_all_sections_in_website') }}. {{ translate('please_review_the_ratio_before_upload') }}</p>

                                         @if(theme_root_path() == 'theme_fashion')
                                         <div class="form-group mt-4 input-field-for-main-banner {{$banner['banner_type'] !='Main Banner'?'d-none':''}}">
                                             <label for="title" class="title-color text-capitalize">{{ translate('Title') }}</label>
                                             <input type="text" name="title" class="form-control" id="title" placeholder="{{ translate('Enter_banner_title') }}" value="{{$banner['title']}}">
                                         </div>
                                         <div class="form-group mb-0 input-field-for-main-banner {{$banner['banner_type'] !='Main Banner'?'d-none':''}}">
                                             <label for="sub_title" class="title-color text-capitalize">{{ translate('Sub_Title') }}</label>
                                             <input type="text" name="sub_title" class="form-control" id="sub_title" placeholder="{{ translate('Enter_banner_sub_title') }}" value="{{$banner['sub_title']}}">
                                         </div>
                                         @endif
                                    </div>
                                </div>
                                <input type="hidden" name="pooja_id" id="pooja_id" value="{{ $banner['pooja_id'] ?? '' }}">
                                <div class="col-md-12 d-flex justify-content-end gap-3">
                                    @if (Helpers::modules_permission_check('Banner Setup', 'Banner Setup', 'edit'))
                                    <button type="reset" class="btn btn-secondary px-4">{{ translate('reset') }}</button>
                                    <button type="submit" class="btn btn--primary px-4">{{ translate('update') }}</button>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/banner.js') }}"></script>

    <script>
        $(document).ready(function () {
            
            let preselectedAppSection = '{{ $banner->app_section ?? '' }}'.trim().toLowerCase();  
            let preselectedImageType = '{{ $banner->image_type ?? '' }}';  
            let preselectedAppSectionResourceType = '{{ $banner->app_section_resource_type ?? '' }}';  
            let preselectedAppSectionResourceID = '{{ $banner->app_section_resource_id ?? '' }}';  

            $('#banner_type_select').on('change', function() {
                let inputValue = $(this).val().toString();
                if (inputValue === 'Mahakal App Banner') {
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

    
                $('#imageType').val(preselectedImageType); 

                if (selectedText === 'shop') {
                    const additionalSelectField = `
                        <br><div class="form-group mb-0" id="additional-select">
                          <select id="additional-select-options" class="form-control w-100" name="app_section_resource_type">
                            <option value="">Select an option</option>
                            <option value="category" ${preselectedAppSectionResourceType === 'category' ? 'selected' : ''}>Category</option>
                            <option value="product" ${preselectedAppSectionResourceType === 'product' ? 'selected' : ''}>Product</option>
                          </select>
                        </div>
                    `;
                    $('#imageTypeContainer').append(additionalSelectField);


                    $('#additional-select-options').trigger('change');
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

                    $('#category-input, #product-input').val(preselectedAppSectionResourceID);
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

            $('#appsection').trigger('change');

            $('#appsection').val(preselectedAppSection).trigger('change'); 
        });
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
    let subServices = @json($subServices);
    let vipPooja    = @json($vipPooja);
    let anushthan   = @json($anushthan);
    let chadhava    = @json($chadhava);
    let tour        = @json($tour);
    let event       = @json($event);
    let darshan       = @json($darshan);
    let offlinePooja       = @json($offlinePooja);

    let preselectedServiceId   = '{{ $banner["resource_id"] ?? "" }}';
    let preselectedSubService  = '{{ $banner["pooja_id"] ?? "" }}';

    function loadSubServices(serviceId) {
        let subServiceContainer = document.getElementById('sub-service-container');
        let subServiceSelect    = document.getElementById('sub_service_id');
        subServiceSelect.innerHTML = '<option value="">{{ translate("Select") }}</option>';

        if (serviceId && subServices[serviceId]) {
            subServices[serviceId].forEach(function (sub) {
                let opt = document.createElement('option');
                opt.value = sub.id;
                opt.textContent = sub.name;
                if (sub.id == preselectedSubService) opt.selected = true;
                subServiceSelect.appendChild(opt);
            });
            subServiceContainer.classList.remove('d-none');
        }
        else if (serviceId == 50) {
            vipPooja.forEach(function (pooja) {
                let opt = document.createElement('option');
                opt.value = pooja.id;
                opt.textContent = pooja.name;
                if (pooja.id == preselectedSubService) opt.selected = true;
                subServiceSelect.appendChild(opt);
            });
            subServiceContainer.classList.remove('d-none');
        }
        else if (serviceId == 51) {
            anushthan.forEach(function (pooja) {
                let opt = document.createElement('option');
                opt.value = pooja.id;
                opt.textContent = pooja.name;
                if (pooja.id == preselectedSubService) opt.selected = true;
                subServiceSelect.appendChild(opt);
            });
            subServiceContainer.classList.remove('d-none');
        }
        else if (serviceId == 52) {
            chadhava.forEach(function (c) {
                let opt = document.createElement('option');
                opt.value = c.id;
                opt.textContent = c.name;
                if (c.id == preselectedSubService) opt.selected = true;
                subServiceSelect.appendChild(opt);
            });
            subServiceContainer.classList.remove('d-none');
        }else if (serviceId == 272) {
            tour.forEach(function (tour) {
                let opt = document.createElement('option');
                opt.value = tour.id;
                opt.textContent = tour.tour_name;
                if (tour.id == preselectedSubService) opt.selected = true;
                subServiceSelect.appendChild(opt);
            });
            subServiceContainer.classList.remove('d-none');
        }
        else if (serviceId == 273) {
            event.forEach(function (event) {
                let opt = document.createElement('option');
                opt.value = event.id;
                opt.textContent = event.event_name;
                if (event.id == preselectedSubService) opt.selected = true;
                subServiceSelect.appendChild(opt);
            });
            subServiceContainer.classList.remove('d-none');
        }
        else if (serviceId == 274) {
            donation.forEach(function (donation) {
                let opt = document.createElement('option');
                opt.value = donation.id;
                opt.textContent = donation.name;
                if (donation.id == preselectedSubService) opt.selected = true;
                subServiceSelect.appendChild(opt);
            });
            subServiceContainer.classList.remove('d-none');
        }
        else if (serviceId == 275) {
            darshan.forEach(function (darshan) {
                let opt = document.createElement('option');
                opt.value = darshan.id;
                opt.textContent = darshan.name;
                if (darshan.id == preselectedSubService) opt.selected = true;
                subServiceSelect.appendChild(opt);
            });
            subServiceContainer.classList.remove('d-none');
        }
        else if (serviceId == 276) {
            offlinePooja.forEach(function (offlinepooja) {
                let opt = document.createElement('option');
                opt.value = offlinepooja.id;
                opt.textContent = offlinepooja.name;
                if (offlinepooja.id == preselectedSubService) opt.selected = true;
                subServiceSelect.appendChild(opt);
            });
            subServiceContainer.classList.remove('d-none');
        }
        else {
            subServiceContainer.classList.add('d-none');
        }
    }

    document.getElementById('mahakalapp_id').addEventListener('change', function () {
        let serviceId = this.value;
        loadSubServices(serviceId);
    });

    document.getElementById('sub_service_id').addEventListener('change', function () {
        document.getElementById('pooja_id').value = this.value;
    });

    // On page load pre-fill
    if (preselectedServiceId) {
        loadSubServices(preselectedServiceId);
    }
</script>

@endpush
