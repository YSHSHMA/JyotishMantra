<div class="row">
    <div class="col-md-6">
        <div class="form-group mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" placeholder="Enter name" required>
            @error('name')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" placeholder="Enter email" required>
            <p class="text-danger small mt-1" id="email-validate" style="display: none;">Email already registered</p>
            @error('email')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-3">
            <label for="mobile_no" class="form-label">Mobile Number</label>
            <input type="number" id="mobile-no" name="mobile_no" value="{{ old('mobile_no') }}" class="form-control @error('mobile_no') is-invalid @enderror" placeholder="Enter mobile number" required oninput="if(this.value.length > 10) this.value = this.value.slice(0, 10);">
            <p class="text-danger small mt-1" id="mobile-no-validate" style="display: none;">Mobile no already registered</p>
            @error('mobile_no')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-3">
        <div class="text-center mt-3">
            <img class="upload-img-view" id="viewer"
                src="{{ dynamicAsset(path: 'public\assets\back-end\img\400x400\img2.jpg') }}" alt="">
        </div>
        <div class="form-group mt-2">
            <label for="image" class="title-color">
                {{ translate('Image') }}<span class="text-danger">*</span>
            </label>
            <span class="ml-1 text-info">
                {{ THEME_RATIO[theme_root_path()]['Brand Image'] }}
            </span>
            <div class="custom-file text-left">
                <input type="file" name="image"
                    class="custom-file-input image-preview-before-upload" data-preview="#viewer" 
                    accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" id="validationCustom04" required>
                <label class="custom-file-label">
                    {{ translate('choose_file') }}
                </label>
                @error('image')
                <div class="invalid-feedback d-block">{{ $message }}</div>
               @enderror
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="text-center mt-3">
            <img class="upload-img-view" id="banner-viewer"
                src="{{ dynamicAsset(path: 'public\assets\back-end\img\400x400\img2.jpg') }}" alt="">
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

    <div class="form-group col-md-6 mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Enter password" required minlength="6">
        @error('password')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group col-md-6 mb-3">
        <label for="password_confirmation" class="form-label">Confirm Password</label>
        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" placeholder="Re-enter password" required minlength="6">
        @error('password_confirmation')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group col-md-6 mb-3">
        <label for="gender" class="form-label">Gender</label>
        <select name="gender" class="form-control @error('gender') is-invalid @enderror" required>
            <option value="">Select gender</option>
            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
        </select>
        @error('gender')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group col-md-6 mb-3">
        <label for="dob" class="form-label">Date of Birth</label>
        <input type="date" name="dob" id="dob" class="form-control @error('dob') is-invalid @enderror" required value="{{ old('dob') }}">
        @error('dob')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group col-md-6 mb-3">
        <label for="type" class="form-label">Type</label>
        <select name="type" id="type" class="form-control @error('type') is-invalid @enderror" required>
            <option value="">Select type</option>
            <option value="in house" {{ old('type') == 'in house' ? 'selected' : '' }}>In house</option>
            <option value="freelancer" {{ old('type') == 'freelancer' ? 'selected' : '' }}>Freelancer</option>
        </select>
        @error('type')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group col-md-6 mb-3" id="salary-div">
        <label for="salary" class="form-label">Salary</label>
        <input type="number" name="salary" id="salary-input" class="form-control @error('salary') is-invalid @enderror" placeholder="Enter salary" value="{{ old('salary') }}">
        @error('salary')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group col-12 mb-3">
        <label for="address" class="form-label">Current Address</label>
        <input type="text" name="address" id="validationCustom09" class="form-control getAddress_google @error('address') is-invalid @enderror" placeholder="Enter address" required value="{{ old('address') }}">
        @error('address')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    {{-- Hidden Google location fields --}}
    <input type="hidden" name="state" id="state" value="{{ old('state') }}">
    <input type="hidden" name="city" id="city" value="{{ old('city') }}">
    <input type="hidden" name="pincode" id="pincode" value="{{ old('pincode') }}">
    <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">
    <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">
</div>
