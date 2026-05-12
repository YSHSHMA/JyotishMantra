@extends('layouts.back-end.app')

@section('title', translate('log_List'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 d-flex gap-2">
                <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/festival.png') }}" alt="">
                {{ translate('log_List') }}
                <span class="badge badge-soft-dark radius-50 fz-14">{{ count($logs) }}</span>
            </h2>
        </div>

        <div class="row mt-20">
            <div class="col-md-12">
                <div class="card">
                    <div class="px-3 py-4">
                        <div class="row g-2 flex-grow-1">
                            <div class="col-sm-8 col-md-6 col-lg-4">
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
                            <div class="col-sm-4 col-md-6 col-lg-8 d-flex justify-content-end">
                                <!-- <button type="button" class="btn btn-outline--primary" data-toggle="dropdown">
                                            <i class="tio-download-to"></i>
                                            {{ translate('export') }}
                                            <i class="tio-chevron-down"></i>
                                        </button> -->
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item"
                                            href="{{ route('admin.calculator.export', ['searchValue' => request('searchValue')]) }}">
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
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table
                                class="table table-hover table-borderless table-thead-bordered table-align-middle card-table w-100 text-start">
                                <thead class="thead-light thead-50 text-capitalize">
                                    <tr>
                                        <th>{{ translate('SL') }}</th>
                                        <th>{{ translate('name') }}</th>
                                        <th>{{ translate('email') }}</th>
                                        <th>{{ translate('IP Adress') }}</th>
                                        <th>{{ translate('latitude') }}</th>
                                        <th>{{ translate('longitude') }}</th>
                                        <th>{{ translate('module') }}</th>
                                        <th>{{ translate('sub_Module') }}</th>
                                        <th>{{ translate('action') }}</th>
                                        <th>{{ translate('date') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($logs as $key => $log)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $log['name'] }}</td>
                                            <td>{{ $log['email'] }}</td>
                                            <td>{{ $log['ip_address'] }}</td>
                                            <td>{{ $log['latitude'] }}</td>
                                            <td>{{ $log['longitude'] }}</td>
                                            <td>{{ $log['module'] }}</td>
                                            <td>{{ $log['sub_module'] }}</td>
                                            <td>{{ $log['action'] }}</td>
                                            <td>{{ date('d M Y h:i A', strtotime($log['created_at'])) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="table-responsive mt-4">
                        <div class="d-flex justify-content-lg-end">
                            {{ $logs->links() }}
                        </div>
                    </div>
                    @if (count($logs) == 0)
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
@endsection

@push('script')
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>
@endpush
