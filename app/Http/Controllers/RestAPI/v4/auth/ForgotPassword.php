<?php

namespace App\Http\Controllers\RestAPI\v4\auth;

use App\Events\PasswordResetMailEvent;
use App\Http\Controllers\Controller;
use App\Models\PasswordReset;
use App\User;
use App\Utils\Helpers;
use App\Utils\SMS_module;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Modules\Gateways\Traits\SmsGateway;

class ForgotPassword extends Controller
{
    public function reset_password_request(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'identity' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $verification_by = Helpers::get_business_settings('forgot_password_verification');
        $otp_interval_time = Helpers::get_business_settings('otp_resend_time') ?? 1; //second

        $password_verification_data = PasswordReset::where(['user_type'=>'customer'])->where('identity', 'like', "%{$request['identity']}%")->latest()->first();
        if ($verification_by == 'email') {
            $customer = User::Where(['email' => $request['identity']])->first();
            if (isset($customer)) {
                if(isset($password_verification_data) &&  Carbon::parse($password_verification_data->created_at)->diffInSeconds() < $otp_interval_time){
                    $time= $otp_interval_time - Carbon::parse($password_verification_data->created_at)->diffInSeconds();

                    return response()->json([
                        'status'=>0,
                        'message' => translate('please_try_again_after').' '.CarbonInterval::seconds($time)->cascade()->forHumans()
                    ], 403);
                }else {
                    $token = Str::random(120);
                    $reset_data = PasswordReset::where(['identity' => $customer['email']])->latest()->first();
                    if($reset_data){
                        $reset_data->token = $token;
                        $reset_data->created_at = now();
                        $reset_data->updated_at = now();
                        $reset_data->save();
                    }else{
                        $reset_data = new PasswordReset();
                        $reset_data->identity = $customer['email'];
                        $reset_data->token = $token;
                        $reset_data->user_type = 'customer';
                        $reset_data->created_at = now();
                        $reset_data->updated_at = now();
                        $reset_data->save();
                    }

                    $reset_url = $request['reset_url'] . '&token=' . $token;

                    $emailServices_smtp = Helpers::get_business_settings('mail_config');
                    if ($emailServices_smtp['status'] == 0) {
                        $emailServices_smtp = Helpers::get_business_settings('mail_config_sendgrid');
                    }
                    if ($emailServices_smtp['status'] == 1) {
                        try{
                            PasswordResetMailEvent::dispatch($customer['email'], $reset_url);
                            $response = 'Check your email';
                            $status = 1;
                        } catch (\Exception $exception) {
                            return response()->json([
                                'status'=>0,
                                'message' => translate('email_is_not_configured'). translate('contact_with_the_administrator')
                            ], 403);
                        }
                    } else {
                        $status = 0;
                        $response = translate('email_sent_failed');
                    }
                    return response()->json(['status'=>$status,'message' => $response], 200);
                }
            }
        } elseif ($verification_by == 'phone') {
            $customer = User::where('phone', 'like', "%{$request['identity']}%")->first();
            if (isset($customer)) {
                if(isset($password_verification_data) &&  Carbon::parse($password_verification_data->created_at)->diffInSeconds() < $otp_interval_time){
                    $time= $otp_interval_time - Carbon::parse($password_verification_data->created_at)->diffInSeconds();

                    return response()->json([
                        'status'=>0,
                        'message' => translate('please_try_again_after').' '.CarbonInterval::seconds($time)->cascade()->forHumans()
                    ], 200);
                }else {
                    $token = rand(1000, 9999);
                    $reset_data = PasswordReset::where(['identity' => $customer['phone']])->latest()->first();
                    if($reset_data){
                        $reset_data->token = $token;
                        $reset_data->created_at = now();
                        $reset_data->updated_at = now();
                        $reset_data->save();
                    }else{
                        $reset_data = new PasswordReset();
                        $reset_data->identity = $customer['phone'];
                        $reset_data->token = $token;
                        $reset_data->user_type = 'customer';
                        $reset_data->created_at = now();
                        $reset_data->updated_at = now();
                        $reset_data->save();
                    }

                    $otp_resend_time = Helpers::get_business_settings('otp_resend_time') > 0 ? Helpers::get_business_settings('otp_resend_time') : 0;
                    $token_time = Carbon::parse($reset_data->created_at);
                    $convert_time = $token_time->addSeconds($otp_resend_time);
                    $time_count = $convert_time > Carbon::now() ? Carbon::now()->diffInSeconds($convert_time) : 0;

                    $published_status = 0;
                    $payment_published_status = config('get_payment_publish_status');
                    if (isset($payment_published_status[0]['is_published'])) {
                        $published_status = $payment_published_status[0]['is_published'];
                    }

                    if($published_status == 1){
                        SmsGateway::send($customer->phone, $token);
                    }else{
                        SMS_module::send($customer->phone, $token);
                    }
                    return response()->json(['status'=>1,'message' => translate('otp_sent_successfully'), 'new_time'=>$time_count], 200);
                }
            }
        }
        return response()->json(['errors' => [
            ['status'=>0,'code' => 'not-found', 'message' => translate('user_not_found').'!']
        ]], 403);
    }

