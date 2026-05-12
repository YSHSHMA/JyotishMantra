<?php

namespace App\Utils;

use App\Utils\Helpers;
use App\Models\CategoryShippingCost;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Review;
use App\Models\ShippingMethod;
use App\Models\ShippingType;
use App\Models\Translation;
use Illuminate\Support\Facades\DB;

class ProductManager
{
    public static function get_product($id)
    {
        return Product::active()->with(['rating', 'seller.shop', 'tags'])->where('id', $id)->first();
    }

    public static function get_same_day_delivery($request, $limit = 10, $offset = 1, $type , $shops)
    {
        $user = Helpers::get_customer($request);
        if($type == 'featured-product'){
            $paginator = Product::whereIn('user_id', $shops)->with(['reviews'])->active()->where('featured', 1)->withCount(['orderDetails'])->paginate($limit, ['*'], 'page', $offset);
        } elseif($type == 'latest-product'){
            $paginator = Product::whereIn('user_id', $shops)->with(['reviews'])->active()->orderBy('id', 'desc')->paginate($limit, ['*'], 'page', $offset);
        } 

        return [
            'total_size' => $paginator->total(),
            'limit' => (int)$limit,
            'offset' => (int)$offset,
            'products' => $paginator->items()
        ];
    }

    public static function get_latest_products($request, $limit = 10, $offset = 1)
    {
        $user = Helpers::get_customer($request);
        $paginator = Product::active()
            ->with(['rating', 'tags', 'seller.shop', 'flashDealProducts.flashDeal'])
            ->withCount(['reviews', 'wishList' => function ($query) use ($user) {
                $query->where('customer_id', $user != 'offline' ? $user->id : '0');
            }])
            ->orderBy('id', 'desc')
            ->paginate($limit, ['*'], 'page', $offset);

        $currentDate = date('Y-m-d H:i:s');
        $paginator?->map(function ($product) use ($currentDate) {
            $flashDealStatus = 0;
            $flashDealEndDate = 0;
            if (count($product->flashDealProducts) > 0) {
                $flashDeal = null;
                foreach ($product->flashDealProducts as $flashDealData) {
                    if ($flashDealData->flashDeal) {
                        $flashDeal = $flashDealData->flashDeal;
                    }
                }
                if ($flashDeal) {
                    $startDate = date('Y-m-d H:i:s', strtotime($flashDeal->start_date));
                    $endDate = date('Y-m-d H:i:s', strtotime($flashDeal->end_date));
                    $flashDealStatus = $flashDeal->status == 1 && (($currentDate >= $startDate) && ($currentDate <= $endDate)) ? 1 : 0;
                    $flashDealEndDate = $flashDeal->end_date;
                }
            }
            $product['flash_deal_status'] = $flashDealStatus;
            $product['flash_deal_end_date'] = $flashDealEndDate;
            return $product;
        });

        return [
            'total_size' => $paginator->total(),
            'limit' => (int)$limit,
            'offset' => (int)$offset,
            'products' => $paginator->items()
        ];
    }

    public static function get_featured_products($request, $limit = 10, $offset = 1)
    {
        $user = Helpers::get_customer($request);
        $currentDate = date('Y-m-d H:i:s');
        //change review to ratting
        $paginator = Product::with(['seller.shop', 'rating', 'tags', 'flashDealProducts.flashDeal'])->active()
            ->withCount(['reviews', 'wishList' => function ($query) use ($user) {
                $query->where('customer_id', $user != 'offline' ? $user->id : '0');
            }])
            ->where('featured', 1)
            ->withCount(['orderDetails'])->orderBy('order_details_count', 'DESC')
            ->paginate($limit, ['*'], 'page', $offset);

        $currentDate = date('Y-m-d H:i:s');
        $paginator?->map(function ($product) use ($currentDate) {
            $flashDealStatus = 0;
            $flashDealEndDate = 0;
            if (count($product->flashDealProducts) > 0) {
                $flashDeal = null;
                foreach ($product->flashDealProducts as $flashDealData) {
                    if ($flashDealData->flashDeal) {
                        $flashDeal = $flashDealData->flashDeal;
                    }
                }
                if ($flashDeal) {
                    $startDate = date('Y-m-d H:i:s', strtotime($flashDeal->start_date));
                    $endDate = date('Y-m-d H:i:s', strtotime($flashDeal->end_date));
                    $flashDealStatus = $flashDeal->status == 1 && (($currentDate >= $startDate) && ($currentDate <= $endDate)) ? 1 : 0;
                    $flashDealEndDate = $flashDeal->end_date;
                }
            }
            $product['flash_deal_status'] = $flashDealStatus;
            $product['flash_deal_end_date'] = $flashDealEndDate;
            return $product;
        });

        return [
            'total_size' => $paginator->total(),
            'limit' => (int)$limit,
            'offset' => (int)$offset,
            'products' => $paginator->items()
        ];
    }

