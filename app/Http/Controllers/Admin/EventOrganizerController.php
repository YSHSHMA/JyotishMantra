<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Repositories\EventOrganizerRepositoryInterface;
use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Enums\ViewPaths\Admin\EventOrganizerPath;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\EventOrganizeAddRequest;
use App\Http\Requests\Admin\EventOrganizeUpdateRequest;
use App\Services\EventOrganizeService;
use App\Traits\FileManagerTrait;
use App\Utils\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class EventOrganizerController extends Controller
{

    use FileManagerTrait;
    public function __construct(
        private readonly EventOrganizerRepositoryInterface       $EventOrganizerRepo,
        private readonly TranslationRepositoryInterface     $translationRepo,
    ) {}

    public function index(Request $request)
    {
        $language = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $language[0];
        return view(EventOrganizerPath::ADD[VIEW], compact('language', 'defaultLanguage'));
    }

    public function store(EventOrganizeAddRequest $request, EventOrganizeService $service)
    {
        $array = $service->getAddData($request);
        $insert = $this->EventOrganizerRepo->add(data: $array);
        $this->translationRepo->add(request: $request, model: 'App\Models\EventOrganizer', id: $insert->id);
        Toastr::success(translate('Organizer_added_successfully'));
        Helpers::editDeleteLogs('Event', 'Organizer', 'Insert');
        return redirect()->route(EventOrganizerPath::LIST[REDIRECT]);
    }



    public function list(Request $request)
    {
        $getData = $this->EventOrganizerRepo->getListWhere(orderBy: ['id' => 'desc'], searchValue: $request->get('searchValue'), filters: ['is_approve' => $request->get('is_approve')], dataLimit: getWebConfig(name: 'pagination_limit'));
        return view(EventOrganizerPath::LIST[VIEW], compact('getData'));
    }

    public function changeStatus(Request $request)
    {
        $data['status'] = $request->get('status', 0);
        $this->EventOrganizerRepo->update(id: $request['id'], data: $data);
        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }

    public function verification_status(Request $request)
    {
        $data['is_approve'] = $request->get('status', 0);
        $this->EventOrganizerRepo->update(id: $request['id'], data: $data);
        if ($data['is_approve'] == 1) {
            \App\Models\Seller::where('relation_id', $request['id'])->where('type', 'event')->update(['status' => "approved"]);
        } elseif ($data['is_approve'] == 2) {
            \App\Models\Seller::where('relation_id', $request['id'])->where('type', 'event')->update(['status' => "hold"]);
        } elseif ($data['is_approve'] == 0) {
            \App\Models\Seller::where('relation_id', $request['id'])->where('type', 'event')->update(['status' => "pending"]);
        }
        return response()->json(['success' => 1, 'message' => translate('User_Verification_status_updated_successfully')], 200);
    }

    public function delete(Request $request, EventOrganizeService $service)
    {
        $old_data = $this->EventOrganizerRepo->getFirstWhere(params: ['id' => $request['id']]);
        if ($old_data) {
            $service->deleteImage($old_data);
            $this->EventOrganizerRepo->delete(params: ['id' => $request['id']]);
            $this->translationRepo->delete('App\Models\EventOrganizer', $request['id']);
            Toastr::success(translate('Organizer_Deleted_successfully'));
            Helpers::editDeleteLogs('Event', 'Organizer', 'Delete');
            return response()->json(['success' => 1, 'message' => translate('event_Organizer_deleted_successfully')], 200);
        } else {
            Toastr::error(translate('Organizer_Deleted_Failed'));
            return response()->json(['success' => 0, 'message' => translate('Not_found_data')], 400);
        }
    }

    public function update(Request $request, $id)
    {
        $getData = $this->EventOrganizerRepo->getFirstWhere(params: ['id' => $id], relations: ['translations']);
        if ($getData) {
            $language = getWebConfig(name: 'pnc_language') ?? null;
            $defaultLanguage = $language[0];
            return view(EventOrganizerPath::UPDATE[VIEW], compact('getData', 'language', 'defaultLanguage'));
        } else {
            Toastr::error(translate('Event_Organizer_data_Not_found'));
            return redirect()->route(EventOrganizerPath::LIST[REDIRECT]);
        }
    }

    public function edit(EventOrganizeUpdateRequest $request, EventOrganizeService $service, $id)
    {
        $getData = $this->EventOrganizerRepo->getFirstWhere(params: ['id' => $id]);
        $array = $service->updateData($request, $getData);
        $insert = $this->EventOrganizerRepo->update(id: $id, data: $array);
        $this->translationRepo->update(request: $request, model: 'App\Models\EventOrganizer', id: $id);
        Helpers::editDeleteLogs('Event', 'Organizer', 'Update');
        Toastr::success(translate('Event_Organizer_Update_successfully'));
        return redirect()->route(EventOrganizerPath::LIST[REDIRECT]);
    }

    public function view_information(Request $request, $id)
    {
        $getData = $this->EventOrganizerRepo->getFirstWhere(params: ['id' => $id]);
        if ($getData) {
            $order = [];
            $getdata_show =  \App\Models\Seller::where('relation_id', $id)->where('type', 'event')->first();
            return view(EventOrganizerPath::VIEW[VIEW], compact('getData', 'order', 'getdata_show'));
        } else {
            Toastr::error(translate('event_Organizer_Not_found'));
            return redirect()->route(EventOrganizerPath::LIST[REDIRECT]);
        }
    }
    public function DocVerifiedResend(Request $request)
    {
        $id = $request->vendor_id;
        $getvendor = \App\Models\Seller::where("relation_id", $id)->where('type', 'event')->first();
        $update_seller_status = 0;
        if ($getvendor && json_decode($getvendor['all_doc_info'], true) && $request['arrays']) {
            $doc_encode = json_decode($getvendor['all_doc_info'], true);
            foreach (json_decode($getvendor['all_doc_info'], true) as $key => $value) {
                foreach ($request['arrays'] as $key12 => $value12) {
                    if ($value12['name'] == $key) {
                        $doc_encode[$key] = $value12['value'];
                        if ($update_seller_status == 0 && $value12['value'] == 2) {
                            $update_seller_status = 2;
                        }
                    }
                }
            }

            if ($update_seller_status == 2) {
                $notis = new \App\Models\Notification();
                $notis->sent_by = $id;
                $notis->sent_to = "event";
                $notis->title = "Profile Review";
                $notis->description = $request['reason'];
                $notis->notification_count = 1;
                $notis->image = '';
                $notis->status = 1;
                $notis->created_at = date('Y-m-d H:i:s');
                $notis->save();
            }
            \App\Models\Seller::where('relation_id', $id)->where('type', 'event')->update(['all_doc_info' => json_encode($doc_encode), "reupload_doc_status" => (($update_seller_status == 2) ? 2 : 1), 'update_seller_status' => $update_seller_status]);
        } else {
            $doc_encode = [
                "organizer_name" => 0,
                "organizer_pan_no" => 0,
                "organizer_address" => 0,
                "full_name" => 0,
                "email_address" => 0,
                "contact_number" => 0,
                "itr_return" => 0,
                "gst_no" => 0,
                "user_image" => 0,
                "cancelled_cheque_image" => 0,
                "pan_card_image" => 0,
                "aadhar_image" => 0,
                "account_no" => 0,
                "ifsc_code" => 0,
                "bank_name" => 0,
                "account_type" => 0,
                "beneficiary_name" => 0,
                'branch_name' => 0,
                "itr_return_image" => 0,
                "aadhar_number" => 0
            ];
            \App\Models\Seller::where('relation_id', $id)->where('type', 'event')->update(['all_doc_info' => json_encode($doc_encode), "reupload_doc_status" => (($update_seller_status == 2) ? 2 : 1), 'update_seller_status' => $update_seller_status]);
        }
        return response()->json(['message' => translate('vendor_successfully')]);
    }
}