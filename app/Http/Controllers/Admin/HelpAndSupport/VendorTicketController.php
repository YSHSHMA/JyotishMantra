<?php

namespace App\Http\Controllers\Admin\HelpAndSupport;

use App\Enums\ViewPaths\Admin\VendorSuppTicket;
use App\Http\Controllers\Controller;
use App\Models\VendorSupportTicket;
use App\Models\VendorSupportTicketConv;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use SimplePie\Cache\Redis;

class VendorTicketController extends Controller
{
    public function AddIssue()
    {
        $TypeList = ['vendor', 'seller', 'tour', 'trust', 'event'];
        $issueList = VendorSupportTicket::paginate(10);
        return view(VendorSuppTicket::ISSUELIST[VIEW], compact('TypeList', 'issueList'));
    }

    public function StoreIssue(Request $request)
    {
        $request->validate([
            'created_by' => 'required|in:admin,vendor',
            'type' => 'required',
            'message' => 'required|unique:vendor_support_tickets,message',
            'message' => [
                'required',
                \Illuminate\Validation\Rule::unique('vendor_support_tickets', 'message')->where(function ($query) use ($request) {
                    return $query->where('created_by', $request->created_by)->where('type', $request->type);
                })
            ],
        ]);

        $support = new VendorSupportTicket();
        $support->created_by = $request['created_by'];
        $support->type = $request['type'];
        $support->message = $request['message'];
        $support->status = 1;
        $support->save();
        Toastr::success(translate('issue_Added_successfully'));
        return back();
    }

    public function statusUpdateIssue(Request $request)
    {
        $support = VendorSupportTicket::find($request['id']);
        $support->status = $request->get('status', 0);
        $support->save();
        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }

    public function UpdateIssue(Request $request)
    {
        $TypeList = ['vendor', 'seller', 'tour', 'trust', 'event'];
        $getissue = VendorSupportTicket::find($request['id']);
        return view(VendorSuppTicket::ISSUEUPDATE[VIEW], compact('TypeList', 'getissue'));
    }
    public function EditIssue(Request $request)
    {
        $request->validate([
            'id' => "required|exists:vendor_support_tickets,id",
            'created_by' => 'required|in:admin,vendor',
            'type' => 'required',
            'message' => 'required|unique:vendor_support_tickets,message',
            'message' => [
                'required',
                \Illuminate\Validation\Rule::unique('vendor_support_tickets', 'message')->where(function ($query) use ($request) {
                    return $query->where('created_by', $request->created_by)->where('type', $request->type);
                })->ignore($request->id)
            ],
        ]);

        $support = VendorSupportTicket::find($request->id);
        $support->created_by = $request['created_by'];
        $support->type = $request['type'];
        $support->message = $request['message'];
        $support->save();
        Toastr::success(translate('issue_Updated_successfully'));
        return redirect()->route('admin.vendor-support-ticket.view');
    }

    public function DeleteIssue(Request $request)
    {
        $support = VendorSupportTicket::find($request->id);
        if ($support) {
            $support->delete();
            Toastr::success(translate('issue_Deleted_successfully'));
            return back();
        } else {
            Toastr::error(translate('issue_Deleted_Failed'));
            return back();
        }
    }

    // vendor

    public function ListVendorIssue(Request $request)
    {
        $getData = VendorSupportTicketConv::where('created_by', 'vendor')
            ->when(isset($request['type']) && ($request['type'] != 'all'), function ($query) use ($request) {
                return $query->where('type', $request['type']);
            })
            ->when(isset($request['status']) && ($request['status'] != 'all'), function ($query) use ($request) {
                return $query->where('status', $request['status']);
            })->paginate(10);
        $TypeList = ['vendor', 'seller', 'tour', 'trust', 'event'];
        return view(VendorSuppTicket::VENDORLIST[VIEW], compact('TypeList', 'getData'));
    }

