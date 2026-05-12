<div class="w-100">
    <div class="card-header">
        <h5 class="mb-0"> {{ translate('Service_Charges') }}</h5>
    </div>
    <div class="m-3">

        <div class="row">
            <div class="col-lg-3 col-sm-4 col-12">
                <div class="row bg-primary my-2" style="border-radius: 15px; margin:0px; padding:12px 0;">
                    <div class="col-4 mt-2">
                        <i class="tio-invisible" style="font-size: 35px; color: white;"></i>
                    </div>
                    <div class="col-8 text-right">
                        <p style="color: white; font-size: 15px; margin-bottom: 8px;">
                            <b>{{ translate('Live_Stream') }}</b>
                        </p>
                        <p style="color: white; font-size: 15px; margin-bottom: 8px;">
                            <b>₹ {{ $getData['commission_live']??'0'}}</b>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-4 col-12">
                <div class="row bg-danger my-2" style="border-radius: 15px; margin:0px; padding:12px 0;">
                    <div class="col-4 mt-2">
                        <i class="tio-armchair" style="font-size: 30px; color: white;"></i>
                    </div>
                    <div class="col-8 text-right">
                        <p style="color: white; font-size: 15px; margin-bottom: 8px;">
                            <b> {{ translate('Seats') }}</b>
                        </p>
                        <p style="color: white; font-size: 15px; margin-bottom: 8px;">
                            <b>₹ {{ $getData['commission_seats']??'0'}}</b>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-4 col-12">
                
            </div>
            <div class="col-lg-3 col-sm-4 col-12">
                
            </div>
            <div class="col-lg-12 col-sm-8 col-12"></div>
        </div>
    </div>

    
</div>