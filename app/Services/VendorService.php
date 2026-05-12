<?php

namespace App\Services;

use App\Traits\FileManagerTrait;
use Illuminate\Support\Str;

class VendorService
{
    use FileManagerTrait;
    /**
     * @param string $email
     * @param string $password
     * @param string|bool|null $rememberToken
     * @return bool
     */
    public function isLoginSuccessful(string $type, string $email, string $password, string|null|bool $rememberToken): bool
    {
        if ($type == 'seller' && auth('seller')->attempt(['email' => $email, 'password' => $password], $rememberToken)) {
            return true;
        } elseif ($type == 'tour' && auth('tour')->attempt(['email' => $email, 'password' => $password], $rememberToken)) {
            return true;
        } elseif ($type == 'event' && auth('event')->attempt(['email' => $email, 'password' => $password], $rememberToken)) {
            return true;
        } elseif ($type == 'trust' && auth('trust')->attempt(['email' => $email, 'password' => $password], $rememberToken)) {
            return true;
        } elseif ($type == 'tour_employee' && auth('tour_employee')->attempt(['email' => $email, 'password' => $password], $rememberToken)) {
            return true;
        } elseif ($type == 'event_employee' && auth('event_employee')->attempt(['email' => $email, 'password' => $password], $rememberToken)) {
            return true;
        } elseif ($type == 'trust_employee' && auth('trust_employee')->attempt(['email' => $email, 'password' => $password], $rememberToken)) {
            return true;
        } elseif ($type == 'purohit' && auth('purohit')->attempt(['mobile' => $email, 'password' => $password], $rememberToken)) {
            return true;
        } elseif ($type == 'guruji' && auth('guruji')->attempt(['email' => $email, 'password' => $password], $rememberToken)) {
            return true;
        }
        return false;
    }