    public function VendorIssueStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:vendor_support_tickets_conv,id',
        ]);
        $ticket_his = \App\Models\VendorSupportTicketConv::find($request->id);
        $ticket_his->status = $request->get('status', 'close');
        $ticket_his->save();
        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }

    public function VendorIssueGetSingle(Request $request)
    {
        $supportTicket = \App\Models\VendorSupportTicketConv::with(['Tour', 'Event', 'Trust', 'seller', 'conversations'])->find($request->id);
        \App\Models\VendorSupportTicketConvHis::where('ticket_issue_id', $request->id)->update(['read_admin_status' => 1]);
        return view(VendorSuppTicket::VENDORSINGLE[VIEW], compact('supportTicket'));
    }

    public function VendorSupportTicketReplay(Request $request)
    {
        $request->validate([
            'ticket_issue_id' => [
                'required',
                'integer',
                \Illuminate\Validation\Rule::exists('vendor_support_tickets_conv', 'id')->where('status', 'open'),
            ],
            "sender_type" => "required|in:admin,user",
            'replay' => "required",
        ], [], [
            'ticket_issue_id.exists' => 'The selected ticket is invalid or close.',
        ]);
        $attachedPaths = [];
        if ($request->hasFile('image')) {
            foreach ($request->file('image') as $image) {
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->storeAs('public/support-ticket', $imageName);
                $attachedPaths[] = $imageName;
            }
        }

        $ticket_his = new \App\Models\VendorSupportTicketConvHis();
        $ticket_his->ticket_issue_id = $request->ticket_issue_id;
        $ticket_his->sender_type = $request->sender_type;
        $ticket_his->message = $request->replay;
        $ticket_his->attached = json_encode($attachedPaths);
        $ticket_his->save();
        Toastr::success(translate('ticket_Added_successfully'));
        return back();
    }

    public function ListAdminIssue(Request $request)
    {
        $getData = VendorSupportTicketConv::where('created_by', 'admin')
            ->when(isset($request['type']) && ($request['type'] != 'all'), function ($query) use ($request) {
                return $query->where('type', $request['type']);
            })->when(isset($request['status']) && ($request['status'] != 'all'), function ($query) use ($request) {
                return $query->where('status', $request['status']);
            })
            ->with(['Tour', 'Event', 'Trust', 'seller'])->paginate(10);
        $TypeList = ['vendor', 'seller', 'tour', 'trust', 'event'];
        $support_list = VendorSupportTicket::where('created_by', 'admin')->get();
        $vendor_list = \App\Models\Seller::select([
            \Illuminate\Support\Facades\DB::raw('CASE WHEN type = "seller" THEN id ELSE relation_id END as relation_id'),
            'type',
            'f_name',
            'l_name'
        ])->get();//->whereIn('status', ['approved',''])
        return view(VendorSuppTicket::ADMINLIST[VIEW], compact('TypeList', 'getData', 'support_list', 'vendor_list'));
    }

    public function ListAdminStore(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|integer|exists:vendor_support_tickets,id',
            'vendor_id' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    $existsInRelationId = \App\Models\Seller::where('relation_id', $value)->exists();
                    $existsInId = \App\Models\Seller::where('id', $value)->exists();

                    if (!$existsInRelationId && !$existsInId) {
                        $fail("The selected vendor ID is invalid.");
                    }
                }
            ],
            'created_by' => 'required|in:admin,vendor',
            'type' => 'required|in:seller,vendor,event,tour,trust',
            'query_title' => 'required',
            'message' => 'required',
        ]);

        $save_ticket = new \App\Models\VendorSupportTicketConv();
        $save_ticket->ticket_id = $request->ticket_id;
        $save_ticket->created_by = $request->created_by;
        $save_ticket->type = $request->type;
        $save_ticket->vendor_id = $request->vendor_id;
        $save_ticket->query_title = $request->query_title;
        $save_ticket->status = 'open';
        $save_ticket->save();

        $ticket_his = new \App\Models\VendorSupportTicketConvHis();
        $ticket_his->ticket_issue_id = $save_ticket->id;
        $ticket_his->sender_type = 'admin';
        $ticket_his->message = $request->message;
        $ticket_his->save();
        Toastr::success(translate('ticket_created_successfully'));
        return back();
    }
}
