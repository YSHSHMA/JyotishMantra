<?php

namespace App\Services;

use Illuminate\Support\Str;
use App\Traits\FileManagerTrait;

class RestaurantService
{

    use FileManagerTrait;

    public function getAddData(object $request): array
    {
        return [
            'restaurant_name' => $request['restaurant_name'][array_search('en', $request['lang'])],
            'latitude' => $request['latitude'],
            'longitude' => $request['longitude'],
            'country_id' => $request['country_id'],
            'state_id' => $request['state_id'],
            'cities_id' => $request['cities_id'],
            'zipcode' => $request['zipcode'],
            'phone_no' => $request['phone_no'],
            'email_id' => $request['email_id'],
            'website_link' => $request['website_link'],
            'open_time' => $request['open_time'],
            'close_time' => $request['close_time'],
            'description' => $request['description'][array_search('en', $request['lang'])],
            'menu_highlights' => $request['menu_highlights'][array_search('en', $request['lang'])],
            'more_details' => $request['more_details'][array_search('en', $request['lang'])],
            'youtube_video'=>$request['youtube_video'],
            'image'=> (($request->file('image')) ? $this->upload(dir: 'temple/restaurant/', format: 'webp', image: $request->file('image')) : ""),

        ];
    }


    public function updateData(object $request,$old_data): array
    {
        $retrun_array = [
            'restaurant_name' => $request['restaurant_name'][array_search('en', $request['lang'])],
            'latitude' => $request['latitude'],
            'longitude' => $request['longitude'],
            'country_id' => $request['country_id'],
            'state_id' => $request['state_id'],
            'cities_id' => $request['cities_id'],
            'zipcode' => $request['zipcode'],
            'phone_no' => $request['phone_no'],
            'email_id' => $request['email_id'],
            'website_link' => $request['website_link'],
            'open_time' => $request['open_time'],
            'close_time' => $request['close_time'],
            'description' => $request['description'][array_search('en', $request['lang'])],
            'menu_highlights' => $request['menu_highlights'][array_search('en', $request['lang'])],
            'more_details' => $request['more_details'][array_search('en', $request['lang'])],
            'youtube_video'=>$request['youtube_video'],
        ];

        if ($request->file('image')) {
            $retrun_array['image'] = $this->upload(dir: 'temple/restaurant/', format: 'webp', image: $request->file('image'));
            $this->delete(filePath: '/temple/restaurant/' . $old_data['image']);
        }

        return $retrun_array;
    }

    public function removeImage($old_data){
        $this->delete(filePath: '/temple/restaurant/' . $old_data['image']);
        return true;
    }

    public function imageAdd($request,$old_data)
    {
        $imageNames = [];
        if ($request->file('images')) {
                foreach ($request->file('images') as $image) {
                    $images = $this->upload(dir: 'temple/restaurant/', format: 'webp', image: $image);
                    $imageNames[] =  $images;
                    // if ($request->has('images_active') && $request->has('images') && count($request['images']) > 0) {
                    // $imageNames[] = [
                    //     'image_name' => $images,
                    // ];
                    // }
                }
             
    }

    if(!empty($old_data['images']) && json_decode($old_data['images'])){
        $imageNames = array_merge(json_decode($old_data['images']),$imageNames);
      }

        return [
            'images' => $imageNames ?? []
        ];
    }

    public function deleteImage($name, $old_data)
    {
        $this->delete(filePath: '/temple/restaurant/' . $name);
        $dataImage = json_decode($old_data['images'], true);
        foreach ($dataImage as $index => $image) {
            if ($image == $name) {
                unset($dataImage[$index]);
                break;
            }
        }
        $dataImage = array_values($dataImage);

       return ['images' => json_encode($dataImage)];
    }

    public function locationRemove($name):bool|array{
        return $this->delete(filePath: '/temple/restaurant/review/' . $name);
    }
}
