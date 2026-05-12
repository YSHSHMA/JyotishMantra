<?php

namespace App\Http\Controllers\RestAPI\v3\seller\auth;

use App\Events\VendorRegistrationMailEvent;
use App\Http\Controllers\Controller;
use App\Models\Seller;
use App\Models\Shop;
use App\Utils\Helpers;
use App\Utils\ImageManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email'         => 'required|unique:sellers',
            'shop_address'  => 'required_if:type,seller',
            'f_name'        => 'required',
            'l_name'        => 'required',
            'shop_name'     => 'required_if:type,seller',
            'phone'         => 'required|unique:sellers',
            'password'      => 'required|min:8',
            'image'         => 'required_if:type,seller|mimes: jpg,jpeg,png,gif',
            'logo'          => 'required_if:type,seller|mimes: jpg,jpeg,png,gif',
            'banner'        => 'required_if:type,seller|mimes: jpg,jpeg,png,gif',
            'bottom_banner' => 'required_if:type,seller|mimes: jpg,jpeg,png,gif',
            "type"         =>   "required|in:seller,event,tour,trust",
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => Helpers::error_processor($validator)], 403);
        }

        DB::beginTransaction();
        try {
            $seller = new Seller();
            $seller->f_name = $request->f_name;
            $seller->l_name = $request->l_name;
            $seller->phone = $request->phone;
            $seller->email = $request->email;
            $seller->image = ImageManager::upload('seller/', 'webp', $request->file('image'));
            $seller->password = bcrypt($request->password);
            $seller->status =  $request->status == 'approved' ? 'approved' : "pending";
            $seller->type =  $request->type;
            $seller->save();
            if ($request->type == 'seller') {
                $shop = new Shop();
                $shop->seller_id = $seller->id;
                $shop->name = $request->shop_name;
                $shop->address = $request->shop_address;
                $shop->contact = $request->phone;
                $shop->image = ImageManager::upload('shop/', 'webp', $request->file('logo'));
                $shop->banner = ImageManager::upload('shop/banner/', 'webp', $request->file('banner'));
                $shop->bottom_banner = ImageManager::upload('shop/banner/', 'webp', $request->file('bottom_banner'));
                $shop->save();
            } elseif ($request->type == 'tour') {
                $tours = new \App\Models\TourAndTravel();
                $tours->person_name = $request->f_name . ' ' . $request->l_name;
                $tours->person_phone = $request->phone;
                $tours->person_email = $request->email;
                $tours->status =  0;
                $tours->is_approve =  0;
                $vendor_logo = '';
                if ($request->file('logo')) {
                    $vendor_logo = ImageManager::upload('tour_and_travels/doc/', 'webp', $request->file('logo'));
                }
                $tours->image = $vendor_logo;
                $tours->save();
                Seller::where('id', $seller->id)->update(['relation_id' => $tours->id]);
            } elseif ($request->type == 'event') {
                $eventOrg = new \App\Models\EventOrganizer();
                $eventOrg->full_name = $request->f_name . ' ' . $request->l_name;
                $eventOrg->contact_number = $request->phone;
                $eventOrg->email_address = $request->email;
                $eventOrg->status =  0;
                $eventOrg->is_approve =  0;
                $vendor_logo = '';
                if ($request->file('image')) {
                    $vendor_logo = \App\Utils\ImageManager::upload('event/organizer/', 'webp', $request->file('image'));
                }
                $eventOrg->image = $vendor_logo;
                $eventOrg->save();
                \App\Models\Seller::where('id', $seller->id)->update(['relation_id' => $eventOrg->id]);
            } elseif ($request->type == 'trust') {
                $DonateTrust = new \App\Models\DonateTrust();
                $DonateTrust->memberlist = json_encode([['member_name' => $request->f_name . ' ' . $request->l_name, 'member_phone_no' => $request->phone, 'member_position' => "Owner"]]);
                $DonateTrust->trust_email = $request->email;
                $DonateTrust->status =  0;
                $DonateTrust->is_approve =  0;
                $vendor_logo = '';
                if ($request->file('logo')) {
                    $vendor_logo = \App\Utils\ImageManager::upload('donate/trust/', 'webp', $request->file('logo'));
                }
                $DonateTrust->theme_image = $vendor_logo;
                $DonateTrust->save();
                \App\Models\Seller::where('id', $seller->id)->update(['relation_id' => $DonateTrust->id]);
            }
            DB::table('seller_wallets')->insert([
                'seller_id' => $seller['id'],
                'withdrawn' => 0,
                'commission_given' => 0,
                'total_earning' => 0,
                'pending_withdraw' => 0,
                'delivery_charge_earned' => 0,
                'collected_cash' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            DB::commit();
            $data = [
                'name' => $request['f_name'],
                'status' => 'pending',
                'subject' => translate('Vendor_Registration_Successfully_Completed'),
                'title' => translate('registration_Complete') . '!',
                'message' => translate('congratulation') . '!' . translate('Your_registration_request_has_been_send_to_admin_successfully') . '!' . translate('Please_wait_until_admin_reviewal') . '.',
            ];
            // event(new VendorRegistrationMailEvent($request['email'], $data));
            $data  = \App\Models\Seller::where('email', ($request['email'] ?? ""))->with('shop')->first();
            if ($data  && isset($request['email']) && filter_var($request['email'], FILTER_VALIDATE_EMAIL)) {
                $data['type'] = 'invitation';
                $data['email'] = $request['email'];
                $data['subject'] = 'Vendor_Registration_Successfully_Completed';
                $data['htmlContent'] = \Illuminate\Support\Facades\View::make(
                    'email-templates.vendor-registration',
                    compact('data')
                )->render();
                Helpers::emailSendMessage($data);
            }
            //Whatsap
            $message_data = [
                'shop_name' => $data->shop->name ?? $data['f_name'],
                'vendor_name' => $data['f_name'] . ' ' . $data['l_name'],
                'type' => 'text-with-media',
                'attachment' => asset('/storage/app/public/shop/banner/' . ($data->shop->banner ?? '')),
                'booking_date' => date('d-m-Y', strtotime($data['created_at'])),
                'seller_id' => $data['id'],
            ];

            $messages =  Helpers::whatsappMessage('ecom', 'Vendor Registration Successfully Completed', $message_data);
            return response()->json(['message' => 'vendor apply successfully!'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'vendor apply fail!'], 403);
        }
    }
}
