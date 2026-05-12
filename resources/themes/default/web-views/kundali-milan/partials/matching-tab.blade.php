<div class="tab-pane fade" id="matching-info" role="tabpanel" aria-labelledby="matching-info-tab">
    <div class="row mt-3">
        <div class="col-md-12">
            <div class="tab-details-block">
                <div class="row">
                    <div class="col-xl-3 col-md-3 pb-3 mt-2">
                        <div class="milan-info-block">
                            <h5 class="font-weight-bold" style="color: #f3e2aa">अष्टकूट</h5>
                            <h5 class="text-white"> <span id="ashtakoot">{{$matchData['ashtakoota']['received_points']}}</span> / 36 </h5>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-3 pb-3 mt-2">
                        <div class="milan-info-block">
                            <h5 class="font-weight-bold" style="color: #f3e2aa">मांगलिक मिलान</h5>
                            <h5 id="manglik" class="text-white">{{$matchData['manglik']['status']==true?'हाँ':'नहीं'}}</h5>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-3 pb-3 mt-2">
                        <div class="milan-info-block">
                            <h5 class="font-weight-bold" style="color: #f3e2aa">रज्जू दोष</h5>
                            <h5 id="rajju" class="text-white">{{$matchData['rajju_dosha']['status']==true?'हाँ':'नहीं'}}</h5>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-3 pb-3 mt-2">
                        <div class="milan-info-block">
                            <h5 class="font-weight-bold" style="color: #f3e2aa">वेध दोष</h5>
                            <h5 id="vedha" class="text-white">{{$matchData['vedha_dosha']['status']==true?'हाँ':'नहीं'}}</h5>
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <h5 class="font-weight-bold">निष्कर्ष</h5>
                        <p id="conclusion">{{$matchData['conclusion']['match_report']}}</p>
                    </div>
                </div>
           </div>
        </div>
    </div>
</div>