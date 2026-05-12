@extends('layouts.back-end.app')
@section('title', translate('cities_gallery'))
@section('content')
<div class="content container-fluid">
    <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
        <h2 class="h1 mb-0 align-items-center d-flex gap-2">
            <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/package.png') }}" alt="">
            {{ translate('cities_gallery') }}
        </h2>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body text-start">
                    <form id="form_submit_data" method="post" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="cities_id" value="{{ $getData['id'] }}">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="additional_image_column col-md-12">
                                        <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                                            <div>
                                                <label for="name" class="title-color text-capitalize font-weight-bold mb-0">{{ translate('upload_additional_image') }}</label>
                                                <span class="badge badge-soft-info">{{ THEME_RATIO[theme_root_path()]['Product Image'] }}</span>
                                                <span class="input-label-secondary cursor-pointer" data-toggle="tooltip" title="{{ translate('upload_any_additional_images_for_this_product_from_here') }}.">
                                                    <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/info-circle.svg') }}" alt="">
                                                </span>
                                            </div>
                                        </div>
                                        <p class="text-muted">{{ translate('upload_additional_gallery_images') }}</p>
                                        <div class="coba-area">

                                            <div class="row g-2" id="additional_Image_Section">
                                                <div class="col-sm-12 col-md-4">
                                                    <div class="custom_upload_input position-relative border-dashed-2 choose_image">
                                                        <input type="file" name="images[]" onchange="filesManager(this)" class="custom-upload-input-file action-add-more-image" data-index="1" data-imgpreview="additional_Image_1" data-target-section="#additional_Image_Section" multiple accept=".jpg, .webp, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                                        <span class="delete_file_input delete_file_input_section btn btn-outline-danger btn-sm square-btn d-none">
                                                            <i class="tio-delete"></i>
                                                        </span>

                                                        <div class="img_area_with_preview position-absolute z-index-2 border-0">
                                                            <img id="additional_Image_1" class="h-auto aspect-1 bg-white d-none" alt="" src="">
                                                        </div>
                                                        <div class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                                            <div class="d-flex flex-column justify-content-center align-items-center">
                                                                <img alt="" src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}" class="w-75">
                                                                <h3 class="text-muted">
                                                                    {{ translate('Upload_Image') }}
                                                                </h3>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                @if (isset($getData['images']) && !empty($getData['images']) && json_decode($getData['images']))
                                                @foreach (json_decode($getData['images']) as $unique_id => $photo)
                                                <div class="col-sm-12 col-md-4">
                                                    <div class="custom_upload_input custom-upload-input-file-area position-relative border-dashed-2">

                                                        <a class="delete_file_input_css btn btn-outline-danger btn-sm square-btn" href="{{ route('admin.cities.delete-image', ['id' => $getData['id'], 'name' => $photo]) }}">
                                                            <i class="tio-delete"></i>
                                                        </a>
                                                        <div class="img_area_with_preview position-absolute z-index-2 border-0">
                                                            <img id="additional_Image_{{ $unique_id }}" alt="" class="h-auto aspect-1 bg-white onerror-add-class-d-none" src="{{ getValidImage(path: 'storage/app/public/cities/' . $photo, type: 'backend-product') }}">
                                                        </div>
                                                        <div class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                                            <div class="d-flex flex-column justify-content-center align-items-center">
                                                                <img alt="" src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}" class="w-75">
                                                                <h3 class="text-muted">
                                                                    {{ translate('Upload_Image') }}
                                                                </h3>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                @endforeach
                                                @endif



                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-3">

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<span id="image-path-of-product-upload-icon" data-path="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}"></span>
<span id="image-path-of-product-upload-icon-two" data-path="{{ dynamicAsset(path: 'public/assets/back-end/img/400x400/img2.jpg') }}"></span>
<span id="message-upload-image" data-text="{{ translate('upload_Image') }}"></span>
@endsection

@push('script')
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>

