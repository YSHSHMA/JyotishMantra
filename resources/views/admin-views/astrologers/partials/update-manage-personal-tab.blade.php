<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="name" class="form-label">Name</label>
            <input type="text" name="name" class="form-control" placeholder="Name" value="{{ $astrologer['name'] }}"
                required>
        </div>
        <div class="form-group">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" class="form-control" placeholder="Email"
                value="{{ $astrologer['email'] }}" readonly required>
        </div>
        <div class="form-group">
            <label for="mobile_no" class="form-label">Mobile Number</label>
            <input type="number" name="mobile_no" class="form-control" placeholder="Mobile Number" readonly
                value="{{ $astrologer['mobile_no'] }}" required oninput="if(this.value.length > 10) this.value = this.value.slice(0, 10);">
        </div>
    </div>
    <div class="col-md-3">
        <div class="text-center mt-3">
            <img class="upload-img-view" id="viewer"
                src="{{!empty($astrologer['image'])?$astrologer['image']:'' }}">
        </div>
        <div class="form-group mt-2">
            <label for="image" class="title-color">
                {{ translate('Image') }}<span class="text-danger">*</span>
            </label>
            <span class="ml-1 text-info">
                {{ THEME_RATIO[theme_root_path()]['Brand Image'] }}
            </span>
            <div class="custom-file text-left">
                <input type="file" name="image" id="astrologer-image"
                    class="custom-file-input image-preview-before-upload" data-preview="#viewer"
                    accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                <label class="custom-file-label" for="astrologer-image">
                    {{ translate('choose_file') }}
                </label>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="text-center mt-3">
            <img class="upload-img-view" id="banner-viewer"
                src="{{!empty($astrologer['banner'])?url('/storage/app/public/astrologers/banner/' . $astrologer['banner']):'' }}">
        </div>
        <div class="form-group mt-2">
            <label for="banner_image" class="title-color">
                {{ translate('banner') }}<span class="text-danger">*</span>
            </label>
            <span class="ml-1 text-info">
                {{ THEME_RATIO[theme_root_path()]['Brand Image'] }}
            </span>
            <div class="custom-file text-left">
                <input type="file" name="banner_image"
                    class="custom-file-input image-preview-before-upload" data-preview="#banner-viewer" 
                    accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" required>
                <label class="custom-file-label">
                    {{ translate('choose_file') }}
                </label>
                @error('image')
                <div class="invalid-feedback d-block">{{ $message }}</div>
               @enderror
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="form-group col-md-6">
        <label for="gender" class="form-label">Gender</label>
        <select name="gender" id="" class="form-control">
            <option value="male" {{ $astrologer['gender'] == 'male' ? 'selected' : '' }}>Male</option>
            <option value="female" {{ $astrologer['gender'] == 'female' ? 'selected' : '' }}>Female</option>
        </select>
    </div>
    <div class="form-group col-md-6">
        <label for="dob" class="form-label">Birth Date</label>
        <input type="date" name="dob" class="form-control" value="{{ $astrologer['dob'] }}" required>
    </div>
    {{-- <div class="form-group col-md-6">
        <label for="pancard" class="form-label">Pancard</label>
        <input type="text" name="pancard" class="form-control" placeholder="Pancard number" value="{{$astrologer['pancard']}}" required>
    </div>
    <div class="form-group col-md-6">
        <label for="adharcard" class="form-label">Adharcard</label>
        <input type="text" name="adharcard" class="form-control" placeholder="Adharcard number" maxlength="12" value="{{$astrologer['adharcard']}}" required oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 12);">
    </div> --}}
    <div class="form-group col-md-6">
        <label for="type" class="form-label">Type</label>
        <select name="type" id="type" class="form-control" disabled>
            <option value="in house" {{$astrologer['type']=='in house'?'selected':''}}>In house</option>
            <option value="freelancer" {{$astrologer['type']=='freelancer'?'selected':''}}>Freelancer</option>
        </select>
        <input type="hidden" name="astro_type" class="form-control" value="{{$astrologer->type}}">
    </div>
    @if ($astrologer->type=='in house')
    <div class="form-group col-md-6" id="salary-div">
        <label for="salary" class="form-label">Salary</label>
        <input type="number" name="salary" class="form-control" placeholder="Salary" value="{{$astrologer->salary}}">
    </div>
    @endif
    {{-- <div class="form-group col-md-6">
        <label for="city" class="form-label">Which city do you currently
            live in?</label>
        <input type="text" name="city" class="form-control" placeholder="City" value="{{ $astrologer['city'] }}"
            required>
    </div> --}}
    {{-- <div class="form-group col-md-6">
        <label for="address" class="form-label">Your current address</label>
        <textarea name="address" id="" class="form-control" rows="2" required>{{ $astrologer['address'] }}</textarea>
    </div> --}}
    <div class="form-group col-12">
        <label for="address" class="form-label">Your current address</label>
        <input type="text" name="address" id="google-address" class="form-control getAddress_google" placeholder="Address" value="{{ $astrologer['address'] }}" required></input>
    </div>
    <input type="hidden" name="state" id="state" value="{{ $astrologer['state'] }}" class="form-control" placeholder="state">
    <input type="hidden" name="city" id="city" value="{{ $astrologer['city'] }}" class="form-control" placeholder="city">
    <input type="hidden" name="pincode" id="pincode" value="{{ $astrologer['pincode'] }}" class="form-control" placeholder="pincode">
    <input type="hidden" name="latitude" id="latitude" value="{{ $astrologer['latitude'] }}" class="form-control" placeholder="latitude">
    <input type="hidden" name="longitude" id="longitude" value="{{ $astrologer['longitude'] }}" class="form-control" placeholder="longitude">
</div>
