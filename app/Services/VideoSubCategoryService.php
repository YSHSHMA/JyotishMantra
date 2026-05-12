<?php

namespace App\Services;
use Illuminate\Support\Str;

use App\Traits\FileManagerTrait;

class VideoSubCategoryService
{
use FileManagerTrait;

   
    public function getAddData(object $request): array
    {
        return [
            'name' => $request['name'][array_search('en', $request['lang'])],
            'image' => $this->upload('video-subcategory-img/', 'webp', $request->file('image')),
            'category_id' => $request['category_id'],

        ];
    }

    public function getUpdateData(object $request, object $data): array
    {
        $image = $request->file('image') ? $this->update('video-subcategory-img/', $data['image'], 'webp', $request->file('image')) : $data['image'];

        return [
            'name' => $request['name'][array_search('en', $request['lang'])],
            'image' => $image,
            'category_id' => $request['category_id'],

        ];
    }
    
 public function deleteImage(object $data): bool
    {
        if ($data['image']) {$this->delete('video-subcategory-img/'.$data['image']);};
        return true;
    }


}

