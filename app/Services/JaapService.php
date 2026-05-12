<?php

namespace App\Services;
use Illuminate\Support\Str;

use App\Traits\FileManagerTrait;

class JaapService
{
    use FileManagerTrait;

    public function getAddData(object $request): array
    {
        return [
            'name' => $request['name'][array_search('en', $request['lang'])],
            'mantra' => $request['mantra'][array_search('en', $request['lang'])],
            'image' => $this->upload('jaap/', 'webp', $request->file('image')),
            'status' => 1,
        ];
    }

    public function getUpdateData(object $request, object $data): array
    {
        $image = $request->file('image') ? $this->update('jaap/', $data['image'],'webp', $request->file('image')) : $data['image'];
        return [
            'name' => $request->name[array_search('en', $request['lang'])],
            'mantra' => $request->mantra[array_search('en', $request['lang'])],
            'image' => $image,
        ];
    }

    public function deleteImage(object $data): bool
    {
        if ($data['image']) {$this->delete('jaap/'.$data['image']);};
        return true;
    }

}
