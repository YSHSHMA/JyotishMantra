@extends('layouts.back-end.app')

@section('title', translate('cancellation_policy'))

@section('content')
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 d-flex gap-2">
            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/') }}" alt="">
            {{ translate('cancellation_policy') }}
        </h2>
    </div>
    <div class="row">
        <!-- Form for adding new cancellation_policy -->
        <div class="col-md-12 mb-3">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.driving-cancellation-policy.cancellation-store') }}" method="post" enctype="multipart/form-data">
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
                                <div class="form-group {{$lang != $defaultLanguage ? 'd-none':''}} form-system-language-form" id="{{$lang}}-form">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="title-color" for="name">{{ translate('title') }}</label>
                                            <input type="text" name="title[]" class="form-control" value="{{ old('title.'.$loop->index)}}" placeholder="{{ translate('Enter_policy_title') }}" {{ $lang == $defaultLanguage ? 'required':''}}>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="title-color" for="name">{{ translate('number_of_days') }}</label>
                                            <input type="text" name="percentage" class="form-control percentages" value="{{ old('percentage')}}" onkeyup="this.value = this.value.replace(/[^0-9]/g, '');$('.percentages').val(this.value)" placeholder="{{ translate('Enter_policy_percentage') }}" {{ $lang == $defaultLanguage ? 'required':''}}>
                                            <input type="hidden" name="lang[]" value="{{$lang}}" id="lang">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="title-color" for="name">{{ translate('Enter_Refund_day/hours') }}</label>
                                            <select name="time_unit" class="form-control hourSelect house_options" onchange="$('.house_options').val(this.value);updateConvertedHours()">
                                                <option value="">-- Select --</option>
                                                <option value="hours" {{ old('time_unit') == 'hours' ? 'selected' : '' }}>Hours</option>
                                                <option value="day" {{ old('time_unit') == 'day' ? 'selected' : '' }}>Days</option>
                                            </select>
                                            <input type="number" class="form-control mt-2 user_enter_hours_days hours_days" name="time_value" onkeyup="this.value = this.value.replace(/[^0-9]/g, '');$('.hours_days').val(this.value);updateConvertedHours()" value="{{ old('time_value') }}" placeholder="{{ translate('Enter_Refund_day_hours') }}" {{$lang == $defaultLanguage? 'required':''}}>
                                            <input type="hidden" name="day" class="form-control convert_hours" value="{{ old('day')}}">
                                        </div>
                                        <div class="col-md-12">
                                            <label class="title-color" for="name">{{ translate('message') }}<span class="text-danger">*</span> ({{ strtoupper($lang) }})</label>
                                            <textarea name="message[]" class="form-control ckeditor" placeholder="{{ translate('message') }}" {{$lang == $defaultLanguage? 'required':''}}> {{ old('message.'.$loop->index)}} </textarea>
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

        <!-- Section for displaying tour categiry list -->
        <div class="col-md-12">
            <div class="card">
                <!-- Table displaying tour package -->
                <div class="text-start p-4">
                    <div class="table-responsive">
                        <table id="policy_table" class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                            <thead class="thead-light thead-50 text-capitalize">
                                <tr>
                                    <th>{{ translate('SL') }}</th>
                                    <th>{{ translate('title') }}</th>
                                    <th>{{ translate('percentage') }}</th>
                                    <th>{{ translate('house') }}</th>
                                    <th>{{ translate('message') }}</th>
                                    <th>{{ translate('status') }}</th>
                                    <th>{{ translate('action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
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
    $(document).ready(function() {
        initDataTable({
            tableId: '#policy_table',
            ajaxUrl: "{{ route('admin.driving-cancellation-policy.cancellation-list-filter') }}",
            exportTitle: "policy list",
            pageLength: 25,
            columns: [{
                    data: 'id',
                    name: 'id'
                }, // serial no
                {
                    data: 'title',
                    name: 'title'
                },
                {
                    data: 'percentage',
                    name: 'percentage'
                },
                {
                    data: 'day',
                    name: 'day'
                },
                {
                    data: 'message',
                    name: 'message'
                },
                {
                    data: 'status',
                    name: 'status',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'option',
                    name: 'option',
                    orderable: false,
                    searchable: false
                },
            ],
            extraOptions: {
                serverSide: true,
            }
        });
    });

    $(document).ready(function() {
        $(document).on('change', '.toggle-switch-message', function(e) {
            e.preventDefault();

            const $checkbox = $(this);
            const form = $checkbox.closest('form');
            const isChecked = $checkbox.is(':checked');

            // Extract data
            const title = isChecked ? $checkbox.data('on-title') : $checkbox.data('off-title');
            const html = isChecked ? $checkbox.data('on-message') : $checkbox.data('off-message');

            $checkbox.prop('checked', !isChecked);

            Swal.fire({
                title: title,
                html: html,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, proceed',
                cancelButtonText: 'Cancel',
            }).then((result) => {
                if (result.value) {
                    $checkbox.prop('checked', isChecked);
                    form.submit();
                }
            });
        });
    });

    $(document).on('click', '.delete-data', function(e) {
        e.preventDefault();

        let formId = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: "This action cannot be undone.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.value) {
                $('#' + formId).submit();
            }
        });
    });

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