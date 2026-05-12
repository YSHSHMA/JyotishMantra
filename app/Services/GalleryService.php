<?php

namespace App\Services;

use Illuminate\Support\Str;

use App\Traits\FileManagerTrait;

class GalleryService
{
    use FileManagerTrait;


    public function getAddData(object $request): array
    {
        $processedImages = $this->getProcessedImages(request: $request);
        return [
            'temple_id' => $request['temple_id'],
            'title' => $request['title'][array_search('en', $request['lang'])],
            'slug' => $this->getSlug($request),
            'images' => json_encode($processedImages['image_names']),
        ];
    }

    public function updateData(object $request,string $old_img): array{


        $processedImages = $this->getProcessedImages(request: $request);
       $array = array_merge(json_decode($old_img),$processedImages['image_names']);
        return [
            'temple_id' => $request['temple_id'],
            'title' => $request['title'][array_search('en', $request['lang'])],
            'slug' => $this->getSlug($request),
            'images' => json_encode($array),
        ];
    }


    public function getProcessedImages(object $request): array
    {
        $imageNames = [];
        if ($request->file('images')) {
            foreach ($request->file('images') as $image) {
                $images = $this->upload(dir: 'temple/gallery/', format: 'webp', image: $image);
                $imageNames[] = $images;
                if ($request->has('images_active') && $request->has('images') && count($request['images']) > 0) {
                    $imageNames[] = [
                        'image_name' => $images,
                    ];
                }
            }
        }
        return [
            'image_names' => $imageNames ?? []
        ];
    }
    public function getSlug(object $request): string
    {
        return Str::slug($request['title'][array_search('en', $request['lang'])]);
    }

    public function deleteImages(object $temple): bool
    {
        if (!is_null($temple['images'])) {
            $images = json_decode($temple['images']);
            if (!is_null($images)) {
                foreach ($images as $image) {
                    $this->delete(filePath: '/temple/gallery/' . $image);
                }
            }
        }
        $this->delete(filePath: '/temple/gallery/' . $temple['thumbnail']);
        return true;
    }

    public function deleteImage(object $request, object $temple): array
    {
        $images = [];
        foreach (json_decode($temple['images']) as $image) {
            if ($image != $request['name']) {
                $images[] = $image;
            }
        }
        return [
            'images' => $images
        ];
    }

    public function image_remove($data,$name){
        $images = [];
        $removeImage = '';
        foreach (json_decode($data['images']) as $image) {
            if ($image != $name) {
                $images[] = $image;
            }else{
                $removeImage = $image;
            }
        }
    $this->delete(filePath: '/temple/gallery/' . $removeImage);
        return [
            'images' => $images,
        ];
    }
}
