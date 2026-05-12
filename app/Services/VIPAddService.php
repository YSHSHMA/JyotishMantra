<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Enums\ViewPaths\Admin\VIPPath;
use App\Traits\FileManagerTrait;
use Illuminate\Support\Str;
use App\Models\VIPPooja;
use Rap2hpoutre\FastExcel\FastExcel;

class VIPAddService
{
    use FileManagerTrait;

    public function getProcessedImages(object $request): array
    {
        $imageNames = [];
        if ($request->file('images')) {
            foreach ($request->file('images') as $image) {
                $images = $this->upload(dir: 'pooja/vip/', format: 'webp', image: $image);
                $imageNames[] = $images;
                if($request->has('images_active') && $request->has('images') && count($request['images']) > 0){
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

    public function getProcessedUpdateImages(object $request, object $vip): array
    {
        $vipImages = json_decode($vip->images);
        $colorImageArray = [];
        if ($request->has('images_active') && $request->has('images') && count($request->images) > 0) {
            $dbColorImage = $vip->images ? json_decode($vip->images, true) : [];
            if (!$dbColorImage) {
                foreach ($vipImages as $image) {
                    $dbColorImage[] = [
                        'image_name' => $image,
                    ];
                }
            }
        }

        if ($request->file('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = $this->upload(dir: 'pooja/vip/', format: 'webp', image: $image);
                $vipImages[] = $imageName;
                if ($request->has('images_active') && $request->has('images') && count($request->images) > 0) {
                    $colorImageArray[] = [
                        'image_name' => $imageName,
                    ];
                }
            }
        }

        return [
            'image_names' => $vipImages ?? []
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
        $dropdown = '<option value="' . 0 . '" disabled selected>---'.translate("Select").'---</option>';
        foreach ($productes as $row) {
            if ($row->id == $request['name']) {
                $dropdown .= '<option value="' . $row->id . '" selected >' . $row->defaultName . '</option>';
            } else {
                $dropdown .= '<option value="' . $row->id . '">' . $row->defaultName . '</option>';
            }
        }

        return $dropdown;
    }

    public function deleteImages(object $vip): bool
    {
        if (!is_null($vip['images'])) {
            $images = json_decode($vip['images']);
            if (!is_null($images)) {
                foreach ($images as $image) {
                    $this->delete(filePath: '/pooja/vip/' . $image);
                }
            }
        }
        $this->delete(filePath: '/pooja/vip/thumbnail/' . $vip['thumbnail']);
        return true;
    }

    public function deleteImage(object $request, object $vip): array
    {
        $images = [];
        foreach (json_decode($vip['images']) as $image) {
            if ($image != $request['name']) {
                $images[] = $image;
            }
        }
        return [
            'images' => $images
        ];
    }


    // Counslling Code
    public function getAddVIPData(object $request, string $addedBy): array
    {   
        // dd($addedBy);
        $processedImages = $this->getProcessedImages(request: $request);
        $digitalFile = '';
        if ($request['digital_product_type'] == 'ready_product') {
            $digitalFile = $this->fileUpload(dir: 'vippooja/digital-product/', format: $request['digital_file_ready']->getClientOriginalExtension(), file: $request['digital_file_ready']);
        }
        $packageData=[];
        $package=$request['packages_id'];
        foreach($package as $key=> $pac){
            $packageData[$key]=['package_id'=>$pac,'package_price'=>$request['package_price'][$key]];
        }
        return [
            'added_by' => $addedBy,
            'user_id' => $addedBy == 'admin' ? auth('admin')->id() : auth('seller')->id(),
            'name' => $request['name'][array_search('en', $request['lang'])],
            'slug' => $this->getSlug($request),
            'short_benifits' => $request['short_benifits'][array_search('en', $request['lang'])],
            'pooja_heading' => $request['pooja_heading'][array_search('en', $request['lang'])],
            'details' => $request['details'][array_search('en', $request['lang'])],
            'benefits' => $request['benefits'][array_search('en', $request['lang'])],
            'process' => $request['process'][array_search('en', $request['lang'])],
            'temple_details' => $request['temple_details'][array_search('en', $request['lang'])],
            'is_anushthan' => $request['is_anushthan'],
            'packages_id' => json_encode($packageData),
            'product_id' => json_encode($request['product_id']),
            'video_provider' => 'youtube',
            'prashadam_id' => $request['prashadam_id'],
            'video_url' => $request['video_url'],
            'status' => $addedBy == 'admin' ? 1 : 0,
            'images' => json_encode($processedImages['image_names']),
            'thumbnail' => $this->upload(dir: 'pooja/vip/thumbnail/', format: 'png', image: $request['image']),
            'digital_file_ready' => $digitalFile,
            'meta_title' => $request['meta_title'],
            'meta_description' => $request['meta_description'],
            'meta_image' => $this->upload(dir: 'pooja/vip/meta/', format: 'png', image: $request['meta_image']),
        ];
    }
    public function getUpdateVIPData(object $request, object $vip, string $updateBy): array
    {
        $processedImages = $this->getProcessedUpdateImages(request: $request, vip: $vip);
        $digitalFile = null;
        if ($request['product_type'] == 'counselling') {
            if ($request['digital_product_type'] == 'ready_product' && $request->hasFile('digital_file_ready')) {
                $digitalFile = $this->update(dir: 'pooja/vip/digital-product/', oldImage: $vip['digital_file_ready'], format: $request['digital_file_ready']->getClientOriginalExtension(), image: $request['digital_file_ready'], fileType: 'file');
            } elseif (($request['digital_product_type'] == 'ready_after_sell') && $vip['digital_file_ready']) {
                $this->delete(filePath: 'pooja/vip/digital-product/' . $vip['digital_file_ready']);
            }
        } elseif ($vip['digital_file_ready']) {
            $this->delete(filePath: 'pooja/vip/digital-product/' . $vip['digital_file_ready']);
        }
        $packageData=[];
        $package=$request['packages_id'];
        foreach($package as $key=> $pac){
            $packageData[$key]=['package_id'=>$pac,'package_price'=>$request['package_price'][$key]];
        }
        $dataArray = [
            'name' => $request['name'][array_search('en', $request['lang'])],
            'slug' => $this->getSlug($request),
            'short_benifits' => $request['short_benifits'][array_search('en', $request['lang'])],
            'pooja_heading' => $request['pooja_heading'][array_search('en', $request['lang'])],
            'details' => $request['details'][array_search('en', $request['lang'])],
            'benefits' => $request['benefits'][array_search('en', $request['lang'])],
            'process' => $request['process'][array_search('en', $request['lang'])],
            'temple_details' => $request['temple_details'][array_search('en', $request['lang'])],
            'is_anushthan' => $request['is_anushthan'],
            'packages_id' => json_encode($packageData),
            'prashadam_id' => $request['prashadam_id'],
            'product_id' => json_encode($request['product_id']),
            'video_provider' => 'youtube',
            'video_url' => $request['video_url'],          
            'images' => json_encode($processedImages['image_names']),
            'digital_file_ready' => $digitalFile,
            'meta_title' => $request['meta_title'],
            'meta_description' => $request['meta_description'],
            'meta_image' => $request->file('meta_image') ? $this->update(dir: 'pooja/vip/meta/', oldImage: $vip['meta_image'], format: 'png', image: $request['meta_image']) : $vip['meta_image'],
        ];

        if ($request->file('image')) {
            $dataArray += [
                'thumbnail' => $this->update(dir: 'pooja/vip/thumbnail/', oldImage: $vip['thumbnail'], format: 'webp', image: $request['image'], fileType: 'image')
            ];
        }

       

        if($updateBy=='seller' && $vip->request_status == 2){
            $dataArray += [
                'request_status' => 0
            ];
        }

        if($updateBy=='admin' && $vip->added_by == 'seller' && $vip->request_status == 2){
            $dataArray += [
                'request_status' => 1
            ];
        }

        return $dataArray;
    }
   
}
