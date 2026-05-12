<div class="card w-100">
    <div class="card-header">
        <h5 class="mb-0"> {{ translate('Service_Commission') }}</h5>
    </div>

    <div class="my-5">
        <form action="{{ route('admin.tour_visits.commission_update',[$getData['id']]) }}" method="post">
            @csrf
            <input type="hidden" name="type" value="commission">
            <div class="row">
                <div class="col-md-6 my-2">
                    <div class="row">
                        <div class="col-4 text-center" style="align-content: center;">
                            <p style="font-size: 15px; margin: 0px"><b> {{ translate('platform_charge') }}</b>
                            </p>
                        </div>
                        <div class="col-4">
                            <div class="input-group">
                                <input type="number" class="form-control" required name="tour_commission" value="{{ $getData['tour_commission']??'0'}}">
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