<?php

namespace App\Http\Controllers\Admin\HelpAndSupport;

use App\Contracts\Repositories\SupportTicketConvRepositoryInterface;
use App\Contracts\Repositories\SupportTicketRepositoryInterface;
use App\Enums\ViewPaths\Admin\SupportTicket;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\SupportTicketRequest;
use App\Models\SupportIssue;
use App\Models\SupportType;
use App\Repositories\SupportTicketRepository;
use App\Services\SupportTicketService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Brian2694\Toastr\Facades\Toastr;

class SupportTicketController extends BaseController
{
    /**
     * @param SupportTicketRepository $supportTicketRepo
     */
    public function __construct(
        private readonly SupportTicketRepositoryInterface $supportTicketRepo,
        private readonly SupportTicketConvRepositoryInterface $supportTicketConvRepo,
    )
    {
    }

    /**
     * @param Request|null $request
     * @param string|null $type
     * @return \Illuminate\Contracts\View\View Index function is the starting point of a controller
     * Index function is the starting point of a controller
     */
    public function index(Request|null $request, string $type = null): View
    {
        return $this->getListView($request);
    }

    public function getListView(Request $request): View
    {
        $tickets = $this->supportTicketRepo->getListWhere(
            orderBy: ['id' => 'desc'],
            searchValue: $request->get('searchValue'),
            filters: ['priority' => $request['priority'], 'status' => $request['status'],'names'=>$request['names']],
            relations:['TicketType','TicketIssue'],
            dataLimit: getWebConfig('pagination_limit')
        );
        return view(SupportTicket::LIST[VIEW], compact('tickets'));
    }
    public function IndexTicket(Request $request):View{
        $tickets = $this->supportTicketRepo->getListWhere(
            orderBy: ['id' => 'desc'],
            searchValue: $request->get('searchValue'),
            filters: ['priority' => $request['priority'], 'status' => $request['status'],'names'=>$request['names']],
            relations:['TicketType','TicketIssue'],
            dataLimit: getWebConfig('pagination_limit')
        );
        $getId = \App\Models\SupportType::where('name',$request['names'])->first();
        $getissues = \App\Models\SupportIssue::where('type_id',($getId['id']??''))->get();
        return view('admin-views.support-ticket.view1', compact('tickets','getissues'));
    }

    public function updateStatus(Request $request): JsonResponse
    {
        $ticket = $this->supportTicketRepo->getFirstWhere(params:['id'=>$request['id']]);
        $status = $ticket['status'] == 'open' ? 'close':'open';
        $this->supportTicketRepo->update(id:$ticket['id'], data: ['status' => $status]);
        return response()->json([
            'message' => translate('status_updated_successfully')
        ], 200);
    }

    public function getView($id): View
    {
        \App\Models\SupportTicketConv::where('support_ticket_id',$id)->where('read_admin_status',0)->update(['read_admin_status'=>1]);
        $supportTicket = $this->supportTicketRepo->getListWhere(filters: ['id'=>$id], relations: ['conversations'], dataLimit: 'all');
        return view(SupportTicket::VIEW[VIEW], compact('supportTicket'));
    }

    public function reply(SupportTicketRequest $request, SupportTicketService $supportTicketService): RedirectResponse
    {
        if ($request['image'] == null && $request['replay'] == null) {
            Toastr::warning(translate('type_something').'!');
            return back();
        }
        $dataArray = $supportTicketService->getAddData(request: $request);
        $this->supportTicketConvRepo->add(data: $dataArray);
        return back();
    }
    
    //new 
    public function AddType(Request $request)
    {

        $query = SupportType::query();
        if (isset($request['id']) && !empty($request['id'])) {
            $query->where('name', 'like', '%' . $request['id'] . '%');
        }

        $TypeList = $query->paginate(10);
        return view(SupportTicket::TYPELIST[VIEW], compact('TypeList'));
    }

    public function StoreType(Request $request)
    {
        $request->validate(
            [
                'name' => 'required|unique:support_type,name',
            ],
            [
                'name.required' => 'The name field is required.',
                'name.unique' => 'The name must be unique. This name already exists.',
            ]
        );
        $support = new SupportType();
        $support->name = $request['name'];
        $support->status = 1;
        $support->save();
        return back();
    }

    public function statusUpdate(Request $request){
        $support = SupportType::find($request['id']);
        $support->status = $request->get('status', 0);
        $support->save();
        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }

    public function DeleteType(Request $request,$id){
        $support = SupportType::find($id);
        if ($support) {
            $support->delete();
            Toastr::success(translate('type_Deleted_successfully'));
            return back();
        } else {
            Toastr::error(translate('type_Deleted_Failed'));
            return back();
        }
    }

    public function UpdateType($id){
        $support = SupportType::find($id);
        return view(SupportTicket::TYPEUPDATE[VIEW], compact('support'));
    }

    public function EditType(Request $request){
        $request->validate(
            [
                'name' => 'required|unique:support_type,name,' . $request->id,
            ],
            [
                'name.required' => 'The name field is required.',
                'name.unique' => 'The name must be unique. This name already exists.',
            ]
        );
        $support = SupportType::find($request->id);
        $support->name = $request['name'];
        $support->save();
        return redirect()->route(SupportTicket::TYPELIST[REDIRECT]);
    }

    public function AddIssue(Request $request){
        $query = SupportIssue::query();
        if (isset($request['name']) && !empty($request['name'])) {
            $query->where('name', 'like', '%' . $request['name'] . '%');
        }

        $issueList = $query->with('TicketType')->paginate(10);
        $TypeList = SupportType::where('status',1)->get();
        return view(SupportTicket::ISSUELIST[VIEW], compact('issueList','TypeList'));
    }

    public function StoreIssue(Request $request){
        $request->validate(
            [
                'issue_name' => 'required|unique:support_issue,issue_name',
                'type_id' => 'required',
            ],
            [
                'type_id.required' => 'The Type field is required.',
                'issue_name.required' => 'The Issue name field is required.',
                'issue_name.unique' => 'The issue name must be unique. This Issue name already exists.',
            ]
        );
        $support = new SupportIssue();
        $support->issue_name = $request['issue_name'];
        $support->type_id = $request['type_id'];
        $support->status = 1;
        $support->save();
        return back();
    }

    public function UpdateIssue($id){
        $TypeList = SupportType::where('status',1)->get();
        $support = SupportIssue::find($id);
        return view(SupportTicket::ISSUEUPDATE[VIEW], compact('support','TypeList'));
    }

    public function EditIssue(Request $request){
        $request->validate(
            [
                'issue_name' => 'required|unique:support_issue,issue_name,'. $request->id,
                'type_id' => 'required',
            ],
            [
                'type_id.required' => 'The Type field is required.',
                'issue_name.required' => 'The Issue name field is required.',
                'issue_name.unique' => 'The issue name must be unique. This Issue name already exists.',
            ]
        );
        $support = SupportIssue::find($request->id);
        $support->issue_name = $request['issue_name'];
        $support->type_id = $request['type_id'];
        $support->save();
        return redirect()->route(SupportTicket::ISSUELIST[REDIRECT]);
    }
    public function statusUpdateIssue(Request $request){
        $support = SupportIssue::find($request['id']);
        $support->status = $request->get('status', 0);
        $support->save();
        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }

    public function DeleteIssue($id){
        $support = SupportIssue::find($id);
        if ($support) {
            $support->delete();
            Toastr::success(translate('issue_Deleted_successfully'));
            return back();
        } else {
            Toastr::error(translate('issue_Deleted_Failed'));
            return back();
        }
    }

}