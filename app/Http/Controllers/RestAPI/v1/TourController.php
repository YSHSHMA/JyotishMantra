<?php

namespace App\Http\Controllers\RestAPI\v1;

use App\Contracts\Repositories\TourVisitPlaceRepositoryInterface;
use App\Contracts\Repositories\TourVisitRepositoryInterface;
use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Seller;
use App\Models\TourAndTravel;
use App\Models\TourCab;
use App\Models\TourCabManage;
use App\Models\TourDriverManage;
use App\Models\TourLeads;
use App\Models\TourOrder;
use App\Models\TourReviews;
use App\Models\TourType;
use App\Models\TourVisits;
use App\Models\User;
use App\Traits\FileManagerTrait;
use App\Utils\ImageManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Utils\Helpers;
use App\Services\TourVisitService;
use DateTime;
use PhpParser\Node\Expr\Cast\Double;
use SimplePie\Cache\Redis;
use Illuminate\Support\Facades\DB;

class TourController extends Controller
{
    use FileManagerTrait;
    public function __construct(
        private readonly TranslationRepositoryInterface     $translationRepo,
        private readonly TourVisitRepositoryInterface  $tourtraveller,
        private readonly TourVisitPlaceRepositoryInterface  $tourvisitplac,
    ) {}

    public function dashboard(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "cab_assign" => ['required', function ($attribute, $value, $fail) {
                if (!TourAndTravel::where('id', $value)->where('is_approve', 1)->where('status', 1)->exists()) {
                    $fail('The selected traveller Id is invalid or inactive.');
                }
            },]
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'recode' => 0, 'data' => [], 'errors' => Helpers::error_processor($validator)], 200);
        }
        $arrayList = [];
        $OrderInfo = \App\Models\TourOrder::whereIn('status', [1, 0])->where('refund_status', 0)->where('amount_status', 1)->with(['accept'])->get();
        $arrayList['order'] = [
            // "all_order" => \App\Models\TourOrder::whereIn('status', [1, 0])->where('refund_status', 0)->where('amount_status', 1)->with(['accept'])->whereHas('accept', function ($subQuery) {
            //     $subQuery->where('status', 1);
            // })->count(),
            'pending_order' => \App\Models\TourOrder::whereIn('status', [1, 0])->where(['refund_status' => 0, 'pickup_status' => 0, 'amount_status' => 1, 'drop_status' => 0, 'cab_assign' => 0])
                ->where('pickup_date', '>', \Carbon\Carbon::today()->toDateString())
                ->whereHas('accept', function ($query) use ($request) {
                    $query->where('tour_order_accept.status', 1)->where('traveller_id', $request['cab_assign']);
                })->withCabOrderCheck($request->cab_assign)->with(['accept'])->count(),
            "confirm_order" => $OrderInfo->where('pickup_status', 0)->where('drop_status', 0)->where('cab_assign',  $request['cab_assign'])->count(),
            "pickup_order" => $OrderInfo->where('pickup_status', 1)->where('drop_status', 0)->where('cab_assign',  $request['cab_assign'])->count(),
            "complete_order" => $OrderInfo->where('pickup_status', 1)->where('drop_status', 1)->where('cab_assign',  $request['cab_assign'])->count(),
            'canceled' => \App\Models\TourAndTravel::where('id', $request['cab_assign'])->first()['cancel_order'],
        ];
        $arrayList['order']['all_order'] = (($arrayList['order']['pending_order'] ?? 0) + ($arrayList['order']['confirm_order'] ?? 0) + ($arrayList['order']['pickup_order'] ?? 0) + ($arrayList['order']['complete_order'] ?? 0));

        $tourInformation = \App\Models\TourAndTravel::where('id', $request['cab_assign'])->first();

        $arrayList['wallet'] = [
            "withdrawable_balance" => $tourInformation['wallet_amount'],
            "collected_balance" => $tourInformation['withdrawal_amount'],
            "pending_withdraw" => $tourInformation['withdrawal_pending_amount'],
            "total_admin_commission" => $tourInformation['admin_commission'],
            "total_tax" => $tourInformation['gst_amount'],
        ];
        return response()->json(['status' => 1, "message" => "dashboard", 'data' => $arrayList], 200);
    }

    public function TourBannerShow(Request $request)
    {
        $banners = \App\Models\Banner::whereHas('category', function ($query) {
            $query->where('name', 'Tour');
        })->with('category')->where('banner_type', 'Tour Background Image')->where(['published' => 1, 'resource_type' => 'mahakalapp'])
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->whereNotNull('start_date')
                        ->whereNotNull('end_date')
                        ->whereDate('start_date', '<=', now())
                        ->whereDate('end_date', '>=', now());
                })
                    ->orWhere(function ($q) {
                        $q->whereNull('start_date')
                            ->whereNull('end_date');
                    });
            })->get()
            ->map(function ($tourVisit) {
                return getValidImage(path: 'storage/app/public/banner/' . ($tourVisit['photo'] ?? ""), type: 'backend-product');
            });
        if ($banners->isNotEmpty()) {
            return response()->json(['status' => 1, 'count' => count($banners), 'data' => $banners], 200);
        } else {
            return response()->json(['status' => 0, 'count' => 0, 'data' => []], 200);
        }
    }

    public function AllCategory()
    {
        $cities_tour = TourType::where('status', 1)->orderBy('id', 'desc')->get();
        if (!empty($cities_tour) && count($cities_tour) > 0) {
            $translation = [];
            foreach ($cities_tour as $key => $value) {
                $hindi_tour = $value ? $value->translations()->pluck('value', 'key')->toArray() : [];
                $translation[$key]['slug'] = $value['slug'];
                $translation[$key]['en_name'] = ($value['name'] ?? "");
                $translation[$key]['hi_name'] = ($hindi_tour['name'] ?? "");
            }
            return response()->json(['status' => 1, 'count' => count($translation), 'data' => $translation], 200);
        } else {
            return response()->json(['status' => 0, 'count' => 0, 'data' => []], 200);
        }
    }

    public function AllTour(Request $request)
    {
        $request->validate([
            'special_type' => ['nullable', function ($attribute, $value, $fail) {
                if (!TourType::where('slug', $value)->where('status', 1)->exists()) {
                    $fail('The selected tour type is invalid or inactive.');
                }
            },],
            "state_name" => ['nullable', function ($attribute, $value, $fail) {
                if (!TourVisits::where('state_name', $value)->where('status', 1)->exists()) {
                    $fail('The selected state name is invalid or inactive.');
                }
            },],
            "cities_name" => ['nullable', function ($attribute, $value, $fail) {
                if (!TourVisits::where('cities_name', $value)->where('status', 1)->exists()) {
                    $fail('The selected citie name is invalid or inactive.');
                }
            },],
        ], [
            'special_type' => 'special Tour type is required!',
        ]);
        $special_tour = [];
        $cities_tour = [];
        $cities_tour12 = [];
        if (!empty($request->state_name) && !empty($request->cities_name) && !empty($request->special_type)) {
            $special_tour = TourVisits::where('tour_type', $request->special_type)->where(['state_name' => $request->state_name, 'cities_name' => $request->cities_name])->where('status', 1)
                ->where(function ($query) {
                    $query->whereIn('use_date', [0, 2, 3, 4])
                        ->orWhere(function ($query) {
                            $query->where('use_date', 1)
                                ->where(function ($subQuery) {
                                    $subQuery->whereIn('customized_type', ['', '0'])
                                        ->whereNotNull('startandend_date')
                                        ->whereRaw('? < STR_TO_DATE(SUBSTRING_INDEX(startandend_date, " - ", 1), "%Y-%m-%d")', [date('Y-m-d')]);
                                })
                                ->orWhere(function ($subQuery) {
                                    $subQuery->whereIn('customized_type', [1, 2, 3]);
                                });
                        });
                    // })->withTourCheck()->groupBy('cities_name')->get()->groupBy('state_name');
                })->withTourCheck()->get()->groupBy('state_name');
        } else if (!empty($request->state_name) && empty($request->cities_name) && empty($request->special_type)) {
            $special_tour = TourVisits::where(['state_name' => $request->state_name])->where('status', 1)
                ->where(function ($query) {
                    $query->whereIn('use_date', [0, 2, 3, 4])
                        // ->orWhere(function ($query) {
                        //     $query->where('use_date', 1)
                        //         ->whereNotNull('startandend_date')
                        //         ->whereRaw('? < STR_TO_DATE(SUBSTRING_INDEX(startandend_date, " - ", 1), "%Y-%m-%d")', [date('Y-m-d')]);
                        // });
                        ->orWhere(function ($query) {
                            $query->where('use_date', 1)
                                ->where(function ($subQuery) {
                                    $subQuery->whereIn('customized_type', ['', '0'])
                                        ->whereNotNull('startandend_date')
                                        ->whereRaw('? < STR_TO_DATE(SUBSTRING_INDEX(startandend_date, " - ", 1), "%Y-%m-%d")', [date('Y-m-d')]);
                                })
                                ->orWhere(function ($subQuery) {
                                    $subQuery->whereIn('customized_type', [1, 2, 3]);
                                });
                        });
                })->withTourCheck()->groupBy('cities_name')->get()->groupBy('state_name');
        } else if (!empty($request->state_name) && !empty($request->cities_name) && empty($request->special_type)) {
            $special_tour = TourVisits::where(['state_name' => $request->state_name, 'cities_name' => $request->cities_name])->where('status', 1)
                ->where(function ($query) {
                    $query->whereIn('use_date', [0, 2, 3, 4])
                        ->orWhere(function ($query) {
                            $query->where('use_date', 1)
                                ->where(function ($subQuery) {
                                    $subQuery->whereIn('customized_type', ['', '0'])
                                        ->whereNotNull('startandend_date')
                                        ->whereRaw('? < STR_TO_DATE(SUBSTRING_INDEX(startandend_date, " - ", 1), "%Y-%m-%d")', [date('Y-m-d')]);
                                })
                                ->orWhere(function ($subQuery) {
                                    $subQuery->whereIn('customized_type', [1, 2, 3]);
                                });
                        });
                })->withTourCheck()->get()->groupBy('state_name');
        } else if (!empty($request->special_type)) {
            $newquery = TourVisits::where('tour_type', $request->special_type);
            if (!empty($request->state_name)) {
                $newquery->where(['state_name' => $request->state_name]);
            }
            if (!empty($request->cities_name)) {
                $newquery->where(['cities_name' => $request->cities_name]);
            }
            $special_tour = $newquery->where('status', 1)->where(function ($query) {
                $query->whereIn('use_date', [0, 2, 3, 4])
                    ->orWhere(function ($query) {
                        $query->where('use_date', 1)
                            ->where(function ($subQuery) {
                                $subQuery->whereIn('customized_type', ['', '0'])
                                    ->whereNotNull('startandend_date')
                                    ->whereRaw('? < STR_TO_DATE(SUBSTRING_INDEX(startandend_date, " - ", 1), "%Y-%m-%d")', [date('Y-m-d')]);
                            })
                            ->orWhere(function ($subQuery) {
                                $subQuery->whereIn('customized_type', [1, 2, 3]);
                            });
                    });
            })->withTourCheck()->groupBy('cities_name')->get()->groupBy('state_name');
        } else {
            $special_tour = TourVisits::where('status', 1)->where(function ($query) {
                $query->whereIn('use_date', [0, 2, 3, 4])
                    ->orWhere(function ($query) {
                        $query->where('use_date', 1)
                            ->where(function ($subQuery) {
                                $subQuery->whereIn('customized_type', ['', '0'])
                                    ->whereNotNull('startandend_date')
                                    ->whereRaw('? < STR_TO_DATE(SUBSTRING_INDEX(startandend_date, " - ", 1), "%Y-%m-%d")', [date('Y-m-d')]);
                            })
                            ->orWhere(function ($subQuery) {
                                $subQuery->whereIn('customized_type', [1, 2, 3]);
                            });
                    });
            })->withCount('TourOrderReview')
                ->withAvg('review', 'star')->withTourCheck()->groupBy('cities_name')->get()->groupBy('state_name');
        }
        // dd($special_tour);
        if (!empty($special_tour) && count($special_tour) > 0) {
            $p = 0;
            foreach ($special_tour as $key => $val) {
                if (!empty($val) && count($val) > 0) {
                    $q = 0;
                    foreach ($val as $kay => $state) {
                        $hindi_tour = (TourVisits::find($state['id'])) ? (TourVisits::find($state['id']))->translations()->pluck('value', 'key')->toArray() : [];
                        if ($q == 0) {
                            $cities_tour[$p]['en_state_name'] = $state['state_name'] ?? '';
                            $cities_tour[$p]['hi_state_name'] = $hindi_tour['state_name'] ?? "";
                        }

                        $cities_tour[$p]['list'][$q]['id'] = ($state['id'] ?? '');
                        $cities_tour[$p]['list'][$q]['slug'] = ($state['slug'] ?? "");

                        $cities_tour[$p]['list'][$q]['total_tour_count'] = TourVisits::where('status', 1)->where(function ($query) {
                            $query->whereIn('use_date', [0, 2, 3, 4])
                                ->orWhere(function ($query) {
                                    $query->where('use_date', 1)
                                        ->where(function ($subQuery) {
                                            $subQuery->whereIn('customized_type', ['', '0'])
                                                ->whereNotNull('startandend_date')
                                                ->whereRaw('? < STR_TO_DATE(SUBSTRING_INDEX(startandend_date, " - ", 1), "%Y-%m-%d")', [date('Y-m-d')]);
                                        })
                                        ->orWhere(function ($subQuery) {
                                            $subQuery->whereIn('customized_type', [1, 2, 3]);
                                        });
                                });
                        })->where('cities_name', ($state['cities_name'] ?? ""))->withTourCheck()->count();


                        $cities_tour[$p]['list'][$q]['share_link'] = route('tour.tourvisit', ['id' => ($state['slug'] ?? "")]);
                        $cities_tour[$p]['list'][$q]['en_cities_name'] = $state['cities_name'];
                        $cities_tour[$p]['list'][$q]['hi_cities_name'] = $hindi_tour['cities_name'] ?? "";
                        $cities_tour[$p]['list'][$q]['hi_tour_name'] = $hindi_tour['tour_name'] ?? "";
                        $cities_tour[$p]['list'][$q]['en_tour_name'] = $state['tour_name'];
                        $numb_days_en = '';
                        $numb_days_in = '';
                        if ($state['number_of_day'] == 0.5) {
                            $numb_days_en = 'Half Day';
                            $numb_days_in = 'आधा दिन';
                        } elseif ($state['number_of_day'] > 0 && ($state['number_of_night'] ?? 0) <= 0) {
                            $numb_days_en = ($state['number_of_day'] ?? '') . " Day";
                            $numb_days_in = ($state['number_of_day'] ?? '') . " दिन";
                        } else {
                            $numb_days_en = ($state['number_of_day'] ?? '') . "D/" . ($state['number_of_night'] ?? '') . "N";
                            $numb_days_in = ($state['number_of_day'] ?? '') . "दिन/" . ($state['number_of_night'] ?? '') . "रात्रि";
                        }
                        $cities_tour[$p]['list'][$q]['en_number_of_day'] = $numb_days_en;
                        $cities_tour[$p]['list'][$q]['hi_number_of_day'] = $numb_days_in;
                        $tour_type_show = '';
                        if ($state['use_date'] == 0) {
                            $tour_type_show = 'Cities Tour';
                        } elseif ($state['use_date'] == 1) {
                            $tour_type_show = 'Special Tour(With Date)';
                        } elseif ($state['use_date'] == 4) {
                            $tour_type_show = 'Special Tour(Without Date)';
                        } elseif ($state['use_date'] == 2) {
                            $tour_type_show = 'Daily Tour(With Address)';
                        } elseif ($state['use_date'] == 3) {
                            $tour_type_show = 'Daily Tour(WithOut Address)';
                        }
                        $cities_tour[$p]['list'][$q]['tour_type'] = $tour_type_show;

                        $cities_tour[$p]['list'][$q]['is_person_use'] = ($state['is_person_use'] ?? '');
                        $cities_tour[$p]['list'][$q]['ex_transport_price'] = (json_decode($state['ex_transport_price'] ?? '[]', true));

                        $cabs_lists = [];
                        $p_services = [];
                        if (!empty($state['cab_list_price']) && json_decode($state['cab_list_price'], true)) {
                            $getCabOrderDesc = json_decode($state['cab_list_price'], true);
                            foreach (array_reverse($getCabOrderDesc) as $kk => $val_v) {
                                $cabs_lists[$kk]['min_price'] =  number_format((float)($val_v['price'] / (1 - (($state['percentage_off'] ?? 0) / 100))), 2);
                                $cabs_lists[$kk]['price'] = $val_v['price'];
                                $cabs_lists[$kk]['cab_id'] = ($val_v['cab_id'] ?? 0);
                                $cabs_lists[$kk]['min'] = ($val_v['min'] ?? 0);
                                $cabs_lists[$kk]['max'] = ($val_v['max'] ?? 0);
                                $getCabs = \App\Models\TourCab::where('id', $val_v['cab_id'] ?? 0)->first();
                                $cab_name = ucwords($getCabs['name'] ?? '');
                                $cabs_lists[$kk]['cab_name'] = $cab_name;
                                $cabs_lists[$kk]['seats'] = ($getCabs['seats'] ?? '');
                                $cabs_lists[$kk]['image'] =  getValidImage(path: 'storage/app/public/tour_and_travels/cab/' . ($getCabs['image'] ?? ""), type: 'backend-product');
                                $p_services[] = 'transport';
                            }
                        }
                        $package_lists = [];
                        if (!empty($state['package_list_price']) && json_decode($state['package_list_price'], true)) {
                            foreach (json_decode($state['package_list_price'], true) as $kk => $val_s) {
                                $package_lists[$kk]['price'] = $val_s['pprice'];
                                $package_lists[$kk]['package_id'] = $val_s['package_id'];
                                $getpackage = \App\Models\TourPackage::where('id', $val_s['package_id'])->first();
                                $package_lists[$kk]['package_name'] = ucwords($getpackage['name'] ?? '');
                                $package_lists[$kk]['seats'] = ($getpackage['seats'] ?? '');
                                $package_lists[$kk]['type'] = ($getpackage['type'] ?? '');
                                $package_lists[$kk]['title'] = ($getpackage['title'] ?? '');
                                $package_lists[$kk]['image'] = getValidImage(path: 'storage/app/public/tour_and_travels/package/' . ($getpackage['image'] ?? ""), type: 'backend-product');
                                $p_services[] = ($getpackage['type'] ?? '');
                            }
                        }
                        $cities_tour[$p]['list'][$q]['cab_list'] = $cabs_lists;
                        $cities_tour[$p]['list'][$q]['package_list'] = $package_lists;
                        if ($state['is_person_use'] == 1) {
                            $p_services = [];
                            $raw_package = (json_decode(($state['is_included_package'] ?? '{}'), true));
                            $key_map = [
                                'cab' => 'transport',
                                'sightseen' => 'sightseen',
                                'food' => 'food',
                                'hotel' => 'hotel',
                            ];

                            $is_includeds_package = [];

                            foreach ($raw_package as $key3 => $value) {
                                if ($value == 1) {
                                    $mapped_key = $key_map[$key3] ?? $key3;
                                    $is_includeds_package[$mapped_key] = 1;
                                    $p_services[] = $mapped_key;
                                }
                            }
                        }
                        $cities_tour[$p]['list'][$q]['services'] = array_values(array_unique($p_services));
                        $cities_tour[$p]['list'][$q]['use_date'] = ($state['use_date'] ?? '');

                        $dateRange = explode(' - ', $state['startandend_date'] ?? "");
                        $startDate = isset($dateRange[0]) ? $dateRange[0] : '';
                        $endDate = isset($dateRange[1]) ? $dateRange[1] : '';
                        if ($startDate && $endDate) {
                            $start = new DateTime($startDate);
                            $end = new DateTime($endDate);
                            $difference = $start->diff($end)->days;
                            if (isset($state['customized_type']) && isset($state['customized_dates'])) {
                                $customizedType = $state['customized_type'];
                                $customizedDates = json_decode($state['customized_dates'] ?? "[]", true);
                                switch ($customizedType) {
                                    case 1:
                                        $today = new DateTime();
                                        $nextDates = [];
                                        foreach ($customizedDates as $day) {
                                            $next = new DateTime("next " . $day);
                                            // if (strtolower($today->format('l')) == strtolower($day)) {
                                            //     $next = clone $today;
                                            // }
                                            $nextDates[] = $next;
                                        }
                                        usort($nextDates, function ($a, $b) {
                                            return $a <=> $b;
                                        });
                                        $startDate = $nextDates[0]->format("Y-m-d");
                                        $endDate = $nextDates[0]->modify("+" . $difference . " days")->format("Y-m-d");
                                        break;

                                    case 2:
                                        $dayNumbers = array_map(function ($date) {
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
                                        $startDate = $nextDates[0]->format("Y-m-d");
                                        $endDate = $nextDates[0]->modify("+" . $difference . " days")->format("Y-m-d");
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
                                        $startDate = $nextDates[0]->format("Y-m-d");
                                        $endDate = $nextDates[0]->modify("+" . $difference . " days")->format("Y-m-d");;
                                        break;
                                }
                            }
                        }
                        if($startDate){
                            $cities_tour[$p]['list'][$q]['date'] = $startDate . " - " . $endDate;
                        }else{
                            $cities_tour[$p]['list'][$q]['date'] = "";
                        }

                        $plans = [
                            0 => ['name' => 'Basic', 'style' => '#495057'],
                            1 => ['name' => 'Standard', 'style' => '#218838'],
                            2 => ['name' => 'Premium', 'style' => '#0056b3'],
                            3 => ['name' => 'Golden', 'style' => '#FFA500'],
                            4 => ['name' => 'Luxury', 'style' => '#b02a37'],
                        ];

                        $selectedPlan = $state->plan_type ?? 0;
                        $cities_tour[$p]['list'][$q]['plan_type_name'] = $plans[$selectedPlan]['name'];
                        $cities_tour[$p]['list'][$q]['plan_type_color'] = $plans[$selectedPlan]['style'];

                        $cities_tour[$p]['list'][$q]['tour_order_review_count'] = (int)$state['tour_order_review_count'];
                        $cities_tour[$p]['list'][$q]['review_avg_star'] = number_format((float)$state['review_avg_star'], 2);
                        $cities_tour[$p]['list'][$q]['percentage_off'] = ($state['percentage_off'] ?? 0);

                        $cities_tour[$p]['list'][$q]['pickup_time'] = ($state['pickup_time'] ?? '');
                        $cities_tour[$p]['list'][$q]['pickup_location'] = ($state['pickup_location'] ?? '');
                        $cities_tour[$p]['list'][$q]['pickup_lat'] = ($state['pickup_lat'] ?? '');
                        $cities_tour[$p]['list'][$q]['pickup_long'] = ($state['pickup_long'] ?? '');
                        $cities_tour[$p]['list'][$q]['cities_name'] = ($state['cities_name'] ?? '');
                        $cities_tour[$p]['list'][$q]['country_name'] = ($state['country_name'] ?? '');
                        $cities_tour[$p]['list'][$q]['state_name'] = ($state['state_name'] ?? '');

                        $cities_tour[$p]['list'][$q]['customized_type'] = ($state['customized_type'] ?? '');
                        $getDate_type = $oldDate_type =  json_decode(($state['customized_dates'] ?? '[]'), true);
                        if (($state['customized_type'] ?? '') == 2 && $oldDate_type && count($oldDate_type) > 0) {
                            $getDate_type = [];
                            foreach ($oldDate_type as $dat2) {
                                $getDate_type[] = date('d', strtotime($dat2));
                            }
                        } elseif (($state['customized_type'] ?? '') == 3 && $oldDate_type && count($oldDate_type) > 0) {
                            $getDate_type = [];
                            foreach ($oldDate_type as $dat2) {
                                $getDate_type[] = date('d-m', strtotime($dat2));
                            }
                        }
                        $cities_tour[$p]['list'][$q]['customized_dates'] = ($getDate_type);

                        $cities_tour[$p]['list'][$q]['tour_image'] = getValidImage(path: 'storage/app/public/tour_and_travels/tour_visit/' . ($state['tour_image'] ?? ""), type: 'backend-product');
                        if (!empty($request->state_name) && !empty($request->cities_name) && empty($request->special_type)) {
                            $cities_tour12[$q] = $cities_tour[$p]['list'][$q];
                        } elseif (!empty($request->cities_name) && !empty($request->special_type)) {
                            $cities_tour12[$q] = $cities_tour[$p]['list'][$q];
                        } else {
                            $cities_tour12[$p] = $cities_tour[$p];
                        }
                        $q++;
                    }
                }
                $p++;
            }
        }
        if (!empty($cities_tour12) && count($cities_tour12) > 0) {
            return response()->json(['status' => 1, 'count' => count($cities_tour12), 'data' => $cities_tour12], 200);
        } else {
            return response()->json(['status' => 0, 'count' => 0, 'data' => []], 200);
        }
    }
    public function GetAllState()
    {
        $cities_tour = [];
        $special_tour = TourVisits::select('state_name')->where('status', 1)->where(function ($query) {
            $query->whereIn('use_date', [0, 2, 3, 4])
                ->orWhere(function ($query) {
                    $query->where('use_date', 1)
                        ->where(function ($subQuery) {
                            $subQuery->whereIn('customized_type', ['', '0'])
                                ->whereNotNull('startandend_date')
                                ->whereRaw('? < STR_TO_DATE(SUBSTRING_INDEX(startandend_date, " - ", 1), "%Y-%m-%d")', [date('Y-m-d')]);
                        })
                        ->orWhere(function ($subQuery) {
                            $subQuery->whereIn('customized_type', [1, 2, 3]);
                        });
                });
        })->withTourCheck()->groupBy('state_name')->get();

        if (!empty($special_tour) && count($special_tour) > 0) {
            $p = 0;
            foreach ($special_tour as $val) {
                $cities_tour[$p]['name'] = $val['state_name'];
                $states = \App\Models\States::where('name', $val['state_name'])->first();
                $cities_tour[$p]['logo'] = getValidImage(path: 'storage/app/public/state_logo/' . ($states['logo'] ?? ""), type: 'backend-profile');
                $p++;
            }
            return response()->json(['status' => 1, 'count' => count($cities_tour), 'data' => $cities_tour], 200);
        } else {
            return response()->json(['status' => 0, 'count' => 0, 'data' => []], 200);
        }
    }

    public function GetCitiesFilters(Request $request)
    {
        $request->validate([
            'special_type' => ['nullable', function ($attribute, $value, $fail) {
                if (!TourType::where('slug', $value)->where('status', 1)->exists()) {
                    $fail('The selected tour type is invalid or inactive.');
                }
            },],
            "state_name" => ['nullable', function ($attribute, $value, $fail) {
                if (!TourVisits::where('state_name', $value)->where('status', 1)->exists()) {
                    $fail('The selected state name is invalid or inactive.');
                }
            },],
            "cities_name" => ['nullable', function ($attribute, $value, $fail) {
                if (!TourVisits::where('cities_name', $value)->where('status', 1)->exists()) {
                    $fail('The selected citie name is invalid or inactive.');
                }
            },],
        ]);


        $querys = TourVisits::where('status', 1);
        if (!empty($request->special_type)) {
            $querys->where('tour_type', $request->special_type);
        }
        if (!empty($request->state_name)) {
            $querys->where(['state_name' => $request->state_name]);
        }
        if (!empty($request->cities_name)) {
            $querys->where(['cities_name' => $request->cities_name]);
        }
        $querys->where(function ($query) {
            $query->whereIn('use_date', [0, 2, 3, 4])
                ->orWhere(function ($query) {
                    $query->where('use_date', 1)
                        ->where(function ($subQuery) {
                            $subQuery->whereIn('customized_type', ['', '0'])
                                ->whereNotNull('startandend_date')
                                ->whereRaw('? < STR_TO_DATE(SUBSTRING_INDEX(startandend_date, " - ", 1), "%Y-%m-%d")', [date('Y-m-d')]);
                        })
                        ->orWhere(function ($subQuery) {
                            $subQuery->whereIn('customized_type', [1, 2, 3]);
                        });
                });
        })->withTourCheck();
        if (empty($request->cities_name)) {
            $querys->groupBy('cities_name');
        }
        $special_tour = $querys->get();
        $cities_tour = [];
        if (!empty($special_tour) && count($special_tour) > 0) {
            foreach ($special_tour as $key => $val) {
                $hindi_tour = $val ? $val->translations()->pluck('value', 'key')->toArray() : [];
                $cities_tour[$key]['id'] = ($val['id'] ?? '');
                $cities_tour[$key]['slug'] = ($val['slug'] ?? "");
                $cities_tour[$key]['hi_tour_name'] = $hindi_tour['tour_name'] ?? "";
                $cities_tour[$key]['en_tour_name'] = $val['tour_name'];
                $numb_days_en = '';
                $numb_days_in = '';
                if ($val['number_of_day'] == 0.5) {
                    $numb_days_en = 'Half Day';
                    $numb_days_in = 'आधा दिन';
                } elseif ($val['number_of_day'] > 0 && ($val['number_of_night'] ?? 0) <= 0) {
                    $numb_days_en = ($val['number_of_day'] ?? '') . " Day";
                    $numb_days_in = ($val['number_of_day'] ?? '') . " दिन";
                } else {
                    $numb_days_en = ($val['number_of_day'] ?? '') . "D/" . ($val['number_of_night'] ?? '') . "N";
                    $numb_days_in = ($val['number_of_day'] ?? '') . "दिन/" . ($val['number_of_night'] ?? '') . "रात्रि";
                }
                $cities_tour[$key]['en_number_of_day'] = $numb_days_en;
                $cities_tour[$key]['hi_number_of_day'] = $numb_days_in;

                $cities_tour[$key]['pickup_time'] = ($val['pickup_time'] ?? '');
                $cities_tour[$key]['pickup_location'] = ($val['pickup_location'] ?? '');
                $cities_tour[$key]['pickup_lat'] = ($val['pickup_lat'] ?? '');
                $cities_tour[$key]['pickup_long'] = ($val['pickup_long'] ?? '');
                $cities_tour[$key]['cities_name'] = ($val['cities_name'] ?? '');
                $cities_tour[$key]['country_name'] = ($val['country_name'] ?? '');
                $cities_tour[$key]['state_name'] = ($val['state_name'] ?? '');
                $cities_tour[$key]['is_person_use'] = ($val['is_person_use'] ?? '');
                $cities_tour[$key]['ex_transport_price'] = (json_decode($val['ex_transport_price'] ?? '[]', true));


                $cabs_lists = [];
                $p_services = [];
                if (!empty($val['cab_list_price']) && json_decode($val['cab_list_price'], true)) {
                    foreach (json_decode($val['cab_list_price'], true) as $kk => $val_v) {
                        $cabs_lists[$kk]['price'] = $val_v['price'];
                        $cabs_lists[$kk]['min'] = $val_v['min'] ?? 0;
                        $cabs_lists[$kk]['max'] = $val_v['max'] ?? 0;
                        $cabs_lists[$kk]['cab_id'] = $val_v['cab_id'] ?? 0;
                        $getCabs = \App\Models\TourCab::where('id', $val_v['cab_id'] ?? 0)->first();
                        $cab_name = ucwords($getCabs['name'] ?? '');
                        $cabs_lists[$kk]['cab_name'] = $cab_name;
                        $cabs_lists[$kk]['seats'] = ($getCabs['seats'] ?? '');
                        $cabs_lists[$kk]['image'] =  getValidImage(path: 'storage/app/public/tour_and_travels/cab/' . ($getCabs['image'] ?? ""), type: 'backend-product');

                        $p_services[] = 'transport';
                    }
                }
                $package_lists = [];
                if (!empty($val['package_list_price']) && json_decode($val['package_list_price'], true)) {
                    foreach (json_decode($val['package_list_price'], true) as $kk => $val_s) {
                        $package_lists[$kk]['price'] = $val_s['pprice'];
                        $package_lists[$kk]['package_id'] = $val_s['package_id'];
                        $getpackage = \App\Models\TourPackage::where('id', $val_s['package_id'])->first();
                        $package_lists[$kk]['package_name'] = ucwords($getpackage['name'] ?? '');
                        $package_lists[$kk]['seats'] = ($getpackage['seats'] ?? '');
                        $package_lists[$kk]['type'] = ($getpackage['type'] ?? '');
                        $package_lists[$kk]['title'] = ($getpackage['title'] ?? '');
                        $package_lists[$kk]['image'] = getValidImage(path: 'storage/app/public/tour_and_travels/package/' . ($getpackage['image'] ?? ""), type: 'backend-product');
                        $p_services[] = ($getpackage['type'] ?? '');
                    }
                }
                if ($val['is_person_use'] == 1) {
                    $p_services = [];
                    $raw_package = (json_decode(($val['is_included_package'] ?? '{}'), true));
                    $key_map = [
                        'cab' => 'transport',
                        'sightseen' => 'sightseen',
                        'food' => 'food',
                        'hotel' => 'hotel',
                    ];

                    $is_includeds_package = [];

                    foreach ($raw_package as $key2 => $value) {
                        if ($value == 1) {
                            $mapped_key = $key_map[$key2] ?? $key2;
                            $is_includeds_package[$mapped_key] = 1;
                            $p_services[] = $mapped_key;
                        }
                    }
                }
                $cities_tour[$key]['cab_list'] = $cabs_lists;
                $cities_tour[$key]['package_list'] = $package_lists;
                $cities_tour[$key]['services'] = array_values(array_unique($p_services));
                $cities_tour[$key]['use_date'] = ($val['use_date'] ?? '');
                $cities_tour[$key]['date'] = ($val['startandend_date'] ?? '');
                $cities_tour[$key]['tour_image'] = getValidImage(path: 'storage/app/public/tour_and_travels/tour_visit/' . ($val['tour_image'] ?? ""), type: 'backend-product');
            }
        }
        if (!empty($cities_tour) && count($cities_tour) > 0) {
            return response()->json(['status' => 1, 'count' => count($cities_tour), 'data' => $cities_tour], 200);
        } else {
            return response()->json(['status' => 0, 'count' => 0, 'data' => []], 200);
        }
    }

    public function CitiesTour(Request $request)
    {
        $request->validate([
            'special_type' => 'required|in:0,1',
            "cities_name" => "required",
        ], [
            'special_type.required' => 'special Tour type is required!',
            "cities_name.required" => "Cities Name is required!",
        ]);
        $cities_tour = [];
        if ($request->special_type == 1) {
            $special_tour = TourVisits::where('tour_type', 'special_tour')->where('status', 1)->where('cities_name', $request->cities_name)
                ->where(function ($query) {
                    $query->whereIn('use_date', [0, 2, 3, 4])
                        ->orWhere(function ($query) {
                            $query->where('use_date', 1)
                                ->where(function ($subQuery) {
                                    $subQuery->whereIn('customized_type', ['', '0'])
                                        ->whereNotNull('startandend_date')
                                        ->whereRaw('? < STR_TO_DATE(SUBSTRING_INDEX(startandend_date, " - ", 1), "%Y-%m-%d")', [date('Y-m-d')]);
                                })
                                ->orWhere(function ($subQuery) {
                                    $subQuery->whereIn('customized_type', [1, 2, 3]);
                                });
                        });
                })->withTourCheck()->get();
            if (!empty($special_tour) && count($special_tour) > 0) {
                foreach ($special_tour as $key => $val) {
                    $hindi_tour = $val ? $val->translations()->pluck('value', 'key')->toArray() : [];
                    $cities_tour[$key]['id'] = ($val['id'] ?? '');
                    $cities_tour[$key]['slug'] = ($val['slug'] ?? "");
                    $cities_tour[$key]['hi_tour_name'] = $hindi_tour['tour_name'] ?? "";
                    $cities_tour[$key]['en_tour_name'] = $val['tour_name'];

                    $numb_days_en = '';
                    $numb_days_in = '';
                    if ($val['number_of_day'] == 0.5) {
                        $numb_days_en = 'Half Day';
                        $numb_days_in = 'आधा दिन';
                    } elseif ($val['number_of_day'] > 0 && ($val['number_of_night'] ?? 0) <= 0) {
                        $numb_days_en = ($val['number_of_day'] ?? '') . " Day";
                        $numb_days_in = ($val['number_of_day'] ?? '') . " दिन";
                    } else {
                        $numb_days_en = ($val['number_of_day'] ?? '') . "D/" . ($val['number_of_night'] ?? '') . "N";
                        $numb_days_in = ($val['number_of_day'] ?? '') . "दिन/" . ($val['number_of_night'] ?? '') . "रात्रि";
                    }
                    $cities_tour[$key]['en_number_of_day'] = $numb_days_en;
                    $cities_tour[$key]['hi_number_of_day'] = $numb_days_in;
                    $cities_tour[$key]['is_person_use'] = ($val['is_person_use'] ?? '');
                    $cities_tour[$key]['ex_transport_price'] = (json_decode($val['ex_transport_price'] ?? '[]', true));
                    $cabs_lists = [];
                    $p_services = [];
                    if (!empty($val['cab_list_price']) && json_decode($val['cab_list_price'], true)) {
                        foreach (json_decode($val['cab_list_price'], true) as $kk => $val_c) {
                            $cabs_lists[$kk]['price'] = $val_c['price'];
                            $cabs_lists[$kk]['cab_id'] = ($val_c['cab_id'] ?? 0);
                            $cabs_lists[$kk]['min'] = ($val_c['min'] ?? 0);
                            $cabs_lists[$kk]['max'] = ($val_c['max'] ?? 0);
                            $getCabs = \App\Models\TourCab::where('id', ($val_c['cab_id'] ?? 0))->first();
                            $cab_name = ucwords($getCabs['name'] ?? '');
                            $cabs_lists[$kk]['cab_name'] = $cab_name;
                            $cabs_lists[$kk]['seats'] = ($getCabs['seats'] ?? '');
                            $cabs_lists[$kk]['image'] =  getValidImage(path: 'storage/app/public/tour_and_travels/cab/' . ($getCabs['image'] ?? ""), type: 'backend-product');
                            $p_services[] = 'transport';
                        }
                    }
                    $package_lists = [];
                    if (!empty($val['package_list_price']) && json_decode($val['package_list_price'], true)) {
                        foreach (json_decode($val['package_list_price'], true) as $kk => $val_s) {
                            $package_lists[$kk]['price'] = $val_s['pprice'];
                            $package_lists[$kk]['package_id'] = $val_s['package_id'];
                            $getpackage = \App\Models\TourPackage::where('id', $val_s['package_id'])->first();
                            $package_lists[$kk]['package_name'] = ucwords($getpackage['name'] ?? '');
                            $package_lists[$kk]['seats'] = ($getpackage['seats'] ?? '');
                            $package_lists[$kk]['type'] = ($getpackage['type'] ?? '');
                            $package_lists[$kk]['title'] = ($getpackage['title'] ?? '');
                            $package_lists[$kk]['image'] = getValidImage(path: 'storage/app/public/tour_and_travels/package/' . ($getpackage['image'] ?? ""), type: 'backend-product');
                            $p_services[] = ($getpackage['type'] ?? '');
                        }
                    }
                    $cities_tour[$key]['cab_list'] = $cabs_lists;
                    $cities_tour[$key]['package_list'] = $package_lists;
                    if ($val['is_person_use'] == 1) {
                        $p_services = [];
                        $raw_package = (json_decode(($val['is_included_package'] ?? '{}'), true));
                        $key_map = [
                            'cab' => 'transport',
                            'sightseen' => 'sightseen',
                            'food' => 'food',
                            'hotel' => 'hotel',
                        ];

                        $is_includeds_package = [];

                        foreach ($raw_package as $key1 => $value) {
                            if ($value == 1) {
                                $mapped_key = $key_map[$key1] ?? $key1;
                                $is_includeds_package[$mapped_key] = 1;
                                $p_services[] = $mapped_key;
                            }
                        }
                    }
                    $cities_tour[$key]['services'] = array_values(array_unique($p_services));
                    $cities_tour[$key]['use_date'] = ($val['use_date'] ?? '');
                    $cities_tour[$key]['date'] = ($val['startandend_date'] ?? '');
                    $cities_tour[$key]['tour_image'] = getValidImage(path: 'storage/app/public/tour_and_travels/tour_visit/' . $val['tour_image'], type: 'backend-product');
                }
            }
        } else {
            $special_tour = TourVisits::where('tour_type', 'cities_tour')->where('cities_name', $request->cities_name)->where('status', 1)->get();
            if (!empty($special_tour) && count($special_tour) > 0) {
                $p = 0;
                foreach ($special_tour as $key => $val) {
                    $hindi_tour = $val ? $val->translations()->pluck('value', 'key')->toArray() : [];
                    $cities_tour[$p]['id'] = ($val['slug'] ?? "");
                    $cities_tour[$p]['en_tour_name'] = $val['tour_name'] ?? "";
                    $numb_days_en = '';
                    $numb_days_in = '';
                    if ($val['number_of_day'] == 0.5) {
                        $numb_days_en = 'Half Day';
                        $numb_days_in = 'आधा दिन';
                    } elseif ($val['number_of_day'] > 0 && ($val['number_of_night'] ?? 0) <= 0) {
                        $numb_days_en = ($val['number_of_day'] ?? '') . " Day";
                        $numb_days_in = ($val['number_of_day'] ?? '') . " दिन";
                    } else {
                        $numb_days_en = ($val['number_of_day'] ?? '') . "D/" . ($val['number_of_night'] ?? '') . "N";
                        $numb_days_in = ($val['number_of_day'] ?? '') . "दिन/" . ($val['number_of_night'] ?? '') . "रात्रि";
                    }
                    $cities_tour[$p]['en_number_of_day'] = $numb_days_en;
                    $cities_tour[$p]['hi_number_of_day'] = $numb_days_in;
                    $cities_tour[$p]['is_person_use'] = ($val['is_person_use'] ?? '');
                    $cities_tour[$p]['ex_transport_price'] = (json_decode($val['ex_transport_price'] ?? '[]', true));
                    $cities_tour[$p]['hi_tour_name'] = $hindi_tour['tour_name'] ?? "";
                    $cities_tour[$p]['use_date'] = ($val['use_date'] ?? '');
                    $cities_tour[$p]['date'] = ($val['startandend_date'] ?? '');
                    // $cities_tour[$p]['package_list'] = json_decode($val['package_list'], true);
                    $cabs_lists = [];
                    $p_services = [];
                    if (!empty($val['cab_list_price']) && json_decode($val['cab_list_price'], true)) {
                        foreach (json_decode($val['cab_list_price'], true) as $kk => $val_p) {
                            $cabs_lists[$kk]['price'] = $val_p['price'];
                            $cabs_lists[$kk]['cab_id'] = ($val_p['cab_id'] ?? 0);
                            $cabs_lists[$kk]['min'] = ($val_c['min'] ?? 0);
                            $cabs_lists[$kk]['max'] = ($val_c['max'] ?? 0);
                            $getCabs = \App\Models\TourCab::where('id', ($val_p['cab_id'] ?? 0))->first();
                            $cab_name = ucwords($getCabs['name'] ?? '');
                            $cabs_lists[$kk]['cab_name'] = $cab_name;
                            $cabs_lists[$kk]['seats'] = ($getCabs['seats'] ?? '');
                            $cabs_lists[$kk]['image'] =  getValidImage(path: 'storage/app/public/tour_and_travels/cab/' . ($getCabs['image'] ?? ""), type: 'backend-product');
                            $p_services[] = 'transport';
                        }
                    }
                    $package_lists = [];
                    if (!empty($val['package_list_price']) && json_decode($val['package_list_price'], true)) {
                        foreach (json_decode($val['package_list_price'], true) as $kk => $val_s) {
                            $package_lists[$kk]['price'] = $val_s['pprice'];
                            $package_lists[$kk]['package_id'] = $val_s['package_id'];
                            $getpackage = \App\Models\TourPackage::where('id', $val_s['package_id'])->first();
                            $package_lists[$kk]['package_name'] = ucwords($getpackage['name'] ?? '');
                            $package_lists[$kk]['seats'] = ($getpackage['seats'] ?? '');
                            $package_lists[$kk]['type'] = ($getpackage['type'] ?? '');
                            $package_lists[$kk]['title'] = ($getpackage['title'] ?? '');
                            $package_lists[$kk]['image'] = getValidImage(path: 'storage/app/public/tour_and_travels/package/' . ($getpackage['image'] ?? ""), type: 'backend-product');
                            $p_services[] = ($getpackage['type'] ?? '');
                        }
                    }
                    $cities_tour[$p]['cab_list'] = $cabs_lists;
                    $cities_tour[$p]['package_list'] = $package_lists;
                    if ($val['is_person_use'] == 1) {
                        $p_services = [];
                        $raw_package = (json_decode(($val['is_included_package'] ?? '{}'), true));
                        $key_map = [
                            'cab' => 'transport',
                            'sightseen' => 'sightseen',
                            'food' => 'food',
                            'hotel' => 'hotel',
                        ];

                        $is_includeds_package = [];

                        foreach ($raw_package as $key1 => $value) {
                            if ($value == 1) {
                                $mapped_key = $key_map[$key1] ?? $key1;
                                $is_includeds_package[$mapped_key] = 1;
                                $p_services[] = $mapped_key;
                            }
                        }
                    }
                    $cities_tour[$p]['services'] = array_values(array_unique($p_services));
                    $cities_tour[$p]['image'] = getValidImage(path: 'storage/app/public/tour_and_travels/tour_visit/' . $val['tour_image'], type: 'backend-product');
                    $p++;
                }
            }
        }
        if (!empty($cities_tour) && count($cities_tour) > 0) {
            return response()->json(['status' => 1, 'count' => count($cities_tour), 'data' => $cities_tour], 200);
        } else {
            return response()->json(['status' => 0, 'count' => 0, 'data' => []], 200);
        }
    }

    public function TourLeads(Request $request)
    {
        $request->validate([
            'tour_id' =>  ['required', function ($attribute, $value, $fail) {
                if (!TourVisits::where(function ($query) use ($value) {
                    $query->where('id', $value)
                        ->orWhere('slug', $value);
                })->where('status', 1)->exists()) {
                    $fail('The selected tour is invalid or inactive.');
                }
            },],
            'package_id' => 'required',
            'user_id' => ['required', function ($attribute, $value, $fail) {
                if (!User::where('id', $value)->where('is_active', 1)->exists()) {
                    $fail('The selected user is invalid or inactive.');
                }
            },],
            'amount' => 'required|numeric|min:1',
        ], [
            'tour_id.required' => "tour is required!",
            'package_id.required' => "package is required!",
            'user_id.required' => "user Id is required!",
            'amount.required' => "amount is required!",
        ]);

        $tourInfo = TourVisits::where(function ($query) use ($request) {
            $query->where('id', $request->tour_id)
                ->orWhere('slug', $request->tour_id);
        })->where('status', 1)->first();

        $leads = new TourLeads();
        $leads->tour_id = ($tourInfo['id'] ?? 0);
        $leads->package_id = $request->package_id;
        $leads->user_id = $request->user_id;
        $leads->amount = $request->amount;
        $leads->platform = 'app';
        $leads->amount_status = 0;
        $leads->status = 1;
        $leads->save();

        return response()->json(['status' => 1, 'data' => ['insert_id' => $leads->id]], 200);
    }

    public function TourById(Request $request)
    {
        $request->validate([
            'tour_id' =>  ['required', function ($attribute, $value, $fail) {
                if (!TourVisits::where(function ($query) use ($value) {
                    $query->where('id', $value)
                        ->orWhere('slug', $value);
                })->where('status', 1)->exists()) {
                    $fail('The selected tour is invalid or inactive.');
                }
            },],
        ], [
            'tour_id.required' => "tour is required!",
        ]);
        $cities_tour = [];
        $special_tour = TourVisits::where(function ($query) use ($request) {
            $query->where('id', $request->tour_id)
                ->orWhere('slug', $request->tour_id);
        })->where('status', 1)->first();
        if (!empty($special_tour)) {
            $hindi_tour = $special_tour ? $special_tour->translations()->pluck('value', 'key')->toArray() : [];
            $getRecode = ['tour_name', 'description', 'highlights', 'inclusion', "exclusion", 'terms_and_conditions', 'cancellation_policy', 'notes'];
            foreach ($getRecode as $name) {
                $cities_tour['en_' . $name] = $special_tour[$name] ?? "";
                $cities_tour['hi_' . $name] = $hindi_tour[$name] ?? "";
            }
            $cities_tour['use_date'] = ($special_tour['use_date'] ?? '');
            $cities_tour['customized_type'] = ($special_tour['customized_type'] ?? '');
            $getDate_type = $oldDate_type =  json_decode(($special_tour['customized_dates'] ?? '[]'), true);
            if (($special_tour['customized_type'] ?? '') == 2 && $oldDate_type && count($oldDate_type) > 0) {
                $getDate_type = [];
                foreach ($oldDate_type as $dat2) {
                    $getDate_type[] = date('d', strtotime($dat2));
                }
            } elseif (($special_tour['customized_type'] ?? '') == 3 && $oldDate_type && count($oldDate_type) > 0) {
                $getDate_type = [];
                foreach ($oldDate_type as $dat2) {
                    $getDate_type[] = date('d-m', strtotime($dat2));
                }
            }
            $cities_tour['customized_dates'] = ($getDate_type);
            $dateRange = explode(' - ', $special_tour['startandend_date'] ?? "");
            $startDate = isset($dateRange[0]) ? $dateRange[0] : '';
            $endDate = isset($dateRange[1]) ? $dateRange[1] : '';

            if ($startDate && $endDate) {
                $start = new DateTime($startDate);
                $end = new DateTime($endDate);
                $difference = $start->diff($end)->days;
                if (isset($special_tour['customized_type']) && isset($special_tour['customized_dates'])) {
                    $customizedType = $special_tour['customized_type'];
                    $customizedDates = json_decode($special_tour['customized_dates'] ?? "[]", true);
                    switch ($customizedType) {
                        case 1:
                            $today = new DateTime('today');
                            $nextDates = [];
                            foreach ($customizedDates as $day) {
                                $next = new DateTime("next " . $day);
                                // if (strtolower($today->format('l')) == strtolower($day)) {
                                //     $next = clone $today;
                                // }
                                $nextDates[] = $next;
                            }
                            usort($nextDates, function ($a, $b) {
                                return $a <=> $b;
                            });
                            $startDate = $nextDates[0]->format("Y-m-d");
                            $endDate = $nextDates[0]->modify("+" . $difference . " days")->format("Y-m-d");
                            break;

                        case 2:
                            $dayNumbers = array_map(function ($date) {
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

                            $startDate = $nextDates[0]->format("Y-m-d");
                            $endDate = $nextDates[0]->modify("+" . $difference . " days")->format("Y-m-d");
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
                            $startDate = $nextDates[0]->format("Y-m-d");
                            $endDate = $nextDates[0]->modify("+" . $difference . " days")->format("Y-m-d");
                            break;
                    }
                }
            }
            if($startDate){
                $cities_tour['date'] = $startDate . " - " . $endDate;
            }else{
                $cities_tour['date'] = "";
            }

            $cities_tour['share_link'] = route('tour.tourvisit', ['id' => ($special_tour['slug'] ?? "")]);
            $cities_tour['pickup_time'] = ($special_tour['pickup_time'] ?? '');
            $cities_tour['pickup_location'] = ($special_tour['pickup_location'] ?? '');
            $cities_tour['pickup_lat'] = ($special_tour['pickup_lat'] ?? '');
            $cities_tour['pickup_long'] = ($special_tour['pickup_long'] ?? '');
            $cities_tour['cities_name'] = ($special_tour['cities_name'] ?? '');
            $cities_tour['country_name'] = ($special_tour['country_name'] ?? '');
            $cities_tour['state_name'] = ($special_tour['state_name'] ?? '');
            $cities_tour['ex_distance'] = ($special_tour['ex_distance'] ?? 0);
            $cities_tour['is_person_use'] = ($special_tour['is_person_use'] ?? 0);
            $cities_tour['ex_transport_price'] = (json_decode($special_tour['ex_transport_price'] ?? '[]', true));
            $cities_tour['transport_gst'] = \App\Models\ServiceTax::find(1)['tour_transport_tax'] ?? 1;
            $cities_tour['tour_gst'] = \App\Models\ServiceTax::find(1)['tour_tax'] ?? 1;
            $path = 'storage/app/public/tour_and_travels/tour_visit/' . $special_tour['itineraryupload'];
            if (DOMAIN_POINTED_DIRECTORY == 'public') {
                $result = str_replace('storage/app/public', 'storage', $path);
            } else {
                $result = $path;
            }
            $cities_tour['itineraryupload'] = (($special_tour['itineraryupload']) ? asset($result) : '');

            $numb_days_en = '';
            $numb_days_in = '';
            if ($special_tour['number_of_day'] == 0.5) {
                $numb_days_en = 'Half Day';
                $numb_days_in = 'आधा दिन';
            } elseif ($special_tour['number_of_day'] > 0 && ($special_tour['number_of_night'] ?? 0) <= 0) {
                $numb_days_en = ($special_tour['number_of_day'] ?? '') . " Day";
                $numb_days_in = ($special_tour['number_of_day'] ?? '') . " दिन";
            } else {
                $numb_days_en = ($special_tour['number_of_day'] ?? '') . "D/" . ($special_tour['number_of_night'] ?? '') . "N";
                $numb_days_in = ($special_tour['number_of_day'] ?? '') . "दिन/" . ($special_tour['number_of_night'] ?? '') . "रात्रि";
            }
            $cities_tour['en_number_of_day'] = $numb_days_en;
            $cities_tour['hi_number_of_day'] = $numb_days_in;
            // $cities_tour['package_list'] = json_decode($special_tour['package_list'], true);
            $cabs_lists = [];
            $p_services = [];
            $tour_package_total_price = 0;
            if ($special_tour['use_date'] == 1) {
                if (!empty($special_tour['cab_list_price']) && json_decode($special_tour['cab_list_price'], true)) {
                    $getCabOrderDesc = json_decode($special_tour['cab_list_price'], true);
                    foreach (array_reverse($getCabOrderDesc) as $kk => $val_p) {
                        $cab_id = ($val_p['cab_id'] ?? 0);
                        $getCabs = \App\Models\TourCab::where('id', $cab_id)->first();
                        if ($getCabs) {
                            $hindi_tourcabs = $getCabs ? $getCabs->translations()->pluck('value', 'key')->toArray() : [];
                        } else {
                            $hindi_tourcabs = [];
                        }

                        $available_seats = \App\Models\TourOrder::where('tour_id', $request->tour_id)
                            ->where('amount_status', 1)
                            ->where('pickup_date', $startDate)
                            ->where('status', 1)
                            ->where('available_seat_cab_id', $cab_id)
                            ->sum('qty');
                            if(in_array($special_tour['customized_type'],[1,2,3])){
                                $available_seats = 0;
                            }
                        if ($special_tour['is_person_use'] == 1) {
                            $cabs_lists[$kk] = [
                                'id' => $val_p['id'],
                                'price' => $val_p['price'],
                                'min' => $val_p['min'] ?? 0,
                                'max' => $val_p['max'] ?? 0,
                                'cab_id' =>  $val_p['id'], ///$cab_id,
                                'en_cab_name' => ucwords($getCabs['name'] ?? ''),
                                'hi_cab_name' => ucwords($hindi_tourcabs['name'] ?? ''),
                                'en_description' => ucwords($getCabs['description'] ?? ''),
                                'hi_description' => ucwords($hindi_tourcabs['description'] ?? ''),
                                'seats' => ($getCabs['seats'] ?? 0),
                                'total_seats' => ($getCabs['seats'] ?? 0),
                                'total_seats_message' => 1,
                                'total_booking_seats' => 0,
                                'image' => getValidImage(path: 'storage/app/public/tour_and_travels/cab/' . ($getCabs['image'] ?? ""), type: 'backend-product'),
                            ];
                        } else {
                            if (!isset($cabs_lists[$cab_id])) {
                                $cabs_lists[$cab_id] = [
                                    'id' => $val_p['id'],
                                    'price' => $val_p['price'],
                                    'cab_id' => $cab_id,
                                    'min' => $val_p['min'] ?? 0,
                                    'max' => $val_p['max'] ?? 0,
                                    'en_cab_name' => ucwords($getCabs['name'] ?? ''),
                                    'hi_cab_name' => ucwords($hindi_tourcabs['name'] ?? ''),
                                    'en_description' => ucwords($getCabs['description'] ?? ''),
                                    'hi_description' => ucwords($hindi_tourcabs['description'] ?? ''),
                                    'seats' => ($getCabs['seats'] ?? 0),
                                    'total_seats' => ($getCabs['seats'] ?? 0),
                                    'total_seats_message' => 1,
                                    'total_booking_seats' => $available_seats,
                                    'image' => getValidImage(path: 'storage/app/public/tour_and_travels/cab/' . ($getCabs['image'] ?? ""), type: 'backend-product'),
                                ];
                            } else {
                                $cabs_lists[$cab_id]['total_seats_message'] += 1;
                                $cabs_lists[$cab_id]['total_seats'] += ($getCabs['seats'] ?? 0);
                                $cabs_lists[$cab_id]['total_booking_seats'] += $available_seats;
                            }
                        }
                        $p_services[] = 'transport';
                    }
                    $cabs_lists = array_values($cabs_lists);
                }
            } else {
                if (!empty($special_tour['cab_list_price']) && json_decode($special_tour['cab_list_price'], true)) {
                    $getCabOrderDesc = json_decode($special_tour['cab_list_price'], true);
                    foreach (array_reverse($getCabOrderDesc) as $kk => $val_p) {
                        $cabs_lists[$kk]['price'] = $val_p['price'];
                        $cabs_lists[$kk]['cab_id'] = $val_p['cab_id'] ?? 0;
                        $cabs_lists[$kk]['min'] = $val_p['min'] ?? 0;
                        $cabs_lists[$kk]['max'] = $val_p['max'] ?? 0;
                        $getCabs = \App\Models\TourCab::where('id', $val_p['cab_id'] ?? 0)->first();
                        if ($getCabs) {
                            $hindi_tourcabs = $getCabs ? $getCabs->translations()->pluck('value', 'key')->toArray() : [];
                        } else {
                            $hindi_tourcabs = [];
                        }
                        $cabs_lists[$kk]['en_cab_name'] = ucwords($getCabs['name'] ?? '');
                        $cabs_lists[$kk]['hi_cab_name'] = ucwords($hindi_tourcabs['name'] ?? '');

                        $cabs_lists[$kk]['en_description'] = ucwords($getCabs['description'] ?? '');
                        $cabs_lists[$kk]['hi_description'] = ucwords($hindi_tourcabs['description'] ?? '');
                        $cabs_lists[$kk]['seats'] = ($getCabs['seats'] ?? '');
                        $cabs_lists[$kk]['total_seats'] = 0;
                        $cabs_lists[$kk]['total_seats_message'] = 0;
                        $cabs_lists[$kk]['total_booking_seats'] = 0;
                        $cabs_lists[$kk]['image'] =  getValidImage(path: 'storage/app/public/tour_and_travels/cab/' . ($getCabs['image'] ?? ""), type: 'backend-product');
                        $p_services[] = 'transport';
                    }
                }
            }

            $packages = json_decode($special_tour['package_list_price'] ?? "[]", true);
            $packageIds = array_column($packages, 'package_id');
            $cities_tour['hotel_type_list'] = \App\Models\TourPackage::select('hotel_type')->whereIn('id', $packageIds)->whereNotNull('hotel_type')->groupBy('hotel_type')->get();

            $package_lists = [];
            if (!empty($special_tour['package_list_price']) && json_decode($special_tour['package_list_price'], true) && $special_tour['use_date'] == 1  && $special_tour['is_person_use'] == 0) {
                foreach (json_decode($special_tour['package_list_price'], true) as $kk => $val_s) {
                    $tour_package_total_price += (((int)($val_s['included'] ?? 0) == 0) ? (float)$val_s['pprice'] : 0);
                }
            } else if (!empty($special_tour['package_list_price']) && json_decode($special_tour['package_list_price'], true)) {
                foreach (json_decode($special_tour['package_list_price'], true) as $kk => $val_s) {
                    $tour_package_total_price += (((int)($val_s['included'] ?? 0) == 0) ? (float)$val_s['pprice'] : 0);
                    $package_lists[$kk]['price'] = (((int)($val_s['included'] ?? 0) == 0) ? $val_s['pprice'] : 0);
                    $package_lists[$kk]['package_id'] = $val_s['package_id'];
                    $getpackage = \App\Models\TourPackage::where('id', $val_s['package_id'])->first();
                    $package_lists[$kk]['en_package_name'] = ucwords($getpackage['name'] ?? '');
                    $package_lists[$kk]['en_description'] = ucwords($getpackage['description'] ?? '');
                    $hindi_tourpackage = $getpackage ? $getpackage->translations()->pluck('value', 'key')->toArray() : [];
                    $package_lists[$kk]['hi_package_name'] = ($hindi_tourpackage['name'] ?? '');
                    $package_lists[$kk]['hi_description'] = ($hindi_tourpackage['description'] ?? '');
                    $package_lists[$kk]['seats'] = ($getpackage['seats'] ?? '');
                    $package_lists[$kk]['type'] = ($getpackage['type'] ?? '');
                    $package_lists[$kk]['title'] = ($getpackage['title'] ?? '');
                    $package_lists[$kk]['included_status'] = (int)($val_s['included'] ?? 0);
                    $package_lists[$kk]['hotel_type'] = ($getpackage['hotel_type'] ?? "");
                    $package_lists[$kk]['image'] = getValidImage(path: 'storage/app/public/tour_and_travels/package/' . ($getpackage['image'] ?? ""), type: 'backend-product');
                    $p_services[] = ($getpackage['type'] ?? '');
                    $cities_tour[$getpackage['type'] . '_list'][] = $package_lists[$kk];
                }
            }

            $cities_tour['cab_list'] = $cabs_lists;
            $cities_tour['package_list'] = $package_lists;
            $cities_tour['tour_package_total_price'] = $tour_package_total_price;
            // if ($special_tour['is_person_use'] == 1) {
            //     $p_services = [];
            //     $raw_package = (json_decode(($special_tour['is_included_package'] ?? '{}'), true));
            //     $key_map = [
            //         'cab' => 'transport',
            //         'sightseen' => 'sightseen',
            //         'food' => 'food',
            //         'hotel' => 'hotel',
            //     ];

            //     $is_includeds_package = [];

            //     foreach ($raw_package as $key3 => $value) {
            //         if ($value == 1) {
            //             $mapped_key = $key_map[$key3] ?? $key3;
            //             $is_includeds_package[$mapped_key] = 1;
            //             $p_services[] = $mapped_key;
            //         }
            //     }
            // }
            $cities_tour['services'] = array_values(array_unique($p_services));
            $cities_tour['image'] = getValidImage(path: 'storage/app/public/tour_and_travels/tour_visit/' . $special_tour['tour_image'], type: 'backend-product');
            $image_list = [];
            $image_list[] = getValidImage(path: 'storage/app/public/tour_and_travels/tour_visit/' . $special_tour['tour_image'], type: 'backend-product');
            if (!empty($special_tour['image']) && json_decode($special_tour['image'], true)) {
                foreach (json_decode($special_tour['image'], true) as $value) {
                    $image_list[] = getValidImage(path: 'storage/app/public/tour_and_travels/tour_visit/' . $value, type: 'backend-product');
                }
            }
            $cities_tour['image_list'] = $image_list;
            if (!empty($special_tour['time_slot']) && json_decode($special_tour['time_slot'], true)) {
                $cities_tour['time_slot'] = json_decode($special_tour['time_slot'], true);
            } else {
                $cities_tour['time_slot'] = [];
            }

            $cities_tour['itinerary_place'] = [];
            $get_itineraryData = \App\Models\TourVisitPlace::where('tour_visit_id', $request->tour_id)->where('status', 1)->get();
            if ($get_itineraryData) {
                foreach ($get_itineraryData as $key => $value) {
                    $gethindi = $value ? $value->translations()->pluck('value', 'key')->toArray() : [];
                    $cities_tour['itinerary_place'][$key]['id'] = $value['id'];
                    $cities_tour['itinerary_place'][$key]['en_name'] = $value['name'];
                    $cities_tour['itinerary_place'][$key]['hi_name'] = $gethindi['name'] ?? '';
                    $cities_tour['itinerary_place'][$key]['en_time'] = $value['time'];
                    $cities_tour['itinerary_place'][$key]['hi_time'] = $gethindi['time'] ?? "";

                    $cities_tour['itinerary_place'][$key]['en_description'] = $value['description'];
                    $cities_tour['itinerary_place'][$key]['hi_description'] = $gethindi['description'] ?? "";

                    $itinerary_image_list = [];
                    if (!empty($value['images']) && json_decode($value['images'], true)) {
                        foreach (json_decode($value['images'], true) as $itn_va) {
                            $itinerary_image_list[] = getValidImage(path: 'storage/app/public/tour_and_travels/tour_visit_place/' . $itn_va, type: 'backend-product');
                        }
                    }
                    $cities_tour['itinerary_place'][$key]['image'] = $itinerary_image_list;
                }
            }
        }
        if (!empty($cities_tour)) {
            $cities_tour['user_booking_count'] = \App\Models\TourOrder::where('tour_id', $request->tour_id)
                ->where('amount_status', 1)
                ->where('status', 1)
                ->count();
            $getDataUser =    \App\Models\TourOrder::where('tour_id', $request->tour_id)->where('amount_status', 1)->where('status', 1)->with(['userData'])->get();
            $cities_tour['user_profile_image'] = [];
            if ($getDataUser) {
                foreach ($getDataUser as $valimg) {
                    $cities_tour['user_profile_image'][] = getValidImage(path: 'storage/app/public/profile/' . ($value['userData']['image'] ?? ""), type: 'backend-profile');
                }
            }
            return response()->json(['status' => 1, 'count' => 1, 'data' => $cities_tour], 200);
        }
        return response()->json(['status' => 0, 'count' => 0, 'data' => []], 200);
    }

    public function TourSeatAvailability(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tour_id' => ['required', function ($attribute, $value, $fail) {
                if (!TourVisits::where(function ($query) use ($value) {
                    $query->where('id', $value)
                        ->orWhere('slug', $value);
                })->where('status', 1)->exists()) {
                    $fail('Tour ID does not exist.');
                }
            },],
            'cab_id' => 'required',
        ], [
            'tour_id.required' => 'Tour Id is Empty!',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'recode' => 0, 'data' => [], 'errors' => Helpers::error_processor($validator)], 200);
        }
        $special_tour = TourVisits::select('id', 'customized_type', 'customized_dates', 'cab_list_price')
            ->where(function ($query) use ($request) {
                $query->where('id', $request->tour_id)
                    ->orWhere('slug', $request->tour_id);
            })->whereIn('customized_type', [1, 2, 3])->where('status', 1)->first();

        if ($special_tour) {
            $cabList = json_decode($special_tour['cab_list_price'], true);
            $filteredCabs = array_filter($cabList, function ($cab) use ($request) {
                return $cab['cab_id'] == $request['cab_id'];
            });
            $cabCount = count($filteredCabs);
            $seatsPerCab = (TourCab::where('id', $request['cab_id'])->first()['seats'] ?? 0);  //17seats
            $totalAvailableSeats = $cabCount * $seatsPerCab;
            $availableDates = [];
            $customizedDates = json_decode($special_tour->customized_dates, true) ?? [];
            $startDate = now()->format('Y-m-d');
            $endDate = now()->addMonths(2)->format('Y-m-d');

            $bookedSeats = TourOrder::select(
                'pickup_date',
                \DB::raw('SUM(qty) as total_booked_seats')
            )
                ->where('tour_id', $request->tour_id)
                ->where('available_seat_cab_id', $request['cab_id'])
                ->whereBetween('pickup_date', [$startDate, $endDate])
                ->groupBy('pickup_date')
                ->get()
                ->pluck('total_booked_seats', 'pickup_date');
            switch ($special_tour->customized_type) {
                case 1:
                    $startDateObj = now();
                    $endDateObj = now()->addMonths(2);
                    $dayMap = [
                        'monday' => 1,
                        'tuesday' => 2,
                        'wednesday' => 3,
                        'thursday' => 4,
                        'friday' => 5,
                        'saturday' => 6,
                        'sunday' => 7
                    ];
                    $targetDays = [];
                    foreach ($customizedDates as $dayName) {
                        $lowerDay = strtolower($dayName);
                        if (isset($dayMap[$lowerDay])) {
                            $targetDays[] = $dayMap[$lowerDay];
                        }
                    }
                    $currentDate = $startDateObj->copy();
                    while ($currentDate->lte($endDateObj)) {
                        $dayOfWeek = $currentDate->dayOfWeek;
                        if (in_array($dayOfWeek, $targetDays)) {
                            $date = $currentDate->format('d-m-Y');
                            $date2 = $currentDate->format('Y-m-d');

                            $booked = $bookedSeats->get($date2) ?? 0;
                            $available = $totalAvailableSeats - $booked;
                            $dayNames = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                            $dayName = $dayNames[$dayOfWeek - 1];
                            $availableDates[$date] = $available;
                        }
                        $currentDate->addDay();
                    }
                    break;
                case 2: // Monthly - use only day numbers
                    $startDateObj = now();
                    $endDateObj = now()->addMonths(2);
                    $dayNumbers = [];
                    foreach ($customizedDates as $dateStr) {
                        $originalDate = \Carbon\Carbon::parse($dateStr);
                        $dayNumbers[] = $originalDate->day;
                    }
                    $dayNumbers = array_unique($dayNumbers);
                    sort($dayNumbers);
                    $currentDate = $startDateObj->copy()->startOfMonth();
                    while ($currentDate->lte($endDateObj)) {
                        foreach ($dayNumbers as $dayNumber) {
                            try {
                                $generatedDate = $currentDate->copy()->setDay($dayNumber);
                                if ($generatedDate->format('Y-m') === $currentDate->format('Y-m')) {
                                    $dateFormatted = $generatedDate->format('d-m-Y');
                                    $dateFormatted2 = $generatedDate->format('Y-m-d');
                                    if ($generatedDate->gte($startDateObj)) {
                                        $booked = $bookedSeats->get($dateFormatted2) ?? 0;
                                        $available = $totalAvailableSeats - $booked;
                                        $availableDates[$dateFormatted] =  $available;
                                    }
                                }
                            } catch (\Exception $e) {
                            }
                        }
                        $currentDate->addMonth();
                    }
                    break;
                case 3:
                    $startDateObj = now();
                    $endDateObj = now()->addMonths(2);

                    foreach ($customizedDates as $dateStr) {
                        $originalDate = \Carbon\Carbon::parse($dateStr);
                        $month = $originalDate->month;
                        $day = $originalDate->day;
                        $currentYearDate = $startDateObj->copy()
                            ->setYear($startDateObj->year)
                            ->setMonth($month)
                            ->setDay($day);
                        $nextYearDate = $currentYearDate->copy()->addYear();
                        $datesToCheck = [];
                        if ($currentYearDate->gte($startDateObj) && $currentYearDate->lte($endDateObj)) {
                            $datesToCheck[] = $currentYearDate;
                        }
                        if ($nextYearDate->gte($startDateObj) && $nextYearDate->lte($endDateObj)) {
                            $datesToCheck[] = $nextYearDate;
                        }
                        foreach ($datesToCheck as $date) {
                            $dateFormatted = $date->format('d-m-Y');
                            $dateFormatted2 = $date->format('Y-m-d');
                            $booked = $bookedSeats->get($dateFormatted2) ?? 0;
                            $available = $totalAvailableSeats - $booked;
                            $availableDates[$dateFormatted] = max(0, $available);
                        }
                    }
                    break;
            }
            return response()->json(['status' => 1, 'message' => '', 'data' => ['available_seats_by_date'=>$availableDates,'total_seats'=>$totalAvailableSeats]], 200);
        }else{
            return response()->json(['status' => 0, 'message' => 'Not Found Data', 'data' => []], 200);
        }
    }

    public function travellerInfo(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'tour_id' => ['required', function ($attribute, $value, $fail) {
                    if (!TourVisits::where(function ($query) use ($value) {
                        $query->where('id', $value)
                            ->orWhere('slug', $value);
                    })->where('status', 1)->exists()) {
                        $fail('Tour ID does not exist.');
                    }
                },],
            ], [
                'tour_id.required' => 'Tour Id is Empty!',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'recode' => 0, 'data' => [], 'errors' => Helpers::error_processor($validator)], 200);
            }

            $special_tour = TourVisits::where(function ($query) use ($request) {
                $query->where('id', $request->tour_id)
                    ->orWhere('slug', $request->tour_id);
            })->where('status', 1)->first();
            $getInfo = TourAndTravel::where('id', ($special_tour['created_id'] ?? 0))->where('status', 1)->first();
            if (!empty($special_tour) && !empty($getInfo)) {
                $getData = [];
                if (!empty($special_tour['created_id'])) {
                    $hindi_tour = $getInfo ? $getInfo->translations()->pluck('value', 'key')->toArray() : [];
                    $getData['id'] = ($getInfo['id'] ?? "");
                    $getData['en_owner_name'] = ($getInfo['owner_name'] ?? "");
                    $getData['hi_owner_name'] = ($hindi_tour['owner_name'] ?? "");
                    $getData['en_company_name'] = ($getInfo['company_name'] ?? "");
                    $getData['hi_company_name'] = ($hindi_tour['company_name'] ?? "");

                    $getData['experience'] = ($getInfo['experience'] ?? "");

                    $getAllTourIds = TourVisits::where('created_id', $getInfo['id'])->pluck('id')->toArray();
                    $avgStar = TourReviews::whereIn('tour_id', $getAllTourIds)->avg('star');

                    $getData['rating'] = (int)($avgStar ?? 0);
                    $getData['verified_status'] = ($getInfo['is_approve'] ?? "");
                    $getData['gst_no'] = ($getInfo['gst_number'] ?? "");

                    $getData['image'] = getValidImage(path: 'storage/app/public/tour_and_travels/doc/' . ($getInfo['image'] ?? ""), type: 'backend-product');
                }
                return response()->json(['status' => 1, 'count' => 1, 'data' => $getData], 200);
            }
            return response()->json(['status' => 0, 'count' => 0, 'data' => []], 200);
        } catch (\Exception $e) {
            return response()->json(['status'  => 0, 'count' => 0, 'message' => 'Something went wrong, please try again later.', 'error'   => $e->getMessage(), 'data'    => []], 200);
        }
    }

    public function BookingList(Request $request)
    {
        $request->validate([
            'user_id' =>  ['required', function ($attribute, $value, $fail) {
                if (!User::where('id', $value)->where('is_active', 1)->exists()) {
                    $fail('The selected user is invalid or inactive.');
                }
            },],
        ], [
            'user_id.required' => 'User Id is required!',
        ]);
        $bookingList = [];
        if (!empty($request->order_id)) {
            $all_booking = TourOrder::where('user_id', $request->user_id)->where('id', $request->order_id)->with(['Tour'])->first();
            if (!empty($all_booking)) {
                $bookingList['id'] = $all_booking['id'];
                $bookingList['order_id'] = $all_booking['order_id'];
                $bookingList['qty'] = $all_booking['qty'];
                $bookingList['en_tour_name'] = $all_booking['Tour']['tour_name'] ?? '';
                $bookingList['tour_id'] = $all_booking['tour_id'] ?? '';
                $getDatas = TourReviews::where('user_id', $all_booking['user_id'])->where('order_id', $all_booking['id'])->where('tour_id', $all_booking['tour_id'])->first();
                $bookingList['review_status'] = $getDatas['is_edited'] ?? 0;
                $tourTranslation = TourVisits::find($all_booking['tour_id']);
                $hindi_tour = [];
                if ($tourTranslation) {
                    $hindi_tour = $tourTranslation ? $tourTranslation->translations()->pluck('value', 'key')->toArray() : [];
                }
                $bookingList['hi_tour_name'] = $hindi_tour['tour_name'] ?? "";
                $bookingList['tour_image'] = getValidImage(path: 'storage/app/public/tour_and_travels/tour_visit/' . ($all_booking['Tour']['tour_image'] ?? ''), type: 'backend-product');
                $bookingList['order_id'] = $all_booking['order_id'];
                if ($all_booking['part_payment'] == 'custom') {
                    $bookingList['amount'] = (($all_booking['order_amount'] ?? 0) + ($all_booking['coupon_amount'] ?? 0));
                } else {
                    $bookingList['amount'] = (($all_booking['amount'] ?? 0) + ($all_booking['coupon_amount'] ?? 0));
                }
                $bookingList['coupon_amount'] = $all_booking['coupon_amount'];
                $total_amounts = (((float)$all_booking['amount'] ?? 0) + ((float)$all_booking['coupon_amount'] ?? 0));
                if ($all_booking['part_payment'] == 'part') {
                    $total_amounts += ((float)$all_booking['amount'] ?? 0);
                } elseif ($all_booking['part_payment'] == 'custom') {
                    $total_amounts = ((float)$all_booking['order_amount'] ?? 0);
                }
                $bookingList['total_amount'] = $total_amounts;
                $bookingList['remaining_amount'] = (($all_booking['part_payment'] == 'custom') ? ($all_booking['order_amount'] - $all_booking['amount']) : (($all_booking['part_payment'] == 'part') ? $all_booking['amount'] : 0));
                $bookingList['paid_amount'] = (((float)$all_booking['amount'] ?? 0) + ((float)$all_booking['coupon_amount'] ?? 0));
                $bookingList['refund_status'] = $all_booking['refund_status'] ?? 0;
                $bookingList['refund_amount'] = $all_booking['refund_amount'] ?? 0;
                $bookingList['pay_amount'] = $all_booking['amount'];
                $bookingList['total_tax_price'] = 0;
                $booking_arrays = [];
                $tba = 0;
                if (!empty($all_booking['booking_package']) && json_decode($all_booking['booking_package'], true)) {
                    foreach (json_decode($all_booking['booking_package'], true) as $key => $value) {
                        if ($value['type'] == 'cab') {
                            $getCabs = \App\Models\TourCab::where('id', $value['id'])->first();
                            $hindi_tourcabs = [];
                            if ($getCabs) {
                                $hindi_tourcabs = $getCabs ? $getCabs->translations()->pluck('value', 'key')->toArray() : [];
                            }
                            $booking_arrays[$tba]['en_name'] = ucwords($getCabs['name'] ?? '');
                            $booking_arrays[$tba]['hi_name'] = ucwords($hindi_tourcabs['name'] ?? '');
                            $booking_arrays[$tba]['image'] =  getValidImage(path: 'storage/app/public/tour_and_travels/cab/' . ($getCabs['image'] ?? ""), type: 'backend-product');
                            $booking_arrays[$tba]['price'] = $value['price'];
                            if (($all_booking['use_date'] == 2 || $all_booking['use_date'] == 3 || $all_booking['use_date'] == 4) && !empty(json_decode($all_booking['Tour']['package_list_price'] ?? '[]', true)) && json_decode($all_booking['Tour']['package_list_price'] ?? '[]', true)) {
                                foreach (json_decode($all_booking['Tour']['package_list_price'] ?? '[]', true) as $pq => $valupq) {
                                    $tba++;
                                    $tourPackages = \App\Models\TourPackage::where('id', $valupq['package_id'])->first();
                                    $hindi_tourpack = [];
                                    if ($tourPackages) {
                                        $hindi_tourpack = $tourPackages ? $tourPackages->translations()->pluck('value', 'key')->toArray() : [];
                                    }
                                    $booking_arrays[$tba]['en_name'] = ucwords($tourPackages['name'] ?? '');
                                    $booking_arrays[$tba]['hi_name'] = ucwords($hindi_tourpack['name'] ?? '');
                                    $booking_arrays[$tba]['image'] = getValidImage(path: 'storage/app/public/tour_and_travels/package/' . ($tourPackages['image'] ?? ""), type: 'backend-product');
                                    $booking_arrays[$tba]['price'] = 0;
                                    $booking_arrays[$tba]['qty'] = $value['qty'];
                                }
                                // $tba++;
                            }
                        } elseif ($value['type'] == 'per_head') {
                            $booking_arrays[$tba]['en_name'] = "Per Head";
                            $booking_arrays[$tba]['hi_name'] = "प्रति व्यक्ति";
                            $booking_arrays[$tba]['image'] = '';
                            $booking_arrays[$tba]['price'] = $value['price'];
                        } elseif ($value['type'] == 'cgst' || $value['type'] == 'sgst') {
                            $bookingList[$value['type'] . '_title'] = $value['title'];
                            $bookingList[$value['type'] . '_price'] = $value['price'];
                            $bookingList['total_tax_price'] += $value['price'];
                            continue;
                        } else if ($value['type'] == 'other' || $value['type'] ==  "foods" || $value['type'] == "hotel" || \Illuminate\Support\Str::startsWith($value['type'], 'other')) {
                            $tourPackages = \App\Models\TourPackage::where('id', $value['id'])->first();
                            $hindi_tourpack = [];
                            if ($tourPackages) {
                                $hindi_tourpack = $tourPackages ? $tourPackages->translations()->pluck('value', 'key')->toArray() : [];
                            }
                            $booking_arrays[$tba]['en_name'] = ucwords($tourPackages['name'] ?? '');
                            $booking_arrays[$tba]['hi_name'] = ucwords($hindi_tourpack['name'] ?? '');
                            $booking_arrays[$tba]['image'] = getValidImage(path: 'storage/app/public/tour_and_travels/package/' . ($tourPackages['image'] ?? ''), type: 'backend-product');
                            $booking_arrays[$tba]['price'] = (($all_booking['use_date'] == 0) ? $value['price'] : 0);
                        } else {
                            $booking_arrays[$tba]['en_name'] = str_replace('_', ' ', $value['type']);
                            $booking_arrays[$tba]['hi_name'] = str_replace('_', ' ', $value['type']);
                            $booking_arrays[$tba]['image'] = '';
                            $booking_arrays[$tba]['price'] = $value['price'];
                        }
                        $booking_arrays[$tba]['qty'] = $value['qty'];
                        $tba++;
                    }
                }
                $bookingList['booking_packages'] = $booking_arrays;
                $bookingList['part_payment'] = $all_booking['part_payment'];
                $bookingList['amount_status'] = $all_booking['amount_status'];
                $bookingList['transaction_id'] = $all_booking['transaction_id'];
                $bookingList['refund_status'] = $all_booking['refund_status'];
                $bookingList['pickup_address'] = $all_booking['pickup_address'];
                $bookingList['pickup_date'] = date('d M,Y', strtotime($all_booking['pickup_date']));
                $bookingList['pickup_time'] = $all_booking['pickup_time'];
                $bookingList['pickup_otp'] = $all_booking['pickup_otp'];
                $bookingList['pickup_status'] = $all_booking['pickup_status'];
                $bookingList['drop_opt'] = $all_booking['drop_opt'];
                $bookingList['drop_status'] = $all_booking['drop_status'];
                $bookingList['booking_time'] = date('d M,Y h:i A', strtotime($all_booking['created_at']));
                $getSpecial_tour = \App\Models\TourRefundPolicy::where('status', 1)->where('type', $all_booking['Tour']['tour_type'] ?? '')->orderBy('day', 'desc')->get();
                $Amount_Pay = 0;
                $pickupTimestamp = strtotime($all_booking['pickup_date'] . ' ' . $all_booking['pickup_time']);
                if (!empty($getSpecial_tour) && count($getSpecial_tour) > 0) {
                    foreach ($getSpecial_tour as $val) {
                        $calculatedTimestamp = strtotime("-" . $val['day'] . " hours", $pickupTimestamp);
                        $currentTimestamp = strtotime(date('Y-m-d h:i A'));
                        if ($currentTimestamp <= $calculatedTimestamp) {
                            $Amount_Pay = ($all_booking['amount'] * $val['percentage']) / 100;
                            break;
                        }
                    }
                }
                $bookingList['cancel_refund_amount_given'] = $Amount_Pay;
                $bookingList['invoice_url'] = url('api/v1/tour/tour-order-invoice/' . $all_booking['id']);

                $bookingList['itinerary_place'] = [];
                $get_itineraryData = \App\Models\TourVisitPlace::where('tour_visit_id', $all_booking['tour_id'])->where('status', 1)->get();
                if ($get_itineraryData) {
                    foreach ($get_itineraryData as $key => $value) {
                        $gethindi = $value ? $value->translations()->pluck('value', 'key')->toArray() : [];
                        $bookingList['itinerary_place'][$key]['id'] = $value['id'];
                        $bookingList['itinerary_place'][$key]['en_name'] = $value['name'];
                        $bookingList['itinerary_place'][$key]['hi_name'] = $gethindi['name'] ?? '';
                        $bookingList['itinerary_place'][$key]['en_time'] = $value['time'];
                        $bookingList['itinerary_place'][$key]['hi_time'] = $gethindi['time'] ?? '';

                        $bookingList['itinerary_place'][$key]['en_description'] = $value['description'];
                        $bookingList['itinerary_place'][$key]['hi_description'] = $gethindi['description'] ?? '';

                        $itinerary_image_list = [];
                        if (!empty($value['images']) && json_decode($value['images'], true)) {
                            foreach (json_decode($value['images'], true) as $itn_va) {
                                $itinerary_image_list[] = getValidImage(path: 'storage/app/public/tour_and_travels/tour_visit_place/' . $itn_va, type: 'backend-product');
                            }
                        }
                        $bookingList['itinerary_place'][$key]['image'] = $itinerary_image_list;
                    }
                }
            }
            return response()->json(['status' => 1, 'count' => 1, 'data' => $bookingList], 200);
        } else {
            $all_booking = TourOrder::where('user_id', $request->user_id)->with(['Tour'])->orderBy('id', "desc")->get();
            if (!empty($all_booking) && count($all_booking) > 0) {
                foreach ($all_booking as $key => $value) {
                    $bookingList[$key]['id'] = $value['id'];
                    $bookingList[$key]['order_id'] = $value['order_id'];
                    $bookingList[$key]['qty'] = $value['qty'];
                    $bookingList[$key]['en_tour_name'] = $value['Tour']['tour_name'] ?? '';
                    $tourTranslation = TourVisits::find($value['tour_id']);
                    $hindi_tour = [];
                    if ($tourTranslation) {
                        $hindi_tour = $tourTranslation ? $tourTranslation->translations()->pluck('value', 'key')->toArray() : [];
                    }
                    $bookingList[$key]['hi_tour_name'] = $hindi_tour['tour_name'] ?? "";
                    $bookingList[$key]['tour_image'] = getValidImage(path: 'storage/app/public/tour_and_travels/tour_visit/' . ($value['Tour']['tour_image'] ?? ''), type: 'backend-product');
                    $bookingList[$key]['order_id'] = $value['order_id'];
                    $bookingList[$key]['amount'] = (($value['amount'] ?? 0) + ($value['coupon_amount'] ?? 0));
                    $bookingList[$key]['coupon_amount'] = $value['coupon_amount'];
                    if ($value['part_payment'] == 'custom') {
                        $bookingList[$key]['pay_amount'] = ($value['order_amount'] ?? 0);
                    } else {
                        $bookingList[$key]['pay_amount'] = ($value['amount'] ?? 0);
                    }
                    $bookingList[$key]['amount_status'] = $value['amount_status'];
                    $bookingList[$key]['transaction_id'] = $value['transaction_id'];
                    $bookingList[$key]['refund_status'] = $value['refund_status'];
                    $bookingList[$key]['pickup_address'] = $value['pickup_address'];
                    $bookingList[$key]['pickup_date'] = $value['pickup_date'];
                    $bookingList[$key]['pickup_time'] = $value['pickup_time'];
                    $bookingList[$key]['pickup_otp'] = $value['pickup_otp'];
                    $bookingList[$key]['pickup_status'] = $value['pickup_status'];
                    $bookingList[$key]['drop_opt'] = $value['drop_opt'];
                    $bookingList[$key]['drop_status'] = $value['drop_status'];
                    $bookingList[$key]['booking_time'] = $value['created_at'];
                    $bookingList[$key]['part_payment'] = $value['part_payment'];
                }
                return response()->json(['status' => 1, 'count' => count($bookingList), 'data' => $bookingList], 200);
            }
        }
        return response()->json(['status' => 0, 'count' => 0, 'data' => $bookingList], 200);
    }

    public function BookingOrderRemmimgPay(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => ['required', function ($attribute, $value, $fail) {
                if (!User::where('id', $value)->where('is_active', 1)->exists()) {
                    $fail('The selected user is invalid or inactive.');
                }
            },],
            'order_id' => ['required', function ($attribute, $value, $fail) use ($request) {
                if (!TourOrder::where('id', $value)->where('user_id', $request['user_id'])->exists()) {
                    $fail('The selected Order id invalid.');
                }
            },],
            'wallet_type' => 'required|in:0,1',
            'payment_amount' => 'required|numeric|min:1',
            'transaction_id' => 'required',
            'online_pay' => 'required_unless:transaction_id,wallet',
        ], [
            'user_id.required' => 'Cab Id is Empty!',
            'order_id.required' => 'Order Id is Empty!',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'recode' => 0, 'data' => [], 'errors' => Helpers::error_processor($validator)], 200);
        }

        if ($request->wallet_type == 1 && ($request['online_pay'] ?? 0) > 0) {
            User::where('id', $request->user_id)->update(['wallet_balance' => \Illuminate\Support\Facades\DB::raw('wallet_balance + ' . $request['online_pay'])]);
            $wallet_transaction = new \App\Models\WalletTransaction();
            $wallet_transaction->user_id = $request->user_id;
            $wallet_transaction->transaction_id = (($request->transaction_id) ? $request->transaction_id : \Illuminate\Support\Str::uuid());
            $wallet_transaction->reference = 'add_funds_to_wallet';
            $wallet_transaction->transaction_type = 'add_fund';
            $wallet_transaction->balance = User::where('id', $request->user_id)->first()['wallet_balance'];
            $wallet_transaction->credit = $request['online_pay'];
            $wallet_transaction->save();
        }

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            $user = User::find($request->user_id);
            $event_booking = TourOrder::with(['Tour'])->find($request['order_id']);
            if ($event_booking['part_payment'] == 'full') {
                return response()->json(['status' => 0, 'message' => "full Pay Successfully", 'data' => []], 200);
            } elseif ($event_booking['amount'] > $request['payment_amount']) {
                return response()->json(['status' => 0, 'message' => "Please Check Remaining Amount Pay", 'data' => []], 200);
            }
            if ($request->wallet_type == 1) {
                if ($user['wallet_balance'] >= $request['payment_amount']) {
                    User::where('id', $user['id'])->update(['wallet_balance' => \Illuminate\Support\Facades\DB::raw('wallet_balance - ' . $request['payment_amount'])]);
                    $gst_amount = 0;
                    $admin_commission = 0;
                    $final_amount = $request['payment_amount'];
                    $eventtax = \App\Models\ServiceTax::find(1);
                    if ($eventtax['tour_tax']) {
                        // $gst_amount = (($final_amount * ($eventtax['tour_tax'] ?? 0)) / 100);
                        // $final_amount = $final_amount - $gst_amount;
                    }
                    if ($event_booking['Tour']['tour_commission']) {
                        $admin_commission = (($final_amount * $event_booking['Tour']['tour_commission']) / 100);
                        $final_amount = ($final_amount - $admin_commission);
                    }
                    TourOrder::where('id', $request['order_id'])->update(['part_payment' => 'full', 'admin_commission' => \Illuminate\Support\Facades\DB::raw('admin_commission + ' . $admin_commission), 'gst_amount' => \Illuminate\Support\Facades\DB::raw('gst_amount + ' . $gst_amount), 'final_amount' => \Illuminate\Support\Facades\DB::raw('final_amount + ' . $final_amount), 'amount' => \Illuminate\Support\Facades\DB::raw('amount + ' . $request['payment_amount'])]);
                    $wallet_transaction = new \App\Models\WalletTransaction();
                    $wallet_transaction->user_id = $user['id'];
                    $wallet_transaction->transaction_id = \Illuminate\Support\Str::uuid();
                    $wallet_transaction->reference = 'Tour order';
                    $wallet_transaction->transaction_type = 'tour_order';
                    $wallet_transaction->balance = User::where('id', $user['id'])->first()['wallet_balance'];
                    $wallet_transaction->debit = $request->payment_amount;
                    $wallet_transaction->save();
                    $message_data['orderId'] = ($event_booking['order_id'] ?? '');
                    $message_data['title_name'] = ($event_booking['Tour']['tour_name'] ?? '');
                    $message_data['booking_date'] = ($event_booking['pickup_date'] ?? '');
                    $message_data['time'] = ($event_booking['pickup_time'] ?? '');
                    $message_data['place_name'] = ($event_booking['pickup_address'] ?? '');
                    $message_data['tour_type'] = ucwords(str_replace('_', ' ', (($event_booking['Tour']['tour_type'] ?? ''))));
                    $message_data['final_amount'] = webCurrencyConverter(amount: (float)$event_booking['amount'] ?? 0);
                    $message_data['customer_id'] = $request->user_id;
                    if ($event_booking['Tour']['tour_image']) {
                        $message_data['type'] = 'text-with-media';
                        $message_data['attachment'] = asset('/storage/app/public/tour_and_travels/tour_visit/' . $event_booking['Tour']['tour_image'] ?? '');
                    }
                    $message_data['remain_amount'] = webCurrencyConverter(amount: 0);
                    Helpers::whatsappMessage('tour', 'remaining payment success', $message_data);

                    \Illuminate\Support\Facades\DB::commit();
                    return response()->json(['status' => 1, 'message' => "Remaining amount pay Successfully", 'data' => []], 200);
                } else {
                    return response()->json(['status' => 0, 'message' => 'please wallet Amount Check', 'data' => []], 200);
                }
            } else {
                $eventtax = \App\Models\ServiceTax::find(1);
                $gst_amount = 0;
                $admin_commission = 0;
                $final_amount = $request->payment_amount;
                if ($eventtax['tour_tax']) {
                    // $gst_amount = (($final_amount * ($eventtax['tour_tax'] ?? 0)) / 100);
                    // $final_amount = $final_amount - $gst_amount;
                }
                if ($event_booking['Tour']['tour_commission']) {
                    $admin_commission = (($final_amount * $event_booking['Tour']['tour_commission']) / 100);
                    $final_amount = ($final_amount - $admin_commission);
                }
                TourOrder::where('id', $request['order_id'])->update(['part_payment' => 'full', 'admin_commission' => \Illuminate\Support\Facades\DB::raw('admin_commission + ' . $admin_commission), 'gst_amount' => \Illuminate\Support\Facades\DB::raw('gst_amount + ' . $gst_amount), 'final_amount' => \Illuminate\Support\Facades\DB::raw('final_amount + ' . $final_amount), 'amount' => \Illuminate\Support\Facades\DB::raw('amount + ' . $request['payment_amount'])]);
                $message_data['orderId'] = ($event_booking['order_id'] ?? '');
                $message_data['title_name'] = ($event_booking['Tour']['tour_name'] ?? '');
                $message_data['booking_date'] = ($event_booking['pickup_date'] ?? '');
                $message_data['time'] = ($event_booking['pickup_time'] ?? '');
                $message_data['place_name'] = ($event_booking['pickup_address'] ?? '');
                $message_data['tour_type'] = ucwords(str_replace('_', ' ', (($event_booking['Tour']['tour_type'] ?? ''))));
                $message_data['final_amount'] = webCurrencyConverter(amount: (float)$event_booking['amount'] ?? 0);
                $message_data['customer_id'] = $request->user_id;
                if ($event_booking['Tour']['tour_image']) {
                    $message_data['type'] = 'text-with-media';
                    $message_data['attachment'] = asset('/storage/app/public/tour_and_travels/tour_visit/' . $event_booking['Tour']['tour_image'] ?? '');
                }
                $message_data['remain_amount'] = webCurrencyConverter(amount: 0);
                Helpers::whatsappMessage('tour', 'remaining payment success', $message_data);

                \Illuminate\Support\Facades\DB::commit();
                return response()->json(['status' => 1, 'message' => 'Remaining Amount pay Successfully', 'data' => []], 200);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return response()->json(['status' => 0, 'message' => 'An error occurred: ' . $e->getMessage(), 'data' => []], 200);
        }
    }

    public function touraddcomment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => ['required', function ($attribute, $value, $fail) {
                if (!User::where('id', $value)->where('is_active', 1)->exists()) {
                    $fail('The selected user is invalid or inactive.');
                }
            }],
            'tour_id' => ['required', function ($attribute, $value, $fail) {
                if (!TourVisits::where('id', $value)->exists()) {
                    $fail('Tour ID does not exist.');
                }
            }],
            'order_id' => 'required',
            'type' => 'required|in:view,add',
        ], [
            'user_id.required' => 'User Id is Empty!',
            'tour_id.required' => 'Tour Id is Empty!',
            'star.required' => 'Star is Empty!',
            'comment.required' => 'Comment is Empty!',
        ]);
        $validator->sometimes('star', 'required|numeric|between:1,5', function ($input) {
            return $input->type === 'add';
        });
        $validator->sometimes('comment', 'required', function ($input) {
            return $input->type === 'add';
        });

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'recode' => 0, 'data' => [], 'errors' => Helpers::error_processor($validator)], 403);
        }
        $images = '';
        $contact = TourReviews::where('user_id', $request->user_id)->where('order_id', $request->order_id)->where('tour_id', $request->tour_id)->with(['userData'])->first();
        if ($request->type == 'view') {
            $getList['star'] = $contact['star'] ?? '';
            $getList['created_at'] = date('d M,Y h:i A', strtotime($contact['created_at'] ?? ''));
            $getList['comment'] = $contact['comment'] ?? '';
            $getList['is_edited'] = $contact['is_edited'] ?? '';
            $getList['user_name'] = $contact['userData']['name'] ?? '';
            $getList['user_image'] = getValidImage(path: 'storage/app/public/profile/' . ($contact['userData']['image'] ?? ""), type: 'backend-product');
            if (!empty($value['image'])) {
                $getList['image'] =  getValidImage(path: 'storage/app/public/tour_and_travels/review/' . $contact['image'], type: 'backend-product');
            }
            return response()->json(['status' => 1, 'message' => 'get Comment', 'recode' => 1, 'data' => $getList, 'errors' => []], 200);
        } else {
            if (!$contact || $contact['is_edited'] == 0) {
                if ($request->file('image')) {
                    $images = ImageManager::upload('tour_and_travels/review/', 'webp', $request->file('image'));
                }
                if ($contact) {
                    $contact = TourReviews::find($contact['id']);
                } else {
                    $contact = new TourReviews();
                }
                $contact->order_id = $request->order_id;
                $contact->user_id = $request->user_id;
                $contact->tour_id = $request->tour_id;
                $contact->status = 1;
                $contact->comment = $request->comment;
                $contact->star = $request->star;
                $contact->is_edited = 1;
                $contact->image = $images;
                $contact->save();
            } else {
                return response()->json(['status' => 0, 'message' => 'You have Already added Comment', 'recode' => 0, 'data' => [], 'errors' => []], 200);
            }
        }
        return response()->json(['status' => 1, 'message' => 'User Add Comment Successfully', 'recode' => 0, 'data' => [], 'errors' => []], 200);
    }

    public function gettourcomment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tour_id' => ['required', function ($attribute, $value, $fail) {
                if (!TourVisits::where('id', $value)->where('status', 1)->exists()) {
                    $fail('Tour ID does not exist.');
                }
            },],
        ], [
            'tour_id.required' => 'Tour Id is Empty!',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'recode' => 0, 'data' => [], 'errors' => Helpers::error_processor($validator)], 403);
        }
        if (!empty($request->user_id) && !empty($request->order_id) && !empty($request->event_id)) {
            $check = TourReviews::where('tour_id', $request->tour_id)->where('order_id', $request->order_id)->where('user_id', $request->user_id)->first();
            return response()->json(['status' => 1, 'message' => 'get Tour Comments', 'data' => $check], 200);
        } else {
            $getData = TourReviews::where(['status' => 1, 'tour_id' => $request->tour_id])->with(['userData'])->orderBy('id', 'desc')->get();
            $getData_stars = TourReviews::where(['status' => 1, 'tour_id' => $request->tour_id])->groupBy('tour_id')->avg('star');
            $getList = [];
            if (!empty($getData) && count($getData) > 0) {
                foreach ($getData as $key => $value) {
                    $getList[$key]['star'] = $value['star'];
                    $getList[$key]['created_at'] = $value['created_at'];
                    $getList[$key]['comment'] = $value['comment'];
                    $getList[$key]['user_name'] = $value['userData']['name'];
                    $getList[$key]['user_image'] = getValidImage(path: 'storage/app/public/profile/' . $value['userData']['image'], type: 'backend-product');
                    if (!empty($value['image'])) {
                        $getList[$key]['image'] =  getValidImage(path: 'storage/app/public/tour_and_travels/review/' . $value['image'], type: 'backend-product');
                    }
                }
                return response()->json(['status' => 1, 'message' => 'get Tour Comments', 'tour_star' => $getData_stars, 'recode' => count($getData), 'data' => $getList], 200);
            }
        }
        return response()->json(['status' => 0, 'message' => 'No Comment', 'recode' => 0, 'data' => []], 200);
    }


    public function SearchName(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ], [
            'name.required' => 'Product name is required!',
        ]);

        $sellers = TourVisits::where(function ($q) use ($request) {
            $q->orWhere('tour_name', 'like', "%{$request['name']}%");
            $q->orWhere('cities_name', 'like', "%{$request['name']}%");
            $q->orWhere('country_name', 'like', "%{$request['name']}%");
            $q->orWhere('state_name', 'like', "%{$request['name']}%")
                ->orWhereHas('translations', function ($query) use ($request) {
                    $query->whereIn('key', ['tour_name', 'cities_name', 'state_name', 'country_name'])
                        ->where('value', 'like', "%{$request['name']}%");
                });
        })->where('status', 1)
            ->where(function ($query) {
                $query->whereIn('use_date', [0, 2, 3, 4])
                    ->orWhere(function ($query) {
                        $query->where('use_date', 1)
                            ->where(function ($subQuery) {
                                $subQuery->whereIn('customized_type', ['', '0'])
                                    ->whereNotNull('startandend_date')
                                    ->whereRaw('? < STR_TO_DATE(SUBSTRING_INDEX(startandend_date, " - ", 1), "%Y-%m-%d")', [date('Y-m-d')]);
                            })
                            ->orWhere(function ($subQuery) {
                                $subQuery->whereIn('customized_type', [1, 2, 3]);
                            });
                    });
            })->withTourCheck()->get();
        if ($request->role == 'web') {
            $recodes = '';
            foreach ($sellers as $product) {
                $gethindi = $product ? $product->translations()->pluck('value', 'key')->toArray() : [];
                $recodes .= '<li class="list-group-item px-0 overflow-hidden">
                                <button type="submit" class="search-result-product btn p-0 m-0 search-result-product-button align-items-baseline text-start" 
                                        data-product-name="' . $product['tour_name'] . '" onclick="return $(`.search_ids`).val(`' . ($product['slug'] ?? '') . '`)">
                                    <span><i class="czi-search"></i></span>
                                    <div class="text-truncate">' . (($request['lang'] == 'hi') ? ($gethindi['tour_name'] ?? "") : $product['tour_name']) . '</div>
                                    <span class="px-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-up-left" viewBox="0 0 16 16">
                                            <path fill-rule="evenodd" d="M2 2.5a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1H3.707l10.147 10.146a.5.5 0 0 1-.708.708L3 3.707V8.5a.5.5 0 0 1-1 0z"/>
                                        </svg>
                                    </span>
                                </button>
                                
                            </li>';
            }
        } else {
            $recodes = [];
            foreach ($sellers as $ky => $product) {
                $gethindi = $product ? $product->translations()->pluck('value', 'key')->toArray() : [];
                $recodes[$ky] = ['id' => $product['id'], 'name' => $product['tour_name'], 'hi_name' => $gethindi['tour_name'] ?? ""];
            }
        }
        if (!empty($sellers) && count($sellers) > 0) {
            return response()->json(['status' => 1, 'count' => count($sellers), 'data' => $recodes], 200);
        } else {
            return response()->json(['status' => 0, 'count' => 0, 'data' => (($request->role == 'web') ? '' : [])], 400);
        }
    }

    public function TourCoupon(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => ['required', function ($attribute, $value, $fail) {
                if (!User::where('id', $value)->where('is_active', 1)->exists()) {
                    $fail('The selected user is invalid or inactive.');
                }
            },],
            'coupon_code' => ['required', function ($attribute, $value, $fail) {
                if (!Coupon::where('code', $value)->where('coupon_type', 'tour_order')->where('status', 1)->whereDate('start_date', '<=', date('Y-m-d'))->whereDate('expire_date', '>=', date('Y-m-d'))->exists()) {
                    $fail('Invalid Coupon Code.');
                }
            }],

            'amount' => 'required|numeric|min:1',
        ], [
            'user_id.required' => 'User Id is Empty!',
            'coupon_code.required' => 'Coupon Code is Empty!',
            'amount.required' => 'Amount is Empty!',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'recode' => 0, 'data' => [], 'errors' => Helpers::error_processor($validator)], 200);
        }
        $couponData = Coupon::where('code', $request->get('coupon_code'))->where('coupon_type', 'tour_order')->where('status', 1)->whereDate('start_date', '<=', date('Y-m-d'))->whereDate('expire_date', '>=', date('Y-m-d'))->first();
        $checkCoupon = TourOrder::where('coupon_id', ($couponData['id'] ?? ""))->where('amount_status', 1)->where('user_id', $request->get('user_id'))->count();
        if (($couponData['limit'] ?? 0) <= $checkCoupon) {
            return response()->json(['status' => 0, 'message' => 'The coupon code has already been used', 'recode' => 0, 'data' => []], 200);
        }
        if ($couponData['customer_id'] != 0 && $couponData['customer_id'] != $request->user_id) {
            return response()->json(['status' => 0, 'message' => 'Invalid Coupon Code', 'recode' => 0, 'data' => []], 200);
        }
        if (($couponData['min_purchase'] > $request->get('amount'))) {
            return response()->json(['status' => 0, 'message' => 'Minimum amount Rs ' . ($couponData['min_purchase']) . ' This coupon is applicable', 'recode' => 0, 'data' => []], 200);
        }
        $coupon_amount = 0;
        $final_amount = $request->get('amount');
        if ($couponData['discount_type'] == 'amount') {
            $coupon_amount = $couponData['discount'];
            $final_amount = ($final_amount - ($couponData['discount'] ?? 0));
        }
        if ($couponData['discount_type'] == 'percentage') {
            $coupon_amount =  round((($final_amount * ($couponData['discount'] ?? 0)) / 100), 2);
            if ($couponData['max_discount'] < $coupon_amount) {
                $coupon_amount =  $couponData['max_discount'];
            }
            $final_amount =  ($final_amount - $coupon_amount);
        }


        return response()->json(['status' => 1, 'message' => 'Successfully Coupon Apply', 'recode' => 1, 'data' => ['coupon_id' => $couponData['id'], 'coupon_amount' => $coupon_amount, 'final_amount' => $final_amount]], 200);
    }

    public function TourGetDistance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tour_id' => ['required', function ($attribute, $value, $fail) use ($request) {
                if (TourVisits::where('id', $value)->whereNull('lat')->whereNull('long')->exists()) {
                    $fail('The selected Tour ID is invalid or already has latitude and longitude set.');
                }
            },],
            'lat' => "required",
            'long' => "required",
        ], [
            'tour_id.required' => 'Tour Id is Empty!',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'recode' => 0, 'data' => [], 'errors' => Helpers::error_processor($validator)], 200);
        }
        $getTour = TourVisits::where('id', $request->tour_id)->first();
        $ExChargeAmount = [];
        if (!empty($request->cab_id)) {
            $bus_list = json_decode($getTour['cab_list_price'] ?? '[]', true);
            foreach ($bus_list as $cab) {
                if ($cab['cab_id'] == $request->cab_id) {
                    $ExChargeAmount = $cab['exprice'] ?? [];
                    break;
                }
            }
        }
        $unit = 'k';
        $earthRadiusKm = 6371;
        $lat1 = deg2rad($getTour['lat']);
        $long1 = deg2rad($getTour['long']);
        $lat2 = deg2rad($request->lat);
        $long2 = deg2rad($request->long);

        $dLat = $lat2 - $lat1;
        $dLon = $long2 - $long1;

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos($lat1) * cos($lat2) *
            sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadiusKm * $c;

        // if ($unit == 'M') {
        //     return $distance * 0.621371;
        // }
        $totalAmount = 0;
        $matched = false;
        if ($ExChargeAmount && is_array($ExChargeAmount)) {
            foreach ($ExChargeAmount as $range) {
                if ($distance >= $range['start'] && $distance <= $range['end']) {
                    $totalAmount = $range['charge'] + $range['driver'];
                    $matched = true;
                    break;
                }
            }
            if (!$matched) {
                $last = end($ExChargeAmount);
                $totalAmount = $last['charge'] + $last['driver'];
            }
        }

        return response()->json(['status' => 1, 'message' => 'Successfully', 'recode' => 1, 'data' => round($distance, 2), 'ExChargeAmount' => $totalAmount], 200);
    }

    public function TourPending(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cab_assign' => ['required', function ($attribute, $value, $fail) {
                if (!TourAndTravel::where('id', $value)->where('is_approve', 1)->where('status', 1)->exists()) {
                    $fail('The selected traveller Id is invalid or inactive.');
                }
            },],
        ], [
            'cab_assign.required' => 'Cab Id is Empty!',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'recode' => 0, 'data' => [], 'errors' => Helpers::error_processor($validator)], 200);
        }
        $all_booking = \App\Models\TourOrder::whereIn('status', [1, 0])->where(['refund_status' => 0, 'pickup_status' => 0, 'amount_status' => 1, 'drop_status' => 0, 'cab_assign' => 0])
            ->where('pickup_date', '>', \Carbon\Carbon::today()->toDateString())
            ->whereHas('accept', function ($query) use ($request) {
                $query->where('tour_order_accept.status', 1)->where('traveller_id', $request['cab_assign']);
            })->withCabOrderCheck($request->cab_assign)->with(['accept'])->orderBy('id', 'desc')->get();

        if (!empty($all_booking) && count($all_booking) > 0) {
            foreach ($all_booking as $key => $value) {
                $bookingList[$key]['id'] = $value['id'];
                $bookingList[$key]['tour_id'] = $value['tour_id'];
                $bookingList[$key]['order_id'] = $value['order_id'];
                $bookingList[$key]['qty'] = $value['qty'];
                $bookingList[$key]['user_name'] = ($value['userData']['name'] ?? "");
                $bookingList[$key]['user_phone'] = ($value['userData']['phone'] ?? "");
                $bookingList[$key]['user_image'] = getValidImage(path: 'storage/app/public/profile/' . ($value['userData']['image'] ?? ""), type: 'backend-product');
                $bookingList[$key]['en_tour_name'] = ($value['Tour']['tour_name'] ?? "");
                $hindi_tour = ($value['Tour']['tour_name'] ?? "") ? (TourVisits::find($value['tour_id']))->translations()->pluck('value', 'key')->toArray() : [];
                $bookingList[$key]['hi_tour_name'] = $hindi_tour['tour_name'] ?? "";
                $bookingList[$key]['tour_image'] = getValidImage(path: 'storage/app/public/tour_and_travels/tour_visit/' . ($value['Tour']['tour_image'] ?? ""), type: 'backend-product');
                $bookingList[$key]['amount'] = (($value['amount'] ?? 0) + ($value['coupon_amount'] ?? 0));
                $bookingList[$key]['coupon_amount'] = $value['coupon_amount'];
                $bookingList[$key]['pay_amount'] = $value['amount'];
                $bookingList[$key]['amount_status'] = $value['amount_status'];
                $bookingList[$key]['transaction_id'] = $value['transaction_id'];
                $bookingList[$key]['refund_status'] = $value['refund_status'];
                $bookingList[$key]['pickup_address'] = $value['pickup_address'];
                $bookingList[$key]['pickup_date'] = $value['pickup_date'];
                $bookingList[$key]['pickup_time'] = $value['pickup_time'];
                $bookingList[$key]['booking_time'] = $value['created_at'];

                $getdata = TourOrder::where('id', $value['id'])->with(['Tour', 'company', 'Driver', 'CabsManage'])->withDriverInfo($value['id'])->first();

                if ($getdata) {

                    $bookingList[$key]['driver_data']  = json_decode($getdata['driver_data'] ?? '[]') ?? '';
                    $bookingList[$key]['cabs_data']  = json_decode($getdata['Cabs_data'] ?? '[]') ?? '';

                    $bookingList[$key]['tour_bookings'] = []; //$getdata['booking_package'];

                    $assign_cabs_use_allPackages = 0;
                    $assign_cabs_use_qtys = 0;
                    if (!empty($getdata['booking_package']) && json_decode($getdata['booking_package'], true)) {
                        foreach (json_decode($getdata['booking_package'], true) as $val) {
                            if ($getdata['use_date'] == 0 || ($val['type'] == 'cab' && $getdata['use_date'] == 1) || ($val['type'] != 'ex_distance' && $getdata['use_date'] == 2) || ($val['type'] != 'ex_distance' && $getdata['use_date'] == 3) || ($val['type'] != 'ex_distance' && $getdata['use_date'] == 4)) {
                                if ($val['type'] == 'cab') {
                                    $tourPackages = \App\Models\TourCab::where('id', $val['id'])->first();
                                    $images = getValidImage(path: 'storage/app/public/tour_and_travels/cab/' . ($tourPackages['image'] ?? ""), type: 'backend-product');
                                    $assign_cabs_use_allPackages = $val['id'];
                                    $assign_cabs_use_qtys = $val['qty'];
                                } elseif ($val['type'] == 'other' || $val['type'] == 'foods' || $val['type'] == 'hotel' || \Illuminate\Support\Str::startsWith($val['type'], 'other')) {
                                    $tourPackages = \App\Models\TourPackage::where('id', $val['id'])->first();
                                    $images = getValidImage(path: 'storage/app/public/tour_and_travels/package/' . ($tourPackages['image'] ?? ""), type: 'backend-product');
                                } else {
                                    $tourPackages = [];
                                }
                            }
                        }
                    }


                    if (!empty($getdata['Tour']['cab_list_price']) && json_decode($getdata['Tour']['cab_list_price'], true) && $getdata['Tour']['is_person_use'] == 0) {
                        foreach (json_decode($getdata['Tour']['cab_list_price'], true) as $p_info) {
                            if ($assign_cabs_use_allPackages == ($p_info['cab_id'] ?? 0)) {
                                $tourPackages = \App\Models\TourCab::where('id', $p_info['cab_id'])->first();
                                $bookingList[$key]['tour_bookings'][] = [
                                    'image' => getValidImage(path: 'storage/app/public/tour_and_travels/cab/' . ($tourPackages['image'] ?? ''), type: 'backend-product'),
                                    'name' =>  $tourPackages['name'] ?? "",
                                    'seats' => $tourPackages['seats'] ?? "",
                                    'qty' => $assign_cabs_use_qtys,
                                    'amount' => setCurrencySymbol(amount: usdToDefaultCurrency(amount: ((float)$p_info['price'] ?? 0) * $assign_cabs_use_qtys ?? 1), currencyCode: getCurrencyCode()),
                                ];
                            }
                        }
                    }
                    if (!empty($getdata['Tour']['package_list_price']) && json_decode($getdata['Tour']['package_list_price'], true) && $getdata['Tour']['is_person_use'] == 0) {
                        foreach (json_decode($getdata['Tour']['package_list_price'], true) as $p_info) {
                            $tourPackages = \App\Models\TourPackage::where('id', $p_info['id'])->first();
                            $bookingList[$key]['tour_bookings'][] = [
                                'image' => getValidImage(path: 'storage/app/public/tour_and_travels/package/' . ($tourPackages['image'] ?? ''), type: 'backend-product'),
                                'name' => $tourPackages['name'] ?? "",
                                'seats' => "",
                                'qty'  => $assign_cabs_use_qtys,
                                'amount' => setCurrencySymbol(amount: usdToDefaultCurrency(amount: ((float)$p_info['pprice'] ?? 0) * $assign_cabs_use_qtys ?? 1), currencyCode: getCurrencyCode()),

                            ];
                        }
                    }

                    if (!empty($getdata['booking_package']) && json_decode($getdata['booking_package'], true) && $getdata['Tour']['is_person_use'] == 1) {
                        foreach (json_decode($getdata['booking_package'], true) as $val) {
                            if ($val["type"] == "per_head") {
                                $bookingList[$key]['tour_bookings'][] = [
                                    'image' => '',
                                    'name' => "Per Head",
                                    'seats' => "",
                                    'qty'  => $val['qty'],
                                    'gst' => setCurrencySymbol(amount: usdToDefaultCurrency(amount: ((float)$val['tax_price'] ?? 0)), currencyCode: getCurrencyCode()),
                                    'amount' => setCurrencySymbol(amount: usdToDefaultCurrency(amount: ((float)$val['pprice'] ?? 0)), currencyCode: getCurrencyCode()),
                                    'total_price' => setCurrencySymbol(amount: usdToDefaultCurrency(amount: ((float)$val['total_price'] ?? 0)), currencyCode: getCurrencyCode()),

                                ];
                            } elseif ($val["type"] == "foods" || $val["type"] == "hotel") {
                                $tourPackages = \App\Models\TourPackage::where('id', $val['id'])->first();
                                $bookingList[$key]['tour_bookings'][] = [
                                    'image' => getValidImage(path: 'storage/app/public/tour_and_travels/package/' . ($tourPackages['image'] ?? ''), type: 'backend-product'),
                                    'name' => $tourPackages['name'] ?? "",
                                    'seats' => "",
                                    'qty' => $val['qty'],
                                    'gst' => setCurrencySymbol(amount: usdToDefaultCurrency(amount: ((float)$val['tax_price'] ?? 0)), currencyCode: getCurrencyCode()),
                                    'amount' => setCurrencySymbol(amount: usdToDefaultCurrency(amount: ((float)$val['pprice'] ?? 0)), currencyCode: getCurrencyCode()),
                                    'total_price' => setCurrencySymbol(amount: usdToDefaultCurrency(amount: ((float)$val['total_price'] ?? 0)), currencyCode: getCurrencyCode()),

                                ];
                            } elseif ($val["type"] == "route") {
                                $bookingList[$key]['tour_bookings'][] = [
                                    'image' => '',
                                    'name' => "Route",
                                    'seats' => ucwords(str_replace("_", " ", $val['price'])),
                                    'qty' => "",
                                    'gst' => setCurrencySymbol(amount: usdToDefaultCurrency(amount: ((float) 0)), currencyCode: getCurrencyCode()),
                                    'amount' => setCurrencySymbol(amount: usdToDefaultCurrency(amount: ((float) 0)), currencyCode: getCurrencyCode()),
                                    'total_price' => setCurrencySymbol(amount: usdToDefaultCurrency(amount: ((float) 0)), currencyCode: getCurrencyCode()),
                                ];
                            }
                        }
                    }

                    $bookingList[$key]['tour_itinerary'] = [];
                    if (isset($getdata['Tour'], $getdata['Tour']['TourPlane'], $getdata['Tour']['TourPlane'][0])) {
                        $vv = 0;
                        foreach ($getdata['Tour']['TourPlane'] as $viisit) {
                            $gethindi = $viisit ? $viisit->translations()->pluck('value', 'key')->toArray() : [];
                            $bookingList[$key]['tour_itinerary'][$vv]['id'] = $viisit['id'];
                            $bookingList[$key]['tour_itinerary'][$vv]['en_name'] = $viisit['name'];
                            $bookingList[$key]['tour_itinerary'][$vv]['hi_name'] = $gethindi['name'] ?? "";
                            $bookingList[$key]['tour_itinerary'][$vv]['en_time'] = $viisit['time'];
                            $bookingList[$key]['tour_itinerary'][$vv]['hi_time'] = $gethindi['time'] ?? '';

                            $bookingList[$key]['tour_itinerary'][$vv]['en_description'] = $viisit['description'];
                            $bookingList[$key]['tour_itinerary'][$vv]['hi_description'] = $gethindi['description'];

                            $itinerary_image_list = [];
                            if (!empty($viisit['images']) && json_decode($viisit['images'], true)) {
                                foreach (json_decode($viisit['images'], true) as $itn_va) {
                                    $itinerary_image_list[] = getValidImage(path: 'storage/app/public/tour_and_travels/tour_visit_place/' . $itn_va, type: 'backend-product');
                                }
                            }
                            $bookingList[$key]['tour_itinerary'][$vv]['image'] = $itinerary_image_list;
                            $vv++;
                        }
                    }

                    $bookingList[$key]['part_payment'] = $getdata['part_payment'];
                    $bookingList[$key]['advance_withdrawal_amount'] = $getdata['advance_withdrawal_amount'];
                    $bookingList[$key]['status'] = $getdata['status'];
                    $bookingList[$key]['refund_amount'] = $getdata['refund_amount'];
                    if (!empty($getdata['company'])) {
                        $bookingList[$key]['traveller_info'] = [
                            'image' => getValidImage(path: 'storage/app/public/tour_and_travels/doc/' . ($getdata['company']['image'] ?? ''), type: 'backend-profile'),
                            'name' => ($getdata['company']['company_name'] ?? ''),
                            'phone' => $getdata['company']['phone_no'],
                            'email' => $getdata['company']['email']
                        ];
                    } else {
                        $bookingList[$key]['traveller_info'] = null;
                    }
                }
            }
            return response()->json(['status' => 1, 'count' => count($bookingList), 'data' => $bookingList], 200);
        } else {
            return response()->json(['status' => 0, 'count' => '0', 'data' => []], 200);
        }
    }

    public function CabTourOrdercancel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cab_assign' => ['required', function ($attribute, $value, $fail) {
                if (!TourAndTravel::where('id', $value)->where('is_approve', 1)->where('status', 1)->exists()) {
                    $fail('The selected cab Id is invalid or inactive.');
                }
            },],
            'order_id' => ['required', function ($attribute, $value, $fail) {
                if (!TourOrder::where('id', $value)->where('refund_status', 0)->whereIn('status', [0, 1])->where('pickup_status', 0)->exists()) {
                    $fail('Order ID does not exist.');
                }
            },],
        ], [
            'cab_assign.required' => 'Cab Id is Empty!',
            'order_id.required' => 'Order Id is Empty!',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'recode' => 0, 'data' => [], 'errors' => Helpers::error_processor($validator)], 200);
        }

        $getData =  TourOrder::where('id', $request->order_id)->where('cab_assign', $request->cab_assign)->with(['userData'])->first();
        if ($getData) {

             $cancel_vendor_list = json_decode($getData['cancel_vendor_list'] ?? '[]', true);
            if (!is_array($cancel_vendor_list)) {
                $cancel_vendor_list = [];
            }
            $vendorId = $request->cab_assign;
            if (!in_array($vendorId, $cancel_vendor_list)) {
                $cancel_vendor_list[] = (string)$vendorId;
            }
            TourOrder::where('id', $request->order_id)->where('cab_assign', $request->cab_assign)->update(['cab_assign' => 0,'traveller_cab_id' => 0, 'traveller_driver_id' => 0, 'on_load' => 0, 'cancel_vendor_list' => json_encode($cancel_vendor_list)]);
            TourAndTravel::where('id', $request->cab_assign)->update(["cancel_order" => DB::raw('cancel_order + ' . 1)]);
            return response()->json(['status' => 1, 'message' => 'Cancel Order', 'data' => []], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Not Found Data', 'data' => []], 200);
        }
    }

    public function TourAssign(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cab_assign' => ['required', function ($attribute, $value, $fail) {
                if (!TourAndTravel::where('id', $value)->where('is_approve', 1)->where('status', 1)->exists()) {
                    $fail('The selected cab Id is invalid or inactive.');
                }
            },],
            'order_id' => ['required', function ($attribute, $value, $fail) {
                if (!TourOrder::where('id', $value)->where('cab_assign', '=', 0)->exists()) {
                    $fail('The selected Order already assigned.');
                }
            },],
        ], [
            'cab_assign.required' => 'Cab Id is Empty!',
            'order_id.required' => 'Order Id is Empty!',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'recode' => 0, 'data' => [], 'errors' => Helpers::error_processor($validator)], 200);
        }
        TourOrder::where('id', $request->order_id)->update(['cab_assign' => $request->cab_assign]);
        return response()->json(['status' => 1, 'message' => 'cab assign Successfully', 'data' => []], 200);
    }

    public function VendorTourOrderView(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => ['required', function ($attribute, $value, $fail) {
                if (!TourOrder::where('id', $value)->exists()) {
                    $fail('The selected Order id invalid.');
                }
            },],
        ], [
            'cab_assign.required' => 'Cab Id is Empty!',
            'order_id.required' => 'Order Id is Empty!',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'recode' => 0, 'data' => [], 'errors' => Helpers::error_processor($validator)], 200);
        }
        $getdata = TourOrder::where('id', $request->order_id)->with(['Tour', 'company', 'Driver', 'CabsManage'])->first();
        $bookingList = [];
        if ($getdata) {
            $bookingList['id'] = $getdata['id'];
            $bookingList['tour_id'] = $getdata['tour_id'];
            $bookingList['order_id'] = $getdata['order_id'];
            $bookingList['qty'] = $getdata['qty'];
            $bookingList['user_name'] = ($getdata['userData']['name'] ?? "");
            $bookingList['user_phone'] = ($getdata['userData']['phone'] ?? "");
            $bookingList['user_image'] = getValidImage(path: 'storage/app/public/profile/' . ($getdata['userData']['image'] ?? ""), type: 'backend-profile');
            $bookingList['en_tour_name'] = ($getdata['Tour']['tour_name'] ?? "");
            $hindi_tour = ($getdata['Tour']['tour_name'] ?? "") ? (TourVisits::find($getdata['tour_id']))->translations()->pluck('value', 'key')->toArray() : [];
            $bookingList['hi_tour_name'] = $hindi_tour['tour_name'] ?? "";
            $bookingList['tour_image'] = getValidImage(path: 'storage/app/public/tour_and_travels/tour_visit/' . ($getdata['Tour']['tour_image'] ?? ""), type: 'backend-product');
            $bookingList['tour_bookings'] = []; //$getdata['booking_package'];

            $assign_cabs_use_allPackages = 0;
            $assign_cabs_use_qtys = 0;
            if (!empty($getdata['booking_package']) && json_decode($getdata['booking_package'], true)) {
                foreach (json_decode($getdata['booking_package'], true) as $val) {
                    if ($getdata['use_date'] == 0 || ($val['type'] == 'cab' && $getdata['use_date'] == 1) || ($val['type'] != 'ex_distance' && $getdata['use_date'] == 2) || ($val['type'] != 'ex_distance' && $getdata['use_date'] == 3) || ($val['type'] != 'ex_distance' && $getdata['use_date'] == 4)) {
                        if ($val['type'] == 'cab') {
                            $tourPackages = \App\Models\TourCab::where('id', $val['id'])->first();
                            $images = getValidImage(path: 'storage/app/public/tour_and_travels/cab/' . ($tourPackages['image'] ?? ""), type: 'backend-product');
                            $assign_cabs_use_allPackages = $val['id'];
                            $assign_cabs_use_qtys = $val['qty'];
                        } elseif ($val['type'] == 'other' || $val['type'] == 'foods' || $val['type'] == 'hotel' || \Illuminate\Support\Str::startsWith($val['type'], 'other')) {
                            $tourPackages = \App\Models\TourPackage::where('id', $val['id'])->first();
                            $images = getValidImage(path: 'storage/app/public/tour_and_travels/package/' . ($tourPackages['image'] ?? ""), type: 'backend-product');
                        } else {
                            $tourPackages = [];
                        }
                    }
                }
            }


            $bookingList['tour_bookings'] = [];
            $ppi = 0;
            if (!empty($getdata['Tour']['cab_list_price']) && json_decode($getdata['Tour']['cab_list_price'], true)) {
                foreach (json_decode($getdata['Tour']['cab_list_price'], true) as $p_info) {
                    if ($assign_cabs_use_allPackages == ($p_info['cab_id'] ?? "")) {
                        $tourPackages = \App\Models\TourCab::where('id', $p_info['cab_id'])->first();
                        $bookingList['tour_bookings'][$ppi]['image'] = getValidImage(path: 'storage/app/public/tour_and_travels/cab/' . ($tourPackages['image'] ?? ''), type: 'backend-product');
                        $bookingList['tour_bookings'][$ppi]['name'] =  $tourPackages['name'] ?? "";
                        $bookingList['tour_bookings'][$ppi]['seats'] = $tourPackages['seats'] ?? "";
                        $bookingList['tour_bookings'][$ppi]['qty'] = $assign_cabs_use_qtys;
                        $bookingList['tour_bookings'][$ppi]['amount'] = setCurrencySymbol(amount: usdToDefaultCurrency(amount: ((float)$p_info['price'] ?? 0) * $assign_cabs_use_qtys ?? 1), currencyCode: getCurrencyCode());
                        $ppi++;
                    }
                }
            }
            if (!empty($getdata['Tour']['package_list_price']) && json_decode($getdata['Tour']['package_list_price'], true)) {
                foreach (json_decode($getdata['Tour']['package_list_price'], true) as $p_info) {
                    $tourPackages = \App\Models\TourPackage::where('id', $p_info['id'])->first();
                    $bookingList['tour_bookings'][$ppi]['image'] = getValidImage(path: 'storage/app/public/tour_and_travels/package/' . ($tourPackages['image'] ?? ''), type: 'backend-product');
                    $bookingList['tour_bookings'][$ppi]['name'] = $tourPackages['name'] ?? "";
                    $bookingList['tour_bookings'][$ppi]['seats'] = "";
                    $bookingList['tour_bookings'][$ppi]['qty']  = $assign_cabs_use_qtys;
                    $bookingList['tour_bookings'][$ppi]['amount'] = setCurrencySymbol(amount: usdToDefaultCurrency(amount: ((float)$p_info['pprice'] ?? 0) * $assign_cabs_use_qtys ?? 1), currencyCode: getCurrencyCode());
                    $ppi++;
                }
            }
            $bookingList['tour_itinerary'] = [];
            if (isset($getdata['Tour'], $getdata['Tour']['TourPlane'], $getdata['Tour']['TourPlane'][0])) {
                $vv = 0;
                foreach ($getdata['Tour']['TourPlane'] as $viisit) {
                    $gethindi = $viisit ? $viisit->translations()->pluck('value', 'key')->toArray() : [];
                    $bookingList['tour_itinerary'][$vv]['id'] = $viisit['id'];
                    $bookingList['tour_itinerary'][$vv]['en_name'] = $viisit['name'];
                    $bookingList['tour_itinerary'][$vv]['hi_name'] = $gethindi['name'] ?? "";
                    $bookingList['tour_itinerary'][$vv]['en_time'] = $viisit['time'];
                    $bookingList['tour_itinerary'][$vv]['hi_time'] = $gethindi['time'] ?? '';

                    $bookingList['tour_itinerary'][$vv]['en_description'] = $viisit['description'];
                    $bookingList['tour_itinerary'][$vv]['hi_description'] = $gethindi['description'];

                    $itinerary_image_list = [];
                    if (!empty($viisit['images']) && json_decode($viisit['images'], true)) {
                        foreach (json_decode($viisit['images'], true) as $itn_va) {
                            $itinerary_image_list[] = getValidImage(path: 'storage/app/public/tour_and_travels/tour_visit_place/' . $itn_va, type: 'backend-product');
                        }
                    }
                    $bookingList['tour_itinerary'][$vv]['image'] = $itinerary_image_list;
                    $vv++;
                }
            }

            $bookingList['order_id'] = $getdata['order_id'];
            $bookingList['amount'] = (($getdata['amount'] ?? 0) + ($getdata['coupon_amount'] ?? 0));
            $bookingList['coupon_amount'] = $getdata['coupon_amount'];
            $bookingList['pay_amount'] = $getdata['amount'];
            $bookingList['part_payment'] = $getdata['part_payment'];
            $bookingList['advance_withdrawal_amount'] = $getdata['advance_withdrawal_amount'];
            $bookingList['amount_status'] = $getdata['amount_status'];
            $bookingList['transaction_id'] = $getdata['transaction_id'];
            $bookingList['refund_status'] = $getdata['refund_status'];
            $bookingList['pickup_address'] = $getdata['pickup_address'];
            $bookingList['pickup_date'] = $getdata['pickup_date'];
            $bookingList['pickup_time'] = $getdata['pickup_time'];
            $bookingList['booking_time'] = $getdata['created_at'];
            $bookingList['status'] = $getdata['status'];
            $bookingList['refund_status'] = $getdata['refund_status'];
            $bookingList['refund_amount'] = $getdata['refund_amount'];
            if (!empty($getdata['company'])) {
                $bookingList['traveller_info'] = [
                    'image' => getValidImage(path: 'storage/app/public/tour_and_travels/doc/' . ($getdata['company']['image'] ?? ''), type: 'backend-profile'),
                    'name' => ($getdata['company']['company_name'] ?? ''),
                    'phone' => $getdata['company']['phone_no'],
                    'email' => $getdata['company']['email']
                ]; //$value['CabsManage'];
            } else {
                $bookingList['traveller_info'] = [];
            }
            if (!empty($getdata['CabsManage'])) {
                $bookingList['cab_info'] = [
                    'image' => getValidImage(path: 'storage/app/public/tour_and_travels/tour_traveller_cab/' . ($getdata['CabsManage']['Cabs']['image'] ?? ''), type: 'backend-product'),
                    'name' => ($getdata['CabsManage']['Cabs']['name'] ?? ''),
                    'reg_number' => $getdata['CabsManage']['reg_number'],
                    'model_number' => $getdata['CabsManage']['model_number']
                ]; //$value['CabsManage'];
            } else {
                $bookingList['cab_info'] = [];
            }
            if (!empty($getdata['Driver'])) {
                $bookingList['driver_info'] = [
                    'image' => getValidImage(path: 'storage/app/public/tour_and_travels/tour_traveller_driver/' . $getdata['Driver']['image'] ?? '', type: 'backend-profile'),
                    'name' => $getdata['Driver']['name'],
                    'phone' => $getdata['Driver']['phone']
                ];
            } else {
                $bookingList['driver_info'] = [];
            }
        }
        return response()->json(['status' => 1, 'message' => 'cab assign Successfully', 'data' => $bookingList], 200);
    }

    public function TourAssignCabDriver(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cab_assign' => ['required', function ($attribute, $value, $fail) use ($request) {
                if (!TourAndTravel::where('id', $value)->where('is_approve', 1)->where('status', 1)->exists()) {
                    $fail('The selected traveller Id is invalid or inactive.');
                }
                if (!TourOrder::where('id', $request['order_id'])->where('cab_assign', $value)->where('status', 1)->exists()) {
                    $fail('Please confirm the order first.');
                }
            },],
            'order_id' => 'required|exists:tour_order,id',
            'cab_id' => [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    $cabIds = is_string($value) ? json_decode($value, true) : $value;
                    $cabIds = is_array($cabIds) ?  $cabIds : explode(',', (string) $cabIds);

                    $currentTour = \App\Models\TourOrder::with(['Tour'])->find($request->id);
                    if (!$currentTour) return;

                    if ($currentTour['Tour']['tour_type'] == 'cities_tour') {
                        $bookingPackage = json_decode($currentTour['booking_package'], true);
                        if (is_array($bookingPackage) && $currentTour['Tour']['tour_type'] == 'cities_tour') {
                            foreach ($bookingPackage as $item) {
                                if (isset($item['type']) && ($item['type'] === 'cab' || $item['type'] === 'per_head') && isset($item['qty'])) {
                                    $qty_cities = $item['qty'];
                                    break;
                                }
                            }
                        }
                        if (($currentTour['Tour']['is_person_use'] == 0) && $qty_cities > count($cabIds)) {
                            $fail("Please select a $qty_cities Cabs.");
                        }
                    }

                    $pickupDate = $currentTour->pickup_date;
                    $days_numberdays = \App\Models\TourVisits::where('id', $currentTour->tour_id)->value('number_of_day');
                    $days_numbernight = \App\Models\TourVisits::where('id', $currentTour->tour_id)->value('number_of_night');
                    if ($days_numberdays > $days_numbernight) {
                        $days = $days_numberdays;
                    } else {
                        $days = $days_numbernight;
                    }
                    $endDate = date('Y-m-d', strtotime("+$days days", strtotime($pickupDate . ' -1 day')));

                    $currentTour->update(['drop_date' => $endDate]);

                    foreach ($cabIds as $cabId) {
                        $overlapping = \App\Models\TourOrder::where('drop_status', 0)
                            ->where('id', '!=', $request->id)
                            ->where(function ($query) use ($cabId) {
                                $query->whereJsonContains('traveller_cab_id', (string) $cabId) // JSON case
                                    ->orWhereRaw("FIND_IN_SET(?, traveller_cab_id)", [$cabId]); // Comma-separated case
                            })
                            ->where(function ($query) use ($pickupDate, $endDate) {
                                $query->whereBetween('pickup_date', [$pickupDate, $endDate])
                                    ->orWhereBetween('drop_date', [$pickupDate, $endDate])
                                    ->orWhereRaw('? BETWEEN pickup_date AND drop_date', [$pickupDate])
                                    ->orWhereRaw('? BETWEEN pickup_date AND drop_date', [$endDate]);
                            })
                            ->exists();

                        if ($overlapping) {
                            if ($currentTour['Tour']['use_date'] == 1 || $currentTour['Tour']['use_date'] == 4) {
                                $cabs_data  = \App\Models\TourCabManage::where('id', $cabId)->with(['Cabs'])->first();
                                $getcheckQty = \App\Models\TourOrder::where(function ($query) use ($cabId, $request) {
                                    $query->whereRaw("JSON_CONTAINS(traveller_cab_id, ?)", [json_encode((string) $cabId)])
                                        ->orWhere('id', $request->id);
                                }) //whereRaw("JSON_CONTAINS(traveller_cab_id, ?)", [json_encode((string) $cabId)])
                                    ->where('tour_id', $currentTour['tour_id'])
                                    ->where('pickup_status', 0)
                                    ->where('pickup_date', [$pickupDate])
                                    ->select('booking_package')
                                    ->get()
                                    ->map(function ($tourVisit) {
                                        $packages = json_decode($tourVisit->booking_package, true);
                                        $cabPackage = collect($packages)->firstWhere('type', 'cab');
                                        if (!$cabPackage) {
                                            $cabPackage = collect($packages)->firstWhere('type', 'per_head');
                                        }
                                        return $cabPackage ? (int) $cabPackage['qty'] : 0;
                                    })->sum();
                                if ($cabs_data && $cabs_data['Cabs']['seats'] > 0) {
                                    if ($getcheckQty > $cabs_data['Cabs']['seats']) {
                                        $fail("Cab Name " . $cabs_data['Cabs']['name'] . " is already booked for the given seats. " . ($getcheckQty - $cabs_data['Cabs']['seats']) . " Seats are Not Available.");
                                    }
                                } else {
                                    $fail("Cab Name " . $cabs_data['Cabs']['name'] . " is already booked for the given seats.");
                                }
                            } else {
                                $fail("Cab ID $cabId is already booked for the given date range.");
                            }
                        }
                    }
                },
            ],

            'driver_id' => [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    // Ensure value is a string before decoding
                    $driverIds = is_string($value) ? json_decode($value, true) : $value;
                    $driverIds = is_array($driverIds) ? $driverIds : explode(',', (string) $driverIds);

                    $currentTour = \App\Models\TourOrder::with(['Tour'])->find($request->id);
                    if (!$currentTour) return;

                    if ($currentTour['Tour']['tour_type'] == 'cities_tour') {
                        $bookingPackage = json_decode($currentTour['booking_package'], true);
                        if (is_array($bookingPackage) && $currentTour['Tour']['tour_type'] == 'cities_tour') {
                            foreach ($bookingPackage as $item) {
                                if (isset($item['type']) && ($item['type'] === 'cab' || $item['type'] === 'per_head') && isset($item['qty'])) {
                                    $qty_cities = $item['qty'];
                                    break;
                                }
                            }
                        }
                        if (($currentTour['Tour']['is_person_use'] == 0) && $qty_cities > count($driverIds)) {
                            $fail("Please select a $qty_cities Driver.");
                        }
                    }

                    $pickupDate = $currentTour->pickup_date;
                    $days_numberdays = \App\Models\TourVisits::where('id', $currentTour->tour_id)->value('number_of_day');
                    $days_numbernight = \App\Models\TourVisits::where('id', $currentTour->tour_id)->value('number_of_night');
                    if ($days_numberdays > $days_numbernight) {
                        $days = $days_numberdays;
                    } else {
                        $days = $days_numbernight;
                    }
                    $endDate = date('Y-m-d', strtotime("+$days days", strtotime($pickupDate . ' -1 day')));

                    foreach ($driverIds as $driverId) {
                        $overlapping = \App\Models\TourOrder::where('drop_status', 0)
                            ->where('id', '!=', $request->id)
                            ->where(function ($query) use ($driverId) {
                                $query->whereJsonContains('traveller_driver_id', (string) $driverId) // JSON case
                                    ->orWhereRaw("FIND_IN_SET(?, traveller_driver_id)", [$driverId]); // Comma-separated case
                            })
                            ->where(function ($query) use ($pickupDate, $endDate) {
                                $query->whereBetween('pickup_date', [$pickupDate, $endDate])
                                    ->orWhereBetween('drop_date', [$pickupDate, $endDate])
                                    ->orWhereRaw('? BETWEEN pickup_date AND drop_date', [$pickupDate])
                                    ->orWhereRaw('? BETWEEN pickup_date AND drop_date', [$endDate]);
                            })
                            ->exists();

                        if ($overlapping) {
                            if ($currentTour['Tour']['use_date'] == 1 || $currentTour['Tour']['use_date'] == 4) {
                                $cabIds = is_string($value) ? json_decode($value, true) : $value;
                                $cabIds = is_array($cabIds) ?  $cabIds : explode(',', (string) $cabIds);

                                $cabs_data  = \App\Models\TourCabManage::where('id', $cabIds[0])->with(['Cabs'])->first();
                                $getcheckQty = \App\Models\TourOrder::where(function ($query) use ($cabIds, $request) {
                                    $query->whereRaw("JSON_CONTAINS(traveller_cab_id, ?)", [json_encode((string) $cabIds[0])])
                                        ->orWhere('id', $request->id);
                                })
                                    //whereRaw("JSON_CONTAINS(traveller_cab_id, ?)", [json_encode((string) $cabIds[0])])
                                    ->where('tour_id', $currentTour['tour_id'])->where('pickup_status', 0)
                                    ->where('pickup_date', [$pickupDate])
                                    ->select('booking_package')
                                    ->get()
                                    ->map(function ($tourVisit) {
                                        $packages = json_decode($tourVisit->booking_package, true);
                                        $cabPackage = collect($packages)->firstWhere('type', 'cab');
                                        if (!$cabPackage) {
                                            $cabPackage = collect($packages)->firstWhere('type', 'per_head');
                                        }
                                        return $cabPackage ? (int) $cabPackage['qty'] : 0;
                                    })->sum();
                                if ($cabs_data && $cabs_data['Cabs']['seats'] > 0) {
                                    if ($getcheckQty > $cabs_data['Cabs']['seats']) {
                                        $fail("This Cab is full and Driver Already Assign.");
                                        $fail("Driver ID $driverId is already booked for the given seats. Only " . ($getcheckQty - $cabs_data['Cabs']['seats']) . " seats are available.");
                                    }
                                } else {
                                    $fail("Driver ID $driverId is already booked for the given seats. Only " . ($getcheckQty - $cabs_data['Cabs']['seats']) . " seats are available.");
                                }
                            } else {
                                $fail("Driver ID $driverId is already booked for the given date range.");
                            }
                        }
                    }
                },
            ],
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'recode' => 0, 'data' => [], 'errors' => Helpers::error_processor($validator)], 200);
        }

        $save = TourOrder::find($request->order_id);
        $save->traveller_cab_id = $request['cab_id'];
        $save->traveller_driver_id = $request['driver_id'];
        $save->save();

        $tourOrder = TourOrder::where('id', $request->order_id)->with(['Tour', 'Driver', 'CabsManage'])->withDriverInfo($request->order_id)->first();
        if ($tourOrder['driver_data'] && json_decode($tourOrder['driver_data'], true)) {
            foreach (json_decode($tourOrder['driver_data'], true) as $kk => $infos) {
                $message_data['driver_name'] = ($infos['name'] ?? '');
                $message_data['driver_number'] = "+91" . ($infos['phone'] ?? '');
                $message_data['vehicle_name'] = (json_decode($tourOrder['Cabs_data'] ?? '[]', true)[$kk]['cab_name'] ?? '');
                $message_data['vehicle_number'] = (json_decode($tourOrder['Cabs_data'] ?? '[]', true)[$kk]['reg_number'] ?? '');
                $message_data['orderId'] = ($tourOrder['order_id'] ?? '');
                $message_data['title_name'] = ($tourOrder['Tour']['tour_name'] ?? '');
                $message_data['booking_date'] = date("d M,Y", strtotime($tourOrder['pickup_date'] ?? ''));
                $message_data['time'] = ($tourOrder['pickup_time'] ?? '');
                $message_data['place_name'] = ($tourOrder['pickup_address'] ?? '');
                $message_data['tour_type'] = ucwords(str_replace('_', ' ', (($tourOrder['Tour']['tour_type'] ?? ''))));
                $message_data['final_amount'] = webCurrencyConverter(amount: (float)$tourOrder['amount'] ?? 0);
                $message_data['customer_id'] = $tourOrder['user_id'];
                if ($tourOrder['Tour']['tour_image']) {
                    $message_data['type'] = 'text-with-media';
                    $message_data['attachment'] = asset('/storage/app/public/tour_and_travels/tour_visit/' . $tourOrder['Tour']['tour_image'] ?? '');
                }
                $remain_amount = ((!empty($tourOrder['part_payment']) && $tourOrder['part_payment'] == 'part') ? $tourOrder['amount'] : 0);
                $message_data['remain_amount'] = webCurrencyConverter(amount: (float)$remain_amount ?? 0);
                \App\Utils\Helpers::whatsappMessageVendorSend('tour', 'driver_reminder', $message_data);
            }
        }

        if ($save) {
            return response()->json(['status' => 1, 'message' => "Assign cab and Driver", 'count' => '1', 'data' => []], 200);
        } else {
            return response()->json(['status' => 0, 'message' => "Assign Failed", 'count' => '0', 'data' => []], 200);
        }
    }

    public function TourAssignConfirm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cab_assign' => ['required', function ($attribute, $value, $fail) {
                if (!TourAndTravel::where('id', $value)->where('is_approve', 1)->where('status', 1)->exists()) {
                    $fail('The selected cab Id is invalid or inactive.');
                }
            },],
            "type" => "required|in:confirm,one,two",
        ], [
            'cab_assign.required' => 'Cab Id is Empty!',
            'type.required' => 'Please Choose confirm,one,two',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'recode' => 0, 'data' => [], 'errors' => Helpers::error_processor($validator)], 200);
        }

        $booking_query = TourOrder::where('cab_assign', $request->cab_assign)->where('amount_status', 1)->whereIn('refund_status', [0, 3]);
        if ($request->type == "confirm") {
            $booking_query->where('pickup_status', 0)->where('drop_status', 0);
        } elseif ($request->type == "one") {
            $booking_query->where('pickup_status', 1)->where('drop_status', 0);
        } elseif ($request->type == "two") {
            $booking_query->where('pickup_status', 1)->where('drop_status', 1);
        }
        $all_booking = $booking_query->with(['Tour', 'company', "userData", 'Driver', 'CabsManage'])->orderBy('id', 'desc')->get();
        if (!empty($all_booking) && count($all_booking) > 0) {
            foreach ($all_booking as $key => $value) {
                $bookingList[$key]['id'] = $value['id'];
                $bookingList[$key]['tour_id'] = $value['tour_id'];
                $bookingList[$key]['order_id'] = $value['order_id'];
                $bookingList[$key]['qty'] = $value['qty'];
                $bookingList[$key]['user_name'] = $value['userData']['name'] ?? "";
                $bookingList[$key]['user_phone'] = $value['userData']['phone'] ?? "";
                $bookingList[$key]['user_image'] = getValidImage(path: 'storage/app/public/profile/' . ($value['userData']['image'] ?? ""), type: 'backend-product');
                $bookingList[$key]['en_tour_name'] = ($value['Tour']['tour_name'] ?? "");
                $hindi_tour = ($value['Tour']['tour_name'] ?? "") ? (TourVisits::find($value['tour_id']))->translations()->pluck('value', 'key')->toArray() : [];
                $bookingList[$key]['hi_tour_name'] = $hindi_tour['tour_name'] ?? "";
                $bookingList[$key]['tour_image'] = getValidImage(path: 'storage/app/public/tour_and_travels/tour_visit/' . ($value['Tour']['tour_image'] ?? ""), type: 'backend-product');
                $bookingList[$key]['order_id'] = $value['order_id'];
                $bookingList[$key]['amount'] = (($value['amount'] ?? 0) + ($value['coupon_amount'] ?? 0));
                $bookingList[$key]['coupon_amount'] = $value['coupon_amount'];
                $bookingList[$key]['pay_amount'] = $value['amount'];
                $bookingList[$key]['amount_status'] = $value['amount_status'];
                $bookingList[$key]['transaction_id'] = $value['transaction_id'];
                $bookingList[$key]['refund_status'] = $value['refund_status'];
                $bookingList[$key]['pickup_address'] = $value['pickup_address'];
                $bookingList[$key]['pickup_date'] = $value['pickup_date'];
                $bookingList[$key]['pickup_time'] = $value['pickup_time'];
                $bookingList[$key]['booking_time'] = $value['created_at'];

                $bookingList[$key]['tour_bookings'] = [];

                $assign_cabs_use_allPackages = 0;
                $assign_cabs_use_qtys = 0;
                if (!empty($value['booking_package']) && json_decode($value['booking_package'], true)) {
                    foreach (json_decode($value['booking_package'], true) as $val) {
                        if ($value['use_date'] == 0 || ($val['type'] == 'cab' && $value['use_date'] == 1) || ($val['type'] != 'ex_distance' && $value['use_date'] == 2) || ($val['type'] != 'ex_distance' && $value['use_date'] == 3) || ($val['type'] != 'ex_distance' && $value['use_date'] == 4)) {
                            if ($val['type'] == 'cab') {
                                $tourPackages = \App\Models\TourCab::where('id', $val['id'])->first();
                                $images = getValidImage(path: 'storage/app/public/tour_and_travels/cab/' . ($tourPackages['image'] ?? ""), type: 'backend-product');
                                $assign_cabs_use_allPackages = $val['id'];
                                $assign_cabs_use_qtys = $val['qty'];
                            } elseif ($val['type'] == 'other' || $val['type'] == 'foods' || $val['type'] == 'hotel' || \Illuminate\Support\Str::startsWith($val['type'], 'other')) {
                                $tourPackages = \App\Models\TourPackage::where('id', $val['id'])->first();
                                $images = getValidImage(path: 'storage/app/public/tour_and_travels/package/' . ($tourPackages['image'] ?? ""), type: 'backend-product');
                            } else {
                                $tourPackages = [];
                            }
                        }
                    }
                }


                $bookingList[$key]['tour_bookings'] = [];
                $ppi = 0;
                if (!empty($value['Tour']['cab_list_price']) && json_decode($value['Tour']['cab_list_price'], true)) {
                    foreach (json_decode($value['Tour']['cab_list_price'], true) as $p_info) {
                        if ($assign_cabs_use_allPackages == ($p_info['cab_id'] ?? "")) {
                            $tourPackages = \App\Models\TourCab::where('id', $p_info['cab_id'])->first();
                            $bookingList[$key]['tour_bookings'][$ppi]['image'] = getValidImage(path: 'storage/app/public/tour_and_travels/cab/' . ($tourPackages['image'] ?? ''), type: 'backend-product');
                            $bookingList[$key]['tour_bookings'][$ppi]['name'] =  $tourPackages['name'] ?? "";
                            $bookingList[$key]['tour_bookings'][$ppi]['seats'] = $tourPackages['seats'] ?? "";
                            $bookingList[$key]['tour_bookings'][$ppi]['qty'] = $assign_cabs_use_qtys;
                            $bookingList[$key]['tour_bookings'][$ppi]['amount'] = setCurrencySymbol(amount: usdToDefaultCurrency(amount: ((float)$p_info['price'] ?? 0) * $assign_cabs_use_qtys ?? 1), currencyCode: getCurrencyCode());
                            $ppi++;
                        }
                    }
                }
                if (!empty($value['Tour']['package_list_price']) && json_decode($value['Tour']['package_list_price'], true)) {
                    foreach (json_decode($value['Tour']['package_list_price'], true) as $p_info) {
                        $tourPackages = \App\Models\TourPackage::where('id', $p_info['id'])->first();
                        $bookingList[$key]['tour_bookings'][$ppi]['image'] = getValidImage(path: 'storage/app/public/tour_and_travels/package/' . ($tourPackages['image'] ?? ''), type: 'backend-product');
                        $bookingList[$key]['tour_bookings'][$ppi]['name'] = $tourPackages['name'] ?? "";
                        $bookingList[$key]['tour_bookings'][$ppi]['seats'] = "";
                        $bookingList[$key]['tour_bookings'][$ppi]['qty']  = $assign_cabs_use_qtys;
                        $bookingList[$key]['tour_bookings'][$ppi]['amount'] = setCurrencySymbol(amount: usdToDefaultCurrency(amount: ((float)$p_info['pprice'] ?? 0) * $assign_cabs_use_qtys ?? 1), currencyCode: getCurrencyCode());
                        $ppi++;
                    }
                }
                $bookingList[$key]['tour_itinerary'] = [];
                if (isset($value['Tour'], $value['Tour']['TourPlane'], $value['Tour']['TourPlane'][0])) {
                    $vv = 0;
                    foreach ($value['Tour']['TourPlane'] as $viisit) {
                        $gethindi = $viisit ? $viisit->translations()->pluck('value', 'key')->toArray() : [];
                        $bookingList[$key]['tour_itinerary'][$vv]['id'] = $viisit['id'];
                        $bookingList[$key]['tour_itinerary'][$vv]['en_name'] = $viisit['name'];
                        $bookingList[$key]['tour_itinerary'][$vv]['hi_name'] = $gethindi['name'] ?? "";
                        $bookingList[$key]['tour_itinerary'][$vv]['en_time'] = $viisit['time'];
                        $bookingList[$key]['tour_itinerary'][$vv]['hi_time'] = $gethindi['time'] ?? "";

                        $bookingList[$key]['tour_itinerary'][$vv]['en_description'] = $viisit['description'];
                        $bookingList[$key]['tour_itinerary'][$vv]['hi_description'] = $gethindi['description'] ?? "";

                        $itinerary_image_list = [];
                        if (!empty($viisit['images']) && json_decode($viisit['images'], true)) {
                            foreach (json_decode($viisit['images'], true) as $itn_va) {
                                $itinerary_image_list[] = getValidImage(path: 'storage/app/public/tour_and_travels/tour_visit_place/' . $itn_va, type: 'backend-product');
                            }
                        }
                        $bookingList[$key]['tour_itinerary'][$vv]['image'] = $itinerary_image_list;
                        $vv++;
                    }
                }

                $bookingList[$key]['order_id'] = $value['order_id'];
                $bookingList[$key]['amount'] = (($value['amount'] ?? 0) + ($value['coupon_amount'] ?? 0));
                $bookingList[$key]['coupon_amount'] = $value['coupon_amount'];
                $bookingList[$key]['pay_amount'] = $value['amount'];
                $bookingList[$key]['part_payment'] = $value['part_payment'];
                $bookingList[$key]['advance_withdrawal_amount'] = $value['advance_withdrawal_amount'];
                $bookingList[$key]['amount_status'] = $value['amount_status'];
                $bookingList[$key]['transaction_id'] = $value['transaction_id'];

                $total_amounts = (((float)$value['amount'] ?? 0) + ((float)$value['coupon_amount'] ?? 0));
                if ($value['part_payment'] == 'part') {
                    $total_amounts += ((float)$value['amount'] ?? 0);
                }
                $bookingList[$key]['total_amount'] = $total_amounts;
                $bookingList[$key]['remaining_amount'] = (($value['part_payment'] == 'part') ? $value['amount'] : 0);
                $bookingList[$key]['paid_amount'] = (((float)$value['amount'] ?? 0) + ((float)$value['coupon_amount'] ?? 0));


                $bookingList[$key]['pickup_address'] = $value['pickup_address'];
                $bookingList[$key]['pickup_date'] = $value['pickup_date'];
                $bookingList[$key]['pickup_time'] = $value['pickup_time'];
                $bookingList[$key]['booking_time'] = $value['created_at'];
                $bookingList[$key]['status'] = $value['status'];
                $bookingList[$key]['refund_status'] = $value['refund_status'] ?? 0;
                $bookingList[$key]['refund_amount'] = $value['refund_amount'] ?? 0;
                if (!empty($value['company'])) {
                    $bookingList[$key]['traveller_info'] = [
                        'image' => getValidImage(path: 'storage/app/public/tour_and_travels/doc/' . ($value['company']['image'] ?? ''), type: 'backend-profile'),
                        'name' => ($value['company']['company_name'] ?? ''),
                        'phone' => $value['company']['phone_no'],
                        'email' => $value['company']['email']
                    ];
                } else {
                    $bookingList[$key]['traveller_info'] = null;
                }

                $getCabDatas = TourOrder::where('cab_assign', $request->cab_assign)->withDriverInfo($value['id'])->first();
                $bookingList[$key]['driver_data']  = json_decode($getCabDatas['driver_data'] ?? '[]') ?? '';
                $bookingList[$key]['cabs_data']  = json_decode($getCabDatas['Cabs_data'] ?? '[]') ?? '';
            }
            return response()->json(['status' => 1, 'count' => count($bookingList), 'data' => $bookingList], 200);
        } else {
            return response()->json(['status' => 0, 'count' => '0', 'data' => []], 200);
        }
    }


    public function TourCabView(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cab_assign' => ['required', function ($attribute, $value, $fail) {
                if (!TourAndTravel::where('id', $value)->where('is_approve', 1)->where('status', 1)->exists()) {
                    $fail('The selected cab Id is invalid or inactive.');
                }
            },],
            "order_id" => "required",
        ], [
            'cab_assign.required' => 'Cab Id is Empty!',
            'order_id.required' => 'Order Id is Empty!',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'recode' => 0, 'data' => [], 'errors' => Helpers::error_processor($validator)], 200);
        }
        $getData =  TourOrder::where('id', $request->order_id)->where('cab_assign', $request->cab_assign)->with(['userData', 'CabsManage', 'Driver'])->first();
        $bookingList = [];
        if (!empty($getData)) {
            $bookingList['id'] = $getData['id'];
            $bookingList['tour_id'] = $getData['tour_id'];
            $bookingList['order_id'] = $getData['order_id'];
            $bookingList['qty'] = $getData['qty'];
            $bookingList['user_name'] = $getData['userData']['name'] ?? "";
            $bookingList['user_phone'] = $getData['userData']['phone'] ?? "";
            $bookingList['user_image'] = getValidImage(path: 'storage/app/public/profile/' . ($getData['userData']['image'] ?? ""), type: 'backend-product');
            $bookingList['en_tour_name'] = ($getData['Tour']['tour_name'] ?? "");
            $hindi_tour = ($getData['Tour']['tour_name'] ?? "") ? (TourVisits::find($getData['tour_id']))->translations()->pluck('value', 'key')->toArray() : [];
            $bookingList['hi_tour_name'] = $hindi_tour['tour_name'] ?? "";
            $bookingList['tour_image'] = getValidImage(path: 'storage/app/public/tour_and_travels/tour_visit/' . ($getData['Tour']['tour_image'] ?? ""), type: 'backend-product');
            $bookingList['order_id'] = $getData['order_id'];
            $bookingList['amount'] = (($getData['amount'] ?? 0) + ($getData['coupon_amount'] ?? 0));
            $bookingList['coupon_amount'] = $getData['coupon_amount'];
            $bookingList['pay_amount'] = $getData['amount'];
            $bookingList['amount_status'] = $getData['amount_status'];
            $bookingList['transaction_id'] = $getData['transaction_id'];
            $bookingList['refund_status'] = $getData['refund_status'];
            $bookingList['pickup_address'] = $getData['pickup_address'];
            $bookingList['pickup_date'] = $getData['pickup_date'];
            $bookingList['pickup_time'] = $getData['pickup_time'];
            $bookingList['booking_time'] = $getData['created_at'];
            if (!empty($getData['CabsManage'])) {
                $bookingList['cab_info'] = ['name' => ($getData['CabsManage']['Cabs']['name'] ?? ''), 'reg_number' => $getData['CabsManage']['reg_number'], 'model_number' => $getData['CabsManage']['model_number']]; //$value['CabsManage'];
            } else {
                $bookingList['cab_info'] = [];
            }
            if (!empty($getData['Driver'])) {
                $bookingList['driver_info'] = ['name' => $getData['Driver']['name'], 'phone' => $getData['Driver']['phone']];
            } else {
                $bookingList['driver_info'] = [];
            }
            return response()->json(['status' => 1, 'message' => 'Get Recode', 'data' => $bookingList], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Not Found Data', 'data' => []], 200);
        }
    }

    public function TourCabOtpVerify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cab_assign' => ['required', function ($attribute, $value, $fail) use ($request) {
                if (!TourAndTravel::where('id', $value)->where('is_approve', 1)->where('status', 1)->exists()) {
                    $fail('The selected traveller Id is invalid or inactive.');
                }
                if (!TourOrder::where('id', $request['order_id'])->whereRaw("NOT JSON_CONTAINS(traveller_cab_id, '0')")->where('status', 1)->exists()) {
                    $fail('Please provide driver and cab.');
                }
            },],
            "order_id" => "required",
            "otp" => "required",
            "type" => "required|in:one,two",
        ], [
            'cab_assign.required' => 'Cab Id is Empty!',
            'type.required' => 'Please Choose one,two',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'recode' => 0, 'data' => [], 'errors' => Helpers::error_processor($validator)], 200);
        }

        $getData =  TourOrder::where('id', $request->order_id)->where('cab_assign', $request->cab_assign)->with(['userData'])->first();
        if (!empty($getData)) {
            $deviceToken = $getData['userData']['cm_firebase_token'] ?? '';
            if ($request->type == 'one' && $request->otp == $getData['pickup_otp']) {
                TourOrder::where('id', $request->order_id)->where('cab_assign', $request->cab_assign)->update(['pickup_status' => 1]);
            } elseif ($request->type == 'two' && $request->otp == $getData['drop_opt'] && $getData['pickup_status'] == 1 && $getData['drop_status'] == 0) {
                TourOrder::where('id', $request->order_id)->where('cab_assign', $request->cab_assign)->update(['drop_status' => 1]);
                TourAndTravel::where('id', $request->cab_assign)
                    ->update([
                        'wallet_amount' => \Illuminate\Support\Facades\DB::raw('wallet_amount + ' . ($getData['final_amount'] - $getData['advance_withdrawal_amount'] ?? 0)),
                        'gst_amount' => \Illuminate\Support\Facades\DB::raw('gst_amount + ' . $getData['gst_amount']),
                        'admin_commission' => \Illuminate\Support\Facades\DB::raw('admin_commission + ' . $getData['admin_commission']),
                    ]);

                $tourOrder = TourOrder::where('id', $request->order_id)->first();
                $message_data['orderId'] = ($tourOrder['order_id'] ?? '');
                $message_data['title_name'] = ($tourOrder['Tour']['tour_name'] ?? '');
                $message_data['booking_date'] = date("d M,Y", strtotime($tourOrder['pickup_date'] ?? ''));
                $message_data['time'] = ($tourOrder['pickup_time'] ?? '');
                $message_data['place_name'] = ($tourOrder['pickup_address'] ?? '');
                $message_data['tour_type'] = ucwords(str_replace('_', ' ', (($tourOrder['Tour']['tour_type'] ?? ''))));
                $message_data['final_amount'] = webCurrencyConverter(amount: (float)$tourOrder['amount'] ?? 0);
                $message_data['customer_id'] = $tourOrder['user_id'];
                if ($tourOrder['Tour']['tour_image']) {
                    $message_data['type'] = 'text-with-media';
                    $message_data['attachment'] = asset('/storage/app/public/tour_and_travels/tour_visit/' . $tourOrder['Tour']['tour_image'] ?? '');
                }
                $remain_amount = ((!empty($tourOrder['part_payment']) && $tourOrder['part_payment'] == 'part') ? $tourOrder['amount'] : 0);
                $message_data['remain_amount'] = webCurrencyConverter(amount: (float)$remain_amount ?? 0);
                Helpers::whatsappMessage('tour', 'Completed', $message_data);

                $getOld_pending_req = TourAndTravel::where('id', $request->cab_assign)->first();
                $withdrawal  =  new \App\Models\WithdrawalAmountHistory();
                $withdrawal->type = "tour_order";
                $withdrawal->vendor_id = $tourOrder['cab_assign'];
                $withdrawal->ex_id = ($request->order_id ?? "");
                $withdrawal->old_wallet_amount = $getOld_pending_req['wallet_amount'] ?? 0;
                $withdrawal->req_amount = $tourOrder['amount'] ?? 0;
                $withdrawal->save();
            } else {
                return response()->json(['status' => 0, 'message' => (($getData['drop_status'] == 1) ? "Invalid OTP" : 'Already close Booking'), 'data' => []], 200);
            }
            return response()->json(['status' => 1, 'message' => 'OTP successfully verified', 'data' => []], 200);
        }
        return response()->json(['status' => 0, 'message' => 'Not Found Data', 'data' => []], 200);
    }


    public function CabStoreFcmToken() {}

    public function TourCabOtpSend(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cab_assign' => ['required', function ($attribute, $value, $fail) {
                if (!TourAndTravel::where('id', $value)->where('is_approve', 1)->where('status', 1)->exists()) {
                    $fail('The selected cab Id is invalid or inactive.');
                }
            },],
            "order_id" => "required",
            "type" => "required|in:one,two",
        ], [
            'cab_assign.required' => 'Cab Id is Empty!',
            'type.required' => 'Please Choose one,two',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'recode' => 0, 'data' => [], 'errors' => Helpers::error_processor($validator)], 200);
        }

        $getData =  TourOrder::where('id', $request->order_id)->where('cab_assign', $request->cab_assign)->with(['userData'])->first();
        if (!empty($getData)) {

            $deviceToken = $getData['userData']['cm_firebase_token'] ?? '';
            $message_data['customer_id'] = $getData['userData']['id'];
            if ($request->type == 'one') {
                $title = 'PICKUP OTP';
                $message =  "Your ride has arrived! 🚗 Share your Pickup OTP " . $getData['pickup_otp'] . " with the captain to start your journey.Please do not share this OTP with anyone else for your safety, Happy Journey !";
                $message_data['otp'] = $getData['pickup_otp'];
                \App\Utils\Helpers::whatsappMessage('tour', 'pickup otp', $message_data);
            } else {
                $title = 'DROP OTP';
                $message = "You’ve reached your destination! 🎉 Share your Drop OTP " . $getData['drop_opt'] . " with the captain to confirm the trip. Thank you for riding with us! Please do not share this OTP with anyone else for your safety.";
                $message_data['otp'] = $getData['drop_opt'];
                \App\Utils\Helpers::whatsappMessage('tour', 'drop otp', $message_data);
            }
            $web_config  = \App\Models\BusinessSetting::where('type', 'company_fav_icon')->first();
            $data = [
                'title' => $title,
                "description" => $message,
                "image" => theme_asset(path: 'storage/app/public/company') . '/' . $web_config['value'],
                'order_id' => 0,
                "type" => "order",
            ];
            $response = \App\Utils\Helpers::send_push_notif_to_device1($deviceToken, $data);
            return response()->json(['status' => true, 'message' => 'Send Otp', 'data' => $data], 200);
        }
        return response()->json(['status' => false, 'message' => 'Not Found Data', 'data' => []], 200);
    }


    public function CabProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cab_assign' => ['required', function ($attribute, $value, $fail) {
                if (!TourAndTravel::where('id', $value)->where('is_approve', 1)->where('status', 1)->exists()) {
                    $fail('The selected cab Id is invalid or inactive.');
                }
            },],
        ], [
            'cab_assign.required' => 'Cab Id is Empty!',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'recode' => 0, 'data' => [], 'errors' => Helpers::error_processor($validator)], 200);
        }
        $user = TourAndTravel::where(['id' => $request->cab_assign])->first();
        if (!empty($user)) {
            $user['gst_image'] = getValidImage(path: 'storage/app/public/tour_and_travels/doc/' . $user['gst_image'], type: 'backend-product');
            $user['pan_card_image'] = getValidImage(path: 'storage/app/public/tour_and_travels/doc/' . $user['pan_card_image'], type: 'backend-product');
            $user['aadhaar_card_image'] = getValidImage(path: 'storage/app/public/tour_and_travels/doc/' . $user['aadhaar_card_image'], type: 'backend-product');
            $user['address_proof_image'] = getValidImage(path: 'storage/app/public/tour_and_travels/doc/' . $user['address_proof_image'], type: 'backend-product');
            $user['image'] = getValidImage(path: 'storage/app/public/tour_and_travels/doc/' . $user['image'], type: 'backend-product');

            return response()->json(['status' => 1, 'message' => 'Company Information', 'data' => $user], 200);
        }
        return response()->json(['status' => 0, 'message' => 'Not Found Data', 'data' => []], 200);
    }

    public function CabProfileUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cab_assign' => ['required', function ($attribute, $value, $fail) {
                if (!TourAndTravel::where('id', $value)->where('is_approve', 1)->where('status', 1)->exists()) {
                    $fail('The selected cab Id is invalid or inactive.');
                }
            },],
            'owner_name' => "required",
            "company_name" => "required",
            "address" => "required",
            "web_site_link" => "required",
            "services" => "required",
            "area_of_operation" => "required",
            "person_address" => "required",
            "person_name" => "required",
            "bank_holder_name" => "required",
            "bank_name" => "required",
            "bank_branch" => "required",
            "ifsc_code" => "required",
            "account_number" => "required",
            'gst_image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'pan_card_image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'aadhaar_card_image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'address_proof_image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ], [
            'cab_assign.required' => 'Cab Id is Empty!',
            "person_name.required" => "Person name filed is Empty",
            "bank_holder_name.required" => "Bank Holder name filed is Empty",
            "bank_name.required" => "Bank name filed is Empty",
            "bank_branch.required" => "Bank branch name filed is Empty",
            "ifsc_code.required" => "ifsc code filed is Empty",
            "account_number.required" => "account number filed is Empty"
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'recode' => 0, 'data' => [], 'errors' => Helpers::error_processor($validator)], 200);
        }
        $user = TourAndTravel::where(['id' => $request->cab_assign])->first();
        $user->owner_name = $request->owner_name;
        $user->company_name = $request->company_name;
        $user->address = $request->address;
        $user->web_site_link = $request->web_site_link;
        $user->services = $request->services;
        $user->area_of_operation = $request->area_of_operation;
        $user->person_name = $request->person_name;
        $user->person_address = $request->person_address;
        $user->bank_holder_name = $request->bank_holder_name;
        $user->bank_name = $request->bank_name;
        $user->bank_branch = $request->bank_branch;
        $user->ifsc_code = $request->ifsc_code;
        $user->account_number = $request->account_number;
        if ($request->file('gst_image')) {
            if (!empty($user->gst_image) && \Illuminate\Support\Facades\Storage::exists('tour_and_travels/doc/' . $user->gst_image)) {
                \Illuminate\Support\Facades\Storage::delete('tour_and_travels/doc/' . $user->gst_image);
            }
            $user->gst_image =  ImageManager::upload('tour_and_travels/doc/', 'png', $request['gst_image']);
        }
        if ($request->file('pan_card_image')) {
            if (!empty($user->pan_card_image) && \Illuminate\Support\Facades\Storage::exists('tour_and_travels/doc/' . $user->pan_card_image)) {
                \Illuminate\Support\Facades\Storage::delete('tour_and_travels/doc/' . $user->pan_card_image);
            }
            $user->pan_card_image =  ImageManager::upload('tour_and_travels/doc/', 'png', $request['pan_card_image']);
        }
        if ($request->file('aadhaar_card_image')) {
            if (!empty($user->aadhaar_card_image) && \Illuminate\Support\Facades\Storage::exists('tour_and_travels/doc/' . $user->aadhaar_card_image)) {
                \Illuminate\Support\Facades\Storage::delete('tour_and_travels/doc/' . $user->aadhaar_card_image);
            }
            $user->aadhaar_card_image =  ImageManager::upload('tour_and_travels/doc/', 'png', $request['aadhaar_card_image']);
        }
        if ($request->file('address_proof_image')) {
            if (!empty($user->address_proof_image) && \Illuminate\Support\Facades\Storage::exists('tour_and_travels/doc/' . $user->address_proof_image)) {
                \Illuminate\Support\Facades\Storage::delete('tour_and_travels/doc/' . $user->address_proof_image);
            }
            $user->address_proof_image =  ImageManager::upload('tour_and_travels/doc/', 'png', $request['address_proof_image']);
        }
        if ($request->file('image')) {
            if (!empty($user->image) && \Illuminate\Support\Facades\Storage::exists('tour_and_travels/doc/' . $user->image)) {
                \Illuminate\Support\Facades\Storage::delete('tour_and_travels/doc/' . $user->image);
            }
            $user->image =  ImageManager::upload('tour_and_travels/doc/', 'png', $request['image']);
        }
        $user->save();
        return response()->json(['status' => 1, 'message' => 'update Successfully', 'data' => []], 200);
    }



    public function CabInactiveUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cab_assign' => ['required', function ($attribute, $value, $fail) {
                if (!TourAndTravel::where('id', $value)->where('is_approve', 0)->whereIn('status', [1, 0])->exists()) {
                    $fail('The selected cab Id is invalid or inactive.');
                }
            },],
            'owner_name' => 'required',
            'company_name' => 'required',
            'phone_no' => 'required',
            'email' => 'required|email',
            'address' => 'required',
            'web_site_link' => 'required',
            'services' => 'required',
            'area_of_operation' => 'required',
            'person_name' => 'required',
            'person_phone' => 'required',
            'person_email' => 'required|email',
            'person_address' => 'required',
            'bank_holder_name' => 'required',
            'bank_name' => 'required',
            'bank_branch' => 'required',
            'ifsc_code' => 'required',
            'account_number' => 'required',
            'gst_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'pan_card_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'aadhaar_card_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'address_proof_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ], [
            'cab_assign.required' => 'Cab Id is Empty!',
            'owner_name.required' => 'Owner Name is Empty!',
            'company_name.required' => 'Company Name is Empty!',
            'phone_no.required' => 'phone No is Empty!',
            'email.required' => 'Email is Empty!',
            'address.required' => 'Full Address is Empty!',
            'web_site_link.required' => 'web site Url is Empty!',
            'services.required' => 'services is Empty!',
            'area_of_operation.required' => 'area of operation is Empty!',
            "person_name.required" => "Person name filed is Empty",
            "person_phone.required" => "Person phone filed is Empty",
            "person_email.required" => "Person Email filed is Empty",
            "person_address.required" => "Person Address filed is Empty",
            "bank_holder_name.required" => "Bank Holder name filed is Empty",
            "bank_name.required" => "Bank name filed is Empty",
            "bank_branch.required" => "Bank branch name filed is Empty",
            "ifsc_code.required" => "ifsc code filed is Empty",
            "account_number.required" => "account number filed is Empty"
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'recode' => 0, 'data' => [], 'errors' => Helpers::error_processor($validator)], 200);
        }
        $user = TourAndTravel::where(['id' => $request->cab_assign])->first();
        $user->id = $request->cab_assign;
        $user->owner_name = $request->owner_name;
        $user->company_name = $request->company_name;
        $user->phone_no = $request->phone_no;
        $user->email = $request->email;
        $user->address = $request->address;
        $user->web_site_link = $request->web_site_link;
        $user->services = $request->services;
        $user->area_of_operation = $request->area_of_operation;
        $user->person_name = $request->person_name;
        $user->person_phone = $request->person_phone;
        $user->person_email = $request->person_email;
        $user->person_address = $request->person_address;
        $user->bank_holder_name = $request->bank_holder_name;
        $user->bank_name = $request->bank_name;
        $user->bank_branch = $request->bank_branch;
        $user->ifsc_code = $request->ifsc_code;
        $user->account_number = $request->account_number;
        $user->gst_image =  (($request->file('gst_image')) ? ImageManager::upload('tour_and_travels/doc/', 'png', $request['gst_image']) : "");
        $user->pan_card_image = (($request->file('pan_card_image')) ? ImageManager::upload('tour_and_travels/doc/', 'png', $request['pan_card_image']) : "");
        $user->aadhaar_card_image = (($request->file('aadhaar_card_image')) ? ImageManager::upload('tour_and_travels/doc/', 'png', $request['aadhaar_card_image']) : "");
        $user->address_proof_image = (($request->file('address_proof_image')) ? ImageManager::upload('tour_and_travels/doc/', 'png', $request['address_proof_image']) : "");
        $user->image = (($request->file('image')) ? ImageManager::upload('tour_and_travels/doc/', 'png', $request['image']) : "");

        $user->save();
        return response()->json(['status' => 1, 'message' => 'update Successfully', 'data' => []], 200);
    }

    public function TravellerAddCab(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "cab_assign" => ['required', function ($attribute, $value, $fail) {
                if (!TourAndTravel::where('id', $value)->where('is_approve', 1)->where('status', 1)->exists()) {
                    $fail('The selected cab Id is invalid or inactive.');
                }
            },],
            'cab_id' => 'required|integer|exists:tour_cab,id',
            'reg_number' => "required|string|max:15|regex:/^[A-Za-z0-9 ]+$/|unique:tour_traveller_cabs,reg_number",
            'model_number' => 'required|string|max:50',
            'image' => "required|image|mimes:jpeg,png,jpg,gif"
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'recode' => 0, 'data' => [], 'errors' => Helpers::error_processor($validator)], 200);
        }
        $save = new \App\Models\TourCabManage();
        $save->traveller_id =  $request['cab_assign'];
        $save->cab_id =  $request['cab_id'];
        $save->model_number =  $request['model_number'];
        $save->reg_number =  $request['reg_number'];
        $save->status =  0;
        if ($request->file('image')) {
            $save->image = imageManager::upload('tour_and_travels/tour_traveller_cab/', 'webp', $request->file('image'));
        }
        $save->save();
        if ($save) {
            return response()->json(['status' => 1, 'message' => 'Tour visit traveller cab added successfully', 'data' => []], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'added Failed', 'data' => []], 200);
        }
    }

    public function TravellerCabList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "cab_assign" => ['required', function ($attribute, $value, $fail) {
                if (!TourAndTravel::where('id', $value)->where('is_approve', 1)->where('status', 1)->exists()) {
                    $fail('The selected cab Id is invalid or inactive.');
                }
            },],
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'recode' => 0, 'data' => [], 'errors' => Helpers::error_processor($validator)], 200);
        }
        $getData = \App\Models\TourCabManage::where('traveller_id', $request['cab_assign'])->with('Cabs')->get();
        if (!empty($getData) && count($getData) > 0) {
            $getArray = [];
            foreach ($getData as $key => $value) {
                $getArray[$key]['id'] = $value['id'];
                $getArray[$key]['cab_id'] = $value['cab_id'];
                $getArray[$key]['cab_name'] = $value['Cabs']['name'] ?? "";
                $getArray[$key]['model_number'] = $value['model_number'];
                $getArray[$key]['reg_number'] = $value['reg_number'];
                $getArray[$key]['image'] =  getValidImage(path: 'storage/app/public/tour_and_travels/tour_traveller_cab/' . $value['image'], type: 'backend-product');
                $getArray[$key]['status'] = $value['status'];
            }
            return response()->json(['status' => 1, 'message' => 'Tour visit traveller cab get successfully', 'data' => $getArray], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Not Found', 'data' => []], 200);
        }
    }

    public function TravellerCabSingle(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "cab_assign" => ['required', function ($attribute, $value, $fail) {
                if (!TourAndTravel::where('id', $value)->where('is_approve', 1)->where('status', 1)->exists()) {
                    $fail('The selected traveller Id is invalid or inactive.');
                }
            },],
            'traveller_cab_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'recode' => 0, 'data' => [], 'errors' => Helpers::error_processor($validator)], 200);
        }
        $getData = \App\Models\TourCabManage::where('traveller_id', $request['cab_assign'])->where('id', $request['traveller_cab_id'])->first();
        if (!empty($getData)) {
            $getData['image'] =  getValidImage(path: 'storage/app/public/tour_and_travels/tour_traveller_cab/' . $getData['image'], type: 'backend-product');

            return response()->json(['status' => 1, 'message' => 'Tour visit traveller cab get successfully', 'data' => $getData], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Not Found', 'data' => []], 200);
        }
    }

    public function TravellerCabUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'traveller_id' => 'required|integer|exists:tour_traveller_cabs,id',
            "cab_assign" => ['required', function ($attribute, $value, $fail) use ($request) {
                if (!TourAndTravel::where('id', $value)->where('is_approve', 1)->where('status', 1)->exists()) {
                    $fail('The selected cab Id is invalid or inactive.');
                }
                if (!\App\Models\TourCabManage::where('traveller_id', $value)->where('id', $request['traveller_id'])->exists()) {
                    $fail('The selected cab Id is invalid or inactive.');
                }
            },],
            'cab_id' => 'required|integer|exists:tour_cab,id',
            'reg_number' => "required|string|max:15|regex:/^[A-Za-z0-9 ]+$/|unique:tour_traveller_cabs,reg_number",
            'model_number' => 'required|string|max:50',
            'image' => "image|mimes:jpeg,png,jpg,gif"
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'recode' => 0, 'data' => [], 'errors' => Helpers::error_processor($validator)], 200);
        }
        $save = \App\Models\TourCabManage::find($request['traveller_id']);
        $save->cab_id =  $request['cab_id'];
        $save->model_number =  $request['model_number'];
        $save->reg_number =  $request['reg_number'];
        if ($request->file('image')) {
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists('tour_and_travels/tour_traveller_cab/' . $save['image'])) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete('tour_and_travels/tour_traveller_cab/' . $save['image']);
            }
            $save->image = imageManager::upload('tour_and_travels/tour_traveller_cab/', 'webp', $request->file('image'));
        }
        $save->save();
        if ($save) {
            return response()->json(['status' => 1, 'message' => 'Tour visit traveller cab updated successfully', 'data' => []], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'updated Failed', 'data' => []], 200);
        }
    }

    public function TravellerCabDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'traveller_id' => 'required|integer|exists:tour_traveller_cabs,id',
            "cab_assign" => ['required', function ($attribute, $value, $fail) use ($request) {
                if (!TourAndTravel::where('id', $value)->where('is_approve', 1)->where('status', 1)->exists()) {
                    $fail('The selected cab Id is invalid or inactive.');
                }
                if (!\App\Models\TourCabManage::where('traveller_id', $value)->where('id', $request['traveller_id'])->exists()) {
                    $fail('records are invalid.');
                }
                if (\App\Models\TourOrder::where('cab_assign', $value)->where('traveller_cab_id', $request['traveller_id'])->where('drop_status', 0)->exists()) {
                    $fail("This cab is already booked so don't delete the recode.");
                }
            },]
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'recode' => 0, 'data' => [], 'errors' => Helpers::error_processor($validator)], 200);
        }
        $old_data = \App\Models\TourCabManage::find($request['traveller_id']);
        if ($old_data) {
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists('tour_and_travels/tour_traveller_cab/' . $old_data['image'])) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete('tour_and_travels/tour_traveller_cab/' . $old_data['image']);
            }
            $old_data->delete();
            return response()->json(['success' => 1, 'message' => translate('Traveller_cab_Deleted_successfully')], 200);
        } else {
            return response()->json(['success' => 0, 'message' => translate('Traveller_cab_Deleted_Failed')], 400);
        }
    }

    public function TravellerAddDriver(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "cab_assign" => ['required', function ($attribute, $value, $fail) {
                if (!TourAndTravel::where('id', $value)->where('is_approve', 1)->where('status', 1)->exists()) {
                    $fail('The selected cab Id is invalid or inactive.');
                }
            },],
            'phone' => "required|digits:10|unique:tour_traveller_driver,phone",
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'gender' => 'required|in:male,female,other',
            'dob' => 'required|date|before:-18 years',
            'year_ex' => 'required|integer|min:0',
            'license_number' => "required|string|regex:/^[A-Za-z0-9 ]{1,15}$/|unique:tour_traveller_driver,license_number",
            'pan_number' => "required|string|regex:/^[A-Za-z0-9 ]{1,15}$/|unique:tour_traveller_driver,pan_number",
            'aadhar_number' => "required|string|regex:/^\d{12}$/|unique:tour_traveller_driver,aadhar_number",
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'license_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'pan_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'aadhar_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'recode' => 0, 'data' => [], 'errors' => Helpers::error_processor($validator)], 200);
        }
        $save = new \App\Models\TourDriverManage();
        $save->traveller_id =  $request['cab_assign'];
        $save->name =  $request['name'];
        $save->phone =  $request['phone'];
        $save->email =  ($request['email'] ?? '');
        $save->gender =  $request['gender'];
        $save->dob =  $request['dob'];
        $save->year_ex =  $request['year_ex'];
        $save->license_number =  $request['license_number'];
        $save->pan_number =  $request['pan_number'];
        $save->aadhar_number =  $request['aadhar_number'];
        $save->status =  0;

        if ($request->file('image')) {
            $save->image = imageManager::upload('tour_and_travels/tour_traveller_driver/', 'webp', $request->file('image'));
        }
        if ($request->file('license_image')) {
            $save->license_image = imageManager::upload('tour_and_travels/tour_traveller_driver/', 'webp', $request->file('license_image'));
        }
        if ($request->file('pan_image')) {
            $save->pan_image = imageManager::upload('tour_and_travels/tour_traveller_driver/', 'webp', $request->file('pan_image'));
        }
        if ($request->file('aadhar_image')) {
            $save->aadhar_image = imageManager::upload('tour_and_travels/tour_traveller_driver/', 'webp', $request->file('aadhar_image'));
        }
        $save->save();
        if ($save) {
            return response()->json(['status' => 1, 'message' => 'Tour visit traveller driver added successfully', 'data' => []], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'added Failed', 'data' => []], 200);
        }
    }

    public function TravellerDriverList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "cab_assign" => ['required', function ($attribute, $value, $fail) {
                if (!TourAndTravel::where('id', $value)->where('is_approve', 1)->where('status', 1)->exists()) {
                    $fail('The selected traveller Id is invalid or inactive.');
                }
            },],
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'recode' => 0, 'data' => [], 'errors' => Helpers::error_processor($validator)], 200);
        }
        $getData = \App\Models\TourDriverManage::where('traveller_id', $request['cab_assign'])->get();
        if (!empty($getData) && count($getData) > 0) {
            $getArray = [];
            foreach ($getData as $key => $value) {
                $getArray[$key]['id'] = $value['id'];
                $getArray[$key]['name'] = $value['name'];
                $getArray[$key]['phone'] = $value['phone'] ?? "";
                $getArray[$key]['email'] = $value['email'];
                $getArray[$key]['gender'] = $value['gender'];
                $getArray[$key]['dob'] = date('d M,Y', strtotime($value['dob']));
                $getArray[$key]['year_ex'] = $value['year_ex'];
                $getArray[$key]['license_number'] = $value['license_number'];
                $getArray[$key]['pan_number'] = $value['pan_number'];
                $getArray[$key]['aadhar_number'] = $value['aadhar_number'];
                $getArray[$key]['status'] = $value['status'];
                $getArray[$key]['order_complete'] = $value['order_complete'];
                $getArray[$key]['image'] =  getValidImage(path: 'storage/app/public/tour_and_travels/tour_traveller_driver/' . $value['image'], type: 'backend-product');
                $getArray[$key]['license_image'] =  getValidImage(path: 'storage/app/public/tour_and_travels/tour_traveller_driver/' . $value['license_image'], type: 'backend-product');
                $getArray[$key]['pan_image'] =  getValidImage(path: 'storage/app/public/tour_and_travels/tour_traveller_driver/' . $value['pan_image'], type: 'backend-product');
                $getArray[$key]['aadhar_image'] =  getValidImage(path: 'storage/app/public/tour_and_travels/tour_traveller_driver/' . $value['aadhar_image'], type: 'backend-product');
                $getArray[$key]['status'] = $value['status'];
            }
            return response()->json(['status' => 1, 'message' => 'Tour visit traveller cab get successfully', 'data' => $getArray], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Not Found', 'data' => []], 200);
        }
    }

    public function TravellerDriverSingle(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "cab_assign" => ['required', function ($attribute, $value, $fail) {
                if (!TourAndTravel::where('id', $value)->where('is_approve', 1)->where('status', 1)->exists()) {
                    $fail('The selected traveller Id is invalid or inactive.');
                }
            },],
            'traveller_cab_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'recode' => 0, 'data' => [], 'errors' => Helpers::error_processor($validator)], 200);
        }
        $getData = \App\Models\TourCabManage::where('traveller_id', $request['cab_assign'])->where('id', $request['traveller_cab_id'])->first();
        if (!empty($getData)) {
            $getData['image'] =  getValidImage(path: 'storage/app/public/tour_and_travels/tour_traveller_driver/' . $getData['image'], type: 'backend-product');
            $getData['license_image'] =  getValidImage(path: 'storage/app/public/tour_and_travels/tour_traveller_driver/' . $getData['license_image'], type: 'backend-product');
            $getData['pan_image'] =  getValidImage(path: 'storage/app/public/tour_and_travels/tour_traveller_driver/' . $getData['pan_image'], type: 'backend-product');
            $getData['aadhar_image'] =  getValidImage(path: 'storage/app/public/tour_and_travels/tour_traveller_driver/' . $getData['aadhar_image'], type: 'backend-product');
            return response()->json(['status' => 1, 'message' => 'Tour visit traveller cab get successfully', 'data' => $getData], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Not Found', 'data' => []], 200);
        }
    }

    public function TravellerDriverUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "cab_assign" => ['required', function ($attribute, $value, $fail) {
                if (!TourAndTravel::where('id', $value)->where('is_approve', 1)->where('status', 1)->exists()) {
                    $fail('The selected traveller Id is invalid or inactive.');
                }
            },],
            "driver_id" => ['required', function ($attribute, $value, $fail) use ($request) {
                if (!\App\Models\TourDriverManage::where('id', $value)->where('traveller_id', $request['cab_assign'])->exists()) {
                    $fail('The selected driver Id is invalid.');
                }
            },],
            'phone' => "required|digits:10|unique:tour_traveller_driver,phone," . $request['driver_id'],
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'gender' => 'required|in:male,female,other',
            'dob' => 'required|date|before:-18 years',
            'year_ex' => 'required|integer|min:0',
            'license_number' => "required|string|regex:/^[A-Za-z0-9 ]{1,15}$/|unique:tour_traveller_driver,license_number," . $request['driver_id'],
            'pan_number' => "required|string|regex:/^[A-Za-z0-9 ]{1,15}$/|unique:tour_traveller_driver,pan_number," . $request['driver_id'],
            'aadhar_number' => "required|string|regex:/^\d{12}$/|unique:tour_traveller_driver,aadhar_number," . $request['driver_id'],
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'license_image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'pan_image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'aadhar_image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'recode' => 0, 'data' => [], 'errors' => Helpers::error_processor($validator)], 200);
        }
        $save = \App\Models\TourDriverManage::find($request['driver_id']);
        $save->traveller_id =  $request['cab_assign'];
        $save->name =  $request['name'];
        $save->phone =  $request['phone'];
        $save->email =  ($request['email'] ?? '');
        $save->gender =  $request['gender'];
        $save->dob =  $request['dob'];
        $save->year_ex =  $request['year_ex'];
        $save->license_number =  $request['license_number'];
        $save->pan_number =  $request['pan_number'];
        $save->aadhar_number =  $request['aadhar_number'];
        $save->status =  0;

        if ($request->file('image')) {
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists('tour_and_travels/tour_traveller_driver/' . $save['image'])) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete('tour_and_travels/tour_traveller_driver/' . $save['image']);
            }
            $save->image = imageManager::upload('tour_and_travels/tour_traveller_driver/', 'webp', $request->file('image'));
        }
        if ($request->file('license_image')) {
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists('tour_and_travels/tour_traveller_driver/' . $save['license_image'])) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete('tour_and_travels/tour_traveller_driver/' . $save['license_image']);
            }
            $save->license_image = imageManager::upload('tour_and_travels/tour_traveller_driver/', 'webp', $request->file('license_image'));
        }
        if ($request->file('pan_image')) {
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists('tour_and_travels/tour_traveller_driver/' . $save['pan_image'])) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete('tour_and_travels/tour_traveller_driver/' . $save['pan_image']);
            }
            $save->pan_image = imageManager::upload('tour_and_travels/tour_traveller_driver/', 'webp', $request->file('pan_image'));
        }
        if ($request->file('aadhar_image')) {
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists('tour_and_travels/tour_traveller_driver/' . $save['aadhar_image'])) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete('tour_and_travels/tour_traveller_driver/' . $save['aadhar_image']);
            }
            $save->aadhar_image = imageManager::upload('tour_and_travels/tour_traveller_driver/', 'webp', $request->file('aadhar_image'));
        }
        $save->save();
        if ($save) {
            return response()->json(['status' => 1, 'message' => 'Tour visit traveller driver updated successfully', 'data' => []], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'added Failed', 'data' => []], 200);
        }
    }

    public function TravellerDriverDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'driver_id' => 'required|integer|exists:tour_traveller_driver,id',
            "cab_assign" => ['required', function ($attribute, $value, $fail) use ($request) {
                if (!TourAndTravel::where('id', $value)->where('is_approve', 1)->where('status', 1)->exists()) {
                    $fail('The selected traveller Id is invalid or inactive.');
                }
                if (!\App\Models\TourDriverManage::where('traveller_id', $value)->where('id', $request['driver_id'])->exists()) {
                    $fail('records are invalid.');
                }
                if (\App\Models\TourOrder::where('cab_assign', $value)->where('traveller_driver_id', $request['driver_id'])->where('drop_status', 0)->exists()) {
                    $fail("This driver is already booked so don't delete the recode.");
                }
            },]
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'recode' => 0, 'data' => [], 'errors' => Helpers::error_processor($validator)], 200);
        }
        $old_data = \App\Models\TourDriverManage::find($request['driver_id']);
        if ($old_data) {
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists('tour_and_travels/tour_traveller_driver/' . $old_data['image'])) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete('tour_and_travels/tour_traveller_driver/' . $old_data['image']);
            }
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists('tour_and_travels/tour_traveller_driver/' . $old_data['license_image'])) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete('tour_and_travels/tour_traveller_driver/' . $old_data['license_image']);
            }
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists('tour_and_travels/tour_traveller_driver/' . $old_data['pan_image'])) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete('tour_and_travels/tour_traveller_driver/' . $old_data['pan_image']);
            }
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists('tour_and_travels/tour_traveller_driver/' . $old_data['aadhar_image'])) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete('tour_and_travels/tour_traveller_driver/' . $old_data['aadhar_image']);
            }
            $old_data->delete();
            return response()->json(['success' => 1, 'message' => translate('Traveller_driver_Deleted_successfully')], 200);
        } else {
            return response()->json(['success' => 0, 'message' => translate('Traveller_driver_Deleted_Failed')], 400);
        }
    }

    public function GetTypes()
    {
        $getType = \App\Models\TourType::where('status', 1)->orderBy('id', 'desc')->get();
        if (count($getType) > 0) {
            $getData = [];
            foreach ($getType as $key => $value) {
                $getData[$key]['slug'] = $value['slug'];
                $getData[$key]['name'] = $value['name'];
            }
            return response()->json(['status' => 1, 'message' => 'Get Successfully', 'recode' => count($getData), 'data' => $getData], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Not found', 'recode' => 0, 'data' => []], 400);
        }
    }
    public function GetCabList()
    {
        $getType = \App\Models\TourCab::where('status', 1)->orderBy('id', 'desc')->get();
        if (count($getType) > 0) {
            $getData = [];
            foreach ($getType as $key => $value) {
                $getData[$key]['id'] = $value['id'];
                $getData[$key]['name'] = $value['name'];
                $getData[$key]['seats'] = $value['seats'];
            }
            return response()->json(['status' => 1, 'message' => 'Get Successfully', 'recode' => count($getData), 'data' => $getData], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Not found', 'recode' => 0, 'data' => []], 400);
        }
    }
    public function GetPackageList()
    {
        $getpackage = \App\Models\TourPackage::where('status', 1)->orderBy('id', 'desc')->get();
        if (count($getpackage) > 0) {
            $getData = [];
            foreach ($getpackage as $key => $value) {
                $getData[$key]['id'] = $value['id'];
                $getData[$key]['name'] = $value['name'];
                $getData[$key]['seats'] = $value['title'];
            }
            return response()->json(['status' => 1, 'message' => 'Get Successfully', 'recode' => count($getData), 'data' => $getData], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Not found', 'recode' => 0, 'data' => []], 400);
        }
    }

    public function GetLanguageList()
    {
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        if ($languages) {
            return response()->json(['status' => 1, 'message' => 'Get Successfully', 'recode' => count($languages), 'data' => $languages], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Not found', 'recode' => 0, 'data' => []], 400);
        }
    }




    public function AddTour(Request $request, TourVisitService $service)
    {
        $validator = Validator::make($request->all(), [
            'tour_name' => 'required|array',
            'tour_name.*' => 'required|string|min:1',
            'tour_type' => 'required',
            'created_id' => 'required',
            'cities_name' => 'required|array',
            'cities_name.*' => 'required|string|min:1',
            'country_name' => 'required|array',
            'country_name.*' => 'required|string|min:1',
            'state_name' => 'required|array',
            'state_name.*' => 'required|string|min:1',
            'lat' => 'required',
            'long' => 'required',
            'ex_distance' => 'required',

            'description' => 'required|array',
            'description.*' => 'required|string|min:1',
            'highlights' => 'required|array',
            'highlights.*' => 'required|string|min:1',
            'inclusion' => 'required|array',
            'inclusion.*' => 'required|string|min:1',
            'exclusion' => 'required|array',
            'exclusion.*' => 'required|string|min:1',
            'terms_and_conditions' => 'required|array',
            'terms_and_conditions.*' => 'required|string|min:1',
            'cancellation_policy' => 'required|array',
            'cancellation_policy.*' => 'required|string|min:1',
            'notes' => 'required|array',
            'notes.*' => 'required|string|min:1',

            'cab_id' => 'required|array',
            'cab_id.*' => 'required|string|min:1',
            'price' => 'required|array',
            'price.*' => 'required|string|min:1',

            'package_id' => 'required|array',
            'package_id.*' => 'required|string|min:1',
            'pprice' => 'required|array',
            'pprice.*' => 'required|string|min:1',
            'use_date' => 'required|in:0,1,2,3,4',
            "startandend_date" => 'required_if:use_date,1',
            "pickup_time" => "required_if:use_date,1",
            "pickup_location" => "required_if:use_date,1,4,2",
            "pickup_lat" => "required_if:use_date,1,4,2",
            "pickup_long" => "required_if:use_date,1,4,2",
            'tour_image' => 'required|image|mimes:jpeg,png,jpg,gif',
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'recode' => 0, 'data' => [], 'errors' => Helpers::error_processor($validator)], 200);
        }
        \Illuminate\Support\Facades\DB::beginTransaction();
        $request['lang'] = getWebConfig(name: 'pnc_language') ?? [];

        try {
            $dataArray = $service->getTourVisitData($request);
            $insert = $this->tourtraveller->add(data: $dataArray);
            $this->translationRepo->add(request: $request, model: 'App\Models\TourVisits', id: $insert->id);

            \Illuminate\Support\Facades\DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Tour visit data added successfully.',], 200);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Failed to add tour visit data. Please try again later.', 'error' => $e->getMessage(),], 400);
        }
    }


    public function TourList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cab_assign' => ['required', function ($attribute, $value, $fail) {
                if (!TourAndTravel::where('id', $value)->where('is_approve', 1)->where('status', 1)->exists()) {
                    $fail('The selected cab Id is invalid or inactive.');
                }
            },],
        ], [
            'cab_assign.required' => 'Cab Id is Empty!',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'recode' => 0, 'data' => [], 'errors' => Helpers::error_processor($validator)], 200);
        }
        $getDatalist = $this->tourtraveller->getListWhere(orderBy: ['id' => 'desc'], filters: ['created_id' => [$request['cab_assign'], 0]], dataLimit: 'all');
        if ($getDatalist) {
            $getDatas = [];
            foreach ($getDatalist as $k => $val) {
                $getDatas[$k]['id'] = $val['id'];
                $getDatas[$k]['tour_id'] = $val['tour_id'];
                $getDatas[$k]['tour_name'] = $val['tour_name'];
                $getDatas[$k]['tour_type'] = $val['tour_type'];
                $getcheckbox  = \App\Models\TourOrderAccept::where('traveller_id', $request['cab_assign'])->where('tour_id', $val['id'])->first();
                $getDatas[$k]['accept_type'] = $getcheckbox['status'] ?? 0;
                $getDatas[$k]['create_by'] = (($val['created_id']) ? 'admin' : 'vendor');
                $getDatas[$k]['status'] = $val['status'];
            }
            return response()->json(['status' => 1, 'message' => 'Get Successfully', 'recode' => count($getDatas), 'data' => $getDatas], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Not found', 'recode' => 0, 'data' => []], 400);
        }
    }

    public function TourStatusChage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cab_assign' => ['required', function ($attribute, $value, $fail) {
                if (!TourAndTravel::where('id', $value)->where('is_approve', 1)->where('status', 1)->exists()) {
                    $fail('The selected cab Id is invalid or inactive.');
                }
            },],
            'tour_id' => ['required', function ($attribute, $value, $fail) use ($request) {
                if (!TourVisits::where('id', $value)->whereIn('created_id', [$request->cab_assign, 0])->exists()) {
                    $fail('The selected Tour Id is invalid.');
                }
            },],
            'status' => "required|in:1,0",
        ], [
            'cab_assign.required' => 'Cab Id is Empty!',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'recode' => 0, 'data' => [], 'errors' => Helpers::error_processor($validator)], 200);
        }

        $data['status'] = $request->get('status', 0);
        $this->tourtraveller->update(id: $request['tour_id'], data: $data);
        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }


    public function TourGetId(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cab_assign' => ['required', function ($attribute, $value, $fail) {
                if (!TourAndTravel::where('id', $value)->where('is_approve', 1)->where('status', 1)->exists()) {
                    $fail('The selected cab Id is invalid or inactive.');
                }
            },],
            'tour_id' => ['required', function ($attribute, $value, $fail) use ($request) {
                if (!TourVisits::where('id', $value)->where('created_id', $request->cab_assign)->exists()) {
                    $fail('The selected Tour Id is invalid.');
                }
            },],
        ], [
            'cab_assign.required' => 'Cab Id is Empty!',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'recode' => 0, 'data' => [], 'errors' => Helpers::error_processor($validator)], 200);
        }
        $getDatalist = \App\Models\TourVisits::where('id', $request->tour_id)->first();
        if ($getDatalist) {
            $hindi_tour = $getDatalist ? $getDatalist->translations()->pluck('value', 'key')->toArray() : [];
            $getDatas['id'] = $getDatalist['id'];
            $getDatas['tour_id'] = $getDatalist['tour_id'];
            $getDatas['en_tour_name'] = $getDatalist['tour_name'];
            $getDatas['hi_tour_name'] = $hindi_tour['tour_name'] ?? "";
            $getDatas['tour_type'] = $getDatalist['tour_type'];
            $getDatas['en_cities_name'] = $getDatalist['cities_name'];
            $getDatas['hi_cities_name'] = $hindi_tour['cities_name'] ?? "";
            $getDatas['en_country_name'] = $getDatalist['country_name'];
            $getDatas['hi_country_name'] = $hindi_tour['country_name'] ?? "";
            $getDatas['en_state_name'] = $getDatalist['state_name'];
            $getDatas['hi_state_name'] = $hindi_tour['state_name'] ?? "";
            $getDatas['lat'] = $getDatalist['lat'];
            $getDatas['long'] = $getDatalist['long'];
            $getDatas['en_description'] = $getDatalist['description'];
            $getDatas['hi_description'] = $hindi_tour['description'] ?? "";
            $getDatas['en_highlights'] = $getDatalist['highlights'];
            $getDatas['hi_highlights'] = $hindi_tour['highlights'] ?? "";
            $getDatas['en_inclusion'] = $getDatalist['inclusion'];
            $getDatas['hi_inclusion'] = $hindi_tour['inclusion'] ?? "";
            $getDatas['en_exclusion'] = $getDatalist['exclusion'];
            $getDatas['hi_exclusion'] = $hindi_tour['exclusion'] ?? "";
            $getDatas['en_terms_and_conditions'] = $getDatalist['terms_and_conditions'];
            $getDatas['hi_terms_and_conditions'] = $hindi_tour['terms_and_conditions'] ?? "";
            $getDatas['en_cancellation_policy'] = $getDatalist['cancellation_policy'];
            $getDatas['hi_cancellation_policy'] = $hindi_tour['cancellation_policy'] ?? "";
            $getDatas['en_notes'] = $getDatalist['notes'];
            $getDatas['hi_notes'] = $hindi_tour['notes'] ?? "";
            $getDatas['cab_list_price'] = json_decode($getDatalist['cab_list_price'], true);
            $getDatas['package_list_price'] = json_decode($getDatalist['package_list_price'], true);
            $getDatas['tour_image'] = getValidImage(path: 'storage/app/public/tour_and_travels/tour_visit/' . $getDatalist['tour_image'], type: 'backend-product');
            $getDatas['itineraryupload'] = getValidImage(path: 'storage/app/public/tour_and_travels/tour_visit/' . $getDatalist['itineraryupload'], type: 'backend-product');
            $getmutiimage = [];
            if (!empty($getDatalist['image']) && json_decode($getDatalist['image'], true)) {
                foreach (json_decode($getDatalist['image'], true) as $key => $value) {
                    $getmutiimage[] = getValidImage(path: 'storage/app/public/tour_and_travels/tour_visit/' . $value, type: 'backend-product');
                }
            }
            $getDatas['image'] = $getmutiimage;
            $getDatas['use_date'] = $getDatalist['use_date'];

            $getDatas['pickup_time'] = $getDatalist['pickup_time'];
            $getDatas['pickup_location'] = $getDatalist['pickup_location'];
            $getDatas['pickup_lat'] = $getDatalist['pickup_lat'];
            $getDatas['pickup_long'] = $getDatalist['pickup_long'];
            $getDatas['startandend_date'] = $getDatalist['startandend_date'];
            $getDatas['ex_distance'] = $getDatalist['ex_distance'];

            return response()->json(['status' => 1, 'message' => 'Get Successfully', 'recode' => 1, 'data' => $getDatas], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Not found', 'recode' => 0, 'data' => []], 400);
        }
    }

    public function TourImageRemove(Request $request, TourVisitService $service)
    {
        $validator = Validator::make($request->all(), [
            'cab_assign' => ['required', function ($attribute, $value, $fail) {
                if (!TourAndTravel::where('id', $value)->where('is_approve', 1)->where('status', 1)->exists()) {
                    $fail('The selected cab Id is invalid or inactive.');
                }
            },],
            'tour_id' => ['required', function ($attribute, $value, $fail) use ($request) {
                if (!TourVisits::where('id', $value)->where('created_id', $request->cab_assign)->exists()) {
                    $fail('The selected Tour Id is invalid.');
                }
            },],
            'image_name' => "required",
        ], [
            'cab_assign.required' => 'Cab Id is Empty!',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'recode' => 0, 'data' => [], 'errors' => Helpers::error_processor($validator)], 200);
        }
        $getData  = $this->tourtraveller->getFirstWhere(params: ['id' => $request->tour_id]);
        if (empty($getData)) {
            return back();
        }
        $dataIMage = $service->ImageRemove($getData, $request->image_name);
        $this->tourtraveller->update(id: $request->tour_id, data: ['image' => json_encode($dataIMage)]);
        return response()->json(['status' => 1, 'message' => 'image Remove Successfully', 'recode' => 1, 'data' => []], 200);
    }

    public function TourUpdate(Request $request, TourVisitService $service)
    {
        $validator = Validator::make($request->all(), [
            'tour_id' => ['required', function ($attribute, $value, $fail) use ($request) {
                if (!TourVisits::where('id', $value)->where('created_id', $request->created_id)->exists()) {
                    $fail('The selected Tour Id is invalid.');
                }
            },],
            'tour_name' => 'required|array',
            'tour_name.*' => 'required|string|min:1',
            'tour_type' => 'required',
            'created_id' => 'required',
            'cities_name' => 'required|array',
            'cities_name.*' => 'required|string|min:1',
            'country_name' => 'required|array',
            'country_name.*' => 'required|string|min:1',
            'state_name' => 'required|array',
            'state_name.*' => 'required|string|min:1',
            'lat' => 'required',
            'long' => 'required',
            'ex_distance' => 'required',

            'description' => 'required|array',
            'description.*' => 'required|string|min:1',
            'highlights' => 'required|array',
            'highlights.*' => 'required|string|min:1',
            'inclusion' => 'required|array',
            'inclusion.*' => 'required|string|min:1',
            'exclusion' => 'required|array',
            'exclusion.*' => 'required|string|min:1',
            'terms_and_conditions' => 'required|array',
            'terms_and_conditions.*' => 'required|string|min:1',
            'cancellation_policy' => 'required|array',
            'cancellation_policy.*' => 'required|string|min:1',
            'notes' => 'required|array',
            'notes.*' => 'required|string|min:1',

            'cab_id' => 'required|array',
            'cab_id.*' => 'required|string|min:1',
            'price' => 'required|array',
            'price.*' => 'required|string|min:1',

            'package_id' => 'required|array',
            'package_id.*' => 'required|string|min:1',
            'pprice' => 'required|array',
            'pprice.*' => 'required|string|min:1',
            'use_date' => 'required|in:0,1',
            "startandend_date" => 'required_if:use_date,1',
            "pickup_time" => "required_if:use_date,1",
            "pickup_location" => "required_if:use_date,1,2,4",
            "pickup_lat" => "required_if:use_date,1,2,4",
            "pickup_long" => "required_if:use_date,1,2,4",
            'tour_image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'recode' => 0, 'data' => [], 'errors' => Helpers::error_processor($validator)], 200);
        }
        \Illuminate\Support\Facades\DB::beginTransaction();
        $request['lang'] = getWebConfig(name: 'pnc_language') ?? [];

        try {
            $dataArray = $service->getTourVisitData($request);
            $this->tourtraveller->update(id: $request->tour_id, data: $dataArray);
            $this->translationRepo->update(request: $request, model: 'App\Models\TourVisits', id: $request->tour_id);

            \Illuminate\Support\Facades\DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Tour visit data updated successfully.',], 200);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Failed to update tour visit data. Please try again later.', 'error' => $e->getMessage(),], 400);
        }
    }

    public function TourDelete(Request $request, TourVisitService $service)
    {
        $validator = Validator::make($request->all(), [
            'cab_assign' => ['required', function ($attribute, $value, $fail) {
                if (!TourAndTravel::where('id', $value)->where('is_approve', 1)->where('status', 1)->exists()) {
                    $fail('The selected cab Id is invalid or inactive.');
                }
            },],
            'tour_id' => ['required', function ($attribute, $value, $fail) use ($request) {
                if (!TourVisits::where('id', $value)->where('created_id', $request->cab_assign)->exists()) {
                    $fail('The selected Tour Id is invalid.');
                }
            },]
        ], [
            'cab_assign.required' => 'Cab Id is Empty!',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'recode' => 0, 'data' => [], 'errors' => Helpers::error_processor($validator)], 200);
        }

        $getData = $this->tourtraveller->getFirstWhere(params: ['id' => $request['tour_id'], 'status' => 0, 'created_id' => $request['cab_assign']]);
        if (!empty($getData)) {
            $service->removeimages($getData);
            $this->tourtraveller->delete(params: ['id' => $request['tour_id']]);
            return response()->json(['status' => 1, 'message' => translate('Tour_visit_Deleted_successfully'), 'data' => []], 200);
        } else {
            return response()->json(['status' => 0, 'message' => translate('Travel_tour_visit_will_be_deleted_by_administrator_only'), 'data' => []], 200);
        }
    }
    public function TourOrderAccept(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cab_assign' => ['required', function ($attribute, $value, $fail) {
                if (!TourAndTravel::where('id', $value)->where('is_approve', 1)->where('status', 1)->exists()) {
                    $fail('The selected cab Id is invalid or inactive.');
                }
            },],
            'tour_id' => ['required', function ($attribute, $value, $fail) use ($request) {
                if (!TourVisits::where('id', $value)->whereIn('created_id', [0, $request->cab_assign])->exists()) {
                    $fail('The selected Tour Id is invalid.');
                }
            },],
            'status' => "required|in:1,0"
        ], [
            'cab_assign.required' => 'Cab Id is Empty!',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'recode' => 0, 'data' => [], 'errors' => Helpers::error_processor($validator)], 200);
        }

        $getData = \App\Models\TourOrderAccept::where('traveller_id', $request['cab_assign'])->where('tour_id', $request['tour_id'])->first();
        $checkOrder = \App\Models\TourOrder::where('cab_assign', $request['cab_assign'])->where('tour_id', $request['tour_id'])->where('drop_status', 0)->first();
        if ($checkOrder) {
            return response()->json(['success' => 0, 'message' => translate('There_are_still_some_orders_left_on_this_tour'), 'data' => []], 200);
        } else {
            if (!empty($getData)) {
                $saveData = \App\Models\TourOrderAccept::find($getData['id']);
            } else {
                $saveData = new \App\Models\TourOrderAccept();
            }
            $saveData->tour_id = $request->tour_id;
            $saveData->traveller_id = $request['cab_assign'];
            $saveData->status = $request['status'];
            $saveData->save();
            return response()->json(['success' => 1, 'message' => translate('status_updated_successfully'), 'data' => $saveData], 200);
        }
    }

    public function NewTopTour()
    {
        $cities_tour = [];
        $special_tour = TourVisits::where('status', 1)->where(function ($query) {
            $query->whereIn('use_date', [0, 2, 3, 4])
                ->orWhere(function ($query) {
                    $query->where('use_date', 1)
                        ->where(function ($subQuery) {
                            $subQuery->whereIn('customized_type', ['', '0'])
                                ->whereNotNull('startandend_date')
                                ->whereRaw('? < STR_TO_DATE(SUBSTRING_INDEX(startandend_date, " - ", 1), "%Y-%m-%d")', [date('Y-m-d')]);
                        })
                        ->orWhere(function ($subQuery) {
                            $subQuery->whereIn('customized_type', [1, 2, 3]);
                        });
                });
        })->withCount('TourOrderReview')
            ->withAvg('review', 'star')->withTourCheck()->orderBy('id', 'desc')->limit(10)->get();

        if (!empty($special_tour) && count($special_tour) > 0) {
            $p = 0;
            foreach ($special_tour as $key => $val) {
                $hindi_tour = $val ? $val->translations()->pluck('value', 'key')->toArray() : [];
                $cities_tour[$p]['id'] = ($val['id'] ?? "");
                $cities_tour[$p]['slug'] = ($val['slug'] ?? "");
                $cities_tour[$p]['en_tour_name'] = $val['tour_name'] ?? "";
                $cities_tour[$p]['hi_tour_name'] = $hindi_tour['tour_name'] ?? "";
                $cities_tour[$p]['use_date'] = ($val['use_date'] ?? '');
                $cities_tour[$p]['date'] = ($val['startandend_date'] ?? '');
                $numb_days_en = '';
                $numb_days_in = '';
                if ($val['number_of_day'] == 0.5) {
                    $numb_days_en = 'Half Day';
                    $numb_days_in = 'आधा दिन';
                } elseif ($val['number_of_day'] > 0 && ($val['number_of_night'] ?? 0) <= 0) {
                    $numb_days_en = ($val['number_of_day'] ?? '') . " Day";
                    $numb_days_in = ($val['number_of_day'] ?? '') . " दिन";
                } else {
                    $numb_days_en = ($val['number_of_day'] ?? '') . "D/" . ($val['number_of_night'] ?? '') . "N";
                    $numb_days_in = ($val['number_of_day'] ?? '') . "दिन/" . ($val['number_of_night'] ?? '') . "रात्रि";
                }
                $cities_tour[$p]['en_number_of_day'] = $numb_days_en;
                $cities_tour[$p]['hi_number_of_day'] = $numb_days_in;

                $plans = [
                    0 => ['name' => 'Basic', 'style' => '#495057'],
                    1 => ['name' => 'Standard', 'style' => '#218838'],
                    2 => ['name' => 'Premium', 'style' => '#0056b3'],
                    3 => ['name' => 'Golden', 'style' => '#FFA500'],
                    4 => ['name' => 'Luxury', 'style' => '#b02a37'],
                ];

                $selectedPlan = $val['plan_type'] ?? 0;
                $cities_tour[$p]['plan_type_name'] = $plans[$selectedPlan]['name'];
                $cities_tour[$p]['plan_type_color'] = $plans[$selectedPlan]['style'];

                $cities_tour[$p]['tour_order_review_count'] = (int)$val['tour_order_review_count'];
                $cities_tour[$p]['review_avg_star'] = number_format((float)$val['review_avg_star'], 2);
                $cities_tour[$p]['percentage_off'] = ($val['percentage_off'] ?? 0);
                $cities_tour[$p]['is_person_use'] = ($val['is_person_use'] ?? '');

                $min_amounts = 0;
                if (!empty($val['cab_list_price']) && json_decode($val['cab_list_price'], true)) {
                    $getCabOrderDesc = json_decode($val['cab_list_price'], true);
                    $prices = array_column($getCabOrderDesc, 'price');
                    $min_amounts += (int)min($prices);
                }
                if (!empty($val['package_list_price']) && json_decode($val['package_list_price'], true)) {
                    foreach (json_decode($val['package_list_price'], true) as $kk => $val_s) {
                        if (in_array($val['use_date'], [1, 2, 3, 4, 5]) && ($val['is_person_use'] ?? 0) == 0) {
                            $min_amounts += $val_s['pprice'];
                        } elseif (($val['is_person_use'] ?? 0) == 1 && ($val_s['included'] ?? 0) == 1) {
                            $min_amounts += $val_s['pprice'];
                        }
                    }
                }

                $cities_tour[$p]['min_amount'] = (string)$min_amounts;

                // $cabs_lists = [];
                // $p_services = [];
                // if (!empty($val['cab_list_price']) && json_decode($val['cab_list_price'], true)) {
                //     foreach (json_decode($val['cab_list_price'], true) as $kk => $val_p) {
                //         $cabs_lists[$kk]['price'] = $val_p['price'];
                //         $cabs_lists[$kk]['cab_id'] = ($val_p['cab_id'] ?? 0);
                //         $cabs_lists[$kk]['min'] = ($val_p['min'] ?? 0);
                //         $cabs_lists[$kk]['max'] = ($val_p['max'] ?? 0);
                //         $getCabs = \App\Models\TourCab::where('id', ($val_p['cab_id'] ?? 0))->first();
                //         $cab_name = ucwords($getCabs['name'] ?? '');
                //         $cabs_lists[$kk]['cab_name'] = $cab_name;
                //         $cabs_lists[$kk]['seats'] = ($getCabs['seats'] ?? '');
                //         $cabs_lists[$kk]['image'] =  getValidImage(path: 'storage/app/public/tour_and_travels/cab/' . ($getCabs['image'] ?? ""), type: 'backend-product');
                //         $p_services[] = 'transport';
                //     }
                // }
                // $package_lists = [];
                // if (!empty($val['package_list_price']) && json_decode($val['package_list_price'], true)) {
                //     foreach (json_decode($val['package_list_price'], true) as $kk => $val_s) {
                //         $package_lists[$kk]['price'] = $val_s['pprice'];
                //         $package_lists[$kk]['package_id'] = $val_s['package_id'];
                //         $getpackage = \App\Models\TourPackage::where('id', $val_s['package_id'])->first();
                //         $package_lists[$kk]['package_name'] = ucwords($getpackage['name'] ?? '');
                //         $package_lists[$kk]['seats'] = ($getpackage['seats'] ?? '');
                //         $package_lists[$kk]['type'] = ($getpackage['type'] ?? '');
                //         $package_lists[$kk]['title'] = ($getpackage['title'] ?? '');
                //         $package_lists[$kk]['image'] = getValidImage(path: 'storage/app/public/tour_and_travels/package/' . ($getpackage['image'] ?? ""), type: 'backend-product');
                //         $p_services[] = ($getpackage['type'] ?? '');
                //     }
                // }
                // $cities_tour[$p]['cab_list'] = $cabs_lists;
                // $cities_tour[$p]['package_list'] = $package_lists;
                // if ($val['is_person_use'] == 1) {
                //     $p_services = [];
                //     $raw_package = (json_decode(($val['is_included_package'] ?? '{}'), true));
                //     $key_map = [
                //         'cab' => 'transport',
                //         'sightseen' => 'sightseen',
                //         'food' => 'food',
                //         'hotel' => 'hotel',
                //     ];

                //     $is_includeds_package = [];

                //     foreach ($raw_package as $key => $value) {
                //         if ($value == 1) {
                //             $mapped_key = $key_map[$key] ?? $key;
                //             $is_includeds_package[$mapped_key] = 1;
                //             $p_services[] = $mapped_key;
                //         }
                //     }
                // }
                // $cities_tour[$p]['services'] = array_values(array_unique($p_services));
                $cities_tour[$p]['image'] = getValidImage(path: 'storage/app/public/tour_and_travels/tour_visit/' . $val['tour_image'], type: 'backend-product');
                $p++;
            }
            return response()->json(['status' => 1, 'count' => count($cities_tour), 'data' => $cities_tour], 200);
        } else {
            return response()->json(['status' => 0, 'count' => 0, 'data' => []], 200);
        }
    }

    public function couponListType(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'type' => 'required|in:event,tour',
        ], [
            'type.required' => 'type is provide!',
        ]);
        if ($validator->fails()) {
            return response()->json(["status" => 0, "message" => "", 'errors' => Helpers::error_processor($validator), 'data' => []], 403);
        }

        if ($request->type == 'event' && !empty(\Illuminate\Support\Facades\Auth::guard('api')->user()->id)) {
            $userId = \Illuminate\Support\Facades\Auth::guard('api')->user()->id;
            $coupons = Coupon::where('status', 1)
                ->where('coupon_type', 'event_order')
                ->whereDate('start_date', '<=', date('Y-m-d'))
                ->whereDate('expire_date', '>=', date('Y-m-d'))
                ->whereRaw('`limit` > (SELECT COUNT(*) FROM `event_orders` WHERE `event_orders`.`coupon_id` = `coupons`.`id`)')
                ->whereNotExists(function ($query) use ($userId) {
                    $query->from('event_orders')
                        ->whereColumn('event_orders.coupon_id', 'coupons.id')
                        ->where('event_orders.user_id', $userId)
                        ->where('event_orders.transaction_status', 1);
                })
                ->get();
        } else if ($request->type == 'tour' && !empty(\Illuminate\Support\Facades\Auth::guard('api')->user()->id)) {
            $userId = \Illuminate\Support\Facades\Auth::guard('api')->user()->id;
            $coupons = Coupon::where('status', 1)
                ->where('coupon_type', 'tour_order')
                ->whereDate('start_date', '<=', date('Y-m-d'))
                ->whereDate('expire_date', '>=', date('Y-m-d'))
                ->whereRaw('`limit` > (SELECT COUNT(*) FROM `tour_order` WHERE `tour_order`.`coupon_id` = `coupons`.`id`)')
                ->whereNotExists(function ($query) use ($userId) {
                    $query->from('tour_order')
                        ->whereColumn('tour_order.coupon_id', 'coupons.id')
                        ->where('tour_order.user_id', $userId)
                        ->where('tour_order.amount_status', 1);
                })
                ->get();
        }
        if ($coupons && count($coupons) > 0) {
            return response()->json(['status' => 1, 'coupons' => $coupons], 200);
        } else {
            return response()->json(['status' => 0, 'coupons' => $coupons], 200);
        }
    }

    public function BookingOrderPolicy(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'user_id' =>  ['required', function ($attribute, $value, $fail) {
                if (!User::where('id', $value)->where('is_active', 1)->exists()) {
                    $fail('The selected user is invalid or inactive.');
                }
            },],
            "order_id" => ['required', function ($attribute, $value, $fail) use ($request) {
                if (!TourOrder::where('id', $value)->where('user_id', $request->user_id)->whereIn('status', [0, 1])->exists()) {
                    $fail('The selected Order is invalid or Refunded.');
                }
            },],
        ], [
            'user_id.required' => 'User Id is required!',
        ]);
        if ($validator->fails()) {
            return response()->json(["status" => 0, "message" => Helpers::error_processor($validator)[0], 'errors' => Helpers::error_processor($validator), 'data' => []], 403);
        }

        $all_booking = TourOrder::where('user_id', $request->user_id)->where('id', $request->order_id)->with(['Tour'])->first();
        $policy_array = [];
        if (!empty($all_booking['Tour']['tour_type'] ?? '')) {
            $getSpecial_tour = \App\Models\TourRefundPolicy::where('status', 1)->where('type', $all_booking['Tour']['tour_type'])->orderBy('day', 'desc')->get();
            $indexs = 0;
            foreach ($getSpecial_tour as $val) {
                $pickupDate = strtotime($all_booking['pickup_date'] . ' ' . $all_booking['pickup_time'] . ' -' . $val['day'] . ' hours');
                $createdAt = strtotime($all_booking['created_at']);
                if ($pickupDate > $createdAt) {
                    $policy_array[$indexs]['en_message'] = preg_replace('/\{\{\s*\$date\s*\}\}/', '<strong>' . date('d-m-Y h:i A', strtotime($all_booking['pickup_date'] . ' ' . $all_booking['pickup_time'] . ' -' . $val['day'] . ' hours')) . '</strong>', ($val['message'] ?? ''));
                    $hindi_tour =  $all_booking['Tour'] ? (TourVisits::find($all_booking['Tour']['id']))->translations()->pluck('value', 'key')->toArray() : [];
                    $policy_array[$indexs]['hi_message'] = preg_replace('/\{\{\s*\$date\s*\}\}/', '<strong>' . date('d-m-Y h:i A', strtotime($all_booking['pickup_date'] . ' ' . $all_booking['pickup_time'] . ' -' . $val['day'] . ' hours')) . '</strong>', ($hindi_tour['message'] ?? ''));
                    $policy_array[$indexs]['percentage'] = $val['percentage'] . "%";
                    $policy_array[$indexs]['amount'] = (($all_booking['amount'] * $val['percentage']) / 100);
                    $policy_array[$indexs]['date'] = date('d-m-Y h:i A', strtotime($all_booking['pickup_date'] . ' ' . $all_booking['pickup_time'] . ' -' . $val['day'] . ' hours'));
                    $indexs++;
                }
            }
        }

        if ($policy_array && count($policy_array) > 0) {
            return response()->json(['status' => 1, 'message' => "", 'data' => $policy_array], 200);
        } else {
            return response()->json(['status' => 0, "message" => "", 'data' => []], 200);
        }
    }

    public function UserTourOrderCancel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' =>  ['required', function ($attribute, $value, $fail) {
                if (!User::where('id', $value)->where('is_active', 1)->exists()) {
                    $fail('The selected user is invalid or inactive.');
                }
            },],
            "order_id" => ['required', function ($attribute, $value, $fail) use ($request) {
                if (!TourOrder::where('id', $value)->where('user_id', $request->user_id)->whereIn('status', [0, 1])->exists()) {
                    $fail('The selected Order is invalid or Refunded.');
                }
            },],
            "msg" => "required",
        ], [
            'user_id.required' => 'User Id is required!',
        ]);
        if ($validator->fails()) {
            return response()->json(["status" => 0, "message" => Helpers::error_processor($validator)[0]['message'], 'errors' => Helpers::error_processor($validator), 'data' => []], 403);
        }
        $tourOrder = TourOrder::where('id', ($request->order_id ?? ''))->with(['Tour'])->first();

        $getSpecial_tour = \App\Models\TourRefundPolicy::where('status', 1)->where('type', $tourOrder['Tour']['tour_type'] ?? '')->orderBy('day', 'desc')->get();
        $Amount_Pay = 0;
        $pickupTimestamp = strtotime($tourOrder['pickup_date'] . ' ' . $tourOrder['pickup_time']);
        if (!empty($getSpecial_tour) && count($getSpecial_tour) > 0) {
            foreach ($getSpecial_tour as $val) {
                $calculatedTimestamp = strtotime("-" . $val['day'] . " hours", $pickupTimestamp);
                $currentTimestamp = strtotime(date('Y-m-d h:i A'));
                if ($currentTimestamp <= $calculatedTimestamp) {
                    $Amount_Pay = ($tourOrder['amount'] * $val['percentage']) / 100;
                    break;
                }
            }
        }
        $getData = \App\Models\TourCancelTicket::where('order_id', $request->order_id)->first();
        if ($getData) {
            return response()->json(['status' => 0, 'message' => 'No Found', 'recode' => 0, 'data' => []], 200);
        }
        $ticket = new \App\Models\TourCancelTicket();
        $ticket->user_id = $request->user_id;
        $ticket->order_id = $request->order_id;
        $ticket->message = $request->msg;
        $ticket->status = 1; //0
        $ticket->save();

        User::where('id', $request->user_id)->update(['wallet_balance' => \Illuminate\Support\Facades\DB::raw('wallet_balance + ' . ($Amount_Pay ?? 0))]);

        $wallet_transaction = new \App\Models\WalletTransaction();
        $wallet_transaction->user_id = $request->user_id;
        $wallet_transaction->transaction_id = \Illuminate\Support\Str::uuid();
        $wallet_transaction->reference = 'tour order refund';
        $wallet_transaction->transaction_type = 'tour_order_refund';
        $wallet_transaction->balance = User::where('id', $request->user_id)->first()['wallet_balance'];
        $wallet_transaction->credit = ($Amount_Pay ?? 0);
        $wallet_transaction->save();

        TourOrder::where('id', $request->order_id)->update(['refund_status' => 1, 'status' => 2, 'refound_id' => "wallet", 'refund_amount' => ($Amount_Pay ?? 0), 'cab_assign' => 0, 'traveller_id' => ($tourOrder['cab_assign'] ?? 0), 'refund_date' => date('Y-m-d H:i:s'), 'refund_query_id' => $ticket->id]);

        $message_data['orderId'] = ($tourOrder['order_id'] ?? '');
        $message_data['title_name'] = ($tourOrder['Tour']['tour_name'] ?? '');
        $message_data['booking_date'] = date('d M,Y', strtotime($tourOrder['pickup_date'] ?? ''));
        $message_data['time'] = ($tourOrder['Tour']['pickup_time'] ?? '');
        $message_data['place_name'] = ($tourOrder['Tour']['pickup_address'] ?? '');
        $message_data['tour_type'] = ucwords(str_replace('_', ' ', (($tourOrder['Tour']['tour_type'] ?? ''))));
        $message_data['final_amount'] = webCurrencyConverter(amount: (float)$Amount_Pay ?? 0);
        $message_data['refund_amount'] = webCurrencyConverter(amount: (float)$Amount_Pay ?? 0);
        $message_data['refund_date'] = date('d M,Y h:i A');
        $message_data['customer_id'] =  $request->user_id;
        $message_data['reject_reason'] = $request->msg;
        \App\Utils\Helpers::whatsappMessage('tour', 'Tour Canceled', $message_data);
        return response()->json(['status' => 1, "message" => "Refund Successfully", 'data' => []], 200);
    }


    public function TourOrderInvoiceDownload(Request $request, $order_id)
    {
        try {
            $tourOrders = \App\Models\TourOrder::where('id', $order_id)->with(['Tour', 'userData', 'company'])->first();
            if (!$tourOrders) {
                return response()->json([
                    "status" => 0,
                    "message" => "Tour order not found.",
                    "data" => []
                ], 403);
            }
            $refund_policy = \App\Models\TourRefundPolicy::where('status', 1)->where('type', ($tourOrders['Tour']['tour_type'] ?? ""))->orderBy('id', 'asc')->get();
            $mpdf_view  = \Illuminate\Support\Facades\View::make('web-views.tour.paid-invoice', compact('tourOrders', 'refund_policy'));
            \App\Utils\Helpers::gen_mpdf($mpdf_view, 'tour_order_', $order_id);
            return response()->json(["status" => 1, "message" => "Invoice generated successfully."]);
        } catch (\Exception $e) {
            return response()->json([
                "status" => 0,
                "message" => "An error occurred.",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    public function TravellerWithdrawal(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cab_assign' => ['required', function ($attribute, $value, $fail) {
                if (!TourAndTravel::where('id', $value)->where('is_approve', 1)->where('status', 1)->exists()) {
                    $fail('The selected cab Id is invalid or inactive.');
                }
            },],
            'req_amount' => 'required|numeric',
            'holder_name'     => 'nullable|required_without:upi_code',
            'bank_name'       => 'nullable|required_without:upi_code',
            'ifsc_code'       => 'nullable|required_without:upi_code',
            'account_number'  => 'nullable|required_without:upi_code',
            'upi_code'        => 'nullable|required_without:holder_name,bank_name,ifsc_code,account_number',
        ], [
            'cab_assign.required' => 'Cab Id is Empty!',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'recode' => 0, 'data' => [], 'errors' => Helpers::error_processor($validator)], 200);
        }

        $getOld_pending_req = \App\Models\TourAndTravel::where('id', $request->cab_assign)->first();
        $check_request_old = \App\Models\WithdrawalAmountHistory::where('vendor_id', $request->cab_assign)->where('type', 'tour')->where('status', 0)->first();
        if ($request['req_amount'] > $getOld_pending_req['wallet_amount']) {
            return response()->json(["status" => 0, "message" => "Your wallet balance is not valid"]);
        } elseif ($check_request_old) {
            return response()->json(["status" => 0, "message" => "Your amount request has already been sent. Please wait for further processing."]);
        }
        if ($request['req_amount'] <= $getOld_pending_req['wallet_amount'] && $getOld_pending_req['withdrawal_pending_amount'] <= 0) {
            $withdrawal  =  new \App\Models\WithdrawalAmountHistory();
            $withdrawal->type = "tour";
            $withdrawal->vendor_id = $request->cab_assign;
            $withdrawal->ex_id = (($request->ex_id) ? $request->ex_id : "");
            $withdrawal->holder_name = $request['holder_name'] ?? "";
            $withdrawal->bank_name = $request['bank_name'] ?? "";
            $withdrawal->ifsc_code = $request['ifsc_code'] ?? "";
            $withdrawal->account_number = $request['account_number'] ?? "";
            $withdrawal->upi_code = $request['upi_code'] ?? '';
            $withdrawal->old_wallet_amount = $getOld_pending_req['wallet_amount'] ?? 0;
            $withdrawal->req_amount = $request['req_amount'] ?? 0;
            $withdrawal->save();
            if ($request->ex_id) {
                \App\Models\TourOrder::where('id', $request->ex_id)->update(['advance_withdrawal_amount' => $request['req_amount']]);
            } else {
                \App\Models\TourAndTravel::where('id', $request->cab_assign)->update(['withdrawal_pending_amount' => $request['req_amount']]);
            }
            return response()->json(["status" => 1, "message" => translate('Payment_request_sent_successfully')]);
        } else {
            return response()->json(["status" => 0, "message" => "Your amount request has already been sent. Please wait for further processing."]);
        }
    }

    public function TravellerWithdrawalHistory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cab_assign' => ['required', function ($attribute, $value, $fail) {
                if (!TourAndTravel::where('id', $value)->where('is_approve', 1)->where('status', 1)->exists()) {
                    $fail('The selected cab Id is invalid or inactive.');
                }
            },],
        ], [
            'cab_assign.required' => 'Cab Id is Empty!',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'recode' => 0, 'data' => [], 'errors' => Helpers::error_processor($validator)], 200);
        }

        $getdata = \App\Models\WithdrawalAmountHistory::where('vendor_id', $request->cab_assign)->where('type', 'tour')->with(['Tour'])->get();
        if ($getdata && count($getdata) > 0) {
            $show_datas = [];
            $key = 0;
            foreach ($getdata as $value) {
                $show_datas[$key]['id'] = $value['id'];
                $show_datas[$key]['old_wallet_amount'] = $value['old_wallet_amount'];
                $show_datas[$key]['req_amount'] = $value['req_amount'];
                $show_datas[$key]['approval_amount'] = $value['approval_amount'];
                $show_datas[$key]['message'] = $value['message'];
                $show_datas[$key]['status'] = $value['status'];
                $show_datas[$key]['transcation_id'] = $value['transcation_id'];
                $show_datas[$key]['payment_method'] = $value['payment_method'];
                $show_datas[$key]['upi_code'] = $value['upi_code'];
                $show_datas[$key]['bank_name'] = $value['bank_name'];
                $show_datas[$key]['holder_name'] = $value['holder_name'];
                $show_datas[$key]['ifsc_code'] = $value['ifsc_code'];
                $show_datas[$key]['account_number'] = $value['account_number'];
                $show_datas[$key]['created_at'] = $value['created_at'];
                $key++;
            }
            return response()->json(["status" => 1, "message" => "Request Transaction History.", 'data' => $show_datas]);
        } else {
            return response()->json(["status" => 0, "message" => "Not Found Trasaction.", 'data' => []]);
        }
    }

    public function TravellerOrderAmountHistory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cab_assign' => ['required', function ($attribute, $value, $fail) {
                if (!TourAndTravel::where('id', $value)->where('is_approve', 1)->where('status', 1)->exists()) {
                    $fail('The selected cab Id is invalid or inactive.');
                }
            },],
        ], [
            'cab_assign.required' => 'Cab Id is Empty!',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'recode' => 0, 'data' => [], 'errors' => Helpers::error_processor($validator)], 200);
        }

        $getdata = \App\Models\WithdrawalAmountHistory::where('vendor_id', $request->cab_assign)->whereIn('type', ['tour_order', 'tour'])->with(['TourVisit', 'Tour'])->get();
        if ($getdata && count($getdata) > 0) {
            $show_datas = [];
            $key = 0;
            foreach ($getdata as $value) {
                $show_datas[$key]['id'] = $value['id'];
                $show_datas[$key]['old_wallet_amount'] = $value['old_wallet_amount'];
                $show_datas[$key]['available_amount'] = $value['req_amount'];
                $hindi_tour = ($value['TourVisit']['Tour'] ?? "") ? $value['TourVisit']['Tour']->translations()->pluck('value', 'key')->toArray() : [];
                $show_datas[$key]['tour_id'] = $value['TourVisit']['Tour']['id'] ?? "";
                $show_datas[$key]['en_tour_name'] = $value['TourVisit']['Tour']['tour_name'] ?? "";
                $show_datas[$key]['hi_tour_name'] = $hindi_tour['tour_name'] ?? "";
                $show_datas[$key]['order_id'] = $value['TourVisit']['id'] ?? "";
                $show_datas[$key]['created_at'] = $value['created_at'];

                if ($value['type'] == 'tour_order') {
                    $show_datas[$key]['credit_debit'] = 'Credit';
                } else {
                    $show_datas[$key]['credit_debit'] = 'Debit';
                }
                $show_datas[$key]['type'] = $value['type'];
                $show_datas[$key]['approval_amount'] = (float)$value['approval_amount'];
                $show_datas[$key]['message'] = (string)$value['message'];
                $show_datas[$key]['status'] = $value['status'];
                $show_datas[$key]['transcation_id'] = (string)$value['transcation_id'];
                $show_datas[$key]['payment_method'] = (string)$value['payment_method'];
                $show_datas[$key]['upi_code'] = (string)$value['upi_code'];
                $show_datas[$key]['bank_name'] = (string)$value['bank_name'];
                $show_datas[$key]['holder_name'] = (string)$value['holder_name'];
                $show_datas[$key]['ifsc_code'] = (string)$value['ifsc_code'];
                $show_datas[$key]['account_number'] = (string)$value['account_number'];
                $key++;
            }
            return response()->json(["status" => 1, "message" => "Request Transaction History.", 'data' => $show_datas]);
        } else {
            return response()->json(["status" => 0, "message" => "Not Found Trasaction.", 'data' => []]);
        }
    }

    public function BookingTabTourCalculations(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tour_id' =>  ['required', function ($attribute, $value, $fail) {
                if (!TourVisits::where(function ($query) use ($value) {
                    $query->where('id', $value)
                        ->orWhere('slug', $value);
                })->where('status', 1)->exists()) {
                    $fail('The selected tour is invalid or inactive.');
                }
            },],
            "lead_id" => "required",
        ], [
            'tour_id.required' => 'Tour Id is Empty!',
            "lead_id" => "Lead Id is Empty!",
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'recode' => 0, 'data' => [], 'errors' => Helpers::error_processor($validator)], 200);
        }
        $special_tour = TourVisits::where(function ($query) use ($request) {
            $query->where('id', $request->tour_id)
                ->orWhere('slug', $request->tour_id);
        })->first();
        $tourLeads = TourLeads::where('id', $request->lead_id)->first();
        $perQTY = 0;
        $perQTY_amount = 0;
        if ($special_tour && $tourLeads) {
            $transportUsePercent = \App\Models\ServiceTax::find(1)['tour_transport_tax'] ?? 1;
            $gstget = \App\Models\ServiceTax::find(1)['tour_tax'] ?? 1;
            if ($request->item && count($request->item) > 0) {
                if ($special_tour['is_person_use'] == 1) {
                    $oldIds = [];
                    $request['item'] = collect($request->item)->map(function ($item, $index) use ($gstget, &$oldIds, $transportUsePercent, &$perQTY, &$perQTY_amount) {
                        if ($item['type'] === 'per_head') {
                            $withoutGst = (($item['price'] * $gstget) / 100);
                            $fullPrice = ($item['price'] + $withoutGst);
                            if (!empty($item['qty']) && $item['qty'] > 0) {
                                $pricen = ceil(($fullPrice / $item['qty']) * 100) / 100;
                            } else {
                                $pricen = 0;
                            }
                            $item['man_price'] = $pricen;
                            $item['pprice'] = $fullPrice;
                            $item['gst'] = $gstget;
                            $item['tax_price'] = $withoutGst;
                            $item['total_price'] = $fullPrice;
                            $item['title'] = "Per Head";
                            $item['id'] = $item['id'];
                            $perQTY = $item['qty'];
                            $perQTY_amount += $fullPrice ?? 0;
                        } elseif ((in_array($item['type'], ['hotel', 'other', 'foods']) || \Illuminate\Support\Str::startsWith($item['type'], 'other')) && !in_array($item['id'], $oldIds)) {
                            $getTypes = \App\Models\TourPackage::where('id', $item['id'])->first();
                            if (!empty($item['qty']) && $item['qty'] > 0) {
                                $pricen = ceil(($item['price'] / $item['qty']) * 100) / 100;
                            } else {
                                $pricen = $item['price'];
                            }
                            $withoutGst = (($item['price'] * $gstget) / 100);
                            $fullPrice = ($item['price'] + $withoutGst);
                            $newDatas = [
                                'type'       =>  $getTypes['type'] ?? 'other',
                                'man_price'   => $pricen,
                                'price'      => $item['price'],
                                'pprice'      => ($item['price2']),
                                'qty'      => $item['qty'],
                                'gst'         => $gstget,
                                'tax_price'   => $withoutGst,
                                'total_price' => ($fullPrice),
                                'title'       => $getTypes['type'] ?? "",
                                'id'          => $item['id'],
                            ];
                            $oldIds[] = $item['id'];
                            $perQTY_amount += $fullPrice ?? 0;
                            return $newDatas;
                        } elseif (in_array($item['type'], ['transport', 'ex_distance'])) {
                            if ($item['price'] > 0) {
                                $withoutGst = (($item['price'] * $gstget) / 100);
                                $fullPrice = ($item['price'] + $withoutGst);
                                if (!empty($item['qty']) && $item['qty'] > 0) {
                                    $pricen = ceil(($fullPrice / $item['qty']) * 100) / 100;
                                } else {
                                    $pricen = 0;
                                }
                                $item['man_price'] = $pricen;
                                $item['pprice'] = $fullPrice;
                                $item['tax'] = $gstget ?? 0;
                                $item['tax_price'] = $withoutGst;
                                $item['total_price'] = $fullPrice;
                                $item['title'] = "Ex Transport";
                                $perQTY_amount += $fullPrice;
                            } else {
                                return null;
                            }
                        }
                        return $item;
                    })->filter()->values()->toArray();
                } else {
                    $request['item'] = collect($request->item)->map(function ($item) use ($gstget, $transportUsePercent, &$perQTY, &$perQTY_amount, $special_tour) {
                        if ($item['type'] == 'cab') {
                            $withoutGst = (($item['price'] * $gstget) / 100);
                            $fullPrice = ($item['price'] + $withoutGst);
                            if (!empty($item['qty']) && $item['qty'] > 0) {
                                $pricen = ceil(($fullPrice / $item['qty']) * 100) / 100;
                            } else {
                                $pricen = 0;
                            }
                            $item['man_price'] = $pricen;
                            $item['pprice'] = $fullPrice;
                            $item['tax'] = $gstget;
                            $item['tax_price'] = $withoutGst;
                            $item['total_price'] = $fullPrice;
                            $item['title'] = "Cab";
                            $item['id'] = $item['id'];
                            $perQTY = $item['qty'];
                            $perQTY_amount += $fullPrice ?? 0;
                        } elseif ($item['type'] == 'hotel' || $item['type'] == 'other' || $item['type'] == 'food' || $item['type'] == 'foods' || \Illuminate\Support\Str::startsWith($item['type'], 'other')) {

                            $withoutGst = (($item['price'] * $gstget) / 100);
                            $fullPrice = ($item['price'] + $withoutGst);

                            $item['man_price'] = (($special_tour['use_date'] == 0) ? (ceil(($item['price2'] / $item['qty']) * 100) / 100) : 0);
                            $item['pprice'] = (($special_tour['use_date'] == 0) ? $item['price2'] : 0);
                            $item['price'] = (($special_tour['use_date'] == 0) ? $item['price2'] : 0);
                            $item['tax'] = (($special_tour['use_date'] == 0) ? $gstget : 0);
                            $item['tax_price'] = (($special_tour['use_date'] == 0) ? $withoutGst : 0);
                            $item['total_price'] = (($special_tour['use_date'] == 0) ? $fullPrice : 0);
                            $item['title'] = ucwords($item['type']);
                            $item['id'] = $item['id'];
                            $perQTY_amount += (($special_tour['use_date'] == 0) ? $fullPrice : 0);
                        } elseif (in_array(trim($item['type']), ['transport', 'ex_distance'])) {
                            if ($item['price'] > 0) {
                                if (isset($item['ExChargeAmount']) && ($item['ExChargeAmount'] ?? 0)) {
                                    $item['price2'] += $item['ExChargeAmount'];
                                }
                                $withoutGst = (($item['price'] * $gstget) / 100);
                                $fullPrice = ($item['price'] + $withoutGst);
                                if (!empty($item['qty']) && $item['qty'] > 0) {
                                    $pricen = ceil(($fullPrice / $item['qty']) * 100) / 100;
                                } else {
                                    $pricen = 0;
                                }
                                $item['man_price'] = $pricen;
                                $item['pprice'] = $item['price2'];
                                $item['tax'] = $gstget;
                                $item['tax_price'] = $withoutGst;
                                $item['total_price'] = $fullPrice;
                                $item['title'] = "Ex Transport";
                                $perQTY_amount += $fullPrice;
                            } else {
                                return null;
                            }
                        }
                        return $item;
                    })->filter()->values()->toArray();
                }
            }
            $tourLeads->booking_package = json_encode($request->item);
            $tourLeads->part_payment = $request->part_payment_type ?? "full";
            $tourLeads->coupan_amount = $request->coupan_amount ?? '';
            $tourLeads->coupon_id = $request->coupon_id ?? 0;
            $tourLeads->pickup_address = $request->pickup_address;
            $tourLeads->pickup_date = $request->pickup_date;
            $tourLeads->pickup_time = $request->pickup_time;
            $tourLeads->pickup_long = $request->log ?? '';
            $tourLeads->pickup_lat = $request->lat ?? '';
            $tourLeads->qty = $perQTY;
            $tourLeads->amount = ((($request->part_payment_type ?? "full") == 'full') ? $perQTY_amount : ($perQTY_amount / 2));
            $tourLeads->save();
            return response()->json(["status" => 1, "message" => "Update Data.", 'data' => $request->item], 200);
        } else {
            return response()->json(["status" => 0, "message" => "Not Found Data.", 'data' => []], 200);
        }
    }

    // vendor
    public function all_vendor()
    {
        $vendors = Seller::join('tour_and_travels', 'sellers.relation_id', '=', 'tour_and_travels.id')
            ->leftJoin('tour_visits', 'tour_and_travels.id', '=', 'tour_visits.created_id')
            // ->leftJoin('tour_review', 'tour_visits.id', '=', 'tour_review.tour_id')
            ->leftJoin('tour_review', function ($join) {
                $join->on('tour_visits.id', '=', 'tour_review.tour_id')
                    ->where('tour_review.status', 1);
            })
            ->where('sellers.type', 'tour')
            ->where('sellers.status', 'approved')
            // ->where('sellers.verify_status', 1)
            ->where('tour_and_travels.status', 1)
            ->where('tour_and_travels.is_approve', 1)
            // ->where('tour_review.status',1)
            ->select(
                'sellers.id as seller_id',
                'tour_and_travels.company_name',
                'tour_and_travels.image',
                'tour_and_travels.banner',
                // 'tour_and_travels.*',
                'tour_and_travels.id as tour_id',
                // 'tour_visits.*',
                // 'tour_and_travels.image as tour_image',
                DB::raw('COUNT(DISTINCT CASE WHEN tour_visits.status = 1 THEN tour_visits.id END) as tour_count'),
                DB::raw('COUNT(tour_review.id) as review_count'),
                DB::raw('SUM(tour_review.star) as review_sum'),
                DB::raw('IFNULL(SUM(tour_review.star) / NULLIF(COUNT(tour_review.id), 0), 0) as avg_rating')
            )
            ->groupBy('sellers.id', 'tour_and_travels.id', 'tour_and_travels.image') // include any selected columns not aggregated
            ->get()
            ->map(function ($vendor) {
                $vendor->image = getValidImage(
                    path: 'storage/app/public/tour_and_travels/doc/' . $vendor->image,
                    type: 'backend-product'
                );

                $vendor->banner = getValidImage(
                    path: 'storage/app/public/tour_and_travels/doc/' . $vendor->banner,
                    type: 'backend-product'
                );

                return $vendor;
            });

        if ($vendors) {
            return response()->json(["status" => 1, "message" => "Got Data.", 'data' => $vendors], 200);
        } else {
            return response()->json(["status" => 0, "message" => "Not Found Data.", 'data' => []], 200);
        }
    }

    public function vendor_tour(Request $request)
    {
        if (!$request->has('id')) {
            return response()->json(["status" => 0, "message" => "Tour id is required."], 200);
        }
        $id = $request->id;
        $vendor = Seller::join('tour_and_travels', 'sellers.relation_id', '=', 'tour_and_travels.id')
            ->leftJoin('tour_visits', 'tour_and_travels.id', '=', 'tour_visits.created_id')
            ->leftJoin('tour_review', function ($join) {
                $join->on('tour_visits.id', '=', 'tour_review.tour_id')
                    ->where('tour_review.status', 1);
            })
            ->where('sellers.type', 'tour')
            ->where('sellers.status', 'approved')
            ->where('tour_and_travels.id', $id)
            ->where('tour_and_travels.status', 1)
            ->where('tour_and_travels.is_approve', 1)
            ->select(
                'sellers.id as seller_id',
                'tour_and_travels.company_name',
                'tour_and_travels.image',
                'tour_and_travels.banner',
                'tour_and_travels.id as tour_id',
                DB::raw('COUNT(DISTINCT CASE WHEN tour_visits.status = 1 THEN tour_visits.id END) as tour_count'),
                DB::raw('COUNT(tour_review.id) as review_count'),
                DB::raw('SUM(tour_review.star) as review_sum'),
                DB::raw('IFNULL(SUM(tour_review.star) / NULLIF(COUNT(tour_review.id), 0), 0) as avg_rating')
            )
            ->first();

        $getDataAll = TourVisits::select('id', 'tour_name as en_tour_name', 'number_of_day', 'number_of_night', 'is_person_use', 'is_included_package', "cab_list_price", "package_list_price", 'percentage_off', 'plan_type as plan_type_name', 'tour_image', 'use_date')->where('status', 1)->where('created_id', $id)->where(function ($query) {
            $query->whereIn('use_date', [0, 2, 3, 4])
                ->orWhere(function ($query) {
                    $query->where('use_date', 1)
                        ->where(function ($subQuery) {
                            $subQuery->whereIn('customized_type', ['', '0'])
                                ->whereNotNull('startandend_date')
                                ->whereRaw('? < STR_TO_DATE(SUBSTRING_INDEX(startandend_date, " - ", 1), "%Y-%m-%d")', [date('Y-m-d')]);
                        })
                        ->orWhere(function ($subQuery) {
                            $subQuery->whereIn('customized_type', [1, 2, 3]);
                        });
                });
        })->orderBy('id', 'desc')->withCount('TourOrderReview')
            ->withAvg('review', 'star')->withTourCheck()->get()->makeHidden(['translations'])->map(function ($event) {
                $event->tour_image = $event->tour_image ? (getValidImage(path: 'storage/app/public/tour_and_travels/tour_visit/' . ($event->tour_image ?? ""), type: 'backend-product')) : "";
                $transEvent = [];
                if ($event) {
                    $transEvent = $event->translations()->pluck('value', 'key')->toArray();
                }
                $event->hi_tour_name = ($transEvent['tour_name'] ?? "");
                $p_services = [];
                if (!empty($event->cab_list_price) && json_decode($event->cab_list_price ?? "[]", true)) {
                    foreach (json_decode($event->cab_list_price, true) as $kk => $val_p) {
                        $p_services[] = 'transport';
                    }
                }
                $package_lists = [];
                $cab_priceAddon = 0;
                if (!empty($event->package_list_price) && json_decode($event->package_list_price ?? "[]", true)) {
                    foreach (json_decode($event->package_list_price, true) as $kk => $val_s) {
                        if ($event->use_date != 0 && $event->is_person_use == 0) {
                            $cab_priceAddon += ($getpackage['pprice'] ?? '');
                        }
                        $getpackage = \App\Models\TourPackage::where('id', $val_s['package_id'])->first();
                        $p_services[] = ($getpackage['type'] ?? '');
                    }
                }
                if ($event->is_person_use == 1) {
                    $p_services = [];
                    $raw_package = (json_decode(($event->is_included_package ?? '{}'), true));
                    $key_map = [
                        'cab' => 'transport',
                        'sightseen' => 'sightseen',
                        'food' => 'food',
                        'hotel' => 'hotel',
                    ];

                    $is_includeds_package = [];

                    foreach ($raw_package as $key1 => $value) {
                        if ($value == 1) {
                            $mapped_key = $key_map[$key1] ?? $key1;
                            $is_includeds_package[$mapped_key] = 1;
                            $p_services[] = $mapped_key;
                        }
                    }
                }
                $event->is_included_package = array_values(array_unique($p_services));
                $newData = json_decode($event->cab_list_price ?? "[]", true);
                $totalPrice = 0;
                if (!empty($newData)) {
                    $prices = array_column($newData, 'price');
                    $totalPrice = min($prices);
                }
                $event->off_total_price = (int)$totalPrice + (int)($cab_priceAddon);
                $event->min_total_price = (int)($event->off_total_price / (1 - (($event->percentage_off ?? 0) / 100)));
                $plans = [
                    0 => ['name' => 'Basic', 'style' => '#495057'],
                    1 => ['name' => 'Standard', 'style' => '#218838'],
                    2 => ['name' => 'Premium', 'style' => '#0056b3'],
                    3 => ['name' => 'Golden', 'style' => '#FFA500'],
                    4 => ['name' => 'Luxury', 'style' => '#b02a37'],
                ];

                $selectedPlan = $event->plan_type_name ?? 0;
                $event->plan_type_name = $plans[$selectedPlan]['name'];
                $event->plan_type_color = $plans[$selectedPlan]['style'];
                unset($event->package_list_price);
                unset($event->cab_list_price);
                unset($event->cab_data);
                return $event;
            });;
        if ($vendor && $getDataAll) {
            $vendor['image'] = getValidImage(path: 'storage/app/public/tour_and_travels/doc/' . ($vendor['image'] ?? ""), type: 'backend-logo');
            $vendor['banner'] = getValidImage(path: 'storage/app/public/tour_and_travels/doc/' . ($vendor['banner'] ?? ""), type: 'backend-logo');
            return response()->json(["status" => 1, "message" => "Got Data.", 'vendor' => $vendor, 'data' => $getDataAll], 200);
        } else {
            return response()->json(["status" => 0, "message" => "Not Found Data.", 'vendor' => '', 'data' => []], 200);
        }
    }
}
