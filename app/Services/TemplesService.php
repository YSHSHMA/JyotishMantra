<?php

namespace App\Services;
use Illuminate\Support\Str;
use App\Traits\FileManagerTrait;
class TemplesService
{

    
    use FileManagerTrait;

    public function getProcessedImages(object $request): array
    {
        $imageNames = [];
        if ($request->file('images')) {
            foreach ($request->file('images') as $image) {
                $images = $this->upload(dir: 'temple/', format: 'webp', image: $image);
                $imageNames[] = $images;
                if($request->has('images_active') && $request->has('images') && count($request['images']) > 0){
                    $imageNames[] = [
                        'image_name' => $images,
                    ];
                }
            }
        }
        return [
            'image_names' => $imageNames ?? []
        ];

    }


    public function getAddTemplesData(object $request, string $addedBy): array
    {   
        // dd($addedBy);
        $processedImages = $this->getProcessedImages(request: $request);
        return [
            'added_by' => $addedBy,
            'user_id' => $addedBy == 'admin' ? auth('admin')->id() : auth('seller')->id(),
            'category_id'=>$request['category_id'],
            'name' => $request['name'][array_search('en', $request['lang'])],
            'slug' => $this->getSlug($request),
            'short_description' => $request['short_description'][array_search('en', $request['lang'])],
            'details' => $request['details'][array_search('en', $request['lang'])],
            'more_details' => $request['more_details'][array_search('en', $request['lang'])],
            'country_id'=>$request['country_id'],
            'city_id' => $request['city_id'],
            'district_id' => $request['district_id'],
            'state_id' => $request['state_id'],
            'entry_fee' => $request['entry_fee'],
            'opening_time' => $request['opening_time'],
            'closeing_time' => $request['closeing_time'],
            'facilities' => $request['facilities'][array_search('en', $request['lang'])],
            'tips_restrictions' => $request['tips_restrictions'][array_search('en', $request['lang'])],
            'require_time' => ($request['require_time']),
            'video_provider' => 'youtube',
            'video_url' => $request['video_url'],
            'status' => $addedBy == 'admin' ? 1 : 0,
            'images' => json_encode($processedImages['image_names']),
            'logo' => $this->upload(dir: 'temple/logo/', format: 'png', image: $request['logo']),
            'thumbnail' => $this->upload(dir: 'temple/thumbnail/', format: 'png', image: $request['image']),
            'meta_title' => $request['meta_title'],
            'meta_description' => $request['meta_description'],
            'meta_image' => $this->upload(dir: 'temple/meta/', format: 'png', image: $request['meta_image']),
            'longitude' => $request['longitude'],
            'latitude' => $request['latitude'],
            'expect_details'=> $request['expect_details'][array_search('en', $request['lang'])],
            'tips_details'=> $request['tips_details'][array_search('en', $request['lang'])],
            'temple_known'=> $request['temple_known'][array_search('en', $request['lang'])],
            'temple_services'=> $request['temple_services'][array_search('en', $request['lang'])],
            'temple_aarti'=> $request['temple_aarti'][array_search('en', $request['lang'])],
            'tourist_place'=> $request['tourist_place'][array_search('en', $request['lang'])],
            'temple_local_food'=>$request['temple_local_food'][array_search('en', $request['lang'])]
        ];
    }

    public function getAddTemplesPackageData(object $request, string $addedBy): array
    {   
        return [
            'addedBy'   => $addedBy,
            'name' => $request['name'][array_search('en', $request['lang'])],
            'description' => $request['short_description'][array_search('en', $request['lang'])],
            'type'    => $request['type'][array_search('en', $request['lang'])],
            'status' => $addedBy == 'admin' ? 1 : 0,
        ];
    }

