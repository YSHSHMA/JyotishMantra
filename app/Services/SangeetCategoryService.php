<?php

namespace App\Services;
use Illuminate\Support\Str;

use App\Traits\FileManagerTrait;

class SangeetCategoryService
{
use FileManagerTrait;

   
    public function getAddData(object $request): array
    {
        return [
            'name' => $request['name'][array_search('en', $request['lang'])],
            'image' => $this->upload('sangeet-category-img/', 'webp', $request->file('image')),
            'banner' => $this->upload('sangeet-category-banner/', 'webp', $request->file('banner')),

        ];
    }

    public function getUpdateData(object $request, object $data): array
    {
        $image = $request->file('image') ? $this->update('sangeet-category-img/', $data['image'], 'webp', $request->file('image')) : $data['image'];
        $banner = $request->file('banner') ? $this->update('sangeet-category-banner/', $data['banner'], 'webp', $request->file('banner')) : $data['banner'];

        return [
            'name' => $request['name'][array_search('en', $request['lang'])],
            'image' => $image,
            'banner' => $banner,
        ];
    }
    
 public function deleteImage(object $data): bool
    {
        if ($data['image']) {
            $this->delete('sangeet-category-img/' . $data['image']);
        }
        
        if ($data['banner']) {
            $this->delete('sangeet-category-banner/' . $data['banner']);
        }
        
        return true;
    }


}

