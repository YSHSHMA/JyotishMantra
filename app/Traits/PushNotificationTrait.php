<?php

namespace App\Traits;

use App\Models\NotificationMessage;
use App\Models\Order;
use phpDocumentor\Reflection\Type;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Google\Auth\HttpHandler\HttpHandlerFactory;

trait PushNotificationTrait
{
    use CommonTrait;

    /**
     * @param string $key
     * @param string $type
     * @param object|array $order
     * @return void
     * push notification order related
     */
    protected function sendOrderNotification(string $key, string $type, object|array $order): void
    {
        try {
            $lang = getDefaultLanguage();
            /** for customer  */
            if ($type == 'customer') {
                $fcmToken = $order->customer?->cm_firebase_token;
                $lang = $order->customer?->app_language ?? $lang;
                $value = $this->pushNotificationMessage($key, 'customer', $lang);
                $value = $this->textVariableDataFormat(value: $value, key: $key, userName: "{$order->customer?->f_name} {$order->customer?->l_name}", shopName: $order->seller?->shop?->name, deliveryManName: "{$order->deliveryMan?->f_name} {$order->deliveryMan?->l_name}", time: now()->diffForHumans(), orderId: $order->id);

                if ($fcmToken && $value) {
                    $data = [
                        'title' => translate('order'),
                        'description' => $value,
                        'order_id' => $order['id'],
                        'image' => '',
                        'type' => 'order'
                    ];

                    $this->sendPushNotificationToDevice($fcmToken, $data);
                }
            }
            /** end for customer  */
            /**for seller */
            if ($type == 'seller') {
                $sellerFcmToken = $order->seller?->cm_firebase_token;
                if ($sellerFcmToken) {
                    $lang = $order->seller?->app_language ?? $lang;
                    $value_seller = $this->pushNotificationMessage($key, 'seller', $lang);
                    $value_seller = $this->textVariableDataFormat(value: $value_seller, key: $key, userName: "{$order->customer?->f_name} {$order->customer?->l_name}", shopName: $order->seller?->shop?->name, deliveryManName: "{$order->deliveryMan?->f_name} {$order->deliveryMan?->l_name}", time: now()->diffForHumans(), orderId: $order->id);

                    if ($value_seller != null) {
                        $data = [
                            'title' => translate('order'),
                            'description' => $value_seller,
                            'order_id' => $order['id'],
                            'image' => '',
                            'type' => 'order'
                        ];

                        $this->sendPushNotificationToDevice($sellerFcmToken, $data);
                    }
                }
            }
            /**end for seller */
            /** for delivery man*/
            if ($type == 'delivery_man') {
                $fcmTokenDeliveryMan = $order->deliveryMan?->fcm_token;
                $lang = $order->deliveryMan?->app_language ?? $lang;
                $value_delivery_man = $this->pushNotificationMessage($key, 'delivery_man', $lang);
                $value_delivery_man = $this->textVariableDataFormat(value: $value_delivery_man, key: $key, userName: "{$order->customer?->f_name} {$order->customer?->l_name}", shopName: $order->seller?->shop?->name, deliveryManName: "{$order->deliveryMan?->f_name} {$order->deliveryMan?->l_name}", time: now()->diffForHumans(), orderId: $order->id);
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
                if ($fcmTokenDeliveryMan) {
                    $this->sendPushNotificationToDevice($fcmTokenDeliveryMan, $data);
                }
            }

            /** end delivery man*/
            /** for online puja*/
            if ($type == 'puja') {
                $fcmToken = $order->customer?->cm_firebase_token;
                $lang = $order->customer?->app_language ?? $lang;
                $value = $this->pushNotificationMessage($key, 'puja', $lang);
                $value = $this->textVariableDataFormat(value: $value, key: $key, userName: "{$order->customer?->f_name} {$order->customer?->l_name}", amount: "{$order->pay_amount}",  time: "{$order->schedule_time}", date: "{$order->booking_date}", orderId: $order->order_id);

                if ($fcmToken && $value) {
                    $data = [
                        'title' => translate('online puja'),
                        'description' => $value,
                        'order_id' => $order['order_id'],
                        'image' => '',
                        'type' => 'order'
                    ];
                    $this->sendPushNotificationToDevice($fcmToken, $data);
                }
            }
            /** end online puja*/
            /** for counselling*/
            if ($type == 'counselling') {
                $fcmToken = $order->customer?->cm_firebase_token;
                $lang = $order->customer?->app_language ?? $lang;
                $value = $this->pushNotificationMessage($key, 'counselling', $lang);
                $value = $this->textVariableDataFormat(value: $value, key: $key, userName: "{$order->customer?->f_name} {$order->customer?->l_name}", amount: "{$order->pay_amount}",  time: "{$order->schedule_time}", date: "{$order->booking_date}", orderId: $order->order_id);

                if ($fcmToken && $value) {
                    $data = [
                        'title' => translate('counselling'),
                        'description' => $value,
                        'order_id' => $order['order_id'],
                        'image' => '',
                        'type' => 'order'
                    ];
                    $this->sendPushNotificationToDevice($fcmToken, $data);
                }
            }
            /** end counselling*/
            /** for offlinepuja*/
            if ($type == 'offlinepuja') {
                $fcmToken = $order->customer?->cm_firebase_token;
                $lang = $order->customer?->app_language ?? $lang;
                $value = $this->pushNotificationMessage($key, 'offlinepuja', $lang);
                $value = $this->textVariableDataFormat(value: $value, key: $key, userName: "{$order->customer?->f_name} {$order->customer?->l_name}", amount: "{$order->pay_amount}",  time: "{$order->schedule_time}", date: "{$order->booking_date}", orderId: $order->order_id);
                // dd($value);

                if ($fcmToken && $value) {
                    $data = [
                        'title' => translate('offlinepuja'),
                        'description' => $value,
                        'order_id' => $order['order_id'],
                        'image' => '',
                        'type' => 'order'
                    ];
                    $this->sendPushNotificationToDevice($fcmToken, $data);
                }
            }
            /** end offlinepuja*/
        } catch (\Exception $e) {
        }
    }

