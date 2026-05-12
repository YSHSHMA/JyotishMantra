<div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
    <div class="row">
        <div class="col-md-12">
           <div class="tab-details-block">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="font-weight-bold">जन्म एवं पंचांग विवरण</h6>
                        <div class="kundli-basic-details1">
                            <table class="table">
                              <tbody>
                                   <tr class="bg-light-warning">
                                      <td><b>नाम</b></td>
                                      <td><span id="username">{{ isset($userData['username'])?$userData['username']:(isset($userData['name'])?$userData['name']:'') }}</span></td>
                                   </tr>
                                   <tr>
                                      <td><b>जन्म दिनांक</b></td>
                                      <td>{{ isset($savedDOB)?date('d/m/Y',strtotime($savedDOB)):$userData['dob'] }}</td>
                                   </tr>
                                   <tr class="bg-light-warning">
                                      <td><b>जन्म समय</b></td>
                                      <td>{{$userData['time']}}</td>
                                   </tr>
                                   <tr>
                                      <td><b>अक्षांश</b></td>
                                      <td>{{$userData['latitude']}}</td>
                                   </tr>
                                   <tr class="bg-light-warning">
                                      <td><b>रेखांश</b></td>
                                      <td>{{$userData['longitude']}}</td>
                                   </tr>
                                   <tr>
                                      <td><b>टाइम जोन</b></td>
                                      <td>{{$userData['timezone']}}</td>
                                   </tr>
                                   <tr>
                                      <td><b>सूर्योदय</b></td>
                                      <td>{{!empty($birthData['sunrise'])?$birthData['sunrise']:''}}</td>
                                   </tr>
                                   <tr class="bg-light-warning">
                                      <td><b>सूर्यास्त</b></td>
                                      <td>{{!empty($birthData['sunset'])?$birthData['sunset']:''}}</td>
                                   </tr>
                                   <tr class="bg-light-warning">
                                      <td><b>अयनांश</b></td>
                                      <td>{{!empty($birthData['ayanamsha'])?$birthData['ayanamsha']:''}}</td>
                                   </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="font-weight-bold">ज्योतिष विवरणिवरण</h6>
                        <div class="kundli-basic-details1">
                        <table class="table">
                          <tbody>
                            <tr class="bg-light-warning">
                               <td><b>लग्न</b></td>
                               <td>{{!empty($lagnaData['asc_report']['ascendant'])?$lagnaData['asc_report']['ascendant']:''}}</td>
                            </tr>
                            <tr>
                               <td><b>राशि</b></td>
                               <td>{{!empty($astroData['sign'])?$astroData['sign']:''}}</td>
                            </tr>
                            <tr class="bg-light-warning">
                               <td><b>राशि स्वामी</b></td>
                               <td>{{!empty($astroData['SignLord'])?$astroData['SignLord']:''}}</td>
                            </tr>
                            <tr>
                               <td><b>करण</b></td>
                               <td>{{!empty($astroData['Karan'])?$astroData['Karan']:''}}</td>
                            </tr>
                            <tr class="bg-light-warning">
                               <td><b>योग</b></td>
                               <td>{{!empty($astroData['Yog'])?$astroData['Yog']:''}}</td>
                            </tr>
                            <tr>
                               <td><b>नक्षत्र</b></td>
                               <td>{{!empty($astroData['Naksahtra'])?$astroData['Naksahtra']:''}}</td>
                            </tr>
                            <tr class="bg-light-warning">
                               <td><b>वर्ण</b></td>
                               <td>{{!empty($astroData['Varna'])?$astroData['Varna']:''}}</td>
                            </tr>
                            <tr>
                               <td><b>वश्य</b></td>
                               <td>{{!empty($astroData['Vashya'])?$astroData['Vashya']:''}}</td>
                            </tr>
                            <tr class="bg-light-warning">
                               <td><b>योनी</b></td>
                               <td>{{!empty($astroData['Yoni'])?$astroData['Yoni']:''}}</td>
                            </tr>
                            <tr>
                               <td><b>गण</b></td>
                               <td>{{!empty($astroData['Gan'])?$astroData['Gan']:''}}</td>
                            </tr>
                            <tr class="bg-light-warning">
                               <td><b>नाडी</b></td>
                               <td>{{!empty($astroData['Nadi'])?$astroData['Nadi']:''}}</td>
                            </tr>
                            <tr>
                               <td><b>नक्षत्र-चरण</b></td>
                               <td>{{$astroData['Naksahtra']}} - {{$astroData['Charan']}}</td>
                            </tr>
                            <tr class="bg-light-warning">
                               <td><b>युन्जा</b></td>
                               <td>{{!empty($astroData['yunja'])?$astroData['yunja']:''}}</td>
                            </tr>
                            <tr>
                               <td><b>तत्व</b></td>
                               <td>{{!empty($astroData['tatva'])?$astroData['tatva']:''}}</td>
                            </tr>
                            <tr class="bg-light-warning">
                               <td><b>नाम वर्णमाला</b></td>
                               <td>{{!empty($astroData['name_alphabet'])?$astroData['name_alphabet']:''}}</td>
                            </tr>
                            <tr>
                               <td><b>पाया</b></td>
                               <td>{{!empty($astroData['paya'])?$astroData['paya']:''}}</td>
                            </tr>
                         </tbody>
                        </table>
                        </div>
                    </div>
                </div>
           </div>
        </div>
    </div>
</div>