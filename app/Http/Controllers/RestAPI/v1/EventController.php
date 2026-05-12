<?php

namespace App\Http\Controllers\RestAPI\v1;

use App\Contracts\Repositories\EventCategoryRepositoryInterface;
use App\Contracts\Repositories\EventPackageRepositoryInterface;
use App\Contracts\Repositories\EventsRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Models\EventOrder;
use App\Models\EventOrderItems;
use App\Models\Events;
use App\Models\EventsReview;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Utils\Helpers;
use App\Utils\ImageManager;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Library\Payer;
use App\Library\Payment as PaymentInfo;
use App\Traits\Payment;
use App\Library\Receiver;
use App\Models\BusinessSetting;
use App\Models\Coupon;
use App\Models\Currency;
use App\Models\EventApproTransaction;
use App\Models\EventCategory;
use App\Models\EventInterest;
use App\Models\EventLeads;
use App\Models\EventOrganizer;
use App\Models\WalletTransaction;
use DateTime;

class EventController extends Controller
{

    public function __construct(
        private readonly EventsRepositoryInterface       $EventsRepo,
        private readonly EventCategoryRepositoryInterface       $EventcategoryRepo,
        private readonly EventPackageRepositoryInterface       $EvpackageRepo,
    ) {}

    public function EventList(Request $request)
    {

        $organizer = $request->get('organizer') ?? null;
        $prices = $request->get('price') ?? null;
        $language_search = $request->get('language') ?? null;
        $venue_data = $request->get('venue_data') ?? null;
        $category_id = $request->get('category_id') ?? null;
        $upcoming = $request->get('upcoming') ?? null;

        // $getData =  $this->EventsRepo->getListWhere(orderBy: ['id' => 'desc'], searchValue: $request->get('searchValue'), filters: ['active_event' => 1, 'organizer' => $organizer, 'price' => $prices, 'language' => $language, 'venue_data' => $venue_data, 'category_id' => $category_id, 'is_approve' => 1, 'status' => 1, 'upcoming' => $upcoming], dataLimit: 'all', relations: ['organizers', 'categorys', 'eventArtist', 'translations']);

        $lat = $request->get('latitude') ?? "";
        $long = $request->get('longitude') ?? "";
        $radius = 50;
        $upcomingEvents =  \App\Models\Events::when(isset($request['price']), function ($query) use ($request) {
            $prices = $request['price'] ?? [];
            $query->where(function ($q) use ($prices) {
                foreach ($prices as $priceRange) {
                    list($minPrice, $maxPrice) = explode('-', $priceRange);
                    $q->orWhere(function ($subQuery) use ($minPrice, $maxPrice) {
                        for ($j = 0; $j < 30; $j++) {
                            for ($i = 0; $i < 30; $i++) {
                                $subQuery->orWhereRaw("
                                    CAST(JSON_UNQUOTE(JSON_EXTRACT(all_venue_data ,'$[$j].package_list[$i].price_no')) AS UNSIGNED) BETWEEN ? AND ?
                                ", [$minPrice, $maxPrice]);
                            }
                        }
                    });
                }
            });
        })->when(isset($request['language']), function ($query) use ($request) {
            $language = $request['language'];
            $query->where(function ($q) use ($language) {
                foreach ($language as $venueData) {
                    $q->orWhereRaw("language = ?  ", [$venueData]);
                }
            });
        })->when(isset($request['category_id']), function ($query) use ($request) {
            $category_id = $request['category_id'];
            $query->where(function ($q) use ($category_id) {
                foreach ($category_id as $category) {
                    $q->orWhereRaw("category_id = ?  ", [$category]);
                }
            });
        })->when(isset($request['organizer']), function ($query) use ($request) {
            return $query->where('organizer_by', $request['organizer']);
        })->where('is_approve', 1)
            ->where('status', 1)
            ->whereRaw("
            STR_TO_DATE(
                CONCAT(
                    JSON_UNQUOTE(JSON_EXTRACT(all_venue_data, CONCAT('$[', JSON_LENGTH(all_venue_data) - 1, '].date'))), 
                    ' ',
                    JSON_UNQUOTE(JSON_EXTRACT(all_venue_data, CONCAT('$[', JSON_LENGTH(all_venue_data) - 1, '].start_time')))
                ), '%Y-%m-%d %h:%i %p'
            ) >= STR_TO_DATE(?, '%Y-%m-%d %h:%i %p')
        ", [date('Y-m-d g:i A')])
            ->with(['categorys'])->withCount('EventOrderReview')
            ->withAvg('review', 'star')
            ->orderBy('id', 'desc')
            ->get();
        $filteredEvents = collect();
        foreach ($upcomingEvents as $event) {
            $allVenueData = json_decode($event->all_venue_data, true);
            foreach ($allVenueData as $venue) {
                if (isset($venue['en_event_lat']) && isset($venue['en_event_long'])) {
                    if ((!empty($lat) && !empty($long))) {
                        $latFrom = deg2rad($lat);
                        $lonFrom = deg2rad($long);
                        $latTo = deg2rad($venue['en_event_lat']);
                        $lonTo = deg2rad($venue['en_event_long']);
                        $earthRadius = 6371;
                        $dLat = $latTo - $latFrom;
                        $dLon = $lonTo - $lonFrom;
                        $a = sin($dLat / 2) * sin($dLat / 2) +
                            cos($latFrom) * cos($latTo) *
                            sin($dLon / 2) * sin($dLon / 2);
                        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
                        $distance = $earthRadius * $c;
                        if ($distance <= $radius) {
                            $filteredEvents->push($event);
                            break;
                        }
                    } else {
                        $filteredEvents->push($event);
                        break;
                    }
                }
            }
        }
        $filteredRunningEvents = collect();
        $eventData = $filteredEvents->merge($filteredRunningEvents)->sortByDesc('id');

        $eventList = [];
        $eventList1 = [];
        if ($eventData) {
            foreach ($eventData as $newp) {
                if (!empty($newp['all_venue_data']) && json_decode($newp['all_venue_data'], true)) {
                    $venuePrices = [];
                    foreach (json_decode($newp['all_venue_data'], true) as $check) {
                        $currentDateTime = new DateTime();
                        $eventDateTime = DateTime::createFromFormat('d-m-Y h:i A', date('d-m-Y', strtotime($check['date'])) . ' ' . date('h:i A', strtotime($check['start_time'])));
                        if ($eventDateTime && $eventDateTime > $currentDateTime) {
                            $date_upcommining = date('d M,Y', strtotime($check['date']));
                            $time_upcommining = date('h:i A', strtotime($check['start_time']));
                            if (!empty($check['package_list'])) {
                                $venuePrices = array_filter(array_column($check['package_list'], 'price_no'), 'is_numeric');
                            }
                            break;
                        }

                        $date_upcommining1 = date('d M,Y', strtotime($check['date']));
                        $time_upcommining1 = date('h:i A', strtotime($check['start_time']));
                    }
                    $eventList[] = [
                        'event' => $newp,
                        'date' => $eventDateTime->format('Y-m-d H:i:s'),
                        'formatted_date' => ($date_upcommining == '') ? $date_upcommining1 : ($date_upcommining ?? ""),
                        'formatted_time' => ($time_upcommining == '') ? $time_upcommining1 : ($time_upcommining ?? ""),
                        'venuePrices' => $venuePrices,
                        'informational_status' => $newp['informational_status'] ?? 1
                    ];
                    $newp['event_dates'] =  $eventDateTime->format('Y-m-d H:i:s');
                    $newp['formatted_date'] = ($date_upcommining == '') ? $date_upcommining1 : ($date_upcommining ?? "");
                    $eventList1[] = $newp;
                }
            }
        }
        usort($eventList, function ($a, $b) {
            return strtotime($a['date']) - strtotime($b['date']);
        });
        usort($eventList1, function ($a, $b) {
            return strtotime($a['event_dates']) - strtotime($b['event_dates']);
        });


        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        if (isset($eventList1) && count($eventList1) > 0) {
            $events_translation = [];
            $Index = 0;
            foreach ($eventList1 as $key => $val) {
                // $events_translation = $eventList1->map(function ($val) use ($languages, $defaultLanguage) {
                $translationKeys = ['event_name', 'event_about', 'event_schedule', 'event_attend', 'event_team_condition'];
                $translations = $val->translations()->pluck('value', 'key')->toArray();
                $translate = [];
                foreach ($languages as $language) {
                    foreach ($translationKeys as $translationKey) {
                        $lang = $language === 'in' ? 'hi' : $language;
                        $events_translation[$Index]["{$lang}_{$translationKey}"] = ($language == $defaultLanguage)  ? $val[$translationKey]  : ($translations[$translationKey] ?? '');
                    }
                }
                $events_translation[$Index]['id'] = $val['id'];
                $events_translation[$Index]['slug'] = $val['slug'];
                $events_translation[$Index]['organizer_by'] = $val['organizer_by'];
                $events_translation[$Index]['age_group'] = $val['age_group'];

                $events_translation[$Index]['review_count'] = (string)$val->event_order_review_count ?? 0;
                $events_translation[$Index]['review_avg_star'] = (string)round($val->review_avg_star ?? 0, 1);

                $events_translation[$Index]['language'] = $val['language'];
                $events_translation[$Index]['days'] = $val['days'];
                $events_translation[$Index]['start_to_end_date'] = $val['start_to_end_date'];
                $events_translation[$Index]['event_dates'] = $val['event_dates'];
                $events_translation[$Index]['formatted_date'] = $val['formatted_date'];
                $events_translation[$Index]['informational_status'] = $val['informational_status'] ?? 1;
                $events_translation[$Index]['youtube_video'] = $val['youtube_video'];
                $events_translation[$Index]['all_venue_data'] = [];
                if (!empty($val['all_venue_data']) && json_decode($val['all_venue_data'], true)) {
                    $pp = 0;
                    foreach (json_decode($val['all_venue_data'], true) as $key => $value) {
                        $currentDateTime = new DateTime();
                        $eventDateTime = DateTime::createFromFormat('d-m-Y h:i A', date('d-m-Y', strtotime($value['date'])) . ' ' . date('h:i A', strtotime($value['start_time'])));
                        if ($eventDateTime && $eventDateTime > $currentDateTime) {
                            $events_translation[$Index]['all_venue_data'][$pp] = $value;
                            if (!empty($value['package_list'])) {
                                foreach ($value['package_list'] as $keys => $val2) {
                                    $events_translation[$Index]['all_venue_data'][$pp]['package_list'][$keys]['package_id'] =  $val2['package_name'];
                                    $getpackages =  (\App\Models\EventPackage::where('id', $val2['package_name'])->first() ?? []);
                                    $hindiname = $getpackages->translations()->pluck('value', 'key')->toArray();
                                    $events_translation[$Index]['all_venue_data'][$pp]['package_list'][$keys]['en_package_name'] =  ($getpackages['package_name'] ?? '');
                                    $events_translation[$Index]['all_venue_data'][$pp]['package_list'][$keys]['hi_package_name'] =  ($hindiname['package_name'] ?? '');
                                }
                            }
                            $pp++;
                        }
                    }
                }

                $events_translation[$Index]['event_image'] =  getValidImage(path: 'storage/app/public/event/events/' . $val['event_image'], type: 'backend-product');
                if (isset($val['images']) && json_decode($val['images'])) {
                    $decodedimageList = json_decode($val['images'], true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        foreach ($decodedimageList as $key2 => $imgs) {
                            $decodedimageList[$key2] = getValidImage(path: 'storage/app/public/event/events/' . $imgs, type: 'backend-product');
                        }
                    }
                    $events_translation[$Index]['images'] = $decodedimageList;
                }
                //     return $events_translation;
                // });
                $Index++;
            }
            return response()->json(['status' => 1, 'message' => 'filter Successfully', 'recode' => count($events_translation), 'data' => $events_translation], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Not Found Event Filter', 'recode' => 0, 'data' => []], 200);
        }
    }

    public function CategoryList(Request $request)
    {
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        $getData = $this->EventcategoryRepo->getListWhere(orderBy: ['id' => 'desc'], searchValue: $request->get('searchValue'), filters: ['status' => 1], relations: ['translations'], dataLimit: 'all');
        if (isset($getData) && count($getData) > 0) {
            $events_translation = [];
            $events_translation = $getData->map(function ($val) use ($languages, $defaultLanguage) {
                $translationKeys = ['category_name'];
                $translations = $val->translations()->pluck('value', 'key')->toArray();
                $translatedData = [];
                foreach ($languages as $language) {
                    foreach ($translationKeys as $translationKey) {
                        $lang = $language === 'in' ? 'hi' : $language;
                        $translatedData["{$lang}_{$translationKey}"] = ($language == $defaultLanguage)
                            ? $val[$translationKey]
                            : ($translations[$translationKey] ?? '');
                    }
                }
                $translatedData['id'] = $val['id'];
                $translatedData['image'] = getValidImage(path: 'storage/app/public/event/category/' . $val['image'], type: 'backend-product');
                return $translatedData;
            });

            return response()->json(['status' => 1, 'message' => 'get Category Successfully', 'recode' => count($events_translation), 'data' => $events_translation], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Not Found Category', 'recode' => 0, 'data' => []], 400);
        }
    }

    public function GetEventById(Request $request)
    {
        $user_id = $request->get('user_id') ?? null;
        $event_id = $request->get('event_id') ?? null;
        $getData = Events::where(function ($query) use ($event_id) {
            $query->where('id', $event_id)
                ->orWhere('slug', $event_id);
        })->where(['status' => 1, 'is_approve' => 1])->with(['organizers', 'categorys', 'eventArtist'])->first(); //$this->EventsRepo->getFirstWhere(params: ['id' => $event_id, 'status' => 1, 'is_approve' => 1], relations: ['organizers', 'categorys', 'eventArtist', 'translations']);
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        $events_translation = [];
        if (!empty($getData)) {
            $translationKeys = ['event_name', 'event_about', 'event_schedule', 'event_attend', 'event_team_condition', 'language'];
            $hindi_translation = $getData->translations()->pluck('value', 'key')->toArray();
            foreach ($translationKeys as $key => $value) {
                $events_translation['en_' . $value] = $getData[$value];
                $events_translation['hi_' . $value] = $hindi_translation[$value];
            }
            $events_translation['id'] =  $getData['id'];
            $events_translation['organizer_by'] =  $getData['organizer_by'];
            $events_translation['age_group'] =  $getData['age_group'];
            $events_translation['days'] =  $getData['days'];
            $events_translation['start_to_end_date'] =  $getData['start_to_end_date'];
            $events_translation['youtube_video'] =  $getData['youtube_video'];
            $events_translation['event_interested'] =  $getData['event_interested'];
            $events_translation['informational_status'] =  $getData['informational_status'];
            $events_translation['aadhar_status'] =  $getData['required_aadhar_status'] ?? 0;
            $events_translation['categorys']['id'] =  $getData['categorys']['id'];
            $getcategorys = EventCategory::where('id', $getData['categorys']['id'])->first();
            $trans_categorys =  $getcategorys->translations()->pluck('value', 'key')->toArray();
            $events_translation['categorys']['en_category_name'] =  $getcategorys['category_name'];
            $events_translation['categorys']['hi_category_name'] =  ($trans_categorys['category_name'] ?? "");

            $getorganizer = EventOrganizer::where('id', $getData['organizers']['id'])->first();
            $trans_organizer =  $getorganizer->translations()->pluck('value', 'key')->toArray();
            $events_translation['organizers']['en_organizer_name'] =  $getorganizer['organizer_name'];
            $events_translation['organizers']['hi_organizer_name'] =  ($trans_organizer['organizer_name'] ?? "");

            $events_translation['organizers']['id'] =  $getData['organizers']['id'];
            $events_translation['organizers']['full_name'] =  $getData['organizers']['full_name'];
            $events_translation['organizers']['email_address'] =  $getData['organizers']['email_address'];
            $events_translation['organizers']['contact_number'] =  $getData['organizers']['contact_number'];

            $events_translation['event_image'] =  getValidImage(path: 'storage/app/public/event/events/' . $getData['event_image'], type: 'backend-product');
            $imagesArray = [];
            if (!empty($getData['images']) && json_decode($getData['images'])) {
                foreach (json_decode($getData['images']) as $key => $img) {
                    $imagesArray[$key] =  getValidImage(path: 'storage/app/public/event/events/' . $img, type: 'backend-product');
                }
            }
            if (!empty($getData['organizers']['image'])) {
                $events_translation['organizers']['image'] = getValidImage(path: 'storage/app/public/event/organizers/' . $getData['organizers']['image'], type: 'backend-product');
            }
            if (!empty($getData['categorys']['image'])) {
                $events_translation['categorys']['image'] = getValidImage(path: 'storage/app/public/event/category/' . $getData['categorys']['image'], type: 'backend-product');
            }
            if (!empty($getData['eventArtist'])) {
                $hindi_artisat  = ($getData['eventArtist'])->translations()->pluck('value', 'key')->toArray();
                $events_translation['artist']['id']  = ($getData['eventArtist']['id'] ?? '');
                $events_translation['artist']['en_artist_name']  = ($getData['eventArtist']['name'] ?? '');
                $events_translation['artist']['hi_artist_name']  = ($hindi_artisat['name'] ?? '');
                $events_translation['artist']['en_description']  = ($getData['eventArtist']['description'] ?? '');
                $events_translation['artist']['hi_description']  = ($hindi_artisat['description'] ?? '');
                $events_translation['artist']['en_profession']  = ($getData['eventArtist']['profession'] ?? '');
                $events_translation['artist']['hi_profession']  = ($hindi_artisat['profession'] ?? '');
                $events_translation['artist']['image']  = getValidImage(path: 'storage/app/public/event/events/' . ($getData['eventArtist']['image'] ?? ''), type: 'backend-product');
            }
            $events_translation['all_venue_data'] = [];
            $Update_date = "";
            $groupedEvents = [];
            $today = date('Y-m-d h:i A');
            if (!empty($getData['all_venue_data']) && json_decode($getData['all_venue_data'])) {
                $allEvents = json_decode($getData['all_venue_data'], true);
                $pp = 0;
                foreach ($allEvents as $event) {
                    if (strtotime($event['date'] . " " . $event['start_time']) < strtotime($today)) {
                        continue;
                    }
                    // $eventDateTime = DateTime::createFromFormat('d-m-Y h:i A', $event['date'] . " " . $event['start_time']);
                    // if($eventDateTime && $eventDateTime->getTimestamp() < time()) {
                    //     continue;
                    // }
                    $venueKey = $event['en_event_venue']; // group by venue
                    if ($pp == 0) {
                        $Update_date = $event['date'] . " " . $event['start_time'];
                        $pp++;
                    }
                    if (!isset($groupedEvents[$venueKey])) {
                        $groupedEvents[$venueKey] = [
                            'id'   => $event['id'],
                            'en_event_venue'   => $event['en_event_venue'],
                            'en_event_country' => $event['en_event_country'],
                            'en_event_state'   => $event['en_event_state'],
                            'en_event_cities'  => $event['en_event_cities'],
                            'hi_event_venue'   => $event['hi_event_venue'],
                            'hi_event_country' => $event['hi_event_country'],
                            'hi_event_state'   => $event['hi_event_state'],
                            'hi_event_cities'  => $event['hi_event_cities'],
                            'events'           => []
                        ];
                    }

                    $packageListing = [];
                    if (!empty($event['package_list'])) {
                        foreach ($event['package_list'] as $keys => $val2) {
                            $packageListing[$keys]['package_id'] = $val2['package_name'];

                            $getpackages = (\App\Models\EventPackage::where('id', $val2['package_name'])->first() ?? []);
                            $hindiname = [];
                            if ($getpackages) {
                                $hindiname = $getpackages->translations()->pluck('value', 'key')->toArray();
                            }
                            $packageListing[$keys]['en_package_name'] = ($getpackages['package_name'] ?? '');
                            $packageListing[$keys]['hi_package_name'] = ($hindiname['package_name'] ?? '');
                            $packageListing[$keys]['en_description'] = ($getpackages['description'] ?? '');
                            $packageListing[$keys]['hi_description'] = ($hindiname['description'] ?? '');
                            $packageListing[$keys]['seats_no'] = (string)($val2['seats_no'] ?? '');
                            $packageListing[$keys]['price'] = (string)($val2['price_no'] ?? '');
                            $packageListing[$keys]['available'] = (string)($val2['available'] ?? '');
                            $packageListing[$keys]['sold'] = (string)($val2['sold'] ?? '');
                        }
                    }

                    $groupedEvents[$venueKey]['events'][] = [
                        'date'           => $event['date'],
                        'start_time'     => $event['start_time'],
                        'end_time'       => $event['end_time'],
                        'event_duration' => $event['event_duration'],
                        'package_list'   => $packageListing,
                    ];
                }

                // Reset keys and sort each venue's events by date/time
                $groupedEvents = array_values($groupedEvents);

                foreach ($groupedEvents as &$venue) {
                    usort($venue['events'], function ($a, $b) {
                        $timeA = strtotime($a['date'] . ' ' . $a['start_time']);
                        $timeB = strtotime($b['date'] . ' ' . $b['start_time']);
                        return $timeA <=> $timeB;
                    });
                }
            }
            $events_translation['all_venue_data'] = $groupedEvents;
            // if (!empty($getData['all_venue_data'] && json_decode($getData['all_venue_data']))) {
            //     $pp = 0;
            //     foreach (json_decode($getData['all_venue_data'], true) as $key => $value) {
            //         $currentDateTime = new DateTime();
            //         $eventDateTime = DateTime::createFromFormat(
            //             'd-m-Y h:i A',
            //             date('d-m-Y', strtotime($value['date'])) . ' ' . date('h:i A', strtotime($value['start_time']))
            //         );

            //         if ($eventDateTime && $eventDateTime > $currentDateTime) {
            //             if ($pp == 0) {
            //                 $Update_date = $value['date'];
            //             }
            //             $events_translation['all_venue_data'][$pp] = $value;

            //             if (!empty($value['package_list'])) {
            //                 foreach ($value['package_list'] as $keys => $val2) {
            //                     $events_translation['all_venue_data'][$pp]['package_list'][$keys]['package_id'] = $val2['package_name'];

            //                     $getpackages = (\App\Models\EventPackage::where('id', $val2['package_name'])->first() ?? []);
            //                     $hindiname = [];
            //                     if ($getpackages) {
            //                         $hindiname = $getpackages->translations()->pluck('value', 'key')->toArray();
            //                     }

            //                     $events_translation['all_venue_data'][$pp]['package_list'][$keys]['en_package_name'] = ($getpackages['package_name'] ?? '');
            //                     $events_translation['all_venue_data'][$pp]['package_list'][$keys]['hi_package_name'] = ($hindiname['package_name'] ?? '');
            //                     $events_translation['all_venue_data'][$pp]['package_list'][$keys]['en_description'] = ($getpackages['description'] ?? '');
            //                     $events_translation['all_venue_data'][$pp]['package_list'][$keys]['hi_description'] = ($hindiname['description'] ?? '');
            //                 }
            //             }
            //             $pp++;
            //         }
            //     }

            //     usort($events_translation['all_venue_data'], function ($a, $b) {
            //         $timeA = strtotime($a['date'] . ' ' . $a['start_time']);
            //         $timeB = strtotime($b['date'] . ' ' . $b['start_time']);
            //         return $timeA <=> $timeB;
            //     });
            // }
            $events_translation['event_next_date'] =  $Update_date;
            $events_translation['images'] = $imagesArray;

            $events_translation['remaining_event'] = [];

            // $upcomingEvents = \App\Models\Events::select('id', 'event_name as en_event_name', 'informational_status', 'start_to_end_date', 'event_image', 'all_venue_data')->where('is_approve', 1)
            //     ->where('status', 1)
            //     ->where('id', "!=", $event_id)
            //     ->with(['categorys'])
            //     ->withCount('EventOrderReview')
            //     ->withAvg('review', 'star')
            //     ->orderBy('id', 'desc')
            //     ->get()->makeHidden(['translations'])->map(function ($event) {
            //         $event->event_image = $event->event_image ? (getValidImage(path: 'storage/app/public/event/events/' . ($event->event_image ?? ""), type: 'backend-product')) : "";
            //         $transEvent = [];
            //         if ($event) {
            //             $transEvent = $event->translations()->pluck('value', 'key')->toArray();
            //         }
            //         $event->hi_event_name = ($transEvent['event_name'] ?? "");
            //         $venueData = collect(json_decode($event->all_venue_data ?? "[]", true))
            //             ->filter(function ($venue) {
            //                 if (empty($venue['date'])) {
            //                     return false;
            //                 }
            //                 // $eventDate = \Carbon\Carbon::parse($venue['date']);
            //                 // $today = \Carbon\Carbon::today();
            //                 // return $eventDate->greaterThanOrEqualTo($today);
            //                 $eventDate = \Carbon\Carbon::parse($venue['date'])->startOfDay();
            //                 $today = \Carbon\Carbon::now()->startOfDay();
            //                 return $eventDate->gte($today);
            //             })
            //             ->sortBy('date')
            //             ->first();
            //         $event->venue_data = $venueData ? [(array) $venueData] : [];
            //         unset($event->all_venue_data);
            //         return $event;
            //     });
            $upcomingEvents = \App\Models\Events::select('id', 'event_name as en_event_name', 'informational_status', 'start_to_end_date', 'event_image', 'all_venue_data')
                ->where('is_approve', 1)
                ->where('status', 1)
                ->where('id', "!=", $event_id)
                ->with(['categorys'])
                ->withCount('EventOrderReview')
                ->withAvg('review', 'star')
                ->orderBy('id', 'desc')
                ->get()
                ->makeHidden(['translations'])
                ->map(function ($event) {
                    $event->event_image = $event->event_image ? (getValidImage(path: 'storage/app/public/event/events/' . ($event->event_image ?? ""), type: 'backend-product')) : "";
                    $event->review_avg_star = (int)$event->review_avg_star ?? 0;
                    $transEvent = [];
                    if ($event) {
                        $transEvent = $event->translations()->pluck('value', 'key')->toArray();
                    }
                    $event->hi_event_name = ($transEvent['event_name'] ?? "");
                    $venueData = collect(json_decode($event->all_venue_data ?? "[]", true))
                        ->filter(function ($venue) {
                            if (empty($venue['date'])) {
                                return false;
                            }
                            $eventDate = \Carbon\Carbon::parse($venue['date'])->startOfDay();
                            $today = \Carbon\Carbon::now()->startOfDay();
                            return $eventDate->gte($today);
                        })
                        ->sortBy('date')
                        ->first();
                    $event->venue_data = $venueData ? [(array) $venueData] : [];
                    unset($event->all_venue_data);
                    return $event;
                })
                ->filter(function ($event) {
                    if (empty($event->start_to_end_date)) {
                        return false;
                    }
                    if (strpos($event->start_to_end_date, ' - ') !== false) {
                        $dates = explode(' - ', $event->start_to_end_date);
                        $endDate = \Carbon\Carbon::parse(trim($dates[1]))->endOfDay();
                    } else {
                        $endDate = \Carbon\Carbon::parse($event->start_to_end_date)->endOfDay();
                    }
                    $today = \Carbon\Carbon::now();
                    return $endDate->gte($today);
                })
                ->values();
            $events_translation['remaining_event'] = $upcomingEvents->take(10);


            $events_translation['event_interest'] = EventInterest::where('event_id', $event_id)->where('user_id', $user_id)->count();
            return response()->json(['status' => 1, 'message' => 'get Event Successfully', 'recode' => '1', 'data' => $events_translation], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Not Found Event', 'recode' => 0, 'data' => []], 400);
        }
    }

    public function EventLeads(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => ['required', function ($attribute, $value, $fail) {
                if (!User::where('id', $value)->where('is_active', 1)->exists()) {
                    $fail('The selected user is invalid or inactive.');
                }
            },],
            'event_id' => 'required',
            'venue_id' => 'required',
            'package_id' => 'required',
            'no_of_seats' => 'required',
            'amount' => 'required',
        ], [
            'user_id.required' => 'Login User Id',
            'event_id.required' => 'Booking Event Id!',
            'venue_id.required' => 'Venue is Empty!',
            'package_id.required' => 'package is Empty!',
            'no_of_seats.required' => 'seat Number is Empty!',
            'amount.required' => 'Total Amount is provide!',
        ]);

        if ($validator->fails()) {
            return response()->json(["status" => 0, "message" => "", 'errors' => Helpers::error_processor($validator), 'data' => []], 403);
        }
        DB::beginTransaction();
        try {
            $user_infor = User::where('id', $request['user_id'])->where('is_active', 1)->first();
            $leads = new EventLeads();
            $leads->user_id = $request['user_id'];
            $leads->user_phone = $user_infor['phone'];
            $leads->user_name = $user_infor['name'];
            $leads->event_id = $request->get('event_id');
            $leads->package_id = $request->get('package_id');
            $leads->venue_id = $request->get('venue_id');
            $leads->qty = $request->get('no_of_seats');
            $leads->coupon_amount = ($request->get('coupon_amount') ?? 0);
            $leads->coupon_id = ($request->get('coupon_id') ?? '');
            $leads->amount = (($request->get('coupon_amount') ?? 0) + ($request->get('amount') / ($request->get('no_of_seats') ?? 0)));
            $leads->total_amount = ($request->get('amount'));
            $leads->save();


            if ($leads->id) {
                DB::commit();
                return response()->json(['status' => 1, 'message' => 'save event Leads.', 'id' => $leads->id], 200);
            } else {
                DB::rollBack();
                return response()->json(['status' => 0, 'message' => 'not insert', 'id' => ''], 400);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 0, 'message' => 'An error occurred: ' . $e->getMessage(), 'recode' => '', 'data' => []], 200);
        }
    }

    public function EventLeadsUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lead_id' => [
                'required',
                \Illuminate\Validation\Rule::exists('event_leads', 'id')->where(function ($query) {
                    $query->where('status', 0);
                }),
            ],
            'no_of_seats' => 'required',
            'amount' => 'required',
            "member" => "required",
        ], [
            'lead_id.required' => 'lead Id is Empty!',
            'no_of_seats.required' => 'seat Number is Empty!',
            'amount.required' => 'Total Amount is provide!',
        ]);

        if ($validator->fails()) {
            return response()->json(["status" => 0, "message" => "", 'errors' => Helpers::error_processor($validator), 'data' => []], 403);
        }
        DB::beginTransaction();
        try {
            $leads = EventLeads::find($request['lead_id']);
            $leads->qty = $request->get('no_of_seats');
            $leads->coupon_amount = ($request->get('coupon_amount') ?? 0);
            $leads->coupon_id = ($request->get('coupon_id') ?? '');
            $leads->amount = (($request->get('coupon_amount') ?? 0) + ($request->get('amount') / ($request->get('no_of_seats') ?? 0)));
            $leads->total_amount = ($request->get('amount'));
            $JsonEncodeMembers = [];
            if ($request['no_of_seats'] > 0) {
                for ($qn = 0; $qn < $request['no_of_seats']; $qn++) {
                    $JsonEncodeMembers[$qn]['id'] = ($qn + 1);
                    $JsonEncodeMembers[$qn]['name'] = $request['member'][$qn]['name'] ?? '';
                    $JsonEncodeMembers[$qn]['phone'] = $request['member'][$qn]['phone'] ?? '';
                    $JsonEncodeMembers[$qn]['aadhar'] = $request['member'][$qn]['aadhar'] ?? '';
                    $JsonEncodeMembers[$qn]['verify'] =  $request['member'][$qn]['verify'] ?? 0;
                    $JsonEncodeMembers[$qn]['aadhar_verify'] =  $request['member'][$qn]['aadhar_verify'] ?? 0;
                    $JsonEncodeMembers[$qn]['time'] = '';
                }
            }
            $leads->user_information = json_encode($JsonEncodeMembers);
            $leads->save();
            if ($leads->id) {
                DB::commit();
                return response()->json(['status' => 1, 'message' => 'save event Leads.', 'id' => $leads->id], 200);
            } else {
                DB::rollBack();
                return response()->json(['status' => 0, 'message' => 'not insert', 'id' => ''], 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 0, 'message' => 'An error occurred: ' . $e->getMessage(), 'recode' => '', 'data' => []], 200);
        }
    }

    // public function EventOrder(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'user_id' => ['required', function ($attribute, $value, $fail) {
    //             if (!User::where('id', $value)->where('is_active', 1)->exists()) {
    //                 $fail('The selected user is invalid or inactive.');
    //             }
    //         },],
    //         'event_id' => 'required',
    //         'venue_id' => 'required',
    //         'package_id' => 'required',
    //         'no_of_seats' => 'required',
    //         'amount' => 'required',
    //     ], [
    //         'user_id.required' => 'Login User Id',
    //         'event_id.required' => 'Booking Event Id!',
    //         'venue_id.required' => 'Venue is Empty!',
    //         'package_id.required' => 'package is Empty!',
    //         'no_of_seats.required' => 'seat Number is Empty!',
    //         'amount.required' => 'Total Amount is provide!',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(["status" => 0, "message" => "", 'errors' => Helpers::error_processor($validator), 'data' => []], 403);
    //     }

    //     $user_id = $request['user_id'];
    //     $user_infor = User::where('id', $request['user_id'])->where('is_active', 1)->first();

    //     $leads = new EventLeads();
    //     $leads->user_phone = $user_infor['phone'];
    //     $leads->user_name = $user_infor['name'];
    //     $leads->event_id = $request->get('event_id');
    //     $leads->package_id = $request->get('package_id');
    //     $leads->venue_id = $request->get('venue_id');
    //     $leads->qty = $request->get('no_of_seats');
    //     $leads->coupon_amount = ($request->get('coupon_amount')??0);
    //     $leads->coupon_id = ($request->get('coupon_id')??'');
    //     $leads->amount = (($request->get('coupon_amount')??0)+($request->get('amount') / ($request->get('no_of_seats') ?? 0)));
    //     $leads->total_amount = ($request->get('amount'));
    //     $leads->save();
    //     $leadsID = $leads->id;
    //     $insertedId = '';

    //     $eventData = Events::where('is_approve', 1)->where('status', 1)->find($request['event_id']);
    //     if (!empty($eventData) && !empty($request->get('package_id')) && !empty($request->get('no_of_seats'))) {

    //         DB::beginTransaction();
    //         try {
    //             $orderData = new EventOrder();
    //             $orderData->user_id = $request['user_id'];
    //             $orderData->event_id = $request['event_id'];
    //             $orderData->venue_id = $request['venue_id'];
    //             $orderData->amount = $request['amount'];
    //             $orderData->coupon_amount = ($request['coupon_amount']??0);
    //             $orderData->coupon_id = ($request['coupon_id']??0);
    //             $orderData->transaction_status = 0;
    //             $orderData->status = 1;
    //             $orderData->save();
    //             $insertedId = $orderData->id;

    //             $orderItems = new EventOrderItems();
    //             $orderItems->order_id = $insertedId;
    //             $orderItems->package_id = $request->get('package_id');
    //             $orderItems->no_of_seats = $request->get('no_of_seats');
    //             $orderItems->amount = $request['amount'];
    //             $orderItems->save();
    //             $foundPackage = false;

    //             if (!empty($eventData['all_venue_data']) && json_decode($eventData['all_venue_data'], true)) {
    //                 foreach (json_decode($eventData['all_venue_data'], true) as $key => $value) {
    //                     if (($value['id'] ?? "") == $request->get('venue_id') && !empty($value['package_list'])) {
    //                         $package = collect($value['package_list'])->firstWhere('package_name', $request->get('package_id'));
    //                         $foundPackage = true;
    //                         $amounts = 0;
    //                         if (!empty($package) && ($package['available']??0) >= $request->get('no_of_seats')) {
    //                                         $amounts = ((($package['price_no'] * $request->get('no_of_seats')) <= $request->get('amount')) ? ($package['price_no'] * $request->get('no_of_seats')) : 0); 
    //                             }else{
    //                                 DB::rollBack();
    //                                 return response()->json(['status' => 0, 'message' => $request->get('no_of_seats') . ' seats are not available. ' . $package['available']  . ' seats are available.', 'recode' => '', 'data' => []], 400);

    //                             }
    //                             if ($amounts == 0) {
    //                                 DB::rollBack();
    //                                 return response()->json(['status' => 0, 'message' => 'Please valid Amount', 'recode' => '', 'data' => []], 200);
    //                             }
    //                     }
    //                 }
    //             }
    //             if (!$foundPackage) {
    //                 $PackagesSeats = json_decode($eventData['all_venue_data'], true);
    //                 if (json_last_error() !== JSON_ERROR_NONE) {
    //                     DB::rollBack();
    //                     return response()->json(['status' => 0, 'message' => 'Booking seats data is not properly formatted.', 'recode' => '', 'data' => []], 400);
    //                 }
    //                 $foundPackage = false;
    //                 if (!$foundPackage) {
    //                     DB::rollBack();
    //                     return response()->json(['status' => 0, 'message' => ' Package ID not found in booking seats.', 'recode' => '', 'data' => []], 400);
    //                 }
    //             }
    //             DB::commit();
    //             $getData = EventOrder::with(['orderitem', 'eventid'])->where('id', $insertedId)->get();
    //             //create pay link

    //             $currency_model = Helpers::get_business_settings('currency_model');
    //             if ($currency_model == 'multi_currency') {
    //                 $currency_code = 'USD';
    //             } else {
    //                 $default = BusinessSetting::where(['type' => 'system_default_currency'])->first()->value;
    //                 $currency_code = Currency::find($default)->code;
    //             }
    //             $additional_data['order_id'] = $insertedId;
    //             $additional_data['event_id'] = $request['event_id'];
    //             $additional_data['leads_id'] = $leadsID;
    //             $additional_data['package_id'] = $request->get('package_id');
    //             $additional_data['customer_id'] = $request->get('user_id');
    //             $additional_data['payment_mode'] = 'app';
    //             $additional_data['amount'] = $request['amount'];
    //             $additional_data['user_name'] = $user_infor['name'];
    //             $additional_data['user_email'] = $user_infor['email'];
    //             $additional_data['user_phone'] = $user_infor['phone'];
    //             $additional_data['business_name'] = 'Event Order Amount';
    //             $additional_data['business_logo'] = asset('storage/app/public/company') . '/' . Helpers::get_business_settings('company_web_logo');
    //             $payer = new Payer(
    //                 $user_infor['name'],
    //                 $user_infor['email'],
    //                 $user_infor['phone'],
    //                 ''
    //             );
    //             $payment_info = new PaymentInfo(
    //                 success_hook: 'add_fund_to_wallet_success',
    //                 failure_hook: 'add_fund_to_wallet_fail',
    //                 currency_code: $currency_code,
    //                 payment_method: 'razor_pay',
    //                 payment_platform: "app",
    //                 payer_id: $insertedId,
    //                 receiver_id: '100',
    //                 additional_data: $additional_data,
    //                 payment_amount: $request['amount'],
    //                 external_redirect_link: route('payment.event-order-transaction-success'),
    //                 attribute: 'event_order',
    //                 attribute_id: idate("U"),
    //             );
    //             $receiver_info = new Receiver('receiver_name', 'example.png');
    //             $redirect_link = Payment::generate_link($payer, $payment_info, $receiver_info);

    //             return response()->json(['status' => 1, 'message' => 'Order placed successfully', 'recode' => 1, 'data' => ['pay_link' => $redirect_link]], 200);
    //         } catch (\Exception $e) {
    //             DB::rollBack();
    //             return response()->json(['status' => 0, 'message' => 'An error occurred: ' . $e->getMessage(), 'recode' => '', 'data' => []], 200);
    //         }
    //     } else {
    //         DB::rollBack();
    //         return response()->json(['status' => 0, 'message' => 'Invalid Event or Seats Data', 'recode' => 0, 'data' => []], 400);
    //     }
    //     return response()->json(['status' => 0, 'message' => 'Please Currct Data Pass', 'recode' => 0, 'data' => []], 400);
    // }
    public function EventOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => ['required', function ($attribute, $value, $fail) {
                if (!User::where('id', $value)->where('is_active', 1)->exists()) {
                    $fail('The selected user is invalid or inactive.');
                }
            },],
            'event_id' => 'required',
            'venue_id' => 'required',
            'package_id' => 'required',
            'no_of_seats' => 'required',
            'amount' => 'required',
            'lead_id' => 'required',
            'wallet_type' => 'required|in:0,1',
            "transaction_id" => 'required',
            'online_amount' => 'required',
        ], [
            'user_id.required' => 'Login User Id',
            'event_id.required' => 'Booking Event Id!',
            'venue_id.required' => 'Venue is Empty!',
            'package_id.required' => 'package is Empty!',
            'no_of_seats.required' => 'seat Number is Empty!',
            'amount.required' => 'Total Amount is provide!',
            'lead_id.required' => 'Lead Id is provide!',
            'wallet_type.required' => 'wallet type is provide 0,1 !',
        ]);

        if ($validator->fails()) {
            return response()->json(["status" => 0, "message" => "", 'errors' => Helpers::error_processor($validator), 'data' => []], 403);
        }

        if ($request->wallet_type == 1 && $request['online_amount'] > 0) {
            User::where('id', $request['user_id'])->update(['wallet_balance' => DB::raw('wallet_balance + ' . $request['online_amount'])]);
            $wallet_transaction = new WalletTransaction();
            $wallet_transaction->user_id = $request['user_id'];
            $wallet_transaction->transaction_id = \Illuminate\Support\Str::uuid();
            $wallet_transaction->reference = 'add_funds_to_wallet';
            $wallet_transaction->transaction_type = 'add_fund';
            $wallet_transaction->balance = User::where('id', $request['user_id'])->first()['wallet_balance'];
            $wallet_transaction->credit = $request->online_amount;
            $wallet_transaction->save();
        }

        $user_id = $request['user_id'];
        $user_infor = User::where('id', $request['user_id'])->where('is_active', 1)->first();
        $leadsID = $request->lead_id;
        $eventData = Events::where('is_approve', 1)->where('status', 1)->find($request['event_id']);
        if (!empty($eventData) && !empty($request->get('package_id')) && !empty($request->get('no_of_seats'))) {
            DB::beginTransaction();
            try {
                $foundPackage = false;
                if (!empty($eventData['all_venue_data']) && json_decode($eventData['all_venue_data'], true)) {
                    foreach (json_decode($eventData['all_venue_data'], true) as $key => $value) {
                        if (($value['id'] ?? "") == $request->get('venue_id') && !empty($value['package_list'])) {
                            $booking_date_w_message = $value['date'];
                            $booking_time_w_message = $value['start_time'];
                            $venue_name_w_message = $value['en_event_cities'];
                            $package = collect($value['package_list'])->firstWhere('package_name', $request->get('package_id'));
                            $foundPackage = true;
                            $amounts = -1;
                            if (!empty($package) && ($package['available'] ?? 0) >= $request->get('no_of_seats')) {
                                $amounts = ((($package['price_no'] * $request->get('no_of_seats')) <= ($request->get('amount') ?? 0) + ($request['coupon_amount'] ?? 0)) ? ($package['price_no'] * $request->get('no_of_seats')) : 0);
                            } else {
                                DB::rollBack();
                                if ($request->wallet_type == 0) {
                                    User::where('id', $request['user_id'])->update(['wallet_balance' => DB::raw('wallet_balance + ' . $request['amount'])]);
                                    $wallet_transaction = new WalletTransaction();
                                    $wallet_transaction->user_id = $request['user_id'];
                                    $wallet_transaction->transaction_id = \Illuminate\Support\Str::uuid();
                                    $wallet_transaction->reference = 'Event order refund';
                                    $wallet_transaction->transaction_type = 'event_order_refund';
                                    $wallet_transaction->balance = User::where('id', $request['user_id'])->first()['wallet_balance'];
                                    $wallet_transaction->credit = $request->amount;
                                    $wallet_transaction->save();
                                }
                                return response()->json(['status' => 0, 'message' => $request->get('no_of_seats') . ' seats are not available. ' . $package['available']  . ' seats are available.', 'recode' => '', 'data' => []], 400);
                            }
                            if ($amounts < 0) {
                                DB::rollBack();
                                if ($request->wallet_type == 0) {
                                    User::where('id', $request['user_id'])->update(['wallet_balance' => DB::raw('wallet_balance + ' . $request['amount'])]);
                                    $wallet_transaction = new WalletTransaction();
                                    $wallet_transaction->user_id = $request['user_id'];
                                    $wallet_transaction->transaction_id = \Illuminate\Support\Str::uuid();
                                    $wallet_transaction->reference = 'Event order refund';
                                    $wallet_transaction->transaction_type = 'event_order_refund';
                                    $wallet_transaction->balance = User::where('id', $request['user_id'])->first()['wallet_balance'];
                                    $wallet_transaction->credit = $request->amount;
                                    $wallet_transaction->save();
                                }
                                return response()->json(['status' => 0, 'message' => 'Please valid Amount', 'recode' => '', 'data' => []], 200);
                            }
                        }
                    }
                }
                if (!$foundPackage) {
                    $PackagesSeats = json_decode($eventData['all_venue_data'], true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        DB::rollBack();
                        if ($request->wallet_type == 0) {
                            User::where('id', $request['user_id'])->update(['wallet_balance' => DB::raw('wallet_balance + ' . $request['amount'])]);
                            $wallet_transaction = new WalletTransaction();
                            $wallet_transaction->user_id = $request['user_id'];
                            $wallet_transaction->transaction_id = \Illuminate\Support\Str::uuid();
                            $wallet_transaction->reference = 'Event order refund';
                            $wallet_transaction->transaction_type = 'event_order_refund';
                            $wallet_transaction->balance = User::where('id', $request['user_id'])->first()['wallet_balance'];
                            $wallet_transaction->credit = $request->amount;
                            $wallet_transaction->save();
                        }
                        return response()->json(['status' => 0, 'message' => 'Booking seats data is not properly formatted.', 'recode' => '', 'data' => []], 400);
                    }
                    $foundPackage = false;
                    if (!$foundPackage) {
                        DB::rollBack();
                        if ($request->wallet_type == 0) {
                            User::where('id', $request['user_id'])->update(['wallet_balance' => DB::raw('wallet_balance + ' . $request['amount'])]);
                            $wallet_transaction = new WalletTransaction();
                            $wallet_transaction->user_id = $request['user_id'];
                            $wallet_transaction->transaction_id = \Illuminate\Support\Str::uuid();
                            $wallet_transaction->reference = 'Event order refund';
                            $wallet_transaction->transaction_type = 'event_order_refund';
                            $wallet_transaction->balance = User::where('id', $request['user_id'])->first()['wallet_balance'];
                            $wallet_transaction->credit = $request->amount;
                            $wallet_transaction->save();
                        }
                        return response()->json(['status' => 0, 'message' => ' Package ID not found in booking seats.', 'recode' => '', 'data' => []], 400);
                    }
                }
                $orderData = new EventOrder();
                $orderData->user_id = $request['user_id'];
                $orderData->event_id = $request['event_id'];
                $orderData->venue_id = $request['venue_id'];
                $orderData->amount = $request['amount'];
                $orderData->coupon_amount = ($request['coupon_amount'] ?? 0);
                $orderData->coupon_id = ($request['coupon_id'] ?? 0);
                $orderData->transaction_status = (($request->transaction_id) ? 1 : 2);
                $orderData->transaction_id = $request->transaction_id;
                $orderData->status = 1;
                $orderData->platform = "app";
                $orderData->save();
                $insertedId = $orderData->id;

                $orderItems = new EventOrderItems();
                $orderItems->order_id = $insertedId;
                $orderItems->package_id = $request->get('package_id');
                $orderItems->no_of_seats = $request->get('no_of_seats');
                $orderItems->amount = $request['amount'];
                $JsonEncodeMembers = [];
                $membersList = EventLeads::where('id', $request->lead_id)->first();
                if ($membersList['qty'] > 0) {
                    $memberJsonDecode = json_decode($membersList['user_information'] ?? "[]", true);
                    if ($memberJsonDecode && count($memberJsonDecode) > 0)
                        $qn = 0;
                    foreach ($memberJsonDecode as $values) {
                        $JsonEncodeMembers[$qn]['id'] = ($qn + 1);
                        $JsonEncodeMembers[$qn]['name'] = $values['name'] ?? '';
                        $JsonEncodeMembers[$qn]['phone'] = $values['phone'] ?? '';
                        $JsonEncodeMembers[$qn]['aadhar'] = $values['aadhar'] ?? '';
                        $JsonEncodeMembers[$qn]['verify'] =  $values['verify'] ?? 0;
                        $JsonEncodeMembers[$qn]['aadhar_verify'] =  $values['aadhar_verify'] ?? 0;
                        $JsonEncodeMembers[$qn]['time'] = '';
                        $qn++;
                    }
                }
                $orderItems->user_information = json_encode($JsonEncodeMembers);
                $orderItems->save();
                if ($request->wallet_type == 1) {
                    User::where('id', $request['user_id'])->update(['wallet_balance' => DB::raw('wallet_balance - ' . $request['amount'])]);
                    $wallet_transaction = new WalletTransaction();
                    $wallet_transaction->user_id = $request['user_id'];
                    $wallet_transaction->transaction_id = \Illuminate\Support\Str::uuid();
                    $wallet_transaction->reference = 'Event order';
                    $wallet_transaction->transaction_type = 'event_order';
                    $wallet_transaction->balance = User::where('id', $request['user_id'])->first()['wallet_balance'];
                    $wallet_transaction->debit = $request->amount;
                    $wallet_transaction->save();
                }
                /////////////////////////////////////////////////////////////////////

                $bookingSeats = json_decode($eventData['all_venue_data'], true);
                $foundPackage = [];
                if ($bookingSeats) {
                    $pn = 0;
                    foreach ($bookingSeats as $keys => $bo_se) {
                        $foundPackage[$keys] = $bo_se;
                        if (($bo_se['id'] ?? "") == $request['venue_id'] && !empty($bo_se['package_list'])) {
                            foreach ($bo_se['package_list'] as $kp => $ch_seat) {
                                if ($ch_seat['package_name'] == $request->get('package_id')) {
                                    if ($ch_seat['available'] < $request->get('no_of_seats')) {
                                        return response()->json(['status' => 0, 'message' => $request->get('no_of_seats') . ' seats are not available. ' . $ch_seat['available']  . ' seats are available.', 'recode' => '', 'data' => []], 200);
                                    } else {
                                        $booking_date_w_message = $bo_se['date'];
                                        $booking_time_w_message = $bo_se['start_time'];
                                        $venue_name_w_message = $bo_se['en_event_cities'];

                                        $foundPackage[$keys]['package_list'][$kp]['available'] = ($ch_seat['available'] - $request->get('no_of_seats'));
                                        $foundPackage[$keys]['package_list'][$kp]['sold'] = ($ch_seat['sold'] + $request->get('no_of_seats'));
                                        $eventtax = \App\Models\ServiceTax::find(1);
                                        $amdin_commission = 0;
                                        $final_amount = 0;
                                        $govtTax = 0;
                                        $orderamount = (($request['amount'] ?? 0) + ($request['coupon_amount'] ?? 0));

                                        if (!empty($eventData) && $eventData['commission_seats']) {
                                            $govtTax = (($orderamount * ($eventtax['event_tax'] ?? 0)) / 100);
                                            $orderamount = $orderamount - $govtTax;
                                            $amdin_commission =  (($orderamount * $eventData['commission_seats']) / 100);
                                            $final_amount = $orderamount - $amdin_commission;
                                        }
                                        $array['admin_commission'] = $amdin_commission;
                                        $array['gst_amount'] = $govtTax;
                                        $array['final_amount'] = $final_amount;
                                        EventOrder::where('id', $insertedId)->update($array);
                                        EventOrderItems::where('id', $orderItems->id)->update(['sub_amount' => ($getLead['amount'] ?? 0), 'gst' => ($eventtax['event_tax'] ?? 0), 'gst_amount' => $govtTax]);
                                        $listOrganizer =  EventOrganizer::where('id', $eventData['event_organizer_id'])->first();
                                        EventOrganizer::where('id', $eventData['event_organizer_id'])->update(
                                            [
                                                'org_total_tax' => ($listOrganizer['org_total_tax'] + $govtTax),
                                                "org_withdrawable_ready" => ($listOrganizer["org_withdrawable_ready"] + $final_amount),
                                                "org_total_commission" => ($listOrganizer["org_total_commission"] + $amdin_commission),
                                            ]
                                        );
                                    }
                                }
                            }
                            \App\Models\EventLeads::where('id', $request->lead)->update(['status' => 1]);
                        }
                    }
                    Events::where('id',  $eventData['id'])->update(['all_venue_data' => $foundPackage]);
                }


                //////////////////////////////////////////////////////////////////////

                EventLeads::where('id', $request->lead_id)->update(['status' => 1]);
                $eventOrder = \App\Models\EventOrder::where('id', $orderData->id)->with(['orderitem', 'eventid'])->first();
                $message_data['title_name'] = $eventOrder['eventid']['event_name'];
                $message_data['place_name'] = $venue_name_w_message;
                $message_data['booking_date'] = date('Y-m-d', strtotime($booking_date_w_message));
                $message_data['time'] = ($booking_time_w_message);
                $message_data['orderId'] = $eventOrder['order_no'];
                $message_data['final_amount'] = webCurrencyConverter(amount: (float)$eventOrder['amount'] ?? 0);
                $message_data['customer_id'] =  $eventOrder['user_id'];
                $message_data['number'] =  $eventOrder['orderitem'][0]['no_of_seats'] ?? 0;
                $message_data['link'] =  route('event-create-pdf-invoice', [$eventOrder['id']]);
                Helpers::whatsappMessage('event', 'Event booking Confirmed', $message_data);
                $memberList = json_decode($eventOrder['orderitem'][0]['user_information'] ?? '[]', true) ?? [];
                if ($memberList  && count($memberList) > 0) {
                    foreach ($memberList as $key => $vals) {
                        $messageData = [
                            'customer_id' => $eventOrder['user_id'],
                            'member_names' => $vals['name'],
                            'link' => route('event-order-details2', [$eventOrder['id'], ($vals['id'])]),
                        ];
                        \App\Jobs\SendWhatsappMessage::dispatch('event', 'event pass attachment', $messageData);
                    }
                }
                DB::commit();
                return response()->json(['status' => 1, 'message' => 'Order placed successfully', 'recode' => 1, 'data' => []], 200);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['status' => 0, 'message' => 'An error occurred: ' . $e->getMessage(), 'recode' => '', 'data' => []], 200);
            }
        } else {
            DB::rollBack();
            if ($request->wallet_type == 0) {
                User::where('id', $request['user_id'])->update(['wallet_balance' => DB::raw('wallet_balance + ' . $request['amount'])]);
                $wallet_transaction = new WalletTransaction();
                $wallet_transaction->user_id = $request['user_id'];
                $wallet_transaction->transaction_id = \Illuminate\Support\Str::uuid();
                $wallet_transaction->reference = 'Event order refund';
                $wallet_transaction->transaction_type = 'event_order_refund';
                $wallet_transaction->balance = User::where('id', $request['user_id'])->first()['wallet_balance'];
                $wallet_transaction->credit = $request->amount;
                $wallet_transaction->save();
            }
            return response()->json(['status' => 0, 'message' => 'Invalid Event or Seats Data', 'recode' => 0, 'data' => []], 400);
        }
        return response()->json(['status' => 0, 'message' => 'Please Currct Data Pass', 'recode' => 0, 'data' => []], 400);
    }



    public function Eventaddcomment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => ['required', function ($attribute, $value, $fail) {
                if (!User::where('id', $value)->where('is_active', 1)->exists()) {
                    $fail('The selected user is invalid or inactive.');
                }
            },],
            'event_id' => ['required', function ($attribute, $value, $fail) {
                if (!Events::where('id', $value)->where('status', 1)->exists()) {
                    $fail('Event does not exist.');
                }
            },],
            'order_id' => 'required',
            'star' => 'required|numeric|between:1,5',
            'comment' => 'required',
        ], [
            'user_id.required' => 'User Id is Empty!',
            'event_id.required' => 'Event Id is Empty!',
            'order_id.required' => 'Order Id is Empty!',
            'star.required' => 'star is Empty!',
            'comment.required' => 'Comment is Empty!',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'recode' => 0, 'data' => [], 'errors' => Helpers::error_processor($validator)], 403);
        }
        $images = '';
        if ($request->file('image')) {
            $images = ImageManager::upload('event/comment/', 'webp', $request->file('image'));
        }

        $check = EventsReview::where('event_id', $request->event_id)->where('order_id', $request->order_id)->where('user_id', $request->user_id)->first();
        if (!$check || $check['is_edited'] == 0) {
            if (!empty($check)) {
                $contact = EventsReview::find($check['id']);
            } else {
                $contact = new EventsReview();
            }
            $contact->user_id = $request->user_id;
            $contact->event_id = $request->event_id;
            $contact->order_id = $request->order_id;
            $contact->comment = $request->comment;
            $contact->star = $request->star;
            $contact->image = $images;
            $contact->is_edited = 1;
            $contact->save();
            return response()->json(['status' => 1, 'message' => 'User Add Comment Successfully', 'recode' => 0, 'data' => [], 'errors' => []], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'User has already added a comment', 'recode' => 0, 'data' => [], 'errors' => []], 200);
        }
    }


    public function geteventcomment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'event_id' => ['required', function ($attribute, $value, $fail) {
                if (!Events::where('id', $value)->where('status', 1)->exists()) {
                    $fail('Events does not exist.');
                }
            },],
        ], [
            'event_id.required' => 'Event Id is Empty!',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'recode' => 0, 'data' => [], 'errors' => Helpers::error_processor($validator)], 403);
        }
        if (!empty($request->user_id) && !empty($request->order_id) && !empty($request->event_id)) {
            $check = EventsReview::where('event_id', $request->event_id)->where('order_id', $request->order_id)->where('user_id', $request->user_id)->first();
            return response()->json(['status' => 1, 'message' => 'get Event Comments', 'data' => $check], 200);
        } else {
            $getData = EventsReview::where(['status' => 1, 'event_id' => $request->event_id])->with(['userData'])->orderBy('id', 'desc')->get();
            $getData_stars = EventsReview::where(['status' => 1, 'event_id' => $request->event_id])->groupBy('event_id')->avg('star');

            if (!empty($getData) && count($getData) > 0) {
                foreach ($getData as $key => $value) {
                    $getList[$key]['star'] = $value['star'];
                    $getList[$key]['created_at'] = date('d M,Y h:i A', strtotime($value['created_at']));
                    $getList[$key]['comment'] = $value['comment'];
                    $getList[$key]['user_name'] = $value['userData']['name'];
                    $getList[$key]['is_edited'] = $value['is_edited'];
                    $getList[$key]['user_image'] = getValidImage(path: 'storage/app/public/profile/' . $value['userData']['image'], type: 'backend-product');
                    if (!empty($value['image'])) {
                        $getList[$key]['image'] =  getValidImage(path: 'storage/app/public/event/comment/' . $value['image'], type: 'backend-product');
                    }
                }
                return response()->json(['status' => 1, 'message' => 'get Event Comments', 'event_star' => $getData_stars, 'recode' => count($getData), 'data' => $getList], 200);
            }
        }
        return response()->json(['status' => 0, 'message' => 'No Comment', 'recode' => 0, 'data' => []], 400);
    }

    public function paymentRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'organizer_id' => ['required', function ($attribute, $value, $fail) {
                if (!EventOrganizer::where('id', $value)->where('is_approve', 1)->where('status', 1)->exists()) {
                    $fail('Event Organizer does not exist.');
                }
            },],
            'amount' => 'required|numeric|min:1',
        ], [
            'organizer_id.required' => 'Event Organizer Id is Empty!',
            'amount.required' => 'Amount is Empty!',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'recode' => 0, 'data' => [], 'errors' => Helpers::error_processor($validator)], 403);
        }

        $alreadyPending = EventApproTransaction::where('types', 'withdrawal')->where(['organizer_id' => $request->get('organizer_id'), "status" => 0])->first();
        if (empty($alreadyPending)) {
            $insertData = new EventApproTransaction();
            $insertData->types = 'withdrawal';
            $insertData->transaction_id = '';
            $insertData->amount = $request->get('amount');
            $insertData->status = '0';
            $insertData->organizer_id = $request->get('organizer_id');
            $insertData->save();
            EventOrganizer::where(['id' => $request->get('organizer_id'), 'is_approve' => 1, 'status' => 1])->update(['org_withdrawable_pending' => $request->get('amount')]);
            return response()->json(['status' => 1, 'message' => 'Payment Request Send Successfully', 'recode' => 1, 'data' => []], 200);
        }
        return response()->json(['status' => 0, 'message' => 'Already Request Is Panding', 'recode' => 0, 'data' => []], 400);
    }

    public function EventCoupon(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => ['required', function ($attribute, $value, $fail) {
                if (!User::where('id', $value)->where('is_active', 1)->exists()) {
                    $fail('The selected user is invalid or inactive.');
                }
            },],
            'coupon_code' => ['required', function ($attribute, $value, $fail) {
                if (!Coupon::where('code', $value)->where('coupon_type', 'event_order')->where('status', 1)->whereDate('start_date', '<=', date('Y-m-d'))->whereDate('expire_date', '>=', date('Y-m-d'))->exists()) {
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
        $couponData = Coupon::where('code', $request->get('coupon_code'))->where('coupon_type', 'event_order')->where('status', 1)->whereDate('start_date', '<=', date('Y-m-d'))->whereDate('expire_date', '>=', date('Y-m-d'))->first();
        $checkCoupon = EventOrder::where('coupon_id', ($couponData['id'] ?? ""))->whereIn('transaction_status', ['1', '2', '3'])->where('user_id', $request->get('user_id'))->count();
        if (($couponData['limit'] ?? 0) <= $checkCoupon) {
            // return response()->json(['status' => 0, 'message' => 'Coupon code is available for limited users which is not available at this time', 'recode' => 0, 'data' => []], 200);
            return response()->json(['status' => 0, 'message' => 'The coupon code has already been used', 'recode' => 0, 'data' => []], 200);
        }
        if ($couponData['customer_id'] != 0 && $couponData['customer_id'] != $request->user_id) {
            return response()->json(['status' => 0, 'message' => 'Invalid Coupon Code', 'recode' => 0, 'data' => []], 200);
        }
        //      400                   200                              
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
            // $coupon_amount = (($final_amount * ($couponData['discount'] ?? 0)) / 100);
            $coupon_amount =  round((($final_amount * ($couponData['discount'] ?? 0)) / 100), 2);
            if ($couponData['max_discount'] < $coupon_amount) {
                $coupon_amount =  $couponData['max_discount'];
            }
            $final_amount =  ($final_amount - $coupon_amount);
        }
        return response()->json(['status' => 1, 'message' => 'Successfully Coupon Apply', 'recode' => 1, 'data' => ['coupon_id' => $couponData['id'], 'coupon_amount' => $coupon_amount, 'final_amount' => $final_amount]], 200);
    }

    public function createorganizer(Request $request)
    {
        $rules = [
            "organizer_name" => "required",
            'organizer_pan_no' => 'required|numeric',
            'organizer_address' => 'required',
            'gst_no_type' => 'required|numeric',
            'itr_return' => 'required|numeric',
            'full_name' => 'required',
            'email_address' => "required|email",
            'contact_number' => "required|numeric",
            'beneficiary_name' => "required",
            'account_type' => "required|in:saving account,current account",
            'bank_name' => "required",
            'branch_name' => "required",
            'ifsc_code' => "required",
            'account_no' => "required|numeric",
            'pan_card_image' => "required|image|mimes:jpeg,png,jpg,gif,svg,webp,bmp",
            'aadhar_image' => "required|image|mimes:jpeg,png,jpg,gif,svg,webp,bmp",
            'cancelled_cheque_image' => "required|image|mimes:jpeg,png,jpg,gif,svg,webp,bmp",
            'image' => "required|image|mimes:jpeg,png,jpg,gif,svg,webp,bmp",
        ];

        // Add the custom message for the validation errors
        $messages = [
            'organizer_name.required' => 'Organizer Name is Empty!',
            'organizer_pan_no.required' => 'Organizer PAN Number is Empty!',
            'organizer_address.required' => 'Organizer address is Empty!',
            'gst_no_type.required' => 'Package Type is Empty!',
            'itr_return.required' => 'ITR Return status is Empty!',
            'full_name.required' => 'Full Name is Empty!',
            'email_address.required' => 'Email Address is Empty!',
            'contact_number.required' => 'Contact Number is Empty!',
            'beneficiary_name.required' => 'Beneficiary Name is Empty!',
            'account_type.required' => 'Account Type is Empty!',
            'bank_name.required' => 'Bank Name is Empty!',
            'branch_name.required' => 'Branch is Empty!',
            'ifsc_code.required' => 'IFSC Code is Empty!',
            'account_no.required' => 'Account Number is Empty!',
            'image.required' => 'Organizer User Image is Empty!',
            'pan_card_image.required' => 'Pan Card Image is Empty!',
            'aadhar_image.required' => 'Aadhar Card Image is Empty!',
            'cancelled_cheque_image.required' => 'Cancelled Cheque Image is Empty!',
        ];

        // Conditionally add 'gst_no' validation if gst_no_type is 1
        if ($request->get('gst_no_type') == 1) {
            $rules['gst_no'] = 'required|numeric';
            $messages['gst_no.required'] = 'GST Number is Empty!';
        }

        // Validate the request
        $validator = Validator::make($request->all(), $rules, $messages);


        if ($validator->fails()) {
            return response()->json(["status" => 0, "message" => "", 'errors' => Helpers::error_processor($validator), 'data' => []], 403);
        }
        $images = '';
        $pancardimage = '';
        $aadharimage = '';
        $cancelledchequeimage = '';
        if ($request->file('image')) {
            $images = ImageManager::upload('event/organizer/', 'webp', $request->file('image'));
        }
        if ($request->file('pan_card_image')) {
            $pancardimage = ImageManager::upload('event/organizer/', 'webp', $request->file('pan_card_image'));
        }
        if ($request->file('aadhar_image')) {
            $aadharimage = ImageManager::upload('event/organizer/', 'webp', $request->file('aadhar_image'));
        }
        if ($request->file('cancelled_cheque_image')) {
            $cancelledchequeimage = ImageManager::upload('event/organizer/', 'webp', $request->file('cancelled_cheque_image'));
        }

        $createorg =  new EventOrganizer();
        $createorg->organizer_name = $request->get('organizer_name');
        $createorg->organizer_pan_no = $request->get('organizer_pan_no');
        $createorg->organizer_name = $request->get('organizer_name');
        $createorg->organizer_address = $request->get('organizer_address');
        $createorg->organizer_name = $request->get('organizer_name');
        $createorg->gst_no_type = $request->get('gst_no_type');
        $createorg->gst_no = (($request->get('gst_no_type') == 1) ? $request->get('gst_no') : "");
        $createorg->itr_return = $request->get('itr_return');
        $createorg->full_name = $request->get('full_name');
        $createorg->email_address = $request->get('email_address');
        $createorg->contact_number = $request->get('contact_number');
        $createorg->beneficiary_name = $request->get('beneficiary_name');
        $createorg->account_type = $request->get('account_type');
        $createorg->bank_name = $request->get('bank_name');
        $createorg->branch_name = $request->get('branch_name');
        $createorg->ifsc_code = $request->get('ifsc_code');
        $createorg->account_no = $request->get('account_no');
        $createorg->pan_card_image = $pancardimage;
        $createorg->aadhar_image = $aadharimage;
        $createorg->cancelled_cheque_image = $cancelledchequeimage;
        $createorg->image = $images;
        $createorg->save();
        return response()->json(['status' => 1, 'message' => 'Organizer Create Successfully', 'recode' => 1, 'data' => []], 200);
    }

    public function organizergetbyid(Request $request)
    {
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        $getOrganizer = EventOrganizer::where('id', $request->get("id"))->with('translations')->first();
        $Organizer_translation = [];
        if (!empty($getOrganizer)) {
            foreach ($languages as $keys => $language) {
                $translationKeys = ['organizer_name', 'organizer_address'];
                $translate = [];
                if (!empty($getOrganizer['translations'])) {
                    foreach ($getOrganizer['translations'] as $translation) {
                        if ($translation->locale == $language && in_array($translation->key, $translationKeys)) {
                            $translate[$language][$translation->key] = $translation->value;
                        }
                    }
                }
                $lang = $language === 'in' ? 'hi' : $language;
                foreach ($translationKeys as $translationKey) {
                    $Organizer_translation["{$lang}_{$translationKey}"] = (($language == $defaultLanguage) ? $getOrganizer[$translationKey] : ($translate[$language][$translationKey] ?? $getOrganizer[$translationKey]));
                }
                $Organizer_translation['id'] =  $getOrganizer['id'];
                $Organizer_translation['organizer_pan_no'] =  $getOrganizer['organizer_pan_no'];
                $Organizer_translation['gst_no_type'] =  $getOrganizer['gst_no_type'];
                $Organizer_translation['gst_no'] =  $getOrganizer['gst_no'];
                $Organizer_translation['itr_return'] =  $getOrganizer['itr_return'];
                $Organizer_translation['full_name'] =  $getOrganizer['full_name'];
                $Organizer_translation['email_address'] =  $getOrganizer['email_address'];
                $Organizer_translation['contact_number'] =  $getOrganizer['contact_number'];
                $Organizer_translation['beneficiary_name'] =  $getOrganizer['beneficiary_name'];
                $Organizer_translation['account_type'] =  $getOrganizer['account_type'];
                $Organizer_translation['bank_name'] =  $getOrganizer['bank_name'];
                $Organizer_translation['branch_name'] =  $getOrganizer['branch_name'];
                $Organizer_translation['ifsc_code'] =  $getOrganizer['ifsc_code'];
                $Organizer_translation['account_no'] =  $getOrganizer['account_no'];
            }
            $Organizer_translation['image'] =  getValidImage(path: 'storage/app/public/event/organizer/' . $getOrganizer['image'], type: 'backend-product');
            $Organizer_translation['pan_card_image'] =  getValidImage(path: 'storage/app/public/event/organizer/' . $getOrganizer['pan_card_image'], type: 'backend-product');
            $Organizer_translation['aadhar_image'] =  getValidImage(path: 'storage/app/public/event/organizer/' . $getOrganizer['aadhar_image'], type: 'backend-product');
            $Organizer_translation['cancelled_cheque_image'] =  getValidImage(path: 'storage/app/public/event/organizer/' . $getOrganizer['cancelled_cheque_image'], type: 'backend-product');
            return response()->json(['status' => 1, 'message' => 'Organizer Find', 'recode' => 1, 'data' => $Organizer_translation], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Organizer Not Found', 'recode' => 0, 'data' => []], 400);
        }
    }

    public function organizerupdate(Request $request)
    {
        $rules = [
            "organizer_id" => ['required', function ($attribute, $value, $fail) {
                if (!EventOrganizer::where('id', $value)->exists()) {
                    $fail('Event Organizer does not exist.');
                }
            },],
            "organizer_name" => "required",
            'organizer_pan_no' => 'required|numeric',
            'organizer_address' => 'required',
            'gst_no_type' => 'required|numeric',
            'itr_return' => 'required|numeric',
            'full_name' => 'required',
            'email_address' => "required|email",
            'contact_number' => "required|numeric",
            'beneficiary_name' => "required",
            'account_type' => "required|in:saving account,current account",
            'bank_name' => "required",
            'branch_name' => "required",
            'ifsc_code' => "required",
            'account_no' => "required|numeric",
            'pan_card_image' => "nullable|image|mimes:jpeg,png,jpg,gif,svg,webp,bmp",
            'aadhar_image' => "nullable|image|mimes:jpeg,png,jpg,gif,svg,webp,bmp",
            'cancelled_cheque_image' => "nullable|image|mimes:jpeg,png,jpg,gif,svg,webp,bmp",
            'image' => "nullable|image|mimes:jpeg,png,jpg,gif,svg,webp,bmp",
        ];

        // Add the custom message for the validation errors
        $messages = [
            'organizer_name.required' => 'Organizer Name is Empty!',
            'organizer_pan_no.required' => 'Organizer PAN Number is Empty!',
            'organizer_address.required' => 'Organizer address is Empty!',
            'gst_no_type.required' => 'Package Type is Empty!',
            'itr_return.required' => 'ITR Return status is Empty!',
            'full_name.required' => 'Full Name is Empty!',
            'email_address.required' => 'Email Address is Empty!',
            'contact_number.required' => 'Contact Number is Empty!',
            'beneficiary_name.required' => 'Beneficiary Name is Empty!',
            'account_type.required' => 'Account Type is Empty!',
            'bank_name.required' => 'Bank Name is Empty!',
            'branch_name.required' => 'Branch is Empty!',
            'ifsc_code.required' => 'IFSC Code is Empty!',
            'account_no.required' => 'Account Number is Empty!',
            'image.required' => 'Organizer User Image is Empty!',
            'pan_card_image.required' => 'Pan Card Image is Empty!',
            'aadhar_image.required' => 'Aadhar Card Image is Empty!',
            'cancelled_cheque_image.required' => 'Cancelled Cheque Image is Empty!',
        ];
        if ($request->get('gst_no_type') == 1) {
            $rules['gst_no'] = 'required|numeric';
            $messages['gst_no.required'] = 'GST Number is Empty!';
        }
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json(["status" => 0, "message" => "", 'errors' => Helpers::error_processor($validator), 'data' => []], 403);
        }

        $createorg = EventOrganizer::where('id', $request->get('organizer_id'))->first();
        $createorg->organizer_name = $request->get('organizer_name');
        $createorg->organizer_pan_no = $request->get('organizer_pan_no');
        $createorg->organizer_name = $request->get('organizer_name');
        $createorg->organizer_address = $request->get('organizer_address');
        $createorg->organizer_name = $request->get('organizer_name');
        $createorg->gst_no_type = $request->get('gst_no_type');
        $createorg->gst_no = (($request->get('gst_no_type') == 1) ? $request->get('gst_no') : "");
        $createorg->itr_return = $request->get('itr_return');
        $createorg->full_name = $request->get('full_name');
        $createorg->email_address = $request->get('email_address');
        $createorg->contact_number = $request->get('contact_number');
        $createorg->beneficiary_name = $request->get('beneficiary_name');
        $createorg->account_type = $request->get('account_type');
        $createorg->bank_name = $request->get('bank_name');
        $createorg->branch_name = $request->get('branch_name');
        $createorg->ifsc_code = $request->get('ifsc_code');
        $createorg->account_no = $request->get('account_no');

        if ($request->file('image')) {
            $createorg->image = ImageManager::upload('event/organizer/', 'webp', $request->file('image'));
        }
        if ($request->file('pan_card_image')) {
            $createorg->pan_card_image = ImageManager::upload('event/organizer/', 'webp', $request->file('pan_card_image'));
        }
        if ($request->file('aadhar_image')) {
            $createorg->aadhar_image = ImageManager::upload('event/organizer/', 'webp', $request->file('aadhar_image'));
        }
        if ($request->file('cancelled_cheque_image')) {
            $createorg->cancelled_cheque_image = ImageManager::upload('event/organizer/', 'webp', $request->file('cancelled_cheque_image'));
        }

        $createorg->save();
        return response()->json(['status' => 1, 'message' => 'Organizer Updated Successfully', 'recode' => 1, 'data' => []], 200);
    }

    public function AddInterested(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'event_id' => ['required', function ($attribute, $value, $fail) {
                if (!Events::where('id', $value)->where('is_approve', 1)->where('status', 1)->exists()) {
                    $fail('Event does not exist.');
                }
            },],
            'user_id' => ['required', function ($attribute, $value, $fail) {
                if (!User::where('id', $value)->where('is_active', 1)->exists()) {
                    $fail('The selected user is invalid or inactive.');
                }
            },],
        ], [
            'event_id.required' => 'Event Id is Empty!',
            'user_id.required' => 'User ID is Empty!',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'recode' => 0, 'data' => [], 'errors' => Helpers::error_processor($validator)], 403);
        }

        $check = EventInterest::where(['user_id' => $request->user_id, 'event_id' => $request->event_id])->first();
        if (empty($check)) {
            $interest = new EventInterest();
            $interest->user_id = $request->user_id;
            $interest->event_id = $request->event_id;
            $interest->save();
            Events::where('id', $request->event_id)->update(['event_interested' => DB::raw('event_interested + 1')]);
            return response()->json(['status' => 1, 'message' => "The Event you're Interested in has been Successfully Added", 'recode' => 1, 'data' => []], 200);
        } else {
            return response()->json(['status' => 0, 'message' => "The event you're interested in has already been added successfully", 'recode' => 0, 'data' => []], 400);
        }
    }

    public function eventorderlist(Request $request)
    {
        $request->validate([
            'user_id' => ['required', function ($attribute, $value, $fail) {
                if (!User::where('id', $value)->where('is_active', 1)->exists()) {
                    $fail('The selected user is invalid or inactive.');
                }
            },],
        ]);
        if (!empty($request->id)) {
            $getData = EventOrder::where('id', $request->id)->where('user_id', $request->user_id)->with(['orderitem', 'userdata', 'eventid'])->first();
            $orderList['id'] = $getData['id'];
            $orderList['order_no'] = $getData['order_no'];
            $orderList['amount'] = $getData['amount'];
            $orderList['total_seats'] = ($getData['orderitem'][0]['no_of_seats'] ?? "");
            $get_package = \App\Models\EventPackage::where('id', ($getData['orderitem'][0]['package_id'] ?? ""))->first();
            $transPackage = [];
            if ($get_package) {
                $transPackage = $get_package->translations()->pluck('value', 'key')->toArray();
            }
            $orderList['package_id'] = ($getData['orderitem'][0]['package_id'] ?? "");
            $orderList['en_package_name'] = ($get_package['package_name'] ?? "");
            $orderList['hi_package_name'] = ($transPackage['package_name'] ?? "");
            $orderList['amount_status'] = $getData['transaction_status'];
            $orderList['user_name'] = $getData['userdata']['name'];
            $orderList['user_phone'] = $getData['userdata']['phone'];
            $orderList['user_email'] = $getData['userdata']['email'];
            if ($getData['eventid']) {
                $transEvent = $getData['eventid']->translations()->pluck('value', 'key')->toArray();
            }
            $orderList['event_id'] = ($getData['event_id'] ?? "");
            $orderList['en_event_name'] = ($getData['eventid']['event_name'] ?? "");
            $orderList['hi_event_name'] = ($transEvent['event_name'] ?? "");
            $getArtist = \App\Models\Eventartist::where('id', ($getData['eventid']['event_artist'] ?? ""))->first();
            $transEventartist = [];
            if ($getArtist) {
                $transEventartist = $getArtist->translations()->pluck('value', 'key')->toArray();
            }
            $orderList['artist_id'] = ($getData['eventid']['event_artist'] ?? "");
            $orderList['en_artist_name'] = ($getData['eventid']['eventArtist']['name'] ?? "");
            $orderList['hi_artist_name'] = ($transEventartist['name'] ?? "");
            $orderList['artist_image'] =  getValidImage(path: 'storage/app/public/event/events/' . ($getData['eventid']['eventArtist']['image'] ?? ""), type: 'product');

            $getorganizers = \App\Models\EventOrganizer::where('id', ($getData['eventid']['event_organizer_id'] ?? ""))->first();
            $transEventorganizers = [];
            if ($getorganizers) {
                $transEventorganizers = $getorganizers->translations()->pluck('value', 'key')->toArray();
            }
            $orderList['organizer_id'] = ($getData['eventid']['event_organizer_id'] ?? "");
            $orderList['en_organizer_name'] = ($getData['eventid']['organizers']['organizer_name'] ?? "");
            $orderList['hi_organizer_name'] = ($transEventorganizers['organizer_name'] ?? "");
            $orderList['organizer_image'] =  getValidImage(path: 'storage/app/public/event/organizer/' . ($getData['eventid']['organizers']['image'] ?? ""), type: 'product');

            $getcategorys = \App\Models\EventCategory::where('id', ($getData['eventid']['category_id'] ?? ""))->first();
            $transEventcategorys = [];
            if ($getcategorys) {
                $transEventcategorys = $getcategorys->translations()->pluck('value', 'key')->toArray();
            }
            $orderList['category_id'] = ($getData['eventid']['category_id'] ?? "");
            $orderList['en_category_name'] = ($getData['eventid']['categorys']['category_name'] ?? "");
            $orderList['hi_category_name'] = ($transEventcategorys['category_name'] ?? "");
            $orderList['category_image'] =  getValidImage(path: 'storage/app/public/event/category/' . ($getData['eventid']['categorys']['image'] ?? ""), type: 'product');
            $orderList['event_image'] =  getValidImage(path: 'storage/app/public/event/events/' . ($getData['eventid']['event_image'] ?? ""), type: 'product');
            $venueData  = [];
            if ($getData['eventid'] && !empty($getData['eventid']['all_venue_data'])) {
                $allVenues = json_decode($getData['eventid']['all_venue_data'], true);
                $venueData = collect($allVenues)->firstWhere('id', $getData['venue_id']);
            }

            $orderList['en_event_venue'] =  ((!empty($venueData['en_event_venue_full_address'] ?? '')) ? ucwords($venueData['en_event_venue_full_address'] ?? '') : ucwords($venueData['en_event_venue'] ?? ''));
            $orderList['hi_event_venue'] =  ((!empty($venueData['hi_event_venue_full_address'] ?? '')) ? ucwords($venueData['hi_event_venue_full_address'] ?? '') : ucwords($venueData['hi_event_venue'] ?? ''));
            $orderList['event_date'] = date('d M, Y', strtotime($venueData['date'] ?? '')) . ' ' . ($venueData['start_time'] ?? '');
            $orderList['event_booking_date'] = date('d M, Y H:i A', strtotime($getData['created_at']));
            $orderList['coupon_amount'] = $getData['coupon_amount'] ?? 0;
            $getDatas = EventsReview::where('user_id', $request->user_id)->where('order_id', $getData['id'])->where('event_id', $getData['event_id'])->first();
            $orderList['review_status'] = $getDatas['is_edited'] ?? 0;
        } else {
            $getData = EventOrder::where('user_id', $request->user_id)->with(['orderitem', 'userdata', 'eventid'])->orderBy('id', 'desc')->get();
            $orderList = [];
            if ($getData) {
                foreach ($getData as $key => $value) {
                    $orderList[$key]['id'] = $value['id'];
                    $orderList[$key]['order_no'] = $value['order_no'];
                    $orderList[$key]['amount'] = $value['amount'];
                    $orderList[$key]['total_seats'] = ($value['orderitem'][0]['no_of_seats'] ?? "");
                    $get_package = \App\Models\EventPackage::where('id', ($value['orderitem'][0]['package_id'] ?? ""))->first();
                    $transPackage = [];
                    if ($get_package) {
                        $transPackage = $get_package->translations()->pluck('value', 'key')->toArray();
                    }
                    $orderList[$key]['package_id'] = ($value['orderitem'][0]['package_id'] ?? "");
                    $orderList[$key]['en_package_name'] = ($get_package['package_name'] ?? "");
                    $orderList[$key]['hi_package_name'] = ($transPackage['package_name'] ?? "");
                    $orderList[$key]['amount_status'] = $value['transaction_status'];
                    $orderList[$key]['user_name'] = $value['userdata']['name'];
                    $orderList[$key]['user_phone'] = $value['userdata']['phone'];
                    $orderList[$key]['user_email'] = $value['userdata']['email'];
                    if ($value['eventid']) {
                        $transEvent = $value['eventid']->translations()->pluck('value', 'key')->toArray();
                    }
                    $orderList[$key]['event_id'] = ($value['event_id'] ?? "");
                    $orderList[$key]['en_event_name'] = ($value['eventid']['event_name'] ?? "");
                    $orderList[$key]['hi_event_name'] = ($transEvent['event_name'] ?? "");
                    $getArtist = \App\Models\Eventartist::where('id', ($value['eventid']['event_artist'] ?? ""))->first();
                    $transEventartist = [];
                    if ($getArtist) {
                        $transEventartist = $getArtist->translations()->pluck('value', 'key')->toArray();
                    }
                    $orderList[$key]['artist_id'] = ($value['eventid']['event_artist'] ?? "");
                    $orderList[$key]['en_artist_name'] = ($value['eventid']['eventArtist']['name'] ?? "");
                    $orderList[$key]['hi_artist_name'] = ($transEventartist['name'] ?? "");
                    $orderList[$key]['artist_image'] =  getValidImage(path: 'storage/app/public/event/events/' . ($value['eventid']['eventArtist']['image'] ?? ""), type: 'product');

                    $getorganizers = \App\Models\EventOrganizer::where('id', ($value['eventid']['event_organizer_id'] ?? ""))->first();
                    $transEventorganizers = [];
                    if ($getorganizers) {
                        $transEventorganizers = $getorganizers->translations()->pluck('value', 'key')->toArray();
                    }
                    $orderList[$key]['organizer_id'] = ($value['eventid']['event_organizer_id'] ?? "");
                    $orderList[$key]['en_organizer_name'] = ($value['eventid']['organizers']['organizer_name'] ?? "");
                    $orderList[$key]['hi_organizer_name'] = ($transEventorganizers['organizer_name'] ?? "");
                    $orderList[$key]['organizer_image'] =  getValidImage(path: 'storage/app/public/event/organizer/' . ($value['eventid']['organizers']['image'] ?? ""), type: 'product');

                    $getcategorys = \App\Models\EventCategory::where('id', ($value['eventid']['category_id'] ?? ""))->first();
                    $transEventcategorys = [];
                    if ($getcategorys) {
                        $transEventcategorys = $getcategorys->translations()->pluck('value', 'key')->toArray();
                    }
                    $orderList[$key]['category_id'] = ($value['eventid']['category_id'] ?? "");
                    $orderList[$key]['en_category_name'] = ($value['eventid']['categorys']['category_name'] ?? "");
                    $orderList[$key]['hi_category_name'] = ($transEventcategorys['category_name'] ?? "");
                    $orderList[$key]['category_image'] =  getValidImage(path: 'storage/app/public/event/category/' . ($value['eventid']['categorys']['image'] ?? ""), type: 'product');
                    $orderList[$key]['event_image'] =  getValidImage(path: 'storage/app/public/event/events/' . ($value['eventid']['event_image'] ?? ""), type: 'product');
                    $orderList[$key]['event_booking_date'] = date('d M, Y H:i A', strtotime($value['created_at']));
                }
            }
        }
        if (!empty($orderList) && count($orderList) > 0) {
            return response()->json(['status' => 1, 'message' => 'event List Successfully', 'recode' => count($orderList), 'data' => $orderList], 200);
        }
        return response()->json(['status' => 0, 'message' => 'Not Found Data', 'recode' => 0, 'data' => []], 400);
    }

    public function EventOrderPass(Request $request)
    {
        $request->validate([
            'order_id' => ['required', function ($attribute, $value, $fail) {
                if (!EventOrder::where('id', $value)->exists()) {
                    $fail('The selected Order is invalid.');
                }
            },],
            "num" => "required",
        ]);
        $orderData = EventOrder::where('id', $request->order_id)->with(['orderitem', 'eventid', 'userdata', 'coupon'])->first();
        $Data = [
            "eventname" => $orderData['eventid']['event_name'],
            "price" => $orderData['amount'],
            'total_user' => $orderData['orderitem'][0]['no_of_seats'],
        ];
        if (!empty($request->num)) {
            $Data['user'] = $request->num;
        }
        $dataString = json_encode($Data);
        $google2fa = new \PragmaRX\Google2FAQRCode\Google2FA();
        $secret = $google2fa->generateSecretKey();
        $imageData = $google2fa->getQRCodeInline(
            "Mahakal",
            $dataString,
            $secret
        );
        $ticket = $orderData['orderitem'][0]['no_of_seats'];
        $id = $request->order_id;
        if (!empty($request->num)) {
            return \Illuminate\Support\Facades\View::make('web-views.event.pdf.pass1', compact('orderData', 'imageData', 'ticket', 'id'));
            // Helpers::gen_mpdf($mpdf_view, 'event_pass_', $id.$num);
            \App\Utils\Helpers::gen_mpdf($mpdf_view, 'pass_', $request->num);
            return response()->json(["status" => 1, "message" => "Invoice generated successfully."]);
        } else {
            return response()->json(["status" => 1, "message" => "Invoice generated successfully."]);
        }
    }

    public function GetQRCodes(Request $request)
    {
        $request->validate([
            'id' => ['required', function ($attribute, $value, $fail) {
                if (!EventOrder::where('id', $value)->exists()) {
                    $fail('The selected Order is invalid.');
                }
            }]
        ]);

        $orderData = EventOrder::where('id', $request->id)->with(['orderitem', 'userdata', 'eventid'])->first();
        $orderList['amount'] = $orderData['amount'];
        $orderList['total_seats'] = ($orderData['orderitem'][0]['no_of_seats'] ?? "");
        $get_package = \App\Models\EventPackage::where('id', ($orderData['orderitem'][0]['package_id'] ?? ""))->first();
        $transPackage = [];
        if ($get_package) {
            $transPackage = $get_package->translations()->pluck('value', 'key')->toArray();
        }
        $orderList['en_package_name'] = ($get_package['package_name'] ?? "");
        $orderList['hi_package_name'] = ($transPackage['package_name'] ?? "");
        if ($orderData['eventid']) {
            $transEvent = $orderData['eventid']->translations()->pluck('value', 'key')->toArray();
        }
        $orderList['en_event_name'] = ($orderData['eventid']['event_name'] ?? "");
        $orderList['hi_event_name'] = ($transEvent['event_name'] ?? "");
        $getArtist = \App\Models\Eventartist::where('id', ($orderData['eventid']['event_artist'] ?? ""))->first();
        $transEventartist = [];
        if ($getArtist) {
            $transEventartist = $getArtist->translations()->pluck('value', 'key')->toArray();
        }
        $orderList['en_artist_name'] = ($orderData['eventid']['eventArtist']['name'] ?? "");
        $orderList['hi_artist_name'] = ($transEventartist['name'] ?? "");

        $getorganizers = \App\Models\EventOrganizer::where('id', ($orderData['eventid']['event_organizer_id'] ?? ""))->first();
        $transEventorganizers = [];
        if ($getorganizers) {
            $transEventorganizers = $getorganizers->translations()->pluck('value', 'key')->toArray();
        }
        $orderList['en_organizer_name'] = ($orderData['eventid']['organizers']['organizer_name'] ?? "");
        $orderList['hi_organizer_name'] = ($transEventorganizers['organizer_name'] ?? "");

        $getcategorys = \App\Models\EventCategory::where('id', ($orderData['eventid']['category_id'] ?? ""))->first();
        $transEventcategorys = [];
        if ($getcategorys) {
            $transEventcategorys = $getcategorys->translations()->pluck('value', 'key')->toArray();
        }
        $orderList['en_category_name'] = ($orderData['eventid']['categorys']['category_name'] ?? "");
        $orderList['hi_category_name'] = ($transEventcategorys['category_name'] ?? "");
        $orderList['event_image'] =  getValidImage(path: 'storage/app/public/event/events/' . ($orderData['eventid']['event_image'] ?? ""), type: 'product');
        $venueData  = [];
        if ($orderData['eventid'] && !empty($orderData['eventid']['all_venue_data'])) {
            $allVenues = json_decode($orderData['eventid']['all_venue_data'], true);
            $venueData = collect($allVenues)->firstWhere('id', $orderData['venue_id']);
        }
        $orderList['en_event_venue'] =  ((!empty($venueData['en_event_venue_full_address'] ?? '')) ? ucwords($venueData['en_event_venue_full_address'] ?? '') : ucwords($venueData['en_event_venue'] ?? ''));
        $orderList['hi_event_venue'] =  ((!empty($venueData['hi_event_venue_full_address'] ?? '')) ? ucwords($venueData['hi_event_venue_full_address'] ?? '') : ucwords($venueData['hi_event_venue'] ?? ''));
        $orderList['event_date'] = date('d M, Y', strtotime($venueData['date'] ?? '')) . ' ' . ($venueData['start_time'] ?? '');
        $orderList['footer_phone'] = getWebConfig(name: 'company_phone');
        $orderList['footer_email'] = getWebConfig(name: 'company_email');
        $orderList['footer_url'] = url('/');
        $orderList['footer_copyright'] = getWebConfig('company_copyright_text');
        $orderListNew = [];
        if ($orderData && $orderData['orderitem'][0] && $orderData['orderitem'][0]['user_information'] && json_decode($orderData['orderitem'][0]['user_information'] ?? "[]", true)) {
            foreach (json_decode($orderData['orderitem'][0]['user_information'] ?? "[]", true) as $num => $value) {
                $url = route("verify-code-event-pass", [$request->id, ($num ?? 1)]);
                $qrCode = new \Endroid\QrCode\QrCode($url);
                $writer = new \Endroid\QrCode\Writer\PngWriter();
                $result = $writer->write($qrCode);
                $folder = storage_path('app/public/qrcodes');
                if (!\Illuminate\Support\Facades\File::exists($folder)) {
                    \Illuminate\Support\Facades\File::makeDirectory($folder, 0777, true);
                }
                $filePath = $folder . "/qr_{$request->id}_{$num}.png";
                $result->saveToFile($filePath);
                $orderListNew[$num] = $orderList;
                $orderListNew[$num]['pass_url'] = getValidImage(path: 'storage/app/public/qrcodes/qr_' . $request->id . '_' . $num . '.png', type: 'backend-product');
                $orderListNew[$num]['pass_user_name'] = $value['name'];
            }
        }
        // if ($orderData && $orderData['orderitem'][0] && $orderData['orderitem'][0]['no_of_seats'] ?? 0) {
        //     for ($num = 0; $num < ((int)$orderData['orderitem'][0]['no_of_seats'] ?? 0); $num++) {
        //         $url = route("verify-code-event-pass", [$request->id, ($num ?? 1)]);
        //         $qrCode = new \Endroid\QrCode\QrCode($url);
        //         // $qrCode->setSize(300);
        //         $writer = new \Endroid\QrCode\Writer\PngWriter();
        //         $result = $writer->write($qrCode);
        //         $folder = storage_path('app/public/qrcodes');
        //         if (!\Illuminate\Support\Facades\File::exists($folder)) {
        //             \Illuminate\Support\Facades\File::makeDirectory($folder, 0777, true);
        //         }
        //         $filePath = $folder . "/qr_{$request->id}_{$num}.png";
        //         $result->saveToFile($filePath);
        //         $imageData[] = getValidImage(path: 'storage/app/public/qrcodes/qr_' . $request->id . '_' . $num . '.png', type: 'backend-product');
        //     }
        // }
        return response()->json(['status' => 1, 'data' => $orderListNew], 200);
    }
}
