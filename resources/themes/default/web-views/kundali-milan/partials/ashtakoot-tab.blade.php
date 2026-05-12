<div class="tab-pane fade" id="ashtkut" role="tabpanel" aria-labelledby="ashtkut-tab">
    <div class="row mt-3">
        <div class="col-md-12">
            <div class="tab-details-block">
                <div class="row">
                    <div class="col-md-7">
                        <h6 class="font-weight-bold">अष्टकूट मिलान विवरण <span
                                class="text-orange font-20 font-weight-bolder"> :
                                {{ $ashtakootData['total']['received_points'] }}/{{ $ashtakootData['total']['total_points'] }}</span>
                        </h6>
                        <div class="kundli-basic-details1 tableFixHead">
                            <table class="table">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col"><b>गुण</b></th>
                                        <th scope="col"><b>बारे में</b></th>
                                        <th scope="col"><b>पुरुष</b></th>
                                        <th scope="col"><b>महिला</b></th>
                                        <th scope="col"><b>कुल अंक</b></th>
                                        <th scope="col"><b>प्राप्त अंक</b></th>
                                    </tr>
                                </thead>
                                <tbody id="tb_ashtakoot" class="bg-white">
                                    @if (count($ashtakootData) > 0)
                                        @foreach ($ashtakootData as $key => $ashtakoot)
                                            @if ($key == 'conclusion')
                                                @break
                                            @endif
                                        <tr>
                                            <td>
                                                <p class="mb-0">
                                                    {{ $key == 'varna' ? 'वर्ण' : ($key == 'vashya' ? 'वश्य' : ($key == 'tara' ? 'तारा' : ($key == 'yoni' ? 'योनि' : ($key == 'maitri' ? 'मैत्री' : ($key == 'gan' ? 'गन' : ($key == 'bhakut' ? 'भकूट' : ($key == 'nadi' ? 'नाडी' : 'कुल अंक'))))))) }}
                                                </p>
                                            </td>
                                            <td>
                                                <p class="mb-0" id="varna_desc">
                                                    {{ isset($ashtakoot['description']) ? $ashtakoot['description'] : '-' }}
                                                </p>
                                            </td>
                                            <td>
                                                <p class="mb-0" id="varna_male">
                                                    {{ isset($ashtakoot['male_koot_attribute']) ? $ashtakoot['male_koot_attribute'] : '-' }}
                                                </p>
                                            </td>
                                            <td>
                                                <p class="mb-0" id="varna_female">
                                                    {{ isset($ashtakoot['female_koot_attribute']) ? $ashtakoot['female_koot_attribute'] : '-' }}
                                                </p>
                                            </td>
                                            <td>
                                                <p class="mb-0" id="varna_total">
                                                    {{ isset($ashtakoot['total_points']) ? $ashtakoot['total_points'] : '-' }}
                                                </p>
                                            </td>
                                            <td>
                                                <p class="mb-0" id="varna_receive">
                                                    {{ isset($ashtakoot['received_points']) ? $ashtakoot['received_points'] : '-' }}
                                                </p>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-5">
                    <h6 class="font-weight-bold">निष्कर्ष</h6>
                    <div class="kundli-basic-details1 p-4">
                        @if ($ashtakootData['conclusion']['status'] == true)
                            <p class="mb-3 font-weight-bolder text-center" ><i
                                    class="fa fa-thumbs-o-up" style="font-size: 2.2em;color:#fe9802"></i></p>
                        @else
                            <p class="mb-0" id="result_down"><i class="fa fa-thumbs-o-down"
                                    style="font-size: 2.2em;"></i></p>
                        @endif
                        <p class="mb-0" id="report">{{ $ashtakootData['conclusion']['report'] }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
