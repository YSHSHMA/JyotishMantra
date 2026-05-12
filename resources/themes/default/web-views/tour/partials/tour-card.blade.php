     <style>
         #filters li span {
             padding: 5px 6px;
         }

         .ribbon {
             width: 150px;
             height: 150px;
             overflow: hidden;
             position: absolute;
             top: 0px;
             left: 0px
                 /*-26px*/
             ;

         }

         .ribbon span {
             position: absolute;
             display: block;
             width: 160px;
             padding: 2px 0;
             color: #fff;
             font-weight: bold;
             text-align: center;
             font-size: 14px;
             transform: rotate(-45deg);
             top: 20px
                 /*30px*/
             ;
             left: -40px;
             box-shadow: 0 3px 10px rgba(0, 0, 0, 0.3);
             background: linear-gradient(45deg, #6c757d, #495057);
             z-index: 1000;
         }

         #no-tour-message {
             font-size: 18px;
             color: #ff0000;
             font-weight: bold;
         }

         .puja-image {
             aspect-ratio: 14 / 9;
             width: 100%;
             height: auto;
             object-fit: cover;
             background-color: #f5f5f5;
             border-radius: 0.5rem;
             transition: transform 0.3s ease, box-shadow 0.3s ease;
         }

         .puja-image:hover {
             transform: scale(1.02);
             box-shadow: 0 6px 14px rgba(0, 0, 0, 0.12);
         }

         .tour-card {
             display: flex;
             flex-direction: column;
             background: white;
             border-radius: 0.75rem;
             overflow: hidden;
             box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
             transition: box-shadow 0.3s ease;
         }

         .tour-card:hover {
             box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
         }

         .tour-title-css {
             font-size: 1rem;
             font-weight: 600;
             padding: 0.75rem 1rem;
             /* border-bottom: 1px solid #f0f0f0; */
             white-space: nowrap;
             overflow: hidden;
             text-overflow: ellipsis;
         }

         .tour-details {
             justify-content: space-between;
             align-items: flex-start;
             padding: 0px 1rem 0.75rem 1rem;
             gap: 1rem;
         }

         .amenity-item img {
             width: 30px;
             height: 30px;
             margin-right: 0.5rem;
         }

         /* Price section */

         .tour-price .current {
             font-size: 1.2rem;
             font-weight: bold;
             color: #16a34a;
         }

         .tour-price .discount {
             font-size: 0.75rem;
             font-weight: 600;
             color: #dc2626;
         }

         .tour-price .old {
             font-size: 0.75rem;
             color: #9ca3af;
             text-decoration: line-through;
         }

         /* Extra info */
         .tour-info {
             padding: 0.45rem 1rem;
             font-size: 0.8rem;
             color: #4b5563;
             /* height: 72px;
             overflow: auto; */
         }

         /* Toggle button */
         .view-toggle {
             display: none;
             /* hidden by default */
             text-align: center;
             font-size: 0.75rem;
             font-weight: 600;
             color: #FF7722;
             margin: 0.5rem 0;
             cursor: pointer;
             position: relative;
         }

         .view-toggle::after {
             content: ' ▼';
             font-size: 0.7rem;
         }

         .view-toggle.active::after {
             content: ' ▲';
         }

         /* Footer button */
         .tour-footer {
             padding: 0.75rem 1rem;
         }

         .tour-footer a {
             display: block;
             width: 100%;
             text-align: center;
             background: #FF7722;
             color: white;
             font-size: 0.85rem;
             font-weight: 600;
             padding: 0.6rem 0;
             border-radius: 0.5rem;
             transition: background 0.3s ease;
         }

         .tour-footer a:hover {
             background: #e8691d;
         }
     </style>
     @if (!empty($getDataAll) && count($getDataAll) > 0)
     <!-- <div class="row grid sm:grid-cols-2 lg:grid-cols-4 gap-5"> -->
     <div class="{{ ((($font_page??0) == 1) ? 'row grid sm:grid-cols-2 lg:grid-cols-4 gap-5 ': 'row grids') }} ">
         @foreach ($getDataAll as $use)
         @php
         $extraText = '';
         $min_indexes = [];
         if(in_array($use['use_date'], [1,2,4])) {
         $extraText .= "<div><strong>".translate('pickup_From').":</strong> ".($use['pickup_location'] ?? '')."</div>";
         }

         if(in_array($use['use_date'], [1])) {
         $dateRange = explode(' - ', $use['startandend_date']);
         $startDate = isset($dateRange[0]) ? $dateRange[0] : '';
         $endDate = isset($dateRange[1]) ? $dateRange[1] : '';

         if ($startDate && $endDate) {
         $start = new DateTime($startDate);
         $end = new DateTime($endDate);
         $difference = $start->diff($end)->days;

         $dateDisplay = "";
         if (isset($use['customized_type']) && isset($use['customized_dates'])) {
         $customizedType = $use['customized_type'];
         $customizedDates = json_decode($use['customized_dates']??"[]",true);

         switch ($customizedType) {
         case 1:
         $today = new DateTime();
         $nextDates = [];
         foreach ($customizedDates as $day) {
         $next = new DateTime("next " . $day);         
         $nextDates[] = $next;
         }
         usort($nextDates, function ($a, $b) {
         return $a <=> $b;
             });
             $nextDay = $nextDates[0]->format("d/m/Y");
             $nextendDay = $nextDates[0]->modify("+".$difference." days")->format("d/m/Y");

             $extraText .= "<div><strong>".translate('Tour_Date').":</strong> ".$nextDay." - ".$nextendDay."</div>";
             break;

             case 2:
             $dayNumbers = array_map(function($date) {
             return (int)date('d', strtotime($date));
             }, $customizedDates);
             $today = new DateTime();
             $nextDates = [];
             foreach ($dayNumbers as $dayNumber) {
             $next = new DateTime($today->format('Y-m-' . sprintf('%02d', $dayNumber)));
             if ($next < $today) {
                 $next->modify('+1 month');
                 }
                 $nextDates[] = $next;
                 }
                 usort($nextDates, function ($a, $b) {
                 return $a <=> $b;
                     });

                     $nextDay = $nextDates[0]->format("d/m/Y");
                     $nextendDay = $nextDates[0]->modify("+".$difference." days")->format("d/m/Y");

                     $extraText .= "<div><strong>".translate('Tour_Date').":</strong> ".$nextDay." - ".$nextendDay."</div>";
                     break;

                     case 3:
                     $today = new DateTime();
                     $nextDates = [];
                     foreach ($customizedDates as $dateStr) {
                     $date = DateTime::createFromFormat('Y-m-d', $dateStr);
                     $monthDay = $date->format('m-d');
                     $currentYearDate = DateTime::createFromFormat('Y-m-d', $today->format('Y') . '-' . $monthDay);
                     if ($currentYearDate < $today) {
                         $currentYearDate->modify('+1 year');
                         }
                         $nextDates[] = $currentYearDate;
                         }
                         usort($nextDates, function ($a, $b) {
                         return $a <=> $b;
                             });
                             $nextDay = $nextDates[0]->format("d/m/Y");                             
                             $formattedEndDay = $nextDates[0]->modify("+".$difference." days")->format("d/m/Y");;

                             $extraText .= "<div><strong>".translate('Tour_Date').":</strong> ".$nextDay." - ".$formattedEndDay."</div>";
                             break;
                             }
                             }
                             }
                             }

                             // Days / Nights Label
                             if ($use['number_of_day'] == 0.5) {
                             $numb_days_en = translate('Half Day');
                             } elseif ($use['number_of_day'] > 0 && ($use['number_of_night'] ?? 0) <= 0) {
                                 $numb_days_en=($use['number_of_day'] ?? '' ) . "D" ;
                                 } else {
                                 $numb_days_en=($use['number_of_day'] ?? '' ) . "D/" . ($use['number_of_night'] ?? '' ) . "N" ;
                                 }
                                 $getStates=\App\Models\TourVisits::select('cities_name', 'state_name' )->where('id', $use['id'])->first();

                                 // Price
                                 $price_minst = 0;

                                 if (!empty($use['cab_list_price'])) {
                                 $decodedPrices = json_decode($use['cab_list_price'], true);
                                 if (is_array($decodedPrices)) {
                                 $prices = array_column($decodedPrices, 'price');
                                 $price_minst = !empty($prices) ? min($prices) : 0;
                                 if (!empty($prices)) {
                                 $price_minst = min($prices);
                                 $min_indexes = array_keys($prices, $price_minst);
                                 }
                                 }
                                 }
                                 $include_package_amount = 0;
                                 $package_tourone = [];
                                 if (!empty($use['package_list_price']) && json_decode($use['package_list_price'], true) && ($use['is_person_use'] == 0 && in_array($use['use_date'], [1, 2, 3, 4]))) {
                                 foreach (json_decode($use['package_list_price'], true) as $plis) {
                                 $tourPackages = \App\Models\TourPackage::find($plis['package_id']);
                                 if ($tourPackages && !isset($package_tourone[$tourPackages['type']])) {
                                 $package_tourone[$tourPackages['type']] = theme_asset('public/assets/front-end/img/' . $tourPackages['type'] . '.png');
                                 }
                                 $include_package_amount += $plis['pprice'] ?? 0;
                                 }
                                 }
                                 $includePackages = json_decode($use['is_included_package'], true)
                                 @endphp

                                 <div class="{{ ((($font_page??0) == 1) ? 'col-md-3 portfolioTour portfolio ':'col-md-3 col-12 mb-2')}} all_tours {{ $use['tour_type'] }}  {{ $getStates['cities_name'] }}  {{ Illuminate\Support\Str::Slug($getStates['cities_name'], '_') }} {{ Illuminate\Support\Str::Slug($getStates['state_name'], '_') }}  {{ \Illuminate\Support\Str::Slug($getStates['state_name'], '_') }}_{{ $use['tour_type'] }}  {{ \Illuminate\Support\Str::Slug($getStates['state_name'], '_') }}_all_tours" data-cat="{{ $use['tour_type'] }}">
                                     <!-- Image -->
                                     <div class="tour-card">
                                         <div class="relative flex items-center justify-center min-h-[11rem] text-center" style="min-height: 10.65rem; background: lavenderblush;">
                                             @php
                                             $plans = [
                                             0 => ['name' => 'Basic', 'style' => 'linear-gradient(45deg, #6c757d, #495057)'],
                                             1 => ['name' => 'Standard', 'style' => 'linear-gradient(45deg, #28a745, #218838)'],
                                             2 => ['name' => 'Premium', 'style' => 'linear-gradient(45deg, #007bff, #0056b3)'],
                                             3 => ['name' => 'Golden', 'style' => 'linear-gradient(45deg, #FFD700, #FFA500)'],
                                             4 => ['name' => 'Luxury', 'style' => 'linear-gradient(45deg, #dc3545, #b02a37)'],
                                             5 => ['name' => 'Only Cab', 'style' => 'linear-gradient(45deg,#dc3545, #4b0ec3)'],
                                             ];

                                             $selectedPlan = $use['plan_type'] ?? 0;
                                             @endphp
                                             <div class="ribbon">
                                                 <span style="background: {{ $plans[$selectedPlan]['style'] }};z-index:1000">
                                                     {{ translate($plans[$selectedPlan]['name']) }}
                                                 </span>
                                             </div>
                                             <a href="{{ route('tour.tourvisit', [$use['slug'] ?? '']) }}" style="{{ ((($font_page??0) == 1) ? 'display: inline':'')}}">
                                                 <img src="{{ getValidImage('storage/app/public/tour_and_travels/tour_visit/' . $use['tour_image'], 'product') }}" alt="{{ $use['tour_name'] }}" class="puja-image">
                                             </a>
                                             @if (!empty($use['number_of_day']))
                                             <span class="p-1 pl-2 pr-2 font-bold fs-13 d-flex" style="{{ ((($font_page2??0) == 1) ? 'top:8.6rem':'bottom: 6px')}}; inset-inline-start: 9px;position: absolute; background: #FF7722;color:white;z-index: 3;border-radius: 4px !important; white-space: nowrap;opacity: 0.8;">
                                                 @if (($use['is_person_use']??0) == 1)
                                                 <i class="fa fa-user-group"></i>&nbsp;{{ translate('Person wise')}}
                                                 @else
                                                 <i class="fa fa-car"></i>&nbsp;{{ translate('Cab wise')}}
                                                 @endif
                                             </span>
                                             </span>
                                             @endif
                                             @if (!empty($use['number_of_day']))
                                             <span class="p-1 pl-2 pr-2 font-bold fs-13 d-flex" style="{{ ((($font_page2??0) == 1) ? 'top:8.6rem':'bottom: 6px')}};inset-inline-end: 9px;position: absolute; background: #FF7722;color:white;z-index: 3;border-radius: 4px !important; white-space: nowrap;">
                                                 <span class="direction-ltr blink d-block">
                                                     {{ $numb_days_en }}
                                                 </span>
                                             </span>
                                             @endif
                                         </div>

                                         <!-- Title -->
                                         <div class="d-flex gap-2 justify-content-center pooja-heading underborder p-2">
                                             @if($use['is_person_use'] == 0 || ($use['is_person_use'] == 1 && ($includePackages['sightseen']??0) == 1)) <div class="amenity-item"><img src="{{ theme_asset('public/assets/front-end/img/sightseeing.png') }}"></div>@endif
                                             @if($use['is_person_use'] == 0 || ($use['is_person_use'] == 1 && ($includePackages['cab']??0) == 1)) <div class="amenity-item"><img src="{{ theme_asset('public/assets/front-end/img/car.png') }}"></div>@endif
                                             @if($use['is_person_use'] == 0 || ($use['is_person_use'] == 1 && ($includePackages['food']??0) == 1)) <div class="amenity-item"><img src="{{ theme_asset('public/assets/front-end/img/foods.png') }}"></div>@endif
                                             @if($use['is_person_use'] == 0 || ($use['is_person_use'] == 1 && ($includePackages['hotel']??0) == 1)) <div class="amenity-item"><img src="{{ theme_asset('public/assets/front-end/img/hotel.png') }}"></div>@endif
                                         </div>
                                         <span class="card-title tour-title d-none"><?php echo $getStates['cities_name'] ?? "" ?></span>
                                         <span class="card-title tour-title d-none"><?php echo $getStates['state_name'] ?? "" ?></span>
                                         <span class="tour-cab-person d-none"><?php echo ((($use['is_person_use'] ?? 0) == 1) ? 'per_person' : 'cabs') ?></span>

                                         <h3 class="card-title tour-title-css tour-title m-0">{{ $use['tour_name'] }}</h3>
                                         <!-- Amenities + Price -->
                                         <div class="tour-details">
                                             <div class="d-flex align-items-center">
                                                 <span class="small font-weight-bolder">{{ translate('Starting_from') }} </span>
                                             </div>
                                             <div class="tour-price">
                                                 <?php $total_package_price = (($price_minst ?? 0) + ($include_package_amount ?? 0)); ?>
                                                 <div><span class="current">{{ setCurrencySymbol(usdToDefaultCurrency($total_package_price), getCurrencyCode()) }}</span> <span class="old">{{ setCurrencySymbol(($total_package_price / (1 - (($use['percentage_off'] ?? 0) / 100))), getCurrencyCode()) }}</span> (<span class="discount">{{$use['percentage_off']??0}}% OFF</span>)</div>
                                                 <div class="d-flex align-items-center">
                                                     {!! \App\Utils\displayStarRating(($use->review_avg_star ?? 5)) !!}
                                                     <span class="ml-2">({{ number_format(($use->review_avg_star ?? 5), 1) }})</span>
                                                 </div>
                                             </div>
                                         </div>

                                         <!-- Toggle -->
                                         @if(trim($extraText) != '')
                                         <div class="tour-info extra-info d-none">{!! $extraText !!}</div>
                                         <div class="tour-footer d-flex space-x-2 mt-0">
                                             <a href="{{ route('tour.tourvisit', [$use['slug'] ?? '']) }}"
                                                 class="flex-1 bg-[#FF7722] text-white text-center py-2 rounded-md font-bold">
                                                 {{ translate('book_Now') }}
                                             </a>
                                             <button class="view-toggle-btn w-10 h-10 flex items-center justify-center bg-gray-100 rounded-md border border-gray-300"
                                                 onclick="toggleView(this)" title="View More">
                                                 <i class="fa fa-eye"></i>
                                             </button>
                                         </div>
                                         @else
                                         <!-- Book now -->
                                         <div class="tour-footer">
                                             <a href="{{ route('tour.tourvisit', [$use['slug'] ?? '']) }}">{{ translate('book_Now') }}</a>
                                         </div>
                                         @endif
                                     </div>
                                 </div>
                                 @endforeach
     </div>
     @else
     <div id="no-tour-message" class="col-12 text-center my-3 text-danger">No Tours Available</div>
     @endif
     <script>
         function toggleView(button) {
             const card = button.closest('.tour-card');
             const extraInfo = card.querySelector('.extra-info');

             if (extraInfo.classList.contains('d-none')) {
                 extraInfo.classList.remove('d-none');
                 button.title = "View Less";
                 button.innerHTML = "<i class='fa fa-eye-slash'></i>";
             } else {
                 extraInfo.classList.add('d-none');
                 button.title = "View More";
                 button.innerHTML = "<i class='fa fa-eye'></i>";
             }
         }
     </script>