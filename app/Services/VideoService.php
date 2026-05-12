<?php

namespace App\Services;
use Illuminate\Support\Str;

use App\Traits\FileManagerTrait;

class VideoService
{
    use FileManagerTrait;


public function getAddData(object $request): array
{
    $title = $request->input('title');
    $url = $request->input('url');
     $playlist_name = $request->input('playlist_name');

    $data = [
        'title' => json_encode($title, JSON_UNESCAPED_UNICODE),
        'url' => json_encode($url, JSON_UNESCAPED_UNICODE),  
        'list_type' => $request->input('list_type'),
         'playlist_name' => $playlist_name,
        'category_id' => $request->input('category_id'),
        'subcategory_id' => $request->input('subcategory_id'),

    ];

    $urlStatus = array_fill(0, count($title), 1);
    $data['url_status'] = json_encode($urlStatus, JSON_UNESCAPED_UNICODE);


    $images = [];
    if ($request->hasFile('image')) {
        foreach ($request->file('image') as $file) {
            $images[] = $this->upload('video-img/', 'webp', $file);
        }
    }
    $data['image'] = json_encode($images, JSON_UNESCAPED_UNICODE);
//dd($data);
    return $data;
}


public function getUpdateData(object $request, object $data): array
{
    // Decode the existing images from the data
    $existingImages = json_decode($data['image'], true) ?? [];
    $existingUrls = json_decode($data['url'], true) ?? [];
    $existingUrlStatus = json_decode($data['url_status'], true) ?? [];

    // Check if there are files to update
    $updatedImages = $existingImages;


    $updatedUrls = $request->input('url', $existingUrls);
    $updatedUrlStatus = $request->input('url_status', $existingUrlStatus);

     if (count($updatedUrls) > count($existingUrlStatus)) {
         $updatedUrlStatus = array_pad($updatedUrlStatus, count($updatedUrls), 1); 
     } elseif (count($updatedUrls) < count($existingUrlStatus)) {
         $updatedUrlStatus = array_slice($updatedUrlStatus, 0, count($updatedUrls)); 
     }


    if ($request->hasFile('image')) {
        // Loop through the uploaded images and update them
        foreach ($request->file('image') as $key => $imageFile) {
            if ($imageFile) {
                // Update the image and replace the existing one
                $updatedImages[$key] = $this->update('video-img/', $existingImages[$key] ?? '', 'webp', $imageFile);
            }
        }
    }

    return [
        'title' => json_encode($request->input('title'), JSON_UNESCAPED_UNICODE),
        'url' => json_encode($request->input('url'), JSON_UNESCAPED_UNICODE),
        'list_type' => $request->input('list_type') ?? null,
        'playlist_name' => $request->input('playlist_name') ?? null,
        'category_id' => $request->input('category_id') ?? $data['category_id'],
        'subcategory_id' => $request->input('subcategory_id') ?? $data['subcategory_id'],
        'image' => json_encode($updatedImages, JSON_UNESCAPED_UNICODE),
        'url_status' => json_encode($updatedUrlStatus, JSON_UNESCAPED_UNICODE),
    ];
}

 public function deleteImage(object $data): bool
    {
        if ($data['image']) {$this->delete('video-img/'.$data['image']);};
        return true;
    }   

}