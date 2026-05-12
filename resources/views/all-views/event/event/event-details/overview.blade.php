@php 
use App\Utils\Helpers;
@endphp
<div class="card-body">
    <div class="d-flex flex-wrap gap-3 justify-content-between">
        <div class="media flex-column flex-sm-row gap-3">
            @if($view_type == 1)
            <img class="avatar avatar-170 rounded-0" src="{{  getValidImage(path: 'storage/app/public/event/organizer/'.$getData['image'], type: 'backend-product') }}" alt="Image">
            @else
            <img class="avatar avatar-170 rounded-0" src="{{  getValidImage(path: 'storage/app/public/event/events/'.$getData['event_image'], type: 'backend-product') }}" alt="Image">
            @endif
            <div class="media-body">
                <div class="d-block">
                    @if($view_type == 1)
                    <h2 class="mb-2 pb-1">{{ ($getData['organizer_name']??"") }} ({{ ($getData['unique_id']??"") }})</h2>
                    @else
                    <h2 class="mb-2 pb-1">{{ ($getData['event_name']??"") }} ({{ ($getData['unique_id']??"") }})</h2>

                    @endif
                    @if($view_type == 2)
                    <div class="d-flex gap-3 flex-wrap mb-3 lh-1">
                        <div class="review-hover position-relative cursor-pointer d-flex gap-2 align-items-center">
                            <i class="tio-star"></i>
                            <a href="javascript:" class="text-dark"> {{ number_format(\App\Models\EventsReview::where('event_id', ($getData['id'] ?? "")) ->where('status', 1) ->avg('star'),2) }} Ratings</a>
                        </div>
                        <!-- <span class="border-left"></span> -->
                        <span class="border-left"></span>
                        <a href="javascript:" class="text-dark">{{ \App\Models\EventsReview::where('event_id',($getData['id']??""))->where('status',1)->count()}} Reviews</a>
                    </div>
                    @endif
                    <a href="{{ route('event-details',[$getData['slug']])}}" class="btn btn-outline--primary px-4" target="_blank"><i class="tio-globe"></i>
                        View live
                    </a>
                </div>
            </div>
        </div>
        <div class="">

            <div class="d-flex justify-content-start gap-2">
                <!-- <span class="btn btn-outline-warning btn-sm square-btn reject-artist_data" title="Reject Event" data-id="reject-{{ ($getData['id']??'')}}">
               <i class="tio-blocked"></i>
            </span>
            <form action="" method="post" id="reject-{{ ($getData['id']??'')}}">
               @csrf
               <input type="hidden" name="status" value="{{ ($getData['status']??'')}}">
            </form> -->

            </div>
        </div>
    </div>
    <hr>
    <div class="d-flex gap-3 flex-wrap flex-lg-nowrap">
        <div class="border p-3 w-170">
            <div class="d-flex flex-column mb-1">
                <h6 class="font-weight-normal">Total services :</h6>
                <h3 class="text-primary fs-18">2</h3>
            </div>
            @if($view_type == 2)
            <div class="d-flex flex-column">
                <h6 class="font-weight-normal">Total orders :</h6>
                <h3 class="text-primary fs-18">
                    @php
                    echo \App\Models\EventOrder::where(['event_id'=>$getData['id']])->where('transaction_status',1)->where('status',1)->count();
                    @endphp
                </h3>
            </div>
            @endif
        </div>
        <div class="row">
            <div class="col-sm-12 mb-2">
                <div class="row">
                    <div class="col-md-4">
                        <h4 class="mb-3 text-capitalize">{{ translate('event_organizer') }} </h4>
                        <div class="pair-list">
                            <div>
                                <span class="key">{{ translate('name') }} </span>
                                <span>:</span>
                                @if($view_type == 1)
                                <span class="value text-capitalize">{{ ($getData['organizer_name']??'')}}</span>
                                @else
                                <span class="value text-capitalize">{{ ($getData['organizers']['organizer_name']??'')}}</span>
                                @endif
                            </div>

                            <div>
                                <span class="key">{{ translate('email') }} </span>
                                <span>:</span>
                                @if($view_type == 1)
                                <span class="value text-capitalize">{{ ($getData['email_address']??'')}}</span>
                                @else
                                <span class="value">{{ ($getData['organizers']['email_address']??'')}}</span>
                                @endif
                            </div>

                            <div>
                                <span class="key">{{ translate('phone') }} </span>
                                <span>:</span>

                                @if($view_type == 1)
                                <span class="value text-capitalize">{{ ($getData['contact_number']??'')}}</span>
                                @else
                                <span class="value">{{ ($getData['organizers']['contact_number']??'')}}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        @if($view_type == 2)
                        <h4 class="mb-3 text-capitalize">{{ translate('Event_artist') }} </h4>
                        <div class="pair-list">
                            <div>
                                <span class="key">{{ translate('name') }} </span>
                                <span>:</span>
                                <span class="value text-capitalize">{{ ($getData['eventArtist']['name']??'')}}</span>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-xxl-6">
                <div class="bg-light p-3 border border-primary-light rounded">
                    <h4 class="mb-3 text-capitalize">
                        {{ translate('bank_information') }}
                    </h4>

                    <div class="d-flex gap-5">
                        <div class="pair-list">
                            <div>
                                <span class="key text-nowrap">{{ translate('bank_Name') }}</span>
                                <span class="px-2">:</span>
                                @if($view_type == 1)
                                <span class="value ">{{ ($getData['bank_name']??'')}}</span>
                                @else
                                <span class="value ">{{ ($getData['organizers']['bank_name']??'')}}</span>
                                @endif
                            </div>

                            <div>
                                <span class="key text-nowrap">{{ translate('IFSC') }}</span>
                                <span class="px-2">:</span>
                                @if($view_type == 1)
                                <span class="value ">{{ ($getData['ifsc_code']??'')}}</span>
                                @else
                                <span class="value">{{ ($getData['organizers']['ifsc_code']??'')}}</span>
                                @endif
                            </div>
                        </div>
                        <div class="pair-list">
                            <div>
                                <span class="key text-nowrap">{{ translate('Holder_name ') }}</span>
                                <span class="px-2">:</span>
                                @if($view_type == 1)
                                <span class="value ">{{ ($getData['beneficiary_name']??'')}}</span>
                                @else
                                <span class="value">{{ ($getData['organizers']['beneficiary_name']??'')}}</span>
                                @endif
                            </div>

                            <div>
                                <span class="key text-nowrap">{{ translate('A/C_No') }}</span>
                                <span class="px-2">:</span>
                                @if($view_type == 1)
                                <span class="value ">{{ ($getData['account_no']??'')}}</span>
                                @else
                                <span class="value">{{ ($getData['organizers']['account_no']??'')}}</span>
                                @endif
                            </div>
                        </div>
                        <div class="pair-list">
                            <div>
                                <span class="key text-nowrap"> {{ translate('Branch') }}</span>
                                <span class="px-2">:</span>
                                @if($view_type == 1)
                                <span class="value ">{{ ($getData['branch_name']??'')}}</span>
                                @else
                                <span class="value">{{ ($getData['organizers']['branch_name']??'')}}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--  -->
