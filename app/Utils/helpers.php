<?php

namespace App\Utils;

use App\Models\AddFundBonusCategories;
use App\Models\OrderStatusHistory;
use App\Models\ShippingMethod;
use App\Models\Shop;
use App\Models\Admin;
use App\Models\BusinessSetting;
use App\Models\Category;
use App\Models\Color;
use App\Models\Coupon;
use App\Models\Currency;
use App\Models\Logs;
use App\Models\NotificationMessage;
use App\Models\Order;
use App\Models\Order_Pickup;
use App\Models\OrderDetail;
use App\Models\Permission;
use App\Models\PermissionRole;
use App\Models\Prashad_deliverys;
use App\Models\RefundRequest;
use App\Models\Seller;
use App\Models\Service_order;
use App\Models\Setting;
use App\Models\VendorPermissionRole;
use App\Models\VendorRoles;
use App\Traits\CommonTrait;
use App\User;
use App\Utils\CartManager;
use App\Utils\OrderManager;
use Carbon\Carbon;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class Helpers
{
    use CommonTrait;
    public static function status($id)
    {
        if ($id == 1) {
            $x = 'active';
        } elseif ($id == 0) {
            $x = 'in-active';
        }

        return $x;
    }

    // Mail Setup For All
    // public static function emailSendMessage($data)
    // {
    //     try {
    //         $configEmail = \App\Models\EmailSetup::where('type', $data['type'])->first();

    //         if (!empty($configEmail)) {
    //             $host = $configEmail['host'];
    //             $port = $configEmail['port'];
    //             $encryption = $configEmail['encryption'];

    //             $username = $configEmail['emailid'];
    //             $password = $configEmail['password'];
    //             $fromAddress = $configEmail['username'];
    //             $fromName = ucwords($configEmail['mailername']);

    //             $transport = \Symfony\Component\Mailer\Transport::fromDsn("smtp://$username:$password@$host:$port?encryption=$encryption");
    //             $mailer = new \Symfony\Component\Mailer\Mailer($transport);

    //             $email = (new \Symfony\Component\Mime\Email())
    //                 ->from(new \Symfony\Component\Mime\Address($fromAddress, $fromName))
    //                 ->to($data['email'])
    //                 ->subject($data['subject'])
    //                 ->html($data['htmlContent']);

    //             $mailer->send($email);
    //         }
    //     } catch (\Throwable $e) {
    //         \Log::error('Email sending failed: ' . $e->getMessage());
    //     }
    // }

    public static function emailSendMessage($data)
{
    try {
        $configEmail = \App\Models\EmailSetup::where('type', $data['type'])->first();
        if (!$configEmail) return;

        $host = $configEmail['host'];
        $port = $configEmail['port'];
        $encryption = $configEmail['encryption'];

        // 🔴 MOST IMPORTANT FIX
        $username = urlencode($configEmail['emailid']);
        $password = urlencode($configEmail['password']);

        $fromAddress = $configEmail['emailid'];
        $fromName = ucwords($configEmail['mailername']);

        $scheme = 'smtp';
        if ($encryption === 'ssl') {
            $scheme = 'smtps';
        }

        $dsn = "{$scheme}://{$username}:{$password}@{$host}:{$port}?verify_peer=0";

        $transport = \Symfony\Component\Mailer\Transport::fromDsn($dsn);
        $mailer = new \Symfony\Component\Mailer\Mailer($transport);

        $email = (new \Symfony\Component\Mime\Email())
            ->from(new \Symfony\Component\Mime\Address($fromAddress, $fromName))
            ->to($data['email'])
            ->subject($data['subject'])
            ->html($data['htmlContent']);

        $mailer->send($email);

    } catch (\Throwable $e) {
        \Log::error('Email sending failed: '.$e->getMessage());
    }
}

    public static function TemplateTextEmail($name, $type, $data)
    {
        $configEmail = \App\Models\EmailSetup::where('type', $name)->first();

        $getOlddata = \App\Models\EmailTemplates::where('slug', str_replace(' ', '_', $type))->first();
        $html_content = $getOlddata['html'] ?? "";
        $userInfo = \App\Models\User::where('id', ($data['customer_id'] ?? ""))->first();
        $email_send = $userInfo['email'] ?? "";
        if (!empty($data['vendor_email']) && filter_var($data['vendor_email'], FILTER_VALIDATE_EMAIL)) {
            $email_send = $data['vendor_email'];
        } elseif (empty($userInfo['email']) || $userInfo['email'] == 'user@mahakal.com' || !filter_var($userInfo['email'], FILTER_VALIDATE_EMAIL) || empty($html_content)) {
            return false;
        }

        $html_content = str_replace('{otp}', ($data['otp'] ?? "__"), $html_content);
        $html_content = str_replace('{driver_name}', ($data['driver_name'] ?? ""), $html_content);
        $html_content = str_replace('{driver_number}', ($data['driver_number'] ?? "__"), $html_content);
        $html_content = str_replace('{vehicle_name}', ($data['vehicle_name'] ?? "__"), $html_content);
        $html_content = str_replace('{vehicle_number}', ($data['vehicle_number'] ?? "__"), $html_content);

        $html_content = str_replace('{title_name}', ($data['title_name'] ?? "__"), $html_content);
        $html_content = str_replace('{service_name}', ($data['service_name'] ?? "__"), $html_content);
        $html_content = str_replace('{amount}', ($data['final_amount'] ?? 0.00), $html_content);

        $html_content = str_replace('{product_name}', ($data['product_name'] ?? "__"), $html_content);
        $html_content = str_replace('{order_amount}', ($data['order_amount'] ?? 0.00), $html_content);
        $html_content = str_replace('{remain_amount}', ($data['remain_amount'] ?? 0.00), $html_content);
        $html_content = str_replace('{refund_date}', ($data['refund_date'] ?? ''), $html_content);
        $html_content = str_replace('{order_id}', ($data['orderId'] ?? "__"), $html_content);
        $html_content = str_replace('{user_name}', ($userInfo['name'] ?? "__"), $html_content);
        $html_content = str_replace('{booking_date}', ($data['booking_date'] ?? '__'), $html_content);
        $html_content = str_replace('{ended_at}', ($data['ended_at'] ?? '__'), $html_content);
        $html_content = str_replace('{frequency}', ($data['frequency'] ?? '__'), $html_content);
        $html_content = str_replace('{paid_count}', ($data['paid_count'] ?? '__'), $html_content);
        $html_content = str_replace('{remaining_count}', ($data['remaining_count'] ?? '__'), $html_content);
        $html_content = str_replace('{member_names}', ($data['member_names'] ?? ""), $html_content);
        $html_content = str_replace('{gotra}', ($data['gotra'] ?? ""), $html_content);
        $html_content = str_replace('{prashad}', ($data['prashad'] ?? ""), $html_content);
        $html_content = str_replace('{ad_name}', ($data['ad_name'] ?? "__"), $html_content);
        $html_content = str_replace('{pan_card}', ($data['pan_card'] ?? "__"), $html_content);
        $html_content = str_replace('{place_name}', ($data['place_name'] ?? "__"), $html_content);
        $html_content = str_replace('{puja}', ($data['puja'] ?? ""), $html_content);
        $html_content = str_replace('{chadhava_venue}', ($data['chadhava_venue'] ?? "__"), $html_content);
        $html_content = str_replace('{puja_venue}', ($data['puja_venue'] ?? "__"), $html_content);
        $html_content = str_replace('{pandit_name}', ($data['pandit_name'] ?? ""), $html_content);
        $html_content = str_replace('{reject_reason}', ($data['reject_reason'] ?? ""), $html_content);
        $html_content = str_replace('{scheduled_time}', ($data['scheduled_time'] ?? "__"), $html_content);
        $html_content = str_replace('{live_stream}', ($data['live_stream'] ?? "mahakal.com"), $html_content);
        $html_content = str_replace('{share_video}', ($data['share_video'] ?? "mahakal.com"), $html_content);
        $html_content = str_replace('{certificate_link}', ($data['certificate_link'] ?? "mahakal.com"), $html_content);
        $html_content = str_replace('{time}', ($data['time'] ?? "__"), $html_content);
        $html_content = str_replace('{country}', ($data['country'] ?? "__"), $html_content);
        $html_content = str_replace('{city}', ($data['city'] ?? "__"), $html_content);
        $html_content = str_replace('{dob}', ($data['dob'] ?? "__"), $html_content);
        $html_content = str_replace('{number}', ($data['number'] ?? "__"), $html_content);
        $html_content = str_replace('{attachment}', ($data['attachment'] ?? " "), $html_content);
        $html_content = str_replace('{kundli_type}', ($data['kundli_type'] ?? " "), $html_content);
        $html_content = str_replace('{kundli_page}', ($data['kundli_page'] ?? " "), $html_content);
        $html_content = str_replace('{trust_name}', ($data['trust_name'] ?? " "), $html_content);
        $html_content = str_replace('{admin_commission}', ($data['admin_commission'] ?? "__"), $html_content);
        $html_content = str_replace('{payment_link}', ($data['payment_link'] ?? "__"), $html_content);
        $html_content = str_replace('{link}', ($data['link'] ?? "__"), $html_content);
        $html_content = str_replace('{temple_name}', ($data['temple_name'] ?? "__"), $html_content);
        try {
            if (!empty($configEmail)) {
                $host = $configEmail['host'];
                $port = $configEmail['port'];
                $encryption = $configEmail['encryption'];
                $username = $configEmail['emailid'];
                $password = $configEmail['password'];
                $fromAddress = $configEmail['username'];
                $fromName = ucwords($configEmail['mailername']);
                $newhtml_content =  view('email-templates/text-email-template', compact('html_content'))->render();

                $transport = \Symfony\Component\Mailer\Transport::fromDsn("smtp://$username:$password@$host:$port?encryption=$encryption");
                $mailer = new \Symfony\Component\Mailer\Mailer($transport);
                $email = (new \Symfony\Component\Mime\Email())
                    ->from(new \Symfony\Component\Mime\Address($fromAddress, $fromName))
                    ->to($email_send)
                    ->subject(ucwords(str_replace('_', ' ', $type)))
                    ->html($newhtml_content);
                if (!empty($data['attachment'])) {
                    $attachmentUrl = $data['attachment'];
                    $relativePath = str_replace(url('/storage'), '', $attachmentUrl);
                    $localPath = public_path('storage' . $relativePath);

                    if (file_exists($localPath)) {
                        $email->attachFromPath($localPath);
                    } else {
                    }
                }
                $mailer->send($email);
                return true;
            }
        } catch (\Throwable $e) {
            \Log::error('Email sending failed: ' . $e->getMessage());
        }
    }

    public static function CommanToastrMessage($type, $getorders)
    {
        $web_config  = \App\Models\BusinessSetting::where('type', 'company_fav_icon')->first();
        $token_user = \App\Models\User::where('id', ($getorders['customer_id']??""))->first()['cm_firebase_token'] ?? '';
        $data_user = [];
        $data_user['image'] = theme_asset(path: 'storage/app/public/company') . '/' . $web_config['value'];
        if ($type == 'Tour booking Confirmed') {
            $data_user['title'] = "Tour Booking Confirmed! 🎉";
            $data_user['description'] = "Your tour has been successfully booked. Get ready for an amazing adventure!";
            $data_user['type'] = "tour_booking";
            $data_user['link'] = route('account-order');
        } elseif ($type == "Reminder of tour date") {
            $data_user['title'] = "Reminder: Upcoming Tour Date " . date('d M,Y', strtotime($getorders['booking_date']));
            $data_user['description'] = "Don't forget! Your tour date is approaching. Prepare for an exciting journey.";
            $data_user['type'] = "tour_reminder";
            $data_user['link'] = route('account-order');
        } elseif ($type == "Completed") {
            $data_user['title'] = "Tour Completed! ✅";
            $data_user['description'] = "We hope you enjoyed your tour. Thank you for choosing us!";
            $data_user['type'] = "tour_completed";
            $data_user['link'] = route('account-order');
        } elseif ($type == "Refund") {
            $data_user['title'] = "Refund Processed 💸";
            $data_user['description'] = "Your refund has been successfully processed. Check your account for details.";
            $data_user['type'] = "tour_refund";
            $data_user['link'] = route('account-order');
        } elseif ($type == "Tour Canceled") {
            $data_user['title'] = "Tour Canceled ❌";
            $data_user['description'] = "We're sorry to inform you that your tour has been canceled. Please contact support for assistance.";
            $data_user['type'] = "tour_canceled";
            $data_user['link'] = route('account-order');
        } elseif ($type == "pickup otp") {
            $data_user['title'] = "Wishing You a Blessed Journey! 🙏✨";
            $data_user['description'] = "Thank you for choosing Mahakal.com! Wishing you a safe, smooth, and blessed journey. 🙏✨";
            $data_user['type'] = "pick";
            $data_user['link'] = route('account-order');
        } elseif ($type == "drop otp") {
            $data_user['title'] = "Welcome to Your Destination! 🙏✨";
            $data_user['description'] = "Thank you for traveling with Mahakal.com! May your arrival bring peace, joy, and divine blessings. Wishing you a wonderful stay! 🙏✨";
            $data_user['type'] = "drop";
            $data_user['link'] = route('account-order');
        }

        if ($token_user) {
            Helpers::send_push_notif_to_device1($token_user, $data_user);
        }
    }

    public static function transaction_formatter($transaction)
    {
        if ($transaction['paid_by'] == 'customer') {
            $user = User::find($transaction['payer_id']);
            $payer = $user->f_name . ' ' . $user->l_name;
        } elseif ($transaction['paid_by'] == 'seller') {
            $user = Seller::find($transaction['payer_id']);
            $payer = $user->f_name . ' ' . $user->l_name;
        } elseif ($transaction['paid_by'] == 'admin') {
            $user = Admin::find($transaction['payer_id']);
            $payer = $user->name;
        }

        if ($transaction['paid_to'] == 'customer') {
            $user = User::find($transaction['payment_receiver_id']);
            $receiver = $user->f_name . ' ' . $user->l_name;
        } elseif ($transaction['paid_to'] == 'seller') {
            $user = Seller::find($transaction['payment_receiver_id']);
            $receiver = $user->f_name . ' ' . $user->l_name;
        } elseif ($transaction['paid_to'] == 'admin') {
            $user = Admin::find($transaction['payment_receiver_id']);
            $receiver = $user->name;
        }

        $transaction['payer_info'] = $payer;
        $transaction['receiver_info'] = $receiver;

        return $transaction;
    }

    // Day and Timer View
    public static function getNextPoojaDay($weekday, $time)
    {


        $currentDateTime = new DateTime('now', new DateTimeZone('Asia/Kolkata'));
        $currentDateTime->modify('+1 days');

        $targetTime = DateTime::createFromFormat('H:i:s', $time, new DateTimeZone('Asia/Kolkata'));

        if (!is_array($weekday)) {
            $weekday = [];
        }
        $weekdays = array_map('strtolower', $weekday);

        $daysOfWeek = [
            'sunday' => 0,
            'monday' => 1,
            'tuesday' => 2,
            'wednesday' => 3,
            'thursday' => 4,
            'friday' => 5,
            'saturday' => 6
        ];

        for ($i = 0; $i < 7; $i++) {
            $dateToCheck = clone $currentDateTime;
            $dateToCheck->modify("+$i day");
            $dayOfWeek = strtolower($dateToCheck->format('l'));

            if (in_array($dayOfWeek, $weekdays)) {
                $poojaDateTime = new DateTime($dateToCheck->format('Y-m-d') . ' ' . $targetTime->format('H:i:s'), new DateTimeZone('Asia/Kolkata'));
                if ($dateToCheck->format('Y-m-d') == $currentDateTime->format('Y-m-d')) {
                    if ($poojaDateTime > $currentDateTime) {
                        return $poojaDateTime;
                    }
                } else {
                    return $poojaDateTime;
                }
            }
        }
        return null;
    }


    public static function get_customer($request = null)
    {
        $user = null;
        if (auth('customer')->check()) {
            $user = auth('customer')->user(); // for web

        } elseif (is_object($request) && method_exists($request, 'user')) {
            $user = $request->user() ?? $request->user; //for api

        } elseif (isset($request['payment_request_from']) && in_array($request['payment_request_from'], ['app', 'react']) && !isset($request->user)) {
            $user = $request['is_guest'] ? 'offline' : User::find($request['customer_id']);
        } elseif (session()->has('customer_id') && !session('is_guest')) {
            $user = User::find(session('customer_id'));
        } elseif (isset($request->user)) {
            $user = $request->user;
        }

        if ($user == null) {
            $user = 'offline';
        }

        return $user;
    }

    public static function coupon_discount($request)
    {
        $discount = 0;
        $user = Helpers::get_customer($request);
        $couponLimit = Order::where('customer_id', $user->id)
            ->where('coupon_code', $request['coupon_code'])->count();

        $coupon = Coupon::where(['code' => $request['coupon_code']])
            ->where('limit', '>', $couponLimit)
            ->where('status', '=', 1)
            ->whereDate('start_date', '<=', Carbon::parse()->toDateString())
            ->whereDate('expire_date', '>=', Carbon::parse()->toDateString())->first();

        if (isset($coupon)) {
            $total = 0;
            foreach (CartManager::get_cart(CartManager::get_cart_group_ids($request)) as $cart) {
                $product_subtotal = $cart['price'] * $cart['quantity'];
                $total += $product_subtotal;
            }
            if ($total >= $coupon['min_purchase']) {
                if ($coupon['discount_type'] == 'percentage') {
                    $discount = (($total / 100) * $coupon['discount']) > $coupon['max_discount'] ? $coupon['max_discount'] : (($total / 100) * $coupon['discount']);
                } else {
                    $discount = $coupon['discount'];
                }
            }
        }

        return $discount;
    }

    public static function default_lang()
    {
        if (strpos(url()->current(), '/api')) {
            $lang = App::getLocale();
        } elseif (session()->has('local')) {
            $lang = session('local');
        } else {
            $data = Helpers::get_business_settings('language');
            $code = 'en';
            $direction = 'ltr';
            foreach ($data as $ln) {
                if (array_key_exists('default', $ln) && $ln['default']) {
                    $code = $ln['code'];
                    if (array_key_exists('direction', $ln)) {
                        $direction = $ln['direction'];
                    }
                }
            }
            session()->put('local', $code);
            Session::put('direction', $direction);
            $lang = $code;
        }
        return $lang;
    }

    public static function rating_count($product_id, $rating)
    {
        return Review::where(['product_id' => $product_id, 'rating' => $rating])->whereNull('delivery_man_id')->count();
    }

    public static function get_business_settings($name)
    {
        $config = null;
        $check = ['currency_model', 'currency_symbol_position', 'system_default_currency', 'language', 'company_name', 'decimal_point_settings', 'product_brand', 'digital_product', 'company_email'];

        if (in_array($name, $check) == true && session()->has($name)) {
            $config = session($name);
        } else {
            $data = BusinessSetting::where(['type' => $name])->first();
            if (isset($data)) {
                $config = json_decode($data['value'], true);
                if (is_null($config)) {
                    $config = $data['value'];
                }
            }

            if (in_array($name, $check) == true) {
                session()->put($name, $config);
            }
        }

        return $config;
    }

    public static function get_settings($object, $type)
    {
        $config = null;
        foreach ($object as $setting) {
            if ($setting['type'] == $type) {
                $config = $setting;
            }
        }
        return $config;
    }

    public static function get_shipping_methods($seller_id, $type)
    {
        if ($type == 'admin') {
            return ShippingMethod::where(['status' => 1])->where(['creator_type' => 'admin'])->get();
        } else {
            return ShippingMethod::where(['status' => 1])->where(['creator_id' => $seller_id, 'creator_type' => $type])->get();
        }
    }

    public static function get_image_path($type)
    {
        $path = asset('storage/app/public/brand');
        return $path;
    }

    public static function set_data_format($data)
    {
        $colors = is_array($data['colors']) ? $data['colors'] : json_decode($data['colors']);
        $query_data = Color::whereIn('code', $colors)->pluck('name', 'code')->toArray();
        $color_process = [];
        foreach ($query_data as $key => $color) {
            $color_process[] = array(
                'name' => $color,
                'code' => $key,
            );
        }

        $color_image = isset($data['color_image']) ? (is_array($data['color_image']) ? $data['color_image'] : json_decode($data['color_image'])) : null;
        $color_final = [];
        foreach ($color_process as $color) {
            $image_name = null;
            if ($color_image) {
                foreach ($color_image as $image) {
                    if ($image->color && '#' . $image->color == $color['code']) {
                        $image_name = $image->image_name;
                    }
                }
            }
            $color_final[] = [
                'name' => $color['name'],
                'code' => $color['code'],
                'image' => $image_name,
            ];
        }

        $variation = [];
        $data['category_ids'] = is_array($data['category_ids']) ? $data['category_ids'] : json_decode($data['category_ids']);
        $data['images'] = is_array($data['images']) ? $data['images'] : json_decode($data['images']);
        $data['colors'] = $colors;
        $data['color_image'] = $color_image;
        $data['colors_formatted'] = $color_final;
        $attributes = [];
        if ((is_array($data['attributes']) ? $data['attributes'] : json_decode($data['attributes'])) != null) {
            $attributes_arr = is_array($data['attributes']) ? $data['attributes'] : json_decode($data['attributes']);
            foreach ($attributes_arr as $attribute) {
                $attributes[] = (int)$attribute;
            }
        }
        $data['attributes'] = $attributes;
        $data['choice_options'] = is_array($data['choice_options']) ? $data['choice_options'] : json_decode($data['choice_options']);
        $variation_arr = is_array($data['variation']) ? $data['variation'] : json_decode($data['variation'], true);
        foreach ($variation_arr as $var) {
            $variation[] = [
                'type' => $var['type'],
                'price' => (float)$var['price'],
                'sku' => $var['sku'],
                'qty' => (int)$var['qty'],
            ];
        }
        $data['variation'] = $variation;

        return $data;
    }


    public static function product_data_formatting($data, $multi_data = false)
    {
        if ($data) {
            $storage = [];
            if ($multi_data == true) {
                foreach ($data as $item) {
                    if ($item) {
                        $storage[] = Helpers::set_data_format($item);
                    }
                }
                $data = $storage;
            } else {
                $data = Helpers::set_data_format($data);;
            }

            return $data;
        }
        return null;
    }

    public static function units()
    {
        $x = ['kg', 'pc', 'gms', 'ltrs'];
        return $x;
    }

    public static function default_payment_gateways()
    {
        $methods = [
            'ssl_commerz',
            'paypal',
            'stripe',
            'razor_pay',
            'paystack',
            'senang_pay',
            'paymob_accept',
            'flutterwave',
            'paytm',
            'paytabs',
            'liqpay',
            'mercadopago',
            'bkash'
        ];
        return $methods;
    }

    public static function default_sms_gateways()
    {
        $methods = [
            'twilio',
            'nexmo',
            '2factor',
            'msg91',
            'releans',
        ];
        return $methods;
    }

    public static function remove_invalid_charcaters($str)
    {
        return str_ireplace(['\'', '"', ',', ';', '<', '>', '?'], ' ', preg_replace('/\s\s+/', ' ', $str));
    }

    public static function saveJSONFile($code, $data)
    {
        ksort($data);
        $jsonData = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        file_put_contents(base_path('resources/lang/en/messages.json'), stripslashes($jsonData));
    }

    public static function combinations($arrays)
    {
        $result = [[]];
        foreach ($arrays as $property => $property_values) {
            $tmp = [];
            foreach ($result as $result_item) {
                foreach ($property_values as $property_value) {
                    $tmp[] = array_merge($result_item, [$property => $property_value]);
                }
            }
            $result = $tmp;
        }
        return $result;
    }

    public static function error_processor($validator)
    {
        $err_keeper = [];
        foreach ($validator->errors()->getMessages() as $index => $error) {
            $err_keeper[] = ['code' => $index, 'message' => $error[0]];
        }
        return $err_keeper;
    }

    public static function currency_load()
    {
        $default = Helpers::get_business_settings('system_default_currency');
        $current = \session('system_default_currency_info');
        if (session()->has('system_default_currency_info') == false || $default != $current['id']) {
            $id = Helpers::get_business_settings('system_default_currency');
            $currency = Currency::find($id);
            session()->put('system_default_currency_info', $currency);
            session()->put('currency_code', $currency->code);
            session()->put('currency_symbol', $currency->symbol);
            session()->put('currency_exchange_rate', $currency->exchange_rate);
        }
    }

    public static function currency_converter($amount)
    {
        $currency_model = Helpers::get_business_settings('currency_model');
        if ($currency_model == 'multi_currency') {
            if (session()->has('usd')) {
                $usd = session('usd');
            } else {
                $usd = Currency::where(['code' => 'USD'])->first()->exchange_rate;
                session()->put('usd', $usd);
            }
            $my_currency = \session('currency_exchange_rate');
            $rate = $my_currency / $usd;
        } else {
            $rate = 1;
        }

        return Helpers::set_symbol(round($amount * $rate, 2));
    }

    public static function language_load()
    {
        if (\session()->has('language_settings')) {
            $language = \session('language_settings');
        } else {
            $language = BusinessSetting::where('type', 'language')->first();
            \session()->put('language_settings', $language);
        }
        return $language;
    }

    public static function tax_calculation($product, $price, $tax, $tax_type)
    {
        $amount = ($price / 100) * $tax;
        return $amount;

        //        $discount = self::get_product_discount(product: $product, price: $price);
        //        return (($price-$discount) / 100) * $tax; //after discount decrease
    }

    // Next Day Chadava View
    public static function  getNextChadhavaDay($ChadhavaWeek)
    {
        $currentDateTime = new DateTime('now', new DateTimeZone('Asia/Kolkata'));
        if (!is_array($ChadhavaWeek)) {
            $ChadhavaWeek = [];
        }
        $ChadhavaWeekdays = array_map('strtolower', $ChadhavaWeek);
        $daysOfWeek = [
            'sunday' => 0,
            'monday' => 1,
            'tuesday' => 2,
            'wednesday' => 3,
            'thursday' => 4,
            'friday' => 5,
            'saturday' => 6
        ];

        for ($i = 0; $i < 7; $i++) {
            $dateToCheck = clone $currentDateTime;
            $dateToCheck->modify("+$i day");
            $dayOfWeek = strtolower($dateToCheck->format('l'));
            if (in_array($dayOfWeek, $ChadhavaWeekdays)) {
                $ChadhavaDateTime = new DateTime(
                    $dateToCheck->format('Y-m-d') . ' 23:59:00',
                    new DateTimeZone('Asia/Kolkata')
                );
                // dd($dateToCheck);
                if ($dateToCheck->format('Y-m-d') == $currentDateTime->format('Y-m-d')) {
                    if ($ChadhavaDateTime > $currentDateTime) {
                        return $ChadhavaDateTime;
                    }
                } else {
                    return $ChadhavaDateTime;
                }
            }
        }
        return null;
    }

    public static function get_price_range($product)
    {
        $lowest_price = $product->unit_price;
        $highest_price = $product->unit_price;

        foreach (json_decode($product->variation) as $key => $variation) {
            if ($lowest_price > $variation->price) {
                $lowest_price = round($variation->price, 2);
            }
            if ($highest_price < $variation->price) {
                $highest_price = round($variation->price, 2);
            }
        }

        $lowest_price = Helpers::currency_converter($lowest_price - Helpers::get_product_discount($product, $lowest_price));
        $highest_price = Helpers::currency_converter($highest_price - Helpers::get_product_discount($product, $highest_price));

        if ($lowest_price == $highest_price) {
            return $lowest_price;
        }
        return $lowest_price . ' - ' . $highest_price;
    }

    public static function get_price_range_with_discount($product)
    {
        $lowest_price = $product->unit_price;
        $highest_price = $product->unit_price;
        $getOldPriceClass = (theme_root_path() === 'theme_aster' ? 'product__old-price text-muted' : '');

        foreach (json_decode($product->variation) as $key => $variation) {
            if ($lowest_price > $variation->price) {
                $lowest_price = round($variation->price, 2);
            }
            if ($highest_price < $variation->price) {
                $highest_price = round($variation->price, 2);
            }
        }

        if ($product->discount > 0) {
            $discounted_lowest_price = Helpers::currency_converter($lowest_price - Helpers::get_product_discount($product, $lowest_price));
            $discounted_highest_price = Helpers::currency_converter($highest_price - Helpers::get_product_discount($product, $highest_price));

            if ($discounted_lowest_price == $discounted_highest_price) {
                if ($discounted_lowest_price == self::currency_converter($lowest_price)) {
                    return $discounted_lowest_price;
                } else {
                    return theme_root_path() === "default" ? $discounted_lowest_price . " <del class='align-middle fs-16 text-muted'>" . self::currency_converter($lowest_price) . "</del> " : $discounted_lowest_price . " <del class='$getOldPriceClass'>" . self::currency_converter($lowest_price) . "</del> ";
                }
            }
            return  theme_root_path() === "default" ? "<span>" . $discounted_lowest_price . "</span>" . " <del class='align-middle fs-16 text-muted'>" . self::currency_converter($lowest_price) . "</del> " . ' - ' . "<span>" . $discounted_highest_price . "</span>" . " <del class='align-middle fs-16 text-muted'>" . self::currency_converter($highest_price) . "</del> " : $discounted_lowest_price . " <del class='$getOldPriceClass'>" . self::currency_converter($lowest_price) . "</del> " . ' - ' . $discounted_highest_price . " <del class='$getOldPriceClass'>" . self::currency_converter($highest_price) . "</del> ";
        } else {
            return  theme_root_path() === "default" ? "<span>" . self::currency_converter($lowest_price) . "</span>" . ' - ' . "<span>" . self::currency_converter($highest_price) . "</span>" : self::currency_converter($lowest_price) . ' - ' . self::currency_converter($highest_price);
        }
    }

    public static function get_product_discount($product, $price)
    {
        $discount = 0;
        if ($product['discount_type'] == 'percent') {
            $discount = ($price * $product['discount']) / 100;
        } elseif ($product['discount_type'] == 'flat') {
            $discount = $product['discount'];
        }

        return floatval($discount);
    }

    public static function module_permission_check($mod_name)
    {
        $user_role = auth('admin')->user()->role;
        $permission = $user_role->module_access;
        if (isset($permission) && $user_role->status == 1 && in_array($mod_name, (array)json_decode($permission)) == true) {
            return true;
        }

        if (auth('admin')->user()->admin_role_id == 1) {
            return true;
        }
        return false;
    }


    // ship rocket login
    public static function shiprocketLogin()
    {
        $email = env('SHIPROCKET_EMAIL');
        $password = env('SHIPROCKET_PASSWORD');

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://apiv2.shiprocket.in/v1/external/auth/login',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode([
                'email' => $email,
                'password' => $password
            ]),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        // Check for errors
        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
            curl_close($curl);
            // dd($error_msg);
            return false;
        }

        curl_close($curl);
        $decodedResponse = json_decode($response, true);
        // dd($decodedResponse);
        if (isset($decodedResponse['token'])) {
            return $response;
        } else {
            return false;
        }
    }

    // shiprocket logout
    public static function shiprocketLogout($shiprocketToken)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://apiv2.shiprocket.in/v1/external/auth/logout',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                "Authorization: Bearer $shiprocketToken"
            ),
        ));

        $response = curl_exec($curl);

        // Check for errors
        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
            curl_close($curl);
            // dd($error_msg);
            return false;
        }

        curl_close($curl);
        $decodedResponse = json_decode($response, true);
        // dd($decodedResponse);
        if ($decodedResponse) {
            return $response;
        } else {
            return false;
        }
    }


    // shiprocket cancel order
    public static function shiprocketCancelOrder($orderId, $shiprocketToken)
    {
        if (!empty($orderId)) {
            if (!empty($shiprocketToken)) {
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://apiv2.shiprocket.in/v1/external/orders/cancel',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => json_encode([
                        "ids" => [$orderId]
                    ]),
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/json',
                        "Authorization: Bearer $shiprocketToken"
                    ),
                ));

                $response = curl_exec($curl);

                // Check for errors
                if (curl_errno($curl)) {
                    $error_msg = curl_error($curl);
                    curl_close($curl);
                    // dd($error_msg);
                    return false;
                }

                curl_close($curl);
                $decodedResponse = json_decode($response, true);
                return $decodedResponse;
            }
            return ['status_code' => 402, 'message' => 'shiprocket token is not available'];
        }
        return ['status_code' => 402, 'message' => 'orderId is not available'];
    }

    //shiprocket track order
    public static function shiprocketTrackOrder($shipmentId, $shiprocketToken)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://apiv2.shiprocket.in/v1/external/courier/track/shipment/' . $shipmentId,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                "Authorization: Bearer $shiprocketToken"
            ),
        ));

        $response = curl_exec($curl);

        // Check for errors
        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
            curl_close($curl);
            // dd($error_msg);
            return false;
        }

        curl_close($curl);
        $decodedResponse = json_decode($response, true);
        if (isset($decodedResponse) && $decodedResponse['track_status'] == 1) {
            return $decodedResponse;
        } else {
            return false;
        }
    }

    // shiprocket place order
    public static function shiprocketPlaceOrder($orderId, $shiprocketToken)
    {
        if (!empty($orderId)) {
            if (!empty($shiprocketToken)) {
                $orderData = Order::where('id', $orderId)->with('orderDetails.product')->first();
                // dd($orderData);
                $discount = 0;
                $totalItemPrice = 0;
                $orderItems = [];
                foreach ($orderData['orderDetails'] as $key => $orderDetail) {
                    $orderItems[$key]['name'] = $orderDetail['product']['name'];
                    $orderItems[$key]['sku'] = $orderDetail['product']['code'];
                    $orderItems[$key]['units'] = $orderDetail['qty'];
                    $orderItems[$key]['selling_price'] = $orderDetail['price'] + $orderDetail['tax'];

                    $discount += $orderDetail['discount'];
                    $totalItemPrice += $orderDetail['price'] + $orderDetail['tax'];
                }

                if ($orderData) {
                    $billingDetail = $orderData['billing_address_data'];
                    $shippingDetail = $orderData['shipping_address_data'];

                    if ($billingDetail && $shippingDetail) {
                        $curl = curl_init();

                        $payload = json_encode([
                            "order_id" => $orderData['id'],
                            "order_date" => date("Y-m-d H:i", strtotime($orderData['created_at'])),
                            "pickup_location" => 'work',
                            "channel_id" => "",
                            "comment" => "",
                            "billing_customer_name" => $billingDetail->contact_person_name,
                            "billing_last_name" => "",
                            "billing_address" => $billingDetail->address,
                            "billing_address_2" => "",
                            "billing_city" => $billingDetail->city,
                            "billing_pincode" => $billingDetail->zip,
                            "billing_state" => $billingDetail->state,
                            "billing_country" => $billingDetail->country,
                            "billing_email" => $billingDetail->email,
                            "billing_phone" => explode('+91', $billingDetail->phone)[1],
                            "shipping_is_billing" => false,
                            "shipping_customer_name" => $shippingDetail->contact_person_name ?? $billingDetail->contact_person_name,
                            "shipping_last_name" => "",
                            "shipping_address" => $shippingDetail->address ?? $billingDetail->address,
                            "shipping_address_2" => "",
                            "shipping_city" => $shippingDetail->city ?? $billingDetail->city,
                            "shipping_pincode" => $shippingDetail->zip ?? $billingDetail->zip,
                            "shipping_country" => $shippingDetail->country ?? $billingDetail->country,
                            "shipping_state" => $shippingDetail->state ?? $billingDetail->state,
                            "shipping_email" => $shippingDetail->email ?? $billingDetail->email,
                            "shipping_phone" => explode('+91', $shippingDetail->phone)[1] ?? explode('+91', $billingDetail->phone)[1],
                            "order_items" => $orderItems,
                            "payment_method" => $orderData['payment_method'],
                            "shipping_charges" => $orderData['shipping_cost'],
                            "giftwrap_charges" => 0,
                            "transaction_charges" => 0,
                            "total_discount" => $discount + $orderData['discount_amount'],
                            "sub_total" => $totalItemPrice,
                            "length" => 10,
                            "breadth" => 10,
                            "height" => 10,
                            "weight" => 10,
                        ]);

                        // echo '<pre>';print_r(json_decode($payload)); die;

                        curl_setopt_array($curl, array(
                            CURLOPT_URL => 'https://apiv2.shiprocket.in/v1/external/orders/create/adhoc',
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                            CURLOPT_POSTFIELDS => $payload,
                            CURLOPT_HTTPHEADER => array(
                                'Content-Type: application/json',
                                "Authorization: Bearer $shiprocketToken"
                            ),
                        ));

                        $response = curl_exec($curl);

                        // Check for errors
                        if (curl_errno($curl)) {
                            $error_msg = curl_error($curl);
                            curl_close($curl);
                            // echo '<pre>'; echo 'error-'; print_r($error_msg); die;
                            return false;
                        }

                        curl_close($curl);

                        // echo '<pre>'; echo 'data-'; print_r(json_decode($response, true)); die;
                        $decodedResponse = json_decode($response, true);
                        return $decodedResponse;
                    }
                    return ['status_code' => 402, 'message' => 'unable to get user shipping or delivery details'];
                }
                return ['status_code' => 402, 'message' => 'unable to get user order detail'];
            }
            return ['status_code' => 402, 'message' => 'shiprocket token is not available'];
        }
        return ['status_code' => 402, 'message' => 'orderId is not available'];
    }

    // ShipWay API User Authentication 
    public static function shiprWayLogins()
    {
        $email = env('SHIPWAY_EMAIL');
        $licenseKey = env('SHIPWAY_LICENSE_KEY');

        // Prepare data to send in POST request
        $postData = json_encode([
            "username" => $email,
            "password" => $licenseKey
        ]);

        // Initialize cURL
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://shipway.in/api/authenticateUser',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $postData,
        ));

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
            curl_close($curl);
            return ['success' => false, 'error' => $error_msg];
        }

        curl_close($curl);

        $decodedResponse = json_decode($response, true);
        if ($decodedResponse['status'] == 'Success') {
            return ['success' => true, 'user_id' => $decodedResponse['user_id']];
        } else {
            return ['success' => false, 'error' => $decodedResponse['msg']];
        }
    }

    // ShipWay API PUSH ORDER 
    public static function ShipWayorderPlace($orderId, $weight, $length, $breadth, $height)
    {

        if (!empty($orderId)) {
            $email = env('SHIPWAY_EMAIL');
            $licenseKey = env('SHIPWAY_LICENSE_KEY');
            $token = base64_encode("logistics@mahakal.com:y9NpRj858EEQlAU6Tdp22vLH898TQ695");
            $authHeaderString = 'Authorization: Basic ' . $token;
            if (!empty($authHeaderString)) {
                $orderData = Order::where('id', $orderId)->with('orderDetails.product')->first();
                // dd($orderData);
                $tax = 0;
                $discount = 0;
                $totalItemPrice = 0;
                $orderItems = [];

                foreach ($orderData['orderDetails'] as $key => $orderDetail) {
                    $productDetails = json_decode($orderDetail['product_details'], true);
                    $orderItems[$key]['product'] = $productDetails['name']; // Extract product name
                    $orderItems[$key]['price'] = $orderDetail['price']; // Correct price source
                    $orderItems[$key]['product_code'] = $productDetails['code']; // Extract product code
                    $orderItems[$key]['product_quantity'] = $orderDetail['qty']; // Extract product quantity
                    $orderItems[$key]['discount'] = $orderDetail['discount']; // Correct discount value
                    $orderItems[$key]['tax_rate'] = $orderDetail['tax'];
                    $orderItems[$key]['tax_title'] = "";
                    // Calculate totals

                    $discount = $orderDetail['discount'];
                    $tax = $orderDetail['tax'];
                    $subTotal = $orderDetail['price'] * $orderDetail['qty'] + $orderData['shipping_cost'];
                    $totalItemPrice += $subTotal + $tax - $discount;
                }
                // dd
                $payment_type = ($orderData['payment_method'] == "razor_pay") ? "P" : (($orderData['payment_method'] == "cash_on_delivery") ? "C" : (($orderData['payment_method'] == "pay_by_wallet") ? "P" : ""));

                // dd($orderData);
                if ($orderData) {
                    $billingDetail = $orderData['billing_address_data'];
                    $shippingDetail = $orderData['shipping_address_data'];
                    if ($billingDetail && $shippingDetail) {
                        $payload = [
                            "order_id" => $orderData['id'],
                            "ewaybill" => "",
                            "products" => $orderItems,
                            "discount" => "",
                            "shipping" => $orderData['shipping_cost'],
                            "order_total" => $totalItemPrice,
                            "gift_card_amt" => "0",
                            "taxes" => $tax,
                            "payment_type" => $payment_type,
                            "email" => $billingDetail->email ?? '',
                            "billing_address" => $billingDetail->address,
                            "billing_address2" => "",
                            "billing_city" => $billingDetail->city,
                            "billing_state" => $billingDetail->state,
                            "billing_country" => $billingDetail->country,
                            "billing_firstname" => $billingDetail->contact_person_name,
                            "billing_lastname" => "",
                            "billing_phone" => isset($billingDetail->phone) ? explode('+91', $billingDetail->phone)[1] : '',
                            "billing_zipcode" => $billingDetail->zip,
                            "billing_latitude" => $billingDetail->latitude ?? '',
                            "billing_longitude" => $billingDetail->longitude ?? '',
                            "shipping_address" => $shippingDetail->address,
                            "shipping_address2" => "",
                            "shipping_city" => $shippingDetail->city,
                            "shipping_state" => $shippingDetail->state,
                            "shipping_country" => $shippingDetail->country,
                            "shipping_firstname" => $shippingDetail->contact_person_name,
                            "shipping_lastname" => "",
                            "shipping_phone" => isset($shippingDetail->phone) ? explode('+91', $shippingDetail->phone)[1] : '',
                            "shipping_zipcode" => $shippingDetail->zip,
                            "shipping_latitude" => $shippingDetail->latitude ?? '',
                            "shipping_longitude" => $shippingDetail->longitude ?? '',
                            "order_weight" => $weight,
                            "box_length" => $length,
                            "box_breadth" => $breadth,
                            "box_height" => $height,
                            "order_date" => date("Y-m-d H:i", strtotime($orderData['created_at']))
                        ];
                        // dd($payload);
                        $curl = curl_init();
                        curl_setopt_array($curl, array(
                            CURLOPT_URL => 'https://app.shipway.com/api/v2orders',
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                            CURLOPT_POSTFIELDS => json_encode($payload),
                            CURLOPT_HTTPHEADER => array(
                                $authHeaderString,
                                'Content-Type: application/json'
                            ),
                        ));

                        $response = curl_exec($curl);
                        // Check for errors
                        if (curl_errno($curl)) {
                            $error_msg = curl_error($curl);
                            curl_close($curl);
                            return ['success' => false, 'error' => $error_msg];
                        }
                        curl_close($curl);
                        // echo '<pre>'; echo 'data-'; print_r(json_decode($response, true)); die;
                        $decodedResponse = json_decode($response, true);
                        // dd($response);
                        return $decodedResponse;
                    }
                }
            }
            return ['status' => 401, 'message' => 'shiprocket token is not available'];
        }
        return ['status' => 404, 'message' => 'orderId is not available'];
    }

    // ShipWay API Label Gerante ORDER 
    public static function ShipWayorderLabelGenration($orderId)
    {

        if (!empty($orderId)) {
            $email = env('SHIPWAY_EMAIL');
            $licenseKey = env('SHIPWAY_LICENSE_KEY');
            $token = base64_encode("logistics@mahakal.com:y9NpRj858EEQlAU6Tdp22vLH898TQ695");
            $authHeaderString = 'Authorization: Basic ' . $token;
            if (!empty($authHeaderString)) {
                $orderData = Order::where('id', $orderId)->with('orderDetails.product')->first();

                $tax = 0;
                $discount = 0;
                $totalItemPrice = 0;
                $orderItems = [];

                foreach ($orderData['orderDetails'] as $key => $orderDetail) {
                    $productDetails = json_decode($orderDetail['product_details'], true);
                    $orderItems[$key]['product'] = $productDetails['name'];
                    $orderItems[$key]['price'] = $orderDetail['price'];
                    $orderItems[$key]['product_code'] = $productDetails['code'];
                    $orderItems[$key]['product_quantity'] = $orderDetail['qty'];
                    $orderItems[$key]['discount'] = $orderDetail['discount'];
                    $orderItems[$key]['tax_rate'] = $orderDetail['tax'];
                    $orderItems[$key]['tax_title'] = "";

                    $discount = $orderDetail['discount'];
                    $tax = $orderDetail['tax'];
                    $subTotal = $orderDetail['price'] * $orderDetail['qty'] + $orderData['shipping_cost'];
                    $totalItemPrice += $subTotal + $tax - $discount;
                }
                // dd
                $payment_type = ($orderData['payment_method'] == "razor_pay") ? "P" : (($orderData['payment_method'] == "cash_on_delivery") ? "C" : (($orderData['payment_method'] == "pay_by_wallet") ? "P" : ""));
                // $warehouseData = Order_Pickup::where('order_ids', $orderData['id'])->first();
                if ($orderData) {
                    $billingDetail = $orderData['billing_address_data'];
                    $shippingDetail = $orderData['shipping_address_data'];
                    if ($billingDetail && $shippingDetail) {
                        $payload = [
                            "order_id" => $orderData['id'],
                            "carrier_id" => $orderData['delivery_shipment_id'],
                            "warehouse_id" => $orderData['delivery_channel_id'],
                            "return_warehouse_id" => $orderData['delivery_channel_id'], //warehouse id
                            "ewaybill" => "AD767435878734PR",
                            "products" => $orderItems,
                            "discount" => "",
                            "shipping" => $orderData['shipping_cost'],
                            "order_total" => $totalItemPrice,
                            "gift_card_amt" => "0",
                            "taxes" => $tax,
                            "payment_type" => $payment_type,
                            "email" => $billingDetail->email ?? '',
                            "billing_address" => $billingDetail->address,
                            "billing_address2" => "",
                            "billing_city" => $billingDetail->city,
                            "billing_state" => $billingDetail->state,
                            "billing_country" => $billingDetail->country,
                            "billing_firstname" => $billingDetail->contact_person_name,
                            "billing_lastname" => "",
                            "billing_phone" => isset($billingDetail->phone) ? explode('+91', $billingDetail->phone)[1] : '',
                            "billing_zipcode" => $billingDetail->zip,
                            "billing_latitude" => $billingDetail->latitude ?? '',
                            "billing_longitude" => $billingDetail->longitude ?? '',
                            "shipping_address" => $shippingDetail->address,
                            "shipping_address2" => "",
                            "shipping_city" => $shippingDetail->city,
                            "shipping_state" => $shippingDetail->state,
                            "shipping_country" => $shippingDetail->country,
                            "shipping_firstname" => $shippingDetail->contact_person_name,
                            "shipping_lastname" => "",
                            "shipping_phone" => isset($shippingDetail->phone) ? explode('+91', $shippingDetail->phone)[1] : '',
                            "shipping_zipcode" => $shippingDetail->zip,
                            "shipping_latitude" => $shippingDetail->latitude ?? '',
                            "shipping_longitude" => $shippingDetail->longitude ?? '',
                            "order_weight" => $orderData['order_weight'],
                            "box_length" => $orderData['box_length'],
                            "box_breadth" => $orderData['box_breadth'],
                            "box_height" => $orderData['box_height'],
                            "order_date" => date("Y-m-d H:i", strtotime($orderData['created_at']))
                        ];
                        // dd($payload);
                        $curl = curl_init();
                        curl_setopt_array($curl, array(
                            CURLOPT_URL => 'https://app.shipway.com/api/v2orders',
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                            CURLOPT_POSTFIELDS => json_encode($payload),
                            CURLOPT_HTTPHEADER => array(
                                $authHeaderString,
                                'Content-Type: application/json'
                            ),
                        ));

                        $response = curl_exec($curl);
                        // Check for errors
                        if (curl_errno($curl)) {
                            $error_msg = curl_error($curl);
                            curl_close($curl);
                            return ['success' => false, 'error' => $error_msg];
                        }
                        curl_close($curl);
                        // echo '<pre>'; echo 'data-'; print_r(json_decode($response, true)); die;
                        $decodedResponse = json_decode($response, true);
                        // dd($response);
                        return $decodedResponse;
                    }
                }
            }
            return ['status' => 401, 'message' => 'shiprocket token is not available'];
        }
        return ['status' => 404, 'message' => 'orderId is not available'];
    }

    // ---------------------------SHIPWAY FOR PRASHAD ORDER DELEVERY----------------------------------
    public static function ShipWayorderPrashad($orderId)
    {

        if (!empty($orderId)) {
            $email = env('SHIPWAY_EMAIL');
            $licenseKey = env('SHIPWAY_LICENSE_KEY');
            $token = base64_encode("logistics@mahakal.com:y9NpRj858EEQlAU6Tdp22vLH898TQ695");
            $authHeaderString = 'Authorization: Basic ' . $token;
            if (!empty($authHeaderString)) {
                $orderData = Prashad_deliverys::where('order_id', $orderId)->with('products')->first();
                $tax = 0;
                $discount = 0;
                $totalItemPrice = 0;
                $orderItems = [];
                $products = $orderData->products;
                if (!is_iterable($products)) {
                    $products = [$products];
                }
                foreach ($products as $key => $orderDetail) {
                    $orderItems[$key]['product'] = $orderDetail->name;
                    $orderItems[$key]['price'] = $orderDetail->unit_price;
                    $orderItems[$key]['product_code'] = $orderDetail->code;
                    $orderItems[$key]['product_quantity'] = 1;
                    $orderItems[$key]['discount'] = $orderDetail->discount;
                    $orderItems[$key]['tax_rate'] = $orderDetail->tax;
                    $orderItems[$key]['tax_title'] = "";
                    $discount = $orderDetail->discount;
                    $tax = $orderDetail->tax;
                    $subTotal = $orderDetail->unit_price * 1 + $orderData->shipping_cost;
                    $totalItemPrice += $subTotal + $tax - $discount;
                }
                // $warehouseData = Order_Pickup::where('order_ids', $orderData['id'])->first();
                if ($orderData) {
                    $billingDetail = Service_order::where('order_id', $orderData['order_id'])->first();
                    $userData = User::where('id', $orderData['user_id'])->first();
                    // dd($userData);
                    if ($billingDetail) {
                        $payload = [
                            "order_id" => $orderData['order_id'],
                            "carrier_id" => $orderData['carrier_id'],
                            "warehouse_id" => $orderData['warehouse_id'],
                            "return_warehouse_id" => $orderData['warehouse_id'], //warehouse id
                            "ewaybill" => "AD767435878734PR",
                            "products" => $orderItems,
                            "discount" => "",
                            "shipping" => $orderData['shipping_cost'],
                            "order_total" => $totalItemPrice,
                            "gift_card_amt" => "0",
                            "taxes" => $tax,
                            "payment_type" => $orderData['payment_type'],
                            "email" => $userData->email ?? 'user@mahakal.com',
                            "billing_address" => $billingDetail->area,
                            "billing_address2" => "",
                            "billing_city" => $billingDetail->city,
                            "billing_state" => $billingDetail->state,
                            "billing_country" => 'india',
                            "billing_firstname" => $userData->f_name,
                            "billing_lastname" => $userData->l_name,
                            "billing_phone" => isset($userData->phone) ? explode('+91', $userData->phone)[1] : '',
                            "billing_zipcode" => $billingDetail->pincode,
                            "billing_latitude" => $billingDetail->latitude ?? '',
                            "billing_longitude" => $billingDetail->longitude ?? '',
                            "shipping_address" => $billingDetail->area,
                            "shipping_address2" => "",
                            "shipping_city" => $billingDetail->city,
                            "shipping_state" => $billingDetail->state,
                            "shipping_country" => 'india',
                            "shipping_firstname" => $userData->f_name,
                            "shipping_lastname" =>  $userData->l_name,
                            "shipping_phone" => isset($userData->phone) ? explode('+91', $userData->phone)[1] : '',
                            "shipping_zipcode" => $billingDetail->pincode,
                            "shipping_latitude" => $billingDetail->latitude ?? '',
                            "shipping_longitude" => $billingDetail->longitude ?? '',
                            "order_weight" => 0.30,
                            "box_length" => 10,
                            "box_breadth" => 10,
                            "box_height" => 10,
                            "order_date" => date("Y-m-d H:i", strtotime($orderData['created_at']))
                        ];
                        // dd($payload);
                        $curl = curl_init();
                        curl_setopt_array($curl, array(
                            CURLOPT_URL => 'https://app.shipway.com/api/v2orders',
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                            CURLOPT_POSTFIELDS => json_encode($payload),
                            CURLOPT_HTTPHEADER => array(
                                $authHeaderString,
                                'Content-Type: application/json'
                            ),
                        ));

                        $response = curl_exec($curl);
                        // Check for errors
                        if (curl_errno($curl)) {
                            $error_msg = curl_error($curl);
                            curl_close($curl);
                            return ['success' => false, 'error' => $error_msg];
                        }
                        curl_close($curl);
                        // echo '<pre>'; echo 'data-'; print_r(json_decode($response, true)); die;
                        $decodedResponse = json_decode($response, true);

                        return $decodedResponse;
                    }
                }
            }
            return ['status' => 401, 'message' => 'shiprocket token is not available'];
        }
        return ['status' => 404, 'message' => 'orderId is not available'];
    }

    // ShipWay API ORDER CANCEL
    public static function ShipWayorderOnHold($orderId)
    {
        if (!empty($orderId)) {
            $token = base64_encode("logistics@mahakal.com:y9NpRj858EEQlAU6Tdp22vLH898TQ695");
            $authHeaderString = 'Authorization: Basic ' . $token;
            $postData = json_encode([
                'order_ids' => [$orderId]
            ]);
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://app.shipway.com/api/Onholdorders/',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $postData,
                CURLOPT_HTTPHEADER => array(
                    $authHeaderString,
                    'Content-Type: application/json'
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            // dd($response);
            if (curl_errno($curl)) {
                $error_msg = curl_error($curl);
                curl_close($curl);
                return ['success' => true, 'error' => $error_msg];
            }
            curl_close($curl);
            $decodedResponse = json_decode($response, true);
            return $decodedResponse;
        }
        return ['status' => 404, 'message' => 'orderId is not available'];
    }

    // ShipWay API ORDER CANCEL
    public static function ShipwayCancelShipment($trackingId)
    {
        if (!empty($trackingId)) {
            $token = base64_encode("logistics@mahakal.com:y9NpRj858EEQlAU6Tdp22vLH898TQ695");
            $authHeaderString = 'Authorization: Basic ' . $token;
            $postData = json_encode([
                'awb_number' => [$trackingId]
            ]);
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://app.shipway.com/api/Cancel/',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $postData,
                CURLOPT_HTTPHEADER => array(
                    $authHeaderString,
                    'Content-Type: application/json'
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            // dd($response);
            if (curl_errno($curl)) {
                $error_msg = curl_error($curl);
                curl_close($curl);
                return ['success' => true, 'error' => $error_msg];
            }
            curl_close($curl);
            $decodedResponse = json_decode($response, true);
            return $decodedResponse;
        }
        return ['status' => 404, 'message' => 'orderId is not available'];
    }

    public static function ShipwayCreatemanifest($orderId)
    {
        if (!empty($orderId)) {
            $token = base64_encode("logistics@mahakal.com:y9NpRj858EEQlAU6Tdp22vLH898TQ695");
            $authHeaderString = 'Authorization: Basic ' . $token;
            $postData = json_encode([
                'order_ids' => [$orderId]
            ]);
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://app.shipway.com/api/Createmanifest/',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $postData,
                CURLOPT_HTTPHEADER => array(
                    $authHeaderString,
                    'Content-Type: application/json'
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            // dd($response);
            if (curl_errno($curl)) {
                $error_msg = curl_error($curl);
                curl_close($curl);
                return ['success' => true, 'error' => $error_msg];
            }
            curl_close($curl);
            $decodedResponse = json_decode($response, true);
            return $decodedResponse;
        }
        return ['status' => 404, 'message' => 'orderId is not available'];
    }

    // OrderDetails For Shipway order get
    public static function ShipwayGetOrder($order)
    {
        if (!empty($order)) {
            $token = base64_encode("logistics@mahakal.com:y9NpRj858EEQlAU6Tdp22vLH898TQ695");
            $authHeaderString = 'Authorization: Basic ' . $token;
            $curl = curl_init();
            // dd($order);
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://app.shipway.com/api/getorders?orderid=' . $order,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    $authHeaderString,
                    'Content-Type: application/json'
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            // dd($response);
            if (curl_errno($curl)) {
                $error_msg = curl_error($curl);
                curl_close($curl);
                return ['success' => true, 'error' => $error_msg];
            }
            curl_close($curl);
            $decodedResponse = json_decode($response, true);
            return $decodedResponse;
        }
        return ['status' => 404, 'message' => 'orderId is not available'];
    }

    // ShipWay API ORDER CANCEL
    public static function ShipWayorderChancel($orderId)
    {
        if (!empty($orderId)) {
            $token = base64_encode("logistics@mahakal.com:y9NpRj858EEQlAU6Tdp22vLH898TQ695");
            $authHeaderString = 'Authorization: Basic ' . $token;
            $postData = json_encode([
                'order_ids' => [$orderId]
            ]);
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://app.shipway.com/api/Cancelorders/',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $postData,
                CURLOPT_HTTPHEADER => array(
                    $authHeaderString,
                    'Content-Type: application/json'
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            // dd($response);
            if (curl_errno($curl)) {
                $error_msg = curl_error($curl);
                curl_close($curl);
                return ['success' => true, 'error' => $error_msg];
            }
            curl_close($curl);
            $decodedResponse = json_decode($response, true);
            return $decodedResponse;
        }
        return ['status' => 404, 'message' => 'orderId is not available'];
    }

    // Shipway order RefundAPI
    public static function ShipwayRefundOrder($refundOrderId)
    {
        if (!empty($refundOrderId)) {
            $email = env('SHIPWAY_EMAIL');
            $licenseKey = env('SHIPWAY_LICENSE_KEY');
            $token = base64_encode("logistics@mahakal.com:y9NpRj858EEQlAU6Tdp22vLH898TQ695");
            $authHeaderString = 'Authorization: Basic ' . $token;
            if (!empty($authHeaderString)) {
                $orderid = RefundRequest::where('order_id', $refundOrderId)->first();
                $orderData = OrderDetail::where('order_id', $refundOrderId)->where('product_id', $orderid['product_id'])
                    ->first();
                $tax = 0;
                $discount = 0;
                $totalItemPrice = 0;
                $orderItems = [];
                $productDetails = json_decode($orderData['product_details'], true);
                // dd($productDetails['code']);
                // Populate the order item details  
                $orderItems['product'] = $productDetails['name'];
                $orderItems['product_code'] = $productDetails['code'];
                $orderItems['price'] = $orderData['price'];
                $orderItems['discount'] = $orderData['discount'];
                $orderItems['tax_rate'] = $orderData['tax'];
                $orderItems['tax_title'] = "";
                $orderItems['return_reason_id'] = "78415";
                $orderItems['return_products_images'] = $orderid['images'];
                $orderItems['customer_notes'] = $orderid['refund_reason'];
                $orderItems['variants'] = $orderData['variant'];

                // Calculate totals
                $discount = $orderData['discount'];
                $tax = $orderData['tax'];
                $subTotal = ($orderData['price'] * $orderData['qty']) + ($orderData['shipping_cost'] ?? 0);
                $totalItemPrice = $subTotal + $tax - $discount;

                // dd($orderData);
                if ($orderData) {
                    $orderData = Order::where('id', $refundOrderId)->with('orderDetails.product')->first();
                    // dd($ orderData);
                    $billingDetail = $orderData['billing_address_data']  ?? '';
                    $shippingDetail = $orderData['shipping_address_data'] ?? '';
                    $payment_type = $orderData['payment_method'] == "razor_pay" ? "P" : ($orderData['payment_method'] == "cash_on_delivery" ? "C" : null);
                    // dd($billingDetail);
                    if ($billingDetail && $shippingDetail) {
                        $payload = [
                            "order_id" => $orderData['id'],
                            "return_order_status" => "R",
                            "refund_payment_id" => "5",
                            "products" => $orderItems,
                            "discount" => "",
                            "shipping" => $orderData['shipping_cost'],
                            "order_total" => $totalItemPrice,
                            "gift_card_amt" => "0",
                            "taxes" => $tax,
                            "payment_type" => $payment_type,
                            "email" => $billingDetail->email,
                            "billing_address" => $billingDetail->address,
                            "billing_address2" => "",
                            "billing_city" => $billingDetail->city,
                            "billing_state" => $billingDetail->state,
                            "billing_country" => $billingDetail->country,
                            "billing_firstname" => $billingDetail->contact_person_name,
                            "billing_lastname" => "",
                            "billing_phone" => isset($billingDetail->phone) ? explode('+91', $billingDetail->phone)[1] : '',
                            "billing_zipcode" => $billingDetail->zip,
                            "shipping_address" => $shippingDetail->address,
                            "shipping_address2" => "",
                            "shipping_city" => $shippingDetail->city,
                            "shipping_state" => $shippingDetail->state,
                            "shipping_country" => $shippingDetail->country,
                            "shipping_firstname" => $shippingDetail->contact_person_name,
                            "shipping_lastname" => "",
                            "shipping_phone" => isset($shippingDetail->phone) ? explode('+91', $shippingDetail->phone)[1] : '',
                            "shipping_zipcode" => $shippingDetail->zip,
                            "order_weight" => "110",
                            "box_length" => "20",
                            "box_breadth" => "15",
                            "box_height" => "10",
                            "order_date" => date("Y-m-d H:i", strtotime($orderData['created_at']))
                        ];
                        dd($payload);
                        $curl = curl_init();
                        curl_setopt_array($curl, array(
                            CURLOPT_URL => 'https://app.shipway.com/api/Createreturns',
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                            CURLOPT_POSTFIELDS => json_encode($payload),
                            CURLOPT_HTTPHEADER => array(
                                $authHeaderString,
                                'Content-Type: application/json'
                            ),
                        ));

                        $response = curl_exec($curl);
                        dd($response);
                        // Check for errors
                        if (curl_errno($curl)) {
                            $error_msg = curl_error($curl);
                            curl_close($curl);
                            return ['success' => false, 'error' => $error_msg];
                        }
                        curl_close($curl);
                        // echo '<pre>'; echo 'data-'; print_r(json_decode($response, true)); die;
                        $decodedResponse = json_decode($response, true);
                        return $decodedResponse;
                    }
                }
            }
            return ['status' => 401, 'message' => 'shiprocket token is not available'];
        }
        return ['status' => 404, 'message' => 'orderId is not available'];
    }
    // Order Get api
    public static function ShipwayGetOrders($orderId)
    {
        $token = base64_encode("logistics@mahakal.com:y9NpRj858EEQlAU6Tdp22vLH898TQ695");
        $authHeaderString = 'Authorization: Basic ' . $token;
        if (!empty($authHeaderString)) {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://app.shipway.com/api/Ndr/OrderDetails',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => '{
                        "order_ids" => "$orderId"
                    }',
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json'
                ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);
            if (curl_errno($curl)) {
                $error_msg = curl_error($curl);
                curl_close($curl);
                return ['success' => true, 'error' => $error_msg];
            }
            curl_close($curl);
            $decodedResponse = json_decode($response, true);
            return $decodedResponse;
        }
        return ['status' => 404, 'message' => 'orderId is not available'];
    }
    // Order get the carreir details show
    public static function ShipwayGetCarrierrates($fromPincode, $toPincode, $paymentType, $weight, $length, $breadth, $height)
    {
        $email = env('SHIPWAY_EMAIL');
        $licenseKey = env('SHIPWAY_LICENSE_KEY');
        $token = base64_encode("logistics@mahakal.com:y9NpRj858EEQlAU6Tdp22vLH898TQ695");
        $authHeaderString = 'Authorization: Basic ' . $token;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://app.shipway.com/api/getshipwaycarrierrates?fromPincode=' . $fromPincode . '&toPincode=' . $toPincode . '&paymentType=' . $paymentType . '&weight=' . $weight . '&length=' . $length . '&breadth=' . $breadth . '&height=' . $height,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                $authHeaderString,
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        // dd($response);
        if (curl_errno($curl)) {
            $error = curl_error($curl);
            curl_close($curl);
            return ['error' => $error];
        }

        curl_close($curl);
        return json_decode($response, true);
    }
    // Order Pickup 
    public static function ShipWayorderPickup($orderId, $pickup_date, $pickup_time, $office_close_time, $warehouse_id, $return_warehouse_id, $package_count)
    {
        $email = env('SHIPWAY_EMAIL');
        $licenseKey = env('SHIPWAY_LICENSE_KEY');
        $token = base64_encode("logistics@mahakal.com:y9NpRj858EEQlAU6Tdp22vLH898TQ695");
        $authHeaderString = 'Authorization: Basic ' . $token;
        $orderData = Order_Pickup::where('order_ids', $orderId)->first();
        $postData = [
            'order_ids' => [$orderId]
        ];
        $fields = [
            "pickup_date" => $pickup_date,
            "pickup_time" => $pickup_time,
            "office_close_time" => $office_close_time,
            "package_count" => $package_count,
            "carrier_id" => $orderData['carrier_id'],
            "warehouse_id" => $warehouse_id,
            "return_warehouse_id" => $return_warehouse_id,
            "payment_type" => $orderData['payment_type'],
            "order_ids" => $postData['order_ids']
        ];
        // dd($fields);
        $fieldsJson = json_encode($fields);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://app.shipway.com/api/createpickup/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $fieldsJson,
            CURLOPT_HTTPHEADER => array(
                $authHeaderString,
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        // dd($response);
        if (curl_errno($curl)) {
            $error = curl_error($curl);
            curl_close($curl);
            return ['error' => $error];
        }
        curl_close($curl);
        return json_decode($response, true);
    }

    // ShipWay API Create warehouse
    public static function ShipWayWarehouseCreate($data)
    {
        if (!empty($data)) {
            $token = base64_encode("logistics@mahakal.com:y9NpRj858EEQlAU6Tdp22vLH898TQ695");
            $authHeaderString = 'Authorization: Basic ' . $token;
            $postData = json_encode([
                "title" => $data["shop_name"],
                "company" => $data["shop_name"],
                "contact_person_name" => $data["f_name"] . " " . $data['l_name'],
                "email" => $data["email"],
                "phone" => substr($data["phone"], 0, 3) . "-" . substr($data["phone"], 3),
                "phone_print" => substr($data["phone"], 0, 3) . "-" . substr($data["phone"], 3),
                "address_1" => $data["shop_address"],
                "address_2" => $data["shop_address"],
                "city" => $data["city_name"],
                "state" => $data["state_name"],
                "country" => $data["country_name"],
                "pincode" => $data["zipcode"],
                "longitude" => $data["longitude"],
                "latitude" => $data["latitude"],
                "gst_no" => ($data["gst"] ?? ""),
                "fssai_code" => ($data["fssai_code"] ?? ""),
            ]);
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://app.shipway.com/api/warehouse/',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $postData,
                CURLOPT_HTTPHEADER => array(
                    $authHeaderString,
                    'Content-Type: application/json'
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            // dd($response);
            if (curl_errno($curl)) {
                $error_msg = curl_error($curl);
                curl_close($curl);
                return ['success' => false, 'message' => $error_msg];
            }
            curl_close($curl);
            $decodedResponse = json_decode($response, true);
            return $decodedResponse;
        }
        return ['success' => false, 'message' => 'data is not available'];
    }

    // Shipway Return Reason API  15/02/2025
    public static function ShipWayGetreturnreasons()
    {
        $token = base64_encode("logistics@mahakal.com:y9NpRj858EEQlAU6Tdp22vLH898TQ695");
        $authHeaderString = 'Authorization: Basic ' . $token;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://app.shipway.com/api/Getreturnreasons',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                $authHeaderString,
                'Content-Type: application/json'
            ),
        ));
        $response = curl_exec($curl);

        curl_close($curl);
        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
            curl_close($curl);
            return ['success' => true, 'error' => $error_msg];
        }
        curl_close($curl);
        $decodedResponse = json_decode($response, true);
        return $decodedResponse;
    }

    // Shipway Generate Reverse Pickup 18/02/2025
    public static function ShipWayGenerateReversePickup($refundOrderId)
    {
        $token = base64_encode("logistics@mahakal.com:y9NpRj858EEQlAU6Tdp22vLH898TQ695");
        $authHeaderString = 'Authorization: Basic ' . $token;

        if (!empty($authHeaderString)) {
            $orderid = RefundRequest::where('order_id', $refundOrderId)->first();
            $orderData = OrderDetail::where('order_id', $refundOrderId)
                ->where('product_id', $orderid['product_id'])
                ->first();
            $orderCarrier = Order::where('id', $refundOrderId)->first();

            if (!$orderData) {
                return ['status' => 404, 'message' => 'Order data not found'];
            }

            $tax = $orderData['tax'];
            $discount = $orderData['discount'];
            $subTotal = ($orderData['price'] * $orderData['qty']) + ($orderData['shipping_cost'] ?? 0);
            $totalItemPrice = $subTotal + $tax - $discount;

            $productDetails = json_decode($orderData['product_details'], true);

            $orderItems = [
                "product" => $productDetails['name'],
                "product_code" => $productDetails['code'],
                "price" => $orderData['price'],
                "discount" => $orderData['discount'],
                "tax_rate" => $orderData['tax'],
                "tax_title" => "",
                "return_reason_id" => $orderid['reason_name'],
                "return_products_images" => $orderid['images'],
                "customer_notes" => $orderid['refund_reason'],
                "variants" => $orderData['variant'],
            ];

            $orderTransfer = [
                "account_number" => "925020008047090",
                "phone" => "9713794786",
                "ifsc_code" => "UTIB0000329",
                "account_type" => "current",
                "beneficiary_name" => "MAHAKAL ASTROTECH (OPC) PVT LTD",
                "bank_name" => "AXIS BANK UJJAIN BRANCH",
            ];
            $qcCheckImage = collect(json_decode($orderid['images']))->map(function ($photo) {
                return getValidImage(path: 'storage/app/public/refund/' . $photo, type: 'backend-basic');
            })->toArray();


            $qcData = [
                "qc_text_capture_label" => "",
                "qc_text_capture_value" => "",
                "value_to_check" => "",
            ];
            // dd($orderData);
            $orderData = Order::where('id', $refundOrderId)->with('orderDetails.product')->first();
            $billingDetail = $orderData['billing_address_data'] ?? '';
            $shippingDetail = $orderData['shipping_address_data'] ?? '';

            if (!$billingDetail || !$shippingDetail) {
                return ['status' => 400, 'message' => 'Billing or shipping details missing'];
            }

            $payment_type = match ($orderData['payment_method']) {
                "razor_pay", "pay_by_wallet" => "P",
                "cash_on_delivery" => "C",
                default => ""
            };
            $refund_payment_id = match ($payment_type) {
                "C" => [1, 2, 3, 4, 5], // COD refunds: Bank Account, UPI, Paytm, Gift Card, Store Credits
                "P" => [4, 5, 6], // Prepaid refunds: Gift Card, Store Credits, Original payment method
                default => []
            };
            $refund_payment_id = $refund_payment_id[0];
            $warehouseId = Seller::where('id', $orderData->seller_id)->value('warehouse_id');
            $getData = [
                "order_id" => $orderData['id'],
                "return_order_status" => "R",
                "carrier_id" => $orderCarrier['delivery_shipment_id'],
                "return_warehouse_id" => $warehouseId,
                "refund_payment_id" => "1",
                "transfer_details" => $orderTransfer,
                "products" => [$orderItems],
                "discount" => $orderData['discount'],
                "shipping" => $orderData['shipping_cost'],
                "order_total" => $totalItemPrice,
                "gift_card_amt" => "0",
                "taxes" => $tax,
                "payment_type" => "C",
                "email" => $billingDetail->email,
                "billing_address" => $billingDetail->address,
                "billing_city" => $billingDetail->city,
                "billing_state" => $billingDetail->state,
                "billing_country" => $billingDetail->country,
                "billing_firstname" => $billingDetail->contact_person_name,
                "billing_phone" => isset($billingDetail->phone) ? explode('+91', $billingDetail->phone)[1] : '',
                "billing_zipcode" => $billingDetail->zip,
                "shipping_address" => $shippingDetail->address,
                "shipping_city" => $shippingDetail->city,
                "shipping_state" => $shippingDetail->state,
                "shipping_country" => $shippingDetail->country,
                "shipping_firstname" => $shippingDetail->contact_person_name,
                "shipping_phone" => isset($shippingDetail->phone) ? explode('+91', $shippingDetail->phone)[1] : '',
                "shipping_zipcode" => $shippingDetail->zip,
                "order_weight" => $orderCarrier['order_weight'],
                "box_length" => $orderCarrier['box_length'],
                "box_breadth" => $orderCarrier['box_breadth'],
                "box_height" => $orderCarrier['box_height'],
                "order_date" => date("Y-m-d H:i", strtotime($orderData['created_at'])),
                "quality_check" => "1",
                "qc_checkurl" => $qcCheckImage,
                "qc_text_capture" => [$qcData],
            ];
            // dd($getData);
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => 'https://app.shipway.com/api/Createreturns',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($getData),  // **Fix: Convert array to JSON**
                CURLOPT_HTTPHEADER => [
                    $authHeaderString,
                    'Content-Type: application/json'
                ],
            ]);

            $response = curl_exec($curl);

            if (curl_errno($curl)) {
                $error_msg = curl_error($curl);
                curl_close($curl);
                return ['success' => false, 'error' => $error_msg];
            }

            curl_close($curl);
            return json_decode($response, true);
        }

        return ['status' => 404, 'message' => 'Order ID is not available'];
    }

    // Shipway Generate Reverse Pickup 18/02/2025

    public static function getPublicIp()
    {
        $clientIp = null;
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $clientIp = trim(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0]);
        } elseif (!empty($_SERVER['HTTP_X_REAL_IP'])) {
            $clientIp = $_SERVER['HTTP_X_REAL_IP'];
        } else {
            $clientIp = $_SERVER['REMOTE_ADDR'];
        }
        if (filter_var($clientIp, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return $clientIp;
        }
        return null;
    }

    public static function getLatLongByIP($ip)
    {
        $url = "https://ipinfo.io/{$ip}/json";
        $response = file_get_contents($url);
        $data = json_decode($response, true);

        if (isset($data['loc'])) {
            list($latitude, $longitude) = explode(',', $data['loc']);
            return [
                'latitude' => $latitude,
                'longitude' => $longitude,
            ];
        } else {
            return [
                'latitude' => 0,
                'longitude' => 0,
            ];
        }
    }


    // Shipway Return Reason API   15/02/2025

    //edit and delete logs store
    public static function editDeleteLogs($module, $subModule, $action)
    {
        $location = [
            'latitude' => 0,
            'longitude' => 0,
        ];
        $auth = auth('admin')->user();

        function getClientIP()
        {
            if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                return $_SERVER['HTTP_CLIENT_IP'];
            } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
            } else {
                return $_SERVER['REMOTE_ADDR'];
            }
        }

        function getLatLong($ip)
        {
            $cacheFile = storage_path("cache/ipinfo_{$ip}.json");

            // Check cache (valid for 1 hour)
            if (file_exists($cacheFile) && time() - filemtime($cacheFile) < 3600) {
                return json_decode(file_get_contents($cacheFile), true);
            }

            $url = "https://ipinfo.io/{$ip}/json?AIzaSyA9WZ75akgvEYdJiPK1UQIpYNhiuStGQhA";
            $response = fetchWithRetry($url);

            //dd($url);

            if ($response && isset($response['loc']) && !empty($response['loc'])) {
                list($latitude, $longitude) = explode(',', $response['loc']);
                $data = [
                    'latitude' => (float) $latitude,
                    'longitude' => (float) $longitude,
                ];
                file_put_contents($cacheFile, json_encode($data));
                return $data;

                // dd($data);
            }

            Log::error("IP Info API Failed", ['ip' => $ip, 'response' => $response]);
            return ['latitude' => 0, 'longitude' => 0];
        }

        function fetchWithRetry($url, $retries = 3)
        {
            while ($retries > 0) {
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 5);
                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($httpCode == 200) {
                    return json_decode($response, true);
                } elseif ($httpCode == 429) {
                    sleep(5);
                }

                $retries--;
            }
            return null;
            dd(fetchWithRetry($url));
        }
        $ip = getClientIP();
        if ($ip != "::1") {
            $location = getLatLong($ip);
            // dd($location);
        }

        Log::info('IP Location Data:', ['ip' => $ip, 'response' => $location]);

        // Store logs
        $logs = new Logs();
        $logs->employee_id = $auth->id ?? null;
        $logs->name = $auth->name ?? null;
        $logs->email = $auth->email ?? null;
        $logs->ip_address = $ip ?? null;
        $logs->latitude = $location['latitude'];
        $logs->longitude = $location['longitude'];
        $logs->module = $module;
        $logs->sub_module = $subModule;
        $logs->action = $action;
        $logs->save();
    }



    public static function modules_check($module)
    {
        $user_role = auth('admin')->user()->role;
        $permissionRoles = PermissionRole::where('role_id', $user_role['id'])->get();
        if (isset($permissionRoles) && $user_role->status == 1) {
            foreach ($permissionRoles as $role) {
                if ($role['module'] == $module) {
                    return true;
                }
            }
        }
        if (auth('admin')->user()->admin_role_id == 1) {
            return true;
        }
        return false;
    }

    public static function modules_submodule_check($module, $subModule)
    {
        $user_role = auth('admin')->user()->role;
        $permissionRoles = PermissionRole::where('role_id', $user_role['id'])->get();
        if (isset($permissionRoles) && $user_role->status == 1) {
            foreach ($permissionRoles as $role) {
                if ($role['module'] == $module && $role['sub_module'] == $subModule) {
                    return true;
                }
            }
        }
        if (auth('admin')->user()->admin_role_id == 1) {
            return true;
        }
        return false;
    }

    public static function modules_permission_check($module, $subModule, $permission)
    {
        $user_role = auth('admin')->user()->role;
        $permissionRoles = PermissionRole::where('role_id', $user_role['id'])->get();
        if (isset($permissionRoles) && $user_role->status == 1) {
            foreach ($permissionRoles as $role) {
                $rolePermissions = json_decode($role['permission'], true);
                if ($role['module'] == $module && $role['sub_module'] == $subModule && in_array($permission, $rolePermissions)) {
                    return true;
                }
            }
        }
        if (auth('admin')->user()->admin_role_id == 1) {
            return true;
        }
        return false;
    }

    public static function employeemodules_check($module)
    {
        if (auth('event')->check() || auth('tour')->check() || auth('trust')->check()  || auth('purohit')->check()) {
            return true;
        } elseif (auth('event_employee')->check()) {
            $user_role = auth('event_employee')->user()->emp_role_id;
        } elseif (auth('tour_employee')->check()) {
            $user_role = auth('tour_employee')->user()->emp_role_id;
        } elseif (auth('trust_employee')->check()) {
            $user_role = auth('trust_employee')->user()->emp_role_id;
        }
        $RolesInfo = VendorRoles::where('id', $user_role)->where('status', 1)->first();
        if (!$RolesInfo) {
            return false;
        }
        $permissionRoles = VendorPermissionRole::where('role_id', $user_role)->where('module', trim($module))->first();
        if (!empty($permissionRoles)) {
            return true;
        }
        return false;
    }

    public static function Employee_modules_permission($module, $subModule, $permission)
    {
        if (auth('event')->check() || auth('tour')->check() || auth('trust')->check() || auth('purohit')->check()) {
            return true;
        } elseif (auth('event_employee')->check()) {
            $user_role = auth('event_employee')->user()->emp_role_id;
        } elseif (auth('tour_employee')->check()) {
            $user_role = auth('tour_employee')->user()->emp_role_id;
        } elseif (auth('trust_employee')->check()) {
            $user_role = auth('trust_employee')->user()->emp_role_id;
        }
        $RolesInfo = VendorRoles::where('id', $user_role)->where('status', 1)->first();
        if (!$RolesInfo) {
            return false;
        }
        $permissionRoles = VendorPermissionRole::where('role_id', $user_role)->get();
        if (isset($permissionRoles) && $permissionRoles) {
            foreach ($permissionRoles as $role) {
                $rolePermissions = json_decode($role['permission'], true);
                if ($role['module'] == $module && $role['sub_module'] == $subModule && in_array($permission, $rolePermissions)) {
                    return true;
                }
            }
        }
        return false;
    }

    public static function convert_currency_to_usd($price)
    {
        $currency_model = Helpers::get_business_settings('currency_model');
        if ($currency_model == 'multi_currency') {
            Helpers::currency_load();
            $code = session('currency_code') == null ? 'USD' : session('currency_code');
            if ($code == 'USD') {
                return $price;
            }
            $currency = Currency::where('code', $code)->first();
            $price = floatval($price) / floatval($currency->exchange_rate);

            $usd_currency = Currency::where('code', 'USD')->first();
            $price = $usd_currency->exchange_rate < 1 ? (floatval($price) * floatval($usd_currency->exchange_rate)) : (floatval($price) / floatval($usd_currency->exchange_rate));
        } else {
            $price = floatval($price);
        }

        return $price;
    }

    public static function convert_manual_currency_to_usd($price, $currency = null)
    {
        $currency_model = Helpers::get_business_settings('currency_model');
        if ($currency_model == 'multi_currency') {
            $code = $currency == null ? 'USD' : $currency;
            if ($code == 'USD') {
                return $price;
            }
            $currency = Currency::where('code', $code)->first();
            $price = floatval($price) / floatval($currency->exchange_rate);

            $usd_currency = Currency::where('code', 'USD')->first();
            $price = $usd_currency->exchange_rate < 1 ? (floatval($price) * floatval($usd_currency->exchange_rate)) : (floatval($price) / floatval($usd_currency->exchange_rate));
        } else {
            $price = floatval($price);
        }

        return $price;
    }

    /** push notification order related  */
    public static function send_order_notification($key, $type, $order)
    {
        try {
            $lang = self::default_lang();

            /** for customer  */
            if ($type == 'customer') {
                $fcm_token = $order->customer?->cm_firebase_token;
                $lang = $order->customer?->app_language ?? $lang;
                $value = Helpers::push_notificatoin_message($key, 'customer', $lang);
                $value = Helpers::text_variable_data_format(value: $value, key: $key, shopName: $order->seller?->shop?->name, order_id: $order->id, user_name: "{$order->customer?->f_name} {$order->customer?->l_name}", delivery_man_name: "{$order->delivery_man?->f_name} {$order->delivery_man?->l_name}", time: now()->diffForHumans());
                if (!empty($fcm_token) || $value) {
                    $data = [
                        'title' => translate('order'),
                        'description' => $value,
                        'order_id' => $order['id'],
                        'image' => '',
                        'type' => 'order'
                    ];
                    Helpers::send_push_notif_to_device($fcm_token, $data);
                }
            }
            /** end for customer  */
            /**for seller */
            if ($type == 'seller') {
                $seller_fcm_token = $order->seller?->cm_firebase_token;
                if (!empty($seller_fcm_token)) {
                    $lang = $order->seller?->app_language ?? $lang;
                    $value_seller = Helpers::push_notificatoin_message($key, 'seller', $lang);
                    $value_seller = Helpers::text_variable_data_format(value: $value_seller, key: $key, shopName: $order->seller?->shop?->name, order_id: $order->id, user_name: "{$order->customer?->f_name} {$order->customer?->l_name}", delivery_man_name: "{$order->delivery_man?->f_name} {$order->delivery_man?->l_name}", time: now()->diffForHumans());

                    if ($value_seller != null) {
                        $data = [
                            'title' => translate('order'),
                            'description' => $value_seller,
                            'order_id' => $order['id'],
                            'image' => '',
                            'type' => 'order'
                        ];
                        Helpers::send_push_notif_to_device($seller_fcm_token, $data);
                    }
                }
            }
            /**end for seller */
            /** for delivery man*/
            if ($type == 'delivery_man') {
                $fcm_token_delivery_man = $order->delivery_man?->fcm_token;
                $lang = $order->delivery_man?->app_language ?? $lang;
                $value_delivery_man = Helpers::push_notificatoin_message($key, 'delivery_man', $lang);
                $value_delivery_man = Helpers::text_variable_data_format(value: $value_delivery_man, key: $key, shopName: $order->seller?->shop?->name, order_id: $order->id, user_name: "{$order->customer?->f_name} {$order->customer?->l_name}", delivery_man_name: "{$order->delivery_man?->f_name} {$order->delivery_man?->l_name}", time: now()->diffForHumans());
                $data = [
                    'title' => translate('order'),
                    'description' => $value_delivery_man,
                    'order_id' => $order['id'],
                    'image' => '',
                    'type' => 'order'
                ];
                if ($order->delivery_man_id) {
                    self::add_deliveryman_push_notification($data, $order->delivery_man_id);
                }
                if ($fcm_token_delivery_man) {
                    Helpers::send_push_notif_to_device($fcm_token_delivery_man, $data);
                }
            }

            /** end delivery man*/
        } catch (\Exception $e) {
        }
    }
    /** end push notification to seller  */

    /** push notification variable message formate  */
    public static function text_variable_data_format($value, $key = null, $user_name = null, $shopName = null, $delivery_man_name = null, $time = null, $order_id = null)
    {
        $data =  $value;
        if ($data) {
            $order = $order_id ? Order::find($order_id) : null;
            $data =  $user_name ? str_replace("{userName}", $user_name, $data) : $data;
            $data =  $shopName ? str_replace("{shopName}", $shopName, $data) : $data;
            $data =  $delivery_man_name ? str_replace("{deliveryManName}", $delivery_man_name, $data) : $data;
            $data =  $key == 'expected_delivery_date' ? ($order ? str_replace("{time}", $order->expected_delivery_date, $data) : $data) : ($time ? str_replace("{time}", $time, $data) : $data);
            $data =  $order_id ? str_replace("{orderId}", $order_id, $data) : $data;
        }
        return $data;
    }
    /* end **/
    public static function push_notificatoin_message($key, $user_type, $lang)
    {
        try {
            $notification_key = [
                'pending'   => 'order_pending_message',
                'confirmed' => 'order_confirmation_message',
                'processing' => 'order_processing_message',
                'out_for_delivery' => 'out_for_delivery_message',
                'delivered' => 'order_delivered_message',
                'returned'  => 'order_returned_message',
                'failed'    => 'order_failed_message',
                'canceled'  => 'order_canceled',
                'order_refunded_message'    => 'order_refunded_message',
                'refund_request_canceled_message'   => 'refund_request_canceled_message',
                'new_order_message' => 'new_order_message',
                'order_edit_message' => 'order_edit_message',
                'new_order_assigned_message' => 'new_order_assigned_message',
                'delivery_man_assign_by_admin_message' => 'delivery_man_assign_by_admin_message',
                'order_rescheduled_message' => 'order_rescheduled_message',
                'expected_delivery_date' => 'expected_delivery_date',
                'message_from_admin' => 'message_from_admin',
                'message_from_seller' => 'message_from_seller',
                'message_from_delivery_man' => 'message_from_delivery_man',
                'message_from_customer' => 'message_from_customer',
                'refund_request_status_changed_by_admin' => 'refund_request_status_changed_by_admin',
                'withdraw_request_status_message' => 'withdraw_request_status_message',
                'cash_collect_by_seller_message' => 'cash_collect_by_seller_message',
                'cash_collect_by_admin_message' => 'cash_collect_by_admin_message',
                'fund_added_by_admin_message' => 'fund_added_by_admin_message',
                'delivery_man_charge' => 'delivery_man_charge',
            ];
            $data = NotificationMessage::with(['translations' => function ($query) use ($lang) {
                $query->where('locale', $lang);
            }])->where(['key' => $notification_key[$key], 'user_type' => $user_type])->first() ?? ["status" => 0, "message" => "", "translations" => []];
            if ($data) {
                if ($data['status'] == 0) {
                    return 0;
                }
                return count($data->translations) > 0 ? $data->translations[0]->value : $data['message'];
            } else {
                return false;
            }
        } catch (\Exception $exception) {
        }
    }

    /** chatting related push notification */
    public static function chatting_notification($key, $type, $user_data, $message_form = null)
    {
        try {
            $fcm_token = $type == 'delivery_man' ? $user_data?->fcm_token : $user_data?->cm_firebase_token;
            if ($fcm_token) {
                $lang = $user_data?->app_language ?? self::default_lang();
                $value = Helpers::push_notificatoin_message($key, $type, $lang);

                $value = Helpers::text_variable_data_format(
                    value: $value,
                    key: $key,
                    shopName: $message_form?->shop?->name,
                    user_name: "{$message_form?->f_name} {$message_form?->l_name}",
                    delivery_man_name: "{$message_form?->f_name} {$message_form?->l_name}",
                    time: now()->diffForHumans()
                );
                $data = [
                    'title' => translate('message'),
                    'description' => $value,
                    'order_id' => '',
                    'image' => '',
                    'type' => 'chatting'
                ];
                Helpers::send_push_notif_to_device($fcm_token, $data);
            }
        } catch (\Exception $exception) {
        }
    }
    /** end chatting related push notification */

    /**
     * Device wise notification send
     */

    public static function send_push_notif_to_device($fcm_token, $data)
    {
        $key = BusinessSetting::where(['type' => 'push_notification_key'])->first()->value;
        $url = "https://fcm.googleapis.com/fcm/send";
        $header = array(
            "authorization: key=" . $key . "",
            "content-type: application/json"
        );

        if (isset($data['order_id']) == false) {
            $data['order_id'] = null;
        }

        $postdata = '{
            "to" : "' . $fcm_token . '",
            "data" : {
                "title" :"' . $data['title'] . '",
                "body" : "' . $data['description'] . '",
                "image" : "' . $data['image'] . '",
                "order_id":"' . $data['order_id'] . '",
                "type":"' . $data['type'] . '",
                "is_read": 0
              },
              "notification" : {
                "title" :"' . $data['title'] . '",
                "body" : "' . $data['description'] . '",
                "image" : "' . $data['image'] . '",
                "order_id":"' . $data['order_id'] . '",
                "title_loc_key":"' . $data['order_id'] . '",
                "type":"' . $data['type'] . '",
                "is_read": 0,
                "icon" : "new",
                "sound" : "default"
              }
        }';

        $ch = curl_init();
        $timeout = 120;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        // Get URL content
        $result = curl_exec($ch);
        // close handle to release resources
        curl_close($ch);

        return $result;
    }

    public static function send_push_notif_to_device1($fcm_token, $data)
    {
        if (empty($data['title']) || empty($data['description'])) {
            return true;
        }
        // Path to your Firebase service account JSON file
        $serviceAccountPath = base_path(env('FIREBASE_CREDENTIALS'));
        $projectId = 'manalsoftech-6807e'; // Replace with your Firebase project ID

        // Generate an OAuth2 token
        $credentials = new \Google\Auth\Credentials\ServiceAccountCredentials(
            'https://www.googleapis.com/auth/firebase.messaging',
            json_decode(file_get_contents($serviceAccountPath), true)
        );
        $httpHandler = \Google\Auth\HttpHandler\HttpHandlerFactory::build();
        $authToken = $credentials->fetchAuthToken($httpHandler);
        $accessToken = $authToken['access_token'];

        // FCM v1 endpoint
        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

        // Prepare the payload
        $message = [
            'message' => [
                'token' => $fcm_token,
                'notification' => [
                    'title' => $data['title'] ?? 'Notification',
                    'body' => $data['description'] ?? 'Description',
                    'image' => $data['image'] ?? null,
                ],
                'data' => [
                    'order_id' => (string) ($data['order_id'] ?? ''),
                    'type' => (string) ($data['type'] ?? 'default'),
                    'is_read' => '0',
                ],
                // 'webpush' => [
                //     'fcm_options' => [
                //         'link' => route('tour.index'),
                //     ],
                // ],
            ],
        ];
        if (!empty($data['link'])) {
            $message['message']['webpush']['fcm_options']['link'] = $data['link'];
        }
        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $accessToken,
            ]);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($message));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if ($response === false) {
                throw new \Exception("cURL error: " . curl_error($ch));
            }
            curl_close($ch);
            if ($httpCode === 200) {
                return "Notification sent successfully: " . $response;
            } else {
                return "Failed to send notification. HTTP Code: {$httpCode}. Response: {$response}";
            }
        } catch (\Exception $e) {
            // Handle errors
            return [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Sends a WhatsApp message based on the provided model, type, and data.
     *
     * @param string $modelName The name of the model to use for fetching the WhatsApp template (e.g., 'event', 'tour', 'donate').
     * @param string $type The type of message template to search for (e.g., 'order_placed', 'booking_confirmation').
     * @param array $data An associative array containing the dynamic data to replace in the template.
     * 
     * Keys in $data:
     * - 'title_name' (string): The title of the event, tour, or donation.
     * - 'final_amount' (float): The amount for the transaction.
     * - 'orderId' (string): The ID of the order.
     * - 'customer_id' (int): The ID of the customer.
     * - 'booking_date' (string): The booking date (format: Y-m-d).
     * - 'place_name' (string): The name of the event or tour venue.
     * - 'time' (string): The time of the event or booking.
     * - 'number' (string): Any additional number or reference.
     * 
     * @throws \Exception If no templates are found for the given model.
     * 
     * @return void Sends the WhatsApp message using the selected device and user phone number.
     */
    public static function whatsappMessage($modelName, $type, $data)
    {
        $whatsapp = [];
        switch ($modelName) {
            case 'event':
                $model = \App\Models\WEventTemplate::class;
                if ($type == 'Event booking Confirmed') {
                    Helpers::AddReviewsComman($modelName, $data);
                }
                Helpers::TemplateTextEmail('tour', $type, $data);
                break;
            case 'tour':
                $model = \App\Models\WToursTemplate::class;
                if ($type == 'Tour booking Confirmed') {
                    Helpers::tourOrdervendorBookingMessage($data['orderId']);
                    Helpers::AddReviewsComman($modelName, $data);
                }
                Helpers::CommanToastrMessage($type, $data);
                Helpers::TemplateTextEmail('tour', $type, $data);
                break;
            case 'kundali':
                $model = \App\Models\WKundaliTemplate::class;
                Helpers::TemplateTextEmail('tour', $type, $data);
                break;
            case 'donate':
                $model = \App\Models\WDonationTemplate::class;
                Helpers::TemplateTextEmail('tour', $type, $data);
                break;
            case 'ecom':
                $model = \App\Models\WEcomTemplate::class;
                break;
            case 'offlinepooja':
                $model = \App\Models\WOfflinePoojaTemplate::class;
                break;
            case 'whatsapp':
                $model = \App\Models\WhatsappTemplate::class;
                break;
            case 'consultancy':
                $model = \App\Models\WConsultancyTemplate::class;
                break;
            case 'chadhava':
                $model = \App\Models\WChadhavaTemplate::class;
                break;
            case 'vipanushthan':
                $model = \App\Models\WVIPAnushthanTemplate::class;
                break;
            case 'vipdarshan':
                $model = \App\Models\WTempleDarshanTemplate::class;
                Helpers::TemplateTextEmail('tour', $type, $data);
                break;
            case 'temple':
                $model = \App\Models\WTempleDarshanTemplate::class;
                Helpers::TemplateTextEmail('tour', $type, $data);
                break;
            case 'default':
            default:
                $model = '';
                break;
        }
        $order_data = $model::where('status', 1)->get();
        $response = '';
        if ($order_data->isEmpty()) {
            $response = 'No templates found for the given model:' . $modelName;
        }
        $whatsapp = $order_data->toArray();
        $orderPlaced = array_search($type, array_column($whatsapp, 'template_name'));
        if (!empty($order_data)) {
            $wpMsg = $whatsapp[$orderPlaced]['body'];
            $wpMsg = str_replace('{title_name}', ($data['title_name'] ?? "__"), $wpMsg);
            $wpMsg = str_replace('{temple_name}', ($data['temple_name'] ?? "__"), $wpMsg);
            $wpMsg = str_replace('{service_name}', ($data['service_name'] ?? "__"), $wpMsg);

            $wpMsg = str_replace('{amount}', ($data['final_amount'] ?? 0.00), $wpMsg);
            $wpMsg = str_replace('{refund_amount}', ($data['refund_amount'] ?? 0.00), $wpMsg);
            $wpMsg = str_replace('{product_name}', ($data['product_name'] ?? "__"), $wpMsg);
            $wpMsg = str_replace('{order_amount}', ($data['order_amount'] ?? 0.00), $wpMsg);
            $wpMsg = str_replace('{remain_amount}', ($data['remain_amount'] ?? 0.00), $wpMsg);
            $wpMsg = str_replace('{refund_date}', ($data['refund_date'] ?? ''), $wpMsg);
            $wpMsg = str_replace('{schedule_amount}', ($data['schedule_amount'] ?? 0.00), $wpMsg);
            $wpMsg = str_replace('{transaction_id}', ($data['transaction_id'] ?? 0.00), $wpMsg);
            $wpMsg = str_replace('{kundli_type}', ($data['kundli_type'] ?? " "), $wpMsg);
            $wpMsg = str_replace('{kundli_page}', ($data['kundli_page'] ?? " "), $wpMsg);
            $wpMsg = str_replace('{trust_name}', ($data['trust_name'] ?? " "), $wpMsg);
            $wpMsg = str_replace('{order_id}', ($data['orderId'] ?? "__"), $wpMsg);
            $wpMsg = str_replace('{payment_link}', ($data['payment_link'] ?? "__"), $wpMsg);
            if (!empty($data['seller_id'])) {
                $userInfo = \App\Models\Seller::where('id', $data['seller_id'])->first();
            } elseif (!empty($data['admin_phone'])) {
                $userInfo = ['phone' => $data['admin_phone'], 'name' => $data['admin_name']];
            } elseif (!empty($data['pandit_mobile'])) {
                $phone = $data['pandit_mobile'];
                $phone = trim($phone);
                if (!preg_match('/^\+91/', $phone)) {
                    $phone = '+91' . $phone;
                }
                $userInfo = ['phone' => $phone];
            } else {
                $userInfo = \App\Models\User::where('id', $data['customer_id'] ?? "")->first();
            }
            $wpMsg = str_replace('{user_name}', ($userInfo['name'] ?? "__"), $wpMsg);
            $wpMsg = str_replace('{booking_date}', ($data['booking_date'] ?? '__'), $wpMsg);
            $wpMsg = str_replace('{ended_at}', ($data['ended_at'] ?? '__'), $wpMsg);
            $wpMsg = str_replace('{frequency}', ($data['frequency'] ?? '__'), $wpMsg);
            $wpMsg = str_replace('{paid_count}', ($data['paid_count'] ?? '__'), $wpMsg);
            $wpMsg = str_replace('{remaining_count}', ($data['remaining_count'] ?? '__'), $wpMsg);
            $wpMsg = str_replace('{member_names}', ($data['member_names'] ?? ""), $wpMsg);
            $wpMsg = str_replace('{gotra}', ($data['gotra'] ?? ""), $wpMsg);
            $wpMsg = str_replace('{prashad}', ($data['prashad'] ?? ""), $wpMsg);
            $wpMsg = str_replace('{ad_name}', ($data['ad_name'] ?? "__"), $wpMsg);
            $wpMsg = str_replace('{pan_card}', ($data['pan_card'] ?? "__"), $wpMsg);
            $wpMsg = str_replace('{otp}', ($data['otp'] ?? "__"), $wpMsg);
            $wpMsg = str_replace('{driver_name}', ($data['driver_name'] ?? ""), $wpMsg);
            $wpMsg = str_replace('{driver_number}', ($data['driver_number'] ?? "__"), $wpMsg);
            $wpMsg = str_replace('{vehicle_name}', ($data['vehicle_name'] ?? "__"), $wpMsg);
            $wpMsg = str_replace('{vehicle_number}', ($data['vehicle_number'] ?? "__"), $wpMsg);

            $wpMsg = str_replace('{place_name}', ($data['place_name'] ?? "__"), $wpMsg);
            if (empty($data['puja'])) {
                $pujaMessage = '';
            } elseif (!empty($data['puja']) && is_array($data['puja'])) {
                $pujaMessage = collect($data['puja'])
                    ->map(function ($value, $key) {
                        return is_int($key) ? $value : "$key: $value";
                    })
                    ->implode("\n");
            } else {
                $pujaMessage = $data['puja'];
            }
            $wpMsg = str_replace('{puja}', $pujaMessage, $wpMsg);

            if (empty($data['darshan'])) {
                $darshanMessage = '';
            } elseif (!empty($data['darshan']) && is_array($data['darshan'])) {
                $darshanMessage = collect($data['darshan'])
                    ->map(function ($value, $key) {
                        return is_int($key) ? $value : "$key: $value";
                    })
                    ->implode("\n");
            } else {
                $darshanMessage = $data['darshan'];
            }
            $wpMsg = str_replace('{darshan}', $darshanMessage, $wpMsg);

            if (empty($data['bhojan'])) {
                $bhojanMessage = '';
            } elseif (!empty($data['bhojan']) && is_array($data['bhojan'])) {
                $bhojanMessage = collect($data['bhojan'])
                    ->map(function ($value, $key) {
                        return is_int($key) ? $value : "$key: $value";
                    })
                    ->implode("\n");
            } else {
                $bhojanMessage = $data['bhojan'];
            }
            $wpMsg = str_replace('{bhojan}', $bhojanMessage, $wpMsg);

            if (empty($data['locker'])) {
                $lockerMessage = '';
            } elseif (!empty($data['locker']) && is_array($data['locker'])) {
                $lockerMessage = collect($data['locker'])
                    ->map(function ($value, $key) {
                        return is_int($key) ? $value : "$key: $value";
                    })
                    ->implode("\n");
            } else {
                $lockerMessage = $data['locker'];
            }
            $wpMsg = str_replace('{locker}', $lockerMessage, $wpMsg);

            // $wpMsg = str_replace('{puja}', ($data['puja'] ?? ""), $wpMsg);
            $wpMsg = str_replace('{chadhava_venue}', ($data['chadhava_venue'] ?? "__"), $wpMsg);

            $wpMsg = str_replace('{puja_venue}', ($data['puja_venue'] ?? "__"), $wpMsg);
            $wpMsg = str_replace('{venue_address}', ($data['venue_address'] ?? "__"), $wpMsg);
            $wpMsg = str_replace('{tracking}', ($data['tracking'] ?? "__"), $wpMsg);
            $wpMsg = str_replace('{landmark}', ($data['landmark'] ?? "__"), $wpMsg);
            $wpMsg = str_replace('{pandit_name}', ($data['pandit_name'] ?? ""), $wpMsg);
            $wpMsg = str_replace('{reject_reason}', ($data['reject_reason'] ?? ""), $wpMsg);
            $wpMsg = str_replace('{scheduled_time}', ($data['scheduled_time'] ?? "__"), $wpMsg);
            $wpMsg = str_replace('{live_stream}', ($data['live_stream'] ?? "mahakal.com"), $wpMsg);
            $wpMsg = str_replace('{share_video}', ($data['share_video'] ?? "mahakal.com"), $wpMsg);
            $wpMsg = str_replace('{certificate_link}', ($data['certificate_link'] ?? "mahakal.com"), $wpMsg);
            $wpMsg = str_replace('{counselling_report}', ($data['counselling_report'] ?? "mahakal.com"), $wpMsg);
            $wpMsg = str_replace('{order_canceled_reason}', ($data['order_canceled_reason'] ?? "mahakal.com"), $wpMsg);
            $wpMsg = str_replace('{link}', ($data['link'] ?? "mahakal.com/all-puja"), $wpMsg);
            if (!empty($data['time'])) {
                $wpMsg = str_replace('{time}', (date('h:i A', strtotime($data['time'])) ?? "__"), $wpMsg);
            }
            $wpMsg = str_replace('{country}', ($data['country'] ?? "__"), $wpMsg);
            $wpMsg = str_replace('{name}', ($data['name'] ?? "__"), $wpMsg);
            $wpMsg = str_replace('{city}', ($data['city'] ?? "__"), $wpMsg);
            $wpMsg = str_replace('{dob}', ($data['dob'] ?? "__"), $wpMsg);
            $wpMsg = str_replace('{number}', ($data['number'] ?? "__"), $wpMsg);
            $wpMsg = str_replace('{attachment}', ($data['attachment'] ?? "__"), $wpMsg);
            // $wpMsg = str_replace('{buttons}', ($data['buttons'] ?? "__"), $wpMsg);
            $wpMsg = str_replace('{shop_name}', ($data['shop_name'] ?? "__"), $wpMsg);
            $wpMsg = str_replace('{vendor_name}', ($data['vendor_name'] ?? "__"), $wpMsg);
            $wpMsg = str_replace('{admin_commission}', ($data['admin_commission'] ?? "__"), $wpMsg);
            $wpMsg = str_replace('{discount}', ($data['discount'] ?? "__"), $wpMsg);
            $wpMsg = str_replace('{rprice}', ($data['rprice'] ?? "__"), $wpMsg);
            $wpMsg = str_replace('{tax_amount}', ($data['tax_amount'] ?? "__"), $wpMsg);
            $wpMsg = str_replace('{paymant_model}', ($data['paymant_model'] ?? "__"), $wpMsg);
            $device = Admin::where('id', 1)->where('status', 1)->first();

            if ($device) {
                $text = \App\Traits\Whatsapp::formatText($wpMsg);
                if (!empty($data['attachment'])) {
                    $text = $text ?? ''; // Ensure $text is defined
                    $body["caption"] = $text;
                } else {
                    $body["text"] = $text; // Fallback for undefined $text
                }
                // Determine file type and extension
                if (!empty($data['attachment'])) {
                    $explode = explode('.', $data['attachment']);
                    $file_type = strtolower(end($explode));

                    $extensions = [
                        'jpg' => 'image',
                        'jpeg' => 'image',
                        'png' => 'image',
                        'webp' => 'image',
                        'pdf' => 'document',
                        'docx' => 'document',
                        'xlsx' => 'document',
                        'csv' => 'document',
                        'txt' => 'document'
                    ];

                    if (array_key_exists($file_type, $extensions)) {
                        $body[$extensions[$file_type]] = ['url' => $data['attachment']];
                    }
                }

                $type = isset($data['type']) ? $data['type'] : "plain-text";
                if (isset($data['type']) && $data['type'] == 'text-with-media') {
                    $body['attachment'] = $data['attachment'];
                }
                // dd($body);
                $response = \App\Traits\Whatsapp::messageSend(
                    $body,
                    'device_mahakal_2024',
                    ($userInfo['phone'] ?? ""),
                    $type,
                    true
                );
            } else {
                $response = 'device not';
            }
        }

        return $response;
    }
    //astrologer Online app
    public static function getOnlineAstrologers()
    {
        $baseUrl = env('ASTRO_BASE_URL'); // http://89.116.32.44:3001
        $url = $baseUrl . '/api/astrologers/online/astrologers';
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ]);
        $response = curl_exec($curl);
        // handle curl error
        if (curl_errno($curl)) {
            $error = curl_error($curl);
            curl_close($curl);
            return [
                'success' => false,
                'error' => $error
            ];
        }

        curl_close($curl);

        $decoded = json_decode($response, true);

        return [
            'success' => true,
            'data' => $decoded
        ];
    }
    //astro_livestream
    public static function LivestreamAstrologers()
    {
        $baseUrl = env('ASTRO_BASE_URL'); // http://89.116.32.44:3001
        $url = $baseUrl . '/api/live-stream/active';
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ]);
        $response = curl_exec($curl);
        // handle curl error
        if (curl_errno($curl)) {
            $error = curl_error($curl);
            curl_close($curl);
            return [
                'success' => false,
                'error' => $error
            ];
        }

        curl_close($curl);

        $decoded = json_decode($response, true);

        return [
            'success' => true,
            'data' => $decoded
        ];
    }

    public static function tourOrdervendorBookingMessage($order_id)
    {
        $getorders = \App\Models\TourOrder::where('order_id', $order_id)->with(['acceptss' => function ($query) {
            $query->where('status', 1);
        }, 'acceptss.TourTraveller'])->first();
        $web_config  = \App\Models\BusinessSetting::where('type', 'company_fav_icon')->first();
        if (!empty($getorders) && $getorders['acceptss']) {
            foreach ($getorders['acceptss'] as $key => $value) {
                $token = \App\Models\Seller::where('relation_id', $value['TourTraveller']['id'])->first()['cm_firebase_token'] ?? '';
                $data = [
                    "title" => "Tour Booking",
                    "description" => "Check out the latest Trip Booking Now! Click Here to view",
                    "image" => theme_asset(path: 'storage/app/public/company') . '/' . $web_config['value'],
                    "order_id" => $order_id,
                    "type" => "tour",
                    "link" => route('tour-vendor.order.pending'),
                ];
                if ($token) {
                    Helpers::send_push_notif_to_device1($token, $data);
                }
            }
        }
    }

    public static function whatsappMessageVendorSend($modelName, $type, $data)
    {
        $whatsapp = [];
        $Phone_numbers = '';
        switch ($modelName) {
            case 'event':
                $model = \App\Models\WEventTemplate::class;
                $Phone_numbers = '';
                break;
            case 'tour':
                $model = \App\Models\WToursTemplate::class;
                $Phone_numbers = $data['driver_number'];
                break;
            case 'donate':
                $model = \App\Models\WDonationTemplate::class;
                $Phone_numbers = '';
                break;
            case 'default':
            default:
                $model = '';
                break;
        }
        $order_data = $model::where('status', 1)->get();
        $response = '';
        if ($order_data->isEmpty()) {
            $response = 'No templates found for the given model:' . $modelName;
        }
        $whatsapp = $order_data->toArray();
        $orderPlaced = array_search($type, array_column($whatsapp, 'template_name'));
        if (!empty($order_data)) {
            $wpMsg = $whatsapp[$orderPlaced]['body'];
            $userInfo = \App\Models\User::where('id', ($data['customer_id'] ?? ""))->first();
            
            $wpMsg = str_replace('{title_name}', ($data['title_name'] ?? "__"), $wpMsg);
            $wpMsg = str_replace('{amount}', ($data['final_amount'] ?? 0.00), $wpMsg);
            $wpMsg = str_replace('{product_name}', ($data['product_name'] ?? "__"), $wpMsg);
            $wpMsg = str_replace('{order_amount}', ($data['order_amount'] ?? 0.00), $wpMsg);
            $wpMsg = str_replace('{remain_amount}', ($data['remain_amount'] ?? 0.00), $wpMsg);
            $wpMsg = str_replace('{order_id}', ($data['orderId'] ?? "__"), $wpMsg);
            $wpMsg = str_replace('{user_name}', ($userInfo['name'] ?? "__"), $wpMsg);
            $wpMsg = str_replace('{booking_date}', ($data['booking_date'] ?? '__'), $wpMsg);
            $wpMsg = str_replace('{start_date}', ($data['start_date'] ?? '__'), $wpMsg);
            $wpMsg = str_replace('{end_date}', ($data['end_date'] ?? '__'), $wpMsg);
            $wpMsg = str_replace('{ad_name}', ($data['ad_name'] ?? "__"), $wpMsg);
            $wpMsg = str_replace('{pan_card}', ($data['pan_card'] ?? "__"), $wpMsg);
            $wpMsg = str_replace('{driver_name}', ($data['driver_name'] ?? ""), $wpMsg);
            $wpMsg = str_replace('{driver_number}', ($data['driver_number'] ?? "__"), $wpMsg);
            $wpMsg = str_replace('{vehicle_name}', ($data['vehicle_name'] ?? "__"), $wpMsg);
            $wpMsg = str_replace('{vehicle_number}', ($data['vehicle_number'] ?? "__"), $wpMsg);
            $wpMsg = str_replace('{place_name}', ($data['place_name'] ?? "__"), $wpMsg);
            $wpMsg = str_replace('{time}', ($data['time'] ?? "__"), $wpMsg);
            $wpMsg = str_replace('{country}', ($data['country'] ?? "__"), $wpMsg);
            $wpMsg = str_replace('{city}', ($data['city'] ?? "__"), $wpMsg);
            $wpMsg = str_replace('{dob}', ($data['dob'] ?? "__"), $wpMsg);
            $wpMsg = str_replace('{number}', ($data['number'] ?? "__"), $wpMsg);
            $wpMsg = str_replace('{attachment}', ($data['attachment'] ?? ""), $wpMsg);
            $device = Admin::where('id', 1)->where('status', 1)->first();

            if ($device) {
                $text = \App\Traits\Whatsapp::formatText($wpMsg);
                if (!empty($data['attachment'])) {
                    $text = $text ?? '';
                    $body["caption"] = $text;
                } else {
                    $body["text"] = $text;
                }
                if (!empty($data['attachment'])) {
                    $explode = explode('.', $data['attachment']);
                    $file_type = strtolower(end($explode));
                    $extensions = [
                        'jpg' => 'image',
                        'jpeg' => 'image',
                        'png' => 'image',
                        'webp' => 'image',
                        'pdf' => 'document',
                        'docx' => 'document',
                        'xlsx' => 'document',
                        'csv' => 'document',
                        'txt' => 'document'
                    ];
                    if (array_key_exists($file_type, $extensions)) {
                        $body[$extensions[$file_type]] = ['url' => $data['attachment']];
                    }
                }
                $type = isset($data['type']) ? $data['type'] : "plain-text";
                if (isset($data['type']) && $data['type'] == 'text-with-media') {
                    $body['attachment'] = $data['attachment'];
                }
                $response = \App\Traits\Whatsapp::messageSend(
                    $body,
                    'device_mahakal_2024',
                    $Phone_numbers,
                    $type,
                    true
                );
            } else {
                $response = 'device not';
            }
        }

        return $response;
    }

    public static function AddReviewsComman($type, $data)
    {
        $feedbackExists = \App\Models\UserFeedback::where('user_id', ($data['customer_id'] ?? ""))->exists();
        if (!$feedbackExists) {
            \App\Models\UserFeedback::create([
                'user_id' => ($data['customer_id'] ?? ""),
                'message' => \App\Utils\getRandomFeedbackMessage(),
            ]);
        }
        if ($type == 'tour') {
            $getorders = \App\Models\TourOrder::where('order_id', $data['orderId'])->first();
            $reviews = [
                "Well-planned module hai. User reviews aur customizable pilgrimage packages add karne se aur helpful ho sakta hai.",
                "Yatra booking kaafi smooth hai. Mandir darshan aur dharmik sthal visit ke liye well-organized plans available hain, jo safar ko hassle-free banate hain.",
                "Pilgrimage tours ki details bahut hi clearly di gayi hain. Har itinerary well-structured hai, aur booking process bhi fast aur convenient hai.",
                "Bahut hi accha feature hai, jo dharmik yatraon ko suvidhajanak banata hai. Travel, stay aur darshan ki planning acchi tarah se ki gayi hai, jo ek stress-free experience deta hai.",
                "App ke madhyam se yatra plan karna bahut easy ho gaya hai. Booking, prasad, aur guide ki suvidha bhi uplabdh hai, jo trip ko aur bhi special banati hai.",
            ];
            \App\Models\TourReviews::create([
                'user_id' => ($data['customer_id'] ?? ""),
                'order_id' => $getorders['id'],
                'tour_id' => $getorders['tour_id'],
                'star' => 5,
                'comment' => \Illuminate\Support\Arr::random($reviews)
            ]);
        } elseif ($type == 'event') {
            $getorders = \App\Models\EventOrder::where('order_no', $data['orderId'])->first();
            $reviews = [
                "Event listing organized hai. Event reminders aur calendar sync ka feature add ho jaye to aur bhi useful hoga.",
                "App ke madhyam se dharmik events ki jaankari lena aur unmein shamil hona bahut hi aasan ho gaya hai. Sabhi details clear di gayi hain, aur registration process bhi smooth hai.",
                "Mandir aur dharmik samaroho ki updates regularly mil rahi hain, jo bahut hi helpful hai. Event reminders aur booking ka process bhi flawless hai.",
                "Yeh feature bahut hi accha laga. Har event ki details achi tarah explain ki gayi hain, aur timing, location sab kuch clear hai. Reminder ka option bhi bahut useful hai.",
                "Agar kisi bhi dharmik event ka hissa banna ho to is app se sab kuch bahut aasaan ho jata hai. Event ki planning aur details bilkul clear milti hain, jo experience ko aur bhi accha banata hai.",
            ];
            \App\Models\EventsReview::create([
                'user_id' => ($data['customer_id'] ?? ""),
                'order_id' => $getorders['id'],
                'event_id' => $getorders['event_id'],
                'star' => 5,
                'comment' => \Illuminate\Support\Arr::random($reviews)
            ]);
        }
    }

    public static function send_push_notif_to_topic($data, $topic = 'sixvalley')
    {
        $key = BusinessSetting::where(['type' => 'push_notification_key'])->first()->value;

        $url = "https://fcm.googleapis.com/fcm/send";
        $header = [
            "authorization: key=" . $key . "",
            "content-type: application/json",
        ];

        $image = asset('storage/app/public/notification') . '/' . $data['image'];
        $postdata = '{
            "to" : "/topics/' . $topic . '",
            "data" : {
                "title":"' . $data->title . '",
                "body" : "' . $data->description . '",
                "image" : "' . $image . '",
                "is_read": 0
              },
              "notification" : {
                "title":"' . $data->title . '",
                "body" : "' . $data->description . '",
                "image" : "' . $image . '",
                "title_loc_key":null,
                "is_read": 0,
                "icon" : "new",
                "sound" : "default"
              }
        }';

        $ch = curl_init();
        $timeout = 120;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        // Get URL content
        $result = curl_exec($ch);
        // close handle to release resources
        curl_close($ch);

        return $result;
    }

    public static function get_seller_by_token($request)
    {
        $data = '';
        $success = 0;

        $token = explode(' ', $request->header('authorization'));
        if (count($token) > 1 && strlen($token[1]) > 30) {
            $seller = Seller::where(['auth_token' => $token['1']])->first();
            if (isset($seller)) {
                $data = $seller;
                $success = 1;
            }
        }

        return [
            'success' => $success,
            'data' => $data
        ];
    }

    public static function remove_dir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir . "/" . $object) == "dir") Helpers::remove_dir($dir . "/" . $object);
                    else unlink($dir . "/" . $object);
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }

    public static function currency_code()
    {
        Helpers::currency_load();
        if (session()->has('currency_symbol')) {
            $symbol = session('currency_symbol');
            $code = Currency::where(['symbol' => $symbol])->first()->code;
        } else {
            $system_default_currency_info = session('system_default_currency_info');
            $code = $system_default_currency_info->code;
        }
        return $code;
    }

    public static function get_language_name($key)
    {
        $values = Helpers::get_business_settings('language');
        foreach ($values as $value) {
            if ($value['code'] == $key) {
                $key = $value['name'];
            }
        }

        return $key;
    }

    public static function setEnvironmentValue($envKey, $envValue)
    {
        $envFile = app()->environmentFilePath();
        $str = file_get_contents($envFile);
        if (is_bool(env($envKey))) {
            $oldValue = var_export(env($envKey), true);
        } else {
            $oldValue = env($envKey);
        }

        if (strpos($str, $envKey) !== false) {
            $str = str_replace("{$envKey}={$oldValue}", "{$envKey}={$envValue}", $str);
        } else {
            $str .= "{$envKey}={$envValue}\n";
        }
        $fp = fopen($envFile, 'w');
        fwrite($fp, $str);
        fclose($fp);
        return $envValue;
    }

    public static function requestSender()
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt_array($curl, array(
            CURLOPT_URL => route(base64_decode('YWN0aXZhdGlvbi1jaGVjaw==')),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
        ));
        $response = curl_exec($curl);
        $data = json_decode($response, true);
        return $data;
    }

    public static function sales_commission($order)
    {
        $discount_amount = 0;
        if ($order->coupon_code) {
            $coupon = Coupon::where(['code' => $order->coupon_code])->first();
            if ($coupon) {
                $discount_amount = $coupon->coupon_type == 'free_delivery' ? 0 : $order['discount_amount'];
            }
        }
        $order_summery = OrderManager::order_summary($order);
        $order_total = $order_summery['subtotal'] - $order_summery['total_discount_on_product'] - $discount_amount;
        $commission_amount = self::seller_sales_commission($order['seller_is'], $order['seller_id'], $order_total);

        return $commission_amount;
    }

    public static function sales_commission_before_order($cart_group_id, $coupon_discount)
    {
        $carts = CartManager::get_cart($cart_group_id);
        $cart_summery = OrderManager::order_summary_before_place_order($carts, $coupon_discount);
        $commission_amount = self::seller_sales_commission($carts[0]['seller_is'], $carts[0]['seller_id'], $cart_summery['order_total']);

        return $commission_amount;
    }

    public static function seller_sales_commission($seller_is, $seller_id, $order_total)
    {
        $commission_amount = 0;
        if ($seller_is == 'seller') {
            $seller = Seller::find($seller_id);
            if (isset($seller) && $seller['sales_commission_percentage'] !== null) {
                $commission = $seller['sales_commission_percentage'];
            } else {
                $commission = Helpers::get_business_settings('sales_commission');
            }
            $commission_amount = number_format(($order_total / 100) * $commission, 2);
        }
        return $commission_amount;
    }

    public static function categoryName($id)
    {
        return Category::select('name')->find($id)->name;
    }

    public static function set_symbol($amount)
    {
        $decimal_point_settings = Helpers::get_business_settings('decimal_point_settings');
        $position = Helpers::get_business_settings('currency_symbol_position');
        if (!is_null($position) && $position == 'left') {
            $string = currency_symbol() . '' . number_format($amount, (!empty($decimal_point_settings) ? $decimal_point_settings : 0));
        } else {
            $string = number_format($amount, !empty($decimal_point_settings) ? $decimal_point_settings : 0) . '' . currency_symbol();
        }
        return $string;
    }

    public static function pagination_limit()
    {
        $pagination_limit = BusinessSetting::where('type', 'pagination_limit')->first();
        if ($pagination_limit != null) {
            return $pagination_limit->value;
        } else {
            return 25;
        }
    }

    public static function gen_mpdf($view, $file_prefix, $file_postfix)
    {
        $mpdf = new \Mpdf\Mpdf(['default_font' => 'DejaVuSans', 'mode' => 'utf-8', 'format' => [190, 250]]);
        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont = true;

        $mpdf->useSubstitutions = true;
        $mpdf->SetFont('DejaVuSans');
        $mpdf_view = $view;
        $mpdf_view = $mpdf_view->render();
        $mpdf->WriteHTML('<meta charset="UTF-8">' . $mpdf_view);
        $mpdf->Output($file_prefix . $file_postfix . '.pdf', 'D');
    }

    public static function gen_mpdf_Pdf($view, $file_prefix, $file_postfix)
    {
        $mpdf = new \Mpdf\Mpdf(['default_font' => 'FreeSerif', 'mode' => 'utf-8', 'format' => [190, 250]]);
        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont = true;

        $mpdf_view = $view;
        $mpdf_view = $mpdf_view->render();
        $mpdf->WriteHTML($mpdf_view);
        $directory = storage_path('app/public/donate/invoice');
        if (!\Illuminate\Support\Facades\File::exists($directory)) {
            \Illuminate\Support\Facades\File::makeDirectory($directory, 0755, true);
        }
        $filePath = storage_path('app/public/donate/invoice/' . $file_prefix . $file_postfix . '.pdf');
        $mpdf->Output($filePath, 'F');
        return $filePath;
    }

    public static function gen_mpdf_temple_Pdf($view, $file_prefix, $file_postfix)
    {
        $mpdf = new \Mpdf\Mpdf(['default_font' => 'FreeSerif', 'mode' => 'utf-8', 'format' => [190, 250]]);
        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont = true;

        $mpdf_view = $view;
        $mpdf_view = $mpdf_view->render();
        $mpdf->WriteHTML($mpdf_view);
        $directory = storage_path('app/public/temple/invoice');
        if (!\Illuminate\Support\Facades\File::exists($directory)) {
            \Illuminate\Support\Facades\File::makeDirectory($directory, 0755, true);
        }
        $filePath = storage_path('app/public/temple/invoice/' . $file_prefix . $file_postfix . '.pdf');
        $mpdf->Output($filePath, 'F');
        return $filePath;
    }

    public static function generate_referer_code()
    {
        $ref_code = strtoupper(Str::random('20'));
        if (User::where('referral_code', '=', $ref_code)->exists()) {
            return generate_referer_code();
        }
        return $ref_code;
    }

    public static function add_fund_to_wallet_bonus($amount)
    {
        $bonuses = AddFundBonusCategories::where('is_active', 1)
            ->whereDate('start_date_time', '<=', now())
            ->whereDate('end_date_time', '>=', now())
            ->where('min_add_money_amount', '<=', $amount)
            ->get();

        $bonuses = $bonuses->where('min_add_money_amount', $bonuses->max('min_add_money_amount'));

        foreach ($bonuses as $key => $item) {
            $item->applied_bonus_amount = $item->bonus_type == 'percentage' ? ($amount * $item->bonus_amount) / 100 : $item->bonus_amount;

            //max bonus check
            if ($item->bonus_type == 'percentage' && $item->applied_bonus_amount > $item->max_bonus_amount) {
                $item->applied_bonus_amount = $item->max_bonus_amount;
            }
        }

        return $bonuses->max('applied_bonus_amount') ?? 0;
    }
}


