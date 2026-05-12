<?php

namespace App\Http\Controllers\Admin\Sahitya;

use App\Contracts\Repositories\SahityaRepositoryInterface;
use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Enums\ViewPaths\Admin\Sahitya;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\SahityaAddRequest;
use App\Http\Requests\Admin\SahityaUpdateRequest;
use App\Http\Resources\SahityaResource;
use App\Traits\PaginatorTrait;
use App\Services\SahityaService;
use App\Utils\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;


class SahityaController extends BaseController
{
    use PaginatorTrait;

    public function __construct(
        private readonly SahityaRepositoryInterface      
         $sahityaRepo,
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

    public function getList(Request $request): Application|Factory|View
    {
        $sahitya = $this->sahityaRepo->getListWhere(orderBy:['id'=>'desc'],searchValue:$request->get('searchValue'), dataLimit: getWebConfig(name: 'pagination_limit'));
        return view(Sahitya::LIST[VIEW], compact('sahitya'));
    }

    public function getAddView(Request $request): View
    {
        $sahityas = $this->sahityaRepo->getListWhere(
            searchValue: $request->get('searchValue'),
            dataLimit: getWebConfig(name: 'pagination_limit')
        );

        $language = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $language[0];


        // Determine the view to use based on conditions (if any)
        $view = Sahitya::LIST[VIEW]; // Example view selection, adjust as needed

        return view($view, compact('sahityas', 'language', 'defaultLanguage'));
    }


 public function getUpdateView(string|int $id): View|RedirectResponse
    {
        $sahitya = $this->sahityaRepo->getFirstWhere(params:['id'=>$id], relations: ['translations']);
        $language = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $language[0];
        return view(Sahitya::UPDATE[VIEW], compact('sahitya', 'language', 'defaultLanguage'));
    }

    public function updateStatus(Request $request): JsonResponse
    {
        $data = [
          'status' => $request->get('status', 0),
        ];
        $this->sahityaRepo->update(id:$request['id'], data:$data);
        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }

 public function add(SahityaAddRequest $request, SahityaService $sahityaService): RedirectResponse
    {
        $dataArray = $sahityaService->getAddData(request:$request);
        $savedAttributes = $this->sahityaRepo->add(data:$dataArray);
        $this->translationRepo->add(request:$request, model:'App\Models\Sahitya', id:$savedAttributes->id);

        Toastr::success(translate('sahitya_added_successfully'));
        Helpers::editDeleteLogs('Sahitya','Sahitya','Insert');
        return redirect()->route('admin.sahitya.view');
    }



 public function update(SahityaUpdateRequest $request, $id, SahityaService $sahityaService): RedirectResponse
    {
        $sahitya = $this->sahityaRepo->getFirstWhere(params:['id'=>$request['id']]);
        $dataArray = $sahityaService->getUpdateData(request: $request, data:$sahitya);
        $this->sahityaRepo->update(id:$request['id'], data:$dataArray);
        $this->translationRepo->update(request:$request, model:'App\Models\Sahitya', id:$request['id']);

        Toastr::success(translate('sahitya_updated_successfully'));
        Helpers::editDeleteLogs('Sahitya','Sahitya','Update');
        return redirect()->route('admin.sahitya.view');
    }



public function delete(Request $request, SahityaService $sahityaService): JsonResponse
{
    $sahitya = $this->sahityaRepo->getFirstWhere(['id' => $request->input('id')]);

    if (!$sahitya) {
        return response()->json(['error' => translate('sahitya not found')], 404);
    }

    $this->sahityaRepo->delete(['id' => $request->input('id')]);
    $sahityaService->deleteImage(data:$sahitya);
    $this->translationRepo->delete(model:'App\Models\Sahitya', id:$request['id']);
    Helpers::editDeleteLogs('Sahitya','Sahitya','Delete');
    return response()->json(['message' => translate('sahitya deleted successfully')]);
}



}