@if (Helpers::Employee_modules_permission('Dashboard', 'Wallet', 'View'))
<div class="card-body mt-3">
    <div class="row justify-content-between align-items-center g-2 mb-3">
        <div class="col-sm-6">
            <h4 class="d-flex align-items-center text-capitalize gap-10 mb-0">
                <img width="20" class="mb-1" src="{{ dynamicAsset(path: 'public/assets/back-end/img/admin-wallet.png') }}" alt="">
                {{ translate('event_wallet') }}
            </h4>
        </div>
        <div class='col-sm-6 text-end'>

        </div>
    </div>

    <div class="row g-2" id="order_stats">
        <div class="col-lg-4">
            <div class="card h-100 d-flex justify-content-center align-items-center">
                <div class="card-body d-flex flex-column gap-10 align-items-center justify-content-center">
                    <img width="48" class="mb-2" src="{{ dynamicAsset(path: 'public/assets/back-end/img/withdraw.png') }}" alt="">
                    <h3 class="for-card-count mb-0 fz-24">
                        @if($view_type == 1)
                        {{ $getData['org_withdrawable_ready']??'0.00'}}₹
                        @else
                        {{ $getData['organizers']['org_withdrawable_ready']??'0.00'}}₹
                        @endif
                    </h3>
                    <div class="font-weight-bold text-capitalize mb-30">
                        {{ translate('withdrawable_balance') }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="row g-2">
                @if($view_type == 2)
                <div class="col-md-6">
                    <div class="card card-body h-100 justify-content-center">
                        <div class="d-flex gap-2 justify-content-between align-items-center">
                            <div class="d-flex flex-column align-items-start">
                                <h3 class="mb-1 fz-24">
                                    @php
                                    echo \App\Models\EventOrder::where(['event_id'=>$getData['id'],'transaction_status'=>1,'status'=>1])->sum('amount');
                                    @endphp
                                    ₹
                                </h3>
                                <div class="text-capitalize mb-0"> {{ translate('Event_Order_amount') }}</div>
                            </div>
                            <div>
                                <img width="40" class="mb-2" src="{{ dynamicAsset(path: 'public/assets/back-end/img/withdraw-icon.png') }}" alt="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6"></div>
                @endif
                <div class="col-md-6">
                    <div class="card card-body h-100 justify-content-center">
                        <div class="d-flex gap-4 justify-content-between align-items-center">
                            <div class="d-flex flex-column align-items-start">
                                <h3 class="mb-1 fz-24">
                                    @if($view_type == 1)
                                    {{ $getData['org_withdrawable_pending']??'0.00'}}₹
                                    @else
                                    {{ $getData['organizers']['org_withdrawable_pending']??'0.00'}}₹
                                    @endif
                                </h3>
                                <div class="text-capitalize mb-0"> {{ translate('Pending_withdraw') }}</div>
                            </div>
                            <div>
                                <img width="40" class="mb-2" src="{{ dynamicAsset(path: 'public/assets/back-end/img/pw.png') }}" alt="">
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-md-6">
                    <div class="card card-body h-100 justify-content-center">
                        <div class="d-flex gap-2 justify-content-between align-items-center">
                            <div class="d-flex flex-column align-items-start">
                                <h3 class="mb-1 fz-24">
                                    @if($view_type == 1)
                                    {{ $getData['org_collected_cash']??"0.00" }}₹
                                    @else
                                    {{ $getData['organizers']['org_collected_cash']??"0.00" }}₹
                                    @endif
                                </h3>
                                <div class="text-capitalize mb-0"> {{ translate('Collected_cash') }}</div>
                            </div>
                            <div>
                                <img width="40" src="{{ dynamicAsset(path: 'public/assets/back-end/img/cc.png') }}" alt="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card card-body h-100 justify-content-center">
                        <div class="d-flex gap-2 justify-content-between align-items-center">
                            <div class="d-flex flex-column align-items-start">
                                <h3 class="mb-1 fz-24">
                                    @if($view_type == 1)
                                    {{ $getData['org_total_commission']??'0.00'}}₹
                                    @else
                                    {{ $getData['organizers']['org_total_commission']??'0.00'}}₹
                                    @endif
                                </h3>
                                <div class="text-capitalize mb-0">
                                    {{ translate('Total_commission_given') }}
                                </div>
                            </div>
                            <div>
                                <img width="40" src="{{ dynamicAsset(path: 'public/assets/back-end/img/tcg.png') }}" alt="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card card-body h-100 justify-content-center">
                        <div class="d-flex gap-2 justify-content-between align-items-center">
                            <div class="d-flex flex-column align-items-start">
                                <h3 class="mb-1 fz-24">
                                    @if($view_type == 1)
                                    {{ $getData['org_total_tax']??'0.00'}}₹
                                    @else
                                    {{ $getData['organizers']['org_total_tax']??'0.00'}}₹
                                    @endif
                                </h3>
                                <div class="text-capitalize mb-0">
                                    {{ translate('Total_tax_given') }}
                                </div>
                            </div>
                            <div>
                                <img width="40" src="{{ dynamicAsset(path: 'public/assets/back-end/img/ttg.png') }}" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif