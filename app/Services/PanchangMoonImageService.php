<?php

namespace App\Services;
use Illuminate\Support\Str;

use App\Traits\FileManagerTrait;

class PanchangMoonImageService
{
use FileManagerTrait;

   
    public function getAddData(object $request): array
    {
        return [
            'name' => $request['name'][array_search('en', $request['lang'])],
            'image' => $this->upload('panchang-moon-img/', 'webp', $request->file('image')),

        ];
    }

    public function getUpdateData(object $request, object $data): array
    {
        $image = $request->file('image') ? $this->update('panchang-moon-img/', $data['image'], 'webp', $request->file('image')) : $data['image'];

        return [
            'name' => $request['name'][array_search('en', $request['lang'])],
            'image' => $image,
        ];
    }
    
 public function deleteImage(object $data): bool
    {
        if ($data['image']) {$this->delete('panchang-moon-img/'.$data['image']);};
        return true;
    }


}

