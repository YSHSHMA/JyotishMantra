<?php

namespace App\Services;

use App\Traits\FileManagerTrait;

class CitiesAddService
{
    use FileManagerTrait;

    public function getAddData(object $request): array
    {
        $images = [];
        if ($request->file('images')) {
            foreach ($request->file('images') as $image) {
                $images[] = $this->upload(dir: 'cities/citie_image/', format: 'webp', image: $image);
            }
        }
        $image = '';
        if ($request->file('image')) {
            $image = $this->upload(dir: 'cities/citie_image/', format: 'webp', image: $request->file('image'));
        }

        return [
            'city' => $request['city'][array_search('en', $request['lang'])],
            'country_id' => $request['country_id'],
            'state_id' => $request['state_id'],
            'short_desc' => $request['short_desc'][array_search('en', $request['lang'])],
            'description' => $request['description'][array_search('en', $request['lang'])],
            'famous_for' => $request['famous_for'][array_search('en', $request['lang'])],
            'latitude' => $request['latitude'],
            'longitude' => $request['longitude'],
            'festivals_and_events' => $request['festivals_and_events'][array_search('en', $request['lang'])],
            'image' => $image,
            'slider_image' => json_encode($images),
        ];
    }

    public function getUpdateData(object $request, $old_data): array
    {

        $datarray = [
            'city' => $request['city'][array_search('en', $request['lang'])],
            'country_id' => $request['country_id'],
            'state_id' => $request['state_id'],
            'short_desc' => $request['short_desc'][array_search('en', $request['lang'])],
            'description' => $request['description'][array_search('en', $request['lang'])],
            'famous_for' => $request['famous_for'][array_search('en', $request['lang'])],
            'latitude' => $request['latitude'],
            'longitude' => $request['longitude'],
            'festivals_and_events' => $request['festivals_and_events'][array_search('en', $request['lang'])],
        ];

        $images = json_decode($old_data['images'], true) ?? [];
        if ($request->file('images')) {
            foreach ($request->file('images') as $image) {
                $images[] = $this->upload(dir: 'cities/citie_image/', format: 'webp', image: $image);
            }
            $datarray['slider_image'] = json_encode($images);
        }
        if ($request->file('image')) {
            $datarray['image'] = $this->upload(dir: 'cities/citie_image/', format: 'webp', image: $request->file('image'));
            $this->delete(filePath: '/cities/citie_image/' . $old_data['image']);
        }

        return $datarray;
    }


    public function delete_SliderImages($old_data, $name)
    {
        $this->delete(filePath: 'cities/citie_image/' . $name);
        $dataImage = json_decode($old_data['slider_image'], true);
        $index = array_search($name, $dataImage);
        if (!empty($dataImage)) {
            foreach ($dataImage as $index => $image) {
                if ($image == $name) {
                    unset($dataImage[$index]);
                    break;
                }
            }
            $dataImage = array_values($dataImage);
        }
        return ['slider_image' => json_encode($dataImage)];
    }

    public function addgalleryImages($request, $old_data)
    {
        $processedImages = $this->getProcessedImages(request: $request);
        if (!empty($old_data['images']) && json_decode($old_data['images'])[0]) {
            $processedImages['image_names'] = array_merge(json_decode($old_data['images']), $processedImages['image_names']);
        }
        return [
            'images' => json_encode($processedImages['image_names']),
        ];
    }

    public function locationRemove($name): bool|array
    {
        return $this->delete(filePath: 'cities/review/' . $name);
    }


    public function deletes($old_data, $name)
    {
        $this->delete(filePath: 'cities/' . $name);
        $dataImage = json_decode($old_data['images'], true);
        $index = array_search($name, $dataImage);
        if (!empty($dataImage)) {
            foreach ($dataImage as $index => $image) {
                if ($image == $name) {
                    unset($dataImage[$index]);
                    break;
                }
            }
            $dataImage = array_values($dataImage);
        }
        return ['images' => json_encode($dataImage)];
    }

    public function getProcessedImages(object $request): array
    {
        $imageNames = [];
        if ($request->file('images')) {
            foreach ($request->file('images') as $image) {
                $images = $this->upload(dir: 'cities/', format: 'webp', image: $image);
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
}
