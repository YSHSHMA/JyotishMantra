<?php

namespace App\Http\Controllers\RestAPI\v3\seller;

use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Models\Eventartist;
use App\Models\EventOrder;
use App\Models\EventOrderItems;
use App\Models\EventOrganizer;
use App\Models\Events;
use App\Models\Seller;
use App\Models\User;
use App\Models\VendorEmployees;
use App\Models\VendorRoles;
use App\Services\EventOrganizeService;
use App\Utils\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class OrganizerController extends Controller
{
    public function __construct(
        private readonly TranslationRepositoryInterface  $translationRepo
    ) {}
    public function Dashboard(Request $request)
    {
        if (empty($request->seller['relation_id'] ?? '')) {
            return response()->json(['status' => 0, 'message' => 'Not Found Organizer Id', 'recode' => 0, 'data' => []], 200);
        }
        $eventCounts = [
            'pending'   => Events::byOrganizer($request->seller['relation_id'])->pending()->count(),
            'upcoming'  => Events::byOrganizer($request->seller['relation_id'])->upcoming()->count(),
            'running'   => Events::byOrganizer($request->seller['relation_id'])->running()->count(),
            'complete'  => Events::byOrganizer($request->seller['relation_id'])->complete()->count(),
            'canceled'  => Events::byOrganizer($request->seller['relation_id'])->canceled()->count(),
        ];

        $orderStatus = [
            'pending'   => EventOrder::whereIn('event_id', Events::byOrganizer($request->seller['relation_id'])->pending()->pluck('id'))
                ->where(['transaction_status' => 1, 'status' => 1])
                ->count(),
            'upcoming'  => EventOrder::whereIn('event_id', Events::byOrganizer($request->seller['relation_id'])->upcoming()->pluck('id'))
                ->where(['transaction_status' => 1, 'status' => 1])
                ->count(),
            'running'   => EventOrder::whereIn('event_id', Events::byOrganizer($request->seller['relation_id'])->running()->pluck('id'))
                ->where(['transaction_status' => 1, 'status' => 1])
                ->count(),
            'complete'  => EventOrder::whereIn('event_id', Events::byOrganizer($request->seller['relation_id'])->complete()->pluck('id'))
                ->where(['transaction_status' => 1, 'status' => 1])
                ->count(),
            'canceled'  => EventOrder::whereIn('event_id', Events::byOrganizer($request->seller['relation_id'])->canceled()->pluck('id'))
                ->where('status', 2)
                ->count(),
        ];


        $tourInformation = \App\Models\EventOrganizer::where('id', $request->seller['relation_id'])->first();
        $dashboardData = [
            'totalEarning' => $tourInformation['org_withdrawable_ready'] ?? 0,
            'pendingWithdraw' => $tourInformation['org_withdrawable_pending'] ?? 0,
            "adminCommission" => $tourInformation['org_total_commission'] ?? 0,
            "withdrawn" => $tourInformation['org_collected_cash'] ?? 0,
            'collectedTotalTax' => $tourInformation['org_total_tax'] ?? 0,
        ];
        if ($tourInformation) {
            return response()->json(['status' => 1, 'message' => 'get Successfully', 'recode' => 1, 'data' => ['wallet' => $dashboardData, "events" => $eventCounts, 'orders' => $orderStatus]], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Not Found Information', 'recode' => 0, 'data' => []], 200);
        }
    }

    public function ProfileView(Request $request)
    {
        if (empty($request->seller['relation_id'] ?? '')) {
            return response()->json(['status' => 0, 'message' => 'Not Found Organizer Id', 'recode' => 0, 'data' => []], 200);
        }
        $getData = EventOrganizer::with('sellers')->where('id', $request->seller['relation_id'])->first();
        if ($getData) {
            $decoded = json_decode($getData['sellers']['all_doc_info'] ?? "[]", true);
            $converted = array_map(function ($key, $value) {
                return [(string)$key => (is_array($value) ? array_map('strval', $value) : (int)$value)];
            }, array_keys($decoded), $decoded);
            $converted1 = array_merge(...$converted);
            $filtered = [
                "id" => $getData['id'],
                "unique_id" => $getData['unique_id'],
                "organizer_name" => $getData['organizer_name'],
                "organizer_pan_no" => $getData['organizer_pan_no'],
                "organizer_address" => $getData['organizer_address'],
                "gst_no_type" => $getData['gst_no_type'],
                "gst_no" => $getData['gst_no'],
                "itr_return" => $getData['itr_return'],
                "full_name" => $getData['full_name'],
                "email_address" => $getData['email_address'],
                "contact_number" => $getData['contact_number'],
                "beneficiary_name" => $getData['beneficiary_name'],
                "account_type" => $getData['account_type'],
                "bank_name" => $getData['bank_name'],
                "branch_name" => $getData['branch_name'],
                "ifsc_code" => $getData['ifsc_code'],
                "account_no" => $getData['account_no'],
                "aadhar_number" => $getData['aadhar_number'],
                "is_approve" => $getData['is_approve'],
                "status" => $getData['status'],
                "created_at" => date('d-m-Y h:i A', strtotime($getData['created_at'])),
                "updated_at" => date('d-m-Y h:i A', strtotime($getData['updated_at'])),
                "profile_status" => ($getData['sellers']['status'] ?? ""),
                "pan_card_image" => getValidImage(path: 'storage/app/public/event/organizer/' . ($getData['pan_card_image'] ?? ''), type: 'backend-product'),
                "aadhar_image" => getValidImage(path: 'storage/app/public/event/organizer/' . ($getData['aadhar_image'] ?? ''), type: 'backend-product'),
                "cancelled_cheque_image" => getValidImage(path: 'storage/app/public/event/organizer/' . ($getData['cancelled_cheque_image'] ?? ''), type: 'backend-product'),
                "itr_return_image" => getValidImage(path: 'storage/app/public/event/organizer/' . ($getData['itr_return_image'] ?? ''), type: 'backend-product'),
                "image" => getValidImage(path: 'storage/app/public/event/organizer/' . ($getData['image'] ?? ''), type: 'backend-product'),
                "all_doc_info" => $converted1,
            ];
            return response()->json(['status' => 1, 'message' => 'Not Found Information', 'recode' => 1, "data" => $filtered], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Not Found Information', 'recode' => 0, 'data' => []], 200);
        }
    }

    public function ProfileUpdate(Request $request, EventOrganizeService $service)
    {
        if (empty($request->seller['relation_id'] ?? '')) {
            return response()->json(['status' => 0, 'message' => 'Not Found Organizer Id', 'recode' => 0, 'data' => []], 200);
        }
        $rules = [
            'f_name'             => 'nullable|string|max:255',
            'contact_number'        => 'nullable|string|max:20',
            'email_address'         => 'nullable|email|max:255',
            'organizer_name'        => 'nullable|string|max:255',
            'itr_return'            => 'nullable|string',
            'itr_return_image'      => 'nullable|file',
            'organizer_address'     => 'nullable|string|max:500',
            'image'            => 'nullable|file',
            'aadhar_number'         => 'nullable|string|max:20',
            'aadhar_front_image'          => 'nullable|file',
            'pan_number'      => 'nullable|string|max:20',
            'pan_card_image'        => 'nullable|file',
            'gst'                => 'nullable|string|max:20',
            'bank_name'             => 'nullable|string|max:255',
            'branch_name'           => 'nullable|string|max:255',
            'holder_name'      => 'nullable|string|max:255',
            'ifsc'             => 'nullable|string|max:20',
            'account_no'            => 'nullable|string|max:30',
            'account_type'          => 'nullable|string|max:20',
            'cancelled_cheque_image' => 'nullable|file',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['success' => 0, 'errors'  => $validator->errors()], 422);
        }

        $organizerData = EventOrganizer::find($request->seller['relation_id']);
        $vendor = Seller::where('type', 'event')->where("relation_id", $request->seller['relation_id'])->first();
        if (empty($checkData['all_doc_info'])) {
            $getUniqueArray = ['full_name' => 2, 'contact_number' => 2, 'email_address' => 2, 'organizer_name' => 2, 'itr_return' => 2, 'itr_return_image' => 2, "organizer_address" => 2, 'user_image' => 2, "aadhar_number" => 2, 'aadhar_image' => 2, 'organizer_pan_no' => 2, 'pan_card_image' => 2, 'gst_no' => 2, 'bank_name' => 2, 'branch_name' => 2, 'beneficiary_name' => 2, 'ifsc_code' => 2, 'account_no' => 2, 'account_type' => 2, 'cancelled_cheque_image' => 2];
            $vendor->update(["all_doc_info" => json_encode($getUniqueArray)]);
        }
        if ($organizerData && $vendor) {
            $allData = $service->ReCorrectEventData($request, $organizerData, $vendor);
            $vendor->update($allData['vendor']);
            $organizerData->update($allData['event']);
            return response()->json(['status' => 1, 'message' => 'update a seller', 'recode' => 0, 'data' => []], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Not Found Seller', 'recode' => 0, 'data' => []], 200);
        }
    }

    public function PermissionList()
    {
        $roleList = VendorRoles::where('type', 'event')->get();
        if ($roleList && count($roleList) > 0) {
            return response()->json(['status' => 1, 'message' => 'get All List', 'recode' => count($roleList), 'data' => ($roleList)], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Not Found Information', 'recode' => 0, 'data' => []], 200);
        }
    }

    public function AddEmployee(Request $request)
    {
        if (empty($request->seller['relation_id'] ?? '')) {
            return response()->json(['status' => 0, 'message' => 'Not Found Organizer Id', 'recode' => 0, 'data' => []], 200);
        }
        $validator = Validator::make($request->all(), [
            'identify_number' => 'required|unique:vendor_employee,identify_number',
            'name' => 'required',
            'email' => 'required|unique:vendor_employee,email|unique:sellers,email',
            'em_phone' => 'required|unique:vendor_employee,phone|unique:sellers,phone',
            'password' => 'required',
            'emp_role_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'errors' => $validator->errors()], 200);
        }

        $employee = new VendorEmployees();
        $employee->identify_number = $request['identify_number'];
        $employee->name = $request['name'];
        $employee->type = 'event';
        $employee->phone = $request['em_phone'];
        $employee->email = $request['email'];
        $employee->emp_role_id = $request['emp_role_id'];
        $employee->password = bcrypt($request['password']);
        if ($request['image']) {
            $fileName = $imageName = time() . '_' . uniqid() . '.' . $request['image']->getClientOriginalExtension();
            if (!Storage::disk('public')->exists('event/employee')) {
                Storage::disk('public')->makeDirectory('event/employee');
            }
            Storage::disk('public')->put('event/employee/' . $fileName, file_get_contents($request['image']));
            $employee->image = $imageName;
        }
        $employee->relation_id = $request->seller['relation_id'];
        $employee->save();
        if ($employee) {
            return response()->json(['status' => 1, 'message' => 'save Employee', 'recode' => 1, 'data' => []], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Not Found Information', 'recode' => 0, 'data' => []], 200);
        }
    }
    public function EmployeeList(Request $request)
    {
        if (empty($request->seller['relation_id'] ?? '')) {
            return response()->json(['status' => 0, 'message' => 'Not Found Organizer Id', 'recode' => 0, 'data' => []], 200);
        }
        $getData = VendorEmployees::select('id', 'identify_number', 'name', 'phone', 'email', 'emp_role_id', 'image', 'status')->where('type', 'event')->where('relation_id', $request->seller['relation_id'])->orderBy("id", "desc")->get()->map(function ($item) {
            $item['image'] = getValidImage(path: 'storage/app/public/event/employee/' . ($item['image'] ?? ''), type: 'backend-product');
            return $item;
        })->values();
        if ($getData && count($getData) > 0) {
            return response()->json(['status' => 1, 'message' => 'get All Employee', 'recode' => count($getData), 'data' => $getData], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Not Found Information', 'recode' => 0, 'data' => []], 200);
        }
    }

    public function EmployeeGetById(Request $request)
    {
        if (empty($request->seller['relation_id'] ?? '')) {
            return response()->json(['status' => 0, 'message' => 'Not Found Organizer Id', 'recode' => 0, 'data' => []], 200);
        }
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:vendor_employee,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'errors' => $validator->errors()], 200);
        }
        $getData = VendorEmployees::select('id', 'identify_number', 'name', 'phone', 'email', 'emp_role_id', 'image')->where('type', 'event')->where('id', $request['id'])->where('relation_id', $request->seller['relation_id'])->first();
        if ($getData) {
            $getData['image'] = getValidImage(path: 'storage/app/public/event/employee/' . ($getData['image'] ?? ''), type: 'backend-product');
            return response()->json(['status' => 1, 'message' => 'get All Employee', 'recode' => 1, 'data' => $getData], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Not Found Information', 'recode' => 0, 'data' => []], 200);
        }
    }

    public function EmployeeUpdate(Request $request)
    {
        if (empty($request->seller['relation_id'] ?? '')) {
            return response()->json(['status' => 0, 'message' => 'Not Found Organizer Id', 'recode' => 0, 'data' => []], 200);
        }
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:vendor_employee,id',
            'name' => 'required',
            'email' => [
                'required',
                Rule::unique('vendor_employee', 'email')->ignore($request->id, 'id'),
                Rule::unique('sellers', 'email'),
            ],
            'em_phone' => [
                'required',
                Rule::unique('vendor_employee', 'phone')->ignore($request->id, 'id'),
                Rule::unique('sellers', 'phone'),
            ],
            'emp_role_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'errors' => $validator->errors()], 200);
        }

        $employee = VendorEmployees::find($request['id']);
        $employee->name = $request['name'];
        $employee->phone = $request['em_phone'];
        $employee->email = $request['email'];
        $employee->emp_role_id = $request['emp_role_id'];
        if ($request['image']) {
            if (Storage::disk('public')->exists("event/employee/" . $employee['image'])) {
                Storage::disk('public')->delete("event/employee/" . $employee['image']);
            }
            $fileName = $imageName = time() . '_' . uniqid() . '.' . $request['image']->getClientOriginalExtension();
            if (!Storage::disk('public')->exists('event/employee')) {
                Storage::disk('public')->makeDirectory('event/employee');
            }
            Storage::disk('public')->put('event/employee/' . $fileName, file_get_contents($request['image']));
            $employee->image = $imageName;
        }
        $employee->relation_id = $request->seller['relation_id'];
        $employee->save();
        if ($employee) {
            return response()->json(['status' => 1, 'message' => 'Employee update successfully', 'recode' => 1, 'data' => []], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Not Found Information', 'recode' => 0, 'data' => []], 200);
        }
    }

    public function EmployeeDelete(Request $request)
    {
        if (empty($request->seller['relation_id'] ?? '')) {
            return response()->json(['status' => 0, 'message' => 'Not Found Organizer Id', 'recode' => 0, 'data' => []], 200);
        }
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:vendor_employee,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'errors' => $validator->errors()], 200);
        }
        $employee = VendorEmployees::find($request['id']);
        if ($employee) {
            if (Storage::disk('public')->exists("event/employee/" . $employee['image'])) {
                Storage::disk('public')->delete("event/employee/" . $employee['image']);
            }
            $employee->delete();
            return response()->json(['status' => 1, 'message' => 'Employee deleted successfully', 'recode' => 1, 'data' => []], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Not Found Information', 'recode' => 0, 'data' => []], 200);
        }
    }

    public function EmployeeStatusUpdate(Request $request)
    {
        if (empty($request->seller['relation_id'] ?? '')) {
            return response()->json(['status' => 0, 'message' => 'Not Found Organizer Id', 'recode' => 0, 'data' => []], 200);
        }
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:vendor_employee,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'errors' => $validator->errors()], 200);
        }
        $employee = VendorEmployees::find($request['id']);
        if ($employee) {
            $employee->status = (($employee['status'] == 1) ? 0 : 1);
            $employee->save();
            return response()->json(['status' => 1, 'message' => 'Employee Status Update successfully', 'recode' => 1, 'data' => []], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Not Found Information', 'recode' => 0, 'data' => []], 200);
        }
    }

    public function AddArtist(Request $request)
    {
        if (empty($request->seller['relation_id'] ?? '')) {
            return response()->json(['status' => 0, 'message' => 'Not Found Organizer Id', 'recode' => 0, 'data' => []], 200);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:1',
            'profession' => 'required|string|min:1',
            'description' => 'required|string|min:1',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'errors' => $validator->errors()], 200);
        }

        $artist = new Eventartist();
        $artist->name = $request['name'];
        $artist->profession = $request['profession'];
        $artist->description = $request['description'];
        if ($request->file('image')) {
            $fileName = $imageName = time() . '_' . uniqid() . '.' . $request->file('image')->getClientOriginalExtension();
            if (!Storage::disk('public')->exists('event/events')) {
                Storage::disk('public')->makeDirectory('event/events');
            }
            Storage::disk('public')->put('event/events/' . $fileName, file_get_contents($request->file('image')));
            $artist->image = $imageName;
        }
        $artist->created_by = $request->seller['relation_id'];
        $artist->status = 0;
        $artist->save();
        if ($artist) {
            return response()->json(['status' => 1, 'message' => 'save Artist', 'recode' => 1, 'data' => []], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Not Found Information', 'recode' => 0, 'data' => []], 200);
        }
    }

    public function ArtistList(Request $request)
    {
        if (empty($request->seller['relation_id'] ?? '')) {
            return response()->json(['status' => 0, 'message' => 'Not Found Organizer Id', 'recode' => 0, 'data' => []], 200);
        }
        $getData = Eventartist::select('id', 'name', 'profession', 'description', 'image', 'status')->when($request['name'], function ($query) use ($request) {
            $query->where('name', 'like', "%" . $request['name'] . "%");
        })->orderBy('id', 'desc')->get()->map(function ($item) {
            $item['image'] = getValidImage(path: 'storage/app/public/event/events/' . ($item['image'] ?? ''), type: 'backend-product');
            return $item;
        })->values()->toArray();
        if ($getData && count($getData) > 0) {
            return response()->json(['status' => 1, 'message' => 'get All Artist', 'recode' => count($getData), 'data' => $getData], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Not Found Information', 'recode' => 0, 'data' => []], 200);
        }
    }

    public function ArtistGetById(Request $request)
    {
        if (empty($request->seller['relation_id'] ?? '')) {
            return response()->json(['status' => 0, 'message' => 'Not Found Organizer Id', 'recode' => 0, 'data' => []], 200);
        }
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:artist,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'errors' => $validator->errors()], 200);
        }
        $getData = Eventartist::select('id', 'name', 'profession', 'description', 'image', 'status')->where('id', $request['id'])->where('created_by', $request->seller['relation_id'])->first();
        if ($getData) {
            $getData['image'] = getValidImage(path: 'storage/app/public/event/events/' . ($getData['image'] ?? ''), type: 'backend-product');
            return response()->json(['status' => 1, 'message' => 'get artist', 'recode' => 1, 'data' => $getData], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Not Found Information', 'recode' => 0, 'data' => []], 200);
        }
    }

    public function ArtistUpdate(Request $request)
    {
        if (empty($request->seller['relation_id'] ?? '')) {
            return response()->json(['status' => 0, 'message' => 'Not Found Organizer Id', 'recode' => 0, 'data' => []], 200);
        }
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:artist,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'errors' => $validator->errors()], 200);
        }

        $artist = Eventartist::find($request['id']);
        $artist->name = $request['name'];
        $artist->profession = $request['profession'];
        $artist->description = $request['description'];
        if ($request->file('image')) {
            if (Storage::disk('public')->exists("event/events/" . $artist['image'])) {
                Storage::disk('public')->delete("event/events/" . $artist['image']);
            }
            $fileName = $imageName = time() . '_' . uniqid() . '.' . $request->file('image')->getClientOriginalExtension();
            if (!Storage::disk('public')->exists('event/events')) {
                Storage::disk('public')->makeDirectory('event/events');
            }
            Storage::disk('public')->put('event/events/' . $fileName, file_get_contents($request->file('image')));
            $artist->image = $imageName;
        }
        $artist->save();
        if ($artist) {
            return response()->json(['status' => 1, 'message' => 'save Artist', 'recode' => 1, 'data' => []], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Not Found Information', 'recode' => 0, 'data' => []], 200);
        }
    }

    public function ArtistStatusUpdate(Request $request)
    {
        if (empty($request->seller['relation_id'] ?? '')) {
            return response()->json(['status' => 0, 'message' => 'Not Found Organizer Id', 'recode' => 0, 'data' => []], 200);
        }
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:artist,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'errors' => $validator->errors()], 200);
        }

        $artist = Eventartist::find($request['id']);
        $artist->status = (($artist['status'] == 1) ? 0 : 1);;
        $artist->save();
        if ($artist) {
            return response()->json(['status' => 1, 'message' => 'Update Status Successfully', 'recode' => 1, 'data' => []], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Not Found Information', 'recode' => 0, 'data' => []], 200);
        }
    }

    public function ArtistDelete(Request $request)
    {
        if (empty($request->seller['relation_id'] ?? '')) {
            return response()->json(['status' => 0, 'message' => 'Not Found Organizer Id', 'recode' => 0, 'data' => []], 200);
        }
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:artist,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'errors' => $validator->errors()], 200);
        }
        $artist = Eventartist::find($request['id']);
        if ($artist) {
            if (Storage::disk('public')->exists("event/events/" . $artist['image'])) {
                Storage::disk('public')->delete("event/events/" . $artist['image']);
            }
            $artist->delete();
            return response()->json(['status' => 1, 'message' => 'Artist deleted successfully', 'recode' => 1, 'data' => []], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Not Found Information', 'recode' => 0, 'data' => []], 200);
        }
    }

    public function EventCategory(Request $request)
    {
        if (empty($request->seller['relation_id'] ?? '')) {
            return response()->json(['status' => 0, 'message' => 'Not Found Organizer Id', 'recode' => 0, 'data' => []], 200);
        }
        $getCategorys = \App\Models\EventCategory::select('id', 'category_name')->where('status', 1)->get()->makeHidden(['translations']);
        if ($getCategorys) {
            return response()->json(['status' => 1, 'message' => 'get Category successfully', 'recode' => count($getCategorys), 'data' => $getCategorys], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Not Found Information', 'recode' => 0, 'data' => []], 200);
        }
    }

    public function EventPackageList(Request $request)
    {
        if (empty($request->seller['relation_id'] ?? '')) {
            return response()->json(['status' => 0, 'message' => 'Not Found Organizer Id', 'recode' => 0, 'data' => []], 200);
        }
        $getPackage = \App\Models\EventPackage::select('id', 'package_name')->where('status', 1)->get()->makeHidden(['translations']);
        if ($getPackage) {
            return response()->json(['status' => 1, 'message' => 'get Category successfully', 'recode' => count($getPackage), 'data' => $getPackage], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Not Found Information', 'recode' => 0, 'data' => []], 200);
        }
    }

    public function AddEvent(Request $request)
    {
        if (empty($request->seller['relation_id'] ?? '')) {
            return response()->json(['status' => 0, 'message' => 'Not Found Organizer Id', 'recode' => 0, 'data' => []], 200);
        }
        $rules = [
            'event_name'          => 'required|string|min:1',
            'category_id'           => 'required',
            // 'event_organizer_id'    => 'required',

            'event_about'         => 'required|string|min:1',

            'event_schedule'      => 'required|string|min:1',

            'event_attend'        => 'required|string|min:1',

            'event_team_condition' => 'required|string|min:1',

            'age_group'             => 'required',
            'event_artist'          => 'required',
            'language'              => 'required',

            'days'                  => ['required', 'integer', 'min:1'],

            'start_to_end_date' => [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->days == 1) {
                        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
                            $fail("The {$attribute} must be a single date (YYYY-MM-DD) when days is 1.");
                        }
                    } else {
                        if (!preg_match('/^\d{4}-\d{2}-\d{2} - \d{4}-\d{2}-\d{2}$/', $value)) {
                            $fail("The {$attribute} must be a date range (YYYY-MM-DD - YYYY-MM-DD) when days is greater than 1.");
                        }
                    }
                }
            ],

            'venue'                 => 'required',
            'venue.*.en_event_venue' => 'required|string',
            'venue.*.en_event_country' => 'required|string|max:255',
            'venue.*.en_event_state' => 'required|string|max:255',
            'venue.*.en_event_cities' => 'required|string|max:255',
            'venue.*.en_event_lat' => 'required|string|max:255',
            'venue.*.en_event_long' => 'required|string|max:255',
            'venue.*.date'          => 'required|date',
            'venue.*.start_time'    => 'required|date_format:h:i A',
            'venue.*.end_time'      => 'required|date_format:h:i A',

            // 'event_image'           => 'required|image|mimes:jpg,jpeg,png',
            // 'images'                => 'required|array',
            // 'images.*'              => 'required|image|mimes:jpg,jpeg,png,gif,bmp,webp,tif,tiff',
        ];

        if ($request->filled('informational_status') && $request->informational_status == 0) {
            $rules['venue.*.package_list'] = 'required';
        }
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'errors' => $validator->errors()], 200);
        }

        $event_image = '';
        if ($request->file('event_image')) {
            $fileName = $event_image = time() . '_' . uniqid() . '.' . $request->file('event_image')->getClientOriginalExtension();
            if (!Storage::disk('public')->exists('event/events')) {
                Storage::disk('public')->makeDirectory('event/events');
            }
            Storage::disk('public')->put('event/events/' . $fileName, file_get_contents($request->file('event_image')));
        }
        $imageNames = [];
        if ($request->file('images')) {
            foreach ($request->file('images') as $image) {
                $fileName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                if (!Storage::disk('public')->exists('event/events')) {
                    Storage::disk('public')->makeDirectory('event/events');
                }
                Storage::disk('public')->put('event/events/' . $fileName, file_get_contents($image));
                $imageNames[] = $fileName;
            }
        }
        $all_venue_data = [];
        // if (isset($request['venue'][0]) && !empty($request['venue'][0])) {
        //     for ($i = 0; $i < count($request['venue']); $i++) {
        //         $id = $i;
        //         $all_venue_data[$i] = $request['venue'][$i];
        //         $all_venue_data[$i]['id'] = ($id + 1);
        //         $start_date = \Carbon\Carbon::parse($all_venue_data[$i]['start_time']);
        //         $end_dates   = \Carbon\Carbon::parse($all_venue_data[$i]['end_time']);
        //         $all_venue_data[$i]['event_duration'] = $end_dates->diff($start_date)->format('%H:%I');

        //         $packages = [];
        //         if (!empty($request['venue'][$i]['package_list']) && json_decode($request['venue'][$i]['package_list'])) {
        //             foreach (json_decode($request['venue'][$i]['package_list']) as $key => $va) {
        //                 $packages[$key]['package_name'] = $va->package_name;
        //                 $packages[$key]['seats_no'] = $va->seats_no;
        //                 $packages[$key]['price_no'] = $va->price_no;
        //                 $packages[$key]['available'] = $va->seats_no;
        //                 $packages[$key]['sold'] = 0;
        //             }
        //         }
        //         $all_venue_data[$i]['package_list'] = $packages;
        //     }
        // }
        $venueData = $request->input('venue');

        // If venue is JSON string, decode it
        if (is_string($venueData)) {
            $venueData = json_decode($venueData, true);
        }

        // Ensure it's always an array
        $venueData = $venueData ?? [];

        $all_venue_data = [];

        if (!empty($venueData) && isset($venueData[0])) {
            foreach ($venueData as $i => $venue) {
                $all_venue_data[$i] = $venue;
                $all_venue_data[$i]['id'] = $i + 1;

                $start_date = \Carbon\Carbon::parse($venue['start_time']);
                $end_dates  = \Carbon\Carbon::parse($venue['end_time']);
                $all_venue_data[$i]['event_duration'] = $end_dates->diff($start_date)->format('%H:%I');

                // Handle package list
                $packages = [];
                if (!empty($venue['package_list'])) {
                    $decodedPackages = is_string($venue['package_list'])
                        ? json_decode($venue['package_list'])
                        : $venue['package_list'];

                    if ($decodedPackages) {
                        foreach ($decodedPackages as $key => $va) {
                            $packages[$key] = [
                                'package_name' => $va->package_name,
                                'seats_no'     => $va->seats_no,
                                'price_no'     => $va->price_no,
                                'available'    => $va->seats_no,
                                'sold'         => 0,
                            ];
                        }
                    }
                }
                $all_venue_data[$i]['package_list'] = $packages;
            }
        }


        $meta_image = '';
        if ($request->file('meta_image')) {
            $fileName = $meta_image = time() . '_' . uniqid() . '.' . $request->file('meta_image')->getClientOriginalExtension();
            if (!Storage::disk('public')->exists('event/events')) {
                Storage::disk('public')->makeDirectory('event/events');
            }
            Storage::disk('public')->put('event/events/' . $fileName, file_get_contents($request->file('meta_image')));
        }
        $insertQuery = [
            'event_name' => $request['event_name'],
            "slug" =>  \Illuminate\Support\Str::slug($request['event_name'], '-') . '-' . \Illuminate\Support\Str::random(6),
            'category_id' => $request['category_id'],
            'organizer_by' => 'outside',
            'informational_status' => $request['informational_status'] ?? 0,
            'required_aadhar_status' => $request['required_aadhar_status'] ?? 0,
            'event_organizer_id' => $request->seller['relation_id'],
            'event_about' => $request['event_about'],
            'event_schedule' => $request['event_schedule'],
            'event_attend' => $request['event_attend'],
            'event_team_condition' => $request['event_team_condition'],
            'age_group' => $request['age_group'],
            'event_artist' => $request['event_artist'],
            'language' => $request['language'],
            'days' => $request['days'],
            'all_venue_data' => json_encode($all_venue_data),
            'start_to_end_date' => $request['start_to_end_date'],
            'event_image' => $event_image,
            'images' => json_encode($imageNames),
            'youtube_video' => $request['youtube_video'],
            'meta_title' => $request['meta_title'],
            'meta_description' => $request['meta_description'],
            'meta_image' => $meta_image,
            'commission_live' => 2,
            'commission_seats' => 2,
            "is_approve" => 0,
            "event_approve_amount" => 0,
            "approve_amount_status" => 0,
            "status" => 0,
        ];
        $insert = Events::create($insertQuery);
        if ($insert) {
            return response()->json(['status' => 1, 'message' => 'insert Data Successfully', 'recode' => 1, 'data' => []], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Not Insert Data', 'recode' => 0, 'data' => []], 200);
        }
    }

    public function EventList(Request $request)
    {
        if (empty($request->seller['relation_id'] ?? '')) {
            return response()->json(['status' => 0, 'message' => 'Not Found Organizer Id', 'recode' => 0, 'data' => []], 200);
        }
        $informational_status = $request->input('informational_status', null);
        $is_approve = $request->input('is_approve', null);
        $status = $request->input('status', null);
        $getData = Events::with('categorys', 'eventArtist', 'EventOrder')
            ->withCount(['EventOrder as order_count' => function ($query) {
                $query->where('transaction_status', 1)->where('status', 1);
            }])
            ->when(!empty($request['type']), function ($query) use ($request) {
                $today = now()->format('Y-m-d');
                if ($request['type'] == 1) {
                    $query->whereRaw("STR_TO_DATE( IF(INSTR(start_to_end_date, ' - ') > 0, SUBSTRING_INDEX(start_to_end_date, ' - ', 1), start_to_end_date), '%Y-%m-%d') > ? ", [$today]);
                } elseif ($request['type'] == 2) {
                    $query->whereRaw("
                                    ? BETWEEN 
                                    STR_TO_DATE( IF(INSTR(start_to_end_date, ' - ') > 0, SUBSTRING_INDEX(start_to_end_date, ' - ', 1), start_to_end_date), '%Y-%m-%d' )
                                    AND 
                                    STR_TO_DATE( IF(INSTR(start_to_end_date, ' - ') > 0,SUBSTRING_INDEX(start_to_end_date, ' - ', -1), start_to_end_date), '%Y-%m-%d') ", [$today]);
                } elseif ($request['type'] == 3) {
                    $query->whereRaw("STR_TO_DATE( IF(INSTR(start_to_end_date, ' - ') > 0, SUBSTRING_INDEX(start_to_end_date, ' - ', -1), start_to_end_date), '%Y-%m-%d') < ? ", [$today]);
                }
            })->when((!empty($request['cancel']) && $request['cancel'] == 1), function ($query) use ($request) {
                $query->whereHas('EventOrder', function ($q) use ($request) {
                    $q->where('transaction_status', 1)->whereIn('status', [2, 3]);
                });
            })
            ->when(($is_approve != null), function ($query) use ($is_approve) {
                $query->where('is_approve', $is_approve);
            })
            ->when($informational_status != null, function ($query) use ($informational_status) {
                $query->where('informational_status', $informational_status);
            })
            ->when($status != null, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->where('event_organizer_id', $request->seller['relation_id'])->where('organizer_by', 'outside')->orderBy('id', 'desc')->get()->makeHidden(['translations']);
        if ($getData && count($getData) > 0) {
            $filtered = $getData->map(function ($event) {
                return [
                    'id' => $event->id,
                    'event_name' => $event->event_name,
                    'unique_id' => $event->unique_id,
                    'age_group' => $event->age_group,
                    'start_to_end_date' => $event->start_to_end_date,
                    'venue_list' => collect(json_decode($event->all_venue_data))->map(function ($venue) {
                        return [
                            'en_event_venue' => $venue->en_event_venue ?? "",
                            'en_event_venue_full_address' => $venue->en_event_venue_full_address ?? "",
                            'date' => $venue->date ?? "",
                        ];
                    })->values(),
                    'artist_name' => $event->eventArtist->name ?? "",
                    'category' => $event->categorys->category_name ?? "",
                    'status' => $event->status,
                    'is_approve' => $event->is_approve,
                    'order_count' => $event->EventOrder->where('transaction_status', 1)->where('status', 1)->count(),
                    "all_user_count" => collect($event->EventOrder)
                        ->flatMap(fn($order) => $order->orderitem)
                        ->flatMap(fn($item) => json_decode($item->user_information, true) ?? [])
                        ->count(),
                    "verified_user_count" => collect($event->EventOrder)
                        ->flatMap(fn($order) => $order->orderitem)
                        ->flatMap(fn($item) => json_decode($item->user_information, true) ?? [])
                        ->where('verify', 1)
                        ->count(),
                ];
            });
            return response()->json(['status' => 1, 'message' => 'Data get Successfully', 'recode' => count($getData), 'data' => $filtered], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Not Found Data', 'recode' => 0, 'data' => []], 200);
        }
    }

    public function EventGetById(Request $request)
    {
        if (empty($request->seller['relation_id'] ?? '')) {
            return response()->json(['status' => 0, 'message' => 'Not Found Organizer Id', 'recode' => 0, 'data' => []], 200);
        }
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:events,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'errors' => $validator->errors()], 200);
        }
        $getData = Events::with('categorys', 'eventArtist')->where('id', $request['id'])->where('event_organizer_id', $request->seller['relation_id'])->where('organizer_by', 'outside')->first()->makeHidden(['translations']);
        if ($getData) {
            $events_images = [];
            if (isset($getData['images']) && json_decode($getData['images'])) {
                $decodedimageList = json_decode($getData['images'], true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    foreach ($decodedimageList as $key2 => $imgs) {
                        $decodedimageList[$key2] = getValidImage(path: 'storage/app/public/event/events/' . $imgs, type: 'backend-product');
                    }
                }
                $events_images = $decodedimageList;
            }
            $filtered = [
                'id' => $getData->id,
                'event_name' => $getData->event_name,
                'unique_id' => $getData->unique_id,
                'age_group' => $getData->age_group,
                'start_to_end_date' => $getData->start_to_end_date,
                'venue_list' => json_decode($getData->all_venue_data ?? "[]"),
                'artist_name' => $getData->eventArtist->name ?? "",
                'category' => $getData->categorys->category_name ?? "",
                "category_id" => $getData->category_id,
                "informational_status" => $getData->informational_status,
                "required_aadhar_status" => $getData->required_aadhar_status,
                "event_about" => $getData->event_about,
                "event_schedule" => $getData->event_schedule,
                "event_attend" => $getData->event_attend,
                "event_team_condition" => $getData->event_team_condition,
                "event_artist" => $getData->event_artist,
                "language" => $getData->language,
                "days" => $getData->days,
                "event_image" => getValidImage(path: 'storage/app/public/event/events/' . ($getData->event_image ?? ""), type: 'product'),
                "images" => $events_images,
                "youtube_video" => $getData->youtube_video,
                "meta_title" => $getData->meta_title,
                "meta_description" => $getData->meta_description,
                "meta_image" => getValidImage(path: 'storage/app/public/event/events/' . ($getData->meta_image ?? ""), type: 'product'),

            ];
            return response()->json(['status' => 1, 'message' => 'Data get Successfully', 'recode' => 1, 'data' => $filtered], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Not Insert Data', 'recode' => 0, 'data' => []], 200);
        }
    }

    public function EventUpdate(Request $request)
    {
        if (empty($request->seller['relation_id'] ?? '')) {
            return response()->json(['status' => 0, 'message' => 'Not Found Organizer Id', 'recode' => 0, 'data' => []], 200);
        }
        $rules = [
            'event_name'          => 'required|string|min:1',
            'category_id'           => 'required',
            'event_about'         => 'required|string|min:1',

            'event_schedule'      => 'required|string|min:1',

            'event_attend'        => 'required|string|min:1',

            'event_team_condition' => 'required|string|min:1',

            'age_group'             => 'required',
            'event_artist'          => 'required',
            'language'              => 'required',

            'days'                  => ['required', 'integer', 'min:1'],

            'start_to_end_date' => [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->days == 1) {
                        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
                            $fail("The {$attribute} must be a single date (YYYY-MM-DD) when days is 1.");
                        }
                    } else {
                        if (!preg_match('/^\d{4}-\d{2}-\d{2} - \d{4}-\d{2}-\d{2}$/', $value)) {
                            $fail("The {$attribute} must be a date range (YYYY-MM-DD - YYYY-MM-DD) when days is greater than 1.");
                        }
                    }
                }
            ],

            'venue'                 => 'required|array',
            'venue.*.en_event_venue' => 'required|string',
            'venue.*.en_event_country' => 'required|string|max:255',
            'venue.*.en_event_state' => 'required|string|max:255',
            'venue.*.en_event_cities' => 'required|string|max:255',
            'venue.*.en_event_lat' => 'required|string|max:255',
            'venue.*.en_event_long' => 'required|string|max:255',
            'venue.*.date'          => 'required|date',
            'venue.*.start_time'    => 'required|date_format:h:i A',
            'venue.*.end_time'      => 'required|date_format:h:i A',

            // 'event_image'           => 'required|image|mimes:jpg,jpeg,png',
            // 'images'                => 'required|array',
            // 'images.*'              => 'required|image|mimes:jpg,jpeg,png,gif,bmp,webp,tif,tiff',
        ];

        if ($request->filled('informational_status') && $request->informational_status == 0) {
            $rules['venue.*.package_list'] = 'required';
        }
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'errors' => $validator->errors()], 200);
        }
        $getOlddata = Events::find($request['id']);
        $event_image = '';
        if ($request->file('event_image')) {
            if (Storage::disk('public')->exists("event/events/" . $getOlddata['event_image'])) {
                Storage::disk('public')->delete("event/events/" . $getOlddata['event_image']);
            }
            $fileName = $event_image = time() . '_' . uniqid() . '.' . $request->file('event_image')->getClientOriginalExtension();
            if (!Storage::disk('public')->exists('event/events')) {
                Storage::disk('public')->makeDirectory('event/events');
            }
            Storage::disk('public')->put('event/events/' . $fileName, file_get_contents($request->file('event_image')));
        }
        $imageNames = [];
        if ($getOlddata['images']) {
            $imageNames = json_decode($getOlddata['images']);
        }
        if ($request->file('images')) {
            foreach ($request->file('images') as $image) {
                $fileName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                if (!Storage::disk('public')->exists('event/events')) {
                    Storage::disk('public')->makeDirectory('event/events');
                }
                Storage::disk('public')->put('event/events/' . $fileName, file_get_contents($image));
                $imageNames[] = $fileName;
            }
        }
        $all_venue_data = [];
        if (isset($request['venue'][0]) && !empty($request['venue'][0])) {
            for ($i = 0; $i < count($request['venue']); $i++) {
                $id = $i;
                $all_venue_data[$i] = $request['venue'][$i];
                $all_venue_data[$i]['id'] = ($id + 1);
                $start_date = \Carbon\Carbon::parse($all_venue_data[$i]['start_time']);
                $end_dates   = \Carbon\Carbon::parse($all_venue_data[$i]['end_time']);
                $all_venue_data[$i]['event_duration'] = $end_dates->diff($start_date)->format('%H:%I');

                $packages = [];
                if (!empty($request['venue'][$i]['package_list']) && json_decode($request['venue'][$i]['package_list'])) {
                    foreach (json_decode($request['venue'][$i]['package_list']) as $key => $va) {
                        $packages[$key]['package_name'] = $va->package_name;
                        $packages[$key]['seats_no'] = $va->seats_no;
                        $packages[$key]['price_no'] = $va->price_no;
                        $packages[$key]['available'] = $va->seats_no;
                        $packages[$key]['sold'] = 0;
                    }
                }
                $all_venue_data[$i]['package_list'] = $packages;
            }
        }

        $meta_image = '';
        if ($request->file('meta_image')) {
            if (Storage::disk('public')->exists("event/events/" . $getOlddata['meta_image'])) {
                Storage::disk('public')->delete("event/events/" . $getOlddata['meta_image']);
            }
            $fileName = $meta_image = time() . '_' . uniqid() . '.' . $request->file('meta_image')->getClientOriginalExtension();
            if (!Storage::disk('public')->exists('event/events')) {
                Storage::disk('public')->makeDirectory('event/events');
            }
            Storage::disk('public')->put('event/events/' . $fileName, file_get_contents($request->file('meta_image')));
        }
        $insertQuery = [
            'event_name' => $request['event_name'],
            'category_id' => $request['category_id'],
            'informational_status' => $request['informational_status'] ?? 0,
            'required_aadhar_status' => $request['required_aadhar_status'] ?? 0,
            'event_about' => $request['event_about'],
            'event_schedule' => $request['event_schedule'],
            'event_attend' => $request['event_attend'],
            'event_team_condition' => $request['event_team_condition'],
            'age_group' => $request['age_group'],
            'event_artist' => $request['event_artist'],
            'language' => $request['language'],
            'days' => $request['days'],
            'all_venue_data' => json_encode($all_venue_data),
            'start_to_end_date' => $request['start_to_end_date'],
            'event_image' => $event_image,
            'images' => json_encode($imageNames),
            'youtube_video' => $request['youtube_video'],
            'meta_title' => $request['meta_title'],
            'meta_description' => $request['meta_description'],
            'meta_image' => $meta_image,
        ];
        $insert = $getOlddata->update($insertQuery);
        if ($insert) {
            return response()->json(['status' => 1, 'message' => 'Update Data Successfully', 'recode' => 1, 'data' => []], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Failed', 'recode' => 0, 'data' => []], 200);
        }
    }

    public function EventImageRemove(Request $request)
    {
        if (empty($request->seller['relation_id'] ?? '')) {
            return response()->json(['status' => 0, 'message' => 'Not Found Organizer Id', 'recode' => 0, 'data' => []], 200);
        }
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:events,id',
            'name' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'errors' => $validator->errors()], 200);
        }
        $getData = Events::where('id', $request['id'])->where('event_organizer_id', $request->seller['relation_id'])->where('organizer_by', 'outside')->first();
        if ($getData) {
            $imageName = basename($request['name']);
            $getImages = json_decode($getData['images'] ?? "[]", true);

            $imageNames = array_filter($getImages, function ($img) use ($imageName) {
                return $img !== $imageName;
            });
            if (Storage::disk('public')->exists("event/events/" . $imageName)) {
                Storage::disk('public')->delete("event/events/" . $imageName);
            }
            $imageNames = array_values($imageNames);
            Events::where('id', $request['id'])->update(['images' => json_encode($imageNames)]);
            return response()->json(['status' => 1, 'message' => 'Update Data Successfully', 'recode' => 1, 'data' => []], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Failed', 'recode' => 0, 'data' => []], 200);
        }
    }

    public function EventDelete(Request $request)
    {
        if (empty($request->seller['relation_id'] ?? '')) {
            return response()->json(['status' => 0, 'message' => 'Not Found Organizer Id', 'recode' => 0, 'data' => []], 200);
        }
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:events,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'errors' => $validator->errors()], 200);
        }
        // $getData = Events::with(['EventOrder'])->where('id', $request['id'])->whereHas('EventOrder', function ($q) {
        //     $q->where('status', 0);
        // })->first();
        $getData = Events::with(['EventOrder'])->where('event_organizer_id', $request->seller['relation_id'])->where('organizer_by', 'outside')
            ->where('id', $request['id'])
            ->whereDoesntHave('EventOrder', function ($q) {
                $q->where('transaction_status', 1)
                    ->where('status', '=', 1);
            })
            ->first();

        if ($getData) {
            $ImageMulti = json_decode($getData['images'] ?? "[]");
            if ($ImageMulti) {
                foreach ($ImageMulti as $image) {
                    if (Storage::disk('public')->exists("event/events/" . $image)) {
                        Storage::disk('public')->delete("event/events/" . $image);
                    }
                }
            }
            if (Storage::disk('public')->exists("event/events/" . $getData['meta_image'])) {
                Storage::disk('public')->delete("event/events/" . $getData['meta_image']);
            }
            if (Storage::disk('public')->exists("event/events/" . $getData['event_image'])) {
                Storage::disk('public')->delete("event/events/" . $getData['event_image']);
            }
            Events::where('id', $request['id'])->delete();
            return response()->json(['status' => 1, 'message' => 'Deleted Data Successfully', 'recode' => 1, 'data' => []], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Not Delete Event', 'recode' => 0, 'data' => []], 200);
        }
    }

    public function EventStatusUpdate(Request $request)
    {
        if (empty($request->seller['relation_id'] ?? '')) {
            return response()->json(['status' => 0, 'message' => 'Not Found Organizer Id', 'recode' => 0, 'data' => []], 200);
        }
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:events,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'errors' => $validator->errors()], 200);
        }

        $eventData = Events::find($request['id']);
        $eventData->status = (($eventData['status'] == 1) ? 0 : 1);;
        $eventData->save();
        if ($eventData) {
            return response()->json(['status' => 1, 'message' => 'update Status Successfully', 'recode' => 1, 'data' => []], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Not Found Information', 'recode' => 0, 'data' => []], 200);
        }
    }

    public function OrderList(Request $request)
    {
        if (empty($request->seller['relation_id'] ?? '')) {
            return response()->json(['status' => 0, 'message' => 'Not Found Organizer Id', 'recode' => 0, 'data' => []], 200);
        }
        $validator = Validator::make($request->all(), [
            'event_id' => 'nullable|exists:events,id',
            'category_id' => 'nullable|exists:event_category,id',
            'type'   => 'nullable|in:1,2,3|required_without:cancel',
            'cancel' => 'nullable|in:1|required_without:type'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'errors' => $validator->errors()], 200);
        }
        $get_querys = EventOrder::with(['eventid', 'userdata', 'orderitem'])
            ->when(!empty($request['event_id']), function ($query) use ($request) {
                $query->where('event_id', $request['event_id']);
            })->when(!empty($request['category_id']), function ($query) use ($request) {
                $query->whereHas('eventid', function ($q) use ($request) {
                    $q->where('category_id', $request['category_id']);
                });
            })->when(($request['start_date'] && $request['end_date']), function ($query4) use ($request) {
                $query4->whereBetween('updated_at', [$request['start_date'], $request['end_date']]);
            })->when(!empty($request['type']), function ($query) use ($request) {
                $query->whereHas('eventid', function ($q) use ($request) {
                    $today = now()->format('Y-m-d');
                    if ($request['type'] == 1) {
                        $q->whereRaw("STR_TO_DATE( IF(INSTR(start_to_end_date, ' - ') > 0, SUBSTRING_INDEX(start_to_end_date, ' - ', 1), start_to_end_date), '%Y-%m-%d') > ? ", [$today]);
                    } elseif ($request['type'] == 2) {
                        $q->whereRaw("
                                    ? BETWEEN 
                                    STR_TO_DATE( IF(INSTR(start_to_end_date, ' - ') > 0, SUBSTRING_INDEX(start_to_end_date, ' - ', 1), start_to_end_date), '%Y-%m-%d' )
                                    AND 
                                    STR_TO_DATE( IF(INSTR(start_to_end_date, ' - ') > 0,SUBSTRING_INDEX(start_to_end_date, ' - ', -1), start_to_end_date), '%Y-%m-%d') ", [$today]);
                    } elseif ($request['type'] == 3) {
                        $q->whereRaw("STR_TO_DATE( IF(INSTR(start_to_end_date, ' - ') > 0, SUBSTRING_INDEX(start_to_end_date, ' - ', -1), start_to_end_date), '%Y-%m-%d') < ? ", [$today]);
                    }
                });
            })->where('transaction_status', 1);
        if ($request['cancel'] == 1) {
            $get_querys->whereIn('status', [2, 3]);
        } else {
            $get_querys->where('status', 1);
        }
        $eventData = $get_querys
            ->whereHas('eventid', function ($q) use ($request) {
                $q->where('event_organizer_id', $request->seller['relation_id']);
            })
            ->orderBy('id', 'desc')->get();
        if ($eventData && count($eventData) > 0) {
            $filtered = $eventData->map(function ($event) {
                $venues = collect(json_decode($event->eventid->all_venue_data ?? "[]", true));
                $venueId = $event->venue_id ?? "";
                $filteredVenue = $venues->firstWhere('id', $venueId);
                return [
                    'id' => $event->id,
                    'order_no' => $event->order_no,
                    'amount' => $event->amount,
                    'coupon_amount' => $event->coupon_amount,
                    'admin_commission' => $event->admin_commission,
                    'gst_amount' => $event->gst_amount,
                    'final_amount' => $event->final_amount,
                    'created_at' => date("d-m-Y h:i A", strtotime($event->created_at)),
                    'user_name' => $event->userdata->name ?? "",
                    'user_phone' => $event->userdata->phone ?? "",
                    'user_email' => $event->userdata->email ?? "",

                    'event_name' => $event->eventid->event_name ?? "",
                    'event_image' => getValidImage(path: 'storage/app/public/event/events/' . ($event->eventid?->event_image ?? ""), type: 'product'),
                    'event_artist' => $event->eventid->eventArtist->name ?? "",
                    'event_category' => $event->eventid->categorys->category_name ?? "",
                    'event_venue' => $filteredVenue['en_event_venue_full_address'] ?? ($filteredVenue['en_event_venue'] ?? ""),
                    'event_date' => $filteredVenue['date'] ?? "",
                    'event_start_time' => $filteredVenue['start_time'] ?? "",
                    "book_package_name" => $event->orderitem[0]?->category['package_name'] ?? "",
                    "book_seats" => $event->orderitem[0]?->no_of_seats ?? "",
                    "book_sub_amount" => $event->orderitem[0]?->sub_amount ?? "",
                    "book_gst" => $event->orderitem[0]?->gst ?? "",
                    "book_gst_amount" => $event->orderitem[0]?->gst_amount ?? "",
                    "book_total_amount" => $event->orderitem[0]?->amount ?? "",
                ];
            });
            return response()->json(['status' => 1, 'message' => 'Get Order List Successfully', 'recode' => count($eventData), 'data' => $filtered], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Not Found Information', 'recode' => 0, 'data' => []], 200);
        }
    }

    public function WithdrawalReqAdd(Request $request)
    {
        if (empty($request->seller['relation_id'] ?? '')) {
            return response()->json(['status' => 0, 'message' => 'Not Found Organizer Id', 'recode' => 0, 'data' => []], 200);
        }
        $validator = Validator::make($request->all(), [
            "holder_name"    => "required_without:upi_code",
            "bank_name"      => "required_without:upi_code",
            "ifsc_code"      => "required_without:upi_code",
            "account_number" => "required_without:upi_code",
            "upi_code"       => "required_without_all:holder_name,bank_name,ifsc_code,account_number",
            "req_amount"     => "required|numeric|min:1",
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'errors' => $validator->errors()], 200);
        }
        if (!\App\Models\WithdrawalAmountHistory::where(['vendor_id' => $request->seller['relation_id'], 'type' => "event", 'status' => 0])->exists()) {
            $getData = \App\Models\EventOrganizer::find($request->seller['relation_id']);
            if ($request['req_amount'] <= $getData['org_withdrawable_ready']) {
                $withdrawal  =  new \App\Models\WithdrawalAmountHistory();
                $withdrawal->type = "event";
                $withdrawal->vendor_id = $request->seller['relation_id'];
                $withdrawal->ex_id = "";
                $withdrawal->holder_name = $request['holder_name'] ?? "";
                $withdrawal->bank_name = $request['bank_name'] ?? "";
                $withdrawal->ifsc_code = $request['ifsc_code'] ?? "";
                $withdrawal->account_number = $request['account_number'] ?? "";
                $withdrawal->upi_code = $request['upi_code'] ?? '';
                $withdrawal->old_wallet_amount = $getData['org_withdrawable_ready'];
                $withdrawal->req_amount = $request['req_amount'];
                $withdrawal->save();
                if ($withdrawal) {
                    $getData->update(['org_withdrawable_pending' => $request['req_amount']]);
                }
                return response()->json(['status' => 1, 'message' => translate('Payment_request_sent_successfully'), 'recode' => 0, 'data' => []], 200);
            } else {
                return response()->json(['status' => 0, 'message' => translate('Payment_Request_failed'), 'recode' => 0, 'data' => []], 200);
            }
        } else {
            return response()->json(['status' => 0, 'message' => translate('A_payment_request_has_already_been_sent'), 'recode' => 0, 'data' => []], 200);
        }
    }

    public function WithdrawalList(Request $request)
    {
        if (empty($request->seller['relation_id'] ?? '')) {
            return response()->json(['status' => 0, 'message' => 'Not Found Organizer Id', 'recode' => 0, 'data' => []], 200);
        }
        $vendorId = $request->seller['relation_id'];
        $withdrawRequests = \App\Models\WithdrawalAmountHistory::with(['Event'])->where(['vendor_id' => $vendorId, 'type' => "event"])->orderBy('id', 'desc')->get();
        if ($withdrawRequests && count($withdrawRequests) > 0) {
            $filtered = $withdrawRequests->map(function ($event) {
                return [
                    'id' => $event->id,
                    'old_wallet_amount' => $event->old_wallet_amount,
                    'req_amount' => $event->req_amount,
                    'approval_amount' => $event->approval_amount,
                    'message' => $event->message,
                    'transcation_id' => $event->transcation_id,
                    'payment_method' => $event->payment_method,
                    'upi_code' => $event->upi_code,
                    'bank_name' => $event->bank_name,
                    'branch_code' => $event->branch_code,
                    'ifsc_code' => $event->ifsc_code,
                    'account_number' => $event->account_number,
                    'holder_name' => $event->holder_name,
                    'status' => $event->status,
                    'created_at' => date("d-m-Y h:i A", strtotime($event->created_at)),
                ];
            });
            return response()->json(['status' => 1, 'message' => 'Get Order List Successfully', 'recode' => count($withdrawRequests), 'data' => $filtered], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Not Found Information', 'recode' => 0, 'data' => []], 200);
        }
    }

    public function EventTodayList(Request $request)
    {
        if (empty($request->seller['relation_id'] ?? '')) {
            return response()->json(['status' => 0, 'message' => 'Not Found Organizer Id', 'recode' => 0, 'data' => []], 200);
        }
        $today = now()->format('Y-m-d');
        $getData = Events::with('categorys', 'eventArtist', 'EventOrder')
            ->withCount(['EventOrder as order_count' => function ($query) {
                $query->where('transaction_status', 1)->where('status', 1);
            }])
            ->whereRaw("? BETWEEN 
                                    STR_TO_DATE( IF(INSTR(start_to_end_date, ' - ') > 0, SUBSTRING_INDEX(start_to_end_date, ' - ', 1), start_to_end_date), '%Y-%m-%d' )
                                    AND 
                                    STR_TO_DATE( IF(INSTR(start_to_end_date, ' - ') > 0,SUBSTRING_INDEX(start_to_end_date, ' - ', -1), start_to_end_date), '%Y-%m-%d') ", [$today])
            ->whereRaw("JSON_CONTAINS(all_venue_data, JSON_OBJECT('date', ?))", [$today])
            ->where('is_approve', 1)->where('status', 1)
            ->where('event_organizer_id', $request->seller['relation_id'])
            ->where('organizer_by', 'outside')->orderBy('id', 'desc')->get()->makeHidden(['translations']);
        if ($getData && count($getData) > 0) {
            $getData->each(function ($event) use ($today) {
                $venues = collect(json_decode($event->all_venue_data ?? "[]", true))
                    ->where('date', $today)
                    ->values();
                $event->all_venue_data = $venues;
            });
            $filtered = $getData->map(function ($event) {
                return [
                    'id' => $event->id,
                    'event_name' => $event->event_name,
                    'unique_id' => $event->unique_id,
                    'age_group' => $event->age_group,
                    'start_to_end_date' => $event->start_to_end_date,
                    'venue_list' => collect($event->all_venue_data)->map(function ($venue) {
                        return [
                            'en_event_venue' => $venue['en_event_venue'] ?? "",
                            'en_event_venue_full_address' => $venue['en_event_venue_full_address'] ?? "",
                            'date' => $venue['date'] ?? "",
                        ];
                    })->values(),
                    'artist_name' => $event->eventArtist->name ?? "",
                    'category' => $event->categorys->category_name ?? "",
                    'status' => $event->status,
                    'is_approve' => $event->is_approve,
                    'order_count' => $event->EventOrder->where('transaction_status', 1)->where('status', 1)->count(),
                    "all_user_count" => collect($event->EventOrder)
                        ->flatMap(fn($order) => $order->orderitem)
                        ->flatMap(fn($item) => json_decode($item->user_information, true) ?? [])
                        ->count(),
                    "verified_user_count" => collect($event->EventOrder)
                        ->flatMap(fn($order) => $order->orderitem)
                        ->flatMap(fn($item) => json_decode($item->user_information, true) ?? [])
                        ->where('verify', 1)
                        ->count(),
                ];
            });
            return response()->json(['status' => 1, 'message' => 'Data get Successfully', 'recode' => count($getData), 'data' => $filtered], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Not Found Data', 'recode' => 0, 'data' => []], 200);
        }
    }

    public function EventScannerQrCode(Request $request)
    {
        if (empty($request->seller['relation_id'] ?? '')) {
            return response()->json(['success' => 0, 'message' => 'Not Found Organizer Id', 'recode' => 0, 'data' => []], 200);
        }
        $validator = Validator::make($request->all(), [
            "url"     => "required",
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'errors' => $validator->errors()], 200);
        }
        $parts = explode('/', parse_url($request['url'], PHP_URL_PATH));
        $orderId = $parts[count($parts) - 2];
        $member = $parts[count($parts) - 1];
        $getData = \App\Models\EventOrderItems::with(['orderdata'])->whereHas('orderdata', function ($q) use ($request) {
            $q->whereHas('eventid', function ($q2) use ($request) {
                $q2->where('event_organizer_id', $request->seller['relation_id']);
            });
        })->where('order_id', $orderId)->first();
        if (!$getData) {
            return response()->json(['success' => 0, 'error' => "Invalid Qr Code", 'message' => "Order not found"], 200);
        }
        $getUserData = json_decode($getData['user_information'] ?? '[]', true);
        $userIndex = $getUserData[collect($getUserData)->search(fn($item) => $item['id'] == $member)] ?? [];
        if ($userIndex) {
            if ($request['type'] == 'verify' && $userIndex['verify'] == 0) {
                $getUserData[collect($getUserData)->search(fn($item) => $item['id'] == $member)]['verify'] = 1;
                $getData->user_information = json_encode($getUserData, JSON_UNESCAPED_UNICODE);
                $getData->save();
                return response()->json(['success' => 2, 'message' => "User verified successfully", "data" => []], 200);
            } elseif ($userIndex['verify'] == 1) {
                return response()->json(['success' => 0, 'message' => 'Already success', 'data' => []], 200);
            }
            $getNewData = [];
            $getNewData['name'] = $userIndex['name'];
            $getNewData['phone'] = $userIndex['phone'];
            $getNewData['aadhar'] = $userIndex['aadhar'];
            $getImages = \App\Models\UserAadhaarKyc::where('aadhaar_number', $userIndex['aadhar'])->first();
            $getNewData['image'] = $getImages['image'] ?? '';
            $getNewData['verify'] = $userIndex['verify'];
            $getNewData['aadhar_verify_status'] = $userIndex['aadhar_verify_status'] ?? '';
            $getNewData['event_name'] = data_get($getData, 'orderdata.eventid.event_name', '');
            $venues = data_get($getData, 'orderdata.eventid.all_venue_data', "[]");
            $singleVenue = collect(json_decode($venues, true))->firstWhere('id', $getData['orderdata']['venue_id'] ?? 0);
            $getNewData['event_venue'] = (($singleVenue['en_event_venue_full_address']) ? $singleVenue['en_event_venue_full_address'] : ($singleVenue['en_event_venue'] ?? ""));
            $getNewData['event_date'] = ($singleVenue['date'] ?? "");
            $getNewData['event_start_time'] = ($singleVenue['start_time'] ?? "");
            $getNewData['event_end_time'] = ($singleVenue['end_time'] ?? "");
            return response()->json(['success' => 1, 'message' => 'success', 'data' => $getNewData], 200);
        } else {
            return response()->json(['success' => 0, 'message' => 'Not Found', 'data' => []], 200);
        }
    }
    public function EventUserVerifyList(Request $request)
    {
        if (empty($request->seller['relation_id'] ?? '')) {
            return response()->json(['success' => 0, 'message' => 'Not Found Organizer Id', 'recode' => 0, 'data' => []], 200);
        }
        $getData = \App\Models\EventOrderItems::select('order_id', 'package_id', 'no_of_seats', 'sub_amount', 'gst', 'gst_amount', 'amount', 'user_information')->with(['orderdata', 'category'])->whereHas('orderdata', function ($q) use ($request) {
            $q->whereHas('eventid', function ($q2) use ($request) {
                $q2->where('event_organizer_id', $request->seller['relation_id']);
                if (!empty($request['event_id'])) {
                    $q2->where('id', $request['event_id']);
                }
            });
        })->orderBy('id', 'desc')->get();
        if ($getData) {
            $ii = 0;
            $userListData = [];
            foreach ($getData as $key => $value) {
                if ($value['user_information'] && json_decode($value['user_information'] ?? "[]", true)) {
                    foreach (json_decode($value['user_information'] ?? "[]", true) as $k => $mem) {
                        $mem = array_combine(array_map('strval', array_keys($mem)), $mem);
                        if ($mem['verify'] == 1) {
                            $userListData[$ii] = $mem;
                            $getImages = \App\Models\UserAadhaarKyc::where('aadhaar_number', $mem['aadhar'])->first();
                            $userListData[$ii]['image'] = $getImages['image'] ?? '';
                            $userListData[$ii]['order_id'] = $value['orderdata']['order_no'] ?? '';
                            $userListData[$ii]['event_name'] = $value['orderdata']['eventid']['event_name'] ?? '';
                            $userListData[$ii]['id'] = $value['orderdata']['id'] ?? '';
                            $ii++;
                        }
                    }
                }
            }
            return response()->json(['success' => 1, 'message' => 'get Recodes', 'data' => $userListData], 200);
        } else {
            return response()->json(['success' => 0, 'message' => 'Not Found Data', 'data' => []], 200);
        }
    }

    public function EventList_BookingPage(Request $request)
    {
        if (empty($request->seller['relation_id'] ?? '')) {
            return response()->json(['status' => 0, 'message' => 'Not Found Organizer Id', 'recode' => 0, 'data' => []], 200);
        }
        $informational_status = $request->input('informational_status', null);
        $is_approve = $request->input('is_approve', null);
        $status = $request->input('status', null);
        $today = now()->format('Y-m-d');
        $getData = Events::with('categorys', 'eventArtist', 'EventOrder')
            ->whereRaw("STR_TO_DATE( IF(INSTR(start_to_end_date, ' - ') > 0, SUBSTRING_INDEX(start_to_end_date, ' - ', -1), start_to_end_date), '%Y-%m-%d') > ? ", [$today])
            ->where('is_approve', 1)
            ->where('informational_status', 0)
            ->where('status', 1)
            ->where('event_organizer_id', $request->seller['relation_id'])->where('organizer_by', 'outside')->orderBy('id', 'desc')->get()->makeHidden(['translations']);
        if ($getData && count($getData) > 0) {
            $filtered = $getData->map(function ($event) {
                return [
                    'id' => $event->id,
                    'event_name' => $event->event_name,
                    'unique_id' => $event->unique_id,
                    'age_group' => $event->age_group,
                    'start_to_end_date' => $event->start_to_end_date,
                    'venue_list' => collect(json_decode($event->all_venue_data))->map(function ($venue) {
                        $packages = collect($venue->package_list ?? [])->map(function ($pkg) {
                            $package = \App\Models\EventPackage::select('id', 'package_name')
                                ->where('id', $pkg->package_name)
                                ->where('status', 1)
                                ->first();
                            return [
                                'id'           => (int)$package->id ?? null,
                                'package_name' => (string)$package->package_name ?? null,
                                'seats_no'     => (string)$pkg->seats_no ?? 0,
                                'price_no'     => (string)$pkg->price_no ?? 0,
                                'available'    => (string)$pkg->available ?? 0,
                                'sold'         => (int)$pkg->sold ?? 0,
                            ];
                        });
                        return [
                            'id' => (int)$venue->id,
                            'en_event_venue' => $venue->en_event_venue ?? "",
                            'en_event_venue_full_address' => $venue->en_event_venue_full_address ?? "",
                            'date' => $venue->date ?? "",
                            "package_list" => ($packages ?? []),
                        ];
                    })->values(),
                    'artist_name' => $event->eventArtist->name ?? "",
                    'category' => $event->categorys->category_name ?? "",
                ];
            });
            return response()->json(['status' => 1, 'message' => 'Data get Successfully', 'recode' => count($getData), 'data' => $filtered], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Not Found Data', 'recode' => 0, 'data' => []], 200);
        }
    }
    public function CreateOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_phone' => 'required',
            'user_name' => 'required',
            'event_id' => 'required',
            'venue_id' => 'required',
            'package_id' => 'required',
            'no_of_seats' => 'required',
            'amount' => 'required',
            'wallet_type' => 'required|in:0,1',
            'online_amount' => 'required',
            "member" => "required",
        ], [
            'user_id.required' => 'Login User Id',
            'event_id.required' => 'Booking Event Id!',
            'venue_id.required' => 'Venue is Empty!',
            'package_id.required' => 'package is Empty!',
            'no_of_seats.required' => 'seat Number is Empty!',
            'amount.required' => 'Total Amount is provide!',
            'wallet_type.required' => 'wallet type is provide 0,1 !',
        ]);

        if ($validator->fails()) {
            return response()->json(["status" => 0, "message" => "", 'errors' => Helpers::error_processor($validator), 'data' => []], 403);
        }

        $userfind = User::where('phone', ($request['user_phone'] ?? ""))->first();
        if ($userfind) {
            $user_id = $userfind['id'];
        } else {
            $user = new User();
            $user->phone = $request['user_phone'] ?? "";
            $user->name = $request['user_name'];
            $user->f_name = (explode(" ", $request['user_name'])[0] ?? "");
            $user->l_name = (explode(" ", $request['user_name'])[1] ?? "");
            $user->email = $request['user_phone'] ?? "";
            $user->password =  bcrypt('12345678');
            $user->save();
            $user_id = $user->id;
            $data = [
                'customer_id' => ($user->id ?? "")
            ];
            \App\Utils\Helpers::whatsappMessage('whatsapp', 'Welcome Message', $data);
        }

        $lead_id = new \App\Models\EventLeads();
        $lead_id->user_id = $user_id;
        $lead_id->user_phone = $request['user_phone'] ?? "";
        $lead_id->user_name = $request['user_name'];
        $lead_id->event_id = $request['event_id'];
        $lead_id->package_id = $request['package_id'];
        $lead_id->venue_id = $request['venue_id'];
        $lead_id->qty = $request->get('no_of_seats');
        $lead_id->coupon_amount = ($request->get('coupon_amount') ?? 0);
        $lead_id->coupon_id = ($request->get('coupon_id') ?? '');
        $lead_id->amount = (($request->get('coupon_amount') ?? 0) + ($request->get('amount') / ($request->get('no_of_seats') ?? 0)));
        $lead_id->total_amount = ($request->get('amount'));
        $JsonEncodeMembers = [];
        if ($request['no_of_seats'] > 0) {
            for ($qn = 0; $qn < $request['no_of_seats']; $qn++) {
                $JsonEncodeMembers[$qn]['id'] = ($qn + 1);
                $JsonEncodeMembers[$qn]['name'] = $request['member'][$qn]['name'] ?? '';
                $JsonEncodeMembers[$qn]['phone'] = $request['member'][$qn]['phone'] ?? '';
                $JsonEncodeMembers[$qn]['aadhar'] = $request['member'][$qn]['aadhar'] ?? '';
                $JsonEncodeMembers[$qn]['verify'] =  $request['member'][$qn]['verify'] ?? 0;
                $JsonEncodeMembers[$qn]['aadhar_verify'] =  $request['member'][$qn]['aadhar_verify'] ?? 0;
                $JsonEncodeMembers[$qn]['time'] = '';
            }
        }
        $lead_id->user_information = json_encode($JsonEncodeMembers);
        $lead_id->save();
        $eventData = Events::where('is_approve', 1)->where('status', 1)->find($request['event_id']);
        if (!empty($eventData) && !empty($request->get('package_id')) && !empty($request->get('no_of_seats'))) {
            DB::beginTransaction();
            try {
                $foundPackage = false;
                if (!empty($eventData['all_venue_data']) && json_decode($eventData['all_venue_data'], true)) {
                    foreach (json_decode($eventData['all_venue_data'], true) as $key => $value) {
                        if (($value['id'] ?? "") == $request->get('venue_id') && !empty($value['package_list'])) {
                            $booking_date_w_message = $value['date'];
                            $booking_time_w_message = $value['start_time'];
                            $venue_name_w_message = $value['en_event_cities'];
                            $package = collect($value['package_list'])->firstWhere('package_name', $request->get('package_id'));
                            $foundPackage = true;
                            $amounts = -1;
                            if (!empty($package) && ($package['available'] ?? 0) >= $request->get('no_of_seats')) {
                                $amounts = ((($package['price_no'] * $request->get('no_of_seats')) <= ($request->get('amount') ?? 0) + ($request['coupon_amount'] ?? 0)) ? ($package['price_no'] * $request->get('no_of_seats')) : 0);
                            } else {
                                DB::rollBack();
                                return response()->json(['status' => 0, 'message' => $request->get('no_of_seats') . ' seats are not available. ' . $package['available']  . ' seats are available.', 'recode' => '', 'data' => []], 400);
                            }
                            if ($amounts < 0) {
                                DB::rollBack();
                                return response()->json(['status' => 0, 'message' => 'Please valid Amount', 'recode' => '', 'data' => []], 200);
                            }
                        }
                    }
                }
                if (!$foundPackage) {
                    $PackagesSeats = json_decode($eventData['all_venue_data'], true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        DB::rollBack();
                        return response()->json(['status' => 0, 'message' => 'Booking seats data is not properly formatted.', 'recode' => '', 'data' => []], 400);
                    }
                    $foundPackage = false;
                    if (!$foundPackage) {
                        DB::rollBack();
                        return response()->json(['status' => 0, 'message' => ' Package ID not found in booking seats.', 'recode' => '', 'data' => []], 400);
                    }
                }
                $orderData = new EventOrder();
                $orderData->user_id = $user_id;
                $orderData->event_id = $request['event_id'];
                $orderData->venue_id = $request['venue_id'];
                $orderData->amount = $request['amount'];
                $orderData->coupon_amount = ($request['coupon_amount'] ?? 0);
                $orderData->coupon_id = ($request['coupon_id'] ?? 0);
                $orderData->transaction_status = (($request->wallet_type == 1) ? 1 : 0);
                $orderData->transaction_id = (($request->wallet_type == 1) ? 'Cash' : '');
                $orderData->status = (($request->wallet_type == 1) ? 1 : 0);
                $orderData->platform = "app";
                $orderData->save();
                $insertedId = $orderData->id;

                $orderItems = new EventOrderItems();
                $orderItems->order_id = $insertedId;
                $orderItems->package_id = $request->get('package_id');
                $orderItems->no_of_seats = $request->get('no_of_seats');
                $orderItems->amount = $request['amount'];
                $JsonEncodeMembers = [];
                $membersList = \App\Models\EventLeads::where('id', $lead_id->id)->first();
                if ($membersList['qty'] > 0) {
                    $memberJsonDecode = json_decode($membersList['user_information'] ?? "[]", true);
                    if ($memberJsonDecode && count($memberJsonDecode) > 0)
                        $qn = 0;
                    foreach ($memberJsonDecode as $values) {
                        $JsonEncodeMembers[$qn]['id'] = ($qn + 1);
                        $JsonEncodeMembers[$qn]['name'] = $values['name'] ?? '';
                        $JsonEncodeMembers[$qn]['phone'] = $values['phone'] ?? '';
                        $JsonEncodeMembers[$qn]['aadhar'] = $values['aadhar'] ?? '';
                        $JsonEncodeMembers[$qn]['verify'] =  $values['verify'] ?? 0;
                        $JsonEncodeMembers[$qn]['aadhar_verify'] =  $values['aadhar_verify'] ?? 0;
                        $JsonEncodeMembers[$qn]['time'] = '';
                        $qn++;
                    }
                }
                $orderItems->user_information = json_encode($JsonEncodeMembers);
                $orderItems->save();
                if ($request->wallet_type == 1) {
                    $bookingSeats = json_decode($eventData['all_venue_data'], true);
                    $foundPackage = [];
                    if ($bookingSeats) {
                        $pn = 0;
                        foreach ($bookingSeats as $keys => $bo_se) {
                            $foundPackage[$keys] = $bo_se;
                            if (($bo_se['id'] ?? "") == $request['venue_id'] && !empty($bo_se['package_list'])) {
                                foreach ($bo_se['package_list'] as $kp => $ch_seat) {
                                    if ($ch_seat['package_name'] == $request->get('package_id')) {
                                        if ($ch_seat['available'] < $request->get('no_of_seats')) {
                                            return response()->json(['status' => 0, 'message' => $request->get('no_of_seats') . ' seats are not available. ' . $ch_seat['available']  . ' seats are available.', 'recode' => '', 'data' => []], 200);
                                        } else {
                                            $booking_date_w_message = $bo_se['date'];
                                            $booking_time_w_message = $bo_se['start_time'];
                                            $venue_name_w_message = $bo_se['en_event_cities'];

                                            $foundPackage[$keys]['package_list'][$kp]['available'] = ($ch_seat['available'] - $request->get('no_of_seats'));
                                            $foundPackage[$keys]['package_list'][$kp]['sold'] = ($ch_seat['sold'] + $request->get('no_of_seats'));
                                            $eventtax = \App\Models\ServiceTax::find(1);
                                            $amdin_commission = 0;
                                            $final_amount = 0;
                                            $govtTax = 0;
                                            $orderamount = (($request['amount'] ?? 0) + ($request['coupon_amount'] ?? 0));
                                            if (!empty($eventData) && $eventData['commission_seats']) {
                                                $govtTax = (($orderamount * ($eventtax['event_tax'] ?? 0)) / 100);
                                                $orderamount = $orderamount - $govtTax;
                                                $amdin_commission =  (($orderamount * $eventData['commission_seats']) / 100);
                                                $final_amount = $orderamount - $amdin_commission;
                                            }
                                            $array['admin_commission'] = $amdin_commission;
                                            $array['gst_amount'] = $govtTax;
                                            $array['final_amount'] = $final_amount;
                                            EventOrder::where('id', $insertedId)->update($array);
                                            EventOrderItems::where('id', $orderItems->id)->update(['sub_amount' => ($getLead['amount'] ?? 0), 'gst' => ($eventtax['event_tax'] ?? 0), 'gst_amount' => $govtTax]);
                                            $listOrganizer =  EventOrganizer::where('id', $eventData['event_organizer_id'])->first();
                                            EventOrganizer::where('id', $eventData['event_organizer_id'])->update(
                                                [
                                                    'org_total_tax' => ($listOrganizer['org_total_tax'] + $govtTax),
                                                    "org_withdrawable_ready" => ($listOrganizer["org_withdrawable_ready"] + $final_amount),
                                                    "org_total_commission" => ($listOrganizer["org_total_commission"] + $amdin_commission),
                                                ]
                                            );
                                        }
                                    }
                                }
                                \App\Models\EventLeads::where('id', $request->lead)->update(['status' => 1]);
                            }
                        }
                        Events::where('id',  $eventData['id'])->update(['all_venue_data' => $foundPackage]);
                    }
                    \App\Models\EventLeads::where('id', $lead_id->id)->update(['status' => 1]);
                    $eventOrder = \App\Models\EventOrder::where('id', $orderData->id)->with(['orderitem', 'eventid'])->first();
                    $message_data['title_name'] = $eventOrder['eventid']['event_name'];
                    $message_data['place_name'] = $venue_name_w_message;
                    $message_data['booking_date'] = date('Y-m-d', strtotime($booking_date_w_message));
                    $message_data['time'] = ($booking_time_w_message);
                    $message_data['orderId'] = $eventOrder['order_no'];
                    $message_data['final_amount'] = webCurrencyConverter(amount: (float)$eventOrder['amount'] ?? 0);
                    $message_data['customer_id'] =  $eventOrder['user_id'];
                    $message_data['number'] =  $eventOrder['orderitem'][0]['no_of_seats'] ?? 0;
                    $message_data['link'] =  route('event-create-pdf-invoice', [$eventOrder['id']]);
                    Helpers::whatsappMessage('event', 'Event booking Confirmed', $message_data);
                    $memberList = json_decode($eventOrder['orderitem'][0]['user_information'] ?? '[]', true) ?? [];
                    if ($memberList  && count($memberList) > 0) {
                        foreach ($memberList as $key => $vals) {
                            $messageData = [
                                'customer_id' => $eventOrder['user_id'],
                                'member_names' => $vals['name'],
                                'link' => route('event-order-details2', [$eventOrder['id'], ($vals['id'])]),
                            ];
                            \App\Jobs\SendWhatsappMessage::dispatch('event', 'event pass attachment', $messageData);
                        }
                    }
                    DB::commit();
                    return response()->json(['status' => 1, 'message' => 'Order placed successfully', 'recode' => 1, 'data' => []], 200);
                } else {
                    $wallet_amount = 0;
                    $total_amount = $request['amount'];
                    $onlinepay = $request['amount'];
                    $data = [
                        'additional_data' => [
                            'business_name' => \App\Models\BusinessSetting::where(['type' => 'company_name'])->first()->value,
                            'business_logo' => asset('storage/app/public/company') . '/' . Helpers::get_business_settings('company_web_logo'),
                            'payment_mode' => 'web',
                            'customer_id' => $user_id,
                            "order_id" => $insertedId,
                            "memberList" => ($request['member']),
                            "darshanInfo" => '',
                            "amount" => ($onlinepay ?? 0),
                            "user_name" => ($request['user_name'] ?? ''),
                            "user_email" => '',
                            "user_phone" => $request['person_phone'],
                            'total_amount' => $total_amount,
                            'wallet_amount' => $wallet_amount,
                            "online_pay" => $onlinepay,
                            'page_name' => 'event_order',
                            'success_url' => route('event_pay_success', [$eventData['slug'], 'lead' => $lead_id->id]),
                        ],
                        'user_id' => $user_id,
                        'payment_amount' => $onlinepay,
                        "order_id" => $insertedId,
                        "attribute" => "event_order",
                        "external_redirect_link" => route('event_pay_success', [$eventData['slug'], 'lead' => $lead_id->id]),
                    ];
                    $url_open = \App\Http\Controllers\Customer\PaymentController::TrustVIPTicketBooking($data);

                    $dataemail['admin_phone'] = $request['user_phone'];
                    $dataemail['admin_name'] = ($request['user_name'] ?? '');
                    $dataemail['payment_link'] = ($url_open ?? '');
                    $dataemail['title'] = $eventData['event_name'];
                    $dataemail['final_amount'] = $onlinepay;
                    Helpers::whatsappMessage('event', 'event_order_paymant_link_message', $dataemail);

                    $qrCode = new \Endroid\QrCode\QrCode($url_open);
                    $writer = new \Endroid\QrCode\Writer\PngWriter();
                    $result = $writer->write($qrCode);
                    $folder = storage_path('app/public/qrcodes');
                    if (!\Illuminate\Support\Facades\File::exists($folder)) {
                        \Illuminate\Support\Facades\File::makeDirectory($folder, 0777, true);
                    }
                    $filePath = $folder . "/eventorderbookingamount.png";
                    $result->saveToFile($filePath);
                    $imageData = getValidImage(path: 'storage/app/public/qrcodes/eventorderbookingamount.png', type: 'backend-product');
                    $query12 = parse_url($url_open, PHP_URL_QUERY);
                    parse_str($query12, $params12);
                    $paymentId = $params12['payment_id'] ?? null;
                    DB::commit();
                    return response()->json(['status' => 2, 'message' => 'get url Successfully', 'data' => $imageData, 'url' => $url_open, 'paymentID' => $paymentId], 200);
                }
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['status' => 0, 'message' => 'An error occurred: ' . $e->getMessage(), 'recode' => '', 'data' => []], 200);
            }
        } else {
            DB::rollBack();
            return response()->json(['status' => 0, 'message' => 'Invalid Event or Seats Data', 'recode' => 0, 'data' => []], 400);
        }
        return response()->json(['status' => 0, 'message' => 'Please Currct Data Pass', 'recode' => 0, 'data' => []], 400);
    }
}
