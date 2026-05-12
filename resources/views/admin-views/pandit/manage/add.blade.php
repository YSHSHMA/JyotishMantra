@extends('layouts.back-end.app')

@section('title', translate('pandit_Add'))

@section('content')
    @push('css_or_js')
    <style>
        .select2-selection__choice{
            background-color: rebeccapurple !important;
        }
    </style>
    @endpush

    <div class="content container-fluid">

        <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
            <h2 class="h1 mb-0 d-flex align-items-center gap-2">
                <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/festival.png') }}" alt="">
                {{ translate('pandit_Add') }}
            </h2>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card mb-3">
                    <div class="card-body text-start">
                        <form action="{{ route('admin.pandit.add') }}" method="post"
                            enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <div class="col-12">
                                    <ul class="nav nav-tabs mb-3" id="pills-tab" role="tablist">
                                        <li class="nav-item col-3" role="presentation">
                                            <button class="nav-link w-100 active" id="personal-tab" data-toggle="pill"
                                                data-target="#personal" type="button" role="tab"
                                                aria-controls="personal" aria-selected="true">Personal Detail</button>
                                        </li>
                                        <li class="nav-item col-3" role="presentation">
                                            <button class="nav-link w-100" id="skill-tab" data-toggle="pill"
                                                data-target="#skill" type="button" role="tab" aria-controls="skill"
                                                aria-selected="false">Skill Detail</button>
                                        </li>
                                        <li class="nav-item col-3" role="presentation">
                                            <button class="nav-link w-100" id="other-tab" data-toggle="pill"
                                                data-target="#other" type="button" role="tab" aria-controls="other"
                                                aria-selected="false">Other Detail</button>
                                        </li>
                                        <li class="nav-item col-3" role="presentation">
                                            <button class="nav-link w-100" id="availability-tab" data-toggle="pill"
                                                data-target="#availability" type="button" role="tab"
                                                aria-controls="availability" aria-selected="false">Availability</button>
                                        </li>
                                    </ul>
                                </div>

                                <div class="col-12">
                                    <div class="tab-content" id="pills-tabContent">
                                        <div class="tab-pane fade show active" id="personal" role="tabpanel"
                                            aria-labelledby="personal-tab">
                                            @include('admin-views.pandit.partials.manage-personal-tab')
                                        </div>
                                        
                                        <div class="tab-pane fade show" id="skill" role="tabpanel"
                                        aria-labelledby="skill-tab">
                                            @include('admin-views.pandit.partials.manage-skill-tab')
                                        </div>

                                        <div class="tab-pane fade show" id="other" role="tabpanel"
                                            aria-labelledby="other-tab">
                                            @include('admin-views.pandit.partials.manage-other-tab')
                                        </div>

                                        <div class="tab-pane fade show" id="availability" role="tabpanel"
                                            aria-labelledby="availability-tab">
                                            @include('admin-views.pandit.partials.manage-availability-tab')
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="d-flex gap-3 justify-content-end">
                                <button type="reset" id="reset"
                                    class="btn btn-secondary px-4">{{ translate('reset') }}</button>
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
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>
    {{-- <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script> --}}

    {{-- select 2 --}}
    <script>
        $('.multi-select').select2({
            placeholder: 'Select an option'
        });
    </script>

    {{-- category change --}}
    <script>
        $('#category').change(function (e) { 
            e.preventDefault();
            
            let id = $(this).val();
            let options = "";
            let data = {
                _token : '{{csrf_token()}}',
                id : id
            };
            $.ajax({
                type: "POST",
                url: "{{route('admin.pandit.pooja')}}",
                data: data,
                success: function (response) {
                    $('#pooja').html('');
                    if(response.status == 200){
                        if(response.pooja.length > 0){
                            $.each(response.pooja, function (key, value) { 
                                 options += `<option value"${value.id}">${value.name}</option>`;
                            });
                            $('#pooja').append(options);
                        }
                    }
                }
            });
        });
    </script>

    {{-- days --}}
    <script>
        $(document).ready(function() {
            var sundayIncrement = 1;
            var mondayIncrement = 1;
            var tuesdayIncrement = 1;
            var wednesdayIncrement = 1;
            var thursdayIncrement = 1;
            var fridayIncrement = 1;
            var saturdayIncrement = 1;

            $("#sunday-add").click(function() {
                sundayIncrement++;
                $('#sunday-dynamic-field').append(`<tr id="sunday-row${sundayIncrement}"><td><input type="time" name="sunday_from[]" class="form-control" /></td><td><input type="time" name="sunday_to[]" class="form-control" /></td><td><button type="button" name="remove" id="${sundayIncrement}" class="btn btn-danger sunday-btn-remove">X</button></td></tr>`);
            });

            $(document).on('click', '.sunday-btn-remove', function() {
                var button_id = $(this).attr("id");
                $('#sunday-row' + button_id + '').remove();
            });

            $("#monday-add").click(function() {
                mondayIncrement++;
                $('#monday-dynamic-field').append(`<tr id="monday-row${mondayIncrement}"><td><input type="time" name="monday_from[]" class="form-control" /></td><td><input type="time" name="monday_to[]" class="form-control" /></td><td><button type="button" name="remove" id="${mondayIncrement}" class="btn btn-danger monday-btn-remove">X</button></td></tr>`);
            });

            $(document).on('click', '.monday-btn-remove', function() {
                var button_id = $(this).attr("id");
                $('#monday-row' + button_id + '').remove();
            });

            $("#tuesday-add").click(function() {
                tuesdayIncrement++;
                $('#tuesday-dynamic-field').append(`<tr id="tuesday-row${tuesdayIncrement}"><td><input type="time" name="tuesday_from[]" class="form-control" /></td><td><input type="time" name="tuesday_to[]" class="form-control" /></td><td><button type="button" name="remove" id="${tuesdayIncrement}" class="btn btn-danger tuesday-btn-remove">X</button></td></tr>`);
            });

            $(document).on('click', '.tuesday-btn-remove', function() {
                var button_id = $(this).attr("id");
                $('#tuesday-row' + button_id + '').remove();
            });

            $("#wednesday-add").click(function() {
                wednesdayIncrement++;
                $('#wednesday-dynamic-field').append(`<tr id="wednesday-row${wednesdayIncrement}"><td><input type="time" name="wednesday_from[]" class="form-control" /></td><td><input type="time" name="wednesday_to[]" class="form-control" /></td><td><button type="button" name="remove" id="${wednesdayIncrement}" class="btn btn-danger wednesday-btn-remove">X</button></td></tr>`);
            });

            $(document).on('click', '.wednesday-btn-remove', function() {
                var button_id = $(this).attr("id");
                $('#wednesday-row' + button_id + '').remove();
            });

            $("#thursday-add").click(function() {
                thursdayIncrement++;
                $('#thursday-dynamic-field').append(`<tr id="thursday-row${thursdayIncrement}"><td><input type="time" name="thursday_from[]" class="form-control" /></td><td><input type="time" name="thursday_to[]" class="form-control" /></td><td><button type="button" name="remove" id="${thursdayIncrement}" class="btn btn-danger thursday-btn-remove">X</button></td></tr>`);
            });

            $(document).on('click', '.thursday-btn-remove', function() {
                var button_id = $(this).attr("id");
                $('#thursday-row' + button_id + '').remove();
            });

            $("#friday-add").click(function() {
                fridayIncrement++;
                $('#friday-dynamic-field').append(`<tr id="friday-row${fridayIncrement}"><td><input type="time" name="friday_from[]" class="form-control" /></td><td><input type="time" name="friday_to[]" class="form-control" /></td><td><button type="button" name="remove" id="${fridayIncrement}" class="btn btn-danger friday-btn-remove">X</button></td></tr>`);
            });

            $(document).on('click', '.friday-btn-remove', function() {
                var button_id = $(this).attr("id");
                $('#friday-row' + button_id + '').remove();
            });

            $("#saturday-add").click(function() {
                saturdayIncrement++;
                $('#saturday-dynamic-field').append(`<tr id="saturday-row${saturdayIncrement}"><td><input type="time" name="saturday_from[]" class="form-control" /></td><td><input type="time" name="saturday_to[]" class="form-control" /></td><td><button type="button" name="remove" id="${saturdayIncrement}" class="btn btn-danger saturday-btn-remove">X</button></td></tr>`);
            });

            $(document).on('click', '.saturday-btn-remove', function() {
                var button_id = $(this).attr("id");
                $('#saturday-row' + button_id + '').remove();
            });

        });
    </script>
@endpush
