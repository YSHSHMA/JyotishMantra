<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Repositories\EventPackageRepositoryInterface;
use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Enums\ViewPaths\Admin\EventpackagePath;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\EventpackageRequest;
use App\Services\EventPackageSevice;
use App\Traits\FileManagerTrait;
use App\Utils\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class EventPackageController extends Controller
{

    use FileManagerTrait;
    public function __construct(
        private readonly EventPackageRepositoryInterface       $EventpackageRepo,
        private readonly TranslationRepositoryInterface      $translationRepo,
    ) {
    }

    public function index(Request $request)
    {
        $getData = $this->EventpackageRepo->getListWhere(orderBy: ['id' => 'desc'], searchValue: $request->get('searchValue'), dataLimit: getWebConfig(name: 'pagination_limit'));
        $language = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $language[0];
        return view(EventpackagePath::ADD[VIEW], compact('getData', 'language', 'defaultLanguage'));
    }

    public function store(EventpackageRequest $request, EventPackageSevice $service)
    {
        $Array = $service->getAddData($request);
        $insert = $this->EventpackageRepo->add(data: $Array);
        $this->translationRepo->add(request: $request, model: 'App\Models\EventPackage', id: $insert->id);
        Toastr::success(translate('Package_added_successfully'));
        Helpers::editDeleteLogs('Event','Package','Insert');
        return redirect()->route(EventpackagePath::ADD[REDIRECT]);
    }

    public function changeStatus(Request $request)
    {
        $data['status'] = $request->get('status', 0);
        $this->EventpackageRepo->update(id: $request['id'], data: $data);
        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }

    public function delete(Request $request, EventPackageSevice $service)
    {
        $old_data = $this->EventpackageRepo->getFirstWhere(params: ['id' => $request['id']]);
        if ($old_data) {
            $service->deleteImage($old_data['image']);
            $this->EventpackageRepo->delete(params: ['id' => $request['id']]);
            $this->translationRepo->delete('App\Models\EventPackage', $request['id']);
            Toastr::success(translate('package_Deleted_successfully'));
            Helpers::editDeleteLogs('Event','Package','Delete');
            return response()->json(['success' => 1, 'message' => translate('event_packages_deleted_successfully')], 200);
        } else {
            Toastr::error(translate('package_Deleted_Failed'));
            return response()->json(['success' => 0, 'message' => translate('Not_found_data')], 400);
        }
    }

    public function update(Request $request,$id){
        $getdata = $this->EventpackageRepo->getFirstWhere(params: ['id' => $id],relations:['translations']);
        $language = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $language[0];
        return view(EventpackagePath::UPDATE[VIEW],compact('language','defaultLanguage','getdata'));
    }
    
    public function edit(EventpackageRequest $request, EventPackageSevice $service,$id){
        $old_data = $this->EventpackageRepo->getFirstWhere(params: ['id' => $id]);
        $array = $service->updateData($request,$old_data);
        $this->EventpackageRepo->update(id:$id,data: $array);
        $this->translationRepo->update(request: $request, model: 'App\Models\EventPackage', id: $id);
        Toastr::success(translate('Package_update_successfully'));
        Helpers::editDeleteLogs('Event','Event','Update');
        return redirect()->route(EventpackagePath::ADD[REDIRECT]);
    }
}
