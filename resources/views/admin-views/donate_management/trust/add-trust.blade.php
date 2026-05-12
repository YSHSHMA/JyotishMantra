@extends('layouts.back-end.app')

@section('title', translate('add_Trust'))
@push('css_or_js')
<link href="{{ dynamicAsset(path: 'public/assets/select2/css/select2.min.css') }}" rel="stylesheet">
@endpush
@section('content')
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 d-flex gap-2">
            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/') }}" alt="">
            {{ translate('add_Trust') }}
        </h2>
    </div>
    <div class="row">
        <!-- Form for adding new add_Trust -->
        <div class="col-md-12 mb-3">

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.donate_management.trust.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <ul class="nav nav-tabs w-fit-content mb-4">
                            @foreach($languages as $lang)
                            <li class="nav-item text-capitalize">
                                <a class="nav-link form-system-language-tab cursor-pointer {{$lang == $defaultLanguage? 'active':''}}" id="{{$lang}}-link">
                                    {{ getLanguageName($lang).'('.strtoupper($lang).')' }}
                                </a>
                            </li>
                            @endforeach
                        </ul>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6 mt-2">
                                            <label class="title-color" for="name">{{ translate('Temple_select') }} <span class="text-danger">(optional)</label>
                                            <select type="text" name="temple[]" class="form-control temple_select select2" required multiple>
                                                @if($temple_list)
                                                @foreach($temple_list as $va)
                                                <option value="{{ $va['id'] }}">{{ $va['name'] }}</option>
                                                @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                @foreach($languages as $lang)
                                <div class="form-group {{$lang != $defaultLanguage ? 'd-none':''}} form-system-language-form" id="{{$lang}}-form">
                                    <div class="row">
                                        <div class="col-md-6 mt-2">
                                            <label class="title-color" for="category_name">{{ translate('Select_category') }}<span class="text-danger">*</span></label>
                                            <select name="category_id" class="form-control fillupdata " data-point='1' onchange="$(`.fillupdata[data-point='1']`).val(this.value)" {{$lang == $defaultLanguage? 'required':''}}>
                                                <option value="">Select Category</option>
                                                @if($all_categorys)
                                                @foreach($all_categorys as $vals)
                                                <option value="{{ $vals['id']}}" {{ ((old('category_id') == $vals['id'])?"selected":"")}}>{{ ($vals['name']??"")}}</option>
                                                @endforeach
                                                @endif
                                            </select>
                                            <input type="hidden" name="lang[]" value="{{$lang}}" id="lang">
                                        </div>
                                        <div class="col-md-6 mt-2">
                                            <label class="title-color" for="name">{{ translate('Name') }} <span class="text-danger">*</span> ({{ strtoupper($lang) }})</label>
                                            <input type="text" name="name[]" class="form-control" value="{{ old('name.'.$loop->index)}}" id="{{$lang}}_name" placeholder="{{ translate('Enter_name') }}" {{$lang == $defaultLanguage? 'required':''}}>
                                        </div>
                                        <div class="col-md-6 mt-2">
                                            <label class="title-color" for="trust_name">{{ translate('Trust_Name') }} <span class="text-danger">*</span> ({{ strtoupper($lang) }})</label>
                                            <input type="text" name="trust_name[]" class="form-control" value="{{ old('trust_name.'.$loop->index)}}" id="{{$lang}}_trust_name" placeholder="{{ translate('Enter_trust_name') }}" {{$lang == $defaultLanguage? 'required':''}}>
                                        </div>
                                        <div class="col-md-6 mt-2">
                                            <label class="title-color" for="Trust_pan_card">{{ translate('Trust_pan_card') }}</label>
                                            <input type="text" name="trust_pan_card" class="form-control fillupdata" data-point='2' onblur="$(`.fillupdata[data-point='2']`).val(this.value)" value="{{ old('trust_pan_card')}}" id="{{$lang}}_trust_pan_card" placeholder="{{ translate('Enter_trust_pan_card') }}" maxlength="10" onkeyup="formatPAN(this)" {{$lang == $defaultLanguage? 'required':''}}>
                                        </div>
                                        <div class="col-md-4 mt-2">
                                            <label class="title-color" for="pan_card">{{ translate('pan_card') }}</label>
                                            <input type="text" name="pan_card" class="form-control fillupdata" data-point='3' onblur="$(`.fillupdata[data-point='3']`).val(this.value)" value="{{ old('pan_card')}}" id="{{$lang}}_pan_card" placeholder="{{ translate('Enter_pan_card') }}" maxlength="10" onkeyup="formatPAN(this)" {{$lang == $defaultLanguage? 'required':''}}>
                                        </div>
                                        <div class="col-md-4 mt-2">
                                            <label class="title-color" for="full_address">{{ translate('Full_address') }}<span class="text-danger">*</span> ({{ strtoupper($lang) }})</label>
                                            <input type="text" name="full_address[]" class="form-control" value="{{ old('full_address.'.$loop->index)}}" id="{{$lang}}_full_address" placeholder="{{ translate('Enter_full_address') }}" {{$lang == $defaultLanguage? 'required':''}}>
                                        </div>
                                        <div class="col-md-4 mt-2">
                                            <label class="title-color" for="trust_email">{{ translate('trust_email') }}<span class="text-danger">*</span></label>
                                            <input type="email" name="trust_email" class="form-control fillupdata" data-point='4' onblur="$(`.fillupdata[data-point='4']`).val(this.value)" value="{{ old('trust_email')}}" id="{{$lang}}_trust_email" placeholder="{{ translate('Enter_trust_email') }}" {{$lang == $defaultLanguage? 'required':''}}>
                                        </div>
                                        <div class="col-md-12">
                                            <label class="title-color w-100 font-weight-bold h3" for="details">{{ translate('Description') }}<span class="text-danger">*</span> ({{ strtoupper($lang) }})</label>
                                            <textarea class='form-control ckeditor' name='description[]'>{{ old('description.'.$loop->index)}}</textarea>
                                        </div>
                                    </div>
                                </div>
                                @endforeach

                                <div class="row mt-3 form-group">
                                    <div class="col-md-12">
                                        <label class="title-color w-100 font-weight-bold h3" for="details">{{ translate('Trust_members') }}</label>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row mb-3">
                                            <div class="col-3"><b>Name</b></div>
                                            <div class="col-3"><b>Phone</b></div>
                                            <div class="col-3"><b>Position</b></div>
                                            <div class="col-3">
                                                <a id="addMember" class="btn btn-outline-primary btn-sm"><i class='tio-add'></i>Add</a>
                                            </div>
                                        </div>

                                        <div id="memberContainer">
                                            @if(old('member_name'))
                                            @foreach(old('member_name') as $index => $memberName)
                                            <div class="row mt-2">
                                                <div class="col-3">
                                                    <input type='text' name='member_name[]' class='form-control' value="{{ old('member_name.'.$index) }}" onkeyup="allowOnlyLetters(this)" required>
                                                </div>
                                                <div class="col-3">
                                                    <input type='text' name='member_phone_no[]' class='form-control' value="{{ old('member_phone_no.'.$index) }}" maxlength="13" onkeyup="formatIndianPhone(this)" required>
                                                </div>
                                                <div class="col-3">
                                                    <input type='text' name='member_position[]' class='form-control' value="{{ old('member_position.'.$index) }}" onkeyup="allowOnlyLetters(this)" required>
                                                </div>
                                                <div class="col-3">
                                                    <a class="btn btn-outline-danger btn-sm removeMember"><i class='tio-remove'></i></a>
                                                </div>
                                            </div>
                                            @endforeach
                                            @else
                                            <div class="row">
                                                <div class="col-3"><input type='text' name='member_name[]' class='form-control' onkeyup="allowOnlyLetters(this)" required></div>
                                                <div class="col-3"><input type='text' name='member_phone_no[]' class='form-control' maxlength="13" onkeyup="formatIndianPhone(this)" required></div>
                                                <div class="col-3"><input type='text' name='member_position[]' class='form-control' onkeyup="allowOnlyLetters(this)" required></div>
                                                <div class="col-3"></div>
                                            </div>
                                            @endif
                                        </div>
                                    </div>


                                </div>
                                <div class="row mt-3 form-group">
                                    <div class="col-md-12">
                                        <label class="title-color w-100 font-weight-bold h3" for="details">{{ translate('Bank_details') }}</label>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="title-color w-100" for="Beneficiary_Name">{{ translate('Beneficiary_Name') }}</label>
                                        <input type='text' class="form-control @error('beneficiary_name') is-invalid @enderror" name='beneficiary_name' value="{{ old('beneficiary_name') }}" placeholder="{{ translate('Beneficiary_Name') }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="title-color w-100" for="account_type">{{ translate('Account_type') }}</label>
                                        <select class='form-control' name='account_type' placeholder="{{ translate('Account_type') }}">
                                            <option value="saving account" {{ old('category_id') == "saving account" ? 'selected' : '' }}>saving account</option>
                                            <option value="current account" {{ old('category_id') == "current account" ? 'selected' : '' }}>current account</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="title-color w-100" for="address">{{ translate('Bank_name') }}</label>
                                        <select class='form-control' name='bank_name' placeholder="{{ translate('Bank_name') }}">
                                            @if($bankList)
                                            @foreach($bankList as $bank)
                                            <option value="{{ $bank['bank_name'] }}" {{ ((old('bank_name') == ($bank['bank_name']??"") )?"selected":"")}}>{{ $bank['bank_name'] }}</option>
                                            @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="title-color w-100" for="ifsc_code">{{ translate('IFSC_code') }}</label>
                                        <input type='text' class='form-control' name='ifsc_code' value="{{ old('ifsc_code') }}" placeholder="{{ translate('IFSC_code') }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="title-color w-100" for="account_no">{{ translate('Account_Number') }}</label>
                                        <input type='text' class='form-control' name='account_no' value="{{ old('account_no') }}" placeholder="{{ translate('Account_Number') }}" onkeyup="this.value = this.value.replace(/[^0-9]/g, '')">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="title-color w-100" for="c_account_no">{{ translate('Confirm_Account_number') }}</label>
                                        <input type='text' class='form-control confirm_account_no' name='c_account_no' value="{{ old('c_account_no') }}" placeholder="{{ translate('Account_Number') }}" onkeyup="this.value = this.value.replace(/[^0-9]/g, '')">
                                        <span class="font-weight-bold confirm_account_no-error text-danger"></span>
                                    </div>
                                </div>
                                <div class="row mt-3 form-group">
                                    <div class="col-md-12">
                                        <label class="title-color w-100" for="website_link">{{ translate('website_link') }}</label>
                                        <input type='text' class='form-control' name='website' value="{{ old('website') }}" placeholder="{{ translate('website_link') }}">
                                    </div>
                                    <div class="col-md-12 mt-4">
                                        <label class="title-color w-100 font-weight-bold h3" for="upload_Images">{{ translate('upload_Images') }}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 rest-part">
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                                                        <div>
                                                            <label for="name" class="title-color text-capitalize font-weight-bold mb-0">{{ translate('pan_card_image') }}</label>
                                                            <span class="badge badge-soft-info">{{ THEME_RATIO[theme_root_path()]['Product Image'] }}</span>
                                                            <span class="input-label-secondary cursor-pointer" data-toggle="tooltip" title="{{ translate('add_pan_card_image') }} JPG, PNG, JPEG, WEBP, PDF or DOC  {{ translate('format_within') }} 5MB">
                                                                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="custom_upload_input">
                                                            <input type="file" name="pan_card_image" class="custom-upload-input-file action-upload-color-image image-input-pdf" id="" data-imgpreview="pan_card_images" accept=".jpg, .jpeg, .png, .webp, .pdf, .doc, .docx">
                                                            <span class="delete_file_input btn btn-outline-danger btn-sm square-btn d--none">
                                                                <i class="tio-delete"></i>
                                                            </span>
                                                            <div class="img_area_with_preview position-absolute z-index-2">
                                                                <img id="pan_card_images" class="h-auto aspect-1 bg-white d-none" src="dummy" alt="">
                                                            </div>
                                                            <div class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                                                <div class="d-flex flex-column justify-content-center align-items-center">
                                                                    <img alt="" class="w-75" src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}">
                                                                    <h3 class="text-muted">{{ translate('Upload_Image') }}</h3>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <p class="text-muted mt-2">
                                                            {{ translate('image_format') }} : {{ 'Jpg, png, jpeg, webp, pdf or doc' }}
                                                            <br>
                                                            {{ translate('image_size') }} : {{ translate('max') }} {{ '5 MB' }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                                                        <div>
                                                            <label for="name" class="title-color text-capitalize font-weight-bold mb-0">{{ translate('Trustees_pan_card_image') }}</label>
                                                            <span class="badge badge-soft-info">{{ THEME_RATIO[theme_root_path()]['Product Image'] }}</span>
                                                            <span class="input-label-secondary cursor-pointer" data-toggle="tooltip" title="{{ translate('add_Trustees_pan_card_image') }} JPG, PNG,  JPEG, WEBP, PDF or DOC {{ translate('format_within') }} 5MB">
                                                                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="custom_upload_input">
                                                            <input type="file" name="trustees_pan_card_image" class="custom-upload-input-file action-upload-color-image image-input-pdf" data-imgpreview="trustees_pan_card_images" accept=".jpg, .jpeg, .png, .webp, .pdf, .doc, .docx">
                                                            <span class="delete_file_input btn btn-outline-danger btn-sm square-btn d--none">
                                                                <i class="tio-delete"></i>
                                                            </span>
                                                            <div class="img_area_with_preview position-absolute z-index-2">
                                                                <img id="trustees_pan_card_images" class="h-auto aspect-1 bg-white d-none" src="dummy" alt="">
                                                            </div>
                                                            <div class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                                                <div class="d-flex flex-column justify-content-center align-items-center">
                                                                    <img alt="" class="w-75" src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}">
                                                                    <h3 class="text-muted">{{ translate('Upload_Image') }}</h3>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <p class="text-muted mt-2">
                                                            {{ translate('image_format') }} : {{ 'Jpg, png, jpeg, webp, pdf or doc' }}
                                                            <br>
                                                            {{ translate('image_size') }} : {{ translate('max') }} {{ '5 MB' }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- 1 section -->
                                    <div class="col-md-4">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                                                        <div>
                                                            <label for="name" class="title-color text-capitalize font-weight-bold mb-0">{{ translate('12A_certificate') }} <span class="text-danger">*</span></label>
                                                            <span class="badge badge-soft-info">{{ THEME_RATIO[theme_root_path()]['Product Image'] }}</span>
                                                            <span class="input-label-secondary cursor-pointer" data-toggle="tooltip" title="{{ translate('add_12A_certificate') }} JPG, PNG, JPEG, WEBP, PDF or DOC {{ translate('format_within') }} 5MB">
                                                                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <input type="text" class="form-control my-2" name="twelve_a_number" placeholder="{{ translate('12A_certificate') }}">
                                                    <div>
                                                        <div class="custom_upload_input">
                                                            <input type="file" name="twelve_a_certificate" class="custom-upload-input-file action-upload-color-image image-input-pdf" id="" data-imgpreview="12A_certificate" accept=".jpg, .jpeg, .png, .webp, .pdf, .doc, .docx" required>
                                                            <span class="delete_file_input btn btn-outline-danger btn-sm square-btn d--none">
                                                                <i class="tio-delete"></i>
                                                            </span>
                                                            <div class="img_area_with_preview position-absolute z-index-2">
                                                                <img id="12A_certificate" class="h-auto aspect-1 bg-white d-none" src="dummy" alt="">
                                                            </div>
                                                            <div class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                                                <div class="d-flex flex-column justify-content-center align-items-center">
                                                                    <img alt="" class="w-75" src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}">
                                                                    <h3 class="text-muted">{{ translate('Upload_Image') }}</h3>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <p class="text-muted mt-2">
                                                            {{ translate('image_format') }} : {{ 'Jpg, png, jpeg, webp, pdf or doc' }}
                                                            <br>
                                                            {{ translate('image_size') }} : {{ translate('max') }} {{ '5 MB' }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- 2 second -->
                                    <div class="col-md-4">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                                                        <div>
                                                            <label for="name" class="title-color text-capitalize font-weight-bold mb-0">{{ translate('eighty_G_certificate') }} <span class="text-danger">*</span></label>
                                                            <span class="badge badge-soft-info">{{ THEME_RATIO[theme_root_path()]['Product Image'] }}</span>
                                                            <span class="input-label-secondary cursor-pointer" data-toggle="tooltip" title="{{ translate('add_eighty_G_certificate_in') }} JPG, PNG, JPEG, WEBP, PDF or DOC {{ translate('format_within') }} 5MB">
                                                                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <input type="text" class="form-control my-2" name="eighty_g_number" placeholder="{{ translate('eighty_G_certificate') }}">
                                                    <div>
                                                        <div class="custom_upload_input">
                                                            <input type="file" name="eighty_g_certificate" class="custom-upload-input-file action-upload-color-image image-input-pdf" id="" data-imgpreview="pre_eighty_g_certificate" accept=".jpg, .jpeg, .png, .webp, .pdf, .doc, .docx" required>
                                                            <span class="delete_file_input btn btn-outline-danger btn-sm square-btn d--none">
                                                                <i class="tio-delete"></i>
                                                            </span>

                                                            <div class="img_area_with_preview position-absolute z-index-2">
                                                                <img id="pre_eighty_g_certificate" class="h-auto aspect-1 bg-white d-none" src="dummy" alt="">
                                                            </div>
                                                            <div class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                                                <div class="d-flex flex-column justify-content-center align-items-center">
                                                                    <img alt="" class="w-75" src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}">
                                                                    <h3 class="text-muted">{{ translate('Upload_Image') }}</h3>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <p class="text-muted mt-2">
                                                            {{ translate('image_format') }} : {{ 'Jpg, png, jpeg, webp, pdf or doc' }}
                                                            <br>
                                                            {{ translate('image_size') }} : {{ translate('max') }} {{ '5 MB' }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--  3 three -->
                                    <div class="col-md-4">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                                                        <div>
                                                            <label for="name" class="title-color text-capitalize font-weight-bold mb-0">{{ translate('Niti_aayog_certificate ') }} <span class="text-danger">*</span></label>
                                                            <span class="badge badge-soft-info">{{ THEME_RATIO[theme_root_path()]['Product Image'] }}</span>
                                                            <span class="input-label-secondary cursor-pointer" data-toggle="tooltip" title="{{ translate('add_your_aadhaar_card') }} JPG, PNG, JPEG, WEBP, PDF or DOC {{ translate('format_within') }} 5MB">
                                                                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <input type="text" class="form-control my-2" name="niti_aayog_number" placeholder="{{ translate('Niti_aayog_certificate') }}">
                                                    <div>
                                                        <div class="custom_upload_input">
                                                            <input type="file" name="niti_aayog_certificate" class="custom-upload-input-file action-upload-color-image image-input-pdf" id="" data-imgpreview="pre_niti_aayog_certificate" accept=".jpg, .jpeg, .png, .webp, .pdf, .doc, .docx" required>
                                                            <span class="delete_file_input btn btn-outline-danger btn-sm square-btn d--none">
                                                                <i class="tio-delete"></i>
                                                            </span>

                                                            <div class="img_area_with_preview position-absolute z-index-2">
                                                                <img id="pre_niti_aayog_certificate" class="h-auto aspect-1 bg-white d-none" src="dummy" alt="">
                                                            </div>
                                                            <div class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                                                <div class="d-flex flex-column justify-content-center align-items-center">
                                                                    <img alt="" class="w-75" src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}">
                                                                    <h3 class="text-muted">{{ translate('Upload_Image') }}</h3>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <p class="text-muted mt-2">
                                                            {{ translate('image_format') }} : {{ 'Jpg, png, jpeg, webp, pdf or doc' }}
                                                            <br>
                                                            {{ translate('image_size') }} : {{ translate('max') }} {{ '5 MB' }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- 4 forth -->
                                    <div class="col-md-4">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                                                        <div>
                                                            <label for="name" class="title-color text-capitalize font-weight-bold mb-0">{{ translate('CSR_certificate ') }}</label>
                                                            <span class="badge badge-soft-info">{{ THEME_RATIO[theme_root_path()]['Product Image'] }}</span>
                                                            <span class="input-label-secondary cursor-pointer" data-toggle="tooltip" title="{{ translate('add_CSR_certificate_in') }} JPG, PNG, JPEG, WEBP, PDF or DOC {{ translate('format_within') }} 5MB">
                                                                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <input type="text" class="form-control my-2" name="csr_number" placeholder="{{ translate('CSR_certificate') }}">
                                                    <div>
                                                        <div class="custom_upload_input">
                                                            <input type="file" name="csr_certificate" class="custom-upload-input-file action-upload-color-image image-input-pdf" id="" data-imgpreview="pre_csr_certificate" accept=".jpg, .jpeg, .png, .webp, .pdf, .doc, .docx">
                                                            <span class="delete_file_input btn btn-outline-danger btn-sm square-btn d--none">
                                                                <i class="tio-delete"></i>
                                                            </span>
                                                            <div class="img_area_with_preview position-absolute z-index-2">
                                                                <img id="pre_csr_certificate" class="h-auto aspect-1 bg-white d-none" src="dummy" alt="">
                                                            </div>
                                                            <div class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                                                <div class="d-flex flex-column justify-content-center align-items-center">
                                                                    <img alt="" class="w-75" src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}">
                                                                    <h3 class="text-muted">{{ translate('Upload_Image') }}</h3>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <p class="text-muted mt-2">
                                                            {{ translate('image_format') }} : {{ 'Jpg, png, jpeg, webp, pdf or doc' }}
                                                            <br>
                                                            {{ translate('image_size') }} : {{ translate('max') }} {{ '5 MB' }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- 5 five -->
                                    <div class="col-md-4">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                                                        <div>
                                                            <label for="name" class="title-color text-capitalize font-weight-bold mb-0">{{ translate('E_anudhan_certificate ') }} <span class="text-danger">*</span></label>
                                                            <span class="badge badge-soft-info">{{ THEME_RATIO[theme_root_path()]['Product Image'] }}</span>
                                                            <span class="input-label-secondary cursor-pointer" data-toggle="tooltip" title="{{ translate('add_E_anudhan_certificate_in') }} JPG, PNG, JPEG, WEBP, PDF or DOC, {{ translate('format_within') }} 5MB">
                                                                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <input type="text" class="form-control my-2" name="e_anudhan_number" placeholder="{{ translate('E_anudhan_certificate') }}">
                                                    <div>
                                                        <div class="custom_upload_input">
                                                            <input type="file" name="e_anudhan_certificate" class="custom-upload-input-file action-upload-color-image image-input-pdf" id="" data-imgpreview="pre_e_anudhan_certificate" accept=".jpg, .jpeg, .png, .webp, .pdf, .doc, .docx" required>
                                                            <span class="delete_file_input btn btn-outline-danger btn-sm square-btn d--none">
                                                                <i class="tio-delete"></i>
                                                            </span>
                                                            <div class="img_area_with_preview position-absolute z-index-2">
                                                                <img id="pre_e_anudhan_certificate" class="h-auto aspect-1 bg-white d-none" src="dummy" alt="">
                                                            </div>
                                                            <div class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                                                <div class="d-flex flex-column justify-content-center align-items-center">
                                                                    <img alt="" class="w-75" src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}">
                                                                    <h3 class="text-muted">{{ translate('Upload_Image') }}</h3>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <p class="text-muted mt-2">
                                                            {{ translate('image_format') }} : {{ 'Jpg, png, jpeg, webp, pdf or doc' }}
                                                            <br>
                                                            {{ translate('image_size') }} : {{ translate('max') }} {{ '5 MB' }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- 6 six -->
                                    <div class="col-md-4">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                                                        <div>
                                                            <label for="name" class="title-color text-capitalize font-weight-bold mb-0">{{ translate('FRC_certificate') }}</label>
                                                            <span class="badge badge-soft-info">{{ THEME_RATIO[theme_root_path()]['Product Image'] }}</span>
                                                            <span class="input-label-secondary cursor-pointer" data-toggle="tooltip" title="{{ translate('add_FRC_certificate_in') }} JPG, PNG, JPEG, WEBP, PDF or DOC {{ translate('format_within') }} 5MB">
                                                                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <input type="text" class="form-control my-2" name="frc_number" placeholder="{{ translate('FRC_certificate') }}">
                                                    <div>
                                                        <div class="custom_upload_input">
                                                            <input type="file" name="frc_certificate" class="custom-upload-input-file action-upload-color-image image-input-pdf" id="" data-imgpreview="pre_frc_certificate" accept=".jpg, .jpeg, .png, .webp, .pdf, .doc, .docx">
                                                            <span class="delete_file_input btn btn-outline-danger btn-sm square-btn d--none">
                                                                <i class="tio-delete"></i>
                                                            </span>
                                                            <div class="img_area_with_preview position-absolute z-index-2">
                                                                <img id="pre_frc_certificate" class="h-auto aspect-1 bg-white d-none" src="dummy" alt="">
                                                            </div>
                                                            <div class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                                                <div class="d-flex flex-column justify-content-center align-items-center">
                                                                    <img alt="" class="w-75" src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}">
                                                                    <h3 class="text-muted">{{ translate('Upload_Image') }}</h3>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <p class="text-muted mt-2">
                                                            {{ translate('image_format') }} : {{ 'Jpg, png, jpeg, webp, pdf or doc' }}
                                                            <br>
                                                            {{ translate('image_size') }} : {{ translate('max') }} {{ '5 MB' }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mt-2 p-0">

                                <!-- gallery -->
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                                                        <div>
                                                            <label for="name" class="title-color text-capitalize font-weight-bold mb-0">{{ translate('theme_image') }}</label>
                                                            <span class="badge badge-soft-info">{{ THEME_RATIO[theme_root_path()]['Product Image'] }}</span>
                                                            <span class="input-label-secondary cursor-pointer" data-toggle="tooltip" title="{{ translate('theme_image') }} JPG, PNG, JPEG or WEBP {{ translate('format_within') }} 2MB">
                                                                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="custom_upload_input">
                                                            <input type="file" name="theme_image" class="custom-upload-input-file action-upload-color-image image-input" data-imgpreview="pre_theme_image" accept=".jpg, .jpeg, .png, .webp" required>
                                                            <span class="delete_file_input btn btn-outline-danger btn-sm square-btn d--none">
                                                                <i class="tio-delete"></i>
                                                            </span>
                                                            <div class="img_area_with_preview position-absolute z-index-2">
                                                                <img id="pre_theme_image" class="h-auto aspect-1 bg-white d-none" src="dummy" alt="">
                                                            </div>
                                                            <div class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                                                <div class="d-flex flex-column justify-content-center align-items-center">
                                                                    <img alt="" class="w-75" src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}">
                                                                    <h3 class="text-muted">{{ translate('Upload_Image') }}</h3>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <p class="text-muted mt-2">
                                                            {{ translate('image_format') }} : {{ 'Jpg, png, jpeg,' }}
                                                            <br>
                                                            {{ translate('image_size') }} : {{ translate('max') }} {{ '2 MB' }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="additional_image_column">
                                            <div class="card h-100">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                                                        <div>
                                                            <label for="name" class="title-color text-capitalize font-weight-bold mb-0">{{ translate('Upload_Multiple_Images') }}</label>
                                                            <span class="badge badge-soft-info">{{ THEME_RATIO[theme_root_path()]['Product Image'] }}</span>
                                                            <span class="input-label-secondary cursor-pointer" data-toggle="tooltip" title="{{ translate('Upload_any_image_here') }}.">
                                                                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <p class="text-muted">{{ translate('Upload_Multiple_Images') }}</p>
                                                    <div class="coba-area">
                                                        <div class="row g-2" id="additional_Image_Section">
                                                            <div class="col-sm-12 col-md-4">
                                                                <div
                                                                    class="custom_upload_input position-relative border-dashed-2">
                                                                    <input type="file" name="gallery_image[]"
                                                                        class="custom-upload-input-file action-add-more-image"
                                                                        data-index="1"
                                                                        data-imgpreview="additional_Image_1"
                                                                        accept=".jpg, .webp, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*"
                                                                        data-target-section="#additional_Image_Section">
                                                                    <span class="delete_file_input delete_file_input_section btn btn-outline-danger btn-sm square-btn d-none">
                                                                        <i class="tio-delete"></i>
                                                                    </span>
                                                                    <div class="img_area_with_preview position-absolute z-index-2 border-0">
                                                                        <img id="additional_Image_1" class="h-auto aspect-1 bg-white d-none" alt="" src="">
                                                                    </div>
                                                                    <div
                                                                        class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                                                        <div
                                                                            class="d-flex flex-column justify-content-center align-items-center">
                                                                            <img alt=""
                                                                                src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}"
                                                                                class="w-75">
                                                                            <h3 class="text-muted">
                                                                                {{ translate('Upload_Image') }}
                                                                            </h3>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
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
                        <div class="d-flex flex-wrap gap-2 justify-content-end">
                            <button type="reset" class="btn btn-secondary">{{ translate('reset') }}</button>
                            <button type="submit" class="btn btn--primary">{{ translate('submit') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Section for displaying event categiry list -->

    </div>
</div>
<span id="image-path-of-product-upload-icon"
    data-path="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}"></span>
<span id="message-upload-image" data-text="{{ translate('Upload_Image') }}"></span>
@endsection

@push('script')
<!-- Include SweetAlert2 for confirmation dialogs -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="{{ dynamicAsset(path: 'public/js/ckeditor.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/admin/product-add-update.js') }}"></script>
<script>
    $('.image-input').on('change', function() {
        const input = this;
        const imgPreviewId = $(this).data('imgpreview');
        const img = document.getElementById(imgPreviewId);

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                if (img !== null) {
                    img.src = e.target.result;
                    img.classList.remove('d-none');
                }
                const imgName = input.files[0].name;
                const closestDataTitleElement = input.closest('[data-title]');
                if (closestDataTitleElement) {
                    closestDataTitleElement.setAttribute("data-title", imgName);
                }
            };
            reader.readAsDataURL(input.files[0]);
        }
    });

    ////////////////////////////////////////////////////////////////////////////////////////////// gallery 
    

    document.getElementById('addMember').addEventListener('click', function(event) {
        event.preventDefault();

        // Create a new row for the member inputs
        const newRow = document.createElement('div');
        newRow.className = 'row mt-2';

        newRow.innerHTML = `
        <div class="col-3"><input type='text' name='member_name[]' class='form-control' onkeyup="allowOnlyLetters(this)" required></div>
        <div class="col-3"><input type='text' name='member_phone_no[]' class='form-control' maxlength="13" onkeyup="formatIndianPhone(this)" required></div>
        <div class="col-3"><input type='text' name='member_position[]' class='form-control' onkeyup="allowOnlyLetters(this)" required></div>
        <div class="col-3">
            <a class="btn btn-outline-danger btn-sm removeMember"><i class='tio-remove'></i></a>
        </div>
    `;

        document.getElementById('memberContainer').appendChild(newRow);

        newRow.querySelector('.removeMember').addEventListener('click', function() {
            newRow.remove();
        });
    });

    // document.querySelector('.removeMember').addEventListener('click', function() {
    //     this.closest('.row').remove();
    // });
    document.addEventListener('DOMContentLoaded', function() {
        const removeButton = document.querySelector('.removeMember');
        if (removeButton) {
            removeButton.addEventListener('click', function() {
                this.closest('.row').remove();
            });
        }
    });

    function formatPAN(input) {
        let value = input.value.toUpperCase().replace(/[^A-Z0-9]/g, ''); // Allow only A-Z, 0-9
        let formatted = '';

        for (let i = 0; i < value.length && i < 10; i++) {
            if (i < 5) {
                if (/[A-Z]/.test(value[i])) {
                    formatted += value[i];
                }
            } else if (i < 9) {
                if (/[0-9]/.test(value[i])) {
                    formatted += value[i];
                }
            } else if (i === 9) {
                if (/[A-Z]/.test(value[i])) {
                    formatted += value[i];
                }
            }
        }
        input.value = formatted;
    }

    const accountNo = document.querySelector('input[name="account_no"]');
    const confirmAccountNo = document.querySelector('.confirm_account_no');

    confirmAccountNo.addEventListener('input', function() {
        if (accountNo.value !== confirmAccountNo.value) {
            document.querySelector('.confirm_account_no-error').textContent = "Account numbers do not match*.";
        } else {
            document.querySelector('.confirm_account_no-error').textContent = "";
        }
    });
