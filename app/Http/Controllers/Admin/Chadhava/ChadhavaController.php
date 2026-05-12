<?php

namespace App\Http\Controllers\Admin\Chadhava;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\Foundation\Application;
use App\Enums\WebConfigKey;
use App\Contracts\Repositories\AttributeRepositoryInterface;
use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Contracts\Repositories\ChadhavaRepositoryInterface;
use App\Contracts\Repositories\ProductRepositoryInterface;
use App\Contracts\Repositories\CitiesRepositoryInterface;
use App\Http\Requests\Admin\ChadhavaAddRequest;
use App\Http\Requests\ChadhavaUpdateRequest;
use App\Enums\ViewPaths\Admin\ChadhavaPath;
use App\Models\Bhagwan;
use App\Models\Cities;
use App\Models\Product;
use App\Services\ChadhavaAddService;
use Brian2694\Toastr\Facades\Toastr;
use mysql_xdevapi\Exception;
use App\Traits\FileManagerTrait;
use App\Utils\Helpers;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions;

class ChadhavaController  extends BaseController
{
    
    use FileManagerTrait {
        delete as deleteFile;
        update as updateFile;
    }

    public function __construct(
        private readonly ChadhavaRepositoryInterface         $chadhavaRepo,
        private readonly AttributeRepositoryInterface        $attributeRepo,
        private readonly TranslationRepositoryInterface      $translationRepo,
        private readonly ProductRepositoryInterface          $productRepo,
        private readonly CitiesRepositoryInterface           $citiesRepo,

    ) {
    }

    public function index(Request|null $request, string $type = null): View
    {
        return $this->getList($request);
    }

    public function getList(Request $request): Application|Factory|View
    {
        $chadhava = $this->chadhavaRepo->getListWhere(orderBy: ['id' => 'desc'], searchValue: $request->get('searchValue'),dataLimit: getWebConfig(name: 'pagination_limit'));
        return view(ChadhavaPath::LIST[VIEW], compact('chadhava'));
    }
    public function getAddView(): View
    {
        $productes = $this->productRepo->getListWhere(filters: ['category_id' => 33, 'position' => 0], dataLimit: 'all');
        $citiesList = Cities::orderBy('city', 'asc')->get();
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        $bhagwanId = Bhagwan::all();
        return view(ChadhavaPath::ADD[VIEW], compact('productes','languages', 'defaultLanguage','citiesList','bhagwanId'));
    }
 
    public function add(ChadhavaAddRequest $request, ChadhavaAddService $chadhavaAdd): RedirectResponse
    {   
        $dataArray = $chadhavaAdd->getAddChadhavaData($request, addedBy: 'admin');
        $savedChadhava = $this->chadhavaRepo->add(data: $dataArray);
        // dd($savedChadhava);
        $this->chadhavaRepo->addChadhavaTag(request: $request, chadhava: $savedChadhava);
        $this->translationRepo->add(request: $request, model: 'App\Models\Chadhava', id: $savedChadhava->id);
        Toastr::success(translate('chadhava_added_successfully'));
        Helpers::editDeleteLogs('Chadhava','Chadhava','Insert');
        return redirect()->route('admin.chadhava.list');
    }
     // Service Status On/Off
    public function updateStatus(Request $request): JsonResponse
     {
         $data = [
             'status' => $request->get('status', 0),
         ];
         $this->chadhavaRepo->update(id: $request['id'], data: $data);
         return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }
     // chadhava Update Data
    public function getUpdateView(string|int $id): View|RedirectResponse
     {
        $chadhava = $this->chadhavaRepo->getFirstWhere(params: ['id' => $id], relations: ['translations']);
        $citiesList = Cities::orderBy('city', 'asc')->get();
        $productes = $this->productRepo->getListWhere(filters: ['category_id' => 33, 'position' => 0], dataLimit: 'all');
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        $bhagwanId = Bhagwan::all();
        return view(ChadhavaPath::UPDATE[VIEW], compact('citiesList','chadhava', 'languages', 'defaultLanguage','productes','bhagwanId'));
    }
     
    public function update(ChadhavaUpdateRequest $request, ChadhavaAddService $chadhavaAdd, $id): JsonResponse|RedirectResponse
     {
        $chadhava = $this->chadhavaRepo->getFirstWhereWithoutGlobalScope(params: ['id' => $id], relations: ['translations']);
        $dataArray = $chadhavaAdd->getUpdateChadhavaData(request: $request, chadhava: $chadhava, updateBy: 'admin');
         // dd($dataArray);
        $this->chadhavaRepo->update(id: $id, data: $dataArray);
        $this->chadhavaRepo->addChadhavaTag(request: $request, chadhava: $chadhava);
        $this->translationRepo->update(request: $request, model: 'App\Models\Chadhava', id: $id);
        Toastr::success(translate('chadhava_updated_successfully'));
        Helpers::editDeleteLogs('Chadhava','Chadhava','Update');
        return redirect()->route('admin.chadhava.list');
    }
     // Chadhava Delete
    public function delete(string|int $id, ChadhavaAddService $chadhavaAdd): RedirectResponse
     {
        $chadhava = $this->chadhavaRepo->getChadhavaFirstWhere(params: ['id' => $id]);
        if ($chadhava) {
             $this->translationRepo->delete(model: 'App\Models\Chadhava', id: $id);
             $chadhavaAdd->deleteImages(chadhava: $chadhava);
             $this->chadhavaRepo->delete(params: ['id' => $id]);
             Toastr::success(translate('Chadhava_removed_successfully'));
             Helpers::editDeleteLogs('Chadhava','Chadhava','Delete');
         } else {
             Toastr::error(translate('invalid_product'));
         }
 
         return back();
    }
     //Delete Product Images
    public function deleteImage(Request $request, ChadhavaAddService $chadhavaAdd): RedirectResponse
     {
        $this->deleteFile(filePath: '/chadhava/' . $request['image']);
        $chadhava = $this->chadhavaRepo->getFirstWhere(params: ['id' => $request['id']]);
        if (count(json_decode($chadhava['images'])) < 2) {
             Toastr::warning(translate('you_can_not_delete_all_images'));
             return back();
        }
        $imageProcessing = $chadhavaAdd->deleteImage(request: $request, chadhava: $chadhava);
        $updateData = [
             'images' => json_encode($imageProcessing['images']),
        ];
        $this->chadhavaRepo->update(id: $request['id'], data: $updateData);
        Toastr::success(translate('Chadhava_image_removed_successfully'));
        return back();
    }

    // Singe Pooja View
    public function getView(string $addedBy, string|int $id): View
    {   
        $ChadhavaActive = $this->productRepo->getFirstWhereActive(params: ['id' => $id]);
        $relations =  ['orderDetails','translations','pandit','product'];
        $chadhava = $this->chadhavaRepo->getFirstWhereWithoutGlobalScope(params: ['id' => $id], relations: $relations);
        //   dd($vippooja);
        return view(ChadhavaPath::VIEW[VIEW], compact('chadhava', 'addedBy','ChadhavaActive'));
    }
}