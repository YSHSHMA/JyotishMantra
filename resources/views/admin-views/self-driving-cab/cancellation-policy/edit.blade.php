@extends('layouts.back-end.app')

@section('title', translate('cancellation_policy_edit'))

@section('content')
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 d-flex gap-2">
            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/') }}" alt="">
            {{ translate('cancellation_policy_edit') }}
        </h2>
    </div>
    <div class="row">
        <!-- Form for adding new cancellation_policy_edit -->
        <div class="col-md-12 mb-3">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.driving-cancellation-policy.cancellation-update',['id'=>$getData['id']]) }}" method="post" enctype="multipart/form-data">
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
                                        if ($translations->locale == $lang && $translations->key == 'message') {
                                            $translate[$lang]['message'] = $translations->value;
                                        }
                                    }
                                }
                                ?>
                                <div class="form-group {{$lang != $defaultLanguage ? 'd-none':''}} form-system-language-form" id="{{$lang}}-form">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="title-color" for="name">{{ translate('title') }}</label>
                                            <input type="text" name="title[]" class="form-control" value="{{ old('title.'.$loop->index,$translate[$lang]['title'] ?? $getData['title'])}}" placeholder="{{ translate('Enter_policy_title') }}" {{ $lang == $defaultLanguage ? 'required':''}}>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="title-color" for="name">{{ translate('number_of_days') }}</label>
                                            <input type="text" name="percentage" class="form-control percentages" value="{{ old('percentage',$getData['percentage'])}}" onkeyup="this.value = this.value.replace(/[^0-9]/g, '');$('.percentages').val(this.value)" placeholder="{{ translate('Enter_policy_percentage') }}" {{ $lang == $defaultLanguage ? 'required':''}}>
                                            <input type="hidden" name="lang[]" value="{{$lang}}" id="lang">
                                        </div>
                                        <div class="col-md-4">
                                            <?php
                                            $totalHours = old('day', $getData['day'] ?? 0);
                                            $timeUnit = ($totalHours % 24 === 0) ? 'day' : 'hours';
                                            $timeValue = ($timeUnit === 'day') ? ($totalHours / 24) : $totalHours;
                                            ?>
                                            <label class="title-color" for="name">{{ translate('Enter_Refund_day/hours') }}</label>
                                            <select name="time_unit" class="form-control hourSelect house_options" onchange="$('.house_options').val(this.value);updateConvertedHours()">
                                                <option value="">-- Select --</option>
                                                <option value="hours" {{ old('time_unit',$timeUnit) == 'hours' ? 'selected' : '' }}>Hours</option>
                                                <option value="day" {{ old('time_unit',$timeUnit) == 'day' ? 'selected' : '' }}>Days</option>
                                            </select>
                                            <input type="number" class="form-control mt-2 user_enter_hours_days hours_days" name="time_value" onkeyup="this.value = this.value.replace(/[^0-9]/g, '');$('.hours_days').val(this.value);updateConvertedHours()" value="{{ old('time_value',$timeValue) }}" placeholder="{{ translate('Enter_Refund_day_hours') }}" {{$lang == $defaultLanguage? 'required':''}}>
                                            <input type="hidden" name="day" class="form-control convert_hours" value="{{ old('day',$getData['day'])}}">
                                        </div>
                                        <div class="col-md-12">
                                            <label class="title-color" for="name">{{ translate('message') }}<span class="text-danger">*</span> ({{ strtoupper($lang) }})</label>
                                            <textarea name="message[]" class="form-control ckeditor" placeholder="{{ translate('message') }}" {{$lang == $defaultLanguage? 'required':''}}> {{ old('message.'.$loop->index,$translate[$lang]['message'] ?? $getData['message'])}} </textarea>
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

<script>
    function updateConvertedHours() {
        const type = document.querySelector('.hourSelect').value;
        const value = parseInt(document.querySelector('.user_enter_hours_days').value);
        let totalHours = 0;
        if (!isNaN(value)) {
            if (type === 'hours') {
                totalHours = value;
            } else if (type === 'day') {
                totalHours = value * 24;
            }
        }
        $(".convert_hours").val(totalHours);
    }
</script>
@endpush