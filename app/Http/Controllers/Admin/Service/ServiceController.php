<?php

namespace App\Http\Controllers\Admin\Service;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\Foundation\Application;
use App\Http\Requests\Admin\ServicesAddRequest;
use App\Http\Requests\ServiceUpdateRequest;
use App\Enums\ViewPaths\Admin\ServiceDetails;
use App\Enums\WebConfigKey;
use App\Contracts\Repositories\CategoryRepositoryInterface;
use App\Contracts\Repositories\AttributeRepositoryInterface;
use App\Contracts\Repositories\OfflinePoojaCategoryRepositoryInterface;
use App\Contracts\Repositories\OfflinePoojaRefundPolicyRepositoryInterface;
use App\Contracts\Repositories\OfflinePoojaRepositoryInterface;
use App\Contracts\Repositories\OfflinePoojaScheduleRepositoryInterface;
use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Contracts\Repositories\ServiceRepositoryInterface;
use App\Contracts\Repositories\ProductRepositoryInterface;
use App\Contracts\Repositories\PackageRepositoryInterface;
use App\Enums\ViewPaths\Admin\SubCategory;
use App\Models\Astrologer\Astrologer;
use App\Models\Category;
use App\Models\Product;
use App\Models\Package;
use App\Models\Vippooja;
use App\Models\Service;
use App\Services\ServiceAdd;
use Brian2694\Toastr\Facades\Toastr;
use mysql_xdevapi\Exception;
use App\Traits\FileManagerTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\VIPAddService;
use App\Http\Requests\Admin\VIPAddRequest;
use App\Http\Requests\VIPUpdateRequest;
use App\Contracts\Repositories\VIPRepositoryInterface;
use App\Events\ProductRequestStatusUpdateEvent;
use App\Http\Requests\Admin\OfflinePoojaAddRequest;
use App\Http\Requests\Admin\OfflinePoojaCategoryAddRequest;
use App\Http\Requests\Admin\OfflinePoojaCategoryUpdateRequest;
use App\Http\Requests\Admin\OfflinePoojaRefundPolicyAddRequest;
use App\Http\Requests\Admin\OfflinePoojaRefundPolicyUpdateRequest;
use App\Http\Requests\Admin\OfflinePoojaScheduleAddRequest;
use App\Http\Requests\Admin\OfflinePoojaScheduleUpdateRequest;
use App\Http\Requests\Admin\OfflinePoojaUpdateRequest;
use App\Http\Requests\Admin\ProductDenyRequest;
use App\Models\Cities;
use App\Models\CityDetail;
use App\Models\OfflinepoojaCategory;
use App\Models\Temple;
use App\Services\OfflinePoojaCategoryService;
use App\Services\OfflinePoojaRefundPolicyService;
use App\Services\OfflinePoojaScheduleService;
use App\Services\OfflinePoojaService;
use App\Utils\Helpers;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\F;

class ServiceController  extends BaseController
{

    use FileManagerTrait {
        delete as deleteFile;
        update as updateFile;
    }

    public function __construct(
        private readonly OfflinePoojaCategoryRepositoryInterface     $offlinePoojaCategoryRepo,
        private readonly ServiceRepositoryInterface          $serviceRepo,
        private readonly CategoryRepositoryInterface         $categoryRepo,
        private readonly AttributeRepositoryInterface        $attributeRepo,
        private readonly TranslationRepositoryInterface      $translationRepo,
        private readonly ProductRepositoryInterface          $productRepo,
        private readonly PackageRepositoryInterface         $packageRepo,
        private readonly VIPRepositoryInterface              $vipRepo,
        private readonly OfflinePoojaRepositoryInterface     $offlinePoojaRepo,
        private readonly OfflinePoojaRefundPolicyRepositoryInterface     $offlinePoojaRefundPolicyRepo,
        private readonly OfflinePoojaScheduleRepositoryInterface     $offlinePoojaScheduleRepo,

    ) {}

    public function index(Request|null $request, string $type = null): View
    {
        return $this->getList($request);
    }

    public function getList(Request $request): Application|Factory|View
    {
        $categories = $this->categoryRepo->getListWhere(filters: ['position' => 0], dataLimit: 'all');
        $subCategory = $this->categoryRepo->getFirstWhere(params: ['id' => $request['sub_category_id']]);
        $subSubCategory = $this->categoryRepo->getFirstWhere(params: ['id' => $request['sub_sub_category_id']]);
        $pandit = Astrologer::where('status', 1)->where('primary_skills', 3)->where('type', 'in house')->get();
        $services = $this->serviceRepo->getListWhere(orderBy: ['id' => 'desc'], searchValue: $request->get('searchValue'), filters: ['product_type' => 'pooja'], dataLimit: getWebConfig(name: 'pagination_limit'));
        $prashadamList = $this->productRepo->getListWhere(filters: ['category_id' => 53, 'position' => 0, 'user_id' => 14], dataLimit: 'all');
        // dd($prashadamList);
        return view(ServiceDetails::LIST[VIEW], compact('services', 'subCategory', 'prashadamList'));
    }

    // Singe Pooja View
    public function getViewService(string $addedBy, string|int $id): View
    {
        $serviceActive = $this->productRepo->getFirstWhereActive(params: ['id' => $id]);
        $relations =  ['category', 'orderDetails', 'translations', 'pandit', 'product', 'package'];
        $service = $this->serviceRepo->getFirstWhereWithoutGlobalScope(params: ['id' => $id], relations: $relations);
        // dd($service);
        return view(ServiceDetails::VIEW[VIEW], compact('service', 'addedBy', 'serviceActive'));
    }


    public function getAddView(): View
    {
        // $categories = $this->categoryRepo->getListWhere(filters: ['position' => 0], dataLimit: 'all');
        $subCategories = Category::where('parent_id', 33)->get();
        $productes = $this->productRepo->getListWhere(filters: ['category_id' => 33, 'position' => 0], dataLimit: 'all');
        $packages = $this->packageRepo->getListWhere(filters: ['category_id' => 33, 'position' => 0], dataLimit: 'all');
        $pandit = Astrologer::where('status', 1)->where('primary_skills', 3)->where('type', 'in house')->get();
        $digitalProductSetting = getWebConfig(name: 'digital_product');
        $attributes = $this->attributeRepo->getList(orderBy: ['name' => 'desc'], dataLimit: 'all');
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        // dd($packages);
        return view(ServiceDetails::ADD[VIEW], compact('pandit', 'packages', 'productes', 'subCategories', 'digitalProductSetting', 'attributes', 'languages', 'defaultLanguage'));
    }

