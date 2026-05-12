<?php

namespace App\Services;

use App\Traits\FileManagerTrait;

class MasikRashiService
{
    use FileManagerTrait;

    public function getAddData(object $request): array
    {
        return [
            'name' => $request['name'][array_search('en', $request['lang'])],
            'month' => $request['month'][array_search('en', $request['lang'])],
            'lang' => $request['lang'][array_search('en', $request['lang'])],
            'akshar' => $request['akshar'][array_search('en', $request['lang'])],
            'detail' => $request['detail'][array_search('en', $request['lang'])],
            'status' => 1,
        ];
    }

    public function getUpdateData(object $request, object $data): array
    {
        $image = $request->file('image') ? $this->update('rashi/', $data['image'],'webp', $request->file('image')) : $data['image'];
        return [
            'name' => $request['name'],
            'month' => $request['month'],
            'lang' => $request['lang'],
            'akshar' => $request['akshar'],
            'detail' => $request['detail'],
        ];
    }

    public function deleteImage(object $data): bool
    {
        if ($data['image']) {$this->delete('profile/'.$data['image']);};
        return true;
    }

}
