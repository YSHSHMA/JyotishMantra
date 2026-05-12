<?php

namespace App\Http\Controllers\Admin\Sangeet;

use App\Contracts\Repositories\SangeetCategoryRepositoryInterface;
use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Enums\ViewPaths\Admin\SangeetCategory;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\SangeetCategoryAddRequest;
use App\Http\Requests\Admin\SangeetCategoryUpdateRequest;
use App\Http\Resources\SangeetCategoryResource;
use App\Traits\PaginatorTrait;
use App\Services\SangeetCategoryService;
use App\Utils\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SangeetCategoryController extends BaseController
{
    use PaginatorTrait;

    public function __construct(
        private readonly SangeetCategoryRepositoryInterface      
         $sangeetcategoryRepo,
        private readonly TranslationRepositoryInterface     $translationRepo,
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
        return $this->getAddView($request);
    }

    public function getList(): JsonResponse
    {
        $sangeetcategorys = $this->sangeetcategoryRepo->getList(dataLimit: 'all');
        return response()->json(SangeetCategoryResource::collection($sangeetcategorys));
    }

public function getAddView(Request $request): View
{
    $sangeetcategorys = $this->sangeetcategoryRepo->getListWhere(
        searchValue: $request->get('searchValue'),
        dataLimit: getWebConfig(name: 'pagination_limit')
    );

    $language = getWebConfig(name: 'pnc_language') ?? null;
    $defaultLanguage = $language[0];


    // Determine the view to use based on conditions (if any)
    $view = SangeetCategory::LIST[VIEW]; // Example view selection, adjust as needed

    return view($view, compact('sangeetcategorys', 'language', 'defaultLanguage'));
}


 public function getUpdateView(string|int $id): View|RedirectResponse
    {
        $sangeetcategory = $this->sangeetcategoryRepo->getFirstWhere(params:['id'=>$id], relations: ['translations']);
        $language = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $language[0];
        return view(SangeetCategory::UPDATE[VIEW], compact('sangeetcategory', 'language', 'defaultLanguage'));
    }

       public function updateStatus(Request $request): JsonResponse
    {
        $data = [
            'status' => $request->get('status', 0),
        ];
        $this->sangeetcategoryRepo->update(id: $request['id'], data: $data);
        
        return response()->json(['success' => true, 'message' => translate('status_updated_successfully')]);
    }

 public function add(SangeetCategoryAddRequest $request, SangeetCategoryService $sangeetcategoryService): RedirectResponse
    {
        $dataArray = $sangeetcategoryService->getAddData(request:$request);
        $savedAttributes = $this->sangeetcategoryRepo->add(data:$dataArray);
        $this->translationRepo->add(request:$request, model:'App\Models\SangeetCategory', id:$savedAttributes->id);

        Toastr::success(translate('sangeetcategory_added_successfully'));
        Helpers::editDeleteLogs('Sangeet','Sangeet Category','Insert');
        return redirect()->route('admin.sangeetcategory.view');
    }



 public function update(SangeetCategoryUpdateRequest $request, $id, SangeetCategoryService $sangeetCategoryService): RedirectResponse
    {
        $panchang = $this->sangeetcategoryRepo->getFirstWhere(params:['id'=>$request['id']]);
        $dataArray = $sangeetCategoryService->getUpdateData(request: $request, data:$panchang);
        $this->sangeetcategoryRepo->update(id:$request['id'], data:$dataArray);
        $this->translationRepo->update(request:$request, model:'App\Models\SangeetCategory', id:$request['id']);

        Toastr::success(translate('sangeetcategory_updated_successfully'));
        Helpers::editDeleteLogs('Sangeet','Sangeet Category','Update');
        return redirect()->route('admin.sangeetcategory.view');
    }



public function delete(Request $request, SangeetCategoryService $sangeetcategoryService): JsonResponse
{
    $sangeetcategory = $this->sangeetcategoryRepo->getFirstWhere(['id' => $request->input('id')]);

    if (!$sangeetcategory) {
        return response()->json(['error' => translate('sangeetcategory not found')], 404);
    }

    $this->sangeetcategoryRepo->delete(['id' => $request->input('id')]);
    $sangeetcategoryService->deleteImage(data:$sangeetcategory);
    $this->translationRepo->delete(model: 'App\Models\SangeetCategory', id: $request->input('id'));
    Helpers::editDeleteLogs('Sangeet','Sangeet Category','Delete');
    return response()->json(['message' => translate('sangeetcategory deleted successfully')]);
}


}