    public function add(ServicesAddRequest $request, ServiceAdd $serviceAdd): RedirectResponse
    {
        // dd($request->input());
        $dataArray = $serviceAdd->getAddServicesData($request, addedBy: 'admin');
        // dd($dataArray);
        $savedService = $this->serviceRepo->add(data: $dataArray);
        $this->serviceRepo->addServiceTags(request: $request, service: $savedService);
        // dd($savedService);
        $this->translationRepo->add(request: $request, model: 'App\Models\Service', id: $savedService->id);
        Toastr::success(translate('New_Pooja_service_added_successfully'));
        Helpers::editDeleteLogs('Pooja', 'Pooja', 'Insert');
        return redirect()->route('admin.service.list');
    }

    // Service Status On/Off
    public function updateStatus(Request $request): JsonResponse
    {
        $data = [
            'status' => $request->get('status', 0),
        ];
        $this->serviceRepo->update(id: $request['id'], data: $data);
        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }
    // Services Update Data
    public function getUpdateView(string|int $id): View|RedirectResponse
    {
        $service = $this->serviceRepo->getFirstWhere(params: ['id' => $id], relations: ['translations']);
        $subCategories = Category::where('parent_id', 33)->get();
        $productes = $this->productRepo->getListWhere(filters: ['category_id' => 33, 'position' => 0], dataLimit: 'all');
        $packages = $this->packageRepo->getListWhere(filters: ['category_id' => 33, 'position' => 0], dataLimit: 'all');
        $pandit = Astrologer::where('status', 1)->get();
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        return view(ServiceDetails::UPDATE[VIEW], compact('pandit', 'packages', 'service', 'languages', 'defaultLanguage', 'subCategories', 'productes'));
    }

    public function update(ServiceUpdateRequest $request, ServiceAdd $serviceAdd, $id): JsonResponse|RedirectResponse
    {
        $service = $this->serviceRepo->getFirstWhereWithoutGlobalScope(params: ['id' => $id], relations: ['translations']);
        $dataArray = $serviceAdd->getUpdateServiceData(request: $request, service: $service, updateBy: 'admin');
        // dd($dataArray);
        $this->serviceRepo->update(id: $id, data: $dataArray);
        $this->serviceRepo->addServiceTags(request: $request, service: $service);
        $this->translationRepo->update(request: $request, model: 'App\Models\Service', id: $id);

        Toastr::success(translate('Pooja_service_updated_successfully'));
        Helpers::editDeleteLogs('Pooja', 'Pooja', 'Update');
        return redirect()->route('admin.service.list');
    }
    // Services Delete
    public function delete(string|int $id, ServiceAdd $serviceAdd): RedirectResponse
    {
        $service = $this->serviceRepo->getSerFirstWhere(params: ['id' => $id]);
        if ($service) {
            $this->translationRepo->delete(model: 'App\Models\Service', id: $id);
            $serviceAdd->deleteImages(service: $service);
            $this->serviceRepo->delete(params: ['id' => $id]);
            Toastr::success(translate('Pooja_service_removed_successfully'));
            Helpers::editDeleteLogs('Pooja', 'Pooja', 'Delete');
        } else {
            Toastr::error(translate('invalid_product'));
        }

        return back();
    }
    //Delete Product Images
    public function deleteImage(Request $request, ServiceAdd $serviceAdd): RedirectResponse
    {
        $this->deleteFile(filePath: '/pooja/' . $request['image']);
        $service = $this->serviceRepo->getFirstWhere(params: ['id' => $request['id']]);

        if (count(json_decode($service['images'])) < 2) {
            Toastr::warning(translate('you_can_not_delete_all_images'));
            return back();
        }
        $imageProcessing = $serviceAdd->deleteImage(request: $request, service: $service);

        $updateData = [
            'images' => json_encode($imageProcessing['images']),
        ];
        $this->serviceRepo->update(id: $request['id'], data: $updateData);

        Toastr::success(translate('Pooja_service_image_removed_successfully'));
        return back();
    }
    // Get Category
    public function getCategories(Request $request, ServiceAdd $ServiceAdd): JsonResponse
    {
        $parentId = $request['parent_id'];
        $filter = ['parent_id' => $parentId];
        $categories = $this->categoryRepo->getListWhere(filters: $filter, dataLimit: 'all');
        $dropdown = $service->getCategoryDropdown(request: $request, categories: $categories);
        $childCategories = '';
        if (count($categories) == 1) {
            $subCategories = $this->categoryRepo->getListWhere(filters: ['parent_id' => $categories[0]['id']], dataLimit: 'all');
            $childCategories = $service->getCategoryDropdown(request: $request, categories: $subCategories);
        }

        return response()->json([
            'select_tag' => $dropdown,
            'sub_categories' => count($categories) == 1 ? $childCategories : '',
        ]);
    }

    // Serice Reject and Approve
    public function deny(ProductDenyRequest $request): JsonResponse
    {
        $dataArray = [
            'request_status' => 2,
            'status' => 0,
            'denied_note' => $request['denied_note'],
        ];
        $this->serviceRepo->update(id: $request['id'], data: $dataArray);
        $service = $this->serviceRepo->getFirstWhereWithoutGlobalScope(params: ['id' => $request['id']], relations: ['translations']);
        $vendor = $this->sellerRepo->getFirstWhere(params: ['id' => $service['user_id']]);
        if ($vendor['cm_firebase_token']) {
            ProductRequestStatusUpdateEvent::dispatch('product_request_rejected_message', 'seller', $vendor['app_language'] ?? getDefaultLanguage(), $vendor['cm_firebase_token']);
        }
        return response()->json(['message' => translate('product_request_denied') . '.']);
    }

