<div class="tab-pane fade" id="month" role="tabpanel" aria-labelledby="month-tab">
    <div class="row mt-3">
        <div class="col-md-12 card border-0 box-shadow mygap-bottom">
            <div class="card-body mybgcolor">
                <p>राशि अक्षर:
                    {{ isset($monthRashiData[0]['akshar']) ? $monthRashiData[0]['akshar'] : 'No Data Available' }}
                </p>

                <div id="monthHindiDetail" >{!! isset($monthRashiData[1]['detail'])?$monthRashiData[1]['detail']:'No Data Available' !!}</div>
                <div id="monthEnglishDetail" style="display: none;">{!! isset($monthRashiData[0]['detail'])?$monthRashiData[0]['detail']:'No Data Available' !!}</div>
            </div>
        </div>
    </div>
</div>
