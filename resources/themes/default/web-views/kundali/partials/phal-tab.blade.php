<div class="tab-pane fade" id="phal" role="tabpanel" aria-labelledby="phal-tab">
    <div class="row mt-3">
        <div class="col-md-12">
            <div class="tab-details-block">
                <ul class="nav nav-pills mb-3 justify-content-center" id="pills-tab" role="tablist">
                  <li class="nav-item">
                    <a class="nav-link active" id="pills-lagna-tab" data-toggle="pill" href="#pills-lagna" role="tab" aria-controls="pills-lagna" aria-selected="true" style="color: #222 !important; font-weight: 600;border-radius: 39px;padding: 4px 20px;">लग्न फल</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="pills-grah-bhava-tab" data-toggle="pill" href="#pills-grah-bhava" role="tab" aria-controls="pills-grah-bhava" aria-selected="false" style="color: #222 !important; font-weight: 600;border-radius: 39px;padding: 4px 20px;">ग्रह भाव फल</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="pills-dainik-nakshatra-tab" data-toggle="pill" href="#pills-dainik-nakshatra" role="tab" aria-controls="pills-dainik-nakshatra" aria-selected="false" style="color: #222 !important; font-weight: 600;border-radius: 39px;padding: 4px 20px;">दैनिक नक्षत्र फल</a>
                  </li>
                </ul>
                <div class="tab-content" id="pills-tabContent">
                  <div class="tab-pane fade show active" id="pills-lagna" role="tabpanel" aria-labelledby="pills-lagna-tab">
                      <div class="tab-details-block">
                        <div class="row">
                            <div class="col-md-12">
                                <h6 class="pb-2 pt-3 fw-bold">आपका लग्न <span id="lagnaname1" style="font-size:20px; color: orange;">{{!empty($lagnaResult['asc_report']['ascendant'])?$lagnaResult['asc_report']['ascendant']:''}}</span> है</h6>
                                <p style="font-size:16px;" class="" id="lagnadetail">{{!empty($lagnaResult['asc_report']['report'])?$lagnaResult['asc_report']['report']:''}}</p>
                            </div>
                        </div>
                   </div>
                  </div>
                  <div class="tab-pane fade" id="pills-grah-bhava" role="tabpanel" aria-labelledby="pills-grah-bhava-tab">
                      <div class="tab-details-block">
                        <div class="row">
                            <div class="col-md-12">
                                <h6 class="font-weight-bold">ग्रह का चयन कीजिये</h6>
                                <select class="form-control mb-3" id="planetsname" onchange="planetNameChange()">
                                  <option value="sun">सूर्य</option>
                                  <option value="moon">चन्द्र</option>
                                  <option value="mars">मंगल</option>
                                  <option value="mercury">बुध</option>
                                  <option value="jupiter">गुरु</option>
                                  <option value="venus">शुक्र</option>
                                  <option value="saturn">शनि</option>
                               </select>
                               <h6 class="fw-bold">ग्रह भाव फल <span id="planetname" style="font-size:20px; color: orange;"> </span></h6>
                               <p style="font-size:14px;" class="text-muted" id="planetdetail"></p>
                            </div>
                        </div>
                   </div>
                  </div>
                  <div class="tab-pane fade" id="pills-dainik-nakshatra" role="tabpanel" aria-labelledby="pills-dainik-nakshatra-tab">
                      <div class="tab-details-block">
                        <div class="row">
                            <div class="col-md-12">
                                <h6 class="pb-2 fw-bold pt-3">आपका नक्षत्र <span id="birthMoonNakshatra">{{!empty($nakshatraResult['birth_moon_nakshatra'])?$nakshatraResult['birth_moon_nakshatra']:''}}</span> है</h6>
                               <h6 class="text-warning fw-bold"><span id="birthMoonNakshatraa">{{!empty($nakshatraResult['birth_moon_nakshatra'])?$nakshatraResult['birth_moon_nakshatra']:''}} </span> नक्षत्र दैनिक फल - <small class="text-muted"><span id="date2">{{!empty($nakshatraResult['prediction_date'])?$nakshatraResult['prediction_date']:''}}</span></small></h6>
                               <div>
                                  <h6 class="fw-bold pt-2">Health - स्वास्थ्य</h6>
                                  <p id="health">{{!empty($nakshatraResult['prediction']['health'])?$nakshatraResult['prediction']['health']:''}}</p>
                                  <h6 class="fw-bold pt-2">Personal Life - व्यक्तिगत जीवन</h6>
                                  <p id="life">{{!empty($nakshatraResult['prediction']['personal_life'])?$nakshatraResult['prediction']['personal_life']:''}}</p>
                                  <h6 class="fw-bold pt-2">Profession - व्यापार/व्यवसाय</h6>
                                  <p id="profession">{{!empty($nakshatraResult['prediction']['profession'])?$nakshatraResult['prediction']['profession']:''}}</p>
                                  <h6 class="fw-bold pt-2">Emotions - भावनाएं</h6>
                                  <p id="emotion">{{!empty($nakshatraResult['prediction']['emotions'])?$nakshatraResult['prediction']['emotions']:''}}</p>
                                  <h6 class="fw-bold pt-2">Travel - यात्रा</h6>
                                  <p id="travel">{{!empty($nakshatraResult['prediction']['travel'])?$nakshatraResult['prediction']['travel']:''}}</p>
                                  <h6 class="fw-bold pt-2">Luck - भाग्य</h6>
                                  <p id="luck">{{!empty($nakshatraResult['prediction']['luck'])?$nakshatraResult['prediction']['luck']:''}}</p>
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