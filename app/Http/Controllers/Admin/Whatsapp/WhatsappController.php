<?php

namespace App\Http\Controllers\Admin\Whatsapp;

use App\Http\Controllers\Controller;
use App\Traits\Whatsapp;
use App\Traits\PdfGenerator;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\WhatsappTemplate;
use App\Models\WEcomTemplate;
use App\Models\WEventTemplate;
use App\Models\WDonationTemplate;
use App\Models\WToursTemplate;
use App\Models\WChadhavaTemplate;
use App\Models\WConsultancyTemplate;
use App\Models\WOfflinePoojaTemplate;
use App\Models\WVIPAnushthanTemplate;
use Brian2694\Toastr\Facades\Toastr;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\View as PdfView;
use Intervention\Image\Facades\Image;
use App\Jobs\SendAllWhatsapp;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\WKundaliTemplate;
use App\Models\WTempleDarshanTemplate;
use App\Utils\Helpers;

class WhatsappController extends Controller
{
    use PdfGenerator;
    use Whatsapp;
    public function offline_pooja_template(Request $request)
    {
        $whatsapp = WOfflinePoojaTemplate::where('status', 1)->get();
        return view('admin-views.whatsapp.offline-pooja-template', compact('whatsapp'));
    }
    public function pooja_template(Request $request)
    {
        $whatsapp = WhatsappTemplate::where('status', 1)->get();
        return view('admin-views.whatsapp.pooja-template', compact('whatsapp'));
    }
    public function vip_anushthan_template(Request $request)
    {
        $whatsapp = WVIPAnushthanTemplate::where('status', 1)->get();
        return view('admin-views.whatsapp.vip-anushthan-template', compact('whatsapp'));
    }
    public function chadhava_template(Request $request)
    {
        $whatsapp = WChadhavaTemplate::where('status', 1)->get();
        return view('admin-views.whatsapp.chadhava-template', compact('whatsapp'));
    }
    public function counsltancy_template(Request $request)
    {
        $whatsapp = WConsultancyTemplate::where('status', 1)->get();
        return view('admin-views.whatsapp.counsltancy-template', compact('whatsapp'));
    }
    public function ecom_template(Request $request)
    {
        $whatsapp = WEcomTemplate::where('status', 1)->get();
        return view('admin-views.whatsapp.ecom-template', compact('whatsapp'));
    }
    public function event_template(Request $request)
    {
        $whatsapp = WEventTemplate::where('status', 1)->get();
        return view('admin-views.whatsapp.event-template', compact('whatsapp'));
    }
    public function donation_template(Request $request)
    {
        $whatsapp = WDonationTemplate::where('status', 1)->get();
        return view('admin-views.whatsapp.donation-template', compact('whatsapp'));
    }
    public function kundali_template()
    {
        $whatsapp = WKundaliTemplate::where('status', 1)->get();
        return view('admin-views.whatsapp.kundali-template', compact('whatsapp'));
    }
    public function kundali_template_update(Request $request)
    {
        WKundaliTemplate::where('id', $request->id)->update(['body' => $request->body]);
        Toastr::success(translate('Kundali_Template_Successfully_update'));
        return back();
    }
    public function tours_template(Request $request)
    {
        $whatsapp = WToursTemplate::where('status', 1)->get();
        return view('admin-views.whatsapp.tours-template', compact('whatsapp'));
    }
    // Update For the All Tempaltees
    public function offline_pooja_template_update(Request $request)
    {
        WOfflinePoojaTemplate::where('id', $request->id)->update(['body' => $request->body]);
        Toastr::success(translate('Offline_Pooja_Template_Successfully_update'));
        return back();
    }
    public function pooja_template_update(Request $request)
    {
        WhatsappTemplate::where('id', $request->id)->update(['body' => $request->body]);
        Toastr::success(translate('Pooja_Template_Successfully_update'));
        return back();
    }
    public function vip_anushthan_template_update(Request $request)
    {
        WVIPAnushthanTemplate::where('id', $request->id)->update(['body' => $request->body]);
        Toastr::success(translate('vip_anushthan_Template_Successfully_update'));
        return back();
    }
    public function chadhava_template_update(Request $request)
    {
        WChadhavaTemplate::where('id', $request->id)->update(['body' => $request->body]);
        Toastr::success(translate('Pooja_Template_Successfully_update'));
        return back();
    }
    public function tours_template_update(Request $request)
    {
        WToursTemplate::where('id', $request->id)->update(['body' => $request->body]);
        Toastr::success(translate('Pooja_Template_Successfully_update'));
        return back();
    }
    public function counsltancy_template_update(Request $request)
    {
        WConsultancyTemplate::where('id', $request->id)->update(['body' => $request->body]);
        Toastr::success(translate('Pooja_Template_Successfully_update'));
        return back();
    }
    public function ecom_template_update(Request $request)
    {
        WEcomTemplate::where('id', $request->id)->update(['body' => $request->body]);
        Toastr::success(translate('Pooja_Template_Successfully_update'));
        return back();
    }
    public function event_template_update(Request $request)
    {
        WEventTemplate::where('id', $request->id)->update(['body' => $request->body]);
        Toastr::success(translate('Pooja_Template_Successfully_update'));
        return back();
    }
    public function donation_template_update(Request $request)
    {
        WDonationTemplate::where('id', $request->id)->update(['body' => $request->body]);
        Toastr::success(translate('Pooja_Template_Successfully_update'));
        return back();
    }
    public function temple_darshan_template_update(Request $request)
    {
        WTempleDarshanTemplate::where('id', $request->id)->update(['body' => $request->body]);
        Toastr::success(translate('temple_darshan_Template_Successfully_update'));
        return back();
    }
    // Whatsapp Connect the code 29-11-2024 By Er.Rahul Bathri
    public function whatsapp_panel(Request $request)
    {
        return view('admin-views.whatsapp.whatsapp-panel');
    }
    public function templeDarshanTemplate()
    {
        $whatsapp = WTempleDarshanTemplate::where('status', 1)->get();
        return view('admin-views.whatsapp.temple-darshan-template', compact('whatsapp'));
    }
    public function send_whatsapp_message(Request $request)
    {
        $userPhone = \App\Models\User::select('phone', 'name')->get();
        return view('admin-views.whatsapp.message.send-whatsapp-message', compact('userPhone'));
    }
    public function create_session(Request $request)
    {
        $res = Admin::where('id', 1)->first();


        $response = Http::post(env('WA_SERVER_URL') . '/sessions/add', [
            'id' => 'device_mahakal_2024',
            'isLegacy' => false
        ]);

        if ($response->status() == 200) {
            $body = json_decode($response->body());
            $data['qr'] = $body->data->qr;
            $data['message'] = $body->message;
            Admin::where('id', 1)->update(['qr' => $body->data->qr]);
            return response()->json($data);
        } elseif ($response->status() == 409) {
            $data['qr'] = $res['qr'];
            $data['message'] = __('QR code received, please scan the QR code');
            return response()->json($data);
        }
        return back()->with('error', 'Something went wrong');
    }