    public function getAddTemplesPackagePriceData(object $request, string $addedBy): array
    {   
        $dataArray = [
            'addedBy'   => $addedBy,
            'package_id'    => $request['package_id'],
            'varient_name' => $request['name'][array_search('en', $request['lang'])],
            'description' => $request['details'][array_search('en', $request['lang'])],
            'temple_id'    => $request['temple_id'],
            'trust_id'    => $request['trust_id'],
            'base_price'    => $request['base_price'],
            'daily_slots_limit'    => $request['daily_slots_limit'],
            'image'    => $request['color'],
            'image'    =>   $this->upload(dir: 'temple/package/', format: 'png', image: $request['image']),
            'max_qty_per_day'    => $request['max_qty_per_day'],
            'max_duration_hour'    => $request['max_duration_hour'],
            'platform_fee_percentage'    => $request['platform_fee_percentage'],
            'receipt_fee_percentage'    => $request['receipt_fee_percentage'],
            'gst_rate'    => $request['gst_rate'],
            'is_available' => $request['is_available'], //  corrected
            'status' => $addedBy == 'admin' ? 1 : 0,
        ];
        return $dataArray;
    }

    public function getEditTemplesPackagePriceData(object $request, string $addedBy): array
    {   
        $dataArray = [
            'addedBy'   => $addedBy,
            'package_id'    => $request['package_id'],
            'varient_name'  => $request['name'][array_search('en', $request['lang'])],
            'description'   => $request['details'][array_search('en', $request['lang'])],
            'temple_id'     => $request['temple_id'],
            'trust_id'      => $request['trust_id'],
            'base_price'    => $request['base_price'],
            'daily_slots_limit' => $request['daily_slots_limit'],
            'max_qty_per_day'   => $request['max_qty_per_day'],
            'max_duration_hour' => $request['max_duration_hour'],
            'platform_fee_percentage' => $request['platform_fee_percentage'],
            'receipt_fee_percentage'  => $request['receipt_fee_percentage'],
            'gst_rate'    => $request['gst_rate'],
            'is_available' => $request['is_available'], 
            'status'      => $addedBy == 'admin' ? 1 : 0,
        ];
        if (!empty($request['color'])) {
            $dataArray['color'] = $request['color'];
        }
        if ($request->hasFile('image')) {
            $dataArray['image'] = $this->upload(
                dir: 'temple/package/',
                format: 'png',
                image: $request['image']
            );
        }
        return $dataArray;
    }





    public function getSlug(object $request): string
    {
        return Str::slug($request['name'][array_search('en', $request['lang'])], '-') . '-' . Str::random(6);
    }

    public function getStatesDropdown(object $request, object $cities): string
    {
        $dropdown = '<option value="' . 0 . '" disabled selected>---'.translate("Select").'---</option>';
        // dd($cities);
        foreach ($cities as $row) {
            if ($row->id == $request['cities']) {
                $dropdown .= '<option value="' . $row->id . '" selected >' . $row->city . '</option>';
            } else {
                $dropdown .= '<option value="' . $row->id . '">' . $row->city . '</option>';
            }
        }

        return $dropdown;
    }

    public function deleteImages(object $temple): bool
    {
        if (!is_null($temple['images'])) {
            $images = json_decode($temple['images']);
            if (!is_null($images)) {
                foreach ($images as $image) {
                    $this->delete(filePath: '/temple/' . $image);
                }
            }
        }
        $this->delete(filePath: '/temple/thumbnail/' . $temple['thumbnail']);
        return true;
    }

