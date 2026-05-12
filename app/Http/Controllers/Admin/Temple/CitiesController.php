<?php

namespace App\Http\Controllers\Admin\Temple;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\View;
use App\Contracts\Repositories\CategoryRepositoryInterface;
use App\Contracts\Repositories\AttributeRepositoryInterface;
use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Contracts\Repositories\CitiesRepositoryInterface;
use App\Contracts\Repositories\ProductRepositoryInterface;
use App\Services\CitiesAddService;
use App\Http\Requests\Admin\CitiesAddRequest;
use App\Http\Requests\CitiesUpdateRequest;
use App\Enums\ViewPaths\Admin\CitiesPath;
use App\Models\Cities;
use App\Models\States;
use Brian2694\Toastr\Facades\Toastr;
use mysql_xdevapi\Exception;
use App\Traits\FileManagerTrait;
use App\Utils\Helpers;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Maatwebsite\Excel\Facades\Excel;

class CitiesController extends BaseController
{
    use FileManagerTrait {
        delete as deleteFile;
        update as updateFile;
    }

    public function __construct(
    private readonly CitiesRepositoryInterface           $citiesRepo,
    private readonly CategoryRepositoryInterface         $categoryRepo,
    private readonly AttributeRepositoryInterface        $attributeRepo,
    private readonly TranslationRepositoryInterface      $translationRepo,
    private readonly ProductRepositoryInterface          $productRepo,

    )
    {
    }

    public function index(Request|null $request, string $type = null): View
    {
        return $this->getList($request);
    }

    public function getList(Request $request): Application|Factory|View
    {
        $cities = $this->citiesRepo->getListWhere(orderBy:['id'=>'desc'],searchValue:$request->get('searchValue'),dataLimit: getWebConfig(name: 'pagination_limit'));
        return view(CitiesPath::LIST[VIEW],compact('cities'));
    }
    
    public function getView(string $addedBy,string|int $id): View
    {
        $relations = ['translations'];
        $cities = $this->citiesRepo->getFirstWhereWithoutGlobalScope(params: ['id' => $id], relations: $relations);
        $stateList = $this->citiesRepo->getListWhere(filters: ['position' => 0], dataLimit: 'all');
        return view(CitiesPath::VIEW[VIEW],compact('cities','addedBy','stateList'));
    }

    public function getAddView(): View
    {   
        $attributes = $this->attributeRepo->getList(orderBy: ['name' => 'desc'], dataLimit: 'all');
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        $citiesList = cities::orderBy('city', 'asc')->get();
        $stateList = States::orderBy('name', 'asc')->get();
        return view(CitiesPath::ADD[VIEW],compact('attributes','languages', 'defaultLanguage','stateList','citiesList'));
    }

    public function add(CitiesAddRequest $request, CitiesAddService $CitiesAdd): RedirectResponse
    {
        // dd($request->input());
        $dataArray = $CitiesAdd->getAddData($request);
        $savedCities = $this->citiesRepo->add(data:$dataArray);
        // dd($savedCities);
        $this->translationRepo->add(request:$request, model:'App\Models\Cities', id:$savedCities->id);
        Toastr::success(translate('cities_added_successfully'));
        return redirect()->route('admin.temple.cities.list');
    }
    // CITIES Update Data
    public function getUpdateView(string|int $id): View|RedirectResponse
    {
        $cities = $this->citiesRepo->getFirstWhere(params:['id'=>$id], relations: ['translations']);
        $citesList = $this->citiesRepo->getListWhere(filters: ['position' => 0], dataLimit: 'all');
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        $stateList = States::orderBy('id', 'asc')->get();
        return view(CitiesPath::UPDATE[VIEW], compact('cities','citesList','languages', 'defaultLanguage','stateList'));
    }

    public function update(CitiesUpdateRequest $request, CitiesAddService $CitiesAdd, $id): JsonResponse|RedirectResponse
    {
        $cities = $this->citiesRepo->getFirstWhereWithoutGlobalScope(params: ['id' => $id], relations: ['translations']);
        $dataArray = $CitiesAdd->getUpdateData(request: $request, cities: $cities);
        // dd($dataArray);
        $this->citiesRepo->update(id: $id, data: $dataArray);
        $this->translationRepo->update(request: $request, model: 'App\Models\Cities', id: $id);
        Toastr::success(translate('cities_updated_successfully'));
        Helpers::editDeleteLogs('Temple','City','Update');
        return redirect()->route('admin.temple.cities.list');
    }
    
     // Cities's Delete
    public function delete(string|int $id,CitiesAddService $CitiesAdd): RedirectResponse
     {
        $cities= $this->citiesRepo->getCitiesFirstWhere(params: ['id' => $id]);
        if ($cities){
            $this->translationRepo->delete(model: 'App\Models\Cities', id: $id);
            $this->citiesRepo->delete(params: ['id' => $id]);
            Toastr::success(translate('cities_removed_successfully'));
            Helpers::editDeleteLogs('Temple','City','Delete');
        }else {
            Toastr::error(translate('invalid_product'));
        }
        return back();
    }
    
}