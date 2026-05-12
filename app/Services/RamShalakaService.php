<?php

namespace App\Services;
use Illuminate\Support\Str;

use App\Traits\FileManagerTrait;

class RamShalakaService
{
    use FileManagerTrait;

    public function getAddData(object $request): array
    {
        return [
            'description' => $request['description'][array_search('en', $request['lang'])],
            'letter' => $request->letter,
            'chaupai' => $request->chaupai,
            'status' => 1,
        ];
    }

    public function getUpdateData(object $request, object $data): array
    {
       
        return [
            'description' => $request->description[array_search('en', $request['lang'])],
            'letter' => $request->letter,
            'chaupai' => $request->chaupai,
            
        ];
    }



}
