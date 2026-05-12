<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Enums\ViewPaths\Admin\ServiceDetails;
use App\Traits\FileManagerTrait;
use Illuminate\Support\Str;
use App\Models\FAQModel;
use Rap2hpoutre\FastExcel\FastExcel;

class OfflinePoojaRefundPolicyService
{
    use FileManagerTrait;

    public function getProcessedImages(object $request): array
    {
        $imageNames = [];
        if ($request->file('images')) {
            foreach ($request->file('images') as $image) {
                $images = $this->upload(dir: 'offlinepooja/', format: 'webp', image: $image);
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

    public function getProcessedUpdateImages(object $request, object $offlinePooja): array
    {
        $offlinePoojaImages = json_decode($offlinePooja->images);
        $colorImageArray = [];
        if ($request->has('images_active') && $request->has('images') && count($request->images) > 0) {
            $dbColorImage = $offlinePooja->images ? json_decode($offlinePooja->images, true) : [];
            if (!$dbColorImage) {
                foreach ($offlinePoojaImages as $image) {
                    $dbColorImage[] = [
                        'image_name' => $image,
                    ];
                }
            }
        }

        if ($request->file('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = $this->upload(dir: 'offlinepooja/', format: 'webp', image: $image);
                $offlinePoojaImages[] = $imageName;
                if ($request->has('images_active') && $request->has('images') && count($request->images) > 0) {
                    $colorImageArray[] = [
                        'image_name' => $imageName,
                    ];
                }
            }
        }

        return [
            'image_names' => $offlinePoojaImages ?? []
        ];
    }

    public function getCategoriesArray(object $request): array
    {
        $category = [];
        if ($request['category_id'] != null) {
            $category[] = [
                'id' => $request['category_id'],
                'position' => 1,
            ];
        }
        if ($request['sub_category_id'] != null) {
            $category[] = [
                'id' => $request['sub_category_id'],
                'position' => 2,
            ];
        }
        if ($request['sub_sub_category_id'] != null) {
            $category[] = [
                'id' => $request['sub_sub_category_id'],
                'position' => 3,
            ];
        }
        return $category;
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
  

    public function getCategoryDropdown(object $request, object $categories): string
    {
        $dropdown = '<option value="' . 0 . '" disabled selected>---'.translate("Select").'---</option>';
        foreach ($categories as $row) {
            if ($row->id == $request['sub_category']) {
                $dropdown .= '<option value="' . $row->id . '" selected >' . $row->defaultName . '</option>';
            } else {
                $dropdown .= '<option value="' . $row->id . '">' . $row->defaultName . '</option>';
            }
        }

        return $dropdown;
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

    public function deleteImages(object $service): bool
    {
        if (!is_null($service['images'])) {
            $images = json_decode($service['images']);
            if (!is_null($images)) {
                foreach ($images as $image) {
                    $this->delete(filePath: '/offlinepooja/' . $image);
                }
            }
        }
        $this->delete(filePath: '/pooja/thumbnail/' . $service['thumbnail']);
        return true;
    }
    
    public function deleteImage(object $request, object $offlinePooja): array
    {
        // dd($offlinePooja);
        $images = [];
        foreach (json_decode($offlinePooja['images']) as $image) {
            if ($image != $request['name']) {
                $images[] = $image;
            }
        }
        return [
            'images' => $images
        ];
    }

    public function getAddOfflinePoojaRefundPolicyData(object $request, string $addedBy): array
    {   
        return [
            'days' => $request['days'],
            'percent' => $request['percent'],
            'message' => $request['message'][array_search('en', $request['lang'])],
            'status' => 1,
        ];
    }
    // Update Service GET
    public function getUpdateServiceData(object $request, object $offlinePooja, string $updateBy): array
    {
        $dataArray = [
            'days' => $request['days'],
            'percent' => $request['percent'],
            'message' => $request['message'][array_search('en', $request['lang'])],
        ];
       
        if($updateBy=='seller' && $offlinePooja->request_status == 2){
            $dataArray += [
                'request_status' => 0
            ];
        }

        if($updateBy=='admin' && $offlinePooja->added_by == 'seller' && $offlinePooja->request_status == 2){
            $dataArray += [
                'request_status' => 1
            ];
        }

        return $dataArray;
    }


    // Event Update
    public function getUpdateEventData(object $request, object $service): array
    {
        $EventData=[];
        $schedule=$request['schedule'];
        // foreach($schedule as $key=> $pac){
        //     $EventData[$key]=['schedule_date'=>$pac,'schedule_time'=>$request['schedule_time'][$key]];
        // }
        $dataArray = [
            'schedule' => json_encode($schedule),
        ];
        return $dataArray;

    }

    // Counslling Code
    public function getAddCounsellingData(object $request, string $addedBy): array
    {   
        // dd($addedBy);
        $processedImages = $this->getProcessedImages(request: $request);
        $digitalFile = '';
        if ($request['product_type'] == 'counselling' && $request['digital_product_type'] == 'ready_product') {
            $digitalFile = $this->fileUpload(dir: 'pooja/digital-product/', format: $request['digital_file_ready']->getClientOriginalExtension(), file: $request['digital_file_ready']);
        }
        return [
            'added_by' => $addedBy,
            'user_id' => $addedBy == 'admin' ? auth('admin')->id() : auth('seller')->id(),
            'name' => $request['name'][array_search('en', $request['lang'])],
            'slug' => $this->getSlug($request),
            'category_ids' => json_encode($this->getCategoriesArray(request: $request)),
            'category_id' => $request['category_id'],
            'sub_category_id' => $request['sub_category_id'],
            'sub_sub_category_id' => $request['sub_sub_category_id'],
            'product_id' => json_encode($request['product_id']),
            'product_type' => 'counselling',
            'details' => $request['description'][array_search('en', $request['lang'])],
            'process' => $request['process'][array_search('en', $request['lang'])],
            'counselling_main_price' => $request['counselling_main_price'],
            'counselling_selling_price' => $request['counselling_selling_price'],
            'video_provider' => 'youtube',
            'video_url' => $request['video_url'],
            'status' => $addedBy == 'admin' ? 1 : 0,
            'images' => json_encode($processedImages['image_names']),
            'thumbnail' => $this->upload(dir: 'pooja/thumbnail/', format: 'png', image: $request['image']),
            'digital_file_ready' => $digitalFile,
            'meta_title' => $request['meta_title'],
            'meta_description' => $request['meta_description'],
            'meta_image' => $this->upload(dir: 'pooja/meta/', format: 'png', image: $request['meta_image']),
        ];
    }
    public function getUpdateCounsellingData(object $request, object $service, string $updateBy): array
    {
        $processedImages = $this->getProcessedUpdateImages(request: $request, service: $service);
        $digitalFile = null;
        if ($request['product_type'] == 'counselling') {
            if ($request['digital_product_type'] == 'ready_product' && $request->hasFile('digital_file_ready')) {
                $digitalFile = $this->update(dir: 'product/digital-product/', oldImage: $service['digital_file_ready'], format: $request['digital_file_ready']->getClientOriginalExtension(), image: $request['digital_file_ready'], fileType: 'file');
            } elseif (($request['digital_product_type'] == 'ready_after_sell') && $service['digital_file_ready']) {
                $this->delete(filePath: 'pooja/digital-product/' . $service['digital_file_ready']);
            }
        } elseif ($request['product_type'] == 'counselling' && $service['digital_file_ready']) {
            $this->delete(filePath: 'pooja/digital-product/' . $service['digital_file_ready']);
        }
        $dataArray = [
            'name' => $request['name'][array_search('en', $request['lang'])],
            'slug' => $this->getSlug($request),
            'product_type' => 'counselling',
            'category_ids' => json_encode($this->getCategoriesArray(request: $request)),
            'category_id' => $request['category_id'],
            'sub_category_id' => $request['sub_category_id'],
            'sub_sub_category_id' => $request['sub_sub_category_id'],
            'product_id' => json_encode($request['product_id']),
            'details' => $request['description'][array_search('en', $request['lang'])],
            'process' => $request['process'][array_search('en', $request['lang'])],
            'counselling_main_price' => $request['counselling_main_price'],
            'counselling_selling_price' => $request['counselling_selling_price'],
            'video_provider' => 'youtube',
            'video_url' => $request['video_url'],          
            'images' => json_encode($processedImages['image_names']),
            'digital_file_ready' => $digitalFile,
            'meta_title' => $request['meta_title'],
            'meta_description' => $request['meta_description'],
            'meta_image' => $request->file('meta_image') ? $this->update(dir: 'pooja/meta/', oldImage: $service['meta_image'], format: 'png', image: $request['meta_image']) : $service['meta_image'],
        ];

        if ($request->file('image')) {
            $dataArray += [
                'thumbnail' => $this->update(dir: 'pooja/thumbnail/', oldImage: $service['thumbnail'], format: 'webp', image: $request['image'], fileType: 'image')
            ];
        }

       

        if($updateBy=='seller' && $service->request_status == 2){
            $dataArray += [
                'request_status' => 0
            ];
        }

        if($updateBy=='admin' && $service->added_by == 'seller' && $service->request_status == 2){
            $dataArray += [
                'request_status' => 1
            ];
        }

        return $dataArray;
    }
   
}
