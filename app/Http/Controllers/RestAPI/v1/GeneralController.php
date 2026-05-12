<?php

namespace App\Http\Controllers\RestAPI\v1;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\GuestUser;
use App\Models\HelpTopic;
use App\Models\Shop;
use App\Utils\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GeneralController extends Controller
{
    // public function faq(){
    //     return response()->json(HelpTopic::orderBy('ranking')->get(),200);
    // }

    public function faq(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|exists:faq_category,name',
        ], [
            'type.required' => 'Name is Empty!',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $getData  = \App\Models\FAQ::where('status', 1)->with(['Category'])->whereHas('Category', function ($subQuery) use ($request) {
            $subQuery->where('status', 1);
            $subQuery->where('name', $request['type']);
        })->get();
        if ($getData) {
            $getArray = [];
            foreach ($getData as $key => $value) {
                $getArray[$key]['id'] = $value['id'];
                $getArray[$key]['en_question'] = $value['question'];
                $getArray[$key]['en_detail'] = $value['detail'];
                $hindi_data = $value->translations()->pluck('value', 'key')->toArray();
                $getArray[$key]['hi_question'] = $hindi_data['question'];
                $getArray[$key]['hi_detail'] = $hindi_data['detail'];
            }

            return response()->json(['status' => 1, 'message' => 'get Successfully', 'data' => $getArray], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Not Found Data', 'data' => []], 200);
        }
    }

    public function get_guest_id(Request $request)
    {
        $guest_id = GuestUser::insertGetId([
            'ip_address' => $request->ip(),
            'created_at' => now(),
        ]);
        return response()->json(['guest_id' => $guest_id], 200);
    }

    public function contact_store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile_number' => 'required',
            'subject' => 'required',
            'message' => 'required',
            'email' => 'required',
            'name' => 'required',
        ], [
            'name.required' => 'Name is Empty!',
            'mobile_number.required' => 'Mobile Number is Empty!',
            'subject.required' => ' Subject is Empty!',
            'message.required' => 'Message is Empty!',
            'email.required' => 'Email is Empty!',

        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $contact = new Contact;
        $contact->name = $request->name;
        $contact->email = $request->email;
        $contact->mobile_number = $request->mobile_number;
        $contact->subject = $request->subject;
        $contact->message = $request->message;
        $contact->save();

        return response()->json(['message' => 'Your Message Send Successfully'], 200);
    }

    public function TermAndConditions(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:seller,tour,event,trustees',
        ], [
            'type.required' => 'type is Empty (use only: seller,tour,event,trustees)!',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $getData  = \App\Models\BusinessSetting::where('type', $request->type . "_terms_condition")->first();
        $getData1  = \App\Models\BusinessSetting::where('type', $request->type . "_privacy_policy")->first();
        if ($getData) {
            $getArray = [];
            $getArray['terms_condition'] = $getData['value'] ?? '';
            $getArray['privacy_policy'] = $getData1['value'] ?? '';
            return response()->json(['status' => 1, 'message' => 'get Successfully', 'data' => $getArray], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Not Found Data', 'data' => []], 200);
        }
    }

    /////////////////////////////////////  Support Vendor /////////////////////////////////////////////
    public function SupportIssuess(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:seller,tour,event,trust',
        ], [
            'type.required' => 'type is Empty (use only: seller,tour,event,trust)!',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $getData  = \App\Models\VendorSupportTicket::where('created_by', 'vendor')->where('type', $request->type)->where('status', 1)->get();
        if ($getData) {
            $getArray = [];
            $key = 0;
            foreach ($getData as $val) {
                $getArray[$key]['id'] = $val['id'] ?? '';
                $getArray[$key]['message'] = $val['message'] ?? '';
                $key++;
            }
            return response()->json(['status' => 1, 'message' => 'get Successfully', 'data' => $getArray], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Not Found Data', 'data' => []], 200);
        }
    }

    public function SupportgetTicket(Request $request)
    {
        $type = optional(auth('seller-api')->user())->type ?? '';
        $vendorId = (($type == 'seller') ? auth('seller')->id() : optional(auth('seller-api')->user())->relation_id ?? '');
        $message_list = \App\Models\VendorSupportTicketConv::where(['created_by' => 'vendor', 'type' => $type, 'vendor_id' => $vendorId])
            ->when(isset($request['status']) && ($request['status'] != 'all'), function ($query) use ($request) {
                return $query->where('status', $request['status']);
            })->with(['seller', 'Trust', 'Event', 'Tour', 'TicketTitle'])->get();
        if ($message_list) {
            $getData = [];
            $key = 0;
            foreach ($message_list as $value) {
                $getData[$key]['id'] = $value['id'];
                $getData[$key]['ticket_id'] = $value['ticket_id'];
                $getData[$key]['issue_name'] = $value['TicketTitle']['message'] ?? "";
                $getData[$key]['query_title'] = $value['query_title'];
                $getData[$key]['status'] = $value['status'];
                $getData[$key]['created_at'] = date('d M,Y h:i A', strtotime($value['created_at']));
                $getData[$key]['updated_at'] = date('d M,Y h:i A', strtotime($value['updated_at']));
                if ($value['type'] == 'seller') {
                    $getData[$key]['user_image'] = getValidImage(path: 'storage/app/public/seller/' . $value['seller']['image'] ?? '', type: 'backend-profile');
                    $getData[$key]['user_name'] = $value['seller']['f_name'] ?? "" . " " . $value['seller']['l_name'] ?? "";
                    $getData[$key]['user_phone'] = $value['seller']['phone'] ?? "";
                    $getData[$key]['user_email'] = $value['seller']['email'] ?? "";
                } elseif ($value['type'] == 'tour') {
                    $getData[$key]['user_image'] = getValidImage(path: 'storage/app/public/tour_and_travels/doc/' . $value['Tour']['image'] ?? '', type: 'backend-profile');
                    $getData[$key]['user_name'] = $value['Tour']['owner_name'] ?? "";
                    $getData[$key]['user_phone'] = $value['Tour']['phone_no'] ?? "";
                    $getData[$key]['user_email'] = $value['Tour']['email'] ?? "";
                } elseif ($value['type'] == 'event') {
                    $getData[$key]['user_image'] = getValidImage(path: 'storage/app/public/event/organizer/' . $value['Event']['image'] ?? '', type: 'backend-profile');
                    $getData[$key]['user_name'] = $value['Event']['organizer_name'] ?? "";
                    $getData[$key]['user_phone'] = $value['Event']['contact_number'] ?? "";
                    $getData[$key]['user_email'] = $value['Event']['email_address'] ?? "";
                } elseif ($value['type'] == 'trust') {
                    $getData[$key]['user_image'] = getValidImage(path: 'storage/app/public/donate/trust/' . $value['Trust']['theme_image'] ?? '', type: 'backend-profile');
                    $getData[$key]['user_name'] = $value['Trust']['name'] ?? "";
                    $getData[$key]['user_phone'] = json_decode($value['Trust']['memberlist'] ?? '[]', true)[0]['member_phone_no'] ?? '';
                    $getData[$key]['user_email'] = $value['Trust']['trust_email'] ?? "";
                }
                $key++;
            }
        }
        return response()->json(['status' => 1, 'message' => 'get Successfully', 'data' => $getData], 200);
    }

    public function SupportCreateTicket(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ticket_id' => 'required|integer|exists:vendor_support_tickets,id',
            'query_title' => 'required',
            'message' => 'required',
        ], [
            'ticket_id.required' => 'ticket is Empty!',
            'query_title.required' => 'title is Empty!',
            'message.required' => 'message is Empty!',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            $type = optional(auth('seller-api')->user())->type ?? '';
            $vendorId = (($type == 'seller') ? auth('seller')->id() : optional(auth('seller-api')->user())->relation_id ?? '');

            $save_ticket = new \App\Models\VendorSupportTicketConv();
            $save_ticket->ticket_id = $request->ticket_id;
            $save_ticket->created_by = 'vendor';
            $save_ticket->type = $type;
            $save_ticket->vendor_id = $vendorId;
            $save_ticket->query_title = $request->query_title;
            $save_ticket->status = 'open';
            $save_ticket->save();

            $ticket_his = new \App\Models\VendorSupportTicketConvHis();
            $ticket_his->ticket_issue_id = $save_ticket->id;
            $ticket_his->sender_type = 'user';
            $ticket_his->message = $request->message;
            $ticket_his->save();
            \Illuminate\Support\Facades\DB::commit();

            return response()->json(['message' => 'Support ticket created successfully.', 'status'  => 1, 'data' => []], 200);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return response()->json(['error'   => 'Something went wrong!', 'status'  => 0, 'data' => [], 'message' => $e->getMessage()], 500);
        }
    }

    public function SupportgetTicketId(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:vendor_support_tickets_conv,id'
        ], [
            'id.required' => 'ticket is Empty!'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $type = optional(auth('seller-api')->user())->type ?? '';
        $vendorId = (($type == 'seller') ? auth('seller')->id() : optional(auth('seller-api')->user())->relation_id ?? '');

        $supportTicket = \App\Models\VendorSupportTicketConv::with(['seller', 'Trust', 'Event', 'Tour', 'TicketTitle', 'conversations'])->where('vendor_id', $vendorId)->find($request->id);
        $getData = [];
        if ($supportTicket) {
            \App\Models\VendorSupportTicketConvHis::where('ticket_issue_id', $request->id)->update(['read_user_status' => 1]);
            $getData['issue_name'] = $supportTicket['TicketTitle']['message'] ?? "";
            $getData['query_title'] = $supportTicket['query_title'] ?? '';
            $getData['status'] = $supportTicket['status'] ?? "";
            $getData['created_at'] = date('d M,Y h:i A', strtotime($supportTicket['created_at']));
            $getData['updated_at'] = date('d M,Y h:i A', strtotime($supportTicket['updated_at']));
            if ($supportTicket['type'] == 'seller') {
                $getData['user_image'] = getValidImage(path: 'storage/app/public/seller/' . $supportTicket['seller']['image'] ?? '', type: 'backend-profile');
                $getData['user_name'] = $supportTicket['seller']['f_name'] ?? "" . " " . $supportTicket['seller']['l_name'] ?? "";
                $getData['user_phone'] = $supportTicket['seller']['phone'] ?? "";
                $getData['user_email'] = $supportTicket['seller']['email'] ?? "";
            } elseif ($supportTicket['type'] == 'tour') {
                $getData['user_image'] = getValidImage(path: 'storage/app/public/tour_and_travels/doc/' . $supportTicket['Tour']['image'] ?? '', type: 'backend-profile');
                $getData['user_name'] = $supportTicket['Tour']['owner_name'] ?? "";
                $getData['user_phone'] = $supportTicket['Tour']['phone_no'] ?? "";
                $getData['user_email'] = $supportTicket['Tour']['email'] ?? "";
            } elseif ($supportTicket['type'] == 'event') {
                $getData['user_image'] = getValidImage(path: 'storage/app/public/event/organizer/' . $supportTicket['Event']['image'] ?? '', type: 'backend-profile');
                $getData['user_name'] = $supportTicket['Event']['organizer_name'] ?? "";
                $getData['user_phone'] = $supportTicket['Event']['contact_number'] ?? "";
                $getData['user_email'] = $supportTicket['Event']['email_address'] ?? "";
            } elseif ($supportTicket['type'] == 'trust') {
                $getData['user_image'] = getValidImage(path: 'storage/app/public/donate/trust/' . $supportTicket['Trust']['theme_image'] ?? '', type: 'backend-profile');
                $getData['user_name'] = $supportTicket['Trust']['name'] ?? "";
                $getData['user_phone'] = json_decode($supportTicket['Trust']['memberlist'] ?? '[]', true)[0]['member_phone_no'] ?? '';
                $getData['user_email'] = $supportTicket['Trust']['trust_email'] ?? "";
            }
            $getData['chat'] = [];
            foreach ($supportTicket['conversations'] as $keyp => $message) {
                $getData['chat'][$keyp]['sender_type']  = $message['sender_type'];
                $getData['chat'][$keyp]['created_at']  = date('d M,Y h:i A', strtotime($message['created_at']));
                $getData['chat'][$keyp]['message']  = $message['message'];
                if ($message['attached'] != null && count(json_decode($message['attached'])) > 0) {
                    foreach (json_decode($message['attached']) as $index => $photo) {
                        $getData['chat'][$keyp]['image'][$index] = getValidImage(path: 'storage/app/public/support-ticket/' . $photo, type: 'backend-basic');
                    }
                }
            }
            return response()->json(['status' => 1, 'message' => 'get Successfully', 'data' => $getData], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Not Found', 'data' => []], 200);
        }
    }

    public function SupportReply(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ticket_issue_id' => [
                'required',
                'integer',
                \Illuminate\Validation\Rule::exists('vendor_support_tickets_conv', 'id')->where('status', 'open'),
            ],
            'replay' => "required",
        ], [
            'ticket_issue_id.required' => 'ticket Id is Empty!',
            'replay.required' => 'message is Empty!'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            $type = optional(auth('seller-api')->user())->type ?? '';
            $vendorId = (($type == 'seller') ? auth('seller')->id() : optional(auth('seller-api')->user())->relation_id ?? '');

            $attachedPaths = [];
            if ($request->hasFile('image')) {
                foreach ($request->file('image') as $image) {
                    $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $image->storeAs('public/support-ticket', $imageName);
                    $attachedPaths[] = $imageName;
                }
            }

            $ticket_his = new \App\Models\VendorSupportTicketConvHis();
            $ticket_his->ticket_issue_id = $request->ticket_issue_id;
            $ticket_his->sender_type = 'user';
            $ticket_his->message = $request->replay;
            $ticket_his->attached = json_encode($attachedPaths);
            $ticket_his->save();
            \Illuminate\Support\Facades\DB::commit();
            return response()->json(['message' => 'Message sent successfully.', 'status'  => 1, 'data' => []], 200);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return response()->json(['error'   => 'Something went wrong!', 'status'  => 0, 'data' => [], 'message' => $e->getMessage()], 500);
        }
    }

    public function SupportTicketClose(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:vendor_support_tickets_conv,id',
        ], [
            'id.required' => 'Id is Empty!',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            $type = optional(auth('seller-api')->user())->type ?? '';
            $vendorId = (($type == 'seller') ? auth('seller')->id() : optional(auth('seller-api')->user())->relation_id ?? '');

            $ticket_his = \App\Models\VendorSupportTicketConv::where('vendor_id', $vendorId)->find($request->id);
            $ticket_his->status = $request->get('status', 'close');
            $ticket_his->save();

            \Illuminate\Support\Facades\DB::commit();

            return response()->json(['message' => 'Ticket closed successfully.', 'status'  => 1, 'data' => []], 200);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return response()->json(['error'   => 'Something went wrong!', 'status'  => 0, 'data' => [], 'message' => $e->getMessage()], 500);
        }
    }

    /////////////////////////////////////  Support Admin /////////////////////////////////////////////

    public function AdminSupportgetTicket(Request $request)
    {
        $type = optional(auth('seller-api')->user())->type ?? '';
        $vendorId = (($type == 'seller') ? auth('seller')->id() : optional(auth('seller-api')->user())->relation_id ?? '');
        $message_list = \App\Models\VendorSupportTicketConv::where(['created_by' => 'admin', 'type' => $type, 'vendor_id' => $vendorId])
            ->when(isset($request['status']) && ($request['status'] != 'all'), function ($query) use ($request) {
                return $query->where('status', $request['status']);
            })->with(['seller', 'Trust', 'Event', 'Tour', 'TicketTitle'])->get();
        if ($message_list) {
            $getData = [];
            $key = 0;
            foreach ($message_list as $value) {
                $getData[$key]['id'] = $value['id'];
                $getData[$key]['ticket_id'] = $value['ticket_id'];
                $getData[$key]['issue_name'] = $value['TicketTitle']['message'] ?? "";
                $getData[$key]['query_title'] = $value['query_title'];
                $getData[$key]['status'] = $value['status'];
                $getData[$key]['created_at'] = date('d M,Y h:i A', strtotime($value['created_at']));
                $getData[$key]['updated_at'] = date('d M,Y h:i A', strtotime($value['updated_at']));
                if ($value['type'] == 'seller') {
                    $getData[$key]['user_image'] = getValidImage(path: 'storage/app/public/seller/' . $value['seller']['image'] ?? '', type: 'backend-profile');
                    $getData[$key]['user_name'] = $value['seller']['f_name'] ?? "" . " " . $value['seller']['l_name'] ?? "";
                    $getData[$key]['user_phone'] = $value['seller']['phone'] ?? "";
                    $getData[$key]['user_email'] = $value['seller']['email'] ?? "";
                } elseif ($value['type'] == 'tour') {
                    $getData[$key]['user_image'] = getValidImage(path: 'storage/app/public/tour_and_travels/doc/' . $value['Tour']['image'] ?? '', type: 'backend-profile');
                    $getData[$key]['user_name'] = $value['Tour']['owner_name'] ?? "";
                    $getData[$key]['user_phone'] = $value['Tour']['phone_no'] ?? "";
                    $getData[$key]['user_email'] = $value['Tour']['email'] ?? "";
                } elseif ($value['type'] == 'event') {
                    $getData[$key]['user_image'] = getValidImage(path: 'storage/app/public/event/organizer/' . $value['Event']['image'] ?? '', type: 'backend-profile');
                    $getData[$key]['user_name'] = $value['Event']['organizer_name'] ?? "";
                    $getData[$key]['user_phone'] = $value['Event']['contact_number'] ?? "";
                    $getData[$key]['user_email'] = $value['Event']['email_address'] ?? "";
                } elseif ($value['type'] == 'trust') {
                    $getData[$key]['user_image'] = getValidImage(path: 'storage/app/public/donate/trust/' . $value['Trust']['theme_image'] ?? '', type: 'backend-profile');
                    $getData[$key]['user_name'] = $value['Trust']['name'] ?? "";
                    $getData[$key]['user_phone'] = json_decode($value['Trust']['memberlist'] ?? '[]', true)[0]['member_phone_no'] ?? '';
                    $getData[$key]['user_email'] = $value['Trust']['trust_email'] ?? "";
                }
                $key++;
            }
        }
        return response()->json(['status' => 1, 'message' => 'get Successfully', 'data' => $getData], 200);
    }

   
    
}