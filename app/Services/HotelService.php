<?php

namespace App\Services;

use Illuminate\Support\Str;

use App\Traits\FileManagerTrait;

class HotelService
{
    use FileManagerTrait;


    public function getAddData(object $request): array
    {
        return [
            'hotel_name' => $request['hotel_name'][array_search('en', $request['lang'])],
            'country_id' => $request['country_id'],
            'state_id' => $request['state_id'],
            'cities_id' => $request['cities_id'],
            'zipcode' => $request['zipcode'],
            'phone_no' => $request['phone_no'],
            'email_id' => $request['email_id'],
            'website_link' => $request['website_link'],
            'description' => $request['description'][array_search('en', $request['lang'])],
            'amenities' => $request['amenities'][array_search('en', $request['lang'])],
            'room_amenities' => $request['room_amenities'][array_search('en', $request['lang'])],
            'room_types' => $request['room_types'][array_search('en', $request['lang'])],
            'booking_information' => $request['booking_information'][array_search('en', $request['lang'])],
            'youtube_video' => ($request['youtube_video'] ?? ""),
            'image' => (($request->file('image')) ? $this->upload(dir: 'temple/hotel/', format: 'webp', image: $request->file('image')) : ""),
            'latitude' => $request['latitude'],
            'longitude' => $request['longitude'],
        ];
    }

    public function updateData(object $request, $old_data): array
    {
        $retrun_array = [
            'hotel_name' => $request['hotel_name'][array_search('en', $request['lang'])],
            'country_id' => $request['country_id'],
            'state_id' => $request['state_id'],
            'cities_id' => $request['cities_id'],
            'zipcode' => $request['zipcode'],
            'phone_no' => $request['phone_no'],
            'email_id' => $request['email_id'],
            'website_link' => $request['website_link'],
            'description' => $request['description'][array_search('en', $request['lang'])],
            'amenities' => $request['amenities'][array_search('en', $request['lang'])],
            'room_amenities' => $request['room_amenities'][array_search('en', $request['lang'])],
            'room_types' => $request['room_types'][array_search('en', $request['lang'])],
            'booking_information' => $request['booking_information'][array_search('en', $request['lang'])],
            'youtube_video' => ($request['youtube_video'] ?? ""),
            'latitude' => $request['latitude'],
            'longitude' => $request['longitude'],
        ];

        if ($request->file('image')) {
            $retrun_array['image'] = $this->upload(dir: 'temple/hotel/', format: 'webp', image: $request->file('image'));
            $this->delete(filePath: '/temple/hotel/' . $old_data['image']);
        }

        return $retrun_array;
    }

    public function removeImage($old_data)
    {
        $this->delete(filePath: '/temple/hotel/' . $old_data['image']);
        return true;
    }

    public function addgalleryImages($request, $old_data)
    {
        $processedImages = $this->getProcessedImages(request: $request);
        if (!empty($old_data['images']) && json_decode($old_data['images'])) {
            $processedImages['image_names'] = array_merge(json_decode($old_data['images']), $processedImages['image_names']);
        }
        return [
            'images' => json_encode($processedImages['image_names']),
        ];
    }

    public function deletes($old_data, $name)
    {
        $this->delete(filePath: '/temple/hotel/' . $name);
        $dataImage = json_decode($old_data['images'], true);
        $index = array_search($name, $dataImage);
        if ($index !== false) {
            unset($dataImage[$index]);
        }
        $dataImage = array_values($dataImage);
        return ['images' => json_encode($dataImage)];
    }

    public function getProcessedImages(object $request): array
    {
        $imageNames = [];
        if ($request->file('images')) {
            foreach ($request->file('images') as $image) {
                $images = $this->upload(dir: 'temple/hotel/', format: 'webp', image: $image);
                $imageNames[] = $images;
                if ($request->has('images_active') && $request->has('images') && count($request['images']) > 0) {
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

    public function locationRemove($name): bool|array
    {
        return $this->delete(filePath: '/temple/hotel/review/' . $name);
    }
}