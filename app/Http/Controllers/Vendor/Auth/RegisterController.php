<?php

namespace App\Http\Controllers\Vendor\Auth;

use App\Contracts\Repositories\ShopRepositoryInterface;
use App\Contracts\Repositories\VendorRepositoryInterface;
use App\Contracts\Repositories\VendorWalletRepositoryInterface;
use App\Enums\SessionKey;
use App\Enums\ViewPaths\Vendor\Auth;
use App\Events\VendorRegistrationMailEvent;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Vendor\VendorAddRequest;
use App\Services\ShopService;
use App\Services\VendorService;
use App\Utils\Helpers;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Session;

class RegisterController extends BaseController
{
    public function __construct(
        private readonly VendorRepositoryInterface $vendorRepo,
        private readonly VendorWalletRepositoryInterface $vendorWalletRepo,
        private readonly ShopRepositoryInterface $shopRepo,
        private readonly VendorService $vendorService,
        private readonly ShopService $shopService,
    ) {}

    public function index(?Request $request, string $type = null): View|Collection|LengthAwarePaginator|null|callable|RedirectResponse
    {
        return $this->getView();
    }
    public function getView(): View|RedirectResponse
    {
        $businessMode = getWebConfig(name: 'business_mode');
        $vendorRegistration = getWebConfig(name: 'seller_registration');
        if ((isset($businessMode) && $businessMode == 'single') || (isset($vendorRegistration) && $vendorRegistration == 0)) {
            Toastr::warning(translate('access_denied') . '!!');
            return redirect('/');
        }
        $googleMapsApiKey =  config('services.google_maps.api_key');
        return view(VIEW_FILE_NAMES[Auth::VENDOR_REGISTRATION[VIEW]], compact('googleMapsApiKey'));
    }
    public function add(VendorAddRequest $request): JsonResponse
    {
        $recaptcha = getWebConfig('recaptcha');
        if (isset($recaptcha) && $recaptcha['status'] == 1) {
            try {
                $request->validate([
                    'g-recaptcha-response' => [
                        function ($attribute, $value, $fail) {
                            $secret_key = getWebConfig('recaptcha')['secret_key'];
                            $response = $value;
                            $url = 'https://www.google.com/recaptcha/api/siteverify?secret=' . $secret_key . '&response=' . $response;
                            $response = \file_get_contents($url);
                            $response = json_decode($response);
                            if (!$response->success) {
                                $fail(translate('ReCAPTCHA_Failed'));
                            }
                        },
                    ],
                ]);
            } catch (\Exception $exception) {
                return response()->json(['error' => translate('Captcha_Failed')]);
            }
        } else {
            if (strtolower($request['default_recaptcha_id_seller_regi']) != strtolower(Session(SessionKey::VENDOR_RECAPTCHA_KEY))) {
                Session::forget(SessionKey::VENDOR_RECAPTCHA_KEY);
                return response()->json(['error' => translate('Captcha_Failed')]);
            }
        }
        \Illuminate\Support\Facades\DB::beginTransaction();
        $vendor = $this->vendorRepo->add(data: $this->vendorService->getAddData($request));
        if ($request->from_submit == 'seller') {
            $this->shopRepo->add($this->shopService->getAddShopDataForRegistration(request: $request, vendorId: $vendor['id']));
            // $datas =  \App\Utils\Helpers::ShipWayWarehouseCreate($request);
            // if ($datas['success'] == false) {
            //     \Illuminate\Support\Facades\DB::rollBack();
            // } else {
            //     $this->vendorRepo->update(id: $vendor['id'], data: ['warehouse_id' => $datas['warehouse_response']['warehouse_id'] ?? ""]);
            // }
            $this->vendorWalletRepo->add($this->vendorService->getInitialWalletData(vendorId: $vendor['id']));
            \Illuminate\Support\Facades\DB::commit();
        } elseif ($request->from_submit == 'tour') {
            $tours = new \App\Models\TourAndTravel();
            $tours->person_name = $request->f_name . ' ' . $request->l_name;
            $tours->person_phone = $request->phone;
            $tours->person_email = $request->email;
            $tours->status =  0;
            $tours->is_approve =  0;
            $vendor['image'] = '';
            if ($request->file('image')) {
                $vendor['image'] = \App\Utils\ImageManager::upload('tour_and_travels/doc/', 'webp', $request->file('image'));
            }
            $tours->image = $vendor['image'];
            $tours->save();
            \App\Models\Seller::where('id', $vendor['id'])->update(['relation_id' => $tours->id]);
            \Illuminate\Support\Facades\DB::commit();
        } elseif ($request->from_submit == 'event') {
            $eventOrg = new \App\Models\EventOrganizer();
            $eventOrg->full_name = $request->f_name . ' ' . $request->l_name;
            $eventOrg->contact_number = $request->phone;
            $eventOrg->email_address = $request->email;
            $eventOrg->status =  0;
            $eventOrg->is_approve =  0;
            $vendor['image'] = '';
            if ($request->file('image')) {
                $vendor['image'] = \App\Utils\ImageManager::upload('event/organizer/', 'webp', $request->file('image'));
            }
            $eventOrg->image = $vendor['image'];
            $eventOrg->save();
            \App\Models\Seller::where('id', $vendor['id'])->update(['relation_id' => $eventOrg->id]);
            \Illuminate\Support\Facades\DB::commit();
        } elseif ($request->from_submit == 'trust') {
            $DonateTrust = new \App\Models\DonateTrust();
            $DonateTrust->memberlist = json_encode([['member_name' => $request->f_name . ' ' . $request->l_name, 'member_phone_no' => $request->phone, 'member_position' => "Owner"]]);
            $DonateTrust->trust_email = $request->email;
            $DonateTrust->status =  0;
            $DonateTrust->is_approve =  0;
            $vendor['image'] = '';
            if ($request->file('image')) {
                $vendor['image'] = \App\Utils\ImageManager::upload('donate/trust/', 'webp', $request->file('image'));
            }
            $DonateTrust->theme_image = $vendor['image'];
            $DonateTrust->save();
            \App\Models\Seller::where('id', $vendor['id'])->update(['relation_id' => $DonateTrust->id]);
            \Illuminate\Support\Facades\DB::commit();
        }
        $data = [
            'name' => $request['f_name'],
            'status' => 'pending',
            'subject' => translate('Vendor_Registration_Successfully_Completed'),
            'title' => translate('registration_Complete') . '!',
            'message' => translate('congratulation') . '!' . translate('Your_registration_request_has_been_send_to_admin_successfully') . '!' . translate('Please_wait_until_admin_reviewal') . '.',
        ];
        // event(new VendorRegistrationMailEvent($request['email'], $data));
        $data  = \App\Models\Seller::where('email', ($request['email'] ?? ""))->with('shop')->first();

        if ($data  && isset($request['email']) && filter_var($request['email'], FILTER_VALIDATE_EMAIL)) {
            $data['type'] = 'invitation';
            $data['email'] = $request['email'];
            $data['subject'] = 'Vendor_Registration_Successfully_Completed';
            $data['htmlContent'] = \Illuminate\Support\Facades\View::make(
                'email-templates.vendor-registration',
                compact('data')
            )->render();
            Helpers::emailSendMessage($data);
        }
        //Whatsap
        $message_data = [
            'shop_name' => $data->shop->name ?? $data['f_name'],
            'vendor_name' => $data['f_name'] . ' ' . $data['l_name'],
            'type' => 'text-with-media',
            'attachment' => asset('/storage/app/public/shop/banner/' . ($data->shop->banner ?? '')),
            'booking_date' => date('d-m-Y', strtotime($data['created_at'])),
            'seller_id' => $data['id'],
        ];

        $messages =  Helpers::whatsappMessage('ecom', 'Vendor Registration Successfully Completed', $message_data);
        return response()->json(
            [
                'redirectRoute' => route('vendor.auth.login')
            ]
        );
    }
}