    /**
     * chatting related push notification
     * @param string $key
     * @param string $type
     * @param object $userData
     * @param object $messageForm
     * @return void
     */
    protected function chattingNotification(string $key, string $type, object $userData, object $messageForm): void
    {
        try {
            $fcm_token = $type == 'delivery_man' ? $userData?->fcm_token : $userData?->cm_firebase_token;
            if ($fcm_token) {
                $lang = $userData?->app_language ?? getDefaultLanguage();
                $value = $this->pushNotificationMessage($key, $type, $lang);

                $value = $this->textVariableDataFormat(
                    value: $value,
                    key: $key,
                    userName: "{$messageForm?->f_name} {$messageForm?->l_name}",
                    shopName: $messageForm?->shop?->name,
                    deliveryManName: "{$messageForm?->f_name} {$messageForm?->l_name}",
                    time: now()->diffForHumans()
                );
                $data = [
                    'title' => translate('message'),
                    'description' => $value,
                    'order_id' => '',
                    'image' => '',
                    'type' => 'chatting'
                ];
                $this->sendPushNotificationToDevice($fcm_token, $data);
            }
        } catch (\Exception $exception) {
        }
    }
    protected function withdrawStatusUpdateNotification(string $key, string $type, string $lang, int $status, string $fcmToken): void
    {
        $value = $this->pushNotificationMessage($key, $type, $lang);
        if ($value != null) {
            $data = [
                'title' => translate('withdraw_request_' . ($status == 1 ? 'approved' : 'denied')),
                'description' => $value,
                'image' => '',
                'type' => 'notification'
            ];
            $this->sendPushNotificationToDevice($fcmToken, $data);
        }
    }
    protected function customerStatusUpdateNotification(string $key, string $type, string $lang, string $status, string $fcmToken): void
    {
        $value = $this->pushNotificationMessage($key, $type, $lang);
        if ($value != null) {
            $data = [
                'title' => translate('your_account_has_been' . '_' . $status),
                'description' => $value,
                'image' => '',
                'type' => 'block'
            ];
            $this->sendPushNotificationToDevice($fcmToken, $data);
        }
    }
    protected function productRequestStatusUpdateNotification(string $key, string $type, string $lang, string $fcmToken): void
    {
        $value = $this->pushNotificationMessage($key, $type, $lang);
        if ($value != null) {
            $data = [
                'title' => translate($key),
                'description' => $value,
                'image' => '',
                'type' => 'notification'
            ];
            $this->sendPushNotificationToDevice($fcmToken, $data);
        }
    }
    protected function cashCollectNotification(string $key, string $type, string $lang, float $amount, string $fcmToken): void
    {
        $value = $this->pushNotificationMessage($key, $type, $lang);
        if ($value != null) {
            $data = [
                'title' => currencyConverter($amount) . ' ' . translate('_cash_deposit'),
                'description' => $value,
                'image' => '',
                'type' => 'notification'
            ];
            $this->sendPushNotificationToDevice($fcmToken, $data);
        }
    }