if (!function_exists('currency_symbol')) {
    function currency_symbol()
    {
        Helpers::currency_load();
        if (\session()->has('currency_symbol')) {
            $symbol = \session('currency_symbol');
        } else {
            $system_default_currency_info = \session('system_default_currency_info');
            $symbol = $system_default_currency_info->symbol;
        }
        return $symbol;
    }
}
//formats currency
if (!function_exists('format_price')) {
    function format_price($price)
    {
        return number_format($price, 2) . currency_symbol();
    }
}

/*function translate($key)
{
    $local = Helpers::default_lang();
    App::setLocale($local);

    try {
        $lang_array = include(base_path('resources/lang/' . $local . '/messages.php'));
        $processed_key = ucfirst(str_replace('_', ' ', Helpers::remove_invalid_charcaters($key)));
        $key = Helpers::remove_invalid_charcaters($key);
        if (!array_key_exists($key, $lang_array)) {
            $lang_array[$key] = $processed_key;
            $str = "<?php return " . var_export($lang_array, true) . ";";
            file_put_contents(base_path('resources/lang/' . $local . '/messages.php'), $str);
            $result = $processed_key;
        } else {
            $result = __('messages.' . $key);
        }
    } catch (\Exception $exception) {
        $result = __('messages.' . $key);
    }

    return $result;
}*/

