{{-- rashi and namakshar modal --}}
<div class="modal fade" id="namakshar-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header calulator-modal-header">
                <h5 class="modal-title" id="exampleModalLabel">राशि एवं नामाक्षर</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
            </div>
            <div class="modal-body mybgcolor text-center">
                <h5 class="font-weight-bolder">आपकी राशि</h5>
                <div class="">
                    <h5 id="rashi" class="fw-bold text-color mynewbadge"></h5>
                </div>
                <hr>
                <h5 class="font-weight-bolder mt-3">आपका नामाक्षर</h5>
                <div class="">
                    <h5 id="namakshar" class="fw-bold text-color mynewbadge"></h5>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- kalsarp dosha modal --}}
<div class="modal fade" id="kalsarp-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header calulator-modal-header">
                <h5 class="modal-title" id="exampleModalLabel">कालसर्प दोष</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
            </div>
            <div class="modal-body mybgcolor text-center">
                <h5 class="font-weight-bolder">Is Kalsarpa Dosha Present ?</h5>
                <div class="col-12 text-center" style="display: ruby;">
                    <p id="result" class="circle fw-bold" style=" font-weight: 900; position: static;color: #fff;"></p>
                </div>
                <h5 id="type" class="font-weight-bolder">कालसर्प प्रकार</h5>
                <p id="conclusion"></p>
                <h6 id="oneline" class="font-weight-bolder">विवरण</h6>
                <p id="one_line"></p>
                <h6 id="report" class="font-weight-bolder">विश्लेषण</h6>
                <p id="ks_report"></p>
            </div>
        </div>
    </div>
</div>

{{-- mangal dosha modal --}}
<div class="modal fade" id="mangalik-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header calulator-modal-header">
                <h5 class="modal-title" id="exampleModalLabel">मांगलिक दोष</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
            </div>
            <div class="modal-body mybgcolor text-center">
                <h6 class="mb-4"> Manglik Dosha Percentage : <span id="mangalikPer" style="color:#f7a708;font-weight: 900;"></span></h6>
                <div class="col-md-12">
                    <h6 class="mb-0 font-weight-bolder">जन्म कुंडली ग्रह भाव पर आधारित</h6>
                    <ul class="list-group mb-4" id="house">
                    </ul>
                    <h6 class="mb-0 font-weight-bolder">जन्म कुंडली ग्रह दृष्टि पर आधारित</h6>
                    <ul class="list-group mb-4" id="aspect">
                    </ul>
                    <h6 class="font-weight-bolder">मांगलिक प्रभाव</h6>
                    <p id="status"></p>
                    <h6 class="font-weight-bolder">मांगलिक विश्लेषण</h6>
                    <p id="report"></p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- mangal dosha modal --}}
<div class="modal fade" id="pitra-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header calulator-modal-header">
                <h5 class="modal-title" id="exampleModalLabel">पित्र दोष</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
            </div>
            <div class="modal-body mybgcolor text-center">
                <h5 class="font-weight-bolder">क्या आपकी कुंडली में पित्र दोष है ?</h5>
                <div class="col-12 text-center" style="display: ruby;">
                <p id="pitra-result" class="circle fw-bold"></p>
               </div> 
                <h5 class="font-weight-bolder">निष्कर्ष</h5>

                <p id="pitra-conclusion"></p>

                <h6 id="rules">नियंस जिसके परिणाम स्वरुप आपकी कुंडली में पित्र दोष स्थापित हुआ है</h6>
                <p id="rules_matched"></p>

                <h6 id="pitra">पित्र दोष क्या है?</h6>
                <p id="pitra_dosha"></p>

                <h6 id="peffects">पित्र दोष के परिणाम</h6>
                <ul class="list-group" id="effects">
                </ul>

                <h6 id="premedies">पित्र दोष के उपाय</h6>
                <ul class="list-group" id="remedies">
                </ul>
            </div>
        </div>
    </div>
</div>

