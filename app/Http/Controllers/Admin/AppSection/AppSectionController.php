<?php

namespace App\Http\Controllers\Admin\AppSection;

use App\Contracts\Repositories\AppSectionRepositoryInterface;
use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Enums\ViewPaths\Admin\AppSection;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\AppSectionAddRequest;
use App\Http\Requests\Admin\AppSectionUpdateRequest;
use App\Http\Resources\AppSectionResource;
use App\Traits\PaginatorTrait;
use App\Services\AppSectionService;
use App\Utils\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AppSectionController extends BaseController
{
    use PaginatorTrait;

    public function __construct(
        private readonly AppSectionRepositoryInterface       $appsectionRepo,
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
        $appsections = $this->appsectionRepo->getList(dataLimit: 'all');
        return response()->json(AppSectionResource::collection($appsections));
    }

    public function getAddView(Request $request): View
    {
        $appsections = $this->appsectionRepo->getListWhere(
            searchValue: $request->get('searchValue'),
            dataLimit: getWebConfig(name: 'pagination_limit')
        );

        $language = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $language[0];


        // Determine the view to use based on conditions (if any)
        $view = AppSection::LIST[VIEW]; // Example view selection, adjust as needed

        return view($view, compact('appsections', 'language', 'defaultLanguage',));
    }


    public function getUpdateView(string|int $id): View
    {
        $appsection = $this->appsectionRepo->getFirstWhere(
            params: ['id' => $id],
            relations: ['translations']
        );

        $language = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $language[0];

        // Determine the view to use based on conditions (if any)
        $view = AppSection::UPDATE[VIEW]; // Example view selection, adjust as needed

        return view($view, compact('appsection', 'language', 'defaultLanguage'));
    }

       public function updateStatus(Request $request): JsonResponse
    {
        $data = [
            'status' => $request->get('status', 'false'),
        ];
        $this->appsectionRepo->update(id: $request['id'], data: $data);
        
        return response()->json(['success' => true, 'message' => translate('status_updated_successfully')]);
    }

    public function add(AppSectionAddRequest $request, AppSectionService $appSectionService): RedirectResponse
    {
         $dataArray = $appSectionService->getAddData(request:$request);
            $savedAttributes = $this->appsectionRepo->add(data:$dataArray);
            $this->translationRepo->add(request:$request, model:'App\Models\AppSection', id:$savedAttributes->id);

        Toastr::success(translate('appsection_added_successfully'));
        Helpers::editDeleteLogs('App Section','App Section','Insert');
        return redirect()->route('admin.appsection.view');
    }



     public function update(AppSectionUpdateRequest $request, $id, AppSectionService $appSectionService): RedirectResponse
        {
            $app = $this->appsectionRepo->getFirstWhere(params:['id'=>$request['id']]);
            $dataArray = $appSectionService->getUpdateData(request: $request, data:$app);
            $this->appsectionRepo->update(id:$request['id'], data:$dataArray);
            $this->translationRepo->update(request:$request, model:'App\Models\AppSection', id:$request['id']);

            Toastr::success(translate('appsection_updated_successfully'));
            Helpers::editDeleteLogs('App Section','App Section','Update');
            return redirect()->route('admin.appsection.view');
        }



    public function delete(Request $request): JsonResponse
    {
        $appsection = $this->appsectionRepo->getFirstWhere(['id' => $request->input('id')]);

        if (!$appsection) {
            return response()->json(['error' => translate(' not found')], 404);
        }

        $this->appsectionRepo->delete(['id' => $request->input('id')]);
        $this->translationRepo->delete(model: 'App\Models\AppSection', id: $request->input('id'));
        Helpers::editDeleteLogs('App Section','App Section','Delete');
        return response()->json(['message' => translate('deleted successfully')]);
    }

}
