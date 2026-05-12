<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Repositories\DonateCategoryRepositoryInterface;
use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Enums\ViewPaths\Admin\DonateCategoryPath;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DonateCategoryRequest;
use App\Services\DonateCategoryService;
use App\Traits\FileManagerTrait;
use App\Utils\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class DonatePurposeController extends Controller
{
    
    use FileManagerTrait;
    public function __construct(
        private readonly TranslationRepositoryInterface     $translationRepo,
        private readonly DonateCategoryRepositoryInterface  $donatepur,
    ) {}

    public function AddPurpose(Request $request){
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        $getData = $this->donatepur->getListWhere(orderBy: ['id' => 'desc'], searchValue: $request->get('searchValue'), filters:['types'=>"porpose"], dataLimit: getWebConfig(name: 'pagination_limit'));
        return view(DonateCategoryPath::ADDPURPOSE[VIEW], compact('getData','defaultLanguage','languages'));
    }

    public function StorePurpose(DonateCategoryRequest $request,DonateCategoryService $service){
        $dataArray = $service->getPurposeAddData($request);
        $insert = $this->donatepur->add(data: $dataArray);
        $this->translationRepo->add(request: $request, model: 'App\Models\DonateCategory', id: $insert->id);
        Toastr::success(translate('Purpose_added_successfully'));
        Helpers::editDeleteLogs('Donate','Purpose','Insert');
        return redirect()->route(DonateCategoryPath::ADDPURPOSE[REDIRECT]);
    }

    public function PurposeStatus(Request $request){
        $data['status'] = $request->get('status', 0);
        $this->donatepur->update(id: $request['id'], data: $data);
        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);   
    }

    public function PurposeDelete(Request $request,DonateCategoryService $service){
        $old_data = $this->donatepur->getFirstWhere(params: ['id' => $request->get('id')]);
        if (!empty($old_data['image'])) {
            $service->deletePurposeImage($old_data['image']);
        }
        $this->donatepur->delete(params: ['id' => $request->get('id')]);
        $this->translationRepo->delete(model:'App\Models\DonateCategory',id: $request->get('id'));
        Toastr::success(translate('Purpose_Deleted_successfully'));
        Helpers::editDeleteLogs('Donate','Purpose','Delete');
        return redirect()->route(DonateCategoryPath::ADDPURPOSE[REDIRECT]);
    }

    public function PurposeUpdate(Request $request,$id){
        $old_data = $this->donatepur->getFirstWhere(params: ['id' => $id],relations:['translations']);
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        return view(DonateCategoryPath::ADDPURUPDATE[VIEW], compact('old_data','defaultLanguage','languages'));
    }

    public function PurposeUpdateSave(DonateCategoryRequest $request,DonateCategoryService $service){
        $old_data = $this->donatepur->getFirstWhere(params: ['id' => $request->get('id')]);
        $dataArray = $service->getPurposeUpdateData($request,$old_data);
        $insert = $this->donatepur->update(id:$request->get('id'),data: $dataArray);
        $this->translationRepo->update(request: $request, model: 'App\Models\DonateCategory', id: $request->get('id'));
        Toastr::success(translate('Purpose_Update_successfully'));
        Helpers::editDeleteLogs('Donate','Purpose','Update');
        return redirect()->route(DonateCategoryPath::ADDPURPOSE[REDIRECT]);
    }
}
