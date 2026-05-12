<?php

namespace App\Http\Controllers\RestAPI\v1;

use App\Events\OrderStatusEvent;
use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Category;
use App\Models\Chadhava;
use App\Models\Astrologer\Astrologer;
use App\Models\Package;
use App\Models\Product;
use App\Models\Chadhava_orders;
use App\Models\Cities;
use App\Models\CounsellingUser;
use App\Models\Leads;
use App\Models\Order;
use App\Models\User;
use App\Models\Coupon;
use App\Models\Devotee;
use App\Models\PoojaForecast;
use App\Models\OfflinePoojaOrder;
use App\Models\PoojaRecords;
use App\Models\Prashad_deliverys;
use App\Models\ProductLeads;
use App\Models\Service_order;
use App\Models\ServiceReview;
use App\Models\Vippooja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Utils\Helpers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use App\Models\WalletTransaction;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Facades\Validator;

class ServicesController extends Controller
{

    public function getpooja_category($id)
    {
        $get_pooja_category = Category::where('parent_id', 33)->get();
        if ($get_pooja_category->isNotEmpty()) {
            $get_pooja_category->transform(function ($get_pooja_category) {
                $translations = $get_pooja_category->translations()->pluck('value', 'key')->toArray();
                $CategoryDataGet = [
                    'id' => $get_pooja_category->id,
                    'en_name' => $get_pooja_category->name,
                    'hi_name' => $translations['name'] ?? null,
                    'icon' => $get_pooja_category->icon ? url('/category' . $get_pooja_category->icon) : null,
                ];
                return $CategoryDataGet;
            });
            $filteredData = $get_pooja_category->filter(function ($item) {
                return !empty($item['en_name']) && $item !== null;
            })->values();
            if ($filteredData->isEmpty()) {
                return response()->json(['status' => 200, 'message' => 'No special event']);
            } else {
                return response()->json(['status' => 200, 'data' => $filteredData]);
            }
        }
        return response()->json(['status' => 400, 'message' => 'No special event']);
    }

    // Category Wise Pooja Show API
    public function servicesBySubCategory($subCategoryId)
    {
        // Fetch services with the given subcategory ID
        $list = PoojaForecast::with([
            'service' => function ($query) {
                $query->where('status', 1)
                      ->where('product_type', 'pooja');
            }
        ])
        ->whereIn('type', ['weekly', 'special'])
        ->orderBy('booking_date', 'asc')
        ->where('is_expired', 0)
        ->get()
        ->filter(function ($item) {
            return $item->service;
        })
        ->unique('service_id') 
        ->values();

        $serviceIds = $list->pluck('service_id')->toArray();

        $services = Service::where('status', 1)
        ->whereIn('id', $serviceIds)
        ->where('category_id', 33) 
        ->where('product_type', 'pooja')
        ->where('sub_category_id', $subCategoryId)
        ->with(['categories', 'pandit', 'product'])
        ->get();
    

        if ($services->isNotEmpty()) {
            // Transform the service data
            $services->transform(function ($service) {
                // Handle packages
                $packages = json_decode($service->packages_id, true);
                $servicePackages = [];
                if (is_array($packages)) {
                    foreach ($packages as $val) {
                        $packageId = $val['package_id'];
                        $packageModel = Package::find($packageId);
                        if ($packageModel) {
                            $packageTranslations = $packageModel->translations()->pluck('value', 'key')->toArray();
                            $servicePackages[] = [
                                'package_id' => $packageId,
                                'en_package_name' => $packageModel->title,
                                'hi_package_name' => $packageTranslations['title'] ?? null,
                                'person' => $packageModel->person,
                                'color' => $packageModel->color,
                                'en_description' => $packageModel->description,
                                'hi_description' => $packageTranslations['description'] ?? null,
                                'package_price' => $val['package_price']
                            ];
                        }
                    }
                }

                // Handle products
                $products = json_decode($service->product_id, true);
                $serviceProducts = [];
                if (is_array($products)) {
                    foreach ($products as $productId) {
                        $ProductModel = Product::where('id', $productId)->where('status', 1)->first();
                        if ($ProductModel) {
                            $productTranslations = $ProductModel->translations()->pluck('value', 'key')->toArray();
                            $serviceProducts[] = [
                                'product_id' => $productId,
                                'en_name' => $ProductModel->name,
                                'hi_name' => $productTranslations['name'] ?? null,
                                'en_details' => $ProductModel->details,
                                'hi_details' => $productTranslations['description'] ?? null,
                                'price' => $ProductModel->unit_price,
                                'thumbnail' => $ProductModel->thumbnail ? url('/storage/app/public/product/thumbnail/' . $ProductModel->thumbnail) : null,
                                'images' => $ProductModel->images ? url('/storage/app/public/product/' . $ProductModel->images) : null,
                            ];
                        }
                    }
                }

                // Process translations for the service
                $translations = $service->translations()->pluck('value', 'key')->toArray();

                // Format final service object
                $serviceData = [
                    'id' => $service->id,
                    'en_name' => $service->name,
                    'hi_name' => $translations['name'] ?? null,
                    'en_pooja_heading' => $service->pooja_heading,
                    'hi_pooja_heading' => $translations['pooja_heading'] ?? null,
                    'slug' => $service->slug,
                    'image' => $service->images ? url('/storage/app/public/pooja/' . $service->images) : null,
                    'status' => $service->status,
                    'user_id' => $service->user_id,
                    'added_by' => $service->added_by,
                    'en_short_benifits' => $service->short_benifits,
                    'hi_short_benifits' => $translations['short_benifits'] ?? null,
                    'product_type' => $service->product_type,
                    'pooja_type' => $service->pooja_type,
                    'schedule' => $service->schedule,
                    'counselling_main_price' => $service->counselling_main_price,
                    'counselling_selling_price' => $service->counselling_selling_price,
                    'en_details' => $service->details,
                    'hi_details' => $translations['details'] ?? null,
                    'en_benefits' => $service->benefits,
                    'hi_benefits' => $translations['benefits'] ?? null,
                    'en_process' => $service->process,
                    'hi_process' => $translations['process'] ?? null,
                    'en_temple_details' => $service->temple_details,
                    'hi_temple_details' => $translations['temple_details'] ?? null,
                    'category_ids' => $service->category_ids,
                    'category_id' => $service->category_id,
                    'sub_category_id' => $service->sub_category_id,
                    'sub_sub_category_id' => $service->sub_sub_category_id,
                    'product_id' => $service->product_id,
                    'packages_id' => $service->packages_id,
                    'pandit_assign' => $service->pandit_assign,
                    'en_pooja_venue' => $service->pooja_venue,
                    'hi_pooja_venue' => $translations['pooja_venue'] ?? null,
                    'pooja_time' => $service->pooja_time,
                    'week_days' => $service->week_days,
                    'video_provider' => $service->video_provider,
                    'video_url' => $service->video_url,
                    'thumbnail' => $service->thumbnail ? url('/storage/app/public/pooja/thumbnail/' . $service->thumbnail) : null,
                    'digital_file_ready' => $service->digital_file_ready,
                    'en_meta_title' => $service->meta_title,
                    'hi_meta_title' => $translations['meta_title'] ?? null,
                    'meta_description' => $service->meta_description,
                    'meta_image' => $service->meta_image,
                    'packages' => $servicePackages,
                    'products' => $serviceProducts,
                ];

                // Additional processing for week days
                $poojaw = json_decode($service->week_days);
                $poojat = date('H:i:s', strtotime($service->pooja_time));
                $nextBooking = PoojaForecast::where('service_id', $service->id)
                    ->where('is_expired', 0)
                    ->whereIn('type', ['weekly', 'special'])
                    ->whereHas('service', function ($query) {
                        $query->where('status', 1)
                            ->where('product_type', 'pooja');
                    })
                    ->orderBy('booking_date', 'asc')
                    ->first();
                
                $nextPoojaDay = $nextBooking?->booking_date;
                
                if ($nextPoojaDay instanceof \DateTime) {
                    $serviceData['next_pooja_date'] = $nextPoojaDay->format('Y-m-d H:i:s');
                } else {
                    $serviceData['next_pooja_date'] = $nextPoojaDay
                        ? date('Y-m-d H:i:s', strtotime($nextPoojaDay))
                        : null;
                }
                
                $serviceData['pooja_type_text'] = match ($nextBooking?->type) {
                    'special' => 'Special Pooja',
                    'weekly' => 'Weekly Pooja',
                    default => null,
                };

                return $serviceData;
            });

            $filteredData = $services->filter(function ($item) {
                return !empty($item['en_name']) && $item !== null;
            })->values();

            if ($filteredData->isEmpty()) {
                return response()->json(['status' => 200, 'message' => 'No special event']);
            } else {
                return response()->json(['status' => 200, 'data' => $filteredData]);
            }
        }

        return response()->json(['status' => 400, 'message' => 'No special event']);
    }

    // All Pooja Show the API
    public function pooja()
    {
        $services = PoojaForecast::with([
            'service' => function ($query) {
                $query->where('status', 1)
                      ->where('product_type', 'pooja');
            }
        ])
        ->whereIn('type', ['weekly', 'special'])
        ->orderBy('booking_date', 'asc')
        ->where('is_expired', 0)
        ->get()
        ->filter(function ($item) {
            return $item->service;
        })
        ->unique('service_id') 
        ->values();
    
        // Transform the service data
        $services->transform(function ($forecast) {
        $service = $forecast->service;
        $translations = $service->translations()->pluck('value', 'key')->toArray();

            // Format final service object
            $serviceData = [
                'id' => $service->id,
                'en_name' => $service->name,
                'hi_name' => $translations['name'] ?? null,
                'slug' => $service->slug,
                'status' => $service->status,
                'en_short_benifits' => $service->short_benifits,
                'hi_short_benifits' => $translations['short_benifits'] ?? null,
                'en_pooja_heading' => $service->pooja_heading,
                'hi_pooja_heading' => $translations['pooja_heading'] ?? null,
                'product_type' => $service->product_type,
                'pooja_type' => $service->pooja_type,
                'en_pooja_venue' => $service->pooja_venue ?? null,
                'hi_pooja_venue' => $translations['pooja_venue'] ?? null,
                'week_days' => $service->week_days,
                'thumbnail' => $service->thumbnail ? url('/storage/app/public/pooja/thumbnail/' . $service->thumbnail) : null,
            ];

            // Additional processing for week days
            $poojaw = json_decode($service->week_days);
            $poojat = date('H:i:s', strtotime($service->pooja_time));
            $nextBooking = PoojaForecast::where('service_id', $service->id)
                    ->where('is_expired', 0)
                    ->whereIn('type', ['weekly', 'special'])
                    ->whereHas('service', function ($query) {
                        $query->where('status', 1)
                            ->where('product_type', 'pooja');
                    })
                    ->orderBy('booking_date', 'asc')
                    ->first();
                
                $nextPoojaDay = $nextBooking?->booking_date;
                
                if ($nextPoojaDay instanceof \DateTime) {
                    $serviceData['next_pooja_date'] = $nextPoojaDay->format('Y-m-d H:i:s');
                } else {
                    $serviceData['next_pooja_date'] = $nextPoojaDay
                        ? date('Y-m-d H:i:s', strtotime($nextPoojaDay))
                        : null;
                }
                
                $serviceData['pooja_type_text'] = match ($nextBooking?->type) {
                    'special' => 'Special Pooja',
                    'weekly' => 'Weekly Pooja',
                    default => null,
                };
            return $serviceData;
        });

        // Filter out services with empty English names
        $filteredServices = $services->filter(fn($item) => !empty($item['en_name']))->values();

        // Fetch VIP Poojas
        $vippooja = Vippooja::where('status', 1)->where('is_anushthan', '0')->get();

        $vippooja->transform(function ($vippooja) {
            $translations = $vippooja->translations()->pluck('value', 'key')->toArray();
            return [
                'id' => $vippooja->id,
                'en_name' => $vippooja->name,
                'hi_name' => $translations['name'] ?? null,
                'en_pooja_heading' => $vippooja->pooja_heading,
                'hi_pooja_heading' => $translations['pooja_heading'] ?? null,
                'slug' => $vippooja->slug,
                'status' => $vippooja->status,
                'en_short_benifits' => $vippooja->short_benifits,
                'hi_short_benifits' => $translations['short_benifits'] ?? null,
                'is_anushthan' => $vippooja->is_anushthan,
                'thumbnail' => $vippooja->thumbnail ? url('/storage/app/public/pooja/vip/thumbnail/' . $vippooja->thumbnail) : null,
            ];
        });

        $filteredVipPooja = $vippooja->filter(fn($item) => !empty($item['en_name']))->values();

        // Fetch Anushthan Poojas
        $anushthanpooja = Vippooja::where('status', 1)->where('is_anushthan', '1')->get();

        $anushthanpooja->transform(function ($anushthanpooja) {
            $translations = $anushthanpooja->translations()->pluck('value', 'key')->toArray();
            return [
                'id' => $anushthanpooja->id,
                'en_name' => $anushthanpooja->name,
                'hi_name' => $translations['name'] ?? null,
                'en_pooja_heading' => $anushthanpooja->pooja_heading,
                'hi_pooja_heading' => $translations['pooja_heading'] ?? null,
                'slug' => $anushthanpooja->slug,
                'status' => $anushthanpooja->status,
                'en_short_benifits' => $anushthanpooja->short_benifits,
                'hi_short_benifits' => $translations['short_benifits'] ?? null,
                'is_anushthan' => $anushthanpooja->is_anushthan,
                'thumbnail' => $anushthanpooja->thumbnail ? url('/storage/app/public/pooja/vip/thumbnail/' . $anushthanpooja->thumbnail) : null,
            ];
        });

        $filteredAnushthan = $anushthanpooja->filter(fn($item) => !empty($item['en_name']))->values();

        $chadhava = Chadhava::where('status', 1)->with(['pandit', 'product', 'cities'])->get();

        $chadhava->transform(function ($chadhava) {

            // Process translations for the service
            $translations = $chadhava->translations()->pluck('value', 'key')->toArray();

            // Format final service object
            $chadhavaData = [
                'id' => $chadhava->id,
                'en_name' => $chadhava->name,
                'hi_name' => $translations['name'] ?? null,
                'en_pooja_heading' => $chadhava->pooja_heading,
                'hi_pooja_heading' => $translations['pooja_heading'] ?? null,
                'slug' => $chadhava->slug,
                'status' => $chadhava->status,
                'en_short_details' => $chadhava->short_details,
                'hi_short_details' => $translations['short_details'] ?? null,
                'product_type' => $chadhava->product_type,
                'en_details' => $chadhava->details,
                'hi_details' => $translations['description'] ?? null,
                'en_chadhava_venue' => $chadhava->chadhava_venue,
                'hi_chadhava_venue' => $translations['chadhava_venue'] ?? null,
                'chadhava_week' => $chadhava->chadhava_week,
                'thumbnail' => $chadhava->thumbnail ? url('/storage/app/public/chadhava/thumbnail/' . $chadhava->thumbnail) : null,
            ];

            // Additional processing for week days
            $chadhava_week = json_decode($chadhava->chadhava_week);
            $nextChadhavaDay = Helpers::getNextChadhavaDay($chadhava_week);

            // Check pooja_type and handle accordingly
            if ($chadhava->chadhava_type == 1) {
                $startDate = $chadhava->start_date;
                $endDate = $chadhava->end_date;
                $currentDate = time();
                $formattedDates = [];
                $ChadhavaearliestDate = '';

                if ($startDate && $endDate && $startDate <= $endDate) {
                    $currentDateIter = $startDate->copy();
                    while ($currentDateIter <= $endDate) {
                        $formattedDates[] = $currentDateIter->format('Y-m-d');
                        $currentDateIter->addDay();
                    }

                    foreach ($formattedDates as $date) {
                        if (strtotime($date) > $currentDate) {
                            $ChadhavaearliestDate = date('d M, l', strtotime($date));
                            break;
                        }
                    }
                }

                $chadhavaData['next_chadhava_date'] = $ChadhavaearliestDate ?: null;
                $chadhavaData['chadhava_type_text'] = ' Date Wise Chadhava';
            } else {
                // For Weekly Pooja, use the calculated next pooja day
                $chadhavaData['next_chadhava_date'] = $nextChadhavaDay instanceof \DateTime ? $nextChadhavaDay->format('Y-m-d H:i:s') : ($nextChadhavaDay ? date('Y-m-d H:i:s', strtotime($nextChadhavaDay)) : null);
                $chadhavaData['chadhava_type_text'] = 'Weekly Chadhava';
            }

            return $chadhavaData;
        });

        $filteredChadhava = $chadhava->filter(fn($item) => !empty($item['en_name']))->values();

        // Prepare final response
        return response()->json([
            'status' => 200,
            'pooja' => $filteredServices,
            'vip_pooja' => $filteredVipPooja,
            'anushthan' => $filteredAnushthan,
            'chadhava' => $filteredChadhava
        ]);
    }

    // Single Pooja Show by slug
    public function getServiceBySlug($slug)
    {
        $list = PoojaForecast::with([
            'service' => function ($query) {
                $query->where('status', 1)
                      ->where('product_type', 'pooja');
            }
        ])
        ->whereIn('type', ['weekly', 'special'])
        ->orderBy('booking_date', 'asc')
        ->where('is_expired', 0)
        ->get()
        ->filter(function ($item) {
            return $item->service;
        })
        ->unique('service_id') 
        ->values();

        $serviceIds = $list->pluck('service_id')->toArray();

        $service = Service::where('status', 1)
            ->where('slug', $slug)
            ->where('category_id', '33')
            ->where('product_type', 'pooja')
            ->with(['categories', 'pandit', 'product'])
            ->first();

        if ($service !== null) {  // Check if the service exists
            // Transform the service data
            $transformedService = $this->transformService($service);

            if (empty($transformedService['en_name'])) {
                return response()->json(['status' => 400]);
            } else {
                return response()->json(['status' => 200, 'data' => $transformedService]);
            }
        }

        return response()->json(['status' => 400]);
    }

    private function transformService($service)
    {
        // Handle packages
        $packages = json_decode($service->packages_id, true);
        $servicePackages = [];
        if (is_array($packages)) {
            foreach ($packages as $val) {
                $packageId = $val['package_id'];
                $packageModel = Package::find($packageId);
                if ($packageModel) {
                    $packageTranslations = $packageModel->translations()->pluck('value', 'key')->toArray();
                    $servicePackages[] = [
                        'package_id' => $packageId,
                        'en_package_name' => $packageModel->title,
                        'hi_package_name' => $packageTranslations['title'] ?? null,
                        'person' => $packageModel->person,
                        'color' => $packageModel->color,
                        'en_description' => $packageModel->description,
                        'hi_description' => $packageTranslations['description'] ?? null,
                        'package_price' => $val['package_price']
                    ];
                }
            }
        }

        // Handle products
        $products = json_decode($service->product_id, true);
        $serviceProducts = [];
        if (is_array($products)) {
            foreach ($products as $productId) {
                $ProductModel = Product::where('id', $productId)->where('status', 1)->first();
                if ($ProductModel) {
                    $productTranslations = $ProductModel->translations()->pluck('value', 'key')->toArray();
                    $serviceProducts[] = [
                        'product_id' => $productId,
                        'en_name' => $ProductModel->name,
                        'hi_name' => $productTranslations['name'] ?? null,
                        'en_details' => $ProductModel->details,
                        'hi_details' => $productTranslations['description'] ?? null,
                        'price' => $ProductModel->unit_price,
                        'thumbnail' => $ProductModel->thumbnail ? url('/storage/app/public/product/thumbnail/' . $ProductModel->thumbnail) : null,
                        'images' => $ProductModel->images ? url('/storage/app/public/product/' . $ProductModel->images) : null,
                    ];
                }
            }
        }

        // Handle venues
        // $venues = json_decode($service->pooja_venue, true);
        // $serviceVenues = [];
        // if (is_array($venues)) {
        //     foreach ($venues as $venue) {
        //         $serviceVenues[] = ['address' => $venue];
        //     }
        // }

        // Process translations for the service
        $translations = $service->translations()->pluck('value', 'key')->toArray();
        $count = Service_order::where('type', 'pooja')->where('service_id',  $service->id)->count();
        // Format final service object
        $serviceData = [
            'count' => $count,
            'id' => $service->id,
            'en_name' => $service->name,
            'hi_name' => $translations['name'] ?? null,
            'en_pooja_heading' => $service->pooja_heading,
            'hi_pooja_heading' => $translations['pooja_heading'] ?? null,
            'slug' => $service->slug,
            'image' => $service->images ? url('/storage/app/public/pooja/' . $service->images) : null,
            'status' => $service->status,
            'user_id' => $service->user_id,
            'added_by' => $service->added_by,
            'en_short_benifits' => $service->short_benifits,
            'hi_short_benifits' => $translations['short_benifits'] ?? null,
            'product_type' => $service->product_type,
            'pooja_type' => $service->pooja_type,
            'schedule' => $service->schedule,
            'counselling_main_price' => $service->counselling_main_price,
            'counselling_selling_price' => $service->counselling_selling_price,
            'en_details' => $service->details,
            'hi_details' => $translations['details'] ?? null,
            'en_benefits' => $service->benefits,
            'hi_benefits' => $translations['benefits'] ?? null,
            'en_process' => $service->process,
            'hi_process' => $translations['process'] ?? null,
            'en_temple_details' => $service->temple_details,
            'hi_temple_details' => $translations['temple_details'] ?? null,
            'category_ids' => $service->category_ids,
            'category_id' => $service->category_id,
            'sub_category_id' => $service->sub_category_id,
            'sub_sub_category_id' => $service->sub_sub_category_id,
            'product_id' => $service->product_id,
            'packages_id' => $service->packages_id,
            'pandit_assign' => $service->pandit_assign,
            'en_pooja_venue' => $service->pooja_venue,
            'hi_pooja_venue' => $translations['pooja_venue'] ?? null,
            'pooja_time' => $service->pooja_time,
            'week_days' => $service->week_days,
            'video_provider' => $service->video_provider,
            'video_url' => $service->video_url,
            'thumbnail' => $service->thumbnail ? url('/storage/app/public/pooja/thumbnail/' . $service->thumbnail) : null,
            'digital_file_ready' => $service->digital_file_ready,
            'en_meta_title' => $service->meta_title,
            'hi_meta_title' => $translations['meta_title'] ?? null,
            'meta_description' => $service->meta_description,
            'meta_image' => $service->meta_image,
            'packages' => $servicePackages,
            'products' => $serviceProducts,

        ];

        // Additional processing for week days
        $poojaw = json_decode($service->week_days);
        $poojat = date('H:i:s', strtotime($service->pooja_time));
        $nextBooking = PoojaForecast::where('service_id', $service->id)
                    ->where('is_expired', 0)
                    ->whereIn('type', ['weekly', 'special'])
                    ->whereHas('service', function ($query) {
                        $query->where('status', 1)
                            ->where('product_type', 'pooja');
                    })
                    ->orderBy('booking_date', 'asc')
                    ->first();
                
                $nextPoojaDay = $nextBooking?->booking_date;
                
                if ($nextPoojaDay instanceof \DateTime) {
                    $serviceData['next_pooja_date'] = $nextPoojaDay->format('Y-m-d H:i:s');
                } else {
                    $serviceData['next_pooja_date'] = $nextPoojaDay
                        ? date('Y-m-d H:i:s', strtotime($nextPoojaDay))
                        : null;
                }
                
                $serviceData['pooja_type_text'] = match ($nextBooking?->type) {
                    'special' => 'Special Pooja',
                    'weekly' => 'Weekly Pooja',
                    default => null,
                };

        return $serviceData;
    }