function auto_translator($q, $sl, $tl)
{
    $res = file_get_contents("https://translate.googleapis.com/translate_a/single?client=gtx&ie=UTF-8&oe=UTF-8&dt=bd&dt=ex&dt=ld&dt=md&dt=qca&dt=rw&dt=rm&dt=ss&dt=t&dt=at&sl=" . $sl . "&tl=" . $tl . "&hl=hl&q=" . urlencode($q), $_SERVER['DOCUMENT_ROOT'] . "/transes.html");
    $res = json_decode($res);
    return str_replace('_', ' ', $res[0][0][0]);
}

function getLanguageCode(string $country_code): string
{
    $locales = array(
        'af-ZA',
        'am-ET',
        'ar-AE',
        'ar-BH',
        'ar-DZ',
        'ar-EG',
        'ar-IQ',
        'ar-JO',
        'ar-KW',
        'ar-LB',
        'ar-LY',
        'ar-MA',
        'ar-OM',
        'ar-QA',
        'ar-SA',
        'ar-SY',
        'ar-TN',
        'ar-YE',
        'az-Cyrl-AZ',
        'az-Latn-AZ',
        'be-BY',
        'bg-BG',
        'bn-BD',
        'bs-Cyrl-BA',
        'bs-Latn-BA',
        'cs-CZ',
        'da-DK',
        'de-AT',
        'de-CH',
        'de-DE',
        'de-LI',
        'de-LU',
        'dv-MV',
        'el-GR',
        'en-AU',
        'en-BZ',
        'en-CA',
        'en-GB',
        'en-IE',
        'en-JM',
        'en-MY',
        'en-NZ',
        'en-SG',
        'en-TT',
        'en-US',
        'en-ZA',
        'en-ZW',
        'es-AR',
        'es-BO',
        'es-CL',
        'es-CO',
        'es-CR',
        'es-DO',
        'es-EC',
        'es-ES',
        'es-GT',
        'es-HN',
        'es-MX',
        'es-NI',
        'es-PA',
        'es-PE',
        'es-PR',
        'es-PY',
        'es-SV',
        'es-US',
        'es-UY',
        'es-VE',
        'et-EE',
        'fa-IR',
        'fi-FI',
        'fil-PH',
        'fo-FO',
        'fr-BE',
        'fr-CA',
        'fr-CH',
        'fr-FR',
        'fr-LU',
        'fr-MC',
        'he-IL',
        'hi-IN',
        'hr-BA',
        'hr-HR',
        'hu-HU',
        'hy-AM',
        'id-ID',
        'ig-NG',
        'is-IS',
        'it-CH',
        'it-IT',
        'ja-JP',
        'ka-GE',
        'kk-KZ',
        'kl-GL',
        'km-KH',
        'ko-KR',
        'ky-KG',
        'lb-LU',
        'lo-LA',
        'lt-LT',
        'lv-LV',
        'mi-NZ',
        'mk-MK',
        'mn-MN',
        'ms-BN',
        'ms-MY',
        'mt-MT',
        'nb-NO',
        'ne-NP',
        'nl-BE',
        'nl-NL',
        'pl-PL',
        'prs-AF',
        'ps-AF',
        'pt-BR',
        'pt-PT',
        'ro-RO',
        'ru-RU',
        'rw-RW',
        'sv-SE',
        'si-LK',
        'sk-SK',
        'sl-SI',
        'sq-AL',
        'sr-Cyrl-BA',
        'sr-Cyrl-CS',
        'sr-Cyrl-ME',
        'sr-Cyrl-RS',
        'sr-Latn-BA',
        'sr-Latn-CS',
        'sr-Latn-ME',
        'sr-Latn-RS',
        'sw-KE',
        'tg-Cyrl-TJ',
        'th-TH',
        'tk-TM',
        'tr-TR',
        'uk-UA',
        'ur-PK',
        'uz-Cyrl-UZ',
        'uz-Latn-UZ',
        'vi-VN',
        'wo-SN',
        'yo-NG',
        'zh-CN',
        'zh-HK',
        'zh-MO',
        'zh-SG',
        'zh-TW'
    );

    foreach ($locales as $locale) {
        $locale_region = explode('-', $locale);
        if (strtoupper($country_code) == $locale_region[1]) {
            return $locale_region[0];
        }
    }

    return "en";
}

