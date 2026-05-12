<?php

namespace App\Services;

use Illuminate\Support\Str;
use App\Traits\FileManagerTrait;

class TourVisitService
{


    use FileManagerTrait;


    public function getTourVisitData(object $request): array
    {
        $package_list = [];
        $personNumber = [];
        if (($request['step'] == 2) && !empty($request['min_person']) && count($request['min_person']) > 0) {
            $pi1 = 0;
            foreach ($request['min_person'] as $key => $value) {
                $personNumber[$pi1]['id'] = ($pi1 + 1);
                $personNumber[$pi1]['min'] = $request['min_person'][$key];
                $personNumber[$pi1]['max'] = $request['max_person'][$key];
                $personNumber[$pi1]['basic'] = $request['package_price'][$key] ?? $request['person_price'][$key];
                $personNumber[$pi1]['price'] = $request['person_price'][$key];
                $pi1++;
            }
        }

        $personTransportNum = [];
        if (($request['step'] == 2) && !empty($request['start_person']) && count($request['start_person']) > 0) {
            $pti1 = 0;
            foreach ($request['start_person'] as $key => $value) {
                $personTransportNum[$pti1]['id'] = ($pti1 + 1);
                $personTransportNum[$pti1]['min'] = $request['start_person'][$key];
                $personTransportNum[$pti1]['max'] = $request['end_person'][$key];
                $personTransportNum[$pti1]['pick'] = $request['person_pick'][$key];
                $personTransportNum[$pti1]['drop'] = $request['person_drop'][$key];
                $personTransportNum[$pti1]['both'] = $request['person_both'][$key];
                $pti1++;
            }
        }
        $is_included_package = [];
        if (($request['step'] == 2) && $request['is_person_use'] == 1) {
            $is_included_package['sightseen'] = (($request['include_package']['sight_seen'] ?? '' == 'on') ? 1 : 0);
            $is_included_package['cab'] = (($request['include_package']['cab'] ?? '' == 'on') ? 1 : 0);
            $is_included_package['food'] = (($request['include_package']['food'] ?? '' == 'on') ? 1 : 0);
            $is_included_package['hotel'] = (($request['include_package']['hotel'] ?? '' == 'on') ? 1 : 0);
        }

        $cab_list_price = [];
        if (($request['step'] == 2) && !empty($request['cab_id']) && count($request['cab_id']) > 0) {
            $i = 0;
            foreach ($request['cab_id'] as $key => $value) {
                $cab_list_price[$i]['id'] = ($i + 1);
                $cab_list_price[$i]['cab_id'] = $request['cab_id'][$key];
                $cab_list_price[$i]['price'] = $request['price'][$key];
                $cab_list_price[$i]['exprice'] = json_decode($request['excharge'][$key] ?? '[]');
                $i++;
            }
        }
        $package_list_price = [];
        if (($request['step'] == 2) && !empty($request['package_id']) && count($request['package_id']) > 0) {
            $i = 0;
            foreach ($request['package_id'] as $key => $value) {
                if (!empty($request['package_id'][$key] ?? '') && ($request['pprice'][$key] ?? 0) > 0) {
                    $package_list_price[$i]['id'] = ($i + 1);
                    $package_list_price[$i]['package_id'] = $request['package_id'][$key];
                    $package_list_price[$i]['day'] = $request['pnumber'][$key];
                    $package_list_price[$i]['per_price'] = $request['pperson'][$key];
                    $package_list_price[$i]['pprice'] = $request['pprice'][$key];
                    $package_list_price[$i]['included'] = 0;
                    $i++;
                }
            }
        }
        $package_list_price2 = [];
        $ij = 0;
        if (($request['step'] == 2) && $request['is_person_use'] == 1) {
            if (!empty($request['food_package_id']) && count($request['food_package_id']) > 0) {
                foreach ($request['food_package_id'] as $key => $value) {
                    if (!empty($request['food_package_id'][$key] ?? '') && (($request['food_pprice'][$key] ?? 0) > 0 || ($request['food_check'][$key] ?? 0) == 1)) {
                        $package_list_price2[$ij]['id'] = ($ij + 1);
                        $package_list_price2[$ij]['package_id'] = $request['food_package_id'][$key];
                        $package_list_price2[$ij]['day'] = $request['food_pnumber'][$key];
                        $package_list_price2[$ij]['per_price'] = $request['food_pperson'][$key];
                        $package_list_price2[$ij]['pprice'] = $request['food_pprice'][$key] ?? 0;
                        $package_list_price2[$ij]['included'] = $request['food_check'][$key] ?? 0;
                        $ij++;
                    }
                }
            }
            if (($request['step'] == 2) && !empty($request['hotal_package_id']) && count($request['hotal_package_id']) > 0) {
                $i = $ij;
                foreach ($request['hotal_package_id'] as $key1 => $value) {
                    if (!empty($request['hotal_package_id'][$key1] ?? '') && (($request['hotal_pprice'][$key1] ?? 0) > 0 || ($request['hotal_check'][$key] ?? 0) == 1)) {
                        $package_list_price2[$i]['id'] = ($i + 1);
                        $package_list_price2[$i]['package_id'] = $request['hotal_package_id'][$key1];
                        $package_list_price2[$i]['day'] = $request['hotal_pnumber'][$key1];
                        $package_list_price2[$i]['per_price'] = $request['hotal_pperson'][$key1];
                        $package_list_price2[$i]['pprice'] = $request['hotal_pprice'][$key1];
                        $package_list_price2[$i]['included'] = $request['hotal_check'][$key1] ?? 0;
                        $i++;
                    }
                }
            }
        }

        $imageNames = [];
        if ($request->file('images')) {
            foreach ($request->file('images') as $image) {
                $images = $this->upload(dir: 'tour_and_travels/tour_visit/', format: 'webp', image: $image);
                $imageNames[] = $images;
            }
        }
        $tour_image = '';
        if ($request->file('tour_image')) {
            $tour_image = $this->upload(dir: 'tour_and_travels/tour_visit/', format: 'webp', image: $request->file('tour_image'));
        }
        $meta_image = '';
        if ($request->file('meta_image')) {
            $meta_image = $this->upload(dir: 'tour_and_travels/tour_visit/', format: 'webp', image: $request->file('meta_image'));
        }
        $time_slot_data = '';
        if (($request['step'] == 2) && !empty($request['time_slot']) && count($request['time_slot']) > 0) {
            $time_slot = [];
            $i = 0;
            $timeFormatRegex = '/^(0[1-9]|1[0-2]):([0-5][0-9]) (AM|PM)$/';
            foreach ($request['time_slot'] as $key => $value) {
                if (preg_match($timeFormatRegex, $value)) {
                    $time_slot[$i] = $value;
                    $i++;
                }
            }
            $time_slot_data = json_encode($time_slot);
        }
        if ($request['step'] == 1) {
            $array_group = [
                'tour_name' => $request['tour_name'][array_search('en', $request['lang'])],
                'slug' => Str::slug($request['tour_name'][array_search('en', $request['lang'])], '-') . '-' . Str::random(6),
                'created_id' => $request['created_id'],
                'created_type' => (($request['created_id'] == 0) ? "admin" : "company"),
                'tour_type' => $request['tour_type'],
                'cities_name' => $request['cities_name'][array_search('en', $request['lang'])],
                'country_name' => $request['country_name'][array_search('en', $request['lang'])],
                'state_name' => $request['state_name'][array_search('en', $request['lang'])],
                "percentage_off" => $request['percentage_off'] ?? 0,
                "youtube_link" => ($request['youtube_link'] ?? ""),
                "plan_type" => $request['plan_type'] ?? 0,
                "highlights" => $request['highlights'][array_search('en', $request['lang'])],
                'lat' => $request['lat'],
                "long" => $request['long'],
                'description' => $request['description'][array_search('en', $request['lang'])],
                'inclusion' => $request['inclusion'][array_search('en', $request['lang'])],
                'exclusion' => $request['exclusion'][array_search('en', $request['lang'])],
                'terms_and_conditions' => $request['terms_and_conditions'][array_search('en', $request['lang'])],
                'cancellation_policy' => $request['cancellation_policy'][array_search('en', $request['lang'])],
                'notes' => $request['notes'][array_search('en', $request['lang'])],
                'number_of_day' => $request['number_of_day'],
                'number_of_night' => $request['number_of_night'],
                'status' => 0,
                'ex_distance' => $request['ex_distance'],
                'tour_commission' => (($request['created_id'] == 0) ? 10 : (\App\Models\TourAndTravel::where('id', $request['created_id'])->first()['admin_commission'] ?? 0)),
            ];
        } elseif ($request['step'] == 2) {
            $array_group = [
                'package_list' => json_encode($package_list),
                "is_person_use" => $request['is_person_use'] ?? 0,
                'cab_list_price' => (($request['is_person_use'] == 1) ? json_encode($personNumber) : json_encode($cab_list_price)),
                'ex_transport_price' => (($request['is_person_use'] == 1) ? json_encode($personTransportNum) : json_encode([])),
                'package_list_price' => (($request['is_person_use'] == 1) ? json_encode($package_list_price2) : json_encode($package_list_price)),
                'is_included_package' => json_encode($is_included_package),
                'use_date' => $request['use_date'] ?? 0,
                'startandend_date' => (($request['use_date'] == 1) ? $request['startandend_date'] : ''),
                'pickup_time' => (($request['use_date'] == 1) ? $request['pickup_time'] : ''),
                'pickup_location' => (($request['use_date'] == 1 || $request['use_date'] == 2  || $request['use_date'] == 4) ? $request['pickup_location'] : ''),
                'pickup_lat' => (($request['use_date'] == 1 || $request['use_date'] == 2  || $request['use_date'] == 4) ? $request['pickup_lat'] : ''),
                'pickup_long' => (($request['use_date'] == 1 || $request['use_date'] == 2  || $request['use_date'] == 4) ? $request['pickup_long'] : ''),
                'time_slot' => $time_slot_data,
                'cities_tour' => (($request['use_date'] == 0) ? 1 : 0),
                'tour_commission' => (($request['created_id'] == 0) ? 10 : (\App\Models\TourAndTravel::where('id', $request['created_id'])->first()['admin_commission'] ?? 0)),
            ];
        } elseif ($request['step'] == 3) {
            $itineraryupload = '';
            if ($request->file('itineraryupload')) {
                $itineraryupload = $this->update(dir: 'tour_and_travels/tour_visit/', oldImage: '', format: $request->file('itineraryupload')->getClientOriginalExtension(), image: $request->file('itineraryupload'), fileType: 'file');
            }
            $array_group = [
                'image' => json_encode($imageNames),
                'tour_image' => $tour_image,
                "meta_title" => $request['meta_title'] ?? "",
                "meta_description" => $request['meta_description'] ?? "",
                "meta_image" => $meta_image,
                "itineraryupload" => $itineraryupload,
            ];
        } else {
            $array_group = [];
        }


        if ($request['step'] == 2 && in_array($request['customized_type'], [1, 2, 3])) {
            $array_group['customized_type'] = (($request['use_date'] == 1) ? $request['customized_type'] : 0);
            switch ($array_group['customized_type']) {
                case 1:
                    $array_group['customized_dates'] = $request['customized_weekly'];
                    break;
                case 2:
                    $array_group['customized_dates'] = $request['customized_monthly'];
                    break;
                case 3:
                    $array_group['customized_dates'] = $request['customized_yearly'];
                    break;
            }
        }

        return $array_group;
    }