    // Front Side Api Order Get the Data
    public function LeadStore(Request $request)
    {
        $service_id   = $request->input('service_id');
        $package_id   = $request->input('package_id');
        $personPhone  = $request->input('person_phone');

        $customer = User::where('phone', $personPhone)->first();
        if (!$customer) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid user: Phone number not registered.'
            ], 404);
        }

        $full_name = $customer ? trim(($customer->f_name ?? '') . ' ' . ($customer->l_name ?? '')) : '';
        $full_name = $full_name ?: ($customer->name ?? '');
        $puja = Service::where('id', $service_id)->where('product_type','pooja')->where('status','1')->first();
        if (!$puja) {
            return response()->json(['status' => false, 'message' => 'puja not found.'], 404);
        }

        // Step 2: Decode packages and product_ids JSON
        $pujaPackages = json_decode($puja->packages_id, true);
        $pujaProducts = json_decode($puja->product_id, true);

        // Step 3: Find package with matching package_id (no need to check price from user)
        $matchedPackage = null;
        foreach ($pujaPackages as $pkg) {
            if ((int)$pkg['package_id'] === (int)$package_id) {
                $matchedPackage = $pkg;
                break;
            }
        }

        if (!$matchedPackage) {
            return response()->json(['status' => false, 'message' => 'Package ID not found in  Puja record.'], 400);
        }

        // Step 4: Get package details from 'packages' table
        $package = Package::find($package_id);
        if (!$package) {
            return response()->json(['status' => false, 'message' => 'Package details not found.'], 404);
        }
        // Step 5: Store in leads table
        $lead = new Leads();
        $lead->service_id     = $puja->id;
        $lead->type           = $puja->product_type;
        $lead->package_id     = $matchedPackage['package_id'];
        $lead->package_price  = $matchedPackage['package_price']; // fetched from  Pooja
        $lead->package_name   = $package->title;
        $lead->noperson       = $package->person;
        $lead->product_id     = json_encode($pujaProducts); // if you want to store all products
        $lead->person_phone   = $customer->phone;
        $lead->person_name    = $full_name;  // Optional fields
        $lead->customer_id    = $customer->id;  // Optional fields
        $lead->platform       = 'app';
        $lead->booking_date   =  $request->input('booking_date');
        $lead->save();
        // Get inserted ID
        $insertedRowId = $lead->id;
        if (!empty($insertedRowId)) {
            $leadno = 'PJ' . (100 + $insertedRowId); // Removed +1 to avoid skipping
        } else {
            $leadno = 'PJ101';
        }
        $lead->leadno = $leadno;
        $lead->save();

        return response()->json([
            'status'  => true,
            'message' => 'Lead successfully created.',
            'lead'    => $lead,
        ], 200);
        
    }


    public function SankalpStore($orderId, Request $request)
    {
        try {
            // Fetch order
            $serviceOrder = Service_order::where('order_id', $orderId)->first();
            if (!$serviceOrder) {
                return response()->json(['success' => false, 'message' => 'Order not found.'], 404);
            }

            // Prevent update if already edited
            if ($serviceOrder->is_edited == 1) {
                return response()->json(['success' => false, 'message' => 'Order already edited. You cannot update it.'], 403);
            }

            // Prepare base details
            $cust_details = [
                'newphone'   => $request->newPhone,
                'gotra'      => $request->gotra,
                'members'    => json_encode($request->members),
                'is_prashad' => $request->is_prashad ?? 0,
            ];

            // Add address info only if is_prashad == 1
            if ($request->is_prashad == 1) {
                $cust_details = array_merge($cust_details, [
                    'pincode'   => $request->pincode,
                    'city'      => $request->city,
                    'state'     => $request->state,
                    'house_no'  => $request->house_no,
                    'area'      => $request->area,
                    'landmark'  => $request->landmark,
                    'latitude'  => $request->latitude,
                    'longitude' => $request->longitude,
                ]);
            }
            
            // Update service_order
            Service_order::where('order_id', $orderId)->update($cust_details);
            $customer = User::where('id', $serviceOrder->customer_id)->first();
            $full_name = $customer ? trim(($customer->f_name ?? '') . ' ' . ($customer->l_name ?? '')) : '';
            $full_name = $full_name ?: ($customer->name ?? '');

            // Prepare devotee data
            $devoteeData = [
                'name'             => $full_name,
                'phone'            => $request->newPhone ?? ($customer->phone ?? ''),
                'gotra'            => $request->gotra,
                'service_order_id' => $orderId,
                'members'          => json_encode($request->members),
                'is_prashad'       => $request->is_prashad ?? 0,
                'status'       => 1,
                'type'       => $serviceOrder->type ?? '',
            ];

            // Only add address if prashad is 1
            if ($request->is_prashad == 1) {
                $devoteeData = array_merge($devoteeData, [
                    'address_city'     => $request->city,
                    'address_state'    => $request->state,
                    'house_no'         => $request->house_no,
                    'address_pincode'  => $request->pincode,
                    'area'             => $request->area,
                    'latitude'         => $request->latitude,
                    'longitude'        => $request->longitude,
                    'landmark'         => $request->landmark,
                ]);
            }

            // Update if exists, else create
            Devotee::updateOrCreate(
                ['service_order_id' => $orderId], // Matching condition
                $devoteeData        // Data to update or insert
            );
            // Add address to devoteeData if prashad is selected
            if ($request->is_prashad == 1) {
                Prashad_deliverys::updateOrCreate(
                    ['order_id' => $orderId], // Check by order_id
            
                    [   // Always update these fields
                        'seller_id'     => '14',
                        'warehouse_id'  => '61202',
                        'service_id'    => $serviceOrder->service_id,
                        'user_id'       => $serviceOrder->customer_id,
                        'product_id'    => '853',
                        'type'          => $serviceOrder->type,
                        'payment_type'  => 'P',
                        'booking_date'  => $serviceOrder->booking_date,
                    ]
                );
            }

            // Save review
            ServiceReview::create([
                'order_id'     => $orderId,
                'user_id'      => $serviceOrder->customer_id,
                'service_id'   => $serviceOrder->service_id,
                'service_type' => 'pooja',
                'rating'       => '5',
            ]);

            // Prepare message data
            $membersList = json_decode($serviceOrder->members, true);
            $formattedMembers = $membersList ? implode(', ', $membersList) : 'No members specified';
            // whatsapp
            $userInfo = User::where('id', ($serviceOrder->customer_id ?? ""))->first();
            $service_name = Service::where('id', ($serviceOrder->service_id ?? ""))->where('product_type', 'pooja')->first();
            $bookingDetails = Service_order::where('service_id', ($serviceOrder->service_id ?? ""))->where('type', 'pooja')
                ->where('booking_date', ($serviceOrder->booking_date ?? ""))
                ->where('customer_id', ($serviceOrder->customer_id ?? ""))
                ->where('order_id', ($orderId ?? ""))
                ->first();

            $message_data = [
                'service_name' => $service_name['name'],
                'member_names' => $formattedMembers,
                'type' => 'text-with-media',
                'attachment' =>  asset('/storage/app/public/pooja/thumbnail/' . $service_name->thumbnail),
                'booking_date' => date('d-m-Y', strtotime($serviceOrder->booking_date)),
                'puja_venue' => $service_name['pooja_venue'],
                'orderId' => $orderId,
                'prashad'      => $request->is_prashad == 1 ? 'Your Prasad is being prepared and will be dispatched  within 7–8 days.' : '',
                'gotra' => $request->input('gotra'),
                'customer_id' => ($serviceOrder->customer_id ?? ""),
            ];
            $messages =  Helpers::whatsappMessage('whatsapp', 'Sankalp Information', $message_data);
            // Mail Setup for Pooja Management Send to  User Email Id
            if ($userInfo && !empty($userInfo['email']) && filter_var($userInfo['email'], FILTER_VALIDATE_EMAIL)) {
                $data['type'] = 'pooja';
                $data['email'] = $userInfo['email'];
                $data['subject'] = 'Information given by you for puja';
                $data['htmlContent'] = \Illuminate\Support\Facades\View::make('admin-views.email.email-template.pooja-sankalp-template', compact('userInfo', 'service_name', 'bookingDetails', 'formattedMembers', 'request'))->render();

                Helpers::emailSendMessage($data);
            }

            return response()->json([
                'success'     => true,
                'message'     => 'Pooja Sankalp Record Successfully Inserted.',
                'sankalpData' => $serviceOrder,
            ], 200);

        } catch (\Exception $e) {
            \Log::error('SankalpStore Error: ' . $e->getMessage());
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong. Please try again later.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }   

    public function SankalpUpdate($orderId, Request $request)
    {
        try {
            // Fetch order
            $serviceOrder = Service_order::where('order_id', $orderId)->first();
            if (!$serviceOrder) {
                return response()->json(['success' => false, 'message' => 'Order not found.'], 404);
            }

            // Prepare base details
            $cust_details = [
                'newphone'   => $request->newPhone,
                'gotra'      => $request->gotra,
                'members'    => json_encode($request->members),
                'is_prashad' => $request->is_prashad ?? 0,
                'is_edited' => '1',
            ];

            // Add address info only if is_prashad == 1
            if ($request->is_prashad == 1) {
                $cust_details = array_merge($cust_details, [
                    'pincode'   => $request->pincode,
                    'city'      => $request->city,
                    'state'     => $request->state,
                    'house_no'  => $request->house_no,
                    'area'      => $request->area,
                    'landmark'  => $request->landmark,
                    'latitude'  => $request->latitude,
                    'longitude' => $request->longitude,
                ]);
            }
            
            // Update service_order
            Service_order::where('order_id', $orderId)->update($cust_details);
            $customer = User::where('id', $serviceOrder->customer_id)->first();
            $full_name = $customer ? trim(($customer->f_name ?? '') . ' ' . ($customer->l_name ?? '')) : '';
            $full_name = $full_name ?: ($customer->name ?? '');

            // Prepare devotee data
            $devoteeData = [
                'name'             => $full_name,
                'phone'            => $request->newPhone ?? ($customer->phone ?? ''),
                'gotra'            => $request->gotra,
                'service_order_id' => $orderId,
                'members'          => json_encode($request->members),
                'is_prashad'       => $request->is_prashad ?? 0,
            ];

            // Only add address if prashad is 1
            if ($request->is_prashad == 1) {
                $devoteeData = array_merge($devoteeData, [
                    'address_city'     => $request->city,
                    'address_state'    => $request->state,
                    'house_no'         => $request->house_no,
                    'address_pincode'  => $request->pincode,
                    'area'             => $request->area,
                    'latitude'         => $request->latitude,
                    'longitude'        => $request->longitude,
                    'landmark'         => $request->landmark,
                ]);
            }

            // Update if exists, else create
            Devotee::updateOrCreate(
                ['service_order_id' => $orderId], // Matching condition
                $devoteeData        // Data to update or insert
            );
            // Add address to devoteeData if prashad is selected
            if ($request->is_prashad == 1) {
                Prashad_deliverys::updateOrCreate(
                    ['order_id' => $orderId], // Check by order_id
            
                    [   // Always update these fields
                        'seller_id'     => '14',
                        'warehouse_id'  => '61202',
                        'service_id'    => $serviceOrder->service_id,
                        'user_id'       => $serviceOrder->customer_id,
                        'product_id'    => '853',
                        'type'          => $serviceOrder->type,
                        'payment_type'  => 'P',
                        'booking_date'  => $serviceOrder->booking_date,
                    ]
                );
            }

            // Save review
            ServiceReview::create([
                'order_id'     => $orderId,
                'user_id'      => $serviceOrder->customer_id,
                'service_id'   => $serviceOrder->service_id,
                'service_type' => 'pooja',
                'rating'       => '5',
            ]);

            // Prepare message data
            $membersList = json_decode($serviceOrder->members, true);
            $formattedMembers = $membersList ? implode(', ', $membersList) : 'No members specified';
            // whatsapp
            $userInfo = User::where('id', ($serviceOrder->customer_id ?? ""))->first();
            $service_name = Service::where('id', ($serviceOrder->service_id ?? ""))->where('product_type', 'pooja')->first();
            $bookingDetails = Service_order::where('service_id', ($serviceOrder->service_id ?? ""))->where('type', 'pooja')
                ->where('booking_date', ($serviceOrder->booking_date ?? ""))
                ->where('customer_id', ($serviceOrder->customer_id ?? ""))
                ->where('order_id', ($orderId ?? ""))
                ->first();

            $message_data = [
                'service_name' => $service_name['name'],
                'member_names' => $formattedMembers,
                'type' => 'text-with-media',
                'attachment' =>  asset('/storage/app/public/pooja/thumbnail/' . $service_name->thumbnail),
                'booking_date' => date('d-m-Y', strtotime($serviceOrder->booking_date)),
                'puja_venue' => $service_name['pooja_venue'],
                'orderId' => $orderId,
                'prashad'      => $request->is_prashad == 1 ? 'Your Prasad is being prepared and will be dispatched  within 7–8 days.' : '',
                'gotra' => $request->input('gotra'),
                'customer_id' => ($serviceOrder->customer_id ?? ""),
            ];
            $messages =  Helpers::whatsappMessage('whatsapp', 'Sankalp Information', $message_data);
            // Mail Setup for Pooja Management Send to  User Email Id
            if ($userInfo && !empty($userInfo['email']) && filter_var($userInfo['email'], FILTER_VALIDATE_EMAIL)) {
                $data['type'] = 'pooja';
                $data['email'] = $userInfo['email'];
                $data['subject'] = 'Information given by you for puja';
                $data['htmlContent'] = \Illuminate\Support\Facades\View::make('admin-views.email.email-template.pooja-sankalp-template', compact('userInfo', 'service_name', 'bookingDetails', 'formattedMembers', 'request'))->render();

                Helpers::emailSendMessage($data);
            }

            return response()->json([
                'success'     => true,
                'message'     => 'Pooja Sankalp Record Successfully Inserted.',
                'sankalpData' => $serviceOrder,
            ], 200);

        } catch (\Exception $e) {
            \Log::error('SankalpStore Error: ' . $e->getMessage());
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong. Please try again later.',
                'error'   => $e->getMessage()
            ], 500);
        }
    } 

    public function SearchPooja(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'search' => 'required',
        ], [
            'search.required' => 'Search by name, venue',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'recode' => 0, 'data' => []], 403);
        }

        $search = $request->input('search');

        $poojaServices = Service::where(function ($query) use ($search) {
            $query->where('name', 'LIKE', "%$search%")
                ->orWhere('pooja_venue', 'LIKE', "%$search%");

            if (str_contains(strtolower($search), 'pooja')) {
                $query->orWhere('product_type', 'pooja');
            }
        })
            ->where('status', 1)
            ->where('product_type', 'pooja')
            ->get()
            ->map(function ($item) {
                $translations = $item->translations()->pluck('value', 'key')->toArray();

                // Additional processing for week days
                $serviceData = [
                    'id' => $item->id,
                    'en_name' => $item->name,
                    'hi_name' => $translations['name'] ?? null,
                    'slug' => $item->slug,
                    'en_pooja_venue' => $item->pooja_venue,
                    'hi_pooja_venue' => $translations['pooja_venue'] ?? null,
                    'thumbnail' => $item->thumbnail ? url('/storage/app/public/pooja/thumbnail/' . $item->thumbnail) : null,

                ];

                $poojaw = json_decode($item->week_days);
                $poojat = date('H:i:s', strtotime($item->pooja_time));
                $nextBooking = PoojaForecast::where('service_id', $service->id)
                    ->where('is_expired', 0)
                    ->whereIn('type', ['weekly', 'special'])
                    ->whereHas('service', function ($query) {
                        $query->where('status', 1)
                            ->where('product_type', 'pooja');
                    })
                    ->orderBy('booking_date', 'asc')
                    ->first();
                
                $nextPoojaDay = $nextBooking?->booking_date;
                
                if ($nextPoojaDay instanceof \DateTime) {
                    $serviceData['next_pooja_date'] = $nextPoojaDay->format('Y-m-d H:i:s');
                } else {
                    $serviceData['next_pooja_date'] = $nextPoojaDay
                        ? date('Y-m-d H:i:s', strtotime($nextPoojaDay))
                        : null;
                }
                
                $serviceData['pooja_type_text'] = match ($nextBooking?->type) {
                    'special' => 'Special Pooja',
                    'weekly' => 'Weekly Pooja',
                    default => null,
                };

                return $serviceData;
            });

        $vipPooja = Vippooja::where(function ($query) use ($search) {
            $query->where('name', 'LIKE', "%$search%");

            if (str_contains(strtolower($search), 'vip')) {
                $query->orWhere('is_anushthan', 0);
            }
        })
            ->where('status', 1)
            ->where('is_anushthan', 0)
            ->get()
            ->map(function ($item) {
                $translations = $item->translations()->pluck('value', 'key')->toArray();

                return [
                    'id' => $item->id,
                    'en_name' => $item->name,
                    'hi_name' => $translations['name'] ?? null,
                    'slug' => $item->slug,
                    'thumbnail' => $item->thumbnail ? url('/storage/app/public/pooja/vip/thumbnail/' . $item->thumbnail) : null,
                ];
            });

        $anushthan = Vippooja::where(function ($query) use ($search) {
            $query->where('name', 'LIKE', "%$search%");

            if (str_contains(strtolower($search), 'anushthan')) {
                $query->orWhere('is_anushthan', 1);
            }
        })
            ->where('status', 1)
            ->where('is_anushthan', 1)
            ->get()
            ->map(function ($item) {
                $translations = $item->translations()->pluck('value', 'key')->toArray();

                return [
                    'id' => $item->id,
                    'en_name' => $item->name,
                    'hi_name' => $translations['name'] ?? null,
                    'slug' => $item->slug,
                    'thumbnail' => $item->thumbnail ? url('/storage/app/public/pooja/vip/thumbnail/' . $item->thumbnail) : null,
                ];
            });


        $chadhavaPooja = Chadhava::where(function ($query) use ($search) {
            $query->where('name', 'LIKE', "%$search%")
                ->orWhere('chadhava_venue', 'LIKE', "%$search%");

            if (str_contains(strtolower($search), 'chadhava')) {
                $query->orWhere('status', 1);
            }
        })
            ->get()
            ->map(function ($item) {
                $translations = $item->translations()->pluck('value', 'key')->toArray();

                $chadhavaData = [
                    'id' => $item->id,
                    'en_name' => $item->name,
                    'hi_name' => $translations['name'] ?? null,
                    'slug' => $item->slug,
                    'en_pooja_venue' => $item->chadhava_venue,
                    'hi_pooja_venue' => $translations['chadhava_venue'] ?? null,
                    'thumbnail' => $item->thumbnail ? url('/storage/app/public/chadhava/thumbnail/' . $item->thumbnail) : null,
                ];

                $chadhava_week = json_decode($item->chadhava_week);
                $nextChadhavaDay = Helpers::getNextChadhavaDay($chadhava_week);

                // Check pooja_type and handle accordingly
                if ($item->chadhava_type == 1) {
                    $startDate = $item->start_date;
                    $endDate = $item->end_date;
                    $currentDate = time();
                    $formattedDates = [];
                    $ChadhavaearliestDate = '';

                    if ($startDate && $endDate && $startDate <= $endDate) {
                        $currentDateIter = $startDate->copy();
                        while ($currentDateIter <= $endDate) {
                            $formattedDates[] = $currentDateIter->format('Y-m-d');
                            $currentDateIter->addDay();
                        }

                        foreach ($formattedDates as $date) {
                            if (strtotime($date) > $currentDate) {
                                $ChadhavaearliestDate = date('d M, l', strtotime($date));
                                break;
                            }
                        }
                    }

                    $chadhavaData['next_chadhava_date'] = $ChadhavaearliestDate ?: null;
                    $chadhavaData['chadhava_type_text'] = ' Date Wise Chadhava';
                } else {
                    // For Weekly Pooja, use the calculated next pooja day
                    $chadhavaData['next_chadhava_date'] = $nextChadhavaDay instanceof \DateTime ? $nextChadhavaDay->format('Y-m-d H:i:s') : ($nextChadhavaDay ? date('Y-m-d H:i:s', strtotime($nextChadhavaDay)) : null);
                    $chadhavaData['chadhava_type_text'] = 'Weekly Chadhava';
                }

                return $chadhavaData;
            });


        $data = [
            'status' => 1,
        ];


        if ($poojaServices->isNotEmpty()) {
            $data['pooja_services'] = $poojaServices;
        }

        if ($vipPooja->isNotEmpty()) {
            $data['vip_pooja'] = $vipPooja;
        }

        if ($anushthan->isNotEmpty()) {
            $data['anushthan'] = $anushthan;
        }

        if ($chadhavaPooja->isNotEmpty()) {
            $data['chadhava'] = $chadhavaPooja;
        }

        return response()->json($data);
    }

    public function getAllOrders(Request $request)
    {
        $user = Auth::guard('api')->user();
        if ($user) {
            $userId = $user->id;
            $type = $request->get('type');

            if ($type === 'chadhava') {
                $query = Chadhava_orders::where('customer_id', $userId)->orderBy('id', 'desc');
            } else {
                $query = Service_order::where('customer_id', $userId)->orderBy('id', 'desc');
            }

            if ($type === 'pooja') {
                $query->with(['services' => function ($query) {
                    $query->select('id', 'name', 'thumbnail');
                }]);
            }   elseif ($type === 'panditpooja') {
                $query->with(['services' => function ($query) {
                    $query->select('id', 'name', 'thumbnail');
                }]);
            }   elseif ($type === 'vip') {
                $query->with(['vippoojas' => function ($query) {
                    $query->select('id', 'name', 'thumbnail');
                }]);
            } elseif ($type === 'anushthan') {
                $query->with(['vippoojas' => function ($query) {
                    $query->select('id', 'name', 'thumbnail');
                }]);
            } elseif ($type === 'chadhava') {
                $query->with(['chadhava' => function ($query) {
                    $query->select('id', 'name', 'thumbnail');
                }]);
            } elseif ($type === 'counselling') {
                $query->with(['counselling' => function ($query) {
                    $query->select('id', 'name', 'thumbnail');
                }]);
            }

            if (!empty($type)) {
                $query->where('type', $type);
            }

            $orders = $query->get();

            $response = $orders->map(function ($order) use ($type) {
                $relation = null;
                $url = '';

                $nameTranslation = null;
                if ($type === 'pooja') {
                    $relation = 'services';
                    $nameTranslation = $order->services->translations()->where('key', 'name')->first()->value ?? null;
                    $url = url("storage/app/public/pooja/thumbnail/{$order->services->thumbnail}");
                } elseif ($type === 'panditpooja') {
                    $relation = 'services';
                    $nameTranslation = $order->services->translations()->where('key', 'name')->first()->value ?? null;
                    $url = url("storage/app/public/pooja/thumbnail/{$order->services->thumbnail}");
                } elseif ($type === 'vip') {
                    $relation = 'vippoojas';
                    $nameTranslation = $order->vippoojas->translations()->where('key', 'name')->first()->value ?? null;
                    $url = url("storage/app/public/pooja/vip/thumbnail/{$order->vippoojas->thumbnail}");
                } elseif ($type === 'anushthan') {
                    $relation = 'vippoojas';
                    $nameTranslation = $order->vippoojas->translations()->where('key', 'name')->first()->value ?? null;
                    $url = url("storage/app/public/pooja/vip/thumbnail/{$order->vippoojas->thumbnail}");
                } elseif ($type === 'chadhava') {
                    $relation = 'chadhava';
                    $nameTranslation = $order->chadhava->translations()->where('key', 'name')->first()->value ?? null;
                    $url = url("storage/app/public/chadhava/thumbnail/{$order->chadhava->thumbnail}");
                } elseif ($type === 'counselling') {
                    $relation = 'counselling';
                    $nameTranslation = $order->counselling->translations()->where('key', 'name')->first()->value ?? null;
                    $url = url("storage/app/public/pooja/thumbnail/{$order->counselling->thumbnail}");
                }
                $status = '';
                $order_status = '';
                switch ($order->status) {
                    case 0:
                        $status = 'Pending';
                        break;
                    case 1:
                        $status = 'Complete';
                        break;
                    case 2:
                        $status = 'Cancel';
                        break;
                    case 6:
                        $status = 'Rejected';
                        break;
                    default:
                        $status = 'Unknown';
                }

                switch ($order->order_status) {

                    case 0:
                        $order_status = 'Pending';
                        break;
                    case 1:
                        $order_status = 'Complete';
                        break;
                    case 3:
                        $order_status = 'Scheduled';
                        break;
                    case 4:
                        $order_status = 'Live';
                        break;
                    case 5:
                        $order_status = 'Shared';
                        break;
                    case 6:
                        $order_status = 'Rejected';
                        break;
                    default:
                        $order_status = 'Unknown status';
                }
                $order->status = $status;
                $order->order_status = $order_status;
                return [
                    'id' => $order->id,
                    'service_id' => $order->service_id,
                    'order_id' => $order->order_id,
                    'pay_amount' => $order->pay_amount,
                    'status' => $order->status,
                    'order_status' => $order->order_status,
                    'booking_date' => $order->booking_date,
                    'created_at' => date('Y-m-d H:i:s',  strtotime($order->created_at)),
                    'services' => $relation ? array_merge(
                        $order->$relation->toArray(),
                        ['hi_name' => $nameTranslation, 'thumbnail' => $url]
                    ) : null,
                ];
            });

            return response()->json([
                'status' => true,
                'message' => 'Orders retrieved successfully',
                'orders' => $response,
            ]);
        }

        return response()->json(['status' => false, 'message' => 'Unauthorized user']);
    }

    public function getAllServiceOrders(Request $request)
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Unauthorized user']);
        }

        $userId = $user->id;

        $currentDateTime = new DateTime('now', new DateTimeZone('Asia/Kolkata'));
        $startDateTime = $currentDateTime->modify('-7 day')->format('Y-m-d H:i:s');

        $service_getOrders = Service_order::with(['services' => function ($query) {
            $query->select('id', 'name', 'thumbnail');
        }, 'vippoojas' => function ($query) {
            $query->select('id', 'name', 'thumbnail');
        }, 'counselling' => function ($query) {
            $query->select('id', 'name', 'thumbnail');
        }])
            ->where('customer_id', $userId)
            ->whereIn('type', ['pooja', 'vip', 'anushthan', 'counselling'])
            ->where('created_at', '>=', $startDateTime)
            ->get();

        $chadhavaOrders = Chadhava_orders::with(['chadhava' => function ($query) {
            $query->select('id', 'name', 'thumbnail');
        }])
            ->where('customer_id', $userId)
            ->where('created_at', '>=', $startDateTime)
            ->get();

        $offlinepoojaOrders = OfflinePoojaOrder::where('customer_id', $userId)->where('created_at', '>=', $startDateTime)->with(['offlinePooja' => function ($query) {
            $query->select('id', 'name', 'thumbnail');
        }])->get()->map(function ($order) {
            $order->type = 'offlinepooja';
            return $order;
        });


        $eventOrders_all = \App\Models\EventOrder::select('event_orders.*','order_no as order_id' )->where('user_id', $userId)->whereIn('transaction_status', ['1', '2', '3'])->where('created_at', '>=', $startDateTime)->with(['eventid' => function ($query) {
            $query->select('id', 'event_name as name', 'event_image as thumbnail');
        }])->get()->map(function ($order) {
            $order->type = 'event';
            if ($order->eventid) {
                $order->eventid->makeHidden(['organizers', 'categorys', 'eventArtist']);
            }
            return $order;
        });
        $donateOrders_all = \App\Models\DonateAllTransaction::select('donate_all_transaction.*','trans_id as order_id' )->where('user_id', $userId)->where('amount_status', 1)->where('created_at', '>=', $startDateTime)->with(['getTrust' => function ($query) {
            $query->select('id', 'name', 'theme_image as thumbnail');
        }, 'adsTrust' => function ($query) {
            $query->select('id', 'name', 'image as thumbnail');
        }])->get()->map(function ($order) {
            // $order->type = 'donate';
            if ($order->adsTrust) {
                $order->adsTrust->makeHidden(['Purpose']);
            }
            return $order;
        });

        $tourOrders_all = \App\Models\TourOrder::where('user_id', $userId)->whereIn('amount_status', [1, 2, 3])->where('created_at', '>=', $startDateTime)->with(['Tour' => function ($query) {
            $query->select('id', 'tour_name as name', 'tour_image as thumbnail');
        }])->get()->map(function ($order) {
            $order->type = 'tour';
            if ($order->Tour) {
                $order->Tour->makeHidden(['TourPlane']);
            }
            return $order;
        });

        $kundalis_order_all = \App\Models\BirthJournalKundali::where('user_id', $userId)->where('payment_status', 1)
            ->where('created_at', '>=', $startDateTime)
            ->whereHas('birthJournal', function ($query) {
                $query->where('name', 'kundali');
            })
            ->with(['birthJournal' => function ($query) {
                $query->select('id', 'name', 'image as thumbnail');
                $query->where('name', 'kundali');
            }])->get()->map(function ($order) {
                $order->type = 'kundli';
                return $order;
            });

        $kundali_milan_order_all = \App\Models\BirthJournalKundali::where('user_id', $userId)->where('payment_status', 1)
            ->where('created_at', '>=', $startDateTime)
            ->whereHas('birthJournal', function ($query) {
                $query->where('name', 'kundali_milan');
            })
            ->with(['birthJournal' => function ($query) {
                $query->select('id', 'name', 'image as thumbnail');
                $query->where('name', 'kundali_milan');
            }])->get()->map(function ($order) {
                $order->type = 'kundli milan';
                return $order;
            });


        $allOrders = collect($service_getOrders ?: []) // Ensure collection
            ->merge(collect($chadhavaOrders ?: []))
            ->merge(collect($offlinepoojaOrders ?: []))
            ->merge(collect($eventOrders_all ?: []))
            ->merge(collect($donateOrders_all ?: []))
            ->merge(collect($tourOrders_all ?: []))
            ->merge(collect($kundalis_order_all ?: []))
            ->merge(collect($kundali_milan_order_all ?: []))
            ->sortByDesc('created_at');
        // dd($allOrders);
        $response = $allOrders->map(function ($order) {
            $relation = null;
            $url = '';
            $nameTranslation = null;

            if ($order->type === 'pooja') {
                $relation = 'services';
                $nameTranslation = $order->services->translations()->where('key', 'name')->first()->value ?? null;
                $url = url("storage/app/public/pooja/thumbnail/{$order->services->thumbnail}");
            } elseif ($order->type === 'vip') {
                $relation = 'vippoojas';
                $nameTranslation = $order->vippoojas->translations()->where('key', 'name')->first()->value ?? null;
                $url = url("storage/app/public/pooja/vip/thumbnail/{$order->vippoojas->thumbnail}");
            } elseif ($order->type === 'anushthan') {
                $relation = 'vippoojas';
                $nameTranslation = $order->vippoojas->translations()->where('key', 'name')->first()->value ?? null;
                $url = url("storage/app/public/pooja/vip/thumbnail/{$order->vippoojas->thumbnail}");
            } elseif ($order->type === 'chadhava') {
                $relation = 'chadhava';
                $nameTranslation = $order->chadhava->translations()->where('key', 'name')->first()->value ?? null;
                $url = url("storage/app/public/chadhava/thumbnail/{$order->chadhava->thumbnail}");
            } elseif ($order->type === 'counselling') {
                $relation = 'counselling';
                $nameTranslation = $order->counselling->translations()->where('key', 'name')->first()->value ?? null;
                $url = url("storage/app/public/pooja/thumbnail/{$order->counselling->thumbnail}");
            } elseif ($order->type === 'offlinepooja') {
                $relation = 'offlinePooja';
                $nameTranslation = $order->offlinePooja->translations()->where('key', 'name')->first()->value ?? null;
                $url = url("storage/app/public/offlinepooja/thumbnail/{$order->offlinePooja->thumbnail}");
            } elseif ($order->type == 'event') {
                $relation = 'eventid';
                $nameTranslation = $order->eventid->translations()->where('key', 'event_name')->first()->value ?? null;
                $url = url("storage/app/public/event/events/" . $order['eventid']['thumbnail'] ?? "");
            } elseif ($order->type == 'tour') {
                $relation = 'Tour';
                $nameTranslation = $order->Tour->translations()->where('key', 'tour_name')->first()->value ?? null;
                $url = url("storage/app/public/tour_and_travels/tour_visit/" . $order['Tour']['thumbnail'] ?? "");
            } elseif ($order->type == 'donate_trust' || $order->type == 'donate_ads') {
                if ($order['type'] == 'donate_trust') {
                    $relation = 'getTrust';
                    $nameTranslation = $order->getTrust->translations()->where('key', 'trust_name')->first()->value ?? null;
                    $url = url('storage/app/public/donate/trust/' . ($order['getTrust']['thumbnail'] ?? ''));
                } else {
                    $relation = 'adsTrust';
                    $nameTranslation = $order->adsTrust->translations()->where('key', 'name')->first()->value ?? null;
                    $url = url('storage/app/public/donate/ads/' . ($order['adsTrust']['thumbnail'] ?? ''));
                }
            } elseif ($order->type == 'kundli') {
                $relation = 'birthJournal';
                $nameTranslation = $order->birthJournal->translations()->where('key', 'name')->first()->value ?? null;
                $url = url("storage/app/public/birthjournal/image/" . ($order['birthJournal']['thumbnail'] ?? ''));
            } elseif ($order->type == 'kundli milan') {
                $relation = 'birthJournal';
                $url = url("storage/app/public/birthjournal/image/" . ($order['birthJournal']['thumbnail'] ?? ''));
            }

            $status = '';
            $order_status = '';
            switch ($order->status) {
                case 0:
                    $status = 'Pending';
                    $order_status = 'Pending';
                    break;
                case 1:
                    $status = 'Complete';
                    $order_status = 'Complete';
                    break;
                case 2:
                    $status = 'Cancel';
                    break;
                case 3:
                    $order_status = 'Schedule';
                    break;
                case 4:
                    $order_status = 'Live';
                    break;
                case 5:
                    $order_status = 'Share';
                    break;
                case 6:
                    $status = 'Rejected';
                    $order_status = 'Rejected';
                    break;
                default:
                    $status = 'Unknown Status';
            }

            if ($order->type === 'kundli milan' || $order->type === 'kundli' || $order->type === 'donate_trust' || $order->type === 'donate_ads' || $order->type === 'tour' || $order->type === 'event') {
                $status = '';
                $order_status = '';
                if (($order->type === 'kundli milan' && $order->milan_verify == 1) || ($order->type === 'kundli' && $order->kundali_pdf != '')) {
                    $status = 'Complete';
                    $order_status = 'Complete';
                } elseif (($order->type === 'kundli milan' && $order->milan_verify == 0) || ($order->type === 'kundli' && $order->kundali_pdf == '')) {
                    $status = 'Processing';
                    $order_status = 'Processing';
                } elseif (($order->type == 'donate_trust' || $order->type == 'donate_ads') || ($order->type === 'event' && $order->status == 1)) {
                    $status = 'Success';
                    $order_status = 'Success';
                } elseif ($order->type === 'event' && $order->status == 2) {
                    $status = 'Refunded';
                    $order_status = 'Refunded';
                } elseif ($order->type === 'tour') {
                    if (($order->status == 0 || $order->status == 1) && $order->cab_assign == 0 && $order->pickup_status == 0) {
                        $status = "Pending";
                        $order_status = "Pending";
                    } elseif (($order->status == 0 || $order->status == 1) && $order->cab_assign != 0 && $order->pickup_status == 0) {
                        $status = "Processing";
                        $order_status = "Processing";
                    } elseif (($order->status == 0 || $order->status == 1) && $order->cab_assign != 0 && $order->pickup_status == 1 && $order->drop_status == 0) {
                        $status = "Pickup";
                        $order_status = "Pickup";
                    } elseif (($order->status == 0 || $order->status == 1) && $order->cab_assign != 0 && $order->drop_status == 1) {
                        $status = "Completed";
                        $order_status = "Completed";
                    } else {
                        $status = "Refund";
                        $order_status = "Refund";
                    }
                } else {
                    $status = 'Pending';
                    $order_status = 'Pending';
                }
                return [
                    'id' => $order->id,
                    'type' => $order->type,
                    'service_id' => '',
                    'order_id' => $order->order_id,
                    'pay_amount' => $order->amount ?? 0,
                    'status' => $status,
                    'order_status' => $order_status,
                    'booking_date' => $order->created_at,
                    'created_at' => $order->created_at,
                    'services' => $relation && $order->$relation
                        ? array_merge($order->$relation->toArray(), ['hi_name' => $nameTranslation, 'thumbnail' => $url])
                        : null,
                ];
            } else {
                return [
                    'id' => $order->id,
                    'type' => $order->type,
                    'service_id' => $order->service_id,
                    'order_id' => $order->order_id,
                    'pay_amount' => $order->pay_amount,
                    'status' => $status,
                    'order_status' => $order_status,
                    'booking_date' => $order->booking_date,
                    'created_at' => $order->created_at,
                    'services' => $relation ? array_merge(
                        $order->$relation->toArray(),
                        ['hi_name' => $nameTranslation, 'thumbnail' => $url]
                    ) : null,
                ];
            }
        });

        return response()->json([
            'status' => true,
            'message' => 'Orders retrieved successfully',
            'orders' => $response->values(),
        ]);
    }

    public function poojaDetails($orderid)
    {
        $serviceOrder = Service_order::where('order_id', $orderid)
            ->with([
                'services:id,name,thumbnail',
                'vippoojas:id,name,thumbnail',
                'packages',
                'pandit:id,name,email,mobile_no',
                'product_leads:id,leads_id,final_price,qty,product_name,product_price',
            ])
            ->first();

        if (!$serviceOrder) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid order ID or order not found.',
            ], 404);
        }

        $customer = User::where('phone', $serviceOrder['leads']['person_phone'])
            ->select('id', 'name', 'email', 'phone')
            ->first();

        if ($customer) {
            $serviceOrder['customer'] = $customer;
        } else {
            $serviceOrder['customer'] = null;
        }

        if ($serviceOrder->services) {
            $serviceOrder->services->hi_name = $serviceOrder->services->translations()
                ->where('key', 'name')
                ->first()
                ->value ?? null;

            $serviceOrder->services->thumbnail = $serviceOrder->services->thumbnail
                ? url("storage/app/public/pooja/thumbnail/{$serviceOrder->services->thumbnail}")
                : null;
        }


        if ($serviceOrder->vippoojas) {
            $serviceOrder->vippoojas->hi_name = $serviceOrder->vippoojas->translations()
                ->where('key', 'name')
                ->first()
                ->value ?? null;

            $serviceOrder->vippoojas->thumbnail = $serviceOrder->vippoojas->thumbnail
                ? url("storage/app/public/pooja/vip/thumbnail/{$serviceOrder->vippoojas->thumbnail}")
                : null;
        }

        $status = '';
        $order_status = '';

        switch ($serviceOrder->status) {
            case 0:
                $status = 'Pending';
                break;
            case 1:
                $status = 'Complete';
                break;
            case 2:
                $status = 'Cancel';
                break;
            case 6:
                $status = 'Rejected';
                break;
            default:
                $status = 'Unknown';
        }

        switch ($serviceOrder->order_status) {

            case 0:
                $order_status = 'Pending';
                break;
            case 1:
                $order_status = 'Complete';
                break;
            case 3:
                $order_status = 'Scheduled';
                break;
            case 4:
                $order_status = 'Live';
                break;
            case 5:
                $order_status = 'Shared';
                break;
            case 6:
                $order_status = 'Rejected';
                break;
            default:
                $order_status = 'Unknown status';
        }

        $serviceOrder->status = $status;
        $serviceOrder->order_status = $order_status;
        $isEdited = ServiceReview::where('order_id', $orderid)
            ->where('service_type', $serviceOrder->type)
            ->where('status', 1)
            ->value('is_edited');


        if ($serviceOrder->type == 'counselling') {
            $counsellingUser = CounsellingUser::where('order_id', $orderid)->first();

            if ($counsellingUser) {
                $serviceOrder['counselling_user'] = $counsellingUser;
            } else {
                $serviceOrder['counselling_user'] = null;
            }
        }

        return response()->json([
            'success' => true,
            'order' => $serviceOrder,
            'is_review' => $isEdited,
            'refund_day_limit' => getWebConfig('refund_day_limit'),
            'current_date' => Carbon::now(),
        ], 200);
    }

    public function chadhavaDetailsOrder($orderid)
    {
        $chadhavaOrder = Chadhava_orders::where('order_id', $orderid)
            ->with([
                'chadhava:id,name,thumbnail',
                'pandit:id,name,email,mobile_no',
                'product_leads:id,leads_id,final_price,qty,product_name,product_price',
            ])
            ->first();

        if (!$chadhavaOrder) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid order ID or order not found.',
            ], 404);
        }

        $customer = User::where('phone', $chadhavaOrder['leads']['person_phone'])
            ->select('id', 'name', 'email', 'phone')
            ->first();

        if ($customer) {
            $chadhavaOrder['customer'] = $customer;
        } else {
            $chadhavaOrder['customer'] = null;
        }

        if ($chadhavaOrder->chadhava) {
            $chadhavaOrder->chadhava->hi_name = $chadhavaOrder->chadhava->translations()
                ->where('key', 'name')
                ->first()
                ->value ?? null;

            $chadhavaOrder->chadhava->thumbnail = $chadhavaOrder->chadhava->thumbnail
                ? url("storage/app/public/chadhava/thumbnail/{$chadhavaOrder->chadhava->thumbnail}")
                : null;
        }

        $status = '';
        $order_status = '';

        switch ($chadhavaOrder->status) {
            case 0:
                $status = 'Pending';
                break;
            case 1:
                $status = 'Complete';
                break;
            case 2:
                $status = 'Cancel';
                break;
            case 6:
                $status = 'Rejected';
                break;
            default:
                $status = 'Unknown';
        }

        switch ($chadhavaOrder->order_status) {
            case 0:
                $order_status = 'Pending';
                break;
            case 1:
                $order_status = 'Complete';
                break;
            case 3:
                $order_status = 'Scheduled';
                break;
            case 4:
                $order_status = 'Live';
                break;
            case 5:
                $order_status = 'Shared';
                break;
            case 6:
                $order_status = 'Rejected';
                break;
            default:
                $order_status = 'Unknown status';
        }

        $chadhavaOrder->status = $status;
        $chadhavaOrder->order_status = $order_status;
        $isEdited = ServiceReview::where('order_id', $orderid)
            ->where('service_type', 'chadhava')
            ->where('status', 1)
            ->value('is_edited');


        return response()->json([
            'success' => true,
            'order' => $chadhavaOrder,
            'is_review' => $isEdited,
            'refund_day_limit' => getWebConfig('refund_day_limit'),
            'current_date' => Carbon::now(),
        ], 200);
    }

    // Order List
    public function poojaList(Request $request)
    {
        $order_by = $request->order_by ?? 'desc';
        $customerId = auth('customer')->id();

        $serviceOrders = Service_order::with(['services'])
            ->where([
                'customer_id' => $customerId,
                'type' => 'pooja'
            ])
            ->orderBy('id', $order_by)
            ->get();

        if ($serviceOrders->isEmpty()) {
            return response()->json([
                'data' => $serviceOrders,
                'message' => 'Pooja orders retrieved successfully.',
                'status' => 200
            ]);
        }
    }

    //-----------------------------------------------VIP AND ANUSHTHAN DETAILS------------------------------------------------------------
    public function getallVipPooja()
    {
        $vippooja = Vippooja::where('status', 1)->where('is_anushthan', '0')->get();
        if ($vippooja->isNotEmpty()) {
            $vippooja->transform(function ($vippooja) {

                $translations = $vippooja->translations()->pluck('value', 'key')->toArray();
                $vipData = [
                    'id' => $vippooja->id,
                    'en_name' => $vippooja->name,
                    'hi_name' => $translations['name'] ?? null,
                    'en_pooja_heading' => $vippooja->pooja_heading,
                    'hi_pooja_heading' => $translations['pooja_heading'] ?? null,
                    'slug' => $vippooja->slug,
                    'status' => $vippooja->status,
                    'en_short_benifits' => $vippooja->short_benifits,
                    'hi_short_benifits' => $translations['short_benifits'] ?? null,
                    'is_anushthan' => $vippooja->is_anushthan,
                    'is_anushthan' => $vippooja->is_anushthan,
                    'thumbnail' => $vippooja->thumbnail ? url('/storage/app/public/pooja/vip/thumbnail/' . $vippooja->thumbnail) : null,

                ];
                return $vipData;
            });
            $filteredData = $vippooja->filter(function ($item) {
                return !empty($item['en_name']) && $item !== null;
            })->values();

            if ($filteredData->isEmpty()) {
                return response()->json(['status' => 200, 'message' => 'No VIP Pooja Details']);
            } else {
                return response()->json(['status' => 200, 'data' => $filteredData]);
            }
        }
        return response()->json(['status' => 400, 'message' => 'No VIP Pooja Details']);
    }



    public function vipDetails($slug)
    {
        $vip = Vippooja::where('status', 1)->where('slug', $slug)->where('is_anushthan', '0')->with(['pandit', 'product', 'packages'])->first();

        if ($vip !== null) {  // Check if the service exists
            // Transform the service data
            $transformedeVip = $this->transformVipDetails($vip);

            if (empty($transformedeVip['en_name'])) {
                return response()->json(['status' => 400]);
            } else {
                return response()->json(['status' => 200, 'data' => $transformedeVip]);
            }
        }

        return response()->json(['status' => 400]);
    }
    // Anushthan Details Traslation
    private function transformVipDetails($vip)
    {
        $packages = json_decode($vip->packages_id, true);
        $vippoojaPackages = [];
        if (is_array($packages)) {
            foreach ($packages as $val) {
                $packageId = $val['package_id'];
                $packageModel = Package::find($packageId);
                if ($packageModel) {
                    $packageTranslations = $packageModel->translations()->pluck('value', 'key')->toArray();
                    $vippoojaPackages[] = [
                        'package_id' => $packageId,
                        'en_package_name' => $packageModel->title,
                        'hi_package_name' => $packageTranslations['title'] ?? null,
                        'person' => $packageModel->person,
                        'color' => $packageModel->color,
                        'en_description' => $packageModel->description,
                        'hi_description' => $packageTranslations['description'] ?? null,
                        'package_price' => $val['package_price']
                    ];
                }
            }
        }

        // Handle products
        $products = json_decode($vip->product_id, true);
        $vippoojaProducts = [];
        if (is_array($products)) {
            foreach ($products as $productId) {
                $ProductModel = Product::where('id', $productId)->where('status', 1)->first();
                if ($ProductModel) {
                    $productTranslations = $ProductModel->translations()->pluck('value', 'key')->toArray();
                    $vippoojaProducts[] = [
                        'product_id' => $productId,
                        'en_name' => $ProductModel->name,
                        'hi_name' => $productTranslations['name'] ?? null,
                        'en_details' => $ProductModel->details,
                        'hi_details' => $productTranslations['description'] ?? null,
                        'price' => $ProductModel->unit_price,
                        'thumbnail' => $ProductModel->thumbnail ? url('/storage/app/public/product/thumbnail/' . $ProductModel->thumbnail) : null,
                        'images' => $ProductModel->images ? url('/storage/app/public/product/' . $ProductModel->images) : null,
                    ];
                }
            }
        }
        // Process translations for the service
        $translations = $vip->translations()->pluck('value', 'key')->toArray();
        $count = Service_order::where('type', 'vip')->where('service_id',  $vip->id)->count();
        return [
            'count' => $count,
            'id' => $vip->id,
            'en_name' => $vip->name,
            'hi_name' => $translations['name'] ?? null,
            'en_pooja_heading' => $vip->pooja_heading,
            'hi_pooja_heading' => $translations['pooja_heading'] ?? null,
            'slug' => $vip->slug,
            'image' => $vip->images ? url('/storage/app/public/pooja/vip/' . $vip->images) : null,
            'status' => $vip->status,
            'user_id' => $vip->user_id,
            'added_by' => $vip->added_by,
            'en_short_benifits' => $vip->short_benifits,
            'hi_short_benifits' => $translations['short_benifits'] ?? null,
            'is_anushthan' => $vip->is_anushthan,
            'en_details' => $vip->details,
            'hi_details' => $translations['details'] ?? null,
            'en_benefits' => $vip->benefits,
            'hi_benefits' => $translations['benefits'] ?? null,
            'en_process' => $vip->process,
            'hi_process' => $translations['process'] ?? null,
            'en_temple_details' => $vip->temple_details,
            'hi_temple_details' => $translations['temple_details'] ?? null,
            'product_id' => $vip->product_id,
            'packages_id' => $vip->packages_id,
            'video_provider' => $vip->video_provider,
            'video_url' => $vip->video_url,
            'thumbnail' => $vip->thumbnail ? url('/storage/app/public/pooja/vip/thumbnail/' . $vip->thumbnail) : null,
            'digital_file_ready' => $vip->digital_file_ready,
            'en_meta_title' => $vip->meta_title,
            'hi_meta_title' => $translations['meta_title'] ?? null,
            'meta_description' => $vip->meta_description,
            'meta_image' => $vip->meta_image,
            'packages' => $vippoojaPackages,
            'products' => $vippoojaProducts,

        ];
    }

    // VIP DATE LEAD STORE AND SNAKAL DATA STORE
    public function VipLeadStore(Request $request)
    {
        $service_id   = $request->input('service_id');
        $package_id   = $request->input('package_id');
        $personPhone  = $request->input('person_phone');

        $customer = User::where('phone', $personPhone)->first(); // FIXED

        $full_name = $customer ? trim(($customer->f_name ?? '') . ' ' . ($customer->l_name ?? '')) : '';
        $full_name = $full_name ?: ($customer->name ?? '');
        // Step 1: Fetch VIP Pooja record
        
        $vip = Vippooja::where('id', $service_id)->where('status', '1')->first();

        if (!$vip) {
            return response()->json(['status' => false, 'message' => 'VIP Pooja not found.'], 404);
        }
        // Allow only if is_anushthan == '0'
        if ($vip->is_anushthan != '0') {
            return response()->json(['status' => false, 'message' => 'Only VIP Pooja allowed here.'], 403);
        }

        $type = 'vip'; // Safe to assign now
        // Step 2: Decode packages and product_ids JSON
        $vipPackages = json_decode($vip->packages_id, true);
        $vipProducts = json_decode($vip->product_id, true);

        // Step 3: Find package with matching package_id (no need to check price from user)
        $matchedPackage = null;
        foreach ($vipPackages as $pkg) {
            if ((int)$pkg['package_id'] === (int)$package_id) {
                $matchedPackage = $pkg;
                break;
            }
        }

        if (!$matchedPackage) {
            return response()->json(['status' => false, 'message' => 'Package ID not found in VIP Pooja record.'], 400);
        }

        // Step 4: Get package details from 'packages' table
        $package = Package::find($package_id);
        if (!$package) {
            return response()->json(['status' => false, 'message' => 'Package details not found.'], 404);
        }
        // Step 5: Store in leads table
        $lead = new Leads();
        $lead->service_id     = $vip->id;
        $lead->type           = $type;
        $lead->package_id     = $matchedPackage['package_id'];
        $lead->package_price  = $matchedPackage['package_price']; // fetched from VIP Pooja
        $lead->package_name   = $package->title;
        $lead->noperson       = $package->person;
        $lead->product_id     = json_encode($vipProducts); // if you want to store all products
        $lead->person_phone   = $customer->phone;
        $lead->person_name    = $full_name;  // Optional fields
        $lead->customer_id    = $customer->id;
        $lead->platform       = 'app';
        $lead->booking_date   =  $request->input('booking_date');
        $lead->save();
        // Get inserted ID
        $insertedRowId = $lead->id;

        if (!empty($insertedRowId)) {
            $leadno = 'VP' . (100 + $insertedRowId); // Removed +1 to avoid skipping
        } else {
            $leadno = 'VP101';
        }

        $lead->leadno = $leadno;
        $lead->save(); // Efficient: No need for separate update query

        return response()->json([
            'status'  => true,
            'message' => 'Lead successfully created.',
            'lead'    => $lead,
        ], 200);
        
    }

    public function VipSankalpStore($orderId, Request $request)
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
            'longitude' => $request->input('longitude'),
            'latitude' => $request->input('latitude'),
            'members' => json_encode($request->input('members')),
            'is_prashad' => $request->input('is_prashad'),
        ];

        $sankalpUpdate = Service_order::where('order_id', $orderId)->update($cust_details);
        $serviceID = Service_order::where('order_id', $orderId)->first();
        if (!$serviceID) {
            return redirect()->back()->with('error', 'Order not found.');
        }

        // Get Customer Info
        $customer = User::find($serviceID->customer_id);
        $full_name = '';
        if ($customer) {
            $full_name = trim($customer->f_name . ' ' . $customer->l_name);
            $full_name = $full_name ?: $customer->name ?: '';
        }

        // Create Devotee Record
        Devotee::create([
            'name'             => $full_name,
            'phone'            => $request->input('newPhone') ?: ($customer->phone ?? ''),
            'gotra'            => $request->input('gotra'),
            'service_order_id' => $orderId,
            'members'          => $request->has('members') ? json_encode($request->input('members')) : '[]',
            'address_city'     => $request->input('city'),
            'address_state'    => $request->input('state'),
            'house_no'         => $request->input('house_no'),
            'address_pincode'  => $request->input('pincode'),
            'area'             => $request->input('area'),
            'latitude'         => $request->input('latitude'),
            'longitude'        => $request->input('longitude'),
            'landmark'         => $request->input('landmark'),
            'is_prashad'       => $request->input('is_prashad'),
            'status'       => 1,
            'type'       => $serviceID->type ?? '',
        ]);

        if ($serviceID) {
            \App\Models\User::where('id', $serviceID->customer_id)->update([
                'zip' => $request->input('pincode'),
                'city' => $request->input('city'),
                'house_no' => $request->input('house_no'),
                'street_address' => $request->input('area'),
            ]);
        }

        if ($request->input('is_prashad') == 1) {
            $prashad_order = [
                'seller_id' => '14',
                'order_id' => $orderId,
                'warehouse_id' => '61202',
                'service_id' => $serviceID->service_id,
                'user_id' =>   $serviceID->customer_id,
                'product_id' => '853',
                'type' => $serviceID->type,
                'payment_type' => 'P',
                'booking_date' => $serviceID->booking_date
            ];
            $prashadOrder = Prashad_deliverys::create($prashad_order);
        }
        $sankalpData = Service_order::where('order_id', $orderId)->with(['customers', 'services', 'packages', 'leads'])->first();
        $UsersData = Service_order::where('type', 'vip')->with(['customers', 'vippoojas', 'packages', 'leads'])->first();
        $serviceReview = ServiceReview::create([
            'order_id' => $orderId,
            'user_id' => $sankalpData['customer_id'],
            'service_id' => $serviceID->service_id,
            'service_type' => 'vip',
            'rating' => '5',
        ]);
        $membersList = json_decode($sankalpData['members'], true);
        $formattedMembers = $membersList ? implode(', ', $membersList) : 'No members specified';

        // whatsapp
        $userInfo = \App\Models\User::where('id', ($sankalpData['customer_id'] ?? ""))->first();
        $service_name = \App\Models\Vippooja::where('id', ($sankalpData['service_id'] ?? ""))->where('is_anushthan', 0)->first();
        $bookingDetails = \App\Models\Service_order::where('service_id', ($sankalpData['service_id'] ?? ""))->where('type', 'vip')
            ->where('booking_date', ($sankalpData['booking_date'] ?? ""))
            ->where('customer_id', ($sankalpData['customer_id'] ?? ""))
            ->where('order_id', ($orderId ?? ""))
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
            'orderId' => $orderId,
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
        if ($sankalpData) {
            return response()->json([
                'success' => true,
                'message' => 'Vip Sankalp Record Successfully Inserted.',
                'sankalpData' => $sankalpData,
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update service order.',
            ], 500);
        }
    }

    public function VipSankalpUpdate($orderId, Request $request)
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
            'members' => json_encode($request->input('members')),
            'is_prashad' => $request->input('is_prashad'),
            'is_edited' => '1',
        ];

        $sankalpUpdate = Service_order::where('order_id', $orderId)->update($cust_details);
        $serviceOrder = Service_order::where('order_id', $orderId)->first();
        if (!$serviceOrder) {
            return redirect()->back()->with('error', 'Order not found.');
        }
        $customer = User::find($serviceOrder->customer_id);
        $full_name = '';
        if ($customer) {
            $full_name = trim($customer->f_name . ' ' . $customer->l_name);
            $full_name = $full_name ?: $customer->name ?: '';
        }
        $devotee = Devotee::where('service_order_id', $orderId)->first();
        if ($devotee) {
            $devotee->update([
                'name'             => $full_name,
                'phone'            => $request->input('newPhone') ?: ($customer->phone ?? ''),
                'gotra'            => $request->input('gotra'),
                'members'          => $request->has('members') ? json_encode($request->input('members')) : '[]',
                'address_city'     => $request->input('city'),
                'address_state'    => $request->input('state'),
                'house_no'         => $request->input('house_no'),
                'address_pincode'  => $request->input('pincode'),
                'area'             => $request->input('area'),
                'latitude'         => $request->input('latitude'),
                'longitude'        => $request->input('longitude'),
                'landmark'         => $request->input('landmark'),
                'is_prashad'       => $request->input('is_prashad'),
                'status'       => 1,
                'type'       => $serviceID->type ?? '',
            ]);
        } else {
            Devotee::create([
                'name'             => $full_name,
                'phone'            => $request->input('newPhone') ?: ($customer->phone ?? ''),
                'gotra'            => $request->input('gotra'),
                'service_order_id' => $orderId,
                'members'          => $request->has('members') ? json_encode($request->input('members')) : '[]',
                'address_city'     => $request->input('city'),
                'address_state'    => $request->input('state'),
                'house_no'         => $request->input('house_no'),
                'address_pincode'  => $request->input('pincode'),
                'area'             => $request->input('area'),
                'latitude'         => $request->input('latitude'),
                'longitude'        => $request->input('longitude'),
                'landmark'         => $request->input('landmark'),
                'is_prashad'       => $request->input('is_prashad'),
                'status'       => 1,
                'type'       => $serviceID->type ?? '',
            ]);
        }
        $sankalpData = Service_order::where('order_id', $orderId)->with(['customers', 'services','vippoojas', 'packages', 'leads'])->first();

        if ($request->input('is_prashad') == 1) {
            $prashad_order = [
                'seller_id' => '14',
                'order_id' => $orderId,
                'warehouse_id' => '61202',
                'service_id' => $sankalpData->service_id,
                'user_id' =>   $sankalpData->customer_id,
                'product_id' => '853',
                'type' => $sankalpData->type,
                'payment_type' => 'P',
                'booking_date' => $sankalpData->booking_date
            ];
            $prashadOrder = Prashad_deliverys::create($prashad_order);
        }
        $UsersData = Service_order::where('type', 'vip')->with(['customers', 'vippoojas', 'packages', 'leads'])->first();
        $membersList = json_decode($sankalpData['members'], true);
        $formattedMembers = $membersList ? implode(', ', $membersList) : 'No members specified';
        $serviceReview = ServiceReview::create([
            'order_id' => $orderId,
            'user_id' => $sankalpData['customer_id'],
            'service_id' => $sankalpData->service_id,
            'service_type' => 'vip',
            'rating' => '5',
        ]);

        // whatsapp
        $userInfo = \App\Models\User::where('id', ($sankalpData['customer_id'] ?? ""))->first();
        $service_name = \App\Models\Vippooja::where('id', ($sankalpData['service_id'] ?? ""))->where('is_anushthan', 0)->first();
        $bookingDetails = \App\Models\Service_order::where('service_id', ($sankalpData['service_id'] ?? ""))->where('type', 'vip')
            ->where('booking_date', ($sankalpData['booking_date'] ?? ""))
            ->where('customer_id', ($sankalpData['customer_id'] ?? ""))
            ->where('order_id', ($orderId ?? ""))
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
            'orderId' => $orderId,
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
        if ($sankalpData) {
            return response()->json([
                'success' => true,
                'message' => 'Vip Sankalp Record Successfully Inserted.',
                'sankalpData' => $sankalpData,
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update service order.',
            ], 500);
        }
    }

    public function getallAnushthan()
    {
        $vippooja = Vippooja::where('status', 1)->where('is_anushthan', '1')->get();
        if ($vippooja->isNotEmpty()) {
            $vippooja->transform(function ($vippooja) {

                $translations = $vippooja->translations()->pluck('value', 'key')->toArray();
                $vipData = [
                    'id' => $vippooja->id,
                    'en_name' => $vippooja->name,
                    'hi_name' => $translations['name'] ?? null,
                    'en_pooja_heading' => $vippooja->pooja_heading,
                    'hi_pooja_heading' => $translations['pooja_heading'] ?? null,
                    'slug' => $vippooja->slug,
                    'status' => $vippooja->status,
                    'en_short_benifits' => $vippooja->short_benifits,
                    'hi_short_benifits' => $translations['short_benifits'] ?? null,
                    'is_anushthan' => $vippooja->is_anushthan,
                    'is_anushthan' => $vippooja->is_anushthan,
                    'thumbnail' => $vippooja->thumbnail ? url('/storage/app/public/pooja/vip/thumbnail/' . $vippooja->thumbnail) : null,

                ];
                return $vipData;
            });
            $filteredData = $vippooja->filter(function ($item) {
                return !empty($item['en_name']) && $item !== null;
            })->values();

            if ($filteredData->isEmpty()) {
                return response()->json(['status' => 200, 'message' => 'No Anushthan Details']);
            } else {
                return response()->json(['status' => 200, 'data' => $filteredData]);
            }
        }
        return response()->json(['status' => 400, 'message' => 'No Anushthan Details']);
    }

    public function AnushthanDetails($slug)
    {
        $anushthan = Vippooja::where('status', 1)->where('slug', $slug)->where('is_anushthan', '1')->with(['pandit', 'product'])->first();

        if ($anushthan !== null) {  // Check if the service exists
            // Transform the service data
            $transformedaAnushthan = $this->transformAnushthanDetails($anushthan);

            if (empty($transformedaAnushthan['en_name'])) {
                return response()->json(['status' => 400]);
            } else {
                return response()->json(['status' => 200, 'data' => $transformedaAnushthan]);
            }
        }

        return response()->json(['status' => 400]);
    }
    // Anushthan Details Traslation
    private function transformAnushthanDetails($anushthan)
    {
        $packages = json_decode($anushthan->packages_id, true);
        $vippoojaPackages = [];
        if (is_array($packages)) {
            foreach ($packages as $val) {
                $packageId = $val['package_id'];
                $packageModel = Package::find($packageId);
                if ($packageModel) {
                    $packageTranslations = $packageModel->translations()->pluck('value', 'key')->toArray();
                    $vippoojaPackages[] = [
                        'package_id' => $packageId,
                        'en_package_name' => $packageModel->title,
                        'hi_package_name' => $packageTranslations['title'] ?? null,
                        'person' => $packageModel->person,
                        'color' => $packageModel->color,
                        'en_description' => $packageModel->description,
                        'hi_description' => $packageTranslations['description'] ?? null,
                        'package_price' => $val['package_price']
                    ];
                }
            }
        }

        // Handle products
        $products = json_decode($anushthan->product_id, true);
        $vippoojaProducts = [];
        if (is_array($products)) {
            foreach ($products as $productId) {
                $ProductModel = Product::where('id', $productId)->where('status', 1)->first();
                if ($ProductModel) {
                    $productTranslations = $ProductModel->translations()->pluck('value', 'key')->toArray();
                    $vippoojaProducts[] = [
                        'product_id' => $productId,
                        'en_name' => $ProductModel->name,
                        'hi_name' => $productTranslations['name'] ?? null,
                        'en_details' => $ProductModel->details,
                        'hi_details' => $productTranslations['description'] ?? null,
                        'price' => $ProductModel->unit_price,
                        'thumbnail' => $ProductModel->thumbnail ? url('/storage/app/public/product/thumbnail/' . $ProductModel->thumbnail) : null,
                        'images' => $ProductModel->images ? url('/storage/app/public/product/' . $ProductModel->images) : null,
                    ];
                }
            }
        }
        // Process translations for the service
        $translations = $anushthan->translations()->pluck('value', 'key')->toArray();
        $count = Service_order::where('type', 'anushthan')->where('service_id',  $anushthan->id)->count();
        return [
            'count' => $count,
            'id' => $anushthan->id,
            'en_name' => $anushthan->name,
            'hi_name' => $translations['name'] ?? null,
            'en_pooja_heading' => $anushthan->pooja_heading,
            'hi_pooja_heading' => $translations['pooja_heading'] ?? null,
            'slug' => $anushthan->slug,
            'image' => $anushthan->images ? url('/storage/app/public/pooja/vip/' . $anushthan->images) : null,
            'status' => $anushthan->status,
            'user_id' => $anushthan->user_id,
            'added_by' => $anushthan->added_by,
            'en_short_benifits' => $anushthan->short_benifits,
            'hi_short_benifits' => $translations['short_benifits'] ?? null,
            'en_details' => $anushthan->details,
            'hi_details' => $translations['details'] ?? null,
            'en_benefits' => $anushthan->benefits,
            'hi_benefits' => $translations['benefits'] ?? null,
            'en_process' => $anushthan->process,
            'hi_process' => $translations['process'] ?? null,
            'en_temple_details' => $anushthan->temple_details,
            'hi_temple_details' => $translations['temple_details'] ?? null,
            'is_anushthan' => $anushthan->is_anushthan,
            'product_id' => $anushthan->product_id,
            'packages_id' => $anushthan->packages_id,
            'video_provider' => $anushthan->video_provider,
            'video_url' => $anushthan->video_url,
            'thumbnail' => $anushthan->thumbnail ? url('/storage/app/public/pooja/vip/thumbnail/' . $anushthan->thumbnail) : null,
            'digital_file_ready' => $anushthan->digital_file_ready,
            'en_meta_title' => $anushthan->meta_title,
            'hi_meta_title' => $translations['meta_title'] ?? null,
            'meta_description' => $anushthan->meta_description,
            'meta_image' => $anushthan->meta_image,
            'packages' => $vippoojaPackages,
            'products' => $vippoojaProducts,

        ];
    }

    // VIP DATE LEAD STORE AND SNAKAL DATA STORE
    public function AnushthanLeadStore(Request $request)
    {
        $service_id   = $request->input('service_id');
        $package_id   = $request->input('package_id');
        $personPhone  = $request->input('person_phone');

        $customer = User::where('phone', $personPhone)->first(); // FIXED

        $full_name = $customer ? trim(($customer->f_name ?? '') . ' ' . ($customer->l_name ?? '')) : '';
        $full_name = $full_name ?: ($customer->name ?? '');
        // Step 1: Fetch VIP Pooja record
        $anushthan = Vippooja::where('id', $service_id)->where('status', '1')->first();

        if (!$anushthan) {
            return response()->json(['status' => false, 'message' => 'Anushthan Pooja not found.'], 404);
        }
        // Allow only if is_anushthan == '0'
        if ($anushthan->is_anushthan != '1') {
            return response()->json(['status' => false, 'message' => 'Only Anushthan Pooja allowed here.'], 403);
        }

        $type = 'anushthan';
        // Step 2: Decode packages and product_ids JSON
        $anushthanPackages = json_decode($anushthan->packages_id, true);
        $anushthanProducts = json_decode($anushthan->product_id, true);

        // Step 3: Find package with matching package_id (no need to check price from user)
        $matchedPackage = null;
        foreach ($anushthanPackages as $pkg) {
            if ((int)$pkg['package_id'] === (int)$package_id) {
                $matchedPackage = $pkg;
                break;
            }
        }

        if (!$matchedPackage) {
            return response()->json(['status' => false, 'message' => 'Package ID not found in VIP Pooja record.'], 400);
        }

        // Step 4: Get package details from 'packages' table
        $package = Package::find($package_id);
        if (!$package) {
            return response()->json(['status' => false, 'message' => 'Package details not found.'], 404);
        }
        // Step 5: Store in leads table
        $lead = new Leads();
        $lead->service_id     = $anushthan->id;
        $lead->type           = $type;
        $lead->package_id     = $matchedPackage['package_id'];
        $lead->package_price  = $matchedPackage['package_price']; // fetched from VIP Pooja
        $lead->package_name   = $package->title;
        $lead->noperson       = $package->person;
        $lead->product_id     = json_encode($anushthanProducts); // if you want to store all products
        $lead->person_phone   = $customer->phone;
        $lead->person_name    = $full_name;  // Optional fields
        $lead->customer_id    = $customer->id;
        $lead->platform       = 'app';
        $lead->booking_date   =  $request->input('booking_date');
        $lead->save();
        // Get inserted ID
        $insertedRowId = $lead->id;

        if (!empty($insertedRowId)) {
            $leadno = 'APJ' . (100 + $insertedRowId); // Removed +1 to avoid skipping
        } else {
            $leadno = 'APJ101';
        }

        // Update leadno in same model instance
        $lead->leadno = $leadno;
        $lead->save(); // Efficient: No need for separate update query

        return response()->json([
            'status'  => true,
            'message' => 'Lead successfully created.',
            'lead'    => $lead,
        ], 200);
    }

    public function AnushthanSankalpStore($orderId, Request $request)
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
            'longitude' => $request->input('longitude'),
            'latitude' => $request->input('latitude'),
            'members' => json_encode($request->input('members')),
            'is_prashad' => $request->input('is_prashad'),
        ];

        $sankalpUpdate = Service_order::where('order_id', $orderId)->update($cust_details);

        $serviceID = Service_order::where('order_id', $orderId)->first();
        if (!$serviceID) {
            return redirect()->back()->with('error', 'Order not found.');
        }

        // Get Customer Info
        $customer = User::find($serviceID->customer_id);
        $full_name = '';
        if ($customer) {
            $full_name = trim($customer->f_name . ' ' . $customer->l_name);
            $full_name = $full_name ?: $customer->name ?: '';
        }

        // Create Devotee Record
        Devotee::create([
            'name'             => $full_name,
            'phone'            => $request->input('newPhone') ?: ($customer->phone ?? ''),
            'gotra'            => $request->input('gotra'),
            'service_order_id' => $orderId,
            'members'          => $request->has('members') ? json_encode($request->input('members')) : '[]',
            'address_city'     => $request->input('city'),
            'address_state'    => $request->input('state'),
            'house_no'         => $request->input('house_no'),
            'address_pincode'  => $request->input('pincode'),
            'area'             => $request->input('area'),
            'latitude'         => $request->input('latitude'),
            'longitude'        => $request->input('longitude'),
            'landmark'         => $request->input('landmark'),
            'is_prashad'       => $request->input('is_prashad'),
            'status'       => 1,
            'type'       => $serviceID->type ?? '',
        ]);
        
        if ($serviceID) {
            \App\Models\User::where('id', $serviceID->customer_id)->update([
                'zip' => $request->input('pincode'),
                'city' => $request->input('city'),
                'house_no' => $request->input('house_no'),
                'street_address' => $request->input('area'),
            ]);
        }

        if ($request->input('is_prashad') == 1) {
            $prashad_order = [
                'seller_id' => '14',
                'order_id' => $orderId,
                'warehouse_id' => '61202',
                'service_id' => $serviceID->service_id,
                'user_id' =>   $serviceID->customer_id,
                'product_id' => '853',
                'type' => $serviceID->type,
                'payment_type' => 'P',
                'booking_date' => $serviceID->booking_date
            ];
            $prashadOrder = Prashad_deliverys::create($prashad_order);
        }
        $sankalpData = Service_order::where('order_id', $orderId)->with(['customers', 'services','vippoojas', 'packages', 'leads'])->first();
        $serviceReview = ServiceReview::create([
            'order_id' => $orderId,
            'user_id' => $sankalpData['customer_id'],
            'service_id' => $serviceID->service_id,
            'service_type' => 'anushthan',
            'rating' => '5',
        ]);
        $UsersData = Service_order::where('type', 'anushthan')->with(['customers', 'vippoojas', 'packages', 'leads'])->first();
        $membersList = json_decode($sankalpData['members'], true);
        $formattedMembers = $membersList ? implode(', ', $membersList) : 'No members specified';

        // whatsapp
        $userInfo = \App\Models\User::where('id', ($sankalpData['customer_id'] ?? ""))->first();
        $service_name = \App\Models\Vippooja::where('id', ($sankalpData['service_id'] ?? ""))->where('is_anushthan', 1)->first();
        $bookingDetails = \App\Models\Service_order::where('service_id', ($sankalpData['service_id'] ?? ""))->where('type', 'anushthan')
            ->where('booking_date', ($sankalpData['booking_date'] ?? ""))
            ->where('customer_id', ($sankalpData['customer_id'] ?? ""))
            ->where('order_id', ($orderId ?? ""))
            ->first();

        $message_data = [
            'service_name' => $sankalpData['vippoojas']['name'],
            'member_names' => $formattedMembers,
            'gotra' => $request->input('gotra'),
            'prashad' => $sankalpData['is_prashad'] == 1 ? 'Your Prasad is being prepared and will be dispatched to your address within 7-8 days, only if you selected "Yes" for receiving Prasad.' : '',
            'type' => 'text-with-media',
            'attachment' =>  asset('/storage/app/public/pooja/vip/thumbnail/' . $service_name->thumbnail),
            'booking_date' => date('d-m-Y', strtotime($sankalpData['booking_date'])),
            'puja_venue' => $service_name['pooja_venue'],
            'pooja' => 'Anushthan',
            'orderId' => $orderId,
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
        if ($sankalpData) {
            return response()->json([
                'success' => true,
                'message' => 'Anushthan Sankalp Record Successfully Inserted.',
                'sankalpData' => $sankalpData,
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update service order.',
            ], 500);
        }
    }

    public function AnushthanSankalpUpdate($orderId, Request $request)
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
            'members' => json_encode($request->input('members')),
            'is_prashad' => $request->input('is_prashad'),
            'is_edited' => '1',
        ];

        $sankalpUpdate = Service_order::where('order_id', $orderId)->update($cust_details);
        $serviceOrder = Service_order::where('order_id', $orderId)->first();
        if (!$serviceOrder) {
            return redirect()->back()->with('error', 'Order not found.');
        }
        $customer = User::find($serviceOrder->customer_id);
        $full_name = '';
        if ($customer) {
            $full_name = trim($customer->f_name . ' ' . $customer->l_name);
            $full_name = $full_name ?: $customer->name ?: '';
        }
        $devotee = Devotee::where('service_order_id', $orderId)->first();
        if ($devotee) {
            $devotee->update([
                'name'             => $full_name,
                'phone'            => $request->input('newPhone') ?: ($customer->phone ?? ''),
                'gotra'            => $request->input('gotra'),
                'members'          => $request->has('members') ? json_encode($request->input('members')) : '[]',
                'address_city'     => $request->input('city'),
                'address_state'    => $request->input('state'),
                'house_no'         => $request->input('house_no'),
                'address_pincode'  => $request->input('pincode'),
                'area'             => $request->input('area'),
                'latitude'         => $request->input('latitude'),
                'longitude'        => $request->input('longitude'),
                'landmark'         => $request->input('landmark'),
                'is_prashad'       => $request->input('is_prashad'),
            ]);
        } else {
            Devotee::create([
                'name'             => $full_name,
                'phone'            => $request->input('newPhone') ?: ($customer->phone ?? ''),
                'gotra'            => $request->input('gotra'),
                'service_order_id' => $orderId,
                'members'          => $request->has('members') ? json_encode($request->input('members')) : '[]',
                'address_city'     => $request->input('city'),
                'address_state'    => $request->input('state'),
                'house_no'         => $request->input('house_no'),
                'address_pincode'  => $request->input('pincode'),
                'area'             => $request->input('area'),
                'latitude'         => $request->input('latitude'),
                'longitude'        => $request->input('longitude'),
                'landmark'         => $request->input('landmark'),
                'is_prashad'       => $request->input('is_prashad'),
            ]);
        }
        $sankalpData = Service_order::where('order_id', $orderId)->with(['customers', 'services','vippoojas','packages', 'leads'])->first();

        if ($request->input('is_prashad') == 1) {
            $prashad_order = [
                'seller_id' => '14',
                'order_id' => $orderId,
                'warehouse_id' => '61202',
                'service_id' => $sankalpData->service_id,
                'user_id' =>   $sankalpData->customer_id,
                'product_id' => '853',
                'type' => $sankalpData->type,
                'payment_type' => 'P',
                'booking_date' => $sankalpData->booking_date
            ];
            $prashadOrder = Prashad_deliverys::create($prashad_order);
        }
        $UsersData = Service_order::where('type', 'anushthan')->with(['customers', 'vippoojas', 'packages', 'leads'])->first();
        $membersList = json_decode($sankalpData['members'], true);
        $formattedMembers = $membersList ? implode(', ', $membersList) : 'No members specified';
        $serviceReview = ServiceReview::create([
            'order_id' => $orderId,
            'user_id' => $sankalpData['customer_id'],
            'service_id' => $sankalpData->service_id,
            'service_type' => 'anushthan',
            'rating' => '5',
        ]);
        // whatsapp
        $userInfo = \App\Models\User::where('id', ($sankalpData['customer_id'] ?? ""))->first();
        $service_name = \App\Models\Vippooja::where('id', ($sankalpData['service_id'] ?? ""))->where('is_anushthan', 1)->first();
        $bookingDetails = \App\Models\Service_order::where('service_id', ($sankalpData['service_id'] ?? ""))->where('type', 'anushthan')
            ->where('booking_date', ($sankalpData['booking_date'] ?? ""))
            ->where('customer_id', ($sankalpData['customer_id'] ?? ""))
            ->where('order_id', ($orderId ?? ""))
            ->first();

        $message_data = [
            'service_name' => $sankalpData['vippoojas']['name'],
            'member_names' => $formattedMembers,
            'gotra' => $request->input('gotra'),
            'prashad' => $sankalpData['is_prashad'] == 1 ? 'Your Prasad is being prepared and will be dispatched to your address within 7-8 days, only if you selected "Yes" for receiving Prasad.' : '',
            'type' => 'text-with-media',
            'attachment' =>  asset('/storage/app/public/pooja/vip/thumbnail/' . $service_name->thumbnail),
            'booking_date' => date('d-m-Y', strtotime($sankalpData['booking_date'])),
            'puja_venue' => $service_name['pooja_venue'],
            'pooja' => 'Anushthan',
            'orderId' => $orderId,
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
        if ($sankalpData) {
            return response()->json([
                'success' => true,
                'message' => 'Anushthan Sankalp Record Successfully Inserted.',
                'sankalpData' => $sankalpData,
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update service order.',
            ], 500);
        }
    }
    // -------------------------------------------------GET ALL THE CHADHAVA DETAILS=-----------------------------------------------------
    public function getallChadhava()
    {
       $chadhava = Chadhava::with(['pandit', 'product', 'cities'])->where('status', 1)
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->where('chadhava_type', 1)
                    ->where('end_date', '>', date('Y-m-d'));
                })->orWhere('chadhava_type', 0);
            })
            ->get();

    
        if ($chadhava->isNotEmpty()) {
            $chadhava->transform(function ($chadhava) {

                // dd($GetName);
                $products = json_decode($chadhava->product_id, true);
                $chadhavaProducts = [];
                if (is_array($products)) {
                    foreach ($products as $productId) {
                        $ProductModel = Product::where('id', $productId)
                        ->where('status', 1)
                        ->first();
                    
                        if ($ProductModel) {
                            $productTranslations = $ProductModel->translations()->pluck('value', 'key')->toArray();
                            $chadhavaProducts[] = [
                                'product_id' => $productId,
                                'en_name' => $ProductModel->name,
                                'hi_name' => $productTranslations['name'] ?? null,
                                'en_details' => $ProductModel->details,
                                'hi_details' => $productTranslations['description'] ?? null,
                                'price' => $ProductModel->unit_price,
                                'thumbnail' => $ProductModel->thumbnail ? url('/storage/app/public/product/thumbnail/' . $ProductModel->thumbnail) : null,
                                'images' => $ProductModel->images ? url('/storage/app/public/product/' . $ProductModel->images) : null,
                            ];
                        }
                    }
                }

                // Process translations for the service
                $translations = $chadhava->translations()->pluck('value', 'key')->toArray();

                // Format final service object
                $chadhavaData = [
                    'id' => $chadhava->id,
                    'en_name' => $chadhava->name,
                    'hi_name' => $translations['name'] ?? null,
                    'en_pooja_heading' => $chadhava->pooja_heading,
                    'hi_pooja_heading' => $translations['pooja_heading'] ?? null,
                    'slug' => $chadhava->slug,
                    'image' => $chadhava->images ? url('/storage/app/public/chadhava/' . $chadhava->images) : null,
                    'status' => $chadhava->status,
                    'user_id' => $chadhava->user_id,
                    'added_by' => $chadhava->added_by,
                    'en_short_details' => $chadhava->short_details,
                    'hi_short_details' => $translations['short_details'] ?? null,
                    'product_type' => $chadhava->product_type,
                    'en_details' => $chadhava->details,
                    'hi_details' => $translations['description'] ?? null,
                    'product_id' => $chadhava->product_id,
                    'en_chadhava_venue' => $chadhava->chadhava_venue,
                    'hi_chadhava_venue' => $translations['chadhava_venue'] ?? null,
                    'chadhava_week' => $chadhava->chadhava_week,
                    'is_video' => $chadhava->is_video,
                    'thumbnail' => $chadhava->thumbnail ? url('/storage/app/public/chadhava/thumbnail/' . $chadhava->thumbnail) : null,
                    'digital_file_ready' => $chadhava->digital_file_ready,
                    'en_meta_title' => $chadhava->meta_title,
                    'hi_meta_title' => $translations['meta_title'] ?? null,
                    'meta_description' => $chadhava->meta_description,
                    'meta_image' => $chadhava->meta_image,
                    'products' => $chadhavaProducts,
                ];

                // Additional processing for week days
                $nextChadhavaDay = $chadhava->getNextAvailableDate();

                // Check pooja_type and handle accordingly
                if ($chadhava->chadhava_type == 1) {
                    $startDate = $chadhava->start_date;
                    $endDate = $chadhava->end_date;
                    $currentDate = time();
                    $formattedDates = [];
                    $ChadhavaearliestDate = '';

                    if ($startDate && $endDate && $startDate <= $endDate) {
                        $currentDateIter = $startDate->copy();
                        while ($currentDateIter <= $endDate) {
                            $formattedDates[] = $currentDateIter->format('Y-m-d');
                            $currentDateIter->addDay();
                        }

                        foreach ($formattedDates as $date) {
                            if (strtotime($date) > $currentDate) {
                                $ChadhavaearliestDate = date('Y-m-d H:i:s', strtotime($date));
                                break;
                            }
                        }
                    }
                    $chadhavaData['next_chadhava_date'] = $ChadhavaearliestDate ?: null;
                    $chadhavaData['chadhava_type_text'] = 'Date Wise Chadhava';
                } else {
                    $chadhavaData['next_chadhava_date'] = $nextChadhavaDay instanceof \DateTime ? $nextChadhavaDay->format('Y-m-d H:i:s') : ($nextChadhavaDay ? date('Y-m-d H:i:s', strtotime($nextChadhavaDay)) : null);
                    $chadhavaData['chadhava_type_text'] = 'Weekly Chadhava';
                }

                return $chadhavaData;
            });

            // Filter out services with empty English names or null service data
            $filteredData = $chadhava->filter(function ($item) {
                return !empty($item['en_name']) && $item !== null;
            })->values();

            return $filteredData->isEmpty()
                ? response()->json(['status' => 200, 'message' => 'No Date Wise Chadhava'])
                : response()->json(['status' => 200, 'data' => $filteredData]);
        }

        return response()->json(['status' => 400, 'message' => 'No  Date Wise Chadhava']);
    }

   
    public function chadhavaDetails($identifier)
    {
        if (is_numeric($identifier)) {
            $chadhava = Chadhava::where('status', 1)
                ->where('id', $identifier)
                ->with(['cities', 'product'])
                ->first();
        } else {
            $chadhava = Chadhava::where('status', 1)
                ->where('slug', $identifier)
                ->with(['cities', 'product'])
                ->first();
        }

        if ($chadhava !== null) {
            $transformedChadhava = $this->transformChadhavaDetails($chadhava);

            if (empty($transformedChadhava['en_name'])) {
                return response()->json(['status' => 400]);
            } else {
                return response()->json(['status' => 200, 'data' => $transformedChadhava]);
            }
        }

        return response()->json(['status' => 400]);
    }
    // Chadahva TrasformDetails
    private function transformChadhavaDetails($chadhava)
    {

        $products = json_decode($chadhava->product_id, true);
        $chadhavaProducts = [];
        if (is_array($products)) {
            foreach ($products as $productId) {
                $ProductModel = Product::where('id', $productId)
                       ->where('status', 1)
                       ->first();
                if ($ProductModel) {
                    $productTranslations = $ProductModel->translations()->pluck('value', 'key')->toArray();
                    $chadhavaProducts[] = [
                        'product_id' => $productId,
                        'en_name' => $ProductModel->name,
                        'hi_name' => $productTranslations['name'] ?? null,
                        'en_details' => $ProductModel->details,
                        'hi_details' => $productTranslations['description'] ?? null,
                        'price' => $ProductModel->unit_price,
                        'thumbnail' => $ProductModel->thumbnail ? url('/storage/app/public/product/thumbnail/' . $ProductModel->thumbnail) : null,
                        'images' => $ProductModel->images ? url('/storage/app/public/product/' . $ProductModel->images) : null,
                    ];
                }
            }
        }


        // Process translations for the service
        $translations = $chadhava->translations()->pluck('value', 'key')->toArray();
        $count = Chadhava_orders::where('type', 'chadhava')->where('service_id',  $chadhava->id)->count();
        // Format final service object
        $chadhavaData = [
            'count' => $count,
            'id' => $chadhava->id,
            'en_name' => $chadhava->name,
            'hi_name' => $translations['name'] ?? null,
            'slug' => $chadhava->slug,
            'image' => $chadhava->images ? url('/storage/app/public/chadhava/' . $chadhava->images) : null,
            'status' => $chadhava->status,
            'user_id' => $chadhava->user_id,
            'added_by' => $chadhava->added_by,
            'en_short_details' => $chadhava->short_details,
            'hi_short_details' => $translations['short_details'] ?? null,
            'product_type' => $chadhava->product_type,
            'en_details' => $chadhava->details,
            'hi_details' => $translations['description'] ?? null,
            'product_id' => $chadhava->product_id,
            'en_pooja_heading' => $chadhava->pooja_heading,
            'hi_pooja_heading' => $translations['pooja_heading'] ?? null,
            'en_chadhava_venue' => $chadhava->chadhava_venue,
            'hi_chadhava_venue' => $translations['chadhava_venue'] ?? null,
            'chadhava_week' => $chadhava->chadhava_week,
            'is_video' => $chadhava->is_video,
            'thumbnail' => $chadhava->thumbnail ? url('/storage/app/public/chadhava/thumbnail/' . $chadhava->thumbnail) : null,
            'digital_file_ready' => $chadhava->digital_file_ready,
            'en_meta_title' => $chadhava->meta_title,
            'hi_meta_title' => $translations['meta_title'] ?? null,
            'meta_description' => $chadhava->meta_description,
            'meta_image' => $chadhava->meta_image,
            'products' => $chadhavaProducts,
        ];
        // Additional processing for week days
        $chadhava_week = json_decode($chadhava->chadhava_week);
        $nextChadhavaDay = Helpers::getNextChadhavaDay($chadhava_week);

        // Check pooja_type and handle accordingly
        if ($chadhava->chadhava_type == 1) {
            $startDate = $chadhava->start_date;
            $endDate = $chadhava->end_date;
            $currentDate = time();
            $formattedDates = [];
            $ChadhavaearliestDate = '';

            if ($startDate && $endDate && $startDate <= $endDate) {
                $currentDateIter = $startDate->copy();
                while ($currentDateIter <= $endDate) {
                    $formattedDates[] = $currentDateIter->format('Y-m-d');
                    $currentDateIter->addDay();
                }

                foreach ($formattedDates as $date) {
                    if (strtotime($date) > $currentDate) {
                        $ChadhavaearliestDate = date('Y-m-d H:i:s', strtotime($date));
                        break;
                    }
                }
            }

            $chadhavaData['next_chadhava_date'] = $ChadhavaearliestDate ?: null;
            $chadhavaData['chadhava_type_text'] = ' Date Wise Chadhava';
        } else {
            // For Weekly Pooja, use the calculated next pooja day
            $chadhavaData['next_chadhava_date'] = $nextChadhavaDay instanceof \DateTime ? $nextChadhavaDay->format('Y-m-d H:i:s') : ($nextChadhavaDay ? date('Y-m-d H:i:s', strtotime($nextChadhavaDay)) : null);
            $chadhavaData['chadhava_type_text'] = 'Weekly Chadhava';
        }

        return $chadhavaData;
    }
    // VIP DATE LEAD STORE AND SNAKAL DATA STORE
    public function ChadhavaLeadStore(Request $request)
    {
        $service_id   = $request->input('service_id');
        $personPhone  = $request->input('person_phone');
        // Validate customer existence
        $customer = User::where('phone', $personPhone)->first();
        if (!$customer) {
            return response()->json(['status' => false, 'message' => 'User not found.'], 404);
        }

        $full_name = trim(($customer->f_name ?? '') . ' ' . ($customer->l_name ?? ''));
        $full_name = $full_name ?: ($customer->name ?? '');

        $chadhava = Chadhava::where('id', $service_id)->where('status', '1')->first();
        if (!$chadhava) {
            return response()->json(['status' => false, 'message' => 'Chadhava not found.'], 404);
        }
        $type='chadhava';
        $chadhavaProducts = json_decode($chadhava->product_id, true);

        if (!$chadhavaProducts || !is_array($chadhavaProducts)) {
            return response()->json(['status' => false, 'message' => 'Invalid or missing product_id.'], 400);
        }

        // Create lead
        $lead = Leads::create([
            'service_id'    => $chadhava->id,
            'type'          => $type,
            'product_id'    => json_encode($chadhavaProducts),
            'package_name'  => $chadhava->name,
            'person_phone'  => $customer->phone,
            'person_name'   => $full_name,
            'customer_id'   => $customer->id,
            'platform'      => 'app',
            'booking_date'  => $request->input('booking_date'),
        ]);

        $leadno = 'CC' . (100 + $lead->id + 1);
        $lead->update(['leadno' => $leadno]);

        return response()->json([
            'success' => true,
            'message' => 'Lead successfully created.',
            'lead'    => $lead,
        ], 200);
    }

    public function ChadhavaSankalpStore($orderId, Request $request)
    {
        $cust_details = [
            'newPhone' => $request->input('newPhone'),
            'gotra' => $request->input('gotra'),
            'members' => $request->input('members'),
            'reason' => $request->input('reason'),
        ];

        $sankalpUpdate = Chadhava_orders::where('order_id', $orderId)->update($cust_details);
        $sankalpData = Chadhava_orders::where('order_id', $orderId)->with(['customers', 'chadhava', 'leads'])->first();
        $service_name = \App\Models\Chadhava::where('id', ($sankalpData['service_id'] ?? ""))->where('chadhava_type', 0)->first();
        $UsersData = Chadhava_orders::where('type', 'chadhava')->where('order_id', $orderId)->with(['customers', 'chadhava', 'packages', 'leads'])->first();
        $serviceReview = ServiceReview::create([
            'order_id' => $orderId,
            'user_id' => $UsersData['customer_id'],
            'service_id' => $sankalpData['service_id'],
            'service_type' => 'chadhava',
            'rating' => '5',
        ]);

        // whatsapp
        $userInfo = \App\Models\User::where('id', ($UsersData['customer_id'] ?? ""))->first();
        $bookingDetails = \App\Models\Chadhava_orders::where('service_id', ($UsersData['service_id'] ?? ""))->where('type', 'chadhava')
            ->where('booking_date', ($UsersData['booking_date'] ?? ""))
            ->where('customer_id', ($UsersData['customer_id'] ?? ""))
            ->where('order_id', ($orderId ?? ""))
            ->first();

        $message_data = [
            'service_name' => $UsersData['chadhava']['name'],
            'member_names' => $UsersData['members'],
            'gotra' => $request->input('gotra'),
            'type' => 'text-with-media',
            'attachment' =>  asset('/storage/app/public/chadhava/thumbnail/' . $UsersData['chadhava']['thumbnail']),
            'booking_date' => date('d-m-Y', strtotime($UsersData['booking_date'])),
            'orderId' => $orderId,
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

        if ($sankalpData) {
            return response()->json([
                'success' => true,
                'message' => 'Chadhava Sankalp Record Successfully Inserted.',
                'sankalpData' => $sankalpData,
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update service order.',
            ], 500);
        }
    }

    public function ChadhavaSankalpUpdate($orderId, Request $request)
    {
        $cust_details = [
            'newPhone' => $request->input('newPhone'),
            'gotra' => $request->input('gotra'),
            'members' => $request->input('members'),
            'reason' => $request->input('reason'),
            'is_edited' => '1',
        ];

        $sankalpUpdate = Chadhava_orders::where('order_id', $orderId)->update($cust_details);
        $sankalpData = Chadhava_orders::where('order_id', $orderId)->with(['customers', 'chadhava', 'leads'])->first();
        $service_name = \App\Models\Chadhava::where('id', ($sankalpData['service_id'] ?? ""))->where('chadhava_type', 0)->first();
        $UsersData = Chadhava_orders::where('type', 'chadhava')->where('order_id', $orderId)->with(['customers', 'chadhava', 'packages', 'leads'])->first();
        $serviceReview = ServiceReview::create([
            'order_id' => $orderId,
            'user_id' => $sankalpData['customer_id'],
            'service_id' => $sankalpData->service_id,
            'service_type' => 'chadhava',
            'rating' => '5',
        ]);
        // whatsapp
        $userInfo = \App\Models\User::where('id', ($UsersData['customer_id'] ?? ""))->first();
        $bookingDetails = \App\Models\Chadhava_orders::where('service_id', ($UsersData['service_id'] ?? ""))->where('type', 'chadhava')
            ->where('booking_date', ($UsersData['booking_date'] ?? ""))
            ->where('customer_id', ($UsersData['customer_id'] ?? ""))
            ->where('order_id', ($orderId ?? ""))
            ->first();

        $message_data = [
            'service_name' => $UsersData['chadhava']['name'],
            'member_names' => $UsersData['members'],
            'gotra' => $request->input('gotra'),
            'type' => 'text-with-media',
            'attachment' =>  asset('/storage/app/public/chadhava/thumbnail/' . $UsersData['chadhava']['thumbnail']),
            'booking_date' => date('d-m-Y', strtotime($UsersData['booking_date'])),
            'orderId' => $orderId,
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


        if ($sankalpData) {
            return response()->json([
                'success' => true,
                'message' => 'Chadhava Sankalp Record Successfully Inserted.',
                'sankalpData' => $sankalpData,
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update service order.',
            ], 500);
        }
    }

    public function coupon_list(Request $request)
    {
        if (!Auth::guard('api')->check()) {
            return response()->json(['status' => false, 'message' => 'Unauthorized user']);
        }

        $userId = auth('api')->id();
        $couponType = $request->query('coupon_type');

        // Validate coupon type
        $validCouponTypes = ['pooja', 'vippooja', 'anushthan', 'counselling', 'offlinepooja'];
        if ($couponType && !in_array($couponType, $validCouponTypes)) {
            return response()->json(['status' => false, 'message' => 'Invalid coupon type']);
        }

        // Already used coupon codes
        $appliedServiceCoupons = Service_order::where('customer_id', $userId)
            ->pluck('coupon_code')
            ->filter()
            ->toArray();

        // Build coupon query
        $query = Coupon::where('status', 1)
            ->where('limit', '>', 0)
            ->whereDate('start_date', '<=', Carbon::today())
            ->whereDate('expire_date', '>=', Carbon::today())
            ->whereNotIn('code', $appliedServiceCoupons)
            ->where(function ($q) use ($userId) {
                $q->where('customer_id', $userId)
                    ->orWhere('customer_id', 0);
            });

        // Apply coupon_type filter if passed
        if ($couponType) {
            $query->where('coupon_type', $couponType);
        } else {
            $query->whereIn('coupon_type', $validCouponTypes);
        }

        $list = $query->get();

        return $list->isNotEmpty()
            ? response()->json(['status' => true, 'list' => $list])
            : response()->json(['status' => false, 'message' => 'No coupons found']);
    }

    public function coupon_apply(Request $request)
    {
        if (!empty(Auth::guard('api')->user()->id)) {
            $userId = Auth::guard('api')->user()->id;

            // Check if user has already used this coupon
            $appliedServiceCoupon = Service_order::where('customer_id', $userId)
                ->where('coupon_code', $request->coupon_code)
                ->exists();

            if (!$appliedServiceCoupon) {
                $coupon = Coupon::whereIn('coupon_type', ['pooja', 'vippooja', 'anushthan'])
                    ->where('status', 1)
                    ->where('limit', '>', 0)
                    ->where('start_date', '<=', date("Y-m-d"))
                    ->where('expire_date', '>=', date("Y-m-d"))
                    ->where('code', $request->coupon_code)
                    ->first();

                // Check if coupon exists and is valid for this user
                if ($coupon) {
                    if (($coupon->customer_id == $userId || $coupon->customer_id == 0)) {
                        if ($coupon['coupon_type'] == $request['service_type']) {
                            return response()->json(['status' => true, 'data' => $coupon]);
                        } else {
                            return response()->json(['status' => false, 'message' => 'Coupon is invalid']);
                        }
                    } else {
                        return response()->json(['status' => false, 'message' => 'Coupon is not valid for your account']);
                    }
                } else {
                    return response()->json(['status' => false, 'message' => 'Coupon limit exceeded or expired']);
                }
            } else {
                return response()->json(['status' => false, 'message' => 'You have already applied this coupon']);
            }
        }

        return response()->json(['status' => false, 'message' => 'An authorized user']);
    }
    // Daan Function
    public function charityStore(Request $request)
    {
        $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'charity' => 'required|array',
        ]);

        DB::beginTransaction();

        try {
            $leadId = $request->lead_id;
            $charities = $request->charity;

            // 🔹 Case 1: अगर charity empty है तो सब delete कर दो
            if (empty($charities)) {
                ProductLeads::where('leads_id', $leadId)->delete();

                Leads::where('id', $leadId)->update([
                    'status' => 1,
                    'payment_status' => 'pending',
                    'platform' => 'app',
                    'add_product_id' => json_encode([]),
                    'final_amount' => 0,
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'All charity products removed successfully.',
                    'final_amount' => 0,
                    'products' => []
                ], 200);
            }

            $incomingProductIds = collect($charities)->pluck('product_id')->toArray();

            ProductLeads::where('leads_id', $leadId)
                ->whereNotIn('product_id', $incomingProductIds)
                ->delete();

            foreach ($charities as $charity) {
                if (!isset($charity['product_id']) || !isset($charity['quantity'])) {
                    continue;
                }

                $product = Product::where('id', $charity['product_id'])
                    ->where('status', 1)
                    ->first();

                if (!$product) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid product ID: ' . $charity['product_id'],
                    ], 400);
                }

                $charityStore = ProductLeads::where('leads_id', $leadId)
                    ->where('product_id', $product->id)
                    ->first();

                if ($charityStore) {
                    $charityStore->qty = $charity['quantity'];
                    $charityStore->final_price = $charityStore->qty * $product->unit_price;
                    $charityStore->save();
                } else {
                    $charityStore = new ProductLeads();
                    $charityStore->leads_id = $leadId;
                    $charityStore->product_id = $product->id;
                    $charityStore->final_price = $product->unit_price * $charity['quantity'];
                    $charityStore->qty = $charity['quantity'];
                    $charityStore->product_name = $product->name;
                    $charityStore->product_price = $product->unit_price;
                    $charityStore->status = 1;
                    $charityStore->save();
                }
            }

            $totalAmount = ProductLeads::where('leads_id', $leadId)->sum('final_price');
            $productlist = ProductLeads::where('leads_id', $leadId)->get();

            $add_product_array = [];
            foreach ($productlist as $p) {
                $add_product_array[] = [
                    'product_id' => $p->product_id,
                    'price' => $p->product_price,
                    'qty' => $p->qty,
                ];
            }

            Leads::where('id', $leadId)->update([
                'status' => 1,
                'payment_status' => 'pending',
                'platform' => 'app',
                'add_product_id' => json_encode($add_product_array),
                'final_amount' => $totalAmount,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Charity list updated successfully.',
                'final_amount' => $totalAmount,
                'products' => $add_product_array
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ], 500);
        }
    }
    // Pooja Place
    public function poojaPlaceOrder(Request $request)
    {
        $leadsdata = Leads::find($request->leads_id);
        if (!$leadsdata) {
            return redirect()->back()->with('error', 'Lead not found.');
        }
       
        $productlist = ProductLeads::where('leads_id',  $leadsdata->id)->get();
        $add_product_array = [];
        foreach ($productlist as $product) {
            $add_product_array[] = [
                'product_id' => $product->product_id,
                'price' => $product->product_price,
                'qty' => $product->qty,
            ];
        }
        $wallet = User::select('wallet_balance')->where('id', $leadsdata->customer_id)->first();
        $productamount = ProductLeads::where('leads_id',$leadsdata->id)->sum('final_price') ?? 0;
        $couponDiscount = $request->coupon_amount;
        if ($leadsdata->type == 'pooja') {
            $amount = max(0, ($leadsdata->package_price ?? 0) + $productamount - $couponDiscount);
        } elseif ($leadsdata->type == 'vip') {
           
            $amount = max(0, ($leadsdata->package_price ?? 0) + $productamount - $couponDiscount);
        } elseif ($leadsdata->type == 'anushthan') {
       
            $amount = max(0, ($leadsdata->package_price ?? 0) + $productamount - $couponDiscount);
        } elseif ($leadsdata->type == 'counselling') {
          
           $amount = max(0, ($leadsdata->package_price ?? 0)  - $couponDiscount);
        }        
        $actualWalletBalance = $wallet['wallet_balance'] ?? 0;
        $totalAmount =  $amount  + $couponDiscount;
        if ($actualWalletBalance >= $amount) {
            // Wallet can cover full amount
           $viaWallet = $amount;
           $viaOnline = 0;
           $transaction_id = \Str::uuid();
       } elseif ($actualWalletBalance > 0) {
            // Wallet can cover partially
           $viaWallet = $actualWalletBalance;
           $viaOnline = $amount - $actualWalletBalance;
           $transaction_id = \Str::uuid();
       } else {
             // Wallet can't cover anything
           $viaWallet = 0;
           $viaOnline = $amount;
           $transaction_id = null;
       }       
        $remainingWalletBalance = $actualWalletBalance - $amount;
        if ($remainingWalletBalance < 0) {
            $remainingWalletBalance = 0;
        }
        Leads::where('id', $leadsdata->id)->update([
            'status' => 0,
            'payment_status' => 'pending',
            'platform' => 'app',
            'add_product_id' =>json_encode($add_product_array) ?? null,
            'final_amount' => $totalAmount,
            'via_wallet' => $viaWallet,
            'via_online' => $viaOnline,
            'coupon_amount' => $couponDiscount
        ]);
      
        $type = '';
        if ($leadsdata->type == 'pooja') {
            $type = 'PJ';
        } elseif ($leadsdata->type == 'vip') {
            $type = 'VPJ';
        } elseif ($leadsdata->type == 'anushthan') {
            $type = 'APJ';
        } elseif ($leadsdata->type == 'counselling') {
            $type = 'CL';
        }
        $orderId = '';
        $orderData = Service_order::select('id')->orderBy('id', 'desc')->first();
        if (!empty($orderData['id'])) {
            $orderId = $type . (100000 + $orderData['id'] + 1);
        } else {
            $orderId = $type . (100001);
        }
         // Wallet Transection Details
         if ($viaWallet > 0) {
            $wallet_transaction = new WalletTransaction();
            $wallet_transaction->user_id = $request->customer_id;
            $wallet_transaction->transaction_id = $transaction_id;

            switch ($leadsdata->type) {
                case 'counselling':
                    $wallet_transaction->reference = 'counselling order payment';
                    $wallet_transaction->transaction_type = 'counselling_order_place';
                    break;
                case 'pooja':
                    $wallet_transaction->reference = 'pooja order payment';
                    $wallet_transaction->transaction_type = 'pooja_order_place';
                    break;
                case 'vip':
                    $wallet_transaction->reference = 'vip order payment';
                    $wallet_transaction->transaction_type = 'vip_order_place';
                    break;
                case 'anushthan':
                    $wallet_transaction->reference = 'anushthan order payment';
                    $wallet_transaction->transaction_type = 'anushthan_order_place';
                    break;
            }

            $wallet_transaction->balance = $remainingWalletBalance;
            $wallet_transaction->debit = $viaWallet;
            $wallet_transaction->save();

            // Update wallet balance
            User::where('id', $leadsdata->customer_id)->update([
                'wallet_balance' => $remainingWalletBalance
            ]);
        }
        $serviceOrderAdd = new Service_order();
        $serviceOrderAdd->customer_id = $leadsdata->customer_id;
        $serviceOrderAdd->service_id = $leadsdata->service_id;
        $serviceOrderAdd->type = $leadsdata->type;
        $serviceOrderAdd->leads_id = $leadsdata->id;
        $serviceOrderAdd->coupon_amount = $couponDiscount;
        $serviceOrderAdd->coupon_code = $request->coupon_code;
        $serviceOrderAdd->payment_id = $request->payment_id;
        $serviceOrderAdd->package_id = $leadsdata->package_id ?? null;
        $serviceOrderAdd->pay_amount = $totalAmount;
        $serviceOrderAdd->wallet_amount = $viaWallet;
        $serviceOrderAdd->transection_amount = $viaOnline;
        $serviceOrderAdd->wallet_translation_id = $transaction_id;
        $serviceOrderAdd->order_id = $orderId;
        $serviceOrderAdd->package_price = $leadsdata->package_price;
        $serviceOrderAdd->booking_date = $leadsdata?->booking_date ?? null;
        $serviceOrderAdd->save();
        Leads::where('id', $leadsdata->id)->update([
            'status' => 0,
            'payment_status' => 'Complete',
            'order_id' => $orderId,
        ]);
        $productlist = ProductLeads::where('leads_id', $leadsdata->leads_id)->get();
            $add_product_array = [];
            foreach ($productlist as $product) {
                $add_product_array[] = [
                    'product_id' => $product->product_id,
                    'price' => $product->product_price,
                    'qty' => $product->qty,
                ];
            }
        
        if ($leadsdata->type !== 'counselling') {
            PoojaRecords::create([
                'customer_id'     => $serviceOrderAdd->customer_id,
                'service_id'      => $serviceOrderAdd->service_id,
                'product_id' =>   json_encode($add_product_array),
                'service_order_id'=> $serviceOrderAdd->order_id,
                'package_id'      => $serviceOrderAdd->package_id,
                'package_price'   => $serviceOrderAdd->package_price ?? 0.00,
                'amount'          => $serviceOrderAdd->pay_amount ?? 0.00,
                'coupon'          => $serviceOrderAdd->coupon_amount ?? 0.00,
                'via_wallet'      => $serviceOrderAdd->wallet_amount ?? 0.00,
                'via_online'      => $serviceOrderAdd->transection_amount ?? 0.00,
                'booking_date'    => $serviceOrderAdd->booking_date,
            ]);
        }

        if ($request->type == 'pooja') {

            // whatsapp
            $userInfo = \App\Models\User::where('id', ($leadsdata->customer_id ?? ""))->first();
            $service_name = \App\Models\Service::where('id', ($leadsdata->service_id ?? ""))->where('product_type', 'pooja')->first();
            $bookingDetails = $serviceOrderAdd;

            $message_data = [
                'service_name' => $service_name['name'],
                'type' => 'text-with-media',
                'attachment' =>  asset('/storage/app/public/pooja/thumbnail/' . $service_name->thumbnail),
                'booking_date' => date('Y-m-d', strtotime($request->booking_date)),
                'puja_venue' => $service_name['pooja_venue'],
                'orderId' => $orderId,
                'final_amount' => webCurrencyConverter((float)($request->payment_amount - ($bookingDetails->coupon_amount ?? 0))),
                'customer_id' => $request->customer_id,
            ];

            $messages =  Helpers::whatsappMessage('whatsapp', 'Pooja Confirmed', $message_data);
            // Mail Setup for Pooja Management Send to  User Email Id
            if ($userInfo && !empty($userInfo['email']) && filter_var($userInfo['email'], FILTER_VALIDATE_EMAIL)) {
                $data['type'] = 'pooja';
                $data['email'] = $userInfo['email'];
                $data['subject'] = 'Confirmation of Your Service Purchase';
                $data['htmlContent'] = \Illuminate\Support\Facades\View::make('admin-views.email.email-template.pooja-template', compact('userInfo', 'service_name', 'bookingDetails'))->render();

                Helpers::emailSendMessage($data);
                $order = Service_order::where('order_id', $orderId)->where('status', '0')->with(['customer'])->first();
                event(new OrderStatusEvent(key: '0', type: 'puja', order: $order));
            }
        } elseif ($request->type == 'vip') {

            // whatsapp
            $userInfo = \App\Models\User::where('id', ($leadsdata->customer_id ?? ""))->first();
            $service_name = \App\Models\Vippooja::where('id', ($leadsdata->service_id ?? ""))->where('is_anushthan', 0)->first();
            $bookingDetails = $serviceOrderAdd;

            $message_data = [
                'service_name' => $service_name['name'],
                'type' => 'text-with-media',
                'attachment' =>  asset('/storage/app/public/pooja/vip/thumbnail/' . $service_name->thumbnail),
                'booking_date' => date('Y-m-d', strtotime($request->booking_date)),
                'puja' => 'VIP Puja',
                'orderId' => $orderId,
                'final_amount' => webCurrencyConverter((float)($request->payment_amount - ($bookingDetails->coupon_amount ?? 0))),
                'customer_id' => $request->customer_id,
            ];

            $messages =  Helpers::whatsappMessage('vipanushthan', 'Pooja Confirmed', $message_data);
            // Mail Setup for Pooja Management Send to  User Email Id
            if ($userInfo && !empty($userInfo['email']) && filter_var($userInfo['email'], FILTER_VALIDATE_EMAIL)) {
                $data['type'] = 'pooja';
                $data['email'] = $userInfo['email'];
                $data['subject'] = 'Confirmation of Your VIP Service Purchase';
                $data['htmlContent'] = \Illuminate\Support\Facades\View::make('admin-views.email.email-template.pooja-template', compact('userInfo', 'service_name', 'bookingDetails'))->render();

                Helpers::emailSendMessage($data);
                $order = Service_order::where('order_id', $orderId)->where('status', '0')->with(['customer'])->first();
                event(new OrderStatusEvent(key: '0', type: 'puja', order: $order));
            }
        } elseif ($request->type == 'anushthan') {
            // whatsapp
            $userInfo = \App\Models\User::where('id', ($leadsdata->customer_id ?? ""))->first();
            $service_name = \App\Models\Vippooja::where('id', ($leadsdata->service_id ?? ""))->where('is_anushthan', 1)->first();
            $bookingDetails = $serviceOrderAdd;

            $message_data = [
                'service_name' => $service_name['name'],
                'type' => 'text-with-media',
                'attachment' =>  asset('/storage/app/public/pooja/vip/thumbnail/' . $service_name->thumbnail),
                'booking_date' => date('Y-m-d', strtotime($request->booking_date)),
                'puja' => 'Anushthan',
                'orderId' => $orderId,
                'final_amount' => webCurrencyConverter((float)($request->payment_amount - ($bookingDetails->coupon_amount ?? 0))),
                'customer_id' => $request->customer_id,
            ];
            $messages =  Helpers::whatsappMessage('vipanushthan', 'Pooja Confirmed', $message_data);
            // Mail Setup for Pooja Management Send to  User Email Id
            if ($userInfo && !empty($userInfo['email']) && filter_var($userInfo['email'], FILTER_VALIDATE_EMAIL)) {
                $data['type'] = 'pooja';
                $data['email'] = $userInfo['email'];
                $data['subject'] = 'Confirmation of Your Anushthan Service Purchase';
                $data['htmlContent'] = \Illuminate\Support\Facades\View::make('admin-views.email.email-template.pooja-template', compact('userInfo', 'service_name', 'bookingDetails'))->render();

                Helpers::emailSendMessage($data);
                $order = Service_order::where('order_id', $orderId)->where('status', '0')->with(['customer'])->first();
                event(new OrderStatusEvent(key: '0', type: 'puja', order: $order));
            }
        } elseif ($request->type == 'counselling') {

            $userInfo = \App\Models\User::where('id', ($leadsdata->customer_id ?? ""))->first();
            $service_name = \App\Models\Service::where('id', ($leadsdata->service_id ?? ""))->where('product_type', 'counselling')->first();
            $bookingDetails = $serviceOrderAdd;

            $message_data = [
                'service_name' => $service_name['name'],
                'type' => 'text-with-media',
                'attachment' =>  asset('/storage/app/public/pooja/thumbnail/' . $service_name->thumbnail),
                'orderId' => $orderId,
                'final_amount' => webCurrencyConverter((float)($request->payment_amount - ($bookingDetails->coupon_amount ?? 0))),
                'customer_id' => $request->customer_id,
            ];

            $messages =  Helpers::whatsappMessage('consultancy', 'Order Confirmed', $message_data);

            // Mail Setup for Pooja Management Send to  User Email Id
            if ($userInfo && !empty($userInfo['email']) && filter_var($userInfo['email'], FILTER_VALIDATE_EMAIL)) {
                $data['type'] = 'counselling';
                $data['email'] = $userInfo['email'];
                $data['subject'] = 'Confirmation of Your Counselling Service Purchase';
                $data['htmlContent'] = \Illuminate\Support\Facades\View::make('admin-views.email.email-template.pooja-template', compact('userInfo', 'service_name', 'bookingDetails'))->render();

                Helpers::emailSendMessage($data);
            }
        }

        if ($serviceOrderAdd) {
            return response()->json(['status' => true, 'order_id' => $orderId, 'message' => 'Order placed successfully'], 200);
        } else {
            return response()->json(['status' => false, 'message' => 'Unable to place order'], 400);
        }
    }
    // Chadhava Order
    public function chadhavaPlaceOrder(Request $request)
    {
        $leadsdata = Leads::find($request->leads_id);
        if (!$leadsdata) {
            return redirect()->back()->with('error', 'Lead not found.');
        }
       
        $productlist = ProductLeads::where('leads_id', $request->leads_id)->get();
        $add_product_array = [];
        foreach ($productlist as $product) {
            $add_product_array[] = [
                'product_id' => $product->product_id,
                'price' => $product->product_price,
                'qty' => $product->qty,
            ];
        }
        $wallet = User::select('wallet_balance')->where('id', $request->customer_id)->first();
        $productAmount = ProductLeads::where('leads_id', $request->leads_id)->sum('final_price') ?? 0;
        $actualWalletBalance = $wallet->wallet_balance ?? 0;

        if ($actualWalletBalance >= $productAmount) {
            $viaWallet = $productAmount;
            $viaOnline = 0;
        } else {
            $viaWallet = $actualWalletBalance;
            $viaOnline = $productAmount - $actualWalletBalance;
        }
        $totalAmount = $viaWallet + $viaOnline;
        $remainingWalletBalance = $actualWalletBalance - $viaWallet;

        Leads::where('id', $leadsdata->id)->update([
            'status' => 0,
            'payment_status' => 'pending',
            'platform' => 'app',
            'add_product_id' =>json_encode($add_product_array) ?? null,
            'final_amount' => $totalAmount,
            'via_wallet' => $viaWallet,
            'via_online' => $viaOnline
        ]);
        $orderId = '';
        $orderData = Chadhava_orders::select('id')->orderBy('id', 'desc')->first();
        if (!empty($orderData['id'])) {
            $orderId = 'CC' . (100000 + $orderData['id'] + 1);
        } else {
            $orderId = 'CC' . (100001);
        }

        $wallet_transaction = new WalletTransaction();
        $wallet_transaction->user_id = $request->customer_id;
        $wallet_transaction->transaction_id = \Str::uuid();
        $wallet_transaction->reference = 'chadhava order payment';
        $wallet_transaction->transaction_type = 'chadhava_order_place';
        $wallet_transaction->balance = $remainingWalletBalance;
        $wallet_transaction->debit = $viaWallet;
        $wallet_transaction->save();
        User::where('id', $request->customer_id)->update(['wallet_balance' => $remainingWalletBalance]);

        $serviceOrderAdd = new Chadhava_orders;
        $serviceOrderAdd->customer_id = $request->customer_id;
        $serviceOrderAdd->service_id = $leadsdata->service_id;
        $serviceOrderAdd->type = $leadsdata->type;
        $serviceOrderAdd->leads_id = $leadsdata->id;
        $serviceOrderAdd->booking_date = $leadsdata->booking_date;
        $serviceOrderAdd->order_id = $orderId;
        $serviceOrderAdd->payment_id = $request->payment_id;
        $serviceOrderAdd->pay_amount = $totalAmount;
        $serviceOrderAdd->wallet_amount = $viaWallet;
        $serviceOrderAdd->transection_amount = $viaOnline;
        $serviceOrderAdd->wallet_translation_id = $wallet_transaction->transaction_id;
        $serviceOrderAdd->save();

        Leads::where('id', $leadsdata->id)->update([
            'status' => 0,
            'payment_status' => 'Complete',
            'order_id' => $orderId,
        ]);

        // whatsapp
        $userInfo = \App\Models\User::where('id', ($request->customer_id ?? ""))->first();
        $service_name = \App\Models\Chadhava::where('id', ($request->service_id ?? ""))->where('chadhava_type', 0)->first();
        $bookingDetails = $serviceOrderAdd;
        $message_data = [
            'service_name' => $service_name['name'],
            'type' => 'text-with-media',
            'attachment' =>  asset('/storage/app/public/chadhava/thumbnail/' . $service_name->thumbnail),
            'booking_date' => date('Y-m-d', strtotime($request->booking_date)),
            'chadhava_venue' => $service_name['chadhava_venue'],
            'orderId' => $orderId,
            'final_amount' => webCurrencyConverter(amount: (float)$request->payment_amount ?? 0),
            'customer_id' => $request->customer_id,
        ];
        $messages =  Helpers::whatsappMessage('chadhava', 'Chadhava Confirmed', $message_data);
        // Mail Setup for Pooja Management Send to  User Email Id
        if ($userInfo && !empty($userInfo['email']) && filter_var($userInfo['email'], FILTER_VALIDATE_EMAIL)) {
            $data['type'] = 'chadhava';
            $data['email'] = $userInfo['email'];
            $data['subject'] = 'Confirmation of Your Chadhava Service Purchase';
            $data['htmlContent'] = \Illuminate\Support\Facades\View::make('admin-views.email.email-template.pooja-template', compact('userInfo', 'service_name', 'bookingDetails'))->render();

            Helpers::emailSendMessage($data);
            $order = Chadhava_orders::where('order_id', $orderId)->where('status', '0')->with(['customer'])->first();
            event(new OrderStatusEvent(key: '0', type: 'puja', order: $order));
        }

        if ($serviceOrderAdd) {
            return response()->json(['status' => true, 'order_id' => $orderId, 'message' => 'Order placed successfully'], 200);
        } else {
            return response()->json(['status' => false, 'message' => 'Unable to place order'], 400);
        }
    }

    public function CounsellingSankalpStore(Request $request)
    {
        $orderId = $request->order_id;
        $details = [
            'order_id' => $request->input('order_id'),
            'name' => $request->input('name'),
            'gender' => $request->input('gender'),
            'mobile' => $request->input('mobile'),
            'dob' => $request->input('dob'),
            'time' => $request->input('time'),
            'country' => $request->input('country'),
            'city' => $request->input('city'),
        ];

        $sankalpData = CounsellingUser::create($details);

        $serviceData = \App\Models\Service_order::where('type', 'counselling')->where('order_id', ($orderId ?? ""))->first();
        $dob = \App\Models\CounsellingUser::where('order_id', ($orderId ?? ""))->first();

        $serviceReview = ServiceReview::create([
            'order_id' => $orderId,
            'user_id' => $serviceData['customer_id'],
            'service_id' => $serviceData['service_id'],
            'service_type' => 'counselling',
            'rating' => '5',
        ]);

        $userInfo = \App\Models\User::where('id', ($serviceData['customer_id'] ?? ""))->first();
        $service_name = \App\Models\Service::where('id', ($serviceData['service_id'] ?? ""))->where('product_type', 'counselling')->first();
        $bookingDetails = \App\Models\Service_order::where('service_id', ($serviceData['service_id'] ?? ""))->where('type', 'counselling')
            ->where('customer_id', ($serviceData['customer_id'] ?? ""))
            ->where('order_id', ($orderId ?? ""))
            ->first();

        $message_data = [
            'service_name' => $service_name['name'],
            'name' => $dob->name,
            'gender' => $dob->gender,
            'city' => $dob->city,
            'country' => $dob->country,
            'time' => $dob->time,
            'dob' => $dob->dob,
            'type' => 'text-with-media',
            'attachment' =>  asset('/storage/app/public/pooja/thumbnail/' . $service_name->thumbnail),
            'customer_id' => ($serviceData['customer_id'] ?? ""),
        ];
        $messages =  Helpers::whatsappMessage('consultancy', 'message', $message_data);

        // Mail Setup for Pooja Management Send to  User Email Id
        if ($userInfo && !empty($userInfo['email']) && filter_var($userInfo['email'], FILTER_VALIDATE_EMAIL)) {
            $data['type'] = 'pooja';
            $data['email'] = $userInfo['email'];
            $data['subject'] = 'Information given by you for counselling';
            $data['htmlContent'] = \Illuminate\Support\Facades\View::make('admin-views.email.email-template.pooja-sankalp-template', compact('userInfo', 'service_name', 'bookingDetails', 'dob', 'request', 'serviceData'))->render();

            Helpers::emailSendMessage($data);
        }
        return response()->json([
            'success' => true,
            'message' => 'counselling sankalp record successfully created.',
            'sankalpData' => $sankalpData,
        ], 201);
    }

    public function CounsellingSankalpUpdate($orderId, Request $request)
    {

        $details = [
            'name' => $request->input('name'),
            'gender' => $request->input('gender'),
            'mobile' => $request->input('mobile'),
            'dob' => $request->input('dob'),
            'time' => $request->input('time'),
            'country' => $request->input('country'),
            'city' => $request->input('city'),
            'is_update' => '1',
        ];

        $sankalpData = CounsellingUser::where('order_id', $orderId)->update($details);

        return response()->json([
            'success' => true,
            'message' => 'counselling sankalp record successfully updated.',
            'sankalpData' => $sankalpData,
        ], 201);
    }



    public function CounsellingLeadStore(Request $request)
    {
        $service_id   = $request->input('service_id');
        $personPhone  = $request->input('person_phone');

        $customer = User::where('phone', $personPhone)->first();
        if (!$customer) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid user: Phone number not registered.'
            ], 404);
        }

        $full_name = $customer ? trim(($customer->f_name ?? '') . ' ' . ($customer->l_name ?? '')) : '';
        $full_name = $full_name ?: ($customer->name ?? '');
        $counselling = Service::where('id', $service_id)->where('product_type','counselling')->where('status','1')->first();
        if (!$counselling) {
            return response()->json(['status' => false, 'message' => 'counselling not found.'], 404);
        }
        $lead = new Leads();
        $lead->service_id     = $counselling->id;
        $lead->type           = $counselling->product_type;
        $lead->package_name   = $counselling->name;
        $lead->package_price  = $counselling->counselling_selling_price;
        $lead->person_phone   = $customer->phone;
        $lead->person_name    = $full_name;  
        $lead->platform       = 'app';
        $lead->customer_id    = $customer->id;
        $lead->save();
       
        $insertedRowId = $lead->id;
        if (!empty($insertedRowId)) {
            $leadno = 'CL' . (100 + $insertedRowId + 1);
        } else {
            $leadno = 'CL101';
        }
        $lead->leadno = $leadno;
        $lead->save();
        return response()->json([
            'success' => true,
            'message' => 'Lead successfully created.',
            'lead' => $lead,
        ], 200);
    }

    public function getWalletBalance($customer_id)
    {
        $userWallet = User::select('wallet_balance')
            ->where('id', $customer_id)
            ->first();

        if (!$userWallet) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'wallet_balance' => $userWallet->wallet_balance,
        ], 200);
    }

    // Pooja VIP,Anushthan,Counselling Order Track
    public function getServiceOrderTrack($orderid, Request $request)
    {

        $serviceOrder = Service_order::where('order_id', $orderid)->first();

        $certificate = !empty($serviceOrder['pooja_certificate'])
            ? asset('public/' . $serviceOrder['pooja_certificate'])
            : '';

        if (!$serviceOrder) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid order ID or order not found.',
            ], 404);
        }

        $customer = User::where('phone', $serviceOrder['leads']['person_phone'] ?? null)
            ->select('id', 'name', 'email', 'phone')
            ->first();

        $serviceOrder['customer'] = $customer ?: null;


        $status = '';
        $order_status = '';
        $prashad_status = '';

        switch ($serviceOrder->status) {
            case 0:
                $status = 'Pending';
                break;
            case 1:
                $status = 'completed';
                break;
            case 2:
                $status = 'Cancel';
                break;
            case 6:
                $status = 'Rejected';
                break;
            default:
                $status = 'Unknown';
        }

        switch ($serviceOrder->order_status) {
            case 0:
                $order_status = 'Pending';
                break;
            case 1:
                $order_status = 'completed';
                break;
            case 3:
                $order_status = 'Scheduled';
                break;
            case 4:
                $order_status = 'Live';
                break;
            case 5:
                $order_status = 'Shared';
                break;
            case 6:
                $order_status = 'Rejected';
                break;
            default:
                $order_status = 'Unknown';
        }

        switch ($serviceOrder->prashad_status) {
            case 0:
                $prashad_status = 'Pending';
                break;
            case 1:
                $prashad_status = 'confirmed';
                break;
            case 2:
                $prashad_status = 'packaging';
                break;
            case 3:
                $prashad_status = 'out_for_delivery';
                break;
            case 4:
                $prashad_status = 'delivered';
                break;
            case 5:
                $prashad_status = 'failed_to_Deliver';
                break;
            case 6:
                $prashad_status = 'canceled';
                break;
            default:
                $prashad_status = 'Completed';
        }

        $serviceOrder->status = $status;
        $serviceOrder->order_status = $order_status;
        $serviceOrder->prashad_status = $prashad_status;

        $response = [
            'order_id' => $serviceOrder->order_id,
            'status' => $serviceOrder->status,
            'schedule_time' => $serviceOrder->schedule_time,
            'schedule_created' => $serviceOrder->schedule_created,
            'live_stream' => $serviceOrder->live_stream,
            'live_created_stream' => $serviceOrder->live_created_stream,
            'pooja_video' => $serviceOrder->pooja_video,
            'video_created_sharing' => $serviceOrder->video_created_sharing,
            'reject_reason' => $serviceOrder->reject_reason,
            'pooja_certificate' => ($certificate  ?? ''),
            'order_completed' => $serviceOrder->order_completed,
            'order_canceled' => $serviceOrder->order_canceled,
            'order_canceled_reason' => $serviceOrder->order_canceled_reason,
            'order_status' => $serviceOrder->order_status,
            'booking_date' => $serviceOrder->booking_date,
            'created_at' => date('Y-m-d H:i:s',  strtotime($serviceOrder->created_at)),
            'counselling_report' => $serviceOrder->counselling_report ? url('/storage/app/public/consultation-order-report/' . $serviceOrder->counselling_report) : null,
            'prashad_status' => $serviceOrder['is_prashad'] == 1 ? $serviceOrder->prashad_status : 'prashad_status',
        ];
        return response()->json([
            'success' => true,
            'message' => 'Order retrieved successfully.',
            'order' => $response,
        ]);
    }

    // Chdhava Order Track
    public function getChadhavaOrderTrack($orderid, Request $request)
    {

        $serviceOrder = Chadhava_orders::where('order_id', $orderid)->first();

        $certificate = !empty($serviceOrder['pooja_certificate'])
            ? asset('public/' . $serviceOrder['pooja_certificate'])
            : '';
        if (!$serviceOrder) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid order ID or order not found.',
            ], 404);
        }

        $customer = User::where('phone', $serviceOrder['leads']['person_phone'] ?? null)
            ->select('id', 'name', 'email', 'phone')
            ->first();

        $serviceOrder['customer'] = $customer ?: null;


        $status = '';
        $order_status = '';

        switch ($serviceOrder->status) {
            case 0:
                $status = 'Pending';
                break;
            case 1:
                $status = 'Complete';
                break;
            case 2:
                $status = 'Cancel';
                break;
            case 6:
                $status = 'Rejected';
                break;
            default:
                $status = 'Unknown';
        }

        switch ($serviceOrder->order_status) {
            case 0:
                $order_status = 'Pending';
                break;
            case 1:
                $order_status = 'Complete';
                break;
            case 2:
                $order_status = 'Cancel';
                break;
            case 3:
                $order_status = 'Scheduled';
                break;
            case 4:
                $order_status = 'Live';
                break;
            case 5:
                $order_status = 'Shared';
                break;
            case 6:
                $order_status = 'Rejected';
                break;
            default:
                $order_status = 'Unknown status';
        }

        $serviceOrder->status = $status;
        $serviceOrder->order_status = $order_status;



        $response = [
            'order_id' => $serviceOrder->order_id,
            'status' => $serviceOrder->status,
            'schedule_time' => $serviceOrder->schedule_time,
            'schedule_created' => $serviceOrder->schedule_created,
            'live_stream' => $serviceOrder->live_stream,
            'live_created_stream' => $serviceOrder->live_created_stream,
            'pooja_video' => $serviceOrder->pooja_video,
            'video_created_sharing' => $serviceOrder->video_created_sharing,
            'reject_reason' => $serviceOrder->reject_reason,
            'pooja_certificate' => ($certificate  ?? ''),
            'order_completed' => $serviceOrder->order_completed,
            'order_canceled' => $serviceOrder->order_canceled,
            'order_canceled_reason' => $serviceOrder->order_canceled_reason,
            'order_status' => $serviceOrder->order_status,
            'booking_date' => $serviceOrder->booking_date,
            'created_at' => date('Y-m-d H:i:s',  strtotime($serviceOrder->created_at)),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Order retrieved successfully.',
            'order' => $response,
        ]);
    }

    public function getPrashadOrderTrack($orderid, Request $request)
    {

        $prashadOrder = Prashad_deliverys::where('order_id', $orderid)->first();

        if (!$prashadOrder) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid order ID or order not found.',
            ], 404);
        }

        $response = [
            'seller_id' => $prashadOrder->seller_id,
            'type' => $prashadOrder->type,
            'order_id' => $prashadOrder->order_id,
            'service_id' => $prashadOrder->service_id,
            'user_id' => $prashadOrder->user_id,
            'product_id' => $prashadOrder->product_id,
            'booking_date' => $prashadOrder->booking_date,
            'order_completed' => $prashadOrder->order_completed,
            'payment_type' => $prashadOrder->payment_type,
            'manifest_id' => $prashadOrder->manifest_id,
            'shippingurl' => $prashadOrder->shippingurl,
            'awb' => $prashadOrder->awb,
            'carrier_id' => $prashadOrder->carrier_id,
            'carrier_name' => $prashadOrder->carrier_name,
            'message' => $prashadOrder->message,
            'warehouse_id' => $prashadOrder->warehouse_id,
            'delivery_charge' => $prashadOrder->delivery_charge,
            'delivery_partner' => $prashadOrder->delivery_partner,
            'pooja_status' => $prashadOrder->pooja_status,
            'order_status' => $prashadOrder->order_status,
            'shipment_status_scan' => $prashadOrder->shipment_status_scan,
            'added_by' => $prashadOrder->added_by,
            'status' => $prashadOrder->status,
            'created_at' => date('Y-m-d H:i:s',  strtotime($prashadOrder->created_at)),
        ];
        return response()->json([
            'success' => true,
            'message' => 'Order retrieved successfully.',
            'order' => $response,
        ]);
    }

    public function servicereview($slug)
    {
        $epooja = Service::where('slug', $slug)->where('product_type', 'pooja')->first();
        if (!$epooja) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found'
            ], 404);
        }
        $reviews = ServiceReview::where('service_id', $epooja->id)->with(['userData'])->orderBy('id', 'desc')->get();
        foreach ($reviews as $review) {
            if ($review->userData && isset($review->userData->image)) {
                $review->userData->image = asset('storage/app/public/profile/' . $review->userData->image);
            }
        }
        $reviewCounts = [
            'excellent' => $reviews->where('rating', 5)->count(),
            'good' => $reviews->where('rating', 4)->count(),
            'average' => $reviews->where('rating', 3)->count(),
            'below_average' => $reviews->where('rating', 2)->count(),
            'poor' => $reviews->where('rating', 1)->count(),
            'averageStar' => $reviews->avg('rating'),
            'list' => $reviews,
        ];
        $totalReviews = $reviews->sum('reviews_count');
        $response = [
            'success' => true,
            'review_summary' => $reviewCounts,
            'total_reviews' => $totalReviews,
        ];
        return response()->json($response, 200);
    }

    public function vippoojareview($slug)
    {
        $epooja = Vippooja::where('slug', $slug)->where('is_anushthan', 0)->first();
        if (!$epooja) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found'
            ], 404);
        }
        $reviews = ServiceReview::where('service_id', $epooja->id)->with(['userData'])->orderBy('id', 'desc')->get();
        foreach ($reviews as $review) {
            if ($review->userData && isset($review->userData->image)) {
                $review->userData->image = asset('storage/app/public/profile/' . $review->userData->image);
            }
        }
        $reviewCounts = [
            'excellent' => $reviews->where('rating', 5)->count(),
            'good' => $reviews->where('rating', 4)->count(),
            'average' => $reviews->where('rating', 3)->count(),
            'below_average' => $reviews->where('rating', 2)->count(),
            'poor' => $reviews->where('rating', 1)->count(),
            'averageStar' => $reviews->avg('rating'),
            'list' => $reviews,
        ];
        $totalReviews = $reviews->sum('reviews_count');
        $response = [
            'success' => true,
            'review_summary' => $reviewCounts,
            'total_reviews' => $totalReviews,
        ];
        return response()->json($response, 200);
    }

    public function anushthanreview($slug)
    {
        $epooja = Vippooja::where('slug', $slug)->where('is_anushthan', 1)->first();
        if (!$epooja) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found'
            ], 404);
        }
        $reviews = ServiceReview::where('service_id', $epooja->id)->with(['userData'])->orderBy('id', 'desc')->get();
        foreach ($reviews as $review) {
            if ($review->userData && isset($review->userData->image)) {
                $review->userData->image = asset('storage/app/public/profile/' . $review->userData->image);
            }
        }
        $reviewCounts = [
            'excellent' => $reviews->where('rating', 5)->count(),
            'good' => $reviews->where('rating', 4)->count(),
            'average' => $reviews->where('rating', 3)->count(),
            'below_average' => $reviews->where('rating', 2)->count(),
            'poor' => $reviews->where('rating', 1)->count(),
            'averageStar' => $reviews->avg('rating'),
            'list' => $reviews,
        ];
        $totalReviews = $reviews->sum('reviews_count');
        $response = [
            'success' => true,
            'review_summary' => $reviewCounts,
            'total_reviews' => $totalReviews,
        ];
        return response()->json($response, 200);
    }

    public function chadhavareview($slug)
    {
        $epooja = Chadhava::where('slug', $slug)->first();
        if (!$epooja) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found'
            ], 404);
        }
        $reviews = ServiceReview::where('service_id', $epooja->id)->with(['userData'])->orderBy('id', 'desc')->get();
        foreach ($reviews as $review) {
            if ($review->userData && isset($review->userData->image)) {
                $review->userData->image = asset('storage/app/public/profile/' . $review->userData->image);
            }
        }
        $reviewCounts = [
            'excellent' => $reviews->where('rating', 5)->count(),
            'good' => $reviews->where('rating', 4)->count(),
            'average' => $reviews->where('rating', 3)->count(),
            'below_average' => $reviews->where('rating', 2)->count(),
            'poor' => $reviews->where('rating', 1)->count(),
            'averageStar' => $reviews->avg('rating'),
            'list' => $reviews,
        ];
        $totalReviews = $reviews->sum('reviews_count');
        $response = [
            'success' => true,
            'review_summary' => $reviewCounts,
            'total_reviews' => $totalReviews,
        ];
        return response()->json($response, 200);
    }

    public function counsellingreview($slug)
    {
        $epooja = Service::where('slug', $slug)->where('product_type', 'counselling')->first();
        if (!$epooja) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found'
            ], 404);
        }
        $reviews = ServiceReview::where('service_id', $epooja->id)->with(['userData'])->orderBy('id', 'desc')->get();
        foreach ($reviews as $review) {
            if ($review->userData && isset($review->userData->image)) {
                $review->userData->image = asset('storage/app/public/profile/' . $review->userData->image);
            }
        }
        $reviewCounts = [
            'excellent' => $reviews->where('rating', 5)->count(),
            'good' => $reviews->where('rating', 4)->count(),
            'average' => $reviews->where('rating', 3)->count(),
            'below_average' => $reviews->where('rating', 2)->count(),
            'poor' => $reviews->where('rating', 1)->count(),
            'averageStar' => $reviews->avg('rating'),
            'list' => $reviews,
        ];
        $totalReviews = $reviews->sum('reviews_count');
        $response = [
            'success' => true,
            'review_summary' => $reviewCounts,
            'total_reviews' => $totalReviews,
        ];
        return response()->json($response, 200);
    }

    // invoice 
    public function counselling_invoice($id)
    {
        $user = Auth::guard('api')->user();
        if ($user) {
            $companyPhone = getWebConfig(name: 'company_phone');
            $companyEmail = getWebConfig(name: 'company_email');
            $companyName = getWebConfig(name: 'company_name');
            $companyWebLogo = getWebConfig(name: 'company_web_logo');
            $details = Service_order::where('id', $id)->with('customers')->with('services')->with('payments')->with('counselling_user')->first();
            $mpdf_view = \View::make(VIEW_FILE_NAMES['consultation_order_invoice_service'], compact('details', 'companyPhone', 'companyEmail', 'companyName', 'companyWebLogo'));
            Helpers::gen_mpdf($mpdf_view, 'order_invoice_service', $details['order_id']);
            if ($mpdf_view) {
                return response()->json(["status" => true, "message" => "Invoice generated successfully."], 200);
            }
            return response()->json(["status" => false, "message" => "An error occured."], 400);
        }
        return response()->json(['status' => false, 'message' => 'Unauthorized user']);
    }

    public function offlinepooja_invoice($id)
    {
        // $user = Auth::guard('api')->user();
        // if ($user) {
        $companyPhone = getWebConfig(name: 'company_phone');
        $companyEmail = getWebConfig(name: 'company_email');
        $companyName = getWebConfig(name: 'company_name');
        $companyWebLogo = getWebConfig(name: 'company_web_logo');
        $details = OfflinePoojaOrder::where('id', $id)->with('customers')->with('offlinePooja')->with('leads')->with('package')->with('payments')->first();
        $mpdf_view = \View::make(VIEW_FILE_NAMES['order_invoice_offlinepooja'], compact('details', 'companyPhone', 'companyEmail', 'companyName', 'companyWebLogo'));
        Helpers::gen_mpdf($mpdf_view, 'order_invoice_offlinepooja', $details['order_id']);
        if ($mpdf_view) {
            return response()->json(["status" => true, "message" => "Invoice generated successfully."], 200);
        }
        return response()->json(["status" => false, "message" => "An error occured."], 400);
        // }
        // return response()->json(['status' => false, 'message' => 'Unauthorized user']);
    }

    public function pooja_invoice($id)
    {
        // $user = Auth::guard('api')->user();
        // if ($user) {
        $companyPhone = getWebConfig(name: 'company_phone');
        $companyEmail = getWebConfig(name: 'company_email');
        $companyName = getWebConfig(name: 'company_name');
        $companyWebLogo = getWebConfig(name: 'company_web_logo');
        $details = Service_order::where('order_id', $id)->with('customers')->with('services')->with('leads')->with('packages')->with('payments')->with('product_leads.productsData')->first();
        $mpdf_view = \View::make(VIEW_FILE_NAMES['order_invoice_service'], compact('details', 'companyPhone', 'companyEmail', 'companyName', 'companyWebLogo'));
        Helpers::gen_mpdf($mpdf_view, 'order_invoice_service', $details['order_id']);
        if ($mpdf_view) {
            return response()->json(["status" => true, "message" => "Invoice generated successfully."], 200);
        }
        return response()->json(["status" => false, "message" => "An error occured."], 400);
        // }
        // return response()->json(['status' => false, 'message' => 'Unauthorized user']);
    }

    public function vip_invoice($id)
    {
        // $user = Auth::guard('api')->user();
        // if ($user) {
        $companyPhone = getWebConfig(name: 'company_phone');
        $companyEmail = getWebConfig(name: 'company_email');
        $companyName = getWebConfig(name: 'company_name');
        $companyWebLogo = getWebConfig(name: 'company_web_logo');
        $details = Service_order::where('id', $id)->with('customers')->with('vippoojas')->with('leads')->with('packages')->with('payments')->with('product_leads.productsData')->first();
        $mpdf_view = \View::make(VIEW_FILE_NAMES['order_invoice_vip'], compact('details', 'companyPhone', 'companyEmail', 'companyName', 'companyWebLogo'));
        Helpers::gen_mpdf($mpdf_view, 'order_invoice_vip', $details['order_id']);
        if ($mpdf_view) {
            return response()->json(["status" => true, "message" => "Invoice generated successfully."], 200);
        }
        return response()->json(["status" => false, "message" => "An error occured."], 400);
        // }
        // return response()->json(['status' => false, 'message' => 'Unauthorized user']);
    }

    public function anushthan_invoice($id)
    {
        // $user = Auth::guard('api')->user();
        // if ($user) {
        $companyPhone = getWebConfig(name: 'company_phone');
        $companyEmail = getWebConfig(name: 'company_email');
        $companyName = getWebConfig(name: 'company_name');
        $companyWebLogo = getWebConfig(name: 'company_web_logo');
        $details = Service_order::where('id', $id)->with('customers')->with('vippoojas')->with('leads')->with('packages')->with('payments')->with('product_leads.productsData')->first();
        $mpdf_view = \View::make(VIEW_FILE_NAMES['order_invoice_anushthan'], compact('details', 'companyPhone', 'companyEmail', 'companyName', 'companyWebLogo'));
        Helpers::gen_mpdf($mpdf_view, 'order_invoice_vip', $details['order_id']);
        if ($mpdf_view) {
            return response()->json(["status" => true, "message" => "Invoice generated successfully."], 200);
        }
        return response()->json(["status" => false, "message" => "An error occured."], 400);
        // }
        // return response()->json(['status' => false, 'message' => 'Unauthorized user']);
    }

    public function chadhava_invoice($id)
    {
        // $user = Auth::guard('api')->user();
        // if ($user) {
        $companyPhone = getWebConfig(name: 'company_phone');
        $companyEmail = getWebConfig(name: 'company_email');
        $companyName = getWebConfig(name: 'company_name');
        $companyWebLogo = getWebConfig(name: 'company_web_logo');
        $details = Chadhava_orders::where('id', $id)->with('customers')->with('chadhava')->with('leads')->with('packages')->with('payments')->with('product_leads.productsData')->first();
        $mpdf_view = \View::make(VIEW_FILE_NAMES['order_invoice_chadhava'], compact('details', 'companyPhone', 'companyEmail', 'companyName', 'companyWebLogo'));
        Helpers::gen_mpdf($mpdf_view, 'order_invoice_chadhava', $details['order_id']);
        if ($mpdf_view) {
            return response()->json(["status" => true, "message" => "Invoice generated successfully."], 200);
        }
        return response()->json(["status" => false, "message" => "An error occured."], 400);
        // }
        // return response()->json(['status' => false, 'message' => 'Unauthorized user']);
    }


    // Chadhava Serach api for web
    public function ChadahvaSearchName(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ], [
            'name.required' => 'Chadhava name is required!',
        ]);

        $sellers = Chadhava::where(function ($q) use ($request) {
            $q->orWhere('name', 'like', "%{$request['name']}%");
            $q->orWhere('chadhava_venue', 'like', "%{$request['name']}%");
            $q->orWhere('short_details', 'like', "%{$request['name']}%");
            $q->orWhere('pooja_heading', 'like', "%{$request['name']}%")
                ->orWhereHas('translations', function ($query) use ($request) {
                    $query->whereIn('key', ['name', 'chadhava_venue', 'short_details', 'pooja_heading'])
                        ->where('value', 'like', "%{$request['name']}%")->where('translationable_type','App\Models\Chadhava ');
                });
        })->where('status', 1)->get();
        if ($sellers) {
            $recodes = '';
            foreach ($sellers as $product) {
                $gethindi = $product ? $product->translations()->pluck('value', 'key')->toArray() : [];
                $recodes .= '<li class="list-group-item px-0 overflow-hidden">
                                <a href="' . route('chadhava.details', $product['slug']) . '" 
                                    class="search-result-product btn p-0 m-0 search-result-product-button align-items-baseline text-start d-flex justify-content-between">
                                        <span><i class="czi-search"></i></span>
                                        <div class="text-truncate flex-grow-1 px-2">' . e($product['name']) . '</div>
                                        <span class="px-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                                class="bi bi-arrow-up-left" viewBox="0 0 16 16">
                                                <path fill-rule="evenodd"
                                                    d="M2 2.5a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1H3.707l10.147 10.146a.5.5 0 0 1-.708.708L3 3.707V8.5a.5.5 0 0 1-1 0z"/>
                                            </svg>
                                        </span>
                                    </a>
                                
                            </li>';
            }
        } 
        if (!empty($sellers) && count($sellers) > 0) {
            return response()->json(['status' => 1, 'count' => count($sellers), 'data' => $recodes], 200);
        } else {
            return response()->json(['status' => 0, 'count' => 0, 'data' => (($request->role == 'web') ? '' : [])], 400);
        }
    }

    public function liveStreamData($key) {
        $liveKey = $key;
        $orders = Service_order::where('live_stream', $liveKey)->orWhere('live_stream', 'https://stream.mahakal.com/live-stream/'.$liveKey)
        ->get();
        $allData = collect();
        $isComplete = false;
        foreach ($orders as $order) {
            $serviceId   = $order->service_id;
            $bookingDate = $order->booking_date;
            $pujaTime = $order->schedule_created;
            $isComplete = $order->is_completed == 1 ? true : false;

            if ($order->type === 'pooja') {
                $service = Service::where('id', $serviceId)->where('status', 1)->where('product_type', 'pooja')->first();
                if ($service) {
                    $allData->push([
                        'liveKey'             => $liveKey,
                        'is_complete'         => $order->is_completed == 1,
                        'service_name'        => $service->name,
                        'pooja_venue'         => $service->pooja_venue,
                        'booking_date'        => $bookingDate,
                        'puja_time'        => $pujaTime,
                    ]);
                }

            } elseif (in_array($order->type, ['vip', 'anushthan'])) {
                $service = Vippooja::where('id', $serviceId)
                            ->where('status', 1)
                            ->first();

                if ($service) {
                    $allData->push([
                        'liveKey'             => $liveKey,
                        'is_complete'         => $order->is_completed == 1,
                        'service_name'        => $service->name,
                        'pooja_venue'         => null, // VIP/Anushthan don't have pooja_venue
                        'booking_date'        => $bookingDate,
                        'puja_time'        => $pujaTime,
                    ]);
                }
            }
        }

        return response()->json([
            'liveKey' => $liveKey,
            'allData' => $allData,
            'iscomplete' => $isComplete
        ]);
    }
    //
    public function pujadevotee()
    {

        $users = range(0, 13);

        shuffle($users);

        $selectedUsers = array_slice($users, 0, 5);

        $images = array_map(function ($user) {
            return theme_asset(path: 'public/assets/user_list/user' . $user . '.jpg');
        }, $selectedUsers);

        return response()->json([
            'status' => 200,
            'message' => 'Puja devotee images',
            'images' => $images
        ]);
    }

}