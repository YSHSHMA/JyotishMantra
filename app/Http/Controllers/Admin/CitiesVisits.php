<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Repositories\CitiesVisitRepositoryInterface;
use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Enums\ViewPaths\Admin\CitiesVisits as AdminCitiesVisits;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Cities_visitsRequest;
use App\Models\CitiesVisits as ModelsCitiesVisits;
use App\Services\Cities_visitsService;
use Brian2694\Toastr\Facades\Toastr;
use App\Traits\FileManagerTrait;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CitiesVisits extends Controller
{
    use FileManagerTrait {
        delete as deleteFile;
        update as updateFile;
    }
    public function __construct(
        private readonly CitiesVisitRepositoryInterface  $citiesVisitRepo,
        private readonly TranslationRepositoryInterface  $translationRepo,

    ) {
    }

    public function list(Request $request, $id)
    {
        $language = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $language[0];
        $id = $id;
        $list  = $this->citiesVisitRepo->getListWhere(orderBy: ['id' => 'desc'], searchValue: $request->get('searchValue'), filters: ['citie_id' => $id], dataLimit: getWebConfig(name: 'pagination_limit'));         
        return view(AdminCitiesVisits::LIST[VIEW], compact('language', 'defaultLanguage', 'list','id'));
    }


    public function store(Cities_visitsRequest $request, Cities_visitsService $CitiesVisitAdd): RedirectResponse
    {

        $dataArray = $CitiesVisitAdd->getAddData($request);
        $savedCities = $this->citiesVisitRepo->add(data: $dataArray);
        $this->translationRepo->add(request: $request, model: 'App\Models\CitiesVisits', id: $savedCities->id);
        Toastr::success(translate('cities_Visit_added_successfully'));
        return redirect()->route('admin.citie_visit.list',['id'=>$request->citie_id]);
    }


    public function update(Request $request)
    {
        $list['translation'] = $this->citiesVisitRepo->getFirstWhere(params:['id'=>$request['id']], relations: ['translations']);
        $language = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $language[0];
        $id = $request['id'];
        return view(AdminCitiesVisits::UPDATE[VIEW], compact('language', 'defaultLanguage', 'list','id'));
    }

    public function edit_function(Cities_visitsRequest $request,Cities_visitsService $CitiesVisitAdd)
    {
        // dd($request->id);
        $old_data = $this->citiesVisitRepo->getFirstWhere(params:['id'=>$request['id']]);
        $dataArray = $CitiesVisitAdd->getUpdateData($request,$old_data);
        $this->citiesVisitRepo->update($request->id,data: $dataArray);
        $this->translationRepo->update(request: $request, model: 'App\Models\CitiesVisits', id: $request->id);
        Toastr::success(translate('cities_Visit_Update_successfully'));
        $getData = $this->citiesVisitRepo->getFirstWhere(['id'=>$request->id],[]);
        return redirect()->route('admin.citie_visit.list',['id'=>$getData->citie_id]);
    }

    public function delete_citie_visit(Request $request,Cities_visitsService $CitiesVisitAdd){
        // dd($request);
        $getData = $this->citiesVisitRepo->getFirstWhere(['id'=>$request->id],[]);
        $CitiesVisitAdd->deleteImage($getData);               
        $this->citiesVisitRepo->delete(['id'=>$request->id]);
        $this->translationRepo->delete('App\Models\CitiesVisits',$request->id);
        Toastr::success(translate('cities_Visit_deletd_successfully'));
        return redirect()->route('admin.citie_visit.list',['id'=>$getData->citie_id]);
    }
}
