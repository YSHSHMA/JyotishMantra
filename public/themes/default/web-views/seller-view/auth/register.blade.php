@extends('layouts.front-end.app')

@section('title', translate('vendor_Apply'))

@push('css_or_js')
<link href="{{theme_asset(path: 'public/assets/back-end/css/select2.min.css')}}" rel="stylesheet"/>
<link href="{{theme_asset(path: 'public/assets/back-end/css/croppie.css')}}" rel="stylesheet">
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/css/intlTelInput.css') }}">
@endpush


@section('content')

<div class="container py-5 rtl text-start">

    <h3 class="mb-3 text-center fs-24 font-semi-bold text-capitalize"> {{translate('shop_application')}}</h3>
    <form class="__shop-apply" action="{{route('vendor.auth.registration.index')}}" id="vendor-registration-form" method="post" enctype="multipart/form-data">
        @csrf
        <div class="card __card mb-3">
            <div class="card-header">
                <h5 class="card-title m-0 fs-16 font-semi-bold d-flex align-items-center gap-2">
                    <i class="fa fa-user-o" aria-hidden="true"></i>
                    <span>{{ translate('vendor_Info') }}</span>
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <input type="text" class="form-control form-control-user" id="exampleFirstName" name="f_name" value="{{old('f_name')}}" placeholder="{{translate('first_name')}}" required>
                    </div>
                    <div class="col-sm-6">
                        <input type="text" class="form-control form-control-user" id="exampleLastName" name="l_name" value="{{old('l_name')}}" placeholder="{{translate('last_name')}}" required>
                    </div>
                    <div class="col-sm-6">
                        <input type="email" class="form-control form-control-user" id="exampleInputEmail" name="email" value="{{old('email')}}" placeholder="{{translate('email_address')}}" required>
                    </div>
                    <div class="col-sm-6">
                        <input class="form-control form-control-user phone-input-with-country-picker"
                               type="tel"  value="{{ old('phone') }}" id="exampleInputPhone"
                               placeholder="{{ translate('enter_phone_number') }}" required>
                        <div class="">
                            <input type="text" class="country-picker-phone-number w-50" name="phone" hidden readonly>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group">
                            <div class="password-toggle rtl">
                                <input class="form-control text-align-direction password-check" name="password" type="password" id="exampleInputPassword"
                                       placeholder="{{ translate('password') }}" required>
                                <label class="password-toggle-btn">
                                    <input class="custom-control-input " type="checkbox"><i
                                        class="tio-hidden password-toggle-indicator"></i><span
                                        class="sr-only">{{ translate('show_password') }} </span>
                                </label>
                            </div>
                            <small class="text-danger mx-1 password-error"></small>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group mb-0">
                            <div class="password-toggle rtl">
                                <input class="form-control text-align-direction" name="confirm_password" type="password"
                                       placeholder="{{ translate('repeat_password') }}"
                                       id="exampleRepeatPassword" required>
                                <label class="password-toggle-btn">
                                    <input class="custom-control-input text-align-direction" type="checkbox">
                                    <i class="tio-hidden password-toggle-indicator"></i>
                                    <span class="sr-only">{{ translate('show_password') }}</span>
                                </label>
                            </div>
                        </div>
                        <div class="pass invalid-feedback">{{translate('repeat_password_not_match')}} .</div>
                    </div>

                    <div class="col-sm-12">
                        <div class="text-center">
                            <img class="__img-125px object-cover" id="viewer"
                                 src="{{ getValidImage(path: 'public/assets/front-end/img/placeholder/user.png', type: 'avatar') }}" alt="banner image"/>
                        </div>
                        <div class="custom-file mt-3">
                            <input type="file" name="image" id="custom-file-upload"
                                   class="custom-file-input image-preview-before-upload"
                                   data-preview="#viewer"
                                accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                            <label class="custom-file-label overflow-hidden text-truncate" for="customFileUpload">{{translate('upload_image')}}</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card __card">
            <div class="card-header">
                <h5 class="card-title m-0 fs-16 font-semi-bold d-flex align-items-center gap-2">
                    <svg width="20" height="20" x="0" y="0" viewBox="0 0 128 128" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><g><path id="_x38_" d="m94.581 96.596c0-1.106.907-1.984 2.013-1.984s1.984.878 1.984 1.984v29.392c0 1.105-.879 2.013-1.984 2.013h-85.178c-1.105 0-1.984-.907-1.984-2.013v-50.537c0-1.106.878-1.984 1.984-1.984s2.013.878 2.013 1.984v48.552h81.152z" fill="#000000" data-original="#000000" class=""></path><path id="_x37_" d="m29.104 120.8c0 1.105-.907 2.013-2.013 2.013s-1.983-.907-1.983-2.013v-25.537c0-3.798 1.53-7.256 4.053-9.75 2.495-2.494 5.953-4.054 9.751-4.054s7.256 1.56 9.75 4.054c2.495 2.494 4.054 5.952 4.054 9.75v25.537c0 1.105-.907 2.013-2.012 2.013-1.106 0-1.985-.907-1.985-2.013v-25.537c0-2.692-1.105-5.131-2.891-6.915-1.786-1.786-4.224-2.892-6.917-2.892s-5.159 1.105-6.944 2.892c-1.758 1.784-2.863 4.223-2.863 6.915z" fill="#000000" data-original="#000000" class=""></path><path id="_x36_" d="m65.584 104.843h18.367v-13.974h-18.367zm20.38 3.997h-22.364c-1.105 0-2.013-.879-2.013-1.984v-17.999c0-1.105.907-1.984 2.013-1.984h22.364c1.105 0 2.013.879 2.013 1.984v17.998c0 1.106-.908 1.985-2.013 1.985z" fill="#000000" data-original="#000000" class=""></path><path id="_x35_" clip-rule="evenodd" d="m42.256 110.058c1.077 0 1.984-.906 1.984-1.983 0-1.105-.907-2.013-1.984-2.013-1.105 0-2.013.907-2.013 2.013 0 1.076.907 1.983 2.013 1.983z" fill-rule="evenodd" fill="#000000" data-original="#000000" class=""></path><path id="_x34_" d="m44.58 61.959v-.114l1.333-24.744h-7.683l-4.535 24.971c.028 1.587.624 3.005 1.616 4.054.963 1.021 2.324 1.644 3.826 1.644 1.475 0 2.834-.623 3.798-1.644 1.021-1.077 1.616-2.551 1.616-4.167zm5.301-24.857-1.304 24.857c0 1.616.624 3.09 1.616 4.167.964 1.021 2.324 1.644 3.826 1.644 1.474 0 2.834-.623 3.798-1.644 1.021-1.077 1.616-2.551 1.616-4.167h.028l-.681-12.471c-.057-1.105.794-2.041 1.9-2.098 1.105-.057 2.041.794 2.097 1.899l.652 12.556v.114h.028c0 1.616.624 3.09 1.616 4.167.963 1.021 2.324 1.644 3.826 1.644 1.104 0 1.983.907 1.983 2.012s-.879 2.013-1.983 2.013c-2.636 0-5.018-1.134-6.718-2.919-.255-.283-.51-.566-.737-.879-.226.313-.481.596-.736.879-1.701 1.785-4.083 2.919-6.69 2.919-2.636 0-5.017-1.134-6.718-2.919-.255-.283-.51-.566-.737-.879-.227.313-.481.596-.736.879-1.701 1.785-4.082 2.919-6.69 2.919-2.636 0-5.017-1.134-6.718-2.919-.255-.283-.51-.566-.737-.879-.227.313-.482.596-.737.879-1.701 1.785-4.082 2.919-6.689 2.919-2.636 0-5.018-1.134-6.718-2.919-.255-.283-.51-.566-.737-.879-.227.313-.482.596-.737.879-1.7 1.785-4.081 2.919-6.689 2.919-2.239 0-4.28-.822-5.896-2.154-1.616-1.331-2.807-3.23-3.289-5.413-.169-.708-.226-1.389-.169-2.069.056-.68.227-1.36.51-2.041l8.277-20.181c.85-2.098 2.239-3.798 3.94-4.988 1.729-1.191 3.77-1.843 5.981-1.843h36.565c1.105 0 2.012.907 2.012 2.013 0 1.077-.907 1.984-2.012 1.984h-5.413zm-15.675 0h-8.107l-7.284 25.084c.057 1.531.652 2.92 1.616 3.94s2.324 1.644 3.827 1.644c1.474 0 2.834-.623 3.798-1.644 1.021-1.077 1.616-2.551 1.616-4.167h.028c0-.114 0-.255.028-.369zm-12.245 0h-3.231c-1.389 0-2.665.396-3.713 1.134-1.077.765-1.956 1.842-2.522 3.231l-8.278 20.152c-.113.283-.198.567-.227.851 0 .283 0 .567.085.878.283 1.304.992 2.438 1.956 3.231.935.766 2.069 1.19 3.345 1.19 1.474 0 2.834-.623 3.798-1.644 1.021-1.077 1.616-2.551 1.616-4.167h.028c0-.199.028-.369.057-.567z" fill="#000000" data-original="#000000" class=""></path><path id="_x33_" d="m60.624 115.585c-1.105 0-2.013-.878-2.013-1.983s.908-2.013 2.013-2.013h28.316c1.104 0 1.983.907 1.983 2.013s-.879 1.983-1.983 1.983z" fill="#000000" data-original="#000000" class=""></path><path id="_x32_" d="m124.003 46.767-25.736 43.536c-.567.963-1.786 1.275-2.722.708-.312-.17-.566-.425-.736-.736l-25.71-43.508c-.028-.057-.057-.113-.085-.142-1.247-2.268-2.211-4.733-2.891-7.284-.652-2.551-.992-5.187-.992-7.908 0-8.673 3.515-16.524 9.184-22.221 5.697-5.698 13.548-9.212 22.25-9.212 8.673 0 16.525 3.514 22.223 9.211s9.212 13.549 9.212 22.222c0 2.721-.368 5.357-1.021 7.908-.681 2.607-1.673 5.073-2.948 7.369zm-27.438-34.41c5.271 0 10.034 2.126 13.492 5.583 3.458 3.458 5.584 8.22 5.584 13.492s-2.126 10.034-5.584 13.492-8.221 5.612-13.492 5.612c-5.273 0-10.063-2.154-13.521-5.612-3.43-3.458-5.583-8.22-5.583-13.492s2.153-10.034 5.583-13.492c3.459-3.457 8.248-5.583 13.521-5.583zm10.658 8.418c-2.721-2.749-6.491-4.422-10.657-4.422-4.167 0-7.937 1.673-10.687 4.422-2.721 2.721-4.422 6.491-4.422 10.657 0 4.167 1.701 7.937 4.422 10.686 2.75 2.721 6.52 4.393 10.687 4.393 4.166 0 7.937-1.672 10.657-4.393 2.722-2.749 4.423-6.519 4.423-10.686 0-4.166-1.702-7.935-4.423-10.657zm-10.658 64.596 24.008-40.645c1.105-1.984 1.956-4.138 2.551-6.406.567-2.183.879-4.479.879-6.888 0-7.567-3.09-14.427-8.049-19.387-4.962-4.96-11.821-8.049-19.389-8.049-7.597 0-14.456 3.089-19.416 8.049-4.962 4.96-8.022 11.82-8.022 19.387 0 2.409.283 4.705.85 6.888.596 2.239 1.446 4.365 2.523 6.349l.028.057z" fill="#000000" data-original="#000000" class=""></path><path id="_x31_" d="m96.565 22.278c2.495 0 4.79 1.049 6.462 2.693 1.645 1.644 2.665 3.939 2.665 6.462s-1.021 4.818-2.665 6.462c-1.672 1.672-3.938 2.693-6.462 2.693-2.522 0-4.819-1.021-6.492-2.693-.028-.028-.057-.085-.113-.113-1.586-1.644-2.55-3.883-2.55-6.349 0-2.522 1.021-4.818 2.663-6.462.057-.028.085-.085.142-.114 1.644-1.587 3.885-2.579 6.35-2.579zm3.628 5.498c-.935-.907-2.21-1.474-3.628-1.474-1.389 0-2.636.539-3.571 1.417-.028.028-.057.057-.085.085-.937.936-1.504 2.211-1.504 3.628 0 1.389.539 2.636 1.419 3.572.028.028.057.057.085.085.936.907 2.211 1.502 3.656 1.502 1.418 0 2.693-.595 3.628-1.502.936-.936 1.502-2.211 1.502-3.657 0-1.417-.566-2.692-1.502-3.628z" fill="#000000" data-original="#000000" class=""></path></g></g></svg>
                    <span>{{ translate('shop_Info') }}</span>
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6 ">
                        <input type="text" class="form-control form-control-user" id="shop_name" name="shop_name" placeholder="{{translate('shop_name')}}" value="{{old('shop_name')}}" required>
                    </div>
                    <div class="col-sm-6">
                        <textarea name="shop_address" class="form-control" id="shop_address" rows="1" placeholder="{{translate('shop_address')}}">{{old('shop_address')}}</textarea>
                    </div>
                    <div class="col-sm-6">
                        <div class="pb-3">
                            <div class="text-center">
                                <img class="__img-125px object-cover" id="viewerLogo"
                                     src="{{ getValidImage(path: 'public/assets/front-end/img/placeholder/placeholder-1-1.png', type: 'logo') }}"
                                     alt="banner image"/>
                            </div>
                        </div>

                        <div class="form-group mb-0">
                            <div class="custom-file">
                                <input type="file" name="logo" id="Logo-upload"
                                       class="custom-file-input image-preview-before-upload"
                                       data-preview="#viewerLogo"
                                    accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                <label class="custom-file-label overflow-hidden text-truncate" for="Logo-upload">{{translate('upload_logo')}}</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="pb-3">
                            <div class="text-center">
                                <img class="height-100px" id="viewerBanner"
                                     src="{{ getValidImage(path: 'public/assets/front-end/img/placeholder/placeholder-4-1.png', type: 'wide-banner') }}"
                                     alt="banner image"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="custom-file">
                                <input type="file" name="banner" id="banner-upload"
                                       class="custom-file-input overflow-hidden __p-2p image-preview-before-upload"
                                       data-preview="#viewerBanner"
                                       accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                <label class="custom-file-label overflow-hidden text-truncate" for="banner-upload">{{translate('upload_Banner')}}</label>
                            </div>
                        </div>
                    </div>

                    @php($recaptcha = getWebConfig(name: 'recaptcha'))
                    @if(isset($recaptcha) && $recaptcha['status'] == 1)
                        <div id="recaptcha_element" class="w-100" data-type="image"></div>
                        <br/>
                    @else
                    <div class="col-12">
                        <div class="row py-2">
                            <div class="col-6 pr-0">
                                <input type="text" class="form-control __h-40" name="default_recaptcha_id_seller_regi" value=""
                                       placeholder="{{translate('enter_captcha_value')}}" autocomplete="off" required>
                            </div>
                            <div class="col-6 input-icons mb-2 w-100 rounded bg-white">
                                <span class="d-flex align-items-center align-items-center get-vendor-regi-recaptcha-verify"
                                data-link="{{ route('vendor.auth.recaptcha', ['tmp'=>':dummy-id']) }}">
                                    <img src="{{ route('vendor.auth.recaptcha', ['tmp'=>1]).'?captcha_session_id=sellerRecaptchaSessionKey' }}" alt="" class="rounded __h-40" id="default_recaptcha_id">
                                    <i class="tio-refresh position-relative cursor-pointer p-2"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="col-sm-12">
                        <div class="form-group mb-0 d-flex flex-wrap justify-content-between">
                            <label class="form-group mb-3 d-flex align-items-center flex-grow-1 cursor-pointer user-select-none">
                                <strong>
                                    <input type="checkbox" class="mr-1" name="remember" id="vendor-remember-input-checked">
                                </strong>
                                <span class="mb-4px d-block w-0 flex-grow pl-1">
                                    <span>{{translate('i_agree_to_Your_terms')}}</span>
                                    <a class="font-size-sm text-force-underline text-primary" target="_blank" href="{{route('terms')}}">
                                        {{translate('terms_and_condition')}}
                                    </a>
                                </span>
                            </label>
                        </div>
                        <input type="hidden" name="from_submit" value="seller">
                        <button type="button" class="btn btn--primary btn-user btn-block font-semi-bold form-submit" id="apply" data-form-id="vendor-registration-form" disabled>
                            {{ translate('apply_Shop') }}
                        </button>
                        <div class="text-center">
                            <a class="small"  href="{{route('vendor.auth.login')}}">
                                {{ translate('already_have_an_account') }} ? {{ translate('sign_in') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<div class="modal fade registration-success-modal" tabindex="-1" aria-labelledby="toggle-modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg">
            <div class="modal-header border-0 pb-0 d-flex justify-content-end">
                <button type="button" class="btn-close border-0" data-dismiss="modal" aria-label="Close"><i class="tio-clear"></i></button>
            </div>
            <div class="modal-body px-4 px-sm-5 pt-0">
                <div class="d-flex flex-column align-items-center text-center gap-2 mb-2">
                    <img src="{{dynamicAsset(path: 'public/assets/front-end/img/congratulations.png')}}" width="70" class="mb-3 mb-20" alt="">
                    <h5 class="modal-title">{{translate('congratulations')}}</h5>
                    <div class="text-center">{{translate('your_registration_is_successful').', '.translate('please-wait_for_admin_approval').'.'.translate(' you’ll_get_a_mail_soon')}}</div>
                </div>
            </div>
        </div>
    </div>
</div>
<span id="get-confirm-and-cancel-button-text" data-sure ="{{translate('are_you_sure').'?'}}"
      data-message="{{translate('want_to_apply_as_a_vendor').'?'}}"
      data-confirm="{{translate('yes')}}" data-cancel="{{translate('no')}}"></span>
@endsection

@push('script')
@if(isset($recaptcha) && $recaptcha['status'] == 1)
    <script type="text/javascript">
        "use strict";
        var onloadCallback = function () {
            grecaptcha.render('recaptcha_element', {
                'sitekey': '{{ getWebConfig(name: 'recaptcha')['site_key'] }}'
            });
        };
    </script>
    <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit" async defer></script>
@endif

<script src="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/js/intlTelInput.js') }}"></script>
<script src="{{ theme_asset(path: 'public/assets/front-end/js/country-picker-init.js') }}"></script>
<script src="{{ theme_asset(path: 'public/assets/front-end/js/vendor-registration.js') }}"></script>
@endpush
