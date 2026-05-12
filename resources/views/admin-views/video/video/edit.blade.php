@extends('layouts.back-end.app')
@section('title', translate('video_Update'))
@section('content')
    <div class="content container-fluid">
        <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
            <h2 class="h1 mb-0 align-items-center d-flex gap-2">
                <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/video/logo.png') }}" alt="">
                {{ translate('video_Update') }}
            </h2>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body text-start">
                        <form action="{{ route('admin.video.update', [$video['id']]) }}" method="post"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <!-- Category -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="category_id" class="title-color">
                                            {{ translate('Video Category') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select name="category_id" class="form-control" id="category_id" required>
                                            <option value="">{{ translate('Select Category') }}</option>
                                            @foreach ($allVideoCategories as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ $category->id == $video->category_id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <!-- SubCategory -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="subcategory_id" class="title-color">
                                            {{ translate('Video SubCategory') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select name="subcategory_id" class="form-control" id="subcategory_id" required>
                                            <option value="">{{ translate('Select SubCategory') }}</option>
                                            @foreach ($videoSubCategories as $subcategory)
                                                <option value="{{ $subcategory->id }}"
                                                    {{ $subcategory->id == $video->subcategory_id ? 'selected' : '' }}>
                                                    {{ $subcategory->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <!-- List Type -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="list_type" class="title-color">
                                            {{ translate('List Type') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select name="list_type" class="form-control" id="list_type">
                                            <option value="">{{ translate('Video List Type') }}</option>
                                            @foreach ($videoListType as $listtype)
                                                <option value="{{ $listtype->name }}"
                                                    {{ $listtype->name == $video->list_type ? 'selected' : '' }}>
                                                    {{ $listtype->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 d-none" id="list-name-container">
                                    <div class="form-group">
                                        <label for="playlist_name" class="title-color">
                                            {{ translate('Playlist Name') }}
                                        </label>
                                        <input type="text" name="playlist_name" class="form-control" id="playlist_name"
                                            value="{{ $video->playlist_name }}"
                                            placeholder="{{ translate('Enter Playlist Name') }}">
                                    </div>
                                </div>
                            </div>

                            <div id="video-entry-wrapper">
                                <div class="video-entry">
                                    <div class="row">
                                        <div class="col-md-4">
                                            {{ translate('Title') }}
                                        </div>
                                        <div class="col-md-4">
                                            {{ translate('URL') }}
                                        </div>
                                        <div class="col-md-4">
                                            {{ translate('Thumbnail') }}
                                        </div>
                                    </div>
                                    <div class="row mt-5">
                                        @php
                                            $names = json_decode($video->title, true);
                                            $urls = json_decode($video->url, true);
                                            $images = json_decode($video->image, true);
                                            $count = count($names);
                                        @endphp
                                        @for ($i = 0; $i < $count; $i++)
                                            <div class="col-md-3">
                                                <input type="text" name="title[]" class="form-control"
                                                    value="{{ $names[$i] }}"
                                                    placeholder="{{ translate('ex') }} : {{ translate('title') }}"
                                                    required>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="url" name="url[]" class="form-control"
                                                    value="{{ $urls[$i] }}"
                                                    placeholder="{{ translate('ex') }} : {{ translate('url') }}">
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <div class="custom-file text-left">
                                                        <input type="file" name="image[]" id="image{{ $i }}"
                                                            class="custom-file-input image-preview-before-upload"
                                                            data-preview="#viewer{{ $i }}"
                                                            accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                                        <label class="custom-file-label" for="image{{ $i }}">
                                                            {{ translate('choose_file') }}
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="text-center mb-3">
                                                    @if (!empty($images[$i]))
                                                        <img style="width: 90px; height: 40px;" class="upload-img-view"
                                                            id="viewer{{ $i }}"
                                                            src="{{ getValidImage(path: 'storage/app/public/video-img/' . $images[$i], type: 'backend-video') }}"
                                                            alt="">
                                                    @else
                                                        <img style="width: 90px; height: 40px;" class="upload-img-view"
                                                            id="viewer{{ $i }}"
                                                            src="{{ asset('no-image-placeholder.jpg') }}"
                                                            alt="No image uploaded">
                                                    @endif
                                                </div>
                                            </div>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12 text-right">
                                    <button type="button" id="add-more" class="btn btn-primary">Add More</button>
                                </div>
                            </div>
                            <br>
                            <div class="d-flex justify-content-end gap-3">
                                <button type="reset" id="reset"
                                    class="btn btn-secondary px-4">{{ translate('reset') }}</button>
                                <button type="submit" class="btn btn--primary px-4">{{ translate('update') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/videos-management.js') }}"></script>
    <script src="//cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('.ckeditor').ckeditor();
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#category_id').change(function() {
                var categoryId = $(this).val();
                if (categoryId) {
                    $.ajax({
                        url: "{{ route('admin.video.subcategories') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            category_id: categoryId
                        },
                        success: function(response) {
                            $('#subcategory_id').html(response);
                        }
                    });
                } else {
                    $('#subcategory_id').html(
                        '<option value="">{{ translate('Select SubCategory') }}</option>');
                }
            });
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            var initialListType = $('#list_type').val();
            if (initialListType === 'Playlist') {
                $('#list-name-container').removeClass('d-none');
            }

            $('#list_type').change(function() {
                var listType = $(this).val();
                if (listType === 'Playlist') {
                    $('#list-name-container').removeClass('d-none');
                } else {
                    $('#list-name-container').addClass('d-none');
                }
            });
        });
    </script>
    <script>
        let entryIndex = 1;
        $("#add-more").on('click', function(e) {
            e.preventDefault();
            entryIndex++;
            const newEntry = `
             <div class="video-entry" id="video-entry-${entryIndex}">
                <div class="row">
                  <div class="col-md-3">
                    <div class="form-group">
                         <label for="name" class="title-color">
                         {{ translate('title') }}
                         <span class="text-danger">*</span>
                         </label>
                         <input type="text" name="title[]" class="form-control" id="title-${entryIndex}"
                            value=""
                            placeholder="{{ translate('ex') }} : {{ translate('title') }}" required>
                      </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                         <label for="url" class="title-color">
                         {{ translate('video (url)') }}
                         </label>
                         <input type="url" name="url[]" class="form-control" placeholder="{{ translate('ex') }} : {{ translate('url') }}">
                      </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                         <label for="image" class="title-color">
                         {{ translate('thumbnail') }}
                         </label>
                         <div class="custom-file text-left">
                            <input type="file" name="image[]" class="custom-file-input image-preview-before-upload" data-preview="#main-detail-viewer-${entryIndex}"  accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" required>
                            <label class="custom-file-label" for="image">
                            {{ translate('choose_file') }}
                            </label>
                         </div>
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="text-center mt-4">
                          <img  style="width: 90px; height: 40px;" class="upload-img-view" id="main-detail-viewer-${entryIndex}" src="{{ dynamicAsset('public/assets/back-end/img/400x400/img2.jpg') }}" alt="">
                           <button type="button" class="btn btn-danger btn-remove">X</button>
                        </div>
                  </div>
                </div>
             </div>
         `;
            $("#video-entry-wrapper").append(newEntry);
            $(".image-preview-before-upload").off('change').on('change', function() {
                readImageURL(this);
            });
        });

        $(".image-preview-before-upload").change(function() {
            readImageURL(this);
        });

        function readImageURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $(input).attr('data-preview') ? $($(input).attr('data-preview')).attr('src', e.target.result) :
                        null;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        // Remove button functionality
        $(document).on('click', '.btn-remove', function() {
            $(this).closest('.video-entry').remove();
        });
    </script>
@endpush
