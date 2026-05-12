@extends('layouts.back-end.app')
@section('title', translate('sangeet_Add'))
@section('content')
    @push('css_or_js')
        {{-- datepicker --}}
        <link href='https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/ui-lightness/jquery-ui.css' rel='stylesheet'>
    @endpush
    <div class="content container-fluid">
        <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
            <h2 class="h1 mb-0 d-flex align-items-center gap-2">
                <img width="25" src="{{ dynamicAsset(path: 'public/assets/back-end/img/sangeet/logo.png') }}"
                    alt="">
                {{ translate('sangeet_Setup') }}
            </h2>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-3">
                    <div class="card-body text-start">
                        <form action="{{ route('admin.sangeet.storeDetails') }}" method="post"
                            enctype="multipart/form-data">
                            @csrf

                            <div id="sangeet-entry-wrapper">
                                <div class="sangeet-entry">
                                    <!-- Hidden field for sangeet_id -->
                                    <input type="hidden" name="sangeet_id[]" value="{{ $id }}">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Title</label>
                                                <input type="text" name="title[]" class="form-control"
                                                    placeholder="Enter Title" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Singer Name</label>
                                                <input type="text" name="singer_name[]" class="form-control"
                                                    placeholder="Enter Singer Name" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="audio"
                                                    class="title-color">{{ translate('Choose Audio File') }}<span
                                                        class="text-danger">*</span></label>
                                                <input type="file" name="audio[]" class="form-control" accept="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group">
                                            <input type="checkbox" name="famous" value="1" id="famous">
                                            <label for="famous">If you click this check box, this song will also be added
                                                to the famous category.</label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="input-label d-flex"
                                                    for="module_type">{{ translate('lyrics') }} </label>
                                                <textarea class="ckeditor form-control" name="lyrics[]" placeholder="Song Lyrics"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="detail_image" class="title-color">{{ translate('thumbnail') }}<span
                                                    class="text-danger">*</span></label>
                                            <div class="text-center">
                                                <img class="upload-img-view" id="detail-viewer-0"
                                                    src="{{ dynamicAsset(path: 'public/assets/back-end/img/400x400/img2.jpg') }}"
                                                    alt="">
                                            </div><br>
                                            <div class="form-group">
                                                <div class="custom-file text-left">
                                                    <input type="file" name="image[]" id="image-0"
                                                        class="custom-file-input image-preview-before-upload"
                                                        data-preview="#detail-viewer-0" required
                                                        accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                                    <label class="custom-file-label"
                                                        for="detail-image">{{ translate('choose_file') }}</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="background_image"
                                                class="title-color">{{ translate('background') }}<span
                                                    class="text-danger">*</span></label>
                                            <div class="text-center">
                                                <img class="upload-img-view" id="background-preview-0"
                                                    src="{{ dynamicAsset(path: 'public/assets/back-end/img/400x400/img2.jpg') }}"
                                                    alt="">
                                            </div><br>
                                            <div class="form-group">
                                                <div class="custom-file text-left">
                                                    <input type="file" name="background_image[]" id="background_image-0"
                                                        class="custom-file-input image-preview-before-upload"
                                                        data-preview="#background-preview-0" required
                                                        accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                                    <label class="custom-file-label"
                                                        for="background_image-0">{{ translate('choose_file') }}</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12 text-right">
                            <button type="button" id="add-more" class="btn btn-primary">Add More</button>
                        </div>
                    </div>
                    <br>
                    <div class="d-flex gap-3 justify-content-end">
                        <button type="reset" id="reset"
                            class="btn btn-secondary px-4">{{ translate('reset') }}</button>
                        <button type="submit" class="btn btn--primary px-4">{{ translate('submit') }}</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
@push('script')
    {{-- datepicker --}}
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
    <script src="//cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            // Initialize CKEditor for existing textareas
            $('.ckeditor').each(function() {
                CKEDITOR.replace($(this).attr('id'));
            });

            // Add more fields
            let entryIndex = 1;
            $("#add-more").on('click', function(e) {
                e.preventDefault();
                entryIndex++;
                const newEntry = `
            <div class="sangeet-entry" id="sangeet-entry-${entryIndex}">
            <!-- Hidden field for sangeet_id -->
                  <input type="hidden" name="sangeet_id[]" value="{{ $id }}">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" name="title[]" class="form-control" placeholder="Enter Title" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                     <div class="form-group">
                              <label>Singer Name</label>
                              <input type="text" name="singer_name[]" class="form-control" placeholder="Enter Singer Name" required>
                          </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                              <label for="audio" class="title-color">{{ translate('Choose Audio File') }}<span class="text-danger">*</span></label>
                              <input type="file" name="audio[]" class="form-control" accept=".mp3, .wav, .ogg, .aac, .m4a">
                          </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                    <input type="checkbox" name="famous[]" value="1" id="famous">
                    <label for="famous">If you click this check box, this song will also be added to the famous category.</label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="input-label d-flex" for="module_type">{{ translate('lyrics') }} </label>
                            <textarea class="ckeditor form-control" name="lyrics[]" id="lyrics-${entryIndex}" placeholder="Song Lyrics"></textarea>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="detail_image" class="title-color">{{ translate('thumbnail') }}<span class="text-danger">*</span></label>
                        <div class="text-center">
                            <img class="upload-img-view" id="detail-viewer-${entryIndex}"
                            src="{{ dynamicAsset(path: 'public/assets/back-end/img/400x400/img2.jpg') }}"
                            alt="">
                        </div>
                        <div class="form-group">
                            <div class="custom-file text-left">
                                <input type="file" name="image[]" id="image-${entryIndex}" class="custom-file-input image-preview-before-upload" data-preview="#detail-viewer-${entryIndex}" required accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                <label class="custom-file-label" for="image-${entryIndex}">{{ translate('choose_file') }}</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="background_image-${entryIndex}" class="title-color">{{ translate('background') }}<span class="text-danger">*</span></label>
                        <div class="text-center">
                            <img class="upload-img-view" id="background-preview-${entryIndex}"
                            src="{{ dynamicAsset(path: 'public/assets/back-end/img/400x400/img2.jpg') }}"
                            alt="">
                        </div>
                        <div class="form-group">
                            <div class="custom-file text-left">
                                <input type="file" name="background_image[]" id="background_image-${entryIndex}" class="custom-file-input image-preview-before-upload" data-preview="#background-preview-${entryIndex}" required accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                <label class="custom-file-label" for="background_image-${entryIndex}">{{ translate('choose_file') }}</label>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-danger remove-entry" data-target="#sangeet-entry-${entryIndex}">X</button>
            </div>`;
                $("#sangeet-entry-wrapper").append(newEntry);

                // Reinitialize CKEditor for the new textarea
                CKEDITOR.replace(`lyrics-${entryIndex}`);

                // Reinitialize the image preview functionality
                initializeImagePreview();
            });

            // Remove entry
            $(document).on('click', '.remove-entry', function() {
                var target = $(this).data('target');
                $(target).remove();
            });

            // Image preview
            function initializeImagePreview() {
                $('.image-preview-before-upload').off('change').on('change', function() {
                    var input = this;
                    var target = $(input).data('preview');
                    if (input.files && input.files[0]) {
                        var reader = new FileReader();
                        reader.onload = function(e) {
                            $(target).attr('src', e.target.result);
                        }
                        reader.readAsDataURL(input.files[0]);
                    }
                });
            }

            // Initialize image preview for existing inputs
            initializeImagePreview();
        });
    </script>
@endpush
