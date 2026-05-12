@extends('layouts.back-end.app-event')

@section('title', translate('add_Employee'))

@section('content')
@php 
use App\Utils\Helpers;
@endphp
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
                    <form action="{{ route('event-vendor.employee.employee-update',['id'=>$old_data['id']]) }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label class="title-color" for="em_id">{{ translate('Employee_id') }}<span class="text-danger">*</span></label>
                                <input type="text" name="identify_number" class="form-control" id="identify_number" value="{{ old('identify_number',$old_data['identify_number'])}}" placeholder="{{ translate('Enter_Employess_id') }}" required onkeyup="checkValues(this)" data-type="identify_number">
                                <span class="text-danger identify_number_message d-none font-weight-bolder">Employee Number has already been Entered</span>
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="title-color" for="role_id">{{ translate('select_Role') }}<span class="text-danger">*</span></label>
                                <select name="emp_role_id" class="form-control" id="emp_role_id" required>
                                    <option value="">Select Role</option>
                                    @if($roleList)
                                    @foreach($roleList as $val)
                                    <option value="{{$val['id']}}" {{ ((old('emp_role_id',$old_data['emp_role_id']) == $val['id'] )?'selected':'' ) }}>{{$val['name']}}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="title-color" for="name">{{ translate('name') }}<span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" id="name" placeholder="{{ translate('Enter_Name_name') }}" required  value="{{ old('name',$old_data['name']) }}">
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="title-color" for="email">{{ translate('email') }}<span class="text-danger">*</span></label>
                                <input type="text" name="email" class="form-control" id="email" placeholder="{{ translate('Enter_email_id') }}" value="{{ old('email',$old_data['email']) }}" required onkeyup="checkValues(this)" data-type="email">
                                <span class="text-danger email_message d-none font-weight-bolder">Employee Email has already been Entered</span>
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="title-color" for="phone">{{ translate('phone') }}<span class="text-danger">*</span>(+91)</label>
                                <input type="text" name="em_phone" class="form-control" placeholder="{{ translate('Enter_Phone_number') }}" value="{{ old('em_phone',$old_data['phone']) }}" required onkeyup="checkValues(this)" data-type="phone">
                                <span class="text-danger phone_message d-none font-weight-bolder">Employee phone has already been Entered</span>
                            </div>                            
                            <div class="col-md-6 form-group">
                                <div class="text-center">
                                    <img class="upload-img-view" id="detail-viewer" src="{{ getValidImage(path: 'storage/app/public/event/employee/'.($old_data['image']??''), type: 'backend-product')  }}" alt="">
                                </div>
                                <div class="form-group">
                                    <label for="detail_image" class="title-color">
                                        {{ translate('employee_image') }}<span class="text-danger">*</span>
                                    </label>
                                    <span class="ml-1 text-info">
                                        {{ THEME_RATIO[theme_root_path()]['Brand Image'] }}
                                    </span>
                                    <div class="custom-file text-left">
                                        <input type="file" name="image" id="image" class="custom-file-input image-preview-before-upload" data-preview="#detail-viewer" accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                        <label class="custom-file-label" for="detail-image">
                                            {{ translate('choose_file') }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Buttons for form actions -->
                         @if (Helpers::Employee_modules_permission('Employee', 'Employee List', 'Update'))
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
            url:"{{ route('event-vendor.employee.check-value')}}",
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                type:type,
                value:value,
                status:1,
                id:"{{ $old_data['id']}}"
            },
            success:function(data){
                if(data.success == 1){
                    $(`.${type}_message`).removeClass('d-none');
                }else{
                    $(`.${type}_message`).addClass('d-none');
                }
            }
        });
    }
</script>
@endpush