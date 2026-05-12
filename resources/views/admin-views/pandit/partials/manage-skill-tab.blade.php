<div class="row">
    <div class="col-md-8">
        <div class="form-group">
            <label for="qualification" class="form-label">Qualification/Degree</label>
            <input type="text" name="qualification" class="form-control" placeholder="Your Qualification/Degree" required>
        </div>
        <div class="form-group">
            <label for="college" class="form-label">College/School/University</label>
            <input type="text" name="college" class="form-control" placeholder="Your College/School/University" required>
        </div>
        <div class="form-group">
            <label for="language_known" class="form-label">Language Known</label>
            <select name="language_known[]" id="" multiple class="form-control multi-select" required>
                <option value="hi">Hindi</option>
                <option value="en">English</option>
            </select>
        </div>
    </div>
    <div class="col-md-4 mt-3">
        <div class="text-center">
            <img class="upload-img-view" id="degree-viewer"
                src="{{ dynamicAsset(path: 'public\assets\back-end\img\400x400\img2.jpg') }}"
                alt="">
        </div>
        <div class="form-group mt-2">
            <label for="qualification_image" class="title-color">
                {{ translate('qualification_Image') }}<span class="text-danger">*</span>
            </label>
            <span class="ml-1 text-info">
                {{ THEME_RATIO[theme_root_path()]['Brand Image'] }}
            </span>
            <div class="custom-file text-left">
                <input type="file" name="qualification_image" id="degree-image"
                    class="custom-file-input image-preview-before-upload" data-preview="#degree-viewer"
                    required accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                <label class="custom-file-label" for="degree-image">
                    {{ translate('choose_file') }}
                </label>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="form-group col-6">
        <label for="category" class="form-label">Category</label>
        <select name="category[]" id="category" multiple class="form-control multi-select" required>
            @foreach ($categories as $category)
                <option value="{{$category['id']}}">{{$category['name']}}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group col-6">
        <label for="pooja" class="form-label">Pooja</label>
        <select name="pooja[]" id="pooja" multiple class="form-control multi-select" required>
        </select>
    </div>
    <div class="form-group col-6">
        <label for="experties" class="form-label">Experties</label>
        <select name="experties[]" id="" multiple class="form-control multi-select" required>
            @foreach ($experties as $expert)
                <option value="{{$expert['id']}}">{{$expert['name']}}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group col-6">
        <label for="experience" class="form-label">Experiece in years</label>
        <input type="text" name="experience" class="form-control" placeholder="Your Experience" required>
    </div>
</div>