    /**
     * push notification variable message format
     */
    protected function textVariableDataFormat($value, $key = null, $userName = null, $shopName = null, $deliveryManName = null, $time = null, $orderId = null, $amount = null, $date = null)
    {
        $data = $value;
        if ($data) {
            $order = $orderId ? Order::find($orderId) : null;
            $data = $userName ? str_replace("{userName}", $userName, $data) : $data;
            $data = $shopName ? str_replace("{shopName}", $shopName, $data) : $data;
            $data = $deliveryManName ? str_replace("{deliveryManName}", $deliveryManName, $data) : $data;
            $data = $key == 'expected_delivery_date' ? ($order ? str_replace("{time}", $order->expected_delivery_date, $data) : $data) : ($time ? str_replace("{time}", $time, $data) : $data);
            $data = $orderId ? str_replace("{orderId}", $orderId, $data) : $data;
            $data = $amount ? str_replace("{amount}", $amount, $data) : $data;
            $data = $time ? str_replace("{time}", $time, $data) : $data;
            $data = $date ? str_replace("{date}", $date, $data) : $data;
        }
        return $data;
    }

    /**
     * push notification variable message
     * @param string $key
     * @param string $userType
     * @param string $lang
     * @return false|int|mixed|void
     */
    protected function pushNotificationMessage(string $key, string $userType, string $lang)
    {
        try {
            $notificationKey = [
                'pending' => 'order_pending_message',
                'confirmed' => '       ',
                'processing' => 'order_processing_message',
                'out_for_delivery' => 'out_for_delivery_message',
                'delivered' => 'order_delivered_message',
                'returned' => 'order_returned_message',
                'failed' => 'order_failed_message',
                'canceled' => 'order_canceled',
                'order_refunded_message' => 'order_refunded_message',
                'refund_request_canceled_message' => 'refund_request_canceled_message',
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
                'product_request_approved_message' => 'product_request_approved_message',
                'product_request_rejected_message' => 'product_request_rejected_message',
                'customer_block_message' => 'customer_block_message',
                'customer_unblock_message' => 'customer_unblock_message',
                //online puja
                '0' => 'puja_order_pending_message',
                '1' => 'puja_order_completed',
                '2' => 'puja_order_cancel',
                '3' => 'puja_order_schedule_time',
                '4' => 'puja_order_live_now',
                '5' => 'puja_order_share_now',
                '6' => 'puja_order_rejected',

                // Counselling 
                'counselling_0' => 'counselling_order_pending_message',
                'counselling_1' => 'counselling_order_completed',
                'counselling_2' => 'counselling_order_cancel',

                // Offlinepuja 
                'offlinepuja_0' => 'offlinepuja_order_pending_message',
                'offlinepuja_1' => 'offlinepuja_order_completed',
                'offlinepuja_2' => 'offlinepuja_order_cancel',
                'offlinepuja_3' => 'offlinepuja_order_schedule_time',
                'offlinepuja_4' => 'offlinepuja_order_live_now',

            ];
            $data = NotificationMessage::with(['translations' => function ($query) use ($lang) {
                $query->where('locale', $lang);
            }])->where(['key' => $notificationKey[$key], 'user_type' => $userType])->first() ?? ["status" => 0, "message" => "", "translations" => []];
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

    /**
     * Device wise notification send
     * @param string $fcmToken
     * @param array $data
     * @return bool|string
     */

    // protected function sendPushNotificationToDevice(string $fcmToken, array $data): bool|string
    // {
    //     $key = getWebConfig(name: 'push_notification_key');
    //     $url = "https://fcm.googleapis.com/fcm/send";
    //     $header = array(
    //         "authorization: key=" . $key . "",
    //         "content-type: application/json"
    //     );

    //     if (isset($data['order_id']) == false) {
    //         $data['order_id'] = null;
    //     }

    //     $postData = '{
    //         "to" : "' . $fcmToken . '",
    //         "data" : {
    //             "title" :"' . $data['title'] . '",
    //             "body" : "' . $data['description'] . '",
    //             "image" : "' . $data['image'] . '",
    //             "order_id":"' . $data['order_id'] . '",
    //             "type":"' . $data['type'] . '",
    //             "is_read": 0
    //           },
    //           "notification" : {
    //             "title" :"' . $data['title'] . '",
    //             "body" : "' . $data['description'] . '",
    //             "image" : "' . $data['image'] . '",
    //             "order_id":"' . $data['order_id'] . '",
    //             "title_loc_key":"' . $data['order_id'] . '",
    //             "type":"' . $data['type'] . '",
    //             "is_read": 0,
    //             "icon" : "new",
    //             "sound" : "default"
    //           }
    //     }';

    //     $ch = curl_init();
    //     $timeout = 120;
    //     curl_setopt($ch, CURLOPT_URL, $url);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //     curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    //     curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

    //     // Get URL content
    //     $result = curl_exec($ch);
    //     // close handle to release resources
    //     curl_close($ch);

    //     return $result;
    // }

    protected function sendPushNotificationToDevice(string $fcmToken, array $data): bool|string
    {
        $projectId = 'manalsoftech-6807e'; // Your Firebase Project ID
        $serviceAccountPath = env('FIREBASE_CREDENTIALS');

        if (!file_exists($serviceAccountPath)) {
            return "Service account JSON file not found at path: $serviceAccountPath";
        }

        try {
            $credentials = new \Google\Auth\Credentials\ServiceAccountCredentials(
                'https://www.googleapis.com/auth/firebase.messaging',
                json_decode(file_get_contents($serviceAccountPath), true)
            );

            $httpHandler = \Google\Auth\HttpHandler\HttpHandlerFactory::build();
            $authToken = $credentials->fetchAuthToken($httpHandler);

            if (empty($authToken['access_token'])) {
                return "OAuth token not generated.";
            }

            $accessToken = $authToken['access_token'];
        } catch (\Exception $e) {
            return "Error generating OAuth token: " . $e->getMessage();
        }

        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

        $headers = [
            "Authorization: Bearer $accessToken",
            "Content-Type: application/json",
        ];

        $image = asset('storage/app/public/notification') . '/' . ($data['image'] ?? 'default.png');

        $payload = [
            'message' => [
                'token' => $fcmToken,
                'notification' => [
                    'title' => $data['title'] ?? 'Notification',
                    'body' => $data['description'] ?? 'Description',
                    'image' => $image,
                ],
                'data' => [
                    'title' => $data['title'] ?? '',
                    'body' => $data['description'] ?? '',
                    'image' => $image,
                    'order_id' => (string)($data['order_id'] ?? ''),
                    'type' => $data['type'] ?? 'notification',
                    'is_read' => '0',
                ],
            ],
        ];
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $responseData = json_decode($result, true);
        curl_close($ch);

        if ($httpCode === 200) {
            return "Notification sent successfully.";
        }

        $errorMessage = $responseData['error']['message'] ?? 'Unknown error';
        $errorCode = $responseData['error']['code'] ?? $httpCode;

        \Log::error("FCM Device Error - Code: {$errorCode}, Message: {$errorMessage}");

        return "Failed to send notification. HTTP Code: {$httpCode}. Firebase Error: {$errorCode} - {$errorMessage}";
    }

    /**
     * Device wise notification send
     * @param array $data
     * @param string $topic
     * @return bool|string
     */
    // protected function sendPushNotificationToTopic(array|object $data, string $topic = 'sixvalley'): bool|string
    // {
    //     $key = getWebConfig(name: 'push_notification_key');

    //     $url = "https://fcm.googleapis.com/fcm/send";
    //     $header = ["authorization: key=" . $key . "",
    //         "content-type: application/json",
    //     ];

    //     $image = asset('storage/app/public/notification') . '/' . $data['image'];
    //     $postData = '{
    //         "to" : "/topics/' . $topic . '",
    //         "data" : {
    //             "title":"' . $data->title . '",
    //             "body" : "' . $data->description . '",
    //             "image" : "' . $image . '",
    //             "type":"notification",
    //             "is_read": 0
    //           },
    //           "notification" : {
    //             "title":"' . $data->title . '",
    //             "body" : "' . $data->description . '",
    //             "image" : "' . $image . '",
    //             "title_loc_key":null,
    //             "type":"notification",
    //             "is_read": 0,
    //             "icon" : "new",
    //             "sound" : "default"
    //           }
    //     }';

    //     $ch = curl_init();
    //     $timeout = 120;
    //     curl_setopt($ch, CURLOPT_URL, $url);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //     curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    //     curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

    //     // Get URL content
    //     $result = curl_exec($ch);
    //     // close handle to release resources
    //     curl_close($ch);

    //     return $result;
    // }

    protected function sendPushNotificationToTopic(array|object $data, string $topic = 'sixvalley'): bool|string
    {
        $projectId = 'manalsoftech-6807e'; // Your Firebase Project ID
        $serviceAccountPath = env('FIREBASE_CREDENTIALS');

        if (!file_exists($serviceAccountPath)) {
            return "Service account JSON file not found at path: $serviceAccountPath";
        }

        try {
            $credentials = new ServiceAccountCredentials(
                'https://www.googleapis.com/auth/firebase.messaging',
                json_decode(file_get_contents($serviceAccountPath), true)
            );

            $httpHandler = HttpHandlerFactory::build();
            $authToken = $credentials->fetchAuthToken($httpHandler);

            if (empty($authToken['access_token'])) {
                return "OAuth token not generated.";
            }

            $accessToken = $authToken['access_token'];
        } catch (\Exception $e) {
            return "Error generating OAuth token: " . $e->getMessage();
        }
             //  Fetch slug dynamically based on type + service_id
        $slug = null;
        try {
            if (!empty($data['service_id']) && !empty($data['type'])) {
                switch ($data['type']) {
                    case 'puja':
                        $slug = \App\Models\Service::where('id', $data['service_id'])->value('slug');
                        break;

                    case 'vip':
                        $slug = \App\Models\Vippooja::where('id', $data['service_id'])->value('slug');
                        break;

                    case 'anushthan':
                        $slug = \App\Models\Vippooja::where('id', $data['service_id'])->value('slug');
                        break;

                    case 'chadhava':
                        $slug = \App\Models\Chadhava::where('id', $data['service_id'])->value('slug');
                        break;

                    case 'offlinepuja':
                        $slug = \App\Models\PoojaOffline::where('id', $data['service_id'])->value('slug');
                        break;

                    case 'consultancy':
                        $slug = \App\Models\Service::where('id', $data['service_id'])->value('slug');
                        break;

                    case 'event':
                        $slug = \App\Models\Events::where('id', $data['service_id'])->value('slug');
                        break;

                    case 'darshan':
                        $slug = \App\Models\Temple::where('id', $data['service_id'])->value('slug');
                        break;

                    case 'tour':
                        $slug = \App\Models\TourVisits::where('id', $data['service_id'])->value('slug');
                        break;

                    case 'donation':
                        $slug = \App\Models\DonateAds::where('id', $data['service_id'])->value('slug');
                        break;

                    case 'product':
                        $slug = \App\Models\Product::where('id', $data['service_id'])->value('slug');
                        break;
                }
            }
        } catch (\Exception $ex) {
            \Log::error("Slug fetch failed: " . $ex->getMessage());
        }

        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

        $headers = [
            "Authorization: Bearer $accessToken",
            "Content-Type: application/json",
        ];

        $image = asset('storage/app/public/notification') . '/' . ($data['image'] ?? 'default.png');

        $payload = [
            'message' => [
                'topic' => $topic,
                'notification' => [
                    'title' => $data['title'] ?? 'Notification',
                    'body' => $data['description'] ?? 'Description',
                    'image' => $image,
                ],
                'data' => [
                    'type' => 'notification',
                    'notification_type'=> $data['type'] ?? '',
                    'service_id'=> $data['service_id'] ?? '',
                        'slug' => $slug ?? '', // add slug here
                    'is_read' => '0',
                    'order_id' => (string)($data['order_id'] ?? ''),
                ],
            ],
        ];
        // dd($payload,$data,$slug);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $responseData = json_decode($result, true);
        curl_close($ch);

        if ($httpCode === 200) {
            return "Notification sent successfully.";
        }

        $errorMessage = $responseData['error']['message'] ?? 'Unknown error';
        $errorCode = $responseData['error']['code'] ?? $httpCode;

        \Log::error("FCM Error - Code: {$errorCode}, Message: {$errorMessage}");

        return "Failed to send notification. HTTP Code: {$httpCode}. Firebase Error: {$errorCode} - {$errorMessage}";
    }
    
    public static function sendFirebasePushNotification($data, string $deviceToken = ''): bool|string
    {
        $key = config('services.firebase.server_key');

        $url = "https://fcm.googleapis.com/fcm/send";

        // Headers for the request
        $header = [
            "Authorization: key=" . $key,
            "Content-Type: application/json",
        ];

        if (is_object($data)) {
            $data = (array) $data;
        }

        $image = isset($data['image']) ? asset('storage/app/public/notification') . '/' . $data['image'] : null;

        // Check if device token is provided (to send to a specific device)
        if (empty($deviceToken)) {
            return 'Device token is required to send notification.';
        }

        $postData = [
            'to' => $deviceToken,  // Send to a specific device token
            'data' => [
                'title' => $data['title'] ?? 'Default Title',
                'body' => $data['description'] ?? 'Default description',
                'image' => $image,
                'type' => 'notification',
                'is_read' => 0,
            ],
            'notification' => [
                'title' => $data['title'] ?? 'Default Title',
                'body' => $data['description'] ?? 'Default description',
                'image' => $image,
                'type' => 'notification',
                'is_read' => 0,
                'icon' => 'new',
                'sound' => 'default',
            ]
        ];

        // Encode the data to JSON format
        $jsonPostData = json_encode($postData);

        // cURL initialization
        $ch = curl_init();
        $timeout = 120;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPostData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        // Get the response from FCM
        $result = curl_exec($ch);

        // Check if there was an error with the cURL request
        if ($result === false) {
            $error = curl_error($ch);
            curl_close($ch);
            return "cURL Error: " . $error;
        }

        // Close cURL handle
        curl_close($ch);

        // Optionally, decode and check the response for success
        $responseData = json_decode($result, true);

        if (isset($responseData['success']) && $responseData['success'] > 0) {
            return true;
        } else {
            return isset($responseData['results'][0]['error']) ? $responseData['results'][0]['error'] : 'Unknown error';
        }
    }
}
