@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app-trustees')

@section('title', translate('donation_history'))

@section('content')
@php
if (auth('trust')->check()) {
$relationEmployees = auth('trust')->user()->relation_id;
} elseif (auth('trust_employee')->check()) {
$relationEmployees = auth('trust_employee')->user()->relation_id;
} elseif (auth('purohit')->check()) {
$relationEmployees = (\App\Models\Purohit::with(['temple'])->where('id',auth('purohit')->user()->id)->first()['temple']['trust_id']??0);
}
@endphp
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 d-flex gap-2">
            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/') }}" alt="">
            {{ translate('donation_history') }}
        </h2>
    </div>
    <div class="row">
        <div class="col-md-12 mb-2">
            @if(Helpers::Employee_modules_permission('Donation Management', 'Donation History', 'Filter'))
            <div class='row'>
                <div class="col-sm-6 col-lg-3 col-md-3  mt-2">
                    <a class="order-stats order-stats_confirmed" href="{{ route('trustees-vendor.donation-history.list') }}">
                        <div class="order-stats__content">
                            <i class="tio-all_done">all_done</i>
                            <h6 class="order-stats__subtitle">{{ translate('All')}}</h6>
                        </div>
                        <span class="order-stats__title">
                            @php
                            echo \App\Models\DonateAllTransaction::whereIn('type',['donate_ads','donate_trust'])->where('amount_status',1)->where('trust_id',$relationEmployees)->count();
                            @endphp
                        </span>
                    </a>
                </div>
                <div class="col-sm-6 col-lg-3 col-md-3  mt-2">
                    <a class="order-stats order-stats_confirmed" href="{{ route('trustees-vendor.donation-history.list',['type'=>'donate_trust']) }}">
                        <div class="order-stats__content">
                            <i class="tio-all_done">all_done</i>
                            <h6 class="order-stats__subtitle">{{ translate('trust')}}</h6>
                        </div>
                        <span class="order-stats__title">
                            @php
                            echo \App\Models\DonateAllTransaction::whereIn('type',['donate_trust'])->where('amount_status',1)->where('trust_id',$relationEmployees)->count();
                            @endphp

                        </span>

                    </a>
                </div>
                <div class="col-sm-6 col-lg-3 col-md-3 mt-2">
                    <a class="order-stats order-stats_confirmed" href="{{ route('trustees-vendor.donation-history.list',['type'=>'donate_ads']) }}">
                        <div class="order-stats__content">
                            <i class="tio-all_done">all_done</i>
                            <h6 class="order-stats__subtitle">{{ translate('ads')}}</h6>
                        </div>
                        <span class="order-stats__title">
                            @php
                            echo \App\Models\DonateAllTransaction::whereIn('type',['donate_ads'])->where('amount_status',1)->where('trust_id',$relationEmployees)->count();
                            @endphp
                        </span>
                    </a>
                </div>

            </div>
            @endif
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="px-3 py-4">
                    <!-- Search bar -->
                    <div class="row align-items-center">
                        <div class="col-sm-4 col-md-6 col-lg-8 mb-2 mb-sm-0">
                        </div>
                        <div class="col-sm-8 col-md-6 col-lg-4">
                            <form action="{{ url()->current() }}" method="GET">
                                <div class="input-group input-group-custom input-group-merge">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="tio-search"></i>
                                        </div>
                                    </div>
                                    <input id="datatableSearch_" type="search" name="searchValue" class="form-control" placeholder="{{ translate('search_by_name') }}" aria-label="{{ translate('search_by_name') }}" required>
                                    <button type="submit" class="btn btn--primary">{{ translate('search') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- Table displaying trust  -->
                <div class="text-start">
                    <div class="table-responsive">
                        <table id="datatable" class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                            <thead class="thead-light thead-50 text-capitalize">
                                <tr>
                                    <th>{{ translate('SL') }}</th>
                                    <th>{{ translate('Tran_ID') }}</th>
                                    <th>{{ translate('type') }}</th>
                                    <th>{{ translate('User_Info') }}</th>
                                    <th>{{ translate('donated_date') }}</th>
                                    <th>{{ translate('TXN_id') }}</th>
                                    <th>{{ translate('Amount') }}</th>
                                    @if(Helpers::Employee_modules_permission('Donation Management', 'Donation History', 'Admin Commission'))
                                    <th>{{ translate('admin_commission') }}</th>
                                    @endif
                                    @if(Helpers::Employee_modules_permission('Donation Management', 'Donation History', 'Final Amount'))
                                    <th>{{ translate('Final_amount') }}</th>
                                    @endif
                                    <th>{{ translate('option')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Loop through items -->
                                @foreach($ads_transaction as $key => $items)
                                <tr>
                                    <td>{{$ads_transaction->firstItem()+$key}}</td>
                                    <td>
                                        <span>{{ $items['trans_id'] }}</span><br>
                                        @if(strtolower($items['platform']) == 'web')
                                        <span class="badge badge-soft-success my-1"> {{ ucwords($items['platform']) }}</span><br>
                                        @elseif(strtolower($items['platform']) == 'app')
                                        <span class="badge badge-soft-info my-1"> {{ ucwords($items['platform']) }}</span><br>
                                        @endif
                                        @if(strtolower(($items['transaction_id']??'')) == 'wallet')
                                        <span class="btn btn-sm btn-outline-secondary">Wallet</span>
                                        @else
                                        <span class="badge badge-soft-warning">Online</span>
                                        @endif      
                                                                          
                                    </td>
                                    <td><span>
                                            @if($items['ads_id'] == 0)
                                            Trust Name: <span class="font-weight-bold" title="{{ ($items['getTrust']['trust_name']??'') }}" data-toggle="tooltip" data-placement="left">{{ Str::limit(($items['getTrust']['trust_name']??""),20) }}</span>
                                            @else
                                            Ads Name: <span class="font-weight-bold" title="{{ ($items['adsTrust']['name']??'') }}" data-toggle="tooltip" data-placement="left">{{ Str::limit(($items['adsTrust']['name']??""),20) }}</span>
                                            @endif
                                        </span>
                                    </td>
                                    <td>
                                        <span>{{ ($items['users']['name']??'') }}</span><br>
                                        <span>{{ ($items['users']['phone']??'') }}</span><br>
                                        <span>{{ ($items['users']['email']??'') }}</span><br>
                                    </td>
                                    <td>{{ date('d M,Y h:i A',strtotime($items['created_at']??'')) }}</td>
                                    <td>{{ ($items['transaction_id']??'') }}</td>
                                    <td>₹{{ ($items['amount']??'') }}</td>
                                    @if(Helpers::Employee_modules_permission('Donation Management', 'Donation History', 'Admin Commission'))
                                    <td>₹{{ ($items['admin_commission']??'') }}</td>
                                    @endif
                                    @if(Helpers::Employee_modules_permission('Donation Management', 'Donation History', 'Final Amount'))
                                    <td>₹{{ ($items['final_amount']??'') }}</td>
                                    @endif
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <a class="btn btn-outline-info btn-sm square-btn" title="{{ translate('share') }}" href="{{ route('trustees-vendor.donation-history.view',['id'=>$items['id']])}}">
                                                <i class="tio-invisible"></i>
                                            </a>
                                            <a class="btn btn-outline-info btn-sm square-btn" title="{{ translate('invoice') }}" target="_blank" href="{{ route('donate-create-pdf-invoice', [$items['id']]) }}">
                                                <i class="tio-arrow_large_downward">arrow_large_downward</i>
                                            </a>
                                            <a class="btn btn-outline-info btn-sm square-btn" title="{{ translate('80G') }}" target="_blank" href="{{ url('api/v1/donate/twoal-a-certificate', [$items['id']]) }}">
                                                <i class="tio-file_text">file_text</i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            @if(count($ads_transaction) > 0)
                            <fbody>
                                <th colspan='6' class='font-weight-bold'>Total</th>
                                @if(request('type'))
                                <th class='font-weight-bold'>₹{{ \App\Models\DonateAllTransaction::where('type',request('type'))->where('amount_status',1)->where('trust_id',$relationEmployees)->sum('amount') }}</th>
                                <th class='font-weight-bold'>₹{{ \App\Models\DonateAllTransaction::where('type',request('type'))->where('amount_status',1)->where('trust_id',$relationEmployees)->sum('admin_commission') }}</th>
                                <th class='font-weight-bold'>₹{{ \App\Models\DonateAllTransaction::where('type',request('type'))->where('amount_status',1)->where('trust_id',$relationEmployees)->sum('final_amount') }}</th>
                                @else
                                <th class='font-weight-bold'>₹{{ \App\Models\DonateAllTransaction::whereIn('type',['donate_ads','donate_trust'])->where('amount_status',1)->where('trust_id',$relationEmployees)->sum('amount') }}</th>
                                @if(Helpers::Employee_modules_permission('Donation Management', 'Donation History', 'Admin Commission'))
                                <th class='font-weight-bold'>₹{{ \App\Models\DonateAllTransaction::whereIn('type',['donate_ads','donate_trust'])->where('amount_status',1)->where('trust_id',$relationEmployees)->sum('admin_commission') }}</th>
                                @endif
                                @if(Helpers::Employee_modules_permission('Donation Management', 'Donation History', 'Final Amount'))
                                <th class='font-weight-bold'>₹{{ \App\Models\DonateAllTransaction::whereIn('type',['donate_ads','donate_trust'])->where('amount_status',1)->where('trust_id',$relationEmployees)->sum('final_amount') }}</th>
                                @endif
                                @endif
                            </fbody>
                            @endif
                        </table>
                    </div>
                </div>
                <!-- Pagination for trust list -->
                <div class="table-responsive mt-4">
                    <div class="d-flex justify-content-lg-end">
                        {!! $ads_transaction->links() !!}
                    </div>
                </div>
                <!-- Message for no data to show -->
                @if(count($ads_transaction) == 0)
                <div class="text-center p-4">
                    <img class="mb-3 w-160" src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}" alt="{{ translate('image') }}">
                    <p class="mb-0">{{ translate('no_data_to_show') }}</p>
                </div>
                @endif

            </div>
        </div>
    </div>
</div>
<!-- Hidden HTML element for delete route -->
<span id="route-admin-trust-delete" data-url="{{ route('trustees-vendor.ads-management.ad-trust-delete') }}"></span>
<!-- Toast message for trust deleted -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 5">
    <div id="panchangmoonimage-deleted-message" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-body">
            {{ translate('Trust_deleted_Successfully') }}
        </div>
    </div>
</div>
@endsection

@push('script')
<!-- Include SweetAlert2 for confirmation dialogs -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

@endpush