    public function approveStatus(Request $request): JsonResponse
    {
        $service = $this->serviceRepo->getFirstWhereWithoutGlobalScope(params: ['id' => $request['id']], relations: ['translations']);
        $dataArray = [
            'request_status' => ($product['request_status'] == 0) ? 1 : 0
        ];
        $this->serviceRepo->update(id: $request['id'], data: $dataArray);
        $vendor = $this->sellerRepo->getFirstWhere(params: ['id' => $service['user_id']]);
        if ($vendor['cm_firebase_token']) {
            ProductRequestStatusUpdateEvent::dispatch('product_request_approved_message', 'seller', $vendor['app_language'] ?? getDefaultLanguage(), $vendor['cm_firebase_token']);
        }
        return response()->json(['message' => translate('product_request_approved') . '.']);
    }

    // counselling function 
    public function counselling_index(Request|null $request, string $type = null): View
    {
        return $this->counselling_getList($request);
    }

    public function counselling_getList(Request $request): Application|Factory|View
    {
        $counselling = $this->serviceRepo->getListWhere(orderBy: ['id' => 'desc'], searchValue: $request->get('searchValue'), filters: ['product_type' => 'counselling'], dataLimit: getWebConfig(name: 'pagination_limit'));
        // dd($counselling->toArray());
        return view(ServiceDetails::COUNSELLING_LIST[VIEW], compact('counselling'));
    }

    public function counselling_getAddView(): View
    {
        $subCategories = Category::where('parent_id', 39)->get();
        $productes = $this->productRepo->getListWhere(filters: ['category_id' => 39, 'position' => 0], dataLimit: 'all');
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        return view(ServiceDetails::COUNSELLING_ADD[VIEW], compact('subCategories', 'productes', 'languages', 'defaultLanguage'));
    }

    public function counselling_add(ServicesAddRequest $request, ServiceAdd $serviceAdd): RedirectResponse
    {

        $dataArray = $serviceAdd->getAddCounsellingData($request, addedBy: 'admin');
        // dd($dataArray);
        $savedService = $this->serviceRepo->add(data: $dataArray);
        $this->serviceRepo->addServiceTags(request: $request, service: $savedService);
        $this->translationRepo->add(request: $request, model: 'App\Models\Service', id: $savedService->id);
        Toastr::success(translate('New_counsultancy_added_successfully'));
        Helpers::editDeleteLogs('Consultation', 'Consultation', 'Insert');
        return redirect()->route('admin.service.counselling.list');
    }

    public function counselling_getUpdateView(string|int $id): View|RedirectResponse
    {
        $service = $this->serviceRepo->getFirstWhere(params: ['id' => $id], relations: ['translations']);
        // dd($service);
        $products = Product::where('category_id', 39)->get();
        $subCategories = Category::where('parent_id', 39)->get();
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        return view(ServiceDetails::COUNSELLING_UPDATE[VIEW], compact('service', 'products', 'languages', 'defaultLanguage', 'subCategories'));
    }

    public function counselling_update(ServiceUpdateRequest $request, ServiceAdd $serviceAdd, $id): JsonResponse|RedirectResponse
    {
        $service = $this->serviceRepo->getFirstWhereWithoutGlobalScope(params: ['id' => $id], relations: ['translations']);
        $dataArray = $serviceAdd->getUpdateCounsellingData(request: $request, service: $service, updateBy: 'admin');
        // dd($dataArray);
        $this->serviceRepo->update(id: $id, data: $dataArray);
        $this->serviceRepo->addServiceTags(request: $request, service: $service);
        $this->translationRepo->update(request: $request, model: 'App\Models\Service', id: $id);

        Toastr::success(translate('counsultancy_updated_successfully'));
        Helpers::editDeleteLogs('Consultation', 'Consultation', 'Update');
        return redirect()->route('admin.service.counselling.list');
    }

    public function counselling_delete(string|int $id, ServiceAdd $serviceAdd): RedirectResponse
    {
        $service = $this->serviceRepo->getSerFirstWhere(params: ['id' => $id]);
        if ($service) {
            $this->translationRepo->delete(model: 'App\Models\Service', id: $id);
            $serviceAdd->deleteImages(service: $service);
            $this->serviceRepo->delete(params: ['id' => $id]);
            Toastr::success(translate('counsultancy_removed_successfully'));
            Helpers::editDeleteLogs('Consultation', 'Consultation', 'Delete');
        } else {
            Toastr::error(translate('invalid_data'));
        }

        return back();
    }

    //Delete Product Images
    public function counsellingDeleteImage(Request $request, ServiceAdd $serviceAdd): RedirectResponse
    {
        $this->deleteFile(filePath: '/pooja/' . $request['image']);
        $service = $this->serviceRepo->getFirstWhere(params: ['id' => $request['id']]);

        if (count(json_decode($service['images'])) < 2) {
            Toastr::warning(translate('you_can_not_delete_all_images'));
            return back();
        }
        $imageProcessing = $serviceAdd->deleteImage(request: $request, service: $service);

        $updateData = [
            'images' => json_encode($imageProcessing['images']),
        ];
        $this->serviceRepo->update(id: $request['id'], data: $updateData);

        Toastr::success(translate('counsultancy_image_removed_successfully'));
        return back();
    }

    public function counselling_updateStatus(Request $request): JsonResponse
    {
        $data = [
            'status' => $request->get('status', 0),
        ];
        $this->serviceRepo->update(id: $request['id'], data: $data);
        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }


    // Package GET Controller Service
    public function getPackagesDropdown(Request $request)
    {
        $selectedPackageIds = $request->input('packageIds', []);
        $packages = Package::all();
        $html = '';
        $html .= '<select name="packages_id[]" class="form-control" id="package_id">';
        foreach ($packages as $package) {
            $isDisabled = in_array($package->id, $selectedPackageIds) ? 'disabled' : '';
            $html .= '<option value="' . $package->id . '" ' . $isDisabled . '>
                                ' . $package->title . '
                            </option>';
        }
        $html .= '</select>';
        return response()->json(['status' => 200, 'html' => $html]);
    }


    // shadule Add the Pooja 
    public function pooja_schedule(Request|null $request, string $type = null): View
    {
        return $this->getSchedule($request, $type);
    }

