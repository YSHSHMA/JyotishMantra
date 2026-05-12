<?php

namespace App\Services;

use App\Http\Requests\Request;
use Illuminate\Support\Str;
use App\Traits\FileManagerTrait;

class TourPackageService
{


    use FileManagerTrait;
    //vehicle
    public function getAddVehicleData(object $request)
    {
        $dataArray = [];
        if ($request && $request['brand_name'][array_search('en', $request['lang'])] && count($request['brand_name'][array_search('en', $request['lang'])]) > 0) {
            for ($i = 0; $i < count($request['brand_name'][array_search('en', $request['lang'])]); $i++) {
                $dataArray[$i]['type'] = $request['type'][array_search('en', $request['lang'])];
                $dataArray[$i]['brand_name'] = $request['brand_name'][array_search('en', $request['lang'])][$i];
            }
        }
        return $dataArray;
    }

    public function getUpdateVehicleData(object $request)
    {
        return [
            'type' => $request['type'][array_search('en', $request['lang'])],
            'brand_name' => $request['brand_name'][array_search('en', $request['lang'])],
        ];
    }

    //cab
    public function getAddTourData(object $request): array
    {
        return [
            'name' => $request['name'][array_search('en', $request['lang'])],
            'title' => $request['title'][array_search('en', $request['lang'])],
            'type' => $request['type'],
            'hotel_type' => $request['hotel_type'],
            'seats' => $request['seats'],
            'description' => $request['description'][array_search('en', $request['lang'])],
            'image' => $this->upload(dir: 'tour_and_travels/package/', format: 'png', image: $request['image']),
        ];
    }

    public function getAddCabData(object $request)
    {
        return [
            "vehicle_category" => $request['vehicle_category'],
            'slug' =>  Str::slug(($request['name'][array_search('en', $request['lang'])]), '-') . '-' . Str::random(6),
            'name' => $request['name'][array_search('en', $request['lang'])],
            'seats' => $request['seats'],
            'description' => $request['description'][array_search('en', $request['lang'])],
            'image' => $this->upload(dir: 'tour_and_travels/cab/', format: 'png', image: $request['image']),
        ];
    }


    public function getUpdateTourData(object $request,$old_data): array
    {
        $dataArray = [
            // "vehicle_category" => $request['vehicle_category'],
            'name' => $request['name'][array_search('en', $request['lang'])],
            'title' => $request['title'][array_search('en', $request['lang'])],
            'type' => $request['type'],
            'hotel_type' => $request['hotel_type'],
            'seats' => $request['seats'],
            'description' => $request['description'][array_search('en', $request['lang'])],
        ];
        // if (empty($old_data['slug'])) {
        //     $dataArray['slug'] = Str::slug($request['name'][array_search('en', $request['lang'])], '-') . '-' . Str::random(6);
        // }
        if ($request->file('image')) {
            $dataArray['image'] = $this->upload(dir: 'tour_and_travels/package/', format: 'png', image: $request['image']);
        }

        return $dataArray;
    }

    public function getUpdateCabData(object $request): array
    {
        $dataArray = [
            "vehicle_category" => $request['vehicle_category'],
            'name' => $request['name'][array_search('en', $request['lang'])],
            'seats' => $request['seats'],
            'description' => $request['description'][array_search('en', $request['lang'])],
        ];

        if ($request->file('image')) {
            $dataArray['image'] = $this->upload(dir: 'tour_and_travels/cab/', format: 'png', image: $request['image']);
        }

        return $dataArray;
    }

    public function deleteImage($old_data)
    {
        return $this->delete(filePath: '/tour_and_travels/package/' . $old_data['image']);
    }

    public function CapImageRemove($old_data)
    {
        return $this->delete(filePath: '/tour_and_travels/cab/' . $old_data['image']);
    }
}