    public static function get_top_rated_products($request, $limit = 10, $offset = 1)
    {
        $user = Helpers::get_customer($request);
        //change review to ratting
        $reviews = Product::with(['seller.shop', 'flashDealProducts.flashDeal', 'rating', 'tags'])->active()
            ->withCount(['wishList' => function ($query) use ($user) {
                $query->where('customer_id', $user != 'offline' ? $user->id : '0');
            }])
            ->withCount(['reviews'])->orderBy('reviews_count', 'DESC')
            ->paginate($limit, ['*'], 'page', $offset);

        $currentDate = date('Y-m-d H:i:s');
        $reviews?->map(function ($product) use ($currentDate) {
            $flashDealStatus = 0;
            $flashDealEndDate = 0;
            if (count($product->flashDealProducts) > 0) {
                $flashDeal = null;
                foreach ($product->flashDealProducts as $flashDealData) {
                    if ($flashDealData->flashDeal) {
                        $flashDeal = $flashDealData->flashDeal;
                    }
                }
                if ($flashDeal) {
                    $startDate = date('Y-m-d H:i:s', strtotime($flashDeal->start_date));
                    $endDate = date('Y-m-d H:i:s', strtotime($flashDeal->end_date));
                    $flashDealStatus = $flashDeal->status == 1 && (($currentDate >= $startDate) && ($currentDate <= $endDate)) ? 1 : 0;
                    $flashDealEndDate = $flashDeal->end_date;
                }
            }
            $product['flash_deal_status'] = $flashDealStatus;
            $product['flash_deal_end_date'] = $flashDealEndDate;
            return $product;
        });

        return [
            'total_size' => $reviews->total(),
            'limit' => (int)$limit,
            'offset' => (int)$offset,
            'products' => $reviews
        ];
    }

    public static function get_best_selling_products($request, $limit = 10, $offset = 1)
    {
        $user = Helpers::get_customer($request);
        //change reviews to rattings
        $paginator = OrderDetail::with(['product.seller.shop', 'product.rating', 'product.reviews', 'product.flashDealProducts.flashDeal', 'product' => function ($query) use ($user) {
            $query->withCount(['wishList' => function ($query) use ($user) {
                $query->where('customer_id', $user != 'offline' ? $user->id : '0');
            }]);
        }])
            ->whereHas('product', function ($query) {
                $query->active();
            })
            ->select('product_id', DB::raw('COUNT(product_id) as count'))
            ->groupBy('product_id')
            ->orderBy("count", 'desc')
            ->paginate($limit, ['*'], 'page', $offset);

        $currentDate = date('Y-m-d H:i:s');
        $paginator?->map(function ($product) use ($currentDate) {
            if ($product->product) {
                $flashDealStatus = 0;
                $flashDealEndDate = 0;
                if (count($product->product->flashDealProducts) > 0) {
                    $flashDeal = null;
                    foreach ($product->product->flashDealProducts as $flashDealData) {
                        if ($flashDealData->flashDeal) {
                            $flashDeal = $flashDealData->flashDeal;
                        }
                    }
                    if ($flashDeal) {
                        $startDate = date('Y-m-d H:i:s', strtotime($flashDeal->start_date));
                        $endDate = date('Y-m-d H:i:s', strtotime($flashDeal->end_date));
                        $flashDealStatus = $flashDeal->status == 1 && (($currentDate >= $startDate) && ($currentDate <= $endDate)) ? 1 : 0;
                        $flashDealEndDate = $flashDeal->end_date;
                    }
                }
                $product->product['flash_deal_status'] = $flashDealStatus;
                $product->product['flash_deal_end_date'] = $flashDealEndDate;
                $product->product['reviews_count'] = $product->product->reviews->count();
                unset($product->product->reviews);
            }
            return $product;
        });

        $data = [];
        foreach ($paginator as $order) {
            $data[] = $order->product;
        }

        return [
            'total_size' => $paginator->total(),
            'limit' => (int)$limit,
            'offset' => (int)$offset,
            'products' => $data
        ];
    }

