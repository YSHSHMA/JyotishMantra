<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Enums\ViewPaths\Admin\ServiceDetails;
use App\Traits\FileManagerTrait;
use Illuminate\Support\Str;
use App\Models\FAQModel;
use Rap2hpoutre\FastExcel\FastExcel;

class ServiceAdd
{
    use FileManagerTrait;

    public function getProcessedImages(object $request): array
    {
        $imageNames = [];
        if ($request->file('images')) {
            foreach ($request->file('images') as $image) {
                $images = $this->upload(dir: 'pooja/', format: 'webp', image: $image);
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
                $imageName = $this->upload(dir: 'pooja/', format: 'webp', image: $image);
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
                    $this->delete(filePath: '/pooja/' . $image);
                }
            }
        }
        $this->delete(filePath: '/pooja/thumbnail/' . $service['thumbnail']);
        return true;
    }

    public function deleteImage(object $request, object $service): array
    {
        $images = [];
        foreach (json_decode($service['images']) as $image) {
            if ($image != $request['name']) {
                $images[] = $image;
            }
        }
        return [
            'images' => $images
        ];
    }

    public function getAddServicesData(object $request, string $addedBy): array
    {   
        
        $processedImages = $this->getProcessedImages(request: $request);
        $digitalFile = '';
        if ($request['product_type'] == 'pooja' && $request['digital_product_type'] == 'ready_product') {
            $digitalFile = $this->fileUpload(dir: 'pooja/digital-product/', format: $request['digital_file_ready']->getClientOriginalExtension(), file: $request['digital_file_ready']);
        }
        $packageData=[];
        $package=$request['packages_id'];
        foreach($package as $key=> $pac){
            $packageData[$key]=['package_id'=>$pac,'package_price'=>$request['package_price'][$key]];
        }

        $cityData=[];
        $visibility=$request['visibility'];
        foreach($visibility as $key=> $visible){
            $cityData[$key]=['city'=>$request['city'][$key],'visibility'=>$visible];
        }
        // $poojaVenues = $request['pooja_venue'][array_search('en', $request['lang'])];
        // $poojaExpload= explode(',',$poojaVenues);
        return [
            'added_by' => $addedBy,
            'user_id' => $addedBy == 'admin' ? auth('admin')->id() : auth('seller')->id(),
            'name' => $request['name'][array_search('en', $request['lang'])],
            'short_benifits' => $request['short_benifits'][array_search('en', $request['lang'])],
            'pooja_heading' => $request['pooja_heading'][array_search('en', $request['lang'])],
            'slug' => $this->getSlug($request),
            'category_ids' => json_encode($this->getCategoriesArray(request: $request)),
            'visible_city' => json_encode($cityData),
            // 'is_visible_city' => $request['is_visible_city'],
            'category_id' => $request['category_id'],
            'sub_category_id' => $request['sub_category_id'],
            'sub_sub_category_id' => $request['sub_sub_category_id'],
            'packages_id' => json_encode($packageData),
            'shadule' => $request['shadule'],
            'pooja_type' => $request['pooja_type'],
            'pandit_assign' => $request['pandit_assign'],
            'product_id' => json_encode($request['product_id']),
            'product_type' => 'pooja',
            'details' => $request['details'][array_search('en', $request['lang'])],
            'benefits' => $request['benefits'][array_search('en', $request['lang'])],
            'process' => $request['process'][array_search('en', $request['lang'])],
            'temple_details' => $request['temple_details'][array_search('en', $request['lang'])],
            'pooja_venue' => $request['pooja_venue'][array_search('en', $request['lang'])],
            'prashadam_id' => $request['prashadam_id'],
            'pooja_time' => $request['pooja_time'],
            'week_days' => json_encode($request['week_days']),
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
    // Update Service GET
    public function getUpdateServiceData(object $request, object $service, string $updateBy): array
    {
        // dd($request);
        $processedImages = $this->getProcessedUpdateImages(request: $request, service: $service);
        $digitalFile = null;
        if ($request['product_type'] == 'pooja') {
            if ($request['digital_product_type'] == 'ready_product' && $request->hasFile('digital_file_ready')) {
                $digitalFile = $this->update(dir: 'product/digital-product/', oldImage: $service['digital_file_ready'], format: $request['digital_file_ready']->getClientOriginalExtension(), image: $request['digital_file_ready'], fileType: 'file');
            } elseif (($request['digital_product_type'] == 'ready_after_sell') && $service['digital_file_ready']) {
                $this->delete(filePath: 'pooja/digital-product/' . $service['digital_file_ready']);
            }
        } elseif ($request['product_type'] == 'pooja' && $service['digital_file_ready']) {
            $this->delete(filePath: 'pooja/digital-product/' . $service['digital_file_ready']);
        }
        $packageData=[];
        $package=$request['packages_id'];
        foreach($package as $key=> $pac){
            $packageData[$key]=['package_id'=>$pac,'package_price'=>$request['package_price'][$key]];
        }

        $cityData=[];
        $visibility=$request['visibility'];
        foreach($visibility as $key=> $visible){
            $cityData[$key]=['city'=>$request['city'][$key],'visibility'=>$visible];
        }
        // $poojaVenues = $request['pooja_venue'][array_search('en', $request['lang'])];
        // dd($poojaVenues);
        $dataArray = [
            'name' => $request['name'][array_search('en', $request['lang'])],
            'short_benifits' => $request['short_benifits'][array_search('en', $request['lang'])],
            'pooja_heading' => $request['pooja_heading'][array_search('en', $request['lang'])],
            'slug' => $this->getSlug($request),
            'product_type' => 'pooja',
            'pooja_type' => $request['pooja_type'],
            'category_ids' => json_encode($this->getCategoriesArray(request: $request)),
            'visible_city' => json_encode($cityData),
            // 'is_visible_city' => $request['is_visible_city'],
            'category_id' => $request['category_id'],
            'sub_category_id' => $request['sub_category_id'],
            'sub_sub_category_id' => $request['sub_sub_category_id'],
            'packages_id' => json_encode($packageData),
            'pandit_assign' => $request['pandit_assign'],
            'product_id' => json_encode($request['product_id']),
            'details' => $request['details'][array_search('en', $request['lang'])],
            'benefits' => $request['benefits'][array_search('en', $request['lang'])],
            'process' => $request['process'][array_search('en', $request['lang'])],
            'temple_details' => $request['temple_details'][array_search('en', $request['lang'])],
            'pooja_venue' => $request['pooja_venue'][array_search('en', $request['lang'])],
            'prashadam_id' => $request['prashadam_id'],
            'pooja_time' => $request['pooja_time'],
            'week_days' => $request['pooja_type'] == 1 ? null : json_encode($request['week_days']),
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
        if ($request['pooja_type'] == 1) {
            \App\Models\PoojaForecast::where('service_id', $service->id)
                ->update(['is_expired' => 1]);
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
