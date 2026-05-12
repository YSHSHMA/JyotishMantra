<?php

namespace App\Http\Controllers\Admin\Astrology;

use App\Contracts\Repositories\FastFestivalRepositoryInterface;
//use App\Contracts\Repositories\AstrologyRepositoryInterface;
//use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Enums\ExportFileNames\Admin\FastFestival as FastFestivalExport;
use App\Enums\ViewPaths\Admin\FastFestival;
use App\Exports\FastFestivalListExport;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\FastFestivalAddRequest;
use App\Http\Requests\Admin\FastFestivalUpdateRequest;
use App\Services\FastFestivalService;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\FastFestivalHindiMonth;
use App\Utils\Helpers;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FastFestivalController extends BaseController
{
    public function __construct(
        private readonly FastFestivalRepositoryInterface           $fastfestivalRepo,
        //private readonly AstrologyRepositoryInterface           $astrologyRepo,
       // private readonly TranslationRepositoryInterface     $translationRepo,
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
        $fastfestivals = $this->fastfestivalRepo->getListWhere(orderBy:['id'=>'desc'],searchValue:$request->get('searchValue'), dataLimit: getWebConfig(name: 'pagination_limit'));
        return view(FastFestival::LIST[VIEW], compact('fastfestivals'));
    }

    public function getAddView(): View
    {
     
        $language = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $language[0];
        return view(FastFestival::ADD[VIEW], compact( 'language', 'defaultLanguage'));
    }

    public function getUpdateView(string|int $id): View|RedirectResponse
    {
        $fastfestival = $this->fastfestivalRepo->getFirstWhere(params:['id'=>$id]);
        $language = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $language[0];
        return view(FastFestival::UPDATE[VIEW], compact('fastfestival', 'language', 'defaultLanguage'));
    }

    public function updateStatus(Request $request): JsonResponse
    {
        $data = [
          'status' => $request->get('status', 0),
        ];
        $this->Repo->update(id:$request['id'], data:$data);
        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }

    public function delete(Request $request, FastFestivalService $Service): RedirectResponse
    {
        $this->astrologyRepo->updateByParams(params:['_id'=>$request['id']],data:['_id' =>$request['_id'],'sub_category_id'=>null,'sub_sub_category_id'=>null]);
        $fastfestival = $this->fastfestivalRepo->getFirstWhere(params:['id'=>$request['id']]);
        $fastfestivalService->deleteImage(data:$fastfestival);
        //$this->translationRepo->delete(model:'App\Models\FastFestival', id:$request['id']);
        $this->fastfestivalRepo->delete(params: ['id'=>$request['id']]);
        Toastr::success(translate('fastfestival_deleted_successfully'));
        return redirect()->back();
    }


    public function add(FastFestivalAddRequest $request, FastFestivalService $fastfestivalService): RedirectResponse
    {
        $dataArray = $fastfestivalService->getAddData(request:$request);
        $savedAttributes = $this->fastfestivalRepo->add(data:$dataArray);
        //$this->translationRepo->add(request:$request, model:'App\Models\FastFestival', id:$savedAttributes->id);

        Toastr::success(translate('fastfestival_added_successfully'));
        return redirect()->route('admin.fastfestival.list');
    }

    public function update(FastFestivalUpdateRequest $request, $id, FastFestivalService $fastfestivalService): RedirectResponse
    {
        $fastfestival = $this->fastfestivalRepo->getFirstWhere(params:['id'=>$request['id']]);
        $dataArray = $fastfestivalService->getUpdateData(request: $request, data:$fastfestival);
        $this->fastfestivalRepo->update(id:$request['id'], data:$dataArray);
        Helpers::editDeleteLogs('Fast Festival','Fast Festival','Update');

        Toastr::success(translate('fastfestival_updated_successfully'));
        return redirect()->route('admin.fastfestival.list');
    }

    public function exportList(Request $request): BinaryFileResponse
    {
        $fastfestival = $this->fastfestivalRepo->getListWhere(searchValue:$request->get('searchValue'), dataLimit: 'all');
        $active = $this->fastfestivalRepo->getListWhere(filters:['status'=>1], dataLimit: 'all')->count();
        $inactive = $this->fastfestivalRepo->getListWhere(filters:['status'=>0], dataLimit: 'all')->count();
        return Excel::download(new FastFestivalListExport(
            [
                'fastfestival'=> $fastfestival,
                'search' => $request['search'] ,
                'active' => $active,
                'inactive' => $inactive,
            ]), FastFestivalExport::EXPORT_XLSX) ;
    }
}
