<?php

namespace App\Services;
use Illuminate\Support\Str;

use App\Traits\FileManagerTrait;

class RashiService
{
    use FileManagerTrait;

    public function getSlug(object $request): string
    {
        return Str::slug($request['name'][array_search('en', $request['lang'])]);
    }

    public function getAddData(object $request): array
    {
        return [
            'name' => $request['name'][array_search('en', $request['lang'])],
            'slug' => $this->getSlug($request),
            'image' => $this->upload('rashi/', 'webp', $request->file('image')),
            'status' => 1,
        ];
    }

    public function getUpdateData(object $request, object $data): array
    {
        $image = $request->file('image') ? $this->update('rashi/', $data['image'],'webp', $request->file('image')) : $data['image'];
        return [
            'name' => $request->name[array_search('en', $request['lang'])],
            'image' => $image,
        ];
    }

    public function deleteImage(object $data): bool
    {
        if ($data['image']) {$this->delete('profile/'.$data['image']);};
        return true;
    }

}