    /**
     * @return array
     */
    public function getInitialWalletData(int $vendorId): array
    {
        return [
            'seller_id' => $vendorId,
            'withdrawn' => 0,
            'commission_given' => 0,
            'total_earning' => 0,
            'pending_withdraw' => 0,
            'delivery_charge_earned' => 0,
            'collected_cash' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function logout(): void
    {
        auth()->guard('seller')->logout();
        auth()->guard('tour')->logout();
        auth()->guard('event')->logout();
        auth()->guard('trust')->logout();
        auth()->guard('tour_employee')->logout();
        auth()->guard('event_employee')->logout();
        auth()->guard('trust_employee')->logout();
        auth()->guard('guruji')->logout();
        session()->invalidate();
    }

    /**
     * @param object $request
     * @return array
     */
    public function getFreeDeliveryOverAmountData(object $request): array
    {
        return [
            'free_delivery_status' => $request['free_delivery_status'] == 'on' ? 1 : 0,
            'free_delivery_over_amount' => currencyConverter($request['free_delivery_over_amount'], 'usd'),
        ];
    }

    /**
     * @return array[minimum_order_amount: float|int]
     */
    public function getMinimumOrderAmount(object $request): array
    {
        return [
            'minimum_order_amount' => currencyConverter($request['minimum_order_amount'], 'usd')
        ];
    }

    /**
     * @param object $request
     * @param object $vendor
     * @return array
     */
    public function getVendorDataForUpdate(object $request, object $vendor): array
    {
        $image = $request['image'] ? $this->update(dir: 'seller/', oldImage: $vendor['image'], format: 'webp', image: $request->file('image')) : $vendor['image'];
        $aadharFrontImage = $request['aadhar_front_image'] ? $this->update(dir: 'seller/', oldImage: $vendor['aadhar_front_image'], format: 'webp', image: $request->file('aadhar_front_image')) : $vendor['aadhar_front_image'];
        $aadharBackImage = $request['aadhar_back_image'] ? $this->update(dir: 'seller/', oldImage: $vendor['aadhar_back_image'], format: 'webp', image: $request->file('aadhar_back_image')) : $vendor['aadhar_back_image'];
        $pancardImage = $request['pancard_image'] ? $this->update(dir: 'seller/', oldImage: $vendor['pancard_image'], format: 'webp', image: $request->file('pancard_image')) : $vendor['pancard_image'];
        return [
            'f_name' => $request['f_name'],
            'l_name' => $request['l_name'],
            'phone' => $request['phone'],
            'image' => $image,
            'gst' => $request['gst'],
            'aadhar_number' => $request['aadhar_number'],
            'aadhar_front_image' => $aadharFrontImage,
            'aadhar_back_image' => $aadharBackImage,
            'pan_number' => $request['pan_number'],
            'pancard_image' => $pancardImage,
        ];
    }

    /**
     * @return array[password: string]
     */
    public function getVendorPasswordData(object $request): array
    {
        return [
            'password' => bcrypt($request['password']),
        ];
    }

    /**
     * @param object $request
     * @return array
     */
    public function getVendorBankInfoData(object $request): array
    {
        return [
            'bank_name' => $request['bank_name'],
            'branch' => $request['branch'],
            'ifsc' => $request['ifsc'],
            'holder_name' => $request['holder_name'],
            'account_no' => $request['account_no'],
        ];
    }
    public function getAddData(object $request): array
    {
        return [
            'f_name' => $request['f_name'],
            'l_name' => $request['l_name'],
            'phone' => $request['phone'],
            'email' => $request['email'],
            'gst' => $request['gst'],
            'image' => $this->upload(dir: 'seller/', format: 'webp', image: $request->file('image')),
            'password' => bcrypt($request['password']),
            'status' => 'pending',
            "type" => (($request['from_submit'] == 'admin') ? 'seller' : $request['from_submit']),
            'update_seller_status' => 1,
        ];
    }

    public function updateSellerData(object $request, $vendor, $shop)
    {

        $user_image = $request['user_image'] ? $this->update(dir: 'seller/', oldImage: $vendor['image'], format: 'webp', image: $request->file('user_image')) : $vendor['image'];
        $aadharFrontImage = $request['aadhar_front_image'] ? $this->update(dir: 'seller/', oldImage: $vendor['aadhar_front_image'], format: 'webp', image: $request->file('aadhar_front_image')) : $vendor['aadhar_front_image'];
        $aadharBackImage = $request['aadhar_back_image'] ? $this->update(dir: 'seller/', oldImage: $vendor['aadhar_back_image'], format: 'webp', image: $request->file('aadhar_back_image')) : $vendor['aadhar_back_image'];
        $pancardImage = $request['pancard_image'] ? $this->update(dir: 'seller/', oldImage: $vendor['pancard_image'], format: 'webp', image: $request->file('pancard_image')) : $vendor['pancard_image'];
        $cancel_check = $request['cancel_check'] ? $this->update(dir: 'seller/', oldImage: $vendor['cancel_check'], format: 'webp', image: $request->file('cancel_check')) : $vendor['cancel_check'];
        $dataArray['seller'] =  [
            'f_name' => $request['f_name'],
            'l_name' => $request['l_name'],
            'phone' => $request['phone'],
            'image' => $user_image,
            'gst' => $request['gst'],
            'aadhar_number' => $request['aadhar_number'],
            'aadhar_front_image' => $aadharFrontImage,
            'aadhar_back_image' => $aadharBackImage,
            'pan_number' => $request['pan_number'],
            'pancard_image' => $pancardImage,
            'bank_name' => $request['bank_name'],
            'branch' => $request['branch'],
            'ifsc' => $request['ifsc'],
            'holder_name' => $request['holder_name'],
            'account_no' => $request['account_no'],
            "update_seller_status" => 0,
            "cancel_check" => $cancel_check,
        ];

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

        $dataArray['shop'] = [
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
            'city_name' => $request['city_name'],
            'state_name' => $request['state_name'],
            'country_name' => $request['country_name'],
            'latitude' => $request['latitude'],
            'longitude' => $request['longitude'],
        ];

        return $dataArray;
    }

    public function ReCorrectSellerData(object $request, $vendor, $shop)
    {
        if ($vendor['all_doc_info'] && json_decode($vendor['all_doc_info'], true)) {
            $check_validate = json_decode($vendor['all_doc_info'], true);

            $getUniqueArray = ['f_name', 'phone', 'email', 'user_image', 'image', 'banner', 'bank_name', 'branch', 'ifsc', 'account_no', 'holder_name', 'cancel_check', 'gst', 'aadhar_number', 'aadhar_front_image', 'pan_number', 'pancard_image', 'name',"building_no", 'address', 'pincode', 'gumasta', 'fassai_no', 'fassai_image', 'contact',];
            foreach ($getUniqueArray as $value) {
                if (($check_validate[$value]??0) == 2) {
                    $dataArray['seller']['reupload_doc_status'] = 3;
                    break;
                }
            }
            if ($check_validate['f_name'] == 2 || $check_validate['f_name'] == 0) {
                $check_validate['f_name'] = 0;
                $dataArray['seller']['f_name'] = $request['f_name'];
                $dataArray['seller']['l_name'] = $request['l_name'];
            }
            if ($check_validate['phone'] == 2 || $check_validate['phone'] == 0) {
                $check_validate['phone'] = 0;
                $dataArray['seller']['phone'] = $request['phone'];
            }
            if ($check_validate['user_image'] == 2 || $check_validate['user_image'] == 0) {
                $check_validate['user_image'] = 0;
                $dataArray['seller']['image'] = $request['user_image'] ? $this->update(dir: 'seller/', oldImage: $vendor['image'], format: 'webp', image: $request->file('user_image')) : $vendor['image'];
            }
            if ($check_validate['email'] == 2 || $check_validate['email'] == 0) {
                $check_validate['email'] = 0;
                $dataArray['seller']['email'] = $request['email'];
            }
            if ($check_validate['gst'] == 2 || $check_validate['gst'] == 0) {
                $check_validate['gst'] = 0;
                $dataArray['seller']['gst'] = $request['gst'];
            }
            if ($check_validate['aadhar_number'] == 2 || $check_validate['aadhar_number'] == 0) {
                $check_validate['aadhar_number'] = 0;
                $dataArray['seller']['aadhar_number'] = $request['aadhar_number'];
            }
            if ($check_validate['aadhar_front_image'] == 2 || $check_validate['aadhar_front_image'] == 0) {
                $check_validate['aadhar_front_image'] = 0;
                $dataArray['seller']['aadhar_front_image'] = $request['aadhar_front_image'] ? $this->update(dir: 'seller/', oldImage: $vendor['aadhar_front_image'], format: 'webp', image: $request->file('aadhar_front_image')) : $vendor['aadhar_front_image'];
                $dataArray['seller']['aadhar_back_image'] = $request['aadhar_back_image'] ? $this->update(dir: 'seller/', oldImage: $vendor['aadhar_back_image'], format: 'webp', image: $request->file('aadhar_back_image')) : $vendor['aadhar_back_image'];
            }
            if ($check_validate['pan_number'] == 2 || $check_validate['pan_number'] == 0) {
                $check_validate['pan_number'] = 0;
                $dataArray['seller']['pan_number'] = $request['pan_number'];
            }
            if ($check_validate['pancard_image'] == 2 || $check_validate['pancard_image'] == 0) {
                $check_validate['pancard_image'] = 0;
                $dataArray['seller']['pancard_image'] = $request['pancard_image'] ? $this->update(dir: 'seller/', oldImage: $vendor['pancard_image'], format: 'webp', image: $request->file('pancard_image')) : $vendor['pancard_image'];
            }
            if ($check_validate['bank_name'] == 2 || $check_validate['bank_name'] == 0) {
                $check_validate['bank_name'] = 0;
                $dataArray['seller']['bank_name'] = $request['bank_name'];
            }
            if ($check_validate['branch'] == 2 || $check_validate['branch'] == 0) {
                $check_validate['branch'] = 0;
                $dataArray['seller']['branch'] = $request['branch'];
            }
            if ($check_validate['ifsc'] == 2 || $check_validate['ifsc'] == 0) {
                $check_validate['ifsc'] = 0;
                $dataArray['seller']['ifsc'] = $request['ifsc'];
            }
            if ($check_validate['holder_name'] == 2 || $check_validate['holder_name'] == 0) {
                $check_validate['holder_name'] = 0;
                $dataArray['seller']['holder_name'] = $request['holder_name'];
            }
            if ($check_validate['account_no'] == 2 || $check_validate['account_no'] == 0) {
                $check_validate['account_no'] = 0;
                $dataArray['seller']['account_no'] = $request['account_no'];
            }
            $dataArray['seller']['update_seller_status'] = 0;
            if ($check_validate['cancel_check'] == 2 || $check_validate['cancel_check'] == 0) {
                $check_validate['cancel_check'] = 0;
                $dataArray['seller']['cancel_check'] =  $request['cancel_check'] ? $this->update(dir: 'seller/', oldImage: $vendor['cancel_check'], format: 'webp', image: $request->file('cancel_check')) : $vendor['cancel_check'];
            }

            if ($check_validate['name'] == 2 || $check_validate['name'] == 0) {
                $check_validate['name'] = 0;
                $dataArray['shop']['name'] = $request['name'];
            }
            if (($check_validate['building_no']??0) == 2 || ($check_validate['building_no']??0) == 0) {
                $check_validate['building_no'] = 0;
                $dataArray['shop']['building_no'] = $request['building_no'];
            }
            if ($check_validate['address'] == 2 || $check_validate['address'] == 0) {
                $check_validate['address'] = 0;
                $dataArray['shop']['address'] = $request['address'];
                $dataArray['shop']['city_name'] = $request['city_name'];
                $dataArray['shop']['state_name'] = $request['state_name'];
                $dataArray['shop']['country_name'] = $request['country_name'];
                $dataArray['shop']['latitude'] = $request['latitude'];
                $dataArray['shop']['longitude'] = $request['longitude'];
            }
            if ($check_validate['pincode'] == 2 || $check_validate['pincode'] == 0) {
                $check_validate['pincode'] = 0;
                $dataArray['shop']['pincode'] = $request['pincode'];
            }
            if ($check_validate['fassai_no'] == 2 || $check_validate['fassai_no'] == 0) {
                $check_validate['fassai_no'] = 0;
                $dataArray['shop']['fassai_no'] = $request['fassai_no'];
            }
            if ($check_validate['contact'] == 2 || $check_validate['contact'] == 0) {
                $check_validate['contact'] = 0;
                $dataArray['shop']['contact'] = $request['contact'];
            }
            if ($check_validate['image'] == 2 || $check_validate['image'] == 0) {
                $check_validate['image'] = 0;
                $dataArray['shop']['image'] = $request['image'] ? $this->update(dir: 'shop/', oldImage: $shop['image'], format: 'webp', image: $request->file('image')) : $shop['image'];
            }
            if ($check_validate['banner'] == 2 || $check_validate['banner'] == 0) {
                $check_validate['banner'] = 0;
                $dataArray['shop']['banner'] = $request['banner'] ? $this->update(dir: 'shop/banner/', oldImage: $shop['banner'], format: 'webp', image: $request->file('banner')) : $shop['banner'];
            }
            if (!isset($check_validate['bottom_banner']) || $check_validate['bottom_banner'] == 2 || $check_validate['bottom_banner'] == 0) {
                $check_validate['bottom_banner'] = 0;
                $dataArray['shop']['bottom_banner'] = $request['bottom_banner'] ? $this->update(dir: 'shop/banner/', oldImage: $shop['bottom_banner'], format: 'webp', image: $request->file('bottom_banner')) : $shop['bottom_banner'];
            }
            if (!isset($check_validate['offer_banner']) || $check_validate['offer_banner'] == 2 || $check_validate['offer_banner'] == 0) {
                $check_validate['offer_banner'] = 0;
                $dataArray['shop']['offer_banner'] = $request['offer_banner'] ? $this->update(dir: 'shop/banner/', oldImage: $shop['offer_banner'], format: 'webp', image: $request->file('offer_banner')) : $shop['offer_banner'];
            }
            if ($check_validate['fassai_image'] == 2 || $check_validate['fassai_image'] == 0) {
                $check_validate['fassai_image'] = 0;
                $dataArray['shop']['fassai_image'] = $request['fassai_image'] ? $this->update(dir: 'shop/fassai/', oldImage: $shop['fassai_image'], format: 'webp', image: $request->file('fassai_image')) : $shop['fassai_image'];
            }
            if ($check_validate['gumasta'] == 2 || $check_validate['gumasta'] == 0) {
                $check_validate['gumasta'] = 0;
                $dataArray['shop']['gumasta'] = $request['gumasta'] ? $this->update(dir: 'shop/gumasta/', oldImage: $shop['gumasta'], format: 'webp', image: $request->file('gumasta')) : $shop['gumasta'];
            }
            $dataArray['seller']['all_doc_info'] = json_encode($check_validate);
        }
        return $dataArray;
    }
}