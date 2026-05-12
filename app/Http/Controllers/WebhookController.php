<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\DonationPayment;
use App\Models\DonationSubscription;
use App\Utils\Helpers;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->all();
        \Log::info('Razorpay Webhook Received:', $payload);
        if (!$this->verifyWebhookSignature($request)) {
            \Log::error('Webhook signature verification failed');
            return response()->json(['error' => 'Invalid signature'], 400);
        }
        $event = $payload['event'];
        switch ($event) {
            case 'subscription.created':
                return $this->handleSubscriptionCreated($payload);
            case 'subscription.pending':
                return $this->handleSubscriptionPending($payload);
            case 'subscription.activated':
                return $this->handleSubscriptionActivated($payload);

            case 'subscription.charged':
                return $this->handleSubscriptionCharged($payload);

            case 'payment.captured':
                return $this->handlePaymentCaptured($payload);

            case 'subscription.cancelled':
                return $this->handleSubscriptionCancelled($payload);

            case 'subscription.completed':
                return $this->handleSubscriptionCompleted($payload);
        }

        return response()->json(['status' => 'ignored']);
    }
    private function handleSubscriptionCreated(array $payload)
    {
        try {
            $subscriptionData = $payload['payload']['subscription']['entity'];
            $subscriptionId = $subscriptionData['id'];
            $status = $subscriptionData['status'];

            \Log::info('Subscription created', [
                'subscription_id' => $subscriptionId,
                'status' => $status
            ]);

            // Update your database record if needed
            $subscription = DonationSubscription::where('subscription_id', $subscriptionId)->first();

            if ($subscription) {
                $subscription->update([
                    'status' => $status,
                    'razorpay_response' => json_encode($subscriptionData),
                    'updated_at' => now()
                ]);
            }

            return response()->json(['status' => 'success', 'message' => 'Subscription created handled']);
        } catch (\Exception $e) {
            \Log::error('Error handling subscription created event', [
                'error' => $e->getMessage(),
                'subscription_id' => $subscriptionId ?? 'unknown'
            ]);

            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
    /**
     * Handle subscription pending event
     */
    private function handleSubscriptionPending(array $payload)
    {
        try {
            $subscriptionId = $payload['payload']['subscription']['entity']['id'];
            $status = $payload['payload']['subscription']['entity']['status'];

            \Log::info('Subscription pending', [
                'subscription_id' => $subscriptionId,
                'status' => $status,
                'payload' => $payload
            ]);
            $subscription = DonationSubscription::where('subscription_id', $subscriptionId)->first();

            if ($subscription) {
                $subscription->update([
                    'status' => 'pending',
                    'razorpay_response' => json_encode($payload),
                    'updated_at' => now()
                ]);
                \Log::info('Subscription marked as pending in database', [
                    'subscription_id' => $subscriptionId,
                    'donation_subscription_id' => $subscription->id
                ]);
            } else {
                \Log::warning('Subscription not found in database for pending event', [
                    'subscription_id' => $subscriptionId
                ]);
            }
            return response()->json(['status' => 'success', 'message' => 'Subscription pending handled']);
        } catch (\Exception $e) {
            \Log::error('Error handling subscription pending event', [
                'error' => $e->getMessage(),
                'subscription_id' => $subscriptionId ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    private function handleSubscriptionActivated($payload)
    {
        $subscription = $payload['payload']['subscription']['entity'];
        \Log::info('Subscription Activated:', $subscription);
        DonationSubscription::where('subscription_id', $subscription['id'])
            ->update([
                'status' => 'active',
                'current_start' => isset($subscription['current_start']) ?  date('Y-m-d H:i:s', $subscription['current_start']) : null,
                'current_end' => isset($subscription['current_end']) ?  date('Y-m-d H:i:s', $subscription['current_end']) : null,
                'start_at' => isset($subscription['start_at']) ?  date('Y-m-d H:i:s', $subscription['start_at']) : null,
                'ended_at' => isset($subscription['ended_at']) ?  date('Y-m-d H:i:s', $subscription['ended_at']) : null,
                'paid_count' => $subscription['paid_count'],
                'remaining_count' => $subscription['remaining_count'],
                'updated_at' => now()
            ]);
        $getData = DonationSubscription::where('subscription_id', $subscription['id'])->first();
        $message_data['booking_date'] = date('d-m-Y', strtotime($getData['current_start']));
        $message_data['frequency'] = $getData['frequency'];
        $message_data['final_amount'] = webCurrencyConverter(amount: (float)$getData['amount'] ?? 0);
        $message_data['customer_id'] =  $getData['user_id'];
        Helpers::whatsappMessage('donate', 'membership_active', $message_data);

        \Log::info('Database updated for subscription: ' . $subscription['id']);

        return response()->json(['status' => 'success']);
    }
    private function handleSubscriptionCharged($payload)
    {
        $subscription = $payload['payload']['subscription']['entity'];
        $payment = $payload['payload']['payment']['entity'];

        if (\App\Models\PaymentRequest::where('transaction_id', $payment['id'])->where('is_paid', 1)->exists()) {
            return response()->json(['status' => 'success']);
        }
        $getData = \App\Models\DonationSubscription::where('subscription_id', $subscription['id'])->first();

        $UserPaymant = new \App\Models\PaymentRequest();
        $currency_model = \App\Utils\Helpers::get_business_settings('currency_model');
        if ($currency_model == 'multi_currency') {
            $currency_code = 'USD';
        } else {
            $default = \App\Models\BusinessSetting::where(['type' => 'system_default_currency'])->first()->value;
            $currency_code = \App\Models\Currency::find($default)->code;
        }
        $customer = \App\Models\User::where("id", $getData['user_id'])->first();
        $payer = [
            "name" => $customer['f_name'] . ' ' . $customer['l_name'],
            "email" => $customer['email'],
            "phone" => $customer['phone'],
        ];

        $UserPaymant->payer_id = $getData['user_id'];
        $UserPaymant->receiver_id = '100';
        $UserPaymant->payment_amount = $payment['amount'] / 100;
        $UserPaymant->success_hook = 'digital_payment_success_custom';
        $UserPaymant->failure_hook = 'digital_payment_fail';
        $UserPaymant->transaction_id = $payment['id'];
        $UserPaymant->currency_code = $currency_code;
        $UserPaymant->payment_method = 'razor_pay';
        $UserPaymant->additional_data = json_encode([
            'subscription' => $subscription,
            'payment' => $payment
        ]);
        $UserPaymant->is_paid = 1;
        $UserPaymant->payer_information = json_encode($payer);
        $UserPaymant->external_redirect_link = null;
        $UserPaymant->receiver_information = json_encode(["name" => 'receiver_name', "image" => 'example.png']);
        $UserPaymant->attribute_id = idate("U");
        $UserPaymant->attribute = 'Donate';
        $UserPaymant->payment_platform = "web auto";
        $UserPaymant->created_at = date('Y-m-d H:i:s');
        $UserPaymant->updated_at = date('Y-m-d H:i:s');
        $UserPaymant->save();
        //////////////////////////////////////////////
        \Log::info('Subscription Charged:', [
            'subscription' => $subscription,
            'payment' => $payment
        ]);
        DonationSubscription::where('subscription_id', $subscription['id'])
            ->update([
                'paid_count' => $subscription['paid_count'],
                'remaining_count' => $subscription['remaining_count'],
                'current_start' => isset($subscription['current_start']) ?  date('Y-m-d H:i:s', $subscription['current_start']) : null,
                'current_end' => isset($subscription['current_end']) ?  date('Y-m-d H:i:s', $subscription['current_end']) : null,
                'updated_at' => now()
            ]);

        DonationPayment::create([
            'subscription_id' => $subscription['id'],
            'payment_id' => $payment['id'],
            'amount' => $payment['amount'] / 100,
            'currency' => $payment['currency'],
            'status' => $payment['status'],
            'method' => $payment['method'],
            'captured_at' => isset($payment['captured_at']) ?  date('Y-m-d H:i:s', $payment['captured_at']) : null,
            'created_at' => now()
        ]);
        $notes = $subscription['notes'] ?? [];
        $orderId = $notes['order_id'] ?? null;
        if (($notes['type'] ?? "") == 'donation') {
            $tran_saction = \App\Models\DonateAllTransaction::find($orderId);
            if ($tran_saction->amount_status == 0) {
                $tran_saction->amount_status = 1;
                $tran_saction->transaction_id = $payment['id'] ?? "";
                $tran_saction->save();

                $orderData = \App\Models\DonateAllTransaction::where('id', $tran_saction->id)->with(['getTrust', 'adsTrust'])->first();
                $message_data2['trust_name'] =  $orderData['getTrust']['trust_name'] ?? "Mahakal.com";
                $message_data2['ad_name'] =  $orderData['adsTrust']['name'] ?? '';
                $message_data2['booking_date'] =  date('d M,Y H:i A', strtotime($orderData['created_at']));
                $message_data2['order_amount'] =  $orderData['amount'];
                $message_data2['admin_commission'] =  $orderData['admin_commission'];
                $message_data2['final_amount'] =  $orderData['final_amount'];
                $message_data2['vendor_email'] =   $orderData['getTrust']['trust_email'] ?? "Mahakal.com";
                $message_data2['seller_id'] = \App\Models\Seller::where('relation_id', $orderData['trust_id'])->where('type', 'trust')->first()['id'] ?? 0;
                Helpers::whatsappMessage('donate', 'donation_trust_receipt', $message_data2);
            } else {
                $transaction = new \App\Models\DonateAllTransaction();
                $transaction->type = $tran_saction->type;
                $transaction->user_id = $tran_saction->user_id;
                $transaction->user_name = $tran_saction->user_name;
                $transaction->user_phone = $tran_saction->user_phone;
                $transaction->pan_card = $tran_saction->pan_card;
                $transaction->trust_id = $tran_saction->trust_id;
                $transaction->ads_id = $tran_saction->ads_id;
                $transaction->amount = $tran_saction->amount;
                $transaction->tax_amount =  $tran_saction->tax_amount;
                $transaction->admin_commission =  $tran_saction->admin_commission;
                $transaction->final_amount =  $tran_saction->final_amount;
                $transaction->amount_status = 1;
                $transaction->transaction_id = $payment['id'] ?? "";
                $transaction->information =  $tran_saction->information;
                $transaction->subscription_id = $tran_saction->subscription_id;
                $transaction->frequency = $tran_saction->frequency;
                $transaction->platform = $tran_saction->platform ?? "subscription";
                $transaction->save();

                $orderData = \App\Models\DonateAllTransaction::where('id', $transaction->id)->with(['getTrust', 'adsTrust'])->first();
                $message_data2['trust_name'] =  $orderData['getTrust']['trust_name'] ?? "Mahakal.com";
                $message_data2['ad_name'] =  $orderData['adsTrust']['name'] ?? '';
                $message_data2['booking_date'] =  date('d M,Y H:i A', strtotime($orderData['created_at']));
                $message_data2['order_amount'] =  $orderData['amount'];
                $message_data2['admin_commission'] =  $orderData['admin_commission'];
                $message_data2['final_amount'] =  $orderData['final_amount'];
                $message_data2['vendor_email'] =   $orderData['getTrust']['trust_email'] ?? "Mahakal.com";
                $message_data2['seller_id'] = \App\Models\Seller::where('relation_id', $orderData['trust_id'])->where('type', 'trust')->first()['id'] ?? 0;
                Helpers::whatsappMessage('donate', 'donation_trust_receipt', $message_data2);
                if (preg_match('/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/', strtoupper($orderData['pan_card'] ?? '')) && \App\Models\UserPanCardVerified::where('pan_number', strtoupper($orderData['pan_card'] ?? ''))->exists()) {
                    $message_data_pancard['customer_id'] =  $orderData['user_id'];
                    $message_data_pancard['person_phone'] =  $orderData['user_phone'];
                    $message_data_pancard['pan_card'] =  strtoupper($orderData['pan_card'] ?? '');
                    $orderData = \App\Models\DonateAllTransaction::where('id', $transaction->id)->where('user_id', $orderData['user_id'])->with(['users', 'getTrust', 'adsTrust'])->first();
                    if (empty($orderData['ertiga_certificate'] ?? '')) {
                        \App\Http\Controllers\RestAPI\v1\DonateController::create_donate_cetificate($orderData['id']);
                        $message_data_pancard['attachment']  = getValidImage(path: 'storage/app/public/donate/certificate/' . '80g_' . $orderData['trans_id'] . '.jpg', type: 'product');
                    } else {
                        $message_data_pancard['attachment']  = getValidImage(path: 'storage/app/public/donate/certificate/' . ($orderData['ertiga_certificate'] ?? ''), type: 'product');
                    }
                    $message_data_pancard['type'] = 'text-with-media';
                    Helpers::whatsappMessage('donate', 'Donation Success pdf', $message_data_pancard);
                }
            }
        }
        $getData = \App\Models\DonationSubscription::where('subscription_id', $subscription['id'])->first();
        $message_data['booking_date'] = date('d-m-Y', strtotime($getData['current_start']));
        $message_data['ended_at'] = date('d-m-Y', strtotime($getData['current_end']));
        $message_data['frequency'] = $getData['frequency'];
        $message_data['final_amount'] = webCurrencyConverter(amount: (float)$getData['amount'] ?? 0);
        $message_data['customer_id'] =  $getData['user_id'];
        $message_data['paid_count'] =  $getData['paid_count'];
        $message_data['remaining_count'] =  $getData['remaining_count'];
        Helpers::whatsappMessage('donate', 'membership_charged', $message_data);
        return response()->json(['status' => 'success']);
    }

    private function handlePaymentCaptured($payload)
    {
        $payment = $payload['payload']['payment']['entity'];
        \Log::info('Payment Captured:', $payment);
        return response()->json(['status' => 'success']);
    }

    private function handleSubscriptionCancelled($payload)
    {
        $subscription = $payload['payload']['subscription']['entity'];

        DonationSubscription::where('subscription_id', $subscription['id'])
            ->update([
                'status' => 'cancelled',
                'ended_at' => isset($subscription['ended_at']) ?  date('Y-m-d H:i:s', $subscription['ended_at']) : now(),
                'updated_at' => now()
            ]);
        $getData = DonationSubscription::where('subscription_id', $subscription['id'])->first();
        $message_data['booking_date'] = date('d-m-Y', strtotime($getData['current_start']));
        $message_data['ended_at'] = date('d-m-Y', strtotime($getData['ended_at']));
        $message_data['frequency'] = $getData['frequency'];
        $message_data['final_amount'] = webCurrencyConverter(amount: (float)$getData['amount'] ?? 0);
        $message_data['customer_id'] =  $getData['user_id'];
        $message_data['paid_count'] =  $getData['paid_count'];
        $message_data['remaining_count'] =  $getData['remaining_count'];
        Helpers::whatsappMessage('donate', 'membership_cancelled', $message_data);
        return response()->json(['status' => 'success']);
    }

    private function handleSubscriptionCompleted($payload)
    {
        $subscription = $payload['payload']['subscription']['entity'];

        DonationSubscription::where('subscription_id', $subscription['id'])
            ->update([
                'status' => 'completed',
                'ended_at' => isset($subscription['ended_at']) ?  date('Y-m-d H:i:s', $subscription['ended_at']) : now(),
                'updated_at' => now()
            ]);

        return response()->json(['status' => 'success']);
    }

    private function verifyWebhookSignature($request)
    {
        $webhookSecret =  \App\Models\BusinessSetting::where('type', 'RAZORPAY_WEBHOOK_SECRET')->first()['value'] ?? "";
        $razorpaySignature = $request->header('X-Razorpay-Signature');
        $payload = $request->getContent(); // raw JSON

        \Log::info('Webhook Secret:', [$webhookSecret]);
        \Log::info('Razorpay Signature:', [$razorpaySignature]);
        \Log::info('Payload:', [$payload]);

        if (!$webhookSecret || !$razorpaySignature) {
            return false;
        }
        $expectedSignature = hash_hmac('sha256', $payload, $webhookSecret);

        return hash_equals($expectedSignature, $razorpaySignature);
    }
}
