<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Repositories\TempleCategoriesRepositoryInterface;
use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Enums\ViewPaths\Admin\TempleCategoryEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TempleCategoryAddRequest;
use App\Services\TempleCategoryService;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

use App\Traits\FileManagerTrait;
use App\Utils\Helpers;
use Illuminate\Http\JsonResponse;

class TempleCategoryController extends Controller
{
    use FileManagerTrait;
    public function __construct(
    private readonly TempleCategoriesRepositoryInterface       $templecateRepo,
    private readonly TranslationRepositoryInterface      $translationRepo,
    ){}

    public function index(){
        $language = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $language[0];
        return view(TempleCategoryEnum::ADD[VIEW],compact('language','defaultLanguage'));
    }

    public function add_category(TempleCategoryAddRequest $request,TempleCategoryService $templeCategoryService):RedirectResponse{
       $checkData = $this->templecateRepo->getFirstWhere(['name'=>$request->name[0]]);
        if(!$checkData){
            $dataArray = $templeCategoryService->getAddData($request);
            $insertId=  $this->templecateRepo->add(data:$dataArray);
            $this->translationRepo->add(request:$request, model:'App\Models\TempleCategory', id:$insertId->id);
            Toastr::success(translate('Temple_Category_added_successfully'));
            Helpers::editDeleteLogs('Temple','Temple Category','Insert');
            return redirect()->route(TempleCategoryEnum::LIST[REDIRECT]);
        }else{
            Toastr::error(translate('already_exists_category_name'));
             return redirect()->route(TempleCategoryEnum::ADD[REDIRECT]);
        }
    }

    public function list(Request $request){
        $list =  $this->templecateRepo->getListwhere(orderBy:['id'=>'desc'],searchValue:$request->get('searchValue'),dataLimit: getWebConfig(name: 'pagination_limit'));
        return view(TempleCategoryEnum::LIST[VIEW],compact('list'));
    }

    public function getUpdateView(Request $request,$id){
        $language = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $language[0];
        $getData = $this->templecateRepo->getFirstWhere(params:['id'=>$id],relations:['translations']);
        return view(TempleCategoryEnum::UPDATE[VIEW],compact('language','getData','defaultLanguage'));
    }
    public function update(TempleCategoryAddRequest $request,TempleCategoryService $templeCategoryService,int|string $id): RedirectResponse{
        $old_data = $this->templecateRepo->getFirstWhere(params:['id'=>$id]);
        $dataArray = $templeCategoryService->getUpdateData($request,$old_data);
        $insertId=  $this->templecateRepo->update(id:$id,data:$dataArray);
        $this->translationRepo->update(request:$request, model:'App\Models\TempleCategory', id:$id);
        Toastr::success(translate('Temple_Category_Update_successfully'));
        Helpers::editDeleteLogs('Temple','Temple Category','Update');
        return redirect()->route(TempleCategoryEnum::LIST[REDIRECT]);
    }
    public function delete(Request $request,TempleCategoryService $templeCategoryService,$id):RedirectResponse{
        $old_data = $this->templecateRepo->getFirstWhere(params:['id'=>$id]);
        if($old_data){
            $templeCategoryService->removeImage($old_data);
            $this->templecateRepo->delete(params:['id'=>$id]);
            $this->translationRepo->delete('App\Models\TempleCategory',$id);
            Toastr::success(translate('Temple_Category_Deleted_successfully'));
            Helpers::editDeleteLogs('Temple','Temple Category','Delete');
        }else{
            Toastr::error(translate('Temple_Category_Deleted_Failed'));
        }
        return redirect()->route(TempleCategoryEnum::LIST[REDIRECT]);
    }

    public function updateStatus(Request $request):JsonResponse{
        $data['status'] = $request->get('status',0);
        $this->templecateRepo->update(id:$request['id'], data:$data);
        
        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }
}
