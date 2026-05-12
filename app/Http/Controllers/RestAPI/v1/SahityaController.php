<?php

namespace App\Http\Controllers\RestAPI\v1;

use App\Http\Controllers\Controller;
use App\Models\BhagavadGitaDetails;
use App\Models\BhagavadGitaChapter;
use App\Models\RamShalaka;
use App\Models\Sahitya;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class SahityaController extends Controller
{

	// public function getBhagvadGeetaData(Request $request): JsonResponse
    // {
    //     $query = BhagavadGitaDetails::where('status', 1);
    //     if ($request->has('chapter') && $request->chapter != null) {
    //         $query = $query->where('chapter_id', $request->chapter);
    //         //dd($query);
    //     }

    //     if ($request->has('verse') && $request->verse != null) {
    //         $query = $query->where('verse', $request->verse);
    //     }

    //     $data = $query->orderBy('chapter_id', 'asc')->get()->groupBy('chapter_id');

    //     $response = [];
    //     foreach ($data as $chapter => $verses) {
    //         $chapterItem = BhagavadGitaChapter::find($chapter);
    //        // dd($chapterItem);
    //         $chapterName = $chapterItem->name;
    //         $chapterImage = $chapterItem->image;

    //         // Check if chapter image exists
    //         if ($chapterImage) {
    //             $chapterImageUrl = url('storage/app/public/sahitya/bhagavad-gita/' . $chapterImage);
    //         } else {
    //             $chapterImageUrl = null;
    //         }

    //         // Fetch chapter name translation
    //         $chapterTranslations = $chapterItem->translations()->pluck('value', 'key')->toArray();
    //         $hiChapterName = $chapterTranslations['name'] ?? null;

    //         $response[] = [
    //             'chapter' => $chapter,
    //             'chapter_name' => $chapterName,
    //             'hi_chapter_name' => $hiChapterName, // Hindi chapter name
    //             'chapter_image' => $chapterImageUrl,
    //             'verses' => $verses->map(function ($verse) {
    //                 $verseImage = $verse->image;

    //                 // Check if verse image exists
    //                 if ($verseImage) {
    //                     $verseImageUrl = url('storage/app/public/sahitya/bhagavad-gita/' . $verseImage);
    //                 } else {
    //                     $verseImageUrl = null;
    //                 }

    //                 // Fetch description translation
    //                 $descriptionTranslations = $verse->translations()->pluck('value', 'key')->toArray();
    //                 $hiDescription = $descriptionTranslations['description'] ?? null;

    //                 return [
    //                     'verse' => $verse->verse,
    //                     'description' => $verse->description,
    //                     'hi_description' => $hiDescription, // Hindi description
    //                     'verse_image' => $verseImageUrl,
    //                     'verse_data' => $verse->verse_data, 
    //                 ];
    //             })->all(),
    //         ];
    //     }

    //     return response()->json(['status' => 200, 'data' => $response]);
    // }
	public function getBhagvadGeetaData(Request $request): JsonResponse
	{
	    $cacheKey = 'bhagavad_geeta_data_' . ($request->chapter ?? 'all') . '_' . ($request->verse ?? 'all');

	    $response = cache()->remember($cacheKey, 1, function () use ($request) {
	        $chapters = BhagavadGitaChapter::with('translations')->get();
	        $response = [];

	        foreach ($chapters as $chapterItem) {
	            if ($request->has('chapter') && $request->chapter != $chapterItem->id) {
	                continue; 
	            }

	            $chapterImageUrl = $chapterItem->image ? url('storage/app/public/sahitya/bhagavad-gita/' . $chapterItem->image) : null;
	            $hiChapterName = $chapterItem->translations()->pluck('value', 'key')['name'] ?? null;

	            $versesQuery = BhagavadGitaDetails::where('chapter_id', $chapterItem->id)->where('status', 1);
	            if ($request->has('verse') && $request->verse != null) {
	                $versesQuery->where('verse', $request->verse);
	            }

	            $verses = $versesQuery->get()->map(function ($verse) {
	                return [
	                    'verse' => $verse->verse,
	                    'description' => $verse->description ?? null,
	                    'hi_description' => $verse->translations()->pluck('value', 'key')['description'] ?? null,
	                    'verse_image' => $verse->image ? url('storage/app/public/sahitya/bhagavad-gita/' . $verse->image) : null,
	                    'verse_data' => $verse->verse_data, // Accessing the model attribute
	                ];
	            })->all();
                $verseCount = BhagavadGitaDetails::where('chapter_id', $chapterItem->id)
                                 ->where('status', 1)
                                 ->count();

	            $response[] = [
	                'chapter' => $chapterItem->id,
	                'chapter_name' => $chapterItem->name,
	                'hi_chapter_name' => $hiChapterName ?? null, 
	                'chapter_image' => $chapterImageUrl,  
                    'verse_count' => $verseCount,
	                'verses' => $verses,  
	            ];
	        }

	        return ['status' => 200, 'data' => $response];
	    });

	    return response()->json($response);
	}

	public function sahitya()
    {
        $sahitya = Sahitya::where('status', 1)
            ->get();

        if ($sahitya->isNotEmpty()) {
            $data = $sahitya->map(function ($item) {
                $Translations = $item->translations()->pluck('value', 'key')->toArray();
                $hiName = $Translations['name'] ?? null;

                return [
                    'id' => $item->id,
                    'en_name' => $item->name,
                    'hi_name' => $hiName,
                    'image' => $item->image ? url('storage/app/public/sahitya/' . $item->image) : null,
                ];
            });

            if ($data->isEmpty()) {
                return response()->json([
                    'status' => 400,
                    'message' => 'No sahitya found.',
                ]);
            }

            return response()->json([
                'status' => 200,
                'data' => $data,
            ]);
        }

        return response()->json([
            'status' => 400,
            'message' => 'No sahitya available.',
        ]);
    }
    public function getAllChapterData(Request $request): JsonResponse
    {
        $cacheKey = 'bhagavad_geeta_data_all';
    
        $response = cache()->remember($cacheKey, 1, function () {
            $chapters = BhagavadGitaChapter::with('translations')->get();
            $response = [];
    
            foreach ($chapters as $chapterItem) {
                $chapterImageUrl = $chapterItem->image ? url('storage/app/public/sahitya/bhagavad-gita/' . $chapterItem->image) : null;
                $hiChapterName = $chapterItem->translations()->pluck('value', 'key')['name'] ?? null;
    
                $verses = BhagavadGitaDetails::where('chapter_id', $chapterItem->id)
                    ->where('status', 1)
                    ->get()
                    ->map(function ($verse) {
                        return [
                            'verse' => $verse->verse,
                            'description' => $verse->description ?? null,
                            'hi_description' => $verse->translations()->pluck('value', 'key')['description'] ?? null,
                            'verse_image' => $verse->image ? url('storage/app/public/sahitya/bhagavad-gita/' . $verse->image) : null,
                            'verse_data' => $verse->verse_data,
                        ];
                    })
                    ->all();
    
                $response[] = [
                    'chapter' => $chapterItem->id,
                    'chapter_name' => $chapterItem->name,
                    'hi_chapter_name' => $hiChapterName ?? null,
                    'chapter_image' => $chapterImageUrl,
                    'verses' => $verses,
                ];
            }
    
            return ['status' => 200, 'data' => $response];
        });
    
        return response()->json($response);
    }

    // public function getBhagvadGeetaChapters(Request $request): JsonResponse
    // {
    //     $chapters = BhagavadGitaChapter::all();

    //     $response = [];

    //     return response()->json(['status' => 200, 'data' => $chapters]);
    // }

    // public function getBhagvadGeetaVerses(Request $request): JsonResponse
    // {
    //    $query = BhagavadGitaDetails::where('status', 1);
    //     if($request->has('chapter') && $request->chapter != null){
    //         $query = $query->where('chapter_id', $request->chapter);
    //     }

    //     if($request->has('verse') && $request->verse != null){
    //         $query = $query->where('verse', $request->verse);
    //     }

    //     $query = $query->get();
    //     return response()->json(['status' => 200,'data' => $query]);
        
    // }

    public function ram_shalaka()
    {
        $ramShalaka = RamShalaka::where('status', 1)->with('translations')->get();
         
         foreach ($ramShalaka as $data) {
            $translations = $data->translations()->pluck('value', 'key')->toArray();
            $hiDescription = $translations['description'] ?? null;
            $data->hi_description = $hiDescription;
        }
        
        if ($ramShalaka) {
            return response()->json(['status' => 200, 'data' => $ramShalaka]);
        }
        return response()->json(['status' => 400, 'message' => 'data not found']);
    }

}