    public function removedoc(object $old_data)
    {
        if (!empty($old_data['image']) && json_decode($old_data['image'], true)) {
            foreach (json_decode($old_data['image'], true) as $img) {
                $this->delete(filePath: '/tour_and_travels/tour_visit/' . $img);
            }
        }
        if (!empty($old_data['itineraryupload'])) {
            $this->delete(filePath: '/tour_and_travels/tour_visit/' . $old_data['itineraryupload']);
        }
        if (!empty($old_data['tour_image'])) {
            $this->delete(filePath: '/tour_and_travels/tour_visit/' . $old_data['tour_image']);
        }
        if (!empty($old_data['meta_image'])) {
            $this->delete(filePath: '/tour_and_travels/tour_visit/' . $old_data['meta_image']);
        }
        return true;
    }

    public function allimageremove(object $old_data)
    {
        if (!empty($old_data['image']) && json_decode($old_data['image'], true)) {
            foreach (json_decode($old_data['image'], true) as $img) {
                $this->delete(filePath: '/tour_and_travels/tour_visit/' . $img);
            }
        }
        return true;
    }

    public function ImageRemove(object $old_data, $name)
    {
        $old_images = [];
        if (!empty($old_data['image']) && json_decode($old_data['image'], true)) {
            foreach (json_decode($old_data['image'], true) as $img) {
                if ($img == $name) {
                    $this->delete(filePath: '/tour_and_travels/tour_visit/' . $img);
                } else {
                    $old_images[] = $img;
                }
            }
        }
        return $old_images;
    }

