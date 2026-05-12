<?php

namespace App\Http\Controllers\Web;

use App\Utils\Helpers;
use App\Events\OrderStatusEvent;
use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\DeliveryMan;
use App\Models\DeliveryZipCode;
use App\Models\Order;
use App\Models\Chadhava_orders;
use App\Models\OrderDetail;
use App\Models\EventOrder;
use App\Models\EventsReview;
use App\Models\BirthJournalKundali;
use App\Models\Product;
use App\Models\ProductCompare;
use App\Models\RefundRequest;
use App\Models\Review;
use App\Models\Service_order;
use App\Models\Vippooja;
use App\Models\Service;
use App\Models\Astrologer\Astrologer;
use App\Models\Cities;
use App\Models\CityDetail;
use App\Models\CounsellingUser;
use App\Models\Country;
use App\Models\OfflinePoojaOrder;
use App\Models\OfflinepoojaRefundPolicy;
// use App\Models\OfflinepoojaReview;
use App\Models\OfflinepoojaSchedule;
use App\Models\Prashad_deliverys;
use App\Models\Seller;
use App\Models\ServiceReview;
use App\Models\ShippingAddress;
use App\Models\States;
use App\Models\Devotee;
use App\Models\SupportTicket;
use App\Models\UserFeedback;
use App\Models\UserKundali;
use App\Models\UserKundaliMilan;
use App\Models\WalletTransaction;
use App\Models\Wishlist;
use App\Traits\CommonTrait;
use App\User;
use App\Utils\ApiHelper;
use App\Utils\CustomerManager;
use App\Utils\ImageManager;
use App\Utils\OrderManager;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use function App\Utils\payment_gateways;
use Illuminate\Support\Facades\View as PdfView;
use App\Traits\PdfGenerator;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\File;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevel;
use Endroid\QrCode\Logo\Logo;
use Symfony\Component\HttpFoundation\File\File as SymfonyFile;

class UserProfileController extends Controller
{
    use CommonTrait;
    use PdfGenerator;

    public function __construct(
        private Order $order,
        private Seller $seller,
        private Product $product,
        private Review $review,
        private DeliveryMan $deliver_man,
        private ProductCompare $compare,
        private Wishlist $wishlist,
    ) {}

    public function user_profile(Request $request)
    {
        $wishlists = $this->wishlist->whereHas('wishlistProduct', function ($q) {
            return $q;
        })->where('customer_id', auth('customer')->id())->count();
        $total_order = $this->order->where('customer_id', auth('customer')->id())->count();
        $total_loyalty_point = auth('customer')->user()->loyalty_point;
        $total_wallet_balance = auth('customer')->user()->wallet_balance;
        $addresses = ShippingAddress::where('customer_id', auth('customer')->id())->latest()->get();
        $customer_detail = User::where('id', auth('customer')->id())->first();

        return view(VIEW_FILE_NAMES['user_profile'], compact('customer_detail', 'addresses', 'wishlists', 'total_order', 'total_loyalty_point', 'total_wallet_balance'));
    }

    public function user_account(Request $request)
    {
        $country_restrict_status = Helpers::get_business_settings('delivery_country_restriction');
        $customerDetail = User::where('id', auth('customer')->id())->first();
        return view(VIEW_FILE_NAMES['user_account'], compact('customerDetail'));
    }
    public function user_update(Request $request)
    {
        $request->validate([
            'f_name' => 'required',
            'l_name' => 'required',
            'phone' => [
                'required',
                Rule::unique('users', 'phone')->ignore(auth('customer')->id(), 'id'),
            ],
        ], [
            'f_name.required' => 'First name is required',
            'l_name.required' => 'Last name is required',
            'phone.required' => 'Phone is required',
        ]);

        if ($request['password']) {
            $request->validate([
                'password' => 'required|same:confirm_password',
            ]);
        }

        if (User::where('id', '!=', auth('customer')->id())->where(['phone' => $request['phone']])->first()) {
            Toastr::warning(translate('phone_already_taken'));
            return back();
        }

        $image = $request->file('image');

        if ($image != null) {
            $imageName = ImageManager::update('profile/', auth('customer')->user()->image, 'webp', $request->file('image'));
        } else {
            $imageName = auth('customer')->user()->image;
        }

        User::where('id', auth('customer')->id())->update([
            'image' => $imageName,
        ]);

        $userDetails = [
            'name'=>$request['f_name']." ".$request['l_name'],
            'f_name' => $request['f_name'],
            'l_name' => $request['l_name'],
            'phone' => $request['phone'],
            'password' => strlen($request['password']) > 5 ? bcrypt($request['password']) : auth('customer')->user()->password,
        ];
        $customerDetail = User::where(['id' => auth('customer')->id()])->first();
        if ($customerDetail['email'] != 'user@mahakal.com' && !filter_var($customerDetail['email'] ?? '', FILTER_VALIDATE_EMAIL)) {
            $userDetails['email'] = $request["email"];
        }
        if (auth('customer')->check()) {
            User::where(['id' => auth('customer')->id()])->update($userDetails);
            Toastr::info(translate('updated_successfully'));
            return redirect()->back();
        } else {
            return redirect()->back();
        }
    }

    public function account_address_add()
    {
        $country_restrict_status = Helpers::get_business_settings('delivery_country_restriction');
        $zip_restrict_status = Helpers::get_business_settings('delivery_zip_code_area_restriction');
        $default_location = Helpers::get_business_settings('default_location');

        $countries = $country_restrict_status ? $this->get_delivery_country_array() : COUNTRIES;

        $zip_codes = $zip_restrict_status ? DeliveryZipCode::all() : 0;

        return view(VIEW_FILE_NAMES['account_address_add'], compact('countries', 'zip_restrict_status', 'zip_codes', 'default_location'));
    }

    public function account_delete($id)
    {
        if (auth('customer')->id() == $id) {
            $user = User::find($id);

            $ongoing = ['out_for_delivery', 'processing', 'confirmed', 'pending'];
            $order = Order::where('customer_id', $user->id)->whereIn('order_status', $ongoing)->count();
            if ($order > 0) {
                Toastr::warning(translate('you_can`t_delete_account_due_ongoing_order'));
                return redirect()->back();
            }
            auth()->guard('customer')->logout();

            ImageManager::delete('/profile/' . $user['image']);
            session()->forget('wish_list');

            $user->delete();
            Toastr::info(translate('Your_account_deleted_successfully!!'));
            return redirect()->route('home');
        }

        Toastr::warning(translate('access_denied') . '!!');
        return back();
    }

    public function account_address(): View|RedirectResponse
    {
        $country_restrict_status = getWebConfig(name: 'delivery_country_restriction');
        $zip_restrict_status = getWebConfig(name: 'delivery_zip_code_area_restriction');

        $countries = $country_restrict_status ? $this->get_delivery_country_array() : COUNTRIES;
        $zip_codes = $zip_restrict_status ? DeliveryZipCode::all() : 0;

        $countriesName = [];
        $countriesCode = [];
        foreach ($countries as $country) {
            $countriesName[] = $country['name'];
            $countriesCode[] = $country['code'];
        }

        if (auth('customer')->check()) {
            $shippingAddresses = ShippingAddress::where('customer_id', auth('customer')->id())->latest()->get();
            return view('web-views.users-profile.account-address', compact('shippingAddresses', 'country_restrict_status', 'zip_restrict_status', 'countries', 'zip_codes', 'countriesName', 'countriesCode'));
        } else {
            return redirect()->route('home');
        }
    }

