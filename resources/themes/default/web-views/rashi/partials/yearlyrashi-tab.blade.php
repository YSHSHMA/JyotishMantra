<div class="tab-pane fade" id="varshik" role="tabpanel" aria-labelledby="varshik-tab">
    <div class="row mt-3">
        <div class="col-md-12 card border-0 box-shadow mygap-bottom">
            <div class="card-body mybgcolor">
                <p>राशि अक्षर:
                    {{ isset($yearRashiData[0]['akshar']) ? $yearRashiData[0]['akshar'] : 'No Data Available' }}
                </p>

                <div id="yearHindiDetail">{!! isset($yearRashiData[1]['detail'])?$yearRashiData[1]['detail']:'No Data Available' !!}</div>
                <div id="yearEnglishDetail" style="display: none;">{!! isset($yearRashiData[0]['detail'])?$yearRashiData[0]['detail']:'No Data Available' !!}</div>
            </div>
        </div>
    </div>
</div>