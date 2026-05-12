@extends('layouts.back-end.app')

@section('title', translate('General Review'))

@section('content')
    <div class="content container-fluid">

        <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
            <h2 class="h1 mb-0 d-flex align-items-center gap-2">
                <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/Bhagwan.png') }}" alt="">
                {{ translate('Images') }}
            </h2>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card mb-3">
                    <div class="card-body text-start">
                        <form action="{{ route('admin.general.review.store') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="review_type" class="title-color">
                                            {{ translate('review_Type') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select id="review_type" name="review_type" class="form-control" required>
                                            <option value="pooja">Pooja</option>
                                            <option value="chadhava">Chadhava</option>
                                            <option value="darshan">Darshan</option>
                                            <option value="product">Product</option>
                                            <option value="tour">Tour</option>
                                            <option value="general">General</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="user_name" class="title-color">
                                            {{ translate('user_Name') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="user_name" class="form-control"
                                            placeholder="{{ translate('enter_User_Name') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="profile_image" class="title-color">
                                            {{ translate('profile_image') }}
                                        </label>
                                        <input type="file" name="image"
                                            class="custom-upload-input-file action-upload-color-image form-control"
                                            accept=".jpg, .webp, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="anonymous" class="title-color">
                                            {{ translate('anonymous') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select id="anonymous" name="anonymous" class="form-control" required>
                                            <option value="0">No</option>
                                            <option value="1">Yes</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="star_rating" class="title-color">
                                            {{ translate('star_rating') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select id="star_rating" name="star_rating" class="form-control" required>
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                            <option value="4">4</option>
                                            <option value="5">5</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="video_url" class="title-color">
                                            {{ translate('video_url') }}
                                        </label>
                                        <input type="url" name="video_url" class="form-control"
                                            placeholder="{{ translate('enter_video_url') }}">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="review_text" class="title-color">
                                            {{ translate('review_text') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <textarea name="review_text" id="" maxlength="400" class="form-control" rows="3" placeholder="{{translate('enter review text')}}" required></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex gap-3 justify-content-end">
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
@endpush
