<?php

namespace App\Http\Controllers\RestAPI\v1;

use App\Http\Controllers\Controller;
use App\Models\User_log;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class UserLogsController extends Controller
{

	public function logRequest(Request $request): JsonResponse
{
    $user = Auth::guard('api')->user();

    if (!$user || empty($user->id)) {
        return response()->json(['message' => 'Unauthorized']);
    }

    $type = $request->input('type');
    $location = $request->input('location');
    $title = $request->input('title');

    if (empty($type) || empty($location) || empty($title)) {
        return response()->json(['status' => 400, 'message' => 'Invalid request parameters'], 400);
    }

    $log = new User_log();
    $log->user_id = $user->id; 
    $log->type = $type;
    $log->location = $location;
    $log->title = $title;
    $log->save();

    return response()->json(['status' => 200, 'message' => 'Log created successfully']);
}


}