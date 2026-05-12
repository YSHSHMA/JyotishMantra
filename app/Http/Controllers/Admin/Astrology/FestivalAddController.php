<?php

namespace App\Http\Controllers\Admin\Astrology;

use App\Contracts\Repositories\FestivalAddRepositoryInterface;
//use App\Contracts\Repositories\AstrologyRepositoryInterface;
use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Enums\ExportFileNames\Admin\FestivalAdd as FestivalAddExport;
use App\Enums\ViewPaths\Admin\FestivalAdd;
use App\Exports\FestivalAddListExport;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\FestivalAddAddRequest;
use App\Http\Requests\Admin\FestivalAdddUpdateRequest;
use App\Services\FestivalAddService;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FestivalAddController extends BaseController
{
    public function __construct(
        private readonly FestivalAddRepositoryInterface           $fastivaladdRepo,
        //private readonly AstrologyRepositoryInterface           $astrologyRepo,
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
        return $this->getList($request);
    }

    public function getList(Request $request): Application|Factory|View
    {
        $festivaladds = $this->festivaladdRepo->getListWhere(orderBy:['id'=>'desc'],searchValue:$request->get('searchValue'), dataLimit: getWebConfig(name: 'pagination_limit'));
        return view(FestivalAdd::LIST[VIEW], compact('festivaladds'));
    }

    public function getAddView(): View
    {
        $language = getWebConfig(name: 'pnc_language') ?? null;
        $defeultLanguage = $language[0];
        return view(FestivalAdd::ADD[VIEW], compact( 'language', 'defaultLanguage'));
    }

    public function getUpdateView(string|int $id): View|RedirectResponse
    {
        $festivaladd = $this->festivaladdRepo->getFirstWhere(params:['id'=>$id], relations: ['translations']);
        $language = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $language[0];
        return view(FestivalAdd::UPDATE[VIEW], compact('festivaladd', 'language', 'defaultLanguage'));
    }

    public function updateStatus(Request $request): JsonResponse
    {
        $data = [
          'status' => $request->get('status', 0),
        ];
        $this->festivaladdRepo->update(id:$request['id'], data:$data);
        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }

    public function delete(Request $request, FestivalAddService $festivaladdService): RedirectResponse
    {
        $this->astrologyRepo->updateByParams(params:['festivaladd_id'=>$request['id']],data:['festivaladd_id' =>$request['festivaladd_id'],'sub_category_id'=>null,'sub_sub_category_id'=>null]);
        $festivaladd = $this->festivaladdRepo->getFirstWhere(params:['id'=>$request['id']]);
        $festivaladdService->deleteImage(data:$festivaladd);
        $this->translationRepo->delete(model:'App\Models\FestivalAdd', id:$request['id']);
        $this->festivaladdRepo->delete(params: ['id'=>$request['id']]);
        Toastr::success(translate('festivaladd_deleted_successfully'));
        return redirect()->back();
    }


    public function add(FestivalAddRequest $request, FestivalAddService $festivaladdService): RedirectResponse
    {
        $dataArray = $festivaladdService->getAddData(request:$request);
        $savedAttributes = $this->festivaladdRepo->add(data:$dataArray);
        $this->translationRepo->add(request:$request, model:'App\Models\FestivalAdd', id:$savedAttributes->id);

        Toastr::success(translate('festivaladd_added_successfully'));
        return redirect()->route('admin.festivaladd.list');
    }

    public function update(FestivalAddUpdateRequest $request, $id, FestivalAddService $festivaladdService): RedirectResponse
    {
        $festivaladd = $this->festivaladdRepo->getFirstWhere(params:['id'=>$request['id']]);
        $dataArray = $festivaladdService->getUpdateData(request: $request, data:$festivaladd);
        $this->festivaladdRepo->update(id:$request['id'], data:$dataArray);
        $this->translationRepo->update(request:$request, model:'App\Models\FestivalAdd', id:$request['id']);

        Toastr::success(translate('festivaladd_updated_successfully'));
        return redirect()->route('admin.festivaladd.list');
    }

    public function exportList(Request $request): BinaryFileResponse
    {
        $festivaladd = $this->festivaladdRepo->getListWhere(searchValue:$request->get('searchValue'), dataLimit: 'all');
        $active = $this->festivaladdRepo->getListWhere(filters:['status'=>1], dataLimit: 'all')->count();
        $inactive = $this->festivaladdRepo->getListWhere(filters:['status'=>0], dataLimit: 'all')->count();
        return Excel::download(new FestivalAddListExport(
            [
                'festivaladd'=> $festivaladd,
                'search' => $request['search'] ,
                'active' => $active,
                'inactive' => $inactive,
            ]), FestivalAddExport::EXPORT_XLSX) ;
    }
}
