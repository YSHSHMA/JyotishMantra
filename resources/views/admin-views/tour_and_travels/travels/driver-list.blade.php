<div class="col-md-12 mb-3 d-none driver-add-div">
    <div class="card">
        <div class="card-header">
            <div class="col-12">
                <a class="btn btn-primary float-end btn-sm" onclick="$('.driver-add-div').addClass('d-none');$('.driver-list-show-div').removeClass('d-none')">Driver List</a>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.tour_and_travels.driver.driver-store') }}" method="post" enctype="multipart/form-data" id="services_form">
                @csrf
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label class="title-color" for="name">{{ translate('cab_driver_name') }}<span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{old('name')}}" class="form-control" placeholder="{{ translate('enter_cab_driver_name') }}" required>
                        <input type="hidden" name="traveller_id" value="{{ $getData['id'] }}">
                    </div>
                    <div class="col-md-4 form-group">
                        <label class="title-color" for="phone_no">{{ translate('phone_number') }} </label>
                        <input class="form-control form-control-user phone-input-with-country-picker  @error('phone') is-invalid @enderror onfillup" type="tel" id="exampleInputPhone" value="{{ old('phone') }}" placeholder="{{ translate('enter_phone_number') }}" required oninput="validatePhone(this)">
                        <div class="">
                            <input type="hidden" class="country-picker-phone-number w-50" value="{{ old('phone') }}" name="phone" readonly>
                        </div>
                    </div>
                    <div class="col-md-4 form-group">
                        <label class="title-color" for="email">{{ translate('enter_email_Id') }}</label>
                        <input type="text" name="email" value="{{old('email') }}" class="form-control" placeholder="{{ translate('enter_email_Id') }}">
                    </div>
                    <div class="col-md-4 form-group">
                        <label class="title-color" for="gender">{{ translate('gender') }}<span class="text-danger">*</span></label>
                        <select name="gender" class="form-control" required>
                            <option value="">Select Gender</option>
                            <option value="male" {{ ((old('gender') == 'male' )?"selected":"" )}}>Male</option>
                            <option value="female" {{ ((old('gender') == 'female' )?"selected":"" )}}>FeMale</option>
                            <option value="other" {{ ((old('gender') == 'other' )?"selected":"" )}}>Other</option>
                        </select>
                    </div>
                    <div class="col-md-4 form-group">
                        <label class="title-color" for="reg_number">{{ translate('date_of_birth') }}<span class="text-danger">*</span></label>
                        <input type="date" name="dob" value="{{old('dob')}}" class="form-control" placeholder="{{ translate('enter_date_of_birth') }}" required onchange="validateDob(this)">
                        <span id="date_of_brith_error" style="color: red; font-size: 14px;"></span>
                    </div>
                    <div class="col-md-4 form-group">
                        <label class="title-color" for="year_ex">{{ translate('years_of_driving_experience') }}<span class="text-danger">*</span></label>
                        <input type="number" name="year_ex" value="{{old('year_ex') }}" class="form-control" placeholder="{{ translate('enter_years_of_driving_experience') }}">
                    </div>
                    <div class="col-md-4 form-group">
                        <label class="title-color" for="license_number">{{ translate('driving_license_number') }}<span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" class="form-control " name="license_number" id="license_number_id" value="{{ old('license_number') }}" autocomplete="off" placeholder="{{ translate('enter_driving_license_number') }}" onkeyup="$('.license_verify_status_check').val(0);" required>
                            <input type="hidden" name="licenseverify" class="license_verify_status_check" value="{{ old('licenseverify') }}">
                            <button class="btn btn-primary license-verify-check" type="button" onclick="verifylicenseNumber()">Verify</button>
                        </div>
                    </div>
                    <div class="col-md-4 form-group">
                        <label class="title-color" for="pan_number">{{ translate('pan_number') }}<span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" class="form-control " name="pan_number" id="pan_card" value="{{ old('pan_number') }}" autocomplete="off" placeholder="Enter PanCard Number" onkeyup="validatePAN(this);$('.pancard_verify_status_check').val(0);">
                            <input type="hidden" name="panverify" class="pancard_verify_status_check" value="{{ old('panverify') }}">
                            <button class="btn btn-primary pancard-verify-check" type="button" onclick="verifyPanCard()">Verify</button>
                        </div>
                        <small id="pan_error" style="color: red; display: none;">❌Invalid PAN Number(Format: ABCDE1234F)</small>
                    </div>
                    <div class="col-md-4 form-group">
                        <label class="title-color" for="aadhar_number">{{ translate('aadhar_number') }}<span class="text-danger">*</span></label>
                        <div class="input-group aadhar_number_form">
                            <input type="text" class="form-control" name="aadhar_number" autocomplete="off" value="{{ old('aadhar_number') }}" maxlength="12" placeholder="Enter Aadhar Number" onkeyup="validateAadhar(this);$('.aadhar_verify_status_check').val(0);">
                            <input type="hidden" name="aadharveriy" class="aadhar_verify_status_check" value="{{ old('aadharveriy') }}">
                            <button class="btn btn-primary aadhar-send-buttons" type="button" onclick="aadharSendOtp()">Verify</button>
                        </div>
                        <div class="input-group aadhar_otp_form d-none">
                            <input type="text" class="form-control aadhar_otp" pattern="\d{6}" oninput="this.value = this.value.replace(/\D/g, '').slice(0, 6)" placeholder="{{ translate('Enter Aadhaar OTP') }}">
                            <input type="hidden" class="aadhar_request_id">
                            <button type="button" class="btn btn-warning text-white" onclick="aadharverifyOtp()">{{translate('OTP_verify')}}</button>
                        </div>
                    </div>
                    <!--  -->
                    <div class="col-md-3 mb-4">
                        <div class="text-center">
                            <img class="upload-img-view" id="driver_user_image" src="{{ getValidImage(path: 'storage/app/public/tour_and_travels/package/def.png', type: 'backend-product')  }}" alt="">
                        </div>
                        <div class="form-group">
                            <label for="detail_image" class="title-color"> {{ translate('driver_image') }}<span class="text-danger">*</span></label>
                            <span class="ml-1 text-info"> {{ THEME_RATIO[theme_root_path()]['Brand Image'] }} </span>
                            <div class="custom-file text-left">
                                <input type="file" name="image" id="image" class="custom-file-input image-preview-before-upload" data-preview="#driver_user_image" required accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                <label class="custom-file-label" for="detail-image"> {{ translate('choose_file') }} </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="text-center">
                            <img class="upload-img-view" id="driving_license_number1" src="{{ getValidImage(path: 'storage/app/public/tour_and_travels/package/def.png', type: 'backend-product')  }}" alt="">
                        </div>
                        <div class="form-group">
                            <label for="detail_image" class="title-color"> {{ translate('license') }}<span class="text-danger">*</span></label>
                            <span class="ml-1 text-info"> {{ THEME_RATIO[theme_root_path()]['Brand Image'] }} </span>
                            <div class="custom-file text-left">
                                <input type="file" name="license_image" id="image" class="custom-file-input image-preview-before-upload" data-preview="#driving_license_number1" required accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                <label class="custom-file-label" for="detail-image"> {{ translate('choose_file') }} </label>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-4">
                        <div class="text-center">
                            <img class="upload-img-view" id="pan_number1" src="{{ getValidImage(path: 'storage/app/public/tour_and_travels/package/def.png', type: 'backend-product')  }}" alt="">
                        </div>
                        <div class="form-group">
                            <label for="detail_image" class="title-color"> {{ translate('pan_card') }}<span class="text-danger">*</span></label>
                            <span class="ml-1 text-info"> {{ THEME_RATIO[theme_root_path()]['Brand Image'] }} </span>
                            <div class="custom-file text-left">
                                <input type="file" name="pan_image" id="image" class="custom-file-input image-preview-before-upload" data-preview="#pan_number1" required accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                <label class="custom-file-label" for="detail-image"> {{ translate('choose_file') }} </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="text-center">
                            <img class="upload-img-view" id="aadhar_number1" src="{{ getValidImage(path: 'storage/app/public/tour_and_travels/package/def.png', type: 'backend-product')  }}" alt="">
                        </div>
                        <div class="form-group">
                            <label for="detail_image" class="title-color"> {{ translate('aadhar_card') }}<span class="text-danger">*</span></label>
                            <span class="ml-1 text-info"> {{ THEME_RATIO[theme_root_path()]['Brand Image'] }} </span>
                            <div class="custom-file text-left">
                                <input type="file" name="aadhar_image" id="image" class="custom-file-input image-preview-before-upload" data-preview="#aadhar_number1" required accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                <label class="custom-file-label" for="detail-image"> {{ translate('choose_file') }} </label>
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

