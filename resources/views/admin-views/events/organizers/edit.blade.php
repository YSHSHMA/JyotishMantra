@php 
use App\Utils\Helpers;
@endphp
@extends('layouts.back-end.app')

@section('title', translate('Edit_Organizer'))

@section('content')
<div class="content container-fluid">
   <div class="mb-3">
      <h2 class="h1 mb-0 d-flex gap-2">
         <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/') }}" alt="">
         {{ translate('Edit_Organizer') }}
      </h2>
   </div>
   <div class="row">
      <!-- Form for adding new Edit_Organizer -->
      <div class="col-md-12 mb-3">
         <div class="card">
            <div class="card-body">
               <form action="{{ route('admin.event-managment.organizers.edit',[$getData['id']]) }}" method="post" enctype="multipart/form-data">
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
                  <div class="row">
                     <div class="col-md-12">
                        @foreach($language as $lang)
                        <?php
                                            if (count($getData['translations'])) {
                                                $translate = [];
                                                foreach ($getData['translations'] as $translations) {
                                                    if ($translations->locale == $lang && $translations->key == 'organizer_name') {
                                                        $translate[$lang]['organizer_name'] = $translations->value;
                                                    }
                                                    if ($translations->locale == $lang && $translations->key == 'organizer_address') {
                                                        $translate[$lang]['organizer_address'] = $translations->value;
                                                    }
                                                }
                                            }
                                            ?>
                        <div class="form-group {{$lang != $defaultLanguage ? 'd-none':''}} form-system-language-form" id="{{$lang}}-form">
                           <div class="row">
                              <div class="col-md-6">
                                 <label class="title-color" for="name">{{ translate('Organization_/_Individual_Name') }}<span class="text-danger">*</span> ({{ strtoupper($lang) }})</label>
                                 <input type="text" name="organizer_name[]" class="form-control"  placeholder="{{ translate('organizer_name') }}"  value="{{ $lang == $defaultLanguage ? $getData['organizer_name'] : $translate[$lang]['organizer_name'] ?? '' }}" {{$lang == $defaultLanguage? 'required':''}}>
                                 <input type="hidden" name="lang[]" value="{{$lang}}" id="lang">
                              </div>
                              <div class="col-md-6">
                                 <label class="title-color" for="pan_card">{{ translate('Organization_/_Individual_PAN_Card_Number') }}</label>
                                 <input type="text" name="organizer_pan_no" value="{{ $getData['organizer_pan_no']}}" class="form-control this_key_changeallchange" data-point='pan1' onkeyup="$(`.this_key_changeallchange[data-point='pan1']`).val(this.value)" id="pan_card" placeholder="{{ translate('Organization_/_Individual_PAN_Card_Number') }}" {{$lang == $defaultLanguage? 'required':''}}>
                              </div>
                              <div class="col-md-12 form-group">
                                 <label class="title-color" for="address">{{ translate('Organization_/_Individual_Address') }}<span class="text-danger">*</span> ({{ strtoupper($lang) }})</label>
                                 <textarea class='form-control ckeditor' name='organizer_address[]'>{{ $lang == $defaultLanguage ? $getData['organizer_address'] : $translate[$lang]['organizer_address'] ?? '' }}</textarea>
                              </div>
                           </div>
                        </div>
                        @endforeach
                        <div class="row">
                           <div class="col-md-12 form-group">
                              <label class="title-color w-100" for="address">{{ translate('do_you_have_a_GSTIN_Number') }}</label>
                              <div class='row'>
                                 <div class="col-md-6">
                                    <input type='radio' class='form-radio' name='gst_no_type' style="margin: 15px 0px 0px 24px;" value="1" onclick="$('.gst_none_show').removeClass('d-none')" {{ (($getData['gst_no_type'] == 1)?"checked":"") }}>Yes &nbsp;&nbsp;&nbsp;
                                    <input type='radio' class='' name='gst_no_type' onclick="$('.gst_none_show').addClass('d-none')" value='0' {{ (($getData['gst_no_type'] == 0)?"checked":"") }}>No
                                 </div>
                                 <div class="col-md-6">
                                    <input type='text' class="form-control gst_none_show {{ (($getData['gst_no_type'] == 0) ? 'd-none':'') }}" value="{{ (($getData['gst_no_type'] == 1) ? $getData['gst_no']:'') }}" name='gst_no' placeholder="{{ translate('Enter_GST_number') }}">
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-12">
                              <label class="title-color w-100" for="address">{{ translate('Have_you_filed_last_2_years_ITR_Return') }}</label>
                              <div class='row'>
                                 <div class="col-md-6">
                                    <input type='radio' class='form-radio' name='itr_return' style="margin: 15px 0px 0px 24px;" value="1" {{ (($getData['itr_return'] == 1)?"checked":"") }}>Yes &nbsp;&nbsp;&nbsp;<input type='radio' class='' name='itr_return' value="0" {{ (($getData['itr_return'] == 0)?"checked":"") }}>No
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="row mt-3">
                           <div class="col-md-12">
                              <label class="title-color w-100 font-weight-bold h3" for="details">{{ translate('Contact_Person_Details') }}</label>
                           </div>
                           <div class="col-md-4">
                              <label class="title-color w-100" for="full_name">{{ translate('Full_name') }}</label>
                              <input type='text' class='form-control' name='full_name' value="{{ $getData['full_name']}}" placeholder="{{ translate('Enter_Full_name') }}">
                           </div>
                           <div class="col-md-4">
                              <label class="title-color w-100" for="email_address">{{ translate('Email_Address') }}</label>
                              <input type='text' class='form-control' name='email_address' value="{{ $getData['email_address']}}" placeholder="{{ translate('Enter_Email_Address') }}">
                           </div>
                           <div class="col-md-4">
                              <label class="title-color w-100" for="address">{{ translate('Contact_number') }}</label>
                              <input type='text' class='form-control' name='contact_number' value="{{ $getData['contact_number']}}" placeholder="{{ translate('Enter_Contact_number') }}">
                           </div>
                        </div>
                        <div class="row mt-3 form-group">
                           <div class="col-md-12">
                              <label class="title-color w-100 font-weight-bold h3" for="details">{{ translate('Bank_details') }}</label>
                           </div>
                           <div class="col-md-4">
                              <label class="title-color w-100" for="Beneficiary_Name">{{ translate('Beneficiary_Name') }}</label>
                              <input type='text' class='form-control' name='beneficiary_name' value="{{ $getData['beneficiary_name']}}" placeholder="{{ translate('Beneficiary_Name') }}">
                           </div>
                           <div class="col-md-4">
                              <label class="title-color w-100" for="account_type">{{ translate('Account_type') }}</label>
                              <select class='form-control' name='account_type' placeholder="{{ translate('Account_type') }}">
                                 <option value="saving account" {{ (($getData["account_type"] == "saving account")?"selected":"")}}>saving account</option>
                                 <option value="current account" {{ (($getData["account_type"] == "current account")?"selected":"")}}>current account</option>
                              </select>
                           </div>
                           <div class="col-md-4">
                              <label class="title-color w-100" for="address">{{ translate('Bank_name') }}</label>
                              <select class='form-control' name='bank_name' placeholder="{{ translate('Bank_name') }}">
                                 <option value="State Bank of India" {{ (($getData["bank_name"] == "State Bank of India")?"selected":"") }}>State Bank of India</option>
                                 <option value="ICICI Bank" {{ (($getData["bank_name"] == "ICICI Bank")?"selected":"") }}>ICICI Bank</option>
                                 <option value="HDFC Bank" {{ (($getData["bank_name"] == "HDFC Bank")?"selected":"") }}>HDFC Bank</option>
                                 <option value="Axis Bank"{{ (($getData["bank_name"] == "Axis Bank")?"selected":"") }}>Axis Bank</option>
                                 <option value="Punjab National Bank"{{ (($getData["bank_name"] == "Punjab National Bank")?"selected":"") }}>Punjab National Bank</option>
                                 <option value="Bank of Baroda"{{ (($getData["bank_name"] == "Bank of Baroda")?"selected":"") }}>Bank of Baroda</option>
                                 <option value="Kotak Mahindra Bank"{{ (($getData["bank_name"] == "Kotak Mahindra Bank")?"selected":"") }}>Kotak Mahindra Bank</option>
                                 <option value="Canara Bank" {{ (($getData["bank_name"] == "Canara Bank")?"selected":"") }}>Canara Bank</option>
                                 <option value="Bank of India"{{ (($getData["bank_name"] == "Bank of India")?"selected":"") }}>Bank of India</option>
                                 <option value="Union Bank of India"{{ (($getData["bank_name"] == "Union Bank of India")?"selected":"") }}>Union Bank of India</option>
                                 <option value="Yes Bank"{{ (($getData["bank_name"] == "Yes Bank")?"selected":"") }}>Yes Bank</option>
                                 <option value="Indian Bank" {{ (($getData["bank_name"] == "Indian Bank")?"selected":"") }}>Indian Bank</option>
                                 <option value="Central Bank of India" {{ (($getData["bank_name"] == "Central Bank of India")?"selected":"") }}>Central Bank of India</option>
                                 <option value="IDFC FIRST Bank" {{ (($getData["bank_name"] == "IDFC FIRST Bank")?"selected":"") }}>IDFC FIRST Bank</option>
                                 <option value="Bank of Maharashtra" {{ (($getData["bank_name"] == "Bank of Maharashtra")?"selected":"") }}>Bank of Maharashtra</option>
                                 <option value="Indian Overseas Bank" {{ (($getData["bank_name"] == "Indian Overseas Bank")?"selected":"") }}>Indian Overseas Bank</option>
                                 <option value="UCO Bank" {{ (($getData["bank_name"] == "UCO Bank")?"selected":"") }}>UCO Bank</option>
                                 <option value="Oriental Bank of Commerce" {{ (($getData["bank_name"] == " ")?"selected":"") }}>Oriental Bank of Commerce</option>
                                 <option value="Syndicate Bank" {{ (($getData["bank_name"] == "Syndicate Bank")?"selected":"") }}>Syndicate Bank</option>
                                 <option value="Allahabad Bank" {{ (($getData["bank_name"] == "Allahabad Bank")?"selected":"") }}>Allahabad Bank</option>
                                 <option value="Dena Bank" {{ (($getData["bank_name"] == "Dena Bank")?"selected":"") }}>Dena Bank</option>
                                 <option value="Vijaya Bank" {{ (($getData["bank_name"] == "Vijaya Bank")?"selected":"") }}>Vijaya Bank</option>
                                 <option value="Corporation Bank" {{ (($getData["bank_name"] == "Corporation Bank")?"selected":"") }}>Corporation Bank</option>
                                 <option value="South Indian Bank" {{ (($getData["bank_name"] == "South Indian Bank")?"selected":"") }}>South Indian Bank</option>
                                 <option value="Tamilnad Mercantile Bank" {{ (($getData["bank_name"] == "Tamilnad Mercantile Bank")?"selected":"") }}>Tamilnad Mercantile Bank</option>
                                 <option value="Karnataka Bank" {{ (($getData["bank_name"] == "Karnataka Bank")?"selected":"") }}>Karnataka Bank</option>
                                 <option value="IndusInd Bank" {{ (($getData["bank_name"] == "IndusInd Bank")?"selected":"") }}>IndusInd Bank</option>
                              </select>
                           </div>
                           <div class="col-md-4">
                              <label class="title-color w-100" for="ifsc_code">{{ translate('IFSC_code') }}</label>
                              <input type='text' class='form-control' name='ifsc_code' value="{{ $getData['ifsc_code'] }}" placeholder="{{ translate('IFSC_code') }}">
                           </div>
                           <div class="col-md-4">
                              <label class="title-color w-100" for="account_no">{{ translate('Account_Number') }}</label>
                              <input type='password' class='form-control account_no' name='account_no' value="{{ $getData['account_no'] }}" placeholder="{{ translate('Account_Number') }}">
                           </div>
                           <div class="col-md-4 form-group">
                              <label class="title-color w-100" for="c_account_no">{{ translate('Confirm_Account_number') }}</label>
                              <input type='text' class='form-control c_account_no' name='c_account_no' value="{{ $getData['account_no'] }}" required placeholder="{{ translate('Account_Number') }}">
                           </div>
                        </div>

                        <div class="row mt-3 form-group">
                           <div class="col-md-12">
                              <label class="title-color w-100 font-weight-bold h3" for="upload_Documents">{{ translate('upload_Documents') }}</label>
                           </div>
                           <div class="col-md-12">
                              <p>please make sure that</p>
                              <ul>
                                 <ol>upload a clear image in .jpg or .pdf format only</ol>
                                 <ol>File Size should not be greater then 2mb</ol>
                              </ul>
                           </div>

                        </div>
                     </div>
                     <div class="col-md-12">

                        <div class="row g-2">
                           <div class="col-md-3">
                              <div class="form-group">
                                 <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                                    <div>
                                       <label for="name" class="title-color text-capitalize font-weight-bold mb-0">{{ translate('Upload_pan_card') }}</label>
                                       <span class="badge badge-soft-info">{{ THEME_RATIO[theme_root_path()]['Product Image'] }}</span>
                                       <span class="input-label-secondary cursor-pointer" data-toggle="tooltip" title="{{ translate('add_your_pan_card_in') }} JPG, PNG or JPEG {{ translate('format_within') }} 2MB">
                                          <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">
                                       </span>
                                    </div>
                                 </div>

                                 <div>
                                    <div class="custom_upload_input">
                                       <input type="file" name="pan_card_image" class="custom-upload-input-file action-upload-color-image image-input" id="" data-image-id="pre_pan_card_images_viewer" data-imgpreview="pre_pan_card_images_viewer" accept=".jpg, .webp, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">

                                       <span class="delete_file_input btn btn-outline-danger btn-sm square-btn d--none">
                                          <i class="tio-delete"></i>
                                       </span>

                                       <div class="img_area_with_preview position-absolute z-index-2">
                                          <img id="pre_pan_card_images_viewer"  src="{{ getValidImage(path: 'storage/app/public/event/organizer/'.$getData['pan_card_image'], type: 'backend-product')  }}" class="h-auto aspect-1 bg-white {{ (($getData['pan_card_image'])?'':'d-none') }}" src="" alt="">
                                       </div>
                                       <div class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                          <div class="d-flex flex-column justify-content-center align-items-center">
                                             <img alt="" class="w-75" src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}">
                                             <h3 class="text-muted">{{ translate('Upload_Image') }}</h3>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           
                           <div class="col-md-3">
                              <div class="d-flex justify-content-center">
                                 <div class="form-group w-100">
                                    <div class="d-flex align-items-center justify-content-between gap-2">
                                       <div>
                                          <label class="title-color font-weight-bold" for="upload_Cancelled Cheque">
                                             {{ translate('upload_Cancelled Cheque') }}
                                          </label>
                                          <span class="badge badge-soft-info">{{ THEME_RATIO[theme_root_path()]['Meta Thumbnail'] }}</span>
                                          <span class="input-label-secondary cursor-pointer" data-toggle="tooltip" title="{{ translate('add_upload_Cancelled_Cheque_in') }} JPG, PNG or JPEG {{ translate('format_within') }} 2MB, {{ translate('which_will_be_shown_in_search_engine_results') }}.">
                                             <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">
                                          </span>
                                       </div>

                                    </div>

                                    <div>
                                       <div class="custom_upload_input">
                                          <input type="file" name="cancelled_cheque_image" class="custom-upload-input-file action-upload-color-image image-input" id="" data-image-id="pre_cancelled_cheque_image_viewer" data-imgpreview="pre_cancelled_cheque_image_viewer" accept=".jpg, .webp, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">

                                          <span class="delete_file_input btn btn-outline-danger btn-sm square-btn d--none">
                                             <i class="tio-delete"></i>
                                          </span>
                                          <div class="img_area_with_preview position-absolute z-index-2">
                                             <img id="pre_cancelled_cheque_image_viewer" src="{{ getValidImage(path: 'storage/app/public/event/organizer/'.$getData['cancelled_cheque_image'], type: 'backend-product')  }}" class="h-auto bg-white onerror-add-class-d-none {{ (($getData['cancelled_cheque_image'])?'':'d-none')}}" alt="" src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg-dummy') }}" style="height: 100% !important;">
                                          </div>
                                          <div class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                             <div class="d-flex flex-column justify-content-center align-items-center">
                                                <img alt="" class="w-65" src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}">
                                                <h3 class="text-muted">{{ translate('Upload_Image') }}
                                                </h3>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group">
                                 <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                                    <div>
                                       <label for="name" class="title-color text-capitalize font-weight-bold mb-0">{{ translate('Upload_Aadhar_card') }}</label>
                                       <span class="badge badge-soft-info">{{ THEME_RATIO[theme_root_path()]['Product Image'] }}</span>
                                       <span class="input-label-secondary cursor-pointer" data-toggle="tooltip" title="{{ translate('add_your_Aadhar_card_in') }} JPG, PNG or JPEG {{ translate('format_within') }} 2MB">
                                          <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">
                                       </span>
                                    </div>
                                 </div>

                                 <div>
                                    <div class="custom_upload_input">
                                       <input type="file" name="aadhar_image" class="custom-upload-input-file action-upload-color-image image-input" id="" data-image-id="pre_aadhar_card_images_viewer" data-imgpreview="pre_aadhar_card_images_viewer" accept=".jpg, .webp, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">

                                       <span class="delete_file_input btn btn-outline-danger btn-sm square-btn d--none">
                                          <i class="tio-delete"></i>
                                       </span>

                                       <div class="img_area_with_preview position-absolute z-index-2">
                                          <img id="pre_aadhar_card_images_viewer" src="{{ getValidImage(path: 'storage/app/public/event/organizer/'.$getData['aadhar_image'], type: 'backend-product')  }}" class="h-auto aspect-1 bg-white {{ (($getData['aadhar_image'])?'':'d-none')}}" alt="">
                                       </div>
                                       <div class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                          <div class="d-flex flex-column justify-content-center align-items-center">
                                             <img alt="" class="w-75" src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}">
                                             <h3 class="text-muted">{{ translate('Upload_Image') }}</h3>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group">
                                 <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                                    <div>
                                       <label for="name" class="title-color text-capitalize font-weight-bold mb-0">{{ translate('Upload_organizer_image') }}</label>
                                       <span class="badge badge-soft-info">{{ THEME_RATIO[theme_root_path()]['Product Image'] }}</span>
                                       <span class="input-label-secondary cursor-pointer" data-toggle="tooltip" title="{{ translate('add_your_organizer_image') }} JPG, PNG or JPEG {{ translate('format_within') }} 2MB">
                                          <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">
                                       </span>
                                    </div>
                                 </div>

                                 <div>
                                    <div class="custom_upload_input">
                                       <input type="file" name="organizer_image" class="custom-upload-input-file action-upload-color-image image-input" id="" data-image-id="pre_organizer_images_viewer" data-imgpreview="pre_organizer_images_viewer" accept=".jpg, .webp, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">

                                       <span class="delete_file_input btn btn-outline-danger btn-sm square-btn d--none">
                                          <i class="tio-delete"></i>
                                       </span>

                                       <div class="img_area_with_preview position-absolute z-index-2">
                                          <img id="pre_organizer_images_viewer" src="{{ getValidImage(path: 'storage/app/public/event/organizer/'.$getData['image'], type: 'backend-product')  }}" class="h-auto aspect-1 bg-white {{ (($getData['image'])?'':'d-none')}}" alt="">
                                       </div>
                                       <div class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                          <div class="d-flex flex-column justify-content-center align-items-center">
                                             <img alt="" class="w-75" src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}">
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
                  <div class="d-flex flex-wrap gap-2 justify-content-end">
                     @if (Helpers::modules_permission_check('Event', 'Organizer', 'edit'))
                     <button type="reset" class="btn btn-secondary">{{ translate('reset') }}</button>
                     <button type="submit" class="btn btn--primary">{{ translate('Update') }}</button>
                  </div>
                  @endif
               </form>
            </div>
         </div>
      </div>

      <!-- Section for displaying event categiry list -->

   </div>