  // Update Service GET
    public function getUpdateTempleData(object $request, object $temple, string $updateBy): array
    {
        $processedImages = $this->getProcessedUpdateImages(request: $request,  temple: $temple);
        $matchingTrust = \App\Models\DonateTrust::all()
            ->first(function ($item) use ($temple) {
                $ids = json_decode($item->trust_temple_id, true);
                return in_array($temple['id'], (array) $ids);
            });

        $donateTrustId = $matchingTrust ? $matchingTrust->id : null;

        $dataArray = [
            'name' => $request['name'][array_search('en', $request['lang'])],
            'category_id'=>$request['category_id'],
            'short_description' => $request['short_description'][array_search('en', $request['lang'])],
            'details' => $request['details'][array_search('en', $request['lang'])],
            'more_details' => $request['more_details'][array_search('en', $request['lang'])],
            'country_id'=>$request['country_id'],
            'city_id' => $request['city_id'],
            'state_id' => $request['state_id'],
            'district_id' => $request['district_id'],
            'entry_fee' => $request['entry_fee'],
            'opening_time' => $request['opening_time'],
            'closeing_time' => $request['closeing_time'],
            'facilities' => $request['facilities'][array_search('en', $request['lang'])],
            'tips_restrictions' => $request['tips_restrictions'][array_search('en', $request['lang'])],
            'require_time' => ($request['require_time']),
            'images' => json_encode($processedImages['image_names']),
            'video_provider' => 'youtube',
            'video_url' => $request['video_url'],          
            'meta_title' => $request['meta_title'],
            'meta_description' => $request['meta_description'],
            'meta_image' => $request->file('meta_image') ? $this->update(dir: 'temple/meta/', oldImage: $temple['meta_image'], format: 'png', image: $request['meta_image']) : $temple['meta_image'],
            'longitude' => $request['longitude'],
            'latitude' => $request['latitude'],
            'expect_details'=> $request['expect_details'][array_search('en', $request['lang'])],
            'tips_details'=> $request['tips_details'][array_search('en', $request['lang'])],
            'temple_known'=> $request['temple_known'][array_search('en', $request['lang'])],
            'temple_services'=> $request['temple_services'][array_search('en', $request['lang'])],
            'temple_aarti'=> $request['temple_aarti'][array_search('en', $request['lang'])],
            'tourist_place'=> $request['tourist_place'][array_search('en', $request['lang'])],
            'temple_local_food'=>$request['temple_local_food'][array_search('en', $request['lang'])],
            'trust_id'=>$donateTrustId,
            'aadhaar_verify_status'=>$request['aadhaar_verify_status']??0,
        ];
        if(empty($temple['slug'])){
            $dataArray['slug'] = $this->getSlug($request);
        }

        if ($request->file('logo')) {
            $dataArray += [
                'logo' => $this->update(dir: 'temple/logo/', oldImage: $temple['logo'], format: 'webp', image: $request['logo'], fileType: 'image')
            ];
        }

        if ($request->file('image')) {
            $dataArray += [
                'thumbnail' => $this->update(dir: 'temple/thumbnail/', oldImage: $temple['thumbnail'], format: 'webp', image: $request['image'], fileType: 'image')
            ];
        }

       

        if($updateBy=='seller' && $temple->request_status == 2){
            $dataArray += [
                'request_status' => 0
            ];
        }

        if($updateBy=='admin' && $temple->added_by == 'seller' && $temple->request_status == 2){
            $dataArray += [
                'request_status' => 1
            ];
        }

        return $dataArray;
    }
    public function getUpdateTemplePackageData(object $request, string $updateBy): array
    {
        return [
            'name'        => $request['name'][array_search('en', $request['lang'])],
            'description' => $request['short_description'][array_search('en', $request['lang'])],
            'type'        => $request['type'][array_search('en', $request['lang'])],
        ];
    }
    

    public function getProcessedUpdateImages(object $request, object $temple): array
    {
        $templeImages = json_decode($temple->images);
        $colorImageArray = [];
        if ($request->has('images_active') && $request->has('images') && count($request->images) > 0) {
            $dbColorImage = $temple->images ? json_decode($temple->images, true) : [];
            if (!$dbColorImage) {
                foreach ($templeImages as $image) {
                    $dbColorImage[] = [
                        'image_name' => $image,
                    ];
                }
            }
        }

        if ($request->file('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = $this->upload(dir: 'temple/', format: 'webp', image: $image);
                $templeImages[] = $imageName;
                if ($request->has('images_active') && $request->has('images') && count($request->images) > 0) {
                    $colorImageArray[] = [
                        'image_name' => $imageName,
                    ];
                }
            }
        }

        return [
            'image_names' => $templeImages ?? []
        ];
    }

    public function locationRemove($name):bool|array{
        return $this->delete(filePath: '/temple/review/' . $name);
    }

}
?>