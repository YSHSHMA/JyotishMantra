<?php

namespace App\Services;
use Illuminate\Support\Str;

use App\Traits\FileManagerTrait;

class VideoListTypeService
{
use FileManagerTrait;

   
    public function getAddData(object $request): array
    {
        return [
            'name' => $request['name'][array_search('en', $request['lang'])],
        ];
    }

    public function getUpdateData(object $request, object $data): array
    {
        $image = $request->file('image') ? $this->update('video-sub-subcategory-img/', $data['image'], 'webp', $request->file('image')) : $data['image'];

        return [
            'name' => $request['name'][array_search('en', $request['lang'])],
        ];
    }
    
 public function deleteImage(object $data): bool
    {
        if ($data['image']) {$this->delete('video-subcategory-img/'.$data['image']);};
        return true;
    }


}

