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
                {{ translate('employee_role_list') }}
            </h2>
        </div>
        <div class="card mt-3">
            <div class="px-3 py-4">
                <div class="row justify-content-between align-items-center flex-grow-1">
                    <div class="col-md-4 col-lg-6 mb-2 mb-sm-0">
                        <h5 class="d-flex align-items-center gap-2">
                            {{ translate('employee_Roles') }}
                            <span class="badge badge-soft-dark radius-50 fz-12 ml-1">{{ count($roles) }}</span>
                        </h5>
                    </div>
                    <div class="col-md-8 col-lg-6 d-flex flex-wrap flex-sm-nowrap justify-content-sm-end gap-3">
                        <form action="{{ url()->current() }}?search={{ request('searchValue') }}" method="GET">
                            <div class="input-group input-group-merge input-group-custom">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="tio-search"></i>
                                    </div>
                                </div>
                                <input id="datatableSearch_" type="search" name="searchValue" class="form-control"
                                    placeholder="{{ translate('search_role') }}" value="{{ request('searchValue') }}">
                                <button type="submit" class="btn btn--primary">{{ translate('search') }}</button>
                            </div>
                        </form>
                        <div class="">
                            <button type="button" class="btn btn-outline--primary text-nowrap" data-toggle="dropdown">
                                <i class="tio-download-to"></i>
                                {{ translate('export') }}
                                <i class="tio-chevron-down"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li>
                                    <a class="dropdown-item"
                                        href="{{ route('admin.custom-role.export', ['searchValue' => request('searchValue')]) }}">
                                        <img width="14"
                                            src="{{ dynamicAsset(path: 'public/assets/back-end/img/excel.png') }}"
                                            alt="">
                                        {{ translate('excel') }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="pb-3">
                <div class="table-responsive">
                    <table
                        class="table table-hover table-borderless table-thead-bordered table-align-middle card-table text-start">
                        <thead class="thead-light thead-50 text-capitalize table-nowrap">
                            <tr>
                                <th>{{ translate('SL') }}</th>
                                <th>{{ translate('role_name') }}</th>
                                <th>{{ translate('created_at') }}</th>
                                @if (Helpers::modules_permission_check('Employee', 'Employee Role Setup', 'status'))
                                <th>{{ translate('status') }}</th>
                                @endif
                                @if (Helpers::modules_permission_check('Employee', 'Employee Role Setup', 'edit') || Helpers::modules_permission_check('Employee', 'Employee Role Setup', 'delete'))
                                <th class="text-center">{{ translate('action') }}</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($roles as $key => $role)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $role['name'] }}</td>
                                    <td>{{ date('d-M-y', strtotime($role['created_at'])) }}</td>
                                    @if (Helpers::modules_permission_check('Employee', 'Employee Role Setup', 'status'))
                                    <td>
                                        <form action="{{ route('admin.custom-role.employee-role-status') }}" method="post"
                                            id="employee-role-status{{ $role['id'] }}-form">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $role['id'] }}">
                                            <label class="switcher" for="employee-role-status{{ $role['id'] }}">
                                                <input type="checkbox" class="switcher_input toggle-switch-message"
                                                    id="employee-role-status{{ $role['id'] }}" name="status"
                                                    value="1" {{ $role['status'] == 1 ? 'checked' : '' }}
                                                    data-modal-id = "toggle-status-modal"
                                                    data-toggle-id = "employee-role-status{{ $role['id'] }}"
                                                    data-on-image = "employee-on.png" data-off-image = "employee-off.png"
                                                    data-on-title = "{{ translate('want_to_Turn_ON_Employee_Status') . '?' }}"
                                                    data-off-title = "{{ translate('want_to_Turn_OFF_Employee_Status') . '?' }}"
                                                    data-on-message = "<p>{{ translate('when_the_status_is_enabled_employees_can_access_the_system_to_perform_their_responsibilities') }}</p>"
                                                    data-off-message = "<p>{{ translate('when_the_status_is_disabled_employees_cannot_access_the_system_to_perform_their_responsibilities') }}</p>">
                                                <span class="switcher_control"></span>
                                            </label>
                                        </form>
                                    </td>
                                    @endif

                                    @if (Helpers::modules_permission_check('Employee', 'Employee Role Setup', 'edit') || Helpers::modules_permission_check('Employee', 'Employee Role Setup', 'delete'))
                                    <td>
                                        <div class="d-flex gap-2 justify-content-center">
                                            @if (Helpers::modules_permission_check('Employee', 'Employee Role Setup', 'edit'))
                                            <a href="{{ route('admin.custom-role.update', [$role['id']]) }}"
                                                class="btn btn-outline--primary btn-sm square-btn"
                                                title="{{ translate('edit') }}">
                                                <i class="tio-edit"></i>
                                            </a>
                                            @endif
                                            @if (Helpers::modules_permission_check('Employee', 'Employee Role Setup', 'delete'))
                                            <a href="javascript:"
                                                class="btn btn-outline-danger btn-sm delete-data-without-form"
                                                data-action="{{ route('admin.custom-role.delete') }}"
                                                title="{{ translate('delete') }}" data-id="{{ $role['id'] }}">
                                                <i class="tio-delete"></i>
                                            </a>
                                            @endif
                                        </div>
                                    </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
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
@endpush
