<?php

namespace App\Http\Controllers\RestAPI\v4;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\GuestUser;
use App\Models\HelpTopic;
use App\Models\SocialMedia;
use App\Models\Subscription;
use App\Utils\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GeneralController extends Controller
{
    public function faq()
    {
        return response()->json(HelpTopic::status()->orderBy('ranking')->get(), 200);
    }

    public function get_guest_id(Request $request)
    {
        $guest_id = GuestUser::insertGetId([
            'ip_address' => $request->ip(),
            'created_at' => now(),
        ]);
        return response()->json(['guest_id' => $guest_id], 200);
    }

    public function subscription(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subscription_email' => 'required'
        ], [
            'subscription_email.required' => 'The email is required!'

        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $subscription_email = Subscription::where('email', $request->subscription_email)->first();
        if ($subscription_email) {
            return response()->json(['status' => 'subscribed', 200]);

        } else {
            $new_subcription = new Subscription;
            $new_subcription->email = $request->subscription_email;
            $new_subcription->save();

            return response()->json(['status' => 'success', 200]);
        }

    }

    public function social_media()
    {
        $socials = SocialMedia::where(['active_status' => 1])->get();

        return response()->json(['socials' => $socials, 200]);
    }

    public function contact_store(Request $request)
    {
        $validatorRules = [
            'mobile_number' => 'required',
            'subject' => 'required',
            'message' => 'required',
            'email' => 'required',
            'name' => 'required',
        ];

        $recaptcha = getWebConfig(name: 'recaptcha');
        if (isset($recaptcha) && $recaptcha['status'] == 1) {
            $validatorRules['g-recaptcha-response'] = [
                function ($attribute, $value, $fail) {
                    if (!$this->isGoogleRecaptchaValid($value)) {
                        $fail('ReCAPTCHA Failed');
                    }
                },
            ];
        }
        $validator = Validator::make($request->all(), $validatorRules, [
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
}
