<?php

namespace App\Http\Controllers\Admin\Temple;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\Foundation\Application;
use App\Http\Requests\Admin\TemplesAddRequest;
use App\Http\Requests\TemplesUpdateRequest;
use App\Enums\ViewPaths\Admin\TemplePath;
use App\Enums\WebConfigKey;
use App\Contracts\Repositories\AttributeRepositoryInterface;
use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Contracts\Repositories\TemplesRepositoryInterface;
use App\Contracts\Repositories\CitiesRepositoryInterface;
use App\Models\Cities;
use App\Models\States;
use App\Services\TemplesService;
use Brian2694\Toastr\Facades\Toastr;
use mysql_xdevapi\Exception;
use App\Traits\FileManagerTrait;
use App\Utils\Helpers;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Maatwebsite\Excel\Facades\Excel;


class TempleController  extends BaseController
{

    use FileManagerTrait {
        delete as deleteFile;
        update as updateFile;
    }

    public function __construct(
    private readonly TemplesRepositoryInterface          $templeRepo,
    private readonly CitiesRepositoryInterface          $citiesRepo,
    private readonly AttributeRepositoryInterface        $attributeRepo,
    private readonly TranslationRepositoryInterface      $translationRepo,

    )
    {
    }

    public function index(Request|null $request, string $type = null): View
    {
        return $this->getList($request);
    }

    public function getList(Request $request): Application|Factory|View
    {
        $temple = $this->templeRepo->getListWhere(orderBy:['id'=>'desc'],searchValue:$request->get('searchValue'),dataLimit: getWebConfig(name: 'pagination_limit'));
        return view(TemplePath::LIST[VIEW],compact('temple'));
    }
    public function getView(string $addedBy,string|int $id): View
    {
        $relations = ['translations'];
        $temple = $this->templeRepo->getFirstWhereWithoutGlobalScope(params: ['id' => $id], relations: $relations);
        return view(TemplePath::VIEW[VIEW],compact('temple','addedBy'));
    }


    public function getAddView(): View
    {   
        // $categories = $this->categoryRepo->getListWhere(filters: ['position' => 0], dataLimit: 'all');
        $citiesList = Cities::orderBy('city', 'asc')->get();
        $stateList = States::orderBy('name', 'asc')->get();
        $digitalProductSetting = getWebConfig(name: 'digital_product');
        $attributes = $this->attributeRepo->getList(orderBy: ['name' => 'desc'], dataLimit: 'all');
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        return view(TemplePath::ADD[VIEW],compact('stateList','citiesList','digitalProductSetting', 'attributes','languages', 'defaultLanguage'));
    }

    public function add(TemplesAddRequest $request, TemplesService $templeServices): RedirectResponse
    {
        // dd($request->input());
        $dataArray = $templeServices->getAddTemplesData($request,addedBy: 'admin');
        $savedTemple = $this->templeRepo->add(data:$dataArray);
        // $this->templeRepo->addTemplesTags(request: $request, temple: $savedTemple);
        $this->translationRepo->add(request:$request, model:'App\Models\Temple', id:$savedTemple->id);
        Toastr::success(translate('temple_added_successfully'));
        return redirect()->route('admin.temple.list');
    }

    // Service Status On/Off
    public function updateStatus(Request $request): JsonResponse
    {
        $data = [
          'status' => $request->get('status', 0),
        ];
        $this->templeRepo->update(id:$request['id'], data:$data);
        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }
    // Services Update Data
    public function getUpdateView(string|int $id): View|RedirectResponse
    {
        $temple = $this->templeRepo->getFirstWhere(params:['id'=>$id], relations: ['translations']);
        $citiesList = Cities::orderBy('city', 'asc')->get();
        $stateList = States::orderBy('name', 'asc')->get();
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        return view(TemplePath::UPDATE[VIEW], compact('temple', 'citiesList','stateList','languages', 'defaultLanguage'));
    }
    public function update(TemplesUpdateRequest $request, TemplesService $templeService, $id): JsonResponse|RedirectResponse
    {
        $temple = $this->templeRepo->getFirstWhereWithoutGlobalScope(params: ['id' => $id], relations: ['translations']);
        $dataArray = $templeService->getUpdateTempleData(request: $request, temple: $temple, updateBy: 'admin');
        // dd($dataArray);
        $this->templeRepo->update(id: $id, data: $dataArray);
        $this->translationRepo->update(request: $request, model: 'App\Models\Temple', id: $id);
        Toastr::success(translate('temple_updated_successfully'));
        Helpers::editDeleteLogs('Temple','Temple','Update');
       return redirect()->route('admin.temple.list');
    }
    // Services Delete
    public function delete(string|int $id,TemplesService $templeService): RedirectResponse
    {
        $temple= $this->templeRepo->getTempleFirstWhere(params: ['id' => $id]);
        if ($temple) {
            $this->translationRepo->delete(model: 'App\Models\Temple', id: $id);
            $templeService->deleteImages(temple:$temple);
            $this->templeRepo->delete(params: ['id' => $id]);
            Toastr::success(translate('temple_removed_successfully'));
            Helpers::editDeleteLogs('Temple','Temple','Delete');
        }else {
            Toastr::error(translate('invalid_product'));
        }

        return back();
    }
    //Delete Product Images
    public function deleteImage(Request $request, TemplesService $templeService): RedirectResponse
    {
        $this->deleteFile(filePath: '/temple/' . $request['image']);
        $temple = $this->templeRepo->getFirstWhere(params: ['id' => $request['id']]);
        
        if (count(json_decode($temple['images'])) < 2) {
            Toastr::warning(translate('you_can_not_delete_all_images'));
            return back();
        }
        $imageProcessing = $templeService->deleteImage(request: $request, temple: $temple);

        $updateData = [
            'images' => json_encode($imageProcessing['images']),
        ];
        $this->templeRepo->update(id: $request['id'], data: $updateData);

        Toastr::success(translate('product_image_removed_successfully'));
        return back();
    }

    public function getCities(Request $request, TemplesService $templeService): JsonResponse
    {
        $parentId = $request['id'];
        $filter = ['id' => $parentId];
        $citiesList = Cities::where('state_id', $filter)->get();
        $dropdown = $templeService->getStatesDropdown(request: $request, cities: $citiesList);

        $childStates = '';
        if (count($citiesList) == 1) {
            $subCities = $this->citiesRepo->getListWhere(filters: ['state_id' => $citiesList[0]['id']], dataLimit: 'all');
            $childStates = $templeService->getStatesDropdown(request: $request, cities: $subCities);
        }

        return response()->json([
            'select_tag' => $dropdown,
            'sub_cities' => count($citiesList) == 1 ? $childStates : '',
        ]);
    }
}
