@extends('layouts.back-end.app')

@section('title', translate('SDM_Employee_List'))

@section('content')
    <div class="content container-fluid">
        <div class="row">
            {{-- <div class="col-md-12"> --}}
            <div class="card mb-3">
                <div class="card-body text-start">
                    <form action="{{ route('admin.sdm.employee.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sdm" class="title-color">
                                        {{ translate('SDM') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select id="sdm" name="sdm_id" class="form-control" required>
                                        @forelse ($sdms as $sdm)
                                            <option value="{{$sdm->id}}">{{$sdm->name}}</option>                                            
                                        @empty
                                            <option value="">No SDM Found</option>
                                        @endforelse
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="title-color">
                                        {{ translate('name') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="name" class="form-control"
                                        placeholder="{{ translate('enter_name') }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="email" class="title-color">
                                        {{ translate('email') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="email" name="email" class="form-control"
                                        placeholder="{{ translate('enter_email') }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="mobile" class="title-color">
                                        {{ translate('mobile') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" name="mobile" class="form-control"
                                        placeholder="{{ translate('enter_mobile') }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="password" class="title-color">
                                        {{ translate('password') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="password" name="password" class="form-control"
                                        placeholder="{{ translate('enter_password') }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="my-3">
                            <h4>Temples</h4>
                            <div class="row py-2" id="temple-div">
                                <div class="text-center w-100">
                                    <p class="text-danger">No Temples Found</p>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-3 justify-content-end">
                            <button type="submit" class="btn btn--primary px-4">{{ translate('submit') }}</button>
                        </div>
                    </form>
                </div>
            </div>
            {{-- </div> --}}
        </div>

        <div class="row">
            <div class="card w-100">
                <div class="card-body">
                    <div class="mb-1">
                        <h2 class="h1 mb-0 d-flex gap-2">
                            <img width="20"
                                src="{{ dynamicAsset(path: 'public/assets/back-end/img/sahitya/collector.jpg') }}"
                                alt="">
                            {{ translate('SDM_Employee_List') }}
                            <span class="badge badge-soft-dark radius-50 fz-14">{{ $employees->total() }}</span>
                        </h2>
                    </div>
                    <div class="row">

                        <div class="col-md-12">

                            <div class="px-3 py-4">
                                <div class="row g-2 flex-grow-1">
                                    <div class="col-md-12">
                                        {{-- <form action="{{ url()->current() }}" method="GET">
                                                <div class="input-group input-group-custom input-group-merge">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="tio-search"></i>
                                                        </div>
                                                    </div>
                                                    <input id="datatableSearch_" type="search" name="searchValue" class="form-control"
                                                        placeholder="{{ translate('search_by_name') }}"
                                                        aria-label="{{ translate('search_by_name') }}"
                                                        value="{{ request('searchValue') }}" required>
                                                    <button type="submit"
                                                        class="btn btn--primary input-group-text">{{ translate('search') }}</button>
                                                </div>
                                                </form> --}}
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table
                                    class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100 text-start">
                                    <thead class="thead-light thead-50 text-capitalize">
                                        <tr>
                                            <th>{{ translate('SL') }}</th>
                                            <th>{{ translate('name') }}</th>
                                            <th>{{ translate('SDM') }}</th>
                                            <th>{{ translate('email') }}</th>
                                            <th>{{ translate('mobile') }}</th>
                                            <th>{{ translate('status') }}</th>
                                            <th class="text-center"> {{ translate('action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($employees as $key => $employee)
                                            <tr>
                                                <td>{{ $employees->firstItem() + $key }}</td>
                                                <td class="">
                                                    {{ $employee->name ?? 'NA' }}
                                                </td>
                                                <td class="">
                                                    {{ $employee->sdm->name }}
                                                </td>
                                                <td class="">
                                                    {{ $employee->email }}
                                                </td>
                                                <td class="">
                                                    {{ $employee->mobile }}
                                                </td>
                                                <td class="">
                                                    <form action="{{ route('admin.sdm.employee.status') }}" method="post"
                                                        id="employee-status{{ $employee['id'] }}-form">
                                                        @csrf
                                                        <input type="hidden" name="id"
                                                            value="{{ $employee['id'] }}">
                                                        <label class="switcher mx-auto">
                                                            <input type="checkbox"
                                                                class="switcher_input toggle-switch-message" name="status"
                                                                id="employee-status{{ $employee['id'] }}" value="1"
                                                                {{ $employee['status'] == 1 ? 'checked' : '' }}
                                                                data-modal-id = "toggle-status-modal"
                                                                data-toggle-id = "employee-status{{ $employee['id'] }}"
                                                                data-on-image = "employee-status-on.png"
                                                                data-off-image = "employee-status-off.png"
                                                                data-on-title = "{{ translate('Want_to_Turn_ON') . ' ' . $employee['defaultname'] . ' ' . translate('status') }}"
                                                                data-off-title = "{{ translate('Want_to_Turn_OFF') . ' ' . $employee['defaultname'] . ' ' . translate('status') }}"
                                                                data-on-message = "<p>{{ translate('if_enabled_this_employee_will_be_available_on_the_website_and_customer_app') }}</p>"
                                                                data-off-message = "<p>{{ translate('if_disabled_this_employee_will_be_hidden_from_the_website_and_customer_app') }}</p>">
                                                            <span class="switcher_control"></span>
                                                        </label>
                                                    </form>
                                                </td>
                                                <td>
                                                    <div class="d-flex justify-content-center gap-2">
                                                        <a href="{{ route('admin.sdm.employee.view', $employee['id']) }}"
                                                            class="btn btn-outline-primary btn-sm square-btn"
                                                            title="{{ translate('view') }}">
                                                            <i class="tio-invisible"></i>
                                                        </a>

                                                        <a href="{{ route('admin.sdm.employee.edit', $employee['id']) }}"
                                                            class="btn btn-outline-primary btn-sm square-btn"
                                                            title="{{ translate('edit') }}">
                                                            <i class="tio-edit"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="table-responsive mt-4">
                                <div class="d-flex justify-content-lg-end">
                                    {{ $employees->links() }}
                                </div>
                            </div>
                            @if (count($employees) == 0)
                                <div class="text-center p-4">
                                    <img class="mb-3 w-160"
                                        src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}"
                                        alt="">
                                    <p class="mb-0">{{ translate('no_data_to_show') }}</p>
                                </div>
                            @endif


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

    <script>
        $(document).ready(function() {

            function loadTemples() {
                let sdm = $('#sdm').val();

                $('#temple-div').html(`
                    <div class="text-center w-100">
                        <p>Loading temples...</p>
                    </div>
                `);

                $.ajax({
                    url: "{{ route('admin.sdm.employee.get-temple') }}",
                    type: "GET",
                    data: {
                        sdm: sdm
                    },
                    success: function(response) {

                        if (!response.status || response.temples.length === 0) {
                            $('#temple-div').html(`
                                <div class="text-center w-100">
                                    <p class="text-danger">No Temples Found</p>
                                </div>
                            `);
                            return;
                        }

                        let html = '';

                        $.each(response.temples, function(index, temple) {

                            html += `
                                <div class="col-md-6 mb-2">
                                    <label class="d-flex align-items-center gap-2">
                                        <input type="checkbox"
                                            class="temple-checkbox"
                                            name="temples[]"
                                            value="${temple.id}">
                                        ${temple.name}
                                    </label>
                                </div>
                            `;
                        });

                        $('#temple-div').html(html);
                    },
                    error: function() {
                        $('#temple-div').html(`
                            <div class="text-center w-100">
                                <p class="text-danger">Something went wrong</p>
                            </div>
                        `);
                    }
                });
            }

            // 🔹 Page load
            loadTemples();

            // 🔹 District change
            $('#sdm').on('change', function() {
                loadTemples();
            });

        });
    </script>
@endpush