    public function resend_otp(Request $request){
        $customer = User::where('phone', 'like', '%'.$request['identity'].'%')->first();
        if ($customer) {
            $token_info = PasswordReset::where(['user_type'=>'customer', 'identity'=> $customer->phone])->first();
            $otp_interval_time = Helpers::get_business_settings('otp_resend_time') ?? 1; //minute
            if(isset($token_info) &&  Carbon::parse($token_info->created_at)->diffInSeconds() < $otp_interval_time){
                $time= $otp_interval_time - Carbon::parse($token_info->created_at)->diffInSeconds();

                return response()->json([
                    'status'=>0,
                    'message'=> translate('please_try_again_after').' '.CarbonInterval::seconds($time)->cascade()->forHumans()
                ]);
            }else {
                $token = rand(1000, 9999);
                $token_info->identity = $customer['phone'];
                $token_info->token = $token;
                $token_info->otp_hit_count = 0;
                $token_info->is_temp_blocked = 0;
                $token_info->temp_block_time = null;
                $token_info->created_at = now();
                $token_info->save();

                $published_status = 0;
                $payment_published_status = config('get_payment_publish_status');
                if (isset($payment_published_status[0]['is_published'])) {
                    $published_status = $payment_published_status[0]['is_published'];
                }

                $response = '';
                if($published_status == 1){
                    $response = SmsGateway::send($customer->phone, $token);
                }else{
                    $response = SMS_module::send($customer->phone, $token);
                }

                if ($response == "not_found") {
                    return response()->json([
                        'status'=>0,
                        'message'=> translate('sms_configuration_missing')
                    ], 403);
                }else{
                    return response()->json([
                        'status' => 1,
                        'new_time' => $otp_interval_time,
                        'message'=>translate('otp_sent_successfully')
                    ]);
                }
            }
        }else{
            return response()->json([
                'status'=>0,
                'message'=>translate('invalid_user')
            ], 403);
        }
    }

    public function verify_email_token(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $data = DB::table('password_resets')->where('user_type','customer')->where(['token' => $request['token']])->first();
        if (isset($data)) {
            return response()->json(['identity'=>$data->identity,'status'=>1, 'message' => translate('token_verified')], 200);
        }
        return response()->json(['status'=>0, 'message' => translate('invalid_credentials')], 403);
    }

