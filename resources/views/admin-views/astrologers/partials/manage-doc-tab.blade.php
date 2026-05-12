<div class="row">
    <div class="col-md-8">
        <div class="form-group">
            <label for="adharcard" class="form-label">Aadharcard</label>
            <input type="text" name="adharcard" class="form-control" placeholder="Aadharcard number" maxlength="12"  oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 12);" id="validationCustom10"
            value="{{ old('adharcard', $astrologer->adharcard) }}" required>
            <div class="invalid-feedback">Please enter aadhar number.</div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="text-center mt-3">
                    <img class="upload-img-view" id="aadhar-front-viewer"
                         src="{{ !empty($astrologer['adharcard_front_image']) ? $astrologer['adharcard_front_image'] : dynamicAsset(path: 'public/assets/back-end/img/400x400/img2.jpg') }}" alt="">
                </div>
                <div class="form-group mt-3 text-center">
                    <label for="adhar_front_image" class="title-color">
                        {{ translate('aadhar_Front_Image') }}<span class="text-danger">*</span>
                    </label>
                    <div class="custom-file text-left">
                        <input type="file" name="adhar_front_image"
                               class="custom-file-input image-preview-before-upload" data-preview="#aadhar-front-viewer" 
                               accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" id="validationCustom11" required>
                        <label class="custom-file-label">{{ translate('choose_file') }}</label>
                        <div class="invalid-feedback">Please select aadhar image.</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="text-center mt-3">
                    <img class="upload-img-view" id="aadhar-back-viewer"
                         src="{{ !empty($astrologer['adharcard_back_image']) ? $astrologer['adharcard_back_image'] : dynamicAsset(path: 'public/assets/back-end/img/400x400/img2.jpg') }}" alt="">
                </div>
                <div class="form-group mt-3 text-center">
                    <label for="image" class="title-color">{{ translate('aadhar_Back_Image') }}</label>
                    <div class="custom-file text-left">
                        <input type="file" name="adhar_back_image"
                               class="custom-file-input image-preview-before-upload" data-preview="#aadhar-back-viewer"
                               accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                        <label class="custom-file-label">{{ translate('choose_file') }}</label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label for="pancard" class="form-label">Pancard</label>
            <input type="text" name="pancard" class="form-control" placeholder="Pancard number" id="pancard" id="validationCustom12" 
            value="{{ old('pancard', $astrologer->pancard) }}" required>
            <p class="text-danger" id="pancard-validate" style="display: none;">Pancard is invalid</p>
            <div class="invalid-feedback">Please enter pancard number.</div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="text-center mt-3">
                    <img class="upload-img-view" id="pancard-viewer"
                         src="{{ !empty($astrologer['pancard_image']) ? $astrologer['pancard_image'] : dynamicAsset(path: 'public/assets/back-end/img/400x400/img2.jpg') }}" alt="">
                </div>
                <div class="form-group mt-3 text-center">
                    <label for="image" class="title-color">{{ translate('pancard_Image') }}<span class="text-danger">*</span></label>
                    <div class="custom-file text-left">
                        <input type="file" name="pancard_image"
                               class="custom-file-input image-preview-before-upload" data-preview="#pancard-viewer" 
                               accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" id="validationCustom13" required>
                        <label class="custom-file-label" for="astrologer-image">{{ translate('choose_file') }}</label>
                        <div class="invalid-feedback">Please select pancard image.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-8">
        <div class="row">
            <div class="form-group col-md-6">
                <label for="bank_name" class="form-label">Bank Name</label>
                <input type="text" name="bank_name" class="form-control" placeholder="Enter bank name" id="validationCustom14" 
                value="{{ old('bank_name', $astrologer->bank_name) }}" required>
                <div class="invalid-feedback">Please enter bank name.</div>
            </div>
            <div class="form-group col-md-6">
                <label for="holder_name" class="form-label">Account Holder Name</label>
                <input type="text" name="holder_name" class="form-control" placeholder="Enter account holder name" id="validationCustom15" 
                value="{{ old('holder_name', $astrologer->holder_name) }}" required>
                <div class="invalid-feedback">Please enter account holder name.</div>
            </div>
            <div class="form-group col-md-6">
                <label for="branch_name" class="form-label">Branch Name</label>
                <input type="text" name="branch_name" class="form-control" placeholder="Enter branch name" id="validationCustom16" 
                value="{{ old('branch_name', $astrologer->branch_name) }}" required>
                <div class="invalid-feedback">Please enter branch name.</div>
            </div>
            <div class="form-group col-md-6">
                <label for="bank_ifsc" class="form-label">Bank IFSC</label>
                <input type="text" name="bank_ifsc" class="form-control" placeholder="Enter IFSC code" id="validationCustom17" 
                value="{{ old('bank_ifsc', $astrologer->bank_ifsc) }}" required>
                <div class="invalid-feedback">Please enter bank ifsc.</div>
            </div>
            <div class="form-group col-md-6">
                <label for="account_no" class="form-label">Bank Account No.</label>
                <input type="number" id="account-no" name="account_no" class="form-control" placeholder="Enter account no" id="validationCustom18" 
                value="{{ old('account_no', $astrologer->account_no) }}" required>
                <div class="invalid-feedback">Please enter bank account number.</div>
                <p class="text-danger" id="account-validate" style="display: none;">Account No. does not match</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="text-center mt-2">
            <img class="upload-img-view" id="bank-passbook-viewer"
                 src="{{ !empty($astrologer['bank_passbook_image']) ? $astrologer['bank_passbook_image'] : dynamicAsset(path: 'public/assets/back-end/img/400x400/img2.jpg') }}" alt="">
        </div>
        <div class="form-group mt-3 text-center">
            <label for="image" class="title-color">{{ translate('bank_Passbook_Image') }}</label>
            <div class="custom-file text-left">
                <input type="file" name="bank_passbook_image"
                       class="custom-file-input image-preview-before-upload" data-preview="#bank-passbook-viewer"
                       accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                <label class="custom-file-label" for="astrologer-image">{{ translate('choose_file') }}</label>
            </div>
        </div>
    </div>
</div>
