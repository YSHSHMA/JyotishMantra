<div class="tab-pane fade show active" id="daily" role="tabpanel" aria-labelledby="daily-tab">
    <div class="row mt-3">
        <div class="col-lg-6 col-sm-6 col-xs-6">
            <div class="rashi-box card-body mybgcolor mygap-bottom text-center">
                <div class="box-icon">
                    <img src="{{ asset('public/assets/front-end/img/rashi/personal.png') }}"
                        width="40">
                </div>
                <h2 class="h4 mb-4 text-center font-weight-bolder font-size-18 my-3" id="personal-life-heading">व्यक्तिगत जीवन</h2>
                <p id="personal-life">{{isset($dailyRashiData['prediction']['personal_life'])?$dailyRashiData['prediction']['personal_life']:''}}</p>
            </div>
        </div>
        <div class="col-lg-6 col-sm-6 col-xs-6">
            <div class="card-body mybgcolor rashi-box text-center">
                <div class="box-icon">
                    <img src="{{ asset('public/assets/front-end/img/rashi/profession.png') }}"
                        width="40">
                </div>
                <h2 class="h4 mb-4 text-center font-weight-bolder font-size-18 my-3" id="profession-heading">व्यापार/व्यवसाय</h2>
                <p id="profession">{{isset($dailyRashiData['prediction']['profession'])?$dailyRashiData['prediction']['profession']:''}}</p>
            </div>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-lg-6 col-sm-6 col-xs-6 mygap-bottom">
            <div class="card-body mybgcolor rashi-box text-center">
                <div class="box-icon">
                    <img src="{{ asset('public/assets/front-end/img/rashi/health.png') }}"
                        width="40">
                </div>
                <h2 class="h4 mb-4 text-center font-weight-bolder font-size-18 my-3" id="health-heading">स्वास्थ्य</h2>
                <p id="health">{{isset($dailyRashiData['prediction']['health'])?$dailyRashiData['prediction']['health']:''}}</p>
            </div>
        </div>
        <div class="col-lg-6 col-sm-6 col-xs-6 mygap-bottom">
            <div class="card-body mybgcolor rashi-box text-center">
                <div class="box-icon">
                    <img src="{{ asset('public/assets/front-end/img/rashi/travel.png') }}"
                        width="40">
                </div>
                <h2 class="h4 mb-4 text-center font-weight-bolder font-size-18 my-3" id="travel-heading">यात्रा</h2>
                <p id="travel">{{isset($dailyRashiData['prediction']['travel'])?$dailyRashiData['prediction']['travel']:''}}</p>
            </div>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-lg-6 col-sm-6 col-xs-6 mygap-bottom">
            <div class="card-body mybgcolor rashi-box text-center">
                <div class="box-icon">
                    <img src="{{ asset('public/assets/front-end/img/rashi/luck.png') }}"
                        width="40">
                </div>
                <h2 class="h4 mb-4 text-center font-weight-bolder font-size-18 my-3" id="luck-heading">भाग्य</h2>
                <p id="luck">{{isset($dailyRashiData['prediction']['luck'])?$dailyRashiData['prediction']['luck']:''}}</p>
            </div>
        </div>
        <div class="col-lg-6 col-sm-6 col-xs-6 mygap-bottom">
            <div class="card-body mybgcolor rashi-box text-center">
                <div class="box-icon">
                    <img src="{{ asset('public/assets/front-end/img/rashi/emotion.png') }}"
                        width="40">
                </div>
                <h2 class="h4 mb-4 text-center font-weight-bolder font-size-18 my-3" id="emotion-heading">भावनाएं</h2>
                <p id="emotion">{{isset($dailyRashiData['prediction']['emotions'])?$dailyRashiData['prediction']['emotions']:''}}</p>
            </div>
        </div>
    </div>
</div>