    public function removeimages(object $old_data)
    {
        if (!empty($old_data['image']) && json_decode($old_data['image'], true)) {
            foreach (json_decode($old_data['image'], true) as $img) {
                $this->delete(filePath: '/tour_and_travels/tour_visit/' . $img);
            }
        }
        $this->delete(filePath: '/tour_and_travels/tour_visit/' . $old_data['tour_image']);
        return true;
    }



    public function getUpdateTourData(object $request, $old_data): array
    {

        $package_list = [];
        // if (!empty($request['cab_id']) && count($request['cab_id']) > 0) {
        //        $i=0; foreach ($request['cab_id'] as $key => $value) {
        //             $package_list[$i]['id'] = ($i + 1);
        //             $package_list[$i]['cab_id'] = $request['cab_id'][$key];
        //             $package_list[$i]['package_id'] = $request['package_id'][$key];
        //             $package_list[$i]['people'] = $request['people'][$key];
        //             $package_list[$i]['price'] = $request['price'][$key];
        //         $i++;}
        // }
        $personNumber = [];
        if (($request['step'] == 2) && !empty($request['min_person']) && count($request['min_person']) > 0) {
            $pi1 = 0;
            foreach ($request['min_person'] as $key => $value) {
                $personNumber[$pi1]['id'] = ($pi1 + 1);
                $personNumber[$pi1]['min'] = $request['min_person'][$key];
                $personNumber[$pi1]['max'] = $request['max_person'][$key];
                $personNumber[$pi1]['basic'] = $request['package_price'][$key] ?? $request['person_price'][$key];
                $personNumber[$pi1]['price'] = $request['person_price'][$key];
                $pi1++;
            }
        }

        $personTransportNum = [];
        if (($request['step'] == 2) && !empty($request['start_person']) && count($request['start_person']) > 0) {
            $pti1 = 0;
            foreach ($request['start_person'] as $key => $value) {
                $personTransportNum[$pti1]['id'] = ($pti1 + 1);
                $personTransportNum[$pti1]['min'] = $request['start_person'][$key];
                $personTransportNum[$pti1]['max'] = $request['end_person'][$key];
                $personTransportNum[$pti1]['pick'] = $request['person_pick'][$key];
                $personTransportNum[$pti1]['drop'] = $request['person_drop'][$key];
                $personTransportNum[$pti1]['both'] = $request['person_both'][$key];
                $pti1++;
            }
        }
        $is_included_package = [];
        if (($request['step'] == 2) && $request['is_person_use'] == 1) {
            $is_included_package['sightseen'] = (($request['include_package']['sight_seen'] ?? '' == 'on') ? 1 : 0);
            $is_included_package['cab'] = (($request['include_package']['cab'] ?? '' == 'on') ? 1 : 0);
            $is_included_package['food'] = (($request['include_package']['food'] ?? '' == 'on') ? 1 : 0);
            $is_included_package['hotel'] = (($request['include_package']['hotel'] ?? '' == 'on') ? 1 : 0);
        }
        $cab_list_price = [];
        if (($request['step'] == 2) && !empty($request['cab_id']) && count($request['cab_id']) > 0) {
            $i = 0;
            foreach ($request['cab_id'] as $key => $value) {
                $cab_list_price[$i]['id'] = ($i + 1);
                $cab_list_price[$i]['cab_id'] = $request['cab_id'][$key];
                $cab_list_price[$i]['price'] = $request['price'][$key];
                $cab_list_price[$i]['exprice'] = json_decode($request['excharge'][$key] ?? '[]');
                $i++;
            }
        }
        $package_list_price = [];
        if (($request['step'] == 2) && !empty($request['package_id']) && count($request['package_id']) > 0) {
            $i = 0;
            foreach ($request['package_id'] as $key => $value) {
                if (!empty($request['package_id'][$key] ?? '') && ($request['pprice'][$key] ?? 0) > 0) {
                    $package_list_price[$i]['id'] = ($i + 1);
                    $package_list_price[$i]['package_id'] = $request['package_id'][$key];
                    $package_list_price[$i]['day'] = $request['pnumber'][$key];
                    $package_list_price[$i]['per_price'] = $request['pperson'][$key];
                    $package_list_price[$i]['pprice'] = $request['pprice'][$key];
                    $package_list_price[$i]['included'] = 0;
                    $i++;
                }
            }
        }
        $package_list_price2 = [];
        $ij = 0;
        if (($request['step'] == 2) && $request['is_person_use'] == 1) {
            if (!empty($request['food_package_id']) && count($request['food_package_id']) > 0) {
                foreach ($request['food_package_id'] as $key => $value) {
                    if (!empty($request['food_package_id'][$key] ?? '') && (($request['food_pprice'][$key] ?? 0) > 0 || ($request['food_check'][$key] ?? 0) == 1)) {
                        $package_list_price2[$ij]['id'] = ($ij + 1);
                        $package_list_price2[$ij]['package_id'] = $request['food_package_id'][$key];
                        $package_list_price2[$ij]['day'] = $request['food_pnumber'][$key];
                        $package_list_price2[$ij]['per_price'] = $request['food_pperson'][$key];
                        $package_list_price2[$ij]['pprice'] = $request['food_pprice'][$key] ?? 0;
                        $package_list_price2[$ij]['included'] = $request['food_check'][$key] ?? 0;
                        $ij++;
                    }
                }
            }
            if (!empty($request['hotal_package_id']) && count($request['hotal_package_id']) > 0) {
                $i = $ij;
                foreach ($request['hotal_package_id'] as $key => $value) {
                    if (!empty($request['hotal_package_id'][$key] ?? '') && (($request['hotal_pprice'][$key] ?? 0) > 0 || ($request['hotal_check'][$key] ?? 0) == 1)) {
                        $package_list_price2[$i]['id'] = ($i + 1);
                        $package_list_price2[$i]['package_id'] = $request['hotal_package_id'][$key];
                        $package_list_price2[$i]['day'] = $request['hotal_pnumber'][$key];
                        $package_list_price2[$i]['per_price'] = $request['hotal_pperson'][$key];
                        $package_list_price2[$i]['pprice'] = $request['hotal_pprice'][$key];
                        $package_list_price2[$i]['included'] = $request['hotal_check'][$key] ?? 0;
                        $i++;
                    }
                }
            }
        }

        $time_slot_data = '';
        if (($request['step'] == 2) && !empty($request['time_slot']) && count($request['time_slot']) > 0) {
            $time_slot = [];
            $i = 0;
            $timeFormatRegex = '/^(0[1-9]|1[0-2]):([0-5][0-9]) (AM|PM)$/';
            foreach ($request['time_slot'] as $key => $value) {
                if (preg_match($timeFormatRegex, $value)) {
                    $time_slot[$i] = $value;
                    $i++;
                }
            }
            $time_slot_data = json_encode($time_slot);
        }
        if ($request['step'] == 1) {
            $dataArray = [
                'tour_name' => $request['tour_name'][array_search('en', $request['lang'])],
                'created_id' => $request['created_id'],
                'created_type' => (($request['created_id'] == 0) ? "admin" : "company"),
                'tour_type' => $request['tour_type'],
                'cities_name' => $request['cities_name'][array_search('en', $request['lang'])],
                'country_name' => $request['country_name'][array_search('en', $request['lang'])],
                'state_name' => $request['state_name'][array_search('en', $request['lang'])],
                "percentage_off" => $request['percentage_off'] ?? 0,
                "youtube_link" => ($request['youtube_link'] ?? ""),
                "plan_type" => $request['plan_type'] ?? 0,
                'lat' => $request['lat'],
                "long" => $request['long'],
                "highlights" => $request['highlights'][array_search('en', $request['lang'])],
                'description' => $request['description'][array_search('en', $request['lang'])],
                'inclusion' => $request['inclusion'][array_search('en', $request['lang'])],
                'exclusion' => $request['exclusion'][array_search('en', $request['lang'])],
                'terms_and_conditions' => $request['terms_and_conditions'][array_search('en', $request['lang'])],
                'cancellation_policy' => $request['cancellation_policy'][array_search('en', $request['lang'])],
                'notes' => $request['notes'][array_search('en', $request['lang'])],
                'ex_distance' => $request['ex_distance'],
                'number_of_day' => $request['number_of_day'],
                'number_of_night' => $request['number_of_night'],
                'tour_commission' => (($request['created_id'] == 0) ? 10 : (\App\Models\TourAndTravel::where('id', $request['created_id'])->first()['admin_commission'] ?? 0)),
            ];
        } else if ($request['step'] == 2) {
            $dataArray = [
                'package_list' => json_encode($package_list),
                "is_person_use" => $request['is_person_use'] ?? 0,
                'cab_list_price' => (($request['is_person_use'] == 1) ? json_encode($personNumber) : json_encode($cab_list_price)),
                // 'cab_list_price' => json_encode($cab_list_price),
                'ex_transport_price' => (($request['is_person_use'] == 1) ? json_encode($personTransportNum) : json_encode([])),
                'package_list_price' => (($request['is_person_use'] == 1) ? json_encode($package_list_price2) : json_encode($package_list_price)),
                'is_included_package' => json_encode($is_included_package),
                'use_date' => $request['use_date'],
                'startandend_date' => (($request['use_date'] == 1) ? $request['startandend_date'] : ''),
                'pickup_time' => (($request['use_date'] == 1) ? $request['pickup_time'] : ''),
                'pickup_location' => (($request['use_date'] == 1 || $request['use_date'] == 2 || $request['use_date'] == 4) ? $request['pickup_location'] : ''),
                'pickup_lat' => (($request['use_date'] == 1 || $request['use_date'] == 2 || $request['use_date'] == 4) ? $request['pickup_lat'] : ''),
                'pickup_long' => (($request['use_date'] == 1 || $request['use_date'] == 2 || $request['use_date'] == 4) ? $request['pickup_long'] : ''),
                'time_slot' => $time_slot_data,
                'cities_tour' => (($request['use_date'] == 0) ? 1 : 0),
                'tour_commission' => (($request['created_id'] == 0) ? 10 : (\App\Models\TourAndTravel::where('id', $request['created_id'])->first()['admin_commission'] ?? 0)),
            ];
        } else if ($request['step'] == 3) {
            $dataArray = [
                "meta_title" => $request['meta_title'] ?? "",
                "meta_description" => $request['meta_description'] ?? "",
                'tour_commission' => (($request['created_id'] == 0) ? 10 : (\App\Models\TourAndTravel::where('id', $request['created_id'])->first()['admin_commission'] ?? 0)),
            ];
        }

        $imageNames = [];
        if (empty($old_data['slug'])) {
            $dataArray['slug'] = Str::slug($request['tour_name'][array_search('en', $request['lang'])], '-') . '-' . Str::random(6);
        }
        if (!empty($old_data['image']) && json_decode($old_data['image'], true)) {
            $imageNames = array_merge($imageNames, json_decode($old_data['image'], true));
        }

        if ($request->file('images')) {
            foreach ($request->file('images') as $image) {
                $images = $this->upload(dir: 'tour_and_travels/tour_visit/', format: 'webp', image: $image);
                $imageNames[] = $images;
            }
        }
        $dataArray['image'] = json_encode($imageNames);
        if ($request->file('tour_image')) {
            $dataArray['tour_image'] = $this->update(dir: 'tour_and_travels/tour_visit/', oldImage: $old_data['tour_image'], format: 'webp', image: $request->file('tour_image'));
        }
        if ($request->file('meta_image')) {
            $dataArray['meta_image'] = $this->update(dir: 'tour_and_travels/tour_visit/', oldImage: $old_data['meta_image'], format: 'webp', image: $request->file('meta_image'));
        }
        if ($request->file('itineraryupload')) {
            $dataArray['itineraryupload'] = $this->update(dir: 'tour_and_travels/tour_visit/', oldImage: $old_data['itineraryupload'], format: $request->file('itineraryupload')->getClientOriginalExtension(), image: $request->file('itineraryupload'), fileType: 'file');
        }

        
        if ($request['step'] == 2 && in_array($request['customized_type'], [1, 2, 3])) {
            $dataArray['customized_type'] = (($request['use_date'] == 1) ? $request['customized_type'] : 0);
            switch ($dataArray['customized_type']) {
                case 1:
                    $dataArray['customized_dates'] = $request['customized_weekly'];
                    break;
                case 2:
                    $dataArray['customized_dates'] = $request['customized_monthly'];
                    break;
                case 3:
                    $dataArray['customized_dates'] = $request['customized_yearly'];
                    break;
            }
        }

        return $dataArray;
    }

