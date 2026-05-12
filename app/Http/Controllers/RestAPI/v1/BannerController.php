<?php

namespace App\Http\Controllers\RestAPI\v1;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Product;
use App\Utils\Helpers;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    // public function get_banners(Request $request)
    // {
    //     $theme_name = theme_root_path();

    //     $banner_array = match ($theme_name) {
    //         'default' => array(
    //             'Main Banner',
    //             'Footer Banner',
    //             'Popup Banner',
    //             'Main Section Banner',
    //             'Mahakal Banner',
    //             'Mahakal App Banner',
    //             'Astrology Banner',
    //             'Auspicious Occasion Banner',
    //             'Chat Banner',
    //             'Events Banner',
    //             'E Commerece App Banner',
    //         ),
    //         'theme_aster' => array(
    //             'Main Banner',
    //             'Footer Banner',
    //             'Popup Banner',
    //             'Header Banner',
    //             'Sidebar Banner',
    //             'Top Side Banner',
    //             'Main Section Banner',
    //             'Mahakal Banner',
    //             'Mahakal App Banner',
    //             'Astrology Banner',
    //             'Auspicious Occasion Banner',
    //             'Chat Banner',
    //             'Events Banner',
    //             'E Commerece App Banner',
    //         ),
    //         'theme_fashion' => array(
    //             'Main Banner',
    //             'Footer Banner',
    //             'Popup Banner',
    //             'Main Section Banner',
    //             'Promo Banner Left',
    //             'Promo Banner Middle Top',
    //             'Promo Banner Middle Bottom',
    //             'Promo Banner Right',
    //             'Promo Banner Bottom',
    //             'Mahakal Banner',
    //             'Mahakal App Banner',
    //             'Astrology Banner',
    //             'Auspicious Occasion Banner',
    //             'Chat Banner',
    //             'Events Banner',
    //             'E Commerece App Banner',
    //         ),
    //     };

    //     $banners = Banner::whereIn('banner_type',$banner_array)->where(['published' => 1, 'theme'=>$theme_name])->get();
    //     $pro_ids = [];
    //     $data = [];
    //     foreach ($banners as $banner) {
    //         if ($banner['resource_type'] == 'product' && !in_array($banner['resource_id'], $pro_ids)) {
    //             array_push($pro_ids,$banner['resource_id']);
    //             $product = Product::find($banner['resource_id']);
    //             $banner['product'] = Helpers::product_data_formatting($product);
    //         }

    //         if ($banner['resource_type'] == 'mahakalapp') {
    //             $poojaId = $banner['pooja_id'];
    //             $resourceId = $banner['resource_id'];
    //             $mahakalData = null;
            
    //             if (!$mahakalData) {
    //                 $service = \App\Models\Service::find($poojaId);
    //                 if ($service) {
    //                     if ($service->category_id == 33) {
    //                         // Pooja logic
    //                         $nextBooking = \App\Models\PoojaForecast::where('service_id', $service->id)
    //                             ->where('is_expired', 0)
    //                             ->whereIn('type', ['weekly', 'special'])
    //                             ->whereHas('service', function ($query) {
    //                                 $query->where('status', 1)
    //                                       ->where('product_type', 'pooja');
    //                             })
    //                             ->orderBy('booking_date', 'asc')
    //                             ->first();
                
    //                         $nextPoojaDay = $nextBooking?->booking_date;
                
    //                         if ($nextPoojaDay instanceof \DateTime) {
    //                             $nextPoojaDate = $nextPoojaDay->format('Y-m-d H:i:s');
    //                         } else {
    //                             $nextPoojaDate = $nextPoojaDay
    //                                 ? date('Y-m-d H:i:s', strtotime($nextPoojaDay))
    //                                 : null;
    //                         }
                
    //                         $mahakalData = [
    //                             'id'   => $service->id,
    //                             'name' => $service->name,
    //                             'slug' => $service->slug,
    //                             'next_pooja_date' => $nextPoojaDate,
    //                             'type' => 'pooja',
    //                         ];
    //                     } elseif ($service->category_id == 39) {
    //                         // Counselling logic
    //                         $mahakalData = [
    //                             'id'   => $service->id,
    //                             'name' => $service->name,
    //                             'slug' => $service->slug,
    //                             'type' => 'counselling',
    //                         ];
    //                     }
    //                 }
    //             }
                
            
    //             if (!$mahakalData && in_array($resourceId, [50, 51])) {
    //                 $vipPooja = \App\Models\Vippooja::find($poojaId);
    //                 if ($vipPooja && $vipPooja->status == 1) {
    //                     if ($resourceId == 50 && $vipPooja->is_anushthan == 0) {
    //                         $mahakalData = [
    //                             'id'   => $vipPooja->id,
    //                             'name' => $vipPooja->name,
    //                             'slug' => $vipPooja->slug,
    //                             'type' => 'vip',
    //                         ];
    //                     } elseif ($resourceId == 51 && $vipPooja->is_anushthan == 1) {
    //                         $mahakalData = [
    //                             'id'   => $vipPooja->id,
    //                             'name' => $vipPooja->name,
    //                             'slug' => $vipPooja->slug,
    //                             'type' => 'anusthan',
    //                         ];
    //                     }
    //                 }
    //             }
            
    //             if (!$mahakalData && $resourceId == 52) {
    //                 $chadhava = \App\Models\Chadhava::find($poojaId);
    //                 if ($chadhava && $chadhava->status == 1) {
                
    //                     $chadhavaData = [];

    //                     $chadhava_week = json_decode($chadhava->chadhava_week);
    //                     $nextChadhavaDay = $chadhava->getNextAvailableDate();
                
    //                     if ($chadhava->chadhava_type == 1) {
    //                         $startDate = $chadhava->start_date;
    //                         $endDate = $chadhava->end_date;
    //                         $currentDate = time();
    //                         $formattedDates = [];
    //                         $ChadhavaearliestDate = '';
                
    //                         if ($startDate && $endDate && $startDate <= $endDate) {
    //                             $currentDateIter = $startDate->copy();
    //                             while ($currentDateIter <= $endDate) {
    //                                 $formattedDates[] = $currentDateIter->format('Y-m-d');
    //                                 $currentDateIter->addDay();
    //                             }
                
    //                             foreach ($formattedDates as $date) {
    //                                 if (strtotime($date) > $currentDate) {
    //                                     $ChadhavaearliestDate = date('Y-m-d H:i:s', strtotime($date));
    //                                     break;
    //                                 }
    //                             }
    //                         }
                
    //                         $chadhavaData['next_chadhava_date'] = $ChadhavaearliestDate ?: null;
    //                         $chadhavaData['chadhava_type_text'] = 'Date Wise Chadhava';
    //                     } else {
    //                         // Weekly Chadhava
    //                         $chadhavaData['next_chadhava_date'] = $nextChadhavaDay instanceof \DateTime
    //                             ? $nextChadhavaDay->format('Y-m-d H:i:s')
    //                             : ($nextChadhavaDay ? date('Y-m-d H:i:s', strtotime($nextChadhavaDay)) : null);
                
    //                         $chadhavaData['chadhava_type_text'] = 'Weekly Chadhava';
    //                     }
                
    //                     $mahakalData = [
    //                         'id' => $chadhava->id,
    //                         'name' => $chadhava->name,
    //                         'slug' => $chadhava->slug,
    //                         'type' => 'chadhava',
    //                         'next_chadhava_date' => $chadhavaData['next_chadhava_date'],
    //                         'chadhava_type_text' => $chadhavaData['chadhava_type_text'],
    //                     ];
    //                 }
    //             }

    //             if (!$mahakalData && in_array($resourceId, [272, 273, 274, 275, 276])) {    

    //                 if ($resourceId == 272) {
    //                     $tour = \App\Models\TourVisits::find($poojaId);
    //                     if ($tour && $tour->status == 1) {
    //                         $mahakalData = [
    //                             'id'   => $tour->id,
    //                             'name' => $tour->tour_name,
    //                             'slug' => $tour->slug,
    //                             'type' => 'tour',
    //                         ];
    //                     }
    //                 } 
    //                 elseif ($resourceId == 273) {
    //                     $event = \App\Models\Events::find($poojaId);
    //                     if ($event && $event->status == 1) {
    //                         $mahakalData = [
    //                             'id'   => $event->id,
    //                             'name' => $event->event_name,
    //                             'type' => 'event',
    //                         ];
    //                     }
    //                 }
    //                 elseif ($resourceId == 274) {
    //                     $donation = \App\Models\DonateAds::find($poojaId);
    //                     if ($donation && $darshan->status == 1) {
    //                         $mahakalData = [
    //                             'id'   => $donation->id,
    //                             'name' => $donation->name,
    //                             'type' => 'donation',
    //                         ];
    //                     }
    //                 }
    //                 elseif ($resourceId == 275) {
    //                     $darshan = \App\Models\Temple::find($poojaId);
    //                     if ($darshan && $darshan->status == 1) {
    //                         $mahakalData = [
    //                             'id'   => $darshan->id,
    //                             'name' => $darshan->name,
    //                             'type' => 'temple',
    //                         ];
    //                     }
    //                 }
    //                 elseif ($resourceId == 276) {
    //                     $offlinepooja = \App\Models\PoojaOffline::find($poojaId);
    //                     if ($offlinepooja && $offlinepooja->status == 1) {
    //                         $mahakalData = [
    //                             'id'   => $offlinepooja->id,
    //                             'name' => $offlinepooja->name,
    //                             'slug' => $offlinepooja->slug,
    //                             'type' => 'offlinepooja',
    //                         ];
    //                     }
    //                 }
                
    //             }                
                
    //             $banner['mahakalapp'] = $mahakalData;
    //         }
            
    //         $data[] = $banner;
    //     }

    //     return response()->json($data, 200);

    // }

    public function get_banners(Request $request)
    {
        $theme_name = theme_root_path();

        $banner_array = match ($theme_name) {
            'default' => array(
                'Main Banner',
                'Footer Banner',
                'Popup Banner',
                'Main Section Banner',
                'Mahakal Banner',
                'Mahakal App Banner',
                'Astrology Banner',
                'Auspicious Occasion Banner',
                'Chat Banner',
                'Events Banner',
                'E Commerece App Banner',
            ),
            'theme_aster' => array(
                'Main Banner',
                'Footer Banner',
                'Popup Banner',
                'Header Banner',
                'Sidebar Banner',
                'Top Side Banner',
                'Main Section Banner',
                'Mahakal Banner',
                'Mahakal App Banner',
                'Astrology Banner',
                'Auspicious Occasion Banner',
                'Chat Banner',
                'Events Banner',
                'E Commerece App Banner',
            ),
            'theme_fashion' => array(
                'Main Banner',
                'Footer Banner',
                'Popup Banner',
                'Main Section Banner',
                'Promo Banner Left',
                'Promo Banner Middle Top',
                'Promo Banner Middle Bottom',
                'Promo Banner Right',
                'Promo Banner Bottom',
                'Mahakal Banner',
                'Mahakal App Banner',
                'Astrology Banner',
                'Auspicious Occasion Banner',
                'Chat Banner',
                'Events Banner',
                'E Commerece App Banner',
            ),
        };

        $banners = Banner::whereIn('banner_type', $banner_array)
            ->where(['published' => 1, 'theme' => $theme_name])
            ->get();

        $pro_ids = [];
        $data = [];

        foreach ($banners as $banner) {
            if ($banner['resource_type'] == 'product' && !in_array($banner['resource_id'], $pro_ids)) {
                array_push($pro_ids, $banner['resource_id']);
                $product = Product::find($banner['resource_id']);
                $banner['product'] = Helpers::product_data_formatting($product);
            }

            if ($banner['resource_type'] == 'mahakalapp') {
                $poojaId = $banner['pooja_id'];
                $resourceId = $banner['resource_id'];
                $mahakalData = null;

                // ✅ FIX: Service logic sirf tab chale jab resourceId 50,51,52,272–276 me NA ho
                if (!in_array($resourceId, [50, 51, 52, 272, 273, 274, 275, 276])) {
                    $service = \App\Models\Service::find($poojaId);
                    if ($service) {
                        if ($service && $service->status == 1 && $service->category_id == 33) {
                            $nextBooking = \App\Models\PoojaForecast::where('service_id', $service->id)
                                ->where('is_expired', 0)
                                ->whereIn('type', ['weekly', 'special'])
                                ->whereHas('service', function ($query) {
                                    $query->where('status', 1)
                                        ->where('product_type', 'pooja');
                                })
                                ->orderBy('booking_date', 'asc')
                                ->first();

                            $nextPoojaDay = $nextBooking?->booking_date;

                            if ($nextPoojaDay instanceof \DateTime) {
                                $nextPoojaDate = $nextPoojaDay->format('Y-m-d H:i:s');
                            } else {
                                $nextPoojaDate = $nextPoojaDay
                                    ? date('Y-m-d H:i:s', strtotime($nextPoojaDay))
                                    : null;
                            }

                            $mahakalData = [
                                'id' => $service->id,
                                'name' => $service->name,
                                'slug' => $service->slug,
                                'next_pooja_date' => $nextPoojaDate,
                                'type' => 'pooja',
                            ];
                        } elseif ($service->category_id == 39) {
                            $mahakalData = [
                                'id' => $service->id,
                                'name' => $service->name,
                                'slug' => $service->slug,
                                'type' => 'counselling',
                            ];
                        }
                    }
                }

                if (!$mahakalData && in_array($resourceId, [50, 51])) {
                    $vipPooja = \App\Models\Vippooja::find($poojaId);
                    if ($vipPooja && $vipPooja->status == 1) {
                        if ($resourceId == 50 && $vipPooja->is_anushthan == 0) {
                            $mahakalData = [
                                'id' => $vipPooja->id,
                                'name' => $vipPooja->name,
                                'slug' => $vipPooja->slug,
                                'type' => 'vip',
                            ];
                        } elseif ($resourceId == 51 && $vipPooja->is_anushthan == 1) {
                            $mahakalData = [
                                'id' => $vipPooja->id,
                                'name' => $vipPooja->name,
                                'slug' => $vipPooja->slug,
                                'type' => 'anusthan',
                            ];
                        }
                    }
                }

                if (!$mahakalData && $resourceId == 52) {
                    $chadhava = \App\Models\Chadhava::find($poojaId);
                    if ($chadhava && $chadhava->status == 1) {

                        $chadhavaData = [];

                        $chadhava_week = json_decode($chadhava->chadhava_week);
                        $nextChadhavaDay = $chadhava->getNextAvailableDate();

                        if ($chadhava->chadhava_type == 1) {
                            $startDate = $chadhava->start_date;
                            $endDate = $chadhava->end_date;
                            $currentDate = time();
                            $formattedDates = [];
                            $ChadhavaearliestDate = '';

                            if ($startDate && $endDate && $startDate <= $endDate) {
                                $currentDateIter = $startDate->copy();
                                while ($currentDateIter <= $endDate) {
                                    $formattedDates[] = $currentDateIter->format('Y-m-d');
                                    $currentDateIter->addDay();
                                }

                                foreach ($formattedDates as $date) {
                                    if (strtotime($date) > $currentDate) {
                                        $ChadhavaearliestDate = date('Y-m-d H:i:s', strtotime($date));
                                        break;
                                    }
                                }
                            }

                            $chadhavaData['next_chadhava_date'] = $ChadhavaearliestDate ?: null;
                            $chadhavaData['chadhava_type_text'] = 'Date Wise Chadhava';
                        } else {
                            $chadhavaData['next_chadhava_date'] = $nextChadhavaDay instanceof \DateTime
                                ? $nextChadhavaDay->format('Y-m-d H:i:s')
                                : ($nextChadhavaDay ? date('Y-m-d H:i:s', strtotime($nextChadhavaDay)) : null);

                            $chadhavaData['chadhava_type_text'] = 'Weekly Chadhava';
                        }

                        $mahakalData = [
                            'id' => $chadhava->id,
                            'name' => $chadhava->name,
                            'slug' => $chadhava->slug,
                            'type' => 'chadhava',
                            'next_chadhava_date' => $chadhavaData['next_chadhava_date'],
                            'chadhava_type_text' => $chadhavaData['chadhava_type_text'],
                        ];
                    }
                }

                if (!$mahakalData && in_array($resourceId, [272, 273, 274, 275, 276])) {

                    if ($resourceId == 272) {
                        $tour = \App\Models\TourVisits::find($poojaId);
                        if ($tour && $tour->status == 1) {
                            $mahakalData = [
                                'id' => $tour->id,
                                'name' => $tour->tour_name,
                                'slug' => $tour->slug,
                                'type' => 'tour',
                            ];
                        }
                    } elseif ($resourceId == 273) {
                        $event = \App\Models\Events::find($poojaId);
                        if ($event && $event->status == 1) {
                            $mahakalData = [
                                'id' => $event->id,
                                'name' => $event->event_name,
                                'type' => 'event',
                            ];
                        }
                    } elseif ($resourceId == 274) {
                        $donation = \App\Models\DonateAds::find($poojaId);
                        if ($donation && $donation->status == 1) {
                            $mahakalData = [
                                'id' => $donation->id,
                                'name' => $donation->name,
                                'type' => 'donation',
                            ];
                        }
                    } elseif ($resourceId == 275) {
                        $darshan = \App\Models\Temple::find($poojaId);
                        if ($darshan && $darshan->status == 1) {
                            $mahakalData = [
                                'id' => $darshan->id,
                                'name' => $darshan->name,
                                'type' => 'temple',
                            ];
                        }
                    } elseif ($resourceId == 276) {
                        $offlinepooja = \App\Models\PoojaOffline::find($poojaId);
                        if ($offlinepooja && $offlinepooja->status == 1) {
                            $mahakalData = [
                                'id' => $offlinepooja->id,
                                'name' => $offlinepooja->name,
                                'slug' => $offlinepooja->slug,
                                'type' => 'offlinepooja',
                            ];
                        }
                    }
                }

                $banner['mahakalapp'] = $mahakalData;
            }

            $data[] = $banner;
        }

        return response()->json($data, 200);
    }
}
