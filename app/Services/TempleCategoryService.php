<?php

namespace App\Services;
use Illuminate\Support\Str;
use App\Traits\FileManagerTrait;
class TempleCategoryService
{

    
    use FileManagerTrait;


    
    public function getAddData(object $request): array
    {   
       
        return [
            'name' => $request['name'][array_search('en', $request['lang'])],
            'short_description' => $request['short_description'][array_search('en', $request['lang'])],
            'image' => $this->upload(dir: 'temple/category/', format: 'png', image: $request['image']),  
        ];
    }

    public function getUpdateData(object $request,object $old_image): array
    {   
       
        if($request['image']){
            $this->delete(filePath: '/temple/category/' . $old_image['image']);
          $old_image_new =  $this->upload(dir: 'temple/category/', format: 'png', image: $request['image']);
        }else{
           $old_image_new = $old_image['image'];
        }
        return [
            'name' => $request['name'][array_search('en', $request['lang'])],
            'short_description' => $request['short_description'][array_search('en', $request['lang'])],
            'image' => $old_image_new,  
        ];
    }

    public function removeImage(object $old_image){
        return $this->delete(filePath: '/temple/category/' . $old_image['image']);
    }
    // public function deleteImages(object $temple): bool
    // {
    //     if (!is_null($temple['images'])) {
    //         $images = json_decode($temple['images']);
    //         if (!is_null($images)) {
    //             foreach ($images as $image) {
    //                 $this->delete(filePath: '/temple/category/' . $image);
    //             }
    //         }
    //     }
    //     $this->delete(filePath: '/temple/category/' . $temple['category']);
    //     return true;
    // }
}