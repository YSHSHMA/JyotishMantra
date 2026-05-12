<?php

namespace App\Http\Controllers\Admin\Promotion;

use App\Contracts\Repositories\BannerRepositoryInterface;
use App\Contracts\Repositories\BrandRepositoryInterface;
use App\Contracts\Repositories\CategoryRepositoryInterface;
use App\Contracts\Repositories\ProductRepositoryInterface;
use App\Contracts\Repositories\ShopRepositoryInterface;
use App\Contracts\Repositories\AppSectionRepositoryInterface;
use App\Enums\ViewPaths\Admin\Banner;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\BannerAddRequest;
use App\Http\Requests\Admin\BannerUpdateRequest;
use App\Services\BannerService;
use App\Traits\FileManagerTrait;
use App\Utils\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BannerController extends BaseController
{
    use FileManagerTrait {
        delete as deleteFile;
        update as updateFile;
    }

    public function __construct(
        private readonly BannerRepositoryInterface        $bannerRepo,
        private readonly CategoryRepositoryInterface      $categoryRepo,
        private readonly ShopRepositoryInterface          $shopRepo,
        private readonly BrandRepositoryInterface         $brandRepo,
        private readonly ProductRepositoryInterface       $productRepo,
        private readonly AppSectionRepositoryInterface         $appsectionRepo,
        private readonly BannerService       $bannerService,
    )
    {
    }

    /**
     * @param Request|null $request
     * @param string|null $type
     * @return View Index function is the starting point of a controller
     * Index function is the starting point of a controller
     */
    public function index(Request|null $request, string $type = null): View
    {
        return $this->getListView($request);
    }

    public function getListView(Request $request): View
    {
        $bannerTypes = $this->bannerService->getBannerTypes();
        $banners = $this->bannerRepo->getListWhereIn(
            orderBy: ['id'=>'desc'],
            searchValue: $request['searchValue'],
            filters: ['theme'=>theme_root_path()],
            whereInFilters: ['banner_type' => array_keys($bannerTypes)],
            dataLimit: getWebConfig(name: 'pagination_limit'),
        );

        $categories = $this->categoryRepo->getListWhere(filters: ['position'=>0], dataLimit: 'all');
        $shops = $this->shopRepo->getListWithScope(scope:'active', dataLimit: 'all');
        $brands = $this->brandRepo->getListWhere(dataLimit: 'all');
        $appsections = $this->appsectionRepo->getListWhere(dataLimit: 'all');
        $products = $this->productRepo->getListWithScope(scope:'active', dataLimit: 'all');
        $services = $this->categoryRepo->getListWhere(filters: ['parent_id' => [33, 39]], dataLimit: 'all');
        $allservice = $this->categoryRepo->getListWhere(filters: ['id' => [272, 273, 274, 275, 276]], dataLimit: 'all');
        $services = $services->merge($allservice);
        $subServices = \App\Models\Service::where('status', 1)->whereIn('sub_category_id', $services->pluck('id'))
            ->get()
            ->groupBy('sub_category_id');
        $vipPooja   = \App\Models\Vippooja::where('status', 1)->where('is_anushthan', 0)->get();
        $anushthan = \App\Models\Vippooja::where('status', 1)->where('is_anushthan', 1)->get();
        $chadhava  = \App\Models\Chadhava::where('status', 1)->get();
        $tour  = \App\Models\TourVisits::where('status', 1)->get();
        $event  = \App\Models\Events::where('status', 1)->get();
        $darshan  = \App\Models\Temple::where('status', 1)->get();
        $offlinePooja  = \App\Models\PoojaOffline::where('status', 1)->get();
        $donation  = \App\Models\DonateAds::where('status', 1)->get();
        
        $isReactActive = getWebConfig(name: 'react_setup')['status'];
        return view(Banner::LIST[VIEW],  compact('banners', 'categories','shops', 'brands', 'products', 'appsections', 'isReactActive', 'bannerTypes', 'services', 'subServices', 'vipPooja', 'anushthan', 'chadhava','tour','event', 'darshan', 'offlinePooja','donation'));
    }

    public function add(BannerAddRequest $request): RedirectResponse
    {
        $data = $this->bannerService->getProcessedData(request: $request);
        $this->bannerRepo->add(data:$data);
        Toastr::success(translate('banner_added_successfully'));
        Helpers::editDeleteLogs('Banner Setup','Banner','Insert');
        return redirect()->route('admin.banner.list');
    }

    public function getUpdateView($id): View
    {
        $bannerTypes = $this->bannerService->getBannerTypes();
        $banner = $this->bannerRepo->getFirstWhere(params: ['id'=>$id]);
        $categories = $this->categoryRepo->getListWhere(filters: ['position'=>0], dataLimit: 'all');
        $shops = $this->shopRepo->getListWithScope(scope:'active', dataLimit: 'all');
        $brands = $this->brandRepo->getListWhere(dataLimit: 'all');
        $appsections = $this->appsectionRepo->getListWhere(dataLimit: 'all');
        $products = $this->productRepo->getListWithScope(scope:'active', dataLimit: 'all');
        $services = $this->categoryRepo->getListWhere(filters: ['parent_id' => [33, 39]], dataLimit: 'all');
        $allservice = $this->categoryRepo->getListWhere(filters: ['id' => [272, 273, 274, 275, 276]], dataLimit: 'all');
        $services = $services->merge($allservice);
        $subServices = \App\Models\Service::where('status', 1)->whereIn('sub_category_id', $services->pluck('id'))
            ->get()
            ->groupBy('sub_category_id');
        $vipPooja   = \App\Models\Vippooja::where('status', 1)->where('is_anushthan', 0)->get();
        $anushthan = \App\Models\Vippooja::where('status', 1)->where('is_anushthan', 1)->get();
        $chadhava  = \App\Models\Chadhava::where('status', 1)->get();
        $tour  = \App\Models\TourVisits::where('status', 1)->get();
        $event  = \App\Models\Events::where('status', 1)->get();
        $darshan  = \App\Models\Temple::where('status', 1)->get();
        $offlinePooja  = \App\Models\PoojaOffline::where('status', 1)->get();
        $donation  = \App\Models\DonateAds::where('status', 1)->get();

        return view(Banner::UPDATE[VIEW], compact('banner', 'categories','shops', 'brands', 'products', 'appsections', 'bannerTypes', 'services', 'subServices', 'vipPooja', 'anushthan', 'chadhava', 'tour', 'event', 'darshan', 'offlinePooja','donation'));
    }

    public function update(BannerUpdateRequest $request, $id): RedirectResponse
    {
        $banner = $this->bannerRepo->getFirstWhere(params: ['id'=>$id]);
        $data = $this->bannerService->getProcessedData(request: $request, image: $banner['photo']);
        $this->bannerRepo->update(id:$banner['id'], data:$data);
        Toastr::success(translate('banner_updated_successfully'));
        Helpers::editDeleteLogs('Banner Setup','Banner','Update');
        return redirect()->route(Banner::UPDATE[ROUTE]);
    }

    public function updateStatus(Request $request): JsonResponse
    {
        $status = $request->get('status', 0);
        $this->bannerRepo->update(id:$request['id'], data:['published'=>$status]);
        return response()->json([
            'message' => $status == 1 ? translate("banner_published_successfully") : translate("banner_unpublished_successfully"),
        ]);
    }

    public function delete(Request $request): JsonResponse
    {
        $banner = $this->bannerRepo->getFirstWhere(params: ['id' => $request['id']]);
        $this->deleteFile(filePath: '/banner/' . $banner['photo']);
        $this->bannerRepo->delete(params: ['id' => $request['id']]);
        Helpers::editDeleteLogs('Banner Setup','Banner','Delete');
        return response()->json(['message' => translate('banner_deleted_successfully')]);
    }
}
