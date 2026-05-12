<div class="tab-pane fade" id="lal-kitab" role="tabpanel" aria-labelledby="lal-kitab-tab">
    <div class="row mt-3">
        <div class="col-md-12">
            <div class="tab-details-block">
                <ul class="nav nav-pills mb-3 justify-content-center" id="pills-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="pills-lal-kitab-upachar-tab" data-toggle="pill"
                            href="#pills-lal-kitab-upachar" role="tab" aria-controls="pills-lal-kitab-upachar"
                            aria-selected="true"
                            style="color: #222 !important; font-weight: 600;border-radius: 39px;padding: 4px 20px;">लाल
                            किताब उपचार</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="pills-lal-kitab-rin-tab" data-toggle="pill" href="#pills-lal-kitab-rin"
                            role="tab" aria-controls="pills-lal-kitab-rin" aria-selected="false"
                            style="color: #222 !important; font-weight: 600;border-radius: 39px;padding: 4px 20px;">लाल
                            किताब ऋण</a>
                    </li>

                </ul>
                <div class="tab-content" id="pills-tabContent">
                    <div class="tab-pane fade show active" id="pills-lal-kitab-upachar" role="tabpanel"
                        aria-labelledby="pills-lal-kitab-upachar-tab">
                        <div class="tab-details-block">
                            <div class="container">
                                <h6 class="fw-bold">ग्रह का चयन</h6>
                                <select class="form-control" id="lalkitabRemediesplanet"
                                    onchange="lalkitabRemediesplanetChange()">
                                    <option value="Sun">सूर्य ग्रह के लिए लाल किताब उपचार </option>
                                    <option value="Moon">चन्द्र ग्रह के लिए लाल किताब उपचार</option>
                                    <option value="Mars">मंगल ग्रह के लिए लाल किताब उपचार</option>
                                    <option value="Mercury">बुध ग्रह के लिए लाल किताब उपचार</option>
                                    <option value="Jupiter">गुरु ग्रह के लिए लाल किताब उपचार</option>
                                    <option value="Venus">शुक्र ग्रह के लिए लाल किताब उपचार</option>
                                    <option value="Saturn">शनि ग्रह के लिए लाल किताब उपचार</option>
                                </select>
                                <h6 class="pb-0 pt-3 fw-bold"><span id="lalkitabRemediesplanetname"
                                        style="color:orange;" class="fw-bold"></span>
                                    ग्रह आपकी कुंडली में <span id="lalkitabRemedieshouse"
                                        style="color:orange;"></span> घर में स्थित है.
                                </h6>
                                <p id="lalkitabRemediesdescription"></p>
                                <h6 class="pb-2 pt-2 fw-bold">उपचार</h6>
                                <ol id="lalkitabRemediesremedies">
                                </ol>
                            </div>
                        </div>
                    </div>

                    {{-- rin --}}
                    <div class="tab-pane fade" id="pills-lal-kitab-rin" role="tabpanel"
                        aria-labelledby="pills-lal-kitab-rin-tab">
                        <div class="tab-details-block">
                            <div class="Container">
                                <ul class="list-group" id="lalkitabrin">
                                    @forelse ($lalkitabRin as $rin)
                                        <li class="list-group-item bg-transparent border-bottom border-dark font-weight-bolder text-center h5"
                                            style="border:0;font-weight: 600;">{{$rin['debt_name']}}</li>
                                        <li class="list-group-item bg-transparent border-0 text-center p-0"> <b
                                                style="color:orange"> संकेत </b> </li>
                                        <li class="list-group-item bg-transparent border-0">{{$rin['indications']}}</li>
                                        <li class="list-group-item bg-transparent border-0 text-center p-0"> <b
                                                style="color:orange"> वाकया </b> </li>
                                        <li class="list-group-item bg-transparent border-0">{{$rin['events']}}</li>
                                    @empty
                                        <li class="list-group-item bg-transparent border-bottom border-dark font-weight-bolder text-center h5"
                                            style="border:0;font-weight: 600;">बधाई आपकी कुंडली में कोई भी ऋण नहीं है</li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