    public function getSchedule(Request $request, $id): Application|Factory|View
    {
        $event = Service::where('id', $id)->first();
        if (!$event) {
            Toastr::success(translate('Event not found'));
        }
        $scheduleData = json_decode($event->schedule);

        if (is_array($scheduleData) || is_object($scheduleData)) {
            $schedulesJson = [];
            foreach ($scheduleData as $schedule) {
                $dates = explode(',', $schedule->schedule);

                foreach ($dates as $date) {
                    $formattedSchedule = [
                        'title' => $event->name,
                        'start' => $date . 'T' . $schedule->schedule_time
                    ];
                    $schedulesJson[] = $formattedSchedule;
                }
            }
            $schedulesJson = json_encode($schedulesJson, JSON_PRETTY_PRINT);
        } else {

            $schedulesJson = json_encode([]);
        }

        return view(ServiceDetails::SCHEDULE[VIEW], compact('event', 'schedulesJson'));
    }

    public function ScheduleDelete(Request $request)
    {
        $eventId = $request->input('id');
        $scheduleDate = $request->input('schedule');
        $scheduleTime = $request->input('schedule_time');
        $service = Service::find($eventId);
        // dd($eventId,$service);
        if ($service) {
            $schedules = json_decode($service->schedule, true);
            if (!empty($schedules)) {
                foreach ($schedules as $key => $schedule) {
                    if ($schedule['schedule'] == $scheduleDate && $schedule['schedule_time'] == $scheduleTime) {
                        unset($schedules[$key]);
                    }
                }
                $schedules = array_values($schedules);
                if (empty($schedules)) {
                    $service->schedule = null;
                } else {
                    $service->schedule = json_encode($schedules);
                }
                $service->save();

                return response()->json(['message' => 'Schedule date and time deleted successfully!'], 200);
            } else {
                return response()->json(['message' => 'No schedules found to delete.'], 404);
            }
        } else {
            return response()->json(['message' => 'Service not found!'], 404);
        }
    }

    public function pooja_prashad(Request $request)
    {
        $prashad = Service::where('id', $request->pooja_id)->update(['prashadam_id' => $request->prashadam_id]);
        if ($prashad) {
            Toastr::success(translate('Prashad added successfully!'));
        }
        return back();
    }

    public function event_Update(Request $request, $id): JsonResponse|RedirectResponse
    {
        // dd($request->all());
        $event = Service::find($id);
        $eventData = json_decode($event->schedule, true);
        $schedule = [];
        $schedule = $request->input('schedule');
        foreach ($schedule as $key => $date) {
            $eventData[] = [
                'schedule' => $date,
                'schedule_time' => $request->schedule_time[$key]
            ];
        }
        // dd($eventData);
        $sortedSchedule = collect($eventData)->sortBy(function ($item) {
            // dd($item);
            return strtotime($item['schedule']);
        })->values()->all();
        // dd($sortedSchedule);
        $event->schedule = json_encode($sortedSchedule);


        $update = $event->save();
        // dd($update);
        if ($update) {
            Toastr::success(translate('pooja_event_date_and time_successfully'));
            Helpers::editDeleteLogs('Pooja', 'Event', 'Update');
            return redirect()->back();
        } else {
            return redirect()->back();
        }
    }

    // VIP Pooja Controller 22/07/2024
    // VIP function 
    public function vip_index(Request|null $request, string $type = null): View
    {
        return $this->vip_getList($request);
    }

    public function vip_getList(Request $request): Application|Factory|View
    {
        $vippooja = $this->vipRepo->getListWhere(orderBy: ['id' => 'desc'], searchValue: $request->get('searchValue'), dataLimit: getWebConfig(name: 'pagination_limit'));
        //$vippooja->load('products');  
        $prashadamList = $this->productRepo->getListWhere(filters: ['category_id' => 53, 'position' => 0, 'user_id' => 14], dataLimit: 'all');
        return view(ServiceDetails::VIP_LIST[VIEW], compact('vippooja', 'prashadamList'));
    }

    public function vip_getView(string $addedBy, string|int $id): View
    {
        $vipActive = $this->productRepo->getFirstWhereActive(params: ['id' => $id]);
        $relations =  ['orderDetails', 'translations', 'pandit', 'product', 'packages'];
        $vippooja = $this->vipRepo->getFirstWhereWithoutGlobalScope(params: ['id' => $id], relations: $relations);
        //   dd($vippooja);
        return view(ServiceDetails::VIP_VIEW[VIEW], compact('vippooja', 'addedBy', 'vipActive'));
    }

    public function vip_getAddView(): View
    {
        $productes = $this->productRepo->getListWhere(filters: ['category_id' => 33, 'position' => 0], dataLimit: 'all');
        $packages = $this->packageRepo->getListWhere(filters: ['category_id' => 33, 'position' => 0, 'package_id' => [5, 6, 7, 8]], dataLimit: 'all');
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        return view(ServiceDetails::VIP_ADD[VIEW], compact('productes', 'packages', 'languages', 'defaultLanguage'));
    }
    public function vip_add(VIPAddRequest $request, VIPAddService $vipAdd): RedirectResponse
    {
        $dataArray = $vipAdd->getAddVIPData($request, addedBy: 'admin');
        $savedVIP = $this->vipRepo->add(data: $dataArray);
        $this->vipRepo->addVIPTag(request: $request, vip: $savedVIP);
        $this->translationRepo->add(request: $request, model: 'App\Models\Vippooja', id: $savedVIP->id);
        Toastr::success(translate('New_VIP_Pooja_added_successfully'));
        Helpers::editDeleteLogs('VIP', 'VIP', 'Insert');
        return redirect()->route('admin.service.vip.list');
    }

    public function vip_getUpdateView(string|int $id): View|RedirectResponse
    {
        $vip = $this->vipRepo->getFirstWhere(params: ['id' => $id], relations: ['translations']);
        $productes = $this->productRepo->getListWhere(filters: ['category_id' => 33, 'position' => 0], dataLimit: 'all');
        $packages = $this->packageRepo->getListWhere(filters: ['category_id' => 33, 'position' => 0, 'package_id' => [5, 6, 7, 8]], dataLimit: 'all');
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        return view(ServiceDetails::VIP_UPDATE[VIEW], compact('vip', 'packages', 'productes', 'languages', 'defaultLanguage'));
    }

