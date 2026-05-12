<?php

namespace App\Http\Controllers\Admin\Astrology;

use App\Contracts\Repositories\MasikRashiRepositoryInterface;
use App\Contracts\Repositories\RashiRepositoryInterface;
//use App\Contracts\Repositories\AstrologyRepositoryInterface;
use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Enums\ExportFileNames\Admin\MasikRashi as MasikRashiExport;
use App\Enums\ViewPaths\Admin\MasikRashi;
use App\Exports\MasikRashiListExport;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\MasikRashiAddRequest;
use App\Http\Requests\Admin\MasikRashiUpdateRequest;
use App\Services\MasikRashiService;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class oldMasikRashiController extends BaseController
{
    public function __construct(
        private readonly RashiRepositoryInterface         $rashiRepo,
        private readonly MasikRashiRepositoryInterface           $masikrashiRepo,
        //private readonly AstrologyRepositoryInterface           $astrologyRepo,
        private readonly TranslationRepositoryInterface     $translationRepo,
    ) {}

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
        $masikrashis = $this->masikrashiRepo->getListWhere(orderBy: ['id' => 'desc'], searchValue: $request->get('searchValue'), dataLimit: getWebConfig(name: 'pagination_limit'));
        return view(MasikRashi::LIST[VIEW], compact('masikrashis'));
    }

    public function getAddView(): View
    {
        $rashis = $this->rashiRepo->getListWhere(filters: ['status' => 1], dataLimit: 'all');
        //dd($rashis);
        $language = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $language[0];
        return view(MasikRashi::ADD[VIEW], compact('language', 'defaultLanguage', 'rashis'));
    }

    public function getUpdateView(string|int $id): View|RedirectResponse
    {
        $masikrashi = $this->masikrashiRepo->getFirstWhere(params: ['id' => $id], relations: ['translations']);
        $language = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $language[0];
        return view(MasikRashi::UPDATE[VIEW], compact('masikrashi', 'language', 'defaultLanguage'));
    }

    public function updateStatus(Request $request): JsonResponse
    {
        $data = [
            'status' => $request->get('status', 0),
        ];
        $this->masikrashiRepo->update(id: $request['id'], data: $data);
        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }

    public function delete(Request $request, MasikRashiService $masikrashiService): RedirectResponse
    {
        $this->astrologyRepo->updateByParams(params: ['masikrashi_id' => $request['id']], data: ['masikrashi_id' => $request['masikrashi_id'], 'sub_category_id' => null, 'sub_sub_category_id' => null]);
        $masikrashi = $this->masikrashiRepo->getFirstWhere(params: ['id' => $request['id']]);
        $masikrashiService->deleteImage(data: $masikrashi);
        $this->translationRepo->delete(model: 'App\Models\masikrashi', id: $request['id']);
        $this->masikrashiRepo->delete(params: ['id' => $request['id']]);
        Toastr::success(translate('masikrashi_deleted_successfully'));
        return redirect()->back();
    }


    public function add(MasikRashiAddRequest $request, MasikRashiService $masikrashiService): RedirectResponse
    {
        $dataArray = $masikrashiService->getAddData(request: $request);
        $savedAttributes = $this->masikrashiRepo->add(data: $dataArray);
        $this->translationRepo->add(request: $request, model: 'App\Models\MasikRashi', id: $savedAttributes->id);

        Toastr::success(translate('masikrashi_added_successfully'));
        return redirect()->route('admin.masikrashi.list');
    }

    public function update(MasikRashiUpdateRequest $request, $id, MasikRashiService $masikrashiService): RedirectResponse
    {
        $masikrashi = $this->masikrashiRepo->getFirstWhere(params: ['id' => $request['id']]);
        $dataArray = $masikrashiService->getUpdateData(request: $request, data: $masikrashi);
        $this->masikrashiRepo->update(id: $request['id'], data: $dataArray);
        $this->translationRepo->update(request: $request, model: 'App\Models\MasikRashi', id: $request['id']);

        Toastr::success(translate('masikrashi_updated_successfully'));
        return redirect()->route('admin.masikrashi.list');
    }

    public function exportList(Request $request): BinaryFileResponse
    {
        $masikrashi = $this->masikrashiRepo->getListWhere(searchValue: $request->get('searchValue'), dataLimit: 'all');
        $active = $this->masikrashiRepo->getListWhere(filters: ['status' => 1], dataLimit: 'all')->count();
        $inactive = $this->masikrashiRepo->getListWhere(filters: ['status' => 0], dataLimit: 'all')->count();
        return Excel::download(new masikrashiListExport(
            [
                'masikrashi' => $masikrashi,
                'search' => $request['search'],
                'active' => $active,
                'inactive' => $inactive,
            ]
        ), MasikRashiExport::EXPORT_XLSX);
    }
}