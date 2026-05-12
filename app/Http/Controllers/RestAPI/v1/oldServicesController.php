<?php

namespace App\Http\Controllers\RestAPI\v1;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Cities;
use App\Models\Category;
use App\Models\Chadhava;
use App\Models\Astrologer\Astrologer;
use App\Models\Package;
use App\Models\Product;
use App\Models\Chadhava_orders;
use App\Models\Leads;
use App\Models\Order;
use App\Models\User;
use App\Models\Service_order;
use App\Models\Vippooja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Utils\Helpers;
use Illuminate\Support\Facades\DB;


class ServicesController extends Controller
{

    // show pooja category API
    // public function getpooja_category($id)
    // {
    //     // Retrieve categories with parent_id 33
    //     $get_pooja_category = Category::where('parent_id', 33)->get();

    //     // Create the "All" category
    //     $all_category = [
    //         "id" => 0,
    //         "name" => "All",
    //         "slug" => "all",
    //         "icon" => "def.png",
    //         "parent_id" => 33,
    //         "position" => 1,
    //         "created_at" => "2024-07-03T03:23:10.000000Z",
    //         "updated_at" => "2024-07-24T06:51:05.000000Z",
    //         "home_status" => 0,
    //         "priority" => 0,
    //         "translations" => []
    //     ];

    //     // Add the "All" category to the beginning of the collection
    //     $get_pooja_category->prepend((object) $all_category);

    //     // Return the response
    //     return response()->json([
    //         'status' => 200,
    //         'get_pooja_category' => $get_pooja_category
    //     ]);
    // }

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

    // All Pooja Show the API
    public function pooja() {
    $services = Service::where('status', 1)
        ->where('category_id', 33)
        ->where('product_type', 'pooja')
        ->with(['categories', 'pandit', 'product'])
        ->get();

    if ($services->isNotEmpty()) {
        $services = $services->transform(function ($service) {
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
                    $ProductModel = Product::find($productId);
                    if ($ProductModel) {
                        $productTranslations = $ProductModel->translations()->pluck('value', 'key')->toArray();
                        $serviceProducts[] = [
                            'product_id' => $productId,
                            'en_name' => $ProductModel->name,
                            'hi_name' => $productTranslations['name'] ?? null,
                            'price' => $ProductModel->unit_price,
                            'thumbnail' => $ProductModel->thumbnail ? url('/storage/app/public/product/thumbnail/' . $ProductModel->thumbnail) : null,
                                'images'=> $ProductModel->images ? url('/storage/app/public/product/' . $ProductModel->images) : null,
                        ];
                    }
                }
            }
            // Handle venues
            $serviceVenues = null;
            if ($service->pooja_venue) {
                $serviceVenues = json_decode($service->pooja_venue, true);
                if (is_array($serviceVenues)) {
                    $serviceVenues = implode(', ', $serviceVenues);
                }
            }

            // Process translations for the service
            $translations = $service->translations()->pluck('value', 'key')->toArray();

            // Additional processing for week days
            $weekDays = json_decode($service->week_days, true);
            $weekDaysText = is_array($weekDays) ? implode(', ', $weekDays) : null;

            $poojaTime = date('H:i:s', strtotime($service->pooja_time));
            $nextPoojaDay = Helpers::getNextPoojaDay($weekDays, $poojaTime);

            // Handle pooja_type
            if ($service->pooja_type == 1) { // Special Pooja
                $earliestDate = null;
                if (!empty($service->schedule)) {
                    $event_date = json_decode($service->schedule, true);
                    usort($event_date, function($a, $b) {
                        return strtotime($a['schedule']) - strtotime($b['schedule']);
                    });
                    foreach ($event_date as $entry) {
                        $dt = date('Y-m-d', strtotime($entry['schedule']));
                        if (strtotime($dt) > strtotime(date('Y-m-d'))) {
                            $earliestDate = $dt;
                            break;
                        }
                    }
                }
                $nextPoojaDate = $earliestDate ?: null;
                $poojaTypeText = 'Special Pooja';
            } else {
                $nextPoojaDate = $nextPoojaDay ? (is_string($nextPoojaDay) ? date('Y-m-d H:i:s', strtotime($nextPoojaDay)) : $nextPoojaDay->format('Y-m-d H:i:s')) : null;
                $poojaTypeText = 'Weekly Pooja';
            }

            // Format final service object
            return [
                'id' => $service->id,
                'en_name' => $service->name,
                'hi_name' => $translations['name'] ?? null,
                'slug' => $service->slug,
                'image' => $service->images ? url('/storage/app/public/pooja/' . $service->images) : null,
                'status' => $service->status,
                'user_id' => $service->user_id,
                'added_by' => $service->added_by,
                'en_short_benefits' => $service->short_benefits,
                'hi_short_benefits' => $translations['short_benefits'] ?? null,
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
                'pooja_venue' => $serviceVenues,
                'pooja_time' => $service->pooja_time,
                'week_days' => $weekDaysText,
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
                'next_pooja_date' => $nextPoojaDate,
                'pooja_type_text' => $poojaTypeText,
            ];
        });