function hex2rgb($colour)
{
    if ($colour[0] == '#') {
        $colour = substr($colour, 1);
    }
    if (strlen($colour) == 6) {
        list($r, $g, $b) = array($colour[0] . $colour[1], $colour[2] . $colour[3], $colour[4] . $colour[5]);
    } elseif (strlen($colour) == 3) {
        list($r, $g, $b) = array($colour[0] . $colour[0], $colour[1] . $colour[1], $colour[2] . $colour[2]);
    } else {
        return false;
    }
    $r = hexdec($r);
    $g = hexdec($g);
    $b = hexdec($b);
    return array('red' => $r, 'green' => $g, 'blue' => $b);
}

if (!function_exists('customer_info')) {
    function customer_info()
    {
        return User::where('id', auth('customer')->id())->first();
    }
}

if (!function_exists('order_status_history')) {
    function order_status_history($order_id, $status)
    {
        return OrderStatusHistory::where(['order_id' => $order_id, 'status' => $status])->latest()->pluck('created_at')->first();
    }
}

if (!function_exists('get_shop_name')) {
    function get_shop_name($seller_id)
    {
        $shop = Shop::where(['seller_id' => $seller_id])->first();
        return $shop ? $shop->name : null;
    }
}

if (!function_exists('hex_to_rgb')) {
    function hex_to_rgb($hex)
    {
        $result = preg_match('/^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i', $hex, $matches);
        $data = $result ? hexdec($matches[1]) . ', ' . hexdec($matches[2]) . ', ' . hexdec($matches[3]) : null;

        return $data;
    }
}
if (!function_exists('get_color_name')) {
    function get_color_name($code)
    {
        return Color::where(['code' => $code])->first()->name;
    }
}