    //  Package GET Controller Service
    public function getPackagesDropdownVIP(Request $request)
    {
        $selectedPackageIds = $request->input('packageIds', []);
        $availablePackages = [
            5 => 6,
            6 => 7,
            7 => 8,
            8 => null, // No next package after 8
        ];
        $nextAvailablePackages = [];
        foreach ($selectedPackageIds as $id) {
            if (isset($availablePackages[$id])) {
                $nextAvailablePackages[] = $availablePackages[$id];
            }
        }
        $packages = Package::whereIn('id', array_filter($nextAvailablePackages))->whereNotIn('id', $selectedPackageIds)->get();
        $html = '<select name="packages_id[]" class="form-control" id="package_id">';
        foreach ($packages as $package) {
            $html .= '<option value="' . $package->id . '">
                        ' . $package->title . '
                      </option>';
        }
        $html .= '</select>';

        return response()->json(['status' => 200, 'html' => $html]);
    }


    public function vip_update(VIPUpdateRequest $request, VIPAddService $vipAdd, $id): JsonResponse|RedirectResponse
    {
        $vip = $this->vipRepo->getFirstWhereWithoutGlobalScope(params: ['id' => $id], relations: ['translations']);
        $dataArray = $vipAdd->getUpdateVIPData(request: $request, vip: $vip, updateBy: 'admin');
        $this->vipRepo->update(id: $id, data: $dataArray);
        $this->vipRepo->addVIPTag(request: $request, vip: $vip);
        $this->translationRepo->update(request: $request, model: 'App\Models\Vippooja', id: $id);
        Toastr::success(translate('VIP_updated_successfully'));
        Helpers::editDeleteLogs('VIP', 'VIP', 'Update');
        return redirect()->route('admin.service.vip.list');
    }

    public function vip_delete(string|int $id, VIPAddService $vipAdd): RedirectResponse
    {
        $vip = $this->vipRepo->getVIPFirstWhere(params: ['id' => $id]);
        if ($vip) {
            $this->translationRepo->delete(model: 'App\Models\Vippooja', id: $id);
            $vipAdd->deleteImages(vip: $vip);
            $this->vipRepo->delete(params: ['id' => $id]);
            Toastr::success(translate('vip_removed_successfully'));
            Helpers::editDeleteLogs('VIP', 'VIP', 'Delete');
        } else {
            Toastr::error(translate('invalid_data'));
        }

        return back();
    }
    //Delete Product Images
    public function vipDeleteImage(Request $request, VIPAddService $vipAdd): RedirectResponse
    {
        $this->deleteFile(filePath: '/pooja/vip/' . $request['image']);
        $vip = $this->vipRepo->getFirstWhere(params: ['id' => $request['id']]);

        if (count(json_decode($vip['images'])) < 2) {
            Toastr::warning(translate('you_can_not_delete_all_images'));
            return back();
        }
        $imageProcessing = $vipAdd->deleteImage(request: $request, vip: $vip);

        $updateData = [
            'images' => json_encode($imageProcessing['images']),
        ];
        $this->vipRepo->update(id: $request['id'], data: $updateData);

        Toastr::success(translate('VIP_Pooja_image_removed_successfully'));
        return back();
    }
    public function vip_updateStatus(Request $request): JsonResponse
    {
        $data = [
            'status' => $request->get('status', 0),
        ];
        $this->vipRepo->update(id: $request['id'], data: $data);
        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }

    public function vip_prashad(Request $request)
    {
        $prashad = Vippooja::where('id', $request->vip_id)->update(['prashadam_id' => $request->prashadam_id]);
        if ($prashad) {
            Toastr::success(translate('Prashad added successfully!'));
        }
        return back();
    }

    // Offline pooja function 
    public function offline_pooja_index(Request|null $request, string $type = null): View
    {
        return $this->offline_pooja_getList($request);
    }

    public function offline_pooja_getList(Request $request): Application|Factory|View
    {
        $offlinePooja = $this->offlinePoojaRepo->getListWhere(orderBy: ['id' => 'desc'], searchValue: $request->get('searchValue'), relations: ['category'], dataLimit: getWebConfig(name: 'pagination_limit'));
        // $offlinePooja=$offlinePooja->with('category')->get();
        return view(ServiceDetails::OFFLINE_POOJA_LIST[VIEW], compact('offlinePooja'));
    }

    public function offline_pooja_getAddView(): View
    {
        // $packages = $this->packageRepo->getListWhere(filters: ['category_id' => 33, 'position' => 0, 'id' => 9], dataLimit: 'all');
        $category = OfflinepoojaCategory::where('status', 1)->get();
        $packages = Package::where('type', 'offlinepooja')->where('status', 1)->get();
        $temples = Temple::where('status', 1)->get();
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        return view(ServiceDetails::ADD_OFFLINE_POOJA[VIEW], compact('category', 'packages', 'temples', 'languages', 'defaultLanguage'));
    }

    public function offline_pooja_add(OfflinePoojaAddRequest $request, OfflinePoojaService $offlinePoojaAdd): RedirectResponse
    // public function offline_pooja_add(Request $request)
    {
        $dataArray = $offlinePoojaAdd->getAddOfflinePoojaData($request, addedBy: 'admin');
        $savedOfflinePooja = $this->offlinePoojaRepo->add(data: $dataArray);
        // $this->offlinePoojaRepo->addOfflinePoojaTag(request: $request, offlinepooja: $savedOfflinePooja);
        $this->translationRepo->add(request: $request, model: 'App\Models\PoojaOffline', id: $savedOfflinePooja->id);
        Toastr::success(translate('New_Offline_Pooja_added_successfully'));
        Helpers::editDeleteLogs('Offline Pooja', 'Offline Pooja', 'Insert');
        return redirect()->route('admin.service.offline.pooja.list');
    }

