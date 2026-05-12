<?php

namespace App\Http\Controllers\Customer;

use App\Utils\Helpers;
use App\Http\Controllers\Controller;
use App\Models\ShippingAddress;
use App\Models\ShippingMethod;
use App\Models\CartShipping;
use App\Traits\CommonTrait;
use App\Utils\CartManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SystemController extends Controller
{
    use CommonTrait;
    public function set_payment_method($name)
    {
        if (auth('customer')->check() || session()->has('mobile_app_payment_customer_id')) {
            session()->put('payment_method', $name);
            return response()->json([
                'status' => 1
            ]);
        }
        return response()->json([
            'status' => 0
        ]);
    }

    public function set_shipping_method(Request $request)
    {
        if ($request['cart_group_id'] == 'all_cart_group') {
            foreach (CartManager::get_cart_group_ids() as $group_id) {
                $request['cart_group_id'] = $group_id;
                self::insert_into_cart_shipping($request);
            }
        } else {
            self::insert_into_cart_shipping($request);
        }

        return response()->json([
            'status' => 1
        ]);
    }

    public static function insert_into_cart_shipping($request)
    {
        $shipping = CartShipping::where(['cart_group_id' => $request['cart_group_id']])->first();
        if (isset($shipping) == false) {
            $shipping = new CartShipping();
        }
        $shipping['cart_group_id'] = $request['cart_group_id'];
        $shipping['shipping_method_id'] = $request['id'];
        $shipping['shipping_cost'] = ShippingMethod::find($request['id'])->cost;
        $shipping->save();
    }

    /*
     * default theme
     * @return json
     */
    public function choose_shipping_address(Request $request)
    {
        $zip_restrict_status = Helpers::get_business_settings('delivery_zip_code_area_restriction');
        $country_restrict_status = Helpers::get_business_settings('delivery_country_restriction');

        $physical_product = $request->physical_product;
        $shipping = [];
        $billing = [];

        parse_str($request->shipping, $shipping);
        parse_str($request->billing, $billing);
        $is_guest = !auth('customer')->check();

        if (isset($shipping['save_address']) && $shipping['save_address'] == 'on') {

            if ($shipping['contact_person_name'] == null || $shipping['address'] == null || $shipping['city'] == null || $shipping['zip'] == null || $shipping['country'] == null || ($is_guest && $shipping['email'] == null)) {
                return response()->json([
                    'errors' => translate('Fill_all_required_fields_of_shipping_address')
                ], 403);
            }
            elseif ($country_restrict_status && !self::delivery_country_exist_check($shipping['country'])) {
                return response()->json([
                    'errors' => translate('Delivery_unavailable_in_this_country.')
                ], 403);
            }
            elseif ($zip_restrict_status && !self::delivery_zipcode_exist_check($shipping['zip'])) {
                return response()->json([
                    'errors' => translate('Delivery_unavailable_in_this_zip_code_area')
                ], 403);
            }

            $address_id = DB::table('shipping_addresses')->insertGetId([
                'customer_id' => auth('customer')->id() ?? ((session()->has('guest_id') ? session('guest_id'):0)),
                'is_guest' => auth('customer')->check() ? 0 : (session()->has('guest_id') ? 1:0),
                'contact_person_name' => $shipping['contact_person_name'],
                'address_type' => $shipping['address_type'],
                'address' => $shipping['address'],
                'city' => $shipping['city'],
                'zip' => $shipping['zip'],
                'country' => $shipping['country'],
                'phone' => $shipping['phone'],
                'email' => auth('customer')->check() ? null : $shipping['email'],
                'latitude' => $shipping['latitude'],
                'longitude' => $shipping['longitude'],
                'is_billing' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        }
        else if (isset($shipping['shipping_method_id']) && $shipping['shipping_method_id'] == 0) {

            if ($shipping['contact_person_name'] == null || $shipping['address'] == null || $shipping['city'] == null || $shipping['zip'] == null || $shipping['country'] == null || ($is_guest && $shipping['email'] == null)) {
                return response()->json([
                    'errors' => translate('Fill_all_required_fields_of_shipping/billing_address')
                ], 403);
            }
            elseif ($country_restrict_status && !self::delivery_country_exist_check($shipping['country'])) {
                return response()->json([
                    'errors' => translate('Delivery_unavailable_in_this_country')
                ], 403);
            }
            elseif ($zip_restrict_status && !self::delivery_zipcode_exist_check($shipping['zip'])) {
                return response()->json([
                    'errors' => translate('Delivery_unavailable_in_this_zip_code_area')
                ], 403);
            }

            $address_id = DB::table('shipping_addresses')->insertGetId([
                'customer_id' => auth('customer')->id() ?? ((session()->has('guest_id') ? session('guest_id'):0)),
                'is_guest' => auth('customer')->check() ? 0 : (session()->has('guest_id') ? 1:0),
                'contact_person_name' => $shipping['contact_person_name'],
                'address_type' => $shipping['address_type'],
                'address' => $shipping['address'],
                'city' => $shipping['city'],
                'zip' => $shipping['zip'],
                'country' => $shipping['country'],
                'phone' => $shipping['phone'],
                'email' => auth('customer')->check() ? null : $shipping['email'],
                'latitude' => $shipping['latitude'],
                'longitude' => $shipping['longitude'],
                'is_billing' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        else {
            if (isset($shipping['shipping_method_id'])) {
                $address = ShippingAddress::find($shipping['shipping_method_id']);
                if (!$address->country || !$address->zip) {
                    return response()->json([
                        'errors' => translate('Please_update_country_and_zip_for_this_shipping_address')
                    ], 403);
                }
                elseif ($country_restrict_status && !self::delivery_country_exist_check($address->country)) {
                    return response()->json([
                        'errors' => translate('Delivery_unavailable_in_this_country')
                    ], 403);
                }
                elseif ($zip_restrict_status && !self::delivery_zipcode_exist_check($address->zip)) {
                    return response()->json([
                        'errors' => translate('Delivery_unavailable_in_this_zip_code_area')
                    ], 403);
                }
                $address_id = $shipping['shipping_method_id'];
            }else{
                $address_id =  0;
            }
        }

        if ($request->billing_addresss_same_shipping == 'false') {
            if (isset($billing['save_address_billing']) && $billing['save_address_billing'] == 'on') {

                if ($billing['billing_contact_person_name'] == null || $billing['billing_address'] == null || $billing['billing_city'] == null|| $billing['billing_zip'] == null || $billing['billing_country'] == null || ($is_guest && $billing['billing_contact_email'] == null)) {
                    return response()->json([
                        'errors' => translate('Fill_all_required_fields_of_billing_address')
                    ], 403);
                }
                elseif ($country_restrict_status && !self::delivery_country_exist_check($billing['billing_country'])) {
                    return response()->json([
                        'errors' => translate('Delivery_unavailable_in_this_country')
                    ], 403);
                }
                elseif ($zip_restrict_status && !self::delivery_zipcode_exist_check($billing['billing_zip'])) {
                    return response()->json([
                        'errors' => translate('Delivery_unavailable_in_this_zip_code_area')
                    ], 403);
                }

                $billing_address_id = DB::table('shipping_addresses')->insertGetId([
                    'customer_id' => auth('customer')->id() ?? ((session()->has('guest_id') ? session('guest_id'):0)),
                    'is_guest' => auth('customer')->check() ? 0 : (session()->has('guest_id') ? 1:0),
                    'contact_person_name' => $billing['billing_contact_person_name'],
                    'address_type' => $billing['billing_address_type'],
                    'address' => $billing['billing_address'],
                    'city' => $billing['billing_city'],
                    'zip' => $billing['billing_zip'],
                    'country' => $billing['billing_country'],
                    'phone' => $billing['billing_phone'],
                    'email' => auth('customer')->check() ? null : $billing['billing_contact_email'],
                    'latitude' => $billing['billing_latitude'],
                    'longitude' => $billing['billing_longitude'],
                    'is_billing' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);


            }
            elseif ($billing['billing_method_id'] == 0) {

                if ($billing['billing_contact_person_name'] == null || $billing['billing_address'] == null || $billing['billing_city'] == null || $billing['billing_zip'] == null || $billing['billing_country'] == null || ($is_guest && $billing['billing_contact_email'] == null)) {
                    return response()->json([
                        'errors' => translate('Fill_all_required_fields_of_billing_address')
                    ], 403);
                }
                elseif ($country_restrict_status && !self::delivery_country_exist_check($billing['billing_country'])) {
                    return response()->json([
                        'errors' => translate('Delivery_unavailable_in_this_country')
                    ], 403);
                }
                elseif ($zip_restrict_status && !self::delivery_zipcode_exist_check($billing['billing_zip'])) {
                    return response()->json([
                        'errors' => translate('Delivery_unavailable_in_this_zip_code_area')
                    ], 403);
                }

                $billing_address_id = DB::table('shipping_addresses')->insertGetId([
                    'customer_id' => auth('customer')->id() ?? ((session()->has('guest_id') ? session('guest_id'):0)),
                    'is_guest' => auth('customer')->check() ? 0 : (session()->has('guest_id') ? 1:0),
                    'contact_person_name' => $billing['billing_contact_person_name'],
                    'address_type' => $billing['billing_address_type'],
                    'address' => $billing['billing_address'],
                    'city' => $billing['billing_city'],
                    'zip' => $billing['billing_zip'],
                    'country' => $billing['billing_country'],
                    'phone' => $billing['billing_phone'],
                    'email' => auth('customer')->check() ? null : $billing['billing_contact_email'],
                    'latitude' => $billing['billing_latitude'],
                    'longitude' => $billing['billing_longitude'],
                    'is_billing' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            else {
                $address = ShippingAddress::find($billing['billing_method_id']);
                if ($physical_product == 'yes') {
                    if (!$address->country || !$address->zip) {
                        return response()->json([
                            'errors' => translate('Update_country_and_zip_for_this_billing_address')
                        ], 403);
                    }
                    elseif ($country_restrict_status && !self::delivery_country_exist_check($address->country)) {
                        return response()->json([
                            'errors' => translate('Delivery_unavailable_in_this_country')
                        ], 403);
                    }
                    elseif ($zip_restrict_status && !self::delivery_zipcode_exist_check($address->zip)) {
                        return response()->json([
                            'errors' => translate('Delivery_unavailable_in_this_zip_code_area')
                        ], 403);
                    }
                }
                $billing_address_id = $billing['billing_method_id'];
            }
        }
        else {
            $billing_address_id = $address_id;
        }

        session()->put('address_id', $address_id);
        session()->put('billing_address_id', $billing_address_id);

        return response()->json([], 200);
    }

    /*
     * except default theme
     * @return json
     */
    public function choose_shipping_address_other(Request $request){
        $shipping = [];
        $billing = [];
        parse_str($request->shipping, $shipping);
        parse_str($request->billing, $billing);
        $physical_product = $request->physical_product;
        $zip_restrict_status = Helpers::get_business_settings('delivery_zip_code_area_restriction');
        $country_restrict_status = Helpers::get_business_settings('delivery_country_restriction');
        $billing_input_by_customer=Helpers::get_business_settings('billing_input_by_customer');
        $is_guest = !auth('customer')->check();

        // shipping start
        $address_id = $shipping['shipping_method_id'] ?? 0;

        if(isset($shipping['shipping_method_id'])) {
            if ($shipping['contact_person_name'] == null || !isset($shipping['address_type']) || $shipping['address'] == null || $shipping['city'] == null || !isset($shipping['zip']) || $shipping['zip'] == null || !isset($shipping['country']) || $shipping['country'] == null || $shipping['phone'] == null || ($is_guest && $shipping['email'] == null)) {
                return response()->json([
                    'errors' => translate('Fill_all_required_fields_of_shipping_address')
                ], 403);
            } elseif ($country_restrict_status && !self::delivery_country_exist_check($shipping['country'])) {
                return response()->json([
                    'errors' => translate('Delivery_unavailable_in_this_country.')
                ], 403);
            } elseif ($zip_restrict_status && !self::delivery_zipcode_exist_check($shipping['zip'])) {
                return response()->json([
                    'errors' => translate('Delivery_unavailable_in_this_zip_code_area')
                ], 403);
            }
        }

        if (isset($shipping['save_address']) && $shipping['save_address'] == 'on') {
            $address_id = ShippingAddress::insertGetId([
                'customer_id' => auth('customer')->id() ?? ((session()->has('guest_id') ? session('guest_id'):0)),
                'is_guest' => auth('customer')->check() ? 0 : (session()->has('guest_id') ? 1:0),
                'contact_person_name' => $shipping['contact_person_name'],
                'address_type' => $shipping['address_type'],
                'address' => $shipping['address'],
                'city' => $shipping['city'],
                'zip' => $shipping['zip'],
                'country' => $shipping['country'],
                'phone' => $shipping['phone'],
                'latitude' => $shipping['latitude'],
                'longitude' => $shipping['longitude'],
                'email' => auth('customer')->check() ? null : $shipping['email'],
                'is_billing' => 0,
            ]);

        }elseif(isset($shipping['update_address']) && $shipping['update_address'] == 'on'){
            $get_shipping = ShippingAddress::find($address_id);
            $get_shipping->contact_person_name = $shipping['contact_person_name'];
            $get_shipping->address_type = $shipping['address_type'];
            $get_shipping->address = $shipping['address'];
            $get_shipping->city = $shipping['city'];
            $get_shipping->zip = $shipping['zip'];
            $get_shipping->country = $shipping['country'];
            $get_shipping->phone = $shipping['phone'];
            $get_shipping->latitude = $shipping['latitude'];
            $get_shipping->longitude = $shipping['longitude'];
            $get_shipping->save();

        }elseif(isset($shipping['shipping_method_id']) && !isset($shipping['update_address']) && !isset($shipping['save_address'])){
            $address_id = ShippingAddress::insertGetId([
                'customer_id' => auth('customer')->check() ? 0 : ((session()->has('guest_id') ? session('guest_id'):0)),
                'is_guest' => auth('customer')->check() ? 0 : (session()->has('guest_id') ? 1:0),
                'contact_person_name' => $shipping['contact_person_name'],
                'address_type' => $shipping['address_type'],
                'address' => $shipping['address'],
                'city' => $shipping['city'],
                'zip' => $shipping['zip'],
                'country' => $shipping['country'],
                'phone' => $shipping['phone'],
                'email' => auth('customer')->check() ? null : $shipping['email'],
                'latitude' => $shipping['latitude'],
                'longitude' => $shipping['longitude'],
                'is_billing' => 0,
            ]);
        }
        // shipping end

        // billing start
        $billing_address_id = $address_id ?? 0;
        if ($request->billing_addresss_same_shipping == 'false' && isset($billing['billing_method_id']) && $billing_input_by_customer) {
            $billing_address_id = $billing['billing_method_id'];


            if ($billing['billing_contact_person_name'] == null || !isset($billing['billing_address_type']) || !isset($billing['billing_address']) || $billing['billing_address'] == null || $billing['billing_city'] == null || !isset($billing['billing_zip']) || $billing['billing_zip'] == null || !isset($billing['billing_country']) || $billing['billing_country'] == null || $billing['billing_phone'] == null || ($is_guest && $billing['billing_contact_email'] == null)) {
                return response()->json([
                    'errors' => translate('Fill_all_required_fields_of_billing_address')
                ], 403);

            } elseif ($country_restrict_status && !self::delivery_country_exist_check($billing['billing_country'])) {
                return response()->json([
                    'errors' => translate('Delivery_unavailable_in_this_country')
                ], 403);

            } elseif ($zip_restrict_status && !self::delivery_zipcode_exist_check($billing['billing_zip'])) {
                return response()->json([
                    'errors' => translate('Delivery_unavailable_in_this_zip_code_area')
                ], 403);
            }

            if (isset($billing['save_address_billing']) && $billing['save_address_billing'] == 'on') {

                $billing_address_id = ShippingAddress::insertGetId([
                    'customer_id' => auth('customer')->id() ?? ((session()->has('guest_id') ? session('guest_id'):0)),
                    'is_guest' => auth('customer')->check() ? 0 : (session()->has('guest_id') ? 1:0),
                    'contact_person_name' => $billing['billing_contact_person_name'],
                    'address_type' => $billing['billing_address_type'],
                    'address' => $billing['billing_address'],
                    'city' => $billing['billing_city'],
                    'zip' => $billing['billing_zip'],
                    'country' => $billing['billing_country'],
                    'phone' => $billing['billing_phone'],
                    'email' => auth('customer')->check() ? null : $billing['billing_contact_email'],
                    'latitude' => $billing['billing_latitude'],
                    'longitude' => $billing['billing_longitude'],
                    'is_billing' => 1,
                ]);

            }elseif(isset($billing['update_billing_address']) && $billing['update_billing_address'] == 'on'){
                $get_billing = ShippingAddress::find($billing_address_id);
                $get_billing->contact_person_name = $billing['billing_contact_person_name'];
                $get_billing->address_type = $billing['billing_address_type'];
                $get_billing->address = $billing['billing_address'];
                $get_billing->city = $billing['billing_city'];
                $get_billing->zip = $billing['billing_zip'];
                $get_billing->country = $billing['billing_country'];
                $get_billing->phone = $billing['billing_phone'];
                $get_billing->latitude = $billing['billing_latitude'];
                $get_billing->longitude = $billing['billing_longitude'];
                $get_billing->save();

            }elseif(!isset($billing['update_billing_address']) && !isset($billing['save_address_billing'])){
                $billing_address_id = ShippingAddress::insertGetId([
                    'customer_id' => auth('customer')->check() ? 0 : ((session()->has('guest_id') ? session('guest_id'):0)),
                    'is_guest' => auth('customer')->check() ? 0 : (session()->has('guest_id') ? 1:0),
                    'contact_person_name' => $billing['billing_contact_person_name'],
                    'address_type' => $billing['billing_address_type'],
                    'address' => $billing['billing_address'],
                    'city' => $billing['billing_city'],
                    'zip' => $billing['billing_zip'],
                    'country' => $billing['billing_country'],
                    'phone' => $billing['billing_phone'],
                    'email' => auth('customer')->check() ? null : $billing['billing_contact_email'],
                    'latitude' => $billing['billing_latitude'],
                    'longitude' => $billing['billing_longitude'],
                    'is_billing' => 1,
                ]);
            }
        }elseif($request->billing_addresss_same_shipping == 'false' && !isset($billing['billing_method_id']) && $physical_product != 'yes'){
            return response()->json([
                'errors' => translate('Fill_all_required_fields_of_billing_address')
            ], 403);
        }

        session()->put('address_id', $address_id);
        session()->put('billing_address_id', $billing_address_id);

        return response()->json([], 200);
    }

}
