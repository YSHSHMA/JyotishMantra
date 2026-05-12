<?php

namespace App\Services;

use App\Traits\FileManagerTrait;

class FaqAddService
{
    use FileManagerTrait;

    public function getAddData(object $request): array
    {
        return [
            'question' => $request['question'][array_search('en', $request['lang'])],
            'detail' => $request['detail'][array_search('en', $request['lang'])],
            "category_id"=>$request['category_id'],
        ];
    }

    public function getUpdateData(object $request, object $data): array
    {
       
        return [
            'question' => $request['question'][array_search('en', $request['lang'])],
            'detail' => $request['detail'][array_search('en', $request['lang'])],
            "category_id"=>$request['category_id'],
        ];
    }

   

}