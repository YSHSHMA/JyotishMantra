<div class="col-md-12 mb-3 d-none cab-add-div">
    <div class="card">
        <div class="card-header">
            <div class="col-12">
                <a class="btn btn-primary float-end btn-sm" onclick="$('.cab-add-div').addClass('d-none');$('.cab-list-show-div').removeClass('d-none')">Cab List</a>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.tour_and_travels.cab.cab-store') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="row">

                    <div class="col-md-6 form-group">
                        <label class="title-color" for="name">{{ translate('select_cab') }}<span class="text-danger">*</span></label>
                        <select name="cab_id" class="form-control">
                            <option value="">{{ translate('select_cab') }}</option>
                            @if($carlists)
                            @foreach($carlists as $va)
                            <option value="{{ $va['id']}}" {{ ((old('cab_id') == $va['id'] )?"selected" :"" ) }}>{{ $va['name'] }}</option>
                            @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="title-color" for="reg_number">{{ translate('reg_number') }}</label>
                        <input type="text" name="reg_number" value="{{old('reg_number')}}" class="form-control" placeholder="{{ translate('enter_register_number') }}" required>
                        <input type="hidden" name="traveller_id" value="{{ $getData['id'] }}">
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="title-color" for="model_number">{{ translate('model_number') }}</label>
                        <input type="text" name="model_number" value="{{old('model_number') }}" class="form-control" placeholder="{{ translate('enter_model_number') }}" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="title-color" for="fuel_type">{{ translate('fuel_type') }}</label>
                        <select name="fuel_type" class="form-control" required>
                            <option value="">Select Fuel Type</option>
                            <option value="petrol" {{ (('petrol' == old('fuel_type') )?'selected':'')}}>Petrol</option>
                            <option value="diesel" {{ (('diesel' == old('fuel_type') )?'selected':'')}}>Diesel</option>
                            <option value="cng" {{ (('cng' == old('fuel_type') )?'selected':'')}}>CNG</option>
                            <option value="electric" {{ (('electric' == old('fuel_type') )?'selected':'')}}>Electric</option>
                            <option value="hybrid" {{ (('hybrid' == old('fuel_type') )?'selected':'')}}>Hybrid</option>
                        </select>
                    </div>
                    <div class="additional_image_column col-md-12 mt-2">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                                    <div>
                                        <label for="name"
                                            class="title-color text-capitalize font-weight-bold mb-0">{{ translate('upload_additional_image') }}</label>
                                        <span
                                            class="badge badge-soft-info">{{ THEME_RATIO[theme_root_path()]['Product Image'] }}</span>
                                        <span class="input-label-secondary cursor-pointer" data-toggle="tooltip"
                                            title="{{ translate('upload_any_additional_images_for_this_vehicle_from_here') }}.">
                                            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}"
                                                alt="">
                                        </span>
                                    </div>
                                </div>
                                <p class="text-muted">{{ translate('upload_additional_vehicle_images') }}</p>
                                <div class="row g-2" id="additional_Image_Section">
                                    <div class="col-sm-12 col-md-4">
                                        <div class="custom_upload_input position-relative border-dashed-2">
                                            <input type="file" name="image[]" class="custom-upload-input-file action-add-more-image" data-index="1" data-imgpreview="additional_Image_1" accept=".jpg, .png, .webp, .jpeg, .gif, .bmp, .tif, .tiff|image/*" data-target-section="#additional_Image_Section">

                                            <span class="delete_file_input delete_file_input_section btn btn-outline-danger btn-sm square-btn d-none">
                                                <i class="tio-delete"></i>
                                            </span>

                                            <div class="img_area_with_preview position-absolute z-index-2 border-0">
                                                <img id="additional_Image_1" class="h-auto aspect-1 bg-white d-none" src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg-dummy') }}" alt="">
                                            </div>
                                            <div class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                                <div class="d-flex flex-column justify-content-center align-items-center">
                                                    <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}" class="w-75">
                                                    <h3 class="text-muted">{{ translate('Upload_Image') }}</h3>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Buttons for form actions -->
                <div class="d-flex flex-wrap gap-2 justify-content-end mt-2">
                    <button type="reset" class="btn btn-secondary">{{ translate('reset') }}</button>
                    <button type="submit" class="btn btn--primary">{{ translate('submit') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="col-md-12 cab-list-show-div">
    <div class="card">
        <div class="card-header">
            <div class="col-12">
                <a class="btn btn-primary float-end btn-sm" onclick="$('.cab-add-div').removeClass('d-none');$('.cab-list-show-div').addClass('d-none')">Add Cab</a>
            </div>
        </div>
        <div class="px-3 py-4">
            <!-- Search bar -->
            <div class="row align-items-center">
                <div class="col-sm-4 col-md-6 col-lg-8 mb-2 mb-sm-0">
                    <h5 class="mb-0 d-flex align-items-center gap-2">{{ translate('Cab_list') }}
                        <span class="badge badge-soft-dark radius-50 fz-12">{{ $cabDetails->total() ?? '' }}</span>
                    </h5>
                </div>
                <div class="col-sm-8 col-md-6 col-lg-4">
                    <form action="{{ url()->current() }}" method="GET">
                        <div class="input-group input-group-custom input-group-merge">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="tio-search"></i>
                                </div>
                            </div>
                            <input id="datatableSearch_" type="search" name="searchValue" class="form-control"
                                placeholder="{{ translate('search_by_name') }}"
                                aria-label="{{ translate('search_by_name') }}" required>
                            <button type="submit" class="btn btn--primary">{{ translate('search') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Table displaying tour package -->
        <div class="text-start">
            <div class="table-responsive">
                <table id="datatable"
                    class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                    <thead class="thead-light thead-50 text-capitalize">
                        <tr>
                            <th>{{ translate('SL') }}</th>
                            <th>{{ translate('cab_name') }}</th>
                            <th>{{ translate('reg_number') }}</th>
                            <th>{{ translate('model_name') }}</th>
                            <th>{{ translate('fuel_type') }}</th>
                            <th>{{ translate('status') }}</th>
                            <th>{{ translate('action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Loop through items -->
                        @foreach($cabDetails as $key => $items)
                        <tr>
                            <td>{{$cabDetails->firstItem()+$key}}</td>
                            <td>{{ ($items['Cabs']['name']??"") }}</td>
                            <td>{{ $items['reg_number'] }}</td>
                            <td>{{ $items['model_number'] }}</td>
                            <td>
                                {{ $items['fuel_type'] }}
                            </td>
                            <td>
                                <!-- Form for toggling status -->
                                <form action="{{route('admin.tour_and_travels.cab.cab_status-update') }}" method="post" id="items-status{{$items['id']}}-form">
                                    @csrf
                                    <input type="hidden" name="id" value="{{$items['id']}}">
                                    <label class="switcher mx-auto">
                                        <input type="checkbox" class="switcher_input toggle-switch-message" name="status"
                                            id="items-status{{ $items['id'] }}" value="1"
                                            {{ $items['status'] == 1 ? 'checked' : '' }}
                                            data-modal-id="toggle-status-modal"
                                            data-toggle-id="items-status{{ $items['id'] }}"
                                            data-on-image="items-status-on.png"
                                            data-off-image="items-status-off.png"
                                            data-on-title="{{ translate('Want_to_Turn_ON').' '.($items['Cabs']['name']??'').' '. translate('status') }}"
                                            data-off-title="{{ translate('Want_to_Turn_OFF').' '.($items['Cabs']['name']??'').' '.translate('status') }}"
                                            data-on-message="<p>{{ translate('if_enabled_this_tour_traveller_cab_will_be_available_on_the_website_and_customer_app') }}</p>"
                                            data-off-message="<p>{{ translate('if_disabled_this_tour_traveller_cab_will_be_hidden_from_the_website_and_customer_app') }}</p>">
                                        <span class="switcher_control"></span>
                                    </label>
                                </form>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <a class="btn btn-outline-info btn-sm square-btn" title="{{ translate('edit') }}" href="{{route('admin.tour_and_travels.cab.cab-update',[$items['id']])}}">
                                        <i class="tio-edit"></i>
                                    </a>
                                    <a class="tour_package-delete-button btn btn-outline-danger btn-sm square-btn" id="{{ $items['id'] }}">
                                        <i class="tio-delete"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="table-responsive mt-4">
            <div class="d-flex justify-content-lg-end">
                {!! $cabDetails->links() !!}
            </div>
        </div>
        <!-- Message for no data to show -->
        @if(count($cabDetails) == 0)
        <div class="text-center p-4">
            <img class="mb-3 w-160"
                src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}"
                alt="{{ translate('image') }}">
            <p class="mb-0">{{ translate('no_data_to_show') }}</p>
        </div>
        @endif
    </div>
</div>
<span id="route-admin-tour_package-delete" data-url="{{ route('admin.tour_and_travels.cab.traveller-cab-delete') }}"></span>