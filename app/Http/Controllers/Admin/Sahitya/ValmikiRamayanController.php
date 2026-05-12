<?php

namespace App\Http\Controllers\Admin\Sahitya;

use App\Contracts\Repositories\ValmikiRamayanRepositoryInterface;
use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Enums\ViewPaths\Admin\ValmikiRamayan;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\ValmikiRamayanAddRequest;
use App\Http\Requests\Admin\ValmikiRamayanUpdateRequest;
use App\Http\Resources\ValmikiRamayanResource;
use App\Traits\PaginatorTrait;
use App\Services\ValmikiRamayanService;
use App\Utils\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class ValmikiRamayanController extends BaseController
{
    use PaginatorTrait;

    public function __construct(
        private readonly ValmikiRamayanRepositoryInterface      
         $valmikiramayanRepo,
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
        $valmikiramayan = $this->valmikiramayanRepo->getListWhere(orderBy:['id'=>'desc'],searchValue:$request->get('searchValue'), dataLimit: getWebConfig(name: 'pagination_limit'));
        return view(ValmikiRamayan::LIST[VIEW], compact('valmikiramayan'));
    }

    public function getAddView(Request $request): View
    {
        $valmikiramayans = $this->valmikiramayanRepo->getListWhere(
            searchValue: $request->get('searchValue'),
            dataLimit: getWebConfig(name: 'pagination_limit')
        );

        $language = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $language[0];


        // Determine the view to use based on conditions (if any)
        $view = ValmikiRamayan::LIST[VIEW]; // Example view selection, adjust as needed

        return view($view, compact('valmikiramayans', 'language', 'defaultLanguage'));
    }


 public function getUpdateView(string|int $id): View|RedirectResponse
    {
        $valmikiramayan = $this->valmikiramayanRepo->getFirstWhere(params:['id'=>$id], relations: ['translations']);
        $language = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $language[0];
        return view(ValmikiRamayan::UPDATE[VIEW], compact('valmikiramayan', 'language', 'defaultLanguage'));
    }

    public function updateStatus(Request $request): JsonResponse
    {
        $data = [
          'status' => $request->get('status', 0),
        ];
        $this->valmikiramayanRepo->update(id:$request['id'], data:$data);
        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }

 public function add(ValmikiRamayanAddRequest $request, ValmikiRamayanService $valmikiramayanService): RedirectResponse
    {
        $dataArray = $valmikiramayanService->getAddData(request:$request);
        $savedAttributes = $this->valmikiramayanRepo->add(data:$dataArray);
        $this->translationRepo->add(request:$request, model:'App\Models\ValmikiRamayan', id:$savedAttributes->id);

        Toastr::success(translate('valmikiramayan_added_successfully'));
        Helpers::editDeleteLogs('Sahitya','Valmiki Ramayan','Insert');
        return redirect()->route('admin.valmikiramayan.view');
    }



 public function update(ValmikiRamayanUpdateRequest $request, $id, ValmikiRamayanService $valmikiramayanService): RedirectResponse
    {
        $valmikiramayan = $this->valmikiramayanRepo->getFirstWhere(params:['id'=>$request['id']]);
        $dataArray = $valmikiramayanService->getUpdateData(request: $request, data:$valmikiramayan);
        $this->valmikiramayanRepo->update(id:$request['id'], data:$dataArray);
        $this->translationRepo->update(request:$request, model:'App\Models\ValmikiRamayan', id:$request['id']);

        Toastr::success(translate('valmikiramayan_updated_successfully'));
        Helpers::editDeleteLogs('Sahitya','Valmiki Ramayan','Update');
        return redirect()->route('admin.valmikiramayan.view');
    }



    public function delete(Request $request, ValmikiRamayanService $valmikiramayanService): JsonResponse
    {
        $valmikiramayan = $this->valmikiramayanRepo->getFirstWhere(['id' => $request->input('id')]);

        if (!$valmikiramayan) {
            return response()->json(['error' => translate('valmikiramayan not found')], 404);
        }

        $this->valmikiramayanRepo->delete(['id' => $request->input('id')]);
        $valmikiramayanService->deleteImage(data:$valmikiramayan);
        $this->translationRepo->delete(model:'App\Models\ValmikiRamayan', id:$request['id']);
        Helpers::editDeleteLogs('Sahitya','Valmiki Ramayan','Delete');
        return response()->json(['message' => translate('valmikiramayan deleted successfully')]);
    }



}
