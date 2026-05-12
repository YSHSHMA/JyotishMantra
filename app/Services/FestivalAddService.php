<?php

namespace App\Services;

use App\Traits\FileManagerTrait;

class FestivalAddService
{
    use FileManagerTrait;

    public function getAddData(object $request): array
    {
        return [
            'festival_id' => $request['festival_id'],
            'festival_date' => $request['festival_date'],
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
            'festival_id' => $request['festival_id'],
            'festival_date' => $request['festival_date'],
            'title' => $request['title'][array_search('en', $request['lang'])],
            'tithi' => $request['tithi'][array_search('en', $request['lang'])],
            'detail' => $request['detail'][array_search('en', $request['lang'])],
            'image' => $image,
        ];
    }

    public function deleteImage(object $data): bool
    {
        if ($data['festival_image']) {$this->delete('festival-img/'.$data['festival_image']);};
        return true;
    }

}
