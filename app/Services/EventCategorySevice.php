<?php

namespace App\Services;

use Illuminate\Support\Str;

use App\Traits\FileManagerTrait;

class EventCategorySevice
{
    use FileManagerTrait;



    public function getAddData(object $request): array
    {
        $imageNames = '';
        if ($request->file('image')) {
                $images = $this->upload(dir: 'event/category/', format: 'webp', image: $request->file('image'));
                $imageNames = $images;
            }
        return [
            'image' => $imageNames ?? '',
            'category_name'=>$request['category_name'][array_search('en', $request['lang'])],
        ];
    }

    public function getUpdateData(object $request,$old_data):array{

        $imageNames = $old_data['image'];
        if ($request->file('image')) {
                $images = $this->upload(dir: 'event/category/', format: 'webp', image: $request->file('image'));
                $imageNames = $images;
                if(!empty($images) && !empty($old_data['image'])){
                    $this->delete('event/category/'.$old_data['image']);
                }
            }
        return [
            'image' => $imageNames ?? '',
            'category_name'=>$request['category_name'][array_search('en', $request['lang'])],
        ];
    }

    public function deleteImage(object $old_data):bool|array{
        return $this->delete('event/category/'.$old_data['image']);
    }

}
?>