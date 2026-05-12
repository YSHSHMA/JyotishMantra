<?php

namespace App\Services;

use Illuminate\Support\Str;

use App\Traits\FileManagerTrait;

class BhagwanService
{
    use FileManagerTrait;


    public function getProcessedImages(object $request): array
    {
        $imageNames = [];
        if ($request->file('images')) {
            foreach ($request->file('images') as $image) {
                $images = $this->upload(dir: 'bhagwan/', format: 'webp', image: $image);
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

    public function getProcessedUpdateImages(object $request, object $service): array
    {
        $serviceImages = json_decode($service->images);
        $colorImageArray = [];
        if ($request->has('images_active') && $request->has('images') && count($request->images) > 0) {
            $dbColorImage = $service->images ? json_decode($service->images, true) : [];
            if (!$dbColorImage) {
                foreach ($serviceImages as $image) {
                    $dbColorImage[] = [
                        'image_name' => $image,
                    ];
                }
            }
        }

        if ($request->file('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = $this->upload(dir: 'bhagwan/', format: 'webp', image: $image);
                $serviceImages[] = $imageName;
                if ($request->has('images_active') && $request->has('images') && count($request->images) > 0) {
                    $colorImageArray[] = [
                        'image_name' => $imageName,
                    ];
                }
            }
        }

        return [
            'image_names' => $serviceImages ?? []
        ];
    }

    public function getProcessedWallpapers(object $request): array
    {
        $wallpaperNames = [];
        if ($request->file('wallpapers')) {
            foreach ($request->file('wallpapers') as $wallpaper) {
                $wp = $this->upload(dir: 'bhagwan/wallpaper/', format: 'webp', image: $wallpaper);
                $wallpaperNames[] = $wp;
            }
        }
        return [
            'wallpaper_names' => $wallpaperNames ?? []
        ];
    }

    public function getProcessedUpdateWallpapers(object $request, object $service): array
    {
        $serviceWallpapers = json_decode($service->wallpapers) ?? [];

        if ($request->file('wallpapers')) {
            foreach ($request->file('wallpapers') as $wallpaper) {
                $wpName = $this->upload(dir: 'bhagwan/wallpaper/', format: 'webp', image: $wallpaper);
                $serviceWallpapers[] = $wpName;
            }
        }

        return [
            'wallpaper_names' => $serviceWallpapers ?? []
        ];
    }

    public function getAddData(object $request): array
    {
        $processedImages = $this->getProcessedImages(request: $request);
        $processedWallpapers = $this->getProcessedWallpapers(request: $request);

        return [
            'week' => $request->input('week'),
            'name' => $request['name'][array_search('en', $request['lang'])],
            'images' => json_encode($processedImages['image_names']),
            'wallpapers'=> json_encode($processedWallpapers['wallpaper_names']),
            'thumbnail' => $this->upload(dir: 'bhagwan/thumbnail/', format: 'png', image: $request['image']),
            'status' => 1,
        ];
    }

    public function getUpdateData(object $request, object $data): array
    {
        $processedImages = $this->getProcessedUpdateImages(request: $request, service: $data);
        $processedWallpapers = $this->getProcessedUpdateWallpapers(request: $request, service: $data);

        $dataArray = [
            'week' => $request->input('week'),
            'name' => $request->name[array_search('en', $request['lang'])],
            'images' => json_encode($processedImages['image_names']),
            'wallpapers' => json_encode($processedWallpapers['wallpaper_names']),
        ];

        if ($request->hasFile('image')) {
            $dataArray['thumbnail'] = $this->update(
                dir: 'bhagwan/thumbnail/',
                oldImage: $data['thumbnail'],
                format: 'webp',
                image: $request->file('image'),
                fileType: 'image'
            );
        }

        return $dataArray;
    }


    public function deleteImages(object $service): bool
    {
        if (!is_null($service['images'])) {
            $images = json_decode($service['images']);
            if (!is_null($images)) {
                foreach ($images as $image) {
                    $this->delete(filePath: '/bhagwan/' . $image);
                }
            }
        }

        if (!is_null($service['wallpapers'])) {
            $wallpapers = json_decode($service['wallpapers']);
            if (is_array($wallpapers)) {
                foreach ($wallpapers as $wp) {
                    $this->delete(filePath: '/bhagwan/wallpaper/' . $wp);
                }
            }
        }

        $this->delete(filePath: '/bhagwan/thumbnail/' . $service['thumbnail']);
        return true;
    }


    public function deleteImage(object $request, object $service): array
    {
        $result = [
            'images' => json_decode($service['images'] ?? '[]', true),
            'wallpapers' => json_decode($service['wallpapers'] ?? '[]', true),
        ];

        if (in_array($request['name'], $result['images'])) {
            $updated = [];
            foreach ($result['images'] as $img) {
                if ($img != $request['name']) {
                    $updated[] = $img;
                } else {
                    $this->delete(filePath: 'bhagwan/' . $img);
                }
            }
            $result['images'] = $updated;
        }

        if (in_array($request['name'], $result['wallpapers'])) {
            $updated = [];
            foreach ($result['wallpapers'] as $wp) {
                if ($wp != $request['name']) {
                    $updated[] = $wp;
                } else {
                    $this->delete(filePath: 'bhagwan/wallpaper/' . $wp);
                }
            }
            $result['wallpapers'] = $updated;
        }

        return $result;
    }

    public function deleteAllImage(object $data): bool
    {
        if (!empty($data['images'])) {
            $images = json_decode($data['images'], true);
            if (is_array($images)) {
                foreach ($images as $image) {
                    $this->delete('bhagwan/' . $image);
                }
            } else {
                $this->delete('bhagwan/' . $data['images']);
            }
        }

        if (!empty($data['wallpapers'])) {
            $wallpapers = json_decode($data['wallpapers'], true);
            if (is_array($wallpapers)) {
                foreach ($wallpapers as $wp) {
                    $this->delete('bhagwan/wallpaper/' . $wp);
                }
            }
        }

        if (!empty($data['thumbnail'])) {
            $this->delete('bhagwan/thumbnail/' . $data['thumbnail']);
        }

        if (!empty($data['event_image'])) {
            $this->delete('bhagwan/event-img/' . $data['event_image']);
        }

        return true;
    }
}