<?php

namespace App\Services;
use Illuminate\Support\Str;

use App\Traits\FileManagerTrait;

class SangeetService
{
    use FileManagerTrait;

    public function getAddData(object $request): array
    {
        return [
            'language' => $request['language'],
            'category_id' => $request['category_id'],
            'subcategory_id' => $request['subcategory_id'],
        ];
    }


    public function getUpdateData(object $request, object $data): array
    {
        return [
            'language' => $request['language'],
            'category_id' => $request['category_id'],
            'subcategory_id' => $request['subcategory_id'],
        ];
    }


 public function deleteImage(object $data): bool
    {
        if ($data['image']) {$this->delete('sangeet-img/'.$data['image']);};
        return true;
    }

}
