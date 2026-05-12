@extends('layouts.back-end.app')

@section('title', translate('auth_Log_List'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 d-flex gap-2">
                <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/festival.png') }}" alt="">
                {{ translate('admin_Log_List') }}
                <span class="badge badge-soft-dark radius-50 fz-14">{{ count($authLogs) + count($adminLogs) }}</span>
            </h2>
        </div>
        
        <div class="row mt-20">
            {{-- admin logs --}}
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table
                                class="table table-hover table-borderless table-thead-bordered table-align-middle card-table w-100 text-start">
                                <thead class="thead-light thead-50 text-capitalize">
                                    <tr>
                                        <th>{{ translate('SL') }}</th>
                                        <th>{{ translate('email') }}</th>
                                        <th>{{ translate('role') }}</th>
                                        <th>{{ translate('IP Adress') }}</th>
                                        <th>{{ translate('latitude') }}</th>
                                        <th>{{ translate('longitude') }}</th>
                                        <th>{{ translate('Login Time') }}</th>
                                        <th>{{ translate('Logout Time') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($adminLogs as $key => $log)
                                        <tr>
                                            <td>{{ $key+1 }}</td>
                                            <td>{{ $log['email'] }}</td>
                                            <td>{{ $log['role'] }}</td>
                                            <td class="{{$log['ip_address']!='110.227.221.102'&&$log['ip_address']!='182.70.249.18'?'text-danger':''}}">{{ $log['ip_address'] }}</td>
                                            <td>{{ $log['latitude'] }}</td>
                                            <td>{{ $log['longitude'] }}</td>
                                            <td>{{ $log->login?date('d M Y h:i A',strtotime($log->login)):'' }}</td>
                                            <td>{{ $log->logout?date('d M Y h:i A',strtotime($log->logout)):'' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="table-responsive mt-4">
                        <div class="d-flex justify-content-lg-end">
                            {{-- {{ $adminLogs->links() }} --}}
                            {{ $adminLogs->appends(['admin_page' => request()->input('admin_page')])->links() }}
                        </div>
                    </div>
                    @if (count($adminLogs) == 0)
                        <div class="text-center p-4">
                            <img class="mb-3 w-160"
                                src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}"
                                alt="">
                            <p class="mb-0">{{ translate('no_data_to_show') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- all logs --}}
            <div class="col-md-12 mt-3">
                <div class="card">
                    <div class="px-3 py-4">
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table
                                class="table table-hover table-borderless table-thead-bordered table-align-middle card-table w-100 text-start">
                                <thead class="thead-light thead-50 text-capitalize">
                                    <tr>
                                        <th>{{ translate('SL') }}</th>
                                        <th>{{ translate('email') }}</th>
                                        <th>{{ translate('role') }}</th>
                                        <th>{{ translate('IP Adress') }}</th>
                                        <th>{{ translate('latitude') }}</th>
                                        <th>{{ translate('longitude') }}</th>
                                        <th>{{ translate('Login Time') }}</th>
                                        <th>{{ translate('Logout Time') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($authLogs as $key => $log)
                                        <tr>
                                            <td>{{ $key+1 }}</td>
                                            <td>{{ $log['email'] }}</td>
                                            <td>{{ $log['role'] }}</td>
                                            <td class="{{$log['ip_address']!='110.227.221.102'&&$log['ip_address']!='182.70.249.18'?'text-danger':''}}">{{ $log['ip_address'] }}</td>
                                            <td>{{ $log['latitude'] }}</td>
                                            <td>{{ $log['longitude'] }}</td>
                                            <td>{{ $log->login?date('d M Y h:i A',strtotime($log->login)):'' }}</td>
                                            <td>{{ $log->logout?date('d M Y h:i A',strtotime($log->logout)):'' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="table-responsive mt-4">
                        <div class="d-flex justify-content-lg-end">
                            {{-- {{ $authLogs->links() }} --}}
                            {{ $authLogs->appends(['auth_page' => request()->input('auth_page')])->links() }}
                        </div>
                    </div>
                    @if (count($authLogs) == 0)
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
