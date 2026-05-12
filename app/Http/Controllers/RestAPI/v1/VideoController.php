<?php

namespace App\Http\Controllers\RestAPI\v1;

use App\Http\Controllers\Controller;
use App\Models\Video;
use App\Models\VideoCategory;
use App\Models\VideoSubCategory;
use App\Models\VideoListType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{
  
//video

    public function getVideosBySubcategory($id)
    {
        $videos = Video::where('subcategory_id', $id)->get();

        if ($videos->isEmpty()) {
            return response()->json([
                'status' => 400,
                'message' => 'No videos found for the given subcategory id',
            ]);
        } else {
            return response()->json([
                'status' => 200,
                'data' => $videos,
            ]);
        }
    }




  public function getByListType(Request $request)
    {
        $listType = $request->query('list_type');
        if (!$listType) {
            return response()->json(['error' => 'list_type query parameter is required'], 400);
        }

        $videos = $this->videoService->getVideosByListType($listType);

        return response()->json($videos);
    }


    public function video_list_type()
    {
        $videolistType = VideoListType::all();
        if ($videolistType) {
            return response()->json(['status' => 200, 'videolistType' => $videolistType]);
        }
        return response()->json(['status' => 400, 'message' => 'video listType data not found']);
    }



    public function videoByplaylist(Request $request)
    {
        $subcategoryId = $request->query('subcategory_id');
        $listType = $request->query('listtype');
        $playlistName = $request->query('playlist_name');
        $status = $request->query('status');
        $urlStatus = $request->query('url_status');

        $query = Video::query();

        if ($subcategoryId) {
            $query->where('subcategory_id', $subcategoryId);
        }

        if ($listType) {
            $allowedListTypes = ['playlist', 'shorts', 'live', 'all'];
            if (!in_array($listType, $allowedListTypes)) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Invalid listtype specified',
                ]);
            }
            $query->where('list_type', $listType);
        }

        if ($playlistName) {
            $query->where('playlist_name', 'LIKE', "%$playlistName%");
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($urlStatus) {
            $query->where('url_status', $urlStatus);
        }

        $videos = $query->get();

        $uniqueListTypes = collect();
        if ($subcategoryId && !$listType) {
            $uniqueListTypes = Video::where('subcategory_id', $subcategoryId)
                                    ->pluck('list_type')
                                    ->unique();
        }

        if ($videos->isNotEmpty()) {
            $decodedVideos = $videos->map(function ($video) {
                $title = json_decode($video->title, true);
                $url = json_decode($video->url, true);
                $image = json_decode($video->image, true);
                $urlStatus = json_decode($video->url_status, true); 
                $videoData = [];
                foreach ($title as $key => $t) {
                    $videoData[] = [
                        'title' => $t,
                        'url' => $url[$key],
                        'image' => url('/storage/app/public/video-img/' . $image[$key]),
                        'url_status' => $urlStatus[$key] ?? null, 
                    ];
                }

                return [
                    'category_id' => $video->category_id,
                    'subcategory_id' => $video->subcategory_id,
                    'list_type' => $video->list_type,
                    'playlist_name' => $video->playlist_name,
                    'status' => $video->status,
                    'videos' => $videoData,
                ];
            });

            $response = [
                'status' => 200,
                'data' => $decodedVideos,
            ];

            if ($uniqueListTypes->isNotEmpty()) {
                $response['available_list_types'] = $uniqueListTypes;
            }

            return response()->json($response);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No videos found',
            ]);
        }
    }


    public function videoBylistType(Request $request)
    {
        $subcategoryId = $request->query('subcategory_id');
        $listType = $request->query('list_type');
        $status = $request->query('status');
        $urlStatus = $request->query('url_status');

        $query = Video::query();

        if ($subcategoryId) {
            $query->where('subcategory_id', $subcategoryId);
        }

        if ($listType) {
            $query->where('list_type', $listType);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($urlStatus) {
            $query->where('url_status', $urlStatus);
        }

        $videos = $query->get();

        $uniqueListTypes = collect();

        if ($subcategoryId && !$listType) {
            $uniqueListTypes = Video::where('subcategory_id', $subcategoryId)
                                    ->pluck('list_type')
                                    ->unique();
        }

        $uniqueListTypes = $uniqueListTypes->filter(function ($type) {
            return !is_null($type);
        });

        if ($videos->isNotEmpty()) {
            $decodedVideos = $videos->map(function ($video) {
                $title = json_decode($video->title, true);
                $url = json_decode($video->url, true);
                $image = json_decode($video->image, true);
                $urlStatus = json_decode($video->url_status, true); 
                $videoData = [];
                foreach ($title as $key => $t) {
                    $videoData[] = [
                        'title' => $t,
                        'url' => $url[$key],
                        'image' => url('/storage/app/public/video-img/' . $image[$key]),
                        'url_status' => $urlStatus[$key] ?? null, 
                    ];
                }

                return [
                    'category_id' => $video->category_id,
                    'subcategory_id' => $video->subcategory_id,
                    'list_type' => $video->list_type,
                    'playlist_name' => $video->playlist_name,
                    'status' => $video->status,
                    'videos' => $videoData,
                ];
            });

            $response = [
                'status' => 200,
                'data' => $decodedVideos,
            ];


            if ($uniqueListTypes->isNotEmpty() && !$listType) {
                $response['available_list_types'] = $uniqueListTypes->values()->all(); 
            }

            return response()->json($response);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No videos found',
            ]);
        }
    }

}


http://127.0.0.1:8000/api/v1/video?subcategory_id=1&list_type=playlist