    public static function get_seller_best_selling_products($request, $seller_id, $limit = 10, $offset = 1)
    {
        $user = Helpers::get_customer($request);

        $paginator = OrderDetail::with(['product.rating', 'product' => function ($query) use ($user) {
            $query->withCount(['wishList' => function ($query) use ($user) {
                $query->where('customer_id', $user != 'offline' ? $user->id : '0');
            }]);
        }])
            ->whereHas('product', function ($query) use ($seller_id) {
                $query->when($seller_id == '0', function ($query) use ($seller_id) {
                    return $query->where(['added_by' => 'admin'])->active();
                })
                    ->when($seller_id != '0', function ($query) use ($seller_id) {
                        return $query->where(['added_by' => 'seller', 'user_id' => $seller_id])->active();
                    });
            })
            ->select('product_id', DB::raw('COUNT(product_id) as count'))
            ->groupBy('product_id')
            ->orderBy("count", 'desc')
            ->paginate($limit, ['*'], 'page', $offset);

        $data = [];
        foreach ($paginator as $order) {
            array_push($data, $order->product);
        }

        return [
            'total_size' => $paginator->total(),
            'limit' => (int)$limit,
            'offset' => (int)$offset,
            'products' => $data
        ];
    }

    public static function get_related_products($product_id, $request = null)
    {
        $user = Helpers::get_customer($request);
        $product = Product::find($product_id);
        $products = Product::active()->with(['rating', 'flashDealProducts.flashDeal', 'tags', 'seller.shop'])
            ->withCount(['reviews', 'wishList' => function ($query) use ($user) {
                $query->where('customer_id', $user != 'offline' ? $user->id : '0');
            }])
            ->where('category_ids', $product->category_ids)
            ->where('id', '!=', $product->id)
            ->limit(10)
            ->get();

        $currentDate = date('Y-m-d H:i:s');
        $products?->map(function ($product) use ($currentDate) {
            $flashDealStatus = 0;
            $flashDealEndDate = 0;
            if (count($product->flashDealProducts) > 0) {
                $flashDeal = null;
                foreach ($product->flashDealProducts as $flashDealData) {
                    if ($flashDealData->flashDeal) {
                        $flashDeal = $flashDealData->flashDeal;
                    }
                }
                if ($flashDeal) {
                    $startDate = date('Y-m-d H:i:s', strtotime($flashDeal->start_date));
                    $endDate = date('Y-m-d H:i:s', strtotime($flashDeal->end_date));
                    $flashDealStatus = $flashDeal->status == 1 && (($currentDate >= $startDate) && ($currentDate <= $endDate)) ? 1 : 0;
                    $flashDealEndDate = $flashDeal->end_date;
                }
            }
            $product['flash_deal_status'] = $flashDealStatus;
            $product['flash_deal_end_date'] = $flashDealEndDate;
            return $product;
        });

        return $products;
    }

