
@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')

@section('title', translate('privacy_policy'))

@push('css_or_js')
    <link href="{{ dynamicAsset(path: 'public/assets/back-end/plugins/summernote/summernote.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                <img src="{{dynamicAsset(path: 'public/assets/back-end/img/Pages.png')}}" width="20" alt="">
                {{translate('pages')}}
            </h2>
        </div>
        @include('admin-views.business-settings.pages-inline-menu')
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{translate('privacy_policy')}}</h5>
                    </div>
                    <form action="{{route('admin.business-settings.privacy-policy')}}" method="post">
                        @csrf
                        <div class="card-body">
                        <label for="">Mahakal</label>
                            <div class="form-group">
                                <textarea class="form-control summernote ckeditor" id="editor" name="value">{{$privacy_policy->value??''}}</textarea>
                            </div>
                            <label for="">seller</label>
                            <div class="form-group">
                                <textarea class="form-control summernote ckeditor" name="seller_value">{{$seller_privacy_policy->value??""}}</textarea>
                            </div>
                            <label for="">Tour</label>
                            <div class="form-group">
                                <textarea class="form-control summernote ckeditor" name="tour_value">{{$tour_privacy_policy->value??""}}</textarea>
                            </div>
                            <label for="">Event</label>
                            <div class="form-group">
                                <textarea class="form-control summernote ckeditor" name="event_value">{{$event_privacy_policy->value??""}}</textarea>
                            </div>
                            <label for="">Trustees</label>
                            <div class="form-group">
                                <textarea class="form-control summernote ckeditor" name="trustees_value">{{$trustees_privacy_policy->value??""}}</textarea>
                            </div>
                            @if (Helpers::modules_permission_check('Page & Media', 'Business Pages', 'privacy-policy-save'))
                            <div class="form-group">
                                <input class="form-control btn--primary" type="submit" value="{{translate('submit')}}" name="btn">
                            </div>
                            @endif

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{-- dynamicAsset(path: 'public/assets/back-end/plugins/summernote/summernote.min.js') --}}"></script>
    <script src="{{ dynamicAsset(path: 'public/js/ckeditor.js') }}"></script>
    <script>
        'use strict';
        $(document).ready(function() {
        $('.summernote').ckeditor();
    });
        // $(document).on('ready', function () {
        //     $('.summernote').summernote({
        //         'height': 150,
        //         toolbar: [
        //             ['style', ['bold', 'italic', 'underline', 'clear']],
        //             ['font', ['strikethrough', 'superscript', 'subscript']],
        //             ['fontsize', ['fontsize']],
        //             ['color', ['color']],
        //             ['para', ['ul', 'ol', 'paragraph']],
        //             ['height', ['height']],
        //         ]
        //     });
        // });
    </script>
@endpush

