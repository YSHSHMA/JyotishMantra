<?php

namespace App\Http\Controllers\Admin\Packages;

// use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Contracts\Repositories\PackageRepositoryInterface;
use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Enums\ViewPaths\Admin\Package;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\PackageAddRequest;
use App\Http\Requests\Admin\PackageUpdateRequest;
use App\Services\PackageService;
use App\Utils\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use App\Models\Astrologer\Astrologer as AllPandit;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PackageController extends BaseController
{
    public function __construct(
        private readonly PackageRepositoryInterface         $packageRepo,
        private readonly TranslationRepositoryInterface     $translationRepo,
    ) {
    }

    /**
     * @param Request|null $request
     * @param string|null $type
     * @return View Index function is the starting point of a controller
     * Index function is the starting point of a controller
     */

    public function index(Request|null $request, string $type = null): View
    {
        // return $this->getList($request);
        return $this->getAddView($request, $type);
    }

    public function getAddView(Request $request, $id): View
    {
        $packages = $this->packageRepo->getListWhere(orderBy: ['id' => 'desc'], searchValue: $request->get('searchValue'), filters: ['service_id' => $id, 'status' => 1], dataLimit: getWebConfig(name: 'pagination_limit'));
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        $pandit = AllPandit::where('status',1)->get();
        // dd($packages);
        return view(Package::LIST[VIEW], [
            'packages' => $packages,
            'languages' => $languages,
            'defaultLanguage' => $defaultLanguage,
            'id' => $id,
            'pandit' => $pandit
        ]);
    }

    public function add(PackageAddRequest $request, PackageService $PackageService): RedirectResponse
    {
        $dataArray = $PackageService->getAddData(request: $request);
        $savedAttributes = $this->packageRepo->add(data: $dataArray);
        $this->translationRepo->add(request: $request, model: 'App\Models\Package', id: $savedAttributes->id);

        Toastr::success(translate('Package_added_successfully'));
        return back();
    }

    public function getUpdateView(string|int $id): View|RedirectResponse
    {
        $package = $this->packageRepo->getFirstWhere(params: ['id' => $id], relations: ['translations']);
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        $pandit = AllPandit::where('status',1)->get();
        return view(Package::UPDATE[VIEW], compact('package', 'languages', 'defaultLanguage','pandit'));
    }

    public function update(PackageUpdateRequest $request, PackageService $PackageService): RedirectResponse
    {
        $package = $this->packageRepo->getFirstWhere(params: ['id' => $request['id']]);
        $dataArray = $PackageService->getUpdateData(request: $request, data: $package);
        $this->packageRepo->update(id: $request['id'], data: $dataArray);
        $this->translationRepo->update(request: $request, model: 'App\Models\Package', id: $request['id']);
        Toastr::success(translate('Package_updated_successfully'));
        Helpers::editDeleteLogs('Pooja','Package','Update');
        return redirect()->route('admin.package.list', $request->service_id);
    }

    public function delete(string|int $id, PackageService $PackageService): RedirectResponse
    {
        $Package = $this->packageRepo->getFirstWhere(params: ['id' => $id]);
        if ($Package) {
            $this->translationRepo->delete(model: 'App\Models\Package', id: $id);
            $this->packageRepo->delete(params: ['id' => $id]);
            Toastr::success(translate('Package_deleted_successfully'));
            Helpers::editDeleteLogs('Pooja','Package','Delete');
        } else {
            Toastr::error(translate('error occured'));
        }
        return redirect()->back();
    }
}