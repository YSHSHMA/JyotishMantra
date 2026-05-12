@extends('layouts.back-end.app')

@section('title', translate('VIP_darshan_Booking'))
@push('css_or_js')
<link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />

<style>
    .gj-datepicker-bootstrap [role=right-icon] button .gj-icon {
        top: 14px;
        right: 5px;
    }


    .bg-label-primary {
        background-color: #007bff;
        color: #fff;
    }

    .bg-label-primary:hover {
        background-color: #0056b3;
    }

    .bg-label-danger {
        background-color: #dc3545;
        color: #fff;
    }

    .bg-label-danger:hover {
        background-color: #c82333;
    }

    .bg-label-success {
        background-color: #28a745;
        color: #fff;
    }

    .bg-label-success:hover {
        background-color: #218838;
    }

    .bg-label-info {
        background-color: #17a2b8;
        color: #fff;
    }

    .bg-label-info:hover {
        background-color: #117a8b;
    }

    .bg-label-warning {
        background-color: #ffc107;
        color: #212529;
    }

    .bg-label-warning:hover {
        background-color: #e0a800;
    }

    .dropdown-menufollow {
        background-color: #fff;
        border: 1px solid #ccc;
        border-radius: 0.375rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
        padding: 1rem;
        width: 225px;
        margin-right: 13rem;
        text-align: center;
        display: flex;
        gap: 0.5rem;
        position: absolute;
    }

    .d-flex {
        display: flex;
    }

    .justify-content-center {
        justify-content: center;
    }

    .gap-2 {
        gap: 0.5rem;
    }

    .myactionbtn {
        width: 1.625rem !important;
        height: 1.625rem !important;
    }
</style>
@endpush
@section('content')
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 d-flex gap-2">
            <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/festival.png') }}" alt="">
            {{ translate('VIP_darshan_Booking') }}
            <span class="badge badge-soft-dark radius-50 fz-14">{{ count($darshanlist) }}</span>
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
                                    <input id="datatableSearch_" type="search" name="searchValue" class="form-control"
                                        placeholder="{{ translate('search_by_name') }}"
                                        aria-label="{{ translate('search_by_name') }}"
                                        value="{{ request('searchValue') }}" required>
                                    <button type="submit"
                                        class="btn btn--primary input-group-text">{{ translate('search') }}</button>
                                </div>
                            </form>
                        </div>
                        <div class="col-sm-4 col-md-6 col-lg-8 d-flex justify-content-end">

                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table
                            class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100 text-start">
                            <thead class="thead-light thead-50 text-capitalize">
                                <tr>
                                    <th>{{ translate('SL') }}</th>
                                    <th>{{ translate('User_Info') }}</th>
                                    <th>{{ translate('package_Info') }}</th>
                                    <th>{{ translate('date') }}/{{ translate('time_slot') }}</th>
                                    <th>{{ translate('Trust_name') }}</th>
                                    <th>{{ translate('Price') }}</th>
                                    <th>{{ translate('gst') }}</th>
                                    <th>{{ translate('admin_commission') }}</th>
                                    <th>{{ translate('final_amount') }}</th>
                                    <th class="text-center"> {{ translate('action') }}</th>
                                </tr>
                            </thead>
                            @if($darshanlist && count($darshanlist) > 0)
                            <tbody>
                                @foreach($darshanlist as $key=>$val)
                                <tr>
                                    <td>{{ $darshanlist->firstItem()+$key }}</td>
                                    <td>
                                        <small>{{ ($val['userData']['name']??"") }}</small><br>
                                        <small>{{ ($val['userData']['phone']??"") }}</small><br>
                                        <small>{{ date('d M,Y h:i A',strtotime($val['created_at']??"")) }}</small><br>
                                    </td>
                                    <td>
                                        <span>{{ ($val['title']??"") }}</span><br>
                                        <span>{{ ($val['package_name']??"") }}</span><br>
                                    </td>
                                    <td>
                                        <span>Date : {{ date('d M,Y',strtotime($val['date']??"")) }}</span><br>
                                        <span>Slot : {{ ($val['time']??"") }}</span><br>
                                    </td>
                                    <td><span class="font-weight-bolder">{{ ($val['Temple']->matchingTrust()['trust_name']??"") }}</span></td>
                                    <td>{{ ($val['price']??"") }}</td>
                                    <td>{{ ($val['gst_amount']??"") }}</td>
                                    <td>{{ ($val['admin_commission']??"") }}</td>
                                    <td>{{ ($val['final_amount']??"") }}</td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="{{ route('admin.temple.darshan-bookings.darshan-booking-information',['id'=>$val['id']]) }}" class="btn btn-outline-info btn-sm"><i class="tio-invisible"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            @endif
                        </table>
                    </div>
                </div>
                <div class="table-responsive mt-4">
                    <div class="d-flex justify-content-lg-end">
                        {{ $darshanlist->links() }}
                    </div>
                </div>
                @if (count($darshanlist) == 0)
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

{{-- Histore --}}
<div class="modal fade" id="followUpHistoryModal" tabindex="-1">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="followUpModalHistoryTitleId">
                    Follow Up History
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="table-responsive">
                        <table class="table">
                            <thead class="bg-dark">
                                <tr>
                                    <th scope="col" class="text-white">#</th>
                                    <th scope="col" class="text-white">Follow By</th>
                                    <th scope="col" class="text-white">Last Followup</th>
                                    <th scope="col" class="text-white">Message</th>
                                    <th scope="col" class="text-white">Next Followup</th>
                                </tr>
                            </thead>
                            <tbody id="followUpPoojaHistoryTBody">
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary waves-effect waves-light" data-dismiss="modal">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>
<script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>

@endpush