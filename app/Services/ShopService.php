<?php

namespace App\Services;

use App\Traits\FileManagerTrait;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class ShopService
{
    use FileManagerTrait;
    /**
     * @param object $vendor
     * @return array
     */
    public function getShopDataForAdd(object $vendor):array
    {
        return [
            'seller_id' =>$vendor['id'],
            'name' => $vendor['f_name'],
            'building_no' => '',
            'address' => '',
            'contact' => $vendor['phone'],
            'image' => 'def.png',
            'created_at' => now(),
            'updated_at' => now()
        ];
    }

    /**
     * @return array[name: mixed, address: mixed, contact: mixed, image: bool|mixed, banner: bool|mixed, bottomBanner: bool|mixed, offerBanner: bool|mixed]
     */
    public function getShopDataForUpdate(object $request, object $shop): array
    {
        $image = $request['image'] 
            ? $this->update(dir: 'shop/', oldImage: $shop['image'], format: 'webp', image: $request->file('image')) 
            : $shop['image'];
            
        $banner = $request['banner'] 
            ? $this->update(dir: 'shop/banner/', oldImage: $shop['banner'], format: 'webp', image: $request->file('banner')) 
            : $shop['banner'];
            
        $bottomBanner = $request['bottom_banner'] 
            ? $this->update(dir: 'shop/banner/', oldImage: $shop['bottom_banner'], format: 'webp', image: $request->file('bottom_banner')) 
            : $shop['bottom_banner'];
            
        $offerBanner = $request['offer_banner'] 
            ? $this->update(dir: 'shop/banner/', oldImage: $shop['offer_banner'], format: 'webp', image: $request->file('offer_banner')) 
            : $shop['offer_banner'];
            
        $fassaiImage = $request['fassai_image'] 
            ? $this->update(dir: 'shop/fassai/', oldImage: $shop['fassai_image'], format: 'webp', image: $request->file('fassai_image')) 
            : $shop['fassai_image'];
            
        $gumasta = $request['gumasta'] 
            ? $this->update(dir: 'shop/gumasta/', oldImage: $shop['gumasta'], format: 'webp', image: $request->file('gumasta')) 
            : $shop['gumasta'];
            
        return [
            'name' => $request['name'],
            'building_no' => $request['building_no'],
            'address' => $request['address'],
            'pincode' => $request['pincode'],
            'fassai_no' => $request['fassai_no'],
            'contact' => $request['contact'],
            'image' => $image,
            'banner' => $banner,
            'bottom_banner' => $bottomBanner,
            'offer_banner' => $offerBanner,
            'fassai_image' => $fassaiImage,
            'gumasta' => $gumasta,
        ];
    }

    /**
     * @return array[vacation_status: int, vacation_start_date: mixed, vacation_end_date: mixed, vacation_note: mixed]
     */
    public function getVacationData(object $request):array
    {
        return [
            'vacation_status' => $request['vacation_status'] == 'on' ? 1 : 0,
            'vacation_start_date' => $request['vacation_start_date'],
            'vacation_end_date' => $request['vacation_end_date'],
            'vacation_note' => $request['vacation_note'],
        ];
    }
    public function getAddShopDataForRegistration(object $request,int $vendorId):array
    {
        return [
            'seller_id' => $vendorId,
            'name' => $request['shop_name'],
            'slug' => Str::slug($request['shop_name'], '-') . '-' . Str::random(6),
            'building_no'=>$request['shop_building_no'],
            'address'=>$request['shop_address'],
            'contact' => $request['phone'],
            'city_name' =>$request['city_name'], 
            'state_name' =>$request['state_name'], 
            'country_name' =>$request['country_name'], 
            'latitude' =>$request['latitude'], 
            'longitude' =>$request['longitude'], 
            'pincode' =>$request['zipcode'],
            'fassai_no'=> $request['fssai_code'],
            'image' => $this->upload(dir: 'shop/', format: 'webp', image: $request->file('logo')),
            'banner' => $this->upload(dir: 'shop/banner/', format: 'webp', image: $request->file('banner')),
            'bottom_banner' => $this->upload(dir: 'shop/banner/', format: 'webp', image: $request->file('bottom_banner')),
        ];
    }

}