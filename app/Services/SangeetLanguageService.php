<?php

namespace App\Services;
use Illuminate\Support\Str;

use App\Traits\FileManagerTrait;

class SangeetLanguageService
{
use FileManagerTrait;

   
    public function getAddData(object $request): array
    {
        return [
            'name' => $request['name'][array_search('en', $request['lang'])],
        ];
    }

    public function getUpdateData(object $request, object $data): array
    {

        return [
            'name' => $request['name'][array_search('en', $request['lang'])],
        ];
    }
    
 // public function deleteImage(object $data): bool
 //    {
 //        if ($data['image']) {$this->delete('sangeet-category-img/'.$data['image']);};
 //        return true;
 //    }


}

