<?php

namespace App\Services;

use Illuminate\Support\Str;

use App\Traits\FileManagerTrait;

class DonateCategoryService
{
    use FileManagerTrait;


    public function getSlug(object $request): string
    {
        return Str::slug($request['name'][array_search('en', $request['lang'])], '-');
    }
  


    public function getAddData(object $request): array
    {
        $imageNames = '';
        if ($request->file('image')) {
                $imageNames = $this->upload(dir: 'donate/category/', format: 'webp', image: $request->file('image'));
            }
        return [
            'image' => $imageNames,
            'type'=>"category",
            'name'=>$request['name'][array_search('en', $request['lang'])],
            'slug' => $this->getSlug($request),
        ];
    }

    public function getUpdateData(object $request,$old_data):array{

        $imageNames = $old_data['image'];
        if ($request->file('image')) {
                $imageNames = $this->upload(dir: 'donate/category/', format: 'webp', image: $request->file('image'));
                $this->deleteImage($old_data['image']);
            }
        return [
            'image' => $imageNames ?? '',
            'name'=>$request['name'][array_search('en', $request['lang'])],
            'slug' => $this->getSlug($request),
        ];
    }

    public function deleteImage(string $name):bool|array{
        return $this->delete('donate/category/'.$name);
    }


    //purpose 
    public function getPurposeAddData(object $request):array{
        $imageNames = '';
        if ($request->file('image')) {
                $imageNames = $this->upload(dir: 'donate/purpose/', format: 'webp', image: $request->file('image'));
            }
        return [
            'image' => $imageNames,
            'type'=>"porpose",
            'name'=>$request['name'][array_search('en', $request['lang'])],
        ];
    }

    public function deletePurposeImage(string $name):bool|array{
        return $this->delete('donate/purpose/'.$name);
    }

    public function getPurposeUpdateData(object $request,$old_data):array{
        $imageNames = $old_data['image'];
        if ($request->file('image')) {
                $imageNames = $this->upload(dir: 'donate/purpose/', format: 'webp', image: $request->file('image'));
                $this->deletePurposeImage($old_data['image']);
            }
        return [
            'image' => $imageNames ?? '',
            'name'=>$request['name'][array_search('en', $request['lang'])],
        ];
    }


}
?>