    public function address_store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required',
            'city' => 'required',
            'zip' => 'required',
            'country' => 'required',
            'state' => 'required',
            'address' => 'required',
        ]);

        // $country_restrict_status = Helpers::get_business_settings('delivery_country_restriction');
        // $zip_restrict_status = Helpers::get_business_settings('delivery_zip_code_area_restriction');

        // $country_exist = self::delivery_country_exist_check($request->country);
        // $zipcode_exist = self::delivery_zipcode_exist_check($request->zip);

        // if ($country_restrict_status && !$country_exist) {
        //     Toastr::error(translate('Delivery_unavailable_in_this_country!'));
        //     return back();
        // }

        // if ($zip_restrict_status && !$zipcode_exist) {
        //     Toastr::error(translate('Delivery_unavailable_in_this_zip_code_area!'));
        //     return back();
        // }

        $address = [
            'customer_id' => auth('customer')->check() ? auth('customer')->id() : null,
            'contact_person_name' => $request['name'],
            'address_type' => $request['addressAs'],
            'address' => $request['address'],
            'city' => $request['city'],
            'zip' => $request['zip'],
            'country' => $request['country'],
            'state' => $request['state'],
            'phone' => $request['phone'],
            'is_billing' => $request['is_billing'],
            'latitude' => $request['latitude'],
            'longitude' => $request['longitude'],
            'created_at' => now(),
            'updated_at' => now(),
        ];
        DB::table('shipping_addresses')->insert($address);

        Toastr::success(translate('address_added_successfully!'));

        if (theme_root_path() == 'default') {
            return back();
        } else {
            return redirect()->route('user-profile');
        }
    }

    public function address_edit(Request $request, $id)
    {
        $shippingAddress = ShippingAddress::where('customer_id', auth('customer')->id())->find($id);
        $country_restrict_status = getWebConfig(name: 'delivery_country_restriction');
        $zip_restrict_status = getWebConfig(name: 'delivery_zip_code_area_restriction');

        $delivery_countries = $country_restrict_status ? self::get_delivery_country_array() : COUNTRIES;
        $delivery_zipcodes = $zip_restrict_status ? DeliveryZipCode::all() : 0;

        $countriesName = [];
        $countriesCode = [];
        foreach ($delivery_countries as $country) {
            $countriesName[] = $country['name'];
            $countriesCode[] = $country['code'];
        }

        if (isset($shippingAddress)) {
            return view(VIEW_FILE_NAMES['account_address_edit'], compact('shippingAddress', 'country_restrict_status', 'zip_restrict_status', 'delivery_countries', 'delivery_zipcodes', 'countriesName', 'countriesCode'));
        } else {
            Toastr::warning(translate('access_denied'));
            return back();
        }
    }

    public function address_update(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required',
            'city' => 'required',
            'zip' => 'required',
            'country' => 'required',
            'state' => 'required',
            'address' => 'required',
        ]);

        // $country_restrict_status = Helpers::get_business_settings('delivery_country_restriction');
        // $zip_restrict_status = Helpers::get_business_settings('delivery_zip_code_area_restriction');

        // $country_exist = self::delivery_country_exist_check($request->country);
        // $zipcode_exist = self::delivery_zipcode_exist_check($request->zip);

        // if ($country_restrict_status && !$country_exist) {
        //     Toastr::error(translate('Delivery_unavailable_in_this_country!'));
        //     return back();
        // }

        // if ($zip_restrict_status && !$zipcode_exist) {
        //     Toastr::error(translate('Delivery_unavailable_in_this_zip_code_area!'));
        //     return back();
        // }

        $updateAddress = [
            'contact_person_name' => $request->name,
            'address_type' => $request->addressAs,
            'address' => $request->address,
            'city' => $request->city,
            'zip' => $request->zip,
            'country' => $request->country,
            'state' => $request->state,
            'phone' => $request->phone,
            'is_billing' => $request->is_billing,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        if (auth('customer')->check()) {
            ShippingAddress::where('id', $request->id)->update($updateAddress);
            Toastr::success(translate('address_updated_successfully!'));
            return theme_root_path() == 'default' ? redirect()->route('account-address') : redirect()->route('user-profile');
        } else {
            Toastr::error(translate('Insufficient_permission!'));
            return theme_root_path() == 'default' ? redirect()->route('account-address') : redirect()->route('user-profile');
        }
    }

    public function address_delete(Request $request)
    {
        if (auth('customer')->check()) {
            ShippingAddress::destroy($request->id);
            Toastr::success(translate('address_Delete_Successfully'));
            return redirect()->back();
        } else {
            return redirect()->back();
        }
    }

    public function account_payment()
    {
        if (auth('customer')->check()) {
            return view('web-views.users-profile.account-payment');
        } else {
            return redirect()->route('home');
        }
    }

    public function account_order(Request $request)
    {
        $order_by = $request->order_by ?? 'desc';
        if (theme_root_path() == 'theme_fashion') {
            $show_order = $request->show_order ?? 'ongoing';

            $array = ['pending', 'confirmed', 'out_for_delivery', 'processing'];
            $orders = $this->order->withSum('orderDetails', 'qty')
                ->where(['customer_id' => auth('customer')->id(), 'is_guest' => '0'])
                ->when($show_order == 'ongoing', function ($query) use ($array) {
                    $query->whereIn('order_status', $array);
                })
                ->when($show_order == 'previous', function ($query) use ($array) {
                    $query->whereNotIn('order_status', $array);
                })
                ->when($request['search'], function ($query) use ($request) {
                    $query->where('id', 'like', "%{$request['search']}%");
                })
                ->orderBy('id', $order_by)->paginate(10)->appends(['show_order' => $show_order, 'search' => $request->search]);
        } else {
            $orders = $this->order->withSum('orderDetails', 'qty')->where(['customer_id' => auth('customer')->id(), 'is_guest' => '0'])
                ->orderBy('id', $order_by)
                ->paginate(10);
        }
        $ordersAll = Order::all();
        $allOrder = Service_order::with(['order', 'services', 'vippoojas', 'chadhava', 'chadhavaOrders'])->where('customer_id', auth('customer')->id())->orderBy('id', $order_by)->paginate(10);
        $serviceOrder = Service_order::with('leads')->with('services')->where(['customer_id' => auth('customer')->id(), 'type' => 'pooja'])->orderBy('id', $order_by)->paginate(10, ['*'], 'pooja-page', request('pooja-page', 1));
        $vipOrder = Service_order::with('leads')->with('vippoojas')->where(['customer_id' => auth('customer')->id(), 'type' => 'vip'])->orderBy('id', $order_by)->paginate(10, ['*'], 'vip-page', request('vip-page', 1));
        $anushthanOrder = Service_order::with('leads')->with('vippoojas')->where(['customer_id' => auth('customer')->id(), 'type' => 'anushthan'])->orderBy('id', $order_by)->paginate(10, ['*'], 'anushthan-page', request('anushthan-page', 1));
        $counsellingOrder = Service_order::where(['customer_id' => auth('customer')->id(), 'type' => 'counselling'])->with('services')->orderBy('created_at', 'DESC')->paginate(10);
        $ChadhavaOrder = Chadhava_orders::where(['customer_id' => auth('customer')->id(), 'type' => 'chadhava'])->with('chadhava')->orderBy('created_at', 'DESC')->paginate(10, ['*'], 'chadhava-page', request('chadhava-page', 1));
        $offlinepoojaOrder = OfflinePoojaOrder::with('leads')->with('offlinePooja')->where(['customer_id' => auth('customer')->id()])->whereNot('payment_status',2)->orderBy('id', $order_by)->paginate(10);
        // $historyOrder = Service_order::with(['order' => function ($query) {
        // $query->where('order_status', 'confirmed');
        // }, 'services','vippoojas'])->where('customer_id', auth('customer')->id())->where('status', 1)->orderBy('id', $order_by)->paginate(10);
        // dd($allOrder);
        $eventOrders = \App\Models\EventOrder::where('user_id', auth('customer')->id())->whereIn('transaction_status', ['1', '2', '3'])->with(['eventid'])->orderBy('id', 'DESC')->paginate(10, ['*'], 'event-page', request('event-page', 1));
        $donateOrders = \App\Models\DonateAllTransaction::where('user_id', auth('customer')->id())->where('amount_status', 1)->with(['getTrust', 'adsTrust'])->orderBy('id', 'DESC')->paginate(10, ['*'], 'donate-page', request('donate-page', 1));
        $tourOrders = \App\Models\TourOrder::where('user_id', auth('customer')->id())->whereIn('amount_status', [1, 2, 3])->with(['Tour'])->orderBy('id', 'DESC')->paginate(10, ['*'], 'tour-page', request('tour-page', 1));

        $kundalis_order = BirthJournalKundali::where('user_id', auth('customer')->id())->where('payment_status', 1)
            ->whereHas('birthJournal', function ($query) {
                $query->where('name', 'kundali');
            })
            ->with(['birthJournal' => function ($query) {
                $query->where('name', 'kundali');
            }])->orderBy('id', 'DESC')->paginate(10, ['*'], 'paid-kundli-page', request('paid-kundli-page', 1));

        $kundali_milan_order = BirthJournalKundali::where('user_id', auth('customer')->id())->where('payment_status', 1)
            ->whereHas('birthJournal', function ($query) {
                $query->where('name', 'kundali_milan');
            })
            ->with(['birthJournal' => function ($query) {
                $query->where('name', 'kundali_milan');
            }])->orderBy('id', 'DESC')->paginate(10, ['*'], 'paid-kundlimilan-page', request('paid-kundlimilan-page', 1));
        $serviceOrder_all = Service_order::with('leads')->with('services')->where(['customer_id' => auth('customer')->id(), 'type' => 'pooja'])->orderBy('id', $order_by)->get();
        $vipOrder_all = Service_order::with('leads')->with('vippoojas')->where(['customer_id' => auth('customer')->id(), 'type' => 'vip'])->orderBy('id', $order_by)->get();
        $anushthanOrder_all = Service_order::with('leads')->with('vippoojas')->where(['customer_id' => auth('customer')->id(), 'type' => 'anushthan'])->orderBy('id', $order_by)->get();
        $counsellingOrder_all = Service_order::where(['customer_id' => auth('customer')->id(), 'type' => 'counselling'])->whereNot('payment_status',2)->with('services')->orderBy('id', $order_by)->get();
        $ChadhavaOrder_all = Chadhava_orders::where(['customer_id' => auth('customer')->id(), 'type' => 'chadhava'])->with('chadhava')->orderBy('id', $order_by)->get();
        $offlinepoojaOrder_all = OfflinePoojaOrder::where('customer_id', auth('customer')->id())->whereNot('payment_status',2)->with('offlinePooja')->orderBy('id', $order_by)->get()
            ->map(function ($item) {
                $item->type = 'offlinepooja';
                return $item;
            });

        $eventOrders_all = \App\Models\EventOrder::where('user_id', auth('customer')->id())->whereIn('transaction_status', ['1', '2', '3'])->with(['eventid'])->orderBy('id', $order_by)->get()->map(function ($order) {
            $order->type = 'event';
            return $order;
        });
        $donateOrders_all = \App\Models\DonateAllTransaction::where('user_id', auth('customer')->id())->where('amount_status', 1)->with(['getTrust', 'adsTrust'])->orderBy('id', $order_by)->get()->map(function ($order) {
            $order->type = 'donate';
            return $order;
        });
        $tourOrders_all = \App\Models\TourOrder::where('user_id', auth('customer')->id())->whereIn('amount_status', [1, 2, 3])->with(['Tour'])->orderBy('id', $order_by)->get()->map(function ($order) {
            $order->type = 'tour';
            return $order;
        });

        $kundalis_order_all = BirthJournalKundali::where('user_id', auth('customer')->id())->where('payment_status', 1)
            ->whereHas('birthJournal', function ($query) {
                $query->where('name', 'kundali');
            })
            ->with(['birthJournal' => function ($query) {
                $query->where('name', 'kundali');
            }])->orderBy('id', $order_by)->get()->map(function ($order) {
                $order->type = 'kundli';
                return $order;
            });

        $kundali_milan_order_all = BirthJournalKundali::where('user_id', auth('customer')->id())->where('payment_status', 1)
            ->whereHas('birthJournal', function ($query) {
                $query->where('name', 'kundali_milan');
            })
            ->with(['birthJournal' => function ($query) {
                $query->where('name', 'kundali_milan');
            }])->orderBy('id', $order_by)->get()->map(function ($order) {
                $order->type = 'kundli milan';
                return $order;
            });
        $ecomm_order_all = $this->order->withSum('orderDetails', 'qty')->where(['customer_id' => auth('customer')->id(), 'is_guest' => '0'])->orderBy('id', $order_by)->get()->map(function ($order) {
            $order->type = 'shop';
            return $order;
        });
        $paymentPublishedStatus = config('get_payment_publish_status');
        $paymentGatewayPublishedStatus = isset($paymentPublishedStatus[0]['is_published']) ? $paymentPublishedStatus[0]['is_published'] : 0;
        $payment_gateways_list = payment_gateways();
        $digital_payment = getWebConfig(name: 'digital_payment');
        $allOrders_alls = collect()
            ->merge($serviceOrder_all)
            ->merge($vipOrder_all)
            ->merge($anushthanOrder_all)
            ->merge($counsellingOrder_all)
            ->merge($ChadhavaOrder_all)
            ->merge($offlinepoojaOrder_all)
            ->merge($eventOrders_all)
            ->merge($donateOrders_all)
            ->merge($tourOrders_all)
            ->merge($kundalis_order_all)
            ->merge($kundali_milan_order_all)
            ->merge($ecomm_order_all);
        $sortedOrders = $allOrders_alls->sortByDesc('created_at')->values();
        $page = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage('all_order');
        $perPage = 10;
        $offset = ($page - 1) * $perPage;
        $paginatedOrders = new \Illuminate\Pagination\LengthAwarePaginator(
            $sortedOrders->slice($offset, $perPage)->values(), // Get paginated items
            $sortedOrders->count(), // Total items
            $perPage, // Items per page
            $page, // Current page
            ['path' => request()->url(), 'query' => request()->query(), 'pageName' => 'all_order'] // Maintain query parameters
        );
        return view(VIEW_FILE_NAMES['account_orders'], compact('ChadhavaOrder', 'allOrder', 'orders', 'order_by', 'serviceOrder', 'vipOrder', 'anushthanOrder', 'counsellingOrder', 'eventOrders', 'donateOrders', 'tourOrders', 'offlinepoojaOrder', 'paginatedOrders', 'kundalis_order', 'kundali_milan_order', 'payment_gateways_list', 'paymentGatewayPublishedStatus', 'digital_payment'));
    }

    public function account_order_product(Request $request)
    {
        $order_by = $request->order_by ?? 'desc';
        if (theme_root_path() == 'theme_fashion') {
            $show_order = $request->show_order ?? 'ongoing';

            $array = ['pending', 'confirmed', 'out_for_delivery', 'processing'];
            $orders = $this->order->withSum('orderDetails', 'qty')
                ->where(['customer_id' => auth('customer')->id(), 'is_guest' => '0'])
                ->when($show_order == 'ongoing', function ($query) use ($array) {
                    $query->whereIn('order_status', $array);
                })
                ->when($show_order == 'previous', function ($query) use ($array) {
                    $query->whereNotIn('order_status', $array);
                })
                ->when($request['search'], function ($query) use ($request) {
                    $query->where('id', 'like', "%{$request['search']}%");
                })
                ->orderBy('id', $order_by)->paginate(10)->appends(['show_order' => $show_order, 'search' => $request->search]);
        } else {
            $orders = $this->order->withSum('orderDetails', 'qty')->where(['customer_id' => auth('customer')->id(), 'is_guest' => '0'])
                ->orderBy('id', $order_by)
                ->paginate(10);
        }

        return view(VIEW_FILE_NAMES['account_orders_product'], compact('orders'));
    }
    public function account_order_pooja(Request $request)
    {
        $order_by = $request->order_by ?? 'desc';
        $serviceOrder = Service_order::with('leads')->with('services')->where(['customer_id' => auth('customer')->id(), 'type' => 'pooja'])->orderBy('id', $order_by)->paginate(10);
        return view(VIEW_FILE_NAMES['account_orders_pooja'], compact('serviceOrder'));
    }
    public function account_order_vip(Request $request)
    {
        $order_by = $request->order_by ?? 'desc';
        $vipOrder = Service_order::with('leads')->with('vippoojas')->where(['customer_id' => auth('customer')->id(), 'type' => 'vip'])->orderBy('id', $order_by)->paginate(10);
        return view(VIEW_FILE_NAMES['account_orders_vip'], compact('vipOrder'));
    }
    public function account_order_offlinepooja(Request $request)
    {
        $paymentPublishedStatus = config('get_payment_publish_status');
        $paymentGatewayPublishedStatus = isset($paymentPublishedStatus[0]['is_published']) ? $paymentPublishedStatus[0]['is_published'] : 0;
        $payment_gateways_list = payment_gateways();
        $digital_payment = getWebConfig(name: 'digital_payment');
        $order_by = $request->order_by ?? 'desc';
        $offlinepoojaOrder = OfflinePoojaOrder::with('leads')->with('offlinePooja')->whereNot('payment_status',2)->where(['customer_id' => auth('customer')->id()])->orderBy('id', $order_by)->paginate(10);
        return view(VIEW_FILE_NAMES['account_orders_offlinepooja'], compact('offlinepoojaOrder', 'payment_gateways_list', 'paymentGatewayPublishedStatus', 'digital_payment'));
    }
    public function account_order_anushthan(Request $request)
    {
        $order_by = $request->order_by ?? 'desc';
        $anushthanOrder = Service_order::with('leads')->with('vippoojas')->where(['customer_id' => auth('customer')->id(), 'type' => 'anushthan'])->orderBy('id', $order_by)->paginate(10);
        return view(VIEW_FILE_NAMES['account_orders_anushthan'], compact('anushthanOrder'));
    }
    public function account_order_chadhava(Request $request)
    {
        $order_by = $request->order_by ?? 'desc';
        $ChadhavaOrder = Chadhava_orders::where(['customer_id' => auth('customer')->id(), 'type' => 'chadhava'])->with('chadhava')->orderBy('created_at', 'DESC')->paginate(10);
        return view(VIEW_FILE_NAMES['account_orders_chadhava'], compact('ChadhavaOrder'));
    }
    public function account_order_counselling(Request $request)
    {
        $paymentPublishedStatus = config('get_payment_publish_status');
        $paymentGatewayPublishedStatus = isset($paymentPublishedStatus[0]['is_published']) ? $paymentPublishedStatus[0]['is_published'] : 0;
        $payment_gateways_list = payment_gateways();
        $digital_payment = getWebConfig(name: 'digital_payment');
        $order_by = $request->order_by ?? 'desc';
        $counsellingOrder = Service_order::where(['customer_id' => auth('customer')->id(), 'type' => 'counselling'])->whereNot('payment_status',2)->with('services')->orderBy('created_at', 'DESC')->paginate(10);
        return view(VIEW_FILE_NAMES['account_orders_counselling'], compact('counsellingOrder', 'payment_gateways_list', 'paymentGatewayPublishedStatus', 'digital_payment'));
    }
    public function account_order_event(Request $request)
    {
        $eventOrders = \App\Models\EventOrder::where('user_id', auth('customer')->id())->whereIn('transaction_status', ['1', '2', '3'])->with(['eventid'])->orderBy('id', 'DESC')->paginate(10);
        return view(VIEW_FILE_NAMES['account_orders_event'], compact('eventOrders'));
    }
    public function account_order_tour(Request $request)
    {
        $tourOrders = \App\Models\TourOrder::where('user_id', auth('customer')->id())->whereIn('amount_status', [1, 2, 3])->with(['Tour'])->orderBy('id', 'DESC')->paginate(10);
        return view(VIEW_FILE_NAMES['account_orders_tour'], compact('tourOrders'));
    }

    public function account_order_donate(Request $request)
    {
        $donateOrders = \App\Models\DonateAllTransaction::where('user_id', auth('customer')->id())->with(['getTrust', 'adsTrust'])->where('amount_status',1)->orderBy('id', 'DESC')->paginate(10);
        return view(VIEW_FILE_NAMES['account_orders_donate'], compact('donateOrders'));
    }

    public function donate_order_details(Request $request){
        $donateOrders = \App\Models\DonateAllTransaction::where("id",$request['id'])->where('user_id', auth('customer')->id())->with(['getTrust', 'adsTrust','users'])->where('amount_status',1)->first();
        if($donateOrders){
            return view(VIEW_FILE_NAMES['donate_orders_details'], compact('donateOrders'));
        }else{
            return back();
        }
    }

    public function account_vehicle_booking_order(Request $request)
    {
        $VehicleOrders = \App\Models\SelfVehicleOrder::where('user_id', auth('customer')->id())->with(['SelfCabData'])->orderBy('id', 'DESC')->paginate(10);

        return view(VIEW_FILE_NAMES['account_self_vehicle_booking'], compact('VehicleOrders'));
    }

    public function SelfVehicleOrderView(Request $request)
    {
        $VehicleOrders = \App\Models\SelfVehicleOrder::where('user_id', auth('customer')->id())->where('id', $request['id'])->with(['SelfCabData'])->first();
        if (!$VehicleOrders) {
            return back();
        }
        return view(VIEW_FILE_NAMES['account_self_vehicle_booking_view'], compact('VehicleOrders'));
    }
    public function SelfVehicleRefundRequest(Request $request)
    {
        // $getData = TourCancelTicket::where('order_id', $request->order_id)->first();
        // if ($getData) {
        //     return response()->json(['status' => 0, 'message' => 'No Found', 'recode' => 0, 'data' => []], 200);
        // }
        // $ticket = new TourCancelTicket();
        // $ticket->user_id = $request->user_id;
        // $ticket->order_id = $request->order_id;
        // $ticket->message = $request->msg;
        // $ticket->status = 1; //0
        // $ticket->save();

        // User::where('id', $request->user_id)->update(['wallet_balance' => DB::raw('wallet_balance + ' . ($request->amount ?? 0))]);

        // $wallet_transaction = new \App\Models\WalletTransaction();
        // $wallet_transaction->user_id = $request->user_id;
        // $wallet_transaction->transaction_id = \Illuminate\Support\Str::uuid();
        // $wallet_transaction->reference = 'tour order refund';
        // $wallet_transaction->transaction_type = 'tour_order_refund';
        // $wallet_transaction->balance = User::where('id', $request->user_id)->first()['wallet_balance'];
        // $wallet_transaction->credit = ($request->amount ?? 0);
        // $wallet_transaction->save();
        // $get_tourOrder = TourOrder::where('id', ($request->order_id ?? ''))->first();
        // TourOrder::where('id', $request->order_id)->update(['refund_status' => 1, 'status' => 2, 'refound_id' => "wallet", 'refund_date' => date("Y-m-d H:i:s"), 'refund_amount' => ($request->amount ?? 0), 'cab_assign' => 0, 'traveller_id' => ($get_tourOrder['cab_assign'] ?? 0), 'refund_query_id' => $ticket->id]);
        // $tourOrder = TourOrder::where('id', ($request->order_id ?? ''))->with(['Tour'])->first();
        // $message_data['orderId'] = ($tourOrder['order_id'] ?? '');
        // $message_data['title_name'] = ($tourOrder['Tour']['tour_name'] ?? '');
        // $message_data['booking_date'] = ($tourOrder['pickup_date'] ?? '');
        // $message_data['time'] = ($tourOrder['Tour']['pickup_time'] ?? '');
        // $message_data['place_name'] = ($tourOrder['Tour']['pickup_address'] ?? '');
        // $message_data['tour_type'] = ucwords(str_replace('_', ' ', (($tourOrder['Tour']['tour_type'] ?? ''))));
        // $message_data['final_amount'] = webCurrencyConverter(amount: (float)$tourOrder['amount'] ?? 0);
        // $message_data['customer_id'] =  $request->user_id;
        // \App\Utils\Helpers::whatsappMessage('tour', 'Tour Canceled', $message_data);

        return response()->json(['status' => 1, 'message' => 'added successfully', 'recode' => 1, 'data' => []], 200);
    }

    public function SelfVehicleReviewUpdate(Request $request)
    {
        $validatedData = $request->validate([
            'order_id'     => 'required|string',
            'user_id'      => 'required|integer',
            'self_vehicle_id'   => 'required|integer',
            'rating'       => 'required|integer|min:1|max:5',
            'comment'      => 'nullable|string',
        ]);

        \App\Models\SelfVehicleReview::updateOrCreate(
            [
                'order_id' => $request->input('order_id'),
                'user_id' => $request->input('user_id'),
            ],
            [
                'self_vehicle_id' => $request->input('self_vehicle_id'),
                'star'            => $request->input('rating'),
                'comment'         => $request->input('comment'),
                'is_edited'       => 1,
            ]
        );
        Toastr::success(translate('Review submitted successfully!!'));
        return redirect()->back();
    }

    public function history_order(Request $request)
    {
        $order_by = $request->order_by ?? 'desc';
        if (theme_root_path() == 'theme_fashion') {
            $show_order = $request->show_order ?? 'ongoing';

            $array = ['pending', 'confirmed', 'out_for_delivery', 'processing'];
            $orders = $this->order->withSum('orderDetails', 'qty')
                ->where(['customer_id' => auth('customer')->id(), 'is_guest' => '0'])
                ->when($show_order == 'ongoing', function ($query) use ($array) {
                    $query->whereIn('order_status', $array);
                })
                ->when($show_order == 'previous', function ($query) use ($array) {
                    $query->whereNotIn('order_status', $array);
                })
                ->when($request['search'], function ($query) use ($request) {
                    $query->where('id', 'like', "%{$request['search']}%");
                })
                ->orderBy('id', $order_by)->paginate(10)->appends(['show_order' => $show_order, 'search' => $request->search]);
        } else {
            $orders = $this->order->withSum('orderDetails', 'qty')->where(['customer_id' => auth('customer')->id(), 'is_guest' => '0'])
                ->orderBy('id', $order_by)
                ->paginate(10);
        }
        $ordersAll = Order::all();

        $historyOrder = Service_order::with(['order' => function ($query) {
            $query->where('order_status', 'confirmed');
        }, 'services', 'vippoojas'])->where('customer_id', auth('customer')->id())->where('status', 1)->orderBy('id', $order_by)->paginate(10);
        $chadhavaOrders = Chadhava_orders::where('customer_id', auth('customer')->id())->where('status', 1)->orderBy('id', $order_by)->paginate(10);
        return view(VIEW_FILE_NAMES['history-page'], compact('historyOrder', 'ordersAll', 'orders', 'order_by', 'chadhavaOrders'));
    }


    public function account_order_details(Request $request): View|RedirectResponse
    {
        $order = $this->order->with(['deliveryManReview', 'customer', 'offlinePayments'])
            ->where(['id' => $request['id'], 'customer_id' => auth('customer')->id(), 'is_guest' => '0'])
            ->first();

        if ($order) {
            $order?->details?->map(function ($detail) use ($order) {
                $order['total_qty'] += $detail->qty;

                $reviews = Review::where(['product_id' => $detail['product_id'], 'customer_id' => auth('customer')->id()])->whereNull('delivery_man_id')->get();
                $reviewData = null;
                foreach ($reviews as $review) {
                    if ($review->order_id == $detail->order_id) {
                        $reviewData = $review;
                    }
                }
                if (isset($reviews[0]) && !$reviewData) {
                    $reviewData = ($reviews[0]['order_id'] == null) ? $reviews[0] : null;
                }
                if ($reviewData) {
                    $reviewData['attachment'] = $reviewData['attachment'] ? json_decode($reviewData['attachment']) : [];
                }
                $detail['reviewData'] = $reviewData;
                return $order;
            });
            $response = Helpers::ShipWayGetreturnreasons(); // Call the helper function
            $refundReasons = [];
            if ($response['success'] == 1) {
                $refundReasons = $response['message']; // Get reasons list
            }
            return view(VIEW_FILE_NAMES['account_order_details'], [
                'order' => $order,
                'refund_day_limit' => getWebConfig(name: 'refund_day_limit'),
                'current_date' => Carbon::now(),
                'refundReasons' => $refundReasons,
            ]);
        }

        Toastr::warning(translate('invalid_order'));
        return redirect()->route('');
    }

    public function account_order_details_seller_info(Request $request)
    {
        $order = $this->order->with(['seller.shop'])->find($request->id);
        if (!$order) {
            Toastr::warning(translate('invalid_order'));
            return redirect()->route('');
        }

        $productIds = $this->product->active()->where(['added_by' => $order->seller_is])->where('user_id', $order->seller_id)->pluck('id')->toArray();
        $rating = $this->review->active()->whereIn('product_id', $productIds);
        $rating_count = $rating->count();
        $avg_rating = $rating->avg('rating');
        $product_count = count($productIds);

        $vendorRattingStatusPositive = 0;
        foreach ($rating->pluck('rating') as $singleRating) {
            ($singleRating >= 4 ? ($vendorRattingStatusPositive++) : '');
        }

        $rating_percentage = $rating_count != 0 ? ($vendorRattingStatusPositive * 100) / $rating_count : 0;

        return view(VIEW_FILE_NAMES['seller_info'], compact('avg_rating', 'product_count', 'rating_count', 'order', 'rating_percentage'));
    }

    public function account_order_details_delivery_man_info(Request $request)
    {

        $order = $this->order->with(['verificationImages', 'details.product', 'deliveryMan.rating', 'deliveryManReview', 'deliveryMan' => function ($query) {
            return $query->withCount('review');
        }])->find($request->id);

        if (!$order) {
            Toastr::warning(translate('invalid_order'));
            return redirect()->route('');
        }

        if (theme_root_path() == 'theme_fashion' || theme_root_path() == 'default') {
            foreach ($order->details as $details) {
                if ($details->product) {
                    if ($details->product->product_type == 'physical') {
                        $order['product_type_check'] = $details->product->product_type;
                        break;
                    } else {
                        $order['product_type_check'] = $details->product->product_type;
                    }
                }
            }
        }

        $delivered_count = $this->order->where(['order_status' => 'delivered', 'delivery_man_id' => $order->delivery_man_id, 'delivery_type' => 'self_delivery'])->count();

        return view(VIEW_FILE_NAMES['delivery_man_info'], compact('delivered_count', 'order'));
    }

    public function account_order_details_reviews(Request $request): View|RedirectResponse
    {
        $order = $this->order->with(['deliveryManReview', 'customer', 'offlinePayments', 'details'])
            ->where(['id' => $request['id'], 'customer_id' => auth('customer')->id(), 'is_guest' => '0'])
            ->first();


        if ($order) {

            $order?->details?->map(function ($detail) use ($order) {
                $order['total_qty'] += $detail->qty;

                $reviews = Review::where(['product_id' => $detail['product_id'], 'customer_id' => auth('customer')->id()])->whereNull('delivery_man_id')->get();
                $reviewData = null;
                foreach ($reviews as $review) {
                    if ($review->order_id == $detail->order_id) {
                        $reviewData = $review;
                    }
                }
                if (isset($reviews[0]) && !$reviewData) {
                    $reviewData = ($reviews[0]['order_id'] == null) ? $reviews[0] : null;
                }
                if ($reviewData) {
                    $reviewData['attachment'] = $reviewData['attachment'] ? json_decode($reviewData['attachment']) : [];
                }
                $detail['reviewData'] = $reviewData;
                return $order;
            });

            return view(VIEW_FILE_NAMES['order_details_review'], compact('order'));
        }
        Toastr::warning(translate('invalid_order'));
        return redirect()->route('');
    }


    public function account_wishlist()
    {
        if (auth('customer')->check()) {
            $wishlists = Wishlist::where('customer_id', auth('customer')->id())->get();
            return view('web-views.products.wishlist', compact('wishlists'));
        } else {
            return redirect()->route('home');
        }
    }

    public function account_tickets()
    {
        if (auth('customer')->check()) {
            $supportTickets = SupportTicket::where('customer_id', auth('customer')->id())->with(['TicketType', 'TicketIssue'])->orderByRaw("CASE WHEN status = 'open' THEN 0 ELSE 1 END, created_at DESC")->paginate(10);
            $TicketType = \App\Models\SupportType::where('status', 1)->get();
            $TicketIssue = \App\Models\SupportIssue::where('status', 1)->get();
            return view(VIEW_FILE_NAMES['account_tickets'], compact('supportTickets', 'TicketType', 'TicketIssue'));
        } else {
            return redirect()->route('home');
        }
    }

    public function submitSupportTicket(Request $request): RedirectResponse
    {
        $request->validate([
            'ticket_subject' => 'required',
            'ticket_type_id' => 'required',
            'ticket_issue_id' => 'required',
            // 'ticket_type' => 'required',
            'ticket_priority' => 'required',
            'ticket_description' => 'required_without_all:image.*',
            'image.*' => 'required_without_all:ticket_description|image|mimes:jpeg,png,jpg,gif|max:6000',
        ], [
            'ticket_subject.required' => translate('The_ticket_subject_is_required'),
            'ticket_type_id.required' => 'Type Option is Required',
            'ticket_issue_id.required' => 'issue Option is Required',
            // 'ticket_type.required' => translate('The_ticket_type_is_required'),
            'ticket_priority.required' => translate('The_ticket_priority_is_required'),
            'ticket_description.required_without_all' => translate('Either_a_ticket_description_or_an_image_is_required'),
            'image.*.required_without_all' => translate('Either_a_ticket_description_or_an_image_is_required'),
            'image.*.image' => translate('The_file_must_be_an_image'),
            'image.*.mimes' => translate('The_file_must_be_of_type:_jpeg,_png,_jpg,_gif'),
            'image.*.max' => translate('The_image_must_not_exceed_6_MB'),
        ]);

        $image = [];
        if ($request->file('image')) {
            foreach ($request['image'] as $key => $value) {
                $image_name = ImageManager::upload('support-ticket/', 'webp', $value);
                $image[] = $image_name;
            }
        }

        $ticket = [
            'subject' => $request['ticket_subject'],
            'ticket_type_id' => $request['ticket_type_id'],
            'ticket_issue_id' => $request['ticket_issue_id'],
            // 'type' => $request['ticket_type'],
            'customer_id' => auth('customer')->check() ? auth('customer')->id() : null,
            'priority' => $request['ticket_priority'],
            'description' => $request['ticket_description'],
            'attachment' => json_encode($image),
            'created_at' => now(),
            'updated_at' => now(),
        ];
        DB::table('support_tickets')->insert($ticket);
        return back();
    }

    public function single_ticket(Request $request)
    {
        $ticket = SupportTicket::with(['conversations' => function ($query) {
            $query->when(theme_root_path() == 'default', function ($sub_query) {
                $sub_query->orderBy('id', 'desc');
            });
        }, 'TicketType', 'TicketIssue'])->where('id', $request->id)->first();
        \App\Models\SupportTicketConv::where('support_ticket_id', $request->id)->where('read_user_status', 0)->update(['read_user_status' => 1]);
        return view(VIEW_FILE_NAMES['ticket_view'], compact('ticket'));
    }

    public function comment_submit(Request $request, $id)
    {
        if ($request->file('image') == null && empty($request['comment'])) {
            Toastr::error(translate('type_something') . '!');
            return back();
        }

        DB::table('support_tickets')->where(['id' => $id])->update([
            'status' => 'open',
            'updated_at' => now(),
        ]);

        $image = [];
        if ($request->file('image')) {
            $validator =  $request->validate([
                'image.*' => 'image|mimes:jpeg,png,jpg,gif|max:6000'
            ]);
            foreach ($request->image as $key => $value) {
                $image_name = ImageManager::upload('support-ticket/', 'webp', $value);
                $image[] = $image_name;
            }
        }
        DB::table('support_ticket_convs')->insert([
            'customer_message' => $request->comment,
            'attachment' => json_encode($image),
            'support_ticket_id' => $id,
            'position' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Toastr::success(translate('message_send_successfully') . '!');
        return back();
    }

    public function support_ticket_close($id)
    {
        DB::table('support_tickets')->where(['id' => $id])->update([
            'status' => 'close',
            'updated_at' => now(),
        ]);
        Toastr::success(translate('ticket_closed') . '!');
        return redirect('/account-tickets');
    }


    public function support_ticket_delete(Request $request)
    {

        if (auth('customer')->check()) {
            $support = SupportTicket::find($request->id);

            if ($support->attachment && count(json_decode($support->attachment)) > 0) {
                foreach (json_decode($support->attachment, true) as $image) {
                    ImageManager::delete('/support-ticket/' . $image);
                }
            }

            foreach ($support->conversations as $conversation) {
                if ($conversation->attachment && count(json_decode($conversation->attachment)) > 0) {
                    foreach (json_decode($conversation->attachment, true) as $image) {
                        ImageManager::delete('/support-ticket/' . $image);
                    }
                }
            }
            $support->conversations()->delete();

            $support->delete();
            return redirect()->back();
        } else {
            return redirect()->back();
        }
    }

    public function track_order()
    {
        if (auth('customer')->check()) {
            return view(VIEW_FILE_NAMES['tracking-page']);
        } else {
            return redirect()->route('customer.auth.login');
        }
    }
    public function track_order_wise_result(Request $request)
    {
        if (auth('customer')->check()) {
            $orderDetails = Order::with('orderDetails')->where('id', $request['order_id'])->whereHas('details', function ($query) {
                $query->where('customer_id', (auth('customer')->id()));
            })->first();

            if (!$orderDetails) {
                Toastr::warning(translate('invalid_order'));
                return redirect()->route('');
            }

            $isOrderOnlyDigital = self::getCheckIsOrderOnlyDigital($orderDetails);
            return view(VIEW_FILE_NAMES['track_order_wise_result'], compact('orderDetails', 'isOrderOnlyDigital'));
        }
        return back();
    }

    public function getCheckIsOrderOnlyDigital($order): bool
    {
        $isOrderOnlyDigital = true;
        if ($order->orderDetails) {
            foreach ($order->orderDetails as $detail) {
                $product = json_decode($detail->product_details);
                if ($product->product_type == 'physical') {
                    $isOrderOnlyDigital = false;
                }
            }
        }
        return $isOrderOnlyDigital;
    }

    public function track_order_result(Request $request)
    {
        $isOrderOnlyDigital = false;
        $user = auth('customer')->user();
        $user_phone = $request['phone_number'] ?? '';

        if (!isset($user)) {
            $userInfo = User::where('phone', $request['phone_number'])->orWhere('phone', 'like', "%{$request['phone_number']}%")->first();
            $order = Order::where('id', $request['order_id'])->first();

            if ($order && $order->is_guest) {
                $orderDetails = Order::with('shippingAddress')
                    ->where('id', $request['order_id'])
                    ->first();

                $orderDetails = ($orderDetails && $orderDetails->shippingAddress && $orderDetails->shippingAddress->phone == $request['phone_number']) ? $orderDetails : null;

                if (!$orderDetails) {
                    $orderDetails = Order::where('id', $request['order_id'])
                        ->whereHas('billingAddress', function ($query) use ($request) {
                            $query->where('phone', $request['phone_number']);
                        })->first();
                }
            } elseif ($userInfo) {
                $orderDetails = Order::where('id', $request['order_id'])->whereHas('details', function ($query) use ($userInfo) {
                    $query->where('customer_id', $userInfo->id);
                })->first();
            } else {
                Toastr::error(translate('invalid_Order_Id_or_phone_Number'));
                return redirect()->route('track-order.index', ['order_id' => $request['order_id'], 'phone_number' => $request['phone_number']]);
            }
        } else {
            $order = Order::where('id', $request['order_id'])->first();
            if ($order && $order->is_guest) {
                $orderDetails = Order::where('id', $request['order_id'])->whereHas('shippingAddress', function ($query) use ($request) {
                    $query->where('phone', $request['phone_number']);
                })->first();
            } elseif ($user->phone == $request['phone_number']) {
                $orderDetails = Order::where('id', $request['order_id'])->whereHas('details', function ($query) {
                    $query->where('customer_id', auth('customer')->id());
                })->first();
            }

            if ($request['from_order_details'] == 1) {
                $orderDetails = Order::where('id', $request['order_id'])->whereHas('details', function ($query) {
                    $query->where('customer_id', auth('customer')->id());
                })->first();
            }
        }

        $order_verification_status = getWebConfig(name: 'order_verification');

        if (isset($orderDetails)) {
            if ($orderDetails['order_type'] == 'POS') {
                Toastr::error(translate('this_order_is_created_by_') . ($orderDetails['seller_is'] == 'seller' ? 'vendor' : 'admin') . translate('_from POS') . ',' . translate('please_contact_with_') . ($orderDetails['seller_is'] == 'seller' ? 'vendor' : 'admin') . translate('_to_know_more_details') . '.');
                return redirect()->back();
            }
            $isOrderOnlyDigital = self::getCheckIsOrderOnlyDigital($orderDetails);
            return view(VIEW_FILE_NAMES['track_order'], compact('orderDetails', 'user_phone', 'order_verification_status', 'isOrderOnlyDigital'));
        }

        Toastr::error(translate('invalid_Order_Id_or_phone_Number'));
        return redirect()->route('track-order.index', ['order_id' => $request['order_id'], 'phone_number' => $request['phone_number']]);
    }

    public function track_last_order()
    {
        $orderDetails = OrderManager::track_order(Order::where('customer_id', auth('customer')->id())->latest()->first()->id);

        if ($orderDetails != null) {
            return view('web-views.order.tracking', compact('orderDetails'));
        } else {
            return redirect()->route('track-order.index')->with('Error', translate('invalid_Order_Id_or_phone_Number'));
        }
    }

    public function order_cancel(Request $request, $id)
    {
        $order = Order::where(['id' => $id])->first();
        if ($order['payment_method'] == 'cash_on_delivery' && $order['order_status'] == 'pending') {
            Helpers::ShipWayorderChancel($id);
            OrderManager::stock_update_on_order_status_change($order, 'canceled');
            Order::where(['id' => $id])->update([
                'delivery_partner' => 'self',
                'delivery_order_id' => null,
                'delivery_channel_id' => null,
                'delivery_shipment_id' => null,
                'order_status' => 'canceled'
            ]);
            Toastr::success(translate('successfully_canceled'));
        } elseif ($order['payment_method'] == 'offline_payment') {
            Toastr::error(translate('The_order_status_cannot_be_updated_as_it_is_an_offline_payment'));
        } else {
            Toastr::error(translate('status_not_changable_now'));
        }
        return back();
    }

    public function refund_request(Request $request, $id)
    {
        $order_details = OrderDetail::find($id);
        $user = auth('customer')->user();

        $wallet_status = Helpers::get_business_settings('wallet_status');
        $loyalty_point_status = Helpers::get_business_settings('loyalty_point_status');
        if ($loyalty_point_status == 1) {
            $loyalty_point = CustomerManager::count_loyalty_point_for_amount($id);

            if ($user->loyalty_point < $loyalty_point) {
                Toastr::warning(translate('you_have_not_sufficient_loyalty_point_to_refund_this_order') . '!!');
                return back();
            }
        }

        return view('web-views.users-profile.refund-request', compact('order_details'));
    }

    public function store_refund(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'order_details_id' => 'required',
            'amount' => 'required',
            'refund_reason' => 'required'

        ]);
        $order_details = OrderDetail::find($request->order_details_id);
        $user = auth('customer')->user();

        $loyalty_point_status = Helpers::get_business_settings('loyalty_point_status');
        if ($loyalty_point_status == 1) {
            $loyalty_point = CustomerManager::count_loyalty_point_for_amount($request->order_details_id);

            if ($user->loyalty_point < $loyalty_point) {
                Toastr::warning(translate('you_have_not_sufficient_loyalty_point_to_refund_this_order') . '!!');
                return back();
            }
        }
        $refund_request = new RefundRequest;
        $refund_request->order_details_id = $request->order_details_id;
        $refund_request->customer_id = auth('customer')->id();
        $refund_request->status = 'pending';
        $refund_request->amount = $request->amount;
        $refund_request->product_id = $order_details->product_id;
        $refund_request->order_id = $order_details->order_id;
        $refund_request->refund_reason = $request->refund_reason;
        $refund_request->reason_name = $request->reason_name;
        $refund_request->reason_data = $request->reason_data;

        if ($request->file('images')) {
            $product_images = [];
            foreach ($request->file('images') as $img) {
                $product_images[] = ImageManager::upload('refund/', 'webp', $img);
            }
            $refund_request->images = json_encode($product_images);
        }
        $refund_request->save();

        $order_details->refund_request = 1;
        $order_details->save();

        $order = Order::find($order_details->order_id);
        OrderStatusEvent::dispatch('confirmed', 'customer', $order);

        Toastr::success(translate('refund_requested_successful!!'));
        return redirect()->route('account-order-details', ['id' => $order_details->order_id]);
    }

    public function CreatePdfInvoice($id)
    {
        $orderData = EventOrder::where('id', $id)->with(['orderitem', 'eventid', 'userdata', 'coupon'])->first();
        $mpdf_view = PdfView::make('web-views.event.pdf.invoice', compact('orderData'));
        Helpers::gen_mpdf($mpdf_view, 'event_order_', $id);
    }
    public function CreateEventPass($id, $num = '')
    {
        $orderData = EventOrder::where('id', $id)->with(['orderitem', 'eventid', 'userdata', 'coupon'])->first();
        $Data = [
            "eventname" => $orderData['eventid']['event_name'],
            "price" => $orderData['amount'],
            'total_user' => $orderData['orderitem'][0]['no_of_seats'],
        ];
        $ticket = $orderData['orderitem'][0]['no_of_seats'];
        $url = route("verify-code-event-pass", [$id, ($num ?? 1)]);
        $qrCode = new QrCode($url);
        $writer = new PngWriter();
        $result = $writer->write($qrCode);
        $folder = storage_path('app/public/qrcodes');
        if (!File::exists($folder)) {
            File::makeDirectory($folder, 0777, true);
        }
        $filePath = $folder . "/qr_{$id}_{$num}.png";
        $result->saveToFile($filePath);
        // ///////////////////////
        $builder = new Builder(
            data: $url,
            writer: new PngWriter(),
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: \Endroid\QrCode\ErrorCorrectionLevel::High,
            size: 300,
            margin: 10,
            logoPath: public_path('assets/front-end/img/logo-png.png'),
            logoResizeToWidth: 90
        );
        $result = $builder->build();
        $folder = storage_path('app/public/qrcodes');
        if (!File::exists($folder)) {
            File::makeDirectory($folder, 0777, true);
        }
        $filePath = $folder . "/qr_{$id}_{$num}.png";
        $result->saveToFile($filePath);
        ////////////////////

        $imageData = "<img class='upload-img-view' src='" . getValidImage(path: 'storage/app/public/qrcodes/qr_' . $id . '_' . $num . '.png', type: 'backend-product') . "' alt=''>";
        if (!empty($num)) {
            return \View::make('web-views.event.pdf.pass1', compact('orderData', 'num', 'imageData', 'ticket', 'id'));
        } else {
            $mpdf_view = \View::make('web-views.event.pdf.pass', compact('orderData', 'imageData', 'ticket'));
            return  $mpdf_view;
        }
    }

    public function EventComment(Request $request)
    {
        $validator =  $request->validate([
            'star' => 'required|in:1,2,3,4,5',
            "message" => "required",
            "event_id" => "required",
            "order_id" => "required",
        ]);
        if (auth('customer')->check()) {
            $check = EventsReview::where('event_id', $request->event_id)->where('order_id', $request->order_id)->where('user_id', auth('customer')->id())->first();
            if (!$check || $check['is_edited'] == 0) {
                if (!empty($check)) {
                    $insert = EventsReview::find($check['id']);
                } else {
                    $insert = new EventsReview();
                }
                $insert->user_id = auth('customer')->id();
                $insert->event_id = $request->event_id;
                $insert->order_id = $request->order_id;
                $insert->comment = $request->message;
                $insert->star = $request->star;
                $insert->is_edited = 1;
                $insert->save();
                Toastr::success("Review Add Successfully");
            } else {
                Toastr::success("Review has already been added");
            }
            return back();
        }
    }
    public function generate_invoice($id)
    {
        $order = Order::with('seller')->with('shipping')->where('id', $id)->first();
        $data["email"] = $order->customer["email"];
        $data["order"] = $order;

        $mpdf_view = \View::make(VIEW_FILE_NAMES['order_invoice'], compact('order'));
        Helpers::gen_mpdf($mpdf_view, 'order_invoice_', $order->id);
    }

    public function refund_details($id)
    {
        $order_details = OrderDetail::find($id);
        $refund = RefundRequest::with(['product', 'order'])->where('customer_id', auth('customer')->id())
            ->where('order_details_id', $order_details->id)->first();
        $product = $this->product->find($order_details->product_id);
        $order = $this->order->find($order_details->order_id);

        if ($product) {
            return view(VIEW_FILE_NAMES['refund_details'], compact('order_details', 'refund', 'product', 'order'));
        }

        Toastr::error(translate('product_not_found'));
        return redirect()->back();
    }

    public function submit_review(Request $request, $id)
    {
        $order_details = OrderDetail::where(['id' => $id])->whereHas('order', function ($q) {
            $q->where(['customer_id' => auth('customer')->id(), 'payment_status' => 'paid']);
        })->first();

        if (!$order_details) {
            Toastr::error(translate('invalid_order!'));
            return redirect('/');
        }

        return view('web-views.users-profile.submit-review', compact('order_details'));
    }

    public function refer_earn(Request $request)
    {
        $ref_earning_status = Helpers::get_business_settings('ref_earning_status') ?? 0;
        if (!$ref_earning_status) {
            Toastr::error(translate('you_have_no_permission'));
            return redirect('/');
        }
        $customer_detail = User::where('id', auth('customer')->id())->first();

        return view(VIEW_FILE_NAMES['refer_earn'], compact('customer_detail'));
    }

    public function user_coupons(Request $request)
    {
        $seller_ids = Seller::approved()->pluck('id')->toArray();
        $seller_ids = array_merge($seller_ids, [NULL, '0']);

        $coupons = Coupon::with('seller')
            ->where(['status' => 1])
            ->whereIn('customer_id', [auth('customer')->id(), '0'])
            ->whereIn('customer_id', [auth('customer')->id(), '0'])
            ->whereDate('start_date', '<=', date('Y-m-d'))
            ->whereDate('expire_date', '>=', date('Y-m-d'))
            ->paginate(8);

        return view(VIEW_FILE_NAMES['user_coupons'], compact('coupons'));
    }

    public function ecommerce_coupons()
    {
        $coupons = Coupon::with('seller')
            ->where(['status' => 1])
            ->whereIn('coupon_type', ['first_order', 'free_delivery', 'discount_on_purchase'])
            ->whereDate('start_date', '<=', date('Y-m-d'))
            ->whereDate('expire_date', '>=', date('Y-m-d'))
            ->get();
        return response()->json(['status' => 200, 'coupons' => $coupons]);
    }
    
    public function chadhava_coupons()
    {
        $coupons = Coupon::with('seller')
            ->where(['status' => 1])
            ->where('coupon_type', 'chadhava')
            ->whereDate('start_date', '<=', date('Y-m-d'))
            ->whereDate('expire_date', '>=', date('Y-m-d'))
            ->get();
        return response()->json(['status' => 200, 'coupons' => $coupons]);
    }

    public function counselling_coupons()
    {
        $userId = auth('customer')->user()->id;
        $couponCodesAssigned = Service_order::where('customer_id', $userId)->where('type', 'counselling')->whereNotNull('coupon_code')->pluck('coupon_code')->toArray();

        $coupons = Coupon::with('seller')
            ->where(['status' => 1])
            ->where('coupon_type', 'counselling')
            ->whereDate('start_date', '<=', date('Y-m-d'))
            ->whereDate('expire_date', '>=', date('Y-m-d'))
            ->whereNotIn('code', $couponCodesAssigned)
            ->get();

        return response()->json(['status' => 200, 'coupons' => $coupons]);
    }

    public function pooja_coupons()
    {
        $userId = auth('customer')->user()->id;

        $couponCodesAssigned = Service_order::where('customer_id', $userId)
            ->where('type', 'pooja')
            ->whereNotNull('coupon_code')
            ->pluck('coupon_code')
            ->toArray();

        $coupons = Coupon::with('seller')
            ->where('status', 1)
            ->where('coupon_type', 'pooja')
            ->whereDate('start_date', '<=', now())
            ->whereDate('expire_date', '>=', now())
            ->where(function ($q) use ($userId) {
                $q->where('customer_id', 0) // For all users
                    ->orWhere('customer_id', $userId); // Or specifically assigned to this user
            })
            ->whereNotIn('code', $couponCodesAssigned)
            ->get();

        return response()->json(['status' => 200, 'coupons' => $coupons]);
    }

    public function vippooja_coupons()
    {
        $userId = auth('customer')->user()->id;

        $couponCodesAssigned = Service_order::where('customer_id', $userId)
            ->where('type', 'vip')
            ->whereNotNull('coupon_code')
            ->pluck('coupon_code')
            ->toArray();

        $coupons = Coupon::with('seller')
            ->where('status', 1)
            ->where('coupon_type', 'vippooja')
            ->whereDate('start_date', '<=', now())
            ->whereDate('expire_date', '>=', now())
            ->where(function ($q) use ($userId) {
                $q->where('customer_id', 0) // For all users
                    ->orWhere('customer_id', $userId); // Or specifically assigned to this user
            })
            ->whereNotIn('code', $couponCodesAssigned)
            ->get();

        return response()->json(['status' => 200, 'coupons' => $coupons]);
    }

    public function offlinepooja_coupons()
    {
        $userId = auth('customer')->user()->id;
        $couponCodesAssigned = OfflinePoojaOrder::where('customer_id', $userId)->whereNotNull('coupon_code')->pluck('coupon_code')->toArray();

        $coupons = Coupon::with('seller')
            ->where(['status' => 1])
            ->where('coupon_type', 'offlinepooja')
            ->whereDate('start_date', '<=', date('Y-m-d'))
            ->whereDate('expire_date', '>=', date('Y-m-d'))
            ->whereNotIn('code', $couponCodesAssigned)
            ->get();

        return response()->json(['status' => 200, 'coupons' => $coupons]);
    }

    public function instancevippooja_coupons()
    {
        $userId = auth('customer')->user()->id;
        $couponCodesAssigned = Service_order::where('customer_id', $userId)->where('type', 'vip')->whereNotNull('coupon_code')->pluck('coupon_code')->toArray();

        $coupons = Coupon::with('seller')
            ->where(['status' => 1])
            ->where('coupon_type', 'instance_vip')
            ->whereDate('start_date', '<=', date('Y-m-d'))
            ->whereDate('expire_date', '>=', date('Y-m-d'))
            ->whereNotIn('code', $couponCodesAssigned)
            ->get();

        return response()->json(['status' => 200, 'coupons' => $coupons]);
    }

    public function anushthanpooja_coupons()
    {
        $userId = auth('customer')->user()->id;

        $couponCodesAssigned = Service_order::where('customer_id', $userId)
            ->where('type', 'anushthan')
            ->whereNotNull('coupon_code')
            ->pluck('coupon_code')
            ->toArray();

        $coupons = Coupon::with('seller')
            ->where('status', 1)
            ->where('coupon_type', 'anushthan')
            ->whereDate('start_date', '<=', now())
            ->whereDate('expire_date', '>=', now())
            ->where(function ($q) use ($userId) {
                $q->where('customer_id', 0) // For all users
                    ->orWhere('customer_id', $userId); // Or specifically assigned to this user
            })
            ->whereNotIn('code', $couponCodesAssigned)
            ->get();

        return response()->json(['status' => 200, 'coupons' => $coupons]);
    }

    public function instanceanushthanpooja_coupons()
    {

        $userId = auth('customer')->user()->id;
        $couponCodesAssigned = Service_order::where('customer_id', $userId)->where('type', 'anushthan')->whereNotNull('coupon_code')->pluck('coupon_code')->toArray();

        $coupons = Coupon::with('seller')
            ->where(['status' => 1])
            ->where('coupon_type', 'instance_anushthan')
            ->whereDate('start_date', '<=', date('Y-m-d'))
            ->whereDate('expire_date', '>=', date('Y-m-d'))
            ->whereNotIn('code', $couponCodesAssigned)
            ->get();

        return response()->json(['status' => 200, 'coupons' => $coupons]);
    }

    public function saved_kundali()
    {
        $kundalis = UserKundali::where('user_id', auth('customer')->id())->orderBy('id', 'DESC')->paginate(10);
        return view('web-views.users-profile.saved-kundali', compact('kundalis'));
    }

    public function saved_kundali_show($id)
    {
        $userData = UserKundali::find($id);
        $savedDOB = $userData['dob'];
        if ($userData) {
            $dob = explode('-', $userData['dob']);
            $time = explode(':', $userData['time']);
            $apiData = array(
                'day' => $dob['2'],
                'month' => $dob['1'],
                'year' => $dob['0'],
                'hour' => $time['0'],
                'min' => $time['1'],
                'lat' => $userData['latitude'],
                'lon' => $userData['longitude'],
                'tzone' => $userData['timezone']
            );
            $astroData = json_decode(ApiHelper::astroApi('https://json.astrologyapi.com/v1/astro_details', 'hi', $apiData), true);
            $birthData = json_decode(ApiHelper::astroApi('https://json.astrologyapi.com/v1/birth_details', 'hi', $apiData), true);
            $panchangData = json_decode(ApiHelper::astroApi('https://json.astrologyapi.com/v1/basic_panchang', 'hi', $apiData), true);
            $lagnaData = json_decode(ApiHelper::astroApi('https://json.astrologyapi.com/v1/general_ascendant_report', 'hi', $apiData), true);
            if ($astroData && $birthData && $panchangData && $lagnaData) {
                return view('web-views.kundali.kundali-detail', compact('apiData', 'userData', 'savedDOB', 'astroData', 'birthData', 'panchangData', 'lagnaData'));
            } else {
                Toastr::error(translate('An error occured'));
                return back();
            }
        }
        Toastr::error(translate('No Data Found'));
        return back();
    }

    public function saved_kundali_milan()
    {
        $kundaliMilans = UserKundaliMilan::where('user_id', auth('customer')->id())->orderBy('id', 'DESC')->paginate(10);
        return view('web-views.users-profile.saved-kundali-milan', compact('kundaliMilans'));
    }

    public function saved_kundali_milan_show($id)
    {
        $usersData = UserKundaliMilan::find($id);
        $savedMaleDOB = $usersData->male_dob;
        $savedFemaleDOB = $usersData->female_dob;
        if ($usersData) {
            $maledob = explode('-', $usersData->male_dob);
            $maletime = explode(':', $usersData->male_time);
            $femaledob = explode('-', $usersData->female_dob);
            $femaletime = explode(':', $usersData->female_time);

            $apiData = array(
                'm_day' =>  $maledob['2'],
                'm_month' =>  $maledob['1'],
                'm_year' =>  $maledob['0'],
                'm_hour' =>  $maletime['0'],
                'm_min' =>  $maletime['1'],
                'm_lat' =>  $usersData->male_latitude,
                'm_lon' =>  $usersData->male_longitude,
                'm_tzone' =>  $usersData->male_timezone,
                'f_day' =>  $femaledob['2'],
                'f_month' =>  $femaledob['1'],
                'f_year' =>  $femaledob['0'],
                'f_hour' =>  $femaletime['0'],
                'f_min' =>  $femaletime['1'],
                'f_lat' =>  $usersData->female_latitude,
                'f_lon' =>  $usersData->female_longitude,
                'f_tzone' =>  $usersData->female_timezone
            );

            $astroData = json_decode(ApiHelper::astroApi('https://json.astrologyapi.com/v1/match_astro_details', 'hi', $apiData), true);
            $birthData = json_decode(ApiHelper::astroApi('https://json.astrologyapi.com/v1/match_birth_details', 'hi', $apiData), true);
            if ($astroData && $birthData) {
                return view('web-views.kundali-milan.kundali-milan-detail', compact('apiData', 'usersData', 'savedMaleDOB', 'savedFemaleDOB', 'astroData', 'birthData'));
            } else {
                Toastr::error(translate('An error occured'));
                return back();
            }
        }
        Toastr::error(translate('No Data Found'));
        return back();
    }
    // ----------------------------S E R V I C E    O R D E R   M A N A G E  M E N T -----------------------------------
    public function track_service_order()
    {
        return view(VIEW_FILE_NAMES['tracking-service-page']);
    }

    public function account_service_order(Request $request)
    {
        $orders = Service_order::with('leads')->with('services')->orderBy('order_id', 'DESC')->paginate(10);
        return view(VIEW_FILE_NAMES['account_service_orders'], compact('orders'));
    }
    // Service Order pooja, VIP, Anushthan Details 25/07/2025
    public function account_service_order_details($order_id, Request $request)
    {
        $serviceOrder = Service_order::where('order_id', $order_id)->with(['leads', 'services', 'vippoojas','packages','payments','pandit','product_leads'])->first();

        // If order not found, redirect with warning
        if (!$serviceOrder) {
            Toastr::warning(translate('invalid_order'));
            return redirect()->route('account-order');
        }

        $serviceOrder['customer'] = User::where('phone', $serviceOrder['leads']['person_phone'])->first();
        $serviceOrder['pooja_pandit'] = Astrologer::where('is_pandit_pooja', 'like', '%' . $serviceOrder['service_id'] . '%')->get();

        return view(VIEW_FILE_NAMES['account_service_order_details'], [
            'order' => $serviceOrder,
            'refund_day_limit' => getWebConfig(name: 'refund_day_limit'),
            'current_date' => Carbon::now(),
        ]);
    }
    // Service ORDER Review Controller
    public function account_service_order_details_reviews($order_id, Request $request): View|RedirectResponse
    {
        $order = $this->order->with(['deliveryManReview', 'customer', 'offlinePayments', 'details'])
            ->where(['id' => $request['id'], 'customer_id' => auth('customer')->id(), 'is_guest' => '0'])
            ->first();
        if ($order) {
            $order?->details?->map(function ($detail) use ($order) {
                $order['total_qty'] += $detail->qty;
                $reviews = Review::where(['product_id' => $detail['product_id'], 'customer_id' => auth('customer')->id()])->whereNull('delivery_man_id')->get();
                $reviewData = null;
                foreach ($reviews as $review) {
                    if ($review->order_id == $detail->order_id) {
                        $reviewData = $review;
                    }
                }
                if (isset($reviews[0]) && !$reviewData) {
                    $reviewData = ($reviews[0]['order_id'] == null) ? $reviews[0] : null;
                }
                if ($reviewData) {
                    $reviewData['attachment'] = $reviewData['attachment'] ? json_decode($reviewData['attachment']) : [];
                }
                $detail['reviewData'] = $reviewData;
                return $order;
            });

            return view(VIEW_FILE_NAMES['service_details_review'], compact('order'));
        }
        Toastr::warning(translate('invalid_order'));
        return redirect()->route('');
    }
    public function track_service_order_wise_result($order_id, Request $request)
    {
        if (auth('customer')->check()) {
            $orderDetails = Service_order::with('service')->where('id', $request['order_id'])->whereHas('details', function ($query) {
                $query->where('customer_id', (auth('customer')->id()));
            })->first();
            if (!$orderDetails) {
                Toastr::warning(translate('invalid_order'));
                return redirect()->route('');
            }
            $isOrderOnlyDigital = self::getCheckIsOrderOnlyDigital($orderDetails);
            return view(VIEW_FILE_NAMES['track_serive_wise_result'], compact('orderDetails', 'isOrderOnlyDigital'));
        }
        return back();
    }
        //  pooja, VIP, Anushthan Details 25/07/2025
    public function account_service_pandit_details($order_id, Request $request)
    {
        $panditData = Service_order::where('order_id', $order_id)->with(['leads', 'services', 'vippoojas','packages','payments','pandit','product_leads'])->first();
        if (!$panditData) {
            Toastr::warning(translate('invalid_order'));
            return redirect()->route('account-order');
        }
        return view(VIEW_FILE_NAMES['account_service_pandit_details'], [
            'order' => $panditData,
            'refund_day_limit' => getWebConfig(name: 'refund_day_limit'),
            'current_date' => Carbon::now(),
        ]);
        Toastr::warning(translate('invalid_order'));
        return redirect()->route('');
    }
    public function generate_invoice_service($id)
    {
        $companyPhone = getWebConfig(name: 'company_phone');
        $companyEmail = getWebConfig(name: 'company_email');
        $companyName = getWebConfig(name: 'company_name');
        $companyWebLogo = getWebConfig(name: 'company_web_logo');
        $details = Service_order::where('id', $id)->with('customers')->with('services')->with('leads')->with('packages')->with('payments')->with('product_leads.productsData')->first();
        // dd($details);
        // $mpdf_view = PdfView::make('web-views.order.invoice-service',compact('details'));
        $mpdf_view = \View::make(VIEW_FILE_NAMES['order_invoice_service'], compact('details', 'companyPhone', 'companyEmail', 'companyName', 'companyWebLogo'));
        Helpers::gen_mpdf($mpdf_view, 'order_invoice_service', $details['order_id']);
        // $this->generatePdf($mpdf_view, 'order_invoice_', );

    }
     //  pooja, VIP, Anushthan Details 25/07/2025
    public function account_service_sankalp($order_id)
    {
        $serviceOrder = Service_order::where('order_id', $order_id)->with(['customers','leads','packages','payments'])
        ->first();
        if (!$serviceOrder) {
            Toastr::warning(translate('invalid_order'));
            return redirect()->route('account-order');
        }
        $stateList = States::orderBy('name', 'asc')->get();
        if ($serviceOrder) {
            return view(VIEW_FILE_NAMES['account_service_sankalp'], ['order' => $serviceOrder, 'stateList' => $stateList]);
        }
        Toastr::warning(translate('invalid_order'));
        return redirect()->route('');
    }
    public function account_service_order_user_name($no)
    {
        $user = User::where('phone', $no)->first();
        if ($user) {
            return response()->json(['status' => 200, 'user' => $user]);
        }
        return response()->json(['status' => 400]);
    }
    public function sankalp_update($order_id, Request $request)
    {
        $serviceOrder = Service_order::where('order_id', $order_id)->first();
        if (!$serviceOrder) {
            Toastr::warning(translate('invalid_order'));
            return redirect()->route('account-order');
        }
        
        // Create service review with default rating
        $exists = ServiceReview::where('order_id', $serviceOrder->order_id)->exists();

        if (!$exists) {
            ServiceReview::create([
                'order_id'     => $serviceOrder->order_id,
                'user_id'      => $serviceOrder->customer_id,
                'service_id'   => $serviceOrder->service_id,
                'service_type' => $serviceOrder->type,
                'rating'       => '5',
            ]);
        }
        // 1. Prepare customer details for ServiceOrder update
            $cust_details = [
                'newPhone'   => $request->input('newPhone'),
                'gotra'      => $request->input('gotra'),
                'pincode'    => $request->input('pincode'),
                'city'       => $request->input('city'),
                'state'      => $request->input('state'),
                'house_no'   => $request->input('house_no'),
                'area'       => $request->input('area'),
                'landmark'   => $request->input('landmark'),
                'latitude'   => $request->input('latitude'),
                'longitude'  => $request->input('longitude'),
                'members'    => json_encode($request->input('members', [])),
                'is_prashad' => $request->input('is_prashad'),
                'is_edited'  => 1,
            ];

            // 2. Nullify address-related fields if is_prashad == 0
            if ((int) $request->input('is_prashad') === 0) {
                $cust_details = array_merge($cust_details, [
                    'pincode'   => null,
                    'city'      => null,
                    'state'     => null,
                    'house_no'  => null,
                    'area'      => null,
                    'landmark'  => null,
                    'latitude'  => null,
                    'longitude' => null,
                ]);
            }

            // 3. Fetch customer info and full name
            $customer = User::find($serviceOrder->customer_id);
            $full_name = '';
            if ($customer) {
                $full_name = trim(($customer->f_name ?? '') . ' ' . ($customer->l_name ?? ''));
                if (empty($full_name)) {
                    $full_name = $customer->name ?? '';
                }
            }

            // 4. Prepare Devotee table data
            $devoteeData = [
                'name'             => $full_name,
                'phone'            => $request->input('newPhone') ?: ($customer->phone ?? ''),
                'gotra'            => $request->input('gotra'),
                'service_order_id' => $order_id,
                'members'          => json_encode($request->input('members', [])),
                'address_city'     => $cust_details['city'],
                'address_pincode'  => $cust_details['pincode'],
                'address_state'    => $cust_details['state'],
                'house_no'         => $cust_details['house_no'],
                'area'             => $cust_details['area'],
                'latitude'         => $cust_details['latitude'],
                'longitude'        => $cust_details['longitude'],
                'landmark'         => $cust_details['landmark'],
                'is_prashad'       => $cust_details['is_prashad'],
                'type'       => $serviceOrder->type,
                'status'       => 1,
            ];

            // 5. Update service order
            if ($serviceOrder) {
                $serviceOrder->update($cust_details);
            }

            // 6. Update or create Devotee record
            Devotee::updateOrCreate(
                ['service_order_id' => $order_id], // Unique match
                $devoteeData
            );

        // If prashad is selected, create a Prashad delivery record
        if ((int) $request->input('is_prashad') === 1) {
            Prashad_deliverys::create([
                'seller_id' => '14',
                'order_id'      => $serviceOrder->order_id,
                'warehouse_id' => '61202',
                'service_id'    => $request->input('service_id'),
                'user_id'       => $request->input('user_id'),
                'product_id' => '853',
                'type'          => $serviceOrder->type,
                'payment_type' => 'P',
                'booking_date'  => $request->input('booking_date'),                
            ]);
        }


        // Fetch updated order data with relationships
        $sankalpData = Service_order::where('order_id', $order_id)->with(['customers', 'services','vippoojas', 'packages', 'leads'])->first();

        $membersList = json_decode($sankalpData->members, true);
        $formattedMembers = $membersList ? implode(', ', $membersList) : 'No members specified';

        // User and service information
        $userInfo = User::find($sankalpData->customer_id);

         // Determine service type
        if ($sankalpData->type === 'pooja') {
            $service_name = Service::where('id', $sankalpData->service_id)
                ->where('product_type', 'pooja')
                ->first();

            $bookingDetails = Service_order::where([
                ['service_id', $sankalpData->service_id],
                ['type', $serviceOrder->type],
                ['booking_date', $sankalpData->booking_date],
                ['customer_id', $sankalpData->customer_id],
                ['order_id', $order_id]
            ])->first();

            $message_data = [
                'service_name' => $service_name->name ?? '',
                'member_names' => $formattedMembers,
                'type'         => 'text-with-media',
                'attachment'   => asset('storage/app/public/pooja/thumbnail/' . ($service_name->thumbnail ?? '')),
                'booking_date' => date('d-m-Y', strtotime($sankalpData->booking_date)),
                'puja_venue'   => $service_name->pooja_venue ?? '',
                'orderId'      => $order_id,
                'prashad'      => $sankalpData->is_prashad == 1 ? 'Your Prasad is being prepared and will be dispatched within 7-8 days.' : '',
                'gotra'        => $request->input('gotra'),
                'customer_id'  => $sankalpData->customer_id,
            ];

            Helpers::whatsappMessage('whatsapp', 'Sankalp Information', $message_data);

            $this->sendSankalpEmail($userInfo, $service_name, $bookingDetails, $formattedMembers, $request);

        } elseif ($sankalpData->type === 'vip') {
            $service_name = Vippooja::where('id', $sankalpData->service_id)->where('is_anushthan', '0')->first();

            $bookingDetails = Service_order::where([
                ['service_id', $sankalpData->service_id],
                ['type', 'vip'],
                ['booking_date', $sankalpData->booking_date],
                ['customer_id', $sankalpData->customer_id],
                ['order_id', $order_id]
            ])->first();

            $message_data = [
                'service_name' => $sankalpData['vippoojas']['name'] ?? '',
                'member_names' => $formattedMembers,
                'gotra'        => $request->input('gotra'),
                'prashad'      => $sankalpData->is_prashad == 1 ? 'Your Prasad will be dispatched within 7-8 days.' : '',
                'type'         => 'text-with-media',
                'attachment'   => asset('/storage/app/public/pooja/vipthumbnail/' . ($service_name->thumbnail ?? '')),
                'booking_date' => date('d-m-Y', strtotime($sankalpData->booking_date)),
                'puja_venue'   => $service_name->pooja_venue ?? '',
                'pooja'        => 'VIP Pooja',
                'orderId'      => $order_id,
                'customer_id'  => $sankalpData->customer_id,
            ];

            Helpers::whatsappMessage('vipanushthan', 'Sankalp Information', $message_data);
            $this->sendSankalpEmail($userInfo, $service_name, $bookingDetails, $formattedMembers, $request);

        } else { // anushthan
            $service_name = Vippooja::where('id', $sankalpData->service_id)->where('is_anushthan', '1')->first();

            $bookingDetails = Service_order::where([
                ['service_id', $sankalpData->service_id],
                ['type', 'anushthan'],
                ['booking_date', $sankalpData->booking_date],
                ['customer_id', $sankalpData->customer_id],
                ['order_id', $order_id]
            ])->first();

            $message_data = [
                'service_name' => $sankalpData['vippoojas']['name'] ?? '',
                'member_names' => $formattedMembers,
                'gotra'        => $request->input('gotra'),
                'prashad'      => $sankalpData->is_prashad == 1 ? 'Your Prasad will be dispatched within 7-8 days.' : '',
                'type'         => 'text-with-media',
                'attachment'   => asset('/storage/app/public/pooja/vipthumbnail/' . ($service_name->thumbnail ?? '')),
                'booking_date' => date('d-m-Y', strtotime($sankalpData->booking_date)),
                'puja_venue'   => $service_name->pooja_venue ?? '',
                'pooja'        => 'VIP Pooja',
                'orderId'      => $order_id,
                'customer_id'  => $sankalpData->customer_id,
            ];

            Helpers::whatsappMessage('vipanushthan', 'Sankalp Information', $message_data);
            $this->sendSankalpEmail($userInfo, $service_name, $bookingDetails, $formattedMembers, $request);
        }

        Toastr::success(translate('sankalp_details_successfully_updated!!'));
        return redirect()->back();
    }

    /**
     * Send Sankalp Email
     */
    protected function sendSankalpEmail($userInfo, $service_name, $bookingDetails, $formattedMembers, $request)
    {
        $email = optional($userInfo)->email;
        if ($email && filter_var($email->email, FILTER_VALIDATE_EMAIL)) {
            $data = [
                'type'        => 'pooja',
                'email'       => $email,
                'subject'     => 'Information given by you for puja',
                'htmlContent' => \View::make(
                    'admin-views.email.email-template.pooja-sankalp-template',
                    compact('userInfo', 'service_name', 'bookingDetails', 'formattedMembers', 'request')
                )->render(),
            ];
            Helpers::emailSendMessage($data);
        }
    }
        // pooja, VIP, Anushthan Details 25/07/2025
    public function account_service_review($order_id)
    {
        $serviceOrder = Service_order::where('order_id', $order_id)->with(['leads', 'services', 'vippoojas','packages','payments','pandit','product_leads'])->first();
        if (!$serviceOrder) {
            Toastr::warning(translate('invalid_order'));
            return redirect()->route('account-order');
        }
        $serviceOrder['customer'] = User::where('phone', $serviceOrder['leads']['person_phone'])->first();
        $serviceOrder['pooja_pandit'] = Astrologer::where('is_pandit_pooja', 'like', '%' . $serviceOrder['service_id'] . '%')->get();
        $existingReview = ServiceReview::where('order_id', $order_id)->where('is_edited', 1)
            ->where('user_id', $serviceOrder['customer']->id)
            ->first();
        return view(VIEW_FILE_NAMES['account_service_review'], [
            'order' => $serviceOrder,
            'refund_day_limit' => getWebConfig(name: 'refund_day_limit'),
            'current_date' => Carbon::now(),
            'existingReview' => $existingReview,
        ]);
        Toastr::warning(translate('invalid_order'));
        return redirect()->route('');
    }

    //  pooja , VIP, Anushthan Details 25/07/2025
    public function submit_service_review($order_id, Request $request)
    {
        
        $validatedData = $request->validate([
            'user_id'      => 'required|integer',
            'service_id'   => 'required|integer',
            'service_type' => 'required|string',
            'rating'       => 'required|integer|min:1|max:5',
            'comment'      => 'nullable|string',
            'youtube_link' => 'nullable|url',
            'astro_id'     => 'required|integer',
        ]);

        $serviceOrder = Service_order::where('order_id', $order_id)->first();
        
        if (!$serviceOrder) {
            Toastr::error(translate('Order not found!'));
            return redirect()->back();
        }
        // Update or create review
        ServiceReview::where('order_id', $serviceOrder['order_id'])->update([
            'astro_id'     => $serviceOrder['pandit_assign'],
            'astro_id'     => $serviceOrder['pandit_assign'],
            'service_type' => $serviceOrder['type'],
            'user_id'      => $serviceOrder['customer_id'],
            'rating'       => $request->input('rating'),
            'service_id'   => $serviceOrder['service_id'],
            'comment'      => $request->input('comment'),
            'youtube_link' => $request->input('youtube_link'),
            'is_edited'    => 1,
        ]);
        Toastr::success(translate('Review submitted successfully!!'));
        return redirect()->back();
    }


    // Counselling Order Details
    public function account_counselling_order_details($order_id, Request $request)
    {
        $serviceOrder = Service_order::where('order_id', $order_id)->with('services')->with('payments')->with('customers')->first();
        if ($serviceOrder) {
            return view(VIEW_FILE_NAMES['account_counselling_order_details'], ['order' => $serviceOrder, 'refund_day_limit' => getWebConfig(name: 'refund_day_limit'), 'current_date' => Carbon::now(),]);
        }
        Toastr::warning(translate('invalid_order'));
        return redirect()->route('');
    }

    public function account_counselling_order_user_detail($order_id)
    {
        $serviceOrder = Service_order::where('order_id', $order_id)->with('counselling_user')->with('customers')->first();
        // dd($serviceOrder);
        $country = Country::all();
        if ($serviceOrder) {
            return view(VIEW_FILE_NAMES['account_counselling_order_user_detail'], ['order' => $serviceOrder, 'country' => $country]);
        }
        Toastr::warning(translate('invalid_order'));
        return redirect()->route('');
    }

    public function account_counselling_order_user_update(Request $request)
    {
        if ($request->type == 'add') {
            $user = new CounsellingUser();
            $user->order_id = $request->order_id;
            $user->mobile = $request->person_phone;
        } else if ($request->type == 'update') {
            $user = CounsellingUser::where('order_id', $request->order_id)->first();
        }
        $user->name = $request->name;
        $user->gender = $request->gender;
        $user->dob = $request->dob;
        $user->time = $request->time;
        $user->country = $request->country;
        $user->city = $request->places;
        $user->is_update = 1;
        if ($user->save()) {
            Toastr::success(translate('updated successfully'));
            return redirect()->route('account-counselling-order-user-detail', $request->order_id);
        }
        Toastr::error(translate('unable_to_update'));
        return redirect()->route('account-counselling-order-user-detail', $request->order_id);
    }

    public function account_counselling_order_user_name($no)
    {
        $user = User::where('phone', $no)->first();
        if ($user) {
            return response()->json(['status' => 200, 'user' => $user]);
        }
        return response()->json(['status' => 400]);
    }

    public function consultation_generate_invoice_service($id)
    {
        $companyPhone = getWebConfig(name: 'company_phone');
        $companyEmail = getWebConfig(name: 'company_email');
        $companyName = getWebConfig(name: 'company_name');
        $companyWebLogo = getWebConfig(name: 'company_web_logo');
        $details = Service_order::where('id', $id)->with('customers')->with('services')->with('payments')->with('counselling_user')->first();
        // dd($details);
        // $mpdf_view = PdfView::make('web-views.order.invoice-service',compact('details'));
        $mpdf_view = \View::make(VIEW_FILE_NAMES['consultation_order_invoice_service'], compact('details', 'companyPhone', 'companyEmail', 'companyName', 'companyWebLogo'));
        Helpers::gen_mpdf($mpdf_view, 'order_invoice_service', $details['order_id']);
        // $this->generatePdf($mpdf_view, 'order_invoice_', );
    }
    public function account_counselling_order_track($order_id)
    {
        $serviceOrder = Service_order::where('order_id', $order_id)->first();
        if ($serviceOrder) {
            return view(VIEW_FILE_NAMES['account_counselling_order_track'], ['order' => $serviceOrder,]);
        }
        Toastr::warning(translate('invalid_order'));
        return redirect()->route('');
    }

    public function account_counselling_review($order_id)
    {
        $serviceOrder = Service_order::where('order_id', $order_id)->with('leads')->with('services')->with('packages')->with('payments')->with('pandit')->with('product_leads')->first();
        $serviceOrder['customer'] = User::where('phone', $serviceOrder['leads']['person_phone'])->first();
        $serviceOrder['pooja_pandit'] = Astrologer::where('is_pandit_pooja', 'like', '%' . $serviceOrder['service_id'] . '%')->get();
        $existingReview = ServiceReview::where('order_id', $order_id)->where('is_edited', 1)
            ->where('user_id', $serviceOrder['customer']->id)
            ->first();
        return view(VIEW_FILE_NAMES['account_counselling_review'], [
            'order' => $serviceOrder,
            'refund_day_limit' => getWebConfig(name: 'refund_day_limit'),
            'current_date' => Carbon::now(),
            'existingReview' => $existingReview,
        ]);
        Toastr::warning(translate('invalid_order'));
        return redirect()->route('');
    }

    public function submit_counselling_review($order_id, Request $request)
    {

        $validatedData = $request->validate([
            'order_id' => 'required|string',
            'user_id' => 'required|integer',
            'service_id' => 'required|integer',
            'service_type' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);
        ServiceReview::where('order_id', $request->input('order_id'))->update([
            'astro_id' => $request->input('astro_id'),
            'service_type' => $request->input('service_type'),
            'user_id' => $request->input('user_id'),
            'rating' => $request->input('rating'),
            'service_id' => $request->input('service_id'),
            'comment' => $request->input('comment'),
            'is_edited' => 1,
        ]);
        // dd($serviceReview);
        Toastr::success(translate('Review submitted successfully!!'));
        return redirect()->back();
    }
    // pooja, VIP, Anushthan Details 25/07/2025
    public function account_service_order_track($order_id)
    {
        $serviceOrder = Service_order::where('order_id', $order_id)->first();
        if (!$serviceOrder) {
            Toastr::warning(translate('invalid_order'));
            return redirect()->route('account-order');
        }
        $prashadOrder = Prashad_deliverys::where('order_id', $order_id)->where('pooja_status', '1')->first();
        return view(
            VIEW_FILE_NAMES['account_service_order_track'],
            [
                'order' => $serviceOrder, 
                'prashad' => $prashadOrder,
            ]
        );
    }
    // Certificate
    // pooja, VIP, Anushthan Details 25/07/2025
    public function account_service_certificate($order_id)
    {
        $serviceOrder = Service_order::where('order_id', $order_id)->first();
        if (!$serviceOrder) {
            Toastr::warning(translate('invalid_order'));
            return redirect()->route('account-order');
        }
        if ($serviceOrder) {
            return view(VIEW_FILE_NAMES['account_service_certificate'], ['order' => $serviceOrder,]);
        }
        Toastr::warning(translate('invalid_order'));
        return redirect()->route('');
    }
    // certificate download
    public function downloadCertificate($id)
    {
        $serviceOrder = Service_Order::find($id);
        if (!$serviceOrder || !$serviceOrder->pooja_certificate) {
            Toastr::warning(translate('Certificate not found.'));
        }
        $filePath = asset('assets/back-end/img/certificate/pooja/');
        if (!file_exists($filePath . $serviceOrder->pooja_certificate)) {
            Toastr::warning(translate('File not found.'));
            return back();
        }
        $imageName = $serviceOrder->pooja_certificate;
        return response()->download($filePath, $imageName);
    }

    // VIP ORDER USER PROFILE ACCESS 26/07/2024
    public function account_vip_order_details($order_id, Request $request)
    {
        $vipOrder = Service_order::where('order_id', $order_id)->with('leads')->with('vippoojas')->with('packages')->with('payments')->with('pandit')->with('product_leads')->first();
        $vipOrder['customer'] = User::where('phone', $vipOrder['leads']['person_phone'])->first();
        $vipOrder['pooja_pandit'] = Astrologer::where('is_pandit_pooja', 'like', '%' . $vipOrder['service_id'] . '%')->get();
        return view(VIEW_FILE_NAMES['account_vip_order_details'], [
            'order' => $vipOrder,
            'refund_day_limit' => getWebConfig(name: 'refund_day_limit'),
            'current_date' => Carbon::now(),
        ]);
        Toastr::warning(translate('invalid_order'));
        return redirect()->route('');
    }
    public function account_vip_order_track($order_id)
    {
        $vipOrder = Service_order::where('order_id', $order_id)->first();
        $prashadOrder = Prashad_deliverys::where('order_id', $order_id)->where('pooja_status', '1')->first();

        return view(VIEW_FILE_NAMES['account_vip_order_track'], ['order' => $vipOrder, 'prashad' => $prashadOrder]);

        Toastr::warning(translate('invalid_order'));
        return redirect()->route('');
    }
    public function account_vip_sankalp($order_id)
    {
        $vipOrder = Service_order::where('order_id', $order_id)->with('customers')->with('leads')->with('packages')->with('payments')->first();
        // dd($vipOrder);
        $stateList = States::orderBy('name', 'asc')->get();
        if ($vipOrder) {
            return view(VIEW_FILE_NAMES['account_vip_sankalp'], ['order' => $vipOrder, 'stateList' => $stateList]);
        }
        Toastr::warning(translate('invalid_order'));
        return redirect()->route('');
    }
    public function VIPsankalp_update($order_id, Request $request)
    {
        $cust_details = [
            'newPhone' => $request->input('newPhone'),
            'gotra' => $request->input('gotra'),
            'pincode' => $request->input('pincode'),
            'city' => $request->input('city'),
            'state' => $request->input('state'),
            'house_no' => $request->input('house_no'),
            'area' => $request->input('area'),
            'landmark' => $request->input('landmark'),
            'latitude'  => $request->input('latitude'),
            'longitude' => $request->input('longitude'),
            'members' => json_encode($request->input('members')),
            'is_prashad' => $request->input('is_prashad'),
            'is_edited' => '1',
        ];

        if ($request->input('is_prashad') == 0) {
            $cust_details = array_merge($cust_details, [
                'pincode'   => null,
                'city'      => null,
                'state'     => null,
                'house_no'  => null,
                'area'      => null,
                'landmark'  => null,
                'latitude'  => null,
                'longitude' => null,
            ]);
        }

        $serviceOrder = Service_order::where('order_id', $order_id)->update($cust_details);
        if ($request->input('is_prashad') == 1) {
            $prashad_order = [
                'seller_id' => $request->input('seller_id'),
                'order_id' => $order_id,
                'warehouse_id' => $request->input('warehouse_id'),
                'service_id' => $request->input('service_id'),
                'user_id' =>   $request->input('user_id'),
                'product_id' => $request->input('product_id'),
                'type' => $request->input('type'),
                'payment_type' => $request->input('payment_type'),
                'booking_date' => $request->input('booking_date')
            ];
            $prashadOrder = Prashad_deliverys::create($prashad_order);
        }
        $stateList = States::orderBy('name', 'asc')->get();

        $sankalpData = Service_order::where('order_id', $order_id)->with(['customers', 'services', 'packages', 'leads'])->first();
        $UsersData = Service_order::where('type', 'vip')->with(['customers', 'vippoojas', 'packages', 'leads'])->first();
        $membersList = json_decode($sankalpData['members'], true);
        $formattedMembers = $membersList ? implode(', ', $membersList) : 'No members specified';

        // whatsapp
        $userInfo = \App\Models\User::where('id', ($sankalpData['customer_id'] ?? ""))->first();
        $service_name = \App\Models\Vippooja::where('id', ($sankalpData['service_id'] ?? ""))->where('is_anushthan', 0)->first();
        $bookingDetails = \App\Models\Service_order::where('service_id', ($sankalpData['service_id'] ?? ""))->where('type', 'vip')
            ->where('booking_date', ($sankalpData['booking_date'] ?? ""))
            ->where('customer_id', ($sankalpData['customer_id'] ?? ""))
            ->where('order_id', ($order_id ?? ""))
            ->first();

        $message_data = [
            'service_name' => $sankalpData['vippoojas']['name'],
            'member_names' => $formattedMembers,
            'gotra' => $request->input('gotra'),
            'prashad' => $sankalpData['is_prashad'] == 1 ? 'Your Prasad is being prepared and will be dispatched to your address within 7-8 days, only if you selected "Yes" for receiving Prasad.' : '',
            'type' => 'text-with-media',
            'attachment' =>  asset('/storage/app/public/pooja/vipthumbnail/' . $service_name->thumbnail),
            'booking_date' => date('d-m-Y', strtotime($sankalpData['booking_date'])),
            'puja_venue' => $service_name['pooja_venue'],
            'pooja' => 'VIP Pooja',
            'orderId' => $order_id,
            'customer_id' => ($sankalpData['customer_id'] ?? ""),
        ];
        $messages =  Helpers::whatsappMessage('vipanushthan', 'Sankalp Information', $message_data);
        // Mail Setup for Pooja Management Send to  User Email Id
        if ($userInfo && !empty($userInfo['email']) && filter_var($userInfo['email'], FILTER_VALIDATE_EMAIL)) {
            $data['type'] = 'pooja';
            $data['email'] = $userInfo['email'];
            $data['subject'] = 'Information given by you for puja';
            $data['htmlContent'] = \Illuminate\Support\Facades\View::make('admin-views.email.email-template.pooja-sankalp-template', compact('userInfo', 'service_name', 'bookingDetails', 'formattedMembers', 'request'))->render();

            Helpers::emailSendMessage($data);
        }
        Toastr::success(translate('sankalp_details_successfully_updated!!'));
        return  redirect()->back();
    }

    public function account_vip_pandit_details($order_id, Request $request)
    {
        $panditData = Service_order::where('order_id', $order_id)->with('vippoojas')->with('packages')->with('payments')->with('pandit')->with('product_leads')->first();
        return view(VIEW_FILE_NAMES['account_vip_pandit_details'], [
            'order' => $panditData,
            'refund_day_limit' => getWebConfig(name: 'refund_day_limit'),
            'current_date' => Carbon::now(),
        ]);
        Toastr::warning(translate('invalid_order'));
        return redirect()->route('');
    }
    // Certificate
    public function account_vip_certificate($order_id)
    {
        $vipOrder = Service_order::where('order_id', $order_id)->first();
        if ($vipOrder) {
            return view(VIEW_FILE_NAMES['account_vip_certificate'], ['order' => $vipOrder,]);
        }
        Toastr::warning(translate('invalid_order'));
        return redirect()->route('');
    }
    // certificate download
    public function VIPdownloadCertificate($id)
    {
        $vipOrder = Service_Order::find($id);
        if (!$vipOrder || !$vipOrder->pooja_certificate) {
            Toastr::warning(translate('Certificate not found.'));
        }
        $filePath = asset('assets/back-end/img/certificate/pooja/');
        if (!file_exists($filePath . $vipOrder->pooja_certificate)) {
            Toastr::warning(translate('File not found.'));
            return back();
        }
        $imageName = $vipOrder->pooja_certificate;
        return response()->download($filePath, $imageName);
    }
    public function generate_invoice_vip($id)
    {
        $companyPhone = getWebConfig(name: 'company_phone');
        $companyEmail = getWebConfig(name: 'company_email');
        $companyName = getWebConfig(name: 'company_name');
        $companyWebLogo = getWebConfig(name: 'company_web_logo');
        $details = Service_order::where('id', $id)->with('customers')->with('vippoojas')->with('leads')->with('packages')->with('payments')->with('product_leads.productsData')->first();
        // dd($details);
        // $mpdf_view = PdfView::make('web-views.order.invoice-service',compact('details'));
        $mpdf_view = \View::make(VIEW_FILE_NAMES['order_invoice_vip'], compact('details', 'companyPhone', 'companyEmail', 'companyName', 'companyWebLogo'));
        Helpers::gen_mpdf($mpdf_view, 'order_invoice_vip', $details['order_id']);
        // $this->generatePdf($mpdf_view, 'order_invoice_', );
    }

    public function account_vip_review($order_id)
    {
        $serviceOrder = Service_order::where('order_id', $order_id)->with('leads')->with('vippoojas')->with('packages')->with('payments')->with('pandit')->with('product_leads')->first();
        $serviceOrder['customer'] = User::where('phone', $serviceOrder['leads']['person_phone'])->first();
        $serviceOrder['pooja_pandit'] = Astrologer::where('is_pandit_pooja', 'like', '%' . $serviceOrder['service_id'] . '%')->get();
        $existingReview = ServiceReview::where('order_id', $order_id)->where('is_edited', 1)
            ->where('user_id', $serviceOrder['customer']->id)
            ->first();
        return view(VIEW_FILE_NAMES['account_vip_review'], [
            'order' => $serviceOrder,
            'refund_day_limit' => getWebConfig(name: 'refund_day_limit'),
            'current_date' => Carbon::now(),
            'existingReview' => $existingReview,
        ]);
        Toastr::warning(translate('invalid_order'));
        return redirect()->route('');
    }

    public function submit_vip_review($order_id, Request $request)
    {

        $validatedData = $request->validate([
            'order_id' => 'required|string',
            'user_id' => 'required|integer',
            'service_id' => 'required|integer',
            'service_type' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
            'youtube_link' => 'nullable|url',
        ]);
        ServiceReview::where('order_id', $request->input('order_id'))->update([
            'astro_id' => $request->input('astro_id'),
            'service_type' => $request->input('service_type'),
            'user_id' => $request->input('user_id'),
            'rating' => $request->input('rating'),
            'service_id' => $request->input('service_id'),
            'comment' => $request->input('comment'),
            'youtube_link' => $request->input('youtube_link'),
            'is_edited' => 1,
        ]);
        // dd($serviceReview);
        Toastr::success(translate('Review submitted successfully!!'));
        return redirect()->back();
    }

    // offline pooja
    public function account_offlinepooja_order_details($order_id, Request $request)
    {
        $offlinepoojaOrder = OfflinePoojaOrder::where('order_id', $order_id)->with('leads')->with('offlinePooja')->with('package')->with('payments')->with('customers')->first();
        // dd($offlinepoojaOrder);
        // $vipOrder['customer'] = User::where('phone',$vipOrder['leads']['person_phone'])->first();
        // $vipOrder['pooja_pandit'] = Astrologer::where('is_pandit_pooja','like','%'.$vipOrder['service_id'].'%')->get();
        return view(VIEW_FILE_NAMES['account_offlinepooja_order_details'], [
            'order' => $offlinepoojaOrder,
            'refund_day_limit' => getWebConfig(name: 'refund_day_limit'),
            'current_date' => Carbon::now(),
        ]);
        Toastr::warning(translate('invalid_order'));
        return redirect()->route('');
    }

    public function account_offlinepooja_order_track($order_id)
    {
        $offlinepoojaOrder = OfflinePoojaOrder::where('order_id', $order_id)->first();
        if ($offlinepoojaOrder) {
            return view(VIEW_FILE_NAMES['account_offlinepooja_order_track'], ['order' => $offlinepoojaOrder,]);
        }
        Toastr::warning(translate('invalid_order'));
        return redirect()->route('');
    }

    public function account_offlinepooja_sankalp($order_id)
    {
        $cityData = $state = "";
        $offlinepoojaOrder = OfflinePoojaOrder::where('order_id', $order_id)->with('offlinePooja')->with('customers')->with('leads')->with('package')->with('payments')->with('temple')->first();
        if (!empty($offlinepoojaOrder->city)) {
            $cityData = CityDetail::where('name',$offlinepoojaOrder->city)->get();
            $state = Cities::where('id',$cityData[0]['city_id'])->with('states')->first();
        }
        // dd($offlinepoojaOrder);
        // $stateList = States::orderBy('name', 'asc')->get();
        if ($offlinepoojaOrder) {
            return view(VIEW_FILE_NAMES['account_offlinepooja_sankalp'], ['order' => $offlinepoojaOrder, 'state' => $state, 'cityData' => $cityData]);
        }
        Toastr::warning(translate('invalid_order'));
        return redirect()->route('');
    }

    public function offlinepoojasankalp_update($order_id, Request $request)
    {
        $cust_details = [
            'pooja_method' => $request->input('pooja_method'),
            'pooja_venue_type' => $request->input('pooja_venue_type'),
            'temple_id' => $request->input('pooja_venue_type')=='temple'?$request->input('temple_id'):null,
            'venue_address' => $request->input('pooja_venue_type')=='address'?$request->input('venue_address'):null,
            'state' => $request->input('pooja_venue_type')=='address'?$request->input('state'):null,
            'city' => $request->input('pooja_venue_type')=='address'?$request->input('city'):null,
            'pincode' => $request->input('pooja_venue_type')=='address'?$request->input('pincode'):null,
            'latitude' => $request->input('pooja_venue_type')=='address'?$request->input('latitude'):null,
            'longitude' => $request->input('pooja_venue_type')=='address'?$request->input('longitude'):null,
            'booking_date' => $request->input('booking_date'),
            'landmark' => $request->input('pooja_venue_type')=='address'?$request->input('landmark'):null,
            'is_edited' => '1'
        ];
        $offlinepoojaOrder = OfflinePoojaOrder::where('order_id', $order_id)->update($cust_details);
        $orderDetail = OfflinePoojaOrder::where('order_id', $order_id)->first();
        ServiceReview::create([
            'order_id' => $order_id,
            'user_id' => $orderDetail->customer_id,
            'service_id' => $orderDetail->service_id,
            'service_type' => 'offlinepooja',
            'rating' => 5,
        ]);
        $stateList = States::orderBy('name', 'asc')->get();
        Toastr::success(translate('sankalp_details_successfully_updated!!'));
        return  redirect()->back();
    }

    public function account_offlinepooja_pandit_details($order_id, Request $request)
    {
        $panditData = OfflinePoojaOrder::where('order_id', $order_id)->with('offlinePooja')->with('package')->with('payments')->with('pandit')->first();
        return view(VIEW_FILE_NAMES['account_offlinepooja_pandit_details'], [
            'order' => $panditData,
            'refund_day_limit' => getWebConfig(name: 'refund_day_limit'),
            'current_date' => Carbon::now(),
        ]);
        Toastr::warning(translate('invalid_order'));
        return redirect()->route('');
    }

    // Certificate
    public function account_offlinepooja_certificate($order_id)
    {
        $offlinepoojaOrder = OfflinePoojaOrder::where('order_id', $order_id)->first();
        if ($offlinepoojaOrder) {
            return view(VIEW_FILE_NAMES['account_offlinepooja_certificate'], ['order' => $offlinepoojaOrder,]);
        }
        Toastr::warning(translate('invalid_order'));
        return redirect()->route('');
    }

    // certificate download
    public function offlinepoojadownloadCertificate($id)
    {
        $offlinepoojaOrder = OfflinePoojaOrder::find($id);
        if (!$offlinepoojaOrder || !$offlinepoojaOrder->pooja_certificate) {
            Toastr::warning(translate('Certificate not found.'));
        }
        $filePath = asset('assets/back-end/img/certificate/offlinepooja/');
        if (!file_exists($filePath . $offlinepoojaOrder->pooja_certificate)) {
            Toastr::warning(translate('File not found.'));
            return back();
        }
        $imageName = $offlinepoojaOrder->pooja_certificate;
        return response()->download($filePath, $imageName);
    }

    public function generate_invoice_offlinepooja($id)
    {
        $companyPhone = getWebConfig(name: 'company_phone');
        $companyEmail = getWebConfig(name: 'company_email');
        $companyName = getWebConfig(name: 'company_name');
        $companyWebLogo = getWebConfig(name: 'company_web_logo');
        $details = OfflinePoojaOrder::where('id', $id)->with('customers')->with('offlinePooja')->with('leads')->with('package')->with('payments')->first();
        // dd($details);
        // $mpdf_view = PdfView::make('web-views.order.invoice-service',compact('details'));
        $mpdf_view = \View::make(VIEW_FILE_NAMES['order_invoice_offlinepooja'], compact('details', 'companyPhone', 'companyEmail', 'companyName', 'companyWebLogo'));
        Helpers::gen_mpdf($mpdf_view, 'order_invoice_offlinepooja', $details['order_id']);
        // $this->generatePdf($mpdf_view, 'order_invoice_', );
    }

    public function account_offlinepooja_review($order_id)
    {
        $serviceOrder = OfflinePoojaOrder::where('order_id', $order_id)->with('leads')->with('offlinePooja')->with('package')->with('payments')->with('pandit')->first();
        $serviceOrder['customer'] = User::where('phone', $serviceOrder['leads']['person_phone'])->first();
        $serviceOrder['pooja_pandit'] = Astrologer::where('is_pandit_offlinepooja', 'like', '%' . $serviceOrder['service_id'] . '%')->get();
        $existingReview = ServiceReview::where('order_id', $order_id)->where('service_type', 'offlinepooja')
            ->where('user_id', $serviceOrder['customer']->id)->where('is_edited', 1)
            ->first();
        return view(VIEW_FILE_NAMES['account_offlinepooja_review'], [
            'order' => $serviceOrder,
            'refund_day_limit' => getWebConfig(name: 'refund_day_limit'),
            'current_date' => Carbon::now(),
            'existingReview' => $existingReview,
        ]);
        Toastr::warning(translate('invalid_order'));
        return redirect()->route('');
    }

    public function submit_offlinepooja_review($order_id, Request $request)
    {
        $serviceReview = ServiceReview::where('order_id', $request->input('order_id'))->update([
            'astro_id' => $request->input('astro_id'),
            'service_type' => 'offlinepooja',
            'rating' => $request->input('rating'),
            'comment' => $request->input('comment'),
            'is_edited' => 1,
        ]);
        // dd($serviceReview);
        if ($serviceReview) {
            Toastr::success(translate('Review submitted successfully!'));
        } else {
            Toastr::success(translate('An error occured!'));
        }
        return redirect()->back();
    }

    public function offlinepooja_schedule($orderId)
    {
        $schedulePercent = 0;

        // get days difference
        $orderData = OfflinePoojaOrder::where('order_id', $orderId)->first();
        $bookingDate = Carbon::parse($orderData->booking_date);
        $today = Carbon::today();
        $daysDiff = $today->diffInDays($bookingDate, false);

        // get days percent
        $scheduleData = OfflinepoojaSchedule::where('status', 1)->orderBy('days')->get();
        $exactMatch = $scheduleData->firstWhere('days', $daysDiff);
        if ($exactMatch) {
            $schedulePercent = $exactMatch->percent;
        } else {
            $greaterMatch = $scheduleData->where('days', '>', $daysDiff)->first();
            if ($greaterMatch) {
                $schedulePercent = $greaterMatch->percent;
            } else {
                $schedulePercent = $scheduleData->last()->percent;
            }
        }

        // schedule price
        $poojaPrice = $orderData->package_main_price;
        $schedulePrice = ($poojaPrice * $schedulePercent) / 100;

        //wallet amount
        $walletAmt = User::where('id', $orderData->customer_id)->value('wallet_balance');

        return response()->json(['status' => true, 'schedulePrice' => $schedulePrice, 'walletAmount' => $walletAmt]);
    }

    public function offlinepooja_remainingpay($orderId, $customerId)
    {
        $remainAmt = OfflinePoojaOrder::where('order_id', $orderId)->value('remain_amount');
        $walletAmt = User::where('id', $customerId)->value('wallet_balance');
        return response()->json(['status' => true, 'remainAmount' => $remainAmt, 'walletAmount' => $walletAmt]);
    }

    public function offlinepooja_cancle_order($orderId)
    {
        $refundPercent = 0;

        // get days difference
        $orderData = OfflinePoojaOrder::where('order_id', $orderId)->first();
        $bookingDate = Carbon::parse($orderData->booking_date);
        $today = Carbon::today();
        $daysDiff = $today->diffInDays($bookingDate, false);

        // get days percent
        $scheduleData = OfflinepoojaRefundPolicy::where('status', 1)->orderBy('days')->get();
        $exactMatch = $scheduleData->firstWhere('days', $daysDiff);
        if ($exactMatch) {
            $refundPercent = $exactMatch->percent;
        } else {
            $greaterMatch = $scheduleData->where('days', '>', $daysDiff)->first();
            if ($greaterMatch) {
                $refundPercent = $greaterMatch->percent;
            } else {
                $refundPercent = $scheduleData->last()->percent;
            }
        }

        // schedule price
        $userPaid = $orderData->pay_amount;
        $refundPrice = ($userPaid * $refundPercent) / 100;
        return response()->json(['status' => true, 'refundPrice' => $refundPrice]);
    }

    public function offlinepooja_cancle_order_submit(Request $request)
    {
        $orderCancle = OfflinePoojaOrder::where('order_id', $request->order_id)->update(['status' => 2, 'is_edited' => 1, 'order_canceled' => now(), 'order_canceled_reason' => $request->order_canceled_reason, 'canceled_by' => 'user', 'refund_status' => 1, 'refund_amount' => $request->refund_amount]);
        if ($orderCancle) {
            $walletBal = User::where('id', auth('customer')->id())->get()->value('wallet_balance');
            $currentBal = $walletBal + $request->refund_amount;
            $wallet_transaction = new WalletTransaction();
            $wallet_transaction->user_id = auth('customer')->id();
            $wallet_transaction->transaction_id = \Str::uuid();
            $wallet_transaction->reference = 'offline pooja order payment';
            $wallet_transaction->transaction_type = 'offline_pooja_order_place';
            $wallet_transaction->credit = $request->refund_amount;
            $wallet_transaction->balance = $currentBal;
            $wallet_transaction->save();
            User::where('id', auth('customer')->id())->update(['wallet_balance' => $currentBal]);
        }
        return redirect()->back();
    }

    // ANUSHTHAN ORDER USER PROFILE ACCESS 26/07/2024
    public function account_anushthan_order_details($order_id, Request $request)
    {
        $anushthanOrder = Service_order::where('order_id', $order_id)->with('leads')->with('vippoojas')->with('packages')->with('payments')->with('pandit')->with('product_leads')->first();
        $anushthanOrder['customer'] = User::where('phone', $anushthanOrder['leads']['person_phone'])->first();
        $anushthanOrder['pooja_pandit'] = Astrologer::where('is_pandit_pooja', 'like', '%' . $anushthanOrder['service_id'] . '%')->get();
        return view(VIEW_FILE_NAMES['account_anushthan_order_details'], [
            'order' => $anushthanOrder,
            'refund_day_limit' => getWebConfig(name: 'refund_day_limit'),
            'current_date' => Carbon::now(),
        ]);
        Toastr::warning(translate('invalid_order'));
        return redirect()->route('');
    }
    public function account_anushthan_order_track($order_id)
    {
        $anushthanOrder = Service_order::where('order_id', $order_id)->first();
        $prashadOrder = Prashad_deliverys::where('order_id', $order_id)->where('pooja_status', '1')->first();

        return view(VIEW_FILE_NAMES['account_anushthan_order_track'], ['order' => $anushthanOrder, 'prashad' => $prashadOrder]);

        Toastr::warning(translate('invalid_order'));
        return redirect()->route('');
    }
    public function account_anushthan_sankalp($order_id)
    {
        $anushthanOrder = Service_order::where('order_id', $order_id)->with('customers')->with('leads')->with('packages')->with('payments')->first();
        // dd($anushthanOrder);
        $stateList = States::orderBy('name', 'asc')->get();
        if ($anushthanOrder) {
            return view(VIEW_FILE_NAMES['account_anushthan_sankalp'], ['order' => $anushthanOrder, 'stateList' => $stateList]);
        }
        Toastr::warning(translate('invalid_order'));
        return redirect()->route('');
    }

    public function Anushthansankalp_update($order_id, Request $request)
    {
        $cust_details = [
            'newPhone' => $request->input('newPhone'),
            'gotra' => $request->input('gotra'),
            'pincode' => $request->input('pincode'),
            'city' => $request->input('city'),
            'state' => $request->input('state'),
            'house_no' => $request->input('house_no'),
            'area' => $request->input('area'),
            'landmark' => $request->input('landmark'),
            'latitude'  => $request->input('latitude'),
            'longitude' => $request->input('longitude'),
            'members' => json_encode($request->input('members')),
            'is_prashad' => $request->input('is_prashad'),
            'is_edited' => '1',
        ];

        if ($request->input('is_prashad') == 0) {
            $cust_details = array_merge($cust_details, [
                'pincode'   => null,
                'city'      => null,
                'state'     => null,
                'house_no'  => null,
                'area'      => null,
                'landmark'  => null,
                'latitude'  => null,
                'longitude' => null,
            ]);
        }
        $serviceOrder = Service_order::where('order_id', $order_id)->update($cust_details);
        if ($request->input('is_prashad') == 1) {
            $prashad_order = [
                'seller_id' => $request->input('seller_id'),
                'order_id' => $order_id,
                'warehouse_id' => $request->input('warehouse_id'),
                'service_id' => $request->input('service_id'),
                'user_id' =>   $request->input('user_id'),
                'product_id' => $request->input('product_id'),
                'type' => $request->input('type'),
                'payment_type' => $request->input('payment_type'),
                'booking_date' => $request->input('booking_date')
            ];
            $prashadOrder = Prashad_deliverys::create($prashad_order);
        }
        $stateList = States::orderBy('name', 'asc')->get();

        $sankalpData = Service_order::where('order_id', $order_id)->with(['customers', 'services', 'packages', 'leads'])->first();
        $UsersData = Service_order::where('type', 'vip')->with(['customers', 'vippoojas', 'packages', 'leads'])->first();
        $membersList = json_decode($sankalpData['members'], true);
        $formattedMembers = $membersList ? implode(', ', $membersList) : 'No members specified';

        // whatsapp
        $userInfo = \App\Models\User::where('id', ($sankalpData['customer_id'] ?? ""))->first();
        $service_name = \App\Models\Vippooja::where('id', ($sankalpData['service_id'] ?? ""))->where('is_anushthan', 1)->first();
        $bookingDetails = \App\Models\Service_order::where('service_id', ($sankalpData['service_id'] ?? ""))->where('type', 'anushthan')
            ->where('booking_date', ($sankalpData['booking_date'] ?? ""))
            ->where('customer_id', ($sankalpData['customer_id'] ?? ""))
            ->where('order_id', ($order_id ?? ""))
            ->first();

        $message_data = [
            'service_name' => $sankalpData['vippoojas']['name'],
            'member_names' => $formattedMembers,
            'gotra' => $request->input('gotra'),
            'prashad' => $sankalpData['is_prashad'] == 1 ? 'Your Prasad is being prepared and will be dispatched to your address within 7-8 days, only if you selected "Yes" for receiving Prasad.' : '',
            'type' => 'text-with-media',
            'attachment' =>  asset('/storage/app/public/pooja/vipthumbnail/' . $service_name->thumbnail),
            'booking_date' => date('d-m-Y', strtotime($sankalpData['booking_date'])),
            'puja_venue' => $service_name['pooja_venue'],
            'pooja' => 'VIP Pooja',
            'orderId' => $order_id,
            'customer_id' => ($sankalpData['customer_id'] ?? ""),
        ];
        $messages =  Helpers::whatsappMessage('vipanushthan', 'Sankalp Information', $message_data);
        // Mail Setup for Pooja Management Send to  User Email Id
        if ($userInfo && !empty($userInfo['email']) && filter_var($userInfo['email'], FILTER_VALIDATE_EMAIL)) {
            $data['type'] = 'pooja';
            $data['email'] = $userInfo['email'];
            $data['subject'] = 'Information given by you for puja';
            $data['htmlContent'] = \Illuminate\Support\Facades\View::make('admin-views.email.email-template.pooja-sankalp-template', compact('userInfo', 'service_name', 'bookingDetails', 'formattedMembers', 'request'))->render();

            Helpers::emailSendMessage($data);
        }
        Toastr::success(translate('sankalp_details_successfully_updated!!'));
        return  redirect()->back();
    }

    public function account_anushthan_pandit_details($order_id, Request $request)
    {
        $panditData = Service_order::where('order_id', $order_id)->with('vippoojas')->with('packages')->with('payments')->with('pandit')->with('product_leads')->first();
        return view(VIEW_FILE_NAMES['account_anushthan_pandit_details'], [
            'order' => $panditData,
            'refund_day_limit' => getWebConfig(name: 'refund_day_limit'),
            'current_date' => Carbon::now(),
        ]);
        Toastr::warning(translate('invalid_order'));
        return redirect()->route('');
    }

    public function account_anushthan_certificate($order_id)
    {
        $anushthanOrder = Service_order::where('order_id', $order_id)->first();
        if ($anushthanOrder) {
            return view(VIEW_FILE_NAMES['account_anushthan_certificate'], ['order' => $anushthanOrder,]);
        }
        Toastr::warning(translate('invalid_order'));
        return redirect()->route('');
    }
    // certificate download
    public function AnushthandownloadCertificate($id)
    {
        $anushthanOrder = Service_Order::find($id);
        if (!$anushthanOrder || !$anushthanOrder->pooja_certificate) {
            Toastr::warning(translate('Certificate not found.'));
        }
        $filePath = asset('assets/back-end/img/certificate/pooja/');
        if (!file_exists($filePath . $anushthanOrder->pooja_certificate)) {
            Toastr::warning(translate('File not found.'));
            return back();
        }
        $imageName = $anushthanOrder->pooja_certificate;
        return response()->download($filePath, $imageName);
    }
    public function generate_invoice_anushthan($id)
    {
        $companyPhone = getWebConfig(name: 'company_phone');
        $companyEmail = getWebConfig(name: 'company_email');
        $companyName = getWebConfig(name: 'company_name');
        $companyWebLogo = getWebConfig(name: 'company_web_logo');
        $details = Service_order::where('id', $id)->with('customers')->with('vippoojas')->with('leads')->with('packages')->with('payments')->with('product_leads.productsData')->first();
        // dd($details);
        // $mpdf_view = PdfView::make('web-views.order.invoice-service',compact('details'));
        $mpdf_view = \View::make(VIEW_FILE_NAMES['order_invoice_anushthan'], compact('details', 'companyPhone', 'companyEmail', 'companyName', 'companyWebLogo'));
        Helpers::gen_mpdf($mpdf_view, 'order_invoice_vip', $details['order_id']);
        // $this->generatePdf($mpdf_view, 'order_invoice_', );
    }

    public function account_anushthan_review($order_id)
    {
        $serviceOrder = Service_order::where('order_id', $order_id)->with('leads')->with('vippoojas')->with('packages')->with('payments')->with('pandit')->with('product_leads')->first();
        $serviceOrder['customer'] = User::where('phone', $serviceOrder['leads']['person_phone'])->first();
        $serviceOrder['pooja_pandit'] = Astrologer::where('is_pandit_pooja', 'like', '%' . $serviceOrder['service_id'] . '%')->get();
        $existingReview = ServiceReview::where('order_id', $order_id)->where('is_edited', 1)
            ->where('user_id', $serviceOrder['customer']->id)
            ->first();
        return view(VIEW_FILE_NAMES['account_anushthan_review'], [
            'order' => $serviceOrder,
            'refund_day_limit' => getWebConfig(name: 'refund_day_limit'),
            'current_date' => Carbon::now(),
            'existingReview' => $existingReview,
        ]);
        Toastr::warning(translate('invalid_order'));
        return redirect()->route('');
    }

    public function submit_anushthan_review($order_id, Request $request)
    {

        $validatedData = $request->validate([
            'order_id' => 'required|string',
            'user_id' => 'required|integer',
            'service_id' => 'required|integer',
            'service_type' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
            'youtube_link' => 'nullable|url',
        ]);
        ServiceReview::where('order_id', $request->input('order_id'))->update([
            'astro_id' => $request->input('astro_id'),
            'service_type' => $request->input('service_type'),
            'user_id' => $request->input('user_id'),
            'rating' => $request->input('rating'),
            'service_id' => $request->input('service_id'),
            'comment' => $request->input('comment'),
            'youtube_link' => $request->input('youtube_link'),
            'is_edited' => 1,
        ]);

        Toastr::success(translate('Review submitted successfully!!'));
        return redirect()->back();
    }

    //  ----------------------------------------CHADHAVA ORDER DETAILS--------------------------------------------------------
    public function account_chadhava_order_details($order_id, Request $request)
    {
        $chadhavaOrder = Chadhava_orders::where('order_id', $order_id)->with('leads')->with('chadhava')->with('payments')->with('pandit')->with('product_leads')->first();
        $chadhavaOrder['customer'] = User::where('phone', $chadhavaOrder['leads']['person_phone'])->first();
        $chadhavaOrder['pooja_pandit'] = Astrologer::where('is_pandit_pooja', 'like', '%' . $chadhavaOrder['service_id'] . '%')->get();
        return view(VIEW_FILE_NAMES['account_chadhava_order_details'], [
            'order' => $chadhavaOrder,
            'refund_day_limit' => getWebConfig(name: 'refund_day_limit'),
            'current_date' => Carbon::now(),
        ]);
        Toastr::warning(translate('invalid_order'));
        return redirect()->route('');
    }
    public function account_chadhava_order_track($order_id)
    {
        $chadhavaOrder = Chadhava_orders::where('order_id', $order_id)->first();
        if ($chadhavaOrder) {
            return view(VIEW_FILE_NAMES['account_chadhava_order_track'], ['order' => $chadhavaOrder,]);
        }
        Toastr::warning(translate('invalid_order'));
        return redirect()->route('');
    }
    public function account_chadhava_sankalp($order_id)
    {
        $chadhavaOrder = Chadhava_orders::where('order_id', $order_id)->with('customers')->with('leads')->with('payments')->first();
        // dd($chadhavaOrder);
        $stateList = States::orderBy('name', 'asc')->get();
        if ($chadhavaOrder) {
            return view(VIEW_FILE_NAMES['account_chadhava_sankalp'], ['order' => $chadhavaOrder, 'stateList' => $stateList]);
        }
        Toastr::warning(translate('invalid_order'));
        return redirect()->route('');
    }
    public function chadhava_sankalp_update($order_id, Request $request)
    {
        $cust_details = [
            'newPhone' => $request->input('newPhone'),
            'gotra' => $request->input('gotra'),
            'members' => $request->input('members'),
            'reason' => $request->input('reason'),
            'is_edited' => '1'
        ];
        $serviceOrder = Chadhava_orders::where('order_id', $order_id)->update($cust_details);
        $stateList = States::orderBy('name', 'asc')->get();

        $sankalpData = Chadhava_orders::where('order_id', $order_id)->with(['customers', 'chadhava', 'leads'])->first();
        $service_name = \App\Models\Chadhava::where('id', ($sankalpData['service_id'] ?? ""))->where('chadhava_type', 0)->first();
        $UsersData = Chadhava_orders::where('type', 'chadhava')->where('order_id', $order_id)->with(['customers', 'chadhava', 'packages', 'leads'])->first();

        // whatsapp
        $userInfo = \App\Models\User::where('id', ($UsersData['customer_id'] ?? ""))->first();
        $bookingDetails = \App\Models\Chadhava_orders::where('service_id', ($UsersData['service_id'] ?? ""))->where('type', 'chadhava')
            ->where('booking_date', ($UsersData['booking_date'] ?? ""))
            ->where('customer_id', ($UsersData['customer_id'] ?? ""))
            ->where('order_id', ($order_id ?? ""))
            ->first();

        $message_data = [
            'service_name' => $UsersData['chadhava']['name'],
            'member_names' => $UsersData['members'],
            'gotra' => $request->input('gotra'),
            'type' => 'text-with-media',
            'attachment' =>  asset('/storage/app/public/chadhava/thumbnail/' . $UsersData['chadhava']['thumbnail']),
            'booking_date' => date('d-m-Y', strtotime($UsersData['booking_date'])),
            'orderId' => $order_id,
            'prashad' => $UsersData['is_prashad'] == 1 ? 'Your Prasad is being prepared and will be dispatched to your address within 7-8 days, only if you selected "Yes" for receiving Prasad.' : '',
            'customer_id' => ($UsersData['customers']['id'] ?? ""),
        ];

        $messages =  Helpers::whatsappMessage('chadhava', 'Sankalp Information', $message_data);

        // Mail Setup for Pooja Management Send to  User Email Id
        if ($userInfo && !empty($userInfo['email']) && filter_var($userInfo['email'], FILTER_VALIDATE_EMAIL)) {
            $data['type'] = 'chadhava';
            $data['email'] = $userInfo['email'];
            $data['subject'] = 'Information given by you for chadhava';
            $data['htmlContent'] = \Illuminate\Support\Facades\View::make('admin-views.email.email-template.pooja-sankalp-template', compact('userInfo', 'UsersData', 'service_name', 'bookingDetails', 'request'))->render();
            Helpers::emailSendMessage($data);
        }

        Toastr::success(translate('sankalp_details_successfully_updated!!'));
        return  redirect()->back();
    }

    public function account_chadhava_pandit_details($order_id, Request $request)
    {
        $panditData = Chadhava_orders::where('order_id', $order_id)->with('chadhava')->with('payments')->with('pandit')->with('product_leads')->first();
        return view(VIEW_FILE_NAMES['account_chadhava_pandit_details'], [
            'order' => $panditData,
            'refund_day_limit' => getWebConfig(name: 'refund_day_limit'),
            'current_date' => Carbon::now(),
        ]);
        Toastr::warning(translate('invalid_order'));
        return redirect()->route('');
    }
    public function account_chadhava_certificate($order_id)
    {
        $chadhavaOrder = Chadhava_orders::where('order_id', $order_id)->first();
        if ($chadhavaOrder) {
            return view(VIEW_FILE_NAMES['account_chadhava_certificate'], ['order' => $chadhavaOrder,]);
        }
        Toastr::warning(translate('invalid_order'));
        return redirect()->route('');
    }
    // certificate download
    public function ChadhavadownloadCertificate($id)
    {
        $chadhavaOrder = Chadhava_orders::find($id);
        if (!$chadhavaOrder || !$chadhavaOrder->pooja_certificate) {
            Toastr::warning(translate('Certificate not found.'));
        }
        $filePath = asset('assets/back-end/img/certificate/pooja/');
        if (!file_exists($filePath . $chadhavaOrder->pooja_certificate)) {
            Toastr::warning(translate('File not found.'));
            return back();
        }
        $imageName = $chadhavaOrder->pooja_certificate;
        return response()->download($filePath, $imageName);
    }
    public function generate_invoice_chadhava($id)
    {
        $companyPhone = getWebConfig(name: 'company_phone');
        $companyEmail = getWebConfig(name: 'company_email');
        $companyName = getWebConfig(name: 'company_name');
        $companyWebLogo = getWebConfig(name: 'company_web_logo');
        $details = Chadhava_orders::where('id', $id)->with('customers')->with('chadhava')->with('leads')->with('packages')->with('payments')->with('product_leads.productsData')->first();
        // dd($details);
        // $mpdf_view = PdfView::make('web-views.order.invoice-service',compact('details'));
        $mpdf_view = \View::make(VIEW_FILE_NAMES['order_invoice_chadhava'], compact('details', 'companyPhone', 'companyEmail', 'companyName', 'companyWebLogo'));
        Helpers::gen_mpdf($mpdf_view, 'order_invoice_chadhava', $details['order_id']);
        // $this->generatePdf($mpdf_view, 'order_invoice_', );
    }

    public function account_chadhava_review($order_id)
    {
        $serviceOrder = Chadhava_orders::where('order_id', $order_id)->with('leads')->with('chadhava')->with('packages')->with('payments')->with('pandit')->with('product_leads')->first();
        $serviceOrder['customer'] = User::where('phone', $serviceOrder['leads']['person_phone'])->first();
        $serviceOrder['pooja_pandit'] = Astrologer::where('is_pandit_pooja', 'like', '%' . $serviceOrder['service_id'] . '%')->get();
        $existingReview = ServiceReview::where('order_id', $order_id)->where('is_edited', 1)
            ->where('user_id', $serviceOrder['customer']->id)
            ->first();
        return view(VIEW_FILE_NAMES['account_chadhava_review'], [
            'order' => $serviceOrder,
            'refund_day_limit' => getWebConfig(name: 'refund_day_limit'),
            'current_date' => Carbon::now(),
            'existingReview' => $existingReview,
        ]);
        Toastr::warning(translate('invalid_order'));
        return redirect()->route('account-order');
    }

    public function submit_chadhava_review($order_id, Request $request)
    {

        $validatedData = $request->validate([
            'order_id' => 'required|string',
            'user_id' => 'required|integer',
            'service_id' => 'required|integer',
            'service_type' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);
        ServiceReview::where('order_id', $request->input('order_id'))->update([
            'astro_id' => $request->input('astro_id'),
            'service_type' => $request->input('service_type'),
            'user_id' => $request->input('user_id'),
            'rating' => $request->input('rating'),
            'service_id' => $request->input('service_id'),
            'comment' => $request->input('comment'),
            'is_edited' => 1,
        ]);
        // dd($serviceReview);
        Toastr::success(translate('Review submitted successfully!!'));
        return redirect()->back();
    }

    public function saved_paid_kundali()
    {
        $kundalis = BirthJournalKundali::where('user_id', auth('customer')->id())
            ->whereHas('birthJournal', function ($query) {
                $query->where('name', 'kundali');
            })
            ->with(['birthJournal' => function ($query) {
                $query->where('name', 'kundali');
            }])->orderBy('id', 'DESC')->paginate(10);
        $types = 'kundali';
        return view('web-views.users-profile.saved-paid-kundali', compact('kundalis', 'types'));
    }


    public function saved_paid_kundali_milan()
    {
        $kundalis = BirthJournalKundali::where('user_id', auth('customer')->id())
            ->whereHas('birthJournal', function ($query) {
                $query->where('name', 'kundali_milan');
            })
            ->with(['birthJournal' => function ($query) {
                $query->where('name', 'kundali_milan');
            }])->orderBy('id', 'DESC')->paginate(10);
        $types = "kundali_milan";
        return view('web-views.users-profile.saved-paid-kundali', compact('kundalis', 'types'));
    }

    public function saved_paid_kundali_milan_show(Request $request, $id)
    {
        $kundalis = BirthJournalKundali::where('user_id', auth('customer')->id())->with(['country', 'country_female'])->where('id', $id)->first();
        return view('web-views.users-profile.paid-kundali-milan-details', compact('kundalis'));
    }
    public function GenerateInvoice($id)
    {
        $companyPhone = getWebConfig(name: 'company_phone');
        $companyEmail = getWebConfig(name: 'company_email');
        $companyName = getWebConfig(name: 'company_name');
        $companyWebLogo = getWebConfig(name: 'company_web_logo');
        $details = BirthJournalKundali::where(['id' => $id])->with(['country', 'birthJournal', 'userData', 'astrologer'])->first();
        // dd($details);
        $mpdf_view = \Illuminate\Support\Facades\View::make('admin-views.birth_journal.order_invoice', compact('details', 'companyPhone', 'companyEmail', 'companyName', 'companyWebLogo'));
        $this->generatePdf($mpdf_view, 'order_invoice_', $details['order_id']);
    }

    public function feedback_list()
    {
        if (auth('customer')->check()) {
            $feedback = UserFeedback::where(['user_id' => auth('customer')->id()])->first();
            return view('web-views.users-profile.feedback', compact('feedback'));
        } else {
            Toastr::info(translate('unable_to_find_user'));
            return redirect()->back();
        }
    }

    public function feedback_store(Request $request)
    {
        if (auth('customer')->check()) {
            $feedbackStore = UserFeedback::create([
                'user_id' => auth('customer')->id(),
                'message' => $request->message,
                'is_edited' => 1
            ]);
            if ($feedbackStore) {
                Toastr::success(translate('Your_feedback_submitted'));
                return redirect()->back();
            }
            Toastr::error(translate('unable_to_store_feedback'));
            return redirect()->back();
        } else {
            Toastr::info(translate('unable_to_find_user'));
            return redirect()->back();
        }
    }

    public function feedback_update(Request $request)
    {
        if (auth('customer')->check()) {
            $feedbackUpdate = UserFeedback::where('user_id', auth('customer')->id())->update([
                'message' => $request->message,
                'status' => 0,
                'is_edited' => 1
            ]);
            if ($feedbackUpdate) {
                Toastr::success(translate('Your_feedback_submitted'));
                return redirect()->back();
            }
            Toastr::error(translate('unable_to_update_feedback'));
            return redirect()->back();
        } else {
            Toastr::info(translate('unable_to_find_user'));
            return redirect()->back();
        }
    }

    public function MahakalQrCodes()
    {
        $url = route("mahakal-qr-scan");
        $builder = new Builder(
            data: $url,
            writer: new PngWriter(),
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: \Endroid\QrCode\ErrorCorrectionLevel::High,
            size: 300,
            margin: 10,
            logoPath: public_path('assets/front-end/img/logo-png.png'),
            logoResizeToWidth: 90
        );
        $result = $builder->build();

        // Save to file
        $folder = storage_path('app/public/qrcodes');
        if (!File::exists($folder)) {
            File::makeDirectory($folder, 0777, true);
        }
        $filePath = $folder . "/mahakal-qr-code.png";
        $result->saveToFile($filePath);
        $webPath = asset("storage/app/public/qrcodes/mahakal-qr-code.png");

        echo "<img src=" . $webPath . ">";
    }

    public function MahakalQrScan(Request $request)
    {
        $userAgent = $request->header('User-Agent');

        if (stripos($userAgent, 'Android') !== false) {
            // Android
            return redirect()->to('https://play.google.com/store/apps/details?id=manal.mahakal.com');
        } elseif (stripos($userAgent, 'iPhone') !== false || stripos($userAgent, 'iPad') !== false) {
            // iOS
            return redirect()->to('https://apps.apple.com/in/app/mahakal-com/id6475806433');
        } elseif (stripos($userAgent, 'Mobile') !== false) {
            return redirect()->to('https://play.google.com/store/apps/details?id=manal.mahakal.com');
            // Mobile
        } elseif (stripos($userAgent, 'Tablet') !== false) {
            // Tablet
            return redirect()->to('https://apps.apple.com/in/app/mahakal-com/id6475806433');
        } else {
            // Desktop
            return redirect()->to('https://play.google.com/store/apps/details?id=manal.mahakal.com');
        }
    }

    public function user_account_delete()
    {
        return view('web-views.users-profile.account-delete-info');
    }

    public function verifyCodeEventPass(Request $request, $id, $member_number)
    {
        if (!auth('event')->check()) {
            return redirect()->route('vendor.auth.login')->with('error', 'Please login to access this page.');
        }
        $orderId = \App\Models\EventOrder::where('id', $id)->whereIn('transaction_status', ['1', '2', '3'])->with('orderitem', 'eventid')->first();
        $searchValue = $request['search'];
        $eventQuerys = \App\Models\EventOrder::with(['orderitem', 'eventid', 'userdata'])
            ->when($searchValue, function ($query) use ($searchValue) {
                $query->where('order_no', 'like', "%$searchValue%");
                $query->orWhere(function ($query) use ($searchValue) {
                    $query->whereHas('userdata', function ($q) use ($searchValue) {
                        $q->where('name', 'like', "%$searchValue%")
                            ->orWhere('phone', 'like', "%$searchValue%")
                            ->orWhere('email', 'like', "%$searchValue%");
                    });
                });
            })->where('event_id', $orderId['event_id'])->where('transaction_status', 1)->where('status', 1)->where('venue_id', $orderId['venue_id']);
        $getOrderList = $eventQuerys->paginate(getWebConfig(name: 'pagination_limit'));
        return view(\App\Enums\ViewPaths\AllPaths\EventPath::QRTODAYINFORMATION[VIEW], compact('getOrderList', 'orderId', 'member_number'));
    }


    public function account_order_Darshan(Request $request)
    {
        if (!auth('customer')->check()) {
            Toastr::error('Please Login');
            return back();
        }
        $darshanOrders = \App\Models\DarshanOrder::where('user_id', auth('customer')->id())->whereIn('status', [1, 2, 3])->with(['userData', 'Temple', 'Members'])->orderBy('id', 'DESC')->paginate(10);
        return view(VIEW_FILE_NAMES['account_orders_darshan'], compact('darshanOrders'));
    }

    public function DarshanOrderDetails(Request $request)
    {
        $darshanOrders = \App\Models\DarshanOrder::where('id', $request['id'])->where('user_id', auth('customer')->id())->with(['userData', 'Temple', 'Members'])->first();
        return view(VIEW_FILE_NAMES['darshan_orders_details'], compact('darshanOrders'));
    }
    public function DarshanAddReviews(Request $request)
    {
        if (!auth('customer')->check()) {
            Toastr::error('Please Login');
            return back();
        }
        $getReview = \App\Models\TempleReview::where('temple_id', ($request['temple_id'] ?? ''))->where('user_id', auth('customer')->id())->where('order_id', $request['order_id'])->first();
        if (!$getReview || $getReview['is_edited'] == 0) {
            if (!$getReview) {
                $review = new \App\Models\TempleReview();
            } else {
                $review = \App\Models\TempleReview::find($getReview['id']);
            }
            $review->order_id = $request->order_id;
            $review->user_id = auth('customer')->id();
            $review->temple_id = $request->temple_id;
            $review->star = $request->rating;
            $review->comment = $request->comment;
            $review->status = 1;
            $review->is_edited = 1;
            $review->save();
            Toastr::success('Comment added successfully');
        } else {
            Toastr::error('Comment has already been added');
        }
        return back();
    }


    public function generateQrCode(Request $request)
    {
        echo $url = $request->url;
        echo '<hr>';
        $builder = new Builder(
            data: $url,
            writer: new PngWriter(),
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: \Endroid\QrCode\ErrorCorrectionLevel::High,
            size: 300,
            margin: 10,
            logoPath: public_path('assets/front-end/img/logo-png.png'),
            logoResizeToWidth: 90
        );
        $result = $builder->build();

        // Save to file
        $folder = storage_path('app/public/qrcodes');
        if (!File::exists($folder)) {
            File::makeDirectory($folder, 0777, true);
        }
        $filePath = $folder . "/mahakal-qr-code.png";
        $result->saveToFile($filePath);
        $webPath = asset("storage/app/public/qrcodes/mahakal-qr-code.png");

        echo "<img src=" . $webPath . ">";
    }
}
