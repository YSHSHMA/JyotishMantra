<?php

namespace App\Services;

use Illuminate\Support\Str;

use App\Traits\FileManagerTrait;

class SelfDrivingService
{
    use FileManagerTrait;

    public function policyAddData(object $request): array
    {
        return [
            'title' => $request['title'][array_search('en', $request['lang'])],
            'policy_name' => $request['policy_name'][array_search('en', $request['lang'])],
            'message' => $request['message'][array_search('en', $request['lang'])],
        ];
    }

    public function CancellationAddData(object $request): array
    {
        return [
            'title' => $request['title'][array_search('en', $request['lang'])],
            'percentage' => $request['percentage'],
            'day' => $request['day'],
            'message' => $request['message'][array_search('en', $request['lang'])],
        ];
    }

    public function AddSelfDriving(object $request)
    {

        $Cab_aboutDescription = [];
        if (isset($request['cababout']) && $request['cababout'] && count($request['cababout']) > 0) {
            $vi_id = 0;
            foreach ($request['cababout'] as $kk => $Nvalues) {
                foreach ($Nvalues as $key => $vips) {
                    if ($vips['name']) {
                        $Cab_aboutDescription[$kk][$vi_id]['id'] = $vi_id + 1;
                        $Cab_aboutDescription[$kk][$vi_id]['name'] = $vips['name'];
                        $Cab_aboutDescription[$kk][$vi_id]['details'] = $vips['details'];
                        $vi_id++;
                    }
                }
            }
        }
        ///////////////////////////////////////////////////////////////////////////////////
        $policy_info = [];
        if (isset($request['policyinfo']) && $request['policyinfo'] && count($request['policyinfo']) > 0) {
            $vi_id = 0;
            foreach ($request['policyinfo'] as $kk => $Nvalues) {
                foreach ($Nvalues as $key => $vips) {
                    if ($vips['name']) {
                        $policy_info[$kk][$vi_id]['id'] = $vi_id + 1;
                        $policy_info[$kk][$vi_id]['name'] = $vips['name'];
                        $policy_info[$kk][$vi_id]['price'] = $vips['price'];
                        if (isset($vips['children']) && $vips['children'] && count($vips['children']) > 0) {
                            $ch1 = 0;
                            foreach ($vips['children'] as $vi_ch) {
                                if ($vi_ch['name']) {
                                    $policy_info[$kk][$vi_id]['policy_info'][$ch1]['id'] = $ch1 + 1;
                                    $policy_info[$kk][$vi_id]['policy_info'][$ch1]['name'] = $vi_ch['name'];
                                    $ch1++;
                                }
                            }
                        }
                        $vi_id++;
                    }
                }
            }
        }
        ////////////////////////////////////////////////////////////////////////////
        $Cab_pickupPoint = [];
        if (isset($request['location']) && $request['location'] && count($request['location']) > 0) {
            $vi_id = 0;
            foreach ($request['location'] as $kk => $Nvalues) {
                foreach ($Nvalues as $key => $vips) {
                    if ($vips) {
                        $Cab_pickupPoint[$kk][$vi_id]['id'] = $vi_id + 1;
                        $Cab_pickupPoint[$kk][$vi_id]['point'] = $vips;
                        $vi_id++;
                    }
                }
            }
        }

        $old_images = '';
        if ($request->file('image')) {
            $old_images = $this->upload(dir: 'tour_and_travels/self_driving/', format: 'webp', image: $request->file('image'));
        }
        $multi_image = [];
        if ($request->file('images')) {
            foreach ($request->file('images') as $image) {
                if ($image->isValid()) {
                    $images = $this->upload(dir: 'tour_and_travels/self_driving/', format: 'webp', image: $image);
                    $multi_image[] = $images;
                }
            }
        }
        $tourVehiclename = \App\Models\TourVehicleCetagory::where('id', $request['category_id'])->first();
        $tourCabname = \App\Models\TourCab::where('id', $request['cab_id'])->first();
        $slug_name = Str::slug($tourVehiclename['brand_name'], '-') . '-' . Str::slug($tourCabname['name'], '-') . '-' . Str::random(6);
        return [
            "type" => $request['type'],
            'slug' => $slug_name,
            "category_id" => $request['category_id'],
            "traveller_id" => $request['traveller_id'] ?? 0,
            "cab_id" => $request['cab_id'],
            "air_conditioning_status" => $request['air_conditioning_status'],
            "car_type" => $request['car_type'],
            "basic_price" => $request['basic_price'],
            "drivers_age_details" => $request['drivers_age_details'][array_search('en', $request['lang'])],
            "tip_for_driving" => $request['tip_for_driving'][array_search('en', $request['lang'])],
            "not_local_resident" => $request['not_local_resident'][array_search('en', $request['lang'])],
            "local_resident" => $request['local_resident'][array_search('en', $request['lang'])],
            "cab_about" => json_encode($Cab_aboutDescription),
            "policy_info" => json_encode($policy_info),
            "pick_point" => json_encode($Cab_pickupPoint),
            "thumbnail" => $old_images,
            'images' => json_encode($multi_image),
        ];
    }

