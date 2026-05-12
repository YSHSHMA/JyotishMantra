<?php

namespace App\Http\Controllers\Admin\Temple;

// use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Contracts\Repositories\VisitRepositoryInterface;
use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Enums\ViewPaths\Admin\CitiesPath;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\VisitsAddRequest;
use App\Http\Requests\Admin\VisitsUpdateRequest;
use App\Services\VisitsService;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class VisitController extends BaseController
{
    public function __construct(
        private readonly VisitRepositoryInterface         $visitRepo,
        private readonly TranslationRepositoryInterface     $translationRepo,
    ) {
    }

    /**
     * @param Request|null $request
     * @param string|null $type
     * @return View Index function is the starting point of a controller
     * Index function is the starting point of a controller
     */

    public function index(Request|null $request, string $type = null): View
    {
        // return $this->getList($request);
        return $this->getAddView($request, $type);
    }

    public function getAddView(Request $request, $id): View
    {
        $visit = $this->visitRepo->getListWhere(orderBy: ['id' => 'desc'], searchValue: $request->get('searchValue'), filters: ['citie_id' => $id, 'status' => 1], dataLimit: getWebConfig(name: 'pagination_limit'));
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        // dd($visit);
        return view(CitiesPath::VISIT_LIST[VIEW], [
            'visit' => $visit,
            'languages' => $languages,
            'defaultLanguage' => $defaultLanguage,
            'id' => $id
        ]);
    }

    public function add(VisitsAddRequest $request, VisitsService $visitService): RedirectResponse
    {
        $dataArray = $visitService->getAddData(request: $request);
        $savedAttributes = $this->visitRepo->add(data: $dataArray);
        $this->translationRepo->add(request: $request, model: 'App\Models\CitiesVisit', id: $savedAttributes->id);

        Toastr::success(translate('visit_added_successfully'));
        return back();
    }

    public function getUpdateView(string|int $id): View|RedirectResponse
    {
        $visit = $this->visitRepo->getFirstWhere(params: ['id' => $id], relations: ['translations']);
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        return view(CitiesPath::VISIT_UPDATE[VIEW], compact('visit', 'languages', 'defaultLanguage'));
    }

    public function update(VisitsUpdateRequest $request, VisitsService $visitService): RedirectResponse
    {
        $visit = $this->visitRepo->getFirstWhere(params: ['id' => $request['id']]);
        $dataArray = $visitService->getUpdateData(request: $request, data: $visit);
        $this->visitRepo->update(id: $request['id'], data: $dataArray);
        $this->translationRepo->update(request: $request, model: 'App\Models\CitiesVisit', id: $request['id']);
        Toastr::success(translate('visit_updated_successfully'));
        return redirect()->route('admin.visit.list', $request->citie_id);
    }

    public function delete(string|int $id, VisitsService $visitService): RedirectResponse
    {
        $visit = $this->visitRepo->getFirstWhere(params: ['id' => $id]);
        if ($visit) {
            $this->translationRepo->delete(model: 'App\Models\CitiesVisit', id: $id);
            $this->visitRepo->delete(params: ['id' => $id]);
            Toastr::success(translate('visit_deleted_successfully'));
        } else {
            Toastr::error(translate('error occured'));
        }
        return redirect()->back();
    }
}