if (!function_exists('format_biginteger')) {
    function format_biginteger($value)
    {
        $suffixes = ["1t+" => 1000000000000, "B+" => 1000000000, "M+" => 1000000, "K+" => 1000];
        foreach ($suffixes as $suffix => $factor) {
            if ($value >= $factor) {
                $div = $value / $factor;
                $formatted_value = number_format($div, 1) . $suffix;
                break;
            }
        }

        if (!isset($formatted_value)) {
            $formatted_value = $value;
        }

        return $formatted_value;
    }
}

if (!function_exists('payment_gateways')) {
    function payment_gateways()
    {
        $payment_published_status = config('get_payment_publish_status');
        $payment_gateway_published_status = isset($payment_published_status[0]['is_published']) ? $payment_published_status[0]['is_published'] : 0;

        $payment_gateways_query = Setting::whereIn('settings_type', ['payment_config'])->where('is_active', 1);
        if ($payment_gateway_published_status == 1) {
            $payment_gateways_list = $payment_gateways_query->get();
        } else {
            $payment_gateways_list = $payment_gateways_query->whereIn('key_name', Helpers::default_payment_gateways())->get();
        }

        return $payment_gateways_list;
    }
}

if (!function_exists('get_business_settings')) {
    function get_business_settings($name)
    {
        $config = null;
        $check = ['currency_model', 'currency_symbol_position', 'system_default_currency', 'language', 'company_name', 'decimal_point_settings', 'product_brand', 'digital_product', 'company_email'];

        if (in_array($name, $check) && session()->has($name)) {
            $config = session($name);
        } else {
            $data = BusinessSetting::where(['type' => $name])->first();
            if (isset($data)) {
                $config = json_decode($data['value'], true);
                if (is_null($config)) {
                    $config = $data['value'];
                }
            }

            if (in_array($name, $check)) {
                session()->put($name, $config);
            }
        }
        return $config;
    }
}

