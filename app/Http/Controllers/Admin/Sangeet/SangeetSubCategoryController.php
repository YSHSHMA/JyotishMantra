<?php

namespace App\Http\Controllers\Admin\Sangeet;

use App\Contracts\Repositories\SangeetSubCategoryRepositoryInterface;
use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Enums\ViewPaths\Admin\SangeetSubCategory;
use App\Models\SangeetCategory;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\SangeetSubCategoryAddRequest;
use App\Http\Requests\Admin\SangeetSubCategoryUpdateRequest;
use App\Http\Resources\SangeetSubCategoryResource;
use App\Traits\PaginatorTrait;
use App\Services\SangeetSubCategoryService;
use App\Utils\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SangeetSubCategoryController extends BaseController
{
    use PaginatorTrait;

    public function __construct(
        private readonly SangeetSubCategoryRepositoryInterface       $sangeetsubcategoryRepo,
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
        $sangeetsubcategorys = $this->sangeetsubcategoryRepo->getList(dataLimit: 'all');
        return response()->json(SangeetSubCategoryResource::collection($sangeetsubcategorys));
    }

public function getAddView(Request $request): View
{
    $sangeetsubcategorys = $this->sangeetsubcategoryRepo->getListWhere(
        searchValue: $request->get('searchValue'),
        dataLimit: getWebConfig(name: 'pagination_limit')
    );

    $language = getWebConfig(name: 'pnc_language') ?? null;
    $defaultLanguage = $language[0];

    $sangeetCategories = SangeetCategory::all();

    // Determine the view to use based on conditions (if any)
    $view = SangeetSubCategory::LIST[VIEW]; // Example view selection, adjust as needed

    return view($view, compact('sangeetsubcategorys', 'language', 'defaultLanguage', 'sangeetCategories'));
}


public function getUpdateView(string|int $id): View
{
    $sangeetsubcategory = $this->sangeetsubcategoryRepo->getFirstWhere(
        params: ['id' => $id],
        relations: ['translations']
    );

    $language = getWebConfig(name: 'pnc_language') ?? null;
    $defaultLanguage = $language[0];
    $sangeetCategories = SangeetCategory::all(); // Fetch all sangeet categories

    // Determine the view to use based on conditions (if any)
    $view = SangeetSubCategory::UPDATE[VIEW]; // Example view selection, adjust as needed

    return view($view, compact('sangeetsubcategory', 'language', 'defaultLanguage', 'sangeetCategories'));
}

       public function updateStatus(Request $request): JsonResponse
    {
        $data = [
            'status' => $request->get('status', 0),
        ];
        $this->sangeetsubcategoryRepo->update(id: $request['id'], data: $data);
        
        return response()->json(['success' => true, 'message' => translate('status_updated_successfully')]);
    }

public function add(SangeetSubCategoryAddRequest $request, SangeetSubCategoryService $sangeetSubCategoryService): RedirectResponse
{
     $dataArray = $sangeetSubCategoryService->getAddData(request:$request);
        $savedAttributes = $this->sangeetsubcategoryRepo->add(data:$dataArray);
        $this->translationRepo->add(request:$request, model:'App\Models\SangeetSubCategory', id:$savedAttributes->id);

    Toastr::success(translate('sangeetsubcategory_added_successfully'));
    Helpers::editDeleteLogs('Sangeet','Sangeet Sub Category','Insert');
    return redirect()->route('admin.sangeetsubcategory.view');
}



 public function update(SangeetSubCategoryUpdateRequest $request, $id, SangeetSubCategoryService $sangeetSubCategoryService): RedirectResponse
    {
        $sangeet = $this->sangeetsubcategoryRepo->getFirstWhere(params:['id'=>$request['id']]);
        $dataArray = $sangeetSubCategoryService->getUpdateData(request: $request, data:$sangeet);
        $this->sangeetsubcategoryRepo->update(id:$request['id'], data:$dataArray);
        $this->translationRepo->update(request:$request, model:'App\Models\SangeetSubCategory', id:$request['id']);

        Toastr::success(translate('sangeetsubcategory_updated_successfully'));
        Helpers::editDeleteLogs('Sangeet','Sangeet Sub Category','Update');
        return redirect()->route('admin.sangeetsubcategory.view');
    }



public function delete(Request $request): JsonResponse
{
    $sangeetsubcategory = $this->sangeetsubcategoryRepo->getFirstWhere(['id' => $request->input('id')]);

    if (!$sangeetsubcategory) {
        return response()->json(['error' => translate('Sangeet category not found')], 404);
    }

    $this->sangeetsubcategoryRepo->delete(['id' => $request->input('id')]);
    $this->translationRepo->delete(model: 'App\Models\SangeetSubCategory', id: $request->input('id'));

    Helpers::editDeleteLogs('Sangeet','Sangeet Sub Category','Delete');
    return response()->json(['message' => translate('Subcategory deleted successfully')]);
}


}

