<?php

namespace App\Http\Controllers\RestAPI\v1;

use App\Http\Controllers\Controller;
use App\Models\Bhagwan;
use App\Models\BhagwanLogs;
use App\Models\Chadhava;
use App\Models\Product;
use App\Models\Sangeet;
use App\Models\SangeetCategory;
use App\Models\SangeetDetails;
use App\Models\SangeetSubCategory;
use App\Utils\Helpers;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use DateTime;
use DateTimeZone;

class BhagwanController extends Controller
{

    public function getBhagwanImage()
    {

        $currentDateTime = new DateTime('now', new DateTimeZone('Asia/Kolkata'));
        $currentDate = $currentDateTime->format('Y-m-d');

        $bhagwan = Bhagwan::where('status', 1)
            ->get();

        if ($bhagwan->isNotEmpty()) {
            $data = $bhagwan->map(function ($item) use ($currentDate) {
                $Translations = $item->translations()->pluck('value', 'key')->toArray();
                $hiName = $Translations['name'] ?? null;
            
                $images = json_decode($item->images, true);
                $images = is_array($images) ? $images : [];
            
                $itemDate = $item->date; 
                $eventImage = $itemDate === $currentDate ? $item->event_image : null;
                $itemDate = $itemDate === $currentDate ? $itemDate : null; 
            
                return [
                    'id' => $item->id,
                    'en_name' => $item->name,
                    'hi_name' => $hiName,
                    'thumbnail' => $item->thumbnail ? url('storage/app/public/bhagwan/thumbnail/' . $item->thumbnail) : null,
                    'images' => !empty($images) ? array_map(function ($image) {
                        return url('/storage/app/public/bhagwan/' . $image);
                    }, $images) : [],
                    'week' => $item->week,
                    'date' => $itemDate, 
                    'event_image' => $eventImage ? url('storage/app/public/bhagwan/event-img/' . $item->event_image) : null,
                ];
            });
            
            // Get today's day name
            $today = now()->format('l'); 

            $uniqueWeekDays = $data->pluck('week')->unique()->values()->toArray();
    
            $todayIndex = array_search($today, $uniqueWeekDays);
            $rotatedOrder = array_merge(
                array_slice($uniqueWeekDays, $todayIndex),
                array_slice($uniqueWeekDays, 0, $todayIndex)
            );
            
            $data = $data->sortBy(function ($item) use ($rotatedOrder) {
                return array_search($item['week'], $rotatedOrder);
            })->values();
            

            return response()->json([
                'status' => 200,
                'data' => $data,
            ]);
        }

        return response()->json([
            'status' => 400,
            'message' => 'No data available.',
        ]);
    }

    public function BhagwanLogs(Request $request): JsonResponse
    {
        $user = Auth::guard('api')->user();

        if (!$user || empty($user->id)) {
            return response()->json(['message' => 'Unauthorized']);
        }

        $location = $request->input('location');
        $duration = $request->input('duration');

        if (empty($location) || empty($duration)) {
            return response()->json(['status' => 400, 'message' => 'Invalid request parameters'], 400);
        }

        $item = new BhagwanLogs();
        $item->user_id = $user->id; 
        $item->location = $location;
        $item->duration = $duration;
        $item->save();

        return response()->json(['status' => 200, 'message' => 'created successfully']);
    }

