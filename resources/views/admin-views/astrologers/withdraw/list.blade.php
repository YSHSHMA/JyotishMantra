@php use App\Utils\Helpers; @endphp
@php use Illuminate\Support\Str; @endphp
@extends('layouts.back-end.app')

@section('title', translate('withdraw_List'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-4">
            <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/customer.png') }}" alt="">
                {{ translate(Str::contains(request()->url(), 0) ? 'withdraw_pending_list' : (Str::contains(request()->url(), 1) ? 'withdraw_approved_list' : 'withdraw_completed_list')) }}
                <span class="badge badge-soft-dark radius-50">{{ count($withdrawList) }}</span>
            </h2>
        </div>
        <div class="card">
            {{-- <div class="px-3 py-4">
                <div class="row gy-2 align-items-center">
                    <div class="col-sm-8 col-md-6 col-lg-4">
                        <form action="{{ url()->current() }}" method="GET">
                            <div class="input-group input-group-merge input-group-custom">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="tio-search"></i>
                                    </div>
                                </div>
                                <input id="datatableSearch_" type="search" name="searchValue" class="form-control"
                                    placeholder="{{ translate('search_by_Name_or_Email_or_Phone') }}"
                                    aria-label="Search orders" value="{{ request('searchValue') }}">
                                <button type="submit" class="btn btn--primary">{{ translate('search') }}</button>
                            </div>
                        </form>
                    </div>
                    <div class="col-sm-4 col-md-6 col-lg-8 mb-2 mb-sm-0">
                        <div class="d-flex justify-content-sm-end">
                            <button type="button" class="btn btn-outline-success mr-2" onclick="sendAppLink()">
                                {{ translate('send_App_Link') }}
                            </button>

                            <button type="button" class="btn btn-outline--primary" data-toggle="dropdown">
                                <i class="tio-download-to"></i>
                                {{ translate('export') }}
                                <i class="tio-chevron-down"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li>
                                    <a class="dropdown-item"
                                        href="{{ route('admin.customer.export', ['searchValue' => request('searchValue')]) }}">
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
            </div> --}}
            <div class="table-responsive datatable-custom">
                <table style="text-align: {{ Session::get('direction') === 'rtl' ? 'right' : 'left' }};"
                    class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                    <thead class="thead-light thead-50 text-capitalize">
                        <tr>
                            <th>{{ translate('SL') }}</th>
                            <th>{{ translate('astro_detail') }}</th>
                            <th>{{ translate('holder_name') }}</th>
                            <th>{{ translate('bank_detail') }} </th>
                            <th>{{ translate('account_no') }} </th>
                            <th>{{ translate('request_amt') }} </th>
                            {{-- @if (Helpers::modules_permission_check('Customers', 'Customer List', 'detail') || Helpers::modules_permission_check('Customers', 'Customer List', 'delete')) --}}
                            @if (Str::contains(request()->url(), 0) || Str::contains(request()->url(), 1))
                                <th class="text-center">{{ translate('action') }}</th>
                            @endif
                            {{-- @endif --}}
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($withdrawList as $key => $list)
                            <tr>
                                <td>
                                    <div class="d-flex">
                                        {{ $key+1 }}
                                    </div>
                                </td>
                                <td>
                                    <a href="{{ route('admin.astrologers.manage.detail.overview', [$list->astrologer->id]) }}"
                                        class="title-color hover-c1 d-flex align-items-center gap-10">
                                        <b>{{ ucfirst($list->astrologer->name) }}</b>
                                    </a>
                                    <p class="mb-1">{{ $list->astrologer->mobile_no }}</p>
                                </td>
                                <td>
                                    {{ ucfirst($list->astrologer->holder_name) }}
                                </td>
                                <td>
                                    <p class="mb-1">Bank: <b>{{ $list->astrologer->bank_name }}</b></p>
                                    <p class="mb-1">Branch: <b>{{ $list->astrologer->branch_name }}</b></p>
                                    <p class="mb-1">IFSC: <b>{{ $list->astrologer->bank_ifsc }}</b></p>
                                </td>
                                <td>
                                    {{ $list->astrologer->account_no }}
                                </td>
                                <td>
                                    {{ '₹' . $list->amount }}
                                </td>

                                {{-- @if (Helpers::modules_permission_check('Customers', 'Customer List', 'detail') || Helpers::modules_permission_check('Customers', 'Customer List', 'delete')) --}}
                                @if (Str::contains(request()->url(), 0))
                                    <td>
                                        <form id="approve-form-{{$key+1}}" action="{{ route('admin.astrologers.withdraw.approve') }}"
                                            method="post">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $list->id }}">
                                            <button type="button" class="btn btn-success"
                                                onclick="approveBtn({{$key+1}})">Approve</button>
                                        </form>
                                    </td>
                                @endif
                                @if (Str::contains(request()->url(), 1))
                                    <td>
                                        <form id="complete-form-{{$key+1}}" action="{{ route('admin.astrologers.withdraw.complete') }}"
                                            method="post">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $list->id }}">
                                            <button type="button" class="btn btn-success"
                                                onclick="completeBtn({{$key+1}})">Complete</button>
                                        </form>
                                    </td>
                                @endif
                                {{-- @endif --}}
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="table-responsive mt-4">
                <div class="px-4 d-flex justify-content-lg-end">
                    {!! $withdrawList->links() !!}
                </div>
            </div>
            @if (count($withdrawList) == 0)
                <div class="text-center p-4">
                    <img class="mb-3 w-160"
                        src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}"
                        alt="Image Description">
                    <p class="mb-0">{{ translate('no_data_to_show') }}</p>
                </div>
            @endif
        </div>
    </div>
@endsection
@push('script')
    <script>
        function approveBtn(key) {
            Swal.fire({
                title: "{{ translate('are_you_sure') . '?' }} ",
                text: '{{ translate('you_want_to_approve_request') }} ',
                type: 'info',
                showCancelButton: true,
                cancelButtonColor: 'default',
                confirmButtonColor: 'primary',
                cancelButtonText: '{{ translate('no') }}',
                confirmButtonText: '{{ translate('yes') }}',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $('#approve-form-'+key).submit();
                }
            })
        }
    </script>
    <script>
        function completeBtn(key) {
            Swal.fire({
                title: "{{ translate('are_you_sure') . '?' }} ",
                text: '{{ translate('you_want_to_complete_payment') }} ',
                type: 'info',
                showCancelButton: true,
                cancelButtonColor: 'default',
                confirmButtonColor: 'primary',
                cancelButtonText: '{{ translate('no') }}',
                confirmButtonText: '{{ translate('yes') }}',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $('#complete-form-'+key).submit();
                }
            })
        }
    </script>
@endpush