</script>
<script>
    function formatIndianPhone(input) {
        let value = input.value;
        value = value.replace(/^(\+91)?/, '');
        value = value.replace(/[^0-9]/g, '');
        input.value = '+91' + value;
    }

    function allowOnlyLetters(input) {
        input.value = input.value.replace(/[^a-zA-Z ]/g, '');
    }

    $('.image-input-pdf').on('change', function() {
        let input = this;
        let file = input.files[0];
        let imageId = $(this).data('imgpreview');
        let img = document.getElementById(imageId);

        if (!file || !img) return;

        let fileName = file.name;
        let fileExt = fileName.split('.').pop().toLowerCase();

        let reader = new FileReader();

        // Define file preview logic
        console.log(fileExt);
        if (['jpg', 'jpeg', 'png', 'webp'].includes(fileExt)) {
            reader.onload = function(e) {
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
            $('#' + imageId).removeClass('d-none');
        } else if (fileExt === 'pdf') {
            img.src = "{{ asset('public/assets/back-end/img/doc-icon/pdf.png')}}";
            $('#' + imageId).removeClass('d-none');
        } else if (['doc', 'docx'].includes(fileExt)) {
            img.src = "{{ asset('public/assets/back-end/img/doc-icon/word.png')}}";
            $('#' + imageId).removeClass('d-none');
        } else {
            img.src = "{{ asset('public/assets/back-end/img/doc-icon/word.png')}}";
            $('#' + imageId).removeClass('d-none');
        }
        let container = input.closest('[data-title]');
        if (container) {
            container.setAttribute("data-title", fileName);
        }
    });
</script>
<script>
    $(document).ready(function() {
        $('.temple_select').select2({
            width: '100%'
        });
    });
</script>

@endpush