    public function offline_pooja_getUpdateView(string|int $id): View|RedirectResponse
    {
        $offlinePooja = $this->offlinePoojaRepo->getFirstWhere(params: ['id' => $id], relations: ['category', 'translations']);
        // $packages = $this->packageRepo->getListWhere(filters: ['category_id' => 33, 'position' => 0, 'package_id' => [5, 6, 7, 8]], dataLimit: 'all');
        $category = OfflinepoojaCategory::where('status', 1)->get();
        $packages = Package::where('type', 'offlinepooja')->where('status', 1)->get();
        $temples = Temple::where('status', 1)->get();
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        return view(ServiceDetails::OFFLINE_POOJA_UPDATE[VIEW], compact('offlinePooja', 'category', 'temples', 'packages', 'languages', 'defaultLanguage'));
    }
    public function offline_pooja_update(OfflinePoojaUpdateRequest $request, OfflinePoojaService $offlinePoojaUpdate, $id): JsonResponse|RedirectResponse
    {
        // dd($request->all());
        $offlinePooja = $this->offlinePoojaRepo->getFirstWhereWithoutGlobalScope(params: ['id' => $id], relations: ['translations']);
        $dataArray = $offlinePoojaUpdate->getUpdateServiceData(request: $request, offlinePooja: $offlinePooja, updateBy: 'admin');
        $this->offlinePoojaRepo->update(id: $id, data: $dataArray);
        // $this->offlinePoojaRepo->addVIPTag(request: $request, vip: $vip);
        $this->translationRepo->update(request: $request, model: 'App\Models\PoojaOffline', id: $id);
        Toastr::success(translate('Offline_pooja_updated_successfully'));
        Helpers::editDeleteLogs('Offline Pooja', 'Offline Pooja', 'Update');
        return redirect()->route('admin.service.offline.pooja.list');
    }

    public function offline_pooja_delete(string|int $id, OfflinePoojaService $offlinePoojaUpdate): RedirectResponse
    {
        $offlinePooja = $this->offlinePoojaRepo->getFirstWhere(params: ['id' => $id]);
        if ($offlinePooja) {
            $this->translationRepo->delete(model: 'App\Models\PoojaOffline', id: $id);
            $offlinePoojaUpdate->deleteImages(service: $offlinePooja);
            $this->offlinePoojaRepo->delete(params: ['id' => $id]);
            Toastr::success(translate('offline_pooja_removed_successfully'));
            Helpers::editDeleteLogs('Offline Pooja', 'Offline Pooja', 'Delete');
        } else {
            Toastr::error(translate('invalid_data'));
        }

        return back();
    }

    // //Delete Product Images
    public function offline_pooja_DeleteImage(Request $request, OfflinePoojaService $offlinePoojaUpdate): RedirectResponse
    {
        $this->deleteFile(filePath: '/offlinepooja/' . $request['image']);
        $offlinePooja = $this->offlinePoojaRepo->getFirstWhere(params: ['id' => $request['id']]);


        if (count(json_decode($offlinePooja['images'])) < 2) {
            Toastr::warning(translate('you_can_not_delete_all_images'));
            return back();
        }
        // dd($offlinePooja);
        $imageProcessing = $offlinePoojaUpdate->deleteImage(request: $request, offlinePooja: $offlinePooja);

        $updateData = [
            'images' => json_encode($imageProcessing['images']),
        ];
        $this->offlinePoojaRepo->update(id: $request['id'], data: $updateData);

        Toastr::success(translate('Offline_Pooja_image_removed_successfully'));
        return back();
    }

    public function offline_pooja_updateStatus(Request $request): JsonResponse
    {
        $data = [
            'status' => $request->get('status', 0),
        ];
        $this->offlinePoojaRepo->update(id: $request['id'], data: $data);
        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }

    // Package GET Controller Service
    public function getOfflinePoojaPackagesDropdown(Request $request)
    {
        $selectedPackageIds = $request->input('packageIds', []);
        $packages = Package::where('type', 'offlinepooja')->where('status', 1)->get();
        $html = '';
        $html .= '<select name="package_details[]" class="form-control" id="package_id">';
        foreach ($packages as $package) {
            $isDisabled = in_array($package->id, $selectedPackageIds) ? 'disabled' : '';
            $html .= '<option value="' . $package->id . '" ' . $isDisabled . '>
                                ' . $package->title . '
                            </option>';
        }
        $html .= '</select>';
        return response()->json(['status' => 200, 'html' => $html]);
    }
    // offline pooja refund policy
    public function offline_pooja_refund_policy_index(Request|null $request, string $type = null): View
    {
        return $this->offline_pooja_refund_policy_getList($request);
    }

    public function offline_pooja_refund_policy_getList(Request $request): Application|Factory|View
    {
        $offlineRefundPolicyPooja = $this->offlinePoojaRefundPolicyRepo->getListWhere(orderBy: ['id' => 'desc'], searchValue: $request->get('searchValue'), dataLimit: getWebConfig(name: 'pagination_limit'));
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        return view(ServiceDetails::OFFLINE_POOJA_REFUND_POLICY_LIST[VIEW], compact('offlineRefundPolicyPooja', 'languages', 'defaultLanguage'));
    }

    public function offline_pooja_refund_policy_add(OfflinePoojaRefundPolicyAddRequest $request, OfflinePoojaRefundPolicyService $offlinePoojaRefundPolicyAdd): RedirectResponse
    {
        $dataArray = $offlinePoojaRefundPolicyAdd->getAddOfflinePoojaRefundPolicyData($request, addedBy: 'admin');
        $savedOfflinePoojaRefundPolicy = $this->offlinePoojaRefundPolicyRepo->add(data: $dataArray);
        $this->translationRepo->add(request: $request, model: 'App\Models\OfflinepoojaRefundPolicy', id: $savedOfflinePoojaRefundPolicy->id);
        Toastr::success(translate('New_offline_pooja_refund_policy_added_successfully'));
        Helpers::editDeleteLogs('Offline Pooja', 'Refund Policy', 'Insert');
        return redirect()->route('admin.service.offline.pooja.refund.policy.list');
    }