    public function getBhagwanSangeet(Request $request)
    {   
        $category_id = $request->input('category_id');
        $subcategory_id = $request->input('subcategory_id');
        $language = $request->input('language');

        if (!$category_id) {
            return response()->json(['error' => 'Category ID is required'], 422);
        }

        if (!$subcategory_id) {
            return response()->json(['error' => 'Subcategory ID is required'], 422);
        }

        if (!$language) {
            return response()->json(['error' => 'Language is required'], 422);
        }

        // Check for the 'famous' subcategory
        $subcategory = SangeetSubCategory::where('id', $subcategory_id)
            ->where('category_id', $category_id)
            ->first();

        if ($subcategory && strtolower($subcategory->name) === 'famous') {
            $subcategories = SangeetSubCategory::where('category_id', $category_id)->get();
            $data = [];
            foreach ($subcategories as $sub) {
                $sangeet = Sangeet::where('subcategory_id', $sub->id)
                    ->where('language', $language)
                    ->first();

                if ($sangeet) {
                    // Fetch SangeetDetails with famous = 1
                    $sangeet_details = SangeetDetails::where('sangeet_id', $sangeet->id)
                        ->where('famous', 1)
                        ->get(['id', 'title', 'singer_name', 'audio', 'lyrics', 'image', 'background_image', 'famous', 'status', 'created_at', 'updated_at', 'deleted_at']);

                    foreach ($sangeet_details as $detail) {
                        $detail->image = $detail->image ? url('storage/app/public/sangeet-img/' . $detail->image) : null;
                        $detail->background_image = $detail->background_image ? url('storage/app/public/sangeet-background-img/' . $detail->background_image) : null;
                        $detail->audio = $detail->audio ? url('storage/app/public/sangeet-audio/' . $detail->audio) : null;

                        $data[] = $detail;
                    }
                }
            }

            if (!empty($data)) {
                return response()->json([
                    'status' => 200,
                    'sangeet' => $data,
                ]);
            }
            return response()->json(['status' => 400, 'message' => 'No data found for the category']);
        }

        if($subcategory_id == 'all'){
            $subcategory_data = SangeetSubCategory::where('category_id', $category_id)->get()->pluck('id');
            $sangeet = Sangeet::where('category_id', $category_id)->whereIn('subcategory_id', $subcategory_data)
            ->where('language', $language)
            ->get()->pluck('id');
            //dd($sangeet);
        }else{
           $sangeet = Sangeet::where('category_id', $category_id)->where('subcategory_id', $subcategory_id)
            ->where('language', $language)
            ->get()->pluck('id');
        }

        
        if (!$sangeet) {
            return response()->json(['error' => 'Sangeet not found'], 404);
        }

        $sangeet_details = SangeetDetails::whereIn('sangeet_id', $sangeet)
            ->get(['id', 'title', 'singer_name', 'audio', 'lyrics', 'image', 'background_image', 'famous', 'status', 'created_at', 'updated_at', 'deleted_at']);

        foreach ($sangeet_details as $detail) {
            if ($detail->image) {
                $detail->image = url('storage/app/public/sangeet-img/' . $detail->image);
            }
            if ($detail->background_image) {
                $detail->background_image = url('storage/app/public/sangeet-background-img/' . $detail->background_image);
            }
            if ($detail->audio) {
                $detail->audio = url('storage/app/public/sangeet-audio/' . $detail->audio);
            }
        }

        if ($sangeet_details->isNotEmpty()) {
            return response()->json([
                'status' => 200,   
                'sangeet' => $sangeet_details
            ]);
        }

        return response()->json(['status' => 400, 'message' => 'Sangeet language data not found']);
    }

    public function getByCategoryName(Request $request)
    {

        $request->validate([
            'category_name' => 'required|string|max:255',
        ]);

        $categoryName = $request->input('category_name');

        $category = SangeetCategory::where('name', $categoryName)->first();

        if (!$category) {
            return response()->json(['status' => 400, 'message' => 'Category not found.'], 400);
        }

        $sangeetsubCategory = SangeetSubCategory::where('category_id', $category->id)
            ->where('status', 1)
            ->get();

        if ($sangeetsubCategory->isEmpty()) {
            return response()->json(['status' => 400, 'message' => 'No subcategories available for this category.'], 400);
        }

        $data = $sangeetsubCategory->map(function ($item) {
            $translations = $item->translations()->pluck('value', 'key')->toArray();

            return [
                'id' => $item->id,
                'en_name' => $item->name,
                'hi_name' => $translations['name'] ?? null,
                'status' => $item->status,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
            ];
        })->values();

        return response()->json([
            'status' => 200,
            'category' => [
                'name' => $category->name,
                'id' => $category->id
            ],
            'subcategories' => $data,
        ]);
    }

