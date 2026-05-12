@extends('layouts.back-end.app')

@section('title', translate('app section'))

@section('content')
<div class="content container-fluid">

    <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
        <h2 class="h1 mb-0">
            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/app.png') }}" class="mb-1 mr-1"
                alt="">
            {{ translate('update_app_section') }}
        </h2>
    </div>

    <div class="row">
        <div class="col-md-12 mb-3">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.appsection.update', [$appsection['id']]) }}"
                        method="post" enctype="multipart/form-data">
                        @csrf

                        <ul class="nav nav-tabs w-fit-content mb-4">
                            @foreach($language as $lang)
                            <li class="nav-item text-capitalize">
                                <span class="nav-link form-system-language-tab cursor-pointer {{$lang == $defaultLanguage? 'active':''}}"
                                    id="{{$lang}}-link">
                                    {{ getLanguageName($lang).' ('.strtoupper($lang).')' }}
                                </span>
                            </li>
                            @endforeach
                        </ul>

                          <div class="row mb-3">
                            <div class="col-md-8">
                                @foreach($language as $lang)
                                <?php
                                $translation = $appsection->translations->where('locale', $lang)->first();
                                ?>
                                <div class="form-group {{$lang != $defaultLanguage ? 'd-none':''}} form-system-language-form" id="{{$lang}}-form">
                                    <label class="title-color" for="name">{{ translate('Name') }} ({{strtoupper($lang)}})</label>
                                    <input type="text" name="name[]" value="{{ $lang == $defaultLanguage ? $appsection->name : ($translation ? $translation->value : '') }}" class="form-control" id="name_{{ $lang }}" placeholder="{{ translate('enter_Name') }}" {{$lang == $defaultLanguage ? 'required':''}}>
                                    <input type="hidden" name="lang[]" value="{{$lang}}">
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-3">
                            <button type="reset" class="btn btn-secondary">{{ translate('reset') }}</button>
                            <button type="submit" class="btn btn--primary">{{ translate('update') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.custom-file-input').forEach(input => {
        input.addEventListener('change', function(event) {
            const preview = this.closest('.image-container').querySelector('.upload-img-view');
            const files = event.target.files;

            if (files.length > 0) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                }
                reader.readAsDataURL(files[0]);
            }
        });
    });
});

</script>
@endpush