    public function offline_pooja_refund_policy_getUpdateView(string|int $id): View|RedirectResponse
    {
        $offlinePoojaRefundPolicy = $this->offlinePoojaRefundPolicyRepo->getFirstWhere(params: ['id' => $id], relations: ['translations']);
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        return view(ServiceDetails::OFFLINE_POOJA_REFUND_POLICY_UPDATE[VIEW], compact('offlinePoojaRefundPolicy', 'languages', 'defaultLanguage'));
    }
    public function offline_pooja_refund_policy_update(OfflinePoojaRefundPolicyUpdateRequest $request, OfflinePoojaRefundPolicyService $offlinePoojaRefundPolicyUpdate, $id): JsonResponse|RedirectResponse
    {
        // dd($request->all());
        $offlinePoojaRefundPolicy = $this->offlinePoojaRefundPolicyRepo->getFirstWhereWithoutGlobalScope(params: ['id' => $id], relations: ['translations']);
        $dataArray = $offlinePoojaRefundPolicyUpdate->getUpdateServiceData(request: $request, offlinePooja: $offlinePoojaRefundPolicy, updateBy: 'admin');
        $this->offlinePoojaRefundPolicyRepo->update(id: $id, data: $dataArray);
        $this->translationRepo->update(request: $request, model: 'App\Models\OfflinepoojaRefundPolicy', id: $id);
        Toastr::success(translate('Offline_pooja_refund_policy_updated_successfully'));
        Helpers::editDeleteLogs('Offline Pooja', 'Refund Policy', 'Update');
        return redirect()->route('admin.service.offline.pooja.refund.policy.list');
    }

    public function offline_pooja_refund_policy_delete(string|int $id, OfflinePoojaRefundPolicyService $offlinePoojaRefundPolicyUpdate): RedirectResponse
    {
        $offlinePoojaRefundPolicy = $this->offlinePoojaRefundPolicyRepo->getFirstWhere(params: ['id' => $id]);
        if ($offlinePoojaRefundPolicy) {
            $this->translationRepo->delete(model: 'App\Models\OfflinepoojaRefundPolicy', id: $id);
            $this->offlinePoojaRefundPolicyRepo->delete(params: ['id' => $id]);
            Toastr::success(translate('offline_pooja_refund_policy_removed_successfully'));
            Helpers::editDeleteLogs('Offline Pooja', 'Refund Policy', 'Delete');
        } else {
            Toastr::error(translate('invalid_data'));
        }

        return back();
    }

    public function offline_pooja_refund_policy_updateStatus(Request $request): JsonResponse
    {
        $data = [
            'status' => $request->get('status', 0),
        ];
        $this->offlinePoojaRefundPolicyRepo->update(id: $request['id'], data: $data);
        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }
    // offline pooja schedule
    public function offline_pooja_schedule_index(Request|null $request, string $type = null): View
    {
        return $this->offline_pooja_schedule_getList($request);
    }

    public function offline_pooja_schedule_getList(Request $request): Application|Factory|View
    {
        $offlineSchedulePooja = $this->offlinePoojaScheduleRepo->getListWhere(orderBy: ['id' => 'desc'], searchValue: $request->get('searchValue'), dataLimit: getWebConfig(name: 'pagination_limit'));
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        return view(ServiceDetails::OFFLINE_POOJA_SCHEDULE_LIST[VIEW], compact('offlineSchedulePooja', 'languages', 'defaultLanguage'));
    }

    public function offline_pooja_schedule_add(OfflinePoojaScheduleAddRequest $request, OfflinePoojaScheduleService $offlinePoojaScheduleAdd): RedirectResponse
    {
        $dataArray = $offlinePoojaScheduleAdd->getAddOfflinePoojaScheduleData($request, addedBy: 'admin');
        $savedOfflinePoojaSchedule = $this->offlinePoojaScheduleRepo->add(data: $dataArray);
        $this->translationRepo->add(request: $request, model: 'App\Models\OfflinepoojaSchedule', id: $savedOfflinePoojaSchedule->id);
        Toastr::success(translate('New_offline_pooja_schedule_added_successfully'));
        Helpers::editDeleteLogs('Offline Pooja', 'Schedule', 'Insert');
        return redirect()->route('admin.service.offline.pooja.schedule.list');
    }

    public function offline_pooja_schedule_getUpdateView(string|int $id): View|RedirectResponse
    {
        $offlinePoojaSchedule = $this->offlinePoojaScheduleRepo->getFirstWhere(params: ['id' => $id], relations: ['translations']);
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        return view(ServiceDetails::OFFLINE_POOJA_SCHEDULE_UPDATE[VIEW], compact('offlinePoojaSchedule', 'languages', 'defaultLanguage'));
    }
    public function offline_pooja_schedule_update(OfflinePoojaScheduleUpdateRequest $request, OfflinePoojaScheduleService $offlinePoojaScheduleUpdate, $id): JsonResponse|RedirectResponse
    {
        // dd($request->all());
        $offlinePoojaSchedule = $this->offlinePoojaScheduleRepo->getFirstWhereWithoutGlobalScope(params: ['id' => $id], relations: ['translations']);
        $dataArray = $offlinePoojaScheduleUpdate->getUpdateServiceData(request: $request, offlinePooja: $offlinePoojaSchedule, updateBy: 'admin');
        $this->offlinePoojaScheduleRepo->update(id: $id, data: $dataArray);
        $this->translationRepo->update(request: $request, model: 'App\Models\OfflinepoojaSchedule', id: $id);
        Toastr::success(translate('Offline_pooja_schedule_updated_successfully'));
        Helpers::editDeleteLogs('Offline Pooja', 'Schedule', 'Update');
        return redirect()->route('admin.service.offline.pooja.schedule.list');
    }

    public function offline_pooja_schedule_delete(string|int $id, OfflinePoojaScheduleService $offlinePoojaScheduleUpdate): RedirectResponse
    {
        $offlinePoojaSchedule = $this->offlinePoojaScheduleRepo->getFirstWhere(params: ['id' => $id]);
        if ($offlinePoojaSchedule) {
            $this->translationRepo->delete(model: 'App\Models\OfflinepoojaSchedule', id: $id);
            $this->offlinePoojaScheduleRepo->delete(params: ['id' => $id]);
            Toastr::success(translate('offline_pooja_schedule_removed_successfully'));
            Helpers::editDeleteLogs('Offline Pooja', 'Schedule', 'Delete');
        } else {
            Toastr::error(translate('invalid_data'));
        }

        return back();
    }

    public function offline_pooja_schedule_updateStatus(Request $request): JsonResponse
    {
        $data = [
            'status' => $request->get('status', 0),
        ];
        $this->offlinePoojaScheduleRepo->update(id: $request['id'], data: $data);
        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }
    // offline pooja category
    public function offline_pooja_category_index(Request|null $request, string $type = null): View
    {
        return $this->offline_pooja_category_getList($request);
    }