<div class="col-md-12 driver-list-show-div">
    <div class="card">
        <div class="card-header">
            <div class="col-12">
                <a class="btn btn-primary float-end btn-sm" onclick="$('.driver-add-div').removeClass('d-none');$('.driver-list-show-div').addClass('d-none')">Driver Add</a>
            </div>
        </div>
        <div class="px-3 py-4">
            <!-- Search bar -->
            <div class="row align-items-center">
                <div class="col-sm-4 col-md-6 col-lg-8 mb-2 mb-sm-0">
                    <h5 class="mb-0 d-flex align-items-center gap-2">{{ translate('Package_list') }}
                        <span class="badge badge-soft-dark radius-50 fz-12">{{ $travellerDetails ? $travellerDetails->total() ?? '' : '' }}</span>
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
                            <th>{{ translate('name') }}</th>
                            <th>{{ translate('phone') }}</th>
                            <th>{{ translate('DOB') }}</th>
                            <th>{{ translate('year_experience') }}</th>
                            <th>{{ translate('image') }}</th>
                            <th>{{ translate('status') }}</th>
                            <th>{{ translate('action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Loop through items -->
                        @foreach($travellerDetails as $key => $items)
                        <tr>
                            <td>{{$travellerDetails->firstItem()+$key}}</td>
                            <td>{{ ($items['name']??"") }}</td>
                            <td>{{ $items['phone'] }}</td>
                            <td>{{ date("d M,Y",strtotime($items['dob'])) }}</td>
                            <td>{{ $items['year_ex'] }}year</td>
                            <td>
                                <div class="avatar-60 d-flex align-items-center rounded">
                                    <img class="img-fluid" alt="" src="{{ getValidImage(path: 'storage/app/public/tour_and_travels/tour_traveller_driver/' . $items['image'], type: 'backend-panchang') }}">
                                </div>
                            </td>
                            <td>
                                <!-- Form for toggling status -->
                                <form action="{{route('admin.tour_and_travels.driver.driver_status-update') }}" method="post" id="items-status-driver{{$items['id']}}-form">
                                    @csrf
                                    <input type="hidden" name="id" value="{{$items['id']}}">
                                    <label class="switcher mx-auto">
                                        <input type="checkbox" class="switcher_input toggle-switch-message" name="status"
                                            id="items-status-driver{{ $items['id'] }}" value="1"
                                            {{ $items['status'] == 1 ? 'checked' : '' }}
                                            data-modal-id="toggle-status-modal"
                                            data-toggle-id="items-status-driver{{ $items['id'] }}"
                                            data-on-image="items-status-on.png"
                                            data-off-image="items-status-off.png"
                                            data-on-title="{{ translate('Want_to_Turn_ON').' '.($items['name']??'').' '. translate('status') }}"
                                            data-off-title="{{ translate('Want_to_Turn_OFF').' '.($items['name']??'').' '.translate('status') }}"
                                            data-on-message="<p>{{ translate('if_enabled_this_tour_traveller_driver_will_be_available_on_the_website_and_customer_app') }}</p>"
                                            data-off-message="<p>{{ translate('if_disabled_this_tour_traveller_driver_will_be_hidden_from_the_website_and_customer_app') }}</p>">
                                        <span class="switcher_control"></span>
                                    </label>
                                </form>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <a class="btn btn-outline-info btn-sm square-btn" title="{{ translate('edit') }}" href="{{route('admin.tour_and_travels.driver.driver-update',[$items['id']])}}">
                                        <i class="tio-edit"></i>
                                    </a>
                                    <a class="tour_driver-delete-button btn btn-outline-danger btn-sm square-btn" id="{{ $items['id'] }}">
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
        <!-- Pagination for tour package list -->
        <div class="table-responsive mt-4">
            <div class="d-flex justify-content-lg-end">
                {!! $travellerDetails->links() !!}
            </div>
        </div>
        <!-- Message for no data to show -->
        @if(count($travellerDetails) == 0)
        <div class="text-center p-4">
            <img class="mb-3 w-160"
                src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}"
                alt="{{ translate('image') }}">
            <p class="mb-0">{{ translate('no_data_to_show') }}</p>
        </div>
        @endif
    </div>
</div>


<span id="route-admin-tour_driver-delete" data-url="{{ route('admin.tour_and_travels.driver.traveller-driver-delete') }}"></span>