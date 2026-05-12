<?php

namespace App\Http\Controllers\Admin\Service;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\Foundation\Application;
use App\Enums\ViewPaths\Admin\FAQPath;
use App\Enums\WebConfigKey;
use App\Contracts\Repositories\CategoryRepositoryInterface;
use App\Contracts\Repositories\AttributeRepositoryInterface;
use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Contracts\Repositories\FaqRepositoryInterface;
use App\Services\FaqAddService;
use App\Http\Requests\Admin\FAQAddRequest;
use App\Http\Requests\Admin\FAQUpdateRequest;
use Brian2694\Toastr\Facades\Toastr;
use mysql_xdevapi\Exception;
use App\Traits\FileManagerTrait;
use App\Utils\Helpers;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;


class FAQController  extends BaseController
{
    public function __construct(
    private readonly FaqRepositoryInterface              $FaqRepo,
    private readonly CategoryRepositoryInterface         $categoryRepo,
    private readonly AttributeRepositoryInterface        $attributeRepo,
    private readonly TranslationRepositoryInterface      $translationRepo,
    )
    {
    }

    public function index(Request|null $request, string $type = null): View
    {
        return $this->getList($request);
    }

    public function addCategory(Request $request){
        $category_list = \App\Models\FaqCategory::paginate(10);
        return view(FAQPath::CATEGORY[VIEW],compact('category_list'));
    }
    public function CategoryStore(Request $request){
        $validated = $request->validate([
            'name' => 'required|unique:faq_category,name',
        ]);

        $save = new \App\Models\FaqCategory();
        $save->name = $request['name'];
        $save->save();
        Toastr::success(translate('category_added_successfully'));
        return back();
    }

    public function CategoryStatusUpdate(Request $request){
        $validated = $request->validate([
            'id' => 'required|exists:faq_category,id',
        ]);
        $save = \App\Models\FaqCategory::find($request['id']);
        $save->status = $request->get('status', 0);
        $save->save();
        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }
    public function CategoryUpdate(Request $request){
        $getData = \App\Models\FaqCategory::find($request['id']);
        if($getData){
            return view(FAQPath::CATEGORYUPDATE[VIEW],compact('getData'));
        }else{
            Toastr::error('Category Id is invalid');
            return back();
        }
    }

    public function CategoryEdit(Request $request){
        $validated = $request->validate([
            'name' => 'required|unique:faq_category,name,'.$request['id'],
            'id' => 'required|exists:faq_category,id',
        ]);

        $save = \App\Models\FaqCategory::find($request['id']);
        $save->name = $request['name'];
        $save->save();
        Toastr::success(translate('category_updated_successfully'));
        return redirect()->route('admin.faq.category');
    }

    public function CategoryDelete(Request $request){
        $save = \App\Models\FaqCategory::find($request['id']);
        $faqs= $this->FaqRepo->getFirstWhere(params: ['category_id' => $request['id']]);
        if(!empty($save) && empty($faqs)){
            $save->delete();
            Toastr::error(translate('category_deleted_successfully'));
        }else{
            Toastr::error(translate('already_exists_faq'));
        }
        return redirect()->route('admin.faq.category');
    }

    public function getList(Request $request): Application|Factory|View
    {
        $faqs = $this->FaqRepo->getListWhere(orderBy:['id'=>'desc'],searchValue:$request->get('searchValue'), dataLimit: getWebConfig(name: 'pagination_limit'));
        return view(FAQPath::LIST[VIEW],compact('faqs'));
    }
    public function getView(string $addedBy,string|int $id): View
    {
        $relations = ['translations'];
        $faqs = $this->FaqRepo->getFirstWhereWithoutGlobalScope(params: ['id' => $id], relations: $relations);
        return view(FAQPath::VIEW[VIEW],compact('faqs','addedBy'));
    }


    public function getAddView(): View
    {   
        $attributes = $this->attributeRepo->getList(orderBy: ['name' => 'desc'], dataLimit: 'all');
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $category_list = \App\Models\FaqCategory::where('status',1)->get();
        $defaultLanguage = $languages[0];
        return view(FAQPath::ADD[VIEW],compact('attributes','languages', 'defaultLanguage','category_list'));
    }

    public function add(FAQAddRequest $request, FaqAddService $FaqAdd): RedirectResponse
    {
        // dd($request->input());
        $dataArray = $FaqAdd->getAddData($request);
        $savedFAQ = $this->FaqRepo->add(data:$dataArray);
        $this->translationRepo->add(request:$request, model:'App\Models\Faq', id:$savedFAQ->id);
        Toastr::success(translate('faq_added_successfully'));
        Helpers::editDeleteLogs('FAQ','FAQ','Insert');
        return redirect()->route('admin.faq.list');
    }
    // FAQ Update Data
    public function getUpdateView(string|int $id): View|RedirectResponse
    {
        $faqs = $this->FaqRepo->getFirstWhere(params:['id'=>$id], relations: ['translations']);
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $category_list = \App\Models\FaqCategory::where('status',1)->get();
        $defaultLanguage = $languages[0];
        return view(FAQPath::UPDATE[VIEW], compact('faqs', 'languages', 'defaultLanguage','category_list'));
    }
    public function update(FAQUpdateRequest $request, FaqAddService $FaqAdd, $id): JsonResponse|RedirectResponse
    {
        // $faq = $this->FaqRepo->getFirstWhereWithoutGlobalScope(params: ['id' => $id], relations: ['translations']);
        // $dataArray = $FaqAdd->getUpdateServiceData(request: $request, faqs: $faqs, updateBy: 'admin');
        // dd($dataArray);
        $faq = $this->FaqRepo->getFirstWhere(params: ['id' => $id], relations: ['translations']);
        $dataArray = $FaqAdd->getUpdateData($request,$faq);
        $this->FaqRepo->update(id: $id, data: $dataArray);
        $this->translationRepo->update(request: $request, model: 'App\Models\FAQ', id: $id);
        Toastr::success(translate('service_updated_successfully'));
        Helpers::editDeleteLogs('FAQ','FAQ','Update');
       return redirect()->route('admin.faq.list');
    }
    // FAQ' Status On/Off
    public function updateStatus(Request $request): JsonResponse
    {
        $data = [
          'status' => $request->get('status', 0),
        ];
        $this->FaqRepo->update(id:$request['id'], data:$data);
        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }

     // FAQ's Delete
     public function delete(string|int $id,FaqAddService $FaqAdd): RedirectResponse
     {
         $faqs= $this->FaqRepo->getFirstWhere(params: ['id' => $id]);
         if ($faqs) {
             $this->translationRepo->delete(model: 'App\Models\Faq', id: $id);
             $this->FaqRepo->delete(params: ['id' => $id]);
             Toastr::success(translate('service_removed_successfully'));
             Helpers::editDeleteLogs('FAQ','FAQ','Delete');
         }else {
             Toastr::error(translate('invalid_product'));
         }
 
         return back();
     }
    
}