    public function offline_pooja_category_getList(Request $request): Application|Factory|View
    {
        $offlinePoojaCategory = $this->offlinePoojaCategoryRepo->getListWhere(orderBy: ['id' => 'desc'], searchValue: $request->get('searchValue'), dataLimit: getWebConfig(name: 'pagination_limit'));
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        return view(ServiceDetails::OFFLINE_POOJA_CATEGORY_LIST[VIEW], compact('offlinePoojaCategory', 'languages', 'defaultLanguage'));
    }

    public function offline_pooja_category_add(OfflinePoojaCategoryAddRequest $request, OfflinePoojaCategoryService $offlinePoojaCategoryAdd): RedirectResponse
    {
        $dataArray = $offlinePoojaCategoryAdd->getAddOfflinePoojaCategoryData($request, addedBy: 'admin');
        $savedOfflinePoojaCategory = $this->offlinePoojaCategoryRepo->add(data: $dataArray);
        $this->translationRepo->add(request: $request, model: 'App\Models\OfflinepoojaCategory', id: $savedOfflinePoojaCategory->id);
        Toastr::success(translate('New_offline_pooja_category_added_successfully'));
        Helpers::editDeleteLogs('Offline Pooja', 'Category', 'Insert');
        return redirect()->route('admin.service.offline.pooja.category.list');
    }

    public function offline_pooja_category_getUpdateView(string|int $id): View|RedirectResponse
    {
        $offlinePoojaCategory = $this->offlinePoojaCategoryRepo->getFirstWhere(params: ['id' => $id], relations: ['translations']);
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        return view(ServiceDetails::OFFLINE_POOJA_CATEGORY_UPDATE[VIEW], compact('offlinePoojaCategory', 'languages', 'defaultLanguage'));
    }

    public function offline_pooja_category_update(OfflinePoojaCategoryUpdateRequest $request, OfflinePoojaCategoryService $offlinePoojaCategoryUpdate, $id): JsonResponse|RedirectResponse
    {
        // dd($request->all());
        $offlinePoojaCategory = $this->offlinePoojaCategoryRepo->getFirstWhereWithoutGlobalScope(params: ['id' => $id], relations: ['translations']);
        $dataArray = $offlinePoojaCategoryUpdate->getUpdateServiceData(request: $request, offlinePooja: $offlinePoojaCategory, updateBy: 'admin');
        $this->offlinePoojaCategoryRepo->update(id: $id, data: $dataArray);
        $this->translationRepo->update(request: $request, model: 'App\Models\OfflinepoojaCategory', id: $id);
        Toastr::success(translate('Offline_pooja_category_updated_successfully'));
        Helpers::editDeleteLogs('Offline Pooja', 'Category', 'Update');
        return redirect()->route('admin.service.offline.pooja.category.list');
    }

    public function offline_pooja_category_updateStatus(Request $request): JsonResponse
    {
        $data = [
            'status' => $request->get('status', 0),
        ];
        $this->offlinePoojaCategoryRepo->update(id: $request['id'], data: $data);
        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }

    //  city
    public function offline_pooja_city_index(Request $request): Application|Factory|View
    {
        $cities = Cities::select('id', 'city')->where('status', 1)->get();
        $offlinePoojaCity = CityDetail::orderBy('created_at','desc')->paginate(10);
        // dd($offlinePoojaCity->toArray());
        return view(ServiceDetails::OFFLINE_POOJA_CITY_LIST[VIEW], compact('cities', 'offlinePoojaCity'));
    }

    public function offline_pooja_city_add(Request $request)
    {
        $request->validate([
            'pincode' => 'required|unique:city_details,pincode',
            'city_id' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
        ]);

        $cityName = Cities::select('city')->where('id',$request->city_id)->first();
        $city = new CityDetail();
        $city->city_id = $request->city_id;
        $city->name = $cityName->city;
        $city->pincode = $request->pincode;
        $city->latitude = $request->latitude;
        $city->longitude = $request->longitude;
        if ($city->save()) {
            Toastr::success(translate('New_city_added_successfully'));
            Helpers::editDeleteLogs('City', 'City Detail', 'Insert');
            return redirect()->route('admin.service.offline.pooja.city.list');
        }
        Toastr::error(translate('an_error_occured'));
        return back();
    }

    public function offline_pooja_city_getUpdateView(string|int $id): View|RedirectResponse
    {
        $cities = Cities::select('id', 'city')->where('status', 1)->get();
        $offlinePoojaCity = CityDetail::where('id', $id)->first();
        return view(ServiceDetails::OFFLINE_POOJA_CITY_UPDATE[VIEW], compact('offlinePoojaCity', 'cities'));
    }

    public function offline_pooja_city_update(Request $request, $id)
    {
        $request->validate([
            'pincode'   => 'required',
            'city_id'   => 'required',
            'latitude'  => 'required',
            'longitude' => 'required',
        ]);

        $pincodeExists = CityDetail::where('pincode', $request->pincode)
            ->where('id', '!=', $id)
            ->exists();

        if ($pincodeExists) {
            Toastr::error(translate('pincode_is_assigned_to_another_city'));
            return back();
        }

        $cityName = Cities::select('city')->where('id',$request->city_id)->first();
        $city = CityDetail::find($id);
        $city->city_id   = $request->city_id;
        $city->name      = $cityName->city;
        $city->pincode   = $request->pincode;
        $city->latitude  = $request->latitude;
        $city->longitude = $request->longitude;

        if ($city->save()) {
            Toastr::success(translate('city_updated_successfully'));
            Helpers::editDeleteLogs('City', 'City Detail', 'Update');
            return redirect()->route('admin.service.offline.pooja.city.list');
        }

        Toastr::error(translate('an_error_occurred'));
        return back();
    }


    public function offline_pooja_city_updateStatus(Request $request): JsonResponse
    {
        $status = 0;
        if ($request->has('status')) {
            $status = $request->status;
        }
        $status = CityDetail::where('id', $request->id)->update(['status' => $status]);
        if ($status) {
            return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
        }
        return response()->json(['success' => 0, 'message' => translate('an_error_occured')], 200);
    }
}
