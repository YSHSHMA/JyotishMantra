<?php

namespace App\Services;

use Illuminate\Support\Str;

use App\Traits\FileManagerTrait;

class EventPackageSevice
{
    use FileManagerTrait;



    public function getAddData(object $request): array
    {
        $imageNames = '';
        if ($request->file('image')) {
            $images = $this->upload(dir: 'event/package/', format: 'webp', image: $request->file('image'));
            $imageNames = $images;
        }
        return [
            'image' => $imageNames ?? '',
            'package_name' => $request['package_name'][array_search('en', $request['lang'])],
            'description' => $request['description'][array_search('en', $request['lang'])],
        ];
    }

    public function updateData(object $request,object $old_data): array
    {
        $updateData = [];

        if ($request->file('image')) {
            $this->delete('event/package/' . $old_data['image']);
            $images = $this->upload(dir: 'event/package/', format: 'webp', image: $request->file('image'));
            $updateData['image']  = $images;
        }

        $updateData['package_name'] = $request['package_name'][array_search('en', $request['lang'])];
        $updateData['description'] = $request['description'][array_search('en', $request['lang'])];

        return $updateData;
    }


    public function deleteImage(string $name): bool|array
    {
        return $this->delete('event/package/' . $name);
    }
}
