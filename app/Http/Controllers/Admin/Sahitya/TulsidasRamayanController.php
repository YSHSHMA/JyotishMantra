<?php

namespace App\Http\Controllers\Admin\Sahitya;

use App\Contracts\Repositories\TulsidasRamayanRepositoryInterface;
use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Enums\ViewPaths\Admin\TulsidasRamayan;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\TulsidasRamayanAddRequest;
use App\Http\Requests\Admin\TulsidasRamayanUpdateRequest;
use App\Http\Resources\TulsidasRamayanResource;
use App\Traits\PaginatorTrait;
use App\Services\TulsidasRamayanService;
use App\Utils\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class TulsidasRamayanController extends BaseController
{
    use PaginatorTrait;

    public function __construct(
        private readonly TulsidasRamayanRepositoryInterface      
         $tulsidasramayanRepo,
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
        $tulsidasramayan = $this->tulsidasramayanRepo->getListWhere(orderBy:['id'=>'desc'],searchValue:$request->get('searchValue'), dataLimit: getWebConfig(name: 'pagination_limit'));
        return view(TulsidasRamayan::LIST[VIEW], compact('tulsidasramayan'));
    }

    public function getAddView(Request $request): View
    {
        $tulsidasramayans = $this->tulsidasramayanRepo->getListWhere(
            searchValue: $request->get('searchValue'),
            dataLimit: getWebConfig(name: 'pagination_limit')
        );

        $language = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $language[0];


        // Determine the view to use based on conditions (if any)
        $view = TulsidasRamayan::LIST[VIEW]; // Example view selection, adjust as needed

        return view($view, compact('tulsidasramayans', 'language', 'defaultLanguage'));
    }


 public function getUpdateView(string|int $id): View|RedirectResponse
    {
        $tulsidasramayan = $this->tulsidasramayanRepo->getFirstWhere(params:['id'=>$id], relations: ['translations']);
        $language = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $language[0];
        return view(TulsidasRamayan::UPDATE[VIEW], compact('tulsidasramayan', 'language', 'defaultLanguage'));
    }

    public function updateStatus(Request $request): JsonResponse
    {
        $data = [
          'status' => $request->get('status', 0),
        ];
        $this->tulsidasramayanRepo->update(id:$request['id'], data:$data);
        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }

 public function add(TulsidasRamayanAddRequest $request, TulsidasRamayanService $tulsidasramayanService): RedirectResponse
    {
        $dataArray = $tulsidasramayanService->getAddData(request:$request);
        $savedAttributes = $this->tulsidasramayanRepo->add(data:$dataArray);
        $this->translationRepo->add(request:$request, model:'App\Models\TulsidasRamayan', id:$savedAttributes->id);

        Toastr::success(translate('tulsidasramayan_added_successfully'));
        Helpers::editDeleteLogs('Sahitya','Tulsidas Ramayan','Insert');
        return redirect()->route('admin.tulsidasramayan.view');
    }



 public function update(TulsidasRamayanUpdateRequest $request, $id, TulsidasRamayanService $tulsidasramayanService): RedirectResponse
    {
        $tulsidasramayan = $this->tulsidasramayanRepo->getFirstWhere(params:['id'=>$request['id']]);
        $dataArray = $tulsidasramayanService->getUpdateData(request: $request, data:$tulsidasramayan);
        $this->tulsidasramayanRepo->update(id:$request['id'], data:$dataArray);
        $this->translationRepo->update(request:$request, model:'App\Models\TulsidasRamayan', id:$request['id']);

        Toastr::success(translate('tulsidasramayan_updated_successfully'));
        Helpers::editDeleteLogs('Sahitya','Tulsidas Ramayan','Update');
        return redirect()->route('admin.tulsidasramayan.view');
    }



    public function delete(Request $request, TulsidasRamayanService $tulsidasramayanService): JsonResponse
    {
        $tulsidasramayan = $this->tulsidasramayanRepo->getFirstWhere(['id' => $request->input('id')]);

        if (!$tulsidasramayan) {
            return response()->json(['error' => translate('tulsidasramayan not found')], 404);
        }

        $this->tulsidasramayanRepo->delete(['id' => $request->input('id')]);
        $tulsidasramayanService->deleteImage(data:$tulsidasramayan);
        $this->translationRepo->delete(model:'App\Models\TulsidasRamayan', id:$request['id']);
        Helpers::editDeleteLogs('Sahitya','Tulsidas Ramayan','Delete');
        return response()->json(['message' => translate('tulsidasramayan deleted successfully')]);
    }



}
