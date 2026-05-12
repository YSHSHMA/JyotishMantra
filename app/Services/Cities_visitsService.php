<?php

namespace App\Services;

use App\Traits\FileManagerTrait;

class Cities_visitsService
{
    use FileManagerTrait;

    public function getAddData(object $request): array
    {
        return [
            'month_name' => $request['month_name'][array_search('en', $request['lang'])],
            'citie_id' => $request['citie_id'],
            'season' => $request['season'][array_search('en', $request['lang'])],
            'crowd' => $request['crowd'][array_search('en', $request['lang'])],
            'weather' => $request['weather'][array_search('en', $request['lang'])],
            'sight' => $request['sight'][array_search('en', $request['lang'])],
            'image' => (($request->file('image')) ? $this->upload(dir: 'cities/visit/', format: 'webp', image: $request->file('image')) : ""),
        ];
    }

    public function getUpdateData(object $request,$old_data): array
    {
           
        $Data = [
            // 'id' => $request['id'][array_search('en', $request['lang'])],
            'month_name' => $request['month_name'][array_search('en', $request['lang'])],
            'season' => $request['season'][array_search('en', $request['lang'])],
            'crowd' => $request['crowd'][array_search('en', $request['lang'])],
            'weather' => $request['weather'][array_search('en', $request['lang'])],
            'sight' => $request['sight'][array_search('en', $request['lang'])],            
        ];

        if($request->file('image')){
            $Data['image'] = $this->upload(dir: 'cities/visit/', format: 'webp', image: $request->file('image'));
            $this->delete(filePath:'cities/visit/'.$old_data['image']);
        }

        return $Data;
    }

    public function deleteImage(object $old_data){
        $this->delete(filePath:'cities/visit/'.$old_data['image']);
        return true;
    }


}


?>