<div class="card-body">
    <div class="d-flex flex-wrap gap-3 justify-content-between">
        <div class="media flex-column flex-sm-row gap-3">

            <img class="avatar avatar-170 rounded-0" src="{{  getValidImage(path: 'storage/app/public/tour_and_travels/tour_visit/'.$getData['tour_image'], type: 'backend-product') }}" alt="Image">

            <div class="media-body">
                <div class="d-block">
                    <h2 class="mb-2 pb-1">{{ ($getData['tour_name']??"") }} ({{ ($getData['tour_id']??"") }})</h2>
                    @if($view_type == 2)
                    <div class="d-flex gap-3 flex-wrap mb-3 lh-1">
                        <div class="review-hover position-relative cursor-pointer d-flex gap-2 align-items-center">
                            <i class="tio-star"></i>
                            <a href="javascript:" class="text-dark"> {{ number_format(\App\Models\TourReviews::where('tour_id', ($getData['id'] ?? "")) ->where('status', 1) ->avg('star'),2) }} Ratings</a>
                        </div>
                        <!-- <span class="border-left"></span> -->
                        <span class="border-left"></span>
                        <a href="javascript:" class="text-dark">{{ \App\Models\TourReviews::where('tour_id',($getData['id']??""))->where('status',1)->count()}} Reviews</a>
                    </div>
                    @endif
                    <a href="{{ route('tour.index')}}" class="btn btn-outline--primary px-4" target="_blank"><i class="tio-globe"></i>
                        View live
                    </a>
                </div>
            </div>
        </div>

    </div>
    <hr>
    <div class="d-flex gap-3 flex-wrap flex-lg-nowrap">
        <div class="border p-3 w-170">
            <div class="d-flex flex-column mb-1">
                <h6 class="font-weight-normal">Total services :</h6>
                <h3 class="text-primary fs-18">1</h3>
            </div>
            <div class="d-flex flex-column">
                <h6 class="font-weight-normal">Total orders :
                    <span class="text-primary font-weight-bold h4">
                        @php
                        echo \App\Models\TourOrder::where(['tour_id'=>$getData['id']])->where('amount_status',1)->count();
                        @endphp
                    </span>
                </h6>
                <h6 class="font-weight-normal">Pending orders :
                    <span class="text-primary font-weight-bold h4">
                        @php
                        echo \App\Models\TourOrder::where(['tour_id'=>$getData['id']])->where('amount_status',1)->where('status',0)->where('drop_status',0)->count();
                        @endphp
                    </span>
                </h6>
                <h6 class="font-weight-normal">confirm orders :
                    <span class="text-primary font-weight-bold h4">
                        @php
                        echo \App\Models\TourOrder::where(['tour_id'=>$getData['id']])->where('amount_status',1)->where('status',1)->where('drop_status',0)->count();
                        @endphp
                    </span>
                </h6>

                <h6 class="font-weight-normal">complete orders :
                    <span class="text-primary font-weight-bold h4">
                        @php
                        echo \App\Models\TourOrder::where(['tour_id'=>$getData['id']])->where('amount_status',1)->where('status',1)->where('drop_status',1)->count();
                        @endphp
                    </span>
                </h6>

                <h6 class="font-weight-normal">cancel orders :
                    <span class="text-primary font-weight-bold h4">
                        @php
                        echo \App\Models\TourOrder::where(['tour_id'=>$getData['id']])->where('amount_status',1)->where('status',2)->where('refund_status',1)->where('drop_status',0)->count();
                        @endphp
                    </span>
                </h6>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">

                <div class="card-body mt-3">
                    <div class="row justify-content-between align-items-center g-2 mb-3">
                        <div class="col-sm-6">
                            <h4 class="d-flex align-items-center text-capitalize gap-10 mb-0">
                                <img width="20" class="mb-1" src="{{ dynamicAsset(path: 'public/assets/back-end/img/admin-wallet.png') }}" alt="">
                                {{ translate('tour_amount') }}
                            </h4>
                        </div>
                        <div class='col-sm-6 text-end'>
    
                        </div>
                    </div>
    
                    <div class="row g-2" id="order_stats">
                        <div class="col-lg-3 col-md-3">
                            <div class="card h-100 d-flex justify-content-center align-items-center">
                                <div class="card-body d-flex flex-column gap-10 align-items-center justify-content-center">
                                    <img width="48" class="mb-2" src="{{ dynamicAsset(path: 'public/assets/back-end/img/withdraw.png') }}" alt="">
                                    <h3 class="for-card-count mb-0 fz-24">
                                    {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: (\App\Models\TourOrder::where('status',1)->where('amount_status',1)->where('tour_id',$getData['id'])->sum('amount')??0)), currencyCode: getCurrencyCode()) }}
                                    </h3>
                                    <div class="font-weight-bold text-capitalize mb-30">
                                        {{ translate('total_amount') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3">
                            <div class="card h-100 d-flex justify-content-center align-items-center">
                                <div class="card-body d-flex flex-column gap-10 align-items-center justify-content-center">
                                    <img width="48" class="mb-2" src="{{ dynamicAsset(path: 'public/assets/back-end/img/withdraw.png') }}" alt="">
                                    <h3 class="for-card-count mb-0 fz-24">
                                    {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: (\App\Models\TourOrder::where('status',1)->where('amount_status',1)->where('tour_id',$getData['id'])->sum('final_amount')??0)), currencyCode: getCurrencyCode()) }}
                                    </h3>
                                    <div class="font-weight-bold text-capitalize mb-30">
                                        {{ translate('company_amount') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3">
                            <div class="card h-100 d-flex justify-content-center align-items-center">
                                <div class="card-body d-flex flex-column gap-10 align-items-center justify-content-center">
                                <img width="48" class="mb-2" src="{{ dynamicAsset(path: 'public/assets/back-end/img/tcg.png') }}" alt="">
                                    <h3 class="for-card-count mb-0 fz-24">
                                    {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: (\App\Models\TourOrder::where('status',1)->where('amount_status',1)->where('tour_id',$getData['id'])->sum('admin_commission')??0)), currencyCode: getCurrencyCode()) }}
                                    </h3>
                                    <div class="font-weight-bold text-capitalize mb-30">
                                    {{ translate('Total_commission_given') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3">
                            <div class="card h-100 d-flex justify-content-center align-items-center">
                                <div class="card-body d-flex flex-column gap-10 align-items-center justify-content-center">
                                    <img width="48" class="mb-2" src="{{ dynamicAsset(path: 'public/assets/back-end/img/ttg.png') }}" alt="">
                                    <h3 class="for-card-count mb-0 fz-24">
                                    {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: (\App\Models\TourOrder::where('status',1)->where('amount_status',1)->where('tour_id',$getData['id'])->sum('gst_amount')??0)), currencyCode: getCurrencyCode()) }}
                                    </h3>
                                    <div class="font-weight-bold text-capitalize mb-30">
                                    {{ translate('Total_tax_given') }}
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
<!--  -->