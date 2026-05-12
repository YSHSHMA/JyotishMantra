<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Enums\ViewPaths\Admin\VIPPath;
use App\Traits\FileManagerTrait;
use Illuminate\Support\Str;
use App\Models\Chavdhava;
use Rap2hpoutre\FastExcel\FastExcel;

class ChadhavaAddService
{
    use FileManagerTrait;

    public function getProcessedImages(object $request): array
    {
        $imageNames = [];
        if ($request->file('images')) {
            foreach ($request->file('images') as $image) {
                $images = $this->upload(dir: 'chadhava/', format: 'webp', image: $image);
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

    public function getProcessedUpdateImages(object $request, object $chadhava): array
    {
        $chadhavaImages = json_decode($chadhava->images);
        $colorImageArray = [];
        if ($request->has('images_active') && $request->has('images') && count($request->images) > 0) {
            $dbColorImage = $chadhava->images ? json_decode($chadhava->images, true) : [];
            if (!$dbColorImage) {
                foreach ($chadhavaImages as $image) {
                    $dbColorImage[] = [
                        'image_name' => $image,
                    ];
                }
            }
        }

        if ($request->file('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = $this->upload(dir: 'chadhava/', format: 'webp', image: $image);
                $chadhavaImages[] = $imageName;
                if ($request->has('images_active') && $request->has('images') && count($request->images) > 0) {
                    $colorImageArray[] = [
                        'image_name' => $imageName,
                    ];
                }
            }
        }

        return [
            'image_names' => $chadhavaImages ?? []
        ];
    }



    public function getProductsArray(object $request): array
    {
        $product = [];
        if ($request['product_id'] != null) {
            $category[] = [
                'id' => $request['product_id'],
                'position' => 1,
            ];
        }

        return $product;
    }


    public function getSlug(object $request): string
    {
        return Str::slug($request['name'][array_search('en', $request['lang'])], '-');
    }


    public function getProductDropdown(object $request, object $productes): string
    {
        $dropdown = '<option value="' . 0 . '" disabled selected>---' . translate("Select") . '---</option>';
        foreach ($productes as $row) {
            if ($row->id == $request['name']) {
                $dropdown .= '<option value="' . $row->id . '" selected >' . $row->defaultName . '</option>';
            } else {
                $dropdown .= '<option value="' . $row->id . '">' . $row->defaultName . '</option>';
            }
        }

        return $dropdown;
    }

    public function deleteImages(object $chadhava): bool
    {
        if (!is_null($chadhava['images'])) {
            $images = json_decode($chadhava['images']);
            if (!is_null($images)) {
                foreach ($images as $image) {
                    $this->delete(filePath: '/chadhava/' . $image);
                }
            }
        }
        $this->delete(filePath: '/chadhava/thumbnail/' . $chadhava['thumbnail']);
        return true;
    }

    public function deleteImage(object $request, object $chadhava): array
    {
        $images = [];
        foreach (json_decode($chadhava['images']) as $image) {
            if ($image != $request['name']) {
                $images[] = $image;
            }
        }
        return [
            'images' => $images
        ];
    }


    // Counslling Code
    public function getAddChadhavaData(object $request, string $addedBy): array
    {
        // dd($addedBy);
        $processedImages = $this->getProcessedImages(request: $request);
        return [
            'added_by' => $addedBy,
            'user_id' => $addedBy == 'admin' ? auth('admin')->id() : auth('seller')->id(),
            'bhagwan_id' => $request['bhagwan_id'],
            'name' => $request['name'][array_search('en', $request['lang'])],
            'slug' => $this->getSlug($request),
            'short_details' => $request['short_details'][array_search('en', $request['lang'])],
            'pooja_heading' => $request['pooja_heading'][array_search('en', $request['lang'])],
            'chadhava_venue' => $request['chadhava_venue'][array_search('en', $request['lang'])],
            'details' => $request['description'][array_search('en', $request['lang'])],
            'chadhava_type' => $request['chadhava_type'],
            // Conditional Fields:
            'chadhava_week' => ($request['chadhava_type'] ?? 0) == 1 ? null : json_encode($request['chadhava_week'] ?? []),
            'start_date'    => ($request['chadhava_type'] ?? 0) == 0 ? null : $request['start_date'] ?? null,
            'end_date'      => ($request['chadhava_type'] ?? 0) == 0 ? null : $request['end_date'] ?? null,

            'is_video' => $request['is_video'],
            'product_id' => json_encode($request['product_id']),
            'status' => $addedBy == 'admin' ? 1 : 0,
            'images' => json_encode($processedImages['image_names']),
            'thumbnail' => $this->upload(dir: 'chadhava/thumbnail/', format: 'png', image: $request['image']),
            'meta_title' => $request['meta_title'],
            'meta_description' => $request['meta_description'],
            'meta_image' => $this->upload(dir: 'chadhava/meta/', format: 'png', image: $request['meta_image']),
        ];
    }
    public function getUpdateChadhavaData(object $request, object $chadhava, string $updateBy): array
    {

        $processedImages = $this->getProcessedUpdateImages(request: $request, chadhava: $chadhava);
        $dataArray = [
            'name' => $request['name'][array_search('en', $request['lang'])],
            'slug' => $this->getSlug($request),
            'bhagwan_id' => $request['bhagwan_id'],
            'short_details' => $request['short_details'][array_search('en', $request['lang'])],
            'pooja_heading' => $request['pooja_heading'][array_search('en', $request['lang'])],
            'chadhava_venue' => $request['chadhava_venue'][array_search('en', $request['lang'])],
            'details' => $request['description'][array_search('en', $request['lang'])],
            'chadhava_type' => $request['chadhava_type'],
            // Conditional Fields:
            'chadhava_week' => ($request['chadhava_type'] ?? 0) == 1 ? null : json_encode($request['chadhava_week'] ?? []),
            'start_date'    => ($request['chadhava_type'] ?? 0) == 0 ? null : $request['start_date'] ?? null,
            'end_date'      => ($request['chadhava_type'] ?? 0) == 0 ? null : $request['end_date'] ?? null,

            'is_video' => $request['is_video'],
            'product_id' => json_encode($request['product_id']),
            'images' => json_encode($processedImages['image_names']),
            'meta_title' => $request['meta_title'],
            'meta_description' => $request['meta_description'],
            'meta_image' => $request->file('meta_image') ? $this->update(dir: 'chadhava/meta/', oldImage: $chadhava['meta_image'], format: 'png', image: $request['meta_image']) : $chadhava['meta_image'],
        ];

        if ($request->file('image')) {
            $dataArray += [
                'thumbnail' => $this->update(dir: 'chadhava/thumbnail/', oldImage: $chadhava['thumbnail'], format: 'webp', image: $request['image'], fileType: 'image')
            ];
        }



        if ($updateBy == 'seller' && $chadhava->request_status == 2) {
            $dataArray += [
                'request_status' => 0
            ];
        }

        if ($updateBy == 'admin' && $chadhava->added_by == 'seller' && $chadhava->request_status == 2) {
            $dataArray += [
                'request_status' => 1
            ];
        }

        return $dataArray;
    }
}