    public static function search_products($request, $name, $category = 'all', $limit = 10, $offset = 1)
    {
        $key = explode(' ', $name);;

        $user = Helpers::get_customer($request);

        $paginator = Product::active()->with(['rating', 'tags'])
            ->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%")
                        ->orWhereHas('tags', function ($query) use ($key) {
                            $query->where(function ($q) use ($key) {
                                foreach ($key as $value) {
                                    $q->where('tag', 'like', "%{$value}%");
                                }
                            });
                        });
                }
            })
            ->withCount(['wishList' => function ($query) use ($user) {
                $query->where('customer_id', $user != 'offline' ? $user->id : '0');
            }]);

        if (isset($category) && $category != 'all') {
            $products = $paginator->get();
            $product_ids = [];
            foreach ($products as $product) {
                foreach (json_decode($product['category_ids'], true) as $category_id) {
                    if ($category_id['id'] == $category) {
                        array_push($product_ids, $product['id']);
                    }
                }
            }
            $query = $paginator->whereIn('id', $product_ids);
        } else {
            $query = $paginator;
        }

        $fetched = $query->paginate($limit, ['*'], 'page', $offset);

        return [
            'total_size' => $fetched->total(),
            'limit' => (int)$limit,
            'offset' => (int)$offset,
            'products' => $fetched->items()
        ];
    }

    public static function suggestion_products($name, $limit = 10, $offset = 1)
    {
        $key = [base64_decode($name)];

        $product = Product::select('name')
            ->active()
            ->with(['rating', 'tags'])->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%")
                        ->orWhereHas('tags', function ($query) use ($key) {
                            $query->where(function ($q) use ($key) {
                                foreach ($key as $value) {
                                    $q->where('tag', 'like', "%{$value}%");
                                }
                            });
                        });
                }
            })->paginate($limit, ['*'], 'page', $offset);


        return [
            'products' => $product->items()
        ];
    }

    public static function search_products_web($name, $category = 'all', $limit = 10, $offset = 1)
    {

        $paginator = Product::active()->with(['rating', 'tags'])->select('products.*')->selectRaw("'product' as type")->where(function ($q) use ($name) {
            $q->orWhere('name', 'like', "%{$name}%")
                ->orWhereHas('tags', function ($query) use ($name) {
                    $query->where('tag', 'like', "%{$name}%");
                });
        });

        if (isset($category) && $category != 'all') {
            $products = $paginator->get();
            $product_ids = [];
            foreach ($products as $product) {
                foreach (json_decode($product['category_ids'], true) as $category_id) {
                    if ($category_id['id'] == $category) {
                        array_push($product_ids, $product['id']);
                    }
                }
            }
            $query = $paginator->whereIn('id', $product_ids);
        } else {
            $query = $paginator;
        }

        $fetched = $query->paginate($limit, ['*'], 'page', $offset);

        return [
            'total_size' => $fetched->total(),
            'limit' => (int)$limit,
            'offset' => (int)$offset,
            'products' => $fetched->items()
        ];
    }

    public static function translated_product_search($name, $category = 'all', $limit = 10, $offset = 1)
    {
        $name = base64_decode($name);
        $product_ids = Translation::where('translationable_type', 'App\Models\Product')
            ->where('key', 'name')
            ->where('value', 'like', "%{$name}%")
            ->pluck('translationable_id');

        $paginator = Product::with('tags')
            ->WhereIn('id', $product_ids);

        $query = $paginator;
        if ($category != 'all') {
            $products = $paginator->get();
            $product_ids = [];
            foreach ($products as $product) {
                foreach (json_decode($product['category_ids'], true) as $category_id) {
                    if ($category_id['id'] == $category) {
                        array_push($product_ids, $product['id']);
                    }
                }
            }
            $query = $paginator->whereIn('id', $product_ids);
        }

        $fetched = $query->paginate($limit, ['*'], 'page', $offset);

        return [
            'total_size' => $fetched->total(),
            'limit' => (int)$limit,
            'offset' => (int)$offset,
            'products' => $fetched->items()
        ];
    }

    public static function translated_product_search_web($name, $category = 'all', $limit = 10, $offset = 1)
    {
        try {
            $product_ids = Translation::where('translationable_type', 'App\Models\Product')
                ->where('key', 'name')
                ->where(function ($q) use ($name) {
                    $q->where('name', 'like', "%{$name}%");
                })
                ->orWhereHas('tags', function ($query) use ($name) {
                    $query->where('tag', 'like', "%{$name}%");
                })
                ->pluck('translationable_id');

            $paginator = Product::with('tags')
                ->WhereIn('id', $product_ids);

            $query = $paginator;
            if ($category != 'all') {
                $products = $paginator->get();
                $product_ids = [];
                foreach ($products as $product) {
                    foreach (json_decode($product['category_ids'], true) as $category_id) {
                        if ($category_id['id'] == $category) {
                            array_push($product_ids, $product['id']);
                        }
                    }
                }
                $query = $paginator->whereIn('id', $product_ids);
            }

            $fetched = $query->paginate($limit, ['*'], 'page', $offset);

            return [
                'total_size' => $fetched->total(),
                'limit' => (int)$limit,
                'offset' => (int)$offset,
                'products' => $fetched->items()
            ];
        } catch (\Exception $e) {
            return [
                'total_size' => 0,
                'limit' => (int) $limit,
                'offset' => (int) $offset,
                'products' => [],
                'error' => 'An error occurred while fetching products. Please try again later.'
            ];
        }
    }

    public static function product_image_path($image_type)
    {
        $path = '';
        if ($image_type == 'thumbnail') {
            $path = asset('storage/app/public/product/thumbnail');
        } elseif ($image_type == 'product') {
            $path = asset('storage/app/public/product');
        }
        return $path;
    }

    public static function get_product_review($id)
    {
        $reviews = Review::where('product_id', $id)
            ->where('status', 1)->get();
        return $reviews;
    }

    public static function get_rating($reviews)
    {
        $rating5 = 0;
        $rating4 = 0;
        $rating3 = 0;
        $rating2 = 0;
        $rating1 = 0;
        foreach ($reviews as $key => $review) {
            if ($review->rating == 5) {
                $rating5 += 1;
            }
            if ($review->rating == 4) {
                $rating4 += 1;
            }
            if ($review->rating == 3) {
                $rating3 += 1;
            }
            if ($review->rating == 2) {
                $rating2 += 1;
            }
            if ($review->rating == 1) {
                $rating1 += 1;
            }
        }
        return [$rating5, $rating4, $rating3, $rating2, $rating1];
    }

    public static function get_overall_rating($reviews)
    {
        $totalRating = count($reviews);
        $rating = 0;
        foreach ($reviews as $key => $review) {
            $rating += $review->rating;
        }
        if ($totalRating == 0) {
            $overallRating = 0;
        } else {
            $overallRating = number_format($rating / $totalRating, 2);
        }

        return [$overallRating, $totalRating];
    }

    public static function get_shipping_methods($product)
    {
        if ($product['added_by'] == 'seller') {
            $methods = ShippingMethod::where(['creator_id' => $product['user_id']])->where(['status' => 1])->get();
            if ($methods->count() == 0) {
                $methods = ShippingMethod::where(['creator_type' => 'admin'])->where(['status' => 1])->get();
            }
        } else {
            $methods = ShippingMethod::where(['creator_type' => 'admin'])->where(['status' => 1])->get();
        }

        return $methods;
    }

    public static function get_seller_products($seller_id, $request)
    {
        $user = Helpers::get_customer($request);
        $categories = $request->has('category') ? json_decode($request->category) : [];

        $limit = $request['limit'];
        $offset = $request['offset'];
        $products = Product::active()->with(['rating', 'flashDealProducts.flashDeal', 'tags'])
            ->withCount(['reviews', 'wishList' => function ($query) use ($user) {
                $query->where('customer_id', $user != 'offline' ? $user->id : '0');
            }])
            ->when($seller_id == 0, function ($query) {
                return $query->where(['added_by' => 'admin']);
            })
            ->when($seller_id != 0, function ($query) use ($seller_id) {
                return $query->where(['added_by' => 'seller'])
                    ->where('user_id', $seller_id);
            })
            ->when($request->search, function ($query) use ($request) {
                $key = explode(' ', $request->search);
                foreach ($key as $value) {
                    $query->where('name', 'like', "%{$value}%");
                }
            })
            ->when($request->has('brand_ids') && json_decode($request->brand_ids), function ($query) use ($request) {
                $query->whereIn('brand_id', json_decode($request->brand_ids));
            })
            ->when($request->has('category') && $categories, function ($query) use ($categories) {
                $query->where(function ($query) use ($categories) {
                    return $query->whereIn('category_id', $categories)
                        ->orWhereIn('sub_category_id', $categories)
                        ->orWhereIn('sub_sub_category_id', $categories);
                });
            })
            ->when($request->has('product_id') && $request['product_id'], function ($query) use ($request) {
                return $query->whereNotIn('id', [$request['product_id']]);
            })
            ->orderBy('id', 'desc')
            ->paginate($limit, ['*'], 'page', $offset);

        $currentDate = date('Y-m-d H:i:s');
        $products?->map(function ($product) use ($currentDate) {
            $flashDealStatus = 0;
            $flashDealEndDate = 0;
            if (count($product->flashDealProducts) > 0) {
                $flashDeal = null;
                foreach ($product->flashDealProducts as $flashDealData) {
                    if ($flashDealData->flashDeal) {
                        $flashDeal = $flashDealData->flashDeal;
                    }
                }
                if ($flashDeal) {
                    $startDate = date('Y-m-d H:i:s', strtotime($flashDeal->start_date));
                    $endDate = date('Y-m-d H:i:s', strtotime($flashDeal->end_date));
                    $flashDealStatus = $flashDeal->status == 1 && (($currentDate >= $startDate) && ($currentDate <= $endDate)) ? 1 : 0;
                    $flashDealEndDate = $flashDeal->end_date;
                }
            }
            $product['flash_deal_status'] = $flashDealStatus;
            $product['flash_deal_end_date'] = $flashDealEndDate;
            return $product;
        });

        return [
            'total_size' => $products->total(),
            'limit' => (int)$limit,
            'offset' => (int)$offset,
            'products' => $products->items()
        ];
    }

    public static function get_seller_all_products($seller_id, $limit = 10, $offset = 1)
    {
        $paginator = Product::with(['rating', 'tags'])
            ->where(['user_id' => $seller_id, 'added_by' => 'seller'])
            ->orderBy('id', 'desc')
            ->paginate($limit, ['*'], 'page', $offset);

        return [
            'total_size' => $paginator->total(),
            'limit' => (int)$limit,
            'offset' => (int)$offset,
            'products' => $paginator->items()
        ];
    }

    public static function get_discounted_product($request, $limit = 10, $offset = 1)
    {
        $user = Helpers::get_customer($request);
        //change review to ratting
        $paginator = Product::with(['rating', 'reviews', 'tags'])->active()
            ->withCount(['reviews', 'wishList' => function ($query) use ($user) {
                $query->where('customer_id', $user != 'offline' ? $user->id : '0');
            }])
            ->where('discount', '!=', 0)
            ->orderBy('id', 'desc')
            ->paginate($limit, ['*'], 'page', $offset);

        return [
            'total_size' => $paginator->total(),
            'limit' => (int)$limit,
            'offset' => (int)$offset,
            'products' => $paginator->items()
        ];
    }

    public static function export_product_reviews($data)
    {
        $storage = [];
        foreach ($data as $item) {
            $storage[] = [
                'product' => $item->product['name'] ?? '',
                'customer' => isset($item->customer) ? $item->customer->f_name . ' ' . $item->customer->l_name : '',
                'comment' => $item->comment,
                'rating' => $item->rating
            ];
        }
        return $storage;
    }

    public static function get_user_total_product($added_by, $user_id)
    {
        $total_product = Product::active()->where(['added_by' => $added_by, 'user_id' => $user_id])->count();
        return $total_product;
    }

    public static function get_products_rating_quantity($products)
    {
        $rating5 = 0;
        $rating4 = 0;
        $rating3 = 0;
        $rating2 = 0;
        $rating1 = 0;

        foreach ($products as $product) {
            $review = Review::where(['product_id' => $product])->avg('rating');
            if ($review == 5) {
                $rating5 += 1;
            } else if ($review >= 4 && $review < 5) {
                $rating4 += 1;
            } else if ($review >= 3 && $review < 4) {
                $rating3 += 1;
            } else if ($review >= 2 && $review < 3) {
                $rating2 += 1;
            } else if ($review >= 1 && $review < 2) {
                $rating1 += 1;
            }
        }

        return [$rating5, $rating4, $rating3, $rating2, $rating1];
    }

    public static function get_products_delivery_charge($product, $quantity)
    {
        $delivery_cost = 0;
        $shipping_model = Helpers::get_business_settings('shipping_method');
        $shipping_type = "";

        if ($shipping_model == "inhouse_shipping") {
            $shipping_type = ShippingType::where(['seller_id' => 0])->first();
            if ($shipping_type->shipping_type == "category_wise") {
                $cat_id = $product->category_id;
                $CategoryShippingCost = CategoryShippingCost::where(['seller_id' => 0, 'category_id' => $cat_id])->first();
                $delivery_cost = $CategoryShippingCost ?
                    ($CategoryShippingCost->multiply_qty != 0 ? ($CategoryShippingCost->cost * $quantity) : $CategoryShippingCost->cost)
                    : 0;
            } elseif ($shipping_type->shipping_type == "product_wise") {
                $delivery_cost = $product->multiply_qty != 0 ? ($product->shipping_cost * $quantity) : $product->shipping_cost;
            } elseif ($shipping_type->shipping_type == 'order_wise') {
                $max_order_wise_shipping_cost = ShippingMethod::where(['creator_id' => 1, 'creator_type' => 'admin', 'status' => 1])->max('cost');
                $min_order_wise_shipping_cost = ShippingMethod::where(['creator_id' => 1, 'creator_type' => 'admin', 'status' => 1])->min('cost');
            }
        } elseif ($shipping_model == "sellerwise_shipping") {

            if ($product->added_by == "admin") {
                $shipping_type = ShippingType::where('seller_id', '=', 0)->first();
            } else {
                $shipping_type = ShippingType::where('seller_id', '!=', 0)->where(['seller_id' => $product->user_id])->first();
            }

            if ($shipping_type) {
                $shipping_type = $shipping_type ?? ShippingType::where('seller_id', '=', 0)->first();
                if ($shipping_type->shipping_type == "category_wise") {
                    $cat_id = $product->category_id;
                    if ($product->added_by == "admin") {
                        $CategoryShippingCost = CategoryShippingCost::where(['seller_id' => 0, 'category_id' => $cat_id])->first();
                    } else {
                        $CategoryShippingCost = CategoryShippingCost::where(['seller_id' => $product->user_id, 'category_id' => $cat_id])->first();
                    }

                    $delivery_cost = $CategoryShippingCost ?
                        ($CategoryShippingCost->multiply_qty != 0 ? ($CategoryShippingCost->cost * $quantity) : $CategoryShippingCost->cost)
                        : 0;
                } elseif ($shipping_type->shipping_type == "product_wise") {
                    $delivery_cost = $product->multiply_qty != 0 ? ($product->shipping_cost * $quantity) : $product->shipping_cost;
                } elseif ($shipping_type->shipping_type == 'order_wise') {
                    $max_order_wise_shipping_cost = ShippingMethod::where(['creator_id' => $product->user_id, 'creator_type' => $product->added_by, 'status' => 1])->max('cost');
                    $min_order_wise_shipping_cost = ShippingMethod::where(['creator_id' => $product->user_id, 'creator_type' => $product->added_by, 'status' => 1])->min('cost');
                }
            }
        }
        $data = [
            'delivery_cost' => $delivery_cost,
            'delivery_cost_max' => isset($max_order_wise_shipping_cost) ? $max_order_wise_shipping_cost : 0,
            'delivery_cost_min' => isset($min_order_wise_shipping_cost) ? $min_order_wise_shipping_cost : 0,
            'shipping_type' => $shipping_type->shipping_type ?? '',
        ];
        return $data;
    }

    public static function get_colors_form_products()
    {
        $colors_merge = [];

        $colors_collection = Product::active()
            ->where('colors', '!=', '[]')
            ->pluck('colors')
            ->unique()
            ->toArray();

        foreach ($colors_collection as $color_json) {
            $color_array = json_decode($color_json, true);
            if ($color_array) {
                $colors_merge = array_merge($colors_merge, $color_array);
            }
        }
        $colors = array_unique($colors_merge);

        return $colors;
    }
}