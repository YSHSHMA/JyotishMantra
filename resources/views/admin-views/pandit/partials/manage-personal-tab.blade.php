<div class="row">
    <div class="col-md-8">
        <div class="form-group">
            <label for="name" class="form-label">Name</label>
            <input type="text" name="name" class="form-control" placeholder="Pandit Name" required>
        </div>
        <div class="form-group">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" class="form-control" placeholder="Pandit Email" required>
        </div>
        <div class="form-group">
            <label for="password" class="form-label">Password</label>
            <input type="password" name="password" class="form-control" minlength="6" placeholder="Create a strong password" required>
        </div>
        <div class="form-group">
            <label for="mobile_no" class="form-label">Mobile Number</label>
            <input type="number" name="mobile_no" class="form-control" placeholder="Mobile Number" required>
        </div>
    </div>
    <div class="col-md-4 mt-3">
        <div class="text-center">
            <img class="upload-img-view" id="viewer"
                src="{{ dynamicAsset(path: 'public\assets\back-end\img\400x400\img2.jpg') }}"
                alt="">
        </div>
        <div class="form-group mt-2">
            <label for="image" class="title-color">
                {{ translate('pandit_Image') }}<span class="text-danger">*</span>
            </label>
            <span class="ml-1 text-info">
                {{ THEME_RATIO[theme_root_path()]['Brand Image'] }}
            </span>
            <div class="custom-file text-left">
                <input type="file" name="image" id="pandit-image"
                    class="custom-file-input image-preview-before-upload" data-preview="#viewer"
                    required accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                <label class="custom-file-label" for="pandit-image">
                    {{ translate('choose_file') }}
                </label>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="form-group col-6">
        <label for="gender" class="form-label">Gender</label>
        <select name="gender" id="" class="form-control">
            <option value="male">Male</option>
            <option value="female">Female</option>
        </select>
    </div>
    <div class="form-group col-6">
        <label for="dob" class="form-label">Birth Date</label>
        <input type="date" name="dob" class="form-control" required>
    </div>
    <div class="form-group col-6">
        <label for="maritial" class="form-label">Maritial Status</label>
        <select name="maritial" id="" class="form-control">
            <option value="married">Married</option>
            <option value="single">Single</option>
        </select>
    </div>
    <div class="form-group col-md-6">
        <label for="city" class="form-label">Where do you currently live</label>
        <input type="text" name="city" class="form-control" placeholder="City" required>
    </div>
    <div class="form-group col-md-6">
        <label for="address" class="form-label">Your current address</label>
        <textarea name="address" id="" rows="2" maxlength="200" class="form-control" placeholder="Your Address" required></textarea>
    </div>
    <div class="form-group col-md-6">
        <label for="bio" class="form-label">Your Bio</label>
        <textarea name="bio" id="" rows="2" maxlength="200" class="form-control" placeholder="Describe Bio" required></textarea>
    </div>
</div>