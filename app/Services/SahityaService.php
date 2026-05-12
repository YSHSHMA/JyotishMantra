<?php

namespace App\Services;
use Illuminate\Support\Str;

use App\Traits\FileManagerTrait;

class SahityaService
{
    use FileManagerTrait;

    public function getAddData(object $request): array
    {
        return [
            'name' => $request['name'][array_search('en', $request['lang'])],
            'image' => $this->upload('sahitya/', 'webp', $request->file('image')),
            'status' => 1,
        ];
    }

    public function getUpdateData(object $request, object $data): array
    {
        $image = $request->file('image') ? $this->update('sahitya/', $data['image'],'webp', $request->file('image')) : $data['image'];
        return [
            'name' => $request->name[array_search('en', $request['lang'])],
            'image' => $image,
        ];
    }

    public function deleteImage(object $data): bool
    {
        if ($data['image']) {$this->delete('sahitya/'.$data['image']);};
        return true;
    }

}
