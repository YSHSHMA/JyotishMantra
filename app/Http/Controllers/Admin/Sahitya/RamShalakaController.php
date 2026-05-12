<?php

namespace App\Http\Controllers\Admin\Sahitya;

use App\Contracts\Repositories\RamShalakaRepositoryInterface;
use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Enums\ViewPaths\Admin\RamShalaka;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\RamShalakaAddRequest;
use App\Http\Requests\Admin\RamShalakaUpdateRequest;
use App\Http\Resources\RamShalakaResource;
use App\Traits\PaginatorTrait;
use App\Services\RamShalakaService;
use App\Utils\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class RamShalakaController extends BaseController
{
    use PaginatorTrait;

    public function __construct(
        private readonly RamShalakaRepositoryInterface      
         $ramshalakaRepo,
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
        $ramshalaka = $this->ramshalakaRepo->getListWhere(orderBy:['id'=>'desc'],searchValue:$request->get('searchValue'), dataLimit: getWebConfig(name: 'pagination_limit'));
        return view(RamShalaka::LIST[VIEW], compact('ramshalaka'));
    }

    public function getAddView(Request $request): View
    {
        $ramshalakas = $this->ramshalakaRepo->getListWhere(
            searchValue: $request->get('searchValue'),
            dataLimit: getWebConfig(name: 'pagination_limit')
        );

        $language = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $language[0];


        // Determine the view to use based on conditions (if any)
        $view = RamShalaka::LIST[VIEW]; // Example view selection, adjust as needed

        return view($view, compact('ramshalakas', 'language', 'defaultLanguage'));
    }


 public function getUpdateView(string|int $id): View|RedirectResponse
    {
        $ramshalaka = $this->ramshalakaRepo->getFirstWhere(params:['id'=>$id], relations: ['translations']);
        $language = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $language[0];
        return view(RamShalaka::UPDATE[VIEW], compact('ramshalaka', 'language', 'defaultLanguage'));
    }

    public function updateStatus(Request $request): JsonResponse
    {
        $data = [
          'status' => $request->get('status', 0),
        ];
        $this->ramshalakaRepo->update(id:$request['id'], data:$data);
        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }

 public function add(RamShalakaAddRequest $request, RamShalakaService $ramshalakaService): RedirectResponse
    {
        $dataArray = $ramshalakaService->getAddData(request:$request);
        $savedAttributes = $this->ramshalakaRepo->add(data:$dataArray);
        $this->translationRepo->add(request:$request, model:'App\Models\RamShalaka', id:$savedAttributes->id);

        Toastr::success(translate('ramshalaka_added_successfully'));
        return redirect()->route('admin.ramshalaka.view');
    }



 public function update(RamShalakaUpdateRequest $request, $id, RamShalakaService $ramshalakaService): RedirectResponse
    {
        $ramshalaka = $this->ramshalakaRepo->getFirstWhere(params:['id'=>$request['id']]);
        $dataArray = $ramshalakaService->getUpdateData(request: $request, data:$ramshalaka);
        $this->ramshalakaRepo->update(id:$request['id'], data:$dataArray);
        $this->translationRepo->update(request:$request, model:'App\Models\ramshalaka', id:$request['id']);

        Toastr::success(translate('ramshalaka_updated_successfully'));
        return redirect()->route('admin.ramshalaka.view');
    }



    public function delete(Request $request, RamShalakaService $ramshalakaService): JsonResponse
    {
        $ramshalaka = $this->ramshalakaRepo->getFirstWhere(['id' => $request->input('id')]);

        if (!$ramshalaka) {
            return response()->json(['error' => translate('ramshalaka not found')], 404);
        }

        $this->ramshalakaRepo->delete(['id' => $request->input('id')]);
        $ramshalakaService->deleteImage(data:$ramshalaka);
        $this->translationRepo->delete(model:'App\Models\RamShalaka', id:$request['id']);
        return response()->json(['message' => translate('ramshalaka deleted successfully')]);
    }



}
