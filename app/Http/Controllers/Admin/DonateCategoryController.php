<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Repositories\DonateCategoryRepositoryInterface;
use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Enums\ViewPaths\Admin\DonateCategoryPath;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DonateCategoryRequest;
use App\Models\DonateCategory;
use App\Services\DonateCategoryService;
use App\Traits\FileManagerTrait;
use App\Utils\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class DonateCategoryController extends Controller
{
    use FileManagerTrait;
    public function __construct(
        private readonly TranslationRepositoryInterface     $translationRepo,
        private readonly DonateCategoryRepositoryInterface     $donatecategory,
    ) {}

    public function AddCategory(Request $request){
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        $getData = $this->donatecategory->getListWhere(orderBy: ['id' => 'desc'], searchValue: $request->get('searchValue'),filters:['types'=>'category'], dataLimit: getWebConfig(name: 'pagination_limit'));
        return view(DonateCategoryPath::ADDCATEGORY[VIEW], compact('getData','defaultLanguage','languages'));
    }

    public function StoreCategory(DonateCategoryRequest $request,DonateCategoryService $service){
        $dataArray = $service->getAddData($request);
        $insert = $this->donatecategory->add(data: $dataArray);
        $this->translationRepo->add(request: $request, model: 'App\Models\DonateCategory', id: $insert->id);
        Toastr::success(translate('Category_added_successfully'));
        Helpers::editDeleteLogs('Donate','Category','Insert');
        return redirect()->route(DonateCategoryPath::ADDCATEGORY[REDIRECT]);
    }

    public function CategoryStatus(Request $request){
        $data['status'] = $request->get('status', 0);
        $this->donatecategory->update(id: $request['id'], data: $data);
        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }

    public function CategoryDelete(Request $request,DonateCategoryService $service){
        $old_data = $this->donatecategory->getFirstWhere(params: ['id' => $request->get('id')]);
        if (!empty($old_data['image'])) {
            $service->deleteImage($old_data['image']);
        }
        $this->donatecategory->delete(params: ['id' => $request->get('id')]);
        $this->translationRepo->delete(model:'App\Models\DonateCategory',id: $request->get('id'));
        Toastr::success(translate('Category_Deleted_successfully'));
        Helpers::editDeleteLogs('Donate','Category','Delete');
        return redirect()->route(DonateCategoryPath::ADDCATEGORY[REDIRECT]);
    }

    public function CategoryUpdate(Request $request,$id){
        $old_data = $this->donatecategory->getFirstWhere(params: ['id' => $id],relations:['translations']);
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        return view(DonateCategoryPath::ADDCATEUPDATE[VIEW], compact('old_data','defaultLanguage','languages'));
    }

    public function CategoryUpdateSave(DonateCategoryRequest $request,DonateCategoryService $service){
        $old_data = $this->donatecategory->getFirstWhere(params: ['id' => $request->get('id')]);
        $dataArray = $service->getUpdateData($request,$old_data);
        $insert = $this->donatecategory->update(id:$request->get('id'),data: $dataArray);
        $this->translationRepo->update(request: $request, model: 'App\Models\DonateCategory', id: $request->get('id'));
        Toastr::success(translate('Category_Update_successfully'));
        Helpers::editDeleteLogs('Donate','Category','Update');
        return redirect()->route(DonateCategoryPath::ADDCATEGORY[REDIRECT]);
    }

}
