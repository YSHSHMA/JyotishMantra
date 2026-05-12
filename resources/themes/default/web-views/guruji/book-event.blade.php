<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Mahakal • Booking Form</title>
    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/roboto-font.css') }}">
    <link href="{{ theme_asset(path: 'public/assets/front-end/css/poojafilter/bootstrapnew.min.css') }}"
        rel="stylesheet" />
    <link href="{{ theme_asset(path: 'public/assets/front-end/css/poojafilter/puja-single.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/owl.theme.default.min.css') }}">
    <link rel="stylesheet"href="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/css/intlTelInput.css') }}">
    <link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <style>
        .dates-container {
    display: flex;
    flex-wrap: nowrap; /* all items in one row */
    gap: 15px;
    overflow-x: auto; /* horizontal scroll on small screens */
    padding-bottom: 8px;
}

.date-box {
    min-width: 140px;
    background: #fff;
    border: 1px solid #eee;
    padding: 12px 15px;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    text-align: center;
    cursor: pointer;
    transition: 0.3s;
}

.date-box:hover {
    background: #FF6F00;
    color: #fff;
    transform: translateY(-3px);
}

.date-day {
    font-size: 18px;
    font-weight: 700;
}

.date-full {
    font-size: 14px;
    color: #666;
}

.date-time {
    font-size: 13px;
    margin-top: 5px;
    font-weight: 600;
}

        </style>
</head>

