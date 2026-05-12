@extends('layouts.back-end.app')
@section('title', translate('Edit Sangeet Details'))
@section('content')
    @push('css_or_js')
        <link href='https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/ui-lightness/jquery-ui.css' rel='stylesheet'>
    @endpush
    <div class="content container-fluid">
        <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
            <h2 class="h1 mb-0 d-flex align-items-center gap-2">
                <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/sangeet.png') }}" alt="">
                {{ translate('Edit Sangeet Details') }}
            </h2>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-3">
                    <div class="card-body text-start">
                        <form action="{{ route('admin.sangeet.update.details', $sangeetDetail->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT') <input type="hidden" name="sangeet_id"
                                value="{{ $sangeetDetail->sangeet_id }}">

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Title</label>
                                        <input type="text" name="title" class="form-control" placeholder="Enter Title"
                                            value="{{ $sangeetDetail->title }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Singer Name</label>
                                        <input type="text" name="singer_name" class="form-control"
                                            placeholder="Enter Singer Name" value="{{ $sangeetDetail->singer_name }}"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="audio"
                                            class="title-color">{{ translate('Choose Audio File') }}</label>
                                        <input type="file" name="audio" class="form-control"
                                            accept=".mp3, .wav, .ogg, .aac, .m4a">
                                        @if ($sangeetDetail->audio)
                                            <p>{{ $sangeetDetail->audio }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <input type="checkbox" name="famous" value="1" id="famous"
                                        {{ $sangeetDetail->famous ? 'checked' : '' }}>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="input-label d-flex" for="lyrics">{{ translate('Lyrics') }}</label>
                                        <textarea class="ckeditor form-control" name="lyrics" placeholder="Song Lyrics">{{ old('lyrics', $sangeetDetail->lyrics) }}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label for="image" class="title-color">{{ translate('Thumbnail') }}</label>
                                    <div class="text-center">
                                        @if ($sangeetDetail->image)
                                            <img class="upload-img-view" id="detail-viewer-0"
                                                src="{{ getValidImage(path: 'storage/app/public/sangeet-img/' . $sangeetDetail->image) }}"
                                                alt="">
                                        @endif
                                    </div>
                                    <br>
                                    <div class="form-group">
                                        <div class="custom-file text-left">
                                            <input type="file" name="image" id="thumbnail"
                                                class="custom-file-input image-preview-before-upload"
                                                data-preview="#detail-viewer-0"
                                                accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                            <label class="custom-file-label"
                                                for="thumbnail">{{ translate('Choose File') }}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label for="background_image"
                                        class="title-color">{{ translate('Background Image') }}</label>
                                    <div class="text-center">
                                        @if ($sangeetDetail->background_image)
                                            <img class="upload-img-view" id="background-preview-0"
                                                src="{{ getValidImage(path: 'storage/app/public/sangeet-background-img/' . $sangeetDetail->background_image) }}"
                                                alt="">
                                        @endif
                                    </div>
                                    <br>
                                    <div class="form-group">
                                        <div class="custom-file text-left">
                                            <input type="file" name="background_image" id="background_image"
                                                class="custom-file-input image-preview-before-upload"
                                                data-preview="#background-preview-0"
                                                accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                            <label class="custom-file-label"
                                                for="background_image">{{ translate('Choose File') }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">{{ translate('Update') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
    {{--ck editor--}}
    <script src="{{ dynamicAsset(path: 'public/js/ckeditor.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/js/sample.js') }}"></script>
    <script type="text/javascript">
   $(document).ready(function() {
       $('.ckeditor').ckeditor();
       $('.datepicker').datepicker({
           dateFormat: 'yy-mm-dd'
       });
     
</script>
@endpush
