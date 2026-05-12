<?php

namespace App\Http\Controllers\RestAPI\v4;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Review;
use App\Models\Seller;
use App\Models\Shop;
use App\Utils\Helpers;
use App\Utils\ProductManager;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SellerController extends Controller
{

    public function __construct(
        private Product     $product,
        private Seller      $seller,
        private Coupon      $coupon,
        private Review      $review,
        private OrderDetail $order_details,
    )
    {
    }

    public function get_seller_info(Request $request)
    {
        $shop = Shop::where('slug', $request->slug)->first();

        if ($shop) {
            $sellerId = $shop->seller_id;
        } else {
            $companyName = getWebConfig(name: 'company_name');
            $companySlug = Str::slug($companyName, '-');
            if($companySlug == $request->slug){
                $sellerId = 0;
            }else{
                return response()->json(['message' => translate('shop_not_found'), 'status' => 403], 403);
            }
        }

        $data = [];
        $seller = null;
        if ($sellerId != 0) {
            $seller = $this->seller::approved()->with(['shop'])
                ->where(['id' => $sellerId])
                ->first(['id', 'f_name', 'l_name', 'phone', 'image', 'minimum_order_amount']);
            if (!$seller) {
                return response()->json(['message' => translate('shop_not_found'), 'status' => 403], 403);
            }
        }

        $productIds = $this->product::when($sellerId == 0, function ($query) {
            return $query->where(['added_by' => 'admin']);
        })
            ->withCount('reviews')
            ->when($sellerId != 0, function ($query) use ($sellerId) {
                return $query->where(['added_by' => 'seller'])
                    ->where('user_id', $sellerId);
            })
            ->active()->pluck('id')->toArray();

        $avgRating = $this->review::whereIn('product_id', $productIds)->avg('rating');
        $totalReview = $this->review::whereIn('product_id', $productIds)->count();
        $totalOrder = $this->order_details::whereIn('product_id', $productIds)->groupBy('order_id')->count();
        $totalProduct = $this->product::active()
            ->when($sellerId == 0, function ($query) {
                return $query->where(['added_by' => 'admin']);
            })
            ->when($sellerId != 0, function ($query) use ($sellerId) {
                return $query->where(['added_by' => 'seller'])
                    ->where('user_id', $sellerId);
            })->count();

        $coupons = $this->coupon::when($sellerId != 0, function ($query) use ($sellerId) {
            return $query->with('seller')
                ->whereIn('seller_id', [0, $sellerId]);
        })
            ->when($sellerId == 0, function ($query) {
                return $query->whereIn('seller_id', [0, null]);
            })
            ->where(['status' => 1])
            ->whereDate('start_date', '<=', date('Y-m-d'))
            ->whereDate('expire_date', '>=', date('Y-m-d'))
            ->inRandomOrder()->get();

        $firstOrderCoupons = $this->coupon::where(['status' => 1, 'coupon_type' => 'first_order'])
            ->whereDate('start_date', '<=', date('Y-m-d'))
            ->whereDate('expire_date', '>=', date('Y-m-d'))
            ->inRandomOrder()->get();

        $finalCoupon = $coupons->merge($firstOrderCoupons);

        $finalCoupon->map(function ($coupon) {
            $startDate = $coupon['start_date'];
            $expireDate = $coupon['expire_date'];
            $coupon['start_date_update'] = date("Y-m-d 00:00:01", strtotime($startDate));
            $coupon['expire_date_update'] = date("Y-m-d 23:59:59", strtotime($expireDate));
        });


        $customer = $request->user();
        $popularProduct = $this->product::active()
            ->with(['rating', 'wishList' => function ($query) use ($customer) {
                return $query->where('customer_id', $customer->id ?? 0);
            }, 'compareList' => function ($query) use ($customer) {
                return $query->where('user_id', $customer->id ?? 0);
            }])
            ->withCount('reviews')
            ->whereHas('orderDelivered', function ($query) {
                return $query;
            })->get();

        $popularProductFinal = Helpers::product_data_formatting($popularProduct, true);

        $data['seller'] = $seller;
        $data['avg_rating'] = (float)$avgRating;
        $data['positive_review'] = round(($avgRating * 100) / 5);
        $data['total_review'] = $totalReview;
        $data['total_order'] = $totalOrder;
        $data['total_product'] = $totalProduct;
        $data['coupons'] = $finalCoupon;
        $data['popular_product'] = $popularProductFinal;

        return response()->json($data, 200);
    }

    public function get_seller_products($seller_id, Request $request)
    {
        $data = ProductManager::get_seller_products($seller_id, $request);
        $data['products'] = Helpers::product_data_formatting($data['products'], true);
        return response()->json($data, 200);
    }

    public function get_seller_all_products($seller_id, Request $request)
    {
        $products = Product::active()->with(['rating', 'tags'])
            ->withCount('reviews')
            ->where(['user_id' => $seller_id, 'added_by' => $request->added_by])
            ->when($request->search, function ($query) use ($request) {
                $key = explode(' ', $request->search);
                foreach ($key as $value) {
                    $query->where('name', 'like', "%{$value}%");
                }
            })
            ->latest()
            ->paginate($request->limit, ['*'], 'page', $request->offset);


        $products_final = Helpers::product_data_formatting($products->items(), true);

        return [
            'total_size' => $products->total(),
            'limit' => (int)$request->limit,
            'offset' => (int)$request->offset,
            'products' => $products_final
        ];
    }

    public function get_top_sellers()
    {
        $coupons = Coupon::where(['seller_id' => '0', 'status' => 1])
            ->whereDate('start_date', '<=', date('Y-m-d'))
            ->whereDate('expire_date', '>=', date('Y-m-d'))
            ->get();

        $topSellers = Shop::with(['seller.coupon' => function ($query) {
            $query->where(['status' => 1])
                ->whereDate('start_date', '<=', date('Y-m-d'))
                ->whereDate('expire_date', '>=', date('Y-m-d'));
        }])
            ->whereHas('seller', function ($query) {
                return $query->approved();
            })
            ->withCount(['products' => function ($query) {
                return $query->active();
            }])
            ->paginate(15, ['*'], 'page', 1);
        $topSellers = $topSellers->map(function ($data) use ($coupons) {
            $finalCoupon = $data->seller->coupon->merge($coupons);
            $data['seller']['coupons'] = $finalCoupon;
            $data['seller_id'] = (int)$data['seller_id'];
            return $data;
        });
        return response()->json($topSellers, 200);
    }

    public function get_all_sellers()
    {
        $top_sellers = Shop::whereHas('seller', function ($query) {
            return $query->approved();
        })->get();
        return response()->json($top_sellers, 200);
    }

    public function get_recent_ordered_shops(Request $request)
    {
        $customer = $request->user();

        $coupons = Coupon::where(['seller_id' => '0', 'status' => 1])
            ->whereDate('start_date', '<=', date('Y-m-d'))
            ->whereDate('expire_date', '>=', date('Y-m-d'))
            ->get();

        $sellers = $this->seller->approved()->with(['shop', 'product.reviews' => function ($query) {
            $query->active();
        },
            'coupon' => function ($query) {
                $query->where(['status' => 1])
                    ->whereDate('start_date', '<=', date('Y-m-d'))
                    ->whereDate('expire_date', '>=', date('Y-m-d'));
            }])
            ->whereHas('orders', function ($query) use ($customer) {
                $query->where(['customer_id' => $customer->id, 'seller_is' => 'seller']);
            })
            ->inRandomOrder()->take(12)->get();

        $sellers->map(function ($seller) use ($coupons) {
            $finalCoupon = $seller->coupon->merge($coupons);

            $seller->product->map(function ($product) {
                $product['rating'] = $product?->reviews->pluck('rating')->sum();
                $product['rating_count'] = $product->reviews->count();
            });
            $seller['coupons'] = $finalCoupon;
            $seller->shop['total_rating'] = $seller?->product->pluck('rating')->sum();
            $seller->shop['rating_count'] = $seller->product->pluck('rating_count')->sum();
        });

        return response()->json($sellers, 200);
    }
}
