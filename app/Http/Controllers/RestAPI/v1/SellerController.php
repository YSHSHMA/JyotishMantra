<?php

namespace App\Http\Controllers\RestAPI\v1;

use App\Http\Controllers\Controller;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Review;
use App\Models\Seller;
use App\Models\Shop;
use App\Utils\Helpers;
use App\Utils\ProductManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SellerController extends Controller
{
    public function __construct(
        private Seller       $seller,
    )
    {
    }

    public function get_seller_info(Request $request)
    {
        $data=[];
        $sellerId = $request['seller_id'];
        $seller = $sellerId!= 0 ? Seller::with(['shop'])->where(['id' => $request['seller_id']])->first(['id', 'f_name', 'l_name', 'phone', 'image', 'minimum_order_amount']) : null;

        $productIds = Product::when($sellerId == 0, function ($query) {
            return $query->where(['added_by' => 'admin']);
        })
            ->withCount('reviews')
            ->when($sellerId != 0, function ($query) use ($sellerId) {
                return $query->where(['added_by' => 'seller'])
                    ->where('user_id', $sellerId);
            })
            ->active()->pluck('id')->toArray();

        $avgRating = Review::whereIn('product_id', $productIds)->avg('rating');
        $totalReview = Review::whereIn('product_id', $productIds)->count();
        $totalOrder = Review::whereIn('product_id', $productIds)->groupBy('order_id')->count();
        $totalProduct = Product::active()
            ->when($sellerId == 0, function ($query) {
                return $query->where(['added_by' => 'admin']);
            })
            ->when($sellerId != 0, function ($query) use ($sellerId) {
                return $query->where(['added_by' => 'seller'])
                    ->where('user_id', $sellerId);
            })->count();

        $minimum_order_amount = 0;
        $minimum_order_amount_status = Helpers::get_business_settings('minimum_order_amount_status');
        $minimum_order_amount_by_seller = Helpers::get_business_settings('minimum_order_amount_by_seller');
        $rating_percentage = round(($avgRating * 100) / 5);
        if($sellerId !=0 && $minimum_order_amount_status && $minimum_order_amount_by_seller)
        {
            $minimum_order_amount = $seller['minimum_order_amount'];
            unset($seller['minimum_order_amount']);
        }

        $data['seller'] = $seller;
        $data['avg_rating'] = (float)$avgRating;
        $data['positive_review'] = round(($avgRating * 100) / 5);
        $data['total_review'] = $totalReview;
        $data['total_order'] = $totalOrder;
        $data['total_product'] = $totalProduct;
        $data['minimum_order_amount']= $minimum_order_amount;
        $data['rating_percentage']= $rating_percentage;

        return response()->json($data, 200);
    }

    public function get_seller_products($seller_id, Request $request)
    {
        $data = ProductManager::get_seller_products($seller_id, $request);
        $data['products'] = Helpers::product_data_formatting($data['products'], true) ?? [];
        return response()->json($data, 200);
    }

    public function getSellerList(Request $request, $type)
    {
        $sellers = $this->seller->approved()->with(['shop','orders','product.reviews'=>function($query){
                $query->active();
            }])
            ->withCount(['orders','product' => function ($query) {
                $query->active();
            }])
            ->when($type == 'top', function ($query){
                return $query->whereHas('orders',function($query){
                        $query->where('seller_is','seller');
                    })->orderBy('orders_count', 'DESC');
            })
            ->when($type == 'new', function ($query){
                return $query->latest();
            })
            ->paginate($request['limit'], ['*'], 'page', $request['offset']);

        $sellers?->map(function($seller){
            $seller->product?->map(function($product){
                $product['rating'] = $product?->reviews->pluck('rating')->sum();
                $product['rating_count'] = $product->reviews->count();
            });
            $seller['total_rating'] = $seller?->product->pluck('rating')->sum();
            $seller['rating_count'] = $seller->product->pluck('rating_count')->sum();
            $seller['average_rating'] = $seller['total_rating'] / ($seller['rating_count'] == 0 ? 1 : $seller['rating_count']);
            unset($seller['product']);
            unset($seller['orders']);
        });

        return [
            'total_size' => $sellers->total(),
            'limit' => (int)$request['limit'],
            'offset' => (int)$request['offset'],
            'sellers' => $sellers->items()
        ];

    }

    public function getShopSlug(Request $request, $slug)
    {
        $sellers = $this->seller
            ->approved()
            ->whereHas('shop', function ($query) use ($slug) {
                $query->where('slug', $slug);
            })
            ->with(['shop', 'orders', 'product.reviews' => function ($query) {
                $query->active();
            }])
            ->withCount([
                'orders',
                'product' => function ($query) {
                    $query->active();
                }
            ])
            ->paginate(
                $request['limit'] ?? 10,
                ['*'],
                'page',
                $request['offset'] ?? 1
            );

        if ($sellers->isEmpty()) {
            return response()->json([
                'status' => 400,
                'message' => 'No sellers found for this shop slug.',
            ]);
        }

        $sellers->map(function ($seller) {
            $seller->product?->map(function ($product) {
                $product['rating'] = $product?->reviews->pluck('rating')->sum();
                $product['rating_count'] = $product->reviews->count();
            });

            $seller['total_rating'] = $seller?->product->pluck('rating')->sum();
            $seller['rating_count'] = $seller->product->pluck('rating_count')->sum();
            $seller['average_rating'] = $seller['total_rating'] / ($seller['rating_count'] == 0 ? 1 : $seller['rating_count']);

            unset($seller['product'], $seller['orders']);
        });

        return response()->json([
            'status' => 200,
            'total_size' => $sellers->total(),
            'limit' => (int)($request['limit'] ?? 10),
            'offset' => (int)($request['offset'] ?? 1),
            'sellers' => $sellers->items(),
        ]);
    }

    public function more_sellers()
    {
        $more_seller = $this->seller->approved()->with(['shop'])
            ->inRandomOrder()
            ->take(10)->get();
        return response()->json($more_seller, 200);
    }

    public function get_seller_best_selling_products($seller_id, Request $request)
    {
        $products = ProductManager::get_seller_best_selling_products($request, $seller_id, $request['limit'], $request['offset']);
        $products['products'] = isset($products['products'][0]) ? Helpers::product_data_formatting($products['products'], true) : [];

        return response()->json($products, 200);
    }

    public function get_sellers_featured_product($seller_id, Request $request){

        $user = Helpers::get_customer($request);
        $featured_products = Product::with('reviews')
            ->withCount(['wishList' => function($query) use($user){
                $query->where('customer_id', $user != 'offline' ? $user->id : '0');
            }])
            ->where(['featured'=>'1'])
            ->when($seller_id == '0', function ($query){
                return $query->where(['added_by' => 'admin'])->active();
            })
            ->when($seller_id != '0', function ($query) use ($seller_id) {
                return $query->where(['added_by' => 'seller', 'user_id'=>$seller_id])->active();
            })
            ->paginate($request['limit'], ['*'], 'page', $request['offset']);

        $featured_products?->map(function ($product) {
            $product['reviews_count'] = $product->reviews->count();
            unset($product->reviews);
            return $product;
        });

        return [
            'total_size' => $featured_products->total(),
            'limit' => (int)$request['limit'],
            'offset' => (int)$request['offset'],
            'products' => $featured_products ? Helpers::product_data_formatting($featured_products, true) : []
        ];
    }

    public function get_sellers_recommended_products($seller_id, Request $request)
    {
        $products = Product::active()->with(['category','reviews'])
                    ->when($seller_id == '0', function ($query){
                        return $query->where(['added_by' => 'admin']);
                    })
                    ->when($seller_id != '0', function ($query) use ($seller_id) {
                        return $query->where(['added_by' => 'seller', 'user_id'=>$seller_id]);
                    })
                    ->withCount('orderDelivered')
                    ->withSum('tags', 'visit_count')
                    ->orderBy('order_delivered_count', 'desc')
                    ->orderBy('tags_sum_visit_count', 'desc')
                    ->paginate($request['limit'], ['*'], 'page', $request['offset']);

        $products?->map(function ($product) {
            $product['reviews_count'] = $product->reviews->count();
            unset($product->reviews);
            return $product;
        });


        return [
            'total_size' => $products->total(),
            'limit' => (int)$request['limit'],
            'offset' => (int)$request['offset'],
            'products' => $products ? Helpers::product_data_formatting($products, true) : []
        ];
    }
}
