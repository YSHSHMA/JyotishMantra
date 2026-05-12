<?php

namespace App\Http\Controllers\Admin\Notification;

use App\Contracts\Repositories\NotificationRepositoryInterface;
use App\Enums\ViewPaths\Admin\Notification;
use App\Enums\WebConfigKey;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\NotificationRequest;
use App\Services\NotificationService;
use App\Traits\FileManagerTrait;
use App\Traits\PushNotificationTrait;
use App\Utils\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Vippooja;
use App\Models\Chadhava;
use App\Models\TourVisits;
use App\Models\Events;
use App\Models\Service;
use App\Models\Temple;
use App\Models\PoojaOffline;
use App\Models\DonateAds;
use App\Models\Product;




class NotificationController extends BaseController
{
    use PushNotificationTrait,FileManagerTrait{
        delete as deleteFile;
    }

    /**
     * @param NotificationRepositoryInterface $notificationRepo
     * @param NotificationService $notificationService
     */
    public function __construct(
        private readonly NotificationRepositoryInterface $notificationRepo,
        private readonly NotificationService $notificationService,
    )
    {

    }

    /**
     * @param Request|null $request
     * @param string|null $type
     * @return View|Collection|LengthAwarePaginator|callable|RedirectResponse|null
     */
    public function index(?Request $request, string $type = null): View|Collection|LengthAwarePaginator|null|callable|RedirectResponse
    {
       return $this->getNotificationView(request: $request);
    }

    /**
     * @param $request
     * @return View
     */
    public function getNotificationView($request):View
    {
        $searchValue = $request['searchValue'];
        $notifications = $this->notificationRepo->getListWhere(
            orderBy: ['id' => 'desc'],
            searchValue: $searchValue,
            filters: ['sent_to'=>'customer'],
            dataLimit: getWebConfig(WebConfigKey::PAGINATION_LIMIT),
        );
        $Pooja   = Service::where('status', 1)->where('product_type', 'pooja')->get();
        $vipPooja   = Vippooja::where('status', 1)->where('is_anushthan', 0)->get();
        $anushthan = Vippooja::where('status', 1)->where('is_anushthan', 1)->get();
        $chadhava  = Chadhava::where('status', 1)->get();
        $offlinepuja  = PoojaOffline::where('status', 1)->get();
        $consultancy  = Service::where('status', 1)->where('product_type', 'counselling')->get();
        $event  = Events::where('status', 1)->get();
        $darshan  = Temple::where('status', 1)->get();
        $product      = Product::where('status', 1)->get();
        $donation  = DonateAds::where('status', 1)->get();
        $tour  = TourVisits::where('status', 1)->get();
        return view(Notification::INDEX[VIEW],compact('searchValue','notifications','Pooja', 'vipPooja', 'anushthan', 'chadhava',
        'offlinepuja', 'consultancy', 'event', 'darshan', 'donation', 'product','tour'));

    }

    /**
     * @param NotificationRequest $request
     * @return RedirectResponse
     */
    public function add(NotificationRequest $request):RedirectResponse
    {
        $notification = $this->notificationRepo->add(data: $this->notificationService->getNotificationAddData(request: $request));
        try {
            $this->sendPushNotificationToTopic($notification);
        } catch (\Exception $e) {
            Toastr::warning(translate('push_notification_failed'));
        }

        Toastr::success(translate('notification_sent_successfully'));
        Helpers::editDeleteLogs('Notification','Send Notification','Insert');
        return redirect()->back();
    }

    /**
     * @param string|int $id
     * @return View
     */
    public function getUpdateView(string|int $id):View
    {
        $notification = $this->notificationRepo->getFirstWhere(params: ['id'=>$id]);
        $Pooja        = Service::where('status', 1)->where('product_type', 'pooja')->get();
        $vipPooja     = Vippooja::where('status', 1)->where('is_anushthan', 0)->get();
        $anushthan    = Vippooja::where('status', 1)->where('is_anushthan', 1)->get();
        $chadhava     = Chadhava::where('status', 1)->get();
        $offlinepuja  = PoojaOffline::where('status', 1)->get();
        $consultancy  = Service::where('status', 1)->where('product_type', 'counselling')->get();
        $event        = Events::where('status', 1)->get();
        $darshan      = Temple::where('status', 1)->get();
        $product      = Product::where('status', 1)->get();
        $donation  = DonateAds::where('status', 1)->get();
        $tour         = TourVisits::where('status', 1)->get();

        return view(Notification::UPDATE[VIEW], compact(
            'notification', 'Pooja', 'vipPooja', 'anushthan', 'chadhava',
            'offlinepuja', 'consultancy', 'event', 'darshan', 'tour' ,'product', 'donation'
        ));
    }

    /**
     * @param NotificationRequest $request
     * @param string|int $id
     * @return RedirectResponse
     */
    public function update(NotificationRequest $request, string|int $id):RedirectResponse
    {
        $notification = $this->notificationRepo->getFirstWhere(params: ['id'=>$id]);
        $this->notificationRepo->update(id: $notification['id'],
            data: $this->notificationService->getNotificationUpdateData(
                request: $request, notificationImage: $notification['image']
            )
        );
        Toastr::success(translate('notification_updated_successfully'));
        Helpers::editDeleteLogs('Notification','Send Notification','Update');
        return redirect()->route(Notification::INDEX[ROUTE]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function updateStatus(Request $request):JsonResponse
    {
        $notification = $this->notificationRepo->getFirstWhere(params: ['id'=>$request['id']]);
        $this->notificationRepo->update(id:$notification['id'],data: ['status'=> $request['status']]);
        return response()->json($request['status']);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function delete(Request $request):JsonResponse
    {
        $notification = $this->notificationRepo->getFirstWhere(params: ['id'=>$request['id']]);
        $this->deleteFile('/notification/'.$notification['image']);
        $this->notificationRepo->delete(params: ['id' => $notification['id']]);
        Helpers::editDeleteLogs('Notification','Send Notification','Delete');
        return response()->json();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function resendNotification(Request $request):JsonResponse
    {
        $notification = $this->notificationRepo->getFirstWhere(params: ['id'=>$request['id']]);
        $data = [];
        try {
            $this->sendPushNotificationToTopic($notification);
            $count = $notification['notification_count'] += 1;
            $this->notificationRepo->update(id:$notification['id'],data: ['notification_count' => $count] );
            $data['success'] = true;
            $data['message'] = translate("push_notification_successfully");
        } catch (\Exception $e) {
            $data['success'] = false;
            $data['message'] = translate("push_notification_failed");
        }
        return response()->json($data);
    }
}
