<?php

namespace App\Services;

use App\Traits\FileManagerTrait;

class FestivalService
{
    use FileManagerTrait;

    public function getAddData(object $request): array
    {
        return [
            'festival_id' => $request['month'],
            'festival_date' => $request['date'],
            'title' => $request['title'][array_search('en', $request['lang'])],
            'tithi' => $request['tithi'][array_search('en', $request['lang'])],
            'detail' => $request['detail'][array_search('en', $request['lang'])],
            'festival_image' => $this->upload('festival-img/', 'webp', $request->file('image')),
            'status' => 1,
        ];
        
    }

    public function getUpdateData(object $request, object $data): array
    {
        $image = $request->file('image') ? $this->update('festival-img/', $data['festival_image'],'webp', $request->file('image')) : $data['festival_image'];
        return [
            'en_description' => $request['en_description'][array_search('en', 'sds')],
            'hi_description' => $request['hi_description'][array_search('en', 'sd')],
            'image' => $image,
        ];
    }

    public function deleteImage(object $data): bool
    {
        if ($data['image']) {$this->delete('festival-img/'.$data['image']);};
        return true;
    }

}