    public function getBhagwanChadhava($bhagwan_id)
    {
        if (!is_numeric($bhagwan_id)) {
            return response()->json(['error' => 'Invalid bhagwan ID'], 400);
        }
    
        $chadhava = Chadhava::where('bhagwan_id', $bhagwan_id)->where('status', 1)->get();
    
        if ($chadhava->isEmpty()) {
            return response()->json(['status' => 400, 'message' => 'No Date Wise Chadhava']);
        }
    
        $chadhavaData = [];
        $allProducts = [];
    
        foreach ($chadhava as $item) {
            $products = json_decode($item->product_id, true);
            if (is_array($products) && !empty($products)) {
                $productId = $products[0];
                $product = Product::find($productId);
                if ($product && empty($allProducts)) {
                    $translations = $product->translations()->pluck('value', 'key')->toArray();
                    $allProducts = [
                        'product_id' => $productId,
                        'en_name' => $product->name,
                        'hi_name' => $translations['name'] ?? null,
                        'thumbnail' => $product->thumbnail ? url('/storage/app/public/product/thumbnail/' . $product->thumbnail) : null,
                    ];
                }
            }
    
            $translations = $item->translations()->pluck('value', 'key')->toArray();
            $chadhavaWeek = json_decode($item->chadhava_week);
            $nextChadhavaDay = Helpers::getNextChadhavaDay($chadhavaWeek);
    
            $nextDate = null;
            $chadhavaTypeText = '';
    
            if ($item->chadhava_type == 1) {
                $startDate = $item->start_date;
                $endDate = $item->end_date;
                $currentDate = now();
    
                if ($startDate && $endDate && $startDate <= $endDate) {
                    $currentDateIter = $startDate->copy();
                    while ($currentDateIter <= $endDate) {
                        if ($currentDateIter > $currentDate) {
                            $nextDate = $currentDateIter->format('Y-m-d H:i:s');
                            break;
                        }
                        $currentDateIter->addDay();
                    }
                }
    
                $chadhavaTypeText = 'Date Wise Chadhava';
            } else {
                $nextDate = $nextChadhavaDay instanceof \DateTime
                    ? $nextChadhavaDay->format('Y-m-d H:i:s')
                    : ($nextChadhavaDay ? date('Y-m-d H:i:s', strtotime($nextChadhavaDay)) : null);
                $chadhavaTypeText = 'Weekly Chadhava';
            }
    
            $chadhavaData[] = [
                'id' => $item->id,
                'en_name' => $item->name,
                'hi_name' => $translations['name'] ?? null,
                'thumbnail' => $item->thumbnail ? url('/storage/app/public/chadhava/thumbnail/' . $item->thumbnail) : null,
                'en_short_details' => $item->short_details,
                'hi_short_details' => $translations['short_details'] ?? null,
                'next_chadhava_date' => $nextDate,
                'chadhava_type_text' => $chadhavaTypeText,
            ];
        }
    
        return response()->json([
            'status' => 200,
            'data' => [
                'chadhava' => array_values($chadhavaData),
                'products' => $allProducts,
            ],
        ]);
    }

    public function getBhagwanWallpaper()
    {

        $currentDateTime = new DateTime('now', new DateTimeZone('Asia/Kolkata'));
        $currentDate = $currentDateTime->format('Y-m-d');

        $bhagwan = Bhagwan::where('status', 1)
            ->get();

        if ($bhagwan->isNotEmpty()) {
            $data = $bhagwan->map(function ($item) use ($currentDate) {
                $Translations = $item->translations()->pluck('value', 'key')->toArray();
                $hiName = $Translations['name'] ?? null;
            
                $wallpapers = json_decode($item->wallpapers, true);
                $wallpapers = is_array($wallpapers) ? $wallpapers : [];
            
                $itemDate = $item->date; 
                $eventImage = $itemDate === $currentDate ? $item->event_image : null;
                $itemDate = $itemDate === $currentDate ? $itemDate : null; 
            
                return [
                    'id' => $item->id,
                    'en_name' => $item->name,
                    'hi_name' => $hiName,
                    'thumbnail' => $item->thumbnail ? url('storage/app/public/bhagwan/thumbnail/' . $item->thumbnail) : null,
                    'wallpapers' => !empty($wallpapers) ? array_map(function ($wallpapers) {
                        return url('/storage/app/public/bhagwan/wallpaper/' . $wallpapers);
                    }, $wallpapers) : [],
                    'week' => $item->week,
                    'date' => $itemDate,
                ];
            });
            
            // Get today's day name
            $today = now()->format('l'); 

            $uniqueWeekDays = $data->pluck('week')->unique()->values()->toArray();
    
            $todayIndex = array_search($today, $uniqueWeekDays);
            $rotatedOrder = array_merge(
                array_slice($uniqueWeekDays, $todayIndex),
                array_slice($uniqueWeekDays, 0, $todayIndex)
            );
            
            $data = $data->sortBy(function ($item) use ($rotatedOrder) {
                return array_search($item['week'], $rotatedOrder);
            })->values();
            

            return response()->json([
                'status' => 200,
                'data' => $data,
            ]);
        }

        return response()->json([
            'status' => 400,
            'message' => 'No data available.',
        ]);
    }

}