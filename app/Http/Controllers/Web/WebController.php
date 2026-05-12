<?php

namespace App\Http\Controllers\Web;

use App\Contracts\Repositories\EventsRepositoryInterface;
use App\Models\EventInterest;
use App\Contracts\Repositories\TemplesRepositoryInterface;
use App\Models\EventLeads;
use App\Models\EventOrder;
use App\Models\Events;
use App\Models\EventsReview;
use App\Models\Admin;
use App\Utils\Helpers;
use App\Utils\ApiHelper;
use App\Events\DigitalProductOtpVerificationMailEvent;
use App\Http\Controllers\Controller;
use App\Models\OfflinePaymentMethod;
use App\Models\ShippingAddress;
use App\Models\ShippingMethod;
use App\Models\ShippingType;
use App\Models\Shop;
use App\Models\Subscription;
use App\Models\OrderDetail;
use App\Models\TempleDarshanLead;
use App\Models\Purohit;
use App\Models\Review;
use App\Models\Brand;
use App\Models\User;
use App\Models\BusinessSetting;
use App\Models\Calculator;
use App\Models\Cart;
use App\Models\CartShipping;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Country;
use App\Models\Currency;
use App\Models\DeliveryZipCode;
use App\Models\DigitalProductOtpVerification;
use App\Models\FlashDeal;
use App\Models\FlashDealProduct;
use App\Models\MasikRashi;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductCompare;
use App\Models\Rashi;
use App\Models\Seller;
use App\Models\DonateCategory;
use App\Models\DonateAds;
use App\Models\DonateLeads;
use App\Models\DonateTrust;
use App\Models\Setting;
use App\Models\UserKundali;
use App\Models\UserKundaliMilan;
use App\Models\VarshikRashi;
use App\Models\EventOrderItems;
use App\Models\Wishlist;
use App\Models\DonateAllTransaction;
use App\Models\HotelReview;
use App\Models\Hotels;
use App\Models\Restaurant;
use App\Models\RestaurantReview;
use App\Models\Temple;
use App\Models\TempleCategory;
use App\Models\TempleReview;
use App\Models\Cities;
use App\Models\CitiesReview;
use App\Traits\CommonTrait;
use App\Traits\SmsGateway;
use App\Utils\CartManager;
use App\Utils\Convert;
use App\Utils\CustomerManager;
use App\Utils\OrderManager;
use App\Utils\ProductManager;
use App\Utils\SMS_module;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use function App\Utils\payment_gateways;
use App\Library\Receiver;
use App\Traits\Payment;
use App\Library\Payer;
use App\Library\Payment as PaymentInfo;
use App\Models\AccountNumberVerified;
use Illuminate\Support\Facades\Validator;
use App\Models\Astrologer\Astrologer;
use App\Models\Astrologer\Skills;
use App\Models\AstrologerCategory;
use App\Models\DarshanOrder;
use App\Models\DarshanOrderMembers;
use App\Models\RamShalaka;
use App\Models\SelfCancellationPolicy;
use App\Models\SelfDrivingCabs;
use App\Models\SelfDrivingPolicy;
use App\Models\SelfVehicleLeads;
use App\Models\TourVehicleCetagory;
use App\Models\TrustPujaOrder;
use App\Models\UserAadhaarKyc;
use App\Models\UserPanCardVerified;
use Illuminate\Support\Facades\Hash;


class WebController extends Controller
{
    use CommonTrait;
    use SmsGateway;

    public function __construct(
        private OrderDetail $order_details,
        private Product $product,
        private Wishlist $wishlist,
        private Order $order,
        private Category $category,
        private Brand $brand,
        private Seller $seller,
        private ProductCompare $compare,
        private readonly TemplesRepositoryInterface $templeRepo,
        private readonly EventsRepositoryInterface $eventsRepository,

    ) {}

    public function maintenance_mode()
    {
        $maintenance_mode = Helpers::get_business_settings('maintenance_mode') ?? 0;
        if ($maintenance_mode) {
            return view(VIEW_FILE_NAMES['maintenance_mode']);
        }
        return redirect()->route('home');
    }

    public function flash_deals($id)
    {
        $deal = FlashDeal::with(['products.product.reviews', 'products.product' => function ($query) {
            $query->active();
        }])
            ->where(['id' => $id, 'status' => 1])
            ->whereDate('start_date', '<=', date('Y-m-d'))
            ->whereDate('end_date', '>=', date('Y-m-d'))
            ->first();

        $discountPrice = FlashDealProduct::with(['product'])->whereHas('product', function ($query) {
            $query->active();
        })->get()->map(function ($data) {
            return [
                'discount' => $data->discount,
                'sellPrice' => isset($data->product->unit_price) ? $data->product->unit_price : 0,
                'discountedPrice' => isset($data->product->unit_price) ? $data->product->unit_price - $data->discount : 0,

            ];
        })->toArray();


        if (isset($deal)) {
            return view(VIEW_FILE_NAMES['flash_deals'], compact('deal', 'discountPrice'));
        }
        Toastr::warning(translate('not_found'));
        return back();
    }