<body>
    @php
        $ecommerceLogo = getWebConfig('company_web_logo');
    @endphp
    <header class="container-fluid py-3 bg-white shadow-sm fixed-top">
        <div class="row align-items-center justify-content-between">
        
        <!-- LEFT: Guruji Name -->
        <div class="col-6">
            <div class="guru-title">
                <span class="small-text">Sacred Rituals by</span>
                <h3>{{ $gurujiname->name }}</h3>
            </div>
        </div>

        <!-- RIGHT: Logo -->
        <div class="col-6 d-flex justify-content-end">
            <div class="powered-logo text-end">
                <span class="powered-text">Powered by</span>
                <a href="{{ url('/') }}">
                    <img src="{{ getValidImage('storage/app/public/company/' . $ecommerceLogo, type: 'backend-logo') }}"
                        alt="Mahakal.com" class="site-logo">
                </a>
            </div>
        </div>

    </div>

        <!-- Mobile Steps Dropdown -->
        <div class="collapse bg-light p-2 mt-2 d-md-none" id="mobileSteps">
            <h6 class="text-warning mb-1">{{ translate('Online Booking Form') }}</h6>
            <p class="text-secondary small mb-0">
                <i class="fas fa-box"></i> {{ translate('Package Selection') }}
                <span class="mx-1">→</span>
                <i class="fas fa-user"></i> {{ translate('Details') }}
                <span class="mx-1">→</span>
                <i class="fas fa-check-circle"></i> {{ translate('Confirmation') }}
                <span class="mx-1">→</span>
                <i class="fas fa-credit-card"></i> {{ translate('Payment') }}
            </p>
        </div>

    </header>
    <!-- Main Content (header ke neeche se start hoga) -->
    <main class="" style="margin-top:100px;">
        <!-- Image Slider -->
        @php
            if ($event->product_type == 'pooja') {
                $folder = 'pooja/';
                $bookingdate = $event->booking_date ?? null;
                $eventvenue = $event->pooja_venue ?? null;
            }
            $eventCount = 10000;  
        @endphp
       
            <section class="container mb-2">
                <div class="owl-carousel">
                    @if(!empty($imagePaths))
                        @foreach ($imagePaths as $key => $photo)
                            <div class="item carousel-image {{ $key === 0 ? 'active' : '' }}"
                                id="image{{ $key }}">
                                <img class="w-100 shadow-sm"
                                    src="{{ $photo }}"
                                    alt="Puja Image {{ $key + 1 }}">
                            </div>
                        @endforeach
                    @endif
                </div>
            </section>
            

        <!-- Hero Card -->
        <section class="container mb-3">
            <div class="card-hero">
            <div class="row align-items-center">
                    <!-- Left Side: Details -->
                    <div class="col-md-8 text-start">
                        <div class="flex flex-row mt-2 flex-nowrap leading-normal">
                            <div>
                                <span class="inline-flex">{{ translate('Till_now') }}</span>
                                <span class="font-bold text-dark ml-1">
                                {{ \App\Models\EventOrder::where('event_id', $eventData['id'])->where('transaction_status', 1)->count() }}+
                                    <span class="ml-1 mr-1">{{ translate('Devotees') }}</span>
                                </span>
                                <span style="color:#000;">
                                    {{ translate('Customers') }}.
                                    {{ translate('have successfully booked their religious events on Mahakal.com and benefited from our services') }}..
                                </span>
                                <div class="tray mb-3 ml-3 mt-2">
                                    @php
                                        $uniqueUsers = range(0, 13);
                                        shuffle($uniqueUsers);
                                        $selectedUsers = array_slice($uniqueUsers, 0, 5);
                                    @endphp
                                    @foreach ($selectedUsers as $random_user)
                                        <div class="relative circle-img-container">
                                            <div class="bg-cover bg-center flex-shrink-0 cursor-pointer border border-4 border-white rounded-full circle-img"
                                                style="background-image:url('{{ theme_asset(path: 'public/assets/user_list/user' . $random_user . '.jpg') }}')">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            
                        </div>
                    </div>
                    <!-- Right Side: Countdown -->
                    <div class="col-md-4 text-md-end text-start">
                        <!-- Ratings Display -->
                        <div class="font-10">
                                <h3  class="text-sm mt-2 mb-2 font-medium border-b border-dashed border-primary font-weight-bold">
                                    <i class="fas fa-star"></i>
                                    5/5 (1K +ratings)
                                </h3>
                            </div>
                    </div>
                </div>
                @if (!empty($event) && !empty($event->pooja_heading))
                    <span class="text-12 font-bold  line-clamp-2 text-ellipsis mb-0"
                        style="color:#fe9802;">{{ strtoupper($event->pooja_heading) }}
                    </span>
                @endif
                <h3 class="h3 mt-2 text-dark font-bold" id="displayName">{{ $eventData['event_name'] }}</h3>
                @php
                    $date_upcommining = '';
                    $time_upcommining = '';
                    $endevent_venues = '';
                    $endevent_venues1 = '';
                    $langs = str_replace('_', '-', app()->getLocale()) == 'in' ? 'hi' : str_replace('_', '-', app()->getLocale());

                    if (!empty($eventData['all_venue_data']) && json_decode($eventData['all_venue_data'], true)) {
                        foreach (json_decode($eventData['all_venue_data'], true) as $check) {
                        $currentDateTime = new DateTime();
                        $eventDateTime = DateTime::createFromFormat('d-m-Y h:i A', date('d-m-Y', strtotime($check['date'])) . ' ' . date('h:i A', strtotime($check['start_time'])));
                        $endevent_venues1 = !empty($check[$langs . '_event_venue_full_address'] ?? '') ? $check[$langs . '_event_venue_full_address'] ?? '' : $check[$langs . '_event_venue']; //$check[$langs . '_event_venue'];
                        if ($eventDateTime && $eventDateTime > $currentDateTime) {
                            $endevent_venues = !empty($check[$langs . '_event_venue_full_address'] ?? '') ? $check[$langs . '_event_venue_full_address'] ?? '' : $check[$langs . '_event_venue']; //$check[$langs . '_event_venue'];
                            $date_upcommining = date('d M,Y', strtotime($check['date']));
                            $time_upcommining = date('H:i:s', strtotime($check['start_time']));
                            break;
                        }
                        }
                    } 
                        dd($eventData['all_venue_data']);
                @endphp
                <div class="flex flex-col">
                    <div class="flex items-center space-x-1 pt-2">
                        <div class="d-flex">
                            <img src="{{ theme_asset(path: 'public/assets/front-end/img/track-order/temple.png') }}"
                                alt="Puja Venue" style="width:24px;height:24px;">
                            <p class="pooja-venue" style="color:#000;">{{ $endevent_venues == '' ? $endevent_venues1 : $endevent_venues }}</p>
                        </div>
                    </div>
                </div>
                @if($date_upcommining && \Carbon\Carbon::parse($date_upcommining)->isFuture())
                <div class="mt-2 text-dark"><strong>
                        @if ($eventData['informational_status'] == 0)
                        {{ translate('Event booking will close on') }}
                        @else
                        {{ translate('Event will be start on') }}
                        @endif
                        :
                    </strong>
                </div>
                {{ $date_upcommining }}
                <input type="hidden" name="date" id="fullDate"  value="{{ $date_upcommining }}">
                <input type="hidden" name="dates" id="fullDates" value="{{ $date_upcommining }}">
                <input type="hidden" name="time" id="fullTime"  value="{{ $time_upcommining }}">
                    <div class="countdown d-flex gap-4 justify-content-md-start text-center fw-bold" style="color:#FF6F00;">
                        <div class="time-box">
                            <span class="number days">00</span><br>
                            <small>{{ translate('Days') }}</small>
                        </div>
                        <div class="time-box">
                            <span class="number hours">00</span><br>
                            <small>{{ translate('Hours') }}</small>
                        </div>
                        <div class="time-box">
                            <span class="number minutes">00</span><br>
                            <small>{{ translate('Mins') }}</small>
                        </div>
                        <div class="time-box">
                            <span class="number seconds">00</span><br>
                            <small>{{ translate('Secs') }}</small>
                        </div>
                    </div>
                @endif

                <div class="flex flex-col">
                           <div class="flex flex-wrap justify-center items-center">
                              <div class="mb-4">
                                 <div class="mt-4 font-weight-bold text-dark">
                                    {{ translate('Click on Interested to stay updated about this event') }}.
                                 </div>
                                 <form action="{{ route('event-interested') }}" method="post"
                                    class='event-interested'>
                                    @csrf
                                    <div class="df-ao df-bv df-h">
                                       <div class="df-lf">
                                          <div class="df-h df-lg df-u mt-2">
                                             <div class="text-start">
                                                <a class='h3 ml-2 float-left'>
                                                   <i class="fa fa-thumbs-up text-success"
                                                      aria-hidden="true"></i>
                                                   <small
                                                      style="font-size: 18px;">{{ $eventData['event_interested'] }}</small>
                                                </a>
                                                @if (auth('customer')->check())
                                                @if (\App\Models\EventInterest::where('user_id', auth('customer')->id())->where('event_id', $eventData['id'])->exists())
                                                <button type='button'
                                                   class="btn btn-outline-success"
                                                   data-package_id="{{ $eventData['id'] }}"
                                                   data-venue_id=""
                                                   data-link="{{ route('event-interested') }}"><i
                                                      class="fa fa-check"
                                                      aria-hidden="true"></i>&nbsp;{{ translate('Interested') }}</button>
                                                @else
                                                <button type='button'
                                                   class="auth-book-now btn btn-outline-danger"
                                                   data-package_id="{{ $eventData['id'] }}"
                                                   data-venue_id=""
                                                   data-link="{{ route('event-interested') }}">{{ translate('Interested') }}?</button>
                                                @endif
                                                @else
                                                <button type='button'
                                                   class="participate-btn btn btn-outline-danger"
                                                   data-package_id="{{ $eventData['id'] }}"
                                                   data-venue_id=""
                                                   data-link="{{ route('event-interested') }}">{{ translate('Interested') }}?</button>
                                                @endif
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                 </form>
                                 <div class="small font-weight-bold text-dark">
                                    <p>
                                       <br>
                                       {{ translate('People have shown') }}
                                       <br>
                                       {{ translate('interest recently') }}
                                    </p>
                                 </div>
                              </div>
                           </div>
                        </div>
            </div>
        </section>

        <!-- Packages -->

        <section class="container mb-2">
            <div class="row g-3" id="packageList">

                @if ($eventData['informational_status'] == 0)

                    <h4 class="mt-4 fw-bold text-start text-md-left text-warning">
                        {{ translate('Choose your Preferred Venue') }}
                    </h4>

                    <p class="mb-1 text-muted text-start text-md-left">
                        {{ translate('Select_the_best_puja_package_with_clear_pricing_and_services.') }}
                    </p>

                    @php
                        if (!empty($eventData['all_venue_data']) && ($venues = json_decode($eventData['all_venue_data'], true))) {
                            usort($venues, function ($a, $b) {
                                $aDateTime = strtotime($a['date'] . ' ' . $a['start_time']);
                                $bDateTime = strtotime($b['date'] . ' ' . $b['start_time']);
                                return $aDateTime <=> $bDateTime;
                            });

                            $unique_list      = [];
                            $unique_list2     = [];
                            $unique_datestore = [];
                            $unique_packagestore = [];

                            foreach ($venues as $val) {

                                $venueAddress  = !empty($val[$langs . '_event_venue_full_address'] ?? '') ? $val[$langs . '_event_venue_full_address'] : $val[$langs . '_event_venue'];
                                $venueAddress2 = !empty($val['en_event_venue_full_address'] ?? '') ? $val['en_event_venue_full_address'] : $val['en_event_venue'];

                                $currentDateTime = new DateTime();
                                $eventStartDate  = new DateTime($val['date'] . ' ' . $val['start_time']);
                                $eventEndDate    = new DateTime($val['date'] . ' ' . $val['end_time']);

                                if ($currentDateTime > $eventEndDate) {
                                    continue;
                                }

                                $key = strtolower(str_replace(' ', '-', $venueAddress2));

                                $unique_datestore[$key]['name'] = $venueAddress2;
                                $unique_datestore[$key]['dates'][] = [
                                    "day"  => date("d M", strtotime($val['date'])),
                                    "full" => date("d M, Y", strtotime($val['date'])),
                                    "key"  => $key . "-" . date("d-M-Y", strtotime($val['date'])),
                                    "time" => date("h:i A", strtotime($val['start_time'])) . ' - ' . date("h:i A", strtotime($val['end_time']))
                                ];

                                if (!empty($val['package_list'])) {
                                    foreach ($val['package_list'] as $venu) {

                                        $packKey = $key . "-" . date("d-M-Y", strtotime($val['date']));

                                        $unique_packagestore[$packKey]['name'] = $venueAddress2;
                                        $unique_packagestore[$packKey]['date'] = date("d M, Y", strtotime($val['date']));

                                        $unique_packagestore[$packKey]['data'][] = [
                                            'id'       => $venu['package_name'],
                                            "venue_id" => $val['id'],
                                            "package_name" => (\App\Models\EventPackage::find($venu['package_name'])->package_name ?? ''),
                                            "description"  => (\App\Models\EventPackage::find($venu['package_name'])->description ?? ''),
                                            "available"     => (($venu['available'] <= 0) ? 0 : 1),
                                            "price"         => $venu['price_no'],
                                            "available_count" => $venu['available'],
                                        ];
                                    }
                                }
                                if (!in_array($venueAddress, $unique_list)) {
                                    $unique_list[] = $venueAddress;
                                    $unique_list2[] = $venueAddress2;
                                }
                            }
                        }
                    @endphp

                    @if ($unique_list)
                        @foreach ($unique_list as $kadd => $addes)
                            <div class="venue-card" data-venue="{{ strtolower(str_replace(' ', '-', $unique_list2[$kadd])) }}">
                                <a onclick="showStep('date')">
                                    <h3 class="venue-name">{{ $addes }}</h3>
                                </a>
                            </div>
                        @endforeach
                    @endif
                    {{-- END informational_status --}}
                    <!-- Step 2: Date Selection -->
                    <div class="step" id="step-date">
                        <div class="action-buttons">
                        @if(($unique_list??"") && count($unique_list) > 1)
                        <a class="btn-prev d-block d-sm-none" onclick="showStep('venue')"><i class="fa fa-chevron-left" aria-hidden="true"></i><i class="fa fa-chevron-left" aria-hidden="true"></i></a>
                        <button class="btn btn-prev d-none d-sm-block" onclick="showStep('venue')"><i class="fa fa-chevron-left" aria-hidden="true"></i><i class="fa fa-chevron-left" aria-hidden="true"></i> {{ translate('Change Venue') }}</button>
                        @elseif(($unique_list??"") && count($unique_list) == 1)
                        <span class="font-weight-bolder">{{ $unique_list[0] }}</span>
                        @endif
                        </div>
                        <div class="dates-container">
                        </div>
                    </div>
                    <!-- Step 3: Package Selection -->
                    <div class="step" id="step-package">
                        <div class="action-buttons">
                        @if(($unique_list??"") && count($unique_list) == 1 && ($unique_packagestore??'') && count($unique_packagestore) == 1)
                        <span class="font-weight-bolder">{{ $unique_list[0]??"" }} ( {{($unique_packagestore[(array_keys($unique_packagestore)[0]??"")]['date'] ??"") }} )</span>
                        @else
                        <a class="btn-prev d-block d-sm-none" onclick="showStep('date')"><i class="fa fa-chevron-left" aria-hidden="true"></i><i class="fa fa-chevron-left" aria-hidden="true"></i></a>
                        <button class="btn btn-prev d-none d-sm-block" onclick="showStep('date')"><i class="fa fa-chevron-left" aria-hidden="true"></i><i class="fa fa-chevron-left" aria-hidden="true"></i> {{ translate('Change Date') }}</button>
                        @endif
                        </div>
                        <div class="packages-container">
                        </div>
                    </div>
                @else
                    <?php
                        $unique_datestore = [];
                        $unique_packagestore = []; 
                    ?>
                @endif
            </div>
        </section>

       
    </main>

    <!-- Sticky Footer -->
    {{-- <div class="sticky-bar py-3"
        style="background: url('{{ asset('public/assets/front-end/img/bg-footer.jpg') }}') no-repeat center center/cover;"> --}}
    <div class="sticky-bar py-2">
        <div class="container">
            <div class="row align-items-center">

                <!-- Text -->
                <div class="col-12 col-md-8 text-center text-md-start mb-2 mb-md-0" id="footerInfo">
                    {{ translate('Select Your Puja Package') }}
                </div>

                <!-- Button -->
                <div class="col-12 col-md-4 text-center text-md-end">
                    <button class="btn w-100 w-md-auto px-3" id="btnEditInfo"
                        style="background-color: #FF6F00; border-color: #FF6F00;" disabled>
                        {{ translate('Proceed') }}
                    </button>
                </div>
            </div>
        </div>
    </div>



    <!-- Details Modal -->
    <div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="dateListModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" style="border-top: 2px solid var(--base) !important;/">
                <div class="modal-header">
                    <span class="text-18 font-bold mr-2">
                        {{ translate('Fill_your_details_for_Puja') }}</span>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form id="detailsForm" method="POST" novalidate action="{{ route('guruji.gurujipujaLead', $event->slug) }}">
                    @csrf
                    @php
                        if (auth('customer')->check()) {
                            $customer = App\Models\User::where('id', auth('customer')->id())->first();
                        }
                    @endphp

                    <input type="hidden" name="service_id" value="{{ $forecastServiceId ?? $event->id }}">
                    
                      
                    <input type="hidden" name="type" id="package_type">
                    <input type="hidden" name="package_id" id="package_id">
                    <input type="hidden" name="package_price" id="package_price">
                    <input type="hidden" name="add_product_id" id="add_product_id">
                    <input type="hidden" name="final_amount" id="total_amount">
                    <input type="hidden" name="pandit_id" id="pandit-id">
                    <div class="modal-body">
                        <span class="block text-16 font-bold text-gray-900 text-dark">
                            {{ translate('Enter Your WhatsApp Mobile Number') }}
                        </span>
                        <span class="text-[12px] font-normal text-[#707070]">
                            {{ translate('Your puja booking updates will be sent on the below WhatsApp number') }}
                        </span>

                        <!-- Phone -->
                        <div class="mb-3">
                            <div class="form-group">
                                <label class="form-label font-semibold">
                                    {{ translate('Phone Number') }}
                                    <small class="text-primary">( *
                                        {{ translate('Country code is must like for IND') }} 91 )</small>
                                </label>
                                <input class="form-control phone-input-with-country-picker" type="tel"
                                    value="{{ isset($customer['phone']) ? $customer['phone'] : '' }}"
                                    name="person_phone" id="person-number"
                                    placeholder="{{ translate('Phone Number') }}" inputmode="numeric" required
                                    maxlength="10" minlength="10" {{ isset($customer['phone']) ? 'readonly' : '' }}>

                                <p id="number-validation" class="text-danger d-none">
                                    Enter a valid 10-digit Mobile Number
                                </p>
                            </div>
                        </div>

                        <!-- Name -->
                        <div class="mb-3">
                            <div class="form-group">
                                <label class="form-label font-semibold">{{ translate('Your Name') }}</label>
                                <input class="form-control"
                                    value="{{ !empty($customer['f_name']) ? $customer['f_name'] : '' }}{{ !empty($customer['l_name']) ? $customer['l_name'] : '' }}"
                                    type="text" name="person_name" id="person-name"
                                    placeholder="{{ translate('Ex') }}: {{ translate('Your Name') }}" required
                                    {{ isset($customer['f_name']) ? 'readonly' : '' }}>

                                <p id="name-validation" class="text-danger d-none">
                                    Enter Your Name
                                </p>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-group">
                                <label class="form-label font-semibold">{{ translate('Chosse the Date') }}</label>
                                <input class="form-control text-align-direction" type="text" name="booking_date" id="datepicker" placeholder="{{ translate('Enter_your_puja_praforming_date') }}"
                                            autocomplete="off" required>
                            </div>
                        </div>
                        
                    </div>

                    <div class="modal-footer">
                        <button type="submit" id="bookNowBtn"
                            class="btn btn-primary btn-block btn-shadow mt-1 font-weight-bold w-100">
                            {{ translate('Book Now') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Confirm Modal -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="dateListModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ translate('Confirm Your Details') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @if ($digital_payment['status'] == 1)
                        @foreach ($payment_gateways_list as $payment_gateway)
                            <form method="post" class="digital_payment pooja-pending-form"
                                id="{{ $payment_gateway->key_name }}_form" action="{{ route('GurujipaymentRequest') }}">
                                @csrf

                                <div class="Details">
                                    <input type="hidden" name="payment_method"
                                        value="{{ $payment_gateway->key_name }}">
                                    <input type="hidden" name="payment_platform" value="web">
                                    @if ($payment_gateway->mode == 'live' && isset($payment_gateway->live_values['callback_url']))
                                        <input type="hidden" name="callback"
                                            value="{{ $payment_gateway->live_values['callback_url'] }}">
                                    @elseif($payment_gateway->mode == 'test' && isset($payment_gateway->test_values['callback_url']))
                                        <input type="hidden" name="callback"
                                            value="{{ $payment_gateway->test_values['callback_url'] }}">
                                    @else
                                        <input type="hidden" name="callback" value="">
                                    @endif
                                    <input type="hidden" name="external_redirect_link" value="{{ route('guruji-puja-pending-web-payment') }}">
                                    <label class="d-flex align-items-center gap-2 mb-0 form-check py-2 cursor-pointer">
                                        <input type="radio" id="{{ $payment_gateway->key_name }}"  name="online_payment" class="form-check-input custom-radio"
                                            value="{{ $payment_gateway->key_name }}" hidden>
                                        <img width="30" src="{{ dynamicStorage(path: 'storage/app/public/payment_modules/gateway_image') }}/{{ $payment_gateway->additional_data && json_decode($payment_gateway->additional_data)->gateway_image != null ? json_decode($payment_gateway->additional_data)->gateway_image : '' }}"
                                            alt="" hidden>
                                        <span class="text-capitalize form-check-label" hidden>
                                            @if ($payment_gateway->additional_data && json_decode($payment_gateway->additional_data)->gateway_title != null)
                                                {{ json_decode($payment_gateway->additional_data)->gateway_title }}
                                            @else
                                                {{ str_replace('_', ' ', $payment_gateway->key_name) }}
                                            @endif
                                        </span>
                                    </label>
                                    <input type="hidden" name="order_id" id="pending-order-id" class="orderId" value="">
                                    <input type="hidden" name="leads_id" id="pending-lead-id" class="orderId" value="">
                                </div>
                            </form>
                        @endforeach
                    @endif
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="text-center mb-3"><i class="fas fa-receipt"></i>
                                {{ translate('Booking Receipt') }}</h5>
                            <div class="row">
                                <div class="mb-3 d-flex flex-column gap-1">
                                    <div class="d-flex justify-content-between">
                                        <p class="mb-0"><span id="cService">—</span></p>
                                        <p class="mb-0"><strong>{{ translate('Order ID') }}:</strong>
                                        <span id="cOrderId">—</span></p>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <p class="mb-0"><span id="cVenue">—</span></p>
                                        <p class="mb-0"><strong>{{ translate('Booking Date') }}:</strong>
                                        <span id="cDate">—</span>
                                        </p>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <p class="mb-0"><span id="cPackage">—</span></p>
                                        <p class="mb-0"><span id="cPackagePrice">—</span></p>
                                        <br>
                                        <p class="mb-0"><span id="cGurujiName">—</span></p>
                                    </div>
                                </div>
                                <hr>
                                <h6 class="mb-2"><i class="fas fa-user"></i> {{ translate('Customer Details') }}</h6>
                                <div class="d-flex justify-content-between">
                                    <p class="mb-0"><strong>{{ translate('Name') }}:</strong> 
                                        <span id="cName">—</span></p>
                                    <p class="mb-0"><strong>{{ translate('Mobile') }}:</strong> 
                                        <span id="cMobile">—</span></p>
                                </div>
                            </div>
                            <hr>

                            <h6 class="mb-2"><i class="fas fa-shopping-cart"></i>{{ translate(' Your Devotion List') }}</h6>
                            <table class="table table-sm table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ translate('Item') }}</th>
                                        <th style="width:70px;">{{ translate('Qty') }}</th>
                                        <th style="width:90px;">{{ translate('Price') }}</th>
                                    </tr>
                                </thead>
                                <tbody id="cProducts"></tbody>
                            </table>

                            <!-- Amount -->
                            <div class="text-end mt-2">
                                <h6><strong>{{ translate('Total Amount') }}:</strong><span id="cAmount">—</span>
                                </h6>
                            </div>

                            <hr>

                            <!-- Footer Message -->
                            <p class="text-center text-danger fw-bold">
                                {{ translate('Note Confirmed & Payment Pending') }} </p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">{{ translate('Cancel') }}</button>
                        <button class="btn btn-primary" type="button"
                            id="finalSubmit">{{ translate('Confirm & Submit') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-top: 2px solid var(--base) !important;/">
                <div class="display-6 mb-3">✅</div>
                <h5>पेमेंट सफल!</h5>
                <p>आपकी बुकिंग कन्फर्म हो गई है।</p>
                <button class="btn btn-warning" data-bs-dismiss="modal">ठीक है</button>
            </div>
        </div>
    </div>

    <!-- JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/owl.carousel.min.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/js/intlTelInput.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/country-picker-init.js') }}"></script>
    <script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>

    <script>
        $(document).ready(function() {
        startCountdown();

            function startCountdown() {
                var dateGet = $('#fullDate').val();
                var timeGet = $('#fullTime').val();

                var dateTimeString = dateGet + ' ' + timeGet;
                var newDate = new Date(dateTimeString).getTime();

                if (isNaN(newDate)) {
                    console.error("Invalid date or time format!");
                    return;
                }

                const countdown = setInterval(() => {
                    const now = new Date().getTime();
                    const diff = newDate - now;

                    if (diff <= 0) {
                    clearInterval(countdown);
                    $(".countdown_message").text("Event Starts Live");
                    $(".countdown").addClass("d-none");
                    return;
                    }

                    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((diff % (1000 * 60)) / 1000);

                    $(".seconds").text(seconds < 10 ? '0' + seconds : seconds);
                    $(".minutes").text(minutes < 10 ? '0' + minutes : minutes);
                    $(".hours").text(hours < 10 ? '0' + hours : hours);
                    $(".days").text(days < 10 ? '0' + days : days);
                }, 1000);
            }
        });
        $(document).ready(function() {
            var today = new Date(new Date().getFullYear(), new Date().getMonth(), new Date().getDate());
            $('#datepicker').datepicker({
                uiLibrary: 'bootstrap4',
                format: 'yyyy-mm-dd',
                modal: true,
                footer: true,
                minDate: today
            });

            $("#productCarousel").owlCarousel({
                loop: false,
                autoplay: false, // autoplay off
                margin: 10,
                nav: false,
                responsive: {
                    0: {
                        items: 2
                    }, // Mobile pe 2 cards
                    576: {
                        items: 2
                    }, // Small tablets pe 2
                    768: {
                        items: 3
                    }, // Medium screens pe 3
                    992: {
                        items: 4
                    }, // Large screen pe 4
                    1200: {
                        items: 5
                    }
                }
            });

            // General Carousel (banners/testimonials etc.)
            $(".owl-carousel").owlCarousel({
                loop: true,
                autoplay: true,
                autoplayTimeout: 3000,
                dots: true,
                nav: false,
                margin: 15,
                responsive: {
                    0: {
                        items: 1 // mobile
                    },
                    600: {
                        items: 1 // tablet portrait
                    },
                    992: {
                        items: 1 // tablet landscape / small laptop
                    },
                    1200: {
                        items: 2 // large desktop
                    }
                }
            });
        });

        

</script>
<script>
   const venueData = @json($unique_datestore);

   const packageData = @json($unique_packagestore);
   let userSelection = {
      venue: null,
      date: null,
      package: null
   };

   // Initialize the page
   document.addEventListener('DOMContentLoaded', function() {
      const venueCards = document.querySelectorAll('.venue-card');
      venueCards.forEach(card => {
         card.addEventListener('click', function() {
            venueCards.forEach(c => c.classList.remove('selected'));
            this.classList.add('selected');
            userSelection.venue = this.getAttribute('data-venue');
            updateDateOptions();
         });
         <?php
         if (($unique_list ?? "")) {
            if (count($unique_list ?? []) == 1) { ?>
               showStep('date');
         <?php }
         } ?>
      });

      const packageCards = document.querySelectorAll('.package-card');
      packageCards.forEach(card => {
         card.addEventListener('click', function() {
            packageCards.forEach(c => c.classList.remove('selected'));
            this.classList.add('selected');
            userSelection.package = this.getAttribute('data-package');
         });
      });
      document.querySelector('.venue-card').click();
      document.querySelector('.package-card').click();
   });

   // Update date options based on selected venue
   function updateDateOptions() {
      const datesContainer = document.querySelector('.dates-container');
      datesContainer.innerHTML = '';

      if (userSelection.venue && venueData[userSelection.venue]) {
         const dates = venueData[userSelection.venue].dates;

         dates.forEach(date => {
            const dateCard = document.createElement('div');
            dateCard.className = 'date-card';
            dateCard.setAttribute('data-date', date.full);

            dateCard.innerHTML = `
                <div class="date-box">
                    <div class="date-day">${date.day}</div>
                    <div class="date-full">${date.full}</div>
                    <div class="date-time">${date.time}</div>
                </div>
            `;


            dateCard.addEventListener('click', function() {
               document.querySelectorAll('.date-card').forEach(c => c.classList.remove('selected'));
               this.classList.add('selected');
               userSelection.date = {
                  key: date.key,
                  full: date.full,
                  time: date.time
               };
               updatePackageOptions();
               showStep('package');
            });

            datesContainer.appendChild(dateCard);
         });

         if (dates.length > 0) {
            <?php if($unique_packagestore && count(array_keys($unique_packagestore)??[]) == 1){ ?>
            datesContainer.querySelector('.date-card').click();
            <?php } ?>
         }
      }
   }

   // Show the specified step
   function showStep(step) {
      document.querySelectorAll('.step').forEach(s => s.classList.remove('active'));
      document.getElementById(`step-${step}`).classList.add('active');
      if (step === 'summary') {
         updateSummary();
      }
   }

   function updateSummary() {
      if (userSelection.venue && venueData[userSelection.venue]) {
         document.getElementById('summary-venue').textContent = venueData[userSelection.venue].name;
      }

      if (userSelection.date) {
         document.getElementById('summary-date').textContent =
            `${userSelection.date.full} (${userSelection.date.time})`;
      }

      if (userSelection.package && packageData[userSelection.package]) {
         document.getElementById('summary-package').textContent = packageData[userSelection.package].name;
         document.getElementById('summary-cost').textContent = `$${packageData[userSelection.package].price}`;
      }
   }

   function resetBooking() {
      userSelection = {
         venue: null,
         date: null,
         package: null
      };

      // Reset UI selections
      document.querySelectorAll('.venue-card, .date-card, .package-card').forEach(card => {
         card.classList.remove('selected');
      });

      showStep('venue');

      document.querySelector('.venue-card').click();
      document.querySelector('.package-card').click();
   }

   function updatePackageOptions() {
      const packagesContainer = document.querySelector('.packages-container');
      packagesContainer.innerHTML = '';
      if (userSelection.date && userSelection.venue) {
         const packageKey = userSelection.date.key;
         console.log(packageData);

         if (packageData[packageKey] && packageData[packageKey].data) {
            const packages = packageData[packageKey].data;

            packages.forEach(pkg => {
               if (!pkg.available) return;

               const packageCard = document.createElement('div');
               packageCard.className = `package-card package-${pkg.package_name.toLowerCase().replace(' ', '-')}`;
               packageCard.setAttribute('data-package-id', pkg.id);

               // Determine badge based on package type
               let badge = '';


               const features = pkg.description ?
                  pkg.description.split(',').map(feature =>
                     `${feature.trim()}`
                  ).join('') :
                  '<li><i class="fas fa-check"></i> Basic venue access</li>';

               let buttonHtml = '';
               if (pkg.available_count <= 0) {
                  buttonHtml = `
            <button type="button" class="btn btn--primary btn-block btn-shadow mt-4 font-weight-bold" disabled>
                Sold Out
            </button>
        `;
               } else {
                  const isAuthenticated = "{{ auth('customer')->check() ? 'true' : 'false' }}";

                  if (isAuthenticated == 'true') {
                     buttonHtml = `
                <a href="javascript:void(0);"
                   data-package_id="${pkg.id}"
                   data-venue_id="${pkg.venue_id}"
                   onclick="authbooknow(this)"
                   class="auth-book-now btn btn--primary btn-block btn-shadow mt-2 font-weight-bold"
                   data-link="{{ route('events-leads', [$eventData['id']]) }}">
                   {{ translate('Book Now') }}
                </a>
            `;
                  } else {
                     buttonHtml = `
                <a href="javascript:void(0);"
                   data-package_id="${pkg.id}"
                   data-venue_id="${pkg.venue_id}"
                   onclick="participatebook(this)"
                   class="participate-btn btn btn--primary btn-block btn-shadow mt-4 font-weight-bold"
                   data-link="{{ route('events-leads', [$eventData['id']]) }}">
                   {{ translate('Book Now') }}
                </a>
            `;
                  }
               }

               packageCard.innerHTML = `
                    ${badge}
                    <h3 class="package-name">${pkg.package_name}</h3>
                    <div class="package-price">${Number(pkg.price).toLocaleString("en-US", { style: "currency", currency: "{{getCurrencyCode()}}"} )}</div>
                    ${pkg.available_count > 0 ? 
                        `<div class="package-availability"><marquee style=" font-size: 14px;color:#aa1405;" class="font-weight-bolder">Only ${pkg.available_count} tickets remaining.<small> {{ translate('Book now before they’re gone')}}!</small></marquee></div>` : 
                        ''}
                    <ul class="package-features">
                        ${features}
                    </ul>
                    <div class="package-button">
            ${buttonHtml}
        </div>
                `;

               packageCard.addEventListener('click', function() {
                  document.querySelectorAll('.package-card').forEach(c => c.classList.remove('selected'));
                  this.classList.add('selected');
                  userSelection.package = {
                     id: pkg.package_name,
                     name: pkg.package_name,
                     price: pkg.price,
                     available_count: pkg.available_count
                  };
               });
               packagesContainer.appendChild(packageCard);
            });

            // Auto-select first available package
            if (packagesContainer.children.length > 0) {
               packagesContainer.querySelector('.package-card').click();
            } else {
               packagesContainer.innerHTML = `
                    <div class="no-packages">
                        <i class="fas fa-calendar-times"></i>
                        <h3>No Packages Available</h3>
                        <p>There are no available packages for the selected date.</p>
                    </div>
                `;
            }
         } else {
            packagesContainer.innerHTML = `
                <div class="no-packages">
                    <i class="fas fa-exclamation-triangle"></i>
                    <h3>No Package Data</h3>
                    <p>No package information available for the selected date.</p>
                </div>
            `;
         }
      }
   }

   const participateBtn = packageCard.querySelector('.participate-btn, .auth-book-now');
   if (participateBtn) {
      participateBtn.addEventListener('click', function(e) {
         e.stopPropagation(); // Prevent card selection when clicking button

         // Get the selected package data
         const selectedPackage = userSelection.package;
         if (!selectedPackage) {
            alert('Please select this package first by clicking on the card');
            return;
         }
         handleBooking(selectedPackage, userSelection.venue, userSelection.date);
      });
   }

   function participatebook(that) {
      $('#participateModal').modal('show');
      $(".package_id_model").val($(that).data('package_id'));
      $(".venue_id_model").val($(that).data('venue_id'));
      $("#lead-store-form").attr('action', $(that).data('link'));
   }

   function authbooknow(that) {
      $("#lead-store-form").attr('action', $(that).data('link'));
      $(".package_id_model").val($(that).data('package_id'));
      $(".venue_id_model").val($(that).data('venue_id'));
      $('#lead-store-form').submit();
   }
</script>
    {{-- mobile no blur --}}
    <script>
        $('#person-number').blur(function(e) {
            e.preventDefault();
            var code = $('.iti__selected-dial-code').text();
            var mobile = $(this).val();
            var no = code + '' + mobile;

            $.ajax({
                type: "get",
                url: "{{ url('account-service-order-user-name') }}" + "/" + no,
                success: function(response) {
                    if (response.status == 200) {
                        var name = response.user.f_name + ' ' + response.user.l_name;
                        $('#person-name').val(name);
                    } else {
                        $('#send-otp-btn').addClass('d-none');
                        $('#withoutOTP').removeClass('d-none');
                    }
                }
            });
        });
    </script>

</body>

</html>