</div>
@endsection

@push('script')
<!-- Include SweetAlert2 for confirmation dialogs -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="{{ dynamicAsset(path: 'public/js/ckeditor.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script>
$('.image-input').on('change', function() {
    let input = this;
    let imgId = $(this).data('image-id');
    let img = document.getElementById(imgId);

    if (input.files && input.files[0]) {
        let reader = new FileReader();

        reader.onload = function(e) {
            if (img !== null) {
                img.src = e.target.result;
                $(`#${imgId}`).removeClass('d-none');
            }
            let imgName = input.files[0].name;
            if (input.closest('[data-title]')) {
                input.closest('[data-title]').setAttribute("data-title", imgName);
            }
        };

        reader.readAsDataURL(input.files[0]);
    }
});

$('.account_no, .c_account_no').on('blur', function() {
        var accountNo = $('.account_no').val();
        var confirmAccountNo = $('.c_account_no').val();
       if(accountNo && confirmAccountNo){
        if (accountNo !== confirmAccountNo) {
            toastr.error("Account numbers do not match!");
            $('.c_account_no').val('');
            $('.c_account_no').addClass('is-invalid');
        }else{
            toastr.success("Account numbers match");
            $('.c_account_no').removeClass('is-invalid');
        }
    }
    });


</script>
@endpush