<?php

use App\Events\AddFundToWalletEvent;
use App\Models\Cart;
use App\Utils\CartManager;
use App\Utils\CustomerManager;
use App\Utils\Helpers;
use App\Utils\OrderManager;
use Illuminate\Support\Facades\DB;

if (!function_exists('digital_payment_success')) {
    function digital_payment_success($payment_data)
    {
        if (isset($payment_data) && $payment_data['is_paid'] == 1) {
            $unique_id = OrderManager::gen_unique_id();
            $order_ids = [];
            $additional_data = json_decode($payment_data['additional_data']);

            $data = [];
            if (isset($additional_data->payment_request_from) && in_array($additional_data->payment_request_from, ['app', 'react'])) {
                $data += [
                    'request' => [
                        'customer_id' => $additional_data->customer_id,
                        'is_guest' => $additional_data->is_guest ?? 0,
                        'guest_id' => $additional_data->is_guest ? $additional_data->customer_id : null,
                        'order_note' => $additional_data->order_note,
                        'coupon_code' => $additional_data->coupon_code ?? null,
                        'coupon_discount' => $additional_data->coupon_discount ?? null,
                        'address_id' => $additional_data->address_id ?? null,
                        'billing_address_id' => $additional_data->billing_address_id ?? null,
                        'payment_request_from' => $additional_data->payment_request_from,
                    ],
                ];

                if ($additional_data->is_guest) {
                    $cart_group_ids = Cart::where(['customer_id' => $additional_data->customer_id, 'is_guest' => 1])->groupBy('cart_group_id')->pluck('cart_group_id')->toArray();
                } else {
                    $cart_group_ids = Cart::where(['customer_id' =>  $additional_data->customer_id, 'is_guest' => '0'])->groupBy('cart_group_id')->pluck('cart_group_id')->toArray();
                }
            } else {
                $cart_group_ids = CartManager::get_cart_group_ids();
            }
            session()->put('payment_mode', isset($additional_data->payment_mode) ? $additional_data->payment_mode : 'web');

            foreach ($cart_group_ids as $group_id) {
                $data += [
                    'payment_method' => $payment_data['payment_method'],
                    'order_status' => 'confirmed',
                    'payment_status' => 'paid',
                    'transaction_ref' => $payment_data['transaction_id'],
                    'order_group_id' => $unique_id,
                    'cart_group_id' => $group_id
                ];
                $order_id = OrderManager::generate_order($data);
                unset($data['payment_method']);
                unset($data['cart_group_id']);
                array_push($order_ids, $order_id);
            }

            if (isset($additional_data->payment_request_from) && in_array($additional_data->payment_request_from, ['app', 'react'])) {
                CartManager::cart_clean_for_api_digital_payment($data);
            } else {
                CartManager::cart_clean();
            }
        }
    }
}

if (!function_exists('digital_payment_fail')) {
    function digital_payment_fail($payment_data) {}
}
if (!function_exists('digital_payment_success_custom')) {
    function digital_payment_success_custom($payment_data)
    {
        if (isset($payment_data) && $payment_data['is_paid'] == 1) {
            try {
                $additional_data = json_decode($payment_data['additional_data'], true);
                if (is_array($additional_data) && ($additional_data['qrData'] ?? "") && ($additional_data['qrData']['id'] ?? "")) {
                    $get_Razorpay = \App\Models\Setting::where(['key_name' => 'razor_pay'])->first();
                    if ($get_Razorpay['mode'] == 'live') {
                        $RAZORPAY_KEY_ID = $get_Razorpay['live_values']['api_key'];
                        $RAZORPAY_KEY_SECRET = $get_Razorpay['live_values']['api_secret'];
                        $RAZORPAY_ACCOUNT_NUMBER = $get_Razorpay['live_values']['account_number'] ?? '';
                    } else {
                        $RAZORPAY_KEY_ID = $get_Razorpay['test_values']['api_key'];
                        $RAZORPAY_KEY_SECRET = $get_Razorpay['test_values']['api_secret'];
                        $RAZORPAY_ACCOUNT_NUMBER = $get_Razorpay['test_values']['account_number'] ?? '';
                    }
                    $qrId = ($additional_data['qrData']['id'] ?? "");   // Razorpay QR id (like "qr_Hxxxxxx")
                    $url = "https://api.razorpay.com/v1/payments/qr_codes/$qrId/close";
                    $headers = [
                        "Content-Type: application/json",
                        "Authorization: Basic " . base64_encode("$RAZORPAY_KEY_ID:$RAZORPAY_KEY_SECRET")
                    ];
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                    $response = curl_exec($ch);
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    curl_close($ch);
                    if ($httpCode == 200 || $httpCode == 201) {
                        $closedQr = json_decode($response, true);
                    } else {
                    }
                }
            } catch (Exception $e) {
                error_log("Payment processing error: " . $e->getMessage());
            }
        }
    }
}

// Add Fund To Wallet - Success
if (!function_exists('add_fund_to_wallet_success')) {
    function add_fund_to_wallet_success($payment_data)
    {
        if (isset($payment_data) && $payment_data['is_paid'] == 1) {
            $additional_data = json_decode($payment_data['additional_data']);
            session()->put('payment_mode', isset($additional_data->payment_mode) ? $additional_data->payment_mode : 'web');

            $wallet_transaction = CustomerManager::create_wallet_transaction($payment_data['payer_id'], floatval($payment_data['payment_amount']), 'add_fund', 'add_funds_to_wallet', $payment_data);

            if ($wallet_transaction) {
                try {
                    $message_data = [
                        'customer_id' => $payment_data['payer_id'],
                        'final_amount' => number_format($payment_data['payment_amount'], 2),
                    ];
                    Helpers::whatsappMessage('whatsapp', 'Add Fund To Wallet', $message_data);
                    event(new AddFundToWalletEvent(email: $wallet_transaction->user['email'], walletTransaction: $wallet_transaction));
                } catch (\Exception $ex) {
                    info($ex);
                }
            }
        }
    }
}

// Add Fund To Wallet - Fail
if (!function_exists('add_fund_to_wallet_fail')) {
    function add_fund_to_wallet_fail($payment_data) {}
}

if (!function_exists('config_settings')) {
    function config_settings($key, $settings_type)
    {
        try {
            $config = DB::table('addon_settings')->where('key_name', $key)
                ->where('settings_type', $settings_type)->first();
        } catch (Exception $exception) {
            return null;
        }
        return (isset($config)) ? $config : null;
    }
}
