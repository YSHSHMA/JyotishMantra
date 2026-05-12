<?php

namespace App\Http\Controllers\RestAPI\v1;

use App\Http\Controllers\Controller;
use App\Models\DeliveryCountryCode;
use App\Models\DeliveryZipCode;
use App\Models\DonateTrust;
use App\Models\GuestUser;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\PanditTransectionHistory;
use App\Models\Purohit;
use App\Models\ShippingAddress;
use App\Models\SupportTicket;
use App\Models\SupportTicketConv;
use App\Models\TrustPanditTransection;
use App\Models\VendorEmployees;
use App\Models\Wishlist;
use App\Traits\CommonTrait;
use App\User;
use App\Utils\CustomerManager;
use App\Utils\Helpers;
use App\Utils\ImageManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    use CommonTrait;

    public function info(Request $request)
    {
        $user = $request->user();
        $referral_user_count = User::where('referred_by', $user->id)->count();
        $user->referral_user_count = $referral_user_count;
        $eoCount = User::withCount('orders')->find($user->id)->orders_count;
        $poCount = User::withCount('poojaOrders')->find($user->id)->pooja_orders_count;
        $coCount = User::withCount('chadhavaOrders')->find($user->id)->chadhava_orders_count;
        $opoCount = User::withCount('offlinepoojaOrders')->find($user->id)->offlinepooja_orders_count;
        $toCount = User::withCount('tourOrders')->find($user->id)->tour_orders_count;
        $evoCount = User::withCount('eventOrders')->find($user->id)->event_orders_count;
        $doCount = User::withCount('donationOrders')->find($user->id)->donation_orders_count;
        $koCount = User::withCount('kundaliOrders')->find($user->id)->kundali_orders_count;
        $orderCount = $eoCount + $poCount + $coCount + $opoCount + $toCount + $evoCount + $doCount + $koCount;
        $user->orders_count = $orderCount;

        return response()->json($user, 200);
    }

    public function create_support_ticket(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|exists:support_type,id',
            'issue_id' => 'required|exists:support_issue,id',
            'subject' => "required",
            'priority' => "required",
            'description' => "required",
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'data' => [], 'errors' => Helpers::error_processor($validator)], 403);
        }

        $image = [];
        if ($request->file('image')) {
            foreach ($request->image as $key => $value) {
                $image_name = ImageManager::upload('support-ticket/', 'webp', $value);
                $image[] = $image_name;
            }
        }

        $request['customer_id'] = $request->user()->id;
        $request['ticket_type_id'] = $request['type'];
        $request['ticket_issue_id'] =  $request['issue_id'];
        $request['status'] = 'pending';
        $request['attachment'] = json_encode($image);
        try {
            CustomerManager::create_support_ticket($request);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => [
                    'code' => 'failed',
                    'message' => 'Something went wrong',
                ],
            ], 422);
        }
        return response()->json(['message' => 'Support ticket created successfully.'], 200);
    }

    public function account_delete(Request $request, $id)
    {
        return response()->json(['statusCode' => 500, 'message' => 'Your account deleted successfully'], 500);
        // if ($request->user()->id == $id) {
        //     $user = User::find($id);

        //     $ongoing = ['out_for_delivery', 'processing', 'confirmed', 'pending'];
        //     $order = Order::where('customer_id', $user->id)->whereIn('order_status', $ongoing)->count();
        //     if ($order > 0) {
        //         return response()->json(['message' => 'You can`t delete account due ongoing_order!!'], 403);
        //     }

        //     ImageManager::delete('/profile/' . $user['image']);

        //     $user->delete();
        //     return response()->json(['message' => 'Your account deleted successfully'], 200);
        // } else {
        //     return response()->json(['message' => 'access_denied!!'], 403);
        // }
    }

    public function reply_support_ticket(Request $request, $ticket_id)
    {
        DB::table('support_tickets')->where(['id' => $ticket_id])->update([
            'status' => 'open',
            'updated_at' => now(),
        ]);

        $image = [];
        if ($request->file('image')) {
            foreach ($request->image as $key => $value) {
                $image_name = ImageManager::upload('support-ticket/', 'webp', $value);
                $image[] = $image_name;
            }
        }

        $support = new SupportTicketConv();
        $support->support_ticket_id = $ticket_id;
        $support->attachment = json_encode($image);
        $support->admin_id = 0;
        $support->customer_message = $request['message'];
        $support->save();
        return response()->json(['message' => 'Support ticket reply sent.'], 200);
    }

    public function get_support_tickets(Request $request)
    {
        $getData = SupportTicket::where('customer_id', $request->user()->id)->with(['TicketIssue', 'TicketType'])->latest()->get();
        $ticket = [];
        if ($getData) {
            foreach ($getData as $key => $value) {
                $ticket[$key] = $value;
                $ticket[$key]['type'] = $value['TicketType']['name'] ?? "";
                $ticket[$key]['issue_name'] = $value['TicketIssue']['issue_name'] ?? "";
            }
        }
        return response()->json($ticket, 200);
    }

    public function get_support_ticket_conv($ticket_id)
    {
        $conversations = SupportTicketConv::where('support_ticket_id', $ticket_id)->get();
        $support_ticket = SupportTicket::find($ticket_id);

        $conversations->map(function ($conversation) {
            $conversation->attachment = json_decode($conversation->attachment);
        });

        $conversations = $conversations->toArray();

        if ($support_ticket) {
            $description = array(
                'support_ticket_id' => $ticket_id,
                'admin_id' => null,
                'customer_message' => $support_ticket->description,
                'admin_message' => null,
                'attachment' => json_decode($support_ticket->attachment),
                'position' => 0,
                'created_at' => $support_ticket->created_at,
                'updated_at' => $support_ticket->updated_at,
            );
            array_unshift($conversations, $description);
        }
        return response()->json($conversations, 200);
    }

    public function support_ticket_close($id)
    {
        $ticket = SupportTicket::find($id);
        if ($ticket) {
            $ticket->status = 'close';
            $ticket->updated_at = now();
            $ticket->save();
            return response()->json(['message' => 'Successfully close the ticket'], 200);
        }
        return response()->json(['message' => 'Ticket not found'], 403);
    }

    public function add_to_wishlist(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $wishlist = Wishlist::where('customer_id', $request->user()->id)->where('product_id', $request->product_id)->first();

        if (empty($wishlist)) {
            $wishlist = new Wishlist;
            $wishlist->customer_id = $request->user()->id;
            $wishlist->product_id = $request->product_id;
            $wishlist->save();
            return response()->json(['message' => 'successfully added!'], 200);
        }

        return response()->json(['message' => 'Already in your wishlist'], 409);
    }

    public function remove_from_wishlist(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $wishlist = Wishlist::where('customer_id', $request->user()->id)->where('product_id', $request->product_id)->first();

        if (!empty($wishlist)) {
            Wishlist::where(['customer_id' => $request->user()->id, 'product_id' => $request->product_id])->delete();
            return response()->json(['message' => translate('successfully removed!')], 200);
        }
        return response()->json(['message' => translate('No such data found!')], 404);
    }

    public function wish_list(Request $request)
    {

        $wishlist = Wishlist::whereHas('wishlistProduct', function ($q) {
            return $q;
        })->with(['productFullInfo'])->where('customer_id', $request->user()->id)->get();

        $wishlist->map(function ($data) {
            $data['productFullInfo'] = Helpers::product_data_formatting(json_decode($data['productFullInfo'], true));
            return $data;
        });

        return response()->json($wishlist, 200);
    }

    public function address_list(Request $request)
    {
        $user = Helpers::get_customer($request);
        if ($user == 'offline') {
            $data = ShippingAddress::where(['customer_id' => $request->guest_id, 'is_guest' => 1])->latest()->get();
        } else {
            $data = ShippingAddress::where(['customer_id' => $user->id, 'is_guest' => '0'])->latest()->get();
        }
        return response()->json($data, 200);
    }

    public function add_new_address(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'contact_person_name' => 'required',
            'address_type' => 'required',
            'address' => 'required',
            'city' => 'required',
            'zip' => 'required',
            'country' => 'required',
            'state' => 'required',
            'phone' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'is_billing' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $zip_restrict_status = Helpers::get_business_settings('delivery_zip_code_area_restriction');
        $country_restrict_status = Helpers::get_business_settings('delivery_country_restriction');

        if ($country_restrict_status && !self::delivery_country_exist_check($request->input('country'))) {
            return response()->json(['message' => translate('Delivery_unavailable_for_this_country')], 403);
        } elseif ($zip_restrict_status && !self::delivery_zipcode_exist_check($request->input('zip'))) {
            return response()->json(['message' => translate('Delivery_unavailable_for_this_zip_code_area')], 403);
        }

        $user = Helpers::get_customer($request);

        $address = [
            'customer_id' => $user == 'offline' ? $request->guest_id : $user->id,
            'is_guest' => $user == 'offline' ? 1 : 0,
            'contact_person_name' => $request->contact_person_name,
            'address_type' => $request->address_type,
            'address' => $request->address,
            'city' => $request->city,
            'zip' => $request->zip,
            'country' => $request->country,
            'state' => $request->state,
            'phone' => $request->phone,
            'email' => $request->email,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'is_billing' => $request->is_billing,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        ShippingAddress::insert($address);
        return response()->json(['message' => translate('successfully added!')], 200);
    }

    public function update_address(Request $request)
    {

        $shipping_address = ShippingAddress::where(['customer_id' => $request->user()->id, 'id' => $request->id])->first();
        if (!$shipping_address) {
            return response()->json(['message' => translate('not_found')], 200);
        }

        $zip_restrict_status = Helpers::get_business_settings('delivery_zip_code_area_restriction');
        $country_restrict_status = Helpers::get_business_settings('delivery_country_restriction');

        if ($country_restrict_status && !self::delivery_country_exist_check($request->input('country'))) {
            return response()->json(['message' => translate('Delivery_unavailable_for_this_country')], 403);
        } elseif ($zip_restrict_status && !self::delivery_zipcode_exist_check($request->input('zip'))) {
            return response()->json(['message' => translate('Delivery_unavailable_for_this_zip_code_area')], 403);
        }

        $user = Helpers::get_customer($request);

        $shipping_address->update([
            'customer_id' => $user == 'offline' ? $request->guest_id : $user->id,
            'is_guest' => $user == 'offline' ? 1 : 0,
            'contact_person_name' => $request->contact_person_name,
            'address_type' => $request->address_type,
            'address' => $request->address,
            'city' => $request->city,
            'zip' => $request->zip,
            'country' => $request->country,
            'phone' => $request->phone,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'is_billing' => $request->is_billing,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => translate('update_successful')], 200);
    }

    public function delete_address(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'address_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $user = Helpers::get_customer($request);

        $shipping_address = ShippingAddress::where(['id' => $request['address_id']])
            ->when($user == 'offline', function ($query) use ($request) {
                $query->where(['customer_id' => $request->guest_id, 'is_guest' => 1]);
            })
            ->when($user != 'offline', function ($query) use ($user) {
                $query->where(['customer_id' => $user->id, 'is_guest' => '0']);
            })->first();

        if ($shipping_address && $shipping_address->delete()) {
            return response()->json(['message' => 'successfully removed!'], 200);
        }
        return response()->json(['message' => translate('No such data found!')], 404);
    }

    public function get_order_list(Request $request)
    {
        $status = array(
            'ongoing' => ['out_for_delivery', 'processing', 'confirmed', 'pending'],
            'canceled' => ['canceled', 'failed', 'returned'],
            'delivered' => ['delivered'],
        );

        $orders = Order::with('details.product', 'deliveryMan', 'seller.shop')
            ->withSum('details as order_details_count', 'qty')
            ->where(['customer_id' => $request->user()->id, 'is_guest' => '0'])
            ->when($request->status && $request->status != 'all', function ($query) use ($request, $status) {
                $query->whereIn('order_status', $status[$request->status])
                    ->when($request->type == 'reorder', function ($query) use ($request) {
                        $query->where('order_type', 'default_type');
                    });
            })
            ->orderBy('id', 'desc')
            ->paginate($request['limit'], ['*'], 'page', $request['offset']);

        $orders->map(function ($data) {
            $data->details->map(function ($query) {
                $query['product'] = Helpers::product_data_formatting(json_decode($query['product'], true));
                return $query;
            });

            return $data;
        });

        $orders = [
            'total_size' => $orders->total(),
            'limit' => $request['limit'],
            'offset' => $request['offset'],
            'orders' => $orders->items()
        ];
        return response()->json($orders, 200);
    }

    public function get_order_details(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $user = Helpers::get_customer($request);

        $details = OrderDetail::with('product', 'order.deliveryMan', 'verificationImages', 'seller.shop')
            ->whereHas('order', function ($query) use ($request, $user) {
                // $query->where([
                //     'customer_id' => $user == 'offline' ? $request->guest_id : $user->id,
                //     'is_guest' => $user == 'offline' ? 1 : '0'
                // ]);
            })
            ->where(['order_id' => $request['order_id']])
            ->get();
        $details->map(function ($query) {
            $query['variation'] = json_decode($query['variation'], true);
            $refund_day_limit = getWebConfig(name: 'refund_day_limit');
            $order_details_date = \Carbon\Carbon::parse($query['created_at'])->setTimezone('Asia/Kolkata');
            $current = \Carbon\Carbon::now()->setTimezone('Asia/Kolkata');
            $length = $order_details_date->diffInDays($current);
            $productDetails = is_array($query['product_details']) ? $query['product_details'] : json_decode($query['product_details'] ?? "[]", true);
            $productDetails['product_refund'] = isset($productDetails['product_refund']) ? $productDetails['product_refund'] : 1;
            if ($query['refund_request'] == 0) {
                if ($length >= $refund_day_limit && ($productDetails['product_refund'] == 0)) {
                    $productDetails['product_refund'] = 1;
                }
            }
            $productarray = $query['product'] ?? "";
            $query['product']['product_refund'] = isset($productarray['product_refund']) ? $productarray['product_refund'] : 1;
            if ($query['refund_request'] == 0) {
                if ($length >= $refund_day_limit && ($query['product']['product_refund'] == 0)) {
                    $query['product']['product_refund'] = 1;
                }
            }
            $query['product_details'] = json_encode($productDetails);
            $query['product_details'] = Helpers::product_data_formatting(json_decode($query['product_details'], true));
            return $query;
        });
        return response()->json($details, 200);
    }

    public function get_order_by_id(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $order = Order::withCount('orderDetails')->with(['deliveryMan', 'offlinePayments', 'verificationImages', 'orderDetails'])->where(['id' => $request['order_id']])->first();
        if (isset($order['offlinePayments'])) {
            $order['offlinePayments']->payment_info = $order->offlinePayments->payment_info;
        }
        $refund_day_limit = getWebConfig(name: 'refund_day_limit');
        $order_details_date = $order->created_at;
        $current = \Carbon\Carbon::now();
        $length = $order_details_date->diffInDays($current);
        if (isset($order['orderDetails'])) {
            foreach ($order['orderDetails'] as $key => $value) {
                $productDetails = is_array($value->product_details) ? $value->product_details : json_decode($value->product_details ?? "[]", true);
                $productDetails['product_refund'] = isset($productDetails['product_refund']) ? $productDetails['product_refund'] : 1;
                if ($length >= $refund_day_limit  && ($productDetails['product_refund'] == 0) && ($order['refund_request'] == 0)) {
                    $productDetails['product_refund'] = 1;
                }
                $order['orderDetails'][$key]['product_details'] = json_encode($productDetails);
            }
        }
        return response()->json($order, 200);
    }

    public function update_profile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'f_name' => 'required',
            'l_name' => 'required',
            'phone' => 'required',
        ], [
            'f_name.required' => translate('First name is required!'),
            'l_name.required' => translate('Last name is required!'),
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        if ($request->has('image')) {
            $imageName = ImageManager::update('profile/', $request->user()->image, 'webp', $request->file('image'));
        } else {
            $imageName = $request->user()->image;
        }

        if ($request['password'] != null && strlen($request['password']) > 5) {
            $pass = bcrypt($request['password']);
        } else {
            $pass = $request->user()->password;
        }

        $userDetails = [
            'f_name' => $request->f_name,
            'name' => $request->f_name . ' ' . $request->l_name,
            'l_name' => $request->l_name,
            'phone' => $request->phone,
            'image' => $imageName,
            'password' => $pass,
            'updated_at' => now(),
        ];

        User::where(['id' => $request->user()->id])->update($userDetails);

        return response()->json(['message' => translate('successfully updated!')], 200);
    }

    public function update_cm_firebase_token(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cm_firebase_token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $user = Helpers::get_customer($request);

        if ($user == 'offline') {
            $guest = GuestUser::find($request->guest_id);
            $guest->fcm_token = $request['cm_firebase_token'];
            $guest->save();
        } else {
            DB::table('users')->where('id', $user->id)->update([
                'cm_firebase_token' => $request['cm_firebase_token'],
            ]);
        }

        return response()->json(['message' => translate('successfully updated!')], 200);
    }

    public function get_restricted_country_list(Request $request)
    {
        $stored_countries = DeliveryCountryCode::orderBy('country_code', 'ASC')->pluck('country_code')->toArray();
        $country_list = COUNTRIES;

        $countries = array();

        foreach ($country_list as $country) {
            if (in_array($country['code'], $stored_countries)) {
                $countries[] = $country['name'];
            }
        }

        if ($request->search) {
            $countries = array_values(preg_grep('~' . $request->search . '~i', $countries));
        }

        return response()->json($countries, 200);
    }

    public function get_restricted_zip_list(Request $request)
    {
        $zipcodes = DeliveryZipCode::orderBy('zipcode', 'ASC')
            ->when($request->search, function ($query) use ($request) {
                $query->where('zipcode', 'like', "%{$request->search}%");
            })
            ->get();

        return response()->json($zipcodes, 200);
    }

    public function language_change(Request $request)
    {
        $user = $request->user();
        $user->app_language = $request->current_language;
        $user->save();

        return response()->json(['message' => 'Successfully change'], 200);
    }
    public function FcmTokenUpdate(Request $request)
    {
        if ($request->user_id) {
            User::where('id', $request->user_id)->where('is_active', 1)->update(['cm_firebase_token' => $request->token]);
        }
        return response()->json(['message' => 'Successfully change'], 200);
    }


    // customer tikect
    public function CustomerIssuesType()
    {
        $TicketType = \App\Models\SupportType::where('status', 1)->get();
        if ($TicketType) {
            return response()->json(['status' => 1, 'message' => 'Successfully', "data" => $TicketType], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Not Found', 'data' => []], 200);
        }
    }
    public function CustomerIssuess(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|exists:support_type,id'
        ],);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'data' => [], 'errors' => Helpers::error_processor($validator)], 200);
        }
        $TicketType = \App\Models\SupportIssue::where('status', 1)->where('type_id', $request['type'])->get();
        if ($TicketType) {
            return response()->json(['status' => 1, 'message' => 'Successfully', "data" => $TicketType], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Not Found', 'data' => []], 200);
        }
    }

    public function CustomerCreateSupportTicket(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => "required|exists:users,id",
            'type_id' => 'required|exists:support_type,id',
            'issue_id' => 'required|exists:support_issue,id',
            'subject' => "required",
            'priority' => "required",
            'description' => "required",
            'attachment' => 'nullable|array',
            'attachment.*' => 'image|mimes:jpeg,png,jpg,gif|max:6000',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'data' => [], 'errors' => Helpers::error_processor($validator)], 200);
        }

        $image = [];
        if ($request->file('attachment')) {
            foreach ($request['attachment'] as $key => $value) {
                $image_name = ImageManager::upload('support-ticket/', 'webp', $value);
                $image[] = $image_name;
            }
        }

        $supports = new \App\Models\SupportTicket();

        $supports->subject =  $request['subject'];
        $supports->ticket_type_id =  $request['type_id'];
        $supports->ticket_issue_id =  $request['issue_id'];
        $supports->customer_id =  $request['user_id'];
        $supports->priority =  $request['priority'];
        $supports->description =  $request['description'];
        $supports->attachment =  json_encode($image);
        $supports->created_at =  now();
        $supports->updated_at =  now();
        $supports->save();
        if ($supports) {
            $supportscon = new \App\Models\SupportTicketConv();
            $supportscon->support_ticket_id = $supports->id;
            $supportscon->customer_message = $request['description'];
            $supportscon->attachment = json_encode($image);
            $supportscon->save();
            return response()->json(['status' => 1, 'message' => 'added Successfully', "data" => []], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Not Found', 'data' => []], 200);
        }
    }

    public function CustomergetSupportTicket(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => "required|exists:users,id"
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'data' => [], 'errors' => Helpers::error_processor($validator)], 200);
        }
        $TicketType = \App\Models\SupportTicket::where('customer_id', $request['user_id'])->with(['TicketIssue', 'TicketType'])->get();
        if ($TicketType) {
            $ticket = [];
            foreach ($TicketType as $key => $value) {
                $ticket[$key]['id'] = $value['id'];
                $ticket[$key]['type'] = $value['TicketType']['name'] ?? "";
                $ticket[$key]['issue_name'] = $value['TicketIssue']['issue_name'] ?? "";
                $ticket[$key]['subject'] = $value['subject'];
                $ticket[$key]['description'] = $value['description'];
                $ticket[$key]['priority'] = $value['priority'];
                $ticket[$key]['status'] = $value['status'];
                $ticket[$key]['created_at'] = $value['created_at'];
            }
            return response()->json(['status' => 1, 'message' => 'Successfully', "data" => $ticket], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Not Found', 'data' => []], 200);
        }
    }

    public function CustomergetTicketId(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => "required|exists:users,id",
            "ticket_id" => [
                "required",
                function ($attribute, $value, $fail) use ($request) {
                    $tickets = SupportTicket::where('id', $value)
                        ->where('customer_id', $request->user_id)
                        ->first();
                    if (!$tickets) {
                        $fail("The selected ticket is invalid or does not belong to this user.");
                    }
                }
            ],
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'data' => [], 'errors' => Helpers::error_processor($validator)], 200);
        }
        $ticket = SupportTicket::with(['conversations' => function ($query) {
            $query->when(theme_root_path() == 'default', function ($sub_query) {
                $sub_query->orderBy('id', 'desc');
            });
        }, 'TicketType', 'TicketIssue'])->where('id', $request->ticket_id)->first();

        if ($ticket) {
            // \App\Models\SupportTicketConv::where('support_ticket_id', $request->ticket_id)->where('read_user_status', 0)->update(['read_user_status' => 1]);
            $ticket_data = [];
            $ticket_data['id'] = $ticket['id'];
            $ticket_data['subject'] = $ticket['subject'];
            $ticket_data['type'] = $ticket['TicketType']['name'] ?? "";;
            $ticket_data['issue_name'] = $ticket['TicketIssue']['issue_name'] ?? "";
            $ticket_data['priority'] = $ticket['priority'];
            $ticket_data['status'] = $ticket['status'];
            $ticket_data['created_at'] = $ticket['created_at'];
            foreach ($ticket['conversations'] as $key => $value) {
                $ticket_data['conversations'][$key]['id'] = $value['id'];
                $ticket_data['conversations'][$key]['admin_id'] = $value['admin_id'];
                $ticket_data['conversations'][$key]['customer_message'] = $value['customer_message'];
                $ticket_data['conversations'][$key]['admin_message'] = $value['admin_message'];
                $ticket_data['conversations'][$key]['position'] = $value['position'];
                $ticket_data['conversations'][$key]['created_at'] = $value['created_at'];
                $attach_image = [];
                if (!empty($value['attached']) && json_decode($value['attached'], true)) {
                    foreach (json_decode($value['attached']) as $index => $photo) {
                        $attach_image[] =  dynamicStorage(path: "storage/app/public/support-ticket/" . $photo);
                    }
                }
                $ticket_data['conversations'][$key]['attachment'] = $attach_image;
            }
            return response()->json(['status' => 1, 'message' => 'Successfully', "data" => $ticket_data], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Not Found', 'data' => []], 200);
        }
    }

    public function CustomerReply(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => "required|exists:users,id",
            "ticket_id" => [
                "required",
                function ($attribute, $value, $fail) use ($request) {
                    $tickets = SupportTicket::where('id', $value)
                        ->where('customer_id', $request->user_id)
                        ->where('status', 'open')
                        ->first();
                    if (!$tickets) {
                        $fail("The selected ticket is invalid or does not belong to this user.");
                    }
                }
            ],
            'description' => "required",
            'attachment' => 'nullable|array',
            'attachment.*' => 'image|mimes:jpeg,png,jpg,gif|max:6000',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'data' => [], 'errors' => Helpers::error_processor($validator)], 200);
        }

        $image = [];
        if ($request->file('attachment')) {
            foreach ($request['attachment'] as $key => $value) {
                $image_name = ImageManager::upload('support-ticket/', 'webp', $value);
                $image[] = $image_name;
            }
        }

        $supportscon = new \App\Models\SupportTicketConv();
        $supportscon->support_ticket_id = $request->ticket_id;
        $supportscon->customer_message = $request['description'];
        $supportscon->attachment = json_encode($image);
        $supportscon->created_at = now();
        $supportscon->updated_at = now();
        $supportscon->save();
        if ($supportscon) {
            return response()->json(['status' => 1, 'message' => 'Replay Successfully', "data" => []], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Not Found', 'data' => []], 200);
        }
    }

    public function CustomerTicketClose(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => "required|exists:users,id",
            "ticket_id" => [
                "required",
                function ($attribute, $value, $fail) use ($request) {
                    $tickets = SupportTicket::where('id', $value)
                        ->where('customer_id', $request->user_id)
                        ->where('status', 'open')
                        ->first();
                    if (!$tickets) {
                        $fail("The selected ticket is invalid or does not belong to this user.");
                    }
                }
            ]
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'data' => [], 'errors' => Helpers::error_processor($validator)], 200);
        }

        $supports = \App\Models\SupportTicket::where('id', $request->ticket_id)->first();
        $supports->status =  'close';
        $supports->updated_at =  now();
        $supports->save();
        if ($supports) {
            return response()->json(['status' => 1, 'message' => 'close Ticket Successfully', "data" => []], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Not Found', 'data' => []], 200);
        }
    }

    public function AstroUserProfileUpdate(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'gender'  => 'nullable|string|max:10',
            'dob'     => 'nullable|date',
            'tob'     => 'nullable|string|max:10',
            'pob'     => 'nullable|string|max:255',
        ]);

        try {
            $user = User::find($request->user_id);

            if (!$user) {
                return response()->json([
                    'status'  => false,
                    'message' => 'User not found',
                ], 404);
            }

            $user->gender = $request->gender ?? $user->gender;
            $user->dob    = $request->dob ?? $user->dob;
            $user->tob    = $request->tob ?? $user->tob;
            $user->pob    = $request->pob ?? $user->pob;

            $user->update();

            return response()->json([
                'status'  => true,
                'message' => 'Profile updated successfully',
                'data'    => $user
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function UserSuggestionList(Request $request)
    {
        $search = $request['search'];

        $users = User::select('name', 'id')->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($search) . '%'])
            ->limit(8)
            ->groupBy('name')
            ->get();
        return response()->json([
            'status'  => true,
            'message' => 'Profile updated successfully',
            'data'    => $users
        ]);
    }

    public function PurohitAllEmployeeList(Request $request)
    {
        $search = $request['search'];
        $purohit = $request['purohit'];
        $amountadd = $request['amountadd'] ?? 0;

        $users = VendorEmployees::select('id', 'relation_id', 'identify_number as name', DB::raw('CONCAT(name," - ",identify_number) as full_name'))->when(!empty($search), function ($q) use ($search) {
            $q->where(function ($q2) use ($search) {
                $q2->whereRaw('LOWER(identify_number) LIKE ?', ['%' . strtolower($search) . '%'])
                    ->orWhereRaw('LOWER(name) LIKE ?', ['%' . strtolower($search) . '%']);
            });
        })
            ->where('purohit_id', $purohit)->where('purohit_id', '!=', '0')
            ->limit(10)
            ->get()->map(function ($item) use ($amountadd, $purohit) {
                if ($amountadd == 1) {
                    $amounts = \App\Models\PanditTransectionHistory::where(['trust_id' => $item->relation_id, 'purohit_id' => $purohit, 'emp_id' => $item->id, 'status' => 0])->where('order_id', '!=', '')->sum('debit');
                    $item->full_name = $item->full_name . " :: " . setCurrencySymbol(amount: usdToDefaultCurrency(amount: $amounts), currencyCode: getCurrencyCode());
                }
                return $item;
            });

        return response()->json([
            'status'  => true,
            'message' => 'Profile get successfully',
            'data'    => $users
        ]);
    }

    public function CollectPaymantOrderUpdate(Request $request)
    {
        $decodeStatus = json_decode($request['order_id']);
        if ($decodeStatus == null) {
            \App\Models\TempleOrderDetails::where('order_id', $request['order_id'])->where('type', 'puja')->update(['purohit_id' => $request['purohit']]);
            $querys = \App\Models\TempleOrderDetails::with(['order'])->where('order_id', $request['order_id'])->where('type', 'puja')->where('booking_status', 'confirmed')->where('purohit_id', $request['purohit']);
            $getDataRecodes = (clone $querys)->get();
            $orderIds = (clone $querys)->pluck('id');
        } else {
            \App\Models\TempleOrderDetails::whereIn('order_id', $decodeStatus)->where('type', 'puja')->update(['purohit_id' => $request['purohit']]);
            $querys = \App\Models\TempleOrderDetails::with(['order'])->whereIn('order_id', $decodeStatus)->where('type', 'puja')->where('booking_status', 'confirmed')->where('purohit_id', $request['purohit']);
            $getDataRecodes = (clone $querys)->get();
            $orderIds = (clone $querys)->pluck('id');
        }
        $Ex_employee = VendorEmployees::where('identify_number', $request['ex_id'])->where('purohit_id', $request['purohit'])->first();
        if ($getDataRecodes) {
            \App\Models\TempleOrderDetails::whereIn('id', $orderIds)->update(['emp_id' => $Ex_employee['id']]);
            foreach ($getDataRecodes as $key => $getData) {
                if ($getData['order']['payment_mode'] == 'online') {
                    $request['num'] = 2;
                }
                if ($request['num'] == 1) {
                    \App\Models\Purohit::where('id', $request['purohit'])->update(['collected_amount' => \Illuminate\Support\Facades\DB::raw('collected_amount + ' . ($getData['base_price']))]);
                    \App\Models\DonateTrust::where('id', $getData['trust_id'])->update([
                        'purohit_collected_amount' => \Illuminate\Support\Facades\DB::raw('purohit_collected_amount - ' . ($getData['base_price'])),
                    ]);
                    $withHistory  = new \App\Models\WithdrawalAmountHistory();
                    $withHistory->type = 'trust';
                    $withHistory->vendor_id = $getData['trust_id'];
                    $withHistory->old_wallet_amount = (\App\Models\DonateTrust::where('id',  $getData['trust_id'])->first()['trust_total_amount']);
                    $withHistory->req_amount = ($getData['receipt_fee']);
                    $withHistory->transaction_type = 'debit';
                    $withHistory->message = "Puja Slip Amount";
                    $withHistory->status = 1;
                    $withHistory->transcation_id = "cash";
                    $withHistory->payment_method = "manual";
                    $withHistory->save();
                    $withHistory  = new \App\Models\WithdrawalAmountHistory();
                    $withHistory->type = 'purohit';
                    $withHistory->vendor_id = $getData['trust_id'];
                    $withHistory->ex_id =  $Ex_employee['id'];
                    $withHistory->old_wallet_amount = (\App\Models\VendorEmployees::where('id',  $Ex_employee['id'])->first()['withdrawal_amount']);
                    $withHistory->req_amount = ($getData['base_price']);
                    $withHistory->transaction_type = 'debit';
                    $withHistory->message = "puja Amount Cash Collect";
                    $withHistory->status = 1;
                    $withHistory->transcation_id = "cash";
                    $withHistory->payment_method = "manual";
                    $withHistory->save();
                    \App\Models\VendorEmployees::where('id',  $Ex_employee['id'])->update(['collected_amount' => \Illuminate\Support\Facades\DB::raw('collected_amount + ' . ($getData['base_price']))]);

                    TrustPanditTransection::create([
                        'order_id'       => $getData['order_id'],
                        'type_order_id'  => $getData['type_order_id'] ?? null,
                        'temple_id'      => $getData['temple_id'] ?? null,
                        'trust_id'       => $getData['trust_id'] ?? null,
                        'pandit_id'      => $getData['purohit_id'] ?? null,
                        'package_id'     => $getData['package_id'] ?? null,
                        'package_price'  => ($getData['base_price'] ?? 0),
                        'payment_method' => 'cash',
                        'payment_status' => 'complete',
                        'status' => '1',
                    ]);
                    $existingBalance = PanditTransectionHistory::where('purohit_id', ($getData['purohit_id'] ?? ''))->where('status', 1)->orderByDesc('id')->value('balance') ?? 0;
                    $newBalance = ($existingBalance ?? 0) + ($getData['base_price'] ?? 0);
                    PanditTransectionHistory::create([
                        'order_id'    => $getData['order_id'],
                        'type'    => 'puja',
                        'temple_id'   => $getData['temple_id'] ?? null,
                        'trust_id'    => $getData['trust_id'] ?? null,
                        'purohit_id'  => ($getData['purohit_id'] ?? null),
                        'emp_id'  => ($Ex_employee['id'] ?? null),
                        'credit'      => ($getData['base_price'] ?? 0),
                        'balance'     => $newBalance,
                        'credit_date' => now(),
                        'status' => 1,
                    ]);
                    PanditTransectionHistory::create([
                        'order_id'    => $getData['order_id'],
                        'type'    => 'puja',
                        'temple_id'   => $getData['temple_id'] ?? null,
                        'trust_id'    => $getData['trust_id'] ?? null,
                        'purohit_id'  => ($getData['purohit_id'] ?? null),
                        'emp_id'  => ($Ex_employee['id'] ?? null),
                        'debit'      => ($getData['base_price'] ?? 0),
                        'balance'     => ($existingBalance ?? 0),
                        'credit_date' => now(),
                        'debit_date' => now(),
                        'status' => 1,
                    ]);
                } elseif ($request['num'] == 0) {
                    TrustPanditTransection::create([
                        'order_id'       => $getData['order_id'],
                        'type_order_id'  => $getData['type_order_id'] ?? null,
                        'temple_id'      => $getData['temple_id'] ?? null,
                        'trust_id'       => $getData['trust_id'] ?? null,
                        'pandit_id'      => $getData['purohit_id'] ?? null,
                        'package_id'     => $getData['package_id'] ?? null,
                        'package_price'  => ($getData['base_price'] ?? 0),
                        'payment_method' => 'cash',
                        'payment_status' => 'pending',
                        'status' => '1',
                    ]);
                    $existingBalance = PanditTransectionHistory::where('purohit_id', ($getData['purohit_id'] ?? ''))->whereIn('status', [0, 1])->orderByDesc('id')->value('balance') ?? 0;
                    $newBalance = ($existingBalance ?? 0) + ($getData['base_price'] ?? 0);
                    PanditTransectionHistory::create([
                        'order_id'    => $getData['order_id'],
                        'type'    => 'puja',
                        'temple_id'   => $getData['temple_id'] ?? null,
                        'trust_id'    => $getData['trust_id'] ?? null,
                        'purohit_id'  => ($getData['purohit_id'] ?? null),
                        'emp_id'  => ($Ex_employee['id'] ?? null),
                        'credit'      => ($getData['base_price'] ?? 0),
                        'balance'     => $newBalance,
                        'credit_date' => now(),
                        'status' => 0,
                        'note' => "Pending Amount from Temple To pandit",
                    ]);
                    PanditTransectionHistory::create([
                        'order_id'    => $getData['order_id'],
                        'type'    => 'puja',
                        'temple_id'   => $getData['temple_id'] ?? null,
                        'trust_id'    => $getData['trust_id'] ?? null,
                        'purohit_id'  => ($getData['purohit_id'] ?? null),
                        'emp_id'  => ($Ex_employee['id'] ?? null),
                        'debit'      => ($getData['base_price'] ?? 0),
                        'balance'     => ($existingBalance ?? 0),
                        'credit_date' => now(),
                        'debit_date' => now(),
                        'status' => 0,
                        'note' => "Pending Amount from Temple To pandit",
                    ]);
                    $withHistory  = new \App\Models\WithdrawalAmountHistory();
                    $withHistory->type = 'trust';
                    $withHistory->vendor_id = $getData['trust_id'];
                    $withHistory->old_wallet_amount = (\App\Models\DonateTrust::where('id',  $getData['trust_id'])->first()['trust_total_amount']);
                    $withHistory->req_amount = ($getData['receipt_fee']);
                    $withHistory->transaction_type = 'debit';
                    $withHistory->message = "Puja Slip Amount";
                    $withHistory->status = 1;
                    $withHistory->transcation_id = "cash";
                    $withHistory->payment_method = "manual";
                    $withHistory->save();
                } elseif ($request['num'] == 2) {
                    \App\Models\VendorEmployees::where('id',  $Ex_employee['id'])->update(['withdrawal_amount' => \Illuminate\Support\Facades\DB::raw('withdrawal_amount + ' . ($getData['base_price']))]);
                    TrustPanditTransection::create([
                        'order_id'       => $getData['order_id'],
                        'type_order_id'  => $getData['type_order_id'] ?? null,
                        'temple_id'      => $getData['temple_id'] ?? null,
                        'trust_id'       => $getData['trust_id'] ?? null,
                        'pandit_id'      => $getData['purohit_id'] ?? null,
                        'package_id'     => $getData['package_id'] ?? null,
                        'package_price'  => ($getData['base_price'] ?? 0),
                        'payment_method' => 'online',
                        'payment_status' => 'complete',
                        'status' => '1',
                    ]);
                    $existingBalance = \App\Models\PanditTransectionHistory::where('purohit_id', ($getData['purohit_id'] ?? ''))->orderByDesc('id')->value('balance') ?? 0;
                    $newBalance = ($existingBalance ?? 0) + (($getData['base_price'] ?? 0));
                    PanditTransectionHistory::create([
                        'order_id'    => $getData['order_id'],
                        'type'    => 'puja',
                        'temple_id'   => $getData['temple_id'] ?? null,
                        'trust_id'    => $getData['trust_id'] ?? null,
                        'purohit_id'  => ($getData['purohit_id'] ?? null),
                        'emp_id'  => ($Ex_employee['id'] ?? null),
                        'credit'      => ($getData['base_price'] ?? 0),
                        'balance'     => $newBalance,
                        'credit_date' => now(),
                        'status' => 1,
                    ]);
                    if ($getData['purohit_id']) {
                        Purohit::where('id', $getData['purohit_id'])->update(['withdrawal_amount' => \Illuminate\Support\Facades\DB::raw('withdrawal_amount + ' . $getData['base_price'])]);
                        \App\Models\DonateTrust::where('id', $getData['trust_id'])->update([
                            'purohit_collected_amount' => \Illuminate\Support\Facades\DB::raw('purohit_collected_amount - ' . ($getData['base_price'])),
                        ]);
                    }
                    DonateTrust::where('id', $getData['trust_id'])->update(['trust_total_amount' => \Illuminate\Support\Facades\DB::raw('trust_total_amount + ' . $getData['receipt_fee'])]);
                    $withHistory  = new \App\Models\WithdrawalAmountHistory();
                    // $withHistory->type = 'trust';
                    // $withHistory->vendor_id = $getData['trust_id'];
                    // $withHistory->old_wallet_amount = (\App\Models\DonateTrust::where('id',  $getData['trust_id'])->first()['trust_total_withdrawal']);
                    // $withHistory->req_amount = ($getData['receipt_fee']);
                    // $withHistory->transaction_type = 'credit';
                    // $withHistory->message = "Online Puja Amount";
                    // $withHistory->status = 1;
                    // $withHistory->transcation_id = "online";
                    // $withHistory->payment_method = "manual";
                    // $withHistory->save();
                }
            }
            return response()->json([
                'status'  => true,
                'message' => 'successfully',
                'data'    => []
            ]);
        }
        return response()->json([
            'status'  => false,
            'message' => 'Not Found Data',
            'data'    => []
        ]);
    }
}