<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/spartan-multi-image-picker.js') }}"></script>
<script>
    var colors = 1;
    let imageCount = "{{ 15 - count(json_decode(($gallery->images??'[]'))) }}";
    $(function() {
        if (imageCount > 0) {
            $("#coba").spartanMultiImagePicker({
                fieldName: 'images[]',
                maxCount: colors === 0 ? 15 : imageCount,
                rowHeight: 'auto',
                groupClassName: 'col-6 col-md-4 col-xl-3 col-xxl-2',
                maxFileSize: '',
                placeholderImage: {
                    image: "{{ dynamicAsset(path: 'public/assets/back-end/img/400x400/img2.jpg') }}",
                    width: '100%',
                },
                dropFileLabel: "Drop Here",
                onAddRow: function(index, file) {},
                onRenderedPreview: function(index) {},
                onRemoveRow: function(index) {},
                onExtensionErr: function() {
                    toastr.error(messagePleaseOnlyInputPNGOrJPG, {
                        CloseButton: true,
                        ProgressBar: true
                    });
                },
                onSizeErr: function() {
                    toastr.error(messageFileSizeTooBig, {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            });
        }

        $("#thumbnail").spartanMultiImagePicker({
            fieldName: 'image',
            maxCount: 1,
            rowHeight: 'auto',
            groupClassName: 'col-12',
            maxFileSize: '',
            placeholderImage: {
                image: "{{ productImagePath('thumbnail') . '/' . ($gallery->thumbnail??'') ?? dynamicAsset(path: 'public/assets/back-end/img/400x400/img2.jpg') }}",
                width: '100%',
            },
            dropFileLabel: "Drop Here",
            onAddRow: function(index, file) {

            },
            onRenderedPreview: function(index) {

            },
            onRemoveRow: function(index) {

            },
            onExtensionErr: function() {
                toastr.error(messagePleaseOnlyInputPNGOrJPG, {
                    CloseButton: true,
                    ProgressBar: true
                });
            },
            onSizeErr: function() {
                toastr.error(messageFileSizeTooBig, {
                    CloseButton: true,
                    ProgressBar: true
                });
            }
        });

    });

    function removeGalleryImage(that) {
        var id = $(that).data('id');
        let getText = $('#get-confirm-and-cancel-button-text-for-delete');
        Swal.fire({
            title: getText.data('sure'),
            text: getText.data('text'),
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            cancelButtonText: getText.data('cancel'),
            confirmButtonText: getText.data('confirm'),
            reverseButtons: true
        }).then((result) => {
            if (result.value) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });
                $.ajax({
                    url: $(that).data('action'),
                    method: 'DELETE',
                    data: {},
                    success: function(data) {
                        if ($(this).data('from') === 'currency') {
                            if (parseInt(data.status) === 1) {
                                toastr.success($('#get-delete-currency-message').data('success'));
                            } else {
                                toastr.warning($('#get-delete-currency-message').data('warning'));
                            }
                        } else {
                            toastr.success($('#get-deleted-message').data('text'));
                        }
                        location.reload();
                    }
                });
            }
        })
    }


    function filesManager(that) {
        var form = $("#form_submit_data")[0];
        var formdata = new FormData(form);
        toastr.info(`Please Wait Upload Image`);
        $.ajax({
            url: '{{ route("admin.cities.gallery_add",[$getData["id"]])}}',
            data: formdata,
            dataType: "json",
            beforeSend: function () {
            $('#loading').fadeIn();
        },
            type: "post",
            contentType: false,
            processData: false,
            success: function(data) {
                if (data.success == 1) {
                    toastr.success(data.message);
                } else {
                    toastr.error(data.message);
                }
                location.reload();
            },
            complete: function () {
            $('#loading').fadeOut();
        },
            error: function(xhr, status, error) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    console.log(errors);
                    for (var key in errors) {
                        if (errors.hasOwnProperty(key)) {
                            toastr.error(errors[key][0]);
                        }
                    }
                }
                // var errorMessage = xhr.status + ': ' + xhr.statusText;
                // toastr.error('Error - ' + errorMessage);
            }
        })
    }

</script>
@endpush