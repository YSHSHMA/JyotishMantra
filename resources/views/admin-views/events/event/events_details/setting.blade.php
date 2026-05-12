<div class="card w-100">
    <div class="card-header">
        <h5 class="mb-0"> {{ translate('Service_Commission') }}</h5>
    </div>

    <div class="my-5">
        <form action="{{ route('admin.event-managment.event.commission_update',[$getData['id']]) }}" method="post">
            @csrf
            <input type="hidden" name="type" value="commission">
            <div class="row">
                <div class="col-md-6 my-2">
                    <div class="row">
                        <div class="col-4 text-center" style="align-content: center;">
                            <p style="font-size: 15px; margin: 0px"><b> {{ translate('Live_Stream') }}</b>
                            </p>
                        </div>
                        <div class="col-4">
                            <div class="input-group">
                                <input type="number" class="form-control" required name="live_stream_commission" value="{{ $getData['commission_live']??'0'}}">
                                <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 my-2">
                    <div class="row">
                        <div class="col-4 text-center" style="align-content: center;">
                            <p style="font-size: 15px; margin: 0px"><b> {{ translate('Seats') }}</b></p>
                        </div>
                        <div class="col-4">
                            <div class="input-group">
                                <input type="number" class="form-control" required name="seats_commission" value="{{ $getData['commission_seats']??'0'}}">
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