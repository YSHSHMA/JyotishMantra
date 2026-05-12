<?php

namespace App\Http\Controllers\Admin\Jaap;

use App\Contracts\Repositories\JaapRepositoryInterface;
use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Enums\ViewPaths\Admin\Jaap;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\JaapAddRequest;
use App\Http\Requests\Admin\JaapUpdateRequest;
use App\Http\Resources\JaapResource;
use App\Models\JaapCount;
use App\Traits\PaginatorTrait;
use App\Services\JaapService;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class jaapcontroller extends BaseController
{
    use PaginatorTrait;

    public function __construct(
        private readonly JaapRepositoryInterface      
         $jaapRepo,
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
        $jaap = $this->jaapRepo->getListWhere(orderBy:['id'=>'desc'],searchValue:$request->get('searchValue'), dataLimit: getWebConfig(name: 'pagination_limit'));
        return view(Jaap::LIST[VIEW], compact('jaap'));
    }

    public function getAddView(Request $request): View
    {
        $jaaps = $this->jaapRepo->getListWhere(
            searchValue: $request->get('searchValue'),
            dataLimit: getWebConfig(name: 'pagination_limit')
        );

        $language = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $language[0];

        $view = Jaap::LIST[VIEW]; 

        return view($view, compact('jaaps', 'language', 'defaultLanguage'));
    }


 public function getUpdateView(string|int $id): View|RedirectResponse
    {
        $jaap = $this->jaapRepo->getFirstWhere(params:['id'=>$id], relations: ['translations']);
        $language = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $language[0];
        return view(Jaap::UPDATE[VIEW], compact('jaap', 'language', 'defaultLanguage'));
    }

    public function updateStatus(Request $request): JsonResponse
    {
        $data = [
          'status' => $request->get('status', 0),
        ];
        $this->jaapRepo->update(id:$request['id'], data:$data);
        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }

 public function add(JaapAddRequest $request, JaapService $jaapService): RedirectResponse
    {
        $dataArray = $jaapService->getAddData(request:$request);
        $savedAttributes = $this->jaapRepo->add(data:$dataArray);
        $this->translationRepo->add(request:$request, model:'App\Models\Jaap', id:$savedAttributes->id);

        Toastr::success(translate('jaap_added_successfully'));
        return redirect()->route('admin.jaap.view');
    }



 public function update(JaapUpdateRequest $request, $id, JaapService $jaapService): RedirectResponse
    {
        $jaap = $this->jaapRepo->getFirstWhere(params:['id'=>$request['id']]);
        $dataArray = $jaapService->getUpdateData(request: $request, data:$jaap);
        $this->jaapRepo->update(id:$request['id'], data:$dataArray);
        $this->translationRepo->update(request:$request, model:'App\Models\Jaap', id:$request['id']);

        Toastr::success(translate('jaap_updated_successfully'));
        return redirect()->route('admin.jaap.view');
    }



    public function delete(Request $request, JaapService $jaapService): JsonResponse
    {
        $jaap = $this->jaapRepo->getFirstWhere(['id' => $request->input('id')]);

        if (!$jaap) {
            return response()->json(['error' => translate('jaap not found')], 404);
        }

        $this->jaapRepo->delete(['id' => $request->input('id')]);
        $jaapService->deleteImage(data:$jaap);
        $this->translationRepo->delete(model:'App\Models\Jaap', id:$request['id']);

        return response()->json(['message' => translate('jaap deleted successfully')]);
    }

    public function jaapUserList()
    {
        $jaapCounts = JaapCount::with('customer')->orderBy('id', 'desc')->paginate(10); 
        return view(Jaap::JAAPUSER[VIEW], compact('jaapCounts'));
    }

}

