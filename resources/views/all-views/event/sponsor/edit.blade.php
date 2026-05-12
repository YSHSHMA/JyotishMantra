@extends('layouts.back-end.app-event')

@section('title', translate('Sponsor_update'))
@php
use App\Utils\Helpers;
if (auth('event')->check()) {
$relationEmployees = auth('event')->user()->relation_id;
} elseif (auth('event_employee')->check()) {
$relationEmployees = auth('event_employee')->user()->relation_id;
}
@endphp
@push('css_or_js')
<link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/css/intlTelInput.css') }}">
@endpush
@section('content')
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
            <img src="{{dynamicAsset(path: 'public/assets/back-end/img/coupon_setup.png')}}" alt="">
            {{translate('Sponsor_update')}}
        </h2>
    </div>
    <div class="row">
        <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('event-vendor.sponsor.update-sponsor',['id'=>$getData['id']]) }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 col-lg-4 form-group d-none">
                                <label for="name"
                                    class="title-color font-weight-medium d-flex">{{translate('type')}}</label>
                                <select name="type" class="form-control" value="{{ old('type') }}" required>
                                    <option value="">{{translate('Select_type')}}</option>
                                    <option value="sponsor" {{ ((old('type',$getData['type']) == 'sponsor' ) ? "selected" : '' )}} selected>{{translate('Sponsor')}}</option>
                                    <option value="complimentary" {{ ((old('type',$getData['type']) == 'complimentary' ) ? "selected" : '' )}}>{{translate('complimentary_pass')}}</option>
                                </select>
                            </div>
                            <div class="col-md-6 col-lg-4 form-group">
                                <label for="name" class="title-color font-weight-medium d-flex">{{translate('name')}}</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name',$getData['name']) }}" placeholder="{{translate('name')}}" required>
                            </div>
                            <div class="col-md-6 col-lg-4 form-group">
                                <label for="name" class="title-color font-weight-medium d-flex">{{translate('company_name')}}</label>
                                <input type="text" name="company_name" class="form-control" value="{{ old('company_name',$getData['company_name']) }}" placeholder="{{translate('company_name')}}">
                            </div>

                            <div class="col-md-6 col-lg-4 form-group">
                                <label for="name" class="title-color font-weight-medium d-flex">{{translate('phone')}}</label>
                                <input class="form-control text-align-direction phone-input-with-country-picker" type="tel" id="person-number" value="{{ old('person_phone',$getData['phone']) }}" placeholder="{{ translate('enter_phone_number') }}" required oninput="this.value=this.value.slice(0,10)">
                                <input type="hidden" class="country-picker-phone-number w-50" name="person_phone"  readonly>
                                <p id="number-validation" class="text-danger" style="display: none">
                                    {{ translate('Enter Your Valid Mobile Number') }}
                                </p>
                            </div>
                            <div class="col-md-6 col-lg-4 form-group">
                                <label for="name" class="title-color font-weight-medium d-flex">{{translate('contact_link')}}</label>
                                <input type="text" name="link" value="{{ old('link',$getData['link']) }}" class="form-control" placeholder="{{translate('contact_link')}}">
                            </div>
                            <div class="col-md-6 col-lg-4 form-group">
                                <label for="name" class="title-color font-weight-medium d-flex">{{translate('Pass_list')}}</label>
                                <select name="package_id[]" class="form-control" required multiple>
                                    @php
                                    $oldSelected = old('package_id', $selectedPackages ?? []);
                                    $oldSelected = is_array($oldSelected) ? $oldSelected : [$oldSelected];
                                    @endphp

                                    <option value="">{{translate('Package_Select')}}</option>
                                    @if($packageList)
                                    @foreach($packageList as $va)
                                    <option value="{{$va['id']}}" {{ in_array($va['id'], $oldSelected) ? 'selected' : '' }}>{{$va['package_name']}}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                                                <div>
                                                    <label for="name" class="title-color text-capitalize font-weight-bold mb-0">{{ translate('Sponsor_thumbnail') }}</label>
                                                    <span class="badge badge-soft-info">{{ THEME_RATIO[theme_root_path()]['Product Image'] }}</span>
                                                    <span class="input-label-secondary cursor-pointer" data-toggle="tooltip" title="{{ translate('add_your_service’s_thumbnail_in') }} JPG, PNG or JPEG {{ translate('format_within') }} 2MB">
                                                        <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">
                                                    </span>
                                                </div>
                                            </div>

                                            <div>
                                                <div class="custom_upload_input">
                                                    <input type="file" name="image" class="custom-upload-input-file action-upload-color-image image-preview-before-upload" id="" data-imgpreview="pre_img_viewer" accept=".jpg, .webp, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">

                                                    <span class="delete_file_input btn btn-outline-danger btn-sm square-btn d--none">
                                                        <i class="tio-delete"></i>
                                                    </span>

                                                    <div class="img_area_with_preview position-absolute z-index-2">
                                                        <img id="pre_img_viewer" class="h-auto aspect-1 bg-white" src="{{ getValidImage(path: 'storage\app\public\event\sponsor/' . ($getData['image']??''), type: 'backend-product')  }}" alt="">
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


                        <div class="d-flex align-items-center justify-content-end flex-wrap gap-10">
                            <button type="reset"
                                class="btn btn-secondary px-4">{{translate('reset')}}</button>
                            <button type="submit" class="btn btn--primary px-4">{{translate('submit')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('script')
<script src="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/js/intlTelInput.js') }}"></script>
<script src="{{ theme_asset(path: 'public/assets/front-end/js/country-picker-init.js') }}"></script>
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/admin/product-add-update.js') }}"></script>
@endpush