<div class="card-body">
    <div class="d-flex flex-wrap gap-3 justify-content-between">
        <div class="media flex-column flex-sm-row gap-3">
            @php
            $galleryImages = json_decode($trust_data['gallery_image'], true);
            $randomImage ='';
            if (!empty($galleryImages)) {
            $randomIndex = array_rand($galleryImages);
            $randomImage = $galleryImages[$randomIndex];
            }
            @endphp
            <img class="avatar avatar-170 rounded-0" src="{{  getValidImage(path: 'storage/app/public/donate/trust/'.$randomImage, type: 'backend-product') }}" alt="Image">

            <div class="media-body">
                <div class="d-block">

                    <h2 class="mb-2 pb-1"></h2>
                    <div class="d-flex gap-3 flex-wrap mb-3 lh-1">
                        <div class="review-hover position-relative cursor-pointer d-flex gap-2 align-items-center">
                            <span class="key">{{translate('Trust_Name')}}</span>
                            <span>:</span>
                            <span class="value text-capitalize">{{$trust_data['trust_name']}}</span>
                        </div>
                        
                    </div>
                    @if($trust_data['slug'])
                    <a href="{{ route('all-donate_trust',['slug'=>$trust_data['slug']])}}" class="btn btn-outline--primary px-4" target="_blank"><i class="tio-globe"></i>
                        View live
                    </a>
                    @endif
                </div>
            </div>
        </div>
        <div class="">

            <div class="d-flex justify-content-start gap-2">


            </div>
        </div>
    </div>
    <hr>
    <div class="d-flex gap-3 flex-wrap flex-lg-nowrap">
        <div class="border p-3 w-170">
            <div class="d-flex flex-column mb-1">
                <h6 class="font-weight-normal">Total Ads :</h6>
                <h3 class="text-primary fs-18">{{ \App\Models\DonateAds::where('trust_id',$trust_data['id'])->count() }}</h3>
            </div>
        </div>
        <div class="row">
            <div class="col-xxl-12">
                <div class="bg-light p-3 border border-primary-light rounded">
                    <h4 class="mb-3 text-capitalize">
                        {{ translate('bank_information') }}
                    </h4>

                    <div class="d-flex gap-5">
                        <div class="pair-list">
                            <div>
                                <span class="key text-nowrap">{{ translate('Holder_name ') }}</span>
                                <span class="px-2">:</span>
                                <span class="value ">{{$trust_data['beneficiary_name']}}</span>
    
                            </div>
                            <div>
                                <span class="key text-nowrap">{{ translate('bank_Name') }}</span>
                                <span class="px-2">:</span>
                                <span class="value">{{$trust_data['bank_name']}}</span>

                            </div>

                        </div>
                        <div class="pair-list">
                            <div>
                                <span class="key text-nowrap">{{ translate('IFSC') }}</span>
                                <span class="px-2">:</span>
                                <span class="value ">{{$trust_data['ifsc_code']}}</span>
    
                            </div>

                            <div>
                                <span class="key text-nowrap">{{ translate('A/C_No') }}</span>
                                <span class="px-2">:</span>
                                <span class="value ">{{$trust_data['account_no']}}</span>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
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
                    <img width="48" class="mb-2" src="{{ dynamicAsset(path: 'public/assets/back-end/img/withdraw.png') }}" alt="">
                    <h3 class="for-card-count mb-0 fz-24">
                    {{ ($trust_data['trust_total_amount']??0) }} ₹
                    </h3>
                    <div class="font-weight-bold text-capitalize mb-30">
                        {{ translate('withdrawable_balance') }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="row g-2">

                <div class="col-md-6">
                    <div class="card card-body h-100 justify-content-center">
                        <div class="d-flex gap-2 justify-content-between align-items-center">
                            <div class="d-flex flex-column align-items-start">
                                <h3 class="mb-1 fz-24">
                                {{ \App\Models\DonateAllTransaction::whereIn('type',['donate_ads','donate_trust'])->where('trust_id',$trust_data['id'])->where('amount_status',1)->sum('amount')}}
                                    ₹
                                </h3>
                                <div class="text-capitalize mb-0"> {{ translate('Trust_amount') }}</div>
                            </div>
                            <div>
                                <img width="40" class="mb-2" src="{{ dynamicAsset(path: 'public/assets/back-end/img/withdraw-icon.png') }}" alt="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card card-body h-100 justify-content-center">
                        @php
                        $pending_amount = ($trust_data['trust_req_withdrawal_amount']??0);
                        @endphp
                        <div class="d-flex {{ (($pending_amount <= 0)?'gap-4 justify-content-between':'gap-4') }} align-items-center">
                            <div class="d-flex flex-column align-items-start">
                                <h3 class="mb-1 fz-24">
                                    {{$pending_amount}} ₹
                                </h3>
                                <div class="text-capitalize mb-0"> {{ translate('Pending_withdraw') }}</div>
                            </div>
                            @if($pending_amount > 0)
                            <div class="d-flex flex-column justify-content-between ">
                                <a class='btn btn-success btn-sm' href="{{ route('admin.donate_management.trust.requestapprove',[$trust_data['id'],1])}}">approve</a>
                                <a class='btn btn-danger btn-sm' href="{{ route('admin.donate_management.trust.requestapprove',[$trust_data['id'],2])}}">cancel</a>
                            </div>
                            @endif
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
                               {{ ($trust_data['trust_total_withdrawal']??0) }} ₹
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
                                {{ ($trust_data['admin_commission']??0) }} ₹
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
            </div>
        </div>
    </div>
</div>