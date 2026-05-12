<div class="tab-pane fade" id="dasha" role="tabpanel" aria-labelledby="dasha-tab">
    <div class="row mt-3">
        <div class="col-md-12">
            <div class="tab-details-block">
                <ul class="nav nav-pills mb-3 justify-content-center" id="pills-tab" role="tablist">
                  <li class="nav-item">
                    <a class="nav-link active" id="pills-vimshottari-tab" data-toggle="pill" href="#pills-vimshottari" role="tab" aria-controls="pills-vimshottari" aria-selected="true" style="color: #222 !important; font-weight: 600;border-radius: 39px;padding: 4px 20px;">विमशोत्तरी दशा</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="pills-yogini-tab" data-toggle="pill" href="#pills-yogini" role="tab" aria-controls="pills-yogini" aria-selected="false" style="color: #222 !important; font-weight: 600;border-radius: 39px;padding: 4px 20px;">योगिनी दशा</a>
                  </li>
                </ul>
                <div class="tab-content" id="pills-tabContent">
                  <div class="tab-pane fade show active" id="pills-vimshottari" role="tabpanel" aria-labelledby="pills-vimshottari-tab">
                      <div class="tab-details-block">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="font-weight-bold">विम्शोत्तरी दशा</h6>
                                <div class="kundli-basic-details1 p-3">
                                    <div class="row pt-3 pb-3">
                                       <div class="col-2">
                                          <span class="btn dash" id="cVdasha_mj_planet">{{!empty($currentVDasha['major']['planet'])?$currentVDasha['major']['planet']:''}} </span>
                                       </div>
                                       <div class="col-10">
                                          <h6 class="text-warning fw-bold dasha-text">महादशा</h6>
                                          <div class="d-flex fw-medium">
                                             <p id="cVdasha_mj_start" class="mb-0 me-2">{{!empty($currentVDasha['major']['start'])?$currentVDasha['major']['start']:''}}</p> -
                                             <p id="cVdasha_mj_end" class="ms-2">{{!empty($currentVDasha['major']['end'])?$currentVDasha['major']['end']:''}}</p>
                                          </div>
                                       </div>
                                       <div class="col-2">
                                          <button type="button" class="btn dash" id="cVdasha_mi_planet">{{!empty($currentVDasha['minor']['planet'])?$currentVDasha['minor']['planet']:''}} </button>
                                       </div>
                                       <div class="col-10">
                                          <h6 class="text-warning fw-bold dasha-text">अंतर दशा</h6>
                                          <div class="d-flex fw-medium">
                                             <p id="cVdasha_mi_start" class="mb-0 me-2">{{!empty($currentVDasha['minor']['start'])?$currentVDasha['minor']['start']:''}}</p> -
                                             <p id="cVdasha_mi_end" class="ms-2">{{!empty($currentVDasha['minor']['end'])?$currentVDasha['minor']['end']:''}}</p>
                                          </div>
                                       </div>
                                       <div class="col-2">
                                          <button type="button" class="btn dash" id="cVdasha_smi_planet">{{!empty($currentVDasha['sub_minor']['planet'])?$currentVDasha['sub_minor']['planet']:''}} </button>
                                       </div>
                                       <div class="col-10">
                                          <h6 class="text-warning fw-bold dasha-text">प्रत्यंतर दशा</h6>
                                          <div class="d-flex fw-medium">
                                             <p id="cVdasha_smi_start" class="mb-0 me-2">{{!empty($currentVDasha['sub_minor']['start'])?$currentVDasha['sub_minor']['start']:''}}</p> -
                                             <p id="cVdasha_smi_end" class="ms-2">{{!empty($currentVDasha['sub_minor']['end'])?$currentVDasha['sub_minor']['end']:''}}</p>
                                          </div>
                                       </div>
                                       <div class="col-2">
                                          <button type="button" class="btn dash" id="cVdasha_ssmi_planet">{{!empty($currentVDasha['sub_sub_minor']['planet'])?$currentVDasha['sub_sub_minor']['planet']:''}} </button>
                                       </div>
                                       <div class="col-10">
                                          <h6 class="text-warning fw-bold dasha-text">सूक्ष्म दशा</h6>
                                          <div class="d-flex fw-medium">
                                             <p id="cVdasha_ssmi_start" class="mb-0 me-2">{{!empty($currentVDasha['sub_sub_minor']['start'])?$currentVDasha['sub_sub_minor']['start']:''}}</p> -
                                             <p id="cVdasha_ssmi_end" class="ms-2">{{!empty($currentVDasha['sub_sub_minor']['end'])?$currentVDasha['sub_sub_minor']['end']:''}}</p>
                                          </div>
                                       </div>
                                       <div class="col-2">
                                          <button type="button" class="btn dash" id="cVdasha_sssmi_planet">{{!empty($currentVDasha['sub_sub_sub_minor']['planet'])?$currentVDasha['sub_sub_sub_minor']['planet']:''}} </button>
                                       </div>
                                       <div class="col-10">
                                          <h6 class="text-warning fw-bold dasha-text">प्राण दशा</h6>
                                          <div class="d-flex fw-medium">
                                             <p id="cVdasha_sssmi_start" class="mb-0 me-2">{{!empty($currentVDasha['sub_sub_sub_minor']['start'])?$currentVDasha['sub_sub_sub_minor']['start']:''}}</p> -
                                             <p id="cVdasha_sssmi_end" class="ms-2">{{!empty($currentVDasha['sub_sub_sub_minor']['end'])?$currentVDasha['sub_sub_sub_minor']['end']:''}}</p>
                                          </div>
                                       </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6 class="font-weight-bold">विम्शोत्तरी महा दशा</h6>
                                <div class="kundli-basic-details1">
                                    <table class="table">
                                       <thead>
                                          <tr class="bg-light-warning fw-bold">
                                             <th><b>Planet</b></th>
                                             <th><b>Start Date</b></th>
                                             <th><b>End Date</b></th>
                                          </tr>
                                       </thead>
                                       <tbody id="mVdhasha_tbody">
                                          @forelse ($majorVDasha as $mvDasha)
                                              <tr>
                                                <td>{{$mvDasha['planet']}}</td>
                                                <td>{{$mvDasha['start']}}</td>
                                                <td>{{$mvDasha['end']}}</td>
                                              </tr>
                                          @empty
                                              <tr>
                                                <td colspan="3" class="text-center">{{translate('No_Data_Found')}}</td>
                                              </tr>
                                          @endforelse
                                       </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                   </div>
                  </div>
                  <div class="tab-pane fade" id="pills-yogini" role="tabpanel" aria-labelledby="pills-yogini-tab">
                      <div class="tab-details-block">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="font-weight-bold">योगिनी दशा</h6>
                                <div class="kundli-basic-details1 p-3">
                                    <div class="row pt-3 pb-3">
                                       {{-- mahadasha --}}
                                       @if($currentYDasha['major_dasha']['dasha_name'] == 'Mangla')
                                          <div class="col-2">
                                             <span class="btn dash btn-sm" id="cYdasha_mj_dasha">मंगला</span>
                                          </div>
                                          <div class="col-10">
                                             <h6 class="text-warning fw-bold dasha-text">महादशा</h6>
                                             <div class="d-flex fw-medium">
                                                <p id="cYdasha_mj_start" class="mb-0 me-2">{{!empty($currentYDasha['major_dasha']['start_date'])?$currentYDasha['major_dasha']['start_date']:''}}</p> -
                                                <p id="cYdasha_mj_end" class="ms-2">{{!empty($currentYDasha['major_dasha']['end_date'])?$currentYDasha['major_dasha']['end_date']:''}}</p>
                                             </div>
                                             <p id="mahadashaDetail" class="font-size-14">योगिनी दशा की पहली दशा है मंगला। यह एक वर्ष की होती है। इसके स्वामी चंद्र हैं और जिन जातकों का जन्म आद्र्रा, चित्रा, श्रवण नक्षत्र में होता है, उन्हें मंगला दशा होती है। यह दशा अच्छी मानी जाती है। मंगला योगिनी की कृपा जिस जातक पर हो जाती है उसे हर प्रकार के सुख-संपन्नता प्राप्त होती है। उसके संपूर्ण जीवन में मंगल ही मंगल होता है।<br/><br/><b>मंगला : ऊं नमो मंगले मंगल कारिणी, मंगल मे कर ते नम:</b></p>
                                          </div>
                                       @elseif($currentYDasha['major_dasha']['dasha_name'] == 'Pingla')
                                          <div class="col-2">
                                             <span class="btn dash btn-sm" id="cYdasha_mj_dasha">पिंगला</span>
                                          </div>
                                          <div class="col-10">
                                             <h6 class="text-warning fw-bold dasha-text">महादशा</h6>
                                             <div class="d-flex fw-medium">
                                                <p id="cYdasha_mj_start" class="mb-0 me-2">{{!empty($currentYDasha['major_dasha']['start_date'])?$currentYDasha['major_dasha']['start_date']:''}}</p> -
                                                <p id="cYdasha_mj_end" class="ms-2">{{!empty($currentYDasha['major_dasha']['end_date'])?$currentYDasha['major_dasha']['end_date']:''}}</p>
                                             </div>
                                             <p id="mahadashaDetail" class="font-size-14">क्रमानुसार दूसरी योगिनी दशा पिंगला होती है। यह दो वर्ष की होती है। इसके स्वामी सूर्य हैं। जिनका जन्म पुनर्वसु, स्वाति, धनिष्ठा नक्षत्र में होता है उन्हें पिंगला दशा होती है। यह दशा भी शुभ होती है। पिंगला दशा में जातक के जीवन के सारे संकट शांत हो जाते हैं। उसकी उन्नति होती है और सुख-संपत्ति प्राप्त होती है।<br/><br/><b>पिंगला : ऊं नमो पिंगले वैरिकारिणी, प्रसीद प्रसीद नमस्तुभ्यं</b></p>
                                          </div>
                                       @elseif($currentYDasha['major_dasha']['dasha_name'] == 'Dhanya')
                                          <div class="col-2">
                                             <span class="btn dash btn-sm" id="cYdasha_mj_dasha">धान्या</span>
                                          </div>
                                          <div class="col-10">
                                             <h6 class="text-warning fw-bold dasha-text">महादशा</h6>
                                             <div class="d-flex fw-medium">
                                                <p id="cYdasha_mj_start" class="mb-0 me-2">{{!empty($currentYDasha['major_dasha']['start_date'])?$currentYDasha['major_dasha']['start_date']:''}}</p> -
                                                <p id="cYdasha_mj_end" class="ms-2">{{!empty($currentYDasha['major_dasha']['end_date'])?$currentYDasha['major_dasha']['end_date']:''}}</p>
                                             </div>
                                             <p id="mahadashaDetail" class="font-size-14">तीसरी योगिनी दशा धान्या होती है और यह तीन वर्ष की होती है। इसके स्वामी बृहस्पति हैं। जिनका जन्म पुष्य, विशाखा, शतभिषा नक्षत्र में होता है उन्हें धान्या दशा से जीवन प्रारंभ होता है। यह दशा जिनके जीवन में आती है उन्हें अपार धन-धान्य प्राप्त होता है। <br/><br/><b>धान्या : ऊं धान्ये मंगल कारिणी, मंगलम मे कुरु ते नम:</b></p>
                                          </div>
                                       @elseif ($currentYDasha['major_dasha']['dasha_name'] == 'Bhramari')
                                          <div class="col-2">
                                             <span class="btn dash btn-sm" id="cYdasha_mj_dasha">भ्रामरी</span>
                                          </div>
                                          <div class="col-10">
                                             <h6 class="text-warning fw-bold dasha-text">महादशा</h6>
                                             <div class="d-flex fw-medium">
                                                <p id="cYdasha_mj_start" class="mb-0 me-2">{{!empty($currentYDasha['major_dasha']['start_date'])?$currentYDasha['major_dasha']['start_date']:''}}</p> -
                                                <p id="cYdasha_mj_end" class="ms-2">{{!empty($currentYDasha['major_dasha']['end_date'])?$currentYDasha['major_dasha']['end_date']:''}}</p>
                                             </div>
                                             <p id="mahadashaDetail" class="font-size-14">चौथी योगिनी दशा भ्रामरी होती है और यह चार वर्ष की होती है। इसके स्वामी मंगल हैं। जिनका जन्म अश्विनी, अश्लेषा, अनुराधा, पूर्वाभाद्रपद नक्षत्र में होता है उनकी जन्मकालिक योगिनी दशा भ्रामरी होती है। इस दशा के दौरान व्यक्ति क्रोधी हो जाता है। कई प्रकार के संकट आने लगते हैं। आर्थिक और संपत्ति का नुकसान होता है। व्यक्ति भ्रमित हो जाता है।<br><br><b>भ्रामरी : ऊं नमो भ्रामरी जगतानामधीश्वरी भ्रामर्ये नम:</b></p>
                                          </div>
                                       @elseif($currentYDasha['major_dasha']['dasha_name'] == 'Bhadrika')
                                          <div class="col-2">
                                             <span class="btn dash btn-sm" id="cYdasha_mj_dasha">भद्रिका</span>
                                          </div>
                                          <div class="col-10">
                                             <h6 class="text-warning fw-bold dasha-text">महादशा</h6>
                                             <div class="d-flex fw-medium">
                                                <p id="cYdasha_mj_start" class="mb-0 me-2">{{!empty($currentYDasha['major_dasha']['start_date'])?$currentYDasha['major_dasha']['start_date']:''}}</p> -
                                                <p id="cYdasha_mj_end" class="ms-2">{{!empty($currentYDasha['major_dasha']['end_date'])?$currentYDasha['major_dasha']['end_date']:''}}</p>
                                             </div>
                                             <p id="mahadashaDetail" class="font-size-14">पांचवीं योगिनी दशा भद्रिका होती है और यह पांच वर्ष की होती है। इसके स्वामी बुध हैं। जिनका जन्म भरणी, मघा, ज्येष्ठा, उत्तराभाद्रपद नक्षत्र में होता है उनकी जन्मकालिक योगिनी दशा भद्रिका होती है। इस दशाकाल में जातक के सुकर्मो का शुभ फल प्राप्त होता है। शत्रुओं का शमन होता है और जीवन के व्यवधान समाप्त हो जाते हैं।<br/><br/><b>भद्रिका : ऊं भद्रिके भद्रं देहि देहि, अभद्रं दूरी कुरु ते नम:</b></p>
                                          </div>
                                       @elseif($currentYDasha['major_dasha']['dasha_name'] == 'Ulka')
                                          <div class="col-2">
                                             <span class="btn dash btn-sm" id="cYdasha_mj_dasha">उल्का</span>
                                          </div>
                                          <div class="col-10">
                                             <h6 class="text-warning fw-bold dasha-text">महादशा</h6>
                                             <div class="d-flex fw-medium">
                                                <p id="cYdasha_mj_start" class="mb-0 me-2">{{!empty($currentYDasha['major_dasha']['start_date'])?$currentYDasha['major_dasha']['start_date']:''}}</p> -
                                                <p id="cYdasha_mj_end" class="ms-2">{{!empty($currentYDasha['major_dasha']['end_date'])?$currentYDasha['major_dasha']['end_date']:''}}</p>
                                             </div>
                                             <p id="mahadashaDetail" class="font-size-14">छटी योगिनी दशा उल्का होती है और यह छह वर्ष की होती है। इसके स्वामी शनि हैं। जिनका जन्म कृतिका, पूर्वा फाल्गुनी, मूल, रेवती नक्षत्र में होता है उनकी जन्मकालिक योगिनी दशा उल्का होती है। इस दशाकाल में जातक को मेहनत अधिक करनी पड़ती है। जीवन में दौड़भाग बनी रहती है। कार्यो में शिथिलता आ जाती है। कई तरह के संकट आते हैं।<br/><br/><b>उल्का : ऊं उल्के विघ्नाशिनी कल्याणं कुरु ते नम:</b>></p>
                                          </div>
                                       @elseif($currentYDasha['major_dasha']['dasha_name'] == 'Siddha')
                                          <div class="col-2">
                                             <span class="btn dash btn-sm" id="cYdasha_mj_dasha">सिद्धा</span>
                                          </div>
                                          <div class="col-10">
                                             <h6 class="text-warning fw-bold dasha-text">महादशा</h6>
                                             <div class="d-flex fw-medium">
                                                <p id="cYdasha_mj_start" class="mb-0 me-2">{{!empty($currentYDasha['major_dasha']['start_date'])?$currentYDasha['major_dasha']['start_date']:''}}</p> -
                                                <p id="cYdasha_mj_end" class="ms-2">{{!empty($currentYDasha['major_dasha']['end_date'])?$currentYDasha['major_dasha']['end_date']:''}}</p>
                                             </div>
                                             <p id="mahadashaDetail" class="font-size-14">सातवीं योगिनी दशा सिद्ध होती है औ इसके स्वामी शुक्र हैं। जिनका जन्म रोहिणी, उत्तराफाल्गुनी, पूर्वाषाढ़ा नक्षत्र में होता है उनकी जन्मकालिक योगिनी दशा सिद्धा होती है। इस दशा के भोग काल में जातक की संपत्ति, भौतिक सुख, प्रेम, आकर्षण प्रभाव आदि में वृद्धि होती है। जिन लोगों पर सिद्धा योगिनी की कृपा होती है उनके जीवन में कोई अभाव नहीं रह जाता है।<br/><br/><b>सिद्धा : ऊं नमो सिद्धे सिद्धिं देहि नमस्तुभ्यं</b></p>
                                          </div>
                                       @elseif($currentYDasha['major_dasha']['dasha_name'] == 'Sankata')
                                          <div class="col-2">
                                             <span class="btn dash btn-sm" id="cYdasha_mj_dasha">संकटा</span>
                                          </div>
                                          <div class="col-10">
                                             <h6 class="text-warning fw-bold dasha-text">महादशा</h6>
                                             <div class="d-flex fw-medium">
                                                <p id="cYdasha_mj_start" class="mb-0 me-2">{{!empty($currentYDasha['major_dasha']['start_date'])?$currentYDasha['major_dasha']['start_date']:''}}</p> -
                                                <p id="cYdasha_mj_end" class="ms-2">{{!empty($currentYDasha['major_dasha']['end_date'])?$currentYDasha['major_dasha']['end_date']:''}}</p>
                                             </div>
                                             <p id="mahadashaDetail" class="font-size-14">योगिनी दशा चक्र की आठवीं और अंतिम दशा संकटा होती है और इसके स्वामी राहू हैं। जिनका जन्म मृगशिर, हस्त, उत्तराषाढ़ा नक्षत्र में होता है उनकी जन्मकालिक दशा संकटा होती है। संकटा योगिनी दशाकाल में जातक हर ओर से परेशानियों और संकटों से घिर जाता है। संकटों के नाश के लिए इस दशा के भोगकाल में मातृरूप में योगिनी की पूजा करें।<br/><br/><b>संकटा : ऊं ह्रीं संकटे मम रोगंनाशय स्वाहा</b></p>
                                          </div>
                                       @endif

                                       {{-- antar dasha --}}
                                       @if($currentYDasha['sub_dasha']['dasha_name'] == 'Mangla')
                                          <div class="col-2">
                                             <span class="btn dash btn-sm" id="cYdasha_mj_dasha">मंगला</span>
                                          </div>
                                          <div class="col-10">
                                             <h6 class="text-warning fw-bold dasha-text">अंतर दशा</h6>
                                             <div class="d-flex fw-medium">
                                                <p id="cYdasha_mj_start" class="mb-0 me-2">{{!empty($currentYDasha['sub_dasha']['start_date'])?$currentYDasha['sub_dasha']['start_date']:''}}</p> -
                                                <p id="cYdasha_mj_end" class="ms-2">{{!empty($currentYDasha['sub_dasha']['end_date'])?$currentYDasha['sub_dasha']['end_date']:''}}</p>
                                             </div>
                                             <p id="mahadashaDetail" class="font-size-14">योगिनी दशा की पहली दशा है मंगला। यह एक वर्ष की होती है। इसके स्वामी चंद्र हैं और जिन जातकों का जन्म आद्र्रा, चित्रा, श्रवण नक्षत्र में होता है, उन्हें मंगला दशा होती है। यह दशा अच्छी मानी जाती है। मंगला योगिनी की कृपा जिस जातक पर हो जाती है उसे हर प्रकार के सुख-संपन्नता प्राप्त होती है। उसके संपूर्ण जीवन में मंगल ही मंगल होता है।<br/><br/><b>मंगला : ऊं नमो मंगले मंगल कारिणी, मंगल मे कर ते नम:</b></p>
                                          </div>
                                       @elseif($currentYDasha['sub_dasha']['dasha_name'] == 'Pingla')
                                          <div class="col-2">
                                             <span class="btn dash btn-sm" id="cYdasha_mj_dasha">पिंगला</span>
                                          </div>
                                          <div class="col-10">
                                             <h6 class="text-warning fw-bold dasha-text">अंतर दशा</h6>
                                             <div class="d-flex fw-medium">
                                                <p id="cYdasha_mj_start" class="mb-0 me-2">{{!empty($currentYDasha['sub_dasha']['start_date'])?$currentYDasha['sub_dasha']['start_date']:''}}</p> -
                                                <p id="cYdasha_mj_end" class="ms-2">{{!empty($currentYDasha['sub_dasha']['end_date'])?$currentYDasha['sub_dasha']['end_date']:''}}</p>
                                             </div>
                                             <p id="mahadashaDetail" class="font-size-14">क्रमानुसार दूसरी योगिनी दशा पिंगला होती है। यह दो वर्ष की होती है। इसके स्वामी सूर्य हैं। जिनका जन्म पुनर्वसु, स्वाति, धनिष्ठा नक्षत्र में होता है उन्हें पिंगला दशा होती है। यह दशा भी शुभ होती है। पिंगला दशा में जातक के जीवन के सारे संकट शांत हो जाते हैं। उसकी उन्नति होती है और सुख-संपत्ति प्राप्त होती है।<br/><br/><b>पिंगला : ऊं नमो पिंगले वैरिकारिणी, प्रसीद प्रसीद नमस्तुभ्यं</b></p>
                                          </div>
                                       @elseif($currentYDasha['sub_dasha']['dasha_name'] == 'Dhanya')
                                          <div class="col-2">
                                             <span class="btn dash btn-sm" id="cYdasha_mj_dasha">धान्या</span>
                                          </div>
                                          <div class="col-10">
                                             <h6 class="text-warning fw-bold dasha-text">अंतर दशा</h6>
                                             <div class="d-flex fw-medium">
                                                <p id="cYdasha_mj_start" class="mb-0 me-2">{{!empty($currentYDasha['sub_dasha']['start_date'])?$currentYDasha['sub_dasha']['start_date']:''}}</p> -
                                                <p id="cYdasha_mj_end" class="ms-2">{{!empty($currentYDasha['sub_dasha']['end_date'])?$currentYDasha['sub_dasha']['end_date']:''}}</p>
                                             </div>
                                             <p id="mahadashaDetail" class="font-size-14">तीसरी योगिनी दशा धान्या होती है और यह तीन वर्ष की होती है। इसके स्वामी बृहस्पति हैं। जिनका जन्म पुष्य, विशाखा, शतभिषा नक्षत्र में होता है उन्हें धान्या दशा से जीवन प्रारंभ होता है। यह दशा जिनके जीवन में आती है उन्हें अपार धन-धान्य प्राप्त होता है। <br/><br/><b>धान्या : ऊं धान्ये मंगल कारिणी, मंगलम मे कुरु ते नम:</b></p>
                                          </div>
                                       @elseif ($currentYDasha['sub_dasha']['dasha_name'] == 'Bhramari')
                                          <div class="col-2">
                                             <span class="btn dash btn-sm" id="cYdasha_mj_dasha">भ्रामरी</span>
                                          </div>
                                          <div class="col-10">
                                             <h6 class="text-warning fw-bold dasha-text">अंतर दशा</h6>
                                             <div class="d-flex fw-medium">
                                                <p id="cYdasha_mj_start" class="mb-0 me-2">{{!empty($currentYDasha['sub_dasha']['start_date'])?$currentYDasha['sub_dasha']['start_date']:''}}</p> -
                                                <p id="cYdasha_mj_end" class="ms-2">{{!empty($currentYDasha['sub_dasha']['end_date'])?$currentYDasha['sub_dasha']['end_date']:''}}</p>
                                             </div>
                                             <p id="mahadashaDetail" class="font-size-14">चौथी योगिनी दशा भ्रामरी होती है और यह चार वर्ष की होती है। इसके स्वामी मंगल हैं। जिनका जन्म अश्विनी, अश्लेषा, अनुराधा, पूर्वाभाद्रपद नक्षत्र में होता है उनकी जन्मकालिक योगिनी दशा भ्रामरी होती है। इस दशा के दौरान व्यक्ति क्रोधी हो जाता है। कई प्रकार के संकट आने लगते हैं। आर्थिक और संपत्ति का नुकसान होता है। व्यक्ति भ्रमित हो जाता है।<br><br><b>भ्रामरी : ऊं नमो भ्रामरी जगतानामधीश्वरी भ्रामर्ये नम:</b></p>
                                          </div>
                                       @elseif($currentYDasha['sub_dasha']['dasha_name'] == 'Bhadrika')
                                          <div class="col-2">
                                             <span class="btn dash btn-sm" id="cYdasha_mj_dasha">भद्रिका</span>
                                          </div>
                                          <div class="col-10">
                                             <h6 class="text-warning fw-bold dasha-text">अंतर दशा</h6>
                                             <div class="d-flex fw-medium">
                                                <p id="cYdasha_mj_start" class="mb-0 me-2">{{!empty($currentYDasha['sub_dasha']['start_date'])?$currentYDasha['sub_dasha']['start_date']:''}}</p> -
                                                <p id="cYdasha_mj_end" class="ms-2">{{!empty($currentYDasha['sub_dasha']['end_date'])?$currentYDasha['sub_dasha']['end_date']:''}}</p>
                                             </div>
                                             <p id="mahadashaDetail" class="font-size-14">पांचवीं योगिनी दशा भद्रिका होती है और यह पांच वर्ष की होती है। इसके स्वामी बुध हैं। जिनका जन्म भरणी, मघा, ज्येष्ठा, उत्तराभाद्रपद नक्षत्र में होता है उनकी जन्मकालिक योगिनी दशा भद्रिका होती है। इस दशाकाल में जातक के सुकर्मो का शुभ फल प्राप्त होता है। शत्रुओं का शमन होता है और जीवन के व्यवधान समाप्त हो जाते हैं।<br/><br/><b>भद्रिका : ऊं भद्रिके भद्रं देहि देहि, अभद्रं दूरी कुरु ते नम:</b></p>
                                          </div>
                                       @elseif($currentYDasha['sub_dasha']['dasha_name'] == 'Ulka')
                                          <div class="col-2">
                                             <span class="btn dash btn-sm" id="cYdasha_mj_dasha">उल्का</span>
                                          </div>
                                          <div class="col-10">
                                             <h6 class="text-warning fw-bold dasha-text">अंतर दशा</h6>
                                             <div class="d-flex fw-medium">
                                                <p id="cYdasha_mj_start" class="mb-0 me-2">{{!empty($currentYDasha['sub_dasha']['start_date'])?$currentYDasha['sub_dasha']['start_date']:''}}</p> -
                                                <p id="cYdasha_mj_end" class="ms-2">{{!empty($currentYDasha['sub_dasha']['end_date'])?$currentYDasha['sub_dasha']['end_date']:''}}</p>
                                             </div>
                                             <p id="mahadashaDetail" class="font-size-14">छटी योगिनी दशा उल्का होती है और यह छह वर्ष की होती है। इसके स्वामी शनि हैं। जिनका जन्म कृतिका, पूर्वा फाल्गुनी, मूल, रेवती नक्षत्र में होता है उनकी जन्मकालिक योगिनी दशा उल्का होती है। इस दशाकाल में जातक को मेहनत अधिक करनी पड़ती है। जीवन में दौड़भाग बनी रहती है। कार्यो में शिथिलता आ जाती है। कई तरह के संकट आते हैं।<br/><br/><b>उल्का : ऊं उल्के विघ्नाशिनी कल्याणं कुरु ते नम:</b>></p>
                                          </div>
                                       @elseif($currentYDasha['sub_dasha']['dasha_name'] == 'Siddha')
                                          <div class="col-2">
                                             <span class="btn dash btn-sm" id="cYdasha_mj_dasha">सिद्धा</span>
                                          </div>
                                          <div class="col-10">
                                             <h6 class="text-warning fw-bold dasha-text">अंतर दशा</h6>
                                             <div class="d-flex fw-medium">
                                                <p id="cYdasha_mj_start" class="mb-0 me-2">{{!empty($currentYDasha['sub_dasha']['start_date'])?$currentYDasha['sub_dasha']['start_date']:''}}</p> -
                                                <p id="cYdasha_mj_end" class="ms-2">{{!empty($currentYDasha['sub_dasha']['end_date'])?$currentYDasha['sub_dasha']['end_date']:''}}</p>
                                             </div>
                                             <p id="mahadashaDetail" class="font-size-14">सातवीं योगिनी दशा सिद्ध होती है औ इसके स्वामी शुक्र हैं। जिनका जन्म रोहिणी, उत्तराफाल्गुनी, पूर्वाषाढ़ा नक्षत्र में होता है उनकी जन्मकालिक योगिनी दशा सिद्धा होती है। इस दशा के भोग काल में जातक की संपत्ति, भौतिक सुख, प्रेम, आकर्षण प्रभाव आदि में वृद्धि होती है। जिन लोगों पर सिद्धा योगिनी की कृपा होती है उनके जीवन में कोई अभाव नहीं रह जाता है।<br/><br/><b>सिद्धा : ऊं नमो सिद्धे सिद्धिं देहि नमस्तुभ्यं</b></p>
                                          </div>
                                       @elseif($currentYDasha['sub_dasha']['dasha_name'] == 'Sankata')
                                          <div class="col-2">
                                             <span class="btn dash btn-sm" id="cYdasha_mj_dasha">संकटा</span>
                                          </div>
                                          <div class="col-10">
                                             <h6 class="text-warning fw-bold dasha-text">अंतर दशा</h6>
                                             <div class="d-flex fw-medium">
                                                <p id="cYdasha_mj_start" class="mb-0 me-2">{{!empty($currentYDasha['sub_dasha']['start_date'])?$currentYDasha['sub_dasha']['start_date']:''}}</p> -
                                                <p id="cYdasha_mj_end" class="ms-2">{{!empty($currentYDasha['sub_dasha']['end_date'])?$currentYDasha['sub_dasha']['end_date']:''}}</p>
                                             </div>
                                             <p id="mahadashaDetail" class="font-size-14">योगिनी दशा चक्र की आठवीं और अंतिम दशा संकटा होती है और इसके स्वामी राहू हैं। जिनका जन्म मृगशिर, हस्त, उत्तराषाढ़ा नक्षत्र में होता है उनकी जन्मकालिक दशा संकटा होती है। संकटा योगिनी दशाकाल में जातक हर ओर से परेशानियों और संकटों से घिर जाता है। संकटों के नाश के लिए इस दशा के भोगकाल में मातृरूप में योगिनी की पूजा करें।<br/><br/><b>संकटा : ऊं ह्रीं संकटे मम रोगंनाशय स्वाहा</b></p>
                                          </div>
                                       @endif

                                       {{-- pratyantar dasha --}}
                                       @if($currentYDasha['sub_sub_dasha']['dasha_name'] == 'Mangla')
                                          <div class="col-2">
                                             <span class="btn dash btn-sm" id="cYdasha_mj_dasha">मंगला</span>
                                          </div>
                                          <div class="col-10">
                                             <h6 class="text-warning fw-bold dasha-text">प्रत्यंतर दशा</h6>
                                             <div class="d-flex fw-medium">
                                                <p id="cYdasha_mj_start" class="mb-0 me-2">{{!empty($currentYDasha['sub_sub_dasha']['start_date'])?$currentYDasha['sub_sub_dasha']['start_date']:''}}</p> -
                                                <p id="cYdasha_mj_end" class="ms-2">{{!empty($currentYDasha['sub_sub_dasha']['end_date'])?$currentYDasha['sub_sub_dasha']['end_date']:''}}</p>
                                             </div>
                                             <p id="mahadashaDetail" class="font-size-14">योगिनी दशा की पहली दशा है मंगला। यह एक वर्ष की होती है। इसके स्वामी चंद्र हैं और जिन जातकों का जन्म आद्र्रा, चित्रा, श्रवण नक्षत्र में होता है, उन्हें मंगला दशा होती है। यह दशा अच्छी मानी जाती है। मंगला योगिनी की कृपा जिस जातक पर हो जाती है उसे हर प्रकार के सुख-संपन्नता प्राप्त होती है। उसके संपूर्ण जीवन में मंगल ही मंगल होता है।<br/><br/><b>मंगला : ऊं नमो मंगले मंगल कारिणी, मंगल मे कर ते नम:</b></p>
                                          </div>
                                       @elseif($currentYDasha['sub_sub_dasha']['dasha_name'] == 'Pingla')
                                          <div class="col-2">
                                             <span class="btn dash btn-sm" id="cYdasha_mj_dasha">पिंगला</span>
                                          </div>
                                          <div class="col-10">
                                             <h6 class="text-warning fw-bold dasha-text">प्रत्यंतर दशा</h6>
                                             <div class="d-flex fw-medium">
                                                <p id="cYdasha_mj_start" class="mb-0 me-2">{{!empty($currentYDasha['sub_sub_dasha']['start_date'])?$currentYDasha['sub_sub_dasha']['start_date']:''}}</p> -
                                                <p id="cYdasha_mj_end" class="ms-2">{{!empty($currentYDasha['sub_sub_dasha']['end_date'])?$currentYDasha['sub_sub_dasha']['end_date']:''}}</p>
                                             </div>
                                             <p id="mahadashaDetail" class="font-size-14">क्रमानुसार दूसरी योगिनी दशा पिंगला होती है। यह दो वर्ष की होती है। इसके स्वामी सूर्य हैं। जिनका जन्म पुनर्वसु, स्वाति, धनिष्ठा नक्षत्र में होता है उन्हें पिंगला दशा होती है। यह दशा भी शुभ होती है। पिंगला दशा में जातक के जीवन के सारे संकट शांत हो जाते हैं। उसकी उन्नति होती है और सुख-संपत्ति प्राप्त होती है।<br/><br/><b>पिंगला : ऊं नमो पिंगले वैरिकारिणी, प्रसीद प्रसीद नमस्तुभ्यं</b></p>
                                          </div>
                                       @elseif($currentYDasha['sub_sub_dasha']['dasha_name'] == 'Dhanya')
                                          <div class="col-2">
                                             <span class="btn dash btn-sm" id="cYdasha_mj_dasha">धान्या</span>
                                          </div>
                                          <div class="col-10">
                                             <h6 class="text-warning fw-bold dasha-text">प्रत्यंतर दशा</h6>
                                             <div class="d-flex fw-medium">
                                                <p id="cYdasha_mj_start" class="mb-0 me-2">{{!empty($currentYDasha['sub_sub_dasha']['start_date'])?$currentYDasha['sub_sub_dasha']['start_date']:''}}</p> -
                                                <p id="cYdasha_mj_end" class="ms-2">{{!empty($currentYDasha['sub_sub_dasha']['end_date'])?$currentYDasha['sub_sub_dasha']['end_date']:''}}</p>
                                             </div>
                                             <p id="mahadashaDetail" class="font-size-14">तीसरी योगिनी दशा धान्या होती है और यह तीन वर्ष की होती है। इसके स्वामी बृहस्पति हैं। जिनका जन्म पुष्य, विशाखा, शतभिषा नक्षत्र में होता है उन्हें धान्या दशा से जीवन प्रारंभ होता है। यह दशा जिनके जीवन में आती है उन्हें अपार धन-धान्य प्राप्त होता है। <br/><br/><b>धान्या : ऊं धान्ये मंगल कारिणी, मंगलम मे कुरु ते नम:</b></p>
                                          </div>
                                       @elseif ($currentYDasha['sub_sub_dasha']['dasha_name'] == 'Bhramari')
                                          <div class="col-2">
                                             <span class="btn dash btn-sm" id="cYdasha_mj_dasha">भ्रामरी</span>
                                          </div>
                                          <div class="col-10">
                                             <h6 class="text-warning fw-bold dasha-text">प्रत्यंतर दशा</h6>
                                             <div class="d-flex fw-medium">
                                                <p id="cYdasha_mj_start" class="mb-0 me-2">{{!empty($currentYDasha['sub_sub_dasha']['start_date'])?$currentYDasha['sub_sub_dasha']['start_date']:''}}</p> -
                                                <p id="cYdasha_mj_end" class="ms-2">{{!empty($currentYDasha['sub_sub_dasha']['end_date'])?$currentYDasha['sub_sub_dasha']['end_date']:''}}</p>
                                             </div>
                                             <p id="mahadashaDetail" class="font-size-14">चौथी योगिनी दशा भ्रामरी होती है और यह चार वर्ष की होती है। इसके स्वामी मंगल हैं। जिनका जन्म अश्विनी, अश्लेषा, अनुराधा, पूर्वाभाद्रपद नक्षत्र में होता है उनकी जन्मकालिक योगिनी दशा भ्रामरी होती है। इस दशा के दौरान व्यक्ति क्रोधी हो जाता है। कई प्रकार के संकट आने लगते हैं। आर्थिक और संपत्ति का नुकसान होता है। व्यक्ति भ्रमित हो जाता है।<br><br><b>भ्रामरी : ऊं नमो भ्रामरी जगतानामधीश्वरी भ्रामर्ये नम:</b></p>
                                          </div>
                                       @elseif($currentYDasha['sub_sub_dasha']['dasha_name'] == 'Bhadrika')
                                          <div class="col-2">
                                             <span class="btn dash btn-sm" id="cYdasha_mj_dasha">भद्रिका</span>
                                          </div>
                                          <div class="col-10">
                                             <h6 class="text-warning fw-bold dasha-text">प्रत्यंतर दशा</h6>
                                             <div class="d-flex fw-medium">
                                                <p id="cYdasha_mj_start" class="mb-0 me-2">{{!empty($currentYDasha['sub_sub_dasha']['start_date'])?$currentYDasha['sub_sub_dasha']['start_date']:''}}</p> -
                                                <p id="cYdasha_mj_end" class="ms-2">{{!empty($currentYDasha['sub_sub_dasha']['end_date'])?$currentYDasha['sub_sub_dasha']['end_date']:''}}</p>
                                             </div>
                                             <p id="mahadashaDetail" class="font-size-14">पांचवीं योगिनी दशा भद्रिका होती है और यह पांच वर्ष की होती है। इसके स्वामी बुध हैं। जिनका जन्म भरणी, मघा, ज्येष्ठा, उत्तराभाद्रपद नक्षत्र में होता है उनकी जन्मकालिक योगिनी दशा भद्रिका होती है। इस दशाकाल में जातक के सुकर्मो का शुभ फल प्राप्त होता है। शत्रुओं का शमन होता है और जीवन के व्यवधान समाप्त हो जाते हैं।<br/><br/><b>भद्रिका : ऊं भद्रिके भद्रं देहि देहि, अभद्रं दूरी कुरु ते नम:</b></p>
                                          </div>
                                       @elseif($currentYDasha['sub_sub_dasha']['dasha_name'] == 'Ulka')
                                          <div class="col-2">
                                             <span class="btn dash btn-sm" id="cYdasha_mj_dasha">उल्का</span>
                                          </div>
                                          <div class="col-10">
                                             <h6 class="text-warning fw-bold dasha-text">प्रत्यंतर दशा</h6>
                                             <div class="d-flex fw-medium">
                                                <p id="cYdasha_mj_start" class="mb-0 me-2">{{!empty($currentYDasha['sub_sub_dasha']['start_date'])?$currentYDasha['sub_sub_dasha']['start_date']:''}}</p> -
                                                <p id="cYdasha_mj_end" class="ms-2">{{!empty($currentYDasha['sub_sub_dasha']['end_date'])?$currentYDasha['sub_sub_dasha']['end_date']:''}}</p>
                                             </div>
                                             <p id="mahadashaDetail" class="font-size-14">छटी योगिनी दशा उल्का होती है और यह छह वर्ष की होती है। इसके स्वामी शनि हैं। जिनका जन्म कृतिका, पूर्वा फाल्गुनी, मूल, रेवती नक्षत्र में होता है उनकी जन्मकालिक योगिनी दशा उल्का होती है। इस दशाकाल में जातक को मेहनत अधिक करनी पड़ती है। जीवन में दौड़भाग बनी रहती है। कार्यो में शिथिलता आ जाती है। कई तरह के संकट आते हैं।<br/><br/><b>उल्का : ऊं उल्के विघ्नाशिनी कल्याणं कुरु ते नम:</b>></p>
                                          </div>
                                       @elseif($currentYDasha['sub_sub_dasha']['dasha_name'] == 'Siddha')
                                          <div class="col-2">
                                             <span class="btn dash btn-sm" id="cYdasha_mj_dasha">सिद्धा</span>
                                          </div>
                                          <div class="col-10">
                                             <h6 class="text-warning fw-bold dasha-text">प्रत्यंतर दशा</h6>
                                             <div class="d-flex fw-medium">
                                                <p id="cYdasha_mj_start" class="mb-0 me-2">{{!empty($currentYDasha['sub_sub_dasha']['start_date'])?$currentYDasha['sub_sub_dasha']['start_date']:''}}</p> -
                                                <p id="cYdasha_mj_end" class="ms-2">{{!empty($currentYDasha['sub_sub_dasha']['end_date'])?$currentYDasha['sub_sub_dasha']['end_date']:''}}</p>
                                             </div>
                                             <p id="mahadashaDetail" class="font-size-14">सातवीं योगिनी दशा सिद्ध होती है औ इसके स्वामी शुक्र हैं। जिनका जन्म रोहिणी, उत्तराफाल्गुनी, पूर्वाषाढ़ा नक्षत्र में होता है उनकी जन्मकालिक योगिनी दशा सिद्धा होती है। इस दशा के भोग काल में जातक की संपत्ति, भौतिक सुख, प्रेम, आकर्षण प्रभाव आदि में वृद्धि होती है। जिन लोगों पर सिद्धा योगिनी की कृपा होती है उनके जीवन में कोई अभाव नहीं रह जाता है।<br/><br/><b>सिद्धा : ऊं नमो सिद्धे सिद्धिं देहि नमस्तुभ्यं</b></p>
                                          </div>
                                       @elseif($currentYDasha['sub_sub_dasha']['dasha_name'] == 'Sankata')
                                          <div class="col-2">
                                             <span class="btn dash btn-sm" id="cYdasha_mj_dasha">संकटा</span>
                                          </div>
                                          <div class="col-10">
                                             <h6 class="text-warning fw-bold dasha-text">प्रत्यंतर दशा</h6>
                                             <div class="d-flex fw-medium">
                                                <p id="cYdasha_mj_start" class="mb-0 me-2">{{!empty($currentYDasha['sub_sub_dasha']['start_date'])?$currentYDasha['sub_sub_dasha']['start_date']:''}}</p> -
                                                <p id="cYdasha_mj_end" class="ms-2">{{!empty($currentYDasha['sub_sub_dasha']['end_date'])?$currentYDasha['sub_sub_dasha']['end_date']:''}}</p>
                                             </div>
                                             <p id="mahadashaDetail" class="font-size-14">योगिनी दशा चक्र की आठवीं और अंतिम दशा संकटा होती है और इसके स्वामी राहू हैं। जिनका जन्म मृगशिर, हस्त, उत्तराषाढ़ा नक्षत्र में होता है उनकी जन्मकालिक दशा संकटा होती है। संकटा योगिनी दशाकाल में जातक हर ओर से परेशानियों और संकटों से घिर जाता है। संकटों के नाश के लिए इस दशा के भोगकाल में मातृरूप में योगिनी की पूजा करें।<br/><br/><b>संकटा : ऊं ह्रीं संकटे मम रोगंनाशय स्वाहा</b></p>
                                          </div>
                                       @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6 class="font-weight-bold">योगिनी महा दशा</h6>
                                <div class="kundli-basic-details1">
                                    <table class="table">
                                       <thead>
                                          <tr class="bg-light-warning fw-bold">
                                             <th><b>ग्रह</b></th>
                                             <th><b>आरम्भ तिथि</b></th>
                                             <th><b>समाप्ति तिथि</b></th>
                                             <th><b>अवधि</b></th>
                                          </tr>
                                       </thead>
                                       <tbody id="mYdhasha_tbody">
                                          @forelse ($majorYDasha as $myDasha)
                                                <tr>
                                                <td>{{$myDasha['dasha_name']}}</td>
                                                <td>{{$myDasha['start_date']}}</td>
                                                <td>{{$myDasha['end_date']}}</td>
                                                <td>{{$myDasha['duration'].' वर्ष'}}</td>
                                                </tr>
                                          @empty
                                                <tr>
                                                <td colspan="3" class="text-center">{{translate('No_Data_Found')}}</td>
                                                </tr>
                                          @endforelse
                                       </tbody>
                                    </table>
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