    public function check_session()
    {
        $res = Admin::where('id', 1)->first();
        $id = 'device_mahakal_2024';
        $response = Http::get(env('WA_SERVER_URL') . '/sessions/status/' . $id);

        $device['wa_status'] = $response->status() == 200 ? 1 : 0;
        if ($response->status() == 200) {
            $res = json_decode($response->body());
            if (isset($res->data->userinfo)) {
                $phone = str_replace('@s.whatsapp.net', '', $res->data->userinfo->id);
                $phone = explode(':', $phone);
                $phone = $phone[0] ?? null;
                $device['wa_phone'] = $phone;
                $device['qr'] = null;
            }
        }
        else{
            $emails = [
            'rahulbathrimspl@gmail.com',
            'kamran@manalsoftech.com',
        ];

        foreach ($emails as $email) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $data = [];
                $data['type'] = 'whatsapp_logout';
                $data['email'] = $email;
                $data['subject'] = 'Alert: WhatsApp Device Logged Out';
                $data['htmlContent'] = '
                        <!DOCTYPE html>
                        <html>
                        <head>
                            <meta charset="UTF-8">
                            <title>WhatsApp Logout Alert</title>
                            <style>
                                body {
                                    font-family: Arial, sans-serif;
                                    background-color: #f9f9f9;
                                    color: #333333;
                                    margin: 0;
                                    padding: 0;
                                }
                                .email-container {
                                    max-width: 600px;
                                    margin: 20px auto;
                                    background-color: #ffffff;
                                    border-radius: 10px;
                                    overflow: hidden;
                                    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                                }
                                .header {
                                    background-color: #FF6F00;
                                    color: #ffffff;
                                    padding: 20px;
                                    text-align: center;
                                    font-size: 20px;
                                    font-weight: bold;
                                }
                                .content {
                                    padding: 30px;
                                    font-size: 16px;
                                    line-height: 1.5;
                                }
                                .content p {
                                    margin-bottom: 15px;
                                }
                                .footer {
                                    background-color: #f1f1f1;
                                    color: #555555;
                                    text-align: center;
                                    padding: 15px;
                                    font-size: 14px;
                                }
                                .btn {
                                    display: inline-block;
                                    padding: 12px 25px;
                                    margin-top: 15px;
                                    background-color: #FF6F00;
                                    color: #ffffff;
                                    text-decoration: none;
                                    border-radius: 5px;
                                    font-weight: bold;
                                }
                            </style>
                        </head>
                        <body>
                            <div class="email-container">
                                <div class="header">
                                    WhatsApp Logout Alert
                                </div>
                                <div class="content">
                                    <p>Dear User,</p>
                                    <p>Your WhatsApp device has been <strong>logged out</strong>.</p>
                                    <p>Please check your account and reconnect immediately to continue using your services.</p>
                                    <a href="#" class="btn">Reconnect Now</a>
                                </div>
                                <div class="footer">
                                    Powered by Mahakal.com
                                </div>
                            </div>
                        </body>
                        </html>
                        ';


                Helpers::emailSendMessage($data);
            }
        }
        }
        Admin::where('id', 1)->update($device);
        //    apis('company/setup/device/update','POST', $device);   

        $message = $response->status() == 200 ? __('Device Connected Successfully') : null;

        return response()->json(['message' => $message, 'connected' => $response->status() == 200 ? true : false]);
    }


    public function logout_session()
    {
        $arr['wa_status'] = 0;
        $arr['qr'] = null;
        $id = 'device_mahakal_2024';
        $response = Http::delete(env('WA_SERVER_URL') . '/sessions/delete/' . $id);
        Admin::where('id', 1)->update($arr);
        return response()->json(['message' => __('Congratulations! Your Device Successfully Logout')]);
    }

    public function send_test(Request $request)
    {
        $body = [];
        $text = $this->formatText($request->message);
        $body["text"] = $text;
        $body["message"] = $request->message;
        $type = "plain-text";
        // if (!isset($body)) {
        //     return response()->json(["error" => "Request Failed"], 401);
        // }
        try {
            $response = $this->messageSend(
                $body,
                'device_mahakal_2024',
                '+91' . $request->reciver,
                $type,
                true
            );
            if ($response["status"] == 200) {
                // $logs["user_id"] = Auth::id();
                // $logs["device_id"] = $device->id;
                // $logs["from"] = $device->phone ?? null;
                // $logs["to"] = $request->reciver;
                // $logs["template_id"] = $template->id ?? null;
                // $logs["type"] = "single-send";
                // $this->saveLog($logs);
                return response()->json(
                    [
                        "message" => __("Message sent successfully..!!"),
                    ],
                    200
                );
            } else {
                return response()->json(["error" => "Request Failed"], 401);
            }
        } catch (Exception $e) {
            return response()->json(["error" => "Request Failed"], 401);
        }
    }

    public function all_send_test(Request $request)
    {
        $body = [];
        $text = $this->formatText($request->message);
        $body["text"] = $text;
        $body["message"] = $request->message;
        $type = "plain-text";
        $deviceName = 'device_mahakal_2024';
        try {
            if ($request->reciver === 'all') {
                $allPhones = \App\Models\User::pluck('phone')->toArray();

                foreach ($allPhones as $phone) {
                    $cleanPhone = preg_replace('/[^0-9]/', '', $phone);

                    if (substr($cleanPhone, 0, 2) === '91') {
                        $cleanPhone = substr($cleanPhone, 2);
                    }

                    if (strlen($cleanPhone) === 10) {
                        $formattedPhone = '+91' . $cleanPhone;

                        dispatch(new SendAllWhatsapp(
                            $body,
                            $deviceName,
                            $formattedPhone,
                            $type,
                            true
                        ));
                    }
                }

                return response()->json([
                    "message" => __("Messages are being sent in the background."),
                ], 200);
            } else {

                $formattedPhone = '+91' . $request->reciver;
                $response = $this->messageSend(
                    $body,
                    $deviceName,
                    $formattedPhone,
                    $type,
                    true
                );

                if (isset($response["status"]) && $response["status"] == 200) {
                    return response()->json([
                        "message" => __("Message sent successfully..!!"),
                    ], 200);
                } else {
                    return response()->json(["error" => "Request Failed"], 401);
                }
            }
        } catch (Exception $e) {
            return response()->json(["error" => "Request Failed"], 401);
        }
    }
}