    public function otp_verification_submit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'identity' => 'required',
            'otp' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator), 'status'=>0], 403);
        }

        $max_otp_hit = Helpers::get_business_settings('maximum_otp_hit') ?? 5;
        $temp_block_time = Helpers::get_business_settings('temporary_block_time') ?? 5; // minute
        $id = $request['identity'];
        $password_reset_token = PasswordReset::where('user_type','customer')->where(['token' => $request['otp']])
            ->where('identity', 'like', "%{$id}%")
            ->first();

        if (isset($password_reset_token)) {
            if (isset($password_reset_token->temp_block_time) && Carbon::parse($password_reset_token->temp_block_time)->DiffInSeconds() <= $temp_block_time) {
                $time = $temp_block_time - Carbon::parse($password_reset_token->temp_block_time)->DiffInSeconds();

                return response()->json(['errors' => [
                    ['status'=>0, 'code' => 'not-found', 'message' => translate('please_try_again_after').' '.CarbonInterval::seconds($time)->cascade()->forHumans()]
                ]], 403);
            }

            return response()->json(['message' => translate('otp_verified'), 'status'=>1], 200);

        } else {
            $password_reset = PasswordReset::where(['user_type' => 'customer'])
                ->where('identity', 'like', "%{$id}%")
                ->latest()
                ->first();

            if ($password_reset) {
                if (isset($password_reset->temp_block_time) && Carbon::parse($password_reset->temp_block_time)->diffInMinutes() <= $temp_block_time) {
                    $time = $temp_block_time - Carbon::parse($password_reset->temp_block_time)->diffInMinutes();

                    $message = translate('please_try_again_after').' '.CarbonInterval::seconds($time)->cascade()->forHumans();

                } elseif ($password_reset->is_temp_blocked == 1 && Carbon::parse($password_reset->created_at)->diffInMinutes() >= $temp_block_time) {
                    $password_reset->otp_hit_count = 1;
                    $password_reset->is_temp_blocked = 0;
                    $password_reset->temp_block_time = null;
                    $password_reset->updated_at = now();
                    $password_reset->save();

                    $message = translate('invalid_otp');

                } elseif ($password_reset->otp_hit_count >= $max_otp_hit && $password_reset->is_temp_blocked == 0) {
                    $password_reset->is_temp_blocked = 1;
                    $password_reset->temp_block_time = now();
                    $password_reset->updated_at = now();
                    $password_reset->save();

                    $time = $temp_block_time - Carbon::parse($password_reset->temp_block_time)->DiffInSeconds();

                    $message = translate('too_many_attempts') . translate('please_try_again_after').' '.CarbonInterval::seconds($time)->cascade()->forHumans();

                } else {
                    $password_reset->otp_hit_count += 1;
                    $password_reset->save();

                    $message = translate('invalid_otp');
                }

                return response()->json(['errors' => [
                    ['code' => 'not-found', 'message' => $message, 'status'=>0 ]
                ]], 403);
            } else {
                return response()->json(['errors' => [
                    ['code' => 'not-found', 'message' => translate('invalid_otp') ]
                ], 'status'=>0], 403);
            }
        }

    }

    public function reset_password_submit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'identity' => 'required',
            'otp' => 'required',
            'password' => 'required|same:confirm_password|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $data = DB::table('password_resets')
            ->where('user_type','customer')
            ->where('identity', 'like', "%{$request['identity']}%")
            ->where(['token' => $request['otp']])->first();

        if (isset($data)) {
            User::where('email', 'like', "%{$data->identity}%")
                ->orWhere('phone', 'like', "%{$data->identity}%")
                ->update([
                    'password' => bcrypt(str_replace(' ', '', $request['password']))
                ]);

            DB::table('password_resets')
                ->where('user_type','customer')
                ->where('identity', 'like', "%{$request['identity']}%")
                ->where(['token' => $request['otp']])->delete();

            return response()->json(['status'=>1,'message' => translate('password_changed_successfully')], 200);
        }
        return response()->json(['errors' => [
            ['status'=>0,'code' => 'invalid', 'message' => translate('invalid_token')]
        ]], 400);
    }
}
