@extends('layouts.back-end.app')

@section('title', translate('update_Role'))

@section('content')
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 d-flex gap-10">
            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/brand-setup.png') }}" alt="">
            {{ translate('update_Role') }}
        </h2>
    </div>
    @php($direction = Session::get('direction'))
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.permission-module.role-edit') }}" method="post">
                @csrf
                <input type="hidden" name="id" value="{{ ($getData[0]['role_id'] ??'')}}">
                <div class="row">
                    <div class="col-md-4 col-sm-6 col-12">
                        <input type="text" name="role_name" class="form-control" value="{{ ($getData[0]['Role']['name'] ??'')}}" placeholder="Enter Role Name" required>
                    </div>
                </div>
                <div class="row">
                    @foreach ($groupedPermissions as $key => $permission)
                    <?php
                    $moduleName = "";
                    if (str_contains($key, ' & ')) {
                        $moduleName = str_replace(' & ', ' ', $key);
                        $moduleName = str_replace(' ', '-', $moduleName);
                    } else {
                        $moduleName = str_replace(' ', '-', $key);
                    }
                    ?>
                    <div class="col-12 mt-3">
                        <h4></h4>
                        <div class="ml-2">{{ $key }}&nbsp;
                            <input type="checkbox" name="{{$key}}" data-module="{{$moduleName}}" onchange="moduleCheck(this)" class="module-permission">
                        </div>
                    </div>
                    <div class="row border m-2 p-2 w-100">
                        @foreach ($permission as $per)
                        <div class="col-3"><b>{{ $per['sub_module'] }}</b></div>
                        <div class="col-9">
                            <div class="form-group" style="display: flex; flex-wrap: wrap;">
                                @foreach (json_decode($per['permission'], true) as $value)
                                <?php
                                $isChecked = false;
                                foreach ($getData as $role) {
                                    $rolePermissions = json_decode($role['permission'], true);
                                    if ($role['module'] == $key && $role['sub_module'] == $per['sub_module'] && in_array($value, $rolePermissions)) {
                                        $isChecked = true;
                                        break;
                                    }
                                }
                                ?>
                                <div class="ml-3 gap-2 d-flex align-center">
                                    <input type="checkbox" name="permission[{{ $key }}][{{ $per['sub_module'] }}][]" value="{{ $value }}" class="module-permission {{$moduleName}}" {{ $isChecked ? 'checked' : '' }}>
                                    <label class="title-color mb-0" style="{{ $direction === 'rtl' ? 'margin-right: 1.25rem;' : '' }};"
                                        for="dashboard">{{ translate($value) }}</label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endforeach
                </div>
                <button type="submit" class="btn btn-success my-3">Update Role</button>
            </form>


        </div>
    </div>
</div>
@endsection

@push('script')

<script>
    function moduleCheck(that) {
        var moduleName = $(that).data('module');
        if ($(that).is(':checked')) {
            $('.' + moduleName).prop('checked', true);
        } else {
            $('.' + moduleName).prop('checked', false);
        }
    }
</script>
@endpush