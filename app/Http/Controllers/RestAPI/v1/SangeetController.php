<?php

namespace App\Http\Controllers\RestAPI\v1;

use App\Http\Controllers\Controller;
use App\Models\Sangeet;
use App\Models\SangeetDetails;
use App\Models\SangeetCategory;
use App\Models\SangeetSubCategory;
use App\Models\SangeetLanguage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SangeetController extends Controller
{

    public function sangeet_category($id = null)
    {
        if ($id !== null) {
    
            // Check valid ID
            $exists = SangeetCategory::where('id', $id)
                ->where('status', 1)
                ->exists();
    
            if (!$exists) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Invalid Category ID'
                ]);
            }
        }
    
        $sangeetCategory = SangeetCategory::where('status', 1)
            ->select('id', 'name', 'image', 'banner', 'status')
            ->get();
    
        if ($sangeetCategory->isNotEmpty()) {
    
            $data = $sangeetCategory->map(function ($item) {
    
                $translations = $item->translations()
                    ->pluck('value', 'key')
                    ->toArray();
    
                return [
                    'id'      => $item->id,
                    'en_name' => $item->name,
                    'hi_name' => $translations['name'] ?? null,
                    'image'   => $item->image
                                    ? url('storage/app/public/sangeet-category-img/' . $item->image)
                                    : null,
                    'banner'  => $item->banner
                                    ? url('storage/app/public/sangeet-category-banner/' . $item->banner)
                                    : null,
                ];
            });
    
            return response()->json([
                'status' => 200,
                'data' => $data,
            ]);
        }
    
        return response()->json([
            'status' => 400,
            'message' => 'No sangeet services available.',
        ]);
    }

    public function getBySangeet_category($category_id)
    {

        if (!is_numeric($category_id)) {
            return response()->json(['error' => 'Invalid category ID'], 400);
        }

        $sangeetsubCategory = SangeetSubCategory::where('category_id', $category_id)
        ->where('status', 1)
        ->get();

        if ($sangeetsubCategory->isNotEmpty()) {
            $data = $sangeetsubCategory->map(function ($item) {
                
                $translations = $item->translations()->pluck('value', 'key')->toArray();

                return [
                    'id' => $item->id,
                    'category_id' => $item->category_id,
                    'en_name' => $item->name,
                    'hi_name' => $translations['name'] ?? null,
                    'status' => $item->status,
                    'created_at' => $item->created_at,
                    'updated_at' => $item->updated_at,
                ];
            });

            $filteredData = $data->filter(function ($item) {
                return !empty($item['en_name']);
            })->values();

            if ($filteredData->isEmpty()) {
                return response()->json([
                    'status' => 400,
                    'message' => 'No sangeet services found.',
                ]);
            }

            return response()->json([
                'status' => 200,
                'data' => $filteredData,
            ]);
        }

        return response()->json([
            'status' => 400,
            'message' => 'No sangeet services available.',
        ]);
    }

    public function sangeet_language()
    {
        $sangeetLanguage = SangeetLanguage::all();
            if ($sangeetLanguage->isNotEmpty()) {
        $data = $sangeetLanguage->map(function ($item) {
            $translations = $item->translations()->pluck('value', 'key')->toArray();

            return [
                'id' => $item->id,
                'en_name' => $item->name,
                'name' => $translations['name'] ?? null,
                'status' => $item->status,

            ];
        });

        $filteredData = $data->filter(function ($item) {
            return !empty($item['en_name']);
        })->values();

        if ($filteredData->isEmpty()) {
            return response()->json([
                'status' => 400,
                'message' => 'No language found.',
            ]);
        } else {
                return response()->json([
                    'status' => 200,
                    'data' => $filteredData,
                ]);
            }
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'No language available.',
            ]);
        }
    }



    //correct all details by subcategory
    //GET http://localhost/mahakal_web/api/v1/sangeet/sangeet-details?category_id=1&language=hindi
    public function getSangeetDetails(Request $request)
    {
        $subcategory_id = $request->input('subcategory_id');
        $language = $request->input('language');

        if (!$subcategory_id) {
            return response()->json(['error' => 'Subcategory ID is required'], 422);
        }

        if (!$language) {
            return response()->json(['error' => 'Language is required'], 422);
        }

        $subcategory = SangeetSubCategory::find($subcategory_id);

        if (!$subcategory) {
            return response()->json(['error' => 'Subcategory not found'], 404);
        }

        // If subcategory is 'famous', fetch all subcategories under the same category_id
        if (strtolower($subcategory->name) === 'famous') {
            $category_id = $subcategory->category_id;

            // Get all subcategories under the same category_id
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
                        if ($detail->image) {
                            $detail->image = url('storage/app/public/sangeet-img/' . $detail->image);
                        }
                        if ($detail->background_image) {
                            $detail->background_image = url('storage/app/public/sangeet-background-img/' . $detail->background_image);
                        }
                        if ($detail->audio) {
                            $detail->audio = url('storage/app/public/sangeet-audio/' . $detail->audio);
                        }

                        // Add each detail to the final data array
                        $data[] = $detail;
                    }
                }
            }

            if (!empty($data)) {
                return response()->json([
                    'status' => 200,
                    'category_id' => $category_id,
                    'sangeet' => $data,
                ]);
            }

            return response()->json(['status' => 400, 'message' => 'No data found for the category']);
        }


        $sangeet = Sangeet::where('subcategory_id', $subcategory_id)
            ->where('language', $language)
            ->first();

        if (!$sangeet) {
            return response()->json(['error' => 'Sangeet not found'], 404);
        }

        $sangeet_details = SangeetDetails::where('sangeet_id', $sangeet->id)
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
                'category_id' => $sangeet->category_id,
                'sangeet' => $sangeet_details
            ]);
        }

        return response()->json(['status' => 400, 'message' => 'Sangeet language data not found']);
    }

    //sangeet all details by category
    //GET "http://localhost/mahakal/api/v1/sangeet/sangeet-all-details?category_id=1&language=hindi"
    public function getSangeetAllDetails(Request $request)
    {
        $categoryId = $request->query('category_id');
        $language = $request->query('language');

        $category = SangeetCategory::find($categoryId);

        if (!$category) {
            return response()->json(['error' => 'Category not found'], 404);
        }

        $sangeetDetails = SangeetDetails::whereHas('sangeet', function ($query) use ($categoryId, $language) {
            $query->where('category_id', $categoryId)
                  ->where('language', $language);
        })->get(['id', 'title', 'singer_name', 'audio', 'lyrics', 'image', 'background_image', 'famous', 'status', 'created_at', 'updated_at', 'deleted_at']);

         foreach ($sangeetDetails as $detail) {
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

        if ($sangeetDetails->isEmpty()) {
            return response()->json(['message' => 'No details found'], 404);
        }

        return response()->json($sangeetDetails);
    }

}


//GET /api/sangeet-details?subcategory_id=1&language=hindi