        // Filter out services with empty English names or null service data
        $filteredData = $services->filter(function ($item) {
            return !empty($item['en_name']);
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
        $services = Service::where('status', 1)
            ->where('category_id', 33) // Assuming category_id is constant here
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
                        $ProductModel = Product::find($productId);
                        if ($ProductModel) {
                            $productTranslations = $ProductModel->translations()->pluck('value', 'key')->toArray();
                            $serviceProducts[] = [
                                'product_id' => $productId,
                                'en_name' => $ProductModel->name,
                                'hi_name' => $productTranslations['name'] ?? null,
                                'price' => $ProductModel->unit_price,
                                'thumbnail' => $ProductModel->thumbnail ? url('/storage/app/public/product/thumbnail/' . $ProductModel->thumbnail) : null,
                                'images'=> $ProductModel->images ? url('/storage/app/public/product/' . $ProductModel->images) : null,
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


                if ($service->pooja_venue) {
                    $serviceVenues = json_decode($service->pooja_venue, true);
                    if (is_array($serviceVenues)) {
                        $service->pooja_venue = implode(', ', $serviceVenues);
                    }
                }

                // Process translations for the service
                $translations = $service->translations()->pluck('value', 'key')->toArray();

                // Format final service object
                $serviceData = [
                    'id' => $service->id,
                    'en_name' => $service->name,
                    'hi_name' => $translations['name'] ?? null,
                    'slug' => $service->slug,
                    'image' => $service->images ? url('/storage/app/public/pooja/' . $service->images) : null,
                    'status' => $service->status,
                    'user_id' => $service->user_id,
                    'added_by' => $service->added_by,
                    'en_short_benefits' => $service->short_benefits,
                    'hi_short_benefits' => $translations['short_benefits'] ?? null,
                    'product_type' => $service->product_type,
                    'pooja_type' => $service->pooja_type,
                    'schedule' => $service->schedule,
                    'counselling_main_price' => $service->counselling_main_price,
                    'counselling_selling_price' => $service->counselling_selling_price,
                    'en_details' => $service->details,
                    'hi_details' => $translations['description'] ?? null,
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
                    'pooja_venue' => $service->pooja_venue,
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
                    'venues' => $serviceVenues,
                ];

                // Additional processing for week days
                $poojaw = json_decode($service->week_days);
                $poojat = date('H:i:s', strtotime($service->pooja_time));
                $nextPoojaDay = Helpers::getNextPoojaDay($poojaw, $poojat);

                // Check pooja_type and handle accordingly
                if ($service->pooja_type == 1) { // Special Pooja
                    // For Special Pooja, use the earliest date from the schedule
                    $earliestDate = null;
                    if (!empty($service->schedule)) {
                        $event_date = json_decode($service->schedule, true);
                        usort($event_date, function($a, $b) {
                            return strtotime($a['schedule']) - strtotime($b['schedule']);
                        });
                        foreach ($event_date as $entry) {
                            $dt = date('Y-m-d', strtotime($entry['schedule']));
                            if (strtotime($dt) > strtotime(date('Y-m-d'))) {
                                $earliestDate = $dt;
                                break;
                            }
                        }
                    }
                    // If no next date is found, return null
                    if (!$earliestDate) {
                        return null;
                    }
                    $serviceData['next_pooja_date'] = $earliestDate;
                    $serviceData['pooja_type_text'] = 'Special Pooja';
                } else {
                    // For Weekly Pooja, use the calculated next pooja day
                    if ($nextPoojaDay instanceof \DateTime) {
                        $serviceData['next_pooja_date'] = $nextPoojaDay->format('Y-m-d H:i:s');
                    } else {
                        $serviceData['next_pooja_date'] = $nextPoojaDay ? date('Y-m-d H:i:s', strtotime($nextPoojaDay)) : null;
                    }
                    $serviceData['pooja_type_text'] = 'Weekly Pooja';
                }

                return $serviceData;
            });

            // Filter out services with empty English names or null service data
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
    
    // Single Pooja Show by slug
public function getServiceBySlug($slug)
{
    $service = Service::where('status', 1)
        ->where('slug', $slug)
        ->where('category_id', '33')
        ->where('product_type', 'pooja')
        ->with(['categories', 'pandit', 'product'])
        ->first();

    if ($service) {
        // Transform the service data
        $transformedService = $this->transformService($service);

        // Additional processing for week days and pooja types
        $poojaw = json_decode($service->week_days, true);
        $poojat = date('H:i:s', strtotime($service->pooja_time));
        $nextPoojaDay = Helpers::getNextPoojaDay($poojaw, $poojat);

        if ($service->pooja_type == 1) { // Special Pooja
            // For Special Pooja, use the earliest date from the schedule
            $earliestDate = null;
            if (!empty($service->schedule)) {
                $event_date = json_decode($service->schedule, true);
                usort($event_date, function($a, $b) {
                    return strtotime($a['schedule']) - strtotime($b['schedule']);
                });
                foreach ($event_date as $entry) {
                    $dt = date('Y-m-d', strtotime($entry['schedule']));
                    if (strtotime($dt) > strtotime(date('Y-m-d'))) {
                        $earliestDate = $dt;
                        break;
                    }
                }
            }
            $transformedService['next_pooja_date'] = $earliestDate ?? null;
            $transformedService['pooja_type_text'] = 'Special Pooja';
        } else {
            // For Weekly Pooja, use the calculated next pooja day
            if ($nextPoojaDay instanceof \DateTime) {
                $transformedService['next_pooja_date'] = $nextPoojaDay->format('Y-m-d H:i:s');
            } else {
                $transformedService['next_pooja_date'] = $nextPoojaDay ? date('Y-m-d H:i:s', strtotime($nextPoojaDay)) : null;
            }
            $transformedService['pooja_type_text'] = 'Weekly Pooja';
        }

        // Check if the transformed service has a valid 'en_name'
        if (empty($transformedService['en_name'])) {
            return response()->json(['status' => 400, 'message' => 'Service name not available.']);
        } else {
            return response()->json(['status' => 200, 'data' => $transformedService]);
        }
    }

    return response()->json(['status' => 404, 'message' => 'Service not found.']);
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
            $ProductModel = Product::find($productId);
            if ($ProductModel) {
                $productTranslations = $ProductModel->translations()->pluck('value', 'key')->toArray();
                $serviceProducts[] = [
                    'product_id' => $productId,
                    'en_name' => $ProductModel->name,
                    'hi_name' => $productTranslations['name'] ?? null,
                    'price' => $ProductModel->unit_price,
                    'thumbnail' => $ProductModel->thumbnail ? url('/storage/app/public/product/thumbnail/' . $ProductModel->thumbnail) : null,
                    'images'=> $ProductModel->images ? url('/storage/app/public/product/' . $ProductModel->images) : null,
                ];
            }
        }
    }


    // Handle venues
    $venues = null;
    if ($service->pooja_venue) {
        $venues = json_decode($service->pooja_venue, true);
        if (is_array($venues)) {
            $serviceVenues = implode(', ', $venues);
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

    // Handle week_days
    $weekDaysArray = json_decode($service->week_days, true);
    $weekDays = is_array($weekDaysArray) ? implode(', ', $weekDaysArray) : '';

    // Format final service object
    return [
        'id' => $service->id,
        'en_name' => $service->name,
        'hi_name' => $translations['name'] ?? null,
        'slug' => $service->slug,
        'image' => $service->images ? url('/storage/app/public/pooja/' . $service->images) : null,
        'status' => $service->status,
        'user_id' => $service->user_id,
        'added_by' => $service->added_by,
        'en_short_benefits' => $service->short_benefits,
        'hi_short_benefits' => $translations['short_benefits'] ?? null,
        'product_type' => $service->product_type,
        'pooja_type' => $service->pooja_type,
        'schedule' => $service->schedule,
        'counselling_main_price' => $service->counselling_main_price,
        'counselling_selling_price' => $service->counselling_selling_price,
        'en_details' => $service->details,
        'hi_details' => $translations['description'] ?? null,
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
        'pooja_venue' => $service->pooja_venue,
        'pooja_time' => $service->pooja_time,
        'week_days' => $weekDays,
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
        'venues' => $serviceVenues,
    ];
}


    // Front Side Api Order Get the Data
    public function LeadStore(Request $request)
    {
        // Extracting customer details from the request
        $Lead_details = [
            'service_id' => $request->input('service_id'),
            'type' => 'pooja',
            'package_id' => $request->input('package_id'),
            'product_id' =>  json_encode($request->input('product_id')),
            'package_name' => $request->input('package_name'),
            'package_price' => $request->input('package_price'),
            'noperson' => $request->input('noperson'),
            'person_phone' => $request->input('person_phone'),
            'person_name' => $request->input('person_name'),
            'booking_date' => $request->input('booking_date'),
        ];

      
        // Store lead in the Leads table
        $lead = Leads::create($Lead_details);

        // Return a JSON response
        return response()->json([
            'success' => true,
            'message' => 'Lead successfully created.',
            'lead' => $lead,
        ], 201);
    }

    public function SankalpStore($orderId, Request $request){
        // Prepare customer details from the request
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
        ];
        $sankalpDataUpdated = Service_order::where('order_id', $orderId)->update($cust_details);
        $sankalpData = Service_order::where('order_id', $orderId)
            ->with(['customers', 'services', 'packages', 'leads'])
            ->first();  
        if ($sankalpDataUpdated) {
            return response()->json([
                'success' => true,
                'message' => 'Pooja Sankalp Record Successfully Inserted.',
                'sankalpData' => $sankalpData,
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update service order.',
            ], 500);
        }
    }
    
    public function getAllOrders(Request $request)
    {
        if (!empty(Auth::guard('api')->user()->id)) {
            $orders = null;
            $type = $request->query('type');
            if ($type) {
                $orders = Service_order::where('type', $type)
                    ->where('customer_id', Auth::guard('api')->user()->id)
                    ->with(['order', 'services', 'chadhava'])
                    ->orderBy('id', 'desc')
                    ->get();
            } else {
                $orders = Service_order::where('customer_id', Auth::guard('api')->user()->id)
                    ->with(['order', 'services', 'vippoojas', 'chadhava', 'chadhavaOrders'])
                    ->orderBy('id', 'desc')
                    ->get();
            }
            if ($orders) {
                return response()->json([
                    'status' => true,
                    'data' => $orders,
                ]);
            } else {
                return response()->json([
                    'status' => true,
                    'data' => $orders,
                ]);
            }
        }
        return response()->json(['status' => false, 'message' => 'Unauthorized user']);
    }

    public function poojaDetails($orderid)
    {
        // Retrieve the service order with related data
        $serviceOrder = Service_order::where('order_id', $orderid)
            ->with('leads')
            ->with('services')
            ->with('packages')
            ->with('payments')
            ->with('pandit')
            ->with('product_leads')
            ->first();
    
        // Check if the service order exists
        if (!$serviceOrder) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid order ID or order not found.',
            ], 404);
        }
    
        // Retrieve the customer details based on the phone number in leads
        $customer = User::where('phone', $serviceOrder['leads']['person_phone'])->first();
        if ($customer) {
            $serviceOrder['customer'] = $customer;
        } else {
            $serviceOrder['customer'] = null; // Or handle this case as needed
        }
    
        // Retrieve the available pandits for the pooja based on the service_id
        $poojaPandits = Astrologer::where('is_pandit_pooja', 'like', '%' . $serviceOrder['service_id'] . '%')->get();
        $serviceOrder['pooja_pandit'] = $poojaPandits;
    
        // Return the data as a JSON response
        return response()->json([
            'success' => true,
            'order' => $serviceOrder,
            'refund_day_limit' => getWebConfig('refund_day_limit'),
            'current_date' => Carbon::now(),
        ], 200);
    }

    public function chadhavaDetailsOrder($orderid)
    {
        // Retrieve the service order with related data
        $ChadhavaOrder = Chadhava_orders::where('order_id', $orderid)
            ->with('leads')
            ->with('vippoojas')
            ->with('packages')
            ->with('payments')
            ->with('pandit')
            ->with('product_leads')
            ->first();
  
        if (!$ChadhavaOrder) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid order ID or order not found.',
            ], 404);
        }
    
        $customer = User::where('phone', $ChadhavaOrder['leads']['person_phone'])->first();
        if ($customer) {
            $ChadhavaOrder['customer'] = $customer;
        } else {
            $ChadhavaOrder['customer'] = null; 
        }
    
        $poojaPandits = Astrologer::where('is_pandit_pooja', 'like', '%' . $ChadhavaOrder['service_id'] . '%')->get();
        $ChadhavaOrder['pooja_pandit'] = $poojaPandits;
    
   
        return response()->json([
            'success' => true,
            'order' => $ChadhavaOrder,
            'refund_day_limit' => getWebConfig('refund_day_limit'),
            'current_date' => Carbon::now(),
        ], 200);
    }

    // Order List
    public function poojaList(Request $request){
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
        $vippooja = Vippooja::where('status', 1)->where('is_anushthan', '0')->with(['pandit', 'product'])->get();
        if ($vippooja->isNotEmpty()) {
            $vippooja->transform(function ($vippooja) {
                $packages = json_decode($vippooja->packages_id, true);
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
                $products = json_decode($vippooja->product_id, true);
                $vippoojaProducts = [];
                if (is_array($products)) {
                    foreach ($products as $productId) {
                        $ProductModel = Product::find($productId);
                        if ($ProductModel) {
                            $productTranslations = $ProductModel->translations()->pluck('value', 'key')->toArray();
                            $vippoojaProducts[] = [
                                'product_id' => $productId,
                                'en_name' => $ProductModel->name,
                                'hi_name' => $productTranslations['name'] ?? null,
                                'price' => $ProductModel->unit_price,
                                'thumbnail' => $ProductModel->thumbnail ? url('/storage/app/public/product/thumbnail/' . $ProductModel->thumbnail) : null,
                                'images' => $ProductModel->images ? url('/storage/app/public/product/' . $ProductModel->images) : null,
                            ];
                        }
                    }
                }
                $translations = $vippooja->translations()->pluck('value', 'key')->toArray();               
                $vipData = [
                    'id' => $vippooja->id,
                    'en_name' => $vippooja->name,
                    'hi_name' => $translations['name'] ?? null,
                    'slug' => $vippooja->slug,
                    'image' => $vippooja->images ? url('/storage/app/public/vip/' . $vippooja->images) : null,
                    'status' => $vippooja->status,
                    'user_id' => $vippooja->user_id,
                    'added_by' => $vippooja->added_by,
                    'en_short_benefits' => $vippooja->short_benefits,
                    'hi_short_benefits' => $translations['short_benefits'] ?? null,
                    'is_anushthan' => $vippooja->is_anushthan,
                    'en_details' => $vippooja->details,
                    'hi_details' => $translations['description'] ?? null,
                    'en_benefits' => $vippooja->benefits,
                    'hi_benefits' => $translations['benefits'] ?? null,
                    'en_process' => $vippooja->process,
                    'hi_process' => $translations['process'] ?? null,
                    'en_temple_details' => $vippooja->temple_details,
                    'hi_temple_details' => $translations['temple_details'] ?? null,
                    'is_anushthan' => $vippooja->is_anushthan,
                    'product_id' => $vippooja->product_id,
                    'packages_id' => $vippooja->packages_id,
                    'video_provider' => $vippooja->video_provider,
                    'video_url' => $vippooja->video_url,
                    'thumbnail' => $vippooja->thumbnail ? url('/storage/app/public/vip/thumbnail/' . $vippooja->thumbnail) : null,
                    'digital_file_ready' => $vippooja->digital_file_ready,
                    'en_meta_title' => $vippooja->meta_title,
                    'hi_meta_title' => $translations['meta_title'] ?? null,
                    'meta_description' => $vippooja->meta_description,
                    'meta_image' => $vippooja->meta_image,
                    'packages' => $vippoojaPackages,
                    'products' => $vippoojaProducts,
                  
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


  
    public function vipDetails($id)
    {
        $vip = Vippooja::where('status', 1)->where('id', $id)->where('is_anushthan', '0')->with(['pandit', 'product','packages'])->first();
        
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
                $ProductModel = Product::find($productId);
                if ($ProductModel) {
                    $productTranslations = $ProductModel->translations()->pluck('value', 'key')->toArray();
                    $vippoojaProducts[] = [
                        'product_id' => $productId,
                        'en_name' => $ProductModel->name,
                        'hi_name' => $productTranslations['name'] ?? null,
                        'price' => $ProductModel->unit_price,
                        'thumbnail' => $ProductModel->thumbnail ? url('/storage/app/public/product/thumbnail/' . $ProductModel->thumbnail) : null,
                        'images' => $ProductModel->images ? url('/storage/app/public/product/' . $ProductModel->images) : null,
                    ];
                }
            }
        }
        // Process translations for the service
        $translations = $vip->translations()->pluck('value', 'key')->toArray();
        return[
            'id' => $vip->id,
            'en_name' => $vip->name,
            'hi_name' => $translations['name'] ?? null,
            'slug' => $vip->slug,
            'image' => $vip->images ? url('/storage/app/public/vip/' . $vip->images) : null,
            'status' => $vip->status,
            'user_id' => $vip->user_id,
            'added_by' => $vip->added_by,
            'en_short_benefits' => $vip->short_benefits,
            'hi_short_benefits' => $translations['short_benefits'] ?? null,
            'is_anushthan' => $vip->is_anushthan,
            'en_details' => $vip->details,
            'hi_details' => $translations['description'] ?? null,
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
            'thumbnail' => $vip->thumbnail ? url('/storage/app/public/vip/thumbnail/' . $vip->thumbnail) : null,
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
       
        $Lead_details = [
            'service_id' => $request->input('service_id'),
            'type' => 'pooja',
            'package_id' => $request->input('package_id'),
            'product_id' =>  json_encode($request->input('product_id')),
            'package_name' => $request->input('package_name'),
            'package_price' => $request->input('package_price'),
            'noperson' => $request->input('noperson'),
            'person_phone' => $request->input('person_phone'),
            'person_name' => $request->input('person_name'),
            'booking_date' => $request->input('booking_date'),
        ];
        $lead = Leads::create($Lead_details);
        return response()->json([
            'success' => true,
            'message' => 'Lead successfully created.',
            'lead' => $lead,
        ], 201);
    }

    public function VipSankalpStore($orderId, Request $request){
        $vipcust_details = [
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
        ];
        $sankalpDataUpdated = Service_order::where('order_id', $orderId)->update($vipcust_details);
        $sankalpData = Service_order::where('order_id', $orderId)
            ->with(['customers', 'vippoojas', 'packages', 'leads'])
            ->first();  
        if ($sankalpDataUpdated) {
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
        $vippooja = Vippooja::where('status', 1)->where('is_anushthan', '1')->with(['pandit', 'product'])->get();
        if ($vippooja->isNotEmpty()) {
            $vippooja->transform(function ($vippooja) {
                $packages = json_decode($vippooja->packages_id, true);
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
                $products = json_decode($vippooja->product_id, true);
                $vippoojaProducts = [];
                if (is_array($products)) {
                    foreach ($products as $productId) {
                        $ProductModel = Product::find($productId);
                        if ($ProductModel) {
                            $productTranslations = $ProductModel->translations()->pluck('value', 'key')->toArray();
                            $vippoojaProducts[] = [
                                'product_id' => $productId,
                                'en_name' => $ProductModel->name,
                                'hi_name' => $productTranslations['name'] ?? null,
                                'price' => $ProductModel->unit_price,
                                'thumbnail' => $ProductModel->thumbnail ? url('/storage/app/public/product/thumbnail/' . $ProductModel->thumbnail) : null,
                                'images' => $ProductModel->images ? url('/storage/app/public/product/' . $ProductModel->images) : null,
                            ];
                        }
                    }
                }
                $translations = $vippooja->translations()->pluck('value', 'key')->toArray();               
                $vipData = [
                    'id' => $vippooja->id,
                    'en_name' => $vippooja->name,
                    'hi_name' => $translations['name'] ?? null,
                    'slug' => $vippooja->slug,
                    'image' => $vippooja->images ? url('/storage/app/public/vip/' . $vippooja->images) : null,
                    'status' => $vippooja->status,
                    'user_id' => $vippooja->user_id,
                    'added_by' => $vippooja->added_by,
                    'en_short_benefits' => $vippooja->short_benefits,
                    'hi_short_benefits' => $translations['short_benefits'] ?? null,
                    'is_anushthan' => $vippooja->is_anushthan,
                    'en_details' => $vippooja->details,
                    'hi_details' => $translations['description'] ?? null,
                    'en_benefits' => $vippooja->benefits,
                    'hi_benefits' => $translations['benefits'] ?? null,
                    'en_process' => $vippooja->process,
                    'hi_process' => $translations['process'] ?? null,
                    'en_temple_details' => $vippooja->temple_details,
                    'hi_temple_details' => $translations['temple_details'] ?? null,
                    'is_anushthan' => $vippooja->is_anushthan,
                    'product_id' => $vippooja->product_id,
                    'packages_id' => $vippooja->packages_id,
                    'video_provider' => $vippooja->video_provider,
                    'video_url' => $vippooja->video_url,
                    'thumbnail' => $vippooja->thumbnail ? url('/storage/app/public/vip/thumbnail/' . $vippooja->thumbnail) : null,
                    'digital_file_ready' => $vippooja->digital_file_ready,
                    'en_meta_title' => $vippooja->meta_title,
                    'hi_meta_title' => $translations['meta_title'] ?? null,
                    'meta_description' => $vippooja->meta_description,
                    'meta_image' => $vippooja->meta_image,
                    'packages' => $vippoojaPackages,
                    'products' => $vippoojaProducts,
                  
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

    public function AnushthanDetails($id)
    {
        $anushthan = Vippooja::where('status', 1)->where('id', $id)->where('is_anushthan', '1')->with(['pandit', 'product'])->first();
        
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
                $ProductModel = Product::find($productId);
                if ($ProductModel) {
                    $productTranslations = $ProductModel->translations()->pluck('value', 'key')->toArray();
                    $vippoojaProducts[] = [
                        'product_id' => $productId,
                        'en_name' => $ProductModel->name,
                        'hi_name' => $productTranslations['name'] ?? null,
                        'price' => $ProductModel->unit_price,
                        'thumbnail' => $ProductModel->thumbnail ? url('/storage/app/public/product/thumbnail/' . $ProductModel->thumbnail) : null,
                        'images' => $ProductModel->images ? url('/storage/app/public/product/' . $ProductModel->images) : null,
                    ];
                }
            }
        }
        // Process translations for the service
        $translations = $anushthan->translations()->pluck('value', 'key')->toArray();
        return[
            'id' => $anushthan->id,
            'en_name' => $anushthan->name,
            'hi_name' => $translations['name'] ?? null,
            'slug' => $anushthan->slug,
            'image' => $anushthan->images ? url('/storage/app/public/vip/' . $anushthan->images) : null,
            'status' => $anushthan->status,
            'user_id' => $anushthan->user_id,
            'added_by' => $anushthan->added_by,
            'en_short_benefits' => $anushthan->short_benefits,
            'hi_short_benefits' => $translations['short_benefits'] ?? null,
            'en_details' => $anushthan->details,
            'hi_details' => $translations['description'] ?? null,
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
            'thumbnail' => $anushthan->thumbnail ? url('/storage/app/public/vip/thumbnail/' . $anushthan->thumbnail) : null,
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
        $Lead_details = [
            'service_id' => $request->input('service_id'),
            'type' => 'pooja',
            'package_id' => $request->input('package_id'),
            'product_id' =>  json_encode($request->input('product_id')),
            'package_name' => $request->input('package_name'),
            'package_price' => $request->input('package_price'),
            'noperson' => $request->input('noperson'),
            'person_phone' => $request->input('person_phone'),
            'person_name' => $request->input('person_name'),
            'booking_date' => $request->input('booking_date'),
        ];
        $lead = Leads::create($Lead_details);
        return response()->json([
            'success' => true,
            'message' => 'Lead successfully created.',
            'lead' => $lead,
        ], 201);
    }

    public function AnushthanSankalpStore($orderId, Request $request){
        $vipcust_details = [
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
        ];
        $sankalpDataUpdated = Service_order::where('order_id', $orderId)->update($vipcust_details);
        $sankalpData = Service_order::where('order_id', $orderId)
            ->with(['customers', 'vippoojas', 'packages', 'leads'])
            ->first();  
        if ($sankalpDataUpdated) {
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
    // -------------------------------------------------GET ALL THE CHADHAVA DETAILS=-----------------------------------------------------
    public function getallChadhava()
    {
        $chadhava = Chadhava::where('status', 1)->with(['pandit', 'product', 'cities'])->get();

        if ($chadhava->isNotEmpty()) {
            $chadhava->transform(function ($chadhava) {
                $cityNameGet = $chadhava->chadhava_city;
                $GetName = Cities::find($cityNameGet);
                // dd($GetName);
                $products = json_decode($chadhava->product_id, true);
                $chadhavaProducts = [];
                if (is_array($products)) {
                    foreach ($products as $productId) {
                        $ProductModel = Product::find($productId);
                        if ($ProductModel) {
                            $productTranslations = $ProductModel->translations()->pluck('value', 'key')->toArray();
                            $chadhavaProducts[] = [
                                'product_id' => $productId,
                                'en_name' => $ProductModel->name,
                                'hi_name' => $productTranslations['name'] ?? null,
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
                    'chadhava_city' => $GetName->city,
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

    public function chadhavaDetails($id)
    {
        $chadhava = Chadhava::where('status', 1)
            ->where('id', $id)
            ->with(['cities', 'product'])
            ->first();
       
            if ($chadhava !== null) {  // Check if the service exists
                // Transform the service data
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
        $cityNameGet = $chadhava->chadhava_city;
         $GetName = Cities::find($cityNameGet);
        $products = json_decode($chadhava->product_id, true);
        $chadhavaProducts = [];
        if (is_array($products)) {
            foreach ($products as $productId) {
                $ProductModel = Product::find($productId);
                if ($ProductModel) {
                    $productTranslations = $ProductModel->translations()->pluck('value', 'key')->toArray();
                    $chadhavaProducts[] = [
                        'product_id' => $productId,
                        'en_name' => $ProductModel->name,
                        'hi_name' => $productTranslations['name'] ?? null,
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
        return[
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
            'chadhava_city' => $GetName->city,
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
    }

    // VIP DATE LEAD STORE AND SNAKAL DATA STORE
    public function ChadhavaLeadStore(Request $request)
    {
       
        $Lead_details = [
            'service_id' => $request->input('service_id'),
            'type' => 'pooja',
            'package_id' => $request->input('package_id'),
            'product_id' =>  json_encode($request->input('product_id')),
            'package_name' => $request->input('package_name'),
            'package_price' => $request->input('package_price'),
            'noperson' => $request->input('noperson'),
            'person_phone' => $request->input('person_phone'),
            'person_name' => $request->input('person_name'),
            'booking_date' => $request->input('booking_date'),
        ];
        $lead = Leads::create($Lead_details);
        return response()->json([
            'success' => true,
            'message' => 'Lead successfully created.',
            'lead' => $lead,
        ], 201);
    }

    public function ChadhavaSankalpStore($orderId, Request $request){
        $vipcust_details = [
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
        ];
        $sankalpDataUpdated = Service_order::where('order_id', $orderId)->update($vipcust_details);
        $sankalpData = Service_order::where('order_id', $orderId)
            ->with(['customers', 'vippoojas', 'packages', 'leads'])
            ->first();  
        if ($sankalpDataUpdated) {
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

    
    
}
