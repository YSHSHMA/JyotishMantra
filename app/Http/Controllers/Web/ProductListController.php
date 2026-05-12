<?php

namespace App\Http\Controllers\Web;

use App\Utils\Helpers;
use App\Http\Controllers\Controller;
use App\Models\OrderDetail;
use App\Models\Review;
use App\Models\Shop;
use App\Models\Brand;
use App\Models\Category;
use App\Models\FlashDeal;
use App\Models\FlashDealProduct;
use App\Models\Product;
use App\Models\Translation;
use App\Models\Wishlist;
use App\Models\Banner;
use App\Models\DealOfTheDay;
use App\Models\Seller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductListController extends Controller
{
    public function __construct(
        private Banner       $banner,
    ) {}

    // public function products(Request $request)
    // {
    //     $theme_name = theme_root_path();

    //     return match ($theme_name) {
    //         'default' => self::default_theme($request),
    //         'theme_aster' => self::theme_aster($request),
    //         'theme_fashion' => self::theme_fashion($request),
    //         'theme_all_purpose' => self::theme_all_purpose($request),
    //     };
    // }

    // public function default_theme($request)
    // {

    //     $request['sort_by'] == null ? $request['sort_by'] == 'latest' : $request['sort_by'];

    //     $porduct_data = Product::active()->with(['reviews']);

    //     if ($request['data_from'] == 'category') {
    //         $products = $porduct_data->get();
    //         $product_ids = [];
    //         foreach ($products as $product) {
    //             foreach (json_decode($product['category_ids'], true) as $category) {
    //                 if ($category['id'] == $request['id']) {
    //                     array_push($product_ids, $product['id']);
    //                 }
    //             }
    //         }
    //         $query = $porduct_data->whereIn('id', $product_ids);
    //     }

    //     if ($request['data_from'] == 'brand') {
    //         $query = $porduct_data->where('brand_id', $request['id']);
    //     }

    //     if (!$request->has('data_from') || empty($request['data_from']) || $request['data_from'] == 'latest') {
    //         $query = $porduct_data;
    //     }

    //     if ($request['data_from'] == 'top-rated') {
    //         $reviews = Review::select('product_id', DB::raw('AVG(rating) as count'))
    //             ->groupBy('product_id')
    //             ->orderBy("count", 'desc')->get();
    //         $product_ids = [];
    //         foreach ($reviews as $review) {
    //             array_push($product_ids, $review['product_id']);
    //         }
    //         $query = $porduct_data->whereIn('id', $product_ids);
    //     }

    //     if ($request['data_from'] == 'best-selling') {
    //         $details = OrderDetail::with('product')
    //             ->select('product_id', DB::raw('COUNT(product_id) as count'))
    //             ->groupBy('product_id')
    //             ->orderBy("count", 'desc')
    //             ->get();
    //         $product_ids = [];
    //         foreach ($details as $detail) {
    //             array_push($product_ids, $detail['product_id']);
    //         }
    //         $query = $porduct_data->whereIn('id', $product_ids);
    //     }

    //     if ($request['data_from'] == 'most-favorite') {
    //         $details = Wishlist::with('product')
    //             ->select('product_id', DB::raw('COUNT(product_id) as count'))
    //             ->groupBy('product_id')
    //             ->orderBy("count", 'desc')
    //             ->get();
    //         $product_ids = [];
    //         foreach ($details as $detail) {
    //             array_push($product_ids, $detail['product_id']);
    //         }
    //         $query = $porduct_data->whereIn('id', $product_ids);
    //     }

    //     if ($request['data_from'] == 'featured') {
    //         $query = Product::with(['reviews'])->active()->where('featured', 1);
    //     }

    //     if ($request['data_from'] == 'featured_deal') {
    //         $featured_deal_id = FlashDeal::where(['status' => 1])->where(['deal_type' => 'feature_deal'])->pluck('id')->first();
    //         $featured_deal_product_ids = FlashDealProduct::where('flash_deal_id', $featured_deal_id)->pluck('product_id')->toArray();
    //         $query = Product::with(['reviews'])->withCount('reviews')->active()->whereIn('id', $featured_deal_product_ids);
    //     }

    //     if ($request['data_from'] == 'search') {
    //         $key = explode(' ', $request['name']);
    //         $product_ids = Product::where(function ($q) use ($key) {
    //             foreach ($key as $value) {
    //                 $q->orWhere('name', 'like', "%{$value}%")
    //                     ->orWhereHas('tags', function ($query) use ($value) {
    //                         $query->where('tag', 'like', "%{$value}%");
    //                     });
    //             }
    //         })->pluck('id');

    //         if ($product_ids->count() == 0) {
    //             $product_ids = Translation::where('translationable_type', 'App\Models\Product')
    //                 ->where('key', 'name')
    //                 ->where(function ($q) use ($key) {
    //                     foreach ($key as $value) {
    //                         $q->orWhere('value', 'like', "%{$value}%");
    //                     }
    //                 })
    //                 ->pluck('translationable_id');
    //         }

    //         $query = $porduct_data->WhereIn('id', $product_ids);
    //     }

    //     if ($request['data_from'] == 'discounted') {
    //         $query = Product::with(['reviews'])->withCount('reviews')->active()->where('discount', '!=', 0);
    //     }

    //     if ($request['sort_by'] == 'latest') {
    //         $fetched = $query->latest();
    //     } elseif ($request['sort_by'] == 'low-high') {
    //         $fetched = $query->orderBy('unit_price', 'ASC');
    //     } elseif ($request['sort_by'] == 'high-low') {
    //         $fetched = $query->orderBy('unit_price', 'DESC');
    //     } elseif ($request['sort_by'] == 'a-z') {
    //         $fetched = $query->orderBy('name', 'ASC');
    //     } elseif ($request['sort_by'] == 'z-a') {
    //         $fetched = $query->orderBy('name', 'DESC');
    //     } else {
    //         $fetched = $query->latest();
    //     }

    //     if ($request['min_price'] != null || $request['max_price'] != null) {
    //         $fetched = $fetched->whereBetween('unit_price', [Helpers::convert_currency_to_usd($request['min_price']), Helpers::convert_currency_to_usd($request['max_price'])]);
    //     }

    //     $data = [
    //         'id' => $request['id'],
    //         'name' => $request['name'],
    //         'data_from' => $request['data_from'],
    //         'sort_by' => $request['sort_by'],
    //         'page_no' => $request['page'],
    //         'min_price' => $request['min_price'],
    //         'max_price' => $request['max_price'],
    //     ];

    //     $products = $fetched->paginate(20)->appends($data);

    //     if ($request->ajax()) {

    //         return response()->json([
    //             'total_product' => $products->total(),
    //             'view' => view('web-views.products._ajax-products', compact('products'))->render()
    //         ], 200);
    //     }
    //     if ($request['data_from'] == 'category') {
    //         $data['brand_name'] = Category::find((int)$request['id'])->name;
    //     }
    //     if ($request['data_from'] == 'brand') {
    //         $brand_data = Brand::active()->find((int)$request['id']);
    //         if ($brand_data) {
    //             $data['brand_name'] = $brand_data->name;
    //         } else {
    //             Toastr::warning(translate('not_found'));
    //             return redirect('/');
    //         }
    //     }

    //     return view(VIEW_FILE_NAMES['products_view_page'], compact('products', 'data'));
    // }

    // start same day delivery
    public function same_day_delivery(Request $request)
    {
        $theme_name = theme_root_path();
        $main_banner = $this->banner->where(['banner_type' => 'Main Banner', 'theme' => $theme_name, 'published' => 1])->latest()->get();
        return view('web-views.same-day-delivery.index',compact('main_banner'));
    }

    public function same_day_delivery_index(Request $request)
    {
        $decimal_point_settings = !empty(getWebConfig(name: 'decimal_point_settings')) ? getWebConfig(name: 'decimal_point_settings') : 0;
        $shops = Shop::where([
                'city_name' => $request->city,
                'vacation_status' => 0,
                'temporary_close' => 0
            ])
            ->pluck('seller_id');

        if ($shops->isEmpty()) {
            return response()->json([
                'status' => false,
            ]);
        }
        
        $categoryIds = Product::whereIn('user_id', $shops)
            ->where('status', 1)
            ->pluck('category_id')
            ->unique();
        $categories = Category::with('childes.childes')->where('position', 0)->whereIn('id', $categoryIds)->where('home_status', 1)->priority()->take(8)->get();

        $categoryProducts = Product::whereIn('user_id', $shops)
            ->where('status', 1)
            ->with(['reviews', 'category'])
            ->active()
            ->get()
            ->groupBy('category_id');

        $featured_products = Product::whereIn('user_id', $shops)
            ->with(['reviews'])->active()
            ->where('featured', 1)
            ->withCount(['orderDetails'])->orderBy('order_details_count', 'DESC')
            ->take(12)
            ->get();

        $sellers = Seller::whereIn('id', $shops)
        ->approved()->with(['shop', 'orders', 'product.reviews'])
        // ->whereHas('orders', function ($query) {
        //     $query->where('seller_is', 'seller');
        // })
        ->withCount(['orders', 'product' => function ($query) {
            $query->active();
        }])->orderBy('orders_count', 'DESC')->get();

        $sellers?->map(function ($seller) {
            $seller->product?->map(function ($product) {
                $product['rating'] = $product?->reviews->pluck('rating')->sum();
                $product['review_count'] = $product->reviews->count();
            });
            $seller['total_rating'] = $seller?->product->pluck('rating')->sum();
            $seller['review_count'] = $seller->product->pluck('review_count')->sum();
            $seller['average_rating'] = $seller['total_rating'] / ($seller['review_count'] == 0 ? 1 : $seller['review_count']);
        });
            
        $product = Product::active()->inRandomOrder()->first();
        $deal_of_the_day = DealOfTheDay::join('products', 'products.id', '=', 'deal_of_the_days.product_id')->select('deal_of_the_days.*', 'products.unit_price')->where('products.status', 1)->where('deal_of_the_days.status', 1)->first();
        $latest_products = Product::whereIn('user_id', $shops)->with(['reviews'])->active()->orderBy('id', 'desc')->take(8)->get();

        return response()->json([
            'status' => true,
            'category_view' => view('web-views.products._sdd-category', compact('categories'))->render(),
            'category_product_view' => view('web-views.products._sdd-category-product', compact('categoryProducts'))->render(),
            'featured_view' => view('web-views.products._sdd-featured-products', compact('featured_products'))->render(),
            'seller_view' => view('web-views.products._sdd-seller', compact('sellers'))->render(),
            'latest_view' => view('web-views.partials._deal-of-the-day', compact('decimal_point_settings','product','deal_of_the_day','latest_products'))->render()
        ]);
    }

    public function same_day_delivery_products(Request $request)
    {
        $request->validate([
            'city' => 'required',
            'data_from' => 'required|in:category,featured,latest',
        ]);

        $data_from = $request->data_from;
        $sellers = "";
        $products = "";

        $shops = Shop::where([
            'city_name' => $request->city,
            'vacation_status' => 0,
            'temporary_close' => 0
        ])
        ->pluck('seller_id');

        if ($shops->isEmpty()) {
            Toastr::error(translate('product not found'));
            return back();
        }
        
        if($data_from == 'category'){
            $products = Product::whereIn('user_id', $shops)
            ->where('status', 1)
            ->with(['reviews', 'category'])
            ->active()
            ->get()
            ->groupBy('category_id');

        } elseif($data_from == 'featured'){
            $products = Product::whereIn('user_id', $shops)
                ->with(['reviews'])
                ->active()
                ->where('featured', 1)
                ->orderBy('id', 'desc')
                ->get();

        } elseif($data_from == 'latest'){
            $products = Product::whereIn('user_id', $shops)
            ->with(['reviews'])
            ->active()
            ->orderBy('id', 'desc')
            ->get();

        } elseif($data_from == 'sellers'){
            $sellers = Seller::whereIn('id', $shops)
            ->approved()->with(['shop', 'orders', 'product.reviews'])
            // ->whereHas('orders', function ($query) {
            //     $query->where('seller_is', 'seller');
            // })
            ->withCount(['orders', 'product' => function ($query) {
                $query->active();
            }])->orderBy('orders_count', 'DESC')->get();

            $sellers?->map(function ($seller) {
                $seller->product?->map(function ($product) {
                    $product['rating'] = $product?->reviews->pluck('rating')->sum();
                    $product['review_count'] = $product->reviews->count();
                });
                $seller['total_rating'] = $seller?->product->pluck('rating')->sum();
                $seller['review_count'] = $seller->product->pluck('review_count')->sum();
                $seller['average_rating'] = $seller['total_rating'] / ($seller['review_count'] == 0 ? 1 : $seller['review_count']);
            });
        }


        return view('web-views.same-day-delivery.product',compact('data_from','products','sellers'));
    }

    public function same_day_delivery_sellers(Request $request)
    {
        $request->validate([
            'city' => 'required'
        ]);

        $shops = Shop::where([
            'city_name' => $request->city,
            'vacation_status' => 0,
            'temporary_close' => 0
        ])
        ->pluck('seller_id');

        if ($shops->isEmpty()) {
            Toastr::error(translate('sellers not found'));
            return back();
        }
        
        $sellers = Seller::whereIn('id', $shops)
        ->approved()->with(['shop', 'orders', 'product.reviews'])
        // ->whereHas('orders', function ($query) {
        //     $query->where('seller_is', 'seller');
        // })
        ->withCount(['orders', 'product' => function ($query) {
            $query->active();
        }])->orderBy('orders_count', 'DESC')
        ->get();

        $sellers?->map(function ($seller) {
            $seller->product?->map(function ($product) {
                $product['rating'] = $product?->reviews->pluck('rating')->sum();
                $product['review_count'] = $product->reviews->count();
            });
            $seller['total_rating'] = $seller?->product->pluck('rating')->sum();
            $seller['review_count'] = $seller->product->pluck('review_count')->sum();
            $seller['average_rating'] = $seller['total_rating'] / ($seller['review_count'] == 0 ? 1 : $seller['review_count']);
        });

        // dd($sellers->toArray());

        return view('web-views.same-day-delivery.seller',compact('sellers'));
    }


    public function products(Request $request, $slug = null)
    {
        if ($request['types'] == 'tour') {
            $tour = \App\Models\TourVisits::where('tour_name', $request['name'])->first();
            if (!$tour) {
                return redirect()->to('/');
            }
            return redirect()->route('tour.tourvisit', ['id' => $tour['slug']]);
        } elseif ($request['types'] == 'darshan') {
            $darshan = \App\Models\temple::where('name', $request['name'])->first();
            if (!$darshan) {
                return redirect()->to('/');
            }
            return redirect()->route('temple-details', ['slug' => $darshan['slug']]);
        }
        if ($request['data_from'] == 'category') {
            $request['id'] = Category::where('slug', $request['slug'])->first()['id'] ?? '';
        }
        if ($request['data_from'] == 'brand') {
            $brand_data = Brand::active()->where('slug', $request['slug'])->first()['id'] ?? '';
            if ($brand_data) {
                $request['id'] = $brand_data;
            } else {
                Toastr::warning(translate('not_found'));
                return redirect('/');
            }
        }

        $theme_name = theme_root_path();

        return match ($theme_name) {
            'default' => $this->default_theme($request),
            'theme_aster' => self::theme_aster($request),
            'theme_fashion' => self::theme_fashion($request),
            'theme_all_purpose' => self::theme_all_purpose($request),
            default => abort(404, 'Theme not found'),
        };
    }

    // public function productsById(Request $request)
    // {
    //     $category = Category::findOrFail($request->id);
    //     return redirect()->route('products', [
    //         'slug' => $category->slug,
    //         'data_from' => $request->data_from,
    //         'page' => $request->page,
    //     ]);
    // }

    public function default_theme($request)
    {
        $request['sort_by'] = $request['sort_by'] ?? 'latest';
        $porduct_data = Product::active()->with(['reviews']);
        $query = $porduct_data;

        if ($request['data_from'] == 'category') {
            $products = $porduct_data->get();
            $product_ids = [];
            foreach ($products as $product) {
                foreach (json_decode($product['category_ids'], true) as $category) {
                    if ($category['id'] == $request['id']) {
                        $product_ids[] = $product['id'];
                    }
                }
            }
            $query = $porduct_data->whereIn('id', $product_ids);
        } elseif ($request['data_from'] == 'brand') {
            $query = $porduct_data->where('brand_id', $request['id']);
        } elseif ($request['data_from'] == 'top-rated') {
            $product_ids = Review::select('product_id', DB::raw('AVG(rating) as count'))
                ->groupBy('product_id')
                ->orderBy("count", 'desc')
                ->pluck('product_id')
                ->toArray();
            $query = $porduct_data->whereIn('id', $product_ids);
        } elseif ($request['data_from'] == 'best-selling') {
            $product_ids = OrderDetail::select('product_id', DB::raw('COUNT(product_id) as count'))
                ->groupBy('product_id')
                ->orderBy("count", 'desc')
                ->pluck('product_id')
                ->toArray();
            $query = $porduct_data->whereIn('id', $product_ids);
        } elseif ($request['data_from'] == 'most-favorite') {
            $product_ids = Wishlist::select('product_id', DB::raw('COUNT(product_id) as count'))
                ->groupBy('product_id')
                ->orderBy("count", 'desc')
                ->pluck('product_id')
                ->toArray();
            $query = $porduct_data->whereIn('id', $product_ids);
        } elseif ($request['data_from'] == 'featured') {
            $query = Product::with(['reviews'])->active()->where('featured', 1);
        } elseif ($request['data_from'] == 'featured_deal') {
            $featured_deal_id = FlashDeal::where('status', 1)->where('deal_type', 'feature_deal')->pluck('id')->first();
            $product_ids = FlashDealProduct::where('flash_deal_id', $featured_deal_id)->pluck('product_id')->toArray();
            $query = Product::with(['reviews'])->withCount('reviews')->active()->whereIn('id', $product_ids);
        } elseif ($request['data_from'] == 'search') {
            $key = explode(' ', $request['name']);
            $product_ids = Product::where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%")
                        ->orWhereHas('tags', function ($query) use ($value) {
                            $query->where('tag', 'like', "%{$value}%");
                        });
                }
            })->pluck('id');

            if ($product_ids->count() == 0) {
                $product_ids = Translation::where('translationable_type', 'App\Models\Product')
                    ->where('key', 'name')
                    ->where(function ($q) use ($key) {
                        foreach ($key as $value) {
                            $q->orWhere('value', 'like', "%{$value}%");
                        }
                    })
                    ->pluck('translationable_id');
            }

            $query = $porduct_data->whereIn('id', $product_ids);
        } elseif ($request['data_from'] == 'discounted') {
            $query = Product::with(['reviews'])->withCount('reviews')->active()->where('discount', '!=', 0);
        }

        // Sorting
        $sort_by = $request['sort_by'];
        $fetched = match ($sort_by) {
            'low-high' => $query->orderBy('unit_price', 'ASC'),
            'high-low' => $query->orderBy('unit_price', 'DESC'),
            'a-z' => $query->orderBy('name', 'ASC'),
            'z-a' => $query->orderBy('name', 'DESC'),
            default => $query->latest(),
        };

        if ($request['min_price'] || $request['max_price']) {
            $fetched = $fetched->whereBetween('unit_price', [
                Helpers::convert_currency_to_usd($request['min_price']),
                Helpers::convert_currency_to_usd($request['max_price']),
            ]);
        }

        $data = [
            'id' => $request['id'],
            'slug' => $request['slug'] ?? null,
            'name' => $request['name'] ?? null,
            'data_from' => $request['data_from'] ?? null,
            'sort_by' => $request['sort_by'] ?? null,
            'page_no' => $request['page'] ?? 1,
            'min_price' => $request['min_price'] ?? null,
            'max_price' => $request['max_price'] ?? null,
        ];

        $products = $fetched->paginate(12)->appends($data);

        if ($request->ajax()) {
            return response()->json([
                'total_product' => $products->total(),
                'view' => view('web-views.products._ajax-products', compact('products'))->render()
            ], 200);
        }

        if ($request['data_from'] == 'category') {
            $data['brand_name'] = Category::find((int)$request['id'])->name ?? null;
        }

        if ($request['data_from'] == 'brand') {
            $brand = Brand::active()->find((int)$request['id']);
            if ($brand) {
                $data['brand_name'] = $brand->name;
            } else {
                Toastr::warning(translate('not_found'));
                return redirect('/');
            }
        }

        return view(VIEW_FILE_NAMES['products_view_page'], compact('products', 'data'));
    }

    public function theme_aster($request)
    {
        $request['sort_by'] == null ? $request['sort_by'] == 'latest' : $request['sort_by'];
        $porduct_data = Product::active()->with([
            'reviews',
            'rating',
            'seller.shop',
            'wishList' => function ($query) {
                return $query->where('customer_id', Auth::guard('customer')->user()->id ?? 0);
            },
            'compareList' => function ($query) {
                return $query->where('user_id', Auth::guard('customer')->user()->id ?? 0);
            }
        ])->withCount('reviews');

        $product_ids = [];
        if ($request['data_from'] == 'category') {
            $products = $porduct_data->get();
            $product_ids = [];
            foreach ($products as $product) {
                foreach (json_decode($product['category_ids'], true) as $category) {
                    if ($category['id'] == $request['id']) {
                        array_push($product_ids, $product['id']);
                    }
                }
            }
            $query = $porduct_data->whereIn('id', $product_ids);
        }

        if ($request->has('search_category_value') && $request['search_category_value'] != 'all') {
            $products = $porduct_data->get();
            $product_ids = [];
            foreach ($products as $product) {
                foreach (json_decode($product['category_ids'], true) as $category) {
                    if ($category['id'] == $request['search_category_value']) {
                        array_push($product_ids, $product['id']);
                    }
                }
            }
            $query = $porduct_data->whereIn('id', $product_ids);
        }

        if ($request['data_from'] == 'brand') {
            $query = $porduct_data->where('brand_id', $request['id']);
        }

        if (!$request->has('data_from') || $request['data_from'] == 'latest') {
            $query = $porduct_data;
        }

        if ($request['data_from'] == 'top-rated') {
            $reviews = Review::select('product_id', DB::raw('AVG(rating) as count'))
                ->groupBy('product_id')
                ->orderBy("count", 'desc')->get();
            $product_ids = [];
            foreach ($reviews as $review) {
                array_push($product_ids, $review['product_id']);
            }
            $query = $porduct_data->whereIn('id', $product_ids);
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
            $query = $porduct_data->whereIn('id', $product_ids);
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
            $query = $porduct_data->whereIn('id', $product_ids);
        }
        if ($request['data_from'] == 'featured') {
            $query = Product::with([
                'reviews',
                'seller.shop',
                'wishList' => function ($query) {
                    return $query->where('customer_id', Auth::guard('customer')->user()->id ?? 0);
                },
                'compareList' => function ($query) {
                    return $query->where('user_id', Auth::guard('customer')->user()->id ?? 0);
                }
            ])->active()->withCount('reviews')->where('featured', 1);
        }

        if ($request['data_from'] == 'featured_deal') {
            $featured_deal_id = FlashDeal::where(['status' => 1])->where(['deal_type' => 'feature_deal'])->pluck('id')->first();
            $featured_deal_product_ids = FlashDealProduct::where('flash_deal_id', $featured_deal_id)->pluck('product_id')->toArray();
            $query = Product::with([
                'reviews',
                'seller.shop',
                'wishList' => function ($query) {
                    return $query->where('customer_id', Auth::guard('customer')->user()->id ?? 0);
                },
                'compareList' => function ($query) {
                    return $query->where('user_id', Auth::guard('customer')->user()->id ?? 0);
                }
            ])->active()->withCount('reviews')->whereIn('id', $featured_deal_product_ids);
        }
        if ($request['data_from'] == 'search') {
            $key = explode(' ', $request['name']);
            $product_ids = Product::with([
                'seller.shop',
                'wishList' => function ($query) {
                    return $query->where('customer_id', Auth::guard('customer')->user()->id ?? 0);
                },
                'compareList' => function ($query) {
                    return $query->where('user_id', Auth::guard('customer')->user()->id ?? 0);
                }
            ])
                ->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('name', 'like', "%{$value}%")
                            ->orWhereHas('tags', function ($query) use ($value) {
                                $query->where('tag', 'like', "%{$value}%");
                            });
                    }
                })->pluck('id');

            if ($product_ids->count() == 0) {
                $product_ids = Translation::where('translationable_type', 'App\Models\Product')
                    ->where('key', 'name')
                    ->where(function ($q) use ($key) {
                        foreach ($key as $value) {
                            $q->orWhere('value', 'like', "%{$value}%");
                        }
                    })
                    ->pluck('translationable_id');
            }

            $query = $porduct_data->WhereIn('id', $product_ids);
        }
        if ($request['data_from'] == 'discounted') {
            $query = Product::with([
                'reviews',
                'seller.shop',
                'wishList' => function ($query) {
                    return $query->where('customer_id', Auth::guard('customer')->user()->id ?? 0);
                },
                'compareList' => function ($query) {
                    return $query->where('user_id', Auth::guard('customer')->user()->id ?? 0);
                }
            ])->active()->withCount('reviews')->where('discount', '!=', 0);
        }
        if (!$request['data_from'] && !$request['name'] && $request['ratings']) {
            $query = $query ?? $porduct_data;
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
            $fetched = $query->latest();
        }
        if ($request['min_price'] != null || $request['max_price'] != null) {
            $fetched = $fetched->whereBetween('unit_price', [Helpers::convert_currency_to_usd($request['min_price']), Helpers::convert_currency_to_usd($request['max_price'])]);
        }
        if ($request['ratings'] != null) {
            $fetched->with('rating')->whereHas('rating', function ($query) use ($request) {
                return $query;
            });
        }

        $data = [
            'id' => $request['id'],
            'slug' => $request['slug'] ?? null,
            'name' => $request['name'],
            'data_from' => $request['data_from'],
            'sort_by' => $request['sort_by'],
            'page_no' => $request['page'],
            'min_price' => $request['min_price'],
            'max_price' => $request['max_price'],
        ];
        $common_query = $fetched;
        $rating_1 = 0;
        $rating_2 = 0;
        $rating_3 = 0;
        $rating_4 = 0;
        $rating_5 = 0;

        foreach ($common_query->get() as $rating) {
            if (isset($rating->rating[0]['average']) && ($rating->rating[0]['average'] > 0 && $rating->rating[0]['average'] < 2)) {
                $rating_1 += 1;
            } elseif (isset($rating->rating[0]['average']) && ($rating->rating[0]['average'] >= 2 && $rating->rating[0]['average'] < 3)) {
                $rating_2 += 1;
            } elseif (isset($rating->rating[0]['average']) && ($rating->rating[0]['average'] >= 3 && $rating->rating[0]['average'] < 4)) {
                $rating_3 += 1;
            } elseif (isset($rating->rating[0]['average']) && ($rating->rating[0]['average'] >= 4 && $rating->rating[0]['average'] < 5)) {
                $rating_4 += 1;
            } elseif (isset($rating->rating[0]['average']) && ($rating->rating[0]['average'] == 5)) {
                $rating_5 += 1;
            }
        }
        $ratings = [
            'rating_1' => $rating_1,
            'rating_2' => $rating_2,
            'rating_3' => $rating_3,
            'rating_4' => $rating_4,
            'rating_5' => $rating_5,
        ];

        $products = $common_query->paginate(20)->appends($data);

        if ($request['ratings'] != null) {
            $products = $products->map(function ($product) use ($request) {
                $product->rating = $product->rating->pluck('average')[0];
                return $product;
            });
            $products = $products->where('rating', '>=', $request['ratings'])
                ->where('rating', '<', $request['ratings'] + 1)
                ->paginate(20)->appends($data);
        }

        if ($request->ajax()) {
            return response()->json([
                'total_product' => $products->total(),
                'view' => view(VIEW_FILE_NAMES['products__ajax_partials'], compact('products', 'product_ids'))->render(),
            ], 200);
        }
        if ($request['data_from'] == 'category') {
            $data['brand_name'] = Category::find((int)$request['id'])->name;
        }
        if ($request['data_from'] == 'brand') {
            $brand_data = Brand::active()->find((int)$request['id']);
            if ($brand_data) {
                $data['brand_name'] = $brand_data->name;
            } else {
                Toastr::warning(translate('not_found'));
                return redirect('/');
            }
        }


        return view(VIEW_FILE_NAMES['products_view_page'], compact('products', 'data', 'ratings', 'product_ids'));
    }

    public function theme_fashion(Request $request)
    {

        $tag_category = [];
        if ($request->data_from == 'category') {
            $tag_category = Category::where('id', $request->id)->select('id', 'name')->get();
        }

        $tag_brand = [];
        if ($request->data_from == 'brand') {
            $tag_brand = Brand::where('id', $request->id)->select('id', 'name')->get();
        }
        $request['sort_by'] == null ? $request['sort_by'] == 'latest' : $request['sort_by'];

        $porduct_data = Product::active()->withSum('orderDetails', 'qty', function ($query) {
            $query->where('delivery_status', 'delivered');
        })
            ->with([
                'category',
                'reviews',
                'rating',
                'wishList' => function ($query) {
                    return $query->where('customer_id', Auth::guard('customer')->user()->id ?? 0);
                },
                'compareList' => function ($query) {
                    return $query->where('user_id', Auth::guard('customer')->user()->id ?? 0);
                }
            ])->withCount('reviews');

        $product_ids = [];
        if ($request['data_from'] == 'category') {
            $products = $porduct_data->get();
            $product_ids = [];
            foreach ($products as $product) {
                foreach (json_decode($product['category_ids'], true) as $category) {
                    if ($category['id'] == $request['id']) {
                        array_push($product_ids, $product['id']);
                    }
                }
            }
            $query = $porduct_data->whereIn('id', $product_ids);
        }

        if ($request->has('search_category_value') && $request['search_category_value'] != 'all') {
            $products = $porduct_data->get();
            $product_ids = [];
            foreach ($products as $product) {
                foreach (json_decode($product['category_ids'], true) as $category) {
                    if ($category['id'] == $request['search_category_value']) {
                        array_push($product_ids, $product['id']);
                    }
                }
            }
            $query = $porduct_data->whereIn('id', $product_ids);
        }

        if ($request['data_from'] == 'brand') {
            $query = $porduct_data->where('brand_id', $request['id']);
        }

        if ($request['data_from'] == 'latest') {
            $query = $porduct_data;
        }
        if (!$request->has('data_from') || $request['data_from'] == 'default') {
            $query = $porduct_data->orderBy('order_details_sum_qty', 'DESC');
        }

        if ($request['data_from'] == 'top-rated') {
            $reviews = Review::select('product_id', DB::raw('AVG(rating) as count'))
                ->groupBy('product_id')
                ->orderBy("count", 'desc')->get();
            $product_ids = [];
            foreach ($reviews as $review) {
                array_push($product_ids, $review['product_id']);
            }
            $query = $porduct_data->whereIn('id', $product_ids);
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
            $query = $porduct_data->whereIn('id', $product_ids);
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
            $query = $porduct_data->whereIn('id', $product_ids);
        }

        if ($request['data_from'] == 'featured') {
            $query = Product::with(['reviews'])->active()->withCount('reviews')->where('featured', 1);
        }

        if ($request->has('shop_id') && $request['shop_id'] == 0) {
            $query = Product::active()
                ->with(['reviews'])
                ->withCount('reviews')
                ->where(['added_by' => 'admin', 'featured' => 1]);
        } elseif ($request->has('shop_id') && $request['shop_id'] != 0) {
            $query = Product::active()
                ->withCount('reviews')
                ->where(['added_by' => 'seller', 'featured' => 1])
                ->with(['reviews', 'seller.shop' => function ($query) use ($request) {
                    $query->where('id', $request->shop_id);
                }])
                ->whereHas('seller.shop', function ($query) use ($request) {
                    $query->where('id', $request->shop_id)->whereNotNull('id');
                });
        }

        if ($request['data_from'] == 'featured_deal') {
            $featured_deal_id = FlashDeal::where(['status' => 1])->where(['deal_type' => 'feature_deal'])->pluck('id')->first();
            $featured_deal_product_ids = FlashDealProduct::where('flash_deal_id', $featured_deal_id)->pluck('product_id')->toArray();
            $query = Product::with(['reviews'])->active()->whereIn('id', $featured_deal_product_ids);
        }

        if ($request['data_from'] == 'search') {
            $key = explode(' ', $request['name']);
            $product_ids = Product::where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%")
                        ->orWhereHas('tags', function ($query) use ($value) {
                            $query->where('tag', 'like', "%{$value}%");
                        });
                }
            })->pluck('id');

            $sellers = Shop::where(function ($q) use ($request) {
                $q->orWhere('name', 'like', "%{$request['name']}%");
            })->whereHas('seller', function ($query) {
                return $query->where(['status' => 'approved']);
            })->with('products', function ($query) {
                return $query->active()->where('added_by', 'seller');
            })->get();

            $seller_products = [];
            foreach ($sellers as $seller) {
                if (isset($seller->product) && $seller->product->count() > 0) {
                    $ids = $seller->product->pluck('id');
                    array_push($seller_products, ...$ids);
                }
            }

            $inhouse_product = [];
            $company_name = Helpers::get_business_settings('company_name');

            if (strpos($request['name'], $company_name) !== false) {
                $inhouse_product = Product::active()->withCount('reviews')->Where('added_by', 'admin')->pluck('id');
            }

            $product_ids = $product_ids->merge($seller_products)->merge($inhouse_product);


            if ($product_ids->count() == 0) {
                $product_ids = Translation::where('translationable_type', 'App\Models\Product')
                    ->where('key', 'name')
                    ->where(function ($q) use ($key) {
                        foreach ($key as $value) {
                            $q->orWhere('value', 'like', "%{$value}%");
                        }
                    })
                    ->pluck('translationable_id');
            }

            $query = $porduct_data->WhereIn('id', $product_ids);
        }

        if ($request['data_from'] == 'discounted') {
            $query = Product::with(['reviews'])->active()->withCount('reviews')->where('discount', '!=', 0);
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
            $fetched = $query->latest();
        }

        if ($request['min_price'] != null || $request['max_price'] != null) {
            $fetched = $fetched->whereBetween('unit_price', [Helpers::convert_currency_to_usd($request['min_price']), Helpers::convert_currency_to_usd($request['max_price'])]);
        }
        $common_query = $fetched;

        $products = $common_query->paginate(20);

        if ($request['ratings'] != null) {
            $products = $products->map(function ($product) use ($request) {
                $product->rating = $product->rating->pluck('average')[0];
                return $product;
            });
            $products = $products->where('rating', '>=', $request['ratings'])
                ->where('rating', '<', $request['ratings'] + 1)
                ->paginate(20);
        }

        // Categories start
        $categories = Category::withCount(['product' => function ($query) {
            $query->active();
        }])->with(['childes' => function ($query) {
            $query->with(['childes' => function ($query) {
                $query->withCount(['subSubCategoryProduct'])->where('position', 2);
            }])->withCount(['subCategoryProduct'])->where('position', 1);
        }, 'childes.childes'])
            ->where('position', 0)->get();
        // Categories End

        // Colors Start
        $colors_in_shop_merge = [];
        $colors_collection = Product::active()
            ->withCount('reviews')
            ->where('colors', '!=', '[]')
            ->pluck('colors')
            ->unique()
            ->toArray();

        foreach ($colors_collection as $color_json) {
            $color_array = json_decode($color_json, true);
            if ($color_array) {
                $colors_in_shop_merge = array_merge($colors_in_shop_merge, $color_array);
            }
        }
        $colors_in_shop = array_unique($colors_in_shop_merge);
        // Colors End
        $banner = \App\Models\BusinessSetting::where('type', 'banner_product_list_page')->whereJsonContains('value', ['status' => '1'])->first();

        if ($request->ajax()) {
            return response()->json([
                'total_product' => $products->total(),
                'view' => view(VIEW_FILE_NAMES['products__ajax_partials'], compact('products', 'product_ids'))->render(),
            ], 200);
        }

        if ($request['data_from'] == 'brand') {
            $brand_data = Brand::active()->find((int)$request['id']);
            if (!$brand_data) {
                Toastr::warning(translate('not_found'));
                return redirect('/');
            }
        }

        return view(VIEW_FILE_NAMES['products_view_page'], compact('products', 'tag_category', 'tag_brand', 'product_ids', 'categories', 'colors_in_shop', 'banner'));
    }

    public function theme_all_purpose(Request $request)
    {
        $request['sort_by'] == null ? $request['sort_by'] == 'latest' : $request['sort_by'];

        $porduct_data = Product::active()->with(['reviews', 'rating'])->withCount('reviews');

        $product_ids = [];
        if ($request['data_from'] == 'category') {
            $products = $porduct_data->get();
            $product_ids = [];
            foreach ($products as $product) {
                foreach (json_decode($product['category_ids'], true) as $category) {
                    if ($category['id'] == $request['id']) {
                        array_push($product_ids, $product['id']);
                    }
                }
            }
            $query = $porduct_data->whereIn('id', $product_ids);
        }

        if ($request->has('search_category_value') && $request['search_category_value'] != 'all') {
            $products = $porduct_data->get();
            $product_ids = [];
            foreach ($products as $product) {
                foreach (json_decode($product['category_ids'], true) as $category) {
                    if ($category['id'] == $request['search_category_value']) {
                        array_push($product_ids, $product['id']);
                    }
                }
            }
            $query = $porduct_data->whereIn('id', $product_ids);
        }

        if ($request['data_from'] == 'brand') {
            $query = $porduct_data->where('brand_id', $request['id']);
        }

        if (!$request->has('data_from') || $request['data_from'] == 'latest') {
            $query = $porduct_data;
        }

        if ($request['data_from'] == 'top-rated') {
            $reviews = Review::select('product_id', DB::raw('AVG(rating) as count'))
                ->groupBy('product_id')
                ->orderBy("count", 'desc')->get();
            $product_ids = [];
            foreach ($reviews as $review) {
                array_push($product_ids, $review['product_id']);
            }
            $query = $porduct_data->whereIn('id', $product_ids);
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
            $query = $porduct_data->whereIn('id', $product_ids);
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
            $query = $porduct_data->whereIn('id', $product_ids);
        }

        if ($request['data_from'] == 'featured') {
            $query = Product::with(['reviews'])->active()->withCount('reviews')->where('featured', 1);
        }

        if ($request['data_from'] == 'featured_deal') {
            $featured_deal_id = FlashDeal::where(['status' => 1])->where(['deal_type' => 'feature_deal'])->pluck('id')->first();
            $featured_deal_product_ids = FlashDealProduct::where('flash_deal_id', $featured_deal_id)->pluck('product_id')->toArray();
            $query = Product::with(['reviews'])->active()->withCount('reviews')->whereIn('id', $featured_deal_product_ids);
        }

        if ($request['data_from'] == 'search') {
            $key = explode(' ', $request['name']);
            $product_ids = Product::where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%")
                        ->orWhereHas('tags', function ($query) use ($value) {
                            $query->where('tag', 'like', "%{$value}%");
                        });
                }
            })->pluck('id');

            if ($product_ids->count() == 0) {
                $product_ids = Translation::where('translationable_type', 'App\Models\Product')
                    ->where('key', 'name')
                    ->where(function ($q) use ($key) {
                        foreach ($key as $value) {
                            $q->orWhere('value', 'like', "%{$value}%");
                        }
                    })
                    ->pluck('translationable_id');
            }

            $query = $porduct_data->WhereIn('id', $product_ids);
        }

        if ($request['data_from'] == 'discounted') {
            $query = Product::with(['reviews'])->active()->withCount('reviews')->where('discount', '!=', 0);
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
            $fetched = $query->latest();
        }

        if ($request['min_price'] != null || $request['max_price'] != null) {
            $fetched = $fetched->whereBetween('unit_price', [Helpers::convert_currency_to_usd($request['min_price']), Helpers::convert_currency_to_usd($request['max_price'])]);
        }
        $common_query = $fetched;

        $rating_1 = 0;
        $rating_2 = 0;
        $rating_3 = 0;
        $rating_4 = 0;
        $rating_5 = 0;

        foreach ($common_query->get() as $rating) {
            if (isset($rating->rating[0]['average']) && ($rating->rating[0]['average'] > 0 && $rating->rating[0]['average'] < 2)) {
                $rating_1 += 1;
            } elseif (isset($rating->rating[0]['average']) && ($rating->rating[0]['average'] >= 2 && $rating->rating[0]['average'] < 3)) {
                $rating_2 += 1;
            } elseif (isset($rating->rating[0]['average']) && ($rating->rating[0]['average'] >= 3 && $rating->rating[0]['average'] < 4)) {
                $rating_3 += 1;
            } elseif (isset($rating->rating[0]['average']) && ($rating->rating[0]['average'] >= 4 && $rating->rating[0]['average'] < 5)) {
                $rating_4 += 1;
            } elseif (isset($rating->rating[0]['average']) && ($rating->rating[0]['average'] == 5)) {
                $rating_5 += 1;
            }
        }
        $ratings = [
            'rating_1' => $rating_1,
            'rating_2' => $rating_2,
            'rating_3' => $rating_3,
            'rating_4' => $rating_4,
            'rating_5' => $rating_5,
        ];
        $data = [
            'id' => $request['id'],
            'slug' => $request['slug'] ?? null,
            'name' => $request['name'],
            'data_from' => $request['data_from'],
        ];
        $products_count = $common_query->count();
        $products = $common_query->paginate(4)->appends($data);
        $categories = Category::withCount(['product' => function ($query) {
            $query->where(['status' => '1']);
        }])->with(['childes' => function ($sub_query) {
            $sub_query->with(['childes' => function ($sub_sub_query) {
                $sub_sub_query->withCount(['sub_sub_category_product'])->where('position', 2);
            }])->withCount(['sub_category_product'])->where('position', 1);
        }, 'childes.childes'])
            ->where('position', 0)->get();
        // Categories End
        // Colors Start
        $colors_in_shop_merge = [];
        $colors_collection = Product::active()
            ->withCount('reviews')
            ->where('colors', '!=', '[]')
            ->pluck('colors')
            ->unique()
            ->toArray();

        foreach ($colors_collection as $color_json) {
            $color_array = json_decode($color_json, true);
            if ($color_array) {
                $colors_in_shop_merge = array_merge($colors_in_shop_merge, $color_array);
            }
        }
        $colors_in_shop = array_unique($colors_in_shop_merge);
        // Colors End
        $banner = \App\Models\BusinessSetting::where('type', 'banner_product_list_page')->whereJsonContains('value', ['status' => '1'])->first();

        if ($request->ajax()) {
            return response()->json([
                'total_product' => $products->total(),
                'view' => view(VIEW_FILE_NAMES['products__ajax_partials'], compact('products', 'product_ids'))->render(),
            ], 200);
        }

        if ($request['data_from'] == 'brand') {
            $brand_data = Brand::active()->find((int)$request['id']);
            if (!$brand_data) {
                Toastr::warning(translate('not_found'));
                return redirect('/');
            }
        }
        return view(VIEW_FILE_NAMES['products_view_page'], compact('products', 'product_ids', 'products_count', 'categories', 'colors_in_shop', 'banner', 'ratings'));
    }

}
