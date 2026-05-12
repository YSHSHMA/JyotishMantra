<div class="tab-pane fade" id="dashakut" role="tabpanel" aria-labelledby="dashakut-tab">
    <div class="row mt-3">
        <div class="col-md-12">
            <div class="tab-details-block">
                <div class="row">
                    <div class="col-md-7">
                        <h6 class="font-weight-bold">अदशकूत मिलान विवरण<span
                                class="text-orange font-20 font-weight-bolder"> :
                                {{ $dashakootData['total']['received_points'] }}/{{ $dashakootData['total']['total_points'] }}</span>
                        </h6>
                        <div class="kundli-basic-details1 tableFixHead">
                            <table class="table">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col"><b>गुण</b></th>
                                        <th scope="col"><b>पुरुष</b></th>
                                        <th scope="col"><b>महिला</b></th>
                                        <th scope="col"><b>कुल अंक</b></th>
                                        <th scope="col"><b>प्राप्त अंक</b></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white">

                                    @if (count($dashakootData) > 0)
                                        @foreach ($dashakootData as $key => $dashakoot)
                                            <tr>
                                                <td>
                                                    <p class="mb-0">
                                                        {{ $key == 'dina' ? 'दिन' : ($key == 'gana' ? 'गण' : ($key == 'yoni' ? 'योनि' : ($key == 'rashi' ? 'राशि' : ($key == 'rasyadhipati' ? 'राशि अधिपति' : ($key == 'rajju' ? 'रज्जू' : ($key == 'vedha' ? 'वेध' : ($key == 'vashya' ? 'वश्य' : ($key == 'mahendra' ? 'महेंद्र' : ($key == 'streeDeergha' ? 'स्त्री दीर्घा' : 'कुल अंक'))))))))) }}
                                                    </p>
                                                </td>
                                                <td>
                                                    <p class="mb-0">
                                                        {{ isset($dashakoot['male_koot_attribute']) ? $dashakoot['male_koot_attribute'] : '-' }}
                                                    </p>
                                                </td>
                                                <td>
                                                    <p class="mb-0">
                                                        {{ isset($dashakoot['female_koot_attribute']) ? $dashakoot['female_koot_attribute'] : '-' }}
                                                    </p>
                                                </td>
                                                <td>
                                                    <p class="mb-0">
                                                        {{ isset($dashakoot['total_points']) ? $dashakoot['total_points'] : '-' }}
                                                    </p>
                                                </td>
                                                <td>
                                                    <p class="mb-0">
                                                        {{ isset($dashakoot['received_points']) ? $dashakoot['received_points'] : '-' }}
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
                            @if ($dashakootData['total']['received_points'] >= 18)
                                <p class="mb-3 font-weight-bolder text-center"><i
                                        class="fa fa-thumbs-o-up" style="font-size: 2.2em;color:#fe9802"></i></p>
                                <p class="mb-0">इस मिलान में 36 अंकों के मुकाबले 18 अंक से अधिक अंक हासिल हुए हैं
                                    इसलिए, दशकूत के विश्लेषण के अनुसार, यह एक अच्छा मिलान है</p>
                            @else
                                <p class="mb-0" id="result_down"><i class="fa fa-thumbs-o-down"
                                        style="font-size: 2.2em;"></i></p>
                                <p class="mb-0" id="report">इस मिलान में 36 अंकों के मुकाबले 18 अंक से कम अंक हासिल
                                    हुए हैं इसलिए, दशकूत के विश्लेषण के अनुसार, यह एक अच्छा मिलान नहीं है</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
