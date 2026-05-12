<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Repositories\EventCategoryRepositoryInterface;
use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Enums\ViewPaths\Admin\EventcategoryPath;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\EventCategoryRequest;
use App\Services\EventCategorySevice;
use App\Traits\FileManagerTrait;
use App\Utils\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventCategoryController extends Controller
{

    use FileManagerTrait;
    public function __construct(
        private readonly EventCategoryRepositoryInterface       $EventRepo,
        private readonly TranslationRepositoryInterface      $translationRepo,
    ) {
    }


    public function index()
    {
        $language = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $language[0];
        $category_list = $this->EventRepo->getList(dataLimit:10);
        return view(EventcategoryPath::ADD[VIEW], compact('language', 'defaultLanguage', 'category_list'));
    }

    public function store(EventCategoryRequest $request, EventCategorySevice $service)
    {
        $array = $service->getAddData($request);
        $insertID = $this->EventRepo->add(data: $array);
        $this->translationRepo->add(request: $request, model: 'App\Models\EventCategory', id: $insertID->id);
        Toastr::success(translate('Event_category_added_successfully'));
        Helpers::editDeleteLogs('Event','Category','Insert');
        return redirect()->route(EventcategoryPath::ADD[REDIRECT]);
    }

    public function changeStatus(Request $request):JsonResponse{
        $data['status'] = $request->get('status', 0);
        $this->EventRepo->update(id: $request['id'], data: $data);
        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }

    public function delete(Request $request,EventCategorySevice $service){
        $old_data = $this->EventRepo->getFirstWhere(params:['id'=>$request['id']]);
        if($old_data){
            $service->deleteImage($old_data);
            $this->EventRepo->delete(params:['id'=>$request['id']]);
            $this->translationRepo->delete(model:'App\Models\EventCategory',id:$request['id']);
            Toastr::success(translate('Event_category_deleted_successfully'));
            Helpers::editDeleteLogs('Event','Category','Delete');
        }else{
            Toastr::error(translate('Event_category_deleted_Failed'));
        }
        return redirect()->route(EventcategoryPath::ADD[REDIRECT]);
    }

    public function update(Request $request,$id){
        $old_data = $this->EventRepo->getFirstWhere(params:['id'=>$request['id']],relations:['translations']);
        $language = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $language[0];
        return view(EventcategoryPath::UPDATE[VIEW], compact('language', 'defaultLanguage', 'old_data'));
    }

    public function edit(EventCategoryRequest $request, EventCategorySevice $service,$id){
        $old_data = $this->EventRepo->getFirstWhere(params:['id'=>$id]);
        $array = $service->getUpdateData($request,$old_data);
        $insertID = $this->EventRepo->update(id:$id,data: $array);
        $this->translationRepo->update(request: $request, model: 'App\Models\EventCategory', id: $id);
        Toastr::success(translate('Event_category_updated_successfully'));
        Helpers::editDeleteLogs('Event','Category','Update');
        return redirect()->route(EventcategoryPath::ADD[REDIRECT]);
    }

}
