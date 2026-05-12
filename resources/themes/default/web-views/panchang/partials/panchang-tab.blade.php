<style>
    .event-table {
        width: 100%;
        max-width: 700px;
        border: 1px solid #ddd;
        border-radius: 8px;
    }

    .event-date {
        text-align: center;
        font-size: 1.5rem;
        font-weight: bold;
        color: #555;
        border-right: 1px solid #ddd;
    }

    .event-day {
        color: #888;
        font-size: 1rem;
    }

    .event-info h5 {
        margin: 0;
        font-weight: bold;
        color: #B64623;
        font-size: 18px;
    }

    .event-info,
    .p1 {
        margin: 0;
        font-size: 1.1rem;
        color: #444;
        font-size: 14px;
        color: #fe9802;
    }

    .event-info,
    .p2 {
        margin: 0;
        font-size: 1.1rem;
        color: #444;
        font-size: 12px;
    }

    .event-image img {
        height: 70px;
        width: auto;
    }

    td {
        vertical-align: middle;
        padding: 15px;
    }

    .event-date span {
        display: block;
    }

    /* fast festival */
    .fast-festival {
        height: 50px;
        border: 1px solid;
        border-radius: 10px 10px 0px 0px;
        display: flex;
        align-items: center;
        padding-left: 10px;
        background-color: #763f03;
        font-weight: bold;
        color: white;
    }

    /* muhurat */
    .accordian-heading {
        border: 1px solid;
        border-radius: 10px;
        color: #fe9802;
    }

    .accordian-heading button {
        color: black;
        font-weight: bold;
    }

    .accordian-body {
        border: 1px solid;
        border-radius: 10px;
        text-align: center;
        padding-top: 5px;
        height: 60px;
    }

    /* Default down arrow */
    .arrow:after {
        content: '\25BC';
        /* Downward arrow (↓) */
        font-size: 14px;
        margin-left: 10px;
        transition: transform 0.3s ease;
    }

    /* When accordion is expanded, show up arrow */
    button[aria-expanded="true"] .arrow:after {
        content: '\25B2';
        /* Upward arrow (↑) */
    }
