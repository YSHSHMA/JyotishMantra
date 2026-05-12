<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Repositories\TourRefundPolicyRepositoryInterface;
use App\Contracts\Repositories\TourTypeRepositoryInterface;
use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Enums\ViewPaths\Admin\TourRePolicyPath;
use App\Http\Controllers\Controller;
use App\Traits\FileManagerTrait;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class TourRefundPolicyController extends Controller
{
    use FileManagerTrait;
    public function __construct(
        private readonly TranslationRepositoryInterface      $translationRepo,
        private readonly TourRefundPolicyRepositoryInterface      $policyRepo,
        private readonly TourTypeRepositoryInterface  $tourtypeRepo,
    ) {}
   public function PolicyList(Request $request){
    $getData = $this->policyRepo->getListWhere(orderBy: ['id' => 'desc'], searchValue: $request->get('searchValue'), dataLimit: getWebConfig(name: 'pagination_limit'));
    $languages = getWebConfig(name: 'pnc_language') ?? null;
    $defaultLanguage = $languages[0];
    $gettypelist = $this->tourtypeRepo->getListWhere(orderBy: ['id' => 'desc'],filters:['status'=>1], dataLimit: "all");
    return view(TourRePolicyPath::ADDPOLICY[VIEW], compact('getData','gettypelist','defaultLanguage','languages'));
    }

    public function PolicyAdd(Request $request){
        $validator = Validator::make($request->all(), [
            'percentage' => 'required',
            'day' => 'required',
            'message.*' => 'required|array',
            'message.*' => 'required|string|min:1',
        ]);
        
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $dataArray = [
            'message' => $request->message[array_search('en', $request['lang'])],
            'percentage' => $request->percentage,
            'type' => $request->type,
            'day'=>$request->day,
            'status' => 1,
         ];
         $insert = $this->policyRepo->add(data: $dataArray);
         $this->translationRepo->add(request: $request, model: 'App\Models\TourRefundPolicy', id: $insert->id);
         Toastr::success(translate('Tour_Visit_added_successfully'));
         return redirect()->route(TourRePolicyPath::ADDPOLICY[REDIRECT]);
    }

    public function PolicyStatus(Request $request){
        $data['status'] = $request->get('status', 0);
        $this->policyRepo->update(id: $request['id'], data: $data);
        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }

    public function PolicyUpdate(Request $request,$id){
        $getData  = $this->policyRepo->getFirstWhere(params: ['id' => $id], relations: ['translations']);
        if (empty($getData)) {
            return back();
        }
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        $gettypelist = $this->tourtypeRepo->getListWhere(orderBy: ['id' => 'desc'],filters:['status'=>1], dataLimit: "all");
        return view(TourRePolicyPath::POLICYUPDATE[VIEW], compact('gettypelist','getData', 'languages', 'defaultLanguage'));
    }

    public function PolicyEdit(Request $request){
        $validator = Validator::make($request->all(), [
            'percentage' => 'required',
            'day' => 'required',
            'message.*' => 'required|array',
            'message.*' => 'required|string|min:1',
        ]);
        
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $dataArray = [
            'message' => $request->message[array_search('en', $request['lang'])],
            'percentage' => $request->percentage,
            'type' => $request->type,
            'day' => $request->day,
         ];
        $this->policyRepo->update(id:$request->id,data: $dataArray);
        $this->translationRepo->update(request: $request, model: 'App\Models\TourRefundPolicy', id: $request->id);
        Toastr::success(translate('Tour_Refund_policy_updated_successfully'));
        return redirect()->route(TourRePolicyPath::ADDPOLICY[REDIRECT]);
    }

    public function PolicyDelete(Request $request){
        $this->policyRepo->delete(params: ['id' => $request->id]);
        $this->translationRepo->delete(model: 'App\Models\TourRefundPolicy', id: $request->id);
        Toastr::success(translate('Tour_refund_policy_deleted_successfully'));
        return redirect()->route(TourRePolicyPath::ADDPOLICY[REDIRECT]);
    }

}