    public function search_shop(Request $request)
    {
        $key = explode(' ', $request['shop_name']);
        $sellers = Shop::where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('name', 'like', "%{$value}%");
            }
        })->whereHas('seller', function ($query) {
            return $query->where(['status' => 'approved']);
        })->paginate(30);
        return view(VIEW_FILE_NAMES['all_stores_page'], compact('sellers'));
    }

    public function all_categories()
    {
        $categories = Category::all();
        return view('web-views.products.categories', compact('categories'));
    }

    public function categories_by_category($id)
    {
        $category = Category::with(['childes.childes'])->where('id', $id)->first();
        return response()->json([
            'view' => view('web-views.partials._category-list-ajax', compact('category'))->render(),
        ]);
    }

    public function all_brands(Request $request)
    {
        $brand_status = BusinessSetting::where(['type' => 'product_brand'])->value('value');
        session()->put('product_brand', $brand_status);
        if ($brand_status == 1) {
            $order_by = $request->order_by ?? 'desc';
            $brands = Brand::active()->withCount('brandProducts')->orderBy('name', $order_by)
                ->when($request->has('search'), function ($query) use ($request) {
                    $query->where('name', 'LIKE', '%' . $request->search . '%');
                })->latest()->paginate(15)->appends(['order_by' => $order_by, 'search' => $request->search]);

            return view(VIEW_FILE_NAMES['all_brands'], compact('brands'));
        } else {
            return redirect()->route('home');
        }
    }

    public function all_sellers(Request $request)
    {
        $businessMode = getWebConfig(name: 'business_mode');
        if (isset($businessMode) && $businessMode == 'single') {
            Toastr::warning(translate('access_denied') . ' !!');
            return back();
        }
        $sellers = Shop::active()->with(['seller.product'])
            ->withCount(['products' => function ($query) {
                $query->active();
            }])
            ->when(isset($request['shop_name']), function ($query) use ($request) {
                $key = explode(' ', $request['shop_name']);
                return $query->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('name', 'like', "%{$value}%");
                    }
                });
            })->get();
        //dd($sellers);

        if (theme_root_path() == 'theme_fashion') {
            //
            if ($request->has('order_by') && ($request->order_by == 'rating-high-to-low' || $request->order_by == 'rating-low-to-high')) {
                if ($request->order_by == 'rating-high-to-low') {
                    $sellers = $sellers->sortByDesc('average_rating');
                } else {
                    $sellers = $sellers->sortBy('rating_count');
                }
            }
        }

        $inhouseProducts = Product::active()->with(['reviews', 'rating'])->withCount('reviews')->where(['added_by' => 'admin'])->get();
        $inhouseProductCount = $inhouseProducts->count();

        $inhouseVacation = getWebConfig(name: 'vacation_add');
        $inhouseShop = new Shop([
            'id' => 0,
            'seller_id' => 0,
            'name' => getWebConfig(name: 'company_name'),
            'slug' => Str::slug(getWebConfig(name: 'company_name')),
            'address' => getWebConfig(name: 'shop_address'),
            'contact' => getWebConfig(name: 'company_phone'),
            'image' => getWebConfig(name: 'company_fav_icon'),
            'bottom_banner' => getWebConfig(name: 'bottom_banner'),
            'offer_banner' => getWebConfig(name: 'offer_banner'),
            'vacation_start_date' => $inhouseVacation['vacation_start_date'] ?? null,
            'vacation_end_date' => $inhouseVacation['vacation_end_date'] ?? null,
            'vacation_note' => $inhouseVacation['vacation_note'],
            'vacation_status' => $inhouseVacation['status'] ?? false,
            'temporary_close' => getWebConfig(name: 'temporary_close') ? getWebConfig(name: 'temporary_close')['status'] : 0,
            'banner' => getWebConfig(name: 'shop_banner'),
            'created_at' => Admin::where(['id' => 1])->first()->created_at,
        ]);

        if (!(isset($request['shop_name']) && !str_contains(strtolower(getWebConfig(name: 'company_name')), strtolower($request['shop_name'])))) {
            $sellers = $sellers->prepend($inhouseShop);
        }

        $sellers?->map(function ($seller) use ($inhouseProducts, $inhouseProductCount) {
            if ($seller['id'] != 0) {
                $productIds = Product::active()->where(['added_by' => 'seller'])
                    ->where('user_id', $seller['id'])->pluck('id')->toArray();
                $vendorReviewData = Review::active()->whereIn('product_id', $productIds);
                $seller['average_rating'] = $vendorReviewData->avg('rating');
                $seller['review_count'] = $vendorReviewData->count();
                $seller['total_rating'] = $vendorReviewData->count();

                $vendorRattingStatusPositive = 0;
                foreach ($vendorReviewData->pluck('rating') as $singleRating) {
                    ($singleRating >= 4 ? ($vendorRattingStatusPositive++) : '');
                }

                $seller['positive_review'] = $seller['review_count'] != 0 ? ($vendorRattingStatusPositive * 100) / $seller['review_count'] : 0;
            } else {
                $inhouseReviewData = Review::active()->whereIn('product_id', $inhouseProducts->pluck('id'));
                $inhouseRattingStatusPositive = 0;
                foreach ($inhouseReviewData->pluck('rating') as $singleRating) {
                    ($singleRating >= 4 ? ($inhouseRattingStatusPositive++) : '');
                }

                $seller['id'] = 0;
                $seller['products_count'] = $inhouseProductCount;
                $seller['total_rating'] = $inhouseReviewData->count();
                $seller['review_count'] = $inhouseReviewData->count();
                $seller['average_rating'] = $inhouseReviewData->avg('rating');
                $seller['positive_review'] = $inhouseReviewData->count() != 0 ? ($inhouseRattingStatusPositive * 100) / $inhouseReviewData->count() : 0;
            }
        });

        if ($request->has('order_by')) {
            if ($request['order_by'] == 'asc') {
                $sellers = $sellers->sortBy('name');
            } else if ($request['order_by'] == 'desc') {
                $sellers = $sellers->sortByDesc('name');
            } else if ($request['order_by'] == 'highest-products') {
                $sellers = $sellers->sortByDesc('products_count');
            } else if ($request['order_by'] == 'lowest-products') {
                $sellers = $sellers->sortBy('products_count');
            } else if ($request['order_by'] == 'rating-high-to-low') {
                $sellers = $sellers->sortByDesc('average_rating');
            } else if ($request['order_by'] == 'rating-low-to-high') {
                $sellers = $sellers->sortBy('average_rating');
            };
        }

        return view(VIEW_FILE_NAMES['all_stores_page'], [
            'sellers' => $sellers->paginate(12),
            'order_by' => $request['order_by'],
        ]);
    }

    public function seller_profile($id)
    {
        $seller_info = Seller::find($id);
        return view('web-views.seller-profile', compact('seller_info'));
    }

    public function searched_products(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ], [
            'name.required' => 'Product name is required!',
        ]);

        $result = ProductManager::search_products_web($request['name'], $request['category_id'] ?? 'all');
        $products = $result['products'];
        if ($products == null) {
            $result = ProductManager::translated_product_search_web($request['name'], $request['category_id'] ?? 'all');
            $products = $result['products'];
        }
        $sellers = Shop::where(function ($q) use ($request) {
            $q->orWhere('name', 'like', "%{$request['name']}%");
        })->whereHas('seller', function ($query) {
            return $query->where(['status' => 'approved']);
        })->with('products', function ($query) {
            return $query->active()->where('added_by', 'seller');
        })->get();

        $product_ids = [];
        foreach ($sellers as $seller) {
            if (isset($seller->product) && $seller->product->count() > 0) {
                $ids = $seller->product->pluck('id');
                array_push($product_ids, ...$ids);
            }
        }

        $inhouse_product = [];
        $company_name = Helpers::get_business_settings('company_name');

        if (strpos($request['name'], $company_name) !== false) {
            $ids = Product::active()->Where('added_by', 'admin')->pluck('id');
            array_push($product_ids, ...$ids);
        }
        $seller_products = Product::active()->withCount('reviews')->whereIn('id', $product_ids)->get();
        // tour
        $tour_Visits = \App\Models\TourVisits::select('tour_name as name', DB::raw("'tour' as type"))->where(function ($q) use ($request) {
            $q->orWhere('tour_name', 'like', "%{$request['name']}%")
                ->orWhereHas('translations', function ($query) use ($request) {
                    $query->whereIn('key', ['tour_name'])
                        ->where('value', 'like', "%{$request['name']}%");
                });
        })->where('status', 1)
            ->where(function ($query) {
                $query->whereIn('use_date', [0, 2, 3, 4])->orWhere(function ($query) {
                    $query->where('use_date', 1)->whereIn('customized_type', ['', '0'])
                        ->whereNotNull('startandend_date')
                        ->whereRaw('? < STR_TO_DATE(SUBSTRING_INDEX(startandend_date, " - ", 1), "%Y-%m-%d")', [date('Y-m-d')]);
                });
            })->get();
        // darshan
        $Temple_darshan = \App\Models\Temple::select('name', DB::raw("'darshan' as type"))->where(function ($q) use ($request) {
            $q->orWhere('name', 'like', "%{$request['name']}%")
                ->orWhereHas('translations', function ($query) use ($request) {
                    $query->whereIn('key', ['name'])
                        ->where('value', 'like', "%{$request['name']}%");
                });
        })->where('status', 1)->get();
        $products = array_merge((array) $products, (array) $tour_Visits->toArray());
        $products = array_merge((array) $products, (array) $Temple_darshan->toArray());
        // dd($products);
        return response()->json([
            'result' => view(VIEW_FILE_NAMES['product_search_result'], compact('products', 'seller_products'))->render(),
            'seller_products' => $seller_products->count(),
        ]);
    }

    // global search for theme fashion compare list
    public function searched_products_for_compare_list(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ], [
            'name.required' => 'Product name is required!',
        ]);
        $compare_id = $request['compare_id'];
        $result = ProductManager::search_products_web($request['name']);
        $products = $result['products'];
        if ($products == null) {
            $result = ProductManager::translated_product_search_web($request['name']);
            $products = $result['products'];
        }
        return response()->json([
            'result' => view(VIEW_FILE_NAMES['product_search_result_for_compare_list'], compact('products', 'compare_id'))->render(),
        ]);
    }

    public function checkout_details(Request $request)
    {
        if (
            (!auth('customer')->check() || Cart::where(['customer_id' => auth('customer')->id()])->count() < 1)
            && (!getWebConfig(name: 'guest_checkout') || !session()->has('guest_id') || !session('guest_id'))
        ) {
            Toastr::error(translate('invalid_access'));
            return redirect('customer/auth/login');
        }

        $response = self::checkValidationForCheckoutPages($request);
        if ($response['status'] == 0) {
            foreach ($response['message'] as $message) {
                Toastr::error($message);
            }
            return isset($response['redirect']) ? redirect($response['redirect']) : redirect('customer/auth/login');
        }

        $countryRestrictStatus = getWebConfig(name: 'delivery_country_restriction');
        $zipRestrictStatus = getWebConfig(name: 'delivery_zip_code_area_restriction');
        $countries = $countryRestrictStatus ? $this->get_delivery_country_array() : COUNTRIES;
        $zipCodes = $zipRestrictStatus ? DeliveryZipCode::all() : 0;
        $billingInputByCustomer = getWebConfig(name: 'billing_input_by_customer');
        $defaultLocation = getWebConfig(name: 'default_location');

        $user = Helpers::get_customer($request);
        $shippingAddresses = ShippingAddress::where([
            'customer_id' => $user == 'offline' ? session('guest_id') : auth('customer')->id(),
            'is_guest' => $user == 'offline' ? 1 : '0',
        ])->get();

        $countriesName = [];
        $countriesCode = [];
        foreach ($countries as $country) {
            $countriesName[] = $country['name'];
            $countriesCode[] = $country['code'];
        }

        return view(VIEW_FILE_NAMES['order_shipping'], [
            'physical_product_view' => $response['physical_product_view'],
            'zip_codes' => $zipCodes,
            'country_restrict_status' => $countryRestrictStatus,
            'zip_restrict_status' => $zipRestrictStatus,
            'countries' => $countries,
            'countriesName' => $countriesName,
            'countriesCode' => $countriesCode,
            'billing_input_by_customer' => $billingInputByCustomer,
            'default_location' => $defaultLocation,
            'shipping_addresses' => $shippingAddresses,
            'billing_addresses' => $shippingAddresses
        ]);
    }

    public function checkout_payment(Request $request)
    {
        $response = self::checkValidationForCheckoutPages($request);
        if ($response['status'] == 0) {
            foreach ($response['message'] as $message) {
                Toastr::error($message);
            }
            return $response['redirect'] ? redirect($response['redirect']) : redirect('/');
        }

        $cartItemGroupIDs = CartManager::get_cart_group_ids();
        $cartGroupList = Cart::whereIn('cart_group_id', $cartItemGroupIDs)->get()->groupBy('cart_group_id');
        $isPhysicalProductExistArray = [];
        foreach ($cartGroupList as $groupId => $cartGroup) {
            $isPhysicalProductExist = false;
            foreach ($cartGroup as $cart) {
                if ($cart->product_type == 'physical') {
                    $isPhysicalProductExist = true;
                }
            }
            $isPhysicalProductExistArray[$groupId] = $isPhysicalProductExist;
        }
        $cashOnDeliveryBtnShow = !in_array(false, $isPhysicalProductExistArray);

        $order = Order::find(session('order_id'));
        $couponDiscount = session()->has('coupon_discount') ? session('coupon_discount') : 0;
        $orderWiseShippingDiscount = CartManager::order_wise_shipping_discount();
        $getShippingCostSavedForFreeDelivery = CartManager::get_shipping_cost_saved_for_free_delivery();
        $amount = CartManager::cart_grand_total() - $couponDiscount - $orderWiseShippingDiscount - $getShippingCostSavedForFreeDelivery;
        $inr = Currency::where(['symbol' => '₹'])->first();
        $usd = Currency::where(['code' => 'USD'])->first();
        $myr = Currency::where(['code' => 'MYR'])->first();

        $offlinePaymentMethods = OfflinePaymentMethod::where('status', 1)->get();
        $paymentPublishedStatus = config('get_payment_publish_status');
        $paymentGatewayPublishedStatus = isset($paymentPublishedStatus[0]['is_published']) ? $paymentPublishedStatus[0]['is_published'] : 0;

        if (session()->has('address_id') && session()->has('billing_address_id')) {
            return view(VIEW_FILE_NAMES['payment_details'], [
                'cashOnDeliveryBtnShow' => $cashOnDeliveryBtnShow,
                'order' => $order,
                'cash_on_delivery' => getWebConfig(name: 'cash_on_delivery'),
                'digital_payment' => getWebConfig(name: 'digital_payment'),
                'wallet_status' => getWebConfig(name: 'wallet_status'),
                'offline_payment' => getWebConfig(name: 'offline_payment'),
                'coupon_discount' => $couponDiscount,
                'amount' => $amount,
                'inr' => $inr,
                'usd' => $usd,
                'myr' => $myr,
                'payment_gateway_published_status' => $paymentGatewayPublishedStatus,
                'payment_gateways_list' => payment_gateways(),
                'offline_payment_methods' => $offlinePaymentMethods
            ]);
        }

        Toastr::error(translate('incomplete_info'));
        return back();
    }

    public function checkout_complete(Request $request)
    {
        if ($request->payment_method != 'cash_on_delivery') {
            return back()->with('error', 'Something went wrong!');
        }
        $unique_id = OrderManager::gen_unique_id();
        $order_ids = [];
        $cart_group_ids = CartManager::get_cart_group_ids();
        $carts = Cart::whereIn('cart_group_id', $cart_group_ids)->get();

        $product_stock = CartManager::product_stock_check($carts);
        if (!$product_stock) {
            Toastr::error(translate('the_following_items_in_your_cart_are_currently_out_of_stock'));
            return redirect()->route('shop-cart');
        }

        $verifyStatus = OrderManager::minimum_order_amount_verify($request);
        if ($verifyStatus['status'] == 0) {
            Toastr::info(translate('check_minimum_order_amount_requirement'));
            return redirect()->route('shop-cart');
        }

        $physical_product = false;
        foreach ($carts as $cart) {
            if ($cart->product_type == 'physical') {
                $physical_product = true;
            }
        }

        if ($physical_product) {
            foreach ($cart_group_ids as $group_id) {
                $data = [
                    'payment_method' => 'cash_on_delivery',
                    'order_status' => 'pending',
                    'payment_status' => 'unpaid',
                    'transaction_ref' => '',
                    'order_group_id' => $unique_id,
                    'cart_group_id' => $group_id
                ];
                $order_id = OrderManager::generate_order($data);
                array_push($order_ids, $order_id);
            }

            $orderId = \App\Models\Order::latest()->value('id');
            $latestOrder = \App\Models\Order::latest()->first();
            $customerId = $latestOrder->customer_id ?? null;

            if (!$customerId) {
                Toastr::error('Customer not found!');
                return redirect(url('/'));
            }

            $userInfo = \App\Models\User::find($customerId);
            $orders = \App\Models\Order::where('customer_id', $customerId)
                ->where('created_at', '>=', now()->subMinutes(5))
                ->latest()
                ->get();

            foreach ($orders as $order) {
                $orderDetails = \App\Models\OrderDetail::where('order_id', $order->id)->get();
                $productIds = $orderDetails->pluck('product_id')->toArray();
                $productNames = \App\Models\Product::whereIn('id', $productIds)->pluck('name')->toArray();
                $sellerIds = $orderDetails->pluck('seller_id')->unique();
                $sellers = Seller::whereIn('id', $sellerIds)->get();
                $shops = Shop::whereIn('seller_id', $sellerIds)->pluck('name', 'seller_id');

                if ($userInfo && !empty($userInfo['email']) && filter_var($userInfo['email'], FILTER_VALIDATE_EMAIL)) {
                    $data['type'] = 'ecommerece';
                    $data['email'] = $userInfo['email'];
                    $data['subject'] = 'Confirmation of Your Order #' . $order->id;
                    $data['htmlContent'] = \Illuminate\Support\Facades\View::make(
                        'admin-views.email.email-template.ecom-template',
                        compact('userInfo', 'order', 'orderDetails', 'productNames', 'shops', 'sellers')
                    )->render();

                    Helpers::emailSendMessage($data);
                }

                $message_data = [
                    'product_name' => implode(', ', $productNames),
                    'orderId' =>  $order->id,
                    'order_amount' => webCurrencyConverter(amount: (float)$order->order_amount ?? 0),
                    'customer_id' => ($order->customer_id ?? ""),
                ];

                $messages =  Helpers::whatsappMessage('ecom', 'Order placed', $message_data);
            }
            CartManager::cart_clean();

            return view(VIEW_FILE_NAMES['order_complete'], compact('order_ids'));
        }

        return back()->with('error', 'Something went wrong!');
    }

    public function offline_payment_checkout_complete(Request $request)
    {
        if ($request->payment_method != 'offline_payment') {
            return back()->with('error', 'Something went wrong!');
        }
        $unique_id = OrderManager::gen_unique_id();
        $order_ids = [];
        $cart_group_ids = CartManager::get_cart_group_ids();
        $carts = Cart::whereIn('cart_group_id', $cart_group_ids)->get();

        $product_stock = CartManager::product_stock_check($carts);
        if (!$product_stock) {
            Toastr::error(translate('the_following_items_in_your_cart_are_currently_out_of_stock'));
            return redirect()->route('shop-cart');
        }

        $verifyStatus = OrderManager::minimum_order_amount_verify($request);
        if ($verifyStatus['status'] == 0) {
            Toastr::info(translate('check_minimum_order_amount_requirement'));
            return redirect()->route('shop-cart');
        }

        $offline_payment_info = [];
        $method = OfflinePaymentMethod::where(['id' => $request->method_id, 'status' => 1])->first();

        if (isset($method)) {
            $fields = array_column($method->method_informations, 'customer_input');
            $values = $request->all();

            $offline_payment_info['method_id'] = $request->method_id;
            $offline_payment_info['method_name'] = $method->method_name;
            foreach ($fields as $field) {
                if (key_exists($field, $values)) {
                    $offline_payment_info[$field] = $values[$field];
                }
            }
        }

        foreach ($cart_group_ids as $group_id) {
            $data = [
                'payment_method' => 'offline_payment',
                'order_status' => 'pending',
                'payment_status' => 'unpaid',
                'payment_note' => $request->payment_note,
                'order_group_id' => $unique_id,
                'cart_group_id' => $group_id,
                'offline_payment_info' => $offline_payment_info,
            ];
            $order_id = OrderManager::generate_order($data);
            array_push($order_ids, $order_id);
        }

        CartManager::cart_clean();


        return view(VIEW_FILE_NAMES['order_complete'], compact('order_ids'));
    }

    public function checkout_complete_wallet(Request $request = null)
    {
        if (session()->has('coupon_discount') && session('coupon_discount') > 0 && session('coupon_type') != 'free_delivery') {
            $cartTotal = CartManager::cart_grand_total() - session('coupon_discount');
        } else {
            $cartTotal = CartManager::cart_grand_total();
        }

        $user = Helpers::get_customer($request);
        if ($cartTotal > $user->wallet_balance) {
            Toastr::warning(translate('inefficient balance in your wallet to pay for this order!!'));
            return back();
        } else {
            $unique_id = OrderManager::gen_unique_id();
            $cart_group_ids = CartManager::get_cart_group_ids();
            $carts = Cart::whereIn('cart_group_id', $cart_group_ids)->get();

            $product_stock = CartManager::product_stock_check($carts);
            if (!$product_stock) {
                Toastr::error(translate('the_following_items_in_your_cart_are_currently_out_of_stock'));
                return redirect()->route('shop-cart');
            }

            $verifyStatus = OrderManager::minimum_order_amount_verify($request);
            if ($verifyStatus['status'] == 0) {
                Toastr::info(translate('check_minimum_order_amount_requirement'));
                return redirect()->route('shop-cart');
            }

            $order_ids = [];
            foreach ($cart_group_ids as $group_id) {
                $data = [
                    'payment_method' => 'pay_by_wallet',
                    'order_status' => 'confirmed',
                    'payment_status' => 'paid',
                    'transaction_ref' => '',
                    'order_group_id' => $unique_id,
                    'cart_group_id' => $group_id
                ];
                $order_id = OrderManager::generate_order($data);
                array_push($order_ids, $order_id);
            }

            CustomerManager::create_wallet_transaction($user->id, Convert::default($cartTotal), 'order_place', 'order payment');
            CartManager::cart_clean();
        }

        //whatsap
        $orderId = \App\Models\Order::latest()->value('id');
        $latestOrder = \App\Models\Order::latest()->first();
        $customerId = $latestOrder->customer_id ?? null;

        if (!$customerId) {
            Toastr::error('Customer not found!');
            return redirect(url('/'));
        }

        $userInfo = \App\Models\User::find($customerId);
        $orders = \App\Models\Order::where('customer_id', $customerId)
            ->where('created_at', '>=', now()->subMinutes(5))
            ->latest()
            ->get();

        foreach ($orders as $order) {
            $orderDetails = \App\Models\OrderDetail::where('order_id', $order->id)->get();
            $productIds = $orderDetails->pluck('product_id')->toArray();
            $productNames = \App\Models\Product::whereIn('id', $productIds)->pluck('name')->toArray();
            $sellerIds = $orderDetails->pluck('seller_id')->unique();
            $sellers = Seller::whereIn('id', $sellerIds)->get();
            $shops = Shop::whereIn('seller_id', $sellerIds)->pluck('name', 'seller_id');

            if ($userInfo && !empty($userInfo['email']) && filter_var($userInfo['email'], FILTER_VALIDATE_EMAIL)) {
                $data['type'] = 'ecommerece';
                $data['email'] = $userInfo['email'];
                $data['subject'] = 'Confirmation of Your Order #' . $order->id;
                $data['htmlContent'] = \Illuminate\Support\Facades\View::make(
                    'admin-views.email.email-template.ecom-template',
                    compact('userInfo', 'order', 'orderDetails', 'productNames', 'shops', 'sellers')
                )->render();

                Helpers::emailSendMessage($data);
            }

            $message_data = [
                'product_name' => implode(', ', $productNames),
                'orderId' =>  $order->id,
                'order_amount' => webCurrencyConverter(amount: (float)$order->order_amount ?? 0),
                'customer_id' => ($order->customer_id ?? ""),
            ];

            $messages =  Helpers::whatsappMessage('ecom', 'Order placed', $message_data);
        }
        if (session()->has('payment_mode') && session('payment_mode') == 'app') {
            return redirect()->route('payment-success');
        }
        return view(VIEW_FILE_NAMES['order_complete'], compact('order_ids'));
    }

    public function order_placed(): View
    {
        return view(VIEW_FILE_NAMES['order_complete']);
    }

    public function shop_cart(Request $request): View|RedirectResponse
    {
        if (
            (auth('customer')->check() && Cart::where(['customer_id' => auth('customer')->id()])->count() > 0)
            || (getWebConfig(name: 'guest_checkout') && session()->has('guest_id') && session('guest_id'))
        ) {
            $topRatedShops = [];
            $newSellers = [];
            $currentDate = date('Y-m-d H:i:s');
            if (theme_root_path() === "theme_fashion") {

                $sellerList = $this->seller->approved()->with(['shop', 'product.reviews'])
                    ->withCount(['product' => function ($query) {
                        $query->active();
                    }])->get();
                $sellerList?->map(function ($seller) {
                    $rating = 0;
                    $count = 0;
                    foreach ($seller->product as $item) {
                        foreach ($item->reviews as $review) {
                            $rating += $review->rating;
                            $count++;
                        }
                    }
                    $averageRating = $rating / ($count == 0 ? 1 : $count);
                    $ratingCount = $count;
                    $seller['average_rating'] = $averageRating;
                    $seller['rating_count'] = $ratingCount;

                    $productCount = $seller->product->count();
                    $randomProduct = Arr::random($seller->product->toArray(), $productCount < 3 ? $productCount : 3);
                    $seller['product'] = $randomProduct;
                    return $seller;
                });
                $newSellers     =  $sellerList->sortByDesc('id')->take(12);
                $topRatedShops =  $sellerList->where('rating_count', '!=', 0)->sortByDesc('average_rating')->take(12);
            }
            return view(VIEW_FILE_NAMES['cart_list'], compact('topRatedShops', 'newSellers', 'currentDate', 'request'));
        }
        Toastr::info(translate('invalid_access'));
        return redirect('/');
    }

    //ajax filter (category based)
    public function seller_shop_product(Request $request, $id): View|JsonResponse
    {
        $products = Product::active()->withCount('reviews')->with('shop')->where(['added_by' => 'seller'])
            ->where('user_id', $id)
            ->whereJsonContains('category_ids', [
                ['id' => strval($request->category_id)],
            ])
            ->paginate(12);
        $shop = Shop::where('seller_id', $id)->first();
        if ($request['sort_by'] == null) {
            $request['sort_by'] = 'latest';
        }

        if ($request->ajax()) {
            return response()->json([
                'view' => view(VIEW_FILE_NAMES['products__ajax_partials'], compact('products'))->render(),
            ], 200);
        }

        return view(VIEW_FILE_NAMES['shop_view_page'], compact('products', 'shop'))->with('seller_id', $id);
    }

    public function getQuickView(Request $request): JsonResponse
    {
        $product = ProductManager::get_product($request['product_id']);
        $order_details = OrderDetail::where('product_id', $product->id)->get();
        $wishlists = Wishlist::where('product_id', $product->id)->get();
        $wishlist_status = Wishlist::where(['product_id' => $product->id, 'customer_id' => auth('customer')->id()])->count();
        $countOrder = count($order_details);
        $countWishlist = count($wishlists);
        $relatedProducts = Product::with(['reviews'])->withCount('reviews')->where('category_ids', $product->category_ids)->where('id', '!=', $product->id)->limit(12)->get();
        $currentDate = date('Y-m-d');
        $seller_vacation_start_date = ($product->added_by == 'seller' && isset($product->seller->shop->vacation_start_date)) ? date('Y-m-d', strtotime($product->seller->shop->vacation_start_date)) : null;
        $seller_vacation_end_date = ($product->added_by == 'seller' && isset($product->seller->shop->vacation_end_date)) ? date('Y-m-d', strtotime($product->seller->shop->vacation_end_date)) : null;
        $seller_temporary_close = ($product->added_by == 'seller' && isset($product->seller->shop->temporary_close)) ? $product->seller->shop->temporary_close : false;

        $temporary_close = getWebConfig(name: 'temporary_close');
        $inhouse_vacation = getWebConfig(name: 'vacation_add');
        $inhouse_vacation_start_date = $product->added_by == 'admin' ? $inhouse_vacation['vacation_start_date'] : null;
        $inhouse_vacation_end_date = $product->added_by == 'admin' ? $inhouse_vacation['vacation_end_date'] : null;
        $inHouseVacationStatus = $product->added_by == 'admin' ? $inhouse_vacation['status'] : false;
        $inhouse_temporary_close = $product->added_by == 'admin' ? $temporary_close['status'] : false;

        // Newly Added From Blade
        $overallRating = getOverallRating($product->reviews);
        $rating = getRating($product->reviews);
        $reviews_of_product = Review::where('product_id', $product->id)->latest()->paginate(2);
        $decimal_point_settings = getWebConfig(name: 'decimal_point_settings');
        $more_product_from_seller = Product::active()->withCount('reviews')->where('added_by', $product->added_by)->where('id', '!=', $product->id)->where('user_id', $product->user_id)->latest()->take(5)->get();

        return response()->json([
            'success' => 1,
            'product' => $product,
            'view' => view(VIEW_FILE_NAMES['product_quick_view_partials'], compact(
                'product',
                'countWishlist',
                'countOrder',
                'relatedProducts',
                'currentDate',
                'seller_vacation_start_date',
                'seller_vacation_end_date',
                'seller_temporary_close',
                'inhouse_vacation_start_date',
                'inhouse_vacation_end_date',
                'inHouseVacationStatus',
                'inhouse_temporary_close',
                'wishlist_status',
                'overallRating',
                'rating'
            ))->render(),
        ]);
    }

    public function discounted_products(Request $request): View|JsonResponse
    {
        $request['sort_by'] == null ? $request['sort_by'] == 'latest' : $request['sort_by'];

        $productData = Product::active()->with(['reviews'])->withCount('reviews');

        if ($request['data_from'] == 'category') {
            $products = $productData->get();
            $product_ids = [];
            foreach ($products as $product) {
                foreach (json_decode($product['category_ids'], true) as $category) {
                    if ($category['id'] == $request['id']) {
                        array_push($product_ids, $product['id']);
                    }
                }
            }
            $query = $productData->whereIn('id', $product_ids);
        }

        if ($request['data_from'] == 'brand') {
            $query = $productData->where('brand_id', $request['id']);
        }

        if ($request['data_from'] == 'latest') {
            $query = $productData->orderBy('id', 'DESC');
        }

        if ($request['data_from'] == 'top-rated') {
            $reviews = Review::select('product_id', DB::raw('AVG(rating) as count'))
                ->groupBy('product_id')
                ->orderBy("count", 'desc')->get();
            $product_ids = [];
            foreach ($reviews as $review) {
                array_push($product_ids, $review['product_id']);
            }
            $query = $productData->whereIn('id', $product_ids);
        }

        if ($request['data_from'] == 'best-selling') {
            $details = OrderDetail::with('product')
                ->select('product_id', DB::raw('COUNT(product_id) as count'))
                ->groupBy('product_id')
                ->orderBy("count", 'desc')
                ->get();
            $product_ids = [];
            foreach ($details as $detail) {
                array_push($product_ids, $detail['product_id']);
            }
            $query = $productData->whereIn('id', $product_ids);
        }

        if ($request['data_from'] == 'most-favorite') {
            $details = Wishlist::with('product')
                ->select('product_id', DB::raw('COUNT(product_id) as count'))
                ->groupBy('product_id')
                ->orderBy("count", 'desc')
                ->get();
            $product_ids = [];
            foreach ($details as $detail) {
                array_push($product_ids, $detail['product_id']);
            }
            $query = $productData->whereIn('id', $product_ids);
        }

        if ($request['data_from'] == 'featured') {
            $query = Product::with(['reviews'])->active()->where('featured', 1);
        }

        if ($request['data_from'] == 'search') {
            $key = explode(' ', $request['name']);
            $query = $productData->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%");
                }
            });
        }

        if ($request['data_from'] == 'discounted_products') {
            $query = Product::with(['reviews'])->active()->where('discount', '!=', 0);
        }

        if ($request['sort_by'] == 'latest') {
            $fetched = $query->latest();
        } elseif ($request['sort_by'] == 'low-high') {
            $fetched = $query->orderBy('unit_price', 'ASC');
        } elseif ($request['sort_by'] == 'high-low') {
            $fetched = $query->orderBy('unit_price', 'DESC');
        } elseif ($request['sort_by'] == 'a-z') {
            $fetched = $query->orderBy('name', 'ASC');
        } elseif ($request['sort_by'] == 'z-a') {
            $fetched = $query->orderBy('name', 'DESC');
        } else {
            $fetched = $query;
        }

        if ($request['min_price'] != null || $request['max_price'] != null) {
            $fetched = $fetched->whereBetween('unit_price', [Helpers::convert_currency_to_usd($request['min_price']), Helpers::convert_currency_to_usd($request['max_price'])]);
        }

        $data = [
            'id' => $request['id'],
            'name' => $request['name'],
            'data_from' => $request['data_from'],
            'sort_by' => $request['sort_by'],
            'page_no' => $request['page'],
            'min_price' => $request['min_price'],
            'max_price' => $request['max_price'],
        ];

        $products = $fetched->paginate(5)->appends($data);

        if ($request->ajax()) {
            return response()->json([
                'view' => view(VIEW_FILE_NAMES['products__ajax_partials'], compact('products'))->render()
            ], 200);
        }
        if ($request['data_from'] == 'category') {
            $data['brand_name'] = Category::find((int)$request['id'])->name;
        }
        if ($request['data_from'] == 'brand') {
            $data['brand_name'] = Brand::active()->find((int)$request['id'])->name;
        }

        return view(VIEW_FILE_NAMES['products_view_page'], compact('products', 'data'), $data);
    }

    public function viewWishlist(Request $request): View
    {
        $brand_setting = BusinessSetting::where('type', 'product_brand')->first()->value;

        $wishlists = Wishlist::with([
            'productFullInfo',
            'productFullInfo.compareList' => function ($query) {
                return $query->where('user_id', auth('customer')->id() ?? 0);
            }
        ])
            ->whereHas('wishlistProduct', function ($q) use ($request) {
                $q->when($request['search'], function ($query) use ($request) {
                    $query->where('name', 'like', "%{$request['search']}%")
                        ->orWhereHas('category', function ($qq) use ($request) {
                            $qq->where('name', 'like', "%{$request['search']}%");
                        });
                });
            })
            ->where('customer_id', auth('customer')->id())->paginate(15);

        return view(VIEW_FILE_NAMES['account_wishlist'], compact('wishlists', 'brand_setting'));
    }

    public function storeWishlist(Request $request)
    {
        if ($request->ajax()) {
            if (auth('customer')->check()) {
                $wishlist = Wishlist::where('customer_id', auth('customer')->id())->where('product_id', $request->product_id)->first();
                if ($wishlist) {
                    $wishlist->delete();

                    $countWishlist = Wishlist::whereHas('wishlistProduct', function ($q) {
                        return $q;
                    })->where('customer_id', auth('customer')->id())->count();
                    $product_count = Wishlist::where(['product_id' => $request->product_id])->count();
                    session()->put('wish_list', Wishlist::where('customer_id', auth('customer')->user()->id)->pluck('product_id')->toArray());

                    return response()->json([
                        'error' => translate("product_removed_from_the_wishlist"),
                        'value' => 2,
                        'count' => $countWishlist,
                        'product_count' => $product_count
                    ]);
                } else {
                    $wishlist = new Wishlist;
                    $wishlist->customer_id = auth('customer')->id();
                    $wishlist->product_id = $request->product_id;
                    $wishlist->save();

                    $countWishlist = Wishlist::whereHas('wishlistProduct', function ($q) {
                        return $q;
                    })->where('customer_id', auth('customer')->id())->count();

                    $product_count = Wishlist::where(['product_id' => $request->product_id])->count();
                    session()->put('wish_list', Wishlist::where('customer_id', auth('customer')->user()->id)->pluck('product_id')->toArray());

                    return response()->json([
                        'success' => translate("Product has been added to wishlist"),
                        'value' => 1,
                        'count' => $countWishlist,
                        'id' => $request->product_id,
                        'product_count' => $product_count
                    ]);
                }
            } else {
                return response()->json(['error' => translate('login_first'), 'value' => 0]);
            }
        }
    }

    public function deleteWishlist(Request $request)
    {
        $this->wishlist->where(['product_id' => $request['id'], 'customer_id' => auth('customer')->id()])->delete();
        $data = translate('product_has_been_remove_from_wishlist') . '!';
        $wishlists = $this->wishlist->where('customer_id', auth('customer')->id())->paginate(15);
        $brand_setting = BusinessSetting::where('type', 'product_brand')->first()->value;
        session()->put('wish_list', $this->wishlist->where('customer_id', auth('customer')->user()->id)->pluck('product_id')->toArray());
        return response()->json([
            'success' => $data,
            'count' => count($wishlists),
            'id' => $request->id,
            'wishlist' => view(VIEW_FILE_NAMES['account_wishlist_partials'], compact('wishlists', 'brand_setting'))->render(),
        ]);
    }

    public function delete_wishlist_all()
    {
        $this->wishlist->where('customer_id', auth('customer')->id())->delete();
        session()->put('wish_list', $this->wishlist->where('customer_id', auth('customer')->user()->id)->pluck('product_id')->toArray());
        return redirect()->back();
    }

    //order Details

    public function orderdetails()
    {
        return view('web-views.orderdetails');
    }

    public function chat_for_product(Request $request)
    {
        return $request->all();
    }

    public function supportChat()
    {
        return view('web-views.users-profile.profile.supportTicketChat');
    }

    public function error()
    {
        return view('web-views.404-error-page');
    }

    public function contact_store(Request $request)
    {
        //recaptcha validation
        $recaptcha = Helpers::get_business_settings('recaptcha');
        if (isset($recaptcha) && $recaptcha['status'] == 1) {

            try {
                $request->validate([
                    'g-recaptcha-response' => [
                        function ($attribute, $value, $fail) {
                            $secret_key = Helpers::get_business_settings('recaptcha')['secret_key'];
                            $response = $value;
                            $url = 'https://www.google.com/recaptcha/api/siteverify?secret=' . $secret_key . '&response=' . $response;
                            $response = \file_get_contents($url);
                            $response = json_decode($response);
                            if (!$response->success) {
                                $fail(translate('ReCAPTCHA Failed'));
                            }
                        },
                    ],
                ]);
            } catch (\Exception $exception) {
                return back()->withErrors(translate('Captcha Failed'))->withInput($request->input());
            }
        } else {
            if (strtolower($request->default_captcha_value) != strtolower(Session('default_captcha_code'))) {
                Session::forget('default_captcha_code');
                Toastr::error(translate('captcha_failed'));
                return back()->withInput($request->input());
            }
        }

        $request->validate([
            'mobile_number' => 'required',
            'subject' => 'required',
            'message' => 'required',
            'email' => 'email',
        ], [
            'mobile_number.required' => 'Mobile Number is Empty!',
            'subject.required' => ' Subject is Empty!',
            'message.required' => 'Message is Empty!',
        ]);
        $contact = new Contact;
        $contact->name = $request['name'];
        $contact->email = $request['email'];
        $contact->mobile_number = $request['country_code'] . $request['mobile_number'];
        $contact->subject = $request['subject'];
        $contact->message = $request['message'];
        $contact->save();
        Toastr::success(translate('Your Message Send Successfully'));
        return back();
    }

    public function captcha($tmp)
    {

        $phrase = new PhraseBuilder;
        $code = $phrase->build(4);
        $builder = new CaptchaBuilder($code, $phrase);
        $builder->setBackgroundColor(220, 210, 230);
        $builder->setMaxAngle(25);
        $builder->setMaxBehindLines(0);
        $builder->setMaxFrontLines(0);
        $builder->build($width = 100, $height = 40, $font = null);
        $phrase = $builder->getPhrase();

        if (Session::has('default_captcha_code')) {
            Session::forget('default_captcha_code');
        }
        Session::put('default_captcha_code', $phrase);
        header("Cache-Control: no-cache, must-revalidate");
        header("Content-Type:image/jpeg");
        $builder->output();
    }

    public function order_note(Request $request)
    {
        if ($request->has('order_note')) {
            session::put('order_note', $request['order_note']);
        }
        $response = self::checkValidationForCheckoutPages($request);
        return response()->json($response);
    }

    public function checkValidationForCheckoutPages(Request $request): array
    {
        $response['status'] = 1;
        $response['physical_product_view'] = false;
        $message = [];

        $verifyStatus = OrderManager::minimum_order_amount_verify($request);
        if ($verifyStatus['status'] == 0) {
            $response['status'] = 0;
            $response['errorType'] = 'minimum-order-amount';
            $response['redirect'] = route('shop-cart');
            foreach ($verifyStatus['messages'] as $verifyStatusMessages) {
                $message[] = $verifyStatusMessages;
            }
        }

        $cartItemGroupIDs = CartManager::get_cart_group_ids();
        $shippingMethod = getWebConfig(name: 'shipping_method');

        if (count($cartItemGroupIDs) <= 0) {
            $response['status'] = 0;
            $response['errorType'] = 'empty-cart';
            $response['redirect'] = url('/');
            $message[] = translate('no_items_in_basket');
        }

        $unavailableVendorsStatus = 0;
        $inhouseShippingMsgCount = 0;

        $isPhysicalProductExist = false;
        foreach ($cartItemGroupIDs as $groupId) {
            $cartList = Cart::where('cart_group_id', $groupId)->get();
            foreach ($cartList as $cart) {
                if ($cart->product_type == 'physical') {
                    $isPhysicalProductExist = true;
                    $response['physical_product_view'] = true;
                }
            }
        }

        foreach ($cartItemGroupIDs as $groupId) {
            $cartList = Cart::groupBy('cart_group_id')->where('cart_group_id', $groupId)->get();

            foreach ($cartList as $cartKey => $cart) {
                if ($cartKey == 0) {
                    if ($cart->seller_is == 'admin') {
                        $inhouseTemporaryClose = getWebConfig(name: 'temporary_close') ? getWebConfig(name: 'temporary_close')['status'] : 0;
                        $inhouseVacation = getWebConfig(name: 'vacation_add');
                        $vacationStartDate = $inhouseVacation['vacation_start_date'] ? date('Y-m-d', strtotime($inhouseVacation['vacation_start_date'])) : null;
                        $vacationEndDate = $inhouseVacation['vacation_end_date'] ? date('Y-m-d', strtotime($inhouseVacation['vacation_end_date'])) : null;
                        $vacationStatus = $inhouseVacation['status'] ?? 0;
                        if ($inhouseTemporaryClose || ($vacationStatus && (date('Y-m-d') >= $vacationStartDate) && (date('Y-m-d') <= $vacationEndDate))) {
                            $unavailableVendorsStatus = 1;
                        }
                    } else {
                        $sellerInfo = Seller::where('id', $cart->seller_id)->first();
                        if (!$sellerInfo || $sellerInfo->status != 'approved') {
                            $unavailableVendorsStatus = 1;
                        }
                        if (!isset($sellerInfo->shop) || ($sellerInfo->shop->vacation_status || $sellerInfo->shop->temporary_close)) {
                            $unavailableVendorsStatus = 1;
                        }
                    }
                }
            }

            if ($isPhysicalProductExist) {
                foreach ($cartList as $cart) {
                    if ($shippingMethod == 'inhouse_shipping') {
                        $adminShipping = ShippingType::where('seller_id', 0)->first();
                        $shippingType = isset($adminShipping) ? $adminShipping->shipping_type : 'order_wise';
                    } else {
                        if ($cart->seller_is == 'admin') {
                            $adminShipping = ShippingType::where('seller_id', 0)->first();
                            $shippingType = isset($adminShipping) ? $adminShipping->shipping_type : 'order_wise';
                        } else {
                            $sellerShipping = ShippingType::where('seller_id', $cart->seller_id)->first();
                            $shippingType = isset($sellerShipping) ? $sellerShipping->shipping_type : 'order_wise';
                        }
                    }

                    if ($isPhysicalProductExist && $shippingType == 'order_wise') {
                        $sellerShippingCount = 0;
                        if ($shippingMethod == 'inhouse_shipping') {
                            $sellerShippingCount = ShippingMethod::where(['status' => 1])->where(['creator_type' => 'admin'])->count();
                            if ($sellerShippingCount <= 0 && isset($cart->seller->shop)) {
                                $message[] = translate('shipping_Not_Available_for') . ' ' . getWebConfig(name: 'company_name');
                                $response['status'] = 0;
                                $response['redirect'] = route('shop-cart');
                            }
                        } else {
                            if ($cart->seller_is == 'admin') {
                                $sellerShippingCount = ShippingMethod::where(['status' => 1])->where(['creator_type' => 'admin'])->count();
                                if ($sellerShippingCount <= 0 && isset($cart->seller->shop)) {
                                    $message[] = translate('shipping_Not_Available_for') . ' ' . getWebConfig(name: 'company_name');
                                    $response['status'] = 0;
                                    $response['redirect'] = route('shop-cart');
                                }
                            } else if ($cart->seller_is == 'seller') {
                                $sellerShippingCount = ShippingMethod::where(['status' => 1])->where(['creator_id' => $cart->seller_id, 'creator_type' => 'seller'])->count();
                                if ($sellerShippingCount <= 0 && isset($cart->seller->shop)) {
                                    $message[] = translate('shipping_Not_Available_for') . ' ' . $cart->seller->shop->name;
                                    $response['status'] = 0;
                                    $response['redirect'] = route('shop-cart');
                                }
                            }
                        }

                        if ($sellerShippingCount > 0 && $shippingMethod == 'inhouse_shipping' && $inhouseShippingMsgCount < 1) {
                            $cartShipping = CartShipping::where('cart_group_id', $cart->cart_group_id)->first();
                            if (!isset($cartShipping)) {
                                $response['status'] = 0;
                                $response['errorType'] = 'empty-shipping';
                                $response['redirect'] = route('shop-cart');
                                $message[] = translate('select_shipping_method');
                            }
                            $inhouseShippingMsgCount++;
                        } elseif ($sellerShippingCount > 0 && $shippingMethod != 'inhouse_shipping') {
                            $cartShipping = CartShipping::where('cart_group_id', $cart->cart_group_id)->first();
                            if (!isset($cartShipping)) {
                                $response['status'] = 0;
                                $response['errorType'] = 'empty-shipping';
                                $response['redirect'] = route('shop-cart');
                                $shopIdentity = $cart->seller_is == 'admin' ? getWebConfig(name: 'company_name') : $cart->seller->shop->name;
                                $message[] = translate('select') . ' ' . $shopIdentity . ' ' . translate('shipping_method');
                            }
                        }
                    }
                }
            }
        }

        if ($unavailableVendorsStatus) {
            $message[] = translate('please_remove_all_products_from_unavailable_vendors');
            $response['status'] = 0;
            $response['redirect'] = route('shop-cart');
        }

        $response['message'] = $message;
        return $response ?? [];
    }


    public function getDigitalProductDownload($id, Request $request): JsonResponse
    {
        $orderDetailsData = OrderDetail::with('order.customer')->find($id);
        if ($orderDetailsData) {
            if ($orderDetailsData->order->payment_status !== "paid") {
                return response()->json([
                    'status' => 0,
                    'message' => translate('Payment_must_be_confirmed_first') . ' !!',
                ]);
            };

            if ($orderDetailsData->order->is_guest) {
                $customerEmail = $orderDetailsData->order->shipping_address_data ? $orderDetailsData->order->shipping_address_data->email : ($orderDetailsData->order->billing_address_data ? $orderDetailsData->order->billing_address_data->email : '');

                $customerPhone = $orderDetailsData->order->shipping_address_data ? $orderDetailsData->order->shipping_address_data->phone : ($orderDetailsData->order->billing_address_data ? $orderDetailsData->order->billing_address_data->phone : '');

                $customerData = ['email' => $customerEmail, 'phone' => $customerPhone];
                return self::getDigitalProductDownloadProcess(orderDetailsData: $orderDetailsData, customer: $customerData);
            } else {
                if (auth('customer')->check() && auth('customer')->user()->id == $orderDetailsData->order->customer->id) {
                    $fileName = '';
                    $productDetails = json_decode($orderDetailsData['product_details']);
                    if ($productDetails->digital_product_type == 'ready_product' && $productDetails->digital_file_ready) {
                        $filePath = asset('storage/app/public/product/digital-product/' . $productDetails->digital_file_ready);
                        $fileName = $productDetails->digital_file_ready;
                    } else {
                        $filePath = asset('storage/app/public/product/digital-product/' . $orderDetailsData['digital_file_after_sell']);
                        $fileName = $orderDetailsData['digital_file_after_sell'];
                    }

                    if (File::exists(base_path('storage/app/public/product/digital-product/' . $fileName))) {
                        return response()->json([
                            'status' => 1,
                            'file_path' => $filePath,
                            'file_name' => $fileName,
                        ]);
                    } else {
                        return response()->json([
                            'status' => 0,
                            'message' => translate('file_not_found'),
                        ]);
                    }
                } else {
                    $customerData = ['email' => $orderDetailsData->order->customer->email ?? '', 'phone' => $orderDetailsData->order->customer->phone ?? ''];
                    return self::getDigitalProductDownloadProcess(orderDetailsData: $orderDetailsData, customer: $customerData);
                }
            }
        } else {
            return response()->json([
                'status' => 0,
                'message' => translate('order_Not_Found') . ' !',
            ]);
        }
    }

    public function getDigitalProductDownloadOtpVerify(Request $request): JsonResponse
    {

        $verification = DigitalProductOtpVerification::where(['token' => $request->otp, 'order_details_id' => $request->order_details_id])->first();
        $orderDetailsData = OrderDetail::with('order.customer')->find($request->order_details_id);

        if ($verification) {
            $fileName = '';
            if ($orderDetailsData) {
                $productDetails = json_decode($orderDetailsData['product_details']);
                if ($productDetails->digital_product_type == 'ready_product' && $productDetails->digital_file_ready) {
                    $filePath = asset('storage/app/public/product/digital-product/' . $productDetails->digital_file_ready);
                    $fileName = $productDetails->digital_file_ready;
                } else {
                    $filePath = asset('storage/app/public/product/digital-product/' . $orderDetailsData['digital_file_after_sell']);
                    $fileName = $orderDetailsData['digital_file_after_sell'];
                }
            }

            DigitalProductOtpVerification::where(['token' => $request->otp, 'order_details_id' => $request->order_details_id])->delete();

            if (File::exists(base_path('storage/app/public/product/digital-product/' . $fileName))) {
                return response()->json([
                    'status' => 1,
                    'file_path' => $filePath ?? '',
                    'file_name' => $fileName ?? '',
                    'message' => translate('successfully_verified'),
                ]);
            } else {
                return response()->json([
                    'status' => 0,
                    'message' => translate('file_not_found'),
                ]);
            }
        } else {
            return response()->json([
                'status' => 0,
                'message' => translate('the_OTP_is_incorrect') . ' !',
            ]);
        }
    }

    public function getDigitalProductDownloadOtpReset(Request $request): JsonResponse
    {
        $tokenInfo = DigitalProductOtpVerification::where(['order_details_id' => $request->order_details_id])->first();
        $otpIntervalTime = getWebConfig(name: 'otp_resend_time') ?? 1; //minute
        if (isset($tokenInfo) &&  Carbon::parse($tokenInfo->created_at)->diffInSeconds() < $otpIntervalTime) {
            $timeCount = $otpIntervalTime - Carbon::parse($tokenInfo->created_at)->diffInSeconds();

            return response()->json([
                'status' => 0,
                'time_count' => CarbonInterval::seconds($timeCount)->cascade()->forHumans(),
                'message' => translate('Please_try_again_after') . ' ' . CarbonInterval::seconds($timeCount)->cascade()->forHumans()
            ]);
        } else {
            $guestEmail = '';
            $guestPhone = '';
            $token = rand(1000, 9999);

            $orderDetailsData = OrderDetail::with('order.customer')->find($request->order_details_id);

            try {
                if ($orderDetailsData->order->is_guest) {
                    if ($orderDetailsData->order->shipping_address_data) {
                        $guestEmail = $orderDetailsData->order->shipping_address_data ? $orderDetailsData->order->shipping_address_data->email : null;
                        $guestPhone = $orderDetailsData->order->shipping_address_data ? $orderDetailsData->order->shipping_address_data->phone : null;
                    } else {
                        $guestEmail = $orderDetailsData->order->billing_address_data ? $orderDetailsData->order->billing_address_data->email : null;
                        $guestPhone = $orderDetailsData->order->billing_address_data ? $orderDetailsData->order->billing_address_data->phone : null;
                    }
                } else {
                    $guestEmail = $orderDetailsData->order->customer->email;
                    $guestPhone = $orderDetailsData->order->customer->phone;
                }
            } catch (\Throwable $th) {
            }

            $verifyData = [
                'order_details_id' => $orderDetailsData->id,
                'token' => $token,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            DigitalProductOtpVerification::updateOrInsert(['identity' => $guestEmail, 'order_details_id' => $orderDetailsData->id], $verifyData);
            DigitalProductOtpVerification::updateOrInsert(['identity' => $guestPhone, 'order_details_id' => $orderDetailsData->id], $verifyData);

            $emailServicesSmtp = getWebConfig(name: 'mail_config');
            if ($emailServicesSmtp['status'] == 0) {
                $emailServicesSmtp = getWebConfig(name: 'mail_config_sendgrid');
            }

            if ($emailServicesSmtp['status'] == 1) {
                try {
                    DigitalProductOtpVerificationMailEvent::dispatch($guestEmail, $token);
                    $mailStatus = 1;
                } catch (\Exception $exception) {
                    $mailStatus = 0;
                }
            } else {
                $mailStatus = 0;
            }

            $publishedStatus = 0;
            $paymentPublishedStatus = config('get_payment_publish_status');
            if (isset($paymentPublishedStatus[0]['is_published'])) {
                $publishedStatus = $paymentPublishedStatus[0]['is_published'];
            }

            $response = '';
            if ($publishedStatus == 1) {
                $response = $this->send(receiver: $guestPhone, otp: $token);
            } else {
                $response = SMS_module::send($guestPhone, $token);
            }

            $smsStatus = $response == "not_found" ? 0 : 1;

            return response()->json([
                'mail_status' => $mailStatus,
                'sms_status' => $smsStatus,
                'status' => ($mailStatus || $smsStatus) ? 1 : 0,
                'new_time' => $otpIntervalTime,
                'message' => ($mailStatus || $smsStatus) ? translate('OTP_sent_successfully') : translate('OTP_sent_fail'),
            ]);
        }
    }

    public function getDigitalProductDownloadProcess($orderDetailsData, $customer): JsonResponse
    {
        $status = 2;
        $emailServicesSmtp = getWebConfig(name: 'mail_config');
        if ($emailServicesSmtp['status'] == 0) {
            $emailServicesSmtp = getWebConfig(name: 'mail_config_sendgrid');
        }

        $paymentPublishedStatus = config('get_payment_publish_status');
        $publishedStatus = isset($paymentPublishedStatus[0]['is_published']) ? $paymentPublishedStatus[0]['is_published'] : 0;

        if ($publishedStatus == 1) {
            $smsConfigStatus = Setting::where(['settings_type' => 'sms_config', 'is_active' => 1])->count() > 0 ? 1 : 0;
        } else {
            $smsConfigStatus = Setting::where(['settings_type' => 'sms_config', 'is_active' => 1])->whereIn('key_name', Helpers::default_sms_gateways())->count() > 0 ? 1 : 0;
        }

        if ($emailServicesSmtp['status'] || $smsConfigStatus) {
            $token = rand(1000, 9999);
            if ($customer['email'] == '' && $customer['phone'] == '') {
                return response()->json([
                    'status' => $status,
                    'file_path' => '',
                    'view' => view(VIEW_FILE_NAMES['digital_product_order_otp_verify_failed'])->render(),
                ]);
            }

            $verificationData = DigitalProductOtpVerification::where('identity', $customer['email'])->orWhere('identity', $customer['phone'])->where('order_details_id', $orderDetailsData->id)->latest()->first();
            $otpIntervalTime = getWebConfig(name: 'otp_resend_time') ?? 1; //second

            if (isset($verificationData) &&  Carbon::parse($verificationData->created_at)->diffInSeconds() < $otpIntervalTime) {
                $timeCount = $otpIntervalTime - Carbon::parse($verificationData->created_at)->diffInSeconds();
                return response()->json([
                    'status' => $status,
                    'file_path' => '',
                    'view' => view(VIEW_FILE_NAMES['digital_product_order_otp_verify'], ['orderDetailID' => $orderDetailsData->id, 'time_count' => $timeCount])->render(),
                ]);
            } else {
                $verifyData = [
                    'order_details_id' => $orderDetailsData->id,
                    'token' => $token,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                DigitalProductOtpVerification::updateOrInsert(['identity' => $customer['email'], 'order_details_id' => $orderDetailsData->id], $verifyData);
                DigitalProductOtpVerification::updateOrInsert(['identity' => $customer['phone'], 'order_details_id' => $orderDetailsData->id], $verifyData);

                $resetData = DigitalProductOtpVerification::where('identity', $customer['email'])->orWhere('identity', $customer['phone'])->where('order_details_id', $orderDetailsData->id)->latest()->first();
                $otpResendTime = getWebConfig(name: 'otp_resend_time') > 0 ? getWebConfig(name: 'otp_resend_time') : 0;
                $tokenTime = Carbon::parse($resetData->created_at);
                $convertTime = $tokenTime->addSeconds($otpResendTime);
                $timeCount = $convertTime > Carbon::now() ? Carbon::now()->diffInSeconds($convertTime) : 0;
                $mailStatus = 0;

                if ($emailServicesSmtp['status'] == 1) {
                    try {
                        DigitalProductOtpVerificationMailEvent::dispatch($customer['email'], $token);
                        $mailStatus = 1;
                    } catch (\Exception $exception) {
                    }
                }

                $response = '';
                if ($smsConfigStatus && $publishedStatus == 1) {
                    $response = SmsGateway::send($customer['phone'], $token);
                } else if ($smsConfigStatus && $publishedStatus == 0) {
                    $response = SMS_module::send($customer['phone'], $token);
                }

                $smsStatus = ($response == "not_found" || $smsConfigStatus == 0) ? 0 : 1;
                if ($mailStatus || $smsStatus) {
                    return response()->json([
                        'status' => $status,
                        'file_path' => '',
                        'view' => view(VIEW_FILE_NAMES['digital_product_order_otp_verify'], ['orderDetailID' => $orderDetailsData->id, 'time_count' => $timeCount])->render(),
                    ]);
                } else {
                    return response()->json([
                        'status' => $status,
                        'file_path' => '',
                        'view' => view(VIEW_FILE_NAMES['digital_product_order_otp_verify_failed'])->render(),
                    ]);
                }
            }
        } else {
            return response()->json([
                'status' => $status,
                'file_path' => '',
                'view' => view(VIEW_FILE_NAMES['digital_product_order_otp_verify_failed'])->render(),
            ]);
        }
    }


    public function subscription(Request $request)
    {
        $request->validate([
            'subscription_email' => 'required|email'
        ]);
        $subscriptionEmail = Subscription::where('email', $request['subscription_email'])->first();

        if (isset($subscriptionEmail)) {
            Toastr::info(translate('You_already_subscribed_this_site'));
        } else {
            $newSubscription = new Subscription;
            $newSubscription->email = $request['subscription_email'];
            $newSubscription->save();
            Toastr::success(translate('Your_subscription_successfully_done'));
        }
        if (str_contains(url()->previous(), 'checkout-complete') || str_contains(url()->previous(), 'web-payment')) {
            return redirect()->route('home');
        }
        return back();
    }
    public function review_list_product(Request $request)
    {
        $productReviews = Review::where('product_id', $request->product_id)->latest()->paginate(2, ['*'], 'page', $request->offset + 1);
        $checkReviews = Review::where('product_id', $request->product_id)->latest()->paginate(2, ['*'], 'page', ($request->offset + 1));
        return response()->json([
            'productReview' => view(VIEW_FILE_NAMES['product_reviews_partials'], compact('productReviews'))->render(),
            'not_empty' => $productReviews->count(),
            'checkReviews' => $checkReviews->count(),
        ]);
    }
    public function review_list_shop(Request $request)
    {
        $seller_id = 0;
        if ($request->shop_id != 0) {
            $seller_id = Shop::where('id', $request->shop_id)->first()->seller_id;
        }
        $product_ids = Product::when($request->shop_id == 0, function ($query) {
            return $query->where(['added_by' => 'admin']);
        })
            ->when($request->shop_id != 0, function ($query) use ($seller_id) {
                return $query->where(['added_by' => 'seller'])
                    ->where('user_id', $seller_id);
            })
            ->pluck('id')->toArray();

        $productReviews = Review::active()->whereIn('product_id', $product_ids)->latest()->paginate(4, ['*'], 'page', $request->offset + 1);
        $checkReviews = Review::active()->whereIn('product_id', $product_ids)->latest()->paginate(4, ['*'], 'page', ($request->offset + 1));

        return response()->json([
            'productReview' => view(VIEW_FILE_NAMES['product_reviews_partials'], compact('productReviews'))->render(),
            'not_empty' => $productReviews->count(),
            'checkReviews' => $checkReviews->count(),
        ]);
    }
    public function product_view_style(Request $request)
    {
        Session::put('product_view_style', $request->value);
        return response()->json([
            'message' => translate('View_style_updated') . "!",
        ]);
    }


    public function pay_offline_method_list(Request $request)
    {

        $method = OfflinePaymentMethod::where(['id' => $request->method_id, 'status' => 1])->first();

        return response()->json([
            'methodHtml' => view(VIEW_FILE_NAMES['pay_offline_method_list_partials'], compact('method'))->render(),
        ]);
    }

    // new methods 
    public function rashi_detail(string $rashislug)
    {
        $imagesrc = "";
        if ($rashislug == 'aries') {
            $imagesrc = "public/assets/front-end/img/rashi/1.png";
        } else if ($rashislug == 'taurus') {
            $imagesrc = "public/assets/front-end/img/rashi/2.png";
        } else if ($rashislug == 'gemini') {
            $imagesrc = "public/assets/front-end/img/rashi/3.png";
        } else if ($rashislug == 'cancer') {
            $imagesrc = "public/assets/front-end/img/rashi/4.png";
        } else if ($rashislug == 'leo') {
            $imagesrc = "public/assets/front-end/img/rashi/5.png";
        } else if ($rashislug == 'virgo') {
            $imagesrc = "public/assets/front-end/img/rashi/6.png";
        } else if ($rashislug == 'libra') {
            $imagesrc = "public/assets/front-end/img/rashi/7.png";
        } else if ($rashislug == 'scorpio') {
            $imagesrc = "public/assets/front-end/img/rashi/8.png";
        } else if ($rashislug == 'sagittarius') {
            $imagesrc = "public/assets/front-end/img/rashi/9.png";
        } else if ($rashislug == 'capricorn') {
            $imagesrc = "public/assets/front-end/img/rashi/10.png";
        } else if ($rashislug == 'aquarius') {
            $imagesrc = "public/assets/front-end/img/rashi/11.png";
        } else if ($rashislug == 'pisces') {
            $imagesrc = "public/assets/front-end/img/rashi/12.png";
        }

        $apiData = array(
            'tzone' => 5.5
        );

        // daily rashi
        $url = "https://json.astrologyapi.com/v1/sun_sign_prediction/daily/" . $rashislug;
        $dailyRashiData = json_decode(ApiHelper::astroApi($url, 'hi', $apiData), true);

        // month rashi
        $month = date('m');
        $monthWord = $month == "01" ? 'jan' : ($month == "02" ? 'feb' : ($month == "03" ? 'march' : ($month == "04" ? 'april' : ($month == "05" ? 'may' : ($month == "06" ? 'june' : ($month == "07" ? 'july' : ($month == "08" ? 'august' : ($month == "09" ? 'sep' : ($month == "10" ? 'oct' : ($month == "11" ? 'nov' : 'dec'))))))))));
        $monthRashiData = MasikRashi::where(['name' => $rashislug, 'month' => $monthWord, 'status' => 1])->get();
        // dd($monthRashiData);

        // year rashi
        $yearRashiData = VarshikRashi::where(['name' => $rashislug, 'status' => 1])->get();

        $rashi = Rashi::where(['slug' => $rashislug, 'status' => 1])->first();
        return view('web-views.rashi.rashi-detail', compact('rashi', 'imagesrc', 'dailyRashiData', 'monthRashiData', 'yearRashiData'));
    }

    public function calculator($slug)
    {
        $calculatorList = Calculator::take(15)->get();
        $calculator = Calculator::where('slug', $slug)->first();
        $country = Country::all();
        return view('web-views.calculator.calculator-detail', compact('calculatorList', 'calculator', 'country'));
    }

    public function kundali(Request $request)
    {
        $ipAddress = getenv('REMOTE_ADDR');
        $userId = null;
        if (auth('customer')->check()) {
            $userId = auth('customer')->user()->id;
        }

        $userExists = UserKundali::where(['user_id' => $userId, 'device_id' => $ipAddress, 'name' => $request->username, 'dob' => date('Y-m-d', strtotime(str_replace('/', '-', $request->dob))), 'time' => $request->time, 'country' => $request->country, 'latitude' => $request->latitude, 'longitude' => $request->longitude, 'timezone' => $request->timezone])->exists();

        if ($userExists == false) {
            $user = new UserKundali;
            if (auth('customer')->check()) {
                $user->user_id = auth('customer')->user()->id;
            }
            $user->device_id = $ipAddress;
            $user->name = $request->username;
            $user->dob = date('Y-m-d', strtotime(str_replace('/', '-', $request->dob)));
            $user->time = $request->time;
            $user->country = $request->country;
            $user->city = $request->places;
            $user->latitude = $request->latitude;
            $user->longitude = $request->longitude;
            $user->timezone = $request->timezone;
            $user->save();
        }

        $userData = $request->input();
        $dob = explode('/', $request->dob);
        $time = explode(':', $request->time);
        $apiData = array(
            'day' => $dob['0'],
            'month' => $dob['1'],
            'year' => $dob['2'],
            'hour' => $time['0'],
            'min' => $time['1'],
            'lat' => $request->latitude,
            'lon' => $request->longitude,
            'tzone' => $request->timezone
        );
        $astroData = json_decode(ApiHelper::astroApi('https://json.astrologyapi.com/v1/astro_details', 'hi', $apiData), true);
        $birthData = json_decode(ApiHelper::astroApi('https://json.astrologyapi.com/v1/birth_details', 'hi', $apiData), true);
        $panchangData = json_decode(ApiHelper::astroApi('https://json.astrologyapi.com/v1/basic_panchang', 'hi', $apiData), true);
        $lagnaData = json_decode(ApiHelper::astroApi('https://json.astrologyapi.com/v1/general_ascendant_report', 'hi', $apiData), true);
        if ($astroData == false && $birthData == false && $panchangData == false && $lagnaData == false) {
            Toastr::error(translate('An error occured'));
            return back();
        } else {
            return view('web-views.kundali.kundali-detail', compact('apiData', 'userData', 'astroData', 'birthData', 'panchangData', 'lagnaData'));
        }
    }

    public function kundali_milan(Request $request)
    {
        // dd($request->input());
        $ipAddress = getenv('REMOTE_ADDR');
        $userId = null;
        if (auth('customer')->check()) {
            $userId = auth('customer')->user()->id;
        }

        $userExists = UserKundaliMilan::where(['user_id' => $userId, 'device_id' => $ipAddress, 'male_name' => $request->male_name, 'male_dob' => date('Y-m-d', strtotime(str_replace('/', '-', $request->male_dob))), 'male_time' => $request->male_time, 'male_country' => $request->male_country, 'male_latitude' => $request->male_latitude, 'male_longitude' => $request->male_longitude, 'male_timezone' => $request->male_timezone, 'female_name' => $request->female_name, 'female_dob' => date('Y-m-d', strtotime(str_replace('/', '-', $request->female_dob))), 'female_time' => $request->female_time, 'female_country' => $request->female_country, 'female_latitude' => $request->female_latitude, 'female_longitude' => $request->female_longitude, 'female_timezone' => $request->female_timezone])->exists();

        if ($userExists == false) {
            $user = new UserKundaliMilan();
            if (auth('customer')->check()) {
                $user->user_id = auth('customer')->user()->id;
            }
            $user->device_id = $ipAddress;
            $user->male_name = $request->male_name;
            $user->male_dob = date('Y-m-d', strtotime(str_replace('/', '-', $request->male_dob)));
            $user->male_time = $request->male_time;
            $user->male_country = $request->male_country;
            $user->male_city = $request->male_place;
            $user->male_latitude = $request->male_latitude;
            $user->male_longitude = $request->male_longitude;
            $user->male_timezone = $request->male_timezone;
            $user->female_name = $request->female_name;
            $user->female_dob = date('Y-m-d', strtotime(str_replace('/', '-', $request->female_dob)));
            $user->female_time = $request->female_time;
            $user->female_country = $request->female_country;
            $user->female_city = $request->female_place;
            $user->female_latitude = $request->female_latitude;
            $user->female_longitude = $request->female_longitude;
            $user->female_timezone = $request->female_timezone;
            $user->save();
        }

        $usersData = $request->input();
        $maledob = explode('/', $request->male_dob);
        $maletime = explode(':', $request->male_time);
        $femaledob = explode('/', $request->female_dob);
        $femaletime = explode(':', $request->female_time);

        $apiData = array(
            'm_day' =>  $maledob['0'],
            'm_month' =>  $maledob['1'],
            'm_year' =>  $maledob['2'],
            'm_hour' =>  $maletime['0'],
            'm_min' =>  $maletime['1'],
            'm_lat' =>  $request->male_latitude,
            'm_lon' =>  $request->male_longitude,
            'm_tzone' =>  $request->male_timezone,
            'f_day' =>  $femaledob['0'],
            'f_month' =>  $femaledob['1'],
            'f_year' =>  $femaledob['2'],
            'f_hour' =>  $femaletime['0'],
            'f_min' =>  $femaletime['1'],
            'f_lat' =>  $request->female_latitude,
            'f_lon' =>  $request->female_longitude,
            'f_tzone' =>  $request->female_timezone
        );

        $astroData = json_decode(ApiHelper::astroApi('https://json.astrologyapi.com/v1/match_astro_details', 'hi', $apiData), true);
        $birthData = json_decode(ApiHelper::astroApi('https://json.astrologyapi.com/v1/match_birth_details', 'hi', $apiData), true);
        // dd($astroData);
        if ($astroData == false && $birthData == false) {
            Toastr::error(translate('An error occured'));
            return back();
        } else {
            return view('web-views.kundali-milan.kundali-milan-detail', compact('apiData', 'usersData', 'astroData', 'birthData'));
        }
    }

    // panchang
    public function panchang()
    {
        $country = Country::all();
        $fastData = ApiHelper::GlobalGetApi(url('api/v1/astro/panchang-events?type=Vrat&year=' . date('Y') . '&month=' . date('m')));
        $festivalData = ApiHelper::GlobalGetApi(url('api/v1/astro/panchang-events?type=Festival&year=' . date('Y') . '&month=' . date('m')));
        $marriageMuhuratData = ApiHelper::GlobalGetApi(url('api/v1/astro/muhurat?type=marriage&month=' . date('F') . '&year=' . date('Y')));
        $vehicleMuhuratData = ApiHelper::GlobalGetApi(url('api/v1/astro/muhurat?type=vehicle%20purchase&month=' . date('F') . '&year=' . date('Y')));
        $grahpraveshMuhuratData = ApiHelper::GlobalGetApi(url('api/v1/astro/muhurat?type=griha%20pravesh&month=' . date('F') . '&year=' . date('Y')));
        $propertyMuhuratData = ApiHelper::GlobalGetApi(url('api/v1/astro/muhurat?type=property%20purchase&month=' . date('F') . '&year=' . date('Y')));
        $mundanMuhuratData = ApiHelper::GlobalGetApi(url('api/v1/astro/muhurat?type=mundan&month=' . date('F') . '&year=' . date('Y')));
        $annaprashanMuhuratData = ApiHelper::GlobalGetApi(url('api/v1/astro/muhurat?type=annaprashan&month=' . date('F') . '&year=' . date('Y')));
        $naamkaranMuhuratData = ApiHelper::GlobalGetApi(url('api/v1/astro/muhurat?type=namkaran&month=' . date('F') . '&year=' . date('Y')));
        $vidyarambhMuhuratData = ApiHelper::GlobalGetApi(url('api/v1/astro/muhurat?type=vidyarambh&month=' . date('F') . '&year=' . date('Y')));
        $karnavedhaMuhuratData = ApiHelper::GlobalGetApi(url('api/v1/astro/muhurat?type=karnavedha&month=' . date('F') . '&year=' . date('Y')));
        if ($fastData == false && $festivalData == false && $marriageMuhuratData == false && $vehicleMuhuratData == false && $grahpraveshMuhuratData == false && $propertyMuhuratData == false && $mundanMuhuratData == false && $annaprashanMuhuratData == false && $naamkaranMuhuratData == false && $vidyarambhMuhuratData == false && $karnavedhaMuhuratData == false) {
            Toastr::error(translate('An error occured'));
            return back();
        } else {
            return view('web-views.panchang.panchang', compact('country', 'fastData', 'festivalData', 'marriageMuhuratData', 'vehicleMuhuratData', 'grahpraveshMuhuratData', 'propertyMuhuratData', 'mundanMuhuratData', 'annaprashanMuhuratData', 'naamkaranMuhuratData', 'vidyarambhMuhuratData', 'karnavedhaMuhuratData'));
        }
    }
    // public function panchang(){
    //     $fastData = ApiHelper::GlobalGetApi(url('api/v1/astro/panchang-events?type=Vrat&year='.date('Y').'&month='.date('m')));
    //     $festivalData = ApiHelper::GlobalGetApi(url('api/v1/astro/panchang-events?type=Festival&year='.date('Y').'&month='.date('m')));
    //     $country = Country::all();
    //     return view('web-views.panchang.panchang',compact('country','fastData','festivalData'));
    // }

    public function ram_shalaka()
    {

        $ramShalaka = RamShalaka::all();

        return view('web-views.ramshalaka.ram-shalaka', compact('ramShalaka'));
    }

    // chaughadiya
    public function chaughadiya()
    {
        $country = Country::all();
        return view('web-views.chaughadiya.chaughadiya', compact('country'));
    }

    // all_puja
    public function all_puja()
    {
        return view('web-views.puja.all-puja');
    }


    // all_donate
    public function all_donate()
    {
        $categories = DonateCategory::where('type', 'category')->where('status', 1)->whereHas('donateTrusts', function ($query) {
            $query->where('status', 1)
                ->where('is_approve', 1);
        })->get();
        $purpose = DonateCategory::where('type', 'porpose')->where('status', 1)->get();
        $donateList = DonateAds::select('donate_ads.*', 'dc.slug as c_type', 'dp.name as p_type', DB::raw('"outdonate" as showvalue'), DB::raw("IFNULL(SUM(t.amount),0) as collected_amount"))->where(['donate_ads.is_approve' => 1, 'donate_ads.status' => 1])
            // ->leftJoin('donate_categories as dc', 'dc.id', '=', 'donate_ads.category_id')
            ->leftJoin('donate_categories as dc', function ($join) {
                $join->on('dc.id', '=', 'donate_ads.category_id')
                    ->where('dc.status', '=', 1);
            })
            ->leftJoin('donate_categories as dp', 'dp.id', '=', 'donate_ads.purpose_id')
            ->leftJoin('donate_all_transaction as t', function ($join) {
                $join->on('t.ads_id', '=', 'donate_ads.id')
                    ->where('t.type', 'donate_ads')
                    ->where('t.amount_status', 1);
            })
            // ->where('dc.status', 1)
            ->where(function ($q) {
                $q->whereNull('donate_ads.set_requirement_date_range')
                    ->orWhere('donate_ads.set_requirement_date_range', '')
                    ->orWhere(function ($sub) {
                        $sub->whereRaw(
                            "? BETWEEN 
                                STR_TO_DATE(SUBSTRING_INDEX(donate_ads.set_requirement_date_range, ' - ', 1), '%Y-%m-%d') 
                                AND 
                                STR_TO_DATE(SUBSTRING_INDEX(donate_ads.set_requirement_date_range, ' - ', -1), '%Y-%m-%d')",
                            [date('Y-m-d')]
                        );
                    });
            })
            ->where('dp.status', 1)
            ->groupBy('donate_ads.id', 'dc.slug', 'dp.name')
            ->havingRaw('(donate_ads.set_requirement_amount IS NULL 
             OR donate_ads.set_requirement_amount = 0 
             OR collected_amount < donate_ads.set_requirement_amount)')
            ->orderBy('donate_ads.id', 'desc')
            ->get();
        $trustDonate = DonateTrust::select('donate_trust.*', 'dc.slug as c_name')->where('donate_trust.is_approve', 1)->where('donate_trust.status', 1)->leftJoin('donate_categories as dc', 'dc.id', '=', 'donate_trust.category_id')->where('dc.status', 1)->orderBy('donate_trust.id', 'desc')->get();
        $donateinhouse = DonateAds::select('donate_ads.*', 'dp.slug as p_type', DB::raw('"indonate" as showvalue'), DB::raw("IFNULL(SUM(t.amount),0) as collected_amount"))->where(['donate_ads.is_approve' => 1, 'donate_ads.status' => 1])
            ->leftJoin('donate_categories as dc', function ($join) {
                $join->on('dc.id', '=', 'donate_ads.category_id')
                    ->where('dc.status', '=', 1);
            })
            ->leftJoin('donate_categories as dp', 'dp.id', '=', 'donate_ads.purpose_id')
            ->leftJoin('donate_all_transaction as t', function ($join) {
                $join->on('t.ads_id', '=', 'donate_ads.id')
                    ->where('t.type', 'donate_ads')
                    ->where('t.amount_status', 1);
            })
            ->where('dp.status', 1)
            ->where('donate_ads.type', 'inhouse')
            ->where(function ($q) {
                $q->whereNull('set_requirement_date_range')
                    ->orWhere('set_requirement_date_range', '')
                    ->orWhere(function ($sub) {
                        $sub->whereRaw(
                            "? BETWEEN 
                                STR_TO_DATE(SUBSTRING_INDEX(set_requirement_date_range, ' - ', 1), '%Y-%m-%d') 
                                AND 
                                STR_TO_DATE(SUBSTRING_INDEX(set_requirement_date_range, ' - ', -1), '%Y-%m-%d')",
                            [date('Y-m-d')]
                        );
                    });
            })
            ->where('dp.status', 1)
            ->groupBy('donate_ads.id', 'dc.slug', 'dp.name')
            ->havingRaw('(donate_ads.set_requirement_amount IS NULL 
             OR donate_ads.set_requirement_amount = 0 
             OR collected_amount < donate_ads.set_requirement_amount)')
            ->orderBy('donate_ads.id', 'desc')
            ->get();
        return view('web-views.donate.all-donate', compact('trustDonate', 'donateinhouse', 'categories', 'purpose', 'donateList'));
    }


    public function DonateAdsLeads(Request $request, $id)
    {
        $type = 'Donate';
        $donateList = DonateAds::select('donate_ads.*', 'dc.name as c_type', 'dp.name as p_type', 'dt.id as trust_id', 'dt.trust_name as trust_name', DB::raw('"outdonate" as showvalue'))->where(['donate_ads.is_approve' => 1, 'donate_ads.status' => 1])
            ->leftJoin('donate_categories as dc', function ($join) {
                $join->on('dc.id', '=', 'donate_ads.category_id')
                    ->where('dc.status', '=', 1);
            })
            ->leftJoin('donate_categories as dp', 'dp.id', '=', 'donate_ads.purpose_id')
            ->leftJoin('donate_trust as dt', 'dt.id', '=', 'donate_ads.trust_id')
            // ->where('dc.status', 1)
            ->where('dp.status', 1)
            // ->where('donate_ads.id', base64_decode($id))
            ->where('donate_ads.slug', ($id))
            // ->where('donate_ads.type', 'outsite')
            ->first();

        if (!$donateList) {
            return redirect()->to('/');
        }

        $trust_data = DonateTrust::where('id', $donateList['trust_id'])->first();
        if (!empty($trust_data)) {
            $trust_tans = $trust_data->translations()->pluck('value', 'key')->toArray();
        }
        $donateList['in_trust_name'] = ($trust_tans['trust_name'] ?? "");
        $donateList['en_trust_name'] = $donateList['trust_name'];

        $ids = $donateList['id'] ?? ""; //base64_decode($id);
        $images = getValidImage(path: 'storage/app/public/donate/ads/' . $donateList['image'], type: 'product');
        $countdonate = \App\Models\DonateAllTransaction::where('type', 'donate_ads')->where('ads_id', $ids)->where('amount_status', 1)->count();
        $faqs = \App\Models\FAQ::whereHas('Category', function ($query) {
            $query->where('name', 'donate');
        })->with('Category')->get();
        return view('web-views.donate.donate-information', compact('countdonate', 'images', 'ids', 'type', 'donateList', 'faqs'));
    }

    public function DonateAdsSaveLeads(Request $request, $id)
    {
        $userfind = User::where('phone', ($request->input('person_phone') ?? ""))->first();
        if ($userfind) {
            \Illuminate\Support\Facades\Auth::guard('customer')->loginUsingId($userfind['id']);
        } else {
            $user = new User();
            $user->phone = $request->input('person_phone');
            $user->name = $request->input('person_name');
            $user->f_name = (explode(" ", $request->input('person_name'))[0] ?? "");
            $user->l_name = (explode(" ", $request->input('person_name'))[1] ?? "");
            $user->email = $request->input('person_phone');
            $user->password =  bcrypt('12345678');
            $user->verify_otp = $request->input('verify_otp') ?? 1;
            $user->save();
            \Illuminate\Support\Facades\Auth::guard('customer')->loginUsingId($user->id);

            $data = [
                'customer_id' => ($user->id ?? "")
            ];
            Helpers::whatsappMessage('whatsapp', 'Welcome Message', $data);
        }
        if (auth('customer')->check()) {
            $leads = new DonateLeads();
            $leads->trust_id = $request['trust_id'] ?? 0;
            $leads->ads_id = $request['ads_id'];
            $leads->user_id = auth('customer')->id();
            $leads->frequency = $request['frequency'] ?? "one_time";
            $leads->status = 0;
            $leads->type = ((!empty($request['ads_id'])) ? "ads Donate" : 'donate trust');
            $leads->save();

            // return redirect()->route('donate-trust', [base64_encode(json_encode([$id, $leads->id, ((!empty($request['ads_id'])) ? "ads" : 'trust')]))]);
            if ($request['ads_id']) {
                $slugs =  DonateAds::where('id', $id)->first();
            } else {
                $slugs =  DonateTrust::where('id', $id)->first();
            }
            return redirect()->route('donate-trust', [$slugs['slug'], 'lead' => base64_encode($leads->id), 'type' => ((!empty($request['ads_id'])) ? "ads" : 'trust')]);
        } else {
            Toastr::error('Please Login');
        }
        return back();
    }

    public function DonateLeadUpdates(Request $request)
    {
        $amount = $request['amount'];
        $getLeads = DonateLeads::where('id', ($request['lead_id']))->first();
        $createInfo = [];
        if ($amount) {
            $createInfo[] = [
                'id' => '',
                'title' => 'plat amount',
                'subtitle' => '',
                'amount' => $amount,
                'qty' => 0,
                'fullamount' => ($amount),
            ];
        }
        $donateproductcart = $request['donateproductcart'];
        if (is_string($donateproductcart)) {
            $donateproductcart = json_decode($donateproductcart, true);
        }
        $donateproductcart = is_array($donateproductcart) ? $donateproductcart : [];
        if ($donateproductcart) {
            foreach ($donateproductcart as $key => $value) {
                if ($value['title'] ?? '') {
                    $createInfo[] = [
                        'id' => $value['id'],
                        'title' => $value['title'],
                        'subtitle' => $value['subtitle'],
                        'amount' => $value['amount'],
                        'qty' => $value['qty'],
                        'fullamount' => ($value['qty'] * $value['amount']),
                    ];
                }
            }
        }
        if ($getLeads) {
            $getLeads->amount = $amount;
            $getLeads->frequency = $request['frequency'] ?? "one_time";
            $getLeads->information = json_encode($createInfo);
            $getLeads->save();
        }
        return response()->json(['status' => 1, 'data' => []], 200);
    }

    public function DonateAds(Request $request, $id)
    {
        // $id = (json_decode(base64_decode($id)));
        $type = 'Donate';
        $leads_id = $request->lead;
        $leadId = base64_decode($request->lead);
        $images = [];
        if (isset($request->type) && $request->type == 'ads') {
            $donateList = DonateAds::select('donate_ads.*', 'dc.name as c_type', 'dp.name as p_type', 'dt.trust_name as trust_name', 'dt.gallery_image as trust_images', DB::raw('"outdonate" as showvalue'))->where(['donate_ads.is_approve' => 1, 'donate_ads.status' => 1])
                // ->leftJoin('donate_categories as dc', 'dc.id', '=', 'donate_ads.category_id')
                ->leftJoin('donate_categories as dc', function ($join) {
                    $join->on('dc.id', '=', 'donate_ads.category_id')
                        ->where('dc.status', '=', 1);
                })
                ->leftJoin('donate_categories as dp', 'dp.id', '=', 'donate_ads.purpose_id')
                ->leftJoin('donate_trust as dt', 'dt.id', '=', 'donate_ads.trust_id')
                // ->where('dc.status', 1)
                ->where('dp.status', 1)->where('donate_ads.slug', $id)->first();

            $trust_data = DonateTrust::where('id', $donateList['trust_id'])->first();
            if (!empty($trust_data)) {
                $trust_tans = $trust_data->translations()->pluck('value', 'key')->toArray();
            }
            $donateList['in_trust_name'] = ($trust_tans['trust_name'] ?? "");
            $donateList['en_trust_name'] = $donateList['trust_name'];

            $trust_images = json_decode($donateList['trust_images'], true);
            $random_image = '';
            if (!empty($trust_images)) {
                $random_key = array_rand($trust_images);
                $random_image = $trust_images[0]; //$trust_images[$random_key];
            }



            $images[] = getValidImage(path: 'storage/app/public/donate/ads/' . $donateList['image'], type: 'product');
            $trust_image = getValidImage(path: 'storage/app/public/donate/trust/' . $random_image, type: 'product');
        } else {
            $donateList = DonateTrust::select('donate_trust.*', DB::raw('"outdonate" as showvalue'))->leftJoin('donate_categories as dc', 'dc.id', '=', 'donate_trust.category_id')->where('dc.status', 1)->where('donate_trust.status', 1)->where('donate_trust.is_approve', 1)->where('donate_trust.slug', ($id))->first();
            // dd($donateList);
            $trust_images = json_decode($donateList['gallery_image'], true);
            $random_image = '';
            if (!empty($trust_images)) {
                $random_key = array_rand($trust_images);
                $random_image = $trust_images[0]; //$trust_images[$random_key];
                foreach ($trust_images as $img) {
                    $images[] = getValidImage(path: 'storage/app/public/donate/trust/' . $img, type: 'product');
                }
            }
            $trust_image = getValidImage(path: 'storage/app/public/donate/trust/' . $random_image, type: 'product');
            $donateList['trust_id'] = $donateList['id'];
            $donateList['id'] = '';
            $donateList['in_trust_name'] = $donateList['trust_name'];
            $donateList['en_trust_name'] = $donateList['trust_name'];
            $donateList['name'] = $donateList['name'];
            $donateList['type'] = 'outsite';
        }

        $customer = User::where('id', auth('customer')->id())->first();
        return view('web-views.donate.donate-given', compact('leadId', 'leads_id', 'customer', 'images', 'trust_image', 'type', 'donateList'));
    }

    public function DonateRequest(Request $request)
    {
        $getLeads = DonateLeads::with(['users'])->where('id', base64_decode($request['leads_id']))->where('status', 0)->first();
        if (!$getLeads) {
            Toastr::error(translate("Donate Invalid Data Pass"));
            return redirect()->route('all-donate');
        }
        $frequency = ($getLeads['frequency'] ?? "one_time");
        $subscription_id = '';

        $user_id = $getLeads['user_id'];
        $payment_method = 'razor_pay';
        $payment_platform = 'web';
        $external_redirect_link = route('donate-web-payment');
        $ads_id = $getLeads['ads_id'];
        $trust_id = $getLeads['trust_id'];
        $payment_amount = $final_amount = $getLeads['amount'];
        $admin_commission = 0;
        $trustData = DonateTrust::where('id', $trust_id)->first();
        $AdsData = DonateAds::where('id', $getLeads['ads_id'])->first();
        if (!empty($AdsData)) {
            $inoutcheckads = $AdsData['type'];
            $set_types = $AdsData['set_type'];
        } else {
            $set_types = 0;
            $inoutcheckads = 'outsite';
        }
        if ($inoutcheckads == 'outsite') {
            if (!empty($ads_id) && $ads_id > 0) {
                if (!empty($AdsData) && isset($AdsData['admin_commission']) && $AdsData['admin_commission'] > 0) {
                    $admin_commission = (($payment_amount * $AdsData['admin_commission']) / 100);
                    $final_amount = ($payment_amount - $admin_commission);
                } else {
                    $admin_commission = (($payment_amount * $trustData['ad_commission']) / 100);
                    $final_amount = ($payment_amount - $admin_commission);
                }
            } else {
                $admin_commission = (($payment_amount * $trustData['donate_commission']) / 100);
                $final_amount = ($payment_amount - $admin_commission);
            }
        }
        $information = '';
        if ($set_types == 1) {
            $information = json_encode(['qty' => $request->set_qty, 'total_amount' => $payment_amount]);
        } else {
            $information = json_encode(['qty' => '', 'total_amount' => $payment_amount]);
        }

        $transaction = new DonateAllTransaction();
        $transaction->type = (($ads_id) ? 'donate_ads' : 'donate_trust');
        $transaction->user_id = $user_id;
        $transaction->trust_id = $trust_id;
        $transaction->ads_id = $ads_id;
        $transaction->amount = $payment_amount;
        $transaction->admin_commission =  $admin_commission;
        $transaction->final_amount =  $final_amount;
        $transaction->amount_status = 0;
        $transaction->information = $getLeads['information'];
        $transaction->frequency = $frequency;
        $transaction->platform = 'web';
        $transaction->save();

        if (in_array($frequency, ['weekly', 'monthly', 'quarterly', 'yearly'])) {
            $subscription_id = \App\Http\Controllers\Customer\PaymentController::createSubscriptionPlan($frequency, $getLeads['amount'], $getLeads['user_id'], $transaction->id);
            $transaction->subscription_id = $subscription_id['id'];
            $transaction->save();
            return view('web-views.donate.subscription.custom_checkout', [
                'subscription_id' => $subscription_id['id'],
                'customeremail'   => $getLeads['users']['email'] ?? "",
                'customername'   => $getLeads['users']['name'] ?? "",
                'customerphone'   => $getLeads['users']['phone'] ?? "",
                'amount'          => ($subscription_id['plan']->item->amount / 100),
                "RAZORPAY_KEY" => config('razor_config.api_key'),
                "lead_id" => $transaction->id,
                "business_name" => '',
            ]);
        }

        if ($request->wallet_type == 1) {
            $user = User::where('id', $user_id)->first();
            if ($user['wallet_balance'] >= $payment_amount) {
                User::where('id', $user['id'])->update(['wallet_balance' => DB::raw('wallet_balance - ' . $payment_amount)]);
                $findData =  DonateAllTransaction::where('id', $transaction->id)->first();
                $array['transaction_id'] = 'wallet';
                $array['amount_status'] = 1;
                DonateAllTransaction::where('id', $findData['id'])->update($array);
                $gettrust = DonateTrust::where('id', $findData['trust_id'])->first();
                if ($gettrust) {
                    DonateTrust::where('id', $findData['trust_id'])->update(['trust_total_amount' => ($gettrust['trust_total_amount'] + $findData['final_amount']), 'admin_commission' => ($gettrust['admin_commission'] + $findData['admin_commission'])]);
                }
                $adsTrust = DonateAds::where('id', $findData['ads_id'])->first();
                if ($adsTrust) {
                    DonateAds::where('id', $findData['ads_id'])->update(['total_amount_ads' => ($adsTrust['total_amount_ads'] + $findData['final_amount']), 'admin_commission_amount' => ($adsTrust['admin_commission_amount'] + $findData['admin_commission'])]);
                }

                if (isset($request['leads_id']) && !empty($request['leads_id'])) {
                    DonateLeads::where('id', $request['leads_id'])->update(['status' => 1]);
                }
                $wallet_transaction = new \App\Models\WalletTransaction();
                $wallet_transaction->user_id = $user['id'];;
                $wallet_transaction->transaction_id = \Illuminate\Support\Str::uuid();
                $wallet_transaction->reference = 'Donate order';
                $wallet_transaction->transaction_type = 'donate_order';
                $wallet_transaction->balance = User::where('id', $user['id'])->first()['wallet_balance'];
                $wallet_transaction->debit = $payment_amount;
                $wallet_transaction->save();

                $message_data['title_name'] = ((!empty($gettrust) && !empty($gettrust['trust_name'])) ? $gettrust['trust_name'] : 'Mahakal');
                $message_data['ad_name'] = ((!empty($adsTrust) && !empty($adsTrust['name'])) ? $adsTrust['name'] : '');
                $message_data['final_amount'] = webCurrencyConverter(amount: (float)$findData['amount'] ?? 0);
                $message_data['customer_id'] =  $findData['user_id'];

                $orderData = DonateAllTransaction::where('id', $findData['id'])->where('user_id',  $findData['user_id'])->with(['users', 'getTrust', 'adsTrust'])->first();
                $message_data['person_phone'] =  $orderData['user_phone'];
                $message_data['pan_card'] =  $request['pan_card'] ?? '';
                $mpdf_view = \View::make('web-views.donate.invoice', compact('orderData'));
                Helpers::gen_mpdf_Pdf($mpdf_view, 'donate_order', $findData['id']);
                $message_data['attachment'] = asset('storage/app/public/donate/invoice/donate_order' . $findData['id'] . '.pdf');
                $message_data['type'] = 'text-with-media';
                Helpers::whatsappMessage('donate', 'Donation Success', $message_data);

                $orderData = DonateAllTransaction::where('id', $findData['id'])->with(['getTrust', 'adsTrust'])->first();
                $message_data2['trust_name'] =  $orderData['getTrust']['trust_name'] ?? "Mahakal.com";
                $message_data2['ad_name'] =  $orderData['adsTrust']['name'] ?? '';
                $message_data2['booking_date'] =  date('d M,Y H:i A', strtotime($orderData['created_at']));
                $message_data2['order_amount'] =  $orderData['amount'];
                $message_data2['admin_commission'] =  $orderData['admin_commission'];
                $message_data2['final_amount'] =  $orderData['final_amount'];
                $message_data2['vendor_email'] =   $orderData['getTrust']['trust_email'] ?? "Mahakal.com";
                $message_data2['seller_id'] = \App\Models\Seller::where('relation_id', $orderData['trust_id'])->where('type', 'trust')->first()['id'] ?? 0;
                Helpers::whatsappMessage('donate', 'donation_trust_receipt', $message_data2);

                return redirect()->route('donate-success', [$findData['id']]);
            } else {
                // wallet dedication
                $user = User::where('id', $user_id)->first();
                $wallet_amount = ($user['wallet_balance']);
                $total_amount = $payment_amount;
                $onlinepay = ($payment_amount - $user['wallet_balance']);
                $data = [
                    'additional_data' => [
                        'business_name' => BusinessSetting::where(['type' => 'company_name'])->first()->value,
                        'business_logo' => asset('storage/app/public/company') . '/' . Helpers::get_business_settings('company_web_logo'),
                        'payment_mode' => 'web',
                        'leads_id' => $request->leads_id,
                        'trust_id' => $trust_id,
                        'ads_id' => $ads_id,
                        'transaction_id' => $transaction->id,
                        'customer_id' => $user['id'],
                        "user_name" => $user['name'],
                        "user_email" => $user['email'],
                        "user_phone" => $user['phone'],
                        'total_amount' => $total_amount,
                        'wallet_amount' => $wallet_amount,
                        "online_pay" => $onlinepay,
                        'page_name' => 'donate_order',
                        'success_url' => route('donate-success', [$transaction->id]),
                    ],
                    'user_id' => $user['id'],
                    'payment_method' => $payment_method,
                    'payment_platform' => 'web',
                    'payment_amount' => $onlinepay,
                    'attribute' => "Donate Order",
                    'external_redirect_link' => route('all-pay-wallet-payment-success', [$transaction->id]),
                ];
                $url_open = \App\Http\Controllers\Customer\PaymentController::Wallet_amount_add($data);
                DB::commit();
                return redirect($url_open);
            }
        }
        $currency_model = Helpers::get_business_settings('currency_model');
        if ($currency_model == 'multi_currency') {
            $default_currency = Currency::find(Helpers::get_business_settings('system_default_currency'));
            $currency_code = $default_currency['code'];
            $current_currency = $request->current_currency_code ?? session('currency_code');
        } else {
            $default = BusinessSetting::where(['type' => 'system_default_currency'])->first()->value;
            $currency_code = Currency::find($default)->code;
            $current_currency = $currency_code;
        }

        $additional_data = [
            'business_name' => BusinessSetting::where(['type' => 'company_name'])->first()->value,
            'business_logo' => asset('storage/app/public/company') . '/' . Helpers::get_business_settings('company_web_logo'),
            'payment_mode' => 'web',
            'leads_id' => $request->leads_id,
            'trust_id' => $trust_id,
            'ads_id' => $ads_id,
            'transaction_id' => $transaction->id,
        ];

        $customer = User::where('id', $user_id)->first();

        $additional_data['customer_id'] = $customer['id'];
        $additional_data['payment_request_from'] = 'web';

        $payer = new Payer(
            $customer->f_name . ' ' . $customer->l_name,
            $customer['email'],
            $customer->phone,
            ''
        );
        $payment_info = new PaymentInfo(
            success_hook: 'digital_payment_success_custom',
            failure_hook: 'digital_payment_fail',
            currency_code: $currency_code,
            payment_method: $payment_method,
            payment_platform: 'web',
            payer_id: $customer['id'],
            receiver_id: '100',
            additional_data: $additional_data,
            payment_amount: Convert::usdPaymentModule($payment_amount, $current_currency),
            external_redirect_link: $external_redirect_link,
            attribute: 'Donate',
            attribute_id: idate("U")
        );

        $receiver_info = new Receiver('receiver_name', 'example.png');

        $redirect_link = Payment::generate_link($payer, $payment_info, $receiver_info);
        $parsed_url = parse_url($redirect_link);
        $query_string = $parsed_url['query'];
        parse_str($query_string, $query_params);
        DonateAllTransaction::where('id', $transaction->id)->update(['payment_requests_id' => $query_params['payment_id'], 'transction_link' => $redirect_link]);
        return redirect($redirect_link);
    }

    public function Donatesuccesspage(Request $request, $id)
    {
        $type = $request->get('success');
        $customer = User::where('id', auth('customer')->id())->first();
        return view('web-views.donate.donate-success-page', compact('id', 'type', 'customer'));
    }

    public function DonateSubmit(Request $request)
    {
        $transaction = [
            'user_name' => $request['user_name'],
            'user_phone' => $request['person_phone'],
            'pan_card' => ((preg_match('/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/', strtoupper($request['pan_card']))) ? strtoupper($request['pan_card']) : ''),
        ];
        if(DonateAllTransaction::where('id', $request['id'])->where('user_name','=',null)->exists()){
            DonateAllTransaction::where('id', $request['id'])->update(['user_name' => $request['user_name'],'user_phone' => $request['person_phone']]);
        }
        if (preg_match('/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/', strtoupper($request['pan_card'])) && \App\Models\UserPanCardVerified::where('pan_number', strtoupper($request['pan_card']))->exists()) {
            $message_data['customer_id'] =  $request['user_id'];
            $message_data['person_phone'] =  $request['person_phone'];
            $message_data['pan_card'] =  strtoupper($request['pan_card']);
            $orderData = DonateAllTransaction::where('id', $request['id'])->where('user_id', $request['user_id'])->with(['users', 'getTrust', 'adsTrust'])->first();
            $message_data['attachment'] = asset('storage/app/public/donate/invoice/donate_order' . $request['id'] . '.pdf');
            if (empty($orderData['ertiga_certificate'] ?? '')) {
                DonateAllTransaction::where('id', $request['id'])->update($transaction);
                \App\Http\Controllers\RestAPI\v1\DonateController::create_donate_cetificate($request['id']);
                $message_data['attachment']  = getValidImage(path: 'storage/app/public/donate/certificate/' . '80g_' . $orderData['trans_id'] . '.jpg', type: 'product');
            } else {
                $message_data['attachment']  = getValidImage(path: 'storage/app/public/donate/certificate/' . ($orderData['ertiga_certificate'] ?? ''), type: 'product');
            }
            $message_data['type'] = 'text-with-media';
            Helpers::whatsappMessage('donate', 'Donation Success pdf', $message_data);
        }
        return redirect()->route('donate-success', [$request['id'], 'success' => 'success']);
    }

    public function TrustPujaSuccess(Request $request)
    {
        $order = TrustPujaOrder::where('id', $request['id'])->first();
        if ($order) {
            return view('web-views.donate.trust-puja-order-success', compact('order'));
        }
        return view('web-views.donate.trust-puja-order-success', compact('order'));
    }

    public function DonateTrust(Request $request, $id)
    {
        $customer = User::where('id', auth('customer')->id())->first();
        $type = 'Donate';
        $donateList = DonateTrust::select('donate_trust.*', DB::raw('"outdonate" as showvalue'))
            ->leftJoin('donate_categories as dc', 'dc.id', '=', 'donate_trust.category_id')
            ->where('dc.status', 1)
            ->where('donate_trust.status', 1)
            ->where('donate_trust.is_approve', 1)
            // ->where('donate_trust.id', base64_decode($id))
            ->where('donate_trust.slug', ($id))
            ->first();
        if (!$donateList) {
            return redirect()->to('/');
        }
        $trust_images = json_decode($donateList['gallery_image'], true);
        $random_image = '';
        if (!empty($trust_images)) {
            $random_key = array_rand($trust_images);
            $random_image = $trust_images[$random_key];
        }
        $images = $trust_image = getValidImage(path: 'storage/app/public/donate/trust/' . $random_image, type: 'product');
        $ids = $donateList['id'] ?? ""; //base64_decode($id);
        $donateList['trust_id'] = $donateList['id'];
        $donateList['in_trust_name'] = $donateList['trust_name'];
        $donateList['en_trust_name'] = $donateList['trust_name'];
        $donateList['id'] = '';
        $countdonate = \App\Models\DonateAllTransaction::where('type', 'donate_trust')->where('trust_id', $ids)->where('amount_status', 1)->count();
        $faqs = \App\Models\FAQ::whereHas('Category', function ($query) {
            $query->where('name', 'donate');
        })->with('Category')->get();
        return view('web-views.donate.donate-information', compact('countdonate', 'faqs', 'images', 'ids', 'type', 'donateList'));
    }

    public function DonateInvoice($id)
    {
        $orderData = DonateAllTransaction::where('id', $id)->with(['users', 'getTrust', 'adsTrust'])->first();
        if (empty($orderData)) {
            return back();
        }
        $mpdf_view = \View::make('web-views.donate.invoice', compact('orderData'));
        Helpers::gen_mpdf($mpdf_view, 'donate_order_', $id);
    }

    // astrology_counseling
    public function all_astrology_counseling()
    {
        return view('web-views.astrologyCounseling.all-astrology-counseling');
    }

    // darshan
    public function darshan()
    {
        return view('web-views.darshan.darshan-page');
    }

    // darshan_detail
    public function darshan_detail()
    {
        return view('web-views.darshan.darshan-detail-page');
    }


    public function DarshanList(Request $request)
    {
        $categoryList = TempleCategory::where('status', 1)->get();
        $trustIds = Temple::whereNotNull('trust_id')
            ->distinct()
            ->pluck('trust_id')
            ->toArray();
        $fieldList = !empty($trustIds) ? implode(',', $trustIds) : null;
        if ($request->get('search')) {
            $searchValue = $request->get('search');
            $templeList = Temple::with(['category', 'galleries', 'states', 'cities', 'country', 'translations'])
                ->when($searchValue, function ($query) use ($searchValue) {
                    $query->orWhere('name', 'like', "%$searchValue%");
                    $query->orWhereHas('translations', function ($q) use ($searchValue) {
                        $q->where('key', "name");
                        $q->where('value', 'like', "%$searchValue%");
                    });
                    $query->orwhereHas('cities', function ($q1) use ($searchValue) {
                        $q1->where('city', 'like', "%$searchValue%");
                    });
                    $query->orWhereHas('states', function ($q3) use ($searchValue) {
                        $q3->where('name', 'like', "%$searchValue%");
                    });
                    $query->orWhereHas('country', function ($q4) use ($searchValue) {
                        $q4->where('name', 'like', "%$searchValue%");
                    });
                })
                ->orderByRaw("FIELD(trust_id, $fieldList) = 0, FIELD(trust_id, $fieldList), trust_id IS NULL, id DESC")
                ->where('status', 1)->get();
        } else {
            $templeList = Temple::where('status', 1)->with(['category', 'galleries', 'cities', 'states', 'country'])
                ->orderByRaw("FIELD(trust_id, $fieldList) = 0, FIELD(trust_id, $fieldList), trust_id IS NULL, id DESC")->get();
        }
        return view('web-views.darshan.darshan-list', compact('categoryList', 'templeList'));
    }

    public function TempleDetails(Request $request, $slug)
    {
        $templeList = Temple::where('status', 1)->where('slug', $slug)->with(['category', 'galleries2', 'states'])->first();
        // $review = TempleReview::where('status', 1)->where('temple_id', ($templeList['id'] ?? ""))->with('userData');
        $review = TempleReview::where('status', 1)->with('userData');
        if (!$templeList) {
            return back();
        }
        if (!empty($templeList['latitude']) && !empty($templeList['longitude'])) {
            $nearbyTemple = Temple::where('status', 1)->with('galleries')->withinRadius($templeList['latitude'], $templeList['longitude'], 200)->get();
            $nearbyCities = Cities::where('status', 1)->withinRadius($templeList['latitude'], $templeList['longitude'], 200)->get();
            $nearbyHotels = Hotels::where('status', 1)->withinRadius($templeList['latitude'], $templeList['longitude'], 50)->get();
            $nearbyRestaurant = Restaurant::where('status', 1)->withinRadius($templeList['latitude'], $templeList['longitude'], 50)->get();
        } else {
            $nearbyTemple = [];
            $nearbyCities = [];
            $nearbyHotels = [];
            $nearbyRestaurant = [];
        }
        $ratings = ['total' => $review->avg('star'), 'alluser' => $review->get(), 'list' => $review->orderBy('id', 'desc')->limit('10')->get()];
        return view('web-views.darshan.temple-details', compact('nearbyTemple', 'nearbyCities', 'nearbyHotels', 'nearbyRestaurant', 'templeList', 'ratings'));
    }

    public function VipDarshanLead(Request $request)
    {

        $userfind = User::where('phone', $request->input('person_phone'))->first();
        if ($userfind) {
            \Illuminate\Support\Facades\Auth::guard('customer')->loginUsingId($userfind['id']);
        } else {
            $user = new User();
            $user->phone = $request->input('person_phone');
            $user->name = $request->input('person_name');
            $user->f_name = (explode(" ", $request->input('person_name'))[0] ?? "");
            $user->l_name = (explode(" ", $request->input('person_name'))[1] ?? "");
            $user->email = $request->input('person_phone');
            $user->password =  bcrypt('12345678');
            $user->verify_otp = $request->input('verify_otp') ?? 1;
            $user->save();
            \Illuminate\Support\Facades\Auth::guard('customer')->loginUsingId($user->id);

            $data = [
                'customer_id' => ($user->id ?? "")
            ];
            Helpers::whatsappMessage('whatsapp', 'Welcome Message', $data);
        }
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'temple_id' => 'required',
            'price' => 'required|min:0',
            'package_name' => 'required',
            'name' => 'required',
            'person_phone' => 'required',
            'person_name' => 'required',
        ]);

        if (auth('customer')->check()) {
            $leads = new TempleDarshanLead(); //new instance
            $leads->user_id = auth('customer')->id();
            $leads->name = $request['person_name'];
            $leads->phone = $request['person_phone'];
            $leads->temple_id = $request['temple_id'];
            $purohits = Purohit::where('temple_id', $request['temple_id'])->pluck('id')->toArray();
            if (count($purohits) > 0) {
                $lastOrder = DarshanOrder::where('temple_id', $request['temple_id'])->orderBy('id', 'desc')->first();
                if ($lastOrder) {
                    $lastIndex = array_search($lastOrder->purohit_id, $purohits);
                    $nextIndex = ($lastIndex === false) ? 0 : ($lastIndex + 1) % count($purohits);
                    $purohitId = $purohits[$nextIndex];
                } else {
                    $purohitId = $purohits[0];
                }
                $leads->purohit_id = $purohitId;
            }
            $leads->package_id = $request['id'];
            $leads->title = $request['name'];
            $leads->package_name = $request['package_name'];
            $leads->price = $request['price'] ?? 0;
            $leads->receipt_price = $request['receipt_price'] ?? 0;
            $leads->platform_fee  = $request['platform_fee'] ?? 0;
            $leads->platform_base_price  = $request['platform_base'] ?? 0;
            $leads->platform_gst  = $request['platform_gst'] ?? 0;
            $leads->status = 0;
            $leads->save();
            $slugs = Temple::where('id', $request['temple_id'])->first();
            return redirect()->route('vip-darshan-booking', [$slugs['slug'], 'lead' => base64_encode($leads->id)]);
        } else {
            Toastr::error('Please Login');
        }
        return back();
    }

    public function VipDarshanDetails(Request $request)
    {
        $getData = Temple::where('slug', $request['slug'])->with('galleries2')->first();
        $templeLead = TempleDarshanLead::where('id', base64_decode($request['lead']))->where('status', 0)->first();
        if ($getData && $templeLead && $getData['vip_plans'] && json_decode($getData['vip_plans'], true)) {
            $review = TempleReview::where('status', 1)->where('temple_id', ($getData['id'] ?? ""))->with('userData');
            $ratings = ['total' => $review->avg('star'), 'list' => $review->get()];
            return view('web-views.darshan.vip-darshan-booking', compact('getData', 'templeLead', 'ratings'));
        } else {
            Toastr::error('Invalid Data');
        }
        return back();
    }

    public function VipDarshanLeadUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lead_id' => 'required',
            'information' => 'required',
        ]);
        $getTemple = TempleDarshanLead::where('id', $request->lead_id)->with(['Temple'])->first();
        $info = json_decode($request->information, true);
        if ($validator->fails() || !is_array($info)) {
            return redirect()->route('vip-darshan-booking', ["slug" => $getTemple['Temple']['slug'], "lead" => base64_encode($request->lead_id)])->withErrors($validator)->withInput();
        }
        if (empty($info['date']) || empty($info['time']) || (0 > $info['price'])) {
            return redirect()->route('vip-darshan-booking', ["slug" => $getTemple['Temple']['slug'], "lead" => base64_encode($request->lead_id)])->withErrors(['information' => 'Please select date, time and price'])->withInput();
        }
        if (1 > $info['price']) {
            $getTemple->price = $info['price'];
        }
        $getTemple->date = $info['date'];
        $getTemple->time = $info['time'];
        $getTemple->save();
        return redirect()->route('vip-darshan-booking-pay', ["slug" => $getTemple['Temple']['slug'], "lead" => base64_encode($request->lead_id)]);
    }

    public function VipDarshanBookings(Request $request)
    {
        $getData = Temple::where('slug', $request['slug'])->with(['galleries2', 'states', 'cities'])->first();
        $templeLead = TempleDarshanLead::with(['Temple'])->where('id', base64_decode($request['lead']))->where('status', 0)->first();
        if ($getData && $templeLead && $getData['vip_plans'] && json_decode($getData['vip_plans'], true)) {
            $memberList = DarshanOrderMembers::whereHas('darshanOrder', function ($query) {
                $query->where('user_id', auth('customer')->id());
            })
                ->where('aadhar_verify_status', 1)->orderBy('id', 'desc')->groupBy('aadhar')
                ->get();
            return view('web-views.darshan.vip-darshan-booking-pay', compact('getData', 'templeLead', 'memberList'));
        } else {
            Toastr::error('Invalid Data');
        }
        return redirect()->route('temple-details', ["slug" => $getData['slug']]);
    }

    public function VipDarshanUpdatePersons(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lead_id' => 'required',
            "people" => "required",
            'price' => "required",
        ]);
        $getTemple = \App\Models\TempleDarshanLead::where('id', $request->lead_id)->with(['Temple'])->first();
        if ($getTemple && $request['people'] && json_decode($request['people'], true)) {
            $personData = json_decode($request['people'], true);
            $getTemple->price = $request['price'];
            $getTemple->receipt_price = $request['receipt_price'];
            $getTemple->platform_fee = $request['platform_fee'];
            $getTemple->platform_base_price = $request['platform_base_price'];
            $getTemple->platform_gst = $request['platform_gst'];
            $getTemple->people_qty = count($personData);
            $getTemple->people_info = json_encode($personData);
            $getTemple->save();
            return response()->json(['status' => 1, 'message' => 'Unable to place order'], 200);
        }
        return response()->json(['status' => 0, 'message' => 'Not Found'], 200);
    }

    public function VipDarshanBookSuccess(Request $request)
    {
        $getData = Temple::where('slug', $request['slug'])->first();
        if ($getData) {
            return view('web-views.darshan.vip-darshan-booking-success', compact('getData'));
        } else {
            Toastr::error('Invalid Data');
        }
        return redirect()->to(url('/'));
    }

    public function TempleAddComment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'rating' => 'required|numeric|between:1,5',
            'comment' => 'required|min:1',
        ], [
            'rating.required' => 'star is Empty!',
            'comment.required' => 'Comment is Empty!',
        ]);
        $getTemple = Temple::where('id', $request->temple_id)->first();
        if ($validator->fails()) {
            return redirect()->route('temple-details', [$getTemple['slug'], "comment" => "error"])->withErrors($validator)->withInput();
        }

        $contact = TempleReview::where('user_id', auth('customer')->id())
            ->where('temple_id', $request->temple_id)
            ->first();

        if (!$contact) {
            $contact = new TempleReview();
            $contact->user_id = auth('customer')->id();
            $contact->temple_id = $request->temple_id;
        }
        $contact->comment = $request->comment;
        $contact->star = $request->rating;
        $contact->save();

        return redirect()->route('temple-details', [$getTemple['slug'], "comment" => "success"]);
    }

    public function NearHotelDetails(Request $request, $id)
    {
        $hotelData = Hotels::where('status', 1)->where('id', base64_decode($id))->with(['country', 'cities', 'states'])->first();
        $review = HotelReview::where('status', 1)->where('hotel_id', base64_decode($id))->with('userData');
        if (!$hotelData) {
            return back();
        }
        if (!empty($hotelData['latitude']) && !empty($hotelData['longitude'])) {
            $nearbyTemple = Temple::where('status', 1)->with('galleries')->withinRadius($hotelData['latitude'], $hotelData['longitude'], 200)->get();
            $nearbyCities = Cities::where('status', 1)->withinRadius($hotelData['latitude'], $hotelData['longitude'], 200)->get();
            $nearbyHotels = Hotels::where('status', 1)->withinRadius($hotelData['latitude'], $hotelData['longitude'], 50)->get();
            $nearbyRestaurant = Restaurant::where('status', 1)->withinRadius($hotelData['latitude'], $hotelData['longitude'], 50)->get();
        } else {
            $nearbyTemple = [];
            $nearbyCities = [];
            $nearbyHotels = [];
            $nearbyRestaurant = [];
        }
        $ratings = ['total' => $review->avg('star'), 'list' => $review->get()];
        return view('web-views.darshan.hotel-details', compact('nearbyTemple', 'nearbyCities', 'nearbyHotels', 'nearbyRestaurant', 'hotelData', 'ratings'));
    }

    public function HotelAddComment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'rating' => 'required|numeric|between:1,5',
            'comment' => 'required|min:1',
        ], [
            'rating.required' => 'star is Empty!',
            'comment.required' => 'Comment is Empty!',
        ]);
        $getRestaurant = Hotels::where('id', $request->hotel_id)->first();
        if ($validator->fails()) {
            return redirect()->route('near-hotel', [base64_encode($getRestaurant['id']), "comment" => "error"])->withErrors($validator)->withInput();
        }
        $contact = HotelReview::where('user_id', auth('customer')->id())
            ->where('hotel_id', $request->hotel_id)
            ->first();

        if (!$contact) {
            $contact = new HotelReview();
            $contact->user_id = auth('customer')->id();
            $contact->hotel_id = $request->hotel_id;
        }
        $contact->comment = $request->comment;
        $contact->star = $request->rating;
        $contact->save();

        return redirect()->route('near-hotel', [base64_encode($getRestaurant['id']), "comment" => "success"]);
    }

    public function NearRestaurantDetails(Request $request, $id)
    {
        $restaurantData = Restaurant::where('status', 1)->where('id', base64_decode($id))->with(['country', 'cities', 'states'])->first();
        $review = RestaurantReview::where('status', 1)->where('restaurant_id', base64_decode($id))->with('userData');
        if (!$restaurantData) {
            return back();
        }
        if (!empty($restaurantData['latitude']) && !empty($restaurantData['longitude'])) {
            $nearbyTemple = Temple::where('status', 1)->with('galleries')->withinRadius($restaurantData['latitude'], $restaurantData['longitude'], 200)->get();
            $nearbyCities = Cities::where('status', 1)->withinRadius($restaurantData['latitude'], $restaurantData['longitude'], 200)->get();
            $nearbyHotels = Hotels::where('status', 1)->withinRadius($restaurantData['latitude'], $restaurantData['longitude'], 50)->get();
            $nearbyRestaurant = Restaurant::where('status', 1)->withinRadius($restaurantData['latitude'], $restaurantData['longitude'], 50)->get();
        } else {
            $nearbyTemple = [];
            $nearbyCities = [];
            $nearbyHotels = [];
            $nearbyRestaurant = [];
        }
        $ratings = ['total' => $review->avg('star'), 'list' => $review->get()];
        return view('web-views.darshan.restaurant-details', compact('nearbyTemple', 'nearbyCities', 'nearbyHotels', 'nearbyRestaurant', 'restaurantData', 'ratings'));
    }

    public function RestaurantAddComment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'rating' => 'required|numeric|between:1,5',
            'comment' => 'required|min:1',
        ], [
            'rating.required' => 'star is Empty!',
            'comment.required' => 'Comment is Empty!',
        ]);
        $getRestaurant = Restaurant::where('id', $request->restaurant_id)->first();
        if ($validator->fails()) {
            return redirect()->route('near-restaurant', [base64_encode($getRestaurant['id']), "comment" => "error"])->withErrors($validator)->withInput();
        }

        $contact = RestaurantReview::where('user_id', auth('customer')->id())
            ->where('restaurant_id', $request->restaurant_id)
            ->first();

        if (!$contact) {
            $contact = new RestaurantReview();
            $contact->user_id = auth('customer')->id();
            $contact->restaurant_id = $request->restaurant_id;
        }
        $contact->comment = $request->comment;
        $contact->star = $request->rating;
        $contact->save();
        return redirect()->route('near-restaurant', [base64_encode($getRestaurant['id']), "comment" => "success"]);
    }

    public function NearCitiesDetails(Request $request, $id)
    {
        $citiesData = Cities::where('status', 1)->where('id', base64_decode($id))->with(['visits', 'country', 'states'])->first();
        $review = CitiesReview::where('status', 1)->where('cities_id', base64_decode($id))->with('userData');
        if (!$citiesData) {
            return back();
        }
        if (!empty($citiesData['latitude']) && !empty($citiesData['longitude'])) {
            $nearbyTemple = Temple::where('status', 1)->with('galleries')->withinRadius($citiesData['latitude'], $citiesData['longitude'], 200)->get();
            $nearbyCities = Cities::where('status', 1)->withinRadius($citiesData['latitude'], $citiesData['longitude'], 200)->get();
            $nearbyHotels = Hotels::where('status', 1)->withinRadius($citiesData['latitude'], $citiesData['longitude'], 50)->get();
            $nearbyRestaurant = Restaurant::where('status', 1)->withinRadius($citiesData['latitude'], $citiesData['longitude'], 50)->get();
        } else {
            $nearbyTemple = [];
            $nearbyCities = [];
            $nearbyHotels = [];
            $nearbyRestaurant = [];
        }
        $ratings = ['total' => $review->avg('star'), 'list' => $review->get()];
        return view('web-views.darshan.cities-details', compact('nearbyTemple', 'nearbyCities', 'nearbyHotels', 'nearbyRestaurant', 'citiesData', 'ratings'));
    }

    public function CitiesAddComment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'rating' => 'required|numeric|between:1,5',
            'comment' => 'required|min:1',
        ], [
            'rating.required' => 'star is Empty!',
            'comment.required' => 'Comment is Empty!',
        ]);
        $getcities = Cities::where('id', $request->cities_id)->first();
        if ($validator->fails()) {
            return redirect()->route('near-cities', [base64_encode($getcities['id']), "comment" => "error"])->withErrors($validator)->withInput();
        }

        $contact = CitiesReview::where('user_id', auth('customer')->id())
            ->where('cities_id', $request->cities_id)
            ->first();

        if (!$contact) {
            $contact = new CitiesReview();
            $contact->user_id = auth('customer')->id();
            $contact->cities_id = $request->cities_id;
        }
        $contact->comment = $request->comment;
        $contact->star = $request->rating;
        $contact->save();
        return redirect()->route('near-cities', [base64_encode($getcities['id']), "comment" => "success"]);
    }


    public function registerCreate()
    {
        $categories = AstrologerCategory::where('status', 1)->get();
        $skills = Skills::where('status', 1)->get();
        return view('web-views.astrologer.create', compact('categories', 'skills'));
    }

    // astrologer check email
    public function checkEmail($email)
    {
        $checkEmail = Astrologer::where('email', $email)->exists();
        if ($checkEmail) {
            return response(['status' => 200]);
        }
        return response(['status' => 400]);
    }

    // astrologer check mobile
    public function checkMobile($mobileno)
    {
        $checkMobileno = Astrologer::where('mobile_no', $mobileno)->exists();
        if ($checkMobileno) {
            return response(['status' => 200]);
        }
        return response(['status' => 400]);
    }
    // personal detail form submit
    public function registerPersonalDetail(Request $request)
    {
        $imageName = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $imageName = time() . '-astrologer.' . $file->getClientOriginalExtension();
            $file->storeAs('public/astrologers', $imageName);
        }

        $astrologer = new Astrologer;
        $astrologer->name = $request->name;
        $astrologer->email = $request->email;
        $astrologer->mobile_no = $request->mobile_no;
        $astrologer->gender = $request->gender;
        $astrologer->image = $imageName;
        $astrologer->password = Hash::make($request->password);
        $astrologer->city = $request->city;
        $astrologer->dob = $request->dob;
        $astrologer->address = $request->address;
        if ($astrologer->save()) {
            return response()->json(['status' => 200, 'id' => $astrologer['id'], 'message' => 'Registration successful, please complete your detail']);
        } else {
            return response()->json(['status' => 400, 'message' => 'Unable to register at this moment']);
        }
    }

    // skill detail form submit
    public function registerSkillDetail(Request $request)
    {
        $astrologer = Astrologer::find($request->astro_id);
        $astrologer->primary_skills = $request->primary_skills;
        $astrologer->other_skills = $request->other_skills ? json_encode($request->other_skills) : null;
        $astrologer->category = json_encode($request->category);
        $astrologer->language = json_encode($request->language);
        $astrologer->experience = $request->experience;
        $astrologer->daily_hours_contribution = $request->daily_hours_contribution;
        if ($astrologer->save()) {
            return response()->json(['status' => 200, 'message' => 'Skill updated successfully']);
        } else {
            return response()->json(['status' => 400, 'message' => 'Unable to update skill at this moment']);
        }
    }
    // other detail form submit
    public function registerOtherDetail(Request $request)
    {
        // aadhar front image
        $adharFrontImageName = null;
        if ($request->hasFile('adhar_front_image')) {
            $adharFrontFile = $request->file('adhar_front_image');
            $adharFrontImageName = time() . '-aadharfront.' . $adharFrontFile->getClientOriginalExtension();
            $adharFrontFile->storeAs('public/astrologers/aadhar', $adharFrontImageName);
        }

        // aadhar back image
        $adharBackImageName = null;
        if ($request->hasFile('adhar_back_image')) {
            $adharBackFile = $request->file('adhar_back_image');
            $adharBackImageName = time() . '-aadharback.' . $adharBackFile->getClientOriginalExtension();
            $adharBackFile->storeAs('public/astrologers/aadhar', $adharBackImageName);
        }

        // pancard image
        $pancardImageName = null;
        if ($request->hasFile('pancard_image')) {
            $pancardFile = $request->file('pancard_image');
            $pancardImageName = time() . '-pancard.' . $pancardFile->getClientOriginalExtension();
            $pancardFile->storeAs('public/astrologers/pancard', $pancardImageName);
        }

        $astrologer = Astrologer::find($request->astro_id);
        $astrologer->primary_qualification = $request->primary_qualification;
        $astrologer->primary_degree = $request->primary_degree;
        $astrologer->learn_primary_skill = $request->learn_primary_skill;
        $astrologer->interview_time = $request->interview_time;
        $astrologer->adharcard = $request->adharcard;
        $astrologer->adharcard_front_image = $adharFrontImageName;
        $astrologer->adharcard_back_image = $adharBackImageName;
        $astrologer->pancard = $request->pancard;
        $astrologer->pancard_image = $pancardImageName;
        if ($astrologer->save()) {
            return response()->json(['status' => 200, 'message' => 'Detail updated successfully']);
        } else {
            return response()->json(['status' => 400, 'message' => 'Unable to update detail at this moment']);
        }
    }


    public function EventDetails(Request $request, $slug = '')
    {

        $id = \App\Models\Events::where('slug', $slug)->first()['id'] ?? '';
        if (!$id) {
            return back();
        }
        $upcommining_event1 = \App\Models\Events::select('id', 'event_artist', 'event_name', 'slug', 'language', 'all_venue_data', 'booking_seats', 'youtube_video', 'package_list', 'informational_status', 'event_image', 'images', 'event_about', 'category_id', 'event_team_condition', 'event_interested', 'event_attend', 'event_schedule')
            ->where(['id' => ($id), 'is_approve' => 1, 'status' => 1]);
        $eventData = $upcommining_event1->with(['categorys', 'eventArtist'])->first();
        if (empty($eventData)) {
            return back();
        }
        $ids = $id;
        $customer = \App\Models\User::where('id', auth('customer')->id())->first();
        $eventReviews = \App\Models\EventsReview::where('event_id', ($id))->where('status', 1)->with(['userData'])->orderBy('id', 'desc')->get();
        $event_review = [
            'excellent' =>  $eventReviews->where('star', 5)->count(),
            'good' => $eventReviews->where('star', 4)->count(),
            'average' => $eventReviews->where('star', 3)->count(),
            'belowAverage' => $eventReviews->where('star', 2)->count(),
            'poor' => $eventReviews->where('star', 1)->count(),
            'averageStar' => $eventReviews->avg('star'),
            'list' => $eventReviews,
        ];
        $faqs = \App\Models\FAQ::whereHas('Category', function ($query) {
            $query->where('name', 'event');
        })->with('Category')->get();
        return view('web-views.event.event-details', compact('event_review', 'eventData', 'customer', 'faqs', 'ids'));
    }

    public function EventInterested(Request $request)
    {
        $userfind = User::where('phone', $request->input('person_phone'))->first();
        if ($userfind) {
            \Illuminate\Support\Facades\Auth::guard('customer')->loginUsingId($userfind['id']);
        } else {
            $user = new User();
            $user->phone = $request->input('person_phone');
            $user->name = $request->input('person_name');
            $user->f_name = (explode(" ", $request->input('person_name'))[0] ?? "");
            $user->l_name = (explode(" ", $request->input('person_name'))[1] ?? "");
            $user->email = $request->input('person_phone');
            $user->password =  bcrypt('12345678');
            $user->verify_otp = $request->input('verify_otp') ?? 1;
            $user->save();
            \Illuminate\Support\Facades\Auth::guard('customer')->loginUsingId($user->id);
            $data = [
                'customer_id' => ($user->id ?? "")
            ];
            Helpers::whatsappMessage('whatsapp', 'Welcome Message', $data);
        }
        if (auth('customer')->check()) {
            $check = EventInterest::where(['user_id' => auth('customer')->id(), 'event_id' => $request->event_id])->first();
            if (empty($check)) {
                $interest = new EventInterest();
                $interest->user_id = auth('customer')->id();
                $interest->event_id = $request->event_id;
                $interest->save();
                Events::where('id', $request->event_id)->update(['event_interested' => DB::raw('event_interested + 1')]);
                Toastr::success("The Event you're Interested in has been Successfully Added");
            } else {
                Toastr::success("The event you're interested in has already been added successfully");
            }
        } else {
            Toastr::error('Please Login');
        }
        return back();
    }
    //  public function Event()
    // {
    //     $categories =  \App\Models\EventCategory::where('status', 1)->get();

    //     $todayUpcoming = date('Y-m-d', strtotime('+3 days'));
    //     $futureDateUpcoming = date('Y-m-d', strtotime('+27 days'));
    //     $todayRunning = date('Y-m-d');
    //     $futureDateRunning = date('Y-m-d', strtotime('+2 days'));

    //     // Fetch upcoming events
    //     // $upcomingEvents = \App\Models\Events::select('id', 'event_name', 'language', 'all_venue_data', 'package_list', 'event_image', 'images', 'event_about', 'category_id', DB::raw('"upcommingall" as showvalue'))
    //     //     ->where(['is_approve' => 1, 'status' => 1])
    //     //     ->where(function ($q) use ($todayUpcoming, $futureDateUpcoming) {
    //     //         for ($i = 0; $i < 10; $i++) {
    //     //         $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(all_venue_data, '$[$i].date')) BETWEEN ? AND ?", [$todayUpcoming, $futureDateUpcoming]);
    //     //         }
    //     //     })->with('categorys')->orderBy('id', 'desc')->get();
    //     $upcomingEvents = \App\Models\Events::select('id', 'event_name', 'language', 'all_venue_data', 'package_list', 'event_image', 'images', 'event_about', 'category_id', DB::raw('"upcommingall" as showvalue'))
    //         ->where(['is_approve' => 1, 'status' => 1])
    //         ->where(function ($q) use ($todayUpcoming, $futureDateUpcoming) {
    //             $q->whereRaw("
    //         JSON_UNQUOTE(
    //             JSON_EXTRACT(
    //                 all_venue_data, 
    //                 CONCAT('$[', JSON_LENGTH(all_venue_data) - 1, '].date')
    //             )
    //         ) BETWEEN ? AND ?", [$todayUpcoming, $futureDateUpcoming]);
    //         })
    //         ->with('categorys')
    //         ->orderBy('id', 'desc')
    //         ->get();


    //     // Fetch running events
    //     $runningEvents = \App\Models\Events::select('id', 'event_name', 'language', 'all_venue_data', 'package_list', 'event_image', 'images', 'event_about', 'category_id', DB::raw('"runningall" as showvalue'))
    //         ->where(['is_approve' => 1, 'status' => 1])
    //         ->where(function ($q) use ($todayRunning, $futureDateRunning) {
    //             for ($i = 0; $i < 10; $i++) {
    //                 $q->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(all_venue_data, '$[$i].date')) BETWEEN ? AND ?", [$todayRunning, $futureDateRunning]);
    //             }
    //         })->with('categorys')->orderBy('id', 'desc')->get();

    //     $eventData = $upcomingEvents->merge($runningEvents)->sortByDesc('id');


    //     // dd($eventData);
    //     return view('web-views.event.event', compact('categories', 'eventData'));
    // }

    public function Event(Request $request)
    {
        $categories =  \App\Models\EventCategory::where('status', 1)->get();

        $lat = $request->get('lat') ?? "";
        $long = $request->get('long') ?? "";
        $radius = 45;
        $upcomingEvents =  \App\Models\Events::select(
            'id',
            'event_name',
            'slug',
            'language',
            'all_venue_data',
            'package_list',
            'event_image',
            'images',
            'event_about',
            'category_id',
            'informational_status',
            DB::raw('"runningall" as showvalue')
        )
            ->where('is_approve', 1)
            ->where('status', 1)
            ->whereRaw("
            STR_TO_DATE(
                CONCAT(
                    JSON_UNQUOTE(JSON_EXTRACT(all_venue_data, CONCAT('$[', JSON_LENGTH(all_venue_data) - 1, '].date'))), 
                    ' ',
                    JSON_UNQUOTE(JSON_EXTRACT(all_venue_data, CONCAT('$[', JSON_LENGTH(all_venue_data) - 1, '].start_time')))
                ), '%Y-%m-%d %h:%i %p'
            ) >= STR_TO_DATE(?, '%Y-%m-%d %h:%i %p')
        ", [date('Y-m-d g:i A')])
            ->with(['categorys'])->withCount('EventOrderReview')
            ->withAvg('review', 'star')
            ->orderBy('id', 'desc')
            ->get();

        $filteredEvents = collect();
        foreach ($upcomingEvents as $event) {
            $allVenueData = json_decode($event->all_venue_data, true);
            foreach ($allVenueData as $venue) {
                if (isset($venue['en_event_lat']) && isset($venue['en_event_long'])) {
                    if ((!empty($lat) && !empty($long))) {
                        $latFrom = deg2rad($lat);
                        $lonFrom = deg2rad($long);
                        $latTo = deg2rad($venue['en_event_lat']);
                        $lonTo = deg2rad($venue['en_event_long']);
                        $earthRadius = 6371;
                        $dLat = $latTo - $latFrom;
                        $dLon = $lonTo - $lonFrom;
                        $a = sin($dLat / 2) * sin($dLat / 2) +
                            cos($latFrom) * cos($latTo) *
                            sin($dLon / 2) * sin($dLon / 2);
                        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
                        $distance = $earthRadius * $c;
                        if ($distance <= $radius) {
                            $filteredEvents->push($event);
                            break;
                        }
                    } else {
                        $filteredEvents->push($event);
                        break;
                    }
                }
            }
        }
        $filteredRunningEvents = collect();
        $eventData = $filteredEvents->merge($filteredRunningEvents)->sortByDesc('id');

        $googleMapsApiKey = config('services.google_maps.api_key');
        return view('web-views.event.event', compact('categories', 'eventData', 'googleMapsApiKey'));
    }

    public function EventLeads(Request $request)
    {
        $userfind = User::where('phone', $request->input('person_phone'))->first();
        if ($userfind) {
            \Illuminate\Support\Facades\Auth::guard('customer')->loginUsingId($userfind['id']);
        } else {
            $user = new User();
            $user->phone = $request->input('person_phone');
            $user->name = $request->input('person_name');
            $user->f_name = (explode(" ", $request->input('person_name'))[0] ?? "");
            $user->l_name = (explode(" ", $request->input('person_name'))[1] ?? "");
            $user->email = $request->input('person_phone');
            $user->password =  bcrypt('12345678');
            $user->verify_otp = $request->input('verify_otp') ?? 1;
            $user->save();
            \Illuminate\Support\Facades\Auth::guard('customer')->loginUsingId($user->id);

            $data = [
                'customer_id' => ($user->id ?? "")
            ];
            Helpers::whatsappMessage('whatsapp', 'Welcome Message', $data);
        }

        $validator = Validator::make($request->all(), [
            'event_id' => 'required',
            'package_id' => 'required',
            'person_phone' => 'required',
            'person_name' => 'required',
        ]);


        $amounts = 0;
        $getevents = Events::where('id', $request->get('event_id'))->first();

        if (!empty($getevents) && !empty($getevents['all_venue_data']) && json_decode($getevents['all_venue_data'], true)) {
            foreach (json_decode($getevents['all_venue_data'], true) as $va) {
                if (($va['id'] == $request->get('venue_id')) && !empty($va['package_list'])) {
                    foreach ($va['package_list'] as $value) {
                        if ($value['package_name'] == $request->get('package_id')) {
                            $amounts = $value['price_no'];
                            break;
                        }
                    }
                }
            }
        }
        $userfind = User::where('phone', $request->input('person_phone'))->first();
        $leads = new EventLeads();
        $leads->user_phone = $request->get('person_phone');
        $leads->user_name = $request->get('person_name');
        $leads->event_id = $request->get('event_id');
        $leads->package_id = $request->get('package_id');
        $leads->venue_id = $request->get('venue_id');
        $leads->user_id = $userfind['id'];
        $leads->qty = 1;
        $leads->amount = $amounts;
        $leads->total_amount = $amounts;
        $leads->status = 0;
        $leads->save();
        // return redirect()->route('event-booking', [base64_encode(json_encode(['event' => $request['event_id'], 'lead' => $leads->id]))]);
        return redirect()->route('event-booking', [$getevents['slug'], 'lead' => $leads->id]);
    }

    public function EventBooking(Request $request, $id)
    {
        $ids = $id;
        $event = $id;
        $lead = $request['lead'];
        if (!$lead) {
            return back();
        }
        $upcommining_event1 = \App\Models\Events::select('id', 'event_name', 'slug', 'language', 'all_venue_data', 'booking_seats', 'package_list', 'event_image', 'images', 'event_about', 'category_id', 'event_team_condition', 'event_attend', 'event_schedule', 'required_aadhar_status')
            ->where(['slug' => $event, 'is_approve' => 1, 'status' => 1]);
        $eventData = $upcommining_event1->with(['categorys'])->first();
        if (empty($eventData)) {
            return back();
        }
        $getLeads = \App\Models\EventLeads::where('id', $lead)->with(['coupon'])->first();
        $payment_gateways_list = payment_gateways();
        return view('web-views.event.event-booking', compact('ids', 'getLeads', 'lead', 'eventData', 'payment_gateways_list'));
    }

    public function EventCompletePage(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|between:1,5',
        ], ['rating' => "Please Select a Rate"]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $eventId = json_decode(base64_decode($id));
        $imageName = '';
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('event/comment', 'public');
            $imageName = basename($path);
            $storedPath = \Illuminate\Support\Facades\Storage::url($path);
        }
        $comment = new EventsReview();
        $comment->user_id = auth('customer')->id();
        $comment->event_id = $eventId->event;
        $comment->star = $request->rating;
        $comment->comment = $request->feedback;
        $comment->image = $imageName;
        $comment->status = 1;
        $comment->save();
        return  redirect()->route('event-booking-success', [base64_encode(json_encode(['event' => $eventId->event, 'lead' => $eventId->lead, 'comment' => $comment->id]))]);
    }

    public function Event_Order_Refund($refundDetails)
    {
        $apiKey = config('razor_config.api_key');
        $apiSecret = config('razor_config.api_secret');
        $transactionId = $refundDetails['transaction_id'];
        $amount = $refundDetails['amount'];
        $event_id = $refundDetails['event_id'];

        $refundUrl = "https://api.razorpay.com/v1/payments/{$transactionId}/refund";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $refundUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_USERPWD, $apiKey . ":" . $apiSecret);
        $refundData = json_encode(['amount' => $amount]);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $refundData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($httpcode == 200) {
            $refund_id = json_decode($response);
            EventOrder::where('id', $event_id)->update(['refund_id' => $refund_id->id, 'status' => 3, 'transaction_status' => 1]);
            Toastr::success('Refund processed successfully.');
        } else {
            $error = json_decode($response);
            Toastr::error('Refund failed: ' . $error->error->description);
        }
    }

    public function EventBookingLeadUpdate(Request $request)
    {
        $getLeads = \App\Models\EventLeads::where('id', $request->get('lead_id'))->first();
        if ($getLeads) {
            $getData = \App\Models\Events::select('all_venue_data')->where('id', $getLeads['event_id'])->first();
            $packageList = json_decode($getData['all_venue_data'], true);
            $data_venue = [];
            if ($packageList) {
                foreach ($packageList as $key => $value) {
                    if (($value['id'] == $request->get('venue_id')) && !empty($value['package_list'])) {
                        $data_venue = $value['package_list'];
                    }
                }
            }

            $package = collect($data_venue)->firstWhere('package_name', $request->get('package_id'));
            $price = '';
            if ($package) {
                $price = $package['price_no'];
            }
            $qty =  (($request->get('package_id') == $getLeads['venue_id']) ? (1 + $getLeads['qty']) : 1);
            $updateLead = EventLeads::find($request->get('lead_id'));
            $updateLead->amount = $price;
            $updateLead->total_amount = ($qty * $price);
            $updateLead->venue_id = $request->get('venue_id');
            $updateLead->package_id = $request->get('package_id');
            $updateLead->qty = $qty;
            $updateLead->save();
        }
        return response()->json(['message' => ''], 200);
    }

    // public function EventBookingLeadQtyUpdate(Request $request)
    // {
    //     $msg = '';
    //     $getLeads = \App\Models\EventLeads::where('id', $request->get('lead_id'))->first();
    //     if ($getLeads) {
    //         if ($request->get('type') == 'remove') {
    //             $updateLead = EventLeads::find($request->get('lead_id'));
    //             $updateLead->amount = 0;
    //             $updateLead->venue_id = 0;
    //             $updateLead->total_amount = 0;
    //             $updateLead->qty = 0;
    //             $updateLead->save();
    //             $msg = 'Remove Venue Successfully';
    //         } elseif ($request->get('type') == 'members') {
    //             $updateLead = EventLeads::find($request->get('lead_id'));
    //             $updateLead->user_information = json_encode($request['MultiArrayPush']);
    //             $updateLead->save();
    //         } else {
    //             $updateLead = EventLeads::find($request->get('lead_id'));
    //             $updateLead->amount = $request->get('amount');
    //             $updateLead->coupon_amount = $request->get('coupon_amount');
    //             $updateLead->coupon_id = $request->get('coupon_id');
    //             $updateLead->total_amount = (($request->get('quantity') * $request->get('amount')) - $request->get('coupon_amount'));
    //             $updateLead->qty = $request->get('quantity');
    //             $updateLead->save();
    //             $msg = 'Update Venue Successfully';
    //         }
    //     }
    //     return response()->json(['message' => $msg], 200);
    // }

    public function EventBookingLeadQtyUpdate(Request $request)
    {
        $msg = '';
        $getLeads = \App\Models\EventLeads::find($request->get('lead_id'));

        if (!$getLeads) {
            return response()->json(['message' => 'Lead not found'], 404);
        }

        $type = $request->get('type');

        // -----------------------
        // Remove Venue
        // -----------------------
        if ($type === 'remove') {
            $getLeads->amount = 0;
            $getLeads->venue_id = 0;
            $getLeads->total_amount = 0;
            $getLeads->qty = 0;
            $getLeads->user_information = null;
            $getLeads->save();

            $msg = 'Remove Venue Successfully';
        }

        // -----------------------
        // Members Update
        // -----------------------
        elseif ($type === 'members') {
            $oldData = $getLeads->user_information ? json_decode($getLeads->user_information, true) : [];
            $newData = $request->input('MultiArrayPush', []);

            // Only merge if new data exists
            if (!empty($newData)) {
                $mergedData = array_merge($oldData, $newData);
                $getLeads->user_information = json_encode($mergedData);
                $getLeads->qty = count($mergedData);

                // Use existing DB amount as fallback
                $perTicketPrice = $getLeads->amount > 0 ? $getLeads->amount : ($request->get('amount') ?? 0);
                $getLeads->amount = $perTicketPrice;

                // Total amount calculation (coupon considered)
                $coupon = $getLeads->coupon_amount ?? 0;
                $getLeads->total_amount = ($getLeads->qty * $perTicketPrice) - $coupon;

                $getLeads->save();
            }

            $msg = 'Members updated successfully';
        }

        // Remove single member
        elseif ($type === 'remove_member') {
            $oldData = $getLeads->user_information ? json_decode($getLeads->user_information, true) : [];
            $removeAadhaar = $request->get('aadhaar');
            $removeIndex = $request->get('index'); // 🔥 index भी भेजेंगे

            if (!empty($oldData)) {
                if ($removeAadhaar) {
                    // Aadhaar se remove
                    $updatedData = array_filter($oldData, function ($member) use ($removeAadhaar) {
                        return ($member['aadhar'] ?? '') !== $removeAadhaar;
                    });
                } elseif ($removeIndex !== null) {
                    // Index se remove (agar aadhaar null hai)
                    unset($oldData[$removeIndex]);
                    $updatedData = $oldData;
                } else {
                    $updatedData = $oldData;
                }

                $updatedData = array_values($updatedData); // reindex

                $perTicketPrice = $getLeads->amount > 0 ? $getLeads->amount : ($request->get('amount') ?? 0);
                $coupon = $getLeads->coupon_amount ?? 0;

                if (count($updatedData) > 0) {
                    $getLeads->user_information = json_encode($updatedData);
                    $getLeads->qty = count($updatedData);
                    $getLeads->total_amount = ($getLeads->qty * $perTicketPrice) - $coupon;
                } else {
                    $getLeads->user_information = null;
                    $getLeads->qty = 1;
                    $getLeads->total_amount = $perTicketPrice - $coupon;
                }

                $getLeads->amount = $perTicketPrice;
                $getLeads->save();

                return response()->json([
                    'message' => 'Member removed successfully',
                    'amount' => $getLeads->amount,
                    'total_amount' => $getLeads->total_amount,
                    'qty' => $getLeads->qty,
                    'user_information' => $getLeads->user_information
                ]);
            }
        }

        // -----------------------
        // Venue/Other Update
        // -----------------------
        else {
            $getLeads->amount = $request->get('amount', $getLeads->amount);
            $getLeads->coupon_amount = $request->get('coupon_amount', $getLeads->coupon_amount ?? 0);
            $getLeads->coupon_id = $request->get('coupon_id', $getLeads->coupon_id ?? null);
            $getLeads->qty = $request->get('quantity', $getLeads->qty ?? 0);
            $getLeads->total_amount = ($getLeads->qty * $getLeads->amount) - $getLeads->coupon_amount;

            // Update user_information only if MultiArrayPush is present and not empty
            $multiData = $request->input('MultiArrayPush', []);
            if (!empty($multiData)) {
                $getLeads->user_information = json_encode($multiData);
            }

            $getLeads->save();
            $msg = 'Update Venue Successfully';
        }

        return response()->json([
            'message' => $msg,
            'amount' => $getLeads->amount,
            'total_amount' => $getLeads->total_amount,
            'qty' => $getLeads->qty
        ], 200);
    }



    public function EventSuccessPage(Request $request, $id)
    {
        $ids = $id;
        $datas = json_decode(base64_decode($id));
        return view('web-views.event.event-booking-success', compact('ids', 'datas'));
    }

    public function EventBookingFree(Request $request, $id)
    {
        $booking_seats = [];
        $getLead = EventLeads::where('id', $request['lead'])->first();
        $EventId = Events::where('id', $getLead['event_id'])->first();

        if (empty($EventId) || empty($getLead) ||  ($getLead['package_id'] ?? 0) <= 0) {
            Toastr::error('Invalid data passed.');
            return redirect()->route('event-booking', [$id, 'lead' => $request['lead']]);
        }
        $bookingSeats = json_decode($EventId['all_venue_data'], true);

        $foundPackage = false;
        $pn = 1;
        $keys = 0;
        if ($bookingSeats) {
            foreach ($bookingSeats as $key => $bo_se) {
                $booking_seats['all_venue_data'][$key] = $bo_se;
                if ((($bo_se['id'] ?? "") == $getLead['venue_id']) && !empty($bo_se['package_list'])) {
                    foreach ($bo_se['package_list'] as $keys => $value) {
                        if ($value['package_name'] == $getLead['package_id']) {
                            $foundPackage = true;
                            if (($value['available'] - $getLead['qty']) < 0) {
                                Toastr::error($getLead['qty'] . ' seats are not available. ' . $value['available'] . ' seats are available.');
                                return redirect()->route('event-booking', [$id]);
                            }
                            $booking_seats['all_venue_data'][$key]['package_list'][$keys]['available'] = ($value['available'] - $getLead['qty']);
                            $booking_seats['all_venue_data'][$key]['package_list'][$keys]['sold'] = ($value['sold'] + $getLead['qty']);
                        }
                    }
                }
            }
            Events::where('id', $getLead['event_id'])->update($booking_seats);
        }
        if (!$foundPackage) {
            json_decode($EventId['all_venue_data'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                Toastr::error('Booking seats data is not properly formatted.');
                return redirect()->route('event-booking', [$id]);
            }
        }


        $events = new EventOrder();
        $events->event_id = $getLead['event_id'];
        $events->user_id = $getLead['user_id'];
        $events->venue_id = $getLead['venue_id'];
        $events->amount = 0;
        $events->coupon_amount = 0;
        $events->admin_commission = 0;
        $events->gst_amount = 0;
        $events->final_amount = 0;
        $events->transaction_id = '';
        $events->transaction_status = 1;
        $events->status = 1;
        $events->save();

        $leadMembers = $getLead->user_information ? json_decode($getLead->user_information, true) : [];

        $eventMembers = [];
        foreach ($leadMembers as $index => $member) {
            $temp = [
                'id' => $member['id'] ?? ($index + 1)
            ];
            $eventMembers[] = array_merge($temp, $member);
        }

        $eventItem = new EventOrderItems();
        $eventItem->order_id = $events->id;
        $eventItem->package_id = $getLead['package_id'];
        $eventItem->no_of_seats = $getLead['qty'];
        $eventItem->sub_amount = 0;
        $eventItem->gst = 0;
        $eventItem->gst_amount = 0;
        $eventItem->amount = 0;
        // $JsonEncodeMembers = [];
        // if ($getLead['qty'] > 0) {
        //     for ($qn = 0; $qn < $getLead['qty']; $qn++) {
        //         $JsonEncodeMembers[$qn]['id'] = ($qn + 1);
        //         $JsonEncodeMembers[$qn]['name'] = $request['member'][$qn]['name'] ?? '';
        //         $JsonEncodeMembers[$qn]['phone'] = $request['member'][$qn]['phone'] ?? '';
        //         $JsonEncodeMembers[$qn]['aadhar'] = $request['member'][$qn]['aadhar'] ?? '';
        //         if (($request->file('member')[$qn]['aadhar_image'] ?? '') && $request->file('member')[$qn]['aadhar_image']) {
        //             $JsonEncodeMembers[$qn]['aadhar_image'] =  \App\Utils\ImageManager::file_upload('event/aadhar_image/',  $request->file('member')[$qn]['aadhar_image']->getClientOriginalExtension(), $request->file('member')[$qn]['aadhar_image']);
        //         }
        //         $JsonEncodeMembers[$qn]['verify'] = 0;
        //         $JsonEncodeMembers[$qn]['time'] = '';
        //     }
        // }
        $eventItem->user_information = json_encode($eventMembers);
        $eventItem->save();
        \App\Models\EventLeads::where('id', $getLead['id'])->update(['status' => 1]);
        $userInfo = \App\Models\User::where('phone', ($getLead['user_phone'] ?? ""))->first();
        return redirect()->route('event-booking-success', [$id]);
    }

    public function error_page()
    {
        return view('errors.404');
    }

    public function download_app(Request $request)
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
        // return view('download-page.download-page');
    }


    public function SelfVehicleList(Request $request)
    {
        $vehicleList = TourVehicleCetagory::where('status', 1)->groupBy('type')->get();
        $SelfVehicles = SelfDrivingCabs::with(['getCabId', 'getCategory', 'getType', 'getTraveller'])->where(['is_approve' => 1, 'status' => 1])->get()->filter(function ($item) {
            return $item->getCabId && $item->getTraveller;
        })
            ->groupBy(function ($item) {
                return $item->getCabId->name . '|' . $item->getTraveller->company_name;
            })
            ->map(function ($group) {
                return $group->first();
            })
            ->values();
        return view('web-views.self-vehicle.index', compact('vehicleList', 'SelfVehicles'));
    }

    public function SelfVehicleChoose(Request $request)
    {
        $vehicleList = TourVehicleCetagory::where('status', 1)->groupBy('type')->get();
        $SelfVehicles = SelfDrivingCabs::with(['getCabId', 'getCategory', 'getType', 'getTraveller'])->where(['is_approve' => 1, 'status' => 1])->get()->filter(function ($item) {
            return $item->getCabId && $item->getTraveller;
        })->filter(function ($item) use ($request) {
            return $item->getCabId->slug === $request['slug'];
        })
            ->values();
        return view('web-views.self-vehicle.vehicle-filter', compact('vehicleList', 'SelfVehicles'));
    }

    public function SelfVehicleDetails(Request $request)
    {
        $SelfVehicles = SelfDrivingCabs::with(['getCabId', 'getCategory', 'getType', 'getTraveller'])->where('slug', $request['slug'])->where(['is_approve' => 1, 'status' => 1])->first();
        if (empty($SelfVehicles)) {
            Toastr::error('Not Found Data.');
            return redirect()->to('/');
        }
        return view('web-views.self-vehicle.self-vehicle-details', compact('SelfVehicles'));
    }

    public function SelfVehicleLeads(Request $request)
    {

        $userfind = User::where('phone', ($request->input('person_phone') ?? ""))->first();
        if ($userfind) {
            \Illuminate\Support\Facades\Auth::guard('customer')->loginUsingId($userfind['id']);
        } else {
            $user = new User();
            $user->phone = $request->input('person_phone');
            $user->name = $request->input('person_name');
            $user->f_name = (explode(" ", $request->input('person_name'))[0] ?? "");
            $user->l_name = (explode(" ", $request->input('person_name'))[1] ?? "");
            $user->email = $request->input('person_phone');
            $user->password =  bcrypt('12345678');
            $user->verify_otp = $request->input('verify_otp') ?? 1;
            $user->save();
            \Illuminate\Support\Facades\Auth::guard('customer')->loginUsingId($user->id);
            $data = [
                'customer_id' => ($user->id ?? "")
            ];
            Helpers::whatsappMessage('whatsapp', 'Welcome Message', $data);
        }
        if (auth('customer')->check()) {
            $getData = SelfDrivingCabs::where('id', $request['id'])->first();
            $leads = new SelfVehicleLeads();
            $leads->user_id = auth('customer')->id();
            $leads->type = $getData['type'];
            $leads->category_id = $getData['category_id'];
            $leads->cab_id = $getData['cab_id'];
            $leads->traveller_id = $getData['traveller_id'];
            $leads->vehicle_id = $getData['id'];
            $leads->save();
            return redirect()->route('self-vehicle-booking', ['slug' => $getData['slug'], 'lead' => base64_encode($leads->id)]);
        }
        return redirect()->route('/');
    }

    public function SelfVehicleBooking(Request $request)
    {
        $SelfVehicles = SelfDrivingCabs::with(['getCabId', 'getCategory', 'getType', 'getTraveller'])->where('slug', $request['slug'])->where(['is_approve' => 1, 'status' => 1])->first();
        if (empty($SelfVehicles)) {
            Toastr::error('Not Found Data.');
            return redirect()->to('/');
        }
        $lead_data = SelfVehicleLeads::with(['SelfCabData'])->find(base64_decode($request['lead']));
        $PolicysCondition = SelfDrivingPolicy::where('status', 1)->get();
        $cancellationPolicy = SelfCancellationPolicy::where('status', 1)->get();
        $userfind = User::where('id', auth('customer')->id())->first();
        return view('web-views.self-vehicle.self-vehicle-booking', compact('SelfVehicles', 'lead_data', 'PolicysCondition', 'cancellationPolicy', 'userfind'));
    }

    public function SelfVehicleleadUpdate(Request $request)
    {
        $lead_data = SelfVehicleLeads::with(['SelfCabData'])->find($request['lead']);
        if (!$lead_data) {
            return response()->json(['status' => 0, 'message' => 'not Found Data', 'data' => []], 200);
        }
        $data = [];
        
        if ($request['step'] == '1') {
            $data['pickup_address'] = $request['location'];
            $data['pickup_date'] = date('d-m-Y h:i A', strtotime(explode("to", $request['date'])[0]));
            $data['droup_date'] = date('d-m-Y h:i A', strtotime(explode("to", $request['date'])[1]));
            if ($lead_data['package_id'] == 0) {
                $pick_point = json_decode($lead_data['SelfCabData']['policy_info'] ?? "[]", true);
                $filtered = collect($pick_point['en'] ?? [])->first();
                $data['package_id'] = $filtered['id'];
                $data['price'] = $filtered['price'];
            }
        } elseif ($request['step'] == '2.1' || $request['step'] == '2') {
            $data['package_id'] = $request['package_id'];
            $pick_point = json_decode($lead_data['SelfCabData']['policy_info'] ?? "[]", true);
            $filtered = collect($pick_point['en'] ?? [])->firstWhere('id', $data['package_id']);
            $data['price'] = $filtered['price'];
        } elseif ($request['step'] == '3') {
            $data['f_name'] = $request['firstName'];
            $data['l_name'] = $request['lastName'];
            $data['age'] = $request['user_age'];
            $data['phone_number'] = $request['user_phone'];
            $data['email'] = $request['email_id'];
            $data['aadhaar_number'] = $request['aadhar_number'];
            $data['pancard'] = $request['pan_cards'];
            $data['driving_licence'] = $request['driving_licence'];
        } elseif ($request['step'] == 4) {
            SelfVehicleLeads::where('id', $request['lead'])->update(['wallet_type' => $request['wallet_type']]);
            return response()->json(['status' => 0, 'message' => 'amount', 'data' => (($lead_data['price'] ?? 0) + ($lead_data['security_amount'] ?? 0) + ($lead_data['tax_amount'] ?? 0) - ($lead_data['coupan_amount'] ?? 0))], 200);
        }
        $gst_amount = 0;
        $admin_commission = 0;
        $final_amount = (($lead_data['price'] ?? 0));
        $selfvehicletax = \App\Models\ServiceTax::find(1);
        $vip_admin_commission = 5;
        if ($selfvehicletax['self_vehicle_tax']) {
            $gst_amount = (($final_amount * ($selfvehicletax['self_vehicle_tax'] ?? 0)) / 100);
            $final_amount = $final_amount;
        }
        if (($vip_admin_commission ?? 0)) {
            $admin_commission = (($final_amount * $vip_admin_commission) / 100);
            $final_amount = ($final_amount - $admin_commission);
        }
        $data['tax_amount'] = $gst_amount;
        $data['tax'] = ($selfvehicletax['self_vehicle_tax'] ?? 0);
        $data['admin_amount'] = $admin_commission;
        $data['final_amount'] = $final_amount;
        if ($data) {
            SelfVehicleLeads::where('id', $request['lead'])->update($data);
        }
        return response()->json(['status' => 1, 'message' => 'Unable to update detail at this moment', 'data' => $request->all()], 200);
    }

    public function SelfVehicleBookingSuccess(Request $request)
    {
        $SelfVehicles = SelfDrivingCabs::with(['getCabId', 'getCategory', 'getType', 'getTraveller'])->where('slug', $request['slug'])->where(['is_approve' => 1, 'status' => 1])->first();
        if (empty($SelfVehicles)) {
            Toastr::error('Not Found Data.');
            return redirect()->to('/');
        }
        return view('web-views.self-vehicle.self-vehicle-success', compact('SelfVehicles'));
    }

    // astrologer
    public function add_astrologer()
    {
        return view('web-views.astrologer.register');
    }

    public function astrologer_check(Request $request)
    {
        $astroExists = Astrologer::select('id', 'adharcard', 'pancard', 'account_no', 'bank_ifsc')->where('mobile_no', $request->mobile_no)->first();
        if (!empty($astroExists)) {
            return response()->json([
                'status' => true,
                'message' => 'got data successfully',
                'astro_exists' => $astroExists,
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'unable to get data',
            'astro_exists' => [],
        ]);
    }


    public function astrologer_process(Request $request)
    {
        $astroExists = Astrologer::where('mobile_no', $request->mobile_no)->exists();
        if (!$astroExists) {
            if ($request->type == 'aadhaarcard') {
                $data = UserAadhaarKyc::where('aadhaar_number', $request->id_no)->first();
                if ($data) {
                    $store = new Astrologer();
                    $store->mobile_no = $request->mobile_no;
                    $store->email = $request->mobile_no . '@gmail.com';
                    $store->password = Hash::make('Mahakal@' . $request->mobile_no);
                    $store->name = $data->full_name;
                    $store->adharcard = $request->id_no;
                    $store->adharcard_mobile = $data->phone_no;
                    $store->type = 'freelancer';
                    $store->dob = $data->dob;
                    $store->gender = $data->gender == 'M' ? 'male' : 'female';
                    $store->pincode = $data->zip;
                    $store->state = !empty($data->address) ? json_decode($data->address, true)['state'] : null;
                    $store->city = !empty($data->address) ? json_decode($data->address, true)['loc'] : null;
                    $store->address = !empty($data->address) ? json_decode($data->address, true)['street'] : null;
                    $store->image = $data->image;
                    if ($store->save()) {
                        return response()->json([
                            'status' => true,
                            'message' => 'aadhaar verified and saved successfully',
                        ]);
                    }
                    return response()->json([
                        'status' => false,
                        'message' => 'unable to verify aadhaar data! try again.'
                    ]);
                }
                return response()->json([
                    'status' => false,
                    'message' => 'unable to get aadhaar data! try again.',
                ]);
            } elseif ($request->type == 'pancard') {
                $data = UserPanCardVerified::where('pan_number', $request->id_no)->first();
                if ($data) {
                    $store = new Astrologer();
                    $store->mobile_no = $request->mobile_no;
                    $store->email = $request->mobile_no . '@gmail.com';
                    $store->password = Hash::make('Mahakal@' . $request->mobile_no);
                    $store->name = $data->full_name;
                    $store->pancard = $request->id_no;
                    $store->type = 'freelancer';
                    if ($store->save()) {
                        return response()->json([
                            'status' => true,
                            'message' => 'pancard verified and saved successfully',
                        ]);
                    }
                    return response()->json([
                        'status' => false,
                        'message' => 'unable to verify pancard data! try again.'
                    ]);
                }
                return response()->json([
                    'status' => false,
                    'message' => 'unable to get pancard data! try again.',
                ]);
            } elseif ($request->type == 'bank') {
                $data = AccountNumberVerified::where('account_number', $request->id_no)->first();
                if ($data) {
                    $store = new Astrologer();
                    $store->mobile_no = $request->mobile_no;
                    $store->email = $request->mobile_no . '@gmail.com';
                    $store->password = Hash::make('Mahakal@' . $request->mobile_no);
                    $store->account_no = $request->id_no;
                    $store->bank_ifsc = $data->ifsc;
                    $store->type = 'freelancer';
                    if ($store->save()) {
                        return response()->json([
                            'status' => true,
                            'message' => 'bank verified and saved successfully',
                        ]);
                    }
                    return response()->json([
                        'status' => false,
                        'message' => 'unable to verify bank data! try again.'
                    ]);
                }
                return response()->json([
                    'status' => false,
                    'message' => 'unable to get bank data! try again.',
                ]);
            }
        } else {
            if ($request->type == 'aadhaarcard') {
                $data = UserAadhaarKyc::where('aadhaar_number', $request->id_no)->first();
                if ($data) {
                    $update = Astrologer::where('mobile_no', $request->mobile_no)->first();
                    $update->adharcard = $request->id_no;
                    $update->adharcard_mobile = $data->phone_no;
                    if ($update->save()) {
                        return response()->json([
                            'status' => true,
                            'message' => 'aadhaar verified and updated successfully',
                        ]);
                    }
                    return response()->json([
                        'status' => false,
                        'message' => 'unable to verify aadhaar data, try again!'
                    ]);
                }
                return response()->json([
                    'status' => false,
                    'message' => 'unable to get aadhaar data, try again!',
                ]);
            } elseif ($request->type == 'pancard') {
                $data = UserPanCardVerified::where('pan_number', $request->id_no)->first();
                if ($data) {
                    $update = Astrologer::where('mobile_no', $request->mobile_no)->first();
                    $update->pancard = $request->id_no;
                    if ($update->save()) {
                        return response()->json([
                            'status' => true,
                            'message' => 'pancard verified and updated successfully',
                        ]);
                    }
                    return response()->json([
                        'status' => false,
                        'message' => 'unable to verify pancard data, try again!'
                    ]);
                }
                return response()->json([
                    'status' => false,
                    'message' => 'unable to get pancard data, try again!',
                ]);
            } elseif ($request->type == 'bank') {
                $data = AccountNumberVerified::where('account_number', $request->id_no)->first();
                if ($data) {
                    $update = Astrologer::where('mobile_no', $request->mobile_no)->first();
                    $update->account_no = $request->id_no;
                    $update->bank_ifsc = $data->ifsc;
                    if ($update->save()) {
                        return response()->json([
                            'status' => true,
                            'message' => 'bank verified and updated successfully',
                        ]);
                    }
                    return response()->json([
                        'status' => false,
                        'message' => 'unable to verify bank data, try again!'
                    ]);
                }
                return response()->json([
                    'status' => false,
                    'message' => 'unable to get bank data, try again!',
                ]);
            }
        }
    }

    public function store_astrologer(Request $request)
    {
        $request->validate([
            'mobile_no' => 'required',
            'adharcard' => 'required'
        ]);



        dd($request->all());
    }
}
