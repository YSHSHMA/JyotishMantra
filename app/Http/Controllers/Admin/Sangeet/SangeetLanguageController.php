<?php

namespace App\http\Controllers\Admin\Sangeet;

use App\Contracts\Repositories\SangeetLanguageRepositoryInterface;
use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Enums\ViewPaths\Admin\SangeetLanguage;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\SangeetLanguageAddRequest;
use App\Http\Requests\Admin\SangeetLanguageUpdateRequest;
use App\Http\Resources\SangeetLanguageResource;
use App\Traits\PaginatorTrait;
use App\Services\SangeetLanguageService;
use App\Utils\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SangeetLanguageController extends BaseController
{
    use PaginatorTrait;

    public function __construct(
        private readonly SangeetLanguageRepositoryInterface       $sangeetlanguageRepo,
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
        $sangeetlanguages = $this->sangeetlanguageRepo->getList(dataLimit: 'all');
        return response()->json(SangeetLanguageResource::collection($sangeetlanguages));
    }

public function getAddView(Request $request): View
{
    $sangeetlanguages = $this->sangeetlanguageRepo->getListWhere(
        searchValue: $request->get('searchValue'),
        dataLimit: getWebConfig(name: 'pagination_limit')
    );

    $language = getWebConfig(name: 'pnc_language') ?? null;
    $defaultLanguage = $language[0];
    // Determine the view to use based on conditions (if any)
    $view = SangeetLanguage::LIST[VIEW]; // Example view selection, adjust as needed

    return view($view, compact('sangeetlanguages', 'language', 'defaultLanguage'));
}


public function getUpdateView(string|int $id): View
{
    $sangeetlanguage = $this->sangeetlanguageRepo->getFirstWhere(
        params: ['id' => $id],
        relations: ['translations']
    );

    $language = getWebConfig(name: 'pnc_language') ?? null;
    $defaultLanguage = $language[0];
    // Determine the view to use based on conditions (if any)
    $view = SangeetLanguage::UPDATE[VIEW]; // Example view selection, adjust as needed

    return view($view, compact('sangeetlanguage', 'language', 'defaultLanguage'));
}

       public function updateStatus(Request $request): JsonResponse
    {
        $data = [
            'status' => $request->get('status', 0),
        ];
        $this->sangeetlanguageRepo->update(id: $request['id'], data: $data);
        
        return response()->json(['success' => true, 'message' => translate('status_updated_successfully')]);
    }

public function add(SangeetLanguageAddRequest $request, SangeetLanguageService $sangeetLanguageService): RedirectResponse
{
     $dataArray = $sangeetLanguageService->getAddData(request:$request);
        $savedAttributes = $this->sangeetlanguageRepo->add(data:$dataArray);
        $this->translationRepo->add(request:$request, model:'App\Models\SangeetLanguage', id:$savedAttributes->id);

    Toastr::success(translate('sangeetlanguage_added_successfully'));
    Helpers::editDeleteLogs('Sangeet','Sangeet Language','Insert');
    return redirect()->route('admin.sangeetlanguage.view');
}



 public function update(SangeetLanguageUpdateRequest $request, $id, SangeetLanguageService $sangeetLanguageService): RedirectResponse
    {
        $sangeet = $this->sangeetlanguageRepo->getFirstWhere(params:['id'=>$request['id']]);
        $dataArray = $sangeetLanguageService->getUpdateData(request: $request, data:$sangeet);
        $this->sangeetlanguageRepo->update(id:$request['id'], data:$dataArray);
        $this->translationRepo->update(request:$request, model:'App\Models\SangeetLanguage', id:$request['id']);

        Toastr::success(translate('sangeetlanguage_updated_successfully'));
        Helpers::editDeleteLogs('Sangeet','Sangeet Language','Update');
        return redirect()->route('admin.sangeetlanguage.view');
    }



public function delete(Request $request): JsonResponse
{
    $sangeetlanguage = $this->sangeetlanguageRepo->getFirstWhere(['id' => $request->input('id')]);

    if (!$sangeetlanguage) {
        return response()->json(['error' => translate('Sangeet language not found')], 404);
    }

    $this->sangeetlanguageRepo->delete(['id' => $request->input('id')]);
    $this->translationRepo->delete(model: 'App\Models\SangeetLanguage', id: $request->input('id'));

    Helpers::editDeleteLogs('Sangeet','Sangeet Language','Delete');
    return response()->json(['message' => translate('Language deleted successfully')]);
}


}