    /////////////////////////////////////


    public function getTourVisitPlace(object $request): array
    {
        $imageNames = [];
        if ($request->file('images')) {
            foreach ($request->file('images') as $image) {
                $images = $this->upload(dir: 'tour_and_travels/tour_visit_place/', format: 'webp', image: $image);
                $imageNames[] = $images;
            }
        }
        return [
            'tour_visit_id' => $request['tour_visit_id'],
            'name' => $request['name'][array_search('en', $request['lang'])],
            'description' => $request['description'][array_search('en', $request['lang'])],
            'time' => $request['time'][array_search('en', $request['lang'])],
            'status' => 0,
            'images' => json_encode($imageNames),
        ];
    }

    public function getTourVisitPlaceupdate(object $request, $old_data): array
    {
        $imageNames = [];
        if (!empty($old_data['images']) && json_decode($old_data['images'])) {
            foreach (json_decode($old_data['images'], true) as $key => $value) {
                $imageNames[] = $value;
            }
        }
        if ($request->file('images')) {
            foreach ($request->file('images') as $image) {
                $images = $this->upload(dir: 'tour_and_travels/tour_visit_place/', format: 'webp', image: $image);
                $imageNames[] = $images;
            }
        }
        return [
            'tour_visit_id' => $request['tour_visit_id'],
            'name' => $request['name'][array_search('en', $request['lang'])],
            'description' => $request['description'][array_search('en', $request['lang'])],
            'time' => $request['time'][array_search('en', $request['lang'])],
            'images' => json_encode($imageNames),
        ];
    }

    public function removeplaceimages(object $old_data)
    {
        if (!empty($old_data['images']) && json_decode($old_data['images'], true)) {
            foreach (json_decode($old_data['images'], true) as $img) {
                $this->delete(filePath: '/tour_and_travels/tour_visit_place/' . $img);
            }
        }
        return true;
    }

    public function VisitImageRemove(object $old_data, $name)
    {

        if (!empty($old_data['images']) && json_decode($old_data['images'], true)) {
            $old_images = [];
            foreach (json_decode($old_data['images'], true) as $img) {
                if ($img == $name) {
                    $this->delete(filePath: '/tour_and_travels/tour_visit_place/' . $img);
                } else {
                    $old_images[] = $img;
                }
            }
        }
        return $old_images;
    }
}
