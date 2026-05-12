@extends('layouts.back-end.app')

@section('title', translate('Role_list'))
@push('css_or_js')
<style>

</style>
@endpush
@section('content')
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 d-flex gap-2">
            {{ translate('Role_list') }}
            <span class="badge badge-soft-dark radius-50 fz-14">{{ $groupedPermissions->total() }}</span>
        </h2>
    </div>
    <div class="row mt-20">
        <div class="col-md-12">
            <div class="card">
                <div class="px-3 py-4">
                    <div class="row g-2 flex-grow-1">
                        <div class="col-sm-8 col-md-6 col-lg-4">
                            <form action="{{ url()->current() }}" method="GET">
                                <div class="input-group input-group-custom input-group-merge">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="tio-search"></i>
                                        </div>
                                    </div>
                                    <input id="datatableSearch_" type="search" name="searchValue" class="form-control" placeholder="{{ translate('search_by_name') }}" aria-label="{{ translate('search_by_name') }}" value="{{ request('searchValue') }}" required>
                                    <button type="submit" class="btn btn--primary input-group-text">{{ translate('search') }}</button>
                                </div>
                            </form>
                        </div>
                        <div class="col-sm-4 col-md-6 col-lg-8 d-flex justify-content-end">
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100 text-start">
                            <thead class="thead-light thead-50 text-capitalize">
                                <tr>
                                    <th>{{ translate('SL') }}</th>
                                    <th>{{ translate('type') }}</th>
                                    <th>{{ translate('role_name') }}</th>
                                    <th>{{ translate('status') }}</th>
                                    <th class="text-center"> {{ translate('option') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($groupedPermissions)
                                @foreach($groupedPermissions as $key=>$val)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ ucwords($val['type']) }}</td>
                                    <td>{{ ucwords($val['name']) }}</td>
                                    <td>
                                        <form action="{{ route('admin.permission-module.vendor-role-status') }}" method="post"
                                            id="employee-role-status{{ $val['id'] }}-form">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $val['id'] }}">
                                            <label class="switcher" for="employee-role-status{{ $val['id'] }}">
                                                <input type="checkbox" class="switcher_input toggle-switch-message"
                                                    id="employee-role-status{{ $val['id'] }}" name="status"
                                                    value="1" {{ $val['status'] == 1 ? 'checked' : '' }}
                                                    data-modal-id="toggle-status-modal"
                                                    data-toggle-id="employee-role-status{{ $val['id'] }}"
                                                    data-on-image="employee-on.png" data-off-image="employee-off.png"
                                                    data-on-title="{{ translate('want_to_Turn_ON_Employee_Status') . '?' }}"
                                                    data-off-title="{{ translate('want_to_Turn_OFF_Employee_Status') . '?' }}"
                                                    data-on-message="<p>{{ translate('when_the_status_is_enabled_employees_can_access_the_system_to_perform_their_responsibilities') }}</p>"
                                                    data-off-message="<p>{{ translate('when_the_status_is_disabled_employees_cannot_access_the_system_to_perform_their_responsibilities') }}</p>">
                                                <span class="switcher_control"></span>
                                            </label>
                                        </form>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2 justify-content-center">
                                            <a href="{{ route('admin.permission-module.role-update', [$val['id']]) }}"
                                                class="btn btn-outline--primary btn-sm square-btn"
                                                title="{{ translate('edit') }}">
                                                <i class="tio-edit"></i>
                                            </a>
                                            <a href="javascript:" class="btn btn-outline-danger btn-sm delete-data-without-form"
                                                data-action="{{ route('admin.permission-module.role-delete') }}" title="{{ translate('delete') }}" data-id="{{ $val['id'] }}">
                                                <i class="tio-delete"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="table-responsive mt-4">
                    <div class="d-flex justify-content-lg-end">
                        {{ $groupedPermissions->links() }}
                    </div>
                </div>
                @if(count($groupedPermissions)==0)
                <div class="text-center p-4">
                    <img class="mb-3 w-160" src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}" alt="">
                    <p class="mb-0">{{ translate('no_data_to_show') }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>



@endsection

@push('script')
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>
<script>
    $(function() {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
@endpush