<?php

namespace App\Http\Controllers\Admin\Astrology;

use App\Contracts\Repositories\RashiRepositoryInterface;
//use App\Contracts\Repositories\AstrologyRepositoryInterface;
use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Enums\ExportFileNames\Admin\Rashi as RashiExport;
use App\Enums\ViewPaths\Admin\Rashi;
use App\Exports\RashiListExport;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\RashiAddRequest;
use App\Http\Requests\Admin\RashiUpdateRequest;
use App\Services\RashiService;
use App\Utils\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class RashiController extends BaseController
{
    public function __construct(
        private readonly RashiRepositoryInterface           $rashiRepo,
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
        $rashis = $this->rashiRepo->getListWhere(orderBy:['id'=>'desc'],searchValue:$request->get('searchValue'), dataLimit: getWebConfig(name: 'pagination_limit'));
        return view(Rashi::LIST[VIEW], compact('rashis'));
    }

    public function getAddView(): View
    {
        $language = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $language[0];
        return view(Rashi::ADD[VIEW], compact( 'language', 'defaultLanguage'));
    }

    public function getUpdateView(string|int $id): View|RedirectResponse
    {
        $rashi = $this->rashiRepo->getFirstWhere(params:['id'=>$id], relations: ['translations']);
        $language = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $language[0];
        return view(Rashi::UPDATE[VIEW], compact('rashi', 'language', 'defaultLanguage'));
    }

    public function updateStatus(Request $request): JsonResponse
    {
        $data = [
          'status' => $request->get('status', 0),
        ];
        $this->rashiRepo->update(id:$request['id'], data:$data);
        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }

    public function delete(Request $request, RashiService $rashiService): RedirectResponse
    {
        $this->astrologyRepo->updateByParams(params:['rashi_id'=>$request['id']],data:['rashi_id' =>$request['rashi_id'],'sub_category_id'=>null,'sub_sub_category_id'=>null]);
        $rashi = $this->rashiRepo->getFirstWhere(params:['id'=>$request['id']]);
        $rashiService->deleteImage(data:$rashi);
        $this->translationRepo->delete(model:'App\Models\rashi', id:$request['id']);
        $this->rashiRepo->delete(params: ['id'=>$request['id']]);
        Toastr::success(translate('rashi_deleted_successfully'));
        return redirect()->back();
    }


    public function add(RashiAddRequest $request, RashiService $rashiService): RedirectResponse
    {
        $dataArray = $rashiService->getAddData(request:$request);
        $savedAttributes = $this->rashiRepo->add(data:$dataArray);
        $this->translationRepo->add(request:$request, model:'App\Models\Rashi', id:$savedAttributes->id);

        Toastr::success(translate('rashi_added_successfully'));
        Helpers::editDeleteLogs('Rashi','Rashi','Insert');
        return redirect()->route('admin.rashi.list');
    }

    public function update(RashiUpdateRequest $request, $id, RashiService $rashiService): RedirectResponse
    {
        $rashi = $this->rashiRepo->getFirstWhere(params:['id'=>$request['id']]);
        $dataArray = $rashiService->getUpdateData(request: $request, data:$rashi);
        $this->rashiRepo->update(id:$request['id'], data:$dataArray);
        $this->translationRepo->update(request:$request, model:'App\Models\Rashi', id:$request['id']);
        Helpers::editDeleteLogs('Rashi','Rashi','Update');
        Toastr::success(translate('rashi_updated_successfully'));
        return redirect()->route('admin.rashi.list');
    }

    public function exportList(Request $request): BinaryFileResponse
    {
        $rashi = $this->rashiRepo->getListWhere(searchValue:$request->get('searchValue'), dataLimit: 'all');
        $active = $this->rashiRepo->getListWhere(filters:['status'=>1], dataLimit: 'all')->count();
        $inactive = $this->rashiRepo->getListWhere(filters:['status'=>0], dataLimit: 'all')->count();
        return Excel::download(new rashiListExport(
            [
                'rashi'=> $rashi,
                'search' => $request['search'] ,
                'active' => $active,
                'inactive' => $inactive,
            ]), RashiExport::EXPORT_XLSX) ;
    }
}
