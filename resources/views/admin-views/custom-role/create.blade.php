@php use App\Utils\Helpers; @endphp
@php
    use Illuminate\Support\Facades\Session;
@endphp
@extends('layouts.back-end.app')
@section('title', translate('create_Role'))
@section('content')
    @php($direction = Session::get('direction'))
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2 text-capitalize">
                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/add-new-seller.png') }}" alt="">
                {{ translate('employee_role_setup') }}
            </h2>
        </div>
        <div class="card">
            <div class="card-body">
                <form id="submit-create-role" method="post" action="{{ route('admin.custom-role.store') }}"
                    class="text-start">
                    @csrf
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group mb-4">
                                <label for="name" class="title-color">{{ translate('role_name') }}</label>
                                <input type="text" name="name" class="form-control" id="name"
                                    aria-describedby="emailHelp" placeholder="{{ translate('ex') . ':' . translate('store') }}"
                                    required>
                            </div>
                        </div>
                    </div>

                    {{-- <div class="d-flex gap-4 flex-wrap">
                        <label for="name" class="title-color font-weight-bold mb-0">{{translate('module_permission')}} </label>
                        <div class="form-group d-flex gap-2">
                            <input type="checkbox" id="select-all" class="cursor-pointer">
                            <label class="title-color mb-0 cursor-pointer text-capitalize" for="select-all">{{translate('select_all')}}</label>
                        </div>
                    </div> --}}

                    {{-- <div class="row">
                        <div class="col-sm-6 col-lg-3">
                            <div class="form-group d-flex gap-2">
                                <input type="checkbox" name="modules[]" value="dashboard" class="module-permission" id="dashboard">
                                <label class="title-color mb-0" style="{{$direction === "rtl" ? 'margin-right: 1.25rem;' : ''}};"
                                        for="dashboard">{{translate('dashboard')}}</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="form-group d-flex gap-2">
                                <input type="checkbox" name="modules[]" value="pos_management" class="module-permission" id="pos_management">
                                <label class="title-color mb-0" style="{{$direction === "rtl" ? 'margin-right: 1.25rem;' : ''}};"
                                       for="pos_management">{{translate('pos_management')}}</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="form-group d-flex gap-2">
                                <input type="checkbox" class="module-permission" name="modules[]" value="order_management" id="order">
                                <label class="title-color mb-0 text-capitalize" style="{{$direction === "rtl" ? 'margin-right: 1.25rem;' : ''}};" for="order">{{translate('order_management')}}</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="form-group d-flex gap-2">
                                <input type="checkbox" class="module-permission" name="modules[]" value="product_management" id="product">
                                <label class="title-color mb-0 text-capitalize" style="{{$direction === "rtl" ? 'margin-right: 1.25rem;' : ''}};"
                                        for="product">{{translate('product_management')}}</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="form-group d-flex gap-2">
                                <input type="checkbox" class="module-permission" name="modules[]" value="promotion_management" id="promotion_management">
                                <label class="title-color mb-0 text-capitalize" style="{{$direction === "rtl" ? 'margin-right: 1.25rem;' : ''}};"
                                        for="promotion_management">{{translate('promotion_management')}}</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="form-group d-flex gap-2">
                                <input type="checkbox" name="modules[]" class="module-permission" value="support_section" id="support_section">
                                <label class="title-color mb-0 text-capitalize" style="{{$direction === "rtl" ? 'margin-right: 1.25rem;' : ''}};"
                                        for="support_section">{{translate('help_&_support_section')}}</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="form-group d-flex gap-2">
                                <input type="checkbox" class="module-permission" name="modules[]" value="report" id="report">
                                <label class="title-color mb-0 text-capitalize" style="{{$direction === "rtl" ? 'margin-right: 1.25rem;' : ''}};"
                                        for="report">{{translate('reports_&_analytics')}}</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="form-group d-flex gap-2">
                                <input type="checkbox" class="module-permission" name="modules[]" value="user_section" id="user_section">
                                <label class="title-color mb-0 text-capitalize" style="{{$direction === "rtl" ? 'margin-right: 1.25rem;' : ''}};"
                                        for="user_section">{{translate('user_management')}}</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="form-group d-flex gap-2">
                                <input type="checkbox" class="module-permission" name="modules[]" value="system_settings" id="system_settings">
                                <label class="title-color mb-0 text-capitalize" style="{{$direction === "rtl" ? 'margin-right: 1.25rem;' : ''}};"
                                        for="system_settings">{{translate('system_settings')}}</label>
                            </div>
                        </div>
                    </div> --}}

                    <div class="row">
                        <div class="col-12 mt-3 d-flex">
                            <h4>Select All</h4>
                            <div class="ml-2">
                                <input type="checkbox" name="roles" onchange="allModuleCheck(this)" class="module-permission">
                            </div>
                        </div>
                        @foreach ($permissions as $key => $permission)

                            <?php
                                $moduleName = "";
                                if(str_contains($key,' & ')){
                                    $moduleName = str_replace(' & ',' ',$key);
                                    $moduleName = str_replace(' ','-',$moduleName);
                                }
                                else{
                                    $moduleName = str_replace(' ','-',$key);
                                }
                            ?>
                            <div class="col-12 mt-3 d-flex">
                                <h4>{{ $key }}</h4>
                                <div class="ml-2">
                                    <input type="checkbox" name="{{$key}}" data-module="{{$moduleName}}" onchange="moduleCheck(this)" class="module-permission">
                                </div>
                            </div>
                            <div class="row border m-2 p-2 w-100">   
                                @foreach ($permission as $per)
                                <div class="col-3"><b>{{ $per['sub_module'] }}</b></div>
                                <div class="col-9">
                                    <div class="form-group" style="display: flex; flex-wrap: wrap;">
                                        @foreach (json_decode($per['permission'], true) as $value)
                                        <div class="ml-3 gap-2 d-flex align-center">
                                            <input type="checkbox" name="permission[{{$key}}][{{$per['sub_module']}}][]" value="{{ $value }}"
                                                class="module-permission {{$moduleName}}">
                                            <label class="title-color mb-0"
                                                style="{{ $direction === 'rtl' ? 'margin-right: 1.25rem;' : '' }};"
                                                for="dashboard">{{ translate($value) }}</label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                    @if (Helpers::modules_permission_check('Employee', 'Employee Role Setup', 'add'))
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn--primary">{{ translate('submit') }}</button>
                    </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    {{-- <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/admin/custom-role.js') }}"></script> --}}

    {{-- module check --}}
    <script>
        function moduleCheck(that){
            var moduleName = $(that).data('module');
            if ($(that).is(':checked')) {
                $('.'+moduleName).prop('checked', true);
            } else {
                $('.'+moduleName).prop('checked', false);
            }
        }
    </script>

    {{-- all module check --}}
    <script>
        function allModuleCheck(that) {
        if ($(that).is(':checked')) {
            $('input[type="checkbox"][data-module]')
                .prop('checked', true)
                .trigger('change');
        } else {
            $('input[type="checkbox"][data-module]')
                .prop('checked', false)
                .trigger('change');
        }
    }
    </script>
@endpush