{{-- vimshottari dasha modal --}}
<div class="modal fade" id="vimshottari-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header calulator-modal-header">
                <h5 class="modal-title" id="exampleModalLabel">विमशोत्तरी दशा</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
            </div>
            <div class="modal-body mybgcolor">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="card-body mybgcolor mypadding-0">
                            <h5 class="mb-3 font-weight-bold">वर्तमान विम्शोत्तरी दशा</h5>

                            <div class="row">
                                <div class="col-md-2">
                                    <p id="cVdasha_mj_planet" class="badge badge-danger mybadge"></p>
                                </div>
                                <div class="col-md-10">
                                    <h5>Mahadasha</h5>
                                    <p id="cVdasha_mj_start" class="mb-0"></p>
                                    <p id="cVdasha_mj_end"></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <p id="cVdasha_mi_planet" class="badge badge-danger mybadge"></p>
                                </div>
                                <div class="col-md-10">
                                    <h5>Antar Dasha</h5>
                                    <p id="cVdasha_mi_start" class="mb-0"></p>
                                    <p id="cVdasha_mi_end"></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <p id="cVdasha_smi_planet" class="badge badge-danger mybadge"></p>
                                </div>
                                <div class="col-md-10">
                                    <h5>Pratyantar Dasha</h5>
                                    <p id="cVdasha_smi_start" class="mb-0"></p>
                                    <p id="cVdasha_smi_end"></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <p id="cVdasha_ssmi_planet" class="badge badge-danger mybadge"></p>
                                </div>
                                <div class="col-md-10">
                                    <h5>Sookshma Dasha</h5>
                                    <p id="cVdasha_ssmi_start" class="mb-0"></p>
                                    <p id="cVdasha_ssmi_end"></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <p id="cVdasha_sssmi_planet" class="badge badge-danger mybadge"></p>
                                </div>
                                <div class="col-md-10">
                                    <h5>Pran Dasha</h5>
                                    <p id="cVdasha_sssmi_start" class="mb-0"></p>
                                    <p id="cVdasha_sssmi_end"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-8">
                        <div class="card-body mybgcolor mypadding-0">
                            <h5 class="mb-3 font-weight-bold">विम्शोत्तरी महा दशा</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="mythead-dark">
                                            <tr>
                                                <th scope="col">Planet</th>
                                                <th scope="col">Start Date</th>
                                                <th scope="col">End Date</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white" id="mVdhasha_tbody">

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

{{-- mool ank modal --}}
<div class="modal fade" id="moolank-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header calulator-modal-header">
                <h5 class="modal-title" id="exampleModalLabel">मूल अंक</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
            </div>
            <div class="modal-body mybgcolor text-center">
                <h5 class="font-weight-bolder">आपका मूल अंक</h5>
                <div class="">
                    <h5 id="mook-ank" class="fw-bold text-color mynewbadge"></h5>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- gem suggestion modal --}}
