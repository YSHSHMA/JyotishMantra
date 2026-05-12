<?php

namespace App\Http\Controllers\RestAPI\v1;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\NotificationSeen;
use Carbon\Carbon;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // public function list(Request $request)
    // {

    //     $notification_data = Notification::active()->where(['sent_to'=>'customer']);

    //     $notification = $notification_data->with('notificationSeenBy')
    //         ->latest()->paginate($request['limit'], ['*'], 'page', $request['offset']);

    //     return [
    //         'total_size' => $notification->total(),
    //         'limit' => (int)$request['limit'],
    //         'offset' => (int)$request['offset'],
    //         'new_notification' => $notification_data->whereDoesntHave('notificationSeenBy')->count(),
    //         'notification' => $notification->items()
    //     ];
    // }

    public function list(Request $request)
    {
        $notification_data = Notification::active()->where(['sent_to' => 'customer']);

        $notification = $notification_data->with('notificationSeenBy')
            ->latest()
            ->paginate($request['limit'], ['*'], 'page', $request['offset']);

        // Slug fetch logic
        $notificationItems = $notification->items();

        foreach ($notificationItems as $item) {
            $slug = null;
            if (!empty($item->service_id) && !empty($item->type)) {
                try {
                    switch ($item->type) {
                        case 'puja':
                            $slug = \App\Models\Service::where('id', $item->service_id)->value('slug');
                            break;

                        case 'vip':
                        case 'anushthan':
                            $slug = \App\Models\Vippooja::where('id', $item->service_id)->value('slug');
                            break;

                        case 'chadhava':
                            $slug = \App\Models\Chadhava::where('id', $item->service_id)->value('slug');
                            break;

                        case 'offlinepuja':
                            $slug = \App\Models\PoojaOffline::where('id', $item->service_id)->value('slug');
                            break;

                        case 'consultancy':
                            $slug = \App\Models\Service::where('id', $item->service_id)->value('slug');
                            break;

                        case 'event':
                            $slug = \App\Models\Events::where('id', $item->service_id)->value('slug');
                            break;

                        case 'darshan':
                            $slug = \App\Models\Temple::where('id', $item->service_id)->value('slug');
                            break;

                        case 'tour':
                            $slug = \App\Models\TourVisits::where('id', $item->service_id)->value('slug');
                            break;

                        case 'donation':
                            $slug = \App\Models\DonateAds::where('id', $item->service_id)->value('slug');
                            break;

                        case 'product':
                            $slug = \App\Models\Product::where('id', $item->service_id)->value('slug');
                            break;
                    }
                } catch (\Exception $ex) {
                    \Log::error("Slug fetch failed for notification ID {$item->id}: " . $ex->getMessage());
                }
            }

            $item->slug = $slug ?? ''; // dynamically add slug field
        }

        return [
            'total_size' => $notification->total(),
            'limit' => (int) $request['limit'],
            'offset' => (int) $request['offset'],
            'new_notification' => $notification_data->whereDoesntHave('notificationSeenBy')->count(),
            'notification' => $notificationItems
        ];
    }

    public function notification_seen(Request $request)
    {
        $user = $request->user();
        NotificationSeen::updateOrInsert(['user_id' => $user->id, 'notification_id' => $request->id],[
            'created_at' => Carbon::now(),
        ]);

        $notification_count = Notification::active()
            ->where('sent_to', 'customer')
            ->whereDoesntHave('notificationSeenBy')
            ->count();

        return [
            'notification_count' => $notification_count,
        ];
    }
}
