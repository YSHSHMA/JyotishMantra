@extends('layouts.back-end.app')

@section('title', translate('self_driving_policy'))

@section('content')
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 d-flex gap-2">
            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/') }}" alt="">
            {{ translate('self_driving_policy') }}
        </h2>
    </div>
    <div class="row">
        <!-- Form for adding new self_driving_policy -->
        <div class="col-md-12 mb-3">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.driving-policy.policy-update',[$getData['id']]) }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <!-- Language tabs -->
                        <ul class="nav nav-tabs w-fit-content mb-4">
                            @foreach($languages as $lang)
                            <li class="nav-item text-capitalize">
                                <a class="nav-link form-system-language-tab cursor-pointer {{$lang == $defaultLanguage? 'active':''}}"
                                    id="{{$lang}}-link">
                                    {{ getLanguageName($lang).'('.strtoupper($lang).')' }}
                                </a>
                            </li>
                            @endforeach
                        </ul>
                        <div class="row">
                            <div class="col-md-12">

                                <!-- Input fields for tour package name -->
                                @foreach($languages as $lang)
                                <?php
                                $translate = [];
                                if (count($getData['translations'])) {
                                    foreach ($getData['translations'] as $translations) {
                                        if ($translations->locale == $lang && $translations->key == 'title') {
                                            $translate[$lang]['title'] = $translations->value;
                                        }
                                        if ($translations->locale == $lang && $translations->key == 'policy_name') {
                                            $translate[$lang]['policy_name'] = $translations->value;
                                        }
                                        if ($translations->locale == $lang && $translations->key == 'message') {
                                            $translate[$lang]['message'] = $translations->value;
                                        }
                                    }
                                }
                                ?>
                                <div class="form-group {{$lang != $defaultLanguage ? 'd-none':''}} form-system-language-form" id="{{$lang}}-form">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="title-color" for="name">{{ translate('title') }}</label>
                                            <input type="text" name="title[]" class="form-control" value="{{ old('title.'.$loop->index,$translate[$lang]['title'] ?? $getData['title']) }}" placeholder="{{ translate('Enter_policy_title') }}" {{ $lang == $defaultLanguage ? 'required':''}}>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="title-color" for="name">{{ translate('policy_name') }}</label>
                                            <input type="text" name="policy_name[]" class="form-control" value="{{ old('policy_name.'.$loop->index,($translate[$lang]['policy_name'] ?? $getData['policy_name']))}}" placeholder="{{ translate('Enter_policy_name') }}" {{ $lang == $defaultLanguage ? 'required':''}}>
                                            <input type="hidden" name="lang[]" value="{{$lang}}" id="lang">
                                        </div>
                                        <div class="col-md-12">
                                            <label class="title-color" for="name">{{ translate('message') }}<span class="text-danger">*</span> ({{ strtoupper($lang) }})</label>
                                            <textarea name="message[]" class="form-control ckeditor" placeholder="{{ translate('message') }}" {{$lang == $defaultLanguage? 'required':''}}> {{ old('message.'.$loop->index,($translate[$lang]['message'] ?? $getData['message']))}} </textarea>
                                        </div>
                                    </div>

                                </div>

                                @endforeach
                            </div>

                        </div>
                        <!-- Buttons for form actions -->
                        <div class="d-flex flex-wrap gap-2 justify-content-end">
                            <button type="reset" class="btn btn-secondary">{{ translate('reset') }}</button>
                            <button type="submit" class="btn btn--primary">{{ translate('submit') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('script')
<!-- Include SweetAlert2 for confirmation dialogs -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="{{ dynamicAsset(path: 'public/js/ckeditor.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
@endpush