if (!function_exists('get_customer')) {
    function get_customer($request = null)
    {
        if (auth('customer')->check()) {
            return auth('customer')->user();
        }

        if ($request != null && $request->user() != null) {
            return $request->user();
        }

        if (session()->has('customer_id') && !session('is_guest')) {
            return User::find(session('customer_id'));
        }

        if (isset($request->user)) {
            return $request->user;
        }

        return 'offline';
    }
}

if (!function_exists('product_image_path')) {
    function product_image_path($image_type): string
    {
        $path = '';
        if ($image_type == 'thumbnail') {
            $path = asset('storage/app/public/product/thumbnail');
        } elseif ($image_type == 'product') {
            $path = asset('storage/app/public/product');
        }
        return $path;
    }
}

if (!function_exists('currency_converter')) {
    function currency_converter($amount): string
    {
        $currency_model = Helpers::get_business_settings('currency_model');
        if ($currency_model == 'multi_currency') {
            if (session()->has('usd')) {
                $usd = session('usd');
            } else {
                $usd = Currency::where(['code' => 'USD'])->first()->exchange_rate;
                session()->put('usd', $usd);
            }
            $my_currency = \session('currency_exchange_rate');
            $rate = $my_currency / $usd;
        } else {
            $rate = 1;
        }

        return Helpers::set_symbol(round($amount * $rate, 2));
    }
}

