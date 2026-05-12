<?php

namespace App\Http\Controllers\RestAPI\v1;

use App\Http\Controllers\Controller;
use App\Models\BirthJournal;
use App\Models\BirthJournalKundali;
use App\Models\Country;
use App\Models\States;
use App\Utils\Helpers;
use CodeIgniter\HTTP\Message;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

use App\Traits\Payment;
use App\Library\Receiver;
use App\Models\BusinessSetting;
use App\Models\Currency;
use App\Library\Payer;
use App\Library\Payment as PaymentInfo;
use App\Models\KundaliLeads;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class BirthJournalController extends Controller
{
    public function GetBirthJournal(Request $request)
    {

        $getData = BirthJournal::where('status', 1)->get();
        if ($getData->isNotEmpty()) {
            $BirthJournal_translation = [];
            foreach ($getData as $key => $value) {
                $translations = $value->translations()->pluck('value', 'key')->toArray();
                $BirthJournal_translation[$key]['id'] =  $value['id'];
                $BirthJournal_translation[$key]['en_description'] =  $value['description'];
                $BirthJournal_translation[$key]['en_short_description'] =  $value['short_description'];
                $BirthJournal_translation[$key]['hi_description'] =  $translations['description'] ?? "";
                $BirthJournal_translation[$key]['hi_short_description'] =  $translations['short_description'] ?? "";
                $BirthJournal_translation[$key]['selling_price'] =  $value['selling_price'];
                $BirthJournal_translation[$key]['name'] =  $value['name'];
                $BirthJournal_translation[$key]['type'] =  $value['type'];
                $BirthJournal_translation[$key]['pages'] =  $value['pages'];

                $BirthJournal_translation[$key]['image'] = getValidImage(
                    path: 'storage/app/public/birthjournal/image/' . $value['image'],
                    type: 'backend-product'
                );
            }

            return response()->json(['status' => 1, 'message' => 'Birth Journal successfully', 'recode' => count($BirthJournal_translation), 'data' => $BirthJournal_translation], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Birth Journal Not Fount', 'recode' => 0, 'data' => []], 400);
        }
    }

    public function GetBirthJournalById(Request $request)
    {
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        $getData = BirthJournal::where('status', 1)->with('translations')->find($request['birth_journal_id']);
        if (!empty($getData) && !empty($request['birth_journal_id'])) {
            $BirthJournal_translation = [];

            $translationKeys = ['description', 'short_description'];
            $translations = $getData->translations()->pluck('value', 'key')->toArray();
            $BirthJournal_translation['en_description'] =  $getData['description'];
            $BirthJournal_translation['en_short_description'] =  $getData['short_description'];
            $BirthJournal_translation['hi_description'] =  $translations['description'] ?? "";
            $BirthJournal_translation['hi_short_description'] =  $translations['short_description'] ?? "";
            $BirthJournal_translation['id'] =  $getData['id'];
            $BirthJournal_translation['selling_price'] =  $getData['selling_price'];
            $BirthJournal_translation['pages'] =  $getData['pages'];
            $BirthJournal_translation['name'] =  $getData['name'];
            $BirthJournal_translation['type'] =  $getData['type'];

            $BirthJournal_translation['image'] = getValidImage(path: 'storage/app/public/birthjournal/image/' . $getData['image'], type: 'backend-product');

            return response()->json(['status' => 1, 'message' => 'Birth Journal successfully', 'recode' => 1, 'data' => $BirthJournal_translation], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Birth Journal Not Fount', 'recode' => 0, 'data' => []], 400);
        }
    }

    public function GetCountry(Request $request)
    {
        $getData = Country::all();
        if (!empty($getData) && count($getData) > 0) {
            return response()->json(['status' => 1, 'message' => 'Country List successfully', 'recode' => count($getData), 'data' => $getData], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Country Not Fount', 'recode' => 0, 'data' => []], 400);
        }
    }


    public function GetState(Request $request)
    {
        $googleMapsApiKey = config('services.google_maps.api_key');
        if ($request['name']) {
            $response = Http::get('https://maps.googleapis.com/maps/api/place/autocomplete/json', [
                'input' => $request['name'],
                'key' => $googleMapsApiKey,
                'types' => '(regions)',
                'components' => 'country:' . $request['country_shortname'],
            ]);
            $data = $response->json();
            if ($data['status'] == 'OK') {
                $results = array_filter($data['predictions'], function ($prediction) use ($request) {
                    return stripos($prediction['description'], $request['name']) === 0;
                });
                $formattedResults = array_map(function ($prediction) use ($googleMapsApiKey) {
                    $placeDetails = Http::get('https://maps.googleapis.com/maps/api/place/details/json', [
                        'place_id' => $prediction['place_id'],
                        'key' => $googleMapsApiKey,
                    ])->json();
                    if ($placeDetails['status'] == 'OK') {
                        return [
                            'formatted_address' => $placeDetails['result']['formatted_address'],
                            'latitude' => $placeDetails['result']['geometry']['location']['lat'],
                            'longitude' => $placeDetails['result']['geometry']['location']['lng']
                        ];
                    }
                    return null;
                }, $results);
                $formattedResults = array_filter($formattedResults);
                return response()->json(['status' => 1, 'message' => 'Search results fetched successfully', 'data' => $formattedResults], 200);
            }
            return response()->json(['status' => 0, 'message' => 'No results found', 'data' => []], 404);
        }
        return response()->json(['status' => 0, 'message' => 'name key not found', 'data' => []], 400);
    }

    public function CreateBirthPdf(Request $request)
    {
        $rules = [
            "user_id" =>  [
                'required',
                function ($attribute, $value, $fail) {
                    if (!User::where('id', $value)->where('is_active', 1)->exists()) {
                        $fail('The selected user is invalid or inactive.');
                    }
                },
            ],
            "birth_journal_id" => [
                "required",
                "integer",
                function ($attribute, $value, $fail) use ($request) {
                    $getDatas = BirthJournal::where('id', $value)->where('status', 1)->first();
                    if (!$getDatas) {
                        $fail('Selected Kundli ID is invalid or inactive.');
                        return;
                    }
                },
            ],
            "name" => "required|string|max:255",
            "email" => "nullable|email|max:255",
            "gender" => "required|in:male,female,other",
            "phone_no" => "nullable|string|max:20",
            "bod" => "required|date",
            "time" => "required|date_format:H:i:s",
            "country_id" => "required|integer",
            "state" => "required|string|max:255",
            "lat" => "required|numeric",
            "log" => "required|numeric",
            "language" => "required|string|max:255",
            "tzone" => "required|string|max:255",
            "chart_style" => "required|string|max:255",
            "amount" => "required|numeric",
            "leads_id" => "required",
            "wallet_type" => "required|in:0,1",
            "transaction_id" => "required",
        ];
        if (BirthJournal::where('id', $request->get('birth_journal_id'))->where('name', 'kundali_milan')->where('status', 1)->exists()) {
            $rules = array_merge($rules, [
                'female_name' => 'required|string|max:255',
                'female_email' => 'nullable|email|max:255',
                'female_gender' => 'required|in:male,female,other',
                'female_phone_no' => 'nullable|string|max:20',
                'female_dob' => 'required|date',
                'female_time' => 'required|date_format:H:i:s',
                'female_country_id' => 'required|integer',
                'female_place' => 'required|string|max:255',
                'female_lat' => 'required|numeric',
                'female_long' => 'required|numeric',
                'female_tzone' => 'required|string|max:255',
            ]);
        }
        $messages = [
            'birth_journal_id.required' => 'The birth journal ID field is required.',
            'name.required' => 'The name field is required.',
            'email.required' => 'The email field is required.',
            'gender.required' => 'The gender field is required.',
            'phone_no.required' => 'The phone number field is required.',
            'bod.required' => 'The date of birth field is required.',
            'time.required' => 'The time field is required.',
            'country_id.required' => 'The country ID field is required.',
            'state.required' => 'The Place field is required.',
            'lat.required' => 'The latitude field is required.',
            'log.required' => 'The longitude field is required.',
            'language.required' => 'The language field is required.',
            'tzone.required' => 'The timezone field is required.',
            'chart_style.required' => 'The chart style field is required.',
            'amount.required' => 'The amount field is required.',
            'amount.numeric' => 'The amount must be a number.',
            'leads_id' => "lead Id field is required.",
        ];

        if (BirthJournal::where('id', $request->get('birth_journal_id'))->where('name', 'kundali_milan')->where('status', 1)->exists()) {
            $messages = array_merge($messages, [
                'female_name.required' => 'The female name field is required.',
                'female_email.required' => 'The female email field is required.',
                'female_gender.required' => 'The female gender field is required.',
                'female_phone_no.required' => 'The female phone number field is required.',
                'female_dob.required' => 'The female date of birth field is required.',
                'female_time.required' => 'The female time field is required.',
                'female_country_id.required' => 'The female country ID field is required.',
                'female_place.required' => 'The female place field is required.',
                'female_lat.required' => 'The female latitude field is required.',
                'female_long.required' => 'The female longitude field is required.',
                'female_tzone.required' => 'The female timezone field is required.',
            ]);
        }

        $validatedData = $request->validate($rules, $messages);
        $kundali =  new BirthJournalKundali();
        $kundali->birth_journal_id = $validatedData['birth_journal_id'];
        $kundali->user_id = $validatedData['user_id'];
        $kundali->name = $validatedData['name'];
        $kundali->email = $validatedData['email'];
        $kundali->gender = $validatedData['gender'];
        $kundali->phone_no = $validatedData['phone_no'];
        $kundali->bod = $validatedData['bod'];
        $kundali->time = $validatedData['time'];
        $kundali->country_id = $validatedData['country_id'];
        $kundali->state = $validatedData['state'];
        $kundali->lat = $validatedData['lat'];
        $kundali->log = $validatedData['log'];
        $kundali->language = $validatedData['language'];
        $kundali->tzone = $validatedData['tzone'];
        $kundali->chart_style = $validatedData['chart_style'];
        $kundali->amount = $validatedData['amount'];
        $kundali->payment_status = 1;
        $kundali->transaction_id = (($request->wallet_type == 0) ? $request->transaction_id : "wallet");

        if (BirthJournal::where('id', $request->get('birth_journal_id'))->where('name', 'kundali_milan')->where('status', 1)->exists()) {
            $kundali->female_name = $validatedData['female_name'];
            $kundali->female_email = $validatedData['female_email'];
            $kundali->female_gender = $validatedData['female_gender'];
            $kundali->female_phone_no = $validatedData['female_phone_no'];
            $kundali->female_dob = $validatedData['female_dob'];
            $kundali->female_time = $validatedData['female_time'];
            $kundali->female_country_id = $validatedData['female_country_id'];
            $kundali->female_place = $validatedData['female_place'];
            $kundali->female_lat = $validatedData['female_lat'];
            $kundali->female_long = $validatedData['female_long'];
            $kundali->female_tzone = $validatedData['female_tzone'];
            $kundali->milan_verify = 0;
        } else {
            $companyPhone = getWebConfig(name: 'company_phone');
            $companyEmail = getWebConfig(name: 'company_email');
            $companyName = getWebConfig(name: 'company_name');
            $kundaliPdf = "";
            $apiData = array(
                'name' => $validatedData['name'],
                'gender' => $validatedData['gender'],
                'day' => date('d', strtotime($validatedData['bod'])),
                'month' => date('m', strtotime($validatedData['bod'])),
                'year' => date('Y', strtotime($validatedData['bod'])),
                'hour' => date('H', strtotime($validatedData['time'])),
                'min' => date('i', strtotime($validatedData['time'])),
                'lat' => $validatedData['lat'],
                'lon' => $validatedData['log'],
                'language' => $validatedData['language'],
                'tzone' => $validatedData['tzone'],
                'place' => $validatedData['state'],
                'chart_style' => $validatedData['chart_style'],
                'footer_link' => route('home'),
                'logo_url' => dynamicStorage(path: "storage/app/public/company/" . getWebConfig(name: 'company_web_logo')),
                'company_name' => $companyName,
                'company_info' => 'Description of Mahakal Astrotech (OPC) PVT LTD@2025.',
                'domain_url' => route('home'),
                'company_email' => $companyEmail,
                'company_landline' => $companyPhone,
                'company_mobile' => $companyPhone
            );
            $kundali_Pdf = '';
            $findData =  BirthJournal::where('id', $request->get('birth_journal_id'))->first();
            if (($findData['type'] ?? "basic") == "basic") {
                $kundali_Pdf = json_decode(\App\Utils\ApiHelper::astroApi('https://pdf.astrologyapi.com/v1/basic_horoscope_pdf', $apiData['language'], $apiData), true);
            } else if (($findData['type'] ?? "basic") == "pro") {
                $language = in_array($validatedData['language'], ['hi', 'en']) ? $validatedData['language'] : 'hi';
                $kundali_Pdf = json_decode(\App\Utils\ApiHelper::astroApi('https://pdf.astrologyapi.com/v1/pro_horoscope_pdf', $language, $apiData), true);
            }
            if (!empty($kundali_Pdf['pdf_url'])) {
                $fileName = $kundaliPdf = $findData['pages'] . '_page_' . $apiData['language'] . '_kundali_' . time() . '.pdf';
                $filePath = storage_path('app/public/birthjournal/kundali/' . $fileName);
                if (!file_exists(dirname($filePath))) {
                    mkdir(dirname($filePath), 0755, true);
                }
                $pdfContent = file_get_contents($kundali_Pdf['pdf_url']);
                file_put_contents($filePath, $pdfContent);
            }
            $kundali->milan_verify = 1;
            $kundali->kundali_pdf = $kundaliPdf;
        }
        $kundali->save();
        $findDatas =  BirthJournalKundali::with('birthJournal')->find($kundali->id);
        if (BirthJournal::where('id', $request->get('birth_journal_id'))->where('name', 'kundali_milan')->where('status', 1)->exists()) {
            $message_data['kundli_page'] = $findDatas['birthJournal']['pages'] ?? '';
            $message_data['kundli_type'] = $findDatas['birthJournal']['type'] ?? '';
            $message_data['orderId'] = $findDatas['order_id'] ?? '';
            $message_data['booking_date'] = date('d M,Y h:i A', strtotime($findDatas['created_at'] ?? ''));
            $message_data['final_amount'] = webCurrencyConverter(amount: (float)$request['amount'] ?? 0);
            $message_data['customer_id'] =  $request->user_id;
            Helpers::whatsappMessage('kundali', 'kundali_milan_confirm', $message_data);
        } else {
            $message_data['kundli_page'] = $findDatas['birthJournal']['pages'] ?? '';
            $message_data['kundli_type'] = $findDatas['birthJournal']['type'] ?? '';
            $message_data['final_amount'] = webCurrencyConverter(amount: (float)$request['amount'] ?? 0);
            $message_data['customer_id'] =  $request->user_id;
            $message_data['orderId'] = $findDatas['order_id'] ?? '';
            $message_data['booking_date'] = date('d M,Y h:i A', strtotime($findDatas['created_at'] ?? ''));
            if ($fileName) {
                $message_data['type'] = 'text-with-media';
                $message_data['attachment'] = asset('storage/app/public/birthjournal/kundali/' . $fileName);
            }
            Helpers::whatsappMessage('kundali', 'kundali_pdf', $message_data);
        }
        KundaliLeads::where('id', $validatedData['leads_id'])->update(['payment_status' => 1, 'status' => 1]);
        return response()->json([
            'status' => 1,
            'message' => 'Successfully',
            'data' => []
        ], 200);
    }

    public function GetBirthPdf(Request $request)
    {
        $rules = [
            "user_id" =>  [
                'required',
                function ($attribute, $value, $fail) {
                    if (!User::where('id', $value)->where('is_active', 1)->exists()) {
                        $fail('The selected user is invalid or inactive.');
                    }
                },
            ],
            "type" => [
                'nullable',
                'in:kundali,kundali_milan',
                function ($attribute, $value, $fail) use ($request) {
                    if (!$request->has('order_id') && !$value) {
                        $fail('The type field is required when order_id is not present.');
                    }
                },
            ],
            "order_id" => [
                'nullable',
                function ($attribute, $value, $fail) use ($request) {
                    if (!$request->has('type') && !$value) {
                        $fail('The order_id field is required when type is not present.');
                    }
                },
            ],
        ];
        $messages = ['user_id.required' => 'The User ID field is required.', "type.required" => "Type kundali or kundali_milan"];
        $request->validate($rules, $messages);
        if ($request->order_id) {
            $queryKun = BirthJournalKundali::where(['user_id' => $request['user_id'], 'payment_status' => 1, 'id' => $request->order_id])->with(['birthJournal', 'country', 'country_female']);
        } else {
            $queryKun = BirthJournalKundali::where(['user_id' => $request['user_id'], 'payment_status' => 1])->with(['birthJournal', 'country', 'country_female']);
            if ($request->type == 'kundali_milan') {
                $queryKun->whereHas('birthJournal_kundalimilan', function ($query) {
                    $query->where('name', 'kundali_milan');
                });
            } else {
                $queryKun->whereHas('birthJournal_kundali', function ($query) {
                    $query->where('name', 'kundali');
                });
            }
        }

        $getDatas = $queryKun->orderBy('id','desc')->get();
        if (!empty($getDatas) && count($getDatas) > 0) {
            $getData = [];
            foreach ($getDatas as $k => $pdf) {
                $getData[$k]['id'] = $pdf['id'] ?? '';
                $getData[$k]['user_id'] = $pdf['user_id'] ?? '';
                $getData[$k]['order_id'] = $pdf['order_id'] ?? '';
                $getData[$k]['male_name'] = $pdf['name'] ?? '';
                $getData[$k]['male_gender'] = $pdf['gender'] ?? '';
                $getData[$k]['male_bod'] = $pdf['bod'] ?? '';
                $getData[$k]['male_time'] = $pdf['time'] ?? '';
                $getData[$k]['male_country'] = $pdf['country']['name'] ?? '';
                $getData[$k]['male_state'] = $pdf['state'] ?? '';
                if ($request->type == 'kundali_milan') {
                    $getData[$k]['female_name'] = $pdf['female_name'] ?? '';
                    $getData[$k]['female_gender'] = $pdf['female_gender'] ?? '';
                    $getData[$k]['female_bod'] = $pdf['female_dob'] ?? '';
                    $getData[$k]['female_time'] = $pdf['female_time'] ?? '';
                    $getData[$k]['female_country'] = $pdf['country_female']['name'] ?? '';
                    $getData[$k]['female_state'] = $pdf['female_place'] ?? '';
                }
                $getData[$k]['language'] = $pdf['language'] ?? '';
                $getData[$k]['chart_style'] = $pdf['chart_style'] ?? '';
                $getData[$k]['payment_status'] = $pdf['payment_status'] ?? '';
                $getData[$k]['amount'] = $pdf['amount'] ?? '';
                $getData[$k]['transaction_id'] = $pdf['transaction_id'] ?? '';
                $getData[$k]['milan_verify'] = $pdf['milan_verify'] ?? '';
                $getData[$k]['created_at'] = $pdf['created_at'] ?? '';
                $getData[$k]['invoice_url'] = url('api/v1/birth_journal/invoice/' . $pdf['id'] ?? '');


                $getData[$k]['image'] = getValidImage(path: 'storage/app/public/birthjournal/image/' . ($pdf['birthJournal']['image'] ?? ''), type: 'logo');
                if ($request->type == 'kundali_milan' && ($pdf['milan_verify'] ?? '') == 1) {
                    $getData[$k]['kundali_pdf'] = (($pdf['kundali_pdf']) ? dynamicStorage(path: 'storage/app/public/birthjournal/kundali_milan/' . $pdf['kundali_pdf']) : '');
                } else {
                    $getData[$k]['kundali_pdf'] = (($pdf['kundali_pdf']) ? dynamicStorage(path: 'storage/app/public/birthjournal/kundali/' . $pdf['kundali_pdf']) : '');
                }
            }
            if ($request->order_id) {
                return response()->json(['status' => 1, 'message' => 'get Kundali', 'recode' => 1, 'data' => $getData[0] ?? [],], 200);
            } else {
                return response()->json(['status' => 1, 'message' => 'get All Kundali', 'recode' => count($getData), 'data' => $getData,], 200);
            }
        } else {
            return response()->json(['status' => 0, 'message' => 'not Found kundali', 'recode' => 0, 'data' => []], 400);
        }
    }
    public function CreateLeads(Request $request)
    {
        $rules = [
            "user_id" =>  [
                'required',
                function ($attribute, $value, $fail) {
                    if (!User::where('id', $value)->where('is_active', 1)->exists()) {
                        $fail('The selected user is invalid or inactive.');
                    }
                },
            ],
            "birth_journal_id" => [
                "required",
                "integer",
                function ($attribute, $value, $fail) use ($request) {
                    $getDatas = BirthJournal::where('id', $value)->where('status', 1)->first();
                    if (!$getDatas) {
                        $fail('Selected Kundli ID is invalid or inactive.');
                        return;
                    }
                },
            ],
            "amount" => "required|numeric",
        ];

        $messages = [
            "user_id.required" => "User Id field is required.",
            'birth_journal_id.required' => 'The birth journal ID field is required.',
            'amount.required' => 'The amount field is required.',
        ];

        $validatedData = $request->validate($rules, $messages);
        $userData = User::where('id', $request->user_id)->first();
        $leads =  new KundaliLeads();
        $leads->user_id = $validatedData['user_id'];
        $leads->kundali_id = $validatedData['birth_journal_id'];
        $leads->amount = $validatedData['amount'];
        $leads->phone_no = $userData['phone'];
        $leads->user_name = $userData['name'] ?? "";
        $leads->booking_date = date('Y-m-d H:i:s');
        $leads->payment_status = 0;
        $leads->status = 0;
        $leads->save();

        $leadsId = $leads->id;
        return response()->json([
            'status' => 1,
            'message' => 'create lead successfully',
            'data' => ['lead_id' => $leadsId]
        ], 200);
    }

    public function GetInvoice(Request $request, $id)
    {
        $companyPhone = getWebConfig(name: 'company_phone');
        $companyEmail = getWebConfig(name: 'company_email');
        $companyName = getWebConfig(name: 'company_name');
        $companyWebLogo = getWebConfig(name: 'company_web_logo');
        $details = BirthJournalKundali::where(['id' => $id])->with(['country', 'birthJournal', 'userData', 'astrologer'])->first();
        if ($details) {
            $mpdf_view = \Illuminate\Support\Facades\View::make('admin-views.birth_journal.order_invoice', compact('details', 'companyPhone', 'companyEmail', 'companyName', 'companyWebLogo'));
            \App\Utils\Helpers::gen_mpdf($mpdf_view, 'birth_order_', $details['order_id']);
            return response()->json(["status" => 1, "message" => "Invoice generated successfully."], 200);
        } else {
            return response()->json(["status" => 0, "message" => "Invoice generated Failed."], 400);
        }
    }
}
