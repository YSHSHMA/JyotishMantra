<?php

namespace App\Http\Controllers\RestAPI\v4;

use App\Events\ChattingEvent;
use App\Http\Controllers\Controller;
use App\Models\Chatting;
use App\Models\DeliveryMan;
use App\Models\Seller;
use App\User;
use App\Utils\Helpers;
use App\Utils\ImageManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller
{
    public function list(Request $request, $type)
    {

        if ($type == 'delivery-man') {
            $idParam = 'delivery_man_id';
            $with = 'deliveryMan';
        } elseif ($type == 'seller') {
            $idParam = 'seller_id';
            $with = 'sellerInfo.shops';
        } else {
            return response()->json(['message' => 'Invalid Chatting Type!'], 403);
        }

        $totalSize = Chatting::where(['user_id' => $request->user()->id])
            ->whereNotNull($idParam)
            ->select($idParam)
            ->distinct()
            ->count();

        $allChatIds = Chatting::where(['user_id' => $request->user()->id])
            ->whereNotNull($idParam)
            ->select($idParam)
            ->latest()
            ->get()
            ->unique($idParam)
            ->toArray();

        $uniqueChatIds = array_slice(array_values($allChatIds), $request['offset'], $request['limit']);

        $chats = array();
        if ($uniqueChatIds) {
            foreach ($uniqueChatIds as $uniqueChatId) {
                $userChatting = Chatting::with([$with])
                    ->where(['user_id' => $request->user()->id, $idParam => $uniqueChatId[$idParam]])
                    ->whereNotNull($idParam)
                    ->latest()
                    ->first();

                $userChatting->unseen_message_count = Chatting::where(['user_id'=>$userChatting->user_id, $idParam=>$userChatting->$idParam, 'seen_by_customer'=>'0'])->count();
                $chats[] = $userChatting;
            }
        }

        $data = array();
        $data['total_size'] = $totalSize;
        $data['chat'] = $chats;

        return response()->json($data, 200);
    }

    public function search(Request $request, $type)
    {
        $terms = explode(" ", $request->input('search'));
        if ($type == 'seller') {
            $id_param = 'seller_id';
            $with_param = 'seller_info.shops';
            $users = Seller::when($request->search, function ($query) use ($terms) {
                foreach ($terms as $term) {
                    $query->where('f_name', 'like', '%' . $term . '%')
                        ->orWhere('l_name', 'like', '%' . $term . '%');
                }
            })->pluck('id')->toArray();

        } elseif ($type == 'delivery-man') {
            $with_param = 'delivery_man';
            $id_param = 'delivery_man_id';
            $users = DeliveryMan::when($request->search, function ($query) use ($terms) {
                foreach ($terms as $term) {
                    $query->where('f_name', 'like', '%' . $term . '%')
                        ->orWhere('l_name', 'like', '%' . $term . '%');
                }
            })->pluck('id')->toArray();
        } else {
            return response()->json(['message' => 'Invalid Chatting Type!'], 403);
        }

        $unique_chat_ids = Chatting::where(['user_id' => $request->user()->id])
            ->whereIn($id_param, $users)
            ->select($id_param)
            ->distinct()
            ->get()
            ->toArray();
        $unique_chat_ids = call_user_func_array('array_merge', $unique_chat_ids);

        $chats = array();
        if ($unique_chat_ids) {
            foreach ($unique_chat_ids as $unique_chat_id) {
                $chats[] = Chatting::with([$with_param])
                    ->where(['user_id' => $request->user()->id, $id_param => $unique_chat_id])
                    ->whereNotNull($id_param)
                    ->latest()
                    ->first();
            }
        }

        return response()->json($chats, 200);
    }

    public function get_message(Request $request, $type, $id)
    {
        $validator = Validator::make($request->all(), [
            'offset' => 'required',
            'limit' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        if ($type == 'delivery-man') {
            $id_param = 'delivery_man_id';
            $sent_by = 'sent_by_delivery_man';
            $with = 'deliveryMan';
        } elseif ($type == 'seller') {
            $id_param = 'seller_id';
            $sent_by = 'sent_by_seller';
            $with = 'sellerInfo.shops';

        } else {
            return response()->json(['message' => 'Invalid Chatting Type!'], 403);
        }

        $query = Chatting::with($with)->where(['user_id' => $request->user()->id, $id_param => $id])->latest();

        if (!empty($query->get())) {
            $message = $query->paginate($request->limit, ['*'], 'page', $request->offset);
            $message?->map(function ($conversation) {
                $conversation->attachment = $conversation->attachment ? json_decode($conversation->attachment) : [];
            });

            $query->where($sent_by, 1)->update(['seen_by_customer' => 1]);

            $data = array();
            $data['total_size'] = $message->total();
            $data['limit'] = $request->limit;
            $data['offset'] = $request->offset;
            $data['message'] = array_reverse($message->items());
            return response()->json($data, 200);
        }
        return response()->json(['message' => 'No messages found!'], 200);

    }

    public function send_message(Request $request, $type)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $image = [] ;
        if ($request->file('images')) {
            foreach ($request['images'] as $key=>$value) {
                $image_name = ImageManager::upload('chatting/', 'webp', $value);
                $image[] = $image_name;
            }
        }

        $message_form = User::find($request->user()->id);

        $chatting = new Chatting();
        $chatting->user_id = $request->user()->id;
        $chatting->message = $request->message;
        $chatting->attachment = json_encode($image);
        $chatting->sent_by_customer = 1;
        $chatting->seen_by_customer = 1;

        if ($type == 'seller') {
            $seller = Seller::with('shop')->find($request->id);
            $chatting->seller_id = $request->id;
            $chatting->shop_id = $seller->shop->id;
            $chatting->seen_by_seller = 0;

            ChattingEvent::dispatch('message_from_customer', 'seller', $seller, $message_form);
        } elseif ($type == 'delivery-man') {
            $chatting->delivery_man_id = $request->id;
            $chatting->seen_by_delivery_man = 0;

            $delivery_man = DeliveryMan::find($request->id);
            ChattingEvent::dispatch('message_from_customer', 'delivery_man', $delivery_man, $message_form);
        } else {
            return response()->json('Invalid Chatting Type!', 403);
        }

        if ($chatting->save()) {
            return response()->json(['message' => $request->message, 'time' => now()], 200);
        } else {
            return response()->json(['message' => 'Message sending failed'], 403);
        }
    }
}
