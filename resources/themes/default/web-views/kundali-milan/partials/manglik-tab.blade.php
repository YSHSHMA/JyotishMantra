<div class="tab-pane fade" id="manglik-info" role="tabpanel" aria-labelledby="manglik-info-tab">
    <div class="row mt-3">
        <div class="col-md-12">
            <div class="tab-details-block">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="font-weight-bold">पुरुष मांगलिक विवरण <div
                                class="badge badge-pill badge-warning new-badge mt-0">Male is
                                {{ $manglikData['male']['percentage_manglik_after_cancellation'] }} % manglik</div>
                        </h6>
                        <div class="kundli-basic-details1 p-3">
                            <h6 class="mb-1 font-weight-bolder">भाव के आधार पर</h6>
                            <ol class="list-group px-4 mb-4" id="male_house">
                                @if (count($manglikData['male']['manglik_present_rule']['based_on_house']) > 0)
                                    @foreach ($manglikData['male']['manglik_present_rule']['based_on_house'] as $maleHouse)
                                        <li class="list-item bg-transparent pt-1 border-0">{{ $maleHouse }}</li>
                                    @endforeach
                                @endif
                            </ol>
                            <h6 class="mb-1 font-weight-bolder">दृष्टि के आधार पर</h6>
                            <ol class="list-group px-4 mb-4" id="male_aspect">
                                @if ($manglikData['male']['manglik_present_rule']['based_on_aspect'] > 0)
                                    @foreach ($manglikData['male']['manglik_present_rule']['based_on_aspect'] as $maleAspect)
                                        <li class="list-item bg-transparent pt-1 border-0">{{ $maleAspect }}</li>
                                    @endforeach
                                @endif
                            </ol>
                            <h6 class="font-weight-bolder">मांगलिक प्रभाव</h6>
                            <p id="male_status">{{ $manglikData['male']['manglik_status'] }}</p>
                            <h6 class="font-weight-bolder">मांगलिक विश्लेषण</h6>
                            <p id="male_report">{{ $manglikData['male']['manglik_report'] }}</p>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h6 class="font-weight-bold">महिला मांगलिक विवरण <div
                                class="badge badge-pill badge-warning new-badge mt-0">Female is
                                {{ $manglikData['female']['percentage_manglik_after_cancellation'] }} % manglik</div>
                        </h6>
                        <div class="kundli-basic-details1 p-3">
                            <h6 class="mb-1 font-weight-bolder">भाव के आधार पर</h6>
                            <ol class="list-group px-4 mb-4" id="female_house">
                                @if (count($manglikData['female']['manglik_present_rule']['based_on_house']) > 0)
                                    @foreach ($manglikData['female']['manglik_present_rule']['based_on_house'] as $femaleHouse)
                                        <li class="list-item bg-transparent pt-1 border-0">{{ $femaleHouse }}</li>
                                    @endforeach
                                @endif
                            </ol>
                            <h6 class="mb-1 font-weight-bolder">दृष्टि के आधार पर</h6>
                            <ol class="list-group px-4 mb-4" id="female_aspect">
                                @if (count($manglikData['female']['manglik_present_rule']['based_on_aspect']) > 0)
                                    @foreach ($manglikData['female']['manglik_present_rule']['based_on_aspect'] as $femaleAspect)
                                        <li class="list-item bg-transparent pt-1 border-0">{{ $femaleAspect }}</li>
                                    @endforeach
                                @endif
                            </ol>
                            <h6 class="font-weight-bolder">मांगलिक प्रभाव</h6>
                            <p id="female_status">{{ $manglikData['female']['manglik_status'] }} </p>
                            <h6 class="font-weight-bolder">मांगलिक विश्लेषण</h6>
                            <p id="female_report">{{ $manglikData['female']['manglik_report'] }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-12 mt-4">
                        <h6 class="font-weight-bold">निष्कर्ष</h6>
                        <div class="kundli-basic-details2 p-3">
                            <p class="text-left" id="female_report">{{ $manglikData['conclusion']['report'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
