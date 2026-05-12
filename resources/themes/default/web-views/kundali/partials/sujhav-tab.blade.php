<div class="tab-pane fade" id="sujhav" role="tabpanel" aria-labelledby="sujhav-tab">
    <div class="row mt-3">
        <div class="col-md-12">
            <div class="tab-details-block">
                <ul class="nav nav-pills mb-3 justify-content-center" id="pills-tab" role="tablist">
                  <li class="nav-item">
                    <a class="nav-link active" id="pills-ratna-rujhav-tab" data-toggle="pill" href="#pills-ratna-rujhav" role="tab" aria-controls="pills-ratna-rujhav" aria-selected="true" style="color: #222 !important; font-weight: 600;border-radius: 39px;padding: 4px 20px;">रत्न सुझाव</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="pills-rudraksha-sujhav-tab" data-toggle="pill" href="#pills-rudraksha-sujhav" role="tab" aria-controls="pills-rudraksha-sujhav" aria-selected="false" style="color: #222 !important; font-weight: 600;border-radius: 39px;padding: 4px 20px;">रुद्राक्ष सुझाव</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="pills-puja-sujhav-tab" data-toggle="pill" href="#pills-puja-sujhav" role="tab" aria-controls="pills-puja-sujhav" aria-selected="false" style="color: #222 !important; font-weight: 600;border-radius: 39px;padding: 4px 20px;">पूजा सुझाव</a>
                  </li>
                </ul>
                <div class="tab-content" id="pills-tabContent">
                  <div class="tab-pane fade show active" id="pills-ratna-rujhav" role="tabpanel" aria-labelledby="pills-ratna-rujhav-tab">
                      <div class="tab-details-block">
                        <div class="row">
                           <div class="col-md-4">
                              <div class="card shadow text-center mt-3">
                                 <img src="{{asset('public/assets/front-end/img/gems/'.trim($gemSuggestion['LIFE']['name'],' ').'.jpg')}}" id="lifeimg" class="card-img-top img-fluid mx-auto" alt="life gem" style="width: 65px;margin: 29px 0px 0;">
                                 <div class="card-body">
                                    <h6 class="card-title font-weight-bolder">Life Stone - जीवन रत्न</h6>
                                    <h6 id="lifegemname" class="text-warning fw-bold dasha-text">{{!empty($gemSuggestion['LIFE']['name'])?$gemSuggestion['LIFE']['name']:''}} </h6>
                                    <table class="text-left table rounded overflow-hidden table-bordered">
                                       <tbody>
                                          <tr>
                                             <td><b>Substitute</b></td>
                                             <td><span id="lifesubstitude">{{!empty($gemSuggestion['LIFE']['semi_gem'])?$gemSuggestion['LIFE']['semi_gem']:''}} </span></td>
                                          </tr>
                                          <tr class="bg-light-warning">
                                             <td><b>Finger</b></td>
                                             <td><span id="lifefinger" class="mb-0">{{!empty($gemSuggestion['LIFE']['wear_finger'])?$gemSuggestion['LIFE']['wear_finger']:''}}</span></td>
                                          </tr>
                                          <tr>
                                             <td><b>Weight</b></td>
                                             <td><span id="lifeweight" class="mb-0">{{!empty($gemSuggestion['LIFE']['weight_caret'])?$gemSuggestion['LIFE']['weight_caret']:''}}</span></td>
                                          </tr>
                                          <tr class="bg-light-warning">
                                             <td><b>Day</b></td>
                                             <td><span id="lifeday" class="mb-0">{{!empty($gemSuggestion['LIFE']['wear_day'])?$gemSuggestion['LIFE']['wear_day']:''}} </span></td>
                                          </tr>
                                          <tr>
                                             <td><b>Deity</b></td>
                                             <td><span id="lifedeity" class="mb-0">{{!empty($gemSuggestion['LIFE']['gem_deity'])?$gemSuggestion['LIFE']['gem_deity']:''}} </span></td>
                                          </tr>
                                          <tr class="bg-light-warning">
                                             <td><b>Metal</b></td>
                                             <td><span id="lifemetal" class="mb-0">{{!empty($gemSuggestion['LIFE']['wear_metal'])?$gemSuggestion['LIFE']['wear_metal']:''}} </span></td>
                                          </tr>
                                       </tbody>
                                    </table>
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-4">
                              <div class="card shadow text-center mt-3">
                                 <img src="{{asset('public/assets/front-end/img/gems/'.trim($gemSuggestion['BENEFIC']['name'],' ').'.jpg')}}" id="beneficimg" class="card-img-top img-fluid mx-auto" alt="benefic gem" style="width: 65px;margin: 29px 0px 0;">
                                 <div class="card-body">
                                    <h6 class="card-title font-weight-bolder">Benefic Stone - लाभ रत्न</h6>
                                    <h6 id="beneficgemname" class="text-warning fw-bold dasha-text">{{!empty($gemSuggestion['BENEFIC']['name'])?$gemSuggestion['BENEFIC']['name']:''}} </h6>
                                    <table class="text-left table rounded overflow-hidden table-bordered">
                                       <tbody>
                                          <tr>
                                             <td><b>Substitute</b></td>
                                             <td><span id="beneficsubstitude" class="mb-0">{{!empty($gemSuggestion['BENEFIC']['semi_gem'])?$gemSuggestion['BENEFIC']['semi_gem']:''}} </span></td>
                                          </tr>
                                          <tr class="bg-light-warning">
                                             <td><b>Finger</b></td>
                                             <td><span id="beneficfinger" class="mb-0">{{!empty($gemSuggestion['BENEFIC']['wear_finger'])?$gemSuggestion['BENEFIC']['wear_finger']:''}}</span></td>
                                          </tr>
                                          <tr>
                                             <td><b>Weight</b></td>
                                             <td><span id="beneficweight" class="mb-0">{{!empty($gemSuggestion['BENEFIC']['weight_caret'])?$gemSuggestion['BENEFIC']['weight_caret']:''}}</span></td>
                                          </tr>
                                          <tr class="bg-light-warning">
                                             <td><b>Day</b></td>
                                             <td><span id="beneficday" class="mb-0">{{!empty($gemSuggestion['BENEFIC']['wear_day'])?$gemSuggestion['BENEFIC']['wear_day']:''}} </span></td>
                                          </tr>
                                          <tr>
                                             <td><b>Deity</b></td>
                                             <td><span id="beneficdeity" class="mb-0">{{!empty($gemSuggestion['BENEFIC']['gem_deity'])?$gemSuggestion['BENEFIC']['gem_deity']:''}} </span></td>
                                          </tr>
                                          <tr class="bg-light-warning">
                                             <td><b>Metal</b></td>
                                             <td><span id="beneficmetal" class="mb-0">{{!empty($gemSuggestion['BENEFIC']['wear_metal'])?$gemSuggestion['BENEFIC']['wear_metal']:''}} </span></td>
                                          </tr>
                                       </tbody>
                                    </table>
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-4">
                              <div class="card shadow text-center mt-3">
                                 <img src="{{asset('public/assets/front-end/img/gems/'.trim($gemSuggestion['LUCKY']['name'],' ').'.jpg')}}" id="luckyimg" class="card-img-top img-fluid mx-auto" alt="lucky gem" style="width: 65px;margin: 29px 0px 0;">
                                 <div class="card-body">
                                    <h6 class="card-title font-weight-bolder">Lucky Stone - भाग्य रत्न</h6>
                                    <h6 id="luckygemname" class="text-warning fw-bold dasha-text">{{!empty($gemSuggestion['LUCKY']['name'])?$gemSuggestion['LUCKY']['name']:''}} </h6>
                                    <table class="text-left table rounded overflow-hidden table-bordered">
                                       <tbody>
                                          <tr>
                                             <td><b>Substitute</b></td>
                                             <td><span id="luckysubstitude" class="mb-0">{{!empty($gemSuggestion['LUCKY']['semi_gem'])?$gemSuggestion['LUCKY']['semi_gem']:''}} </span></td>
                                          </tr>
                                          <tr class="bg-light-warning">
                                             <td><b>Finger</b></td>
                                             <td><span id="luckyfinger" class="mb-0">{{!empty($gemSuggestion['LUCKY']['wear_finger'])?$gemSuggestion['LUCKY']['wear_finger']:''}}</span></td>
                                          </tr>
                                          <tr>
                                             <td><b>Weight</b></td>
                                             <td><span id="luckyweight" class="mb-0"> {{!empty($gemSuggestion['LUCKY']['weight_caret'])?$gemSuggestion['LUCKY']['weight_caret']:''}}</span></td>
                                          </tr>
                                          <tr class="bg-light-warning">
                                             <td><b>Day</b></td>
                                             <td><span id="luckyday" class="mb-0">{{!empty($gemSuggestion['LUCKY']['wear_day'])?$gemSuggestion['LUCKY']['wear_day']:''}} </span></td>
                                          </tr>
                                          <tr>
                                             <td><b>Deity</b></td>
                                             <td><span id="luckydeity" class="mb-0">{{!empty($gemSuggestion['LUCKY']['gem_deity'])?$gemSuggestion['LUCKY']['gem_deity']:''}} </span></td>
                                          </tr>
                                          <tr class="bg-light-warning">
                                             <td><b>Metal</b></td>
                                             <td><span id="luckymetal" class="mb-0">{{!empty($gemSuggestion['LUCKY']['wear_metal'])?$gemSuggestion['LUCKY']['wear_metal']:''}} </span></td>
                                          </tr>
                                       </tbody>
                                    </table>
                                 </div>
                              </div>
                           </div>
                        </div>
                   </div>
                  </div>
                  <div class="tab-pane fade" id="pills-rudraksha-sujhav" role="tabpanel" aria-labelledby="pills-rudraksha-sujhav-tab">
                      <div class="tab-details-block">
                        <div class="row">
                            <div>
                              <div class="text-center pb-3 pt-2">
                                 <img src="www.vedicrishi.in{{$rudrakshaSuggestion['img_url']}}" class="img-fluid w-50 justify-content-center img-thumbnail" alt="">
                                 <h6 id="rudrakshaname" class="pb-2 pt-2 fw-bold dasha-text">{{$rudrakshaSuggestion['name']}}</h6>
                              </div>
                              <h6 class="text-warning fw-bold dasha-text">आपको धारण करने की सलाह दी जाती है -</h6>
                              <h6 id="rrudrakshaname" class="fw-bold">{{$rudrakshaSuggestion['name']}}</h6>
                              <p id="rudrakshadetail">{{$rudrakshaSuggestion['detail']}}</p>
                           </div>
                        </div>
                   </div>
                  </div>
                  <div class="tab-pane fade" id="pills-puja-sujhav" role="tabpanel" aria-labelledby="pills-puja-sujhav-tab">
                      <div class="tab-details-block">
                        <div class="row">
                            <div class="Container">
                               <p id="summary" class="text-warning fw-bold pt-3 dasha-text">{{$prayerSuggestion['summary']}}</p>
                               <hr>
                               @if (count($prayerSuggestion['suggestions'])>0)
                               <ul class="list-group" id="poojasuggest">
                                 @foreach ($prayerSuggestion['suggestions'] as $prayerSuggest)
                                 <h5 class="my-2 font-weight-bolder">{{$prayerSuggest['title']}}</h5>
                                 <p>{{$prayerSuggest['summary']}}</p>
                                 @endforeach
                               </ul>
                               @endif
                            </div>
                        </div>
                   </div>
                  </div>
                </div>
                
           </div>
        </div>
    </div>
</div>