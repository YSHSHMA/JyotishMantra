@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')

@section('title', translate('package_Update'))

@section('content')
    <div class="content container-fluid">

        <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
            <h2 class="h1 mb-0 align-items-center d-flex gap-2">
                <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/package.png') }}" alt="">
                {{ translate('package_Update') }}
            </h2>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body text-start">
                        <form action="{{ route('admin.package.update', [$package['id']]) }}" method="post"
                            enctype="multipart/form-data">
                            @csrf
                            {{-- <input type="hidden" name="service_id" value="{{ $package['service_id'] }}"> --}}
                            <ul class="nav nav-tabs w-fit-content mb-4">
                                @foreach ($languages as $lang)
                                    <li class="nav-item text-capitalize">
                                        <span
                                            class="nav-link form-system-language-tab cursor-pointer {{ $lang == $defaultLanguage ? 'active' : '' }}"
                                            id="{{ $lang }}-link">
                                            {{ getLanguageName($lang) . '(' . strtoupper($lang) . ')' }}
                                        </span>
                                    </li>
                                @endforeach
                            </ul>

                            <div class="row">
                                <div class="col-md-12">
                                    <div>
                                        @foreach ($languages as $lang)
                                            <?php
                                            if (count($package['translations'])) {
                                                $translate = [];
                                                foreach ($package['translations'] as $translations) {
                                                    if ($translations->locale == $lang && $translations->key == 'title') {
                                                        $translate[$lang]['title'] = $translations->value;
                                                    }
                                                    if ($translations->locale == $lang && $translations->key == 'description') {
                                                        $translate[$lang]['description'] = $translations->value;
                                                    }
                                                }
                                            }
                                            ?>
                                            <div class="{{ $lang != $defaultLanguage ? 'd-none' : '' }} form-system-language-form"
                                                id="{{ $lang }}-form">
                                                <div class="form-group">
                                                    <label class="title-color" for="title">{{ translate('title') }}
                                                        ({{ strtoupper($lang) }})
                                                    </label>
                                                    <input type="text" name="title[]"
                                                        value="{{ $lang == $defaultLanguage ? $package['title'] : $translate[$lang]['title'] ?? '' }}"
                                                        class="form-control" id="title"
                                                        placeholder="{{ translate('ex') }} : {{ translate('Title') }}"
                                                        {{ $lang == $defaultLanguage ? 'required' : '' }}>
                                                </div>
                                                <div class="form-group">
                                                    <label class="title-color"
                                                        for="description">{{ translate('description') }}
                                                        ({{ strtoupper($lang) }})</label>
                                                    <textarea name="description[]" class="form-control ckeditor" id="description"
                                                        {{ $lang == $defaultLanguage ? 'required' : '' }}>{!! $lang == $defaultLanguage ? $package['description'] : $translate[$lang]['description'] ?? '' !!}</textarea>
                                                </div>
                                            </div>
                                            <input type="hidden" name="lang[]" value="{{ $lang }}">
                                        @endforeach
                                        <input name="position" value="0" class="d-none">
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label class="title-color" for="person">{{ translate('person') }}</label>
                                                <input type="number" name="person" id="" class="form-control"
                                                    value="{{ isset($package['person']) ? $package['person'] : '' }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label class="title-color" for="type">{{ translate('package_type') }}</label>
                                                <select name="type"  id="package_type" class="form-control" required>
                                                    <option value="">Select Package</option>
                                                    <option value="pooja" {{ !empty($package['type']) && $package['type'] == 'pooja' ? 'selected' : '' }}>Pooja</option>
                                                    <option value="vippooja" {{ !empty($package['type']) && $package['type'] == 'vippooja' ? 'selected' : '' }}>VIP Pooja</option>
                                                    <option value="anushthan" {{ !empty($package['type']) && $package['type'] == 'anushthan' ? 'selected' : '' }}>Anushthan</option>
                                                    <option value="offlinepooja" {{ !empty($package['type']) && $package['type'] == 'offlinepooja' ? 'selected' : '' }}>Offline Pooja</option>
                                                    <option value="panditpooja" {{ !empty($package['type']) && $package['type'] == 'panditpooja' ? 'selected' : '' }}>Pandit Pooja</option>
                                                </select>
                                                <span>{{ translate('Note: If the package type is Pandit Pooja, then selecting an Astrologer is mandatory.') }}</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 d-none" id="panditDiv">
                                            <div class="form-group">
                                                <label class="title-color">{{ translate('Astrologer_list') }}</label>
                                                <select name="pandit_id" class="form-control">
                                                    <option value="">Select Astrologer</option>
                                                    @foreach($pandit as $nameList)
                                                        <option value="{{ $nameList->id }}"
                                                            {{ old('pandit_id', $package->pandit_id ?? '') == $nameList->id ? 'selected' : '' }}>
                                                            {{ $nameList->name }} ({{ $nameList->type }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label class="title-color" for="price">{{ translate('color') }}</label>
                                                <input type="color" name="color" class="form-control"
                                                    placeholder="{{ translate('color') }}"
                                                    value="{{ isset($package['color']) ? $package['color'] : '' }}"
                                                    required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-3">
                                @if (Helpers::modules_permission_check('Pooja Managment', 'Pooja Package', 'edit'))
                                <button type="reset" id="reset"
                                    class="btn btn-secondary px-4">{{ translate('reset') }}</button>
                                <button type="submit" class="btn btn--primary px-4">{{ translate('update') }}</button>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/js/ckeditor.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('.ckeditor').ckeditor();
        });
    </script>
    <script>
        $(document).ready(function () {
            $('#package_type').trigger('change');
        });
    </script>
<script>
    $(document).ready(function () {

        $('#package_type').on('change', function () {
            let type = $(this).val();

            if (type === 'panditpooja') {
                $('#panditDiv').removeClass('d-none');
                $('#panditDiv select').prop('required', true);
            } else {
                $('#panditDiv').addClass('d-none');
                $('#panditDiv select').prop('required', false).val('');
            }
        });

    });
</script>
@endpush
