<div class="tab-pane fade" id="dosh" role="tabpanel" aria-labelledby="dosh-tab">
    <div class="row mt-3">
        <div class="col-md-12">
            <div class="tab-details-block">
                <ul class="nav nav-pills mb-3 justify-content-center" id="pills-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="pills-manglik-dosha-tab" data-toggle="pill"
                            href="#pills-manglik-dosha" role="tab" aria-controls="pills-manglik-dosha"
                            aria-selected="true"
                            style="color: #222 !important; font-weight: 600;border-radius: 39px;padding: 4px 20px;">मांगलिक
                            दोष</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="pills-pitra-dosha-tab" data-toggle="pill" href="#pills-pitra-dosha"
                            role="tab" aria-controls="pills-pitra-dosha" aria-selected="false"
                            style="color: #222 !important; font-weight: 600;border-radius: 39px;padding: 4px 20px;">पित्र
                            दोष</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="pills-kaal-sarp-dosha-tab" data-toggle="pill"
                            href="#pills-kaal-sarp-dosha" role="tab" aria-controls="pills-kaal-sarp-dosha"
                            aria-selected="false"
                            style="color: #222 !important; font-weight: 600;border-radius: 39px;padding: 4px 20px;">कालसर्प
                            दोष</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="pills-shani-sade-sati-dosha-tab" data-toggle="pill"
                            href="#pills-shani-sade-sati-dosha" role="tab"
                            aria-controls="pills-shani-sade-sati-dosha" aria-selected="false"
                            style="color: #222 !important; font-weight: 600;border-radius: 39px;padding: 4px 20px;">शनि
                            साढ़े साती दोष</a>
                    </li>
                </ul>
                <div class="tab-content" id="pills-tabContent">
                    <div class="tab-pane fade show active" id="pills-manglik-dosha" role="tabpanel"
                        aria-labelledby="pills-manglik-dosha-tab">
                        <div class="tab-details-block">
                            <h6 class="fw-bold pt-3">मांगलिक विश्लेषण</h6>
                            <!-- <span class="badge badge-light badge-success custom-badge mt-2" id="report"></span> -->
                            <p class="alert alert-success" id="report"> {{ $manglikDosha['manglik_report'] }}</p>
                            <h6 class="fw-bold pt-3">विवरण</h6>
                            <span class="badge badge-light badge-danger custom-badge mt-2">प्रतिशत<br><span
                                    id="mangalikPer"
                                    class="text-dark fw-bold">{{ $manglikDosha['percentage_manglik_after_cancellation'] }}
                                    %</span></span>
                            <span class="badge badge-light badge-danger custom-badge mt-2">मांगलिक प्रभाव<br><span
                                    class="text-dark fw-bold" id="status">{{ $manglikDosha['manglik_status'] }}
                                </span></span>
                            <h6 class="fw-bold pt-3">जन्म कुंडली ग्रह भाव पर आधारित</h6>
                            <div class="alert alert-success">
                                <ul id="house" class="p-0 text-dark">
                                    @foreach ($manglikDosha['manglik_present_rule']['based_on_house'] as $house)
                                        <li class="list-group-item bg-transparent pb-0 border-0"> - {{ $house }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <h6 class="fw-bold pt-3">जन्म कुंडली ग्रह दृष्टि पर आधारित</h6>
                            <div class="alert alert-success">
                                <ul id="aspect" class="p-0 text-dark">

                                    @foreach ($manglikDosha['manglik_present_rule']['based_on_aspect'] as $aspect)
                                        <li class="list-group-item bg-transparent pb-0 border-0"> - {{ $aspect }}
                                        </li>
                                    @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- pitra dosha --}}
                    <div class="tab-pane fade" id="pills-pitra-dosha" role="tabpanel"
                        aria-labelledby="pills-pitra-dosha-tab">
                        <div class="tab-details-block">
                            <div class="row">
                                <div class="col-12 text-center" style="display: ruby;">
                                    <div class="circle fw-bold" id="">
                                        {{ $pitraDosha['is_pitri_dosha_present'] == true ? 'हाँ' : 'नहीं' }}</div>
                                </div>
                                <div>
                                    <h6 class="text-warning fw-bold dasha-text">क्या आपकी कुंडली में पित्र दोष है ?</h6>
                                    <h6 id="rrudrakshaname" class="fw-bold">निष्कर्ष</h6>
                                    <p id="rudrakshadetail">{{ $pitraDosha['conclusion'] }}</p>
                                    @if ($pitraDosha['is_pitri_dosha_present'] == true)
                                        @foreach ($pitraDosha['rules_matched'] as $rules)
                                            {{ $rules }}
                                        @endforeach
                                        <h6 id="pitra">पित्र दोष क्या है?</h6>
                                        <p>{{ $pitraDosha['what_is_pitri_dosha'] }}</p>
                                        <h6 id="peffects">पित्र दोष के परिणाम</h6>
                                        @foreach ($pitraDosha['effects'] as $effects)
                                            <ul class="list-group">
                                                <li class="list-group-item bg-transparent border-0">{{ $effects }}
                                                </li>
                                            </ul>
                                        @endforeach
                                        <h6 id="premedies">पित्र दोष के उपाय</h6>
                                        @foreach ($pitraDosha['remedies'] as $remedies)
                                            <ul class="list-group">
                                                <li class="list-group-item bg-transparent border-0">{{ $remedies }}
                                                </li>
                                            </ul>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- kal sarp dosha --}}
                    <div class="tab-pane fade" id="pills-kaal-sarp-dosha" role="tabpanel"
                        aria-labelledby="pills-kaal-sarp-dosha-tab">
                        <div class="tab-details-block">
                            <div class="row">
                                <div class="col-12 text-center" style="display: ruby;">
                                    <div class="circle fw-bold" id="">
                                        {{ $kalsarpDosha['present'] == true ? 'हाँ' : 'नहीं' }}</div>
                                </div>
                                <div>
                                    <h6 class="text-warning fw-bold dasha-text">Is Kalsarpa Dosha Present ?<h6>
                                            <h6 id="rrudrakshaname" class="fw-bold">विवरण</h6>
                                            <p id="rudrakshadetail">{{ $kalsarpDosha['one_line'] }}</p>

                                            @if ($kalsarpDosha['present'] == true)
                                                <h5 id="type" class="font-weight-bolder">कालसर्प प्रकार</h5>
                                                {{ $kalsarpDosha['type'] }}
                                                <h6 id="oneline" class="font-weight-bolder">विवरण</h6>
                                                {!! $kalsarpDosha['report']['report'] !!}
                                            @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- sade sati --}}
                    <div class="tab-pane fade" id="pills-shani-sade-sati-dosha" role="tabpanel"
                        aria-labelledby="pills-shani-sade-sati-dosha-tab">
                        <div class="tab-details-block">
                            <div class="row">
                                <div class="col-12 text-center" style="display: ruby;">
                                    <div class="circle fw-bold" id="">{{$sadhesatiDosha['sadhesati_status']==true?'हाँ':'नहीं'}}</div>
                                </div>
                                <div class="container ">
                                    <h6 class="fw-bold pt-2">क्या इस समय आपके कुंडली में साढ़े साती दोष है ?</h6>
                                    <h6 class="fw-bold pt-2 text-warning">निष्कर्ष</h6>
                                    <h6 class="fw-bold pt-2"><span id="is_undergoing_sadhesati">{{$sadhesatiDosha['is_undergoing_sadhesati']}}</span></h6>
                                    <h6 class="fw-bold pt-2 text-warning pb-2">साढ़े साती विवरण</h6>
                                    <div class="">
                                        <div style="display: inline-grid;">
                                            <span class="badge badge-light badge-secondary custom-badge mb-1">क्या शनि
                                                वक्री है ? : <span class="fw-bold text-warning"
                                                    id="is_saturn_retrograde">{{$sadhesatiDosha['is_saturn_retrograde']==true?'हाँ':'नहीं'}}</span></span>

                                            <span class="badge badge-light badge-secondary custom-badge">विचार तिथि :
                                                <span class="fw-bold text-warning"
                                                    id="consideration_date">{{$sadhesatiDosha['consideration_date']}}</span></span>
                                        </div>
                                        <div class="mb-2">

                                            <span class="badge badge-light badge-secondary custom-badge mt-2">चन्द्र
                                                राशि : <span class="fw-bold text-warning"
                                                    id="moon_sign">{{$sadhesatiDosha['moon_sign']}}</span></span>
                                            <span class="badge badge-light badge-secondary custom-badge mt-2">शनि राशि
                                                : <span class="fw-bold text-warning"
                                                    id="saturn_sign">{{$sadhesatiDosha['saturn_sign']}}</span></span>
                                        </div>
                                    </div>
                                    <h6 class="fw-bold pt-2">साढ़े साती क्या है ?</h6>
                                    <p class="" id="what_is_sadhesati">{{$sadhesatiDosha['what_is_sadhesati']}}</p>

                                    <h6 class="fw-bold pt-2">साढ़े साती का उपाय</h6>
                                    <ul>
                                        <li>साढ़े की परेशानी से बचने के लिए नियमित हनुमान चालीसा का पाठ करना चाहिए।</li>
                                        <li>इस ग्रह दशा से बचने के लिए काले घोड़े की नाल की अंगूठी बनाकर उसे दाएं हाथ की
                                            मध्यमा उंगली में पहनना चाहिए।</li>
                                        <li>शनि देव को शनिवार के दिन सरसों का तेल और तांबा भेट करना चाहिए।</li>
                                        <li>किसी गरीब व्यक्ति को काले कम्बल का दान करें।</li>
                                        <li>शिवलिंग पर काले टिल अर्पित करें और जल चढ़ाएं।</li>
                                        <li>अगर आप रत्न धारण करना चाहते हैं तो किसी अच्छे ज्योतिषशास्त्री से सम्पर्क
                                            करें और उनकी सलाह से रत्न धारण करें।</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