</style>
<div class="tab-pane fade show active" id="panchang-info" role="tabpanel" aria-labelledby="panchang-info-tab">
    <div class="row">
        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
            <table class="table kundli-basic-details">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col" colspan="2"><b id="panchang-hindi-date"></b></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th scope="row"><img src="{{ asset('public/assets/front-end/img/moon_phases.png') }}"
                                class="img-fluid img-w-90"></th>
                        <td>
                            <h4 class="mb-1"><b><span id="tithi-name"></span> <span id="paksha-name"></span> <span
                                        id="panchang-day"></span> </b></h4>
                            <p class="mb-0 text-muted"><span id="purnimanta"></span> मास</p>
                            <p class="text-muted"><span id="ritu"></span> <span id="vikramsamvat-name"></span>
                                <span id="vikramsamvat-year"></span>
                            </p>
                        </td>
                    </tr>
                </tbody>
            </table>
            <table class="table kundli-basic-details">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col" colspan="2"><b>सूर्यास्त-सूर्योदय</b></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th scope="row"><b><img src="{{ asset('public/assets/front-end/img/sunrise.png') }}"
                                    class="mr-2">सूर्योदय</b></th>
                        <td id="sunrise"></td>
                    </tr>
                    <tr>
                        <th scope="row"><b><img src="{{ asset('public/assets/front-end/img/sunset.png') }}"
                                    class="mr-2">सूर्यास्त</b></th>
                        <td id="sunset"></td>
                    </tr>
                    <tr>
                        <th scope="row"><b><img src="{{ asset('public/assets/front-end/img/moonrise.png') }}"
                                    class="mr-2">चंद्रोदय</b></th>
                        <td id="moonrise"></td>
                    </tr>
                    <tr>
                        <th scope="row"><b><img src="{{ asset('public/assets/front-end/img/moonset.png') }}"
                                    class="mr-2">चन्द्रास्त</b></th>
                        <td id="moonset"></td>
                    </tr>
                </tbody>
            </table>
            <table class="table kundli-basic-details">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col" colspan="2"><b>शुभ-अशुभ समय</b></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td scope="row" class="alert alert-success">
                            <h6 class="mb-0 font-weight-bold">शुभ मुहूर्त</h6>
                            <p class="mb-0"><span id="abhijeet-start"></span> से <span id="abhijeet-end"></span></p>
                        </td>
                        <td scope="row" class="alert alert-warning">
                            <h6 class="mb-0 font-weight-bold">गुलिक काल</h6>
                            <p class="mb-0"><span id="guli-start"></span> से <span id="guli-end"></span></p>
                        </td>
                    </tr>
                    <tr>
                        <td scope="row" class="alert alert-danger">
                            <h6 class="mb-0 font-weight-bold">राहुकाल</h6>
                            <p class="mb-0"><span id="rahu-start"></span> से <span id="rahu-end"></span></p>
                        </td>
                        <td scope="row" class="alert alert-danger">
                            <h6 class="mb-0 font-weight-bold">यमघण्टकाल</h6>
                            <p class="mb-0"><span id="yamghanta-start"></span> से <span id="yamghanta-end"></span></p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
            <table class="table kundli-basic-details">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col" colspan="2"><b>आज का पंचांग</b></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td scope="row">
                            <p class="mb-0 text-muted">तिथि</p>
                            <h6 class="mb-0 font-weight-bold"><span id="paksha-detail-name"></span> <span
                                    id="tithi-detail-name"></span></h6>
                            <p class="mb-0"> <span id="tithi-detail-hour"></span>:<span
                                    id="tithi-detail-min"></span>:<span id="tithi-detail-sec"></span> तक</p>
                        </td>
                        <td scope="row">
                            <p class="mb-0 text-muted">नक्षत्र</p>
                            <h6 class="mb-0 font-weight-bold"><span id="nakshatra-detail-name"></span></h6>
                            <p class="mb-0"><span id="nakshatra-detail-hour"></span>:<span
                                    id="nakshatra-detail-min"></span>:<span id="nakshatra-detail-sec"></span> तक</p>
                        </td>
                    </tr>
                    <tr>
                        <td scope="row">
                            <p class="mb-0 text-muted">योग</p>
                            <h6 class="mb-0 font-weight-bold"><span id="yoga-detail-name"></span></h6>
                            <p class="mb-0"><span id="yoga-detail-hour"></span>:<span
                                    id="yoga-detail-min"></span>:<span id="yoga-detail-sec"></span> तक</p>
                        </td>
                        <td scope="row">
                            <p class="mb-0 text-muted">करण</p>
                            <h6 class="mb-0 font-weight-bold"><span id="karana-detail-name"></span></h6>
                            <p class="mb-0"><span id="karana-detail-hour"></span>:<span
                                    id="karana-detail-min"></span>:<span id="karana-detail-sec"></span> तक</p>
                        </td>
                    </tr>
                    <tr>
                        <td scope="row">
                            <p class="mb-0 text-muted">महीना अमान्त</p>
                            <h6 class="mb-0 font-weight-bold"><span id="amanta-detail-name"></span></h6>
                        </td>
                        <td scope="row">
                            <p class="mb-0 text-muted">महीना पूर्णिमांत</p>
                            <h6 class="mb-0 font-weight-bold"><span id="purnimanta-detail-name"></span></h6>
                        </td>
                    </tr>
                    <tr>
                        <td scope="row">
                            <p class="mb-0 text-muted">विक्रम संवत</p>
                            <h6 class="mb-0 font-weight-bold"><span id="vikramsamvat-detail-year"></span> (<span
                                    id="vikramsamvat-detail-name"></span>)</h6>
                        </td>
                        <td scope="row">
                            <p class="mb-0 text-muted">शक संवत</p>
                            <h6 class="mb-0 font-weight-bold"><span id="shakasamvat-detail-year"></span> (<span
                                    id="shakasamvat-detail-name"></span>)</h6>
                        </td>
                    </tr>
                    <tr>
                        <td scope="row">
                            <p class="mb-0 text-muted">सूर्य राशि</p>
                            <h6 class="mb-0 font-weight-bold"><span id="sun-detail-sign"></span></h6>
                        </td>
                        <td scope="row">
                            <p class="mb-0 text-muted">चंद्र राशि</p>
                            <h6 class="mb-0 font-weight-bold"><span id="moon-detail-sign"></span></h6>
                        </td>
                    </tr>
                    <tr>
                        <td scope="row">
                            <p class="mb-0 text-muted">दिशाशूल</p>
                            <h6 class="mb-0 font-weight-bold"><span id="dishashool-detail-name"></span></h6>
                        </td>
                        <td scope="row">
                            <p class="mb-0 text-muted">अधिक मास</p>
                            <h6 class="mb-0 font-weight-bold"><span id="adhikmas-detail"></span></h6>
                        </td>
                    </tr>
                    <tr>
                        <td scope="row">
                            <p class="mb-0 text-muted">ऋतु</p>
                            <h6 class="mb-0 font-weight-bold"><span id="ritu-detail-name"></span></h6>
                        </td>
                        <td scope="row">
                            <p class="mb-0 text-muted">अयन</p>
                            <h6 class="mb-0 font-weight-bold"><span id="ayana-detail-name"></span></h6>
                        </td>
                    </tr>
                </tbody>
            </table>
            <img src="{{ asset('public/assets/front-end/img/ad-sample-900x100.png') }}" class="img-fluid"
                style="height: 73px;width: 100%;border-radius: 10px;">
        </div>
    </div>
    {{-- fast festival tab --}}
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <div class="fast-festival">
                व्रत और त्यौहार
            </div>
            <!-- start tabs -->
            <div class="tabbable-responsive my-2">
                <div class="tabbable">
                    <ul class="nav nav-pills mb-3 justify-content-center" id="linxea-avenir" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="fast-tab" data-toggle="tab" href="#fast"
                                role="tab" aria-controls="first" aria-selected="true"
                                style="color: #222 !important;font-weight: 600;padding: 5px 15px;">{{ translate('व्रत') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-dark" id="festival-tab" data-toggle="tab" href="#festival"
                                role="tab" aria-controls="second" aria-selected="false"
                                style="color: #222 !important;font-weight: 600;padding: 5px 15px;">
                                {{ translate('त्योहार') }}</a>
                        </li>
                    </ul>
                </div>
            </div>
            <!-- end tabs -->
            <div class="tab-content">
                {{-- fast-infoTab --}}
                @include('web-views.panchang.partials.fast-tab')

                {{-- festival-infoTab --}}
                @include('web-views.panchang.partials.festival-tab')
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <div class="fast-festival">
                शुभ मुहूर्त
            </div>
            <table class="table kundli-basic-details event-table">
                <tbody>

                    <div class="accordion" id="accordionExample">
                        {{-- marriage --}}
                        <div class="card px-2">
                            <div class="card-header" id="headingOne">
                                <h2 class="mb-0 accordian-heading" style="display: flex; align-items: center;">
                                    <img src="{{ asset('public/assets/front-end/img/muhurat/marriage.png') }}"
                                        alt="" style="height: 31px; width: 40px; margin-left: 10px;">
                                    <button class="btn btn-link btn-block text-left" type="button"
                                        data-toggle="collapse" data-target="#collapseOne" aria-expanded="true"
                                        aria-controls="collapseOne" style="display: flex; align-items: center;">
                                        Marriage
                                        <span class="ml-auto arrow"></span>
                                    </button>
                                </h2>
                            </div>

                            <div id="collapseOne" class="collapse show" aria-labelledby="headingOne"
                                data-parent="#accordionExample">
                                <div class="card-body">
                                    <div class="row">
                                        @if ($marriageMuhuratData['status'] == true)
                                            @foreach ($marriageMuhuratData['data'] as $marriage)
                                                @php
                                                    $marriageExplode = explode(' ', $marriage['titleLink']);
                                                @endphp
                                                <div class="col-2 m-1 accordian-body">
                                                    <a href="javascript:0" data-title="Marriage"
                                                        data-image="marriage.png"
                                                        data-date="{{ $marriage['titleLink'] }}"
                                                        data-muhurat="{{ $marriage['muhurat'] }}"
                                                        data-nakshatra="{{ $marriage['nakshatra'] }}"
                                                        data-tithi="{{ $marriage['tithi'] }}"
                                                        onclick="muhuratModal(this)">
                                                        <h6 class="mb-1">{{ explode(',', $marriageExplode[1])[0] }}
                                                        </h6>
                                                        <h6 class="mb-1">{{ substr($marriageExplode[0], 0, 3) }}
                                                        </h6>
                                                    </a>
                                                </div>
                                            @endforeach
                                        @else
                                            <p>No Data Found</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- vehicle purchase --}}
                        <div class="card px-2">
                            <div class="card-header" id="headingTwo">
                                <h2 class="mb-0 accordian-heading" style="display: flex; align-items: center;">
                                    <img src="{{ asset('public/assets/front-end/img/muhurat/vehicle-purchase.png') }}"
                                        alt="" style="height: auto; width: 40px; margin-left: 10px;">
                                    <button class="btn btn-link btn-block text-left" type="button"
                                        data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false"
                                        aria-controls="collapseTwo" style="display: flex; align-items: center;">
                                        Vehicle Purchase
                                        <span class="ml-auto arrow"></span>
                                    </button>
                                </h2>
                            </div>

                            <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo"
                                data-parent="#accordionExample">
                                <div class="card-body">
                                    <div class="row">
                                        @if ($vehicleMuhuratData['status'] == true)
                                            @foreach ($vehicleMuhuratData['data'] as $vehiclepurchase)
                                                @php
                                                    $vehicleExplode = explode(' ', $vehiclepurchase['titleLink']);
                                                @endphp
                                                <div class="col-2 m-1 accordian-body">
                                                    <a href="javascript:0" data-title="Vehicle Purchase"
                                                        data-image="vehicle-purchase.avif"
                                                        data-date="{{ $vehiclepurchase['titleLink'] }}"
                                                        data-muhurat="{{ $vehiclepurchase['muhurat'] }}"
                                                        data-nakshatra="{{ $vehiclepurchase['nakshatra'] }}"
                                                        data-tithi="{{ $vehiclepurchase['tithi'] }}"
                                                        onclick="muhuratModal(this)">
                                                        <h6 class="mb-1">{{ explode(',', $vehicleExplode[1])[0] }}
                                                        </h6>
                                                        <h6 class="mb-1">{{ substr($vehicleExplode[0], 0, 3) }}</h6>
                                                    </a>
                                                </div>
                                            @endforeach
                                        @else
                                            <p>No Data Found</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- grah pravesh --}}
                        <div class="card px-2">
                            <div class="card-header" id="headingThree">
                                <h2 class="mb-0 accordian-heading" style="display: flex; align-items: center;">
                                    <img src="{{ asset('public/assets/front-end/img/muhurat/grah-pravesh.png') }}"
                                        alt="" style="height: auto; width: 40px; margin-left: 10px;">
                                    <button class="btn btn-link btn-block text-left" type="button"
                                        data-toggle="collapse" data-target="#collapseThree" aria-expanded="false"
                                        aria-controls="collapseThree" style="display: flex; align-items: center;">
                                        Grah Pravesh
                                        <span class="ml-auto arrow"></span>
                                    </button>
                                </h2>
                            </div>

                            <div id="collapseThree" class="collapse" aria-labelledby="headingThree"
                                data-parent="#accordionExample">
                                <div class="card-body">
                                    <div class="row">
                                        @if ($grahpraveshMuhuratData['status'] == true)
                                            @foreach ($grahpraveshMuhuratData['data'] as $grahpravesh)
                                                @php
                                                    $grahpraveshExplode = explode(' ', $grahpravesh['titleLink']);
                                                @endphp
                                                <div class="col-2 m-1 accordian-body">
                                                    <a href="javascript:0" data-title="Grah Pravesh"
                                                        data-image="grah-pravesh.png"
                                                        data-date="{{ $grahpravesh['titleLink'] }}"
                                                        data-muhurat="{{ $grahpravesh['muhurat'] }}"
                                                        data-nakshatra="{{ $grahpravesh['nakshatra'] }}"
                                                        data-tithi="{{ $grahpravesh['tithi'] }}"
                                                        onclick="muhuratModal(this)">
                                                        <h6 class="mb-1">
                                                            {{ explode(',', $grahpraveshExplode[1])[0] }}
                                                        </h6>
                                                        <h6 class="mb-1">{{ substr($grahpraveshExplode[0], 0, 3) }}
                                                        </h6>
                                                    </a>
                                                </div>
                                            @endforeach
                                        @else
                                            <p>No Data Found</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- propery purchase --}}
                        <div class="card px-2">
                            <div class="card-header" id="headingFour">
                                <h2 class="mb-0 accordian-heading" style="display: flex; align-items: center;">
                                    <img src="{{ asset('public/assets/front-end/img/muhurat/property-purchase.png') }}"
                                        alt="" style="height: auto; width: 40px; margin-left: 10px;">
                                    <button class="btn btn-link btn-block text-left" type="button"
                                        data-toggle="collapse" data-target="#collapseFour" aria-expanded="false"
                                        aria-controls="collapseFour" style="display: flex; align-items: center;">
                                        Property Purchase
                                        <span class="ml-auto arrow"></span>
                                    </button>
                                </h2>
                            </div>

                            <div id="collapseFour" class="collapse" aria-labelledby="headingFour"
                                data-parent="#accordionExample">
                                <div class="card-body">
                                    <div class="row">
                                        @if ($propertyMuhuratData['status'] == true)
                                            @foreach ($propertyMuhuratData['data'] as $property)
                                                @php
                                                    $propertyExplode = explode(' ', $property['titleLink']);
                                                @endphp
                                                <div class="col-2 m-1 accordian-body">
                                                    <a href="javascript:0" data-title="Property Purchase"
                                                        data-image="property-purchase.png"
                                                        data-date="{{ $property['titleLink'] }}"
                                                        data-muhurat="{{ $property['muhurat'] }}"
                                                        data-nakshatra="{{ $property['nakshatra'] }}"
                                                        data-tithi="{{ $property['tithi'] }}"
                                                        onclick="muhuratModal(this)">
                                                        <h6 class="mb-1">{{ explode(',', $propertyExplode[1])[0] }}
                                                        </h6>
                                                        <h6 class="mb-1">{{ substr($propertyExplode[0], 0, 3) }}
                                                        </h6>
                                                    </a>
                                                </div>
                                            @endforeach
                                        @else
                                            <p>No Data Found</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- mundan --}}
                        <div class="card px-2">
                            <div class="card-header" id="headingFive">
                                <h2 class="mb-0 accordian-heading" style="display: flex; align-items: center;">
                                    <img src="{{ asset('public/assets/front-end/img/muhurat/mundan.png') }}"
                                        alt="" style="height: auto; width: 40px; margin-left: 10px;">
                                    <button class="btn btn-link btn-block text-left" type="button"
                                        data-toggle="collapse" data-target="#collapseFive" aria-expanded="false"
                                        aria-controls="collapseFive" style="display: flex; align-items: center;">
                                        Mundan
                                        <span class="ml-auto arrow"></span>
                                    </button>
                                </h2>
                            </div>

                            <div id="collapseFive" class="collapse" aria-labelledby="headingFive"
                                data-parent="#accordionExample">
                                <div class="card-body">
                                    <div class="row">
                                        @if ($mundanMuhuratData['status'] == true)
                                            @foreach ($mundanMuhuratData['data'] as $mundan)
                                                @php
                                                    $mundanExplode = explode(' ', $mundan['titleLink']);
                                                @endphp
                                                <div class="col-2 m-1 accordian-body">
                                                    <a href="javascript:0" data-title="Mundan"
                                                        data-image="mundan.jpg"
                                                        data-date="{{ $mundan['titleLink'] }}"
                                                        data-muhurat="{{ $mundan['muhurat'] }}"
                                                        data-nakshatra="{{ $mundan['nakshatra'] }}"
                                                        data-tithi="{{ $mundan['tithi'] }}"
                                                        onclick="muhuratModal(this)">
                                                        <h6 class="mb-1">{{ explode(',', $mundanExplode[1])[0] }}
                                                        </h6>
                                                        <h6 class="mb-1">{{ substr($mundanExplode[0], 0, 3) }}</h6>
                                                    </a>
                                                </div>
                                            @endforeach
                                        @else
                                            <p>No Data Found</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- mundan --}}
                        <div class="card px-2">
                            <div class="card-header" id="headingSix">
                                <h2 class="mb-0 accordian-heading" style="display: flex; align-items: center;">
                                    <img src="{{ asset('public/assets/front-end/img/muhurat/anna-prashan.png') }}"
                                        alt="" style="height: auto; width: 40px; margin-left: 10px;">
                                    <button class="btn btn-link btn-block text-left" type="button"
                                        data-toggle="collapse" data-target="#collapseSix" aria-expanded="false"
                                        aria-controls="collapseSix" style="display: flex; align-items: center;">
                                        Anna Prashan
                                        <span class="ml-auto arrow"></span>
                                    </button>
                                </h2>
                            </div>

                            <div id="collapseSix" class="collapse" aria-labelledby="headingSix"
                                data-parent="#accordionExample">
                                <div class="card-body">
                                    <div class="row">
                                        @if ($annaprashanMuhuratData['status'] == true)
                                            @foreach ($annaprashanMuhuratData['data'] as $annaprashan)
                                                @php
                                                    $annaprashanExplode = explode(' ', $annaprashan['titleLink']);
                                                @endphp
                                                <div class="col-2 m-1 accordian-body">
                                                    <a href="javascript:0" data-title="Anna Prashan"
                                                        data-image="anna-prashan.png"
                                                        data-date="{{ $annaprashan['titleLink'] }}"
                                                        data-muhurat="{{ $annaprashan['muhurat'] }}"
                                                        data-nakshatra="{{ $annaprashan['nakshatra'] }}"
                                                        data-tithi="{{ $annaprashan['tithi'] }}"
                                                        onclick="muhuratModal(this)">
                                                        <h6 class="mb-1">
                                                            {{ explode(',', $annaprashanExplode[1])[0] }}
                                                        </h6>
                                                        <h6 class="mb-1">{{ substr($annaprashanExplode[0], 0, 3) }}
                                                        </h6>
                                                    </a>
                                                </div>
                                            @endforeach
                                        @else
                                            <p>No Data Found</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- naamkaran --}}
                        <div class="card px-2">
                            <div class="card-header" id="headingSeven">
                                <h2 class="mb-0 accordian-heading" style="display: flex; align-items: center;">
                                    <img src="{{ asset('public/assets/front-end/img/muhurat/naamkaran.png') }}"
                                        alt="" style="height: auto; width: 40px; margin-left: 10px;">
                                    <button class="btn btn-link btn-block text-left" type="button"
                                        data-toggle="collapse" data-target="#collapseSeven" aria-expanded="false"
                                        aria-controls="collapseSeven" style="display: flex; align-items: center;">
                                        Naamkaran
                                        <span class="ml-auto arrow"></span>
                                    </button>
                                </h2>
                            </div>

                            <div id="collapseSeven" class="collapse" aria-labelledby="headingSeven"
                                data-parent="#accordionExample">
                                <div class="card-body">
                                    <div class="row">
                                        @if ($naamkaranMuhuratData['status'] == true)
                                            @foreach ($naamkaranMuhuratData['data'] as $naamkaran)
                                                @php
                                                    $naamkaranExplode = explode(' ', $naamkaran['titleLink']);
                                                @endphp
                                                <div class="col-2 m-1 accordian-body">
                                                    <a href="javascript:0" data-title="Naamkaran"
                                                        data-image="naamkaran.jpg"
                                                        data-date="{{ $naamkaran['titleLink'] }}"
                                                        data-muhurat="{{ $naamkaran['muhurat'] }}"
                                                        data-nakshatra="{{ $naamkaran['nakshatra'] }}"
                                                        data-tithi="{{ $naamkaran['tithi'] }}"
                                                        onclick="muhuratModal(this)">
                                                        <h6 class="mb-1">{{ explode(',', $naamkaranExplode[1])[0] }}
                                                        </h6>
                                                        <h6 class="mb-1">{{ substr($naamkaranExplode[0], 0, 3) }}
                                                        </h6>
                                                    </a>
                                                </div>
                                            @endforeach
                                        @else
                                            <p>No Data Found</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- vidyarambh --}}
                        <div class="card px-2">
                            <div class="card-header" id="headingEight">
                                <h2 class="mb-0 accordian-heading" style="display: flex; align-items: center;">
                                    <img src="{{ asset('public/assets/front-end/img/muhurat/vidhyarambh.png') }}"
                                        alt="" style="height: auto; width: 40px; margin-left: 10px;">
                                    <button class="btn btn-link btn-block text-left" type="button"
                                        data-toggle="collapse" data-target="#collapseEight" aria-expanded="false"
                                        aria-controls="collapseEight" style="display: flex; align-items: center;">
                                        Vidhyarambh
                                        <span class="ml-auto arrow"></span>
                                    </button>
                                </h2>
                            </div>

                            <div id="collapseEight" class="collapse" aria-labelledby="headingEight"
                                data-parent="#accordionExample">
                                <div class="card-body">
                                    <div class="row">
                                        @if ($vidyarambhMuhuratData['status'] == true)
                                            @foreach ($vidyarambhMuhuratData['data'] as $vidyarambh)
                                                @php
                                                    $vidyarambhExplode = explode(' ', $vidyarambh['titleLink']);
                                                @endphp
                                                <div class="col-2 m-1 accordian-body">
                                                    <a href="javascript:0" data-title="Vidhyarambh"
                                                        data-image="vidhyarambh.png"
                                                        data-date="{{ $vidyarambh['titleLink'] }}"
                                                        data-muhurat="{{ $vidyarambh['muhurat'] }}"
                                                        data-nakshatra="{{ $vidyarambh['nakshatra'] }}"
                                                        data-tithi="{{ $vidyarambh['tithi'] }}"
                                                        onclick="muhuratModal(this)">
                                                        <h6 class="mb-1">
                                                            {{ explode(',', $vidyarambhExplode[1])[0] }}
                                                        </h6>
                                                        <h6 class="mb-1">{{ substr($vidyarambhExplode[0], 0, 3) }}
                                                        </h6>
                                                    </a>
                                                </div>
                                            @endforeach
                                        @else
                                            <p>No Data Found</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- karnvedha --}}
                        <div class="card px-2">
                            <div class="card-header" id="headingNine">
                                <h2 class="mb-0 accordian-heading" style="display: flex; align-items: center;">
                                    <img src="{{ asset('public/assets/front-end/img/muhurat/karnavedha.png') }}"
                                        alt="" style="height: auto; width: 40px; margin-left: 10px;">
                                    <button class="btn btn-link btn-block text-left" type="button"
                                        data-toggle="collapse" data-target="#collapseNine" aria-expanded="false"
                                        aria-controls="collapseNine" style="display: flex; align-items: center;">
                                        Karnavedha
                                        <span class="ml-auto arrow"></span>
                                    </button>
                                </h2>
                            </div>

                            <div id="collapseNine" class="collapse" aria-labelledby="headingNine"
                                data-parent="#accordionExample">
                                <div class="card-body">
                                    <div class="row">
                                        @if ($karnavedhaMuhuratData['status'] == true)
                                            @foreach ($karnavedhaMuhuratData['data'] as $karnavedha)
                                                @php
                                                    $karnavedhaExplode = explode(' ', $karnavedha['titleLink']);
                                                @endphp
                                                <div class="col-2 m-1 accordian-body">
                                                    <a href="javascript:0" data-title="Karnavedha"
                                                        data-image="karnavedha.png"
                                                        data-date="{{ $karnavedha['titleLink'] }}"
                                                        data-muhurat="{{ $karnavedha['muhurat'] }}"
                                                        data-nakshatra="{{ $karnavedha['nakshatra'] }}"
                                                        data-tithi="{{ $karnavedha['tithi'] }}"
                                                        onclick="muhuratModal(this)">
                                                        <h6 class="mb-1">
                                                            {{ explode(',', $karnavedhaExplode[1])[0] }}
                                                        </h6>
                                                        <h6 class="mb-1">{{ substr($karnavedhaExplode[0], 0, 3) }}
                                                        </h6>
                                                    </a>
                                                </div>
                                            @endforeach
                                        @else
                                            <p>No Data Found</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </tbody>
            </table>
        </div>
    </div>
</div>
