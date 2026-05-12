@extends('layouts.back-end.app')

@section('title', translate('Trust_ads_details'))
@push('css_or_js')
<style>
    .rainbow {
        background-color: #343A40;
        border-radius: 4px;
        color: #000;
        cursor: pointer;
        padding: 8px 16px;
    }

    .rainbow-1 {
        background-image: linear-gradient(359deg, #90e979d9 13%, #f8f8f8 54%, #ebd859 103%);
        animation: slidebg 5s linear infinite;
    }

    @keyframes slidebg {
        to {
            background-position: 20vw;
        }
    }
</style>
@endpush

@section('content')
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 d-flex gap-2">
            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/') }}" alt="">
            {{ translate('Trust_ads_details') }}
        </h2>
    </div>
    <div class="row">
        <div class="card w-100">
            <div class="card-body">
                <ul class="nav nav-tabs w-fit-content mb-4">
                    <li class="nav-item text-capitalize">
                        <a class="nav-link active" id="overview-tab" data-toggle="tab" href="#overview-content">
                            {{ translate('overview') }}
                        </a>
                    </li>
                    @if($old_data['type'] == 'outsite')
                    <li class="nav-item text-capitalize">
                        <a class="nav-link" id="setting-tab" data-toggle="tab" href="#setting-content">
                            {{ translate('Service') }}
                        </a>
                    </li>
                    @endif
                    <li class="nav-item text-capitalize">
                        <a class="nav-link" id="transaction-tab" data-toggle="tab" href="#transaction-content">
                            {{ translate('transaction') }}
                        </a>
                    </li>


                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="overview-content">
                        <div class="row">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <img class="avatar avatar-170 rounded-0" src="{{  getValidImage(path: 'storage/app/public/donate/ads/'.$old_data['image'], type: 'backend-product') }}" alt="Image">
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="mt-2">
                                                    <span class="font-weight-bolder">{{translate('Name')}}</span>
                                                    <span>:</span>
                                                    <span class="value text-capitalize">{{$old_data['name']}}</span>
                                                </div>
                                            </div>
                                            @if($old_data['type'] == 'outsite')
                                            <div class="col-12">
                                                <div class="mt-2">
                                                    <span class="font-weight-bolder">{{translate('Category_name')}}</span>
                                                    <span>:</span>
                                                    <span class="value text-capitalize">{{$old_data['category']['name']}}</span>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="mt-2">
                                                    <span class="font-weight-bolder">{{translate('Trust_Name')}}</span>
                                                    <span>:</span>
                                                    <span class="value text-capitalize">{{$old_data['Trusts']['trust_name']}}</span>
                                                </div>
                                            </div>
                                            @endif
                                            <div class="col-12">
                                                <div class="mt-2">
                                                    <span class="font-weight-bolder">{{translate('Purpose_name')}}</span>
                                                    <span>:</span>
                                                    <span class="value text-capitalize">{{($old_data['Purpose']['name']??"")}}</span>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                @if($old_data['set_type'] == 1)
                                                <div class="mt-2">
                                                    <span class="font-weight-bolder">
                                                        {{($old_data['set_amount']??'')}}/{{(($old_data['set_number']??'') > 0)?$old_data['set_number']:''}} {{($old_data['set_unit']??'')}}
                                                    </span>
                                                </div>
                                                @endif
                                            </div>
                                            <div class="col-12">
                                                <a href="#" class="btn btn-outline--primary px-4 mt-4" target="_blank"><i class="tio-globe"></i>
                                                    View live
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="d-flex flex-column gap-10 justify-content-end">
                                            <div class="order-status d-flex justify-content-sm-end gap-10 text-capitalize">

                                                <span class="title-color font-weight-bold">{{translate('status')}}: </span>
                                                @if($old_data['status'] == 1)
                                                <span class="badge badge-soft-success font-weight-bold radius-50 d-flex align-items-center py-1 px-2">{{translate('active')}} </span>
                                                @else
                                                <span class="badge badge-soft-danger font-weight-bold radius-50 d-flex align-items-center py-1 px-2"> {{translate('inactive')}}</span>
                                                @endif
                                            </div>
                                            <div class="order-status d-flex justify-content-sm-end gap-10 text-capitalize">
                                                <span class="title-color font-weight-bold">{{translate('verification_status')}}: </span>
                                                @if($old_data['is_approve'] == 1)
                                                <span class="badge badge-soft-success font-weight-bold radius-50 d-flex align-items-center py-1 px-2">{{translate('Go_To_Live')}} </span>
                                                @elseif($old_data['is_approve'] == 2)
                                                <span class="badge badge-soft-warning font-weight-bold radius-50 d-flex align-items-center py-1 px-2">{{translate('Send_Request')}} </span><br>
                                                @else
                                                <select class="form-control" id="verification_Status_ads">
                                                    <option value="">Select Verification Status</option>
                                                    <option value="1" data-type='1' data-href="{{ route('admin.donate_management.ad_trust.trust_ads_verify_approvel',[$old_data['id'],1]) }}" {{ ($old_data['is_approve'] == 1) ? 'selected' : '' }}>{{translate('Approve')}}</option>
                                                    <option value="2" data-type='2' data-href="{{ route('admin.donate_management.ad_trust.trust_ads_verify_approvel',[$old_data['id'],2]) }}" {{ (($old_data['is_approve'] == 2) || ($old_data['is_approve'] == 4)) ? 'selected' : '' }}>{{translate('Reject')}}</option>
                                                    <option value="0" data-type='0' data-href="{{ route('admin.donate_management.ad_trust.trust_ads_verify_approvel',[$old_data['id'],0]) }}" {{ ($old_data['is_approve'] == 0) ? 'selected' : '' }}>{{translate('Pending')}}</option>
                                                </select>
                                                @endif
                                            </div>
                                            @if($old_data['is_approve'] == 2)
                                            <div class="order-status d-flex justify-content-sm-end gap-10 text-capitalize">
                                                <span class="title-color font-weight-bold">&nbsp;</span>
                                                <a href="{{ route('admin.donate_management.ad_trust.trust_ads_verify_approvel',[$old_data['id'],2,'amount'=>$old_data['approve_amount']??0]) }}" class='btn btn-warning text-white btn-sm'>Resend</a>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <hr>
                                <div class="row">
                                    <div class="col-md-2 border p-3 mt-2">
                                        <div class="d-flex flex-column mb-1">
                                            <h6 class="font-weight-normal">Total services : <a class="text-primary" style="font-size: 18px;"> @if($old_data['type'] == 'outsite')
                                                    1
                                                    @else
                                                    0
                                                    @endif</a></h6>
                                        </div>
                                    </div>
                                    <div class="col-md-1"></div>
                                    <div class="col-md-9 mt-2 p-0">
                                        <div class="bg-light p-3 border border-primary-light rounded">
                                            <h4 class="mb-3 text-capitalize">
                                                {{ translate('Description') }}
                                            </h4>

                                            <div class="d-flex gap-5">
                                                <div class="pair-list">

                                                    {!! $old_data['description'] !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- </div> -->
                            </div>
                            <!--  -->
                            <div class="card-body mt-3">
                                <div class="row justify-content-between align-items-center g-2 mb-3">
                                    <div class="col-sm-6">
                                        <h4 class="d-flex align-items-center text-capitalize gap-10 mb-0">
                                            <img width="20" class="mb-1" src="{{ dynamicAsset(path: 'public/assets/back-end/img/admin-wallet.png') }}" alt="">
                                            {{ translate('Trust_wallet') }}
                                        </h4>
                                    </div>
                                    <div class='col-sm-6 text-end'>


                                    </div>
                                </div>

                                <div class="row g-2" id="order_stats">
                                    <div class="col-lg-4">
                                        <div class="card h-100 d-flex justify-content-center align-items-center">
                                            <div class="card-body d-flex flex-column gap-10 align-items-center justify-content-center">
                                                <img width="40" class="mb-2" src="{{ dynamicAsset(path: 'public/assets/back-end/img/withdraw-icon.png') }}" alt="">

                                                <h3 class="mb-1 fz-24">
                                                    {{ \App\Models\DonateAllTransaction::where('type','donate_ads')->where('ads_id',$old_data['id'])->where('amount_status',1)->sum('amount')}}
                                                    ₹
                                                </h3>
                                                <div class="text-capitalize mb-0"> {{ translate('Ads_total_amount') }}</div>
                                            </div>

                                        </div>
                                        <!-- </div>
                                            </div>
                                        </div> -->
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="setting-content">
                        <div class="row">
                            <div class="card w-100">
                                <div class="card-header">
                                    <h5 class="mb-0"> {{ translate('Service_Commission') }}</h5>
                                </div>

                                <div class="my-5">
                                    <form action="{{ route('admin.donate_management.ad_trust.commission_update',[$old_data['id']]) }}" method="post">
                                        @csrf
                                        <div class="row p-2">
                                            <div class="col-md-6 my-2">
                                                <div class="row">
                                                    <div class="col-6 text-center" style="align-content: center;">
                                                        <p style="font-size: 15px; margin: 0px"><b> {{ translate('Ad_commission') }}</b>
                                                        </p>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="input-group">
                                                            <input type="number" class="form-control" required name="admin_commission" value="{{ $old_data['admin_commission']??'0'}}">
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">%</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6 my-2">
                                            </div>
                                            <div class="col-12 text-end">
                                                <button type="submit" class="btn btn-primary mr-5"> {{ translate('Update') }}</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="transaction-content">
                        <div class="row">
                            <div class="card w-100">
                                <div class="card-header">
                                    <h5 class="mb-0"> {{ translate('Donation_transaction') }}</h5>
                                </div>

                                <div class="my-5">
                                    <div class="col-md-12">
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
                                                            <input type="hidden" name='type' value='donate_tran'>
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
                                                            <th>{{ translate('User_Name') }}</th>
                                                            <th>{{ translate('User_Phone') }}</th>
                                                            <th>{{ translate('TXN_id') }}</th>
                                                            <th>{{ translate('Amount') }}</th>
                                                            <th>{{ translate('admin_commission') }}</th>
                                                            <th>{{ translate('Final_amount') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <!-- Loop through items -->
                                                        @foreach($ads_transaction as $key => $items)
                                                        <tr>
                                                            <td>{{$ads_transaction->firstItem()+$key}}</td>
                                                            <td>{{ $items['trans_id'] }}</td>
                                                            <td>{{ ($items['users']['name']??'') }}</td>
                                                            <td>{{ ($items['users']['phone']??'') }}</td>
                                                            <td>{{ ($items['transaction_id']??'') }}</td>
                                                            <td>₹{{ ($items['amount']??'') }}</td>
                                                            <td>₹{{ ($items['admin_commission']??'') }}</td>
                                                            <td>₹{{ ($items['final_amount']??'') }}</td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                    <fbody>
                                                        <th colspan='5' class='font-weight-bold'>Total</th>
                                                        <th class='font-weight-bold'>₹{{ \App\Models\DonateAllTransaction::whereIn('type',['donate_ads'])->where('amount_status',1)->where('ads_id',$id)->sum('amount') }}</th>
                                                        <th class='font-weight-bold'>₹{{ \App\Models\DonateAllTransaction::whereIn('type',['donate_ads'])->where('amount_status',1)->where('ads_id',$id)->sum('admin_commission') }}</th>
                                                        <th class='font-weight-bold'>₹{{ \App\Models\DonateAllTransaction::whereIn('type',['donate_ads'])->where('amount_status',1)->where('ads_id',$id)->sum('final_amount') }}</th>
                                                    </fbody>
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('script')
<script>
    document.getElementById('verification_Status_ads').addEventListener('change', function() {
        var selectedOption = this.options[this.selectedIndex];
        var url = selectedOption.getAttribute('data-href');
        var type = selectedOption.getAttribute('data-type');

        if (type === '1') {
            Swal.fire({
                title: 'Enter the amount required for the Ads Approval',
                input: 'number',
                inputAttributes: {
                    'placeholder': 'Enter an amount',
                    'min': '1',
                    'max': '999999',
                },
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Submit',
                preConfirm: (number) => {
                    if (!number) {
                        Swal.showValidationMessage('Please enter a valid Amount');
                        return false;
                    }
                    return number;
                }
            }).then((result) => {
                if (result && result.value) {
                    url += `?amount=${result.value}`;
                    window.location.href = url;
                }
            });
        } else if (url) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You want to change the verification status?",
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, change it!'
            }).then((result) => {
                if (result.value) {
                    window.location.href = url;
                }
            });
        }
    });
</script>
@endpush