<div class="modal fade" id="gem-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header calulator-modal-header">
                <h5 class="modal-title" id="exampleModalLabel">रत्न सुझाव</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
            </div>
            <div class="modal-body mybgcolor text-center">
                <div class="row mt-3">
                    <div class="col-md-4">
                        <div class="row border-0 box-shadow pr-1 mb-4">
                            <div class="card-body mybgcolor">
                                <h2 class="h4 mb-4 text-center font-weight-bold">जीवन रत्न</h2>
                                <div class="d-flex justify-content-center mb-4">
                                    <img id="lifeimg" src="" class="text-center rounded-circle" width="50" alt="life gem">
                                </div>
                                <h5 id="lifegemname" class="text-center font-weight-bolder"></h5>
                                <div class="table-responsive">
                                    <table class="table bg-white">
                                        <tbody>
                                            <tr class="">
                                                <td>उपरत्न </td>
                                                <td><p id="lifesubstitude" class="mb-0"></p></td>
                                            </tr>
                                            <tr class="">
                                                <td>ऊँगली </td>
                                                <td><p id="lifefinger" class="mb-0"></p></td>
                                            </tr>
                                            <tr class="">
                                                <td>वजन </td>
                                                <td><p id="lifeweight" class="mb-0"> </p> </td>
                                            </tr>
                                            <tr class="">
                                                <td>दिन </td>
                                                <td><p id="lifeday" class="mb-0"></p></td>
                                            </tr>
                                            <tr class="">
                                                <td>देव </td>
                                                <td><p id="lifedeity" class="mb-0"></p></td>
                                            </tr>
                                            <tr class="">
                                                <td>धातु </td>
                                                <td><font color=orange><p id="lifemetal" class="mb-0"></p></font></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                
                            </div>
                        </div>
                    </div>
        
                    <div class="col-md-4">
                        <div class="row border-0 box-shadow px-1 mb-4">
                            <div class="card-body mybgcolor">
                                <h2 class="h4 mb-4 text-center font-weight-bold">लाभ रत्न</h2>
                                <div class="d-flex justify-content-center mb-4">
                                    <img id="beneficimg" src="" class="text-center rounded-circle" width="50" alt="benefic gem">
                                </div>
                                <h5 id="beneficgemname" class="text-center font-weight-bolder"></h5>
                                <div class="table-responsive">
                                    <table class="table bg-white">
                                        <tbody>
                                            <tr class="">
                                                <td>उपरत्न</td>
                                                <td><p id="beneficsubstitude" class="mb-0"></p></td>
                                            </tr>
                                            <tr class="">
                                                <td>ऊँगली</td>
                                                <td><p id="beneficfinger" class="mb-0"></p></td>
                                            </tr>
                                            <tr class="">
                                                <td>वजन</td>
                                                <td><p id="beneficweight" class="mb-0"></p></td>
                                            </tr>
                                            <tr class="">
                                                <td>दिन</td>
                                                <td><p id="beneficday" class="mb-0"></p></td>
                                            </tr>
                                            <tr class="">
                                                <td>देव</td>
                                                <td><p id="beneficdeity" class="mb-0"></p></td>
                                            </tr>
                                            <tr class="">
                                                <td>धातु</td>
                                                <td><font color=orange><p id="beneficmetal" class="mb-0"></p></font></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
        
                    <div class="col-md-4">
                        <div class="row border-0 box-shadow pl-1 mb-4">
                            <div class="card-body mybgcolor">
                                <h2 class="h4 mb-4 text-center font-weight-bold">भाग्य रत्न</h2>
                                <div class="d-flex justify-content-center mb-4">
                                    <img id="luckyimg" src="" class="text-center rounded-circle" width="50" alt="lucky gem">
                                </div>
                                <h5 id="luckygemname" class="text-center font-weight-bolder"></h5>
                                <div class="table-responsive">
                                    <table class="table bg-white">
                                        <tbody>
                                            <tr class="">
                                                <td>उपरत्न</td>
                                                <td><p id="luckysubstitude" class="mb-0"></p></td>
                                            </tr>
                                            <tr class="">
                                                <td>ऊँगली</td>
                                                <td><p id="luckyfinger" class="mb-0"></p></td>
                                            </tr>
                                            <tr class="">
                                                <td>वजन</td>
                                                <td><p id="luckyweight" class="mb-0"></p></td>
                                            </tr>
                                            <tr class="">
                                                <td>दिन</td>
                                                <td><p id="luckyday" class="mb-0"></p></td>
                                            </tr>
                                            <tr class="">
                                                <td>देव</td>
                                                <td><p id="luckydeity" class="mb-0"></p></td>
                                            </tr>
                                            <tr class="">
                                                <td>धातु</td>
                                                <td><font color=orange><p id="luckymetal" class="mb-0"></p></font></td>
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
    </div>
</div>

{{-- rudraksha modal --}}
<div class="modal fade" id="rudraksha-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header calulator-modal-header">
                <h5 class="modal-title" id="exampleModalLabel">रुद्राक्ष सुझाव</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
            </div>
            <div class="modal-body mybgcolor text-center">
                <h5 id="rudrakshaname" class="font-weight-bolder"></h5>
                <h6><font color=red><strong>आपको पहनने की सलाह दी जाती है - </strong></font>   </h6>
                <h5 id="rrudrakshaname" class="font-weight-bolder"></h5>
                <p id="rudrakshadetail"></p>
            </div>
        </div>
    </div>
</div>

{{-- pooja modal --}}
<div class="modal fade" id="pooja-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header calulator-modal-header">
                <h5 class="modal-title" id="exampleModalLabel">पूजा सुझाव</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
            </div>
            <div class="modal-body mybgcolor text-center">
                <p id="summary"></p>
                <ul class="list-group" id="poojasuggest">
                </ul>
            </div>
        </div>
    </div>
</div>