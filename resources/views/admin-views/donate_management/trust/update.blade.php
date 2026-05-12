@extends('layouts.back-end.app')

@section('title', translate('Trust_update'))
@push('css_or_js')
<link href="{{ dynamicAsset(path: 'public/assets/select2/css/select2.min.css') }}" rel="stylesheet">
@endpush
@section('content')
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 d-flex gap-2">
            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/') }}" alt="">
            {{ translate('Trust_update') }}
        </h2>
    </div>
    <div class="row">
        <!-- Form for adding new Trust_update -->
        <div class="col-md-12 mb-3">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.donate_management.trust.updatestore') }}" method="post" enctype="multipart/form-data">
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
                                            @php
                                            $selectedTemples = old('temple', json_decode($old_data['trust_temple_id'] ?? '[]'));
                                            @endphp

                                            <select type="text" name="temple[]" class="form-control temple_select select2" required multiple>
                                                @if($temple_list)
                                                @foreach($temple_list as $va)
                                                <option value="{{ $va['id'] }}" {{ ((in_array($va['id'], $selectedTemples)) ? 'selected' : '') }}>
                                                    {{ $va['name'] }}
                                                </option>
                                                @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                @foreach($languages as $lang)
                                <?php
                                $translate = [];
                                if (!empty($old_data['translations'])) {
                                    foreach ($old_data['translations'] as $translations) {
                                        if ($translations->locale == $lang && $translations->key == 'trust_name') {
                                            $translate[$lang]['trust_name'] = $translations->value;
                                        }
                                        if ($translations->locale == $lang && $translations->key == 'name') {
                                            $translate[$lang]['name'] = $translations->value;
                                        }
                                        if ($translations->locale == $lang && $translations->key == 'description') {
                                            $translate[$lang]['description'] = $translations->value;
                                        }
                                        if ($translations->locale == $lang && $translations->key == 'full_address') {
                                            $translate[$lang]['full_address'] = $translations->value;
                                        }
                                    }
                                }
                                ?>

                                <div class="form-group {{$lang != $defaultLanguage ? 'd-none':''}} form-system-language-form" id="{{$lang}}-form">
                                    <div class="row">
                                        <div class="col-md-6 mt-2">
                                            <label class="title-color" for="category_name">{{ translate('Select_category') }} <span class="text-danger">*</span></label>
                                            <select name="category_id" class="form-control fillupdata" data-point='1' onchange="$(`.fillupdata[data-point='1']`).val(this.value)" {{$lang == $defaultLanguage? 'required':''}}>
                                                <option value="">Select Category</option>
                                                @if($all_categorys)
                                                @foreach($all_categorys as $vals)
                                                <option value="{{ $vals['id']}}" {{ ((old('category_id',($old_data['category_id']??"")) == $vals['id'])?"selected":"")}}>{{ ($vals['name']??"")}}</option>
                                                @endforeach
                                                @endif
                                            </select>
                                            <input type="hidden" name="lang[]" value="{{$lang}}" id="lang">
                                            <input type="hidden" name="id" value="{{$old_data['id']}}">
                                        </div>
                                        <div class="col-md-6 mt-2">
                                            <label class="title-color" for="name">{{ translate('Name') }} <span class="text-danger">*</span> ({{ strtoupper($lang) }})</label>
                                            <input type="text" name="name[]" class="form-control" value="{{ old('name.'.$loop->index,(($lang == $defaultLanguage) ? $old_data['name'] : ($translate[$lang]['name']??'') ))}}" id="{{$lang}}_name" placeholder="{{ translate('Enter_name') }}" {{$lang == $defaultLanguage? 'required':''}}>
                                        </div>
                                        <div class="col-md-6 mt-2">
                                            <label class="title-color" for="trust_name">{{ translate('Trust_Name') }} <span class="text-danger">*</span> ({{ strtoupper($lang) }})</label>
                                            <input type="text" name="trust_name[]" class="form-control" value="{{ old('trust_name.'.$loop->index,(($lang == $defaultLanguage)? $old_data['trust_name'] : ($translate[$lang]['trust_name']??'') )) }}" id="{{$lang}}_trust_name" placeholder="{{ translate('Enter_trust_name') }}" {{$lang == $defaultLanguage? 'required':''}}>
                                        </div>
                                        <div class="col-md-6 mt-2">
                                            <label class="title-color" for="Trust_pan_card">{{ translate('Trust_pan_card') }}</label>
                                            <input type="text" name="trust_pan_card" class="form-control fillupdata" data-point='2' onblur="$(`.fillupdata[data-point='2']`).val(this.value)" value="{{ old('trust_pan_card',($old_data['trust_pan_card']??'') ) }}" id="{{$lang}}_trust_pan_card" placeholder="{{ translate('Enter_trust_pan_card') }}" maxlength="10" onkeyup="formatPAN(this)" {{$lang == $defaultLanguage? 'required':''}}>
                                        </div>
                                        <div class="col-md-4 mt-2">
                                            <label class="title-color" for="pan_card">{{ translate('pan_card') }}</label>
                                            <input type="text" name="pan_card" class="form-control fillupdata" data-point='3' onblur="$(`.fillupdata[data-point='3']`).val(this.value)" value="{{ old('pan_card',($old_data['pan_card']??''))}}" id="{{$lang}}_pan_card" placeholder="{{ translate('Enter_pan_card') }}" maxlength="10" onkeyup="formatPAN(this)" {{$lang == $defaultLanguage? 'required':''}}>
                                        </div>
                                        <div class="col-md-4 mt-2">
                                            <label class="title-color" for="full_address">{{ translate('Full_address') }}<span class="text-danger">*</span> ({{ strtoupper($lang) }})</label>
                                            <input type="text" name="full_address[]" class="form-control" value="{{ old('full_address.'.$loop->index,(($lang == $defaultLanguage)? $old_data['full_address']??'' : $translate[$lang]['full_address']??'' ) ) }}" id="{{$lang}}_full_address" placeholder="{{ translate('Enter_full_address') }}" {{$lang == $defaultLanguage? 'required':''}}>
                                        </div>
                                        <div class="col-md-4 mt-2">
                                            <label class="title-color" for="trust_email">{{ translate('trust_email') }}<span class="text-danger">*</span></label>
                                            <input type="email" name="trust_email" class="form-control fillupdata" data-point='4' onblur="$(`.fillupdata[data-point='4']`).val(this.value)" value="{{ old('trust_email',$old_data['trust_email'])}}" id="{{$lang}}_trust_email" placeholder="{{ translate('Enter_trust_email') }}" {{$lang == $defaultLanguage? 'required':''}}>
                                        </div>
                                        <div class="col-md-12">
                                            <label class="title-color w-100 font-weight-bold h3" for="details">{{ translate('Description') }}<span class="text-danger">*</span> ({{ strtoupper($lang) }})</label>
                                            <textarea class='form-control ckeditor' name='description[]'>{{ old('description.'.$loop->index,(($lang == $defaultLanguage)?$old_data['description']: ($translate[$lang]['description']??'') ))}}</textarea>
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
                                            @php
                                            $members = old('member_name.*', $old_data['memberlist'] ? json_decode($old_data['memberlist'], true) : []);
                                            @endphp

                                            @if($members)
                                            @foreach($members as $index => $member)
                                            <div class="row mt-2">
                                                <div class="col-3">
                                                    <input type="text" name="member_name[]" class="form-control" value="{{ old('member_name.'.$index, ($member['member_name']??'')) }}" onkeyup="allowOnlyLetters(this)" required>
                                                </div>
                                                <div class="col-3">
                                                    <input type="text" name="member_phone_no[]" class="form-control" value="{{ old('member_phone_no.' . $index, ($member['member_phone_no']??'')) }}" maxlength="13" onkeyup="formatIndianPhone(this)" required>
                                                </div>
                                                <div class="col-3">
                                                    <input type="text" name="member_position[]" class="form-control" value="{{ old('member_position.' . $index, ($member['member_position']??'')) }}" onkeyup="allowOnlyLetters(this)" required>
                                                </div>
                                                <div class="col-3">
                                                    <a class="btn btn-outline-danger btn-sm removeMember"><i class='tio-remove'></i></a>
                                                </div>
                                            </div>
                                            @endforeach
                                            @else
                                            <div class="row mt-2">
                                                <div class="col-3">
                                                    <input type="text" name="member_name[]" class="form-control" onkeyup="allowOnlyLetters(this)" required>
                                                </div>
                                                <div class="col-3">
                                                    <input type="text" name="member_phone_no[]" class="form-control" maxlength="13" onkeyup="formatIndianPhone(this)" required>
                                                </div>
                                                <div class="col-3">
                                                    <input type="text" name="member_position[]" class="form-control" onkeyup="allowOnlyLetters(this)" required>
                                                </div>
                                                <div class="col-3">
                                                    <a class="btn btn-outline-danger btn-sm removeMember"><i class='tio-remove'></i></a>
                                                </div>
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
                                        <input type='text' class="form-control @error('beneficiary_name') is-invalid @enderror" name='beneficiary_name' value="{{ old('beneficiary_name',$old_data['beneficiary_name']) }}" placeholder="{{ translate('Beneficiary_Name') }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="title-color w-100" for="account_type">{{ translate('Account_type') }}</label>
                                        <select class='form-control' name='account_type' placeholder="{{ translate('Account_type') }}">
                                            <option value="saving account" {{ old('account_type',$old_data['account_type']) == "saving account" ? 'selected' : '' }}>saving account</option>
                                            <option value="current account" {{ old('account_type',$old_data['account_type']) == "current account" ? 'selected' : '' }}>current account</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="title-color w-100" for="address">{{ translate('Bank_name') }}</label>
                                        <select class='form-control' name='bank_name' placeholder="{{ translate('Bank_name') }}">
                                            @if($bankList)
                                            @foreach($bankList as $bank)
                                            <option value="{{ $bank['bank_name'] }}" {{ ((old('bank_name',$old_data['bank_name']) == ($bank['bank_name']??"") )?"selected":"")}}>{{ $bank['bank_name'] }}</option>
                                            @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="title-color w-100" for="ifsc_code">{{ translate('IFSC_code') }}</label>
                                        <input type='text' class='form-control' name='ifsc_code' value="{{ old('ifsc_code',$old_data['ifsc_code'])}}" placeholder="{{ translate('IFSC_code') }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="title-color w-100" for="account_no">{{ translate('Account_Number') }}</label>
                                        <input type='text' class='form-control' name='account_no' value="{{ old('account_no',$old_data['account_no'])}}" placeholder="{{ translate('Account_Number') }}" onkeyup="this.value = this.value.replace(/[^0-9]/g, '')">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="title-color w-100" for="c_account_no">{{ translate('Confirm_Account_number') }}</label>
                                        <input type='text' class='form-control confirm_account_no' name='c_account_no' value="{{ old('c_account_no',$old_data['account_no'])}}" placeholder="{{ translate('Account_Number') }}" onkeyup="this.value = this.value.replace(/[^0-9]/g, '')">
                                        <span class="font-weight-bold confirm_account_no-error text-danger"></span>
                                    </div>
                                </div>

                                <div class="row mt-3 form-group">
                                    <div class="col-md-12">
                                        <label class="title-color w-100" for="website_link">{{ translate('website_link') }}</label>
                                        <input type='text' class='form-control' name='website' value="{{ old('website',$old_data['website']) }}" placeholder="{{ translate('website_link') }}">
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
                                                            <span class="input-label-secondary cursor-pointer" data-toggle="tooltip" title="{{ translate('add_pan_card_image') }} JPG, PNG, JPEG, WEBP, PDF or DOC {{ translate('format_within') }} 5MB">
                                                                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        @php
                                                        $panCardImg = $old_data['pan_card_image'] ?? null;
                                                        $panCardImgext = $panCardImg ? strtolower(pathinfo($panCardImg, PATHINFO_EXTENSION)) : null;
                                                        @endphp
                                                        @if($panCardImgext === 'pdf')
                                                        <a href="{{ asset('storage/app/public/donate/document/'.$panCardImg) }}" target="_blank">View PDF</a>
                                                        @elseif($panCardImgext === 'doc')
                                                        <a href="{{ asset('storage/app/public/donate/document/'.$panCardImg) }}" target="_blank">View Doc</a>
                                                        @endif
                                                        <div class="custom_upload_input">
                                                            <input type="file" name="pan_card_image" class="custom-upload-input-file action-upload-color-image image-input-pdf" id="" data-imgpreview="pan_card_images" accept=".jpg, .jpeg, .png, .webp, .pdf, .doc, .docx">
                                                            <span class="delete_file_input btn btn-outline-danger btn-sm square-btn d--none">
                                                                <i class="tio-delete"></i>
                                                            </span>
                                                            <div class="img_area_with_preview position-absolute z-index-2">
                                                                @if($panCardImgext === 'jpg' || $panCardImgext === 'jpeg' || $panCardImgext === 'png' || $panCardImgext === 'gif')
                                                                <img id="pan_card_images" class="h-auto aspect-1 bg-white  {{ (($old_data['pan_card_image'])?'':'d-none')}}" src="{{ getValidImage(path: 'storage/app/public/donate/document/'.$panCardImg, type: 'backend-product') }}" alt="">
                                                                @elseif($panCardImgext === 'pdf')
                                                                <img id="pan_card_images" class="h-auto aspect-1 bg-white  {{ (($old_data['pan_card_image'])?'':'d-none')}}" src="{{ asset('public/assets/back-end/img/doc-icon/pdf.png')}}" alt="">
                                                                @elseif($panCardImgext === 'doc')
                                                                <img id="pan_card_images" class="h-auto aspect-1 bg-white  {{ (($old_data['pan_card_image'])?'':'d-none')}}" src="{{ asset('public/assets/back-end/img/doc-icon/word.png')}}" alt="">
                                                                @endif
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
                                                            <span class="input-label-secondary cursor-pointer" data-toggle="tooltip" title="{{ translate('add_Trustees_pan_card_image') }} JPG, PNG, JPEG, WEBP, PDF or DOC {{ translate('format_within') }} 5MB">
                                                                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        @php
                                                        $TrustpanCardImg = $old_data['trust_pan_card_image'] ?? null;
                                                        $TrustpanCardImgext = $TrustpanCardImg ? strtolower(pathinfo($TrustpanCardImg, PATHINFO_EXTENSION)) : null;
                                                        @endphp
                                                        @if($TrustpanCardImgext === 'pdf')
                                                        <a href="{{ asset('storage/app/public/donate/document/'.$TrustpanCardImg) }}" target="_blank">View PDF</a>
                                                        @elseif($TrustpanCardImgext === 'doc')
                                                        <a href="{{ asset('storage/app/public/donate/document/'.$TrustpanCardImg) }}" target="_blank">View Doc</a>
                                                        @endif
                                                        <div class="custom_upload_input">
                                                            <input type="file" name="trustees_pan_card_image" class="custom-upload-input-file action-upload-color-image image-input-pdf" id="" data-imgpreview="trustees_pan_card_images" accept=".jpg, .jpeg, .png, .webp, .pdf, .doc, .docx">
                                                            <span class="delete_file_input btn btn-outline-danger btn-sm square-btn d--none">
                                                                <i class="tio-delete"></i>
                                                            </span>
                                                            <div class="img_area_with_preview position-absolute z-index-2">
                                                                @if($TrustpanCardImgext === 'jpg' || $TrustpanCardImgext === 'jpeg' || $TrustpanCardImgext === 'png' || $TrustpanCardImgext === 'gif')
                                                                <img id="trustees_pan_card_images" class="h-auto aspect-1 bg-white  {{ (($old_data['trust_pan_card_image'])?'':'d-none')}}" src="{{ getValidImage(path: 'storage/app/public/donate/document/'.$TrustpanCardImg, type: 'backend-product') }}" alt="">
                                                                @elseif($TrustpanCardImgext === 'pdf')
                                                                <img id="trustees_pan_card_images" class="h-auto aspect-1 bg-white  {{ (($old_data['trust_pan_card_image'])?'':'d-none')}}" src="{{ asset('public/assets/back-end/img/doc-icon/pdf.png')}}" alt="">
                                                                @elseif($TrustpanCardImgext === 'doc')
                                                                <img id="trustees_pan_card_images" class="h-auto aspect-1 bg-white  {{ (($old_data['trust_pan_card_image'])?'':'d-none')}}" src="{{ asset('public/assets/back-end/img/doc-icon/word.png')}}" alt="">
                                                                @endif
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
                                    <!--  0 section -->

                                    <!--  1 section -->
                                    <div class="col-md-4">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                                                        <div>
                                                            <label for="name" class="title-color text-capitalize font-weight-bold mb-0">{{ translate('12A_certificate') }}</label>
                                                            <span class="badge badge-soft-info">{{ THEME_RATIO[theme_root_path()]['Product Image'] }}</span>
                                                            <span class="input-label-secondary cursor-pointer" data-toggle="tooltip" title="{{ translate('add_12A_certificate') }} JPG, PNG, JPEG, WEBP, PDF or DOC {{ translate('format_within') }} 5MB">
                                                                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <input type="text" class="form-control my-2" name="twelve_a_number" value="{{  old('twelve_a_number',$old_data['twelve_a_number']) }}" placeholder="{{ translate('12A_certificate') }}">
                                                    <div>
                                                        @php
                                                        $twelveCertificateImg = $old_data['twelve_a_certificate'] ?? null;
                                                        $twelveCertificateImgext = $twelveCertificateImg ? strtolower(pathinfo($twelveCertificateImg, PATHINFO_EXTENSION)) : null;
                                                        @endphp
                                                        @if($twelveCertificateImgext === 'pdf')
                                                        <a href="{{ asset('storage/app/public/donate/document/'.$twelveCertificateImg) }}" target="_blank">View PDF</a>
                                                        @elseif($twelveCertificateImgext === 'doc')
                                                        <a href="{{ asset('storage/app/public/donate/document/'.$twelveCertificateImg) }}" target="_blank">View Doc</a>
                                                        @endif
                                                        <div class="custom_upload_input">
                                                            <input type="file" name="twelve_a_certificate" class="custom-upload-input-file action-upload-color-image image-input-pdf" id="" data-imgpreview="12A_certificate" accept=".jpg, .jpeg, .png, .webp, .pdf, .doc, .docx">
                                                            <span class="delete_file_input btn btn-outline-danger btn-sm square-btn d--none">
                                                                <i class="tio-delete"></i>
                                                            </span>
                                                            <div class="img_area_with_preview position-absolute z-index-2">
                                                                @if($twelveCertificateImgext === 'jpg' || $twelveCertificateImgext === 'jpeg' || $twelveCertificateImgext === 'png' || $twelveCertificateImgext === 'gif')
                                                                <img id="12A_certificate" class="h-auto aspect-1 bg-white  {{ (($old_data['twelve_a_certificate'])?'':'d-none')}}" src="{{ getValidImage(path: 'storage/app/public/donate/document/'.$twelveCertificateImg, type: 'backend-product') }}" alt="">
                                                                @elseif($twelveCertificateImgext === 'pdf')
                                                                <img id="12A_certificate" class="h-auto aspect-1 bg-white  {{ (($old_data['twelve_a_certificate'])?'':'d-none')}}" src="{{ asset('public/assets/back-end/img/doc-icon/pdf.png')}}" alt="">
                                                                @elseif($twelveCertificateImgext === 'doc')
                                                                <img id="12A_certificate" class="h-auto aspect-1 bg-white  {{ (($old_data['twelve_a_certificate'])?'':'d-none')}}" src="{{ asset('public/assets/back-end/img/doc-icon/word.png')}}" alt="">
                                                                @endif
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
                                                            <label for="name" class="title-color text-capitalize font-weight-bold mb-0">{{ translate('eighty_G_certificate') }}</label>
                                                            <span class="badge badge-soft-info">{{ THEME_RATIO[theme_root_path()]['Product Image'] }}</span>
                                                            <span class="input-label-secondary cursor-pointer" data-toggle="tooltip" title="{{ translate('add_eighty_G_certificate_in') }} JPG, PNG, JPEG, WEBP, PDF or DOC {{ translate('format_within') }} 5MB">
                                                                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <input type="text" class="form-control my-2" name="eighty_g_number" value="{{  old('eighty_g_number',$old_data['eighty_g_number']) }}" placeholder="{{ translate('eighty_G_certificate') }}">
                                                    <div>
                                                        @php
                                                        $EightyCertificateImg = $old_data['eighty_g_certificate'] ?? null;
                                                        $EightyCertificateImgext = $EightyCertificateImg ? strtolower(pathinfo($EightyCertificateImg, PATHINFO_EXTENSION)) : null;
                                                        @endphp
                                                        @if($EightyCertificateImgext === 'pdf')
                                                        <a href="{{ asset('storage/app/public/donate/document/'.$EightyCertificateImg) }}" target="_blank">View PDF</a>
                                                        @elseif($EightyCertificateImgext === 'doc')
                                                        <a href="{{ asset('storage/app/public/donate/document/'.$EightyCertificateImg) }}" target="_blank">View Doc</a>
                                                        @endif
                                                        <div class="custom_upload_input">
                                                            <input type="file" name="eighty_g_certificate" class="custom-upload-input-file action-upload-color-image image-input-pdf" id="" data-imgpreview="pre_eighty_g_certificate" accept=".jpg, .jpeg, .png, .webp, .pdf, .doc, .docx">
                                                            <span class="delete_file_input btn btn-outline-danger btn-sm square-btn d--none">
                                                                <i class="tio-delete"></i>
                                                            </span>

                                                            <div class="img_area_with_preview position-absolute z-index-2">
                                                                @if($EightyCertificateImgext === 'jpg' || $EightyCertificateImgext === 'jpeg' || $EightyCertificateImgext === 'png' || $EightyCertificateImgext === 'gif')
                                                                <img id="pre_eighty_g_certificate" class="h-auto aspect-1 bg-white  {{ (($old_data['eighty_g_certificate'])?'':'d-none')}}" src="{{ getValidImage(path: 'storage/app/public/donate/document/'.$EightyCertificateImg, type: 'backend-product') }}" alt="">
                                                                @elseif($EightyCertificateImgext === 'pdf')
                                                                <img id="pre_eighty_g_certificate" class="h-auto aspect-1 bg-white  {{ (($old_data['eighty_g_certificate'])?'':'d-none')}}" src="{{ asset('public/assets/back-end/img/doc-icon/pdf.png')}}" alt="">
                                                                @elseif($EightyCertificateImgext === 'doc')
                                                                <img id="pre_eighty_g_certificate" class="h-auto aspect-1 bg-white  {{ (($old_data['eighty_g_certificate'])?'':'d-none')}}" src="{{ asset('public/assets/back-end/img/doc-icon/word.png')}}" alt="">
                                                                @endif
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
                                                            <label for="name" class="title-color text-capitalize font-weight-bold mb-0">{{ translate('Niti_aayog_certificate ') }}</label>
                                                            <span class="badge badge-soft-info">{{ THEME_RATIO[theme_root_path()]['Product Image'] }}</span>
                                                            <span class="input-label-secondary cursor-pointer" data-toggle="tooltip" title="{{ translate('add_your_aadhaar_card') }} JPG, PNG, JPEG, WEBP, PDF or DOC {{ translate('format_within') }} 5MB">
                                                                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <input type="text" class="form-control my-2" name="niti_aayog_number" value="{{  old('niti_aayog_number',$old_data['niti_aayog_number']) }}" placeholder="{{ translate('Niti_aayog_certificate') }}">
                                                    <div>
                                                        @php
                                                        $NitiCertificateImg = $old_data['niti_aayog_certificate'] ?? null;
                                                        $NitiCertificateImgext = $NitiCertificateImg ? strtolower(pathinfo($NitiCertificateImg, PATHINFO_EXTENSION)) : null;
                                                        @endphp
                                                        @if($NitiCertificateImgext === 'pdf')
                                                        <a href="{{ asset('storage/app/public/donate/document/'.$NitiCertificateImg) }}" target="_blank">View PDF</a>
                                                        @elseif($NitiCertificateImgext === 'doc')
                                                        <a href="{{ asset('storage/app/public/donate/document/'.$NitiCertificateImg) }}" target="_blank">View Doc</a>
                                                        @endif
                                                        <div class="custom_upload_input">
                                                            <input type="file" name="niti_aayog_certificate" class="custom-upload-input-file action-upload-color-image image-input-pdf" id="" data-imgpreview="pre_niti_aayog_certificate" accept=".jpg, .jpeg, .png, .webp, .pdf, .doc, .docx">
                                                            <span class="delete_file_input btn btn-outline-danger btn-sm square-btn d--none">
                                                                <i class="tio-delete"></i>
                                                            </span>

                                                            <div class="img_area_with_preview position-absolute z-index-2">
                                                                @if($NitiCertificateImgext === 'jpg' || $NitiCertificateImgext === 'jpeg' || $NitiCertificateImgext === 'png' || $NitiCertificateImgext === 'gif')
                                                                <img id="pre_niti_aayog_certificate" class="h-auto aspect-1 bg-white  {{ (($old_data['niti_aayog_certificate'])?'':'d-none')}}" src="{{ getValidImage(path: 'storage/app/public/donate/document/'.$NitiCertificateImg, type: 'backend-product') }}" alt="">
                                                                @elseif($NitiCertificateImgext === 'pdf')
                                                                <img id="pre_niti_aayog_certificate" class="h-auto aspect-1 bg-white  {{ (($old_data['niti_aayog_certificate'])?'':'d-none')}}" src="{{ asset('public/assets/back-end/img/doc-icon/pdf.png')}}" alt="">
                                                                @elseif($NitiCertificateImgext === 'doc')
                                                                <img id="pre_niti_aayog_certificate" class="h-auto aspect-1 bg-white  {{ (($old_data['niti_aayog_certificate'])?'':'d-none')}}" src="{{ asset('public/assets/back-end/img/doc-icon/word.png')}}" alt="">
                                                                @endif
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
                                                    <input type="text" class="form-control my-2" name="csr_number" value="{{  old('csr_number',$old_data['csr_number']) }}" placeholder="{{ translate('CSR_certificate') }}">
                                                    <div>
                                                        @php
                                                        $CSRCertificateImg = $old_data['csr_certificate'] ?? null;
                                                        $CSRCertificateImgext = $CSRCertificateImg ? strtolower(pathinfo($CSRCertificateImg, PATHINFO_EXTENSION)) : null;
                                                        @endphp
                                                        @if($CSRCertificateImgext === 'pdf')
                                                        <a href="{{ asset('storage/app/public/donate/document/'.$CSRCertificateImg) }}" target="_blank">View PDF</a>
                                                        @elseif($CSRCertificateImgext === 'doc')
                                                        <a href="{{ asset('storage/app/public/donate/document/'.$CSRCertificateImg) }}" target="_blank">View Doc</a>
                                                        @endif
                                                        <div class="custom_upload_input">
                                                            <input type="file" name="csr_certificate" class="custom-upload-input-file action-upload-color-image image-input-pdf" id="" data-imgpreview="pre_csr_certificate" accept=".jpg, .jpeg, .png, .webp, .pdf, .doc, .docx">
                                                            <span class="delete_file_input btn btn-outline-danger btn-sm square-btn d--none">
                                                                <i class="tio-delete"></i>
                                                            </span>
                                                            <div class="img_area_with_preview position-absolute z-index-2">
                                                                @if($CSRCertificateImgext === 'jpg' || $CSRCertificateImgext === 'jpeg' || $CSRCertificateImgext === 'png' || $CSRCertificateImgext === 'gif')
                                                                <img id="pre_csr_certificate" class="h-auto aspect-1 bg-white {{ (($old_data['csr_certificate'])?'':'d-none')}}" src="{{ getValidImage(path: 'storage/app/public/donate/document/'.$CSRCertificateImg, type: 'backend-product') }}" alt="">
                                                                @elseif($CSRCertificateImgext === 'pdf')
                                                                <img id="pre_csr_certificate" class="h-auto aspect-1 bg-white {{ (($old_data['csr_certificate'])?'':'d-none')}}" src="{{ asset('public/assets/back-end/img/doc-icon/pdf.png')}}" alt="">
                                                                @elseif($CSRCertificateImgext === 'doc')
                                                                <img id="pre_csr_certificate" class="h-auto aspect-1 bg-white {{ (($old_data['csr_certificate'])?'':'d-none')}}" src="{{ asset('public/assets/back-end/img/doc-icon/word.png')}}" alt="">
                                                                @endif
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
                                                            <label for="name" class="title-color text-capitalize font-weight-bold mb-0">{{ translate('E_anudhan_certificate ') }}</label>
                                                            <span class="badge badge-soft-info">{{ THEME_RATIO[theme_root_path()]['Product Image'] }}</span>
                                                            <span class="input-label-secondary cursor-pointer" data-toggle="tooltip" title="{{ translate('add_E_anudhan_certificate_in') }} JPG, PNG, JPEG, WEBP, PDF or DOC {{ translate('format_within') }} 5MB">
                                                                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <input type="text" class="form-control my-2" name="e_anudhan_number" value="{{  old('e_anudhan_number',$old_data['e_anudhan_number']) }}" placeholder="{{ translate('E_anudhan_certificate') }}">
                                                    <div>
                                                        @php
                                                        $EAnudhanCertificateImg = $old_data['e_anudhan_certificate'] ?? null;
                                                        $EAnudhanCertificateImgext = $EAnudhanCertificateImg ? strtolower(pathinfo($EAnudhanCertificateImg, PATHINFO_EXTENSION)) : null;
                                                        @endphp
                                                        @if($EAnudhanCertificateImgext === 'pdf')
                                                        <a href="{{ asset('storage/app/public/donate/document/'.$EAnudhanCertificateImg) }}" target="_blank">View PDF</a>
                                                        @elseif($EAnudhanCertificateImgext === 'doc')
                                                        <a href="{{ asset('storage/app/public/donate/document/'.$EAnudhanCertificateImg) }}" target="_blank">View Doc</a>
                                                        @endif
                                                        <div class="custom_upload_input">
                                                            <input type="file" name="e_anudhan_certificate" class="custom-upload-input-file action-upload-color-image image-input-pdf" id="" data-imgpreview="pre_e_anudhan_certificate" accept=".jpg, .jpeg, .png, .webp, .pdf, .doc, .docx">
                                                            <span class="delete_file_input btn btn-outline-danger btn-sm square-btn d--none">
                                                                <i class="tio-delete"></i>
                                                            </span>
                                                            <div class="img_area_with_preview position-absolute z-index-2">
                                                                @if($EAnudhanCertificateImgext === 'jpg' || $EAnudhanCertificateImgext === 'jpeg' || $EAnudhanCertificateImgext === 'png' || $EAnudhanCertificateImgext === 'gif')
                                                                <img id="pre_e_anudhan_certificate" class="h-auto aspect-1 bg-white {{ (($old_data['e_anudhan_certificate'])?'':'d-none')}}" src="{{ getValidImage(path: 'storage/app/public/donate/document/'.$EAnudhanCertificateImg, type: 'backend-product') }}" alt="">
                                                                @elseif($EAnudhanCertificateImgext === 'pdf')
                                                                <img id="pre_e_anudhan_certificate" class="h-auto aspect-1 bg-white {{ (($old_data['e_anudhan_certificate'])?'':'d-none')}}" src="{{ asset('public/assets/back-end/img/doc-icon/pdf.png')}}" alt="">
                                                                @elseif($EAnudhanCertificateImgext === 'doc')
                                                                <img id="pre_e_anudhan_certificate" class="h-auto aspect-1 bg-white {{ (($old_data['e_anudhan_certificate'])?'':'d-none')}}" src="{{ asset('public/assets/back-end/img/doc-icon/word.png')}}" alt="">
                                                                @endif
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
                                                    <input type="text" class="form-control my-2" name="frc_number" value="{{  old('frc_number',$old_data['frc_number']) }}" placeholder="{{ translate('FRC_certificate') }}">
                                                    <div>
                                                        @php
                                                        $FRCcertificateImg = $old_data['frc_certificate'] ?? null;
                                                        $FRCcertificateImgext = $FRCcertificateImg ? strtolower(pathinfo($FRCcertificateImg, PATHINFO_EXTENSION)) : null;
                                                        @endphp
                                                        @if($FRCcertificateImgext === 'pdf')
                                                        <a href="{{ asset('storage/app/public/donate/document/'.$FRCcertificateImg) }}" target="_blank">View PDF</a>
                                                        @elseif($FRCcertificateImgext === 'doc')
                                                        <a href="{{ asset('storage/app/public/donate/document/'.$FRCcertificateImg) }}" target="_blank">View Doc</a>
                                                        @endif
                                                        <div class="custom_upload_input">
                                                            <input type="file" name="frc_certificate" class="custom-upload-input-file action-upload-color-image image-input-pdf" id="" data-imgpreview="pre_frc_certificate" accept=".jpg, .jpeg, .png, .webp, .pdf, .doc, .docx">
                                                            <span class="delete_file_input btn btn-outline-danger btn-sm square-btn d--none">
                                                                <i class="tio-delete"></i>
                                                            </span>
                                                            <div class="img_area_with_preview position-absolute z-index-2">
                                                                @if($FRCcertificateImgext === 'jpg' || $FRCcertificateImgext === 'jpeg' || $FRCcertificateImgext === 'png' || $FRCcertificateImgext === 'gif')
                                                                <img id="pre_frc_certificate" class="h-auto aspect-1 bg-white {{ (($old_data['frc_certificate'])?'':'d-none')}}" src="{{ getValidImage(path: 'storage/app/public/donate/document/'.$FRCcertificateImg, type: 'backend-product') }}" alt="">
                                                                @elseif($FRCcertificateImgext === 'pdf')
                                                                <img id="pre_frc_certificate" class="h-auto aspect-1 bg-white {{ (($old_data['frc_certificate'])?'':'d-none')}}" src="{{ asset('public/assets/back-end/img/doc-icon/pdf.png')}}" alt="">
                                                                @elseif($FRCcertificateImgext === 'doc')
                                                                <img id="pre_frc_certificate" class="h-auto aspect-1 bg-white {{ (($old_data['frc_certificate'])?'':'d-none')}}" src="{{ asset('public/assets/back-end/img/doc-icon/word.png')}}" alt="">
                                                                @endif
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
                                                            <span class="input-label-secondary cursor-pointer" data-toggle="tooltip" title="{{ translate('theme_image') }} JPG, PNG or JPEG {{ translate('format_within') }} 2MB">
                                                                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="custom_upload_input">
                                                            <input type="file" name="theme_image" class="custom-upload-input-file action-upload-color-image image-input" id="" data-imgpreview="pre_theme_image" accept=".jpg, .png, .jpeg">
                                                            <span class="delete_file_input btn btn-outline-danger btn-sm square-btn d--none">
                                                                <i class="tio-delete"></i>
                                                            </span>
                                                            <div class="img_area_with_preview position-absolute z-index-2">
                                                                <img id="pre_theme_image" class="h-auto aspect-1 bg-white  {{ (($old_data['theme_image'])?'':'d-none')}}" src="{{ getValidImage(path: 'storage/app/public/donate/trust/'.$old_data['theme_image'], type: 'backend-product')  }}" src="dummy" alt="">
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
                                                    <div
                                                        class="d-flex align-items-center justify-content-between gap-2 mb-2">
                                                        <div>
                                                            <label for="name" class="title-color text-capitalize font-weight-bold mb-0">{{ translate('Upload_Multiple_Images') }}</label>
                                                            <span class="badge badge-soft-info">{{ THEME_RATIO[theme_root_path()]['Product Image'] }}</span>
                                                            <span class="input-label-secondary cursor-pointer" data-toggle="tooltip"  title="{{ translate('Upload_any_image_here') }}.">
                                                                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <p class="text-muted">{{ translate('Upload_Multiple_Images') }}</p>
                                                    <div class="coba-area">
                                                        <div class="row g-2" id="additional_Image_Section">
                                                            @if (!empty($old_data['gallery_image']) && json_decode($old_data['gallery_image'], true))
                                                            @foreach (json_decode($old_data['gallery_image'], true) as $key => $photo)
                                                            @php($unique_id = rand(1111, 9999))
                                                            <div class="col-sm-12 col-md-4">
                                                                <div class="custom_upload_input custom-upload-input-file-area position-relative border-dashed-2">
                                                                    <a class="delete_file_input_css btn btn-outline-danger btn-sm square-btn"
                                                                        href="{{ route('admin.donate_management.trust.gallery-image-remove', ['id'=>$old_data['id'],'image_path'=> $photo]) }}">
                                                                        <i class="tio-delete"></i>
                                                                    </a>
                                                                    <div
                                                                        class="img_area_with_preview position-absolute z-index-2 border-0">
                                                                        <img id="additional_Image_{{ $unique_id }}"
                                                                            alt=""
                                                                            class="h-auto aspect-1 bg-white onerror-add-class-d-none"
                                                                            src="{{ getValidImage(path: 'storage/app/public/donate/trust/' . $photo, type: 'backend-product') }}">
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
                                                            @endforeach
                                                            @endif
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
    'use strict';

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


    function removeOldImage(imagePath) {
        Swal.fire({
            title: 'Are you sure to delete this ?',
            text: "आप इसे वापस करने में सक्षम नहीं होंगे",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "{{ route('admin.donate_management.trust.gallery-image-remove') }}?id={{$old_data['id']}}&image_path=" + encodeURIComponent(imagePath);
            }
        });
    }





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
            img.src = "{{ asset('/assets/back-end/img/doc-icon/word.png')}}";
            $('#' + imageId).removeClass('d-none');
        } else {
            img.src = "{{ asset('/assets/back-end/img/doc-icon/word.png')}}";
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