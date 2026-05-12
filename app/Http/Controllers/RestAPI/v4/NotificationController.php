<?php

namespace App\Http\Controllers\RestAPI\v4;

use App\Http\Controllers\Controller;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function get_notifications()
    {
        try {
            return response()->json(Notification::active()->orderBy('id','DESC')->get(), 200);
        } catch (\Exception $e) {
            return response()->json([], 200);
        }
    }
}
