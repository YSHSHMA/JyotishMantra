<div class="card w-100">
    <div class="card-header">
        <h5 class="mb-0"> {{ translate('Service_Commission') }}</h5>
    </div>

    <div class="my-5">
        <form action="{{ route('admin.donate_management.trust.commission_update',[$trust_data['id']]) }}" method="post">
            @csrf
            <input type="hidden" name="type" value="commission">
            <div class="row p-2">
                <div class="col-md-6 my-2">
                    <div class="row">
                        <div class="col-6 text-center" style="align-content: center;">
                            <p style="font-size: 15px; margin: 0px"><b> {{ translate('Ad_with_commission') }}</b>
                            </p>
                        </div>
                        <div class="col-6">
                            <div class="input-group">
                                <input type="number" class="form-control" required name="ad_commission" value="{{ $trust_data['ad_commission']??'0'}}" min="1" onkeyup="this.value = this.value.replace('+',''); if (this.value < 1) this.value = 1;">
                                <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 my-2">
                    <div class="row">
                        <div class="col-6 text-center" style="align-content: center;">
                            <p style="font-size: 15px; margin: 0px"><b> {{ translate('trust_donate_commission') }}</b></p>
                        </div>
                        <div class="col-6">
                            <div class="input-group">
                                <input type="number" class="form-control" required name="donate_commission" value="{{ $trust_data['donate_commission']??'0'}}" min="1" onkeyup="this.value = this.value.replace('+',''); if (this.value < 1) this.value = 1;">
                                <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 my-2">
                    <div class="row">
                        <div class="col-6 text-center" style="align-content: center;">
                            <p style="font-size: 15px; margin: 0px"><b> {{ translate('vip_darshan_commission') }}</b></p>
                        </div>
                        <div class="col-6">
                            <div class="input-group">
                                <input type="number" class="form-control" required name="vip_darshan_commission" value="{{ $trust_data['vip_darshan_commission']??'0'}}" min="1" onkeyup="this.value = this.value.replace('+',''); if (this.value < 1) this.value = 1;">
                                <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-primary mr-5"> {{ translate('Update') }}</button>
                </div>
            </div>
        </form>
    </div>    
</div>