    public function UpdateSelfDriving(object $request)
    {

        $Cab_aboutDescription = [];
        if (isset($request['cababout']) && $request['cababout'] && count($request['cababout']) > 0) {
            $vi_id = 0;
            foreach ($request['cababout'] as $kk => $Nvalues) {
                foreach ($Nvalues as $key => $vips) {
                    if ($vips['name']) {
                        $Cab_aboutDescription[$kk][$vi_id]['id'] = $vi_id + 1;
                        $Cab_aboutDescription[$kk][$vi_id]['name'] = $vips['name'];
                        $Cab_aboutDescription[$kk][$vi_id]['details'] = $vips['details'];
                        $vi_id++;
                    }
                }
            }
        }
        ///////////////////////////////////////////////////////////////////////////////////
        $policy_info = [];
        if (isset($request['policyinfo']) && $request['policyinfo'] && count($request['policyinfo']) > 0) {
            $vi_id = 0;
            foreach ($request['policyinfo'] as $kk => $Nvalues) {
                foreach ($Nvalues as $key => $vips) {
                    if ($vips['name']) {
                        $policy_info[$kk][$vi_id]['id'] = $vi_id + 1;
                        $policy_info[$kk][$vi_id]['name'] = $vips['name'];
                        $policy_info[$kk][$vi_id]['price'] = $vips['price'];
                        if ($vips['children'] && count($vips['children']) > 0) {
                            $ch1 = 0;
                            foreach ($vips['children'] as $vi_ch) {
                                if ($vi_ch['name']) {
                                    $policy_info[$kk][$vi_id]['policy_info'][$ch1]['id'] = $ch1 + 1;
                                    $policy_info[$kk][$vi_id]['policy_info'][$ch1]['name'] = $vi_ch['name'];
                                    $ch1++;
                                }
                            }
                        }
                        $vi_id++;
                    }
                }
            }
        }
        ////////////////////////////////////////////////////////////////////////////
        $Cab_pickupPoint = [];
        if (isset($request['location']) && $request['location'] && count($request['location']) > 0) {
            $vi_id = 0;
            foreach ($request['location'] as $kk => $Nvalues) {
                foreach ($Nvalues as $key => $vips) {
                    if ($vips) {
                        $Cab_pickupPoint[$kk][$vi_id]['id'] = $vi_id + 1;
                        $Cab_pickupPoint[$kk][$vi_id]['point'] = $vips;
                        $vi_id++;
                    }
                }
            }
        }
        $getold_data = \App\Models\SelfDrivingCabs::where('id', $request['id'])->first();
        $old_images = $getold_data['thumbnail'] ?? '';
        if ($request->file('image')) {
            $old_images = $this->update(dir: 'tour_and_travels/self_driving/', oldImage: $old_images, format: 'webp', image: $request->file('image'), fileType: 'image');
        }
        $multi_image = json_decode($getold_data['images'] ?? "[]", true);
        $multi_image = is_array($multi_image) ? $multi_image : [];
        if ($request->file('images')) {
            $imageNames = [];
            foreach ($request->file('images') as $image) {
                if ($image->isValid()) {
                    $images = $this->upload(dir: 'tour_and_travels/self_driving/', format: 'webp', image: $image);
                    $imageNames[] = $images;
                }
            }

            $multi_image = json_encode(array_merge($multi_image, $imageNames));
        }
        $slug_name = $getold_data['slug'] ?? '';
        if (empty($slug_name)) {
            $tourVehiclename = \App\Models\TourVehicleCetagory::where('id', $request['category_id'])->first();
            $tourCabname = \App\Models\TourCab::where('id', $request['cab_id'])->first();
            $slug_name = Str::slug($tourVehiclename['brand_name'], '-') . '-' . Str::slug($tourCabname['name'], '-') . '-' . Str::random(6);
        }
        return [
            "type" => $request['type'],
            'slug' => $slug_name,
            "traveller_id" => $request['traveller_id'] ?? 0,
            "category_id" => $request['category_id'],
            "cab_id" => $request['cab_id'],
            "air_conditioning_status" => $request['air_conditioning_status'],
            "car_type" => $request['car_type'],
            "basic_price" => $request['basic_price'],
            "drivers_age_details" => $request['drivers_age_details'][array_search('en', $request['lang'])],
            "tip_for_driving" => $request['tip_for_driving'][array_search('en', $request['lang'])],
            "not_local_resident" => $request['not_local_resident'][array_search('en', $request['lang'])],
            "local_resident" => $request['local_resident'][array_search('en', $request['lang'])],
            "cab_about" => json_encode($Cab_aboutDescription),
            "policy_info" => json_encode($policy_info),
            "pick_point" => json_encode($Cab_pickupPoint),
            "thumbnail" => $old_images,
            'images' => $multi_image,
        ];
    }

    public function ImageRemove($old_data, $name)
    {
        if (!empty($old_data['images']) && json_decode($old_data['images'], true)) {
            $old_images = [];
            foreach (json_decode($old_data['images'], true) as $img) {
                if ($img == $name) {
                    $this->delete(filePath: '/tour_and_travels/self_driving/' . $img);
                } else {
                    $old_images[] = $img;
                }
            }
        }
        return $old_images;
    }
}
