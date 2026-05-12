@extends('layouts.back-end.app-trustees')

@section('title', translate('add_Employee'))

@section('content')

<?php

use App\Utils\Helpers;

if (auth('trust_employee')->check() && ((\App\Models\VendorRoles::where('id', auth('trust_employee')->user()->emp_role_id)->first()['name'] ?? "") == 'Sub Pandit')) {
    $relationEmployees = auth('trust_employee')->user()->relation_id;
    $roleTabs = 0;
    $PurohitsId = auth('trust_employee')->user()->purohit_id;
    $purohitsEmpId = auth('trust_employee')->user()->id;
    $OldTempleId = auth('trust_employee')->user()->temple_id;
} elseif (auth('purohit')->check()) {
    $roleTabs = 0;
    $relationEmployees = (\App\Models\Purohit::with(['temple'])->where('id', auth('purohit')->user()->id)->first()['temple']['trust_id'] ?? 0);
    $logintype = 'purohit';
    $PurohitsId = auth('purohit')->user()->id;
    $OldTempleId = auth('purohit')->user()->temple_id;
    $purohitsEmpId = 0;
} else {
    $roleTabs = 1;
    $PurohitsId = 0;
    $purohitsEmpId = 0;
    $OldTempleId = 0;
}
?>
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 d-flex gap-2">
            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/') }}" alt="">
            {{ translate('add_Employee') }}
        </h2>
    </div>
    <div class="row">
        <!-- Form for adding new artist -->
        <div class="col-md-12 mb-3">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('trustees-vendor.employee.store-employee') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="title-color" for="em_id">{{ translate('Employee_id') }}<span class="text-danger">*</span></label>
                                <input type="text" name="identify_number" class="form-control" id="identify_number" placeholder="{{ translate('Enter_Employess_id') }}" required onkeyup="checkValues(this)" data-type="identify_number">
                                <span class="text-danger identify_number_message d-none font-weight-bolder">Employee Number has already been Entered</span>
                            </div>
                            <div class="col-md-6 form-group {{ (($roleTabs == 0)?'d-none':'')}}">
                                <label class="title-color" for="role_id">{{ translate('select_Role') }}<span class="text-danger">*</span></label>
                                <select name="emp_role_id" class="form-control" id="emp_role_id" required>
                                    <option value="">Select Role</option>
                                    @if($roleList)
                                    @foreach($roleList as $val)
                                    <option value="{{$val['id']}}" {{ (($roleTabs == 0 && $val['name'] == 'Sub Pandit' ) ?'selected':'' ) }}>{{$val['name']}}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-6 form-group  {{ (($roleTabs == 0)?'d-none':'')}}">
                                <label class="title-color" for="temple_id">
                                    {{ translate('Select_Temple') }} <span class="text-danger">*</span>
                                </label>
                                <select name="temple_id" id="temple_id" class="form-control" required>
                                    <option value="">{{ translate('Select_Temple') }}</option>
                                    @foreach($templeList as $t)
                                    <option value="{{ $t->id }}" data-plan='@json(json_decode($t->package_service ?? "[]", true))' {{ (($OldTempleId == $t->id) ?'selected':'' ) }}>
                                        {{ $t->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 form-group" id="serviceContainer" style="display: none;">
                                <label class="title-color" for="name">
                                    {{ translate('Services') }} <span class="text-danger">*</span>
                                </label>
                                <div id="serviceArea" class="d-flex flex-wrap gap-3"></div>

                                {{-- Hidden input to store selected services --}}
                                <input type="hidden" name="selected_services" id="selected_services">
                            </div>
                            <div class="col-md-6 form-group {{ (($roleTabs == 0)?'d-none':'')}}" id="purohitService" style="display: none;">
                                <label class="title-color" for="name">
                                    {{ translate('Purohit_selected') }} <span class="text-danger">*</span>
                                </label>
                                <select name="purohit_id" id="purohit-id" class="form-control">
                                    <option value="" selected>{{ translate('Purohit_selected') }}</option> 
                                    @foreach($purohitsList as $p)
                                    <option value="{{ $p->id }}" data-plan='{{ $p->name }}'{{ (($p->id == $PurohitsId ) ?'selected':'' ) }}>
                                        {{ $p->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="title-color" for="name">{{ translate('name') }}<span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" id="name" placeholder="{{ translate('Enter_Name_name') }}" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="title-color" for="email">{{ translate('email') }}<span class="text-danger">*</span></label>
                                <input type="text" name="email" class="form-control" id="email" placeholder="{{ translate('Enter_email_id') }}" required onkeyup="checkValues(this)" data-type="email">
                                <span class="text-danger email_message d-none font-weight-bolder">Employee Email has already been Entered</span>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="title-color" for="phone">{{ translate('phone') }}<span class="text-danger">*</span>(+91)</label>
                                <input type="text" name="em_phone" class="form-control" placeholder="{{ translate('Enter_Phone_number') }}" required onkeyup="checkValues(this)" data-type="phone">
                                <span class="text-danger phone_message d-none font-weight-bolder">Employee phone has already been Entered</span>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="title-color" for="password">{{ translate('Enter_password') }}<span class="text-danger">*</span></label>
                                <input type="text" name="password" class="form-control" id="password" placeholder="{{ translate('Enter_Password') }}" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <div class="text-center">
                                    <img class="upload-img-view" id="detail-viewer" src="{{ getValidImage(path: 'storage/app/public/event/package/def.png', type: 'backend-product')  }}" alt="">
                                </div>
                                <div class="form-group">
                                    <label for="detail_image" class="title-color">
                                        {{ translate('employee_image') }}<span class="text-danger">*</span>
                                    </label>
                                    <span class="ml-1 text-info">
                                        {{ THEME_RATIO[theme_root_path()]['Brand Image'] }}
                                    </span>
                                    <div class="custom-file text-left">
                                        <input type="file" name="image" id="image"
                                            class="custom-file-input image-preview-before-upload" data-preview="#detail-viewer"
                                            required accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                        <label class="custom-file-label" for="detail-image">
                                            {{ translate('choose_file') }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Buttons for form actions -->
                        @if (Helpers::Employee_modules_permission('Employee', 'Add Employee', 'Add'))
                        <div class="d-flex flex-wrap gap-2 justify-content-end">
                            <button type="reset" class="btn btn-secondary">{{ translate('reset') }}</button>
                            <button type="submit" class="btn btn--primary">{{ translate('submit') }}</button>
                        </div>
                        @endif
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
    function checkValues(that) {
        var type = $(that).data('type');
        var value = $(that).val();
        $.ajax({
            url: "{{ route('trustees-vendor.employee.check-value')}}",
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                type: type,
                value: value,
                status: 0
            },
            success: function(data) {
                if (data.success == 1) {
                    $(`.${type}_message`).removeClass('d-none');
                } else {
                    $(`.${type}_message`).addClass('d-none');
                }
            }
        });
    }
</script>
<script>
    $(document).ready(function() {

        function TempleId() {
            let selected = $('#temple_id option:selected');
            if(!selected){
                return false;
            }
            let plans = selected.data('plan') || [];
            let $serviceArea = $('#serviceArea');
            let $serviceContainer = $('#serviceContainer');
            let $purohitService = $('#purohitService');

            $serviceArea.empty();

            // Hide by default
            $serviceContainer.hide();
            $purohitService.hide();

            if (plans.length > 0) {
                // Generate only active services
                let hasActive = false;
                plans.forEach(plan => {
                    if (plan.status == 1) {
                        hasActive = true;
                        $serviceArea.append(`
                                <label class="me-3">
                                    <input type="checkbox" class="service-check" value="${plan.name}" checked>
                                    ${plan.name}
                                </label>
                            `);
                    }
                });

                // Show only if there are active services
                if (hasActive) {
                    $serviceContainer.show();
                    $purohitService.show();
                    updateSelectedServices();
                } else {
                    $('#selected_services').val('');
                }
            } else {
                $('#selected_services').val('');
            }
        }
        TempleId();
        $('#temple_id').on('change', function() {
           TempleId();
        });

        // When user toggles checkboxes, update JSON
        $(document).on('change', '.service-check', function() {
            updateSelectedServices();
        });

        // Collect selected services into hidden input
        function updateSelectedServices() {
            let selected = [];
            $('.service-check:checked').each(function() {
                selected.push($(this).val());
            });
            $('#selected_services').val(JSON.stringify(selected));
        }

    });
</script>


@endpush