// Day and Timer View
if (!function_exists('getNextPoojaDay')) {
    function getNextPoojaDay($weekday, $time)
    {

        $currentDateTime = new DateTime('now', new DateTimeZone('Asia/Kolkata'));
        $currentDateTime->modify('+1 days');

        $targetTime = DateTime::createFromFormat('H:i:s', $time, new DateTimeZone('Asia/Kolkata'));

        if (!is_array($weekday)) {
            $weekday = [];
        }
        $weekdays = array_map('strtolower', $weekday);

        $daysOfWeek = [
            'sunday' => 0,
            'monday' => 1,
            'tuesday' => 2,
            'wednesday' => 3,
            'thursday' => 4,
            'friday' => 5,
            'saturday' => 6
        ];

        for ($i = 0; $i < 7; $i++) {
            $dateToCheck = clone $currentDateTime;
            $dateToCheck->modify("+$i day");
            $dayOfWeek = strtolower($dateToCheck->format('l'));

            if (in_array($dayOfWeek, $weekdays)) {
                $poojaDateTime = new DateTime($dateToCheck->format('Y-m-d') . ' ' . $targetTime->format('H:i:s'), new DateTimeZone('Asia/Kolkata'));
                if ($dateToCheck->format('Y-m-d') == $currentDateTime->format('Y-m-d')) {
                    if ($poojaDateTime > $currentDateTime) {
                        return $poojaDateTime;
                    }
                } else {
                    return $poojaDateTime;
                }
            }
        }
        return null;
    }
}
// Next Day Chadava View
if (!function_exists('getnextChadavaDay')) {
    function  getNextChadhavaDay($ChadhavaWeek)
    {
        $currentDateTime = new DateTime('now', new DateTimeZone('Asia/Kolkata'));
        if (!is_array($ChadhavaWeek)) {
            $ChadhavaWeek = [];
        }
        $ChadhavaWeekdays = array_map('strtolower', $ChadhavaWeek);
        $daysOfWeek = [
            'sunday' => 0,
            'monday' => 1,
            'tuesday' => 2,
            'wednesday' => 3,
            'thursday' => 4,
            'friday' => 5,
            'saturday' => 6
        ];

        for ($i = 0; $i < 7; $i++) {
            $dateToCheck = clone $currentDateTime;
            $dateToCheck->modify("+$i day");
            $dayOfWeek = strtolower($dateToCheck->format('l'));
            if (in_array($dayOfWeek, $ChadhavaWeekdays)) {
                $ChadhavaDateTime = new DateTime(
                    $dateToCheck->format('Y-m-d') . ' 23:59:00',
                    new DateTimeZone('Asia/Kolkata')
                );
                // dd($dateToCheck);
                if ($dateToCheck->format('Y-m-d') == $currentDateTime->format('Y-m-d')) {
                    if ($ChadhavaDateTime > $currentDateTime) {
                        return $ChadhavaDateTime;
                    }
                } else {
                    return $ChadhavaDateTime;
                }
            }
        }
        return null;
    }
}
if (!function_exists('getRandomFeedbackMessage')) {
    function getRandomFeedbackMessage()
    {
        $messages = [
            'Is platform par Pandit booking karna bohot aasaan hai. Verified Pandit milte hain aur poori process hassle-free hai',
            'Online puja booking kaafi easy aur convenient hai. Sirf date select karo aur sab kuch smoothly manage ho jata hai. Live streaming aur prasad delivery jaise features isse aur bhi special banate hain',
            'Festivals aur events ki sari important jankari ek jagah mil jati hai. Reminder aur updates milne se kuch miss nahi hota.',
            'Transparent donation system hai aur certificate milne se vishwas aur badhta hai. Gau seva aur anna daan jaise options bhi hain jo achha lagta hai.',
            'Yahan se Pandit ji book karna super easy hai! Verified Pandit milte hain, aur proper vidhi vidhan se puja hoti hai. Bahut hi convenient service hai!',
            'Puja ghar baithe book ho jati hai, aur live streaming se ashirwad bhi mil jata hai. Samagri ka bhi tension nahi, sab kuch manage ho jata hai!',
            'Mandir yatra aur teerth darshan ka itna systematic arrangement kahin aur nahi mila! Hotel, travel, aur darshan ka pura management top-notch hai!',
            'Agar aap religious products bechte hain, toh yeh platform best hai! Direct customers milte hain aur sale kaafi badh gayi hai!',
            'Mujhe daily panchang aur chandra grah ka sthiti yahan se pata chal jata hai. Ab kisi kaam ke liye shubh muhurat dhundhna aur bhi easy ho gaya hai!',
            'Kundali milan aur dosh nivaran ka detailed analysis yahan milta hai. Plus, personal astrologer se consult karna bhi possible hai!',
            'Roz ka panchang, choghadiya, aur shubh muhurat dekhne ka quick aur reliable source! Har important kaam se pehle yahin check karta hoon',
            'Kundali dosh aur uske upay bohot clearly samjhaye gaye hain. Astrologers bhi kaafi knowledgeable hain jo sahi guidance dete hain!',
        ];

        return Arr::random($messages);
    }
}

if (!function_exists('displayStarRating')) {
    function displayStarRating($rating)
    {
        $fullStars = floor($rating);
        $halfStar = ($rating - $fullStars) >= 0.5 ? 1 : 0;
        $remainingStars = 5 - ($fullStars + $halfStar);
        $starsHtml = '';

        // Full stars
        for ($i = 0; $i < $fullStars; $i++) {
            $starsHtml .= '<i class="fas fa-star gold"></i>';
        }

        // Half star
        if ($halfStar) {
            $starsHtml .= '<i class="fas fa-star-half-alt gold"></i>';
        }

        for ($i = 0; $i < $remainingStars; $i++) {
            $starsHtml .= '<i class="fas fa-star gold"></i>';
        }

        return $starsHtml;
    }
}

